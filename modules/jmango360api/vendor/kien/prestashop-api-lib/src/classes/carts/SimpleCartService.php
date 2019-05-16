<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

abstract class SimpleCartService extends BaseService
{
    const ADD_ITEM = 1;
    const UPDATE_ITEM = 2;

    protected $id_product = 0;
    protected $qty = 0;
    protected $id_address_delivery = 0;
    protected $group = null;
    protected $errors = null;
    protected $customization_id = 0;
    protected $id_product_attribute = 0;
    protected $ps_item_id;

    protected $id_cart;
    protected $id_customer;
    protected $mode = self::ADD_ITEM;
    protected $paymentFinder;

    protected function initFromRequestBody()
    {
        $requestData = json_decode($this->getRequestBody());
        $this->id_customer = $requestData->id_customer;
        $this->id_cart = $requestData->id_cart;
        $this->id_address_delivery = $requestData->id_address_delivery;

        $id_product = $requestData->id_product;
        $group = $requestData->group;
        $qty = $requestData->qty;
        $customization_id = $requestData->customization_id;
        $this->ps_item_id = $requestData->ps_item_id;
        $this->id_product = $id_product ? $id_product : 0;

        if ($group) {
            $convertedGroup = array();
            foreach ($group as $key => $value) {
                $intValueKey = (int)$key;
                $convertedGroup[$intValueKey] = $value;
            }
            $this->group = $convertedGroup;
        } else {
            $this->group = null;
        }

        $this->qty = $qty && abs($qty) > 0 ? abs($qty) : 1;
        $this->customization_id = $customization_id ? $customization_id : 0;
    }

    /**
     * Get shopping cart
     * @param int $id_cart
     * @return object
     * @throws Exception
     */
    protected function makeCartResponse($id_cart)
    {
        $cart = new Cart($id_cart, $this->context->language->id);
        if ($cart && $cart->id) {
            $warningMessage = $this->validateCartRules($cart);
            $cartResult = CartUtils::presentShoppingCart(
                $cart,
                $this->context->language->id,
                $this->context->shop->id,
                $this->context,
                $this->module_name
            );

            if ($cartResult['items'] && count($cartResult['items']) > 0) {
                //only check when cart is not empty
                $minimumMessage = $this->validateMinimumAmount($cartResult);
                if ($minimumMessage) {
                    array_push($warningMessage, new JmError(200, $minimumMessage));
                }
                //check product's availability
                $availableMessage = $this->validateProductsAvailable($cart);
                if ($availableMessage) {
                    array_push($warningMessage, new JmError(200, $availableMessage));
                }
            }

            $cartResult = $this->formatCoupon($cartResult);
            unset($cartResult['vouchers']);
            // only prestashop 16 and below need to sort product in cart.
            if (!$this->isV17()) {
                $this->sortCartItem($cartResult, $id_cart);
            }
            $this->setCartDefaultAddress($cartResult, $cart);
            if ((int)$cart->id_customer) {
                $this->setCartCustomerInfo($cartResult, $cart);
            }
            if ($cartResult['id_address_delivery'] && $cartResult['id_address_invoice']) {
                $cartResult['shipping_methods'] = $this->getCarriers($cart);
                $cartResult['payment_methods'] = $this->getPayments($cart);
            }
            $this->response = new GetCartResponse();
            $this->response->cart = $cartResult;
            $this->response->errors = $this->errors;
            $this->response->warnings = $warningMessage;
        } else {
            $this->response = new GetCartResponse();
            $this->response->cart = null;
            $this->response->errors = $this->errors;
        }
        //check out_of_stock ? true:false. and return message.
        foreach ($cartResult['items'] as $value) {
            $is_back_order = ProductCore::isAvailableWhenOutOfStock($value['out_of_stock']);
            if ($value['quantity_available'] < 1 && !$is_back_order) {
                $this->response->messages = array(new JmError(500, $this->getTranslation('There are not enough products in stock', 'shopping-cart-service'), $value['name']));
                $this->response->warnings = array(new JmError(500, $this->getTranslation('There are not enough products in stock', 'shopping-cart-service'), $value['name']));
            }
        }
        return $this->response;
    }

    protected function processChangeProductInCart($update_item = true)
    {
        if ($this->group && 0 == $this->id_product_attribute) {
            $this->id_product_attribute = (int)$this->getIdProductAttributesByIdAttributes($this->id_product, $this->group);
        }

        //check product id > 0
        if ($this->id_product == 0) {
            $this->errors[] = new JmError(500, $this->getTranslation('Product not found', 'shopping-cart-service'));
            return;
        }

        $product = new Product($this->id_product, true, $this->context->language->id);

        //check product is active or able to access
        if (!$product->id
            || !$product->active
            || !$product->checkAccess($this->context->cart->id_customer)) {
            $this->errors[] = new JmError(
                500,
                $this->getTranslation('This product is no longer available.', 'shopping-cart-service')
            );
            return;
        }

        if (!$this->id_product_attribute && $product->hasAttributes()) {
            $minimum_quantity = ($product->out_of_stock == 2) ?
                !Configuration::get('PS_ORDER_OUT_OF_STOCK') : !$product->out_of_stock;
            $this->id_product_attribute = Product::getDefaultAttribute($product->id, $minimum_quantity);
            // @todo do something better than a redirect admin !!
            if (!$this->id_product_attribute) {
                Tools::redirectAdmin($this->context->link->getProductLink($product));
            }
        }

        $qty_to_check = $this->qty;

        $updateQty = $this->qty;
        $operator = 'up';

        $cart_products = $this->context->cart->getProducts();

        if (is_array($cart_products)) {
            foreach ($cart_products as $cart_product) {
                if ($this->productInCartMatchesCriteria($cart_product)) {
                    $qty_to_check = $cart_product['cart_quantity'];

                    switch ($this->mode) {
                        case self::ADD_ITEM:
                            //set update input qty = input qty and operator = up
                            $updateQty = $this->qty;
                            $operator = 'up';

                            //if add new item -> add input qty to cart qty
                            $qty_to_check += $this->qty;
                            break;
                        case self::UPDATE_ITEM:
                            //calculate update qty
                            $updateQty = abs($this->qty - $qty_to_check);


                            if (0 == $updateQty) {
                                //nothing to update
                                return;
                            }

                            //compare cart qty and input qty to get operator
                            $operator = $qty_to_check <= $this->qty ? 'up' : 'down';

                            //if update item -> set cart qty = input qty
                            $qty_to_check = $this->qty;
                            break;
                    }

                    break;
                }
            }
        }

        // Check product quantity availability
        if ($this->id_product_attribute) {
            if (!Product::isAvailableWhenOutOfStock($product->out_of_stock)
                && !Attribute::checkAttributeQty($this->id_product_attribute, $qty_to_check)) {
                if ($this->isV17()) {
                    $this->errors[] = new JmError(500, $this->trans(
                        'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                        array('%product%' => $product->name),
                        'Shop.Notifications.Error'
                    ));
                } else {
                    $this->errors[] = new JmError(
                        500,
                        $this->getTranslation('There are not enough products in stock', 'shopping-cart-service')
                    );
                }
            }
        } elseif (!$product->checkQty($qty_to_check)) {
            if ($this->isV17()) {
                $this->errors[] = new JmError(500, $this->trans(
                    'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                    array('%product%' => $product->name),
                    'Shop.Notifications.Error'
                ));
            } else {
                $this->errors[] = new JmError(500, $this->getTranslation(
                    'There are not enough products in stock',
                    'shopping-cart-service'
                ));
            }
        }

        // If no errors, process product addition
        if (!$this->errors) {
            // Add cart if no cart found
            if (!$this->context->cart->id) {
                if (Context::getContext()->cookie->id_guest) {
                    $guest = new Guest(Context::getContext()->cookie->id_guest);
                    $this->context->cart->mobile_theme = $guest->mobile_theme;
                }
                $this->context->cart->add();
                if ($this->context->cart->id) {
                    $this->context->cookie->id_cart = (int)$this->context->cart->id;
                }
            }

            // Check customizable fields
            if (!$product->hasAllRequiredCustomizableFields() && !$this->customization_id) {
                $this->errors[] = new JmError(
                    500,
                    $this->getTranslation('Please fill in all of the required fields,' .
                        ' and then save your customizations.', 'shopping-cart-service')
                );
            }

            if (!$this->errors) {
                if (version_compare(_PS_VERSION_, '1.7') < 0) {
                    $update_quantity = $this->context->cart->updateQty(
                        $updateQty,
                        $this->id_product,
                        $this->id_product_attribute,
                        $this->customization_id,
                        $operator,
                        $this->id_address_delivery
                    );
                } else {
                    $update_quantity = $this->context->cart->updateQty(
                        $updateQty,
                        $this->id_product,
                        $this->id_product_attribute,
                        $this->customization_id,
                        $operator,
                        $this->id_address_delivery,
                        null,
                        true,
                        true
                    );
                }

                if ($update_quantity < 0) {
                    // If product has attribute, minimal quantity is set with minimal quantity of attribute
                    $minimal_quantity = ($this->id_product_attribute) ? Attribute::getAttributeMinimalQty($this->id_product_attribute) : $product->minimal_quantity;
                    $this->errors[] = new JmError(500, sprintf($this->getTranslation('You must add %d minimum quantity', 'shopping-cart-service'), $minimal_quantity));
                } elseif (!$update_quantity) {
                    $this->errors[] = new JmError(
                        500,
                        $this->getTranslation('You already have the maximum quantity' .
                            'available for this product.', 'shopping-cart-service')
                    );
                }
            }
        }

        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }

    protected function sortCartItem(&$cartResult, $id_cart)
    {
        $items = array();
        $gift_item = array();
        $result = Db::getInstance()->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'cart_product` cp
            WHERE id_cart = \'' . (int)$id_cart . '\'
            ');
        foreach ($result as $row) {
            $index = $this->searchForId($row['id_product'], $cartResult['items'], $row['id_product_attribute']);
            if ($index !== null && !$cartResult['items'][$index]['is_gift']) {
                $items[] = $cartResult['items'][$index];
            }
        }
        foreach ($cartResult['items'] as $item) {
            if ($item['is_gift']) {
                $gift_item[] = $item;
            }
        }
        $cartResult['items'] = array_merge($items, $gift_item);
    }

    public function searchForId($id, $array, $id_product_attribute)
    {
        foreach ($array as $key => $val) {
            if ($val['id_product'] === $id && $id_product_attribute === $val['id_product_attribute']) {
                return $key;
            }
        }
        return null;
    }

    // Avaliable on ProductCore of prestashop 1.7, however prestashop 1.6 doesn't have this function
    // => copy into plugin's class so that both 1.6 and 1.7 can use
    public static function getIdProductAttributesByIdAttributes($id_product, $id_attributes, $find_best = false)
    {
        if (!is_array($id_attributes)) {
            return 0;
        }

        $id_attributes = array_values($id_attributes);

        $id_product_attribute = Db::getInstance()->getValue('
        SELECT pac.`id_product_attribute`
        FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
        INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON pa.id_product_attribute = pac.id_product_attribute
        WHERE id_product = ' . (int)$id_product . '
        AND id_attribute
        IN (' . implode(',', array_map('intval', $id_attributes)) . ')
        GROUP BY id_product_attribute
        HAVING COUNT(id_product) = ' . count($id_attributes));

        if ($id_product_attribute === false && $find_best) {
            //find the best possible combination
            //first we order $id_attributes by the group position
            $orderred = array();
            $result = Db::getInstance()->executeS('SELECT `id_attribute` FROM `' . _DB_PREFIX_ . 'attribute` a
            INNER JOIN `' . _DB_PREFIX_ . 'attribute_group` g ON a.`id_attribute_group` = g.`id_attribute_group`
            WHERE `id_attribute`
            IN (' . implode(',', array_map('intval', $id_attributes)) . ')
            ORDER BY g.`position` ASC');

            foreach ($result as $row) {
                $orderred[] = $row['id_attribute'];
            }

            while ($id_product_attribute === false && count($orderred) > 0) {
                array_pop($orderred);
                $id_product_attribute = Db::getInstance()->getValue('
                SELECT pac.`id_product_attribute`
                FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                INNER JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                ON pa.id_product_attribute = pac.id_product_attribute
                WHERE id_product = ' . (int)$id_product . '
                AND id_attribute
                IN (' . implode(',', array_map('intval', $orderred)) . ')
                GROUP BY id_product_attribute
                HAVING COUNT(id_product) = ' . count($orderred));
            }
        }
        return $id_product_attribute;
    }

    protected function productInCartMatchesCriteria($productInCart)
    {
        return (
                !isset($this->id_product_attribute) ||
                (
                    $productInCart['id_product_attribute'] == $this->id_product_attribute &&
                    $productInCart['id_customization'] == $this->customization_id
                )
            ) && isset($this->id_product) && $productInCart['id_product'] == $this->id_product;
    }


    protected function throwCartFinishedException()
    {
        $this->throwServiceException(400, 'Bad request', 'This cart is in a order!');
    }

    protected function createEmptyCart()
    {
        //create empty cart
        $newCart = new Cart(null, $this->context->language->id);
        $newCart->id_currency = (int)$this->context->currency->id;
        $newCart->id_shop = $this->context->shop->id;
        $newCart->id_shop_group = (int)Shop::getGroupFromShop($this->context->shop->id);


        if ($this->context->customer->id) {
            // Cart created when user logged in
            $newCart->id_customer = (int)$this->context->customer->id;
            $newCart->id_address_delivery = (int)Address::getFirstCustomerAddressId($this->context->customer->id);
            $newCart->id_address_invoice = (int)$newCart->id_address_delivery;
        } else {
            // Cart created for guest.
            // Creating new guest and add to database
            $guest = new Guest();
            $guest->add();
            $newCart->id_guest = $guest->id;
            $newCart->id_address_delivery = 0;
            $newCart->id_address_invoice = 0;
        }
        // Add new cart to database
        $success = $newCart->add();
        return $success ? $newCart : null;
    }

    protected function validateCartRules($cart)
    {
        $cartRules = $cart->getCartRules();
        $warningMessage = array();

        if ($cartRules && count($cartRules) > 0) {
            foreach ($cartRules as $cart_rule) {
                if (($rule = new CartRule((int)$cart_rule['obj']->id)) && Validate::isLoadedObject($rule)) {
                    if ($error = $rule->checkValidity($this->context, true, true)) {
//                        array_push($warningMessage, $error);
                        $this->context->cart->removeCartRule($cart_rule['id_cart_rule']);
                        CartRule::autoAddToCart($this->context);
                    }
                }
            }
        }

        return $warningMessage;
    }

    protected function validateMinimumAmount($cart)
    {
        return $cart ? $cart['minimalPurchaseRequired'] : null;
    }

    //check availability of product in cart and return error if product no longer available.
    protected function validateProductsAvailable($cart)
    {
        $product = $cart->checkQuantities(true);
        if (true === $product || !is_array($product)) {
            return null;
        }
        if ($this->isV17()) {
            if ($product['active']) {
                return $this->trans(
                    'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                    array('%product%' => $product->name),
                    'Shop.Notifications.Error'
                );
            }

            return $this->trans(
                'This product (%product%) is no longer available.',
                array('%product%' => $product['name']),
                'Shop.Notifications.Error'
            );
        } else {
            return $this->trans(
                'The item %product% in your cart is no longer available in this quantity. You cannot proceed with your order until the quantity is adjusted.',
                array('%product%' => $product->name),
                'Shop.Notifications.Error'
            );
        }
    }

    protected function formatCoupon(&$cart)
    {
        if ($cart) {
            $couponData = array();

            if ($this->isV17()) {
                $couponData = $this->findCouponV17($cart);
            } else {
                $couponData = $this->findCoupon16($cart);
            }

            $cart['coupons'] = $couponData;
        }

        return $cart;
    }


    protected function findCouponV17($cart)
    {
        $couponData = array();
        if ($cart) {
            $vouchers = $cart['vouchers'];
            if ($vouchers) {
                $addedVouchers = $vouchers['added'];
                if ($addedVouchers && count($addedVouchers) > 0) {
                    foreach ($addedVouchers as $key => $voucher) {
                        $id_cart_rule = $voucher['id_cart_rule'];
                        $cartRule = new CartRule($id_cart_rule);
                        if ($cartRule && $cartRule->id) {
                            $coupon = new Coupon();
                            $coupon->id = $cartRule->id;
                            $coupon->code = $cartRule->code;
                            array_push($couponData, $coupon);
                        }
                    }
                }
            }

            $cart['coupons'] = $couponData;
        }
        return $couponData;
    }

    protected function findCoupon16($cart)
    {
        $couponData = array();
        if ($cart) {
            $id_cart = $cart['entity_id'];
            $objCart = new CartCore($id_cart);
            if ($objCart) {
                $cartRules = $objCart->getCartRules();

                if ($cartRules && count($cartRules) > 0) {
                    foreach ($cartRules as $rule) {
                        $coupon = new Coupon();
                        $coupon->id = $rule['id_cart_rule'];
                        $coupon->code = $rule['code'];
                        array_push($couponData, $coupon);
                    }
                }
            }
        }

        return $couponData;
    }

    protected function isProductInCart()
    {
        if ($this->context->cart) {
            $cart_products = $this->context->cart->getProducts();

            if (is_array($cart_products)) {
                foreach ($cart_products as $cart_product) {
                    if ($this->productInCartMatchesCriteria($cart_product)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function setCartDefaultAddress(&$cartResult, &$cart)
    {
        if (!(int)$cart->id_customer) {
            return;
        }

        $defaultDeliveryAddressId = (int)Address::getFirstCustomerAddressId($cart->id_customer);
        $defaultInvoiceAddressId = $defaultDeliveryAddressId;
        $cartUpdated = false;

        if (!$cartResult['id_address_delivery']) {
            $address_delivery = new Address($defaultDeliveryAddressId);
            if (!$address_delivery->deleted && $address_delivery->id) {
                $cartResult['address_delivery'] = $address_delivery;
                $cartResult['address_delivery']->country_iso_code = Country::getIsoById($cartResult['address_delivery']->id_country);
                $state_name = State::getNameById($cartResult['address_delivery']->id_state);
                $cartResult['address_delivery']->state = $state_name ? $state_name : '';
                $cart->id_address_delivery = $defaultDeliveryAddressId;
                $cartUpdated = true;
            } else {
                $cartResult['address_delivery'] = null;
                $cart->id_address_delivery = 0;
                $cartUpdated = true;
            }
        } else {
            $address_delivery = new Address($cartResult['id_address_delivery']);
            if (!$address_delivery->deleted && $address_delivery->id) {
                $cartResult['address_delivery'] = $address_delivery;
                $cartResult['address_delivery']->country_iso_code = Country::getIsoById($cartResult['address_delivery']->id_country);
                $state_name = State::getNameById($cartResult['address_delivery']->id_state);
                $cartResult['address_delivery']->state = $state_name ? $state_name : '';
            } else {
                $cartResult['address_delivery'] = null;
                $cart->id_address_delivery = 0;
                $cartUpdated = true;
            }
        }

        if (!$cartResult['id_address_invoice']) {
            $address_invoice = new Address($defaultInvoiceAddressId);
            if (!$address_invoice->deleted && $address_invoice->id) {
                $cartResult['address_invoice'] = $address_invoice;
                $cartResult['address_invoice']->country_iso_code = Country::getIsoById($cartResult['address_invoice']->id_country);
                $state_name = State::getNameById($cartResult['address_invoice']->id_state);
                $cartResult['address_invoice']->state = $state_name ? $state_name : '';
                $cart->id_address_invoice = $defaultInvoiceAddressId;
                $cartUpdated = true;
            } else {
                $cartResult['address_invoice'] = null;
                $cart->id_address_invoice = 0;
                $cartUpdated = true;
            }
        } else {
            $address_invoice = new Address($cartResult['id_address_invoice']);
            if (!$address_invoice->deleted && $address_invoice->id) {
                $cartResult['address_invoice'] = $address_invoice;
                $cartResult['address_invoice']->country_iso_code = Country::getIsoById($cartResult['address_invoice']->id_country);
                $state_name = State::getNameById($cartResult['address_invoice']->id_state);
                $cartResult['address_invoice']->state = $state_name ? $state_name : '';
            } else {
                $cartResult['address_invoice'] = null;
                $cart->id_address_invoice = 0;
                $cartUpdated = true;
            }
        }

        if ($cartUpdated) {
            $cart->update(true);
        }
    }

    public function setCartCustomerInfo(&$cartResult, &$cart)
    {
        $customer = new Customer($cart->id_customer);
        $result = array();
        $result['id'] = $customer->id;
        $result['firstname'] = $customer->firstname;
        $result['lastname'] = $customer->lastname;
        $result['email'] = $customer->email;
        $cartResult['customer_info'] = $result;
    }

    protected function getCarriers($cart_core)
    {
        $cart = $cart_core;
        $context = $this->context;

        $carriers = array();

        $results = array();

        $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
        $exludedCarriers = isset($exluded['carriers']) ? $exluded['carriers'] : array();

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $carrierFinder = new DeliveryOptionsFinder(
                $context,
                $context->getTranslator(),
                new PrestaShop\PrestaShop\Adapter\ObjectPresenter(),
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter()
            );
            $results = $carrierFinder->getDeliveryOptions();
        } else {
            $free_shipping = false;
            foreach ($cart->getCartRules() as $rule) {
                if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                    $free_shipping = true;
                    break;
                }
            }

            foreach ($cart->getDeliveryOptionList() as $id_address => $option_list) {
                foreach ($option_list as $key => $option) {
                    foreach ($option['carrier_list'] as $item) {
                        $carrier = array(
                            'id' => @$item['instance']->id,
                            'name' => @$item['instance']->name,
                            'logo' => @$item['logo'],
                            'delay' => @$item['instance']->delay[$cart->id_lang],
                            'price' => '',
                            'price_with_tax' => @$item['price_with_tax'],
                            'price_without_tax' => @$item['price_without_tax']
                        );
                        if ($option['total_price_with_tax'] && !$option['is_free'] && !$free_shipping) {
                            if (Configuration::get('PS_TAX') == 1) {
                                if (Product::getTaxCalculationMethod((int)$context->cookie->id_customer) == 1) {
                                    $carrier['price'] = sprintf(
                                        '%s %s',
                                        $this->_convertPrice($option['total_price_without_tax']),
                                        $this->getTranslation('(tax excl.)', 'shipping-methods')
                                    );
                                } else {
                                    $carrier['price'] = sprintf(
                                        '%s %s',
                                        $this->_convertPrice($option['total_price_with_tax']),
                                        $this->getTranslation('(tax incl.)', 'shipping-methods')
                                    );
                                }
                            } else {
                                $carrier['price'] = $option['total_price_without_tax'];
                            }
                        } else {
                            $carrier['price'] = $this->getTranslation('Free', 'shipping-methods');
                        }
                        $results[] = $carrier;
                    }
                }
            }
        }
        $selectedCarrier = $cart->getDeliveryOption();
        foreach ($results as $result) {
            if (in_array(@$result['id'], $exludedCarriers)) {
                continue;
            }
            $carrier = new JCarrier();
            $carrier->id = @$result['id'];
            $carrier->id_address = $cart->id_address_delivery;
            $carrier->name = @$result['name'];
            $carrier->logo = !empty($result['logo']) ? sprintf('%s%s', _PS_BASE_URL_, $result['logo']) : '';
            $carrier->delay = @$result['delay'];
            $carrier->price = @$result['price'];
            $carrier->price_with_tax = @$result['price_with_tax'];
            $carrier->price_without_tax = @$result['price_without_tax'];
            if ($carrier->id === (int)$selectedCarrier[$carrier->id_address]) {
                $carrier->selected = true;
            } else {
                $carrier->selected = false;
            }
            $carriers[] = $carrier;
        }

        return $carriers;
    }

    protected function _convertPrice($price = null)
    {
        $smarty = Context::getContext()->smarty;

        return Product::convertPrice(array('price' => $price), $smarty);
    }

    protected function getPayments($cart)
    {
        $this->paymentFinder = new PaymentFinder();
        $payments = array();
        $results = $this->paymentFinder->getPaymentOptions($cart);
        $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
        $exludedPayments = isset($exluded['payments']) ? $exluded['payments'] : array();

        foreach ($results as $item) {
            if (in_array($item['module_id'], $exludedPayments)) {
                continue;
            }
            $payment = new JPayment();
            $payment->id = $item['id'];
            $payment->title = $item['title'];
            $payment->description = $item['description'];
            $payment->logo = !empty($item['logo']) ? sprintf('%s%s', _PS_BASE_URL_, $item['logo']) : '';
            $payment->url = $item['url'];
            $payment->inputs = $item['inputs'];
            $payment->form = $item['form'];

            $payments[] = $payment;
        }

        return $payments;
    }
}

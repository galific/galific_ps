<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;
if (file_exists(_PS_MODULE_DIR_ . 'netreviews/NetReviewsModel.php')) {
    require_once _PS_MODULE_DIR_ . "netreviews/NetReviewsModel.php";
}
if (file_exists(_PS_MODULE_DIR_ . 'netreviews/models/NetReviewsModel.php')) {
    require_once _PS_MODULE_DIR_ . "netreviews/models/NetReviewsModel.php";
}
class CartUtils extends CommonUtils
{
    /**
     * Present shopping cart data
     *
     * @param $cart
     * @return object
     * @throws Exception
     */
    public static function presentShoppingCart($cart, $id_lang, $id_shop, $context, $module_name)
    {
        if ($cart) {
            self::validate($cart);

            $presentedCart = null;
            $presenter = new CartDataTransform($module_name);
            $presentedCart = $presenter->present($cart);
            $cart_discounts = CartRule::getCustomerCartRules(
                $id_lang,
                $cart->id_customer,
                $active = true,
                $includeGeneric = true,
                $inStock = true,
                $cart,
                $freeShippingOnly = false,
                $highlightOnly = true
            );

            $presentedCart = CartUtils::buildShoppingCartItems($presentedCart, $id_lang, $id_shop, $context, $module_name);
            $presentedCart = CartUtils::assignProductCombinationsV2($presentedCart, $id_lang, $id_shop, $context);
            $cart = CartUtils::formatShoppingCart($cart, $presentedCart, $cart_discounts);

            return $cart;
        }

        return null;
    }

    /**
     * Validate cart before present
     * PS-921: Validate addresses
     *
     * @param Cart $cart
     * @throws
     */
    public static function validate($cart)
    {
        if ($cart) {
            $cartUpdate = false;

            $invoiceAddressId = $cart->id_address_invoice;
            try {
                Address::initialize($invoiceAddressId);
            } catch (Exception $e) {
                $cart->id_address_invoice = 0;
                $cartUpdate = true;
            }

            $deliveryAddressId = $cart->id_address_delivery;
            try {
                Address::initialize($deliveryAddressId);
            } catch (Exception $e) {
                $cart->id_address_delivery = 0;
                $cartUpdate = true;
            }

            if ($cartUpdate) {
                Context::getContext()->cart = $cart;
                $cart->save();
            }
        }
    }

    /**
     * Format shopping cart properties follow Magento
     * @param $cartObject
     * @param $presentedCart
     * @return object
     */
    public static function formatShoppingCart($cartObject, &$presentedCart, $cart_discounts)
    {
        if ($presentedCart) {
            //set cart id
            $presentedCart['entity_id'] = $cartObject->id;
            $presentedCart['id_shop'] = $cartObject->id_shop;

            $presentedCart['totals'] = self::formatCartTotals($presentedCart);

            //convert virtual cart
            $is_virtual = $presentedCart['is_virtual'];
            $presentedCart['is_virtual'] = $is_virtual ? '1' : '0';

            $is_active = '1';
            $orderId = BaseService::getIdByCartId($cartObject->id);
            if ($orderId && $orderId > 0) {
                //this cart is in a order
                $is_active = '0';
            }
            $presentedCart['is_active'] = $is_active;

            $products = $presentedCart['products'];


            $presentedCart['items'] = $products;
            unset($presentedCart['products']);

            $presentedCart['added_discounts'] = CartUtils::buildAddedDiscountList($cartObject);
            $presentedCart['discounts'] = CartUtils::buildCartDiscountResponse($cart_discounts, $presentedCart['added_discounts']);
        }
        return $presentedCart;
    }

    /**
     * Assign shopping cart item id
     * @param $presentedCart
     * @return object
     */
    public static function buildShoppingCartItems(&$presentedCart, $id_lang, $id_shop, $context, $module_name)
    {
        if (isset($presentedCart)) {
            $products = $presentedCart['products'];
            if ($products && count($products) > 0) {
                foreach ($products as &$product) {
                    $product = self::formatProductData($product, $id_lang, $id_shop, $context, $module_name);
                    $product = self::addTextureUrl($product);
                }
            }
            $presentedCart['products'] = $products;
        }
        return $presentedCart;
    }

    public static function buildCartDiscountResponse($cart_discounts, $added_rules)
    {
        $result = array();
        if (isset($cart_discounts)) {
            $added = array();
            foreach ($added_rules as $rule) {
                $added[] = (int)$rule['id'];
            }
            foreach ($cart_discounts as $discount) {
                if ($discount['code'] && !in_array((int)$discount['id_cart_rule'], $added)) {
                    $d = array();
                    $d['id'] = $discount['id_cart_rule'];
                    $d['code'] = $discount['code'];
                    $d['description'] = $discount['description'];
                    $d['name'] = $discount['name'];
                    $d['reduction_percent'] = $discount['reduction_percent'];
                    $d['reduction_amount'] = $discount['reduction_amount'];
                    $d['date_from'] = $discount['date_from'];
                    $d['date_to'] = $discount['date_to'];
                    $d['gift_product'] = $discount['gift_product'];
                    $d['gift_product_attribute'] = $discount['gift_product_attribute'];
                    $d['partial_use'] = $discount['partial_use'];
                    $d['minimum_amount'] = $discount['minimum_amount'];
                    $result[] = $d;
                }
            }
        }
        return $result;
    }

    public static function buildAddedDiscountList($cart)
    {
        $added_discount_rules = $cart->getCartRules();
        $result = array();
        $group_id = Customer::getDefaultGroupId($cart->id_customer);
        foreach ($added_discount_rules as $rule) {
            $r = array();
            $cart_rule = new CartRule($rule['id_cart_rule']);
            $r['id'] = $rule['id_cart_rule'];
            $r['name'] = $rule['name'];
            $r['description'] = $cart_rule->description;
            $r['code'] = $rule['code'];
            $r['reduction_amount'] = $rule['reduction_amount'];
            $r['reduction_percent'] = $rule['reduction_percent'];
            //0 (PS_TAX_INC) - tax should be included,
            //1 (PS_TAX_EXC) - tax should be excluded
            if (CartUtils::isV17() && (int)$rule['reduction_amount'] != 0) {
                $cartHasTax = is_null($cart->id) ? false : Cart::getTaxesAverageUsed($cart);
                $r['real_value_incl_tax'] = Tools::ps_round($rule['reduction_amount'] * (1 + $cartHasTax / 100), 2);
                $r['real_value_excl_tax'] = Tools::ps_round($rule['reduction_amount'], 2);
                $r['tax_included'] = (int)$rule["reduction_tax"] === PS_TAX_INC ? true : false;
            } else {
                $r['real_value_incl_tax'] = Tools::ps_round($rule['value_real'], 2);
                $r['real_value_excl_tax'] = Tools::ps_round($rule['value_tax_exc'], 2);
                if (CartUtils::isV17()) {
                    $r['tax_included'] = true;
                } else {
                    $r['tax_included'] = (int)Group::getPriceDisplayMethod($group_id) === PS_TAX_INC ? true : false;
                }
            }
            $result[] = $r;
        }
        return $result;
    }

    /**
     * Create shopping cart item id from selected attributes
     * @param $product
     * @return string
     */
    public static function createShoppingCartItemId($product)
    {
        $id_product = $product['id_product'];
        $id_product_attribute = $product['id_product_attribute'];

        return $id_product . '_' . $id_product_attribute;
    }

    /**
     * Revert cart item id to id_product and id_product_attribute
     * @param $cartItemId
     * @return array
     */
    public static function revertShoppingCartItemId($cartItemId)
    {
        if ($cartItemId) {
            return explode('_', $cartItemId);
        }

        return null;
    }

    /**
     * Format product properties
     * @param $product
     * @return object
     */
    private static function formatProductData(&$product, $id_lang, $id_shop, $context, $module_name)
    {
        if ($product) {
//            $price_without_reduction = $product['price_without_reduction'];
//            $price_with_reduction = $product['$price_with_reduction'];
//            $price_with_reduction_without_tax = $product['price_with_reduction_without_tax'];
//            $total_wt = $product['total_wt'];
//            $price_wt = $product['price_wt'];
//            if (self::isV17()) {
//                $id_product = $product['id'];
//            } else {
            $id_product = $product['id_product'];
//            }

            $product['gallery'] = self::assignImages($id_product, $id_lang);

            //convert price string to double
            $product['price'] = round($product['price'], 2);

            //convert price string to double
            $product['price_tax_exc'] = round($product['price_tax_exc'], 2);

            //convert price string to double
            $product['unit_price'] = round($product['unit_price'], 2);

            //convert price string to double
            $product['total'] = round($product['total'], 2);


            //don't return empty string
            $strNew = $product['new'];
            $strNew = empty($strNew) ? '0' : $strNew;
            $product['new'] = $strNew;

            //don't return empty string
            $strEmbeddedNew = $strNew = $product['embedded_attributes']['new'];
            $strEmbeddedNew = empty($strEmbeddedNew) ? '0' : $strEmbeddedNew;
            $product['embedded_attributes']['new'] = $strEmbeddedNew;

            //set buy request to map selected selections
            $buyRequestData = self::makeBuyRequest($product);
            $buyRequest = new BuyRequest();
            $buyRequest->group = $buyRequestData;
            $product['buy_request'] = $buyRequest;

            $productCore = new Product($id_product, true, $id_lang, $id_shop, $context);

            //product full description
            $product['description'] = $productCore->description;
            $feature = $productCore->getFrontFeatures($id_lang);
            $product['features'] = $feature;

            // Set image for profuct in cart
            // JMIOSV2-7199 : [Woolove app] return 2 image after change product option when update cart and the first image is blur when zoom out
//            if (self::isV17()) {
//                if (ImageType::getFormattedName('thickbox')) {
//                    $product['image'] = $product['cover']['bySize'][ ImageType::getFormattedName('thickbox')]['url'];
//                } else {
//                    $product['image'] = $product['cover']['bySize'][ ImageType::getFormattedName('home')]['url'];
//                }
//                if (!$product['image'] && ImageType::getFormattedName('large')) {
//                    $product['image'] = $product['cover']['bySize'][ImageType::getFormattedName('large')]['url'];
//                }
//            } else {
            $product['image'] = CartUtils::getProductImageUrl($product, $context);
//            }
            // set show_price flag according to backoffice setting
            $customerGroup = new Group(Customer::getDefaultGroupId($context->cart->id_customer));
            $product['show_price'] = $customerGroup->show_prices;

//            $attributeValues = ProductUtils::convertAttributes($productCore, $id_lang);
//            if ($attributeValues && count($attributeValues) == 2) {
//                $groupValues = $attributeValues[0];
//                $combinationValues = $attributeValues[1];
//
//                $product['groups'] = $groupValues;
//                $product['combinations'] = $combinationValues;
//            }

            $shopping_cart_item_id = self::createShoppingCartItemId($product);

            $product['ps_item_id'] = $shopping_cart_item_id;

            $product['quantity'] = $product['stock_quantity'];

            $product['pack_items'] = self::getPackItems($id_product, $id_lang, $context->cart->id_customer);

            $productDetailService = new ProductDetailService($module_name);

            $is_tax_included = Product::getTaxCalculationMethod((int)$context->cart->id_customer) == PS_TAX_INC ? true : false;
            $product['price_without_quantity_discount'] = Product::getPriceStatic(
                (int)$id_product,
                $is_tax_included,
                $product['id_product_attribute'],
                2,
                null,
                false,
                true,
                1,
                false,
                $context->cart->id_customer,
                null,
                null
            );
            $product['price_without_reduction'] = $productCore->getPriceWithoutReduct(!$is_tax_included, $product['id_product_attribute'], 2);
            $product['quantity_discounts'] = $productDetailService->getProductQuantityDiscount(
                $id_lang,
                $id_shop,
                $id_product,
                $context->cart->id_customer,
                $context->currency->id,
                $product['id_product_attribute'],
                $context
            );

            // Set verified review for cart item
            if (CartUtils::isV17()) {
                $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                $moduleManager = $moduleManagerBuilder->build();
                if ($moduleManager->isInstalled('netreviews')
                    && Module::getInstanceByName('netreviews')->active) {
                    $product['verified_review'] = CartUtils::assignVerifiedReview($id_lang, $id_shop, $id_product, $module_name);
                }
            }
            //set ProductUr
            $category = Category::getLinkRewrite($productCore->id_category_default, $id_lang);
            $url = $context->link->getProductLink($productCore, $productCore->link_rewrite, $category, $productCore->ean13);
            $product['product_url'] = $url;
        }

        return $product;
    }


    /**
     * Remove currency symbol from price text
     * @param string $priceText
     * @return double
     */
    public static function convertPriceTextToValue($priceText, $context)
    {
        if ($priceText) {
            if (is_numeric($priceText)) {
                return $priceText;
            }

            $repository = Tools::getCldr($context);
            $currency = new \ICanBoogie\CLDR\Currency($repository->getRepository(), $context->currency->iso_code);
            $localized_currency = $currency->localize($repository->getCulture());
            if (!JmValidator::isNullOrEmptyObject($localized_currency)
                && !JmValidator::isNullOrEmptyObject($localized_currency->locale->numbers->symbols)) {
                $symbols = $localized_currency->locale->numbers->symbols;
                $groupPoint = $symbols['group'];
                $decimalPoint = $symbols['decimal'];
                // First: remove group point and then replace decimal point to .
                $priceText = str_replace($groupPoint, '', $priceText);
                $priceText = str_replace($decimalPoint, '.', $priceText);
                // Second: replace other character except number and - and .
                $result = preg_replace('/[^0-9-.]+/', '', $priceText);
                $format = new NumberFormatter("en", NumberFormatter::DECIMAL);
                $result = $format->parse($result);
                return $result;
            }

            // If can not get symbols the try to get number using intl extension
            $locale = $context->language->iso_code;
            // Remove characters except number and - and . and ,
            $priceText = preg_replace('/[^0-9-.,]+/', '', $priceText);
            $format = new NumberFormatter($locale, NumberFormatter::DECIMAL);
            $result = (float)$format->parse($priceText);
            return $result;
        }
        return 0;
    }

    private static function addTextureUrl($product)
    {
        foreach ($product['groups'] as &$group) {
            if (strcmp($group['group_type'], 'color') === 0) {
                foreach ($group['attributes'] as $key => $value) {
                    if (file_exists(_PS_COL_IMG_DIR_ . $key . '.jpg')) {
                        $group['attributes_texture'][$key] = _PS_BASE_URL_ . '/img/co/' . $key . '.jpg';
                    } else {
                        $group['attributes_texture'][$key] = null;
                    }
                }
            }
        }
        return $product;
    }

    /**
     * Convert totals from object to array
     * @param $cart
     * @return array
     */
    private static function formatCartTotals($cart)
    {
        $result = array();
        if ($cart) {
            $totals = $cart['totals'];
            if ($totals) {
                $total = $totals['total'];
                $total_including_tax = $totals['total_including_tax'];
                $total_excluding_tax = $totals['total_excluding_tax'];

                if ($total) {
                    array_push($result, $total);
                }

                if ($total_including_tax) {
                    array_push($result, $total_including_tax);
                }

                if ($total_excluding_tax) {
                    array_push($result, $total_excluding_tax);
                }
            }
        }
        return $result;
    }

    /**
     * Make buy request from product selection
     * @param $product
     * @return array|null
     */
    private static function makeBuyRequest($product)
    {
        if ($product) {
            $id_product = $product['id_product'];
            $id_product_attribute = $product['id_product_attribute'];

            $attributes = Product::getAttributesParams($id_product, $id_product_attribute);
            if ($attributes && count($attributes) > 0) {
                $result = array();
                foreach ($attributes as $attribute) {
                    $attributeGroup = $attribute['id_attribute_group'];
                    $attributeValue = $attribute['id_attribute'];

                    $result[$attributeGroup] = $attributeValue;
                }
                return empty($result) ? null : $result;
            }
        }
        return null;
    }

    public static function getProductImageUrl($product, $context)
    {
        $finalImageUrl = '';

        if (!isset($product)) {
            return $finalImageUrl;
        }

        // Create image URL
        $id_product = $product['id_product'];
        $image = Image::getBestImageAttribute(
            $context->shop->id,
            $context->language->id,
            $id_product,
            $product["id_product_attribute"]
        );
        if ($image['id_image'] == null || $image['id_image'] == 0) {
            $image = Image::getCover($id_product);
        }

        $finalImageUrl = ProductDataTransform::productImage($image['id_image']);

        return $finalImageUrl;
    }

    protected static function sortCartItems($products)
    {
        if ($products && count($products) > 0) {
            usort($products, function ($p1, $p2) {
                return strtotime($p1['date_add']) - strtotime($p2['date_add']);
            });
        }

        return $products;
    }

    protected static function getPackItems($id_product, $id_lang, $id_customer)
    {
        $pack_items = Pack::isPack($id_product) ? Pack::getItemTable($id_product, $id_lang, true) : array();
        $group_id = Customer::getDefaultGroupId($id_customer);
        $useTax = Group::getPriceDisplayMethod($group_id) === PS_TAX_INC ? true : false;
        foreach ($pack_items as &$item) {
            // get Image by id
            if (sizeof($item['id_image']) > 0) {
                $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $link = new LinkCore();
                $imageUrl = $link->getImageLink($item['link_rewrite'], $item['id_image'], (self::isV17()) ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home'));
                $item['imageUrl'] = $protocol_link . $imageUrl;
                $stockAvailable = new StockAvailable(StockAvailableCore::getStockAvailableIdByProductId($item['id_product']));
                $item['out_of_stock'] = ProductCore::isAvailableWhenOutOfStock($stockAvailable->out_of_stock);
            }
            $item['price'] = Product::getPriceStatic(
                (int)$item['id_product'],
                $useTax,
                $item['id_product_attribute'],
                2
            );
            $prod = new Product($item['id_product']);
            $item['price_without_reduction'] = $prod->getPriceWithoutReduct(!$useTax, $item['id_product_attribute'], 2);
        }
        return $pack_items;
    }

    protected static function assignImages($id_product, $id_lang)
    {
        $product = new ProductCore($id_product);
        $images = $product->getImages($id_lang);

        $product_gallery = array();

        foreach ($images as $k => $image) {
            $id_image = (int)$image['id_image'];
            $images = ProductDataTransform::productImages($id_image);
            $product_gallery[] = $images;
        }

        return $product_gallery;
    }

    public static function assignProductCombinations($presentedCart, $id_lang, $id_shop, $context)
    {
        if (isset($presentedCart)) {
            $products = $presentedCart['products'];
            $combination_list = array();
            if ($products && count($products) > 0) {
                foreach ($products as &$product) {
                    if (!isset($combination_list[$product['id_product']])) {
                        $combination = array();
                        $product_core = new Product($product['id_product']);
                        $attributeValues = ProductUtils::convertAttributes($product_core, $id_lang);
                        if ($attributeValues && count($attributeValues) == 2) {
                            $groupValues = $attributeValues[0];
                            $combinationValues = $attributeValues[1];
                            $combination['id_product'] = $product['id_product'];
                            $combination['allow_order_out_of_stock'] = (bool)ProductCore::isAvailableWhenOutOfStock($product['out_of_stock']);
                            $combination['groups'] = $groupValues;
                            $combination['combinations'] = $combinationValues;
                            $combination_list[$product['id_product']] = $combination;
                        }
                    }
                }
            }
            $presentedCart['combination_list'] = array_values($combination_list);
        }
        return $presentedCart;
    }

    public static function assignProductCombinationsV2($presentedCart, $id_lang, $id_shop, $context)
    {
        if (isset($presentedCart)) {
            $products = $presentedCart['products'];
            $combination_list = array();
            if ($products && count($products) > 0) {
                $uniqueProducts = array();

                foreach ($products as $product) {
                    $buyRequest = isset($product['buy_request']) ? $product['buy_request'] : new BuyRequest();
                    if (!empty($buyRequest->group)) {
                        $uniqueProducts[$product['id_product']][] = $buyRequest->group;
                        if (!isset($combination_list[$product['id_product']])) {
                            $combination_list[$product['id_product']]['id_product'] = $product['id_product'];
                            $combination_list[$product['id_product']]['allow_order_out_of_stock'] = (bool)ProductCore::isAvailableWhenOutOfStock($product['out_of_stock']);
                        }
                    }
                }

                foreach ($uniqueProducts as $id_product => $buy_requests) {
                    $attributeValues = ProductUtils::convertAttributes($id_product, $id_lang, $buy_requests);
                    if ($attributeValues && count($attributeValues) == 2) {
                        $groupValues = $attributeValues[0];
                        $combinationValues = $attributeValues[1];

                        if (!isset($combination_list[$id_product]['groups'])) {
                            $combination_list[$id_product]['groups'] = $groupValues;
                        }

                        if (!isset($combination_list[$id_product]['combinations'])) {
                            $combination_list[$id_product]['combinations'] = $combinationValues;
                        }
                    }
                }
            }
            $presentedCart['combination_list'] = array_values($combination_list);
        }

        return $presentedCart;
    }

    public static function assignVerifiedReview($id_lang, $id_shop, $id_product, $module_name)
    {
        $o_av = new NetReviewsModel();
        $multisite = Configuration::get('AV_MULTISITE');
        $av_idshop = (!empty($multisite)) ? $id_shop : null;
        $productReviewService = new ProductReviewService($module_name);
        if (Configuration::get('AV_MULTILINGUE', null, null, $av_idshop) == 'checked') {
            $iso_lang = pSQL(Language::getIsoById($id_lang));
            $group_name = $productReviewService->getIdConfigurationGroup($iso_lang);
        }
        $stats_product = $o_av->getStatsProduct($id_product, $group_name, $av_idshop);
        return $stats_product ? $stats_product : null;
    }
}

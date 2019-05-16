<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * API to send the details of checkout page i.e shipping address,
 * billing address,product details and shipping methods details
 * Called from checkout page in APP
 */

require_once 'AppCore.php';

class AppCheckout extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     *
     * This is abstract function in appcore
     * @return json
     */
    public function getPageData()
    {
        if (!(int) Tools::getValue('session_data', 0)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Cart id is missing'),
                'AppCheckout'
            );
            $this->writeLog('Cart id is missing');
        } else {
            if ($this->validateCustomer()) {
                $this->context->cart = new Cart(
                    (int) Tools::getValue('session_data', 0),
                    false,
                    null,
                    null,
                    $this->context
                );
                if (!Validate::isLoadedObject($this->context->cart)) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Cart not found'),
                        'AppCheckout'
                    );
                    $this->writeLog('Unable to load cart.');
                } else {
                    $id_shipping = Tools::getValue('id_shipping_address', '');
                    if ($id_shipping == '') {
                        if ($this->context->cart->id_address_delivery > 0) {
                            $id_shipping = $this->context->cart->id_address_delivery;
                        } else {
                            $id_shipping = Address::getFirstCustomerAddressId(
                                (int) $this->context->cookie->id_customer
                            );
                        }
                    }
                    $id_billing = Tools::getValue('id_billing_address', '');
                    if ($id_billing == '') {
                        if ($this->context->cart->id_address_invoice > 0) {
                            $id_billing = $this->context->cart->id_address_invoice;
                        } else {
                            $id_billing = Address::getFirstCustomerAddressId(
                                (int) $this->context->cookie->id_customer
                            );
                        }
                    }
                    
                    if ($this->getShippingAddress($id_shipping)) {
                        if ($this->getBillingAddress($id_billing)) {
                            $this->context->cart->id_currency = $this->context->currency->id;
                            $this->context->cart->id_carrier = 0;
                            if (Tools::getIsset('shipping_method')) {
                                $shipping_method = Tools::getValue('shipping_method');
                                $id_carrier = array(
                                    $this->context->cart->id_address_delivery => $shipping_method . ','
                                );
                                $this->context->cart->setDeliveryOption($id_carrier);
                            }
                            /* Update cookie value after selecting shipping for particular product */
                            if (Tools::getIsset('pp_shippings')) {
                                $carriers_array = array();
                                $selected_carriers = Tools::getValue('pp_shippings', Tools::jsonEncode(array()));
                                $selected_carriers = Tools::jsonDecode($selected_carriers);
                                if (!empty($selected_carriers)) {
                                    foreach ($selected_carriers as $data) {
                                        $carriers_array[$data->product_id] = $data->shipping_id;
                                    }
                                    $this->context->cookie->kb_selected_carrier = serialize($carriers_array);
                                }
                            }


                            $this->context->cart->update();
                            $this->context->cookie->id_cart = (int) $this->context->cart->id;
                            $this->context->cookie->write();
                            $this->content['checkout_page']['per_products_shipping'] = "0";
                            $this->content['checkout_page']['per_products_shipping_methods'] = array();
                            $this->getKbCarrierList();
                            $cart_data = $this->fetchList();
                            /*Set currency code and cart total */
                            $this->content['total_cost'] = (float)Tools::ps_round(
                                (float)$this->context->cart->getOrderTotal(true, Cart::BOTH),
                                2
                            );
                            $this->content['currency_code'] = $this->context->currency->iso_code;
                            $this->content['currency_symbol'] = $this->context->currency->sign;
                            $cart_summary = $cart_data['summary'];
                            $customized_data = $cart_data['customized_data'];
                            $cart_products = array();
                            $index = 0;
                            foreach ($cart_summary['products'] as $product) {
                                $quantity_displayed = 0;
                                $extra_product_line = false;
                                $customization = false;
                                $product_obj = new Product((int) $product['id_product']);
                                $cart_products[$index] = array(
                                    'product_id' => $product_obj->id,
                                    'title' => $product['name'],
                                    'is_gift_product' => "0",
                                    'id_product_attribute' => $product['id_product_attribute'],
                                    'id_address_delivery' => $product['id_address_delivery'],
                                    'stock' => true
                                );
                                $cart_products[$index]['discount_price'] = '';
                                $cart_products[$index]['discount_percentage'] = '';
                                $cart_products[$index]['images'] = $this->context->link->getImageLink(
                                    $product['link_rewrite'],
                                    $product['id_image'],
                                    $this->getImageType('large')
                                );
                                $p_id = $product['id_product'];
                                $p_aid = $product['id_product_attribute'];
                                $da_id = $product['id_address_delivery'];
                                if (isset($customized_data[$p_id][$p_aid][$da_id])) {
                                    $c_i = 0;
                                    $customization = true;
                                    foreach ($customized_data[$p_id][$p_aid][$da_id] as $id_c => $customization) {
                                        foreach ($customization['datas'] as $type => $custom_data) {
                                            if ($type == Product::CUSTOMIZE_TEXTFIELD) {
                                                foreach ($custom_data as $tf) {
                                                    $cart_products[$index]['customizable_items'][$c_i] = array(
                                                        'id_customization_field' => $id_c,
                                                        'type' => "text",
                                                        'text_value' => $tf['value'],
                                                        'quantity' => $customization['quantity']
                                                    );
                                                    $t_i = 0;
                                                    $t_str = 'customizable_items';
                                                    if ($tf['name']) {
                                                        $cart_products[$index][$t_str][$c_i]['title'] = $tf['name'];
                                                    } else {
                                                        $cart_products[$index][$t_str][$c_i]['title'] = "Text #"
                                                                .($t_i+1);
                                                        $t_i++;
                                                    }
                                                    $c_i++;
                                                }
                                            }
                                        }
                                        $quantity_displayed = $quantity_displayed + $customization['quantity'];
                                    }
                                    if (($product['quantity'] - $quantity_displayed) > 0) {
                                        $extra_product_line = true;
                                    }
                                }
                                if ($customization) {
                                    $cart_products[$index]['price'] = $this->formatPrice(
                                        $product['total_customization_wt']
                                    );
                                    $cart_products[$index]['quantity'] = $product['customizationQuantityTotal'];
                                    $cart_products[$index]['product_items'] = array(
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Quantity'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['customizationQuantityTotal']
                                        ),
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Reference'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['reference']
                                        )
                                    );
                                } else {
                                    $cart_products[$index]['price'] = $this->formatPrice($product['total_wt']);
                                    $cart_products[$index]['quantity'] = $product['cart_quantity'];
                                    $cart_products[$index]['product_items'] = array(
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Quantity'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['cart_quantity']
                                        ),
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Reference'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['reference']
                                        )
                                    );
                                }
                                if (isset($product['attributes']) && $product['attributes']) {
                                    $cart_products[$index]['product_items'][] = array(
                                        'name' => parent::getTranslatedTextByFileAndISO(
                                            Tools::getValue('iso_code', false),
                                            $this->l('Attributes'),
                                            'AppCheckout'
                                        ),
                                        'value' => $product['attributes']
                                    );
                                }
                                if (!isset($cart_products[$index]['customizable_items'])) {
                                    $cart_products[$index]['customizable_items'] = array();
                                } else {
                                    $temp_array = array();
                                    $temp_customized_data = $cart_products[$index]['customizable_items'];
                                    unset($cart_products[$index]['customizable_items']);
                                    $cust_index = 0;
                                    foreach ($temp_customized_data as $data) {
                                        $t_str = 'customizable_grp_items';
                                        if (isset($temp_array[$data['id_customization_field']])) {
                                            $t1 = $temp_array[$data['id_customization_field']];
                                            $cart_products[$index]['customizable_items'][$t1][$t_str][] = $data;
                                        } else {
                                            $temp_array[$data['id_customization_field']] = $cust_index;
                                            $cart_products[$index]['customizable_items'][$cust_index][$t_str][] = $data;
                                            $cust_index++;
                                        }
                                    }
                                    unset($temp_array);
                                    unset($temp_customized_data);
                                }
                                $index++;
                                if ($extra_product_line) {
                                    $cart_products[$index] = array(
                                        'product_id' => $product_obj->id,
                                        'title' => $product['name'],
                                        'is_gift_product' => "0",
                                        'id_product_attribute' => $product['id_product_attribute'],
                                        'id_address_delivery' => $product['id_address_delivery']
                                    );
                                    if ($product['cart_quantity'] <= StockAvailable::getQuantityAvailableByProduct(
                                        $product_obj->id,
                                        $product['id_product_attribute']
                                    )) {
                                        $cart_products[$index]['stock'] = true;
                                    } else {
                                        $cart_products[$index]['stock'] = false;
                                    }
                                    $cart_products[$index]['price'] = $this->formatPrice($product['total_wt']);
                                    $cart_products[$index]['discount_price'] = '';
                                    $cart_products[$index]['discount_percentage'] = '';
                                    $cart_products[$index]['images'] = $this->context->link->getImageLink(
                                        $product['link_rewrite'],
                                        $product['id_image'],
                                        $this->getImageType('large')
                                    );
                                    $cart_products[$index]['product_items'] = array(
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Quantity'),
                                                'AppCheckout'
                                            ),
                                            'value' => ($product['cart_quantity'] - $quantity_displayed)
                                        ),
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Reference'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['reference']
                                        )
                                    );
                                    if (isset($product['attributes']) && $product['attributes']) {
                                        $cart_products[$index]['product_items'][] = array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Attributes'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['attributes']
                                        );
                                    }
                                    $tot_qty = $product['cart_quantity'] - $quantity_displayed;
                                    $cart_products[$index]['quantity'] = $tot_qty;
                                    $cart_products[$index]['customizable_items'] = array();
                                    $index++;
                                }
                            }
                            if (!empty($cart_summary['gift_products'])) {
                                foreach ($cart_summary['gift_products'] as $product) {
                                    $cart_products[$index] = array(
                                        'product_id' => $product['id_product'],
                                        'title' => $product['name'],
                                        'is_gift_product' => "1",
                                        'stock' => true,
                                        'id_product_attribute' => $product['id_product_attribute'],
                                        'id_address_delivery' => $product['id_address_delivery'],
                                        'price' => "Gift",
                                        'discount_price' => "",
                                        'discount_percentage' => ""
                                    );
                                    $cart_products[$index]['images'] = $this->context->link->getImageLink(
                                        $product['link_rewrite'],
                                        $product['id_image'],
                                        $this->getImageType('large')
                                    );
                                    $cart_products[$index]['product_items'] = array(
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Quantity'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['cart_quantity']
                                        ),
                                        array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Reference'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['reference']
                                        )
                                    );
                                    if (isset($product['attributes']) && $product['attributes']) {
                                        $cart_products[$index]['product_items'][] = array(
                                            'name' => parent::getTranslatedTextByFileAndISO(
                                                Tools::getValue('iso_code', false),
                                                $this->l('Attributes'),
                                                'AppCheckout'
                                            ),
                                            'value' => $product['attributes']
                                        );
                                    }
                                    $cart_products[$index]['quantity'] = $product['cart_quantity'];
                                    $cart_products[$index]['customizable_items'] = array();
                                    $index++;
                                }
                            }
                            $this->content['status'] = "success";
                            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('Cart information loaded successfully'),
                                'AppCheckout'
                            );
                            $this->writeLog("Cart information loaded successfully");

                            $this->content['checkout_page']['products'] = $cart_products;

                            $cart_total_details = array();
                            $cart_total_details[] = array(
                                'name' => parent::getTranslatedTextByFileAndISO(
                                    Tools::getValue('iso_code', false),
                                    $this->l('Total Products(tax excl.)'),
                                    'AppCheckout'
                                ),
                                'value' => $this->formatPrice($cart_summary['total_products'])
                            );
                            if ($cart_summary['total_discounts'] > 0) {
                                $cart_total_details[] = array(
                                    'name' => parent::getTranslatedTextByFileAndISO(
                                        Tools::getValue('iso_code', false),
                                        $this->l('Total vouchers'),
                                        'AppCheckout'
                                    ),
                                    'value' => "-" .$this->formatPrice($cart_summary['total_discounts'])
                                );
                            }
                            if ($cart_summary['total_wrapping'] > 0 && $this->context->cart->gift) {
                                $cart_total_details[] = array(
                                    'name' => parent::getTranslatedTextByFileAndISO(
                                        Tools::getValue('iso_code', false),
                                        $this->l('Total gift wrapping'),
                                        'AppCheckout'
                                    ),
                                    'value' => $this->formatPrice($cart_summary['total_wrapping'])
                                );
                            }
                            if ($cart_summary['total_shipping'] > 0) {
                                $cart_total_details[] = array(
                                    'name' => parent::getTranslatedTextByFileAndISO(
                                        Tools::getValue('iso_code', false),
                                        $this->l('Total shipping'),
                                        'AppCheckout'
                                    ),
                                    'value' => $this->formatPrice($cart_summary['total_shipping'])
                                );
                            } else {
                                if (!$cart_summary['is_virtual_cart']) {
                                    $cart_total_details[] = array(
                                        'name' => parent::getTranslatedTextByFileAndISO(
                                            Tools::getValue('iso_code', false),
                                            $this->l('Total shipping'),
                                            'AppCheckout'
                                        ),
                                        'value' => parent::getTranslatedTextByFileAndISO(
                                            Tools::getValue('iso_code', false),
                                            $this->l('Free shipping!'),
                                            'AppCheckout'
                                        )
                                    );
                                }
                            }
                            if ($cart_summary['total_tax'] > 0) {
                                $cart_total_details[] = array(
                                    'name' => parent::getTranslatedTextByFileAndISO(
                                        Tools::getValue('iso_code', false),
                                        $this->l('Total Tax'),
                                        'AppCheckout'
                                    ),
                                    'value' => $this->formatPrice($cart_summary['total_tax'])
                                );
                            }
                            $cart_total_details[] = array(
                                'name' => parent::getTranslatedTextByFileAndISO(
                                    Tools::getValue('iso_code', false),
                                    $this->l('Total Price'),
                                    'AppCheckout'
                                ),
                                'value' => $this->formatPrice($cart_summary['total_price'])
                            );

                            if (!empty($cart_summary['discounts'])) {
                                $index = 0;
                                $voucher_data = array();
                                foreach ($cart_summary['discounts'] as $voucher) {
                                    if ((float) $voucher['value_real'] == 0) {
                                        continue;
                                    }
                                    $voucher_data[$index] = array(
                                        'id' => $voucher['id_cart_rule'],
                                        'name' => $voucher['name'],
                                        'value' => "-" . $this->formatPrice($voucher['value_real']),
                                    );
                                    $index++;
                                }
                                $this->content['checkout_page']['vouchers'] = $voucher_data;
                            } else {
                                $this->content['checkout_page']['vouchers'] = array();
                            }

                            $a_txt = parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('(Additional Cost of '),
                                'AppCheckout'
                            );
                            $gift_wrapping_cost = Tools::convertPriceFull(
                                $this->context->cart->getGiftWrappingPrice(),
                                null,
                                $this->context->currency
                            );
                            $this->content['gift_wrapping'] = array(
                                'available' => Configuration::get('PS_GIFT_WRAPPING'),
                                'applied' => ($this->context->cart->gift) ? "1" : "0",
                                'message' => $this->context->cart->gift_message
                                    ? $this->context->cart->gift_message
                                    : "",
                                'cost_text' => ($this->context->cart->getGiftWrappingPrice() > 0) ?
                                    $a_txt . $this->formatPrice($gift_wrapping_cost) . " )" : ""
                            );
                            $g_c = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
                            $c_item = Cart::getNbProducts($this->context->cart->id);
                            $this->content['checkout_page']['guest_checkout_enabled'] = $g_c;
                            $this->content['checkout_page']['cart']['total_cart_items'] = $c_item;
                            if (CartRule::isFeatureActive()) {
                                $this->content['checkout_page']['voucher_allowed'] = "1";
                            } else {
                                $this->content['checkout_page']['voucher_allowed'] = "0";
                            }


                            $currency = Currency::getCurrency((int) $this->context->cart->id_currency);
                            $minimal_purchase = Tools::convertPrice(
                                (float) Configuration::get('PS_PURCHASE_MINIMUM'),
                                $currency
                            );

                            if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase) {
                                $this->content['checkout_page']['minimum_purchase_message'] = sprintf(
                                    parent::getTranslatedTextByFileAndISO(
                                        Tools::getValue('iso_code', false),
                                        $this->l('A minimum purchase total of %1s is required to validate'
                                                . ' your order, current purchase total is %2s'),
                                        'AppCheckout'
                                    ),
                                    Tools::displayPrice($minimal_purchase, $currency),
                                    Tools::displayPrice(
                                        $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS),
                                        $currency
                                    )
                                );
                            } else {
                                $this->content['checkout_page']['minimum_purchase_message'] = "";
                            }

                            $this->content['checkout_page']['totals'] = $cart_total_details;
                        }
                    }
                }
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Fetch cart summary
     *
     * @return array cart summary
     */
    public function fetchList()
    {
        $summary = $this->context->cart->getSummaryDetails();
        $customizedDatas = Product::getAllCustomizedDatas($this->context->cart->id);

        /* override customization tax rate with real tax (tax rules) */
        if ($customizedDatas) {
            foreach ($summary['products'] as &$productUpdate) {
                $productUpdate['image'] = $this->context->link->getImageLink(
                    $productUpdate['link_rewrite'],
                    $productUpdate['id_image'],
                    $this->getImageType('medium')
                );
                $productId = (int) isset($productUpdate['id_product']) ?
                    $productUpdate['id_product'] : $productUpdate['product_id'];
                $productAttributeId = (int) isset($productUpdate['id_product_attribute']) ?
                    $productUpdate['id_product_attribute'] : $productUpdate['product_attribute_id'];

                if (isset($customizedDatas[$productId][$productAttributeId])) {
                    $productUpdate['tax_rate'] = Tax::getProductTaxRate(
                        $productId,
                        $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}
                    );
                }
            }

            Product::addCustomizationPrice($summary['products'], $customizedDatas);
        }

        $cart_product_context = $this->context->cloneContext();
        foreach ($summary['products'] as $key => &$product) {
            $product['quantity'] = $product['cart_quantity']; // for compatibility with 1.2 themes

            if ($cart_product_context->shop->id != $product['id_shop']) {
                $cart_product_context->shop = new Shop((int) $product['id_shop']);
            }
            $null = '';
            $product['price_without_specific_price'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                6,
                null,
                false,
                false,
                1,
                false,
                null,
                null,
                null,
                $null,
                true,
                true,
                $cart_product_context
            );

            if (Product::getTaxCalculationMethod()) {
                $product['is_discounted'] = Tools::ps_round(
                    $product['price_without_specific_price'],
                    _PS_PRICE_COMPUTE_PRECISION_
                ) != Tools::ps_round(
                    $product['price'],
                    _PS_PRICE_COMPUTE_PRECISION_
                );
            } else {
                $product['is_discounted'] = Tools::ps_round(
                    $product['price_without_specific_price'],
                    _PS_PRICE_COMPUTE_PRECISION_
                ) != Tools::ps_round(
                    $product['price_wt'],
                    _PS_PRICE_COMPUTE_PRECISION_
                );
            }
        }

        /* Get available cart rules and unset the cart rules already in the cart */
        $available_cart_rules = CartRule::getCustomerCartRules(
            $this->context->language->id,
            (isset($this->context->cart->id_customer) ? $this->context->cart->id_customer : 0),
            true,
            true,
            true,
            $this->context->cart,
            false,
            true
        );
        $cart_cart_rules = $this->context->cart->getCartRules();
        foreach ($available_cart_rules as $key => $available_cart_rule) {
            foreach ($cart_cart_rules as $cart_cart_rule) {
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule']) {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
            }
        }

        unset($summary['delivery']);
        unset($summary['delivery_state']);
        unset($summary['invoice']);
        unset($summary['invoice_state']);
        unset($summary['formattedAddresses']);

        return array('summary' => $summary, 'customized_data' => $customizedDatas);
    }

    /**
     * Validate customer i.e email is valid or not or customer with provided email address is exist or not
     *
     * @return bool
     */
    public function validateCustomer()
    {
        $email = Tools::getValue('email', '');
        if (!Validate::isEmail($email)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is not valid'),
                'AppCheckout'
            );
            $this->writeLog('Email address is not valid');
            return false;
        } else {
            if (Customer::customerExists(strip_tags($email), false, false)) {
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email, null, false);

                $customer = new Customer($customer_tmp->id);

                /* Update Context */
                $this->context->customer = $customer;
                $this->context->cookie->id_customer = (int) $customer->id;
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->logged = 1;
                $this->context->cookie->email = $customer->email;
                $this->context->cookie->is_guest = $customer->is_guest;
                return true;
            } else {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer with this email not exist'),
                    'AppCheckout'
                );
                $this->writeLog('Customer with this email not exist');
                return false;
            }
        }
    }

    /**
     * Set the selected address to cart context and set the address details in shipping_address paramter
     *
     * @param int $id_shipping shipping address id
     */
    public function getShippingAddress($id_shipping)
    {
        $address = new Address($id_shipping);
        if (!validate::isLoadedObject($address)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Unable to get shipping address details'),
                'AppCheckout'
            );
            $this->writeLog('Address object is not valid');
            return false;
        } else {
            $this->context->cart->id_address_delivery = (int) $id_shipping;
            $this->context->cart->autosetProductAddress();
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            if (!$this->context->cart->update()) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('An error occurred while updating your cart.'),
                    'AppCheckout'
                );
                $this->writeLog('An error occurred while updating your cart.');
                return false;
            }

            if (!$this->context->cart->isMultiAddressDelivery()) {
                $this->context->cart->setNoMultishipping();
            }
            $errors = array();
            $address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers(false, $errors);
            if (count($address_without_carriers) && !$this->context->cart->isVirtualCart()) {
                $flag_error_message = false;
                foreach ($errors as $error) {
                    if ($error == Carrier::SHIPPING_WEIGHT_EXCEPTION && !$flag_error_message) {
                        $this->content['status'] = 'failure';
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('The product selection cannot be delivered by the available carrier(s):'
                                    . ' it is too heavy. Please amend your cart to lower its weight.'),
                            'AppCheckout'
                        );
                        $this->writeLog(
                            'The product selection cannot be delivered by the available carrier(s): ' .
                                ' it is too heavy. Please amend your cart to lower its weight.'
                        );
                        $flag_error_message = true;
                    } elseif ($error == Carrier::SHIPPING_PRICE_EXCEPTION && !$flag_error_message) {
                        $this->content['status'] = 'failure';
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('The product selection cannot be delivered by the available carrier(s).'
                                    . ' Please amend your cart.'),
                            'AppCheckout'
                        );
                        $this->writeLog(
                            'The product selection cannot be delivered by the' .
                                ' available carrier(s). Please amend your cart.'
                        );
                        $flag_error_message = true;
                    } elseif ($error == Carrier::SHIPPING_SIZE_EXCEPTION && !$flag_error_message) {
                        $this->content['status'] = 'failure';
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('The product selection cannot be delivered by the available carrier(s):'
                                    . ' its size does not fit. Please amend your cart to reduce its size.'),
                            'AppCheckout'
                        );
                        $this->writeLog(
                            'The product selection cannot be delivered by the available' .
                                ' carrier(s): its size does not fit. Please amend your cart to reduce its size.'
                        );
                        $flag_error_message = true;
                    }
                }
                if (count($address_without_carriers) > 1 && !$flag_error_message) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('There are no carriers that deliver to some addresses you selected.'),
                        'AppCheckout'
                    );
                    $this->writeLog('There are no carriers that deliver to some addresses you selected.');
                    return false;
                } elseif ($this->context->cart->isMultiAddressDelivery() && !$flag_error_message) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('There are no carriers that deliver to one address you selected.'),
                        'AppCheckout'
                    );
                    $this->writeLog('There are no carriers that deliver to one of the address you selected.');
                    return false;
                } elseif (!$flag_error_message) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('There are no carriers that deliver to the address you selected.'),
                        'AppCheckout'
                    );
                    $this->writeLog('There are no carriers that deliver to the address you selected.');
                    return false;
                }
            }
            $shipping_address = array();
            $shipping_address['id_shipping_address'] = $address->id;
            $shipping_address['firstname'] = $address->firstname;
            $shipping_address['lastname'] = $address->lastname;
            $shipping_address['mobile_no'] = (!empty($address->phone_mobile)) ?
                $address->phone_mobile . "," . $address->phone : $address->phone . "," . $address->phone_mobile;
            $shipping_address['mobile_no'] = rtrim($shipping_address['mobile_no'], ',');
            $shipping_address['company'] = $address->company;
            $shipping_address['address_1'] = $address->address1;
            $shipping_address['address_2'] = $address->address2;
            $shipping_address['city'] = $address->city;
            if ($address->id_state != 0) {
                $shipping_address['state'] = State::getNameById($address->id_state);
            } else {
                $shipping_address['state'] = "";
            }
            $shipping_address['country'] = Country::getNameById(
                $this->context->language->id,
                $address->id_country
            );
            $shipping_address['postcode'] = $address->postcode;
            $shipping_address['alias'] = $address->alias;
            $this->content['checkout_page']['shipping_address'] = $shipping_address;
            return true;
        }
    }

    /**
     * Get list of available carriers on store
     *
     */
    public function getKbCarrierList()
    {
        $delivery_option_list = $this->context->cart->getDeliveryOptionList();
        $delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
        $is_virtual_cart = $this->context->cart->isVirtualCart();
        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }
        $carrier_array = array();
        if ($is_virtual_cart) {
            $this->content['checkout_page']['shipping_available'] = "1";
            $this->content['checkout_page']['shipping_message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('No Delivery Method Required'),
                'AppCheckout'
            );
            $this->content['checkout_page']['shipping_methods'] = array();
        } else {
            $index = 0;
            foreach ($delivery_option_list as $id_address => $option_list) {
                foreach ($option_list as $key => $option) {
                    if (isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key) {
                        $this->content['checkout_page']['default_shipping'] = rtrim($key, ",");
                    }
                    if ($option['unique_carrier']) {
                        foreach ($option['carrier_list'] as $carrier) {
                            $carrier_array[$index]['name'] = $carrier['instance']->name;
                        }
                    } elseif (!$option['unique_carrier']) {
                        $carrier_name = '';
                        foreach ($option['carrier_list'] as $carrier) {
                            $carrier_name .= $carrier['instance']->name . "&";
                        }
                        $carrier_array[$index]['name'] = rtrim($carrier_name, "&");
                    }
                    if ($option['total_price_with_tax']
                        && (isset($option['is_free'])
                        && $option['is_free'] == 0)
                        && !$free_shipping) {
                        $carrier_array[$index]['price'] = $this->formatPrice($option['total_price_with_tax']);
                    } else {
                        $carrier_array[$index]['price'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Free'),
                            'AppCheckout'
                        );
                    }
                    if ($option['unique_carrier']
                        && isset($carrier['instance']->delay[$this->context->language->id])) {
                        $lang_id = $this->context->language->id;
                        $carrier_array[$index]['delay_text'] = $carrier['instance']->delay[$lang_id];
                    } else {
                        $carrier_array[$index]['delay_text'] = "";
                    }
                    $carrier_array[$index]['code'] = rtrim($key, ",");
                    $index++;
                }
            }
            if (empty($carrier_array)) {
                $this->content['checkout_page']['shipping_available'] = "0";
                $this->content['checkout_page']['shipping_message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('No Delivery Method Available'),
                    'AppCheckout'
                );
                $this->content['checkout_page']['shipping_methods'] = array();
            } else {
                $this->content['checkout_page']['shipping_available'] = "1";
                $this->content['checkout_page']['shipping_message'] = "";
                $this->content['checkout_page']['shipping_methods'] = $carrier_array;
            }
        }
    }

    
   
    /**
     * Set the selected billing address to cart context and set the address details in billing_address paramter
     *
     * @param int $id_billing billing addres id
     * @return bool
     */
    public function getBillingAddress($id_billing)
    {
        $address = new Address($id_billing);
        if (!validate::isLoadedObject($address)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Unable to get Billing address details'),
                'AppCheckout'
            );
            $this->writeLog('Address object is not valid');
            return false;
        } else {
            $this->context->cart->id_address_invoice = (int) $id_billing;
            $this->context->cart->autosetProductAddress();
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            if (!$this->context->cart->update()) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('An error occurred while updating your cart.'),
                    'AppCheckout'
                );
                $this->writeLog('An error occurred while updating your cart.');
                return false;
            }

            $billing_address = array();
            $billing_address['id_shipping_address'] = $address->id;
            $billing_address['firstname'] = $address->firstname;
            $billing_address['lastname'] = $address->lastname;
            $billing_address['mobile_no'] = (!empty($address->phone_mobile)) ?
                $address->phone_mobile . "," . $address->phone : $address->phone . "," . $address->phone_mobile;
            $billing_address['mobile_no'] = rtrim($billing_address['mobile_no'], ',');
            $billing_address['company'] = $address->company;
            $billing_address['address_1'] = $address->address1;
            $billing_address['address_2'] = $address->address2;
            $billing_address['city'] = $address->city;
            if ($address->id_state != 0) {
                $billing_address['state'] = State::getNameById($address->id_state);
            } else {
                $billing_address['state'] = "";
            }
            $billing_address['country'] = Country::getNameById(
                $this->context->language->id,
                $address->id_country
            );
            $billing_address['postcode'] = $address->postcode;
            $billing_address['alias'] = $address->alias;
            $this->content['checkout_page']['billing_address'] = $billing_address;
            return true;
        }
    }
}

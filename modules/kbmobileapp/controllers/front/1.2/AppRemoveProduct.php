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
 * API to remove product from the cart
 */

require_once 'AppCore.php';

class AppRemoveProduct extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        if (!(int) Tools::getValue('session_data', 0)) {
            $this->content['status'] = 'status';
            $this->content['message'] = '';
            $this->content['products'] = array();
            $this->writeLog('Cart id is missing');
        } else {
            $this->context->cart = new Cart(
                (int) Tools::getValue('session_data', 0),
                false,
                null,
                null,
                $this->context
            );
            $this->getCartData();
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Validate Cart and get its data
     */
    public function getCartData()
    {
        if (!Validate::isLoadedObject($this->context->cart)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Cart not found'),
                'AppRemoveProduct'
            );
            $this->writeLog('Unable to load cart.');
        } else {
            $this->content['checkout_page']['per_products_shipping'] = "0";
            $this->context->cart->autosetProductAddress();
            $this->context->cart->update();
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cookie->write();
            if (isset($this->context->customer->id)
                && $this->context->customer->id
                && isset($this->context->cart->id_customer)
                && $this->context->cart->id_customer) {
                if ($this->context->cart->id_customer != $this->context->customer->id) {
                    $customer = new Customer($this->context->cart->id_customer);
                    $this->context->customer = $customer;
                }
            }
            $this->context->cart->id_customer = (int) $this->context->customer->id;
            $this->context->cart->secure_key = $this->context->customer->secure_key;
            $this->context->cart->update();
            CartRule::autoAddToCart();
            $this->processDeleteProductInCart();
            $this->context->cart->update();
            if ($this->context->cart->isVirtualCart()) {
                $this->context->cart->gift = 0;
                $this->context->cart->update();
            }
            $cart_data = $this->fetchList();
            $cart_summary = $cart_data['summary'];
            $customized_data = $cart_data['customized_data'];
            $cart_products = array();
            $index = 0;
            foreach ($cart_summary['products'] as $product) {
                $quantity_displayed = 0;
                $extra_product_line = false;
                $customization = false;
                $product_obj = new Product(
                    (int) $product['id_product'],
                    true,
                    $this->context->language->id,
                    $this->context->shop->id
                );
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
                if (!$cart_products[$index]['stock']) {
                    if ((int) $product_obj->out_of_stock == 1) {
                        $cart_products[$index]['stock'] = true;
                    } elseif ((int) $product_obj->out_of_stock == 2
                        && (int) Configuration::get('PS_ORDER_OUT_OF_STOCK') == 1) {
                        $cart_products[$index]['stock'] = true;
                    }
                }
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
                    $ci = 0;
                    $customization = true;
                    foreach ($customized_data[$p_id][$p_aid][$da_id] as $id_customization => $customization) {
                        foreach ($customization['datas'] as $type => $custom_data) {
                            if ($type == Product::CUSTOMIZE_TEXTFIELD) {
                                foreach ($custom_data as $tf) {
                                    $cart_products[$index]['customizable_items'][$ci] = array(
                                        'id_customization_field' => $id_customization,
                                        'type' => "text",
                                        'text_value' => $tf['value'],
                                        'quantity' => $customization['quantity']
                                    );
                                    $t_i = 0;
                                    if ($tf['name']) {
                                        $cart_products[$index]['customizable_items'][$ci]['title'] = $tf['name'];
                                    } else {
                                        $cart_products[$index]['customizable_items'][$ci]['title'] = "Text #".($t_i+1);
                                        $t_i++;
                                    }
                                    $ci++;
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
                    $cart_products[$index]['price'] = $this->formatPrice($product['total_customization_wt']);
                    $cart_products[$index]['quantity'] = $product['customizationQuantityTotal'];
                    $cart_products[$index]['product_items'] = array(
                        array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('Quantity'),
                                'AppRemoveProduct'
                            ),
                            'value' => $product['customizationQuantityTotal']
                        ),
                        array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('SKU'),
                                'AppRemoveProduct'
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
                                'AppRemoveProduct'
                            ),
                            'value' => $product['cart_quantity']
                        ),
                        array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('SKU'),
                                'AppRemoveProduct'
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
                            'AppRemoveProduct'
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
                    foreach ($temp_customized_data as $key => $data) {
                        if (isset($temp_array[$data['id_customization_field']])) {
                            $t_arr = $temp_array[$data['id_customization_field']];
                            $cart_products[$index]['customizable_items'][$t_arr]['customizable_grp_items'][] = $data;
                        } else {
                            $temp_array[$data['id_customization_field']] = $cust_index;
                            $t_txt = 'customizable_grp_items';
                            $cart_products[$index]['customizable_items'][$cust_index][$t_txt][] = $data;
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
                                'AppRemoveProduct'
                            ),
                            'value' => ($product['cart_quantity'] - $quantity_displayed)
                        ),
                        array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('SKU'),
                                'AppRemoveProduct'
                            ),
                            'value' => $product['reference']
                        )
                    );
                    if (isset($product['attributes']) && $product['attributes']) {
                        $cart_products[$index]['product_items'][] = array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('Attributes'),
                                'AppRemoveProduct'
                            ),
                            'value' => $product['attributes']
                        );
                    }
                    $cart_products[$index]['quantity'] = $product['cart_quantity'] - $quantity_displayed;
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
                                'AppRemoveProduct'
                            ),
                            'value' => $product['cart_quantity']
                        ),
                        array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('SKU'),
                                'AppRemoveProduct'
                            ),
                            'value' => $product['reference']
                        )
                    );
                    if (isset($product['attributes']) && $product['attributes']) {
                        $cart_products[$index]['product_items'][] = array(
                            'name' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('Attributes'),
                                'AppRemoveProduct'
                            ),
                            'value' => $product['attributes']
                        );
                    }
                    $cart_products[$index]['quantity'] = $product['cart_quantity'];
                    $cart_products[$index]['customizable_items'] = array();
                    $index++;
                }
            }

            $this->content['products'] = $cart_products;

            $cart_total_details = array();
            $cart_total_details[] = array(
                'name' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Total Products(tax excl.)'),
                    'AppRemoveProduct'
                ),
                'value' => $this->formatPrice($cart_summary['total_products'])
            );
            if ($cart_summary['total_discounts'] > 0) {
                $cart_total_details[] = array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Total vouchers'),
                        'AppRemoveProduct'
                    ),
                    'value' => "-" .$this->formatPrice($cart_summary['total_discounts'])
                );
            }
            if ($cart_summary['total_wrapping'] > 0 && $this->context->cart->gift) {
                $cart_total_details[] = array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Total gift wrapping'),
                        'AppRemoveProduct'
                    ),
                    'value' => $this->formatPrice($cart_summary['total_wrapping'])
                );
            }
            if ($cart_summary['total_shipping'] > 0) {
                $cart_total_details[] = array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Total shipping'),
                        'AppRemoveProduct'
                    ),
                    'value' => $this->formatPrice($cart_summary['total_shipping'])
                );
            } else {
                if (!$cart_summary['is_virtual_cart']) {
                    $cart_total_details[] = array(
                        'name' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Total shipping'),
                            'AppRemoveProduct'
                        ),
                        'value' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Free shipping!'),
                            'AppRemoveProduct'
                        )
                    );
                }
            }
            if ($cart_summary['total_tax'] > 0) {
                $cart_total_details[] = array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Total Tax'),
                        'AppRemoveProduct'
                    ),
                    'value' => $this->formatPrice($cart_summary['total_tax'])
                );
            }
            $cart_total_details[] = array(
                'name' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Total Price'),
                    'AppRemoveProduct'
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
                $this->content['vouchers'] = $voucher_data;
            } else {
                $this->content['vouchers'] = array();
            }

            $a_txt = '(Additional Cost of ';
            if (!$cart_summary['is_virtual_cart']) {
                $gift_wrapping_cost = Tools::convertPriceFull(
                    $this->context->cart->getGiftWrappingPrice(),
                    null,
                    $this->context->currency
                );
                $this->content['gift_wrapping'] = array(
                    'available' => Configuration::get('PS_GIFT_WRAPPING'),
                    'applied' => ($this->context->cart->gift) ? "1" : "0",
                    'message' => ($this->context->cart->gift_message) ? $this->context->cart->gift_message : "",
                    'cost_text' => ($this->context->cart->getGiftWrappingPrice() > 0) ?
                        $a_txt.$this->formatPrice($gift_wrapping_cost) . " )" : ""
                );
            } else {
                $gift_wrapping_cost = Tools::convertPriceFull(
                    $this->context->cart->getGiftWrappingPrice(),
                    null,
                    $this->context->currency
                );
                $this->content['gift_wrapping'] = array(
                    'available' => 0,
                    'applied' => ($this->context->cart->gift) ? "1" : "0",
                    'message' => ($this->context->cart->gift_message) ? $this->context->cart->gift_message : "",
                    'cost_text' => ($this->context->cart->getGiftWrappingPrice() > 0) ?
                        $a_txt. $this->formatPrice($gift_wrapping_cost) . " )" : ""
                );
            }
            $this->content['guest_checkout_enabled'] = Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
            $this->content['cart']['total_cart_items'] = Cart::getNbProducts($this->context->cart->id);
            if (CartRule::isFeatureActive()) {
                $this->content['voucher_allowed'] = "1";
            } else {
                $this->content['voucher_allowed'] = "0";
            }


            $currency = Currency::getCurrency((int) $this->context->cart->id_currency);
            $minimal_purchase = Tools::convertPrice((float) Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
            if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase) {
                    $this->content['minimum_purchase_message'] = sprintf(
                        parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('A minimum purchase total of %1s is required to validate your order, current total is %2s '),
                            'AppRemoveProduct'
                        ),
                        Tools::displayPrice($minimal_purchase, $currency),
                        Tools::displayPrice(
                            $this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS),
                            $currency
                        )
                    );
            } else {
                $this->content['minimum_purchase_message'] = "";
            }

            $this->content['totals'] = $cart_total_details;
            // Get available cart rules and unset the cart rules already in the cart
            $available_cart_rules = CartRule::getCustomerCartRules(
                $this->context->language->id,
                (isset($this->context->customer->id) ? $this->context->customer->id : 0),
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
            if ($available_cart_rules) {
                $voucher_text = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Take advantage of our exclusive offers'),
                    'AppRemoveProduct'
                );
                $template_dir =  $this->getFrontTemplateDir();
                $this->context->smarty->assign('voucher_text', $voucher_text);
                $this->context->smarty->assign('available_cart_rules', $available_cart_rules);
                $vocher_html = $this->context->smarty->fetch($template_dir.'voucher_html.tpl');
                $this->content['voucher_html'] = $vocher_html;
            } else {
                $this->content['voucher_html'] = '';
            }
            $show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true)
                && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));
            if ($show_option_allow_separate_package) {
                $this->content['delay_shipping'] = array(
                    'available' => '1',
                    'applied' => $this->context->cart->allow_seperated_package
                );
            } else {
                $this->content['delay_shipping'] = array(
                    'available' => '0',
                    'applied' => $this->context->cart->allow_seperated_package
                );
            }
            $this->content['cart_id'] = $this->context->cart->id;
        }
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

        // override customization tax rate with real tax (tax rules)
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

        // Get available cart rules and unset the cart rules already in the cart
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
        unset($summary['carrier']);

        return array('summary' => $summary, 'customized_data' => $customizedDatas);
    }


    /**
     * This process delete a product from the cart
     */
    public function processDeleteProductInCart()
    {
        $product_data = Tools::getValue('cart_products', Tools::jsonEncode(array()));
        $product_data = Tools::jsonDecode($product_data);
        if (empty($product_data)) {
            $this->content['status'] = 'Failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Product data is missing.'),
                'AppRemoveProduct'
            );
            $this->writeLog('Product data is missing.');
        } else {
            if (!isset($product_data->cart_products[0]->product_id)) {
                $this->content['status'] = 'Failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Product Id is missing.'),
                    'AppRemoveProduct'
                );
                $this->writeLog('Product Id is missing.');
                $id_product = 0;
            } else {
                $id_product = $product_data->cart_products[0]->product_id;
            }
            if (empty($id_product)) {
                $id_product = 0;
            }

            $id_product_attribute = $product_data->cart_products[0]->id_product_attribute;
            $id_customization = isset($product_data->cart_products[0]->id_customization_field) ? $product_data->cart_products[0]->id_customization_field : '';
            $id_address_delivery = $this->context->cart->id_address_delivery;
            if (empty($id_customization)) {
                $id_customization = null;
            }
            $product = new Product((int) $id_product);
            if (!Validate::isLoadedObject($product)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Product not found'),
                    'AppRemoveProduct'
                );
                $this->writeLog('Product with the provided data is not found.');
            } else {
                $customization_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                    'SELECT * FROM `' . _DB_PREFIX_ . 'customization`
                    WHERE `id_cart` = ' . (int) $this->context->cart->id.
                    ' AND `id_product` = ' . (int) $id_product .
                    ' AND `id_customization` != ' . (int) $id_customization
                );

                if ($customization_product && count($customization_product) > 0) {
                    $product = new Product((int) $id_product);
                    if ($id_product_attribute > 0) {
                        $minimal_quantity = (int) Attribute::getAttributeMinimalQty($id_product_attribute);
                    } else {
                        $minimal_quantity = (int) $product->minimal_quantity;
                    }

                    $total_quantity = 0;
                    foreach ($customization_product as $custom) {
                        $total_quantity += $custom['quantity'];
                    }

                    if ($total_quantity < $minimal_quantity) {
                        $this->content['status'] = 'failure';
                        $this->content['message'] = sprintf(
                            parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('You must add %d minimum quantity'),
                                'AppRemoveProduct'
                            ),
                            $minimal_quantity
                        );
                        $this->writeLog('Mimum quantity required in case of customization product');
                        return;
                    }
                }
                if ($this->context->cart->deleteProduct(
                    $id_product,
                    $id_product_attribute,
                    $id_customization,
                    $id_address_delivery
                )) {
                    Hook::exec('actionAfterDeleteProductInCart', array(
                        'id_cart' => (int) $this->context->cart->id,
                        'id_product' => (int) $id_product,
                        'id_product_attribute' => (int) $id_product_attribute,
                        'customization_id' => (int) $id_customization,
                        'id_address_delivery' => $id_address_delivery
                    ));

                    if (!Cart::getNbProducts((int) $this->context->cart->id)) {
                        $this->context->cart->setDeliveryOption(null);
                        $this->context->cart->gift = 0;
                        $this->context->cart->gift_message = '';
                        $this->context->cart->update();
                    }
                    $this->content['status'] = 'success';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Product Successfully deleted'),
                        'AppRemoveProduct'
                    );
                    $this->writeLog('Product Successfully deleted');
                } else {
                    $this->content['status'] = 'success';
                    $this->content['message'] = '';
                    $this->writeLog('There may exist product in cart with similar attribute');
                }
                CartRule::autoRemoveFromCart();
                CartRule::autoAddToCart();
            }
        }
    }
}

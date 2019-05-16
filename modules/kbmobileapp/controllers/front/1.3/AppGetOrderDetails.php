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
 * API to get the details of order
 */

require_once 'AppCore.php';

class AppGetOrderDetails extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $order_id = Tools::getValue('order_id', 0);
        if ($order_id == 0 || $order_id == "") {
            $this->content['status'] = "failure";
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Order id is missing'),
                'AppGetOrderDetails'
            );
            $this->writeLog("Order id is missing");
        } else {
            $myOrder = new Order((int) $order_id);
            if (Validate::isLoadedObject($myOrder)) {
                $this->content['status'] = "success";
                $this->content['message'] = "";
                $details = $this->makeOrdersResponseArray($myOrder);
                $this->content['order_details'] = $details;
            } else {
                $this->content['status'] = "failure";
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Unable to load order details'),
                    'AppGetOrderDetails'
                );
                $this->writeLog("Unable to load order object");
            }
        }

        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Get details of order
     *
     * @param Object Order $order order object
     * @return array order data
     */
    private function makeOrdersResponseArray($order)
    {
        $order_tmp = array();

        if (Address::addressExists($order->id_address_delivery)) {
            $shipping_address = new Address($order->id_address_delivery);
        } else {
            $shipping_address = false;
        }
        
        if (Address::addressExists($order->id_address_invoice)) {
            $billing_address = new Address($order->id_address_invoice);
        } else {
            $billing_address = false;
        }

        /* Orders Details */
        $order_currency = new Currency($order->id_currency, null, $this->context->shop->id);
        $order_state = new OrderState($order->current_state, $this->context->language->id);
        $order_tmp['order_history']['order_id'] = $order->id;
        $order_tmp['order_history']['cart_id'] = $order->id_cart;
        $order_tmp['order_history']['order_number'] = Order::getUniqReferenceOf($order->id);
        $order_tmp['order_history']['status'] = $order_state->name;
        $order_tmp['order_history']['status_color'] = $order_state->color;
        $order_tmp['order_history']['date_added'] = $order->date_add;
        $order_tmp['order_history']['total'] = $this->formatOrderPrice($order->total_paid, $order_currency);
        if (!Configuration::get('PS_DISALLOW_HISTORY_REORDERING')) {
            $order_tmp['order_history']['reorder_allowed'] = "1";
        } else {
            $order_tmp['order_history']['reorder_allowed'] = "0";
        }

        /* Shipping Details */
        $carrier = new Carrier($order->id_carrier);
        $order_tmp['shipping_address']['firstname'] = ($shipping_address) ? $shipping_address->firstname : '';
        $order_tmp['shipping_address']['lastname'] = ($shipping_address) ? $shipping_address->lastname : '';
        $order_tmp['shipping_address']['company'] = ($shipping_address) ? $shipping_address->company : '';
        $order_tmp['shipping_address']['address_1'] = ($shipping_address) ? $shipping_address->address1 : '';
        $order_tmp['shipping_address']['address_2'] = ($shipping_address) ? $shipping_address->address2 : '';
        if ($shipping_address) {
            $phone_1 = $shipping_address->phone_mobile . "," . $shipping_address->phone;
            $phone_2 = $shipping_address->phone . "," . $shipping_address->phone_mobile;
            $order_tmp['shipping_address']['mobile_no'] = (!empty($shipping_address->phone_mobile)) ?
                    $phone_1 : $phone_2;
            $order_tmp['shipping_address']['mobile_no'] = rtrim($order_tmp['shipping_address']['mobile_no'], ',');
        } else {
            $order_tmp['shipping_address']['mobile_no'] = "";
        }
        $order_tmp['shipping_address']['city'] = ($shipping_address) ? $shipping_address->city : '';
        $order_tmp['shipping_address']['postcode'] = ($shipping_address) ? $shipping_address->postcode : '';
        if ($shipping_address && $shipping_address->id_state != 0) {
            $order_tmp['shipping_address']['state'] = State::getNameById($shipping_address->id_state);
        } else {
            $order_tmp['shipping_address']['state'] = "";
        }
        $c_name = Country::getNameById($this->context->language->id, $shipping_address->id_country);
        $order_tmp['shipping_address']['country'] = ($shipping_address) ? $c_name : '';
        $order_tmp['shipping_address']['alias'] = ($shipping_address) ? $shipping_address->alias : '';
        $order_tmp['shipping_method']['name'] = $carrier->name;
        $order_tmp['payment_method']['name'] = $order->payment;

        
        /* Billing Details */
        $order_tmp['billing_address']['firstname'] = ($billing_address) ? $billing_address->firstname : '';
        $order_tmp['billing_address']['lastname'] = ($billing_address) ? $billing_address->lastname : '';
        $order_tmp['billing_address']['company'] = ($billing_address) ? $billing_address->company : '';
        $order_tmp['billing_address']['address_1'] = ($billing_address) ? $billing_address->address1 : '';
        $order_tmp['billing_address']['address_2'] = ($billing_address) ? $billing_address->address2 : '';
        if ($billing_address) {
            $phone_1 = $billing_address->phone_mobile . "," . $billing_address->phone;
            $phone_2 = $billing_address->phone . "," . $billing_address->phone_mobile;
            $order_tmp['billing_address']['mobile_no'] = (!empty($billing_address->phone_mobile)) ?
                    $phone_1 : $phone_2;
            $order_tmp['billing_address']['mobile_no'] = rtrim($order_tmp['billing_address']['mobile_no'], ',');
        } else {
            $order_tmp['billing_address']['mobile_no'] = "";
        }
        $order_tmp['billing_address']['city'] = ($billing_address) ? $billing_address->city : '';
        $order_tmp['billing_address']['postcode'] = ($billing_address) ? $billing_address->postcode : '';
        if ($billing_address && $billing_address->id_state != 0) {
            $order_tmp['billing_address']['state'] = State::getNameById($billing_address->id_state);
        } else {
            $order_tmp['billing_address']['state'] = "";
        }
        $c_name = Country::getNameById($this->context->language->id, $billing_address->id_country);
        $order_tmp['billing_address']['country'] = ($billing_address) ? $c_name : '';
        $order_tmp['billing_address']['alias'] = ($billing_address) ? $billing_address->alias : '';
        
        
        /* Product Details */
        $products = $order->getProducts();
        $customized_data = Product::getAllCustomizedDatas((int) $order->id_cart);
        Product::addCustomizationPrice($products, $customized_data);

        $p = 0;
        foreach ($products as $result) {
            $order_tmp['products'][$p]['id'] = $result["product_id"];
            $order_tmp['products'][$p]['title'] = str_replace("&", "and", $result["product_name"]);
            $order_tmp['products'][$p]['is_gift_product'] = "0";
            $order_tmp['products'][$p]['stock'] = true;
            $order_tmp['products'][$p]['id_product_attribute'] = $result['product_attribute_id'];
            $order_tmp['products'][$p]['quantity'] = $result["product_quantity"];
            $order_tmp['products'][$p]['price'] = $this->formatOrderPrice($result["unit_price_tax_incl"], $order_currency);
            $order_tmp['products'][$p]['discount_price'] = "";
            $order_tmp['products'][$p]['discount_percentage'] = "";
            $order_tmp['products'][$p]['total'] = $this->formatOrderPrice($result["total_price_tax_incl"], $order_currency);
            $order_tmp['products'][$p]['product_items'] = array(
                array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Quantity'),
                        'AppGetOrderDetails'
                    ),
                    'value' => $result["product_quantity"]
                ),
                array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('SKU'),
                        'AppGetOrderDetails'
                    ),
                    'value' => $result["reference"]
                )
            );
            $order_tmp['products'][$p]['images'] = $this->getProductImage(
                $result["product_id"],
                $result["image"]
            );

            if (isset($customized_data[$result['id_product']][$result['product_attribute_id']])) {
                $k = 0;
                $attr = $result['product_attribute_id'];
                $c = $customized_data[$result['id_product']][$attr][$result['id_address_delivery']];
                foreach ($c as $id_customization => $customization) {
                    foreach ($customization['datas'] as $type => $custom_data) {
                        if ($type == Product::CUSTOMIZE_TEXTFIELD) {
                            $tc = 0;
                            foreach ($custom_data as $textField) {
                                $order_tmp['products'][$p]['customizable_items'][$k] = array(
                                    'id_customization_field' => $id_customization,
                                    'type' => "text",
                                    'text_value' => $textField['value'],
                                    'quantity' => $customization['quantity']
                                );
                                if ($textField['name']) {
                                    $order_tmp['products'][$p]['customizable_items'][$k]['title'] = $textField['name'];
                                } else {
                                    $text = parent::getTranslatedTextByFileAndISO(
                                        Tools::getValue('iso_code', false),
                                        $this->l('Text'),
                                        'AppGetOrderDetails'
                                    );
                                    $order_tmp['products'][$p]['customizable_items'][$k]['title'] = $text." s#".($tc + 1);
                                    $tc++;
                                }
                                $k++;
                            }
                        }
                    }
                }
            }
            if (!isset($order_tmp['products'][$p]['customizable_items'])) {
                $order_tmp['products'][$p]['customizable_items'] = array();
            } else {
                $temp_array = array();
                $temp_customized_data = $order_tmp['products'][$p]['customizable_items'];
                unset($order_tmp['products'][$p]['customizable_items']);
                $c_i = 0;
                foreach ($temp_customized_data as $data) {
                    if (isset($temp_array[$data['id_customization_field']])) {
                        $c_d = $temp_array[$data['id_customization_field']];
                        $order_tmp['products'][$p]['customizable_items'][$c_d]['customizable_grp_items'][] = $data;
                    } else {
                        $temp_array[$data['id_customization_field']] = $c_i;
                        $order_tmp['products'][$p]['customizable_items'][$c_i]['customizable_grp_items'][] = $data;
                        $c_i++;
                    }
                }
                unset($temp_array);
                unset($temp_customized_data);
            }
            $p++;
        }

        $histories = $order->getHistory($this->context->language->id);
        $order_tmp['status_history'] = array();
        $history_index = 0;
        foreach ($histories as $his) {
            $order_tmp['status_history'][$history_index]['id'] = $his["id_order_history"];
            $order_tmp['status_history'][$history_index]['order_status'] = $his["ostate_name"];
            $order_tmp['status_history'][$history_index]['notify'] = $his["send_email"];
            $order_tmp['status_history'][$history_index]['comment'] = '';
            $order_tmp['status_history'][$history_index]['history_date'] = $his["date_add"];
            $order_tmp['status_history'][$history_index]['status_color'] = $his["color"];
            $history_index++;
        }

        /* Order Total */
        $cart_total_details = array();
        $cart_total_details[] = array(
            'name' => parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Total Products'),
                'AppGetOrderDetails'
            ),
            'value' => $this->formatOrderPrice($order->total_products_wt, $order_currency)
        );
        if ($order->total_discounts > 0) {
            $cart_total_details[] = array(
                'name' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Total vouchers'),
                    'AppGetOrderDetails'
                ),
                'value' => "-" .$this->formatOrderPrice($order->total_discounts, $order_currency)
            );
        }
        if ($order->total_wrapping > 0 && $order->gift) {
            $cart_total_details[] = array(
                'name' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Total gift wrapping'),
                    'AppGetOrderDetails'
                ),
                'value' => $this->formatOrderPrice($order->total_wrapping, $order_currency)
            );
        }

        $cart_total_details[] = array(
            'name' => parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Total shipping'),
                'AppGetOrderDetails'
            ),
            'value' => $this->formatOrderPrice($order->total_shipping, $order_currency)
        );

        $cart_total_details[] = array(
            'name' => parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Total Price'),
                'AppGetOrderDetails'
            ),
            'value' => $this->formatOrderPrice($order->total_paid, $order_currency)
        );
        $order_tmp['total'] = $cart_total_details;

        /* Get Cart rules */

        $cart_rules = $order->getCartRules();
        if (!empty($cart_rules)) {
            $index = 0;
            $voucher_data = array();
            foreach ($cart_rules as $voucher) {
                if ((float) $voucher['value'] == 0) {
                    continue;
                }
                $voucher_data[$index] = array(
                    'id' => $voucher['id_order_cart_rule'],
                    'name' => $voucher['name'],
                    'value' => "-" . $this->formatOrderPrice($voucher['value'], $order_currency),
                );
                $index++;
            }
            $order_tmp['vouchers'] = $voucher_data;
        } else {
            $order_tmp['vouchers'] = array();
        }

        //check if gift productd
        $order_tmp['gift_wrapping'] = array(
            'available' => Configuration::get('PS_GIFT_WRAPPING'),
            'applied' => ($order->gift) ? "1" : "0",
            'message' => $order->gift_message,
            'cost_text' => ""
        );

        //Get first message
        $order_comment = $order->getFirstMessage();
        if ($order_comment) {
            $order_tmp['order_comment'] = $order_comment;
        } else {
            $order_tmp['order_comment'] = "";
        }



        return $order_tmp;
    }

    /**
     * Get link of products image
     *
     * @param int $product_id product id
     * @param Object Image $order_product_image image object
     * @return string product image link
     */
    private function getProductImage($product_id, $order_product_image)
    {
        $product = new Product($product_id, false, $this->context->language->id);
        return $this->context->link->getImageLink(
            $product->link_rewrite,
            $order_product_image->id,
            $this->getImageType('large')
        );
    }
}

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
 * API to get personal info and orders of customer
 */

require_once 'AppCore.php';

class AppGetOrders extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        if ($this->validateCustomer()) {
            if ($orders = Order::getCustomerOrders($this->context->customer->id)) {
                $orders_detail = array();
                $index = 0;
                $error = false;
                foreach ($orders as &$order) {
                    $myOrder = new Order((int) $order['id_order']);
                    if (Validate::isLoadedObject($myOrder)) {
                        $order['virtual'] = $myOrder->isVirtual(false);
                        $orders_detail[$index] = $this->makeOrdersResponseArray($myOrder);
                        $index++;
                    } else {
                        $error = true;
                    }
                }
                if ($error) {
                    $this->content['status'] = "failure";
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Unable to load order history'),
                        'AppGetOrders'
                    );
                    $this->writeLog("Unable to load order object");
                } else {
                    $this->content['status'] = "success";
                    $this->content['message'] = "";
                    $this->content['order_history'] = $orders_detail;
                }
            } else {
                $this->content['status'] = "success";
                $this->content['message'] = "";
                $this->content['order_history'] = array();
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
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
                'AppGetOrders'
            );
            $this->writeLog('Email address is not valid');
            return false;
        } else {
            if (Customer::customerExists(strip_tags($email))) {
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email);

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
                $customer_data = array();
                $genders = Gender::getGenders();
                $gender_index = 0;
                foreach ($genders as $gender) {
                    $customer_data['titles'][$gender_index] = array(
                        'id' => $gender->id,
                        'name' => 'gender',
                        'label' => $gender->name
                    );
                    $gender_index++;
                }
                $customer_data['firstname'] = $customer->firstname;
                $customer_data['lastname'] = $customer->lastname;
                $customer_data['email'] = $customer->email;
                $customer_data['dob'] = $customer->birthday;
                $customer_data['gender'] = $customer->id_gender;
                $this->content['personal_info'] = $customer_data;
                return true;
            } else {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer with this email not exist'),
                    'AppGetOrders'
                );
                $this->writeLog('Customer with this email not exist');
                return false;
            }
        }
    }

    /**
     * Get list of orders
     *
     * @param Object Order $order order object
     * @return array order data
     */
    private function makeOrdersResponseArray($order)
    {
        $order_tmp = array();

        $order_currency = new Currency($order->id_currency, null, $this->context->shop->id);
        $order_state = new OrderState($order->current_state, $this->context->language->id);
        $order_tmp['order_id'] = $order->id;
        $order_tmp['cart_id'] = $order->id_cart;
        $order_tmp['order_number'] = Order::getUniqReferenceOf($order->id);
        $order_tmp['status'] = $order_state->name;
        $order_tmp['status_color'] = $order_state->color;
        $order_tmp['date_added'] = $order->date_add;
        $order_tmp['total'] = $this->formatOrderPrice($order->total_paid, $order_currency);
        if (!Configuration::get('PS_DISALLOW_HISTORY_REORDERING')) {
            $order_tmp['reorder_allowed'] = "1";
        } else {
            $order_tmp['reorder_allowed'] = "0";
        }


        /*Product Details */
        $products = $order->getProducts();

        $product_index = 0;
        foreach ($products as $result) {
            $order_tmp['products'][$product_index]['id'] = $result["product_id"];
            $order_tmp['products'][$product_index]['title'] = str_replace("&", "and", $result["product_name"]);
            $order_tmp['products'][$product_index]['is_gift_product'] = "0";
            $order_tmp['products'][$product_index]['stock'] = true;
            $order_tmp['products'][$product_index]['id_product_attribute'] = $result['product_attribute_id'];
            $order_tmp['products'][$product_index]['quantity'] = $result["product_quantity"];
            $order_tmp['products'][$product_index]['price'] = $this->formatOrderPrice($result["unit_price_tax_incl"], $order_currency);
            $order_tmp['products'][$product_index]['discount_price'] = "";
            $order_tmp['products'][$product_index]['discount_percentage'] = "";
            $order_tmp['products'][$product_index]['total'] = $this->formatOrderPrice($result["total_price_tax_incl"], $order_currency);
            $order_tmp['products'][$product_index]['product_items'] = array(
                array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Quantity'),
                        'AppGetOrders'
                    ),
                    'value' => $result["product_quantity"]
                ),
                array(
                    'name' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('SKU'),
                        'AppGetOrders'
                    ),
                    'value' => $result["reference"]
                )
            );
            $order_tmp['products'][$product_index]['images'] = $this->getProductImage(
                $result["product_id"],
                $result["image"]
            );
            $order_tmp['products'][$product_index]['customizable_items'] = array();
            $product_index++;
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
        /* Changes started by rishabh jain on 3rd sep 2018
         * Added urlencode perimeter in image link if enabled by admin
         */
        /* changes for empty images*/
        if (empty($order_product_image)) {
            $iso_code = Language::getIsoById((int) $this->context->language->id);
            if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                return $this->context->link->getImageLink(
                    urlencode($product->link_rewrite),
                    $iso_code.'-default',
                    $this->getImageType('large')
                );
            } else {
                return $this->context->link->getImageLink(
                    $product->link_rewrite,
                    $iso_code.'-default',
                    $this->getImageType('large')
                );
            }
        }
        if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
            return $this->context->link->getImageLink(
                urlencode($product->link_rewrite),
                $order_product_image->id,
                $this->getImageType('large')
            );
        } else {
            return $this->context->link->getImageLink(
                $product->link_rewrite,
                $order_product_image->id,
                $this->getImageType('large')
            );
        }
        /* Changes over */
    }
}

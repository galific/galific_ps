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
 * API to check whether order is created from cart or not when he came back to the APP from web view
 */

require_once 'AppCore.php';

class AppCheckOrderStatus extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $cart_id = Tools::getValue('session_data', 0);
        $cart = new Cart($cart_id);
        if ($this->validateCustomer()) {
            if (!Validate::isLoadedObject($cart)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Unable to load cart'),
                    'AppCheckOrderStatus'
                );
                $this->writeLog('Unable to load cart');
            } else {
                $order_id = Order::getOrderByCartId($cart->id);
                if ($order_id) {
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart = new Cart();
                    $this->context->cart->id_carrier = 0;
                    $this->context->cart->setDeliveryOption(null);
                    $d_id = (int) Address::getFirstCustomerAddressId((int) ($this->context->customer->id));
                    $this->context->cart->id_address_delivery = $d_id;
                    $i_id = (int) Address::getFirstCustomerAddressId((int) ($this->context->customer->id));
                    $this->context->cart->id_address_invoice = $i_id;
                    $this->context->cart->id_customer = (int) $this->context->customer->id;
                    $this->context->cart->secure_key = $this->context->customer->secure_key;
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart->save();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    $this->context->cookie->write();
                    $this->context->cart->autosetProductAddress();
                    $this->content['status'] = 'success';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Order created by this cart'),
                        'AppCheckOrderStatus'
                    );
                    $this->content['cart_id'] = $this->context->cart->id;
                    $this->writeLog('Order created by this cart');
                } else {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = '';
                }
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Validate customer i.e email is valid or not or customer with provided email address is exist or not
     *
     * @retun bool
     */
    public function validateCustomer()
    {
        $email = Tools::getValue('email', '');
        if (!Validate::isEmail($email)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is not valid'),
                'AppCheckOrderStatus'
            );
            $this->writeLog('Email address is not valid');
            return false;
        } else {
            if (Customer::customerExists(strip_tags($email), true, false)) {
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
                    'AppCheckOrderStatus'
                );
                $this->writeLog('Customer with this email not exist');
                return false;
            }
        }
    }
}

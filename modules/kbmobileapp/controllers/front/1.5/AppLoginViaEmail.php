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
 * API to login customer from social options Google and Facebook
 */

require_once 'AppCore.php';

class AppLoginViaEmail extends AppCore
{

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        if (!Tools::getIsset('email_id') || !Tools::getIsset('unique_id')) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Email Id or Unique id is not available.'),
                    'AppLoginViaEmail'
                )
            );
            $this->writeLog('Email Id or Unique id is not available.');
        } else {
            $user_data = array();
            $user_data['email'] = Tools::getValue('email_id');
            $user_data['unique_fingerprint_id'] = Tools::getValue('unique_id');
            $cart_id = Tools::getValue('session_data', '');
            if (!empty($cart_id)) {
                $this->context->cart->id_currency = $this->context->currency->id;
                $this->context->cart = new Cart($cart_id);
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
                $this->context->cookie->write();
            }
            if ($user_data['email'] != '' && $user_data['unique_fingerprint_id'] != '') {
                $this->addUser($user_data);
            } else {
                $this->content['login_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Empty Email or Unique Id.'),
                        'AppLoginViaEmail'
                    )
                );
                $this->writeLog('Empty Email or Unique Id.');
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Add the social user to store if it is not registered
     *
     * @param array $user_data user information
     */
    public function addUser($user_data)
    {
        if (!Customer::customerExists(strip_tags($user_data['email']))) {
            $this->content = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('An account using this email address does not exists.'),
                    'AppMapEmailWithUUID'
                )
            );
            $this->writeLog('Email address does not exist in database.');
        } else {
            $customer_data = array();
            $customer_data = Customer::getCustomersByEmail(strip_tags($user_data['email']));
            $customer_data = $customer_data[0];
            $is_exist_fingerprint = $this->isFingerprintExistLogin($customer_data, $user_data, $this->context->shop->id);
            if ($is_exist_fingerprint) {
                if (isset($customer_data['active']) && !$customer_data['active']) {
                    $this->content['login_user'] = array(
                        'status' => 'failure',
                        'message' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Your account isn\'t available at this time.'),
                            'AppLoginViaEmail'
                        )
                    );
                    $this->writeLog('Account is not active');
                } else {
                    $customer = new Customer();
                    $customer = new Customer($customer_data['id_customer']);
                    //Update Context
                    $this->context->customer = $customer;
                    $this->context->cookie->id_customer = (int) $customer->id;
                    $this->context->cookie->customer_lastname = $customer->lastname;
                    $this->context->cookie->customer_firstname = $customer->firstname;
                    $this->context->cookie->passwd = $customer->passwd;
                    $this->context->cookie->logged = 1;
                    $this->context->cookie->email = $customer->email;
                    $this->context->cookie->is_guest = $customer->is_guest;

                    //Cart
                    if (Configuration::get('PS_CART_FOLLOWING') && (empty($this->context->cookie->id_cart) || Cart::getNbProducts($this->context->cookie->id_cart) == 0) && $id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id)) {
                        $this->context->cart = new Cart($id_cart);
                    } else {
                        $id_carrier = (int) $this->context->cart->id_carrier;
                        if (!$this->context->cart->id_address_delivery) {
                            $this->context->cart->id_carrier = 0;
                            $this->context->cart->setDeliveryOption(null);
                            $this->context->cart->id_address_delivery = (int)
                                Address::getFirstCustomerAddressId((int) $customer->id);
                            $this->context->cart->id_address_invoice = (int)
                                Address::getFirstCustomerAddressId((int) $customer->id);
                        }
                    }
                    $this->context->cart->secure_key = $customer->secure_key;

                    if (isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                        $delivery_option = array(
                            $this->context->cart->id_address_delivery => $id_carrier . ',');
                        $this->context->cart->setDeliveryOption($delivery_option);
                    }
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart->save();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    $this->context->cookie->write();
                    $this->context->cart->autosetProductAddress();
                    $wishlist_count = $this->getWishListCount($customer->id);
                    $this->content['login_user'] = array(
                        'status' => 'success',
                        'message' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('User login successfully'),
                            'AppLoginViaEmail'
                        ),
                        'customer_id' => $customer->id,
                        'wishlist_count' => $wishlist_count,
                        'email' => $customer->email,
                        'session_data' => (int) $this->context->cart->id,
                        'cart_count' => Cart::getNbProducts($this->context->cookie->id_cart)
                    );
                    $this->writeLog('User login successfully');
                }
            } else {
                $this->content['login_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('This Unique Id is not linked with the Email id.'),
                        'AppLoginViaEmail'
                    )
                );
                $this->writeLog('Invalid Unique id');
            }
        }
    }
}

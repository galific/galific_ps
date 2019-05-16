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
 * API to handle login action of customer
 * called from login page in APP
 */

require_once 'AppCore.php';

class AppLogin extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * Get email,password and session_data and logged in them after validation
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        
        $email = Tools::getValue('email', '');
        $password = Tools::getValue('password', '');
        $cart_id = Tools::getValue('session_data', '');
        if (!empty($cart_id)) {
            $this->context->cart->id_currency = $this->context->currency->id;
            $this->context->cart = new Cart($cart_id);
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cookie->write();
        }
        if (empty($email)) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('An email address required.'),
                    'AppLogin'
                )
            );
            $this->writeLog('Email address not provided.');
        } elseif (!Validate::isEmail($email)) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Invalid email address.'),
                    'AppLogin'
                )
            );
            $this->writeLog('Invalid email address.');
        } elseif (empty($password)) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Password is required.'),
                    'AppLogin'
                )
            );
            $this->writeLog('Password is not provided.');
        } elseif (!Validate::isPasswd($password)) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Invalid Password.'),
                    'AppLogin'
                )
            );
            $this->writeLog('Invalid Password.');
        } else {
            $customer = new Customer();
            Hook::exec('actionBeforeAuthentication');
            $authentication = $customer->getByEmail(trim($email), trim($password));
            if (isset($authentication->active) && !$authentication->active) {
                $this->content['login_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Your account isn\'t available at this time.'),
                        'AppLogin'
                    )
                );
                $this->writeLog('Account is not active');
            } elseif (!$authentication || !$customer->id) {
                $this->content['login_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Authentication failed.'),
                        'AppLogin'
                    )
                );
                $this->writeLog('Authentication failed.');
            } else {
                $this->context->cookie->id_customer = (int) ($customer->id);
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->logged = 1;
                $customer->logged = 1;
                $this->context->cookie->is_guest = $customer->isGuest();
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->email = $customer->email;

                // Add customer to the context
                $this->context->customer = $customer;
//
                if (Configuration::get('PS_CART_FOLLOWING') &&
                        (empty($this->context->cookie->id_cart) ||
                        Cart::getNbProducts($this->context->cookie->id_cart) == 0) &&
                        $id_cart = (int) Cart::lastNoneOrderedCart($this->context->customer->id)) {
                    $this->context->cart = new Cart($id_cart);
                } else {
                    $id_carrier = (int) $this->context->cart->id_carrier;
                    if (!$this->context->cart->id_address_delivery) {
                        $this->context->cart->id_carrier = 0;
                        $this->context->cart->setDeliveryOption(null);
                        $d_id = (int) Address::getFirstCustomerAddressId((int) ($customer->id));
                        $this->context->cart->id_address_delivery = $d_id;
                        $i_id = (int) Address::getFirstCustomerAddressId((int) ($customer->id));
                        $this->context->cart->id_address_invoice = $i_id;
                    }
                }
                $this->context->cart->id_customer = (int) $customer->id;
                $this->context->cart->secure_key = $customer->secure_key;

                if (isset($id_carrier) && $id_carrier && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                    $delivery_option = array($this->context->cart->id_address_delivery => $id_carrier . ',');
                    $this->context->cart->setDeliveryOption($delivery_option);
                }

                $this->context->cart->id_currency = $this->context->currency->id;
                $this->context->cart->save();
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
                $this->context->cookie->write();
                $this->context->cart->autosetProductAddress();

                Hook::exec('actionAuthentication', array('customer' => $this->context->customer));
                $wishlist_count = $this->getWishListCount($customer->id);
                $this->content['login_user'] = array(
                    'status' => 'success',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('User login successfully'),
                        'AppLogin'
                    ),
                    'customer_id' => $customer->id,
                    'wishlist_count' => $wishlist_count,
                    'session_data' => (int)$this->context->cart->id,
                    'cart_count' => Cart::getNbProducts($this->context->cookie->id_cart)
                );

                // Login information have changed, so we check if the cart rules still apply
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }
}

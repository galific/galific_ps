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
 * API to add details of guest user
 */

require_once 'AppCore.php';

class AppGuestRegistration extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $email = Tools::getValue('email', '');
        $cart_id = (int)Tools::getValue('session_data', '');
        if (!empty($cart_id)) {
            $this->context->cart->id_currency = $this->context->currency->id;
            $this->context->cart = new Cart($cart_id);
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cookie->write();
        }
        if (empty($email)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Please provide email address'),
                'AppGuestRegistration'
            );
            $this->writeLog('Email address is empty.');
        } else {
            if (!Validate::isEmail($email)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Invalid email address.'),
                    'AppGuestRegistration'
                );
                $this->writeLog('Invalid email address.');
            } else {
                $this->addGuestUser($email);
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Add guest user to DB and context
     *
     * @param string $email email address of customer
     */
    public function addGuestUser($email)
    {
        if (Customer::customerExists(strip_tags($email))) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('An account using this email address has already been registered.'),
                'AppGuestRegistration'
            );
            $this->writeLog('Email address has already been registered.');
        } else {
            if ($id_customer = Customer::customerExists(strip_tags($email), true, false)) {
                $customer = new Customer($id_customer);
            } else {
                $col_query = 'SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'customer LIKE "id_lang"';
                $col_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($col_query);
                if (count($col_result) == 1) {
                    $col_exist = 1;
                } else {
                    $col_exist = 0;
                }
                $insertion_time = date('Y-m-d H:i:s', time());
                $original_passd = Tools::substr(md5(uniqid(mt_rand(), true)), 0, 8);
                $passd = Tools::encrypt($original_passd);
                $secure_key = md5(uniqid(rand(), true));
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'customer SET 
                    id_shop_group = ' . (int) $this->context->shop->id_shop_group . ', 
                    id_shop = ' . (int) $this->context->shop->id . ', 
                    id_gender = 0, 
                    id_default_group = ' . (int) Configuration::get('PS_GUEST_GROUP') . ',';
                if ($col_exist == 1) {
                    $sql .= 'id_lang = ' . (int) $this->context->language->id . ',';
                }
                $sql .= 'id_risk = 0, 
                    firstname = "", 
                    lastname = "", 
                    email = "' . pSQL(strip_tags($email)) . '", 
                    passwd = "' . pSQL($passd) . '", 
                    max_payment_days = 0, 
                    secure_key = "' . pSQL($secure_key) . '", 
                    active = 1, is_guest = 1,
                    date_add = "' . pSQL($insertion_time) . '",
                    date_upd = "' . pSQL($insertion_time) . '"';

                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                $id_customer = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                $customer = new Customer();
                $customer->id = $id_customer;
                $customer->firstname = "";
                $customer->lastname = "";
                $customer->passwd = $passd;
                $customer->email = $email;
                $customer->secure_key = $secure_key;
                $customer->birthday = "";
                $customer->is_guest = 1;
                $customer->active = 1;

                $customer->cleanGroups();
                $customer->addGroups(array((int) Configuration::get('PS_GUEST_GROUP')));
            }

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

            $id_carrier = (int) $this->context->cart->id_carrier;
            $this->context->cart->id_carrier = 0;
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->id_address_delivery = (int)
                    Address::getFirstCustomerAddressId((int) $customer->id);
            $this->context->cart->id_address_invoice = (int)
                    Address::getFirstCustomerAddressId((int) $customer->id);

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
            $this->content['status'] = 'success';
            $this->content['message'] = '';
            $this->writeLog('Guest successfully Created.');
        }
    }
}

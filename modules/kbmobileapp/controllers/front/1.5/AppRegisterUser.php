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
 * API to register user on store
 */

require_once 'AppCore.php';

class AppRegisterUser extends AppCore
{
    private $product = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $user_data = Tools::getValue('signup', Tools::jsonEncode(array()));
        $user_data = Tools::jsonDecode($user_data);
        $cart_id = Tools::getValue('session_data', '');
        if (!empty($cart_id)) {
            $this->context->cart->id_currency = $this->context->currency->id;
            $this->context->cart = new Cart($cart_id);
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cookie->write();
        }
        if (!empty($user_data)) {
            if (!Validate::isName($user_data->first_name)) {
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Invalid First Name.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Invalid First Name.');
            } elseif (!Validate::isName($user_data->last_name)) {
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Invalid Last Name.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Invalid Last Name.');
            } elseif (!Validate::isEmail($user_data->email)) {
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Invalid email address.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Invalid email address.');
            } elseif (!Validate::isPasswd($user_data->password)) {
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Invalid Password.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Invalid Password.');
            } elseif (!$this->isMandatoryMobile($user_data->mobile_number, $user_data->country_code)) {
                /* Changes started
                 * @author : Rishabh jain
                 * Dom: 19th sep 2018
                 * to verify mobile number is mandatory or not
                 */
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Mobile number and country code is Mandatory.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Empty Mobile Number.');
            } elseif ($user_data->mobile_number != '' && $user_data->country_code == '') {
                /* Changes started
                 * @author : Rishabh jain
                 * Dom: 19th sep 2018
                 * to verify mobile number is mandatory or not
                 */
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Country Code is Mandatory.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Empty Country code.');
            } else {
                $this->addUser($user_data);
            }
        } else {
            $this->content['signup_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer data is not available.'),
                    'AppRegisterUser'
                )
            );
            $this->writeLog('Customer data is not available.');
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

   
    /**
     * Add user information to DB and Context
     *
     *@param array $user_data user information
     */
    public function addUser($user_data)
    {
        if (!empty($user_data)) {
            if (Customer::customerExists(strip_tags($user_data->email))) {
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('An account using this email address has already been registered.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Email address has already been registered.');
            } elseif ($user_data->mobile_number != '' && $this->isMobileNumberExist($user_data, $this->context->shop->id)) {
                /* Changes started
                 * @author Rishabh Jain
                 * DOm : 19/09/2018
                 * To check if same mobile number exist with other account
                 */
                $this->content['signup_user'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('This mobile number has already been registered with an other account.'),
                        'AppRegisterUser'
                    )
                );
                $this->writeLog('Mobile Number has already been registered.');
            } else {
                $col_query = 'SHOW COLUMNS FROM ' . _DB_PREFIX_ . 'customer LIKE "id_lang"';
                $col_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($col_query);
                if (count($col_result) == 1) {
                    $col_exist = 1;
                } else {
                    $col_exist = 0;
                }
                $user_data->gender = $user_data->title;
                if (!empty($user_data->dob)) {
                    $dob = array();
                    $dob = explode("/", $user_data->dob);
                    $user_data->dob = $dob[2] . "-" . $dob[1] . "-" . $dob[0];
                }
                $insertion_time = date('Y-m-d H:i:s', time());
                $original_passd = $user_data->password;
                $passd = Tools::encrypt($original_passd);
                $secure_key = md5(uniqid(rand(), true));
                /* condition added by rishabh on 18th sep 2018
                    to fix gender issue in ios app
                    */
                if ($user_data->gender != '') {
                    $gender_qry = '(select id_gender from ' . _DB_PREFIX_ . 'gender '
                            . 'where type = ' . pSQL($user_data->gender) . ')';
                    $gender = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($gender_qry);
                    if (empty($gender)) {
                        $user_data->gender = 0;
                    }
                }
                //changes over
                $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'customer SET 
                    id_shop_group = ' . (int) $this->context->shop->id_shop_group . ', 
                    id_shop = ' . (int) $this->context->shop->id . ', 
                    id_gender = ' . (int) $user_data->gender . ', 
                    id_default_group = ' . (int) Configuration::get('PS_CUSTOMER_GROUP') . ',';
                if ($col_exist == 1) {
                    $sql .= 'id_lang = ' . (int) $this->context->language->id . ',';
                }
                $sql .= 'id_risk = 0, 
                    firstname = "' . pSQL(strip_tags($user_data->first_name)) . '", 
                    lastname = "' . pSQL(strip_tags($user_data->last_name)) . '", 
                    email = "' . pSQL(strip_tags($user_data->email)) . '", 
                    passwd = "' . pSQL($passd) . '", 
                    birthday = "' . pSQL($user_data->dob) . '", 
                    max_payment_days = 0, 
                    secure_key = "' . pSQL($secure_key) . '", 
                    active = 1, date_add = "' . pSQL($insertion_time) . '", date_upd = "' . pSQL($insertion_time) . '"';

                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                $id_customer = Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                $customer = new Customer();
                $customer->id = $id_customer;
                $customer->firstname = ucwords($user_data->first_name);
                $customer->lastname = ucwords($user_data->last_name);
                $customer->passwd = $passd;
                $customer->email = $user_data->email;
                $customer->secure_key = $secure_key;
                $customer->birthday = $user_data->dob;
                $customer->is_guest = 0;
                $customer->active = 1;
                $customer->logged = 1;
                $customer->id_shop = $this->context->shop->id;
                $customer->id_lang = $this->context->language->id;
                        

                $customer->cleanGroups();
                $customer->addGroups(array((int) Configuration::get('PS_CUSTOMER_GROUP')));

                $this->sendConfirmationMail($customer, $original_passd);
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
                $wishlist_count = $this->getWishListCount($customer->id);
                /* Changes started
                 * @author : Rishabh Jain
                 * DOM : 19/09/2018
                 * to add mobile number data in table
                 */
                if (Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION') == 1) {
                    if ($user_data->mobile_number != '' && $user_data->country_code != '') {
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'kbmobileApp_unique_verification SET 
                        id_customer = ' . (int) $customer->id . ',
                        id_shop = ' . (int) $this->context->shop->id . ', 
                        mobile_number = "' . pSQL($user_data->mobile_number) . '", 
                        country_code = "' . pSQL($user_data->country_code) . '",
                        fid = "",
                        date_added =  now(),
                        date_update = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                    }
                }
                /* Changes over */
                $this->content['signup_user'] = array(
                    'status' => 'success',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Customer successfully Created.'),
                        'AppRegisterUser'
                    ),
                    'customer_id' => $customer->id,
                    'wishlist_count' => $wishlist_count,
                    'session_data' => (int)$this->context->cart->id,
                    'cart_count' => Cart::getNbProducts($this->context->cookie->id_cart)
                );
                $this->writeLog('Customer successfully Created.');
            }
        }
    }

    /**
     * Send confirmation mail after successfully registration
     *
     * @param Object Customer $customer customer object
     * @param string $passd customer password
     */
    protected function sendConfirmationMail($customer, $passd)
    {
        if (!Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
            return true;
        }

        return Mail::Send(
            $this->context->language->id,
            'account',
            Mail::l('Welcome!'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => $passd
            ),
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname
        );
    }
}

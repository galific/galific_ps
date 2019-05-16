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

class AppMapEmailWithUUID extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $user_data = array();
        $user_data['email'] = Tools::getValue('email_id');
        $user_data['unique_fingerprint_id'] = Tools::getValue('unique_id');
        if ($user_data['email'] != '' || $user_data['unique_fingerprint_id'] != '') {
            if (!Validate::isEmail($user_data['email'])) {
                $this->content = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Invalid email address.'),
                        'AppMapEmailWithUUID'
                    )
                );
                $this->writeLog('Invalid email address.');
            } else {
                $this->mapEmailId($user_data);
            }
        } else {
            $this->content = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer data is not available.'),
                    'AppMapEmailWithUUID'
                )
            );
            $this->writeLog('Empty email id or fingerprints.');
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

   
    /**
     * Add user information to DB and Context
     *
     *@param array $user_data user information
     */
    public function mapEmailId($user_data)
    {
        if (!empty($user_data)) {
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
                /* Changes started
                 * @author : Rishabh Jain
                 * DOM : 25/09/2018
                 * to add fingerprint data data in table
                 */
                if (Configuration::get('KB_MOBILEAPP_FINGERPRINT_LOGIN') == 1) {
                    $customer = array();
                    $customer = Customer::getCustomersByEmail(strip_tags($user_data['email']));
                    $customer = $customer[0];
                    $data = array();
                    $sql = 'Select count(*) FROM ' . _DB_PREFIX_ . 'kbmobileApp_unique_verification '
                        . 'Where id_customer =' . (int) $customer['id_customer'];
                    $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getvalue($sql);
                    if ($data > 0) {
                        $sql = 'UPDATE ' . _DB_PREFIX_ . 'kbmobileApp_unique_verification SET 
                            fid = "' . pSQL($user_data['unique_fingerprint_id']) . '"
                            , date_update = now() Where id_customer =' . (int) $customer['id_customer'];
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                    } else {
                        $sql = 'INSERT INTO ' . _DB_PREFIX_ . 'kbmobileApp_unique_verification SET 
                                id_customer = ' . (int) $customer['id_customer'] . ',
                                id_shop = ' . (int) $customer['id_shop'] . ', 
                                fid = "' . pSQL($user_data['unique_fingerprint_id']) . '", 
                                country_code = "",
                                mobile_number = "",
                                date_added =  now(),
                                date_update = now()';
                        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                    }
                    $this->content = array(
                        'status' => 'success',
                        'message' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('The account has been mapped for the fingerprint login in this device.'),
                            'AppMapEmailWithUUID'
                        )
                    );
                    $this->writeLog('The account has been mapped for the fingerprint login in this device.');
                } else {
                    $this->content = array(
                        'status' => 'failure',
                        'message' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('The account can not be mapped for the fingerprint login in this device.'),
                            'AppMapEmailWithUUID'
                        )
                    );
                    $this->writeLog('Admin has disabled fingerprint login.');
                }
            }
        }
    }
}

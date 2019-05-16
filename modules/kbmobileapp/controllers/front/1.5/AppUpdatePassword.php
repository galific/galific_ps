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

class AppUpdatePassword extends AppCore
{

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $session_data = Tools::getValue('session_data', '');
        if (!Tools::getIsset('mobile_number') || !Tools::getIsset('country_code') || !Tools::getIsset('new_password')) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Required Data is not available.'),
                    'AppLoginViaPhone'
                ),
                'session_data' => $session_data
            );
            $this->writeLog('Required Data is not available.');
        } else {
            $user_data = array();
            $user_data['mobile_number'] = Tools::getValue('mobile_number');
            $user_data['country_code'] = urlencode(Tools::getValue('country_code'));
            $user_data['new_password'] = Tools::getValue('new_password');
            if ($user_data['mobile_number'] == '') {
                $this->content = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Empty Mobile number.'),
                        'AppUpdatePassword'
                    ),
                    'session_data' => $session_data
                );
                $this->writeLog('Empty Mobile number.');
            } elseif ($user_data['country_code'] == '') {
                $this->content = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Empty Country Code.'),
                        'AppUpdatePassword'
                    ),
                    'session_data' => $session_data
                );
                $this->writeLog('Empty Country Code.');
            } elseif (!Validate::isPasswd($user_data['new_password'])) {
                $this->content = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Invalid Password.'),
                        'AppUpdatePassword'
                    ),
                    'session_data' => $session_data
                );
                $this->writeLog('Invalid Password.');
            } else {
                $this->updatePassword($user_data);
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Add user information to DB and Context
     *
     * @param array $user_data user information
     */
    public function updatePassword($user_data)
    {
        $session_data = Tools::getValue('session_data', '');
        $is_exist_mobile_number = $this->isMobileNumberExistLogin($user_data, $this->context->shop->id);
        if ($is_exist_mobile_number) {
            $customer = new Customer((int) $is_exist_mobile_number);
            /* $is_exist_mobile_number contains the customer id if any otherwise 0 */
            $customer->passwd = Tools::encrypt($user_data['new_password']);
            if ($customer->save()) {
                $this->content = array(
                    'status' => 'sucess',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('The Password has been changed successfully.'),
                        'AppUpdatePassword'
                    ),
                    'session_data' => $session_data
                );
                $this->writeLog('Mobile number Updated Successfully.');
            } else {
                $this->content = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Could not update Password.'),
                        'AppUpdatePassword'
                    ),
                    'session_data' => $session_data
                );
                $this->writeLog('Could not update Password.');
            }
        } else {
            $this->content = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Could not update Password.'),
                    'AppUpdatePassword'
                ),
                'session_data' => $session_data
            );
            $this->writeLog('Mobile number does not Exist.');
        }
    }
}

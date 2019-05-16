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

class AppCheckIfContactNumberExists extends AppCore
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
        if (!Tools::getIsset('mobile_number') || !Tools::getIsset('country_code')) {
            $this->content['login_user'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Mobile Number or country code is not available.'),
                    'AppLoginViaPhone'
                ),
                'does_mobile_number_exists' => false,
                'session_data' => $session_data,
            );
            $this->writeLog('Mobile Number or country code is not available.');
        } else {
            $user_data = array();
            $user_data['mobile_number'] = Tools::getValue('mobile_number');
            $user_data['country_code'] = urlencode(Tools::getValue('country_code'));
            $session_data = Tools::getValue('session_data', '');
            $is_exist_mobile_number = $this->isMobileNumberExistLogin($user_data, $this->context->shop->id);
            if ($is_exist_mobile_number) {
                $this->content = array(
                    'status' => 'success',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Mobile number exists into the database..'),
                        'AppCheckIfContactNumberExists'
                    ),
                    'does_mobile_number_exists' => true,
                    'session_data' => $session_data,
                );
                $this->writeLog('Mobile Number is available.');
            } else {
                $this->content = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Mobile number does not exists into the database..'),
                        'AppCheckIfContactNumberExists'
                    ),
                    'does_mobile_number_exists' => false,
                    'session_data' => $session_data,
                );
                $this->writeLog('Mobile Number is not available.');
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }
}

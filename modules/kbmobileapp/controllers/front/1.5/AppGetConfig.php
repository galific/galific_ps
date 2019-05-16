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
 * API to get Live chat support status and its key
 */

require_once 'AppCore.php';

class AppGetConfig extends AppCore
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
        $this->content['status'] = 'success';
        $this->getWhatsappChatData();
        $this->getLiveChatData();
        $this->getLogConfigurationData();
        $this->getFingerprintLoginConfigurationData();
        $this->getPhoneNumberRegistrationConfigurationData();
        $this->content['session_data'] = $session_data;
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /*
     * Function to get the live chat configuration
     */
    public function getLiveChatData()
    {
        if (Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT_KEY')) {
            $chat_api_key = Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT_KEY');
        } else {
            $chat_api_key = '';
        }
        $this->content['zopim_chat_configurations'] = array(
            'status' => Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT'),
            'chat_api_key' => $chat_api_key
        );
    }
    public function getWhatsappChatData()
    {
        if (Configuration::get('KB_MOBILE_WHATSAPP_CHAT_NUMBER')) {
            $whatsapp_number = Configuration::get('KB_MOBILE_WHATSAPP_CHAT_NUMBER');
        } else {
            $whatsapp_number = '';
        }
        if (Configuration::get('KB_MOBILE_WHATSAPP_CHAT_SUPPORT')) {
            $this->content['whatsapp_configurations'] = array (
                'is_enabled' => true,
                'chat_number' => $whatsapp_number
            );
        } else {
            $this->content['whatsapp_configurations'] = array (
                'is_enabled' => false,
                'chat_number' => $whatsapp_number
            );
        }
    }
    public function getPhoneNumberRegistrationConfigurationData()
    {
        $this->content['phone_number_registartion_configurations'] = array (
            'is_enabled' => Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION'),
            'is_mandatory' => Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY')
        );
    }
    public function getFingerprintLoginConfigurationData()
    {
        $this->content['fingerprint_configurations'] = array (
            'is_enabled' => Configuration::get('KB_MOBILEAPP_FINGERPRINT_LOGIN'),
        );
    }
    public function getLogConfigurationData()
    {
        $this->content['log_configurations'] = array(
            'status' => Configuration::get('KB_MOBILE_APP_ERROR_REPORTING')
        );
    }
}

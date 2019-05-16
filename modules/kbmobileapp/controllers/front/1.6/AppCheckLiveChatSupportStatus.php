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

class AppCheckLiveChatSupportStatus extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->getLiveChatData();
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /*
     * Function to get the live chat configuration
     */
    public function getLiveChatData()
    {
        $this->content['status'] = Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT');
        if (Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT_KEY')) {
            $this->content['chat_api_key'] = Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT_KEY');
        } else {
            $this->content['chat_api_key'] = '';
        }
    }
}

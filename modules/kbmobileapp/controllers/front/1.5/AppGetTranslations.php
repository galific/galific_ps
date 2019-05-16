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
 * API to get translated text in the APP as per the selected language byu customer
 */

require_once 'AppCore.php';

class AppGetTranslations extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $translated_texts = array();
        if (Tools::isSubmit('all_app_texts')) {
            $translated_texts = parent::getAllAppTranslatedTexts(
                Tools::getValue('iso_code', false)
            );
        } elseif (Tools::getValue('app_texts', false)) {
            $texts_to_translate = Tools::getValue('app_texts', '');
            $texts_to_translate = (array)Tools::jsonDecode($texts_to_translate);
            $count = 0;
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
            $iso_code = Language::getIsoById($lang_id);
            if (Tools::isSubmit('iso_code')) {
                $iso_code = Tools::getValue('iso_code');
            }
            $file_path = _PS_MODULE_DIR_.self::APP_TRANSLATION_FILE_FOLDER_PATH.$iso_code.self::TRANSLATION_EXT;
            if (is_readable($file_path)) {
                foreach ($texts_to_translate['translations_text'] as $text) {
                    $translated_texts[$count]['unique_key'] = $text->unique_key;
                    $translated_texts[$count]['iso_code'] = $iso_code;
                    $translated_texts[$count]['trans_text'] = parent::getTranslatedTextByFileAndISO(
                        $iso_code,
                        $text->unique_key,
                        self::APP_TRANSLATION_FILE_NAME
                    );
                    $count++;
                }
            }
        }
        $this->updateLanguageFileRecords();
        $this->content['languages_record'] = $this->returnLanguageRecordAsArray();
        $this->content['install_module'] = '';
        $this->content['translated_texts'] = $translated_texts;
        
        /*Changes start by rishabh jain on 4th sep 2018
         * To disable the facebook and google functionality from admin end.
         * as presently it is creating some issues in ios app.
         */
//        //Read JSON file to get Google details
//        $google_app_id = "";
//        $google_api_key = "";
//        
//        if (Configuration::get('KB_MOBILEAPP_GOOGLE_DATA')) {
//            if (Configuration::get('KB_MOBILEAPP_GOOGLE_DATA') == 1) {
//                $googleStatus = true;
//            }
//        } else {
//            $googleStatus = false;
//        }
//        
//        $facebookdata = array();
//        if (Configuration::get('KB_MOBILEAPP_FACEBOOK_DATA')) {
//            $facebookdata = Tools::unserialize((Configuration::get('KB_MOBILEAPP_FACEBOOK_DATA')));
//        }
//        
//        $facebook_setup_field_value = array(
//            'status' => isset($facebookdata['status']) ? $facebookdata['status'] : '',
//            'app_id' => isset($facebookdata['app_id']) ? $facebookdata['app_id'] : ''
//        );
//        
//        $google_app_id = "";
//        $google_api_key = "";
//        if ($googleStatus) {
//            //Read JSON File
//            if ($filename = Configuration::get('KB_MOBILE_APP_GOOGLE_FILE')) {
//                $dirpath = _PS_IMG_DIR_ . 'kbmobileapp/';
//                $filepath = $dirpath.$filename;
//                if (Tools::file_exists_no_cache($filepath)) {
//                    $json_file_data = Tools::jsonDecode(Tools::file_get_contents($filepath));
//                    //Application ID
//                    if (isset($json_file_data->client[0]->client_info->mobilesdk_app_id)) {
//                        $google_app_id = $json_file_data->client[0]->client_info->mobilesdk_app_id;
//                    }
//                    //API Key
//                    if (isset($json_file_data->client[0]->api_key[0]->current_key)) {
//                        $google_api_key = $json_file_data->client[0]->api_key[0]->current_key;
//                    }
//                }
//            }
//        }
//        
//        //Add code to send Google and FaceBook details
//        $this->content['social_login'] = array(
//            "is_facebook_login_enabled" => ($facebook_setup_field_value['status'] == 1) ? "true" : "false",
//            "is_google_login_enabled" => ($googleStatus) ? "true" : "false",
//            "google_app_id" => $google_app_id,
//            "api_key" => $google_api_key,
//            "fb_app_id" => $facebook_setup_field_value['app_id']
//        );
        /* Changes over */
        return $this->fetchJSONContent();
    }
}

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
        return $this->fetchJSONContent();
    }
}

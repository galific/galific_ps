<?php
/**
 * Class SmartAppBannerGetService
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class SmartAppBannerGetService extends BaseService
{
    private $settingTable = 'SMART_APP_BANNER_SETTING_';
    public function doExecute()
    {
        $shop_id = $this->getRequestValue('id_shop');
        $id_lang = $this->getRequestValue('id_lang');
        $this->settingTable .= $shop_id.'_'.$id_lang;
        if (ConfigurationCore::get($this->settingTable)) {
            $this->response = $smartAppBannerSetting = json_decode(ConfigurationCore::get($this->settingTable));
        } else {
            $this->response = json_decode('{
              "enable": "0",
              "ios_app_id": "",
              "android_app_id": "",
              "days_hidden": "15",
              "days_reminder": "90",
              "app_store_language": "nl",
              "title": "My App",
              "author": "My Company",
              "button": "VIEW",
              "store_ios": "On the App Store",
              "store_android": "In Google Play",
              "price_ios": "FREE",
              "price_android": "FREE"
            }');
        }
    }
}

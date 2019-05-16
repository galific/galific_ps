<?php
/**
 * Class SmartAppBannerSaveService
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class SmartAppBannerSaveService extends BaseService
{
    private $banner_init_script;
    private $setting = 'SMART_APP_BANNER_SETTING_';
    private $script = 'SMART_APP_BANNER_SCRIPT_';

    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            $smartAppBannerSetting = json_decode($jsonRequestBody);
            $shop_id = $this->getRequestValue('id_shop');
            $id_lang = $this->getRequestValue('id_lang');
            $this->setting .= $shop_id.'_'.$id_lang;
            $this->script .= $shop_id.'_'.$id_lang;
            ConfigurationCore::updateValue($this->setting, $jsonRequestBody);
            if (strcmp($smartAppBannerSetting->enable, "1") == 0) {
                $this->banner_init_script = $this->genereteBannerScript($smartAppBannerSetting);
                ConfigurationCore::updateValue($this->script, $this->banner_init_script);
            } else {
                ConfigurationCore::updateValue($this->script, null);
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
        $this->response = null;
        $this->response->errorCode=0;
        $this->response->errorMsg='success';
    }

    public function genereteBannerScript($smartAppBannerSetting)
    {
        $script = 'new SmartBanner({';
        $script .= 'daysHidden:';
        $script .= $smartAppBannerSetting->days_hidden;
        $script .= ',daysReminder:';
        $script .= $smartAppBannerSetting->days_reminder;
        $script .= ',appStoreLanguage:\'';
        $script .= $smartAppBannerSetting->app_store_language;
        $script .= '\',title:\'';
        $script .= $smartAppBannerSetting->title;
        $script .= '\',author:\'';
        $script .= $smartAppBannerSetting->author;
        $script .= '\',button:\'';
        $script .= $smartAppBannerSetting->button;
        $script .= '\',store:{ios:\'';
        $script .= $smartAppBannerSetting->store_ios;
        $script .= '\',android:\'';
        $script .= $smartAppBannerSetting->store_android;
        $script .= '\'},price:{ios:\'';
        $script .= $smartAppBannerSetting->price_ios;
        $script .= '\',android:\'';
        $script .= $smartAppBannerSetting->price_android;
        $script .= '\'}});';

        return $script;
    }
}

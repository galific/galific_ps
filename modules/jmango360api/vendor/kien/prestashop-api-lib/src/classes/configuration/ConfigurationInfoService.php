<?php
/**
 * @author kien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ConfigurationInfoService extends BaseService
{
    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $jsonRequestBody = $this->getRequestBody();
            $data = json_decode($jsonRequestBody);
            ConfigurationCore::updateValue("JM_TICKET", $data->ticket);
            ConfigurationCore::updateValue("JM_EMAIL", $data->email);
            ConfigurationCore::updateValue("JM_APP_KEY", $data->appKey);
            ConfigurationCore::updateValue("JM_DEV_MODE", $data->devMode);
            $this->response = array('ok' => true, 'error' => '');
        }
        if ($this->isGetMethod()) {
            $info = array();
            $info['ticket']=ConfigurationCore::get("JM_TICKET");
            $info['jmemail']=ConfigurationCore::get("JM_EMAIL");
            $info['id_lang']=ConfigurationCore::get("JM_ID_LANG");
            $info['id_shop']=ConfigurationCore::get("JM_ID_SHOP");
            $this->response = $info;
        }
        if ($this->isPutMethod()) {
            $jsonRequestBody = $this->getRequestBody();
            $data = json_decode($jsonRequestBody);
            ConfigurationCore::updateValue("JM_ID_LANG", $data->langId);
            ConfigurationCore::updateValue("JM_ID_SHOP", $data->shopId);
            $this->response = array('ok' => true, 'error' => '');
        }
    }
}

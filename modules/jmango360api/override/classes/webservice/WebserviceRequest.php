<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class WebserviceRequest extends WebserviceRequestCore
{
    public static function getResources()
    {
        if (!class_exists('WebserviceSpecificManagementJapi')) {
            spl_autoload_register(function ($class) {
                if (file_exists(_PS_MODULE_DIR_ . 'jmango360api/classes/webservice/WebserviceSpecificManagementJapi.php')) {
                    require_once _PS_MODULE_DIR_ . 'jmango360api/classes/webservice/WebserviceSpecificManagementJapi.php';
                } else {
                    require_once _PS_MODULE_DIR_ . 'jmango360pwa/classes/webservice/WebserviceSpecificManagementJapi.php';
                }
            });
        }

        $resources = parent::getResources();
        $resources['japi'] = array('description' => 'JMango360 Extended APIs', 'specific_management' => true);
        ksort($resources);
        return $resources;
    }
    public static function getRequestBody()
    {
        return parent::getInstance()->_inputXml;
    }
    public static function getHtmlMethod()
    {
        return parent::getInstance()->method;
    }
}

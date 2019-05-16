<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 2/7/18
 * Time: 1:51 PM
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CustomRequest extends WebserviceRequestCore
{
    public static function getResources()
    {
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

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
 */

class KbMobileAppApiCallModuleFrontController extends ModuleFrontController
{
    /*
     * Build an front controller
     */
    public function __construct()
    {
        parent::__construct();
        $this->context = Context::getContext();
    }

    /*
     * Default front controller initialize function
     * using this we set the json content of webservice
     */
    public function initContent()
    {
        Context::getContext()->cart = new Cart();
        $result = array(
            'type' => 'json',
            'headers' => array(),
            'status' => $_SERVER['SERVER_PROTOCOL'] . '503 Service Unavailable',
            'content' => Tools::jsonEncode(
                array(
                    'status' => 'failure',
                    'message' => $this->module->l('Request unavailable', 'ApiCall')
                )
            )
        );
        if (Tools::getValue('route', false)) {
            $url = Tools::getValue('route', false);
            $class_name = (trim($url, '/'));
            $api_version = Tools::getValue('version');
            if ($api_version != '1.1') {
                $class_name = Tools::ucfirst($class_name);
            }
            $module_controller_dir = _PS_MODULE_DIR_. 'kbmobileapp/controllers/front/';
            $module_controller_dir = $module_controller_dir.$api_version.'/';
            if (Tools::file_exists_no_cache($module_controller_dir.$class_name.'.php')) {
                require_once $module_controller_dir.$class_name.'.php';
                $class_name = Tools::ucwords($class_name);
                $request = new $class_name($url);
                $result = $request->fetch();
            }
        }

        /* Manage cache */
        if (isset($_SERVER['HTTP_LOCAL_CONTENT_SHA1'])
            && $_SERVER['HTTP_LOCAL_CONTENT_SHA1'] == $result['content_sha1']) {
            $result['status'] = $_SERVER['SERVER_PROTOCOL'].' 304 Not Modified';
        }

        if (is_array($result['headers'])) {
            foreach ($result['headers'] as $param_value) {
                header($param_value);
            }
        }
        if (isset($result['type'])) {
            if (!isset($_SERVER['HTTP_LOCAL_CONTENT_SHA1'])
                || $_SERVER['HTTP_LOCAL_CONTENT_SHA1'] != $result['content_sha1']) {
                echo $result['content'];
            }
        }
        die;
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: bangle
 * Date: 03/05/2018
 * Time: 10:07
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

include_once _PS_MODULE_DIR_ . '/jmango360api/vendor/kien/prestashop-onepage-lib/src/controllers/front/Products16.php';
require_once _PS_MODULE_DIR_ . '/jmango360api/jmango360api.php';

class Jmango360ApiProductsModuleFrontController extends Products16
{
    public function init()
    {
        $module = new Jmango360api();
        $this->module_name = $module->name;
        parent::init();
    }
}

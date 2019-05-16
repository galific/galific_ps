<?php
/**
 * Created by PhpStorm.
 * User: bangle
 * Date: 27/04/2018
 * Time: 15:28
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

include_once _PS_MODULE_DIR_ . '/jmango360api/vendor/kien/prestashop-onepage-lib/src/controllers/front/Onepage17.php';
require_once _PS_MODULE_DIR_ . '/jmango360api/jmango360api.php';

class Jmango360ApiOrderopcModuleFrontController extends Onepage17
{
    public function init()
    {
        $module = new Jmango360api();
        $this->module_name = $module->name;
        parent::init();
    }
}

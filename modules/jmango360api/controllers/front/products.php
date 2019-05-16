<?php
/**
 * @author Jmango
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

require_once _PS_MODULE_DIR_ . '/jmango360api/vendor/autoload.php';
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/products/Products_17.php';

    class Jmango360ApiProductsModuleFrontController extends ModuleFrontController
    {
    }
} elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
    /*edit*/
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/product/RedirectProduct16.php';

    //    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/Products16.php';
//
//    class Jmango360ApiProductsModuleFrontController extends Products16
//    {
//    }
} else {
    /*edit*/
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/product/RedirectProduct15.php';

    //    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/Onepage15.php';
//
//    class Jmango360ApiProductsModuleFrontController extends ModuleFrontController
//    {
//    }
}

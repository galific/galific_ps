<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class jmango360apiAddressModuleFrontController
 */

require_once _PS_MODULE_DIR_ . '/jmango360api/vendor/autoload.php';
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/address/RedirectAddress17.php';
//
//    class Jmango360ApiAddressModuleFrontController extends Address17
//    {
//    }
} elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/address/RedirectAddress16.php';
//
//    class Jmango360ApiAddressModuleFrontController extends Address16
//    {
//    }
} else {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/address/RedirectAddress15.php';
//
//    class Jmango360ApiAddressModuleFrontController extends Onepage_15
//    {
//    }
}

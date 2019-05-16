<?php
/**
 * Created by PhpStorm.
 * User: tien
 * Date: 12/21/17
 * Time: 18:03
 * @author : tien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

require_once _PS_MODULE_DIR_ . '/jmango360api/vendor/autoload.php';
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/checkout/RedirectCheckout17.php';
} elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/checkout/RedirectCheckout16.php';
} else {
    include_once _PS_MODULE_DIR_ . '/jmango360api/controllers/front/checkout/RedirectCheckout15.php';
}

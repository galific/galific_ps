<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class jmango360apiPaymentModuleFrontController
 */
class Jmango360ApiInfoModuleFrontController extends ModuleFrontController
{
    public function display()
    {
        header('Content-Type: application/json');
        echo json_encode(array(
            'plugin_version' => $this->module->version,
            'prestashop_version' => defined('_PS_VERSION_') ? _PS_VERSION_ : null
        ));

        return true;
    }
}

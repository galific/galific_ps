<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class Onepage_15
 */
class Onepage15 extends OrderOpcController
{
    public $php_self = '';
    public $module_name;
    /**
     * Initialize parent order controller
     * @see FrontController::init()
     */
    public function init()
    {
        /**
         * Spoof 'redirect to the good order process' logic
         */
        Configuration::set('PS_ORDER_PROCESS_TYPE', 1);
        $this->context->smarty->assign(array(
            'module_name' => $this->module_name,
        ));
        parent::init();

        $this->display_footer = false;
    }

    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/order-opc-15.tpl');
    }
}

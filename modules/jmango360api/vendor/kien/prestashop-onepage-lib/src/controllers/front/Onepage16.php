<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class Onepage_16
 */
class Onepage16 extends OrderOpcController
{
    public $php_self = '';
    public $module_name;
    /**
     * Initialize parent order controller
     * @see FrontController::init()
     */
    public function init()
    {
        Configuration::set('PS_ORDER_PROCESS_TYPE', 1);
        $this->context->smarty->assign(array(
            'module_name' => $this->module_name,
        ));
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        $this->setTemplate(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage16.tpl');
    }

    /**
     * Initializes page header variables
     */
    public function initHeader()
    {
        parent::initHeader();

        $this->context->smarty->assign('content_only', 1);
    }
}

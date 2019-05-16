<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 */

require_once(_PS_MODULE_DIR_ . 'kbmarketplace/libraries/kbmarketplace/KbGlobal.php');
class AdminKbMarketplaceCoreController extends ModuleAdminControllerCore
{
    protected $kb_module_name = 'kbmarketplace';
    public $bootstrap = true;
    public $kbtemplate = 'not_found_page.tpl';
    public $custom_smarty;
    protected $approval_statuses = array();
    protected $statuses = array();
    protected $render_ajax_html = false;

    public function __construct()
    {
        $this->allow_export = true;
        $this->approval_statuses = KbGlobal::getApporvalStatus();
        $this->statuses = KbGlobal::getStatuses();
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->custom_smarty = new Smarty();
        $this->custom_smarty->setCompileDir(_PS_CACHE_DIR_ . 'smarty/compile');
        $this->custom_smarty->setCacheDir(_PS_CACHE_DIR_ . 'smarty/cache');
        $this->custom_smarty->use_sub_dirs = true;
        $this->custom_smarty->setConfigDir(_PS_SMARTY_DIR_ . 'configs');
        $this->custom_smarty->caching = false;
        $this->custom_smarty->registerPlugin('function', 'l', 'smartyTranslate');
        $this->custom_smarty->setTemplateDir(_PS_MODULE_DIR_ . $this->kb_module_name . '/views/templates/admin/');

        parent::__construct();
    }

    public function initProcess()
    {
        parent::initProcess();
        $this->object = new $this->className(Tools::getValue($this->identifier));
    }

    public function processFilter()
    {
        parent::processFilter();
        $prefix = str_replace(array('admin', 'controller'), '', Tools::strtolower(get_class($this)));
        $filters = $this->context->cookie->getFamily($prefix . $this->list_id . 'Filter_');
        $has_active_filter = false;
        $value = 1;
        $active_filter_key = $this->list_id . 'Filter_active';
        if (isset($filters[$prefix . $this->list_id . 'Filter_active'])) {
            $value = $filters[$prefix . $this->list_id . 'Filter_active'];
            $has_active_filter = true;
        } elseif (Tools::getIsset($active_filter_key)) {
            $value = Tools::getValue($active_filter_key);
            $has_active_filter = true;
        }

        if ($has_active_filter) {
            if (isset($this->fields_list['active']['filter_key'])) {
                $key = $this->fields_list['active']['filter_key'];
                $this->_filter = str_replace(' AND a.`active` = ' . $value . ' ', '', $this->_filter);
                $this->_filter = str_replace(' AND a.`active` = ' . $value . ' ', '', $this->_filter);
                $this->_filter = str_replace(' AND a.active = ' . $value . ' ', '', $this->_filter);
                $this->_filter = str_replace(' AND `a.active` = ' . $value . ' ', '', $this->_filter);
                $tmp_tab = explode('!', $key);
                $key = isset($tmp_tab[1]) ? $tmp_tab[0] . '.`' . $tmp_tab[1] . '`' : '`' . $tmp_tab[0] . '`';
                $this->_filter .= ' AND ' . $key . ' = ' . $value . ' ';
            }
        }
    }

    public function postProcess()
    {
        parent::postProcess();

        if (Tools::isSubmit('ajax')) {
            $return = null;
            Hook::exec('actionAjaxKbAdmin' . Tools::ucfirst($this->action) . 'Before', array('controller' => $this));
            Hook::exec(
                'actionAjaxKb' . get_class($this) . Tools::ucfirst($this->action) . 'Before',
                array('controller' => $this)
            );
            if (Tools::getIsset('ajaxView' . $this->table)) {
                if (method_exists($this, 'processKbAjaxView')) {
                    $return = $this->processKbAjaxView();
                }
            } elseif (Tools::isSubmit('action')) {
                $this->action = Tools::getValue('action');
                if (!empty($this->action)
                    && method_exists($this, 'ajaxKbProcess' . Tools::toCamelCase($this->action))) {
                    $return = $this->{'ajaxKbProcess' . Tools::toCamelCase($this->action)}();
                }
            }
            Hook::exec(
                'actionAjaxKbAdmin' . Tools::ucfirst($this->action) . 'After',
                array('controller' => $this,'return' => $return)
            );
            Hook::exec(
                'actionAjaxKb' . get_class($this) . Tools::ucfirst($this->action) . 'After',
                array('controller' => $this, 'return' => $return)
            );
            if ($this->render_ajax_html) {
                echo $return;
            } else {
                echo Tools::jsonEncode($return);
            }
            die;
        }
    }

    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_error)) {
            $this->errors[] = $this->context->cookie->kb_redirect_error;
            unset($this->context->cookie->kb_redirect_error);
        }

        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        
        parent::initContent();
    }

    public function renderView()
    {
        return parent::renderView();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addCSS($this->getKbModuleDir() . 'views/css/admin/kb-marketplace.css');
        $this->addJS($this->getKbModuleDir() . 'views/js/admin/kb-marketplace.js');
        $this->addJS($this->getKbModuleDir() . 'views/js/admin/mpconfigure.js');
    
    }

    public function init()
    {
        parent::init();
    }

    protected function getKbModuleDir()
    {
        return _PS_MODULE_DIR_ . $this->kb_module_name . '/';
    }


    public function showFinalPrice($id_row, $tr)
    {
        unset($id_row);
        return Product::getPriceStatic($tr['id_product'], true, null, 2, null, false, true, 1, true);
    }

    /*
     * Display active status without clickable
     */

    public function showNonClickableStatus($id_row, $tr)
    {
        unset($id_row);
        if ($tr['active'] == 1) {
            return '<a class="list-action-enable action-enabled" href="javascript:void(0)" 
				title="' . $this->module->l('Enable', 'adminkbmarketplacecorecontroller') . '"><i class="icon-check"></i></a>';
        } else {
            return '<a class="list-action-enable action-disabled" href="javascript:void(0)" 
				title="' . $this->module->l('Disable', 'adminkbmarketplacecorecontroller') . '"><i class="icon-remove"></i></a>';
        }
    }
    
    public function renderList()
    {
        $list = parent::renderList();
        $this->bulk_actions = null;

        return $list;
    }
}

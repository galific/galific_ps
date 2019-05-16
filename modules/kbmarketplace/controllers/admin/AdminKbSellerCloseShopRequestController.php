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
 */

require_once dirname(__FILE__).'/AdminKbMarketplaceCoreController.php';

class AdminKbSellerCloseShopRequestController extends AdminKbMarketplaceCoreController
{
    protected $seller_info = array();
    protected $account_deleted_statuses = array();
    public function __construct()
    {
        $this->bootstrap     = true;
        $this->className     = 'Configuration';
        parent::__construct();
    }
    
    public function postProcess()
    {
        parent::postProcess();
    }
    
    private function unusefullFunction()
    {
        $this->module->l('Owner name', 'AdminKbSellerCloseShopRequestController');
        $this->module->l('Details', 'AdminKbSellerCloseShopRequestController');
        $this->module->l('Bank Address', 'AdminKbSellerCloseShopRequestController');
        $this->module->l('Additional Information', 'AdminKbSellerCloseShopRequestController');
        $this->module->l('Address', 'AdminKbSellerCloseShopRequestController');
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJS(_MODULE_DIR_.'kbmarketplace/views/js/admin/fixes.js');
    }
    
    public function initContent()
    {
        $base_link = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
        $profile_default_image_path = $base_link.'modules/'.$this->module->name.'/'.'views/img/shop_close.png';
        $tpl = $this->custom_smarty->createTemplate('free_block.tpl');
        $tpl->assign('img_url', $profile_default_image_path);
        $this->content .= $tpl->fetch();
    
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    }
}

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

class AdminKbSellerShippingMethodController extends AdminKbMarketplaceCoreController
{
    protected $seller_info = array();

    public function __construct()
    {
        $this->bootstrap     = true;
        $this->table         = 'kb_mp_seller_shipping_method';
        $this->className     = 'KbSellerShippingMethod';
        $this->identifier    = 'id_shipping_method';
        parent::__construct();
    }
    

    
    public function postProcess()
    {
    
     parent::postProcess();
                    }

    
    public function setMedia($is_new_theme = false)
    {
        parent::setMedia($is_new_theme);
        $this->context->controller->addJS(_MODULE_DIR_.'kbmarketplace/views/js/admin/fixes.js');
    }
    

    
    public function initContent()
    {
        $base_link = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
        $profile_default_image_path = $base_link.'modules/'.$this->module->name.'/'.'views/img/shipping_method.png';
        $tpl = $this->custom_smarty->createTemplate('free_block.tpl');
        $tpl->assign('img_url', $profile_default_image_path);
        $this->content .= $tpl->fetch();
        parent::initContent();
    }
    
    public function initProcess()
    {
        parent::initProcess();
    }
    
    public function initToolbar()
    {
        parent::initToolbar();
    }

    
        }

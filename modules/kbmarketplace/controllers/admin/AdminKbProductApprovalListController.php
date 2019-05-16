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

class AdminKbProductApprovalListController extends AdminKbMarketplaceCoreController
{

    public function __construct()
    {
        $this->bootstrap     = true;
        $this->table         = 'product';
        $this->className     = 'Product';
        $this->identifier    = 'id_product';
        $this->display       = 'list';
        $this->context       = Context::getContext();
        parent::__construct();
        $this->toolbar_title = $this->module->l('Product Approval List', 'adminkbproductapprovallistcontroller');
        $this->imageType     = 'jpg';

        $alias_image = 'image_shop';

    	if ( (isset($_GET['id'])) && (isset($_GET['state'])) ){
          // $this->setProductState((int)$_GET['id'], $_GET['state'], $_GET['token']);

          $sql = "UPDATE `ps_kb_mp_seller_product` SET `approved`= '".$_GET['state']."' WHERE `id_product` = ". (int)$_GET['id'] ."";
          $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
          Tools::redirect("https://galific.com/admin623dgtg6r/index.php?controller=AdminKbProductApprovalList&token=".$_GET['token']."");
	}
    }
    
    
    public function initProcess()
    {
        parent::initProcess();
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->addJqueryPlugin('fancybox');
    }

    public function initContent()
    {
     	$base_link = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
        $profile_default_image_path = $base_link.'modules/'.$this->module->name.'/'.'views/img/product_approval.png';
        $tpl = $this->custom_smarty->createTemplate('free_block.tpl');
       
	$sql = "SELECT firstname , lastname, email, ps_kb_mp_seller_product.approved as status, ps_kb_mp_seller_product.id_product as view FROM `ps_customer` INNER JOIN `ps_kb_mp_seller` ON `ps_kb_mp_seller`.`id_customer` = `ps_customer`.`id_customer` INNER JOIN `ps_kb_mp_seller_product` ON `ps_kb_mp_seller_product`.`id_seller` = `ps_kb_mp_seller`.`id_seller`";
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        $tpl->assign('list', $value);
	//$tpl->assign('length', count($value));
	$tpl->assign('img_url', $profile_default_image_path);
	$this->content .= $tpl->fetch();
        parent::initContent();

    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function getList(
        $id_lang,
        $orderBy = null,
        $orderWay = null,
        $start = 0,
        $limit = null,
        $id_lang_shop = null
    ) {
        parent::getList($id_lang, $orderBy, $orderWay, $start, $limit, $id_lang_shop);
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
    
    }

    public function setProductState($product_id, $state_id, $token)
    {
    }

    
}

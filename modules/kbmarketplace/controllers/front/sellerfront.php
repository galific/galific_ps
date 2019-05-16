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

require_once 'KbFrontCore.php';

class KbmarketplaceSellerfrontModuleFrontController extends KbmarketplaceFrontCoreModuleFrontController
{
    public $controller_name = 'sellerfront';
    private $page_limit = 12;

    public function __construct()
    {
        parent::__construct();
        $this->context->smarty->assign('kb_is_customer_logged', $this->context->customer->logged);
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS(_THEME_CSS_DIR_ . 'product.css');
        if (Tools::getIsset('render_type')
            && (Tools::getValue('render_type') == 'sellerview'
            || Tools::getValue('render_type') == 'sellerproducts')) {
            $this->addCSS(_THEME_CSS_DIR_ . 'product_list.css');
        }
        $this->addJS(_THEME_JS_DIR_ . 'category.js');
    }

    public function postProcess()
    {
        /*Start- MK made changes on 31-05-18 to validate the customer by email*/
        if (Tools::isSubmit('validateCustomerEmail')) {
            $email = trim(Tools::getValue('email'));
            $existing_customer = Customer::customerExists($email, true);
            if (!empty($existing_customer)) {
                echo 1;
            } else {
                echo 0;
            }
            die;
        }
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            $renderhtml = false;
            if (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getSellerList':
                        $this->json['content'] = $this->getAjaxSellerListHtml();
                        break;
                }
            }
            if (!$renderhtml) {
                echo Tools::jsonEncode($this->json);
            }
            die;
        }
    }
    
    public function initContent()
    {

        if (Tools::getIsset('render_type')) {
            if (Tools::getValue('render_type') == 'sellerview') {
                $this->renderViewToCustomer();
            } elseif (Tools::getValue('render_type') == 'sellerproducts') {
                $this->renderSellersProducts();
            } else {
                $this->context->cookie->__set(
                    'redirect_error',
                    $this->module->l('Currently, You are not authorized to view sellers.', 'sellerfront')
                );
            }
        } else {
                $this->renderSellerList();
             
        }

        parent::initContent();
    }

    public function renderSellerList()
    {
        //coupon- mayank kumar
        if (Tools::getValue('searchcoupon') && Tools::getIsset('searchcoupon')) {
            $getCoupon = Tools::getValue('coupon');
            if (!empty($getCoupon) && Tools::getIsset('coupon')) {
                $coupon = Tools::getValue('coupon');
                $free_shipping = Db::getInstance()->getRow(
                    'SELECT * FROM '._DB_PREFIX_.'cart_rule where code="'.pSQL($coupon).'"'
                );
                if ($free_shipping['free_shipping'] == '1') {
                    echo 'free';
                    die;
                } elseif ($free_shipping['free_shipping'] == '0') {
                    echo 'paid';
                    die;
                } else {
                    echo 'no';
                    die;
                }
            }
        }
        //end of free shipping coupon voucher- Mayank kumar
        $start = 1;
        if (Tools::getIsset('kb_page_start') && (int)Tools::getValue('kb_page_start') > 0) {
            $start = Tools::getValue('kb_page_start');
        }
        
        /*
        * Start- MK made changes on 29-06-18 to get the seller based on the context language.
        */
        $total = KbSeller::getSellers(true, true, null, null, null, null, true, true, Context::getContext()->language->id);
        /*
        * End- MK made changes on 29-06-18 to get the seller based on the context language.
        */

        if ($total > 0) {
            $paging = KbGlobal::getPaging($total, $start, $this->page_limit, false, 'getSellerList');

            $orderby = null;
            if (Tools::getIsset('orderby') && Tools::getValue('orderby') != '') {
                $orderby = Tools::getValue('orderby');
            }

            $orderway = null;
            if (Tools::getIsset('orderway') && Tools::getValue('orderway') != '') {
                $orderway = Tools::getValue('orderway');
            }

            $sellers = KbSeller::getSellers(
                false,
                true,
                $paging['page_position'],
                $this->page_limit,
                $orderby,
                $orderway,
                true,
                true,
                Context::getContext()->language->id // MK made changes on 29-08-18 to get seller based on context language
            );

            $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
            $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
            foreach ($sellers as $key => $val) {
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $val['id_seller'] . '/';
                if (empty($val['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $val['logo'])) {
                    $sellers[$key]['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $sellers[$key]['logo'] = $this->seller_image_path . $val['id_seller'] . '/' . $val['logo'];
                }

                if ($val['title'] == '' || empty($val['title'])) {
                    $sellers[$key]['title'] = $this->module->l('Not Mentioned', 'sellerfront');
                }

                $sellers[$key]['href'] = KbGlobal::getSellerLink($val['id_seller']);

                }

            $this->context->smarty->assign('sellers', $sellers);

            $pagination_string = sprintf(
                $this->module->l('Showing %d - %d of %d items', 'sellerfront'),
                $paging['paging_summary']['record_start'],
                $paging['paging_summary']['record_end'],
                $total
            );
            $this->context->smarty->assign('pagination_string', $pagination_string);
            $this->context->smarty->assign('kb_pagination', $paging);
            
                $this->context->smarty->assign('selected_sort', $orderby.':'.$orderway);
        } else {
            $this->context->smarty->assign(
                'empty_list',
                $this->module->l('No Seller found', 'sellerfront')
            );
        }
        $this->setKbTemplate('seller/list_to_customers.tpl');
    }

    public function renderViewToCustomer()
    {
        $id_seller = Tools::getValue('id_seller', 0);
        if ((int)$id_seller > 0) {
            $seller = new KbSeller($id_seller);
            if ($seller->isSeller()) {
                
                /*
                * Start- MK made changes on 29-06-18 to get the seller information based on the context language.
                */
                $seller_info = $seller->getSellerInfo(Context::getContext()->language->id);
                /*
                * End- MK made changes on 29-06-18 to get the seller information based on the context language.
                */
                $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
                $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $id_seller . '/';
                if (empty($seller_info['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $seller_info['logo'])) {
                    $seller_info['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $seller_info['logo'] = $this->seller_image_path . $id_seller . '/' . $seller_info['logo'];
                }

                if (empty($seller_info['banner'])
                    || !Tools::file_exists_no_cache($seller_image_path . $seller_info['banner'])) {
                    $seller_info['banner'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
                } else {
                    $seller_info['banner'] = $this->seller_image_path . $id_seller . '/' . $seller_info['banner'];
                }




                $state_name = '';
                if (!empty($seller_info['state'])) {
                    $state_name = $seller_info['state'];
                }

                $country_name = '';
                if (!empty($seller_info['id_country'])) {
                    $country_name = Country::getNameById($this->context->language->id, $seller_info['id_country']);
                }

                $seller_info['state'] = $state_name;
                $seller_info['country'] = $country_name;
                $this->context->smarty->assign('seller', $seller_info);

                $id_category = Tools::getValue('s_filter_category', '');
                $filters = array();

                if ((int)$id_category > 0) {
                    $filters['id_category'] = (int)$id_category;
                }

                $this->context->smarty->assign('selected_category', $id_category);

                $total_records = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    true
                );

                $sort_by = array('by' => 'pl.name', 'way' => 'ASC');
                $seleted_sort = '';
                if (Tools::getIsset('s_filter_sortby') && Tools::getValue('s_filter_sortby')) {
                    $seleted_sort = Tools::getValue('s_filter_sortby');
                    $explode = explode(':', Tools::getValue('s_filter_sortby'));
                    $sort_by['by'] = $explode[0];
                    $sort_by['way'] = $explode[1];
                }

                $this->context->smarty->assign('selected_sort', $seleted_sort);

                $start = 1;
                if ((int)Tools::getValue('page_number', 0) > 0) {
                    $start = (int)Tools::getValue('page_number', 0);
                }

                $this->context->smarty->assign('seller_product_current_page', $start);

                $paging = KbGlobal::getPaging($total_records, $start, $this->page_limit, false, 'getSProduct2User');

                $products = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    false,
                    $paging['page_position'],
                    $this->page_limit,
                    $sort_by['by'],
                    $sort_by['way']
                );

                $products = Product::getProductsProperties((int)$this->context->language->id, $products);
                
                $products = array_map(array($this, 'prepareProductForTemplate'), $products);

                $this->context->smarty->assign('products', $products);

                if ($products && count($products) > 0) {
                    $pagination_string = sprintf(
                        $this->module->l('Showing %d - %d of %d items', 'sellerfront'),
                        $paging['paging_summary']['record_start'],
                        $paging['paging_summary']['record_end'],
                        $total_records
                    );

                    $this->context->smarty->assign('pagination_string', $pagination_string);
                }

                $this->context->smarty->assign('kb_pagination', $paging);

                $this->context->smarty->assign(
                    'filter_form_action',
                    KbGlobal::getSellerLink($id_seller)
                );

                    $this->context->smarty->assign('display_new_review', false);

                $this->context->smarty->assign('category_list', $this->getCategoryList());
                $this->setKbTemplate('seller/seller_view_to_customer.tpl');
            }
        }
    }


    public function renderSellersProducts()
    {
        $id_seller = Tools::getValue('id_seller', 0);
        if ((int)$id_seller > 0) {
            $seller = new KbSeller($id_seller);
            if ($seller->isSeller()) {
                /*
                * Start- MK made changes on 29-06-18 to get the seller information based on the context language.
                */
                $seller_info = $seller->getSellerInfo(Context::getContext()->language->id);
                /*
                * End- MK made changes on 29-06-18 to get the seller information based on the context language.
                */
                $title = sprintf($this->module->l('Seller Shop - %s', 'sellerfront'), $seller_info['title']);
                $this->context->smarty->assign('kb_page_title', $title);
                $id_category = Tools::getValue('s_filter_category', '');
                $filters = array();

                if ((int)$id_category > 0) {
                    $filters['id_category'] = (int)$id_category;
                }

                $this->context->smarty->assign('selected_category', $id_category);

                $total_records = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    true
                );

                $sort_by = array('by' => 'pl.name', 'way' => 'ASC');
                $seleted_sort = '';
                if (Tools::getIsset('s_filter_sortby') && Tools::getValue('s_filter_sortby')) {
                    $seleted_sort = Tools::getValue('s_filter_sortby');
                    $explode = explode(':', Tools::getValue('s_filter_sortby'));
                    $sort_by['by'] = $explode[0];
                    $sort_by['way'] = $explode[1];
                }

                $this->context->smarty->assign('selected_sort', $seleted_sort);

                $start = 1;
                if ((int)Tools::getValue('page_number', 0) > 0) {
                    $start = (int)Tools::getValue('page_number', 0);
                }

                $this->context->smarty->assign('seller_product_current_page', $start);

                $paging = KbGlobal::getPaging($total_records, $start, $this->page_limit, false, 'getSProduct2User');

                $products = KbSellerProduct::getProductsWithDetails(
                    $id_seller,
                    $this->context->language->id,
                    $filters,
                    false,
                    $paging['page_position'],
                    $this->page_limit,
                    $sort_by['by'],
                    $sort_by['way']
                );

                $products = Product::getProductsProperties((int)$this->context->language->id, $products);
                $products = array_map(array($this, 'prepareProductForTemplate'), $products);

                $this->context->smarty->assign('products', $products);

                if ($products && count($products) > 0) {
                    $pagination_string = sprintf(
                        $this->module->l('Showing %d - %d of %d items', 'sellerfront'),
                        $paging['paging_summary']['record_start'],
                        $paging['paging_summary']['record_end'],
                        $total_records
                    );

                    $this->context->smarty->assign('pagination_string', $pagination_string);
                }

                $this->context->smarty->assign('kb_pagination', $paging);

                $this->context->smarty->assign(
                    'filter_form_action',
                    $this->context->link->getModuleLink(
                        $this->kb_module_name,
                        $this->controller_name,
                        array('render_type' => 'sellerproducts', 'id_seller' => $id_seller),
                        (bool)Configuration::get('PS_SSL_ENABLED')
                    )
                );

                $this->context->smarty->assign('category_list', $this->getCategoryList());
                $this->setKbTemplate('seller/products_to_customer.tpl');
            }
        }
    }

    public function getAjaxSellerListHtml()
    {
        $start = 1;
        if (Tools::getIsset('start') && (int)Tools::getValue('start') > 0) {
            $start = Tools::getValue('start');
        }
        
        /*
        * Start- MK made changes on 29-06-18 to get the seller based on the context language.
        */
        $total = KbSeller::getSellers(true, true, null, null, null, null, true, true, Context::getContext()->language->id);
        /*
        * End- MK made changes on 29-06-18 to get the seller based on the context language.
        */

        if ($total > 0) {
            $paging = KbGlobal::getPaging($total, $start, $this->page_limit, false, 'getSellerList');

            $orderby = null;
            if (Tools::getIsset('orderby') && Tools::getValue('orderby') != '') {
                $orderby = Tools::getValue('orderby');
            }

            $orderway = null;
            if (Tools::getIsset('orderway') && Tools::getValue('orderway') != '') {
                $orderway = Tools::getValue('orderway');
            }

            $sellers = KbSeller::getSellers(
                false,
                true,
                $paging['page_position'],
                $this->page_limit,
                $orderby,
                $orderway,
                true,
                true,
                Context::getContext()->language->id // MK made changes on 29-08-18 to get seller based on context language
            );

            foreach ($sellers as $key => $val) {
                $base_link = KbGlobal::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'));
                $profile_default_image_path = $base_link . 'modules/' . $this->module->name . '/' . 'views/img/';
                $seller_image_path = _PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH . $val['id_seller'] . '/';
                if (empty($val['logo'])
                    || !Tools::file_exists_no_cache($seller_image_path . $val['logo'])) {
                    $sellers[$key]['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $sellers[$key]['logo'] = $this->seller_image_path . $val['id_seller'] . '/' . $val['logo'];
                }

                if ($val['title'] == '' || empty($val['title'])) {
                    $sellers[$key]['title'] = $this->module->l('Not Mentioned', 'sellerfront');
                }

                $sellers[$key]['href'] = KbGlobal::getSellerLink($val['id_seller']);

                    $sellers[$key]['display_write_review'] = false;


                }

            $this->context->smarty->assign('sellers', $sellers);
            $pagination_string = sprintf(
                $this->module->l('Showing %d - %d of %d items', 'sellerfront'),
                $paging['paging_summary']['record_start'],
                $paging['paging_summary']['record_end'],
                $total
            );
            $this->json['pagination_string'] = $pagination_string;
            $this->json['kb_pagination'] = $paging;
            $this->setKbTemplate('seller/seller_list.tpl');
            
            return $this->fetchTemplate();
        }
    }
    
    private function prepareProductForTemplate(array $rawProduct)
    {
        $pro_assembler = new ProductAssembler($this->context);
        $product = $pro_assembler->assembleProduct($rawProduct);

        $factory = new ProductPresenterFactory($this->context, new TaxConfiguration());
        $presenter = $factory->getPresenter();
        $settings = $factory->getPresentationSettings();

        return $presenter->present(
            $settings,
            $product,
            $this->context->language
        );
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();

        $id_seller = Tools::getValue('id_seller', 0);
        if ($id_seller > 0) {
            $seller = new KbSeller($id_seller);
            if (isset($seller->meta_keyword[$this->context->language->id])
            && !empty($seller->meta_keyword[$this->context->language->id])) {
                $page['meta']['keywords'] = Tools::safeOutput($seller->meta_keyword[$this->context->language->id], false);
            }
            
            if (isset($seller->meta_description[$this->context->language->id])
            && !empty($seller->meta_description[$this->context->language->id])) {
                $page['meta']['description'] = Tools::safeOutput($seller->meta_description[$this->context->language->id], false);
            }
            unset($seller);
        } else {
            $global_settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            if (isset($global_settings['kbmp_seller_listing_meta_keywords'])
            && !empty($global_settings['kbmp_seller_listing_meta_keywords'])) {
                $page['meta']['keywords'] = Tools::safeOutput($global_settings['kbmp_seller_listing_meta_keywords'], false);
            }
            
            if (isset($global_settings['kbmp_seller_listing_meta_description'])
            && !empty($global_settings['kbmp_seller_listing_meta_description'])) {
                $page['meta']['description'] = Tools::safeOutput($global_settings['kbmp_seller_listing_meta_description'], false);
            }
            unset($global_settings);
        }
        return $page;
    }
}

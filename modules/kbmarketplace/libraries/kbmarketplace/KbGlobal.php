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

class KbGlobal extends ObjectModel
{
    const PAGING_RECORD_LIMIT = 10;
    const REASON_MIN_LENGTH = 30;
    const SELLER_DEFAULT_LOGO = 'default_seller_logo.jpg';
    const SELLER_DEFAULT_BANNER = 'default_seller_banner.jpg';

    /*
     * Approval Statuses
     */
    const APPROVAL_WAITING = 0;
    const APPROVED = 1;
    const DISSAPPROVED = 2;
const CANCELLED     = 3;

    /*
     * Statuses
     */
    const DISABLE = 0;
    const ENABLE = 1;

    /*
     * Maximum Rating
     */
    const MAX_RATING = 5;

    /*
     * Multi action types
     */
    const MULTI_ACTION_TYPE_STATUS = 1;
    const MULTI_ACTION_TYPE_DELETE = 2;

     public static function getDefaultSettings($key = null)
    {
        $languages_current_shop     = Language::getLanguages(true, Context::getContext()->shop->id);
        $array_seller_agreement     = array();
        $array_order_email_template = array();

        $seller_order_email_template_default = '';

        foreach ($languages_current_shop as $language) {
            $array_seller_agreement[$language['id_lang']]     = '';
            $array_order_email_template[$language['id_lang']] = $seller_order_email_template_default;
        }
        $category = Category::getAllCategoriesName(null,false,false);
        $allowed_category = array();
        foreach ($category as $id => $value) {
            $allowed_categgory[] = $value['id_category'];
        }
        
        $settings = array(
            'kbmp_default_commission' => 15,
            'kbmp_approval_request_limit' => 3,
            'kbmp_product_limit' => 20,
            'kbmp_seller_registration' => 1,
            'kbmp_new_product_approval_required' => 0,
            'kbmp_email_on_new_order' => 0,
            'kbmp_enable_seller_review' => 0,
            'kbmp_seller_review_approval_required' => 0,
            'kbmp_show_seller_on_front' => 1,
            'kbmp_enable_seller_order_handling' => 0,
            'kbmp_enable_free_shipping' => 1,
            'kbmp_enable_seller_order_details' => 0,
            'kbmp_enable_seller_details' => 0,
            'kbmp_seller_listing_meta_keywords' => '',
            'kbmp_seller_listing_meta_description' => '',
            'kbmp_enable_seller_custom_shipping' => 0,
            'kbmp_allowed_categories' => $allowed_categgory,
            'kbmp_seller_agreement' => $array_seller_agreement,
            'kbmp_seller_order_email_template' => $array_order_email_template
        );
        if ($key != null && isset($settings[$key])) {
            return $settings[$key];
        }
        return $settings;   
    }


    public static function getGlobalSettingByKey($key)
    {
        if (!Configuration::get('KB_MARKETPLACE_CONFIG') || Configuration::get('KB_MARKETPLACE_CONFIG') == '') {
            $settings = self::getDefaultSettings();
        } else {
            $settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        }

        return (isset($settings[$key])) ? $settings[$key] : false;
    }

    public static function getSellerMenus()
    {
        $module = Module::getInstanceByName('kbmarketplace');
        return array(
            array('label' => $module->l('Dashboard', 'kbglobal'), 'title' => $module->l('Dashboard', 'kbglobal'), 'module' => 'kbmarketplace',
                'controller' => 'dashboard', 'icon_class' => 'dashboard'),
            array('label' => $module->l('Seller Profile', 'kbglobal'), 'title' => $module->l('Seller Profile', 'kbglobal'), 'module' => 'kbmarketplace',
                'controller' => 'seller', 'icon_class' => 'pencil'),
            array('label' => $module->l('Products', 'kbglobal'), 'title' => $module->l('Products', 'kbglobal'), 'module' => 'kbmarketplace',
                'controller' => 'product', 'icon_class' => 'list', 'count' => 155),
            array('label' => $module->l('Orders', 'kbglobal'), 'title' => $module->l('Orders', 'kbglobal'), 'module' => 'kbmarketplace',
                'controller' => 'order', 'icon_class' => 'shopping-cart', 'count' => 705),
            array('label' => $module->l('Earning', 'kbglobal'), 'title' => $module->l('Earning', 'kbglobal'), 'module' => 'kbmarketplace',
                'controller' => 'earning', 'icon_class' => 'money'),
            );
    }
    


    public static function getPaging($total, $start, $limit, $include_pagin_summary = false, $callback = '')
    {
        $total_pages = ceil((int)$total / $limit);
        $page_position = (($start - 1) * $limit);

        return array(
            'page_position' => $page_position,
            'paging_summary' => self::getPagingSummary($start, $limit, $total, $total_pages),
            'pagination' => self::generatePaginator(
                $start,
                $limit,
                $total,
                $total_pages,
                $callback,
                $include_pagin_summary
            )
        );
    }

    public static function getPagingSummary($start, $page_limit, $total_records, $total_pages)
    {
        $record_start = $start;
        $record_end = (int)$page_limit;
        if ($start == $total_pages) {
            $record_end = $total_records;
        } else {
            $record_end = $start * (int)$page_limit;
        }
        if ($start > 1) {
            $record_start = (($start - 1) * (int)$page_limit) + 1;
        }

        return array(
            'record_start' => $record_start,
            'record_end' => $record_end,
            'record_total' => $total_records,
            'record_pages' => $total_pages
        );
    }

    public static function generatePaginator(
        $start,
        $limit,
        $total_records,
        $total_pages,
        $ajaxcallfn = '',
        $show_total = true
    ) {
        $summary_txt = '';
        $pagination = '';
        if ($total_pages > 0 && $total_pages != 1 && $start <= $total_pages) {
            $summary_align = 'kb-pagination-left';
            $pagination_align = 'kb-pagination-right';
            if (Configuration::get('KBMP_FRONT_PAGINATION_ALIGN') == 'left') {
                $summary_align = 'kb-pagination-right';
                $pagination_align = 'kb-pagination-left';
            }
            $record_start = $start;
            $record_end = (int)$limit;
            if ($start > 1) {
                $record_start = (($start - 1) * (int)$limit) + 1;
                if ($start == $total_pages) {
                    $record_end = $total_records;
                } else {
                    $record_end = $start * (int)$limit;
                }
            }
            $module = Module::getInstanceByName('kbmarketplace');
            $summary_txt = '<div class="' . $summary_align . ' kb-paginate-summary">
				Showing ' . $record_start . ' to ' . $record_end . ' of '
                . $total_records . ' (' . $total_pages . $module->l(' pages', 'kbglobal').')</div>';

            $pagination .= '<div class="' . $pagination_align . '"><ul class="kb-pagination">';

            $ajax_call_function = '';
            if ($ajaxcallfn != '') {
                $ajax_call_function .= $ajaxcallfn . '({page_number});';
            }

            $right_links = $start + 3;
            $previous = $start - 3; //previous link
            $first_link = true; //boolean var to decide our first link

            if ($start > 1) {
                $previous_link = ($previous == 0) ? 1 : $previous;
                $pagination .= '<li class="first"><a href="javascript:void(0)" data-page="1" 
					onclick="' . str_replace('{page_number}', 1, $ajax_call_function) . '" 
					title="'.$module->l('First', 'kbglobal').'">&laquo;</a></li>'; //first link
                $pagination .= '<li><a href="javascript:void(0)" data-page="' . $previous_link . '" 
					onclick="' . str_replace('{page_number}', $previous_link, $ajax_call_function) . '" 
					title="'.$module->l('Previous', 'kbglobal').'">&lt;</a></li>'; //previous link
                for ($i = ($start - 2); $i < $start; $i++) {
                    if ($i > 0) {
                        $pagination .= '<li><a href="javascript:void(0)" data-page="' . $i . '" 
						onclick="' . str_replace('{page_number}', $i, $ajax_call_function) . '" 
						title="'.$module->l('Page', 'kbglobal') . $i . '">' . $i . '</a></li>';
                    }
                }
                $first_link = false; //set first link to false
            }

            if ($first_link) {
                $pagination .= '<li class="first active">' . $start . '</li>';
            } elseif ($start == $total_pages) {
                $pagination .= '<li class="last active">' . $start . '</li>';
            } else {
                $pagination .= '<li class="active">' . $start . '</li>';
            }

            for ($i = $start + 1; $i < $right_links; $i++) {
                if ($i <= $total_pages) {
                    $pagination .= '<li><a href="javascript:void(0)" data-page="' . $i . '" 
					onclick="' . str_replace('{page_number}', $i, $ajax_call_function) . '" 
					title="'.$module->l('Page', 'kbglobal') . $i . '">' . $i . '</a></li>';
                }
            }
            if ($start < $total_pages) {
                $next_link = ($i > $total_pages) ? $total_pages : $i;
                $pagination .= '<li><a href="javascript:void(0)" data-page="' . $next_link . '" 
					onclick="' . str_replace('{page_number}', $next_link, $ajax_call_function) . '" 
					title="'.$module->l('Next', 'kbglobal').'">&gt;</a></li>'; //next link
                $pagination .= '<li class="last"><a href="javascript:void(0)" data-page="' . $total_pages . '" 
					onclick="' . str_replace('{page_number}', $total_pages, $ajax_call_function) . '" 
					title="'.$module->l('Last').'">&raquo;</a></li>'; //last link
            }

            $pagination .= '</div></ul>';
            if ($show_total) {
                return $summary_txt . $pagination;
            } else {
                return $pagination;
            }
        }
        return '';
    }

    public static function getCategories()
    {
        $root_category = Category::getRootCategories();
        $categories = array();
        $tmp = Category::getNestedCategories($root_category[0]['id_category']);
        foreach ($tmp as $c) {
            $categories[] = array(
                'id_category' => $c['id_category'],
                'name' => KbGlobal::getHiphenString((int)$c['level_depth']) . $c['name'],
            );
            if (isset($c['children']) && is_array($c['children']) && count($c['children']) > 0) {
                KbGlobal::generateCategoryTree($c['children'], $categories);
            }
        }
        return $categories;
    }

    public static function generateCategoryTree($categories, &$generated_tree)
    {
        foreach ($categories as $cat) {
            $generated_tree[] = array(
                'id_category' => $cat['id_category'],
                'name' => self::getHiphenString((int)$cat['level_depth']) . $cat['name'],
            );
            if (isset($cat['children']) && is_array($cat['children']) && count($cat['children']) > 0) {
                self::generateCategoryTree($cat['children'], $generated_tree);
            }
        }
    }

    public static function getHiphenString($depth = 0)
    {
        $str = '';
        if ($depth == 1) {
            return $str;
        }

        for ($i = 0; $i < $depth; $i++) {
            $str .= '-';
        }
        return $str;
    }

    public static function getApporvalStatus($key = null)
    {
        $module = Module::getInstanceByName('kbmarketplace');
        $tmp = array(
            self::APPROVAL_WAITING => $module->l('Waiting for Approval', 'kbglobal'),
            self::APPROVED => $module->l('Approved', 'kbglobal'),
            self::DISSAPPROVED => $module->l('Disapproved', 'kbglobal'),
            self::CANCELLED => $module->l('Cancelled', 'kbglobal')
        );
        if ($key !== null && $key !== '') {
            if (isset($tmp[$key])) {
                return $tmp[$key];
            } else {
                return '';
            }
        } else {
            return $tmp;
        }
    }

    public static function getStatuses()
    {
        $module = Module::getInstanceByName('kbmarketplace');
        return array(
            self::DISABLE => $module->l('Disable', 'kbglobal'),
            self::ENABLE => $module->l('Enable', 'kbglobal')
        );
    }

    public static function convertRatingIntoPercent($overall_rating = 0)
    {
        return (float)((float)($overall_rating / (int)self::MAX_RATING) * 100);
    }

    public static function makeParentToChildCategoryStr(
        $category_w_parents_array = array(),
        $id_lang = null,
        $include_home = false
    ) {
        $root_category = Category::getRootCategory($id_lang);

        $str = '';

        if (count($category_w_parents_array) > 0) {
            foreach ($category_w_parents_array as $c) {
                if ($c['id_category'] == $root_category->id && !$include_home) {
                    continue;
                } else {
                    $str .= $c['name'] . ' >> ';
                }
            }
        }

        return rtrim($str, ' >> ');
    }

    public static function getBaseLink($ssl = null, $id_shop = null)
    {
        static $force_ssl = null;
        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = Context::getContext()->shop;
        }

        $base = (($ssl && Configuration::get('PS_SSL_ENABLED'))
            ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain);

        return $base . $shop->getBaseURI();
    }
    
    public static function getSellerLink($seller, $alias = null, $id_lang = null, $id_shop = null, $force_routes = false)
    {
        $context = Context::getContext();
        if (!(bool)Configuration::get('PS_REWRITING_SETTINGS')) {
            $id = 0;
            if (!is_object($seller)) {
                if (is_array($seller) && isset($seller['id_seller'])) {
                    $id = $seller['id_seller'];
                } elseif ((int)$seller) {
                    $id = $seller;
                } else {
                    $module = Module::getInstanceByName('kbmarketplace');
                    throw new PrestaShopException($module->l('Invalid seller vars', 'kbglobal'));
                }
            }

            return $context->link->getModuleLink(
                'kbmarketplace',
                'sellerfront',
                array('render_type' => 'sellerview', 'id_seller' => $id)
            );
        }
        $dispatcher = Dispatcher::getInstance();

        if (!$id_lang) {
            $id_lang = $context->language->id;
        }
        
        $lang_link = '';
        
        if (Language::isMultiLanguageActivated($id_shop) && (int)Configuration::get('PS_REWRITING_SETTINGS', null, null, $id_shop)) {
            $lang_link = Language::getIsoById($id_lang).'/';
        }

        $url = self::getBaseLink((bool)Configuration::get('PS_SSL_ENABLED'), $id_shop).$lang_link;

        if (!is_object($seller)) {
            if (is_array($seller) && isset($seller['id_seller'])) {
                $seller = new KbSeller($seller['id_seller'], $id_lang);
            } elseif ((int)$seller) {
                $seller = new KbSeller($seller, $id_lang);
            } else {
                $module = Module::getInstanceByName('kbmarketplace');
                throw new PrestaShopException($module->l('Invalid seller vars', 'kbglobal'));
            }
        }
        if (empty($seller->profile_url) && empty($alias)) {
            return $context->link->getModuleLink(
                'kbmarketplace',
                'sellerfront',
                array('render_type' => 'sellerview', 'id_seller' => $seller->id)
            );
        }

        // Set available keywords
        $params = array();
        $params['id'] = $seller->id;
        $params['rewrite'] = (!$alias) ? $seller->profile_url : $alias;

        $params['meta_keywords'] =    Tools::str2url($seller->meta_keyword);
        $params['meta_title'] = Tools::str2url($seller->title);

        return $url.$dispatcher->createUrl('kb_seller_rule', $id_lang, $params, $force_routes, '', $id_shop);
    }
}

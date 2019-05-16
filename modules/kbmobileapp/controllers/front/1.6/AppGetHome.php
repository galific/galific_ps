<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * API to get data of home page
 * called from home page in APP
 */

require_once 'AppCore.php';

class AppGetHome extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->setListPagingData();
        
        $this->updateLanguageFileRecords();

        $this->content['install_module'] = '';
        $this->content['elements'] = $this->getelementdata();
//        $this->content['topslider'] = $this->getTopSliders();
//        $this->content['topbanner'] = $this->getTopBanners();
//        $this->content['fproducts'] = $this->getFeaturedProducts();
//        $this->content['sproducts'] = $this->getSpecialProducts();
//        $this->content['bsproducts'] = $this->getBestSellerProducts();
//        $this->content['newproducts'] = $this->getNewProductsList();
        
        $this->content['languages'] = $this->getLanguagesDataForHome();
        $this->content['currencies'] = $this->getCurrenciesDataForHome();
        $this->content['languages_record'] = $this->returnLanguageRecordAsArray();
        /* changes for adding spin an dwin module compatibility
         * @author :Aayushi Agarwal
         * DOM : 1/12/2018
         */
        $spin_win_response = array();
        //$spin_win_response['is_spin_and_win_enabled'] = true;
        $spin_win_response['maximum_display_frequency'] = "0";
        $spin_win_response['wheel_display_interval'] = "30";
        if (Module::isInstalled('spinwheel')) {
            $email = Tools::getValue('email', '');
            $config = Tools::unserialize(Configuration::get('SPIN_WHEEL'));
            $lang = $this->context->language->id;
            $spin_win_response['is_spin_and_win_enabled'] = true;
            $spin_win_response['maximum_display_frequency'] = "0";
            $spin_win_response['wheel_display_interval'] = "30";
            if ($config['enable'] == 1) {
                $current_client_time = strtotime(date('Y-m-d H:i:s'));
                if (($config["fix_time"] == 0) || ($config["fix_time"] == 1 && $current_client_time >= strtotime($config["active_date"]) && $current_client_time <= strtotime($config["expire_date"]))) {
                    if ($config['who_to_show'] == 2 && $email != '') {
                        $spin_win_response['is_spin_and_win_enabled'] = false;
                    } elseif ($config['who_to_show'] == 3 && $email == '') {
                        $spin_win_response['is_spin_and_win_enabled'] = false;
                    }
                } else {
                    $spin_win_response['is_spin_and_win_enabled'] = false;
                }
            } else {
                $spin_win_response['is_spin_and_win_enabled'] = false;
            }
            $spin_win_response['maximum_display_frequency'] = "0";
            if (isset($config['max_display_freq'])) {
                if ($config['max_display_freq'] == 2) {  //hour
                    $spin_win_response['maximum_display_frequency'] = "0.04";
                } elseif ($config['max_display_freq'] == 3) {  //day
                    $spin_win_response['maximum_display_frequency'] = "1";
                } elseif ($config['max_display_freq'] == 4) {  //week
                    $spin_win_response['maximum_display_frequency'] = "7";
                } elseif ($config['max_display_freq'] == 5) {  //month
                    $spin_win_response['maximum_display_frequency'] = '30';
                } else {
                    $spin_win_response['maximum_display_frequency'] = "0";
                }
            }
            $spin_win_response['wheel_display_interval'] = $config['display_interval'];
        } else {
            $spin_win_response['is_spin_and_win_enabled'] = true;
        }
        if (Configuration::get('KB_MOBILE_APP_SPIN_WIN') == 0) {
            $spin_win_response['is_spin_and_win_enabled'] = false;
        }
        $this->content['spin_win_response'] = $spin_win_response;
        /* changes over */
        if (!Module::isInstalled('blockwishlist') || !Module::isEnabled('blockwishlist')) {
            $this->content['wishlist_active'] = "0";
        } else {
            $this->content['wishlist_active'] = "1";
        }
        /*start:Changes made by aayushi on 1 dec 2018 to send key add_to_cart_redirect_enabled value*/
        if (Configuration::get('KB_MOBILE_APP_CART_OPTION_REDIRECT') == 1) {
            $this->content['add_to_cart_redirect_enabled'] = "1";
        } else {
             $this->content['add_to_cart_redirect_enabled'] = "0";
        }
        if (Configuration::get('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH') == 1) {
            $this->content['display_logo_on_title_bar'] = "1";
            //$this->content['title_bar_logo_url'] = "data:image/png;base64,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";
            $this->content['title_bar_logo_url']=Configuration::get('KB_MOBILE_APP_ADD_LOGO_NAVIGATION');
            /*:Changes made by aayushi on 1 dec 2018 to send key add_to_cart_redirect_enabled value*/
        } else {
            $this->content['display_logo_on_title_bar'] = "0";
            $this->content['title_bar_logo_url'] = '';
        }
        /*end*/

        if (Tools::getIsset('session_data')) {
            $this->getCartId();
        }

        //To get cms content
        $this->getCmsPagesLink();
        $this->content['Menu_Categories'] = $this->getMenus();

        /* To know marketplace is enabled or not */
        $this->content['is_marketplace'] = ($this->isMarketplaceEnable()) ? '1' : '0';
        // chnages by rishabh jain
        $button_color = Configuration::get('KBMOBILEAPP_APP_BUTTON_COLOR');
        $theme_color = Configuration::get('KBMOBILEAPP_APP_THEME_COLOR');
        if ($button_color == '') {
            $button_color = '#F65200';
        }
        if ($theme_color == '') {
            $theme_color = '#262E30';
        }
        $button_color_array = explode('#', $button_color);
        $theme_color_array = explode('#', $theme_color);
        $this->content['app_button_color']= $button_color_array[1];
        $this->content['app_theme_color']= $theme_color_array[1];
        return $this->fetchJSONContent();
    }

    /**
     * Get Top sliders data
     *
     * @return array slider data
     */
    public function getTopSliders()
    {
        $data = array();
        $data['title'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Top Sliders'),
            'AppGetHome'
        );
        
        $slider_text = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Slider'),
            'AppGetHome'
        );
        $data['slides'] = array();

        $results = array();
        $slides = array();

        
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_sliders_banners where type = "slider" AND status = 1';

        if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query)) {
            if (!empty($results)) {
                foreach ($results as $res) {
                    if ($res['redirect_activity'] == 'product') {
                        $target_id = $res['product_id'];
                    } else if ($res['redirect_activity'] == 'category') {
                        $target_id = $res['category_id'];
                    } else {
                        $target_id = '';
                    }
                    $slides[] = array(
                        'click_target' => $res['redirect_activity'],
                        'target_id' => $target_id,
                        'title' => $slider_text.$res['kb_banner_id'],
                        //start: changes made by aayushi on 13 Nov 2018 to resolve the issue of editing banner image
                        //'src' => $res['image_url']."?".time()
                        'src' => $res['image_url'].'?time=' . time()
                        //end: changes made by aayushi on 13 Nov 2018 to resolve the issue of editing banner image
                        
                    );
                }
                $data['slides'] = $slides;
                return $data;
            } else {
                $this->writeLog('No top slider images found.');
            }
        } else {
            $this->writeLog('Error in fetching top slider data.');
        }

        return $data;
    }

    /**
     * Get Top banners data
     *
     * @return array banners data
     */
    public function getTopBanners()
    {
        $data = array();
        $data['title'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Top Banners'),
            'AppGetHome'
        );
        
        $banner_txt = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Banner'),
            'AppGetHome'
        );
        $data['banners'] = array();


        $results = array();
        $slides = array();
        
        $qry = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_sliders_banners where type = "banner" AND status = 1';

        if ($results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry)) {
            if (!empty($results)) {
                foreach ($results as $res) {
                    if ($res['redirect_activity'] == 'product') {
                        $target_id = $res['product_id'];
                    } else if ($res['redirect_activity'] == 'category') {
                        $target_id = $res['category_id'];
                    } else {
                        $target_id = '';
                    }
                    $slides[] = array(
                        'click_target' => $res['redirect_activity'],
                        'target_id' => $target_id,
                        'title' => $banner_txt.$res['kb_banner_id'],
                        //start: changes made by aayushi on 13 Nov 2018 to resolve the issue of editing banner image
                        //'src' => $res['image_url']."?".time()
                        'src' => $res['image_url'].'?time=' . time()
                        //end: changes made by aayushi on 13 Nov 2018 to resolve the issue of editing banner image
                    );
                }
                $data['banners'] = $slides;
                return $data;
            } else {
                $this->writeLog('No top banner images found.');
            }
        } else {
            $this->writeLog('Error in fetching top banners data.');
        }
        return $data;
    }

    /**
     * Get featured product data
     *
     * @return array featured product data
     */
    public function getFeaturedProducts($number_of_products, $image_content_mode)
    {
        $module_name = 'ps_featuredproducts';
        $data = array();
        if (!Module::isInstalled($module_name) || !Module::isEnabled($module_name)) {
            $this->writeLog('Featured product module is either inactive or not installed.');
            return $data;
        }
        $results = array();
        $products = array();
        $category = new Category((int) Configuration::get('HOME_FEATURED_CAT'), (int) $this->context->language->id);
        if (Validate::isLoadedObject($category)) {
            $nb = (int) Configuration::get('HOME_FEATURED_NBR');
            if (Configuration::get('HOME_FEATURED_RANDOMIZE')) {
                if (!$results = $category->getProducts(
                    (int) $this->context->language->id,
                    1,
                    ($nb ? $nb : 8),
                    null,
                    null,
                    false,
                    true,
                    true,
                    ($nb ? $nb : 8)
                )) {
                    $this->writeLog('No product found in featured product section.');
                    return $data;
                }
            } else {
                if (!$results = $category->getProducts(
                    (int) $this->context->language->id,
                    1,
                    ($nb ? $nb : 8),
                    'position'
                )) {
                    $this->writeLog('No product found in featured product section.');
                    return $data;
                }
            }

            if (!empty($results)) {
                $index = 0;
                foreach ($results as $res) {
                    $products[$index] = array(
                        'id' => $res['id_product'],
                        'is_in_wishlist' => $this->isProductHasInWishlist($res['id_product']),
                        'name' => $res['name'],
                        'available_for_order' => $res['available_for_order'],
                        'show_price' => $res['show_price'],
                        'new_products' => (isset($res['new']) && $res['new'] == 1) ? "1" : "0",
                        'on_sale_products' => $res['on_sale'],
                        'category_name' => 'Featured Products',
                        // changes by rishabh jain
                        'image_contentMode' => $image_content_mode,
                        // changes over
                        'ClickActivityName' => 'CategoryProductsActivity',
                        'category_id' => $res['id_category_default'],
                        'price' => $this->formatPrice($res['price_without_reduction']),
                    );
                    /* Changes started by rishabh jain on 3rd sep 2018
                    * Added urlencode perimeter in image link if enabled by admin
                    */
                    if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                        $products[$index]['src']  =  $this->context->link->getImageLink(
                            urlencode($res['link_rewrite']),
                            $res['id_image'],
                            $this->getImageType('large')
                        );
                    } else {
                        $products[$index]['src']  =  $this->context->link->getImageLink(
                            $res['link_rewrite'],
                            $res['id_image'],
                            $this->getImageType('large')
                        );
                    }
                    /* Changes over */

                    if (count($res['specific_prices']) > 0) {
                        $products[$index]['discount_price'] = $this->formatPrice($res['price']);
                        if ($res['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                            $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                            $products[$index]['discount_percentage'] = $temp_price;
                            unset($temp_price);
                        } else {
                            if ($res['price_without_reduction']) {
                                $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                                $percent = (float) ($temp_price / $res['price_without_reduction']);
                                unset($temp_price);
                            } else {
                                $percent = 0;
                            }
                            $products[$index]['discount_percentage'] = Tools::ps_round($percent);
                        }
                    } else {
                        $products[$index]['discount_price'] = '';
                        $products[$index]['discount_percentage'] = '';
                    }
                    $index++;
                }
                $data = $products;
            } else {
                $this->writeLog('No product found in featured product section.');
            }
        } else {
            $this->writeLog('Featured category is not found.');
        }
        return $data;
    }

    public function getProducts($product_list, $number_of_products, $image_content_mode)
    {
        $data = array();
        $results = array();
        $products = array();
        
        if (!Configuration::get('PS_CATALOG_MODE')) {
            if ($results = $this->getCustomProducts(
                (int) $this->context->language->id,
                $product_list,
                0,
                $number_of_products,
                false,
                null,
                null,
                false,
                false,
                $this->context
            )) {
                if (!empty($results)) {
                    $index = 0;
                    foreach ($results as $res) {
                        $products[$index] = array(
                            'id' => $res['id_product'],
                            'is_in_wishlist' => $this->isProductHasInWishlist($res['id_product']),
                            'name' => $res['name'],
                            'available_for_order' => $res['available_for_order'],
                            'show_price' => $res['show_price'],
                            'new_products' => (isset($res['new']) && $res['new'] == 1) ? "1" : "0",
                            'on_sale_products' => $res['on_sale'],
                            'category_name' => 'Special Products',
                            // changes by rishabh jain
                            'image_contentMode' => $image_content_mode,
                            // changes over
                            'ClickActivityName' => 'CategoryProductsActivity',
                            'category_id' => $res['id_category_default'],
                            'price' => $this->formatPrice($res['price_without_reduction']),
                            'src' => $this->context->link->getImageLink(
                                urlencode($res['link_rewrite']),
                                $res['id_image'],
                                $this->getImageType('large')
                            )
                        );
                        if (count($res['specific_prices']) > 0) {
                            $products[$index]['discount_price'] = $this->formatPrice($res['price']);
                            if ($res['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                                $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                                $products[$index]['discount_percentage'] = $temp_price;
                                unset($temp_price);
                            } else {
                                if ($res['price_without_reduction']) {
                                    $temp_price = (float) ($res['specific_prices']['reduction'] * 100);
                                    $percent = (float) ($temp_price / $res['price_without_reduction']);
                                    unset($temp_price);
                                } else {
                                    $percent = 0;
                                }
                                $products[$index]['discount_percentage'] = Tools::ps_round($percent);
                            }
                        } else {
                            $products[$index]['discount_price'] = '';
                            $products[$index]['discount_percentage'] = '';
                        }
                        $index++;
                    }
                    $data = $products;
                } else {
                    $this->writeLog('Special Product Module - Product not found.');
                }
            } else {
                $this->writeLog('Special product module - Not able to get special products.');
            }
        } else {
            $this->writeLog('Special product module - Catalog mode is not active.');
        }

        return $data;
    }
    
    public static function getCustomProducts($id_lang, $product_array, $page_number = 0, $nb_products = 10, $count = false, $order_by = null, $order_way = null, $beginning = false, $ending = false, Context $context = null)
    {
        $filters = array();
        $only_count = false;
        $start = null;
        $limit = $nb_products;
        $orderby = null;
        $orderway = null;
        $alias_where = 'p';
        $order_by = 'price';
        $order_way = 'DESC';
        $product_list = '';
        $product_list = implode(',', $product_array);
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $alias_where = 'product_shop';
        }
        $columns = 'p.*, product_shop.*, product_shop.id_category_default, pl.*,
			MAX(image_shop.`id_image`) id_image, il.legend, m.name manufacturer_name,
			MAX(product_attribute_shop.id_product_attribute) id_product_attribute,
			DATEDIFF(product_shop.`date_add`, DATE_SUB(NOW(), INTERVAL 20 DAY)) > 0 AS new, stock.out_of_stock,
			IFNULL(stock.quantity, 0) as quantity';

        $query = 'SELECT {{COLUMNS}} FROM `'._DB_PREFIX_.'product` p';
        if (isset($filters['id_category']) && (int) $filters['id_category'] > 0) {
            $query .= 'INNER JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
			LEFT JOIN '._DB_PREFIX_.'category c ON (c.id_category = cp.id_category) ';
        }


        $query .= Shop::addSqlAssociation('product', 'p')
                .Product::sqlStock('p', null, false, Context::getContext()->shop).'
			LEFT JOIN '._DB_PREFIX_.'product_lang pl
			ON (pl.id_product = p.id_product'.Shop::addSqlRestrictionOnLang('pl')
                .' AND pl.id_lang = '.(int) $id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'image` i ON (i.`id_product` = p.`id_product`)
			'.Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1').'
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image`
			AND il.`id_lang` = '.(int) $id_lang.')
			LEFT JOIN '._DB_PREFIX_.'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
			LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
			'.Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1')
                .' WHERE p.id_product IN ('.$product_list.') AND '.pSQL($alias_where).'.`active` = 1
			AND '.pSQL($alias_where).'.`visibility` IN ("both", "catalog") ';

        $filter_conditions = '';
        if (isset($filters['id_category']) && (int) $filters['id_category'] > 0) {
            $filter_conditions .= ' AND c.id_category = '.(int) $filters['id_category'].' AND c.active = 1';
        }

        $query .= pSQL($filter_conditions);

        if (is_array($filters) && count($filters) > 0) {
            foreach ($filters as $key => $filter) {
                if ($key != 'id_category') {
                    $filter_conditions .= ' AND '.pSQL($filter);
                }
            }
        }

        $query .= ' GROUP BY '.pSQL($alias_where).'.id_product';

        if ($only_count) {
            $query = str_replace('{{COLUMNS}}', 'COUNT(p.id_product) as total', $query);
            $rows  = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
            return count($rows);
        } else {
            $query = str_replace('{{COLUMNS}}', $columns, $query);
            if ($orderby != null && $orderway != null) {
                $query .= ' ORDER BY '.pSQL($orderby).' '.pSQL($orderway);
            } else {
                $query .= ' ORDER BY '.pSQL($alias_where).'.id_product DESC';
            }

            if ((int) $start > 0 && (int) $limit > 0) {
                $query .= ' LIMIT '.(int) $start.','.(int) $limit;
            } elseif ((int) $limit > 0) {
                $query .= ' LIMIT '.(int) $limit;
            }

            return DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        }
    }
    /**
     * Get special product data
     *
     * @return array special product data
     */
    public function getSpecialProducts($number_of_products, $image_content_mode)
    {
        $data = array();

        $results = array();
        $products = array();
        
        if (!Configuration::get('PS_CATALOG_MODE')) {
            if ($results = Product::getPricesDrop(
                (int) $this->context->language->id,
                0,
                (int) $number_of_products,
                false,
                null,
                null,
                false,
                false,
                $this->context
            )) {
                if (!empty($results)) {
                    $index = 0;
                    foreach ($results as $res) {
                        $products[$index] = array(
                            'id' => $res['id_product'],
                            'is_in_wishlist' => $this->isProductHasInWishlist($res['id_product']),
                            'name' => $res['name'],
                            'available_for_order' => $res['available_for_order'],
                            'show_price' => $res['show_price'],
                            'new_products' => (isset($res['new']) && $res['new'] == 1) ? "1" : "0",
                            'on_sale_products' => $res['on_sale'],
                            'category_name' => 'Special Products',
                            // changes by rishabh jain
                            'image_contentMode' => $image_content_mode,
                            // changes over
                            'ClickActivityName' => 'CategoryProductsActivity',
                            'category_id' => $res['id_category_default'],
                            'price' => $this->formatPrice($res['price_without_reduction']),
                            
                        );
                        /* Changes started by rishabh jain on 3rd sep 2018
                        * Added urlencode perimeter in image link if enabled by admin
                        */
                        if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                            $products[$index]['src']  =  $this->context->link->getImageLink(
                                urlencode($res['link_rewrite']),
                                $res['id_image'],
                                $this->getImageType('large')
                            );
                        } else {
                            $products[$index]['src']  =  $this->context->link->getImageLink(
                                $res['link_rewrite'],
                                $res['id_image'],
                                $this->getImageType('large')
                            );
                        }
                        /* Changes over */

                        if (count($res['specific_prices']) > 0) {
                            $products[$index]['discount_price'] = $this->formatPrice($res['price']);
                            if ($res['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                                $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                                $products[$index]['discount_percentage'] = $temp_price;
                                unset($temp_price);
                            } else {
                                if ($res['price_without_reduction']) {
                                    $temp_price = (float) ($res['specific_prices']['reduction'] * 100);
                                    $percent = (float) ($temp_price / $res['price_without_reduction']);
                                    unset($temp_price);
                                } else {
                                    $percent = 0;
                                }
                                $products[$index]['discount_percentage'] = Tools::ps_round($percent);
                            }
                        } else {
                            $products[$index]['discount_price'] = '';
                            $products[$index]['discount_percentage'] = '';
                        }
                        $index++;
                    }
                    $data = $products;
                } else {
                    $this->writeLog('Special Product Module - Product not found.');
                }
            } else {
                $this->writeLog('Special product module - Not able to get special products.');
            }
        } else {
            $this->writeLog('Special product module - Catalog mode is not active.');
        }

        return $data;
    }

    /**
     * Get Best seller products data
     *
     * @return array best seller product data
     */
    public function getBestSellerProducts($number_of_products, $image_content_mode)
    {
        $data = array();
//        if (!Module::isInstalled($module_name) || !Module::isEnabled($module_name)) {
//            $this->writeLog('Bestseller module is either inactive or not installed.');
//            return $data;
//        }

        $results = array();
        $products = array();
        if (!Configuration::get('PS_CATALOG_MODE')) {
            if ($results = ProductSale::getBestSalesLight(
                (int) $this->context->language->id,
                0,
                (int) $number_of_products
            )) {
                if (!empty($results)) {
                    $index = 0;
                    foreach ($results as $res) {
                        $products[$index] = array(
                            'id' => $res['id_product'],
                            'is_in_wishlist' => $this->isProductHasInWishlist($res['id_product']),
                            'name' => $res['name'],
                            'available_for_order' => $res['available_for_order'],
                            'show_price' => $res['show_price'],
                            'new_products' => (isset($res['new']) && $res['new'] == 1) ? "1" : "0",
                            'on_sale_products' => $res['on_sale'],
                            'category_name' => 'Best Seller Products',
                            'ClickActivityName' => 'CategoryProductsActivity',
                            // changes by rishabh jain
                            'image_contentMode' => $image_content_mode,
                            // changes over
                            'category_id' => $res['id_category_default'],
                            'price' => $this->formatPrice($res['price_without_reduction']),
                            
                        );
                        /* Changes started by rishabh jain on 3rd sep 2018
                        * Added urlencode perimeter in image link if enabled by admin
                        */
                        if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                            $products[$index]['src']  =  $this->context->link->getImageLink(
                                urlencode($res['link_rewrite']),
                                $res['id_image'],
                                $this->getImageType('large')
                            );
                        } else {
                            $products[$index]['src']  =  $this->context->link->getImageLink(
                                $res['link_rewrite'],
                                $res['id_image'],
                                $this->getImageType('large')
                            );
                        }
                        /* Changes over */
                        if (count($res['specific_prices']) > 0) {
                            $products[$index]['discount_price'] = $this->formatPrice($res['price']);
                            if ($res['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                                $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                                $products[$index]['discount_percentage'] = $temp_price;
                                unset($temp_price);
                            } else {
                                if ($res['price_without_reduction']) {
                                    $temp_price = ((float) $res['specific_prices']['reduction'] * 100);
                                    $percent = (float) ($temp_price / $res['price_without_reduction']);
                                    unset($temp_price);
                                } else {
                                    $percent = 0;
                                }
                                $products[$index]['discount_percentage'] = Tools::ps_round($percent);
                            }
                        } else {
                            $products[$index]['discount_price'] = '';
                            $products[$index]['discount_percentage'] = '';
                        }
                        $index++;
                    }
                    $data = $products;
                } else {
                    $this->writeLog('Bestseller Module - Product not found.');
                }
            } else {
                $this->writeLog('Bestseller module - Not able to get best seller products.');
            }
        } else {
            $this->writeLog('Bestseller module - Catalog mode is not active.');
        }

        return $data;
    }

    /**
     * Get new products data
     *
     * @return array new products data
     */
    public function getNewProductsList($number_of_products, $image_content_mode)
    {
        $data = array();
        $results = array();
        $products = array();

        if (!Configuration::get('NEW_PRODUCTS_NBR')) {
            $this->writeLog('New Arrvial module - NBR Configuration is missing.');
            return $data;
        }

        if (!Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) {
            $this->writeLog('New Arrvial module - NB Days is missing.');
            return $data;
        }

        if ($results = Product::getNewProducts(
            (int) $this->context->language->id,
            0,
            (int) $number_of_products
        )) {
            if (!empty($results)) {
                $index = 0;
                foreach ($results as $res) {
                    $products[$index] = array(
                        'id' => $res['id_product'],
                        'is_in_wishlist' => $this->isProductHasInWishlist($res['id_product']),
                        'name' => $res['name'],
                        'available_for_order' => $res['available_for_order'],
                        'show_price' => $res['show_price'],
                        'new_products' => (isset($res['new']) && $res['new'] == 1) ? "1" : "0",
                        'on_sale_products' => $res['on_sale'],
                        'category_name' => 'New Arrivals',
                        // changes by rishabh jain
                        'image_contentMode' => $image_content_mode,
                        // changes over
                        'ClickActivityName' => 'CategoryProductsActivity',
                        'category_id' => $res['id_category_default'],
                        'price' => $this->formatPrice($res['price_without_reduction']),
                    );/* Changes started by rishabh jain on 3rd sep 2018
                    * Added urlencode perimeter in image link if enabled by admin
                    */
                    if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                        $products[$index]['src']  =  $this->context->link->getImageLink(
                            urlencode($res['link_rewrite']),
                            $res['id_image'],
                            $this->getImageType('large')
                        );
                    } else {
                        $products[$index]['src']  =  $this->context->link->getImageLink(
                            $res['link_rewrite'],
                            $res['id_image'],
                            $this->getImageType('large')
                        );
                    }
                    
                    if (count($res['specific_prices']) > 0) {
                        $products[$index]['discount_price'] = $this->formatPrice($res['price']);
                        if ($res['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                            $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                            $products[$index]['discount_percentage'] = $temp_price;
                            unset($temp_price);
                        } else {
                            if ($res['price_without_reduction']) {
                                $temp_price = (float) ($res['specific_prices']['reduction'] * 100);
                                $percent = (float) ($temp_price / $res['price_without_reduction']);
                                unset($temp_price);
                            } else {
                                $percent = 0;
                            }
                            $products[$index]['discount_percentage'] = Tools::ps_round($percent);
                        }
                    } else {
                        $products[$index]['discount_price'] = '';
                        $products[$index]['discount_percentage'] = '';
                    }
                    $index++;
                }
                $data['products'] = $products;
            } else {
                $this->writeLog('Bestseller Module - Product not found.');
            }
        } else {
            $this->writeLog('Bestseller module - Not able to get best seller products.');
        }

        return $products;
    }

    /**
     * Get Menu List
     *
     * @return array menu data
     */
    public function getMenus()
    {
        $pattern = '/^([A-Z_]*)[0-9]+/';

        $groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
        $menu_items = $this->getMenuItems();
        $index = 0;
        $categories = array();
        foreach ($menu_items as $item) {
            if (!$item) {
                continue;
            }

            preg_match($pattern, $item, $value);
            $id = (int) Tools::substr($item, Tools::strlen($value[1]), Tools::strlen($item));

            $active = Group::isFeatureActive();
            switch (Tools::substr($item, 0, Tools::strlen($value[1]))) {
                case 'CAT':
                    $qry = 'SELECT c.id_category, c.id_parent, cl.name
                            FROM `' . _DB_PREFIX_ . 'category` c
                            ' . Shop::addSqlAssociation('category', 'c') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON
                            c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
                            ' . (isset($groups) && $active ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group`
                            cg ON c.`id_category` = cg.`id_category`' : '') . ' 
                            RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON c2.`id_category` = ' . (int) $id . '
                            AND c.`nleft` >= c2.`nleft` AND c.`nright` <= c2.`nright` 
                            WHERE 1 AND `id_lang` = ' . (int) $this->context->language->id . '
                             AND c.`active` = 1 
                            ' . (isset($groups)
                                && $active ? ' AND cg.`id_group` IN (' . pSQL(implode(',', $groups)) . ')' : '') . '
                            ' . (!$this->context->language->id
                                || (isset($groups) && $active) ? ' GROUP BY c.`id_category`' : '') . '
                             ORDER BY c.`level_depth` ASC, category_shop.`position` ASC';

                    $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);

                    if (!empty($results)) {
                        foreach ($results as $m) {
                            if ($m['id_category'] == $id) {
                                $categories[$index] = array(
                                    'id' => $m['id_category'],
                                    'name' => $m['name'],
                                    'second_children' => $this->getSubChild($m['id_category'], $results)
                                );
                                $index++;
                            }
                        }
                    }
                    break;
            }
        }
        return $categories;
    }

    /**
     * Get Menu subchild
     *
     * @param int $id_parent parent id of menu
     * @param array $categories categoryies data
     * @param bool $further_child
     * @return array sub child data
     */
    public function getSubChild($id_parent, $categories, $further_child = true)
    {
        $index = 0;
        $sub_categories = array();
        foreach ($categories as $cat) {
            if ($cat['id_parent'] == $id_parent) {
                $sub_categories[$index] = array(
                    'id' => $cat['id_category'],
                    'name' => $cat['name']
                );
                if ($further_child) {
                    /* Start changes by Aayushi Agarwal on 1 DEC 2018
                     * Updated the index of $sub_categories from "third_children" to 'children'
                     * and further_child = true to show more level of children
                     */
                    $sub_categories[$index]['children'] = $this->getSubChild(
                        $cat['id_category'],
                        $categories,
                        true
                    );
                    /*end:changes*/
                }
                $index++;
            }
        }
        return $sub_categories;
    }

    /**
     * Get Menu Items
     *
     * @return array menu items data
     */
    public function getMenuItems()
    {
        $items = Tools::getValue('items');
        if (is_array($items) && count($items)) {
            return $items;
        } else {
            $shops = Shop::getContextListShopID();
            $conf = null;

            if (count($shops) > 1) {
                foreach ($shops as $key => $shop_id) {
                    $shop_group_id = Shop::getGroupFromShop($shop_id);
                    $conf .= (string) ($key > 1 ? ',' : '') . Configuration::get(
                        'MOD_BLOCKTOPMENU_ITEMS',
                        null,
                        $shop_group_id,
                        $shop_id
                    );
                }
            } else {
                $shop_id = (int) $shops[0];
                $shop_group_id = Shop::getGroupFromShop($shop_id);
                $conf = Configuration::get('MOD_BLOCKTOPMENU_ITEMS', null, $shop_group_id, $shop_id);
            }

            if (Tools::strlen($conf)) {
                return explode(',', $conf);
            } else {
                return array();
            }
        }
    }

    /**
     * Get cart id of customer for cart persistance
     *
     */
    public function getCartId()
    {
        $cart_id = Tools::getValue('session_data', 0);
        $cart = new Cart($cart_id);
        if (!Validate::isLoadedObject($cart)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Unable to load cart'),
                'AppGetHome'
            );
            $this->writeLog('Unable to load cart');
        } else {
            $order_id = Order::getOrderByCartId($cart->id);
            if ($order_id) {
                $this->context->cart->id_currency = $this->context->currency->id;
                $this->context->cart = new Cart();
                $this->context->cart->id_carrier = 0;
                $this->context->cart->setDeliveryOption(null);
                if ($this->context->customer->id > 0) {
                    $d_id = (int) Address::getFirstCustomerAddressId((int) ($this->context->customer->id));
                    $i_id = (int) Address::getFirstCustomerAddressId((int) ($this->context->customer->id));
                    $this->context->cart->id_address_delivery = $d_id;
                    $this->context->cart->id_address_invoice = $i_id;
                    $this->context->cart->id_customer = (int) $this->context->customer->id;
                    $this->context->cart->secure_key = $this->context->customer->secure_key;
                }
                $this->context->cart->id_currency = $this->context->currency->id;
                $this->context->cart->save();
                $this->context->cookie->id_cart = (int) $this->context->cart->id;
                $this->context->cookie->write();
                $this->context->cart->autosetProductAddress();
                $this->content['status'] = 'success';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Order created by this cart'),
                    'AppGetHome'
                );
                $this->content['cart_id'] = $this->context->cart->id;
                $this->writeLog('Order created by this cart');
            } else {
                $this->content['status'] = 'failure';
                $this->content['message'] = '';
            }
        }
    }

    /**
     * Get link of cms pages i.e contact us,about us and term and conditions page
     *
     */
    public function getelementdata()
    {
        $element_data = array();
        $layout_id = Configuration::get('KBMOBILEAPP_HOME_PAGE_LAYOUT');
        if ($layout_id == '') {
            $layout_id = 1;
        }
        $sql = 'Select id_component from ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_layout = '. (int) $layout_id .' order by position asc';
        $components = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (count($components) > 0) {
            $i = 0;
            foreach ($components as $key => $comp) {
                $sql = 'Select id_component_type from ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = '.  $comp['id_component'] ;
                $component_type_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getvalue($sql);
                $sql = 'Select component_name from ' . _DB_PREFIX_ . 'kbmobileapp_component_types where id = '.  $component_type_id ;
                $component_type = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                if ($component_type == 'top_category') {
                    $category_data = array();
                    $sql = 'SELECT *  FROM ' . _DB_PREFIX_ . 'kbmobileapp_top_category
                            where id_component = '.(int)$comp['id_component'];
                    $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                    if (is_array($categories)) {
                        if (count($categories) > 0) {
                            $element_data[$i]['element_type'] = 'categories_top';
                            $element_data[$i]['data'] = array();
                            if ($this->checkSecureUrl()) {
                                $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                                $http_string = 'https://';
                            } else {
                                $http_string = 'http://';
                                $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                            }
                            $category_array = array();
                            $category_array = explode('|', $categories[0]['id_category']);
                            $image_array = array();
                            $image_array = explode('|', $categories[0]['image_url']);
                            foreach ($category_array as $k => $value) {
                                if ($k <= count($category_array) && $value > 0) {
                                    $sql = 'SELECT name  FROM ' . _DB_PREFIX_ . 'category_lang
                                            where id_category = '.(int)$value .' And id_lang = '.(int)$this->context->language->id;
                                    $category_name = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                                    $data = array();
                                    $data['id'] = $value;
                                    if ($image_array[$k] != '') {
                                        $data['image_src'] = $module_dir . 'kbmobileapp/'.$image_array[$k];
                                    } else {
                                        $link = new link;
                                        $data['image_src'] = $link->getCatImageLink($category_name, $value);
                                        if ($data['image_src'] != '') {
                                            $data['image_src'] = $http_string.$data['image_src'];
                                        }
                                    }
                                    $data['image_contentMode'] = $categories[0]['image_content_mode'];
                                    $data['name'] = $category_name;
                                    $category_data[] = $data;
                                }
                            }
                            unset($data);
                            $element_data[$i]['data'] = $category_data;
                            $i++;
                        }
                    }
                } elseif ($component_type == 'banner_square') {
                    $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                    $banner_heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                    $banner_heading_array = Tools::unSerialize($banner_heading_data);
                    
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $banner_data = Db::getInstance()->executeS($sql);
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_square';
                            $element_data[$i]['heading'] =  '';
                            if (isset($banner_heading_array[$this->context->language->id])) {
                                $element_data[$i]['heading'] =  $banner_heading_array[$this->context->language->id];
                            }
                            $element_data[$i]['data'] = array();
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_contentMode'];
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                        }
                    }
                } elseif ($component_type == 'banners_grid') {
                    $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                    $banner_heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                    $banner_heading_array = Tools::unSerialize($banner_heading_data);
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $banner_data = Db::getInstance()->executeS($sql);
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_grid';
                            $element_data[$i]['heading'] =  '';
                            if (isset($banner_heading_array[$this->context->language->id])) {
                                $element_data[$i]['heading'] =  $banner_heading_array[$this->context->language->id];
                            }
                            $element_data[$i]['data'] = array();
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_contentMode'];
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                        }
                    }
                } elseif ($component_type == 'banners_countdown') {
                    $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                    $banner_heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                    $banner_heading_array = Tools::unSerialize($banner_heading_data);
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $banner_data = Db::getInstance()->executeS($sql);
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_countdown';
                            $element_data[$i]['heading'] =  '';
                            if (isset($banner_heading_array[$this->context->language->id])) {
                                $element_data[$i]['heading'] =  $banner_heading_array[$this->context->language->id];
                            }
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_contentMode'];
                                $data['upto_time'] = "".strtotime($bd['countdown']) - time()."";
                                $text_color = array();
                                $text_color = explode("#", $bd['text_color']);
                                $data['timer_text_color'] = $text_color[1];
                                
                                if ($bd['is_enabled_background_color'] == 1) {
                                    $background_color = array();
                                    $background_color = explode("#", $bd['background_color']);
                                    $data['timer_background_color'] = $background_color[1];
                                } else {
                                    $data['timer_background_color'] = '';
                                }
                                if (strtotime($bd['countdown']) > time()) {
                                    $square_banner_data[] = $data;
                                }
                            }
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                            unset($data);
                        }
                    }
                } elseif ($component_type == 'banner_horizontal_slider') {
                    $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                    $banner_heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                    $banner_heading_array = Tools::unSerialize($banner_heading_data);
                    $square_banner_data = array();
                    $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component =' . (int) $comp['id_component'];
                    $banner_data = Db::getInstance()->executeS($sql);
                    if (is_array($banner_data)) {
                        $number_of_entries = count($banner_data);
                        if ($number_of_entries > 0) {
                            $element_data[$i]['element_type'] = 'banners_horizontal_sliding';
                            $element_data[$i]['heading'] =  '';
                            if (isset($banner_heading_array[$this->context->language->id])) {
                                $element_data[$i]['heading'] =  $banner_heading_array[$this->context->language->id];
                            }
                            foreach ($banner_data as $k => $bd) {
                                $data = array();
                                $data['click_target'] = $bd['redirect_activity'];
                                if ($bd['redirect_activity'] == 'category') {
                                    $data['target_id'] = $bd['category_id'];
                                } else {
                                    $data['target_id'] = $bd['product_id'];
                                }
                                $data['src'] = $bd['image_url'];
                                $data['title'] = '';
                                $data['image_contentMode'] = $bd['image_contentMode'];
                                $square_banner_data[] = $data;
                            }
                            unset($data);
                            $element_data[$i]['data'] = $square_banner_data;
                            $i++;
                        }
                    }
                } elseif ($component_type == 'products_recent') {
                    $element_data[$i]['element_type'] = 'products_recent';
                    $element_data[$i]['heading'] =  $this->l('Recent Products');
                    $element_data[$i]['data'] = array();
                    $i++;
                } elseif ($component_type == 'products_grid') {
                    $products = array();
                    $products = $this->getProductsComponentData($comp['id_component']);
                    if (count($products) > 0) {
                        $element_data[$i]['element_type'] = $component_type;
                        $element_data[$i]['heading'] =  '';
                        $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                        $heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                        $heading_array = Tools::unSerialize($heading_data);
                        $element_data[$i]['heading'] =  '';
                        if (isset($heading_array[$this->context->language->id])) {
                            $element_data[$i]['heading'] =  $heading_array[$this->context->language->id];
                        }
                        $element_data[$i]['data'] = $products;
                        unset($products);
                        $i++;
                    }
                } elseif ($component_type == 'products_horizontal') {
                    $products = array();
                    $products = $this->getProductsComponentData($comp['id_component']);
                    if (count($products) > 0) {
                        $element_data[$i]['element_type'] = $component_type;
                        $element_data[$i]['heading'] =  '';
                        $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                        $heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                        $heading_array = Tools::unSerialize($heading_data);
                        $element_data[$i]['heading'] =  '';
                        if (isset($heading_array[$this->context->language->id])) {
                            $element_data[$i]['heading'] =  $heading_array[$this->context->language->id];
                        }
                        $element_data[$i]['data'] = $products;
                        unset($products);
                        $i++;
                    }
                } elseif ($component_type == 'products_square') {
                    $products = array();
                    $products = $this->getProductsComponentData($comp['id_component']);
                    if (count($products) > 0) {
                        $element_data[$i]['element_type'] = $component_type;
                        $element_data[$i]['heading'] =  '';
                        $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $comp['id_component'];
                        $heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                        $heading_array = Tools::unSerialize($heading_data);
                        $element_data[$i]['heading'] =  '';
                        if (isset($heading_array[$this->context->language->id])) {
                            $element_data[$i]['heading'] =  $heading_array[$this->context->language->id];
                        }
                        $element_data[$i]['data'] = $products;
                        unset($products);
                        $i++;
                    }
                }
            }
        }
        return $element_data;
    }
    public function getProductsComponentData($id_component)
    {
        $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_product_data where id_component =' . (int) $id_component;
        $product_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if (count($product_data) > 0) {
            $product_type = $product_data['product_type'];
            $number_of_products = $product_data['number_of_products'];
            $image_content_mode = $product_data['image_content_mode'];
            $products = array();
            if ($product_type == 'best_seller') {
                $products = $this->getBestSellerProducts($number_of_products, $image_content_mode);
            } elseif ($product_type == 'new_products') {
                $products = $this->getNewProductsList($number_of_products, $image_content_mode);
            } elseif ($product_type == 'featured_products') {
                $products = $this->getFeaturedProducts($number_of_products, $image_content_mode);
            } elseif ($product_type == 'special_products') {
                $products = $this->getSpecialProducts($number_of_products, $image_content_mode);
            } elseif ($product_type == 'category_products') {
                $product_list = array();
                $product_list = explode(',', $product_data['category_products']);
                $products = $this->getProducts($product_list, $number_of_products, $image_content_mode);
            } elseif ($product_type == 'custom_products') {
                $product_list = array();
                $product_list = explode(',', $product_data['custom_products']);
                $products = $this->getProducts($product_list, $number_of_products, $image_content_mode);
            }
            $sliced_product = array();
            $sliced_product = array_slice($products, 0, $number_of_products);
            return $sliced_product;
        }
    }
    public function getCmsPagesLink()
    {
        /*start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages*/
        $data=array();
        $saved_cms = Tools::unSerialize(Configuration::get('KB_ENABLED_CMS'));
        $context = Context::getContext();
        if (!empty($saved_cms)) {
            foreach ($saved_cms as $key => $value) {
                $link = $context->link->getCMSLink((int)$value);
                $link .= (parse_url($link, PHP_URL_QUERY) ? '&' : '?') . 'content_only=1';
                //$sql2 = 'select * from '._DB_PREFIX_.'cms_lang where id_cms='.(int)$value;
                $sql2 = 'select * from '._DB_PREFIX_.'cms_lang where id_lang='.(int) $this->context->language->id .' and id_cms='.(int)$value;
                $cms_data2 = Db::getInstance()->getRow($sql2);
                $data[$key] = array(
                    'name'=>$cms_data2['meta_title'],
                    'link'=>$link
                );
            }
        }
        /*end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages*/
        $this->content['cms_links'] = $data;
        $this->content['contact_us_available'] = "1";
        $contact_url = (_PS_VERSION_ >= 1.5) ? 'contact' : 'contact-form';
        $contact_link = $this->context->link->getPageLink($contact_url);
        $contact_link .= (parse_url($contact_link, PHP_URL_QUERY) ? '&' : '?') . 'content_only=1';
        $this->content['contact_us_link'] = $contact_link;
    }
    
    /*
     * Get Footer CMS titles
     * 
     * @return array
     */
    public function getCMSTitlesFooter()
    {
        $context = Context::getContext();
        $footerCms = Configuration::get('FOOTER_CMS');

        if (empty($footerCms)) {
            return array();
        }

        $cmsCategories = explode('|', $footerCms);
        
        $content = array();
        
        foreach ($cmsCategories as $cmsCategory) {
            $ids = explode('_', $cmsCategory);

            if ($ids[0] == 1 && isset($ids[1])) {
                $query = $this->getBlockName($ids[1]);
                $content[$cmsCategory]['link'] = $context->link->getCMSCategoryLink((int) $ids[1], $query['link_rewrite']);
                $content[$cmsCategory]['meta_title'] = $query['name'];
            } else if ($ids[0] == 0 && isset($ids[1])) {
                $query = $this->getCMSMetaTitle($ids[1]);
                $content[$cmsCategory]['link'] = $context->link->getCMSLink((int) $ids[1], $query['link_rewrite']);
                $content[$cmsCategory]['meta_title'] = $query['meta_title'];
            }
        }
        return $content;
    }

    /*
     * Get footer block name
     * 
     * @param int $id block category id
     * @return array
     */
    public function getBlockName($id)
    {
        $sql = 'SELECT cl.`name`, cl.`link_rewrite`
                FROM `'._DB_PREFIX_.'cms_category_lang` cl
                INNER JOIN `'._DB_PREFIX_.'cms_category` c
                ON (cl.`id_cms_category` = c.`id_cms_category`)
                WHERE cl.`id_cms_category` = '.(int)$id.'
                AND (c.`active` = 1 OR c.`id_cms_category` = 1)
                AND cl.`id_lang` = '.(int)Context::getContext()->language->id;

        return Db::getInstance()->getRow($sql);
    }
    
    /*
     * Function to get the cms page title
     *
     * @param int $id cms page id
     * @return array data of cms page 
     */
    public static function getCMSMetaTitle($id)
    {
        $context = Context::getContext();
        $id_shop = (int)$context->shop->id;

        $where_shop = '';
        if (Tools::version_compare(_PS_VERSION_, '1.6.0.12', '>=') == true && $id_shop != false) {
            $where_shop = ' AND cl.`id_shop` = '.(int)$id_shop;
        }

        $sql = 'SELECT cl.`meta_title`, cl.`link_rewrite`
                FROM `'._DB_PREFIX_.'cms_lang` cl
                INNER JOIN `'._DB_PREFIX_.'cms` c
                ON (cl.`id_cms` = c.`id_cms`)
                WHERE cl.`id_cms` = '.(int)$id.'
                AND (c.`active` = 1 OR c.`id_cms` = 1)'.
                $where_shop.'
                AND cl.`id_lang` = '.(int)Context::getContext()->language->id;

        return Db::getInstance()->getRow($sql);
    }
}

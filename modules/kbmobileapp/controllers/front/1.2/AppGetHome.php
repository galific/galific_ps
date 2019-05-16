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
        $this->content['topslider'] = $this->getTopSliders();
        $this->content['topbanner'] = $this->getTopBanners();
        $this->content['fproducts'] = $this->getFeaturedProducts();
        $this->content['sproducts'] = $this->getSpecialProducts();
        $this->content['bsproducts'] = $this->getBestSellerProducts();
        $this->content['newproducts'] = $this->getNewProducts();
        $this->content['languages'] = $this->getLanguagesDataForHome();
        $this->content['currencies'] = $this->getCurrenciesDataForHome();
        $this->content['languages_record'] = $this->returnLanguageRecordAsArray();
        if (!Module::isInstalled('blockwishlist') || !Module::isEnabled('blockwishlist')) {
            $this->content['wishlist_active'] = "0";
        } else {
            $this->content['wishlist_active'] = "1";
        }

        if (Tools::getIsset('session_data')) {
            $this->getCartId();
        }

        //To get cms content
        $this->getCmsLink();
        $this->content['Menu_Categories'] = $this->getMenus();

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
                        'src' => $res['image_url']
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
                        'src' => $res['image_url']
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
    public function getFeaturedProducts()
    {
        $module_name = 'ps_featuredproducts';
        $data = array();
        $data['title'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Feature Products'),
            'AppGetHome'
        );
        $data['products'] = array();

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
                        'ClickActivityName' => 'CategoryProductsActivity',
                        'category_id' => $res['id_category_default'],
                        'price' => $this->formatPrice($res['price_without_reduction']),
                        'src' => $this->context->link->getImageLink(
                            $res['link_rewrite'],
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
                $data['products'] = $products;
            } else {
                $this->writeLog('No product found in featured product section.');
            }
        } else {
            $this->writeLog('Featured category is not found.');
        }
        return $data;
    }

    /**
     * Get special product data
     *
     * @return array special product data
     */
    public function getSpecialProducts()
    {
        $data = array();
        $data['title'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Special Products'),
            'AppGetHome'
        );
        $data['products'] = array();

        $results = array();
        $products = array();

        if (!Configuration::get('PS_CATALOG_MODE')) {
            if ($results = Product::getPricesDrop(
                (int) $this->context->language->id,
                0,
                Configuration::get('BLOCKSPECIALS_SPECIALS_NBR'),
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
                            'ClickActivityName' => 'CategoryProductsActivity',
                            'category_id' => $res['id_category_default'],
                            'price' => $this->formatPrice($res['price_without_reduction']),
                            'src' => $this->context->link->getImageLink(
                                $res['link_rewrite'],
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
                    $data['products'] = $products;
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
    public function getBestSellerProducts()
    {
        $data = array();
        $data['title'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Bestseller Products'),
            'AppGetHome'
        );
        $data['products'] = array();

        $results = array();
        $products = array();
        if (!Configuration::get('PS_CATALOG_MODE')) {
            if ($results = ProductSale::getBestSalesLight(
                (int) $this->context->language->id,
                0,
                (int) Configuration::get('PS_BLOCK_BESTSELLERS_TO_DISPLAY')
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
                            'category_id' => $res['id_category_default'],
                            'price' => $this->formatPrice($res['price_without_reduction']),
                            'src' => $this->context->link->getImageLink(
                                $res['link_rewrite'],
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
                    $data['products'] = $products;
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
    public function getNewProducts()
    {
        $data = array();
        $data['title'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('New Arrivals'),
            'AppGetHome'
        );
        $data['products'] = array();

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
            (int) Configuration::get('NEW_PRODUCTS_NBR')
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
                        'ClickActivityName' => 'CategoryProductsActivity',
                        'category_id' => $res['id_category_default'],
                        'price' => $this->formatPrice($res['price_without_reduction']),
                        'src' => $this->context->link->getImageLink(
                            $res['link_rewrite'],
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
                $data['products'] = $products;
            } else {
                $this->writeLog('Bestseller Module - Product not found.');
            }
        } else {
            $this->writeLog('Bestseller module - Not able to get best seller products.');
        }

        return $data;
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
                    $sub_categories[$index]['third_children'] = $this->getSubChild(
                        $cat['id_category'],
                        $categories,
                        false
                    );
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
    public function getCmsLink()
    {
        $data = array();

        $cmslinks = $this->getCMSTitlesFooter();
        $index = 0;
        if ($cmslinks) {
            foreach ($cmslinks as $cmslink) {
                if ($cmslink['meta_title'] != '') {
                    $cmslink['link'] .= (parse_url($cmslink['link'], PHP_URL_QUERY) ? '&' : '?') . 'content_only=1';
                    $data[$index] = array(
                        'name' => $cmslink['meta_title'],
                        'link' => $cmslink['link']
                    );
                    $index++;
                }
            }
        }
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

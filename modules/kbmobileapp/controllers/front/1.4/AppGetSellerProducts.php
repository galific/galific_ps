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
 * API to get sellers product and categories list
 */

require_once 'AppCore.php';

class AppGetSellerProducts extends AppCore
{
    protected $seller = null;
    private $seller_image_path;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->setListPagingData();

        if (!(int) Tools::getValue('seller_id', 0)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Seller id is missing'),
                'AppGetSellerProducts'
            );
        } else {
            $this->seller = new KbSeller(Tools::getValue('seller_id', 0));
            if (!Validate::isLoadedObject($this->seller)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Seller not found'),
                    'AppGetSellerProducts'
                );
            } else {
                $this->seller_image_path = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'))
                        . 'img/kbmarketplace/sellers/';

                $this->seller = $this->seller->getSellerInfo();
                $this->content['status'] = 'success';
                if (Tools::getIsset('only_products') && Tools::getValue('only_products') == 1) {
                    $this->content['seller_info']['products'] = $this->getSellerProducts();
                } else {
                    $this->content['seller_info'] = $this->getSellerInfo();
                    $this->content['seller_info']['products'] = $this->getSellerProducts();
                }
            }
        }

        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Function to get seller information
     *
     * @return array
     */
    public function getSellerInfo()
    {
        $seller = array();
        $seller['seller_id'] = $this->seller['id_seller'];
        $seller['name'] = $this->seller['title'];
        $seller['rating'] = Tools::math_round(KbSellerReview::getSellerRating($this->seller['id_seller']), '1');

        $seller['Address'] = $this->seller['address'];
        $seller['state_name'] = $this->seller['state'];
        
        $writeEnabled = KbSellerSetting::getSellerSettingByKey($this->seller['id_seller'], 'kbmp_enable_seller_review');
        $seller['is_write_review_enabled'] = $writeEnabled;
        $seller['return_policy'] = preg_replace('/<iframe.*?\/iframe>/i', '', $this->seller['return_policy']);
        $seller['shipping_policy'] = preg_replace('/<iframe.*?\/iframe>/i', '', $this->seller['shipping_policy']);
        $country_name = '';
        if (!empty($this->seller['id_country'])) {
            $country_name = Country::getNameById($this->context->language->id, $this->seller['id_country']);
        }
        $seller['country_name'] = $country_name;

        $m_path = 'kbmarketplace/views/img/seller_media/';
        $marketplaceModuleName = 'kbmarketplace';
        $base_link = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
        $profile_default_image_path = $base_link.'modules/'.$marketplaceModuleName.'/'.'views/img/';
        $seller_image_path  = _PS_IMG_DIR_.KbSeller::SELLER_PROFILE_IMG_PATH.$this->seller['id_seller'].'/';
        if (empty($this->seller['logo']) || !Tools::file_exists_no_cache($seller_image_path . $this->seller['logo'])) {
            $seller['logo'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_LOGO;
        } else {
            $seller['logo'] = $this->seller_image_path . $this->seller['id_seller'] . '/' . $this->seller['logo'];
        }

        if (empty($this->seller['banner'])
            || !Tools::file_exists_no_cache($seller_image_path . $this->seller['banner'])) {
            $seller['banner'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
        } else {
            $seller['banner'] = $this->seller_image_path . $this->seller['id_seller'] . '/' . $this->seller['banner'];
        }

        $seller['filters'] = array(
            'category' => $this->getCategoryList($this->seller['id_seller']),
            'sort' => $this->getSortOrderData()
        );

        return $seller;
    }

    /**
     * Function to get sellers product information
     *
     * @return array
     */
    public function getSellerProducts()
    {
        $seller_products = array();

        $id_category = Tools::getValue('filter_category_id', '');
        $filters = array();

        if ((int) $id_category > 0) {
            $filters['id_category'] = (int) $id_category;
        }

        $total_records = KbSellerProduct::getProductsWithDetails(
            $this->seller['id_seller'],
            $this->context->language->id,
            $filters,
            true
        );

        if ($total_records > 0) {
            $products = KbSellerProduct::getProductsWithDetails(
                $this->seller['id_seller'],
                $this->context->language->id,
                $filters,
                false,
                $this->getPageStart(),
                $this->limit,
                $this->order_by,
                $this->order_way
            );

            if ($products && count($products) > 0) {
                $products = Product::getProductsProperties((int) $this->context->language->id, $products);
                $index = 0;
                foreach ($products as $res) {
                    $seller_products[$index] = array(
                        'id' => $res['id_product'],
                        'is_in_wishlist' => $this->isProductHasInWishlist($res['id_product']),
                        'name' => $res['name'],
                        'available_for_order' => $res['available_for_order'],
                        'show_price' => $res['show_price'],
                        'new_products' => (isset($res['new']) && $res['new'] == 1) ? "1" : "0",
                        'on_sale_products' => $res['on_sale'],
                        'ClickActivityName' => 'ProductActivity',
                        'price' => $this->formatPrice($res['price_without_reduction']),
                        
                    );
                    
                    /* Changes started by rishabh jain on 3rd sep 2018
                    * Added urlencode perimeter in image link if enabled by admin
                    */
                    if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                        $seller_products[$index]['src'] = $this->context->link->getImageLink(
                            urlencode($res['link_rewrite']),
                            $res['id_image'],
                            $this->getImageType('large')
                        );
                    } else {
                        $seller_products[$index]['src'] = $this->context->link->getImageLink(
                            $res['link_rewrite'],
                            $res['id_image'],
                            $this->getImageType('large')
                        );
                    }
                    /* Changes over */

                    
                    if (count($res['specific_prices']) > 0) {
                        $seller_products[$index]['discount_price'] = $this->formatPrice($res['price']);
                        if ($res['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                            $temp_price = (float) $res['specific_prices']['reduction'] * 100;
                            $seller_products[$index]['discount_percentage'] = $temp_price;
                            unset($temp_price);
                        } else {
                            if ($res['price_without_reduction']) {
                                $temp_price = ((float) $res['specific_prices']['reduction'] * 100);
                                $percent = (float) ($temp_price / $res['price_without_reduction']);
                                unset($temp_price);
                            } else {
                                $percent = 0;
                            }
                            $seller_products[$index]['discount_percentage'] = Tools::ps_round($percent);
                        }
                    } else {
                        $seller_products[$index]['discount_price'] = '';
                        $seller_products[$index]['discount_percentage'] = '';
                    }
                    $index++;
                }
            }
        }

        return $seller_products;
    }

    /**
     * Function to get sellers category list information
     *
     * @return array
     */
    protected function getCategoryList($id_seller = 0, $return_unassigned = false)
    {
        $categories = array();
        $categories[] = array(
            'id_category' => 0,
            'name' => parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('All'),
                'AppGetSellerProducts'
            )
        );
        $root_category = Category::getRootCategories();
        $all = Category::getSimpleCategories($this->context->language->id);
        $seller_categories = array();
        if ($id_seller > 0) {
            $seller_categories = KbSellerCategory::getCategoriesBySeller($id_seller);
        }
        foreach ($all as $c) {
            if ($root_category[0]['id_category'] != $c['id_category']) {
                $include_cat = false;
                if ($id_seller > 0) {
                    if ($return_unassigned && !in_array($c['id_category'], $seller_categories)) {
                        $include_cat = true;
                    } elseif (!$return_unassigned && in_array($c['id_category'], $seller_categories)) {
                        $include_cat = true;
                    }
                } else {
                    $include_cat = true;
                }

                if ($include_cat) {
                    $tmp = new Category($c['id_category'], $this->context->language->id, $this->context->shop->id);
                    $parents = $tmp->getParentsCategories();

                    $parents = array_reverse($parents);
                    $str = '';
                    foreach ($parents as $p) {
                        $str .= ' >> ' . $p['name'];
                    }

                    $categories[] = array(
                        'id_category' => $c['id_category'],
                        'name' => ltrim($str, ' >> ')
                    );
                }
            }
        }

        return $categories;
    }

    /**
     * Function to set pagination data
     * limt,page number, order by, order way
     *
     */
    public function setListPagingData()
    {
        parent::setListPagingData();

        $sort_values = array('pl.name', 'p.price');

        if (!in_array($this->order_by, $sort_values)) {
            $this->order_by = 'pl.name';
        }
    }

    /**
     * Function to get sortorder data
     *
     * @return array
     */
    public function getSortOrderData()
    {
        $nameLabel = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Name'),
            'AppGetSellerProducts'
        );
        $priceLabel = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Price'),
            'AppGetSellerProducts'
        );
        $ascnameLabel = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('A-Z'),
            'AppGetSellerProducts'
        );
        $descnameLabel = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Z-A'),
            'AppGetSellerProducts'
        );
        $ascpriceLabel = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Low to High'),
            'AppGetSellerProducts'
        );
        $descpriceLabel = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('High to Low'),
            'AppGetSellerProducts'
        );
        
        
        return array(
            array('order_by' => 'pl.name', 'order_way' => 'asc', 'label' => $nameLabel, 'order_way_label' => $ascnameLabel),
            array('order_by' => 'pl.name', 'order_way' => 'desc', 'label' => $nameLabel, 'order_way_label' => $descnameLabel),
            array('order_by' => 'p.price', 'order_way' => 'asc', 'label' => $priceLabel, 'order_way_label' => $ascpriceLabel),
            array('order_by' => 'p.price', 'order_way' => 'desc', 'label' => $priceLabel, 'order_way_label' => $descpriceLabel),
        );
    }
}

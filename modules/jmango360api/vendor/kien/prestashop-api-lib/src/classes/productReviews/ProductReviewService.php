<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if (file_exists(_PS_MODULE_DIR_ . 'netreviews/NetReviewsModel.php')) {
    require_once _PS_MODULE_DIR_ . "netreviews/NetReviewsModel.php";
}
if (file_exists(_PS_MODULE_DIR_ . 'netreviews/models/NetReviewsModel.php')) {
    require_once _PS_MODULE_DIR_ . "netreviews/models/NetReviewsModel.php";
}

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ProductReviewService extends BaseService
{
    private $group_name;
    private $id_lang;
    private $page_num = 1;
    private $iso_lang;

    public function doExecute()
    {
        if ($this->isGetMethod()) {
            $response = new ProductReviewResponse();
            // not support ver 1.6 yet
            if (!$this->isV17()) {
                $this->response = $response;
                return ;
            }
            // if verified review module is not installed
            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();
            if (!$moduleManager->isInstalled('netreviews') || !Module::getInstanceByName('netreviews')->active) {
                $this->response = $response;
                return ;
            }
            $netReviewsModel = new NetReviewsModel();
            $id_product = (int)Tools::getValue('id_product');
            $this->page_num = (int)Tools::getValue('page_num') ? (int)Tools::getValue('page_num') : 1;

            $multisite = Configuration::get('AV_MULTISITE');
            $av_idshop = (!empty($multisite))? $this->context->shop->getContextShopID():null;
            if (Configuration::get('AV_MULTILINGUE', null, null, $av_idshop) == 'checked') {
                $this->id_lang = $this->context->language->id;
                $this->iso_lang = pSQL(Language::getIsoById($this->id_lang)); //$this->context->language->iso_code
                $this->group_name = $this->getIdConfigurationGroup($this->iso_lang);
            }
            $new_version = true;
            $display_prod_reviews = configuration::get('AV_DISPLAYPRODREVIEWS'.$this->group_name, null, null, $av_idshop);
            $avisverifies_nb_reviews = (int)Configuration::get('AV_NBOFREVIEWS', null, null, $av_idshop);
            $local_id_website = Configuration::get('AV_IDWEBSITE'.$this->group_name, null, null, $av_idshop);
            $local_secure_key = Configuration::get('AV_CLESECRETE'.$this->group_name, null, null, $av_idshop);
            $shop_name = Configuration::get('PS_SHOP_NAME');
            $o_av = new NetReviewsModel();
            $stats_product = (!isset($this->stats_product) || empty($this->stats_product)) ?
                $o_av->getStatsProduct($id_product, $this->group_name, $av_idshop)
                : $this->stats_product;
            if (! empty($stats_product['nb_reviews']) && $display_prod_reviews == 'yes') {
                $reviews_list = array(); //Create array with all reviews data
                $my_review = array(); //Create array with each reviews data
                $reviews = $this->getProductReviews($id_product, $this->group_name, $av_idshop, $avisverifies_nb_reviews, $this->page_num, 'horodate_DESC', 0, false);
                $reviews_count = $stats_product['nb_reviews'];
                $reviews_max_pages = floor($reviews_count/$avisverifies_nb_reviews) + ($reviews_count%$avisverifies_nb_reviews>0 ?1 :0);

                $reviews_rate_portion_keys = array(1,2,3,4,5);
                $reviews_rate_portion = array_fill_keys($reviews_rate_portion_keys, 0);
                $reviews_all = $o_av->getProductReviews($id_product, $this->group_name, $av_idshop, 0, 1, 'horodate_DESC', 0, false);

                foreach ($reviews_all as $review) {
                    switch ($review['rate']) {
                        case '1':
                            $reviews_rate_portion[1] += 1;
                            break;
                        case '2':
                            $reviews_rate_portion[2] += 1;
                            break;
                        case '3':
                            $reviews_rate_portion[3] += 1;
                            break;
                        case '4':
                            $reviews_rate_portion[4] += 1;
                            break;
                        case '5':
                            $reviews_rate_portion[5] += 1;
                            break;
                    }
                }
                foreach ($reviews as $review) {
                    //Create variable for template engine
                    $my_review['ref_produit'] = $review['ref_product'];
                    $my_review['id_product_av'] = $review['id_product_av'];
                    $my_review['sign'] = sha1($local_id_website.$review['id_product_av'].$local_secure_key);
                    if (!isset($review['helpful']) && !isset($review['helpless'])) {
                        Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'av_products_reviews`
                   ADD `helpful` int(7) DEFAULT 0,
                   ADD `helpless` int(7) DEFAULT 0');
                    } else {
                        $my_review['helpful'] = $review['helpful'];
                        $my_review['helpless'] = $review['helpless'];
                    }
                    $my_review['rate'] = $review['rate'];
                    $my_review['rate_percent'] = $review['rate']*20;
                    $my_review['avis'] = html_entity_decode(urldecode($review['review']));
                    // review date
                    if (Tools::strlen($review['horodate'])=='10') {
                        $date = new DateTime();
                        $date->setTimestamp($review['horodate']);
                        $my_review['horodate'] = $date->format('d/m/Y') ;
                    } else {
                        $my_review['horodate'] = date('d/m/Y', strtotime($review['horodate']));
                    }
                    // order date
                    if (isset($review['horodate_order']) && !empty($review['horodate_order'])) {
                        $review['horodate_order'] = str_replace('"', '', $review['horodate_order']);
                        $my_review['horodate_order'] = date('d/m/Y', strtotime($review['horodate_order']));
                    } else {
                        $my_review['horodate_order'] = $my_review['horodate'];
                    }
                    // in case imported reviews which have lack of this info
                    if (!isset($review['horodate']) || empty($review['horodate'])) {
                        $my_review['horodate'] = $my_review['horodate_order'];
                    }

                    $my_review['customer_name'] = urldecode($review['customer_name']);

                    $my_review['discussion'] = array();

                    //renverser le nom et le prénom
                    $customer_name = explode(' ', urldecode($review['customer_name']));
                    $customer_name = array_values(array_filter($customer_name));
                    $customer_name = array_diff($customer_name, array("."));
                    $customer_name = array_reverse($customer_name);
                    $customer_name = implode(' ', $customer_name);

                    $my_review['customer_name'] =  $customer_name;

                    $unserialized_discussion = $this->avJsonDecode($netReviewsModel->acDecodeBase64($review['discussion']), true);
                    $unserialized_discussion = (array)$unserialized_discussion;
                    if ($unserialized_discussion) {
                        foreach ($unserialized_discussion as $k_discussion => $each_discussion) {
                            $each_discussion = (array)$each_discussion;
                            $my_review['discussion'][$k_discussion] = array();
                            if (Tools::strlen($each_discussion['horodate'])=='10') {
                                $date = new DateTime();
                                $date->setTimestamp($each_discussion['horodate']);
                                $my_review['discussion'][$k_discussion]['horodate'] = $date->format('d/m/Y') ;
                            } else {
                                $my_review['discussion'][$k_discussion]['horodate'] = date('d/m/Y', strtotime($each_discussion['horodate']));
                            }
                            $my_review['discussion'][$k_discussion]['commentaire'] = $each_discussion['commentaire'];
                            if ($each_discussion['origine'] == 'ecommercant') {
                                $my_review['discussion'][$k_discussion]['origine'] = $shop_name;
                            } elseif ($each_discussion['origine'] == 'internaute') {
                                $my_review['discussion'][$k_discussion]['origine'] = $my_review['customer_name'];
                            } else {
                                $my_review['discussion'][$k_discussion]['origine'] = $this->l('Moderator');
                            }
                        }
                    }
                    // Media infos
                    $my_review['media_content'] = array();
                    if (isset($review['media_full'])) {
                        $review_images_result =  (array) $this->avJsonDecode(html_entity_decode($review['media_full']), true);
                        if (isset($review_images_result) && !empty($review_images_result) && count($review_images_result) > 1) {
                            foreach ($review_images_result as $k_media => $each_media) {
                                $my_review['media_content'][$k_media] = (array) $each_media;
                            }
                        }
                    }
                    array_push($reviews_list, $my_review);
                }
                //rich snippets informations:
                $average_rate_percent = array();
                $average_rate_percent['floor'] = floor($stats_product['rate']) - 1;
                $average_rate_percent['decimals'] = ($stats_product['rate'] - floor($stats_product['rate']))*20;
            }
        }
        $response->reviews = $reviews_list;
        $response->reviews_count = $reviews_count;
        $response->reviews_max_pages = $reviews_max_pages;
        $response->review_per_page = $avisverifies_nb_reviews;
        $response->overview = $stats_product ? $stats_product : null;
        $this->response = $response;
    }

    public function getIdConfigurationGroup($lang_iso = null)
    {
        $multisite = Configuration::get('AV_MULTISITE');
        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1 && !empty($multisite)) {
            $sql = 'SELECT name FROM '._DB_PREFIX_."configuration where name like 'AV_GROUP_CONF_%' And id_shop = '"
                .$this->context->shop->getContextShopID()."'";
        } else {
            $sql = 'SELECT name FROM '._DB_PREFIX_."configuration where name like 'AV_GROUP_CONF_%'";
        }
        if ($results = Db::getInstance()->ExecuteS($sql)) {
            foreach ($results as $row) {
                if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') == 1 && !empty($multisite)) {
                    $vconf = unserialize(Configuration::get($row['name'], null, null, $this->context->shop->getContextShopID()));
                } else {
                    $vconf = unserialize(Configuration::get($row['name']));
                }
                if ($vconf && in_array($lang_iso, $vconf)) {
                    return '_'.Tools::substr($row['name'], 14);
                }
            }
        }
    }

    public function getProductReviews($id_product, $group_name = null, $id_shop = null, $reviews_per_page = 20, $current_page = 1, $review_filter_orderby = 'horodate_DESC', $review_filter_note = 0, $getreviews = false)
    {
        $filter = "" ;
        $multishop_condition = "" ;
        $limit = "" ;
        $start = ($current_page > 1)? ($current_page-1) * $reviews_per_page : 0;
        $end = $reviews_per_page ;
        $helfulrating ="" ;
        $note_range = array(1,2,3,4,5);
        if (in_array($review_filter_note, $note_range)) {
            $filter .= " and rate = '".$review_filter_note."'";
        }
        $a_sorting = explode("_", $review_filter_orderby);
        if ($a_sorting[0] == "horodate" || $a_sorting[0] == "rate") {
            $filter .=' ORDER BY '.$a_sorting[0].' '.$a_sorting[1];
        } elseif ($a_sorting[0] == "helpfulrating") {
            $helfulrating =", helpful-helpless as helpfulrating" ;
            $filter .=' ORDER BY '.$a_sorting[0].' '.$a_sorting[1];
        }
        if ($reviews_per_page != '0') {
            $limit .=' LIMIT '.$start.', '.$end;
        }
        $sql = 'SELECT *'.$helfulrating.' FROM '._DB_PREFIX_.'av_products_reviews WHERE ref_product = '.(int)$id_product;

        if (!empty($group_name)) {
            if (!empty($id_shop) && Shop::isFeatureActive()) {
                $av_group_conf = unserialize(Configuration::get('AV_GROUP_CONF'.$group_name, null, null, $id_shop));
            } else {
                $av_group_conf = unserialize(Configuration::get('AV_GROUP_CONF'.$group_name));
            }
            $sql .= ' and iso_lang in ("'.implode('","', $av_group_conf).'")';
        } else {
            $sql .= " and iso_lang = '0'";
        }
        if (!empty($id_shop) && Shop::isFeatureActive()) {
            $multishop_condition .= ' and (id_shop = '.$id_shop.')';
        } else {
            $multishop_condition .= ' and id_shop IN(0,1)';
        }

        if ($getreviews == true) {
            $sql = 'SELECT COUNT(ref_product) as nbreviews FROM '._DB_PREFIX_
                .'av_products_reviews WHERE ref_product = '.(int)$id_product.$multishop_condition.$filter;
        } else {
            $sql .= $multishop_condition.$filter.$limit;
        }
        // echo $sql.'<br>';
        return Db::getInstance()->ExecuteS($sql);
    }

    public static function avJsonEncode($codes)
    {
        if (version_compare(_PS_VERSION_, '1.4', '<')) {
            return json_encode($codes);
        } else {
            return Tools::jsonEncode($codes);
        }
    }

    public static function avJsonDecode($codes)
    {
        if (version_compare(_PS_VERSION_, '1.4', '<')) {
            return json_decode($codes);
        } else {
            return Tools::jsonDecode($codes);
        }
    }
}

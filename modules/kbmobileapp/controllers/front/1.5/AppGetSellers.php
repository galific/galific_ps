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
 * API to get marketplace sellers listing
 */

require_once 'AppCore.php';

class AppGetSellers extends AppCore
{
    protected $seller = null;
    protected $seller_image_path;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->seller_image_path = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'))
                        . 'img/kbmarketplace/sellers/';
        
        $this->content['sellers'] = $this->getSellerList();

        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }


    /**
     * Function to get sellers information
     *
     * @return array
     */
    public function getSellerList()
    {
        $this->setListPagingData();
        $sellersData = array();

        $total = KbSeller::getSellers(true, true, null, null, null, null, true, true);
       

        if ($total > 0) {
            $paging = KbGlobal::getPaging($total, $this->page_number, $this->limit, false, 'getSellerList');

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
                $this->limit,
                $orderby,
                $orderway,
                true,
                true
            );
            
            $marketplaceModuleName = 'kbmarketplace';
            foreach ($sellers as $key => $val) {
                $sellersData[$key]['seller_id'] = $val['id_seller'];
                
                if ($val['title'] == '' || empty($val['title'])) {
                    $sellersData[$key]['name'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Not Mentioned'),
                        'AppGetSellers'
                    );
                } else {
                    $sellersData[$key]['name'] = $val['title'];
                }
                
                $sellersData[$key]['rating'] = Tools::math_round(KbSellerReview::getSellerRating($val['id_seller']), '1');
                
                $base_link                  = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
                $profile_default_image_path = $base_link.'modules/'.$marketplaceModuleName.'/'.'views/img/';
                $seller_image_path          = _PS_IMG_DIR_.KbSeller::SELLER_PROFILE_IMG_PATH.$val['id_seller'].'/';
                if (empty($val['logo']) || !Tools::file_exists_no_cache($seller_image_path.$val['logo'])) {
                    $sellersData[$key]['logo'] = $profile_default_image_path.KbGlobal::SELLER_DEFAULT_LOGO;
                } else {
                    $sellersData[$key]['logo'] = $this->seller_image_path.$val['id_seller'].'/'.$val['logo'];
                }
                
                if (empty($val['banner'])
                    || !Tools::file_exists_no_cache($seller_image_path . $val['banner'])) {
                    $sellersData[$key]['banner'] = $profile_default_image_path . KbGlobal::SELLER_DEFAULT_BANNER;
                } else {
                    $sellersData[$key]['banner'] = $this->seller_image_path . $val['id_seller'] . '/' . $val['banner'];
                }
                $writeEnabled = KbSellerSetting::getSellerSettingByKey($val['id_seller'], 'kbmp_enable_seller_review');
                $sellersData[$key]['is_write_review_enabled'] = $writeEnabled;
            }
        }
        return $sellersData;
    }
}

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
 * API to get seller review
 */

require_once 'AppCore.php';

class AppGetSellerReviews extends AppCore
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
        if (!(int) Tools::getValue('seller_id', 0)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Seller id is missing'),
                'AppGetSellerReviews'
            );
        } else {
            $this->seller = new KbSeller(Tools::getValue('seller_id', 0));
            if (!Validate::isLoadedObject($this->seller)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Seller not found'),
                    'AppGetSellerReviews'
                );
            } else {
                $this->seller_image_path = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'))
                        . 'img/kbmarketplace/sellers/';

                $this->seller = $this->seller->getSellerInfo();
                $this->content['status'] = 'success';
                if (Tools::getIsset('only_comments') && Tools::getValue('only_comments') == 1) {
                    $this->content['seller_info']['comments'] = $this->getSellerComments();
                } else {
                    $this->content['seller_info'] = $this->getSellerInfo();
                    $this->content['seller_info']['comments'] = $this->getSellerComments();
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
        $seller['rating'] = KbSellerReview::getSellerRating($this->seller['id_seller']);

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

        $s_path = 'kbmarketplace/views/img/seller_media/';
        $marketplaceModuleName = 'kbmarketplace';
        $base_link = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
        $profile_default_image_path = $base_link.'modules/'.$marketplaceModuleName.'/'.'views/img/';
        $seller_image_path = _PS_IMG_DIR_.KbSeller::SELLER_PROFILE_IMG_PATH.$this->seller['id_seller'].'/';
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

        return $seller;
    }

    /**
     * Function to get sellers review information
     *
     * @return array
     */
    public function getSellerComments()
    {
        $this->setListPagingData();
        $comments = array();
        $review_count = KbSellerReview::getReviewsBySellerId(
            $this->seller['id_seller'],
            $this->context->language->id,
            KbGlobal::APPROVED,
            true
        );

        if ($review_count > 0) {
            $reviews = KbSellerReview::getReviewsBySellerId(
                $this->seller['id_seller'],
                $this->context->language->id,
                KbGlobal::APPROVED,
                false,
                false,
                $this->getPageStart(),
                $this->limit
            );

            $index = 0;
            foreach ($reviews as $rev) {
                $comments[$index] = array(
                    'id' => $rev['id_seller_review'],
                    'comment_date' => date('m/d/y H:i A', strtotime($rev['date_add'])),
                    'commented_by' => $rev['firstname'] . ' ' . $rev['lastname'],
                    'title' => $rev['title'],
                    'text' => $rev['comment'],
                    'rating' => $rev['rating']
                );
                $index++;
            }
        }

        return $comments;
    }
}

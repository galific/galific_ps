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
 * API to save seller review provided by customer
 */

require_once 'AppCore.php';

class AppSaveSellerReview extends AppCore
{
    protected $seller = null;
    private $id_customer = 0;

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
                'AppSaveSellerReview'
            );
        } else {
            $this->seller = new KbSeller(Tools::getValue('seller_id', 0));
            if (!Validate::isLoadedObject($this->seller)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Seller not found'),
                    'AppSaveSellerReview'
                );
            } else {
                $email = Tools::getValue('email', '');
                if (!Validate::isEmail($email)) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Login required'),
                        'AppSaveSellerReview'
                    );
                } elseif (!$this->id_customer = Customer::customerExists($email, true)) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Login required'),
                        'AppSaveSellerReview'
                    );
                } else {
                    $response = $this->saveNewReview();
                    $this->content['status'] = $response['status'];
                    $this->content['message'] = $response['message'];
                }
            }
        }

        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Function to save review information
     *
     * @return array
     */
    protected function saveNewReview()
    {
        $title = Tools::getValue('title');
        $comment = Tools::getValue('text');
        $rating = (int) Tools::getValue('rating');
        
        
        if ($title == '' || $comment == '') {
            $response = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Not able to submit your review. Title or comment is missing.'),
                    'AppSaveSellerReview'
                )
            );
            
            return $response;
        }
        
        $writeEnabled = KbSellerSetting::getSellerSettingByKey($this->seller->id, 'kbmp_enable_seller_review');
        
        if (!$writeEnabled) {
            $response = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Can not save the review as the option is disabled by admin'),
                    'AppSaveSellerReview'
                )
            );
            
            return $response;
        }

        $new_review = new KbSellerReview();
        $new_review->title = $title;
        $new_review->comment = $comment;
        $new_review->rating = $rating;
        $new_review->id_seller = $this->seller->id;
        $new_review->id_customer = (int) $this->id_customer;
        $new_review->id_shop = $this->context->shop->id;
        $new_review->id_lang = $this->context->language->id;
        $approved = KbSellerSetting::getSellerSettingByKey($this->seller->id, 'kbmp_seller_review_approval_required');
        if ($approved == 1) {
            $new_review->approved = (string) KbGlobal::APPROVAL_WAITING;
        } else {
            $new_review->approved = (string) KbGlobal::APPROVED;
        }

        $this->sendNewReviewMail($approved);

        if ($new_review->save()) {
            if ($approved == 1) {
                $response = array(
                    'status' => 'success',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Your review has been submitted successfully and shown to others after getting approval from store.'),
                        'AppSaveSellerReview'
                    ),
                );
            } else {
                $response = array(
                    'status' => 'success',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Your review has been submitted successfully.'),
                        'AppSaveSellerReview'
                    ),
                );
            }
        } else {
            $response = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Right now, system not able to submit your review. Please try again later'),
                    'AppSaveSellerReview'
                ),
            );
        }

        return $response;
    }

    /**
     * Function to send new review email to admin and seller
     *
     */
    protected function sendNewReviewMail($approved)
    {
        $seller_info = $this->seller->getSellerInfo();
        $template_vars = array(
            '{{seller_name}}' => $seller_info['seller_name'],
            '{{seller_email}}' => $seller_info['email'],
            '{{shop_title}}' => $seller_info['title'],
            '{{seller_contact}}' => $seller_info['phone_number'],
            '{{review_title}}' => Tools::getValue('title'),
            '{{review_comment}}' => Tools::getValue('text'),
        );
        if ($approved == KbGlobal::APPROVAL_WAITING) {
            //send email to admin for approval
            $email = new KbEmail(
                KbEmail::getTemplateIdByName('mp_seller_review_approval_request_admin'),
                $seller_info['id_default_lang']
            );
            $email->send(
                Configuration::get('PS_SHOP_EMAIL'),
                Configuration::get('PS_SHOP_NAME'),
                null,
                $template_vars
            );
        }

        //send email to Seller
        $email = new KbEmail(
            KbEmail::getTemplateIdByName('mp_seller_review_notification'),
            $seller_info['id_default_lang']
        );
        $notification_emails = $this->seller->getEmailIdForNotification();
        foreach ($notification_emails as $em) {
            $email->send($em['email'], $em['title'], null, $template_vars);
        }
    }
}

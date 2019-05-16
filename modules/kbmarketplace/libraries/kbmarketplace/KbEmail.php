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

class KbEmail extends ObjectModel
{
    
    public $id;
    //public $id_lang;
    public $name;
    public $description;
    public $subject;
    public $body;
    public $end;
    public $date_add;
    public $date_upd;
    private $id_store_shop;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition       = array(
        'table' => 'kb_mp_email_template',
        'primary' => 'id_email_template',
        'multilang' => true,
        'fields' => array(
            'name' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName',
                'required' => true),
            'description' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'copy_post' => false),
            /* Lang fields */
            'subject' => array('type' => self::TYPE_STRING, 'lang' => true,
                'validate' => 'isMailSubject'),
            'body' => array('type' => self::TYPE_HTML, 'lang' => true, 'validate' => 'isCleanHtml',
                'required' => true)
        )
    );
    protected $webserviceParameters = array(
        'objectsNodeName' => 'kbemails',
        'objectNodeName' => 'kbemail'
    );

    /*
     * Email Template Files
     */
    const EMAIL_TEMPLATE_NAME = 'kb_marketplace_email';

    private $template_html    = '';
    private $template_txt     = '';
    private $template_path;
    private $template_lang    = '';
    private $template_lang_id = null;

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        if (!$id_shop) {
            $this->id_store_shop = Configuration::get('PS_SHOP_DEFAULT');
        }

        parent::__construct($id, $id_lang);
        $this->context = Context::getContext();
        if (!empty($id_lang)) {
            $this->subject = str_replace('{{shop_name}}', Configuration::get('PS_SHOP_NAME'), $this->subject);

            $this->template_html = self::EMAIL_TEMPLATE_NAME.'.html';
            $this->template_txt  = self::EMAIL_TEMPLATE_NAME.'.txt';
            $this->template_path = _PS_MODULE_DIR_.'kbmarketplace/mails/';

            $iso = Language::getIsoById((int) $id_lang);
            if (Tools::file_exists_no_cache($this->template_path.$iso)) {
                $this->template_lang    = $iso;
                $this->template_lang_id = (int) $id_lang;
            } elseif (Tools::file_exists_no_cache($this->template_path.$this->context->language->iso_code)) {
                $this->template_lang    = $this->context->language->iso_code;
                $this->template_lang_id = (int) $this->context->language->id;
            } else {
                $this->template_lang    = Language::getIsoById((int) Configuration::get('PS_LANG_DEFAULT'));
                $this->template_lang_id = (int) Configuration::get('PS_LANG_DEFAULT');
            }

            $directory = $this->template_path.$this->template_lang.'/';
            $base_html = $this->getBaseHtml();

            $content = str_replace('{{template_content}}', rawurldecode($this->body), $base_html);

            $file = fopen($directory.$this->template_html, 'w+');
            fwrite($file, $content);
            fclose($file);

            $file = fopen($directory.$this->template_txt, 'w+');
            fwrite($file, $content);
            fclose($file);
        }
    }
    /*
     * Email template ids, please do not change these ids
     */

    public static function getTemplateId($key)
    {
        $template_ids = array(
            'mp_welcome_seller' => 1,
            'mp_seller_account_approval' => 2,
            'mp_seller_account_disapproval' => 3,
            'mp_seller_registration_notification_admin' => 4,
            'mp_seller_account_approval_after_disapprove' => 5,
            'mp_new_product_notification_admin' => 6,
            'mp_category_request_notification_admin' => 7,
            'mp_category_request_approved' => 8,
            'mp_category_request_disapproved' => 9,
            'mp_product_disapproval_notification' => 10,
            'mp_product_approval_notification' => 11,
            'mp_product_delete_notification' => 12,
            'mp_seller_review_approval_request_admin' => 13,
            'mp_seller_review_notification' => 14,
            'mp_seller_amount_credit_transfer_notification' => 15,
            'mp_seller_review_approved_to_customer' => 16,
            'mp_seller_review_approved_to_seller' => 17,
            'mp_seller_review_disspproved_to_seller' => 18,
            'mp_seller_review_disspproved_to_customer' => 19,
            'mp_seller_amount_debit_transfer_notification' => 20,
            'mp_seller_review_delete_to_seller' => 21,
            'mp_seller_review_delete_to_customer' => 22,
            'mp_seller_account_enable' => 23,
            'mp_seller_account_disable' => 24
        );
        return $template_ids[$key];
    }

    public static function getTemplateIdByName($name)
    {
        $sql = 'Select id_email_template from '._DB_PREFIX_.'kb_mp_email_template WHERE name = "'.pSQL($name).'"';
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
    }

 
    private function getBaseHtml()
    {
        $template_html = '<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/1999/REC-html401-19991224/strict.dtd">
                            <html>
                                    <!-- PRE-HEADER - THIS TEXT IS DISPLAYED IN THE PREHEADER OR JOHNSON BOX -->
                                    <div style="display:none;max-height:0;overflow:hidden;mso-hide:all;">{{template_content}}</div>
                                    <!-- END PRE-HEADER -->
                                    <head>
                                            <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                                            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
                                            <title>Message from {shop_name}</title>
                                            <style>
                                             @media only screen and (max-width: 300px){
                                                     body {
                                                            width:218px !important;
                                                            margin:auto !important;
                                                     }
                                                     .table {
                                                            width:195px !important;
                                                            margin:auto !important;
                                                     }
                                                     .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{
                                                            width:auto !important;
                                                            display: block !important;
                                                     }
                                                     span.title{
                                                            font-size:20px !important;
                                                            line-height: 23px !important
                                                     }
                                                     span.subtitle{
                                                            font-size: 14px !important;
                                                            line-height: 18px !important;
                                                            padding-top:10px !important;
                                                            display:block !important;
                                                     }
                                                     td.box p{
                                                            font-size: 12px !important;
                                                            font-weight: bold !important;
                                                     }
                                                     .table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr {
                                                            display: block !important;
                                                     }
                                                     .table-recap{
                                                            width: 200px!important;
                                                     }
                                                     .table-recap tr td, .conf_body td{
                                                            text-align:center !important;
                                                     }
                                                     .address{
                                                            display: block !important;
                                                            margin-bottom: 10px !important;
                                                     }
                                                     .space_address{
                                                            display: none !important;
                                                     }
                                              }
                                             @media only screen and (min-width: 301px) and (max-width: 500px) {
                                                    body {
                                                       width:308px!important;
                                                       margin:auto!important;
                                                    }
                                                    .table {
                                                       width:285px!important;
                                                       margin:auto!important;
                                                    }
                                                    .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{
                                                       width:auto!important;
                                                       display: block!important;
                                                    }
                                                    .table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr {
                                                       display: block !important;
                                                    }
                                                    .table-recap{
                                                       width: 295px !important;
                                                    }
                                                    .table-recap tr td, .conf_body td{
                                                       text-align:center !important;
                                                    }
                                             }
                                             @media only screen and (min-width: 501px) and (max-width: 768px) {
                                                    body {
                                                       width:478px!important;
                                                       margin:auto!important;
                                                    }
                                                    .table {
                                                       width:450px!important;
                                                       margin:auto!important;
                                                    }
                                                    .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{
                                                       width:auto!important;display: block!important;
                                                    }
                                             }
                                             @media only screen and (max-device-width: 480px) {
                                                    body {
                                                       width:308px!important;
                                                       margin:auto!important;
                                                    }
                                                    .table {
                                                       width:285px;
                                                       margin:auto!important;
                                                    }
                                                    .logo, .titleblock, .linkbelow, .box, .footer, .space_footer{
                                                       width:auto!important;display: block!important;
                                                    }
                                                    .table-recap{
                                                       width: 295px!important;
                                                    }
                                                    .table-recap tr td, .conf_body td{
                                                       text-align:center!important;
                                                    }
                                                    .address{
                                                       display: block !important;margin-bottom: 10px !important;
                                                    }
                                                    .space_address{
                                                       display: none !important;
                                                    }
                                             }
                                            </style>
                                    </head>
                                    <body style="-webkit-text-size-adjust:none;background-color:#fff;width:650px;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;line-height:18px;margin:auto">
                                            <table class="table table-mail" style="width:100%;margin-top:10px;-moz-box-shadow:0 0 5px #afafaf;-webkit-box-shadow:0 0 5px #afafaf;-o-box-shadow:0 0 5px #afafaf;box-shadow:0 0 5px #afafaf;filter:progid:DXImageTransform.Microsoft.Shadow(color=#afafaf,Direction=134,Strength=5)">
                                                    <tr>
                                                            <td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
                                                            <td align="center" style="padding:7px 0">
                                                                    <table class="table" bgcolor="#ffffff" style="width:100%">
                                                                            <tr>
                                                                                    <td align="center" class="logo" style="border-bottom:4px solid #333333;padding:7px 0">
                                                                                            <a title="{shop_name}" href="{shop_url}" style="color:#337ff1">
                                                                                            <img src="{shop_logo}" alt="{shop_name}" />
                                                                                            </a>
                                                                                    </td>
                                                                            </tr>
                                                                            <tr>
                                                                               <td style="border: 1px solid #d6d4d4; background-color: #f8f8f8; padding: 7px 0;">
                                                                                       <table style="width:100%; display: table; border-collapse: separate; border-spacing: 2px; border-color: grey;">
                                                                                               <tbody><tr>
                                                                                                       <td width="10" style="padding:7px 0">&nbsp;</td>
                                                                                                       <td style="padding: 7px 0px;">
                                                                                                               <font size="2" face="Open-sans, sans-serif" color="#555454">
                                                                                                                       <div style="text-align:left;">{{template_content}}</div>
                                                                                                               </font>
                                                                                                       </td>
                                                                                                       <td width="10" style="padding:7px 0">&nbsp;</td>
                                                                                               </tr>
                                                                                       </tbody></table>
                                                                               </td>
                                                                       </tr>
                                                                            <tr>
                                                                                    <td class="footer" style="border-top:4px solid #333333;padding:7px 0">
                                                                                            <span><a href="{shop_url}" style="color:#337ff1">{shop_name}</a> powered by <a href="http://www.prestashop.com/" style="color:#337ff1">PrestaShop&trade;</a></span>
                                                                                    </td>
                                                                            </tr>
                                                                    </table>
                                                            </td>
                                                    </tr>
                                            </table>
                                    </body>
                            </html>';
        return $template_html;
    }

  
    public function sendWelcomeEmailToCustomer($data)
    {
        $template_vars = array(
            '{{email}}' => $data['email'],
            '{{full_name}}' => $data['name']
        );

        return $this->send($data['email'], $data['name'], $this->subject, $template_vars);
    }

    public function sendNotificationOnNewRegistration($data)
    {
        $template_vars = array(
            '{{email}}' => $data['email'],
            '{{full_name}}' => $data['name']
        );

        return $this->send(
                        Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), $this->subject, $template_vars
        );
    }

    public function send($to, $to_name, $subject = null, $vars = array())
    {
        if (!empty($subject)) {
            $this->subject = $subject;
        }

        if (Mail::Send(
                        $this->template_lang_id, self::EMAIL_TEMPLATE_NAME, $this->subject, $vars, $to, $to_name, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), null, null, $this->template_path, false, $this->id_store_shop
                )) {
            return true;
        } else {
            return false;
        }
    }

    
}
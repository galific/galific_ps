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

class KbConfiguration extends Module
{

    const MODEL_FILE = 'model.sql';
    const MODEL_DATA_FILE = 'data.sql';
    const PARENT_TAB_CLASS = 'KBMPMainTab';
    const CSS_ADMIN_PATH = 'views/css/admin/';
    const CSS_FRONT_PATH = 'views/css/front/';
    const FRONT_PAGE_NAME = 'module-kbmarketplace-sellerfront';
    const SELL_CLASS_NAME = 'SELL';

    protected $custom_errors = array();

    public function __construct()
    {
        parent::__construct();
    }

    public function install()
    {
        if (!parent::install()
        || !$this->registerHook('displayBackOfficeHeader')
        || !$this->registerHook('displayHeader')
        || !$this->registerHook('displayAdminCustomersForm')
        || !$this->registerHook('displayCustomerAccountFormTop')
        || !$this->registerHook('additionalCustomerFormFields')
        || !$this->registerHook('actionCustomerAccountAdd')
        || !$this->registerHook('displayNav1')
        || !$this->registerHook('displayNav2')
        || !$this->registerHook('actionValidateOrder')
        || !$this->registerHook('displayCustomerAccount')
        || !$this->registerHook('actionObjectProductUpdateBefore')
        || !$this->registerHook('actionOrderStatusUpdate')
        || !$this->registerHook('actionProductCancel')
        || !$this->registerHook('actionObjectOrderDetailUpdateAfter')
        || !$this->registerHook('actionObjectOrderReturnUpdateAfter')
        || !$this->registerHook('actionCarrierUpdate')
        || !$this->registerHook('displayBackOfficeFooter')
        || !$this->registerHook('displayMyAccountBlock')
        || !$this->registerHook('displayKBLeftColumn')
        || !$this->registerHook('actionDispatcher')
        || !$this->registerHook('actionObjectLanguageAddAfter')
        || !$this->registerHook('actionObjectLanguageDeleteAfter')
        || !$this->registerHook('actionObjectCustomerMessageAddAfter')
        || !$this->registerHook('moduleRoutes')) {
            return false;
        }

        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall()
            || !$this->unregisterHook('displayBackOfficeHeader')
            || !$this->unregisterHook('displayHeader')
            || !$this->unregisterHook('displayAdminCustomersForm')
            || !$this->unregisterHook('displayCustomerAccountFormTop')
            || !$this->unregisterHook('additionalCustomerFormFields')
            || !$this->unregisterHook('actionCustomerAccountAdd')
            || !$this->unregisterHook('displayNav1')
            || !$this->unregisterHook('displayNav2')
            || !$this->unregisterHook('actionValidateOrder')
            || !$this->unregisterHook('displayCustomerAccount')
            || !$this->unregisterHook('actionObjectProductUpdateBefore')
            || !$this->unregisterHook('actionObjectProductCommentAddAfter')
            || !$this->unregisterHook('actionObjectProductCommentDeleteAfter')
            || !$this->unregisterHook('displayOrderConfirmation')
            || !$this->unregisterHook('actionObjectCustomerMessageAddAfter')
            || !$this->unregisterHook('actionOrderStatusUpdate')
            || !$this->unregisterHook('actionProductCancel')
            || !$this->unregisterHook('actionObjectOrderDetailUpdateAfter')
            || !$this->unregisterHook('actionObjectOrderReturnUpdateAfter')
            || !$this->unregisterHook('actionCarrierUpdate')
            || !$this->unregisterHook('displayBackOfficeFooter')
            || !$this->unregisterHook('displayMyAccountBlock')
            || !$this->unregisterHook('displayKBLeftColumn')
            || !$this->unregisterHook('actionDispatcher')
            || !$this->unregisterHook('actionObjectLanguageAddAfter')
            || !$this->unregisterHook('actionObjectLanguageDeleteAfter')
            || !$this->unregisterHook('moduleRoutes')) {
            return false;
        }

        Configuration::deleteByName('KB_MARKETPLACE');

        $sql = 'Select id_meta from ' . _DB_PREFIX_ . 'meta WHERE page = "' . pSQL(self::FRONT_PAGE_NAME) . '"';
        $page_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        $meta_obj = new Meta($page_id);
        $meta_obj->delete();
        return true;
    }

    protected function installModel()
    {
        $is_db_installed = Configuration::getGlobalValue('KB_MARKETPLACE_DB_INSTALLED');
        if (!$is_db_installed) {
            $installation_error = false;

            $rename_timestamp = time();
            foreach ($this->getMPTables() as $table_name) {
                $check_table = 'SELECT count(*) as value FROM information_schema.tables 
					WHERE table_schema = "' . _DB_NAME_ . '" AND table_name = "' . _DB_PREFIX_ . pSQL($table_name) . '"';
                $installed_table = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_table);
                if ((int) $installed_table > 0) {
                    $query = 'RENAME TABLE ' . _DB_PREFIX_ . pSQL($table_name) . ' TO '
                            . _DB_PREFIX_ . pSQL($table_name) . '_' . pSQL($rename_timestamp);
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($query);
                }
            }
            if (!file_exists(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_FILE)) {
                $this->custom_errors[] = $this->l('Model installation file not found.', 'kbconfiguration');
                $installation_error = true;
            } elseif (!is_readable(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_FILE)) {
                $this->custom_errors[] = $this->l('Model installation file is not readable.', 'kbconfiguration');
                $installation_error = true;
            } elseif (!$sql = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_FILE)) {
                $this->custom_errors[] = $this->l('Model installation file is empty.', 'kbconfiguration');
                $installation_error = true;
            }

            if (!$installation_error) {
                $sql = str_replace(array('_PREFIX_', 'ENGINE_TYPE'), array(_DB_PREFIX_, _MYSQL_ENGINE_), $sql);
                $sql = preg_split("/;\s*[\r\n]+/", trim($sql));
                foreach ($sql as $query) {
                    if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($query))) {
                        $installation_error = true;
                    }
                }
            }

            $languages = Language::getLanguages();

            if (!$installation_error) {
                Configuration::updateGlobalValue('KB_MARKETPLACE_DB_INSTALLED', true);
                if (!file_exists(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_DATA_FILE)) {
                    $this->custom_errors[] = $this->l('Model data installation file not found.', 'kbconfiguration');
                    $installation_error = true;
                } elseif (!is_readable(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_DATA_FILE)) {
                    $this->custom_errors[] = $this->l('Model data installation file is not readable.', 'kbconfiguration');
                    $installation_error = true;
                } elseif (!$sql = Tools::file_get_contents(_PS_MODULE_DIR_ . $this->name . '/' . self::MODEL_DATA_FILE)) {
                    $this->custom_errors[] = $this->l('Model data installation file is empty.', 'kbconfiguration');
                    $installation_error = true;
                }

                if (!$installation_error) {
                    $sql = str_replace(array('_PREFIX_'), array(_DB_PREFIX_), $sql);
                    $sql = preg_split("/;\s*[\r\n]+/", trim($sql));

                    //Insert Email Data
                    if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($sql[0]))) {
                        foreach ($this->getEmailTemplateData() as $key => $val) {
                            if ($id_email_template = KbEmail::getTemplateIdByName($key)) {
                                $email_obj = new KbEmail($id_email_template);
                                foreach ($languages as $lng) {
                                    $email_obj->subject[$lng['id_lang']] = $val['subject'];
                                    $email_obj->body[$lng['id_lang']] = $val['body'];
                                }
                                $email_obj->save();
                            }
                        }
                    } else {
                        $installation_error = true;
                        $this->custom_errors[] = $this->l('Email data is not installed.', 'kbconfiguration');
                    }

                    //Insert Seller Menus
                    if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(trim($sql[1]))) {
                        foreach ($this->getSellerMenus() as $key => $val) {
                            if ($id_seller_menu = KbSellerMenu::getMenuIdByModuleAndController('kbmarketplace', $key)) {
                                $menu_obj = new KbSellerMenu($id_seller_menu);
                                foreach ($languages as $lng) {
                                    $menu_obj->label[$lng['id_lang']] = $val['label'];
                                    $menu_obj->title[$lng['id_lang']] = $val['title'];
                                }
                                $menu_obj->save();
                            }
                        }
                    } else {
                        $installation_error = true;
                        $this->custom_errors[] = $this->l('Seller Menu data is not installed.', 'kbconfiguration');
                    }
                }
            }

            if (!$installation_error) {
                $front_url_write_name = 'sellers';
                $meta_obj = new Meta();
                $meta_obj->configurable = 1;
                $meta_obj->page = self::FRONT_PAGE_NAME;
                foreach ($languages as $lng) {
                    $meta_obj->title[$lng['id_lang']] = 'Authorized Sellers';
                    $meta_obj->url_rewrite[$lng['id_lang']] = $front_url_write_name;
                }
                if (!$meta_obj->save()) {
                    $this->custom_errors[] = $this->l('Installation Failed: Error Occurred while inserting url rewrite for seller listing on front.', 'kbconfiguration');
                    $installation_error = true;
                }
            }
            if ($installation_error) {
                $this->custom_errors[] = $this->l('Installation Failed: Error Occurred while installing models.', 'kbconfiguration');
                return false;
            }
        } else {
            $installation_error = false;
            // to drop id_customer connstraint from product review table to add compatibility with knowband product review plugin
            if (!Configuration::getGlobalValue('KB_MARKETPLACE_PRODUCT_REVIEW_COMPATIBILITY')) {
                $sql_review = 'ALTER TABLE '._DB_PREFIX_.'kb_mp_seller_product_review DROP FOREIGN KEY '._DB_PREFIX_.'kb_mp_seller_product_review_ibfk_2';
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql_review);
                
                Configuration::updateValue('KB_MARKETPLACE_PRODUCT_REVIEW_COMPATIBILITY', true);
            }
            // CHANGES OVER
            $modified_tables = $this->getModifiedTables();
            foreach ($modified_tables as $table => $columns) {
                $check = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                    'SHOW TABLES LIKE "' . _DB_PREFIX_ . pSQL($table) . '"'
                );
                if (count($check) > 0) {
                    foreach ($columns as $col => $script) {
                        $check_col_sql = 'SELECT count(*) FROM information_schema.COLUMNS 
                                WHERE COLUMN_NAME = "' . pSQL($col) . '" 
                                AND TABLE_NAME = "' . _DB_PREFIX_ . pSQL($table) . '" 
                                AND TABLE_SCHEMA = "' . _DB_NAME_ . '"';
                        $check_col = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($check_col_sql);
                        if ((int) $check_col == 0) {
                            if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($script)) {
                                $this->custom_errors[] = $this->l('Database Update Error: Not able to modified column', 'kbconfiguration') . ' - '
                                        . $col . $this->l(' of table', 'kbconfiguration') . ' - ' . $table;
                                $installation_error = true;
                            }
                        }
                    }
                }
            }
            if ($installation_error) {
                return false;
            }
        }
        
        //create close shop table
        /*Start- MK made changes on 28-05-18 for gdpr changes*/
        $query = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_mp_seller_shop_close_request` (
            `id_request` int(10) unsigned NOT NULL auto_increment,
            `id_seller` int(10) unsigned DEFAULT NULL,
            `id_shop` int(10) unsigned DEFAULT NULL,
            `seller_email` varchar(255) not null, 
            `account_delete` enum('0','1') NOT NULL DEFAULT '0',
            `approved` enum('0','1','3') NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_request`)
            
        ) ENGINE=" . _MYSQL_ENGINE_ . "  DEFAULT CHARSET=utf8;";
        Db::getInstance()->execute($query);
        
        $query = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_mp_gdpr_request` (
            `id_gdpr_request` int(10) unsigned NOT NULL auto_increment,
            `email` varchar(255) NOT NULL,
            `is_seller` int(10) unsigned DEFAULT NULL, 
            `id_shop` int(10) unsigned DEFAULT '0', 
            `type` varchar(255) NOT NULL,
            `user_agent` varchar(255) NOT NULL,
            `remote_address` varchar(255) NOT NULL,
            `authenticate` varchar(255) NOT NULL,
            `approved` enum('0','1','3') NOT NULL DEFAULT '0',
            `customer_seller_request` INT(10) UNSIGNED NOT NULL DEFAULT '0',
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`id_gdpr_request`)
           
        ) ENGINE=" . _MYSQL_ENGINE_ . "  DEFAULT CHARSET=utf8;";

        Db::getInstance()->execute($query);
        
        $languages = Language::getLanguages(false);

        //mp_seller_shop_close
        $report_data_template = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->name . '/views/templates/admin/report_gdpr.tpl');
        $report_data_template = str_replace('[', '{', $report_data_template);
        $report_data_template = str_replace(']', '}', $report_data_template);
        
        $mail_content = array(
            'mp_seller_shop_close' => array(
                'subject' => 'Customer has requested to close the shop',
                 'description' => 'This template is used to send request to Admin to close the Seller Shop',
                'body' => '<div style="padding: 10px;">
                        <p style="font-size:14px;">Hi Admin,</p>
                        <p></p>
                        <div style="margin-bottom: 10px; width: 100%; border: 1px solid #403744; background: #403744; color: #fff;">
                        <p style="font-size: 16px; text-align: center; font-weight: bold;">REQUEST TO CLOSE THE SHOP ON {{shop_name}}</p>
                        </div>
                        <p></p>
                        <p style="font-size: 14px; text-align: left;">The customer ({{seller_email}}) has requested to close the shopÂ \'{{shop_title}}\'.</p>
                        <p style="text-align: center; font-size: 14px;"></p>
                        <p style="text-align: center; font-size: 14px;">Kindly approve the request to close the customer\'s shop. Closing the shop is not an irreversible action.</p>
                        </div>',
            ),
            'mp_notify_seller_shop_close' => array(
                'subject' => 'You Shop has been closed',
                 'description' => 'This template is used to notify the seller about closing the shop',
                'body' => '<div style="padding: 10px;">
                        <p style="font-size: 14px;">Hi {{seller_name}},</p>
                        <p></p>
                        <div style="margin-bottom: 10px; width: 100%; border: 1px solid #414141; background: #414141; color: #fff;">
                        <p style="font-size: 15px; text-align: center; font-weight: bold;">YOUR SHOP HAS BEEN CLOSED onÂ {{shop_name}}</p>
                        </div>
                        <p style="text-align: center; font-size: 14px;"></p>
                        <p style="font-size: 14px; text-align: center;">Your request to close the shop \'{{shop_title}}\'Â  has been approved.</p>
                        <p style="text-align: center; font-size: 14px;">Your products, review and personal information have been removed from the store.</p>
                        </div>'
            ),
            'mp_request_portibility_gdpr' => array(
                'subject' => 'Confirm Your Personal Data Request',
                'description' => 'This template is used to send confirm request for data access to the customer',
                'body' => '<div style="padding: 10px;">
                            <p style="font-size:14px;">Hi,</p>
                            <p></p>
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #20a9de; background: #3db0aa; color: #fff;">
                            <p style="font-size: 16px; text-align: center; font-weight: bold;">Confirm Your Personal DataÂ Request</p>
                            </div>
                            <p></p>
                            <p style="text-align: center; font-size: 14px;"><span>Do you want to download your data?</p>
                            <p style="text-align: center; font-size: 14px;"><span>Click on the link below to confirm.Â Â </span></p>
                            <p style="text-align: center; font-size: 14px;"></p>
                            <table width="180" cellspacing="0" cellpadding="5" border="0" align="center" style="width: 180px;">
                            <tbody>
                            <tr>
                            <td bgcolor="#f01328" style="color: #ffffff; background-color: #3b3634; padding: 5px;" align="center">
                            <a href="{{confirm_link}}" style="display: block; text-decoration: none; line-height: 26px; font-weight: bold; margin: 0px 0px 10px 0px; font-family: Helvetica; font-size: 16px; background-color: #3b3634; color: #ffffff; width: 100%; margin-bottom: 0;" target="_blank"> Confirm </a></td>
                            </tr>
                            </tbody>
                            </table>
                            </div>'
            ),
            'mp_gdpr_report' => array(
                'subject' => 'Your Personal Data Report',
                'description' => 'This template is used to send personal report to the customer',
                'body' => $report_data_template
            ),
        );
        
        foreach ($mail_content as $key => $content) {
            $id_email_template = Db::getInstance()->getValue('SELECT id_email_template FROM ' . _DB_PREFIX_ . 'kb_mp_email_template where name="'.pSQL($key).'"');
            if (empty($id_email_template)) {
                Db::getInstance()->execute('INSERT INTO ' . _DB_PREFIX_ . 'kb_mp_email_template set end="f",name="'.pSQL($key).'",description="'.pSQL($content['description']).'",date_add=now(),date_upd=now()');
                $id_email_template = DB::getInstance()->Insert_ID();
            }
            if (!empty($id_email_template)) {
                $email_obj = new KbEmail($id_email_template);
                foreach ($languages as $lng) {
                    $email_obj->subject[$lng['id_lang']] = $content['subject'];
                    $email_obj->body[$lng['id_lang']] = html_entity_decode($content['body']);
                }
                $email_obj->save();
            }
        }
        /*End- MK made changes on 28-05-18 for gdpr changes*/
        return true;
    }
    
    
    public function hookActionObjectCustomerMessageAddAfter($param)
    {
        if (Module::isInstalled('kbmarketplace') && !Tools::getIsset('module') && Tools::getIsset('msgText')) {
            $config = Configuration::get('KB_MARKETPLACE');
            if ($config) {
                $check = false;
                if (isset(Context::getContext()->controller) && Context::getContext()->controller->controller_type == 'front') {
                    $check = true;
                } elseif (isset(Context::getContext()->employee->id) && empty(Context::getContext()->employee->id)) {
                    $check = true;
                }
                if (!$check) {
                    return;
                }
                if (Tools::getIsset('id_order')) {
                    $id_order = Tools::getValue('id_order');
                    if (!empty($id_order)) {
                        $id_seller = Db::getInstance()->getValue('SELECT id_seller FROM '._DB_PREFIX_.'kb_mp_seller_earning where id_order='.(int)$id_order);
                        if (!empty($id_seller)) {
                            $kbseller = new KbSeller($id_seller);
                            if ($kbseller->isSeller()) {
                                $order = new Order($id_order);
                                $customer = new Customer($order->id_customer);
                                $message = Tools::getValue('msgText');
                                if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                                    $message = Tools::nl2br(Tools::getValue('msgText'));
                                }
                                $product = new Product(Tools::getValue('id_product'));
                                $product_name = '';
                                if (Validate::isLoadedObject($product) && isset($product->name[(int) $order->id_lang])) {
                                    $product_name = $product->name[(int) $order->id_lang];
                                }
                                $varsTpl = array(
                                    '{lastname}' => $customer->lastname,
                                    '{firstname}' => $customer->firstname,
                                    '{id_order}' => $id_order,
                                    '{email}' => $customer->email,
                                    '{order_name}' => $order->getUniqReference(),
                                    '{message}' => $message,
                                    '{product_name}' => $product_name
                                );
                                
                                $notification_emails = $kbseller->getEmailIdForNotification();
                                foreach ($notification_emails as $em) {
                                    Mail::Send(
                                        (int) $order->id_lang,
                                        'order_customer_comment',
                                        Mail::l('Message from a customer', (int) $order->id_lang),
                                        $varsTpl,
                                        $em['email'],
                                        $em['title'],
                                        $customer->email,
                                        $customer->firstname.' '.$customer->lastname,
                                        null,
                                        null,
                                        _PS_MAIL_DIR_,
                                        true,
                                        (int) $order->id_shop
                                    );
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    /*
     * hook function to export the Customer Data when any other GDPR compliant plugin request to export
     * MK made changes on 30-05-18
     */
   

    private function getMPTables()
    {
        return array(
            'kb_mp_seller', 'kb_mp_seller_lang', 'kb_mp_seller_product', 'kb_mp_seller_product_tracking',
            'kb_mp_seller_review', 'kb_mp_seller_product_review', 'kb_mp_seller_category_request',
            'kb_mp_seller_config', 'kb_mp_seller_category', 'kb_mp_seller_category_tracking', 'kb_mp_reasons',
            'kb_mp_seller_earning', 'kb_mp_seller_order_detail', 'kb_mp_seller_transaction', 'kb_mp_seller_shipping',
            'kb_mp_email_template', 'kb_mp_email_template_lang', 'kb_mp_seller_menu', 'kb_mp_seller_menu_lang'
        );
    }

    /*
     * array(
     *      'table_name' => array(
     *          'new_column_name' => 'script'
     *      )
     * )
     */

    private function getModifiedTables()
    {
        return array(
            'kb_mp_seller' => array(
                'payment_info' => 'ALTER TABLE `' . _DB_PREFIX_ . 'kb_mp_seller` 
                    DROP FOREIGN KEY `' . _DB_PREFIX_ . 'kb_mp_seller_ibfk_1`;
                    ALTER TABLE `' . _DB_PREFIX_ . 'kb_mp_seller` DROP INDEX `id_customer`;
                    ALTER TABLE `' . _DB_PREFIX_ . 'kb_mp_seller` 
                    CHANGE COLUMN `id_paypal` `payment_info` TEXT NULL DEFAULT NULL'
            ),
            'kb_mp_seller_earning' => array(
                'can_handle_order' => 'ALTER TABLE `' . _DB_PREFIX_ . 'kb_mp_seller_earning` 
                    ADD `can_handle_order` TINYINT(1) NOT NULL DEFAULT "0"'
            ),
            'kb_mp_seller_lang' => array(
                'profile_url' => 'ALTER TABLE `' . _DB_PREFIX_ . 'kb_mp_seller_lang` 
                    ADD `profile_url` text DEFAULT NULL'
            )
        );
    }

    private function getEmailTemplateData()
    {
        $data = array(
            'mp_welcome_seller' => array(
                'subject' => 'Market Place Seller Welcome',
                'body' => '<div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000000;">
                            <p style="color: #000; text-align: center; font-size: 15px;">
                            <strong>Market Place Seller Welcome</strong></p>
                            </div>
                            <p>Thank You For Registering as Seller.</p>
                            <p>Your Email: {{email}}</p>
                            <p>Your Name: {{full_name}}</p>
                            <p>Once the Admin approves your seller account, you can start selling on our website.</p>
                            </div>'
            ),
            'mp_seller_account_approval' => array(
                'subject' => 'Market Place Seller Account Approved',
                'body' => '<div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000000;">
                            <p style="color: #000; text-align: center; font-size: 15px;">
                            <strong>Market Place Seller Approved</strong></p>
                            </div>
                            <p>Hi {{full_name}},</p>
                            <p>Congrats, Your seller account is approved and activated.
                            Now you can start selling on our website.</p>
                            <p>Your Email: {{email}}</p>
                            <p>Your Name: {{full_name}}</p>
                            </div>'
            ),
            'mp_seller_account_disapproval' => array(
                'subject' => 'Market Place Seller Account Disapproved',
                'body' => '<div style="padding: 10px;">
                        <div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
                        <p style="font-size: 15px; text-align: center; font-weight: bold;">
                        Market Place Seller Disapproved</p>
                        </div>
                        <p>Hi {{full_name}},</p>
                        <p>Sorry to inform you, Your seller account request is rejected on our website.</p>
                        <p>But do not worry you can request again for your account.</p>
                        <p><b>Reason for Disapproval:</b></p>
                        <pre>{{disapproval_reason}}</pre>
                        </div>'
            ),
            'mp_seller_registration_notification_admin' => array(
                'subject' => 'Market Place Seller Registration Notification',
                'body' => '<div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000000;">
                            <p style="color: #000000; font-size: 15px; text-align: center;">
                            Market Place Seller Registration Notification</p>
                            </div>
                            <p>A customer just registered as seller on your website.</p>
                            <p><b>Details of the Customer are as follows: </b></p>
                            <p><b>Email: </b>{{email}}</p>
                            <p><b>Name:</b> {{full_name}}</p>
                            </div>'
            ),
            'mp_seller_account_approval_after_disapprove' => array(
                'subject' => 'Seller again Requested for Approving his Account',
                'body' => '<p>Hi Admin,</p>
                            <div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000000;">
                            <p style="font-size: 15px; text-align: center; font-weight: bold;">
                            Customer has just requested for approving his seller account, after disapproved by you</p>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Seller Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;"><span><b>Store:</b>
                            {{shop_title}} </span><br /><span><b>Name:</b> {{seller_name}}</span>
                            <br /><span><b>Email:</b> {{seller_email}}</span> <br />
                            <span><b>Contact:</b> {{seller_contact}}</span></div>
                            </div>
                            
                            </div>'
            ),
            'mp_new_product_notification_admin' => array(
                'subject' => 'New Product Approval Request',
                'body' => '<div style="padding: 10px;">
                        <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000;">
                        <p style="color: #000000; font-size: 15px; text-align: center;">
                        New product is just added to our store by <b>{{seller_title}}</b>.
                        </p>
                        </div>
                        <br />
                        <p><b>Product Details:</b></p>
                        <p><span><b>Product Name:</b> {{product_name}}</span> <br />
                        <span><b>SKU:</b> {{product_sku}}</span><br />
                        <span><b>Price:</b> {{product_price}}</span></p>
                        <br />
                        <p><b>Seller Details:</b></p>
                        <p><span><b>Name:</b> {{seller_name}}</span><br /><span> <b>Email:</b>
                        {{seller_email}}</span><br /><span> <b>Contact:</b> {{seller_contact}}</span>
                        </p>
                        <br />
                        <p>Please go to <a href="{shop_url}">store</a> and approve this product.</p>
                        </div>'
            ),
            'mp_category_request_notification_admin' => array(
                'subject' => 'New Category Request Notitfication',
                'body' => '<p>Hi Admin,</p>
                            <div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000;">
                            <p style="color: #000000; font-size: 15px; text-align: center;">
                            One of your seller has requested for new category approval.</p>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Requested Category Details:</p>
                            <p><b>Requested Category</b>:<br />{{requested_category}}</p>
                            <p><b>Reason</b>:</p>
                            <pre><span>{{reason}}</span></pre>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Seller Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;"><span><b>Store:</b>
                            {{shop_title}} </span><br /><span><b>Name:</b> {{seller_name}}</span>
                            <br /><span><b>Email:</b> {{seller_email}}</span> <br />
                            <span><b>Contact:</b> {{seller_contact}}</span></div>
                            </div>
                            <p>Please go to <a href="{shop_url}">store</a> and approve the requested category.</p>
                            </div>'
            ),
            'mp_category_request_approved' => array(
                'subject' => 'Category Approval Notification',
                'body' => '<div style="padding: 10px;">
                            <p>Hi {{seller_name}},</p>
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #3fad1c;">
                            <p style="color: #000000; font-size: 15px; text-align: center;">
                            <b>Congratulations!</b> Your request for new category has been approved.
                            Now you can add your products into this new category.</p>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Requested Category:</p>
                            <p>{{requested_category}}</p>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;"><span><b>Store:</b>
                            {{shop_title}}</span><br /><span><b>Name:</b> {{seller_name}}</span><br />
                            <span><b>Email:</b> {{seller_email}}</span><br /><span><b>Contact:</b>
                            {{seller_contact}}</span></div>
                            </div>
                            </div>'
            ),
            'mp_category_request_disapproved' => array(
                'subject' => 'Category Disapproval Notification',
                'body' => '<div style="padding: 10px;">
                            <p>Hi {{seller_name}},</p>
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
                            <p style="color: #000000; font-size: 15px;
                            text-align: center;"><b>Sorry!</b>
                            Your request for new category has been disapproved by Admin.</p>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Requested Category Details:</p>
                            <p><b>Name:</b><br />{{requested_category}}</p>
                            <p><b>Reason:</b></p>
                            <pre><span>{{comment}}</span></pre>
                            </div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <span><b>Store:</b> {{shop_title}}</span><br /><span><b>Name:</b>
                            {{seller_name}}</span><br /><span><b>Email:</b> {{seller_email}}</span>
                            <br /><span><b>Contact:</b> {{seller_contact}}</span></div>
                            <p>To again request, please go to <a href="{shop_url}">store</a> and make new request.</p>
                            </div>
                            </div>'
            ),
            'mp_product_disapproval_notification' => array(
                'subject' => 'Your Product has been Disapproved',
                'body' => '<div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
                            <p style="color: #000; text-align: center; font-size: 15px;">
                            <strong>Your product has been disapproved on {shop_name}.</strong></p>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p><b>Reason For Disapproving Product:</b></p>
                            <p></p>
                            <pre><span>{{reason}}</span></pre>
                            </div>
                            <br />
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Product Details:</p>
                            <div style="margin-bottom: 10px; width: 100%;"><span>
                            <b>Product Name:</b> {{product_name}}</span><br /><span><b>SKU:</b>
                            {{product_sku}}</span><br /><span><b>Price:</b> {{product_price}}</span></div>
                            <br />
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;"><span><b>Store:</b>
                            {{shop_title}}</span> <br /><span><b>Name:</b> {{seller_name}}
                            </span><br /><span> <b>Email:</b> {{seller_email}}</span>
                            <br /><span><b>Contact:</b> {{seller_contact}}</span></div>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p>To request for approving your product. Please contact to support.</p>
                            </div>
                            </div>'
            ),
            'mp_product_approval_notification' => array(
                'subject' => 'Your Product has been Approved',
                'body' => '<div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #3fad1c;">
                            <p style="font-size: 15px; text-align: center;
                            font-weight: bold;">
                            Your product has been approved and is available for sale.
                            Please go to <a href="{shop_url}">store</a> and review your product.
                            </p>
                            </div>
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Product Details:</p>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <span> <b>Product Name:</b> {{product_name}}</span><br />
                            <span><b>SKU:</b> {{product_sku}}</span><br />
                            <span> <b>Price:</b> {{product_price}}</span></div>
                            <br />
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <span><b>Store:</b> {{shop_title}} </span><br /><span><b>Name:</b>
                            {{seller_name}}</span><br /><span><b>Email:</b>
                            {{seller_email}}</span> <br />
                            <span><b>Contact:</b> {{seller_contact}}</span></div>
                            </div>'
            ),
            'mp_product_delete_notification' => array(
                'subject' => 'Your Product has been Deleted',
                'body' => '<div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
                            <p style="color: #000; text-align: center; font-size: 15px;">
                            <strong>Your product has been deleted from {shop_name}.</strong></p>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p><b> Reason For Deleting Product:</b></p>
                            <p></p>
                            <pre><span>{{reason}}</span></pre>
                            </div>
                            <br />
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Product Details:</p>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <span> <b>Product Name:</b> {{product_name}}</span> <br />
                            <span><b>SKU:</b> {{product_sku}}</span><br />
                            <span> <b>Price:</b> {{product_price}}</span></div>
                            <br />
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <span><b>Store:</b> {{shop_title}}</span> <br />
                            <span><b>Name:</b> {{seller_name}}</span><br />
                            <span><b>Email:</b> {{seller_email}}</span><br />
                            <span><b>Contact:</b> {{seller_contact}}</span></div>
                            <div style="margin-bottom: 10px; width: 100%;"></div>
                            </div>'
            ),
            'mp_seller_review_approval_request_admin' => array(
                'subject' => 'New review is posted on seller',
                'body' => '<p>Hi Admin,</p>
                            <div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000000;">
                            <p style="color: #000; text-align: center; font-size: 15px;">
                            <strong>One of the our customer has posted a review for {{shop_title}}.
                            </strong></p>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Review given by customer:</p>
                            <p><b>Title</b>:<br /> {{review_title}}</p>
                            <p><b>Comment</b>:</p>
                            <pre><span>{{review_comment}}</span></pre>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Seller Details:</p>
                            <div style="margin-bottom: 10px; width: 100%;">
                            <span><b>Store:</b> {{shop_title}}</span><br />
                            <span> <b>Name:</b> {{seller_name}}</span><br />
                            <span><b>Email:</b> {{seller_email}}</span><br />
                            <span> <b>Contact:</b> {{seller_contact}}</span></div>
                            </div>
                            <p>Please go to <a href="{shop_url}">store</a> and approve new review.</p>
                            </div>'
            ),
            'mp_seller_review_notification' => array(
                'subject' => 'New review is just posted for you',
                'body' => '<p>Hi {{seller_name}},</p>
                        <div style="padding: 10px;">
                        <div style="margin-bottom: 10px; width: 100%; border: 1px solid #000000;">
                        <p style="color: #000000; text-align: center;
                        font-size: 15px;">
                        <strong>One of the your customer has posted a review for you.
                        </strong></p>
                        </div>
                        <br />
                        <div style="margin-bottom: 10px; width: 100%;">
                        <p style="text-decoration: underline; font-style: italic;
                        font-size: 15px; font-weight: bold;">Review given by customer:</p>
                        <p><b>Title</b>:<br /> {{review_title}}</p>
                        <p><b>Comment</b>:</p>
                        <pre><span>{{review_comment}}</span></pre>
                        </div>
                        <br />
                        <div style="margin-bottom: 10px; width: 100%;">
                        <p style="text-decoration: underline; font-style: italic;
                        font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                        <div style="margin-bottom: 10px; width: 100%;"><span><b>Store:</b>
                        {{shop_title}}</span><br /><span><b>Name:</b> {{seller_name}}</span><br />
                        <span><b>Email:</b> {{seller_email}}</span><br /><span><b>Contact:</b>
                        {{seller_contact}}</span></div>
                        </div>
                        <p>Please go to <a href="{shop_url}">store</a> to view review status.</p>
                        </div>'
            ),
            'mp_seller_amount_credit_transfer_notification' => array(
                'subject' => 'Admin has just credited your paypal account',
                'body' => '<p>Hi {{seller_name}},</p>
                            <div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #3fad1c;">
                            <p style="color: #000; text-align: center;
                            font-size: 15px;">
                            <strong>Your Paypal Account is just Credited by Admin with amount of {{amount}}
                            </strong></p>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Comment on Transaction:</p>
                            <p></p>
                            <pre><span>{{comment}}</span></pre>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Your Details on Store:</p>
                            <div style="margin-bottom: 10px; width: 100%;"><span><b>Store:</b>
                            {{shop_title}}</span><br /><span><b>Name:</b> {{seller_name}}</span><br />
                            <span><b>Email:</b> {{seller_email}}</span><br /><span><b>Contact:</b>
                            {{seller_contact}}</span></div>
                            </div>
                            <p>Please go to <a href="{shop_url}">
                            store</a> to check your total paid and balance amount by admin.
                            </p>
                            </div>'
            ),
            'mp_seller_review_approved_to_customer' => array(
                'subject' => 'Your review has been approved by admin',
                'body' => '<p>Hi {{customer_name}},</p>
                            <div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #3fad1c;">
                            <p style="color: #000000; font-size: 15px;
                            text-align: center;">
                            Thanks for giving your time on our store and giving us your feedback for sellers.
                            Your review has been approved by admin on {{store_name}}
                            for seller {shop_name} and listed on store
                            </p>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Review given by you:</p>
                            <p></p>
                            <pre><span>{{comment}}</span></pre>
                            </div>
                            </div>'
            ),
            'mp_seller_review_approved_to_seller' => array(
                'subject' => 'Review given by customer has been approved by admin',
                'body' => '<p>Hi {{seller_name}},</p>
                            <div style="padding: 10px;">
                            <div style="margin-bottom: 10px; width: 100%; border: 1px solid #3fad1c;">
                            <p style="color: #000; text-align: center; font-size: 15px;">
                            <strong>Review given by customer upon you has been approved by
                            admin on {{store_name}} and listed on store</strong></p>
                            </div>
                            <br />
                            <div style="margin-bottom: 10px; width: 100%;">
                            <p style="text-decoration: underline; font-style: italic;
                            font-size: 15px; font-weight: bold;">Review Detail:</p>
                            <p></p>
                            <pre><span>{{comment}}</span></pre>
                            </div>
                            </div>'
            ),
            'mp_seller_review_disspproved_to_seller' => array(
                'subject' => 'Review given by customer has been disapproved by admin',
                'body' => '<p>Hi {{seller_name}},</p>
                        <div style="padding: 10px;">
                        <div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
                        <p style="text-align: center; color: #000000; font-size: 15px;">
                        <strong>
                        Review given by customer on your shop "{{store_name}}"
                        has been disapproved by admin</strong></p>
                        </div>
                        <br />
                        <div style="margin-bottom: 10px; width: 100%;">
                        <p style="text-decoration: underline; font-style: italic;
                        font-size: 15px; font-weight: bold;">Review Detail:</p>
                        <p></p>
                        <pre><span>{{comment}}</span></pre>
                        </div>
                        <div style="margin-bottom: 10px; width: 100%;">
                        <p style="text-decoration: underline; font-style: italic;
                        font-size: 15px; font-weight: bold;">
                        Reason for disapproving:</p>
                        <p></p>
                        <pre><span>{{reason}}</span></pre>
                        </div>
                        </div>'
            ),
            'mp_seller_review_disspproved_to_customer' => array(
                'subject' => 'Review given by you has been disapproved by admin',
                'body' => '<p>Hi {{customer_name}},</p>
<div style="padding: 10px;">
<div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
<p style="color: #000; text-align: center; font-size: 15px;">
Thanks for giving your time on our store and giving us your feedback for sellers.
Unfortunately, your review has been dissapproved by admin on {{store_name}} for seller {shop_name}.
</p>
</div>
<br />
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic; font-size: 15px; font-weight: bold;">Review Detail:</p>
<p></p>
<pre><span>{{comment}}<span></span></span></pre>
</div>
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic;
font-size: 15px; font-weight: bold;">Reason for disapproving:</p>
<p></p>
<pre><span>{{reason}}</span></pre>
</div>
</div>'
            ),
            'mp_seller_amount_debit_transfer_notification' => array(
                'subject' => 'Admin has just debited some amount from balance amount',
                'body' => '<p>Hi {{seller_name}},</p>
<div style="padding: 10px;">
<div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
<p style="color: #000; text-align: center; font-size: 15px;">
<strong>Admin has just deducted {{amount}} from your current balance
</strong></p>
</div>
<br />
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic; font-size: 15px; font-weight: bold;">Reason for Deduction:</p>
<p></p>
<pre><span>{{comment}}</span></pre>
</div>
<br />
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic;
font-size: 15px; font-weight: bold;">Your Details on Store:</p>
<div style="margin-bottom: 10px; width: 100%;"><span>
<b>Store:</b> {{shop_title}}</span><br /><span><b>Name:</b>
{{seller_name}}</span> <br /><span><b>Email:</b> {{seller_email}}</span>
<br /><span> <b>Contact:</b> {{seller_contact}}</span></div>
</div>
<p>Please go to <a href="{shop_url}">store</a> to check your updated total paid and balance amount by admin.</p>
</div>'
            ),
            'mp_seller_review_delete_to_seller' => array(
                'subject' => 'Review given by customer has been deleted by admin',
                'body' => '<p>Hi {{seller_name}},</p>
<div style="padding: 10px;">
<div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
<p style="color: #000; text-align: center; font-size: 15px;">
<strong>Review given by customer upon you has been deleted by admin on {{store_name}}
</strong></p>
</div>
<br />
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic; font-size: 15px; font-weight: bold;">Review Detail:</p>
<p></p>
<pre><span>{{comment}}</span></pre>
</div>
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic; font-size: 15px; font-weight: bold;">Reason for delete:</p>
<p></p>
<pre><span>{{reason}}</span></pre>
</div>
</div>'
            ),
            'mp_seller_review_delete_to_customer' => array(
                'subject' => 'Review given by you has been deleted',
                'body' => '<p>Hi {{customer_name}},</p>
<div style="padding: 10px;">
<div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
<p style="color: #000; text-align: center; font-size: 15px;">
Thanks for giving your time on our store and giving us your feedback for sellers.
Your review has been deleted by admin on {{store_name}} for seller {shop_name}.</p>
</div>
<br />
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic; font-size: 15px; font-weight: bold;">Review Detail:</p>
<p></p>
<pre><span>{{comment}}</span></pre>
</div>
<div style="margin-bottom: 10px; width: 100%;">
<p style="text-decoration: underline; font-style: italic; font-size: 15px; font-weight: bold;">Reason for deleting:</p>
<p></p>
<pre><span>{{reason}}</span></pre>
</div>
</div>'
            ),
            'mp_seller_account_enable' => array(
                'subject' => 'Your Seller Account Has Been Enabled',
                'body' => '<div style="padding: 10px;">
<div style="margin-bottom: 10px; width: 100%; border: 1px solid #3fad1c;">
<p style="color: #000; text-align: center; font-size: 15px;"><strong>Your Seller Account Has Been Enabled</strong></p>
</div>
<p>Hey There,</p>
<p>Congrats, Your seller account has been enabled. Now you can start selling on our website.</p>
<p>Your Email: {{email}}</p>
<p>Your Name: {{full_name}}</p>
</div>'
            ),
            'mp_seller_account_disable' => array(
                'subject' => 'Your Seller Account Has Been Disabled',
                'body' => '<div style="padding: 10px;">
<div style="margin-bottom: 10px; width: 100%; border: 1px solid #ff0000;">
<p style="color: #000; text-align: center; font-size: 15px;"><strong>Your Seller Account Has Been Disabled</strong></p>
</div>
<p>Hey There,</p>
<p>Sorry to inform you, because of some inappropriate activities, your seller account has been disabled.</p>
<p>But do not worry you can request again for your account.</p>
</div>'
            ),
            
        );
        return $data;
    }

    private function getSellerMenus()
    {
        $data = array(
            'dashboard' => array(
                'label' => $this->l('Dashboard', 'kbconfiguration'),
                'title' => $this->l('Dashboard', 'kbconfiguration')
            ),
            'seller' => array(
                'label' => $this->l('Seller Profile', 'kbconfiguration'),
                'title' => $this->l('Seller Profile', 'kbconfiguration')
            ),
            'product' => array(
                'label' => $this->l('Products', 'kbconfiguration'),
                'title' => $this->l('Products', 'kbconfiguration')
            ),
            'order' => array(
                'label' => $this->l('Orders', 'kbconfiguration'),
                'title' => $this->l('Orders', 'kbconfiguration')
            ),
            'productreview' => array(
                'label' => $this->l('Product Reviews', 'kbconfiguration'),
                'title' => $this->l('Product Reviews', 'kbconfiguration')
            ),
            'sellerreview' => array(
                'label' => $this->l('My Reviews', 'kbconfiguration'),
                'title' => $this->l('My Reviews', 'kbconfiguration')
            ),
            'earning' => array(
                'label' => $this->l('Earning', 'kbconfiguration'),
                'title' => $this->l('Earning', 'kbconfiguration')
            ),
            'transaction' => array(
                'label' => $this->l('Transactions', 'kbconfiguration'),
                'title' => $this->l('Transactions', 'kbconfiguration')
            ),
            'payoutrequest' => array(
                'label' => $this->l('Payout Request', 'kbconfiguration'),
                'title' => $this->l('Payout Request', 'kbconfiguration')
            ),
            'category' => array(
                'label' => $this->l('Category Request', 'kbconfiguration'),
                'title' => $this->l('Category Request', 'kbconfiguration')
            ),
            'shipping' => array(
                'label' => $this->l('Shipping', 'kbconfiguration'),
                'title' => $this->l('Shipping', 'kbconfiguration')
            )
        );

        return $data;
    }

    protected function getDefaultSettings()
    {
        $settings = 0;
        return $settings;
    }

    protected function installMarketPlaceTabs()
    {
        $parentTab = new Tab();
        $parentTab->name = array();
        $parent_tab = array(
            'bg' => 'Knowband Marketplace',
            'cs' => 'Knowband Marketplace',
            'de' => 'Knowband Markt',
            'el' => 'Knowband Marketplace',
            'es' => 'Knowband mercado',
            'en' => 'Knowband Marketplace',
            'fi' => 'Knowband Marketplace',
            'fr' => 'Knowband marchÃ©',
            'hu' => 'Knowband Marketplace',
            'it' => 'Knowband Marketplace',
            'nl' => 'Knowband Marketplace',
            'pl' => 'Knowband GieÅ‚da',
            'pt' => 'Knowband mercado',
            'ro' => 'Knowband PiaÈ›Äƒ',
            'ru' => 'Knowband Marketplace',
            'sk' => 'Knowband Marketplace',
            'sv' => 'Knowband Marketplace',
            'tr' => 'Knowband Pazaryeri',
            'uk' => 'Knowband Marketplace'
        );
        foreach (Language::getLanguages(true) as $lang) {
            if (isset($parent_tab[$lang['iso_code']])) {
                $parentTab->name[$lang['id_lang']] = $parent_tab[$lang['iso_code']];
            } else {
                $parentTab->name[$lang['id_lang']] = $parent_tab['en'];
        }
        }

        $parentTab->class_name = self::PARENT_TAB_CLASS;
        $parentTab->module = $this->name;
        $parentTab->active = 1;
        $parentTab->icon = 'store';
        $parentTab->id_parent = Tab::getIdFromClassName(self::SELL_CLASS_NAME);
        $parentTab->add();

        $id_parent_tab = (int) Tab::getIdFromClassName(self::PARENT_TAB_CLASS);

        $admin_menus = $this->getAdminMenus();

        foreach ($admin_menus as $menu) {
            $tab = new Tab();
            foreach (Language::getLanguages(true) as $lang) {
                if (isset($menu['name'][$lang['iso_code']])) {
                    $tab->name[$lang['id_lang']] = $menu['name'][$lang['iso_code']];
                } else {
                    $tab->name[$lang['id_lang']] = $menu['name']['en'];
            }
            }
            $tab->class_name = $menu['class_name'];
            $tab->module = $this->name;
            $tab->active = $menu['active'];
            $tab->id_parent = $id_parent_tab;
            $tab->add($this->id);
        }
        return true;
    }

    private function getAdminMenus()
    {
        return array(
            array(
                'class_name' => 'AdminKbMarketPlaceSetting',
                'active' => 1,
                'name' => array(
                            'bg' => 'ï¿½?Ð°ï¿½?Ñ‚Ñ€Ð¾Ð¹ÐºÐ¸',
                            'cs' => 'NastavenÃ­',
                            'de' => 'die Einstellungen',
                            'el' => 'Î¡Ï…Î¸Î¼Î¯ÏƒÎµÎ¹Ï‚',
                            'es' => 'ajustes',
                            'en' => 'Settings',
                            'fi' => 'asetukset',
                            'fr' => 'RÃ©glages',
                            'hu' => 'BeÃ¡llÃ­tÃ¡sok',
                            'it' => 'impostazioni',
                            'nl' => 'instellingen',
                            'pl' => 'Ustawienia',
                            'pt' => 'DefiniÃ§Ãµes',
                            'ro' => 'SetÄƒri',
                            'ru' => 'Ð½Ð°ï¿½?Ñ‚Ñ€Ð¾Ð¹ÐºÐ¸',
                            'sk' => 'nastavenie',
                            'sv' => 'instÃ¤llningar',
                            'tr' => 'Ayarlar',
                            'uk' => 'Ð½Ð°ï¿½?Ñ‚Ñ€Ð¾Ð¹ÐºÐ¸',
                        )
            ),
            array(
                'class_name' => 'AdminKbMPGDPRSetting',
                'active' => 0,
                'name' => array(
                            'bg' => 'GDPR ï¿½?Ð°ï¿½?Ñ‚Ñ€Ð¾Ð¹ÐºÐ¸',
                            'cs' => 'NastavenÃ­ GDPR',
                            'de' => 'BIPR Einstellungen',
                            'el' => 'Î¡Ï…Î¸Î¼Î¯ÏƒÎµÎ¹Ï‚ Î‘Î•Î³Ï‡Î Î ',
                            'es' => 'Ajustes GDPR',
                            'en' => 'GDPR Settings',
                            'fi' => 'GDPR Asetukset',
                            'fr' => 'GDPR RÃ©glages',
                            'hu' => 'GDPR beÃ¡llÃ­tÃ¡sok',
                            'it' => 'Impostazioni GDPR',
                            'nl' => 'GDPR Instellingen',
                            'pl' => 'Ustawienia PKBR',
                            'pt' => 'ConfiguraÃ§Ãµes PIBR',
                            'ro' => 'SetÄƒri GDPR',
                            'ru' => 'ï¿½?Ð°ï¿½?Ñ‚Ñ€Ð¾Ð¹ÐºÐ¸ GDPR',
                            'sk' => 'nastavenie GDPR',
                            'sv' => 'BRP InstÃ¤llningar',
                            'tr' => 'GDPR AyarlarÄ±',
                            'uk' => 'Ð½Ð°Ð»Ð°ÑˆÑ‚ÑƒÐ²Ð°Ð½Ð½ï¿½? GDPR',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerList',
                'active' => 1,
                'name' => array(
                            'bg' => 'Ð¡Ð¿Ð¸ï¿½?ÑŠÐº ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡Ð¸Ñ‚Ðµ',
                            'cs' => 'Seznam prodejcÅ¯',
                            'de' => 'VerkÃ¤ufer-Liste',
                            'el' => 'Î›Î¯ÏƒÏ„Î± Ï€Ï‰Î»Î·Ï„Î­Ï‚',
                            'es' => 'Lista de los vendedores',
                            'en' => 'Sellers List',
                            'fi' => 'Sellers List',
                            'fr' => 'vendeurs Liste',
                            'hu' => 'Sellers lista',
                            'it' => 'Lista venditori',
                            'nl' => 'verkopers List',
                            'pl' => 'Lista sprzedawcÃ³w',
                            'pt' => 'Lista vendedores',
                            'ro' => 'ListÄƒ Sellers',
                            'ru' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†Ð¾Ð²',
                            'sk' => 'zoznam predajcov',
                            'sv' => 'sÃ¤ljare Lista',
                            'tr' => 'SatÄ±cÄ±lar listesi',
                            'uk' => 'ï¿½?Ð¿Ð¸ï¿½?Ð¾Ðº Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†Ñ–Ð²',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerApprovalList',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ Ð¡Ð¿Ð¸ï¿½?ÑŠÐº Ð¾Ð´Ð¾Ð±Ñ€ÐµÐ½Ð¸Ðµ Ð½Ð° Ð¿Ñ€Ð¾Ñ„Ð¸Ð»Ð°',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Seznam SchvÃ¡lenÃ­ Account',
                            'de' => 'VerkÃ¤uferkonto Zulassungsliste',
                            'el' => 'ÎŸ Ï€Ï‰Î»Î·Ï„Î®Ï‚ ÎšÎ±Ï„Î¬Î»Î¿Î³Î¿Ï‚ ÎŸ Î»Î¿Î³Î±ï¿½?Î¹Î±ÏƒÎ¼ÏŒÏ‚ ÎˆÎ³Îºï¿½?Î¹ÏƒÎ·',
                            'es' => 'Lista AprobaciÃ³n Cuenta de vendedor',
                            'en' => 'Seller Account Approval List',
                            'fi' => 'MyyjÃ¤ tilihyvÃ¤ksyntÃ¤Ã¤n List',
                            'fr' => 'Le vendeur Liste des comptes d approbation',
                            'hu' => 'Az eladÃ³ fiÃ³kjÃ³vÃ¡hagyÃ¡sra listÃ¡ja',
                            'it' => 'Venditore Lista account Soddisfazione',
                            'nl' => 'Verkoper Account Goedkeuring List',
                            'pl' => 'Sprzedawca Lista Zatwierdzenie konta',
                            'pt' => 'Vendedor lista de contas de AprovaÃ§Ã£o',
                            'ro' => 'Vanzator Lista cont aprobare',
                            'ru' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ† ï¿½?Ñ‡ÐµÑ‚Ð° Ð£Ñ‚Ð²ÐµÑ€Ð¶Ð´ÐµÐ½Ð¸Ðµ',
                            'sk' => 'PredÃ¡vajÃºci Zoznam SchvÃ¡lenie Account',
                            'sv' => 'SÃ¤ljare Account GodkÃ¤nnande Lista',
                            'tr' => 'SatÄ±cÄ± Hesap OnayÄ± Listesi',
                            'uk' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ†ÑŒ Ñ€Ð°Ñ…ÑƒÐ½ÐºÑƒ Ð—Ð°Ñ‚Ð²ÐµÑ€Ð´Ð¶ÐµÐ½Ð½ï¿½?',
                        )
                
            ),
            array(
                'class_name' => 'AdminKbProductApprovalList',
                'active' => 1,
                'name' => array(
                            'bg' => 'Ð¡Ð¿Ð¸ï¿½?ÑŠÐºÐ° ï¿½? Ð¿Ñ€Ð¾Ð´ÑƒÐºÑ‚Ð¸ ÐžÐ´Ð¾Ð±Ñ€ÐµÐ½Ð¸Ðµ',
                            'cs' => 'Seznam schvÃ¡lenÃ­ vÃ½robku',
                            'de' => 'Produktzulassung Liste',
                            'el' => 'Î›Î¯ÏƒÏ„Î± ÎˆÎ³Îºï¿½?Î¹ÏƒÎ· Î ï¿½?Î¿ÏŠÏŒÎ½Ï„Ï‰Î½',
                            'es' => 'Lista de aprobaciÃ³n del producto',
                            'en' => 'Product Approval List',
                            'fi' => 'TuotehyvÃ¤ksyntÃ¤ List',
                            'fr' => 'Liste d homologation de produit',
                            'hu' => 'TermÃ©k jÃ³vÃ¡hagyÃ¡si listÃ¡ja',
                            'it' => 'Elenco prodotti Omologazione',
                            'nl' => 'Productkeuringsnummer List',
                            'pl' => 'Zatwierdzenie listy produktÃ³w',
                            'pt' => 'Lista de Produtos de AprovaÃ§Ã£o',
                            'ro' => 'Produs Lista de aprobare',
                            'ru' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº ÑƒÑ‚Ð²ÐµÑ€Ð¶Ð´ÐµÐ½Ð¸ï¿½? Ð¿Ñ€Ð¾Ð´ÑƒÐºÑ‚Ð°',
                            'sk' => 'Zoznam schvÃ¡lenie vÃ½robku',
                            'sv' => 'ProduktgodkÃ¤nnande Lista',
                            'tr' => 'ÃœrÃ¼n Onay Listesi',
                            'uk' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº Ð·Ð°Ñ‚Ð²ÐµÑ€Ð´Ð¶ÐµÐ½Ð½ï¿½? Ð¿Ñ€Ð¾Ð´ÑƒÐºÑ‚Ñƒ',
                        )
            ),
            array(
                'class_name' => 'AdminKbProductList',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ ÐŸÑ€Ð¾Ð´ÑƒÐºÑ‚Ð¸',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Products',
                            'de' => 'VerkÃ¤ufer Produkte',
                            'el' => 'Î Ï‰Î»Î·Ï„Î®Ï‚ Î ï¿½?Î¿ÏŠÏŒÎ½Ï„Î±',
                            'es' => 'los productos del vendedor',
                            'en' => 'Seller Products',
                            'fi' => 'myyjÃ¤ Tuotteet',
                            'fr' => 'Produits du vendeur',
                            'hu' => 'Az eladÃ³ TermÃ©kek',
                            'it' => 'venditore Prodotti',
                            'nl' => 'verkoper producten',
                            'pl' => 'Sprzedawca Produkty',
                            'pt' => 'Vendedor produtos',
                            'ro' => 'Produse Vanzator',
                            'ru' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ† Ñ‚Ð¾Ð²Ð°Ñ€Ñ‹',
                            'sk' => 'PredÃ¡vajÃºci Products',
                            'sv' => 'sÃ¤ljaren produkter',
                            'tr' => 'SatÄ±cÄ± ÃœrÃ¼nleri',
                            'uk' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ†ÑŒ Ñ‚Ð¾Ð²Ð°Ñ€Ð¸',
                        )
            ),
            array(
                'class_name' => 'AdminKbOrderList',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÐ¾Ñ€ÑŠÑ‡ÐºÐ¸ Ð·Ð° Ð¿Ñ€Ð¾Ð´Ð°Ð²Ð°Ñ‡Ð°',
                            'cs' => 'objednÃ¡vky prodejce',
                            'de' => 'VerkÃ¤ufer Bestellungen',
                            'el' => 'Î Î±ï¿½?Î±Î³Î³ÎµÎ»Î¯ÎµÏ‚ Ï€Ï‰Î»Î·Ï„Î®',
                            'es' => 'Ã³rdenes vendedor',
                            'en' => 'Seller Orders',
                            'fi' => 'myyjÃ¤ tilaukset',
                            'fr' => 'Commandes du vendeur',
                            'hu' => 'Az eladÃ³ rendelÃ©sek',
                            'it' => 'ordini del venditore',
                            'nl' => 'verkoper bestellingen',
                            'pl' => 'Sprzedawca ZamÃ³wienia',
                            'pt' => 'Vendedor ordens',
                            'ro' => 'comenzi Vanzator',
                            'ru' => 'Ð—Ð°ÐºÐ°Ð·Ñ‹ Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†Ð°',
                            'sk' => 'objednÃ¡vky predajcu',
                            'sv' => 'SÃ¤ljare Order',
                            'tr' => 'SatÄ±cÄ± SipariÅŸleri',
                            'uk' => 'Ð·Ð°Ð¼Ð¾Ð²Ð»ÐµÐ½Ð½ï¿½? Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†ï¿½?',
                        )
            ),
            array(
                'class_name' => 'AdminKbadminOrderList',
                'active' => 1,
                'name' => array(
                            'bg' => 'Admin ÐŸÐ¾Ñ€ÑŠÑ‡ÐºÐ¸',
                            'cs' => 'admin objednÃ¡vky',
                            'de' => 'Admin Bestellungen',
                            'el' => 'Î Î±ï¿½?Î±Î³Î³ÎµÎ»Î¯ÎµÏ‚ Î´Î¹Î±Ï‡ÎµÎ¹ï¿½?Î¹ÏƒÏ„Î®',
                            'es' => 'Ã³rdenes de administraciÃ³n',
                            'en' => 'Admin Orders',
                            'fi' => 'admin tilaukset',
                            'fr' => 'Commandes d administration',
                            'hu' => 'admin rendelÃ©sek',
                            'it' => 'ordini Admin',
                            'nl' => 'Admin bestellingen',
                            'pl' => 'Administrator ZamÃ³wienia',
                            'pt' => 'ordens de admin',
                            'ro' => 'comenzi Admin',
                            'ru' => 'Ð—Ð°ÐºÐ°Ð·Ñ‹ Ð°Ð´Ð¼Ð¸Ð½Ð¸ï¿½?Ñ‚Ñ€Ð°Ñ‚Ð¾Ñ€Ð°',
                            'sk' => 'admin objednÃ¡vky',
                            'sv' => 'admin bestÃ¤llningar',
                            'tr' => 'YÃ¶netici SipariÅŸleri',
                            'uk' => 'Ð·Ð°Ð¼Ð¾Ð²Ð»ÐµÐ½Ð½ï¿½? Ð°Ð´Ð¼Ñ–Ð½Ñ–ï¿½?Ñ‚Ñ€Ð°Ñ‚Ð¾Ñ€Ð°',
                        )
            ),
            array(
                'class_name' => 'AdminKbSProductReview',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐžÑ‚Ð·Ð¸Ð²Ð¸ Ð·Ð° Ð¿Ñ€Ð¾Ð´ÑƒÐºÑ‚Ð°',
                            'cs' => 'Recenze produktÅ¯',
                            'de' => 'Produktrezensionen',
                            'el' => 'Îšï¿½?Î¹Ï„Î¹ÎºÎ­Ï‚ Ï€ï¿½?Î¿ÏŠÏŒÎ½Ï„Ï‰Î½',
                            'es' => 'EvaluaciÃ³n de productos',
                            'en' => 'Product Reviews',
                            'fi' => 'Tuotearvostelut',
                            'fr' => 'Commentaires du produit',
                            'hu' => 'TermÃ©k vÃ©lemÃ©nyek',
                            'it' => 'Recensioni prodotto',
                            'nl' => 'product-reviews^S',
                            'pl' => 'Recenzje produktu',
                            'pt' => 'RevisÃ£o de produtos',
                            'ro' => 'Comentarii produse',
                            'ru' => 'ÐžÑ‚Ð·Ñ‹Ð²Ñ‹ Ð¾ Ñ‚Ð¾Ð²Ð°Ñ€Ðµ',
                            'sk' => 'recenzie produktov',
                            'sv' => 'Produktrecensioner',
                            'tr' => 'Ð’Ñ–Ð´Ð³ÑƒÐºÐ¸ Ð¿Ñ€Ð¾ Ñ‚Ð¾Ð²Ð°Ñ€',
                            'uk' => 'ÃœrÃ¼n Ä°ncelemeleri',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerReviewApproval',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ ÐžÑ‚Ð·Ð¸Ð²Ð¸ Ð¡Ð¿Ð¸ï¿½?ÑŠÐº ÐžÐ´Ð¾Ð±Ñ€ÐµÐ½Ð¸Ðµ',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Recenze seznam schvalovacÃ­',
                            'de' => 'VerkÃ¤ufer Bewertungen Approval Liste',
                            'el' => 'Îšï¿½?Î¹Ï„Î¹ÎºÎ­Ï‚ Î Ï‰Î»Î·Ï„Î®Ï‚ Î›Î¯ÏƒÏ„Î± ÎˆÎ³Îºï¿½?Î¹ÏƒÎ·',
                            'es' => 'Opiniones vendedor lista de aprobaciÃ³n',
                            'en' => 'Seller Reviews Approval List',
                            'fi' => 'MyyjÃ¤arvostelua hyvÃ¤ksyminen List',
                            'fr' => 'Vendeur Avis Liste d approbation',
                            'hu' => 'Az eladÃ³ Ã©rtÃ©kelÃ©sei jÃ³vÃ¡hagyÃ¡sa listÃ¡ja',
                            'it' => 'Venditore recensioni in vetrina Elenco Soddisfazione',
                            'nl' => 'Beoordelingen Verkoper Goedkeuring List',
                            'pl' => 'Sprzedawca Recenzje zatwierdzenie Lista',
                            'pt' => 'Vendedor RevisÃµes Lista de AprovaÃ§Ã£o',
                            'ro' => 'Opinii Vanzator lista pentru aprobare',
                            'ru' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ† ÐžÑ‚Ð·Ñ‹Ð²Ñ‹ Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº ÑƒÑ‚Ð²ÐµÑ€Ð¶Ð´ÐµÐ½Ð¸ï¿½?',
                            'sk' => 'PredÃ¡vajÃºci Recenzie zoznam informaï¿½?nej',
                            'sv' => 'SÃ¤ljaren Recensioner Approval Lista',
                            'tr' => 'SatÄ±cÄ± Onay Listesini Yorumlar',
                            'uk' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ†ÑŒ Ð’Ñ–Ð´Ð³ÑƒÐºÐ¸ Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº Ð·Ð°Ñ‚Ð²ÐµÑ€Ð´Ð¶ÐµÐ½Ð½ï¿½?',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerReviewList',
                'active' => 1,
                'name' => array(
                            'bg' => 'Ð¾Ñ‚Ð·Ð¸Ð²Ð¸ Ð·Ð° Ð¿Ñ€Ð¾Ð´Ð°Ð²Ð°Ñ‡Ð°',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Recenze',
                            'de' => 'VerkÃ¤ufer Bewertungen',
                            'el' => 'Îšï¿½?Î¹Ï„Î¹ÎºÎ­Ï‚ Î Ï‰Î»Î·Ï„Î®Ï‚',
                            'es' => 'Comentarios del vendedor',
                            'en' => 'Seller Reviews',
                            'fi' => 'myyjÃ¤arvostelua',
                            'fr' => 'Commentaires du vendeur',
                            'hu' => 'Az eladÃ³ vÃ©lemÃ©ny',
                            'it' => 'venditore Recensioni',
                            'nl' => 'verkoper Reviews',
                            'pl' => 'Recenzje sprzedajÄ…cego',
                            'pt' => 'Vendedor ComentÃ¡rios',
                            'ro' => 'Opinii Vanzator',
                            'ru' => 'ÐžÑ‚Ð·Ñ‹Ð²Ñ‹ Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†Ð°',
                            'sk' => 'PredÃ¡vajÃºci Recenzie',
                            'sv' => 'sÃ¤ljaren Recensioner',
                            'tr' => 'SatÄ±cÄ± YorumlarÄ±',
                            'uk' => 'Ð’Ñ–Ð´Ð³ÑƒÐºÐ¸ Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†ï¿½?',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerCRequest',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ Ð¡Ð¿Ð¸ï¿½?ÑŠÐº ÐšÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸ï¿½? Ð˜ï¿½?ÐºÐ°Ð½Ðµ',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Seznam kategoriÃ­ Request',
                            'de' => 'VerkÃ¤ufer Kategorie Anforderungsliste',
                            'el' => 'ÎŸ Ï€Ï‰Î»Î·Ï„Î®Ï‚ Î›Î¯ÏƒÏ„Î± Î‘Î¯Ï„Î·ÏƒÎ· ÎšÎ±Ï„Î·Î³Î¿ï¿½?Î¯Î±',
                            'es' => 'Vendedor CategorÃ­a lista de peticiones',
                            'en' => 'Seller Category Request List',
                            'fi' => 'MyyjÃ¤ Luokka Request List',
                            'fr' => 'Vendeur CatÃ©gorie Liste des demandes',
                            'hu' => 'Az eladÃ³ KategÃ³ria Request List',
                            'it' => 'Venditore Categoria richiesta Lista',
                            'nl' => 'Verkoper Categorie List Request',
                            'pl' => 'Sprzedawca Kategoria Zapytanie Lista',
                            'pt' => 'Vendedor Lista Request Categoria',
                            'ro' => 'Vanzator Categorie Cerere Lista',
                            'ru' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº Ð·Ð°Ð¿Ñ€Ð¾ï¿½? ÐšÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ð¸ï¿½? Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†Ð°',
                            'sk' => 'PredÃ¡vajÃºci Zoznam kategÃ³riÃ­ Request',
                            'sv' => 'SÃ¤ljare Kategori Request List',
                            'tr' => 'SatÄ±cÄ± Kategorisi Talep Listesi',
                            'uk' => 'Ð¡Ð¿Ð¸ï¿½?Ð¾Ðº Ð·Ð°Ð¿Ð¸Ñ‚ ÐšÐ°Ñ‚ÐµÐ³Ð¾Ñ€Ñ–ï¿½? Ð¿Ñ€Ð¾Ð´Ð°Ð²Ñ†ï¿½?',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerShipping',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ Shippings',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Shippings',
                            'de' => 'VerkÃ¤ufer Liefer',
                            'el' => 'Î‘Ï€Î¿ÏƒÏ„Î¿Î»Î­Ï‚ Ï€Ï‰Î»Î·Ï„Î®',
                            'es' => 'Los envÃ­os vendedor',
                            'en' => 'Seller Shippings',
                            'fi' => 'myyjÃ¤ Shippingin',
                            'fr' => 'vendeur ExpÃ©ditions',
                            'hu' => 'Az eladÃ³ Shippings',
                            'it' => 'venditore Spedizioni',
                            'nl' => 'verkoper Verzendingen',
                            'pl' => 'Sprzedawca Shippings',
                            'pt' => 'Vendedor embarques',
                            'ro' => 'Shippings VÃ¢nzÄƒtor',
                            'ru' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ† Ð¾Ñ‚Ð³Ñ€ÑƒÐ·Ð¾Ðº',
                            'sk' => 'PredÃ¡vajÃºci Shippings',
                            'sv' => 'sÃ¤ljaren sÃ¤ndnings',
                            'tr' => 'SatÄ±cÄ± TaÅŸÄ±malarÄ±',
                            'uk' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ†ÑŒ Ð²Ñ–Ð´Ð²Ð°Ð½Ñ‚Ð°Ð¶ÐµÐ½ÑŒ',
                        )
            ),
            /*Start - MK made changes on 08-03-2018 for Marketplace changes*/
            array(
                'class_name' => 'AdminKbSellerShippingMethod',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ Ð½Ð° Ð½Ð°Ñ‡Ð¸Ð½Ð° Ð·Ð° Ð´Ð¾ï¿½?Ñ‚Ð°Ð²ÐºÐ°',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ ZpÅ¯sob dopravy',
                            'de' => 'VerkÃ¤ufer Versandart',
                            'el' => 'Î Ï‰Î»Î·Ï„Î®Ï‚ ÎœÎ­Î¸Î¿Î´Î¿Ï‚ Î½Î±Ï…Ï„Î¹Î»Î¯Î±Ï‚',
                            'es' => 'Vendedor MÃ©todo de envÃ­o',
                            'en' => 'Seller Shipping Method',
                            'fi' => 'MyyjÃ¤ toimitustapa',
                            'fr' => 'Vendeur MÃ©thode d expÃ©dition',
                            'hu' => 'Az eladÃ³ szÃ¡llÃ­tÃ¡si mÃ³d',
                            'it' => 'Venditore Metodo di Spedizione',
                            'nl' => 'Verkoper Verzendmethode',
                            'pl' => 'Sprzedawca SposÃ³b wysyÅ‚ki',
                            'pt' => 'Vendedor MÃ©todo de Envio',
                            'ro' => 'VÃ¢nzÄƒtor MetodÄƒ de expediere',
                            'ru' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ† Ð¡Ð¿Ð¾ï¿½?Ð¾Ð± Ð´Ð¾ï¿½?Ñ‚Ð°Ð²ÐºÐ¸',
                            'sk' => 'PredÃ¡vajÃºci SpÃ´sob dopravy',
                            'sv' => 'SÃ¤ljaren SÃ¤ndningsmetod',
                            'tr' => 'SatÄ±cÄ± Nakliye YÃ¶ntemi',
                            'uk' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ†ÑŒ Ð¡Ð¿Ð¾ï¿½?Ñ–Ð± Ð´Ð¾ï¿½?Ñ‚Ð°Ð²ÐºÐ¸',
                        )
            ),
            /*End -MK made changes on 08-03-2018 for Marketplace changes*/
            array(
                'class_name' => 'AdminKbCommission',
                'active' => 1,
                'name' => array(
                            'bg' => 'Admin ÐºÐ¾Ð¼Ð¸ï¿½?Ð¸Ð¸',
                            'cs' => 'admin Provize',
                            'de' => 'Admin-Kommissionen',
                            'el' => 'Î ï¿½?Î¿Î¼Î®Î¸ÎµÎ¹ÎµÏ‚ Î´Î¹Î±Ï‡ÎµÎ¹ï¿½?Î¹ÏƒÏ„Î®',
                            'es' => 'Las comisiones de administraciÃ³n',
                            'en' => 'Admin Commissions',
                            'fi' => 'admin Palkkiot',
                            'fr' => 'Commissions d administration',
                            'hu' => 'admin jutalÃ©kok',
                            'it' => 'Commissioni Admin',
                            'nl' => 'Admin Commissies',
                            'pl' => 'Prowizje Admin',
                            'pt' => 'ComissÃµes de admin',
                            'ro' => 'Comisioane Admin',
                            'ru' => 'ÐšÐ¾Ð¼Ð¸ï¿½?ï¿½?Ð¸Ð¾Ð½Ð½Ñ‹Ðµ Ð°Ð´Ð¼Ð¸Ð½Ð¸ï¿½?Ñ‚Ñ€Ð°Ñ‚Ð¾Ñ€Ð°',
                            'sk' => 'admin ProvÃ­zia',
                            'sv' => 'admin Uppdrag',
                            'tr' => 'YÃ¶netici Komisyonlar',
                            'uk' => 'ÐºÐ¾Ð¼Ñ–ï¿½?Ñ–Ð¹Ð½Ñ– Ð°Ð´Ð¼Ñ–Ð½Ñ–ï¿½?Ñ‚Ñ€Ð°Ñ‚Ð¾Ñ€Ð°',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerTransPayoutRequest',
                'active' => 1,
                'name' => array(
                            'bg' => 'Ð¡Ð´ÐµÐ»ÐºÐ¸ Ð½Ð° Ð¸Ð·Ð¿Ð»Ð°Ñ‰Ð°Ð½Ðµ Ð˜ï¿½?ÐºÐ°Ð½Ðµ',
                            'cs' => 'Transakce VÃ½platnÃ­ Request',
                            'de' => 'Transaktionen Auszahlung anfordern',
                            'el' => 'Î‘Î¯Ï„Î·Î¼Î± Î£Ï…Î½Î±Î»Î»Î±Î³Î­Ï‚ Î Î»Î·ï¿½?Ï‰Î¼ÏŽÎ½',
                            'es' => 'Solicitud de las transacciones de pago',
                            'en' => 'Transactions Payout Request',
                            'fi' => 'Liiketoimet Palautusprosentti PyyntÃ¶',
                            'fr' => 'Transactions Demande de paiement',
                            'hu' => 'TranzakciÃ³k KifizetÃ©s kÃ©rÃ©se',
                            'it' => 'Le transazioni Payout Richiesta',
                            'nl' => 'Transacties Uitbetaling Request',
                            'pl' => 'Transakcje pÅ‚acowe Zapytanie',
                            'pt' => 'Pedido de transaÃ§Ãµes de pagamento',
                            'ro' => 'TranzacÈ›ii de platÄƒ Solicitare',
                            'ru' => 'Ð¡Ð´ÐµÐ»ÐºÐ¸ Payout Ð—Ð°Ð¿Ñ€Ð¾ï¿½?',
                            'sk' => 'Transakcie VÃ½platnÃ© Request',
                            'sv' => 'Transaktioner Utbetalning Request',
                            'tr' => 'Ä°ÅŸlemler Ã–deme Talebi',
                            'uk' => 'Ð£Ð³Ð¾Ð´Ð¸ Payout Ð—Ð°Ð¿Ð¸Ñ‚',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerTrans',
                'active' => 1,
                'name' => array(
                            'bg' => 'Ð¡Ð´ÐµÐ»ÐºÐ¸ Ð·Ð° Ð¿Ñ€Ð¾Ð´Ð°Ð²Ð°Ñ‡Ð°',
                            'cs' => 'Transakce prodejce',
                            'de' => 'VerkÃ¤ufer Transaktionen',
                            'el' => 'ÎŸÎ¹ ÏƒÏ…Î½Î±Î»Î»Î±Î³Î­Ï‚ Î Ï‰Î»Î·Ï„Î®Ï‚',
                            'es' => 'Transacciones vendedor',
                            'en' => 'Seller Transactions',
                            'fi' => 'myyjÃ¤ Transactions',
                            'fr' => 'Transactions du vendeur',
                            'hu' => 'Az eladÃ³ tranzakciÃ³k',
                            'it' => 'Transazioni',
                            'nl' => 'verkoper Transacties',
                            'pl' => 'Sprzedawca Transakcje',
                            'pt' => 'TransaÃ§Ãµes',
                            'ro' => 'TranzacÈ›ii Vanzator',
                            'ru' => 'Ð¡Ð´ÐµÐ»ÐºÐ¸',
                            'sk' => 'transakcie predajcu',
                            'sv' => 'sÃ¤ljaren Transaktioner',
                            'tr' => 'SatÄ±cÄ± Ä°ÅŸlemleri',
                            'uk' => 'ÑƒÐ³Ð¾Ð´Ð¸',
                        )
            ),
            array(
                'class_name' => 'AdminKbSellerCloseShopRequest',
                'active' => 1,
                'name' => array(
                            'bg' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²Ð°Ñ‡ Ð—Ð°Ñ‚Ð²Ð°Ñ€ï¿½?Ð½Ðµ Ð½Ð° Ð·Ð°ï¿½?Ð²ÐºÐ°Ñ‚Ð°',
                            'cs' => 'ProdÃ¡vajÃ­cÃ­ Shop Close Request',
                            'de' => 'VerkÃ¤ufer Shop SchlieÃŸen anfordern',
                            'el' => 'ÎŸ Ï€Ï‰Î»Î·Ï„Î®Ï‚ ÎºÎ±Ï„Î¬ÏƒÏ„Î·Î¼Î± ÎšÎ¿Î½Ï„Î¬ Î‘Î¯Ï„Î·ÏƒÎ·',
                            'es' => 'Vendedor Tienda Cerrar Solicitud',
                            'en' => 'Seller Shop Close Request',
                            'fi' => 'MyyjÃ¤ Shop Close PyyntÃ¶',
                            'hu' => 'Az eladÃ³ Ã¼zlet bezÃ¡rÃ¡sa kÃ©rÃ©se',
                            'it' => 'Venditore Negozio Chiudi Richiesta',
                            'nl' => 'Verkoper Shop Close Vraag',
                            'pl' => 'Sprzedawca Sklep Close Zapytanie',
                            'pt' => 'Vendedor Fechar Pedido Loja',
                            'ro' => 'VÃ¢nzÄƒtor Shop ÃŽnchide Cerere',
                            'ru' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ† ÐœÐ°Ð³Ð°Ð·Ð¸Ð½ Ð—Ð°ÐºÑ€Ñ‹Ñ‚ÑŒ Ð·Ð°Ð¿Ñ€Ð¾ï¿½?',
                            'sk' => 'PredÃ¡vajÃºci Shop Close Request',
                            'sv' => 'SÃ¤ljaren Shop Close Request',
                            'tr' => 'SatÄ±cÄ± MaÄŸaza Ä°steÄŸi Kapat',
                            'uk' => 'ÐŸÑ€Ð¾Ð´Ð°Ð²ÐµÑ†ÑŒ ÐœÐ°Ð³Ð°Ð·Ð¸Ð½ Ð—Ð°ÐºÑ€Ð¸Ñ‚Ð¸ Ð·Ð°Ð¿Ð¸Ñ‚',
                        )
            ),
            array(
                'class_name' => 'AdminKbGDPRRequest',
                'active' => 1,
                'name' => array(
                            'bg' => 'GDPR Ð—Ð°ï¿½?Ð²ÐºÐ¸',
                            'cs' => 'GDPR Å½Ã¡dosti',
                            'de' => 'BIPR Anfragen',
                            'el' => 'Î±Î¹Ï„Î®ÏƒÎµÎ¹Ï‚ Î‘Î•Î³Ï‡Î Î ',
                            'es' => 'Las solicitudes GDPR',
                            'en' => 'GDPR Requests',
                            'fi' => 'GDPR pyynnÃ¶t',
                            'fr' => 'Les demandes de GDPR',
                            'hu' => 'GDPR kÃ©rÃ©sek',
                            'it' => 'Richieste GDPR',
                            'nl' => 'GDPR Verzoeken',
                            'pl' => 'Wnioski PKBR',
                            'pt' => 'Os pedidos PIBR',
                            'ro' => 'Cereri GDPR',
                            'ru' => 'GDPR Ð¿Ñ€Ð¾ï¿½?Ð¸Ñ‚',
                            'sk' => 'GDPR Å½iadosti',
                            'sv' => 'BRP BegÃ¤ran',
                            'tr' => 'GDPR Ä°stekler ',
                            'uk' => 'GDPR Ð¿Ñ€Ð¾ï¿½?Ð¸Ñ‚ÑŒ',
                        )
            ),
            array(
                'class_name' => 'AdminKbEmail',
                'active' => 1,
                'name' => array(
                            'bg' => 'Ð¨Ð°Ð±Ð»Ð¾Ð½Ð¸ Ð·Ð° Ð¸Ð¼ÐµÐ¹Ð»',
                            'cs' => 'e-mailovÃ© Å¡ablony',
                            'de' => 'E-Mail-Vorlagen',
                            'el' => 'Î ï¿½?ÏŒÏ„Ï…Ï€Î± email',
                            'es' => 'Plantillas de correo electrÃ³nico',
                            'en' => 'Email Templates',
                            'fi' => 'SÃ¤hkÃ¶posti Mallit',
                            'fr' => 'ModÃ¨les de courrier Ã©lectronique',
                            'hu' => 'e-mail sablonok',
                            'it' => 'Email Templates',
                            'nl' => 'E-mail Templates',
                            'pl' => 'Szablony wiadomoÅ›ci',
                            'pt' => 'Modelos de e-mail',
                            'ro' => 'Template-uri de e-mail',
                            'ru' => 'Ð¨Ð°Ð±Ð»Ð¾Ð½Ñ‹ ï¿½?Ð»ÐµÐºÑ‚Ñ€Ð¾Ð½Ð½Ð¾Ð¹ Ð¿Ð¾Ñ‡Ñ‚Ñ‹',
                            'sk' => 'e-mailovej Å¡ablÃ³ny',
                            'sv' => 'E-postmallar',
                            'tr' => 'E-posta ÅžablonlarÄ±',
                            'uk' => 'Ð¨Ð°Ð±Ð»Ð¾Ð½Ð¸ ÐµÐ»ÐµÐºÑ‚Ñ€Ð¾Ð½Ð½Ð¾Ñ— Ð¿Ð¾ÑˆÑ‚Ð¸',
                        )
            )
        );
    }

    protected function unInstallMarketPlaceTabs()
    {
        $parentTab = new Tab(Tab::getIdFromClassName(self::PARENT_TAB_CLASS));
        $parentTab->delete();

        $admin_menus = $this->getAdminMenus();

        foreach ($admin_menus as $menu) {
            $sql = 'SELECT id_tab FROM `' . _DB_PREFIX_ . 'tab` Where class_name = "' . pSQL($menu['class_name']) . '" 
				AND module = "' . pSQL($this->name) . '"';
            $id_tab = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $tab = new Tab($id_tab);
            $tab->delete();
        }

        return true;
    }

    public function hookModuleRoutes($params)
    {
        unset($params);
        if (Configuration::get('KB_MARKETPLACE') !== false && Configuration::get('KB_MARKETPLACE') == 1) {
            return array(
                'kb_seller_rule' => array(
                    'controller' => 'sellerfront',
                    'rule' => 'seller/{id}-{rewrite}',
                    'keywords' => array(
                        'id' => array('regexp' => '[0-9]+', 'param' => 'id_seller'),
                        'rewrite' => array('regexp' => '[_a-zA-Z0-9\pL\pS-]*'),
                        'meta_keywords' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                        'meta_title' => array('regexp' => '[_a-zA-Z0-9-\pL]*'),
                    ),
                    'params' => array(
                        'render_type' => 'sellerview',
                        'fc' => 'module',
                        'module' => 'kbmarketplace'
                    ),
                )
            );
        }

        return array();
    }

    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS(_PS_BASE_URL_ . __PS_BASE_URI__ . 'js/jquery/plugins/jquery.fancybox.css');
        $this->context->controller->addJS(_PS_BASE_URL_ . __PS_BASE_URI__ . 'js/jquery/plugins/jquery.fancybox.js');

        $this->context->controller->addCSS($this->_path . self::CSS_FRONT_PATH . 'kb-hooks.css');
        $this->context->controller->addJS($this->_path . 'views/js/front/hook.js');

        $page_name = $this->context->smarty->tpl_vars['page']->value['page_name'];
        if (strripos($page_name, 'cart') !== false || strripos($page_name, 'checkout') !== false) {
            $config = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            $this->context->smarty->assign(
                'cart_url',
                $this->context->link->getModuleLink('kbmarketplace', 'sellerfront')
            );
            $this->context->smarty->assign('allow_free_shipping', 1);
        }
        if (stripos($page_name, 'checkout') !== false) {
        }
        if (stripos($page_name, 'kbmarketplace') !== false) {
            $page_params = explode('-', $page_name);
            $id_seller = Tools::getValue('id_seller', 0);
            if ((isset($page_params[2]) && $page_params[2] == 'sellerfront')) {
                if ($id_seller > 0) {
                    $seller = new KbSeller($id_seller, $this->context->language->id);
                    if (Validate::isLoadedObject($seller) && $seller->isApprovedSeller() && $seller->active == 1) {
                        $this->context->smarty->assign(
                            'meta_keywords',
                            Tools::safeOutput($seller->meta_keyword, false)
                        );
                        $this->context->smarty->assign(
                            'meta_description',
                            Tools::safeOutput($seller->meta_description, false)
                        );
                    }
                } else {
                    $global_settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
                    if (isset($global_settings['kbmp_seller_listing_meta_keywords']) && !empty($global_settings['kbmp_seller_listing_meta_keywords'])) {
                        $this->context->smarty->assign(
                            'meta_keywords',
                            Tools::safeOutput($global_settings['kbmp_seller_listing_meta_keywords'], false)
                        );
                    }

                    if (isset($global_settings['kbmp_seller_listing_meta_description']) && !empty($global_settings['kbmp_seller_listing_meta_description'])) {
                        $this->context->smarty->assign(
                            'meta_description',
                            Tools::safeOutput($global_settings['kbmp_seller_listing_meta_description'], false)
                        );
                    }
                }
            }
        }
    }

    protected function getConfigurationFieldValues()
    {
        if (Configuration::get('KB_MARKETPLACE') === false) {
            $settings = $this->getDefaultSettings();
        } else {
            $settings = Configuration::get('KB_MARKETPLACE');
        }
        $custom_css = '';
        $custom_js = '';
        return array(
            'KB_MARKETPLACE' => $settings,
            'KB_MARKETPLACE_CSS' => $custom_css,
            'KB_MARKETPLACE_JS' => $custom_js,
        );
    }

     protected function renderSellerSettingForm()
    {
        $helper      = new HelperForm();
        $id_customer = (int) Tools::getValue('id_customer');
        $msg         = '';
        $msg_txt1    = '';
        $seller      = new KbSeller(KbSeller::getSellerByCustomerId($id_customer));
        $s_settings  = new KbSellerSetting($seller->id);
        $s_settings->setShop($seller->id_shop);
        if ((Tools::isSubmit('submitSellerSetting') || Tools::isSubmit('submitSellerRegistration'))
            && (int) Tools::getValue('id_customer') > 0) {
            if (Tools::isSubmit('register_as_seller') && Tools::getValue('register_as_seller')
                == 1) {
                $seller->product_limit_wout_approval = 0;
                $seller->approval_request_limit = 0;
                $seller->notification_type           = (string) KbSeller::NOTIFICATION_PRIMARY;
                $seller->registerNewCustomer(
                    $id_customer,
                    1,
                    Tools::getValue('activate_seller')
                );

                $new_customer = new Customer($id_customer);
                $data         = array(
                    'email' => $new_customer->email,
                    'name' => $new_customer->firstname.' '.$new_customer->lastname
                );
                $email        = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_welcome_seller'),
                    $new_customer->id_lang
                );
                $email->sendWelcomeEmailToCustomer($data);

                $email = new KbEmail(
                    KbEmail::getTemplateIdByName('mp_seller_registration_notification_admin'),
                    Configuration::get('PS_LANG_DEFAULT')
                );
                $email->sendNotificationOnNewRegistration($data);

                KbSellerSetting::saveSettingForNewSeller($seller);
                KbSellerSetting::assignCategoryGlobalToSeller($seller);

                $seller_shipping = new KbSellerShipping();
                $seller_shipping->createAndAssignFreeShipping($seller);

                Hook::exec(
                    'actionKbMarketPlaceCustomerRegistration',
                    array('seller' => $seller)
                );

                $this->confirmations[] = $this->l('Customer successfully registered as seller.', 'kbconfiguration');
            } elseif (Tools::isSubmit('kb_mp_seller_config')) {
                $seller_config = Tools::getValue('kb_mp_seller_config');
                $error         = 0;
                $msg = $this->displayConfirmation($this->l('Seller settings successfully saved.', 'kbconfiguration'));
            }
        }

        $fields_options = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Seller Account Configuration', 'kbconfiguration'),
                    'icon' => 'icon-wrench'
                ),
                'submit' => array(
                    'title' => $this->l('Save', 'kbconfiguration'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitSellerSetting',
                )
            )
        );
        $field_values   = array();

        if (!$seller->isSeller()) {
            $fields_options['form']['input'] = array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Register as seller', 'kbconfiguration'),
                    'name' => 'register_as_seller',
                    'hint' => $this->l('To register this customer as seller.', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Approve', 'kbconfiguration'),
                    'name' => 'approve',
                    'disabled' => true,
                    'hint' => $this->l('Approve customer as seller after registering or later.', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Active', 'kbconfiguration'),
                    'name' => 'activate_seller',
                    'hint' => $this->l('Activate seller', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                )
            );

            // by default set to 1
            $field_values = array(
                'register_as_seller' => 0,
                //'approve' => KbGlobal::APPROVAL_WAITING,
                'approve' => 1,
                'activate_seller' => 0
            );
            $helper->submit_action = 'submitSellerRegistration';
        } else {
            $settings = $s_settings->getSettings();
            if (empty($settings) || count($settings) == 0) {
                $settings = KbSellerSetting::getSellerDefaultSetting();
            }

            $fields = array(
                array(
                    'type' => 'text',
                    'required' => true,
                    'label' => $this->l('Default Commission', 'kbconfiguration'),
                    'name' => 'kbmp_default_commission',
                    'hint' => $this->l('This commission will be deducted per product ordered for this seller.', 'kbconfiguration'),
                    'disabled' => false,
                    'values' => 20,
                    'class' => 'fixed-width-xs kbmp_default_commission_seller',
                    'suffix' => '%',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('New Product Approval Required', 'kbconfiguration'),
                    'name' => 'kbmp_new_product_approval_required',
                    'disabled' => true,
                    'hint' => $this->l('New product needs approval from your side before display on front.', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enable Seller Review', 'kbconfiguration'),
                    'name' => 'kbmp_enable_seller_review',
                    'disabled' => true,
                    'hint' => $this->l('Enable customers to give their reviews on seller.', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Seller Review Approval Required', 'kbconfiguration'),
                    'name' => 'kbmp_seller_review_approval_required',
                    'disabled' => true,
                    'hint' => $this->l('With this setting, review first needs approval by you before showing to customers.', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Send Email on Order Place', 'kbconfiguration'),
                    'disabled' => true,
                    'name' => 'kbmp_email_on_new_order',
                    'hint' => $this->l('With this setting, system will send email to seller for new order', 'kbconfiguration'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled', 'kbconfiguration')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled', 'kbconfiguration')
                        )
                    ),
                )
            );

            foreach ($fields as $input) {
                $tmp        = $input;
                $use_global = ($settings[$input['name']]['global'] == 1) ? 'checked="checked"'
                        : '';
                if ($input['name'] == 'kbmp_product_limit') {
                    $html = '';
                } else {
                    $html = '<input type="checkbox" onclick="changeSwitchColor(this)" '
                        . 'class="checkbox kb_checkbox_seller_settings" '
                        . 'name="kb_mp_seller_config['.$input['name'].'][global]" '
                        . 'value="1" '.$use_global.'/>'
                        . '<span class="option-label">Use Global</span>';
                }

                $tmp['desc'] = $html;
                if ($input['type'] == 'select' && isset($input['multiple']) && $input['multiple']) {
                    $tmp['name'] = 'kb_mp_seller_config['.$input['name'].'][main][]';
                } else {
                    $tmp['name'] = 'kb_mp_seller_config['.$input['name'].'][main]';
                }

                $field_values[$tmp['name']] = $settings[$input['name']]['main'];

                $fields_options['form']['input'][] = $tmp;
            }

            $helper->submit_action = 'submitSellerSetting';

            $fields_options['form']['bottom'] = '';

            $assigned_cates = KbSellerCategory::getCategoriesBySeller($seller->id);
            $root           = Category::getRootCategory();
            $tree           = new HelperTreeCategories('seller-categories-tree');
            $tree->setRootCategory($root->id)
                ->setUseCheckBox(true)
                ->setUseSearch(false)
                ->setSelectedCategories($assigned_cates);

            $fields_options['form']['input'][] = array(
                'type' => 'categories_select',
                'label' => $this->l('Categories Allowed', 'kbconfiguration'),
                'name' => 'kbmp_allowed_categories',
                'category_tree' => $tree->render(),
                'hint' => array(
                    $this->l('Categories to be allowed to seller in which he/she can map his/her products.', 'kbconfiguration')
                ),
                'desc' => "If no category is selected that will mean that all the categories are allowed."
                . " In order to enable a category you will have to check all the parent categories "
                . "otherwise the category will not be activated. "
                . "Example- To enable `T-shirts` category, you will have to check all the parent categories "
                . "i.e. Home, Women, Tops and ofcourse T-shirts."
            );
        }

        $helper->show_toolbar = false;

        Hook::exec(
            'displayKbMarketPlaceSellerSettingForm',
            array('fields_options' => $fields_options,
            'fields_value' => $field_values, 'seller' => $seller)
        );

        $helper->tpl_vars = array(
            'fields_value' => $field_values
        );

        $lang                          = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module                = $this;
        $helper->id                    = $id_customer;
        $helper->identifier            = 'id_customer';

        $this->context->smarty->assign(
            array(
                'msg' => $msg,
                'action' => $this->context->link->getAdminLink('AdminCustomers')
                .'&updatecustomer&id_customer='.$id_customer,
                'form_template' => $helper->generateForm(array($fields_options))
            )
        );

        return $this->display(
            _PS_MODULE_DIR_.'/kbmarketplace.php',
            'views/templates/admin/configuration.tpl'
        );
    }
    protected function showTopMenuLink()
    {
        $show = false;
        if ($this->context->customer->logged) {
            $show = (bool) KbSeller::getSellerByCustomerId((int) $this->context->customer->id);
        }
        return $show;
    }

    public function hookActionObjectProductUpdateBefore(&$param)
    {
        $product = $param['object'];

        if ($id_seller = KbSellerProduct::getSellerIdByProductId($product->id)) {
            $seller = new KbSeller($id_seller);

            if (!$seller->isApprovedSeller() || $seller->active == 0) {
                $product->active = 0;
            }
        }
    }

    

    private function processOnNewOrder($order_reference, $render_detail)
    {
       
        $orders_by_reference = Order::getByReference($order_reference);

        $orders = $orders_by_reference->getResults();
        $product_details = array();

        if ($orders && is_array($orders) && count($orders) > 0) {
            foreach ($orders as $order) {
                $seller_products = array();
                $admin_products = array();
                $order_product_detail = $order->getProducts();
                $invoice = new Address($order->id_address_invoice);
                $delivery = new Address($order->id_address_delivery);
                if ($order_product_detail && is_array($order_product_detail) && count($order_product_detail) > 0) {
                    foreach ($order_product_detail as $detail) {
                        $id_seller = (int) KbSellerProduct::getSellerIdByProductId((int) $detail['product_id']);
                        if ($id_seller > 0) {
                            $seller_products[$id_seller][] = $detail;
                        } else {
                            $admin_products[] = $detail;
                        }
                    }
                }

                foreach ($seller_products as $id_seller => $products) {
                    $products_in_this_order = array();
                    $comission_percent = (float) KbSellerSetting::getSellerSettingByKey(
                        $id_seller,
                        'kbmp_default_commission'
                    );
                    $total_earning = 0;
                    $qty_ordered = 0;
                    foreach ($products as $product) {
                        $comson_from_percent = (float) ($comission_percent / 100);
                        $admin_order_item_earning = (float) ($comson_from_percent * $product['total_price_tax_incl']);
                        $sl_od_obj = new KbSellerOrderDetail();
                        $sl_od_obj->id_seller = $id_seller;
                        $sl_od_obj->id_order = $order->id;
                        $sl_od_obj->id_shop = $order->id_shop;
                        $sl_od_obj->id_category = $product['id_category_default'];
                        $sl_od_obj->id_product = $product['product_id'];
                        $sl_od_obj->id_order_detail = $product['id_order_detail'];
                        $sl_od_obj->commission_percent = $comission_percent;
                        $sl_od_obj->qty = ($product['product_quantity'] - (
                                $product['product_quantity_return'] + $product['product_quantity_refunded']
                                ));
                        $sl_od_obj->total_earning = $product['total_price_tax_incl'];
                        $sl_od_obj->seller_earning = ($product['total_price_tax_incl'] - $admin_order_item_earning);
                        $sl_od_obj->admin_earning = $admin_order_item_earning;
                        $sl_od_obj->unit_price = $product['unit_price_tax_incl'];
                        $sl_od_obj->is_consider = '1';
                        $sl_od_obj->is_canceled = '0';
                        $sl_od_obj->save();

                        Hook::exec('actionKbMarketPlaceSOrderDetailSave', array('object' => $sl_od_obj));

                        $products_in_this_order[] = $product['product_id'];
                        $total_earning += $product['total_price_tax_incl'];
                        $qty_ordered = ($qty_ordered + ($product['product_quantity'] - (
                                $product['product_quantity_return'] + $product['product_quantity_refunded'])
                                ));
                    }

                    
                    $admin_earning = (float) ((float) ($comission_percent / 100) * $total_earning);
                    
                    $seller_earning = KbSellerEarning::getEarningBySellerAndOrder($id_seller, (int) $order->id);
                    if (is_array($seller_earning) && count($seller_earning) > 0) {
                        $earning_obj = new KbSellerEarning($seller_earning['id_seller_earning']);
                    } else {
                        $earning_obj = new KbSellerEarning();
                    }
                    $earning_obj->id_seller = $id_seller;
                    $earning_obj->id_shop = $order->id_shop;
                    $earning_obj->id_order = $order->id;
                    $earning_obj->product_count = (int) $qty_ordered;
                    $earning_obj->total_earning = (float) $total_earning;
                    $earning_obj->seller_earning = (float) ($total_earning - $admin_earning);
                    $earning_obj->admin_earning = (float) $admin_earning;
                    $earning_obj->is_canceled = '0';
                    $earning_obj->can_handle_order = 0;
                    $earning_obj->save();
                    Hook::exec('actionKbMarketPlaceSEarningSave', array('object' => $earning_obj));
                }
            }
        }
        $this->context->cookie->kbsellerhandleorder = 0;
        unset($this->context->cookie->kbsellerhandleorder);
    }

   
    public function hookActionValidateOrder($params)
    {
        $tmp = $params['order'];
        unset($this->context->cookie->kb_selected_carrier);
        if (!Configuration::get('KB_MARKETPLACE_CONFIG') || Configuration::get('KB_MARKETPLACE_CONFIG') == '') {
            $settings = KbGLobal::getDefaultSettings();
        } else {
            $settings = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        }
        $render_detail = false;
        $this->processOnNewOrder($tmp->reference, $render_detail);
    }

    

    public function hookActionProductCancel($param)
    {
        $order = $param['order'];
        $order_detail = new OrderDetail($param['id_order_detail']);

        $seller_order_detail = KbSellerOrderDetail::getDetailByOrderItemId($order_detail->id);

        if (count($seller_order_detail) > 0) {
            $id_seller = $seller_order_detail['id_seller'];
            $comission_percent = (float) $seller_order_detail['commission_percent'];
            $qty_ordered = (int) ($order_detail->product_quantity - (
                    $order_detail->product_quantity_return + $order_detail->product_quantity_refunded)
                    );
            $total_earning = $order_detail->total_price_tax_incl;
            $admin_earning = (float) ((float) ($comission_percent / 100) * $total_earning);

            $cancel_statuses = array(
                Configuration::get('PS_OS_ERROR'),
                Configuration::get('PS_OS_CANCELED')
            );

            $sl_od_obj = new KbSellerOrderDetail($seller_order_detail['id_seller_order_detail']);
            $sl_od_obj->id_seller = $id_seller;
            $sl_od_obj->id_order = $order_detail->id_order;
            $sl_od_obj->id_shop = $order->id_shop;
            $sl_od_obj->id_category = $order_detail->id_category_default;
            $sl_od_obj->id_product = $order_detail->product_id;
            $sl_od_obj->id_order_detail = $order_detail->id;
            $sl_od_obj->commission_percent = $comission_percent;
            $sl_od_obj->total_earning = $total_earning;
            $sl_od_obj->seller_earning = ($total_earning - $admin_earning);
            $sl_od_obj->admin_earning = $admin_earning;
            $sl_od_obj->unit_price = $order_detail->unit_price_tax_incl;
            $sl_od_obj->qty = $qty_ordered;
            $sl_od_obj->is_consider = '1';

            if (in_array($order->getCurrentState(), $cancel_statuses)) {
                $sl_od_obj->is_canceled = '1';
            } else {
                $sl_od_obj->is_canceled = '0';
            }

            $sl_od_obj->save();

            Hook::exec('actionKbMarketPlaceSOrderDetailUpdate', array('object' => $sl_od_obj));

            $seller_earning = KbSellerEarning::getEarningBySellerAndOrder($id_seller, $order_detail->id_order);
            if (count($seller_earning) > 0) {
                $earning_obj = new KbSellerEarning($seller_earning['id_seller_earning']);
                $earning_obj->product_count -= $qty_ordered;
                $earning_obj->total_earning = (float) ($earning_obj->total_earning - $total_earning);
                $earning_obj->seller_earning = (float) ($earning_obj->seller_earning - ($total_earning - $admin_earning));
                $earning_obj->admin_earning = (float) ($earning_obj->admin_earning - $admin_earning);
            } else {
                $earning_obj = new KbSellerEarning();
                $earning_obj->id_seller = $id_seller;
                $earning_obj->id_shop = $order_detail->id_shop;
                $earning_obj->id_order = $order_detail->id_order;
                $earning_obj->product_count = $qty_ordered;
                $earning_obj->total_earning = (float) $total_earning;
                $earning_obj->seller_earning = (float) ($total_earning - $admin_earning);
                $earning_obj->admin_earning = (float) $admin_earning;
            }
            if (in_array($order->getCurrentState(), $cancel_statuses)) {
                $earning_obj->is_canceled = '1';
            } else {
                $earning_obj->is_canceled = '0';
            }

            $earning_obj->save();

            Hook::exec('actionKbMarketPlaceSEarningUpdate', array('object' => $earning_obj));
        }
    }

    public function hookActionObjectOrderDetailUpdateAfter($param)
    {
        $order_detail = $param['object'];

        if ($id_seller = KbSellerProduct::getSellerIdByProductId($order_detail->product_id)) {
            $temp = KbSellerOrderDetail::getDetailByOrderItemId($order_detail->id);
            if (count($temp) > 0) {
                $seller_earning = KbSellerEarning::getEarningBySellerAndOrder($id_seller, $order_detail->id_order);
                if (count($seller_earning) > 0) {
                    $comission_percent = (float) KbSellerSetting::getSellerSettingByKey(
                        $id_seller,
                        'kbmp_default_commission'
                    );
                    $qty_ordered = (int) ($order_detail->product_quantity - (
                            $order_detail->product_quantity_return + $order_detail->product_quantity_refunded)
                            );
                    $total_earning = $order_detail->total_price_tax_incl;
                    $admin_earning = (float) ((float) ($comission_percent / 100) * $total_earning);

                    $order = new Order($order_detail->id_order);

                    $cancel_statuses = array(
                        Configuration::get('PS_OS_ERROR'),
                        Configuration::get('PS_OS_CANCELED')
                    );

                    $sl_od_obj = new KbSellerOrderDetail($temp['id_seller_order_detail']);
                    $sl_od_obj->id_seller = $id_seller;
                    $sl_od_obj->id_order = $order_detail->id_order;
                    $sl_od_obj->id_shop = $order->id_shop;
                    $sl_od_obj->id_category = $order_detail->id_category_default;
                    $sl_od_obj->id_product = $order_detail->product_id;
                    $sl_od_obj->id_order_detail = $order_detail->id;
                    $sl_od_obj->commission_percent = $comission_percent;
                    $sl_od_obj->total_earning = $total_earning;
                    $sl_od_obj->seller_earning = ($total_earning - $admin_earning);
                    $sl_od_obj->admin_earning = $admin_earning;
                    $sl_od_obj->unit_price = $order_detail->unit_price_tax_incl;
                    $sl_od_obj->qty = $qty_ordered;
                    $sl_od_obj->is_consider = '1';

                    if (in_array($order->getCurrentState(), $cancel_statuses)) {
                        $sl_od_obj->is_canceled = '1';
                    } else {
                        $sl_od_obj->is_canceled = '0';
                    }

                    $sl_od_obj->save();

                    Hook::exec('actionKbMarketPlaceSOrderDetailUpdate', array('object' => $sl_od_obj));

                    $earning_obj = new KbSellerEarning($seller_earning['id_seller_earning']);
                    $earning_obj->product_count += $qty_ordered;
                    $earning_obj->total_earning = (float) ($earning_obj->total_earning + $total_earning);
                    $earning_obj->seller_earning = (float) ($earning_obj->seller_earning + ($total_earning - $admin_earning));
                    $earning_obj->admin_earning = (float) ($earning_obj->admin_earning + $admin_earning);
                    if (in_array($order->getCurrentState(), $cancel_statuses)) {
                        $earning_obj->is_canceled = '1';
                    } else {
                        $earning_obj->is_canceled = '0';
                    }

                    $earning_obj->save();

                    Hook::exec(
                        'actionKbMarketPlaceSEarningUpdate',
                        array('object' => $earning_obj)
                    );
                }
            }
        }
    }

    protected function getFormatedAddress(Address $the_address, $line_sep, $fields_style = array())
    {
        return AddressFormat::generateAddress($the_address, array('avoid' => array()), $line_sep, ' ', $fields_style);
    }

    protected function getEmailTemplateContent($template_name, $mail_type, $var)
    {
        $email_configuration = Configuration::get('PS_MAIL_TYPE');
        if ($email_configuration != $mail_type && $email_configuration != Mail::TYPE_BOTH) {
            return '';
        }

        $theme_template_path = _PS_MODULE_DIR_ . $this->name . '/views/templates/front/emails/'
                . $this->context->language->iso_code . '_' . $template_name;
        if (Tools::file_exists_cache($theme_template_path)) {
            $this->context->smarty->assign('product_html_vars', $var);
            return $this->context->smarty->fetch($theme_template_path);
        }
        return '';
    }

    public function hookActionOrderStatusUpdate($params = null)
    {
        $id_order = $params['id_order'];

        $order_state = $params['newOrderStatus'];

        $errorOrCanceledStatuses = array(Configuration::get('PS_OS_ERROR'), Configuration::get('PS_OS_CANCELED'));

        $is_canceled = '0';
        if (in_array($order_state->id, $errorOrCanceledStatuses)) {
            $is_canceled = '1';
        }

        $seller_orders = KbSellerEarning::getEarningByOrder($id_order);

        if ($seller_orders && count($seller_orders) > 0) {
            foreach ($seller_orders as $odr) {
                $obj = new KbSellerEarning($odr['id_seller_earning']);
                $obj->is_canceled = $is_canceled;
                $obj->save();

                Hook::exec('actionKbMarketPlaceSEarningUpdate', array('object' => $obj));
            }
        }

        $seller_order_details = KbSellerOrderDetail::getDetailByOrderId($id_order);
        if ($seller_order_details && count($seller_order_details) > 0) {
            foreach ($seller_order_details as $odr) {
                $obj = new KbSellerOrderDetail($odr['id_seller_order_detail']);
                $obj->is_canceled = $is_canceled;
                $obj->save();

                Hook::exec('actionKbMarketPlaceSOrderDetailUpdate', array('object' => $obj));
            }
        }
    }

    public function hookActionObjectOrderReturnUpdateAfter($param)
    {
        $order_return = $param['object'];

        if ($order_return->state == 5) {
            $order_return_details = OrderReturn::getOrdersReturnDetail($order_return->id);
            if (count($order_return_details) > 0) {
                foreach ($order_return_details as $return) {
                    $order_detail = new OrderDetail($return['id_order_detail']);
                    $seller_order_detail = KbSellerOrderDetail::getDetailByOrderItemId($order_detail->id);
                    if (count($seller_order_detail) > 0) {
                        $seller_order_detail_obj = new KbSellerOrderDetail(
                            $seller_order_detail['id_seller_order_detail']
                        );
                        $commission_percent = $seller_order_detail_obj->commission_percent;
                        $returned_qty = (int) $return['product_quantity'];
                        $amount_of_returned_qty = (float) ((int) $return['product_quantity'] * $seller_order_detail_obj->unit_price);

                        $reduce_admin_earning = (float) ((float) ($commission_percent / 100) * $amount_of_returned_qty);
                        $reduce_seller_earning = ($amount_of_returned_qty - $reduce_admin_earning);

                        $seller_order_detail_obj->total_earning = ($seller_order_detail_obj->total_earning - $amount_of_returned_qty);
                        $seller_order_detail_obj->seller_earning = ($seller_order_detail_obj->seller_earning - $reduce_seller_earning);
                        $seller_order_detail_obj->admin_earning = ($seller_order_detail_obj->admin_earning - $reduce_admin_earning);
                        $seller_order_detail_obj->qty = ($seller_order_detail_obj->qty - $returned_qty);

                        $seller_order_detail_obj->save();

                        Hook::exec(
                            'actionKbMarketPlaceSOrderDetailUpdate',
                            array('object' => $seller_order_detail_obj)
                        );

                        $prev_earning = KbSellerEarning::getEarningBySellerAndOrder(
                            $seller_order_detail_obj->id_seller,
                            $seller_order_detail_obj->id_order
                        );

                        if (count($prev_earning) > 0) {
                            $earnin_obj = new KbSellerEarning($prev_earning['id_seller_earning']);
                            $earnin_obj->product_count = $earnin_obj->product_count - $returned_qty;
                            $earnin_obj->total_earning = $earnin_obj->total_earning - $amount_of_returned_qty;
                            $earnin_obj->seller_earning = $earnin_obj->seller_earning - $reduce_seller_earning;
                            $earnin_obj->admin_earning = $earnin_obj->admin_earning - $reduce_admin_earning;

                            $earnin_obj->save();
                            Hook::exec('actionKbMarketPlaceSEarningUpdate', array('object' => $earnin_obj));
                        }
                    }
                }
            }
        }
    }

    public function hookActionCarrierUpdate($params = null)
    {
        $new_carrier = $params['carrier'];

        if ($id_seller_shipping = KbSellerShipping::getIdByReference($new_carrier->id_reference)) {
            $seller_shipping = new KbSellerShipping($id_seller_shipping);
            $seller_shipping->id_carrier = $new_carrier->id;
            if ($seller_shipping->is_default_shipping && !$new_carrier->is_free) {
                $new_carrier->is_free = 1;
                $new_carrier->update();
                $new_carrier->deleteDeliveryPrice('range_weight');
                $new_carrier->deleteDeliveryPrice('range_price');
            }
            $seller_shipping->save();
        }
    }

    public function hookActionDispatcher($params = null)
    {
        $controller = $params['controller_class'];
        if ($controller == 'AdminCarriersController' || $controller == 'AdminCarrierWizardController') {
            if (Tools::getIsset('id_carrier')) {
                $carrier = new Carrier(Tools::getValue('id_carrier'));
                if (KbSellerShipping::getIdByReference($carrier->id_reference)) {
                    $this->context->cookie->kbcarrierredirect = 1;
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminCarriers'));
                }
            }

            if (isset($_REQUEST['submitBulkenableSelectioncarrier']) || Tools::getValue('submitBulkdeletecarrier') == "") {
                $carrier_boxes = Tools::getValue('carrierBox');
                if (!empty($carrier_boxes)) {
                    foreach ($carrier_boxes as $carrier_box) {
                        $carrier = new Carrier($carrier_box);
                        if (KbSellerShipping::getIdByReference($carrier->id_reference)) {
                            $this->context->cookie->kbcarrierredirect = 1;
                            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCarriers'));
                        }
                    }
                }
            } else {
                $id_carrier = (int) Tools::getValue('id_carrier', 0);
                $carrier = new Carrier($id_carrier);
                if (KbSellerShipping::getIdByReference($carrier->id_reference)) {
                    $this->context->cookie->kbcarrierredirect = 1;
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminCarriers'));
                }
            }
        }
    }

    public function hookDisplayMyAccountBlock()
    {
        $show_registration_link = 1;
        if (Configuration::get('KB_MARKETPLACE') !== false &&
                Configuration::get('KB_MARKETPLACE') == 1 &&
                $show_registration_link) {
            $title = $this->l('Become a seller', 'kbconfiguration');
            $html = '<li>';
            if ($this->context->customer->logged) {
                $mp_config = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
                $context = $this->context;
                
                $context->smarty->assign(
                    array('kb_seller_agreement' => '')
                );
                $link_to_register = $this->context->link->getPageLink(
                    'my-account',
                    (bool) Configuration::get('PS_SSL_ENABLED'),
                    null,
                    array('register_as_seller' => 1)
                );
                $this->context->smarty->assign('link_to_register', $link_to_register);
                if (KbSeller::getSellerByCustomerId((int) $this->context->customer->id)) {
                    $menu = KbSellerMenu::getMenusByModuleAndController(
                        'kbmarketplace',
                        'dashboard',
                        $this->context->language->id
                    );
                    $url = $this->context->link->getModuleLink(
                        $menu['module_name'],
                        $menu['controller_name'],
                        array(),
                        (bool) Configuration::get('PS_SSL_ENABLED')
                    );
                    $html .= '<a href="' . $url . '" >' .
                            $this->l('My seller account', 'kbconfiguration') .
                            '</a>';
                } else {
                    $url = $this->context->link->getPageLink(
                        'my-account',
                        (bool) Configuration::get('PS_SSL_ENABLED'),
                        null,
                        array('register_as_seller' => 1)
                    );
                    if (isset($kb_seller_agree) && !empty($kb_seller_agree)) {
                        $html .= $this->context->smarty->fetch(
                            _PS_MODULE_DIR_ . 'kbmarketplace/views/templates/hook/seller_footer_link.tpl'
                        );
                    } else {
                        $html .= '<a href="javascript:void(0)" onclick="if(confirm(\'' .
                                $this->l('Are you sure?', 'kbconfiguration') . '\')){ location.href ='
                                . ' $(this).attr(\'data-href\');}" data-href='
                                . '"' . $url . '">' . $title . '</a>';
                    }
                }
            } else {
                $url = $this->context->link->getPageLink(
                    'my-account',
                    (bool) Configuration::get('PS_SSL_ENABLED'),
                    null,
                    array()
                );
                $html .= '<a href="' . $url . '" >' . $title . '</a>';
            }
            $html .= '</li>';
            return $html;
        }
        return '';
    }

    public function hookDisplayKBLeftColumn()
    {
        $template_path = _PS_MODULE_DIR_ . 'kbmarketplace/views/templates/front/menus.tpl';
        $menus = array();

        $seller_obj = new KbSeller(KbSeller::getSellerByCustomerId((int) $this->context->customer->id));
        if (!$seller_obj->isSeller()) {
            Tools::redirect(
                $this->context->link->getPageLink(
                    'my-account',
                    (bool) Configuration::get('PS_SSL_ENABLED')
                )
            );
        }
        foreach (KbSellerMenu::getAllMenus($this->context->language->id) as $menu) {
            $enabled_menu = array('dashboard','seller','product','order','shipping','earning');
            if (in_array($menu['controller_name'],$enabled_menu)) {
                $active = false;
                if ($menu['controller_name'] == $this->context->controller->controller_name) {
                    $active = true;
                }
                $badge_html = false;



                if ($menu['show_badge'] == 1 && !empty($menu['badge_class'])) {
                    $class_name = ucwords($menu['badge_class']);
                    if (!class_exists($class_name)) {
                        require_once _PS_MODULE_DIR_ . $menu['module_name'] . '/classes/' . $class_name . '.php';
                    }
                    $menu_obj = new $class_name();
                    if (method_exists($menu_obj, 'getMenuBadgeHtml')) {
                        $badge_html = $menu_obj->getMenuBadgeHtml($seller_obj->id);
                    }
                }

                $menus[] = array(
                    'label' => sprintf('%s', $this->l($menu['label'], 'kbconfiguration')),
                    'icon_class' => $menu['icon'],
                    'css_class' => $menu['css_class'],
                    'title' => sprintf('%s', $this->l($menu['title'], 'kbconfiguration')),
                    'active' => $active,
                    'badge' => $badge_html,
                    'href' => $this->context->link->getModuleLink(
                        $menu['module_name'],
                        $menu['controller_name'],
                        array(),
                        (bool) Configuration::get('PS_SSL_ENABLED')
                    )
                );
            }
        }

        $this->context->smarty->assign('menus', $menus);
        return $this->context->smarty->fetch($template_path);
    }

    public function hookActionObjectLanguageAddAfter($params)
    {
        $language = $params['object'];
        if ($language->id > 0) {
            $menus = $this->getSellerMenus();
            foreach ($menus as $key => $val) {
                if ($id_seller_menu = KbSellerMenu::getMenuIdByModuleAndController('kbmarketplace', $key)) {
                    $menu_obj = new KbSellerMenu($id_seller_menu);
                    if (Validate::isLoadedObject($menu_obj)) {
                        $where = 'id_seller_menu = ' . (int) $menu_obj->id
                                . ' AND id_lang = ' . (int) $language->id;
                        $exist = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'kb_mp_seller_menu'
                                . '_lang WHERE ' . $where;
                        $field = array(
                            'id_seller_menu' => (int) $menu_obj->id,
                            'id_lang' => (int) $language->id,
                            'label' => pSQL($val['label']),
                            'title' => pSQL($val['title'])
                        );
                        if (Db::getInstance()->getValue($exist)) {
                            Db::getInstance()->update(
                                'kb_mp_seller_menu_lang',
                                $field,
                                $where
                            );
                        } else {
                            Db::getInstance()->insert(
                                'kb_mp_seller_menu_lang',
                                $field
                            );
                        }
                    }
                }
            }

            $templates = $this->getEmailTemplateData();
            foreach ($templates as $key => $val) {
                if ($id_email_template = KbEmail::getTemplateIdByName($key)) {
                    $email_obj = new KbEmail($id_email_template);
                    if (Validate::isLoadedObject($email_obj)) {
                        $where = 'id_email_template = ' . (int) $email_obj->id
                                . ' AND id_lang = ' . (int) $language->id;
                        $exist = 'SELECT COUNT(*) FROM ' . pSQL(_DB_PREFIX_ . 'kb_mp_email_template')
                                . '_lang WHERE ' . $where;
                        $field = array(
                            'id_email_template' => (int) $email_obj->id,
                            'id_lang' => (int) $language->id,
                            'subject' => pSQL($val['subject']),
                            'body' => pSQL($val['body'])
                        );
                        if (Db::getInstance()->getValue($exist)) {
                            Db::getInstance()->update(
                                'kb_mp_email_template_lang',
                                $field,
                                $where
                            );
                        } else {
                            Db::getInstance()->insert(
                                'kb_mp_email_template_lang',
                                $field
                            );
                        }
                    }
                }
            }

            $sellers = KbSeller::getAllSellers();
            foreach ($sellers as $row) {
                $obj = new KbSeller($row['id_seller']);
                if (Validate::isLoadedObject($obj)) {
                    $where = 'id_seller = ' . (int) $obj->id
                            . ' AND id_lang = ' . (int) $language->id;
                    $exist = 'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'kb_mp_seller'
                            . '_lang WHERE ' . $where;
                    $field = array(
                        'id_seller' => (int) $obj->id,
                        'id_lang' => (int) $language->id,
                        'title' => pSQL(@$obj->title[$row['id_default_lang']]),
                        'description' => pSQL(@$obj->description[$row['id_default_lang']]),
                        'meta_keyword' => pSQL(@$obj->meta_keyword[$row['id_default_lang']]),
                        'meta_description' => pSQL((@$obj->meta_description[$row['id_default_lang']])),
                        'return_policy' => pSQL(@$obj->return_policy[$row['id_default_lang']]),
                        'shipping_policy' => pSQL(@$obj->shipping_policy[$row['id_default_lang']]),
                    );
                    if (Db::getInstance()->getValue($exist)) {
                        Db::getInstance()->update(
                            'kb_mp_seller_lang',
                            $field,
                            $where
                        );
                    } else {
                        Db::getInstance()->insert(
                            'kb_mp_seller_lang',
                            $field
                        );
                    }
                }
            }
        }
    }

    public function hookActionObjectLanguageDeleteAfter($params)
    {
        $language = $params['object'];
        if ($language->id > 0) {
            //Delete Marketplace menus
            $id_parent_tab = (int) Tab::getIdFromClassName(self::PARENT_TAB_CLASS);
            if ($id_parent_tab > 0) {
                $child_tabs = Tab::getTabs($language->id, $id_parent_tab);
                if ($child_tabs && count($child_tabs) > 0) {
                    foreach ($child_tabs as $tab) {
                        $cond = 'id_tab = ' . (int) $tab['id_tab']
                                . ' AND id_lang = ' . (int) $language->id;
                        Db::getInstance()->delete(
                            'tab_lang',
                            $cond
                        );
                    }
                }
                $cond = 'id_tab = ' . (int) $id_parent_tab
                        . ' AND id_lang = ' . (int) $language->id;
                Db::getInstance()->delete(
                    'tab_lang',
                    $cond
                );
            }

            $menus = $this->getSellerMenus();
            foreach ($menus as $key => $val) {
                $tmp = $val;
                unset($tmp);
                if ($id_seller_menu = KbSellerMenu::getMenuIdByModuleAndController('kbmarketplace', $key)) {
                    $menu_obj = new KbSellerMenu($id_seller_menu);
                    if (Validate::isLoadedObject($menu_obj)) {
                        $cond = 'id_seller_menu = ' . (int) $menu_obj->id
                                . ' AND id_lang = ' . (int) $language->id;
                        Db::getInstance()->delete(
                            'kb_mp_seller_menu_lang',
                            $cond
                        );
                    }
                }
            }

            $templates = $this->getEmailTemplateData();
            foreach ($templates as $key => $val) {
                if ($id_email_template = KbEmail::getTemplateIdByName($key)) {
                    $email_obj = new KbEmail($id_email_template);
                    if (Validate::isLoadedObject($email_obj)) {
                        $cond = 'id_email_template = ' . (int) $email_obj->id
                                . ' AND id_lang = ' . (int) $language->id;
                        Db::getInstance()->delete(
                            'kb_mp_email_template_lang',
                            $cond
                        );
                    }
                }
            }

            $sellers = KbSeller::getAllSellers();
            foreach ($sellers as $row) {
                $obj = new KbSeller($row['id_seller']);
                if (Validate::isLoadedObject($obj)) {
                    $cond = 'id_seller = ' . (int) $obj->id
                            . ' AND id_lang = ' . (int) $language->id;
                    Db::getInstance()->delete(
                        'kb_mp_seller_lang',
                        $cond
                    );
                }
            }
        }
    }
    
    public static function kbUserInfo()
    {
        $user_ip = '';
        if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',') > 0) {
                $addr = explode(",", $_SERVER['HTTP_X_FORWARDED_FOR']);
                $user_ip = trim($addr[0]);
            } else {
                $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }
        } else {
            $user_ip = $_SERVER['REMOTE_ADDR'];
        }
        
        
        return array(
            'user_agent' =>  $_SERVER['HTTP_USER_AGENT'],
            'remote_address' => $user_ip,
        );
    }
}

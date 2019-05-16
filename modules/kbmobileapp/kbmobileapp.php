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
 */

include_once dirname(__FILE__) . '/libraries/firebase.php';

class KbMobileApp extends Module
{

    protected $custom_errors = array();
    protected $checkout_session = null;

    const TRANSLATION_RECORD_FILE = 'translation_record.csv';

    public function __construct()
    {
        $this->name = 'kbmobileapp';
        $this->tab = 'front_office_features';
        $this->version = '2.0.9';
        $this->author = 'Knowband';
        $this->need_instance = 0;
        $this->module_key = '566d0c2d6d1cdf310f6be2c84647410e';
        $this->ps_versions_compliancy = array('min' => '1.6.0.4', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();
        

        $this->displayName = $this->l('Knowband Mobile App Creator');
        $this->description = $this->l('Extension that will make your store compatible for mobile app.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        parent::__construct();
    }

    /*
     * Return array of errors coming while installing the module
     * 
     * @return array errors coming while installing the module.
     */
    public function getErrors()
    {
        return $this->custom_errors;
    }

    /*
     * Execute while installing
     * 
     * @return bool
     */
    public function install()
    {
        /*start:changes added by aayushi on 7 march 2019 to resolve issue of ps version*/
        if (version_compare(_PS_VERSION_, '1.7.0.1', '<')) {
            $this->custom_errors[] = $this->l('The plugin you are installing is compatible with prestashop 1.7 version only.');
            return false;
        }
        /*end:changes added by aayushi on 7 march 2019 to resolve issue of ps version*/
        
        if (!function_exists('curl_version') || !in_array('curl', get_loaded_extensions())) {
            $this->custom_errors[] = $this->l('CURL is not enabled. Please enable it to use this module.');
            return false;
        }

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        $this->createYoutubeProductMappingTable();
        $this->setDefaultBannersSliders();
        $this->createDatabaseTables();
        $this->alterDatabaseTables();
        // changes by rishabh jain
        $this->createLayoutDatabase();
        // changes over
        if (!parent::install()
                || !$this->registerHook('displayOverrideTemplate')
                || !$this->registerHook('displayOrderConfirmation')
                || !$this->registerHook('displayAdminProductsExtra')
                || !$this->registerHook('actionProductSave')
                || !$this->registerHook('displayBackOfficeHeader')
                || !$this->registerHook('displayHeader')
                || !$this->registerHook('actionOrderStatusPostUpdate')
                || !$this->registerHook('newOrder')
            ) {
            return false;
        }

        $this->writeLanguageFileRecords();
        Configuration::updateValue('KB_MOBILE_APP', 0);
        
        
        
        $path = _PS_IMG_DIR_ . 'kbmobileapp'  ;
        if (!Tools::file_exists_no_cache($path)) {
            mkdir($path, 0755);
        }
        /* changes made by aayushi on 1 dec 2018 for redirect option */
        Configuration::updateValue('KB_MOBILE_APP_SPIN_WIN', 0);
        Configuration::updateValue('KB_MOBILE_APP_CART_OPTION_REDIRECT', 0);
        Configuration::updateValue('KB_MOBILE_APP_ADD_LOGO_NAVIGATION', '');
        Configuration::updateValue('KBMOBILEAPP_HOME_PAGE_LAYOUT', 1);
        Configuration::updateValue('KBMOBILEAPP_APP_THEME_COLOR', '#10ff98');
        Configuration::updateValue('KBMOBILEAPP_APP_BUTTON_COLOR', '#4544ff');
        Configuration::updateValue('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH', 0);
        Configuration::updateValue('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH', 0);
        /* end */
        if (!Configuration::get('KB_MOBILE_APP_SECURE_KEY')) {
            Configuration::updateValue('KB_MOBILE_APP_SECURE_KEY', $this->abdKeyGenerator());
        }
        /* changes started by rishabh jain on 3rd sep 2018
         * To set the KB_MOBILEAPP_URL_ENCODING permieter to 1 bydefault
         */
        if (!Configuration::get('KB_MOBILEAPP_URL_ENCODING')) {
            Configuration::updateValue('KB_MOBILEAPP_URL_ENCODING', 1);
        }
        /* changes over */
        /* changes started
         * @author :Rishabh Jain
         * @date of Modification : 25/09/2018
         * to add default values for WHATSAPP CHAT Configuration.
         */
        if (!Configuration::get('KB_MOBILE_WHATSAPP_CHAT_SUPPORT')) {
            Configuration::updateValue('KB_MOBILE_WHATSAPP_CHAT_SUPPORT', 0);
        }
        if (!Configuration::get('KB_MOBILE_WHATSAPP_CHAT_NUMBER')) {
            Configuration::updateValue('KB_MOBILE_WHATSAPP_CHAT_NUMBER', '');
        }
        /* changes started
         * @author :Rishabh Jain
         * @date of Modification : 19th sep 2018
         * to add default values for Phone number registartion and requirement.
         */
        if (!Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION')) {
            Configuration::updateValue('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION', 0);
        }
        if (!Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY')) {
            Configuration::updateValue('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY', 0);
        }
        /* changes over */
        
        /* changes started
         * @author :Rishabh Jain
         * @date of Modification : 25/09/2018
         * to add default values for fingerprint login.
         */
        if (!Configuration::get('KB_MOBILEAPP_FINGERPRINT_LOGIN')) {
            Configuration::updateValue('KB_MOBILEAPP_FINGERPRINT_LOGIN', 0);
        }
        /* changes over */
        
        $this->addPaypalOrderStatus();
        
        return true;
    }

    /*
     * Function to generate a security hash for crons
     * 
     * @return string
     */
    protected function abdKeyGenerator($length = 32)
    {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= chr(mt_rand(33, 126));
        }
        return md5($random);
    }

    /*
     * Create an translation_record.csv to save the modified file time of translation files
     */
    private function writeLanguageFileRecords()
    {
        $file_folder_path = _PS_MODULE_DIR_ . $this->name . '/translations/';
        $file_path = $file_folder_path . self::TRANSLATION_RECORD_FILE;
        $files = glob("$file_folder_path*.csv");
        $records = array();
        $count = 0;
        foreach ($files as $file) {
            $file_name = basename($file);
            if ($file_name == self::TRANSLATION_RECORD_FILE) {
                continue;
            }
            $file_orig_name = pathinfo($file_name, PATHINFO_FILENAME);
            $records[$count][] = $file_orig_name;
            $records[$count][] = filemtime($file);
            $count++;
        }

        $this->writeArraytoCSV($records, $file_path);
    }

    /*
     * Put content in a csv file
     * 
     * @param array $array content to write in a file
     * @param string $path path of file
     */
    private function writeArraytoCSV($array, $path)
    {
        $file = fopen($path, "w");

        foreach ($array as $line) {
            fputcsv($file, $line);
        }
        fclose($file);
    }

    /*
     * Create product video mapping table while installing the module
     */
    private function createYoutubeProductMappingTable()
    {
        $query = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kb_product_youtube_mapping` (
			`mapping_id` int(11) NOT NULL AUTO_INCREMENT,
			`id_product` int(11) NOT NULL,
			`youtube_url` varchar(2083) NOT NULL,
			`date_add` datetime NOT NULL,
            `date_update` datetime NOT NULL,
            PRIMARY KEY (`mapping_id`),
			INDEX (`mapping_id`), INDEX (`id_product`)
        ) CHARACTER SET utf8 COLLATE utf8_general_ci';
        Db::getInstance()->execute($query);
    }
    
    /*
     * Create database tables while installing
     */
    private function createDatabaseTables()
    {
        $notificationquery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."kb_push_notifications_history` (
            `kb_notification_id` int(11) NOT NULL AUTO_INCREMENT,
            `title` text NOT NULL,
            `message` text NOT NULL,
            `image_type` enum('url','image') NOT NULL,
            `image_url` text NOT NULL,
            `redirect_activity` enum('home','category','product') NOT NULL,
            `category_id` int(10) DEFAULT NULL,
            `category_name` varchar(250) DEFAULT NULL,
            `product_id` int(10) DEFAULT NULL,
            `product_name` varchar(250) DEFAULT NULL,
            `status` varchar(45) DEFAULT NULL,
            `date_add` datetime NOT NULL,
            PRIMARY KEY (`kb_notification_id`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
        
        $bannerquery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."kb_sliders_banners` (
            `kb_banner_id` int(11) NOT NULL AUTO_INCREMENT,
            `status` int(2) NOT NULL,
            `image_type` enum('url','image') NOT NULL,
            `image_url` text NOT NULL,
            `type` enum('banner','slider') NOT NULL,
            `redirect_activity` enum('home','category','product') NOT NULL,
            `category_id` int(10) DEFAULT NULL,
            `product_id` int(10) DEFAULT NULL,
            `product_name` varchar(250) DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`kb_banner_id`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
        
        $fcmquery = "CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."kb_fcm_details` (
            `fcm_details_id` int(11) NOT NULL AUTO_INCREMENT,
            `kb_cart_id` int(10) NOT NULL,
            `fcm_id` text NOT NULL,
            `device_type` varchar(25) DEFAULT NULL,
            `notification_sent_status` int(2) DEFAULT NULL,
            `date_add` datetime NOT NULL,
            `date_upd` datetime NOT NULL,
            PRIMARY KEY (`fcm_details_id`)
        ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8";
        
        
        Db::getInstance()->execute($notificationquery);
        Db::getInstance()->execute($bannerquery);
        Db::getInstance()->execute($fcmquery);
        /* @author- Rishabh Jain
         * DOM : 19th sep 2018
         * purpose: To add otp and fingerprint functionality in app
         * Addng new table while installing which will
         * store the Phone number and unique id for fingerprint
         */
        $create_table = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kbmobileApp_unique_verification` (
            `id_verification` int(10) UNSIGNED NOT NULL auto_increment,
            `id_customer` int(10) UNSIGNED NOT NULL,
            `id_shop` int(10) UNSIGNED NOT NULL,
            `mobile_number` VARCHAR(100),
            `country_code` VARCHAR(10),
            `fid` VARCHAR(100),
            `date_added` datetime NOT NULL,
            `date_update` datetime NOT NULL,
             PRIMARY KEY (`id_verification`))';

        if (!Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($create_table)) {
            $this->custom_errors[] = $this->l('Error while installing database.');
            return false;
        }
        /*
         * Changes over
         */
    }
    
    /*
     * Alter database tables while installing
     */
    private function alterDatabaseTables()
    {
        $query = "SELECT COLUMN_NAME
            from information_schema.COLUMNS
            where TABLE_NAME = '"._DB_PREFIX_."kb_fcm_details'
            and COLUMN_NAME = 'device_type'";
        
        if (!$row = Db::getInstance()->executeS($query)) {
            $altertable = "ALTER TABLE `"._DB_PREFIX_."kb_fcm_details`
                ADD COLUMN `device_type` varchar(25) DEFAULT NULL";
            Db::getInstance()->execute($altertable);
        }
    }
    /*
     * Execute while uninstalling the module
     * 
     * @return bool
     */
    public function uninstall()
    {
        if (!parent::uninstall()
                || !$this->unregisterHook('displayOverrideTemplate')
                || !$this->unregisterHook('displayOrderConfirmation')
                || !$this->unregisterHook('displayAdminProductsExtra')
                || !$this->unregisterHook('actionProductSave')
                || !$this->unregisterHook('displayBackOfficeHeader')
                || !$this->unregisterHook('displayHeader')
                || !$this->unregisterHook('actionOrderStatusPostUpdate')
                || !$this->unregisterHook('newOrder')
            ) {
            return false;
        }
        
        Configuration::deleteByName('KB_MOBILE_APP');
        Configuration::deleteByName('KB_MOBILE_APP_MARKETPLACE');
        Configuration::deleteByName('KB_MOBILE_APP_ERROR_REPORTING');
        Configuration::deleteByName('KB_MOBILE_APP_CHAT_SUPPORT');
        /* start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
        Configuration::deleteByName('KB_MOBILE_APP_SPIN_WIN');
        Configuration::deleteByName('KB_MOBILE_APP_CART_OPTION_REDIRECT');
        Configuration::deleteByName('KB_MOBILE_APP_ADD_LOGO_NAVIGATION');
        Configuration::deleteByName('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH');
        Configuration::deleteByName('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH');
        Configuration::deleteByName('KB_MOBILE_APP_ENABLED_CMS');
        /* end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
        //start: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
        Configuration::deleteByName('KB_MOBILE_APP_DISABLED_SHIPPING');
        //end: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
         /* changes done by rishabh jain on 3rd sep 2018
         * to delete urlencoding congiguration setting after uninstalling
         */
        Configuration::deleteByName('KB_MOBILEAPP_URL_ENCODING');
        /* Changes over */
        return true;
    }

    /*
     * Called when we configure the module
     * 
     * @return string configuration form html
     */
    public function getContent()
    {
        $html = null;
        $this->tab_display = 'GeneralSettings';

        
        $error = false;
        $show_save_message = false;
        
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }
        
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $this->module_url = $module_dir.'kbmobileapp/';


        if (Tools::getIsset('send_notification') && Tools::getValue('send_notification')) {
            $this->sendPushNotification();
        }
        // changes by rishabh jain
        if (Tools::getIsset('assign_component_id') && Tools::getValue('assign_component_id')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $component_type = Tools::getValue(component_type);
            $position = 1;
            $sql = 'Select id From ' . _DB_PREFIX_ . 'kbmobileapp_component_types where component_name= "' . psql($component_type).'"';
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $sql = 'Select position From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_layout = ' . (int)$id_layout .' Order by position desc';
            $position = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $position  = $position +1;
            $sql= 'insert into ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component
                        (`id_layout`,`id_component_type`,`position`)
                             values('.$id_layout.','.$id_component_type.','.$position.')';
            if (Db::getInstance()->execute($sql)) {
                $result =  Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                die($result);
            }
        }
        if (Tools::getIsset('setComponentOrder') && Tools::getValue('setComponentOrder')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $position_array = array();
            $position = Tools::getValue(position_array);
            $position_array = explode(",", $position);
            if (count($position_array) > 0) {
                $position = 1;
                foreach ($position_array as $key => $pos_val) {
                    if ($pos_val != '') {
                        $position_arr = explode("_", $pos_val);
                        if (isset($position_arr[3])) {
                            $sql = 'update ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component set 
                        	position =' . (int) $position . ' where id_component=' . (int) $position_arr[3];
                            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                            $position += 1;
                        }
                    }
                }
            }
            die();
        }
        if (Tools::getIsset('saveBannerSliderFormData') && Tools::getValue('saveBannerSliderFormData')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $image_url = Tools::getValue(image_url);
            $image_type = Tools::getValue(image_type);
            $redirect_activity = Tools::getValue(redirect_activity);
            $category_id = Tools::getValue('category_id', 0);
            $image_content_mode = Tools::getValue('image_content_mode', '');
            $redirect_product_id = Tools::getValue('redirect_product_id', 0);
            if ($redirect_product_id == '') {
                $redirect_product_id = 0;
            }
            $languages = Language::getLanguages();
            $banner_heading = array();
            foreach ($languages as $k => $language) {
                $key = $k+1;
                $banner_heading[$language['id_lang']] = Tools::getValue('banner_heading_'.$language['id_lang'], '');
            }
            $banner_heading_data = serialize($banner_heading);
            $sql = 'update ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component set 
                        	component_heading="' . pSQL($banner_heading_data) . '" where id_component=' . (int) $id_component;
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
            // changes for file upload
            if ($image_type == 'image') {
                if (isset($_FILES['image'])) {
                    if ($this->checkSecureUrl()) {
                        $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                    } else {
                        $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                    }
                    $file = $_FILES['image'];
                    if (($file['error'] == 0) && !empty($file['name'])) {
                        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $image_name = 'kbmobileapp_' . time() . '.jpg';
                        $path = _PS_IMG_DIR_ . 'kbmobileapp/' . $image_name;
                        move_uploaded_file(
                            $_FILES['image']['tmp_name'],
                            $path
                        );
                        chmod(_PS_IMG_DIR_ . 'kbmobileapp/' . time(), 0777);
                        $image_url = $module_dir . 'kbmobileapp/'.$image_name;
                    }
                }
            } else {
                $image_url = $image_url;
            }
            $product_name = Tools::getValue(redirect_product_name);
            $countdown = Tools::getValue('countdown_validity', '');
            $is_enabled_background_color = Tools::getValue('is_enabled_background_color', 0);
            $background_color = Tools::getValue('timer_background_color', '');
            $timer_text_color = Tools::getValue('timer_text_color', '');
            $image_path = '';
            //$component_type = 'banner_square';
            $position = 1;
            $sql = 'Select id_component_type From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component= ' .(int) $id_component;
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $sql= 'insert into ' . _DB_PREFIX_ . 'kbmobileapp_banners
                        (`id_component`,
                        `id_banner_type`,`countdown`,
                        `product_id`,`category_id`,
                        `redirect_activity`,`image_url`,
                        `image_type`,`product_name`,
                        `image_path`,`image_contentMode`,
                        `background_color`,`text_color`,
                        `banner_heading`,`is_enabled_background_color`
                        ) values(
                        '.$id_component.','.$id_component_type.',"'.psql($countdown).'",'.$redirect_product_id.','.$category_id.',"'.$redirect_activity.'","'.$image_url.'","'.$image_type.'","'.$product_name.'","'.$image_path.'","'.psql($image_content_mode).'","'.psql($background_color).'","'.psql($timer_text_color).'","",'.$is_enabled_background_color.')';
            if (Db::getInstance()->execute($sql)) {
                $result =  Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                $this->getSliderForm(true, $id_component_type);
            }
        }
        if (Tools::getIsset('getComponentType') && Tools::getValue('getComponentType')) {
            $id_component = (int) Tools::getValue(id_component);
            $sql = 'Select id_component_type From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component= ' .(int) $id_component;
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $sql = 'Select component_name From ' . _DB_PREFIX_ . 'kbmobileapp_component_types where id= ' .(int) $id_component_type;
            $id_component_name = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            die($id_component_name);
        }
        if (Tools::getIsset('saveProductFormData') && Tools::getValue('saveProductFormData')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $category_id = Tools::getValue('category_id', 0);
            $number_of_product = Tools::getValue('number_of_product', 0);
            $product_list = Tools::getValue(product_list);
            $category_products = Tools::getValue(category_products);
            $image_content_mode = Tools::getValue('image_content_mode', '');
            $product_type = Tools::getValue('product_type', '');
            $languages = Language::getLanguages();
            $components_heading = array();
            foreach ($languages as $k => $language) {
                $key = $k+1;
                $components_heading[$language['id_lang']] = Tools::getValue('component_heading_'.$language['id_lang'], '');
            }
            $component_heading_data = serialize($components_heading);
            $sql = 'update ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component set 
                        	component_heading="' . pSQL($component_heading_data) . '" where id_component=' . (int) $id_component;
            Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
            $position = 1;
            $sql = 'Select id_component_type From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component= ' .(int) $id_component;
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            
            
            $sql = 'Select id From ' . _DB_PREFIX_ . 'kbmobileapp_product_data where id_component= ' . (int)$id_component;
            $exists = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($exists) {
                $sql = 'update ' . _DB_PREFIX_ . 'kbmobileapp_product_data set 
                        id_component=' . $id_component .', product_type = "'. psql($product_type).'",category_products = "'. psql($category_products).'",custom_products = "'. psql($product_list).'",
                        number_of_products = ' .(int)$number_of_product .', id_category = '. (int)$category_id .',image_content_mode = "'. psql($image_content_mode).'"  where id_component=' . (int) $id_component;
                if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql)) {
                    $this->getProductForm(true, $id_component_type);
                }
            } else {
                $sql= 'insert into ' . _DB_PREFIX_ . 'kbmobileapp_product_data
                            (`id_component`,
                            `product_type`,`category_products`,
                            `custom_products`,`image_content_mode`,
                            `number_of_products`,`id_category`
                            ) values(
                            '.$id_component.',"'.psql($product_type).'","'.psql($category_products).'","'.psql($product_list).'","'.$image_content_mode.'",'.(int) $number_of_product.','.(int) $category_id.')';
                if (Db::getInstance()->execute($sql)) {
                    $result =  Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                    $this->getProductForm(true, $id_component_type);
                }
            }
        }
        if (Tools::getIsset('saveTopcategoryFormData') && Tools::getValue('saveTopcategoryFormData')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = Tools::getValue(id_component);
            $image_content_mode = Tools::getValue('image_content_mode', '');
            $component_type = 'top_category';
            $sql = 'Select id From ' . _DB_PREFIX_ . 'kbmobileapp_component_types where component_name= "' . psql($component_type).'"';
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $id_category_array = '';
            $image_name_array = '';
            for ($i=1; $i<=8; $i++) {
                $position = 1;
                if (Tools::getvalue('id_category_'.$i) > 0) {
                    $id_category_array .= (int) Tools::getvalue('id_category_'.$i) .'|';
                    if (isset($_FILES['image_'.$i])) {
                        if ($this->checkSecureUrl()) {
                            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                        } else {
                            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                        }
                        $file = $_FILES['image_'.$i];
                        if (($file['error'] == 0) && !empty($file['name'])) {
                            $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
                            $image_name = 'kbmobileapp_tc_'.$i .'_'. time() . '.jpg';
                            $path = _PS_IMG_DIR_ . 'kbmobileapp/' . $image_name;
                            move_uploaded_file(
                                $_FILES['image_'.$i]['tmp_name'],
                                $path
                            );
                            chmod(_PS_IMG_DIR_ . 'kbmobileapp/', 0777);
                            $image_name_array .=  $image_name .'|';
                            //$image_url = $module_dir . 'kbmobileapp/'.$image_name;
                        }
                    } else {
                        $sql = 'Select * From ' . _DB_PREFIX_ . 'kbmobileapp_top_category where id_component= ' . (int)$id_component;
                        $categories_array = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
                        if (count($categories_array) > 0) {
                            $cat_arr = explode('|', $categories_array[0]['id_category']);
                            $image_arr = array();
                            $image_arr = explode('|', $categories_array[0]['image_url']);
                            $id_category_component = $i - 1;
                            $image_name_array .= $image_arr[$id_category_component] .'|';
                        } else {
                            $image_name_array .= '|';
                        }
                    }
                }
            }
            $sql = 'Select id From ' . _DB_PREFIX_ . 'kbmobileapp_top_category where id_component= ' . (int)$id_component;
            $exists = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            if ($exists) {
                $sql = 'update ' . _DB_PREFIX_ . 'kbmobileapp_top_category set 
                        id_category="' . pSQL($id_category_array) . '", image_url = "'. psql($image_name_array).'", image_content_mode = "'. psql($image_content_mode).'" where id_component=' . (int) $id_component;
                if (Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql)) {
                    $this->getTopcategoryForm(true);
                }
            } else {
                $sql= 'insert into ' . _DB_PREFIX_ . 'kbmobileapp_top_category
                            (`id_component`,
                            `id_category`,
                            `image_content_mode`,
                            `image_url`
                            ) values(
                            '.$id_component.',"'.psql($id_category_array).'","'.psql($image_content_mode).'", "'.psql($image_name_array).'")';

                if (Db::getInstance()->execute($sql)) {
                    $result =  Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                    $this->getTopcategoryForm(true);
                }
            }
        }
        if (Tools::getIsset('deleteTopCategoryImage') && Tools::getValue('deleteTopCategoryImage')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = Tools::getValue(id_component);
            $id_category_component = Tools::getValue(id_category_component);
            
            $sql = 'Select * From ' . _DB_PREFIX_ . 'kbmobileapp_top_category where id_component= ' . (int)$id_component;
            $categories = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            if (count($categories) > 0) {
                $category_array = explode('|', $categories[0]['id_category']);
                $image_array = array();
                $image_array = explode('|', $categories[0]['image_url']);
                $id_category_component = $id_category_component - 1;
                $image_array[$id_category_component] = '';
                $image_name_array = '';
                $length = count($image_array);
                foreach ($image_array as $key => $value) {
                    if ($key < $length - 1) {
                        $image_name_array .= $value.'|';
                    }
                }
                $sql = 'update ' . _DB_PREFIX_ . 'kbmobileapp_top_category set 
                        image_url = "'. psql($image_name_array).'" where id_component=' . (int) $id_component;
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($sql);
                die('ok');
            }
        }
        if (Tools::getIsset('getlayoutComponent') && Tools::getValue('getlayoutComponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $sql = 'Select * From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_layout= ' . (int)$id_layout.' order by position asc';
            $resultant_components = DB::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $components = array();
            foreach ($resultant_components as $key => $comp) {
                $sql = 'Select component_name From ' . _DB_PREFIX_ . 'kbmobileapp_component_types where id= '.(int)$comp['id_component_type'];
                $component_name = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
                $components[$key]['id'] = $comp['id_component'];
                $components[$key]['type'] = $component_name;
            }
            echo Tools::jsonEncode($components);
            die;
        }
        if (Tools::getIsset('getCategoryProducts') && Tools::getValue('getCategoryProducts')) {
            $id_category = (int) Tools::getValue('id_category', 0);
            $data = $this->getCategoryProductsOption($id_category);
            echo Tools::jsonEncode($data);
            die;
        }
        if (Tools::getIsset('getTopcategoryImageUrl') && Tools::getValue('getTopcategoryImageUrl')) {
            $id_component = Tools::getValue('id_component');
            $sql = 'SELECT id_category,image_url  FROM ' . _DB_PREFIX_ . 'kbmobileapp_top_category
                where id_component = '.(int)$id_component;
            $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $image_url_array = array();
            if ($this->checkSecureUrl()) {
                $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
            } else {
                $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
            }
            if (count($categories) > 0) {
                foreach ($categories as $cat) {
                    $image_array = array();
                    $image_array = explode('|', $cat['image_url']);
                }
                if (count($image_array) > 0) {
                    $total_image = count($image_array) - 1;
                    foreach ($image_array as $key => $image_value) {
                        $k = $key+1;
                        if ($key <= $total_image) {
                            if ($image_value != '') {
                                $image_url_array[$key]['name'] = 'sliderimage_'.$k;
                                $image_url_array[$key]['value'] = $module_dir . 'kbmobileapp/'.$image_value;
                            } else {
                                $image_url_array[$key]['name'] = 'sliderimage_'.$k;
                                $image_url_array[$key]['value'] = '';
                            }
                        }
                    }
                }
            }
            echo Tools::jsonEncode($image_url_array);
            die();
        }
        if (Tools::getIsset('deleteSliderBanner') && Tools::getValue('deleteSliderBanner')) {
            $id_banner = (int) Tools::getValue(id_banner);
            $id_component = (int) Tools::getValue(id_component);
            $sql = 'Select id_component_type From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component= ' .(int) $id_component;
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kbmobileapp_banners where id = ' . (int)$id_banner;
            $row = Db::getInstance()->execute($sql);
            $this->getSliderForm(true, $id_component_type);
        }
        if (Tools::getIsset('getlayoutNameForm') && Tools::getValue('getlayoutNameForm')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $this->getLayoutNameForm($id_layout);
        }
        if (Tools::getIsset('savelayoutNameForm') && Tools::getValue('savelayoutNameForm')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $layout_name = Tools::getValue(layout_name);
            if ($id_layout) {
                $update_title = 'update ' . _DB_PREFIX_ . 'kb_mobileapp_layouts set 
                            layout_name="' . pSQL($layout_name) . '" where id_layout=' . (int) $id_layout;
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_title);
            } else {
                $sql= 'insert into ' . _DB_PREFIX_ . 'kb_mobileapp_layouts
                        (`layout_name`)
                             values("'.psql($layout_name).'")';
                Db::getInstance()->execute($sql);
            }
            $this->getlayoutList(true);
        }
        if (Tools::getIsset('deleteBannerSquarecomponent') && Tools::getValue('deleteBannerSquarecomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'banner_square';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteBannerHorizontalcomponent') && Tools::getValue('deleteBannerHorizontalcomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'banner_horizontal_slider';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteBannerGridcomponent') && Tools::getValue('deleteBannerGridcomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'banners_grid';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteBannerCountdowncomponent') && Tools::getValue('deleteBannerCountdowncomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'banners_countdown';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kbmobileapp_banners where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        
        if (Tools::getIsset('deleteTopcategorycomponent') && Tools::getValue('deleteTopcategorycomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'banner_square';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kbmobileapp_top_category where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteLastAccesscomponent') && Tools::getValue('deleteLastAccesscomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'products_recent';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteProductGridcomponent') && Tools::getValue('deleteProductGridcomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'products_grid';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteProductSquarecomponent') && Tools::getValue('deleteProductSquarecomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'products_square';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        if (Tools::getIsset('deleteProductHorizonatlcomponent') && Tools::getValue('deleteProductHorizonatlcomponent')) {
            $id_layout = (int) Tools::getValue(id_layout);
            $id_component = (int) Tools::getValue(id_component);
            $component_type = 'products_horizontal';
            $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component = ' . (int)$id_component;
            $row = Db::getInstance()->execute($sql);
            die();
        }
        // changes over
        if (Tools::getIsset('change_slider_banner_status') && Tools::getValue('change_slider_banner_status')) {
            $id_banner = Tools::getValue('id_slider', 0);
            $status = Tools::getValue('status', 0);
            if ($id_banner) {
                $this->changeSliderBannerStatus($id_banner, $status);
            }
        }


        if (Tools::getIsset('save_payment_methods') && Tools::getValue('save_payment_methods') == 1) {
            if ($this->setPaymentMethods()) {
                $html .= $this->displayConfirmation($this->l('Payment Method Info has been added successfully.'));
            } else {
                $html .= $this->displayError($this->l('Error in saving the data.'));
            }
        }
        
        if (Tools::getIsset('kb_banner_id') && Tools::getValue('kb_banner_id') != '') {
            $result = $this->setSliderBannerData();
            if (!$result['error']) {
                $html .= $this->displayConfirmation($result['msg']);
            } else {
                $html .= $this->displayError($result['msg']);
            }
        }
        /* start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
        if (Tools::getIsset('KB_MOBILE_APP_SPIN_WIN')) {
            $show_save_message = true;
            //die(Tools::getValue('KB_MOBILE_APP_SPIN_WIN'));
            //die(print_r(Module::isInstalled('spinwheel')));
            if (Tools::getValue('KB_MOBILE_APP_SPIN_WIN')==1) {
                if (Module::isInstalled('spinwheel') && Module::isEnabled('spinwheel')) {
                    $show_save_message = true;
                    if (!Configuration::updateValue('KB_MOBILE_APP_SPIN_WIN', Tools::getValue('KB_MOBILE_APP_SPIN_WIN'))) {
                        $error = true;
                        $html .= $this->displayError($this->l('Error occurred while enabling spin and win functionality.'));
                    }
                } else {
                    Configuration::updateValue('KB_MOBILE_APP_SPIN_WIN', 0);
                    $error = true;
                    $html .= $this->displayError($this->l('Kindly install and enable the Spin Win module first to enable its functionality in the app'));
                }
            } else {
                Configuration::updateValue('KB_MOBILE_APP_SPIN_WIN', 0);
                $show_save_message=true;
            }
        }
        if (Tools::getIsset('KB_MOBILE_APP_CART_OPTION_REDIRECT')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_CART_OPTION_REDIRECT', Tools::getValue('KB_MOBILE_APP_CART_OPTION_REDIRECT'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while enabling mobile app.'));
            }
        }
        if (Tools::getIsset('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH', Tools::getValue('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while enabling mobile app logo.'));
            }
        }
        // CHANGES BY RISHABH JAIN
        if (Tools::getIsset('KBMOBILEAPP_HOME_PAGE_LAYOUT')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KBMOBILEAPP_HOME_PAGE_LAYOUT', Tools::getValue('KBMOBILEAPP_HOME_PAGE_LAYOUT'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while Saving Home page Layout.'));
            }
        }
        if (Tools::getIsset('KBMOBILEAPP_APP_BUTTON_COLOR')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KBMOBILEAPP_APP_BUTTON_COLOR', Tools::getValue('KBMOBILEAPP_APP_BUTTON_COLOR'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while Saving Home page Layout.'));
            }
        }
        if (Tools::getIsset('KBMOBILEAPP_APP_THEME_COLOR')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KBMOBILEAPP_APP_THEME_COLOR', Tools::getValue('KBMOBILEAPP_APP_THEME_COLOR'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while Saving Home page Layout.'));
            }
        }

        if (Tools::getIsset('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH', Tools::getValue('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while enabling displaying short description switch.'));
            }
        }
        $this->context->smarty->assign('logo_url', Configuration::get('KB_MOBILE_APP_ADD_LOGO_NAVIGATION'));
        //die(print_r($_FILES['KB_MOBILE_APP_ADD_LOGO_NAVIGATION']));
        if (Tools::getValue('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH') == 1) {
            if ($_FILES['KB_MOBILE_APP_ADD_LOGO_NAVIGATION']['size'] == 0) {
            } else {
                $file_mimetypes = array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                );
                if (in_array($_FILES['KB_MOBILE_APP_ADD_LOGO_NAVIGATION']['type'], $file_mimetypes)) {
                    if ($_FILES['KB_MOBILE_APP_ADD_LOGO_NAVIGATION']['error'] > 0) {
                    } else {
                        $file_name = $_FILES['KB_MOBILE_APP_ADD_LOGO_NAVIGATION']['name'];
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $base_file_name = basename($file_name, $ext);
                        $mask = _PS_IMG_DIR_ . 'kbmobileapp/' . $base_file_name . '.*';
                        $matches = glob($mask);
                        if (count($matches) > 0) {
                            array_map('unlink', $matches);
                        }

                        $path = _PS_IMG_DIR_ . 'kbmobileapp';
                        if (!move_uploaded_file($_FILES['KB_MOBILE_APP_ADD_LOGO_NAVIGATION']['tmp_name'], $path . '/' . $file_name)) {
                            $error = true;
                            $html.= $this->displayError($this->l('Error in uploading a file'));
                        } else {
                            $module_dir = '';
                            if ($this->checkSecureUrl()) {
                                $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                            } else {
                                $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                            }
                            $notification_image_url = $module_dir . 'kbmobileapp/' . $file_name;
                            $this->context->smarty->assign('logo_url', $notification_image_url);
                            $show_save_message = true;
                            Configuration::updateValue('KB_MOBILE_APP_ADD_LOGO_NAVIGATION', $notification_image_url);
                        }
                    }
                } else {
                    $error = true;
                    $html .= $this->displayError($this->l('Invalid File Format'));
                }
            }
        }
        if (Tools::getIsset('KB_MOBILE_APP_ENABLED_CMS')) {
            $array = Tools::getValue('KB_MOBILE_APP_ENABLED_CMS');
            $show_save_message = true;
            if (!Configuration::updateValue('KB_ENABLED_CMS', serialize($array))) {
                $temp_err = $this->l('Error occurred while setting CMS pages for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        } else {
            Configuration::updateValue('KB_ENABLED_CMS', '');
        }
        /* end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
        //start: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
        if (Tools::getIsset('KB_MOBILE_APP_DISABLED_SHIPPING')) {
            $array = Tools::getValue('KB_MOBILE_APP_DISABLED_SHIPPING');
            $show_save_message = true;
            if (!Configuration::updateValue('KB_DISABLED_SHIPPING', serialize($array))) {
                $temp_err = $this->l('Error occurred while setting shipping methods for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        } else {
            Configuration::updateValue('KB_DISABLED_SHIPPING', '');
        }
        //end: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
        if (Tools::isSubmit('edit')) {
            $code = Tools::getValue('code', '');
            $this->getPaymentData($code);
        }
        
        if (Tools::isSubmit('editSlider')) {
            $id = Tools::getValue('id_slider', '');
            $this->getSliderData($id);
        }

        if (Tools::isSubmit('payment_form')) {
            $this->getPaymentForm(true);
        }

        if (Tools::isSubmit('enable_disable')) {
            $payment_code = Tools::getValue('code', '');
            $this->changePaymentMethodStatus($payment_code);
            $this->getPaymentList(true);
        }

        if (Tools::isSubmit('delete')) {
            $code = Tools::getValue('code', '');
            $this->deletePaymentMethod($code);
            $this->getPaymentForm(true);
        }
        // changes by rishabh jain
        if (Tools::isSubmit('delete_layout')) {
            $code = Tools::getValue('code', '');
            $this->deleteLayout($code);
            $this->getlayoutList(true);
        }
        if (Tools::isSubmit('getBannerForm')) {
            $id_component = (int) Tools::getValue(id_component);
            $sql = 'Select id_component_type From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component= ' .(int) $id_component;
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $this->getSliderForm(true, $id_component_type);
        }
        if (Tools::isSubmit('getProductForm')) {
            $id_component = (int) Tools::getValue(id_component);
            $id_layout = (int) Tools::getValue(id_layout);
            $sql = 'Select id_component_type From ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component= ' .(int) $id_component;
            $id_component_type = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $this->getProductForm(true, $id_component_type);
        }
        if (Tools::isSubmit('getCategoryForm')) {
            $this->getTopcategoryForm(true);
        }
        // changes over
        if (Tools::isSubmit('show_notification_details')) {
            $notification_id = Tools::getValue('notification_id', '');
            $this->showNotificationDetails($notification_id);
        }

        /* Save Cutom CSS Value */
        if (Tools::getIsset('KB_MOBILEAPP_CSS')) {
            $show_save_message = true;
            $custom_css = urlencode(Tools::getValue('KB_MOBILEAPP_CSS'));
            $custom_css = serialize($custom_css);
            Configuration::updateValue('KB_MOBILEAPP_CSS', $custom_css);
        }

        /* Save Enable/Disable Value */
        if (Tools::getIsset('KB_MOBILE_APP')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP', Tools::getValue('KB_MOBILE_APP'))) {
                $error = true;
                $html .= $this->displayError($this->l('Error occurred while enabling mobile app.'));
            }
        }

        /* Save Error Reporting Value */
        if (Tools::getIsset('KB_MOBILE_APP_ERROR_REPORTING')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_ERROR_REPORTING', Tools::getValue('KB_MOBILE_APP_ERROR_REPORTING'))) {
                $temp_err = $this->l('Error occurred while enabling error reporting for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        
        /* Save Error Chat support status Value */
        if (Tools::getIsset('KB_MOBILE_APP_CHAT_SUPPORT')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_CHAT_SUPPORT', Tools::getValue('KB_MOBILE_APP_CHAT_SUPPORT'))) {
                $temp_err = $this->l('Error occurred while enabling chat support for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* Save Error Chat support Value */
        if (Tools::getIsset('KB_MOBILE_APP_CHAT_SUPPORT_KEY')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_CHAT_SUPPORT_KEY', Tools::getValue('KB_MOBILE_APP_CHAT_SUPPORT_KEY'))) {
                $temp_err = $this->l('Error occurred while saving the chat support key for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        
        

        /* Save marketplace enable/disable value */
        if (Tools::getIsset('KB_MOBILE_APP_MARKETPLACE')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_APP_MARKETPLACE', Tools::getValue('KB_MOBILE_APP_MARKETPLACE'))) {
                $temp_err = $this->l('Error occurred while enabling marketplace in mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* changes started
         * @author : Rishabh Jain
         * Dom : 25/09/2018
         * to save the whatsapp configuration value
         */
        /* Save Error Chat support status Value */
        if (Tools::getIsset('KB_MOBILE_WHATSAPP_CHAT_SUPPORT')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_WHATSAPP_CHAT_SUPPORT', Tools::getValue('KB_MOBILE_WHATSAPP_CHAT_SUPPORT'))) {
                $temp_err = $this->l('Error occurred while enabling WHATSAPP chat support for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* Save Error Chat support Value */
        if (Tools::getIsset('KB_MOBILE_WHATSAPP_CHAT_NUMBER')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILE_WHATSAPP_CHAT_NUMBER', Tools::getValue('KB_MOBILE_WHATSAPP_CHAT_NUMBER'))) {
                $temp_err = $this->l('Error occurred while saving the WHATSAPP chat support number for mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* Changes over */
        /* Changes started
         * @author :Rishabh Jain
         * DOm : 19th Sep 2018
         * to save phone number fields
        */
        if (Tools::getIsset('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION', Tools::getValue('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION'))) {
                $temp_err = $this->l('Error occurred while enabling/disabling Phone number registration/login in mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        if (Tools::getIsset('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY', Tools::getValue('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY'))) {
                $temp_err = $this->l('Error occurred while setting required or optional Phone number field in registration form in mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* changes over - done by rishabh jain */
        
        /* Changes started
         * @author :Rishabh Jain
         * DOm : 25/09/2018
         * to save fingerprint field
        */
        if (Tools::getIsset('KB_MOBILEAPP_FINGERPRINT_LOGIN')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILEAPP_FINGERPRINT_LOGIN', Tools::getValue('KB_MOBILEAPP_FINGERPRINT_LOGIN'))) {
                $temp_err = $this->l('Error occurred while enabling/disabling Fingerprint login in mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* changes over - done by rishabh jain */
        /* Changes started by rishah jain on 3rd sep 2018
         * Save Image url encoding decoding enable/disable value 
        */
        if (Tools::getIsset('KB_MOBILEAPP_URL_ENCODING')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILEAPP_URL_ENCODING', Tools::getValue('KB_MOBILEAPP_URL_ENCODING'))) {
                $temp_err = $this->l('Error occurred while enabling/disabling url encoding in mobile app.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /* changes over - done by rishabh jain */
        
        /* Save Firebase server key value */
        if (Tools::getIsset('KB_MOBILEAPP_FIREBASE_KEY')) {
            $show_save_message = true;
            if (!Configuration::updateValue('KB_MOBILEAPP_FIREBASE_KEY', Tools::getValue('KB_MOBILEAPP_FIREBASE_KEY'))) {
                $temp_err = $this->l('Error occurred while saving Firebase server key.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        
        /* Save Push Notification settings */
        if (Tools::getIsset('push_notification')) {
            $show_save_message = true;
            $notification_settings = serialize(Tools::getValue('push_notification'));
            if (!Configuration::updateValue('KB_MOBILEAPP_NOTIFICATION_DATA', $notification_settings)) {
                $temp_err = $this->l('Error occurred while saving push notification data.');
                $html .= $this->displayError($temp_err);
                $error = true;
            }
        }
        /*Changes start by rishabh jain on 4th sep 2018
         * To disable the facebook and google functionality from admin end.
         * as presently it is creating some issues in ios app.
         */
//        /* Save Facebook Settings */
//        if (Tools::getIsset('facebook_setup_status') || Tools::getIsset('facebook_setup_app_id')) {
//            $show_save_message = true;
//            
//            $data = array('status' => Tools::getValue('facebook_setup_status'), 'app_id' => Tools::getValue('facebook_setup_app_id'));
//            $facebook_settings = serialize($data);
//            if (!Configuration::updateValue('KB_MOBILEAPP_FACEBOOK_DATA', $facebook_settings)) {
//                $temp_err = $this->l('Error occurred while saving facebook data.');
//                $html .= $this->displayError($temp_err);
//                $error = true;
//            }
//        }
//        
//        /* Save Google Settings */
//        if (Tools::isSubmit('google_status')) {
//            $show_save_message = true;
//            
//            $status = $this->saveGoogleData();
//            if ($status['error']) {
//                $html .= $this->displayError($status['msg']);
//                $error = true;
//            } else {
//                $google_setting = Tools::getValue('google_status');
//                if (!Configuration::updateValue('KB_MOBILEAPP_GOOGLE_DATA', $google_setting)) {
//                    $temp_err = $this->l('Error occurred while saving google data.');
//                    $html .= $this->displayError($temp_err);
//                    $error = true;
//                }
//            }
//        }
//        
//        
//        /*Validating FB key */
//        if (Tools::getIsset('validatefbkey') && Tools::getValue('validatefbkey')) {
//            $key = Tools::getValue('key', 0);
//            if ($key) {
//                $this->validateFBKey($key);
//                die;
//            } else {
//                die("false");
//            }
//        }
        /* Changes over */
        if (Tools::getvalue('ajaxproductaction')) {
            echo $this->ajaxproductlist();
            die;
        }

        if (!$error && $show_save_message) {
            $html .= $this->displayConfirmation($this->l('Configuration has been saved successfully.'));
        }

        return $html . $this->renderAdminConfigurationHtml().$this->display(__FILE__, 'views/templates/admin/preview_popup.tpl');
    }

    /*
     * Set the helper view,list and form variables
     * called from getContent() method
     * 
     * @return string configuration form html
     */
    private function renderAdminConfigurationHtml()
    {

        $output = null;
        $this->available_tabs_lang = array(
            'GeneralSettings' => $this->l('General Settings'),
            'PushNotificationSettings' => $this->l('Push Notification Settings'),
            'PushNotificationHistory' => $this->l('Push Notification History'),
            'SlidersSettings' => $this->l('Sliders Settings'),
            'BannersSettings' => $this->l('Banners Settings'),
            'PaymentMethods' => $this->l('Payment Methods'),
            /* changes started by
             * @author- Rishabh jain
             * DOm : 19th Dec 18
             * to add the layout tab
             */
            'layoutsettings' => $this->l('Home page Layout'),
            /* chnages over */
            /*start: changes made by Aayushi Agarwal on 1 Dec 2018 to add shipping method functionality*/
           // 'ShippingMethods' => $this->l('Shipping Methods'),
            /*end*/
            /* Changes start by rishabh jain on 4th sep 2018
             * To disable the facebook and google functionality from admin end.
             * as presently it is creating some issues in ios app.
             */
//            'GoogleSetup' => $this->l('Google Setup'),
//            'FacebookSetup' => $this->l('Facebook Setup')
            /* Changes over */
        );
        $this->available_tabs = array(
            'GeneralSettings',
            'PushNotificationSettings',
            'PushNotificationHistory',
//            'SlidersSettings',
//            'BannersSettings',
            'PaymentMethods',
            /* changes started by
             * @author- Rishabh jain
             * DOm : 19th Dec 18
             * to add the layout tab
             */
            'layoutsettings',
            /* chnages over */
            /*start: changes made by Aayushi Agarwal on 1 Dec 2018 to add shipping method functionality*/
           // 'ShippingMethods',
            /*end*/
            /* Changes start by rishabh jain on 4th sep 2018
             * To disable the facebook and google functionality from admin end.
             * as presently it is creating some issues in ios app.
             */
//            'GoogleSetup',
//            'FacebookSetup'
            /* Changes over */
        );
        /* start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
        $cms_array = array();
        $cmslinks = CMS::getCMSPages();
        $index = 0;
        if ($cmslinks) {
            foreach ($cmslinks as $cmslink) {
                if ($cmslink['id_cms'] != '') {
                    $sql = 'select * from ' . _DB_PREFIX_ . 'cms_lang where id_cms=' . (int) $cmslink['id_cms'];
                    $cms_data = Db::getInstance()->getRow($sql);
                    $cms_array[$index] = array(
                        'name' => $cms_data['meta_title'],
                        'id_module' => $cmslink['id_cms']
                    );
                    $index++;
                }
            }
        }
        /* end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
        //start: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
        $shipping_methods = Carrier:: getCarriers($this->context->language->id, true, false, false, null, 5);
        $i = 0;
        $option1 = array();
        foreach ($shipping_methods as $shipping_options) {
            $option1[$i]['id_module'] = $shipping_options['id_carrier'];
            $option1[$i]['name'] = $shipping_options['name'];
            $i++;
        }
        $layout_option = array();
        $available_layouts = array();
        $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mobileapp_layouts';
        $available_layouts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        foreach ($available_layouts as $key => $layouts) {
            $layout_option[$key]['id'] = $layouts['id_layout'];
            $layout_option[$key]['name'] = $layouts['layout_name'];
        }
         
        //end: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
        /* Form fields for general Setting Form */
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('General Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'KB_MOBILE_APP',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable entire mobile app functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        //start: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Spin and Win:'),
                            'name' => 'KB_MOBILE_APP_SPIN_WIN',
                            //'class' => 't',
                            'id' => 'KB_MOBILE_APP_SPIN_WIN',
                            'required' => true,
                            'desc' => $this->l('It can be enabled only if module is installed'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Redirect on Cart Page when Add to Cart'),
                            'name' => 'KB_MOBILE_APP_CART_OPTION_REDIRECT',
                            //'class' => 't',
                            'id' => 'KB_MOBILE_APP_CART_OPTION_REDIRECT',
                            'required' => true,
                            'desc' => $this->l('redirect to cart page or keep on the product page when add to cart is clicked'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Display Short Description:'),
                            'name' => 'KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH',
                            // 'class' => 't',
                            'id' => 'KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH',
                            'required' => true,
                            //'desc' => $this->l('redirect to cart page or keep on the product page when add to cart is clicked'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Logo'),
                            'name' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH',
                            //'class' => 't',
                            'id' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH',
                            'required' => true,
                           
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image for logo:'),
                            'class' => '',
                            'name' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION',
                            'id' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION',
                            'display_image' => true,
                            'required' => false,
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview_logo.tpl'),
//                            'desc' => $this->l('Upload your Image')
                        ),
                        // changes for home page layout
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select layout for Home page'), // The <label> for this <select> tag.
                            'class' => 'chosen',
                            'id' => 'home_page_layout',
                            'hint' => $this->l('Select shipping methods from the list for which you want to disable this module.You can leave it to empty if you want to enable this module for all shipping methods'),
                            'name' => 'home_page_layout', // The content of the 'id' attribute of the <select> tag.
                            'options' => array(
                                'query' => $option1,
                                'id' => 'id_module',
                                'name' => 'name'
                            )
                        ),
                        array(
                        'label' => $this->l('App Button Color'),
                        'type' => 'color',
                        'hint' => $this->l('Select button color of the app.'),
                        'name' => 'app_button_color',
                        'id' => 'app_button_color',
                        'size' => 50,
                        'class' => 'kbsw_wheel_color',
                        'required' => true
                        ),
                        array(
                        'label' => $this->l('Timer Background Colour'),
                        'type' => 'color',
                        'hint' => $this->l('Select theme color of the app.'),
                        'name' => 'app_theme_color',
                        'id' => 'app_theme_color',
                        'size' => 50,
                        'class' => 'kbsw_wheel_color',
                        'required' => true
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Disabled Shipping Methods'), // The <label> for this <select> tag.
                            'multiple' => true,
                            'class' => 'chosen',
                            'id' => 'KB_MOBILE_APP_DISABLED_SHIPPING',
                            'hint' => $this->l('Select shipping methods from the list for which you want to disable this module.You can leave it to empty if you want to enable this module for all shipping methods'),
                            'name' => 'KB_MOBILE_APP_DISABLED_SHIPPING[]', // The content of the 'id' attribute of the <select> tag.
                            'options' => array(
                                'query' => $option1,
                                'id' => 'id_module',
                                'name' => 'name'
                            )
                        ),
                        //end: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
                        /* start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages*/
                        array(
                            'type' => 'select',
                            'label' => $this->l('Enabled CMS Pages'), // The <label> for this <select> tag.
                            'multiple' => true,
                            'class' => 'chosen',
                            'hint' => $this->l('Select cms pages which you want to display in your mobile app'),
                            'name' => 'KB_MOBILE_APP_ENABLED_CMS[]', // The content of the 'id' attribute of the <select> tag.
                            'options' => array(
                                'query' => $cms_array,
                                'id' => 'id_module',
                                'name' => 'name'
                            )
                        ),
                        /* end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
                        array(
                            'label' => $this->l('Enable Request Log Reporting'),
                            'type' => 'radio',
                            'is_bool' => true, //retro compat 1.5
                            'name' => 'KB_MOBILE_APP_ERROR_REPORTING',
                            'desc' => $this->l('This setting will enable/disable error log reporting for every request to Web Services of the module.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            )
                        ),
                        array(
                            'label' => $this->l('Enable Live Chat Support'),
                            'type' => 'radio',
                            'is_bool' => true, //retro compat 1.5
                            'name' => 'KB_MOBILE_APP_CHAT_SUPPORT',
                            'desc' => $this->l('This setting will enable/disable live chat support option in mobile app.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            )
                        ),
                        array(
                            'label' => $this->l('Chat API Key'),
                            'type' => 'text',
                            'hint' => $this->l('Enter API key'),
                            'class' => '',
                            'name' => 'KB_MOBILE_APP_CHAT_SUPPORT_KEY',
                        ),
                        /* Changes started
                         * @author : Rishabh Jain
                         * Dom : 25/09/2018
                         * To add option for enabling disabling whatsap chat
                         */
                        
                        array(
                            'label' => $this->l('Enable Whatsapp Chat Support'),
                            'type' => 'radio',
                            'is_bool' => true, //retro compat 1.5
                            'name' => 'KB_MOBILE_WHATSAPP_CHAT_SUPPORT',
                            'desc' => $this->l('This setting will enable/disable WHATSAPP chat support option in mobile app.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            )
                        ),
                        array(
                            'label' => $this->l('Chat Number'),
                            'type' => 'text',
                            'hint' => $this->l('Enter Chat Number'),
                            'desc' => $this->l('Enter Country code with Chat number as well'),
                            'class' => '',
                            'name' => 'KB_MOBILE_WHATSAPP_CHAT_NUMBER',
                        ),
                        /* Changes over for whatsapp configuration */
                        /* changes start by rishabh jain on 3rd sep 2018
                         * To add an option to enable disable urlencoding of image links
                         */
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable Url Encoding Of Image Links'),
                            'name' => 'KB_MOBILEAPP_URL_ENCODING',
                            'class' => 't',
                            'desc' => $this->l('This setting will enable/disable Url encoding of all image links.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        /* Changes over */
                        /* changes start by rishabh jain on 25/09/2018
                         * To add an option to enable disable  Fingerprint login functionality
                         */
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable/Disable Fingerprint Login'),
                            'name' => 'KB_MOBILEAPP_FINGERPRINT_LOGIN',
                            'class' => 't',
                            'desc' => $this->l('This setting will enable/disable Fingerprint login in Mobile App.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        /* Changes over */
                        
                        /* changes start by rishabh jain on 19th sep 2018
                         * To add an option to enable disable phone number functionality
                         */
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable/Disable Phone Number Registration'),
                            'name' => 'KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION',
                            'class' => 't',
                            'desc' => $this->l('This setting will enable/disable customer registration and login via phone number verification.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Mandatory Phone number at registration'),
                            'name' => 'KB_MOBILEAPP_PHONE_NUMBER_MANDATORY',
                            'class' => 't',
                            'desc' => $this->l('This setting will set the Phone number field as optional or required.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        /* Changes over */
                        array(
                            'label' => $this->l('Custom CSS'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter custom CSS code'),
                            'class' => '',
                            'name' => 'KB_MOBILEAPP_CSS',
                        )
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_general_setting_btn'
                    ),));
        } else {
            $this->fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('General Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable'),
                            'name' => 'KB_MOBILE_APP',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable entire mobile app functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        //start: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Spin and Win:'),
                            'name' => 'KB_MOBILE_APP_SPIN_WIN',
                            //'class' => 't',
                            'id' => 'KB_MOBILE_APP_SPIN_WIN',
                            'required' => true,
                            'desc' => $this->l('It can be enabled only if module is installed'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Redirect on Cart Page when Add to Cart'),
                            'name' => 'KB_MOBILE_APP_CART_OPTION_REDIRECT',
                            //'class' => 't',
                            'id' => 'KB_MOBILE_APP_CART_OPTION_REDIRECT',
                            'required' => true,
                            'desc' => $this->l('redirect to cart page or keep on the product page when add to cart is clicked'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Display Short Description:'),
                            'name' => 'KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH',
                            // 'class' => 't',
                            'id' => 'KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH',
                            'required' => true,
                            //'desc' => $this->l('redirect to cart page or keep on the product page when add to cart is clicked'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Logo'),
                            'name' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH',
                            //'class' => 't',
                            'id' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH',
                            'required' => true,
                            //'desc' => $this->l('redirect to cart page or keep on the product page when add to cart is clicked'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        // changes for layout select option
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select layout for Home page'), // The <label> for this <select> tag.
                            'required' => true,
                            'id' => 'KBMOBILEAPP_HOME_PAGE_LAYOUT',
                            //'hint' => $this->l('Select shipping methods from the list for which you want to disable this module.You can leave it to empty if you want to enable this module for all shipping methods'),
                            'name' => 'KBMOBILEAPP_HOME_PAGE_LAYOUT', // The content of the 'id' attribute of the <select> tag.
                            'options' => array(
                                'query' => $layout_option,
                                'id' => 'id',
                                'name' => 'name'
                            )
                        ),
                        array(
                        'label' => $this->l('App Button Color'),
                        'type' => 'color',
                        'hint' => $this->l('Select button color of the app.'),
                        'name' => 'KBMOBILEAPP_APP_BUTTON_COLOR',
                        'id' => 'KBMOBILEAPP_APP_BUTTON_COLOR',
                        'size' => 50,
                        'class' => 'kbsw_wheel_color',
                        'required' => true
                        ),
                        array(
                        'label' => $this->l('APP Theme Colour'),
                        'type' => 'color',
                        'hint' => $this->l('Select theme color of the app.'),
                        'name' => 'KBMOBILEAPP_APP_THEME_COLOR',
                        'id' => 'KBMOBILEAPP_APP_THEME_COLOR',
                        'size' => 50,
                        'class' => 'kbsw_wheel_color',
                        'required' => true
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image for logo:'),
                            'class' => '',
                            'name' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION',
                            'id' => 'KB_MOBILE_APP_ADD_LOGO_NAVIGATION',
                            'display_image' => true,
                            'required' => false,
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview_logo.tpl'),
//                            'desc' => $this->l('Upload your Image')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Disabled Shipping Methods'), // The <label> for this <select> tag.
                            'multiple' => true,
                            'class' => 'chosen',
                            'id' => 'KB_MOBILE_APP_DISABLED_SHIPPING',
                            'hint' => $this->l('Select shipping methods from the list for which you want to disable this module.You can leave it to empty if you want to enable this module for all shipping methods'),
                            'name' => 'KB_MOBILE_APP_DISABLED_SHIPPING[]', // The content of the 'id' attribute of the <select> tag.
                            'options' => array(
                                'query' => $option1,
                                'id' => 'id_module',
                                'name' => 'name'
                            )
                        ),
                        //end: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
                        /* start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
                        array(
                            'type' => 'select',
                            'label' => $this->l('Enabled CMS Pages'), // The <label> for this <select> tag.
                            'multiple' => true,
                            'class' => 'chosen',
                            'hint' => $this->l('Select cms pages which you want to display in your mobile app'),
                            'name' => 'KB_MOBILE_APP_ENABLED_CMS[]', // The content of the 'id' attribute of the <select> tag.
                            'options' => array(
                                'query' => $cms_array,
                                'id' => 'id_module',
                                'name' => 'name'
                            )
                        ),
                        /* end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
                        array(
                            'label' => $this->l('Enable Request Log Reporting'),
                            'type' => 'switch',
                            'is_bool' => true, //retro compat 1.5
                            'name' => 'KB_MOBILE_APP_ERROR_REPORTING',
                            'desc' => $this->l('This setting will enable/disable error log reporting for every request to Web Services of the module.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            )
                        ),
                        array(
                            'label' => $this->l('Enable Live Chat Support'),
                            'type' => 'switch',
                            'is_bool' => true, //retro compat 1.5
                            'name' => 'KB_MOBILE_APP_CHAT_SUPPORT',
                            'desc' => $this->l('This setting will enable/disable live chat support option in mobile app.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            )
                        ),
                        array(
                            'label' => $this->l('Chat API Key'),
                            'type' => 'text',
                            'hint' => $this->l('Enter API key'),
                            'class' => '',
                            'name' => 'KB_MOBILE_APP_CHAT_SUPPORT_KEY',
                        ),
                        /* Changes started
                         * @author : Rishabh Jain
                         * Dom : 25/09/2018
                         * To add option for enabling disabling whatsap chat
                         */
                        array(
                            'label' => $this->l('Enable Whatsapp Chat Support'),
                            'type' => 'switch',
                            'is_bool' => true, //retro compat 1.5
                            'name' => 'KB_MOBILE_WHATSAPP_CHAT_SUPPORT',
                            'desc' => $this->l('This setting will enable/disable WHATSAPP chat support option in mobile app.'),
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            )
                        ),
                        array(
                            'label' => $this->l('Chat Number'),
                            'type' => 'text',
                            'hint' => $this->l('Enter Chat Number'),
                            'class' => '',
                            'desc' => $this->l('Enter Country code with Chat number as well'),
                            'name' => 'KB_MOBILE_WHATSAPP_CHAT_NUMBER',
                        ),
                        /* Changes over for whatsapp configuration */
                        /* changes start by rishabh jain on 3rd sep 2018
                         * To add an option to enable disable urlencoding of image links
                         */
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable Url Encoding Of Image Links'),
                            'name' => 'KB_MOBILEAPP_URL_ENCODING',
                            'class' => 't',
                            'desc' => $this->l('This setting will enable/disable Url encoding of all image links.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        /* Changes over */
                        /* changes start by rishabh jain on 25/09/2018
                         * To add an option to enable disable Fingerprint login functionality
                         */
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable/Disable Fingerprint Login'),
                            'name' => 'KB_MOBILEAPP_FINGERPRINT_LOGIN',
                            'class' => 't',
                            'desc' => $this->l('This setting will enable/disable Fingerprint Login in Mobile app.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        /* Changes over */
                        
                        /* changes start by rishabh jain on 19th sep 2018
                         * To add an option to enable disable phone number functionality
                         */
                        
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable/Disable Phone Number Registration'),
                            'name' => 'KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION',
                            'class' => 't',
                            'desc' => $this->l('This setting will enable/disable customer registration and login via phone number verification.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Mandatory Phone number at registration'),
                            'name' => 'KB_MOBILEAPP_PHONE_NUMBER_MANDATORY',
                            'class' => 't',
                            'desc' => $this->l('This setting will set the Phone number field as optional or required.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        /* Changes over */
                        array(
                            'label' => $this->l('Custom CSS'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter custom CSS code'),
                            'class' => '',
                            'name' => 'KB_MOBILEAPP_CSS',
                        )
                    ), 'submit' => array(
                        'title' => $this->l('   Save   '),
                        'class' => 'btn btn-default pull-right kb_general_setting_btn'
                    ),)
            );
        }
        
        $secure_key = Configuration::get('KB_MOBILE_APP_SECURE_KEY');
        $cron_link = $this->context->link->getModuleLink('kbmobileapp', 'AppCheckAbandonedCart', array('secure_key' => $secure_key));
        $this->context->smarty->assign('cron_url', $cron_link);
        
        
        /* Form fields for push notification Setting Form */
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->push_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Push Notification Settings'),
                    ),
                    'input' => array(
                        array(
                            'label' => $this->l('Firebase Server Key'),
                            'type' => 'text',
                            'hint' => $this->l('Enter Server key of firebase '),
                            'class' => '',
                            'name' => 'KB_MOBILEAPP_FIREBASE_KEY',
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/seperation.tpl'),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'push_notification[create_order][status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable create order push notification functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Title for Create Order Notification'),
                            'type' => 'text',
                            'hint' => $this->l('Title for Order Created Push Notification'),
                            'class' => '',
                            'name' => 'push_notification[create_order][title]',
                        ),
                        array(
                            'label' => $this->l('Message for Create Order Notification'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter message for Create Order Notification'),
                            'class' => 'vss-notification-textarea',
                            'name' => 'push_notification[create_order][message]',
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/seperation.tpl'),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'push_notification[order_status_change][status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable Order status change push notification functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Title for Order Status Changed Notification'),
                            'type' => 'text',
                            'hint' => $this->l('Title for Order Status Changed Notification'),
                            'class' => '',
                            'name' => 'push_notification[order_status_change][title]',
                        ),
                        array(
                            'label' => $this->l('Message for Order Status Changed Notification'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter message for Order Status Changed Notification'),
                            'class' => 'vss-notification-textarea',
                            'name' => 'push_notification[order_status_change][message]',
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/seperation.tpl'),
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'push_notification[abandoned_cart][status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable abandoned push notification functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Title for Abandoned Cart Notification'),
                            'type' => 'text',
                            'hint' => $this->l('Title for Abandoned Cart Push Notification'),
                            'class' => '',
                            'name' => 'push_notification[abandoned_cart][title]',
                        ),
                        array(
                            'label' => $this->l('Message for Abandoned Cart Notification'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter message Abandoned Cart Notification'),
                            'class' => 'vss-notification-textarea',
                            'name' => 'push_notification[abandoned_cart][message]',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Time interval to check Abandoned cart'),
                            'name' => 'push_notification[abandoned_cart][interval]',
                            'desc' => $this->l('(In Hours)'),
                            'class' => 'small_text',
                            'hint' => $this->l('Margin between the top of the page and content block'),
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/description.tpl'),
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_push_notification_btn'
                    ),)
            );
        } else {
            $this->push_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Push Notification Settings'),
                    ),
                    'input' => array(
                        array(
                            'label' => $this->l('Firebase Server Key'),
                            'type' => 'text',
                            'hint' => $this->l('Enter Server key of firebase '),
                            'class' => '',
                            'name' => 'KB_MOBILEAPP_FIREBASE_KEY',
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/seperation.tpl'),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable'),
                            'name' => 'push_notification[create_order][status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable create order push notification functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Title for Create Order Notification'),
                            'type' => 'text',
                            'hint' => $this->l('Title for Order Created Push Notification'),
                            'class' => '',
                            'name' => 'push_notification[create_order][title]',
                        ),
                        array(
                            'label' => $this->l('Message for Create Order Notification'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter message for Create Order Notification'),
                            'class' => 'vss-notification-textarea',
                            'name' => 'push_notification[create_order][message]',
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/seperation.tpl'),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable'),
                            'name' => 'push_notification[order_status_change][status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable Order status change push notification functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Title for Order Status Changed Notification'),
                            'type' => 'text',
                            'hint' => $this->l('Title for Order Status Changed Notification'),
                            'class' => '',
                            'name' => 'push_notification[order_status_change][title]',
                        ),
                        array(
                            'label' => $this->l('Message for Order Status Changed Notification'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter message for Order Status Changed Notification'),
                            'class' => 'vss-notification-textarea',
                            'name' => 'push_notification[order_status_change][message]',
                            'desc' => $this->l('While editing please keep in mind that you do not edit or remove {{STATUS}}, you can only move it. {{STATUS}} is used to show order status.')
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/seperation.tpl'),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable'),
                            'name' => 'push_notification[abandoned_cart][status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable abandoned push notification functionality.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Title for Abandoned Cart Notification'),
                            'type' => 'text',
                            'hint' => $this->l('Title for Abandoned Cart Push Notification'),
                            'class' => '',
                            'name' => 'push_notification[abandoned_cart][title]',
                        ),
                        array(
                            'label' => $this->l('Message for Abandoned Cart Notification'),
                            'type' => 'textarea',
                            'hint' => $this->l('Enter message Abandoned Cart Notification'),
                            'class' => 'vss-notification-textarea',
                            'name' => 'push_notification[abandoned_cart][message]',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Time interval to check Abandoned cart'),
                            'name' => 'push_notification[abandoned_cart][interval]',
                            'desc' => $this->l('(In Hours)'),
                            'class' => 'small_text',
                            'hint' => $this->l('Margin between the top of the page and content block'),
                        ),
                        array(
                            'type' => 'html',
                            'label' => '',
                            'name' => '',
                            'html_content' => $this->display(__FILE__, 'views/templates/admin/description.tpl'),
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_push_notification_btn'
                    ),)
            );
        }


        /* form fields for send push notification form */


        $redirect_options = array(
            array(
                'push_notification_redirect_name' => 'home',
                'name' => $this->l('Home'),
            ),
            array(
                'push_notification_redirect_name' => 'category',
                'name' => $this->l('Category'),
            ),
            array(
                'push_notification_redirect_name' => 'product',
                'name' => $this->l('Product'),
            )
        );
        $image_options = array(
            array(
                'push_notification_image_type_id' => '',
                'name' => $this->l('Select an Option'),
            ),
            array(
                'push_notification_image_type_id' => 'url',
                'name' => $this->l('URL'),
            ),
            array(
                'push_notification_image_type_id' => 'image',
                'name' => $this->l('Upload'),
            )
        );

        /* Device options */
        
        $deviceOptions = array(
            array(
                'push_notification_device_type_id' => 'both',
                'name' => $this->l('Both Android/iOS'),
            ),
            array(
                'push_notification_device_type_id' => 'android',
                'name' => $this->l('Android'),
            ),
            array(
                'push_notification_device_type_id' => 'ios',
                'name' => $this->l('iOS'),
            )
        );
        

        $options_categories = $this->createCategoryTree();
        
        
        $demo_image = $this->module_url.'libraries/sample/404.gif';
        $this->context->smarty->assign('demo_image', $demo_image);
        $this->fields_form1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Send Push Notification'),
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'kbmobileappconfigurationsettings',
                    ),
                    array(
                        'label' => $this->l('Title'),
                        'type' => 'text',
                        'hint' => $this->l('Title for Push Notification'),
                        'class' => '',
                        'name' => 'push_notification_title',
                    ),
                    array(
                        'label' => $this->l('Message'),
                        'type' => 'textarea',
                        'hint' => $this->l('Message for Push Notification'),
                        'class' => 'vss-textarea',
                        'name' => 'push_notification_message',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Brodcast Device Type'),
                        'name' => 'push_notification_device_type',
                        'class' => 'chosen-dropdown',
                        'hint' => $this->l('Select the device type for notification Android/IOS'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $deviceOptions,
                            'id' => 'push_notification_device_type_id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Image Type'),
                        'name' => 'push_notification_image_type',
                        'class' => 'chosen-dropdown',
                        'hint' => $this->l('Select the Image type for notification Upload/URL'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $image_options,
                            'id' => 'push_notification_image_type_id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'file',
                        'label' => $this->l('Notification Image:'),
                        'class' => '',
                        'name' => 'uploadedfile',
                        'id' => 'uploadedfile',
                        'display_image' => true,
                        'image' => $this->display(__FILE__, 'views/templates/admin/notification_image_preview.tpl'),
                        'required' => false,
//                        'desc' => $this->l('Upload your Image')
                    ),
                    array(
                        'label' => $this->l('Image URL'),
                        'type' => 'text',
                        'hint' => $this->l('Image URL for Push Notification'),
                        'class' => '',
                        'name' => 'push_notification_image_url',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Redirect Activity'),
                        'name' => 'push_notification_redirect_type',
                        'class' => 'chosen-dropdown',
                        'hint' => $this->l('Select the Activity where you have to redirect the customer after click on notification.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $redirect_options,
                            'id' => 'push_notification_redirect_name',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select the Category'),
                        'name' => 'push_notification_redirect_category_id',
                        'hint' => $this->l('Select the category type'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $options_categories,
                            'id' => 'id_category_type',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Enter the products name'),
                        'type' => 'text',
                        'hint' => $this->l('Start typing the products name'),
                        'class' => 'ac_input',
                        'name' => 'push_notification_redirect_product_name',
                        'autocomplete' => false,
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'push_notification_redirect_product_id',
                    ),
                    array(
                        'type' => 'html',
                        'label' => '',
                        'name' => '',
                        'html_content' => $this->display(__FILE__, 'views/templates/admin/push_button.tpl'),
                    )
                ),)
        );


        /* form fields for Payment Methods */
        $form_add_new = $this->getPaymentForm(false);
        
        /* form fields for Slidder */
        //$sliderform = $this->getSliderForm(false);
        
        /* form fields for Google Setup */
        $googleform = $this->getGoogleSetupForm();
        /* form fields for Facebook Setup */
        $facebookform = $this->getFacebookSetupForm();

        $languages = Language::getLanguages();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }

        $helper = new HelperView();
        $helper->module = $this;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->current = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'view/';
        $helper->base_tpl = 'button.tpl';

        $view = $helper->generateView();
        
        $helper->base_tpl = 'send_notification_button.tpl';
        
        $notificationview = $helper->generateView();
        
        $helper->base_tpl = 'add_new_layout_button.tpl';

        $layoutbutton = $helper->generateView();

        $helper->base_tpl = 'cancel_button.tpl';
        
        $cancelview = $helper->generateView();
        
        $helper->base_tpl = 'validate_button.tpl';
        
        $validatebuttonview = $helper->generateView();


        $custom_css = '';
        if (Configuration::get('KB_MOBILEAPP_CSS') && Configuration::get('KB_MOBILEAPP_CSS') != '') {
            $custom_css = Tools::unserialize(Configuration::get('KB_MOBILEAPP_CSS'));
            $custom_css = urldecode($custom_css);
        }
        
        $push_notification_data = $this->getDafaultNotificationsData();
        $field_value = array(
            'KB_MOBILE_APP' => Configuration::get('KB_MOBILE_APP'),
            'KB_MOBILE_APP_ERROR_REPORTING' => Configuration::get('KB_MOBILE_APP_ERROR_REPORTING'),
            'KB_MOBILE_APP_CHAT_SUPPORT' => Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT'),
            'KB_MOBILE_APP_CHAT_SUPPORT_KEY' => Configuration::get('KB_MOBILE_APP_CHAT_SUPPORT_KEY'),
            'KB_MOBILE_APP_MARKETPLACE' => (Configuration::get('KB_MOBILE_APP_MARKETPLACE') ?
                    Configuration::get('KB_MOBILE_APP_MARKETPLACE') : 0),
            /* start:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
            'KB_MOBILE_APP_SPIN_WIN' => Configuration::get('KB_MOBILE_APP_SPIN_WIN'),
            'KB_MOBILE_APP_CART_OPTION_REDIRECT' => Configuration::get('KB_MOBILE_APP_CART_OPTION_REDIRECT'),
            'KB_MOBILE_APP_ADD_LOGO_NAVIGATION' => Configuration::get('KB_MOBILE_APP_ADD_LOGO_NAVIGATION'),
            'KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH' => Configuration::get('KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH'),
            // changes by rishabh jain
            'KBMOBILEAPP_HOME_PAGE_LAYOUT' => Configuration::get('KBMOBILEAPP_HOME_PAGE_LAYOUT'),
            'KBMOBILEAPP_APP_BUTTON_COLOR' => Configuration::get('KBMOBILEAPP_APP_BUTTON_COLOR'),
            'KBMOBILEAPP_APP_THEME_COLOR' => Configuration::get('KBMOBILEAPP_APP_THEME_COLOR'),
            'KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH' => Configuration::get('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH'),
            'KB_MOBILE_APP_ENABLED_CMS[]' => Tools::unSerialize(Configuration::get('KB_ENABLED_CMS')),
            /* end:changes made by Aayushi Agarwal on 1 DEC 2018 to allow functionality to enable cms pages */
            //start: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
            'KB_MOBILE_APP_DISABLED_SHIPPING[]' => Tools::unSerialize(Configuration::get('KB_DISABLED_SHIPPING')),
            //end: added by aayushi on 1 Nov 2018 to not to allow transport methods which are disabled in module
            /* changes started by rishabh jain on 3rd sept 2018
             * Added field value of url encoding by default it will be 1
             */
            'KB_MOBILEAPP_URL_ENCODING' => (Configuration::get('KB_MOBILEAPP_URL_ENCODING')),
            /* Changes over */
            /* changes started
             * @author -Rishabh jain
             * DOM: 25/09/2018
             * Added field value of enabling/disabling whatsapp Chat support
             */
            'KB_MOBILE_WHATSAPP_CHAT_SUPPORT' => Configuration::get('KB_MOBILE_WHATSAPP_CHAT_SUPPORT'),
            'KB_MOBILE_WHATSAPP_CHAT_NUMBER' => Configuration::get('KB_MOBILE_WHATSAPP_CHAT_NUMBER'),
            /* Changes over */
            /* changes started
             * @author -Rishabh jain
             * DOM: 19th sep 2018
             * Added field value of enabling/disabling Phone number field
             */
            'KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION' => (Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION')),
            'KB_MOBILEAPP_PHONE_NUMBER_MANDATORY' => (Configuration::get('KB_MOBILEAPP_PHONE_NUMBER_MANDATORY')),
            /* Changes over */
            /* changes started
             * @author -Rishabh jain
             * DOM: 25/09/2018
             * Added field value of enabling/disabling Phone number field
             */
            'KB_MOBILEAPP_FINGERPRINT_LOGIN' => (Configuration::get('KB_MOBILEAPP_FINGERPRINT_LOGIN')),
            /* Changes over */
            'KB_MOBILEAPP_CSS' => $custom_css,
            'push_notification_message' => '',
            'push_notification_title' => '',
            'push_notification_image_url' => '',
            'push_notification_image_type' => '',
            'push_notification_device_type' => 'both',
            'push_notification_redirect_type' => 'home',
            'push_notification_redirect_category_id' => 0,
            'push_notification_redirect_product_id' => '0',
            'push_notification_redirect_product_name' => '',
            'kbmobileappconfigurationsettings' => 1,
            'KB_MOBILEAPP_FIREBASE_KEY' => (Configuration::get('KB_MOBILEAPP_FIREBASE_KEY') ?
                    Configuration::get('KB_MOBILEAPP_FIREBASE_KEY') : ''),
            'push_notification[create_order][status]' => $push_notification_data['create_order']['status'],
            'push_notification[create_order][title]' => $push_notification_data['create_order']['title'],
            'push_notification[create_order][message]' => $push_notification_data['create_order']['message'],
            'push_notification[order_status_change][status]' => $push_notification_data['order_status_change']['status'],
            'push_notification[order_status_change][title]' => $push_notification_data['order_status_change']['title'],
            'push_notification[order_status_change][message]' => $push_notification_data['order_status_change']['message'],
            'push_notification[abandoned_cart][status]' => $push_notification_data['abandoned_cart']['status'],
            'push_notification[abandoned_cart][title]' => $push_notification_data['abandoned_cart']['title'],
            'push_notification[abandoned_cart][message]' => $push_notification_data['abandoned_cart']['message'],
            'push_notification[abandoned_cart][interval]' => $push_notification_data['abandoned_cart']['interval'],
        );
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->smarty->assign('show_toolbar', false);
        }
        $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules');

        $form = $this->getform($this->fields_form, $languages, $field_value, 'general', $action);
        $form1 = $this->getform($this->fields_form1, $languages, $field_value, 'push', $action);
        $form2 = $this->getform($this->push_fields_form, $languages, $field_value, 'push_notification_settings', $action);

        $this->context->controller->addJs($this->_path . 'views/js/admin/kbmobileapp.js');
        // changes by rishabh jain
        $this->context->controller->addJs($this->_path . 'views/js/admin/layout.js');
        $this->context->controller->addJs($this->_path . 'views/js/admin/jquery.ui.sortable.js');
        $this->context->controller->addJs($this->_path . 'views/js/admin/jquery.autocomplete.js');
        $this->context->controller->addJs($this->_path . 'views/js/velovalidation.js');
        $this->context->controller->addCSS($this->_path . 'views/css/admin/kb_mobile_app_admin.css');
        $this->context->controller->addJs($this->_path . 'views/js/admin/CustomScrollbar.min.js');
        $this->context->controller->addCSS($this->_path . 'views/css/admin/CustomScrollbar.css');
        // changes by rishabh jain
        $this->context->controller->addCSS($this->_path . 'views/css/admin/layout.css');

        if (!version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/pageviewer_16.css');
            $version = 1.6;
        } else {
            $this->context->controller->addCSS($this->_path . 'views/css/admin/pageviewer_15.css');
            $version = 1.5;
        }


        $list = $this->getPaymentList(false);
        $notificationlist = $this->getNotificationList(false);
//        $sliderlist = $this->getSliderList('slider', false);
//        $bannerslist = $this->getSliderList('banner', false);
        /* changes started by
        * @author- Rishabh jain
        * DOm : 19th Dec 18
        * to add the layout tab
        */
        $layoutlist = $this->getLayoutList(false);
        /* chnages over */
        $tabs_data = array();
        foreach ($this->available_tabs as $tab) {
            $tabs_data[$tab] = array(
                'id' => $tab,
                'selected' => (Tools::strtolower($tab) == Tools::strtolower($this->tab_display) || (isset($this->tab_display_module) && 'module' . $this->tab_display_module == Tools::strtolower($tab))),
                'name' => $this->available_tabs_lang[$tab],
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
            );
        }
        
        /* changes started by
        * @author- Rishabh jain
        * DOm : 19th Dec 18
        * to add the layout tab
        */
        // changes by rishabh jain
        $languages = Language::getLanguages();
        $enabled_languages = array();
        foreach ($languages as $key => $lang) {
            $enabled_languages[] = $lang['id_lang'];
        }
        $this->context->smarty->assign('active_languages', Tools::jsonEncode($enabled_languages));

        $this->context->smarty->assign('num_of_lang', count($languages));
        // changes over
        $this->context->smarty->assign('layout_list', $layoutlist);
        /* chnages over */
        $this->context->smarty->assign('table', $list);
        $this->context->smarty->assign('default_tab', $this->tab_display);
        $this->context->smarty->assign('notification_list', $notificationlist);
        $this->context->smarty->assign('available_tabs', $tabs_data);
        $this->context->smarty->assign('default_language_id', $this->context->language->id);
        $this->context->smarty->assign('default_language_code', $this->context->language->iso_code);
        $this->context->smarty->assign('form', $form);
        $this->context->smarty->assign('form1', $form1);
        $this->context->smarty->assign('form2', $form2);
        $this->context->smarty->assign('form_add_new', $form_add_new);
        $this->context->smarty->assign('firstCall', false);
        $this->context->smarty->assign('general_settings', $this->l('General Settings'));
        $this->context->smarty->assign('mod_dir', _MODULE_DIR_);
        $this->context->smarty->assign('version', $version);
        $this->context->smarty->assign('view', $view);
        $this->context->smarty->assign('notification_button', $notificationview);
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }
        // changes by rishabh jain
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $this->context->smarty->assign('loader', $module_dir . 'kbmobileapp/views/img/Layout_Components');
        $this->context->smarty->assign('add_new_layout_button', $layoutbutton);
        // changes over
        $this->context->smarty->assign('cancel_button', $cancelview);
        $this->context->smarty->assign('action', $action);
        $this->context->smarty->assign('action_page', $action . '&configure=kbmobileapp');
        $this->context->smarty->assign('push_notification_history', $this->l('Push Notification History'));
        $this->context->smarty->assign('payments_settings', $this->l('Payment Methods'));
        $this->context->smarty->assign('slidders_settings', $this->l('Sliders Settings'));
        $this->context->smarty->assign('banners_settings', $this->l('Banners Settings'));
        $this->context->smarty->assign('push_notification_settings', $this->l('Push Notification Settings'));
        $this->context->smarty->assign('google_form', $googleform);
        $this->context->smarty->assign('facebook_form', $facebookform);
        $this->context->smarty->assign('validatebuttonview', $validatebuttonview);
        




        $tpl = 'Form_custom.tpl';
        $helper = new Helper();
        $helper->module = $this;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $helper->override_folder = 'helpers/';
        $helper->base_folder = 'form/';
        $helper->setTpl($tpl);
        $tpl = $helper->generate();


        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }

        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $this->context->smarty->assign('log_empty_link', $module_dir . 'kbmobileapp/libraries/emptyRequestLogFile.php');
        $this->context->smarty->assign('log_file_link', $module_dir . 'kbmobileapp/libraries/mobile_app_log.txt?time=' . time());

        $log_links = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'kbmobileapp/views/templates/admin/link_empty_log.tpl');
        // changes by rishabh jain
        $this->context->smarty->assign('banner_countdown', $module_dir . 'kbmobileapp/views/img/Layout_Components/banner_countdown_timer.jpg');
        $this->context->smarty->assign('loader', $module_dir . 'kbmobileapp/views/img/Layout_Components');
        $this->context->smarty->assign('banner_horizontal_sliding', $module_dir . 'kbmobileapp/views/img/Layout_Components/banner_horizontal_sliding.jpg');
        $this->context->smarty->assign('banner_grid', $module_dir . 'kbmobileapp/views/img/Layout_Components/banner_grid.jpg');
        $this->context->smarty->assign('banner_square', $module_dir . 'kbmobileapp/views/img/Layout_Components/banner_square.jpg');
        $this->context->smarty->assign('product_recent_access', $module_dir . 'kbmobileapp/views/img/Layout_Components/product_recent_access.jpg');
        $this->context->smarty->assign('product_square', $module_dir . 'kbmobileapp/views/img/Layout_Components/product_square.jpg');
        $this->context->smarty->assign('product_grid', $module_dir . 'kbmobileapp/views/img/Layout_Components/product_grid.jpg');
        $this->context->smarty->assign('product_horizontal_sliding', $module_dir . 'kbmobileapp/views/img/Layout_Components/product_horizontal_sliding.jpg');
        $this->context->smarty->assign('top_category', $module_dir . 'kbmobileapp/views/img/Layout_Components/top_category.jpg');
        $layouts = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'kbmobileapp/views/templates/admin/layout.tpl');
        // changes over
        $log_file_path = _PS_MODULE_DIR_ . 'kbmobileapp/libraries/mobile_app_log.txt';
        if (Tools::file_exists_no_cache($log_file_path)) {
            $log_file_content = Tools::file_get_contents($log_file_path);
            if ($log_file_content) {
                $output = $output . $tpl . $log_links;
            } else {
                $output = $output . $tpl;
            }
        } else {
            $output = $output . $tpl;
        }
        return $layouts.$output;
    }

    /*
     * Hook function used to display override template of
     * contact us page and order view page in mobile web view
     * 
     * @param array $params having information of frontend e.g controller name
     * @return string path of override tpl file
     */
    public function hookDisplayOverrideTemplate($params)
    {
        $controllerName = get_class($params['controller']);
        if (($controllerName == 'cms' || $controllerName == 'CmsController')
            && Tools::getValue('request_type') == 'kb_mobile_app'
        ) {
            $this->context->smarty->assign('content_only', true);
        }

        if ($controllerName == 'ContactController') {
            if (Tools::getValue('request_type') == 'kb_contact_us') {
                $this->context->cookie->kbmobileappcontactus = "1";
            }

            if (isset($this->context->cookie->kbmobileappcontactus)
                && $this->context->cookie->kbmobileappcontactus
            ) {
                $this->context->smarty->assign('content_only', true);
                return _PS_MODULE_DIR_ . 'kbmobileapp/views/templates/hook/contactform.tpl';
            }
        }
       
        if (($controllerName == 'OrderOpcController' || $controllerName == 'OrderController')
            && isset($this->context->cookie->kbmobileapp)
            && $this->context->cookie->kbmobileapp
        ) {
            return _PS_MODULE_DIR_ . 'kbmobileapp/views/templates/hook/checkout.tpl';
        }
    }

    /*
     * Hook function excute on every page
     * used to render our custom css code and to redirect
     * if customer is on different page in web view other than override templates
     * 
     * @return string code to render in header of page
     */
    public function hookDisplayHeader()
    {
        $controller = Tools::getValue('controller', '');
        if (Tools::getValue('request_type') == 'kb_contact_us') {
            $email = trim(Tools::getValue('email'));
            if (!empty($email)) {
                if (Validate::isEmail($email)) {
                    $customer = new Customer();
                    $customer->getByemail($email);
                    if (Validate::isLoadedObject($customer) && $customer->active) {
                        $this->context->cookie->id_customer = (int) ($customer->id);
                        $this->context->cookie->customer_lastname = $customer->lastname;
                        $this->context->cookie->customer_firstname = $customer->firstname;
                        $this->context->cookie->logged = 1;
                        $customer->logged = 1;
                        $this->context->cookie->is_guest = $customer->isGuest();
                        $this->context->cookie->passwd = $customer->passwd;
                        $this->context->cookie->email = $customer->email;

                        /* Add customer to the context */
                        $this->context->customer = $customer;
                        $this->context->cookie->write();
                    } else {
                        $this->context->customer->logout();
                    }
                }
            } else {
                $this->context->customer->logout();
            }
            $this->context->cookie->kbmobileappcontactus = "1";
            $contact_url = (_PS_VERSION_ >= 1.5) ? 'contact' : 'contact-form';
            /* Changes started by rishabh jain on 3rd sep 2018
             * To show only content on contact us page
             */
            Tools::redirect($this->context->link->getPageLink($contact_url).'?content_only=1');
        }
        if (isset($this->context->cookie->kbmobileapp)
                && $this->context->cookie->kbmobileapp
            || $controller == 'AppPayment'
        ) {
            $this->context->smarty->assign('opc', 1);
            $this->context->smarty->assign('content_only', true);
        }
        if (isset($this->context->cookie->kbmobileapp)
            && $this->context->cookie->kbmobileapp
            && $controller == 'history'
        ) {
            Tools::redirect('index.php?module=kbmobileapp&controller=kbmobileappredirect');
        }
        
        
        if (isset($this->context->cookie->kbmobileapp)
            && $this->context->cookie->kbmobileapp
            && ($controller == 'index' || $controller == 'cart')
        ) {
            Tools::redirect('index.php?module=kbmobileapp&controller=kbmobileappredirecterror');
        }

        if (isset($this->context->cookie->kbmobileapp) && $this->context->cookie->kbmobileapp) {
            if (Configuration::get('KB_MOBILEAPP_CSS') && Configuration::get('KB_MOBILEAPP_CSS') != '') {
                $custom_css = Tools::unserialize(Configuration::get('KB_MOBILEAPP_CSS'));
                $custom_css = urldecode($custom_css);
                $this->context->smarty->assign('custom_css', $custom_css);
                return $this->display(__FILE__, 'views/templates/front/custom_css.tpl');
            }
        }
    }

    /*
     * Hook function execute after placing and order
     * Redirect customer to app from web view after placing an order
     * 
     * @param array $params having order information
     */
    public function hookDisplayOrderConfirmation($params)
    {
        if (isset($this->context->cookie->kbmobileapp) && $this->context->cookie->kbmobileapp) {
            Tools::redirect('index.php?module=kbmobileapp&controller=kbmobileappredirect');
        }
    }

    /*
     * Hook function excute on admin end
     * used to include our js and css files in header for product form
     * 
     * @param array $params
     */
    public function hookDisplayBackOfficeHeader($params)
    {
        unset($params);
        $enable_data = Configuration::get('KB_MOBILE_APP');
        if (isset($enable_data) && $enable_data == 1) {
            $current_url = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            if (strpos($current_url, 'AdminProducts') !== false) {
                /*To add validation files in case the controller is product controller */
                $this->context->controller->addJs(_PS_MODULE_DIR_ . 'kbmobileapp/views/js/velovalidation.js');
                $this->context->controller->addJs(_PS_MODULE_DIR_ . 'kbmobileapp/views/js/validate_form.js');
                $this->context->controller->addCSS(_PS_MODULE_DIR_ . 'kbmobileapp/views/css/kbmobileapp_validation.css');
            }
        }
    }

    /*
     * Hook function to show extra information on product form
     * 
     * @param array @params
     * @return string html of content to be shown
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        unset($params);
        $enable_data = Configuration::get('KB_MOBILE_APP');
        if (isset($enable_data) && $enable_data == 1) {
            $id_product = Tools::getValue('id_product');
            $get_ytdata_qry = 'select * from ' . _DB_PREFIX_ . 'kb_product_youtube_mapping
                where id_product=' . (int) $id_product;
            $yt_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_ytdata_qry);
            if ($yt_data) {
                $this->context->smarty->assign('velsof_yt_data', $yt_data);
            }
            $this->context->smarty->assign('velsof_product_back_url', $this->context->link->getAdminLink('AdminProducts'));
            return $this->display(__FILE__, 'views/templates/admin/admin_youtube_video_link.tpl');
        } else {
            $error_msg = $this->l('Please enable Knowband Mobile App Creator module first.');
            return Tools::displayError($error_msg);
        }
    }

    /*
     * Hook function execute on product save action
     * save and update our product video mapping table
     * 
     * @param array @params
     */
    public function hookActionProductSave($params)
    {
        unset($params);
        $enable_data = Configuration::get('KB_MOBILE_APP');
        if (isset($enable_data) && $enable_data == 1) {
            $id_product = Tools::getValue('id_product', 0);
            $yt_url = Tools::getValue('product_youtube_url');
            if (Validate::isUrlOrEmpty($yt_url)) {
                $get_ytdata_qry = 'select * from ' . _DB_PREFIX_ . 'kb_product_youtube_mapping
                    where id_product=' . (int) $id_product;
                $yt_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_ytdata_qry);
                if ($yt_data) {
                    $update_url = 'update ' . _DB_PREFIX_ . 'kb_product_youtube_mapping set 
                        youtube_url="' . pSQL($yt_url) . '", date_update = now() where id_product=' . (int) $id_product;
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($update_url);
                } else {
                    $map_url_product = 'insert into ' . _DB_PREFIX_ . 'kb_product_youtube_mapping
                    (`id_product`,`youtube_url`,`date_add`,`date_update`)
                    values(' . (int) $id_product . ',"' . pSQL($yt_url) . '",now(),now())';
                    Db::getInstance(_PS_USE_SQL_SLAVE_)->execute($map_url_product);
                }
            } else {
                $error_msg = $this->l('YouTube URL not valid');
                $this->context->controller->errors[] = Tools::displayError($error_msg);
            }
        }
    }

    /*
     * Create instance of helper form and set values
     * 
     * @param array $field_form array of form fileds
     * @param array $languages array of languages on store
     * @param array $field_value array of values of form fields
     * @param string $id id of form
     * @param string $action action url of form
     * 
     * @return string 
     */
    public function getform($field_form, $languages, $field_value, $id, $action)
    {
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $field_value;
        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG')
                : 0;
        $helper->default_form_language = $this->context->language->id;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->title = $this->displayName;
        if ($id == 'general') {
            $helper->show_toolbar = true;
        } else {
            $helper->show_toolbar = false;
        }
        $helper->table = $id;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = $action;
        return $helper->generateForm(array('form' => $field_form));
    }

    /*
     * Function to get list of payment methods for inbuild payment in APP
     * 
     * @return array payment methods
     */
    public function getPaymentMethods()
    {
        $methods = array(
            'paypal' => $this->l('PayPal'),
            'cod' => $this->l('Cash On Delivery'),
        );
        return $methods;
    }

    /*
     * Function to fetch the content of edit link in helper list
     * 
     * @return string html of edit link
     */
    public function displayEditLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_edit_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_edit_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }
    
    /*
     * Function to fetch the content of view link in helper list
     * 
     * @return string html of view link
     */
    public function displayViewLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_view_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_view_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }

    /*
     * Function to fetch the content of delete link in helper list
     * 
     * @return string html of delete link
     */
    public function displayDeleteLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_delete_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_delete_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }
    public function displayDeleteBannerLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_delete_banner_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_delete_banner_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }
    /*
     * Function to fetch the content of delete link in helper list
     * 
     * @return string html of deletelayout  link
     */

    public function displayDeletelayoutLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_deletelayout_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_deletelayout_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }

    /*
     * Function to fetch the content of enable/disable link in helper list
     * 
     * @return string html of enable/disable button
     */
    public function displayEnableLink($token, $id, $value)
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_status_old',
                'enabled' => (bool)$value,
                'enable_identifier' => $id,
                'enabled_token' => $token,
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_status_new',
                'enabled' => (bool)$value,
                'enable_identifier' => $id,
                'enabled_token' => $token,
            ));
        }
        
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }
    
    /*
     * Function to fetch the content of enable link in payment methods helper list
     * 
     * @return string html of enable link
     */
    public function displayEnablepaymentLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_enable_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_enable_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }
    
    /*
     * Function to fetch the content of edit link in payment methods helper list
     * 
     * @return string html of edit link
     */
    public function displayEditsliderLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_editslider_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_editslider_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }

    /* changes by rishabh jain
     * to display edit layout link
     */
    public function displayEditlayoutLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_editlayout_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_editlayout_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }
    public function displayeditLayoutNameLink()
    {
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_edit_name_layout_old'
            ));
        } else {
            $this->smarty->assign(array(
                'vss_cjc_link' => 'vss_edit_name_layout_new'
            ));
        }
        return $this->display(__FILE__, 'views/templates/admin/button.tpl');
    }

    /*
     * Function to set payment methods data
     * 
     * @return bool
     */
    public function setPaymentMethods()
    {

        if (!Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_code = Tools::getValue('payment_method', '');
            $payment_client_id = Tools::getValue('payment_method_client_id', '');
            $payment_other_info = Tools::getValue('payment_method_other_info', '');
            $payment_mode = Tools::getValue('payment_method_mode', 'live');
            $payment_method_name = array();
            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                if (Tools::getIsset('payment_method_name_' . $language['id_lang'])
                        && Tools::getValue('payment_method_name_' . $language['id_lang']) != ''
                    ) {
                    $payment_method_name[$language['iso_code']] = Tools::getValue(
                        'payment_method_name_' . $language['id_lang']
                    );
                } else {
                    $payment_method_name[$language['iso_code']] = Tools::getValue(
                        'payment_method_name_' . $this->context->language->id,
                        ''
                    );
                }
            }
            $data = array(
                array(
                    'payment_code' => $payment_code,
                    'payment_name' => $payment_method_name,
                    'client_id' => $payment_client_id,
                    'other_info' => $payment_other_info,
                    'status' => 1,
                    'payment_mode' => $payment_mode
                )
            );
            Configuration::updateValue('KB_MOBILEAPP_PAYMENT_METHODS', serialize($data));
            return true;
        } else {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));

            $payment_code = Tools::getValue('payment_method', '');
            $payment_client_id = Tools::getValue('payment_method_client_id', '');
            $payment_other_info = Tools::getValue('payment_method_other_info', '');
            $payment_mode = Tools::getValue('payment_method_mode', 'live');
            $payment_method_name = array();

            $languages = Language::getLanguages();
            foreach ($languages as $k => $language) {
                if (Tools::getIsset('payment_method_name_' . $language['id_lang'])
                        && Tools::getValue('payment_method_name_' . $language['id_lang']) != ''
                    ) {
                    $payment_method_name[$language['iso_code']] = Tools::getValue(
                        'payment_method_name_' . $language['id_lang']
                    );
                } else {
                    $payment_method_name[$language['iso_code']] = Tools::getValue(
                        'payment_method_name_' . $this->context->language->id,
                        ''
                    );
                }
            }
            $data = array(
                        'payment_code' => $payment_code,
                        'payment_name' => $payment_method_name,
                        'client_id' => $payment_client_id,
                        'other_info' => $payment_other_info,
                        'status' => 1,
                        'payment_mode' => $payment_mode
            );

            if ($this->checkMethods($payment_code)) {
                $key = $this->getMethodKey($payment_code);
                $payment_data[$key] = $data;
            } else {
                $payment_data[] = $data;
            }


            Configuration::updateValue('KB_MOBILEAPP_PAYMENT_METHODS', serialize($payment_data));
            return true;
        }
    }

    /*
     * Function to check payment method exist in our configuration or not via payment code
     * 
     * @param string $code code of payment method
     * @return bool
     */
    public function checkMethods($code)
    {
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            foreach ($payment_data as $data) {
                if ($data['payment_code'] == $code) {
                    return true;
                }
            }
        }
        return false;
    }

    /*
     * Get key of patyment method from payment methoid configuration array
     * 
     * @param string $code code of payment method
     * @return string $key_code key value of array
     */
    public function getMethodKey($code)
    {
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            foreach ($payment_data as $key_code => $data) {
                if ($data['payment_code'] == $code) {
                    return $key_code;
                }
            }
        }
        return false;
    }

    /*
     * Function to change the payment method status
     * 
     * @param string $code code of payment method
     */
    public function changePaymentMethodStatus($code)
    {
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            $payment_method_update = array();
            foreach ($payment_data as $key_code => $data) {
                if ($data['payment_code'] == $code) {
                    if ($data['status'] == 1) {
                        $data['status'] = 0;
                    } else {
                        $data['status'] = 1;
                    }
                }
                $payment_method_update[$key_code] = $data;
            }
            Configuration::updateValue('KB_MOBILEAPP_PAYMENT_METHODS', serialize($payment_method_update));
        }
    }

    /*
     * Delete payment method from configuration array via payment code
     * 
     * @param string $code unique code of payment method
     */
    public function deletePaymentMethod($code)
    {
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            $payment_method_update = array();
            $index = 0;
            foreach ($payment_data as $key_code => $data) {
                if ($data['payment_code'] != $code) {
                    $payment_method_update[$index] = $data;
                    $index++;
                }
            }
            Configuration::updateValue('KB_MOBILEAPP_PAYMENT_METHODS', serialize($payment_method_update));
        }
    }

    public function deleteLayout($code)
    {
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layouts where id_layout = ' . (int)$code;
        $row = Db::getInstance()->execute($sql);
    }

    /*
     * Get html of payment method list
     * 
     * @param bool $ajax true if getting list in ajax request
     * @return string/array return array for ajax request else string 
     */
    public function getPaymentList($ajax)
    {
        $msg = array();
        $this->fields_list = array();
        $this->table_values = array();

        $index = 0;

        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            foreach ($payment_data as $data) {
                $this->table_values[$index]['s_no'] = $index + 1;
                $this->table_values[$index]['code'] = ($data['payment_code'] != '') ? $data['payment_code'] : '--';
                $this->table_values[$index]['payment_method_name'] = ($data['payment_name'][$this->context->language->iso_code] != '') ? $data['payment_name'][$this->context->language->iso_code] : '--';
                if ($data['status'] == 1) {
                    $this->table_values[$index]['payment_method_status'] = $this->l('Enable');
                } else {
                    $this->table_values[$index]['payment_method_status'] = $this->l('Disable');
                }

                $index++;
            }
        }

        $this->fields_list['s_no'] = array(
            'title' => $this->l('S.No.'),
            'align' => 'center',
            'class' => '',
            'type' => 'int',
        );
        $this->fields_list['code'] = array(
            'title' => $this->l('code'),
            'align' => 'center td-vss-code',
            'class' => '',
            'type' => 'int'
        );
        $this->fields_list['payment_method_name'] = array(
            'title' => $this->l('Payment Method Name'),
            'align' => 'center',
            'class' => '',
        );
        $this->fields_list['payment_method_status'] = array(
            'title' => $this->l('Status'),
            'align' => 'center',
            'class' => '',
        );

        $helper = new HelperList();
        $helper->module = $this;
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->controller = $this;
        }
        $helper->imageType = 'jpg';
        $helper->identifier = 'payment_methods_name';
        $helper->table_id = 'kbmobileapp_payment_methods';
        $helper->no_link = true;
        $helper->simple_header = true;
        $helper->listTotal = $index;
        $helper->show_toolbar = false;
        $helper->colorOnBackground = true;
        $helper->shopLinkType = false;
        $helper->title = $this->l('Payment Methods');
        $helper->actions = array('Edit', 'Delete', 'Enablepayment');
        $table = $helper->generateList($this->table_values, $this->fields_list);

        if ($ajax) {
            $msg['msg'] = $this->displayConfirmation($this->l('Data has been saved successfully.'));
            $msg['html'] = $table;
            echo Tools::jsonEncode($msg);
            die;
        } else {
            return $table;
        }
    }

    /*
     * Function to get notifications helper list
     * 
     * @param bool $ajax true if getting list in ajax request
     * @return string
     */
    public function getNotificationList($ajax)
    {
        $this->notification_fields_list = array();
        $this->table_values = array();

        
        $redirect_options_list = array(
            'home' => $this->l('Home'),
            'category' => $this->l('Category'),
            'product' => $this->l('Product'),
        );
        $image_options_list = array(
            'url' => $this->l('URL'),
            'image' => $this->l('Upload'),
        );
        
        $this->notification_fields_list['kb_notification_id'] = array(
            'title' => $this->l('Notification ID'),
            'align' => 'center',
            'class' => 'td-vss-code',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        
        $this->notification_fields_list['title'] = array(
            'title' => $this->l('Title'),
            'align' => 'center',
            'class' => '',
            'filter_key' => 'title',
            'order_key' => 'title',
            'orderby' => false,
        );
        $this->notification_fields_list['image_type'] = array(
            'title' => $this->l('Image Type'),
            'align' => 'center',
            'class' => '',
            'type' => 'select',
            'list' => $image_options_list,
            'filter_key' => 'image_type',
            'order_key' => 'image_type',
            'orderby' => false,
        );
        
        $this->notification_fields_list['redirect_activity'] = array(
            'title' => $this->l('Redirect Activity'),
            'align' => 'center',
            'class' => '',
            'type' => 'select',
            'list' => $redirect_options_list,
            'filter_key' => 'redirect_activity',
            'order_key' => 'redirect_activity',
            'orderby' => false,
        );
        $this->notification_fields_list['category_name'] = array(
            'title' => $this->l('Category'),
            'align' => 'center',
            'class' => '',
            'filter_key' => 'category_name',
            'order_key' => 'category_name',
            'orderby' => false,
        );
        $this->notification_fields_list['product_name'] = array(
            'title' => $this->l('Product'),
            'align' => 'center',
            'class' => '',
            'filter_key' => 'product_name',
            'order_key' => 'product_name',
            'orderby' => false,
        );
        $this->notification_fields_list['date_add'] = array(
            'title' => $this->l('Sent Date'),
            'align' => 'center',
            'class' => '',
            'type' => 'date',
            'filter_key' => 'date_add',
            'order_key' => 'date_add',
            'orderby' => false,
        );

        $notificationhelper = new HelperList();
        $notificationhelper->module = $this;
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->controller = $this;
        }
        $list_id = 'kb_push_notifications_history';
        $notificationhelper->imageType = 'jpg';
        $notificationhelper->identifier = 'kb_notification_id';
        $notificationhelper->list_id = 'kb_push_notifications_history';
        $notificationhelper->table = 'kb_push_notifications_history';
        $notificationhelper->table_id = 'kb_push_notifications_history';
        $notificationhelper->no_link = true;
        $notificationhelper->simple_header = false;
        $notificationhelper->show_toolbar = true;
        $notificationhelper->colorOnBackground = true;
        $notificationhelper->shopLinkType = false;
        $notificationhelper->title = $this->l('Notification History');
        $notificationhelper->token = Tools::getAdminTokenLite('AdminModules');
        $notificationhelper->currentIndex = AdminController::$currentIndex .'&configure=' . $this->name;
        $notificationhelper->actions = array('view');
        $start = 0;
        $limit = 50;
        
        if (isset($notificationhelper->_default_pagination)) {
            $limit = $notificationhelper->_default_pagination;
        }

        if (Tools::getIsset('page') && (int) Tools::getValue('page') > 1) {
            $page_number = (int) Tools::getValue('page');
            $start = (($page_number - 1) * $limit);
        }

        $filter_str = '';
        
        if (Tools::isSubmit('submitFilter' . $list_id) && Tools::getValue('submitFilter' . $list_id) == 1) {
            $filter_str = $this->getNotificationFilters();
            $this->tab_display = 'PushNotificationHistory';
        } elseif (Tools::isSubmit('submitReset' . $list_id)) {
            $filter_str = '';
            $this->tab_display = 'PushNotificationHistory';
            $context = Context::getContext();
            foreach ($this->notification_fields_list as $key => $param) {
                $temp = $param;
                unset($temp);
                $value_key = $list_id . 'Filter_' . $key;
                $context->cookie->$value_key = null;
                unset($context->cookie->$value_key);
                unset($_POST[$value_key]);
            }

            if (isset($context->cookie->{'submitFilter' . $list_id})) {
                unset($context->cookie->{'submitFilter' . $list_id});
            }
            if (isset($context->cookie->{$list_id . 'Orderby'})) {
                unset($context->cookie->{$list_id . 'Orderby'});
            }
            if (isset($context->cookie->{$list_id . 'Orderway'})) {
                unset($context->cookie->{$list_id . 'Orderway'});
            }
        }

        $notificationhelper->listTotal = $this->getNotifications(
            true,
            $filter_str
        );
        $_list = $this->getNotifications(
            false,
            $filter_str,
            $start,
            $limit
        );
       

        $number_of_entries = count($_list);
        $i = 0;
        while ($number_of_entries != 0) {
            $category_name = (Tools::strlen($_list[$i]['category_name']) > 23)
                    ? Tools::substr($_list[$i]['category_name'], 0, 20).'...'
                    : $_list[$i]['category_name'];
            $product_name = (Tools::strlen($_list[$i]['product_name']) > 23)
                    ? Tools::substr($_list[$i]['product_name'], 0, 20).'...'
                    : $_list[$i]['product_name'];
            $title = (Tools::strlen($_list[$i]['title']) > 23)
                    ? Tools::substr($_list[$i]['title'], 0, 20).'...'
                    :$_list[$i]['title'];
            $this->table_values[$i]['s_no'] = $i + 1;
            $this->table_values[$i]['title'] = $title;
            $this->table_values[$i]['kb_notification_id'] = $_list[$i]['kb_notification_id'];
            $this->table_values[$i]['image_type'] = Tools::ucfirst($_list[$i]['image_type']);
            $this->table_values[$i]['redirect_activity'] = Tools::ucfirst($_list[$i]['redirect_activity']);
            $this->table_values[$i]['category_name'] = $category_name;
            $this->table_values[$i]['product_name'] = $product_name;
            $this->table_values[$i]['date_add'] = $_list[$i]['date_add'];
            $i++;
            $number_of_entries--;
        }

        $notification_list = $notificationhelper->generateList($this->table_values, $this->notification_fields_list);
        
        unset($ajax);

        return $notification_list;
    }
    
    
    /*
     * Function to get Slider/banner list
     * 
     * @param string $ajax $type slider/banner
     * @param bool $ajax true if getting list in ajax request
     * @return string content of helper list
     */
    public function getTopcategoryForm($ajax)
    {
        $msg = array();
        $id_component = Tools::getValue('id_component');
        $options_categories = $this->createCategoryTree();
        $content_mode_options = array(
            array(
                'image_contentmode_id' => 'scaleAspectFill',
                'name' => $this->l('Scale aspect fill'),
            ),
            array(
                'image_contentmode_id' => 'scaleAspectFit',
                'name' => $this->l('Scale aspect Fit'),
            )
        );
        $image_category_1 = array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_1',
                            'id' => 'slideruploadedfile_1',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview1.tpl'),
                        );
        $this->slider_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'select',
                            'label' => $this->l('Image content Mode'),
                            'name' => 'image_content_mode',
                            'class' => 'chosen-dropdown',
                            'hint' => $this->l('Select the Activity where you have to redirect the customer after click.'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $content_mode_options,
                                'id' => 'image_contentmode_id',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 1st Category'),
                            'name' => 'category_id_1',
                            'onchange' => 'setCategoryId(this)',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        $image_category_1,
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 2nd Category'),
                            'name' => 'category_id_2',
                            'hint' => $this->l('Select the category type'),
                            'onchange' => 'setCategoryId(this)',
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_2',
                            'id' => 'slideruploadedfile_2',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview2.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 3rd Category'),
                            'name' => 'category_id_3',
                            'onchange' => 'setCategoryId(this)',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_3',
                            'id' => 'slideruploadedfile_3',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview3.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 4th Category'),
                            'name' => 'category_id_4',
                            'onchange' => 'setCategoryId(this)',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_4',
                            'id' => 'slideruploadedfile_4',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview4.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 5th Category'),
                            'name' => 'category_id_5',
                            'hint' => $this->l('Select the category type'),
                            'onchange' => 'setCategoryId(this)',
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_5',
                            'id' => 'slideruploadedfile_5',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview5.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 6th Category'),
                            'name' => 'category_id_6',
                            'onchange' => 'setCategoryId(this)',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_6',
                            'id' => 'slideruploadedfile_6',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview6.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 7th Category'),
                            'name' => 'category_id_7',
                            'onchange' => 'setCategoryId(this)',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_7',
                            'id' => 'slideruploadedfile_7',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview7.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select 8th Category'),
                            'name' => 'category_id_8',
                            'onchange' => 'setCategoryId(this)',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile_8',
                            'id' => 'slideruploadedfile_8',
                            'onclick' => 'uploadfile(this)',
                            'display_image' => true,
                            'required' => false,
                            'delete_url' => '',
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview8.tpl'),
                        ),
                    ), 'buttons' => array(
                        array(
                        'type' =>'submit',
                        'title' => $this->l('Save'),
                        'js' => "return veloValidateTopcategoryForm(this)",
                        'class' => 'btn btn-default pull-right kb_slider_banner_setting_btn'
                        )
                    ),)
            );
        
        $slider_field_value = array(
            'category_id_1' => 0,
            'category_id_2' => 0,
            'category_id_3' => 0,
            'category_id_4' => 0,
            'category_id_5' => 0,
            'category_id_6' => 0,
            'category_id_7' => 0,
            'category_id_8' => 0,
        );
        $sql = 'SELECT id_category,image_url,image_content_mode  FROM ' . _DB_PREFIX_ . 'kbmobileapp_top_category
                where id_component = '.(int)$id_component;
        $categories = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        if (count($categories) > 0) {
            $category_array = array();
            $category_array = explode('|', $categories[0]['id_category']);
            if (count($category_array) > 0) {
                $total_category = count($category_array) - 1;
                foreach ($category_array as $key => $cat_value) {
                    $k = $key+1;
                    if ($key <= $total_category && $cat_value != '') {
                        $slider_field_value['category_id_'.$k] = $cat_value;
                        //$image_category_1['delete_url'] = 'javascript://delebanner()';
                    }
                }
            }
            $slider_field_value['image_content_mode'] = $categories[0]['image_content_mode'];
        }
        
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $slider_field_value;
        $helper->name_controller = $this->name;
        $helper->table = 'slider2';
        $languages = Language::getLanguages();
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $new_slider_form = $helper->generateForm(array($this->slider_fields_form));
        $msg['html'] = $new_slider_form;
        echo Tools::jsonEncode($msg);
        die;
    }
    public function getSliderList($type, $ajax)
    {
        $this->slider_fields_list = array();
        $this->table_values = array();

        if ($type == 'slider') {
            $title = $this->l('Slider');
            $list_title = $this->l('Sliders');
        } elseif ($type == 'banner') {
            $title = $this->l('Banner');
            $list_title = $this->l('Banners');
        }
        $this->slider_fields_list['s_no'] = array(
            'title' => $this->l('S.No.'),
            'align' => 'center',
            'class' => '',
            'type' => 'int',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->slider_fields_list['kb_banner_id'] = array(
            'title' => $this->l('Banner ID'),
            'align' => 'center',
            'class' => 'td-vss-code td-vss',
        );
        $this->slider_fields_list['name'] = array(
            'title' => $title,
            'align' => 'center',
            'class' => '',
            'type' => 'int',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->slider_fields_list['image_type'] = array(
            'title' => $this->l('Image Type'),
            'align' => 'center',
            'class' => '',
            'display_image' => true,
        );
        
        $this->slider_fields_list['redirect_activity'] = array(
            'title' => $this->l('Redirect Activity'),
            'align' => 'center',
            'class' => '',
        );
        $this->slider_fields_list['category_name'] = array(
            'title' => $this->l('Category'),
            'align' => 'center',
            'class' => '',
        );
        $this->slider_fields_list['product_name'] = array(
            'title' => $this->l('Product'),
            'align' => 'center',
            'class' => '',
        );
        $this->slider_fields_list['status'] = array(
            'title' => $this->l('Status'),
            'active' => 'status',
            'icon' => array(
                0 => 'disabled.gif',
                1 => 'enabled.gif',
                'default' => 'disabled.gif'
            ),
            'ajax' => true,
            'class' => 'fixed-width-xs',
            'align' => 'center',
            'type' => 'bool',
            'orderby' => false,
        );
        

        
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_sliders_banners where type = "'.pSQL($type).'"';
        $row = Db::getInstance()->executeS($sql);
        $number_of_entries = count($row);
        $i = 0;
        while ($number_of_entries != 0) {
            $this->table_values[$i]['s_no'] = $i + 1;
            $this->table_values[$i]['kb_banner_id'] = $row[$i]['kb_banner_id'];
            if ($type == 'slider') {
                $this->table_values[$i]['name'] = $this->l('Slider')."#".($i + 1);
            } elseif ($type == 'banner') {
                $this->table_values[$i]['name'] = $this->l('Banner')."#".($i + 1);
            }
            $this->table_values[$i]['image_type'] = Tools::ucfirst($row[$i]['image_type']);
            $this->table_values[$i]['redirect_activity'] = Tools::ucfirst($row[$i]['redirect_activity']);
            $this->table_values[$i]['category_id'] = $row[$i]['category_id'];
            if ($row[$i]['category_id'] != 0 && $row[$i]['category_id'] != '' && $row[$i]['category_id'] != null) {
                $category_obj = new Category((int)$row[$i]['category_id'], $this->context->language->id, $this->context->shop->id);
                $category_name = (Tools::strlen($category_obj->name) > 23)
                        ? Tools::substr($category_obj->name, 0, 20).'...'
                        : $category_obj->name;
                $this->table_values[$i]['category_name'] = $category_name;
            } else {
                $this->table_values[$i]['category_name'] = null;
            }
            $product_name = (Tools::strlen($row[$i]['product_name']) > 23)
                    ? Tools::substr($row[$i]['product_name'], 0, 20).'...'
                    :$row[$i]['product_name'];
            $this->table_values[$i]['product_id'] = $row[$i]['product_id'];
            $this->table_values[$i]['product_name'] = $product_name;
            $this->table_values[$i]['status'] = $row[$i]['status'];
            $i++;
            $number_of_entries--;
        }
        
        
        $sliderhelper = new HelperList();
        $sliderhelper->module = $this;
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->controller = $this;
        }
        
        if ($type == 'slider') {
            $list_id = 'kb_sliders_list';
        } elseif ($type == 'banner') {
            $list_id = 'kb_banners_list';
        }
        $sliderhelper->imageType = 'jpg';
        $sliderhelper->identifier = 'kb_banner_id';
        $sliderhelper->list_id = $list_id;
        $sliderhelper->table = 'kb_sliders_banners';
        $sliderhelper->table_id = 'kb_banner_id';
        $sliderhelper->no_link = true;
        $sliderhelper->simple_header = true;
        $sliderhelper->listTotal = count($row);
        $sliderhelper->show_toolbar = false;
        $sliderhelper->colorOnBackground = true;
        $sliderhelper->shopLinkType = false;
        $sliderhelper->title = $list_title;
        $sliderhelper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $sliderhelper->actions = array('editslider');
        
        $sliders_list = $sliderhelper->generateList($this->table_values, $this->slider_fields_list);
        
        unset($ajax);

        return $sliders_list;
    }
    /* changes started by
    * @author- Rishabh jain
    * DOm : 19th Dec 18
    * to add the layout tab
     * function added to show the layout list
    */
    public function getlayoutList($ajax)
    {
        $this->layout_fields_list = array();
        $this->table_values = array();
    
    
        $title = $this->l('layouts');
        $list_title = $this->l('Layouts');
        $this->layout_fields_list['s_no'] = array(
            'title' => $this->l('S.No.'),
            'align' => 'center',
            'class' => '',
            'type' => 'int',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->layout_fields_list['kb_layout_id'] = array(
            'title' => $this->l('Layout ID'),
            'align' => 'center',
            'class' => 'td-vss-code',
            'type' => 'int',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->layout_fields_list['layout_name'] = array(
            'title' => $title,
            'align' => 'center',
            'class' => '',
            'type' => 'int',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layouts';
        $row = Db::getInstance()->executeS($sql);
        $number_of_entries = count($row);
        $i = 0;
        while ($number_of_entries != 0) {
            $this->table_values[$i]['s_no'] = $i + 1;
            $this->table_values[$i]['kb_layout_id'] = $row[$i]['id_layout'];
            $this->table_values[$i]['layout_name'] = $row[$i]['layout_name'];
            $i++;
            $number_of_entries--;
        }


        $layouthelper = new HelperList();
        $layouthelper->module = $this;
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->context->controller = $this;
        }

        $list_id = 'kb_layouts_list';
        $layouthelper->identifier = 'kb_layout_id';
        $layouthelper->list_id = $list_id;
        $layouthelper->table = 'kb_mobileapp_layouts';
        $layouthelper->table_id = 'kb_layout_ids';
        $layouthelper->no_link = true;
        $layouthelper->listTotal = count($row);
        $layouthelper->colorOnBackground = true;
        $layouthelper->shopLinkType = false;
        $layouthelper->title = $list_title;
        $layouthelper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $layouthelper->actions = array('editlayout','editLayoutName','deletelayout');
        $layouthelper->simple_header = false;
        $layouthelper->show_toolbar = true;
        $layout_list = $layouthelper->generateList($this->table_values, $this->layout_fields_list);
        $msg = array();
        if ($ajax) {
            $msg['msg'] = $this->displayConfirmation($this->l('Layout has been deleted successfully.'));
            $msg['html'] = $layout_list;
            $helper = new HelperView();
            $helper->module = $this;
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->current = AdminController::$currentIndex . '&configure=' . $this->name;
            $helper->show_toolbar = true;
            $helper->toolbar_scroll = true;
            $helper->override_folder = 'helpers/';
            $helper->base_folder = 'view/';
            $helper->base_tpl = 'button.tpl';

            $view = $helper->generateView();
            $helper->base_tpl = 'add_new_layout_button.tpl';
            $layoutbutton = $helper->generateView();
            $msg['button'] = $layoutbutton;
            $layout_option = array();
            $available_layouts = array();
            $sql = 'Select * from ' . _DB_PREFIX_ . 'kb_mobileapp_layouts';
            $available_layouts = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            $available_options = '';
            foreach ($available_layouts as $key => $layouts) {
                $available_options .= '<option value="'.$layouts['id_layout'].'" selected="selected">'.$layouts['layout_name'].'</option>';
            }
            $msg['layout_select_options'] = $available_options;
            echo Tools::jsonEncode($msg);
            die;
        } else {
            return $layout_list;
        }
        unset($ajax);
    }
    
    
    /* chnages over */
    /*
     * Function to get Payment Form
     * 
     * @param bool $ajax true if getting form in ajax request
     * @return string/array return array for ajax request else string 
     */
    public function getPaymentForm($ajax)
    {

        $options = array();
        $msg = array();

        $options[] = array(
            'payment_method_code' => '0',
            'name' => $this->l('Select Methods'),
        );

        $modeoptions = array();

        $modeoptions[] = array(
            'payment_method_mode_code' => 'live',
            'name' => $this->l('Live'),
        );
        $modeoptions[] = array(
            'payment_method_mode_code' => 'sandbox',
            'name' => $this->l('Sandbox'),
        );


        $payment_methods = $this->getPaymentMethods();
        foreach ($payment_methods as $key => $value) {
            if (!$this->checkMethods($key)) {
                $options[] = array(
                    'payment_method_code' => $key,
                    'name' => $value,
                );
            }
        }

        $this->fields_form2 = array(
            'form' => array(
                'id_form' => 'kbmobileapp_payment_methods',
                'legend' => array(
                    'title' => $this->l('Payment Method Data'),
                ),
                'input' => array(
                    array(
                        'type' => 'hidden',
                        'name' => 'save_payment_methods',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Payment Method'),
                        'name' => 'payment_method',
                        'class' => 'chosen-dropdown',
                        'hint' => $this->l('Select the Payment method'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $options,
                            'id' => 'payment_method_code',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Payment Method Name'),
                        'type' => 'text',
                        'lang' => true,
                        'hint' => $this->l('Enter Name of Payment Method'),
                        'class' => '',
                        'name' => 'payment_method_name',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Mode'),
                        'name' => 'payment_method_mode',
                        'class' => 'chosen-dropdown',
                        'hint' => $this->l('Select the Payment mode'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $modeoptions,
                            'id' => 'payment_method_mode_code',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Client Id'),
                        'type' => 'text',
                        'hint' => $this->l('Enter Client Id of payment method '),
                        'class' => '',
                        'name' => 'payment_method_client_id',
                    ),
                    array(
                        'label' => $this->l('Other Info'),
                        'type' => 'text',
                        'hint' => $this->l('Other information of payment method'),
                        'class' => '',
                        'name' => 'payment_method_other_info',
                    ),
                    array(
                        'type' => 'html',
                        'label' => '',
                        'name' => '',
                        'html_content' => $this->display(__FILE__, 'views/templates/admin/paypal_description.tpl'),
                    ),
                ), 'submit' => array(
                    'title' => $this->l('   Save   '),
                    'class' => 'btn btn-default pull-right kb_payment_method_btn'
                ),)
        );

        $payment_method_name = array();

        $languages = Language::getLanguages();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            $payment_method_name[$language['id_lang']] = '';
        }


        $payment_method_field_value = array(
            'payment_method' => '',
            'payment_method_name' => $payment_method_name,
            'payment_method_client_id' => '',
            'payment_method_other_info' => '',
            'payment_method_mode' => 'live',
            'save_payment_methods' => 1
        );




        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $payment_method_field_value;
        $helper->name_controller = $this->name;
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $form_add_new = $helper->generateForm(array($this->fields_form2));

        if ($ajax) {
            $msg['msg'] = $this->displayConfirmation($this->l('Payment Method Info has been deleted successfully.'));
            $msg['html'] = $form_add_new;
            echo Tools::jsonEncode($msg);
            die;
        } else {
            return $form_add_new;
        }
    }

    /*
     * Function to get Slider/banner Form
     * 
     * @param bool $ajax true if getting form in ajax request
     * @return string/array return array for ajax request else string  
     */

    public function getSliderForm($ajax, $id_component_type)
    {

        $msg = array();
        /* Form fields for slider Setting Form */
        
        $redirect_options = array(
            array(
                'redirect_name' => 'home',
                'name' => $this->l('Home'),
            ),
            array(
                'redirect_name' => 'category',
                'name' => $this->l('Category'),
            ),
            array(
                'redirect_name' => 'product',
                'name' => $this->l('Product'),
            )
        );
        $image_options = array(
            array(
                'image_type_id' => '',
                'name' => $this->l('Select an Option'),
            ),
            array(
                'image_type_id' => 'url',
                'name' => $this->l('URL'),
            ),
            array(
                'image_type_id' => 'image',
                'name' => $this->l('Upload'),
            )
        );
        // changes by rishabh jain
        $content_mode_options = array(
            array(
                'image_contentmode_id' => 'scaleAspectFill',
                'name' => $this->l('Scale aspect fill'),
            ),
            array(
                'image_contentmode_id' => 'scaleAspectFit',
                'name' => $this->l('Scale aspect Fit'),
            )
        );
        // changes over
        $options_categories = $this->createCategoryTree();
        
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->slider_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'status',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable this slider in mobile app.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Image Type'),
                            'name' => 'image_type',
                            'class' => 'chosen-dropdown',
                            'hint' => $this->l('Select the Image type for slider Upload/URL'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $image_options,
                                'id' => 'image_type_id',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'label' => $this->l('Image URL'),
                            'type' => 'text',
                            'hint' => $this->l('Image URL for Slider'),
                            'class' => '',
                            'name' => 'image_url',
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile',
                            'id' => 'slideruploadedfile',
                            'display_image' => true,
                            'required' => false,
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview.tpl'),
//                            'desc' => $this->l('Upload your Image')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Redirect Activity'),
                            'name' => 'redirect_activity',
                            'class' => 'chosen-dropdown',
                            'hint' => $this->l('Select the Activity where you have to redirect the customer after click.'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $redirect_options,
                                'id' => 'redirect_name',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select the Category'),
                            'name' => 'category_id',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'label' => $this->l('Enter the products name'),
                            'type' => 'text',
                            'hint' => $this->l('Start typing the products name'),
                            'class' => 'ac_input',
                            'name' => 'redirect_product_name',
                            'autocomplete' => false,
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'redirect_product_id',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'kb_banner_id',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'kb_banner_slider_type',
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'js' => "sliderFormvalidation()",
                        'class' => 'btn btn-default pull-right kb_slider_banner_setting_btn'
                    ),));
        } else {
            $this->slider_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Settings'),
                    ),
                    'input' => array(
                        array(
                            'label' => $this->l('Heading of this Component'),
                            'type' => 'text',
                            'lang' => true,
                            'hint' => $this->l('Enter Heading of the component'),
                            'class' => '',
                            'name' => 'banner_heading',
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Image Type'),
                            'name' => 'image_type',
                            'class' => 'chosen-dropdown',
                            'onchange' => 'showHideImageType(this)',
                            'hint' => $this->l('Select the Image type for slider Upload/URL'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $image_options,
                                'id' => 'image_type_id',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'label' => $this->l('Image URL'),
                            'type' => 'text',
                            'hint' => $this->l('Image URL for Slider'),
                            'class' => '',
                            'name' => 'image_url',
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Image:'),
                            'class' => '',
                            'name' => 'slideruploadedfile',
                            'id' => 'slideruploadedfile',
                            'onclick' => 'uploadfile()',
                            'display_image' => true,
                            'required' => false,
                            'image' => $this->display(__FILE__, 'views/templates/admin/image_preview.tpl'),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Image content Mode'),
                            'name' => 'image_content_mode',
                            'class' => 'chosen-dropdown',
                            'hint' => $this->l('Select the Activity where you have to redirect the customer after click.'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $content_mode_options,
                                'id' => 'image_contentmode_id',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select Redirect Activity'),
                            'name' => 'redirect_activity',
                            'class' => 'chosen-dropdown',
                            'onchange' => 'showHideRedirectType(this)',
                            'hint' => $this->l('Select the Activity where you have to redirect the customer after click.'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $redirect_options,
                                'id' => 'redirect_name',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Select the Category'),
                            'name' => 'category_id',
                            'hint' => $this->l('Select the category type'),
                            'is_bool' => true,
                            'options' => array(
                                'query' => $options_categories,
                                'id' => 'id_category_type',
                                'name' => 'name',
                            ),
                        ),
                        array(
                            'label' => $this->l('Enter the products name'),
                            'type' => 'text',
                            'hint' => $this->l('Start typing the products name'),
                            'class' => 'ac_input',
                            'name' => 'redirect_banner_product_name',
                            'autocomplete' => false,
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'redirect_banner_product_id',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'kb_banner_id',
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'kb_banner_slider_type',
                        ),
                        array(
                        'type' => 'datetime',
                        'label' => $this->l('Countdown Time validity'),
                        //'name' => 'countdown_validity',
                        'id' => 'countdown_validity',
                        'size' => 5,
                        'required' => true,
                        'hint' => $this->l('Select time till the countdown will be valid'),
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable background color'),
                            'name' => 'is_enabled_background_color',
                            'onchange' => 'showHidebackgroundColor(this)',
                            //'class' => 't',
                            'id' => 'is_enabled_background_color',
                            'required' => true,
                            //'desc' => $this->l(''),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                        'label' => $this->l('Timer Background Colour'),
                        'type' => 'color',
                        'hint' => $this->l('Select background colour for countdown timer.'),
                        'name' => 'timer_background_color',
                        'id' => 'timer_background_color',
                        'size' => 50,
                        'class' => 'kbsw_wheel_color',
                        'required' => true
                        ),
                        array(
                        'label' => $this->l('Timer text colour'),
                        'type' => 'color',
                        'hint' => $this->l('Change color of wheel.'),
                        'name' => 'timer_text_color',
                        'id' => 'timer_text_color',
                        'size' => 50,
                        'class' => 'kbsw_wheel_color',
                        'validate' => 'isColor',
                        'required' => true
                        ),
                    ), 'buttons' => array(
                        array(
                        'type' =>'submit',
                        'title' => $this->l('Save'),
                        'js' => "return veloValidateBannerSliderForm(this)",
                        'class' => 'btn btn-default pull-right kb_slider_banner_setting_btn'
                        )
                    ),
                )
            );
        }
        $id_component = Tools::getValue('id_component');
        $languages = Language::getLanguages(false);


        $slider_field_value = array(
            'image_type' => '',
            'image_url' => '',
            'redirect_activity' => '',
            'category_id' => '',
            'redirect_product_id' => '',
            'redirect_product_name' => '',
            'image_content_mode' => '',
            'kb_banner_id' => '',
            'timer_text_color' => '',
            'timer_background_color' => '',
            'countdown_validity' => '',
            'kb_banner_slider_type' => '',
            'is_enabled_background_color' => 0
        );
        $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $id_component;
        $banner_heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        $banner_heading_array = Tools::unSerialize($banner_heading_data);
        $slider_field_value['banner_heading'] = $banner_heading_array;
//        foreach ($languages as $k => $lang) {
//            $key = $k+1;
//            $slider_field_value['banner_heading_'.$key] = $banner_heading_array[$lang['id_lang']];
//        }
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $slider_field_value;
        $helper->name_controller = $this->name;
        $helper->table = 'slider2';
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $new_slider_form = $helper->generateForm(array($this->slider_fields_form));
        if ($ajax) {
            $this->layout_fields_list = array();
            $this->table_values = array();
            $title = $this->l('Banners');
            $list_title = $this->l('banners');
            $this->layout_fields_list['s_no'] = array(
                'title' => $this->l('S.No.'),
                'align' => 'center',
                'class' => '',
                'type' => 'int',
                'orderby' => false,
                'filter' => false,
                'search' => false
            );

//            $this->layout_fields_list['kb_component_id'] = array(
//                'title' => $this->l('Component ID'),
//                'align' => 'center',
//                'class' => 'td-vss-code  td-vss',
//                'type' => 'int',
//                'orderby' => false,
//                'filter' => false,
//                'search' => false
//            );
            $this->layout_fields_list['kb_banner_id'] = array(
                'title' => $this->l('Component ID'),
                'align' => 'center',
                'class' => 'td-vss-code  td-vss',
                'type' => 'int',
                'orderby' => false,
                'filter' => false,
                'search' => false
            );
            $this->layout_fields_list['Image'] = array(
                'title' => $this->l('Image'),
                'align' => 'center',
                'class' => '',
//                'type' => 'int',
                'callback' => 'getimagepath',
                'callback_object' => $this,
                'orderby' => false,
                'filter' => false,
                'search' => false
            );
            if ($id_component_type == 3) {
                $this->layout_fields_list['Countdown'] = array(
                    'title' => $this->l('Upto Time'),
                    'align' => 'center',
                    'class' => '',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false
                );
                $this->layout_fields_list['background_color'] = array(
                    'title' => $this->l('BG color.'),
                    'align' => 'center',
                    'class' => '',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false
                );
                $this->layout_fields_list['text_color'] = array(
                    'title' => $this->l('Text Color'),
                    'align' => 'center',
                    'class' => '',
                    'orderby' => false,
                    'filter' => false,
                    'search' => false
                );
            }
            $this->layout_fields_list['redirect'] = array(
                'title' => $this->l('Redirect.'),
                'align' => 'center',
                'class' => '',
                'orderby' => false,
                'filter' => false,
                'search' => false
            );
            $this->layout_fields_list['category_id'] = array(
                'title' => $this->l('category'),
                'align' => 'center',
                'class' => '',
                'type' => 'int',
                'orderby' => false,
                'filter' => false,
                'search' => false
            );
            $this->layout_fields_list['product_id'] = array(
                'title' => $this->l('Id product'),
                'align' => 'center',
                'class' => '',
                'type' => 'int',
                'orderby' => false,
                'filter' => false,
                'search' => false
            );
            $this->layout_fields_list['product'] = array(
                'title' => $this->l('Product'),
                'align' => 'center',
                'class' => '',
                'type' => 'int',
                'orderby' => false,
                'filter' => false,
                'search' => false
            );
            $banner_type = $id_component_type;
            $id_component = Tools::getValue('id_component');
            $sql = 'SELECT l.id_layout,lb.is_enabled_background_color,lb.banner_heading,lb.background_color,lb.text_color,lb.countdown,lb.image_path,lb.id,lb.image_url,lb.redirect_activity,lb.category_id,lb.product_name,lb.product_id FROM ' . _DB_PREFIX_ . 'kb_mobileapp_layouts l '
                . 'left join ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component lc '
                . 'on  (l.`id_layout` = lc.`id_layout`) '
                . 'left join ' . _DB_PREFIX_ . 'kbmobileapp_banners lb '
                . 'on  (lb.`id_component` = lc.`id_component`) '
                . 'where lc.`id_component` = '.(int)$id_component.' AND lb.`id_banner_type` = '.(int)$banner_type
                . ' order by lb.id asc';
            $row = Db::getInstance()->executeS($sql);
            $number_of_entries = count($row);
            $i = 0;
            while ($number_of_entries != 0) {
                $this->table_values[$i]['s_no'] = $i + 1;
                $this->table_values[$i]['kb_banner_id'] = $row[$i]['id'];
                $this->table_values[$i]['Image'] = $row[$i]['image_url'];
                $this->table_values[$i]['redirect'] = $row[$i]['redirect_activity'];
                if ($id_component_type == 3) {
                    $this->table_values[$i]['Countdown'] = $row[$i]['countdown'];
                    if ($row[$i]['is_enabled_background_color']) {
                        $this->table_values[$i]['background_color'] = $row[$i]['background_color'];
                    } else {
                        $this->table_values[$i]['background_color'] = '-';
                    }
                    $this->table_values[$i]['text_color'] = $row[$i]['text_color'];
                }
                $this->table_values[$i]['category_id'] = $row[$i]['category_id'];
                $this->table_values[$i]['product'] = $row[$i]['product_name'];
                $this->table_values[$i]['product_id'] = $row[$i]['product_id'];
                $i++;
                $number_of_entries--;
            }

            $layouthelper = new HelperList();
            $layouthelper->module = $this;
            if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                $this->context->controller = $this;
            }

            $list_id = 'kb_banners_component_list';
            $layouthelper->identifier = 'kb_banner_id';
            $layouthelper->list_id = $list_id;
            $layouthelper->table = 'kb_mobileapp_layouts';
            $layouthelper->table_id = 'kb_layout_ids';
            $layouthelper->no_link = true;
            $layouthelper->simple_header = true;
            $layouthelper->listTotal = count($row);
            $layouthelper->show_toolbar = false;
            $layouthelper->colorOnBackground = true;
            $layouthelper->shopLinkType = false;
            $layouthelper->title = $list_title;
            $layouthelper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
            $layouthelper->actions = array('deleteBanner');
            $layout_list = $layouthelper->generateList($this->table_values, $this->layout_fields_list);
            $msg['html'] = $new_slider_form.$layout_list;
            echo Tools::jsonEncode($msg);
            die;
        } else {
            return $new_slider_form;
        }
    }

    /*
     * Function to get Payment method Data by payment code
     * 
     * @param string $code unique code of payment method
     * @return json data of payment method in json format
     */
    public function getPaymentData($code)
    {
        $jsondata = array();
        $payment_method_name_data = array();
        $languages = Language::getLanguages();
        $payment_method_name = array();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            $payment_method_name[$language['id_lang']] = '';
        }

        $flag = 0;
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            foreach ($payment_data as $key_code => $data) {
                if ($data['payment_code'] == $code) {
                    $flag = 1;
                    $jsondata = $data;
                    $languages = Language::getLanguages();
                    foreach ($languages as $k => $language) {
                        $payment_method_name_data[$language['id_lang']] = $data['payment_name'][$language['iso_code']];
                    }
                    $jsondata['payment_name'] = $payment_method_name_data;
                    $jsondata['msg'] = 'success';
                }
            }
        }
        if ($flag == 0) {
            $jsondata['msg'] = 'failure';
        }

        echo Tools::jsonEncode($jsondata);
        die;
    }
    
    /*
     * Function to get Slider/banner Data by id
     * 
     * @param int $id id of slider or banner
     * @return json data of banner or slider in json format
     */
    public function getSliderData($id)
    {
        $jsondata = array();
        
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_sliders_banners where kb_banner_id ='.(int)$id;
        $row = Db::getInstance()->executeS($sql);
        if ($row) {
            $jsondata['category_id'] = $row[0]['category_id'];
            //start: added by aayushi on 13 Nov 2018 to resolve issue of editing banner image
            $jsondata['image_url'] = $row[0]['image_url']."?time_stamp=". time();
            //end: added by aayushi on 13 Nov 2018 to resolve issue of editing banner image
            //$jsondata['image_url'] = $row[0]['image_url']."?".time();
            $jsondata['image_type'] = $row[0]['image_type'];
            $jsondata['kb_banner_id'] = $row[0]['kb_banner_id'];
            $jsondata['product_id'] = $row[0]['product_id'];
            $jsondata['product_name'] = $row[0]['product_name'];
            $jsondata['redirect_activity'] = $row[0]['redirect_activity'];
            $jsondata['status'] = $row[0]['status'];
            $jsondata['type'] = $row[0]['type'];
        }

        echo Tools::jsonEncode($jsondata);
        die;
    }

    /*
     * Function to get payment method data to edit the details
     * 
     * @param string $code unique code of payment method
     * @return json data of payment method in json format
     */
    public function getimagepath($type, $path)
    {
        return '<img src="'.$type.'" style="max-width: 55px;max-height: 45px;">';
    }
    public function editPaymentData($code)
    {
        $jsondata = array();
        $payment_method_name_data = array();
        $languages = Language::getLanguages();
        $payment_method_name = array();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
            $payment_method_name[$language['id_lang']] = '';
        }

        $flag = 0;
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            foreach ($payment_data as $key_code => $data) {
                if ($data['payment_code'] == $code) {
                    $flag = 1;
                    $jsondata = $data;
                    $languages = Language::getLanguages();
                    foreach ($languages as $k => $language) {
                        $payment_method_name_data[$language['id_lang']] = $data['payment_name'][$language['iso_code']];
                    }
                    $jsondata['payment_name'] = $payment_method_name_data;
                    $jsondata['msg'] = 'success';
                }
            }
        }
        if ($flag == 0) {
            $jsondata['msg'] = 'failure';
        }

        echo Tools::jsonEncode($jsondata);
        die;
    }

    /*
     * Function to send Push Notifications request on firebase server
     * 
     * @return json return error status and message in json format
     */
    public function sendPushNotification()
    {

        $error = false;
        $notification_title = Tools::getValue('push_notification_title', '');
        $notification_message = Tools::getValue('push_notification_message', '');
        $notification_image_type = Tools::getValue('push_notification_image_type', 'url');
        $redirect_activity_type = Tools::getValue('push_notification_redirect_type', 'home');
        $redirect_category_id = Tools::getValue('push_notification_redirect_category_id', 0);
        $redirect_product_name = Tools::getValue('push_notification_redirect_product_name', '');
        $redirect_product_id = Tools::getValue('push_notification_redirect_product_id');
        $deviceType = Tools::getValue('push_notification_device_type', 'both');
        $firebase_server_key = '';
        if (Configuration::get('KB_MOBILEAPP_FIREBASE_KEY')) {
            $firebase_server_key = Configuration::get('KB_MOBILEAPP_FIREBASE_KEY');
        } else {
            $error = true;
            $msg = $this->l('Firebase Server key is not available');
        }
        $notification_image_url = '';

        if ($notification_image_type == 'url') {
            $notification_image_url = Tools::getValue('push_notification_image_url', '');
        } elseif ($notification_image_type == 'image') {
            if ($_FILES['uploadedfile']['size'] == 0) {
                $error = true;
                $msg = $this->l('File is empty');
            } else {
                $file_mimetypes = array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                );
                if (in_array($_FILES['uploadedfile']['type'], $file_mimetypes)) {
                    if ($_FILES['uploadedfile']['error'] > 0) {
                        $error = true;
                        $msg = $this->l('File has error');
                    } else {
                        $file_name = $_FILES['uploadedfile']['name'];
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $base_file_name = basename($file_name, $ext);
                        $mask = _PS_IMG_DIR_ . 'kbmobileapp/'. $base_file_name . '.*';
                        $matches = glob($mask);
                        if (count($matches) > 0) {
                            array_map('unlink', $matches);
                        }
                        
                        $path = _PS_IMG_DIR_ . 'kbmobileapp'  ;
                        
                        if (!move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $path . '/' . $file_name)) {
                            $error = true;
                            $msg = $this->l('Error in uploading a file');
                        } else {
                            $module_dir = '';
                            if ($this->checkSecureUrl()) {
                                $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                            } else {
                                $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                            }
                            $notification_image_url = $module_dir . 'kbmobileapp/' . $file_name;
                        }
                    }
                } else {
                    $error = true;
                    $msg = $this->l('Invalid File Format');
                }
            }
        } else {
            $error = true;
            $msg = $this->l('Image type not seletced.');
        }
        if (!$error) {
            $firebase = new Firebase();

            $imageUrl = $notification_image_url;
            $user_id = "";

            $filters = '';
            $product_id = null;
            $category_id = null;
            if ($redirect_activity_type == 'product') {
                $push_type = 'promotional_product';
                $product_id = $redirect_product_id;
            } elseif ($redirect_activity_type == 'category') {
                $push_type = 'promotional_category';
                $category_id = $redirect_category_id;
            } else {
                $push_type = 'promotional_home';
            }
            $firebase_data = array();
            $firebase_data['data']['title'] = $notification_title;
            $firebase_data['data']['is_background'] = false;
            $firebase_data['data']['message'] = $notification_message;
            $firebase_data['data']['image'] = $imageUrl;
            $firebase_data['data']['payload'] = '';
            $firebase_data['data']['user_id'] = $user_id;
            $firebase_data['data']['push_type'] = $push_type;
            $firebase_data['data']['category_id'] = $category_id;
            $firebase_data['data']['product_id'] = $product_id;
            $firebase_data['data']['filters'] = $filters;
            $firebase_data['data']['category_name'] = 'Test';
            
            if ($deviceType == 'android') {
                $response = $firebase->sendToTopic("ANDROID_USERS", $firebase_data, $firebase_server_key, $deviceType);
            } else if ($deviceType == 'ios') {
                $response = $firebase->sendToTopic("IOS_USERS", $firebase_data, $firebase_server_key, $deviceType);
            } else {
                $response = $firebase->sendToTopic("ANDROID_USERS", $firebase_data, $firebase_server_key, 'android');
                $response = $firebase->sendToTopic("IOS_USERS", $firebase_data, $firebase_server_key, 'ios');
            }
            
            $response = $firebase->sendToTopic("PROMO_OFFERS", $firebase_data, $firebase_server_key);

            $insert_query = "INSERT INTO `"._DB_PREFIX_."kb_push_notifications_history` "
                    . "(`kb_notification_id`,"
                    . " `title`,"
                    . " `message`,"
                    . " `image_type`,"
                    . " `image_url`,"
                    . " `redirect_activity`,"
                    . " `category_id`,"
                    . " `category_name`,"
                    . " `product_id`,"
                    . " `product_name`,"
                    . " `status`,"
                    . " `date_add`)"
                    . " VALUES ("
                    . "NULL,"
                    . " '".pSQL($notification_title)."',"
                    . " '".pSQL($notification_message)."',"
                    . " '".pSQL($notification_image_type)."',"
                    . " '".pSQL($imageUrl)."',"
                    . " '".pSQL($redirect_activity_type)."'";
            if ($redirect_activity_type == 'home') {
                $insert_query .= ", NULL, NULL, NULL, NULL,";
            }
            if ($redirect_activity_type == 'category') {
                $category_obj = new Category((int)$redirect_category_id, $this->context->language->id, $this->context->shop->id);
                $redirect_category_name = $category_obj->name;
                $insert_query .= ",". (int)$redirect_category_id.", '".pSQL($redirect_category_name)."', NULL, NULL,";
            }
            if ($redirect_activity_type == 'product') {
                $insert_query .= ", NULL, NULL,". (int)$redirect_product_id.", '".pSQL($redirect_product_name)."',";
            }
            
            $insert_query .= "'sent', now())";
            Db::getInstance()->execute($insert_query);
            
            $msg = $this->l('Notificaton Send successfully');
        }
        /* changes by rishabh jain to show default preview image */
        $demo_image = $this->module_url.'libraries/sample/404.gif';
        $jsondata = array('error' => $error, 'msg' => $msg, 'demo_image' => $demo_image);
        echo Tools::jsonEncode($jsondata);
        die;
    }

    /*
     * Function to check secure url on store
     * 
     * @return bool
     */
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    /*
     * Function to get list of categories on store
     * 
     * @return array category data
     */
    protected function createCategoryTree()
    {
        $data = array();
        $root_category = Category::getRootCategories();
        $all = Category::getSimpleCategories($this->context->language->id);
        $i = 0;
        foreach ($all as $c) {
            if ($root_category[0]['id_category'] != $c['id_category']) {
                $tmp = new Category($c['id_category'], $this->context->language->id, $this->context->shop->id);
                $parents = $tmp->getParentsCategories();

                $parents = array_reverse($parents);

                $str = '';
                foreach ($parents as $p) {
                    $str .= '>>' . $p['name'];
                }
                $data[$i + 1] = array(
                    'id_category_type' => $c['id_category'],
                    'name' => ltrim($str, '>>')
                );
            }
            $i++;
        }
        $data[0] = array(
            'id_category_type' => 0,
            'name' => $this->l("Select the category type")
        );
        return array_reverse($data);
    }
    
    /*
     * Function to get auto suggest product list
     * 
     */
    public function ajaxproductlist()
    {
        $query = Tools::getValue('q', false);
        if (!$query or $query == '' or Tools::strlen($query) < 1) {
            die();
        }

        /*
         * In the SQL request the "q" param is used entirely to match result in database.
         * In this way if string:"(ref : #ref_pattern#)" is displayed on the return list, 
         * they are no return values just because string:"(ref : #ref_pattern#)" 
         * is not write in the name field of the product.
         * So the ref pattern will be cut for the search request.
         */
        if ($pos = strpos($query, ' (ref:')) {
            $query = Tools::substr($query, 0, $pos);
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        /* Excluding downloadable products from packs because download from pack is not supported */
        $excludeVirtuals = (bool) Tools::getValue('excludeVirtuals', false);
        $exclude_packs = (bool) Tools::getValue('exclude_packs', false);

        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
                . 'p.id_product AND pl.id_lang = '
                . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE (pl.name LIKE \'%' . pSQL($query) . '%\' OR p.reference LIKE \'%' . pSQL($query) . '%\')' .
                (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . pSQL($excludeIds) . ') ' : ' ') .
                ($excludeVirtuals ? 'AND p.id_product NOT IN (SELECT pd.id_product FROM '
                        . '`' . _DB_PREFIX_ . 'product_download` pd WHERE (pd.id_product = p.id_product))' : '') .
                ($exclude_packs ? 'AND (p.cache_is_pack IS NULL OR p.cache_is_pack = 0)' : '');

        $items = Db::getInstance()->executeS($sql);

        if ($items) {
            foreach ($items as $item) {
                echo trim($item['name']) . (!empty($item['reference']) ?
                        ' (ref: ' . $item['reference'] . ')' : '') .
                '|' . (int) ($item['id_product']) . "\n";
            }
        }
    }
    
    /*
     * Function to get Default data of Notification settings
     * 
     * @return array default data of notification settings for order
     */
    public function getDafaultNotificationsData()
    {
        if (Configuration::get('KB_MOBILEAPP_NOTIFICATION_DATA')) {
            $notification_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_NOTIFICATION_DATA')));
        } else {
            $notification_data = array(
                'create_order' => array(
                    'status' => "0",
                    'title' => $this->l("Order Successfully Created"),
                    'message' => $this->l("Hi Thanks for your interest. Keep shopping with us to become our premium customer."),
                ),
                'order_status_change' => array(
                    'status' => "0",
                    'title' => $this->l("Order status update"),
                    'message' => $this->l("Hi Your order status has been changed to {{STATUS}}."),
                ),
                'abandoned_cart' => array(
                    'status' => "0",
                    'title' => $this->l("Hurry!"),
                    'message' => $this->l("Hi, Complete your order to get extra benefits on your next order."),
                    'interval' => 1,
                )
            );
        }
        
        return $notification_data;
    }
    
    /*
     * To show the details of notification
     * 
     * @param int $notification_id id of notification
     */
    public function showNotificationDetails($notification_id)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "kb_push_notifications_history where ";
        $sql .= " kb_notification_id=".(int)$notification_id;
        $row = Db::getInstance()->executeS($sql);

        $this->context->smarty->assign('title', $row[0]['title']);
        $this->context->smarty->assign('message', $row[0]['message']);
        $this->context->smarty->assign('redirect_activity', Tools::ucfirst($row[0]['redirect_activity']));
        $this->context->smarty->assign('image_type', Tools::ucfirst($row[0]['image_type']));
        $this->context->smarty->assign('image_url', $row[0]['image_url']);
        $this->context->smarty->assign('category_name', $row[0]['category_name']);
        $this->context->smarty->assign('product_name', $row[0]['product_name']);
        $this->context->smarty->assign('date_add', $row[0]['date_add']);
        
        echo $this->display(__FILE__, 'views/templates/admin/notification_details.tpl');
        die;
    }
    
    /*
     * Function to get list of notifications
     * 
     * @param bool $return_count true for number of records
     * @param string $filter_str conditional string
     * @param bool $return_count offset
     * @param string $filter_str limit
     * @return array
     */
    public function getNotifications(
        $return_count = false,
        $filter_str = '',
        $start = null,
        $limit = null
    ) {
        $sql = 'Select {{COLUMN}} from ' . _DB_PREFIX_ . 'kb_push_notifications_history as h where 1 '
            .$filter_str;

        if ($return_count) {
            $sql = Tools::str_replace_once('{{COLUMN}}', 'COUNT(*)', $sql);
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        } else {
            $columns = 'h.kb_notification_id, h.title, h.message, h.image_type, h.image_url, 
                h.redirect_activity, h.category_id, h.category_name, h.product_id,h.product_name,h.date_add ';
            $sql = Tools::str_replace_once('{{COLUMN}}', $columns, $sql);
            $sql .= ' order by kb_notification_id desc';
            if ($start !== null && $limit !== null) {
                $sql .= ' LIMIT ' . (int) $start . ', ' . (int) $limit;
            }
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        }
    }
    private function createLayoutDatabase()
    {
        $table = _DB_PREFIX_ . 'kb_mobileapp_layouts';
        $check_table_query = "SHOW TABLES LIKE '" . pSQL($table) . "'";
        $check_layout_table_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($check_table_query);
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_mobileapp_layouts` (
                `id_layout` int(11) NOT NULL AUTO_INCREMENT,
                `layout_name` varchar(200) NOT NULL,
                PRIMARY KEY (`id_layout`)
              ) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=latin1";
        Db::getInstance()->execute($sql);
        
        if (count($check_layout_table_result) == 0) {
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "kb_mobileapp_layouts` (`id_layout`, `layout_name`) VALUES
                    (1, 'Default layout')";
            Db::getInstance()->execute($sql);
        }
        
        $table = _DB_PREFIX_ . 'kbmobileapp_component_types';
        $check_table_query = "SHOW TABLES LIKE '" . pSQL($table) . "'";
        $check_table_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($check_table_query);
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kbmobileapp_component_types` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `component_name` varchar(200) NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1";
        Db::getInstance()->execute($sql);
        
        if (count($check_table_result) == 0) {
            $sql = "INSERT INTO `" . _DB_PREFIX_ . "kbmobileapp_component_types` (`id`, `component_name`) VALUES
                  (1, 'top_category'),
                  (2, 'banner_square'),
                  (3, 'banners_countdown'),
                  (4, 'banners_grid'),
                  (5, 'banner_horizontal_slider'),
                  (6, 'products_square'),
                  (7, 'products_horizontal'),
                  (8, 'products_recent'),
                  (9, 'products_grid')";
            Db::getInstance()->execute($sql);
        }
//        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_mobileapp_layout_component` (
//            `id_component` int(11) NOT NULL AUTO_INCREMENT,
//            `id_layout` int(11) NOT NULL,
//            `id_component_type` int(11) NOT NULL,
//            `position` int(11) NOT NULL,
//            `component_heading` varchar(200) DEFAULT NULL,
//            PRIMARY KEY (`id_component`)
//          ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
        /*start:changes made by aayushi to resolve bug of component heading*/
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_mobileapp_layout_component` (
            `id_component` int(11) NOT NULL AUTO_INCREMENT,
            `id_layout` int(11) NOT NULL,
            `id_component_type` int(11) NOT NULL,
            `position` int(11) NOT NULL,
            `component_heading` varchar(200) CHARACTER SET
            utf32 COLLATE utf32_general_ci NULL DEFAULT NULL,
            PRIMARY KEY (`id_component`)
          ) ENGINE=MyISAM DEFAULT CHARSET=latin1";
        /*end:changes made by aayushi to resolve bug of component heading*/
        Db::getInstance()->execute($sql);
        
        $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'kbmobileapp_banners` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_component` int(11) NOT NULL,
                `id_banner_type` int(11) NOT NULL,
                `countdown` varchar(200) DEFAULT NULL,
                `product_id` int(10) DEFAULT NULL,
                `category_id` int(10) DEFAULT NULL,
                `redirect_activity` enum("product","category","home") NOT NULL,
                `image_url` longtext,
                `image_type` enum("url","image") DEFAULT NULL,
                `product_name` varchar(200) DEFAULT NULL,
                `image_path` longtext,
                `image_contentMode` varchar(200) NOT NULL,
                `banner_heading` varchar(200) DEFAULT NULL,
                `background_color` varchar(11) DEFAULT NULL,
                `is_enabled_background_color` int(10) NOT NULL DEFAULT "1",
                `text_color` varchar(11) DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM AUTO_INCREMENT=65 DEFAULT CHARSET=latin1';
        Db::getInstance()->execute($sql);
        
        
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kbmobileapp_product_data` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `product_type` varchar(200) NOT NULL,
                `category_products` text,
                `custom_products` text,
                `image_content_mode` varchar(200) NOT NULL,
                `number_of_products` int(11) NOT NULL,
                `id_category` int(11) DEFAULT NULL,
                `id_component` int(11) NOT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1";
        Db::getInstance()->execute($sql);
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kbmobileapp_top_category` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_component` int(11) NOT NULL,
                `id_category` varchar(200) NOT NULL,
                `image_url` longtext,
                `image_content_mode` varchar(200) DEFAULT NULL,
                PRIMARY KEY (`id`)
              ) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1";
        Db::getInstance()->execute($sql);
        
        // To insert dummy data
        $sql = 'Select count(*) from '. _DB_PREFIX_ . 'kb_mobileapp_layout_component';
        $num_of_component = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        if ($num_of_component == 0) {
            // Banner Data
            $id_layout = 1;
            $custom_ssl_var = 0;
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
            if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
            } else {
                $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
            }
            $url = $module_dir.'kbmobileapp/libraries/sample/';
            
            $redirect_activity = 'home';
            $category_id = 0;
            $image_content_mode = 'scaleAspectFill';
            $redirect_product_id = 0;
            
            $languages = Language::getLanguages();
            $banner_heading = array();
            foreach ($languages as $k => $language) {
                $key = $k+1;
                $banner_heading[$language['id_lang']] = $this->l('These are default Banners.');
            }
            $banner_heading_data = serialize($banner_heading);
            $slider_heading = array();
            foreach ($languages as $k => $language) {
                $key = $k+1;
                $slider_heading[$language['id_lang']] = $this->l('These are default Sliders.');
            }
            $slider_heading_data = serialize($slider_heading);
            $product_name = '';
            $countdown = '';
            $is_enabled_background_color = 0;
            $background_color = '';
            $timer_text_color = '';
            $id_component_type = 5;
            $position = 1;
            $image_type = 'url';
            $image_path = '';
            
            $sql= 'insert into ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component
                        (`id_layout`,`id_component_type`,`position`,component_heading)
                             values('.$id_layout.','.$id_component_type.','.$position.',"'.psql($slider_heading_data).'")';
            if (Db::getInstance()->execute($sql)) {
                // changes for top sliders
                $result =  Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                $data = array(
                    array(
                        'image_url' => $url.'sample-slider1.jpg',
                    ),
                    array(
                        'image_url' => $url.'sample-slider2.jpg',
                    ),
                    array(
                        'image_url' => $url.'sample-slider3.jpg',
                    )
                );
                foreach ($data as $key => $value) {
                    $sql= 'insert into ' . _DB_PREFIX_ . 'kbmobileapp_banners
                        (`id_component`,
                        `id_banner_type`,`countdown`,
                        `product_id`,`category_id`,
                        `redirect_activity`,`image_url`,
                        `image_type`,`product_name`,
                        `image_path`,`image_contentMode`,
                        `background_color`,`text_color`,
                        `banner_heading`,`is_enabled_background_color`
                        ) values(
                        '.$result.','.$id_component_type.',"'.psql($countdown).'",'.$redirect_product_id.','.$category_id.',"'.$redirect_activity.'","'.$value['image_url'].'","'.$image_type.'","'.$product_name.'","'.$image_path.'","'.psql($image_content_mode).'","'.psql($background_color).'","'.psql($timer_text_color).'","",'.$is_enabled_background_color.')';
                    Db::getInstance()->execute($sql);
                }
                // changes over
            }
            // default data for horizontal banners
            $id_component_type = 2;
            $position = 2;
            $sql= 'insert into ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component
                        (`id_layout`,`id_component_type`,`position`,component_heading)
                             values('.$id_layout.','.$id_component_type.','.$position.',"'.psql($banner_heading_data).'")';
            if (Db::getInstance()->execute($sql)) {
                $result =  Db::getInstance(_PS_USE_SQL_SLAVE_)->Insert_ID();
                // changes for top sliders
                $data = array(
                    array(
                        'image_url' => $url.'sample-banner1.jpg',
                    ),
                    array(
                        'image_url' => $url.'sample-banner2.jpg',
                    ),
                );
                foreach ($data as $key => $value) {
                    $sql= 'insert into ' . _DB_PREFIX_ . 'kbmobileapp_banners
                        (`id_component`,
                        `id_banner_type`,`countdown`,
                        `product_id`,`category_id`,
                        `redirect_activity`,`image_url`,
                        `image_type`,`product_name`,
                        `image_path`,`image_contentMode`,
                        `background_color`,`text_color`,
                        `banner_heading`,`is_enabled_background_color`
                        ) values(
                        '.$result.','.$id_component_type.',"'.psql($countdown).'",'.$redirect_product_id.','.$category_id.',"'.$redirect_activity.'","'.$value['image_url'].'","'.$image_type.'","'.$product_name.'","'.$image_path.'","'.psql($image_content_mode).'","'.psql($background_color).'","'.psql($timer_text_color).'","",'.$is_enabled_background_color.')';
                    Db::getInstance()->execute($sql);
                }
            }
            //$component_type = 'banner_square';
            // Products Data
            $number_of_product = 10;
            $id_layout = 1;
            $image_content_mode = 'scaleAspectFill';
            $product_list = '';
            $product_type_1 = 'featured_products';
            $product_type_2 = 'best_seller';
            $product_type_3 = 'special_products';
            $languages = Language::getLanguages();
            $components_heading_1 = array();
            $components_heading_2 = array();
            $components_heading_3 = array();
            foreach ($languages as $k => $language) {
                $key = $k+1;
                $components_heading_1[$language['id_lang']] = $this->l('Featured Products');
                $components_heading_2[$language['id_lang']] = $this->l('Best Sellers Products');
                $components_heading_3[$language['id_lang']] = $this->l('Special Products');
            }
            $component_heading_data_featured = serialize($components_heading_1);
            $component_heading_data_best_Seller = serialize($components_heading_2);
            $component_heading_data_speacial = serialize($components_heading_3);
            $sql= 'insert into ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component
                        (`id_layout`,`id_component_type`,`position`,component_heading)
                             values('.$id_layout.',6, 3,"'.psql($component_heading_data_featured).'"),
                                 ('.$id_layout.',7,4,"'.psql($component_heading_data_best_Seller).'"),
                                     ('.$id_layout.',9,5,"'.psql($component_heading_data_speacial).'")';
            if (Db::getInstance()->execute($sql)) {
                $sql= 'insert into ' . _DB_PREFIX_ . 'kbmobileapp_product_data
                            (`id_component`,
                            `product_type`,`category_products`,
                            `custom_products`,`image_content_mode`,
                            `number_of_products`,`id_category`
                            ) values(
                            3,"'.psql($product_type_1).'","","","'.$image_content_mode.'",'.(int) $number_of_product.',0),
                            (
                            4,"'.psql($product_type_2).'","","","'.$image_content_mode.'",'.(int) $number_of_product.',0),
                            (
                            5,"'.psql($product_type_3).'","","","'.$image_content_mode.'",'.(int) $number_of_product.',0)';
                Db::getInstance()->execute($sql);
            }
        }
    }
    /*
     * Function to get notification filters query string
     * 
     * @return string query part to get notifications
     */
    public function getNotificationFilters()
    {
        $filter_str = '';
        $filter_prefix = 'kb_push_notifications_historyFilter_';
        if (Tools::getIsset($filter_prefix . 'title')
            && Tools::getValue($filter_prefix . 'title') != ''
        ) {
            $filter_str .= ' AND h.title LIKE "%'
                . pSQL(Tools::getValue($filter_prefix . 'title')) . '%"';
        }
        if (Tools::getIsset($filter_prefix . 'message')
            && Tools::getValue($filter_prefix . 'message') != ''
        ) {
            $filter_str .= ' AND h.message LIKE "%'
                . pSQL(Tools::getValue($filter_prefix . 'message')) . '%"';
        }
        if (Tools::getIsset($filter_prefix . 'image_type') && Tools::getValue($filter_prefix . 'image_type') != '') {
            $filter_str .= ' AND h.image_type LIKE "%' . pSQL(Tools::getValue($filter_prefix . 'image_type')) . '%"';
        }
        if (Tools::getIsset($filter_prefix . 'redirect_activity') && Tools::getValue($filter_prefix . 'redirect_activity') != '') {
            $filter_str .= ' AND h.redirect_activity LIKE "%' . pSQL(Tools::getValue($filter_prefix . 'redirect_activity')) . '%"';
        }
        if (Tools::getIsset($filter_prefix . 'category_name') && Tools::getValue($filter_prefix . 'category_name') != '') {
            $filter_str .= ' AND h.category_name LIKE "%' . pSQL(Tools::getValue($filter_prefix . 'category_name')) . '%"';
        }
        if (Tools::getIsset($filter_prefix . 'product_name') && Tools::getValue($filter_prefix . 'product_name') != '') {
            $filter_str .= ' AND h.product_name LIKE "%' . pSQL(Tools::getValue($filter_prefix . 'product_name')) . '%"';
        }
        if (Tools::getIsset($filter_prefix . 'date_add')) {
            $date_filters = Tools::getValue($filter_prefix . 'date_add');
            if ((isset($date_filters[0]) && !empty($date_filters[0]))
                || (isset($date_filters[1]) && !empty($date_filters[1]))
            ) {
                $filter_str .= ' AND (';
                if ((isset($date_filters[0]) && !empty($date_filters[0]))
                    && (isset($date_filters[1]) && !empty($date_filters[1]))
                ) {
                    $filter_str .= 'DATE(h.date_add) >= "' . pSQL(date('Y-m-d', strtotime($date_filters[0]))) . '" 
                        AND DATE(h.date_add) <= "' . pSQL(date('Y-m-d', strtotime($date_filters[1]))) . '"';
                } elseif (isset($date_filters[0]) && !empty($date_filters[0])) {
                    $filter_str .= 'DATE(h.date_add) >= "' . pSQL(date('Y-m-d', strtotime($date_filters[0]))) . '"';
                } elseif (isset($date_filters[1]) && !empty($date_filters[1])) {
                    $filter_str .= 'DATE(h.date_add) <= "' . pSQL(date('Y-m-d', strtotime($date_filters[1]))) . '"';
                }
                $filter_str .= ')';
            }
        }

        return $filter_str;
    }

    /*
     *Function to set the banner and slider data
     * 
     * @return array 
     */

    public function setBannerData()
    {
        $error = false;
        $status = Tools::getValue('status', '0');
        $image_type = Tools::getValue('image_type', 'url');
        $redirect_activity_type = Tools::getValue('redirect_activity', 'home');
        $redirect_category_id = Tools::getValue('category_id', 0);
        $redirect_product_name = Tools::getValue('redirect_product_name', '');
        $redirect_product_id = Tools::getValue('redirect_product_id');
        $kb_banner_id = Tools::getValue('kb_banner_id');
        $type = Tools::getValue('kb_banner_slider_type');
        $image_url = Tools::getValue('image_url', '');

        
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
        }
        if ($image_type == 'url') {
            $image_url = Tools::getValue('image_url', '');
            $content = Tools::file_get_contents($image_url);
            $name = $type . '_' . $kb_banner_id;
            $path = _PS_IMG_DIR_ . 'kbmobileapp/' . $name . 'jpg';
            $fp = fopen($path, "w");
            fwrite($fp, $content);
            fclose($fp);
            $image_url = $module_dir . 'kbmobileapp/' . $name . 'jpg';
        } elseif ($image_type == 'image') {
            if ($_FILES['slideruploadedfile']['size'] == 0) {
            } else {
                $file_mimetypes = array(
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                    'application/x-shockwave-flash',
                    'image/psd',
                    'image/bmp',
                    'image/tiff',
                    'application/octet-stream',
                    'image/jp2',
                    'image/iff',
                    'image/vnd.wap.wbmp',
                    'image/xbm',
                    'image/vnd.microsoft.icon',
                    'image/webp'
                );
                if (in_array($_FILES['slideruploadedfile']['type'], $file_mimetypes)) {
                    if ($_FILES['slideruploadedfile']['error'] > 0) {
                        $error = true;
                        $msg = $this->l('File has error');
                    } else {
                        $file_name = $_FILES['slideruploadedfile']['name'];
                        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                        $modified_file_name = $type . '_' . $kb_banner_id . '.' . $ext;
                        $base_file_name = basename($file_name, $ext);
//                        $mask = _PS_MODULE_DIR_ . 'kbmobileapp/libraries/uploads/' . $base_file_name . '.*';
                        $mask = _PS_IMG_DIR_ . 'kbmobileapp/' . $base_file_name . '.*';
                        $matches = glob($mask);
                        if (count($matches) > 0) {
                            array_map('unlink', $matches);
                        }
                        $mask = _PS_IMG_DIR_ . 'kbmobileapp/' . $type . '_' . $kb_banner_id . '.*';
                        $matches = glob($mask);
                        if (count($matches) > 0) {
                            array_map('unlink', $matches);
                        }
                        
                        $path = _PS_IMG_DIR_ . 'kbmobileapp';
                        if (!move_uploaded_file($_FILES['slideruploadedfile']['tmp_name'], $path . '/' . $modified_file_name)) {
                            $error = true;
                            $msg = $this->l('Error in uploading a file');
                        } else {
                            $image_url = $module_dir . 'kbmobileapp/' . $modified_file_name;
                        }
                    }
                } else {
                    $error = true;
                    $msg = $this->l('Invalid File Format');
                }
            }
        } else {
            $error = true;
            $msg = $this->l('Image type not seletced.');
        }
        if (!$error) {
            $update_query = "UPDATE `" . _DB_PREFIX_ . "kb_sliders_banners` ";
            $update_query .= "SET `status` = " . (int) $status;
            $update_query .= ",`image_type` = '" . pSQL($image_type) . "'";
            $update_query .= ",`image_url` = '" . pSQL($image_url) . "'";
            $update_query .= ",`type` = '" . pSQL($type) . "'";
            $update_query .= ",`redirect_activity` = '" . pSQL($redirect_activity_type) . "'";
            if ($redirect_activity_type == 'home') {
                $update_query .= ",`category_id` = NULL, `product_id` = NULL, `product_name` = NULL";
            }
            if ($redirect_activity_type == 'category') {
                $update_query .= ",`category_id` = " . (int) $redirect_category_id;
                $update_query .= ", `product_id` = NULL, `product_name` = NULL";
            }
            if ($redirect_activity_type == 'product') {
                $update_query .= ",`category_id` = NULL";
                $update_query .= ", `product_id` = " . (int) $redirect_product_id;
                $update_query .= ", `product_name` = '" . pSQL($redirect_product_name) . "'";
            }
            
            $update_query .= ", `date_upd` = now() WHERE `kb_banner_id` = " . (int) $kb_banner_id;
            
            
            Db::getInstance()->execute($update_query);
            
            
            if ($type == 'slider') {
                $msg = $this->l('Slider Information updated successfully');
                $this->tab_display = 'SlidersSettings';
            } elseif ($type == 'banner') {
                $msg = $this->l('Banner Information updated successfully');
                $this->tab_display = 'BannersSettings';
            } else {
                $msg = '';
            }
        }
        $jsondata = array('error' => $error, 'msg' => $msg);
        return $jsondata;
    }
    
    /*
     * Set default settings of banner and slider while installing
     */
    public function setDefaultBannersSliders()
    {
        
        $table = _DB_PREFIX_.'kb_sliders_banners';
        $check_table_query = "SHOW TABLES LIKE '".pSQL($table)."'";
        $check_table_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($check_table_query);
        
        $custom_ssl_var = 0;
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
            $custom_ssl_var = 1;
        }
        
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        $url = $module_dir.'kbmobileapp/libraries/sample/';
        $data = array(
            array(
                'status' => 1,
                'image_type' => 'url',
                'type' => 'slider',
                'redirect_activity' => 'category',
                'image_url' => $url.'sample-slider1.jpg',
                'category_id' => 3,
            ),
            array(
                'status' => 1,
                'image_type' => 'url',
                'type' => 'slider',
                'redirect_activity' => 'category',
                'image_url' => $url.'sample-slider2.jpg',
                'category_id' => 3,
            ),
            array(
                'status' => 1,
                'image_type' => 'url',
                'type' => 'slider',
                'redirect_activity' => 'category',
                'image_url' => $url.'sample-slider3.jpg',
                'category_id' => 3,
            ),
            array(
                'status' => 1,
                'image_type' => 'url',
                'type' => 'banner',
                'redirect_activity' => 'category',
                'image_url' => $url.'sample-banner1.jpg',
                'category_id' => 3,
            ),
            array(
                'status' => 1,
                'image_type' => 'url',
                'type' => 'banner',
                'redirect_activity' => 'category',
                'image_url' => $url.'sample-banner2.jpg',
                'category_id' => 3,
            ),
        );
        
        if (count($check_table_result) == 0) {
            $bannerquery = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "kb_sliders_banners` (
                `kb_banner_id` int(11) NOT NULL AUTO_INCREMENT,
                `status` int(2) NOT NULL,
                `image_type` enum('url','image') NOT NULL,
                `image_url` text NOT NULL,
                `type` enum('banner','slider') NOT NULL,
                `redirect_activity` enum('home','category','product') NOT NULL,
                `category_id` int(10) DEFAULT NULL,
                `product_id` int(10) DEFAULT NULL,
                `product_name` varchar(250) DEFAULT NULL,
                `date_add` datetime NOT NULL,
                `date_upd` datetime NOT NULL,
                PRIMARY KEY (`kb_banner_id`)
            ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8";
            Db::getInstance()->execute($bannerquery);
            foreach ($data as $values) {
                $query = "INSERT INTO `"._DB_PREFIX_."kb_sliders_banners` ("
                    . "`kb_banner_id`,"
                    . " `status`,"
                    . " `image_type`,"
                    . " `image_url`,"
                    . " `type`,"
                    . " `redirect_activity`,"
                    . " `category_id`,"
                    . " `product_id`,"
                    . " `product_name`,"
                    . " `date_add`,"
                    . " `date_upd`)"
                    . " VALUES ("
                    . "NULL, ".(int)($values['status']).","
                    . " '".pSQL($values['image_type'])."',"
                    . " '".pSQL($values['image_url'])."', '".pSQL($values['type'])."',"
                    . " '".pSQL($values['redirect_activity'])."',"
                    . " ".(int)$values['category_id'].", NULL, NULL, now(), now())";

                Db::getInstance()->execute($query);
            }
        }
    }
         
    /*
     * Function to send Push Notifications request on firebase server while placing and order
     * or after order status change
     * 
     * @param string $type having value order_create/order_status_change
     * @param int $cart_id id of cart
     * @param int $order_id id of order
     * @param string $order_status status of order
     * @param string $email email address of customer
     */
    public function sendNotificationRequest($type, $cart_id = null, $order_id = null, $order_status = null, $email = null)
    {
        // firebase instance
        $notification_data = array();
        
        $firebase_server_key = '';
        if (Configuration::get('KB_MOBILEAPP_FIREBASE_KEY')) {
            $firebase_server_key = Configuration::get('KB_MOBILEAPP_FIREBASE_KEY');
        }
        if (Configuration::get('KB_MOBILEAPP_NOTIFICATION_DATA')) {
            $notification_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_NOTIFICATION_DATA')));
        }
        $push_type = '';
        $title = '';
        $message = '';
        if ($type == 'order_create') {
            if ($notification_data['create_order']['status'] == 0) {
                return false;
            } else {
                $title = $notification_data['create_order']['title'];
                $message = $notification_data['create_order']['message'];
                $push_type = 'order_placed';
            }
        } elseif ($type == 'order_status_change') {
            if ($notification_data['order_status_change']['status'] == 0) {
                return false;
            } else {
                $title = $notification_data['order_status_change']['title'];
                $message = Tools::str_replace_once(
                    '{{STATUS}}',
                    $order_status,
                    $notification_data['order_status_change']['message']
                );
                $push_type = 'order_status_changed';
            }
        }
        
        
        $firebase = new Firebase();

        $user_id = "";

        $firebase_data = array();
        $firebase_data['data']['title'] = $title;
        $firebase_data['data']['is_background'] = false;
        $firebase_data['data']['message'] = $message;
        $firebase_data['data']['image'] = '';
        $firebase_data['data']['payload'] = '';
        $firebase_data['data']['user_id'] = $user_id;
        $firebase_data['data']['push_type'] = $push_type;
        $firebase_data['data']['cart_id'] = $cart_id;
        $firebase_data['data']['order_id'] = $order_id;
        $firebase_data['data']['email_id'] = $email;

        if ($fcm_ids = $this->getFcmIdByCartId($cart_id)) {
            foreach ($fcm_ids as $data) {
                $firebase->sendMultiple($data['fcm_id'], $firebase_data, $firebase_server_key, $data['device_type']);
            }
        }
    }
    
    /*
     * Function to get the fcm id by cart id
     * 
     * @return array
     */
    public function getFcmIdByCartId($cart_id)
    {
        $query = 'SELECT Distinct(fcm_id),device_type FROM ' . _DB_PREFIX_ . 'kb_fcm_details where kb_cart_id ='.(int)$cart_id;
        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
        if ($data) {
            return $data;
        } else {
            return false;
        }
    }
    
    /*
     * Hook function excute when an order is place
     * used to trigger order create push notification
     */
    public function hookNewOrder($params)
    {
        $cart_id = $params['cart']->id;
        $customer_id = $params['cart']->id_customer;
        $order_id = $params['order']->id;
        $type = 'order_create';
        $customer = new Customer((int) $customer_id);
        $customer_email = '';
        if ($customer) {
            $customer_email = $customer->email;
        }
        $this->sendNotificationRequest($type, $cart_id, $order_id, null, $customer_email);
    }
    
    /*
     * Hook function execute on order status change action
     * used to trigger order status change notification
     */
    public function hookActionOrderStatusPostUpdate($params)
    {
        $order_status = $params['newOrderStatus']->name;
        $cart_id = $params['cart']->id;
        $order_id = $params['id_order'];
        $customer_id = $params['cart']->id_customer;
        $type = 'order_status_change';
        $customer = new Customer((int) $customer_id);
        $customer_email = '';
        if ($customer) {
            $customer_email = $customer->email;
        }
        $this->sendNotificationRequest($type, $cart_id, $order_id, $order_status, $customer_email);
    }
    
    /*
     * Function to set the staus of banner and sliders
     */
    public function changeSliderBannerStatus($id_banner, $status)
    {
        $update_query = "UPDATE `"._DB_PREFIX_."kb_sliders_banners` ";
        $update_query .= "SET `status` = ".(int)$status;
        $update_query .= ", `date_upd` = now() WHERE `kb_banner_id` = ".(int)$id_banner;
        Db::getInstance()->execute($update_query);
        die;
    }
    
    /*
     * Function to Add order status for paypal payment method
     */
    protected function addPaypalOrderStatus()
    {
        if (!Configuration::get('KB_PAYPAL_AWT_STATUS_ID')) {
            $awaiting_paypal_name = $this->l('Mobile APP Awaiting Paypal Payment');
            $this->addPaymentStatus($awaiting_paypal_name, '#008ABD', 'KB_PAYPAL_AWT_STATUS_ID', 0, 0, 0);
        }
        
        if (!Configuration::get('KB_PAYPAL_ACCEPTED_STATUS_ID')) {
            $success_paypal_name = $this->l('Mobile APP Paypal Payment Accepted');
            $this->addPaymentStatus($success_paypal_name, '#32CD32', 'KB_PAYPAL_ACCEPTED_STATUS_ID', 1, 1, 1);
        }
    }
    
    /*
     * Function to add order status in order state table
     * @param string $name name of order state
     * @param string $color_code color code of order state
     * @param string $config_name Name of configuration for order state
     *
     */
    protected function addPaymentStatus($name, $color_code, $config_name, $send_email, $invoice, $paid)
    {
        $order_status = new OrderState();
        foreach (Language::getLanguages(true) as $lang) {
            $order_status->name[$lang['id_lang']] = $name;
            $order_status->template[$lang['id_lang']] = 'payment';
        }
        $order_status->send_email = $send_email;
        $order_status->module_name = $this->name;
        $order_status->invoice = $invoice;
        $order_status->color = $color_code;
        $order_status->unremovable = 0;
        $order_status->logable = 1;
        $order_status->delivery = 0;
        $order_status->hidden = 0;
        $order_status->shipped = 0;
        $order_status->paid = $paid;
        if (version_compare(_PS_VERSION_, '1.7.0.2', '>')) {
            $order_status->pdf_invoice = 1;
            $order_status->pdf_delivery = 0;
        }
        $order_status->add();
        Configuration::updateGlobalValue($config_name, $order_status->id);
        $icon_path = $this->getStatusImagePath($config_name);
        $this->addIconForOrderStaus($icon_path, $config_name);
    }


    /*
     * function to delete the order statuses
     */
    protected function deleteOrderStatuses()
    {
        foreach ($this->getOrderStateConfiguration() as $config) {
            $order_status = new OrderState(Configuration::get($config));
            $order_status->delete();
        }
    }
    
    /*
     * Copy Icon for order status from our module to store img dir
     * @param string $icon_path path of icon
     * @param string $config_name name of configuration
     *
     */
    protected function addIconForOrderStaus($icon_path, $config_name)
    {
        if (Tools::file_exists_no_cache($icon_path)) {
            if (is_writable(_PS_ORDER_STATE_IMG_DIR_)) {
                Tools::copy(
                    $icon_path,
                    _PS_ORDER_STATE_IMG_DIR_.Configuration::get($config_name).'.gif'
                );
            }
        }
    }
    
    /*
     * Get image path as per the configuration name
     * @param string $config_name name of configuration
     *
     * @return string path of image icon
     */
    protected function getStatusImagePath($config_name)
    {
        $module_dir = _PS_MODULE_DIR_.$this->name;
        $icon_img = array(
            'KB_PAYPAL_AWT_STATUS_ID' => $module_dir.'/views/img/paypal_awt.gif',
            'KB_PAYPAL_ACCEPTED_STATUS_ID' => $module_dir.'/views/img/paypal_accepted.gif',
        );
        
        return $icon_img[$config_name];
    }
    
    /*
     * Function to get the configuration name of order status created by our module
     *
     * @return array
     */
    protected function getOrderStateConfiguration()
    {
        return array(
            'KB_PAYPAL_AWT_STATUS_ID',
            'KB_PAYPAL_ACCEPTED_STATUS_ID'
        );
    }
    
    /*
     * Function to get Google Setup Form
     * 
     * 
     * @return string
     */
    public function getGoogleSetupForm()
    {

        $msg = array();
        /* Form fields for Google Setting Form */
        
        $description = '';
        if ($filename = Configuration::get('KB_MOBILE_APP_GOOGLE_FILE')) {
            $dirpath = _PS_IMG_DIR_ . 'kbmobileapp/';
            $filepath = $dirpath.$filename;
            if (Tools::file_exists_no_cache($filepath)) {
                $custom_ssl_var = 0;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $custom_ssl_var = 1;
                }

                if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                    $image_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                } else {
                    $image_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_IMG_DIR_);
                }
                $this->context->smarty->assign('json_file_link', $image_dir . 'kbmobileapp/'.$filename.'?time=' . time());

                $description = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'kbmobileapp/views/templates/admin/link_json_file.tpl');
            }
        }
        
        
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $google_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Google Setup'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'google[status]',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable the Google login option in the mobile App.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'google_active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'google_active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Json File:'),
                            'class' => '',
                            'name' => 'google[jsonfile]',
                            'id' => 'googlejsonfile',
                            'display_image' => true,
                            'required' => false,
                            'desc' => $description
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'googlefilename',
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_google_setup_btn'
                    ),));
        } else {
            $google_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Google Setup'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable'),
                            'name' => 'google_status',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable the Google login option in the mobile App.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'google_active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'google_active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'type' => 'file',
                            'label' => $this->l('Json File:'),
                            'class' => '',
                            'name' => 'googlejsonfile',
                            'id' => 'googlejsonfile',
                            'display_image' => false,
                            'required' => false,
                            'desc' => $description
                        ),
                        array(
                            'type' => 'hidden',
                            'name' => 'googlefilename',
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_google_setup_btn'
                    ),)
            );
        }


        $languages = Language::getLanguages();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }


        $google_setup_field_value = array(
            'google_status' => (Configuration::get('KB_MOBILEAPP_GOOGLE_DATA')) ? Configuration::get('KB_MOBILEAPP_GOOGLE_DATA') : '',
            'googlejsonfile' => '',
            'googlefilename' => (Configuration::get('KB_MOBILE_APP_GOOGLE_FILE')) ? Configuration::get('KB_MOBILE_APP_GOOGLE_FILE') : ''
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $google_setup_field_value;
        $helper->name_controller = $this->name;
        $helper->table = 'google_setup';
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $new_google_form = $helper->generateForm(array($google_fields_form));

        return $new_google_form;
    }
    
    /*
     * Function to get Facebook Setup Form
     * 
     * 
     * @return string
     */
    public function getFacebookSetupForm()
    {

        $msg = array();
        /* Form fields for Google Setting Form */
        
        
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $facebook_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Facebook Setup'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Enable'),
                            'name' => 'facebook_setup_status',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable the Google login option in the mobile App.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'facebook_active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'facebook_active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Facebook APP Id'),
                            'type' => 'text',
                            'hint' => $this->l('Enter the facebook app id'),
                            'class' => 'ac_input',
                            'name' => 'facebook_setup_app_id',
                            'autocomplete' => false,
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_facebook_setup_btn'
                    ),));
        } else {
            $facebook_fields_form = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Facebook Setup'),
                    ),
                    'input' => array(
                        array(
                            'type' => 'switch',
                            'label' => $this->l('Enable'),
                            'name' => 'facebook_setup_status',
                            'class' => 't',
                            'required' => true,
                            'desc' => $this->l('This setting will enable/disable the Google login option in the mobile App.'),
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'facebook_active_on',
                                    'value' => 1,
                                    'label' => $this->l('Enable')
                                ),
                                array(
                                    'id' => 'facebook_active_off',
                                    'value' => 0,
                                    'label' => $this->l('Disable')
                                )
                            ),
                        ),
                        array(
                            'label' => $this->l('Facebook APP Id'),
                            'type' => 'text',
                            'hint' => $this->l('Enter the facebook app id'),
                            'class' => 'ac_input',
                            'name' => 'facebook_setup_app_id',
                            'autocomplete' => false,
                        ),
                    ), 'submit' => array(
                        'title' => $this->l('Save'),
                        'class' => 'btn btn-default pull-right kb_facebook_setup_btn'
                    ),)
            );
        }


        $languages = Language::getLanguages();
        foreach ($languages as $k => $language) {
            $languages[$k]['is_default'] = ((int) ($language['id_lang'] == $this->context->language->id));
        }


        $facebookdata = array();
        if (Configuration::get('KB_MOBILEAPP_FACEBOOK_DATA')) {
            $facebookdata = Tools::unserialize((Configuration::get('KB_MOBILEAPP_FACEBOOK_DATA')));
        }
        
        $facebook_setup_field_value = array(
            'facebook_setup_status' => isset($facebookdata['status']) ? $facebookdata['status'] : '',
            'facebook_setup_app_id' => isset($facebookdata['app_id']) ? $facebookdata['app_id'] : ''
        );

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $facebook_setup_field_value;
        $helper->name_controller = $this->name;
        $helper->table = 'facebook_setup';
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $new_facebook_form = $helper->generateForm(array($facebook_fields_form));

        return $new_facebook_form;
    }
    
    /*
     * Function to set google Data
     * 
     */
    public function getProductForm($ajax, $id_component_type)
    {
        $msg = array();
        /* Form fields for slider Setting Form */

        $product_type_options = array(
            array(
                'product_type_id' => 'best_seller',
                'name' => $this->l('Best Seller Products'),
            ),
            array(
                'product_type_id' => 'featured_products',
                'name' => $this->l('Featured Products'),
            ),
            array(
                'product_type_id' => 'new_products',
                'name' => $this->l('New Products'),
            ),
            array(
                'product_type_id' => 'special_products',
                'name' => $this->l('Special Products'),
            ),
            array(
                'product_type_id' => 'category_products',
                'name' => $this->l('From a category'),
            ),
            array(
                'product_type_id' => 'custom_products',
                'name' => $this->l('Custom Products'),
            ),
        );
        // changes by rishabh jain
        $content_mode_options = array(
            array(
                'image_contentmode_id' => 'scaleAspectFill',
                'name' => $this->l('Scale aspect fill'),
            ),
            array(
                'image_contentmode_id' => 'scaleAspectFit',
                'name' => $this->l('Scale aspect Fit'),
            )
        );
        $product_list_options = array();
        $product_list_options = $this->getProductList();
        $category_product_list_options = array();
        $id_category = 0;
        $id_component = Tools::getValue('id_component', 0);
        if ($id_component) {
            $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_product_data where id_component =' . (int) $id_component;
            $component_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
            if (is_array($component_data)) {
                $id_category = $component_data['id_category'];
            }
        }
        if ($id_category) {
            $category_product_list_options = $this->getProductListByCategory($id_category);
        } else {
            $category_product_list_options = $this->getProductListByCategory($id_category);
        }
            //$category_product_list_options
        
        // changes over
        $options_categories = $this->createCategoryTree();
        $this->slider_fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Heading of this Component'),
                        'type' => 'text',
                        'lang' => true,
                        'hint' => $this->l('Enter Heading of the component'),
                        'class' => '',
                        'name' => 'component_heading',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Product Type'),
                        'name' => 'product_type',
                        'class' => 'chosen-dropdown',
                        'onchange' => 'showHideProductType(this)',
                        'hint' => $this->l('Select the Image type for slider Upload/URL'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $product_type_options,
                            'id' => 'product_type_id',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select the Category'),
                        'name' => 'category_id',
                        'onchange' => 'getCategoryproducts(this)',
                        'hint' => $this->l('Select the category type'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $options_categories,
                            'id' => 'id_category_type',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select products from category'),
                        'multiple' => true,
                        'class' => 'chosen',
                        'hint' => $this->l('Select product from the list  to be shown in the special product block'),
                        'name' => 'category_products[]', // The content of the 'id' attribute of the <select> tag.
                        'id' => 'category_products', // The content of the 'id' attribute of the <select> tag.
                        'options' => array(
                            'query'=> $category_product_list_options,
                            'id' =>  'id_product',
                            'name'=>  'name'
                        )
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Select Custom Product'),
                        'multiple' => true,
                        'id' => 'product_list',
                        'class' => 'chosen',
                        'hint' => $this->l('Select product from the list  to be shown in the special product block'),
                        'name' => 'product_list[]', // The content of the 'id' attribute of the <select> tag.
                        'options' => array(
                            'query'=> $product_list_options,
                            'id' =>  'id_product',
                            'name'=>  'name'
                        )
                    ),
                    array(
                        'label' => $this->l('Number of products'),
                        'type' => 'text',
                        'hint' => $this->l('Select number of products to be shown'),
                        'class' => '',
                        'name' => 'number_of_products',
                    ),
                    array(
                        'type' => 'select',
                        'label' => $this->l('Image content Mode'),
                        'name' => 'image_content_mode',
                        'class' => 'chosen-dropdown',
                        'hint' => $this->l('Select the Activity where you have to redirect the customer after click.'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $content_mode_options,
                            'id' => 'image_contentmode_id',
                            'name' => 'name',
                        ),
                    ),
                ), 'buttons' => array(
                    array(
                    'type' =>'submit',
                    'title' => $this->l('Save'),
                    'js' => "return veloValidateProductForm(this)",
                    'class' => 'btn btn-default pull-right kb_product_setting_btn'
                    )
                ),
            )
        );
        
        $id_component = Tools::getValue('id_component');
        $languages = Language::getLanguages(false);
        $slider_field_value = array(
            'category_id' => '',
            'category_products[]' => array(),
            'product_list[]' => array(),
            'number_of_products' => 10
        );
        $sql = 'Select component_heading from  ' . _DB_PREFIX_ . 'kb_mobileapp_layout_component where id_component =' . (int) $id_component;
        $component_heading_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        $component_heading_array = Tools::unSerialize($component_heading_data);
        $slider_field_value['component_heading'] = $component_heading_array;
        $sql = 'Select * from  ' . _DB_PREFIX_ . 'kbmobileapp_product_data where id_component =' . (int) $id_component;
        $component_data = DB::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
        if (is_array($component_data)) {
            $slider_field_value['category_id'] = $component_data['id_category'];
            $slider_field_value['product_type'] = $component_data['product_type'];
            $slider_field_value['image_content_mode'] = $component_data['image_content_mode'];
            $slider_field_value['number_of_products'] = $component_data['number_of_products'];
            $custom_product_list = array();
            $category_product_list = array();
            $custom_product_list = explode(',', $component_data['custom_products']);
            $category_product_list = explode(',', $component_data['category_products']);
            $slider_field_value['product_list[]'] = $custom_product_list;
            $slider_field_value['category_products[]'] = $category_product_list;
        }
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $slider_field_value;
        $helper->name_controller = $this->name;
        $helper->table = 'product';
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $new_product_form = $helper->generateForm(array($this->slider_fields_form));
        $msg['html'] = $new_product_form;
        echo Tools::jsonEncode($msg);
        die;
    }
    public function saveGoogleData()
    {

        $error = false;
        $msg = '';
        $google_status = Tools::getValue('google_status');
        $uploadedfile = Tools::getValue('googlefilename');
        

        if ($_FILES['googlejsonfile']['size'] == 0) {
            if ($google_status == 0 || $uploadedfile != '') {
                $error = false;
                $msg = '';
            } else {
                $error = true;
                $msg = $this->l('File is empty');
            }
        } else {
            $file_mimetypes = array(
                'application/json',
                'application/x-javascript',
                'text/javascript',
                'text/x-javascript',
                'text/x-json'
            );
            if ($_FILES['googlejsonfile']['error'] > 0) {
                $error = true;
                $msg = $this->l('File has error');
            } else {
                $file_name = $_FILES['googlejsonfile']['name'];
                $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                if (Tools::strtolower($ext) != 'json') {
                    $error = true;
                    $msg = $this->l('Invalid File Format');
                } else {
                    $modified_file_name = 'googlejson.'.$ext;
                    $base_file_name = basename($file_name, $ext);
//                        $mask = _PS_MODULE_DIR_ . 'kbmobileapp/libraries/uploads/' . $base_file_name . '.*';
                    $mask = _PS_IMG_DIR_ . 'kbmobileapp/'.$base_file_name . '.*';
                    $matches = glob($mask);
                    if (count($matches) > 0) {
                        array_map('unlink', $matches);
                    }
                    $mask = _PS_IMG_DIR_ . 'kbmobileapp/googlejson.*';
                    $matches = glob($mask);
                    if (count($matches) > 0) {
                        array_map('unlink', $matches);
                    }

                    $path = _PS_IMG_DIR_ . 'kbmobileapp'  ;
                    if (!move_uploaded_file($_FILES['googlejsonfile']['tmp_name'], $path . '/' . $modified_file_name)) {
                        $error = true;
                        $msg = $this->l('Error in uploading a file');
                    } else {
                        Configuration::updateValue('KB_MOBILE_APP_GOOGLE_FILE', $modified_file_name);
                    }
                }
            }
        }
        
        $data = array('error' => $error, 'msg' => $msg);
        return $data;
    }
    
    /*
     * Functio  n definition to validate FB Key
     */
    public function getProductList()
    {
        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
                . 'p.id_product AND pl.id_lang = '
                . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1';

        $product_options = Db::getInstance()->ExecuteS($sql);
        $i = 0;
        $option1 = array();
        foreach ($product_options as $product_options) {
            $option1[$i]['id_product'] = $product_options['id_product'];
            $option1[$i]['name'] = $product_options['name'].' : '.$product_options['reference'];
            $i++ ;
        }
        return $option1;
    }
    
    public function getProductListByCategory($id_category)
    {
        
        $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
                . 'p.id_product AND pl.id_lang = '
                . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 and id_category_default = '.$id_category;

        $product_options = Db::getInstance()->ExecuteS($sql);
        $i = 0;
        $option1 = array();
        foreach ($product_options as $product_options) {
            $option1[$i]['id_product'] = $product_options['id_product'];
            $option1[$i]['name'] = $product_options['name'].' : '.$product_options['reference'];
            $i++ ;
        }
        return $option1;
    }
    public function getCategoryProductsOption($id_category)
    {
        if ($id_category) {
            $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
                . 'p.id_product AND pl.id_lang = '
                . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 and id_category_default = '.$id_category;
        } else {
            $sql = 'SELECT p.`id_product`, `reference`, pl.name
		FROM `' . _DB_PREFIX_ . 'product` p
		LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (pl.id_product = '
                . 'p.id_product AND pl.id_lang = '
                . '' . (int) Context::getContext()->language->id . Shop::addSqlRestrictionOnLang('pl') . ')
		WHERE p.active = 1 ';
        }
        
        
        $product_options = Db::getInstance()->ExecuteS($sql);
        $i = 0;
        $option1 = array();
        $available_options = '';
        $data = array();
        foreach ($product_options as $product_options) {
            $available_options .= '<option value="'.$product_options['id_product'].'">'.$product_options['name'].'</option>';
        }
        $data['category_product_options'] = $available_options;
        return $data;
    }
    public function getLayoutNameForm($id_layout)
    {
        $msg = array();
        /* Form fields for slider Setting Form */

        $this->layout_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Title'),
                        'type' => 'text',
                        'hint' => $this->l('Title for layout'),
                        'class' => '',
                        'name' => 'layout_title',
                    ),
                    array(
                        'type' => 'hidden',
                        'name' => 'layout_id',
                    ),
                ), 'buttons' => array(
                    array(
                    'type' =>'submit',
                    'title' => $this->l('Save'),
                    'js' => "return saveLayoutData(this)",
                    'class' => 'btn btn-default pull-right kb_layout_setting_btn'
                    )
                ),
            )
        );
        $layout_name = '';
        $layout_id = 0;
        if ($id_layout != 0) {
            $sql = 'Select layout_name From ' . _DB_PREFIX_ . 'kb_mobileapp_layouts where id_layout= ' .(int) $id_layout;
            $layout_name = DB::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
            $layout_id =$id_layout;
        }
        $layout_field_value = array(
            'layout_title' => $layout_name,
            'layout_id' => $layout_id,
        );
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->fields_value = $layout_field_value;
        $helper->name_controller = $this->name;
        $helper->table = 'layout';
        $languages = Language::getLanguages();
        $helper->languages = $languages;
        $helper->default_form_language = $this->context->language->id;
        $helper->title = $this->displayName;
        $helper->show_toolbar = false;
        $helper->toolbar_scroll = true;
        $helper->show_cancel_button = false;
        $helper->submit_action = 'void:javascript(0)';
        $new_product_form = $helper->generateForm(array($this->layout_form));
        $msg['html'] = $new_product_form;
        echo Tools::jsonEncode($msg);
        die;
    }
    public function validateFBKey($key)
    {
        $response = 'false';
        if (!empty($key)) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, 'https://graph.facebook.com/'.$key);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            $response = Tools::jsonDecode(curl_exec($curl));
            curl_close($curl);
            if (isset($response->error) || IS_NULL($response)) {
                $response = 'false';
            } else {
                $response = 'true';
            }
        }
        echo $response;
        die;
    }
}

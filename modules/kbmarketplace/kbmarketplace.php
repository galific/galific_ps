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

require_once dirname(__FILE__) . '/classes/kbconfiguration.php';
/**
 * The parent class KbConfiguration is extending the "Module" core class.
 * So no need to extend "Module" core class here in this class.
 */
class KbMarketPlace extends KbConfiguration
{
    private $settings = array();
    private $overrided_file = array(
        'classes/Carrier.php',
        'classes/CartRule.php',
    );

    public function __construct()
    {
        $this->name = 'kbmarketplace';
        $this->tab = 'front_office_features';
        $this->version = '1.0.2';
        $this->author = 'Knowband';
        $this->module_key = '966ef7aa9b434e67d6a01385e1767fdb';
        $this->author_address = '0x2C366b113bd378672D4Ee91B75dC727E857A54A6';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Knowband MarketPlace Free Version');
        $this->description = $this->l('Make store as marketplace where customer can also become a seller and sell their products.');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        parent::__construct();
    }

    public function getErrors()
    {
        return $this->custom_errors;
    }
    
    /*
     * This function is used just to include all the menu texts to translations files.
     */
    private function menuTranslationsIncludeFunction()
    {
        /* Module Seller Front Menu */
        $this->l('Dashboard');
        $this->l('Seller Profile');
        $this->l('Products');
        $this->l('Orders');
        $this->l('Product Reviews');
        $this->l('My Reviews');
        $this->l('Earning');
        $this->l('Transactions');
        $this->l('Payout Request');
        $this->l('Category Request');
        $this->l('Shipping');
        
        /* Module Admin MarketPlace Menu */
        $this->l('Settings');
        $this->l('Sellers List');
        $this->l('Seller Account Approval List');
        $this->l('Product Approval List');
        $this->l('Seller Products');
        $this->l('Seller Orders');
        $this->l('Admin Orders');
        $this->l('Product Reviews');
        $this->l('Seller Reviews Approval List');
        $this->l('Seller Reviews');
        $this->l('Seller Category Request List');
        $this->l('Seller Shippings');
        $this->l('Admin Commissions');
        $this->l('Seller Transactions');
        $this->l('Email Templates');
        /*Start - MK made changes on 08-03-2018 for Marketplace changes*/
        $this->l('Seller Shipping Method');
        $this->l('Transactions Payout Request');
        $this->l('GDPR Settings');
        $this->l('Seller Shop Close Request');
        $this->l('GDPR Requests');
        /*End -MK made changes on 08-03-2018 for Marketplace changes*/
    }
    
    public function install()
    {
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbEmail.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbGlobal.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerCategory.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerEarning.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerMenu.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerOrderDetail.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSeller.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerProduct.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerSetting.php';
        require_once dirname(__FILE__) . '/libraries/kbmarketplace/KbSellerShipping.php';
        
        if (version_compare(_PS_VERSION_, '1.7.0.1', '<')) {
            $this->custom_errors[] = $this->l('The plugin you are installing is compatible with prestashop 1.7 version only.');
            return false;
        }
        if (!function_exists('curl_version') || !in_array('curl', get_loaded_extensions())) {
            $this->custom_errors[] = $this->l('CURL is not enabled. Please enable it to use this module.');
            return false;
        }

        $overriding_error = false;
        foreach ($this->overrided_file as $file) {
            if (Tools::file_exists_no_cache(_PS_OVERRIDE_DIR_ .$file)) {
                $this->custom_errors[] = sprintf($this->l('%s already overridden.'), $file);
                $overriding_error = true;
            }
        }

        if ($overriding_error) {
            $this->custom_errors[] = $this->l('Override issue, please try again after clearing cache or contact to support.');
            return false;
        }

        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }
        
        $temp = false;
        $overrided_files = array();
        $source_dir = _PS_MODULE_DIR_ . 'kbmarketplace/libraries/kbmarketplace';
        foreach (Tools::scandir($source_dir) as $file) {
            if ($file != '.' && $file != '..') {
                $overrided_files[] = $file;
            }
        }
        if (empty($overrided_files)) {
            $this->custom_errors[] = $this->l('Marketplace library files are missing.');
            return false;
        } else {
            $dest_dir = _PS_OVERRIDE_DIR_ . 'classes/kbmarketplace';
            if (method_exists(get_class(new Tools()), "recurseCopy")) {
                if (Tools::recurseCopy($source_dir, $dest_dir) === false) {
                    $this->custom_errors[] = $this->l('Error occurred while copy library files in override folder.');
                    return false;
                } else {
                    Tools::chmodr($dest_dir, 0777);
                    $temp = true;
                }
            } else {
                if (self::recurseCopy($source_dir, $dest_dir) === false) {
                    $this->custom_errors[] = $this->l('Error occurred while copy library files in override folder.');
                    return false;
                } else {
                    Tools::chmodr($dest_dir, 0777);
                    $temp = true;
                }
            }
        }

        if (!Tools::file_exists_no_cache(_PS_IMG_DIR_ . $this->name . '/')) {
            if (!mkdir(_PS_IMG_DIR_ . $this->name . '/', 0777)) {
                $this->custom_errors[] = sprintf($this->l('Not able to create %s folder in image directory. Check Permission.'), 'Marketplace');
                return false;
            }
        }

        if (!Tools::file_exists_no_cache(_PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH)) {
            if (!mkdir(_PS_IMG_DIR_ . KbSeller::SELLER_PROFILE_IMG_PATH, 0777)) {
                $this->custom_errors[] = sprintf($this->l('Not able to create %s folder in image directory. Check Permission.'), KbSeller::SELLER_PROFILE_IMG_PATH);
                return false;
            }
        }
       if ($temp) {
            if (!$this->installModel()) {
                $this->custom_errors[] = $this->l('Error occurred while installing/upgrading modal.');
                return false;
            }

            if (!parent::install()) {
                $this->custom_errors[] = $this->l('Error in installing overrides same methods may be already overridden. Please try again after clearing cache or contact to support.');
                return false;
            }

            $this->installMarketPlaceTabs();

            if (Configuration::get('KB_MARKETPLACE')) {
                Configuration::deleteByName('KB_MARKETPLACE');
            }

            if (Configuration::get('KB_MP_SELLER_ORDER_HANDLING')) {
                Configuration::deleteByName('KB_MP_SELLER_ORDER_HANDLING');
            }

            $this->settings = $this->getDefaultSettings();
            Configuration::updateValue('KB_MARKETPLACE', $this->settings);

            if (!Configuration::get('KB_MP_SELLER_SHIPPING_METHOD')) {
                if (!empty($this->kbShippingMethodName())) {
                    foreach ($this->kbShippingMethodName() as $methods) {
                        Db::getInstance()->execute(
                            'INSERT INTO '._DB_PREFIX_.'kb_mp_seller_shipping_method'
                            . ' set method="'.pSQL($methods['name'])
                            .'", active='.(int)$methods['active']
                            .',date_add=now(),date_upd=now()'
                        );
                    }
                }
                Configuration::updateValue('KB_MP_SELLER_SHIPPING_METHOD', 1);
            }
            

            if (!Configuration::get('KB_MARKETPLACE_CONFIG')
                || Configuration::get('KB_MARKETPLACE_CONFIG') == '') {
                $settings = KbGLobal::getDefaultSettings();
                Configuration::updateValue('KB_MARKETPLACE_CONFIG', serialize($settings));
            }
            
            
            /*Start- MK made changes on 31-05-18 to to add column for privacy policy in seller_lang table*/
            $select_datatype = 'SELECT column_name FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'._DB_NAME_.'" AND TABLE_NAME="'._DB_PREFIX_.'kb_mp_seller_lang" AND column_name="privacy_policy"';
            $data_type = Db::getInstance()->getValue($select_datatype);
            if (empty($data_type)) {
                Db::getInstance()->execute('ALTER TABLE '._DB_PREFIX_.'kb_mp_seller_lang ADD COLUMN `privacy_policy` text');
            }
            /*End- MK made changes on 31-05-18 to to add column for privacy policy in seller_lang table*/
            Hook::exec('actionKbMarketPlaceInstall', array());

            return true;
        } else {
            return false;
        }
    }

    protected function kbShippingMethodName()
    {
        return array(
            array(
                'name' => 'USPS',
                'active' => 1,
            ),
            array(
                'name' => 'FedEx',
                'active' => 1,
            ),
            array(
                'name' => 'DHL',
                'active' => 1,
            ),
            array(
                'name' => 'UPS',
                'active' => 1,
            ),
        );
    }

    public function uninstall()
    {
        if (!parent::uninstall()) {
            return false;
        }

        $this->unInstallMarketPlaceTabs();

        Hook::exec('actionKbMarketPlaceUninstall', array());

        return true;
    }

    /**
     * Copy the folder $src into $dst, $dst is created if it do not exist
     * @param      $src
     * @param      $dst
     * @param bool $del if true, delete the file after copy
     */
    public static function recurseCopy($src, $dst, $del = false)
    {
        if (!Tools::file_exists_cache($src)) {
            return false;
        }
        $dir = opendir($src);

        if (!Tools::file_exists_cache($dst)) {
            mkdir($dst);
        }
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src.DIRECTORY_SEPARATOR.$file)) {
                    self::recurseCopy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file, $del);
                } else {
                    copy($src.DIRECTORY_SEPARATOR.$file, $dst.DIRECTORY_SEPARATOR.$file);
                    if ($del && is_writable($src.DIRECTORY_SEPARATOR.$file)) {
                        unlink($src.DIRECTORY_SEPARATOR.$file);
                    }
                }
            }
        }
        closedir($dir);
        if ($del && is_writable($src)) {
            rmdir($src);
        }
    }

    public function getContent()
    {
        $html = null;
        
        if (Tools::getIsset('KB_MARKETPLACE') && Tools::getIsset('submitMarketplaceConfiguration')) {
            Configuration::updateValue('KB_MARKETPLACE', Tools::getValue('KB_MARKETPLACE'));

            $html .= $this->displayConfirmation($this->l('Configuration has been saved successfully.'));
        }
        return $html . $this->renderConfigurationHtml();
    }

    private function renderConfigurationHtml()
    {
        $fields_form_1 = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Configuration'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'is_bool' => true, //retro compat 1.5
                        'label' => '<strong>' . $this->l('Enable') . ':</strong>',
                        'name' => 'KB_MARKETPLACE',
                        'desc' => $this->l('This setting will enable/disable entire marketplace working except earning of you and sellers on order placing.'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            )
                        ),
                    ),
                    array(
                        'label' => $this->l('Custom CSS'),
                        'type' => 'textarea',
                        'hint' => $this->l('Enter custom CSS code for marketplace'),
                        'class' => 'free-disabled',
                        'disabled' => true,
                        'name' => 'KB_MARKETPLACE_CSS',
                    ),
                    array(
                        'label' => $this->l('Custom JS'),
                        'type' => 'hidden',
                        'hint' => $this->l('Enter custom JS code for marketplace'),
                        'class' => '',
                        'name' => 'KB_MARKETPLACE_JS',
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submitMarketPlaceConfiguration',
                )
            ),
        );
        $this->context->controller->addJs($this->_path . 'views/js/admin/mpconfigure.js');
        $this->context->controller->addCSS($this->_path . 'views/css/admin/mpconfigure.css');
        
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->module = $this;
        $helper->submit_action = 'submitMarketplaceConfiguration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigurationFieldValues()
        );

        return $helper->generateForm(array($fields_form_1));
    }

    public function hookDisplayBackOfficeHeader()
    {
        $controller = $this->context->controller->controller_name;
        if (($controller == 'AdminCarriers'
            || $controller == 'AdminCarrierWizard')
            && isset($this->context->cookie->kbcarrierredirect)
            && $this->context->cookie->kbcarrierredirect) {
            $msg = $this->l('You do not have permission to update seller carriers.');
            $this->context->controller->errors[] = $msg;
            unset($this->context->cookie->kbcarrierredirect);
        }
        $this->context->controller->addCSS($this->_path . parent::CSS_ADMIN_PATH . 'kb-marketplace.css');
    }

    public function hookDisplayAdminCustomersForm($param)
    {
        unset($param);
        return $this->renderSellerSettingForm();
    }
    
    public function hookDisplayCustomerAccountFormTop($params = array())
    {
        if (isset($this->context->customer->id) && $this->context->customer->id > 0) {
            if (KbSeller::getSellerByCustomerId($this->context->customer->id)) {
                return '';
            }
        }
        if (Configuration::get('KB_MARKETPLACE') !== false
            && Configuration::get('KB_MARKETPLACE') == 1) {
                $context = $this->context;
                $agreement_txt = '';
                $context->smarty->assign(
                    array('kb_seller_agreement' => $agreement_txt)
                );
                return $this->display(__FILE__, 'views/templates/hook/account_form_registration.tpl');
            
        }
    }

    public function hookAdditionalCustomerFormFields($params = array())
    {
        if (isset($this->context->customer->id) && $this->context->customer->id > 0) {
            if (KbSeller::getSellerByCustomerId($this->context->customer->id)) {
                return array();
            }
        }
        if (Configuration::get('KB_MARKETPLACE') !== false
            && Configuration::get('KB_MARKETPLACE') == 1) {
            $mp_config = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            if (isset($mp_config['kbmp_seller_registration']) && $mp_config['kbmp_seller_registration'] == 1) {
                $context = $this->context;
                $label = $this->l('Also register me as seller');
                $fields = array();
                $form_field = new FormField;
                $form_field->setName('kbmp_registered_as_seller');
                $form_field->setType('checkbox');
                $form_field->setLabel($label);
                $fields[] = $form_field;
                return $fields;
            }
        }
        return array();
    }

    public function hookActionCustomerAccountAdd($param)
    {
        if (Configuration::get('KB_MARKETPLACE') !== false
            && Configuration::get('KB_MARKETPLACE') == 1) {
            $do_register = Tools::getValue('kbmp_registered_as_seller', false);
            if ($do_register) {
                $new_customer = $param['newCustomer'];
                $seller = new KbSeller();
                $seller->id_customer = $new_customer->id;
                $seller->id_shop = $new_customer->id_shop;
                $seller->id_default_lang = $new_customer->id_lang;
                $seller->approved = KbGlobal::APPROVED;
                $seller->active = KbGlobal::ENABLE;
                $seller->deleted = 0;
                $seller->notification_type = (string)KbSeller::NOTIFICATION_PRIMARY;
                $seller->product_limit_wout_approval = 0;
                $seller->approval_request_limit = 0;

                if ($seller->save(true)) {
                    $data = array(
                        'email' => $new_customer->email,
                        'name' => $new_customer->firstname . ' ' . $new_customer->lastname
                    );
                    $email = new KbEmail(KbEmail::getTemplateIdByName('mp_welcome_seller'), $new_customer->id_lang);
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
                    
                    Hook::exec('actionKbMarketPlaceCustomerRegistration', array('seller' => $seller));
                }
            }
        }
    }
    
    public function hookDisplayNav1()
    {
        if (Configuration::get('KB_MARKETPLACE') !== false
            && Configuration::get('KB_MARKETPLACE') == 1) {
            $kb_displaynav1_links = array();
                $seller_list_link = $this->context->link->getModuleLink(
                    $this->name,
                    'sellerfront',
                    array(),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $kb_displaynav1_links[] = array(
                    'href' => $seller_list_link,
                    'label' => $this->l('Sellers'),
                    'title' => $this->l('Click to view all sellers')
                );
                $this->context->smarty->assign('kb_displaynav1_links', $kb_displaynav1_links);
                return $this->display(__FILE__, 'views/templates/hook/display_nav1.tpl');
            
        }
    }

    public function hookDisplayNav2()
    {
        if (Configuration::get('KB_MARKETPLACE') !== false
            && Configuration::get('KB_MARKETPLACE') == 1) {
            if ($this->showTopMenuLink()) {
                $menus = array();
                $enabled_menu = array('dashboard','seller','product','order','shipping','earning');
                foreach (KbSellerMenu::getAllMenus($this->context->language->id) as $menu) {
                    if (in_array($menu['controller_name'],$enabled_menu)) {
                        $menus[] = array(
                            'label' => $this->l($menu['label']),
                            'title' => $this->l($menu['title']),
                            'href' => $this->context->link->getModuleLink(
                                $menu['module_name'],
                                $menu['controller_name'],
                                array(),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        );
                    }
                }
                $this->context->smarty->assign('seller_account_menus', $menus);
                $menu = KbSellerMenu::getMenusByModuleAndController(
                    'kbmarketplace',
                    'dashboard',
                    $this->context->language->id
                );
                $seller_account_link = $this->context->link->getModuleLink(
                    $menu['module_name'],
                    $menu['controller_name'],
                    array(),
                    (bool)Configuration::get('PS_SSL_ENABLED')
                );
                $this->context->smarty->assign('seller_account_link', $seller_account_link);
            }

            $custom_css = '';
            $custom_js = '';
            $this->context->smarty->assign('PS_ALLOW_ACCENTED_CHARS_URL', (int)Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL'));

            return $this->display(__FILE__, 'views/templates/hook/top_menu_link.tpl');
        }
    }

    public function hookDisplayCustomerAccount($params)
    {
        if (Configuration::get('KB_MARKETPLACE') !== false
            && Configuration::get('KB_MARKETPLACE') == 1) {
            if (!$this->showTopMenuLink()
                && Tools::getIsset('register_as_seller')
                && (int)Tools::getValue('register_as_seller') == 1) {
                $customer = new Customer((int)$this->context->customer->id);
                $seller = new KbSeller();
                $seller->id_customer = $customer->id;
                $seller->id_shop = $customer->id_shop;
                $seller->id_default_lang = $customer->id_lang;
                $seller->approved = KbGlobal::APPROVED;
                $seller->active = KbGlobal::ENABLE;
                $seller->deleted = 0;
                $seller->notification_type = (string)KbSeller::NOTIFICATION_PRIMARY;
                $seller->product_limit_wout_approval = 0;
                $seller->approval_request_limit = 0;

                if ($seller->save(true)) {
                    $this->context->smarty->assign('account_created', true);
                    $data = array(
                        'email' => $customer->email,
                        'name' => $customer->firstname . ' ' . $customer->lastname
                    );

                    $email = new KbEmail(KbEmail::getTemplateIdByName('mp_welcome_seller'), $customer->id_lang);
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
                    
                    Hook::exec('actionKbMarketPlaceCustomerRegistration', array('seller' => $seller));

                    Tools::redirect('my-account');
                }
            }

            if ($this->showTopMenuLink()) {
                $menus = array();
                $enabled_menu = array('dashboard','seller','product','order','shipping','earning');
                foreach (KbSellerMenu::getAllMenus($this->context->language->id) as $menu) {
                    if (in_array($menu['controller_name'],$enabled_menu)) {
                        $menus[] = array(
                            'label' => $this->l($menu['label']),
                            'icon_class' => $menu['icon'],
                            'title' => $this->l($menu['title']),
                            'href' => $this->context->link->getModuleLink(
                                $menu['module_name'],
                                $menu['controller_name'],
                                array(),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        );
                    }
                }

                $this->context->smarty->assign('menus', $menus);
            } else {
                $show_registration_link = KbGlobal::getGlobalSettingByKey('kbmp_seller_registration');
                if ($show_registration_link) {
                    $mp_config = Tools::unserialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
                    $context = $this->context;
                        $context->smarty->assign(
                            array('kb_seller_agreement' => '')
                        );
                    
                    
                    $link_to_register = $this->context->link->getPageLink(
                        'my-account',
                        (bool)Configuration::get('PS_SSL_ENABLED'),
                        null,
                        array('register_as_seller' => 1)
                    );
                    $this->context->smarty->assign('link_to_register', $link_to_register);
                }
            }

            return $this->display(__FILE__, 'views/templates/hook/seller_menus.tpl');
        }
    }

    
    public function hookDisplayBackOfficeFooter()
    {
        if (Tools::getIsset('controller') && Tools::getIsset('id_product')) {
            $controller_name = Tools::getValue('controller');
            if ($controller_name == 'AdminProducts') {
                $id_product = (int)Tools::getValue('id_product');
                if ($id_product > 0) {
                    $carrier_list = array();
                    if ($id_seller = KbSellerProduct::getSellerIdByProductId($id_product)) {
                        $carrier_list = KbSellerShipping::getShippingForProducts(
                            $this->context->language->id,
                            $id_seller,
                            false,
                            false,
                            false,
                            false,
                            Carrier::ALL_CARRIERS,
                            true
                        );
                    } else {
                        $carrier_list = KbSellerShipping::getShippingForProducts(
                            $this->context->language->id,
                            0,
                            true,
                            false,
                            false,
                            false,
                            Carrier::ALL_CARRIERS,
                            true
                        );
                    }
                    if (!empty($carrier_list)) {
                        $product = new Product($id_product);
                        $carrier_selected_list = $product->getCarriers();
                        foreach ($carrier_list as &$carrier) {
                            foreach ($carrier_selected_list as $carrier_selected) {
                                if ($carrier_selected['id_reference'] == $carrier['id_reference']) {
                                    $carrier['selected'] = true;
                                    continue;
                                }
                            }
                        }
                        $this->context->smarty->assign('kb_avail_carrier_list', $carrier_list);
                        return $this->display(__FILE__, 'views/templates/hook/carrier_list_to_admin.tpl');
                    }
                }
            }
        }
        return '';
    }
}

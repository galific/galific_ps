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

require_once dirname(__FILE__).'/AdminKbMarketplaceCoreController.php';
require_once(_PS_MODULE_DIR_.'kbmarketplace/libraries/kbmarketplace/KbGlobal.php');

class AdminKbMarketPlaceSettingController extends AdminKbMarketplaceCoreController
{

    public function __construct()
    {
        $this->table   = 'kb_mp_seller_config';
        $this->display = 'edit';
        parent::__construct();

        $this->fields_form = array(
            'tinymce' => true,
            'input' => array(
                array(
                    'type' => 'text',
                    'suffix' => '%',
                    'label' => $this->module->l('Default Commission', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_default_commission',
                    'required' => true,
                    'validation' => 'isPercentage',
                    'class' => 'fixed-width-xs',
                    'hint' => $this->module->l('Only numerical or decimal values are allowed', 'adminkbmarketplacesettingcontroller'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Approval Request Limit', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_approval_request_limit',
                    'required' => true,
                    'validation' => 'isInt',
                    'disabled' => false,
                    'class' => '',
                    'hint' => $this->module->l('Only Numeric values are allowed', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('Maximum number of request seller can make for approving account after disapproving. This limit will be set for seller after registration with his account and cannot be changed later.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('New Product Limit', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_product_limit',
                    'disabled' => false,
                    'class' => 'freedisabled',
                    'hint' => $this->module->l('Only Numeric values are allowed', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('After this limit, seller cannot add new products until he/she will not be approved by you.', 'adminkbmarketplacesettingcontroller'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable Seller Registration', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_seller_registration',
                    'required' => false,
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'seller_registration_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'seller_registration_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                    'hint' => $this->module->l('Allow new or existing (who is not seller), customer to register as seller on store', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('New Product Approval Required', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_new_product_approval_required',
                    'required' => true,
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'new_product_approval_required_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'new_product_approval_required_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                    'hint' => $this->module->l('New product needs approval from your side before display on front.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Send email to seller on order place', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_email_on_new_order',
                    'required' => false,
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'email_on_new_order_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'email_on_new_order_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                    'hint' => $this->module->l('With this setting, system will send email to seller on new order', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable Seller Review', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_enable_seller_review',
                    'required' => false,
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'enable_seller_review_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'enable_seller_review_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                    'hint' => $this->module->l('Enable customers to give his reviews on seller.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Seller Review Approval Required', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_seller_review_approval_required',
                    'required' => false,
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'seller_review_approval_required_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'seller_review_approval_required_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                    'hint' => $this->module->l('With this setting, review first needs approval by you before showing to customers.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display sellers on front', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_show_seller_on_front',
                    'required' => false,
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'show_seller_on front_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'show_seller_on front_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                    'hint' => $this->module->l('With this setting, customers can view the sellers list as well as their profile.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Allow Order Handling', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_enable_seller_order_handling',
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Sellers to handle orders.', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('This setting will enable/disable sellers to change status, ship, invoice printing of his own orders(order having own products).', 'adminkbmarketplacesettingcontroller'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Allow Free Shipping', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_enable_free_shipping',
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Customer to add free shipping voucher', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('This setting will allow/disallow to use free shipping voucher.', 'adminkbmarketplacesettingcontroller'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display Product Wise Seller details on success', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_enable_seller_order_details',
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Customer to see product wise seller details on order confirmation page', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('This setting will hide/show Seller details on success.', 'adminkbmarketplacesettingcontroller'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Display Seller details on product page', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_enable_seller_details',
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Customer to see seller details on product page', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('This setting will hide/show seller detail on product page.', 'adminkbmarketplacesettingcontroller'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                ),
                /*Start - MK made changes on 08-03-2018 for Marketplace changes*/
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Allow Seller to define own Custom Shipping Method', 'adminkbmarketplacesettingcontroller'),
                    'name' => 'kbmp_enable_seller_custom_shipping',
                    'disabled' => false,
                    'class' => 'free-disabled',
                    'is_bool' => true,
                    'hint' => $this->module->l('Allow Seller to add custom shipping method on Shipping page', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('This setting will be used to enable/disable custom shipping method on Shipping page.', 'adminkbmarketplacesettingcontroller'),
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enable', 'adminkbmarketplacesettingcontroller')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disable', 'adminkbmarketplacesettingcontroller')
                        )
                    ),
                ),
                /*End -MK made changes on 08-03-2018 for Marketplace changes*/
                array(
                    'type' => 'tags',
                    'class' => '',
                    'label' => $this->module->l('Listing Meta Keywords', 'adminkbmarketplacesettingcontroller'),
                    'disabled' => false,
                    'name' => 'kbmp_seller_listing_meta_keywords',
                    'hint' => $this->module->l('Set the keywords/tags for seller listing page on front.', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('Set the comma seperated keywords by which customer can search your seller listing page via search engines. Comma is mandatory even if your are adding only one tag. Ex-: tag1,', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Listing Meta Description', 'adminkbmarketplacesettingcontroller'),
                    'rows' => 5,
                    'disabled' => false,
                    'class' => '',
                    'name' => 'kbmp_seller_listing_meta_description',
                    'hint' => $this->module->l('Set the description for seller listing page on front.', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('Set the description for seller listing page on front.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'textarea',
                    'lang' => true,
                    'label' => $this->module->l('Seller Agreement', 'adminkbmarketplacesettingcontroller'),
                    'disabled' => false,
                    'class' => '',
                    'name' => 'kbmp_seller_agreement',
                    'hint' => $this->module->l('Leave blank, if you dont want.', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('Set the agreement which seller accept before registering on marketplace.', 'adminkbmarketplacesettingcontroller')
                ),
                array(
                    'type' => 'textarea',
                    'lang' => true,
                    'label' => $this->module->l('Order Email Template', 'adminkbmarketplacesettingcontroller'),
                    'autoload_rte' => true,
                    'name' => 'kbmp_seller_order_email_template',
                    'disabled' => false,
                    'hint' => $this->module->l('This template will used to send order detail to seller, if his product is ordered.', 'adminkbmarketplacesettingcontroller'),
                    'desc' => $this->module->l('Keywords like {{sample}} will be replace by dynamic content at the time of execution. Please do not remove these type of words from template, otherwise proper information will not be send in email to seller as well you. You can only change the position of these keywords in the template.', 'adminkbmarketplacesettingcontroller')
                ),
            ),
            'submit' => array('title' => $this->module->l('Save', 'adminkbmarketplacesettingcontroller')),
            'reset' => array('title' => $this->module->l('Reset', 'adminkbmarketplacesettingcontroller'), 'icon' => 'process-icon-reset')
        );

        $this->show_form_cancel_button = false;
        $this->submit_action           = 'submitMarketPlaceConfiguration';
    }

    public function initContent()
    {
        if (!Configuration::get('KB_MARKETPLACE_CONFIG') || Configuration::get('KB_MARKETPLACE_CONFIG')
            == '') {
            $settings = KbGLobal::getDefaultSettings();
        } else {
            $settings = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
        }

        $category_array = $settings['kbmp_allowed_categories'] ;


        $root = Category::getRootCategory();
        $tree = new HelperTreeCategories('kbmp-categories-tree');
        $tree->setRootCategory($root->id)
            ->setInputName('kbmp_allowed_categories')
            ->setUseCheckBox(true)
            ->setUseSearch(false)
            ->setSelectedCategories((array) $category_array);

//        $this->fields_form['input'][] = array(
//            
//            'type' => 'categories_select',
//            'label' => $this->module->l('Categories Allowed', 'adminkbmarketplacesettingcontroller'),
//            'category_tree' => $tree->render(),
//            'class' => 'free-disabled',
//            'name' => 'kbmp_allowed_categories',
//            'hint' => array(
//                $this->module->l('Categories to be allowed to seller in which he/she can map his/her products.', 'adminkbmarketplacesettingcontroller'),
//                $this->module->l('If no category is selected that will mean that all the categories are allowed.', 'adminkbmarketplacesettingcontroller')
//            ),
//            'desc' => $this->module->l('If no category is selected that will mean that all the categories are allowed. In order to enable a category you will have to check all the parent categories otherwise the category will not be activated. Example- To enable `T-shirts` category, you will have to check all the parent categories i.e. Home, Women, Tops and ofcourse T-shirts.', 'adminkbmarketplacesettingcontroller')
//        );


        parent::initContent();
        $this->context->smarty->assign(array(
            'title' => $this->module->l('MarketPlace General Settings', 'adminkbmarketplacesettingcontroller'),
        ));
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJqueryUI('ui.widget');
        $this->context->controller->addJqueryPlugin('tagify');
        $this->context->controller->addJs($this->getKbModuleDir(). 'views/js/admin/mpconfigure.js');
        $this->context->controller->addCSS($this->getKbModuleDir(). 'views/css/admin/mpconfigure.css');
        
    }

    public function renderForm()
    {
        $this->context->controller->addJs(_PS_MODULE_DIR_ . $this->module->name . 'views/js/admin/mpconfigure.js');
        $this->context->controller->addCSS(_PS_MODULE_DIR_ . $this->module->name . 'views/css/admin/mpconfigure.css');
        
        $form = parent::renderForm();
        $tpl  = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_.$this->kb_module_name.'/views/templates/admin/setting.tpl'
        );
        /*Start- MK made changes on 30-05-18 to display the tabs*/
        $this->context->smarty->assign(
            array(
                'selected_nav' => 'config',
                'gdpr_setting'=>  $this->context->link->getAdminLink('AdminKbMPGDPRSetting', true),
                'mp_setting'=>  $this->context->link->getAdminLink('AdminKbMarketPlaceSetting', true),
            )
        );
        $tpl->assign(
            'kb_tabs',
            $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/kb_tabs.tpl'
            )
        );
        /*End- MK made changes on 30-05-18 to display the tabs*/
        $tpl->assign('form_fields', $form);
        $tpl2 = $this->custom_smarty->createTemplate('free_block.tpl');
        $content= $tpl2->fetch();
        return $tpl->fetch().$content;
        
    }

    public function initProcess()
    {
        if (Tools::isSubmit('submitMarketPlaceConfiguration')) {
            $this->action = 'MarketPlaceSetting';
        }
    }

    public function processMarketPlaceSetting()
    {
        $mp_config = array();
        if (Tools::getIsset('kbmp_reset_setting') && Tools::getValue('kbmp_reset_setting')
            == 1) {
            $mp_config = KbGLobal::getDefaultSettings();
            $this->displayWarning($this->module->l('Please click on "Save" button to keep default settings (settings shown below), otherwise previously saved values will be kept.', 'adminkbmarketplacesettingcontroller'));
            return;
        } else {
            $default_settings = KbGLobal::getDefaultSettings();
            $this->getLanguages();
            foreach ($this->fields_form['input'] as $field) {
                $error = false;
                
                if (isset($field['lang']) && $field['lang']) {
                    $lang_data = $default_settings[$field['name']];
                    foreach ($this->_languages as $language) {
                        $lang_data[$language['id_lang']] = '';
                        if ($field['name'] == 'kbmp_seller_order_email_template') {
                            $lang_data[$language['id_lang']] = '';
                        } else {
                            $lang_data[$language['id_lang']] = '';
                        }
                        
                    }
                    $mp_config[$field['name']] = $lang_data;
//                    echo $field['name']."  "; 
//                    print_r($mp_config[$field['name']]);
                    
                } elseif (Tools::getIsset($field['name'])) {
                    if (isset($field['required']) && $field['required']) {
                        if (($value = Tools::getValue($field['name'])) == false && (string) $value
                            != '0') {
                            $error          = true;
                            $this->errors[] = Tools::displayError(sprintf($this->module->l('Field %s is required.', 'adminkbmarketplacesettingcontroller'), $field['label']));
                        } elseif (isset($field['validation']) && !call_user_func(array(
                                "Validate", $field['validation']), Tools::getValue($field['name']))) {
                            $error          = true;
                            $this->errors[] = Tools::displayError(sprintf($this->module->l('Field %s is invalid.', 'adminkbmarketplacesettingcontroller'), $field['label']));
                        }
                    } elseif (isset($field['validation']) && !call_user_func(array(
                            "Validate", $field['validation']), Tools::getValue($field['name']))) {
                        $error          = true;
                        $this->errors[] = Tools::displayError(sprintf($this->module->l('Field %s is invalid.', 'adminkbmarketplacesettingcontroller'), $field['label']));
                    }
                    if (!$error) {
                        if ($field['type'] && isset($field['multiple']) && $field['multiple']) {
                            
                            $mp_config[$field['name']] = Tools::getValue('selectItem'.$field['name']);
                        } else {
                            $mp_config[$field['name']] = Tools::getValue($field['name']);
                        }
                    }
                } else {
                    $mp_config[$field['name']] = $default_settings[$field['name']];
                }
            }
            $mp_config['kbmp_seller_listing_meta_description'] = '';
            
            $mp_config['kbmp_allowed_categories'] = $default_settings['kbmp_allowed_categories'];
            }
        if (!$this->errors || count($this->errors) == 0) {
            $this->confirmations[] = $this->_conf[6];
            Configuration::updateValue('KB_MARKETPLACE_CONFIG', serialize($mp_config));
            Hook::exec('actionMarketplaceSetting', array('controller' => $this, 'settings' => $mp_config));
        }
    }

    public function getFieldsValue($obj)
    {
        unset($obj);

        if (Tools::getIsset('kbmp_reset_setting') && Tools::getValue('kbmp_reset_setting')
            == 1) {
            $this->fields_value = KbGLobal::getDefaultSettings();
            return $this->fields_value;
        }
        if (!Configuration::get('KB_MARKETPLACE_CONFIG') || Configuration::get('KB_MARKETPLACE_CONFIG')
            == '') {
                $settings = KbGLobal::getDefaultSettings();
            } else {
                $settings = Tools::unSerialize(Configuration::get('KB_MARKETPLACE_CONFIG'));
            }
            return $settings;
        }
    }

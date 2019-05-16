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
 */

require_once dirname(__FILE__).'/AdminKbMarketplaceCoreController.php';

class AdminKbMPGDPRSettingController extends AdminKbMarketplaceCoreController
{

    public function __construct()
    {
        $this->table   = 'Configuration';
        $this->display = 'edit';
        parent::__construct();

        $this->fields_form = array(
            'tinymce' => false,
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable GDPR', 'AdminKbMPGDPRSettingController'),
                    'name' => 'enable_gdpr',
                    'required' => false,
                    'disabled' => false,
                    'class' => '',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'value' => 1,
                        ),
                        array(
                            'value' => 0,
                        )
                    ),
                    'hint' => $this->module->l('Enable/Disable this setting to display GDPR in frontend', 'AdminKbMPGDPRSettingController')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable Display Customer ID to seller', 'AdminKbMPGDPRSettingController'),
                    'name' => 'enable_customer_id',
                    'required' => false,
                    'disabled' => false,
                    'class' => '',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'value' => 1,
                        ),
                        array(
                            'value' => 0,
                        )
                    ),
                    'hint' => $this->module->l('Enable/Disable this setting to display customer email to the Seller', 'AdminKbMPGDPRSettingController')
                ),
               array(
                    'type' => 'switch',
                    'label' => $this->module->l('Option to Close Shop', 'AdminKbMPGDPRSettingController'),
                    'name' => 'enable_close_shop',
                    'required' => false,
                    'disabled' => false,
                    'class' => '',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'value' => 1,
                        ),
                        array(
                            'value' => 0,
                        )
                    ),
                    'hint' => $this->module->l('Enable/Disable this setting to display the option to close shop to the seller', 'AdminKbMPGDPRSettingController')
                ),
            ),
            'submit' => array('title' => $this->module->l('Save', 'AdminKbMPGDPRSettingController')),
        );

        $this->show_form_cancel_button = false;
        $this->submit_action = 'submitMPGDPRConfiguration';
        
        
    }

    public function initContent()
    {
        parent::initContent();
        
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
        $this->context->controller->addJqueryUI('ui.widget');
        $this->context->controller->addJqueryPlugin('tagify');
        
    }

    public function renderForm()
    {
        
        $form = parent::renderForm();
        $tpl  = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_.$this->kb_module_name.'/views/templates/admin/setting.tpl'
        );
        $this->context->smarty->assign(array(
            'selected_nav' => 'gdpr_config',
            'gdpr_setting'=>  $this->context->link->getAdminLink('AdminKbMPGDPRSetting', true),
            'mp_setting'=>  $this->context->link->getAdminLink('AdminKbMarketPlaceSetting', true),
        ));
        $tpl->assign(
            'kb_tabs',
            $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->module->name . '/views/templates/admin/kb_tabs.tpl'
            )
        );
        $tpl->assign('form_fields', $form);
        
        $tpl2 = $this->custom_smarty->createTemplate('free_block.tpl');
        $content= $tpl2->fetch();
        
        
        return $tpl->fetch().$content;
    }

    public function initProcess()
    {
        
    }

    public function processMPGDPRSetting()
    {
            $this->confirmations[] = $this->_conf[6];
    }

}

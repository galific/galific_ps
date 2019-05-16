<?php
/**
*  @author ST-themes https://www.sunnytoo.com
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class StRegistration extends Module
{
    public $_html = '';
    public $fields_form;
    public $fields_value;
    public $validation_errors = array();

    public function __construct()
    {
        $this->name          = 'stregistration';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.3';
        $this->author        = 'sunnytoo.com';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('Custom fields for registration by ST-themes');
        $this->description = $this->l('Add custom fields to registration form.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        $result = true;
        if (!parent::install()
            || !Configuration::updateValue('ST_REG_TERMS_AND_CONDITIONS', 0)
            || !Configuration::updateValue('ST_REG_TERMS_AND_CONDITIONS_PAGE', 0)
            || !Configuration::updateValue('ST_REG_CUSTOM_CONTENT', '')
            || !Configuration::updateValue('ST_REG_CONFIRM_EMAIL', 0)
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayBeforeBodyClosingTag')
            || !$this->registerHook('displayCustomerAccountForm')
            || !$this->registerHook('additionalCustomerFormFields')
            // || !$this->registerHook('actionCustomerAccountAdd')
        ) {
             $result = false;
        }
        
        return $result;
    }
    
    public function uninstall()
    {
        if (!parent::uninstall()
        ) {
            return false;
        }
        return true;
    }
    public function getContent()
    {
        $this->initFieldsForm();
        if (isset($_POST['savestregistration']))
        {
            foreach($this->fields_form as $form)
                foreach($form['form']['input'] as $field)
                    if(isset($field['validation']))
                    {
                        $ishtml = ($field['validation']=='isAnything') ? true : false;
                        $errors = array();       
                        $value = Tools::getValue($field['name']);
                        if (isset($field['required']) && $field['required'] && $value==false && (string)$value != '0')
                                $errors[] = sprintf(Tools::displayError('Field "%s" is required.'), $field['label']);
                        elseif($value)
                        {
                            $field_validation = $field['validation'];
                            if (!Validate::$field_validation($value))
                                $errors[] = sprintf(Tools::displayError('Field "%s" is invalid.'), $field['label']);
                        }
                        // Set default value
                        if ($value === false && isset($field['default_value']))
                            $value = $field['default_value'];
                        
                        if(count($errors))
                        {
                            $this->validation_errors = array_merge($this->validation_errors, $errors);
                        }
                        elseif($value==false)
                        {
                            switch($field['validation'])
                            {
                                case 'isUnsignedId':
                                case 'isUnsignedInt':
                                case 'isInt':
                                case 'isBool':
                                    $value = 0;
                                break;
                                default:
                                    $value = '';
                                break;
                            }
                            Configuration::updateValue('ST_REG_'.strtoupper($field['name']), $value);
                        }
                        else
                            Configuration::updateValue('ST_REG_'.strtoupper($field['name']), $value, $ishtml);
                    }
                                                 
            if(count($this->validation_errors))
                $this->_html .= $this->displayError(implode('<br/>',$this->validation_errors));
            else 
                $this->_html .= $this->displayConfirmation($this->l('Settings updated'));
            $this->_clearCache('*');
        }

        $helper = $this->initForm();
        
        return $this->_html.$helper->generateForm($this->fields_form).'<div class="alert alert-info">This free module was created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>, it\'s not allow to sell it, it\'s also not allow to create new modules based on this one. Check more <a href="https://www.sunnytoo.com/blogs?term=743&orderby=date&order=desc" target="_blank">free modules</a>, <a href="https://www.sunnytoo.com/product-category/prestashop-modules" target="_blank">advanced paid modules</a> and <a href="https://www.sunnytoo.com/product-category/prestashop-themes" target="_blank">themes(transformer theme and panda  theme)</a> created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>.</div>';
    }
    public function getCMSpages()
    {
        $cms_tab = array();
        foreach (CMS::listCms($this->context->language->id) as $cms_file) {
            $cms_tab[] = array('id' => $cms_file['id_cms'], 'name' => $cms_file['meta_title']);
        }
        return $cms_tab;
    }
    protected function initFieldsForm()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Terms and conditions'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->l('Added a terms and conditions check on registration form:'),
                    'name' => 'terms_and_conditions',
                    'is_bool' => true,
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'terms_and_conditions_on',
                            'value' => 1,
                            'label' => $this->l('Yes')),
                        array(
                            'id' => 'terms_and_conditions_checked',
                            'value' => 2,
                            'label' => $this->l('Yes, checked by default')),
                        array(
                            'id' => 'terms_and_conditions_off',
                            'value' => 0,
                            'label' => $this->l('No')),
                    ),
                    'desc' => $this->l('Users have to agree the terms and conditions to register.'),
                    'validation' => 'isUnsignedInt',
                ), 

                array(
                    'type' => 'select',
                    'label' => $this->l('Page for the Terms and conditions'),
                    'name' => 'terms_and_conditions_page',
                    'options' => array(
                        'query' => $this->getCMSpages(),
                        'id' => 'id',
                        'name' => 'name',
                        'default' => array(
                            'value' => 0,
                            'label' => $this->l('None')
                        ),
                    ),
                    'validation' => 'isUnsignedInt',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l('Custom content:'),
                    'name' => 'custom_content',
                    'cols' => 80,
                    'rows' => 12,
                    'validation' => 'isAnything',
                    'desc' => array(
                        $this->l('1. Code here will be injected to the registration form.'),
                        $this->l('2. HTML codes allowed. Turn off the "Use HTMLPurifier Library" setting on the Shop parameters > General page if you want to put html codes into this field.'),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Added a Confirm Email field:'),
                    'name' => 'confirm_email',
                    'is_bool' => true,
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'confirm_email_on',
                            'value' => 1,
                            'label' => $this->l('Yes')),
                        array(
                            'id' => 'confirm_email_off',
                            'value' => 0,
                            'label' => $this->l('No')),
                    ),
                    'desc' => $this->l('Users have to fill in the Confirm Email field with the exactly same address as the Email field to make sure the email address is correct.'),
                    'validation' => 'isUnsignedInt',
                ), 
            ),
            'submit' => array(
                'title' => $this->l('   Save   ')
            )
        );
    }
    protected function initForm()
    {
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table =  $this->table;
        $helper->module = $this;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'savestregistration';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id
        );
        return $helper;
    }
    
    private function getConfigFieldsValues()
    {
        $fields_values = array(
            'terms_and_conditions' => Configuration::get('ST_REG_TERMS_AND_CONDITIONS'),
            'terms_and_conditions_page' => Configuration::get('ST_REG_TERMS_AND_CONDITIONS_PAGE'),
            'custom_content' => Configuration::get('ST_REG_CUSTOM_CONTENT'),
            'confirm_email' => Configuration::get('ST_REG_CONFIRM_EMAIL'),
        );
        
        return $fields_values;
    }
    public function hookDisplayHeader($params)
    {
        if (array_key_exists('register_form', $this->context->smarty->getTemplateVars())) {
            $this->context->smarty->getTemplateVars('urls');
        }
        
        
        $this->context->controller->addJS($this->_path.'views/js/stregistration.js');
    }
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        return $this->fetch('module:stregistration/views/templates/hook/modal.tpl');
    }
    public function hookDisplayCustomerAccountFormTop($params)
    {
        return $this->hookDisplayCustomerAccountForm($params);
    }
    public function hookDisplayCustomerAccountForm($params)
    {
        if (!$this->isCached('module:stregistration/views/templates/hook/form.tpl', $this->getCacheId())) {
            $format = array();
            $terms_and_conditions = (int)Configuration::get('ST_REG_TERMS_AND_CONDITIONS');
            if($terms_and_conditions){
                $opening_a = $closeing_a = '';
                if($cms_id = Configuration::get('ST_REG_TERMS_AND_CONDITIONS_PAGE')){
                    $cms = new CMS($cms_id, $this->context->language->id);
                    $opening_a = '<a href="'.$this->context->link->getCMSLink($cms, $cms->link_rewrite, (bool) Configuration::get('PS_SSL_ENABLED')).'" class="st_reg_terms_link">';
                    $closeing_a = '</a>';
                }
                $format['st_reg_terms_and_conditions'] = array(
                    'name' => 'st_reg_terms_and_conditions',
                    'type' => 'checkbox',
                    'label' => sprintf($this->l('I agree to the %s terms of service %s and will adhere to them unconditionally.'), $opening_a, $closeing_a),
                    'value' => $terms_and_conditions==2 ? 1 : 0,
		    'required' => true,
		    'disabled' => true,
                    'errors' => array(),
                );
            }
            $this->context->smarty->assign(array(
                'st_reg_form' => $format,
                'st_reg_custom_content' => Configuration::get('ST_REG_CUSTOM_CONTENT'),
            ));
        }
        return $this->fetch('module:stregistration/views/templates/hook/form.tpl', $this->getCacheId());
    }

    public function hookAdditionalCustomerFormFields($params)
    {
        if(!Configuration::get('ST_REG_CONFIRM_EMAIL'))
            return;

        $formField = new FormField();
        $formField->setName('confirm_email');
        $formField->setType('email');
        $formField->setLabel($this->l('Confirm Email'));
        $formField->setRequired(true);

        return array($formField);
    }
    /*public function hookActionCustomerAccountAdd($params)
    {
        if(!isset($params['fields']))
           return false;
    }*/
}

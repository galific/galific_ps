<?php
/**
*  @author ST-themes https://www.sunnytoo.com
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class StCustomCode extends Module
{
    public $_html = '';
    public $fields_form;
    public $fields_value;
    public $validation_errors = array();

    public function __construct()
    {
        $this->name          = 'stcustomcode';
        $this->tab           = 'front_office_features';
        $this->version       = '1.0.0';
        $this->author        = 'sunnytoo.com';
        $this->need_instance = 0;
        $this->bootstrap     = true;
        
        parent::__construct();
        
        $this->displayName = $this->l('Custom code module by ST-themes');
        $this->description = $this->l('This module allows you to add custom css or JavaScript, Google analytics code, google fonts and meta tags to your site.');
        $this->ps_versions_compliancy = array('min' => '1.7', 'max' => _PS_VERSION_);
    }
    public function install()
    {
        $result = true;
        if (!parent::install()
            || !Configuration::updateValue('ST_CUSTOMCODE_CUSTOM_CSS', '')
            || !Configuration::updateValue('ST_CUSTOMCODE_CUSTOM_JS', '')
            || !Configuration::updateValue('ST_CUSTOMCODE_HEAD_CODE', '')
            || !Configuration::updateValue('ST_CUSTOMCODE_BODY_CODE', '')
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayBeforeBodyClosingTag')
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
        $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        $this->initFieldsForm();
        if (isset($_POST['savestcustomcode']))
        {
            if (isset($_POST['custom_css']) && $_POST['custom_css'])
                $_POST['custom_css'] = str_replace('\\', '¤', $_POST['custom_css']);

            foreach($this->fields_form as $form)
                foreach($form['form']['input'] as $field)
                    if(isset($field['validation']))
                    {
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
                            Configuration::updateValue('ST_CUSTOMCODE_'.strtoupper($field['name']), $value);
                        }
                        else
                            Configuration::updateValue('ST_CUSTOMCODE_'.strtoupper($field['name']), $value, true);
                    }
                                                 
            if(count($this->validation_errors))
                $this->_html .= $this->displayError(implode('<br/>',$this->validation_errors));
            else 
                $this->_html .= $this->displayConfirmation($this->getTranslator()->trans('Settings updated', array(), 'Admin.Theme.Panda'));
        }

        $helper = $this->initForm();
        
        return $this->_html.$helper->generateForm($this->fields_form).'<div class="alert alert-info">This free module was created by <a href="https://www.sunnytoo.com" target="_blank">ST-THEMES</a>, it\'s not allow to sell it, it\'s also not allow to create new modules based on this one. Check more <a href="https://www.sunnytoo.com/blogs?term=743&orderby=date&order=desc" target="_blank">free modules</a>, <a href="https://www.sunnytoo.com/product-category/prestashop-modules" target="_blank">advanced paid modules</a> and <a href="https://www.sunnytoo.com/product-category/prestashop-themes" target="_blank">themes(transformer theme and panda  theme)</a> created by ST-THEMES.</div>';
    }

    protected function initFieldsForm()
    {
        $this->fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->displayName,
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'textarea',
                    'label' => $this->getTranslator()->trans('Custom CSS Code:', array(), 'Modules.Stcustomcode.Admin'),
                    'name' => 'custom_css',
                    'cols' => 80,
                    'rows' => 20,
                    'desc' => $this->getTranslator()->trans('Put CSS code without here wrapping them in STYLE tag', array(), 'Modules.Stcustomcode.Admin'),
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->getTranslator()->trans('Custom JAVASCRIPT Code:', array(), 'Modules.Stcustomcode.Admin'),
                    'name' => 'custom_js',
                    'cols' => 80,
                    'rows' => 20,
                    'desc' => $this->getTranslator()->trans('Put JavaScript code here without wrapping them in SCRIPT tag', array(), 'Modules.Stcustomcode.Admin'),
                    'validation' => 'isAnything',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->getTranslator()->trans('Head code:', array(), 'Modules.Stcustomcode.Admin'),
                    'name' => 'head_code',
                    'cols' => 80,
                    'rows' => 20,
                    'validation' => 'isAnything',
                    'desc' => array(
                        $this->getTranslator()->trans('1. Code here will be injected before the closing body tag on every page in your site.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('2. Turn off the "Use HTMLPurifier Library" setting on the Shop parameters > General page if you want to put html codes into this field.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('3. Can be used to add extra meta tags to the header.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('4. Can be used to add Google font to your site.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('5. Can be used to add Google tracking code.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('6. Can be extra css files.', array(), 'Modules.Stcustomcode.Admin'),
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->getTranslator()->trans('Body code:', array(), 'Modules.Stcustomcode.Admin'),
                    'name' => 'body_code',
                    'cols' => 80,
                    'rows' => 20,
                    'validation' => 'isAnything',
                    'desc' => array(
                        $this->getTranslator()->trans('1. Code here will be injected before the closing body tag on every page in your site.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('2. Turn off the "Use HTMLPurifier Library" setting on the Shop parameters > General page if you want to put html codes into this field.', array(), 'Modules.Stcustomcode.Admin'),
                        $this->getTranslator()->trans('3. Can be extra javascript files.', array(), 'Modules.Stcustomcode.Admin'),
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->getTranslator()->trans('   Save   ', array(), 'Admin.Actions')
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
        $helper->submit_action = 'savestcustomcode';
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
            'custom_css' => str_replace('¤', '\\', Configuration::get('ST_CUSTOMCODE_CUSTOM_CSS')),
            'custom_js' => Configuration::get('ST_CUSTOMCODE_CUSTOM_JS'),
            'head_code' => Configuration::get('ST_CUSTOMCODE_HEAD_CODE'),
            'body_code' => Configuration::get('ST_CUSTOMCODE_BODY_CODE'),
        );
        
        return $fields_values;
    }
    public function hookDisplayHeader($params)
    {
        if (!$this->isCached('module:stcustomcode/views/templates/hook/header.tpl', $this->getCacheId())) {
            $custom_css = str_replace('¤', '\\', Configuration::get('ST_CUSTOMCODE_CUSTOM_CSS'));
            $custom_css = preg_replace('/\s\s+/', ' ', $custom_css);
            $this->context->smarty->assign('stcustomcode', array(
                'css' => html_entity_decode($custom_css),
                'js' => html_entity_decode(Configuration::get('ST_CUSTOMCODE_CUSTOM_JS')),
                'head_code' => Configuration::get('ST_CUSTOMCODE_HEAD_CODE'),
            ));
        }
        return $this->fetch('module:stcustomcode/views/templates/hook/header.tpl', $this->getCacheId());
    }
    public function hookDisplayBeforeBodyClosingTag($params)
    {
        if (!$this->isCached('module:stcustomcode/views/templates/hook/body.tpl', $this->getCacheId())) {
            $this->context->smarty->assign('stcustomcode_body_code', Configuration::get('ST_CUSTOMCODE_BODY_CODE'));
        }
        return $this->fetch('module:stcustomcode/views/templates/hook/body.tpl', $this->getCacheId());
    }
}

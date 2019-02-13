<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class Posrecommendedproducts extends Module implements WidgetInterface
{
    private $templateFile;
    public static $sort_by = array(
        1 => array('id' =>1 , 'name' => 'Product Name'),
        2 => array('id' =>2 , 'name' => 'Price'),
        3 => array('id' =>3 , 'name' => 'Product ID'),       
        4 => array('id' =>4 , 'name' => 'Position'),
        5 => array('id' =>5 , 'name' => 'Date updated'),
        6 => array('id' =>6 , 'name' => 'Date added'),
        7 => array('id' =>7 , 'name' => 'Random'),
    );

    public static $order_by = array(
        1 => array('id' =>1 , 'name' => 'Descending'),
        2 => array('id' =>2 , 'name' => 'Ascending'),
    );

    public function __construct()
    {
        $this->name = 'posrecommendedproducts';
        $this->author = 'Posthemes';
        $this->version = '1.0.0';
        $this->need_instance = 0;
        $this->prefixname = 'TSELECT';

        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;
        parent::__construct();

        $this->displayName = $this->trans('Pos recommended products', array(), 'Modules.Featuredproducts.Admin');
        $this->description = $this->trans('Displays selected products in homepage.', array(), 'Modules.Featuredproducts.Admin');

        $this->templateFile = 'module:posrecommendedproducts/views/templates/hook/posrecommendedproducts.tpl';
    }

    public function install()
    {
        $this->_clearCache('*');

        Configuration::updateValue($this->prefixname . '_PRODUCTS', '1,2,3,4,5,6');
        Configuration::updateValue($this->prefixname . '_SORT_BY', 1);
        Configuration::updateValue($this->prefixname . '_ORDER_BY', 1);
        Configuration::updateValue($this->prefixname . '_ITEMS', 3);
        Configuration::updateValue($this->prefixname . '_ITEMS_PER_MD', 2);
        Configuration::updateValue($this->prefixname . '_ITEMS_PER_SM', 2);
        Configuration::updateValue($this->prefixname . '_ITEMS_PER_XS', 2);
        Configuration::updateValue($this->prefixname . '_ITEMS_PER_XXS', 1);
        Configuration::updateValue($this->prefixname . '_ROW',2);
        Configuration::updateValue($this->prefixname . '_LAZY', 1);
        Configuration::updateValue($this->prefixname . '_SPEED', 1000);
        Configuration::updateValue($this->prefixname . '_AUTO', 0);
        Configuration::updateValue($this->prefixname . '_PAUSE', 3000);
        Configuration::updateValue($this->prefixname . '_ARROW', 1);
        Configuration::updateValue($this->prefixname . '_PAGI', 0);
        Configuration::updateValue($this->prefixname . '_MOVE', 1);
        Configuration::updateValue($this->prefixname . '_PAUSEHOVER', 1);

        return parent::install()
            && $this->registerHook('addproduct')
            && $this->registerHook('updateproduct')
            && $this->registerHook('deleteproduct')
            && $this->registerHook('categoryUpdate')
            && $this->registerHook('displayLeftColumn')
			&& $this->registerHook('displayBlockPosition3')
            && $this->registerHook('displayHeader')
            && $this->installFixtures();
        ;
    }
    protected function installFixtures()
    {
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang){
            $values['content_title'][(int)$lang['id_lang']] = 'recommended';
            $values['content_description'][(int)$lang['id_lang']] = '';
            Configuration::updateValue($this->prefixname . '_TITLE', $values['content_title']);
            Configuration::updateValue($this->prefixname . '_DESCRIPTION', $values['content_description']);
        }

        return true;
    }

    public function uninstall()
    {
        $this->_clearCache('*');

        return parent::uninstall();
    }

    public function hookAddProduct($params)
    {
        $this->_clearCache('*');
    }

    public function hookUpdateProduct($params)
    {
        $this->_clearCache('*');
    }

    public function hookDeleteProduct($params)
    {
        $this->_clearCache('*');
    }

    public function hookCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    public function hookActionAdminGroupsControllerSaveAfter($params)
    {
        $this->_clearCache('*');
    }

    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }
    public function hookDisplayHeader($params){
        $this->context->controller->registerJavascript('modules-Posrecommendedproducts', 'modules/'.$this->name.'/views/js/posrecommendedproducts.js', ['position' => 'bottom', 'priority' => 150]);
    }

    public function getContent()
    {
        $this->context->controller->addJS($this->_path. 'views/js/admin.js');
        $output = '';
        $errors = array();

        if (Tools::isSubmit('submitPosrecommendedproducts')) {

            $languages = Language::getLanguages(false);
            $values = array();
            if($this->_postValidation()){
                foreach ($languages as $lang){
                    $values[$this->prefixname . '_title'][$lang['id_lang']] = Tools::getValue('content_title_'.$lang['id_lang']);
                    $values[$this->prefixname . '_description'][$lang['id_lang']] = Tools::getValue('content_description_'.$lang['id_lang']);
                    $values[$this->prefixname . '_column_title'][$lang['id_lang']] = Tools::getValue('column_title_'.$lang['id_lang']);
                }

                if (isset($errors) && count($errors)) {
                    $output = $this->displayError(implode('<br />', $errors));
                } else {
                    Configuration::updateValue($this->prefixname . '_PRODUCTS', implode(',', Tools::getValue('product')));
                    Configuration::updateValue($this->prefixname . '_TITLE', $values[$this->prefixname . '_title']);
                    Configuration::updateValue($this->prefixname . '_DESCRIPTION', $values[$this->prefixname . '_description']);
                    Configuration::updateValue($this->prefixname . '_ITEMS', Tools::getValue('content_items'));
                    Configuration::updateValue($this->prefixname . '_ITEMS_PER_MD', Tools::getValue('content_items_per_md'));
                    Configuration::updateValue($this->prefixname . '_ITEMS_PER_SM', Tools::getValue('content_items_per_sm'));
                    Configuration::updateValue($this->prefixname . '_ITEMS_PER_XS', Tools::getValue('content_items_per_xs'));
                    Configuration::updateValue($this->prefixname . '_ITEMS_PER_XXS', Tools::getValue('content_items_per_xxs'));
                    Configuration::updateValue($this->prefixname . '_ROW', Tools::getValue('content_row'));
                    Configuration::updateValue($this->prefixname . '_LAZY', Tools::getValue('content_lazy'));
                    Configuration::updateValue($this->prefixname . '_SPEED', Tools::getValue('content_speed'));
                    Configuration::updateValue($this->prefixname . '_AUTO', Tools::getValue('content_auto'));
                    Configuration::updateValue($this->prefixname . '_PAUSE', Tools::getValue('content_pause'));
                    Configuration::updateValue($this->prefixname . '_ARROW', Tools::getValue('content_arrow'));
                    Configuration::updateValue($this->prefixname . '_PAGI', Tools::getValue('content_pagi'));
                    Configuration::updateValue($this->prefixname . '_MOVE', Tools::getValue('content_move'));
                    Configuration::updateValue($this->prefixname . '_PAUSEHOVER', Tools::getValue('content_pausehover'));

                    $this->_clearCache('*');

                    $output = $this->displayConfirmation($this->trans('The settings have been updated.', array(), 'Admin.Notifications.Success'));
                }
            }else{
                return $this->_html;
            }
        }

        return $output.$this->renderForm();
    }

    protected function _postValidation()
    {
        $errors = array();
        if (Tools::isSubmit('submitposfeaturedproducts'))
        {

            if (!Validate::isInt(Tools::getValue('content_limit')) || !Validate::isInt(Tools::getValue('content_items')) || !Validate::isInt(Tools::getValue('content_row')) || !Validate::isInt(Tools::getValue('content_speed')) || !Validate::isInt(Tools::getValue('content_pause')) || !Validate::isInt(Tools::getValue('column_items')) || !Validate::isInt(Tools::getValue('column_limit')) || !Validate::isInt(Tools::getValue('column_speed')) || !Validate::isInt(Tools::getValue('column_pause'))
            )
                $errors[] = $this->l('Invalid values');
        } 
        /* Returns if validation is ok */
        if (count($errors))
        {
            $this->_html .= $this->displayError(implode('<br />', $errors));

            return false;
        }

        return true;
    }

    public function renderForm()
    {   
        $id_lang = (int)Context::getContext()->language->id;
        $products = array();
        $products_current = Configuration::get($this->prefixname . '_PRODUCTS');
        if(isset($products_current) && $products_current){
            $products_current = explode(',', $products_current);
            foreach($products_current as $product_current){
                $product_name = Product::getProductName($product_current, null, $id_lang);
                $products[] = array(
                    'name' => $product_name,
                    'product_id' => $product_current
                );
            }
        }
        
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('General Settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'lang' => true,
                    'label' => $this->l('Title'),
                    'name' => 'content_title',
                    'class' => 'fixed-width-xxl',
                    'desc' => $this->l('This title will be displayed on front-office.')
                ),
                array(
                    'type' => 'selectproduct',
                    'label' => 'Select products:',
                    'name' => 'product',
                    'multiple'=> true,
                    'size' => 500
                ),
                    
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );
        $fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Homepage/ Content settings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                'content' => array(
                    'type' => 'textarea',
                    'label' => $this->trans('Description', array(), 'Modules.Customtext.Admin'),
                    'lang' => true,
                    'name' => 'content_description',
                    'cols' => 40,
                    'rows' => 10,
                    'class' => 'rte',
                    'autoload_rte' => true,
                ),
                array(
                        'type' => 'text',
                        'label' => $this->l('Number of Items per row'),
                        'name' => 'content_items',
                        'class' => 'fixed-width-sm',
                ),
                'pos_fp_pro_content' => array(
                    'type' => 'html',
                    'id' => 'pos_fp_pro_content',
                    'label'=> $this->l('Responsive:'),
                    'name' => '',
                ),
                array(
                        'type' => 'text',
                        'label' => $this->l('Rows'),
                        'name' => 'content_row',
                        'class' => 'fixed-width-sm',
                        'desc' => $this->l('Number row products will be displayed.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Lazy load'),
                    'name' => 'content_lazy',
                    'desc' => $this->l('Default is 1000ms'),
                    'values' => array(
                        array(
                            'id' => 'content_lazy_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'content_lazy_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                        'type' => 'text',
                        'label' => $this->l('Slide speed'),
                        'name' => 'content_speed',
                        'class' => 'fixed-width-sm',
                        'suffix' => 'milliseconds',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Auto play'),
                    'name' => 'content_auto',
                    'class' => 'fixed-width-xs',
                    'desc' => $this->l('Default is 1000ms'),
                    'values' => array(
                        array(
                            'id' => 'content_auto_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'content_auto_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                        'type' => 'text',
                        'label' => $this->l('Time auto'),
                        'name' => 'content_pause',
                        'class' => 'fixed-width-sm',
                        'suffix' => 'milliseconds',
                        'desc' => $this->l('This field only is valuable when auto play function is enable. Default is 3000ms.'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show navigation control'),
                    'name' => 'content_arrow',
                    'class' => 'fixed-width-xs',
                    'values' => array(
                        array(
                            'id' => 'content_arrow_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'content_arrow_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Show pagination control'),
                    'name' => 'content_pagi',
                    'class' => 'fixed-width-xs',
                    'desc' => $this->l(''),
                    'values' => array(
                        array(
                            'id' => 'content_pagi_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'content_pagi_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                ),
                array(
                    'type' => 'radio',
                    'label' => $this->l('Scroll number'),
                    'name' => 'content_move',
                    'default_value' => 0,
                    'values' => array(
                        array(
                            'id' => 'content_move_on',
                            'value' => 1,
                            'label' => $this->l('1 item')),
                        array(
                            'id' => 'content_move_off',
                            'value' => 0,
                            'label' => $this->l('All visible items')),
                    ),
                    'validation' => 'isBool',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Pause On Hover'),
                    'name' => 'content_pausehover',
                    'default_value' => 1,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'content_pausehover_on',
                            'value' => 1,
                            'label' => $this->l('Yes'),
                        ),
                        array(
                            'id' => 'content_pausehover_off',
                            'value' => 0,
                            'label' => $this->l('No'),
                        ),
                    ),
                    'validation' => 'isBool',
                ),
                
                
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )       
        );
        //print_r($this->findCateProPerContent()); die;
        
        $fields_form[1]['form']['input']['pos_fp_pro_content']['name'] = $this->BuildDropListGroup($this->findCateProPerContent());
        
        $helper = new HelperForm();
        $helper->show_toolbar = true;
        $helper->table = $this->table;
        $lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
        $helper->module = $this;
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitPosrecommendedproducts';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $module = _PS_MODULE_DIR_ ;
        $helper->tpl_vars = array(
            'module' =>$module,
            'uri' => $this->getPathUri(),
            'fields_value' => $this->getConfigFieldsValues(),
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
            'products' => $products,
        );

        return $helper->generateForm($fields_form);
    }

    public function getConfigFieldsValues()
    {
        $languages = Language::getLanguages(false);
        $fields = array(
            'content_description'       => Configuration::get($this->prefixname . '_DESCRIPTION'),
            'content_items'             => Configuration::get($this->prefixname . '_ITEMS'),
            'content_items_per_md'      => Configuration::get($this->prefixname . '_ITEMS_PER_MD'),
            'content_items_per_sm'      => Configuration::get($this->prefixname . '_ITEMS_PER_SM'),
            'content_items_per_xs'      => Configuration::get($this->prefixname . '_ITEMS_PER_XS'),
            'content_items_per_xxs'     => Configuration::get($this->prefixname . '_ITEMS_PER_XXS'),
            'content_row'               => Configuration::get($this->prefixname . '_ROW'),
            'content_lazy'              => Configuration::get($this->prefixname . '_LAZY'),
            'content_speed'             => Configuration::get($this->prefixname . '_SPEED'),
            'content_auto'              => Configuration::get($this->prefixname . '_AUTO'),
            'content_pause'             => Configuration::get($this->prefixname . '_PAUSE'),
            'content_arrow'             => Configuration::get($this->prefixname . '_ARROW'),
            'content_pagi'              => Configuration::get($this->prefixname . '_PAGI'),
            'content_move'              => Configuration::get($this->prefixname . '_MOVE'),
            'content_pausehover'        => Configuration::get($this->prefixname . '_PAUSEHOVER'),

        );
        
        foreach ($languages as $lang)
        {   
            $fields['content_title'][$lang['id_lang']] = Tools::getValue('content_title_'.$lang['id_lang'], Configuration::get($this->prefixname . '_TITLE', $lang['id_lang']));
            $fields['content_desctiption'][$lang['id_lang']] = Tools::getValue('content_desctiption_'.$lang['id_lang'], Configuration::get($this->prefixname . '_DESCRIPTION', $lang['id_lang']));
        }
        return $fields;
    }

    public function renderWidget($hookName = null, array $configuration = [])
    {
        
    
                $variables = $this->getWidgetVariables($hookName, $configuration);

                if (empty($variables)) {
                    return false;
                }

                $this->smarty->assign($variables);
   

            return $this->fetch($this->templateFile);
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
       
        // content variable
        $products = $this->getProducts();
        
        $title = Configuration::get($this->prefixname . '_TITLE', $this->context->language->id);
        $description = Configuration::get($this->prefixname . '_DESCRIPTION', $this->context->language->id);
        $content_options = array(
            'lazy_load' => (int)Configuration::get($this->prefixname . '_LAZY'),
            'rows' => (int)Configuration::get($this->prefixname . '_ROW'),
            'number_item' => (int)Configuration::get($this->prefixname . '_ITEMS'),
            'speed_slide' => (int)Configuration::get($this->prefixname . '_SPEED'),
            'auto_play' => (int)Configuration::get($this->prefixname . '_AUTO'),
            'auto_time' => (int)Configuration::get($this->prefixname . '_PAUSE'),
            'show_arrow' => (int)Configuration::get($this->prefixname . '_ARROW'),
            'show_pagination' => (int)Configuration::get($this->prefixname . '_PAGI'),
            'move' => (int)Configuration::get($this->prefixname . '_MOVE'),
            'pausehover' => (int)Configuration::get($this->prefixname . '_PAUSEONHOVER'),
            'items_md' => (int)Configuration::get($this->prefixname . '_ITEMS_PER_MD'), 
            'items_sm' => (int)Configuration::get($this->prefixname . '_ITEMS_PER_SM'), 
            'items_xs' => (int)Configuration::get($this->prefixname . '_ITEMS_PER_XS'), 
            'items_xxs' => (int)Configuration::get($this->prefixname . '_ITEMS_PER_XXS'),       
        );

        if (!empty($products)) {
            return array(
                'products' => $products,
                'content_options' => $content_options,
                'title' => $title,
                'description' => $description
            );
        }
        return false;
    }

    protected function getProducts()
    {
        $products_current = Configuration::get($this->prefixname . '_PRODUCTS');
        $result = array();
        if($products_current){
        $array_products = explode(',', $products_current);
        foreach($array_products as $product_id){
            $test = $this->getProductByID($product_id);
            $result[] = $test[0];
        }
        }

        $assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = [];

        foreach ($result as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }

        return $products_for_template;
    }

    private function BuildDropListGroup($group)
    {   
        if(!is_array($group) || !count($group))
            return false;
        //echo '<pre>'; print_r(Configuration::get($this->prefixname . '_ITEMS_PER_MD')); die;
        $html = '<div class="row">';
        foreach($group AS $key => $k)
        {
             if($key==4)
                 $html .= '</div><div class="row">';

             $html .= '<div class="col-xs-4 col-sm-3">'.$k['label'].'</label>'.
             '<select name="'.$k['name'].'" 
             id="'.$k['name'].'" 
             class="'.(isset($k['class']) ? $k['class'] : 'fixed-width-md').'"'.
             (isset($k['onchange']) ? ' onchange="'.$k['onchange'].'"':'').' >';
            
            for ($i=1; $i < 7; $i++){
                $html .= '<option value="'.$i.'" '.(Configuration::get($k['id']) == $i ? ' selected="selected"':'').'>'.$i.'</option>';
            }
                                
            $html .= '</select></div>';
        }

        return $html.'</div>';
    }
    private function findCateProPerContent()
    {
        return array(
            array(
                'name' => 'content_items_per_md',
                'id' => $this->prefixname . '_ITEMS_PER_MD',
                'label' => $this->l('Desktops (<1200 pixels)'),
            ),
            array(
                'name' => 'content_items_per_sm',
                'id' => $this->prefixname . '_ITEMS_PER_SM',
                'label' => $this->l('Tablets (<992 pixels)'),
            ),
            array(
                'name' => 'content_items_per_xs',
                'id' => $this->prefixname . '_ITEMS_PER_XS',
                'label' => $this->l('Phones (<768 pixels)'),
            ),
            array(
                'name' => 'content_items_per_xxs',
                'id' => $this->prefixname . '_ITEMS_PER_XXS',
                'label' => $this->l('Small phones (<480 pixels)'),
            ),
        );
    }
    public function getProductByID($id_product){
        $nb_days_new_product = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        $id_lang =(int) Context::getContext()->language->id;
        $id_shop = (int) Context::getContext()->shop->id;

        $sql = 'SELECT p.*, product_shop.*,  pl.`description`, pl.`description_short`, pl.`available_now`,
                    pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
                    il.`legend` as legend, m.`name` AS manufacturer_name,
                    DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
                    INTERVAL '.(int)$nb_days_new_product.' DAY)) > 0 AS new, product_shop.price AS orderprice
                FROM `'._DB_PREFIX_.'product` p
                LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
                    ON (pl.`id_product` = '.$id_product.'
                    AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
                LEFT JOIN `'._DB_PREFIX_.'product_shop` product_shop
                    ON product_shop.`id_product` = '.$id_product.'
                LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.$id_shop.')
                LEFT JOIN `'._DB_PREFIX_.'image_lang` il
                    ON (image_shop.`id_image` = il.`id_image`
                    AND il.`id_lang` = '.(int)$id_lang.')
                LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
                    ON m.`id_manufacturer` = p.`id_manufacturer`
                WHERE product_shop.`id_shop` = '.$id_shop.'
                    AND p.`id_product` = '.(int)$id_product;

           //echo '<pre>'; print_r($sql); die;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        return Product::getProductsProperties($id_lang, $result);
    }
   
}

<?php
if (!defined('_PS_VERSION_'))
	exit;
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Manufacturer\ManufacturerProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Adapter\Translator;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class posproductsmanufacturer extends Module implements WidgetInterface {
	private $token = '';
	private $_html = '';
	public static $sort_by = array(
        1 => array('id' =>1 , 'name' => 'Product Name'),
        2 => array('id' =>2 , 'name' => 'Price'),
        3 => array('id' =>3 , 'name' => 'Product ID'),       
        4 => array('id' =>4 , 'name' => 'Position'),
        5 => array('id' =>5 , 'name' => 'Date updated'),
        6 => array('id' =>6 , 'name' => 'Date added'),
    );

    public static $order_by = array(
        1 => array('id' =>1 , 'name' => 'Descending'),
        2 => array('id' =>2 , 'name' => 'Ascending'),
    );	
	public function __construct() {
		$this->name 		= 'posproductsmanufacturer';
		$this->tab 			= 'front_office_features';
		$this->version 		= '1.0';
		$this->author 		= 'posthemes';
		$this->bootstrap = true;
		$this->_html        = '';
		$this->displayName 	= $this->l('Product manufacturer');
		$this->description 	= $this->l('Show products from manufacturer');
		parent :: __construct();

		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		$this->templateFile = 'module:posproductsmanufacturer/posproductsmanufacturer.tpl';
       
	}
	
	public function install() {
        Configuration::updateValue($this->name . '_row', 2);
        Configuration::updateValue($this->name . '_number_item',3);
		Configuration::updateValue($this->name . '_speed_slide', 1000);
        Configuration::updateValue($this->name . '_auto_play', 0);
		Configuration::updateValue($this->name . '_pause_time', 3000);
        Configuration::updateValue($this->name . '_show_arrow', 1);
        Configuration::updateValue($this->name . '_show_ctr', 0);
        Configuration::updateValue($this->name . '_limit', 7);
        Configuration::updateValue($this->name . '_sort', 1);
        Configuration::updateValue($this->name . '_order', 1);
        Configuration::updateValue($this->name . '_move', 6);
        Configuration::updateValue($this->name . '_pausehover', 1);
        Configuration::updateValue($this->name . '_per_md', 3);
        Configuration::updateValue($this->name . '_per_sm', 2);
        Configuration::updateValue($this->name . '_per_xs', 2);
        Configuration::updateValue($this->name . '_per_xxs', 1);
		$arrayDefault = array('1','2');
		$cateDefault = implode(',',$arrayDefault);
		Configuration::updateGlobalValue($this->name.'_list_manu',$cateDefault);
		
		return parent :: install()
			&& $this->registerHook('displayHome')
			&& $this->registerHook('header')
			&& $this->installFixtures();
	}
	protected function installFixtures()
	{
		$languages = Language::getLanguages(false);
		foreach ($languages as $lang){
			$this->installFixture((int)$lang['id_lang']);
		}

		return true;
	}

	protected function installFixture($id_lang, $image = null)
	{	
		$values['posproductsmanufacturer_title'][(int)$id_lang] = 'brand sale';
		$values['posproductsmanufacturer_image'][(int)$id_lang] = 'cms_manu.jpg';
		$values['posproductsmanufacturer_link'][(int)$id_lang] = '#';
		Configuration::updateValue($this->name . '_title', $values['posproductsmanufacturer_title']);
		Configuration::updateValue($this->name . '_image', $values['posproductsmanufacturer_image']);
		Configuration::updateValue($this->name . '_link', $values['posproductsmanufacturer_link']);
	}
	
    public function uninstall() {
        $this->_clearCache('posproductsmanufacturer.tpl');
		Configuration::deleteByName($this->name . '_row');
		Configuration::deleteByName($this->name . '_number_item');
		Configuration::deleteByName($this->name . '_speed_slide');
		Configuration::deleteByName($this->name . '_auto_play');
		Configuration::deleteByName($this->name . '_pause_time');
		Configuration::deleteByName($this->name . '_show_arrow');
		Configuration::deleteByName($this->name . '_show_ctr');
		Configuration::deleteByName($this->name . '_limit');
		Configuration::deleteByName($this->name . '_list_manu');
		Configuration::deleteByName($this->name . '_title');
		Configuration::deleteByName($this->name . '_sort');
        Configuration::deleteByName($this->name . '_order');
        Configuration::deleteByName($this->name . '_move');
        Configuration::deleteByName($this->name . '_pausehover');
        Configuration::deleteByName($this->name . '_per_md');
        Configuration::deleteByName($this->name . '_per_sm');
        Configuration::deleteByName($this->name . '_per_xs');
        Configuration::deleteByName($this->name . '_per_xxs');
		
        return parent::uninstall();
    }
        
    public function hookHeader($params){
		$this->context->controller->addJS($this->_path.'js/posproductsmanufacturer.js');
    }
    
	public function renderWidget($hookName = null, array $configuration = [])
    {	 
        if (!$this->isCached($this->templateFile, $this->getCacheId('posproductsmanufacturer'))) {
            $variables = $this->getWidgetVariables($hookName, $configuration);
            if (empty($variables)) {
                return false;
            }

            $this->smarty->assign($variables);
        }

        return $this->fetch($this->templateFile, $this->getCacheId('posproductsmanufacturer'));
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
        $arrayManufacturer = array();
		$manuSelected = Configuration::get($this->name . '_list_manu');
		$manuArray = explode(',', $manuSelected); 
		$id_lang =(int) Context::getContext()->language->id;
		$id_shop = (int) Context::getContext()->shop->id;
		$arrayProductCate = array();

		foreach($manuArray as $id_manufacturer) {
			$manufacturerProducts = $this->getProducts($id_manufacturer);
			$name_manufacturer = Manufacturer::getNameById($id_manufacturer);
			
			$arrayManufacturer[] = array(
				'products' => $manufacturerProducts,
				'id_manufacturer' => $id_manufacturer,
				'name_manufacturer' => $name_manufacturer,
			);
		}
		 
        $title = Configuration::get($this->name . '_title', $this->context->language->id);
        $slider_options = array(
            'rows' => (int)Configuration::get($this->name . '_row'),
			'number_item' => (int)Configuration::get($this->name . '_number_item'),
			'speed_slide' => (int)Configuration::get($this->name . '_speed_slide'),
			'auto_play' => (int)Configuration::get($this->name . '_auto_play'),
			'auto_time' => (int)Configuration::get($this->name . '_pause_time'),
			'show_arrow' => (int)Configuration::get($this->name . '_show_arrow'),
			'show_pagination' => (int)Configuration::get($this->name . '_show_ctr'),
			'limit' => (int)Configuration::get($this->name . '_limit'),
			'move' => (int)Configuration::get($this->name . '_move'),
			'pausehover' => (int)Configuration::get($this->name . '_pausehover'),
			'items_md' => (int)Configuration::get($this->name . '_per_md'),	
			'items_sm' => (int)Configuration::get($this->name . '_per_sm'),	
			'items_xs' => (int)Configuration::get($this->name . '_per_xs'),	
			'items_xxs' => (int)Configuration::get($this->name . '_per_xxs'),     
        );
		$imgname = Configuration::get($this->name . '_image', $this->context->language->id);

		if ($imgname && file_exists(_PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$imgname))
		   $this->smarty->assign('banner_img', $this->context->link->protocol_content.Tools::getMediaServer($imgname).$this->_path.'img/'.$imgname);
	
		$this->context->smarty->assign('slider_options', $slider_options);
		if(!empty($arrayManufacturer)){
	        return array(
				'arrayManufacturers' => $arrayManufacturer,
	            'homeSize' => Image::getSize(ImageType::getFormatedName('home')),
				'title' => Configuration::get($this->name . '_title', $this->context->language->id),			
				'image_link' => Configuration::get($this->name . '_link', $this->context->language->id),
	        )	;
	    }
       
        return false;
    }
	protected function getProducts($id_manufacturer)
    {   
        $random = false;
        $sortby = Configuration::get($this->name . '_sort');
        switch($sortby)
        {
        	case 1:
            $sortby = 'name';
       		break;
       		case 2:
            $sortby = 'price';
       		break;
       		case 3:
            $sortby = 'id_product';
       		break;
       		case 4:
            $sortby = 'position';
       		break;
       		case 5:
            $sortby = 'date_upd';
       		break;
       		case 6:
            $sortby = 'date_add';
       		break;
        }

        $manufacturer = new Manufacturer($id_manufacturer);

        $orderby = Configuration::get($this->name . '_order');
        if($orderby == 1) {
            $orderby = 'DESC';
        } else {
            $orderby = 'ASC';
        }
        $searchProvider = new ManufacturerProductSearchProvider(
            $this->context->getTranslator(),
            $manufacturer
        );

        $context = new ProductSearchContext($this->context);

        $query = new ProductSearchQuery();

        $nProducts = Configuration::get($this->name . '_limit');
        if ($nProducts < 0) {
            $nProducts = 12;
        }

        $query
            ->setResultsPerPage($nProducts)
            ->setPage(1)
        ;

        if ($random) {
            $query->setSortOrder(SortOrder::random());
        } else {
            $query->setSortOrder(new SortOrder('product', $sortby, $orderby));
        }

        $result = $searchProvider->runQuery(
            $context,
            $query
        );
        
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
        
        foreach ($result->getProducts() as $rawProduct) {
            $products_for_template[] = $presenter->present(
                $presentationSettings,
                $assembler->assembleProduct($rawProduct),
                $this->context->language
            );
        }
    
        return $products_for_template;
    }

	

    private function postProcess() {
		if (Tools::isSubmit('submitposproductsmanufacturer'))
		{
			$languages = Language::getLanguages(false);
			$values = array();
            $update_images_values = false;

            foreach ($languages as $lang) {
                if (isset($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']])
                    && isset($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']]['tmp_name'])
                    && !empty($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']]['tmp_name'])) {
                    if ($error = ImageManager::validateUpload($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']], 4000000)) {
                        return $error;
                    } else {
                        $ext = substr($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']]['name'], strrpos($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']]['name'], '.') + 1);
                        $file_name = md5($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']]['name']).'.'.$ext;

                        if (!move_uploaded_file($_FILES['posproductsmanufacturer_image_'.$lang['id_lang']]['tmp_name'], dirname(__FILE__).DIRECTORY_SEPARATOR.'img'.DIRECTORY_SEPARATOR.$file_name)) {
                            return $this->displayError($this->trans('An error occurred while attempting to upload the file.', array(), 'Admin.Notifications.Error'));
                        } else {
                            if (Configuration::hasContext('posproductsmanufacturer_image', $lang['id_lang'], Shop::getContext())
                                && Configuration::get('posproductsmanufacturer_image', $lang['id_lang']) != $file_name) {
                                @unlink(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR . Configuration::get('posproductsmanufacturer_image', $lang['id_lang']));
                            }

                            $values['posproductsmanufacturer_image'][$lang['id_lang']] = $file_name;
                        }
                    }

                    $update_images_values = true;
                }

                $values['posproductsmanufacturer_title'][$lang['id_lang']] = Tools::getValue('posproductsmanufacturer_title_'.$lang['id_lang']);
				$values['posproductsmanufacturer_link'][$lang['id_lang']] = Tools::getValue('posproductsmanufacturer_link_'.$lang['id_lang']);
            }
		 if ($update_images_values) {
            Configuration::updateValue($this->name . '_image', $values['posproductsmanufacturer_image']);
        }

		Configuration::updateValue($this->name . '_title', $values['posproductsmanufacturer_title']);
		Configuration::updateValue($this->name . '_image', $values['posproductsmanufacturer_image']);
		Configuration::updateValue($this->name . '_link', $values['posproductsmanufacturer_link']);
		
		Configuration::updateValue($this->name . '_list_manu', implode(',', Tools::getValue('posproductsmanufacturer_list_manu')));

		Configuration::updateValue($this->name . '_limit', Tools::getValue('posproductsmanufacturer_limit'));
		Configuration::updateValue($this->name . '_row', Tools::getValue('posproductsmanufacturer_row'));
		Configuration::updateValue($this->name . '_speed_slide', Tools::getValue('posproductsmanufacturer_speed_slide'));
		Configuration::updateValue($this->name . '_pause_time', Tools::getValue('posproductsmanufacturer_pause_time'));
		Configuration::updateValue($this->name . '_auto_play', Tools::getValue('posproductsmanufacturer_auto_play'));
		Configuration::updateValue($this->name . '_show_arrow', Tools::getValue('posproductsmanufacturer_show_arrow'));
		Configuration::updateValue($this->name . '_show_ctr', Tools::getValue('posproductsmanufacturer_show_ctr'));
		Configuration::updateValue($this->name . '_number_item', Tools::getValue('posproductsmanufacturer_number_item'));
		Configuration::updateValue($this->name . '_sort', Tools::getValue('posproductsmanufacturer_sort'));
		Configuration::updateValue($this->name . '_order', Tools::getValue('posproductsmanufacturer_order'));
		Configuration::updateValue($this->name . '_move', Tools::getValue('posproductsmanufacturer_move'));
		Configuration::updateValue($this->name . '_pausehover', Tools::getValue('posproductsmanufacturer_pausehover'));
		Configuration::updateValue($this->name . '_per_md', Tools::getValue('posproductsmanufacturer_per_md'));
		Configuration::updateValue($this->name . '_per_sm', Tools::getValue('posproductsmanufacturer_per_sm'));
		Configuration::updateValue($this->name . '_per_xs', Tools::getValue('posproductsmanufacturer_per_xs'));
		Configuration::updateValue($this->name . '_per_xxs', Tools::getValue('posproductsmanufacturer_per_xxs'));

		return $this->displayConfirmation($this->l('The settings have been updated.'));
		}
		
		return '';
    }
	
	public function getContent()
	{
		return $this->postProcess().$this->renderForm();
	}
	public function renderForm()
	{	

		$manuCurrent = Configuration::get($this->name . '_list_manu');
		$manuCurrent = explode(',', $manuCurrent);
        $id_lang = (int) Context::getContext()->language->id;
        $options =  $this->getManufacturers();
		$fields_form[0]['form'] = array(
			'legend' => array(
				'title' => $this->l('Homepage: Module Settings'),
				'icon' => 'icon-cogs'
			),
			'input' => array(
					array(
						'type' => 'text',
						'lang' => true,
						'label' => $this->l('Module title'),
						'name' => 'posproductsmanufacturer_title',
						'desc' => $this->l('This title will be displayed on front-office.')
					),
					// array(
                        // 'type' => 'file_lang',
                        // 'label' => $this->l('Banner image'),
                        // 'name' => 'posproductsmanufacturer_image',
                        // 'desc' => $this->l('Upload an image for your manufacturer banner.'),
                        // 'lang' => true,
                    // ),
                    // array(
                        // 'type' => 'text',
                        // 'lang' => true,
                        // 'label' => $this->l('Banner Link'),
                        // 'name' => 'posproductsmanufacturer_link',
                        // 'desc' => $this->l('Enter the link associated to your banner. When clicking on the banner, the link opens in the same window. If no link is entered, it redirects to the homepage.')
                    // ),
					array(
	                    'type' => 'select',
	                    'label' => $this->l('Sort by:'),
	                    'name' => 'posproductsmanufacturer_sort',
	                    'options' => array(
	                        'query' => self::$sort_by,
	                        'id' => 'id',
	                        'name' => 'name',
	                    ),
	                    'validation' => 'isUnsignedInt',
	                ),
	                array(
	                    'type' => 'select',
	                    'label' => $this->l('Order by:'),
	                    'name' => 'posproductsmanufacturer_order',
	                    'options' => array(
	                        'query' => self::$order_by,
	                        'id' => 'id',
	                        'name' => 'name',
	                    ),
	                    'validation' => 'isUnsignedInt',
	                ), 
					array(
							'type' => 'text',
							'label' => $this->l('Products limit :'),
							'name' => 'posproductsmanufacturer_limit',
							'class' => 'fixed-width-sm',
							'desc' => $this->l('Set the number of products which you would like to see displayed in this module')
					),
					array(
						'type' => 'listmanu',
						'label' => 'Choose the manufacturers:',
						'name' => 'posproductsmanufacturer_list_manu',
						'multiple'=>true,
						'size' => 500
					),
					
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);
		$fields_form[1]['form'] = array(
			'legend' => array(
				'title' => $this->l('Slider configurations'),
				'icon' => 'icon-cogs'
			),
			'input' => array(
				array(
						'type' => 'text',
						'label' => $this->l('Rows of carousel'),
						'name' => 'posproductsmanufacturer_row',
						'class' => 'fixed-width-sm'
				),
				array(
						'type' => 'text',
						'label' => $this->l('Number of Items:'),
						'name' => 'posproductsmanufacturer_number_item',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('Show number of product visible.')
				),
				array(
						'type' => 'text',
						'label' => $this->l('Slide speed:'),
						'name' => 'posproductsmanufacturer_speed_slide',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('')
				),
				
				array(
					'type' => 'switch',
					'label' => $this->l('Auto play'),
					'name' => 'posproductsmanufacturer_auto_play',
					'class' => 'fixed-width-xs',
					'desc' => $this->l(''),
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
					)
				),
				array(
						'type' => 'text',
						'label' => $this->l('Time auto'),
						'name' => 'posproductsmanufacturer_pause_time',
						'class' => 'fixed-width-sm',
						'desc' => $this->l('This field only is value when auto play function is enable. Default is 3000ms.')
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show Next/Back control:'),
					'name' => 'posproductsmanufacturer_show_arrow',
					'class' => 'fixed-width-xs',
					'desc' => $this->l(''),
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
					)
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Show navigation control:'),
					'name' => 'posproductsmanufacturer_show_ctr',
					'class' => 'fixed-width-xs',
					'desc' => $this->l(''),
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
					)
				),
				array(
					'type' => 'radio',
					'label' => $this->l('Scroll number:'),
					'name' => 'posproductsmanufacturer_move',
                    'default_value' => 0,
					'values' => array(
						array(
							'id' => 'posproductsmanufacturer_move_on',
							'value' => 1,
							'label' => $this->l('1 item')),
						array(
							'id' => 'posproductsmanufacturer_move_off',
							'value' => 0,
							'label' => $this->l('All visible items')),
					),
                    'validation' => 'isBool',
				),
				 array(
					'type' => 'switch',
					'label' => $this->l('Pause On Hover:'),
					'name' => 'posproductsmanufacturer_pausehover',
                    'default_value' => 1,
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'posproductsmanufacturer_pausehover_on',
							'value' => 1,
							'label' => $this->l('Yes')),
						array(
							'id' => 'posproductsmanufacturer_pausehover_off',
							'value' => 0,
							'label' => $this->l('No')),
					),
                    'validation' => 'isBool',
				),
				'pos_tabcate_pro' => array(
                    'type' => 'html',
                    'id' => 'pos_tabcate_pro',
                    'label'=> $this->l('Responsive:'),
                    'name' => '',
                ),
			),
			'submit' => array(
				'title' => $this->l('Save'),
			)
		);	
		
		$fields_form[1]['form']['input']['pos_tabcate_pro']['name'] = $this->BuildDropListGroup($this->findCateProPer());
		
		$helper = new HelperForm();
		$helper->show_toolbar = true;
		$helper->table = $this->table;
		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));
		$helper->module = $this;
		$helper->options = $options;
		$helper->default_form_language = $lang->id;
		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'submitposproductsmanufacturer';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$module = _PS_MODULE_DIR_ ;
		$helper->tpl_vars = array(
			'module' =>$module,
			'uri' => $this->getPathUri(),
			'fields_value' => $this->getConfigFieldsValues(),
			'languages' => $this->context->controller->getLanguages(),
			'id_language' => $this->context->language->id,
			'options' => $options,
			'manuCurrent' => $manuCurrent,
		);

		return $helper->generateForm($fields_form);
	}
	
	public function getConfigFieldsValues()
	{
		$languages = Language::getLanguages(false);
		$fields = array();
		$fields['posproductsmanufacturer_number_item'] = Tools::getValue('posproductsmanufacturer_number_item', (int)Configuration::get($this->name . '_number_item'));
		$fields['posproductsmanufacturer_speed_slide'] = Tools::getValue('posproductsmanufacturer_speed_slide', (int)Configuration::get($this->name . '_speed_slide'));
		$fields['posproductsmanufacturer_pause_time'] = Tools::getValue('posproductsmanufacturer_pause_time', (int)Configuration::get($this->name . '_pause_time'));
		$fields['posproductsmanufacturer_auto_play'] = Tools::getValue('posproductsmanufacturer_auto_play', (int)Configuration::get($this->name . '_auto_play'));
		$fields['posproductsmanufacturer_show_arrow'] = Tools::getValue('posproductsmanufacturer_show_arrow', (int)Configuration::get($this->name . '_show_arrow'));
		$fields['posproductsmanufacturer_show_ctr'] = Tools::getValue('posproductsmanufacturer_show_ctr', (int)Configuration::get($this->name . '_show_ctr'));
		$fields['posproductsmanufacturer_limit'] = Tools::getValue('posproductsmanufacturer_limit', (int)Configuration::get($this->name . '_limit'));
		$fields['posproductsmanufacturer_row'] = Tools::getValue('posproductsmanufacturer_row', (int)Configuration::get($this->name . '_row'));
		$fields['posproductsmanufacturer_list_manu'] = Tools::getValue('posproductsmanufacturer_list_manu', Configuration::get($this->name . '_list_manu'));
		$fields['posproductsmanufacturer_move'] = Tools::getValue('posproductsmanufacturer_move', Configuration::get($this->name . '_move'));
		$fields['posproductsmanufacturer_pausehover'] = Tools::getValue('posproductsmanufacturer_pausehover', Configuration::get($this->name . '_pausehover'));
		$fields['posproductsmanufacturer_sort'] = Tools::getValue('posproductsmanufacturer_sort', Configuration::get($this->name . '_sort'));
		$fields['posproductsmanufacturer_order'] = Tools::getValue('posproductsmanufacturer_order', Configuration::get($this->name . '_order'));

		
		foreach ($languages as $lang)
		{	
			$fields['posproductsmanufacturer_title'][$lang['id_lang']] = Tools::getValue('posproductsmanufacturer_title_'.$lang['id_lang'], Configuration::get($this->name . '_title', $lang['id_lang']));
			$fields['posproductsmanufacturer_image'][$lang['id_lang']] = Tools::getValue('posproductsmanufacturer_image_'.$lang['id_lang'], Configuration::get($this->name . '_image', $lang['id_lang']));
			$fields['posproductsmanufacturer_link'][$lang['id_lang']] = Tools::getValue('posproductsmanufacturer_link_'.$lang['id_lang'], Configuration::get($this->name . '_link', $lang['id_lang']));
		}
		
		return $fields;
	}
	
	

    public function getManufacturers() {
		$manuCurrent = Configuration::get($this->name . '_list_manu');
		$manuCurrent = explode(',', $manuCurrent);
		$manufacturers = Manufacturer::getManufacturers();


		foreach($manufacturers as $manufacturer){
			$this->_html .='<li>';
			$this->_html .='<span><input type="checkbox" name="posproductsmanufacturer_list_manu[]" value="'.$manufacturer['id_manufacturer'].'"/><label>'.$manufacturer['name'].'</label></span>';
			$this->_html .='</li>';
		}

         return $this->_html ;
    }
    public function BuildDropListGroup($group)
    {
        if(!is_array($group) || !count($group))
            return false;

        $html = '<div class="row">';
        foreach($group AS $key => $k)
        {
             if($key==4)
                 $html .= '</div><div class="row">';

             $html .= '<div class="col-xs-4 col-sm-3">'.$k['label'].'</label>'.
             '<select name="'.$k['id'].'" 
             id="'.$k['id'].'" 
             class="'.(isset($k['class']) ? $k['class'] : 'fixed-width-md').'"'.
             (isset($k['onchange']) ? ' onchange="'.$k['onchange'].'"':'').' >';
            
            for ($i=1; $i < 7; $i++){
                $html .= '<option value="'.$i.'" '.(Configuration::get($k['id']) == $i ? ' selected="selected"':'').'>'.$i.'</option>';
            }
                                
            $html .= '</select></div>';
        }

        return $html.'</div>';
    }
    public function findCateProPer()
    {
        return array(
            array(
                'id' => 'posproductsmanufacturer_per_md',
                'label' => $this->l('Desktops (>991 pixels)'),
            ),
            array(
                'id' => 'posproductsmanufacturer_per_sm',
                'label' => $this->l('Tablets (>767 pixels)'),
            ),
            array(
                'id' => 'posproductsmanufacturer_per_xs',
                'label' => $this->l('Phones (>480 pixels)'),
            ),
            array(
                'id' => 'posproductsmanufacturer_per_xxs',
                'label' => $this->l('Small phones (>320 pixels)'),
            ),
        );
    }
}
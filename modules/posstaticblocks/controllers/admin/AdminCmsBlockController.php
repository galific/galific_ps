<?php

class AdminCmsBlockController extends ModuleAdminController {
    protected $id_banner;
    public function __construct() {
        $this->table = 'pos_staticblock';
        $this->className = 'Staticblock';
        $this->identifier = 'id_posstaticblock';
        $this->lang = true;
		$this->bootstrap = true;
        $this->deleted = false;
        $this->colorOnBackground = false;
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        $this->context = Context::getContext();
        
        parent::__construct();
            $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->module->getTranslator()->trans('Delete selected', array(), 'Admin.Global'),
                 'confirm' => $this->module->getTranslator()->trans('Delete selected items?', array(), 'Admin.Global'),
                 )
            );
    }

    

    public function renderList() {
         
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?')
            )
        );

        $this->fields_list = array(
            'id_posstaticblock' => array(
                'title' => $this->l('ID'),
                'align' => 'center',
                'width' => 25,
                'lang' => false
            ),
            'title' => array(
                'title' => $this->l('Title'),
                'width' => 90,
                'lang' => false
            ),
            'identify' => array(
                'title' => $this->l('Identify'),
                'width' => '100',
                'lang' => false
            ),
            'hook_position' => array(
                'title' => $this->l('Hook Position'),
                'width' => '300',
                'lang' => false
            ),
            'posorder' => array(
                'title' => $this->l('Order'),
                'width' => '30',
                'lang' => false
            )
        );

//        $this->fields_list['image'] = array(
//            'title' => $this->l('Image'),
//            'width' => 70,
//            "image" => $this->fieldImageSettings["dir"]
//        );
//            
//        $listSlideshows = Staticblock::getSlideshowLists($this->context->language->id);
//        echo "<pre>"; print_r($listSlideshows); die;
        $lists = parent::renderList();
        parent::initToolbar();

        return $lists;
    }
    
  

    public function renderForm() {
        
        $mod = new posstaticblocks();
        $listModules = $mod->getListModuleInstalled();
        
        
        $listHookPosition = array(
            array('hook_position'=> 'displayTop'),
            array('hook_position'=> 'displayTopColumn'),
            array('hook_position'=>'rightColumn'),
            array('hook_position'=> 'leftColumn'),
            array('hook_position'=>'displayHeader'),
            array('hook_position'=>'displayFooter'),
            array('hook_position'=>'displayHome'),
            array('hook_position'=>'displayBlockPosition1'),
            array('hook_position'=>'displayBlockPosition2'),
            array('hook_position'=>'displayBlockPosition3'),
            array('hook_position'=>'displayBlockPosition4'),
            array('hook_position'=>'displayBlockPosition5'),
			 array('hook_position'=>'displayBlockPosition6'),     
			 array('hook_position'=>'displayBlockPosition7'),			 
            array('hook_position'=>'productExtraRight'),
            array('hook_position'=>'displayNav'),
			array('hook_position'=>'displayNav1'),
			array('hook_position'=>'displayBannerSequence'),
			array('hook_position'=>'displayBannerSlide'),
			array('hook_position'=>'displayBrandSlider')


        );
        
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Staticblocks'),
                'image' => '../img/admin/edit.gif'
            ),
            
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title:'),
                    'name' => 'title',
                    'size' => 40,
                    'lang' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Identify:'),
                    'name' => 'identify',
                    'size' => 40,
                    'require' => false
                ),
               	array(
						'type' => 'switch',
						'label' => $this->l('Show/Hide title'),
						'name' => 'active',
						'desc' => $this->l('Show/Hide title.'),
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
                'type' => 'select',
                'label' => $this->l('Hook Position:'),
                'name' => 'hook_position',
                'required' => true,
                'options' => array(
                    'query' => $listHookPosition,
                    'id' => 'hook_position',
                    'name' => 'hook_position'
                ),
             
                'desc' => $this->l('Choose the type of the Hooks')
            ),
            
  
				array(
						'type' => 'switch',
						'label' => $this->l('Show/hide Hook'), 
						'name' => 'showhook',
						'desc' => $this->l('Show/hide Hook'), 
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
                    'type' => 'textarea',
                    'label' => $this->l('Description'),
                    'name' => 'description',
                    'autoload_rte' => TRUE,
                    'lang' => true,
                    'required' => TRUE,
					'class' => 'rte',
                    'rows' => 5,
                    'cols' => 40,
                    'hint' => $this->l('Invalid characters:') . ' <>;=#{}'
                ),
                
                array(
                    'type' => 'text',
                    'label' => $this->l('Order:'),
                    'name' => 'posorder',
                    'size' => 40,
                    'require' => false
                ),
				array(
						'type' => 'switch',
						'label' => $this->l('Insert Module?'), 
						'name' => 'insert_module',
						'desc' => $this->l('Insert Module?'),
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
                'type' => 'select',
                'label' => $this->l('Modules:'),
                'name' => 'name_module',
                'required' => true,
                'options' => array(
                    'query' => $listModules,
                    'id' => 'name',
                    'name' => 'name'
                ),
                    'desc' => $this->l('Choose the type of the Module')
               ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Hook-Modules:'),
                    'name' => 'hook_module',
                    'required' => true,
                    'options' => array(
                        'query' => $listHookPosition,
                        'id' => 'hook_position',
                        'name' => 'hook_position'
                    ),
                    'desc' => $this->l('Choose the type of the Hooks')
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->l('Shop association:'),
                'name' => 'checkBoxShopAsso',
            );
        }

        if (!($obj = $this->loadObject(true)))
            return;


        return parent::renderForm();
    }
    
}

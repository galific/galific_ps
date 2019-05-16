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

require_once 'KbCore.php';

class KbmarketplaceShippingModuleFrontController extends KbmarketplaceCoreModuleFrontController
{
    public $controller_name = 'shipping';
    public $seller_carrier;

    public function __construct()
    {
        parent::__construct();
    }

    public function setMedia()
    {
        parent::setMedia();
        if (Tools::getIsset('action')) {
            if (Tools::getValue('action') == 'edit') {
                $this->addCSS($this->getKbModuleDir() . 'views/css/front/kb-forms.css');
                $this->addJqueryPlugin('typewatch');
                $this->addJs($this->getKbModuleDir() . 'views/js/front/shipping.js');
            }
        }
    }

    public function postProcess()
    {
        parent::postProcess();
        
                }

    public function initContent()
    {
            $this->renderShippingList();
        
        parent::initContent();
    }
    
    public function getTemplateVarPage()
    {
        $page = parent::getTemplateVarPage();
        if (isset($page['meta']) && $this->seller_info) {
            $page_title = $this->module->l('Shippings', 'shipping');
            $page['meta']['title'] =  $page_title;
            $page['meta']['keywords'] = $this->seller_info['meta_keyword'];
            $page['meta']['description'] = $this->seller_info['meta_description'];
        }
        return $page;
    }
    
    private function renderShippingList()
    {
        $this->context->smarty->assign(
            'new_shipping_link',
            $this->context->link->getModuleLink(
                $this->kb_module_name,
                $this->controller_name,
                array('action' => 'edit', 'id_carrier' => 0),
                (bool)Configuration::get('PS_SSL_ENABLED')
            )
        );

        $this->total_records = KbSellerShipping::getSellerShippings(
            $this->seller_obj->id,
            $this->context->language->id,
            true
        );

        if ($this->total_records > 0) {
            $statuses = array(
                array('value' => 1, 'label' => $this->module->l('Yes', 'shipping')),
                array('value' => 0, 'label' => $this->module->l('No', 'shipping'))
            );

            $this->table_header = array(
                array(
                    'label' => $this->module->l('ID', 'shipping'),
                    'align' => 'right',
                    'width' => '60'
                ),
                array(
                    'label' => $this->module->l('Name', 'shipping'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Logo', 'shipping'),
                    'align' => 'left',
                    'class' => '',
                    'width' => '70',
                ),
                array(
                    'label' => $this->module->l('Delay', 'shipping'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Status', 'shipping'),
                    'align' => 'left'
                ),
                array(
                    'label' => $this->module->l('Free Shipping', 'shipping'),
                    'align' => 'left',
                ),
                array(
                    'label' => $this->module->l('Action', 'shipping'),
                    'align' => 'left',
                )
            );

            $shippings = KbSellerShipping::getSellerShippings(
                $this->seller_obj->id,
                $this->context->language->id,
                false,
                $this->getPageStart(),
                $this->tbl_row_limit
            );
            foreach ($shippings as $ct) {
                $actions = array();
                if ($ct['is_default_shipping'] == 0) {
                    $actions = array(
                        array(
                            'type' => 'edit',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'edit', 'id_carrier' => $ct['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        ),
                        array(
                            'type' => 'delete',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'delete', 'id_carrier' => $ct['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            )
                        ),
                        array(
                            'type' => 'extra',
                            'href' => $this->context->link->getModuleLink(
                                $this->kb_module_name,
                                $this->controller_name,
                                array('action' => 'mapping', 'id_carrier' => $ct['id_carrier']),
                                (bool)Configuration::get('PS_SSL_ENABLED')
                            ),
                            'title' => $this->module->l('Click to map product(s)', 'shipping'),
                            'label' => $this->module->l('Mapping', 'shipping'),
                        )
                    );
                }
                $yes_txt = $this->module->l('Yes', 'shipping');
                $no_txt = $this->module->l('No', 'shipping');
                $this->table_content[] = array(
                    array(
                        'value' => '#' . $ct['id_carrier'],
                        'class' => 'kb-tright'
                    ),
                    array('value' => str_replace(' - ' . $this->seller_info['seller_name'], '', $ct['name'])),
                    array(
                        'value' => '--',
                        'class' => 'kb-tcenter'
                    ),
                    array('value' => $ct['delay']),
                    array(
                        'value' => (($ct['active']) ? $yes_txt : $no_txt)
                    ),
                    array(
                        'value' => (($ct['is_free']) ? $yes_txt : $no_txt)
                    ),
                    array(
                        'input' => array('type' => 'action'),
                        'class' => 'kb-tcenter',
                        'actions' => $actions
                    )
                );
            }

            $this->list_row_callback = $this->filter_action_name;
        }

        $this->context->smarty->assign('kblist', $this->renderKbList());

        $this->setKbTemplate('seller/shipping/list.tpl');
    }


        }

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

require_once dirname(__FILE__) . '/AdminKbMarketplaceCoreController.php';

class AdminKbEditCommissionController extends AdminKbMarketplaceCoreController
{

    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller_product';
        $this->className = 'KbSellerProduct';
        $this->identifier = 'id_seller_edit_commission';
        $this->lang = false;
        $this->display = 'edit';
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->toolbar_title = $this->module->l('Edit Commission', 'adminkbeditcommissioncontroller');
        $id_product = Tools::getValue("id_seller_product");
        if (empty($id_product)) {
            $this->errors[] = Tools::displayError(sprintf($this->module->l('Sorry. Product Id not exist.', 'adminkbeditcommissioncontroller')));
        } else {
            $product_detail = KbSellerProduct::getProductDetailsById($id_product);
            if (empty($product_detail)) {
                $this->errors[] = Tools::displayError(sprintf($this->module->l('Sorry. Product Id not exist.', 'adminkbeditcommissioncontroller')));
            } else {
                $this->fields_form = array(
                    'legend' => array(
                        'title' => $this->module->l('Edit Commission', 'adminkbeditcommissioncontroller'),
                        'icon' => 'icon-envelope'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'name' => 'id_seller_product',
                            'required' => true,
                            'id_seller_product' => $id_product,
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->module->l('Set Commission', 'adminkbeditcommissioncontroller'),
                            'name' => 'commission',
                            'id' => 'kb_mp_edit_commission',
                            'required' => true,
                            'col' => '9',
                            'class' => 'col-lg-9'
                        ),
                        array(
                            'type' => 'switch',
                            'label' => $this->module->l('Use Global', 'adminkbeditcommissioncontroller'),
                            'name' => 'use_global',
                            'required' => false,
                            'class' => 't',
                            'is_bool' => true,
                            'values' => array(
                                array(
                                    'id' => 'use_global_on',
                                    'value' => 1,
                                    'label' => $this->module->l('Enabled', 'adminkbmarketplacesettingcontroller')
                                ),
                                array(
                                    'id' => 'use_global_off',
                                    'value' => 0,
                                    'label' => $this->module->l('Disabled', 'adminkbmarketplacesettingcontroller')
                                )
                            )
                        )
                    ),
                    'buttons' => array(
                        array(
                            'title' => $this->module->l('Save', 'adminkbeditcommissioncontroller'),
                            'type' => 'submit',
                            'icon' => 'process-icon-save',
                            'class' => 'pull-right',
                            'name' => 'submitAdd' . $this->table,
                        )
                    )
                );

                if (Tools::getIsset('commission')) {
                    $product_detail["commission"] = Tools::getValue('commission');
                }
                if (Tools::getIsset('use_global')) {
                    $product_detail["use_global"] = Tools::getValue('use_global');
                }

                $this->fields_value = array(
                    'commission' => $product_detail["commission"],
                    'use_global' => $product_detail["use_global"]
                );

                $this->show_form_cancel_button = false;
            }
        }
        $this->submit_action = 'submitMarketPlaceEditCommission';
    }

    public function initProcess()
    {
        parent::initProcess();
    }

    public function postProcess()
    {
        if (Tools::isSubmit('submitMarketPlaceEditCommission')) {
            $this->action = 'EditCommission';
        }
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
    }

    public function renderForm()
    {
        $form = parent::renderForm();
        $tpl = $this->context->smarty->createTemplate(
            _PS_MODULE_DIR_ . $this->kb_module_name . '/views/templates/admin/editcommission.tpl'
        );
        $tpl->assign('form_fields', $form);
        return $tpl->fetch();
    }

    public function initContent()
    {
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    public function processEditCommission()
    {
        $error = false;
        if (!Validate::isInt(Tools::getValue('commission'))) {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('Only numeric value is allowed in Default Commission.', 'adminkbeditcommissioncontroller')
            );
            $error = true;
        }
        if ($error == false) {
            $where = 'id_seller_product = ' . (int) Tools::getValue("id_seller_product");
            $field = array(
                'use_global' => pSQL(Tools::getValue('use_global')),
                'commission' => pSQL(Tools::getValue('commission')),
            );
            Db::getInstance()->update('kb_mp_seller_product', $field, $where);
        }
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbEditCommission')."&id_seller_product=".Tools::getValue("id_seller_product"));
    }
}

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

class AdminKbSellerListController extends AdminKbMarketplaceCoreController
{
    public function __construct()
    {
        parent::__construct();
        $this->bootstrap = true;
        $this->table = 'kb_mp_seller';
        $this->className = 'KbSeller';
        $this->identifier = 'id_seller';
        $this->deleted = false;
        $this->lang = false;
        $this->display = 'list';
        $this->toolbar_title = $this->module->l('Sellers', 'adminkbsellerlistcontroller');

        $tmp = Country::getCountries($this->context->language->id, false, false, false);
        $country_array = array();
        foreach ($tmp as $row) {
            $country_array[$row['id_country']] = $row['country'];
        }

        $this->fields_list = array(
            'id_seller' => array(
                'title' => $this->module->l('ID', 'adminkbsellerlistcontroller'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'firstname' => array(
                'title' => $this->module->l('First Name', 'adminkbsellerlistcontroller'),
                'havingFilter' => true,
                'search' => true,
                'filter_key' => 'c!firstname',
            ),
            'lastname' => array(
                'title' => $this->module->l('Last Name', 'adminkbsellerlistcontroller'),
                'havingFilter' => true,
                'search' => true,
                'filter_key' => 'c!lastname',
            ),
            'email' => array(
                'title' => $this->module->l('Email', 'adminkbsellerlistcontroller'),
                'havingFilter' => true,
                'filter_key' => 'c!email',
                'search' => true
            ),
            'title' => array(
                'title' => $this->module->l('Shop', 'adminkbsellerlistcontroller'),
                'havingFilter' => true,
                'filter_key' => 'sl!title',
                'order_key' => 'sl.title',
            ),
            'state' => array(
                'title' => $this->module->l('State', 'adminkbsellerlistcontroller'),
                'havingFilter' => true,
                'search' => true
            ),
            'cname' => array(
                'title' => $this->module->l('Country', 'adminkbsellerlistcontroller'),
                'type' => 'select',
                'list' => $country_array,
                'havingFilter' => true,
                'filter_key' => 'a!id_country',
                'filter_type' => 'int',
                'order_key' => 'cname',
                'search' => true
            ),
            'active' => array(
                'title' => $this->module->l('Status', 'adminkbsellerlistcontroller'),
                'align' => 'text-center',
                'active' => 'status',
//                'havingFilter' => true,
                'type' => 'bool',
                'order_key' => 'active',
                'search' => true
            ),
            'date_add' => array(
                'title' => $this->module->l('Seller Since', 'adminkbsellerlistcontroller'),
                'align' => 'text-right',
                'havingFilter' => true,
                'type' => 'date',
                'filter_key' => 'a!date_add'
            )
        );

        $this->_select = '
			a.active, sl.title,
			c.`firstname`, c.`lastname`, c.`email`, 
			country_lang.name as cname, a.date_upd';

        $this->_join = '
			LEFT JOIN `' . _DB_PREFIX_ . $this->table . '_lang` sl ON (a.`id_seller` = sl.`id_seller` 
			AND sl.id_lang = ' . (int)$this->context->language->id . ') 
			LEFT JOIN `' . _DB_PREFIX_ . 'customer` c ON (c.`id_customer` = a.`id_customer`) 
			LEFT JOIN `' . _DB_PREFIX_ . 'country` country ON a.id_country = country.id_country 
			LEFT JOIN `' . _DB_PREFIX_ . 'country_lang` country_lang 
			ON (country.`id_country` = country_lang.`id_country` 
			AND country_lang.`id_lang` = ' . (int)$this->context->language->id . ')';

        $this->_orderBy = 'a.id_seller';
        $this->_orderWay = 'DESC';

        $this->_where .= ' AND a.approved = ' . (int)KbGlobal::APPROVED;

        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->bulk_actions = array();
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);
    }

    public function initContent()
    {
        $base_link = KbGlobal::getBaseLink((bool) Configuration::get('PS_SSL_ENABLED'));
        $tpl = $this->custom_smarty->createTemplate('free_block.tpl');
        $this->content .= $tpl->fetch();
        parent::initContent();
    }

    public function initToolbar()
    {
        parent::initToolbar();
        unset($this->toolbar_btn['new']);
    }

    protected function processBulkStatusSelection($status)
    {
        $result = true;
        if (is_array($this->boxes) && !empty($this->boxes)) {
            foreach ($this->boxes as $id) {
                $object         = new $this->className((int) $id);
                $before_update  = (int) $object->active;
                $object->active = (int) $status;
                $update_status  = $object->update(true);
                if ($update_status) {
                    if ($before_update != (int) $status) {
                        if ($before_update === 1) {
                            KbSellerProduct::trackAndUpdateProduct($object->id, 0);
                        } elseif ($before_update === 0) {
                            KbSellerProduct::trackAndUpdateProduct($object->id, 1);
                        }
                    }
                }
                $result &= $update_status;
            }
        }
        return $result;
    }

    public function initPageHeaderToolbar()
    {
        if (!empty($this->display) && $this->display == 'view') {
            $seller = $this->loadObject(true);
            if (Validate::isLoadedObject($seller)) {
                $seller_info = $seller->getSellerInfo();
                $this->toolbar_title = sprintf($this->module->l('Information about Seller: %s', 'adminkbsellerlistcontroller'), $seller_info['seller_name']);
            }
            $this->page_header_toolbar_btn['cancel'] = array(
                'href' => self::$currentIndex . '&token=' . $this->token,
                'desc' => $this->module->l('Back to List', 'adminkbsellerlistcontroller'),
                'icon' => 'process-icon-cancel'
            );
        } elseif (!empty($this->display) && ($this->display == 'update' || $this->display == 'edit')) {
            $seller = $this->loadObject(true);
            if (Validate::isLoadedObject($seller)) {
                $seller_info = $seller->getSellerInfo();
                Tools::redirectAdmin(
                    $this->context->link->getAdminLink('AdminCustomers')
                    .'&updatecustomer&id_customer='.$seller_info['id_customer']
                );
            }
        }

        parent::initPageHeaderToolbar();
    }

    public function processStatus()
    {
        $seller        = new KbSeller(Tools::getValue($this->identifier, 0));
        $before_update = (int) $seller->active;
        if (Validate::isLoadedObject($object = $this->loadObject())) {
            // Object must have a variable called 'active'
            if (!array_key_exists('active', $object)) {
                throw new PrestaShopException($this->module->l('Property active is missing in object ', 'adminkbsellerlistcontroller').get_class($this));
            }

            // Update only active field
            $object->setFieldsToUpdate(array('active' => true));

            // Update active status on object
            $object->active = !(int) $object->active;

            // Change status to active/inactive
            $is_update = $object->update(true);

            if ($is_update) {
//                if ($object->active == 1) {
//                } else {
//                    $id_seller       = $object->id;
//                    $seller_products = KbSellerProduct::getSellerProducts(
//                        $id_seller,
//                        false,
//                        null,
//                        null,
//                        null,
//                        null,
//                        null,
//                        0
//                    );
//
//                    foreach ($seller_products as $product) {
//                        $id_product  = $product['id_product'];
//                        $product_obj = new Product($id_product);
//                        if ($product_obj->active != 0) {
//                            $product_obj->active = 0;
//                            $product_obj->update();
//                        }
//                    }
//                }

                if ($before_update !== !(int) $object->active) {
                    $email_template_code = 'mp_seller_account_disable';
                    if ($before_update === 1) {
                        KbSellerProduct::trackAndUpdateProduct($seller->id, 0);
                    } elseif ($before_update === 0) {
                        $email_template_code = 'mp_seller_account_enable';
                        KbSellerProduct::trackAndUpdateProduct($seller->id, 1);
                    }

                }
                $matches = array();
                if (preg_match('/[\?|&]controller=([^&]*)/', (string) $_SERVER['HTTP_REFERER'], $matches)
                    !== false
                    && Tools::strtolower($matches[1]) !=
                    Tools::strtolower(preg_replace('/controller/i', '', get_class($this)))) {
                    $this->redirect_after = preg_replace(
                        '/[\?|&]conf=([^&]*)/i',
                        '',
                        (string) $_SERVER['HTTP_REFERER']
                    );
                } else {
                    $this->redirect_after = self::$currentIndex.'&token='.$this->token;
                }

                $page = (int) Tools::getValue('page');
                $page = $page > 1 ? '&submitFilter'.$this->table.'='.(int) $page
                        : '';
                $this->redirect_after .= $page;
            } else {
                $this->errors[] = Tools::displayError($this->module->l('An error occurred while updating the status.', 'adminkbsellerlistcontroller'));
            }
        } else {
            $this->errors[] = Tools::displayError($this->module->l('An error occurred while updating the status for an object.', 'adminkbsellerlistcontroller'))
                .' <b>'.$this->table.'</b> '.Tools::displayError($this->module->l('(cannot load object)', 'adminkbsellerlistcontroller'));
        }
        Hook::exec('actionKbMarketPlaceSellerStatusUpdate', array('seller' => $seller));
    }

    public function renderView()
    {
        $seller_obj = new $this->className(Tools::getValue($this->identifier, 0));
        if (!Validate::isLoadedObject($seller_obj)) {
            $this->context->cookie->__set(
                'kb_redirect_error',
                $this->module->l('You are trying to view information of missing seller.', 'adminkbsellerlistcontroller')
            );
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbSellerApprovalList'));
        } else {
            $customer       = new Customer($seller_obj->id_customer);
            $gender         = new Gender($customer->id_gender, $this->context->language->id);
            $seller_lang    = new Language($seller_obj->id_default_lang);
            $shop           = new Shop($seller_obj->id_shop);
            $total_products = KbSellerProduct::getSellerProducts(
                $seller_obj->id,
                true,
                null,
                null,
                null,
                null,
                null,
                0
            );

            
//echo($total_product_need_approval);die;
            if ($seller_obj->getSellerInfo()['description'] !=
                strip_tags($seller_obj->getSellerInfo()['description'])) {
                $seller_obj->getSellerInfo()['description'] = strip_tags($seller_obj->getSellerInfo()['description']);
            } else {
                $seller_obj->getSellerInfo()['description'] = $seller_obj->getSellerInfo()['description'];
            }
            if ($seller_obj->getSellerInfo()['return_policy'] !=
                strip_tags($seller_obj->getSellerInfo()['return_policy'])) {
                $seller_obj->getSellerInfo()['return_policy'] =
                    strip_tags($seller_obj->getSellerInfo()['return_policy']);
            } else {
                $seller_obj->getSellerInfo()['return_policy'] = $seller_obj->getSellerInfo()['return_policy'];
            }
            if ($seller_obj->getSellerInfo()['shipping_policy'] !=
                strip_tags($seller_obj->getSellerInfo()['shipping_policy'])) {
                $seller_obj->getSellerInfo()['shipping_policy'] = strip_tags($seller_obj->getSellerInfo()['shipping_policy']);
            } else {
                $seller_obj->getSellerInfo()['shipping_policy'] = $seller_obj->getSellerInfo()['shipping_policy'];
            }
            if ($seller_obj->getSellerInfo()['privacy_policy'] !=
                strip_tags($seller_obj->getSellerInfo()['privacy_policy'])) {
                $seller_obj->getSellerInfo()['privacy_policy'] = strip_tags($seller_obj->getSellerInfo()['privacy_policy']);
            } else {
                $seller_obj->getSellerInfo()['privacy_policy'] = $seller_obj->getSellerInfo()['privacy_policy'];
            }
            $tpl_vars = array(
                'seller_info' => $seller_obj->getSellerInfo(),
                'display_summary' => true,
                'total_products' => $total_products,
                'gender' => $gender,
                'registration_date' => Tools::displayDate($seller_obj->date_add, null, true),
                'shop_is_feature_active' => Shop::isFeatureActive(),
                'name_shop' => $shop->name,
                'customerLanguage' => $seller_lang,
            );

            $default_template_dir  = $this->context->smarty->getTemplateDir(0);
            $this->context->smarty->setTemplateDir(
                _PS_MODULE_DIR_.$this->kb_module_name.'/views/templates/admin/'
            );
            $this->override_folder = $this->tpl_folder      = 'sellerview/';
            $helper                = new HelperView($this);
            $this->setHelperDisplay($helper);
            $helper->tpl_vars      = $tpl_vars;
            if (!is_null($this->base_tpl_view)) {
                $helper->base_tpl = $this->base_tpl_view;
            }
            $view = $helper->generateView();
            $this->context->smarty->setTemplateDir($default_template_dir);
            return $view;
        }
    }

   
}

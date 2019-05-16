<?php
/**
 * Created by PhpStorm.
 * User: tien
 * Date: 12/21/17
 * Time: 18:03
 * @author : tien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmCheckout17 extends ModuleFrontController
{

    protected $template_dir;
    private $module_dir = "";
    private $module_url = "";

    private $registerForm;
    private $addressForm;
    public $module_name;
    public function init()
    {
        $this->context->smarty->addTemplateDir(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/');

        parent::init();

        $this->registerForm = $this->makeCustomerForm();
        $this->registerForm->fillFromCustomer(
            $this->context->customer
        );
        $this->template_dir = _PS_MODULE_DIR_ . $this->module_name ."/vendor/kien/prestashop-onepage-lib/src/views/templates/";

        $this->addressForm = $this->makeAddressForm();
        $this->addressForm->fillWith(array(
            'firstname' => $this->context->customer->firstname,
            'lastname' => $this->context->customer->lastname,
        ));
        echo "";
    }

    public function initContent()
    {
        parent::initContent();

        $this->_assignCountries();

//        $module_url = Tools::getProtocol(Tools::usingSecureMode()).
//$_SERVER['HTTP_HOST'].$this->module->getPathUri().$this->;
        $this->module_url = $this->context->link->getModuleLink($this->module_name, 'jmcheckout', array());

        $addresses = array();
        if ($this->context->customer->id != 0) {
            $addresses = $this->context->customer->getAddresses($this->context->language->id);
        }

        $selectedDeliveryAddressId = $this->context->cart->id_address_delivery;
        $selectedInvoiceAddressId = $this->context->cart->id_address_invoice;
        $this->context->smarty->assign(
            'selected_delivery_address_id',
            count($addresses) == 0 ? 0 : $selectedDeliveryAddressId
        );
        $this->context->smarty->assign(
            'selected_invoice_address_id',
            count($addresses) == 0 ? 0 : $selectedInvoiceAddressId
        );
        $this->context->smarty->assign('addresses', $addresses);
        $this->context->smarty->assign('lang_code', $this->context->language->iso_code);
        $this->context->smarty->assign('is_logged', (int) $this->context->customer->isLogged());
        $this->context->smarty->assign('customer', $this->context->customer);
        $this->context->smarty->assign('myopc_checkout_url', $this->module_url);
        $this->context->smarty->assign('template_dir', $this->template_dir);

        $this->context->smarty->assign(array(
            'register_form' => $this->registerForm->getProxy(),
            'address_form' => $this->addressForm->getProxy(),
            'guest_allowed' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED')
        ));
        $this->setTemplate('module:'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/jmcheckout.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();

        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->module_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
        } else {
            $this->module_dir = _PS_MODULE_DIR_.$this->module_name.'/';
        }

        $this->addJS(array(
            _THEME_JS_DIR_.'tools/vatManagement.js',
            $this->module_dir.'views/js/statesManagement.js',
            _THEME_JS_DIR_.'authentication.js',
            _PS_JS_DIR_.'validate.js'
        ));

        $this->addCSS($this->module_dir.'views/css/bootstrap.min.css');
        $this->addCSS($this->module_dir.'views/css/font-awesome-4.7.0/css/font-awesome.css');
        $this->addCSS($this->module_dir.'views/css/responsive.css');
        $this->addCSS($this->module_dir.'views/css/ladda.min.css');
        $this->addCSS($this->module_dir.'views/css/17/style.css');

        $lang_iso_code = $this->context->language->iso_code;
        if ($lang_iso_code == 'nl') {
            $this->addJS($this->module_dir.'views/js/libs/lang/nl.js');
        } elseif ($lang_iso_code == 'es') {
            $this->addJS($this->module_dir.'views/js/libs/lang/es.js');
        } elseif ($lang_iso_code == 'it') {
            $this->addJS($this->module_dir.'views/js/libs/lang/it.js');
        } elseif ($lang_iso_code == 'ar') {
            $this->addJS($this->module_dir.'views/js/libs/lang/ar.js');
        }

        $this->registerJavascript(
            'module-jmango360api-jquery.min.js',
            'modules/'.$this->module_name.'/views/js/libs/jquery.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap.min.js',
            'modules/'.$this->module_name.'/views/js/libs/bootstrap.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap-select.1.12.2.js',
            'modules/'.$this->module_name.'/views/js/libs/bootstrap-select.1.12.2.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap-select.js',
            'modules/'.$this->module_name.'/views/js/libs/bootstrap-select.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-jquery.min.js',
            'modules/'.$this->module_name.'/views/js/libs/jquery.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-collapse.js',
            'modules/'.$this->module_name.'/views/js/libs/collapse.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-jquery.form-validator.js',
            'modules/'.$this->module_name.'/views/js/libs/jquery.form-validator.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );

        $this->registerJavascript(
            'module-jmango360api-spin.min.js',
            'modules/'.$this->module_name.'/views/js/libs/spin.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-ladda.min.js',
            'modules/'.$this->module_name.'/views/js/libs/ladda.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-template.js',
            'modules/'.$this->module_name.'/views/js/template.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-jmcheckout.js',
            'modules/'.$this->module_name.'/views/js/17/jmcheckout.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
    }

    public function initHeader()
    {
        parent::initHeader();

        $this->context->smarty->assign('content_only', 1);
    }

    public function _assignCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $availableCountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $availableCountries = Country::getCountries($this->context->language->id, true);
        }

        $this->context->smarty->assign('countries', $availableCountries);
        $this->context->smarty->assign('default_country', (int)Configuration::get('PS_COUNTRY_DEFAULT'));
    }

    public function _assignDatetime()
    {
    }

    public function postProcess()
    {
        $requestParameters = Tools::getAllValues();
        if (Tools::isSubmit('ajax')) {
            $this->json = array();
            if (Tools::isSubmit('PlaceOrder')) {
//                $this->json = $this->placeOrder();
            } elseif (Tools::isSubmit('submitGuestAccount')) {
                $this->registerForm->fillWith($requestParameters);
                if ($this->registerForm->validate()) {
                    if ($this->registerForm->submit()) {
                        $billingContent = $this->loadAddressForm();
                        $this->json = array_merge(
                            $billingContent,
                            array(
                                "hasError" => false,
                                "goto_section" => "billing"
                            )
                        );
                    } else {
                        $this->json = array(
                            "hasError" => true,
                            "errors" => $this->registerForm->getErrors()
                        );
                    }
                } else {
                    $errs = array();
                    foreach ($this->registerForm->getErrors() as $field => $errors) {
                        foreach ($errors as $error) {
                            $errs[] = $field . ' : ' . $error;
                        }
                    }
                    $this->json = array(
                        "hasError" => true,
                        "errors" => $errs
                    );
                }
            } elseif (Tools::isSubmit('saveAddress')) {
                if (isset($requestParameters['billing_address_id'])) {
                    // Edit address
                    $requestParameters['id_address'] = $requestParameters['billing_address_id'];
                }

                $saved = $this->addressForm->fillWith($requestParameters)->submit();
                if (!$saved) {
                    $errs = array();
                    foreach ($this->addressForm->getErrors() as $field => $errors) {
                        foreach ($errors as $error) {
                            $errs[] = $field . ' : ' . $error;
                        }
                    }

                    $this->json = array(
                        "hasError" => true,
                        "errors" => $errs
                    );
                } else {
                    $id_address = $this->addressForm->getAddress()->id;
                    if ($requestParameters['saveAddress'] === 'invoice') {
                        $this->context->cart->updateAddressId($this->context->cart->id_address_invoice, $id_address);
                        $this->context->cart->id_address_invoice = $id_address;
                        $this->context->cart->save();
                        if ($requestParameters['use_for_shipping'] == 1) {
                            $this->context->cart->id_address_delivery = $id_address;
                            $this->context->cart->save();
                        }
                    } else {
                        $this->context->cart->id_address_delivery = $id_address;
                        $this->context->cart->save();
                    }

                    $addresses = array();
                    if ($this->context->customer->id != 0) {
                        $addresses = $this->context->customer->getAddresses($this->context->language->id);
                    }

                    $this->json = array(
                        "hasError" => false,
                        'id_address_delivery' => $this->context->cart->id_address_delivery,
                        'id_address_invoice' => $this->context->cart->id_address_invoice,
                        'addresses' => $addresses,
                    );
                }
            } elseif (Tools::isSubmit('updateCartAddress')) {
//                $this->json = $this->updateCartAddress();
            } elseif (Tools::isSubmit('createAddress')) {
//                $this->json = $this->createNewAddress();
            }

            if (Tools::isSubmit('submitDiscount')) {
//                if ($this->nb_products)
//                    $this->json = $this->addCartRule();
//                else {
//                    $this->errors[] = $this->module->l('Your shopping cart is empty.', 'jmcheckout');
//                    $this->json = array(
//                        'hasError' => !empty($this->errors),
//                        'errors' => $this->errors
//                    );
//                }
            } elseif (Tools::isSubmit('deleteDiscount')) {
//                if ($this->nb_products)
//                    $this->json = $this->removeDiscount();
//                else {
//                    $this->errors[] = $this->module->l('Your shopping cart is empty.', 'jmcheckout');;
//                    $this->json = array(
//                        'hasError' => !empty($this->errors),
//                        'errors' => $this->errors
//                    );
//                }
            } elseif (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'changeCountry':
                        $this->json = $this->loadAddressForm();
                        break;
                    case 'getCarrierList':
//                        $this->json = $this->getCarrierList(Tools::getValue('id_country'),
//                            Tools::getValue('id_state'),
//                            Tools::getValue('postcode'),
//                            Tools::getValue('city'),
//                            Tools::getValue('id_address_delivery'));
                        break;
                    case 'updateCarrier':
//                        $this->json = $this->updateCarrier();
                        break;
                    case 'loadCart':
//                        $this->json = $this->loadCart();
                        break;
                    case 'loadPayment':
//                        $this->json = $this->loadPayments(Tools::getValue('id_country'),
//                        Tools::getValue('id_address_delivery'),Tools::getValue('selected_payment_method_id'), true);
                        break;
                    case 'updatePayment':
//                        $this->json = $this->updatePayment();
                        break;
                }
            }
            echo Tools::jsonEncode($this->json);
            die;
        }
    }

    protected function loadAddressForm()
    {
        $addressForm = $this->makeAddressForm();
        $addressForm->fillWith(array(
            'firstname' => $this->context->customer->firstname,
            'lastname' => $this->context->customer->lastname,
        ));

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(array('id_country' => Tools::getValue('id_country')));
        }

        $templateParams = array_merge(
            $addressForm->getTemplateVariables(),
            array(
                'type' => 'invoice',
                'guest_allowed' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'myopc_checkout_url' => $this->module_url,
                'template_dir' => $this->template_dir
            )
        );

        ob_end_clean();
        header('Content-Type: application/json');
        $templateContent = '';
        $scope = $this->context->smarty->createData(
            $this->context->smarty
        );
        $scope->assign($templateParams);

        try {
            $tpl = $this->context->smarty->createTemplate(
                'modules/'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/_partials/address-form.tpl',
                $scope
            );
            $templateContent = $tpl->fetch();
        } catch (PrestaShopException $e) {
            PrestaShopLogger::addLog($e->getMessage());

            if (defined('_PS_MODE_DEV_') && _PS_MODE_DEV_) {
                $this->warning[] = $e->getMessage();
                $scope->assign(array('notifications' => $this->prepareNotifications()));

                $tpl = $this->context->smarty->createTemplate(
                    $this->getTemplateFile('_partials/notifications'),
                    $scope
                );

                $templateContent = $tpl->fetch();
            }
        }

        return array(
            'updated_section' => $templateContent
        );
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: tien
 * Date: 12/21/17
 * Time: 18:03
 * @author tien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmCheckout16 extends ModuleFrontController
{
    private $template_dir;
    private $module_dir = "";
    private $image_extensions = array('.gif', '.png', '.jpg', '.jpeg');
    public $create_account;
    public $json = array();
    protected $nb_products;
    public $default_shipping_selected;
    public $is_logged;
    public $errors = array();
    private $shipping_error = array();
    public $module_name;
    /**
     * JmCheckout16 constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->ajax = $this->isAjax();
    }

    /**
     * Check AJAX request
     */
    protected function isAjax()
    {
        return $this->isXmlHttpRequest() && Tools::isSubmit('ajax');
    }

    public function init()
    {
        parent::init();

        if (Tools::getIsset('XDEBUG_SESSION_START')) {
            Configuration::set('PS_JS_THEME_CACHE', 0);
        }

        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->module_dir = __PS_BASE_URI__ . 'modules/' . $this->name . '/';
        } else {
            $this->module_dir = _PS_MODULE_DIR_ . $this->module_name .'/';
        }

        if ($this->context->customer->isLogged()) {
            $this->is_logged = true;
        } else {
            $this->is_logged = false;
        }

        $this->nb_products = $this->context->cart->nbProducts();

        //PS-412 [Prestashop] Impossible to checkout Onepage in Prestashop 16 app (install 1.11.0)
        // Build a classic url index.php?controller=foo&...

        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $useSSL = ((isset($this->ssl) && $this->ssl && Configuration::get('PS_SSL_ENABLED')) || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $link = new JmLink($protocol_link, $protocol_content);
        $this->context->smarty->registerObject('linkJm', $link);
        $this->context->smarty->assign('linkJm', $link);
        $this->context->smarty->assign('module_name', $this->module_name);
    }

    public function initContent()
    {
        parent::initContent();

        if ($this->nb_products == 0) {
            $this->context->smarty->assign('empty', 1);
        }

        $checkout_url = __PS_BASE_URI__ . 'index.php?fc=module&module='.$this->module_name.'&controller=jmcheckout';

//        $default_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
//        $countries = Country::getCountries((int)$this->context->cookie->id_lang, true);

        $default_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
        // current country
        $current_country = (int)Tools::getCountry();

        /*
         * Fix for validation_plugin variable => $id_address_delivery, $id_address_invoice, $customer_name
         *  not use.
         */
//        $id_address_delivery = 0;
//        $id_address_invoice = 0;
        if ($this->is_logged) {
            /*
            * fix for validation_plugin $presentedCart not use.
            * Before :    $customer_name = $this->context->customer->firstname.' '.$this->context->customer->lastname;
            * $id_address_delivery = $this->context->cart->id_address_delivery;
            * $id_address_invoice = $this->context->cart->id_address_invoice;
            */
            $this->context->customer->firstname . ' ' . $this->context->customer->lastname;
            $id_address_delivery = $this->context->cart->id_address_delivery;
            $id_address_invoice = $this->context->cart->id_address_invoice;
        } else {
            $this->context->cart->id_customer = 0;
        }

        /**
         * Check if checkout with Guest will genarate form field as..
         */
        $current_country_temp = ((int)Tools::getValue('id_country') > 0) ? Tools::getValue('id_country') : $current_country;

        if (!$this->is_logged && ((int)Configuration::get('PS_GUEST_CHECKOUT_ENABLED') == 0)) {
            //
        } else {
            $this->initFormFieldBillingAddress($current_country_temp);
            $this->getAlertForCreateNewAccount($current_country_temp);
        }
        $selectedDeliveryAddressId = $this->context->cart->id_address_delivery;
        $selectedInvoiceAddressId = $this->context->cart->id_address_invoice;

        $this->getCarrierList($default_country, 0, '', '', $selectedDeliveryAddressId, null);
        $payment_methods = $this->loadPaymentMethods();
        $customer = $this->context->customer;
        $addresses = array();
        if ($this->context->customer->id != 0) {
            $addresses = $customer->getAddresses($this->context->language->id);
        }
        $countries = $this->loadCountries();
        $summary = $this->loadCart();

        $idSelectedState = 0;
        $idSelectedInvoiceState = 0;

        $this->context->smarty->assign('idSelectedState', 0);
        $this->context->smarty->assign('idSelectedInvoiceState', 0);

        //old message to delivety shipping.
        $old_message = $this->constructMessageToCart();
        $this->context->smarty->assign('oldMessage', $old_message['message']);

        if (CustomerCore::customerHasAddress($customer->id, $selectedDeliveryAddressId)) {
            $address = new Address($selectedDeliveryAddressId);
            $idSelectedState = $address->id_state;
            if ($idSelectedState) {
                $this->context->smarty->assign('idSelectedState', $idSelectedState);
            }
        }
        if (CustomerCore::customerHasAddress($customer->id, $selectedInvoiceAddressId)) {
            $address = new Address($selectedInvoiceAddressId);
            $idSelectedInvoiceState = $address->id_state;
            if ($idSelectedInvoiceState) {
                $this->context->smarty->assign('idSelectedInvoiceState', $idSelectedInvoiceState);
            }
        }
        // TOS
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $link_conditions = $this->context->link->getCMSLink(
            $cms,
            $cms->link_rewrite,
            (bool)Configuration::get('PS_SSL_ENABLED')
        );
        $message_phone = Tools::displayError('You must register at least one phone number.');

        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();

        $this->assignAddressFormat();

        $this->context->smarty->assign(array(
            'years' => $years,
            'months' => $months,
            'days' => $days,
        ));

        $this->template_dir = _PS_MODULE_DIR_ .$this->module_name. "/vendor/kien/prestashop-onepage-lib/src/views/templates/";

        $this->context->smarty->assign('enable_coupon_onepage', Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE));
        $this->context->smarty->assign('cms_id', (int)Configuration::get('PS_CONDITIONS_CMS_ID'));
        $this->context->smarty->assign('conditions', (int)Configuration::get('PS_CONDITIONS'));
        $this->context->smarty->assign('link_conditions', $link_conditions);
        $this->context->smarty->assign('lang_code', $this->context->language->iso_code);
        $this->context->smarty->assign('genders', Gender::getGenders());
        $this->context->smarty->assign('default_country', $default_country);
        $this->context->smarty->assign('current_country', $current_country);
        $this->context->smarty->assign('template_dir', $this->template_dir);
        $this->context->smarty->assign('myopc_checkout_url', $checkout_url);
        $this->context->smarty->assign('payment_methods', $payment_methods);
        $this->context->smarty->assign('message_phone', $message_phone);
        $this->context->smarty->assign('is_logged', (int)$this->is_logged);
        $this->context->smarty->assign('customer', $customer);
        $this->context->smarty->assign('company_create_new', $this->context->customer->company);
        $this->context->smarty->assign('one_phone_at_least', (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'));
        $this->context->smarty->assign(
            'selected_delivery_address_id',
            count($addresses) == 0 ? 0 : $selectedDeliveryAddressId
        );
        $this->context->smarty->assign(
            'selected_invoice_address_id',
            count($addresses) == 0 ? 0 : $selectedInvoiceAddressId
        );

        $this->context->smarty->assign('addresses', $addresses);
        $this->context->smarty->assign('countries', $countries);
        $this->context->smarty->assign('summary', $summary);

        /**
         * Support custom CSS and JS in checkout
         */
        $customCss = CheckoutSettingsService::getCheckoutCustomCss();
        $this->context->smarty->assign('custom_css', $customCss);
        $customJS = CheckoutSettingsService::getCheckoutCustomJs();
        $this->context->smarty->assign('custom_js', $customJS);

        $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/billing-address-form.tpl');
        $this->context->smarty->assign('output', ($output));
        if ($this->isAjax()) {
            return;
        }
        $this->setTemplate('jmcheckout.tpl');
    }

    public function postProcess()
    {
        if ($this->isAjax()) {
            $this->json = array();
            if (Tools::isSubmit('ReloadFormField')) {
                $id_country = (int)Tools::getValue('id_country');
                $this->initFormFieldBillingAddress($id_country);
            }
            if (Tools::isSubmit('getShippingAddressHtml')) {
                $id_country = (int)Tools::getValue('id_country');
                $this->getAddressFormFields('shipping', $id_country);
            } elseif (Tools::isSubmit('PlaceOrder')) {
                $this->json = $this->placeOrder();
            } elseif (Tools::isSubmit('submitGuestAccount')) {
//                $this->json = $this->createNewGuestAccount();
                $this->json = $this->submitBilling();
            } elseif (Tools::isSubmit('updateCartAddress')) {
                $this->json = $this->updateCartAddress();
            } elseif (Tools::isSubmit('createAddress')) {
                $this->json = $this->createNewAddress();
            } elseif (Tools::isSubmit('get_countries')) {
                $this->getCountries();
            }

            if (Tools::isSubmit('submitDiscount')) {
                if ($this->nb_products) {
                    $this->json = $this->addCartRule();
                } else {
                    $this->errors[] = $this->module->l('Your shopping cart is empty.', 'JmCheckout16');
                    $this->json = array(
                        'hasError' => !empty($this->errors),
                        'errors' => $this->errors
                    );
                }
            } elseif (Tools::isSubmit('deleteDiscount')) {
                if ($this->nb_products) {
                    $this->json = $this->removeDiscount();
                } else {
                    $this->errors[] = $this->module->l('Your shopping cart is empty.', 'JmCheckout16');
                    $this->json = array(
                        'hasError' => !empty($this->errors),
                        'errors' => $this->errors
                    );
                }
            } elseif (Tools::isSubmit('method')) {
                switch (Tools::getValue('method')) {
                    case 'getCarrierList':
                        $this->json = $this->getCarrierList(
                            Tools::getValue('id_country'),
                            Tools::getValue('id_state'),
                            Tools::getValue('postcode'),
                            Tools::getValue('city'),
                            Tools::getValue('id_address_delivery')
                        );
                        break;
                    case 'updateCarrier':
                        $this->json = $this->updateCarrier();
                        break;
                    case 'loadCart':
                        $this->json = $this->loadCart();
                        break;
                    case 'loadPayment':
                        $this->json = $this->loadPayments(
                            Tools::getValue('id_country'),
                            Tools::getValue('id_address_delivery'),
                            Tools::getValue('selected_payment_method_id')
                        );
                        break;
                    case 'updatePayment':
                        $this->json = $this->updatePayment();
                        break;
                }
            }
            echo Tools::jsonEncode($this->json);
            die;
        }
    }

    public function createNewGuestAccount()
    {
        $current_step = Tools::getValue('step');
        $billing_address = null;
        $shipping_address = null;
        $id_billing_address = Tools::getValue('billing_address_id') ? Tools::getValue('billing_address_id') : 0;
        $id_shipping_address = Tools::getValue('shipping_address_id') ? Tools::getValue('shipping_address_id') : 0;


        if (!$id_shipping_address) {
            $shipping_params = Tools::getValue('shipping');
            $id_shipping_address = (isset($shipping_params['address_id']) && $shipping_params['address_id']) ? $shipping_params['address_id'] : 0;
        }

        $billing_params = Tools::getValue('billing');

        $use_for_shipping = isset($billing_params['use_for_shipping']) ? $billing_params['use_for_shipping'] : 0;
        $shipping_params = Tools::getValue('shipping');
        $same_as_billing = isset($shipping_params['same_as_billing']) ? $shipping_params['same_as_billing'] : 0;
        $email = isset($billing_params['email']) ? ($billing_params['email']) : null;
        $customer = $this->context->customer;
        $check_is_logged = isset($billing_params['is_logged']) ? ($billing_params['is_logged']) : 1;

        if (($check_is_logged == 0) || !isset($customer) || (isset($customer) && $customer->id == 0)) {
            if (!Validate::isEmail($email) || empty($email)) {
                $this->errors[] = Tools::displayError('Invalid email address.');
            } elseif (Customer::customerExists($email)) {
                $this->errors[] = Tools::displayError(
                    'An account using this email address has already been registered.'
                );
            } else {
                $this->create_account = true;
                $billing_params['email'] = $email;
            }

            $this->create_account = true;
            // New Guest customer

            if (!Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
                $this->errors[] = Tools::displayError('You cannot create a guest account.');
            }
            $billing_params['passwd'] = md5(time() . _COOKIE_KEY_);
            $customer = new Customer();
            $lastname = $billing_params['lastname'];
            $firstname = $billing_params['firstname'];
            $idGender = (isset($billing_params['gender_id']) ? $billing_params['gender_id'] : '');

            // PS-861 : [Ps 16] Onepage checkout - Display wrong error message  in case I input invalid Date of birth.
            $birthDay = (empty($billing_params['years']) ? '' : (int)$billing_params['years'] . '-' . (int)$billing_params['months'] . '-' . (int)$billing_params['days']);
            if (!@checkdate($billing_params['months'], $billing_params['days'], $billing_params['years']) && !($billing_params['months'] == '' && $billing_params['days'] == '' && $billing_params['years'] == '')) {
                $this->errors[] = Tools::displayError('Invalid date of birth');
            } else {
                if (!Validate::isBirthDate($birthDay)) {
                    $this->errors[] = Tools::displayError('Invalid date of birth');
                }
            }

            $customer->days = (int)$billing_params['days'];
            $customer->years = (int)$billing_params['years'];
            $customer->months = (int)$billing_params['months'];

            $customer->optin = Tools::getValue('optin');
            $customer->newsletter = Tools::getValue('newsletter');

            $customer->birthday = $birthDay;
            $customer->id_gender = $idGender;
            $customer->firstname = $firstname;
            $customer->lastname = $lastname;
            $customer->passwd = md5(time() . _COOKIE_KEY_);
            $customer->email = $email;
            $customer->active = 1;
            $customer->is_guest = 1;

            $this->errors = array_unique(array_merge($this->errors, $customer->validateController()));
            $this->errors = $this->errors + $customer->validateFieldsRequiredDatabase();

            //convert err.
            if (!count($this->errors)) {
                if (!$customer->add()) {
                    $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                }
            }
        }
        // Validate address for guest.
        // PS-785 : Onepage - Value of some field are removed and I can continue checkout when continue checkout as guest again after come back to shopping cart.

        if ((!count($this->errors)) && (!$this->is_logged) && ($current_step == 'billing')) {
            $address = new Address();
            $address->id_country = (isset($billing_params['country_id']) && ($billing_params['country_id'] != '')) ? $billing_params['country_id'] : null;
            $address->address2 = (isset($billing_params['street2']) && ($billing_params['street2'] != ' ')) ? $billing_params['street2'] : null;
            $address->address1 = (isset($billing_params['street']) && ($billing_params['street'] != "")) ? $billing_params['street'] : null;
            $address->firstname = (isset($billing_params['firstname']) && ($billing_params['firstname'] != '')) ? $billing_params['firstname'] : null;
            $address->lastname = (isset($billing_params['lastname']) && ($billing_params['lastname'] != '')) ? $billing_params['lastname'] : null;
            $address->city = (isset($billing_params['city']) && ($billing_params['city'] != '')) ? $billing_params['city'] : null;
            $address->company = (isset($billing_params['company']) && ($billing_params['company'] != '')) ? $billing_params['company'] : null;
            $address->postcode = (isset($billing_params['postcode']) && ($billing_params['postcode'] != '')) ? $billing_params['postcode'] : null;
            $address->other = (isset($billing_params['other']) && ($billing_params['other'] != '')) ? $billing_params['other'] : null;
            $address->phone = (isset($billing_params['telephone']) && ($billing_params['telephone'] != '')) ? $billing_params['telephone'] : null;
            $address->phone_mobile = (isset($billing_params['phone_mobile']) && ($billing_params['phone_mobile'] != '')) ? $billing_params['phone_mobile'] : null;
            $address->dni = (isset($billing_params['dni']) && ($billing_params['dni'] != '')) ? $billing_params['dni'] : null;
            $address->vat_number = (isset($billing_params['vat_number']) && ($billing_params['vat_number'] != '')) ? $billing_params['vat_number'] : null;
            $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
        }

        $temp = array();
        foreach ($this->errors as $err) {
            $temp[] = $err;
        }
        $this->errors = $temp;

        // Validate billing address
        if (!count($this->errors)) {
            if ($current_step == 'billing') {
                if ($id_billing_address == 0
                    || ($id_billing_address != 0 && $customer->is_guest)
                    || ($id_billing_address != 0 && $billing_params['edit'] == 1)) {
                    if (!($country = new Country($billing_params['country_id']))
                        || !Validate::isLoadedObject($country)) {
                        $this->errors[] = htmlspecialchars_decode(
                            Tools::displayError('Country cannot be loaded with address->id_country'),
                            ENT_QUOTES
                        );
                    }

                    if (!$country->active) {
                        $this->errors[] = Tools::displayError('This country is not active.');
                    }

                    $postcode = $billing_params['postcode'];
                    /* Check zip code format */
                    if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                        $this->errors[] = htmlspecialchars_decode(sprintf(
                            Tools::displayError('The Zip/Postal code you\'ve entered is invalid.
                            It must follow this format: %s'),
                            str_replace(
                                'C',
                                $country->iso_code,
                                str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
                            )
                        ), ENT_QUOTES);
                    } elseif (empty($postcode) && $country->need_zip_code) {
                        $this->errors[] = htmlspecialchars_decode(
                            Tools::displayError('A Zip / Postal code is required.'),
                            ENT_QUOTES
                        );
                    } elseif ($postcode && !Validate::isPostCode($postcode)) {
                        $this->errors[] = htmlspecialchars_decode(
                            Tools::displayError('The Zip / Postal code is invalid.'),
                            ENT_QUOTES
                        );
                    }

                    if ($country->need_identification_number
                        && (!$billing_params['dni']
                            || !Validate::isDniLite($billing_params['dni']))) {
                        $this->errors[] = Tools::displayError(
                            'The identification number is incorrect or has already been used.'
                        );
                    } elseif (!$country->need_identification_number) {
                        $billing_params['dni'] = null;
                    }

                    $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states : 0;
                    $id_state = isset($billing_params)
                    && isset($billing_params['state_id']) ? (int)$billing_params['state_id'] : 0;
                    if ((Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state) {
                        $this->errors[] = Tools::displayError('This country requires you to choose a State.');
                    }
                    if (!$this->context->customer->is_guest
                        && !empty($billing_params['alias'])
                        && (int)$this->context->customer->id > 0) {
                        if (Address::aliasExist(
                            $billing_params['alias'],
                            $id_billing_address,
                            (int)$this->context->customer->id
                        )) {
                            $this->errors[] = htmlspecialchars_decode(sprintf(
                                Tools::displayError('The alias "%s" has already been used. Please select another one.'),
                                $billing_params['alias']
                            ), ENT_QUOTES);
                        }
                    }
                    // Check phone
                    if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && empty($billing_params['telephone']) && empty($billing_params['phone_mobile'])) {
                        $this->errors[] = Tools::displayError('You must register at least one phone number.');
                    }
                }
            } else {
                if ($id_shipping_address == 0
                    || ($id_shipping_address != 0 && $customer->is_guest)
                    || ($id_shipping_address != 0 && $shipping_params['edit'] == 1)
                ) {
                    if (!($country = new Country($shipping_params['country_id']))
                        || !Validate::isLoadedObject($country)) {
                        $this->errors[] = htmlspecialchars_decode(
                            Tools::displayError('Country cannot be loaded with address->id_country'),
                            ENT_QUOTES
                        );
                    }

                    if (!$country->active) {
                        $this->errors[] = Tools::displayError('This country is not active.');
                    }

                    $postcode = $shipping_params['postcode'];
                    /* Check zip code format */
                    if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                        $this->errors[] = htmlspecialchars_decode(sprintf(
                            Tools::displayError(
                                'The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'
                            ),
                            str_replace(
                                'C',
                                $country->iso_code,
                                str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
                            )
                        ), ENT_QUOTES);
                    } elseif (empty($postcode) && $country->need_zip_code) {
                        $this->errors[] = htmlspecialchars_decode(Tools::displayError(
                            'A Zip / Postal code is required.'
                        ), ENT_QUOTES);
                    } elseif ($postcode && !Validate::isPostCode($postcode)) {
                        $this->errors[] = htmlspecialchars_decode(Tools::displayError(
                            'The Zip / Postal code is invalid.'
                        ), ENT_QUOTES);
                    }

                    if ($country->need_identification_number
                        && (!$shipping_params['dni']
                            || !Validate::isDniLite($shipping_params['dni']))) {
                        $this->errors[] = Tools::displayError(
                            'The identification number is incorrect or has already been used.'
                        );
                    } elseif (!$country->need_identification_number) {
                        $billing_params['dni'] = null;
                    }

                    $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states : 0;
                    $id_state = isset($shipping_params)
                    && isset($shipping_params['state_id']) ? (int)$shipping_params['state_id'] : 0;
                    if ((Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state) {
                        $this->errors[] = Tools::displayError('This country requires you to choose a State.');
                    }
                    if (!$this->context->customer->is_guest
                        && !empty($shipping_params['alias'])
                        && (int)$this->context->customer->id > 0) {
                        if (Address::aliasExist($shipping_params['alias'], $id_shipping_address, (int)$this->
                        context->customer->id)) {
                            $this->errors[] = htmlspecialchars_decode(sprintf(Tools::displayError(
                                'The alias "%s" has already been used. Please select another one.'
                            ), Tools::safeOutput($shipping_params['alias'])), ENT_QUOTES);
                        }
                    }
                }
            }
        }

        if (!count($this->errors)) {
            if ($current_step == 'billing') {
                if ($id_billing_address == 0) {
                    $billing_address = new Address();
                } else {
                    $billing_address = new Address($id_billing_address);

                    if ($use_for_shipping) {
                        $id_shipping_address = $id_billing_address;
                        $shipping_address = new Address($id_shipping_address);
                    }
                }

                if ($id_billing_address == 0
                    || ($id_billing_address != 0
                        && $customer->is_guest)
                    || ($id_billing_address != 0
                        && $billing_params['edit'] == 1)) {
                    $billing_address->firstname = (!empty($billing_params['firstname'])) ?
                        $billing_params['firstname'] : ' ';
                    $billing_address->lastname = (!empty($billing_params['lastname'])) ?
                        $billing_params['lastname'] : ' ';
                    $billing_address->company = (!empty($billing_params['company'])) ? $billing_params['company'] : ' ';
                    $billing_address->vat_number = (!empty($billing_params['vat_number'])) ? $billing_params['vat_number'] : ' ';
                    $billing_address->address1 = (!empty($billing_params['street'])) ? $billing_params['street'] : ' ';
                    $billing_address->address2 = (!empty($billing_params['street2'])) ?
                        $billing_params['street2'] : ' ';
                    $billing_address->city = (!empty($billing_params['city'])) ? $billing_params['city'] : ' ';
                    $billing_address->phone = (!empty($billing_params['telephone'])) ?
                        $billing_params['telephone'] : '';
                    $billing_address->phone_mobile = (!empty($billing_params['phone_mobile'])) ?
                        $billing_params['phone_mobile'] : ' ';
                    $billing_address->id_country = (!empty($billing_params['country_id'])) ?
                        $billing_params['country_id'] : (int)Configuration::get('PS_COUNTRY_DEFAULT');
                    if (Country::getNeedZipCode($billing_address->id_country)) {
                        $billing_address->postcode = $billing_params['postcode'];
                    }
                    $billing_address->id_state = (!empty($billing_params['state_id'])) ?
                        $billing_params['state_id'] : 0;

                    if (!Country::containsStates($billing_address->id_country)) {
                        $billing_address->id_state = 0;
                    }
                    $billing_address->vat_number = (!empty($billing_params['vat_number'])) ?
                        $billing_params['vat_number'] : '';

                    if (Country::isNeedDniByCountryId($billing_address->id_country)) {
                        $billing_address->dni = $billing_params['dni'];
                    }
                    $billing_address->alias = (isset($billing_params['alias']))
                        ? (empty($billing_params['alias']))
                            ? $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9)
                            : $billing_params['alias']
                        : $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9);
                    $billing_address->other = (!empty($billing_params['other'])) ? $billing_params['other'] : '';
                    $billing_address->id_customer = $customer->id;

                    $validate_address = $billing_address->validateController();
                    if ($validate_address && count($validate_address) > 0) {
                        foreach ($validate_address as $key => $value) {
                            $this->errors[] = sprintf(Tools::displayError('The field %s is required.'), $key);
//                                $this->errors[] = Tools::displayError($this->getPlainTextFromHtml($value));
                        }
                    } else {
//                        if (!$billing_address->save()) {
                        if ($this->updateAddress($billing_address)) {
                            $this->errors[] = Tools::displayError('Error occurred while creating new address');
                        } else {
                            $id_billing_address = $billing_address->id;

                            if ($use_for_shipping) {
                                $id_shipping_address = $id_billing_address;
                                $shipping_address = new Address($id_shipping_address);
                            }
                        }
                    }
                }
            }

            if ($current_step == 'shipping') {
                if (!$same_as_billing) {
                    if ($id_shipping_address == 0) {
                        $shipping_address = new Address();
                    } else {
                        $shipping_address = new Address($id_shipping_address);
                    }
                } else {
                    if ($id_shipping_address == 0) {
                        $shipping_address = new Address();
                    } else {
                        $id_shipping_address = $this->context->cart->id_address_invoice;
                        $shipping_address = new Address($id_shipping_address);
                    }
                }

                if ($id_shipping_address == 0
                    || ($id_shipping_address != 0 && ($customer->is_guest))
                    || ($id_shipping_address != 0 && $shipping_params['edit'] == 1)) {
                    $shipping_address->firstname = (!empty($shipping_params['firstname'])) ?
                        $shipping_params['firstname'] : ' ';
                    $shipping_address->lastname = (!empty($shipping_params['lastname'])) ?
                        $shipping_params['lastname'] : ' ';
                    $shipping_address->company = (!empty($shipping_params['company'])) ?
                        $shipping_params['company'] : ' ';

                    $shipping_address->vat_number = (!empty($shipping_params['vat_number'])) ? $shipping_params['vat_number'] : ' ';

                    $shipping_address->address1 = (!empty($shipping_params['street'])) ?
                        $shipping_params['street'] : ' ';
                    $shipping_address->address2 = (!empty($shipping_params['street2'])) ?
                        $shipping_params['street2'] : ' ';
                    $shipping_address->city = (!empty($shipping_params['city'])) ? $shipping_params['city'] : ' ';
                    $shipping_address->phone = (!empty($shipping_params['telephone'])) ?
                        $shipping_params['telephone'] : ' ';
                    $shipping_address->phone_mobile = (!empty($shipping_params['phone_mobile'])) ?
                        $shipping_params['phone_mobile'] : ' ';
                    $shipping_address->id_country = (!empty($shipping_params['country_id'])) ?
                        $shipping_params['country_id'] : (int)Configuration::get('PS_COUNTRY_DEFAULT');

                    if (Country::getNeedZipCode($shipping_address->id_country)) {
                        $shipping_address->postcode = $shipping_params['postcode'];
                    }
                    $shipping_address->id_state = (!empty($shipping_params['state_id'])) ?
                        $shipping_params['state_id'] : 0;

                    if (!Country::containsStates($shipping_address->id_country)) {
                        $shipping_address->id_state = 0;
                    }
                    $shipping_address->vat_number = (!empty($shipping_params['vat_number'])) ?
                        $shipping_params['vat_number'] : '';

                    if (Country::isNeedDniByCountryId($shipping_address->id_country)) {
                        $shipping_address->dni = $shipping_params['dni'];
                    }
                    $shipping_address->alias = (isset($shipping_params['alias']))
                        ? (empty($shipping_params['alias']))
                            ? $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9)
                            : $shipping_params['alias']
                        : $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9);
                    $shipping_address->other = (!empty($shipping_params['other'])) ? $shipping_params['other'] : '';
                    $shipping_address->id_customer = $customer->id;

                    $validate_address = $shipping_address->validateController();
                    if ($validate_address && count($validate_address) > 0) {
                        foreach ($validate_address as $key => $value) {
                            $this->errors[] = Tools::displayError($this->getPlainTextFromHtml($value));
                        }
                    } else {
//                        if (!$shipping_address->save()) {
                        if ($this->updateAddress($shipping_address)) {
                            $this->errors[] = Tools::displayError('Error occurred while creating new address');
                        } else {
                            $id_shipping_address = $shipping_address->id;
                        }
                    }
                }

                //save message from "comment-box" DELIVERY ADDRESS
                $new_message = (string)Tools::getValue('message_box');
                $this->updateMessageToCart($new_message);
            }
        }

        if (!count($this->errors)) {
            if (!$customer->is_guest) {
                $this->context->customer = $customer;
                $customer->cleanGroups();
                // we add the guest customer in the default customer group
                $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
//                            if (!$this->sendConfirmationMail($customer)) {
//                                $this->errors[] = Tools::displayError('The email cannot be sent.');
//                            }
            } else {
                $customer->cleanGroups();
                // we add the guest customer in the guest customer group
                $customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
            }
            $this->updateContext($customer);
            if ($current_step == 'billing') {
                $this->context->cart->id_address_invoice = $id_billing_address;
                if ($use_for_shipping) {
                    $this->context->cart->id_address_delivery = $id_shipping_address;
                }
            } else {
                $this->context->cart->id_address_delivery = $id_shipping_address;
                if ($same_as_billing) {
                    $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
                }
            }

            $delivery_option =
                array((int)$this->context->cart->id_address_delivery => (int)$this->context->cart->id_carrier . ',');
            $this->context->cart->setDeliveryOption($delivery_option);

            // If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
            $this->context->cart->update();

            // Avoid articles without delivery address on the cart
            $this->context->cart->autosetProductAddress();

            $goToSection = 'shipping';
            $dupplicateBillingInfo = false;
            $shipmentHtmlContent = '';
            $allow_sections = array();

            if ($current_step == 'billing') {
                if ($use_for_shipping) {
                    $allow_sections[] = 'shipping';
                    $goToSection = 'shipping_method';
                    $dupplicateBillingInfo = true;
                    // load shipping method
                    $shipmentContent = $this->getCarrierList(
                        $shipping_address->id_country,
                        $shipping_address->id_state,
                        $shipping_address->postcode,
                        $shipping_address->city,
                        $id_shipping_address,
                        null
                    );
                    if (!$shipmentContent['hasError']) {
                        $shipmentHtmlContent = $shipmentContent['carrier_block'];
                    } else {
                        if (array_key_exists('errors', $shipmentContent)) {
                            $this->errors = $shipmentContent['errors'];
                        } else {
                            $this->errors[] = $this->module->l(
                                'There are no carriers that deliver to the address you selected.',
                                'JmCheckout16'
                            );
                        }
                    }
                } else {
                    $goToSection = 'shipping';
                    $dupplicateBillingInfo = false;
                }
            } else {
                $goToSection = 'shipping_method';
                $dupplicateBillingInfo = false;
                $allow_sections[] = 'shipping';
                // load shipping method
                $shipmentContent = $this->getCarrierList(
                    $shipping_address->id_country,
                    $shipping_address->id_state,
                    $shipping_address->postcode,
                    $shipping_address->city,
                    $id_shipping_address,
                    null
                );

                if (!$shipmentContent['hasError']) {
                    $shipmentHtmlContent = $shipmentContent['carrier_block'];
                } else {
                    if (array_key_exists('errors', $shipmentContent)) {
                        $this->errors = $shipmentContent['errors'];
                    } else {
                        $this->errors[] = $this->module->l(
                            'There are no carriers that deliver to the address you selected.',
                            'JmCheckout16'
                        );
                    }
                }
            }

            Hook::exec('actionCustomerAccountAdd', array(
                '_POST' => $_POST,
                'newCustomer' => $customer
            ));

            $addresses = array();
            if ($customer->id != 0) {
                $addresses = $customer->getAddresses($this->context->language->id);
            }
            $this->assignAddressFormat();
            if ($this->ajax) {
                $return = array(
                    'shipping_fields' => $this->getAddressFormFields('shipping', $billing_address->id_country),
                    'allow_sections' => $allow_sections,
                    'goto_section' => $goToSection,
                    'updated_section' => $shipmentHtmlContent,
                    'duplicateBillingInfo' => $dupplicateBillingInfo,
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'isSaved' => true,
                    'id_customer' => (int)$this->context->cookie->id_customer,
                    'id_address_delivery' => $this->context->cart->id_address_delivery,
                    'id_address_invoice' => $this->context->cart->id_address_invoice,
                    'addresses' => $addresses,
                    'token' => Tools::getToken(false)
                );
                $this->ajaxDie(Tools::jsonEncode($return));
            }
        }
        $return = array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->addJS(array(
            _THEME_JS_DIR_ . 'tools/vatManagement.js',
            $this->module_dir . 'views/js/statesManagement.js',
            _THEME_JS_DIR_ . 'authentication.js',
            _PS_JS_DIR_ . 'validate.js'
        ));

        $this->addCSS($this->module_dir . 'views/css/bootstrap.min.css');
        $this->addCSS($this->module_dir . 'views/css/font-awesome-4.7.0/css/font-awesome.css');
        $this->addCSS($this->module_dir . 'vendor/kien/prestashop-onepage-lib/src/views/css/style.css');
        $this->addCSS($this->module_dir . 'views/css/responsive.css');
        $this->addCSS($this->module_dir . 'views/css/ladda.min.css');


        $this->addJS($this->module_dir . 'views/js/libs/collapse.js');
        $this->addJS($this->module_dir . 'views/js/libs/jquery.form-validator.js');
        $this->addJS($this->module_dir . 'views/js/libs/jquery.deserialize.js');


        $lang_iso_code = $this->context->language->iso_code;
        if ($lang_iso_code == 'nl') {
            $this->addJS($this->module_dir . 'views/js/libs/lang/nl.js');
        } elseif ($lang_iso_code == 'es') {
            $this->addJS($this->module_dir . 'views/js/libs/lang/es.js');
        } elseif ($lang_iso_code == 'it') {
            $this->addJS($this->module_dir . 'views/js/libs/lang/it.js');
        } elseif ($lang_iso_code == 'ar') {
            $this->addJS($this->module_dir . 'views/js/libs/lang/ar.js');
        }

        $this->addJS($this->module_dir . 'views/js/libs/spin.min.js');
        $this->addJS($this->module_dir . 'views/js/libs/ladda.min.js');
        $this->addJS($this->module_dir . 'vendor/kien/prestashop-onepage-lib/src/views/js/jmcheckout.js?v=1176');
        $this->addJS($this->module_dir . 'views/js/template.js');
        $this->addJS($this->module_dir . 'views/js/common.js');
    }

    protected function loadPayments(
        $id_country = 0,
        $id_address_delivery = 0,
        $selected_payment_method = 0
    ) {
        $payment_methods = array();
        $context = Context::getContext();
        $id_country = $id_country;
        if (isset($context->cart) && $id_address_delivery) {
            $billing = new Address((int)$id_address_delivery);
            $id_country = $billing->id_country;
        }

        $use_groups = Group::isFeatureActive();

        $frontend = true;
        $groups = array();
        if (isset($context->employee)) {
            $frontend = false;
        } elseif (isset($context->customer) && $use_groups) {
            $groups = $context->customer->getGroups();
            if (!count($groups)) {
                $groups = array(Configuration::get('PS_UNIDENTIFIED_GROUP'));
            }
        }

        $hook_payment = 'Payment';
        if (Db::getInstance()->getValue('SELECT `id_hook` FROM `'
            . _DB_PREFIX_ . 'hook` WHERE `name` = \'displayPayment\'')) {
            $hook_payment = 'displayPayment';
        }

        $paypal_condition = '';
        $iso_code = Country::getIsoById((int)$id_country);
        $paypal_countries = array('ES', 'FR', 'PL', 'IT');
        if (Context::getContext()->getMobileDevice()
            && Context::getContext()->shop->getTheme() == 'default'
            && in_array($iso_code, $paypal_countries)) {
            $paypal_condition = ' AND m.`name` = \'paypal\'';
        }

        $list = Shop::getContextListShopID();
        $methods = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT DISTINCT m.`id_module`, h.`id_hook`, m.`name`, hm.`position`
			FROM `' . _DB_PREFIX_ . 'module` m
			' . ($frontend ? 'LEFT JOIN `' . _DB_PREFIX_ . 'module_country` mc
			ON (m.`id_module` = mc.`id_module` AND mc.id_shop = ' . (int)$context->shop->id . ')' : '') . '
			' . ($frontend && $use_groups ? 'INNER JOIN `' . _DB_PREFIX_ . 'module_group` mg
			ON (m.`id_module` = mg.`id_module` AND mg.id_shop = ' . (int)$context->shop->id . ')' : '') . '
			' . ($frontend && $this->is_logged && $use_groups ? 'INNER JOIN `' . _DB_PREFIX_ . 'customer_group` cg
			on (cg.`id_group` = mg.`id_group`AND cg.`id_customer` = ' . (int)$context->customer->id . ')' : '') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'hook_module` hm ON hm.`id_module` = m.`id_module`
			LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON hm.`id_hook` = h.`id_hook`
			WHERE h.`name` = \'' . pSQL($hook_payment) . '\'
			' . (($frontend) ? 'AND mc.id_country = ' . (int)$id_country : '') . '
			AND (SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'module_shop ms
				WHERE ms.id_module = m.id_module AND ms.id_shop IN(' . implode(', ', $list) . ')) = ' . (int)count($list) . '
			AND hm.id_shop IN(' . implode(', ', $list) . ')
			' . ((count($groups)
                && $frontend && $use_groups) ?
                'AND (mg.`id_group`
                IN (' . implode(', ', $groups) . '))' : '') . pSQL($paypal_condition) . '
			GROUP BY hm.id_hook, hm.id_module
			ORDER BY hm.`position`, m.`name` DESC'
        );

        $payment_number = 0;
        $hook_args = array('cookie' => $this->context->cookie, 'cart' => $this->context->cart);
        $counter = 0;
        if ($methods) {
            foreach ($methods as $module) {
                //Hide Unsupported Payment Method - Amazon payments
                //https://jmango360.atlassian.net/browse/PS-489
                if ($module['name'] == 'amzpayments') {
                    continue;
                }

                if (($module_instance = Module::getInstanceByName($module['name']))
                    && (is_callable(array($module_instance, 'hookpayment'))
                        || is_callable(array($module_instance, 'hookDisplayPayment')))) {
                    $currency_ids = array();
                    $currency_data = Currency::checkPaymentCurrencies($module_instance->id);
                    foreach ($currency_data as $currency) {
                        $currency_ids[] = $currency['id_currency'];
                    }

                    if (!in_array($this->context->currency->id, $currency_ids)) {
                        if (!in_array(-1, $currency_ids) && !in_array(-2, $currency_ids)) {
                            continue;
                        }
                    }

                    if (!$module_instance->currencies
                        || ($module_instance->currencies
                            && count(Currency::checkPaymentCurrencies($module_instance->id)))) {
                        if (is_callable(array($module_instance, 'hookPayment'))
                            || is_callable(array($module_instance, 'hookDisplayPayment'))) {
                            $is_hook = false;
                            $html = '';
                            $display_name = '';
                            if ($module['name'] == 'twenga') {
                                if (is_callable(array($module_instance, 'hookPayment'))) {
                                    $html .= call_user_func(array($module_instance, 'hookPayment'), $hook_args);
                                }

                                if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment'))) {
                                    $html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
                                }
                                $is_hook = true;
                            } else {
                                if ($module['name'] == 'brinkscheckout') {
                                    $this->context->controller->addCSS(_PS_MODULE_DIR_ .
                                        'brinkscheckout/css/2checkout.css', 'all');
                                    $key_config = Configuration::get('TWOCHECKOUT_SID');

                                    if (Configuration::get('TWOCHECKOUT_SANDBOX')) {
                                        $this->context->controller->addJS(
                                            'https://sandbox.2checkout.com/checkout/api/script/publickey/' . $key_config
                                        );
                                    } else {
                                        $this->context->controller->addJS(
                                            'https://www.2checkout.com/checkout/api/script/publickey/' . $key_config
                                        );
                                    }
                                } elseif ($module['name'] == 'twocheckout') {
                                    if (Configuration::get('TWOCHECKOUT_SANDBOX')) {
                                        $this->context->controller->addJS(
                                            'https://sandbox.2checkout.com/checkout/api/script/publickey/' .
                                            Configuration::get('TWOCHECKOUT_SID') . ''
                                        );
                                    } else {
                                        $this->context->controller->addJS(
                                            'https://www.2checkout.com/checkout/api/script/publickey/' .
                                            Configuration::get('TWOCHECKOUT_SID') . ''
                                        );
                                    }
                                    $this->context->smarty->assign(
                                        'twocheckout_sid',
                                        Configuration::get('TWOCHECKOUT_SID')
                                    );
                                    $this->context->smarty->assign(
                                        'twocheckout_public_key',
                                        Configuration::get('TWOCHECKOUT_PUBLIC')
                                    );
                                }
                            }

                            $is_hook = true;
                            if (is_callable(array($module_instance, 'hookPayment'))) {
                                $html = call_user_func(array($module_instance, 'hookPayment'), $hook_args);
                            }
                            if (empty($html) && is_callable(array($module_instance, 'hookDisplayPayment'))) {
                                $html = call_user_func(array($module_instance, 'hookDisplayPayment'), $hook_args);
                            }
                            $html = str_replace('&amp;', '&', $html);

                            $additional_payment_methods = array();

                            preg_match_all(
                                '/<a.*?>.*?<img.*?src="(.*?)".*?\/?>(.*?)<\/a>/ms',
                                $html,
                                $matches_1,
                                PREG_SET_ORDER
                            );
                            preg_match_all(
                                '/<input .*?type="image".*?src="(.*?)".*?>.*?<span.*?>(.*?)<\/span>/ms',
                                $html,
                                $matches_2,
                                PREG_SET_ORDER
                            );
                            $matches = array_merge($matches_1, $matches_2);

                            foreach ($matches as $match) {
                                $additional_payment_methods[$module_instance->id . '_' . $payment_number]['img'] =
                                    preg_replace('/(\r)?\n/m', ' ', trim($match[1]));
                                $additional_payment_methods[$module_instance->id . '_' . $payment_number]['description'] =
                                    preg_replace('/\s/m', ' ', trim($match[2]));

                                $payment_number++;
                            }
                        }
                        $html = trim(preg_replace('/\s\s+/', ' ', $html)); // adding to fix bankwire payment methods
                        if ($is_hook && !empty($html)) {
                            preg_match('/<a\s+(.*)href=\"(.*?)\"/i', $html, $fetch_module_url);

                            if (isset($fetch_module_url[2])) {
                                $payment_module_url = $fetch_module_url[2];
                            } else {
                                $payment_module_url = '';
                            }


                            if ($module['name'] == 'payu') {
                                $payment_module_url = str_replace(
                                    'themes/default/css/global.css',
                                    'modules/payu/payment.php',
                                    $payment_module_url
                                );
                            } // @Nitin Jain, 7-October-2015, Payu was geting css path in href link instead of php file.

                            if (($module['name'] == 'zipcheck' && $this->is_logged && $payment_module_url == '')
                                || ($module['name'] == 'zipcheck'
                                    && $payment_module_url == ''
                                    && Tools::isSubmit('ajax'))) {
                                continue;
                            }

                            //Get Image
                            $payment_image_url = '';
                            foreach ($this->image_extensions as $img_ext) {
                                if (file_exists(_PS_MODULE_DIR_ . $module['name'] . '/' . $module['name'] . $img_ext)) {
                                    $custom_ssl_var = 0;
                                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                                        $custom_ssl_var = 1;
                                    }

                                    if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                                        $payment_image_url =
                                            _PS_BASE_URL_SSL_ . _MODULE_DIR_ . $module['name'] . '/' . $module['name'] . $img_ext;
                                    } else {
                                        $payment_image_url =
                                            _PS_BASE_URL_ . _MODULE_DIR_ . $module['name'] . '/' . $module['name'] . $img_ext;
                                    }
                                    break;
                                }
                            }
                            require_once(_PS_MODULE_DIR_ . $module['name'] . '/' . $module['name'] . '.php');
                            $dom = new DOMDocument;
                            @$dom->loadHTML(
                                '<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $html
                            );
                            $xpath = new DOMXPath($dom);
                            $results = $xpath->query("//a");

                            if ($results->length > 0) {
                                $display_name = trim($results->item(0)->nodeValue);
                            }

                            if (empty($display_name)) {
                                if (class_exists($module['name'], false)) {
                                    $pay_temp = new $module['name'];
                                    $display_name = $pay_temp->displayName;
                                } else {
                                    $display_name = $module['name'];
                                }
                            }

                            if ($module['name'] == 'bankwire') {
                                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                                    $custom_ssl_var = 1;
                                }

                                if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                                    if ((strpos($payment_module_url, 'https') !== false)) {
                                        $payment_module_url = $payment_module_url;
                                    } else {
                                        $payment_module_url = str_replace('http', 'https', $payment_module_url);
                                    }
                                }
                            }

                            //PS-630
                            // Get Payment Method Display Name for opale
                            if ($_SERVER['SERVER_NAME'] == 'www.preprod.opale-bijoux.com' || $_SERVER['SERVER_NAME'] == 'www.opale-bijoux.com') {
                                $htmls_opale = $html;
                                if (isset($htmls_opale)) {
                                    $display_name_temps = null;
                                    preg_match('/<h4>(.*?)<\/h4>/', $htmls_opale, $display_name_temps);
                                    $display_name = preg_replace('/<br\/>/', ' ', $display_name_temps[1]);
                                }
                            }

                            /**
                             * PS-832: Support exclude payments
                             */
                            $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
                            $exludedPayments = isset($exluded['payments']) ? $exluded['payments'] : array();
                            if (in_array($module['name'], $exludedPayments)) {
                                continue;
                            }

                            $payment_methods['methods'][] = array(
                                'id_module' => (int)$module_instance->id,
                                'name' => $module['name'],
                                'display_name' => $display_name,
                                'payment_module_url' => $payment_module_url,
                                'html' => $html, //base64_encode(($html)),
                                'payment_image_url' => $payment_image_url,
                                'additional' => $additional_payment_methods
                            );
                        }
                    }
                }
            }
            $counter++;
        } else {
            $payment_methods['warning'] = $this->module->l('No payment modules have been installed.', 'JmCheckout16');
        }

        if ($counter == 0) {// added for ship2pay
            $this->errors[] = $this->module->l(
                'No payment method is available for use at this time. ',
                'JmCheckout16'
            );
        }

        if ($this->context->cart->getOrderTotal(true) == 0) {
            $this->errors[] = $this->module->l('No payment method required.', 'JmCheckout16');
        }

        $payment_html = '';

        if (!count($this->errors)) {
            $this->context->smarty->assign(array(
                'selected_payment_method' => $selected_payment_method,
                'payment_methods' => $payment_methods['methods']));

            $checkout_url = __PS_BASE_URI__ . 'index.php?fc=module&module='.$this->module_name.'&controller=jmcheckout';
            $this->context->smarty->assign('myopc_checkout_url', $checkout_url);
            $payment_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module_name .
                '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/payment-methods.tpl');
        }

        return array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors,
            'payment_method' => $payment_html,
            'payment_method_list' => $payment_methods
        );
    }

    public function loadPaymentMethods()
    {
        $payment_methods = array();
        $modules_list = Module::getPaymentModules();
        $hook_args = array('cookie' => $this->context->cookie, 'cart' => $this->context->cart);

        foreach ($modules_list as $module) {
            $module_obj = Module::getInstanceById($module['id_module']);

            //Check currency
            if (is_callable(array($module_obj, 'checkCurrency'))) {
                if (!$module_obj->checkCurrency($this->context->cart)) {
                    continue;
                }
            } else {
                /** TODO Tien:Implement check currency function */
            }

            //Get Image
            $payment_image_url = '';

            foreach ($this->image_extensions as $img_ext) {
                if (file_exists(_PS_MODULE_DIR_ . $module_obj->name . '/' . $module_obj->name . $img_ext)) {
                    $custom_ssl_var = 0;
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                        $custom_ssl_var = 1;
                    }

                    if ((bool)Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                        $payment_image_url = _PS_BASE_URL_SSL_ .
                            _MODULE_DIR_ . $module['name'] . '/' . $module['name'] . $img_ext;
                    } else {
                        $payment_image_url = _PS_BASE_URL_ . _MODULE_DIR_ . $module['name'] . '/' . $module['name'] . $img_ext;
                    }
                    break;
                }
            }

            $is_hook = true;
            if (is_callable(array($module_obj, 'hookPayment'))) {
                $html = call_user_func(array($module_obj, 'hookPayment'), $hook_args);
            }

            if (empty($html) && is_callable(array($module_obj, 'hookDisplayPayment'))) {
                $html = call_user_func(array($module_obj, 'hookDisplayPayment'), $hook_args);
            }

            $html = str_replace('&amp;', '&', $html);
            $html = trim(preg_replace('/\s\s+/', ' ', $html));

            if ($is_hook && !empty($html)) {
                preg_match('/<a\s+(.*)href=\"(.*?)\"/i', $html, $fetch_module_url);

                if (isset($fetch_module_url[2])) {
                    $payment_module_url = $fetch_module_url[2];
                } else {
                    $payment_module_url = '';
                }

                $payment_methods[] = array(
                    'id_module' => (int)$module_obj->id,
                    'name' => $module_obj->name,
                    'display_name' => $module_obj->displayName,
                    'html' => $html, //base64_encode(($html)),
                    'payment_module_url' => $payment_module_url,
                    'payment_image_url' => $payment_image_url
//                'additional' => $additional_payment_methods
                );
            }
        }

        return $payment_methods;
    }

    public function loadCountries()
    {
        $this->id_country = (int)Tools::getCountry();
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }
        return $countries;
    }

    public function initHeader()
    {
        parent::initHeader();
        $this->context->smarty->assign('content_only', 1);
    }

    /**
     * Update context after customer creation
     * @param Customer $customer Created customer
     */
    protected function updateContext(Customer $customer)
    {
        $this->context->customer = $customer;
        $this->context->cookie->id_customer = (int)$customer->id;
        $this->context->cookie->customer_lastname = $customer->lastname;
        $this->context->cookie->customer_firstname = $customer->firstname;
        $this->context->cookie->passwd = $customer->passwd;
        $this->context->cookie->logged = 1;
        // if register process is in two steps, we display a message to confirm account creation
        if (!Configuration::get('PS_REGISTRATION_PROCESS_TYPE')) {
            $this->context->cookie->account_created = 1;
        }
        $customer->logged = 1;
        $this->context->cookie->email = $customer->email;
        $this->context->cookie->is_guest = !Tools::getValue('is_new_customer', 1);
        // Update cart address
        $this->context->cart->secure_key = $customer->secure_key;
    }

    private function updateCarrier()
    {
        $error = array();
        $delivery_address = new Address($this->context->cart->id_address_delivery);

        if (Validate::isLoadedObject($delivery_address)) {
            $id_zone = Address::getZoneById((int)$delivery_address->id);
            $this->context->country->id_zone = $id_zone;

            if (!Address::isCountryActiveById((int)$delivery_address->id)) {
                $error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            } elseif (!Validate::isLoadedObject($delivery_address) || $delivery_address->deleted) {
                $error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            }

            if (!count($error)) {
                $carriers = $this->context->cart->simulateCarriersOutput();

                if (!count($carriers)) {
                    $error[] = $this->module->l(
                        'There are no carriers that deliver to the address you selected.',
                        'JmCheckout16'
                    );
                }
            }
        }

        if (count($error)) {
            return array(
                'hasError' => !empty($error),
                'errors' => $error,
            );
        }

        // Add checking for all addresses
        $address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers();
        if (count($address_without_carriers) && !$this->context->cart->isVirtualCart()) {
            if (count($address_without_carriers) > 1) {
                $error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            } elseif ($this->context->cart->isMultiAddressDelivery()) {
                $error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            } else {
                $error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            }
        }

        if (count($error)) {
            return array(
                'hasError' => !empty($error),
                'errors' => $error,
            );
        }

        if (Tools::getIsset('delivery_option')) {
            if ($this->validateDeliveryOption(Tools::getValue('delivery_option'))) {
                $this->context->cart->setDeliveryOption(Tools::getValue('delivery_option'));
            }
        } elseif (Tools::getIsset('id_carrier')) {
            $delivery_option_list = $this->context->cart->getDeliveryOptionList();
            if (count($delivery_option_list) == 1) {
                $key = Cart::desintifier(Tools::getValue('id_carrier'));
                foreach ($delivery_option_list as $id_address => $options) {
                    if (isset($options[$key])) {
                        $this->context->cart->setDeliveryOption(array($id_address => $key));
                    }
                }
            }
        } else {
            $error[] = $this->module->l('No Shipping Method Selected.', 'JmCheckout16');
        }

        Hook::exec('actionCarrierProcess', array('cart' => $this->context->cart));

        if (!$this->context->cart->update()) {
            $error[] = $this->module->l('Error occurred updating cart.', 'JmCheckout16');
        }


        // Carrier has changed, so we check if the cart rules still apply
        CartRule::autoRemoveFromCart($this->context);
        CartRule::autoAddToCart($this->context);

        // load payment method
        $billing_address = new Address($this->context->cart->id_address_invoice);
        $paymentHtmlContent = '';
        $paymentContent = $this->loadPayments($billing_address->id_country, $billing_address->id, 0);
        if (!$paymentContent['hasError']) {
            if (isset($paymentContent)) {
                $paymentHtmlContent = $paymentContent['payment_method'];
            }
        } else {
            $error[] = $paymentContent['errors'];
        }

        return array(
            'goto_section' => !empty($error) ? '' : 'payment',
            'updated_section' => !empty($error) ? '' : $paymentHtmlContent,
            'hasError' => !empty($error),
            'errors' => $error);
    }

    private function updatePayment()
    {
        if (!Tools::getIsset(('payment_method'))
            && $this->context->cart->getOrderTotal(false, Cart::BOTH) != 0) {
            $this->errors[] = $this->module->l('No payment method is selected.', 'JmCheckout16');
        }

        $cart_summary_html = '';
        if (empty($this->errors)) {
            $this->_assignSummaryInformations();
            $cart_summary_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module_name .
                '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/cart-summary.tpl');
        }

        return array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors,
            'goto_section' => !empty($this->errors) ? '' : 'review',
            'updated_section' => $cart_summary_html,
        );
    }

    private function getCarrierList(
        $id_country = 0,
        $id_state = 0,
        $postcode = '',
        $city = '',
        $id_address_delivery = 0,
        $default_carrier = null
    ) {
        //Start - New Code
        if (empty($id_country)) {
            $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }

        $carriers = array();
        $deliveries = array();

        $delivery_address = null;

        if (empty($id_address_delivery)) {
            $delivery_address = new Address($this->context->cart->id_address_delivery);
            if (!isset($delivery_address->id)) {
                $delivery_address = new Address(0);
            }
        } else {
            $delivery_address = new Address($id_address_delivery);
        }

        if (Validate::isLoadedObject($delivery_address) && count($this->shipping_error) == 0) {
            // Address has changed, so we check if the cart rules still apply
            CartRule::autoRemoveFromCart($this->context);
            CartRule::autoAddToCart($this->context);

            //As there is no multishipping, set each product delivery address with main delivery address
            $this->context->cart->setNoMultishipping();
            $id_zone = Address::getZoneById((int)$delivery_address->id);
            $this->context->country->id_zone = $id_zone;

            if (!Address::isCountryActiveById((int)$delivery_address->id)) {
                $this->shipping_error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            } elseif (!Validate::isLoadedObject($delivery_address)
                || $delivery_address->deleted) {
                $this->shipping_error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            }

            if (!count($this->shipping_error)) {
                $address = new Address($this->context->cart->id_address_delivery);
                $id_zone = Address::getZoneById($address->id);
                $default_country = new Country($id_country);
                $carriers = $this->context->cart->simulateCarriersOutput($default_country, true);

                $checked = $this->context->cart->simulateCarrierSelectedOutput();
                $delivery_option_list = $this->context->cart->getDeliveryOptionList();

                /**
                 * PS-832: Support exclude carriers
                 */
                $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
                $exludedCarriers = isset($exluded['carriers']) ? $exluded['carriers'] : array();
                foreach ($delivery_option_list as $id_address => $option_list) {
                    foreach ($option_list as $key => $option) {
                        $carrier_id = trim($key, ',');
                        if (in_array($carrier_id, $exludedCarriers)) {
                            unset($delivery_option_list[$id_address][$key]);
                        }
                    }
                }

                if (!empty($default_carrier)
                    && isset($delivery_option_list[$id_address_delivery])
                    && array_key_exists($default_carrier . ',', $delivery_option_list[$id_address_delivery])) {
                    $this->context->cart->setDeliveryOption(array($id_address_delivery => $default_carrier . ','));
                } else {
                    $this->setDefaultCarrierSelection();
                }

                $delivery_option = $this->context->cart->getDeliveryOption(null, false, false);
                $deliveries = array(
                    'address_collection' => $this->context->cart->getAddressCollection(),
                    'delivery_option_list' => $delivery_option_list,
                    'carriers' => $carriers,
                    'carriers_count' => count($carriers),
                    'checked' => $checked,
                    'delivery_option' => $delivery_option,
                    'default_shipping_method' => $this->default_shipping_selected . ','
                );

                if (!count($carriers)) {
                    $this->shipping_error[] = $this->module->l(
                        'There are no carriers that deliver to the address you selected.',
                        'JmCheckout16'
                    );
                }

                $_POST['id_address_delivery'] = $this->context->cart->id_address_delivery;
                if (!count($this->shipping_error)) {
                    $vars = array(
                        'HOOK_BEFORECARRIER' => Hook::exec('displayBeforeCarrier', array(
                            'carriers' => $carriers,
                            'checked' => $checked,
                            'delivery_option_list' => $delivery_option_list,
                            'delivery_option' => $delivery_option
                        ))
                    );

                    Cart::addExtraCarriers($vars);

                    $this->context->smarty->assign($vars);
                }
            }
        }


        if (!count($this->shipping_error)) {
            $arr = array('IS_VIRTUAL_CART' => $this->context->cart->isVirtualCart());
        } else {
            $arr = array(
                'IS_VIRTUAL_CART' => $this->context->cart->isVirtualCart(),
                'hasError' => !empty($this->shipping_error),
                'shipping_error' => $this->shipping_error,
            );
            return $arr;
        }

        $page_data = array_merge($deliveries, $arr, $this->assignWrappingTOS());
        $this->context->smarty->assign($page_data);

        // Add checking for all addresses
        $address_without_carriers = $this->context->cart->getDeliveryAddressesWithoutCarriers();
        if (count($address_without_carriers) && !$this->context->cart->isVirtualCart()) {
            if (count($address_without_carriers) > 1) {
                $this->shipping_error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            } elseif ($this->context->cart->isMultiAddressDelivery()) {
                $this->shipping_error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            } else {
                $this->shipping_error[] = $this->module->l(
                    'There are no carriers that deliver to the address you selected.',
                    'JmCheckout16'
                );
            }
        }

        $temp_vars = array(
            'hasError' => !empty($this->shipping_error),
            'errors' => $this->shipping_error,
            'carriers_count' => count($carriers),
            'is_cart_virtual' => $this->context->cart->isVirtualCart(),
            'carrier_block' =>
                $this->context->smarty->fetch(_PS_MODULE_DIR_ . $this->module_name.
                    '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/shipping-methods.tpl'),
//                'order-shipping-extra' => $this->context->smarty->fetch(_PS_MODULE_DIR_.'supercheckout/views/templates/front/order-shipping-extra.tpl'),
            'HOOK_EXTRACARRIER' => Hook::exec(
                'displayCarrierList',
                array('address' => Context::getContext()->cart->getAddressCollection())
            )
        );

        return $temp_vars;
    }

    protected function setDefaultCarrierSelection()
    {
        if (!$this->context->cart->getDeliveryOption(null, true)) {
            $this->context->cart->setDeliveryOption($this->context->cart->getDeliveryOption());
        }
    }

    private function loadCart()
    {
        $result = array();

        if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
            Cart::addExtraCarriers($result);
        }

        $summary = $this->context->cart->getSummaryDetails(null, true);
        $customized_datas = Product::getAllCustomizedDatas($this->context->cart->id, null, true);
        $cart_product_context = Context::getContext()->cloneContext();

        foreach ($summary['products'] as &$product) {
            $product['quantity_without_customization'] = $product['quantity'];
            if ($customized_datas
                && isset($customized_datas[(int)$product['id_product']][(int)$product['id_product_attribute']])) {
                foreach ($customized_datas[(int)$product['id_product']]
                         [(int)$product['id_product_attribute']] as $addresses) {
                    foreach ($addresses as $customization) {
                        $product['quantity_without_customization'] -= (int)$customization['quantity'];
                    }
                }
            }

            if ($cart_product_context->shop->id != $product['id_shop']) {
                $cart_product_context->shop = new Shop((int)$product['id_shop']);
            }

            $null = false;
            $product['price_without_quantity_discount'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                2,
                null,
                false,
                false,
                1,
                false,
                null,
                null,
                null,
                $null,
                true,
                true,
                $cart_product_context
            );

            if (Product::getTaxCalculationMethod()) {
                $product['is_discounted'] = $product['price_without_quantity_discount'] != $product['price'];
            } else {
                $product['is_discounted'] = $product['price_without_quantity_discount'] != $product['price_wt'];
            }
        }

        // override customization tax rate with real tax (tax rules)
        if ($customized_datas) {
            foreach ($summary['products'] as &$product_update) {
                $product_id = (int)isset($product_update['id_product']) ?
                    $product_update['id_product'] : $product_update['product_id'];
                $product_attribute_id = (int)isset($product_update['id_product_attribute'])
                    ? $product_update['id_product_attribute']
                    : $product_update['product_attribute_id'];

                if (isset($customized_datas[$product_id][$product_attribute_id])) {
                    $product_update['tax_rate'] = Tax::getProductTaxRate($product_id, $this->context->cart->{
                    Configuration::get('PS_TAX_ADDRESS_TYPE')});
                }
            }
            Product::addCustomizationPrice($summary['products'], $customized_datas);
        }
        // Get available cart rules and unset the cart rules already in the cart
//        $available_cart_rules = $this->getCartRules();
        $show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true)
            && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));
        $this->context->smarty->assign($summary);

        $cart_summary_extra = array(
            'token_cart' => Tools::getToken(false),
            'voucherAllowed' => CartRule::isFeatureActive(),
            'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
            'customizedDatas' => $customized_datas,
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'lastProductAdded' => $this->context->cart->getLastProduct(),
//            'displayVouchers' => $available_cart_rules,
            'currencySign' => $this->context->currency->sign,
            'currencyRate' => $this->context->currency->conversion_rate,
            'currencyFormat' => $this->context->currency->format,
            'currencyBlank' => $this->context->currency->blank,
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
            'empty_cart_warning' => $this->module->l('Your shopping cart is empty.', 'JmCheckout16'),
            'enable_coupon_onepage' => Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE)
        );

        $summary = array_merge($summary, $cart_summary_extra, $this->assignWrappingTOS());

        $summary = array_merge($summary, array(
            'HOOK_SHOPPING_CART' => Hook::exec('displayShoppingCartFooter', $summary),
            'HOOK_SHOPPING_CART_EXTRA' => Hook::exec('displayShoppingCart', $summary),
            'customizedDatas' => Product::getAllCustomizedDatas($this->context->cookie->id_cart)
        ));

        return $summary;
    }

    protected function validateDeliveryOption($delivery_option)
    {
        if (!is_array($delivery_option)) {
            return false;
        }


        foreach ($delivery_option as $option) {
            if (!preg_match('/(\d+,)?\d+/', $option)) {
                return false;
            }
        }

        return true;
    }

    private function assignWrappingTOS()
    {
        $wrapping_fees_tax_inc = $wrapping_fees = $this->getGiftWrappingPrice();

        // TOS
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);
        $this->link_conditions = $this->context->link->getCMSLink(
            $cms,
            $cms->link_rewrite,
            (bool)Configuration::get('PS_SSL_ENABLED')
        );

        $free_shipping = false;
        foreach ($this->context->cart->getCartRules() as $rule) {
            if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                $free_shipping = true;
                break;
            }
        }

        $delivery_extras = array(
            'free_shipping' => $free_shipping,
            'show_TOS' => 1,
            'checkedTOS' => (int)$this->context->cookie->checkedTOS,
            'recyclablePackAllowed' => (int)Configuration::get('PS_RECYCLABLE_PACK'),
            'giftAllowed' => (int)Configuration::get('PS_GIFT_WRAPPING'),
            'cms_id' => (int)Configuration::get('PS_CONDITIONS_CMS_ID'),
            'conditions' => (int)Configuration::get('PS_CONDITIONS'),
            'link_conditions' => $this->link_conditions,
            'cartGiftChecked' => $this->context->cart->gift,
            'recyclable' => (int)$this->context->cart->recyclable,
            'gift_wrapping_price' => (float)$wrapping_fees,
            'total_wrapping_cost' => Tools::convertPrice($wrapping_fees_tax_inc, $this->context->currency),
            'total_wrapping_tax_exc_cost' => Tools::convertPrice($wrapping_fees, $this->context->currency));

        return $delivery_extras;
    }

    public function getGiftWrappingPrice($with_taxes = true, $id_address = null)
    {
        static $address = null;

        $wrapping_fees = (float)Configuration::get('PS_GIFT_WRAPPING_PRICE');
        if ($with_taxes && $wrapping_fees > 0) {
            if ($address === null) {
                if ($id_address === null) {
                    $id_address = (int)$this->context->cart->id_address_delivery; //$id_address = (int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
                }
                try {
                    $address = Address::initialize($id_address);
                } catch (Exception $e) {
                    $address = new Address();
                    $address->id_country = Configuration::get('PS_COUNTRY_DEFAULT');
                }
            }

            $tax_manager = TaxManagerFactory::getManager(
                $address,
                (int)Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP')
            );
            $tax_calculator = $tax_manager->getTaxCalculator();
            $wrapping_fees = $tax_calculator->addTaxes($wrapping_fees);
        }

        return $wrapping_fees;
    }

    protected function addCartRule()
    {
        $discountarr = array();
        if (CartRule::isFeatureActive()) {
            if (!($code = trim(Tools::getValue('coupon_code')))) {
                $this->errors[] = $this->module->l('You must enter a voucher code.', 'coupon-service');
            } elseif (!Validate::isCleanHtml($code)) {
                $this->errors[] = $this->module->l('The voucher code is invalid', 'jmcheckout_16');
            } else {
                if (($cart_rule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cart_rule)) {
                    if ($error = $cart_rule->checkValidity($this->context, false, true)) {
                        if (is_array($error)) {
                            $this->errors = $error;
                        } else {
                            $this->errors[] = $error;
                        }
                    } else {
                        $this->context->cart->addCartRule($cart_rule->id);
                        $discountarr['success'] = $this->module->l('Voucher successfully applied', 'JmCheckout16');
                    }
                } else {
                    $this->errors[] = $this->module->l('The voucher code is invalid', 'jmcheckout_16');
                }
            }
        } else {
            $this->errors[] = $this->module->l('This feature is not active for this voucher', 'JmCheckout16');
        }

        /* Is there only virtual product in cart */
        if ($this->context->cart->isVirtualCart()) {
            //Set id_carrier to 0 (no shipping price)
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->update();
        }
        $available_cart_rules = $this->getCartRules();
        $cart_html = '';
        if ($available_cart_rules) {
            $msg = $this->module->l('Take advantage of our exclusive offers:', 'JmCheckout16');
            $cart_html .= '<p id="title" class="title-offers" style="font-weight: 600;color: black!important;">'
                . $msg . '</p>';
            foreach ($available_cart_rules as $cart_rule) {
                if ($cart_rule['code'] != '') {
                    $cart_html .= '<span onclick="$(\'#coupon_code\').val(\'' . $cart_rule['code'] .
                        '\');return false;" class="coupon_code" data-code="' . $cart_rule['code'] . '">'
                        . $cart_rule['code'] . '</span> - ';
                }
                $cart_html .= $cart_rule['name'] . '<br />';
            }
        }
        $discountarr['cart_rule'] = $cart_html;

        $result = array();
        if (Configuration::get('PS_ORDER_PROCESS_TYPE') == 1) {
            $groups = (Validate::isLoadedObject($this->context->customer)) ?
                $this->context->customer->getGroups() : array(1);
            if ($this->context->cart->id_address_delivery) {
                $deliveryAddress = new Address($this->context->cart->id_address_delivery);
            }
            $id_country = (isset($deliveryAddress) && $deliveryAddress->id) ?
                (int)$deliveryAddress->id_country : (int)Tools::getCountry();
            Cart::addExtraCarriers($result);
        }
        $result['summary'] = $this->context->cart->getSummaryDetails(null, true);
        $result['customizedDatas'] = Product::getAllCustomizedDatas($this->context->cart->id, null, true);
        $result['HOOK_SHOPPING_CART'] = Hook::exec('displayShoppingCartFooter', $result['summary']);
        $result['HOOK_SHOPPING_CART_EXTRA'] = Hook::exec('displayShoppingCart', $result['summary']);

        foreach ($result['summary']['products'] as &$product) {
            $product['quantity_without_customization'] = $product['quantity'];
            if ($result['customizedDatas']
                && isset($result['customizedDatas']
                    [(int)$product['id_product']][(int)$product['id_product_attribute']])) {
                foreach ($result['customizedDatas'][(int)$product['id_product']]
                         [(int)$product['id_product_attribute']] as $addresses) {
                    foreach ($addresses as $customization) {
                        $product['quantity_without_customization'] -= (int)$customization['quantity'];
                    }
                }
            }
        }
        if ($result['customizedDatas']) {
            Product::addCustomizationPrice($result['summary']['products'], $result['customizedDatas']);
        }

        $json = '';
        Hook::exec('actionCartListOverride', array('summary' => $result, 'json' => &$json));

        $this->_assignSummaryInformations();
        $cart_summary_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ .$this->module_name.
            '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/cart-summary.tpl');

        $temp_vars = array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors,
            'updated_section' => $cart_summary_html
        );

        return $temp_vars;
    }

    protected function getCartRules()
    {
        $available_cart_rules = CartRule::getCustomerCartRules(
            $this->context->language->id,
            (isset($this->context->customer->id) ? $this->context->customer->id : 0),
            true,
            true,
            true,
            $this->context->cart
        );
        $cart_cart_rules = $this->context->cart->getCartRules();
        foreach ($available_cart_rules as $key => $available_cart_rule) {
            if ((isset($available_cart_rule['highlight'])
                    && !$available_cart_rule['highlight'])
                || strpos($available_cart_rule['code'], 'BO_ORDER_') === 0) {
                unset($available_cart_rules[$key]);
                continue;
            }
            foreach ($cart_cart_rules as $cart_cart_rule) {
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule']) {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
            }
        }
        return $available_cart_rules;
    }

    protected function removeDiscount()
    {
        if (CartRule::isFeatureActive()) {
            if (($id_cart_rule = (int)Tools::getValue('deleteDiscount')) && Validate::isUnsignedId($id_cart_rule)) {
                $this->context->cart->removeCartRule($id_cart_rule);
            } else {
                $this->errors[] = $this->module->l('Error occured while removing voucher', 'JmCheckout16');
            }
        } else {
            $this->errors[] = $this->module->l('This feature is not active for this voucher', 'JmCheckout16');
        }

        /* Is there only virtual product in cart */
        if ($this->context->cart->isVirtualCart()) {
            $this->context->cart->setDeliveryOption(null);
            $this->context->cart->update();
        }
        $cart_summary_html = '';
        if (empty($this->errors)) {
            $this->_assignSummaryInformations();
            $cart_summary_html = $this->context->smarty->fetch(_PS_MODULE_DIR_ .$this->module_name.
                '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/cart-summary.tpl');
        }
        return array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors,
            'updated_section' => $cart_summary_html
        );
    }

    private function placeOrder()
    {
        $product = $this->context->cart->checkQuantities(true);

        if ((int)$id_product = $this->context->cart->checkProductsAccess()) {
            $this->errors[] = htmlspecialchars_decode(sprintf(Tools::displayError(
                'An item in your cart is no longer available (%1s). You cannot proceed with your order.'
            ), Product::getProductName((int)$id_product)), ENT_QUOTES);
        }

        // If some products have disappear
        if (is_array($product)) {
            $this->errors[] = htmlspecialchars_decode(sprintf(Tools::displayError(
                'An item (%1s) in your cart is no longer available in this quantity.
                You cannot proceed with your order until the quantity is adjusted.'
            ), $product['name']), ENT_QUOTES);
        }

        // Check minimal amount
        $currency = Currency::getCurrency((int)$this->context->cart->id_currency);

        $orderTotal = $this->context->cart->getOrderTotal();
        $minimal_purchase = Tools::convertPrice((float)Configuration::get('PS_PURCHASE_MINIMUM'), $currency);
        if ($this->context->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS) < $minimal_purchase) {
            $this->errors[] = htmlspecialchars_decode(
                sprintf(
                    Tools::displayError(
                        'A minimum purchase total of %1s (tax excl.)
                        is required to validate your order, current purchase total is %2s (tax excl.).'
                    ),
                    Tools::displayPrice($minimal_purchase, $currency),
                    Tools::displayPrice(
                        $this->context->cart->getOrderTotal(
                            false,
                            Cart::ONLY_PRODUCTS
                        ),
                        $currency
                    )
                ),
                ENT_QUOTES
            );
        }

        return array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors
        );
    }

    // get old_message to delivery shipping
    private function constructMessageToCart()
    {
        $old_message = Message::getMessageByCartId((int)$this->context->cart->id);
        return $old_message;
    }

    private function updateMessageToCart($message_content)
    {
        if ($message_content) {
            if (!Validate::isMessage($message_content)) {
                $this->errors[] = Tools::displayError('Invalid message');
            } elseif ($old_message = Message::getMessageByCartId((int)$this->context->cart->id)) {
                $message = new Message((int)$old_message['id_message']);
                $message->message = $message_content;
                $message->update();
            } else {
                $message = new Message();
                $message->message = $message_content;
                $message->id_cart = (int)$this->context->cart->id;
                $message->id_customer = (int)$this->context->cart->id_customer;
                $message->add();
            }
        } else {
            if ($old_message = Message::getMessageByCartId($this->context->cart->id)) {
                $message = new Message($old_message['id_message']);
                $message->delete();
            }
        }
        return true;
    }

    private function getFormattedAddress()
    {
        // Getting a list of formated address fields with associated values
        $formated_adrress_values_list = array();
        $customer = $this->context->customer;
        if (Validate::isLoadedObject($customer)) {
            /* Getting customer addresses */
            $customer_addresses = $customer->getAddresses($this->context->language->id);

            foreach ($customer_addresses as $address) {
                $tmp_address = new Address($address['id_address']);
                $formated_adrress_values_list[$address['id_address']]
                ['ordered_fields'] = AddressFormat::getOrderedAddressFields($address['id_country']);
                $formated_adrress_values_list[$address['id_address']]
                ['formated_fields_values'] = AddressFormat::getFormattedAddressFieldsValues(
                    $tmp_address,
                    $formated_adrress_values_list[$address['id_address']]['ordered_fields']
                );

                unset($tmp_address);
            }
        }
        return $formated_adrress_values_list;
    }

    private function updateCartAddress()
    {
        $delivery_address_id = Tools::getValue('delivery_address_id');
        $invoice_address_id = Tools::getValue('invoice_address_id');
        if ($delivery_address_id) {
            $this->context->cart->id_address_delivery = $delivery_address_id;
        }

        if ($invoice_address_id) {
            $this->context->cart->id_address_invoice = $invoice_address_id;
        }

        $this->context->cart->update();

        return "";
    }

    private function createNewAddress()
    {

        /* @var AddressCore $address */
        $address = new Address();
        $this->errors = $address->validateController();
        $address->id_customer = (int)$this->context->customer->id;

        // Check page token
        if ($this->context->customer->isLogged() && !$this->isTokenValid()) {
            $this->errors[] = Tools::displayError('Invalid token.');
        }

        // Check phone
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST')
            && !Tools::getValue('phone')
            && !Tools::getValue('phone_mobile')) {
            $this->errors[] = Tools::displayError('You must register at least one phone number.');
        }
        if ($address->id_country) {
            // Check country
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                throw new PrestaShopException('Country cannot be loaded with address->id_country');
            }

            if ((int)$country->contains_states && !(int)$address->id_state) {
                $this->errors[] = Tools::displayError('This country requires you to chose a State.');
            }

            if (!$country->active) {
                $this->errors[] = Tools::displayError('This country is not active.');
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] =
                    htmlspecialchars_decode(sprintf(Tools::displayError(
                        'The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'
                    ), str_replace('C', $country->iso_code, str_replace(
                        'N',
                        '0',
                        str_replace('L', 'A', $country->zip_code_format)
                    ))), ENT_QUOTES);
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = htmlspecialchars_decode(Tools::displayError(
                    'A Zip/Postal code is required.'
                ), ENT_QUOTES);
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = htmlspecialchars_decode(Tools::displayError(
                    'The Zip/Postal code is invalid.'
                ), ENT_QUOTES);
            }

            // Check country DNI
            if ($country->isNeedDni() && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni')))) {
                $this->errors[] = Tools::displayError(
                    'The identification number is incorrect or has already been used.'
                );
            } elseif (!$country->isNeedDni()) {
                $address->dni = null;
            }
        }
        // Check if the alias exists
//        if (!$this->context->customer->is_guest && !empty($_POST['alias']) && (int)$this->context->customer->id > 0) {
//            $id_address = Tools::getValue('id_address');
//            if (Configuration::get('PS_ORDER_PROCESS_TYPE')
//              && (int)Tools::getValue('opc_id_address_'.Tools::getValue('type')) > 0) {
//                $id_address = Tools::getValue('opc_id_address_'.Tools::getValue('type'));
//            }
//
//            if (Address::aliasExist(Tools::getValue('alias'), (int)$id_address, (int)$this->context->customer->id)) {
//                $this->errors[] = sprintf(Tools::displayError('
//The alias "%s" has already been used. Please select another one.
//), Tools::safeOutput(Tools::getValue('alias')));
//            }
//        }

        // Check the requires fields which are settings in the BO
        $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

        if (Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
            if (count($this->errors)) {
                $return = array(
                    'hasError' => (bool)$this->errors,
                    'errors' => $this->errors
                );
                $this->ajaxDie(Tools::jsonEncode($return));
            }
        }

        // Save address
        if ($result = $address->save()) {
            unset($this->context->cart->id_address_delivery);
            unset($this->context->cart->id_address_invoice);
            $this->context->cart->updateAddressId(0, $address->id);
            $this->context->cart->update();

            $customer = $this->context->customer;
            $customer_addresses = $customer->getAddresses($this->context->language->id);

            $return = array(
                'hasError' => (bool)$this->errors,
                'errors' => $this->errors,
                'id_address_delivery' => (int)$this->context->cart->id_address_delivery,
                'id_address_invoice' => (int)$this->context->cart->id_address_invoice,
                'id_address_selected' => (int)$this->context->cart->id_address_delivery,
                "addresses" => $customer_addresses
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
        $return = array(
            'hasError' => (bool)$this->errors,
            'errors' => $this->errors
        );
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    protected function _assignSummaryInformations()
    {
        $summary = $this->context->cart->getSummaryDetails();
        $customizedDatas = Product::getAllCustomizedDatas($this->context->cart->id);

        // override customization tax rate with real tax (tax rules)
        if ($customizedDatas) {
            foreach ($summary['products'] as &$productUpdate) {
                $productId = (int)isset($productUpdate['id_product']) ?
                    $productUpdate['id_product'] : $productUpdate['product_id'];
                $productAttributeId = (int)isset($productUpdate['id_product_attribute']) ?
                    $productUpdate['id_product_attribute'] : $productUpdate['product_attribute_id'];

                if (isset($customizedDatas[$productId][$productAttributeId])) {
                    $productUpdate['tax_rate'] =
                        Tax::getProductTaxRate($productId, $this->context->cart->{
                        Configuration::get('PS_TAX_ADDRESS_TYPE')});
                }
            }

            Product::addCustomizationPrice($summary['products'], $customizedDatas);
        }

        $cart_product_context = Context::getContext()->cloneContext();
        foreach ($summary['products'] as $key => &$product) {
            $temp = null;
            $product['quantity'] = $product['cart_quantity'];// for compatibility with 1.2 themes

            if ($cart_product_context->shop->id != $product['id_shop']) {
                $cart_product_context->shop = new Shop((int)$product['id_shop']);
            }
            $product['price_without_specific_price'] = Product::getPriceStatic(
                $product['id_product'],
                !Product::getTaxCalculationMethod(),
                $product['id_product_attribute'],
                6,
                null,
                false,
                false,
                1,
                false,
                null,
                null,
                null,
                $temp,
                true,
                true,
                $cart_product_context
            );

            if (Product::getTaxCalculationMethod()) {
                $product['is_discounted'] =
                    Tools::ps_round($product['price_without_specific_price'], _PS_PRICE_COMPUTE_PRECISION_) !=
                    Tools::ps_round($product['price'], _PS_PRICE_COMPUTE_PRECISION_);
            } else {
                $product['is_discounted'] =
                    Tools::ps_round($product['price_without_specific_price'], _PS_PRICE_COMPUTE_PRECISION_) !=
                    Tools::ps_round($product['price_wt'], _PS_PRICE_COMPUTE_PRECISION_);
            }
        }

        // Get available cart rules and unset the cart rules already in the cart
        $available_cart_rules =
            CartRule::getCustomerCartRules($this->context->language->id, (isset($this->context->customer->id) ?
                $this->context->customer->id : 0), true, true, true, $this->context->cart, false, true);
        $cart_cart_rules = $this->context->cart->getCartRules();
        foreach ($available_cart_rules as $key => $available_cart_rule) {
            foreach ($cart_cart_rules as $cart_cart_rule) {
                if ($available_cart_rule['id_cart_rule'] == $cart_cart_rule['id_cart_rule']) {
                    unset($available_cart_rules[$key]);
                    continue 2;
                }
            }
        }

        $show_option_allow_separate_package = (!$this->context->cart->isAllProductsInStock(true)
            && Configuration::get('PS_SHIP_WHEN_AVAILABLE'));
        $advanced_payment_api = (bool)Configuration::get('PS_ADVANCED_PAYMENT_API');

        // TOS
        $cms = new CMS(Configuration::get('PS_CONDITIONS_CMS_ID'), $this->context->language->id);

        $tos_data = array_merge($this->assignWrappingTOS());

        $this->context->smarty->assign($tos_data);
        $this->context->smarty->assign($summary);
        $this->context->smarty->assign(array(
            'token_cart' => Tools::getToken(false),
            'isLogged' => $this->is_logged,
            'isVirtualCart' => $this->context->cart->isVirtualCart(),
            'productNumber' => $this->context->cart->nbProducts(),
            'voucherAllowed' => CartRule::isFeatureActive(),
            'shippingCost' => $this->context->cart->getOrderTotal(true, Cart::ONLY_SHIPPING),
            'shippingCostTaxExc' => $this->context->cart->getOrderTotal(false, Cart::ONLY_SHIPPING),
            'customizedDatas' => $customizedDatas,
            'CUSTOMIZE_FILE' => Product::CUSTOMIZE_FILE,
            'CUSTOMIZE_TEXTFIELD' => Product::CUSTOMIZE_TEXTFIELD,
            'lastProductAdded' => $this->context->cart->getLastProduct(),
            'displayVouchers' => $available_cart_rules,
            'show_option_allow_separate_package' => $show_option_allow_separate_package,
            'smallSize' => Image::getSize(ImageType::getFormatedName('small')),
            'advanced_payment_api' => $advanced_payment_api,
            'discounts' => $this->context->cart->getCartRules(),
            'enable_coupon_onepage' => Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE)
        ));

        $this->context->smarty->assign(array(
            'HOOK_SHOPPING_CART' => Hook::exec('displayShoppingCartFooter', $summary),
            'HOOK_SHOPPING_CART_EXTRA' => Hook::exec('displayShoppingCart', $summary)
        ));
    }

    /**
     * Assign address var to smarty
     */
    protected function assignAddressFormat()
    {
        if ((int)(Tools::getValue("id_country"))) {
            $id_country = Tools::getValue("id_country");
        } elseif ((int)(Tools::getValue("id_country")) == 0) {
            if (isset(Tools::getValue("billing")['country_id']) && Tools::getValue("billing")['use_for_shipping'] != 0) {
                $id_country = Tools::getValue("billing")['country_id'];
            } elseif (isset(Tools::getValue("shipping")['country_id'])
                && (array_key_exists('same_as_billing', Tools::getValue("shipping"))
                    && Tools::getValue("shipping")['same_as_billing'] != 0)
            ) {
                $id_country = Tools::getValue("billing")['country_id'];
            } else {
                $id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            }
        } else {
            $id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            if (Configuration::get('PS_DETECT_COUNTRY') && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
                preg_match('#(?<=-)\w\w|\w\w(?!-)#', $_SERVER['HTTP_ACCEPT_LANGUAGE'], $array);
                if (is_array($array) && isset($array[0]) && Validate::isLanguageIsoCode($array[0])) {
                    $id_country = (int)Country::getByIso($array[0], true);
                }
            }
        }

        $requireFormFieldsList = AddressFormat::getFieldsRequired();
        $ordered_adr_fields = AddressFormat::getOrderedAddressFields($id_country, true, true);
        $ordered_adr_fields = array_unique(array_merge($ordered_adr_fields, $requireFormFieldsList));

        $this->context->smarty->assign('has_reload', true);
        $this->context->smarty->assign('default_country', (int)Configuration::get('PS_COUNTRY_DEFAULT'));
        $this->context->smarty->assign(array(
            'ordered_adr_fields' => $ordered_adr_fields,
            'required_fields' => $requireFormFieldsList
        ));
    }

    public function getPlainTextFromHtml($html)
    {
        // Remove the HTML tags
        $html = strip_tags($html);

        // Convert HTML entities to single characters
        $html = html_entity_decode($html, ENT_QUOTES, 'UTF-8');
        return $html;
    }

    /*
     * function : get form field need for register new address,
     * @param $id_country: id of country
     */
    private function initFormFieldBillingAddress($id_country)
    {
        if (!isset($id_country) || $id_country == null || $id_country == '') {
            $id_country = (int)Tools::getCountry();
        }
        $newsletter = Configuration::get('PS_CUSTOMER_NWSL')
            || (Module::isInstalled('blocknewsletter')
                && Module::getInstanceByName('blocknewsletter')->active);

        $smarty = $this->context->smarty;

        //<editor-fold desc="assigns all and required address fields">
        $addressFormat = AddressFormat::getOrderedAddressFields($id_country, false, true);
        $addressItems = array();
        foreach ($addressFormat as $addressline) {
            foreach (explode(' ', $addressline) as $addressItem) {
                $addressItems[] = trim($addressItem);
            }
        }

        $requireFormFieldsList = AddressFormat::getFieldsRequired();
        // Add missing require fields for a new user susbscription form
        foreach ($requireFormFieldsList as $fieldName) {
            if (!in_array($fieldName, $addressItems)) {
                $addressItems[] = trim($fieldName);
            }
        }

        foreach (array('inv', 'dlv') as $addressType) {
            $smarty->assign(array(
                $addressType . '_adr_fields' => $addressFormat,
                $addressType . '_all_fields' => $addressItems,
                'required_fields' => $requireFormFieldsList
            ));
        }
        //</editor-fold>

        //assign countries
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($id_country, true, true);
        } else {
            $countries = Country::getCountries($id_country, true);
        }

        $this->context->smarty->assign('countries', $countries);
        //$this->context->smarty->assign('hasReload', true);
        $this->context->smarty->assign('newsletter', $newsletter);
        $this->context->smarty->assign('optin', (bool)Configuration::get('PS_CUSTOMER_OPTIN'));
        $cpn = '';
        if ($this->context->customer->company) {
            $cpn = $this->context->customer->company;
        }
        $this->context->smarty->assign('company_create_new', $cpn);

        if ($this->isAjax()) {
            $this->assignAddressFormat();
            $message_phone = Tools::displayError('You must register at least one phone number.');
            $countries = $this->loadCountries();

            $this->context->smarty->assign('customer', $this->context->customer);
            $this->context->smarty->assign('genders', Gender::getGenders());
            $this->context->smarty->assign('message_phone', $message_phone);
            $this->context->smarty->assign('countries', $countries);
            $this->context->smarty->assign('country_select', $id_country);
            $this->context->smarty->assign('one_phone_at_least', (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'));


            $years = Tools::dateYears();
            $months = Tools::dateMonths();
            $days = Tools::dateDays();


            $this->context->smarty->assign(array(
                'years' => $years,
                'months' => $months,
                'days' => $days,
            ));

            if (Tools::getValue('step') == 'billing') {
                $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ .$this->module_name. '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/billing-address-form.tpl');
            } elseif (Tools::getValue('step') == 'shipping') {
                $output = $this->context->smarty->fetch(_PS_MODULE_DIR_ .$this->module_name. '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/shipping-address-form.tpl');
            }
            $this->context->smarty->assign('output', ($output));

//            if (!$this->is_logged) {
//                $return = array(
//                    'customer_form_required' => $requireFormFieldsList,
//                    'countries'=> $countries,
//                    'current_country' => $id_country,
//                    'output' => $output,
//                );
//                $this->ajaxDie(Tools::jsonEncode($return));
//            }
            $return = array(
                'ordered_adress_fields' => $addressFormat,
                'output' => $output,
                'required_fields' => $addressItems,
                'customer_form_required' => $requireFormFieldsList,
                'countries' => $countries,
                'current_country' => $id_country,
                'has_reload' => true
            );
            $this->ajaxDie(Tools::jsonEncode($return));
        }
    }

    /**
     * @param $type 'shipping'|'billing'
     * @param $id_country
     * @return html
     */
    private function getAddressFormFields($type, $id_country = null)
    {
        if (!$id_country) {
            $id_country = (int)Tools::getCountry();
        }
        if (!$this->is_logged) {
            if (isset(Tools::getValue("billing")['country_id']) && Tools::getValue("billing")['use_for_shipping'] != 0) {
                $id_country = Tools::getValue("billing")['country_id'];
            } elseif (isset(Tools::getValue("shipping")['country_id']) && Tools::getValue("shipping")['same_as_billing'] != 0) {
                $id_country = Tools::getValue("billing")['country_id'];
            } else {
                $id_country = (int)Configuration::get('PS_COUNTRY_DEFAULT');
            }
        }

        /* @var Smarty $smarty */
        $smarty = $this->context->smarty;


        //<editor-fold desc="assigns all and required fields">
        $addressFormat = AddressFormat::getOrderedAddressFields($id_country, false, true);
        $addressItems = array();
        foreach ($addressFormat as $addressline) {
            foreach (explode(' ', $addressline) as $addressItem) {
                $addressItems[] = trim($addressItem);
            }
        }

        $requireFormFieldsList = AddressFormat::getFieldsRequired();
        // Add missing require fields for a new user susbscription form
        foreach ($requireFormFieldsList as $fieldName) {
            if (!in_array($fieldName, $addressItems)) {
                $addressItems[] = trim($fieldName);
            }
        }

        foreach (array('inv', 'dlv') as $addressType) {
            $smarty->assign(array(
                $addressType . '_adr_fields' => $addressFormat,
                $addressType . '_all_fields' => $addressItems,
                'required_fields' => $requireFormFieldsList
            ));
        }
        //</editor-fold>

        /* @var CustomerCore $customer */
        $customer = $this->context->customer;
        $countries = $this->loadCountries();

        $smarty->assign('countries', $countries);
        $smarty->assign('is_logged', $customer->isLogged(false));
        $smarty->assign('customer', $customer);
        $smarty->assign('current_country', $id_country);
        $smarty->assign('one_phone_at_least', (int)Configuration::get('PS_ONE_PHONE_AT_LEAST'));

        $html = '';
        try {
            $html = $smarty->fetch(_PS_MODULE_DIR_ .$this->module_name. '/vendor/kien/prestashop-onepage-lib/src/views/templates/front/shipping-address-form.tpl');
        } catch (Exception $e) {
            ob_end_clean();
            echo "ERROR " . get_class($e) . ": " . $e->getMessage();
        }
        return $html;
    }

    // Get all alert-message.
    private function getAlertForCreateNewAccount()
    {
        $message_warn = array();
        $address = new Address();
        $message_warn = $address->validateController();
        $message_warn = array_merge($message_warn, $address->validateFieldsRequiredDatabase());
        $this->context->smarty->assign('message_alert_required', ($message_warn));
    }

    /**
     *  Get Countries
     */
    public function getCountries()
    {
        $countries = $this->loadCountries();
        $this->ajaxDie(Tools::jsonEncode($countries));
    }

    private function updateAddress($addressUpdate, $address_id = null)
    {
        // Get current step.
        $step = Tools::getValue('step');
        if (!$address_id) {
            //Get id of address Edit-Update.
            $address_id = Tools::getValue($step . '_address_id') ? Tools::getValue($step . '_address_id') : 0;
        }

        // Create new Obj needed
        $address_old = new Address($address_id);
        $address = new AddressCore();
        $address = $addressUpdate;
        $country = new Country($address->id_country);

        //
        $address->id_customer = (int)$this->context->customer->id;
        $address->id = null;

        // If we edit this address, delete old address and create a new one
        if (Validate::isLoadedObject($address_old)) {
            if (Validate::isLoadedObject($country) && !$country->contains_states) {
                $address->id_state = 0;
            }
            if (Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id)) {
                if ($address_old->isUsed()) {
                    $address_old->delete();
                } else {
                    $address->id = (int)$address_old->id;
                    $address->date_add = $address_old->date_add;
                }
            }
        }
        $address->save();
    }

    /**
     * @function  submitBilling()
     */
    private function submitBilling()
    {
        /**
         *  1. create new guest account
         *  2. create new address
         *  3. Edit Address
         *  4. Select Address
         *
         *  Continue: 1. step 3 - Shipping Methods
         *            2. Step 2 - Shippping
         *            3. errors
         *            4.
         */

        $this->billing_params = Tools::getValue('billing');
        $this->shipping_params = Tools::getValue('shipping');
        $this->current_step = Tools::getValue('step');

        /** @var CustomerCore $customer */
        $customer = $this->context->customer;
        $guest = null;

        if (!isset($customer) || !$customer->isLogged(true)) {
            $guest = $this->createGuestAccount();
            $this->processValidationGuestAcount($this->billing_params);

            if ($guest) {
                $customer = $guest;
//                $this->updateContext($guest);
            }
        }

        // Process address.
        if ($guest == null) {
            $guest = $this->context->customer;
        }
        if (!count($this->errors)) {
            $return = $this->processSubmitAddress($guest);

            // return.
            if (!count($this->errors)) {
                $return = $this->processForNextStep(
                    $return['customer'],
                    $return['billing_address'],
                    $return['shipping_address']
                );
            }

            $this->updateContext($customer);
        }
        if ($this->errors) {
            //Convert format err.
            $temp = array();
            foreach ($this->errors as $err) {
                $temp[] = $err;
            }
            $this->errors = $temp;

            $return = array(
                'hasError' => !empty($this->errors),
                'errors' => $this->errors
            );
        }
        $this->ajaxDie(Tools::jsonEncode($return));
    }

    public function processForNextStep($customer, $billing_address, $shipping_address)
    {
        $current_step = $this->current_step;

        $id_billing_address = $billing_address ? $billing_address->id : Tools::getValue('billing_address_id');
        $id_shipping_address = $shipping_address ? $shipping_address->id : Tools::getValue('shipping_address_id');

        $billing_params = $this->billing_params;
        $shipping_params = $this->shipping_params;

        $use_for_shipping = isset($billing_params['use_for_shipping']) ? $billing_params['use_for_shipping'] : 0;
        $same_as_billing = isset($shipping_params['same_as_billing']) ? $shipping_params['same_as_billing'] : 0;

        if (!count($this->errors)) {
            if (!$customer->is_guest) {
                $this->context->customer = $customer;
                $customer->cleanGroups();
                // we add the guest customer in the default customer group
                $customer->addGroups(array((int)Configuration::get('PS_CUSTOMER_GROUP')));
//                            if (!$this->sendConfirmationMail($customer)) {
//                                $this->errors[] = Tools::displayError('The email cannot be sent.');
//                            }
            } else {
                $customer->cleanGroups();
                // we add the guest customer in the guest customer group
                $customer->addGroups(array((int)Configuration::get('PS_GUEST_GROUP')));
            }
            $this->updateContext($customer);
            if ($current_step == 'billing') {
                $this->context->cart->id_address_invoice = $id_billing_address;
                if ($use_for_shipping) {
                    $this->context->cart->id_address_delivery = $id_shipping_address;
                }
            } else {
                $this->context->cart->id_address_delivery = $id_shipping_address;
                if ($same_as_billing) {
                    $this->context->cart->id_address_invoice = $this->context->cart->id_address_delivery;
                }
            }

            $delivery_option =
                array((int)$this->context->cart->id_address_delivery => (int)$this->context->cart->id_carrier . ',');
            $this->context->cart->setDeliveryOption($delivery_option);

            // If a logged guest logs in as a customer, the cart secure key was already set and needs to be updated
            $this->context->cart->update();

            // Avoid articles without delivery address on the cart
            $this->context->cart->autosetProductAddress();

            $goToSection = 'shipping';
            $dupplicateBillingInfo = false;
            $shipmentHtmlContent = '';
            $allow_sections = array();

            if ($current_step == 'billing') {
                if ($use_for_shipping) {
                    $allow_sections[] = 'shipping';
                    $goToSection = 'shipping_method';
                    $dupplicateBillingInfo = true;
                    // load shipping method
                    $shipmentContent = $this->getCarrierList(
                        $shipping_address->id_country,
                        $shipping_address->id_state,
                        $shipping_address->postcode,
                        $shipping_address->city,
                        $id_shipping_address,
                        null
                    );
                    if (!$shipmentContent['hasError']) {
                        $shipmentHtmlContent = $shipmentContent['carrier_block'];
                    } else {
                        if (array_key_exists('errors', $shipmentContent)) {
                            $this->errors = $shipmentContent['errors'];
                        } else {
                            $this->errors[] = $this->module->l(
                                'There are no carriers that deliver to the address you selected.',
                                'JmCheckout16'
                            );
                        }
                    }
                } else {
                    $goToSection = 'shipping';
                    $dupplicateBillingInfo = false;
                }
            } else {
                $goToSection = 'shipping_method';
                $dupplicateBillingInfo = false;
                $allow_sections[] = 'shipping';
                // load shipping method
                $shipmentContent = $this->getCarrierList(
                    $shipping_address->id_country,
                    $shipping_address->id_state,
                    $shipping_address->postcode,
                    $shipping_address->city,
                    $id_shipping_address,
                    null
                );

                if (!$shipmentContent['hasError']) {
                    $shipmentHtmlContent = $shipmentContent['carrier_block'];
                } else {
                    if (array_key_exists('errors', $shipmentContent)) {
                        $this->errors = $shipmentContent['errors'];
                    } else {
                        $this->errors[] = $this->module->l(
                            'There are no carriers that deliver to the address you selected.',
                            'JmCheckout16'
                        );
                    }
                }
            }

            Hook::exec('actionCustomerAccountAdd', array(
                '_POST' => $_POST,
                'newCustomer' => $customer
            ));

            $addresses = array();
            if ($customer->id != 0) {
                $addresses = $customer->getAddresses($this->context->language->id);
            }
            $this->assignAddressFormat();
            if ($this->ajax) {
                $return = array(
                    'shipping_fields' => $this->getAddressFormFields('shipping'),
                    'allow_sections' => $allow_sections,
                    'goto_section' => $goToSection,
                    'updated_section' => $shipmentHtmlContent,
                    'duplicateBillingInfo' => $dupplicateBillingInfo,
                    'hasError' => !empty($this->errors),
                    'errors' => $this->errors,
                    'isSaved' => true,
                    'id_customer' => (int)$this->context->cookie->id_customer,
                    'id_address_delivery' => $this->context->cart->id_address_delivery,
                    'id_address_invoice' => $this->context->cart->id_address_invoice,
                    'addresses' => $addresses,
                    'token' => Tools::getToken(false)
                );
                return $return;
            }
        }
        $return = array(
            'hasError' => !empty($this->errors),
            'errors' => $this->errors
        );
        return $return;
    }

    public function processSubmitAddress($customer)
    {
        $id_billing_address = Tools::getValue('billing_address_id') ? Tools::getValue('billing_address_id') : 0;
        $id_shipping_address = Tools::getValue('shipping_address_id') ? Tools::getValue('shipping_address_id') : 0;

        $billing_params = $this->billing_params;
        $shipping_params = $this->shipping_params;

        $use_for_shipping = isset($billing_params['use_for_shipping']) ? $billing_params['use_for_shipping'] : 0;
        $same_as_billing = isset($shipping_params['same_as_billing']) ? $shipping_params['same_as_billing'] : 0;

        $current_step = $this->current_step;
        //validate (address)  $addressInfo

        if ($current_step == 'billing') {
            $this->processValidationBilling($id_billing_address, $customer, $billing_params);
        } elseif ($current_step == 'shipping') {
            $this->processValidationShipping($id_shipping_address, $customer, $shipping_params);
        }

        $billing_address = null;
        $shipping_address = null;

        if (!count($this->errors)) {
            if ($current_step == 'billing') {
                if ($id_billing_address == 0) {
                    $billing_address = new Address();
                } else {
                    $billing_address = new Address($id_billing_address);

                    if ($use_for_shipping) {
                        $id_shipping_address = $id_billing_address;
                        $shipping_address = new Address($id_shipping_address);
                    }
                }

                if ($id_billing_address == 0
                    || ($id_billing_address != 0 && $customer->is_guest)
                    || ($id_billing_address != 0 && $billing_params['edit'] == 1)
                ) {
                    $billing_address->firstname = (!empty($billing_params['firstname'])) ? $billing_params['firstname'] : ' ';
                    $billing_address->lastname = (!empty($billing_params['lastname'])) ? $billing_params['lastname'] : ' ';
                    $billing_address->company = (!empty($billing_params['company'])) ? $billing_params['company'] : ' ';
                    $billing_address->vat_number = (!empty($billing_params['vat_number'])) ? $billing_params['vat_number'] : ' ';
                    $billing_address->address1 = (!empty($billing_params['street'])) ? $billing_params['street'] : ' ';
                    $billing_address->address2 = (!empty($billing_params['street2'])) ? $billing_params['street2'] : ' ';
                    $billing_address->city = (!empty($billing_params['city'])) ? $billing_params['city'] : ' ';
                    $billing_address->phone = (!empty($billing_params['telephone'])) ? $billing_params['telephone'] : '';
                    $billing_address->phone_mobile = (!empty($billing_params['phone_mobile'])) ? $billing_params['phone_mobile'] : ' ';
                    $billing_address->id_country = (!empty($billing_params['country_id']))
                        ? $billing_params['country_id']
                        : (int)Configuration::get('PS_COUNTRY_DEFAULT');

                    if (Country::getNeedZipCode($billing_address->id_country)) {
                        $billing_address->postcode = $billing_params['postcode'];
                    }

                    $billing_address->id_state = (!empty($billing_params['state_id'])) ? $billing_params['state_id'] : 0;

                    if (!Country::containsStates($billing_address->id_country)) {
                        $billing_address->id_state = 0;
                    }

                    $billing_address->vat_number = (!empty($billing_params['vat_number'])) ? $billing_params['vat_number'] : '';

                    if (Country::isNeedDniByCountryId($billing_address->id_country)) {
                        $billing_address->dni = $billing_params['dni'];
                    }

                    $billing_address->alias = (isset($billing_params['alias']))
                        ? (empty($billing_params['alias']))
                            ? $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9)
                            : $billing_params['alias']
                        : $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9);
                    $billing_address->other = (!empty($billing_params['other'])) ? $billing_params['other'] : '';
                    $billing_address->id_customer = $customer->id;

                    $validate_address = $billing_address->validateController();
                    if ($validate_address && count($validate_address) > 0) {
                        foreach ($validate_address as $key => $value) {
                            $this->errors[] = sprintf(Tools::displayError('The field %s is required.'), $key);
                        }
                    } else {
                        if ($this->updateAddress($billing_address)) {
                            $this->errors[] = Tools::displayError('Error occurred while creating new address');
                        } else {
                            $id_billing_address = $billing_address->id;

                            if ($use_for_shipping) {
                                $id_shipping_address = $id_billing_address;
                                $shipping_address = new Address($id_shipping_address);
                            }
                        }
                    }
                }
            }

            if ($current_step == 'shipping') {
                if (!$same_as_billing) {
                    if ($id_shipping_address == 0) {
                        $shipping_address = new Address();
                    } else {
                        $shipping_address = new Address($id_shipping_address);
                    }
                } else {
                    if ($id_shipping_address == 0) {
                        $shipping_address = new Address();
                    } else {
                        $id_shipping_address = $this->context->cart->id_address_invoice;
                        $shipping_address = new Address($id_shipping_address);
                    }
                }

                if ($id_shipping_address == 0
                    || ($id_shipping_address != 0 && ($customer->is_guest))
                    || ($id_shipping_address != 0 && $shipping_params['edit'] == 1)
                ) {
                    $shipping_address->firstname = (!empty($shipping_params['firstname'])) ? $shipping_params['firstname'] : ' ';
                    $shipping_address->lastname = (!empty($shipping_params['lastname'])) ? $shipping_params['lastname'] : ' ';
                    $shipping_address->company = (!empty($shipping_params['company'])) ? $shipping_params['company'] : ' ';
                    $shipping_address->vat_number = (!empty($shipping_params['vat_number'])) ? $shipping_params['vat_number'] : ' ';
                    $shipping_address->address1 = (!empty($shipping_params['street'])) ? $shipping_params['street'] : ' ';
                    $shipping_address->address2 = (!empty($shipping_params['street2'])) ? $shipping_params['street2'] : ' ';
                    $shipping_address->city = (!empty($shipping_params['city'])) ? $shipping_params['city'] : ' ';
                    $shipping_address->phone = (!empty($shipping_params['telephone'])) ? $shipping_params['telephone'] : ' ';
                    $shipping_address->phone_mobile = (!empty($shipping_params['phone_mobile'])) ? $shipping_params['phone_mobile'] : ' ';
                    $shipping_address->id_country = (!empty($shipping_params['country_id']))
                        ? $shipping_params['country_id']
                        : (int)Configuration::get('PS_COUNTRY_DEFAULT');

                    if (Country::getNeedZipCode($shipping_address->id_country)) {
                        $shipping_address->postcode = $shipping_params['postcode'];
                    }
                    $shipping_address->id_state = (!empty($shipping_params['state_id'])) ? $shipping_params['state_id'] : 0;

                    if (!Country::containsStates($shipping_address->id_country)) {
                        $shipping_address->id_state = 0;
                    }
                    $shipping_address->vat_number = (!empty($shipping_params['vat_number'])) ? $shipping_params['vat_number'] : '';

                    if (Country::isNeedDniByCountryId($shipping_address->id_country)) {
                        $shipping_address->dni = $shipping_params['dni'];
                    }
                    $shipping_address->alias = (isset($shipping_params['alias']))
                        ? (empty($shipping_params['alias']))
                            ? $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9)
                            : $shipping_params['alias']
                        : $this->module->l('Title Delivery Alias', 'JmCheckout16') . ' - ' . date('s') . rand(0, 9);
                    $shipping_address->other = (!empty($shipping_params['other'])) ? $shipping_params['other'] : '';
                    $shipping_address->id_customer = $customer->id;

                    $validate_address = $shipping_address->validateController();
                    if ($validate_address && count($validate_address) > 0) {
                        foreach ($validate_address as $key => $value) {
                            $this->errors[] = Tools::displayError($this->getPlainTextFromHtml($value));
                        }
                    } else {
                        if ($this->updateAddress($shipping_address)) {
                            $this->errors[] = Tools::displayError('Error occurred while creating new address');
                        } else {
                            $id_shipping_address = $shipping_address->id;
                        }
                    }
                }

                //save message from "comment-box" DELIVERY ADDRESS
                $new_message = (string)Tools::getValue('message_box');
                $this->updateMessageToCart($new_message);
            }
        }

        return array(
            'customer' => $customer,
            'billing_address' => $billing_address,
            'shipping_address' => $shipping_address
        );
    }

    public function createGuestAccount()
    {
        $billing_params = Tools::getValue('billing');
        $this->billing_params = $billing_params;
        $email = isset($billing_params['email']) ? ($billing_params['email']) : null;

        if (!Validate::isEmail($email) || empty($email)) {
            $this->errors[] = Tools::displayError('Invalid email address.');
        } elseif (Customer::customerExists($email)) {
            $this->errors[] = Tools::displayError(
                'An account using this email address has already been registered.'
            );
        } else {
            $this->create_account = true;
            $billing_params['email'] = $email;
        }

        $this->create_account = true;
        // New Guest customer

        if (!Configuration::get('PS_GUEST_CHECKOUT_ENABLED')) {
            $this->errors[] = Tools::displayError('You cannot create a guest account.');
        }
        $billing_params['passwd'] = md5(time() . _COOKIE_KEY_);
        $customer = new Customer();
        $lastname = $billing_params['lastname'];
        $firstname = $billing_params['firstname'];
        $idGender = (isset($billing_params['gender_id']) ? $billing_params['gender_id'] : '');

        // PS-861 : [Ps 16] Onepage checkout - Display wrong error message  in case I input invalid Date of birth.
        $birthDay = (empty($billing_params['years']) ? '' : (int)$billing_params['years'] . '-' . (int)$billing_params['months'] . '-' . (int)$billing_params['days']);
        if (!@checkdate($billing_params['months'], $billing_params['days'], $billing_params['years']) && !($billing_params['months'] == '' && $billing_params['days'] == '' && $billing_params['years'] == '')) {
            $this->errors[] = Tools::displayError('Invalid date of birth');
        } else {
            if (!Validate::isBirthDate($birthDay)) {
                $this->errors[] = Tools::displayError('Invalid date of birth');
            }
        }

        $customer->days = (int)$billing_params['days'];
        $customer->years = (int)$billing_params['years'];
        $customer->months = (int)$billing_params['months'];

        $customer->optin = Tools::getValue('optin');
        $customer->newsletter = Tools::getValue('newsletter');

        $customer->birthday = $birthDay;
        $customer->id_gender = $idGender;
        $customer->firstname = $firstname;
        $customer->lastname = $lastname;
        $customer->passwd = md5(time() . _COOKIE_KEY_);
        $customer->email = $email;
        $customer->active = 1;
        $customer->is_guest = 1;

        $this->errors = array_unique(array_merge($this->errors, $customer->validateController()));
        $this->errors = $this->errors + $customer->validateFieldsRequiredDatabase();

        //convert err.
        if (!count($this->errors)) {
            if (!$customer->add()) {
                $this->errors[] = Tools::displayError('An error occurred while creating your account.');
                return false;
            } else {
                return $customer;
            }
        }
        return false;
    }

    protected function processValidationBilling($id_billing_address, $customer, $billing_params)
    {
        if ($id_billing_address == 0
            || ($id_billing_address != 0 && $customer->is_guest)
            || ($id_billing_address != 0 && $billing_params['edit'] == 1)) {
            if (!($country = new Country($billing_params['country_id']))
                || !Validate::isLoadedObject($country)) {
                $this->errors[] = htmlspecialchars_decode(
                    Tools::displayError('Country cannot be loaded with address->id_country'),
                    ENT_QUOTES
                );
            }

            if (!$country->active) {
                $this->errors[] = Tools::displayError('This country is not active.');
            }

            $postcode = $billing_params['postcode'];
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] = htmlspecialchars_decode(sprintf(
                    Tools::displayError('The Zip/Postal code you\'ve entered is invalid.
                            It must follow this format: %s'),
                    str_replace(
                        'C',
                        $country->iso_code,
                        str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
                    )
                ), ENT_QUOTES);
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = htmlspecialchars_decode(
                    Tools::displayError('A Zip / Postal code is required.'),
                    ENT_QUOTES
                );
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = htmlspecialchars_decode(
                    Tools::displayError('The Zip / Postal code is invalid.'),
                    ENT_QUOTES
                );
            }

            if ($country->need_identification_number
                && (!$billing_params['dni']
                    || !Validate::isDniLite($billing_params['dni']))) {
                $this->errors[] = Tools::displayError(
                    'The identification number is incorrect or has already been used.'
                );
            } elseif (!$country->need_identification_number) {
                $billing_params['dni'] = null;
            }

            $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states : 0;
            $id_state = isset($billing_params)
            && isset($billing_params['state_id']) ? (int)$billing_params['state_id'] : 0;
            if ((Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state) {
                $this->errors[] = Tools::displayError('This country requires you to choose a State.');
            }
            if (!$this->context->customer->is_guest
                && !empty($billing_params['alias'])
                && (int)$this->context->customer->id > 0) {
                if (Address::aliasExist(
                    $billing_params['alias'],
                    $id_billing_address,
                    (int)$this->context->customer->id
                )) {
                    $this->errors[] = htmlspecialchars_decode(sprintf(
                        Tools::displayError('The alias "%s" has already been used. Please select another one.'),
                        $billing_params['alias']
                    ), ENT_QUOTES);
                }
            }
            // Check phone
            if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && empty($billing_params['telephone']) && empty($billing_params['phone_mobile'])) {
                $this->errors[] = Tools::displayError('You must register at least one phone number.');
            }
        }
    }

    protected function processValidationShipping($id_shipping_address, $customer, $shipping_params)
    {
        if ($id_shipping_address == 0
            || ($id_shipping_address != 0 && $customer->is_guest)
            || ($id_shipping_address != 0 && $shipping_params['edit'] == 1)
        ) {
            if (!($country = new Country($shipping_params['country_id']))
                || !Validate::isLoadedObject($country)) {
                $this->errors[] = htmlspecialchars_decode(
                    Tools::displayError('Country cannot be loaded with address->id_country'),
                    ENT_QUOTES
                );
            }

            if (!$country->active) {
                $this->errors[] = Tools::displayError('This country is not active.');
            }

            $postcode = $shipping_params['postcode'];
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] = htmlspecialchars_decode(sprintf(
                    Tools::displayError(
                        'The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'
                    ),
                    str_replace(
                        'C',
                        $country->iso_code,
                        str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
                    )
                ), ENT_QUOTES);
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = htmlspecialchars_decode(Tools::displayError(
                    'A Zip / Postal code is required.'
                ), ENT_QUOTES);
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = htmlspecialchars_decode(Tools::displayError(
                    'The Zip / Postal code is invalid.'
                ), ENT_QUOTES);
            }

            if ($country->need_identification_number
                && (!$shipping_params['dni']
                    || !Validate::isDniLite($shipping_params['dni']))) {
                $this->errors[] = Tools::displayError(
                    'The identification number is incorrect or has already been used.'
                );
            } elseif (!$country->need_identification_number) {
                $shipping_params['dni'] = null;
            }

            $contains_state = isset($country) && is_object($country) ? (int)$country->contains_states : 0;
            $id_state = isset($shipping_params)
            && isset($shipping_params['state_id']) ? (int)$shipping_params['state_id'] : 0;
            if ((Tools::isSubmit('submitGuestAccount')) && $contains_state && !$id_state) {
                $this->errors[] = Tools::displayError('This country requires you to choose a State.');
            }
            if (!$this->context->customer->is_guest
                && !empty($shipping_params['alias'])
                && (int)$this->context->customer->id > 0) {
                if (Address::aliasExist($shipping_params['alias'], $id_shipping_address, (int)$this->
                context->customer->id)) {
                    $this->errors[] = htmlspecialchars_decode(sprintf(Tools::displayError(
                        'The alias "%s" has already been used. Please select another one.'
                    ), Tools::safeOutput($shipping_params['alias'])), ENT_QUOTES);
                }
            }
        }
    }

    protected function processValidationGuestAcount($billing_params)
    {
        if ((!count($this->errors)) && (!$this->is_logged)) {
            $address = new Address();
            $address->id_country = (isset($billing_params['country_id']) && ($billing_params['country_id'] != '')) ? $billing_params['country_id'] : null;
            $address->address2 = (isset($billing_params['street2']) && ($billing_params['street2'] != ' ')) ? $billing_params['street2'] : null;
            $address->address1 = (isset($billing_params['street']) && ($billing_params['street'] != "")) ? $billing_params['street'] : null;
            $address->firstname = (isset($billing_params['firstname']) && ($billing_params['firstname'] != '')) ? $billing_params['firstname'] : null;
            $address->lastname = (isset($billing_params['lastname']) && ($billing_params['lastname'] != '')) ? $billing_params['lastname'] : null;
            $address->city = (isset($billing_params['city']) && ($billing_params['city'] != '')) ? $billing_params['city'] : null;
            $address->company = (isset($billing_params['company']) && ($billing_params['company'] != '')) ? $billing_params['company'] : null;
            $address->postcode = (isset($billing_params['postcode']) && ($billing_params['postcode'] != '')) ? $billing_params['postcode'] : null;
            $address->other = (isset($billing_params['other']) && ($billing_params['other'] != '')) ? $billing_params['other'] : null;
            $address->phone = (isset($billing_params['telephone']) && ($billing_params['telephone'] != '')) ? $billing_params['telephone'] : null;
            $address->phone_mobile = (isset($billing_params['phone_mobile']) && ($billing_params['phone_mobile'] != '')) ? $billing_params['phone_mobile'] : null;
            $address->dni = (isset($billing_params['dni']) && ($billing_params['dni'] != '')) ? $billing_params['dni'] : null;
            $address->vat_number = (isset($billing_params['vat_number']) && ($billing_params['vat_number'] != '')) ? $billing_params['vat_number'] : null;
            $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
        }
    }
}

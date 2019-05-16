<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

class KbMobileAppAppPaymentModuleFrontController extends ModuleFrontController
{
    public $controller_name = 'AppPayment';
    public $module_name = 'kbmobileapp';
    public $app_version = 1.1;
    public $error = array();
    protected $checkout_session = null;
    protected $checkoutProcess = null;

    /*
     * Build an front controller
     */
    public function __construct()
    {
        parent::__construct();
    }
    
    /*
     * Defualt prestashop function for front controller
     */
    public function init()
    {
        parent::init();
        $this->cartChecksum = new CartChecksum(new AddressChecksum());
    }

    /*
     * Default front controller initialize function
     */
    public function initContent()
    {
        parent::initContent();
        $this->context = Context::getContext();
        $this->context->cookie->kbmobileapp = 1;
        $cart_id = Tools::getValue('session_data', 0);
        if (!(int) $cart_id) {
            $load_cart_err = $this->module->l('Unable to load cart, cart id is missing.', 'AppPayment');
            $this->error[] = $load_cart_err;
        } else {
            if ($this->validateCustomer()) {
                $id_currency = Tools::getValue('id_currency', $this->context->currency->id);
                if (Tools::isSubmit('iso_code')) {
                    $id_language = (int)Language::getIdByIso(Tools::getValue('iso_code', ''));
                    if ($id_language) {
                        $this->context->language = new Language($id_language);
                        $this->context->cookie->id_lang = $id_language;
                    }
                }
                $this->context->currency = new Currency($id_currency, null, $this->context->shop->id);
                $this->context->cart = new Cart((int) $cart_id, false, null, null, $this->context);
                
                if (!Validate::isLoadedObject($this->context->cart)) {
                    $cart_invalid_err = $this->module->l('Unable to load cart, cart is not valid.', 'AppPayment');
                    $this->error[] = $cart_invalid_err;
                } else {
                    $id_shipping_address = Tools::getValue('id_shipping_address', '');
                    $address = new Address($id_shipping_address);
                    if (!validate::isLoadedObject($address)) {
                        $ship_addr_err = $this->module->l('Shipping address is not valid.', 'AppPayment');
                        $this->error[] = $ship_addr_err;
                    } else {
                        $this->app_version = Tools::getValue('version', 1.1);
                        $this->context->cart = new Cart($cart_id);
                        $deliveryOptionsFinder = new DeliveryOptionsFinder(
                            $this->context,
                            $this->getTranslator(),
                            $this->objectPresenter,
                            new PriceFormatter()
                        );

                        $this->checkout_session = new CheckoutSession(
                            $this->context,
                            $deliveryOptionsFinder
                        );
                        
                        if (Tools::getIsset('order_message')) {
                            $txt_message = urldecode(Tools::getValue('order_message', ''));
                            $this->updateMessage($txt_message);
                        }
                        /* Set per product shipping methods id in cookie */
                        if (Tools::getIsset('pp_shippings')) {
                            $carriers_array = array();
                            $selected_carriers = Tools::getValue('pp_shippings', Tools::jsonEncode(array()));
                            $selected_carriers = Tools::jsonDecode($selected_carriers);
                            if (!empty($selected_carriers)) {
                                foreach ($selected_carriers as $data) {
                                    $carriers_array[$data->product_id] = $data->shipping_id;
                                }
                                $this->context->cookie->kb_selected_carrier = serialize($carriers_array);
                            }
                        }
                        $carrier_selected = $this->context->cart->id_carrier;
                        
                        $id_carrier = array(
                            $id_shipping_address => $carrier_selected . ','
                        );
                        $this->checkout_session->setDeliveryOption(
                            $id_carrier
                        );
                        $id_invoice_address = $this->context->cart->id_address_invoice;
                        $this->checkout_session->setIdAddressDelivery($id_shipping_address);
                        $this->checkout_session->setIdAddressInvoice($id_invoice_address);
                        $this->context->cookie->checkedTOS = 1;
                        $this->context->cart->id_address_delivery = (int) $id_shipping_address;
                        $this->context->cart->id_address_invoice = (int) $id_invoice_address;
                        $this->context->cart->id_currency = (int)$id_currency;
                        $this->context->cart->secure_key = $this->context->customer->secure_key;
                        $this->context->cart->save();
                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                        $this->context->cookie->id_currency = $id_currency;
                        $this->context->cookie->write();
                        $this->context->cart->autosetProductAddress();
                        
                        $this->checkoutProcess = new CheckoutProcess(
                            $this->context,
                            $this->checkout_session
                        );
                        
                        $this->addCheckoutStep();
                        $address_step = array(
                            'id_address_delivery' => $id_shipping_address,
                            'id_address_invoice' => $id_invoice_address,
                            'confirm-addresses' => 1,
                            'controller' => 'order'
                        );
                        
                        $this->handleCheckoutSession($address_step);
                        
                        $shipping_step_value = array(
                            'delivery_option' => $id_carrier,
                            'confirmDeliveryOption' => 1,
                            'controller' => 'order'
                        );
                        
                        
                        $this->handleCheckoutSession($shipping_step_value);
                        $this->saveDataToPersistPayment($this->checkoutProcess);
                    }
                }
            }
        }

        if ($this->error) {
            $this->context->smarty->assign(array(
                'kberror' => $this->error,
                'HOOK_HEADER' => '',
            ));
            $this->setTemplate('module:kbmobileapp/views/templates/front/kberror.tpl');
        } else {
            $advanced_payment_api = (bool) Configuration::get('PS_ADVANCED_PAYMENT_API');
            $this->context->smarty->assign(array(
                'advanced_payment_api' => $advanced_payment_api
            ));
            $this->context->smarty->assign(array(
                'HOOK_LEFT_COLUMN' => null,
                'HOOK_RIGHT_COLUMN' => null,
            ));
            $this->display_footer = false;
            $this->assignPayment();
            $this->context->smarty->assign('opc', 1);
            /* Changes start by rishabh jain on 3rd sep 2018
             * To prevent redirecting to supercheckout page
             * Added klarna_supercheckout perimeter in redirecting url as this condition is already been there in supercheckout versions.
             */
            Tools::redirect('index.php?controller=order&klarna_supercheckout=1');
            /* Changes over */
        }
    }

    /*
     * Function to assign samrt variables for tpl
     */
    protected function assignPayment()
    {
        if ((bool) Configuration::get('PS_ADVANCED_PAYMENT_API')) {
            $this->addJS(_THEME_JS_DIR_ . 'advanced-payment-api.js');
            $this->context->smarty->assign(array(
                'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                'HOOK_ADVANCED_PAYMENT' => Hook::exec('advancedPaymentOptions', array(), null, true),
                'link_conditions' => $this->link_conditions
            ));
        } else {
            $this->context->smarty->assign(array(
                'HOOK_TOP_PAYMENT' => Hook::exec('displayPaymentTop'),
                'HOOK_PAYMENT' => Hook::exec('displayPayment'),
            ));
        }
    }

    /*
     * Function to validate the customer
     *
     * @return bool
     */
    public function validateCustomer()
    {
        $email = Tools::getValue('email', '');
        if (!Validate::isEmail($email)) {
            $invalid_email_err = $this->module->l('Customer Email is not Valid', 'AppPayment');
            $this->error[] = $invalid_email_err;
            return false;
        } else {
            if (Customer::customerExists(strip_tags($email), false, false)) {
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email, null, false);

                $customer = new Customer($customer_tmp->id);

                /* Update Context */
                $this->context->customer = $customer;
                $this->context->cookie->id_customer = (int) $customer->id;
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->logged = 1;
                $this->context->cookie->email = $customer->email;
                $this->context->cookie->is_guest = $customer->is_guest;
                return true;
            } else {
                $email_exist_err = $this->module->l('Customer with this email not exist', 'AppPayment');
                $this->error[] = $email_exist_err;
                return false;
            }
        }
    }

    /*
     * Function to set the order message
     *
     * @param string $messageContent message content
     * @return bool
     */
    protected function updateMessage($messageContent)
    {
        if ($messageContent) {
            if (!Validate::isMessage($messageContent)) {
                $invalid_msg_err = $this->module->l('Invalid message', 'AppPayment');
                $this->error[] = $invalid_msg_err;
            } elseif ($oldMessage = Message::getMessageByCartId((int) $this->context->cart->id)) {
                $message = new Message((int) $oldMessage['id_message']);
                $message->message = $messageContent;
                $message->update();
            } else {
                $message = new Message();
                $message->message = $messageContent;
                $message->id_cart = (int) $this->context->cart->id;
                $message->id_customer = (int) $this->context->cart->id_customer;
                $message->add();
            }
        } else {
            if ($oldMessage = Message::getMessageByCartId($this->context->cart->id)) {
                $message = new Message($oldMessage['id_message']);
                $message->delete();
            }
        }
        return true;
    }
    
    /*
     * Function to perisit the order step
     *
     * @param Object CheckoutProcess $process process object
     */
    private function saveDataToPersist(CheckoutProcess $process)
    {
        $data = $process->getDataToPersist();

        $data['checksum'] = $this->cartChecksum->generateChecksum($this->context->cart);

        Db::getInstance()->execute(
            'UPDATE '._DB_PREFIX_.'cart SET checkout_session_data = "'.pSQL(Tools::jsonEncode($data)).'"
                WHERE id_cart = '.(int) $this->context->cart->id
        );
    }

    /*
     * Function to handlecheckout process request
     *
     * @param array $data
     */
    private function handleCheckoutSession($data)
    {
        $this->restorePersistedData($this->checkoutProcess);
        $this->checkoutProcess->handleRequest(
            $data
        );

        $presenter = new CartPresenter();
        $presented_cart = $presenter->present($this->context->cart);

        $this->checkoutProcess
            ->setNextStepReachable()
            ->markCurrentStep()
            ->invalidateAllStepsAfterCurrent();

        $this->saveDataToPersist($this->checkoutProcess);
    }
    
    /*
     * Function to add checkout step
     */
    private function addCheckoutStep()
    {
        
        $this->checkoutProcess
            ->addStep(new CheckoutPersonalInformationStep(
                $this->context,
                $this->getTranslator(),
                $this->makeLoginForm(),
                $this->makeCustomerForm()
            ))
            ->addStep(new CheckoutAddressesStep(
                $this->context,
                $this->getTranslator(),
                $this->makeAddressForm()
            ));
        
        if (!$this->context->cart->isVirtualCart()) {
            $checkoutDeliveryStep = new CheckoutDeliveryStep(
                $this->context,
                $this->getTranslator()
            );

            $checkoutDeliveryStep
                ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
                ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
                ->setIncludeTaxes(
                    !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                    && (int) Configuration::get('PS_TAX')
                )
                ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
                ->setGiftCost(
                    $this->context->cart->getGiftWrappingPrice(
                        $checkoutDeliveryStep->getIncludeTaxes()
                    )
                );

            $this->checkoutProcess->addStep($checkoutDeliveryStep);
        }

        $this->checkoutProcess
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $this->getTranslator(),
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $this->getTranslator()
                )
            ));
    }
    
    /*
     * Function to restore the cheout process data
     *
     * @param Object CheckoutProcess $process checkout process object
     */
    private function restorePersistedData(CheckoutProcess $process)
    {
        $rawData = Db::getInstance()->getValue(
            'SELECT checkout_session_data FROM '._DB_PREFIX_.'cart WHERE id_cart = '.(int) $this->context->cart->id
        );
        $data = json_decode($rawData, true);
        if (!is_array($data)) {
            $data = array();
        }

        $checksum = $this->cartChecksum->generateChecksum($this->context->cart);
        
        if (isset($data['checksum']) && $data['checksum'] === $checksum) {
            $process->restorePersistedData($data);
        }
    }
    
    
    /*
     * Function to save checkout process step data
     *
     * @param Object CheckoutProcess $process checkout process object
     */
    private function saveDataToPersistPayment(CheckoutProcess $process)
    {
        $data = array(
            'checkout-personal-information-step' => array
            (
                'step_is_reachable' => 1,
                'step_is_complete' => 1
            ),
            'checkout-addresses-step' => array
            (
                'step_is_reachable' => 1,
                'step_is_complete' => 1,
                'use_same_address' => 1
            ),
            'checkout-delivery-step' => array
            (
                'step_is_reachable' => 1,
                'step_is_complete' => 1
            ),
            'checkout-payment-step' => array
            (
                'step_is_reachable' => 1,
                'step_is_complete' => false,
            )
        );

        $data['checksum'] = $this->cartChecksum->generateChecksum($this->context->cart);

        Db::getInstance()->execute(
            'UPDATE '._DB_PREFIX_.'cart SET checkout_session_data = "'.pSQL(Tools::jsonEncode($data)).'"
                WHERE id_cart = '.(int) $this->context->cart->id
        );
    }
}

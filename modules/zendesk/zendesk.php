<?php
/**
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
*  @author    Presta-Module
*  @author    202 ecommerce
*  @copyright 2009-2016 Presta-Module
*  @copyright 2017-2018 202 ecommerce
*  @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

// Loading ZendeskApi
require_once(_PS_MODULE_DIR_ . 'zendesk/models/ZendeskApi.php');

class Zendesk extends Module
{
    public $api;
    public $reseller_api;

    private $onboarding = false;
    private $is_bootstrap = true;

    // DEBUG
    private $app_id = 128903;//old : 86180 or 128240 id of prestashop application;

    public function __construct()
    {
        $this->name = 'zendesk';
        $this->tab = 'administration';
        $this->version = '1.1.0';
        $this->author = '202 ecommerce';
        $this->ps_versions_compliancy['min'] = '1.5.0.1';
        $this->module_key = '478622aa5726d385d1de33ae1f543919';

        $this->bootstrap = false;

        parent::__construct();

        $this->displayName = $this->l('Zendesk');
        $this->description = $this->l('Zendesk helps you deliver the best customer support to your customers.');

        $this->api = new ZendeskApi();
        if (isset($this->context->controller) && $this->context->controller instanceof AdminModulesController && Tools::getValue('configure') == $this->name) {
            $this->defineIfIsOnBoarding();

            require_once(_PS_MODULE_DIR_ . 'zendesk/models/ZendeskResellerApi.php');
            $this->reseller_api = new ZendeskResellerApi();
        }
    }

    public function install()
    {
        if (!parent::install()) {
            return false;
        }

        Configuration::updateValue('ZENDESK_ONBOARDING', (int)true);
        Configuration::updateValue('ZENDESK_WIDGET', 1);
        Configuration::updateValue('ZENDESK_APP', 1);
        /* Connector */
        Configuration::updateValue('ZENDESK_CONNECTOR_KEY', Tools::strtoupper(Tools::passwdGen(16)));

        /* Install Tabs */
        $tab = new Tab();
        foreach (Language::getLanguages(true) as $language) {
            $tab->name[(int)$language['id_lang']] = 'Zendesk';
        }
        $tab->class_name = 'AdminZendesk';
        $tab->id_parent = -1; /* Hidden */
        $tab->module = $this->name;
        $tab->add();

        $hooks = array('displayBackOfficeHeader', 'displayHeader', 'actionObjectCustomerMessageAddAfter');
        foreach ($hooks as $hook) {
            if (!$this->registerHook($hook)) {
                return false;
            }
        }

        return true;
    }

    public function uninstall()
    {
        /* Uninstall Tabs */
        $tab = new Tab((int)Tab::getIdFromClassName('AdminZendesk'));
        $tab->delete();

        /* Reset configurations */
        Configuration::updateValue('ZENDESK_SUBDOMAIN', '');
        Configuration::updateValue('ZENDESK_USERNAME', '');
        Configuration::updateValue('ZENDESK_APIKEY', '');
        Configuration::updateValue('ZENDESK_WIDGET', 0);
        Configuration::updateValue('ZENDESK_APP', 0);
        Configuration::updateValue('ZENDESK_ONBOARDING', (int)true);

        /* Uninstall Module */
        if (!parent::uninstall()) {
            return false;
        }

        return true;
    }
    
    /**
     *  Handle ajax requests (like verify subdomain owner, subodmain availability, ...)
     *
     * @since 1.0.0
     */
    public function handleAjaxRequest()
    {
        $json = array();
        $action = Tools::getValue('action', 'undefined');

        switch ($action) {
            case 'verifySubdomainAvailability':
                $subdomain = Tools::getValue('subdomain');
                
                $return = $this->reseller_api->verifySubdomainAvailability($subdomain);
                if (isset($return->success) && $return->success) {
                    $json['success'] = true;
                    $json['msg'] = $this->l('Subdomain available');
                } else {
                    $json['success'] = false;
                    $json['msg'] = $this->l('Subdomain not available');
                }

                break;
            case 'verifySubdomainOwner':
                Configuration::updateValue('ZENDESK_SUBDOMAIN', Tools::getValue('subdomain'));
                Configuration::updateValue('ZENDESK_USERNAME', Tools::getValue('username'));
                Configuration::updateValue('ZENDESK_APIKEY', Tools::getValue('api_key'));

                $json['success'] = $this->api->verifySubdomainOwner();
                if ($json['success']) {
                    $json['msg'] = $this->l('Success');
                } else {
                    if (Tools::getValue('screen_is') == 'onboarding') {
                        Configuration::updateValue('ZENDESK_ONBOARDING', (int)true);
                    }
                    $json['msg'] = $this->l('This account isn\'t yours. Thanks to choose an another one.');
                }
                break;
            case 'createTrialAccount':
                $owner =  array(
                    'name' => Tools::getValue('owner_name'),
                    'email' => Tools::getValue('owner_email')
                );
                $address = array(
                    'phone' => Tools::getValue('address_phone')
                );
                $account = array(
                    'name' => Tools::getValue('company_name'),
                    'subdomain' => Tools::getValue('subdomain'),
                    'help_desk_size' => Tools::getValue('help_desk_size')
                );
                $language = Tools::getValue('language');

                $return = $this->reseller_api->createTrialAccount($owner, $address, $account, $language);
                if (!isset($return->error)) {
                    $json['success'] = true;
                    $json['msg'] = $this->l('Success');
                    Configuration::updateValue('ZENDESK_ONBOARDING', (int)false);
                    Configuration::updateValue('ZENDESK_SUBDOMAIN', Tools::getValue('subdomain'));
                    Configuration::updateValue('ZENDESK_USERNAME', Tools::getValue('owner_email'));
                } else {
                    $json['success'] = false;
                    $json['msg'] = $this->reseller_api->getLastError(true);
                }
                break;
            case 'undefined':
            default:
                $json['success'] = false;
                $json['msg'] = '';
                break;
        }

        die(Tools::jsonEncode($json));
    }

    public function getContent()
    {
        if (Tools::getValue('action', 0)) {
            $this->handleAjaxRequest();
        }

        $this->_html = '';

        if (Tools::isSubmit('submitSubDomainExist')) {
            Configuration::updateValue('ZENDESK_SUBDOMAIN', Tools::getValue('subdomain'));
            Configuration::updateValue('ZENDESK_ONBOARDING', (int)false);
            $this->defineIfIsOnBoarding();
        } elseif (Tools::isSubmit('submitConfig')) {
            $widget = Tools::getValue('embed-toggle', false);
            $app = Tools::getValue('settings-toggle', false);

            Configuration::updateValue('ZENDESK_WIDGET', (int)$widget);
            Configuration::updateValue('ZENDESK_APP', (int)$app);
            Configuration::updateValue('ZENDESK_SUBDOMAIN', Tools::getValue('subdomain'));
            Configuration::updateValue('ZENDESK_USERNAME', Tools::getValue('email'));
            Configuration::updateValue('ZENDESK_APIKEY', Tools::getValue('api_key'));

            if ((int)$app) {
                $this->installApp();
            }

            $account = $this->api->getMe();

            if (is_object($account) && isset($account->error)) {
                $error = $account->error;
                $this->context->controller->errors[] = $this->l('Error occured: '.(isset($error->title)?$error->title:'Unexpected error').'. Error message: '.(isset($error->message)?$error->message:'-'));
            }

            if (!$this->api->isValid()) {
                $this->context->controller->errors[] = $this->l('Account is not valid');
            }
        }

        

        $account = $this->api->getMe();

        if (is_object($account) && isset($account->error)) {
            $error = $account->error;
            $error_msg = isset($account->description)?$account->description:((isset($error->title)?$error->title:'Unexpected error').'. Error message: '.(isset($error->message)?$error->message:'-'));
            $this->context->controller->errors[] = $this->l('Error occured: '.$error_msg);
        }

        $this->_html .= $this->renderForm();

        return $this->_html;
    }

    public function renderForm()
    {
        $content = '';

        if ($this->onboarding) {
            $content = $this->renderOnBoardingForm();
        } else {
            $content = $this->renderConfigForm();
        }

        return $content;
    }

    public function renderOnBoardingForm()
    {
        $shop_name = Configuration::get('PS_SHOP_NAME');

        $this->context->smarty->assign(array(
            'dev_part' => Tools::getValue('part', 'onboarding'),
            'shop_name' => $shop_name,
            'domain_suggestion' => Tools::link_rewrite($shop_name),
            'owner_email' => Configuration::get('PS_SHOP_EMAIL'),
            'shop_phone' => Configuration::get('PS_SHOP_PHONE'),
            'free_trial_url' => 'https://www.zendesk.com/register#getstarted',
        ));
        
        return $this->display(__FILE__, 'views/templates/admin/onboarding.tpl');
    }

    public function renderConfigForm()
    {
        $this->context->smarty->assign(array(
            'zendesk_subdomain' => Configuration::get('ZENDESK_SUBDOMAIN'),
            'zendesk_api_key' => Configuration::get('ZENDESK_APIKEY'),
            'zendesk_email' => Configuration::get('ZENDESK_USERNAME'),
            'zendesk_widget' => (int)Configuration::get('ZENDESK_WIDGET'),
            'zendesk_app' => (int)Configuration::get('ZENDESK_APP'),
        ));

        return $this->display(__FILE__, 'views/templates/admin/settings.tpl');
    }
    
    /**
     *  Install the Zendesk App into the Zendesk Manager
     *
     * @since 1.0.0
     */
    private function installApp()
    {
        $need = array(
            'to_install' => true,
            'to_update' => false
        );

        $apps = $this->api->listAppInstallations();
        if (is_object($apps) && is_array($apps->installations)) {
            foreach ($apps->installations as $installation) {
                if ((int)$installation->app_id == (int)$this->app_id) {
                    $settings = array();

                    if (empty($installation->settings->url) || $installation->settings->url != Context::getContext()->shop->getBaseURL()) {
                        $need['to_update'] = true;
                        $settings['url'] = Context::getContext()->shop->getBaseURL();
                    }
                    if (empty($installation->settings->access_token) || $installation->settings->access_token != Configuration::get('ZENDESK_CONNECTOR_KEY')) {
                        $need['to_update'] = true;
                        $settings['access_token'] = Configuration::get('ZENDESK_CONNECTOR_KEY');
                    }
                    if (empty($installation->settings->order_id_field_id)) {
                        $need['to_update'] = true;
                        $ticket_field = array(
                            'type' => 'text',
                            'title' => $this->l('Order reference'),
                            'removable' => false,
                        );

                        $ret = $this->api->createTicketField($ticket_field);
                        if (isset($ret->ticket_field->id)) {
                            $settings['order_id_field_id'] = $ret->ticket_field->id;
                        }
                    } else {
                        Configuration::updateValue('ZENDESK_ORDER_ID_FIELD_ID', $installation->settings->order_id_field_id);
                    }

                    $need['to_install'] = false;

                    // Update settings
                    if ($need['to_update']) {
                        // Don't cast $installation->id as an Int, it may overflow on 32 bits systems and thus fails with Validate::isUnsignedInt... Keep it as a string
                        $ret = $this->api->updateApp($installation->id, $settings);
                        if (!empty($ret) && !isset($ret->error)) {
                            // Update our own if success and if needed
                            if (isset($settings['order_id_field_id'])) {
                                Configuration::updateValue('ZENDESK_ORDER_ID_FIELD_ID', $settings['order_id_field_id']);
                            }
                            $this->context->controller->confirmations[] = $this->l('App update successful');
                        } else {
                            $this->context->controller->errors[] = $this->l('Error during update app');
                        }
                    }

                    continue;
                }
            }
        }

        if ($need['to_install']) {
            // Process install
            $ticket_field = array(
                'type' => 'text',
                'title' => $this->l('Order reference'),
                'removable' => false,
            );

            $ret = $this->api->createTicketField($ticket_field);
            if (isset($ret->ticket_field->id)) {
                $install = $this->api->installApp((int)$this->app_id, $ret->ticket_field->id);
                if (isset($install->id)) {
                    Configuration::updateValue('ZENDESK_ORDER_ID_FIELD_ID', $ret->ticket_field->id);
                    $this->context->controller->confirmations[] = $this->l('App installed successful');
                } else {
                    $this->context->controller->errors[]  = $this->l('Error during install app'.' : '.($install->description ? $install->description : $install->error));
                }
            } else {
                $this->context->controller->errors[]  = $this->l('Can\'t process the creation of the user field.'.' '.(isset($ret->description) ? $ret->description : $ret->error));
            }
        }

        return true;
    }

    /**
     *  Check if settings are done or if we are on onBoarding
     *
     * @since 1.0.0
     */
    private function defineIfIsOnBoarding()
    {
        $conf = Configuration::getMultiple(array('ZENDESK_ONBOARDING', 'ZENDESK_SUBDOMAIN'));
        if ((bool)$conf['ZENDESK_ONBOARDING'] || ($conf['ZENDESK_SUBDOMAIN'] == '')) {
            $this->onboarding = true;
        } else {
            $this->onboarding = false;
        }
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (isset($this->context->controller) && $this->context->controller instanceof AdminModulesController && Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJQuery();
            //$this->context->controller->addJS($this->_path.'views/js/script.js');
        } elseif (isset($this->context->controller) && $this->context->controller instanceof AdminZendeskController) {
            $this->context->controller->addJQuery();
            $this->context->controller->addJqueryPlugin(array('fancybox'));
            $this->context->controller->addJS($this->_path.'views/js/admin.js');

            if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
                $this->context->controller->addCSS($this->_path.'views/css/bootstrap.css');
            }
            $this->context->controller->addCSS($this->_path.'views/css/admin.css');
        } elseif (isset($this->context->controller) && $this->context->controller instanceof AdminCustomerThreadsController && !Tools::getvalue('forcelegacycontact') && !Tools::getValue('id_customer_thread')) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminZendesk'));
        }
    }

    public function hookDisplayHeader()
    {
        if ($this->api->isValid(true)) {
            if (!$this->context->controller instanceof ContactController && Configuration::get('ZENDESK_WIDGET')) {
                $js_defs = array(
                    'zendesk_subdomain' => Configuration::get('ZENDESK_SUBDOMAIN'),
                    'zendesk_iso' => $this->context->language->iso_code
                );

                $this->smarty->assign($js_defs);
                return $this->display(__FILE__, 'views/templates/hook/header.tpl');
            }
        }
    }

    public function hookActionObjectCustomerMessageAddAfter($params)
    {
        $type_array = array(1 => 'problem', 2 => 'question');

        $customer_message = $params['object'];
        $customer_thread = new CustomerThread((int)$customer_message->id_customer_thread);
        if ($customer_thread->id_customer) {
            $customer = new Customer((int)$customer_thread->id_customer);
            $requester_name = $customer->firstname.' '.$customer->lastname;
        } else {
            $requester_name = $customer_thread->email;
        }
        $contact = new Contact((int)$customer_thread->id_contact);
       
        if ($contact->customer_service) {
            $data = array();
            $data['type'] =  $type_array[2];
            $data['status'] = 'new';
            //$data['tags'] = array('contact');
            $data['subject'] = $contact->name[(int)$customer_thread->id_lang];
            $data['comment']['body'] = $customer_message->message;
            $data['requester'] = array(
                'name' => $requester_name,
                'email' => $customer_thread->email
            );
            $data['via'] = array(
                'channel' => 'web',
            );
            $data['external_id'] = (int)$customer_message->id;
            if ($customer_message->file_name != '') {
                $ret = $this->api->uploadFile($customer_message->file_name, _PS_UPLOAD_DIR_.$customer_message->file_name);
                if (!isset($ret->error)) {
                    $data['comment']['uploads'] = $ret->upload->token;
                }
            }

            // If we have an order reference, set it in the ticket
            if (isset($customer_thread->id_order) && Configuration::get('ZENDESK_ORDER_ID_FIELD_ID')) {
                $order = new Order($customer_thread->id_order);
                if (Validate::isLoadedObject($order)) {
                    $data['custom_fields'] = array(
                        array(
                            'id' => Configuration::get('ZENDESK_ORDER_ID_FIELD_ID'),
                            'value' => $order->reference
                        ),
                    );
                }
            }

            $this->api->addTicket($data);
        }
    }
}

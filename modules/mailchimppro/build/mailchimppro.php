<?php
/**
 * MailChimp
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    Mailchimp
 * @copyright Mailchimp
 * @license   commercial
 */

/**
 * Class Mailchimppro
 */
class Mailchimppro extends Module
{
    /**
     * @var \DrewM\MailChimp\MailChimp MailChimp API client object
     *
     * @see https://github.com/drewm/mailchimp-api
     */
    protected $apiClient;

    public $menus = array(
        array(
            'is_root' => true,
            'name' => 'Mailchimp Config',
            'class_name' => 'mailchimppro',
            'visible' => true,
            'parent_class_name' => 0,
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Config',
            'class_name' => 'AdminMailchimpProConfig',
            'visible' => true,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Setup Wizard',
            'class_name' => 'AdminMailchimpProWizard',
            'visible' => true,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp List',
            'class_name' => 'AdminMailchimpProLists',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Batches',
            'class_name' => 'AdminMailchimpProBatches',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Carts',
            'class_name' => 'AdminMailchimpProCarts',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Customers',
            'class_name' => 'AdminMailchimpProCustomers',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Orders',
            'class_name' => 'AdminMailchimpProOrders',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Products',
            'class_name' => 'AdminMailchimpProProducts',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Stores',
            'class_name' => 'AdminMailchimpProStores',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Sync',
            'class_name' => 'AdminMailchimpProSync',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Sites',
            'class_name' => 'AdminMailchimpProSites',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Mailchimp Automations',
            'class_name' => 'AdminMailchimpProAutomations',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'List members',
            'class_name' => 'AdminMailchimpProListMembers',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Promo rules',
            'class_name' => 'AdminMailchimpProPromoRules',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
        array(
            'is_root' => false,
            'name' => 'Promo codes',
            'class_name' => 'AdminMailchimpProPromoCodes',
            'visible' => false,
            'parent_class_name' => 'mailchimppro',
        ),
    );


    public function __construct()
    {
        $this->name = 'mailchimppro';
        $this->tab = 'administration';
        $this->version = '2.0.1';
        $this->author = 'Mailchimp';
        $this->need_instance = 1;
        $this->bootstrap = true;
        $this->module_key = '793ebc5f330220c7fb7b817fe0d63a92';

        parent::__construct();

        $this->displayName = $this->l('Mailchimp');
        $this->description = $this->l('Official Mailchimp integration for PrestaShop');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        require_once $this->getLocalPath() . 'vendor/autoload.php';
    }


    /**
     * Install the required tabs, configs and stuff
     *
     * @since 0.0.1
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     *
     * @return bool
     */
    public function install()
    {
        $tabRepository = new \PrestaChamps\PrestaShop\Tab\TabRepository($this->menus, 'mailchimppro');
        $tabRepository->install();

        return parent::install() &&
            // The moduleRoutes hook is necessary in order to load the autoloader
            $this->registerHook('moduleRoutes') &&
            $this->registerHook('displayFooter') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('actionObjectUpdateAfter') &&
            $this->registerHook('actionObjectDeleteAfter') &&
            $this->registerHook('actionOrderStatusUpdate') &&
            $this->registerHook('actionCartSave') &&
            $this->registerHook('actionObjectCustomerAddAfter') &&
            $this->registerHook('actionObjectCartRuleAddAfter') &&
            $this->registerHook('actionObjectCartRuleDeleteBefore') &&
            $this->registerHook('displayAdminOrderContentOrder') &&
            $this->registerHook('displayAdminOrderTabOrder') &&
            $this->registerHook('displayBackOfficeTop') &&
            $this->registerHook('actionFrontControllerSetMedia') &&
            $this->registerHook('actionObjectCartRuleUpdateAfter');
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function uninstall()
    {
        $tabRepository = new \PrestaChamps\PrestaShop\Tab\TabRepository($this->menus, 'mailchimppro');
        $tabRepository->uninstall();

        return parent::uninstall();
    }


    /**
     * Check if the current PrestaShop installation is version 1.7 or below
     *
     * @return bool
     */
    public static function isPs17()
    {
        return (bool)version_compare(_PS_VERSION_, '1.7', '>=');
    }


    /**
     * Redirect to the custom config controller
     *
     * @throws PrestaShopException
     */
    public function getContent()
    {
        Tools::redirectAdmin($this->context->link->getAdminLink('AdminMailchimpProConfig'));
    }

    /**
     * Place UTM tracking cookie when the user arrived via MailChimp
     *
     * @param $params
     */
    public function hookDisplayHeader($params)
    {
        if (Tools::getValue('utm_source') === 'mailchimp' || !empty(Tools::getValue('mc_cid'))) {
            $this->context->cookie->landing_site = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $mc_cid = Tools::getValue('mc_cid', false);
            $utm_source = Tools::getValue('utm_source', false);
            if ($mc_cid) {
                setcookie('mc_cid', Tools::getValue('mc_cid'));
            }
            if ($utm_source) {
                setcookie('utm_source', urldecode(Tools::getValue('utm_source')));
            }
            $this->context->cookie->utm_source = Tools::getValue('utm_source');
            setcookie(
                'landing_site',
                (Tools::usingSecureMode() ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"
            );
        }
    }

    /**
     * Mailchimp API client factory
     *
     * @throws Exception
     */
    public function getApiClient()
    {
        if ($this->apiClient) {
            return $this->apiClient;
        }
        $this->apiClient = new DrewM\MailChimp\MailChimp(Configuration::get(MailchimpProConfig::MAILCHIMP_API_KEY));

        return $this->apiClient;
    }

    /**
     * @param       $url
     * @param       $method
     * @param array $data
     *
     * @return mixed
     * @throws Exception
     */
    public function sendApiRequest($url, $method, $data = array())
    {
        if ($method === 'POST') {
            $this->getApiClient()->post($url, $data);
        } elseif ($method === 'PATCH') {
            $this->getApiClient()->patch($url, $data);
        } elseif ($method === 'PUT') {
            $this->getApiClient()->put($url, $data);
        } elseif ($method === 'DELETE') {
            $this->getApiClient()->delete($url, $data);
        } else {
            $this->getApiClient()->get($url, $data);
        }

        return $this->getApiClient()->getLastResponse();
    }

    /**
     * Display site MailChimp site verification
     *
     * @param $params
     *
     * @return string
     */
    public function hookDisplayFooter($params)
    {
        try {
            $result = $this->sendApiRequest("ecommerce/stores/{$this->context->shop->id}", 'GET');
            if ($this->getApiClient()->success()) {
                $result = json_decode($result['body'], true);

                if (isset($result['connected_site'])) {
                    $footer = $result['connected_site']['site_script']['fragment'];
                    if (!Configuration::get(MailchimpProConfig::MAILCHIMP_SCRIPT_VERIFIED)) {
                        $site_id = $result['connected_site']['site_foreign_id'];
                        (new \PrestaChamps\MailchimpPro\Commands\SiteVerifyCommand($this->apiClient, $site_id))
                            ->execute();
                        $this->sendApiRequest(
                            "ecommerce/stores/{$this->context->shop->id}",
                            'POST',
                            array('is_syncing' => false)
                        );
                        Configuration::updateValue(MailchimpProConfig::MAILCHIMP_SCRIPT_VERIFIED, true);
                    }

                    return $footer;
                }
            }
            PrestaShopLogger::addLog("[MAILCHIMP] :{$this->getApiClient()->getLastError()}");
        } catch (Exception $e) {
            PrestaShopLogger::addLog("[MAILCHIMP] :{$e->getMessage()}");
        }
        return '';
    }

    /**
     * @param $params
     *
     * @return string
     * @throws Exception
     */
    public function hookActionFrontControllerSetMedia($params)
    {
        $result = $this->getApiClient()->get("ecommerce/stores/{$this->context->shop->id}");
        try {
            if (!Configuration::get(MailchimpProConfig::MAILCHIMP_SCRIPT_VERIFIED)) {
                $this->sendApiRequest(
                    "connected-sites/{$result['connected_site']['site_foreign_id']}/actions/verify-script-installation",
                    'POST'
                );
                Configuration::updateValue(MailchimpProConfig::MAILCHIMP_SCRIPT_VERIFIED, true);
            }
            $this->context->controller->addJS($result['connected_site']['site_script']['url'], false);
        } catch (Exception $exception) {
            PrestaShopLogger::addLog("[MAILCHIMP] :{$exception->getMessage()}");
        }

        return '';
    }

    /**
     * Sync the newly created customer to MailChimp
     *
     * @param $params
     */
    public function hookActionObjectCustomerAddAfter($params)
    {
        try {
            /**
             * @var $customer Customer
             */
            $customer = $params['object'];
            $command = new \PrestaChamps\MailchimpPro\Commands\CustomerSyncCommand(
                $this->context,
                $this->getApiClient(),
                array($customer->id)
            );
            $command->triggerDoubleOptIn(true);
            $command->execute();
        } catch (Exception $exception) {
            $this->context->controller->errors[] = "[MAILCHIMP] :{$exception->getMessage()}";
            PrestaShopLogger::addLog("[MAILCHIMP] :{$exception->getMessage()}");
        }
    }

    /**
     * @todo Refactor code to use a service pattern
     *
     * @param $params
     *
     * @throws Exception
     */
    public function hookActionObjectCartRuleAddAfter($params)
    {
        $object = new CartRule($params['object']->id, $this->context->language->id);
        $command = new \PrestaChamps\MailchimpPro\Commands\CartRuleSyncCommand(
            $this->context,
            $this->getApiClient(),
            array($object)
        );
        $command->setMethod($command::SYNC_METHOD_POST);
        $command->setSyncMode($command::SYNC_MODE_REGULAR);
        $command->execute();
    }

    /**
     * @todo Refactor code to use a service pattern
     *
     * @param $params
     *
     * @throws Exception
     */
    public function hookActionObjectCartRuleUpdateAfter($params)
    {
        $object = new CartRule($params['object']->id, $this->context->language->id);
        $command = new \PrestaChamps\MailchimpPro\Commands\CartRuleSyncCommand(
            $this->context,
            $this->getApiClient(),
            array($object)
        );
        $command->setMethod($command::SYNC_METHOD_PATCH);
        $command->setSyncMode($command::SYNC_MODE_REGULAR);
        $command->execute();
    }

    /**
     * @todo Refactor code to use a service pattern
     *
     * @param $params
     *
     * @throws Exception
     */
    public function hookActionObjectCartRuleDeleteBefore($params)
    {
        $object = new CartRule($params['object']->id, $this->context->language->id);
        $command = new \PrestaChamps\MailchimpPro\Commands\CartRuleSyncCommand(
            $this->context,
            $this->getApiClient(),
            array($object)
        );
        $command->setMethod($command::SYNC_METHOD_DELETE);
        $command->setSyncMode($command::SYNC_MODE_REGULAR);
        $command->execute();
    }


    /**
     * Create or update the cart in Mailchimp
     *
     * @todo Use command pattern instead
     *
     * @param $params
     *
     * @throws Exception
     */
    public function hookActionCartSave($params)
    {
        if (Tools::getValue('controller') === 'adminaddresses') {
            return;
        }
        $cartId = isset(Context::getContext()->cart->id) ? Context::getContext()->cart->id : false;
        if ($cartId && !$this->context->customer->isGuest()) {
            try {
                /**
                 * @var $cart Cart
                 */
                $cart = new Cart($cartId, $this->context->language->id);
                $data = (new \PrestaChamps\MailchimpPro\Formatters\CartFormatter(
                    $cart,
                    $this->context->customer,
                    $this->context
                ))->format();
                $result = $this->sendApiRequest(
                    "/ecommerce/stores/{$this->context->shop->id}/carts",
                    'POST',
                    $data
                );
                if ($result['headers']['http_code'] === 400) {
                    $this->sendApiRequest(
                        "/ecommerce/stores/{$this->context->shop->id}/carts/{$cart->id}",
                        'PATCH',
                        $data
                    );
                }
            } catch (Exception $exception) {
                PrestaShopLogger::addLog("[MAILCHIMP] :{$exception->getMessage()}");
            }
        }
    }

    /**
     * Sync the order status update to MailChimp
     *
     * @param $params
     */
    public function hookActionOrderStatusUpdate($params)
    {
        try {
            $order = new Order($params['id_order'], $this->context->language->id);
            $shippingAddress = new \Address($order->id_address_delivery, $this->context->language->id);
            $billingAddress = new \Address($order->id_address_invoice, $this->context->language->id);
            $data = (new \PrestaChamps\MailchimpPro\Formatters\OrderFormatter(
                $order,
                $order->getCustomer(),
                $billingAddress,
                $shippingAddress,
                $this->context
            ))->format();
            $result = $this->sendApiRequest(
                "ecommerce/stores/{$this->context->shop->id}/orders",
                'POST',
                $data
            );
            if ($result['headers']['http_code'] === 400) {
                $this->sendApiRequest(
                    "ecommerce/stores/{$this->context->shop->id}/orders/{$order->id}",
                    'PATCH',
                    $data
                );
            }
        } catch (Exception $exception) {
            $this->context->controller->errors[] = "[MAILCHIMP] :{$exception->getMessage()}";
            PrestaShopLogger::addLog("[MAILCHIMP] :{$exception->getMessage()}");
        }
    }

    public function hookActionValidateOrder($params)
    {
        if (isset($params['order']) && is_subclass_of($params['order'], 'OrderCore')) {
            try {
                $order = new Order($params['order']->id, $this->context->language->id);
                $orderSyncCommand = new \PrestaChamps\MailchimpPro\Commands\OrderSyncCommand(
                    $this->context,
                    $this->getApiClient(),
                    array($params['order']->id)
                );
                $orderSyncCommand->execute();
                $this->sendApiRequest(
                    "ecommerce/stores/{$this->context->shop->id}/carts/$order->id_cart",
                    'DELETE'
                );
            } catch (Exception $exception) {
                $this->context->controller->errors[] = "[MAILCHIMP] :{$exception->getMessage()}";
                PrestaShopLogger::addLog("[MAILCHIMP] :{$exception->getMessage()}");
            }
        }
    }

    /**
     * Delete the objects from the MailChimp account also
     *
     * @param $params
     */
    public function hookActionProductUpdate($params)
    {
        try {
            if (isset($params['product'])) {
                $product = $params['product'];
                if (is_a($product, 'ProductCore')) {
                    /**
                     * @var $product Product
                     */
                    $service = new \PrestaChamps\MailchimpPro\Commands\ProductSyncCommand(
                        $this->context,
                        new \DrewM\MailChimp\MailChimp(\Configuration::get(\MailchimpProConfig::MAILCHIMP_API_KEY)),
                        array($product->id)
                    );

                    if ($product->isNew()) {
                        $service->setMethod($service::SYNC_METHOD_POST);
                    } else {
                        $service->setMethod($service::SYNC_METHOD_PATCH);
                    }

                    $service->execute();
                }
            }
        } catch (Exception $exception) {
            $this->context->controller->errors[] = $exception->getMessage();
            PrestaShopLogger::addLog(
                "MAILCHIMP_ERROR: {$exception->getMessage()}",
                1,
                $exception->getCode(),
                PrestaChamps\MailchimpPro\Commands\ProductSyncCommand::class,
                null,
                true
            );
        }
    }

    /**
     * Delete the objects from the MailChimp account also
     *
     * @param $object
     */
    public function hookActionObjectDeleteAfter($object)
    {
        if (is_subclass_of($object['object'], 'ProductCore')) {
            $objectId = $object['object']->id;
            try {
                $this->getApiClient()->delete("ecommerce/stores/{$this->context->shop->id}/products/$objectId");
            } catch (Exception $e) {
                $this->context->controller->errors[] = "[MAILCHIMP] :{$e->getMessage()}";
                PrestaShopLogger::addLog("[MAILCHIMP] :{$e->getMessage()}");
            }
        }
    }

    /**
     * Sync the object updates to Mailchimp
     *
     * @param $object
     */
    public function hookActionObjectUpdateAfter($object)
    {
        if (is_subclass_of($object['object'], 'CustomerCore')) {
            try {
                $url = "ecommerce/stores/{$this->context->shop->id}/customers/{$object['object']->id}";
                $data = (new \PrestaChamps\MailchimpPro\Formatters\CustomerFormatter($object['object'], $this->context))
                    ->format();
                $this->sendApiRequest($url, 'PATCH', $data);
            } catch (Exception $exception) {
                $this->context->controller->errors[] = $exception->getMessage();
                PrestaShopLogger::addLog(
                    "[MAILCHIMP]: {$exception->getMessage()}",
                    1,
                    $exception->getCode(),
                    PrestaChamps\MailchimpPro\Commands\CustomerSyncCommand::class,
                    null,
                    true
                );
            }
        }

        if (is_subclass_of($object['object'], 'ShopCore')) {
            try {
                $service = new \PrestaChamps\MailchimpPro\Commands\StoreSyncCommand(
                    $this->context,
                    $this->getApiClient(),
                    array($object['object']->id)
                );
                $service->setSyncMode($service::SYNC_MODE_REGULAR);
                $service->setMethod($service::SYNC_METHOD_PATCH);
                $service->execute();
            } catch (Exception $exception) {
                $this->context->controller->errors[] = $exception->getMessage();
                PrestaShopLogger::addLog(
                    "[MAILCHIMP]: {$exception->getMessage()}",
                    1,
                    $exception->getCode(),
                    \PrestaChamps\MailchimpPro\Commands\StoreSyncCommand::class,
                    null,
                    true
                );
            }
        }
    }

    /**
     * @param $params
     *
     * @return string
     */
    public function hookDisplayAdminOrderContentOrder($params)
    {
        try {
            /**
             * @var $order Order
             */
            $order = $params['order'];
            $response = $this->getApiClient()->get("ecommerce/stores/{$order->id_shop}/orders/{$order->id}");
            if ($this->getApiClient()->success()) {
                $this->context->smarty->assign(array(
                    'order' => $response,
                ));
                return $this->context->smarty->fetch(
                    $this->getLocalPath() . 'views/templates/admin/mc-order-detail-tab-content.tpl'
                );
            }

            return $this->context->smarty->fetch(
                $this->getLocalPath() . 'views/templates/admin/mc-order-detail-tab-content-empty.tpl'
            );
        } catch (Exception $exception) {
            $this->context->controller->errors[] =
                $this->l("Unable to fetch MailChimp order: {$exception->getMessage()}");
        }

        return '';
    }

    /**
     * @param $params
     *
     * @return string
     * @throws SmartyException
     */
    public function hookDisplayAdminOrderTabOrder($params)
    {
        return $this->context->smarty->fetch(
            $this->getLocalPath() . '/views/templates/admin/mc-order-detail-tab-title.tpl'
        );
    }

    /**
     * @throws SmartyException
     */
    public function hookDisplayBackOfficeTop()
    {
        if ($this->context->controller->controller_name === 'AdminCarts' && isset($_REQUEST['viewcart'])) {
            $cart = new Cart(Tools::getValue('id_cart'));
            $response = $this->getApiClient()->get("ecommerce/stores/{$cart->id_shop}/carts/{$cart->id}");
            if ($this->getApiClient()->success()) {
                $this->context->smarty->assign(array(
                    'cart' => $response,
                ));
                $this->context->controller->content .=
                    $this->context->smarty->fetch(
                        $this->getLocalPath() . 'views/templates/admin/mc-cart-detail.tpl'
                    );
            }
        }
    }
}

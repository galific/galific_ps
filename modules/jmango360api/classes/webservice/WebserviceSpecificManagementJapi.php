<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

require_once _PS_MODULE_DIR_ . '/jmango360api/vendor/autoload.php'; // Autoload files using Composer autoload

require_once _PS_MODULE_DIR_ . '/jmango360api/jmango360api.php';
require_once _PS_MODULE_DIR_ . '/jmango360api/classes/config/ServiceProvider.php';

/**
 * Class WebserviceSpecificManagementJapi
 */

class WebserviceSpecificManagementJapi implements WebserviceSpecificManagementInterface
{
    /** @var WebserviceOutputBuilderCore */
    protected $objOutput;
    /** @var WebserviceRequestCore */
    protected $wsObject;

    protected $output;

    /** @var PaymentFinder */
    protected $paymentFinder;

    protected $apis = array(
        'plugin_version' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        ),
        'prestashop_version' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        ),
        'cookie_key' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        ),
        'payments' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        ),
        'carriers' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        ),
        'set_carrier' => array(
            'get' => 'false', 'put' => 'false', 'post' => 'true', 'delete' => 'false', 'head' => 'false'
        ),
        'cart' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        ),
        'url_rewrite' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        )
    );

    protected $rest_apis = array(
        'products' => array(
            'get' => 'true', 'put' => 'false', 'post' => 'false', 'delete' => 'false', 'head' => 'true'
        )
    );

    public function __construct()
    {
        $this->paymentFinder = new PaymentFinder();
    }

    /**
     * @param WebserviceOutputBuilderCore $obj
     * @return WebserviceSpecificManagementInterface
     */
    public function setObjectOutput(WebserviceOutputBuilderCore $obj)
    {
        $this->objOutput = $obj;
        return $this;
    }

    public function getObjectOutput()
    {
        return $this->objOutput;
    }

    public function setWsObject(WebserviceRequestCore $obj)
    {
        $this->wsObject = $obj;
        return $this;
    }

    public function getWsObject()
    {
        return $this->wsObject;
    }

    /**
     * Routers management
     */
    public function manage()
    {
        if (Tools::getIsset('XDEBUG_SESSION_START')) {
            @ini_set('display_errors', 'on');
            @error_reporting(E_ALL | E_STRICT);
        }

        if (isset($this->wsObject->urlSegment)) {
            for ($i = 1; $i < 6; $i++) {
                if (count($this->wsObject->urlSegment) == $i) {
                    $this->wsObject->urlSegment[$i] = '';
                }
            }
        }

        // PS-667: Some variables of smarty are used by extension, however, they are not declared in our webservice
        if (Context::getContext()->smarty) {
            Context::getContext()->smarty->assign(array(
                'show_taxes' => (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX')),
                'tpl_dir' => _PS_THEME_DIR_,
                'tpl_uri' => _THEME_DIR_
            ));
        }

        switch ($this->wsObject->urlSegment[1]) {
            case 'prestashop_version':
                $this->getPrestashopVersion();
                break;
            case 'plugin_version':
                $this->getPluginVersion();
                break;
            case 'url_rewrite':
                $this->getUrlRewrite();
                break;
            case 'cookie_key':
                $this->getCookieKey();
                break;
            case 'cart':
                $this->getCart();
                break;
            case 'carriers':
                $this->getCarriers();
                break;
            case 'set_carrier':
                if (in_array($this->wsObject->method, array('POST'))) {
                    $this->setCarrier();
                } else {
                    throw new WebserviceException('This method is not allowed.', 405);
                }
                break;
            case 'payments':
                $this->getPayments();
                break;

            case 'rest':
                $serviceClass = ServiceProvider::provide($this->wsObject);
                if (isset($serviceClass)) {
                    $serviceClass->execute($this->wsObject, $this->objOutput, $this->output);
                } else {
                    $exception = new WebserviceException(sprintf('JMango360 API does not exist'), 400);
                    throw $exception->setDidYouMean($this->wsObject->urlSegment[1], array_keys($this->rest_apis));
                }
                break;

            case '':
                foreach ($this->apis as $api => $attr) {
                    $more_attr = array_merge($attr, array(
                        'xlink_resource' => $this->wsObject->wsUrl . $this->wsObject->urlSegment[0] . '/' . $api,
                    ));
                    $this->output .= $this->objOutput->getObjectRender()->renderNodeHeader(
                        $api,
                        array(),
                        $more_attr,
                        false
                    );
                }
                break;
            default:
                $exception = new WebserviceException(sprintf(
                    'JMango360 API "%s" does not exist',
                    $this->wsObject->urlSegment[1]
                ), 400);
                throw $exception->setDidYouMean($this->wsObject->urlSegment[1], array_keys($this->apis));
        }
    }

    /**
     * Get plugin version
     */
    protected function getPluginVersion()
    {
        $isPwa  = Tools::getValue('pwa') === 'true' ? 1 : 0;
        $output = array();
        $output['empty'] = new JPluginVersion();
        $module = new jmango360api();
        $version = new JPluginVersion();
        $version->id = 1;

        // prestashop 17
        if ($isPwa) {
            if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                $moduleManagerBuilder = PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder::getInstance();
                $moduleManager = $moduleManagerBuilder->build();
                if ($moduleManager->isInstalled('jmango360pwa') && Module::getInstanceByName('jmango360pwa')->active) {
                    $module = Module::getInstanceByName('jmango360pwa');
                    $version->value = $module->version;
                } else {
                    $version->error = "PWA plugin is not installed or enabled";
                }
            } else {
                //prestashop 16
                if (Module::isInstalled('jmango360pwa') && Module::getInstanceByName('jmango360pwa')->active) {
                    $module = Module::getInstanceByName('jmango360pwa');
                    $version->value = $module->version;
                } else {
                    $version->error = "PWA plugin is not installed or enabled";
                }
            }
        } else {
            $version->value = $module->version;
        }

        $output[] = $version;

        $this->output = $this->objOutput->getContent(
            $output,
            '',
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_DETAILS,
            false
        );
    }

    /**
     * Get plugin version
     */
    protected function getPrestashopVersion()
    {
        $output = array();
        $output['empty'] = new JPrestashopVersion();

        $version = new JPrestashopVersion();
        $version->id = 1;
        $version->value = defined('_PS_VERSION_') ? _PS_VERSION_ : null;
        $output[] = $version;

        $this->output = $this->objOutput->getContent(
            $output,
            '',
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_DETAILS,
            false
        );
    }

    /**
     * Get url rewrite by shop ID & lang ID
     */
    protected function getUrlRewrite()
    {
        $shopId = isset($this->wsObject->urlFragments['id_shop']) ? $this->wsObject->urlFragments['id_shop'] : null;
        $langId = isset($this->wsObject->urlFragments['id_lang']) ? $this->wsObject->urlFragments['id_lang'] : null;

        if (!$langId) {
            throw new WebserviceException('You have to set the \'id_lang\' parameter to get a result', 400);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
            SELECT *
            FROM `' . _DB_PREFIX_ . 'meta` m
            LEFT JOIN `' . _DB_PREFIX_ . 'meta_lang` ml ON m.`id_meta` = ml.`id_meta`
            WHERE ml.`id_lang` = ' . (int)$langId . Shop::addSqlRestrictionOnLang('ml', $shopId) . '
            ORDER BY page ASC');

        $output = array();
        $output['empty'] = new JUrlRewrite();
        if ($result) {
            foreach ($result as $row) {
                $urlItem = new JUrlRewrite();
                $urlItem->id = @$row['id_meta'];
                $urlItem->id_lang = @$row['id_lang'];
                $urlItem->id_shop = @$row['id_shop'];
                $urlItem->title = @$row['title'];
                $urlItem->description = @$row['description'];
                $urlItem->url = @$row['page'];
                $urlItem->url_rewrite = @$row['url_rewrite'];

                $output[] = $urlItem;
            }
        }

        $this->output = $this->objOutput->getContent(
            $output,
            null,
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_LIST,
            false
        );
    }

    /**
     * Get value of _COOKIE_KEY_
     */
    protected function getCookieKey()
    {
        if (defined('_COOKIE_KEY_')) {
            $output = array();
            $output['empty'] = new JCookieKey();
            $cookieKey = new JCookieKey();
            $cookieKey->id = 1;
            $cookieKey->value = _COOKIE_KEY_;
            $output[] = $cookieKey;

            $this->output = $this->objOutput->getContent(
                $output,
                '',
                'full',
                $this->wsObject->depth,
                WebserviceOutputBuilder::VIEW_DETAILS,
                false
            );
        }
    }

    /**
     * @return Cart
     * @throws WebserviceException
     */
    protected function _getCart()
    {
        $cartId = $this->wsObject->urlFragments['cart_id'];
        $cart = new Cart($cartId);
        if (!$cart || !$cart->id) {
            throw new WebserviceException('You have to set the \'cart_id\' parameter to get a result', 400);
        }

        return $cart;
    }

    /**
     * @param Cart $cart
     * @return Context
     */
    protected function _getContextFromCart($cart)
    {
        $context = Context::getContext();
        $context->cart = $cart;
        $context->language = new Language($cart->id_lang);
        $context->currency = new Currency($cart->id_currency, $cart->id_lang, $cart->id_shop);

        /**
         * Init country from billing address
         */
        $address = new Address($cart->id_address_invoice, $cart->id_lang);
        if ($address->id_country) {
            $context->country = new Country($address->id_country, $cart->id_lang);
        }

        return $context;
    }

    /**
     * Get cart information
     */
    protected function getCart()
    {
        $cart = $this->_getCart();
        $this->_getContextFromCart($cart);

        $totals = $this->_getTotals($cart);
        $totals['empty'] = new JTotal();

        $this->output = $this->objOutput->getContent(
            $totals,
            null,
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_LIST,
            false
        );
    }

    protected function _getTotals($cart)
    {
        $totals = array();
        $id = 0;

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $cartPresenter = new \PrestaShop\PrestaShop\Adapter\Cart\CartPresenter();
            $raw = $cartPresenter->present($cart);
            $taxTotal = null;

            if (isset($raw['subtotals'])) {
                foreach ($raw['subtotals'] as $subtotal) {
                    if (!$subtotal) {
                        continue;
                    }

                    $total = new JTotal();
                    $total->id = ++$id;

                    if ($total->type = 'products') {
                        $total->type = 'subtotal';
                    } else {
                        $total->type = $subtotal['type'];
                    }

                    $total->label = $subtotal['label'];
                    $total->amount = $subtotal['amount'] == 0 ? '0' : $subtotal['amount'];
                    $total->value = $subtotal['value'];

                    if ($subtotal['type'] != 'tax') {
                        $totals[] = $total;
                    } else {
                        $taxTotal = $total;
                    }
                }
            }

            if (isset($raw['totals'])) {
                foreach ($raw['totals'] as $item) {
                    $total = new JTotal();
                    $total->id = ++$id;
                    $total->type = $item['type'];
                    $total->label = $item['label'];
                    if ($item['type'] == 'total' && !empty($raw['labels']['tax_short'])) {
                        $total->label .= $raw['labels']['tax_short'];
                    }
                    $total->amount = $item['amount'] == 0 ? '0' : $item['amount'];
                    $total->value = $item['value'];

                    $totals[] = $total;
                }
            }

            if ($taxTotal) {
                $totals[] = $taxTotal;
            }
        } else {
            $cartSummary = $cart->getSummaryDetails();
            $useTaxes = (int)Configuration::get('PS_TAX');
            $showTaxes = (int)(Configuration::get('PS_TAX_DISPLAY') == 1 && (int)Configuration::get('PS_TAX'));
            $priceDisplay = Product::getTaxCalculationMethod((int)$cart->id_customer);

            $subtotal = new JTotal();
            if ($useTaxes) {
                if ($priceDisplay) {
                    $subtotal->id = ++$id;
                    $subtotal->type = 'subtotal';
                    $subtotal->label = $this->_l('Subtotal') . ' ' . $this->_l('(tax excl.)');
                    $subtotal->amount = @$cartSummary['total_products'];
                    $subtotal->value = $this->_convertPrice(@$cartSummary['total_products']);
                } else {
                    $subtotal->id = ++$id;
                    $subtotal->type = 'subtotal';
                    $subtotal->label = $this->_l('Subtotal') . ' ' . $this->_l('(tax incl.)');
                    $subtotal->amount = @$cartSummary['total_products_wt'];
                    $subtotal->value = $this->_convertPrice(@$cartSummary['total_products_wt']);
                }
            } else {
                $subtotal->id = ++$id;
                $subtotal->type = 'subtotal';
                $subtotal->label = $this->_l('Subtotal');
                $subtotal->amount = @$cartSummary['total_products'];
                $subtotal->value = $this->_convertPrice(@$cartSummary['total_products']);
            }
            $totals[] = $subtotal;

            if (@$cartSummary['total_wrapping']) {
                $gift = new JTotal();
                if ($useTaxes) {
                    if ($priceDisplay) {
                        $gift->id = ++$id;
                        $gift->type = 'gift_wrapping';
                        $gift->label = $this->_l('Total gift wrapping') . ' ' . $this->_l('(tax excl.):');
                        $gift->amount = @$cartSummary['total_wrapping_tax_exc'];
                        $gift->value = $this->_convertPrice(@$cartSummary['total_wrapping_tax_exc']);
                    } else {
                        $gift->id = ++$id;
                        $gift->type = 'gift_wrapping';
                        $gift->label = $this->_l('Total gift wrapping') . ' ' . $this->_l('(tax incl.)');
                        $gift->amount = @$cartSummary['total_wrapping'];
                        $gift->value = $this->_convertPrice(@$cartSummary['total_wrapping']);
                    }
                } else {
                    $gift->id = ++$id;
                    $gift->type = 'gift_wrapping';
                    $gift->label = $this->_l('Total gift wrapping');
                    $gift->amount = @$cartSummary['total_wrapping_tax_exc'];
                    $gift->value = $this->_convertPrice(@$cartSummary['total_wrapping_tax_exc']);
                }
                $totals[] = $gift;
            }

            $shipping = new JTotal();
            if (@$cartSummary['total_shipping_tax_exc'] && @$cartSummary['free_ship']) {
                $shipping->id = ++$id;
                $shipping->type = 'shipping';
                $shipping->label = $this->_l('Total shipping');
                $shipping->amount = '0';
                $shipping->value = $this->_l('Free Shipping!');
            } else {
                if ($useTaxes && @$cartSummary['total_shipping_tax_exc'] != @$cartSummary['total_shipping']) {
                    if ($priceDisplay) {
                        $shipping->id = ++$id;
                        $shipping->type = 'shipping';
                        $shipping->label = $this->_l('Total shipping') . ' ' . $this->_l('(tax excl.)');
                        $shipping->amount = @$cartSummary['total_shipping_tax_exc'];
                        $shipping->value = $this->_convertPrice(@$cartSummary['total_shipping_tax_exc']);
                    } else {
                        $shipping->id = ++$id;
                        $shipping->type = 'shipping';
                        $shipping->label = $this->_l('Total shipping') . ' ' . $this->_l('(tax incl.)');
                        $shipping->amount = @$cartSummary['total_shipping'];
                        $shipping->value = $this->_convertPrice(@$cartSummary['total_shipping']);
                    }
                } else {
                    $shipping->id = ++$id;
                    $shipping->type = 'shipping';
                    $shipping->label = $this->_l('Total shipping');
                    $shipping->amount = @$cartSummary['total_shipping_tax_exc'];
                    $shipping->value = $this->_convertPrice(@$cartSummary['total_shipping_tax_exc']);
                }
            }
            $totals[] = $shipping;

            if (@$cartSummary['total_discounts']) {
                $discount = new JTotal();
                if ($useTaxes) {
                    if ($priceDisplay) {
                        $discount->id = ++$id;
                        $discount->type = 'discount';
                        $discount->label = $this->_l('Total vouchers') . ' ' . $this->_l('(tax excl.)');
                        $discount->amount = -1 * @$cartSummary['total_discounts_tax_exc'];
                        $discount->value = $this->_convertPrice(-1 * @$cartSummary['total_discounts_tax_exc']);
                    } else {
                        $discount->id = ++$id;
                        $discount->type = 'discount';
                        $discount->label = $this->_l('Total vouchers') . ' ' . $this->_l('(tax incl.)');
                        $discount->amount = -1 * @$cartSummary['total_discounts'];
                        $discount->value = $this->_convertPrice(-1 * @$cartSummary['total_discounts']);
                    }
                } else {
                    $discount->id = ++$id;
                    $discount->type = 'discount';
                    $discount->label = $this->_l('Total vouchers');
                    $discount->amount = -1 * @$cartSummary['total_discounts_tax_exc'];
                    $discount->value = $this->_convertPrice(-1 * @$cartSummary['total_discounts_tax_exc']);
                }
                $totals[] = $discount;
            }

            $grandtotal = new JTotal();
            if ($useTaxes) {
                if (@$cartSummary['total_tax'] && $showTaxes) {
                    if ($priceDisplay) {
                        $grandtotal->id = ++$id;
                        $grandtotal->type = 'total';
                        $grandtotal->label = $this->_l('Total') . ' ' . $this->_l('(tax excl.)');
                        $grandtotal->amount = @$cartSummary['total_price_without_tax'];
                        $grandtotal->value = $this->_convertPrice(@$cartSummary['total_price_without_tax']);
                    }
                    $tax = new JTotal();
                    $tax->id = ++$id;
                    $tax->type = 'tax';
                    $tax->label = $this->_l('Total') . ' ' . $this->_l('(tax excl.)');
                    $tax->amount = @$cartSummary['total_tax'];
                    $tax->value = $this->_convertPrice(@$cartSummary['total_tax']);

                    $totals[] = $tax;
                }
                $grandtotal->id = ++$id;
                $grandtotal->type = 'total';
                $grandtotal->label = $this->_l('Total');
                $grandtotal->amount = @$cartSummary['total_price'];
                $grandtotal->value = $this->_convertPrice(@$cartSummary['total_price']);
            } else {
                $grandtotal->id = ++$id;
                $grandtotal->type = 'total';
                $grandtotal->label = $this->_l('Total') . ' ' . $this->_l('(tax excl.)');
                $grandtotal->amount = @$cartSummary['total_price_without_tax'];
                $grandtotal->value = $this->_convertPrice(@$cartSummary['total_price_without_tax']);
            }
            $totals[] = $grandtotal;
        }

        /**
         * Fix total amount = 0
         */
        foreach ($totals as $total) {
            if ($total->amount === 0) {
                $total->amount = '0';
            }
        }

        return $totals;
    }

    /**
     * Get all possible carrier options of a cart which provided via API request parameters
     *
     * @throws WebserviceException
     */
    protected function getCarriers()
    {
        $cart = $this->_getCart();
        $context = $this->_getContextFromCart($cart);

        $carriers = array();
        $carriers['empty'] = new JCarrier();

        $results = array();
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $carrierFinder = new DeliveryOptionsFinder(
                $context,
                $context->getTranslator(),
                new PrestaShop\PrestaShop\Adapter\ObjectPresenter(),
                new PrestaShop\PrestaShop\Adapter\Product\PriceFormatter()
            );
            $results = $carrierFinder->getDeliveryOptions();
        } else {
            $free_shipping = false;
            foreach ($cart->getCartRules() as $rule) {
                if ($rule['free_shipping'] && !$rule['carrier_restriction']) {
                    $free_shipping = true;
                    break;
                }
            }

            foreach ($cart->getDeliveryOptionList() as $id_address => $option_list) {
                foreach ($option_list as $key => $option) {
                    foreach ($option['carrier_list'] as $item) {
                        $carrier = array(
                            'id' => @$item['instance']->id,
                            'name' => @$item['instance']->name,
                            'logo' => @$item['logo'],
                            'delay' => @$item['instance']->delay[$cart->id_lang],
                            'price' => '',
                            'price_with_tax' => @$item['price_with_tax'],
                            'price_without_tax' => @$item['price_without_tax']
                        );
                        if ($option['total_price_with_tax'] && !$option['is_free'] && !$free_shipping) {
                            if (Configuration::get('PS_TAX') == 1) {
                                if (Product::getTaxCalculationMethod((int)$context->cookie->id_customer) == 1) {
                                    $carrier['price'] = sprintf(
                                        '%s %s',
                                        $this->_convertPrice($option['total_price_without_tax']),
                                        $this->_l('(tax excl.)')
                                    );
                                } else {
                                    $carrier['price'] = sprintf(
                                        '%s %s',
                                        $this->_convertPrice($option['total_price_with_tax']),
                                        $this->_l('(tax incl.)')
                                    );
                                }
                            } else {
                                $carrier['price'] = $option['total_price_without_tax'];
                            }
                        } else {
                            $carrier['price'] = $this->_l('Free');
                        }
                        $results[] = $carrier;
                    }
                }
            }
        }

        $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
        $exludedCarriers = isset($exluded['carriers']) ? $exluded['carriers'] : array();

        foreach ($results as $result) {
            if (in_array(@$result['id'], $exludedCarriers)) {
                continue;
            }

            $carrier = new JCarrier();
            $carrier->id = @$result['id'];
            $carrier->id_address = $cart->id_address_delivery;
            $carrier->name = @$result['name'];
            $carrier->logo = !empty($result['logo']) ? sprintf('%s%s', _PS_BASE_URL_, $result['logo']) : '';
            $carrier->delay = @$result['delay'];
            $carrier->price = @$result['price'];
            $carrier->price_with_tax = @$result['price_with_tax'];
            $carrier->price_without_tax = @$result['price_without_tax'];

            $carriers[] = $carrier;
        }

        $this->output = $this->objOutput->getContent(
            $carriers,
            null,
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_LIST,
            false
        );
    }

    protected function setCarrier()
    {
        $cart = $this->_getCart();
        $this->_getContextFromCart($cart);

        $carrierId = $this->wsObject->urlFragments['carrier_id'];
        if (!$carrierId) {
            throw new WebserviceException('You have to set the \'carrier_id\' parameter to get a result.', 400);
        }
        $carrierId = $carrierId . ',';
        $addressId = $cart->id_address_delivery;

        $cart->setDeliveryOption(array($addressId => $carrierId));
        $cart->update();

        $totals = $this->_getTotals($cart);
        $totals['empty'] = new JTotal();

        $payments = $this->_getPayments($cart);
        $payments['empty'] = new JPayment();

        $this->output =
            $this->objOutput->getContent(
                $payments,
                null,
                'full',
                $this->wsObject->depth,
                WebserviceOutputBuilder::VIEW_LIST,
                false
            ) .
            $this->output = $this->objOutput->getContent(
                $totals,
                null,
                'full',
                $this->wsObject->depth,
                WebserviceOutputBuilder::VIEW_LIST,
                false
            );
    }

    /**
     * Translate string
     *
     * @param string $string
     * @return string
     */
    protected function _l($string = '')
    {
        return Translate::getModuleTranslation('jmango360api', $string, 'jmango360api');
    }

    /**
     * Convert price with right format and currency
     *
     * @param null $price
     * @return string
     */
    protected function _convertPrice($price = null)
    {
        $smarty = Context::getContext()->smarty;

        return Product::convertPrice(array('price' => $price), $smarty);
    }

    /**
     * Get all possible payment options of a cart which provided via API request parameters
     *
     * @throws WebserviceException
     */
    protected function getPayments()
    {
        $cart = $this->_getCart();
        $context = $this->_getContextFromCart($cart);

        $payments = $this->_getPayments($cart);
        $payments['empty'] = new JPayment();

        $this->output = $this->objOutput->getContent(
            $payments,
            null,
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_LIST,
            false
        );

        $paymentUrls = array();
        $paymentUrls['empty'] = new JPaymentUrl();
        $paymentUrl = new JPaymentUrl();
        $paymentUrl->id = 1;
        $paymentUrl->url = $context->link->getModuleLink('jmango360api', 'payment', array(), true);
        $paymentUrls[] = $paymentUrl;

        $this->output .= $this->objOutput->getContent(
            $paymentUrls,
            null,
            'full',
            $this->wsObject->depth,
            WebserviceOutputBuilder::VIEW_DETAILS,
            false
        );
    }

    protected function _getPayments($cart)
    {
        $payments = array();
        $results = $this->paymentFinder->getPaymentOptions($cart);

        $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
        $exludedPayments = isset($exluded['payments']) ? $exluded['payments'] : array();

        foreach ($results as $item) {
            if (in_array($item['module_id'], $exludedPayments)) {
                continue;
            }

            $payment = new JPayment();
            $payment->id = $item['id'];
            $payment->title = $item['title'];
            $payment->description = $item['description'];
            $payment->logo = !empty($item['logo']) ? sprintf('%s%s', _PS_BASE_URL_, $item['logo']) : '';
            $payment->url = $item['url'];
            $payment->inputs = $item['inputs'];
            $payment->form = $item['form'];

            $payments[] = $payment;
        }

        return $payments;
    }

    /**
     * This must be return a string with specific values as WebserviceRequest expects.
     *
     * @return string
     */
    public function getContent()
    {
        return $this->objOutput->getObjectRender()->overrideContent($this->output);
    }
}

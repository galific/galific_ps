<?php
/**
 * Class BaseService
 * @author Jmango
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

abstract class BaseService implements JapiService
{
    /** @var WebserviceRequestCore */
    protected $request;

    protected $response;

    protected $context;

    protected $translator;

    public $module_name;

    /**
     * List of profilers
     *
     * @var array
     */
    public static $profilers = array();

    public function __construct($module_name)
    {
        $this->context = Context::getContext();
        $this->module_name = $module_name;
    }

    /**
     * Check debug flag
     *
     * @return bool
     */
    protected static function isDebug()
    {
        return Tools::getIsset('XDEBUG_SESSION_START');
    }

    /**
     * Start profiler
     *
     * @param $key
     * @throws Exception
     */
    public static function startProfiler($key)
    {
        if (!self::isDebug()) {
            return;
        }

        if (!$key) {
            throw new Exception('Profiler key is empty');
        }

        self::$profilers[$key] = microtime(true);
    }

    /**
     * Stop profiler
     *
     * @param $key
     * @throws Exception
     */
    public static function stopProfiler($key)
    {
        if (!self::isDebug()) {
            return;
        }

        if (!$key) {
            throw new Exception('Profiler key is empty');
        }

        if (!isset(self::$profilers[$key])) {
            throw new Exception('Profiler key not found');
        }

        self::$profilers[$key] = microtime(true) - (float)self::$profilers[$key];
    }

    /**
     * Main service function
     *
     * @param $request
     * @param $response
     * @param $output
     * @throws Exception
     */
    public function execute(&$request, &$response, &$output)
    {
        //assign variables to use later
        $this->request = $request;
        $this->response = $response;

        //check shop id
        if (!$this->checkShopExist()) {
            $this->response = new JmResponse();
            $this->response->errors = array('This shop does not exist!');
            $this->renderOutput();
            return;
        }

        //check ws_key
        if (!$this->checkRequestPermission()) {
            $this->response = new JmResponse();
            $this->response->errors = array('The access key does not have enough permission!');
            $this->renderOutput();
            return;
        }

        $this->startProfiler('EXECUTE');

        //do prepare data and settings before doing logic
        $this->prepare();

        //do logic in sub class
        $this->doExecute();

        $this->stopProfiler('EXECUTE');

        // refer to: https://wiki.php.net/rfc/precise_float_value
        // keep price to original values instead of rounding to 14 decimal precision.
        if (version_compare(phpversion(), '7.1', '>=')) {
            ini_set('serialize_precision', -1);
        }

        $this->renderOutput();

        //suppend
        exit();
    }

    /**
     * Prepare data and settings
     * @return bool
     */
    protected function checkShopExist()
    {
        $shop_id = $this->getRequestValue('id_shop');
        $shop = new Shop($shop_id);

        return $shop && $shop->id;
    }

    /**
     * Check access permission to the request resource
     * @return bool
     */
    protected function checkRequestPermission()
    {
        //TODO: check request permission

        return true;
    }


    /**
     * Prepare data and settings
     */
    protected function prepare()
    {
        $this->prepareLanguage();
        $this->prepareShop();
        $this->prepareCurrency();
        $this->prepareCustomer();
        $this->prepareCart();
    }

    /**
     * Prepare language
     */
    protected function prepareLanguage()
    {
        $lang_id = $this->getRequestValue('id_lang');
        $language = LanguageCore::getLanguage($lang_id);
        $this->context->language->id = $lang_id;
        $this->context->language->iso_code = $language['iso_code'];
        $this->context->language->locale = isset($language['locale']) ? $language['locale'] : null;
        $this->context->language->language_code = $language['language_code'];
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $context = new Context();
            $context->language->id = $lang_id;
            $context->language->iso_code = $language['iso_code'];
            $context->language->locale = $language['locale'];
            $context->language->language_code = $language['language_code'];
            $this->context->getTranslator()->setLocale($language['locale']);
            $this->translator = $context->getTranslator();
        }
    }

    /**
     * Prepare shop
     */
    protected function prepareShop()
    {
        $shop_id = $this->getRequestValue('id_shop');
        $this->context->shop->id = $shop_id;
    }

    /**
     * Prepare customer
     */
    protected function prepareCustomer()
    {
        //TODO: check customer session expired
        $customer_id = $this->getRequestValue('id_customer');
        $customer = new Customer($customer_id);
        $cookie = $this->context->cookie;
        if ($customer && $customer->id) {
            $this->context->customer = $customer;
            $cookie->__set('id_customer', $customer_id);
            $cookie->__set('logged', 1);
        } else {
            //clear the last session of customer
            if ($this->context->customer) {
                $this->context->customer->id = 0;
                $cookie->__set('id_customer', 0);
                $cookie->__set('logged', 0);
            }
        }
    }

    /**
     * Prepare cart
     */
    protected function prepareCart()
    {
        if (strcmp($this->getRequestResourceId(2), 'carts') == 0) {
            $id_cart = $this->getRequestResourceId();
            if ($id_cart != null) {
                $cart = null;

                if ($this->isCartFinished($id_cart)) {
                    $cart = null;
                } else {
                    $cart = new Cart($id_cart, $this->context->language->id);
                }

                $this->context->cart = $cart;
                CartRule::autoAddToCart($this->context);
            }
        }
    }

    /**
     * Prepare currency
     */
    protected function prepareCurrency()
    {
        // If server send currency id, then use it, else, use default currency.
        $id_currency = $this->getRequestValue('id_currency');
        if ($id_currency != null) {
            $this->context->currency = new Currency($id_currency);
        } else {
            $this->context->currency = Currency::getDefaultCurrency();
        }
    }

    /**
     * Get value from URL parameters
     * @param string $key
     * @param null $default_value
     * @return string
     */
    protected function getRequestValue($key, $default_value = null)
    {
        $value = Tools::getValue($key, $default_value);
        return null != $value ? trim($value) : null;
    }

    /**
     * Get value from URL parameters
     * @param int $index , default = 3
     * @return string
     */
    protected function getRequestResourceId($index = 3)
    {
        //url format:
        //- japi/rest/accounts/1/logout

        //ignore japi/rest, so get resource id from index
        return trim($this->request->urlSegment[$index]);
    }

    /**
     * Check if request method is GET
     */
    protected function isGetMethod()
    {
        return 'GET' == $this->request->method;
    }

    /**
     * Check if request method is POST
     */
    protected function isPostMethod()
    {
        return 'POST' == $this->request->method;
    }

    /**
     * Check if request method is PUT
     */
    protected function isPutMethod()
    {
        return 'PUT' == $this->request->method;
    }

    /**
     * Check if request method is DELETE
     */
    protected function isDeleteMethod()
    {
        return 'DELETE' == $this->request->method;
    }


    /**
     * Return unsupported
     */
    protected function throwUnsupportedMethodException()
    {
        throw new WebserviceException('Invalid request method', 400);
    }

    /**
     * Check version => 1.7
     */
    protected function isV17()
    {
        return version_compare(_PS_VERSION_, '1.7', '>=');
    }

    /**
     * Render response in Json format
     */
    protected function renderOutput()
    {
        header("Content-Type: application/json");

        /**
         * Append profiler header
         */
        if (is_array(self::$profilers)) {
            foreach (self::$profilers as $profiler => $time) {
                header(sprintf('X-Profiler-%s: %s', (string)$profiler, (string)$time));
            }
        }

        echo(json_encode($this->response, JSON_PRETTY_PRINT));
    }

    /**
     * Get json request body
     */
    protected function getRequestBody()
    {
        return CustomRequest::getRequestBody();
    }

    /**
     * Implement business logic
     */
    abstract public function doExecute();

    public function initializeCart(&$context)
    {
        $cart = $context->cart;
        if (!isset($cart) || !$cart->id) {
            $cart = new Cart();
            $cart->id = 1;
            $cart->id_lang = (int)$context->cookie->id_lang;
            $cart->id_currency = (int)$context->cookie->id_currency;
            $cart->id_guest = (int)$context->cookie->id_guest;
            $cart->id_shop_group = (int)$context->shop->id_shop_group;
            $cart->id_shop = $context->shop->id;
            if ($context->cookie->id_customer) {
                $cart->id_customer = (int)$context->cookie->id_customer;
                $cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($cart->id_customer);
                $cart->id_address_invoice = (int)$cart->id_address_delivery;
            } else {
                $cart->id_address_delivery = 0;
                $cart->id_address_invoice = 0;
            }

            // Needed if the merchant want to give a free product to every visitors
            $context->cart = $cart;
            CartRule::autoAddToCart($this->context);
        } else {
            $context->cart = $cart;
        }
    }

    public function getTranslation($string, $source)
    {
//        return Translate::getModuleTranslation('jmango360api', $string, $source);
        $file = _PS_MODULE_DIR_ . $this->module_name . '/translations/' . $this->context->language->iso_code . '.php';
        if (file_exists($file)) {
            include($file);
        }
        $translation_text = $GLOBALS['_MODULE'];
        $key = '<{'.$this->module_name.'}prestashop>' . $source . '_' . md5($string);
        if (array_key_exists($key, $translation_text)) {
            return $translation_text[$key];
        } else {
            return $string;
        }
    }

    protected function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $parameters['legacy'] = 'htmlspecialchars';
        if ($this->translator) {
            return $this->translator->trans($id, $parameters, $domain, $locale);
        }

        return null;
    }

    protected function isCartFinished($id_cart)
    {
        $id_order = $this->getIdByCartId($id_cart);

        return $id_order && $id_order > 0;
    }

    protected function throwServiceException($code, $codeMessage, $message)
    {
        header(sprintf('HTTP/1.1 %d %s ', $code, $codeMessage));
        header(sprintf('Status: %d %s ', $code, $codeMessage));
        die($message);
    }

    public static function getIdByCartId($id_cart)
    {
        $sql = 'SELECT `id_order` 
            FROM `' . _DB_PREFIX_ . 'orders`
            WHERE `id_cart` = ' . (int)$id_cart .
            Shop::addSqlRestriction();

        $result = Db::getInstance()->getValue($sql);

        return !empty($result) ? (int)$result : false;
    }


    public function isJmCustomer($id_customer)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'jmango360_user` WHERE `id_user`= ' . (int)$id_customer;
        return DB::getInstance()->getValue($sql) ? true : false;
    }

    public function addJmCustomer($id_customer)
    {
        DB::getInstance()->insert(
            'jmango360_user',
            array(
                'id_user' => (int)$id_customer,
            )
        );
    }

    public function getThemeTranslation($string, $source)
    {
        if (!$this->isV17()) {
            $key = $source . '_' . md5($string);
            if (array_key_exists($key, $this->translation)) {
                return $this->translation[$key];
            } else {
                return $string;
            }
        } else {
            return $this->trans($string, array(), $source, $this->context->language->locale);
        }
    }

    /**
     * Get payload data from POST request
     *
     * @return mixed
     */
    public function retrievePayload()
    {
        $request_body = Tools::file_get_contents('php://input');
        $payload = json_decode($request_body, true);
        return $payload;
    }

    public function formatErrors($errors)
    {
        if (!$this->isV17()) {
            if (sizeof($errors) === 1) {
                $errormsg = sprintf($this->getThemeTranslation('There is %d error', 'errors'), 1);
            } else {
                $errormsg = sprintf($this->getThemeTranslation('There are %d errors', 'errors'), sizeof($errors));
            }
        }
        $i = 1;
        foreach ($errors as $error) {
            $error = strip_tags($error);
            $error = html_entity_decode($error);
            if ($this->isV17() && version_compare(_PS_VERSION_, '1.7.4', '<')) {
                if (strpos($error, 'is invalid.') !== false) {
                    $error = str_replace(' is invalid.', '', $error);
                    $error = $this->trans(
                        'The %s field is invalid.',
                        array($error),
                        'Admin.Notifications.Error',
                        $this->context->language->locale
                    );
                }
            }
            $errormsg = $errormsg . "\n" . $i++ . '. ' . $error;
        }
        return $errormsg;
    }
}

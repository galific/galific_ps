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

abstract class AppCore
{
    public $context;
    protected $content;
    protected $output_format = 'json';
    protected $request_url = '';
    protected $key;
    protected $authenticated = false;
    protected $ssl = false;
    public $controller_type = 'front';
    private $startTime;
    public $shop = null;
    public $errors = array();
    protected $header_params = array();
    public $order_by = 'price';
    public $order_way = 'ASC';
    public $page_number = 1;
    public $limit = 10;
    private $img_1 = 'large';
    private $img_2 = 'medium';
    private $img_3 = '_default';
    private $log_obj = null;
    public $customer_wishlist;

    const BANNER_SIZE_WIDTH = 400;
    const BANNER_SIZE_HEIGHT = 225;
    const LIST_PRODUCT_WIDTH = 200;
    const LIST_PRODUCT_HEIGHT = 200;
    const PRICE_REDUCTION_TYPE_PERCENT = 'percentage';
    const APP_TRANSLATION_FILE_NAME = 'androidgenericapp';
    const APP_TRANSLATION_FILE_FOLDER_PATH = 'kbmobileapp/translations/';
    const TRANSLATION_EXT = '.csv';
    const APP_TRANSLATION_STARTS_WITH = 'app_text_';
    const TRANSLATION_RECORD_FILE = 'translation_record.csv';

    /**
     * Abstract function
     */
    abstract public function getPageData();

    /**
     * Initialize the page
     * Set context values here
     */
    public function init()
    {
        if (!defined('_PS_BASE_URL_')) {
            define('_PS_BASE_URL_', Tools::getShopDomain(true));
        }

        if (!defined('_PS_BASE_URL_SSL_')) {
            define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));
        }

        $protocol_link = (Configuration::get('PS_SSL_ENABLED') || Tools::usingSecureMode()) ? 'https://' : 'http://';
        $useSSL = ((isset($this->ssl)
            && $this->ssl && Configuration::get('PS_SSL_ENABLED'))
            || Tools::usingSecureMode()) ? true : false;
        $protocol_content = ($useSSL) ? 'https://' : 'http://';
        $link = new Link($protocol_link, $protocol_content);
        $this->context->link = $link;

        $id_language = Configuration::get('PS_LANG_DEFAULT');
        $id_currency = Configuration::get('PS_CURRENCY_DEFAULT');
        $id_country = Configuration::get('PS_COUNTRY_DEFAULT');

        if (Tools::getValue('id_language')) {
            $id_language = Tools::getValue('id_language');
        }
        
        if (Tools::isSubmit('iso_code')) {
            $id_language = (int)Language::getIdByIso(Tools::getValue('iso_code', ''));
        }

        if (Tools::getValue('id_currency')) {
            $id_currency = Tools::getValue('id_currency');
        }

        if (Tools::getValue('id_country')) {
            $id_country = Tools::getValue('id_country');
        }

        if ($id_language) {
            $this->context->language = new Language($id_language);
            $this->context->cookie->id_lang = $id_language;
        } else {
            $this->context->language = new Language(Configuration::get('PS_LANG_DEFAULT'));
            $this->context->cookie->id_lang = Configuration::get('PS_LANG_DEFAULT');
        }
        
        $this->customer_wishlist = array();

        /* update customer context */
        if (Tools::getValue('email')) {
            $email = Tools::getValue('email', '');
            if ($email && Validate::isEmail($email)) {
                if (Customer::customerExists(strip_tags(Tools::getValue('email')))) {
                    $customer_obj = new Customer();
                    $customer_tmp = $customer_obj->getByEmail(Tools::getValue('email'));

                    $customer = new Customer($customer_tmp->id);

                    //Update Context
                    $this->context->customer = $customer;
                    $this->context->cookie->id_customer = (int) $customer->id;
                    $this->context->cookie->customer_lastname = $customer->lastname;
                    $this->context->cookie->customer_firstname = $customer->firstname;
                    $this->context->cookie->passwd = $customer->passwd;
                    $this->context->cookie->logged = 1;
                    $this->context->cookie->email = $customer->email;
                    $this->context->cookie->is_guest = $customer->is_guest;
                    $this->getCustomerWishlistProducts($customer->id);
                } else {
                    $customer = new Customer();
                    $this->context->customer = $customer;
                }
            } else {
                $customer = new Customer();
                $this->context->customer = $customer;
            }
        } else {
            $customer = new Customer();
            $this->context->customer = $customer;
        }

        /* update cart context */
        $cart_id = (int)Tools::getValue('session_data', 0);
        if ($cart_id) {
            $this->context->cart = new Cart(
                (int) Tools::getValue('session_data', 0),
                false,
                null,
                null,
                $this->context
            );
            /* Get country id by shipping address id */
            if ($this->context->cart->id_address_delivery > 0) {
                $address = new Address($this->context->cart->id_address_delivery);
                $id_country = $address->id_country;
            }
            $this->context->cart->id_currency = (int) $id_currency;
            $this->context->cart->update();
            $this->context->cookie->id_cart = (int) $this->context->cart->id;
            $this->context->cookie->write();
        }

        
        $this->context->currency = new Currency($id_currency, null, $this->context->shop->id);
        $this->context->country = new Country($id_country, $id_language, $this->context->shop->id);
    }

    /*
     * To get the translatted text by file bname and iso code of language
     * 
     * @param string $lang_iso languag iso code
     * @param string $string text to be translated
     * @param string $file_name name of the file in which a text is present
     * @return string translatted text
     */
    public function getTranslatedTextByFileAndISO($lang_iso, $string, $file_name)
    {
        if (!$lang_iso) {
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
            $lang_iso = Language::getIsoById($lang_id);
        }
        $unique_key = '';
        if ($file_name != self::APP_TRANSLATION_FILE_NAME) {
            $unique_key = Tools::strtolower($file_name).'_'.md5($string);
        } else {
            $unique_key = $string;
        }
        $file_path = _PS_MODULE_DIR_.self::APP_TRANSLATION_FILE_FOLDER_PATH.$lang_iso.self::TRANSLATION_EXT;
        if (is_readable($file_path)) {
            $file_handle = fopen($file_path, 'r');
            while (!feof($file_handle)) {
                $csv_line = fgetcsv($file_handle);
                if ($csv_line[0] == $unique_key) {
                    fclose($file_handle);
                    return $csv_line[1];
                }
            }
        }
        if ($file_name != self::APP_TRANSLATION_FILE_NAME) {
            return $string;
        } else {
            return '';
        }
    }
    
    /*
     * Get all the text with key from csv file for mobile APP
     * 
     * @param string $lang_iso language iso code
     * @return array
     */
    public function getAllAppTranslatedTexts($lang_iso = false)
    {
        if (!$lang_iso) {
            $lang_id = Configuration::get('PS_LANG_DEFAULT');
            $lang_iso = Language::getIsoById($lang_id);
        }
        $file_path = _PS_MODULE_DIR_.self::APP_TRANSLATION_FILE_FOLDER_PATH.$lang_iso.self::TRANSLATION_EXT;
        if (!Tools::file_exists_no_cache($file_path)) {
            $file_path = _PS_MODULE_DIR_.self::APP_TRANSLATION_FILE_FOLDER_PATH.'en'.self::TRANSLATION_EXT;
        }
        $app_texts = array();
        if (is_readable($file_path)) {
            $file_handle = fopen($file_path, 'r');
            $count = 0;
            while (!feof($file_handle)) {
                $csv_line = fgetcsv($file_handle);
                if (0 === strpos($csv_line[0], self::APP_TRANSLATION_STARTS_WITH)) {
                    $app_texts[$count]['unique_key'] = $csv_line[0];
                    $app_texts[$count]['iso_code'] = $lang_iso;
                    $app_texts[$count]['trans_text'] = $csv_line[1];
                    $count++;
                }
            }
        }
        return $app_texts;
    }
    
    /*
     * Function to get the translations record data
     * include file update time stamp
     * 
     * @return array 
     */
    public function returnLanguageRecordAsArray()
    {
        $lang_arr = array();
        $file_folder_path = _PS_MODULE_DIR_.'kbmobileapp/translations/';
        $file_path = $file_folder_path.self::TRANSLATION_RECORD_FILE;
        if (is_readable($file_path)) {
            $file_handle = fopen($file_path, 'r');
            $count = 0;
            while (!feof($file_handle)) {
                $csv_line = fgetcsv($file_handle);
                if (!isset($csv_line[0]) || $csv_line[0] == '') {
                    continue;
                }
                $lang_arr[$count]['iso_code'] = $csv_line[0];
                if (isset($csv_line[1])) {
                    $lang_arr[$count]['timestamp'] = $csv_line[1];
                } else {
                    $lang_arr[$count]['timestamp'] = time();
                }
                $count++;
            }
        }
        return $lang_arr;
    }

    /*
     * Function to update translation record
     * update the language file update timestamp in a csv
     */
    public function updateLanguageFileRecords()
    {
        $file_folder_path = _PS_MODULE_DIR_.'kbmobileapp/translations/';
        $file_path = $file_folder_path.self::TRANSLATION_RECORD_FILE;
        $files = glob("$file_folder_path*.csv");
        $records = array();
        $count = 0;
        $lang_record = $this->returnLanguageRecordAsArray();
        foreach ($files as $file) {
            $file_name = basename($file);
            if ($file_name == self::TRANSLATION_RECORD_FILE) {
                continue;
            }
            $file_orig_name = pathinfo($file_name, PATHINFO_FILENAME);
            if (isset($lang_record[$file_orig_name])) {
                if ($lang_record[$file_orig_name][0] != filemtime($file)) {
                    $records[$count][] = $file_orig_name;
                    $records[$count][] = filemtime($file);
                } else {
                    $records[$count][] = $file_orig_name;
                    $records[$count][] = $lang_record[$file_orig_name][0];
                }
            } else {
                $records[$count][] = $file_orig_name;
                $records[$count][] = filemtime($file);
            }
            $count++;
        }
        
        $this->writeArraytoCSV($records, $file_path);
    }
    
    /*
     * Function to write values in a csv
     * 
     * @param array $array data to be write in csv
     * @param string $path path of a file
     */
    private function writeArraytoCSV($array, $path)
    {
        $file = fopen($path, "w");

        foreach ($array as $line) {
            fputcsv($file, $line);
        }
        fclose($file);
    }

    /*
     * This function is used to include the text in admin tranlstions
     * 
     * @param string $string string to be translate
     * @return string 
     */
    public function l($string)
    {
        /* This is just a blank function to help include the translations text into language files. */
        return $string;
    }

    /*
     * To get the type of image
     * 
     * @param string $type type of image large/medium/default
     * @return string 
     */
    public function getImageType($type = 'large')
    {
        if ($type == 'large') {
            return $this->img_1 . $this->img_3;
        } elseif ($type == 'medium') {
            return $this->img_2 . $this->img_3;
        } else {
            return $this->img_1 . $this->img_3;
        }
    }

    /*
     * Construtor function to initalize log file object
     * 
     * @param string $url requested url
     */
    public function __construct($url = '')
    {
        $this->context = Context::getContext();
        $this->context->controller = $this;

        $this->init();
        $this->context->shop = Shop::initialize();
        $this->request_url = $url;

        $log_dir = _PS_ROOT_DIR_ . '/log/kbmobileapp/';
        if (!is_dir($log_dir)) {
            if (mkdir($log_dir, 0777)) {
                $log_dir = $log_dir . date('Y_m_d', time());
                @mkdir($log_dir, 0777);
            }
        } else {
            $log_dir = $log_dir . date('Y_m_d', time());
            if (!is_dir($log_dir)) {
                @mkdir($log_dir, 0777);
            }
        }

        $this->log_obj = fopen($log_dir . 'log.txt', 'a+');
    }

    /*
     * This function fetch the json data of webservice
     * called from apicall controller
     * 
     * @return array
     */
    public function fetch()
    {
        $this->startTime = microtime(true);
        $content = $this->getPageData();

        return array(
            'type' => $this->output_format,
            'content' => $content,
            'headers' => $this->getHeaders()
        );
    }

    /*
     * To get the translated text
     * 
     * @param string $text text to be translated
     * @return string translated text
     */
    protected function getTranslatedText($text)
    {
        return Translate::getModuleTranslation('kbmobileapp', $text, 'kbmobileapp');
    }

    /*
     * Function to get image directory path
     * 
     * @return string path of image directory
     */
    protected function getImageDIR()
    {
        return $this->getModuleDir() . 'views/img/';
    }

    /*
     * Function to get module directory path
     * 
     * @return string path of module directory
     */
    protected function getModuleDir()
    {
        return _PS_MODULE_DIR_ . 'kbmobileapp/';
    }

    /**
     * This method is used to set header
     * @param array $header (key: value)
     */
    protected function setHeader($header)
    {
        $this->header_params[] = $header;
    }

    /*
     * Function to get the headers
     * 
     * @return array
     */
    private function getHeaders()
    {
        return array_merge(
            $this->header_params,
            array(
                'Access-Time: ' . time(),
                'Content-Type: text/' . $this->output_format,
                'X-Powered-By: PrestaShop Mobile App Webservice',
                'PSWS-Version: ' . _PS_VERSION_,
                'Execution-Time: ' . round(microtime(true) - $this->startTime, 3)
            )
        );
    }

    /*
     * Function to set error for webservice request
     * 
     * @param int $code status code
     * @param string $label error message
     */
    public function setError($code, $label)
    {
        $display_errors = false;
        if (Tools::strtolower(ini_get('display_errors')) != 'off') {
            $display_errors = true;
        }
        $this->errors[] = $display_errors ? array($code, $label) : array(1005, $this->getErrorMessage(1005));
    }

    /*
     * Function to make entry in log file 
     * 
     * @param string $message
     * @param string $log_type
     */
    public function writeLog($message = '', $log_type = 'Error')
    {
        $timestamp = date('Y-m-d H:i:s', time());
        fwrite($this->log_obj, $timestamp . "\t" . $this->request_url . "\t" . $log_type . "\t" . $message . "\r\n");
    }

    /*
     * function to get error message by code
     * 
     * @param int $code status code
     * @return string
     */
    public function getErrorMessage($code)
    {
        $error_description = array(
            1001 => 'You do not have permission for this webservice.',
            1002 => 'Blocks for this request are not defined.',
            1003 => 'Request block: "%s" is not exist for requested context.',
            1004 => 'No Content',
            1005 => 'Internal error. To see this error please display the PHP errors.',
            1006 => '%s module is not installed or active',
            1007 => 'Fatal Error: %s',
            1008 => '%s is missing',
            1009 => 'Authentication Key is not valid',
            1010 => 'Mobile app custom web service not available'
        );

        return $error_description[$code];
    }

    /*
     * Function to get the header status by code
     * 
     * @param int $code status code
     * @return string
     */
    public function getHeaderStatus($code)
    {
        $code = (int) $code;
        $protocol = $_SERVER['SERVER_PROTOCOL'] . ' ' . $code;
        switch ($code) {
            case 200:
                return $protocol . ' OK';
            case 201:
                return $protocol . ' Created';
            case 204:
                return $protocol . ' No Content';
            case 304:
                return $protocol . ' Not Modified';
            case 400:
                return $protocol . ' Bad Request';
            case 401:
                return $protocol . ' Unauthorized';
            case 403:
                return $protocol . ' Forbidden';
            case 404:
                return $protocol . ' Not Found';
            case 405:
                return $protocol . ' Method Not Allowed';
            case 500:
                return $protocol . ' Internal Server Error';
            case 501:
                return $protocol . ' Not Implemented';
            case 503:
                return $protocol . ' Service Unavailable';
        }
    }


    /*
     * Function to get the jsoin content for webservice
     * 
     * @return json
     */
    protected function fetchJSONContent()
    {
        $content = array();
        if (!empty($this->content)) {
            /* check configuration value */
            if (Configuration::get('KB_MOBILE_APP') == 0) {
                $this->content['install_module'] = self::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Mobile App module is disabled.'),
                    'AppCore'
                );
                $this->writeLog('Mobile App module is disabled.');
            }

            /* Check module is installed and active */
            if (!Module::isInstalled('kbmobileapp') || !Module::isEnabled('kbmobileapp')) {
                $this->content['install_module'] = self::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Mobile App module is either inactive or not installed.'),
                    'AppCore'
                );
                $this->writeLog('Mobile App module is either inactive or not installed.');
            }

            $content = $this->content;
        }
        if (Configuration::get('KB_MOBILE_APP_ERROR_REPORTING')) {
            $this->writeRequestDataToLog($content);
        }
        return Tools::jsonEncode($content);
    }
    
    /*
     * Function to write response in a log
     * 
     * @param array $response
     */
    public function writeRequestDataToLog($response)
    {
        $ip = '';
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        $time_stamp = time();
        $datetime = date('Y-m-d H:i:s', time());
        
        $log_file_path = _PS_MODULE_DIR_.'kbmobileapp/libraries/mobile_app_log.txt';
        $fp = fopen($log_file_path, "a+");
        fwrite($fp, $ip.PHP_EOL);
        fwrite($fp, 'Time Stamp - '.$time_stamp.PHP_EOL);
        fwrite($fp, 'Current Date - '.$datetime.PHP_EOL.PHP_EOL);
        fwrite($fp, 'GET Array - '.Tools::jsonEncode($_GET).PHP_EOL.PHP_EOL);
        fwrite($fp, 'POST Array - '.Tools::jsonEncode($_POST).PHP_EOL.PHP_EOL);
        fwrite($fp, 'Request Response - '.Tools::jsonEncode($response).PHP_EOL.PHP_EOL.PHP_EOL);
        fclose($fp);
    }


    /*
     * Function to get base link of store
     * 
     * @param string $ssl
     * @param int $id_shop shop id
     * @return string base url of store
     */
    public function getBaseLink($ssl = null, $id_shop = null)
    {
        static $force_ssl = null;
        if ($ssl === null) {
            if ($force_ssl === null) {
                $force_ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
            }
            $ssl = $force_ssl;
        }

        if (Configuration::get('PS_MULTISHOP_FEATURE_ACTIVE') && $id_shop !== null) {
            $shop = new Shop($id_shop);
        } else {
            $shop = $this->context->shop;
        }

        $base = (($ssl && Configuration::get('PS_SSL_ENABLED')) ?
            'https://' . $shop->domain_ssl : 'http://' . $shop->domain);

        return $base . $shop->getBaseURI();
    }

    /*
     * Function to get the purify html
     * 
     * @param string $value
     * @return string
     */
    protected function filterVar($value)
    {
        if (version_compare(_PS_VERSION_, '1.6.0.7', '>=') === true) {
            return Tools::purifyHTML($value);
        } else {
            return filter_var($value, FILTER_SANITIZE_STRING);
        }
    }
    
    /*
     * Function to format price in order details API
     * 
     * @param float $price
     * @param object $curr
     * @return string formated price
     */
    public function formatOrderPrice($price, $curr = null)
    {
        return Tools::displayPrice(
            $price,
            $curr,
            false,
            $this->context
        );
    }

    /*
     * Function to format price in API's
     * 
     * @param float $price
     * @param object $curr
     * @return string formated price
     */
    public function formatPrice($price, $curr = '')
    {
        return Tools::displayPrice(
            $price,
            $this->context->currency,
            false,
            $this->context
        );
    }

    /*
     * Function to get the starting page number
     * 
     * @return int
     */
    public function getPageStart()
    {
        return (($this->page_number - 1) * $this->limit);
    }

    /*
     * Function to set the list pagination data
     */
    protected function setListPagingData()
    {
        if ($page_number = Tools::getValue('page_number', 0)) {
            $this->page_number = $page_number;
        }

        if ($this->page_number <= 0) {
            $this->page_number = 1;
        }

        if ($order_by = Tools::getValue('order_by', null)) {
            $this->order_by = $order_by;
        }

        if ($order_way = Tools::getValue('sort_by', null)) {
            $this->order_way = $order_way;
        }

        if ($limit = Tools::getValue('item_count', null)) {
            $this->limit = $limit;
        }
    }

    /*
     * Function to get the anchor seperator
     * 
     * @return string
     */
    protected function getAnchor()
    {
        static $anchor = null;
        if ($anchor === null) {
            if (!$anchor = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR')) {
                $anchor = '-';
            }
        }
        return $anchor;
    }

    /*
     * Function to get the wishlist item count
     * 
     * @param int $customer_id id of customer
     * @return int wishlist item count
     */
    public function getWishListCount($customer_id)
    {
        if (!Module::isInstalled('blockwishlist') || !Module::isEnabled('blockwishlist')) {
            $wishlist_count = 0;
        } else {
            $deafult_wishlist_id = $this->getDefaultWishlist($customer_id);
            if ($deafult_wishlist_id) {
                $wishlist_products = $this->getProductByIdCustomer(
                    $deafult_wishlist_id,
                    $customer_id,
                    $this->context->language->id
                );
                if (!$wishlist_products) {
                    $wishlist_count = 0;
                } else {
                    $wishlist_count = count($wishlist_products);
                }
            } else {
                $wishlist_count = 0;
            }
        }

        return $wishlist_count;
    }

    /**
     * Get Default Wishlist ID by Customer ID
     *
     * @param int $id_customer customer id
     * @return int wishlist id of customer
     */
    public function getDefaultWishlist($id_customer)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT id_wishlist FROM `' . _DB_PREFIX_ . 'wishlist`
            WHERE `id_customer` = ' . (int)$id_customer . ' AND `default` = 1'
        );
    }
    
    /**
     * Get Languages data for Home Page
     *
     * @return array Languages Data
     */
    public function getLanguagesDataForHome()
    {
        $lang_data = array();
        $lang_data['lang_list'] = (array)Language::getLanguages(true);
        $lang_data['default_lang'] = Language::getIsoById(
            Configuration::get('PS_LANG_DEFAULT')
        );
        return $lang_data;
    }

    /**
     * Get Wishlist products by Customer ID
     *
     * @param int $id_wishlist wishlist id
     * @param int $id_customer customer id
     * @param int $id_lang language id
     * @return array
     */
    public function getProductByIdCustomer(
        $id_wishlist,
        $id_customer,
        $id_lang,
        $id_product = null,
        $quantity = false
    ) {
        if (!Validate::isUnsignedId($id_customer)
            or ! Validate::isUnsignedId($id_lang)
            or ! Validate::isUnsignedId($id_wishlist)) {
            $this->content['status'] = "failure";
            $this->content['message'] = self::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Unable to get product list'),
                'AppCore'
            );
            $this->writeLog('Unable to get product list');
            return false;
        }
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT wp.`id_product`, wp.`quantity`, p.`quantity` AS product_quantity,
            pl.`name`, wp.`id_product_attribute`, wp.`priority`, pl.link_rewrite, cl.link_rewrite
            AS category_rewrite
            FROM `' . _DB_PREFIX_ . 'wishlist_product` wp
            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = wp.`id_product`
            ' . Shop::addSqlAssociation('product', 'p') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON pl.`id_product` = wp.`id_product` '
            . Shop::addSqlRestrictionOnLang('pl') . '
            LEFT JOIN `' . _DB_PREFIX_ . 'wishlist` w ON w.`id_wishlist` = wp.`id_wishlist`
            LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON cl.`id_category` = product_shop.`id_category_default`
            AND cl.id_lang=' . (int) $id_lang . Shop::addSqlRestrictionOnLang('cl') . '
            WHERE w.`id_customer` = ' . (int) ($id_customer) . '
            AND pl.`id_lang` = ' . (int) ($id_lang) . '
            AND wp.`id_wishlist` = ' . (int) ($id_wishlist) .
            (empty($id_product) === false ? ' AND wp.`id_product` = ' . (int) ($id_product) : '') .
            ($quantity == true ? ' AND wp.`quantity` != 0' : '') . '
            GROUP BY p.id_product, wp.id_product_attribute'
        );
        if (empty($products) === true or ! sizeof($products)) {
            return array();
        }
        for ($i = 0; $i < sizeof($products); ++$i) {
            if (isset($products[$i]['id_product_attribute']) and
                    Validate::isUnsignedInt($products[$i]['id_product_attribute'])) {
                $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT al.`name` AS attribute_name, pa.`quantity` AS "attribute_quantity"
				FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute` a ON (a.`id_attribute` = pac.`id_attribute`)
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group` ag 
                                ON (ag.`id_attribute_group` = a.`id_attribute_group`)
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_lang` al
                                ON (a.`id_attribute` = al.`id_attribute` AND al.`id_lang` = ' . (int) ($id_lang) . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'attribute_group_lang` agl
                                ON (ag.`id_attribute_group`=agl.`id_attribute_group` 
                                AND agl.`id_lang`='.(int)$id_lang.')
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON 
                                (pac.`id_product_attribute` = pa.`id_product_attribute`)
				' . Shop::addSqlAssociation('product_attribute', 'pa') . '
				WHERE pac.`id_product_attribute` = ' . (int) ($products[$i]['id_product_attribute']));
                $products[$i]['attributes_small'] = '';
                if ($result) {
                    foreach ($result as $row) {
                        $products[$i]['attributes_small'] .= $row['attribute_name'] . ', ';
                    }
                }
                $products[$i]['attributes_small'] = rtrim($products[$i]['attributes_small'], ', ');
                if (isset($result[0])) {
                    $products[$i]['attribute_quantity'] = $result[0]['attribute_quantity'];
                }
            } else {
                $products[$i]['attribute_quantity'] = $products[$i]['product_quantity'];
            }
        }
        return ($products);
    }

    /*
     * Function to check status of marketplace module
     * 
     * @retun bool
     */
    protected function isMarketplaceEnable()
    {
        if (!Configuration::get('KB_MOBILE_APP_MARKETPLACE')) {
            return false;
        }
        return true;
    }
    
    /**
     * Get Currencies data for Home Page
     *
     * @return array currency Data
     */
    public function getCurrenciesDataForHome()
    {
        $currency_data = array();
        $data = array();
        $currencies = Currency::getCurrencies(true);
        foreach ($currencies as $currency) {
            if ((int)$currency->active == 1) {
                $data[] = array(
                    'id_currency' => $currency->id,
                    'name' => $currency->name . '(' . $currency->iso_code . ')',
                    'currency_code' => $currency->iso_code,
                    'currency_symbol' => $currency->sign,
                );
            }
        }
        
        $default_currency = Currency::getCurrencyInstance((int)Configuration::get('PS_CURRENCY_DEFAULT'));
        $currency_data['currency_list'] = $data;
        $currency_data['default_currency_id'] = Configuration::get('PS_CURRENCY_DEFAULT');
        $currency_data['default_currency_code'] = $default_currency->iso_code;
        return $currency_data;
    }
    
    /**
     * Set Wishlist Products to public variable $customer_wishlist
     * to check existance of catalog products in wishlist
     *
     * @param int $customer_id customer id
     */
    public function getCustomerWishlistProducts($customer_id)
    {
        $wishlist_products = array();
        $this->customer_wishlist = $wishlist_products;
        if (Module::isInstalled('blockwishlist') && Module::isEnabled('blockwishlist')) {
            $deafult_wishlist_id = $this->getDefaultWishlist($customer_id);
            if ($deafult_wishlist_id) {
                $wishlist_products = $this->getProductByIdCustomer(
                    $deafult_wishlist_id,
                    $customer_id,
                    $this->context->language->id
                );
                if ($wishlist_products) {
                    foreach ($wishlist_products as $product) {
                        $product_obj = new Product((int) $product['id_product']);
                        $default_id_product_attribute = $product_obj->getWsDefaultCombination();
                        if ($product['id_product_attribute'] == $default_id_product_attribute) {
                            $this->customer_wishlist[] = $product['id_product'];
                        }
                    }
                }
            }
        }
    }
    
    /**
     * To get status of product in wishlist added or not
     *
     * @param int $product_id product id
     * @return bool
     */
    public function isProductHasInWishlist($product_id)
    {
        
        if ($this->customer_wishlist) {
            if (in_array($product_id, $this->customer_wishlist)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    
    /*
     * Function to get the path of front teamplate directory
     * 
     * @param int $code status code
     * @return string
     */
    public function getFrontTemplateDir()
    {
        return _PS_MODULE_DIR_.'kbmobileapp/views/templates/front/';
    }
}

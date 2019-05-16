<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

require_once _PS_MODULE_DIR_ . '/jmango360api/classes/config/ApiConfig.php';
require_once _PS_MODULE_DIR_ . '/jmango360api/jmango360api.php';

class ServiceProvider
{

    /**
     * Provide service class
     * @param  WebserviceRequestCore $request
     * @return JapiService
     */
    public static function provide($request)
    {
        /**
         * @var JapiService $serviceClass
         */
        $module = new Jmango360api();
        $module_name = $module->name;

        $serviceClass = null;

        $requestResource = self::getRequestResource($request->urlSegment);

        switch ($requestResource) {
            case self::match(ApiConfig::CUSTOMER_LOGIN, $requestResource):
                $serviceClass = new LoginService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_LOGOUT, $requestResource):
                $serviceClass = new LogoutService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_REGISTER, $requestResource):
                $serviceClass = new RegisterService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_DETAILS, $requestResource):
                $serviceClass = new CustomerService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_ADDRESS, $requestResource):
                $serviceClass = new CustomerAddressService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_ADDRESS_DETAILS, $requestResource):
                $serviceClass = new CustomerAddressService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_ORDER, $requestResource):
                $serviceClass = new CustomerOrderService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_ORDER_DETAILS, $requestResource):
                $serviceClass = new CustomerOrderDetailsService($module_name);
                break;
            case self::match(ApiConfig::PRODUCT_LIST, $requestResource):
                if (self::isV17()) {
                    $serviceClass = new ProductsService17($module_name);
                } else {
                    $serviceClass = new ProductsService16($module_name);
                }
                break;
            case self::match(ApiConfig::PRODUCT_DETAILS, $requestResource):
                $serviceClass = new ProductDetailService($module_name);
                break;
            case self::match(ApiConfig::PRODUCT_SEARCH, $requestResource):
                if (self::isV17()) {
                    $serviceClass = new ProductsSearchService17($module_name);
                } else {
                    $serviceClass = new ProductsSearchService16($module_name);
                }
                break;
            case self::match(ApiConfig::SMART_APP_BANNER, $requestResource):
                if (strcmp(CustomRequest::getHtmlMethod(), 'POST') == 0) {
                    $serviceClass = new SmartAppBannerSaveService($module_name);
                } elseif (strcmp(CustomRequest::getHtmlMethod(), 'GET') == 0) {
                    $serviceClass = new SmartAppBannerGetService($module_name);
                }
                break;
            case self::match(ApiConfig::PRODUCT_DETAIL_RELOAD, $requestResource):
                $serviceClass = new ProductDetailReloadService($module_name);
                break;
            case self::match(ApiConfig::COMPLETED_ORDERS, $requestResource):
                $serviceClass = new OrderCompleteService($module_name);
                break;
            case self::match(ApiConfig::SEARCH_TERMS, $requestResource):
                $serviceClass = new ProductsSearchTermService($module_name);
                break;
            case self::match(ApiConfig::UPDATE_ORDER_BY_VALUES, $requestResource):
                $serviceClass = new CustomizeOrderByValuesService($module_name);
                break;
            case self::match(ApiConfig::GET_MENU_FACTURER, $requestResource):
                if (self::isV17()) {
                    $serviceClass = new ProductsManufacturerService17($module_name);
                } else {
                    $serviceClass = new ProductsManufacturerService16($module_name);
                }
                break;
            case self::match(ApiConfig::CART, $requestResource):
                $serviceClass = new ShoppingCartService($module_name);
                break;
            case self::match(ApiConfig::CART_DETAILS, $requestResource):
                $serviceClass = new ShoppingCartService($module_name);
                break;
            case self::match(ApiConfig::CART_ITEM, $requestResource):
                $serviceClass = new UpdateItemService($module_name);
                break;
            case self::match(ApiConfig::CART_ITEM_DETAILS, $requestResource):
                $serviceClass = new UpdateItemService($module_name);
                break;
            case self::match(ApiConfig::CART_COUPON, $requestResource):
                $serviceClass = new CouponService($module_name);
                break;
            case self::match(ApiConfig::CART_COUNT, $requestResource):
                $serviceClass = new CartCountService($module_name);
                break;
            case self::match(ApiConfig::ECOMM_SETTINGS, $requestResource):
                $serviceClass = new EcommService($module_name);
                break;
            case self::match(ApiConfig::BACKOFFICE, $requestResource):
                $serviceClass = new BackOfficeService($module_name);
                break;
            case self::match(ApiConfig::PRODUCT_URL, $requestResource):
                $serviceClass = new GetId($module_name);
                break;
            case self::match(ApiConfig::ADDRESS_FORM, $requestResource):
                $serviceClass = new AddressFormService($module_name);
                break;
            case self::match(ApiConfig::REGISTER_FORM, $requestResource):
                $serviceClass = new RegistrationFormService($module_name);
                break;
            case self::match(ApiConfig::CONFIG_PAGE, $requestResource):
                $serviceClass = new ConfigurationInfoService($module_name);
                break;
            case self::match(ApiConfig::PRODUCT_ACCESSORIES, $requestResource):
                $serviceClass = new ProductsAccessoriesService($module_name);
                break;
            case self::match(ApiConfig::LOOKBOOK, $requestResource):
                if (self::isV17()) {
                    $serviceClass = new ProductLookbookService17($module_name);
                } else {
                    $serviceClass = new ProductLookbookService16($module_name);
                }
                break;
            case self::match(ApiConfig::SORT_OPTIONS, $requestResource):
                $serviceClass = new ProductSortOptionsService($module_name);
                break;
            case self::match(ApiConfig::GET_PAYMENTS_CARRIERS, $requestResource):
            case self::match(ApiConfig::SET_EXCLUDE_PAYMENTS_CARRIERS, $requestResource):
                $serviceClass = new PaymentCarrierService($module_name);
                break;
            case self::match(ApiConfig::CHECKOUT_CUSTOM_CSS, $requestResource):
                $serviceClass = new CheckoutSettingsService($module_name);
                break;
            case self::match(ApiConfig::CUSTOMER_FORMATTED_ADDRESS, $requestResource):
                $serviceClass = new CustomerFormattedAddressService($module_name);
                break;
            case self::match(ApiConfig::HOOK_FINDER, $requestResource):
                $serviceClass = new HookService($module_name);
                break;
            case self::match(ApiConfig::VERIFIED_REVIEW, $requestResource):
                $serviceClass = new ProductReviewService($module_name);
                break;
            case self::match(ApiConfig::LOGGER, $requestResource):
                $serviceClass = new LogService($module_name);
                break;
            case self::match(ApiConfig::SUBMIT_ORDER, $requestResource):
                $serviceClass = new CreateBraintreeOrderService($module_name);
                break;
            case self::match(ApiConfig::UPDATE_ORDER, $requestResource):
                $serviceClass = new UpdateBraintreeOrderService($module_name);
                break;
        }
        return $serviceClass;
    }

    /**
     * get request resource
     *
     * @param  array $urlSegment
     * @return array
     */
    protected static function getRequestResource($urlSegment)
    {
        //example format:
        //- japi/rest/products
        //- japi/rest/product/1

        $segmentLength = count($urlSegment);
        $result = array();

        //ignore japi/rest, so start index = 2
        $startIndex = 2;

        for ($i = $startIndex; $i < $segmentLength; $i++) {
            $value = $urlSegment[$i];
            if (!empty($value)) {
                array_push($result, $value);
            }
        }
        return $result;
    }

    /**
     * Check if request resource match with a api
     *
     * @param  string $apiConfig
     * @param array $requestResource
     * @return bool
     */
    protected static function match($apiConfig, $requestResource)
    {
        $apiConfigResource = explode('/', $apiConfig);
        $match = false;

        if (count($apiConfigResource) == count($requestResource)) {
            $count = count($apiConfigResource);
            for ($i = 0; $i < $count; $i++) {
                $apiValue = $apiConfigResource[$i];
                $requestValue = $requestResource[$i];
                if ($apiValue == $requestValue
                    || self::isValidRequestResource($apiValue, $requestValue)) {
                    $match = true;
                } else {
                    $match = false;
                    break;
                }
            }
        }
        return $match;
    }

    /**
     * Compare api format and request value from url
     *
     * @param string $apiValue
     * @param string $requestValue
     * @return bool
     */
    protected static function isValidRequestResource($apiValue, $requestValue)
    {
        $result = preg_replace('/\{.*?\}/', $requestValue, $apiValue);
        return $result == $requestValue;
    }

    /**
     * Check version => 1.7
     */
    public static function isV17()
    {
        return version_compare(_PS_VERSION_, '1.7', '>=');
    }
}

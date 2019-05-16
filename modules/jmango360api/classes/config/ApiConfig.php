<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ApiConfig
{
    //Customers
    const CUSTOMER_LOGIN = 'accounts/login';
    const CUSTOMER_LOGOUT = 'accounts/{customer_id}/logout';

    const CUSTOMER_REGISTER = 'accounts/register';
    const CUSTOMER_DETAILS = 'accounts/{customer_id}';

    const CUSTOMER_ADDRESS = 'accounts/{customer_id}/addresses';
    const CUSTOMER_FORMATTED_ADDRESS = 'accounts/{customer_id}/formattedAddresses';
    const CUSTOMER_ADDRESS_DETAILS = 'accounts/{customer_id}/addresses/{address_id}';

    const CUSTOMER_ORDER = 'accounts/{customer_id}/orders';
    const CUSTOMER_ORDER_DETAILS = 'accounts/{customer_id}/orders/{order_id}';

    //Products
    const PRODUCT_LIST = 'products';

    const PRODUCT_DETAILS = 'product';

    const PRODUCT_URL = 'product_url';

    const PRODUCT_SEARCH = 'search';

    const UPDATE_ORDER_BY_VALUES = 'jmOrderByValues';

    //Smart app banner
    const SMART_APP_BANNER = 'smartAppBanner';

    const PRODUCT_DETAIL_RELOAD = 'product/reload';

    const COMPLETED_ORDERS = 'getOrders';

    const SEARCH_TERMS = 'search_terms';

    const GET_MENU_FACTURER = 'manufacturers/{id_manufacturer}';

    const CART = 'carts';
    const CART_DETAILS = 'carts/{id_cart}';

    const CART_COUPON = 'carts/{id_cart}/coupon';

    const CART_ITEM = 'carts/{id_cart}/items';
    const CART_ITEM_DETAILS = 'carts/{id_cart}/items/{id_item}';

    const CART_COUNT = 'carts/{id_cart}/count';

    const CART_CONFIG = 'carts/config';

    const ECOMM_SETTINGS = 'ecommerceSettings';

    const BACKOFFICE = 'backoffice';

    //forms
    const REGISTER_FORM = 'form/register';
    const ADDRESS_FORM = 'form/address';

    //configuration Page
    const CONFIG_PAGE = 'app_config';

    const PRODUCT_ACCESSORIES = 'product/related';

    const LOOKBOOK = 'lookbook';

    const SORT_OPTIONS = 'sortOptions';

    const GET_PAYMENTS_CARRIERS = 'getPaymentsCarriers';
    const SET_EXCLUDE_PAYMENTS_CARRIERS = 'setExcludePaymentsCarriers';

    const CHECKOUT_CUSTOM_CSS = 'checkoutCustomCss';

    const HOOK_FINDER = 'hookByName';

    //verified review
    const VERIFIED_REVIEW = 'product/verifiedReview';

    const LOGGER = 'log';

    //braintree
    const SUBMIT_ORDER = 'submitOrder';
    const UPDATE_ORDER = 'updateOrder';
}

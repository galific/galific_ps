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
 *
 * Description
 *
 * API to add product into customer wishlist
 */

require_once 'AppCore.php';

class AppAddToWishlist extends AppCore
{
    private $customer = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        if ($this->checkModule()) {
            if ($this->validateCustomerEmail()) {
                $product_id = Tools::getValue('product_id', 0);
                $product = new Product((int) $product_id);
                if (!validate::isLoadedObject($product)) {
                    $this->content['status'] = "failure";
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Unable to load product'),
                        'AppAddToWishlist'
                    );
                    $this->writeLog('Unable to load product');
                } else {
                        $this->addProductToWishlist($product);
                }
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Check whether blockwishlist module is installed and enabled
     *
     * @return bool
     */
    public function checkModule()
    {
        $module_name = 'blockwishlist';
        if (!Module::isInstalled($module_name) || !Module::isEnabled($module_name)) {
            $this->content['status'] = "failure";
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Wishlist module is either inactive or not installed.'),
                'AppAddToWishlist'
            );
            $this->writeLog('Wishlist module is either inactive or not installed.');
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate customer i.e email is valid or not or customer with provided email address is exist or not
     *
     * @return bool
     */
    public function validateCustomerEmail()
    {
        $email = Tools::getValue('email', '');
        if ($email && Validate::isEmail($email)) {
            if (Customer::customerExists(strip_tags($email))) {
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email);
                $this->customer = new Customer($customer_tmp->id);
                return true;
            } else {
                $this->content['status'] = "failure";
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer with this email is not exist.'),
                    'AppAddToWishlist'
                );
                $this->writeLog('Customer with this email is not exist.');
                return false;
            }
        } else {
            $this->content['status'] = "failure";
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is missing or invalid.'),
                'AppAddToWishlist'
            );
            $this->writeLog('Email address is missing or invalid.');
            return false;
        }
    }

    /**
     * Add product to default wishlist
     *
     * @param object $product object of product
     */
    public function addProductToWishlist($product)
    {
        require_once(_PS_MODULE_DIR_ . 'blockwishlist/WishList.php');
        require_once(_PS_MODULE_DIR_ . 'blockwishlist/blockwishlist.php');
        $deafult_wishlist_id = $this->getDefaultWishlist($this->customer->id);
        if (empty($deafult_wishlist_id) || !$deafult_wishlist_id) {
            $wishlist = new WishList();
            $wishlist->id_shop = $this->context->shop->id;
            $wishlist->id_shop_group = $this->context->shop->id_shop_group;
            $wishlist->default = 1;

            $mod_wishlist = new BlockWishList();
            $wishlist->name = $mod_wishlist->default_wishlist_name;
            $wishlist->id_customer = (int) $this->customer->id;
            list($us, $s) = explode(' ', microtime());
            srand($s * $us);
            $wishlist->token = Tools::strtoupper(
                Tools::substr(
                    sha1(uniqid(rand(), true) . _COOKIE_KEY_ . $this->customer->id),
                    0,
                    16
                )
            );
            $wishlist->add();
            $this->context->cookie->id_wishlist = (int) $wishlist->id;
        } else {
            $this->context->cookie->id_wishlist = (int) $deafult_wishlist_id;
        }
        $id_product_attribute = $product->getWsDefaultCombination();
        if ($product->minimal_quantity < 1) {
            WishList::addProduct(
                $this->context->cookie->id_wishlist,
                $this->customer->id,
                $product->id,
                $id_product_attribute,
                1
            );
        } else {
            WishList::addProduct(
                $this->context->cookie->id_wishlist,
                $this->customer->id,
                $product->id,
                $id_product_attribute,
                $product->minimal_quantity
            );
        }
        $this->content['status'] = "success";
        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Product added to wishlist'),
            'AppAddToWishlist'
        );
        $wishlist_products = $this->getProductByIdCustomer(
            $this->context->cookie->id_wishlist,
            $this->customer->id,
            $this->context->language->id
        );
        $this->content['wishlist_count'] = count($wishlist_products);
        $this->writeLog('Product added to wishlist');
    }
}

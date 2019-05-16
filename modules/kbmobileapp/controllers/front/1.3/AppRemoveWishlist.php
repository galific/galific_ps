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
 * API to remove product from customer wishlist
 */

require_once 'AppCore.php';

class AppRemoveWishlist extends AppCore
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
                        'AppRemoveWishlist'
                    );
                    $this->writeLog('Unable to load product');
                } else {
                        $this->removeProductFromWishlist($product);
                }
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Check whether blockwishlist module is intalled and enabled
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
                'AppRemoveWishlist'
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
                    'AppRemoveWishlist'
                );
                $this->writeLog('Customer with this email is not exist.');
                return false;
            }
        } else {
            $this->content['status'] = "failure";
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is missing or invalid.'),
                'AppRemoveWishlist'
            );
            $this->writeLog('Email address is missing or invalid.');
            return false;
        }
    }


    /**
     * Remove product from customer wishlist
     *
     * @param Object Product $product product object
     */
    public function removeProductFromWishlist($product)
    {
        if (isset($this->context->cookie->id_wishlist) && $this->context->cookie->id_wishlist != '') {
            $wishlist_id = $this->context->cookie->id_wishlist;
        } else {
            $wishlist_id = $this->getDefaultWishlist($this->customer->id);
        }
        require_once(_PS_MODULE_DIR_ . 'blockwishlist/WishList.php');
        require_once(_PS_MODULE_DIR_ . 'blockwishlist/blockwishlist.php');
        $id_product_attribute = Tools::getValue('id_product_attribute', 0);
        if (!$id_product_attribute) {
            $id_product_attribute = $product->getWsDefaultCombination();
        }
        WishList::removeProduct($wishlist_id, $this->customer->id, $product->id, $id_product_attribute);
        $this->content['status'] = "success";
        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
            Tools::getValue('iso_code', false),
            $this->l('Product removed from wishlist'),
            'AppRemoveWishlist'
        );
        $wishlist_products = $this->getProductByIdCustomer(
            $wishlist_id,
            $this->customer->id,
            $this->context->language->id
        );
        $this->content['wishlist_count'] = count($wishlist_products);
        $this->writeLog('Product removed from wishlist');
    }
}

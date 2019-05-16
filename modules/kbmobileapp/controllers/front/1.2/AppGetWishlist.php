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
 * API to get wishlist products of customer
 */

require_once 'AppCore.php';

class AppGetWishlist extends AppCore
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
                $this->getWishlistProducts();
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Check whether blockwishlist module installed and enabled
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
                'AppGetWishlist'
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
                    'AppGetWishlist'
                );
                $this->writeLog('Customer with this email is not exist.');
                return false;
            }
        } else {
            $this->content['status'] = "failure";
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is missing or invalid.'),
                'AppGetWishlist'
            );
            $this->writeLog('Email address is missing or invalid.');
            return false;
        }
    }

    /**
     * Get product list of default wishlist of customer
     */
    public function getWishlistProducts()
    {
        $deafult_wishlist_id = $this->getDefaultWishlist($this->customer->id);
        if ($deafult_wishlist_id) {
            $wishlist_products = $this->getProductByIdCustomer(
                $deafult_wishlist_id,
                $this->customer->id,
                $this->context->language->id
            );
            if (!$wishlist_products) {
                $this->content['status'] = "success";
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('No product found in wishlist'),
                    'AppGetWishlist'
                );
                $this->writeLog('No product found in wishlist');
                $this->content['wishlist_products'] = array();
                return;
            }
            $products_array = array();
            if ($wishlist_products) {
                $index = 0;
                $priceDisplay = Product::getTaxCalculationMethod(0); //(int)$this->context->cookie->id_customer
                foreach ($wishlist_products as $product) {
                    $product_obj = new Product((int) $product['id_product']);
                    $products_array[$index] = array(
                        'product_id' => $product['id_product'],
                        'title' => $product['name'],
                        'is_gift_product' => "0",
                        'stock' => true,
                        'id_product_attribute' => $product['id_product_attribute']
                    );
                    $new_p_bit = (isset($product_obj->new) && $product_obj->new == 1) ? "1" : "0";
                    $products_array[$index]['available_for_order'] = $product_obj->available_for_order;
                    $products_array[$index]['show_price'] = $product_obj->show_price;
                    $products_array[$index]['new_products'] = $new_p_bit;
                    $products_array[$index]['on_sale_products'] = $product_obj->on_sale;
                    $products_array[$index]['quantity'] = $product['quantity'];
                    $products_array[$index]['minimal_quantity'] = $product_obj->minimal_quantity;
                    if ($product_obj->out_of_stock == 1) {
                        $products_array[$index]['allow_out_of_stock'] = "1";
                    } elseif ($product_obj->out_of_stock == 0) {
                        $products_array[$index]['allow_out_of_stock'] = "0";
                    } elseif ($product_obj->out_of_stock == 2) {
                        $out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
                        if ($out_of_stock == 1) {
                            $products_array[$index]['allow_out_of_stock'] = "1";
                        } else {
                            $products_array[$index]['allow_out_of_stock'] = "0";
                        }
                    }
                    if (!$priceDisplay || $priceDisplay == 2) {
                        $price = $product_obj->getPrice(true, (int) $product['id_product_attribute']);
                        $price_without_reduction = $product_obj->getPriceWithoutReduct(false);
                    } else {
                        $price = $product_obj->getPrice(false, (int) $product['id_product_attribute']);
                        $price_without_reduction = $product_obj->getPriceWithoutReduct(true);
                    }
                    if ($priceDisplay >= 0 && $priceDisplay <= 2) {
                        if ($price_without_reduction <= 0 || !$product_obj->specificPrice) {
                            $products_array[$index]['price'] = $this->formatPrice($price);
                            $products_array[$index]['discount_price'] = '';
                            $products_array[$index]['discount_percentage'] = '';
                        } else {
                            if ($product_obj->specificPrice) {
                                $specific_price = $product_obj->specificPrice['reduction_type'];
                                if ($specific_price == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                                    $temp_price = $product_obj->specificPrice['reduction'] * 100;
                                    $products_array[$index]['discount_percentage'] = $temp_price;
                                    unset($temp_price);
                                }
                            } elseif ($product_obj->specificPrice
                                && $product_obj->specificPrice['reduction_type'] == 'amount'
                                && $product_obj->specificPrice['reduction'] > 0) {
                                $temp_price = (float) ($product_obj->specificPrice['reduction'] * 100);
                                $percent = (float) ($temp_price/ $price_without_reduction);
                                $products_array[$index]['discount_percentage'] = Tools::ps_round($percent);
                            }
                            $products_array[$index]['price'] = $this->formatPrice($price_without_reduction);
                            $products_array[$index]['discount_price'] = $this->formatPrice($price);
                        }
                    } else {
                        $products_array[$index]['price'] = '';
                        $products_array[$index]['discount_price'] = '';
                        $products_array[$index]['discount_percentage'] = '';
                    }
                    $image_id = Product::getCover($product['id_product']);
                    if ($image_id) {
                        $products_array[$index]['images'] = $this->context->link->getImageLink(
                            $product['link_rewrite'],
                            $image_id['id_image'],
                            $this->getImageType('large')
                        );
                    } else {
                        $products_array[$index]['images'] = $this->context->link->getImageLink(
                            $product['link_rewrite'],
                            $this->context->language->iso_code . '-default',
                            $this->getImageType('large')
                        );
                    }
                    $products_array[$index]['product_items'] = array(
                        array(
                            'name' => 'Quantity',
                            'value' => $product['quantity']
                        ),
                        array(
                            'name' => 'SKU',
                            'value' => $product_obj->reference
                        ),
                        array(
                            'name' => 'Attributes',
                            'value' => $product['attributes_small']
                        )
                    );
                    $products_array[$index]['customizable_items'] = array();
                    $index++;
                }
            }
            $this->content['status'] = "success";
            $this->content['message'] = "";
            $this->content['wishlist_products'] = $products_array;
        } else {
            $this->content['status'] = "success";
            $this->content['message'] = "";
            $this->writeLog('No wishlist found.');
            $this->content['wishlist_products'] = array();
        }
    }
}

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
 * API to add product into cart call from product page of APP
 */

require_once 'AppCore.php';

class AppAddToCart extends AppCore
{
    private $product = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $product_data = Tools::getValue('cart_products', Tools::jsonEncode(array()));
        $product_data = Tools::jsonDecode($product_data);
        if (empty($product_data)) {
            $this->content['cart_add_result'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Product data is missing'),
                    'AppAddToCart'
                )
            );
            $this->writeLog('Product data is missing.');
        } else {
            $id_product = $product_data->cart_products[0]->product_id;
            if (empty($id_product)) {
                $id_product = 0;
            }
            $this->product = new Product(
                $id_product,
                true,
                $this->context->language->id,
                $this->context->shop->id,
                $this->context
            );
            if (!Validate::isLoadedObject($this->product)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Product not found'),
                    'AppAddToCart'
                );
                $this->writeLog('Product with the provided data is not found.');
            } else {
                $cart_id = Tools::getValue('session_data', '');
                if (empty($cart_id)) {
                    /* Add new cart to save product data */
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart->add();
                    if ($this->context->cart->id) {
                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    }
                } else {
                    $this->context->cart = new Cart($cart_id, false, null, null, $this->context);
                    if (!Validate::isLoadedObject($this->context->cart)) {
                        $this->context->cart->id_currency = $this->context->currency->id;
                        $this->context->cart->add();
                    }
                    $this->context->cart->id_currency = $this->context->currency->id;
                    if ($this->context->cart->id) {
                        $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    }
                }
                if ($this->product->customizable) {
                    $post_customizable_data = $product_data->customization_details;
                    if (!empty($post_customizable_data)) {
                        if (!$this->saveCustomizedData($post_customizable_data)) {
                            $this->content['status'] = 'failure';
                            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('Invalid Message'),
                                'AppAddToCart'
                            );
                            $this->writeLog('Invalid message in customization field');
                        }
                    }
                }
                $qty = $product_data->cart_products[0]->minimal_quantity;
                $id_product_attribute = $product_data->cart_products[0]->id_product_attribute;
                $this->addProduct($id_product, $id_product_attribute, $qty);
                $this->content['session_data'] = (int)$this->context->cart->id;
            }
        }

        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Save customized product data
     *
     * @param array $post_customizable_data product customizable data
     * @return bool
     */
    public function saveCustomizedData($post_customizable_data)
    {
        $error = 0;
        if ($field_ids = $this->product->getCustomizationFieldIds()) {
            $authorized_text_fields = array();
            foreach ($field_ids as $field_id) {
                if ($field_id['type'] == Product::CUSTOMIZE_TEXTFIELD) {
                    $fid = (int) $field_id['id_customization_field'];
                    $authorized_text_fields[$fid] = 'textField' . $fid;
                }
            }

            $indexes = array_flip($authorized_text_fields);
            foreach ($post_customizable_data as $data) {
                if (in_array('textField' . (int) $data->id_customization_field, $authorized_text_fields)
                    && $data->text_value != '') {
                    if (!Validate::isMessage($data->value)) {
                        $error = 1;
                    } else {
                        $this->context->cart->addTextFieldToProduct(
                            $this->product->id,
                            $indexes['textField' . (int) $data->id_customization_field],
                            Product::CUSTOMIZE_TEXTFIELD,
                            $data->text_value
                        );
                    }
                } elseif (in_array('textField' . (int) $data->id_customization_field, $authorized_text_fields)
                    && $data->text_value == '') {
                    $this->context->cart->deleteCustomizationToProduct(
                        (int) $this->product->id,
                        $indexes['textField' . (int) $data->id_customization_field]
                    );
                }
            }
        }
        if ($error == 1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Add product into cart with provided quantity
     *
     * @param int $id_product product id
     * @param int $id_product_attribute product attruibute id
     * @param int $qty product quantity
     */
    public function addProduct($id_product, $id_product_attribute, $qty)
    {
        if ($qty == 0) {
            $qty = 1;
        }
        if (!empty($id_product_attribute)) {
            $minimal_quantity = (int) Attribute::getAttributeMinimalQty($id_product_attribute);
        } else {
            $minimal_quantity = (int) $this->product->minimal_quantity;
        }
        if ($minimal_quantity == 0) {
            $minimal_quantity = 1;
        }
        if ((int) $qty < $minimal_quantity) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Add minimum Quantity of product'),
                'AppAddToCart'
            );
            $this->writeLog('Error in adding product into cart');
        } else {
            $update_status = $this->context->cart->updateQty($qty, $id_product, $id_product_attribute);
            if (!$update_status) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Unable to add product into the cart'),
                    'AppAddToCart'
                );
                $this->writeLog('Error in adding product into cart');
            } else {
                $this->content['status'] = 'success';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Product successfully Added'),
                    'AppAddToCart'
                );
            }
        }
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 3/22/18
 * Time: 9:24 AM
 * @author kien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class UpdateItemService extends SimpleCartService
{
    public function doExecute()
    {

        $this->id_cart = $this->getRequestResourceId();

        if ($this->isCartFinished($this->id_cart)) {
            $this->throwCartFinishedException();
            return;
        }

        if ($this->isPostMethod()) {
            //add product to cart

            $this->mode = self::ADD_ITEM;
            $this->initFromRequestBody();
            $this->processChangeProductInCart();
            $this->makeCartResponse($this->getRequestResourceId());
        } elseif ($this->isPutMethod()) {
            //update product in cart

            $this->initFromRequestBody();
            $ids = CartUtils::revertShoppingCartItemId($this->ps_item_id);
            $old_id_product_attribute = 0;

            if ($ids && count($ids) == 2) {
                $this->id_product = $ids[0];
                $old_id_product_attribute = $ids[1] != null ? $ids[1] : 0;
            }

            if ($this->isOnlyUpdateQuantity($old_id_product_attribute)) {
                $this->mode = self::UPDATE_ITEM;
                $this->id_product_attribute = $old_id_product_attribute;
                $this->processChangeProductInCart();
            } else {
                // add item with new options to cart
                $this->mode = self::ADD_ITEM;
                $this->processChangeProductInCart();

                if ($this->errors == null) {
                    // If item wiht new selection is successfull added to cart, delete old item
                    // If there's any error when adding item with new selection, existing items in cart will not change
                    // and error will be returned.

                    //set id product attribute to delete
                    $this->id_product_attribute = $old_id_product_attribute;

                    $this->processDeleteProductInCart();
                }
            }

            $this->makeCartResponse($this->getRequestResourceId());
        } elseif ($this->isDeleteMethod()) {
            //delete item in cart
            $cartItemId = $this->getRequestResourceId('5');

            $ids = CartUtils::revertShoppingCartItemId($cartItemId);
            if ($ids && count($ids) == 2) {
                $this->id_product = $ids[0];
                $this->id_product_attribute = $ids[1];
            }
            $customization_id = $this->getRequestValue('customization_id');
            $this->customization_id = $customization_id ? $customization_id : 0;
            $this->processDeleteProductInCart();
            $this->makeCartResponse($this->getRequestResourceId());
        }
    }

    /**
     * This process delete a product from the cart
     */
    protected function processDeleteProductInCart()
    {
        $customization_product = Db::getInstance()->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'customization`
		WHERE `id_cart` = ' . (int)$this->context->cart->id . '
		AND `id_product` = ' . (int)$this->id_product . '
		AND `id_customization` != ' . (int)$this->customization_id);

        if (count($customization_product)) {
            $product = new Product((int)$this->id_product);
            if ($this->id_product_attribute > 0) {
                $minimal_quantity = (int)Attribute::getAttributeMinimalQty($this->id_product_attribute);
            } else {
                $minimal_quantity = (int)$product->minimal_quantity;
            }

            $total_quantity = 0;
            foreach ($customization_product as $custom) {
                $total_quantity += $custom['quantity'];
            }

            if ($total_quantity < $minimal_quantity) {
                $this->errors[] = new JmError(
                    500,
                    $this->trans(
                        'You must add %d minimum quantity',
                        array(!Tools::getValue('ajax'),
                            $minimal_quantity),
                        'Shop.Notifications.Error'
                    )
                );
                return false;
            }
        }

        $data = array(
            'id_cart' => (int)$this->context->cart->id,
            'id_product' => (int)$this->id_product,
            'id_product_attribute' => (int)$this->id_product_attribute,
            'customization_id' => (int)$this->customization_id,
            'id_address_delivery' => (int)$this->id_address_delivery
        );

        Hook::exec('actionObjectProductInCartDeleteBefore', $data, null, true);

        if ($this->context->cart->deleteProduct(
            $this->id_product,
            $this->id_product_attribute,
            $this->customization_id,
            $this->id_address_delivery
        )) {
            Hook::exec('actionObjectProductInCartDeleteAfter', $data);

            if (!Cart::getNbProducts((int)$this->context->cart->id)) {
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->gift = 0;
                $this->context->cart->gift_message = '';
                $this->context->cart->update();
            }
        }

        CartRule::autoRemoveFromCart();
        CartRule::autoAddToCart();
    }

    /**
     * Only update quantity in cases:
     * 1. no group data received
     * 2. old selection = new selection
     * @param $old_id_product
     * @param $old_id_product_attribute
     * @return bool
     */
    protected function isOnlyUpdateQuantity($old_id_product_attribute)
    {
        if (!$this->group) {
            //mobile doesn't send product selection
            return true;
        } else {
            $this->id_product_attribute =
                (int)$this->getIdProductAttributesByIdAttributes($this->id_product, $this->group);
            if (0 != $old_id_product_attribute
                && $this->id_product_attribute == $old_id_product_attribute) {
                //same product selection
                return true;
            }
        }
        return false;
    }
}

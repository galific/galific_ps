<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ShoppingCartService extends SimpleCartService
{
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            //shopping cart is prepared in base service
            $cart = $this->context->cart;
            if ($cart && $cart->id) {
                $id_cart = $cart->id;
                if ($this->isCartFinished($id_cart)) {
                    //return empty cart
                    $cartResponse = new GetCartResponse();
                    $cartResponse->cart = new Cart();
                    $cartResponse->cart->id = 0;
                    $cartResponse->cart->products = array();

                    $this->response = $cartResponse;
                } else {
                    $this->makeCartResponse($id_cart);
                }
            } else {
                //response empty cart
                $cartResponse = new GetCartResponse();
                $cartResponse->cart = new Cart();
                $cartResponse->cart->id = 0;
                $cartResponse->cart->products = array();

                $this->response = $cartResponse;
            }
        } elseif ($this->isPostMethod()) {
            //add item to cart when no cart created
            $cart = $this->context->cart;

            if ($cart && $cart->id) {
                if ($this->isCartFinished($cart->id)) {
                    //$cart = $this->createEmptyCart();
                    //$this->context->cart = $cart;

                    $this->throwCartFinishedException();
                    return;
                }
            } else {
                $cart = $this->createEmptyCart();
                $this->context->cart = $cart;
            }

            if ($cart && $cart->id) {
                //add product to cart
                $id_cart = $cart->id;
                $this->initFromRequestBody();
                $this->processChangeProductInCart(false);
                $this->makeCartResponse($id_cart);
            } else {
                $this->throwServiceException(500, 'Internal server error', 'Can not create cart');
            }
        } elseif ($this->isPutMethod()) {
            //update shopping cart when customer logged in
            $this->initFromRequestBody();

            $this->context->cart = new Cart($this->id_cart, $this->context->language->id);

            $customer = new CustomerCore((int)$this->id_customer);

            // Update customer info into cart
            $this->context->cart->id_customer = (int)$this->id_customer;
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($this->id_customer);
            $this->context->cart->id_address_invoice = (int)$this->context->cart->id_address_delivery;
            $this->context->cart->id_guest = $customer->id_guest;
            $this->context->cart->secure_key = $customer->secure_key;

            //Update new cart rules
            CartRule::autoRemoveFromCart();
            CartRule::autoAddToCart();

            //Save updated cart to database
            $this->context->cart->update();

            $cartResult = CartUtils::presentShoppingCart(
                $this->context->cart,
                $this->context->language->id,
                $this->context->shop->id,
                $this->context,
                $this->module_name
            );

            $this->response = new GetCartResponse();
            $this->response->cart = $cartResult;
        }
    }

    /**
     * @param mixed $id_product_attribute
     */
    public function setIdProductAttribute($id_product_attribute)
    {
        $this->id_product_attribute = $id_product_attribute;
    }

    /**
     * @param mixed $id_product
     */
    public function setIdProduct($id_product)
    {
        $this->id_product = $id_product;
    }

    /**
     * @param mixed $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @param null $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @param mixed $customization_id
     */
    public function setCustomizationId($customization_id)
    {
        $this->customization_id = $customization_id;
    }

    /**
     * @param mixed $id_cart
     */
    public function setIdCart($id_cart)
    {
        $this->id_cart = $id_cart;
        $this->context->cart = new Cart($this->id_cart, $this->context->language->id);
    }

    /**
     * @param mixed $op
     */
    public function setOp($op)
    {
        $this->op = $op;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
}

<?php
/**
 *
 * @author Jmango
 * @copyright opyright 2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class CartConfigService extends BaseService
{
    public function doExecute()
    {
        $cart = null;
        // Creating new empty cart
        if ($this->isGetMethod()) {
            $this->context->cart=new Cart(null, $this->context->language->id);
            $this->context->cart->id_currency = (int)$this->context->currency->id;
            $this->context->cart->id_shop = $this->context->shop->id;
            $this->context->cart->id_shop_group = (int)Shop::getGroupFromShop($this->context->shop->id);
            // Cart created when user logged in
            if ($this->context->customer->id) {
                $this->context->cart->id_customer = (int)$this->context->customer->id;
                $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($this->context->customer->id);
                $this->context->cart->id_address_invoice = (int)$this->context->cart->id_address_delivery;
            } else {
                // Cart created for guest.
                // Creating new guest and add to database
                $guest = new Guest();
                $guest->add();
                $this->context->cart->id_guest = $guest->id;
                $this->context->cart->id_address_delivery = 0;
                $this->context->cart->id_address_invoice = 0;
            }
            // Add new cart to database
            $this->context->cart->add();
            $cart['id'] = $this->context->cart->id;
            $this->response = $cart;
        }
        // Update informations on existing cart
        // E.g: when user login, need to update id_customer, addresses to cart
        if ($this->isPostMethod()) {
            $request_object = json_decode($this->request->getRequestBody());
            $id_cart = $request_object->id_cart;
            $id_customer = $request_object->id_customer;

            $this->context->cart = new Cart($id_cart, $this->context->language->id);
            // Update customer info into cart
            $this->context->cart->id_customer = (int)$id_customer;
            $this->context->cart->id_address_delivery = (int)Address::getFirstCustomerAddressId($id_customer);
            $this->context->cart->id_address_invoice = (int)$this->context->cart->id_address_delivery;
            //Update new cart rules
            CartRule::autoRemoveFromCart();
            CartRule::autoAddToCart();
            //Save updated cart to database
            $this->context->cart->update();
            $cart['id'] = $this->context->cart->id;
            $this->response->cart->id = $cart;
        }
    }
}

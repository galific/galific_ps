<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CustomerOrderDetailsService extends BaseService
{
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            //get customer orders

            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);

            if ($customer && $customer->id) {
                $this->context->customer = $customer;
                $order_id_index = 5;
                $order_id = $this->getRequestResourceId($order_id_index);
                $language_id = $this->context->language->id;
                $order = new OrderCore($order_id, $language_id);

                if ($order && $order->id) {
                    $this->response = new CustomerOrderDetailsResponse();
                    $products = $order->getCartProducts();
                    $jmProducts = array();

                    if ($products && count($products) > 0) {
                        foreach ($products as $p) {
                            //get product id with key = product_id
                            $product_id = $p['product_id'];

                            //load product core
                            $product_core = new ProductCore(
                                $product_id,
                                true,
                                $language_id,
                                $this->context->shop->id,
                                $this->context
                            );

                            if ($product_core && $product_core->id) {
                                //get product url by product core
                                $productUrl = $this->context->link->getProductLink($product_core);

                                //convert to JmProduct object
                                $jmProduct = ProductDataTransform::productDetails($product_core);

                                //add  product url for sharing on mobile
                                $jmProduct->product_url = $productUrl;
                                $jmProduct->quantity = $p['cart_quantity'];
                                $jmProduct->unit_price_tax_incl = $p['unit_price_tax_incl'];
                                $jmProduct->unit_price_tax_excl = $p['unit_price_tax_excl'];
                                $jmProduct->selectedAttributes = $p['product_attribute_id'] !='0' ? $this->assignAttribute($product_core, $language_id, $p['product_attribute_id']) : null;
                                $jmProduct->id_product_attribute = $p['product_attribute_id'];
                                array_push($jmProducts, $jmProduct);
                            }
                        }
                    }
                    //dynamic add "products" to response
                    $order->products = $jmProducts;
                    $order->customer_group_without_tax = (strcmp(Group::getPriceDisplayMethod(Customer::getDefaultGroupId($customer_id)), "1") == 0 ? true : false);
                    $this->response->order = $order;
                } else {
                    $this->response = new JmResponse();
                    $this->response->errors = array('Order doest not exits!');
                }
            } else {
                $this->response = new JmResponse();
                $this->response->errors = array('Customer doest not exits!');
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }

    public function assignAttribute($product_core, $language_id, $product_attribute_id)
    {
        $attributes = array();
        $attrs = $product_core->getAttributeCombinationsById($product_attribute_id, $language_id);
        foreach ($attrs as $attr) {
            $attributes[] = array('key' => $attr['group_name'], 'value' => $attr['attribute_name']);
        }
        return $attributes;
    }
}

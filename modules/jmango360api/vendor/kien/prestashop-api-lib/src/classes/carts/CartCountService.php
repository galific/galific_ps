<?php
/**
 * Created by PhpStorm.
 * User: bangle
 * Date: 21/03/2018
 * Time: 17:39
 * @author bangle
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CartCountService extends SimpleCartService
{

    public function doExecute()
    {
        $count = 0;
        $cart = $this->context->cart;

        if ($cart && $cart->id) {
            if ($this->isCartFinished($cart->id)) {
                $count = 0;
            } else {
                $count = $cart->nbProducts();
            }
        }

        $this->response = new GetCartCountResponse();
        $this->response->count = $count;
    }
}

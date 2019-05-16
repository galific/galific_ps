<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CouponService extends SimpleCartService
{
    public function doExecute()
    {
        $id_cart = $this->getRequestResourceId();

        if ($this->isCartFinished($id_cart)) {
            $this->throwCartFinishedException();
            return;
        }

        if ($this->isPostMethod()) {
            $requestData = json_decode($this->getRequestBody());
            $code = $requestData->code;

            $errors = $this->addCoupon($code);
            if ($errors) {
                $this->response = new JmResponse();
                $this->response->errors = $errors;
            } else {
                $this->makeCartResponse($id_cart);
            }
        } elseif ($this->isDeleteMethod()) {
            $this->removeCoupon();
            $this->makeCartResponse($id_cart);
        } else {
            $this->throwUnsupportedMethodException();
        }
    }

    protected function addCoupon($code)
    {
        $errors = array();
        if (CartRule::isFeatureActive()) {
            if (!$code) {
                array_push($errors, new JmError(
                    500,
                    $this->getTranslation('You must enter a voucher code.', 'coupon-service')
                ));
            } else if (!Validate::isCleanHtml($code)) {
                array_push($errors, new JmError(
                    500,
                    $this->getTranslation('The voucher code is invalid.', 'coupon-service')
                ));
            } else {
                if (($cartRule = new CartRule(CartRule::getIdByCode($code))) && Validate::isLoadedObject($cartRule)) {
                    if ($error = $cartRule->checkValidity($this->context, false, true)) {
                        $error = html_entity_decode($error);
                        array_push($errors, new JmError(500, $this->getTranslation($error, 'coupon-service')));
                    } else {
                        $this->context->cart->addCartRule($cartRule->id);
                    }
                } else {
                    array_push($errors, new JmError(
                        500,
                        $this->getTranslation('This voucher does not exist.', 'coupon-service')
                    ));
                }
            }
        } else {
            array_push($errors, new JmError(
                500,
                $this->getTranslation('Feature voucher code is not active.', 'coupon-service')
            ));
        }

        return $errors;
    }

    protected function removeCoupon()
    {
        $code = $this->getRequestValue('code');

        $id_cart_rule = CartRule::getIdByCode($code);
        if ($id_cart_rule) {
            $this->context->cart->removeCartRule($id_cart_rule);
            CartRule::autoAddToCart($this->context);
        }
    }
}

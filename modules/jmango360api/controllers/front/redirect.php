<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class jmango360apiRedirectModuleFrontController
 */
class Jmango360ApiRedirectModuleFrontController extends ModuleFrontController
{
    public function init()
    {
        /* @var $cookie CookieCore */
        $cookie = Context::getContext()->cookie;

        $key = Tools::getValue('ws_key');
        if (!$key || !WebserviceKey::isKeyActive($key)) {
            header('HTTP/1.1 401 Unauthorized');
            die('401 Unauthorized');
        }

        if (Tools::getValue('id_lang')) {
            $cookie->__set('id_lang', Tools::getValue('id_lang'));
        }

        if (Tools::getValue('id_customer')) {
            $customer = new Customer(Tools::getValue('id_customer'));

            if ($customer->id) {
                $cookie->__set('id_customer', $customer->id);
                $cookie->__set('logged', 1);
            }
        }

        $cartId = Tools::getValue('cart_id');
        /* @var $cart CartCore */
        $cart = new Cart($cartId);
        if ($cart->id) {
            $cookie->__set('id_currency', $cart->id_currency);
            $cookie->__set('id_cart', $cart->id);

            if ($cart->id_customer) {
                /* @var $customer CustomerCore */
                $customer = new Customer($cart->id_customer);
                if ($customer->id) {
                    $cookie->__set('id_customer', $customer->id);
                    $cookie->__set('logged', 1);
                }
            }
        } else {
            if ($cartId) {
                header('HTTP/1.1 400 Bad Request (Cart ID not valid)');
                die('400 Bad Request');
            }
        }

        parent::init();

        $url = Tools::getValue('url');
        if ($url) {
            if (strpos($url, 'prestashop-backup2.jmango360.com') !== false || strpos($url, 'noqa.store') !== false) {
                if (Tools::getValue('id_lang') && Tools::getValue('id_lang') == 2) {
                    $url = str_replace('/module/jmango360api/address', '/ar/module/jmango360api/address', $url);
                }
            }
            Tools::redirect($url);
        } else {
            Tools::redirect(
                Context::getContext()->link->getModuleLink('jmango360api', 'orderopc', array(), true)
            );
        }
    }
}

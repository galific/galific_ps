<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class jmango360apiPaymentModuleFrontController
 */

require_once _PS_MODULE_DIR_ . '/jmango360api/vendor/autoload.php';
class Jmango360ApiPaymentModuleFrontController extends ModuleFrontController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if ($this->context->cart->nbProducts()) {
                $this->context->smarty->assign(array(
                    'HOOK_PAYMENT' => Hook::exec('displayPayment'),
                    'content_only' => true
                ));

                $this->setTemplate('payment.tpl');
            } else {
                $this->context->smarty->assign(array(
                    'content_only' => true
                ));
                $this->setTemplate('empty.tpl');
            }
        } else {
            $presentedCart = $this->cart_presenter->present($this->context->cart);
            if (count($presentedCart['products']) <= 0 || $presentedCart['minimalPurchaseRequired']) {
                $this->setTemplate('module:jmango360api/views/templates/front/empty.tpl');
                return;
            }

            $paymentFinder = new PaymentOptionsFinder();
            $conditionsToApproveFinder = new ConditionsToApproveFinder($this->context, $this->context->getTranslator());

            $this->context->smarty->assign(array(
                'payment_options' => $paymentFinder->present(),
                'conditions_to_approve' => $conditionsToApproveFinder->getConditionsToApproveForTemplate(),
                'selected_payment_option' => null,
                'selected_delivery_option' => null,
                'show_final_summary' => false
            ));

            $this->setTemplate('module:jmango360api/views/templates/front/payment-17.tpl');
        }
    }
}

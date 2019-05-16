<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Symfony\Component\Translation\TranslatorInterface;

class JmCheckoutPaymentStep extends JmCheckoutStep
{
    protected $template;

    private $selected_payment_option;

    protected $paymentOptionsFinder;
    protected $conditionsToApproveFinder;
    public $module_name;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        PaymentOptionsFinder $paymentOptionsFinder,
        ConditionsToApproveFinder $conditionsToApproveFinder,
        $module_name
    ) {
        $this->template = 'module:'.$module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/steps/payment.tpl';
        parent::__construct($context, $translator, $checkoutSession, $module_name);
        $this->paymentOptionsFinder = $paymentOptionsFinder;
        $this->conditionsToApproveFinder = $conditionsToApproveFinder;
        $this->module_name = $module_name;
    }

    public function handleRequest(array $requestParams = array())
    {
        if (isset($requestParams['select_payment_option'])) {
            $this->selected_payment_option = $requestParams['select_payment_option'];
        }

        $this->setTitle(
            $this->getTranslator()->trans(
                'Payment',
                array(),
                'Shop.Theme.Checkout'
            )
        );
    }

    public function render(array $extraParams = array())
    {
        $isFree = 0 == (float)$this->getCheckoutSession()->getCart()->getOrderTotal(true, Cart::BOTH);

        $paymentOptions = $this->paymentOptionsFinder->present($isFree);

        /**
         * PS-832: Support exclude payments
         */
        $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
        $exludedPayments = isset($exluded['payments']) ? $exluded['payments'] : array();
        foreach ($paymentOptions as $paymentModule => $payment) {
            // Fix "module_name" is null
            if (count($payment) && empty($payment[0]['module_name'])) {
                $paymentOptions[$paymentModule][0]['module_name'] = $paymentModule;
            }
            if (in_array($paymentModule, $exludedPayments)) {
                unset($paymentOptions[$paymentModule]);
            }
        }

        $conditionsToApprove = $this->conditionsToApproveFinder->getConditionsToApproveForTemplate();

        $deliveryOptions = $this->getCheckoutSession()->getDeliveryOptions();

        $deliveryOptionKey = $this->getCheckoutSession()->getSelectedDeliveryOption();

        if (isset($deliveryOptions[$deliveryOptionKey])) {
            $selectedDeliveryOption = $deliveryOptions[$deliveryOptionKey];
        } else {
            $selectedDeliveryOption = 0;
        }
        unset($selectedDeliveryOption['product_list']);

        $assignedVars = array(
            'is_free' => $isFree,
            'payment_options' => $paymentOptions,
            'conditions_to_approve' => $conditionsToApprove,
            'selected_payment_option' => $this->selected_payment_option,
            'selected_delivery_option' => $selectedDeliveryOption,
            'show_final_summary' => Configuration::get('PS_FINAL_SUMMARY_ENABLED'),
            'module_name' => $this->module_name
        );

        return $this->renderTemplate($this->getTemplate(), array($extraParams), $assignedVars);
    }
}

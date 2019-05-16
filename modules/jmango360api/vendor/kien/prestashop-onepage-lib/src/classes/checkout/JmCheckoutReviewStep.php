<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Symfony\Component\Translation\TranslatorInterface;

class JmCheckoutReviewStep extends JmCheckoutStep
{
    protected $template;

    protected $conditionsToApproveFinder;

    protected $presentCart;

    public $module_name;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        ConditionsToApproveFinder $conditionsToApproveFinder,
        $presentCart = null,
        $module_name
    ) {
        $this->template = 'module:'.$module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/steps/review.tpl';
        parent::__construct($context, $translator, $checkoutSession, $module_name);
        $this->conditionsToApproveFinder = $conditionsToApproveFinder;
        $this->presentCart = $presentCart;
        $this->context->smarty->assign(
            'cart_template_path',
            'module:'.$module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/steps/_partials/order-final-summary-table.tpl'
        );
        $this->module_name = $module_name;
    }

    public function handleRequest(array $requestParams = array())
    {
    }

    public function render(array $extraParams = array())
    {
        $conditionsToApprove = $this->conditionsToApproveFinder->getConditionsToApproveForTemplate();
        $assignedVars = array(
            'discounts' => $this->context->cart->getCartRules(),
            'voucher_allowed' => CartRule::isFeatureActive(),
            'conditions_to_approve' => $conditionsToApprove,
            'cart' => $this->presentCart,
            'enable_coupon_onepage' => Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE),
            'module_name' => $this->module_name
        );
        return $this->renderTemplate($this->getTemplate(), array($extraParams), $assignedVars);
    }
}

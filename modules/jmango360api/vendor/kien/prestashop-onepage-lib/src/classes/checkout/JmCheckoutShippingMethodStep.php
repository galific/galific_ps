<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 2/27/18
 * Time: 3:00 PM
 * @author kien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Symfony\Component\Translation\TranslatorInterface;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class JmCheckoutShippingMethodStep extends JmCheckoutStep
{
    protected $template;

    protected $checkoutProcess;
    protected $checkoutDeliveryStep;
    public $json = array();

    public $frontController;
    private $checkoutController;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        FinalJmCheckout17 $checkoutController
    ) {
        $this->template = 'module:'.$checkoutController->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/shipping-methods.tpl';

        parent::__construct($context, $translator, $checkoutSession, $checkoutController->module_name);
        $this->frontController = new ModuleFrontController();

        $translator = $this->getTranslator();
        $this->checkoutController = $checkoutController;

        $this->checkoutDeliveryStep = new JmCheckoutDeliveryStep(
            $this->context,
            $translator,
            $checkoutSession,
            $checkoutController->module_name
        );
    }

    public function handleRequest(array $requestParams = array())
    {
        $errors = array();
        $this->checkoutDeliveryStep
            ->setRecyclablePackAllowed((bool)Configuration::get('PS_RECYCLABLE_PACK'))
            ->setGiftAllowed((bool)Configuration::get('PS_GIFT_WRAPPING'))
            ->setIncludeTaxes(
                !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer)
                && (int)Configuration::get('PS_TAX')
            )
            ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
            ->setGiftCost(
                $this->context->cart->getGiftWrappingPrice(
                    $this->checkoutDeliveryStep->getIncludeTaxes()
                )
            );

        $this->checkoutDeliveryStep->handleRequest(Tools::getAllValues());

        if ($this->checkoutDeliveryStep->hasErrors()) {
            foreach ($this->checkoutDeliveryStep->getErrors() as $error) {
                $errors[] = $error;
            }
        }

        // Incase there's no Delivery method available
        if (empty($this->checkoutDeliveryStep->getCheckoutSession()->getDeliveryOptions())) {
            $errors[] = $this->checkoutController->getLocalizeMessage(
                'There are no carriers that deliver to the address you selected.',
                'JmCheckout16'
            );
        }
        return $errors;
    }

    public function render(array $extraParams = array())
    {
        $this->checkoutDeliveryStep->setTemplate($this->template);

        $isDisplayCommentBox = version_compare(_PS_VERSION_, '1.7.2.0', '>=');

        return $this->checkoutDeliveryStep->render(array(
            'delivery_option_list' => $this->checkoutSession,
            'isDisplayCommentBox' => $isDisplayCommentBox,
            'module_name' => $this->checkoutController->module_name
        ));
    }
}

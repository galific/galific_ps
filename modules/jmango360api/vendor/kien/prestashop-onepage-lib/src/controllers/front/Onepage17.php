<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class Onepage_17
 */
class Onepage17 extends ModuleFrontController
{
    protected $checkoutProcess;
    protected $cartChecksum;
    public $module_name;
    public function init()
    {
        parent::init();
        $this->context->smarty->assign(array(
            'module_name' => $this->module_name,
        ));
        $this->cartChecksum = new CartChecksum(new AddressChecksum());
    }

    public function postProcess()
    {
        parent::postProcess();

        $this->bootstrap();
    }

    /**
     * Append Shipping Method & Payment Method steps
     */
    protected function bootstrap()
    {
        $this->checkoutProcess = new CheckoutProcess(
            $this->context,
            $this->getCheckoutSession()
        );

        if (!$this->context->cart->isVirtualCart()) {
            $checkoutDeliveryStep = new CheckoutDeliveryStep(
                $this->context,
                $this->getTranslator()
            );

            $checkoutDeliveryStep
                ->setRecyclablePackAllowed((bool)Configuration::get('PS_RECYCLABLE_PACK'))
                ->setGiftAllowed((bool)Configuration::get('PS_GIFT_WRAPPING'))
                ->setIncludeTaxes(
                    !Product::getTaxCalculationMethod((int)$this->context->cart->id_customer)
                    && (int)Configuration::get('PS_TAX')
                )
                ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
                ->setGiftCost($this->context->cart->getGiftWrappingPrice($checkoutDeliveryStep->getIncludeTaxes()));

            $this->checkoutProcess->addStep($checkoutDeliveryStep);
        }

        $this->checkoutProcess
            ->addStep(new CheckoutPaymentStep(
                $this->context,
                $this->getTranslator(),
                new PaymentOptionsFinder(),
                new ConditionsToApproveFinder(
                    $this->context,
                    $this->getTranslator()
                )
            ));
    }

    public function initContent()
    {
        parent::initContent();

        if (version_compare(_PS_VERSION_, '1.7', '<')) {
            if ($this->context->cart->nbProducts()) {
                //
            } else {
                $this->context->smarty->assign(array(
                    'content_only' => true
                ));
                $this->setTemplate('empty.tpl');
            }
        } else {
            $presentedCart = $this->cart_presenter->present($this->context->cart);
            if (count($presentedCart['products']) <= 0 || $presentedCart['minimalPurchaseRequired']) {
                $this->setTemplate('module:'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/empty.tpl');
                return;
            }

            $this->restorePersistedData($this->checkoutProcess);

            $this->checkoutProcess->handleRequest(
                Tools::getAllValues()
            );

            $this->checkoutProcess
                ->setNextStepReachable()
                ->markCurrentStep()
                ->invalidateAllStepsAfterCurrent();

            $this->saveDataToPersist($this->checkoutProcess);

            $this->context->smarty->assign(array(
                'checkout_process' =>
                    new \PrestaShop\PrestaShop\Core\Foundation\Templating\RenderableProxy($this->checkoutProcess),
                'cart' => $presentedCart,
            ));

            $this->setTemplate('module:'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage.tpl');
        }
    }

    protected function getCheckoutSession()
    {
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new \PrestaShop\PrestaShop\Adapter\Product\PriceFormatter()
        );

        $session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );

        return $session;
    }

    private function saveDataToPersist(CheckoutProcess $process)
    {
        $data = $process->getDataToPersist();
        $data['checksum'] = $this->cartChecksum->generateChecksum($this->context->cart);

        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'cart SET checkout_session_data = "' . pSQL(json_encode($data)) . '"
                WHERE id_cart = ' . (int)$this->context->cart->id
        );
    }

    private function restorePersistedData(CheckoutProcess $process)
    {
        $rawData = Db::getInstance()->getValue(
            'SELECT checkout_session_data FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . (int)$this->context->cart->id
        );
        $data = json_decode($rawData, true);
        if (!is_array($data)) {
            $data = array();
        }

        $checksum = $this->cartChecksum->generateChecksum($this->context->cart);
        if (isset($data['checksum']) && $data['checksum'] === $checksum) {
            $process->restorePersistedData($data);
        }
    }
}

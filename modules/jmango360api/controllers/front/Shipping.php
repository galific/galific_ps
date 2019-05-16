<?php
/**
 * Created by PhpStorm.
 * User: tien
 * Date: 2/22/18
 * Time: 15:20
 * @author tien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;

class Jmango360ApiShippingModuleFrontController extends ModuleFrontController
{
    private $shipping_error = array();
    protected $checkoutProcess;
    protected $checkoutDeliveryStep;
    public $json = array();
    private $module_dir = "";

    public function init()
    {
        parent::init();
        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->module_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
        } else {
            $this->module_dir = _PS_MODULE_DIR_.'jmango360api/';
        }
        $this->initContent();
    }

    public function setMedia()
    {
        parent::setMedia();

        if (version_compare(_PS_VERSION_, '1.6.0.1', '<')) {
            $this->module_dir = __PS_BASE_URI__.'modules/'.$this->name.'/';
        } else {
            $this->module_dir = _PS_MODULE_DIR_.'jmango360api/';
        }

        $this->addJS(array(
            _THEME_JS_DIR_.'tools/vatManagement.js',
            $this->module_dir.'views/js/statesManagement.js',
            _THEME_JS_DIR_.'authentication.js',
            _PS_JS_DIR_.'validate.js'
        ));

        $this->addCSS($this->module_dir.'views/css/bootstrap.min.css');
        $this->addCSS($this->module_dir.'views/css/font-awesome-4.7.0/css/font-awesome.css');
        $this->addCSS($this->module_dir.'views/css/responsive.css');
        $this->addCSS($this->module_dir.'views/css/ladda.min.css');
        $this->addCSS($this->module_dir.'views/css/17/style.css');
        $lang_iso_code = $this->context->language->iso_code;
        if ($lang_iso_code == 'nl') {
            $this->addJS($this->module_dir.'views/js/libs/lang/nl.js');
        } elseif ($lang_iso_code == 'es') {
            $this->addJS($this->module_dir.'views/js/libs/lang/es.js');
        } elseif ($lang_iso_code == 'it') {
            $this->addJS($this->module_dir.'views/js/libs/lang/it.js');
        } elseif ($lang_iso_code == 'ar') {
            $this->addJS($this->module_dir.'views/js/libs/lang/ar.js');
        }

        $this->registerJavascript(
            'module-jmango360api-jquery.min.js',
            'modules/jmango360api/views/js/libs/jquery.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap.min.js',
            'modules/jmango360api/views/js/libs/bootstrap.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap-select.1.12.2.js',
            'modules/jmango360api/views/js/libs/bootstrap-select.1.12.2.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-bootstrap-select.js',
            'modules/jmango360api/views/js/libs/bootstrap-select.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-jquery.min.js',
            'modules/jmango360api/views/js/libs/jquery.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-collapse.js',
            'modules/jmango360api/views/js/libs/collapse.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-jquery.form-validator.js',
            'modules/jmango360api/views/js/libs/jquery.form-validator.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );

        $this->registerJavascript(
            'module-jmango360api-spin.min.js',
            'modules/jmango360api/views/js/libs/spin.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-ladda.min.js',
            'modules/jmango360api/views/js/libs/ladda.min.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-template.js',
            'modules/jmango360api/views/js/template.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
        $this->registerJavascript(
            'module-jmango360api-jmcheckout.js',
            'modules/jmango360api/views/js/17/jmcheckout.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );

        return true;
    }

    public function initContent()
    {
        parent::initContent();
        $checkout_url =  __PS_BASE_URI__.'index.php?fc=module&module=jmango360api&controller=jmcheckout';
        $this->context->smarty->assign('myopc_checkout_url', $checkout_url);
        $this->context->smarty->assign('is_logged', (int) $this->context->customer->isLogged());
        $this->setTemplate('module:jmango360api/views/templates/front/onepage17/shipping-methods.tpl');
    }

    public function postProcess()
    {
        $output = $this->bootstrap();
        $this->json=$this->updateCarrier();

        if (!Tools::isSubmit('ajax')) {
            echo $output;
        } else {
            echo json_encode($this->json);
        }
        die();
    }

    protected function bootstrap()
    {
        $translator = $this->getTranslator();

        $session = $this->getCheckoutSession();
        $this->checkoutProcess = new CheckoutProcess(
            $this->context,
            $session
        );

        $this->checkoutDeliveryStep = new CheckoutDeliveryStep(
            $this->context,
            $translator
        );

        $this->checkoutDeliveryStep
            ->setRecyclablePackAllowed((bool) Configuration::get('PS_RECYCLABLE_PACK'))
            ->setGiftAllowed((bool) Configuration::get('PS_GIFT_WRAPPING'))
            ->setIncludeTaxes(
                !Product::getTaxCalculationMethod((int) $this->context->cart->id_customer)
                && (int) Configuration::get('PS_TAX')
            )
            ->setDisplayTaxesLabel((Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC')))
            ->setGiftCost(
                $this->context->cart->getGiftWrappingPrice(
                    $this->checkoutDeliveryStep->getIncludeTaxes()
                )
            );
        $this->checkoutProcess->addStep($this->checkoutDeliveryStep);


        $this->checkoutProcess
            ->setNextStepReachable()
            ->markCurrentStep()
            ->invalidateAllStepsAfterCurrent();

        $this->checkoutDeliveryStep->setTemplate($this->template);
        $this->context->smarty->assign(array(
            'layout' => $this->getLayout(),
            'stylesheets' => $this->getStylesheets(),
            'javascript' => $this->getJavascript(),
            'js_custom_vars' => Media::getJsDef(),
            'notifications' => $this->prepareNotifications(),
        ));
        $html = $this->checkoutDeliveryStep->render(array(
            'delivery_option_list'=>$this->getCheckoutSession()->getDeliveryOptions()
        ));
        return $html;
    }

    protected function getCheckoutSession()
    {
        $deliveryOptionsFinder = new DeliveryOptionsFinder(
            $this->context,
            $this->getTranslator(),
            $this->objectPresenter,
            new PriceFormatter()
        );

        $session = new CheckoutSession(
            $this->context,
            $deliveryOptionsFinder
        );
        return $session;
    }

    protected function updateCarrier()
    {
        $error = array();

        $this->checkoutDeliveryStep->handleRequest(Tools::getAllValues());

        if (empty($this->context->cart->getDeliveryOptionList())) {
            $error[] = $this->module->l(
                'There are no carriers that deliver to the address you selected.',
                'jmcheckout'
            );
        }

        return array(
            'goto_section' => !empty($error) ? '' : 'payment',
            'updated_section' => !empty($error) ? '' : '$paymentHtmlContent',
            'hasError' => !empty($error),
            'errors' => $error);
    }
}

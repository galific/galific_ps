<?php
/**
 * Created by PhpStorm.
 * User: i4cu
 * Date: 2/22/18
 * Time: 15:20
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use Symfony\Component\Translation\TranslatorInterface;

class JmCheckoutShippingAddressStep extends JmCheckoutStep
{
    protected $template_dir;
    protected $template;

    private $addressForm;
    private $checkoutController;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        CustomerAddressForm $addressForm,
        FinalJmCheckout17 $checkoutController
    ) {
        $this->template_dir = _PS_MODULE_DIR_ .$checkoutController->module_name. "/vendor/kien/prestashop-onepage-lib/src/views/templates/";
        $this->template = 'module:'.$checkoutController->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/_partials/shipping-address-form.tpl';
        parent::__construct($context, $translator, $checkoutSession, $checkoutController->module_name);
        $this->addressForm = $addressForm;
        $this->checkoutController = $checkoutController;
        $this->context->smarty->assign('shipping_address', 1);

        $this->checkoutController->registerJavascript(
            'noqa/themes/specialdev603/assets/js/custom.js',
            'themes/specialdev603/assets/js/custom.js',
            array('position' => 'head', 'priority' => 150, 'attribute' => 'async')
        );
    }


    public function handleRequest(array $requestParameters = array())
    {
        $data = array();
        $errors = array();
        $setToBilling = false;

        if (isset($requestParameters['shipping_address_id'])) {
            // Edit address
            $requestParameters['id_address'] = $requestParameters['shipping_address_id'];
        }

        if (isset($requestParameters['set_to_billing']) && $requestParameters['set_to_billing']) {
            $setToBilling = true;
        }

        $saved = $this->addressForm->fillWith($requestParameters)->submit();

        if (!$saved) {
            foreach ($this->addressForm->getErrors() as $field => $errs) {
                foreach ($errs as $err) {
                    $errors[] = $field . ': ' . $err;
                }
            }
        } else {
            $id_address = $this->addressForm->getAddress()->id;
            if ($requestParameters['saveAddress'] === 'delivery') {
                $current_invoice_id = $this->context->cart->id_address_invoice;
                $this->context->cart->updateAddressId($this->context->cart->id_address_delivery, $id_address);
                $this->context->cart->id_address_delivery = $id_address;
                $this->context->cart->setNoMultishipping();
                if ($setToBilling) {
                    $this->context->cart->id_address_invoice = $id_address;
                } else {
                    $this->context->cart->id_address_invoice = $current_invoice_id;
                }
                $this->context->cart->save();
            }

            $addresses = array();
            if ($this->context->customer->id != 0) {
                $addresses = $this->context->customer->getAddresses($this->context->language->id);
            }

            $data = array(
                'id_address_delivery' => $this->context->cart->id_address_delivery,
                'id_address_invoice' => $this->context->cart->id_address_invoice,
                'addresses' => $addresses,
                'customer' => $this->context->customer
            );
        }

        return array_merge(
            $data,
            array("errors" => $errors)
        );
    }

    public function getTemplateParameters()
    {
        $addressForm = $this->checkoutController->createAddressForm();
        $addressForm->fillWith(array(
            'firstname' => $this->context->customer->firstname,
            'lastname' => $this->context->customer->lastname,
        ));

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(array('id_country' => Tools::getValue('id_country')));
        }
        $templateParams = array_merge(
            $addressForm->getTemplateVariables(),
            array(
                'type' => 'delivery',
                'guest_allowed' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'myopc_checkout_url' => $this->module_url,
                'template_dir' => $this->template_dir,
                'javascript' => $this->checkoutController->getJavascript(),
                'customer' => $this->context->customer,
                'module_name' => $this->checkoutController
            )
        );

        ob_end_clean();
        header('Content-Type: application/json');

        return $templateParams;
    }

    public function render(array $extraParams = array())
    {
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            $this->getTemplateParameters()
        );
    }
}

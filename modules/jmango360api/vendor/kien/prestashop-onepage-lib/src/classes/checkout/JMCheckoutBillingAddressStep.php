<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use Symfony\Component\Translation\TranslatorInterface;

class JMCheckoutBillingAddressStep extends JmCheckoutStep
{
    protected $template;

    protected $template_dir;

    private $addressForm;
    private $checkoutController;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        CustomerAddressForm $addressForm,
        FinalJmCheckout17 $checkoutController
    ) {
        $this->template = 'module:'.$checkoutController->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/_partials/address-form.tpl';

        $this->template_dir = _PS_MODULE_DIR_ .$checkoutController->module_name. "/vendor/kien/prestashop-onepage-lib/src/views/templates";

        parent::__construct($context, $translator, $checkoutSession, $checkoutController->module_name);
        $this->addressForm = $addressForm;
        $this->checkoutController = $checkoutController;

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

        $isSaveAction = false;

        if (Tools::getIsset('billing') && Tools::getValue('billing')['edit'] == 1) {
            $isSaveAction = true;
        }
        if (isset($requestParameters['billing_address_id']) && $requestParameters['billing_address_id'] != 0) {
            // Edit address
            $requestParameters['id_address'] = $requestParameters['billing_address_id'];
            $id_address = $requestParameters['billing_address_id'];
            if ($this->context->customer->is_guest == 1) {
                $isSaveAction = true;
            }
        } elseif ($this->context->customer->id != 0 && $this->context->customer->is_guest == 1) {
            if ($this->context->cart->id_address_invoice != 0) {
                $requestParameters['id_address'] = $this->context->cart->id_address_invoice;
            }
            $isSaveAction = true;
        } else {
            $isSaveAction = true;
        }

        if ($isSaveAction) {
            $saved = $this->addressForm->fillWith($requestParameters)->submit();
            if (!$saved) {
                foreach ($this->addressForm->getErrors() as $field => $errs) {
                    foreach ($errs as $err) {
                        $errors[] = $field . ' : ' . $err;
                    }
                }
            } else {
                $id_address = $this->addressForm->getAddress()->id;
            }
        }

        if (empty($errors)) {
            if ($requestParameters['saveAddress'] === 'invoice') {
                $this->context->cart->updateAddressId($this->context->cart->id_address_invoice, $id_address);
                $this->context->cart->id_address_invoice = $id_address;
                if ($requestParameters['use_for_shipping'] == 1) {
                    $this->context->cart->id_address_delivery = $id_address;
                }
                $this->context->cart->autosetProductAddress();
                $this->context->cart->save();
            } else {
                $this->context->cart->id_address_delivery = $id_address;
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
                'customer' => $this->context->customer,
                'module_name' => $this->checkoutController->module_name
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
        if (Tools::getIsset('submitGuestAccount')) {
            if ($this->context->cart->id_address_invoice != 0) {
                $billingAddress = new Address($this->context->cart->id_address_invoice);
                $requestParameters = json_decode(json_encode($billingAddress), true);
                $addressForm->fillWith($requestParameters);
            }
        }
        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(array('id_country' => Tools::getValue('id_country')));
        }
        $templateParams = array_merge(
            $addressForm->getTemplateVariables(),
            array(
                'type' => 'invoice',
                'guest_allowed' => Configuration::get('PS_GUEST_CHECKOUT_ENABLED'),
                'myopc_checkout_url' => $this->module_url,
                'template_dir' => $this->template_dir,
                'javascript' => $this->checkoutController->getJavascript(),
                'customer' => $this->context->customer,
                'module_name' => $this->checkoutController->module_name
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

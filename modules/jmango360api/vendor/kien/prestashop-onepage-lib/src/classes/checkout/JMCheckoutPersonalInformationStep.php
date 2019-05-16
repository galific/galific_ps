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

class JMCheckoutPersonalInformationStep extends JmCheckoutStep
{
    protected $template;

    private $registerForm;
    private $controller;

    public function __construct(
        Context $context,
        TranslatorInterface $translator,
        JmCheckoutSession $checkoutSession,
        CustomerForm $registerForm,
        FinalJmCheckout17 $controller
    ) {
        $this->template = 'module:'.$controller->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/onepage17/_partials/customer-form.tpl';
        parent::__construct($context, $translator, $checkoutSession, $controller->module_name);
        $this->registerForm = $registerForm;
        $this->controller = $controller;
    }

    public function handleRequest(array $requestParameters = array())
    {
        $data = array();
        $errors = array();
        $this->registerForm->fillWith($requestParameters);
        if ($this->registerForm->validate()) {
            $this->registerForm->submit();
        }

        foreach ($this->registerForm->getErrors() as $field => $errs) {
            foreach ($errs as $err) {
                $errors = $err;
            }
        }

        return array_merge(
            $data,
            array("errors" => $errors)
        );
    }

    public function render(array $extraParams = array())
    {
        return $this->renderTemplate(
            $this->getTemplate(),
            $extraParams,
            array(
                'register_form' => $this->registerForm->getProxy(),
                'guest_allowed' => $this->getCheckoutSession()->isGuestAllowed(),
            )
        );
    }
}

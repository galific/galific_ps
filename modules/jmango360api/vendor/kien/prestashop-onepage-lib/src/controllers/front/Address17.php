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

class Address17 extends ModuleFrontController
{
    public $auth = false;
    public $guestAllowed = true;
    public $php_self = '';
    public $authRedirection = 'addresses';
    public $ssl = true;

    private $address_form;
    private $should_redirect = false;
    public $module_name;
    /**
     * Initialize address controller
     * @see FrontController::init()
     */
    public function init()
    {
        // Currenly mobile's seding param 'id' when editting address (It's Magento behaviour).
        // In Prestashop, in order to display existed address data, the param 'id_address' should be provided.
        if (Tools::getValue('id')) {
            $_GET['id_address'] = Tools::getValue('id');
        }
        $this->context->smarty->assign(array(
            'module_name' => $this->module_name,
        ));
        parent::init();
        $this->address_form = $this->makeAddressForm();
        $this->context->smarty->assign('address_form', $this->address_form->getProxy());
        $this->address_form->setTemplate('module:'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/address-form-17.tpl');
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::getValue('id_address')) {
            $this->address_form->loadAddressById(Tools::getValue('id_address'));
            $this->context->smarty->assign('editing', true);
        } else {
            $this->context->smarty->assign('editing', false);
        }
        $this->address_form->fillWith(Tools::getAllValues());

        if (Tools::isSubmit('submitAddress')) {
            if (!$this->address_form->submit()) {
                $this->errors[] = $this->trans('Please fix the error below.', array(), 'Shop.Notifications.Error');
            } else {
                if (Tools::getValue('id_address')) {
                    $this->success[] = $this->trans(
                        'Address successfully updated!',
                        array(),
                        'Shop.Notifications.Success'
                    );
                } else {
                    $this->success[] = $this->trans(
                        'Address successfully added!',
                        array(),
                        'Shop.Notifications.Success'
                    );
                }

                if (!empty($this->success)) {
                    if ($this->context->customer) {
                        $return = "<meta name=\"JM-Account-Id\" content=\"" . $this->context->customer->id . "\">" .
                          "<meta name=\"JM-Account-Email\" content=\"" . $this->context->customer->email . "\">";
                        die($return);
                    }
                }
            }
        }
    }


    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate(
            'module:'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/address17.tpl',
            array('entity' => 'address', 'id' => Tools::getValue('id_address'))
        );
    }

    public function displayAjaxAddressForm()
    {
        $addressForm = $this->makeAddressForm();

        if (Tools::getIsset('id_address') && ($id_address = (int)Tools::getValue('id_address'))) {
            $addressForm->loadAddressById($id_address);
        }

        if (Tools::getIsset('id_country')) {
            $addressForm->fillWith(array('id_country' => Tools::getValue('id_country')));
        }

        ob_end_clean();
        header('Content-Type: application/json');
        $this->ajaxDie(Tools::jsonEncode(array(
            'address_form' => $this->renderTemplate(
                'module:'.$this->module_name.'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/address-form-17.tpl',
                array(),
                $addressForm->getTemplateVariables()
            ),
        )));
    }

    protected function renderTemplate($template, array $extraParams = array(), array $params = array())
    {
        $defaultParams = array(
//            'title' => $this->getTitle(),
        );

        $scope = $this->context->smarty->createData(
            $this->context->smarty
        );

        $scope->assign(array_merge($defaultParams, $extraParams, $params));

        $tpl = $this->context->smarty->createTemplate(
            $template,
            $scope
        );

        $html = $tpl->fetch();

        return $html;
    }
}

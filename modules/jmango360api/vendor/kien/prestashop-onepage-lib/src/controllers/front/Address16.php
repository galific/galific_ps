<?php
/**
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class Address16 extends AddressController
{

    public $php_self = '';
    public $module_name;
    /**
     * Initialize parent order controller
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
    }


    /**
     * Assign template vars related to page content
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate(_PS_MODULE_DIR_ . $this->module_name .'/vendor/kien/prestashop-onepage-lib/src/views/templates/front/address16.tpl');
    }

    /**
     * Initializes page header variables
     */
    public function initHeader()
    {
        parent::initHeader();
        $this->context->smarty->assign('content_only', 1);
    }

    /**
     * Start forms process
     * @see FrontController::postProcess()
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddress')) {
            $this->processSubmitAddress();
        } elseif (!Validate::isLoadedObject($this->_address) && Validate::isLoadedObject($this->context->customer)) {
            $_POST['firstname'] = $this->context->customer->firstname;
            $_POST['lastname'] = $this->context->customer->lastname;
            $_POST['company'] = $this->context->customer->company;
        }
    }


    /**
     * Process changes on an address
     */
    protected function processSubmitAddress()
    {
        $address = new Address();
        $this->errors = $address->validateController();
        $address->id_customer = (int)$this->context->customer->id;

        // Check page token
        if ($this->context->customer->isLogged() && !$this->isTokenValid()) {
            $this->errors[] = Tools::displayError('Invalid token.');
        }

        // Check phone
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST')
            && !Tools::getValue('phone')
            && !Tools::getValue('phone_mobile')) {
            $this->errors[] = Tools::displayError('You must register at least one phone number.');
        }
        if ($address->id_country) {
            // Check country
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                throw new PrestaShopException('Country cannot be loaded with address->id_country');
            }

            if ((int)$country->contains_states && !(int)$address->id_state) {
                $this->errors[] = Tools::displayError('This country requires you to chose a State.');
            }

            if (!$country->active) {
                $this->errors[] = Tools::displayError('This country is not active.');
            }

            $postcode = Tools::getValue('postcode');
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $this->errors[] =
                    sprintf(
                        Tools::displayError('The Zip/Postal code you\'ve entered is invalid.
                        It must follow this format: %s'),
                        str_replace(
                            'C',
                            $country->iso_code,
                            str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))
                        )
                    );
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = Tools::displayError('A Zip/Postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = Tools::displayError('The Zip/Postal code is invalid.');
            }

            // Check country DNI
            if ($country->isNeedDni() && (!Tools::getValue('dni') || !Validate::isDniLite(Tools::getValue('dni')))) {
                $this->errors[] =
                    Tools::displayError('The identification number is incorrect or has already been used.');
            } elseif (!$country->isNeedDni()) {
                $address->dni = null;
            }
        }
        // Check if the alias exists
        if (!$this->context->customer->is_guest
            && !empty(Tools::getValue('alias'))
            && (int)$this->context->customer->id > 0) {
            $id_address = Tools::getValue('id_address');
            if (Configuration::get('PS_ORDER_PROCESS_TYPE')
                && (int)Tools::getValue('opc_id_address_'.Tools::getValue('type')) > 0) {
                $id_address = Tools::getValue('opc_id_address_'.Tools::getValue('type'));
            }

            if (Address::aliasExist(Tools::getValue('alias'), (int)$id_address, (int)$this->context->customer->id)) {
                $this->errors[] =
                    sprintf(
                        Tools::displayError('The alias "%s" has already been used. Please select another one.'),
                        Tools::safeOutput(Tools::getValue('alias'))
                    );
            }
        }

        // Check the requires fields which are settings in the BO
        $this->errors = array_merge($this->errors, $address->validateFieldsRequiredDatabase());

        // Don't continue this process if we have errors !
        if ($this->errors && !$this->ajax) {
            return;
        }

        // If we edit this address, delete old address and create a new one
        if (Validate::isLoadedObject($this->_address)) {
            if (Validate::isLoadedObject($country) && !$country->contains_states) {
                $address->id_state = 0;
            }
            $address_old = $this->_address;
            if (Customer::customerHasAddress($this->context->customer->id, (int)$address_old->id)) {
                if ($address_old->isUsed()) {
                    $address_old->delete();
                } else {
                    $address->id = (int)$address_old->id;
                    $address->date_add = $address_old->date_add;
                }
            }
        }

        if ($this->ajax && Configuration::get('PS_ORDER_PROCESS_TYPE')) {
            $this->errors = array_unique(array_merge($this->errors, $address->validateController()));
            if (count($this->errors)) {
                $return = array(
                    'hasError' => (bool)$this->errors,
                    'errors' => $this->errors
                );
                $this->ajaxDie(Tools::jsonEncode($return));
            }
        }

        /*
       * Save address
       * fix for validation_plugin $result not use.
       * Before :    if ($result = $address->save()) {
       */
        if ($address->save()) {
            // Update id address of the current cart if necessary
            if (isset($address_old) && $address_old->isUsed()) {
                $this->context->cart->updateAddressId($address_old->id, $address->id);
            } else { // Update cart address
                $this->context->cart->autosetProductAddress();
            }

            if ((bool)Tools::getValue('select_address', false) == true
                || (Tools::getValue('type') == 'invoice' && Configuration::get('PS_ORDER_PROCESS_TYPE'))) {
                $this->context->cart->id_address_invoice = (int)$address->id;
            } elseif (Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                $this->context->cart->id_address_invoice = (int)$this->context->cart->id_address_delivery;
            }

            $this->context->cart->update();
            $this->ajax = true;

            if (empty($this->errors)) {
                if ($this->context->customer) {
                    $return = "<meta name=\"JM-Account-Id\" content=\"" . $this->context->customer->id . "\">" .
                      "<meta name=\"JM-Account-Email\" content=\"" . $this->context->customer->email . "\">";
                    die($return);
                }
            }
        }
        $this->errors[] = Tools::displayError('An error occurred while updating your address.');
    }
}

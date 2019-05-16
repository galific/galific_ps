<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CustomerAddressService extends BaseService
{
    public $errors = array();
    public $translation;
    public $company;

    public function doExecute()
    {
        $this->id_lang = Tools::getValue('id_lang');

        $lang = new LanguageCore($this->id_lang);
        include_once(_PS_THEME_DIR_ . 'lang/' . $lang->iso_code . '.php');
        $this->translation = $GLOBALS['_LANG'];

        if ($this->isGetMethod()) {
            //get customer addresses

            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);

            if ($customer && $customer->id) {
                $language_id = $this->context->language->id;
                $addresses = $customer->getAddresses($language_id);
                foreach ($addresses as &$address) {
                    $address['id'] = $address['id_address'];
                    unset($address['id_address']);
                    $address['country_iso_code'] = Country::getIsoById($address['id_country']);
                }
                $this->response = new CustomerAddressResponse();
                $this->response->addresses = $addresses;
            } else {
                $this->response = new JmResponse();
                $this->response->errors = array('Customer doest not exits!');
            }
        } elseif ($this->isPostMethod()) {
            //add customer address
            $customer_id = $this->getRequestResourceId();
            $this->response = new CustomerAddressResponse();
            $customer = new Customer($customer_id);

            if ($customer && $customer->id) {
                $this->context->customer = $customer;
                try {
                    $address = $this->createAddressFromRequest($customer->id);
                    if (!$address->id && empty($this->errors)) {
                        $address->add();
                    }
                    $this->response->address = $address;
                } catch (PrestaShopDatabaseException $e) {
                    $this->response->errors = array('Failed to create new address! ' . $e->getMessage());
                } catch (PrestaShopException $e) {
                    $this->response->errors = array('Failed to create new address! ' . $e->getMessage());
                }
            } else {
                $this->response->errors = array('Customer doest not exits!');
            }
        } elseif ($this->isPutMethod()) {
            $customer_id = $this->getRequestResourceId();
            $this->response = new CustomerAddressResponse();
            $customer = new Customer($customer_id);

            if ($customer && $customer->id) {
                $this->context->customer = $customer;
                try {
                    $address = $this->createAddressFromRequest($customer->id);
                    if (empty($this->errors)) {
                        if ($address->isUsed()) {
                            $old_address = new Address($address->id);
                            $address->id = $address->id_address = null;
                            $address->save() && $old_address->delete();
                        } else {
                            $address->update();
                        }
                        $this->response->address = $address;
                    }
                } catch (PrestaShopDatabaseException $e) {
                    $this->response->errors = new JmError(500, Tools::displayError('An error occurred while updating your address.'));
                } catch (PrestaShopException $e) {
                    $this->response->errors = new JmError(500, Tools::displayError('An error occurred while updating your address.'));
                }
            } else {
                $this->response->errors = array('Customer doest not exits!');
            }
        } elseif ($this->isDeleteMethod()) {
            // delete customer address
            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);

            if ($customer && $customer->id) {
                $id_address = Tools::getValue('id_address');

                $language_id = $this->context->language->id;
                $address = new AddressCore($id_address, $language_id);

                if ($address->delete()) {
                    //address deleted
                    $this->response = new JmResponse();
//                    $this->response->messages = array('Address is deleted!');
                } else {
                    $this->response = new JmResponse();
                    $this->response->errors = new JmError(500, Tools::displayError('This address cannot be deleted.'));
                }
            } else {
                $this->response = new JmResponse();
                $this->response->errors = new JmError(500, Tools::displayError('This address cannot be deleted.'));
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }

    /**
     * Origin: FrontController::makeAddressPersister()
     *
     * @return CustomerAddressPersister
     */
    protected function makeAddressPersister()
    {
        return new CustomerAddressPersister(
            $this->context->customer,
            $this->context->cart,
            Tools::getToken(true, $this->context)
        );
    }

    /**
     * Origin: FrontController::makeAddressForm()
     *
     * @return CustomerAddressForm
     */
    protected function makeAddressForm()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $availableCountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $availableCountries = Country::getCountries($this->context->language->id, true);
        }

        $addressPersister = $this->makeAddressPersister();
        $token = $addressPersister->getToken();

        $form = new CustomerAddressForm(
            $this->context->smarty,
            $this->context->language,
            $this->context->getTranslator(),
            $addressPersister,
            new CustomerAddressFormatter(
                $this->context->country,
                $this->context->getTranslator(),
                $availableCountries
            )
        );

        $form->fillWith(array());
        $form->setValue('token', $token);

        return $form;
    }

    /**
     * Add new customer address follow PS 1.7
     *
     * @param $id_customer
     * @return Address
     */
    protected function createAddressFromRequest17($id_customer)
    {
        $request = json_decode($this->getRequestBody());
        if ($request->address->id) {
            $request->address->id_address = $request->address->id;
        }
        $address_form = $this->makeAddressForm();
        $address_form->fillWith((array)$request->address);
        if (!$address_form->submit()) {
            $errors = $address_form->getErrors();
            foreach ($errors as $field => $error) {
                if (!empty($error)) {
                    $form_field = $address_form->getField($field);
                    $this->errors[] = new JmError(500, sprintf('%s: %s', $form_field->getLabel(), join(', ', $error)));
                }
            }
            $this->response->errors = $this->errors;
        }

        return $address_form->getAddress();
    }

    public function createAddressFromRequest($id_customer)
    {
        if ($this->isV17()) {
            return $this->createAddressFromRequest17($id_customer);
        }

        $request = json_decode($this->getRequestBody());
        $request_address = $request->address;
        $address = new Address();
        $customer = new Customer($id_customer);
        $address->id_customer = $customer->id;
        if ((int)$request_address->id) {
            $address->id = $request_address->id;
        }
        $address->firstname = $request_address->firstname;
        $address->lastname = $request_address->lastname;
        $address->address1 = $request_address->address1;
        $address->address2 = $request_address->address2;
        $address->postcode = $request_address->postcode;
        $address->city = $request_address->city;
        $address->id_country = $request_address->id_country;
        $address->id_state = $request_address->id_state;
        $address->phone = $request_address->phone;
        $address->phone_mobile = $request_address->phone_mobile;
        $address->company = $this->company ? $this->company : $request_address->company;
        $address->vat_number = $request_address->vat_number;
        $address->dni = $request_address->dni;
        $address->other = $request_address->other;
        $address->alias = $request_address->alias;

        if ($this->isV17() && !$address->alias) {
            $address->alias = $this->getThemeTranslation('My Address', 'Shop.Theme.Checkout');
        }

        // Check phone
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && !$address->phone && !$address->phone_mobile) {
            $this->errors[] = Tools::displayError('You must register at least one phone number.');
        }

        if ($address->id_country) {
            // Check country
            $country = new Country($address->id_country);
            if (!$this->isV17()) {
                if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                    throw new PrestaShopException('Country cannot be loaded with address->id_country');
                }

                if ((int)$country->contains_states && !(int)$address->id_state) {
                    $this->errors[] = Tools::displayError('This country requires you to chose a State.');
                }

                if (!$country->active) {
                    $this->errors[] = Tools::displayError('This country is not active.');
                }
            }


            $postcode = $address->postcode;
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                if ($this->isV17()) {
                    $this->errors[] = $this->translator->trans(
                        'Invalid postcode - should look like "%zipcode%"',
                        array('%zipcode%' => $country->zip_code_format),
                        'Shop.Forms.Errors'
                    );
                } else {
                    $this->errors[] = sprintf(Tools::displayError('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'), str_replace('C', $country->iso_code, str_replace('N', '0', str_replace('L', 'A', $country->zip_code_format))));
                }
            } elseif (empty($postcode) && $country->need_zip_code) {
                $this->errors[] = Tools::displayError('A Zip/Postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $this->errors[] = Tools::displayError('The Zip/Postal code is invalid.');
            }

            // Check country DNI
            if ($country->isNeedDni() && (!$address->dni || !Validate::isDniLite($address->dni))) {
                if ($this->isV17()) {
                    $this->errors[] = $this->translator->trans(
                        'The identification number is incorrect or has already been used.',
                        array(),
                        'Admin.Orderscustomers.Notification'
                    );
                } else {
                    $this->errors[] = Tools::displayError('The identification number is incorrect or has already been used.');
                }
            }
        }

        // Check if the alias exists
        // only for version 1.6. Prestashop 1.7 allow same alias for multiple address
        if (!$customer->is_guest && !empty($address->alias) && (int)$customer->id > 0 && !$this->isV17()) {
            $id_address = $address->id;
//                    if (Configuration::get('PS_ORDER_PROCESS_TYPE') && (int)Tools::getValue('opc_id_address_'.Tools::getValue('type')) > 0) {
//                        $id_address = Tools::getValue('opc_id_address_'.Tools::getValue('type'));
//                    }

            if (Address::aliasExist($address->alias, (int)$id_address, (int)$customer->id)) {
                $this->errors[] = sprintf(Tools::displayError('The alias "%s" has already been used. Please select another one.'), Tools::safeOutput($address->alias));
            }
        }

        // Check the requires fields which are settings in the BO
        $this->errors = array_merge($this->errors, $this->validateFieldsRequiredDatabase($address));
        $this->errors = array_merge($this->errors, $address->validateController());

        if (!empty($this->errors)) {
            $this->response->errors[] = new JmError(500, $this->formatErrors($this->errors));
        }

        return $address;
    }

    public function validateFieldsRequiredDatabase($address_fields)
    {
        $errors = array();
        $required_fields = AddressFormat::getFieldsRequired();
        $country = new Country($address_fields->id_country);
        if ((int)$country->contains_states && $this->isV17()) {
            $required_fields[] = 'id_state';
        }

        foreach ($address_fields as $field => $data) {
            if (!in_array($field, $required_fields)) {
                continue;
            }

            if (empty($data)) {
                if ($this->isV17()) {
                    $errors[$field] = $this->translator->trans(
                        'The %name% field is required.',
                        array('%name%' => $field),
                        'Admin.Notifications.Error'
                    );
                } else {
                    $errors[$field] = sprintf(Tools::displayError('The field %s is required.'), $field);
                }
            }
        }
        return $errors;
    }
}

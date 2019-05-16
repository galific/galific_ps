<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * API to save address of customer
 */

require_once 'AppCore.php';

class AppAddAddress extends AppCore
{
    private $address = null;
    private $country = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore class
     *
     * @return json
     */
    public function getPageData()
    {
        if (Tools::getIsset('shipping_address')) {
            if ($this->validateCustomer()) {
                $this->processSubmitAddress();
            }
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Check country needs DNI number
     *
     * @return bool
     */
    public function isNeedDni()
    {
        return Country::isNeedDniByCountryId($this->country->id);
    }

    /**
     * Check country needs DNI number
     *
     * @param int $id_country country id
     * @return bool
     */
    public static function isNeedDniByCountryId($id_country)
    {
        return (bool) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT `need_identification_number`
			FROM `' . _DB_PREFIX_ . 'country`
			WHERE `id_country` = ' . (int) $id_country);
    }


    /**
     * Set submitted value to an address object
     *
     */
    public function processSubmitAddress()
    {
        $address_data = Tools::getValue('shipping_address', Tools::jsonEncode(array()));
        $address_data = Tools::jsonDecode($address_data);
        if (!empty($address_data)) {
            $id_address = Tools::getValue('id_shipping_address', 0);
            $address = new Address((int) $id_address);
            $data = $address_data;
            if (isset($data->firstname)) {
                $address->firstname = $data->firstname;
            }
            if (isset($data->lastname)) {
                $address->lastname = $data->lastname;
            }
            if (isset($data->company)) {
                $address->company = $data->company;
            }
            if (isset($data->address1)) {
                $address->address1 = $data->address1;
            }
            if (isset($data->address2)) {
                $address->address2 = $data->address2;
            }
            if (isset($data->postcode)) {
                $address->postcode = $data->postcode;
            }
            if (isset($data->city)) {
                $address->city = $data->city;
            }
            if (isset($data->country)) {
                $address->id_country = $data->country;
                $address->country = Country::getNameById($this->context->language->id, $data->country);
            }
            if (isset($data->phone)) {
                $address->phone = $data->phone;
            }
            if (isset($data->phone_mobile)) {
                $address->phone_mobile = $data->phone_mobile;
            }
            if (isset($data->alias)) {
                $address->alias = $data->alias;
            }
            if (isset($data->state)) {
                $address->id_state = $data->state;
            }
            if (isset($data->other)) {
                $address->other = $data->other;
            }
            if (isset($data->vat_number)) {
                $address->vat_number = $data->vat_number;
            }
            if (isset($data->dni)) {
                $address->dni = $data->dni;
            }

            if ($this->validatePostAddress($address)) {
                /* If we edit this address, delete old address and create a new one */
                if (Validate::isLoadedObject($address)) {
                    $address_old = $address;
                    if (Customer::customerHasAddress($this->context->customer->id, (int) $address_old->id)) {
                        $address->id = (int) $address_old->id;
                        $address->date_add = $address_old->date_add;
                    }
                }
                /* Save address */
                if ($address->save()) {
                    /* update guest customer firstname and lastname */
                    if ($this->context->cookie->is_guest == 1) {
                        if ($this->context->cookie->id_customer) {
                            $customer = new Customer((int) $this->context->cookie->id_customer);
                            $customer->firstname = $address->firstname;
                            $customer->lastname = $address->firstname;
                            $customer->update('true');
                        }
                    }
                    /* Update id address of the current cart if necessary */
                    if (isset($address_old) && $address_old->isUsed()) {
                        $this->context->cart->updateAddressId($address_old->id, $address->id);
                    } else {
                        /* Update cart address */
                        $this->context->cart->autosetProductAddress();
                    }

                    if (Configuration::get('PS_ORDER_PROCESS_TYPE')) {
                        $this->context->cart->id_address_invoice = (int) $address->id;
                    }

                    if ($id_address != 0) {
                        $this->content['shipping_address_reponse'] = array(
                            'status' => 'success',
                            'message' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('shipping address has been updated successfully.'),
                                'AppAddAddress'
                            )
                        );
                        $this->writeLog('shipping address has been updated successfully.');
                    } else {
                        $this->content['shipping_address_reponse'] = array(
                            'status' => 'success',
                            'message' => parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('shipping address has been added successfully.'),
                                'AppAddAddress'
                            )
                        );
                        $this->writeLog('shipping address has been added successfully.');
                    }
                    $this->content['cart_id'] = $this->context->cart->id;
                    $addresses = $this->context->customer->getAddresses($this->context->language->id);
                    $this->content['shipping_address_count'] = count($addresses);
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart->update();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    $this->context->cookie->write();

                    $this->content['id_shipping_address'] = (int) $address->id;
                } else {
                    $this->content['shipping_address_reponse'] = array(
                        'status' => 'failure',
                        'message' => parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('An error occurred while adding your address.'),
                            'AppAddAddress'
                        )
                    );
                    $this->writeLog('An error occurred while adding your address.');
                }
            }
        }
    }

    /**
     * Validate address object
     *
     * @param object $address address object
     * @return bool
     */
    public function validatePostAddress(&$address)
    {
        $errors = array();
        $errors = $address->validateController();
        $address->id_customer = (int) $this->context->customer->id;
        /* Check phone */
        if (Configuration::get('PS_ONE_PHONE_AT_LEAST') && empty($address->phone) && empty($address->phone_mobile)) {
            $errors[] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('You must register at least one phone number.'),
                'AppAddAddress'
            );
            $this->writeLog('Customer must register at least one phone number.');
        }

        if ($address->id_country) {
            /* Check country */
            if (!($country = new Country($address->id_country)) || !Validate::isLoadedObject($country)) {
                $errors[] = 'Country cannot be loaded with country id ' . $address->id_country;
                $this->writeLog('Country cannot be loaded with country id ' . $address->id_country);
            }

            if ((int) $country->contains_states && !(int) $address->id_state) {
                $errors[] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('This country requires you to chose a State.'),
                    'AppAddAddress'
                );
                $this->writeLog('Country requires you to choose a state.');
            }

            if (!$country->contains_states) {
                $address->id_state = 0;
            }

            if (!$country->active) {
                $errors[] =  parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('This country is not active.'),
                    'AppAddAddress'
                );
                $this->writeLog('Country is not active');
            }

            $postcode = $address->postcode;
            /* Check zip code format */
            if ($country->zip_code_format && !$country->checkZipCode($postcode)) {
                $errors[] = sprintf(
                    parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('The Zip/Postal code you\'ve entered is invalid. It must follow this format: %s'),
                        'AppAddAddress'
                    ),
                    str_replace(
                        'C',
                        $country->iso_code,
                        str_replace(
                            'N',
                            '0',
                            str_replace('L', 'A', $country->zip_code_format)
                        )
                    )
                );
                $this->writeLog('Zipcode format is invalid');
            } elseif (empty($postcode) && $country->need_zip_code) {
                $errors[] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('A Zip/Postal code is required.'),
                    'AppAddAddress'
                );
                $this->writeLog('A Zip/Postal code is required.');
            } elseif ($postcode && !Validate::isPostCode($postcode)) {
                $errors[] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('The Zip/Postal code is invalid.'),
                    'AppAddAddress'
                );
                $this->writeLog('Zipcode is invalid');
            }

            /* Check country DNI */
            if ($country->isNeedDni() && (!$address->dni || !Validate::isDniLite($address->dni))) {
                $errors[] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('The identification number is incorrect or has already been used.'),
                    'AppAddAddress'
                );
                $this->writeLog('The identification number is incorrect or has already been used.');
            } elseif (!$country->isNeedDni()) {
                $address->dni = null;
            }
        }
        /* Check if the alias exists */
        if (!$this->context->customer->is_guest && !empty($address->alias) && (int) $this->context->customer->id > 0) {
            if (isset($address->id) && !empty($address->id)) {
                $id_address = $address->id;
            } else {
                $id_address = 0;
            }

            if (Address::aliasExist($address->alias, (int) $id_address, (int) $this->context->customer->id)) {
                $errors[] = sprintf(
                    parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('The alias %s has already been used. Please select another one.'),
                        'AppAddAddress'
                    ),
                    $address->alias
                );
            }
        }
        /* Don't continue this process if we have errors ! */
        if ($errors) {
            $this->content['shipping_address_reponse'] = array(
                'status' => 'failure',
                'message' => implode('<br>', $errors)
            );
            $this->writeLog('An error occurred while validating shipping address.');
            return false;
        } else {
            return true;
        }
    }

    /**
     * Validate customer i.e email is valid or not or customer with provided email address is exist or not
     *
     * @return bool
     */
    public function validateCustomer()
    {
        $email = Tools::getValue('user_email', '');
        if (!Validate::isEmail($email)) {
            $this->content['shipping_address_reponse'] = array(
                'status' => 'failure',
                
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Email address is not valid'),
                    'AppAddAddress'
                ),
            );
            $this->writeLog('Email address is not valid');
            return false;
        } else {
            if (Customer::customerExists(strip_tags($email), false, false)) {
                $cart_id = Tools::getValue('session_data', '');
                if (!empty($cart_id)) {
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart = new Cart($cart_id);
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    $this->context->cookie->write();
                } else {
                    $this->context->cart->id_currency = $this->context->currency->id;
                    $this->context->cart->add();
                    $this->context->cookie->id_cart = (int) $this->context->cart->id;
                    $this->context->cookie->write();
                }
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email, null, false);

                $customer = new Customer($customer_tmp->id);

                /*Update Context */
                $this->context->customer = $customer;
                $this->context->cookie->id_customer = (int) $customer->id;
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->logged = 1;
                $this->context->cookie->email = $customer->email;
                $this->context->cookie->is_guest = $customer->is_guest;

                $this->context->cart->id_carrier = 0;
                $this->context->cart->setDeliveryOption(null);
                $this->context->cart->id_address_delivery = (int)
                        Address::getFirstCustomerAddressId((int) $customer->id);
                $this->context->cart->id_address_invoice = (int)
                        Address::getFirstCustomerAddressId((int) $customer->id);

                return true;
            } else {
                $this->content['shipping_address_reponse'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Customer with this email not exist'),
                        'AppAddAddress'
                    )
                );
                $this->writeLog('Customer with this email not exist');
                return false;
            }
        }
    }
}

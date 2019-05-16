<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 6/14/18
 * Time: 5:50 PM
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class AddressFormService extends BaseService
{
    private $id_country;
    private $address_form;
    private $id_address;
    private $address;
    private $id_customer;
    public $is_register_process;

    public function doExecute()
    {
        $this->id_country = (int)Tools::getValue('id_country');
        $country_iso_code = Tools::getValue('country_iso_code');
        $country_name = Tools::getValue('country_name');
        if (!$this->id_country && !empty($country_iso_code)) {
            $this->id_country = Country::getByIso($country_iso_code);
        }
        if (!$this->id_country && !empty($country_name)) {
            $this->id_country = Country::getIdByName($this->context->language->id, $country_name);
        }

        $this->id_customer = (int)Tools::getValue('id_customer');

        if (!$this->id_country) {
            if ($this->isV17()) {
                $this->id_country = $this->getDefaultCountry17();
            } else {
                $this->id_country = $this->getDefaultCountry16();
            }
        }
        $this->id_address = (int)Tools::getValue('id_address');
        if ($this->id_address) {
            $this->address = new Address($this->id_address);
            $this->id_country = $this->address->id_country;
        }

        $country = new Country($this->id_country);
        $this->context->country = $country;
        $lang = new LanguageCore($this->context->language->id);
        include_once(_PS_THEME_DIR_ . 'lang/' . $lang->iso_code . '.php');
        $this->translation = $GLOBALS['_LANG'];

        $response = new JmAddressFormResponse();
        if ($this->isV17()) {
            $response->address_fields = $this->getAddressFormV17();
        } else {
            $response->address_fields = $this->getAddressFormV16();
            $one_phone_at_least = (int)Configuration::get('PS_ONE_PHONE_AT_LEAST');
            $response->one_phone_at_least = $one_phone_at_least ? true : false;
            $response->tax_identification = array();
            if ((int)$country->need_identification_number && $this->is_register_process) {
                $address_field = new JmAddressField();
                $address_field->key = 'dni';
                $address_field->label = $this->getThemeTranslation('Identification number', 'address');
                $address_field->required = true;
                $address_field->type = 'text';
                $response->tax_identification[] = $address_field;
                $response->tax_identification_title = $this->getThemeTranslation('Tax identification', 'authentication');
            }
        }

        //assign values to address form
        if ($this->id_address) {
            $this->assignAddressValues($response->address_fields, $this->address);
        }

        /**
         * PS-915: Assign firstname, lastname
         */
        if ($this->context->customer->id) {
            foreach ($response->address_fields as &$field) {
                if (in_array($field->key, array('firstname', 'lastname'))) {
                    if (!$field->value) {
                        $field->value = $this->context->customer->{$field->key};
                    }
                }
            }
        }

        $this->response = $response;
    }

    public function getAddressFormV16()
    {
        $countries = $this->assignCountries();
        $selected_country = $countries[$this->id_country];

        $postCodeExist = false;
        $stateExist = false;
        $dniExist = false;
        $homePhoneExist = false;
        $mobilePhoneExist = false;
        $countryExist = false;

        $requireFormFieldsList = AddressFormat::getFieldsRequired();
        if ($this->id_country) {
            $ordered_adr_fields = AddressFormat::getOrderedAddressFields($this->id_country, true, true);
        } else {
            //get address format of default country
            $ordered_adr_fields = AddressFormat::getOrderedAddressFields(0, true, true);
        }
        $ordered_adr_fields = array_unique(array_merge($ordered_adr_fields, $requireFormFieldsList));
        $result = array();
        foreach ($ordered_adr_fields as $field) {
            $address_field = new JmAddressField();
            $address_field->key = $field;
            if (!$this->is_register_process
                || ($this->is_register_process
                    && !Configuration::get('PS_B2B_ENABLE'))) {
                if ($field === 'company') {
                    $address_field->label = $this->getThemeTranslation('Company', 'address');
                    $address_field->required = in_array($field, $requireFormFieldsList);
                    $address_field->type = 'text';
                    $address_field->value = $this->context->customer->company ? $this->context->customer->company : '';
                }
            }
            if ($field === 'vat_number') {
                $address_field->label = $this->getThemeTranslation('VAT number', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->type = 'text';
            }

            if ($field === 'firstname') {
                $address_field->label = $this->getThemeTranslation('First name', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->type = 'text';
                if ($this->id_customer) {
                    $address_field->value = $this->context->customer->firstname;
                }
            }
            if ($field === 'lastname') {
                $address_field->label = $this->getThemeTranslation('Last name', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->type = 'text';
                if ($this->id_customer) {
                    $address_field->value = $this->context->customer->lastname;
                }
            }
            if ($field === 'address1') {
                $address_field->label = $this->getThemeTranslation('Address', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->type = 'text';
            }
            if ($field === 'address2') {
                $address_field->label = $this->getThemeTranslation('Address (Line 2)', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->type = 'text';
            }
            if ($field === 'postcode') {
                $address_field->label = $this->getThemeTranslation('Zip/Postal Code', 'address');
                $address_field->required = true;
                $address_field->type = 'text';
                $postCodeExist = true;
            }
            if ($field === 'city') {
                $address_field->label = $this->getThemeTranslation('City', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->type = 'text';
            }
            if (($field === 'Country:name' || $field === 'country' || $field === 'Country:iso_code') && !$countryExist) {
                $address_field->key = 'id_country';
                $address_field->label = $this->getThemeTranslation('Country', 'address');
                $address_field->required = in_array($field, $requireFormFieldsList);
                $address_field->value = (String)$this->id_country;
                $address_field->type = 'list_select';
                $address_field->options = $this->convertCountryToOption($this->assignCountries());
                $countryExist = true;
            }
            if (($field === 'State:name' || $field === 'State:iso_code') && !$stateExist && $this->id_country != 0) {
                $address_field->key = 'id_state';
                $address_field->label = $this->getThemeTranslation('State', 'address');
                $address_field->required = true;
                $address_field->type = 'select';
                $stateExist = true;
                $address_field->options = $this->convertStateToOption($this->id_country);
            }
            if (!$this->is_register_process) {
                if ($field === 'dni') {
                    $address_field->label = $this->getThemeTranslation('Identification number', 'address');
                    $address_field->required = true;
                    $address_field->type = 'text';
                    $dniExist = true;
                }
                if ($field === 'phone') {
                    $address_field->label = $this->getThemeTranslation('Home phone', 'address');
                    $address_field->required = in_array($field, $requireFormFieldsList);
                    $address_field->type = 'text';
                    $homePhoneExist = true;
                }
                if ($field === 'phone_mobile') {
                    $address_field->label = $this->getThemeTranslation('Mobile phone', 'address');
                    $address_field->required = in_array($field, $requireFormFieldsList);
                    $address_field->type = 'text';
                    $mobilePhoneExist = true;
                }
            }
            if ($address_field->type) {
                $result[$address_field->key] = $address_field;
            }
        }
        if (!$postCodeExist) {
            $address_field = new JmAddressField();
            $address_field->key = 'postcode';
            $address_field->label = $this->getThemeTranslation('Zip/Postal Code', 'address');
            $address_field->required = true;
            $address_field->type = 'text';
            $result[$address_field->key] = $address_field;
        }
        if (!$stateExist && $this->id_country) {
            $address_field = new JmAddressField();
            $address_field->key = 'id_state';
            $address_field->label = $this->getThemeTranslation('State', 'address');
            $address_field->required = true;
            $address_field->type = 'select';
            $result[$address_field->key] = $address_field;
            $address_field->options = $this->convertStateToOption($this->id_country);
        }
        if (!$dniExist && !$this->is_register_process) {
            $address_field = new JmAddressField();
            $address_field->key = 'dni';
            $address_field->label = $this->getThemeTranslation('Identification number', 'address');
            $address_field->required = true;
            $address_field->type = 'text';
            $result[$address_field->key] = $address_field;
        }
        $address_field = new JmAddressField();
        $address_field->key = 'other';
        $address_field->label = $this->getThemeTranslation('Additional information', 'address');
        $address_field->required = false;
        $address_field->type = 'textarea';
        $result[$address_field->key] = $address_field;

        if (!$homePhoneExist) {
            $address_field = new JmAddressField();
            $address_field->key = 'phone';
            $address_field->label = $this->getThemeTranslation('Home phone', 'address');
            $address_field->required = in_array($address_field->key, $requireFormFieldsList);
            $address_field->type = 'text';
            $result[$address_field->key] = $address_field;
        }

        if (!$mobilePhoneExist) {
            $address_field = new JmAddressField();
            $address_field->key = 'phone_mobile';
            $address_field->label = $this->getThemeTranslation('Mobile phone', 'address');
            $address_field->required = in_array($address_field->key, $requireFormFieldsList);
            $address_field->type = 'text';
            $result[$address_field->key] = $address_field;
        }

        $address_field = new JmAddressField();
        $address_field->key = 'alias';
        $address_field->label = $this->getThemeTranslation('Please assign an address title for future reference.', 'address');
        $address_field->required = true;
        $address_field->type = 'text';
        $address_field->value = $this->getThemeTranslation('My address', 'address');
        $result[$address_field->key] = $address_field;

        if (!(int)$selected_country['need_zip_code']) {
            unset($result['postcode']);
        }
        $stateList = $this->id_country ? State::getStatesByIdCountry($this->id_country) : null;
        if (!(int)$selected_country['contains_states'] || !Country::containsStates($this->id_country) || empty($stateList)) {
            unset($result['id_state']);
        }
        if (!(int)$selected_country['need_identification_number']) {
            unset($result['dni']);
        }

        $result = array_values($result);
        $pos = 1;
        foreach ($result as &$field) {
            $field->position = $pos++;
        }
        return $result;
    }

    public function getAddressFormV17()
    {
        $param = array();
        $param['isolang'] = $this->context->language->iso_code;
        $param['id_lang'] = $this->context->language->id;
        $param['controller'] = 'address';

        $availableCountries = $this->assignCountries();

        $this->address_form = new CustomerAddressForm(
            $this->context->smarty,
            $this->context->language,
            $this->context->getTranslator(),
            $this->makeAddressPersister(),
            new CustomerAddressFormatter(
                $this->context->country,
                $this->context->getTranslator(),
                $availableCountries
            )
        );

        $customerAddressForm = $this->address_form->fillWith($param);
        $form_fields = $customerAddressForm->getFormatter()->getFormat();
        $response = $this->transformAddressForm($form_fields);
        return $response;
    }

    public function transformAddressForm($form_fields)
    {
        $result = array();
        $pos = 1;
        $country = new Country($this->id_country);
        foreach ($form_fields as $field) {
            if ($field->getType() !== 'hidden') {
                $address_field = new JmAddressField();
                $address_field->type = $field->getType();
                $address_field->required = $field->isRequired();
                $address_field->label = $field->getLabel();
                $address_field->key = $field->getName();
                if ($address_field->key === 'id_country') {
                    $address_field->type = 'list_select';
                    $address_field->value = (String)$this->id_country;
                    $address_field->options = $this->convertCountryToOption($this->assignCountries());
                }
                if ($address_field->key === 'id_state') {
                    $address_field->type = 'select';
                    $address_field->options = $this->convertStateToOption($this->id_country);
                }
                $address_field->position = $pos++;
                $result[] = $address_field;
            }
        }
        return $result;
    }

    protected function makeAddressPersister()
    {
        return new CustomerAddressPersister(
            $this->context->customer,
            $this->context->cart,
            Tools::getToken(true, $this->context)
        );
    }

    protected function assignCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $availableCountries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $availableCountries = Country::getCountries($this->context->language->id, true);
        }
        return $availableCountries;
    }

    public function convertCountryToOption($countryList)
    {
        $options = array();
        foreach ($countryList as $country) {
            $option = array();
            $option['id'] = $country['id_country'];
            $option['label'] = $country['name'];
            unset($country['states']);
            $option['data'] = $country;
            $options[] = $option;
        }
        return $options;
    }

    public function convertStateToOption($id_country)
    {
        $options = array();
        $states = State::getStatesByIdCountry($id_country);
        foreach ($states as $state) {
            $option = array();
            $option['id'] = $state['id_state'];
            $option['label'] = $state['name'];
            $options[] = $option;
        }
        // sort state according to state name.
        usort($options, array($this, "cmp"));
        return $options;
    }

    public function assignAddressValues(&$address_form, $address)
    {
        $address = (array)$address;
        foreach ($address_form as &$field) {
            $field->value = $address[$field->key];
        }
    }

    public function getDefaultCountry17()
    {

        $firstAddress = (int)Address::getFirstCustomerAddressId($this->id_customer);
        // address book exist => use first address's country
        if ($firstAddress) {
            $country = Address::getCountryAndState($firstAddress);
            return $country['id_country'];
        } else {
            // address book not exist => use default country
            $country = new Country(Configuration::get('PS_COUNTRY_DEFAULT'));
            if (!$country->active) {
                // default country is not actived
                return 0;
            }
            return Configuration::get('PS_COUNTRY_DEFAULT');
        }
    }

    public function getDefaultCountry16()
    {
        $firstAddress = (int)Address::getFirstCustomerAddressId($this->id_customer);
        $countries = array_values(Country::getCountries($this->context->language->id, true));
        if ($firstAddress) {
            // address book exist => use default country if it's active
            $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
            $country = new Country($id_country);
            if (!$country->active) {
                // default country is not actived
                // address book exist => use first country from country list
//                $id_country = $countries['0']['id_country'];
                return 0;
            }
        } else {
            // Address book not exist => use default address
            $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        $country = new Country($id_country);
        if (!$country->active) {
            // default country is not actived
            return 0;
        }
        return $id_country;
    }

    public function cmp($a, $b)
    {
        return strcmp($a['label'], $b['label']);
    }
}

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
 * API to get the address details in address edit form
 */

require_once 'AppCore.php';

class AppGetAddressDetails extends AppCore
{
    private $address = null;
    private $country = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $id_shipping = Tools::getValue('id_shipping_address', 0);
        $this->getFormDataWValues($id_shipping);
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
     * Check VAT number field is needed in address form
     *
     * @return bool
     */
    private function isNeedVat()
    {
        if (Module::isInstalled('vatnumber')
            && Module::getInstanceByName('vatnumber')->active
            && Configuration::get('VATNUMBER_MANAGEMENT')) {
            return true;
        }
        return false;
    }

    /**
     * Assign active countries on shop to the address form
     *
     * @return array of country list
     */
    public function assignCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        $country_list = array();
        $country_index = 0;
        foreach ($countries as $country) {
            $country_list[$country_index] = array(
                'id' => $country['id_country'],
                'name' => htmlentities($country['name'], ENT_COMPAT, 'UTF-8')
            );
            $country_index++;
        }

        $this->content['default_country_id'] = $this->country->id;
        return $country_list;
    }


    /**
     * Get form fields of address form with its values
     *
     * @param int $id_shipping address id
     */
    public function getFormDataWValues($id_shipping)
    {
        $address = new Address((int) $id_shipping);
        if (!validate::isLoadedObject($address)) {
            $this->content['shipping_address_reponse'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Unable to load address'),
                    'AppGetAddressDetails'
                )
            );
            $this->writeLog('Address object is not valid');
        } else {
            $id_country = $address->id_country;
            $this->country = new Country($id_country);
            $this->content['countries'] = $this->assignCountries();
            $form_data = array();
            $requireFormFieldsList = AddressFormat::getFieldsRequired();
            $ordered_adr_fields = AddressFormat::getOrderedAddressFields($id_country, true, true);
            $ordered_adr_fields = array_unique(array_merge($ordered_adr_fields, $requireFormFieldsList));
            $form_index = 0;
            foreach ($ordered_adr_fields as $field) {
                if ($field == 'vat_number') {
                    if ($this->isNeedVat()) {
                        $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('VAT Number'),
                            'AppGetAddressDetails'
                        );
                        $form_data[$form_index]['type'] = "textfield";
                        $form_data[$form_index]['name'] = "vat_number";
                        $form_data[$form_index]['value'] = $address->vat_number;
                        $form_data[$form_index]['required'] = (in_array($field, $requireFormFieldsList)) ? "1" : "0";
                        $form_data[$form_index]['validation'] = "";
                        $form_data[$form_index]['group_items'] = array();
                        $form_index++;
                    }
                } elseif ($field == 'dni') {
                    if ($this->isNeedDni()) {
                        $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Identification number'),
                            'AppGetAddressDetails'
                        );
                        $form_data[$form_index]['type'] = "textfield";
                        $form_data[$form_index]['name'] = "dni";
                        $form_data[$form_index]['value'] = $address->dni;
                        $form_data[$form_index]['required'] = "1";
                        $form_data[$form_index]['validation'] = "";
                        $form_data[$form_index]['group_items'] = array();
                        $form_index++;
                    }
                } elseif ($field == 'postcode') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Zip/Postal Code'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "postcode";
                    $form_data[$form_index]['value'] = $address->postcode;
                    $form_data[$form_index]['required'] = "1";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'firstname') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('First name'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "firstname";
                    $form_data[$form_index]['value'] = $address->firstname;
                    $form_data[$form_index]['required'] = "1";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'lastname') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Last name'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "lastname";
                    $form_data[$form_index]['value'] = $address->lastname;
                    $form_data[$form_index]['required'] = "1";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'address1') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Address'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "address1";
                    $form_data[$form_index]['value'] = $address->address1;
                    $form_data[$form_index]['required'] = "1";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'address2') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Address (Line 2)'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "address2";
                    $form_data[$form_index]['value'] = $address->address2;
                    $form_data[$form_index]['required'] = "";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'city') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('City'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "city";
                    $form_data[$form_index]['value'] = $address->city;
                    $form_data[$form_index]['required'] = (in_array($field, $requireFormFieldsList)) ? "1" : "0";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'Country:name' || $field == 'country' || $field == 'Country:iso_code') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Country'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "dropdownfield";
                    $form_data[$form_index]['name'] = "country";
                    $form_data[$form_index]['value'] = $address->id_country;
                    $form_data[$form_index]['required'] = "1";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'State:name') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('State'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "dropdownfield";
                    $form_data[$form_index]['name'] = "state";
                    $form_data[$form_index]['value'] = $address->id_state;
                    $form_data[$form_index]['required'] = "1";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'company') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Company'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "company";
                    $form_data[$form_index]['value'] = $address->company;
                    $form_data[$form_index]['required'] = (in_array($field, $requireFormFieldsList)) ? "1" : "0";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'phone') {
                        $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Home phone'),
                            'AppGetAddressDetails'
                        );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "phone";
                    $form_data[$form_index]['value'] = $address->phone;
                    $form_data[$form_index]['required'] = (int) Configuration::get('PS_ONE_PHONE_AT_LEAST') ? "1" : "0";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                } elseif ($field == 'phone_mobile') {
                    $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Mobile phone'),
                        'AppGetAddressDetails'
                    );
                    $form_data[$form_index]['type'] = "textfield";
                    $form_data[$form_index]['name'] = "phone_mobile";
                    $form_data[$form_index]['value'] = $address->phone_mobile;
                    $form_data[$form_index]['required'] = (int) Configuration::get('PS_ONE_PHONE_AT_LEAST') ? "1" : "0";
                    $form_data[$form_index]['validation'] = "";
                    $form_data[$form_index]['group_items'] = array();
                    $form_index++;
                }
            }
            $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Identification number'),
                'AppGetAddressDetails'
            );
            $form_data[$form_index]['type'] = "textfield";
            $form_data[$form_index]['name'] = "dni";
            $form_data[$form_index]['value'] = $address->dni;
            $form_data[$form_index]['required'] = "1";
            $form_data[$form_index]['validation'] = "";
            $form_data[$form_index]['group_items'] = array();
            $form_index++;
            $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Additional information'),
                'AppGetAddressDetails'
            );
            $form_data[$form_index]['type'] = "textarea";
            $form_data[$form_index]['name'] = "other";
            $form_data[$form_index]['value'] = $address->other;
            $form_data[$form_index]['required'] = "0";
            $form_data[$form_index]['validation'] = "";
            $form_data[$form_index]['group_items'] = array();
            $form_index++;
            $form_data[$form_index]['label'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Address title'),
                'AppGetAddressDetails'
            );
            $form_data[$form_index]['type'] = "textfield";
            $form_data[$form_index]['name'] = "alias";
            $form_data[$form_index]['value'] = $address->alias;
            $form_data[$form_index]['required'] = "1";
            $form_data[$form_index]['validation'] = "";
            $form_data[$form_index]['group_items'] = array();
            $this->content['shipping_address_reponse'] = array(
                'status' => 'success',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Address loaded successfully'),
                    'AppGetAddressDetails'
                ),
            );
            $this->writeLog('Address loaded successfully');
            $this->content['shipping_address_items'] = $form_data;
            $this->content['default_country_id'] = $address->id_country;
            $this->content['zipcode_required'] = $this->country->need_zip_code;
            if ($this->country->isNeedDni()) {
                $this->content['dni_required'] = "1";
            } else {
                $this->content['dni_required'] = "0";
            }
            if ($address->id_state == 0) {
                $this->content['default_state_id'] = "";
            } else {
                $this->content['default_state_id'] = (string) $address->id_state;
            }
        }
    }
}

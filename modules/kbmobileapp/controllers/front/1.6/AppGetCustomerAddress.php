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
 * API to get list of addresses of customer
 */

require_once 'AppCore.php';

class AppGetCustomerAddress extends AppCore
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
        
        $email = Tools::getValue('email', '');
        if (!Validate::isEmail($email)) {
            $this->content['shipping_address_reponse'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Email address is not valid'),
                    'AppGetCustomerAddress'
                )
            );
            $this->writeLog('Email address is not valid');
        } else {
            $this->getCustomerAddresses($email);
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Get list of addresses of customer
     *
     * @param string $email email address of the customer
     */
    public function getCustomerAddresses($email)
    {
        if (Customer::customerExists(strip_tags($email), false, false)) {
            $customer_obj = new Customer();
            $customer_tmp = $customer_obj->getByEmail($email, null, false);

            $customer = new Customer($customer_tmp->id);

            //Update Context
            $this->context->customer = $customer;
            $total = 0;
            $multiple_addresses = array();
            $addresses = $this->context->customer->getAddresses($this->context->language->id);
            if (!empty($addresses)) {
                foreach ($addresses as $detail) {
                    $address = new Address($detail['id_address']);
                    $multiple_addresses[$total]['id_shipping_address'] = $address->id;
                    $multiple_addresses[$total]['firstname'] = $address->firstname;
                    $multiple_addresses[$total]['lastname'] = $address->lastname;
                    $multiple_addresses[$total]['mobile_no'] = (!empty($address->phone_mobile)) ?
                        $address->phone_mobile . "," . $address->phone : $address->phone . "," . $address->phone_mobile;
                    $multiple_addresses[$total]['mobile_no'] = rtrim($multiple_addresses[$total]['mobile_no'], ',');
                    $multiple_addresses[$total]['company'] = $address->company;
                    $multiple_addresses[$total]['address_1'] = $address->address1;
                    $multiple_addresses[$total]['address_2'] = $address->address2;
                    $multiple_addresses[$total]['city'] = $address->city;
                    if ($address->id_state != 0) {
                        $multiple_addresses[$total]['state'] = State::getNameById($address->id_state);
                    } else {
                        $multiple_addresses[$total]['state'] = "";
                    }
                    $multiple_addresses[$total]['country'] = Country::getNameById(
                        $this->context->language->id,
                        $address->id_country
                    );
                    $multiple_addresses[$total]['postcode'] = $address->postcode;
                    $multiple_addresses[$total]['alias'] = $address->alias;
                    unset($address);
                    ++$total;
                }
                $this->content['default_address'] = '1';
            } else {
                $this->content['default_address'] = '0';
            }
            $this->content['shipping_address'] = $multiple_addresses;
        } else {
            $this->content['shipping_address_reponse'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer not exist for this email address.'),
                    'AppGetCustomerAddress'
                )
            );
            $this->writeLog('Customer not exist for this email address.');
        }
    }
}

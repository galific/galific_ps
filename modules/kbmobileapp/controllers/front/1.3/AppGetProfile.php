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
 * API to get the baisic information of customer
 */

require_once 'AppCore.php';

class AppGetProfile extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->validateCustomer();
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Validate customer and provide his basic information
     *
     * @return bool
     */
    public function validateCustomer()
    {
        $email = Tools::getValue('email', '');
        if (!Validate::isEmail($email)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is not valid'),
                'AppGetProfile'
            );
            $this->writeLog('Email address is not valid');
            return false;
        } else {
            if (Customer::customerExists(strip_tags($email))) {
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email);

                $customer = new Customer($customer_tmp->id);

                //Update Context
                $this->context->customer = $customer;
                $this->context->cookie->id_customer = (int) $customer->id;
                $this->context->cookie->customer_lastname = $customer->lastname;
                $this->context->cookie->customer_firstname = $customer->firstname;
                $this->context->cookie->passwd = $customer->passwd;
                $this->context->cookie->logged = 1;
                $this->context->cookie->email = $customer->email;
                $this->context->cookie->is_guest = $customer->is_guest;
                $customer_data = array();
                $genders = Gender::getGenders();
                $gender_index = 0;
                foreach ($genders as $gender) {
                    $customer_data['titles'][$gender_index] = array(
                        'id' => $gender->id,
                        'name' => 'gender',
                        'label' => $gender->name
                    );
                    $gender_index++;
                }
                $customer_data['firstname'] = $customer->firstname;
                $customer_data['lastname'] = $customer->lastname;
                $customer_data['email'] = $customer->email;
                $customer_data['dob'] = $customer->birthday;
                $customer_data['gender'] = $customer->id_gender;
                $this->content['personal_info'] = $customer_data;
                $this->content['status'] = "success";
                $this->content['message'] = "";
                return true;
            } else {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer with this email not exist'),
                    'AppGetProfile'
                );
                $this->writeLog('Customer with this email not exist');
                return false;
            }
        }
    }
}

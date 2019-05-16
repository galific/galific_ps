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
 * API to update customer basic information
 */

require_once 'AppCore.php';

class AppUpdateProfile extends AppCore
{
    private $product = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->updateCustomerInfo();
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }


    /**
     * Update customer information
     *
     */
    public function updateCustomerInfo()
    {
        $user_data = Tools::getValue('personal_info', Tools::jsonEncode(array()));
        $user_data = Tools::jsonDecode($user_data);
        $email = Tools::getValue('email', '');
        if ($email && Validate::isEmail($email)) {
            if (Customer::customerExists(strip_tags($email))) {
                $customer_obj = new Customer();
                $customer_tmp = $customer_obj->getByEmail($email);

                $customer = new Customer($customer_tmp->id);
                $customer_obj = new Customer();
                if (!empty($user_data)) {
                    $authentication = $customer_obj->getByEmail(trim($email), trim($user_data->password));
                    if (!Validate::isName($user_data->first_name)) {
                        $this->content['status'] = "failure";
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Invalid First Name.'),
                            'AppUpdateProfile'
                        );
                        $this->writeLog('Invalid First Name.');
                    } elseif (!Validate::isName($user_data->last_name)) {
                        $this->content['status'] = "failure";
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Invalid Last Name.'),
                            'AppUpdateProfile'
                        );
                        $this->content['message'] = "Invalid Last Name.";
                        $this->writeLog('Invalid Last Name.');
                    } elseif (!$user_data->password || !$authentication) {
                        $this->content['status'] = "failure";
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Authentication failed.'),
                            'AppUpdateProfile'
                        );
                        $this->writeLog('Authentication failed.');
                    } elseif (!empty($user_data->new_password)
                        && $user_data->new_password != $user_data->cnfrm_password) {
                        $this->content['status'] = "failure";
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('The password and confirmation do not match.'),
                            'AppUpdateProfile'
                        );
                        $this->writeLog('The password and confirmation do not match.');
                    } elseif (!empty($user_data->new_password) && !Validate::isPasswd($user_data->new_password)) {
                        $this->content['status'] = "failure";
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('Invalid Password'),
                            'AppUpdateProfile'
                        );
                        $this->writeLog('Invalid Password');
                    } else {
                        $customer->firstname = ucwords($user_data->first_name);
                        $customer->lastname = ucwords($user_data->last_name);
                        if (!empty($user_data->title)) {
                            $customer->id_gender = (int) $user_data->title;
                        }
                        if (!empty($user_data->new_password)) {
                            $passd = Tools::encrypt($user_data->new_password);
                            $customer->passwd = $passd;
                        }
                        if ($customer->update(true)) {
                            //Update Context
                            $this->context->customer = $customer;
                            $this->context->cookie->id_customer = (int) $customer->id;
                            $this->context->cookie->customer_lastname = $customer->lastname;
                            $this->context->cookie->customer_firstname = $customer->firstname;
                            $this->context->cookie->passwd = $customer->passwd;
                            $this->context->cookie->logged = 1;
                            $this->context->cookie->email = $customer->email;
                            $this->context->cookie->is_guest = $customer->is_guest;
                            $this->content['status'] = "success";
                            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('Personal Information updated successfully.'),
                                'AppUpdateProfile'
                            );
                            $this->writeLog('Personal Information updated successfully.');
                        } else {
                            $this->content['status'] = "failure";
                            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                                Tools::getValue('iso_code', false),
                                $this->l('The information cannot be updated.'),
                                'AppUpdateProfile'
                            );
                            $this->writeLog('The information cannot be updated.');
                        }
                    }
                } else {
                    $this->content['status'] = "failure";
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Personal information is missing.'),
                        'AppUpdateProfile'
                    );
                    $this->writeLog('Personal information is missing.');
                }
            } else {
                $this->content['status'] = "failure";
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Customer with this email is not exist.'),
                    'AppUpdateProfile'
                );
                $this->writeLog('Customer with this email is not exist.');
            }
        } else {
            $this->content['status'] = "failure";
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email address is missing or invalid.'),
                'AppUpdateProfile'
            );
            $this->writeLog('Email address is missing or invalid.');
        }
    }
}

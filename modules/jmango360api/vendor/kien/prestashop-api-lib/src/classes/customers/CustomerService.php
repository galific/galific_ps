<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CustomerService extends BaseService
{
    private $errors = array();
    public $translation;

    public function doExecute()
    {
        if ($this->isGetMethod()) {
            //get customer details
            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);

            if ($customer && $customer->id) {
                $this->response = new CustomerResponse();
                $this->response->customer = $customer;
            } else {
                $this->response = new JmResponse();
                $this->response->errors = array('Customer doest not exits!');
            }
        } elseif ($this->isPostMethod()) {
            //update customer info
            $this->response = new JmResponse();
            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);
            $old_customer = $customer;

            $request = json_decode($this->getRequestBody());

            $this->id_lang = Tools::getValue('id_lang');

            $lang = new LanguageCore($this->id_lang);
            include_once(_PS_THEME_DIR_.'lang/'.$lang->iso_code.'.php');
            $this->translation=$GLOBALS['_LANG'];


            if ($customer && $customer->id) {
                $birthdate = $request->birthDate;
                $email = $request->email;
                if (!Validate::isEmail($request->email)) {
                    $this->errors[] = Tools::displayError('This email address is not valid');
                }
                if ($customer->email != $email && Customer::customerExists($email, true)) {
                    $error = new JmError(500, 'An account using this email address has already been registered.');
                    $this->response->errors = array($error);
                    return;
                }
                $id_shop = $this->getRequestValue('id_shop');
                $gender = $request->id_gender;
                $firstname = $request->firstname;
                $lastname = $request->lastname;

                $customer->id_shop = empty($id_shop) ? $customer->id_shop : $id_shop;
                $customer->firstname = empty($firstname) ? $customer->firstname : $firstname;
                $customer->lastname = empty($lastname) ? $customer->lastname : $lastname;
                $customer->id_gender = empty($gender) ? $customer->id_gender : $gender;
                $customer->birthday = $birthdate;
                $customer->company = $request->company;
                $customer->siret = $request->siret;
                $customer->ape = $request->ape;
                $customer->newsletter = $request->newsletter;
                $customer->website = $request->website;
                $customer->email = $email;
                $customer->optin = $request->optin;
                $this->errors = array_merge($this->errors, $customer->validateController());
                if (!empty($this->errors)) {
                    $this->response->errors[] = new JmError(500, $this->formatErrors($this->errors));
                }
                try {
                    if (empty($this->errors)) {
                        $customer->update(false);
                        $this->response->customer = $customer;
                    } else {
                        $this->response->customer = $old_customer;
                    }
                } catch (PrestaShopDatabaseException $e) {
                    $this->response->errors = array('Failed to update customer info! ' . $e->getMessage());
                } catch (PrestaShopException $e) {
                    $this->response->errors = array('Failed to update customer info! ' . $e->getMessage());
                }
            } else {
                $this->response->errors = array('Customer doest not exits!');
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}

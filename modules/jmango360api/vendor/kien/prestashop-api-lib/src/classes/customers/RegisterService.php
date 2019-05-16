<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class RegisterService extends BaseService
{
    private $errors = array();
    public $translation;
    public $id_lang;

    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $request = json_decode($this->getRequestBody());
            $this->response = new CustomerResponse();
            $this->id_lang = Tools::getValue('id_lang');

            $lang = new LanguageCore($this->id_lang);
            include_once(_PS_THEME_DIR_ . 'lang/' . $lang->iso_code . '.php');
            $this->translation = $GLOBALS['_LANG'];

            //check email exist
            $email = $request->email;
            if (!Validate::isEmail($email)) {
                if ($this->isV17()) {
                    $this->response->errors[] = new JmError(500, $this->trans('Invalid email address.', array(), 'Admin.Notifications.Error'));
                } else {
                    $this->response->errors[] = new JmError(500, Tools::displayError('Invalid email address.'));
                }
                return;
            }
            $emailExist = CustomerCore::customerExists($email);

            if ($emailExist) {
                $this->response = new JmResponse();
                if ($this->isV17()) {
                    $this->response->errors[] = new JmError(500, $this->trans('The email "%mail%" is already used, please choose another one or sign in', array('%mail%' => $email), 'Shop.Notifications.Error'));
                } else {
                    $this->response->errors[] = new JmError(500, Tools::displayError('An account using this email address has already been registered.'));
                }
                return;
            } else {
                $id_shop = $this->getRequestValue('id_shop');
                $birthdate = $request->birthDate;
                $gender = $request->id_gender;
                $firstname = $request->firstname;
                $lastname = $request->lastname;
                $password = $request->passwd;
                $data_privacy = $request->data_privacy;

                if ($this->isV17()) {
                    $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
                    $moduleManager = $moduleManagerBuilder->build();

                    if ($moduleManager->isInstalled('ps_dataprivacy') && Module::getInstanceByName('ps_dataprivacy')->active) {
                        if ((bool)(strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_AUTH', $this->context->language->id))) && !$data_privacy) {
                            $this->errors[] = Translate::getModuleTranslation('blockcustomerprivacy', 'If you agree to the terms in the Customer Data Privacy message, please click the check box below.', 'blockcustomerprivacy');
                        }
                    }
                } else {
                    if (Module::isInstalled('blockcustomerprivacy') && Module::getInstanceByName('blockcustomerprivacy')->active) {
                        if ((bool)(strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_AUTH', $this->context->language->id))
                                && ConfigurationCore::get('CUSTPRIV_AUTH_PAGE'))
                            && !$data_privacy) {
                            $this->errors[] = Translate::getModuleTranslation('blockcustomerprivacy', 'If you agree to the terms in the Customer Data Privacy message, please click the check box below.', 'blockcustomerprivacy');
                        }
                    }
                }

                $customer = new Customer();
                $customer->id_shop = $id_shop;
                $customer->id_guest = false;
                $customer->email = $email;

                if ($this->isV17()) {
                    $hash = new \PrestaShop\PrestaShop\Core\Crypto\Hashing();
                    $hash->hash($password);
                    $customer->passwd = $hash->hash($password);
                } else {
                    $customer->passwd = Tools::encrypt($password);
                }

                $customer->firstname = $firstname;
                $customer->lastname = $lastname;
                $customer->id_gender = $gender;
                $customer->birthday = $birthdate;
                $customer->company = $request->company;
                $customer->siret = $request->siret;
                $customer->ape = $request->ape;
                $customer->newsletter = $request->newsletter;
                if ((int)$customer->newsletter) {
                    $customer->newsletter_date_add = pSQL(date('Y-m-d H:i:s'));
                }
                $customer->optin = $request->optin;
                $customer->website = $request->website;
                $customer->active = 1;
                $this->errors = array_merge($this->errors, $customer->validateController());

                if ($request->address) {
                    $customerAddressService = new CustomerAddressService();
                    $customerAddressService->company = $customer->company;
                    $address = $customerAddressService->createAddressFromRequest($customer->id);
                    $this->errors = array_merge($this->errors, $customerAddressService->errors);
                }

                if (!empty($this->errors)) {
                    $this->response->errors[] = new JmError(500, $this->formatErrors($this->errors));
                }
                try {
                    if (empty($this->errors)) {
                        $customer->add(true, true);
                        if (!$this->isJmCustomer($customer->id)) {
                            $this->addJmCustomer($customer->id);
                        }
                        if ($request->address) {
                            $address->id_customer = $customer->id;
                            $address->add();
                        }
                        try {
                            if (Configuration::get('PS_CUSTOMER_CREATION_EMAIL')) {
                                Mail::Send(
                                    Tools::getValue('id_lang'),
                                    'account',
                                    Mail::l('Welcome!'),
                                    array(
                                        '{firstname}' => $customer->firstname,
                                        '{lastname}' => $customer->lastname,
                                        '{email}' => $customer->email,
                                        '{passwd}' => Tools::getValue('passwd')
                                    ),
                                    $customer->email,
                                    $customer->firstname . ' ' . $customer->lastname
                                );
                            }
                        } catch (Exception $e) {
                        }
                        $this->response->customer = $customer;
                    } else {
                        $this->response->customer = null;
                    }
                } catch (PrestaShopDatabaseException $e) {
                    $this->response = new JmResponse();
                    $this->response->errors = array('Failed to create customer info! ' . $e->getMessage());
                } catch (PrestaShopException $e) {
                    $this->response = new JmResponse();
                    $this->response->errors = array('Failed to create customer info! ' . $e->getMessage());
                }
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}

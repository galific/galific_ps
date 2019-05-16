<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 6/14/18
 * Time: 5:49 PM
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class RegistrationFormService extends BaseService
{
    private $SHOP_THEME_CUSTOMER_ACCOUNT;
    private $MODULE_EMAIL_SUBSCRIPTIONSHOP;
    private $id_lang;
    private $result;
    public $translation;
    public $customer;

    public function doExecute()
    {
        $this->id_lang = Tools::getValue('id_lang');
        $lang = new LanguageCore($this->id_lang);
        include_once(_PS_THEME_DIR_ . 'lang/' . $lang->iso_code . '.php');
        $this->translation = $GLOBALS['_LANG'];

        if (version_compare(_PS_VERSION_, '1.7.0.5', '>')) {
            $this->SHOP_THEME_CUSTOMER_ACCOUNT = 'Shop.Theme.Customeraccount';
            $this->MODULE_EMAIL_SUBSCRIPTIONSHOP = 'Modules.Emailsubscription.Shop';
        } else {
            $this->SHOP_THEME_CUSTOMER_ACCOUNT = 'Shop.Theme.Customer.Account';
            $this->MODULE_EMAIL_SUBSCRIPTIONSHOP = 'Modules.Email.Subscription.Shop';
        }

        $this->customer = (array)$this->context->customer;
        foreach ($this->customer as $key => $value) {
            if (is_null($value)) {
                $this->customer[$key] = "";
            }
        }

        //set gender titles fields
        $response = new JmRegistrationFormResponse();
        $response->titles = $this->getTitles();
        $response->gender = $this->convertTitle($this->getTitles());

        $response->addressRequired = false;

        $response->userInfo = $this->setCustomerInfoFields();
        $response->b2bEnabled = Configuration::get('PS_B2B_ENABLE') && !$this->isV17() ? true : false;
        if (!$this->isV17() && (int)Configuration::get('PS_REGISTRATION_PROCESS_TYPE') == PS_REGISTRATION_PROCESS_AIO && !$this->context->customer->id) {
            $addressFormService = new AddressFormService();
            $addressFormService->is_register_process = 1;
            $addressFormService->doExecute();
            $response->address_fields = $addressFormService->response->address_fields;
            $response->addressRequired = true;
            $response->tax_identification = $addressFormService->response->tax_identification;
            $response->tax_identification_title = $addressFormService->response->tax_identification_title;
        }

        if (!$this->isV17()) {
            $response->data_privacy_block = array();
            if (Module::isInstalled('blockcustomerprivacy') && Module::getInstanceByName('blockcustomerprivacy')->active) {
                if ((bool)(strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_AUTH', $this->context->language->id)) && ConfigurationCore::get('CUSTPRIV_AUTH_PAGE')) && !$this->context->customer->id) {
                    $privacy_fields = $this->createField('data_privacy', strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_AUTH', $this->context->language->id)), true, 14);
                    $privacy_fields['type'] = 'radio';
                    $response->data_privacy_block[] = $privacy_fields;
                }
            }
            $response->personal_info_title = $this->getThemeTranslation('Your personal information', 'authentication');
            $response->company_info_title = $this->getThemeTranslation('Your company information', 'authentication');
            $response->address_title = $this->getThemeTranslation('Your address', 'authentication');
            $response->data_privacy_block_title = Translate::getModuleTranslation('blockcustomerprivacy', 'Customer data privacy', 'blockcustomerprivacy');
        }

        if (Configuration::get('PS_B2B_ENABLE')) {
            if ($this->isV17()) {
                $response->userInfo = array_merge($response->userInfo, $this->setCompanyInfoFields());
            } else {
                $response->companyInfo = $this->setCompanyInfoFields();
            }
        }

        $this->response = $response;
    }

    public function getTitles()
    {
        $sql = 'SELECT * 
                FROM ' . _DB_PREFIX_ . 'gender_lang
                WHERE `id_lang` = ' . $this->id_lang;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $result;
    }

    public function setCustomerInfoFields()
    {
        $fields = array();
        if (!$this->isV17()) {
            $fields[] = $this->createField('firstname', $this->getThemeTranslation('First name', 'authentication'), true, 2);
            $fields[] = $this->createField('lastname', $this->getThemeTranslation('Last name', 'authentication'), true, 3);
            $fields[] = $this->createField('email', $this->getThemeTranslation('Email', 'authentication'), true, 4);
            if (!$this->context->customer->id) {
                $fields[] = $this->createField('password', $this->getThemeTranslation('Password', 'authentication'), true, 5);
//                $fields[] = $this->createField('confirmation', $this->getThemeTranslation('Confirm Password', 'authentication'), true, 6);
            }
            $birthDateField = $this->createField('birthDate', $this->getThemeTranslation('Date of Birth', 'authentication'), false, 7);
            $birthDateField['type'] = 'date';
            $fields[] = $birthDateField;
        } else {
            $fields[] = $this->createField('firstname', $this->getThemeTranslation('First name', 'Admin.Global'), true, 2);
            $fields[] = $this->createField('lastname', $this->getThemeTranslation('Last name', 'Admin.Global'), true, 3);
            $fields[] = $this->createField('email', $this->getThemeTranslation('Email', 'Admin.Global'), true, 6);
            if (!$this->context->customer->id) {
                $fields[] = $this->createField('password', $this->getThemeTranslation('Password', 'Admin.Global'), true, 7);
//                $fields[] = $this->createField('confirmation', $this->getThemeTranslation('Confirm Password', 'Admin.Global'), true, 8);
            }
            if (Configuration::get('PS_CUSTOMER_BIRTHDATE')) {
                $birthDateField = $this->createField('birthDate', $this->getThemeTranslation('Date of birth', 'Admin.Global'), false, 9);
                $birthDateField['type'] = 'date';
                $fields[] = $birthDateField;
            }
        }

        if ($this->isV17()) {
            //Opt-in
            if ((bool)Configuration::get('PS_CUSTOMER_OPTIN')) {
                $opt_in_message = $this->getThemeTranslation('Receive offers from our partners', $this->SHOP_THEME_CUSTOMER_ACCOUNT);
                $optin_fields = $this->createField('optin', $opt_in_message, false, 12);
                $optin_fields['type'] = 'radio';
                $fields[] = $optin_fields;
            }

            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();

            //Newsletter
            $newsletter = ($moduleManager->isInstalled('ps_emailsubscription') && Module::getInstanceByName('ps_emailsubscription')->active);
            if ((bool)$newsletter) {
                $newsletter_condition_msg = Configuration::get('NW_CONDITIONS', $this->context->language->id);
                $newsletter_msg = $this->trans(
                    'Sign up for our newsletter[1][2]%conditions%[/2]',
                    array(
                        '[1]' => $newsletter_condition_msg ? "\n" : '',
                        '[2]' => '',
                        '%conditions%' => $newsletter_condition_msg,
                        '[/2]' => '',
                    ),
                    $this->MODULE_EMAIL_SUBSCRIPTIONSHOP
                );
                $newsletter_fields = $this->createField('newsletter', $newsletter_msg, false, 13);
                $newsletter_fields['type'] = 'radio';
                $fields[] = $newsletter_fields;
            }

            //Customer privacy block
            if ($moduleManager->isInstalled('ps_dataprivacy') && Module::getInstanceByName('ps_dataprivacy')->active) {
                if ((bool)(strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_AUTH', $this->context->language->id)))) {
                    $privacy_message = $this->trans(
                        'Customer data privacy[1][2]%message%[/2]',
                        array(
                            '[1]' => "\n",
                            '[2]' => '',
                            '%message%' => strip_tags(Configuration::get('CUSTPRIV_MSG_AUTH', $this->context->language->id)),
                            '[/2]' => '',
                        ),
                        'Modules.Dataprivacy.Shop'
                    );
                    if (!$this->context->customer->id) {
                        $privacy_fields = $this->createField('data_privacy', $privacy_message, true, 14);
                        $privacy_fields['type'] = 'radio';
                        $fields[] = $privacy_fields;
                    } else {
                        $privacy_label = $this->createField('data_privacy_label', $privacy_message, false, 99);
                        $privacy_label['type'] = 'radio';
                        $fields[] = $privacy_label;
                    }
                }
            }
        } else {
            //Opt-in
            if ((bool)Configuration::get('PS_CUSTOMER_OPTIN')) {
                $optin_fields = $this->createField('optin', $this->getThemeTranslation('Receive special offers from our partners!', 'authentication'), false, 12);
                $optin_fields['type'] = 'radio';
                $fields[] = $optin_fields;
            }

            //Newsletter
            $newsletter = Configuration::get('PS_CUSTOMER_NWSL') || (Module::isInstalled('blocknewsletter') && Module::getInstanceByName('blocknewsletter')->active);
            if ((bool)$newsletter) {
                $newsletter_fields = $this->createField('newsletter', $this->getThemeTranslation('Sign up for our newsletter!', 'authentication'), false, 11);
                $newsletter_fields['type'] = 'radio';
                $fields[] = $newsletter_fields;
            }
            //Customer privacy block
            if (Module::isInstalled('blockcustomerprivacy') && Module::getInstanceByName('blockcustomerprivacy')->active) {
                if ((bool)(strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_IDENTITY', $this->context->language->id)) && ConfigurationCore::get('CUSTPRIV_IDENTITY_PAGE')) && $this->context->customer->id) {
                    $privacy_label = $this->createField('data_privacy_label', strip_tags(ConfigurationCore::get('CUSTPRIV_MSG_IDENTITY', $this->context->language->id)), false, 99);
                    $privacy_label['type'] = 'label';
                    $fields[] = $privacy_label;
                }
            }
        }

        return $fields;
    }

    public function setCompanyInfoFields()
    {
        $fields = array();
        if (!$this->isV17()) {
            $fields[] = $this->createField('company', $this->getThemeTranslation('Company', 'authentication'), false, 15);
            $fields[] = $this->createField('siret', $this->getThemeTranslation('SIRET', 'authentication'), false, 16);
            $fields[] = $this->createField('ape', $this->getThemeTranslation('APE', 'authentication'), false, 17);
            $fields[] = $this->createField('website', $this->getThemeTranslation('Website', 'authentication'), false, 18);
        } else {
            $fields[] = $this->createField('company', $this->getThemeTranslation('Company', 'Admin.Global'), false, 4);
            $fields[] = $this->createField('siret', $this->getThemeTranslation('Identification number', 'Shop.Forms.Labels'), false, 5);
        }
        return $fields;
    }

    public function createField($key, $label, $required, $position)
    {
        $field = array();
        $field['type'] = 'text';
        $field['key'] = $key;
        $field['label'] = $label;
        $field['required'] = $required;
        $field['position'] = $position;
        if ($key == 'companyName') {
            $field['value'] = $this->customer['company'];
        } elseif ($key == 'companyCoCNumber') {
            $field['value'] = $this->customer['siret'];
        } elseif ($key == 'companyVatNumber') {
            $field['value'] = $this->customer['ape'];
        } elseif ($key == 'birthDate') {
            $field['value'] = $this->customer['birthday'] !== '0000-00-00' ? $this->customer['birthday'] : '';
        } elseif ($key == 'firstname') {
            $field['value'] = $this->customer['firstname'];
        } elseif ($key == 'lastname') {
            $field['value'] = $this->customer['lastname'];
        } else {
            $field['value'] = isset($this->customer[$key]) ? $this->customer[$key] : '';
        }
        return $field;
    }

    public function convertTitle($titles)
    {
        $field = array();
        $field['type'] = "select";
        $field['key'] = "id_gender";
        if ($this->isV17()) {
            $field['label'] = $this->getThemeTranslation('Social title', 'Admin.Global');
        } else {
            $field['label'] = $this->getThemeTranslation('Title', 'authentication');
        }
        $field['required'] = false;
        $field['position'] = 1;
        $field['options'] = $titles;
        $field['value'] = $this->customer['id_gender'] ? $this->customer['id_gender'] : '';
        return $field;
    }
}

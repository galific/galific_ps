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
 * API to send forgot passowrd email to customer
 * Called from login page in APP
 */

require_once 'AppCore.php';

class AppForgotPassword extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * Send the forgot passowrd mail to the customer
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        if (Tools::getIsset('email')) {
            if (!($email = trim(Tools::getValue('email'))) || !Validate::isEmail($email)) {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Invalid email address.'),
                    'AppForgotPassword'
                );
                $this->writeLog('Invalid email address.');
            } else {
                $customer = new Customer();
                $customer->getByemail($email);
                $min_time = (int) Configuration::get('PS_PASSWD_TIME_FRONT');
                if (!Validate::isLoadedObject($customer)) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('There is no account registered for this email address.'),
                        'AppForgotPassword'
                    );
                    $this->writeLog('There is no account registered for this email address.');
                } elseif (!$customer->active) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('You cannot regenerate the password for this account.'),
                        'AppForgotPassword'
                    );
                    $this->writeLog('Cannot regenerate the password for this account as the account is not active.');
                } elseif ((strtotime($customer->last_passwd_gen . '+' . ($min_time) . ' minutes') - time()) > 0) {
                    $this->content['status'] = 'failure';
                    $this->content['message'] = sprintf(
                        parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('You can regenerate your password only every %d minute(s)'),
                            'AppForgotPassword'
                        ),
                        (int) $min_time
                    );
                    $this->writeLog('Password is generated after ' . (int) $min_time);
                } else {
                    $mail_params = array(
                        '{email}' => $customer->email,
                        '{lastname}' => $customer->lastname,
                        '{firstname}' => $customer->firstname,
                        '{url}' => $this->context->link->getPageLink(
                            'password',
                            true,
                            null,
                            'token=' . $customer->secure_key . '&id_customer=' . (int) $customer->id
                        )
                    );
                    if (Mail::Send(
                        $this->context->language->id,
                        'password_query',
                        Mail::l('Password query confirmation'),
                        $mail_params,
                        $customer->email,
                        $customer->firstname . ' ' . $customer->lastname
                    )) {
                        $this->content['status'] = 'success';
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('A confirmation email has been sent to your address: '),
                            'AppForgotPassword'
                        );
                        $this->content['message'] = $this->content['message'] .  $customer->email;
                        $this->writeLog('An error occurred while sending the email.');
                    } else {
                        $this->content['status'] = 'failure';
                        $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                            Tools::getValue('iso_code', false),
                            $this->l('An error occurred while sending the email.'),
                            'AppForgotPassword'
                        );
                        $this->writeLog('An error occurred while sending the email.');
                    }
                }
            }
        } else {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Email parameter is not set'),
                'AppForgotPassword'
            );
            $this->writeLog('Email parameter is not set');
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }
}

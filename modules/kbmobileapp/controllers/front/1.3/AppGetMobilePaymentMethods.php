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
 * API to get mobile payment methods configured by admin
 */

require_once 'AppCore.php';

class AppGetMobilePaymentMethods extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->getPaymentmethods();
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /*
     * Function to get the active payment methods
     */
    public function getPaymentmethods()
    {
        if (Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')) {
            $payment_data = Tools::unserialize((Configuration::get('KB_MOBILEAPP_PAYMENT_METHODS')));
            $payment_method_array = array();
            $index = 0;
            foreach ($payment_data as $data) {
                if ($data['status'] == 1) {
                    $lang_code = Tools::getValue('iso_code', $this->context->language->iso_code);
                    $payment_method_array[$index] = array (
                        'payment_method_name' => $data['payment_name'][$lang_code],
                        'payment_method_code' => $data['payment_code'],
                        'configuration' => array(
                                'payment_method_mode' => $data['payment_mode'],
                                'client_id' => $data['client_id'],
                                'is_default' => 'no',
                                'other_info' => $data['other_info']
                            )
                    );
                    $index++;
                }
            }
            
            if (count($payment_method_array)) {
                $this->content['status'] = 'success';
                $this->content['message'] = '';
                $this->content['payments'] = $payment_method_array;
                $this->writeLog('Set configured payment methods.');
            } else {
                $this->content['status'] = 'failure';
                $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('No Payment methods is enabled'),
                    'AppGetMobilePaymentMethods'
                );
                $this->writeLog('No Payment methods is enabled.');
            }
        } else {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('No Payment methods has been configured'),
                'AppGetMobilePaymentMethods'
            );
            $this->writeLog('No Payment methods has been configured.');
        }
    }
}

<?php
/**
 * @author Jmango360
 * @copyright 2018 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class PaymentCarrierService
 */
class PaymentCarrierService extends BaseService
{
    const JM360_EXCLUDE_PAYMENTS_CARRIERS = 'JM360_EXCLUDE_PAYMENTS_CARRIERS';

    /**
     * Implement business logic
     */
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            // Get setting
            $this->response = $this->getSettings();
        } elseif ($this->isPostMethod()) {
            // Save setting
            $this->response = $this->saveSettings();
        }
    }

    /**
     * Get payment and carrier methods
     *
     * @return PaymentCarrierResponse|JmResponse
     */
    public function getSettings()
    {
        $response = new PaymentCarrierResponse();

        $excluded = json_decode(Configuration::get(self::JM360_EXCLUDE_PAYMENTS_CARRIERS), true);
        $excludedPayments = isset($excluded['payments']) ? $excluded['payments'] : array();
        $excludedCarriers = isset($excluded['carriers']) ? $excluded['carriers'] : array();

        $langId = $this->getRequestValue('id_lang');
        if (!$langId) {
            $languages = Language::getLanguages();
            if (!count($languages)) {
                $response = new JmResponse();
                $response->errors = array('No language found!');
                return $response;
            }
            $langId = $languages[0]['id_lang'];
        }

        $carriers = Carrier::getCarriers($langId, true, false, false, null, Carrier::ALL_CARRIERS);
        foreach ($carriers as $carrier) {
            $response->carriers[] = array(
                'id' => $carrier['id_carrier'],
                'name' => $carrier['name'],
                'excluded' => in_array($carrier['id_carrier'], $excludedCarriers) ? true : false
            );
        }

        $payments = PaymentModule::getInstalledPaymentModules();
        foreach ($payments as $payment) {
            try {
                $module = Module::getInstanceById($payment['id_module']);
                $response->payments[] = array(
                    'id' => $payment['name'],
                    'name' => $module->displayName,
                    'excluded' => in_array($payment['name'], $excludedPayments) ? true : false
                );
            } catch (Exception $e) {
                continue;
            }
        }

        return $response;
    }

    /**
     * Set excluded payment and carrier methods
     *
     * @return PaymentCarrierResponse|JmResponse
     */
    public function saveSettings()
    {
        $shopId = $this->getRequestValue('id_shop');
        $payload = $this->retrievePayload();
        try {
            $data = array(
                'carriers' => array(),
                'payments' => array()
            );
            if ($payload['carriers']) {
                $data['carriers'] = $payload['carriers'];
            }
            if ($payload['payments']) {
                $data['payments'] = $payload['payments'];
            }
            if (!isset($data['carriers']) && !isset($data['payments'])) {
                $response = new JmResponse();
                $response->errors = array('Data invalid!');
                return $response;
            }
            Configuration::updateValue(self::JM360_EXCLUDE_PAYMENTS_CARRIERS, json_encode($data), false, null, $shopId);
        } catch (Exception $e) {
            $response = new JmResponse();
            $response->errors = array('Data invalid!');
            return $response;
        }

        return $this->getSettings();
    }

    /**
     * Get excluded payment and carrier methods from storage
     *
     * @return array
     */
    public static function getExcludedPaymentsCarriers()
    {
        try {
            return json_decode(Configuration::get(self::JM360_EXCLUDE_PAYMENTS_CARRIERS), true);
        } catch (Exception $e) {
            return array();
        }
    }
}

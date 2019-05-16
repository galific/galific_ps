<?php
/**
 * Created by PhpStorm.
 * User: JMango
 * Date: 4/16/18
 * Time: 13:42
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class EcommService extends BaseService
{
    const JM_COUPON_FOR_ONEPAGE = 'JM_COUPON_FOR_ONEPAGE';
    const JM_COUPON_FOR_NATIVE = 'JM_COUPON_FOR_NATIVE';

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

    public function getSettings()
    {
        $ecommResponse = new EcommResponse();
        $ecommResponse->enableCouponForOnePage = Configuration::hasKey(EcommService::JM_COUPON_FOR_ONEPAGE) ?
            Configuration::get(EcommService::JM_COUPON_FOR_ONEPAGE) : "1";
        $ecommResponse->enableCouponForNative = Configuration::hasKey(EcommService::JM_COUPON_FOR_NATIVE) ?
            Configuration::get(EcommService::JM_COUPON_FOR_NATIVE) : "1";
        return $ecommResponse;
    }

    public function saveSettings()
    {
        $payload = $this->retrievePayload();
        if (array_key_exists('enableCouponForOnePage', $payload)) {
            Configuration::updateGlobalValue(EcommService::JM_COUPON_FOR_ONEPAGE, $payload['enableCouponForOnePage']);
        }
        if (array_key_exists('enableCouponForNative', $payload)) {
            Configuration::updateGlobalValue(EcommService::JM_COUPON_FOR_NATIVE, $payload['enableCouponForNative']);
        }
        return $this->getSettings();
    }

    public function retrievePayload()
    {
        $request_body = Tools::file_get_contents('php://input');
        $payload = json_decode($request_body, true);
        return $payload;
    }
}

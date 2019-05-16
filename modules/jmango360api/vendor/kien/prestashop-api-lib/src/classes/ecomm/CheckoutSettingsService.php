<?php
/**
 * @author Jmango360
 * @copyright 2018 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class CheckoutSettingsService
 */
class CheckoutSettingsService extends BaseService
{
    const JM360_CHECKOUT_CUSTOM_CSS = 'JM360_CHECKOUT_CUSTOM_CSS';
    const JM360_CHECKOUT_CUSTOM_JS = 'JM360_CHECKOUT_CUSTOM_JS';

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
     * Get checkout settings
     *
     * @return CheckoutSettingsResponse
     */
    public function getSettings()
    {
        $response = new CheckoutSettingsResponse();

        $customCss = Configuration::get(self::JM360_CHECKOUT_CUSTOM_CSS);
        $response->css = $customCss ? $customCss : '';

        $customJs = Configuration::get(self::JM360_CHECKOUT_CUSTOM_JS);
        $response->js = $customJs ? $customJs : '';

        return $response;
    }

    /**
     * Set checkout settings
     *
     * @return CheckoutSettingsResponse|JmResponse
     */
    public function saveSettings()
    {
        $shopId = $this->getRequestValue('id_shop');
        $payload = $this->retrievePayload();
        try {
            if (isset($payload['css'])) {
                Configuration::updateValue(self::JM360_CHECKOUT_CUSTOM_CSS, $payload['css'], false, null, $shopId);
            }
            if (isset($payload['js'])) {
                Configuration::updateValue(self::JM360_CHECKOUT_CUSTOM_JS, $payload['js'], false, null, $shopId);
            }
        } catch (Exception $e) {
            $response = new JmResponse();
            $response->errors = array('Could not save data!');
            return $response;
        }

        return $this->getSettings();
    }

    /**
     * Get checkout custom CSS
     *
     * @return string
     */
    public static function getCheckoutCustomCss()
    {
        $css = Configuration::get(self::JM360_CHECKOUT_CUSTOM_CSS);
        return $css ? $css : '';
    }

    /**
     * Get checkout custom JS
     *
     * @return string
     */
    public static function getCheckoutCustomJs()
    {
        $js = Configuration::get(self::JM360_CHECKOUT_CUSTOM_JS);
        return $js ? $js : '';
    }
}

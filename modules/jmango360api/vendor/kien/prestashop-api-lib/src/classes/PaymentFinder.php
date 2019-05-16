<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

//namespace jmango360api\classes\checkout;

class PaymentFinder
{
    protected $params;
    protected $hookName;
    protected $expectedInstanceClasses;

    /**
     * Copy from PaymentOptionsFinderCore::find()
     *
     * @param Cart $cart
     * @return array
     * @throws WebserviceException
     */
    public function getPaymentOptions(Cart $cart)
    {
        if (!$cart || !$cart->id) {
            throw new WebserviceException('Cart not found', 400);
        }

        /**
         * Provide cart object for all payments's hook
         */
        $this->setParams(array('cart' => $cart));

        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $paymentOptions = $this->_getPaymentOptions17();
        } elseif (version_compare(_PS_VERSION_, '1.6', '>=')) {
            $paymentOptions = $this->_getPaymentOptions15();
            $paymentOptions += $this->_getPaymentOptions16();
        } else {
            $paymentOptions = $this->_getPaymentOptions15();
        }

        return $paymentOptions;
    }

    /**
     * Execute hook specified in params and check if the result matches the expected classes if asked.
     *
     * @return array Content returned by modules
     *
     * @throws \Exception if class doesn't match interface or expected classes
     */
    public function find()
    {
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $hookFinder = new PrestaShopBundle\Service\Hook\HookFinder();
            $hookFinder->setHookName($this->hookName);
            $hookFinder->setParams($this->params);
            $hookFinder->setExpectedInstanceClasses($this->expectedInstanceClasses);
            return $hookFinder->find();
        }
        $hookContent = Hook::exec($this->hookName, $this->params, null, true);

        if (!is_array($hookContent)) {
            $hookContent = array();
        }
        return $hookContent;
    }

    /**
     * Replace the params array.
     *
     * @param array $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get available payment options for PS 15
     */
    protected function _getPaymentOptions15()
    {
        $this->hookName = 'displayPayment';

        $content = Context::getContext();
        $protocol_link = Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://';

        /**
         * PS-989: Prepare env for Paypal payment
         */
        $content->smarty->assign(array(
            'link' => $content->link,
            'cart' => $content->cart,
            'lang_iso' => $content->language->iso_code,
            'currency' => Tools::setCurrency($content->cookie),
            'base_dir' => _PS_BASE_URL_ . __PS_BASE_URI__,
            'base_dir_ssl' => $protocol_link . Tools::getShopDomainSsl() . __PS_BASE_URI__,
            'force_ssl' => Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE')
        ));

        /**
         * PS-988: Prepare env for Stripe payment
         */
        if (Module::isInstalled('stripe_official')) {
            if (!$content->controller) {
                $content->controller = new FrontController();
            }
        }

        $rawDisplayPaymentOptions = $this->find();

        $output = array();
        $id = 0;
        foreach ($rawDisplayPaymentOptions as $moduleId => $rawDisplayPaymentOption) {
            $option = $this->_parsePaymentFromHtml($rawDisplayPaymentOption);
            if (empty($option['url'])) {
                continue;
            }
            $output[] = array(
                'id' => ++$id,
                'module_id' => $moduleId,
                'title' => isset($option['title']) ? $option['title'] : null,
                'description' => isset($option['description']) ? $option['description'] : null,
                'logo' => isset($option['logo']) ? $option['logo'] : null,
                'url' => isset($option['url']) ? $option['url'] : null,
                'inputs' => null,
                'form' => null
            );
        }

        return $output;
    }

    /**
     * Parse payment data from html, support PS 1.5
     *
     * @param $html
     * @return array
     * @throws
     */
    protected function _parsePaymentFromHtml($html)
    {
        if (!$html) {
            return array();
        }

        $doc = new DOMDocument();

        // Set error level to ignore some warnings
        $internalErrors = libxml_use_internal_errors(true);

        if (function_exists('mb_convert_encoding')) {
            $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        } elseif (function_exists('iconv')) {
            $string = iconv('utf-8//TRANSLIT//IGNORE', 'HTML-ENTITIES', $html);
        } else {
            throw new Swift_SwiftException('No suitable convert encoding function (use UTF-8 as your charset or install the mbstring or iconv extension).');
        }

        // Restore error level
        libxml_use_internal_errors($internalErrors);

        $xpath = new DOMXPath($doc);
        $output = array();

        $paymentElms = $xpath->query('//p[contains(@class,"payment_module")]');
        foreach ($paymentElms as $paymentElm) {
            $linkElms = $xpath->query('descendant::a', $paymentElm);
            foreach ($linkElms as $linkElm) {
                foreach ($linkElm->attributes as $attribute) {
                    if ($attribute->name == 'title') {
                        $output['title'] = trim($attribute->value);
                    } elseif ($attribute->name == 'href') {
                        $output['url'] = trim($attribute->value);
                    }
                }
                $output['description'] = trim($linkElm->nodeValue);
            }
            $imgElms = $xpath->query('descendant::img', $paymentElm);
            foreach ($imgElms as $imgElm) {
                foreach ($imgElm->attributes as $attribute) {
                    if ($attribute->name == 'src') {
                        $output['logo'] = trim($attribute->value);
                    } elseif ($attribute->name == 'title') {
                        if (empty($output['title'])) {
                            $output['title'] = trim($attribute->value);
                        }
                    } elseif ($attribute->name == 'alt') {
                        if (empty($output['title'])) {
                            $output['title'] = trim($attribute->value);
                        }
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Get available payment options for PS 16
     */
    protected function _getPaymentOptions16()
    {
        $this->hookName = 'displayPaymentEU';
        $rawDisplayPaymentEUOptions = $this->find();

        if (!is_array($rawDisplayPaymentEUOptions)) {
            $rawDisplayPaymentEUOptions = array();
        }

        $output = array();
        $id = 0;
        foreach ($rawDisplayPaymentEUOptions as $moduleId => $option) {
            if (empty($option['cta_text']) & empty($option['logo'])) {
                continue;
            }
            $output[] = array(
                'id' => ++$id,
                'module_id' => $moduleId,
                'title' => isset($option['cta_text']) ? $option['cta_text'] : null,
                'description' => null,
                'logo' => isset($option['logo']) ? $option['logo'] : null,
                'url' => isset($option['action']) ? $option['action'] : null,
                'inputs' => null,
                'form' => null
            );
        }

        return $output;
    }

    /**
     * Get available payment options for PS 17
     */
    protected function _getPaymentOptions17()
    {
        // Payment options coming from intermediate, deprecated version of the Advanced API
        $this->hookName = 'displayPaymentEU';
        $rawDisplayPaymentEUOptions = $this->find();

        if (!is_array($rawDisplayPaymentEUOptions)) {
            $rawDisplayPaymentEUOptions = array();
        }

        $displayPaymentEUOptions = array_map(
            array('PrestaShop\PrestaShop\Core\Payment\PaymentOption', 'convertLegacyOption'),
            $rawDisplayPaymentEUOptions
        );

        // Payment options coming from regular Advanced API
        $this->hookName = 'advancedPaymentOptions';
        $advancedPaymentOptions = $this->find();
        if (!is_array($advancedPaymentOptions)) {
            $advancedPaymentOptions = array();
        }

        // Payment options coming from regular Advanced API
        $this->hookName = 'paymentOptions';
        $this->expectedInstanceClasses = array('PrestaShop\PrestaShop\Core\Payment\PaymentOption');
        $newOption = $this->find();
        if (!is_array($newOption)) {
            $newOption = array();
        }

        $paymentOptions = array_merge($displayPaymentEUOptions, $advancedPaymentOptions, $newOption);

        foreach ($paymentOptions as $paymentOptionKey => $paymentOption) {
            if (!is_array($paymentOption)) {
                unset($paymentOptions[$paymentOptionKey]);
            }
        }

        $output = array();
        $id = 0;
        foreach ($paymentOptions as $moduleId => $payment) {
            foreach ($payment as $option) {
                /* @var $option PrestaShop\PrestaShop\Core\Payment\PaymentOption */
                $output[] = array(
                    'id' => ++$id,
                    'module_id' => $moduleId,
                    'title' => $option->getCallToActionText(),
                    'description' => $option->getAdditionalInformation(),
                    'logo' => $option->getLogo(),
                    'url' => $option->getAction(),
                    'inputs' => $option->getInputs(),
                    'form' => $option->getForm()
                );
            }
        }

        return $output;
    }
}

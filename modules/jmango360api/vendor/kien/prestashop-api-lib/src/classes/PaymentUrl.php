<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JPaymentUrl
 */
class JPaymentUrl
{
    public $id;
    public $url;

    protected $webserviceParameters = array(
        'objectNodeName' => 'payment_url',
        'objectsNodeName' => 'payment_urls',
        'objectNodeNames' => 'payment_urls',
        'fields' => array(
            'url' => array('sqlId' => 'url')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

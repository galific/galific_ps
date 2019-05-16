<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JCookieKey
 */
class JCookieKey
{
    public $id;
    public $value;

    protected $webserviceParameters = array(
        'objectNodeName' => 'cookie_key',
        'objectsNodeName' => 'cookie_keys',
        'objectNodeNames' => 'cookie_keys',
        'fields' => array(
            'value' => array('sqlId' => 'value')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

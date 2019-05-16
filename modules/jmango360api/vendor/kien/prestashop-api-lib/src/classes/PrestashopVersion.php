<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JPrestashopVersion
 */
class JPrestashopVersion
{
    public $id;
    public $value;

    protected $webserviceParameters = array(
        'objectNodeName' => 'prestashop_version',
        'objectsNodeName' => 'prestashop_versions',
        'objectNodeNames' => 'prestashop_versions',
        'fields' => array(
            'value' => array('sqlId' => 'value')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

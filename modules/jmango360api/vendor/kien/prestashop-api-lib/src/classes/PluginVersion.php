<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JPluginVersion
 */
class JPluginVersion
{
    public $id;
    public $value;
    public $error;

    protected $webserviceParameters = array(
        'objectNodeName' => 'plugin_version',
        'objectsNodeName' => 'plugin_versions',
        'objectNodeNames' => 'plugin_versions',
        'fields' => array(
            'value' => array('sqlId' => 'value'),
            'error' => array('sqlId' => 'error')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

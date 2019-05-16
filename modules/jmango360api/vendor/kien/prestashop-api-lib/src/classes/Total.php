<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JTotal
 */
class JTotal
{
    public $id;
    public $type;
    public $label;
    public $value;
    public $amount;

    protected $webserviceParameters = array(
        'objectNodeName' => 'total',
        'objectsNodeName' => 'totals',
        'objectNodeNames' => 'totals',
        'fields' => array(
            'type' => array('sqlId' => 'type'),
            'label' => array('sqlId' => 'label'),
            'value' => array('sqlId' => 'value'),
            'amount' => array('sqlId' => 'amount')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

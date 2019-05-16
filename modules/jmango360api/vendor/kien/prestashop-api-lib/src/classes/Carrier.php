<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JCarrier
 */
class JCarrier
{
    public $id;
    public $id_address;
    public $name;
    public $logo;
    public $delay;
    public $price;
    public $price_with_tax;
    public $price_without_tax;
    public $selected;

    protected $webserviceParameters = array(
        'objectNodeName' => 'carrier',
        'objectsNodeName' => 'carriers',
        'objectNodeNames' => 'carriers',
        'fields' => array(
            'id' => array('sqlId' => 'id'),
            'id_address' => array('sqlId' => 'id_address'),
            'name' => array('sqlId' => 'name'),
            'delay' => array('sqlId' => 'delay'),
            'price' => array('sqlId' => 'price'),
            'price_with_tax' => array('sqlId' => 'price_with_tax'),
            'price_without_tax' => array('sqlId' => 'price_without_tax'),
            'logo' => array('sqlId' => 'logo'),
            'selected' => array('sqlId' => 'selected')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

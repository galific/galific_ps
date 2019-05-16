<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JPayment
 */
class JPayment
{
    public $id;
    public $title;
    public $logo;
    public $description;
    public $url;
    public $inputs;
    public $form;

    protected $webserviceParameters = array(
        'objectNodeName' => 'payment',
        'objectsNodeName' => 'payments',
        'objectNodeNames' => 'payments',
        'fields' => array(
            'id' => array('sqlId' => 'id'),
            'title' => array('sqlId' => 'title'),
            'description' => array('sqlId' => 'description'),
            'logo' => array('sqlId' => 'logo'),
            'url' => array('sqlId' => 'url'),
            'inputs' => array('sqlId' => 'inputs'),
            'form' => array('sqlId' => 'form')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

<?php
/**
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

/**
 * Class JUrlRewrite
 */
class JUrlRewrite
{
    public $id;
    public $id_shop;
    public $id_lang;
    public $title;
    public $description;
    public $url;
    public $url_rewrite;

    protected $webserviceParameters = array(
        'objectNodeName' => 'url',
        'objectsNodeName' => 'urls',
        'objectNodeNames' => 'urls',
        'fields' => array(
            'id' => array('sqlId' => 'id'),
            'id_shop' => array('sqlId' => 'id_shop'),
            'id_lang' => array('sqlId' => 'id_lang'),
            'title' => array('sqlId' => 'title'),
            'description' => array('sqlId' => 'description'),
            'url' => array('sqlId' => 'url'),
            'url_rewrite' => array('sqlId' => 'url_rewrite')
        )
    );

    public function getWebserviceParameters()
    {
        return $this->webserviceParameters;
    }
}

<?php

/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2016 knowband
 * @license   see file: LICENSE.txt
 */

class KbSellerShippingMethod extends ObjectModel
{
    public $id_shipping_method;
    public $method;
    public $active;
    public $date_add;
    public $date_upd;


    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'kb_mp_seller_shipping_method',
        'primary' => 'id_shipping_method',
        'fields' => array(
            'id_shipping_method' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'method' => array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'active' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate',
                'copy_post' => false)
        )
    );

    public function __construct($id = null)
    {
        parent::__construct($id);
    }
    
    public static function getAllShippingMethod()
    {
        return Db::getInstance()->executeS('SELECT * FROM '._DB_PREFIX_.'kb_mp_seller_shipping_method where active=1');
    }
}
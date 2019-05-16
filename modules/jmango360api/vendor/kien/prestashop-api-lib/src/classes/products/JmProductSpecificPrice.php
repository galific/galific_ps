<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmProductSpecificPrice
{
    const REDUCTION_TYPE_PERCENTAGE = 'percentage';
    const REDUCTION_TYPE_AMOUNT = 'amount';

    public $id_currency;
    public $id_product;
    public $id_product_attribute;
    public $price;
    public $from_quantity;
    public $reduction;
    public $reduction_tax_incl;
    public $reduction_type;
    public $from;
    public $to;
}

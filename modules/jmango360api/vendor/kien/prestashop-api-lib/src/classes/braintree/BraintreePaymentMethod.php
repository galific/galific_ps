<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class BraintreePaymentMethod extends PaymentModule
{
    public $name;
    public function __construct()
    {
        $this->name = 'Braintree';
    }
}
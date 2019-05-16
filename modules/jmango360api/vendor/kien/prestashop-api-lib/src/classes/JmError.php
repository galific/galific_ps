<?php
/**
 * Class JmError
 * @author Jmango
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

//namespace  _PS_MODULE_DIR_;

class JmError
{
    public function __construct($code, $message)
    {
        $this->code = $code;
        $this->message = $message;
    }

    public $code;
    public $message;
}

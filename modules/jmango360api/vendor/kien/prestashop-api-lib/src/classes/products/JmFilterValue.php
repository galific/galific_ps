<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmFilterValue
{
    public $name;
    public $nbr;
    public $link;

    /**
     * Convert fashion manufacturer  -> fashion_manufacturer
     * This is value of the $name but we need to replace space with underscore
     * in order to make it browser friendly
    */
    public $valueKey;
}

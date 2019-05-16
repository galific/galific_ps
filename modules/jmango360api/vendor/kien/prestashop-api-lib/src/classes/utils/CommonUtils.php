<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CommonUtils
{
    /**
     * Check version => 1.7
     */
    public static function isV17()
    {
        return version_compare(_PS_VERSION_, '1.7', '>=');
    }
}

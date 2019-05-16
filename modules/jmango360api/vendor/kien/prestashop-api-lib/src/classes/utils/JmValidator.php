<?php
/**
 * Class JmValidator
 * @author Jmango
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmValidator
{
    /**
     * http://php.net/manual/en/function.gettype.php
     */
    public static function isNullOrEmptyObject($object)
    {
        if (gettype($object) === 'array') {
            return self::isArrayEmpty($object);
        } elseif (gettype($object) === 'string') {
            return self::isNullOrEmptyString($object);
        }

        return false;
    }

    public static function isNullOrEmptyString($value)
    {
        return (!isset($value) || trim($value)==='');
    }

    public static function isArrayEmpty($arr)
    {
        if (is_array($arr)) {
            foreach ($arr as $value) {
                if (!empty($value)) {
                    return false;
                }
            }
        }
        return true;
    }

    /** Check valid birthdate format YYYY-MM-dd
     * @param $date
     * @return false|int
     */
    public static function isValidBirthdateFormat($date)
    {
        $rightFormat =  '/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/';

        return preg_match($rightFormat, $date);
    }
}

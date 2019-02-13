<?php
/**
 * 2007-2018 PrestaShop.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Class SearchEngineCore.
 */
class SearchEngineCore extends ObjectModel
{
    public $server;
    public $getvar;

    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'search_engine',
        'primary' => 'id_search_engine',
        'fields' => array(
            'server' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl', 'required' => true),
            'getvar' => array('type' => self::TYPE_STRING, 'validate' => 'isModuleName', 'required' => true),
        ),
    );

    /**
     * Get keywords.
     *
     * @param string $url
     *
     * @return bool|string
     */
    public static function getKeywords($url)
    {
        $parsedUrl = @parse_url($url);
        if (!isset($parsedUrl['host']) || !isset($parsedUrl['query'])) {
            return false;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('SELECT `server`, `getvar` FROM `' . _DB_PREFIX_ . 'search_engine`');
        foreach ($result as $row) {
            $host = &$row['server'];
            $varname = &$row['getvar'];
            if (strstr($parsedUrl['host'], $host)) {
                $array = array();
                preg_match('/[^a-z]' . $varname . '=.+\&/U', $parsedUrl['query'], $array);
                if (empty($array[0])) {
                    preg_match('/[^a-z]' . $varname . '=.+$/', $parsedUrl['query'], $array);
                }
                if (empty($array[0])) {
                    return false;
                }
                $str = urldecode(str_replace('+', ' ', ltrim(substr(rtrim($array[0], '&'), strlen($varname) + 1), '=')));
                if (!Validate::isMessage($str)) {
                    return false;
                }

                return $str;
            }
        }
    }
}

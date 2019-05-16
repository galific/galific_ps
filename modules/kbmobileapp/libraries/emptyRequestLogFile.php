<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 *
 * Description
 *
 * Updates quantity in the cart
 */
//echo dirname(__FILE__);die;
include(dirname(__FILE__) . '/../../../config/config.inc.php');
include(_PS_ROOT_DIR_ . '/init.php');

$log_file_path = _PS_MODULE_DIR_.'kbmobileapp/libraries/mobile_app_log.txt';

if (file_exists($log_file_path)) {
    echo unlink($log_file_path);
} else {
    echo '0';
}
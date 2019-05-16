<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * API to set fcm id with cart id
 */

require_once 'AppCore.php';

class AppFCMregister extends AppCore
{
    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $cart_id = Tools::getValue('cart_id', 0);
        $fcm_id = Tools::getValue('fcm_id', 0);
        if ($cart_id && $fcm_id) {
            $this->setFcmData($cart_id, $fcm_id);
        } else {
            $this->content['status'] = 'failure';
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /*
     * Function to set fcnm data
     * 
     * @param int $cart_id cart id
     * @param string $fcm_id device id
     */
    public function setFcmData($cart_id, $fcm_id)
    {
        $fcm_data = $this->isFcmAndCartExist($cart_id, $fcm_id);
        if (!$fcm_data) {
            $query = "INSERT INTO `"._DB_PREFIX_."kb_fcm_details` ("
                    . "`fcm_details_id`,"
                    . " `kb_cart_id`,"
                    . " `fcm_id`,"
                    . " `notification_sent_status`,"
                    . " `date_add`, `date_upd`) VALUES ("
                    . "NULL, '". (int)$cart_id."', '".pSQL($fcm_id)."', '0', now(), now())";

            Db::getInstance()->execute($query);
        }
        $this->content['status'] = 'success';
    }
    
    /*
     * Function to check is fcm id and cart id is already exist
     * 
     * @param int $cart_id cart id
     * @param string $fcm_id device id
     * @return bool
     */
    public function isFcmAndCartExist($cart_id, $fcm_id)
    {
        $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_fcm_details where kb_cart_id = '. (int)$cart_id.' AND fcm_id ="'.pSQL($fcm_id).'"';
        $data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
        if ($data) {
            return $data;
        } else {
            return false;
        }
    }
}

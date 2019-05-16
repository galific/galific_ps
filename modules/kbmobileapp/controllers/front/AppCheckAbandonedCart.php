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
 */

include_once _PS_MODULE_DIR_ . 'kbmobileapp/libraries/firebase.php';


class KbMobileAppAppCheckAbandonedCartModuleFrontController extends ModuleFrontController
{
    public $controller_name = 'AppPayment';
    public $module_name = 'kbmobileapp';
    public $error = array();

    /*
     * Build an front controller
     */
    public function __construct()
    {
        parent::__construct();
    }

    /*
     * Default front controller initialize function
     */
    public function initContent()
    {
        parent::initContent();
        $this->context = Context::getContext();
        $settings = Tools::unSerialize(Configuration::get('KB_MOBILEAPP_NOTIFICATION_DATA'));
        
        if (Tools::getValue('secure_key')) {
            $secure_key = Configuration::get('KB_MOBILE_APP_SECURE_KEY');
            if ($secure_key == Tools::getValue('secure_key')) {
                if (isset($settings['abandoned_cart']['status']) && $settings['abandoned_cart']['status'] == 1) {
                    $interval = (int)$settings['abandoned_cart']['interval'];
                    $title = $settings['abandoned_cart']['title'];
                    $message = $settings['abandoned_cart']['message'];
                    $interval_in_sec = $interval * 60 * 60;
                    $checkdate = date("Y-m-d H:i:s", time() - $interval_in_sec);
                    $query = 'SELECT * FROM ' . _DB_PREFIX_ . 'kb_fcm_details where'
                            . ' date_add < "'.pSQL($checkdate).'" AND notification_sent_status = 0';
                    $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
                    if ($result) {
                        foreach ($result as $data) {
                            $order_id = Order::getOrderByCartId((int)$data['kb_cart_id']);
                            if (!$order_id) {
                                $this->sendAbandonedCartNotification($data['kb_cart_id'], $data['fcm_id'], $title, $message, $data['device_type']);
                            }
                        }
                    }
                    echo $this->module->l('Notification Sent successfully');
                    die;
                } else {
                    echo $this->module->l('Abandoned cart setting is disabled');
                    die;
                }
            } else {
                echo $this->module->l('You are not authorized to access this page');
                die;
            }
        } else {
            echo $this->module->l('You are not authorized to access this page');
            die;
        }
    }

    /*
     * Function to send abandoned cartr pust notification
     * 
     * @param int $cart_id id of cart
     * @param string $fcm_id unique device id
     * @param string $title title of push notification
     * @param string $message message of push notification
     */
    public function sendAbandonedCartNotification($cart_id, $fcm_id, $title, $message, $deviceType)
    {
        $firebase_server_key = '';
        if (Configuration::get('KB_MOBILEAPP_FIREBASE_KEY')) {
            $firebase_server_key = Configuration::get('KB_MOBILEAPP_FIREBASE_KEY');
        }
        
         $firebase = new Firebase();
         
        $email = $this->getCustomerEmailByCartId($cart_id);

        $user_id = ""; // user_id

        $push_type = "kb_abandoned_cart";
        $firebase_data = array();
        $firebase_data['data']['title'] = $title;
        $firebase_data['data']['is_background'] = false;
        $firebase_data['data']['message'] = $message;
        $firebase_data['data']['image'] = '';
        $firebase_data['data']['payload'] = '';
        $firebase_data['data']['user_id'] = $user_id;
        $firebase_data['data']['push_type'] = $push_type;
        $firebase_data['data']['cart_id'] = $cart_id;
        $firebase_data['data']['email_id'] = $email;
        
        $firebase->sendMultiple($fcm_id, $firebase_data, $firebase_server_key, $deviceType);
        $this->updateDatabase($cart_id);
    }
   
    /*
     * Function to get customer email addres by cart id
     * 
     * @param int $cart_id id of cart
     * @return string customer email address
     */
    public function getCustomerEmailByCartId($cart_id)
    {
        
        $cart_obj = new Cart(
            $cart_id,
            false,
            null,
            null,
            $this->context
        );
        $customer_email = '';
        $customer_id = $cart_obj->id_customer;
        if ($customer_id) {
            $customer = new Customer((int) $customer_id);
            if ($customer) {
                $customer_email = $customer->email;
            }
        }
        
        return $customer_email;
    }
    
    /*
     * Update the notification sent staus in our fcm details table so that we can not
     * trigger notification again
     * 
     * @param int $cart_id id of cart
     */
    public function updateDatabase($cart_id)
    {
        $update_query = "UPDATE `"._DB_PREFIX_."kb_fcm_details` ";
        $update_query .= "SET `notification_sent_status` = 1, `date_upd` = now()";
        $update_query .= " WHERE `kb_cart_id` = ".(int)$cart_id;
        Db::getInstance()->execute($update_query);
    }
}

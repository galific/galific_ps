<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.7.0
 */
class CitrusNotifyModuleFrontController extends ModuleFrontController
{
	
	public function postProcess()
    {
		global $smarty, $cart, $cookie;
	
		$cart_id = $_REQUEST['TxId'];
		//$cart_id = str_replace("PORD-","", $cart_id);
		$tmpid = explode('-',$cart_id);
		$cart_id=$tmpid[1];
		$response = $_REQUEST['TxStatus'];
		
		PrestaShopLogger::addLog("Citrus: Processing Notify for CartID-".$cart_id,1, null, 'Citrus', (int)$cart_id, true);

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'citrus') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
			die();
        }

		//verify sig
		$citrus_api_key= Configuration::get('CITRUS_API_KEY');
		$citrus_mode= Configuration::get('CITRUS_MODE');
		
		$data = $_REQUEST['TxId'].$_REQUEST['TxStatus'].$_REQUEST['amount'].$_REQUEST['pgTxnNo'].$_REQUEST['issuerRefNo'].$_REQUEST['authIdCode'].$_REQUEST['firstName'].$_REQUEST['lastName'].$_REQUEST['pgRespCode'].$_REQUEST['addressZip'];
		$respSig = $_REQUEST['signature'];
		
		$secSignature = hash_hmac('sha1',$data, $citrus_api_key);
		
		if ($respSig != $secSignature)
			$response="FORGED";
				
		if ($response == 'SUCCESS')
		{
			$responseMsg="Your Order has Been Processed";
			$status = Configuration::get('CITRUS_ID_ORDER_SUCCESS');
			
			$amount = $_REQUEST['amount'];
			$message = $_REQUEST['TxMsg'];
			
			$order_id = intval(Order::getOrderByCartId((int)($cart_id)));
			if($order_id > 0)
			{
				$order = new Order($order_id);	
				if($order->current_state != $status)
				{
					$history = new OrderHistory();
					$history->id_order = (int)$order->id;
					$history->changeIdOrderState($status, (int)($order->id)); 
					PrestaShopLogger::addLog("Citrus: Updated status for order-".$order_id,1, null, 'Citrus', (int)$cart_id, true);
				}
			}
			else {
				$cart = new Cart($cart_id);				
				
				if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0) {
					retun;
				}

				$this->validateOrder($cart_id, $status, $amount, $this->displayName,NULL,NULL,NULL,false,$cart->secure_key);
				PrestaShopLogger::addLog("Citrus: Created new order for CartId-".$cart_id,1, null, 'Citrus', (int)$cart_id, true);
			}			
		}
		
		die("Process completed");
		
    }
}

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
class CitruspayuValidationModuleFrontController extends ModuleFrontController
{
	public $warning = '';
	public $message = '';
	public function initContent()
  	{  
		parent::initContent();
	
		$this->context->smarty->assign(array(
		  	'warning' => $this->warning,
			'message' => $this->message
        	));        	
	    
		$this->setTemplate('module:citruspayu/views/templates/front/validation.tpl');  
    	
    	
  	}
  
    public function postProcess()
    {
        $cart = $this->context->cart;

        if ($cart->id_customer == 0 || $cart->id_address_delivery == 0 || $cart->id_address_invoice == 0 || !$this->module->active) {
            Tools::redirect('index.php?controller=order&step=1');
        }

        // Check that this payment option is still available in case the customer changed his address just before the end of the checkout process
        $authorized = false;
        foreach (Module::getPaymentModules() as $module) {
            if ($module['name'] == 'citruspayu') {
                $authorized = true;
                break;
            }
        }

        if (!$authorized) {
           $this->warning='This payment method is not available.';
		   $this->message='Contact Administrator for available payment methods.';
		   return;
        }

        $customer = new Customer($cart->id_customer);

        if (!Validate::isLoadedObject($customer)) {
            Tools::redirect('index.php?controller=order&step=1');
        }

		$currency = $this->context->currency;
		
		//check if we have citrus or payu
		$payemntType  = 0;
		if (isset($_REQUEST['TxMsg']))
		{
			$payemntType = 1;
		}
		else if (isset($_REQUEST['key']))
		{
			$payemntType = 2;
		}
		else 
		{
			$this->warning="Payment error - Invalid response received from payment gateway";
			$this->message="Payment gateway did not respond is proper format.";
			return;
		}
		
		if ($payemntType == 1)
		{
		
			//process citrus response			
			$amount = $_REQUEST['amount'];
			$this->message = $_REQUEST['TxMsg'];
			$response = $_REQUEST['TxStatus'];
			$cart_id = $cart->id;
			
			//verify sig			
			$citrus_api_key= Configuration::get('CITRUSPAYU_API_KEY');
			$citrus_mode= Configuration::get('CITRUSPAYU_MODE');
			
			$data=$_REQUEST['TxId'].$_REQUEST['TxStatus'].$_REQUEST['amount'].$_REQUEST['pgTxnNo'].$_REQUEST['issuerRefNo'].$_REQUEST['authIdCode'].$_REQUEST['firstName'].$_REQUEST['lastName'].$_REQUEST['pgRespCode'].$_REQUEST['addressZip'];
			$respSig=$_REQUEST['signature'];
			
			$secSignature = hash_hmac('sha1',$data, $citrus_api_key);
			
			if ($respSig != $secSignature)
				$response="FORGED";
					
			if ($response == 'SUCCESS')
			{
				$responseMsg="Your Order has Been Processed";
				$status = Configuration::get('CITRUSPAYU_ID_ORDER_SUCCESS');
			}
			else 
			{
				$responseMsg="Transaction Failed, Retry!!";
				$status = Configuration::get('CITRUSPAYU_ID_ORDER_FAILED');
										
			}			
		}
		else if ($payemntType == 2) 
		{
			//process payu money response
			$postdata = $_REQUEST;			
			$pum_key= Configuration::get('CITRUSPAYU_PAYUKEY');
			$pum_salt= Configuration::get('CITRUSPAYU_PAYUSALT');
				
			if (isset($postdata['key']) && ($postdata['key'] == $pum_key)) {
			
				$cart_id = $cart->id;
				$txnid = $postdata['txnid'];
			
				$amount      		= 	$postdata['amount'];
				$productInfo  		= 	$postdata['productinfo'];
				$firstname    		= 	$postdata['firstname'];
				$email        		=	$postdata['email'];
				$udf5 				= 	$postdata['udf5'];
				$keyString 	  		=  	$pum_key.'|'.$txnid.'|'.$amount.'|'.$productInfo.'|'.$firstname.'|'.$email.'|||||'.$udf5.'|||||';
				$keyArray 	  		= 	explode("|",$keyString);
				$reverseKeyArray 	= 	array_reverse($keyArray);
				$reverseKeyString	=	implode("|",$reverseKeyArray);
			
				if (isset($postdata['status']) && $postdata['status'] == 'success') {
					$saltString     = $pum_salt.'|'.$postdata['status'].'|'.$reverseKeyString;
					$sentHashString = strtolower(hash('sha512', $saltString));
					$responseHashString=$postdata['hash'];
			
					$status = Configuration::get('CITRUSPAYU_ID_ORDER_FAILED');
					$responseMsg = "Thank you for shopping with us. However, the transaction has been declined.";
					
					if($sentHashString==$responseHashString){
						$status = Configuration::get('CITRUSPAYU_ID_ORDER_SUCCESS');
						$responseMsg = "Thank you for shopping with us. Your account has been charged and your transaction is successful. We will be shipping your order to you soon.";
					} else {
						//tampered
						$status = Configuration::get('CITRUSPAYU_ID_ORDER_FAILED');
						$responseMsg = "Thank you for shopping with us. However, the payment failed";
						$this->message = "Payment gateway response is not received in proper format. Could be an attempt to tamper payment.";
					}
				}
				else {
					$status = Configuration::get('CITRUSPAYU_ID_ORDER_FAILED');
					$responseMsg = "Thank you for shopping with us. However, the payment has been cancelled or declined.";
					$this->message = "Payment gateway responded - ". $postdata['field9'];
				}
			}
		
		}
				
		
		if ($payemntType == 1)
		{
			PrestaShopLogger::addLog("Citrus - PayuMoney: Created Order for Cartid-".$cart_id,1, null, 'Citrus', (int)$cart_id, true);
			$this->module->validateOrder((int)$cart_id,  $status, (float)$amount, "Citrus", null, null, null, false, $customer->secure_key);
		}
		else
		{
			PrestaShopLogger::addLog("Citrus - PayUmoney: Created Order for Cartid-".$cart_id,1, null, 'PayUmoney', (int)$cart_id, true);
			$this->module->validateOrder((int)$cart_id,  $status, (float)$amount, "PayUmoney", null, null, null, false, $customer->secure_key);
		}
			
		if ($status == Configuration::get('CITRUSPAYU_ID_ORDER_SUCCESS'))
		{			
			Tools::redirect('index.php?controller=order-confirmation&id_cart='.(int)$cart->id.'&id_module='.(int)$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
		}
		else
		{
			$this->warning= $responseMsg;
			
			//Tools::redirect('index.php');
						
		}
    }
}

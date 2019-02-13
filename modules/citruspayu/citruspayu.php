<?php /* Prestashop payment module for Citrus Payment Gateway */ ?>
<?php 	
error_reporting(E_ALL);	

use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
	exit;
}

class citruspayu extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	private $_title;
	
	function __construct()
	{		
		$this->name = 'citruspayu';		
		$this->tab = 'payments_gateways';		
		$this->version = 1.7;
		$this->author = 'Payumoney.com';
				
		$this->bootstrap = true;			
		parent::__construct();		
			
		$this->displayName = $this->trans('Citrus and PayUmoney', array(), 'Modules.Citruspayu.Admin');
		$this->description = $this->trans('Accept payments by Citrus and PayUmoney', array(), 'Modules.Citruspayu.Admin');
		$this->confirmUninstall = $this->trans('Are you sure you want to delete these details?', array(), 'Modules.Citruspayu.Admin');
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		
		
		//Decide on Citrus or PayUM
		$citrus_vanityurl= Configuration::get('CITRUSPAYU_VANITY_URL');
		$citrus_access_key= Configuration::get('CITRUSPAYU_ACCESS_KEY');
		$citrus_api_key= Configuration::get('CITRUSPAYU_API_KEY');
		$citrus_mode= Configuration::get('CITRUSPAYU_MODE');
		$payu_key= Configuration::get('CITRUSPAYU_PAYUKEU');
		$payu_salt= Configuration::get('CITRUSPAYU_PAYUSALT');

		$cper = Configuration::get('CITRUSPAYU_CITRUSPERCENTAGE');
		if ($cper == null || $cper == '')
		{
			$cper = 0;
		}
		
		$pper = Configuration::get('CITRUSPAYU_PAYUPERCENTAGE');
		if ($pper == null || $pper == 'ICP')
		{
			$pper = 0;
		}
		
		$title = "";
		if(!$payu_key && !$payu_salt) {
			$title = 'Citrus ICP';
		}
		elseif(!$citrus_vanityurl  && !$citrus_access_key && !$citrus_api_key) {
			$title = 'PayUmoney';
		}
		else {
			if($cper == 0)
			{
				$title = 'PayUmoney';
			}
			elseif($pper == 0)
			{
				$title = 'Citrus ICP';
			}
			else {
		
				$sql = 'select distinct count(*) as totcount from '. _DB_PREFIX_.'order_payment where payment_method = "Citrus"';
				$results = Db::getInstance()->getRow($sql);
				$ccount = $results['totcount'];
		
				$sql = 'select distinct count(*) as totcount from '. _DB_PREFIX_.'order_payment where payment_method = "PayUmoney"';
				$results = Db::getInstance()->getRow($sql);
				$pcount = $results['totcount'];
				
				if ($ccount > 0 || $pcount > 0)
				{
					$total = $ccount + $pcount;
					$ccount = ($ccount * 100)/$total;
					$pcount = ($pcount * 100)/$total;
				}
		
				if($ccount > $cper && $pcount <= $pper) {
					$title = 'PayUmoney';
				}
				elseif ($ccount <= $cper && $pcount > $pper) {
					$title = 'Citrus ICP';
				}
				else {
					if($pcount >= $ccount)
						$title = 'Citrus ICP';
					else
						$title = 'PayUmoney';
				}
			}
		}

						
		$this->_title = $title;
		
		$this->page = basename(__FILE__, '.php');		
					
	}	
	
	
	public function install()
	{
		Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'order_state` ( `invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`, `module_name`)	VALUES	(0, 0, \'#33FF99\', 0, 1, 0, \'citrus\');');
		$id_order_state = (int) Db::getInstance()->Insert_ID();
		Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) VALUES ('.$id_order_state.', 1, \'Payment accepted\', \'payment\')');
		Configuration::updateValue('CITRUSPAYU_ID_ORDER_SUCCESS', $id_order_state);			
		unset($id_order_state);
				
		Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'order_state`( `invoice`, `send_email`, `color`, `unremovable`, `logable`, `delivery`, `module_name`) VALUES (0, 0, \'#33FF99\', 0, 1, 0, \'citrus\');');
		$id_order_state = (int) Db::getInstance()->Insert_ID();
		Db::getInstance()->Execute('INSERT INTO `' . _DB_PREFIX_ . 'order_state_lang` (`id_order_state`, `id_lang`, `name`, `template`) VALUES ('.$id_order_state.', 1, \'Payment Failed\', \'payment\')');
		Configuration::updateValue('CITRUSPAYU_ID_ORDER_FAILED', $id_order_state);		
		unset($id_order_state);
		
		return parent::install()
			&& $this->registerHook('paymentOptions')
			&& $this->registerHook('displayPaymentByBinaries');
	
	}

	public function uninstall()
	{
		
		Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'order_state_lang` WHERE id_order_state = '.Configuration::get('CITRUSPAYU_ID_ORDER_SUCCESS').' and id_lang = 1' );
		Db::getInstance()->Execute('DELETE FROM `' . _DB_PREFIX_ . 'order_state_lang`  WHERE id_order_state = '.Configuration::get('CITRUSPAYU_ID_ORDER_FAILED').' and id_lang = 1');

		return Configuration::deleteByName('CITRUSPAYU_VANITY_URL')
			&& Configuration::deleteByName('CITRUSPAYU_ACCESS_KEY')
			&& Configuration::deleteByName('CITRUSPAYU_API_KEY')
			&& Configuration::deleteByName('CITRUSPAYU_MODE')
			&& Configuration::deleteByName('CITRUSPAYU_PAYUKEY')
			&& Configuration::deleteByName('CITRUSPAYU_PAYUSALT')
			&& Configuration::deleteByName('CITRUSPAYU_CITRUSPERCENTAGE')
			&& Configuration::deleteByName('CITRUSPAYU_PAYUPERCENTAGE')
			&& parent::uninstall();		
	}


	public function hookdisplayPaymentByBinaries($params)
	{
		if (!$this->active) {
            return;
        }

		$btn = '<section class="js-payment-binary js-payment-citruspayu disabled">';
		
		$btn = $btn.'<button type="button" onclick="launchICP(); return false;" class="btn btn-primary center-block">';
        $btn = $btn.'Make Payment with Citrus';
		$btn = $btn.'</button>';		
		//$btn = $btn.'<a href="#" style="color:black;" id="btnStartCitrus" onclick="launchICP(); return false;" title="Make Payment with Citrus">Make Payment with Citrus</a>';
		$btn = $btn.'</section>';
		
		return $btn;
	}

	public function hookPaymentOptions($params)
	{		
		if (!$this->active) {
			return;
		}
	
		$newOption = new PaymentOption();
		
		if ($this->_title == "Citrus ICP")
		{
			$newOption->setCallToActionText($this->l('Pay by Citrus'))
			->setForm($this->generateForm());
			//->setAction($this->context->link->getModuleLink($this->name, 'validation', array(), true));
			
			$newOption->setModuleName('citruspayu');
			$newOption->setBinary(true);
								
		}
		else 
		{
			$citrus_mode= Configuration::get('CITRUSPAYU_MODE');
			$action = 'https://secure.payu.in/_payment.php';
			if($citrus_mode == 'sandbox')
			{
				$action = 'https://test.payu.in/_payment.php';
			}
			
			$inputs = $this->payuInput();
			
			$newOption->setCallToActionText($this->l($this->_title))			
				->setAction($action)
				->setInputs($inputs)
				->setAdditionalInformation($this->context->smarty->fetch('module:citruspayu/payu.tpl'));
				
				//->setLogo(Media::getMediaPath(_PS_MODULE_DIR_ . $this->name . '/logo.jpg'));				
			
			$newOption->setModuleName('citruspayu');
		}
		
		return [$newOption];
	}
	
	private function _postValidation()
	{
		if (Tools::isSubmit('btnSubmit')) {
			if (!Tools::getValue('CITRUSPAYU_VANITY_URL') && (Tools::getValue('CITRUSPAYU_ACCESS_KEY') || Tools::getValue('CITRUSPAYU_API_KEY')) ) {
				$this->_postErrors[] = $this->trans('Payment / Vanity URL is required.', array(),'Modules.Citruspayu.Admin');
			} elseif (!Tools::getValue('CITRUSPAYU_ACCESS_KEY') && (Tools::getValue('CITRUSPAYU_VANITY_URL') || Tools::getValue('CITRUSPAYU_API_KEY'))) {
				$this->_postErrors[] = $this->trans('Access Key is required.', array(), 'Modules.Citruspayu.Admin');		
			} elseif (!Tools::getValue('CITRUSPAYU_API_KEY') && (Tools::getValue('CITRUSPAYU_ACCESS_KEY') || Tools::getValue('CITRUSPAYU_VANITY_URL'))) {
				$this->_postErrors[] = $this->trans('Secret Key is required.', array(), 'Modules.Citruspayu.Admin');
			} elseif (!Tools::getValue('CITRUSPAYU_MODE')) {
				$this->_postErrors[] = $this->trans('Gateway mode is required.', array(), 'Modules.Citruspayu.Admin');
			} elseif (!Tools::getValue('CITRUSPAYU_PAYUKEY') && Tools::getValue('CITRUSPAYU_PAYUSALT')) {
				$this->_postErrors[] = $this->trans('PayUmoney Key is required.', array(), 'Modules.Citruspayu.Admin');
			} elseif (!Tools::getValue('CITRUSPAYU_PAYUSALT') && Tools::getValue('CITRUSPAYU_PAYUKEY')) {
				$this->_postErrors[] = $this->trans('PayUmoney Salt is required.', array(), 'Modules.Citruspayu.Admin');
			}				
		}
	}

	private function _postProcess()
	{
		if (Tools::isSubmit('btnSubmit')) {
			Configuration::updateValue('CITRUSPAYU_VANITY_URL', Tools::getValue('CITRUSPAYU_VANITY_URL'));
			Configuration::updateValue('CITRUSPAYU_ACCESS_KEY', Tools::getValue('CITRUSPAYU_ACCESS_KEY'));
			Configuration::updateValue('CITRUSPAYU_API_KEY', Tools::getValue('CITRUSPAYU_API_KEY'));
			Configuration::updateValue('CITRUSPAYU_MODE', Tools::getValue('CITRUSPAYU_MODE'));
			Configuration::updateValue('CITRUSPAYU_PAYUKEY', Tools::getValue('CITRUSPAYU_PAYUKEY'));
			Configuration::updateValue('CITRUSPAYU_PAYUSALT', Tools::getValue('CITRUSPAYU_PAYUSALT'));
			Configuration::updateValue('CITRUSPAYU_CITRUSPERCENTAGE', Tools::getValue('CITRUSPAYU_CITRUSPERCENTAGE'));
			Configuration::updateValue('CITRUSPAYU_PAYUPERCENTAGE', Tools::getValue('CITRUSPAYU_PAYUPERCENTAGE'));
		}
		$this->_html .= $this->displayConfirmation($this->trans('Settings updated', array(), 'Admin.Notifications.Success'));
	}
	
	public function getContent()
	{
		 $this->_html = '';

        if (Tools::isSubmit('btnSubmit')) {
            $this->_postValidation();
            if (!count($this->_postErrors)) {
                $this->_postProcess();
            } else {
                foreach ($this->_postErrors as $err) {
                    $this->_html .= $this->displayError($err);
                }
            }
        }

        $this->_html .= $this->_displayCheck();
        $this->_html .= $this->renderForm();

		return $this->_html;
	}
	
	public function renderForm()
	{
		
		$options = array(
			array(
					'id_option' => 'production', 
					'name' => 'production' 
					),
				array(
					'id_option' => 'sandbox',
					'name' => 'sandbox'
					),
				);
			
		$fields_form = array(
			'form' => array(
					'legend' => array(
						'title' => $this->trans('Citrus Gateway details', array(), 'Modules.Citruspayu.Admin'),
						'icon' => 'icon-envelope'
						),
					'input' => array(
						array(
							'type' => 'select',
							'label' => $this->trans('Gateway Mode', array(), 'Modules.Citruspayu.Admin'),
							'name' => 'CITRUSPAYU_MODE',
							'required' => true,
							'options' => array(
								'query' => $options,
								'id' => 'id_option', 
								'name' => 'name'
								)
							),
						array(
							'type' => 'text',
							'label' => $this->trans('Payment / Vanity URL', array(), 'Modules.Citruspayu.Admin'),
							'name' => 'CITRUSPAYU_VANITY_URL',
							'required' => true
							),
						array(
							'type' => 'text',
							'label' => $this->trans('Access Key', array(), 'Modules.Citruspayu.Admin'),
							'name' => 'CITRUSPAYU_ACCESS_KEY',
							'required' => true
							),
						array(
							'type' => 'text',
							'label' => $this->trans('Secret Key', array(), 'Modules.CheckPayment.Admin'),
							'name' => 'CITRUSPAYU_API_KEY',
							'required' => true
							),
						array(
								'type' => 'text',
								'label' => $this->trans('PayUmoney Key', array(), 'Modules.Citruspayu.Admin'),
								'name' => 'CITRUSPAYU_PAYUKEY',
								'required' => true
						),
						array(
								'type' => 'text',
								'label' => $this->trans('PayUmoney Salt', array(), 'Modules.Citruspayu.Admin'),
								'name' => 'CITRUSPAYU_PAYUSALT',
								'required' => true
						),
						array(
								'type' => 'text',
								'label' => $this->trans('Payment to Route to Citrus (%)', array(), 'Modules.Citruspayu.Admin'),
								'name' => 'CITRUSPAYU_CITRUSPERCENTAGE',
								'required' => true
						),
						array(
								'type' => 'text',
								'label' => $this->trans('Payment to Route to PayUmoney (%)', array(), 'Modules.Citruspayu.Admin'),
								'name' => 'CITRUSPAYU_PAYUPERCENTAGE',
								'required' => true
							),
						),
					'submit' => array(
						'title' => $this->trans('Save', array(), 'Admin.Actions'),
						)
					),
				);

		$helper = new HelperForm();
		$helper->show_toolbar = false;
		$helper->id = (int)Tools::getValue('id_carrier');
		$helper->identifier = $this->identifier;
		$helper->submit_action = 'btnSubmit';
		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
		$helper->token = Tools::getAdminTokenLite('AdminModules');
		$helper->tpl_vars = array(
			'fields_value' => $this->getConfigFieldsValues(),
			);

		$this->fields_form = array();

		return $helper->generateForm(array($fields_form));
	}

	public function getConfigFieldsValues()
	{
		return array(
			'CITRUSPAYU_VANITY_URL' => Tools::getValue('CITRUSPAYU_VANITY_URL', Configuration::get('CITRUSPAYU_VANITY_URL')),
			'CITRUSPAYU_ACCESS_KEY' => Tools::getValue('CITRUSPAYU_ACCESS_KEY', Configuration::get('CITRUSPAYU_ACCESS_KEY')),
			'CITRUSPAYU_API_KEY' => Tools::getValue('CITRUSPAYU_API_KEY', Configuration::get('CITRUSPAYU_API_KEY')),
			'CITRUSPAYU_MODE' => Tools::getValue('CITRUSPAYU_MODE', Configuration::get('CITRUSPAYU_MODE')),
			'CITRUSPAYU_PAYUKEY' => Tools::getValue('CITRUSPAYU_PAYUKEY', Configuration::get('CITRUSPAYU_PAYUKEY')),
			'CITRUSPAYU_PAYUSALT' => Tools::getValue('CITRUSPAYU_PAYUSALT', Configuration::get('CITRUSPAYU_PAYUSALT')),
			'CITRUSPAYU_CITRUSPERCENTAGE' => Tools::getValue('CITRUSPAYU_CITRUSPERCENTAGE', Configuration::get('CITRUSPAYU_CITRUSPERCENTAGE')),
			'CITRUSPAYU_PAYUPERCENTAGE' => Tools::getValue('CITRUSPAYU_PAYUPERCENTAGE', Configuration::get('CITRUSPAYU_PAYUPERCENTAGE')),
			);
	}

	public function hookDisplayBackOfficeHeader()
	{
		$this->context->controller->addJquery();
		$this->context->controller->addJS(($this->_path) . 'views/js/admin.js');	
	}
	
	private function _displayCheck()
	{
		return $this->display(__FILE__, './views/templates/hook/infos.tpl');
	}

	protected function generateForm()
	{
		global $smarty, $cart;
		
		$citrus_vanityurl= Configuration::get('CITRUSPAYU_VANITY_URL');
		$citrus_access_key= Configuration::get('CITRUSPAYU_ACCESS_KEY');
		$citrus_api_key= Configuration::get('CITRUSPAYU_API_KEY');
		$citrus_mode= Configuration::get('CITRUSPAYU_MODE');
			
		$merchantTxnId = rand(200,3000).'-'.$cart->id;
		$customer = new Customer($cart->id_customer);
		$address = new Address($cart->id_address_invoice);
		$state=new State($address->id_state);
		$country=new Country($address->id_country);
		$firstName = $address->firstname;
		$lastName = $address->lastname;		
		$zipcode = $address->postcode;
		$email = $customer->email;
		$phone = $address->phone;
		$city = $address->city;;
			
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));
		$currency_code =$currency->iso_code;
		$orderAmount =number_format(Tools::convertPrice($cart->getOrderTotal(),$currency), 2, '.', '');
			
		$return_url = $this->context->link->getModuleLink($this->name, 'validation', array(), true);
		$notify_url = $this->context->link->getModuleLink($this->name, 'notify', array(), true);
		
		$issuerCode = ''; 
		$customer = new Customer($cart->id_customer);
		$address = new Address($cart->id_address_invoice);
		
		$data = $citrus_vanityurl.$orderAmount.$merchantTxnId.$currency_code;
		$secSignature = $this->generateHmacKey($data, $citrus_api_key);
					
		$action = "";
		
		$smarty->assign(array(
			'vanityurl' => $citrus_vanityurl,
			'gateway' => $citrus_mode,
			'action' => $action,
			'merchantAccessKey' => $citrus_access_key,
			'merchantTxnId' => $merchantTxnId,
			'orderAmount' => $orderAmount,
			'secSignature' => $secSignature,
			'currency' => $currency_code,
			'customer' => $customer,
			'address' => $address,
			'country' => $country->iso_code,
			'state' => $state->name,
			'return_url' => $return_url,
			'notify_url' => $notify_url,
			'reqtime' => time() . '000',        ));        
		
		return $this->display(__FILE__, 'citrus.tpl');    
		
	}

	protected function payuInput()
	{
		global $smarty, $cart;
	
		$udf5 = "Prestashop_v_1.7";
		$citrus_mode= Configuration::get('CITRUSPAYU_MODE');
		$payu_key= Configuration::get('CITRUSPAYU_PAYUKEY');
		$payu_salt= Configuration::get('CITRUSPAYU_PAYUSALT');
	
		$merchantTxnId = rand(200,3000).'-'.$cart->id;
		$customer = new Customer($cart->id_customer);
		$address = new Address($cart->id_address_invoice);
		$state=new State($address->id_state);
		$country=new Country($address->id_country);
		$firstName = $address->firstname;
		$lastName = $address->lastname;
		$zipcode = $address->postcode;
		$email = $customer->email;
		$phone = $address->phone;
		$city = $address->city;;
			
		$id_currency = intval(Configuration::get('PS_CURRENCY_DEFAULT'));
		$currency = new Currency(intval($id_currency));
		$currency_code =$currency->iso_code;
		$orderAmount =number_format(Tools::convertPrice($cart->getOrderTotal(),$currency), 2, '.', '');
			
		$return_url = $this->context->link->getModuleLink($this->name, 'validation', array(), true);

		$action = 'https://secure.payu.in/_payment.php';
		if($citrus_mode == 'sandbox')
		{
			$action = 'https://sandboxsecure.payu.in/_payment.php';
		}
			
		$orderId = $cart->id;
		$productInfo = "Product Information";
		$Pg = 'CC';
		$surl = $return_url;
		$furl = $return_url;
		$curl = $return_url;

		$hash=hash('sha512', $payu_key.'|'.$orderId.'|'.$orderAmount.'|'.$productInfo.'|'.$firstName.'|'.$email.'|||||'.$udf5.'||||||'.$payu_salt);
		$user_credentials = $payu_key.':'.$email;
		$service_provider = 'payu_paisa';

		$values  = array(
			'key' => $payu_key,
			'txnid' => $orderId,
			'amount' => $orderAmount,
			'productinfo' => $productInfo,
			'firstname' => $firstName,
			'Lastname' => $lastName,
			'Zipcode' => $zipcode,
			'email' => $email,
			'phone' => $phone,
			'surl' => $surl,
			'furl' => $furl,
			'curl' => $curl,
			'Hash' => $hash,
			'Pg' => $Pg,
			'service_provider' => $service_provider,
			'address1' => $address->address1,
			'address2' => "",
			'city' => $address->city,
			'country' => $country->iso_code,
			'udf5' => $udf5,
			'state' => '',
		);
				
		$inputs = array();
		foreach ($values as $k => $v)
		{
			$inputs[$k] = array(
				'name' => $k,
				'type' => 'hidden',
				'value' => $v,						
			);	
		}	
		
		return $inputs;
	}
	
	public function generateHmacKey($data, $apiKey){
		$hmac=hash_hmac('sha1',$data, $apiKey);
		return $hmac;
	}
	
	// ==========================================================================================
	
/*
	
	//Only for notify url
	public function finalizeOrderNotify($response,$cart_id,$amount)    
	{    
	
		global $smarty, $cart, $cookie;
		//verify sig
		$citrus_api_key= Configuration::get('citrus_api_key');
		$citrus_mode= Configuration::get('citrus_mode');
		$data=$_REQUEST['TxId'].$_REQUEST['TxStatus'].$_REQUEST['amount'].$_REQUEST['pgTxnNo'].$_REQUEST['issuerRefNo'].$_REQUEST['authIdCode'].$_REQUEST['firstName'].$_REQUEST['lastName'].$_REQUEST['pgRespCode'].$_REQUEST['addressZip'];
		$respSig=$_REQUEST['signature'];
		CitrusPay::setApiKey($citrus_api_key,$citrus_mode);
		
		PrestaShopLogger::addLog("Citrus: Processing Notify for CartID-".$cart_id,1, null, 'Citrus', (int)$cart_id, true);
		
		if($respSig != $this->generateHmacKey($data,CitrusPay::getApiKey()))
			$response="FORGED";
		
		
		if($response == 'SUCCESS')
		{
			$status=Configuration::get('CITRUS_ID_ORDER_SUCCESS');
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
				$this->validateOrder($cart_id, $status, $amount, $this->displayName,NULL,NULL,NULL,false,$cart->secure_key);
				PrestaShopLogger::addLog("Citrus: Created new order for CartId-".$cart_id,1, null, 'Citrus', (int)$cart_id, true);
			}			
		}
		
		
		PrestaShopLogger::addLog("Citrus: Exiting Notify",1, null, 'Citrus', (int)$cart_id, true);
	}
	
	*/
}
?>
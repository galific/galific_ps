<?php

class blockinvitereferrals extends Module {
	
	public function __construct() {
		$this->name = 'blockinvitereferrals';
		$this->tab = version_compare(_PS_VERSION_, '1.4.0.0', '>=')?'advertising_marketing':'InviteReferrals';
		$this->version = '1.0';
		$this->need_instance = 0;
		$this->author = 'InviteReferrals';
		$this->module_key = "120d817df1e1ceecfe06f2f716073c29";
		//$this->bootstrap = true;
		parent::__construct();
		$this->displayName = $this->l('Block InviteReferrals');
		$this->description = $this->l('Simplest tool to launch Customer Referral Campaigns to boost invites and site traffic.');
		
	}
	
	function install()
	{
		if (!parent::install() 
			OR !$this->registerHook('footer')
			OR !$this->registerHook('displayorderConfirmation')
			OR !Configuration::updateValue('BRANDID', ''))
			   
			return false;
			
		return true;
	}
	
	function uninstall()
	{
		if (!Configuration::deleteByName('BRANDID') OR !parent::uninstall())
			return false;
		return true;
	}
	
	public function getContent()
	{
		$output = '<h2><img src="'.$this->_path.'logo.gif" alt="" /> '.$this->displayName.'</h2>';
		if (Tools::isSubmit('submitblockinvitereferrals'))
		{
			$enablemodule = Tools::getValue('enablemodule');			
			Configuration::updateValue('ENABLEMODULE', $enablemodule);
			
			$brandid = Tools::getValue('brandid');			
			Configuration::updateValue('BRANDID', $brandid);
				
			$secretkey = Tools::getValue('secretkey');
			Configuration::updateValue('SECRETKEY', $secretkey);			
			
			$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="Confirmation" />Settings updated</div>';
			
			
			
			
			
		}
		return $output.$this->displayForm();
	}

	public function displayForm()
	{
		global $cookie;
		
		$enabled = Configuration::get('ENABLEMODULE');
		$enabledString = '';					        
		if ($enabled) { 
        	$enabledString = '<option value="1" selected="selected">Yes</option><option value="0">NO</option>';
        } else {
        	$enabledString = '<option value="1">YES</option><option value="0" selected="selected">NO</option>';
        }        
		
		return '<form action="'.$_SERVER['REQUEST_URI'].'" method="post">		
			<p>Above all things, subscribe to <a href="http://www.invitereferrals.com" title="Above all things, subscribe to invitereferrals" target="_blank" style="color:orange"><b>InviteReferrals</b></a></p>
			<fieldset>			
				<legend><a href="http://www.invitereferrals.com"><img src="http://d11yp7khhhspcr.cloudfront.net/images/site/campaigns/site/inviteReferralsLogo3.png" alt="" /></a>Settings</legend>				
				
				<label>Enabled</label>
				<div class="margin-form">
					<select name="enablemodule">'.$enabledString.'</select>
				</div>
				<p class="margin-form">Select Yes/NO to Enable/Disable the module</p>
				<div class="clear"></div>							

				<label>Brandid</label>
				<div class="margin-form">
					<input type="text" name="brandid" value="'.Configuration::get('BRANDID').'" />
				</div>
				<br>
				<div class="clear"></div>
				
				<label>Secret Key</label>
				<div class="margin-form">
					<input type="text" name="secretkey" value="'.Configuration::get('SECRETKEY').'" />
					
					
				<div class="bg-info col-md-12 lead well" style="font-size:15px;">
				
		  Please Follow Instructions:
		  <br/><br/>
		  Sign into your invitereferrals.com account. Then go to the <b><a target="_blank" href="http://www.invitereferrals.com/campaign/documentation/plugins">Invitereferrals Module section</a></b> <br>and <b>copy your brandID and secretKey</b>
		  <br/></br>
		  Follow this <b><a target="_blank" href="http://www.invitereferrals.com/blog/install-Free-Prestashop-Module-Refer-a-Friend-Program">Module Configuration instructions here</a></b>
		  </div>
		  
				
				
				<center><input type="submit" name="submitblockinvitereferrals" value="'.$this->l('Save').'" class="button" /></center>
				
			</fieldset>
		</form>		
		';
	}
	
	
	function hookdisplayOrderConfirmation($params)
	{
		if(empty($params['objOrder']->reference))
		{
			return 1;
		}
		
		global $smarty;
				
		$brandid = Configuration::get('BRANDID');
		$isModuleEnabled = Configuration::get('ENABLEMODULE');
		$secretKey = Configuration::get('SECRETKEY');		
		
		if ($brandid) {
			$smarty->assign(array('brandid' => $brandid));			
			$smarty->assign(array('isModuleEnabled' => $isModuleEnabled));
			$smarty->assign(array('secretKey' => $secretKey));			
			//$smarty->assign(array('secretParam' => $secretParam));

			/* Get the customer information */
			if($this->context->customer->email){
				$email = $this->context->customer->email; // set email id
				$fname = $this->context->customer->firstname; // set first name
				$lname = $this->context->customer->lastname; // set last name
			} else {
				$email = ''; // set email id
				$fname = ''; // set first name
				$lname = ''; // set last name
			}
			
			$smarty->assign(array('email' => $email));
			$smarty->assign(array('fname' => $fname));
			$smarty->assign(array('lname' => $lname));
			
			$purchase_value = $params['total_to_pay'];
			$orderid = $params['objOrder']->reference;
			$smarty->assign(array('purchase_value' => $purchase_value));
			$smarty->assign(array('orderid' => $orderid));
			
			$t = time();
			$smarty->assign(array('tgNpinTime' => $t));
			
			$encodedParam = strtoupper(md5($secretKey.'|'.$brandid.'|'.$t.'|'.$email));
			$smarty->assign(array('encodedParam' => $encodedParam));
						
			return $this->display(__FILE__, 'blockinvitereferrals_pixel.tpl');
		}		
		return $output;
	}
	
	function hookFooter($params) {	
		global $smarty;
				
		$brandid = Configuration::get('BRANDID');
		$isModuleEnabled = Configuration::get('ENABLEMODULE');
		$secretKey = Configuration::get('SECRETKEY');		
		
		if ($brandid) {
			$smarty->assign(array('brandid' => $brandid));			
			$smarty->assign(array('isModuleEnabled' => $isModuleEnabled));
			$smarty->assign(array('secretKey' => $secretKey));			
			//$smarty->assign(array('secretParam' => $secretParam));

			/* Get the customer information */
			if($params['cart']->id_customer){
				$customer = new Customer((int)$params['cart']->id_customer);
				$email = $customer->email; // set email id
				$fname = $customer->firstname; // set first name
				$lname = $customer->lastname; // set last name
			} else {
				$email = ''; // set email id
				$fname = ''; // set first name
				$lname = ''; // set last name
			}
			
			$smarty->assign(array('email' => $email));
			$smarty->assign(array('fname' => $fname));
			$smarty->assign(array('lname' => $lname));
			
			$t = time();
			$smarty->assign(array('tgNpinTime' => $t));
			
			$encodedParam = strtoupper(md5($secretKey.'|'.$brandid.'|'.$t.'|'.$email));
			$smarty->assign(array('encodedParam' => $encodedParam));
						
			return $this->display(__FILE__, 'blockinvitereferrals.tpl');
		}		
		return $output;
	}

}

?>

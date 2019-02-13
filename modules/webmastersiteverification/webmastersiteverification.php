<?php
/*
* PrestaShop
* @link: 				http://www.prestashop.com/
*
* @script name: 		Webmaster Site Verification
* @purpose: 			Verify your online store with Google, Bing, Alexa, Yandex, WOT, Pinterest and Norton Safeweb
* @version: 			2.0
* @type:				Module
* @author: 				ByBe ( support@bybe.net )
* @copyright: 			(c) 2015 ByBe ( https://www.bybe.net/ )
*
*/
if (!defined('_PS_VERSION_'))
	exit;

class WebmasterSiteVerification extends Module
	{

	/* Fuction Constructor */
	function __construct()
		{
			$this->name = 'webmastersiteverification';
			$this->tab = 'analytics_stats';
			$this->version = '2.0';
			$this->author = 'bybe.net';
			$this->displayName = $this->l('Webmaster Site Verification');
			$this->description = $this->l('Verify your online stores with Google, Bing, Alexa, Norton, WOT, Yandex and Pinterest');
			$this->confirmUninstall = $this->l('Doh! Are you absolutely sure that you wish to remove Webmaster Site Verification?');

	parent::__construct();
		// Warnings
		if ($this->id AND !Configuration::get('GOOGLE') OR !Configuration::get('GOOGLEAPPS') OR !Configuration::get('BING') OR !Configuration::get('ALEXA') OR !Configuration::get('NORTON') OR !Configuration::get('WOT') OR !Configuration::get('PINTEREST') OR !Configuration::get('YANDEX'))
			$this->warning = $this->l('You have not entered any site verification IDs');
	}

	/* Install Hook */
	function install()
	{
		if (!parent::install() OR !$this->registerHook('header') OR !Configuration::updateValue('GOOGLE', '') OR !Configuration::updateValue('GOOGLEAPPS', '') OR !Configuration::updateValue('BING', '') OR !Configuration::updateValue('ALEXA', '') OR !Configuration::updateValue('NORTON', '') OR !Configuration::updateValue('WOT', '') OR !Configuration::updateValue('PINTEREST', '') OR !Configuration::updateValue('YANDEX', ''))
			return false;
		return true;
	}

	/* Uninstall */
	function uninstall()
		{
			if (!Configuration::deleteByName('GOOGLE') OR !Configuration::deleteByName('GOOGLEAPPS') OR !Configuration::deleteByName('BING') OR !Configuration::deleteByName('ALEXA') OR !Configuration::deleteByName('NORTON') OR !Configuration::deleteByName('WOT') OR !Configuration::deleteByName('PINTEREST') OR !Configuration::deleteByName('YANDEX') OR !parent::uninstall())
				return false;
			return true;
		}

	/* Backend Content */
	public function getContent()
		{
			$output = '<h2 style="margin-top:10px; line-height:0;">'.$this->displayName.' v'.$this->version.'</h2>';
			$output .= '<p>'.$this->description.'.</p>';

			if (Tools::isSubmit('submitGoogleId'))
			{
					Configuration::updateValue('GOOGLE', $_POST['googleId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Google Webmaster Tools ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitGoogleappsId'))
			{
					Configuration::updateValue('GOOGLEAPPS', $_POST['googleappsId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Google Apps ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitBingId'))
			{
					Configuration::updateValue('BING', $_POST['bingId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Bing Web Master Tools ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitAlexaId'))
			{
					Configuration::updateValue('ALEXA', $_POST['alexaId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Alexa ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitNortonId'))
			{
					Configuration::updateValue('NORTON', $_POST['nortonId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Norton Safeweb ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitWotId'))
			{
					Configuration::updateValue('WOT', $_POST['wotId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your WOT ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitPinterestId'))
			{
					Configuration::updateValue('PINTEREST', $_POST['pinterestId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Pinterest ID has been saved...').'</div>';
			}
			elseif (Tools::isSubmit('submitYandexId'))
			{
					Configuration::updateValue('YANDEX', $_POST['yandexId']);
					$output .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('Confirmation').'" />&nbsp;'.$this->l('Your Yandex Web Master Tools ID has been saved...').'</div>';
			}
			return $output.$this->displayForm();
		}

	/* Content Forms */
	public function displayForm()
		{
			return '
			<fieldset>
        <legend><a href="https://www.bybe.net/" target="_blank" title="Visit our Website"><img src="'.$this->_path.'img/bybe.png" alt="Icon" />Author</a></legend>
        <p><a href="https://www.bybe.net/" target="_blank" title="Visit our Website"><img src="'.$this->_path.'img/bybe-logo.png" alt="ByBe" /></a></p>
        <p>Feedback: <a href="mailto:support@bybe.net" title="Email us for some support">support@bybe.net</a><br />
        Website Link: <a href="https://www.bybe.net" target="_blank">www.bybe.net</a></p>
        <p><div style="height:30px;width:68px;"><div style="float:left;"><a href="https://twitter.com/bybe_net" target="_blank" title="Follow us on Twitter"><img src="'.$this->_path.'img/twitter.png" alt="Twitter" /></a></div><div style="float:right;"><a href="https://www.facebook.com/ByBeUK" target="_blank" title="Visit our Facebook Page"><img src="'.$this->_path.'img/facebook.png" alt="Facebook" /></a></div></div></p>
        <p>TIP: You can visit the verification sites by clicking the images below, these will open up in a new window.</p>
      </fieldset>
      <br />
      <form action="'.$_SERVER['REQUEST_URI'].'" method="post" style="margin-top:25px;">
      <fieldset>
        <legend><a href="https://www.google.com/webmasters/tools/" target="_blank" title="Visit Google Webmaster Tools"><img src="'.$this->_path.'img/google-s.png" alt="Offical Google Icon" title="" />Google Webmaster Tools</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="googleId" value="'.Configuration::get('GOOGLE').'" style="width:280px;"/><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Google Webmaster Tools ID.').'</div></td>
            <td width="160" valign="top"><a href="https://www.google.com/webmasters/tools/" target="_blank" title="Visit Google Webmaster Tools" style="margin-left:15px;"><img src="'.$this->_path.'img/google-b.png" alt="Google" style="border:1px solid #DFD5C3;padding:0;"/></a></td>
            <td width="150"><input type="submit" name="submitGoogleId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://www.google.com/work/apps/business/" target="_blank" title="Visit Google Apps"><img src="'.$this->_path.'img/google-apps-s.png" alt="Google Apps Icon" />Google Apps</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="googleappsId" value="'.Configuration::get('GOOGLEAPPS').'"  style="width:280px;" /><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Google Apps ID.').'</div></td>
            <td width="160" valign="top"><a href="https://www.google.com/work/apps/business/" target="_blank" title="Visit Google Apps" style="margin-left:15px;"><img src="'.$this->_path.'img/google-apps-b.png" alt="Google Apps" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
            <td width="150"><input type="submit" name="submitGoogleappsId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://www.bing.com/toolbox/webmaster/" target="_blank" title="Visit Bing Webmaster Tools"><img src="'.$this->_path.'img/bing-s.png" alt="Bing Webmaster Tools Icon" />Bing Webmaster Tools</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
        <tr>
          <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
          <td valign="top"><input type="text" name="bingId" value="'.Configuration::get('BING').'"  style="width:280px;" /><br />
          <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Bing Webmaster Tools ID.').'</div></td>
          <td width="160" valign="top"><a href="https://www.bing.com/toolbox/webmaster/" target="_blank" title="Visit Bing Webmaster Tools" style="margin-left:15px;"><img src="'.$this->_path.'img/bing-b.png" alt="Bing Webmaster Tools" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
          <td width="150"><input type="submit" name="submitBingId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
        </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://www.alexa.com/" target="_blank" title="Visit Alexa"><img src="'.$this->_path.'img/alexa-s.png" alt="Alexa Site Verify Icon" />Alexa.com</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="alexaId" value="'.Configuration::get('ALEXA').'"  style="width:280px;" /><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Alexa Site Verification ID.').'</div></td>
            <td width="160" valign="top"><a href="https://www.alexa.com/" target="_blank" title="Visit Alexa" style="margin-left:15px;"><img src="'.$this->_path.'img/alexa-b.png" alt="Alexa" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
            <td width="150"><input type="submit" name="submitAlexaId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://safeweb.norton.com/help/site_owners/" target="_blank" title="Visit Norton Safeweb"><img src="'.$this->_path.'img/symantec-s.png" alt="Norton Safeweb Icon" />Norton Safeweb</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="nortonId" value="'.Configuration::get('NORTON').'"  style="width:280px;" /><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Norton Safeweb Verification ID.').'</div></td>
            <td width="160" valign="top"><a href="https://safeweb.norton.com/help/site_owners/" target="_blank" title="Visit Norton Safeweb" style="margin-left:15px;"><img src="'.$this->_path.'img/symantec-b.png" alt="Norton" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
            <td width="150"><input type="submit" name="submitNortonId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://www.mywot.com/" target="_blank" title="Visit MyWot.com"><img src="'.$this->_path.'img/wot-s.png" alt="WOT Icon" />MyWOT.com</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="wotId" value="'.Configuration::get('WOT').'"  style="width:280px;" /><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your WOT Site Verify ID.').'</div></td>
            <td width="160" valign="top"><a href="https://www.mywot.com/" target="_blank" title="Visit MyWot.com" style="margin-left:15px;"><img src="'.$this->_path.'img/wot-b.png" alt="WOT" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
            <td width="150"><input type="submit" name="submitWotId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://www.pinterest.com/" target="_blank" title="Visit Pinterest"><img src="'.$this->_path.'img/pinterest-s.png" alt="pinterest Icon" />pinterest.com</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="pinterestId" value="'.Configuration::get('PINTEREST').'"  style="width:280px;" /><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Pinterest Site Verify ID.').'</div></td>
            <td width="160" valign="top"><a href="https://www.pinterest.com/" target="_blank" title="Visit Pinterest" style="margin-left:15px;"><img src="'.$this->_path.'img/pinterest-b.png" alt="pinterest" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
            <td width="150"><input type="submit" name="submitPinterestId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      <fieldset>
        <legend><a href="https://webmaster.yandex.com/" target="_blank" title="Visit Yandex Webmaster Tools"><img src="'.$this->_path.'img/yandex-s.png" alt="Yandex Webmaster" />Yandex Webmaster Tools</a></legend>
        <table border="0" width="850" cellpadding="0" cellspacing="0" id="form">
          <tr>
            <td width="200" valign="top"><label>'.$this->l('Verification ID').'</label></td>
            <td valign="top"><input type="text" name="yandexId" value="'.Configuration::get('YANDEX').'"  style="width:280px;" /><br />
            <div style="font-size:10px;padding-top:5px;width:240px;">'.$this->l('Please enter your Yandex Webmaster Tools ID.').'</div></td>
            <td width="160" valign="top"><a href="https://webmaster.yandex.com/" target="_blank" title="Visit Yandex Webmaster Tools" style="margin-left:15px;"><img src="'.$this->_path.'img/yandex-b.png" alt="Yandex" style="border:1px solid #DFD5C3;padding:0;" /></a></td>
            <td width="150"><input type="submit" name="submitYandexId" value="'.$this->l('Save').'" class="button bybe" style="background:#8bc954!important;border-radius:0!important;color:white;font-size:20px;height:60px;font-weight:300;margin:0 15px;text-shadow:none;text-transform:uppercase;width:100px;"/></td>
          </tr>
        </table>
      </fieldset>
      <br />
      </form>
      <br />
      <fieldset>
        <legend><img src="'.$this->_path.'img/link.png" alt="Icon" />Donate a Link</legend>
          <p>If you like this module then please show your support by linking to our site, simply use the code below. Thank You for your Support.</p>
          <div class="warn">
            <p>&lt;div&gt;&lt;a href=&quot;https://www.bybe.net&quot;&gt;ByBe&lt;/a&gt;&lt;/div&gt;</p>
          </div>
      </fieldset>';
	}

	/* Variables */

	function hookHeader($params)
	{
		global $smarty;

		$googleId = Configuration::get('GOOGLE');
		$googleappsId = Configuration::get('GOOGLEAPPS');
		$bingId = Configuration::get('BING');
		$alexaId = Configuration::get('ALEXA');
		$nortonId = Configuration::get('NORTON');
		$wotId = Configuration::get('WOT');
		$pinterestId = Configuration::get('PINTEREST');
		$yandexId = Configuration::get('YANDEX');

		if (strlen(Configuration::get('GOOGLE')) > 0)
			$smarty->assign('google_pass_thou', $googleId);
		if (strlen(Configuration::get('GOOGLEAPPS')) > 0)
			$smarty->assign('googleapps_pass_thou', $googleappsId);
		if (strlen(Configuration::get('BING')) > 0)
			$smarty->assign('bing_pass_thou', $bingId);
		if (strlen(Configuration::get('ALEXA')) > 0)
			$smarty->assign('alexa_pass_thou', $alexaId);
		if (strlen(Configuration::get('NORTON')) > 0)
			$smarty->assign('norton_pass_thou', $nortonId);
		if (strlen(Configuration::get('WOT')) > 0)
			$smarty->assign('wot_pass_thou', $wotId);
		if (strlen(Configuration::get('PINTEREST')) > 0)
			$smarty->assign('pinterest_pass_thou', $pinterestId);
		if (strlen(Configuration::get('YANDEX')) > 0)
			$smarty->assign('yandex_pass_thou', $yandexId);

		return $this->display(__FILE__, 'webmastersiteverification.tpl');
	}
}
?>

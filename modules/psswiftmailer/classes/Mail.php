<?php
/**
  * 2007-2015 PrestaShop
  *
  * NOTICE OF LICENSE
  *
  * This source file is subject to the Open Software License (OSL 3.0)
  * that is bundled with this package in the file LICENSE.txt.
  * It is also available through the world-wide-web at this URL:
  * http://opensource.org/licenses/osl-3.0.php
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
  *  @author    PrestaShop SA <contact@prestashop.com>, Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2007-2015 PrestaShop SA, 2016 Inveo s.r.o.
  *  @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  *  @version   5.0
  *  International Registered Trademark & Property of PrestaShop SA
  */

class MailCore extends ObjectModel
{
	public $id;

	/** @var string Recipient */
	public $recipient;

	/** @var string Template */
	public $template;

	/** @var string Subject */
	public $subject;

	/** @var int Language ID */
	public $id_lang;

	/** @var int Timestamp */
	public $date_add;

	/**
	* @see ObjectModel::$definition
	*/
	public static $definition = array(
		'table' => 'mail',
		'primary' => 'id_mail',
		'fields' => array(
			'recipient' => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'copy_post' => false, 'required' => true, 'size' => 126),
			'template' => array('type' => self::TYPE_STRING, 'validate' => 'isTplName', 'copy_post' => false, 'required' => true, 'size' => 62),
			'subject' => array('type' => self::TYPE_STRING, 'validate' => 'isMailSubject', 'copy_post' => false, 'required' => true, 'size' => 254),
			'id_lang' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false, 'required' => true),
			'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false, 'required' => true),
		),
	);
	const TYPE_HTML = 1;
	const TYPE_TEXT = 2;
	const TYPE_BOTH = 3;

	/**
	* Send Email
	*
	* @param int $id_lang Language ID of the email (to translate the template)
	* @param string $template Template: the name of template not be a var but a string !
	* @param string $subject Subject of the email
	* @param string $template_vars Template variables for the email
	* @param string $to To email
	* @param string $to_name To name
	* @param string $from From email
	* @param string $from_name To email
	* @param array $file_attachment Array with three parameters (content, mime and name). You can use an array of array to attach multiple files
	* @param bool $mode_smtp SMTP mode (deprecated)
	* @param string $template_path Template path
	* @param bool $die Die after error
	* @param string $bcc Bcc recipient
	* @return bool|int Whether sending was successful. If not at all, false, otherwise amount of recipients succeeded.
	*/
	public static function Send(
					$id_lang, $template, $subject, $template_vars, $to,
					$to_name = null, $from = null, $from_name = null, $file_attachment = null, $mode_smtp = null,
					$template_path = _PS_MAIL_DIR_, $die = false, $id_shop = null, $bcc = null, $reply_to = null
	)
	{
		if(!self::checkSwiftMailer($die))
			return false;
	
		if(!is_array($template_vars))
			$template_vars = array();

		$theme_path = _PS_THEME_DIR_;

		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			if(!$id_shop)
				$id_shop = Context::getContext()->shop->id;

			$configuration = Configuration::getMultiple(
				array(
					'PS_SHOP_EMAIL',
					'PS_MAIL_METHOD',
					'PS_MAIL_SERVER',
					'PS_MAIL_USER',
					'PS_MAIL_PASSWD',
					'PS_SHOP_NAME',
					'PS_MAIL_SMTP_ENCRYPTION',
					'PS_MAIL_SMTP_PORT',
					'PS_MAIL_TYPE'
				), null, null, $id_shop
			);

			// Get the path of theme by id_shop if exist
			if(is_numeric($id_shop) && $id_shop) {
				$shop = new Shop((int)$id_shop);
				$theme_name = $shop->getTheme();

				if(_THEME_NAME_ != $theme_name)
					$theme_path = _PS_ROOT_DIR_.'/themes/'.$theme_name.'/';
			}
		}
		else
		{
			$id_shop = 1;
			$configuration = Configuration::getMultiple(
				array(
					'PS_SHOP_EMAIL',
					'PS_MAIL_METHOD',
					'PS_MAIL_SERVER',
					'PS_MAIL_USER',
					'PS_MAIL_PASSWD',
					'PS_SHOP_NAME',
					'PS_MAIL_SMTP_ENCRYPTION',
					'PS_MAIL_SMTP_PORT',
					'PS_MAIL_TYPE'
				)
			);
		}

		// Returns immediately if emails are deactivated
		if($configuration['PS_MAIL_METHOD'] == 3)
			return true;
            
		if (!isset($configuration['PS_MAIL_SMTP_ENCRYPTION']))
			$configuration['PS_MAIL_SMTP_ENCRYPTION'] = 'off';
		if(!isset($configuration['PS_MAIL_SMTP_PORT']))
			$configuration['PS_MAIL_SMTP_PORT'] = null;

		if(!isset($from) || !Validate::isEmail($from))
			$from = $configuration['PS_SHOP_EMAIL'];

		if(!isset($from_name) || !Validate::isMailName($from_name))
			$from_name = $configuration['PS_SHOP_NAME'];

		if(!Validate::isEmail($from)) // swift requires from
			self::dieOrLog('Error: parameter "from" is corrupted', $die);

		if(!Validate::isMailName($from_name))
			 $from_name = null;
		
		if(is_array($to) && count($to) == 1)
			$to = (string)$to[0];

		if(!is_array($to) && !Validate::isEmail($to))
		{
			self::dieOrLog('Error: parameter "to" is corrupted', $die);
			return false;
		}

		// if bcc is not null, make sure it's a vaild e-mail
		if(!is_null($bcc) && !is_array($bcc) && !Validate::isEmail($bcc))
		{
			self::dieOrLog('Error: parameter "bcc" is corrupted', $die);
			$bcc = null;
		}

		// Do not crash for this error, that may be a complicated customer name
		if(is_string($to_name) && !empty($to_name) && !Validate::isMailName($to_name))
			$to_name = null;

		if(!Validate::isTplName($template))
		{
			self::dieOrLog('Error: invalid e-mail template', $die);
			return false;
		}

		if(!Validate::isMailSubject($subject))
		{
			self::dieOrLog('Error: invalid e-mail subject', $die);
			return false;
		}
		
		self::loadSwift();

		$message = Swift_Message::newInstance();
		$message->setFrom(array($from => $from_name));
		$addrAr = array();
		/* Construct multiple recipients list if needed */
		if(is_array($to) && isset($to))
		{
			foreach($to as $key => $addr)
			{
				$addr = trim($addr);
				if(!Validate::isEmail($addr))
					self::dieOrLog('Error: mail parameters are corrupted', $die);
				if($to_name && is_array($to_name) && Validate::isGenericName($to_name[$key]))
					$to_name = $to_name[$key];
				$to_name = (($to_name == null || $to_name == $addr) ? '' : $to_name);
				//$message->addTo($addr, $to_name);
				$addrAr[$addr] = $to_name;
			}
		}
		else
		{
			/* Simple recipient, one address */
			$to_name = (($to_name == null || $to_name == $to) ? '' : $to_name);
			$addrAr[$to] = $to_name;
			//$message->addTo($to, $to_name);
		}
		$message->setTo($addrAr);
		if(isset($bcc))
			$message->addBcc($bcc);

		try {
			/* Connect with the appropriate configuration */
			if(
				(int)($configuration['PS_MAIL_METHOD']) == 2
					&&
				// use smtp only for the shop domain
				Tools::substr($from, strpos($from, '@') + 1) == Tools::substr($configuration['PS_SHOP_EMAIL'], strpos($configuration['PS_SHOP_EMAIL'], '@') + 1))
			{
				$transport = Swift_SmtpTransport::newInstance(
									$configuration['PS_MAIL_SERVER'],
									$configuration['PS_MAIL_SMTP_PORT']
									);
				if($configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'ssl' || $configuration['PS_MAIL_SMTP_ENCRYPTION'] == 'tls')
					$transport->setEncryption($configuration['PS_MAIL_SMTP_ENCRYPTION']);
				$transport->setTimeout(4);
				if(!$transport)
					return false;
				if(!empty($configuration['PS_MAIL_USER']))
					$transport->setUsername($configuration['PS_MAIL_USER']);
				if(!empty($configuration['PS_MAIL_PASSWD']))
					$transport->setPassword($configuration['PS_MAIL_PASSWD']);
			} else {
				$transport = Swift_MailTransport::newInstance();
			}

			if(!$transport)
				return false;

			$mailer = Swift_Mailer::newInstance($transport);
			/* Get templates content */
			$iso = Language::getIsoById((int)($id_lang));
			if(!$iso)
			{
				self::dieOrLog('Error - No ISO code for email', $die);
				return false;
			}
			$iso_template = $iso.'/'.$template;
			
			if(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				$module_name = false;
				$override_mail = false;

				if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
				{
					$shop_uri = $shop->physical_uri;
				}
				else
				{
					$shop_uri = __PS_BASE_URI__;
				}

				// get templatePath
				if(preg_match('#'.$shop_uri.'modules/#', str_replace(DIRECTORY_SEPARATOR, '/', $template_path)) && preg_match('#modules/([a-z0-9_-]+)/#ui', str_replace(DIRECTORY_SEPARATOR, '/', $template_path), $res))
					$module_name = $res[1];

				if($module_name !== false && (file_exists($theme_path.'modules/'.$module_name.'/mails/'.$iso_template.'.txt') || file_exists($theme_path.'modules/'.$module_name.'/mails/'.$iso_template.'.html')))
				{
					$template_path = $theme_path.'modules/'.$module_name.'/mails/';
				}
				elseif(file_exists($theme_path.'mails/'.$iso_template.'.txt') || file_exists($theme_path.'mails/'.$iso_template.'.html'))
				{
					$template_path = $theme_path.'mails/';
					$override_mail  = true;
				}
			}

			if(!file_exists($template_path.$iso_template.'.txt') && ($configuration['PS_MAIL_TYPE'] == self::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == self::TYPE_TEXT))
			{
				self::dieOrLog('Error - The following e-mail template is missing: '.$template_path.$iso_template.'.txt', $die);
				return false;
			} elseif(!file_exists($template_path.$iso_template.'.html') && ($configuration['PS_MAIL_TYPE'] == self::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == self::TYPE_HTML))
			{
				self::dieOrLog('Error - The following e-mail template is missing: '.$template_path.$iso_template.'.html', $die);
				return false;
			}
			
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$template_html = '';
				$template_txt = '';

				if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
					Hook::exec('actionEmailAddBeforeContent', array(
						'template' => $template,
						'template_html' => &$template_html,
						'template_txt' => &$template_txt,
						'id_lang' => (int)$id_lang
					), null, true);

				$template_html .= file_get_contents($template_path.$iso_template.'.html');
				$template_txt .= strip_tags(html_entity_decode(file_get_contents($template_path.$iso_template.'.txt'), null, 'utf-8'));
				
				if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
					Hook::exec('actionEmailAddAfterContent', array(
						'template' => $template,
						'template_html' => &$template_html,
						'template_txt' => &$template_txt,
						'id_lang' => (int)$id_lang
					), null, true);

				if($override_mail && file_exists($template_path.$iso.'/lang.php'))
				{
					include_once($template_path.$iso.'/lang.php');
				}
				elseif($module_name && file_exists($theme_path.'mails/'.$iso.'/lang.php'))
				{
					include_once($theme_path.'mails/'.$iso.'/lang.php');
				}
				elseif(file_exists(_PS_MAIL_DIR_.$iso.'/lang.php'))
				{
					include_once(_PS_MAIL_DIR_.$iso.'/lang.php');
				}
				else
				{
					self::dieOrLog('Error - The language file is missing for: '.$iso, $die);
					return false;
				}
			}
			else
			{
				$template_html = file_get_contents($template_path.$iso_template.'.html');
				$template_txt = strip_tags(html_entity_decode(file_get_contents($template_path.$iso_template.'.txt'), null, 'utf-8'));
				include_once(dirname(__FILE__).'/../mails/'.$iso.'/lang.php');
			}

			if(version_compare(_PS_VERSION_, '1.3.0.0', '>=') && version_compare(_PS_VERSION_, '1.4.0.0', '<'))
			{
				global $_LANGMAIL;
				$subject = ((is_array($_LANGMAIL) && key_exists($subject, $_LANGMAIL)) ? $_LANGMAIL[$subject] : $subject);
			}

			/* Create mail and attach differents parts */
			$message->setSubject($subject);
			$message->setCharset('utf-8');
			
			if(!($reply_to && Validate::isEmail($reply_to)))
				$reply_to = $from;

			if(isset($reply_to) && $reply_to)
				$message->setReplyTo($reply_to);

			/* Set Message-ID - getmypid() is blocked on some hosting */
			if(version_compare(_PS_VERSION_, '1.6.1.5', '<'))
			{
				$message->setId(self::getCleanId(self::generateId()));
			}
			else
			{
				$message->setId(self::generateId());
			}

			$message->setEncoder(Swift_Encoding::getQpEncoding());
			
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
				{
					$template_vars = array_map(array('Tools', 'htmlentitiesDecodeUTF8'), $template_vars);
					$template_vars = array_map(array('Tools', 'stripslashes'), $template_vars);
				}
				
				if(strpos($template_html, '{shop_logo}') !== false)
					if(Configuration::get('PS_LOGO_MAIL') !== false && file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $id_shop)))
					{
						$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO_MAIL', null, null, $id_shop);
					}
					else
					{
						if(file_exists(_PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop)))
							$logo = _PS_IMG_DIR_.Configuration::get('PS_LOGO', null, null, $id_shop);
					}
				ShopUrl::cacheMainDomainForShop((int)$id_shop);
				
				if((Context::getContext()->link instanceof Link) === false)
					Context::getContext()->link = new Link();
				
				$template_vars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME', null, null, $id_shop));
				$template_vars['{shop_url}'] = Context::getContext()->link->getPageLink('index', true, Context::getContext()->language->id, null, false, $id_shop);
				$template_vars['{my_account_url}'] = Context::getContext()->link->getPageLink('my-account', true, Context::getContext()->language->id, null, false, $id_shop);
				$template_vars['{guest_tracking_url}'] = Context::getContext()->link->getPageLink('guest-tracking', true, Context::getContext()->language->id, null, false, $id_shop);
				$template_vars['{history_url}'] = Context::getContext()->link->getPageLink('history', true, Context::getContext()->language->id, null, false, $id_shop);
				$template_vars['{color}'] = Tools::safeOutput(Configuration::get('PS_MAIL_COLOR', null, null, $id_shop));
				
				if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
				{
					// Get extra template_vars
					$extra_template_vars = array();
					Hook::exec('actionGetExtraMailTemplateVars', array(
						'template' => $template,
						'template_vars' => $template_vars,
						'extra_template_vars' => &$extra_template_vars,
						'id_lang' => (int)$id_lang
					), null, true);
					$template_vars = array_merge($template_vars, $extra_template_vars);
				}
			}
			else
			{
				if(strpos($template_html, '{shop_logo}') !== false && file_exists(_PS_IMG_DIR_.'logo.jpg'))
					$logo = _PS_IMG_DIR_.'logo.jpg';
				$template_vars['{shop_name}'] = Tools::safeOutput(Configuration::get('PS_SHOP_NAME'));
				$template_vars['{shop_url}'] = 'http://'.Tools::getHttpHost(false, true).__PS_BASE_URI__;
			}
			
			if(isset($logo))
			{
				$template_vars['{shop_logo}'] = $message->embed(Swift_Image::fromPath($logo));
			}
			else
			{
				$template_vars['{shop_logo}'] = '';
			}

			$to_plugin = array();
			foreach($addrAr as $addr => $addName)
				$to_plugin[$addr] = $template_vars;

			$mailer->registerPlugin(new Swift_Plugins_DecoratorPlugin($to_plugin));

			if($configuration['PS_MAIL_TYPE'] == self::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == self::TYPE_TEXT)
				$message->addPart(self::smtpRelay($to, $id_lang, $id_shop, $template_txt, false), 'text/plain');

			if($configuration['PS_MAIL_TYPE'] == self::TYPE_BOTH || $configuration['PS_MAIL_TYPE'] == self::TYPE_HTML)
				$message->addPart(self::smtpRelay($to, $id_lang, $id_shop, $template_html, true), 'text/html');
			
			if($file_attachment && !empty($file_attachment))
			{
				// Multiple attachments?
				if(!is_array(current($file_attachment)))
					$file_attachment = array($file_attachment);

				foreach($file_attachment as $attachment)
					if(isset($attachment['content']) && isset($attachment['name']) && isset($attachment['mime']))
						$message->attach(Swift_Attachment::newInstance($attachment['content'], $attachment['name'], $attachment['mime']));
			}
			/* Send mail */
			$send = $mailer->send($message);
			
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				ShopUrl::resetMainDomainCache();
				
				if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
					if($send && Configuration::get('PS_LOG_EMAILS')) {
						$mail = new Mail();
						$mail->template = Tools::substr($template, 0, 62);
						$mail->subject = Tools::substr($subject, 0, 254);
						$mail->id_lang = (int)$id_lang;

						$recepientsAr = array();
						if(is_array($message->getTo()))
							$recepientsAr = array_merge($message->getTo(), $recepientsAr);
						if(is_array($message->getCc()))
							$recepientsAr = array_merge($message->getCc(), $recepientsAr);
						if(is_array($message->getBcc()))
							$recepientsAr = array_merge($message->getBcc(), $recepientsAr);

						foreach(array_flip($recepientsAr) as $recipient)
						{
							/** @var Swift_Address $recipient */
							$mail->id = null;
							$mail->recipient = Tools::substr($recipient, 0, 126);
							$mail->add();
						}
					}
			}

			return $send;
		} catch (Swift_SwiftException $e) {
			if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
			{
				PrestaShopLogger::addLog(
					'Swift Error: '.$e->getMessage(),
					3,
					null,
					'Swift_Message'
				);
			}
			return false;
		}
	}
	
	/**
	* @param $id_mail Mail ID
	* @return bool Whether removal succeeded
	*/
	public static function eraseLog($id_mail)
	{
		return Db::getInstance()->delete('mail', 'id_mail = '.(int)$id_mail);
	}

	/**
	* @return bool
	*/
	public static function eraseAllLogs()
	{
		return Db::getInstance()->execute('TRUNCATE TABLE '._DB_PREFIX_.'mail');
	}

	/**
	* Send a test email
	*
	* @param bool $smtp_checked Is SMTP checked?
	* @param string $smtp_server SMTP Server hostname
	* @param string $content Content of the email
	* @param string $subject Subject of the email
	* @param bool $type Deprecated
	* @param string $to To email address
	* @param string $from From email address
	* @param string $smtp_login SMTP login name
	* @param string $smtp_password SMTP password
	* @param int $smtp_port SMTP Port
	* @param bool|string $smtp_encryption Encryption type. "off" or false disable encryption.
	* @return bool|string True if succeeded, otherwise the error message
	*/
	public static function sendMailTest($smtp_checked, $smtp_server, $content, $subject, $type, $to, $from, $smtp_login, $smtp_password, $smtp_port = 25, $smtp_encryption)
	{
		if(!self::checkSwiftMailer(false))
			return false;
		self::loadSwift();
		try {
			$result = false;
			if($smtp_checked)
			{
				$transport = Swift_SmtpTransport::newInstance(
									$smtp_server,
									$smtp_port
									);
				if($smtp_encryption == 'ssl' || $smtp_encryption == 'tls')
					$transport->setEncryption($smtp_encryption);
				if(!empty($smtp_login))
					$transport->setUsername($smtp_login);
				if(!empty($smtp_password))
					$transport->setPassword($smtp_password);
				$transport->setTimeout(5);
			} else {
				$transport = Swift_MailTransport::newInstance();
			}
			$mailer = Swift_Mailer::newInstance($transport);
			
			$message = Swift_Message::newInstance();
			$message->setFrom($from);
			$message->addTo($to);
			$message->setSubject($subject);
			$message->setCharset('utf-8');
			
			if(version_compare(_PS_VERSION_, '1.6.1.5', '<'))
			{
				$message->setId(self::getCleanId(self::generateId()));
			}
			else
			{
				$message->setId(self::generateId());
			}

			$message->setEncoder(Swift_Encoding::getQpEncoding());
			$message->addPart($content, $type);

			if($mailer->send($message))
				$result = true;

		}
		catch (Swift_TransportException $e)
		{
			$result = false;
		}

		return $result;
	}
	
	/**
	* This method is used to get the translation for email Object.
	* For an object is forbidden to use htmlentities,
	* we have to return a sentence with accents.
	*
	* @param string $string raw sentence (write directly in file)
	* @return mixed
	*/
	public static function l($string, $id_lang = null, Context $context = null)
	{
		global $_LANGMAIL;
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			if(!$context) {
				$context = Context::getContext();
			}
			if($id_lang == null) {
				$id_lang = (!isset($context->language) || !is_object($context->language)) ? (int)Configuration::get('PS_LANG_DEFAULT') : (int)$context->language->id;
			}
			$iso_code = Language::getIsoById((int)$id_lang);

			$file_core = _PS_ROOT_DIR_.'/mails/'.$iso_code.'/lang.php';
			if(Tools::file_exists_cache($file_core) && empty($_LANGMAIL)) {
				include($file_core);
			}

			$file_theme = _PS_THEME_DIR_.'mails/'.$iso_code.'/lang.php';
			if(Tools::file_exists_cache($file_theme)) {
				include($file_theme);
			}

			if(!is_array($_LANGMAIL)) {
				return (str_replace('"', '&quot;', $string));
			}
			
			if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
			{
				$key = str_replace('\'', '\\\'', $string);
				return str_replace('"', '&quot;', Tools::stripslashes((array_key_exists($key, $_LANGMAIL) && !empty($_LANGMAIL[$key])) ? $_LANGMAIL[$key] : $string));
			}
			else
			{
				if(array_key_exists($key, $_LANGMAIL) && !empty($_LANGMAIL[$key]))
					$str = $_LANGMAIL[$key];
				else
					$str = $string;

				return str_replace('"', '&quot;', stripslashes($str));
			}
		}
		else
		{
			global $cookie;

			$key = str_replace('\'', '\\\'', $string);

			if ($id_lang == null)
				$id_lang = (!isset($cookie) || !is_object($cookie)) ? (int)_PS_LANG_DEFAULT_ : (int)$cookie->id_lang;

			$file_core = _PS_ROOT_DIR_.'/mails/'.Language::getIsoById((int)$id_lang).'/lang.php';
			if (file_exists($file_core) && empty($_LANGMAIL))
				include_once($file_core);

			$file_theme = _PS_THEME_DIR_.'mails/'.Language::getIsoById((int)$id_lang).'/lang.php';
			if (file_exists($file_theme))
				include_once($file_theme);

			if (!is_array($_LANGMAIL))
				return (str_replace('"', '&quot;', $string));
			if (key_exists($key, $_LANGMAIL))
				$str = $_LANGMAIL[$key];
			else
				$str = $string;

			return str_replace('"', '&quot;', stripslashes($str));
		}
	}
	
	/**
	* Check if a multibyte character set is used for the data
	*
	* @param string $data Data
	* @return bool Whether the string uses a multibyte character set
	*/
	protected static function generateId($idstring = null)
	{
		$midparams = array(
			'utctime' => gmstrftime('%Y%m%d%H%M%S'),
			'randint' => mt_rand(),
			'customstr' => (preg_match("/^(?<!\\.)[a-z0-9\\.]+(?!\\.)\$/iD", $idstring) ? $idstring : "swift") ,
			'hostname' => (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : php_uname('n')),
		);
		if(version_compare(_PS_VERSION_, '1.6.1.5', '<'))
		{
			return vsprintf("<%s.%d.%s@%s>", $midparams);
		}
		else
		{
			return vsprintf("%s.%d.%s@%s", $midparams);
		}
	}
	
	/**
	* Check if a multibyte character set is used for the data
	*
	* @param string $data Data
	* @return bool Whether the string uses a multibyte character set
	*/
	public static function isMultibyte($data)
	{
		$length = Tools::strlen($data);
		for ($i = 0; $i < $length; $i++) {
		if(ord(($data[$i])) > 128) {
			return true;
		}
		}
		return false;
	}

	/**
	* MIME encode the string
	*
	* @param string $string The string to encode
	* @param string $charset The character set to use
	* @param string $newline The newline character(s)
	* @return mixed|string MIME encoded string
	*/
	public static function mimeEncode($string, $charset = 'UTF-8', $newline = "\r\n")
	{
		if(!self::isMultibyte($string) && Tools::strlen($string) < 75) {
			return $string;
		}

		$charset = Tools::strtoupper($charset);
		$start = '=?'.$charset.'?B?';
		$end = '?=';
		$sep = $end.$newline.' '.$start;
		$length = 75 - Tools::strlen($start) - Tools::strlen($end);
		$length = $length - ($length % 4);

		if($charset === 'UTF-8') {
			$parts = array();
			$maxchars = floor(($length * 3) / 4);
			$stringLength = Tools::strlen($string);

			while($stringLength > $maxchars) {
				$i = (int)$maxchars;
				$result = ord($string[$i]);

				while($result >= 128 && $result <= 191) {
					$result = ord($string[--$i]);
				}

				$parts[] = base64_encode(Tools::substr($string, 0, $i));
				$string = Tools::substr($string, $i);
				$stringLength = Tools::strlen($string);
			}

			$parts[] = base64_encode($string);
			$string = implode($sep, $parts);
		} else {
			$string = chunk_split(base64_encode($string), $length, $sep);
			$string = preg_replace('/'.preg_quote($sep).'$/', '', $string);
		}

		return $start.$string.$end;
	}
	
	protected static function getCleanId($id) // we will not touch the generateId() method to keep the compatibility
	{
		preg_match('/<(.*)>/', $id, $matches);
		return $matches[1];
	}
	
	protected static function dieOrLog($msg, $die)
	{
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			Tools::dieOrLog(Tools::displayError($msg), $die);
		}
		else
		{
			echo Tools::displayError($msg);
			if($die)
				exit();
		}
	}
	
	protected static function smtpRelay($to, $id_lang, $id_shop, $msg, $html){
		// add SMTP relay only to employees
		$emplEm = array();
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$emplEm[] = Configuration::get('PS_SHOP_EMAIL', null, null, $id_shop);
		}
		else
		{
			$emplEm[] = Configuration::get('PS_SHOP_EMAIL');
		}

		foreach(Contact::getContacts($id_lang) as $contact)
			$emplEm[] = $contact['email'];

		if(is_array($to) || !in_array($to, $emplEm))
			return $msg;
		
		if($html)
			return str_replace('</body>', '<div style="font-size: 10px; text-align: center; padding-top: 4px; border-top: 1px solid #ccc">copyright &copy; '.date('Y').' Swift Mailer for PrestaShop 1.3/1.4/1.5/1.6 by <a href="http://www.inveostore.com">www.inveostore.com</a><br />(the text above is displayed in the employees\' emails only)</div></body>', $msg);

		return $msg."\n".'copyright (c) '.date('Y').' Swift Mailer for PrestaShop 1.3/1.4/1.5/1.6 by www.inveostore.com'."\n".'(the text above is displayed in the employees\' emails only)';
	}
	
	protected static function checkSwiftMailer($die)
	{
		if(file_exists(PS_SWIFT_FILE))
			return true;
		
		$msg = 'Swift Mailer was not found. Please download Swift Mailer 5.4.x at https://github.com/swiftmailer/swiftmailer/releases and copy '.basename(PS_SWIFT_FILE).' file with all other files to '.basename(dirname(_PS_TOOL_DIR_)).DIRECTORY_SEPARATOR.basename(_PS_TOOL_DIR_).DIRECTORY_SEPARATOR.'swift5'.DIRECTORY_SEPARATOR.' directory retaining the directory structure.';
		if($die)
		{
			self::dieOrLog($msg, true);
		}
		else
		{
			trigger_error($msg, E_USER_WARNING);
			return false;
		}
	}
	
	protected static function loadSwift()
	{
		if(!defined('PS_SWIFT_LOADED'))
		{
			// making __autoload() spl compatible
			if(version_compare(_PS_VERSION_, '1.5.0.0', '<') && function_exists('__autoload')) // PS 1.3 & 1.4
			{
				if(version_compare(PHP_VERSION, '5.1.2', '>='))
				{
					// destroying __autoload magic function
					spl_autoload_register(array('MailCore', 'fakeAutoload'));
					spl_autoload_unregister(array('MailCore', 'fakeAutoload'));
					// re-registering spl compatible autoload
					spl_autoload_register(array('MailCore', 'splAutoload'));
				}
			}
			include_once(PS_SWIFT_FILE);
			define('PS_SWIFT_LOADED', true);
		}
	}
	
	public static function fakeAutoload($className)
	{
		return false;
	}
	
	public static function splAutoload($className)
	{
		if(version_compare(_PS_VERSION_, '1.4.0.0', '<')) // PS 1.3
		{
			$f = dirname(__FILE__).'/../classes/'.str_replace(chr(0), '', $className).'.php';
			if(!class_exists($className, false) && file_exists($f))
				require_once($f);
		}
		else // PS 1.4
		{
			__autoload($className);
		}
	}
}

// PS 1.3 compatibility layer
if(version_compare(_PS_VERSION_, '1.4.0.0', '<'))
{
	class Mail extends MailCore
	{
	}
}

define('PS_SWIFT_FILE', _PS_TOOL_DIR_.'swift5/swift_required.php');

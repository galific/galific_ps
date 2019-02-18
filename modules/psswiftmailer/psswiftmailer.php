<?php
/**
  *  Swift Mailer 5 for PrestaShop
  *
  *  @author    Inveo s.r.o. <inqueries@inveoglobal.com>
  *  @copyright 2009-2016 Inveo s.r.o.
  *  @license   EULA
  */

if (!defined('_PS_VERSION_'))
	exit;

class Psswiftmailer extends Module
{

	private $_swiftFile = '';
	private $_mailFile = '';

	public function __construct()
	{
		$this->name = 'psswiftmailer';
		$this->tab = 'others';
		$this->version = '5.0.02';
		$this->author = 'Inveo s.r.o.';
		$this->need_instance = 0;
		$this->displayName = $this->l('Swift Mailer 5 for PrestaShop');
		$this->description = $this->l('Add support for the latest Swift Mailer.');
		$this->confirmUninstall = sprintf($this->l('To fully uninstall the module, you will need to manually revert %s file. Do you want continue?'), current($this->_getClassFilenames()));
		if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
			$this->bootstrap = true;

		$this->_swiftFile = _PS_TOOL_DIR_.'swift5/swift_required.php';
		$this->_mailFile = 'Mail.php';

		parent::__construct();
	}

	public function install()
	{
		if(version_compare(_PS_VERSION_, '1.3', '<') || version_compare(_PS_VERSION_, '1.7', '>='))
			return $this->_returnError($this->displayName.' '.$this->version.' '.$this->l('supports only PrestaShop 1.3, 1.4, 1.5 and 1.6.'));
		
		if(!$this->_checkPermissions())
			return $this->_returnError(sprintf($this->l('Please make %s directory writable.'), dirname(current($this->_getClassFilenames()))));
		$this->_backupFiles();
		$this->_installFiles();

		if(!parent::install())
			return false;
		
		if(!$this->registerHook($this->_getBackOfficeHookName()))
			return false;

		Tools::redirectAdmin($this->_getLink());
		return true;
	}
	
	public function uninstall()
	{
		return parent::uninstall();
	}
	
	private function _getClassFilenames()
	{
		return array(_PS_MODULE_DIR_.$this->name.'/classes/'.$this->_mailFile => _PS_CLASS_DIR_.$this->_mailFile);
	}
	
	private function _backupFiles()
	{
		foreach($this->_getClassFilenames() as $fileSource => $fileTarget)
			if(file_exists($fileTarget))
			{
				if(@rename($fileTarget, $fileTarget.'_'.date('Y-m-d_H-i-s')))
					@chmod($fileTarget, 0666);
			}
			else
			{
				if(@file_put_contents($fileTarget, ''))
					@chmod($fileTarget, 0666);
			}
	}
	
	private function _installFiles()
	{
		foreach($this->_getClassFilenames() as $fileSource => $fileTarget)
			if(file_exists($fileSource))
				if(@copy($fileSource, $fileTarget))
					@chmod($fileTarget, 0666);
	}
	
	private function _checkPermissions()
	{
		foreach($this->_getClassFilenames() as $fileSource => $fileTarget)
			if(!is_writable(dirname($fileTarget)))
				return false;
		return true;
	}
	
	private function _checkSwiftMailer()
	{
		return file_exists($this->_swiftFile);
	}
	
	private function _returnError($msg)
	{
		if(version_compare(_PS_VERSION_, '1.5', '>='))
		{
			$this->_errors[] = $msg;
		}
		else
		{
			echo $this->displayError($msg);
		}
	
		return false;
	}

	public function getContent()
	{
		// 5.0.02 upgrade
		if(!$this->isRegisteredInHook($this->_getBackOfficeHookName()))
			$this->registerHook($this->_getBackOfficeHookName());

		$html = '';
		if(!$this->_checkSwiftMailer())
		{
			$html .= $this->displayError($this->_swiftMissing());
		}
		elseif(!method_exists('Mail', 'checkSwiftMailer'))
		{
			$html .= $this->displayError(sprintf($this->l('Swift Mailer 5.x class is not installed. Please re-install the %s module.'), $this->displayName));
		}
		else
		{
			$html .= $this->displayConfirmation($this->l('Swift Mailer is up and running!'));
		}

		if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
		{
			
			$this->context->controller->addCSS($this->_path.'views/css/inveocopyright.css');
			$this->smarty->assign(array(
						'mod_name' => $this->displayName,
						'mod_ver' => $this->version,
						'mod_copy_date' => date('Y')
						)
					);
			$html .= $this->display(__FILE__, 'views/templates/admin/intro.tpl');
		}
		else
		{
			$html = '<h2>'.$this->displayName.'</h2>'.$html;
			$html .=
				'<div class="clear"></div>'.
				'<div style="text-align: right; margin-top: 1em">'.
				$this->displayName.' '.$this->version.'<br /><br />'.
				'copyright &copy; 2012-'.date('Y').' Inveo<br />'.$this->l('PrestaShop Modules:').' <a href="http://www.inveostore.com" style="color:blue;text-decoration:underline">www.inveostore.com</a> | '.$this->l('eCommerce Services:').' <a href="http://www.inveo.us" style="color:blue;text-decoration:underline">www.inveo.us</a>'.
				'</div>';
		}
		return $html;
	}
	
	public function hookBackOfficeTop($params)
	{
		if(!$this->_checkSwiftMailer())
			return
				'<div style="color: #000; background-color: #FF0033; border: 1px solid #fff; padding: 5px; width: 100%; position: fixed; left: 0px !important; bottom: 0px !important; z-index: 99 !important">'.
				'<img src="../img/admin/warning.gif" alt="" title="">&nbsp;'.$this->_swiftMissing().' <a href="'.$this->_getLink().'" style="color: #000; text-decoration: underline !important">'.$this->l('Configure').' &raquo;</a></div>';
	}
	
	public function hookDisplayBackOfficeTop($params)
	{
		return $this->hookBackOfficeTop($params);
	}
	
	private function _getBackOfficeHookName()
	{
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$hook = 'displayBackOfficeTop';
		}
		else
		{
			$hook = 'backOfficeTop';
		}
		return $hook;
	}
	
	private function _swiftMissing()
	{
		return sprintf($this->l('Swift Mailer 5.x is missing. Please <a href=%s>download Swift Mailer</a> 5.4.x. and copy '.basename($this->_swiftFile).' file with all other files to '.basename(dirname(_PS_TOOL_DIR_)).DIRECTORY_SEPARATOR.basename(_PS_TOOL_DIR_).DIRECTORY_SEPARATOR.'swift5'.DIRECTORY_SEPARATOR.' directory retaining the directory structure.'), 'https://github.com/swiftmailer/swiftmailer/releases');
	}
	
	private function _getLink()
	{
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			return 'index.php?controller=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)Tab::getCurrentTabId().(int)$this->context->cookie->id_employee);
		return 'index.php?tab=AdminModules&configure='.$this->name.'&token='.Tools::getAdminToken('AdminModules'.(int)Tab::getCurrentTabId().(int)$GLOBALS['cookie']->id_employee);
	}
}

?>
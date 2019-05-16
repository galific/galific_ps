<?php
/**
 * PrestaShop module created by VEKIA, a guy from official PrestaShop community ;-)
 *
 * @author    VEKIA https://www.prestashop.com/forums/user/132608-vekia/
 * @copyright 2010-9999 VEKIA
 * @license   This program is not free software and you can't resell and redistribute it
 * @themes    You are allowed to insert this module - as is - to themes that you want to sell
 *
 * CONTACT WITH DEVELOPER http://mypresta.eu
 * support@mypresta.eu
 */
class mib extends Module
{
    function __construct()
    {
        ini_set("display_errors", 0);
        error_reporting(0);
        $this->mypresta_link = 'https://mypresta.eu/modules/front-office-features/manufacturers-brands-images-block.html';
        $this->name          = 'mib';
        $this->tab           = 'advertising_marketing';
        $this->author        = 'MyPresta.eu';
        $this->version       = '1.5.1';
        $this->bootstrap     = true;
        $this->module_key    = '5478cde3d84ec0d4b6f73b3f5c17dd33';
        parent::__construct();

        $this->displayName = $this->l('Manufacturers Images Block');
        $this->description = $this->l('Module creates block with manufacturer pictures with links');

        $this->checkforupdates();
    }

    public function hookactionAdminControllerSetMedia($params)
    {
        //for update feature purposes
    }

    public function checkforupdates($display_msg = 0, $form = 0)
    {
        // ---------- //
        // ---------- //
        // VERSION 14 //
        // ---------- //
        // ---------- //
        $this->mkey = "nlc";
        if (@file_exists('../modules/' . $this->name . '/key.php')) {
            @require_once('../modules/' . $this->name . '/key.php');
        } else {
            if (@file_exists(dirname(__FILE__) . $this->name . '/key.php')) {
                @require_once(dirname(__FILE__) . $this->name . '/key.php');
            } else {
                if (@file_exists('modules/' . $this->name . '/key.php')) {
                    @require_once('modules/' . $this->name . '/key.php');
                }
            }
        }
        if ($form == 1) {
            return '
            <div class="panel" id="fieldset_myprestaupdates" style="margin-top:20px;">
            ' . ($this->psversion() == 6 || $this->psversion() == 7 ? '<div class="panel-heading"><i class="icon-wrench"></i> ' . $this->l('MyPresta updates') . '</div>' : '') . '
			<div class="form-wrapper" style="padding:0px!important;">
            <div id="module_block_settings">
                    <fieldset id="fieldset_module_block_settings">
                         ' . ($this->psversion() == 5 ? '<legend style="">' . $this->l('MyPresta updates') . '</legend>' : '') . '
                        <form action="' . $_SERVER['REQUEST_URI'] . '" method="post">
                            <label>' . $this->l('Check updates') . '</label>
                            <div class="margin-form">' . (Tools::isSubmit('submit_settings_updates_now') ? ($this->inconsistency(0) ? '' : '') . $this->checkforupdates(1) : '') . '
                                <button style="margin: 0px; top: -3px; position: relative;" type="submit" name="submit_settings_updates_now" class="button btn btn-default" />
                                <i class="process-icon-update"></i>
                                ' . $this->l('Check now') . '
                                </button>
                            </div>
                            <label>' . $this->l('Updates notifications') . '</label>
                            <div class="margin-form">
                                <select name="mypresta_updates">
                                    <option value="-">' . $this->l('-- select --') . '</option>
                                    <option value="1" ' . ((int)(Configuration::get('mypresta_updates') == 1) ? 'selected="selected"' : '') . '>' . $this->l('Enable') . '</option>
                                    <option value="0" ' . ((int)(Configuration::get('mypresta_updates') == 0) ? 'selected="selected"' : '') . '>' . $this->l('Disable') . '</option>
                                </select>
                                <p class="clear">' . $this->l('Turn this option on if you want to check MyPresta.eu for module updates automatically. This option will display notification about new versions of this addon.') . '</p>
                            </div>
                            <label>' . $this->l('Module page') . '</label>
                            <div class="margin-form">
                                <a style="font-size:14px;" href="' . $this->mypresta_link . '" target="_blank">' . $this->displayName . '</a>
                                <p class="clear">' . $this->l('This is direct link to official addon page, where you can read about changes in the module (changelog)') . '</p>
                            </div>
                            <div class="panel-footer">
                                <button type="submit" name="submit_settings_updates"class="button btn btn-default pull-right" />
                                <i class="process-icon-save"></i>
                                ' . $this->l('Save') . '
                                </button>
                            </div>
                        </form>
                    </fieldset>
                    <style>
                    #fieldset_myprestaupdates {
                        display:block;clear:both;
                        float:inherit!important;
                    }
                    </style>
                </div>
            </div>
            </div>';
        } else {
            if (defined('_PS_ADMIN_DIR_')) {
                if (Tools::isSubmit('submit_settings_updates')) {
                    Configuration::updateValue('mypresta_updates', Tools::getValue('mypresta_updates'));
                }
                if (Configuration::get('mypresta_updates') != 0 || (bool)Configuration::get('mypresta_updates') != false) {
                    if (Configuration::get('update_' . $this->name) < (date("U") - 259200)) {
                        $actual_version = mibUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version);
                    }
                    if (mibUpdate::version($this->version) < mibUpdate::version(Configuration::get('updatev_' . $this->name))) {
                        $this->context->controller->warnings[] = '<strong>' . $this->displayName . '</strong>: ' . $this->l('New version available, check http://MyPresta.eu for more informations') . ' <a href="' . $this->mypresta_link . '">' . $this->l('More details in changelog') . '</a>';
                    }
                }
                if ($display_msg == 1) {
                    if (mibUpdate::version($this->version) < mibUpdate::version(mibUpdate::verify($this->name, (isset($this->mkey) ? $this->mkey : 'nokey'), $this->version))) {
                        return "<span style='color:red; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('New version available!') . "</span>";
                    } else {
                        return "<span style='color:green; font-weight:bold; font-size:16px; margin-right:10px;'>" . $this->l('Module is up to date!') . "</span>";
                    }
                }
            }
        }
    }

    public static function psversion($part = 1)
    {
        $version = _PS_VERSION_;
        $exp     = $explode = explode(".", $version);
        if ($part == 1) {
            return $exp[1];
        }
        if ($part == 2) {
            return $exp[2];
        }
        if ($part == 3) {
            return $exp[3];
        }
    }

    function install()
    {
        if ($this->psversion() == 7) {
            if (parent::install() == false OR !
                Configuration::updateValue('update_' . $this->name, '0') OR
                !$this->registerHook('displayLeftColumn') OR
                !$this->registerHook('displayRightColumn') OR
                !$this->registerHook('displayTop') OR
                !$this->registerHook('displayTopColumn') OR
                !$this->registerHook('displayFooter') OR
                !$this->registerHook('displayHome') OR
                !$this->registerHook('header')) {
                return false;
            }
        }

        return true;
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS(($this->_path) . 'mib.css', 'all');
        if (Configuration::get('MIB_CAROUSEL') == 1) {
            $this->context->controller->addCSS(($this->_path) . 'lib/css/lightslider.css', 'all');
            $this->context->controller->addJS(($this->_path) . 'lib/js/lightslider.js', 'all');
            $this->context->controller->addJS(($this->_path) . 'views/js/mib.js', 'all');
        }
    }

    public function displayAdvert()
    {
        return $this->display(__file__, 'views/templates/admin/admin.tpl');
    }

    public function buildManufacturersList($params)
    {
        $manufacturers = Manufacturer::getManufacturers();
        foreach ($manufacturers as &$manufacturer) {
            $manufacturer['image'] = $this->context->language->iso_code . '-default';
            if (file_exists(_PS_MANU_IMG_DIR_ . $manufacturer['id_manufacturer'] . '-' . ImageType::getFormatedName('small') . '.jpg')) {
                $manufacturer['image'] = $manufacturer['id_manufacturer'];
            }
            $manufacturer['image_url'] = $manufacturer['id_manufacturer'] . '-' . ImageType::getFormatedName('small') . '.jpg';
        }
        $this->context->smarty->assign('manufacturers', $manufacturers);

        return $this->display(__FILE__, 'mib.tpl');
    }

    public function hookHome($params)
    {
        if (Configuration::get('MIB_WHERE') == 'displayHome') {
            return $this->buildManufacturersList($params);
        }
    }

    public function hookdisplayLeftColumn($params)
    {
        if (Configuration::get('MIB_WHERE') == 'displayLeftColumn') {
            return $this->buildManufacturersList($params);
        }
    }

    public function hookdisplayRightColumn($params)
    {
        if (Configuration::get('MIB_WHERE') == 'displayRightColumn') {
            return $this->buildManufacturersList($params);
        }
    }

    public function hookdisplayTop($params)
    {
        if (Configuration::get('MIB_WHERE') == 'displayTop') {
            return $this->buildManufacturersList($params);
        }
    }

    public function hookdisplayTopColumn($params)
    {
        if (Configuration::get('MIB_WHERE') == 'displayTopColumn') {
            return $this->buildManufacturersList($params);
        }
    }

    public function hookdisplayFooter($params)
    {
        if (Configuration::get('MIB_WHERE') == 'displayFooter') {
            return $this->buildManufacturersList($params);
        }
    }

    public function getContent()
    {
        if (Tools::isSubmit('btnSubmit')) {
            Configuration::updateValue('MIB_CAROUSEL', Tools::getValue('MIB_CAROUSEL'));
            Configuration::updateValue('MIB_WHERE', Tools::getValue('MIB_WHERE'));
        }

        return $this->displayAdvert() . $this->displayForm() . $this->checkforupdates(0, 1);
    }

    public function inconsistency()
    {
        return;
    }

    public function displayForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon'  => 'icon-wrench'
                ),
                'input'  => array(
                    array(
                        'type'    => 'select',
                        'label'   => $this->l('Position of manufacturers block'),
                        'desc'    => $this->l(''),
                        'name'    => 'MIB_WHERE',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id'   => 'displayLeftColumn',
                                    'name' => $this->l('Left column') . ' (displayLeftColumn)',
                                ),
                                array(
                                    'id'   => 'displayRightColumn',
                                    'name' => $this->l('Right column') . ' (displayRightColumn)',
                                ),
                                array(
                                    'id'   => 'displayHome',
                                    'name' => $this->l('Homepage') . ' (displayHome)',
                                ),
                                array(
                                    'id'   => 'displayTop',
                                    'name' => $this->l('Top') . ' (displayTop)',
                                ),
                                array(
                                    'id'   => 'displayTopColumn',
                                    'name' => $this->l('Top column') . ' (displayTopColumn)',
                                ),
                                array(
                                    'id'   => 'displayFooter',
                                    'name' => $this->l('Footer') . ' (displayFooter)',
                                ),
                            ),
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                    array(
                        'type'    => 'select',
                        'label'   => $this->l('Enable carousel'),
                        'desc'    => $this->l('Feature - when active - will create a carousel with brands'),
                        'name'    => 'MIB_CAROUSEL',
                        'options' => array(
                            'query' => array(
                                array(
                                    'id'   => '0',
                                    'name' => $this->l('Disable')
                                ),
                                array(
                                    'id'   => '1',
                                    'name' => $this->l('Enable')
                                ),
                            ),
                            'id'    => 'id',
                            'name'  => 'name'
                        )
                    ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                )
            ),
        );

        $helper                           = new HelperForm();
        $helper->show_toolbar             = false;
        $helper->default_form_language    = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->submit_action            = 'btnSubmit';
        $helper->currentIndex             = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token                    = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars                 = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'languages'    => $this->context->controller->getLanguages(),
            'id_language'  => $this->context->language->id
        );

        return $helper->generateForm(array($fields_form));
    }

    public function getConfigFieldsValues()
    {
        return array(
            'MIB_CAROUSEL' => Tools::getValue('MIB_CAROUSEL', Configuration::get('MIB_CAROUSEL')),
            'MIB_WHERE'    => Tools::getValue('MIB_WHERE', Configuration::get('MIB_WHERE')),
        );
    }

}

class mibUpdate extends mib
{
    public static function version($version)
    {
        $version = (int)str_replace(".", "", $version);
        if (strlen($version) == 3) {
            $version = (int)$version . "0";
        }
        if (strlen($version) == 2) {
            $version = (int)$version . "00";
        }
        if (strlen($version) == 1) {
            $version = (int)$version . "000";
        }
        if (strlen($version) == 0) {
            $version = (int)$version . "0000";
        }

        return (int)$version;
    }

    public static function encrypt($string)
    {
        return base64_encode($string);
    }

    public static function verify($module, $key, $version)
    {
        if (ini_get("allow_url_fopen")) {
            if (function_exists("file_get_contents")) {
                $actual_version = @file_get_contents('http://dev.mypresta.eu/update/get.php?module=' . $module . "&version=" . self::encrypt($version) . "&lic=$key&u=" . self::encrypt(_PS_BASE_URL_ . __PS_BASE_URI__));
            }
        }
        Configuration::updateValue("update_" . $module, date("U"));
        Configuration::updateValue("updatev_" . $module, $actual_version);

        return $actual_version;
    }
}

?>
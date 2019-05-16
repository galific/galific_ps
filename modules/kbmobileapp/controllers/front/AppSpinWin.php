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

class KbMobileAppAppSpinWinModuleFrontController extends ModuleFrontController
{
    public $controller_name = 'AppSpinWin';
    public $module_name = 'kbmobileapp';
    public $app_version = 1.1;
    public $error = array();

    /*
     * Build an front controller
     */
    public function __construct()
    {
        parent::__construct();
    }

  
    public function initContent()
    {
        parent::initContent();
        $this->context = Context::getContext();
        if (Module::isInstalled('spinwheel')) {
            $show_wheel = true;
            $every_visit_flag = true;
            $new_visit_flag = true;
            $return_visit_flag = true;
            $all_visitor = true;
            $show_on_page = true;
            $mobile_only = false;
            $display_interval_flag = true;
            $config = Tools::unserialize(Configuration::get('SPIN_WHEEL'));
            $lang = $this->context->language->id;
            //Check if enable
            if ($config['enable'] == 1) {
                $title_text = $config['spin_wheel_title_text'][$lang];
                $description = $config['spin_wheel_subtitle_text'][$lang];
                $rules = $config['spin_wheel_rules_text'][$lang];
                $rule_points = explode(PHP_EOL, $rules);
                $this->context->smarty->assign(array(
                    'title_text' => $title_text,
                    'description' => $description,
                    'rules_name' => $rule_points));
                $sql = "SELECT * FROM " . _DB_PREFIX_ . "wheel_slices ws INNER JOIN " . _DB_PREFIX_ . "wheel_label_lang wll ON ws.id_slice = wll.id_slice WHERE "
                    . " wll.lang_id = '" . (int) $this->context->language->id . "'";
                $slices = Db::getInstance()->executeS($sql);
                $label_name = array();
                foreach ($slices as $slice) {
                    $label_name[] = $slice['slice_label'];
                }
                $mobile_class = 'Mobile';
                //Check pages
                $show_on_page = true;
                $show_wheel = false;
                if ($config['coupon_display_options'] == 1) {
                    $this->context->smarty->assign('display_option', 1);
                } elseif ($config['coupon_display_options'] == 2) {
                    $this->context->smarty->assign('display_option', 2);
                } else {
                    $this->context->smarty->assign('display_option', '');
                }
                $mobile_only = true;
                if ($config['wheel_design'] == '2') {
                    $wheel_color = $config['wheel_color'];
                } else {
                    $wheel_color = '';
                }
                    $this->context->smarty->assign('time_display', '');
                    $this->context->smarty->assign('scroll_display', '');
                    $this->context->smarty->assign('exit_display', false);
                $this->context->smarty->assign(array(
                    'wheel_design' => $config['wheel_design'],
                    'wheel_color' => $wheel_color,
                    'theme' => $config['theme'],
                    'wheel_sound' => $config['wheel_sound'],
                    'cust_name' => $config['cust_name'],
                    'req_cust_name' => $config['req_cust_name'],
                    'spin_button_color' => $config['background_color_spin'],
                    'cancel_button_color' => $config['background_color_cancel'],
                    'background_color_wheel' => $config['background_color_wheel'],
                    'text_color_wheel' => $config['text_color_wheel'],
                    'show_popup' => 0,
                    'email_recheck' => $config['email_recheck'],
                    'show_fireworks' => $config['show_fireworks'],
                    'wheel_device' => $mobile_class,
                    'custom_css' => $config['custom_css'],
                    'custom_js' => $config['custom_js'],
                    'text_color_popup' => $config['text_color_popup'],
                    'font_family' => $config['font_family'],
                    'Wheel_Display_Interval' => $config['display_interval'],
                ));

                //Get Privacy Policy setting
                $gdpr = array();
                if ($config['GDPR_status'] == 1) {
                    $getActiveServiceList = array();
                    if ($config['GDPR_advance'] == 1) {
                        $getActiveServiceListSql = 'Select sws.service_id, is_manadatory, description from '._DB_PREFIX_.'spin_wheel_services sws join '._DB_PREFIX_.'spin_wheel_service_lang  swsl on sws.service_id = swsl.service_id where sws.status = 1 and swsl.lang_id = '.(int)$lang.' order by sws.service_id asc';
                        $getActiveServiceList = Db::getInstance()->executeS($getActiveServiceListSql);
                    }
                    $gdpr['status'] = 1;
                    $gdpr['privacy_text'] = $config['spin_wheel_privacy_text'][$lang];
                    $gdpr['privacy_link'] = $config['privacy_link'];
                    $gdpr['advance'] = $config['GDPR_advance'];
                    $gdpr['services'] = $getActiveServiceList;
                }
                if (count($gdpr)) {
                    $this->context->smarty->assign('gdpr', $gdpr);
                }
                if ($config['display_image'] == 1) {
                    if (isset($config['image_path'])) {
                        if (strpos($config['image_path'], 'show.jpg') == false) {
                            $this->context->smarty->assign('front_image_path', $config['image_path']);
                        }
                    }
                }
                $custom_ssl_var = 0;
                if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                    $custom_ssl_var = 1;
                }
                if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
                    $ps_base_url = _PS_BASE_URL_SSL_;
                } else {
                    $ps_base_url = _PS_BASE_URL_;
                }
                $this->context->smarty->assign('rootDirectory', _PS_MODULE_DIR_ . 'spinwheel');
                $this->context->smarty->assign('spinwheel_base_url', $ps_base_url . __PS_BASE_URI__);
                $link = $this->context->link->getModuleLink('spinwheel', 'framespinwheel');
                $this->context->smarty->assign('spin_wheel_front_path', $link);
                /* changes by rishabh jain */
                $path = __PS_BASE_URI__.'modules/spinwheel';
                /* changes over */
                $this->context->smarty->assign('path', $path);
                $this->context->smarty->assign('label_name', $label_name);
                //active/expire date
                //$this->setFrontMedia();
                /*start:changes added by Aayushi Agarwal on 5 Dec 2018 to remove issue of displaying
                 * popup at all the web view pages
                 */
                $this->context->cookie->kbmobileappspin = "1";
                /*end*/
                $this->setTemplate('module:kbmobileapp/views/templates/front/spinwheel_container.tpl');
            }
        }
    }
    public function setMedia()
    {
        parent::setMedia();
        $this->addCSS($this->getModuleDirUrl() . 'spinwheel/views/css/front/spin_wheel.css');
        $this->addCSS('https://fonts.googleapis.com/css?family=Baloo+Bhaijaan|Merriweather|Roboto|Acme|Bree+Serif|Cinzel|Gloria+Hallelujah|Indie+Flower|Pacifico');
        $this->addJs($this->getModuleDirUrl() . 'spinwheel/views/js/front/velsof_wheel.js');
        $this->addJs($this->getModuleDirUrl() . 'kbmobileapp/views/js/spin_wheel.js');
        $this->addJs($this->getModuleDirUrl() . 'spinwheel/views/js/velovalidation.js');
        $this->addJs($this->getModuleDirUrl() . 'spinwheel/views/js/front/tooltipster.js');
        $this->addJs($this->getModuleDirUrl() . 'spinwheel/views/js/front/jquery.fireworks.js');
        $this->addCSS($this->getModuleDirUrl() . 'spinwheel/views/css/front/tooltipster.css');
    }

    private function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }
    
    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;

        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }
}

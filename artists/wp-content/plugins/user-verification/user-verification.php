<?php
/*
Plugin Name: User Verification
Plugin URI: http://pickplugins.com
Description: Verify user before access on your website.
Version: 1.0.25
WC requires at least: 3.0.0
WC tested up to: 3.5
Text Domain: user-verification
Domain Path: /languages
Author: PickPlugins
Author URI: http://pickplugins.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

if ( ! defined('ABSPATH')) exit;  // if direct access 


class UserVerification{
	
	public function __construct(){
	
		$this->uv_define_constants();

        $this->uv_loading_functions();
		//$this->uv_declare_classes();
		$this->uv_declare_actions();
		$this->uv_loading_script();
        add_action( 'init', array( $this, 'uv_declare_classes' ));


		add_action( 'init', array( $this, 'textdomain' ));


	}


	public function textdomain() {

		$locale = apply_filters( 'plugin_locale', get_locale(), 'user-verification' );
		load_textdomain('user-verification', WP_LANG_DIR .'/user-verification/user-verification-'. $locale .'.mo' );

		load_plugin_textdomain( 'user-verification', false, plugin_basename( dirname( __FILE__ ) ) . '/languages/' );
	}


	public function uv_loading_functions() {
		
		require_once( UV_PLUGIN_DIR . 'includes/functions.php');
		require_once( UV_PLUGIN_DIR . 'includes/functions-woocommerce.php');
		require_once( UV_PLUGIN_DIR . 'includes/functions-recaptcha.php');
        require_once( UV_PLUGIN_DIR . 'includes/functions-paid-memberships-pro.php');
        require_once( UV_PLUGIN_DIR . 'includes/functions-ultimate-member.php');


	}
	

	public function uv_loading_script() {
	
		add_action( 'admin_enqueue_scripts', 'wp_enqueue_media' );
		add_action( 'wp_enqueue_scripts', array( $this, 'uv_front_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'uv_admin_scripts' ) );
	}
	
	
	public function uv_declare_actions() {

		require_once( UV_PLUGIN_DIR . 'includes/actions/action-uv-registration.php');
	}
	
	public function uv_declare_classes() {

        require_once( UV_PLUGIN_DIR . 'includes/classes/class-wp-admin-menu.php');

        require_once( UV_PLUGIN_DIR . 'includes/classes/class-emails.php');
		require_once( UV_PLUGIN_DIR . 'includes/classes/class-settings.php');
		require_once( UV_PLUGIN_DIR . 'includes/classes/uv-class-column-users.php');	
	}
	
	public function uv_define_constants() {

		$this->_define('UV_PLUGIN_URL', plugins_url('/', __FILE__)  );
		$this->_define('UV_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		$this->_define('UV_PLUGIN_NAME', __('User Verification','user-verification') );
		$this->_define('UV_CONTACT_URL', 'http://pickplugins.com/contact' );

	}
	
	private function _define( $name, $value ) {
		if( $name && $value )
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}
		
	public function uv_front_scripts(){
		
		wp_enqueue_script('jquery');

		wp_enqueue_script('uv_front_js', plugins_url( '/assets/front/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script( 'uv_front_js', 'uv_ajax', array( 'uv_ajaxurl' => admin_url( 'admin-ajax.php')));
		
		wp_enqueue_style('uv_style', UV_PLUGIN_URL.'assets/front/css/style.css');	
		
		//global
        wp_enqueue_style('fontawesome', UV_PLUGIN_URL.'assets/global/css/fontawesome.min.css');
	}

	public function uv_admin_scripts(){
		
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-accordion');
		
		wp_enqueue_script('uv_admin_js', plugins_url( '/assets/admin/js/scripts.js' , __FILE__ ) , array( 'jquery' ));
		wp_localize_script( 'uv_admin_js', 'uv_ajax', array( 'uv_ajaxurl' => admin_url( 'admin-ajax.php')));
		wp_localize_script( 'uv_admin_js', 'L10n_user_verification', array(
			'confirm_text' => __( 'Are you sure?', 'user-verification' ),
			'reset_confirm_text' => __( 'Do you really want to reset?', 'user-verification' ),
			'text_approve_now' => __( 'Approve now', 'user-verification' ),
			'text_remove_approve' => __( 'Remove Approval', 'user-verification' ),
			'text_updateing' => __( 'Updating user', 'user-verification' ),
		));
							
		wp_enqueue_style('uv_admin_style', UV_PLUGIN_URL.'assets/admin/css/style.css');
        wp_enqueue_style('jquery-ui', UV_PLUGIN_URL.'assets/global/css/jquery-ui.css');

		
		// Global
		wp_enqueue_style('fontawesome', UV_PLUGIN_URL.'assets/global/css/fontawesome.min.css');

	}
} 

new UserVerification();
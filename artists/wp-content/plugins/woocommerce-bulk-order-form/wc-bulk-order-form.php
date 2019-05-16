<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @since             3.0
 * @package           WooCommerce Bulk Order Form
 *
 * @wordpress-plugin
 * Plugin Name:          WooCommerce Bulk Order Form
 * Plugin URI:           https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * Description:          Adds the [wcbulkorder] shortcode which allows you to display bulk order forms on any page in your site
 * Version:              3.1.1
 * Author:               Ewout Fernhout, Varun Sridharan, Jeremiah Prummer
 * Author URI:           https://wpovernight.com/
 * License:              GPL-2.0+
 * License URI:          http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:          woocommerce-bulk-order-form
 * Domain Path:          /languages
 * WC requires at least: 2.6.14
 * WC tested up to:      3.6.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WC_BOF_FILE', plugin_basename( __FILE__ ) );
define( 'WC_BOF_PATH', plugin_dir_path( __FILE__ ) ); # Plugin DIR
define( 'WC_BOF_INC', WC_BOF_PATH . 'includes/' ); # Plugin INC Folder
define( 'WC_BOF_DEPEN', 'woocommerce/woocommerce.php' );

register_activation_hook( __FILE__, 'wc_bof_activate_plugin' );
register_deactivation_hook( __FILE__, 'wc_bof_deactivate_plugin' );
register_deactivation_hook( WC_BOF_DEPEN, 'wc_bof_dependency_plugin_deactivate' );
add_action( 'admin_init', 'wc_bof_activate_redirect' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function wc_bof_activate_plugin() {
	add_option( 'wc_bof_activate_redirect', true );
	require_once( WC_BOF_INC . 'helpers/class-activator.php' );
	WooCommerce_Bulk_Order_Form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function wc_bof_deactivate_plugin() {
	require_once( WC_BOF_INC . 'helpers/class-deactivator.php' );
	WooCommerce_Bulk_Order_Form_Deactivator::deactivate();
}


/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function wc_bof_dependency_plugin_deactivate() {
	require_once( WC_BOF_INC . 'helpers/class-deactivator.php' );
	WooCommerce_Bulk_Order_Form_Deactivator::dependency_deactivate();
}


function wc_bof_activate_redirect() {
	if ( get_option( 'wc_bof_activate_redirect', false ) ) {
		delete_option( 'wc_bof_activate_redirect' );
		if ( ! isset( $_GET['activate-multi'] ) ) {
			wp_redirect( wc_bof_settings_page_link() );
		}
	}
}

require_once( WC_BOF_INC . 'functions.php' );
require_once( plugin_dir_path( __FILE__ ) . 'bootstrap.php' );

if ( ! function_exists( 'WooCommerce_Bulk_Order_Form' ) ) {
	/**
	 * @return null|\WooCommerce_Bulk_Order_Form
	 */
	function WooCommerce_Bulk_Order_Form() {
		return WooCommerce_Bulk_Order_Form::get_instance();
	}
}
WooCommerce_Bulk_Order_Form();

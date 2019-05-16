<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/core
 * @since 3.0
 */
class WooCommerce_Bulk_Order_Form_Activator {
	/**
	 * WooCommerce_Bulk_Order_Form_Activator constructor.
	 */
	public function __construct() {
	}

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		require_once( WC_BOF_INC . 'helpers/class-version-check.php' );
		require_once( WC_BOF_INC . 'helpers/class-dependencies.php' );

		if ( WooCommerce_Bulk_Order_Form_Dependencies( WC_BOF_DEPEN ) ) {
			WooCommerce_Bulk_Order_Form_Version_Check::activation_check( '3.7' );
		} else {
			if ( is_plugin_active( WC_BOF_FILE ) ) {
				deactivate_plugins( WC_BOF_FILE );
			}
			wp_die( wc_bof_dependency_message() );
		}
	}
}

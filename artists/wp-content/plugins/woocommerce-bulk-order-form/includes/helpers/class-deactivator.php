<?php

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/core
 * @since 3.0
 */
class WooCommerce_Bulk_Order_Form_Deactivator {
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

	}

	public static function dependency_deactivate() {
		if ( is_plugin_active( WC_BOF_FILE ) ) {
			add_action( 'update_option_active_plugins', array( __CLASS__, 'deactivate_dependent' ) );
		}
	}

	/**
	 * @static
	 */
	public static function deactivate_dependent() {
		deactivate_plugins( WC_BOF_FILE );
	}

}

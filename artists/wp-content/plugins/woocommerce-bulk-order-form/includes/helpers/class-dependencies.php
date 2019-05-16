<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/core
 * @since 3.0
 */

if ( ! class_exists( 'WooCommerce_Bulk_Order_Form_Dependencies' ) ) {
	/**
	 * Class WooCommerce_Bulk_Order_Form_Dependencies
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class WooCommerce_Bulk_Order_Form_Dependencies {
		/**
		 * active_plugins
		 *
		 * @var
		 */
		private static $active_plugins;

		/**
		 * @static
		 */
		public static function init() {
			self::$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() ) {
				self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
			}
		}

		/**
		 * @param string $pluginToCheck
		 *
		 * @return bool
		 * @static
		 */
		public static function active_check( $pluginToCheck = '' ) {
			if ( ! self::$active_plugins ) {
				self::init();
			}
			return in_array( $pluginToCheck, self::$active_plugins ) || array_key_exists( $pluginToCheck, self::$active_plugins );
		}
	}
}
/**
 * WC Detection
 */
if ( ! function_exists( 'WooCommerce_Bulk_Order_Form_Dependencies' ) ) {
	function WooCommerce_Bulk_Order_Form_Dependencies( $pluginToCheck = 'woocommerce/woocommerce.php' ) {
		return WooCommerce_Bulk_Order_Form_Dependencies::active_check( $pluginToCheck );
	}
}

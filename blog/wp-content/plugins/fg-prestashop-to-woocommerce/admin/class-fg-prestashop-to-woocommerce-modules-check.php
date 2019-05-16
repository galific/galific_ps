<?php

/**
 * Module to check the modules that are needed
 *
 * @link       https://wordpress.org/plugins/fg-prestashop-to-woocommerce/
 * @since      3.9.0
 *
 * @package    FG_Prestashop_to_WooCommerce
 * @subpackage FG_Prestashop_to_WooCommerce/admin
 */

if ( !class_exists('FG_Prestashop_to_WooCommerce_Modules_Check', false) ) {

	/**
	 * Class to check the modules that are needed
	 *
	 * @package    FG_Prestashop_to_WooCommerce
	 * @subpackage FG_Prestashop_to_WooCommerce/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Prestashop_to_WooCommerce_Modules_Check {

		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Check if some modules are needed
		 *
		 */
		public function check_modules() {
			$premium_url = 'https://www.fredericgilles.net/fg-prestashop-to-woocommerce/';
			$message_premium = __('Your Prestashop database contains %s. You need the <a href="%s" target="_blank">Premium version</a> to import them.', 'fg-prestashop-to-woocommerce');
			if ( defined('FGP2WCP_LOADED') ) {
				// Message for the Premium version
				$message_addon = __('Your Prestashop database contains %1$s. You need the <a href="%3$s" target="_blank">%4$s</a> to import them.', 'fg-prestashop-to-woocommerce');
			} else {
				// Message for the free version
				$message_addon = __('Your Prestashop database contains %1$s. You need the <a href="%2$s" target="_blank">Premium version</a> and the <a href="%3$s" target="_blank">%4$s</a> to import them.', 'fg-prestashop-to-woocommerce');
			}
			$modules = array(
				// Check if we need the Premium version: check the number of customers
				array(array($this, 'count'),
					array('customer', 1),
					'fg-prestashop-to-woocommerce-premium/fg-prestashop-to-woocommerce-premium.php',
					sprintf($message_premium, __('several customers', 'fg-prestashop-to-woocommerce'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of attributes
				array(array($this, 'count'),
					array('attribute', 0),
					'fg-prestashop-to-woocommerce-premium/fg-prestashop-to-woocommerce-premium.php',
					sprintf($message_premium, __('some attributes', 'fg-prestashop-to-woocommerce'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of accessories
				array(array($this, 'count'),
					array('accessory', 0),
					'fg-prestashop-to-woocommerce-premium/fg-prestashop-to-woocommerce-premium.php',
					sprintf($message_premium, __('some accessories', 'fg-prestashop-to-woocommerce'), $premium_url)
				),
				
				// Check if we need the Premium version: check the number of orders
				array(array($this, 'count'),
					array('orders', 1),
					'fg-prestashop-to-woocommerce-premium/fg-prestashop-to-woocommerce-premium.php',
					sprintf($message_premium, __('some orders', 'fg-prestashop-to-woocommerce'), $premium_url)
				),
				
				// Check if we need the Brands module
				array(array($this, 'count'),
					array('manufacturer', 1),
					'fg-prestashop-to-woocommerce-premium-brands-module/fgp2wc-brands.php',
					sprintf($message_addon, __('several manufacturers', 'fg-prestashop-to-woocommerce'), $premium_url, $premium_url . 'brands/', __('Brands add-on', 'fg-prestashop-to-woocommerce'))
				),
				
				// Check if we need the WPML module
				array(array($this, 'count'),
					array('lang', 1),
					'fg-prestashop-to-woocommerce-premium-wpml-module/fgp2wc-wpml.php',
					sprintf($message_addon, __('several languages', 'fg-prestashop-to-woocommerce'), $premium_url, $premium_url . 'wpml/', __('WPML add-on', 'fg-prestashop-to-woocommerce'))
				),
				
				// Check if we need the Customer Groups module
				array(array($this, 'count'),
					array('group', 3),
					'fg-prestashop-to-woocommerce-premium-customer-groups-module/fgp2wc-customer-groups.php',
					sprintf($message_addon, __('customer groups', 'fg-prestashop-to-woocommerce'), $premium_url, $premium_url . 'customer-groups/', __('Customer Groups add-on', 'fg-prestashop-to-woocommerce'))
				),
				
			);
			foreach ( $modules as $module ) {
				list($callback, $params, $plugin, $message) = $module;
				if ( !is_plugin_active($plugin) ) {
					if ( call_user_func_array($callback, $params) ) {
						$this->plugin->display_admin_warning($message);
					}
				}
			}
		}

		/**
		 * Count the number of rows in the table
		 *
		 * @param string $table Table
		 * @param int $min_value Minimum value to trigger the warning message
		 * @return bool Trigger the warning or not
		 */
		private function count($table, $min_value) {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "SELECT COUNT(*) AS nb FROM ${prefix}${table}";
			return ($this->count_sql($sql) > $min_value);
		}

		/**
		 * Execute the SQL request and return the nb value
		 *
		 * @param string $sql SQL request
		 * @return int Count
		 */
		private function count_sql($sql) {
			$count = 0;
			$result = $this->plugin->prestashop_query($sql, false);
			if ( isset($result[0]['nb']) ) {
				$count = $result[0]['nb'];
			}
			return $count;
		}

	}
}

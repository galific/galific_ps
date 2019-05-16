<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.fredericgilles.net/fg-prestashop-to-woocommerce/
 * @since             2.0.0
 * @package           FG_PrestaShop_to_WooCommerce
 *
 * @wordpress-plugin
 * Plugin Name:       FG PrestaShop to WooCommerce
 * Plugin URI:        https://www.fredericgilles.net/fg-prestashop-to-woocommerce/
 * Description:       A plugin to migrate PrestaShop e-commerce solution to WooCommerce
 * Version:           3.47.3
 * Author:            Frédéric GILLES
 * Author URI:        https://www.fredericgilles.net/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       fg-prestashop-to-woocommerce
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'FGP2WC_PLUGIN_VERSION', '3.47.3' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-fg-prestashop-to-woocommerce-activator.php
 */
function activate_FG_PrestaShop_to_WooCommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fg-prestashop-to-woocommerce-activator.php';
	FG_PrestaShop_to_WooCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-fg-prestashop-to-woocommerce-deactivator.php
 */
function deactivate_FG_PrestaShop_to_WooCommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-fg-prestashop-to-woocommerce-deactivator.php';
	FG_PrestaShop_to_WooCommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_FG_PrestaShop_to_WooCommerce' );
register_deactivation_hook( __FILE__, 'deactivate_FG_PrestaShop_to_WooCommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-fg-prestashop-to-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    2.0.0
 */
function run_FG_PrestaShop_to_WooCommerce() {

	$plugin = new FG_PrestaShop_to_WooCommerce();
	$plugin->run();

}
run_FG_PrestaShop_to_WooCommerce();

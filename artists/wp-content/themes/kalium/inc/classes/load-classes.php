<?php
/**
 *    Kalium WordPress Theme
 *
 *    Laborator.co
 *    www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

$current_dir = dirname( __FILE__ );

return apply_filters( 'kalium_load_classes', array(
	// Helpers and utils (order matters)
	'Kalium_Helpers'          => array( 'path' => $current_dir . '/core/kalium-helpers.php' ),
	'Kalium_URL'              => array( 'path' => $current_dir . '/utility/kalium-url.php' ),

	// Generic classes
	'Kalium_WP_Hook_Value'    => array(
		'path'            => $current_dir . '/core/kalium-hook-value.php',
		'global_instance' => false,
	),

	// Core classes
	'Kalium_Theme_License'    => array( 'path' => $current_dir . '/core/kalium-theme-license.php' ),
	'Kalium_Theme_Upgrader'   => array( 'path' => $current_dir . '/core/kalium-theme-upgrader.php' ),
	'Kalium_Theme_Plugins'    => array( 'path' => $current_dir . '/core/kalium-theme-plugins.php' ),
	'Kalium_Version_Upgrades' => array( 'path' => $current_dir . '/core/kalium-version-upgrades.php' ),
	'Kalium_Translations'     => array( 'path' => $current_dir . '/core/kalium-translations.php' ),
	'Kalium_Images'           => array( 'path' => $current_dir . '/core/kalium-images.php' ),
	'Kalium_Media'            => array( 'path' => $current_dir . '/core/kalium-media.php' ),

	// Plugin compatibility
	'Kalium_WooCommerce'      => array( 'path' => $current_dir . '/compatibility/kalium-woocommerce.php' ),
	'Kalium_WPBakery'         => array( 'path' => $current_dir . '/compatibility/kalium-wpbakery.php' ),
	'Kalium_LayerSlider'      => array( 'path' => $current_dir . '/compatibility/kalium-layerslider.php' ),
	'Kalium_ACF'              => array( 'path' => $current_dir . '/compatibility/kalium-acf.php' ),

	// Utility classes
	'Laborator_System_Status' => array( 'path' => $current_dir . '/utility/laborator-system-status.php' ),
) );

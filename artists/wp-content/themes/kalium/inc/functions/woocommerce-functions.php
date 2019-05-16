<?php
/**
 *	Kalium WordPress Theme
 *
 *	WooCommerce Functions
 *	
 *	Laborator.co
 *	www.laborator.co 
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 *  Check if shop is in catalog mode
 */
function kalium_woocommerce_is_catalog_mode() {
	return 1 == get_data( 'shop_catalog_mode' );
}

/**
 * Get layout type for loop products
 */
function kalium_woocommerce_get_catalog_layout() {
	
	// Shop columns
	$shop_catalog_layout = get_data( 'shop_catalog_layout' );
	
	if ( in_array( $shop_catalog_layout, array( 'full-bg', 'distanced-centered', 'transparent-bg' ) ) ) {
		return $shop_catalog_layout;
	}
	
	return 'default';
}

/**
 * Get shop sidebar position, if hidden "false" will be returned
 */
function kalium_woocommerce_get_sidebar_position() {
	$sidebar = get_data( 'shop_sidebar' );
	
	if ( in_array( $sidebar, array( 'left', 'right' ) ) ) {
		return $sidebar;
	}
	
	return false;
}

/**
 * Get shop sidebar position, if hidden "false" will be returned
 */
function kalium_woocommerce_single_product_get_sidebar_position() {
	$sidebar = get_data( 'shop_single_sidebar_position' );
	
	if ( in_array( $sidebar, array( 'left', 'right' ) ) ) {
		return $sidebar;
	}
	
	return false;
}

/**
 * Products per row in mobile devices
 */
function kalium_woocommerce_products_per_row_on_mobile() {
	$columns_mobile = get_data( 'shop_product_columns_mobile' );	
	return kalium_get_number_from_word( $columns_mobile );
}

/**
 *  Get category columns number
 */
function kalium_woocommerce_get_category_columns() {
	$category_columns = get_data( 'shop_category_columns' );
	$columns = kalium_get_number_from_word( $category_columns );
	return apply_filters( 'kalium_woocommerce_get_category_columns', $columns );
}

/**
 *  Support multi currency in AJAX mode for paged products page
 */
function kalium_wcml_multi_currency_ajax_actions( $actions ) {
	$actions[] = 'kalium_endless_pagination_get_paged_items';
	return $actions;
}

/**
 *  Use Kalium's default product gallery layout
 */
function kalium_woocommerce_use_custom_product_gallery_layout() {
	$not_supported_plugins = array(
		'woocommerce-additional-variation-images/woocommerce-additional-variation-images.php'
	);
	
	// Disable custom product gallery when certain plugins are activated (not supported)
	foreach ( $not_supported_plugins as $plugin_file ) {
		if ( kalium()->helpers->isPluginActive( $plugin_file ) ) {
			return false;
		}
	}
	
	return true;
}

/**
 *  Check if zoom is enabled
 */
function kalium_woocommerce_is_product_gallery_zoom_enabled() {
	return get_theme_support( 'wc-product-gallery-zoom' );
}

/**
 *  Check if gallery lightbox is enabled
 */
function kalium_woocommerce_is_product_gallery_lightbox_enabled() {
	return get_theme_support( 'wc-product-gallery-lightbox' );
}

/**
 * Get localized strings from Kalium theme used in WooCommerce template files
 */
function kalium_woocmmerce_get_i18n_str( $str_id, $echo = false ) {
	$found_string = 'kalium_woocmmerce_get_i18n_str::notFoundString';
	
	$strings = array(
		'(leave blank to leave unchanged)' => __( '(leave blank to leave unchanged)', 'kalium' ),
		'Current password' => __( 'Current password', 'kalium' ),
	);
	
	if ( isset( $strings[ $str_id ] ) ) {
		$found_string = $strings[ $str_id ];
	}
	
	if ( ! $echo ) {
		return $found_string;
	}
	
	echo $found_string;
}

/**
 *  Get product image size
 */
function kalium_woocommerce_get_product_image_size( $type = 'single' ) {
	// Larger product image
	$image_size = apply_filters( 'kalium_woocommerce_single_product_main_image_size', 'woocommerce_single' );
	
	// Thumbnail product image
	if ( 'gallery' == $type || 'gallery_thumbnail' == $type ) {
		$gallery_thumbnail = wc_get_image_size( 'gallery_thumbnail' );
		return apply_filters( 'kalium_woocommerce_single_product_gallery_image_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
	}
	
	return $image_size;
}

/**
 * Products masonry layout type
 */
function kalium_woocommerce_products_masonry_layout() {
	switch ( get_data( 'shop_loop_masonry_layout_mode' ) ) {
		case 'fitRows':
			return 'fitRows';
			break;
	}

	return 'masonry';
}
<?php
/**
 *    Kalium WordPress Theme
 *
 *    Other Functions
 *
 *    Laborator.co
 *    www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Homepage hashtags links fix
 */
function kalium_unique_hashtag_url_base_menu_item( $classes, $item ) {
	$url = $item->url;

	// Only hashtag links
	if ( false !== strpos( $url, '#' ) ) {
		$url_md5 = ( preg_replace( '/#.*/', '', $url ) );

		// Skip first item only
		if ( ! isset( $GLOBALS['kalium_hashtag_links'][ $url_md5 ] ) ) {
			$GLOBALS['kalium_hashtag_links'][ $url_md5 ] = true;

			return $classes;
		}

		$remove_classes = array(
			'current_page_item',
			'current-menu-item',
			'current-menu-ancestor',
			'current_page_ancestor',
		);

		foreach ( $remove_classes as $class_to_remove ) {
			$current_item_index = array_search( $class_to_remove, $classes );

			if ( false !== $current_item_index ) {
				unset( $classes[ $current_item_index ] );
			}
		}
	}

	return $classes;
}

/**
 * Homepage hashtags reset skipped item
 */
function kalium_unique_hashtag_url_base_reset( $args ) {
	$GLOBALS['kalium_hashtag_links'] = array();

	return $args;
}

/**
 * Post link plus mapper for WPML plugin
 *
 * @type filter
 */
function kalium_post_link_plus_result_mapper( $results ) {

	if ( kalium()->helpers->isPluginActive( 'sitepress-multilingual-cms/sitepress.php' ) ) {
		$results_new = array();

		foreach ( $results as $result ) {
			$results_new[] = get_post( apply_filters( 'wpml_object_id', $result->ID, $result->post_type, true ) );
		}

		return $results_new;
	}

	return $results;
}

/**
 * Warn users to install ACF5 Pro
 *
 * @type action
 */
function kalium_acf5_warning_init() {
	$is_using_acf4 = function_exists( 'acf' ) ? version_compare( acf()->version, '4.4.12', '<=' ) : false;
	$is_using_acf5_free = false === $is_using_acf4 && function_exists( 'acf' ) && false === defined( 'ACF_PRO' );

	if ( ( $is_using_acf4 || $is_using_acf5_free ) && 'kalium-install-plugins' !== kalium()->url->get( 'page' ) ) {
		$install_button = '<button type="button" class="button" id="kalium-acf5-pro-install-button"><i class="loading"></i> Deactivate ACF4 &amp; Install ACF5 Pro</button>';
		$acf_warning    = sprintf( 'You are currently using <strong>Advanced Custom Fields &ndash; %s</strong> which will not be supported in the upcoming updates of Kalium!<br><br>Please install and activate <strong>Advanced Custom Fields PRO</strong> plugin which is bundled with the theme <em>(free of charge)</em> either by installing from <a href="%s">Appearance &gt; Install Plugins</a> or clicking the button below which will deactivate previous version and install/activate ACF5 PRO automatically: <br><br>%s<br><br><em>Note: ACF4 and its addons will not be deleted (<a href="https://d.pr/i/RbEchZ" target="_blank">see here</a>), however we recommend you to delete them after installing ACF5 PRO.</em>', acf()->version, esc_url( admin_url( 'themes.php?page=kalium-install-plugins' ) ), $install_button );

		if ( $is_using_acf5_free ) {
			$install_button = '<button type="button" class="button" id="kalium-acf5-pro-install-button"><i class="loading"></i> Deactivate ACF5 (free) &amp; Install ACF5 Pro</button>';
			$acf_warning    = sprintf( 'You are currently using <strong>Advanced Custom Fields &ndash; %s (free version)</strong> which does not fully support Kalium!<br><br>Please install and activate <strong>Advanced Custom Fields PRO</strong> plugin which is bundled with the theme <em>(free of charge)</em> either by installing from <a href="%s">Appearance &gt; Install Plugins</a> or clicking the button below which will current ACF and install/activate ACF5 PRO automatically: <br><br>%s', acf()->version, esc_url( admin_url( 'themes.php?page=kalium-install-plugins' ) ), $install_button );
		}

		kalium()->helpers->addAdminNotice( $acf_warning, 'warning' );

		// Plugin disable and enable
		if ( kalium()->post( 'kalium_acf4_deactivate' ) && current_user_can( 'manage_options' ) ) {
			$acf4_plugin = 'advanced-custom-fields/acf.php';

			deactivate_plugins( array(
				$acf4_plugin,
				'acf-flexible-content/acf-flexible-content.php',
				'acf-gallery/acf-gallery.php',
				'acf-repeater/acf-repeater.php',
			) );
			die( did_action( "deactivate_{$acf4_plugin}" ) ? '1' : '-1' );
		}
	}

	// Activate ACF5 Pro
	if ( kalium()->post( 'kalium_acf5_activate' ) && current_user_can( 'manage_options' ) ) {
		$acf5_plugin = 'advanced-custom-fields-pro/acf.php';
		$all_plugins = apply_filters( 'all_plugins', get_plugins() );
		$success     = - 1;

		// Install and activate the plugin
		if ( ! isset( $all_plugins[ $acf5_plugin ] ) ) {

			if ( ! class_exists( 'Plugin_Upgrader', false ) ) {
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			}

			// Plugin file
			$download_url = TGM_Plugin_Activation::get_instance()->get_download_url( 'advanced-custom-fields-pro' );

			$skin_args = array(
				'type'   => 'upload',
				'title'  => "ACF Pro",
				'url'    => '',
				'nonce'  => 'install-plugin_advanced-custom-fields-pro',
				'plugin' => '',
				'api'    => '',
				'extra'  => array(),
			);

			$skin = new Plugin_Installer_Skin( $skin_args );

			// Create a new instance of Plugin_Upgrader.
			$upgrader = new Plugin_Upgrader( $skin );
			$upgrader->install( $download_url );
			$success = 1;

			// Update list of activated plugins
			$all_plugins = apply_filters( 'all_plugins', get_plugins() );
		}

		// Plugin exists, simply activate it
		if ( isset( $all_plugins[ $acf5_plugin ] ) ) {
			activate_plugins( $acf5_plugin );
			if ( did_action( 'activated_plugin' ) ) {
				$success = 1;
			}
		}

		die( (string) $success );
	}
}

add_action( 'admin_init', 'kalium_acf5_warning_init', 10 );

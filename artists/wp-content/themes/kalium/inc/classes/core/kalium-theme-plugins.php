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

class Kalium_Theme_Plugins {

	/**
	 * Plugins data
	 */
	private $plugins_data;

	/**
	 * Constructor
	 */
	public function __construct() {
		// Register TGMPA Plugins
		add_action( 'tgmpa_register', array( $this, 'registerTGMPAPlugins' ) );
	}

	/**
	 * Retrieve plugin information
	 */
	private function retrievePluginData() {
		// Transient life
		$transient_time = DAY_IN_SECONDS; // Check for plugin updates every day

		// Load plugins from transient
		$plugins_data = get_site_transient( 'kalium_theme_plugins_data' );

		// Fetch newer list
		if ( 'remote' == kalium()->url->get( 'refresh-plugins' ) && current_user_can( 'update_plugins' ) && ! get_site_transient( 'kalium_plugins_refresh_remote' ) ) {
			delete_site_transient( 'kalium_theme_plugins_data' );
			set_site_transient( 'kalium_plugins_refresh_remote', true, 300 );
			wp_redirect( remove_query_arg( 'refresh-plugins' ) );
			die();
		}

		// Fetch data when user is on TGMPA page
		if ( 'kalium-install-plugins' == $this->admin_page && false === get_site_transient( 'kalium_plugins_refreshed' ) ) {
			$plugins_data = false;
			set_site_transient( 'kalium_plugins_refreshed', true, HOUR_IN_SECONDS * 3 );
		}
		// Bundled plugins page
		else if ( 'kalium-install-plugins' == $this->admin_page && current_user_can( 'update_plugins' ) ) {
			$plugins_data = false;
		}

		// Fetch data
		if ( false === $plugins_data ) {
			// Get latest theme version
			$response = wp_remote_post( Kalium_Theme_License::getAPIServerURL(), array(
				'body' => array(
					'plugin_data'     => 'kalium',
					'current_version' => kalium()->getVersion(),
					'license_key'     => kalium()->theme_license->getLicenseKey(),
				),
			) );

			$response_body = wp_remote_retrieve_body( $response );
			$plugins_data  = json_decode( $response_body );

			set_site_transient( 'kalium_theme_plugins_data', $plugins_data, $transient_time );
		}

		// Set plugin slug as array key
		$plugins_data_associative = array();

		if ( is_array( $plugins_data ) ) {
			foreach ( $plugins_data as $plugin ) {
				$plugins_data_associative[ $plugin->slug ] = (array) $plugin;
			}
		}

		$this->plugins_data = $plugins_data_associative;
	}

	/**
	 * Get list of plugins that are required or recommended for the theme
	 */
	public function getPluginsList() {
		$plugins = array();

		// Advanced Custom Fields Pro
		$plugins[] = $this->preparePluginEntry( 'Advanced Custom Fields PRO', 'advanced-custom-fields-pro' );

		// Portfolio Post Type
		$plugins[] = $this->preparePluginEntry( 'Portfolio Post Type', 'portfolio-post-type' );

		// WPBakery Page Builder
		$plugins[] = $this->preparePluginEntry( 'WPBakery Page Builder', 'js_composer' );

		// WooCommerce
		$plugins[] = $this->preparePluginEntry( 'WooCommerce', 'woocommerce' );

		// Revolution Slider
		$plugins[] = $this->preparePluginEntry( 'Slider Revolution', 'revslider' );

		// Layer Slider
		$plugins[] = $this->preparePluginEntry( 'LayerSlider WP', 'LayerSlider' );

		// Product filter and Size guide plugin
		if ( is_shop_supported() ) {
			// WooCommerce Product Filter
			$plugins[] = $this->preparePluginEntry( 'WooCommerce Product Filter', 'prdctfltr' );

			// WooCommerce Product Size Guide
			$plugins[] = $this->preparePluginEntry( 'WooCommerce Product Size Guide', 'ct-size-guide' );
		}

		return $plugins;
	}

	/**
	 * Third party plugins for Kalium
	 */
	public function registerTGMPAPlugins() {
		// Retrieve plugins data
		$this->retrievePluginData();

		// Plugins list
		$plugins = $this->getPluginsList();

		$config = array(
			'id'           => 'kalium',
			'default_path' => '',
			'menu'         => 'kalium-install-plugins',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => false,
			'message'      => $this->getTGMPANotice(),
		);

		tgmpa( $plugins, $config );

		// Set plugin source for bundled plugins only
		add_filter( 'upgrader_pre_download', array( $this, 'setSourceForBundledPlugins' ), 1000, 3 );
	}

	/**
	 * Prepare TGMPA plugin entry
	 */
	public function preparePluginEntry( $plugin_name, $plugin_slug, $required = false ) {
		$plugin = array(
			'native_name' => $plugin_name,
			'name'        => $plugin_name,
			'slug'        => $plugin_slug,
			'required'    => $required,
		);

		// Retrieve plugin data
		if ( isset( $this->plugins_data[ $plugin_slug ] ) ) {
			$plugin_data = $this->plugins_data[ $plugin_slug ];
			$plugin      = array_merge( $plugin, $plugin_data );
		}

		return $plugin;
	}

	/**
	 * Get TGMPA notices
	 */
	public function getTGMPANotice() {
		/*$refresh = sprintf( '<br><br>If you are not seeing latest version of any of our premium bundled plugins, then click <a href="%s">here</a> to fetch latest available plugin versions.', esc_url( add_query_arg( 'refresh-plugins', 'remote' ) ) );
		if ( current_user_can( 'update_plugins' ) && get_site_transient( 'kalium_plugins_refresh_remote' ) ) {
			$refresh = '';
		}*/
		$refresh = '';
		$message = '<p>All premium plugins bundled with Kalium include free updates, the list of plugins below is automatically updated upon release of new plugin versions. ' . $refresh . '</p>';

		return $message;
	}

	/**
	 * Set source for bundled plugins when installing or updating them
	 */
	public function setSourceForBundledPlugins( $return, $package, $upgrader ) {
		global $pagenow;

		$skin = $upgrader->skin;

		// Make sure it is a plugin
		if ( $skin instanceof Plugin_Upgrader_Skin ) {
			$plugin_slug = dirname( $skin->plugin );

			if ( isset( $this->plugins_data[ $plugin_slug ] ) ) {
				$plugin = $this->plugins_data[ $plugin_slug ];

				if ( ! empty( $plugin['source'] ) ) {
					$source = $plugin['source'];
					$skin->feedback( 'downloading_package', $source );

					$download_file = download_url( $source );

					if ( is_wp_error( $download_file ) ) {
						return new WP_Error( 'download_failed', $package->strings['download_failed'], $download_file->get_error_message() );
					}

					return $download_file;
				}
			}
		} // Update page
		else if ( 'update.php' == $pagenow ) {

			if ( $skin instanceof Bulk_Plugin_Upgrader_Skin && ! empty( $skin->plugin_info ) ) {
				$plugin_info = $skin->plugin_info;

				// Current updating plugin meta
				$name = $plugin_info['Name'];

				// Check for matching bundled plugin
				foreach ( $this->getPluginsList() as $plugin ) {

					// Matched bundled plugin
					if ( ! empty( $plugin['source'] ) && $name == $plugin['native_name'] ) {
						$version = $this->getLatestVersionForPlugin( $plugin['slug'] );

						// Only if its the same version or older
						if ( version_compare( $version, $plugin['version'], '<=' ) ) {
							$source = $plugin['source'];
							$skin->feedback( 'downloading_package', $source );

							$download_file = download_url( $source );

							return $download_file;
						}
					}
				}
			}
		}

		return $return;
	}

	/**
	 * Get latest version for a plugin
	 */
	public function getLatestVersionForPlugin( $plugin_slug ) {
		$plugin_updates = get_plugin_updates();

		foreach ( $plugin_updates as $plugin_file => $plugin ) {
			$_plugin_slug = dirname( $plugin_file );

			if ( $plugin_slug == $_plugin_slug && isset( $plugin->update ) ) {
				return $plugin->update->new_version;
			}
		}

		return '';
	}
}

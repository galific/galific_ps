<?php
/*
Plugin Name: WooCommerce Product Filter
Plugin URI: https://www.mihajlovicnenad.com/product-filter
Description: Advanced product filter for any Wordpress template! - Mihajlovicnenad.com
Author: Mihajlovic Nenad
Version: 6.6.5
Requires at least: 4.5
Tested up to: 5.0.3
WC requires at least: 3.0.0
WC tested up to: 3.5.4
Author URI: https://www.mihajlovicnenad.com
Text Domain: prdctfltr
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( !class_exists( 'PrdctfltrInit' ) ) :

	final class PrdctfltrInit {

		public static $version = '6.6.5';

		protected static $_instance = null;

		public static function instance() {

			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}

		public function __construct() {
			do_action( 'prdctfltr_loading' );

			$this->includes();

			$this->init_hooks();

			do_action( 'prdctfltr_loaded' );
		}

		private function init_hooks() {
			register_activation_hook( __FILE__, array( $this, 'activate' ) );
			add_action( 'init', array( $this, 'check_version' ), 10 );
			add_action( 'init', array( $this, 'init' ), 0 );
		}

		private function is_request( $type ) {
			switch ( $type ) {
				case 'admin' :
					return is_admin();
				case 'ajax' :
					return defined( 'DOING_AJAX' );
				case 'cron' :
					return defined( 'DOING_CRON' );
				case 'frontend' :
					return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
			}
		}

		public function includes() {

			include_once( 'lib/pf-characteristics.php' );
			include_once( 'lib/pf-widget.php' );
			include_once( 'lib/pf-fixoptions.php' );

			if ( $this->is_request( 'admin' ) ) {

				add_action( 'vc_before_init', array( $this, 'composer' ) );
				add_action( 'enqueue_block_editor_assets', array( $this, 'gutenberg' ) );
				include_once ( 'lib/pf-settings.php' );

			}

			if ( $this->is_request( 'frontend' ) ) {
				$this->frontend_includes();
			}

		}

		public function frontend_includes() {
			include_once( 'lib/pf-frontend.php' );
			include_once( 'lib/pf-shortcode.php' );
		}

		public function include_template_functions() {

		}

		public function init() {

			do_action( 'before_prdctfltr_init' );

			$this->load_plugin_textdomain();

			do_action( 'after_prdctfltr_init' );

		}

		public function load_plugin_textdomain() {

			$domain = 'prdctfltr';
			$dir = untrailingslashit( WP_LANG_DIR );
			$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

			if ( $loaded = load_textdomain( $domain, $dir . '/plugins/' . $domain . '-' . $locale . '.mo' ) ) {
				return $loaded;
			}
			else {
				load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
			}

		}

		public function setup_environment() {

		}

		public function plugin_url() {
			return untrailingslashit( plugins_url( '/', __FILE__ ) );
		}

		public function template_path() {
			return apply_filters( 'prdctfltr_template_path', '/templates/' );
		}

		public function plugin_path() {
			return untrailingslashit( plugin_dir_path( __FILE__ ) );
		}

		public function plugin_basename() {
			return untrailingslashit( plugin_basename( __FILE__ ) );
		}

		public function ajax_url() {
			return admin_url( 'admin-ajax.php', 'relative' );
		}

		public function version() {
			return self::$version;
		}

		public function gutenberg() {

			$prdctfltr = array();
			$presets = array(
				array(
					'label' => __( 'Default', 'prdctfltr' ),
					'value' => ''
				)
			);

			$saved_presets = get_option( 'prdctfltr_templates', array() );

			if ( is_array( $saved_presets ) ) {
				foreach ( $saved_presets as $k => $v ) {
					$presets[] = array(
						'label' => $k,
						'value' => $k
					);
				}
			}
			$prdctfltr['presets'] = $presets;

			wp_enqueue_script(
				'gutenberg-for-product-filter',
				plugins_url( '/lib/js/gutenberg.js', __FILE__ ),
				array( 'wp-blocks', 'wp-i18n', 'wp-element' ),
				'1.0.0'
			);
			wp_localize_script( 'gutenberg-for-product-filter', 'prdctfltr', $prdctfltr );

		}

		public function composer() {
			require_once( 'lib/pf-composer.php' );
		}

		function check_version() {

			$version = get_option( 'wc_settings_prdctfltr_version', false );

			if ( $version === false ) {
				$check = get_option( 'wc_settings_prdctfltr_always_visible', false );
				if ( $check === false ) {
					update_option( 'wc_settings_prdctfltr_version', self::$version, 'yes' );
					return '';
				}
				else {
					$version = get_option( 'wc_settings_prdctfltr_version', '5.8.1' );
				}
			}

			if ( version_compare( '5.8.2', $version, '>' ) ) {
				add_action( 'admin_init', array( &$this, 'fix_database_582' ), 100 );
			}

			if ( version_compare( '6.0.6', $version, '>' ) ) {
				add_action( 'init', array( &$this, 'fix_database_606' ), 100 );
			}

			if ( version_compare( '6.5.3', $version, '>' ) ) {
				add_action( 'init', array( &$this, 'fix_database_653' ), 100 );
			}

		}

		public static function version_check( $version = '3.0.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if( version_compare( $woocommerce->version, $version, ">=" ) ) {
					return true;
				}
			}
			return false;
		}

		function fix_database_653() {
			$check = get_option( 'wc_settings_prdctfltr_always_visible', false );
			if ( $check !== false ) {
				$check2 = get_option( 'prdctfltr_wc_default', false );
				if ( $check2 !== false ) {
					global $wpdb;
					$fixThis = $wpdb->get_results( "SELECT `option_name` FROM `$wpdb->options` WHERE `option_name` LIKE CONVERT( _utf8 'wc_settings_prdctfltr_%'USING utf8mb4 ) COLLATE utf8mb4_unicode_ci AND `autoload` = 'yes' LIMIT 99999" );
					include_once( 'lib/pf-options-autoload.php' );
					$inHere = array_merge( $autoload_std, $forbidden_std, $deprecated_653 );
					foreach( $fixThis as $fix ) {
						if ( !in_array( $fix->option_name, $inHere ) ) {
							delete_option( $fix->option_name );
						}
					}
					if ( !empty( $deprecated_653 ) ) {
						foreach( $deprecated_653 as $deprecate ) {
							delete_option( $deprecate );
						}
					}
					update_option( 'wc_settings_prdctfltr_version', '6.5.4', 'yes' );
				}
			}
		}

		function fix_database_606() {
			global $wpdb;

			$default = $wpdb->get_results( "SELECT `option_name`, `option_value` FROM `$wpdb->options` WHERE `option_name` LIKE CONVERT( _utf8 'wc_settings_prdctfltr_%'USING utf8mb4 ) COLLATE utf8mb4_unicode_ci LIMIT 99999" );

			if ( !empty( $default ) ) {
				$fix_default = array();
				include_once( 'lib/pf-options-autoload.php' );

				foreach( $default as $k => $v ) {
					if ( in_array( $v->option_name, $forbidden_std ) ) {
						$wpdb->query( "update $wpdb->options set autoload='yes' where option_name = '$v->option_name';" );
					}
					else if ( in_array( $v->option_name, $dont_autoload_std ) || substr( $v->option_name, 0, 41 ) == 'wc_settings_prdctfltr_term_customization_' || substr( $v->option_name, 0, 43 ) == 'wc_settings_prdctfltr_filter_customization_' ) {
						$wpdb->query( "update $wpdb->options set autoload='no' where option_name = '$v->option_name';" );
					}
					else if ( in_array( $v->option_name, $autoload_std ) ) {
						$wpdb->query( "update $wpdb->options set autoload='yes' where option_name = '$v->option_name';" );
					}
					else if ( strpos( $v->option_name, 'transient' ) ) {
						delete_option( $v->option_name );
					}
					else {
						$fix_default[$v->option_name] = get_option( $v->option_name );
						$wpdb->query( "update $wpdb->options set autoload='no' where option_name = '$v->option_name';" );
					}
				}

				if ( !empty( $fix_default ) ) {
					$fix_default = json_encode( $fix_default );
					update_option( 'prdctfltr_wc_default', $fix_default, 'no' );
				}

				$templates = get_option( 'prdctfltr_templates', array() );
				if ( !empty( $templates ) && is_array( $templates ) ) {
					update_option( 'prdctfltr_backup_templates', $templates, 'no' );
					foreach( $templates as $k1 => $v1 ) {
						if ( !empty( $v1 ) && substr( $v1, 0, 1 ) == '{' ) {
							update_option( 'prdctfltr_wc_template_' . sanitize_title( $k1 ), $v1, 'no' );
							$templates[$k1] = array();
						}
					}
				}
				update_option( 'prdctfltr_templates', $templates, 'no' );
			}
			update_option( 'wc_settings_prdctfltr_version', self::$version, 'yes' );

		}

		function fix_database_582() {

			global $wpdb;

			$wpdb->query( "update $wpdb->options set autoload='yes' where option_name like '%prdctfltr%';" );
			$wpdb->query( "delete from $wpdb->options where option_name like '_transient_prdctfltr_%';" );
			$wpdb->query( "delete from $wpdb->options where option_name like '_transient_%_prdctfltr_%';" );
			$wpdb->query( "delete from $wpdb->options where option_name like 'wc_settings_prdctfltr_%_end';" );
			$wpdb->query( "delete from $wpdb->options where option_name like 'wc_settings_prdctfltr_%_title' and option_value = '' ;" );
			delete_option( 'wc_settings_prdctfltr_force_categories' );
			delete_option( 'wc_settings_prdctfltr_force_emptyshop' );
			delete_option( 'wc_settings_prdctfltr_force_search' );
			delete_option( 'wc_settings_prdctfltr_caching' );
			delete_option( 'wc_settings_prdctfltr_selected' );
			delete_option( 'wc_settings_prdctfltr_attributes' );
			update_option( 'wc_settings_prdctfltr_version', '6.0.5', 'yes' );

		}

		function activate() {

			if ( false !== get_transient( 'prdctfltr_default' ) ) {
				delete_transient( 'prdctfltr_default' );
			}

			$active_presets = get_option( 'prdctfltr_templates', array() );

			if ( !empty( $active_presets ) && is_array( $active_presets ) ) {
				foreach( $active_presets as $k => $v ) {
					if ( false !== ( $transient = get_transient( 'prdctfltr_' . $k ) ) ) {
						delete_transient( 'prdctfltr_' . $k );
					}
				}
			}

		}

	}

	function Prdctfltr() {
		return PrdctfltrInit::instance();
	}

	PrdctfltrInit::instance();

endif;


?>
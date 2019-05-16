<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fredericgilles.net/fg-prestashop-to-woocommerce/
 * @since      2.0.0
 *
 * @package    FG_PrestaShop_to_WooCommerce
 * @subpackage FG_PrestaShop_to_WooCommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    FG_PrestaShop_to_WooCommerce
 * @subpackage FG_PrestaShop_to_WooCommerce/includes
 * @author     Frédéric GILLES
 */
class FG_PrestaShop_to_WooCommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      FG_PrestaShop_to_WooCommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		if ( defined( 'FGP2WC_PLUGIN_VERSION' ) ) {
			$this->version = FGP2WC_PLUGIN_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'fg-prestashop-to-woocommerce';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - FG_PrestaShop_to_WooCommerce_Loader. Orchestrates the hooks of the plugin.
	 * - FG_PrestaShop_to_WooCommerce_i18n. Defines internationalization functionality.
	 * - FG_PrestaShop_to_WooCommerce_Admin. Defines all hooks for the admin area.
	 * - FG_PrestaShop_to_WooCommerce_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-prestashop-to-woocommerce-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-prestashop-to-woocommerce-i18n.php';

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';
		if ( !class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require_once $class_wp_importer;
			}
		}

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-prestashop-to-woocommerce-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-prestashop-to-woocommerce-tools.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-prestashop-to-woocommerce-compatibility.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-prestashop-to-woocommerce-modules-check.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-prestashop-to-woocommerce-progressbar.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */

		$this->loader = new FG_PrestaShop_to_WooCommerce_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the FG_PrestaShop_to_WooCommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new FG_PrestaShop_to_WooCommerce_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		// Add links to the plugin page
		$this->loader->add_filter( 'plugin_action_links_fg-prestashop-to-woocommerce/fg-prestashop-to-woocommerce.php', $this, 'plugin_action_links' );
		
		/**
		 * The plugin is hooked to the WordPress importer
		 */
		if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') ) {
			return;
		}

		$plugin_admin = new FG_PrestaShop_to_WooCommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_init', $plugin_admin, 'init' );
		$this->loader->add_action( 'fgp2wc_post_test_database_connection', $plugin_admin, 'get_prestashop_info', 9 );
		$this->loader->add_action( 'fgp2wc_post_empty_database', $plugin_admin, 'delete_woocommerce_data' );
		$this->loader->add_action( 'load-importer-fgp2wc', $plugin_admin, 'add_help_tab', 20 );
		$this->loader->add_action( 'fgp2wc_import_notices', $plugin_admin, 'display_media_count', 10 );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'display_notices', 20 );
		$this->loader->add_action( 'wp_ajax_fgp2wc_import', $plugin_admin, 'ajax_importer' );
		$this->loader->add_filter( 'fgp2wc_pre_import_check', $plugin_admin, 'pre_import_check', 10, 1 );
		
		/*
		 * Modules checker
		 */
		$plugin_modules_check = new FG_PrestaShop_to_WooCommerce_Modules_Check( $plugin_admin );
		$this->loader->add_action( 'fgp2wc_post_test_database_connection', $plugin_modules_check, 'check_modules' );
		
	}

	/**
	 * Customize the links on the plugins list page
	 *
	 * @param array $links Links
	 * @return array Links
	 */
	public function plugin_action_links($links) {
		// Add the import link
		$import_link = '<a href="admin.php?import=fgp2wc">'. __('Import', $this->plugin_name) . '</a>';
		array_unshift($links, $import_link);
		return $links;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    FG_PrestaShop_to_WooCommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}

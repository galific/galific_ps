<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://wordpress.org/plugins/fg-prestashop-to-woocommerce/
 * @since      2.0.0
 *
 * @package    FG_PrestaShop_to_WooCommerce
 * @subpackage FG_PrestaShop_to_WooCommerce/admin
 */

if ( !class_exists('FG_PrestaShop_to_WooCommerce_Admin', false) ) {

	/**
	 * The admin-specific functionality of the plugin.
	 *
	 * @package    FG_PrestaShop_to_WooCommerce
	 * @subpackage FG_PrestaShop_to_WooCommerce/admin
	 * @author     Frédéric GILLES
	 */
	class FG_PrestaShop_to_WooCommerce_Admin extends WP_Importer {

		const IMPORT_TIMEOUT = 7200; // Timeout = 2 hours

		/**
		 * The ID of this plugin.
		 *
		 * @since    2.0.0
		 * @access   private
		 * @var      string    $plugin_name    The ID of this plugin.
		 */
		private $plugin_name;

		/**
		 * The version of this plugin.
		 *
		 * @since    2.0.0
		 * @access   private
		 * @var      string    $version    The current version of this plugin.
		 */
		private $version;

		public $plugin_options;						// Plug-in options
		public $progressbar;
		public $default_language = 1;				// Default language ID
		public $current_language = 1;				// Current language ID
		public $prestashop_version = '';			// PrestaShop DB version
		public $default_country = 0;				// Default country
		public $global_tax_rate = 0;
		public $chunks_size = 10;
		public $product_types = array();			// WooCommerce product types
		public $product_visibilities = array();		// WooCommerce product visibilities
		public $media_count = 0;					// Number of imported medias
		public $imported_products = array();		// Imported products
		public $imported_manufacturers = array();	// Imported manufacturers
		public $imported_categories = array();		// Imported product categories
		public $imported_cms_categories = array();	// Imported categories

		protected $faq_url;							// URL of the FAQ page
		
		private $post_type = 'post';				// post or page
		private $image_filename_key = false;		// Optimization to get the right image filename
		private $default_backorders = 'no';			// Allow backorders
		private $imported_tags = array();			// Imported tags
		private $notices = array();					// Error or success messages
		private $log_file;
		private $log_file_url;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    2.0.0
		 * @param    string    $plugin_name       The name of this plugin.
		 * @param    string    $version           The version of this plugin.
		 */
		public function __construct( $plugin_name, $version ) {

			$this->plugin_name = $plugin_name;
			$this->version = $version;
			$this->faq_url = 'https://wordpress.org/plugins/fg-prestashop-to-woocommerce/faq/';
			
			// Logger
			$upload_dir = wp_upload_dir();
			$this->log_file = $upload_dir['basedir'] . '/' . $this->plugin_name . '.log';
			$this->log_file_url = $upload_dir['baseurl'] . '/' . $this->plugin_name . '.log';

			// Progress bar
			$this->progressbar = new FG_PrestaShop_to_WooCommerce_ProgressBar($this);
			
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
		 * Register the stylesheets for the admin area.
		 *
		 * @since    2.0.0
		 */
		public function enqueue_styles() {

			wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/fg-prestashop-to-woocommerce-admin.css', array(), $this->version, 'all' );

		}

		/**
		 * Register the JavaScript for the admin area.
		 *
		 * @since    2.0.0
		 */
		public function enqueue_scripts() {

			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/fg-prestashop-to-woocommerce-admin.js', array( 'jquery', 'jquery-ui-progressbar' ), $this->version, false );
			wp_localize_script( $this->plugin_name, 'objectL10n', array(
				'delete_imported_data_confirmation_message' => __( 'All previously imported data will be deleted from WordPress..', 'fg-prestashop-to-woocommerce' ),
				'delete_all_confirmation_message' => __( 'All content will be deleted from WordPress.', 'fg-prestashop-to-woocommerce' ),
				'delete_no_answer_message' => __( 'Please select a remove option.', 'fg-prestashop-to-woocommerce' ),
				'import_completed' => __( 'IMPORT COMPLETED', 'fg-prestashop-to-woocommerce' ),
				'content_removed_from_wordpress' => __( 'Content removed from WordPress', 'fg-prestashop-to-woocommerce' ),
				'settings_saved' => __( 'Settings saved', 'fg-prestashop-to-woocommerce' ),
				'importing' => __( 'Importing…', 'fg-prestashop-to-woocommerce' ),
				'import_stopped_by_user' => __( 'IMPORT STOPPED BY USER', 'fg-prestashop-to-woocommerce' ),
			) );
			wp_localize_script( $this->plugin_name, 'objectPlugin', array(
				'log_file_url' => $this->log_file_url,
				'progress_url' => $this->progressbar->get_url(),
			));
			
		}

		/**
		 * Initialize the plugin
		 */
		public function init() {
			register_importer('fgp2wc', __('PrestaShop', 'fg-prestashop-to-woocommerce'), __('Import PrestaShop e-commerce solution to WooCommerce', 'fg-prestashop-to-woocommerce'), array($this, 'importer'));
		}

		/**
		 * Display the stored notices
		 */
		public function display_notices() {
			foreach ( $this->notices as $notice ) {
				echo '<div class="' . $notice['level'] . '"><p>[' . $this->plugin_name . '] ' . $notice['message'] . "</p></div>\n";
			}
		}
		
		/**
		 * Write a message in the log file
		 * 
		 * @since 3.0.0
		 * 
		 * @param string $message
		 */
		public function log($message) {
			file_put_contents($this->log_file, "$message\n", FILE_APPEND);
		}
		
		/**
		 * Store an admin notice
		 */
		public function display_admin_notice( $message )	{
			$this->notices[] = array('level' => 'updated', 'message' => $message);
			error_log('[INFO] [' . $this->plugin_name . '] ' . $message);
			$this->log($message);
		}

		/**
		 * Store an admin error
		 */
		public function display_admin_error( $message )	{
			$this->notices[] = array('level' => 'error', 'message' => $message);
			error_log('[ERROR] [' . $this->plugin_name . '] ' . $message);
			$this->log('[ERROR] ' . $message);
		}

		/**
		 * Store an admin warning
		 * 
		 * @since 3.0.0
		 */
		public function display_admin_warning( $message )	{
			$this->notices[] = array('level' => 'error', 'message' => $message);
			error_log('[WARNING] [' . $this->plugin_name . '] ' . $message);
			$this->log('[WARNING] ' . $message);
		}

		/**
		 * Run the importer
		 */
		public function importer() {
			$feasible_actions = array(
				'empty',
				'save',
				'test_database',
				'test_ftp',
				'import',
			);
			$action = '';
			foreach ( $feasible_actions as $potential_action ) {
				if ( isset($_POST[$potential_action]) ) {
					$action = $potential_action;
					break;
				}
			}
			$this->dispatch($action);
			$this->display_admin_page(); // Display the admin page
		}
		
		/**
		 * Import triggered by AJAX
		 *
		 * @since    3.0.0
		 */
		public function ajax_importer() {
			$current_user = wp_get_current_user();
			if ( !empty($current_user) && $current_user->has_cap('import') ) {
				$action = filter_input(INPUT_POST, 'plugin_action', FILTER_SANITIZE_STRING);

				if ( $action == 'update_wordpress_info') {
					// Update the WordPress database info
					echo $this->get_database_info();

				} else {
					ini_set('display_errors', true); // Display the errors that may happen (ex: Allowed memory size exhausted)

					// Empty the log file if we empty the WordPress content
					if ( ($action == 'empty') || (($action == 'import') && filter_input(INPUT_POST, 'automatic_empty', FILTER_VALIDATE_BOOLEAN)) ) {
						file_put_contents($this->log_file, '');
					}

					$time_start = date('Y-m-d H:i:s');
					$this->display_admin_notice("=== START $action $time_start ===");
					$result = $this->dispatch($action);
					if ( !empty($result) ) {
						echo json_encode($result); // Send the result to the AJAX caller
					}
					$time_end = date('Y-m-d H:i:s');
					$this->display_admin_notice("=== END $action $time_end ===\n");
				}
			}
			wp_die();
		}
		
		/**
		 * Dispatch the actions
		 * 
		 * @param string $action Action
		 * @return object Result to return to the caller
		 */
		public function dispatch($action) {
			set_time_limit(self::IMPORT_TIMEOUT);
			
			// Suspend the cache during the migration to avoid exhausted memory problem
			wp_suspend_cache_addition(true);
			wp_suspend_cache_invalidation(true);
			
			// Default values
			$this->plugin_options = array(
				'automatic_empty'				=> false,
				'url'							=> null,
				'hostname'						=> 'localhost',
				'port'							=> 3306,
				'database'						=> null,
				'username'						=> 'root',
				'password'						=> '',
				'prefix'						=> 'ps_',
				'sku'							=> 'reference',
				'skip_media'					=> false,
				'first_image'					=> 'as_is_and_featured',
				'image_size'					=> 'thumbnail',
				'import_external'				=> false,
				'import_duplicates'				=> false,
				'force_media_import'			=> false,
				'stock_management'				=> true,
				'meta_keywords_in_tags'			=> false,
				'import_as_pages'				=> false,
				'timeout'						=> 5,
				'price'							=> 'without_tax',
				'first_image_not_in_gallery'	=> false,
				'logger_autorefresh'			=> true,
			);
			$options = get_option('fgp2wc_options');
			if ( is_array($options) ) {
				$this->plugin_options = array_merge($this->plugin_options, $options);
			}
			do_action('fgp2wc_post_get_plugin_options');
			
			// Check if the upload directory is writable
			$upload_dir = wp_upload_dir();
			if ( !is_writable($upload_dir['basedir']) ) {
				$this->display_admin_error(__('The wp-content directory must be writable.', 'fg-prestashop-to-woocommerce'));
			}
			
			// Requires at least WordPress 4.4
			if ( version_compare(get_bloginfo('version'), '4.4', '<') ) {
				$this->display_admin_error(sprintf(__('WordPress 4.4+ is required. Please <a href="%s">update WordPress</a>.', 'fg-prestashop-to-woocommerce'), admin_url('update-core.php')));
			}
			
			elseif ( !empty($action) ) {
				switch($action) {
					
					// Delete content
					case 'empty':
						if ( check_admin_referer( 'empty', 'fgp2wc_nonce' ) ) { // Security check
							if ($this->empty_database($_POST['empty_action'])) { // Empty WP database
								$this->display_admin_notice(__('WordPress content removed', 'fg-prestashop-to-woocommerce'));
							} else {
								$this->display_admin_error(__('Couldn\'t remove content', 'fg-prestashop-to-woocommerce'));
							}
							wp_cache_flush();
						}
						break;
					
					// Save database options
					case 'save':
						if ( check_admin_referer( 'parameters_form', 'fgp2wc_nonce' ) ) { // Security check
							$this->save_plugin_options();
							$this->display_admin_notice(__('Settings saved', 'fg-prestashop-to-woocommerce'));
						}
						break;
					
					// Test the database connection
					case 'test_database':
						if ( check_admin_referer( 'parameters_form', 'fgp2wc_nonce' ) ) { // Security check
							// Save database options
							$this->save_plugin_options();

							if ( $this->test_database_connection() ) {
								return array('status' => 'OK', 'message' => __('Connection successful', 'fg-prestashop-to-woocommerce'));
							} else {
								return array('status' => 'Error', 'message' => __('Connection failed', 'fg-prestashop-to-woocommerce') . '<br />' . __('See the errors in the log below', 'fg-prestashop-to-woocommerce'));
							}
						}
						break;
					
					// Run the import
					case 'import':
						if ( defined('DOING_CRON') || check_admin_referer( 'parameters_form', 'fgp2wc_nonce') ) { // Security check
							// Save database options
							if ( !defined('DOING_CRON') ) {
								$this->save_plugin_options();
							} else {
								// CRON triggered
								$this->plugin_options['automatic_empty'] = 0; // Don't delete the existing data when triggered by cron
							}

							if ( $this->test_database_connection() ) {
								// Automatic empty
								if ( $this->plugin_options['automatic_empty'] ) {
									if ($this->empty_database('all')) {
										$this->display_admin_notice(__('WordPress content removed', 'fg-prestashop-to-woocommerce'));
									} else {
										$this->display_admin_error(__('Couldn\'t remove content', 'fg-prestashop-to-woocommerce'));
									}
									wp_cache_flush();
								}

								// Import content
								$this->import();
							}
						}
						break;
					
					// Stop the import
					case 'stop_import':
						if ( check_admin_referer( 'parameters_form', 'fgp2wc_nonce' ) ) { // Security check
							$this->stop_import();
						}
						break;
					
					default:
						// Do other actions
						do_action('fgp2wc_dispatch', $action);
				}
			}
		}
		
		/**
		 * Build the option page
		 * 
		 */
		private function display_admin_page() {
			$data = $this->plugin_options;
			
			$data['title'] = __('Import PrestaShop', 'fg-prestashop-to-woocommerce');
			$data['description'] = __('This plugin will import products, categories, tags, images and CMS from PrestaShop to WooCommerce/WordPress.<br />Compatible with PrestaShop versions 1.1 to 1.7.', 'fg-prestashop-to-woocommerce');
			$data['description'] .= "<br />\n" . sprintf(__('For any issue, please read the <a href="%s" target="_blank">FAQ</a> first.', 'fg-prestashop-to-woocommerce'), $this->faq_url);
			$data['database_info'] = $this->get_database_info();
			
			// Hook for modifying the admin page
			$data = apply_filters('fgp2wc_pre_display_admin_page', $data);
			
			// Load the CSS and Javascript
			$this->enqueue_styles();
			$this->enqueue_scripts();
			
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/admin-display.php';
			
			// Hook for doing other actions after displaying the admin page
			do_action('fgp2wc_post_display_admin_page');
			
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @since 3.0.0
		 * 
		 * @return string Database info
		 */
		private function get_database_info() {
			$posts_count = $this->count_posts('post');
			$pages_count = $this->count_posts('page');
			$media_count = $this->count_posts('attachment');
			$products_count = $this->count_posts('product');
			$cat_count = wp_count_terms('category', array('hide_empty' => 0));
			$product_cat_count = wp_count_terms('product_cat', array('hide_empty' => 0));
			if ( is_wp_error($product_cat_count) ) {
				$product_cat_count = 0;
			}
			$tags_count = wp_count_terms('post_tag', array('hide_empty' => 0));

			$database_info =
				sprintf(_n('%d category', '%d categories', $cat_count, 'fg-prestashop-to-woocommerce'), $cat_count) . "<br />" .
				sprintf(_n('%d post', '%d posts', $posts_count, 'fg-prestashop-to-woocommerce'), $posts_count) . "<br />" .
				sprintf(_n('%d page', '%d pages', $pages_count, 'fg-prestashop-to-woocommerce'), $pages_count) . "<br />" .
				sprintf(_n('%d product category', '%d product categories', $product_cat_count, 'fg-prestashop-to-woocommerce'), $product_cat_count) . "<br />" .
				sprintf(_n('%d product', '%d products', $products_count, 'fg-prestashop-to-woocommerce'), $products_count) . "<br />" .
				sprintf(_n('%d media', '%d medias', $media_count, 'fg-prestashop-to-woocommerce'), $media_count) . "<br />" .
				sprintf(_n('%d tag', '%d tags', $tags_count, 'fg-prestashop-to-woocommerce'), $tags_count) . "<br />";
			$database_info = apply_filters('fgp2wc_get_database_info', $database_info);
			return $database_info;
		}
		
		/**
		 * Count the number of posts for a post type
		 * @param string $post_type
		 */
		public function count_posts($post_type) {
			$count = 0;
			$excluded_status = array('trash', 'auto-draft');
			$tab_count = wp_count_posts($post_type);
			foreach ( $tab_count as $key => $value ) {
				if ( !in_array($key, $excluded_status) ) {
					$count += $value;
				}
			}
			return $count;
		}
		
		/**
		 * Add an help tab
		 * 
		 */
		public function add_help_tab() {
			$screen = get_current_screen();
			$screen->add_help_tab(array(
				'id'	=> 'fgp2wc_help_instructions',
				'title'	=> __('Instructions'),
				'content'	=> '',
				'callback' => array($this, 'help_instructions'),
			));
			$screen->add_help_tab(array(
				'id'	=> 'fgp2wc_help_options',
				'title'	=> __('Options'),
				'content'	=> '',
				'callback' => array($this, 'help_options'),
			));
			$screen->set_help_sidebar('<a href="' . $this->faq_url . '" target="_blank">' . __('FAQ', 'fg-prestashop-to-woocommerce') . '</a>');
		}
		
		/**
		 * Instructions help screen
		 * 
		 * @return string Help content
		 */
		public function help_instructions() {
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-instructions.tpl.php';
		}
		
		/**
		 * Options help screen
		 * 
		 * @return string Help content
		 */
		public function help_options() {
			include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/help-options.tpl.php';
		}
		
		/**
		 * Open the connection on the PrestaShop database
		 *
		 * return boolean Connection successful or not
		 */
		protected function prestashop_connect() {
			global $prestashop_db;

			if ( !class_exists('PDO') ) {
				$this->display_admin_error(__('PDO is required. Please enable it.', 'fg-prestashop-to-woocommerce'));
				return false;
			}
			try {
				$prestashop_db = new PDO('mysql:host=' . $this->plugin_options['hostname'] . ';port=' . $this->plugin_options['port'] . ';dbname=' . $this->plugin_options['database'], $this->plugin_options['username'], $this->plugin_options['password'], array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''));
				if ( defined('WP_DEBUG') && WP_DEBUG ) {
					$prestashop_db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Display SQL errors
				}
			} catch ( PDOException $e ) {
				$this->display_admin_error(__('Couldn\'t connect to the PrestaShop database. Please check your parameters. And be sure the WordPress server can access the PrestaShop database.', 'fg-prestashop-to-woocommerce') . "<br />\n" . $e->getMessage() . "<br />\n" . sprintf(__('Please read the <a href="%s" target="_blank">FAQ for the solution</a>.', 'fg-prestashop-to-woocommerce'), $this->faq_url));
				return false;
			}
			return true;
		}
		
		/**
		 * Execute a SQL query on the PrestaShop database
		 * 
		 * @param string $sql SQL query
		 * @return array Query result
		 */
		public function prestashop_query($sql) {
			global $prestashop_db;
			$result = array();
			
			try {
				$query = $prestashop_db->query($sql, PDO::FETCH_ASSOC);
				if ( is_object($query) ) {
					foreach ( $query as $row ) {
						$result[] = $row;
					}
				}
				
			} catch ( PDOException $e ) {
				$this->display_admin_error(__('Error:', 'fg-prestashop-to-woocommerce') . $e->getMessage());
			}
			return $result;
		}
		
		/**
		 * Delete all posts, medias and categories from the database
		 *
		 * @param string $action	imported = removes only new imported data
		 * 							all = removes all
		 * @return boolean
		 */
		private function empty_database($action) {
			global $wpdb;
			$result = true;
			
			$wpdb->show_errors();
			
			// Hook for doing other actions before emptying the database
			do_action('fgp2wc_pre_empty_database', $action);
			
			$sql_queries = array();
			
			if ( $action == 'all' ) {
				// Remove all content
				
				$sql_queries[] = "TRUNCATE $wpdb->commentmeta";
				$sql_queries[] = "TRUNCATE $wpdb->comments";
				$sql_queries[] = "TRUNCATE $wpdb->term_relationships";
				$sql_queries[] = "TRUNCATE $wpdb->termmeta";
				$sql_queries[] = "TRUNCATE $wpdb->postmeta";
				$sql_queries[] = "TRUNCATE $wpdb->posts";
				$sql_queries[] = <<<SQL
-- Delete Terms
DELETE FROM $wpdb->terms
WHERE term_id > 1 -- non-classe
SQL;
				$sql_queries[] = <<<SQL
-- Delete Terms taxonomies
DELETE FROM $wpdb->term_taxonomy
WHERE term_id > 1 -- non-classe
SQL;
				$sql_queries[] = "ALTER TABLE $wpdb->terms AUTO_INCREMENT = 2";
				$sql_queries[] = "ALTER TABLE $wpdb->term_taxonomy AUTO_INCREMENT = 2";
				
			} else {
				
				// (Re)create a temporary table with the IDs to delete
				$sql_queries[] = <<<SQL
DROP TEMPORARY TABLE IF EXISTS {$wpdb->prefix}fg_data_to_delete;
SQL;

				$sql_queries[] = <<<SQL
CREATE TEMPORARY TABLE IF NOT EXISTS {$wpdb->prefix}fg_data_to_delete (
`id` bigint(20) unsigned NOT NULL,
PRIMARY KEY (`id`)
) DEFAULT CHARSET=utf8;
SQL;
				
				// Insert the imported posts IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT post_id FROM $wpdb->postmeta
WHERE meta_key LIKE '_fgp2wc_%'
SQL;
				
				// Delete the imported posts and related data

				$sql_queries[] = <<<SQL
-- Delete Comments and Comment metas
DELETE c, cm
FROM $wpdb->comments c
LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE c.comment_post_ID = del.id;
SQL;

				$sql_queries[] = <<<SQL
-- Delete Term relashionships
DELETE tr
FROM $wpdb->term_relationships tr
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE tr.object_id = del.id;
SQL;

				$sql_queries[] = <<<SQL
-- Delete Posts Children and Post metas
DELETE p, pm
FROM $wpdb->posts p
LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE p.post_parent = del.id
AND p.post_type != 'attachment'; -- Don't remove the old medias attached to posts
SQL;

				$sql_queries[] = <<<SQL
-- Delete Posts and Post metas
DELETE p, pm
FROM $wpdb->posts p
LEFT JOIN $wpdb->postmeta pm ON pm.post_id = p.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE p.ID = del.id;
SQL;

				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported terms IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT term_id FROM $wpdb->termmeta
WHERE meta_key LIKE '_fgp2wc_%'
SQL;
				
				// Delete the imported terms and related data

				$sql_queries[] = <<<SQL
-- Delete Terms, Term taxonomies and Term metas
DELETE t, tt, tm
FROM $wpdb->termmeta tm
LEFT JOIN $wpdb->term_taxonomy tt ON tt.term_id = tm.term_id
LEFT JOIN $wpdb->terms t ON t.term_id = tm.term_id
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE tm.term_id = del.id;
SQL;

				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported comments IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT comment_id FROM $wpdb->commentmeta
WHERE meta_key LIKE '_fgp2wc_%'
SQL;
				
				// Delete the imported comments and related data
				$sql_queries[] = <<<SQL
-- Delete Comments and Comment metas
DELETE c, cm
FROM $wpdb->comments c
LEFT JOIN $wpdb->commentmeta cm ON cm.comment_id = c.comment_ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE c.comment_ID = del.id;
SQL;

			}
			
			// Delete WooCommerce transients
			$sql_queries[] = <<<SQL
-- Delete WooCommerce transients
DELETE o FROM $wpdb->options o
WHERE o.option_name LIKE '_transient_wc_%'
OR o.option_name LIKE '_transient_timeout_wc_%';
SQL;

			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$result &= $wpdb->query($sql);
				}
			}
			
			// Hook for doing other actions after emptying the database
			do_action('fgp2wc_post_empty_database', $action);
			
			// Re-count categories and tags items
			$this->terms_count();
			
			// Clean the cache
			$this->clean_cache(array(), 'category');
			$this->clean_cache(array(), 'product_cat');
			delete_transient('wc_count_comments');
			
			$this->optimize_database();
			
			$this->progressbar->set_total_count(0);
			
			$wpdb->hide_errors();
			return ($result !== false);
		}

		/**
		 * Optimize the database
		 *
		 */
		protected function optimize_database() {
			global $wpdb;
			
			$sql = <<<SQL
OPTIMIZE TABLE 
`$wpdb->commentmeta` ,
`$wpdb->comments` ,
`$wpdb->options` ,
`$wpdb->postmeta` ,
`$wpdb->posts` ,
`$wpdb->terms` ,
`$wpdb->term_relationships` ,
`$wpdb->term_taxonomy`,
`$wpdb->termmeta`
SQL;
			$wpdb->query($sql);
		}
		
		/**
		 * Delete all woocommerce data
		 *
		 */
		public function delete_woocommerce_data() {
			global $wpdb;
			global $wc_product_attributes;
			
			$wpdb->show_errors();
			
			$sql_queries = array();
			$sql_queries[] = <<<SQL
-- Disable foreign key checks
SET FOREIGN_KEY_CHECKS=0;
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce attribute taxonomies
TRUNCATE {$wpdb->prefix}woocommerce_attribute_taxonomies
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce order items
TRUNCATE {$wpdb->prefix}woocommerce_order_items
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce order item metas
TRUNCATE {$wpdb->prefix}woocommerce_order_itemmeta
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce download logs
TRUNCATE {$wpdb->prefix}wc_download_log
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce downloadable product permissions
TRUNCATE {$wpdb->prefix}woocommerce_downloadable_product_permissions
SQL;

			$sql_queries[] = <<<SQL
-- Enable foreign key checks
SET FOREIGN_KEY_CHECKS=1;
SQL;

			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$wpdb->query($sql);
				}
			}
			
			// Reset the WC pages flags
			$wc_pages = array('shop', 'cart', 'checkout', 'myaccount');
			foreach ( $wc_pages as $wc_page ) {
				update_option('woocommerce_' . $wc_page . '_page_id', 0);
			}
			
			// Empty attribute taxonomies cache
			delete_transient('wc_attribute_taxonomies');
			$wc_product_attributes = array();
			$this->delete_var_prices_transient();
			
			// Reset the PrestaShop last imported post ID
			update_option('fgp2wc_last_cms_id', 0);
			update_option('fgp2wc_last_product_id', 0);
			
			$wpdb->hide_errors();
			
			$this->display_admin_notice(__('WooCommerce data deleted', 'fg-prestashop-to-woocommerce'));
			
			// Recreate WooCommerce default data
			if ( class_exists('WC_Install') ) {
				WC_Install::create_pages();
				$this->display_admin_notice(__('WooCommerce default data created', 'fg-prestashop-to-woocommerce'));
			}
		}
		
		/**
		 * Delete the wc_var_prices transient
		 * 
		 * @since 3.35.0
		 */
		public function delete_var_prices_transient() {
			$this->delete_transient('wc_var_prices_');
		}
		
		/**
		 * Delete the transient
		 * 
		 * @since 3.35.0
		 * 
		 * @param string $transient Transient
		 */
		public function delete_transient($transient) {
			global $wpdb;
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_$transient%') OR `option_name` LIKE ('_transient_timeout_$transient%')" );
		}
		
		/**
		 * Test the database connection
		 * 
		 * @return boolean
		 */
		private function test_database_connection() {
			global $prestashop_db;
			
			if ( $this->prestashop_connect() ) {
				try {
					$prefix = $this->plugin_options['prefix'];
					
					// Test that the "product" table exists
					$result = $prestashop_db->query("DESC ${prefix}product");
					if ( !is_a($result, 'PDOStatement') ) {
						$errorInfo = $prestashop_db->errorInfo();
						throw new PDOException($errorInfo[2], $errorInfo[1]);
					}
					
					$this->display_admin_notice(__('Connected with success to the PrestaShop database', 'fg-prestashop-to-woocommerce'));
					
					$this->import_configuration();
					do_action('fgp2wc_post_test_database_connection');
					
					return true;
					
				} catch ( PDOException $e ) {
					$this->display_admin_error(__('Couldn\'t connect to the PrestaShop database. Please check your parameters. And be sure the WordPress server can access the PrestaShop database.', 'fg-prestashop-to-woocommerce') . "<br />\n" . $e->getMessage());
					return false;
				}
				$prestashop_db = null;
			}
			return false;
		}
		
		/**
		 * Test if the WooCommerce plugin is activated
		 *
		 * @return bool True if the WooCommerce plugin is activated
		 */
		public function test_woocommerce_activation() {
			if ( !class_exists('WooCommerce', false) ) {
				$this->display_admin_error(__('Error: the <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce plugin</a> must be installed and activated to import the products.', 'fg-prestashop-to-woocommerce'));
				return false;
			}
			return true;
		}

		/**
		 * Get some PrestaShop information
		 *
		 */
		public function get_prestashop_info() {
			$message = __('PrestaShop data found:', 'fg-prestashop-to-woocommerce') . "\n";
			
			// Products
			$products_count = $this->get_products_count();
			$message .= sprintf(_n('%d product', '%d products', $products_count, 'fg-prestashop-to-woocommerce'), $products_count) . "\n";
			
			// Product categories
			$products_categories_count = $this->get_products_categories_count();
			$message .= sprintf(_n('%d product category', '%d product categories', $products_categories_count, 'fg-prestashop-to-woocommerce'), $products_categories_count) . "\n";
			
			// CMS Articles
			$posts_count = $this->get_cms_count();
			$message .= sprintf(_n('%d CMS article', '%d CMS articles', $posts_count, 'fg-prestashop-to-woocommerce'), $posts_count) . "\n";
			
			// CMS categories
			$cms_categories_count = $this->get_cms_categories_count();
			$message .= sprintf(_n('%d CMS category', '%d CMS categories', $cms_categories_count, 'fg-prestashop-to-woocommerce'), $cms_categories_count) . "\n";
			
			// Employees
			$employees_count = $this->get_employees_count();
			$message .= sprintf(_n('%d employee', '%d employees', $employees_count, 'fg-prestashop-to-woocommerce'), $employees_count) . "\n";
			
			// Customers
			$customers_count = $this->get_customers_count();
			$message .= sprintf(_n('%d customer', '%d customers', $customers_count, 'fg-prestashop-to-woocommerce'), $customers_count) . "\n";
			
			// Orders
			$orders_count = $this->get_orders_count();
			$message .= sprintf(_n('%d order', '%d orders', $orders_count, 'fg-prestashop-to-woocommerce'), $orders_count) . "\n";
			
			$message = apply_filters('fgp2wc_pre_display_prestashop_info', $message);
			
			$this->display_admin_notice($message);
		}
		
		/**
		 * Get the number of PrestaShop products
		 * 
		 * @return int Number of products
		 */
		private function get_products_count() {
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}product
			";
			$result = $this->prestashop_query($sql);
			$products_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $products_count;
		}
		
		/**
		 * Get the number of PrestaShop products categories
		 * 
		 * @since 3.0.0
		 * 
		 * @return int Number of products categories
		 */
		private function get_products_categories_count() {
			$prefix = $this->plugin_options['prefix'];
			
			// Exclude the parent categories of the root category
			$root_category = $this->get_root_category();
			$exclude_root_parent_categories_sql = '';
			if ( !empty($root_category) ) {
				$exclude_root_parent_categories_sql = "AND c.level_depth >= " . $root_category['level_depth'];
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}category c
				WHERE c.active = 1
				$exclude_root_parent_categories_sql
			";
			$result = $this->prestashop_query($sql);
			$products_cat_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $products_cat_count;
		}
		
		/**
		 * Get the number of PrestaShop articles
		 * 
		 * @return int Number of articles
		 */
		private function get_cms_count() {
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}cms
			";
			$result = $this->prestashop_query($sql);
			$cms_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $cms_count;
		}
		
		/**
		 * Get the number of PrestaShop CMS categories
		 * 
		 * @since 3.0.0
		 * 
		 * @return int Number of categories
		 */
		private function get_cms_categories_count() {
			$prefix = $this->plugin_options['prefix'];
			if ( version_compare($this->prestashop_version, '1.4', '<') ) {
				$category_table = 'category'; // PrestaShop 1.4
			} else {
				$category_table = 'cms_category';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}$category_table c
				WHERE c.active = 1
			";
			$result = $this->prestashop_query($sql);
			$cat_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $cat_count;
		}
		
		/**
		 * Get the number of PrestaShop employees
		 * 
		 * @return int Number of employees
		 */
		public function get_employees_count() {
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}employee
				WHERE active = 1
			";
			$result = $this->prestashop_query($sql);
			$employees_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $employees_count;
		}
		
		/**
		 * Get the number of PrestaShop customers
		 * 
		 * @return int Number of customers
		 */
		public function get_customers_count() {
			$prefix = $this->plugin_options['prefix'];
			$deleted = '';
			if ( $this->column_exists('customer', 'deleted') ) {
				$deleted = 'AND c.deleted = 0';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}customer c
				WHERE c.active = 1
				$deleted
			";
			$result = $this->prestashop_query($sql);
			$customers_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $customers_count;
		}
		
		/**
		 * Get the number of PrestaShop orders
		 * 
		 * @return int Number of orders
		 */
		private function get_orders_count() {
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}orders
			";
			$result = $this->prestashop_query($sql);
			$orders_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $orders_count;
		}
		
		/**
		 * Save the plugin options
		 *
		 */
		public function save_plugin_options() {
			$this->plugin_options = array_merge($this->plugin_options, $this->validate_form_info());
			update_option('fgp2wc_options', $this->plugin_options);
			
			// Hook for doing other actions after saving the options
			do_action('fgp2wc_post_save_plugin_options');
		}
		
		/**
		 * Validate POST info
		 *
		 * @return array Form parameters
		 */
		private function validate_form_info() {
			// Add http:// before the URL if it is missing
			$url = esc_url(filter_input(INPUT_POST, 'url', FILTER_SANITIZE_URL));
			if ( !empty($url) && (preg_match('#^https?://#', $url) == 0) ) {
				$url = 'http://' . $url;
			}
			return array(
				'automatic_empty'				=> filter_input(INPUT_POST, 'automatic_empty', FILTER_VALIDATE_BOOLEAN),
				'url'							=> $url,
				'hostname'						=> filter_input(INPUT_POST, 'hostname', FILTER_SANITIZE_STRING),
				'port'							=> filter_input(INPUT_POST, 'port', FILTER_SANITIZE_NUMBER_INT),
				'database'						=> filter_input(INPUT_POST, 'database', FILTER_SANITIZE_STRING),
				'username'						=> filter_input(INPUT_POST, 'username'),
				'password'						=> filter_input(INPUT_POST, 'password'),
				'prefix'						=> filter_input(INPUT_POST, 'prefix', FILTER_SANITIZE_STRING),
				'sku'							=> filter_input(INPUT_POST, 'sku', FILTER_SANITIZE_STRING),
				'skip_media'					=> filter_input(INPUT_POST, 'skip_media', FILTER_VALIDATE_BOOLEAN),
				'first_image'					=> filter_input(INPUT_POST, 'first_image', FILTER_SANITIZE_STRING),
				'image_size'					=> filter_input(INPUT_POST, 'image_size', FILTER_SANITIZE_STRING),
				'import_external'				=> filter_input(INPUT_POST, 'import_external', FILTER_VALIDATE_BOOLEAN),
				'import_duplicates'				=> filter_input(INPUT_POST, 'import_duplicates', FILTER_VALIDATE_BOOLEAN),
				'force_media_import'			=> filter_input(INPUT_POST, 'force_media_import', FILTER_VALIDATE_BOOLEAN),
				'meta_keywords_in_tags'			=> filter_input(INPUT_POST, 'meta_keywords_in_tags', FILTER_VALIDATE_BOOLEAN),
				'import_as_pages'				=> filter_input(INPUT_POST, 'import_as_pages', FILTER_VALIDATE_BOOLEAN),
				'timeout'						=> filter_input(INPUT_POST, 'timeout', FILTER_SANITIZE_NUMBER_INT),
				'price'							=> filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING),
				'stock_management'				=> filter_input(INPUT_POST, 'stock_management', FILTER_VALIDATE_BOOLEAN),
				'first_image_not_in_gallery'	=> filter_input(INPUT_POST, 'first_image_not_in_gallery', FILTER_VALIDATE_BOOLEAN),
				'logger_autorefresh'			=> filter_input(INPUT_POST, 'logger_autorefresh', FILTER_VALIDATE_BOOLEAN),
			);
		}
		
		/**
		 * Import
		 *
		 */
		private function import() {
			if ( $this->prestashop_connect() ) {
				
				$time_start = microtime(true);
				
				define('WP_IMPORTING', true);
				update_option('fgp2wc_stop_import', false, false); // Reset the stop import action
				
				// Check prerequesites before the import
				$do_import = apply_filters('fgp2wc_pre_import_check', true);
				if ( !$do_import) {
					return;
				}
				
				$this->import_configuration();
				$total_elements_count = $this->get_total_elements_count();
				$this->progressbar->set_total_count($total_elements_count);
				
				$this->post_type = ($this->plugin_options['import_as_pages'] == 1) ? 'page' : 'post';

				$this->product_types = $this->create_woocommerce_product_types(); // (Re)create the WooCommerce product types
				$this->product_visibilities = $this->create_woocommerce_product_visibilities(); // (Re)create the WooCommerce product visibilities
				$this->global_tax_rate = $this->get_default_tax_rate();
				
				// Hook for doing other actions before the import
				do_action('fgp2wc_pre_import');
				
				if ( !isset($this->premium_options['skip_cms']) || !$this->premium_options['skip_cms'] ) {
					$this->import_cms();
				}
				if ( !isset($this->premium_options['skip_products_categories']) || !$this->premium_options['skip_products_categories'] ) {
					$this->import_product_categories();
				}
				if ( !isset($this->premium_options['skip_products']) || !$this->premium_options['skip_products'] ) {
					$this->import_products();
					$this->imported_products = $this->get_imported_products();
				}
				
				if ( !$this->import_stopped() ) {
					// Hook for doing other actions after the import
					do_action('fgp2wc_post_import');
				}
				
				// Hook for other notices
				do_action('fgp2wc_import_notices');
				
				// Debug info
				if ( defined('WP_DEBUG') && WP_DEBUG ) {
					$this->display_admin_notice(sprintf("Memory used: %s bytes<br />\n", number_format(memory_get_usage())));
					$time_end = microtime(true);
					$this->display_admin_notice(sprintf("Duration: %d sec<br />\n", $time_end - $time_start));
				}
				
				if ( $this->import_stopped() ) {
					// Import stopped by the user
					$this->display_admin_notice("IMPORT STOPPED BY USER");
				} else {
					// Import completed
					$this->display_admin_notice("IMPORT COMPLETED");
				}
				wp_cache_flush();
			}
		}
		
		/**
		 * Actions to do before the import
		 * 
		 * @param bool $import_doable Can we start the import?
		 * @return bool Can we start the import?
		 */
		public function pre_import_check($import_doable) {
			if ( $import_doable ) {
				if ( !$this->plugin_options['skip_media'] && empty($this->plugin_options['url']) ) {
					$this->display_admin_error(__('The URL field is required to import the media.', 'fg-prestashop-to-woocommerce'));
					$import_doable = false;
				}
			}
			return $import_doable;
		}

		/**
		 * Get the number of elements to import
		 * 
		 * @since 3.0.0
		 * 
		 * @return int Number of elements to import
		 */
		private function get_total_elements_count() {
			$count = 0;
			
			// CMS
			if ( !isset($this->premium_options['skip_cms']) || !$this->premium_options['skip_cms'] ) {
				$count += $this->get_cms_categories_count();
				$count += $this->get_cms_count();
			}

			// Products categories
			if ( !isset($this->premium_options['skip_products_categories']) || !$this->premium_options['skip_products_categories'] ) {
				$count += $this->get_products_categories_count();
			}
			
			// Products
			if ( !isset($this->premium_options['skip_products']) || !$this->premium_options['skip_products'] ) {
				$count += $this->get_products_count();
			}

			$count = apply_filters('fgp2wc_get_total_elements_count', $count);
			
			return $count;
		}
		
		/**
		 * Stop the import
		 * 
		 * @since 3.0.0
		 */
		public function stop_import() {
			update_option('fgp2wc_stop_import', true);
		}
		
		/**
		 * Test if the import needs to stop
		 * 
		 * @since 3.0.0
		 * 
		 * @return boolean Import needs to stop or not
		 */
		public function import_stopped() {
			return get_option('fgp2wc_stop_import');
		}
		
		/**
		 * Create the WooCommerce product types
		 *
		 * @return array Product types
		 */
		protected function create_woocommerce_product_types() {
			return $this->create_unique_terms(
				array(
					'simple',
					'grouped',
					'variable',
					'external',
				), 'product_type');
		}
		
		/**
		 * Create the WooCommerce visibilities
		 *
		 * @since 3.27.0
		 * 
		 * @return array Product visibilities
		 */
		protected function create_woocommerce_product_visibilities() {
			return $this->create_unique_terms(
				array(
					'exclude-from-search',
					'exclude-from-catalog',
					'outofstock',
				), 'product_visibility');
		}
		
		/**
		 * Create unique terms and get them
		 *
		 * @since 3.27.0
		 * 
		 * @param array $term_slugs Term slugs
		 * @param string $taxonomy Taxonomy
		 * @return array Terms
		 */
		private function create_unique_terms($term_slugs, $taxonomy) {
			$terms = array();
			foreach ( $term_slugs as $term_slug ) {
				$term = get_term_by('slug', $term_slug, $taxonomy);
				if ( !empty($term) ) {
					$terms[$term_slug] = $term->term_id;
				} else {
					$new_term = wp_insert_term($term_slug, $taxonomy);
					if ( !is_wp_error($new_term) ) {
						$terms[$term_slug] = $new_term['term_id'];
					}
				}
			}
			return $terms;
		}
		
		/**
		 * Import PrestaShop configuration
		 */
		protected function import_configuration() {
			$config = $this->get_configuration();
			$this->default_language = $config['PS_LANG_DEFAULT'];
			$this->current_language = $this->default_language;
			$this->prestashop_version = $this->get_prestashop_version($config);
			$this->default_backorders = ($config['PS_ORDER_OUT_OF_STOCK'] == 1)? 'yes' : 'no';
			$this->default_country = isset($config['PS_COUNTRY_DEFAULT'])? $config['PS_COUNTRY_DEFAULT'] : 0;
		}
		
		/**
		 * Return the PrestaShop version
		 * 
		 * @since 3.7.4
		 * 
		 * @param array $config PrestaShop constants
		 * @return string PrestaShop version
		 */
		private function get_prestashop_version($config) {
			$version = '0';
			if ( isset($config['PS_VERSION_DB']) ) {
				$version = $config['PS_VERSION_DB'];
			} elseif ( isset($config['PS_INSTALL_VERSION']) ) {
				$version = $config['PS_INSTALL_VERSION'];
			} elseif ( !$this->column_exists('product', 'location') ) {
				$version = '1.0';
			} elseif ( !$this->column_exists('orders', 'total_products_wt') ) {
				$version = '1.2';
			} elseif ( !$this->table_exists('cms_category') ) {
				$version = '1.3';
			} elseif ( !$this->table_exists('stock_available') ) {
				$version = '1.4';
			} else {
				$version = '1.5';
			}
			return $version;
		}

		/**
		 * Import CMS data
		 */
		private function import_cms() {
			$this->import_cms_categories();
			$this->import_cms_articles();
		}
		
		/**
		 * Import CMS categories
		 *
		 * @return int Number of CMS categories imported
		 */
		private function import_cms_categories() {
			$taxonomy = 'category';
			$cat_count = 0;
			
			if ( $this->import_stopped() ) {
				return 0;
			}
			$this->log(__('Importing CMS categories...', 'fg-prestashop-to-woocommerce'));
			
			// Set the list of previously imported categories
			$this->get_imported_cms_categories($this->current_language);
			
			$categories = $this->get_cms_categories();
			if ( is_array($categories) ) {
				$terms = array('1'); // unclassified category
				foreach ( $categories as $category ) {
					
					// Check if the category is already imported
					if ( array_key_exists($category['id_category'], $this->imported_cms_categories[$this->current_language]) ) {
						continue; // Do not import already imported category
					}

					// Insert the category
					$new_category = array(
						'cat_name' 				=> $category['name'],
						'category_description'	=> isset($category['description'])? $category['description']: '',
						'category_nicename'		=> $category['slug'],
					);
					
					// Hook before inserting the category
					$new_category = apply_filters('fgp2wc_pre_insert_category', $new_category, $category);
					
					if ( ($cat_id = wp_insert_category($new_category)) !== false ) {
						$cat_count++;
						$terms[] = $cat_id;
						add_term_meta($cat_id, '_fgp2wc_old_cms_category_id' . '-lang' . $this->current_language, $category['id_category'], true); // Store the category ID
					}
					
					// Hook after inserting the category
					do_action('fgp2wc_post_insert_category', $cat_id, $category);
				}
				
				// Set the list of previously imported categories
				$this->get_imported_cms_categories($this->current_language);
				
				// Update the categories with their parent ids
				// We need to do it in a second step because the children categories
				// may have been imported before their parent
				foreach ( $categories as $category ) {
					if ( array_key_exists($category['id_category'], $this->imported_cms_categories[$this->current_language]) && array_key_exists($category['id_parent'], $this->imported_cms_categories[$this->current_language]) ) {
						$cat_id = $this->imported_cms_categories[$this->current_language][$category['id_category']];
						$parent_cat_id = $this->imported_cms_categories[$this->current_language][$category['id_parent']];
						wp_update_term($cat_id, $taxonomy, array('parent' => $parent_cat_id));
					}
				}
				
				// Hook after importing all the categories
				do_action('fgp2wc_post_import_categories', $categories);
				
				// Update cache
				if ( !empty($terms) ) {
					wp_update_term_count_now($terms, $taxonomy);
					$this->clean_cache($terms, $taxonomy);
				}
			}
			$this->progressbar->increment_current_count($cat_count);
			$this->display_admin_notice(sprintf(_n('%d category imported', '%d categories imported', $cat_count, 'fg-prestashop-to-woocommerce'), $cat_count));
			return $cat_count;
		}
		
		/**
		 * Clean the cache
		 * 
		 */
		public function clean_cache($terms=array(), $taxonomy='category') {
			delete_option($taxonomy . '_children');
			clean_term_cache($terms, $taxonomy);
		}

		/**
		 * Store the mapping of the imported CMS categories
		 * 
		 * @param int $language Language ID
		 */
		public function get_imported_cms_categories($language) {
			$this->imported_cms_categories[$language] = $this->get_term_metas_by_metakey('_fgp2wc_old_cms_category_id' . '-lang' . $language);
		}
		
		/**
		 * Import CMS articles
		 *
		 * @return array:
		 * 		int posts_count: Number of posts imported
		 */
		private function import_cms_articles() {
			$imported_posts_count = 0;
			$this->imported_tags = array();
			
			if ( $this->import_stopped() ) {
				return 0;
			}
			$this->log(__('Importing CMS articles...', 'fg-prestashop-to-woocommerce'));
			
			// Set the list of previously imported categories
			$this->get_imported_cms_categories($this->current_language);
			
			// Hook for doing other actions before the import
			do_action('fgp2wc_pre_import_posts');
			
			do {
				if ( $this->import_stopped() ) {
					break;
				}
				$posts = $this->get_cms_articles($this->chunks_size); // Get the CMS articles
				$posts_count = count($posts);
				
				if ( is_array($posts) ) {
					foreach ( $posts as $post ) {
						
						$new_post_id = $this->import_cms_article($post, $this->current_language);
						
						// Increment the CMS last imported post ID
						update_option('fgp2wc_last_cms_id', $post['id_cms']);
						
						if ( !is_wp_error($new_post_id) ) {
							$imported_posts_count++;
							
							// Hook for doing other actions after inserting the post
							do_action('fgp2wc_post_insert_post', $new_post_id, $post);
						}
					}
				}
				$this->progressbar->increment_current_count($posts_count);
			} while ( ($posts != null) && ($posts_count > 0) );
			
			// Hook for doing other actions after the import
			do_action('fgp2wc_post_import_posts');
			
			$tags_count = count(array_unique($this->imported_tags));
			$this->display_admin_notice(sprintf(_n('%d article imported', '%d articles imported', $imported_posts_count, 'fg-prestashop-to-woocommerce'), $imported_posts_count));
			$this->display_admin_notice(sprintf(_n('%d tag imported', '%d tags imported', $tags_count, 'fg-prestashop-to-woocommerce'), $tags_count));
			return array(
				'posts_count'	=> $imported_posts_count,
				'tags_count'	=> $tags_count,
			);
		}
		
		/**
		 * Import a CMS article
		 *
		 * @param array $post Post data
		 * @param int $language Language ID
		 * @return int $new_post_id New imported post ID
		 */
		public function import_cms_article($post, $language) {
			// Hook for modifying the CMS post before processing
			$post = apply_filters('fgp2wc_pre_process_post', $post);

			// Date
			$post_date = $post['date'];

			// Content
			$content = $post['content'];

			// Medias
			if ( !$this->plugin_options['skip_media'] ) {
				// Extra featured image
				$featured_image = '';
				list($featured_image, $post) = apply_filters('fgp2wc_pre_import_media', array($featured_image, $post));
				// Import media
				$result = $this->import_media_from_content($featured_image . $content, $post_date);
				$post_media = $result['media'];
				$this->media_count += $result['media_count'];
			} else {
				// Skip media
				$post_media = array();
			}

			// Categories IDs
			$categories = array($post['id_category']);
			// Hook for modifying the post categories
			$categories = apply_filters('fgp2wc_post_categories', $categories, $post);
			$categories_ids = array();
			foreach ( $categories as $ps_category_id ) {
				if ( array_key_exists($ps_category_id, $this->imported_cms_categories[$language]) ) {
					$categories_ids[] = $this->imported_cms_categories[$language][$ps_category_id];
				}
			}
			if ( count($categories_ids) == 0 ) {
				$categories_ids[] = 1; // default category
			}

			// Process content
			$content = $this->process_content($content, $post_media);

			// Status
			$status = ($post['active'] == 1)? 'publish' : 'draft';

			// Tags
			$tags = array();
			if ( $this->plugin_options['meta_keywords_in_tags'] && !empty($post['meta_keywords']) ) {
				$tags = explode(',', $post['meta_keywords']);
				$this->import_tags($tags, 'post_tag');
				$this->imported_tags = array_merge($this->imported_tags, $tags);
			}

			// Insert the post
			$new_post = array(
				'post_category'		=> $categories_ids,
				'post_content'		=> $content,
				'post_date'			=> $post_date,
				'post_status'		=> $status,
				'post_title'		=> $post['meta_title'],
				'post_name'			=> $post['slug'],
				'post_type'			=> $this->post_type,
				'tags_input'		=> $tags,
				'menu_order'        => $post['position'],
			);

			// Hook for modifying the WordPress post just before the insert
			$new_post = apply_filters('fgp2wc_pre_insert_post', $new_post, $post);

			$new_post_id = wp_insert_post($new_post, true);
			
			if ( !is_wp_error($new_post_id) ) {
				add_post_meta($new_post_id, '_fgp2wc_old_cms_article_id', $post['id_cms']);
				
				// Add links between the post and its medias
				$this->add_post_media($new_post_id, $this->get_attachment_ids($post_media), $post_date, $this->plugin_options['first_image'] != 'as_is');
			}
			return $new_post_id;
		}
		
		/**
		 * Import tags
		 * 
		 * @since 2.8.0
		 * 
		 * @param array $tags Tags
		 * @param string $taxonomy Taxonomy (post_tag | product_tag)
		 */
		public function import_tags($tags, $taxonomy) {
			foreach ( $tags as $tag ) {
				$new_term = wp_insert_term($tag, $taxonomy);
				if ( !is_wp_error($new_term) ) {
					add_term_meta($new_term['term_id'], '_fgp2wc_imported', 1, true);
				}
			}
		}
		
		/**
		 * Get the imported products
		 *
		 * @since 3.36.0
		 * 
		 * @return array of products mapped with the PrestaShop products ids
		 */
		public function get_imported_products() {
			return $this->get_imported_ps_posts($meta_key = '_fgp2wc_old_product_id');
		}
		
		/**
		 * Returns the imported posts mapped with their PrestaShop ID
		 *
		 * @since 3.6.0
		 * 
		 * @param string $meta_key Meta key (default = _fgp2wc_old_cms_article_id)
		 * @return array of post IDs [ps_article_id => wordpress_post_id]
		 */
		public function get_imported_ps_posts($meta_key = '_fgp2wc_old_cms_article_id') {
			global $wpdb;
			$posts = array();

			$sql = "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '$meta_key'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$posts[$result->meta_value] = $result->post_id;
			}
			ksort($posts);
			return $posts;
		}

		/**
		 * Store the mapping of the imported product categories
		 * 
		 * @param int $language Language ID
		 */
		public function get_imported_categories($language) {
			$this->imported_categories[$language] = $this->get_term_metas_by_metakey('_fgp2wc_old_product_category_id' . '-lang' . $language);
		}
		
		/**
		 * Store the mapping of the imported manufacturers
		 * 
		 * @since 3.2.0
		 * 
		 * @param int $language Language ID
		 */
		public function get_imported_manufacturers($language) {
			$this->imported_manufacturers[$language] = $this->get_term_metas_by_metakey('_fgp2wc_old_manufacturer_id' . '-lang' . $language);
		}
		
		/**
		 * Import product categories
		 *
		 * @return int Number of product categories imported
		 */
		private function import_product_categories() {
			$cat_count = 0;
			$imported_cat_count = 0;
			$terms = array();
			$taxonomy = 'product_cat';
			
			if ( $this->import_stopped() ) {
				return 0;
			}
			$this->log(__('Importing product categories...', 'fg-prestashop-to-woocommerce'));
			
			// Allow HTML in term descriptions
			foreach ( array('pre_term_description') as $filter ) {
				remove_filter($filter, 'wp_filter_kses');
			}
			
			// Set the list of previously imported categories
			$this->get_imported_categories($this->current_language);
			
			$categories = $this->get_all_product_categories();
			$cat_count = count($categories);
			foreach ( $categories as $category ) {
				
				// Check if the category is already imported
				if ( array_key_exists($category['id_category'], $this->imported_categories[$this->current_language]) ) {
					continue; // Do not import already imported category
				}
				
				// Date
				$date = $category['date'];
				
				// Insert the category
				$new_category = array(
					'description'	=> isset($category['description'])? $category['description']: '',
					'slug'			=> $category['slug'], // slug
				);
				
				// Parent category ID
				if ( array_key_exists($category['id_parent'], $this->imported_categories[$this->current_language]) ) {
					$parent_cat_id = $this->imported_categories[$this->current_language][$category['id_parent']];
					$new_category['parent'] = $parent_cat_id;
				}
				
				// Hook before inserting the category
				$new_category = apply_filters('fgp2wc_pre_insert_product_category', $new_category, $category);
				
				$new_term = wp_insert_term($category['name'], $taxonomy, $new_category);
				if ( !is_wp_error($new_term) ) {
					$imported_cat_count++;
					$terms[] = $new_term['term_id'];
					$this->imported_categories[$this->current_language][$category['id_category']] = $new_term['term_id'];
					
					// Store the product category ID
					add_term_meta($new_term['term_id'], '_fgp2wc_old_product_category_id' . '-lang' . $this->current_language, $category['id_category'], true);
					
					// Category ordering
					if ( function_exists('wc_set_term_order') ) {
						wc_set_term_order($new_term['term_id'], $category['position'], $taxonomy);
					}
					
					// Category thumbnails
					if ( !$this->plugin_options['skip_media'] && function_exists('update_woocommerce_term_meta') ) {
						if ( ($category['id_parent'] != 0) && ($category['is_root_category'] != 1) ) { // Don't try to import root categories thumbnails
							$category_thumbnails = $this->build_image_filenames('category', $category['id_category']); // Get the potential filenames
							foreach ( $category_thumbnails as $category_thumbnail ) {
								if ( !empty($category_thumbnail) ) {
									$thumbnail_id = $this->import_media($category['name'], $category_thumbnail, $date);
									if ( !empty($thumbnail_id) ) {
										$this->media_count++;
										update_woocommerce_term_meta($new_term['term_id'], 'thumbnail_id', $thumbnail_id);
										break; // the media has been imported, we don't continue with the other potential filenames
									}
								}
							}
						}
					}
					
					// Hook after inserting the category
					do_action('fgp2wc_post_insert_product_category', $new_term['term_id'], $category);
				}
			}
			
			// Set the list of imported categories
			$this->get_imported_categories($this->current_language);
			
			// Hook after importing all the categories
			do_action('fgp2wc_post_import_product_categories', $categories);
			
			// Update cache
			if ( !empty($terms) ) {
				wp_update_term_count_now($terms, $taxonomy);
				$this->clean_cache($terms, $taxonomy);
			}
			$this->progressbar->increment_current_count($cat_count);
			$this->display_admin_notice(sprintf(_n('%d product category imported', '%d product categories imported', $imported_cat_count, 'fg-prestashop-to-woocommerce'), $imported_cat_count));
		}
		
		/**
		 * Import products
		 *
		 * @return int Number of products imported
		 */
		private function import_products() {
			$imported_products_count = 0;
			
			if ( !$this->test_woocommerce_activation() ) {
				return 0;
			}
			
			if ( $this->import_stopped() ) {
				return 0;
			}
			
			// Hook for doing other actions before importing the products
			do_action('fgp2wc_pre_import_products');
			
			if ( $this->import_stopped() ) {
				return 0;
			}
			
			$this->log(__('Importing products...', 'fg-prestashop-to-woocommerce'));
			
			do {
				if ( $this->import_stopped() ) {
					break;
				}
				$products = $this->get_products($this->chunks_size);
				$products_count = count($products);
				foreach ( $products as $product ) {
					
					$new_post_id = $this->import_product($product, $this->current_language);
					
					// Increment the PrestaShop last imported product ID
					update_option('fgp2wc_last_product_id', $product['id_product']);

					if ( $new_post_id ) {
						$imported_products_count++;
						
						update_option('fgp2wc_last_update', date('Y-m-d H:i:s'));

						// Hook for doing other actions after inserting the post
						do_action('fgp2wc_post_import_product', $new_post_id, $product);
					}
				}
				$this->progressbar->increment_current_count($products_count);
			} while ( ($products != null) && ($products_count > 0) );
			
			// Recount the terms
			$this->recount_terms();
			
			$this->display_admin_notice(sprintf(_n('%d product imported', '%d products imported', $imported_products_count, 'fg-prestashop-to-woocommerce'), $imported_products_count));
		}
		
		/**
		 * Import a product
		 *
		 * @param array $product Product data
		 * @param int $language Language ID
		 * @return int New post ID
		 */
		public function import_product($product, $language) {
			$product_medias = array();
			$post_media = array();

			// Date
			$date = $product['date'];

			// Product images
			if ( !$this->plugin_options['skip_media'] ) {

				$images = $this->get_product_images($product['id_product']);
				foreach ( $images as $image ) {
					$image_name = !empty($image['legend'])? $image['legend'] : $product['name'];
					$image_filenames = $this->build_image_filenames('product', $image['id_image'], $product['id_product']); // Get the potential filenames
					$media_id = $this->guess_import_media($image_name, $image_filenames, $date, $image['id_image']);
					if ( $media_id !== false ) {
						$product_medias[] = $media_id;
					}
				}
				$this->media_count += count($product_medias);

				// Import content media
				$result = $this->import_media_from_content($product['description'] . $product['description_short'], $date);
				$post_media = $result['media'];
				$this->media_count += $result['media_count'];
			}

			// Product categories
			$categories_ids = array();
			$product_categories = $this->get_product_categories($product['id_product']);
			foreach ( $product_categories as $cat ) {
				if ( isset($this->imported_categories[$language]) && array_key_exists($cat, $this->imported_categories[$language]) ) {
					$categories_ids[] = $this->imported_categories[$language][$cat];
				}
			}

			// Tags
			$tags = $this->get_product_tags($product['id_product']);
			if ( $this->plugin_options['meta_keywords_in_tags'] && !empty($product['meta_keywords']) ) {
				$tags = array_merge($tags, explode(',', $product['meta_keywords']));
			}
			$this->import_tags($tags, 'product_tag');

			// Process content
			$content = isset($product['description'])? $product['description'] : '';
			$content = $this->process_content($content, $post_media);
			$excerpt = isset($product['description_short'])? $product['description_short'] : '';
			$excerpt = $this->process_content($excerpt, $post_media);
			
			// Status
			$status = (($product['active'] == 1) && ($product['available_for_order'] == 1))? 'publish': 'draft';
			
			// Insert the post
			$new_post = array(
				'post_content'		=> $content,
				'post_date'			=> $date,
				'post_excerpt'		=> $excerpt,
				'post_status'		=> $status,
				'post_title'		=> $product['name'],
				'post_name'			=> $product['slug'],
				'post_type'			=> 'product',
				'tax_input'			=> array(
					'product_cat'	=> $categories_ids,
					'product_tag'	=> $tags,
				),
			);

			// Hook for modifying the WordPress post just before the insert
			$new_post = apply_filters('fgp2wc_pre_insert_product', $new_post, $product);
			
			$new_post_id = wp_insert_post($new_post);

			if ( $new_post_id ) {
				// Product type (simple or variable)
				$product_type = $this->product_types['simple'];
				wp_set_object_terms($new_post_id, intval($product_type), 'product_type', true);
				
				// Product visibility
				$this->set_product_visibility($new_post_id, $product['visibility']);
				
				// Product galleries
				$medias_id = array();
				foreach ($product_medias as $media) {
					$medias_id[] = $media;
				}
				if ( $this->plugin_options['first_image_not_in_gallery'] ) {
					// Don't include the first image into the product gallery
					array_shift($medias_id);
				}
				$gallery = implode(',', $medias_id);

				// Prices
				$product['specific_prices'] = $this->get_specific_prices($product['id_product']);
				$prices = $this->calculate_prices($product);
				$reduction_from = isset($product['reduction_from'])? strtotime($product['reduction_from']): '';
				$reduction_to = isset($product['reduction_to'])? strtotime($product['reduction_to']): '';
				
				// SKU = Stock Keeping Unit
				$sku = $this->get_sku($product);
				if ( empty($sku) ) {
					$sku = $this->get_product_supplier_reference($product['id_product']);
				}

				// Stock
				$manage_stock = $this->plugin_options['stock_management']? 'yes': 'no';
				$quantity = 0;
				$out_of_stock_value = 0;
				if ( version_compare($this->prestashop_version, '1.5', '<') ) {
					$quantity = $product['quantity'];
					$out_of_stock_value = $product['out_of_stock'];
				} else {
					$stock = $this->get_product_stock($product['id_product'], $product['id_shop_default']);
					if ( !empty($stock) ) {
						$quantity = $stock['quantity'];
						$out_of_stock_value = $stock['out_of_stock'];
					}
				}
				$stock_status = (!$this->plugin_options['stock_management'] || ($quantity > 0))? 'instock': 'outofstock';
				if ( $stock_status == 'outofstock' ) {
					wp_set_object_terms($new_post_id, $this->product_visibilities['outofstock'], 'product_visibility', true);
				}

				// Backorders
				$backorders = $this->allow_backorders($out_of_stock_value);

				// Add the meta data
				add_post_meta($new_post_id, '_stock_status', $stock_status, true);
				add_post_meta($new_post_id, '_regular_price', $prices['regular_price'], true);
				add_post_meta($new_post_id, '_price', $prices['price'], true);
				add_post_meta($new_post_id, '_sale_price', $prices['special_price'], true);
				add_post_meta($new_post_id, '_sale_price_dates_from', $reduction_from, true);
				add_post_meta($new_post_id, '_sale_price_dates_to', $reduction_to, true);
				add_post_meta($new_post_id, '_weight', floatval($product['weight']), true);
				add_post_meta($new_post_id, '_length', floatval($product['depth']), true);
				add_post_meta($new_post_id, '_width', floatval($product['width']), true);
				add_post_meta($new_post_id, '_height', floatval($product['height']), true);
				add_post_meta($new_post_id, '_sku', $sku, true);
				add_post_meta($new_post_id, '_stock', $quantity, true);
				add_post_meta($new_post_id, '_manage_stock', $manage_stock, true);
				add_post_meta($new_post_id, '_backorders', $backorders, true);
				add_post_meta($new_post_id, '_product_image_gallery', $gallery, true);
				add_post_meta($new_post_id, '_virtual', 'no', true);
				add_post_meta($new_post_id, '_downloadable', 'no', true);
				add_post_meta($new_post_id, 'total_sales', 0, true);
				add_post_meta($new_post_id, '_wc_review_count', 0, true);
				add_post_meta($new_post_id, '_wc_rating_count', array(), true);
				add_post_meta($new_post_id, '_wc_average_rating', 0, true);
				
				// Add the reference value
				if ( ($this->plugin_options['sku'] != 'reference') && !empty($product['reference']) ) {
					add_post_meta($new_post_id, 'reference', $product['reference'], true);
				}
				
				// Add the EAN-13 value
				if ( ($this->plugin_options['sku'] != 'ean13') && !empty($product['ean13']) ) {
					add_post_meta($new_post_id, 'ean13', $product['ean13'], true);
				}
				// Barcode
				add_post_meta($new_post_id, 'barcode', $product['ean13'], true);
				
				
				// Add links between the post and its medias
				$this->add_post_media($new_post_id, $product_medias, $date, true);
				$this->add_post_media($new_post_id, $this->get_attachment_ids($post_media), $date, false);

				// Add the PrestaShop ID as a post meta
				add_post_meta($new_post_id, '_fgp2wc_old_product_id', $product['id_product'], true);
				
				// Hook for doing other actions after inserting the post
				do_action('fgp2wc_post_insert_product', $new_post_id, $product, $language);
			}
			return $new_post_id;
		}
		
		/**
		 * Import a media by guessing its name
		 * 
		 * @param string $image_name Image name
		 * @param array $image_filenames List of potential filenames
		 * @param date $date Media creation date
		 * @param int $image_id Original image ID (optional)
		 * @return int media ID
		 */
		public function guess_import_media($image_name, $image_filenames, $date='', $image_id=0) {
			// Optimization to get the right image filename
			$media_id = false;
			if ( $this->image_filename_key !== false ) {
				$media_id = $this->import_media($image_name, $image_filenames[$this->image_filename_key], $date, array(), $image_id);
			}
			if ( $media_id === false ) {
				foreach ( $image_filenames as $key => $image_filename ) {
					if ( $key !== $this->image_filename_key ) {
						$media_id = $this->import_media($image_name, $image_filename, $date, array(), $image_id);
						if ( $media_id !== false ) {
							$this->image_filename_key = $key;
							break; // the media has been imported, we don't continue with the other potential filenames
						}
					}
				}
			}
			return $media_id;
		}
		
		/**
		 * Calculate the product prices
		 * 
		 * @since 3.4.0
		 * 
		 * @param array $product Product
		 * @return array Prices
		 */
		public function calculate_prices($product) {
			$regular_price = isset($product['price'])? floatval($product['price']): 0.0;
			
			// Special price
			$special_price = $this->calculate_special_price($regular_price, $product, 'before_tax');
			
			// Tax included
			if ( $this->plugin_options['price'] == 'with_tax' ) {
				$regular_price *= $this->global_tax_rate;
				$regular_price = round($regular_price, 4);
				if ( !empty($special_price) ) {
					$special_price *= $this->global_tax_rate;
					$special_price = round($special_price, 4);
				}
			}
			
			$special_price = $this->calculate_special_price($special_price, $product, 'after_tax');
			if ( $special_price == 0.0 ) {
				$special_price = '';
			}
			$prices = array(
				'regular_price'	=> $regular_price,
				'special_price'	=> $special_price,
				'price'			=> !empty($special_price)? $special_price: $regular_price,
			);
			
			return $prices;
		}
		
		/**
		 * Get the specific prices for a product (PrestaShop 1.4+)
		 * 
		 * @since 3.11.0
		 * 
		 * @param int $product_id Preduct ID
		 * @return array Specific prices
		 */
		protected function get_specific_prices($product_id) {
			$prices = array();
			
			if ( version_compare($this->prestashop_version, '1.4', '>=') ) {
				$prefix = $this->plugin_options['prefix'];
				if ( version_compare($this->prestashop_version, '1.5', '<') ) {
					// PrestaShop 1.4
					$product_attribute_column = '"" AS id_product_attribute';
					$specific_price_rule_criteria = '';
				} else {
					// PrestaShop 1.5
					$product_attribute_column = 'p.id_product_attribute';
					$specific_price_rule_criteria = 'AND p.id_specific_price_rule = 0';
				}
				if ( version_compare($this->prestashop_version, '1.6.1', '<') ) {
					// PrestaShop 1.6.0 and less
					$reduction_tax_field = '0 AS reduction_tax';
				} else {
					$reduction_tax_field = 'p.reduction_tax';
				}
				$sql = "
					SELECT $product_attribute_column, p.reduction, $reduction_tax_field, p.reduction_type, p.`from`, p.`to`
					FROM ${prefix}specific_price p
					WHERE p.id_product = '$product_id'
					AND (p.id_country = {$this->default_country} OR p.id_country = 0)
					AND (p.from <= NOW() OR p.from = '0000-00-00 00:00:00')
					AND (p.to >= NOW() OR p.to = '0000-00-00 00:00:00')
					$specific_price_rule_criteria
				";
				$prices = $this->prestashop_query($sql);
			}
			
			return $prices;
		}
		
		/**
		 * Calculate the special price for a product
		 * 
		 * @since 3.26.0
		 * 
		 * @param float $regular_price Regular price
		 * @param array $product Product data
		 * @param string $before_or_after_tax 'before_tax' or 'after_tax'
		 * @return float Special price
		 */
		private function calculate_special_price($regular_price, $product, $before_or_after_tax) {
			$special_price = 0.0;
			if ( version_compare($this->prestashop_version, '1.4', '<') ) {
				// PrestaShop 1.3 and less
				if ( $before_or_after_tax == 'before_tax' ) {
					if ( isset($product['reduction_price']) && !empty($product['reduction_price']) && ($product['reduction_price'] != '0.00') ) {
						$special_price = floatval($product['reduction_price']);
					} elseif ( isset($product['reduction_percent']) && !empty($product['reduction_percent']) ) {
						$special_price = $regular_price * (100 - $product['reduction_percent']) / 100;
					}
				}
			} else {
				// PrestaShop 1.4+
				if ( isset($product['id_product']) ) {
					if ( count($product['specific_prices']) > 0 ) {
						$special_price = $regular_price;
						foreach ( $product['specific_prices'] as $specific_price ) {
							if ( (($before_or_after_tax == 'before_tax') && ($specific_price['reduction_tax'] == 0)) || (($before_or_after_tax == 'after_tax') && ($specific_price['reduction_tax'] == 1)) ) {
								if ( !isset($product['id_product_attribute']) || empty($specific_price['id_product_attribute']) || ($product['id_product_attribute'] == $specific_price['id_product_attribute']) ) {
									$reduction = $specific_price['reduction'];
									if ( $specific_price['reduction_type'] == 'percentage' ) {
										$special_price *= (1 - $reduction); // Percentage reduction
									} else {
										if ( ($this->plugin_options['price'] != 'with_tax') && ($specific_price['reduction_tax'] == 1) ) {
											$reduction /= $this->global_tax_rate; // Remove the tax if the product is imported without tax and if the specific price is set with tax
										}
										$special_price -= $reduction; // Amount reduction
									}
								}
							}
						}
					}
				}
			}
			return $special_price;
		}
		
		/**
		 * Get the SKU from a product or from a product attribute
		 * 
		 * @param array $product Product or product attribute
		 * @return string SKU
		 */
		public function get_sku($product) {
			$sku = '';
			
			// SKU = Stock Keeping Unit
			switch ( $this->plugin_options['sku'] ) {
				case 'ean13':
					$sku = $product['ean13'];
					break;
				default:
					$sku = $product['reference'];
			}
			if ( empty($sku) ) {
				$sku = $product['supplier_reference'];
			}
			return $sku;
		}
		
		/**
		 * Get the product stock (quantity and backorder)
		 * 
		 * @since 3.8.2
		 * 
		 * @param int $product_id Product ID
		 * @param int $default_shop_id Default shop ID
		 * @return array Stock
		 */
		protected function get_product_stock($product_id, $default_shop_id) {
			$stock = array();
			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT s.quantity, s.out_of_stock
				FROM ${prefix}stock_available s
				WHERE s.id_product = '$product_id'
				AND s.id_product_attribute = 0
				AND (s.id_shop = '$default_shop_id' OR s.id_shop = 0)
				ORDER BY s.id_shop DESC
				LIMIT 1
			";
			$result = $this->prestashop_query($sql);
			if ( count($result) > 0 ) {
				$stock = $result[0];
			}
			
			return $stock;
		}
		
		/**
		 * Set the product visibility in WooCommerce
		 * 
		 * @since 3.27.0
		 * 
		 * @param int $new_post_id Post ID
		 * @param string $visibility PrestaShop visibility
		 */
		protected function set_product_visibility($new_post_id, $visibility) {
			switch ( $visibility ) {
				case 'catalog':
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-search'], 'product_visibility', true);
					break;
					
				case 'search':
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-catalog'], 'product_visibility', true);
					break;
					
				case 'none':
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-search'], 'product_visibility', true);
					wp_set_object_terms($new_post_id, $this->product_visibilities['exclude-from-catalog'], 'product_visibility', true);
					break;
			}
		}
		
		/**
		 * Recalculate the terms counters
		 * 
		 */
		private function recount_terms() {
			$taxonomy_names = wc_get_attribute_taxonomy_names();
			foreach ( $taxonomy_names as $taxonomy ) {
				$terms = get_terms($taxonomy, array('hide_empty' => 0));
				$termtax = array();
				foreach ( $terms as $term ) {
					$termtax[] = $term->term_taxonomy_id; 
				}
				wp_update_term_count($termtax, $taxonomy);
			}
		}
		
		/**
		 * Get PrestaShop configuration
		 *
		 * @return array of keys/values
		 */
		private function get_configuration() {
			$config = array();

			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT name, value
				FROM ${prefix}configuration
				ORDER BY id_configuration
			";

			$result = $this->prestashop_query($sql);
			foreach ( $result as $row ) {
				if ( !isset($config[$row['name']]) ) {
					$config[$row['name']] = $row['value'];
				}
			}
			return $config;
		}
		
		/**
		 * Get CMS categories
		 *
		 * @return array of Categories
		 */
		private function get_cms_categories() {
			$categories = array();
			
			if ( version_compare($this->prestashop_version, '1.4', '<') ) {
				$category_table = 'category'; // PrestaShop 1.4
				$order = 'c.id_category';
			} else {
				$category_table = 'cms_category';
				$order = 'c.position';
			}
			if ( $this->table_exists($category_table) ) {
				$prefix = $this->plugin_options['prefix'];
				$lang = $this->current_language;
				$sql = "
					SELECT c.id_${category_table} AS id_category, cl.name, cl.link_rewrite AS slug, cl.description, c.id_parent
					FROM ${prefix}${category_table} c
					INNER JOIN ${prefix}${category_table}_lang AS cl ON cl.id_${category_table} = c.id_${category_table} AND cl.id_lang = '$lang'
					WHERE c.active = 1
					ORDER BY $order
				";
				$sql = apply_filters('fgp2wc_get_cms_categories_sql', $sql, $prefix);

				$categories = $this->prestashop_query($sql);
				$categories = apply_filters('fgp2wc_get_cms_categories', $categories);
			}
			return $categories;
		}
		
		/**
		 * Get CMS articles
		 *
		 * @param int $limit Number of articles max
		 * @return array of Posts
		 */
		protected function get_cms_articles($limit=1000) {
			$articles = array();
			
			$last_prestashop_cms_id = (int)get_option('fgp2wc_last_cms_id'); // to restore the import where it left

			$prefix = $this->plugin_options['prefix'];
			$lang = $this->current_language;

			// Hooks for adding extra cols and extra joins
			$extra_cols = apply_filters('fgp2wc_get_posts_add_extra_cols', '');
			$extra_joins = apply_filters('fgp2wc_get_posts_add_extra_joins', '');

			// Index or no index
			if ( $this->column_exists('cms', 'indexation') ) {
				$indexation_field = 'a.indexation';
			} else {
				$indexation_field = ' 1 AS indexation';
			}

			if ( version_compare($this->prestashop_version, '1.4', '<') ) {
				// PrestaShop 1.3
				$sql = "
					SELECT a.id_cms, l.meta_title, l.meta_description, l.meta_keywords, l.content, l.link_rewrite AS slug, '' AS id_category, 0 AS position, 1 AS active, $indexation_field, '' AS date
					$extra_cols
					FROM ${prefix}cms a
					INNER JOIN ${prefix}cms_lang AS l ON l.id_cms = a.id_cms AND l.id_lang = '$lang'
					WHERE a.id_cms > '$last_prestashop_cms_id'
					$extra_joins
					ORDER BY a.id_cms
					LIMIT $limit
				";
			} else {
				// PrestaShop 1.4+
				$sql = "
					SELECT a.id_cms, l.meta_title, l.meta_description, l.meta_keywords, l.content, l.link_rewrite AS slug, a.id_cms_category AS id_category, a.position, a.active, $indexation_field, c.date_add AS date
					$extra_cols
					FROM ${prefix}cms a
					INNER JOIN ${prefix}cms_lang AS l ON l.id_cms = a.id_cms AND l.id_lang = '$lang'
					LEFT JOIN ${prefix}cms_category AS c ON c.id_cms_category = a.id_cms_category
					WHERE a.id_cms > '$last_prestashop_cms_id'
					$extra_joins
					ORDER BY a.id_cms
					LIMIT $limit
				";
			}
			$sql = apply_filters('fgp2wc_get_posts_sql', $sql, $prefix, $extra_cols, $extra_joins, $last_prestashop_cms_id, $limit);
			$articles = $this->prestashop_query($sql);
			
			return $articles;
		}
		
		/**
		 * Get product categories
		 *
		 * @return array of Categories
		 */
		private function get_all_product_categories() {
			$categories = array();

			$prefix = $this->plugin_options['prefix'];
			$lang = $this->current_language;
			$root_category_field = version_compare($this->prestashop_version, '1.5', '<')? '0 AS is_root_category' : 'c.is_root_category';
			
			// Exclude the parent categories of the root category
			$root_category = $this->get_root_category();
			$exclude_root_parent_categories_sql = '';
			if ( !empty($root_category) ) {
				$exclude_root_parent_categories_sql = "AND c.level_depth >= " . $root_category['level_depth'];
			}
			
			if ( version_compare($this->prestashop_version, '1.4', '<') ) {
				// PrestaShop 1.3
				$position_field = '0 AS position';
				$order = 'c.level_depth, c.id_category';
			} else {
				$position_field = 'c.position';
				$order = 'c.level_depth, c.position';
			}
			if ( version_compare($this->prestashop_version, '1.5', '<') ) {
				// PrestaShop 1.4 and less
				$shop_criteria = '';
			} else {
				$shop_criteria = 'AND cl.id_shop = c.id_shop_default';
			}
			$sql = "
				SELECT c.id_category, c.date_add AS date, $position_field, c.id_parent, $root_category_field, cl.name, cl.description, cl.link_rewrite AS slug
				FROM ${prefix}category c
				INNER JOIN ${prefix}category_lang AS cl ON cl.id_category = c.id_category AND cl.id_lang = '$lang' $shop_criteria
				WHERE c.active = 1
				$exclude_root_parent_categories_sql
				ORDER BY $order
			";
			$sql = apply_filters('fgp2wc_get_product_categories_sql', $sql, $prefix);
			$categories = $this->prestashop_query($sql);
			
			$categories = apply_filters('fgp2wc_get_product_categories', $categories);
			
			return $categories;
		}
		
		/**
		 * Get the root category if exists
		 * 
		 * @since 2.4.0
		 * 
		 * @return array Root category
		 */
		private function get_root_category() {
			$category = array();
			$prefix = $this->plugin_options['prefix'];
			if ( $this->column_exists('category', 'is_root_category') ) {
				$sql = "
					SELECT c.id_category, c.level_depth
					FROM ${prefix}category c
					WHERE c.is_root_category = 1
					LIMIT 1
				";
				$result = $this->prestashop_query($sql);
				$category = isset($result[0])? $result[0] : array();
			}
			return $category;
		}
		
		/**
		 * Get the products
		 * 
		 * @param int $limit Number of products max
		 * @return array of products
		 */
		private function get_products($limit=1000) {
			$products = array();

			$last_prestashop_product_id = (int)get_option('fgp2wc_last_product_id'); // to restore the import where it left
			
			$prefix = $this->plugin_options['prefix'];
			$lang = $this->current_language;
			$location_field = $this->column_exists('product', 'location')? 'p.location': '"" AS location';
			if ( version_compare($this->prestashop_version, '1.5', '<') ) {
				if ( version_compare($this->prestashop_version, '1.4', '<') ) {
					// PrestaShop 1.3 and less
					$width_field = '0 AS width';
					$height_field = '0 AS height';
					$depth_field = '0 AS depth';
					$reduction_fields = ', p.reduction_price, p.reduction_percent, p.reduction_from, p.reduction_to';
					$available_for_order_field = '1 AS available_for_order';
				} else {
					// PrestaShop 1.4+
					$width_field = 'p.width';
					$height_field = 'p.height';
					$depth_field = 'p.depth';
					$reduction_fields = '';
					$available_for_order_field = 'p.available_for_order';
				}
				$sql = "
					SELECT p.id_product, p.id_supplier, p.id_manufacturer, p.id_category_default, p.on_sale, p.quantity, p.price, p.wholesale_price, p.reference, p.ean13, p.supplier_reference, $location_field, $width_field, $height_field, $depth_field, p.weight, p.out_of_stock, p.active, $available_for_order_field, 'both' AS visibility, 0 AS is_virtual, p.date_add AS date, pl.name, pl.link_rewrite AS slug, pl.description, pl.description_short, pl.meta_description, pl.meta_keywords, pl.meta_title
					$reduction_fields
					FROM ${prefix}product p
					INNER JOIN ${prefix}product_lang AS pl ON pl.id_product = p.id_product AND pl.id_lang = '$lang'
				";
			} else {
				// PrestaShop 1.5+
				$sql = "
					SELECT DISTINCT p.id_product, p.id_supplier, p.id_manufacturer, p.id_category_default, p.on_sale, 0 AS quantity, p.id_shop_default, p.price, p.wholesale_price, p.reference, p.ean13, p.supplier_reference, $location_field, p.width, p.height, p.depth, p.weight, 0 AS out_of_stock, p.active, p.available_for_order, p.visibility, p.is_virtual, p.date_add AS date, pl.name, pl.link_rewrite AS slug, pl.description, pl.description_short, pl.meta_description, pl.meta_keywords, pl.meta_title
					FROM ${prefix}product p
					INNER JOIN ${prefix}product_lang AS pl ON pl.id_product = p.id_product AND pl.id_lang = '$lang' AND pl.id_shop = p.id_shop_default
				";
			}
			$sql .= "
					WHERE p.id_product > '$last_prestashop_product_id'
					ORDER BY p.id_product
					LIMIT $limit
			";
			$products = $this->prestashop_query($sql);
			
			return $products;
		}
		
		/**
		 * Get the product images
		 *
		 * @param int $product_id Product ID
		 * @return array of images
		 */
		protected function get_product_images($product_id) {
			$images = array();

			$prefix = $this->plugin_options['prefix'];
			$lang = $this->current_language;
			$sql = "
				SELECT i.id_image, i.position, i.cover, il.legend
				FROM ${prefix}image i
				LEFT JOIN ${prefix}image_lang il ON il.id_image = i.id_image AND il.id_lang = '$lang'
				WHERE i.id_product = '$product_id'
				ORDER BY i.cover DESC, i.position
			";
			$images = $this->prestashop_query($sql);
			
			return $images;
		}
		
		/**
		 * Get the categories from a product
		 *
		 * @param int $product_id PrestaShop product ID
		 * @return array of categories IDs
		 */
		protected function get_product_categories($product_id) {
			$categories = array();

			$prefix = $this->plugin_options['prefix'];
			$sql = "
				SELECT cp.id_category
				FROM ${prefix}category_product cp
				WHERE cp.id_product = $product_id
			";
			$result = $this->prestashop_query($sql);
			foreach ( $result as $row ) {
				$categories[] = $row['id_category'];
			}
			return $categories;
		}
		
		/**
		 * Get the tags from a product
		 *
		 * @param int $product_id PrestaShop product ID
		 * @return array of tags
		 */
		protected function get_product_tags($product_id) {
			$tags = array();

			$prefix = $this->plugin_options['prefix'];
			$lang = $this->current_language;
			$sql = "
				SELECT t.name
				FROM ${prefix}tag t
				INNER JOIN ${prefix}product_tag pt ON pt.id_tag = t.id_tag
				WHERE pt.id_product = $product_id
				AND t.id_lang = '$lang'
			";
			$result = $this->prestashop_query($sql);
			foreach ( $result as $row ) {
				$tags[] = $row['name'];
			}
			
			return $tags;
		}
		
		/**
		 * Get the product supplier reference (PrestaShop 1.5+)
		 *
		 * @param int $product_id PrestaShop product ID
		 * @return string Supplier reference
		 */
		protected function get_product_supplier_reference($product_id) {
			$supplier_reference = '';
			
			if ( version_compare($this->prestashop_version, '1.5', '>=') ) {
				// PrestaShop 1.5+
				$prefix = $this->plugin_options['prefix'];
				$sql = "
					SELECT ps.product_supplier_reference
					FROM ${prefix}product_supplier ps
					WHERE ps.id_product = '$product_id'
					LIMIT 1
				";
				$supplier_references = $this->prestashop_query($sql);
				if ( isset($supplier_references[0]['product_supplier_reference']) ) {
					$supplier_reference = $supplier_references[0]['product_supplier_reference'];
				}
			}
			return $supplier_reference;
		}
		
		/**
		 * Get the WooCommerce default tax rate
		 *
		 * @return float Tax rate
		 */
		protected function get_default_tax_rate() {
			global $wpdb;
			$tax = 1;
			
			try {
				$sql = "
					SELECT tax_rate
					FROM {$wpdb->prefix}woocommerce_tax_rates
					WHERE tax_rate_priority = 1
					LIMIT 1
				";
				$tax_rate = $wpdb->get_var($sql);
				if ( !empty($tax_rate) ) {
					$tax = 1 + ($tax_rate / 100);
				}
			} catch ( PDOException $e ) {
				$this->plugin->display_admin_error(__('Error:', 'fg-prestashop-to-woocommerce') . $e->getMessage());
			}
			return $tax;
		}
		
		/**
		 * Determine potential filenames for the image
		 *
		 * @param string $type Image type (category, product)
		 * @param int $id_image Image ID
		 * @param int $id_product Product ID
		 * @return string Image file name
		 */
		public function build_image_filenames($type, $id_image, $id_product='') {
			$filenames = array();
			switch ( $type ) {
				case 'category':
					$filenames[] = untrailingslashit($this->plugin_options['url']) . '/img/c/' . $id_image . '.jpg';
					$filenames[] = untrailingslashit($this->plugin_options['url']) . '/img/c/' . $id_image . '-category.jpg';
					break;
				
				case 'attribute_texture':
					$filenames[] = untrailingslashit($this->plugin_options['url']) . '/img/co/' . $id_image . '.jpg';
					break;
				
				case 'product':
					$subdirs = str_split(strval($id_image));
					$subdir = implode('/', $subdirs);
					if ( $this->plugin_options['image_size'] == 'thumbnail' ) {
						$filenames[] = untrailingslashit($this->plugin_options['url']) . '/img/p/' . $subdir . '/' . $id_image . '-thickbox_default.jpg';
					}
					$filenames[] = untrailingslashit($this->plugin_options['url']) . '/img/p/' . $subdir . '/' . $id_image . '.jpg';
					$filenames[] = untrailingslashit($this->plugin_options['url']) . '/img/p/' . $id_product . '-' . $id_image . '.jpg';
					break;
			}
			return $filenames;
		}
		
		/**
		 * Import post medias from content
		 *
		 * @param string $content post content
		 * @param date $post_date Post date (for storing media)
		 * @param array $options Options
		 * @return array:
		 * 		array media: Medias imported
		 * 		int media_count:   Medias count
		 */
		public function import_media_from_content($content, $post_date, $options=array()) {
			$media = array();
			$media_count = 0;
			$matches = array();
			$alt_matches = array();
			$title_matches = array();
			
			if ( preg_match_all('#<(img|a)(.*?)(src|href)="(.*?)"(.*?)>#', $content, $matches, PREG_SET_ORDER) > 0 ) {
				if ( is_array($matches) ) {
					foreach ($matches as $match ) {
						$filename = $match[4];
						$other_attributes = $match[2] . $match[5];
						// Image Alt
						$image_alt = '';
						if (preg_match('#alt="(.*?)"#', $other_attributes, $alt_matches) ) {
							$image_alt = wp_strip_all_tags(stripslashes($alt_matches[1]), true);
						}
						// Image caption
						$image_caption = '';
						if (preg_match('#title="(.*?)"#', $other_attributes, $title_matches) ) {
							$image_caption = $title_matches[1];
						}
						$attachment_id = $this->import_media($image_alt, $filename, $post_date, $options, 0, $image_caption);
						if ( $attachment_id !== false ) {
							$media_count++;
							$attachment = get_post($attachment_id);
							if ( !is_null($attachment) ) {
								$media[$filename] = array(
									'id'	=> $attachment_id,
									'name'	=> $attachment->post_name,
								);
							}
						}
					}
				}
			}
			return array(
				'media'			=> $media,
				'media_count'	=> $media_count
			);
		}
		
		/**
		 * Import a media
		 *
		 * @param string $name Image name
		 * @param string $filename Image URL
		 * @param date $date Date (optional)
		 * @param array $options Options (optional)
		 * @param int $image_id Original image ID (optional)
		 * @param string $image_caption Image caption
		 * @return int attachment ID or false
		 */
		public function import_media($name, $filename, $date='', $options=array(), $image_id=0, $image_caption='') {
			
			// Check if the media is already imported
			$attachment_id = $this->get_wp_post_id_from_meta('_fgp2wc_old_image_id', $image_id);
			
			if ( !$attachment_id ) {

				if ( empty($date) || ($date == '0000-00-00 00:00:00') ) {
					$date = date('Y-m-d H:i:s');
				}
				$import_external = ($this->plugin_options['import_external'] == 1) || (isset($options['force_external']) && $options['force_external'] );

				$filename = urldecode($filename); // for filenames with spaces or accents

				$filetype = wp_check_filetype($filename);
				if ( empty($filetype['type']) || ($filetype['type'] == 'text/html') ) { // Unrecognized file type
					return false;
				}

				// Upload the file from the PrestaShop web site to WordPress upload dir
				if ( preg_match('/^http/', $filename) ) {
					if ( $import_external || // External file 
						preg_match('#^' . $this->plugin_options['url'] . '#', $filename) // Local file
					) {
						$old_filename = $filename;
					} else {
						return false;
					}
				} elseif ( preg_match('#^/img#', $filename) ) {
					$old_filename = untrailingslashit($this->plugin_options['url']) . $filename;
				} else {
					$old_filename = untrailingslashit($this->plugin_options['url']) . '/img/' . $filename;
				}
				$old_filename = str_replace(" ", "%20", $old_filename); // for filenames with spaces

				// Get the upload path
				$upload_path = $this->upload_dir($filename, $date);

				// Make sure we have an uploads directory.
				if ( !wp_mkdir_p($upload_path) ) {
					$this->display_admin_error(sprintf(__("Unable to create directory %s", 'fg-prestashop-to-woocommerce'), $upload_path));
					return false;
				}

				$new_filename = $filename;
				if ( $this->plugin_options['import_duplicates'] == 1 ) {
					// Images with duplicate names
					$new_filename = preg_replace('#.*img/#', '', $new_filename);
					$new_filename = str_replace('http://', '', $new_filename);
					$new_filename = str_replace('/', '_', $new_filename);
				}

				$basename = basename($new_filename);
				$extension = substr(strrchr($basename, '.'), 1);
				$basename_without_extension = preg_replace('/(\.[^.]+)$/', '', $basename);
				$post_title = $name;
				$new_full_filename = $upload_path . '/' . $this->format_filename($basename_without_extension . '-' . $name) . '.' . $extension;

//				print "Copy \"$old_filename\" => $new_full_filename<br />";
				if ( ! @$this->remote_copy($old_filename, $new_full_filename) ) {
//					$error = error_get_last();
//					$error_message = $error['message'];
//					$this->display_admin_error("Can't copy $old_filename to $new_full_filename : $error_message");
					return false;
				}

				// Image Alt
				$image_alt = '';
				if ( !empty($name) ) {
					$image_alt = wp_strip_all_tags(stripslashes($name), true);
				}

				// GUID
				$upload_dir = wp_upload_dir();
				$guid = str_replace($upload_dir['basedir'], $upload_dir['baseurl'], $new_full_filename);
				$attachment_id = $this->insert_attachment($post_title, $basename, $new_full_filename, $guid, $date, $filetype['type'], $image_alt, $image_id, $image_caption);
			}
			
			return $attachment_id;
		}
		
		/**
		 * Format a filename
		 * 
		 * @since 3.7.3
		 * 
		 * @param string $filename Filename
		 * @return string Formated filename
		 */
		public function format_filename($filename) {
			$filename = FG_PrestaShop_to_WooCommerce_Tools::convert_to_latin($filename);
			$filename = preg_replace('/%.{2}/', '', $filename); // Remove the encoded characters
			$filename = sanitize_file_name($filename);
			return $filename;
		}
		
		/**
		 * Returns the imported post ID corresponding to a meta key and value
		 *
		 * @since 3.3.0
		 * 
		 * @param string $meta_key Meta key
		 * @param string $meta_value Meta value
		 * @return int WordPress post ID
		 */
		public function get_wp_post_id_from_meta($meta_key, $meta_value) {
			global $wpdb;

			$sql = "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '$meta_key' AND meta_value = '$meta_value' LIMIT 1";
			$post_id = $wpdb->get_var($sql);
			return $post_id;
		}

		/**
		 * Determine the media upload directory
		 * 
		 * @since 2.3.0
		 * 
		 * @param string $filename Filename
		 * @param date $date Date
		 * @return string Upload directory
		 */
		public function upload_dir($filename, $date) {
			$upload_dir = wp_upload_dir(strftime('%Y/%m', strtotime($date)));
			$use_yearmonth_folders = get_option('uploads_use_yearmonth_folders');
			if ( $use_yearmonth_folders ) {
				$upload_path = $upload_dir['path'];
			} else {
				$short_filename = preg_replace('#.*img/#', '/', $filename);
				if ( strpos($short_filename, '/') != 0 ) {
					$short_filename = '/' . $short_filename; // Add a slash before the filename
				}
				$upload_path = $upload_dir['basedir'] . untrailingslashit(dirname($short_filename));
			}
			return $upload_path;
		}
		
		/**
		 * Save the attachment and generates its metadata
		 * 
		 * @since 2.3.0
		 * 
		 * @param string $attachment_title Attachment name
		 * @param string $basename Original attachment filename
		 * @param string $new_full_filename New attachment filename with path
		 * @param string $guid GUID
		 * @param date $date Date
		 * @param string $filetype File type
		 * @param string $image_alt Image description
		 * @param int $image_id Image ID
		 * @param string $image_caption Image caption
		 * @return int|false Attachment ID or false
		 */
		public function insert_attachment($attachment_title, $basename, $new_full_filename, $guid, $date, $filetype, $image_alt='', $image_id=0, $image_caption='') {
			$post_name = sanitize_title($attachment_title);
			
			// If the attachment does not exist yet, insert it in the database
			$attachment_id = 0;
			$attachment = $this->get_attachment_from_name($post_name);
			if ( $attachment ) {
				$attached_file = basename(get_attached_file($attachment->ID));
				if ( $attached_file == $basename ) { // Check if the filename is the same (in case where the legend is not unique)
					$attachment_id = $attachment->ID;
				}
			}
			if ( $attachment_id == 0 ) {
				$attachment_data = array(
					'guid'				=> $guid, 
					'post_date'			=> $date,
					'post_mime_type'	=> $filetype,
					'post_name'			=> $post_name,
					'post_title'		=> $attachment_title,
					'post_status'		=> 'inherit',
					'post_content'		=> '',
					'post_excerpt'		=> $image_caption,
				);
				$attachment_id = wp_insert_attachment($attachment_data, $new_full_filename);
				if ( !empty($image_id) ) {
					add_post_meta($attachment_id, '_fgp2wc_old_image_id', $image_id, true);
				} else {
					add_post_meta($attachment_id, '_fgp2wc_imported', 1, true); // To delete the imported attachments
				}
			}
			
			if ( !empty($attachment_id) ) {
				if ( preg_match('/(image|audio|video)/', $filetype) ) { // Image, audio or video
					// you must first include the image.php file
					// for the function wp_generate_attachment_metadata() to work
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attach_data = wp_generate_attachment_metadata( $attachment_id, $new_full_filename );
					wp_update_attachment_metadata($attachment_id, $attach_data);

					// Image Alt
					if ( !empty($image_alt) ) {
						update_post_meta($attachment_id, '_wp_attachment_image_alt', addslashes($image_alt)); // update_post_meta expects slashed
					}
				}
				return $attachment_id;
			} else {
				return false;
			}
		}
		
		/**
		 * Check if the attachment exists in the database
		 *
		 * @param string $name
		 * @return object Post
		 */
		private function get_attachment_from_name($name) {
			$name = preg_replace('/\.[^.]+$/', '', basename($name));
			$r = array(
				'name'			=> $name,
				'post_type'		=> 'attachment',
				'numberposts'	=> 1,
			);
			$posts_array = get_posts($r);
			if ( is_array($posts_array) && (count($posts_array) > 0) ) {
				return $posts_array[0];
			}
			else {
				return false;
			}
		}
		
		/**
		 * Process the post content
		 *
		 * @param string $content Post content
		 * @param array $post_media Post medias
		 * @return string Processed post content
		 */
		public function process_content($content, $post_media) {
			
			if ( !empty($content) ) {
				$content = str_replace(array("\r", "\n"), array('', ' '), $content);
				
				// Replace page breaks
				$content = preg_replace("#<hr([^>]*?)class=\"system-pagebreak\"(.*?)/>#", "<!--nextpage-->", $content);
				
				// Replace media URLs with the new URLs
				$content = $this->process_content_media_links($content, $post_media);
			}

			return $content;
		}

		/**
		 * Replace media URLs with the new URLs
		 *
		 * @param string $content Post content
		 * @param array $post_media Post medias
		 * @return string Processed post content
		 */
		private function process_content_media_links($content, $post_media) {
			$matches = array();
			$matches_caption = array();
			
			if ( is_array($post_media) ) {
				
				// Get the attachments attributes
				$attachments_found = false;
				foreach ( $post_media as $old_filename => &$media_var ) {
					$post_media_name = $media_var['name'];
					$attachment = $this->get_attachment_from_name($post_media_name);
					if ( $attachment ) {
						$media_var['attachment_id'] = $attachment->ID;
						$media_var['url_old_filename'] = urlencode($old_filename); // for filenames with spaces
						if ( preg_match('/image/', $attachment->post_mime_type) ) {
							// Image
							$image_src = wp_get_attachment_image_src($attachment->ID, 'full');
							$media_var['new_url'] = $image_src[0];
							$media_var['width'] = $image_src[1];
							$media_var['height'] = $image_src[2];
						} else {
							// Other media
							$media_var['new_url'] = wp_get_attachment_url($attachment->ID);
						}
						$attachments_found = true;
					}
				}
				if ( $attachments_found ) {
				
					// Remove the links from the content
					$this->post_link_count = 0;
					$this->post_link = array();
					$content = preg_replace_callback('#<(a) (.*?)(href)=(.*?)</a>#i', array($this, 'remove_links'), $content);
					$content = preg_replace_callback('#<(img) (.*?)(src)=(.*?)>#i', array($this, 'remove_links'), $content);
					
					// Process the stored medias links
					$first_image_removed = false;
					foreach ($this->post_link as &$link) {
						
						// Remove the first image from the content
						if ( ($this->plugin_options['first_image'] == 'as_featured') && !$first_image_removed && preg_match('#^<img#', $link['old_link']) ) {
							$link['new_link'] = '';
							$first_image_removed = true;
							continue;
						}
						$new_link = $link['old_link'];
						$alignment = '';
						if ( preg_match('/(align="|float: )(left|right)/', $new_link, $matches) ) {
							$alignment = 'align' . $matches[2];
						}
						if ( preg_match_all('#(src|href)="(.*?)"#i', $new_link, $matches, PREG_SET_ORDER) ) {
							$caption = '';
							foreach ( $matches as $match ) {
								$old_filename = $match[2];
								$link_type = ($match[1] == 'src')? 'img': 'a';
								if ( array_key_exists($old_filename, $post_media) ) {
									$media = $post_media[$old_filename];
									if ( array_key_exists('new_url', $media) ) {
										if ( (strpos($new_link, $old_filename) > 0) || (strpos($new_link, $media['url_old_filename']) > 0) ) {
											$new_link = preg_replace('#('.$old_filename.'|'.$media['url_old_filename'].')#', $media['new_url'], $new_link, 1);
											
											if ( $link_type == 'img' ) { // images only
												// Define the width and the height of the image if it isn't defined yet
												if ((strpos($new_link, 'width=') === false) && (strpos($new_link, 'height=') === false)) {
													$width_assertion = isset($media['width'])? ' width="' . $media['width'] . '"' : '';
													$height_assertion = isset($media['height'])? ' height="' . $media['height'] . '"' : '';
												} else {
													$width_assertion = '';
													$height_assertion = '';
												}
												
												// Caption shortcode
												if ( preg_match('/class=".*caption.*?"/', $link['old_link']) ) {
													if ( preg_match('/title="(.*?)"/', $link['old_link'], $matches_caption) ) {
														$caption_value = str_replace('%', '%%', $matches_caption[1]);
														$align_value = ($alignment != '')? $alignment : 'alignnone';
														$caption = '[caption id="attachment_' . $media['attachment_id'] . '" align="' . $align_value . '"' . $width_assertion . ']%s' . $caption_value . '[/caption]';
													}
												}
												
												$align_class = ($alignment != '')? $alignment . ' ' : '';
												$new_link = preg_replace('#<img(.*?)( class="(.*?)")?(.*) />#', "<img$1 class=\"$3 " . $align_class . 'size-full wp-image-' . $media['attachment_id'] . "\"$4" . $width_assertion . $height_assertion . ' />', $new_link);
											}
										}
									}
								}
							}
							
							// Add the caption
							if ( $caption != '' ) {
								$new_link = sprintf($caption, $new_link);
							}
						}
						$link['new_link'] = $new_link;
					}
					
					// Reinsert the converted medias links
					$content = preg_replace_callback('#__fg_link_(\d+)__#', array($this, 'restore_links'), $content);
				}
			}
			return $content;
		}
		
		/**
		 * Remove all the links from the content and replace them with a specific tag
		 * 
		 * @param array $matches Result of the preg_match
		 * @return string Replacement
		 */
		private function remove_links($matches) {
			$this->post_link[] = array('old_link' => $matches[0]);
			return '__fg_link_' . $this->post_link_count++ . '__';
		}
		
		/**
		 * Restore the links in the content and replace them with the new calculated link
		 * 
		 * @param array $matches Result of the preg_match
		 * @return string Replacement
		 */
		private function restore_links($matches) {
			$link = $this->post_link[$matches[1]];
			$new_link = array_key_exists('new_link', $link)? $link['new_link'] : $link['old_link'];
			return $new_link;
		}
		
		/**
		 * Add a link between a media and a post (parent id + thumbnail)
		 *
		 * @param int $post_id Post ID
		 * @param array $post_media Post medias
		 * @param array $date Date
		 * @param boolean $set_featured_image Set the featured image?
		 */
		public function add_post_media($post_id, $post_media, $date, $set_featured_image=true) {
			$thumbnail_is_set = false;
			if ( is_array($post_media) ) {
				foreach ( $post_media as $media ) {
					$attachment = get_post($media);
					if ( !empty($attachment) && ($attachment->post_type == 'attachment') ) {
						$attachment->post_parent = $post_id; // Attach the post to the media
						$attachment->post_date = $date ;// Define the media's date
						wp_update_post($attachment);

						// Set the featured image. If not defined, it is the first image of the content.
						if ( $set_featured_image && !$thumbnail_is_set ) {
							set_post_thumbnail($post_id, $attachment->ID);
							$thumbnail_is_set = true;
						}
					}
				}
			}
		}

		/**
		 * Get the IDs of the medias
		 *
		 * @param array $post_media Post medias
		 * @return array Array of attachment IDs
		 */
		public function get_attachment_ids($post_media) {
			$attachments_ids = array();
			if ( is_array($post_media) ) {
				foreach ( $post_media as $media ) {
					$attachment = $this->get_attachment_from_name($media['name']);
					if ( !empty($attachment) ) {
						$attachments_ids[] = $attachment->ID;
					}
				}
			}
			return $attachments_ids;
		}
		
		/**
		 * Copy a remote file
		 * in replacement of the copy function
		 * 
		 * @param string $url URL of the source file
		 * @param string $path destination file
		 * @return boolean
		 */
		public function remote_copy($url, $path) {
			
			// Don't copy the file if already copied
			if ( !$this->plugin_options['force_media_import'] && file_exists($path) && (filesize($path) > 0) ) {
				return true;
			}
			
			$response = wp_remote_get($url, array(
				'timeout'		=> $this->plugin_options['timeout'],
				'sslverify'		=> false,
				'redirection'	=> 0,
				'user-agent'	=> 'Mozilla/5.0 AppleWebKit (KHTML, like Gecko) Chrome/ Safari/', // the default "WordPress..." user agent is rejected with some NGINX config
			)); // Uses WordPress HTTP API
			
			if ( is_wp_error($response) ) {
				trigger_error($response->get_error_message(), E_USER_WARNING);
				return false;
			} elseif ( $response['response']['code'] != 200 ) {
				trigger_error($response['response']['message'], E_USER_WARNING);
				return false;
			} else {
				file_put_contents($path, wp_remote_retrieve_body($response));
				return true;
			}
		}
		
		/**
		 * Allow the backorders or not
		 * 
		 * @param int $out_of_stock_value Out of stock value 0|1|2
		 * @return string yes|no
		 */
		protected function allow_backorders($out_of_stock_value) {
			switch ( $out_of_stock_value ) {
				case 0: $backorders = 'no'; break;
				case 1: $backorders = 'yes'; break;
				default: $backorders = $this->default_backorders;
			}
			return $backorders;
		}
		
		/**
		 * Recount the items for a taxonomy
		 * 
		 * @return boolean
		 */
		private function terms_tax_count($taxonomy) {
			$terms = get_terms(array($taxonomy));
			// Get the term taxonomies
			$terms_taxonomies = array();
			foreach ( $terms as $term ) {
				$terms_taxonomies[] = $term->term_taxonomy_id;
			}
			if ( !empty($terms_taxonomies) ) {
				return wp_update_term_count_now($terms_taxonomies, $taxonomy);
			} else {
				return true;
			}
		}
		
		/**
		 * Recount the items for each category and tag
		 * 
		 * @return boolean
		 */
		private function terms_count() {
			$result = $this->terms_tax_count('category');
			$result |= $this->terms_tax_count('post_tag');
		}
		
		/**
		 * Display the number of imported media
		 * 
		 */
		public function display_media_count() {
			$this->display_admin_notice(sprintf(_n('%d media imported', '%d medias imported', $this->media_count, 'fg-prestashop-to-woocommerce'), $this->media_count));
		}

		/**
		 * Test if a column exists
		 *
		 * @param string $table Table name
		 * @param string $column Column name
		 * @return bool
		 */
		public function column_exists($table, $column) {
			global $prestashop_db;
			
			try {
				$prefix = $this->plugin_options['prefix'];
				
				$sql = "SHOW COLUMNS FROM ${prefix}${table} LIKE '$column'";
				$query = $prestashop_db->query($sql, PDO::FETCH_ASSOC);
				$result = $query->fetch();
				return !empty($result);
			} catch ( PDOException $e ) {}
			return false;
		}
		
		/**
		 * Test if a table exists
		 *
		 * @param string $table Table name
		 * @return bool
		 */
		public function table_exists($table) {
			global $prestashop_db;
			
			try {
				$prefix = $this->plugin_options['prefix'];
				
				$sql = "SHOW TABLES LIKE '${prefix}${table}'";
				$query = $prestashop_db->query($sql, PDO::FETCH_ASSOC);
				$result = $query->fetch();
				return !empty($result);
			} catch ( PDOException $e ) {}
			return false;
		}
		
		/**
		 * Get all the term metas corresponding to a meta key
		 * 
		 * @param string $meta_key Meta key
		 * @return array List of term metas: term_id => meta_value
		 */
		public function get_term_metas_by_metakey($meta_key) {
			global $wpdb;
			$metas = array();
			
			$sql = "SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key = '$meta_key'";
			$results = $wpdb->get_results($sql);
			foreach ( $results as $result ) {
				$metas[$result->meta_value] = $result->term_id;
			}
			ksort($metas);
			return $metas;
		}
		
		/**
		 * Returns the imported product ID corresponding to a PrestaShop ID
		 *
		 * @since 3.22.0
		 * 
		 * @param int $ps_product_id PrestaShop product ID
		 * @return int WordPress product ID
		 */
		public function get_wp_product_id_from_prestashop_id($ps_product_id) {
			$product_id = $this->get_wp_post_id_from_meta('_fgp2wc_old_product_id', $ps_product_id);
			return $product_id;
		}
		
	}
}

<?php

/**
 * The Progress Bar
 *
 * @link       https://wordpress.org/plugins/fg-prestashop-to-woocommerce/
 * @since      3.0.0
 *
 * @package    FG_PrestaShop_to_WooCommerce
 * @subpackage FG_PrestaShop_to_WooCommerce/admin
 */

if ( !class_exists('FG_PrestaShop_to_WooCommerce_ProgressBar', false) ) {

	/**
	 * The Progress Bar class
	 *
	 * @package    FG_PrestaShop_to_WooCommerce
	 * @subpackage FG_PrestaShop_to_WooCommerce/admin
	 * @author     Frédéric GILLES
	 */
	class FG_PrestaShop_to_WooCommerce_ProgressBar {
		
		private $plugin;
		private $total_count = 0;
		private $current_count = 0;
		private $filename;
		private $url;
		
		/**
		 * Initialize the class and set its properties.
		 *
		 * @param    FG_PrestaShop_to_WooCommerce_Admin    $plugin       Admin plugin
		 */
		public function __construct($plugin) {
			$this->plugin = $plugin;
			$upload_dir = wp_upload_dir();
			$filename = $this->plugin->get_plugin_name() . '-progress.json';
			$this->filename = $upload_dir['basedir'] . '/' . $filename;
			$this->url = $upload_dir['baseurl'] . '/' . $filename;
			$counters = $this->read_progress();
			if ( isset($counters->total) ) {
				$this->total_count = $counters->total;
			}
			if ( isset($counters->current) ) {
				$this->current_count = $counters->current;
			}
		}
		
		/**
		 * Get the progress file URL
		 * 
		 * @return string Progress file URL
		 */
		public function get_url() {
			return $this->url;
		}
		
		/**
		 * Read the progress counters
		 * 
		 * @return array|false Array of counters
		 */
		private function read_progress() {
			if ( file_exists($this->filename) ) {
				$json_content = file_get_contents($this->filename);
				return json_decode($json_content);
			} else {
				return false;
			}
		}
		
		/**
		 * Set the total count
		 * 
		 * @param int $count Count
		 */
		public function set_total_count($count) {
			if ( $count != $this->total_count ) {
				$this->total_count = $count;
				$this->current_count = 0;
				$this->save_progress();
			}
		}
		
		/**
		 * Increment the current count
		 * 
		 * @param int $count Count
		 */
		public function increment_current_count($count) {
			$this->current_count += $count;
			$this->save_progress();
		}
		
		/**
		 * Save the progress counters
		 * 
		 */
		private function save_progress() {
			file_put_contents($this->filename, json_encode(array(
				'total'		=> $this->total_count,
				'current'	=> $this->current_count,
			)));
			
		}
	}
}

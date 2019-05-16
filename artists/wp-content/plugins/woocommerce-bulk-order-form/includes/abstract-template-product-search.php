<?php
/**
 * The admin-specific functionality of the plugin.
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) { die; }

abstract class WooCommerce_Bulk_Order_Form_Template_Product_Search {
	public $type = ''; # Type of the current template
	public static $founded_post = '';
	private static $query_obj = '';
	private static $post_args = array();
	
	public function __construct() {
		$this->set_default_args();
	}
	public function _clear_defaults(){
		self::$post_args = array();
		$this->set_default_args();
	}
	
	/**
	 * Sets Default Args of search Query
	 */
	public function set_default_args($posttypes = array('product','product_variation')){
		self::$post_args['post_type'] = $posttypes;
		self::$post_args['nopaging'] = true;
		self::$post_args['suppress_filters'] = false;
		self::$post_args['update_post_term_cache'] = false;
		self::$post_args['update_post_meta_cache'] = false;
		self::$post_args['cache_results']          = false;
		self::$post_args['no_found_rows']          = true;
		self::$post_args['fields']  = 'ids';
	}
	public function set_search_args($args){
		if(!empty($args)) {
			self::$post_args = $args;
			return true;
		}
		return false;
	}
	public function set_post_type($post_type = ''){
		if(!in_array($post_type,self::$post_args['post_type'])){
			self::$post_args['post_type'][] = $post_type;
		}
	}
	public function set_search_by_title_query($type = 'add'){
		if($type == 'add'){
			add_filter( 'posts_search', array($this, 'search_by_title_init'), 10, 2 );
		} else {
			remove_filter( 'posts_search', array($this, 'search_by_title_init'), 10);
		}
		
	}
	public function set_category($cats = array(),$field = 'id'){
		$terms = array();
		foreach ($cats as $key => $value) {
			if ( is_numeric($value) && $term = get_term_by('id', $value, 'product_cat') ) {
				$terms[] = $value;
			} elseif ( $term = get_term_by('name', $value, 'product_cat') ) {
				$terms[] = (int) $term->term_id;
			} elseif ( $term = get_term_by('slug', $value, 'product_cat') ) {
				$terms[] = (int) $term->term_id;
			}
		}

		self::$post_args['tax_query'][] = array(
			'taxonomy' 	=> 'product_cat',
			'field'    	=> $field,
			'terms'    	=> $terms,
		);
	}
	public function set_excludes($ids = array()){
		self::$post_args['post__not_in'] = $ids;
	}
	public function set_includes($ids = array()){
		self::$post_args['post__in'] = $ids;
	}
	public function set_post_per_page($num){
		self::$post_args['posts_per_page'] = $num;
	
	}
	public function set_post_parent($ids = array()){
		self::$post_args['post_parent'] = $ids;
	}
	public function set_meta_query($query = array()){
		self::$post_args['meta_query'][] = $query;
	}
	public function set_sku_search($term){
		$this->set_meta_query(array( 'key'=> '_sku', 'value'=> '^'.$term, 'compare'=> 'REGEXP' ));
	}
	public function set_search_query ($s = ''){
		self::$post_args['s'] = $s;
	}
		
	public function set_orderby($order_by){
		self::$post_args['orderby'] = $order_by;
	}
	
	public function set_order($order){
		self::$post_args['order'] = $order;
	}
	
	/**
	 * Returns the product output title stucture
	 * @use [sku] -> To get products sku
	 * @use [title] -> To get products title
	 * @use [price] -> To get products price
	 * @return boolean  [[Description]]
	 */
	public function get_output_title_format($key = 'TPS',$separator = ' - ',$use_filter = true){
		$title = wc_bof_get_title_templates();
		$return = false; 
		if(isset($title[$key])){ $return = $title[$key]; }
		if($use_filter){ $return = apply_filters('wc_bof_output_title',$return,$this->type); }
		return $return;
	}
	public function get_output_title($key = 'TPS',$separator = ' - ',$name,$price,$sku){
		$return = array(); 

		if($key == 'STP'){ 
			if(!empty($sku)){$return[] = $sku;}
			if(!empty($name)){$return[] = $name;}
			if(!empty($price)){$return[] = wc_price($price);}
		} 
		else if($key == 'TPS'){ 
			if(!empty($name)){$return[] = $name;}
			if(!empty($price)){$return[] = wc_price($price);}
			if(!empty($sku)){$return[] = $sku;}
		}
		else if($key == 'TP'){ 
			if(!empty($name)){$return[] = $name;}
			if(!empty($price)){$return[] = wc_price($price);}
		}
		else if($key == 'TS'){
			if(!empty($name)){$return[] = $name;}
			if(!empty($sku)){$return[] = $sku;}			
		}
		else if($key == 'T'){
			if(!empty($name)){$return[] = $name;}
		}
		return implode($separator,$return);
	}
	
	/*
	 * Returns all search args
	 */
	
	public function get_search_args(){
		return self::$post_args;
	}
	
	public function get_products(){
		$search_args = $this->get_search_args(); 
		$search_args = apply_filters("wc_bof_product_search_args",$search_args,$this->type);
		$posts = get_posts($search_args);
		foreach ($posts as $key => $post) {
			$product = wc_bof_get_product( $post );
			$product_type = method_exists($product, 'get_type') ? $product->get_type() : $product->product_type;
			if ($product_type == 'external') {
				unset($posts[$key]);
			}
		}
		if ( $posts && isset($search_args['posts_per_page']) && $search_args['posts_per_page'] != '-1' && count($posts) > $search_args['posts_per_page'] ) {
			$posts = array_slice($posts, 0, $search_args['posts_per_page']);
		}
		self::$founded_post = $posts;
		return self::$founded_post;
	}
	
	public function search_by_title_init( $search, $wp_query ) {
		if ( ! empty( $search ) && ! empty( $wp_query->query_vars['search_terms'] ) ) {
			global $wpdb;
			$q = $wp_query->query_vars;
			$n = ! empty( $q['exact'] ) ? '' : '%';
			$search = array();

			foreach ( ( array ) $q['search_terms'] as $term )
				$search[] = $wpdb->prepare( "$wpdb->posts.post_title LIKE %s", $n . $wpdb->esc_like( $term ) . $n );

			if ( ! is_user_logged_in() )
				$search[] = "$wpdb->posts.post_password = ''";

			$search = ' AND ' . implode( ' AND ', $search );
		}
		return $search;
	}	
	
	public function get_product_title($id){
		$title = get_the_title($id);
		return html_entity_decode($title, ENT_COMPAT, 'UTF-8');
	}
	
	public function get_product_image($id,$forceFilter = true){
		if ( wc_bof_option( 'show_image' ) ) {
			$settings = get_option( 'wc_bof_general', array() );
			if ( !empty( $settings['wc_bof_image_width'] ) && !empty( $settings['wc_bof_image_height'] ) ) {
				$size = array( intval( $settings['wc_bof_image_width'] ), intval( $settings['wc_bof_image_height'] ) );
			} else {
				$size = 'shop_thumbnail';
			}

			if ( has_post_thumbnail( $id ) ) {
				$img = get_the_post_thumbnail_url( $id, $size );
			} elseif ( ( $parent_id = wp_get_post_parent_id( $id ) ) && has_post_thumbnail( $parent_id ) ) {
				$img = get_the_post_thumbnail_url( $parent_id, $size );
			} elseif ( function_exists('wc_placeholder_img_src') ) {
				$img = wc_placeholder_img_src( $size );
			} else {
				$img = apply_filters( 'woocommerce_placeholder_img_src', '' );
			}
		} else {
			return '';
		}

		return $img;
	}
}
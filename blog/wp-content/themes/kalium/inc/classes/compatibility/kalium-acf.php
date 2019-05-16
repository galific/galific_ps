<?php
/**
 *    40 ACF fallback for "get_field"
 *
 *    Laborator.co
 *    www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_ACF {

	/**
	 * ACF plugin is active
	 */
	public $acf_installed = false;

	/**
	 * Construct
	 */
	public function __construct() {
		$this->acf_installed = function_exists( 'get_field' );
	}

	/**
	 * Get field with fallback function
	 */
	public function get_field( $field_key, $post_id = false, $format_value = true ) {
		global $post;

		if ( $this->acf_installed ) {
			return get_field( $field_key, $post_id, $format_value );
		}

		// Get raw field from post
		if ( ! $post_id && $post instanceof WP_Post ) {
			$post_id = $post->ID;
		}

		if ( $post_id ) {
			return get_post_meta( $post_id, $field_key, true );
		}

		return null;
	}
}
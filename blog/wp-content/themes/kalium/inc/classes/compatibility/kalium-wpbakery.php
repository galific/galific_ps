<?php
/**
 *	Kalium WordPress Theme
 *
 *	Laborator.co
 *	www.laborator.co
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

class Kalium_WPBakery {

	/**
	 * Required plugin/s for this class
	 */
	public static $plugins = array( 'js_composer/js_composer.php' );

	/**
	 * Registered lightbox items to parse as JavaScript
	 */
	public $lightbox_items = array();

	/**
	 * Class instructor, define necesarry actions
	 */
	public function __construct() {
		$this->template_redirect_priority = 100;

		// Row wrapper
		add_filter( 'vc_shortcode_output', array( $this, 'vcRow' ), 100, 3 );

		// Inner row full-width support
		add_filter( 'vc_after_init', array( $this, 'vcInnerRowParams' ), 100 );
		add_filter( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, array( $this, 'vcInnerRowClass' ), 100, 3 );

		// Lightbox support for single and gallery images elements
		add_action( 'vc_after_init', array( $this, 'vcLightboxOptionForImageElements' ) );
		add_filter( 'vc_shortcode_output', array( $this, 'registerLightboxItems' ), 100, 3 );
		add_action( 'wp_footer', array( $this, 'parseRegisteredLightboxItems' ) );

		// Lightbox support for grid items
		add_filter( 'vc_gitem_add_link_param', array( $this, 'vcGridItemLightboxOption' ) );
		add_filter( 'vc_gitem_zone_image_block_link', array( $this, 'vcGridItemLightboxLink' ), 100, 3 );
	}

	/**
	 * Row wrapper
	 *
	 * @type filter
	 */
	public function vcRow( $output, $object, $atts ) {

		static $use_container;

		if ( ! isset( $use_container ) ) {
			$use_container = ! is_singular( 'post' );

			// In portfolio it is not allowed as well, only in WPBakery Portfolio item type
			if ( is_singular( 'portfolio' ) ) {
				$use_container = 'type-7' == kalium_get_field( 'item_type' );
			}
		}

		// VC Section and Row
		if ( in_array( $object->settings( 'base' ), array( 'vc_section', 'vc_row' ) ) ) {
			$row_container_classes = array( 'vc-row-container' );

			// Row width
			if ( empty( $atts['full_width'] ) ) {

				// Applied to valid pages or post types only
				if ( $use_container ) {
					$row_container_classes[] = 'container';
				}
			}
			// Stretch row
			else if ( 'stretch_row' == $atts['full_width'] ) {

				// Applied to valid pages or post types only
				if ( $use_container ) {
					$row_container_classes[] = 'container';
				}
			}
			// Stretch row and content
			else if ( 'stretch_row_content' == $atts['full_width'] ) {
				$row_container_classes[] = 'vc-row-container--stretch-content';
			}
			// Stretch row and content (no spaces)
			else if ( 'stretch_row_content_no_spaces' == $atts['full_width'] ) {
				$row_container_classes[] = 'vc-row-container--stretch-content-no-spaces';
			}

			// Custom classes
			if ( ! empty( $atts['el_class'] ) ) {
				$classes = explode( ' ', $atts['el_class'] );

				foreach ( $classes as $class ) {
					$row_container_classes[] = "parent--{$class}";
				}
			}

			// Wrap the row
			$output = sprintf( '<div class="%2$s">%1$s</div>', $output, kalium()->helpers->showClasses( $row_container_classes ) );
		}

		return $output;
	}

	/**
	 * Inner row params
	 *
	 * @type action
	 */
	public function vcInnerRowParams() {
		$container_type = array(
			'type' => 'dropdown',
			'heading' => 'Container type',
			'param_name' => 'container_type',
			'std' => 'fixed',
			'value' => array(
				'Fluid container' => 'fluid',
				'Fixed container'  => 'fixed',
			),
			'description' => 'Fluid container will expand to 100% of column size, while fixed container will keep defined screen sizes and aligned on center.',
			'weight' => 1
		);

		vc_add_param( 'vc_row_inner', $container_type );
	}

	/**
	 * Inner row class
	 *
	 * @type filter
	 */
	public function vcInnerRowClass( $classes, $base = '', $atts = array() ) {

		// Row stretch class
		if ( 'vc_row' == $base ) {

			// Stretched row
			if ( ! empty( $atts['full_width'] ) && 'stretch_row' == $atts['full_width'] ) {
				$classes .= ' row-stretch';

			}
		}
		// Inner row
		elseif ( 'vc_row_inner' == $base ) {

			// Fixed container
			if ( empty( $atts['container_type'] ) || 'fixed' == $atts['container_type'] ) {

				// Applied to pages only
				if ( is_page() ) {
					$classes .= ' container-fixed';
				}
			}
		}

		return $classes;
	}

	/**
	 * Lightbox option for Single Image, Gallery Images and Images carousel elements
	 *
	 * @type action
	 */
	public function vcLightboxOptionForImageElements() {
		// Elements that support this "action" attribute
		foreach ( array( 'vc_single_image' => 'onclick', 'vc_gallery' => 'onclick', 'vc_images_carousel' => 'onclick' ) as $element_id => $attribute_id ) {
			$param = WPBMap::getParam( $element_id, $attribute_id );

			// Add to select list
			if ( ! empty( $param ) && is_array( $param ) ) {
				$param['value'][ 'Open in theme default lightbox (use Kalium\'s built-in lightbox for videos and images)' ] = 'kalium_lightbox';
				vc_update_shortcode_param( $element_id, $param );
			}
		}
	}

	/**
	 * Add lightbox param as link for grid items
	 *
	 * @type filter
	 */
	public function vcGridItemLightboxOption( $param ) {
		$param['value'][ 'Open in theme default lightbox (use Kalium\'s built-in lightbox for videos and images)' ] = 'kalium_lightbox';
		return $param;
	}

	/**
	 * Grid item lightbox link
	 *
	 * @type filter
	 */
	public function vcGridItemLightboxLink( $image_block, $link, $css_class ) {

		if ( 'kalium_lightbox' === $link ) {
			// Lightbox library
			kalium_enqueue_lightbox_library();

			$css_class .= ' kalium-lightbox-entry';
			return '<a {{ post_image_url_href }} class="' . esc_attr(  $css_class  ) . '" title="{{ title }}"></a>';
		}

		return $image_block;
	}

	/**
	 * Register lightbox items to use with theme default lightbox
	 *
	 * @type filter
	 */
	public function registerLightboxItems( $output, $object, $atts ) {
		$base = $object->settings( 'base' );

		static $container_id = 1;

		// Single image
		if ( 'vc_single_image' == $base && ! empty( $atts['image'] ) && 'kalium_lightbox' == get_array_key( $atts, 'onclick' ) ) {
			$this->lightbox_items[] = array(
				'container' => $container_id,
				'tag' => $base,
				'image' => wp_get_attachment_image_url( $atts['image'], 'original' )
			);

			$output = preg_replace( '#\<div# ', '<div data-lightbox-container="' . $container_id . '" ', $output, 1 );
			$container_id++;
		}
		// Gallery images
		else if ( ( 'vc_gallery' == $base || 'vc_images_carousel' == $base ) && ! empty( $atts['images'] ) && 'kalium_lightbox' == get_array_key( $atts, 'onclick' ) ) {
			$images = array();

			foreach ( explode( ',', $atts['images'] ) as $attachment_id ) {
				$images[] = wp_get_attachment_image_url( $attachment_id, 'original' );
			}

			$this->lightbox_items[] = array(
				'container' => $container_id,
				'tag' => $base,
				'images' => $images
			);

			$output = preg_replace( '#\<div# ', '<div data-lightbox-container="' . $container_id . '" ', $output, 1 );
			$container_id++;
		}

		return $output;
	}

	/**
	 * Parse registered items for
	 */
	public function parseRegisteredLightboxItems() {

		if ( ! empty( $this->lightbox_items ) ) {

			// Lightbox library
			kalium_enqueue_lightbox_library();

			// Entries global
			kalium_define_js_variable( 'kalium_wpb_lightbox_items', $this->lightbox_items );
		}
	}

	/**
	 * Deregister isotope
	 *
	 * @type action
	 */
	public function template_redirect() {
		wp_deregister_script( 'isotope' );
	}
}

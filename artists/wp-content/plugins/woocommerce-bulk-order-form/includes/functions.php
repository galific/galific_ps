<?php
/**
 * Common Plugin Functions
 *
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/core
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}


global $wc_bof_db_settins_values;
$wc_bof_db_settins_values = array();
add_action( 'wc_bof_loaded', 'wc_bof_get_settings_from_db', 1 );

if ( ! function_exists( 'wc_bof_option' ) ) {
	function wc_bof_option( $key = '', $default = false ) {
		global $wc_bof_db_settins_values;
		if ( '' === $key ) {
			return $wc_bof_db_settins_values;
		}
		if ( isset( $wc_bof_db_settins_values[ WC_BOF_DB . $key ] ) ) {
			return $wc_bof_db_settins_values[ WC_BOF_DB . $key ];
		}

		return $default;
	}
}

if ( ! function_exists( 'wc_bof_get_settings_from_db' ) ) {
	/**
	 * Retrives All Plugin Options From DB
	 */
	function wc_bof_get_settings_from_db() {
		global $wc_bof_db_settins_values;
		$section = array();
		$section = apply_filters( 'wc_bof_settings_section', $section );
		$values  = array();
		foreach ( $section as $settings ) {
			foreach ( $settings as $set ) {
				$db_val = get_option( WC_BOF_DB . $set['id'] );
				if ( is_array( $db_val ) ) {
					unset( $db_val['section_id'] );
					$values = array_merge( $db_val, $values );
				}
			}
		}
		$wc_bof_db_settins_values = $values;
	}
}

if ( ! function_exists( 'wc_bof_is_request' ) ) {
	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 *
	 * @return bool
	 */
	function wc_bof_is_request( $type ) {
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
}

if ( ! function_exists( 'wc_bof_current_screen' ) ) {
	/**
	 * Gets Current Screen ID from wordpress
	 *
	 * @return string [Current Screen ID]
	 */
	function wc_bof_current_screen() {
		$screen = get_current_screen();
		return $screen->id;
	}
}

if ( ! function_exists( 'wc_bof_get_screen_ids' ) ) {
	/**
	 * Returns Predefined Screen IDS
	 *
	 * @return [Array]
	 */
	function wc_bof_get_screen_ids() {
		$screen_ids   = array();
		$screen_ids[] = 'woocommerce_page_woocommerce-bulk-order-form-settings';
		return $screen_ids;
	}
}

if ( ! function_exists( 'wc_bof_dependency_message' ) ) {
	function wc_bof_dependency_message() {
		$text = __( WC_BOF_NAME . ' requires <b> WooCommerce </b> To Be Installed..  <br/> <i>Plugin Deactivated</i> ', WC_BOF_TXT );
		return $text;
	}
}

if ( ! function_exists( 'wc_bof_do_settings_sections' ) ) {
	/**
	 * Prints out all settings sections added to a particular settings page
	 *
	 * Part of the Settings API. Use this in a settings page callback function
	 * to output all the sections and fields that were added to that $page with
	 * add_settings_section() and add_settings_field()
	 *
	 * @global       $wp_settings_sections Storage array of all settings sections added to admin pages
	 * @global       $wp_settings_fields Storage array of settings fields and info about their pages/sections
	 * @since 2.7.0
	 *
	 * @param string $page The slug name of the page whose settings sections you want to output
	 */
	function wc_bof_do_settings_sections( $page ) {
		global $wp_settings_sections, $wp_settings_fields;
		if ( ! isset( $wp_settings_sections[ $page ] ) )
			return;
		$section_count = count( $wp_settings_sections[ $page ] );
		if ( $section_count > 1 ) {
			echo '<ul class="subsubsub wc_bof_settings_submenu">';
			foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
				echo '<li> <a href="#' . $section['id'] . '">' . $section['title'] . '</a> | </li>';
			}
			echo '</ul> <br/>';
		}

		foreach ( (array) $wp_settings_sections[ $page ] as $section ) {
			if ( $section_count > 1 ) {
				echo '<div id="settings_' . $section['id'] . '" class="hidden wc_bof_settings_content">';
			}
			if ( $section['title'] )
				echo "<h2>{$section['title']}</h2>\n";
			if ( $section['callback'] )
				call_user_func( $section['callback'], $section );
			if ( ! isset( $wp_settings_fields ) || ! isset( $wp_settings_fields[ $page ] ) || ! isset( $wp_settings_fields[ $page ][ $section['id'] ] ) )
				continue;
			echo '<table class="form-table">';
			do_settings_fields( $page, $section['id'] );
			echo '</table>';
			if ( $section_count > 1 ) {
				echo '</div>';
			}
		}
	}
}

if ( ! function_exists( 'wc_bof_template_types' ) ) {
	/*
	 * Returns Available Bulk Order Form Template Views
	 */
	function wc_bof_template_types() {
		$templates              = array();
		$templates['standard']  = array(
			'name'     => __( 'Standard Template', WC_BOF_TXT ),
			'callback' => 'WooCommerce_Bulk_Order_Form_Template_UI',
		);
		$templates['variation'] = array(
			'name'     => __( 'Variation Template', WC_BOF_TXT ),
			'callback' => 'WooCommerce_Bulk_Order_Form_Template_UI',
		);
		$templates              = apply_filters( 'wc_bof_templates', $templates );
		return $templates;
	}
}

if ( ! function_exists( 'wc_bof_active_template' ) ) {
	function wc_bof_active_template( $name = '' ) {
		//$templates = !empty(wc_bof_option('template_type')) ? wc_bof_option('template_type') : 'standard' ;
		$templates = wc_bof_option( 'template_type' );
		if ( ! empty( $template ) ) {
			$template = wc_bof_option( 'template_type' );
		} else {
			$template = 'standard';
		}
		if ( ! empty( $name ) ) {
			if ( $name == $templates ) {
				return true;
			}
			return false;
		}
		return $template;
	}
}

if ( ! function_exists( 'wc_bof_template_select_box' ) ) {
	function wc_bof_template_select_box() {
		$list   = wc_bof_template_types();
		$return = array();
		foreach ( $list as $LK => $LV ) {
			$return[ $LK ] = $LV;
		}

		return $return;
	}

}

if ( ! function_exists( 'wc_bof_get_template' ) ) {
	function wc_bof_get_template( $name, $args = array(), $dfpath = '' ) {
		if ( empty( $dfpath ) ) {
			$dfpath = WC_BOF_PATH . '/templates/';
		}
		wc_get_template( $name, $args, 'woocommerce/wcbulkorder', $dfpath );
	}
}

if ( ! function_exists( 'wc_bof_get_search_types' ) ) {
	function wc_bof_get_search_types() {
		$types               = array();
		$types['all']        = __( "All", WC_BOF_TXT );
		$types['sku']        = __( "Product SKU", WC_BOF_TXT );
		$types['id']         = __( "Product ID", WC_BOF_TXT );
		$types['title']      = __( "Product Title", WC_BOF_TXT );
		$types['attributes'] = __( "Product Attributes", WC_BOF_TXT );
		$types               = apply_filters( 'wc_bof_search_types', $types );
		return $types;
	}
}

if ( ! function_exists( 'wc_bof_get_title_templates' ) ) {
	function wc_bof_get_title_templates() {
		$title        = array();
		$title['STP'] = '[sku] - [title] - [price]';
		$title['TPS'] = '[title] - [price] - [sku]';
		$title['TP']  = '[title] - [price]';
		$title['TS']  = '[title] - [sku]';
		$title['T']   = '[title]';
		return apply_filters( 'wc_bof_product_display_templates_tags', $title );
	}
}

if ( ! function_exists( 'wc_bof_settings_products_json' ) ) {
	function wc_bof_settings_products_json( $ids ) {
		$json_ids = array();
		if ( ! empty( $ids ) ) {
			if ( ! is_array( $ids ) ) {
				$ids = explode( ',', $ids );
			}

			foreach ( $ids as $product_id ) {
				$product = wc_bof_get_product( $product_id );
				if ( ! $product ) {
					continue;
				}
				$json_ids[ $product_id ] = wp_kses_post( $product->get_formatted_name() );
			}
		}
		return $json_ids;
	}
}

if ( ! function_exists( 'wc_bof_settings_get_categories' ) ) {
	function wc_bof_settings_get_categories( $tax = 'product_cat', $terms = array() ) {

		if ( empty( $terms ) ) {
			$args                           = array();
			$args['hide_empty']             = false;
			$args['number']                 = 0;
			$args['pad_counts']             = true;
			$args['update_term_meta_cache'] = false;
			$terms                          = get_terms( $tax, $args );
			$output                         = array();
		}
		foreach ( $terms as $term ) {
			$output[ $term->term_id ] = $term->name . ' (' . $term->count . ') ';
		}

		return $output;
	}
}
if ( ! function_exists( 'wc_bof_settings_get_product_attributes' ) ) {
	function wc_bof_settings_get_product_attributes( $selected = false ) {
		$output = array();
		if ( $selected ) {
			$output = wc_bof_option( 'product_attributes', array() );
		} else {
			$attribute_taxonomies = wc_get_attribute_taxonomies();

			foreach ( $attribute_taxonomies as $term ) {
				$output[ $term->attribute_id ] = $term->attribute_label;
			}
		}

		return $output;
	}
}

if ( ! function_exists( 'wc_bof_settings_page_link' ) ) {
	function wc_bof_settings_page_link( $tab = '', $section = '' ) {
		$settings_url = admin_url( 'admin.php?page=' . WC_BOF_SLUG . '-settings' );
		if ( ! empty( $tab ) ) {
			$settings_url .= '&tab=' . $tab;
		}
		if ( ! empty( $section ) ) {
			$settings_url .= '#' . $section;
		}
		return $settings_url;
	}
}


if ( ! function_exists( 'wc_bof_is_wc_v' ) ) {
	function wc_bof_is_wc_v( $compare = '>=', $version = '' ) {
		if ( defined('WOOCOMMERCE_VERSION') && ( empty( $version ) || version_compare( WOOCOMMERCE_VERSION, $version, $compare ) ) ) {
			return true;
		} else {
			return false;
		}
	}
}

if ( ! function_exists( 'wc_bof_get_product' ) ) {
	/**
	 * @param $product_id
	 *
	 * @return bool|mixed
	 */
	function wc_bof_get_product( $product_id ) {
        return wc_get_product( $product_id );
        // return product from registry - disabled for now
		// return WooCommerce_Bulk_Order_Form_Product_Registry::get( $product_id );
	}
}

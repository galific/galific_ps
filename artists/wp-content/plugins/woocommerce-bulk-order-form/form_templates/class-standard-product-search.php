<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class WooCommerce_Bulk_Order_Form_Standard_Product_Search
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class WooCommerce_Bulk_Order_Form_Standard_Product_Search extends WooCommerce_Bulk_Order_Form_Template_Product_Search {
	/**
	 * WooCommerce_Bulk_Order_Form_Standard_Product_Search constructor.
	 */
	public function __construct() {
		add_action( 'wc_bof_render_standard_template_product_search', array( $this, 'init_class' ), 1, 2 );
	}

	/**
	 * @param $return
	 * @param $arr
	 */
	public function init_class( &$return, $arr ) {
		parent::__construct();
		$this->type = 'standard';
		$return     = $this->render_query( $arr );
	}

	/**
	 * @param $arr
	 *
	 * @return mixed
	 */
	public function render_query( $arr ) {
		$this->set_post_per_page( $arr['max_items'] );
		$this->pid_result = $this->search_by_all( $arr );
		$this->pid_result = $this->extract_products( $arr );
		return $this->pid_result;
	}

	/**
	 * @param $arr
	 *
	 * @return array
	 */
	public function search_by_all( $arr ) {
		//$search_results = array();
		//$search_types = wc_bof_get_search_types();
		//foreach ( $search_types as $id => $name ) {}
		//$products_by_sku       = array();
		//$products_by_id        = array();
		//$products_by_title     = array();

		$prodcuts_by_attribute = array();
		$products_by_sku       = $this->search_by_sku( $arr );
		$products_by_id        = $this->search_by_id( $arr );
		$products_by_title     = $this->search_by_title( $arr );
		$status_enabled        = wc_bof_option( 'enable_search_attributes', false );

		if ( 'on' === $status_enabled ) {
			$prodcuts_by_attribute = $this->search_by_attribute( $arr );
		}

		$products = array_unique( array_merge( $products_by_sku, $products_by_id, $products_by_title, $prodcuts_by_attribute ) );
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function search_by_sku( $arr ) {
		$products = array();
		$this->_clear_defaults();
		// TODO: clear defaults should be replaced with something more precise
		// right now it really clears all, but we need something that can reset stuff
		// while still using arguments like max_items - right now we need to set that
		// for every request
		$this->set_post_per_page( $arr['max_items'] );
		$this->set_search_by_sku( $arr );
		$products = $this->get_products();
		$this->_clear_defaults();
		$products = array_unique( array_merge( $products ) );
		return $products;
	}

	/**
	 * @param $arr
	 */
	public function set_search_by_sku( $arr ) {
		$this->set_sku_search( $arr['term'] );
	}

	/**
	 * @param $arr
	 *
	 * @return array
	 */
	public function search_by_id( $arr ) {
		$this->_clear_defaults();
		$this->set_post_per_page( $arr['max_items'] );
		$search_product_1 = $this->set_search_by_id_with_post_in( $arr );
		$this->_clear_defaults();
		$this->set_post_per_page( $arr['max_items'] );
		$search_product_2 = $this->set_search_by_id_with_post_parent( $arr );
		$this->_clear_defaults();
		$products = array_unique( array_merge( $search_product_1, $search_product_2 ) );
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function set_search_by_id_with_post_in( $arr ) {
		if ( is_numeric( $arr['term'] ) ) {
			$this->set_includes( array( 0, $arr['term'] ) );
			return $this->get_products();
		}
		return array();
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function set_search_by_id_with_post_parent( $arr ) {
		if ( is_numeric( $arr['term'] ) ) {
			$this->set_post_parent( $arr['term'] );
			return $this->get_products();
		}
		return array();
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function search_by_title( $arr ) {
		$products = array();
		$this->_clear_defaults();
		$this->set_post_per_page( $arr['max_items'] );
		$this->set_search_by_title( $arr );
		$this->set_search_by_title_query();
		$products = $this->get_products();
		$this->set_search_by_title_query( 'remove' );
		$products = array_unique( array_merge( $products ) );
		return $products;
	}

	/**
	 * @param $arr
	 */
	public function set_search_by_title( $arr ) {
		$this->set_search_query( $arr['term'] );
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function search_by_attribute( $arr ) {
		$products_return = array();
		$this->_clear_defaults();
		$this->set_post_per_page( $arr['max_items'] );
		$attributes = wc_bof_option( 'product_attributes', array() );
		if ( empty( $attributes ) ) {
			$attributes = wc_get_attribute_taxonomies();
		}

		$search_args_old          = $this->get_search_args();
		$search_args              = array();
		$search_args['tax_query'] = array( 'relation' => 'OR' );
		foreach ( $attributes as $tax ) {
			if ( is_object( $tax ) ) {
				$name = wc_attribute_taxonomy_name( $tax->attribute_name );
			} else {
				$name = wc_attribute_taxonomy_name_by_id( $tax );
			}
			$search_args['tax_query'][] = array(
				'field'    => 'name',
				'taxonomy' => $name,
				'terms'    => $arr['term'],
			);
		}
		$search_args_old = array_merge( $search_args_old, $search_args );

		$this->set_search_args( $search_args_old );
		$products = $this->get_products();
		$this->_clear_defaults();
		$products = array_unique( array_merge( $products_return, $products ) );
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array
	 */
	public function extract_products( $arr ) {
		$suggestions     = array();
		$active_currency = get_woocommerce_currency_symbol();
		foreach ( $this->pid_result as $pid ) {
			$post_type  = get_post_type( $pid );
			$child_args = array(
				'post_parent' => $pid,
				'post_type'   => 'product_variation',
			);
			$children   = get_children( $child_args );
			$id         = $pid;
			if ( ( 'product' == $post_type ) && ! empty( $children ) ) {
				continue;
			}

			if ( ( 'product' == $post_type ) && empty( $children ) ) {
				$product = wc_bof_get_product( $pid );
				if ( ! $product || false == $product->is_visible() ) {
					continue;
				}
				$price      = floatval( $product->get_price() );
				$price_html = $product->get_price_html();
				$sku        = $product->get_sku();
				$title      = $this->get_product_title( $id );
				$img        = $this->get_product_image( $id );

			} elseif ( 'product_variation' == $post_type ) {
				$product = wc_bof_get_product( $pid );
				if ( ! $product || false == $product->is_visible() ) {
					continue;
				}
				$parent  = wc_bof_get_product( $pid );

				if ( wc_bof_is_wc_v( '>=', '3.0' ) ) {
					$parent_id = $product->get_parent_id();
				} else {
					$parent_id = $product->parent;
				}

				if ( false === $parent_id || ! $parent ) {
					continue;
				}

				if ( wc_bof_is_wc_v( '>=', '3.0' ) ) {
					$id = $pid;
				} else {
					$id = $product->variation_id;
				}

				$price        = floatval( $product->get_price() );
				$price_html   = $product->get_price_html();
				$sku          = $product->get_sku();
				$title        = $product->get_title();
				$parent_image = $this->get_product_image( $id, false );
				$img          = $this->get_product_image( $parent_id, false );
				$attributes   = $product->get_variation_attributes();

				// add variation title
				$title .= " - " . wc_get_formatted_variation( $product, true, false );

				if ( ! empty( $img ) ) {
					$img = $img;
				} elseif ( ! empty( $parent_image ) ) {
					$img = $parent_image;
				} else {
					$img = apply_filters( 'woocommerce_placeholder_img_src', '' );
				}
			}

			$label_price              = $price;
			$label                    = $this->get_output_title( 'TPS', $arr['product_display_separator'], $title, $label_price, $sku );
			$suggestion               = array();
			$suggestion['label']      = html_entity_decode( apply_filters( 'wc_bulk_order_form_label', $label, $price, $title, $sku, $active_currency, $product ) );
			$suggestion['label']      = strip_tags( $suggestion['label'] );
			$suggestion['price']      = $price;
			$suggestion['price_html'] = $price_html;
			$suggestion['symbol']     = $active_currency;
			$suggestion['id']         = $id;
			$suggestion['imgsrc']     = $img;
			if ( ! empty( $variation_id ) ) {
				$suggestion['variation_id'] = $variation_id;
			}
			$suggestions[] = $suggestion;
		}
		return $suggestions;
	}
}

return new WooCommerce_Bulk_Order_Form_Standard_Product_Search;

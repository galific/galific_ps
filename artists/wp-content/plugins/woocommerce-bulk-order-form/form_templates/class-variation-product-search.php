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
 * Class WooCommerce_Bulk_Order_Form_Variation_Product_Search
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class WooCommerce_Bulk_Order_Form_Variation_Product_Search extends WooCommerce_Bulk_Order_Form_Template_Product_Search {

	/**
	 * WooCommerce_Bulk_Order_Form_Variation_Product_Search constructor.
	 */
	public function __construct() {
		add_action( 'wc_bof_render_variation_template_product_search', array( $this, 'init_class' ), 1, 2 );
	}

	/**
	 * @param $return
	 * @param $arr
	 */
	public function init_class( &$return, $arr ) {
		parent::__construct();
		$this->type = 'variation';
		$return     = $this->render_query( $arr );
	}

	/**
	 * @param $arr
	 *
	 * @return mixed
	 */
	public function render_query( $arr ) {
		$result = '';
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

		//$products = array_unique(array_merge($products_by_sku, $products_by_id, $products_by_title));
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
		$search_product_1 = $this->set_search_with_tax_parent( $arr );
		$this->_clear_defaults();
		$this->set_post_per_page( $arr['max_items'] );
		$search_product_2 = $this->set_search_with_tax( $arr );
		$this->_clear_defaults();
		$products = array_unique( array_merge( $search_product_1, $search_product_2 ) );
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function set_search_with_tax_parent( $arr ) {
		$products = array();

		if ( is_numeric( $arr['term'] ) ) {
			$this->set_post_parent( array( $arr['term'] ) );
			$products = $this->get_products();
			$this->_clear_defaults();
		}
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function set_search_with_tax( $arr ) {
		$products = array();
		if ( is_numeric( $arr['term'] ) ) {
			$products = $this->get_products();
			$this->_clear_defaults();
		}
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array
	 */
	public function search_by_title( $arr ) {
		$products = array();
		$this->_clear_defaults();
		$this->set_post_per_page( $arr['max_items'] );
		$this->set_search_by_title_query();
		$products = $this->set_search_by_title( $arr );
		$this->set_search_by_title_query( 'remove' );
		$this->_clear_defaults();
		$products = array_unique( array_merge( $products ) );
		return $products;
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function set_search_by_title( $arr ) {
		$product = array();

		if ( ! is_numeric( $arr['term'] ) ) {
			$this->set_search_query( $arr['term'] );
			$product = $this->get_products();
		}
		return $product;
	}

	/**
	 * @param $arr
	 *
	 * @return array|string
	 */
	public function search_by_attribute( $arr ) {
		$products_return = array();
		$this->_clear_defaults();
		// TODO: clear defaults should be replaced with something more precise
		// right now it really clears all, but we need something that can reset stuff
		// while still using arguments like max_items - right now we need to set that
		// for every request
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
		$number_step     = apply_filters( 'wcbulkorder_number_step', '1' );
		$min_quantity    = apply_filters( 'wcbulkorder_min_quantity', 0 );
		$max_quantity    = apply_filters( 'wcbulkorder_max_quantity', 100000 );
		$suggestions     = array();
		$active_currency = get_woocommerce_currency_symbol();

		foreach ( $this->pid_result as $pid ) {
			global $product;
			$post_type = get_post_type( $pid );
			if ( 'product' !== $post_type ) {
				continue;
			}
			$attribute_html          = '';
			$variation_template_type = '1';
			$product                 = wc_bof_get_product( $pid );
			if ( ! $product || false == $product->is_visible() ) {
				continue;
			}
			$product_has_variation = 'no';
			$add_to_cart           = '';

			if ( wc_bof_is_wc_v( '>=', '3.0' ) ) {
				$product_type = $product->get_type();
			} else {
				$product_type = $product->product_type;
			}

			if ( $product->has_child() ) {
				$product_has_variation = 'yes';
			}
			$price          = floatval( $product->get_price() );
			$price_html     = $product->get_price_html();
			$sku            = $product->get_sku();
			$stock_quantity = $product->get_stock_quantity();
			$max_quantity   = isset( $stock_quantity ) ? $stock_quantity : $max_quantity;
			$title          = $this->get_product_title( $pid );
			$img            = $this->get_product_image( $pid );

			//  if($arr['variation_attributes'] == 'attributes_value'){
			$variation_template_type = 'attributes_value';
			$att_list                = array();
			$add_to_cart             = $product->add_to_cart_url();
			$product_attributes      = $product->get_attributes();
			$attributes_keys         = array_keys( $product_attributes );

			if ( ! empty( $product_attributes ) ) {
				if ( 'variable' == $product_type ) {
					$get_variations = sizeof( $product->get_children() ) <= apply_filters( 'woocommerce_ajax_variation_threshold', 30, $product );
					$selected_attrs = '';

					if ( wc_bof_is_wc_v( '>=', '3.0' ) ) {
						$selected_attrs = $product->get_default_attributes();
					} else {
						$selected_attrs = $product->get_variation_default_attributes();
					}
					ob_start();
					wc_bof_get_template( 'add-to-cart/variable.php', array(
						'product'              => $product,
						'args'                 => $arr,
						'available_variations' => $get_variations ? $product->get_available_variations() : false,
						'attributes'           => $product->get_variation_attributes(),
						'selected_attributes'  => $selected_attrs,
					) );
					$attribute_html .= ob_get_clean();
				}
			}

			if ( empty( $attribute_html ) ) {
				$attribute_html = apply_filters( 'wcbulkorder_no_variations_found', '<span></span>' );
			}
			//}

			$label_price                      = $price;
			$label                            = $this->get_output_title( 'TPS', $arr['product_display_separator'], $title, $label_price, $sku );
			$suggestion                       = array();
			$suggestion['label']              = html_entity_decode( apply_filters( 'wc_bulk_order_form_label', $label, $price, $title, $sku, $active_currency, $product ) );
			$suggestion['label']              = strip_tags( $suggestion['label'] );
			$suggestion['price']              = $price;
			$suggestion['price_html']         = $price_html;
			$suggestion['symbol']             = $active_currency;
			$suggestion['id']                 = $pid;
			$suggestion['imgsrc']             = $img;
			$suggestion['has_variation']      = $product_has_variation;
			$suggestion['attribute_html']     = $attribute_html;
			$suggestion['add_to_cart_url']    = $add_to_cart;
			$suggestion['variation_template'] = $variation_template_type;
			$suggestion['min']                = $min_quantity;
			$suggestion['max']                = $max_quantity;
			$suggestion['step']               = $number_step;
			$suggestions[]                    = $suggestion;
		}

		return $suggestions;
	}
}

return new WooCommerce_Bulk_Order_Form_Variation_Product_Search;

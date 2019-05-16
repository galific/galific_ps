<?php
/**
 * Dependency Checker
 *
 * Checks if required Dependency plugin is enabled
 *
 * @link https://wordpress.org/plugins/woocommerce-bulk-order-form/
 * @package WooCommerce Bulk Order Form
 * @subpackage WooCommerce Bulk Order Form/FrontEnd
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class WooCommerce_Bulk_Order_Form_Variation_Add_To_Cart_Handler
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class WooCommerce_Bulk_Order_Form_Variation_Add_To_Cart_Handler {
	/**
	 * WooCommerce_Bulk_Order_Form_Variation_Add_To_Cart_Handler constructor.
	 */
	public function __construct() {
		add_action( 'wc_bof_variation_add_to_cart', array( $this, 'add_to_cart' ), 10, 2 );
		add_action( 'wc_bof_variation_single_add_to_cart', array( $this, 'single_add_to_cart' ), 10, 2 );
	}

	/**
	 * @param $return
	 * @param $args
	 */
	public function single_add_to_cart( &$return, $args ) {
		$this->add_to_cart( $return, $args );
	}

	/**
	 * @param $return
	 * @param $args
	 */
	public function add_to_cart( &$return, $args ) {
		if ( isset( $args['wcbof_products'] ) ) {
			$success  = 0;
			$products = $args['wcbof_products'];
			unset( $products['removeHidden'] );

			foreach ( $products as $product ) {
				$qty          = $product['product_qty'];
				$product_id   = $product['product_id'];
				$variation_id = isset( $product['variation_id'] ) ? $product['variation_id'] : '';
				if ( empty( $product_id ) || ( isset( $product['variation_id'] ) && $product['variation_id'] == '' ) ) {
					continue;
				}
				$attributes = isset( $product['attributes'] ) ? $product['attributes'] : null;
				$status     = WC()->cart->add_to_cart( $product_id, $qty, $variation_id, $attributes, null );
				if ( $status ) {
					$success++;
				}
			}

			if ( $success > 0 ) {
				$url       = $cart_url = $this->get_cart_url();
				$product_n = _n( 'product', 'products', $success, WC_BOF_TXT );
				$msg       = sprintf( __( '<a class="button wc-forward" href="%s">View Cart</a> Your %s was successfully added to your cart.', WC_BOF_TXT ), $url, $product_n );
				$type      = 'success';
			} else {
				$msg  = __( "Looks like there was an error. Please try again.", WC_BOF_TXT );
				$type = 'error';
			}
			wc_add_notice( $msg, $type );
		}
	}

	/**
	 * @return mixed|string|void
	 */
	public function get_cart_url() {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.5.2', '>=' ) ) {
			return wc_get_cart_url();
		} else {
			$cart_page_id = woocommerce_get_page_id( 'cart' );
			if ( $cart_page_id ) {
				return apply_filters( 'woocommerce_get_cart_url', get_permalink( $cart_page_id ) );
			} else {
				return '';
			}
		}
	}
}

return new WooCommerce_Bulk_Order_Form_Variation_Add_To_Cart_Handler;

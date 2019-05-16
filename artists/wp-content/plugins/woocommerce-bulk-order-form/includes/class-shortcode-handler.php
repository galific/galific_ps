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
 * Class WooCommerce_Bulk_Order_Form_ShortCode_Handler
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class WooCommerce_Bulk_Order_Form_ShortCode_Handler {

	/**
	 * WooCommerce_Bulk_Order_Form_ShortCode_Handler constructor.
	 */
	public function __construct() {
		add_shortcode( 'wcbulkorder', array( $this, 'render_bulk_order' ) );
	}

	/**
	 * @param        $att
	 * @param string $content
	 *
	 * @return string
	 */
	public function render_bulk_order( $att, $content = '' ) {

		wp_enqueue_script( 'wc-add-to-cart-variation' );
		$render = '';
		$dbs    = $this->get_db_settings();

		$default_atts = apply_filters( 'wc_bof_shortcode_atts', array(
			'template'                    => $dbs['template_type'],
			'rows'                        => $dbs['no_of_rows'],
			'price'                       => $dbs['show_price'],
			'price_label'                 => $dbs['price_label'],
			'product_label'               => $dbs['product_label'],
			'quantity_label'              => $dbs['quantity_label'],
			'variation_label'             => $dbs['variation_label'],
			'add_rows'                    => $dbs['add_rows'],
			'category'                    => $dbs['category'],
			'excluded'                    => $dbs['excluded'],
			'included'                    => $dbs['included'],
			'search_by'                   => $dbs['search_by'],
			'max_items'                   => $dbs['max_items'],
			'product_display'             => $dbs['product_display'],
			'variation_display'           => $dbs['variation_display'],
			'product_display_separator'   => ' - ',
			'variation_display_separator' => ' - ',
			'product_attributes'          => $dbs['product_attributes'],
			'single_addtocart'            => $dbs['single_addtocart'],
			'single_addtocart_label'      => $dbs['single_addtocart_label'],
			'cart_label'                  => $dbs['cart_label'],
			'total_label'                 => $dbs['total_label'],
			'term'                        => 'Ship',
		) );

		$atts = shortcode_atts( $default_atts, $att, 'wcbulkorder' );

		$atts['total_columns']  = 5;
		$atts['active_columns'] = 0;

		$template_list = wc_bof_template_types();
		$class_called  = false;


		if ( ! is_array( $atts['category'] ) ) {
			if ( ! empty( $atts['category'] ) ) {
				$atts['category'] = explode( ',', $atts['category'] );
			}
		}

		if ( ! is_array( $atts['excluded'] ) ) {
			if ( ! empty( $atts['excluded'] ) ) {
				$atts['excluded'] = explode( ',', $atts['excluded'] );
			}
		}

		if ( ! is_array( $atts['included'] ) ) {
			if ( ! empty( $atts['included'] ) ) {
				$atts['included'] = explode( ',', $atts['included'] );
			}
		}


		///do_action_ref_array('wc_bof_render_'.$atts['template'].'_template_product_search',array(&$return,$atts));

		do_action( 'wc_bof_before_shortcode_render', $att, $content );

		if ( isset( $template_list[ $atts['template'] ] ) ) {
			if ( isset( $template_list[ $atts['template'] ]['callback'] ) ) {
				$class = $template_list[ $atts['template'] ]['callback'];
				if ( class_exists( $class ) ) {
					$class        = new $class( $atts, $atts['template'] );
					$render       = $class->render();
					$class_called = true;
				}
			}
		}

		if ( ! $class_called ) {
			do_action_ref_array( 'wc_bof_render_' . $atts['template'] . '_template', array(
				&$render,
				&$atts,
				&$content,
			) );
		}

		do_action( 'wc_bof_after_shortcode_render', $att, $content );

		return $render;
	}

	/**
	 * @return array
	 */
	public function get_db_settings() {
		$db_settings = array();

		$db_settings['add_rows']                 = wc_bof_option( 'add_rows' );
		$db_settings['show_price']               = wc_bof_option( 'show_price' );
		$db_settings['template_type']            = wc_bof_option( 'template_type' );
		$db_settings['no_of_rows']               = wc_bof_option( 'no_of_rows' );
		$db_settings['max_items']                = wc_bof_option( 'max_items' );
		$db_settings['price_label']              = wc_bof_option( 'price_label' );
		$db_settings['product_label']            = wc_bof_option( 'product_label' );
		$db_settings['quantity_label']           = wc_bof_option( 'quantity_label' );
		$db_settings['variation_label']          = wc_bof_option( 'variation_label' );
		$db_settings['category']                 = wc_bof_option( 'category' );
		$db_settings['excluded']                 = wc_bof_option( 'excluded' );
		$db_settings['included']                 = wc_bof_option( 'included' );
		$db_settings['search_by']                = wc_bof_option( 'search_by' );
		$db_settings['product_display']          = wc_bof_option( 'result_format' );
		$db_settings['variation_display']        = wc_bof_option( 'result_variation_format' );
		$db_settings['product_attributes']       = wc_bof_option( 'product_attributes' );
		$db_settings['attribute_display_format'] = wc_bof_option( 'attribute_display_format' ); // deprecated?
		$db_settings['single_addtocart']         = wc_bof_option( 'single_addtocart' );
		$db_settings['single_addtocart_label']   = wc_bof_option( 'single_addtocart_label' );
		$db_settings['cart_label']               = wc_bof_option( 'cart_label' );
		$db_settings['total_label']              = wc_bof_option( 'total_label' );
		if ( empty( $db_settings['template_type'] ) ) {
			$db_settings['template_type'] = 'standard';
		}
		if ( empty( $db_settings['no_of_rows'] ) ) {
			$db_settings['no_of_rows'] = '2';
		}
		if ( empty( $db_settings['max_items'] ) ) {
			$db_settings['max_items'] = -1;
		}
		if ( empty( $db_settings['price_label'] ) ) {
			$db_settings['price_label'] = __( 'Price', WC_BOF_TXT );
		}
		if ( empty( $db_settings['product_label'] ) ) {
			$db_settings['product_label'] = __( 'Product', WC_BOF_TXT );
		}
		if ( empty( $db_settings['quantity_label'] ) ) {
			$db_settings['quantity_label'] = __( 'Qty', WC_BOF_TXT );
		}
		if ( empty( $db_settings['variation_label'] ) ) {
			$db_settings['variation_label'] = __( 'Variation', WC_BOF_TXT );
		}
		if ( empty( $db_settings['category'] ) ) {
			$db_settings['category'] = '';
		}
		if ( empty( $db_settings['excluded'] ) ) {
			$db_settings['excluded'] = '';
		}
		if ( empty( $db_settings['included'] ) ) {
			$db_settings['included'] = '';
		}
		if ( empty( $db_settings['search_by'] ) ) {
			$db_settings['search_by'] = 'all';
		}
		if ( empty( $db_settings['product_display'] ) ) {
			$db_settings['product_display'] = 'TPS';
		}
		if ( empty( $db_settings['variation_display'] ) ) {
			$db_settings['variation_display'] = 'TPS';
		}
		if ( empty( $db_settings['product_attributes'] ) ) {
			$db_settings['product_attributes'] = array();
		}
		if ( empty( $db_settings['attribute_display_format'] ) ) {
			$db_settings['attribute_display_format'] = 'value';
		}
		if ( empty( $db_settings['single_addtocart'] ) ) {
			$db_settings['single_addtocart'] = false;
		}
		if ( empty( $db_settings['single_addtocart_label'] ) ) {
			$db_settings['single_addtocart_label'] = __( 'Add To Cart', WC_BOF_TXT );
		}
		if ( empty( $db_settings['cart_label'] ) ) {
			$db_settings['cart_label'] = __( 'Add To Cart', WC_BOF_TXT );
		}
		if ( empty( $db_settings['total_label'] ) ) {
			$db_settings['total_label'] = __( 'Total', WC_BOF_TXT );
		}

		$db_settings = apply_filters( 'wc_bof_shortcode_settings', $db_settings );
		return $db_settings;
	}
}

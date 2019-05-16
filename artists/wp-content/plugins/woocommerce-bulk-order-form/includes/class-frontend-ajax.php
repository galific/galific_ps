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
 * Class WooCommerce_Bulk_Order_Form_Ajax_FrontEnd
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @since 1.0
 */
class WooCommerce_Bulk_Order_Form_Ajax_FrontEnd {
	/**
	 * WooCommerce_Bulk_Order_Form_Ajax_FrontEnd constructor.
	 */
	public function __construct() {
		add_action( 'wp_ajax_wcbulkorder_product_search', array( $this, 'product_search' ) );
		add_action( 'wp_ajax_nopriv_wcbulkorder_product_search', array( $this, 'product_search' ) );

		add_action( 'wp', array( $this, 'add_to_cart_process' ) );
		add_action( 'wp_ajax_wcbulkorder_product_buy_now', array( $this, 'ajax_add_to_cart_process' ) );
		add_action( 'wp_ajax_nopriv_wcbulkorder_product_buy_now', array( $this, 'ajax_add_to_cart_process' ) );

		add_action( 'wp_ajax_wcbulkorder_product_single_buy_now', array( $this, 'ajax_single_add_to_cart_process' ) );
		add_action( 'wp_ajax_nopriv_wcbulkorder_product_single_buy_now', array(
			$this,
			'ajax_single_add_to_cart_process',
		) );

	}

	public function product_search() {
		//sleep(400);
		if ( ! isset( $_REQUEST['wcbulkorder']['settings'] ) ) {
		}
		$args         = $_REQUEST['wcbulkorder']['settings'];
		$args['term'] = $_REQUEST['term'];

		// var_export($args);die();

		//$args['callback'] = $_REQUEST['callback'];
		if ( ! empty( $args['category'] ) ) {
			$args['category'] = explode( ',', $args['category'] );
		}
		if ( ! empty( $args['excluded'] ) ) {
			$args['excluded'] = explode( ',', $args['excluded'] );
		}
		if ( ! empty( $args['included'] ) ) {
			$args['included'] = explode( ',', $args['included'] );
		}

		$return = '';
		do_action_ref_array( 'wc_bof_render_' . $args['template'] . '_template_product_search', array(
			&$return,
			$args,
		) );
		wp_send_json( $return );
		wp_die();
	}

	public function ajax_add_to_cart_process() {
		$this->add_to_cart_process();
		ob_start();
		wc_print_notices();
		$output = ob_get_clean();
		wp_send_json_success( $output );
		wp_die();
	}

	public function add_to_cart_process() {
		$return = '';
		if ( isset( $_REQUEST['wcbulkorder']['settings'] ) ) {
			$posted_values = apply_filters( 'wc_bof_add_to_cart_data', $_REQUEST['wcbulkorder'] );
			$template      = $_REQUEST['wcbulkorder']['settings']['template'];
			do_action_ref_array( 'wc_bof_' . $template . '_add_to_cart', array( &$return, &$posted_values ) );
		}
	}

	public function ajax_single_add_to_cart_process() {
		$this->single_add_to_cart_process();
		ob_start();
		wc_print_notices();
		$output = ob_get_clean();
		wp_send_json_success( $output );
		wp_die();
	}

	public function single_add_to_cart_process() {
		$return = '';
		if ( isset( $_REQUEST['wcbulkorder']['settings'] ) ) {
			$posted_values = $_REQUEST['wcbulkorder'];
			$template      = $_REQUEST['wcbulkorder']['settings']['template'];
			do_action_ref_array( 'wc_bof_' . $template . '_single_add_to_cart', array( &$return, &$posted_values ) );
		}
	}

}

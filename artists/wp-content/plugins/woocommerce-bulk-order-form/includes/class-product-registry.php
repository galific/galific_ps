<?php
/**
 *
 * Project : wcbof
 * Date : 10-08-2018
 * Time : 10:51 AM
 * File : class-product-registry.php
 *
 * @author Varun Sridharan <varunsridharan23@gmail.com>
 * @version 1.0
 * @package wcbof
 * @copyright 2018 Varun Sridharan
 * @license GPLV3 Or Greater (https://www.gnu.org/licenses/gpl-3.0.txt)
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
}

if ( ! class_exists( 'WooCommerce_Bulk_Order_Form_Product_Registry' ) ) {
	/**
	 * Class WooCommerce_Bulk_Order_Form_Product_Registry
	 *
	 * @author Varun Sridharan <varunsridharan23@gmail.com>
	 * @since 1.0
	 */
	class WooCommerce_Bulk_Order_Form_Product_Registry {
		/**
		 * products
		 *
		 * @var array
		 */
		protected static $products = array();

		/**
		 * @param $product
		 *
		 * @return bool
		 * @static
		 */
		public static function set( $product ) {
			if ( is_numeric( $product ) ) {
				$product_id = $product;
				$product    = wc_get_product( $product );
			} elseif ( $product instanceof WC_Product ) {
				$product_id = $product->get_id();
			} elseif ( ! empty( $product->ID ) ) {
				$product_id = $product->ID;
			} else {
				return false;
			}

			self::add( $product_id, $product );
			return $product;
		}

		/**
		 * @param $product
		 *
		 * @return bool|mixed
		 * @static
		 */
		public static function get( $product ) {
			if ( self::exists( $product ) ) {
				return self::$products[ $product ];
			} else {
				return self::set( $product );
			}
		}

		/**
		 * @param $product_id
		 * @param $instance
		 *
		 * @return bool
		 * @static
		 */
		public static function add( $product_id, $instance ) {
			if ( false === self::exists( $product_id ) ) {
				self::$products[ $product_id ] = $instance;
				return true;
			}
			return false;
		}

		/**
		 * @param $product_id
		 *
		 * @return bool
		 * @static
		 */
		public static function exists( $product_id ) {
			if ( isset( self::$products[ $product_id ] ) ) {
				return true;
			}
			return false;
		}


		/**
		 * @param $product_id
		 *
		 * @return bool
		 * @static
		 */
		public static function clear( $product_id ) {
			if ( isset( self::$products[ $product_id ] ) ) {
				unset( self::$products[ $product_id ] );
				return true;
			}
			return false;
		}
	}
}
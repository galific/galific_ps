<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	class WC_Prdctfltr {

		public static $version;

		public static $dir;
		public static $path;
		public static $url_path;
		public static $settings;
		public static $filter;
		public static $wc_version;

		public static function init() {
			$class = __CLASS__;
			new $class;
		}

		function __construct() {

			global $prdctfltr_global;

			self::$version = PrdctfltrInit::$version;
			self::$wc_version = PrdctfltrInit::version_check();

			self::$dir = trailingslashit( Prdctfltr()->plugin_path() );
			self::$path = trailingslashit( Prdctfltr()->plugin_path() );
			self::$url_path = trailingslashit( Prdctfltr()->plugin_url() );

			self::$settings['permalink_structure'] = get_option( 'permalink_structure' );
			self::$settings['wc_settings_prdctfltr_disable_scripts'] = get_option( 'wc_settings_prdctfltr_disable_scripts', array() );
			self::$settings['wc_settings_prdctfltr_ajax_js'] = get_option( 'wc_settings_prdctfltr_ajax_js', '' );
			self::$settings['wc_settings_prdctfltr_custom_tax'] = get_option( 'wc_settings_prdctfltr_custom_tax', 'no' );
			self::$settings['wc_settings_prdctfltr_enable'] = get_option( 'wc_settings_prdctfltr_enable', 'yes' );

			self::$settings['wc_settings_prdctfltr_enable_overrides'] = get_option( 'wc_settings_prdctfltr_enable_overrides', array( 'orderby', 'result-count' ) );
			if ( !is_array( self::$settings['wc_settings_prdctfltr_enable_overrides'] ) ) {
				self::$settings['wc_settings_prdctfltr_enable_overrides'] = array();
			}
			foreach( self::$settings['wc_settings_prdctfltr_enable_overrides'] as $k => $v ) {
				self::$settings['wc_settings_prdctfltr_enable_overrides'][$k] = 'loop/' . $v . '.php';
			}
			self::$settings['templates'] = array_merge( self::$settings['wc_settings_prdctfltr_enable_overrides'], array( 'loop/pagination.php', 'loop/no-products-found.php' ) );

			self::$settings['wc_settings_prdctfltr_enable_action'] = get_option( 'wc_settings_prdctfltr_enable_action', '' );
			self::$settings['wc_settings_prdctfltr_default_templates'] = get_option( 'wc_settings_prdctfltr_default_templates', 'no' );
			self::$settings['wc_settings_prdctfltr_instock'] = get_option( 'woocommerce_hide_out_of_stock_items', 'no' );
			self::$settings['wc_settings_prdctfltr_use_ajax'] = get_option( 'wc_settings_prdctfltr_use_ajax', 'no' );
			self::$settings['wc_settings_prdctfltr_ajax_class'] = get_option( 'wc_settings_prdctfltr_ajax_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_category_class'] = get_option( 'wc_settings_prdctfltr_ajax_category_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_product_class'] = get_option( 'wc_settings_prdctfltr_ajax_product_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_pagination_class'] = get_option( 'wc_settings_prdctfltr_ajax_pagination_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_count_class'] = get_option( 'wc_settings_prdctfltr_ajax_count_class', '' );
			self::$settings['wc_settings_prdctfltr_ajax_orderby_class'] = get_option( 'wc_settings_prdctfltr_ajax_orderby_class', '' );
			self::$settings['wc_settings_prdctfltr_force_redirects'] = get_option( 'wc_settings_prdctfltr_force_redirects', 'no' );
			self::$settings['wc_settings_prdctfltr_use_analytics'] = get_option( 'wc_settings_prdctfltr_use_analytics', 'no' );
			self::$settings['wc_settings_prdctfltr_shop_page_override'] = get_option( 'wc_settings_prdctfltr_shop_page_override', '' );
			self::$settings['wc_settings_prdctfltr_clearall'] = get_option( 'wc_settings_prdctfltr_clearall', array() );
			self::$settings['wc_settings_prdctfltr_showon_product_cat'] = get_option( 'wc_settings_prdctfltr_showon_product_cat', array() );
			self::$settings['wc_settings_prdctfltr_hideempty'] = get_option( 'wc_settings_prdctfltr_hideempty', 'no' ) == 'yes' ? 1 : 0;
			self::$settings['wc_settings_prdctfltr_pagination_type'] = get_option( 'wc_settings_prdctfltr_pagination_type', 'default' );
			self::$settings['wc_settings_prdctfltr_remove_single_redirect'] = get_option( 'wc_settings_prdctfltr_remove_single_redirect', 'yes' );
			self::$settings['wc_settings_prdctfltr_product_animation'] = get_option( 'wc_settings_prdctfltr_product_animation', 'default' );
			self::$settings['wc_settings_prdctfltr_filtering_mode'] = get_option( 'wc_settings_prdctfltr_filtering_mode', 'simple' );
			self::$settings['wc_settings_prdctfltr_after_ajax_scroll'] = get_option( 'wc_settings_prdctfltr_after_ajax_scroll', 'products' );
			self::$settings['wc_settings_prdctfltr_ajax_pagination'] = get_option( 'wc_settings_prdctfltr_ajax_pagination', '' );
			self::$settings['wc_settings_prdctfltr_ajax_permalink'] = get_option( 'wc_settings_prdctfltr_ajax_permalink', '' );
			self::$settings['wc_settings_prdctfltr_ajax_failsafe'] = get_option( 'wc_settings_prdctfltr_ajax_failsafe', array( 'wrapper', 'product' ) );
			self::$settings['wc_settings_prdctfltr_force_action'] = get_option( 'wc_settings_prdctfltr_force_action', 'no' );
			self::$settings['wc_settings_prdctfltr_use_variable_images'] = get_option( 'wc_settings_prdctfltr_use_variable_images', 'no' );

			self::$settings['wc_settings_prdctfltr_more_overrides'] = get_option( 'wc_settings_prdctfltr_more_overrides', false );
			if ( self::$settings['wc_settings_prdctfltr_more_overrides'] === false ) {
				self::$settings['wc_settings_prdctfltr_more_overrides'] = array( 'product_cat', 'product_tag' );
				if ( self::$settings['wc_settings_prdctfltr_custom_tax'] == 'yes' ) {
					self::$settings['wc_settings_prdctfltr_more_overrides'][] = 'characteristics';
				}
			}

			add_filter( 'woocommerce_locate_template', array( &$this, 'prdctrfltr_add_filter' ), 0, 3 );
			add_filter( 'wc_get_template_part', array( &$this, 'prdctrfltr_add_filter' ), 0, 3 );
			add_filter( 'wcml_multi_currency_ajax_actions', array( &$this, 'wcml_currency' ), 50, 1 );

			if ( self::$settings['wc_settings_prdctfltr_enable'] == 'action' && self::$settings['wc_settings_prdctfltr_enable_action'] !== '' ) {
				$curr_action = explode( ':', self::$settings['wc_settings_prdctfltr_enable_action'] );
				if ( isset( $curr_action[1] ) ) {
					$curr_action[1] = floatval( $curr_action[1] );
				}
				else {
					$curr_action[1] = 10;
				}
				add_filter( $curr_action[0], array( &$this, 'prdctfltr_get_filter' ), $curr_action[1] );
			}

			add_filter( 'body_class', array( $this, 'add_body_class' ) );
			add_action( 'wp_enqueue_scripts', array( &$this, 'prdctfltr_scripts' ) );
			add_action( 'wp_footer', array( &$this, 'localize_scripts' ) );

			add_action( 'woocommerce_product_query', array( &$this, 'prdctfltr_wc_query' ), 999998, 2 );
			add_action( 'woocommerce_product_query', array( &$this, 'prdctfltr_wc_tax' ), 999999, 2 );
			//add_action( 'woocommerce_product_query', array( &$this, 'get_vars' ), 999997, 2 );

			add_action( 'prdctfltr_output_css', array( &$this, 'prdctfltr_add_css' ) );
			add_filter( 'mnthemes_add_meta_information_used', array( &$this, 'prdctfltr_info' ) );

			add_filter( 'woocommerce_product_is_visible', array( &$this, 'outofstock_show' ), 2, 999999 );

			if ( !is_admin() ) {
				if ( self::$settings['permalink_structure'] !== '' ) {
					if ( self::$settings['wc_settings_prdctfltr_force_redirects'] !== 'yes' ) {
						add_action( 'template_redirect', array( &$this, 'prdctfltr_redirect' ), 0 );
					}
				}
			}

			if ( self::$settings['wc_settings_prdctfltr_remove_single_redirect'] == 'yes' ) {
				add_filter( 'woocommerce_redirect_single_search_result', array( &$this, 'return_false' ), 999 );
			}
			if ( self::$settings['wc_settings_prdctfltr_use_analytics'] == 'yes' ) {
				add_action( 'wp_ajax_nopriv_prdctfltr_analytics', array( &$this, 'prdctfltr_analytics' ) );
				add_action( 'wp_ajax_prdctfltr_analytics', array( &$this, 'prdctfltr_analytics' ) );
			}

			add_action( 'prdctfltr_output', array( &$this, 'prdctfltr_get_filter' ), 10 );

			if ( self::$settings['wc_settings_prdctfltr_use_variable_images'] == 'yes' ) {
				//add_action( 'post_thumbnail_html', array( &$this, 'prdctfltr_switch_thumbnails' ), 999, 5 );
				add_action( 'woocommerce_product_get_image', array( &$this, 'prdctfltr_switch_thumbnails_350' ), 999, 5 );
			}

			add_action( 'prdctfltr_filter_before', array( &$this, 'make_filterjs' ) );
			add_action( 'prdctfltr_filter_after', array( &$this, 'do_after' ) );
			add_action( 'prdctfltr_filter_form_after', array( &$this, 'get_added_inputs' ) );
			add_action( 'prdctfltr_filter_wrapper_before', array( &$this, 'get_added_inputs' ) );
			add_action( 'prdctfltr_filter_wrapper_before', array( &$this, 'make_adoptive' ), 10 );
			add_action( 'prdctfltr_filter_wrapper_before', array( &$this, 'get_top_bar' ), 20 );
			add_action( 'woocommerce_after_shop_loop', array( &$this, 'cleanup' ), 999 );

			//add_action( 'wc_get_price_decimal_separator', array( &$this, 'fix_decimal' ), 99 );

		}

		function prdctfltr_info( $val ) {
			$val = array_merge( $val, array( 'WooCommerce Product Filter - All in One WooCommerce Filter' ) );
			return $val;
		}

		function true( $var ) {
			return true;
		}

		function do_after() {
			global $prdctfltr_global;

			$prdctfltr_global['init'] = true;

			if ( !isset( $prdctfltr_global['mobile'] ) && self::$settings['instance']['wc_settings_prdctfltr_mobile_preset'] !== 'default' ) {
				$prdctfltr_global['mobile'] = true;

				self::$settings['remember_mobile'] = array(
					'instance' => self::$settings['instance'],
					'id' => $prdctfltr_global['unique_id']
				);

				$prdctfltr_global['unique_id'] = null;
				$prdctfltr_global['preset'] = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_mobile_preset'];

				include( self::$dir . 'templates/product-filter.php' );

				self::$settings['instance'] = self::$settings['remember_mobile']['instance'];
				$prdctfltr_global['unique_id'] = self::$settings['remember_mobile']['id'];

				unset( self::$settings['remember_mobile'] );
				unset( $prdctfltr_global['mobile'] );
			}
			$prdctfltr_global['sc_query'] = null;
			self::$settings['maxheight']= null;
		}

		function make_filterjs() {

			if ( is_ajax() ) {
				return;
			}

			global $prdctfltr_global;

			if ( !isset( $prdctfltr_global['sc_init'] ) ) {

				if ( is_shop() || is_product_taxonomy() || is_search() || isset( self::$settings['shop_query']['wc_query'] ) && self::$settings['shop_query']['wc_query'] == 'product_query' ) {

					global $woocommerce_loop;
					$columns = apply_filters( 'loop_shop_columns', 4 );
					if ( isset( $woocommerce_loop['columns'] ) && !empty( $woocommerce_loop['columns'] ) ) {
						$columns = $woocommerce_loop['columns'];
					}

					$per_page = apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) );

					$pf_col = intval( get_option( 'wc_settings_prdctfltr_ajax_columns', '' ) );
					$pf_row = intval( get_option( 'wc_settings_prdctfltr_ajax_rows', '' ) );

					if ( $pf_col > 0 ) {
						$columns = $pf_col;
					}
					if ( $pf_row > 0 ) {
						$per_page = $columns*$pf_row;
					}

					if ( !isset( self::$settings['shop_query'] ) ) {

						$ordering_args = array( 'orderby' => 'menu_order title', 'order' => 'ASC' );

						$meta_query    = WC()->query->get_meta_query();
						$query_args    = array(
							'post_type'           => 'product',
							'post_status'         => 'publish',
							'ignore_sticky_posts' => 1,
							'orderby'             => $ordering_args['orderby'],
							'order'               => $ordering_args['order'],
							'posts_per_page'      => $per_page,
							'meta_query'          => $meta_query,
							'tax_query'           => WC()->query->get_tax_query()
						);

						self::$settings['shop_query'] = $query_args;

					}

					$prdctfltr_global['pagefilters'][self::$settings['instance']['id']]['archive'] = true;
					$prdctfltr_global['pagefilters'][self::$settings['instance']['id']]['query_args'] = array_unique( self::$settings['shop_query'], SORT_REGULAR );

					$prdctfltr_global['pagefilters'][self::$settings['instance']['id']]['atts'] = array(
						'archive' => true,
						'columns' => $columns,
						'per_page' => $per_page
					);

				}

			}

			$prdctfltr_global['filter_js'][self::$settings['instance']['id']] = array(
				'widget_search' => isset( $prdctfltr_global['widget_search'] ) ? 'yes' : 'no',
				'widget_options' => isset( $prdctfltr_global['widget_options'] ) ? $prdctfltr_global['widget_options'] : '',
				'collectors' => WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_selection_area'],
				'collector_style' => WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_collector'],
				'button_text' => WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_submit']
			);

		}


		public static function get_filter_appearance() {

			global $prdctfltr_global;

			if ( !defined( 'DOING_AJAX' ) && !isset( $prdctfltr_global['sc_init'] ) ) {
				if ( is_shop() || is_product_category() ) {
					if ( self::prdctfltr_check_appearance() === false ) {
						return false;
					}
				}
			}

			self::$settings['instance'] = null;
			self::$settings['adv'] = 0;
			self::$settings['mta'] = 0;
			self::$settings['rng'] = 0;
			self::$settings['cnt'] = 1;

			self::make_filter();

			$curr_elements = self::$settings['instance']['wc_settings_prdctfltr_active_filters'];

			if ( empty( $curr_elements ) ) {
				return false;
			}

			$active_filters = array();
			$pf_n=0;
			$pf_r=0;

			foreach( $curr_elements as $el ) {

				$el_fil = false;

				if ( $el == 'cat' ) {
					$el_fil = 'product_cat';
				}
				else if ( $el == 'tag' ) {
					$el_fil = 'product_tag';
				}
				else if ( $el == 'char' ) {
					$el_fil = 'characteristics';
				}
				else if ( substr( $el, 0, 3 ) == 'pa_' ) {
					$el_fil = $el;
				}
				else if ( $el == 'advanced' ) {
					$el_fil = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_taxonomy'][$pf_n];
					$pf_n++;
				}
				else if ( $el == 'range' ) {
					$el_fil = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$pf_r];
					$pf_r++;
				}
				else if ( in_array( $el, array( 'sort', 'instock', 'price', 'per_page', 'search' ) ) ) {
					$el_fil = false;
				}

				if ( $el_fil !== false ) {
					$active_filters[$el_fil] = array();
				}

			}
			self::$settings['instance']['active'] = $active_filters;

			$prdctfltr_global['unique_id'] = isset( $prdctfltr_global['unique_id'] ) ? $prdctfltr_global['unique_id'] : uniqid( 'prdctfltr-' );
			self::$settings['instance']['id'] = $prdctfltr_global['unique_id'];

			return true;

		}

		function get_added_inputs() {
			global $prdctfltr_global;

			$curr_elements = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_active_filters'];
			$pf_activated = isset( $prdctfltr_global['pf_activated'] ) ? $prdctfltr_global['pf_activated'] : array();

			if ( 1==1 ) {

		?>
			<div class="prdctfltr_add_inputs">
			<?php
				if ( !in_array( 'search', $curr_elements ) && isset( $pf_activated['s'] ) ) {
					echo '<input type="hidden" name="' . ( isset( $prdctfltr_global['sc_init'] ) ? 'search_products' : 's' ) . '" value="' . esc_attr( $pf_activated['s'] ) . '" />';
				}
				if ( isset( $_GET['page_id'] ) ) {
					echo '<input type="hidden" name="page_id" value="' . esc_attr( $_GET['page_id'] ) . '" />';
				}
				if ( isset($_GET['lang']) ) {
					echo '<input type="hidden" name="lang" value="' . esc_attr( $_GET['lang'] ) . '" />';
				}
				if ( !in_array( 'sort', $curr_elements ) && isset( $pf_activated['orderby'] ) ) {
					echo '<input type="hidden" name="orderby" value="' . esc_attr( $pf_activated['orderby'] ) . '" class="pf_added_orderby" />';
				}

				if ( !empty( $prdctfltr_global['active_permalinks'] ) ) {
					foreach ( $prdctfltr_global['active_permalinks'] as $pf_k => $pf_v ) {
						if ( !array_key_exists( $pf_k, self::$settings['instance']['active'] ) ) {
							echo '<input type="hidden" name="' . esc_attr( $pf_k ) . '" value="' . esc_attr( $prdctfltr_global['permalinks_data'][$pf_k . '_string'] ) . '" class="pf_added_input" />';
						}
						$prdctfltr_global['filter_js'][$prdctfltr_global['unique_id']]['adds'][$pf_k] = $prdctfltr_global['permalinks_data'][$pf_k . '_string'];
					}
				}

				$curr_posttype = get_option( 'wc_settings_prdctfltr_force_product', 'no' );
				if ( $curr_posttype == 'no' ) {
					if ( !isset( $pf_activated['s'] ) && self::$settings['permalink_structure'] == '' && ( is_shop() || is_product_taxonomy() ) ) {
						echo '<input type="hidden" name="post_type" value="product" />';
					}
				}
				else {
					echo '<input type="hidden" name="post_type" value="product" />';
				}

				do_action( 'prdctfltr_add_inputs' );
			?>
			</div>
		<?php
			}

		}
		function prdctfltr_scripts() {

			$curr_scripts = self::$settings['wc_settings_prdctfltr_disable_scripts'];

			//wp_enqueue_style( 'prdctfltr', self::$url_path .'lib/css/style' . ( is_rtl() ? '-rtl' : '' ) . '.css', false, self::$version );
			wp_enqueue_style( 'prdctfltr', self::$url_path .'lib/css/style' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', false, self::$version );

			if ( !in_array( 'mcustomscroll', $curr_scripts ) ) {
				wp_register_script( 'prdctfltr-scrollbar-js', self::$url_path .'lib/js/jquery.mCustomScrollbar.concat.min.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'prdctfltr-scrollbar-js' );
			}

			if ( !in_array( 'isotope', $curr_scripts ) ) {
				wp_register_script( 'prdctfltr-isotope-js', self::$url_path .'lib/js/isotope.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'prdctfltr-isotope-js' );
			}

			if ( !in_array( 'ionrange', $curr_scripts ) ) {
				wp_register_script( 'prdctfltr-ionrange-js', self::$url_path .'lib/js/ion.rangeSlider.min.js', array( 'jquery' ), self::$version, true );
				wp_enqueue_script( 'prdctfltr-ionrange-js' );
			}

			wp_register_script( 'prdctfltr-main-js', self::$url_path .'lib/js/prdctfltr.js', array( 'jquery', 'hoverIntent' ), self::$version, true );
			wp_enqueue_script( 'prdctfltr-main-js' );

		}

		function localize_scripts() {

			global $prdctfltr_global;

			if ( !isset( $prdctfltr_global['init'] ) ) {
				wp_dequeue_script( 'prdctfltr-scrollbar-js' );
				wp_dequeue_script( 'prdctfltr-isotope-js' );
				wp_dequeue_script( 'prdctfltr-ionrange-js' );
				wp_dequeue_script( 'prdctfltr-history' );
				wp_dequeue_script( 'prdctfltr-main-js' );
			}
			else if ( wp_script_is( 'prdctfltr-main-js', 'enqueued' ) ) {
				global $wp_rewrite;

				$curr_args = apply_filters( 'prdctfltr_localize_javascript', array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'wc_ajax' => WC()->ajax_url(),
					'url' => self::$url_path,
					'rtl' => is_rtl(),
					'page_rewrite' => $wp_rewrite->pagination_base,
					'js' => self::$settings['wc_settings_prdctfltr_ajax_js'],
					'use_ajax' => self::$settings['wc_settings_prdctfltr_use_ajax'],
					'ajax_class' => self::$settings['wc_settings_prdctfltr_ajax_class'],
					'ajax_category_class' => self::$settings['wc_settings_prdctfltr_ajax_category_class'],
					'ajax_product_class' => self::$settings['wc_settings_prdctfltr_ajax_product_class'],
					'ajax_pagination_class' => self::$settings['wc_settings_prdctfltr_ajax_pagination_class'],
					'ajax_count_class' => self::$settings['wc_settings_prdctfltr_ajax_count_class'],
					'ajax_orderby_class' => self::$settings['wc_settings_prdctfltr_ajax_orderby_class'],
					'ajax_pagination_type' => self::$settings['wc_settings_prdctfltr_pagination_type'],
					'ajax_animation' => self::$settings['wc_settings_prdctfltr_product_animation'],
					'ajax_scroll' => self::$settings['wc_settings_prdctfltr_after_ajax_scroll'],
					'analytics' => self::$settings['wc_settings_prdctfltr_use_analytics'],
					'clearall' => self::$settings['wc_settings_prdctfltr_clearall'],
					'permalinks' => self::$settings['wc_settings_prdctfltr_ajax_permalink'],
					'ajax_failsafe' => is_array( self::$settings['wc_settings_prdctfltr_ajax_failsafe'] ) ? self::$settings['wc_settings_prdctfltr_ajax_failsafe'] : array(),
					'localization' => array(
						'close_filter' => esc_html__( 'Close filter', 'prdctfltr' ),
						'filter_terms' => esc_html__( 'Filter terms', 'prdctfltr' ),
						'ajax_error' => esc_html__( 'AJAX Error!', 'prdctfltr' ),
						'show_more' => esc_html__( 'Show More', 'prdctfltr' ),
						'show_less' => esc_html__( 'Show Less', 'prdctfltr' ),
						'noproducts' => esc_html__( 'No products found!', 'prdctfltr' ),
						'clearall' => esc_html__( 'Clear all filters', 'prdctfltr' ),
						'getproducts' => esc_html__( 'Show products', 'prdctfltr' )
					),
					'js_filters' => ( isset( $prdctfltr_global['filter_js'] ) ? $prdctfltr_global['filter_js'] : array() ),
					'pagefilters' => ( isset( $prdctfltr_global['pagefilters'] ) ? $prdctfltr_global['pagefilters'] : array() ),
					'rangefilters' => ( isset( $prdctfltr_global['ranges'] ) ? $prdctfltr_global['ranges'] : array() ),
					'orderby' => ( isset( $prdctfltr_global['default_order']['orderby'] ) ? $prdctfltr_global['default_order']['orderby'] : '' ),
					'order' => ( isset( $prdctfltr_global['default_order']['order'] ) ? $prdctfltr_global['default_order']['order'] : '' ),
					'active_sc' => ( isset( WC_Prdctfltr_Shortcodes::$settings['sc'] ) ? WC_Prdctfltr_Shortcodes::$settings['sc'] : '' ),
					'animation' => array(
						'delay' => 100,
						'duration' => 300
					)
				) );
				wp_localize_script( 'prdctfltr-main-js', 'prdctfltr', $curr_args );
			}

		}

		public static function make_global( $set, $query = array() ) {

			global $prdctfltr_global;

			if ( isset( $prdctfltr_global['mobile'] ) ) {
				return true;
			}

			if ( 1==1 ) :

			$stop = false;

			if ( $stop === false ) {

				$taxonomies = array();
				$taxonomies_data = array();
				$permalink_taxonomies = array();
				$permalink_taxonomies_data = array();
				$misc = array();
				$rng_terms = array();
				$mta_terms = array();
				$rng_for_activated = array();
				$mta_for_activated = array();
				$mta_for_array = array();

				$product_taxonomies = get_object_taxonomies( 'product' );
				if ( ( $product_type = array_search( 'product_type', $product_taxonomies ) ) !== false ) {
					unset( $product_taxonomies[$product_type] );
				}

				$sc_args = array();

				$prdctfltr_global['taxonomies'] = $product_taxonomies;

				if ( isset( $prdctfltr_global['sc_query'] ) && is_array( $prdctfltr_global['sc_query'] ) ) {
					foreach( $prdctfltr_global['sc_query'] as $sck => $scv ) {
						if ( in_array( $sck, $product_taxonomies ) ) {
							continue;
						}
						$sc_args[$sck] = $scv;
					}
				}

				$set = array_merge( $sc_args, $set );

				if ( isset( $set ) && !empty( $set ) ) {

					$get = $set;
					self::$settings['original_set'] = $set;

					if ( isset( $get['search_products'] ) && !empty( $get['search_products'] ) && !isset( $get['s'] ) ) {
						$get['s'] = $get['search_products'];
					}

					$allowed = array( 'orderby', 'product_order', 'order', 'product_sort', 'min_price', 'max_price', 'instock_products', 'sale_products', 'products_per_page', 'product_count', 's', 'vendor' );

					foreach( $get as $k => $v ){
						if ( $v == '' ) {
							continue;
						}

						if ( in_array( $k, $allowed ) ) {
							if ( $k == 'order' || $k == 'product_sort' ) {
								$misc['order'] = ( strtoupper( $v ) == 'DESC' ? 'DESC' : 'ASC' );
							}
							else if ( $k == 'orderby' || $k == 'product_order' ) {
								$misc['orderby'] = strtolower( $v );
							}
							else if ( in_array( $k, array( 'products_per_page', 'product_count' ) ) ) {
								$misc['products_per_page'] = intval( $v );
							}
							else if ( in_array( $k, array( 'min_price', 'max_price' ) ) ) {
								$misc[$k] = floatval( $v );
							}
							else {
								$misc[$k] = $v;
							}
						}
						else if ( taxonomy_exists( $k ) || substr( $k, 0, 7 ) == 'filter_' ) {

							if ( strpos( $v, ',' ) ) {
								$selected = explode( ',', $v );
								$taxonomies_data[$k . '_relation'] = 'IN';
							}
							else if ( strpos( $v, '+' ) ) {
								$selected = explode( '+', $v );
								$taxonomies_data[$k . '_relation'] = 'AND';
							}
							else if ( strpos( $v, ' ' ) ) {
								$selected = explode( ' ', $v );
								$taxonomies_data[$k . '_relation'] = 'AND';
							}
							else {
								$selected = array( $v );
							}

							if ( substr( $k, 0, 3 ) == 'pa_' ) {
								$f_attrs[] = 'attribute_' . $k;

								foreach( $selected as $val ) {
									if ( term_exists( $val, $k ) !== null ) {
										$taxonomies[$k][] = $val;
										$f_terms[] = self::prdctfltr_utf8_decode($val);
									}
								}
							}
							else if ( substr( $k, 0, 7 ) == 'filter_' ) {
								$k = 'pa_' . substr( $k, 7 );
								$f_attrs[] = 'attribute_' . $k;

								foreach( $selected as $val ) {
									if ( term_exists( $val, $k ) !== null ) {
										$taxonomies[$k][] = $val;
										$f_terms[] = self::prdctfltr_utf8_decode($val);
									}
								}
							}
							else {
								foreach( $selected as $val ) {
									if ( term_exists( $val, $k ) !== null ) {
										$taxonomies[$k][] = $val;
									}
								}
							}

							if ( !empty( $taxonomies[$k] ) ) {
								if ( isset( $taxonomies_data[$k . '_relation'] ) && $taxonomies_data[$k . '_relation'] == 'AND' ){
									$taxonomies_data[$k . '_string'] = implode( '+', $taxonomies[$k] );
								}
								else {
									$taxonomies_data[$k . '_string'] = implode( ',', $taxonomies[$k] );
								}
							}

						}
						else if ( substr($k, 0, 4) == 'rng_' ) {

							if ( substr($k, 0, 8) == 'rng_min_' ) {
								$rng_for_activated[$k] = ( $k == 'rng_min_price' ? floatval( $v ): $v );
								$rng_terms[str_replace('rng_min_', '', $k)]['min'] = $v;
							}
							else if ( substr($k, 0, 8) == 'rng_max_' ) {
								$rng_for_activated[$k] = ( $k == 'rng_max_price' ? floatval( $v ): $v );
								$rng_terms[str_replace('rng_max_', '', $k)]['max'] = $v;
							}
							else if ( substr($k, 0, 12) == 'rng_orderby_' ) {
								$rng_terms[str_replace('rng_orderby_', '', $k)]['orderby'] = $v;
							}
							else if ( substr($k, 0, 10) == 'rng_order_' ) {
								$rng_terms[str_replace('rng_order_', '', $k)]['order'] = ( strtoupper( $v ) == 'DESC' ? 'DESC' : 'ASC' );
							}

						}
						else if ( substr($k, 0, 4) == apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) ) {
							$mta_key = esc_attr( substr($k, 4, -5) );
							$mta_type = self::get_meta_type( substr($k, -4, 1) );
							$mta_compare = self::get_meta_compare( substr($k, -2, 2) );

							if ( strpos( $v, ',' ) ) {
								$mta_selected = array_map( 'esc_attr', explode( ',', $v ));
								$mta_relation = 'OR';
							}
							else if ( strpos( $v, '+' ) ) {
								$mta_selected = array_map( 'esc_attr', explode( '+', $v ) );
								$mta_relation = 'AND';
							}
							else {
								$mta_selected = esc_attr( $v );
							}

							$mta_for_activated[$k] = $v;
							$mta_for_array[$k] = is_array( $mta_selected ) ? $mta_selected : array( $mta_selected );
							if ( is_array( $mta_selected ) ) {
								$mta_terms['relation'] = $mta_relation;
								foreach( $mta_selected as $mta_sngl ) {
									if ( strpos( $mta_compare, 'BETWEEN') > -1 && strpos( $mta_sngl, '-' ) ) {
										$mta_sngl = explode( '-', $mta_sngl );
									}
									$mta_terms[] = array(
										'key' => $mta_key,
										'type' => $mta_type,
										'compare' => $mta_compare,
										'value' => $mta_sngl
									);
								}
							}
							else {
								if ( strpos( $mta_compare, 'BETWEEN') > -1 && strpos( $mta_selected, apply_filters( 'prdctfltr_meta_key_between_separator', '-' ) ) ) {
									$mta_selected = explode( apply_filters( 'prdctfltr_meta_key_between_separator', '-' ), $mta_selected );
								}
								$mta_terms[] = array(
									'key' => $mta_key,
									'type' => $mta_type,
									'compare' => $mta_compare,
									'value' => $mta_selected
								);
							}

						}

					}

					if ( !empty( $rng_terms ) ) {

						foreach ( $rng_terms as $rng_name => $rng_inside ) {

							if ( !in_array( $rng_name, array( 'price' ) ) ) {

								if ( ( isset( $rng_inside['min'] ) && isset( $rng_inside['max'] ) ) === false || !taxonomy_exists( $rng_name ) ) {
									unset( $rng_terms[$rng_name] );
									unset( $rng_for_activated['rng_min_' . $rng_name] );
									unset( $rng_for_activated['rng_max_' . $rng_name] );
									continue;
								}

								if ( isset( $rng_terms[$rng_name]['orderby'] ) && $rng_terms[$rng_name]['orderby'] == 'number' ) {
									$curr_term_args = array(
										'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
										'orderby' => 'slug'
									);
									$pf_terms = self::prdctfltr_get_terms( $rng_name, $curr_term_args );
									$pf_sort_args = array(
										'order' => isset( $rng_terms[$rng_name]['order'] ) ? $rng_terms[$rng_name]['order'] : ''
									);
									$pf_terms = self::prdctfltr_sort_terms_naturally( $pf_terms, $pf_sort_args );
								}
								else {
									$curr_term_args = array(
										'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
										'orderby' => isset( $rng_terms[$rng_name]['orderby'] ) ? $rng_terms[$rng_name]['orderby'] : '',
										'order' => isset( $rng_terms[$rng_name]['order'] ) ? $rng_terms[$rng_name]['order'] : ''
									);
									$pf_terms = self::prdctfltr_get_terms( $rng_name, $curr_term_args );
								}

								if ( empty( $pf_terms ) ) {
									continue;
								}

								$rng_found = false;

								$curr_ranges = array();

								foreach ( $pf_terms as $c => $s ) {
									if ( $rng_found == true ) {
										$curr_ranges[] = $s->slug;
										if ( $s->slug == $rng_inside['max'] ) {
											$rng_found = false;
											continue;
										}
									}
									if ( $s->slug == $rng_inside['min'] && $rng_found === false ) {
										$rng_found = true;
										$curr_ranges[] = $s->slug;
									}
								}

								$taxonomies[$rng_name] = $curr_ranges;
								$taxonomies_data[$rng_name.'_string'] = implode( $curr_ranges, ',' );
								$taxonomies_data[$rng_name.'_relation'] = 'IN';

								if ( substr( $rng_name, 0, 3 ) == 'pa_' ) {
									$f_attrs[] = 'attribute_' . $rng_name;

									foreach ( $curr_ranges as $cr ) {
										$f_terms[] = $cr;
									}
								}

							}
							else {
								if ( !isset( $rng_inside['min'] ) || !isset( $rng_inside['max'] ) || ( $rng_inside['min'] < $rng_inside['max'] ) === false ) {
									unset( $rng_terms[$rng_name] );
									unset( $rng_for_activated['rng_min_' . $rng_name] );
									unset( $rng_for_activated['rng_max_' . $rng_name] );
								}
							}

						}

					}

				}

				if ( is_product_taxonomy() || isset( $prdctfltr_global['sc_query'] ) && !empty( $prdctfltr_global['sc_query'] ) ) {

					$check_links = apply_filters( 'prdctfltr_check_permalinks', $product_taxonomies );

					foreach( $check_links as $check_link ) {

						$curr_link = false;
						$pf_helper = array();
						$pf_helper_real = array();
						$is_attribute = substr( $check_link, 0, 3 ) == 'pa_' ? true : false;


						if ( !isset( $set[$check_link] ) && ( $curr_var = get_query_var( $check_link ) ) !== '' ) {
							$curr_link = $curr_var;
						}
						else if ( isset( $prdctfltr_global['sc_query'][$check_link] ) && $prdctfltr_global['sc_query'][$check_link] !== '' ) {
							$curr_link = $prdctfltr_global['sc_query'][$check_link];
						}

						else {
							$curr_link = false;
						}

						if ( $curr_link ) {

							if ( !is_array( $curr_link ) ) {
								if ( strpos( $curr_link, ',' ) ) {
									$pf_helper = explode( ',', $curr_link );
									$permalink_taxonomies_data[$check_link.'_relation'] = 'IN';
								}
								else if ( strpos( $curr_link, '+' ) ) {
									$pf_helper = explode( '+', $curr_link );
									$permalink_taxonomies_data[$check_link.'_relation'] = 'AND';
								}
								else if ( strpos( $curr_link, ' ' ) ) {
									$pf_helper = explode( ' ', $curr_link );
									$permalink_taxonomies_data[$check_link.'_relation'] = 'AND';
								}
								else {
									$pf_helper = array( $curr_link );
								}
							}
							else {
								$pf_helper = $curr_link;
							}

							foreach( $pf_helper as $val ) {
								if ( term_exists( $val, $check_link ) !== null ) {
									$pf_helper_real[] = $val;
									if ( $is_attribute ) {
										$f_terms[] = self::prdctfltr_utf8_decode($val);
									}
								}
							}

							if ( !empty( $pf_helper_real ) ) {
								$permalink_taxonomies[$check_link] = $pf_helper_real;

								if ( $is_attribute ) {
									$f_attrs[] = 'attribute_' . $check_link;
								}
								if ( isset( $permalink_taxonomies_data[$check_link . '_relation'] ) && $permalink_taxonomies_data[$check_link . '_relation'] == 'AND' ){
									$permalink_taxonomies_data[$check_link . '_string'] = implode( '+', $pf_helper_real );
								}
								else {
									$permalink_taxonomies_data[$check_link . '_string'] = implode( ',', $pf_helper_real );
								}
							}

						}

					}

				}

				if ( isset( $misc['order'] ) && !isset( $misc['orderby'] ) ) {
					unset( $misc['order'] );
				}

				$prdctfltr_global['done_filters'] = true;
				$prdctfltr_global['taxonomies_data'] = $taxonomies_data;
				$prdctfltr_global['active_taxonomies'] = $taxonomies;
				$prdctfltr_global['active_misc'] = $misc;
				$prdctfltr_global['range_filters'] = $rng_terms;
				$prdctfltr_global['meta_filters'] = $mta_terms;
				$prdctfltr_global['meta_data'] = $mta_for_activated;
				$prdctfltr_global['active_filters'] = array_merge( $prdctfltr_global['active_taxonomies'], $prdctfltr_global['active_misc'], $rng_for_activated, $mta_for_array );

				$prdctfltr_global['active_permalinks'] = array_merge( $permalink_taxonomies, $prdctfltr_global['active_taxonomies'] );
				$prdctfltr_global['permalinks_data'] = array_merge( $permalink_taxonomies_data, $prdctfltr_global['taxonomies_data'] );

				if ( !empty( $prdctfltr_global['active_permalinks'] ) && ( is_shop() || is_product_taxonomy() ) ) {
					$prdctfltr_global['sc_query'] = $prdctfltr_global['active_permalinks'];
				}

				if ( !empty( $misc ) || !empty( $rng_for_activated ) || !empty( $mta_for_array ) || count( $taxonomies ) == 1 && !isset( $taxonomies['product_cat'] ) || count( $taxonomies ) > 1 ) {
					add_filter( 'woocommerce_is_filtered', 'WC_Prdctfltr::return_true' );
				}

				$prdctfltr_global['active_in_filter'] = $prdctfltr_global['active_filters'];
				if ( isset( $prdctfltr_global['sc_query'] ) && !is_array( $prdctfltr_global['sc_query'] ) ) {
					foreach ( $check_links as $check_link ) {
						if ( isset( $prdctfltr_global['sc_query'][$check_link] ) && isset( $prdctfltr_global['active_in_filter'][$check_link] ) && $prdctfltr_global['sc_query'][$check_link] == $prdctfltr_global['active_in_filter'][$check_link] ) {
							unset( $prdctfltr_global['active_in_filter'][$check_link] );
						}
						
					}
				}

				$prdctfltr_global['pf_activated'] = array_merge( $prdctfltr_global['active_in_filter'], $prdctfltr_global['active_permalinks'] );
				self::$settings['pf_activated'] = $prdctfltr_global['pf_activated'];

				if ( isset( $f_attrs ) ) {
					$prdctfltr_global['f_attrs'] = $f_attrs;
				}
				if ( isset( $f_terms ) ) {
					$prdctfltr_global['f_terms'] = $f_terms;
				}

				$pf_activated = $prdctfltr_global['active_taxonomies'];
				$pf_tax_query = array();

				if ( !empty( $pf_activated ) || !empty( $prdctfltr_global['active_permalinks'] ) ) {

					foreach ( $pf_activated as $k => $v ) {
						$relation = isset( $prdctfltr_global['taxonomies_data'][$k . '_relation'] ) && $prdctfltr_global['taxonomies_data'][$k.'_relation'] == 'AND' ? 'AND' : 'IN';
						if ( count( $v ) > 1 ) {
							if ( $relation == 'AND' ) {
								$precompile = array();
								foreach( $v as $k12 => $v12 ) {

									$asked_term = get_term_by( 'slug', $v12, $k );
									$child_terms = get_term_children( $asked_term->term_id, $k );

									if ( !empty( $child_terms ) ) {
										$precompile[] = array( 'taxonomy' => $k, 'field' => 'term_id', 'terms' => array_merge( $child_terms, array( $asked_term->term_id ) ), 'include_children' => false, 'operator' => 'IN' );
									}
									else {
										$precompile[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v12, 'include_children' => false, 'operator' => 'IN' );
									}
								}

								$precompile['relation'] = 'AND';

								$pf_tax_query[] = $precompile;
							}
							else {
								$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
							}
						}
						else {
							$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
						}
					}

					$pf_permalinks = $prdctfltr_global['active_permalinks'];

					foreach ( $pf_permalinks as $k => $v ) {
						$relation = isset( $prdctfltr_global['permalinks_data'][$k . '_relation'] ) && $prdctfltr_global['permalinks_data'][$k . '_relation'] == 'AND' ? 'AND' : 'IN';
						if ( count( $v ) > 1 ) {
							if ( $relation == 'AND' ) {
								$precompile = array();
								foreach( $v as $k12 => $v12 ) {

									$asked_term = get_term_by( 'slug', $v12, $k );
									$child_terms = get_term_children( $asked_term->term_id, $k );

									if ( !empty( $child_terms ) ) {
										$precompile[] = array( 'taxonomy' => $k, 'field' => 'term_id', 'terms' => array_merge( $child_terms, array( $asked_term->term_id ) ), 'include_children' => false, 'operator' => 'IN' );
									}
									else {
										$precompile[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v12, 'include_children' => false, 'operator' => 'IN' );
									}
								}

								$precompile['relation'] = 'AND';

								$pf_tax_query[] = $precompile;
							}
							else {
								$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
							}
						}
						else {
							$pf_tax_query[] = array( 'taxonomy' => $k, 'field' => 'slug', 'terms' => $v, 'include_children' => true, 'operator' => 'IN' );
						}
					}

				}

				if ( self::$wc_version ) {

					$active = $prdctfltr_global['active_filters'];

					$curr_instock = self::$settings['wc_settings_prdctfltr_instock'];

					if ( !isset($active['instock_products']) && isset(WC_Prdctfltr_Shortcodes::$settings['sc_instock']) && in_array( WC_Prdctfltr_Shortcodes::$settings['sc_instock'], array( 'in', 'out', 'both' ) ) ) {
						$active['instock_products'] = WC_Prdctfltr_Shortcodes::$settings['sc_instock'];
					}

					if ( ( ( ( isset( $active['instock_products'] ) && $active['instock_products'] !== '' && ( $active['instock_products'] == 'in' || $active['instock_products'] == 'out' ) ) || $curr_instock == 'yes' ) !== false ) && ( !isset( $active['instock_products'] ) || $active['instock_products'] !== 'both' ) ) {
						$operator = isset( $active['instock_products'] ) && $active['instock_products'] == 'out' ? 'IN' : 'NOT IN';
					}

					if ( isset( $operator ) ) {

						$pf_tax_query[] = array( 'taxonomy' => 'product_visibility', 'field' => 'slug', 'terms' => array( 'outofstock' ), 'operator' => $operator );
					}
					$pf_tax_query[] = array( 'taxonomy' => 'product_visibility', 'field' => 'slug', 'terms' => array( 'exclude-from-' . ( isset( $active['s'] ) ? 'search' : 'catalog' ) ), 'operator' => 'NOT IN' );

				}

				if ( !empty( $pf_tax_query ) ) {
					self::$settings['tax_query'] = $pf_tax_query;
					$prdctfltr_global['tax_query'] = $pf_tax_query;
				}

			}

			endif;

		}

		public static function outofstock_show( $visible, $id ) {
			global $prdctfltr_global;
			if ( isset( $prdctfltr_global['active_filters']['instock_products'] ) && in_array( $prdctfltr_global['active_filters']['instock_products'], array( 'both', 'out', 'in' ) ) ) {
				return true;
			}
			return $visible;
		}

		public static function get_min_max_price_meta_query( $args ) {

			$min = isset( $args['min_price'] ) ? floatval( $args['min_price'] ) : 0;
			$max = isset( $args['max_price'] ) ? floatval( $args['max_price'] ) : 9999999999;

			if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_shop' ) && ! wc_prices_include_tax() ) {
				$tax_classes = array_merge( array( '' ), WC_Tax::get_tax_classes() );
				$class_min   = $min;

				foreach ( $tax_classes as $tax_class ) {
					if ( $tax_rates = WC_Tax::get_rates( $tax_class ) ) {
						$class_min = $min - WC_Tax::get_tax_total( WC_Tax::calc_exclusive_tax( $min, $tax_rates ) );
					}
				}

				$min = $class_min;
			}

			return array(
				'key'     => '_price',
				'value'   => array( $min, $max ),
				'compare' => 'BETWEEN',
				'type'    => 'DECIMAL(10,' . wc_get_price_decimals() . ')',
			);
		}

		public static function sc_wc_query( $query ) {
			if ( isset( $query->query_vars['prdctfltr_active'] ) ) {
				call_user_func( 'WC_Prdctfltr::prdctfltr_wc_query', $query, array() );
			}
		}
		public static function sc_wc_tax( $query ) {
			if ( isset( $query->query_vars['prdctfltr_active'] ) ) {
				call_user_func( 'WC_Prdctfltr::prdctfltr_wc_tax', $query, array() );
			}
		}

		public static function prdctfltr_wc_query( $query, $that ) {

			if ( !is_ajax() && current_filter() == 'woocommerce_product_query' ) {
				self::get_vars( $query, array() );
				self::make_global( $_REQUEST, $query );
			}

			global $prdctfltr_global;

			$stop = true;

			$curr_args = array();
			$f_attrs = array();
			$f_terms = array();
			$rng_terms = array();

			if ( isset( $prdctfltr_global['active_filters'] ) ) {

				$pf_activated =  $prdctfltr_global['active_filters'];

				if ( isset( $prdctfltr_global['range_filters'] ) ) {
					$rng_terms = $prdctfltr_global['range_filters'];
				}

				if ( isset( $prdctfltr_global['f_attrs'] ) ) {

					$f_attrs = $prdctfltr_global['f_attrs'];

					if ( isset( $prdctfltr_global['f_terms'] ) ) {
						$f_terms = $prdctfltr_global['f_terms'];
					}

				}

			}

			if ( is_ajax() && !isset( $prdctfltr_global['sc_init'] ) || isset( $prdctfltr_global['sc_init'] ) && isset( $pf_activated['orderby'] ) && $pf_activated['orderby'] !== '' ) {

				$orderby = '';
				$order = '';

				$default_order = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
				$default_explode = explode( '-', $default_order );
				$dfltOrderBy       = esc_attr( $default_explode[0] );
				$dfltOrder         = ( isset( $default_explode[1] ) && !empty( $default_explode[1] ) ? $default_explode[1] : '' );
				$prdctfltr_global['default_order']['orderby'] = isset( $dfltOrderBy ) ? $dfltOrderBy : '';
				$prdctfltr_global['default_order']['order'] = isset( $dfltOrder ) ? $dfltOrder : '';

				$orderby_value = isset( $pf_activated['orderby'] ) ? wc_clean( (string) $pf_activated['orderby'] ) : $default_order;
				$orderby_value = explode( '-', $orderby_value );
				$orderby       = esc_attr( $orderby_value[0] );
				$order         = isset( $pf_activated['order'] ) && !empty( $pf_activated['order'] ) ? ( $pf_activated['order'] == 'DESC' ? 'DESC' : 'ASC' ) : ( isset( $orderby_value[1] ) && !empty( $orderby_value[1] ) ? $orderby_value[1] : '' );


				$orderby = strtolower( $orderby );
				$order   = strtoupper( $order );

				switch ( $orderby ) {

					case 'rand' :
						$curr_args['orderby']  = 'rand';
					break;
					case 'date' :
					case 'date ID' :
						$curr_args['orderby']  = 'date';
						$curr_args['order']    = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					case 'price' :
						if ( 'DESC' === $order ) {
							add_filter( 'posts_clauses', array( WC()->query, 'order_by_price_desc_post_clauses' ) );
						} else {
							add_filter( 'posts_clauses', array( WC()->query, 'order_by_price_asc_post_clauses' ) );
						}
					break;
					case 'popularity' :
						$curr_args['meta_key'] = 'total_sales';
						add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );
					break;
					case 'rating' :
						if ( self::$wc_version === false ) {
							add_filter( 'posts_clauses', array( WC()->query, 'order_by_rating_post_clauses' ) );
						}
						else {
							$curr_args['orderby']  = array( "meta_value_num" => "DESC", "ID" => "ASC" );
							$curr_args['order']  = "ASC";
							$curr_args['meta_key'] = '_wc_average_rating';
						}
					break;
					case 'title' :
						$curr_args['orderby']  = 'title';
						$curr_args['order']    = $order == 'DESC' ? 'DESC' : 'ASC';
					break;
					case 'menu_order' :
					case 'menu_order title' :
					case '' :
						$curr_args['orderby'] = 'menu_order title';
						$curr_args['order'] = $order == 'DESC' ? 'DESC' : 'ASC';
					break;
					case 'comment_count' :
						$curr_args['orderby'] = 'comment_count';
						$curr_args['order']   = $order == 'ASC' ? 'ASC' : 'DESC';
					break;
					default :
						$curr_args['orderby'] = $orderby;
						$curr_args['order']   = $order == 'ASC' ? 'ASC' : 'DESC';
					break;

				}

			}

			if ( !isset($pf_activated['min_price']) && !isset($pf_activated['rng_min_price']) && isset($query->query_vars['min_price']) && $query->query_vars['min_price'] !== '' ) {
				$pf_activated['min_price'] = $query->query_vars['min_price'];
			}

			if ( !isset($pf_activated['max_price']) && !isset($pf_activated['rng_max_price']) && isset($query->query_vars['max_price']) && $query->query_vars['max_price'] !== '' ) {
				$pf_activated['max_price'] = $query->query_vars['max_price'];
			}

			if ( ( isset( $pf_activated['min_price'] ) || isset( $pf_activated['max_price'] ) ) !== false || ( isset( $pf_activated['rng_min_price'] ) && isset( $pf_activated['rng_max_price'] ) ) !== false || ( isset( $pf_activated['sale_products'] ) || isset( $query->query_vars['sale_products'] ) ) !== false ) {
				if ( self::$wc_version === false ) {
					add_filter( 'posts_join' , array( 'WC_Prdctfltr', 'prdctfltr_join_price' ), 99997 );
					add_filter( 'posts_where' , array( 'WC_Prdctfltr', 'prdctfltr_price_filter' ), 99998, 2 );
				}
				else {
					$prices = self::get_prices( $query->query_vars );

					if ( ( isset( $pf_activated['sale_products'] ) || isset( $query->query_vars['sale_products'] ) ) !== false ) {
						$curr_args['post__in'] = isset( $curr_args['post__in'] ) ? array_merge( $curr_args['post__in'], wc_get_product_ids_on_sale() ) : wc_get_product_ids_on_sale();
					}
					if ( isset( $prices['min_price'] ) || isset( $prices['max_price'] ) ) {
						$prdctfltr_global['meta_filters']['price_filter'] = self::get_min_max_price_meta_query( $prices );
					}
				}

			}

			/* WC <3.0.x support for instock, will be removed soon! */
			if ( self::$wc_version === false ) {
				include( 'deprecated-instock.php' );
			}

			if ( isset( $pf_activated['products_per_page'] ) && $pf_activated['products_per_page'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
					'posts_per_page' => floatval( $pf_activated['products_per_page'] )
				) );
			}

			if ( isset( $pf_activated['s'] ) && $pf_activated['s'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
					's' => $pf_activated['s']
				) );
			}

			if ( isset( $pf_activated['vendor'] ) && $pf_activated['vendor'] !== '' ) {
				$curr_args = array_merge( $curr_args, array(
					'author' => $pf_activated['vendor']
				) );
			}

			if ( isset( $prdctfltr_global['meta_filters'] ) ) {
				$product_metas = self::unconvert_price_filter_limits( apply_filters( 'prdctfltr_meta_query', $prdctfltr_global['meta_filters'] ) );

				if ( !empty( $product_metas ) ) {
					$curr_args['meta_query']['relation'] = 'AND';
					$curr_args['meta_query'][] = $product_metas;
					$checkMeta = isset( $query->query_vars['meta_query'] ) ? $query->query_vars['meta_query'] : array() ;
					if ( !empty( $checkMeta ) ) {
						foreach( $checkMeta as $mk => $mv ) {
							if ( $mk == 'price_filter' || is_array( $mv ) && key( $mv ) == 'price_filter' ) {
								unset($checkMeta[$mk]);
							}
						}
					}
					$curr_args['meta_query'][] = $checkMeta;
				}
			}

			if ( self::$wc_version ) {
				if ( !isset($pf_activated['instock_products']) && isset($query->query_vars['instock_products']) && in_array( $query->query_vars['instock_products'], array( 'in', 'out', 'both' ) ) ) {
					$pf_activated['instock_products'] = $query->query_vars['instock_products'];
				}

				if ( ( ( ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] !== '' && ( $pf_activated['instock_products'] == 'in' || $pf_activated['instock_products'] == 'out' ) ) || self::$settings['wc_settings_prdctfltr_instock'] == 'yes' ) !== false ) && ( !isset( $pf_activated['instock_products'] ) || $pf_activated['instock_products'] !== 'both' ) ) {

					if ( count( $f_attrs ) > 0 ) {

						global $wpdb;

						$tax_query  = ( isset( $prdctfltr_global['tax_query'] ) ? $prdctfltr_global['tax_query'] : array() );

						if ( empty( $tax_query ) ) {
							global $wp_the_query;
							$tax_query = isset( $wp_the_query->tax_query->queries ) && !empty( $wp_the_query->tax_query->queries ) ? $wp_the_query->tax_query->queries : array();
						}

						$join  = '';
						$where = '';
						if ( !empty( $tax_query ) ) {
							$tax_query  = new WP_Tax_Query( $tax_query );
							$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
							$join  = $tax_query_sql['join'];
							$where = $tax_query_sql['where'];
						}

						if ( !empty( $join ) && !empty( $where ) ) {
							$curr_atts =  implode( '","', array_map( 'esc_sql', $f_attrs ) );
							$curr_terms = implode( '","', array_map( 'esc_sql', $f_terms ) );
							$outofstock = get_term_by( 'slug', 'outofstock', 'product_visibility' );

							$variableStockOut = $wpdb->get_results( sprintf( '
								SELECT DISTINCT(%1$s.post_parent) as ID FROM %1$s
								INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id)
								INNER JOIN %3$s ON (%1$s.ID = %3$s.object_id)
								WHERE %1$s.post_type = "product_variation"
								AND pf1.meta_key IN ("'.$curr_atts.'") AND pf1.meta_value IN ("'.$curr_terms.'","")
								AND ( %1$s.ID IN ( SELECT object_id FROM %3$s WHERE term_taxonomy_id IN ( ' . $outofstock->term_id . ' ) ) )
								AND ( %1$s.ID IN ( SELECT post_id FROM %2$s WHERE meta_key LIKE "attribute_pa_%%" GROUP BY post_id HAVING COUNT( DISTINCT meta_key ) = ' . count( $f_attrs ) . ' ) )
								GROUP BY pf1.post_id
								HAVING COUNT(DISTINCT pf1.meta_key) = ' . count( $f_attrs ) .'
								LIMIT 29999
							', $wpdb->posts, $wpdb->postmeta, $wpdb->term_relationships ), ARRAY_N );

							if ( !empty( $variableStockOut ) ) {
								$variableStockOutFil = array();
								foreach ( $variableStockOut as $k => $p ) {
									if ( !in_array( $p[0], $variableStockOutFil ) ) {
										$variableStockOutFil[] = $p[0];
									}
								}
								if ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] == 'out' ) {
									self::$settings['variable_outofstock'] = $variableStockOutFil;
									add_filter( 'posts_where' , array( 'WC_Prdctfltr', 'prdctfltr_add_variable_outofstock' ), 99998, 2 );
								}
								else {
									$curr_args = array_merge( $curr_args, array( 'post__not_in' => $variableStockOutFil ) );
								}
								
							}
						}
					}
				}
			}

			foreach ( $curr_args as $k => $v ) {
				switch( $k ) {
					case 'post__in' :
						$v = array_unique( $v );
						$postIn = isset( $query->query_vars['post__in'] ) && !empty( $query->query_vars['post__in'] ) ? $query->query_vars['post__in'] : array();
						$ins = ( empty( $postIn ) ? $v : array_intersect( $postIn, $v ) );
						$query->set( $k, $ins );
					break;
					default:
						$query->set( $k, $v );
					break;
				}
			}

		/*	if ( !is_ajax() ) {
				remove_filter( 'pre_get_posts', 'WC_Prdctfltr::sc_wc_query', 999999 );
			}*/

		}

		public static function get_vars( $query, $that ) {
			if ( $query->is_main_query() ) {

				$ordering_args = array( 'orderby' => 'menu_order title', 'order' => 'ASC' );

				$meta_query    = WC()->query->get_meta_query();
				$query_args    = array(
					'post_type'           => 'product',
					'post_status'         => 'publish',
					'ignore_sticky_posts' => 1,
					'orderby'             => $ordering_args['orderby'],
					'order'               => $ordering_args['order'],
					'meta_query'          => $meta_query,
					'tax_query'           => WC()->query->get_tax_query()
				);

				self::$settings['shop_query'] = $query_args;
			}
		}

		public static function prdctfltr_wc_tax( $query, $that ) {

			global $prdctfltr_global;

			$stop = true;
			$curr_args = array();

			$pf_tax_query = apply_filters( 'prdctfltr_tax_query', ( isset( self::$settings['tax_query'] ) ? self::$settings['tax_query'] : array() ) );

			$pf_activated = isset( $prdctfltr_global['active_taxonomies'] ) ? $prdctfltr_global['active_taxonomies'] : array();

			if ( !empty( $pf_tax_query ) ) {

				$pf_tax_query['relation'] = 'AND';

				$now = !empty( $query->tax_query->queries ) ? $query->tax_query->queries : array();

				if ( !empty( $now ) ) {
					foreach( $now as $k => $v ) {
						if ( isset( $v['taxonomy'] ) && $v['taxonomy'] == 'product_visibility' && isset( $v['terms'] ) && is_array( $v['terms'] ) && empty( array_intersect( array( 'exclude-from-catalog', 'exclude-from-search', 'outofstock' ), $v['terms'] ) ) ) {
							unset( $now[$k] );
						}
					}
					$query->query_vars['tax_query'] = $query->tax_query->queries = array_unique( array_merge( $pf_tax_query, $now ), SORT_REGULAR );
				}
				else {
					$query->query_vars['tax_query'] = $query->tax_query->queries = array_unique( $pf_tax_query, SORT_REGULAR );
				}

				if ( is_ajax() && empty( $query->tax_query->queried_terms ) && !empty( $pf_activated ) ) {

					$addTerms = array();

					foreach ( $pf_activated as $k => $v ) {
						$addTerms[$k] = array(
							'terms' => $v,
							'field' => 'slug'
						);
					}
					$query->is_tax = true;
					$query->tax_query->queried_terms = $addTerms;

				}

			}

		/*	if ( !is_ajax() ) {
				remove_filter( 'parse_tax_query', 'WC_Prdctfltr::sc_wc_tax', 999999 );
			}*/

		}

		public static function prdctfltr_join_price( $join ) {
			global $wpdb, $prdctfltr_global;
			$pf_activated = $prdctfltr_global['active_filters'];

			if ( isset( $prdctfltr_global['active_filters']['sale_products'] ) && $prdctfltr_global['active_filters']['sale_products'] == 'on' ) {
				$meta_keys = array(
					'_sale_price',
					'_min_variation_sale_price',
					'_max_variation_sale_price'
				);
			}
			else {
				$meta_keys = array(
					'_price',
					'_min_variation_price',
					'_max_variation_price'
				);
			}

			$join .= " INNER JOIN $wpdb->postmeta AS pf_price ON $wpdb->posts.ID = pf_price.post_id AND pf_price.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $meta_keys ) ) . "') ";
			return $join;

		}

		public static function prdctfltr_add_variable_outofstock( $where, &$wp_query ) {

			if ( !empty( self::$settings['variable_outofstock'] ) ) {
				global $wpdb;
				$where .= " OR $wpdb->posts.ID IN ('" . implode( "','", array_map( 'esc_sql', self::$settings['variable_outofstock'] ) ) . "') ";
				remove_filter( 'posts_where' , 'prdctfltr_add_variable_outofstock' );
			}
			return $where;
		}

		public static function prdctfltr_price_filter( $where, &$wp_query ) {
			global $wpdb, $prdctfltr_global;

			$pf_activated = $prdctfltr_global['active_filters'];

			if ( isset( $pf_activated['sale_products'] ) && $pf_activated['sale_products'] == 'on' ) {

				$pf_sale = true;
				$pf_where_keys = array(
					array(
						'_sale_price','_min_variation_sale_price'
					),
					array(
						'_sale_price','_max_variation_sale_price'
					)
				);

			}
			else {

				$pf_sale = false;
				$pf_where_keys = array(
					array(
						'_price','_min_variation_price'
					),
					array(
						'_price','_max_variation_price'
					)
				);

			}

			$prices = self::get_prices( $wp_query->query_vars );
			$_min_price = isset( $prices['min_price'] ) ? $prices['min_price'] : null;
			$_max_price = isset( $prices['max_price'] ) ? $prices['max_price'] : null;

			if ( ( isset( $_min_price ) || isset( $_max_price ) ) !== false ) {
				if ( $_min_price < $_max_price ) {
					$where .= " AND ( ( pf_price.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $pf_where_keys[0] ) ) . "') AND pf_price.meta_value >= $_min_price AND pf_price.meta_value <= $_max_price AND pf_price.meta_value != '' ) OR ( pf_price.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $pf_where_keys[1] ) ) . "') AND pf_price.meta_value >= $_min_price AND pf_price.meta_value <= $_max_price AND pf_price.meta_value != '' ) ) ";
				}
			}
			else if ( $pf_sale === true ) {
				$where .= " AND ( pf_price.meta_key IN ('" . implode( "','", array_map( 'esc_sql', $pf_where_keys[0] ) ) . "') AND pf_price.meta_value > 0 ) ";
			}

			remove_filter( 'posts_where' , 'prdctfltr_price_filter' );

			return $where;
			
		}

		public static function get_prices( $query ) {
			global $prdctfltr_global;
			if ( empty( $query ) ) {
				global $wp_query;
				$query = $wp_query;
			}

			$pf_activated = $prdctfltr_global['active_filters'];

			$_min_price = null;
			if ( isset( $query['min_price'] ) ) {
				$_min_price =  $query['min_price'];
			}
			if ( isset( $pf_activated['rng_min_price'] ) ) {
				$_min_price = $pf_activated['rng_min_price'];
			}
			if ( isset( $pf_activated['min_price'] ) ) {
				$_min_price =  $pf_activated['min_price'];
			}

			$_max_price = null;
			if ( isset( $query['max_price'] ) ) {
				$_max_price =  $query['max_price'];
			}
			if ( isset( $pf_activated['rng_max_price'] ) ) {
				$_max_price = $pf_activated['rng_max_price'];
			}
			if ( isset( $pf_activated['max_price'] ) ) {
				$_max_price =  $pf_activated['max_price'];
			}

			if ( isset( $_min_price ) ) {
				$_min_price = floatval( $_min_price ) - apply_filters( 'prdctfltr_min_price_margin', 0.01 );
			}

			if ( isset( $_max_price ) ) {
				$_max_price = floatval( $_max_price ) + apply_filters( 'prdctfltr_max_price_margin', 0.01 );
			}

			return array(
				'min_price' => $_min_price,
				'max_price' => $_max_price
			);

		}

		function prdctrfltr_add_filter( $template, $slug, $name ) {

			if ( in_array( $slug, self::$settings['templates'] ) ) {

				$do = false;

				switch ( $slug ) {
					case 'loop/no-products-found.php' :
						if ( !isset( self::$settings['did_noproducts'] ) ) {
							$do = true;
						}
					break;
					case 'loop/pagination.php' :
						global $prdctfltr_global;
						if ( !isset( $prdctfltr_global['sc_init'] ) && self::$settings['wc_settings_prdctfltr_pagination_type'] !== 'default' && self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' && is_woocommerce() ) {
							$do = true;
						}
					break;
					case 'loop/orderby.php' :
					case 'loop/result-count.php' :
						if ( self::$settings['wc_settings_prdctfltr_enable'] == 'yes' && in_array( $slug, self::$settings['wc_settings_prdctfltr_enable_overrides'] ) ) {
							$do = true;
						}
						else if ( in_array( self::$settings['wc_settings_prdctfltr_enable'], array( 'no', 'action' ) ) && self::$settings['wc_settings_prdctfltr_default_templates'] == 'yes' ) {
							$slug = 'blank/' . $slug;
							$do = true;
						}
					break;
					default :
					break;
				}

				if ( $do ) {
					self::$settings['template'] = $slug;
					return self::$path . 'templates/getright.php';
				}

			}

			return $template;

		}

		function prdctfltr_redirect() {

			if ( !empty( $_REQUEST ) ) {

				if ( is_shop() || is_product_taxonomy() ) {

					$request = array();
					foreach( $_REQUEST as $k3 => $v3 ) {
						if ( taxonomy_exists( $k3 ) ) {
							if ( strpos( $v3, ' ' ) > -1 ) {
								$v3 = str_replace( ' ', '+', $v3 );
							}
						}
						else if ( $k3 == 's' ) {
							$v3 = str_replace( ' ', '%20', $v3 );
						}
						$request[$k3] = $v3;
					}

					global $wp_rewrite;

					$current = $GLOBALS['wp_the_query']->get_queried_object();
					if ( !isset( $current->taxonomy ) || !$current->taxonomy ) {
						if ( isset( $request['product_cat'] ) && $request['product_cat'] !== '' ) {
							$current = new stdClass();
							$current->taxonomy = 'product_cat';
							$current->slug = $request['product_cat'];
						}
					}

					if ( isset( $current->taxonomy ) ) {

						if ( isset( $request[$current->taxonomy] ) ) {

							if ( strpos( $request[$current->taxonomy], ',' ) || strpos( $request[$current->taxonomy], '+' ) || strpos ( $request[$current->taxonomy], ' ' ) ) {
								$rewrite = $wp_rewrite->get_extra_permastruct( $current->taxonomy );
								if ( $rewrite !== false ) {
									if ( strpos( $request[$current->taxonomy], ',' ) ) {
										$terms = explode( ',', $request[$current->taxonomy] );
									}
									else if ( strpos( $request[$current->taxonomy], '+' ) ) {
										$terms = explode( '+', $request[$current->taxonomy] );
									}
									else if ( strpos( $request[$current->taxonomy], ' ' ) ) {
										$terms = explode( ' ', $request[$current->taxonomy] );
									}

									foreach( $terms as $term ) {
										$checked = get_term_by( 'slug', $term, $current->taxonomy );
										if ( !is_wp_error( $checked ) ) {
	/*										if ( $checked->parent !== 0 ) {*/
												$parents[] = $checked->parent;
	/*										}*/
										}
									}

									$parent_slug = '';
									if ( isset( $parents ) ) {
										$parents_unique = array_unique( $parents );
										if ( count( $parents_unique ) == 1 && $parents_unique[0] !== 0 ) {
											$not_found = false;
											$parent_check = $parents_unique[0];
											while ( $not_found === false ) {
												if ( $parent_check !== 0 ) {
													$checked = get_term_by( 'id', $parent_check, $current->taxonomy );
													if ( !is_wp_error( $checked ) ) {
														$get_parent = $checked->slug;
														$parent_slug =  $get_parent . '/' . $parent_slug;
														if ( $checked->parent !== 0 ) {
															$parent_check = $checked->parent;
														}
														else {
															$not_found = true;
														}
													}
													else {
														$not_found = true;
													}
												}
												else {
													$not_found = true;
												}
											}
										}
									}

									$redirect = preg_replace( '/\?.*/', '', get_bloginfo( 'url' ) ) . '/' . str_replace( '%' . $current->taxonomy . '%', $parent_slug . $request[$current->taxonomy], $rewrite );
								}
							}
							else {
								$link = get_term_link( $request[$current->taxonomy], $current->taxonomy );
								if ( !is_wp_error( $link ) ) {
									$redirect = preg_replace( '/\?.*/', '', $link );
								}
							}

							if ( isset( $redirect ) ) {

								$redirect = untrailingslashit( $redirect );

								unset( $request[$current->taxonomy] );

								if ( !empty( $request ) ) {

									$req = '';

									foreach( $request as $k => $v ) {
										if ( $v == '' || in_array( $k, apply_filters( 'prdctfltr_block_request', array( 'woocs_order_emails_is_sending' ) ) ) ) {
											unset( $request[$k] );
											continue;
										}

										$req .= $k . '=' . $v . '&';
									}

									$redirect = $redirect . '/?' . $req;

									if ( substr( $redirect, -1 ) == '&' ) {
										$redirect = substr( $redirect, 0, -1 );
									}

									if ( substr( $redirect, -1 ) == '?' ) {
										$redirect = substr( $redirect, 0, -1 );
									}

								}

								if ( isset( $redirect ) ) {

									wp_redirect( $redirect, 302 );
									exit();

								}

							}

						}

					}

				}

			}
			else {
				$uri  = $_SERVER['REQUEST_URI'];
				$qPos = strpos( $uri, '?' );

				if ( $qPos === strlen( $uri ) - 1 ) {
					wp_redirect( substr( $uri, 0, $qPos ), 302 );
					exit();
				}
			}

		}

		public static function prdctrfltr_search_array( $array, $attrs ) {
			$results = array();
			$found = 0;

			foreach ( $array as $subarray ) {
				if ( isset( $subarray['attributes'] ) ) {
					foreach ( $attrs as $k => $v ) {
						if ( in_array( $v, $subarray['attributes'] ) ) {
							$found++;
						}
					}
				}
				if ( count($attrs) == $found ) {
					$results[] = $subarray;
				}

				if ( !empty( $results ) ) {
					return $results;
				}

				$found = 0;
			}

			return $results;
		}

		public static function prdctfltr_sort_terms_hierarchicaly( Array &$cats, Array &$into, $parentId = 0 ) {
			foreach ( $cats as $i => $cat ) {
				if ( $cat->parent == $parentId ) {
					$into[$cat->term_id] = $cat;
					unset($cats[$i]);
				}
			}
			foreach ( $into as $topCat ) {
				$topCat->children = array();
				self::prdctfltr_sort_terms_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
			}
		}

		public static function tofloat($num) {
			$num = substr( $num, -1 ) == '.' ?  substr( $num, 0 , -1 ) : $num;

			$dotPos = strrpos($num, '.');
			$commaPos = strrpos($num, ',');
			$sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

			if (!$sep) {
				return floatval(preg_replace("/[^0-9]/", "", $num));
			}

			return floatval(
				preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
				preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
			);
		}

		public static function prdctfltr_sort_terms_naturally( $terms, $args ) {

			$sort_terms = array();

			foreach($terms as $term) {
				$id = (string) self::tofloat( $term->name );
				$sort_terms[$id] = $term;
			}

			ksort( $sort_terms );

			if ( strtoupper( $args['order'] ) == 'DESC' ) {
				$sort_terms = array_reverse( $sort_terms );
			}

			return $sort_terms;

		}

		public static function prdctfltr_get_filter() {
			if ( !isset( self::$settings['get_filter'] ) ) {
				self::$settings['get_filter'] = current_filter();
				include( self::$dir . 'templates/product-filter.php' );
			}
		}

		public static function prdctfltr_get_between( $content, $start, $end ){
			$r = explode($start, $content);
			if (isset($r[1])){
				$r = explode($end, $r[1]);
				return $r[0];
			}
			return '';
		}

		public static function prdctfltr_utf8_decode( $str ) {
			$str = preg_replace( "/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode( $str ) );
			return html_entity_decode( $str, null, 'UTF-8' );
			//return sanitize_title( $str );
		}

		public static function prdctfltr_wpml_get_id( $id ) {
			if( function_exists( 'icl_object_id' ) ) {
				return icl_object_id( $id, 'page', true );
			}
			else {
				return $id;
			}
		}

		public static function prdctfltr_wpml_translate_terms( $curr_include, $attr ) {

			if ( empty( $curr_include ) ) {
				return $curr_include;
			}

			global $sitepress;

			if ( function_exists( 'icl_object_id' ) && is_object( $sitepress ) ) {

				$translated_include = array();

				$default_language = $sitepress->get_default_language();
				$current_language = $sitepress->get_current_language();

				foreach( $curr_include as $curr ) {
					$current_term = get_term_by( 'slug', $curr, $attr );

					if ( $current_term ) {

						$term_id = $current_term->term_id;
						if ( $default_language != $current_language ) {
							$term_id = icl_object_id( $term_id, $attr, false, $current_language );
						}

						$term = get_term( $term_id, $attr );
						$translated_include[] = $term->slug;

					}
				}

				return $translated_include;
			}
			else {
				$translated_include = array();
				foreach( $curr_include as $curr ) {
					$translated_include[] = sanitize_title( $curr );
				}
				return $translated_include;
			}
		}

		public static function prdctfltr_wpml_language() {

			if ( isset( self::$settings['wpml_language'] ) ) {
				return self::$settings['wpml_language'];
			}
			else {
				if ( class_exists( 'SitePress' ) ) {
					global $sitepress;

					$default_language = $sitepress->get_default_language();
					$current_language = $sitepress->get_current_language();

					if ( $default_language != $current_language ) {
						$language = sanitize_title( $current_language );
						self::$settings['wpml_language'] = $language;
						return $language;
					}
					else {
						return false;
					}

				}
				else {
					return false;
				}
			}

		}

		public static function prdctfltr_check_appearance() {

			if ( !empty( self::$settings['wc_settings_prdctfltr_showon_product_cat'] ) && !is_shop() && is_product_category() ) {
				if ( !is_product_category( self::$settings['wc_settings_prdctfltr_showon_product_cat'] ) ) {
					return false;
				}
			}

			$curr_shop_disable = get_option( 'wc_settings_prdctfltr_shop_disable', 'no' );

			if ( $curr_shop_disable == 'yes' && is_shop() && !is_product_category() ) {
				return false;
			}

			$curr_display_disable = get_option( 'wc_settings_prdctfltr_disable_display', array() );

			if ( !empty( $curr_display_disable ) ) {
				if ( is_shop() && !is_product_category() && in_array( get_option( 'woocommerce_shop_page_display' ), $curr_display_disable ) ) {
					return false;
				}

				if ( is_product_category() ) {

					$pf_queried_term = get_queried_object();
					$display_type = get_woocommerce_term_meta( $pf_queried_term->term_id, 'display_type', true );
					
					$display_type = ( $display_type == '' ? get_option( 'woocommerce_category_archive_display' ) : $display_type );

					if ( in_array( $display_type, $curr_display_disable ) ) {
						return false;
					}
				}
			}

		}

		public static function prdctfltr_get_styles() {

			global $prdctfltr_global;

			$curr_options = self::$settings['instance'];

			$curr_styles = array(
				( in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_arrow', 'pf_arrow_inline', 'pf_default', 'pf_default_inline', 'pf_select', 'pf_default_select', 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right', 'pf_fullscreen' ) ) ? $curr_options['wc_settings_prdctfltr_style_preset'] : 'pf_default' ),
				( $curr_options['wc_settings_prdctfltr_always_visible'] == 'no' && $curr_options['wc_settings_prdctfltr_disable_bar'] == 'no' || in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right', 'pf_fullscreen', 'pf_arrow', 'pf_arrow_inline' ) ) ? 'prdctfltr_slide' : 'prdctfltr_always_visible' ),
				( $curr_options['wc_settings_prdctfltr_click_filter'] == 'no' ? 'prdctfltr_click' : 'prdctfltr_click_filter' ),
				( $curr_options['wc_settings_prdctfltr_limit_max_height'] == 'no' ? 'prdctfltr_rows' : 'prdctfltr_maxheight' ),
				( $curr_options['wc_settings_prdctfltr_custom_scrollbar'] == 'no' ? 'prdctfltr_scroll_default' : 'prdctfltr_scroll_active' ),
				( $curr_options['wc_settings_prdctfltr_disable_bar'] == 'no' || in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ? '' : 'prdctfltr_disable_bar' ),
				$curr_options['wc_settings_prdctfltr_style_mode'],
				( $curr_options['wc_settings_prdctfltr_adoptive'] == 'no' ? '' : $curr_options['wc_settings_prdctfltr_adoptive_style'] ),
				$curr_options['wc_settings_prdctfltr_style_checkboxes'],
				( $curr_options['wc_settings_prdctfltr_show_search'] == 'no' ? '' : 'prdctfltr_search_fields' ),
				$curr_options['wc_settings_prdctfltr_style_hierarchy'],
				( $curr_options['wc_settings_prdctfltr_tabbed_selection'] == 'yes' ? 'prdctfltr_tabbed_selection' : '' ),
				( $curr_options['wc_settings_prdctfltr_adoptive'] !== 'no' && $curr_options['wc_settings_prdctfltr_adoptive_reorder'] == 'yes' ? 'prdctfltr_adoptive_reorder' : '' ),
				( $curr_options['wc_settings_prdctfltr_selected_reorder'] == 'yes' ? 'prdctfltr_selected_reorder' : '' )

			);

			if ( isset( self::$settings['instance']['step_filter'] ) ) {
				$curr_styles[] = 'prdctfltr_step_filter';
			}

			if ( $curr_options['wc_settings_prdctfltr_disable_reset'] == 'yes' ) {
				$curr_styles[] = 'pf_remove_clearall';
			}

			if ( in_array( $curr_options['wc_settings_prdctfltr_style_preset'], array( 'pf_arrow', 'pf_arrow_inline', 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right', 'pf_fullscreen' ) ) ) {
				self::$settings['instance']['wc_settings_prdctfltr_always_visible'] = 'no';
				self::$settings['instance']['wc_settings_prdctfltr_disable_bar'] = 'no';
			}
			if ( isset( $prdctfltr_global['mobile'] ) ) {
				$curr_styles[] = 'prdctfltr_mobile';
			}

			return $curr_styles;

		}

		public static function prdctfltr_get_settings() {

			global $prdctfltr_global;

			$pf_activated = ( isset ( $prdctfltr_global['active_filters'] ) && is_array( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array() );

			if ( isset ( $prdctfltr_global['active_permalinks'] ) && is_array( $prdctfltr_global['active_permalinks'] ) ) {
				$pf_activated = array_merge( $prdctfltr_global['active_permalinks'], $pf_activated );
			}

			if ( isset( $prdctfltr_global['preset'] ) && $prdctfltr_global['preset'] !== '' ) {
				$get_options = $prdctfltr_global['preset'];
			}

			if ( !isset( $prdctfltr_global['mobile'] ) ) {
				if ( !isset( $prdctfltr_global['disable_overrides'] ) || ( isset( $prdctfltr_global['disable_overrides'] ) && $prdctfltr_global['disable_overrides'] !== 'yes' ) ) {

					$overrides = get_option( 'prdctfltr_overrides', array() );

					$pf_check_overrides = self::$settings['wc_settings_prdctfltr_more_overrides'];

					foreach ( $pf_check_overrides as $pf_check_override ) {

						$override = ( isset( $pf_activated[$pf_check_override][0] ) ? $pf_activated[$pf_check_override][0] : '' );

						if ( $override !== '' ) {

							if ( term_exists( $override, $pf_check_override ) == null ) {
								continue;
							}

							if ( is_array( $overrides ) && isset( $overrides[$pf_check_override] ) ) {

								if ( array_key_exists( $override, $overrides[$pf_check_override] ) ) {
									$get_options = $overrides[$pf_check_override][$override];
									break;
								}

								else if ( is_taxonomy_hierarchical( $pf_check_override ) ) {
									$check = get_term_by( 'slug', $override, $pf_check_override );

									if ( $check->parent !== 0 ) {

										$parents = get_ancestors( $check->term_id, $pf_check_override );

										foreach( $parents as $parent_id ) {
											$check_parent = get_term_by( 'id', $parent_id, $pf_check_override );
											if ( array_key_exists( $check_parent->slug, $overrides[$pf_check_override]) ) {
												$get_options = $overrides[$pf_check_override][$check_parent->slug];
												break;
											}
										}

									}
								}

							}
						}
					}
				}

				if ( !isset( $get_options ) && self::$settings['wc_settings_prdctfltr_shop_page_override'] !== '' && is_shop() && !is_product_taxonomy() ) {
					$get_options = self::$settings['wc_settings_prdctfltr_shop_page_override'];
				}

			}
 
			if ( isset( $get_options ) && $get_options !== '' ) {
				$prdctfltr_global['preset'] = $get_options;
			}

			$name = isset( $get_options ) && is_string( $get_options ) && $get_options !== 'default' ? 'prdctfltr_wc_template_' . sanitize_title( $get_options ) : 'prdctfltr_wc_default';
			$name = ( $language = self::prdctfltr_wpml_language() ) === false ? $name : $name . '_' . $language;

			WC_Prdctfltr_Options::set_preset( $name );

			include( 'pf-options-array.php' );

			$wc_settings_prdctfltr_active_filters = get_option( 'wc_settings_prdctfltr_active_filters', array( 'sort','price','cat' ) );

			$wc_settings_prdctfltr_attributes = array();
			if ( is_array( $wc_settings_prdctfltr_active_filters ) ) {
				foreach ( $wc_settings_prdctfltr_active_filters as $k ) {
					if ( substr( $k, 0, 3 ) == 'pa_' ) {
						$wc_settings_prdctfltr_attributes[] = $k;
					}
				}
			}

			$options = array();
			$options['wc_settings_prdctfltr_active_filters'] = $wc_settings_prdctfltr_active_filters;

			foreach ( $options_std as $z => $x) {
				$options[$z] = get_option( $z, $x );
			}

			foreach ( $wc_settings_prdctfltr_attributes as $k => $attr ) {
				$options['wc_settings_prdctfltr_' . $attr . '_hierarchy'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_hierarchy', 'no' );
				$options['wc_settings_prdctfltr_' . $attr . '_hierarchy_mode'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_hierarchy_mode', 'no' );
				$options['wc_settings_prdctfltr_' . $attr . '_mode'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_mode', 'showall' );
				$options['wc_settings_prdctfltr_' . $attr . '_limit'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_limit', 'no' );
				$options['wc_settings_prdctfltr_' . $attr . '_none'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_none', 'no' );
				$options['wc_settings_prdctfltr_' . $attr . '_adoptive'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_adoptive', 'no' );
				$options['wc_settings_prdctfltr_' . $attr . '_selection'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_selection', 'no' );
				$options['wc_settings_prdctfltr_' . $attr . '_title'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_title', '' );
				$options['wc_settings_prdctfltr_' . $attr . '_description'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_description', '' );
				$options['wc_settings_prdctfltr_' . $attr . '_orderby'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_orderby', '' );
				$options['wc_settings_prdctfltr_' . $attr . '_order'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_order', '' );
				$options['wc_settings_prdctfltr_' . $attr . '_relation'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_relation', 'IN' );
				$options['wc_settings_prdctfltr_' . $attr] = get_option( 'wc_settings_prdctfltr_' . $attr, 'pf_attr_text' );
				$options['wc_settings_prdctfltr_' . $attr . '_multi'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_multi', 'no' );
				$options['wc_settings_prdctfltr_include_' . $attr] = get_option( 'wc_settings_prdctfltr_include_' . $attr, array() );
				$options['wc_settings_prdctfltr_' . $attr . '_term_customization'] = get_option( 'wc_settings_prdctfltr_' . $attr . '_term_customization', array() );
			}

			$options['preset'] = isset( $get_options ) && $get_options !== '' ? $get_options : 'default';

			if ( !isset( self::$settings['widget'] ) ) {
				if ( isset( $options['wc_settings_prdctfltr_style_mode'] ) ) {
					if ( in_array( $options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
						$options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
					}
					if ( $options['wc_settings_prdctfltr_style_preset'] == 'pf_select' && $options['wc_settings_prdctfltr_style_mode'] == 'pf_mod_row' ) {
						$options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
					}
				}
				else {
					$options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
				}
			}
			else {
				$options['wc_settings_prdctfltr_style_preset'] = self::$settings['widget']['style'];
				$options['wc_settings_prdctfltr_style_mode'] = 'pf_mod_multirow';
			}

			if ( $options['wc_settings_prdctfltr_loader'] !== 'none' && substr( $options['wc_settings_prdctfltr_loader'], 0, 4 ) !== 'css-' ) {
				$options['wc_settings_prdctfltr_loader'] = 'css-spinner-full';
			}

			$fc = ( count( $options['wc_settings_prdctfltr_active_filters'] ) );
			if ( isset( self::$settings['widget'] ) || in_array( $options['wc_settings_prdctfltr_style_preset'], array( 'pf_sidebar', 'pf_sidebar_right', 'pf_sidebar_css', 'pf_sidebar_css_right' ) ) ) {
				$columns = 1;
			}
			else {
				$columns = ( $fc < $options['wc_settings_prdctfltr_max_columns'] ? $fc : $options['wc_settings_prdctfltr_max_columns'] );
			}

			$options['wc_settings_prdctfltr_max_columns'] = intval( $columns );

			$options = apply_filters( 'prdctfltr_get_settings', $options );

			self::$settings['instance'] = isset( self::$settings['instance'] ) ? array_merge( self::$settings['instance'], $options ) : $options;

			if ( $options['wc_settings_prdctfltr_button_position'] == 'top' ) {
				add_action( 'prdctfltr_filter_form_before', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10 );
				remove_action( 'prdctfltr_filter_form_after', 'WC_Prdctfltr::prdctfltr_filter_buttons');
			}
			else if ( $options['wc_settings_prdctfltr_button_position'] == 'both' ) {
				add_action( 'prdctfltr_filter_form_after', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10 );
				add_action( 'prdctfltr_filter_form_before', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10 );
			}
			else {
				add_action( 'prdctfltr_filter_form_after', 'WC_Prdctfltr::prdctfltr_filter_buttons', 10 );
				remove_action( 'prdctfltr_filter_form_before', 'WC_Prdctfltr::prdctfltr_filter_buttons');
			}

			self::get_action();

			return $options;

		}

		public static function prdctfltr_get_terms( $term, $args ) {

			if ( !taxonomy_exists( $term ) ) {
				return array();
			}

			$args['hide_empty'] = self::$settings['wc_settings_prdctfltr_hideempty'];

			$orderby = isset( $args['orderby'] ) ? $args['orderby'] : wc_attribute_orderby( $term );
			$get_terms_args = array();

			switch ( $orderby ) {
				case 'name' :
					$get_terms_args['orderby']    = 'name';
					$get_terms_args['menu_order'] = false;
				break;
				case 'id' :
					$get_terms_args['orderby']    = 'id';
					$get_terms_args['order']      = 'ASC';
					$get_terms_args['menu_order'] = false;
				break;
				case '' :
				case 'menu_order' :
					unset( $args['orderby'] );
					unset( $args['order'] );
					$get_terms_args['menu_order'] = 'ASC';
					
				break;
			}

			$args = array_merge( $args, $get_terms_args );
			$args['taxonomy'] = $term;

			$exclude = apply_filters( 'prdctfltr_exclude_categories', array( 'uncategorized' ) );
			if ( !empty( $exclude ) && is_array( $exclude ) ) {
				$args['exclude'] = $exclude;
			}

			$terms = get_terms( $args );

			return $terms;

		}

		public static function prdctfltr_in_array( $needle, $haystack ) {
			return in_array( strtolower( $needle ), array_map( 'strtolower', $haystack ) );
		}

		public static function prdctfltr_filter_buttons() {

			$curr_elements = ( self::$settings['instance']['wc_settings_prdctfltr_active_filters'] !== NULL ? self::$settings['instance']['wc_settings_prdctfltr_active_filters'] : array() );

			global $prdctfltr_global;

			$pf_activated = ( isset( $prdctfltr_global['active_in_filter'] ) ? $prdctfltr_global['active_in_filter'] : array() );

			ob_start();
		?>
			<div class="prdctfltr_buttons">
			<?php
				if ( self::$settings['instance']['wc_settings_prdctfltr_click_filter'] == 'no' ) {
			?>
				<a class="button prdctfltr_woocommerce_filter_submit" href="#">
					<?php
						if ( self::$settings['instance']['wc_settings_prdctfltr_submit'] !== '' ) {
							echo self::$settings['instance']['wc_settings_prdctfltr_submit'];
						}
						else {
							esc_html_e( 'Filter selected', 'prdctfltr' );
						}
					?>
				</a>
			<?php
				}
				if ( self::$settings['instance']['wc_settings_prdctfltr_disable_sale'] == 'no' ) {
				?>
				<span class="prdctfltr_sale">
					<?php
					printf('<label%2$s><input name="sale_products" type="checkbox"%3$s/><span>%1$s</span></label>', esc_html__('Show only products on sale' , 'prdctfltr'), ( isset($pf_activated['sale_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($pf_activated['sale_products']) ? ' checked' : '' ) );
					?>
				</span>
				<?php
				}
				if ( self::$settings['instance']['wc_settings_prdctfltr_disable_instock'] == 'no' && !in_array('instock', $curr_elements) ) {
				?>
				<span class="prdctfltr_instock">
				<?php
					$curr_instock = self::$settings['wc_settings_prdctfltr_instock'];

					if ( $curr_instock == 'yes' ) {
						printf('<label%2$s><input name="instock_products" type="checkbox" value="both"%3$s/><span>%1$s</span></label>', esc_html__('Show out of stock products' , 'prdctfltr'), ( isset($pf_activated['instock_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($pf_activated['instock_products']) ? ' checked' : '' ) );
					}
					else {
						printf('<label%2$s><input name="instock_products" type="checkbox" value="in"%3$s/><span>%1$s</span></label>', esc_html__('In stock only' , 'prdctfltr'), ( isset($pf_activated['instock_products']) ? ' class="prdctfltr_active"' : '' ), ( isset($pf_activated['instock_products']) ? ' checked' : '' ) );
					}
			?>
				</span>
			<?php
				}
			?>
			</div>
		<?php
			$out = ob_get_clean();

			echo $out;
		}

		public static function get_customized_term( $value, $name, $count, $customization, $checked = '' ) {

			if ( !isset( $customization['style'] ) ) {
				return;
			}

			$key = 'term_' . $value;
			$tooltip = 'tooltip_' . $value;
			$input = '';

			if ( $checked !== '' ) {
				$input = '<input type="checkbox" value="' . $value . '"' . $checked . '/>';
			}

			$tip = ( $value == '' ? esc_html__( 'None', 'prdctfltr' ) : ( isset( $customization['settings'][$tooltip] ) ? $customization['settings'][$tooltip] : false ) );
			$count = $count !== false ? ' <span class="prdctfltr_customize_count">' . $count . '</span>' : '';

			switch ( $customization['style'] ) {
				case 'text':
					$insert = '<span class="prdctfltr_customize_' . $customization['settings']['type'] . ' prdctfltr_customize"><span class="prdctfltr_customize_name">' . $name . '</span>' . $count . ( $tip !== false ? '<span class="prdctfltr_tooltip"><span>' . $tip . '</span></span>' : '' ) . $input . '</span>';
				break;
				case 'color':
					if ( !isset( $customization['settings'][$key] ) ) {
						$customization['settings'][$key] = 'transparent';
					}
					$insert = '<span class="prdctfltr_customize_block prdctfltr_customize"><span class="prdctfltr_customize_color" style="background-color:' . $customization['settings'][$key] . ';"></span>' . $count . ( $tip !== false ? '<span class="prdctfltr_tooltip"><span>' . $tip . '</span></span>' : '' ) . $input . '<span class="prdctfltr_customization_search">' . $name . '</span></span>';
				break;
				case 'image':
					if ( !isset( $customization['settings'][$key] ) ) {
						$customization['settings'][$key] = self::$url_path . '/lib/images/pf-transparent.gif';
					}
					$insert = '<span class="prdctfltr_customize_block prdctfltr_customize"><span class="prdctfltr_customize_image"><img src="' . esc_url( $customization['settings'][$key] ) . '" /></span>' . $count . ( $tip !== false ? '<span class="prdctfltr_tooltip"><span>' . $tip . '</span></span>' : '' ) . $input . '<span class="prdctfltr_customization_search">' . $name . '</span></span>';
				break;
				case 'image-text':
					if ( !isset( $customization['settings'][$key] ) ) {
						$customization['settings'][$key] = self::$url_path . '/lib/images/pf-transparent.gif';
					}
					$insert = '<span class="prdctfltr_customize_block prdctfltr_customize"><span class="prdctfltr_customize_image_text"><img src="' . esc_url( $customization['settings'][$key] ) . '" /></span>' . $count . ( $tip !== false ? '<span class="prdctfltr_customize_image_text_tip">' . $tip . '</span><span class="prdctfltr_customization_search">' . $name . '</span>' : $name ) . $input . '</span>';
				break;
				case 'select':
					$insert = '<span class="prdctfltr_customize_select prdctfltr_customize">' . $input . '<span class="prdctfltr_customize_name">' . $name . '</span>' . $count . '</span>';
				break;
				default :
					if ( isset( $customization['settings'][$key] ) ) {
						$insert = $customization['settings'][$key];
					}
				break;
			}

			if ( !isset( $insert ) ) {
				$insert = '';
			}

			return $insert;

		}

		public static function add_customized_terms_css( $id, $customization ) {

			if ( $customization['settings']['type'] == 'border' ) {
				$css_entry = sprintf( '%1$s .prdctfltr_customize {border-color:%2$s;color:%2$s;}%1$s label.prdctfltr_active .prdctfltr_customize {border-color:%3$s;color:%3$s;}%1$s label.pf_adoptive_hide .prdctfltr_customize {border-color:%4$s;color:%4$s;}', '.' . $id, $customization['settings']['normal'], $customization['settings']['active'], $customization['settings']['disabled'] );
			}
			else if ( $customization['settings']['type'] == 'background' ) {
				$css_entry = sprintf( '%1$s .prdctfltr_customize {background-color:%2$s;}%1$s label.prdctfltr_active .prdctfltr_customize {background-color:%3$s;}%1$s label.pf_adoptive_hide .prdctfltr_customize {background-color:%4$s;}', '.' . $id, $customization['settings']['normal'], $customization['settings']['active'], $customization['settings']['disabled'] );
			}
			else if ( $customization['settings']['type'] == 'round' ) {
				$css_entry = sprintf( '%1$s .prdctfltr_customize {background-color:%2$s;border-radius:50%%;}%1$s label.prdctfltr_active .prdctfltr_customize {background-color:%3$s;}%1$s label.pf_adoptive_hide .prdctfltr_customize {background-color:%4$s;}', '.' . $id, $customization['settings']['normal'], $customization['settings']['active'], $customization['settings']['disabled'] );
			}
			else {
				$css_entry = '';
			}

			if ( !isset( self::$settings['css'] ) ) {
				self::$settings['css'] = $css_entry;
			}
			else {
				self::$settings['css'] .= $css_entry;
			}

		}

		public static function prdctfltr_add_css() {
			if ( isset( self::$settings['css'] ) ) {
?>
				<style type="text/css">
					<?php echo self::$settings['css']; ?>
				</style>
<?php
			}
		}

		public static function get_filter_customization( $filter, $key ) {

			if ( $key !== '' ) {
				$customization = get_option( $key, '' );
			}

			if ( empty( $customization ) ) {
				$customization = array();
			}

			return $customization;

		}

		function prdctfltr_analytics() {

			check_ajax_referer( 'prdctfltr_analytics', 'pf_nonce' );

			$data = isset( $_POST['pf_filters'] ) ? $_POST['pf_filters'] : '';
			$defaults = array(
				'sale_products' => 'default',
				'instock_products' => 'default',
				'orderby' => 'default'
			);
			$data = array_merge( $defaults, $data );

			if ( empty( $data ) ) {
				die();
				exit;
			}

			$forbidden = array( 'rng_min_price', 'rng_max_price', 'order' );
			foreach( $data as $k => $v ) {
				if ( in_array( $k, $forbidden ) ) {
					unset( $data[$k] );
				}
				else if ( substr( $k, 0, 4 ) == 'rng_' ) {
					unset( $data[$k] );
				}
				else if ( in_array( $k, array( 'min_price', 'max_price' ) ) ) {
					if ( isset( $data['min_price'] ) ) {
						$data['price'] = isset( $data['max_price'] ) ? $v . '-' . $data['max_price'] : $v . '+';
						unset( $data['min_price'] );
						if ( isset( $data['max_price'] ) ) {
							unset( $data['max_price'] );
						}
					}
					else {
						unset( $data['max_price'] );
					}
				}
			}

			$stats = get_option( 'wc_settings_prdctfltr_filtering_analytics_stats', array() );

			foreach( $data as $k =>$v ) {
				if ( strpos( $v, ',' ) ) {
					$selected = explode( ',', $v );
				}
				else if ( strpos( $v, '+' ) ) {
					$selected = explode( '+', $v );
				}
				else {
					$selected = array( $v );
				}
				foreach ( $selected as $k2 => $v2 ) {
					if ( array_key_exists( $k, $stats ) ) {
						if ( array_key_exists( $v2, $stats[$k] ) ) {
							$stats[$k][$v2] = $stats[$k][$v2] + 1;
						}
						else {
							$stats[$k][$v2] = 1;
						}
					}
					else {
						$stats[$k][$v2] = 1;
					}
				}

			}

			update_option( 'wc_settings_prdctfltr_filtering_analytics_stats', $stats );

			die( 'Updated!' );
			exit;
		}

		public static function get_term_count( $has, $of ) {
			if ( isset( self::$settings['instance']['wc_settings_prdctfltr_show_counts_mode'] ) ) {

				$set = self::$settings['instance']['wc_settings_prdctfltr_show_counts_mode'];

				switch( $set ) {
					case 'default' :
						return $has . apply_filters( 'prdctfltr_count_separator', '/' ) . $of;
					break;
					case 'count' :
						return $has;
					break;
					case 'total' :
						return $of;
					break;
					default:
						return '';
					break;
				}
			}
		}

		public static function nice_number( $n ) {
			$n = ( 0 + str_replace( ',', '', $n ) );

			if( !is_numeric( $n ) ){
				return false;
			}

			if ( $n > 1000000000000 ) {
				return round( ( $n / 1000000000000 ) , 1 ).' ' . esc_html__( 'trillion' , 'prdctfltr' );
			}
			else if ( $n > 1000000000 ) {
				return round( ( $n / 1000000000 ) , 1 ).' ' . esc_html__( 'billion' , 'prdctfltr' );
			}
			else if ( $n > 1000000 ) {
				return round( ( $n / 1000000 ) , 1 ).' ' . esc_html__( 'million' , 'prdctfltr' );
			}
			else if ( $n > 1000 ) {
				return round( ( $n / 1000 ) , 1 ).' ' . esc_html__( 'thousand' , 'prdctfltr' );
			}

			return number_format($n);
		}

		public static function tofloatprice( $num ) {
			$num = substr( $num, -1 ) == '.' ?  substr( $num, 0 , -1 ) : $num;
			$numDeci = apply_filters( 'wc_get_price_decimals', get_option( 'woocommerce_price_num_decimals', 2 ) );

			if ( $numDeci==0 ) {
				return floatval(preg_replace("/[^0-9]/", "", $num));
			}

			return floatval(
				preg_replace("/[^0-9]/", "", substr($num, 0, -$numDeci)) . '.' .
				preg_replace("/[^0-9]/", "", substr($num, -$numDeci+1))
			);
		}


		public static function price_to_float( $ptString ) {

			$ptString = str_replace( get_woocommerce_currency_symbol(), '', $ptString );

			$ptString = str_replace( '&nbsp;', '', $ptString );

			return self::tofloatprice( $ptString );

		}

		public static function get_filtered_price( $mode = 'yes' ) {

			global $wpdb, $prdctfltr_global;

			$tax_query  = ( $mode =='yes' && isset( $prdctfltr_global['tax_query'] ) ? $prdctfltr_global['tax_query'] : array() );

			if ( empty( $tax_query ) ) {
				global $wp_query;
				$tax_query = isset( $wp_query->query_vars['tax_query'] ) && !empty( $wp_query->query_vars['tax_query'] ) ? $wp_query->query_vars['tax_query'] : array();
			}

			$tax_query  = new WP_Tax_Query( $tax_query );

			$tax_query_sql  = $tax_query->get_sql( $wpdb->posts, 'ID' );
			$sql  = "SELECT min( FLOOR( price_meta.meta_value ) ) as min_price, max( CEILING( price_meta.meta_value ) ) as max_price FROM {$wpdb->posts} ";
			$sql .= " LEFT JOIN {$wpdb->postmeta} as price_meta ON {$wpdb->posts}.ID = price_meta.post_id " . $tax_query_sql['join'];
			$sql .= " 	WHERE {$wpdb->posts}.post_type = ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_post_type', array( 'product' ) ) ) ) . "')
						AND {$wpdb->posts}.post_status = 'publish'
						AND price_meta.meta_key IN ('" . implode( "','", array_map( 'esc_sql', apply_filters( 'woocommerce_price_filter_meta_keys', array( '_price' ) ) ) ) . "')
						AND price_meta.meta_value > '' ";
			$sql .= $tax_query_sql['where'];

/*			if ( $search = WC_Query::get_main_search_query_sql() ) {
				$sql .= ' AND ' . $search;
			}*/

			$prices = $wpdb->get_row( $sql );

			if ( intval( $prices->min_price ) < 0 && intval( $prices->max_price ) <= 0 && $mode == 'yes' ) {
				return self::get_filtered_price( 'no' );
			}
			else if ( intval( $prices->min_price ) >= 0 && intval( $prices->min_price ) < intval( $prices->max_price ) ) {
				return $prices;
			}
			else {

				$_min = floor( $wpdb->get_var(
					sprintf('
						SELECT min(meta_value + 0)
						FROM %1$s
						LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
						WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
						AND meta_value != ""
						', $wpdb->posts, $wpdb->postmeta, '_price', '_min_variation_price' )
					)
				);

				$_max = ceil( $wpdb->get_var(
					sprintf('
						SELECT max(meta_value + 0)
						FROM %1$s
						LEFT JOIN %2$s ON %1$s.ID = %2$s.post_id
						WHERE ( meta_key = \'%3$s\' OR meta_key = \'%4$s\' )
						AND meta_value != ""
						', $wpdb->posts, $wpdb->postmeta, '_price', '_max_variation_price' )
				) );

				$prices = new stdClass();

				if ( $_min >= 0 && $_min < $_max ) {
					$prices->min_price = $_min;
					$prices->max_price = $_max;
				}
				else {
					$prices->min_price = 0;
					$prices->max_price = 1000;
				}

				return $prices;
			}

		}

		function add_body_class( $classes ) {
			if ( is_shop() || is_product_taxonomy() ) {
				if ( self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes' ) {
					$classes[] = 'prdctfltr-ajax';
				}
				$classes[] = 'prdctfltr-shop';
			}

			return $classes;
		}

		function debug() {
			global $prdctfltr_global;
		?>
			<div class="prdctfltr_debug"><?php var_dump( $prdctfltr_global ); ?></div>
		<?php
		}

		public static function return_true() {
			return true;
		}

		function return_false() {
			return false;
		}

		public static function get_catalog_ordering_args() {

			$orderby_value = apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );

			return $orderby_value;

		}

		public static function get_taxonomy_terms( $terms, $customization, $curr_include, $curr_cat_selected, $output_terms, $parent = false ) {

			$curr_include = self::prdctfltr_wpml_translate_terms( $curr_include, self::$filter['slug'] );

			foreach ( $terms as $term ) {

				if ( !empty( $curr_include ) && !in_array( $term->slug, $curr_include ) ) {
					continue;
				}

				if ( !empty( $term->children ) ) {
					global $wpdb;

					$pf_childs = get_term_children( $term->term_id, self::$filter['slug'] );
					if ( empty( $pf_childs ) ) {
						$pf_parent = '
							SELECT SUM(%1$s.count) as count FROM %1$s
							WHERE %1$s.term_id = "' . $term->term_id . '"
							OR %1$s.parent = "' . $term->term_id . '"
						';
					}
					else {
						$pf_parent = '
							SELECT SUM(%1$s.count) as count FROM %1$s
							WHERE %1$s.term_id = "' . $term->term_id . '"
							OR %1$s.parent IN ("' . implode( '","', array_map( 'esc_sql', array_merge( $pf_childs, array( $term->term_id ) ) ) ) . '")
						';
					}


					$pf_count = $wpdb->get_var( sprintf( $pf_parent, $wpdb->term_taxonomy ) );

					$term_count_real = $pf_count;
				}
				else {
					$term_count_real = $term->count;
				}

				if ( !empty( self::$filter['customization']['options'] ) ) {

					$term_count = ( self::$settings['instance']['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? false : ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && self::$filter['adoptive'] == 'yes' &&  isset( $output_terms[self::$filter['slug']][$term->slug] ) && $output_terms[self::$filter['slug']][$term->slug] != $term_count_real ? self::get_term_count( $output_terms[self::$filter['slug']][$term->slug], $term_count_real ) : ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && self::$filter['adoptive'] == 'yes' && !empty( $output_terms[self::$filter['slug']] ) && !isset( $output_terms[self::$filter['slug']][$term->slug] ) ? self::get_term_count( 0, $term_count_real ) : $term_count_real ) ) );

					$curr_insert = self::get_customized_term( $term->slug, $term->name, $term_count, $customization );

				}
				else {

					$term_count = ( self::$settings['instance']['wc_settings_prdctfltr_show_counts'] == 'no' || $term_count_real == '0' ? '' : ' <span class="prdctfltr_count">' . ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && self::$filter['adoptive'] == 'yes' &&  isset( $output_terms[self::$filter['slug']][$term->slug] ) && $output_terms[self::$filter['slug']][$term->slug] != $term_count_real ? self::get_term_count( $output_terms[self::$filter['slug']][$term->slug], $term_count_real ) : ( self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && self::$filter['adoptive'] == 'yes' && !empty( $output_terms[self::$filter['slug']] ) && !isset( $output_terms[self::$filter['slug']][$term->slug] ) ? self::get_term_count( 0, $term_count_real ) : $term_count_real ) ) . '</span>' );

					$curr_insert = $term->name . $term_count;

				}

				$pf_adoptive_class = '';

				if ( self::$filter['adoptive'] == 'yes' && isset( $output_terms[self::$filter['slug']] ) && !empty( $output_terms[self::$filter['slug']] ) && !array_key_exists( $term->slug, $output_terms[self::$filter['slug']] ) ) {
					$pf_adoptive_class = ' pf_adoptive_hide';
				}

				printf('<label class="%6$s%4$s%7$s%8$s"><input type="checkbox" value="%1$s"%3$s%9$s /><span>%2$s</span>%5$s</label>', $term->slug, $curr_insert, ( in_array( $term->slug, $curr_cat_selected ) ? ' checked' : '' ), ( in_array( $term->slug, $curr_cat_selected ) ? ' prdctfltr_active' : '' ), ( !empty( $term->children ) ? '<i class="prdctfltr-plus"></i>' : '' ), $pf_adoptive_class, ( !empty( $term->children ) && in_array( $term->slug, $curr_cat_selected ) ? ' prdctfltr_clicked' : '' ), ' prdctfltr_ft_' . sanitize_title( $term->slug ), ( $parent !== false ? ' data-parent="' . $parent . '"' : '' ) );

				if ( isset( self::$filter['hierarchy'] ) && self::$filter['hierarchy'] == 'yes' && !empty( $term->children ) ) {

					printf( '<div class="prdctfltr_sub" data-sub="%1$s">', $term->slug );

					self::get_taxonomy_terms( $term->children, $customization, $curr_include, $curr_cat_selected, $output_terms, $term->slug );

					printf( '</div>' );

				}

			}

		}

		function wcml_currency( $actions ) {
			$actions[] = 'prdctfltr_respond_550';
			return $actions;
		}

		public static function get_dynamic_filter_title( $type, $attr, $p, $title ) {

			if ( self::$settings['instance']['wc_settings_prdctfltr_' . $type . '_filters']['pfr_title'][$p] == 'false' ) {
				return '';
			}

			$args = apply_filters( 'prdctfltr_filter_title_args', array(
				'filter' => 'rng_' . $attr,
				'title' => $title,
				'before' => '<span class="prdctfltr_' . ( isset( self::$settings['widget'] ) ? 'widget' : 'regular' ) . '_title">',
				'after' => '</span>',
			) );

			extract( $args );

			echo $before;


			if ( self::$settings['instance']['wc_settings_prdctfltr_' . $type . '_filters']['pfr_title'][$p] != '' ) {
				echo self::$settings['instance']['wc_settings_prdctfltr_' . $type . '_filters']['pfr_title'][$p];
			}
			else {
				if ( $attr !== 'price' && taxonomy_exists( $attr ) ) {
					$taxonomy = get_taxonomy( $attr );
					echo $taxonomy->labels->name;
				}
				else {
					echo $title;
				}
			}
		?>
			<i class="prdctfltr-down"></i>
		<?php
			echo $after;

		}

		public static function get_filter_taxonomy_title() {

			if ( self::$filter['title'] == 'false' ) {
				return '';
			}

			global $prdctfltr_global;

			$args = apply_filters( 'prdctfltr_filter_title_args', array(
				'before' => '<span class="prdctfltr_' . ( isset( self::$settings['widget'] ) ? 'widget' : 'regular' ) . '_title">',
				'after' => '</span>',
			) );

			extract( $args );

			echo $before;

			if ( self::$filter['title'] != '' ) {
				echo self::$filter['title'];
			}
			else {
				if ( substr( self::$filter['slug'], 0, 3 ) == 'pa_' ) {
					echo wc_attribute_label( self::$filter['slug'] );
				}
				else {
					if ( self::$filter['slug'] == 'product_cat' ) {
						esc_html_e( 'Categories', 'prdctfltr' );
					}
					else if ( self::$filter['slug'] == 'product_tag') {
						esc_html_e( 'Tags', 'prdctfltr' );
					}
					else if ( self::$filter['slug'] == 'characteristics' ) {
						esc_html_e( 'Characteristics', 'prdctfltr' );
					}
					else {
						$term = get_taxonomy( self::$filter['slug'] );
						echo $term->label;
					}
				}
			}
		?>
			<i class="prdctfltr-down"></i>
		<?php
			echo $after;

		}

		public static function get_filter_title( $filter, $title, $option, $terms = array() ) {

			if ( $option !== 'meta' && self::$settings['instance']['wc_settings_prdctfltr_' . $option . '_title'] == 'false' ) {
				return '';
			}

			$args = apply_filters( 'prdctfltr_filter_title_args', array(
				'filter' => $filter,
				'title' => $title,
				'before' => '<span class="prdctfltr_' . ( isset( self::$settings['widget'] ) ? 'widget' : 'regular' ) . '_title">',
				'after' => '</span>',
			) );

			extract( $args );

			echo $before;

			if ( $option !== 'meta' && self::$settings['instance']['wc_settings_prdctfltr_' . $option . '_title'] != '' ) {
				echo self::$settings['instance']['wc_settings_prdctfltr_' . $option . '_title'];
			}
			else {
				echo $title;
			}
		?>
			<i class="prdctfltr-down"></i>
		<?php
			echo $after;

		}

		public static function catalog_instock( $get = '' ) {

			$array = apply_filters( 'prdctfltr_catalog_instock', array(
				'both'    => esc_html__( 'All Products', 'prdctfltr' ),
				'in'  => esc_html__( 'In Stock', 'prdctfltr' ),
				'out' => esc_html__( 'Out Of Stock', 'prdctfltr' )
			) );

			if ( $get !== '' && array_key_exists( $get, $array ) ) {
				return $array[$get];
			}

			if ( $get == '' ) {
				return $array;
			}

		}

		public static function catalog_ordering( $get = '' ) {

			$pf_order_default = array(
				''                 => apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) ),
				'comment_count'    => esc_html__( 'Review Count', 'prdctfltr' ),
				'popularity'       => esc_html__( 'Popularity', 'prdctfltr' ),
				'rating'           => esc_html__( 'Average rating', 'prdctfltr' ),
				'date'             => esc_html__( 'Newness', 'prdctfltr' ),
				'price'            => esc_html__( 'Price: low to high', 'prdctfltr' ),
				'price-desc'       => esc_html__( 'Price: high to low', 'prdctfltr' ),
				'rand'             => esc_html__( 'Random Products', 'prdctfltr' ),
				'title'            => esc_html__( 'Product Name', 'prdctfltr' )
			);

			if ( !empty( self::$settings['instance']['wc_settings_prdctfltr_include_orderby'] ) ) {

				foreach ( $pf_order_default as $k => $v ) {
					if ( $k !== '' && !in_array( $k, self::$settings['instance']['wc_settings_prdctfltr_include_orderby'] ) ) {
						unset( $pf_order_default[$k] );
					}
				}
			}

			$array = apply_filters( 'prdctfltr_catalog_orderby', $pf_order_default );

			if ( $get !== '' && array_key_exists( $get, $array ) ) {
				return $array[$get];
			}

			if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' && array_key_exists( 'rating', $array ) ) {
				unset( $array['rating'] );
			}
			if ( self::$settings['instance']['wc_settings_prdctfltr_orderby_none'] == 'yes' && array_key_exists( '', $array ) ) {
				unset( $array[''] );
			}

			if ( $get == '' ) {
				return $array;
			}

		}

		public static function get_customization( $option ) {

			if ( $option !== '' ) {

				$get_customization = get_option( $option, '' );

				if ( $get_customization !== '' && isset( $get_customization['style'] ) ) {
					$customization_class = ' prdctfltr_terms_customized  prdctfltr_terms_customized_' . $get_customization['style'] . ' ' . $option;

					$customization = $get_customization;
					if ( $customization['style'] == 'text' ) {
						WC_Prdctfltr::add_customized_terms_css( $option, $customization );
					}
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
				$customization_class = ' prdctfltr_text';
			}

			return array(
				'options' => $customization,
				'class' => $customization_class
			);

		}

		public static function check_meta_settings() {
			return array(
				'pfm_title' => '',
				'pfm_description' => '',
				'pfm_key' => '',
				'pfm_compare' => '',
				'pfm_type' => '',
				'pfm_term_customization' => '',
				'pfm_filter_customization' => ''
			);
		}

		public static function check_advanced_settings() {
			return array(
				'pfa_title' => '',
				'pfa_description' => '',
				'pfa_include' => array(),
				'pfa_orderby' => 'name',
				'pfa_order' => 'ASC',
				'pfa_multiselect' => 'no',
				'pfa_relation' => 'IN',
				'pfa_adoptive' => 'no',
				'pfa_selection' => 'no',
				'pfa_none' => 'no',
				'pfa_hierarchy' => 'no',
				'pfa_hierarchy_mode' => 'no',
				'pfa_mode' => 'showall',
				'pfa_style' => 'pf_attr_text',
				'pfa_limit' => 0,
				'pfm_term_customization' => ''
			);
		}

		public static function get_range_value( $rng_name, $val ) {

			$ranges = self::$settings['instance']['wc_settings_prdctfltr_range_filters'];

			if ( isset( $ranges['pfr_taxonomy'] ) && is_array(  $ranges['pfr_taxonomy'] ) ) {
				foreach( $ranges['pfr_taxonomy'] as $k => $v ) {
					if ( $v == $rng_name ) {
						if ( isset( $ranges['pfr_custom'][$k] ) && is_string( $ranges['pfr_custom'][$k] ) && substr( $ranges['pfr_custom'][$k], 0, 1 ) == '{' ) {
							$decode = json_decode( $ranges['pfr_custom'][$k], true );
							if ( isset( $decode[$val] ) ) {
								return $decode[$val];
							}
						}
					}
				}
			}

			return '';

		}

		public static function check_range_settings() {
			return array(
				'pfr_title' => '',
				'pfr_description' => '',
				'pfr_taxonomy' => '',
				'pfr_include' => array(),
				'pfr_orderby' => 'name',
				'pfr_order' => 'ASC',
				'pfr_style' => 'no',
				'pfr_grid' => 'no',
				'pfr_adoptive' => 'no',
				'pfr_custom' => ''
			);
		}

		public static function make_filter() {

			global $wp_query;

			if ( isset( self::$settings['sc_instance'] ) ) {
				$pf_paged = self::$settings['sc_instance']['paged'];
				$pf_per_page = self::$settings['sc_instance']['per_page'];
				$pf_total = self::$settings['sc_instance']['total'];
				$pf_first = self::$settings['sc_instance']['first'];
				$pf_last = self::$settings['sc_instance']['last'];
				$pf_request = self::$settings['sc_instance']['request'];
			}
			else if ( is_shop() || is_product_taxonomy() || is_search() || isset( $wp_query->query_vars['wc_query'] ) && $wp_query->query_vars['wc_query'] == 'product_query' ) {
				$pf_paged = max( 1, $wp_query->get( 'paged' ) );
				$pf_per_page = $wp_query->get( 'posts_per_page' );
				$pf_total = $wp_query->found_posts;
				$pf_first = ( $pf_per_page * $pf_paged ) - $pf_per_page + 1;
				$pf_last = $wp_query->get( 'offset' ) > 0 ? min( $pf_total, $wp_query->get( 'offset' ) + $wp_query->get( 'posts_per_page' ) ) : min( $pf_total, $wp_query->get( 'posts_per_page' ) * $pf_paged );
				$pf_request = $wp_query->request;

			}
			else {

				$pf_paged = 1;
				$pf_per_page = 10;
				$pf_total = 0;
				$pf_first = 0;
				$pf_last = 0;
				$pf_request = '';

			}

			self::$settings['instance'] = array(
				'paged'     => $pf_paged,
				'per_page'  => $pf_per_page,
				'total'     => $pf_total,
				'first'     => $pf_first,
				'last'      => $pf_last,
				'request'   => $pf_request,
				'activated' => array()
			);

			self::prdctfltr_get_settings();

		}

		public static function get_top_bar_showing() {

			$pf_step_filter = isset( self::$settings['instance']['step_filter'] ) ? 'yes' : '';
		?>
			<span class="prdctfltr_showing">
		<?php
			if ( self::$settings['instance']['wc_settings_prdctfltr_noproducts'] !== '' && self::$settings['instance']['total'] == 0 ) {
				echo esc_html__( 'No products found!', 'prdctfltr' );
			}
			else if ( self::$settings['instance']['total'] == 0 ) {
				echo esc_html__( 'No products found!', 'prdctfltr' );
			}
			else if ( self::$settings['instance']['total'] == 1 ) {
				if ( $pf_step_filter !== '' ) {
					echo esc_html__( 'Found a single result', 'prdctfltr' );
				}
				else {
					echo esc_html__( 'Showing the single result', 'prdctfltr' );
				}
			}
			else if ( self::$settings['instance']['total'] <= self::$settings['instance']['per_page'] || -1 == self::$settings['instance']['per_page'] ) {
				if ( $pf_step_filter !== '' ) {
					echo esc_html__( 'Found', 'prdctfltr') . ' ' . self::$settings['instance']['total'] . ' ' . esc_html__( 'results', 'prdctfltr' );
				}
				else {
					echo esc_html__( 'Showing all', 'prdctfltr') . ' ' . self::$settings['instance']['total'] . ' ' . esc_html__( 'results', 'prdctfltr' );
				}
			}
			else {
				if ( $pf_step_filter !== '' ) {
					echo esc_html__( 'Found', 'prdctfltr' ) . ' ' . self::$settings['instance']['total'] . ' ' . esc_html__( 'results', 'prdctfltr' );
				}
				else {
					echo esc_html__( 'Showing', 'prdctfltr' ) . ' ' . self::$settings['instance']['first'] . ' - ' . self::$settings['instance']['last'] . ' ' . esc_html__( 'of', 'prdctfltr' ) . ' ' . self::$settings['instance']['total'] . ' ' . esc_html__( 'results', 'prdctfltr' );
				}
			}
		?>
			</span>
		<?php
			if ( function_exists( 'wc_set_loop_prop' ) ) {
				wc_set_loop_prop( 'total', self::$settings['instance']['total'] );
				wc_set_loop_prop( 'per_page', self::$settings['instance']['per_page'] );			
			}

		}

		function get_top_bar() {

			if ( !isset( self::$settings['widget'] ) && self::$settings['instance']['wc_settings_prdctfltr_disable_bar'] == 'no' ) {

				$icon = self::$settings['instance']['wc_settings_prdctfltr_icon'];
			?>
				<span class="prdctfltr_filter_title">
					<a class="prdctfltr_woocommerce_filter<?php echo ' pf_ajax_' . ( self::$settings['instance']['wc_settings_prdctfltr_loader'] !== '' ? self::$settings['instance']['wc_settings_prdctfltr_loader'] : 'css-spinner-full-01' ); ?>" href="#"><i class="<?php echo ( $icon == '' ? 'prdctfltr-bars' : $icon ); ?><?php echo ( substr( self::$settings['instance']['wc_settings_prdctfltr_loader'], 0, 4 ) == 'css-' ? ' ' . self::$settings['instance']['wc_settings_prdctfltr_loader'] : '' ); ?>"></i></a>
					<span class="prdctfltr_woocommerce_filter_title">
				<?php
					if ( self::$settings['instance']['wc_settings_prdctfltr_title'] !== '' ) {
						echo self::$settings['instance']['wc_settings_prdctfltr_title'];
					}
					else {
						esc_html_e( 'Filter Products', 'prdctfltr' );
					}
				?>
					</span>
				<?php
					self::get_top_bar_showing();
				?>
				</span>
			<?php
			}

		}

		public static function get_action_tag() {

			$action = isset( self::$settings['instance']['action'] ) ? self::$settings['instance']['action'] : '';

			return apply_filters( 'prdctfltr_filter_action', $action );

		}

		public static function get_action() {

			global $prdctfltr_global;

			$action = '';

			if ( isset( self::$settings['instance']['wc_settings_prdctfltr_custom_action'] ) && !empty( self::$settings['instance']['wc_settings_prdctfltr_custom_action'] ) ) {
				$action = ' action="' . esc_url( self::$settings['instance']['wc_settings_prdctfltr_custom_action'] ) . '"';
			}

			if ( isset( $prdctfltr_global['action'] ) && $prdctfltr_global['action'] !== '' ) {
				$action = ' action="' . esc_url( $prdctfltr_global['action'] ) . '"';
			}

			if ( $action == '' ) {
				if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() || is_home() ) {
					if ( self::$settings['wc_settings_prdctfltr_force_action'] == 'yes' ) {
						if ( is_product_taxonomy() ) {
							$action = ' action=""';
						}
						else {
							$action = ' action="' . get_the_permalink( self::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) ) . '"';
						}
					}
					else {
						$action = ' action="' . get_the_permalink( self::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) ) . '"';
					}
				}
				else if ( is_page() ) {
					global $wp;
					if ( self::$settings['permalink_structure'] == '' ) {
						$action = ' action="' . esc_url( remove_query_arg( array( 'page', 'paged' ), esc_url( add_query_arg( $wp->query_string, '', home_url( $wp->request ) ) ) ) ) . '"';
					} else {
						$action = ' action="' . preg_replace( '%\/page/[0-9]+%', '', home_url( $wp->request ) ) . '"';
					}
				}
				else {
					$action = ' action="' . get_the_permalink( self::prdctfltr_wpml_get_id( wc_get_page_id( 'shop' ) ) ) . '"';
				}
			}
			else {
				self::$settings['instance']['step_filter'] = true;
			}

			self::$settings['instance']['action'] = $action;

		}

		public static function get_meta_compare( $compare ) {

			switch ( $compare ) {

				case 11 :
				case '!=' :
					return $compare !== '!=' ? '!=' : 11;
				break;

				case 12 :
				case '>' :
					return $compare !== '>' ? '>' : 12;
				break;

				case 13 :
				case '<' :
					return $compare !== '<' ? '<' : 13;
				break;

				case 14 :
				case '>=' :
					return $compare !== '>=' ? '>=' : 14;
				break;

				case 15 :
				case '<=' :
					return $compare !== '<=' ? '<=' : 15;
				break;

				case 16 :
				case 'LIKE' :
					return $compare !== 'LIKE' ? 'LIKE' : 16;
				break;

				case 17 :
				case 'NOT LIKE' :
					return $compare !== 'NOT LIKE' ? 'NOT LIKE' : 17;
				break;

				case 18 :
				case 'IN' :
					return $compare !== 'IN' ? 'IN' : 18;
				break;

				case 19 :
				case 'NOT IN' :
					return $compare !== 'NOT IN' ? 'NOT IN' : 19;
				break;

				case 20 :
				case 'EXISTS' :
					return $compare !== 'EXISTS' ? 'EXISTS' : 20;
				break;

				case 21 :
				case 'NOT EXISTS' :
					return $compare !== 'NOT EXISTS' ? 'NOT EXISTS' : 21;
				break;

				case 22 :
				case 'NOT EXISTS' :
					return $compare !== 'NOT EXISTS' ? 'NOT EXISTS' : 22;
				break;

				case 23 :
				case 'BETWEEN' :
					return $compare !== 'BETWEEN' ? 'BETWEEN' : 23;
				break;

				case 24 :
				case 'NOT BETWEEN' :
					return $compare !== 'NOT BETWEEN' ? 'NOT BETWEEN' : 24;
				break;

				case 10 :
				case '=' :
				default :
					return $compare !== '=' ? '=' : 10;
				break;
			}

		}

		public static function get_meta_type( $type ) {

			switch ( $type ) {

				case 1 :
				case 'BINARY' :
					return $type !== 'BINARY' ? 'BINARY' : 1;
				break;

				case 2 :
				case 'CHAR' :
					return $type !== 'CHAR' ? 'CHAR' : 2;
				break;

				case 3 :
				case 'DATE' :
					return $type !== 'DATE' ? 'DATE' : 3;
				break;

				case 4 :
				case 'DATETIME' :
					return $type !== 'DATETIME' ? 'DATETIME' : 4;
				break;

				case 5 :
				case 'DECIMAL' :
					return $type !== 'DECIMAL' ? 'DECIMAL' : 5;
				break;

				case 6 :
				case 'SIGNED' :
					return $type !== 'SIGNED' ? 'SIGNED' : 6;
				break;

				case 7 :
				case 'UNSIGNED' :
					return $type !== 'UNSIGNED' ? 'UNSIGNED' : 7;
				break;

				case 8 :
				case 'TIME' :
					return $type !== 'TIME' ? 'TIME' : 8;
				break;

				case 0 :
				case 'NUMERIC' :
				default :
					return $type !== 'NUMERIC' ? 'NUMERIC' : 0;
				break;
			}

		}

		public static function build_meta_key( $key, $compare, $type ) {
			return apply_filters( 'prdctfltr_meta_key_prefix', 'mta_' ) . $key . '_' . self::get_meta_type( $type ) . '_' . self::get_meta_compare( $compare );
		}

		public static function get_wrapper_tag_parameters() {
			echo 'class="prdctfltr_filter_wrapper prdctfltr_columns_' . self::$settings['instance']['wc_settings_prdctfltr_max_columns'] . ( count( self::$settings['instance']['wc_settings_prdctfltr_active_filters'] ) == 1 ? ' prdctfltr_single_filter' : '' ) . '" data-columns="' . self::$settings['instance']['wc_settings_prdctfltr_max_columns'] . '"';
		}

		public static function get_filter_tag_parameters() {

			$styles = self::prdctfltr_get_styles();

			echo 'class="prdctfltr_wc prdctfltr_woocommerce woocommerce ' . ( isset( self::$settings['widget'] ) ? 'prdctfltr_wc_widget' : 'prdctfltr_wc_regular' ) . ' ' . preg_replace( '/\s+/', ' ', implode( $styles, ' ' ) ) . '"';
			echo self::$settings['wc_settings_prdctfltr_use_ajax'] == 'yes'? ' data-page="' . self::$settings['instance']['paged'] . '"' : '';
			echo ' data-loader="' . self::$settings['instance']['wc_settings_prdctfltr_loader'] . '"';
			echo ( WC_Prdctfltr::prdctfltr_wpml_language() !== false ? ' data-lang="' . ICL_LANGUAGE_CODE . '"' : '' );
			echo self::$settings['wc_settings_prdctfltr_use_analytics'] == 'yes' ? ' data-nonce="' . $nonce = wp_create_nonce( 'prdctfltr_analytics' ) . '"' : '';
			global $prdctfltr_global;
			if ( isset( $prdctfltr_global['mobile'] ) ) {
				 echo ' data-mobile="' . self::$settings['instance']['wc_settings_prdctfltr_mobile_resolution'] . '"';
			}
			echo ' data-id="' . $prdctfltr_global['unique_id'] . '"';

		}

		public static function prdctfltr_switch_thumbnails_350( $image, $product, $size, $attr, $placeholder ) {

			global $prdctfltr_global;

			if ( !empty( $prdctfltr_global['f_attrs'] ) || isset( $prdctfltr_global['unique_id'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['attribute'] ) ) {

				global $product;

				if ( method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) {

					if ( empty( self::$settings['v_attr'] ) ) {
						$pf_activated = isset( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array();
						$pf_permalinks = isset( $prdctfltr_global['active_permalinks'] ) ? $prdctfltr_global['active_permalinks'] : array();

						if ( isset( $prdctfltr_global['unique_id'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['attribute'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['filter'] ) ) {
							$atts = $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'];

							if ( !empty( $atts ) ) {
								$pf_permalinks[strstr( $atts['attribute'], 'pa_' ) ? sanitize_title( $atts['attribute'] ) : 'pa_' . sanitize_title( $atts['attribute'] )] = array_map( 'sanitize_title', explode( ',', $atts['filter'] ) );
							}
						}

						$pf_activated = array_merge( $pf_activated, $pf_permalinks );

						if ( !empty( $pf_activated ) ) {
							$attrs = array();
							foreach( $pf_activated as $k => $v ){
								if ( substr( $k, 0, 3 ) == 'pa_' ) {
									$attrs = $attrs + array(
										$k => $v[0]
									);
								}
							}
							self::$settings['v_attr'] = $attrs;
						}
					}


					if ( !empty( self::$settings['v_attr'] ) ) {

						$variables = $product->get_variation_attributes();
						$varIntersect = array_intersect_key( self::$settings['v_attr'], $variables );

						if ( !empty( $varIntersect ) ) {

							foreach ( $product->get_children() as $child_id ) {

								$variation = wc_get_product( $child_id );

								$curr_var_set[$child_id]['attributes'] = $variation->get_variation_attributes();
								$curr_var_set[$child_id]['variation_id'] = $variation->get_id();
							}

							$found = WC_Prdctfltr::prdctrfltr_search_array( $curr_var_set, self::$settings['v_attr'] );
						}

					}
				}
			}

		/*	if ( !empty( $found ) && has_post_thumbnail( $found[0]['variation_id'] ) ) {
				return wp_get_attachment_image_src( get_post_thumbnail_id( $found[0]['variation_id'] ), $size, false );
			}*/
			if ( !empty( $found ) && has_post_thumbnail( $found[0]['variation_id'] ) ) {
				
				return str_replace( preg_replace('/.[^.]*$/', '', wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ), 'full', false ) ) , preg_replace('/.[^.]*$/', '', wp_get_attachment_image_src( get_post_thumbnail_id( $found[0]['variation_id'] ), 'full', false ) ), $image );
			}

			return $image;
		}

		public static function prdctfltr_switch_thumbnails( $html, $post_ID, $post_thumbnail_id, $size, $attr ) {

			global $prdctfltr_global;

			if ( !empty( $prdctfltr_global['f_attrs'] ) || isset( $prdctfltr_global['unique_id'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['attribute'] ) ) {

				global $product;

				if ( method_exists( $product, 'is_type' ) && $product->is_type( 'variable' ) ) {

					if ( empty( self::$settings['v_attr'] ) ) {
						$pf_activated = isset( $prdctfltr_global['active_filters'] ) ? $prdctfltr_global['active_filters'] : array();
						$pf_permalinks = isset( $prdctfltr_global['active_permalinks'] ) ? $prdctfltr_global['active_permalinks'] : array();

						if ( isset( $prdctfltr_global['unique_id'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['attribute'] ) && isset( $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts']['filter'] ) ) {
							$atts = $prdctfltr_global['pagefilters'][$prdctfltr_global['unique_id']]['atts'];

							if ( !empty( $atts ) ) {
								$pf_permalinks[strstr( $atts['attribute'], 'pa_' ) ? sanitize_title( $atts['attribute'] ) : 'pa_' . sanitize_title( $atts['attribute'] )] = array_map( 'sanitize_title', explode( ',', $atts['filter'] ) );
							}
						}

						$pf_activated = array_merge( $pf_activated, $pf_permalinks );

						if ( !empty( $pf_activated ) ) {
							$attrs = array();
							foreach( $pf_activated as $k => $v ){
								if ( substr( $k, 0, 3 ) == 'pa_' ) {
									$attrs = $attrs + array(
										$k => $v[0]
									);
								}
							}
							self::$settings['v_attr'] = $attrs;
						}
					}


					if ( !empty( self::$settings['v_attr'] ) ) {

						$variables = $product->get_variation_attributes();
						$varIntersect = array_intersect_key( self::$settings['v_attr'], $variables );

						if ( !empty( $varIntersect ) ) {

							foreach ( $product->get_children() as $child_id ) {

								$variation = wc_get_product( $child_id );

								$curr_var_set[$child_id]['attributes'] = $variation->get_variation_attributes();
								$curr_var_set[$child_id]['variation_id'] = $variation->get_id();
							}

							$found = WC_Prdctfltr::prdctrfltr_search_array( $curr_var_set, self::$settings['v_attr'] );
						}

					}
				}
			}

			if ( !empty( $found ) && has_post_thumbnail( $found[0]['variation_id'] ) ) {
				return str_replace( preg_replace('/.[^.]*$/', '', wp_get_attachment_image_src( $post_thumbnail_id, 'full', false ) ), preg_replace('/.[^.]*$/', '', wp_get_attachment_image_src( get_post_thumbnail_id( $found[0]['variation_id'] ), 'full', false ) ), $html );
			}

			return $html;

		}

		public static function version_check( $version = '3.0.0' ) {
			if ( class_exists( 'WooCommerce' ) ) {
				global $woocommerce;
				if( version_compare( $woocommerce->version, $version, ">=" ) ) {
					return true;
				}
			}
			return false;
		}

		public static function unconvert_price_filter_limits( $meta_query ) {

			if ( !isset( $meta_query['price_filter'] ) ) {
				return $meta_query;
			}

			if ( isset( $meta_query['price_filter'] ) && isset($meta_query['price_filter']['key']) && $meta_query['price_filter']['key'] === '_price' ) {

				$currency = apply_filters( 'wcml_get_client_currency', null );

				if ( $currency !== null ) {
					if ( $currency !== get_option( 'woocommerce_currency' ) ) {
						global $woocommerce_wpml;
						$meta_query['price_filter']['value'][0] = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $meta_query['price_filter']['value'][0] );
						$meta_query['price_filter']['value'][1] = $woocommerce_wpml->multi_currency->prices->unconvert_price_amount( $meta_query['price_filter']['value'][1] );
					}
				}
				else {
					$meta_query['price_filter']['value'][0] = apply_filters( 'woocs_back_convert_price', $meta_query['price_filter']['value'][0] );
					$meta_query['price_filter']['value'][1] = apply_filters( 'woocs_back_convert_price', $meta_query['price_filter']['value'][1] )+1;
				}

			}

			return $meta_query;

		}

		public static function get_filter_search() {

			global $prdctfltr_global;

			$pf_srch = ( isset( $prdctfltr_global['sc_init'] ) && $prdctfltr_global['sc_init'] === true ? 'search_products' : 's' );

			$pf_placeholder = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_search_placeholder'] != '' ? esc_attr( WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_search_placeholder'] ) : esc_attr( esc_html__( 'Product keywords', 'prdctfltr' ) );

			$insert = '<input class="pf_search" name="' . $pf_srch .'" type="text"' . ( isset( self::$settings['pf_activated']['s'] ) ? ' value="' . esc_attr( self::$settings['pf_activated']['s'] ) . '"' : '' ) . ' placeholder="' . $pf_placeholder . '">';

			printf( '<label%1$s>%2$s <a href="#" class="pf_search_trigger"></a><span>%3$s</span></label>', ( isset( self::$settings['pf_activated']['s'] ) ? ' class="prdctfltr_active"' : '' ), $insert, get_search_query() == '' ? ( isset( self::$settings['pf_activated']['s'] ) ? self::$settings['pf_activated']['s'] : '' ) : get_search_query() );

		}

		public static function get_filter_meta_terms() {

			$fc = self::get_filter_customization( 'meta', self::$filter['terms'] );
			if ( !empty( $fc ) && isset( $fc['settings'] ) && is_array( $fc['settings'] ) ) {

				foreach( $fc['settings'] as $v ) {
					$meta[$v['value']] = $v['text'];
				}

			}

			if ( self::$filter['none'] == 'no' ) {
				if ( !empty( self::$filter['customization']['options'] ) ) {
					$blank_element = self::get_customized_term( '', apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) ), false, self::$filter['customization']['options'] );
				}
				else {
					$blank_element = '<span>' . apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) ) . '</span>';
				}

				printf('<label class="prdctfltr_ft_none"><input type="checkbox" value="" />%1$s</label>', $blank_element );
			}

			if ( empty( $meta ) ) {
				esc_html_e( 'Error! No terms!', 'prdctfltr' );
				$meta = array();
			}
			else {
				foreach ( $meta as $id => $name ) {

					$checked = ( isset( self::$settings['pf_activated'][self::$filter['key']] ) && in_array( $id,  self::$settings['pf_activated'][self::$filter['key']] ) ? ' checked' : ' ' );

					if ( !empty( self::$filter['customization']['options'] ) ) {
						$insert = self::get_customized_term( $id, $name, false, self::$filter['customization']['options'], $checked );
					}
					else {
						$insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), $checked, $name );
					}

					printf( '<label%1$s>%2$s</label>', ( isset( self::$settings['pf_activated'][self::$filter['key']] ) && in_array( $id,  self::$settings['pf_activated'][self::$filter['key']] ) ? ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $insert );
				}
			}

		}

		public static function get_filter( $type ) {

			$filter = self::get_true_filter( $type );

			if ( self::check_adoptive() === false ) {
				return false;
			}

			self::get_filter_wrapper_start();

			self::get_filter_input_fields();

			switch ( self::$filter['filter'] ) {
				case 'range' :
					self::get_dynamic_filter_title( 'range', self::$filter['taxonomy'], self::$settings['rng'], self::$filter['taxonomy'] == 'price' ? esc_html__( 'Price', 'prdctfltr' ) : self::$filter['taxonomy'] );
				break;
				case 'taxonomy' :
					self::get_filter_taxonomy_title();
				break;
				default :
					self::get_filter_title( self::$filter['slug'], self::$filter['title'], self::$filter['name'] );
				break;
			}

			self::get_filter_description();

			self::get_filter_checkboxes_wrapper_start();

			switch ( self::$filter['filter'] ) {
				case 'meta' :
					self::get_filter_meta_terms();
				break;
				case 'search' :
					self::get_filter_search();
				break;
				case 'range' :
					self::get_filter_range_terms();
				break;
				case 'taxonomy' :
					self::get_filter_taxonomy_terms();
				break;
				default :
					self::get_filter_terms();
				break;
			}

			self::get_filter_checkboxes_wrapper_end();

			self::get_filter_wrapper_end();

			self::update_counters();

		}

		public static function update_counters() {

			if ( isset( self::$filter['cnt'] ) ) {
				switch( self::$filter['cnt'] ){
					case 'meta':
						self::$settings['mta'] = self::$settings['mta']+1;
					break;
					case 'range':
						self::$settings['rng'] = self::$settings['rng']+1;
					break;
					case 'advanced':
					default:
						self::$settings['adv'] = self::$settings['adv']+1;
					break;
				}
				
			}

			self::$settings['cnt'] = self::$settings['cnt']+1;

		}

		public static function get_true_filter_description( $name ) {
			return isset( self::$settings['instance']['wc_settings_prdctfltr_' . $name . '_description'] ) ? self::$settings['instance']['wc_settings_prdctfltr_' . $name . '_description'] : '';
		}

		public static function get_true_filter_customization( $name ) {
			return isset( self::$settings['instance']['wc_settings_prdctfltr_' . $name . '_term_customization'] ) ? self::get_customization( self::$settings['instance']['wc_settings_prdctfltr_' . $name . '_term_customization'] ) : false;
		}
		public static function get_true_filter_key_customization( $name ) {
			return self::get_customization( $name );
		}

		public static function get_true_filter( $filter ) {

			$c = isset( self::$settings['cnt'] ) ? self::$settings['cnt']++ : 1;
			self::$settings['cnt'] = $c;

			switch( $filter ) {
				case 'per_page' :

					self::$filter = array(
						'filter'        => $filter,
						'title'         => esc_html__( 'Show Per Page', 'prdctfltr' ),
						'description'   => self::get_true_filter_description( 'perpage' ),
						'name'          => 'perpage',
						'slug'          => 'products_per_page',
						'class'         => 'perpage',
						'customization' => self::get_true_filter_customization( 'perpage' )
					);

				break;

				case 'instock' :

					self::$filter = array(
						'filter'        => $filter,
						'title'         => esc_html__( 'Availability', 'prdctfltr' ),
						'description'   => self::get_true_filter_description( 'instock' ),
						'name'          => 'instock',
						'slug'          => 'instock_products',
						'class'         => 'instock',
						'customization' => self::get_true_filter_customization( 'instock' )
					);

				break;

				case 'sort' :

					self::$filter = array(
						'filter'        => $filter,
						'title'         => esc_html__( 'Sort By', 'prdctfltr' ),
						'description'   => self::get_true_filter_description( 'orderby' ),
						'name'          => 'orderby',
						'slug'          => 'orderby',
						'class'         => 'orderby',
						'customization' => self::get_true_filter_customization( 'orderby' )
					);

				break;

				case 'vendor' :

					self::$filter = array(
						'filter'        => $filter,
						'title'         => esc_html__( 'Vendor', 'prdctfltr' ),
						'description'   => self::get_true_filter_description( 'vendor' ),
						'name'          => 'vendor',
						'slug'          => 'vendor',
						'class'         => 'vendor',
						'customization' => self::get_true_filter_customization( 'vendor' )
					);

				break;

				case 'price' :

					self::$filter = array(
						'filter'        => $filter,
						'title'         => esc_html__( 'Price', 'prdctfltr' ),
						'description'   => self::get_true_filter_description( 'price' ),
						'name'          => 'price',
						'slug'          => 'price',
						'class'         => 'byprice',
						'customization' => self::get_true_filter_customization( 'price' )
					);

				break;

				case 'search' :

					self::$filter = array(
						'filter'        => $filter,
						'title'         => esc_html__( 'Search', 'prdctfltr' ),
						'description'   => self::get_true_filter_description( 'search' ),
						'name'          => 'search',
						'slug'          => 'pf_search',
						'class'         => 'search',
						'customization' => self::get_true_filter_customization( 'search' )
					);

				break;

				case 'meta' :

					$y = self::$settings['mta'];

					foreach ( self::check_meta_settings() as $ck => $cv ) {
						if ( !isset( self::$settings['instance']['wc_settings_prdctfltr_meta_filters'][$ck][$y] ) ) {
							self::$settings['instance']['wc_settings_prdctfltr_meta_filters'][$ck][$y] = $cv;
						}
					}

					$customization = isset( self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_term_customization'][$y] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_term_customization'][$y] : '' ;
					$terms = isset( self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_filter_customization'][$y] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_filter_customization'][$y] : '' ;

					$key = self::build_meta_key( self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_key'][$y], self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_compare'][$y], self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_type'][$y] );

					self::$filter = array(
						'cnt'           => 'meta',
						'filter'        => $filter,
						'title'         => ( $title = self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_title'][$y] ) == '' ? esc_html__( 'Product Meta', 'prdctfltr' ) : $title,
						'name'          => 'meta',
						'slug'          => $key,
						'class'         => 'meta',
						'description'   => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_description'][$y],
						'compare'       => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_compare'][$y],
						'type'          => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_type'][$y],
						'limit'         => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_limit'][$y],
						'relation'      => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_relation'][$y],
						'multi'         => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_multiselect'][$y],
						'key'           => $key,
						'none'          => self::$settings['instance']['wc_settings_prdctfltr_meta_filters']['pfm_none'][$y],
						'customization' => self::get_true_filter_key_customization( $customization ),
						'terms'         => $terms
					);

					self::$filter['class'] .= ( self::$filter['multi'] == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single' ) . ( self::$filter['relation'] == 'AND' ? ' prdctfltr_merge_terms' : '' );

				break;

				case 'range' :

					$p = self::$settings['rng'];

					foreach ( self::check_range_settings() as $k => $v ) {
						if ( !isset( self::$settings['instance']['wc_settings_prdctfltr_range_filters'][$k][$p] ) ) {
							self::$settings['instance']['wc_settings_prdctfltr_range_filters'][$k][$p] = $v;
						}
					}

					$key = self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_taxonomy'][$p];

					self::$filter = array(
						'cnt'           => 'range',
						'filter'        => $filter,
						'title'         => ( $title = self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_title'][$p] ) == '' ? esc_html__( 'Range', 'prdctfltr' ) : $title,
						'name'          => 'range',
						'slug'          => 'rng_' . $key,
						'class'         => 'range',
						'description'   => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_description'][$p],
						'taxonomy'      => $key,
						'include'       => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_include'][$p],
						'orderby'       => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_orderby'][$p],
						'order'         => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_order'][$p],
						'style'         => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_style'][$p],
						'grid'          => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_grid'][$p],
						'adoptive'      => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_adoptive'][$p],
						'custom'        => self::$settings['instance']['wc_settings_prdctfltr_range_filters']['pfr_custom'][$p]
					);

					self::$filter['class'] .= ' prdctfltr_' . $key . ' pf_rngstyle_' . self::$filter['style'];

				break;

				default:

					$mod = '';

					if ( $filter == 'cat' ) {
						$key = 'product_cat';
						$mod = 'regular';
					}
					else if ( $filter == 'tag' ) {

						$key = 'product_tag';
						$mod = 'regular';
					}
					else if ( $filter == 'char' ) {

						$key = 'characteristics';
						$mod = 'regular';
					}
					else if ( substr( $filter, 0, 3) == 'pa_' ) {

						$key = $filter;
						$mod = 'attribute';
					}
					else if ( $filter == 'advanced' ) {
						$key = false;
						$mod = 'advanced';
					}

					self::make_up_filter( $filter, $key, $mod );

				break;
			}

			self::$settings['inFilterInput'][] = self::$filter['slug'];

		}

		public static function make_up_filter( $filter, $key, $mod ) {
			switch ( $mod ) {
				case 'advanced' :

					$n = self::$settings['adv'];

					foreach ( self::check_advanced_settings() as $ck => $cv ) {
						if ( !isset( self::$settings['instance']['wc_settings_prdctfltr_advanced_filters'][$ck][$n] ) ) {
							self::$settings['instance']['wc_settings_prdctfltr_advanced_filters'][$ck][$n] = $cv;
						}
					}

					$customization = isset( self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_term_customization'][$n] ) ? WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_term_customization'][$n] : '' ;

					$key = self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_taxonomy'][$n];

					self::$filter = array(
						'cnt'           => 'advanced',
						'filter'        => 'taxonomy',
						'title'         => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_title'][$n],
						'name'          => 'attributes',
						'slug'          => $key,
						'class'         => 'attributes',
						'customization' => self::get_true_filter_key_customization( $customization ),
						'description'   => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_description'][$n],
						'include'       => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_include'][$n],
						'orderby'       => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_orderby'][$n],
						'order'         => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_order'][$n],
						'limit'         => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_limit'][$n],
						'multi'         => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_multiselect'][$n],
						'relation'      => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_relation'][$n],
						'selection'     => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_selection'][$n],
						'adoptive'      => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_adoptive'][$n],
						'hierarchy'     => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_hierarchy'][$n],
						'hierarchy_mode'=> self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_hierarchy_mode'][$n],
						'mode'          => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_mode'][$n],
						'none'          => self::$settings['instance']['wc_settings_prdctfltr_advanced_filters']['pfa_none'][$n]
					);

				break;
				default :

					$customization = self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'chars' : $filter ) .'_term_customization'];

					self::$filter = array(
						'filter'        => 'taxonomy',
						'title'         => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'custom_tax' : $filter ) . '_title'],
						'name'          => 'attributes',
						'slug'          => $key,
						'class'         => 'attributes',
						'customization' => self::get_true_filter_key_customization( $customization ),
						'description'   => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'custom_tax' : $filter ) . '_description'],
						'include'       => $mod == 'attribute' ? self::$settings['instance']['wc_settings_prdctfltr_include_' . $filter] : self::$settings['instance']['wc_settings_prdctfltr_include_' . $filter . 's'],
						'orderby'       => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'custom_tax' : $filter ) . '_orderby'],
						'order'         => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'custom_tax' : $filter ) . '_order'],
						'limit'         => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'custom_tax' : $filter ) . '_limit'],
						'multi'         => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'chars' : $filter ) . '_multi'],
						'relation'      => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'custom_tax' : $filter ) . '_relation'],
						'selection'     => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'chars' : $filter ) . '_selection'],
						'adoptive'      => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'chars' : $filter ) . '_adoptive'],
						'hierarchy'     => isset( self::$settings['instance']['wc_settings_prdctfltr_' . $filter . '_hierarchy'] ) ? self::$settings['instance']['wc_settings_prdctfltr_' . $filter . '_hierarchy'] : 'no',
						'hierarchy_mode'=> isset( self::$settings['instance']['wc_settings_prdctfltr_' . $filter . '_hierarchy_mode'] ) ? self::$settings['instance']['wc_settings_prdctfltr_' . $filter . '_hierarchy_mode'] : 'no',
						'mode'          => isset( self::$settings['instance']['wc_settings_prdctfltr_' . $filter . '_mode'] ) ? self::$settings['instance']['wc_settings_prdctfltr_' . $filter . '_mode'] : 'showall',
						'none'          => self::$settings['instance']['wc_settings_prdctfltr_' . ( $filter == 'char' ? 'chars' : $filter ) . '_none']
					);

				break;
			}

			self::$filter['class'] .= ' prdctfltr_' . $filter . ( self::$filter['multi'] == 'yes' ? ' prdctfltr_multi' : ' prdctfltr_single' ) . ( self::$filter['adoptive'] == 'yes' ? ' prdctfltr_adoptive' : '' ) . ( self::$filter['selection'] == 'yes' ? ' prdctfltr_selection' : '' ) . ( self::$filter['relation'] == 'AND' ? ' prdctfltr_merge_terms' : '' ) . ( self::$filter['hierarchy_mode'] == 'yes' ? ' prdctfltr_expand_parents' : '' ) . ( self::$filter['hierarchy'] == 'yes' ? ' prdctfltr_hierarchy' : '' ) . ( in_array( self::$filter['mode'], array( 'drill', 'drillback', 'subonly', 'subonlyback' ) ) ? ' prdctfltr_' . self::$filter['mode'] : '' );

			$pf_terms = array();

			if ( self::$filter['orderby'] == 'number' ) {
				$curr_term_args = array(
					'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
					'orderby' => 'slug'
				);
				$pf_terms = self::prdctfltr_get_terms( $key, $curr_term_args );
				$pf_sort_args = array(
					'order' => ( isset( self::$filter['order'] ) ? self::$filter['order'] : '' )
				);
				$pf_terms = self::prdctfltr_sort_terms_naturally( $pf_terms, $pf_sort_args );
			}
			else {
				$curr_term_args = array(
					'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
					'orderby' => ( self::$filter['orderby'] !== '' ? self::$filter['orderby'] : '' ),
					'order' => ( self::$filter['order'] !== '' ? self::$filter['order'] : '' )
				);
				$pf_terms = self::prdctfltr_get_terms( $key, $curr_term_args );
			}
			if ( empty( self::$filter['include'] ) ) {
				foreach ( $pf_terms as $term ) {
					self::$filter['include'][] = $term->slug;
				}
			}

			if ( isset( self::$filter['hierarchy'] ) && self::$filter['hierarchy'] == 'yes' ) {

				$pf_terms_sorted = array();
				self::prdctfltr_sort_terms_hierarchicaly( $pf_terms, $pf_terms_sorted );
				self::$filter['terms'] = $pf_terms_sorted;

				if ( isset( $customization['style'] ) && $customization['style'] !== 'select' ) {
					$customization = array(
						'options' => array(),
						'class' => 'prdctfltr_text'
					);
				}

			}
			else {
				self::$filter['terms'] = $pf_terms;
			}

		}

		public static function get_filter_maxheight() {

			if ( !isset( self::$settings['maxheight'] ) ) {
				$max = ( self::$settings['instance']['wc_settings_prdctfltr_limit_max_height'] == 'yes' ? self::$settings['instance']['wc_settings_prdctfltr_max_height'] . 'px' : '' );
				self::$settings['maxheight'] = ( $max == '' ? '' : self::$settings['maxheight'] = ( isset( self::$filter['customization']['options']['style'] ) && self::$filter['customization']['options']['style'] == 'select' || self::$settings['instance']['wc_settings_prdctfltr_style_preset'] == 'pf_select' ? ' style="height:' . $max . ';"' : ' style="max-height:' . $max . ';"' ) );
			}
			return self::$settings['maxheight'];

		}

		public static function get_filter_wrapper_start() {

			if ( ( self::$settings['instance']['wc_settings_prdctfltr_style_mode'] == 'pf_mod_multirow' || self::$settings['instance']['wc_settings_prdctfltr_style_preset'] == 'pf_select' ) && self::$settings['instance']['wc_settings_prdctfltr_max_columns'] !== 1 && !isset( self::$settings['widget'] ) && self::$settings['cnt'] == self::$settings['instance']['wc_settings_prdctfltr_max_columns'] ) {
				self::$settings['cnt'] = 0;
				self::$filter['class'] .= ' prdctfltr_clearnext';
			}

			?>
				<div class="prdctfltr_filter prdctfltr_<?php echo self::$filter['class'] . ( isset( self::$filter['customization']['class'] ) ? self::$filter['customization']['class'] : ''); ?>" data-filter="<?php echo self::$filter['slug']; ?>"<?php echo isset( self::$filter['limit'] ) && intval( self::$filter['limit'] ) > 0 ? ' data-limit="' . intval( self::$filter['limit'] ) . '"' : '';?>>
			<?php
		}

		public static function get_filter_checkboxes_wrapper_start() {
			$max_height = self::get_filter_maxheight();
		?>
			<div class="prdctfltr_add_scroll"<?php echo !in_array( self::$filter['name'], array( 'range', 'search' ) ) ? $max_height : ''; ?>>
				<div class="prdctfltr_checkboxes">
			<?php
		}

		public static function get_filter_input_taxonomy() {

			global $prdctfltr_global;

			$curr_cat_selected = array();

			if ( isset( self::$settings['pf_activated'][self::$filter['slug']] ) ) {
				$curr_cat_selected = is_array( self::$settings['pf_activated'][self::$filter['slug']] ) ? self::$settings['pf_activated'][self::$filter['slug']] : array( self::$settings['pf_activated'][self::$filter['slug']] );
			}

			if ( empty( $curr_cat_selected ) && !isset( $prdctfltr_global['sc_init'] ) &&  isset( $prdctfltr_global['active_permalinks'][self::$filter['slug']] ) ) {
				$curr_cat_selected = is_array( $prdctfltr_global['active_permalinks'][self::$filter['slug']] ) ? $prdctfltr_global['active_permalinks'][self::$filter['slug']] : array( $prdctfltr_global['active_permalinks'][self::$filter['slug']] );
			}

			if ( !empty( $curr_cat_selected ) ) {
				$curr_cat_selected = array_map( 'sanitize_title', $curr_cat_selected );
			}

			if ( isset( self::$settings['pf_activated']['rng_min_' . self::$filter['slug']] ) ) {
				$curr_cat_selected = array();
			}

			if ( !empty( $curr_cat_selected ) ) {
				$tax_val = isset( $prdctfltr_global['taxonomies_data'][self::$filter['slug'].'_string'] ) ? ' value="' . esc_attr( $prdctfltr_global['taxonomies_data'][self::$filter['slug'].'_string'] ) . '"' : '';
				if ( $tax_val == '' && !empty( $curr_cat_selected ) ) {
					$tax_val = isset( $prdctfltr_global['permalinks_data'][self::$filter['slug'].'_string'] ) ? ' value="' . esc_attr( $prdctfltr_global['permalinks_data'][self::$filter['slug'].'_string'] ) . '"' : '';
				}
				self::$filter['selected'] = $curr_cat_selected;
			}
			else {
				self::$filter['selected'] = array();
			}

			$termAddParent = '';
			if ( !empty( $curr_cat_selected ) && isset( self::$filter['hierarchy'] ) && self::$filter['hierarchy'] == 'yes' ) {

				foreach( $curr_cat_selected as $tax_val_term ) {

					if ( term_exists( $tax_val_term, self::$filter['slug'] ) !== null ) {
						$curr_term = get_term_by( 'slug', $tax_val_term, self::$filter['slug'] );
						$pf_term_parent[] = $curr_term->parent;
					}

				}

				$doNotTerm = null;
				if ( !empty( $pf_term_parent ) ) {
					$firstValueTerm = current( $pf_term_parent );
					foreach ( $pf_term_parent as $valTerm ) {
						if ( $firstValueTerm !== $valTerm ) {
							$doNotTerm = true;
						}
					}
					if ( !isset( $doNotTerm ) && $pf_term_parent[0] !== 0 ) {
						$currParent = get_term_by( 'id', $pf_term_parent[0], self::$filter['slug'] );
						$termAddParent = ' data-parent="' . $currParent->slug . '"';
					}
				}
			}
			self::$settings['instance']['activated'][] = self::$filter['slug'];
		?>
			<input name="<?php echo self::$filter['slug']; ?>" type="hidden"<?php echo ( !empty( $curr_cat_selected ) ? $tax_val : '' ) . $termAddParent; ?> />
		<?php

		}

		public static function get_filter_input_meta() {
			global $prdctfltr_global;
			self::$settings['instance']['activated'][] = self::$filter['key'];
			?>
				<input name="<?php echo self::$filter['key']; ?>" type="hidden"<?php echo ( isset( $prdctfltr_global['meta_data'][self::$filter['key']] ) ? ' value="' . esc_attr( $prdctfltr_global['meta_data'][self::$filter['key']] ) . '"' : '' );?>>
			<?php
		}

		public static function get_filter_input_range() {
			?>
				<input name="rng_min_<?php echo self::$filter['taxonomy']; ?>" type="hidden"<?php echo ( isset( self::$settings['pf_activated']['rng_min_' . self::$filter['taxonomy']] ) ? ' value="' . esc_attr( self::$settings['pf_activated']['rng_min_' . self::$filter['taxonomy']] ) . '"' : '' );?>>
				<input name="rng_max_<?php echo self::$filter['taxonomy']; ?>" type="hidden"<?php echo ( isset( self::$settings['pf_activated']['rng_max_' . self::$filter['taxonomy']] ) ? ' value="' . esc_attr( self::$settings['pf_activated']['rng_max_' . self::$filter['taxonomy']] ) . '"' : '' );?>>
			<?php
				self::$settings['instance']['activated'][] = self::$filter['taxonomy'];
				self::$settings['instance']['activated'][] = 'rng_min_' . self::$filter['taxonomy'];
				self::$settings['instance']['activated'][] = 'rng_max_' . self::$filter['taxonomy'];

				if ( self::$filter['taxonomy'] !== 'price' ) {
				?>
					<input name="rng_orderby_<?php echo self::$filter['taxonomy']; ?>" type="hidden" value="<?php echo self::$filter['orderby']; ?>">
					<input name="rng_order_<?php echo self::$filter['taxonomy']; ?>" type="hidden" value="<?php echo !empty( self::$filter['order'] ) ? self::$filter['order'] : ''; ?>">
				<?php
				}
		}

		public static function get_filter_input_price() {
			self::$settings['instance']['activated'][] = 'min_price';
			self::$settings['instance']['activated'][] = 'max_price';
			?>
				<input name="min_price" type="hidden"<?php echo ( isset( self::$settings['pf_activated']['min_price'] ) ? ' value="' . esc_attr( self::$settings['pf_activated']['min_price'] ) . '"' : '' );?>>
				<input name="max_price" type="hidden"<?php echo ( isset( self::$settings['pf_activated']['max_price'] ) ? ' value="' . esc_attr( self::$settings['pf_activated']['max_price'] ) . '"' : '' );?>>
			<?php
		}

		public static function get_filter_input_default() {
			self::$settings['instance']['activated'][] = self::$filter['slug'];
			?>
				<input name="<?php echo self::$filter['slug']; ?>" type="hidden"<?php echo ( isset( self::$settings['pf_activated'][self::$filter['slug']] ) ? ' value="' . esc_attr( self::$settings['pf_activated'][self::$filter['slug']] ) . '"' : '' );?>>
			<?php
		}

		public static function get_filter_input_fields() {

			switch( self::$filter['filter'] ){
				case 'price' :
					self::get_filter_input_price();
				break;

				case 'meta' :
					self::get_filter_input_meta();
				break;

				case 'range' :
					self::get_filter_input_range();
				break;

				case 'taxonomy' :
					self::get_filter_input_taxonomy();
				break;

				default:
					self::get_filter_input_default();
				break;
			}

		}

		public static function get_filter_description() {

			$desc = isset( self::$filter['description'] ) && !empty( self::$filter['description'] ) ? self::$filter['description'] : '';
			if ( $desc !== '' ) {
				printf( '<div class="prdctfltr_description">%1$s</div>', do_shortcode( $desc ) );
			}

		}

		public static function get_filter_checkboxes_wrapper_end() {
			?>
						</div>
					</div>
			<?php
		}

		public static function get_filter_wrapper_end() {
			?>
				</div>
			<?php
		}

		public static function get_filter_range_terms() {

			global $prdctfltr_global;

			$add_rng_js = '';

			$rngId = uniqid( 'prdctfltr_rng_' ) . self::$settings['rng'];
			$prdctfltr_global['ranges'][$rngId] = array();
			$prdctfltr_global['ranges'][$rngId]['type'] = 'double';
			$prdctfltr_global['ranges'][$rngId]['min_interval'] = 1;

			if ( !in_array( self::$filter['taxonomy'], array( 'price' ) ) ) {

				$curr_include = self::$filter['include'];

				$curr_include = self::prdctfltr_wpml_translate_terms( $curr_include, self::$filter['taxonomy'] );

				if ( isset( self::$filter['orderby'] ) && self::$filter['orderby'] == 'number' ) {
					$curr_term_args = array(
						'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
						'orderby' => 'slug'
					);
					$pf_terms = self::prdctfltr_get_terms( self::$filter['taxonomy'], $curr_term_args );
					$pf_sort_args = array(
						'order' => ( isset( self::$filter['order'] ) ? self::$filter['order'] : '' )
					);
					$pf_terms = self::prdctfltr_sort_terms_naturally( $pf_terms, $pf_sort_args );
				}
				else {
					$curr_term_args = array(
						'hide_empty' => self::$settings['wc_settings_prdctfltr_hideempty'],
						'orderby' => ( self::$filter['orderby'] !== '' ? self::$filter['orderby'] : '' ),
						'order' => ( self::$filter['order'] !== '' ? self::$filter['order'] : '' )
					);
					$pf_terms = self::prdctfltr_get_terms( self::$filter['taxonomy'], $curr_term_args );
				}

				$prdctfltr_global['ranges'][$rngId]['prettyValues'] = array();

				$c=0;

				foreach ( $pf_terms as $attribute ) {

					if ( !empty( $curr_include ) && !in_array( $attribute->slug, $curr_include ) ) {
						continue;
					}

					if ( self::$filter['adoptive'] == 'yes' && isset( self::$settings['adoptive'][self::$filter['taxonomy']] ) && count( self::$settings['adoptive'][self::$filter['taxonomy']] ) !== 1 ) {
						if ( !isset( self::$settings['adoptive'][self::$filter['taxonomy']][$attribute->slug] ) ) {
							continue;
						}
					}

					if ( isset( self::$settings['pf_activated']['rng_min_' . self::$filter['taxonomy']] ) && self::$settings['pf_activated']['rng_min_' . self::$filter['taxonomy']] == $attribute->slug ) {
						$prdctfltr_global['ranges'][$rngId]['from'] = $c;
					}

					if ( isset( self::$settings['pf_activated']['rng_max_' . self::$filter['taxonomy']] ) && self::$settings['pf_activated']['rng_max_' . self::$filter['taxonomy']] == $attribute->slug ) {
						$prdctfltr_global['ranges'][$rngId]['to'] = $c;
					}

					$prdctfltr_global['ranges'][$rngId]['prettyValues'][] = '<span class=\'pf_range_val\'>' . $attribute->slug . '</span>' . $attribute->name;

					$c++;
				}

				if ( !empty( $prdctfltr_global['ranges'][$rngId]['prettyValues'] ) ) {
					$prdctfltr_global['ranges'][$rngId]['min'] = 0;
					$prdctfltr_global['ranges'][$rngId]['max'] = count( $prdctfltr_global['ranges'][$rngId]['prettyValues'] )-1;
				}

				$prdctfltr_global['ranges'][$rngId]['decorate_both'] = false;
				$prdctfltr_global['ranges'][$rngId]['values_separator'] = ' &rarr; ';
				$prdctfltr_global['ranges'][$rngId]['force_edges'] = true;

				if ( self::$filter['custom'] !== '' ) {
					$add_rng_js = self::$filter['custom'];
				}

			}
			else {

				$prices = self::get_filtered_price( self::$filter['adoptive'] );

				$pf_curr_min = self::price_to_float( strip_tags( wc_price( floor( $prices->min_price ) ) ) );
				$pf_curr_max = self::price_to_float( strip_tags( wc_price( ceil( $prices->max_price ) ) ) );

				$prdctfltr_global['ranges'][$rngId]['min'] = apply_filters( 'wcml_raw_price_amount', $pf_curr_min );
				$prdctfltr_global['ranges'][$rngId]['max'] = apply_filters( 'wcml_raw_price_amount', $pf_curr_max );
				$prdctfltr_global['ranges'][$rngId]['minR'] = $pf_curr_min;
				$prdctfltr_global['ranges'][$rngId]['maxR'] = $pf_curr_max;
				$prdctfltr_global['ranges'][$rngId]['force_edges'] = true;

				if ( self::$filter['custom'] !== '' ) {
					$add_rng_js = self::$filter['custom'];
				}

				$currency_pos = get_option( 'woocommerce_currency_pos', 'left' );
				$currency = get_woocommerce_currency_symbol();

				switch ( $currency_pos ) {
					case 'right' :
						$prdctfltr_global['ranges'][$rngId]['postfix'] = $currency;
					break;
					case 'right_space' :
						$prdctfltr_global['ranges'][$rngId]['postfix'] = ' ' . $currency;
					break;
					case 'left_space' :
						$prdctfltr_global['ranges'][$rngId]['prefix'] = $currency . ' ';
					break;
					case 'left' :
					default :
						$prdctfltr_global['ranges'][$rngId]['prefix'] = $currency;
					break;
				}

				if ( isset( self::$settings['pf_activated']['rng_min_' . self::$filter['taxonomy']] ) ) {
					$prdctfltr_global['ranges'][$rngId]['from'] = self::$settings['pf_activated']['rng_min_' . self::$filter['taxonomy']];
				}

				if ( isset( self::$settings['pf_activated']['rng_max_' . self::$filter['taxonomy']] ) ) {
					$prdctfltr_global['ranges'][$rngId]['to'] = self::$settings['pf_activated']['rng_max_' . self::$filter['taxonomy']];
				}

			}

			if ( self::$filter['grid'] == 'yes' ) {
				$prdctfltr_global['ranges'][$rngId]['grid'] = true;
			}

			$pf_divide = apply_filters( 'wcml_raw_price_amount', self::price_to_float( strip_tags( wc_price( 100 ) ) ) );
			$pf_divide_checked = $pf_divide > 0 ? $pf_divide : 100;

			if ( $add_rng_js !== '' ) {

				$rng_set = json_decode( stripslashes( $add_rng_js ), true );

				if ( is_array( $rng_set ) ) {
					foreach( $rng_set as $k24 => $v23 ) {
						if ( $v23 == '' ) {
							continue;
						}
						switch( $k24 ) {
							case 'prefix':
								$outv23 = $v23 . ( isset( $prdctfltr_global['ranges'][$rngId][$k24] ) ? $prdctfltr_global['ranges'][$rngId][$k24] : '' );
							break;
							case 'postfix':
								$outv23 = ( isset( $prdctfltr_global['ranges'][$rngId][$k24] ) ? $prdctfltr_global['ranges'][$rngId][$k24] : '' ) . $v23;
							break;
							default :
								$outv23 = $v23;
							break;
						}
						$prdctfltr_global['ranges'][$rngId][$k24] = $outv23;
					}
				}

			}

			printf( '<input id="%1$s" class="pf_rng_%2$s" data-filter="%2$s" />', $rngId, self::$filter['taxonomy'] );


		}

		public static function get_filter_taxonomy_terms() {

			if ( self::$filter['none'] == 'no' ) {
				if ( !empty( self::$filter['customization']['options'] ) ) {
					$blank = self::get_customized_term( '', apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) ), false, self::$filter['customization']['options'] );
				}
				else {
					$blank = apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) );
				}

				printf('<label class="prdctfltr_ft_none"><input type="checkbox" value="" /><span>%1$s</span></label>', $blank );
			}

			self::get_taxonomy_terms( self::$filter['terms'], ( isset( self::$filter['customization']['options'] ) ? self::$filter['customization']['options'] : array() ), self::$filter['include'], self::$filter['selected'], ( isset( self::$settings['adoptive'] ) ? self::$settings['adoptive'] : null ) );

		}

		public static function get_filter_terms() {

			self::get_filter_labels( self::check_for_customization() );

		}

		public static function get_filter_checked( $id ) {
			switch ( self::$filter['filter'] ) {
				case 'price' :
					$price = ( isset( self::$settings['pf_activated']['min_price'] ) ? self::$settings['pf_activated']['min_price'].'-'.( isset( self::$settings['pf_activated']['max_price'] ) ? self::$settings['pf_activated']['max_price'] : '' ) : '' );
					return ( $price == $id ? true : false );
				break;
				default :
					return ( isset( self::$settings['pf_activated'][self::$filter['slug']] ) && self::$settings['pf_activated'][self::$filter['slug']] == $id ? true : false );
				break;
			}
		}

		public static function get_filter_labels( $ct ) {
			if ( $ct === false ) {
				$ct = self::get_false_terms();
			}


			foreach ( $ct as $id => $name ) {

				$checked = self::get_filter_checked( $id );

				if ( !empty( self::$filter['customization']['options'] ) ) {
					$insert = self::get_customized_term( $id, $name, false, self::$filter['customization']['options'], ( $checked === true ? ' checked' : ' ' ) );
				}
				else {
					$insert = sprintf( '<input type="checkbox" value="%1$s"%2$s/><span>%3$s</span>', esc_attr( $id ), ( $checked === true ? ' checked' : ' ' ), $name );
				}

				printf( '<label%1$s>%2$s</label>', ( $checked === true ?  ' class="prdctfltr_active prdctfltr_ft_' . sanitize_title( $id ) .'"' : ' class="prdctfltr_ft_' . sanitize_title( $id ) .'"' ), $insert );
			}

		}

		public static function check_for_customization() {
			if ( !in_array( self::$filter['filter'], array( 'per_page', 'price' ) ) ) {
				return false;
			}
			$ct = false;
			$fc = self::get_filter_customization( self::$filter['filter'], self::$settings['instance']['wc_settings_prdctfltr_' . self::$filter['name'] . '_filter_customization'] );
			if ( !empty( $fc ) && isset( $fc['settings'] ) && is_array( $fc['settings'] ) ) {
				switch ( self::$filter['filter'] ) {
					case 'price' :
						$ct = array();
						if ( self::$settings['instance']['wc_settings_prdctfltr_price_none'] == 'no' ) {
							$ct = array(
								'-' => apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) )
							);
						}

						foreach( $fc['settings'] as $k => $v ) {
							$pf_custom_ranges = explode( '-', $k );
							if ( $pf_custom_ranges[0] !== '' ) {
								$pf_custom_ranges[0] = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $pf_custom_ranges[0] ) ) );
							}
							if ( $pf_custom_ranges[1] !== '' ) {
								$pf_custom_ranges[1] = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $pf_custom_ranges[1] ) ) );
							}
							$add_num = !empty ( $v ) ? $v : implode( ' - ', $pf_custom_ranges );
							$add_k = self::price_to_float( $pf_custom_ranges[0] ) . '-' . self::price_to_float( $pf_custom_ranges[1] );
							//$add_k = floatval( $pf_custom_ranges[0] ) . '-' . floatval( $pf_custom_ranges[1] );
							$ct[$add_k] = ( $add_num !== '' ? $add_num : '' );
						}

					break;
					case 'perpage' :
						$ct = array();
						foreach( $fc['settings'] as $v ) {
							$ct[intval( $v['value'] )] = $v['text'];
						}
					break;
					default :
						$ct = array();
						foreach( $fc['settings'] as $v ) {
							$ct[esc_attr( $v['value'] )] = $v['text'];
						}
					break;
				}

			}
			return $ct;
		}

		public static function get_false_terms() {

			return call_user_func( 'self::get_false_terms_' . self::$filter['name'] );

		}

		public static function get_false_terms_price() {

			$prices = array();
			$prices_currency = array();
			$catalog_ready_price = array();

			$price_set = self::$settings['instance']['wc_settings_prdctfltr_price_range'];
			$price_add = self::$settings['instance']['wc_settings_prdctfltr_price_range_add'];
			$price_limit = self::$settings['instance']['wc_settings_prdctfltr_price_range_limit'];

			if ( self::$settings['instance']['wc_settings_prdctfltr_price_none'] == 'no' ) {
				$catalog_ready_price = array(
					'-' => apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) )
				);
			}

			for ( $i = 0; $i < $price_limit; $i++ ) {

				if ( $i == 0 ) {
					$min_price = 0;
					$max_price = $price_set;
				}
				else {
					$min_price = $price_set+($i-1)*$price_add;
					$max_price = $price_set+$i*$price_add;
				}

				$prices[$i] = self::price_to_float( strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $min_price ) ) ) ) . '-' . ( ($i+1) == $price_limit ? '' : self::price_to_float( strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $max_price ) ) ) ) );

				$prices_currency[$i] = strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $min_price ) ) ) . ( $i+1 == $price_limit ? '+' : ' - ' . strip_tags( wc_price( apply_filters( 'wcml_raw_price_amount', $max_price ) ) ) );

				$catalog_ready_price = $catalog_ready_price + array(
					$prices[$i] => $prices_currency[$i]
				);

			}

			return $catalog_ready_price;

		}
		public static function get_false_terms_vendor() {

			$catalog_vendor = array();

			$include = is_array( self::$settings['instance']['wc_settings_prdctfltr_include_vendor'] ) ? self::$settings['instance']['wc_settings_prdctfltr_include_vendor'] : array();
			if ( !empty( $include ) && is_array( $include ) ) {
				foreach ( $include as $vendor ) {
					$user = get_userdata( intval( $vendor ) );
					$catalog_vendor[intval( $vendor )] = $user->display_name;
				}
			}
			else {
				$vendors = get_users( 'orderby=nicename' );
				foreach ( $vendors as $vendor ) {
					$include[] = $vendor->ID;
					$catalog_vendor[$vendor->ID] = $vendor->display_name;
				}
			}

			return $catalog_vendor;

		}
		public static function get_false_terms_orderby() {
			return self::catalog_ordering();
		}

		public static function get_false_terms_instock() {
			return self::catalog_instock();
		}

		public static function get_false_terms_perpage() {

			$perpage = array();

			$perpage_set = self::$settings['instance']['wc_settings_prdctfltr_perpage_range'];
			$perpage_limit = self::$settings['instance']['wc_settings_prdctfltr_perpage_range_limit'];

			for ($i = 1; $i <= $perpage_limit; $i++) {

				$perpage[$perpage_set*$i] = $perpage_set*$i . ' ' . ( self::$settings['instance']['wc_settings_prdctfltr_perpage_label'] == '' ? esc_html__( 'Products', 'prdctfltr' ) : self::$settings['instance']['wc_settings_prdctfltr_perpage_label'] );

			}

			return $perpage;

		}

		function make_adoptive() {

			global $prdctfltr_global;
			$pf_adoptive_active = false;

			switch ( self::$settings['instance']['wc_settings_prdctfltr_adoptive_mode'] ) {
				case 'always' :
					$pf_adoptive_active = true;
				break;
				case 'permalink' :
					if ( !empty( $prdctfltr_global['active_filters'] ) || !empty( $prdctfltr_global['active_permalinks'] ) ) {
						$pf_adoptive_active = true;
					}
				break;
				case 'filter' :
					if ( !empty( $prdctfltr_global['active_filters'] ) ) {
						$pf_adoptive_active = true;
					}
				break;
				default :
					$pf_adoptive_active = false;
				break;
			}

			if ( $pf_adoptive_active === true && self::$settings['instance']['wc_settings_prdctfltr_adoptive'] == 'yes' && self::$settings['instance']['total'] > 0 ) {

				$adpt_taxes = self::$settings['instance']['wc_settings_prdctfltr_adoptive_depend'];
				$pf_products = array();

				if ( !empty( $adpt_taxes ) && is_array( $adpt_taxes ) ) {
					$adpt_taxes = array( 'product_cat' );
				}
				else if ( !empty( $adpt_taxes ) && is_string( $adpt_taxes ) ) {
					$adpt_taxes = array( $adpt_taxes );
				}
				else {
					$adpt_taxes = array();
				}
				if ( !empty( $adpt_taxes ) ) {

					$adpt_go = false;
					foreach( $adpt_taxes as $adpt_key => $adpt_tax ) {
						if ( array_key_exists( $adpt_tax, $prdctfltr_global['active_filters'] ) ) {
							$adpt_go = true;
						}
						if ( array_key_exists( $adpt_tax, $prdctfltr_global['active_permalinks'] ) ) {
							$adpt_go = true;
						}
					}

					if ( $adpt_go === true ) {

						$adoptive_args = array(
							'post_type'				=> 'product',
							'post_status'			=> 'publish',
							'fields'				=> 'ids',
							'posts_per_page'		=> apply_filters( 'prdctfltr_adoptive_precision', 999 )
						);

						if ( self::$wc_version === false ) {
							$adoptive_args = array_merge( $adoptive_args, array(
								'meta_query'		=> array(
									array(
										'key'		=> '_visibility',
										'value'		=> array( 'catalog', 'visible' ),
										'compare'	=> 'IN'
									)
								)
							) );
						}

						$tax_query = array();

						for ( $i = 0; $i < count( $adpt_taxes ); $i++ ) {

							if ( isset( $prdctfltr_global['active_filters'][$adpt_taxes[$i]] ) && taxonomy_exists( $adpt_taxes[$i] ) ) {
								$tax_query[] = array(
									'taxonomy' => $adpt_taxes[$i],
									'field' => 'slug',
									'terms' => $prdctfltr_global['active_filters'][$adpt_taxes[$i]]
								);
							}

							if ( isset( $prdctfltr_global['active_permalinks'][$adpt_taxes[$i]] ) && taxonomy_exists( $adpt_taxes[$i] ) ) {
								$tax_query[] = array(
									'taxonomy' => $adpt_taxes[$i],
									'field' => 'slug',
									'terms' => $prdctfltr_global['active_permalinks'][$adpt_taxes[$i]]
								);
							}

						}

						if ( !empty( $tax_query ) ) {
							$tax_query['relation'] = 'AND';
							$adoptive_args['tax_query'] = $tax_query;
						}

						$pf_help_products = new WP_Query( $adoptive_args );

						global $wpdb;
						$pf_products = $wpdb->get_results( $pf_help_products->request );

					}

				}
				else {

					$request = self::$settings['instance']['request'];

					if ( !empty( $request ) && is_string( $request ) ) {

						$t_str = $request;

						$t_pos = strpos( $request, 'SQL_CALC_FOUND_ROWS' );
						if ( $t_pos !== false ) {
							$t_str = str_replace( 'SQL_CALC_FOUND_ROWS', '', $request );
						}

						$t_pos = strpos( $request, 'LIMIT' );
						if ( $t_pos !== false ) {
							$t_str = substr( $request, 0, $t_pos );
						}

						$t_str .= ' LIMIT 0,' . apply_filters( 'prdctfltr_adoptive_precision', 999 ) . ' ';

						global $wpdb;
						$pf_products = $wpdb->get_results( $t_str );

					}

				}

				if ( !empty( $pf_products ) ) {

					$curr_in = array();
					foreach ( $pf_products as $p ) {
						if ( !isset( $p->ID ) ) {
							continue;
						}
						if ( !in_array( $p->ID, $curr_in ) ) {
							$curr_in[] = $p->ID;
						}
					}

					if ( !empty( $curr_in ) && is_array( $curr_in ) ) {

						$adoptive_taxes = array();
						$mysql_adoptive_taxes = '';
						$pf_adoptive_taxes = get_object_taxonomies( 'product', 'names' );

						if ( !empty( self::$settings['instance']['active'] ) ) {
							foreach( self::$settings['instance']['active'] as $k24 => $v34 ) {
								$adoptive_taxes[] = $k24;
							}
							$mysql_adoptive_taxes = 'AND %3$s.taxonomy IN ("' . implode( '","', array_map( 'esc_sql', $adoptive_taxes ) ) . '")';
						}

						$output_terms = array();

						$pf_product_terms_query = '
							SELECT %4$s.slug, %3$s.parent, %3$s.taxonomy, COUNT(DISTINCT %1$s.ID) as count FROM %1$s
							INNER JOIN %2$s ON (%1$s.ID = %2$s.object_id)
							INNER JOIN %3$s ON (%2$s.term_taxonomy_id = %3$s.term_taxonomy_id) ' . $mysql_adoptive_taxes . '
							INNER JOIN %4$s ON (%3$s.term_id = %4$s.term_id)
							WHERE %1$s.ID IN ("' . implode( '","', array_map( 'esc_sql', $curr_in ) ) . '")
							GROUP BY slug,taxonomy
						';

						$pf_product_terms = $wpdb->get_results( sprintf( $pf_product_terms_query, $wpdb->posts, $wpdb->term_relationships, $wpdb->term_taxonomy, $wpdb->terms ) );
						$pf_adpt_set = array();

						foreach ( $pf_product_terms as $p ) {

							if ( !isset( $output_terms[$p->taxonomy] ) ) {
								$output_terms[$p->taxonomy] = array();
							}

							if ( !array_key_exists( $p->slug, $output_terms[$p->taxonomy] ) ) {
								$output_terms[$p->taxonomy][$p->slug] = $p->count;
							}
							else {
								$output_terms[$p->taxonomy][$p->slug] = $p->count+(isset($output_terms[$p->taxonomy][$p->slug])?$output_terms[$p->taxonomy][$p->slug]:0);
							}

							$adpt_prnt = intval( $p->parent );
							if ( $adpt_prnt > 0 ) {
								while ( $adpt_prnt !== 0 ) {
									$adpt_prnt_term = get_term_by( 'id', $adpt_prnt, $p->taxonomy );
									$output_terms[$p->taxonomy][$adpt_prnt_term->slug] = $p->count+(isset($output_terms[$p->taxonomy][$adpt_prnt_term->slug])?$output_terms[$p->taxonomy][$adpt_prnt_term->slug]:0);
									$adpt_prnt = ( ( $adpt_prnt_val = intval( $adpt_prnt_term->parent ) ) > 0 ? $adpt_prnt_val : 0 );
								}
							}

						}

					}

				}

			}

			if ( isset( $output_terms ) ) {
				self::$settings['adoptive'] = $output_terms;
			}

		}

		public static function check_adoptive() {

			if ( !in_array( self::$filter['name'], array( 'range', 'attributes' ) ) ) {
				return true;
			}

			switch ( self::$filter['filter'] ) {
				case 'range' :
					if ( self::$filter['taxonomy'] !== 'price' && WC_Prdctfltr::$settings['instance']['total'] !== 0 && self::$filter['adoptive'] == 'yes' && ( isset( self::$settings['adoptive'] ) && ( !isset( self::$settings['adoptive'][self::$filter['taxonomy']] ) || isset( self::$settings['adoptive'][self::$filter['taxonomy']] ) && empty( self::$settings['adoptive'][self::$filter['taxonomy']]) ) === true ) ) {
						return false;
					}
				break;
				default :

					if ( self::$settings['instance']['total'] !== 0 && self::$filter['adoptive'] == 'yes' /*&& self::$settings['instance']['wc_settings_prdctfltr_adoptive_style'] == 'pf_adptv_default'*/ && ( isset( self::$settings['adoptive'] ) && ( !isset( self::$settings['adoptive'][self::$filter['slug']] ) || isset( self::$settings['adoptive'][self::$filter['slug']] ) && empty( self::$settings['adoptive'][self::$filter['slug']]) ) === true ) ) {
						return false;
					}

				break;
			}

			return true;

		}

		function cleanup() {
			remove_filter( 'woocommerce_is_filtered', 'WC_Prdctfltr::return_true' );
		}

	}

	add_action( 'woocommerce_init', array( 'WC_Prdctfltr', 'init' ) );

	if ( !function_exists( 'mnthemes_add_meta_information' ) ) {
		function mnthemes_add_meta_information_action() {
			$val = apply_filters( 'mnthemes_add_meta_information_used', array() );
			if ( !empty( $val ) ) {
				echo '<meta name="generator" content="' . implode( ', ', $val ) . '"/>';
			}
		}
		function mnthemes_add_meta_information() {
			add_action( 'wp_head', 'mnthemes_add_meta_information_action', 99 );
		}
		mnthemes_add_meta_information();
	}

?>
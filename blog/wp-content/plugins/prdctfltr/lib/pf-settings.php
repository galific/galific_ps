<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	class WC_Settings_Prdctfltr {

		public static function init() {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::prdctfltr_admin_scripts' );
			add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::prdctfltr_add_settings_tab', 49 );
			add_action( 'woocommerce_settings_tabs_settings_products_filter', __CLASS__ . '::prdctfltr_settings_tab' );
			add_action( 'woocommerce_update_options_settings_products_filter', __CLASS__ . '::prdctfltr_update_settings' );
			add_action( 'woocommerce_admin_field_pf_taxonomy', __CLASS__ . '::prdctfltr_pf_taxonomy', 10 );
			add_action( 'woocommerce_admin_field_pf_filter', __CLASS__ . '::prdctfltr_pf_filter', 10 );
			add_action( 'woocommerce_admin_field_pf_filter_analytics', __CLASS__ . '::prdctfltr_pf_filter_analytics', 10 );
			add_action( 'woocommerce_admin_settings_sanitize_option', __CLASS__ . '::prdctfltr_pf_taxonomy_sanitize', 10, 3 );
			add_action( 'wp_ajax_prdctfltr_admin_save', __CLASS__ . '::prdctfltr_admin_save' );
			add_action( 'wp_ajax_prdctfltr_admin_load', __CLASS__ . '::prdctfltr_admin_load' );
			add_action( 'wp_ajax_prdctfltr_admin_delete', __CLASS__ . '::prdctfltr_admin_delete' );
			add_action( 'wp_ajax_prdctfltr_or_add', __CLASS__ . '::prdctfltr_or_add' );
			add_action( 'wp_ajax_prdctfltr_or_remove', __CLASS__ . '::prdctfltr_or_remove' );
			add_action( 'wp_ajax_prdctfltr_m_fields', __CLASS__ . '::prdctfltr_m_fields' );
			add_action( 'wp_ajax_prdctfltr_c_fields', __CLASS__ . '::prdctfltr_c_fields' );
			add_action( 'wp_ajax_prdctfltr_c_terms', __CLASS__ . '::prdctfltr_c_terms' );
			add_action( 'wp_ajax_prdctfltr_r_fields', __CLASS__ . '::prdctfltr_r_fields' );
			add_action( 'wp_ajax_prdctfltr_r_terms', __CLASS__ . '::prdctfltr_r_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms', __CLASS__ . '::set_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms_new_style', __CLASS__ . '::set_terms_new' );
			add_action( 'wp_ajax_prdctfltr_set_terms_save_style', __CLASS__ . '::save_terms' );
			add_action( 'wp_ajax_prdctfltr_set_terms_remove_style', __CLASS__ . '::remove_terms' );
			add_action( 'wp_ajax_prdctfltr_set_filters', __CLASS__ . '::set_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_add', __CLASS__ . '::add_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_new_style', __CLASS__ . '::set_filters_new' );
			add_action( 'wp_ajax_prdctfltr_set_filters_save_style', __CLASS__ . '::save_filters' );
			add_action( 'wp_ajax_prdctfltr_set_filters_remove_style', __CLASS__ . '::remove_filters' );
			add_action( 'wp_ajax_prdctfltr_reset', __CLASS__ . '::reset_options' );
			add_action( 'wp_ajax_prdctfltr_analytics_reset', __CLASS__ . '::analytics_reset' );

			$plugin = Prdctfltr()->plugin_basename();
			add_filter( 'plugin_action_links_' . $plugin, __CLASS__ . '::settings_link' );
		}

		public static function settings_link( $links ) {

			$settings_link = '<a href="admin.php?page=wc-settings&tab=settings_products_filter">' . esc_html__( 'Settings', 'nwscds' ) . '</a>';
			array_push( $links, $settings_link );

			return $links;

		}

		public static function prdctfltr_admin_scripts( $hook ) {

			if ( isset( $_GET['page'], $_GET['tab'] ) && ( $_GET['page'] == 'wc-settings' || $_GET['page'] == 'woocommerce_settings' ) && $_GET['tab'] == 'settings_products_filter' ) {

				//wp_enqueue_style( 'prdctfltr-admin', Prdctfltr()->plugin_url() .'/lib/css/admin' . ( is_rtl() ? '-rtl' : '' ) . '.css', false, PrdctfltrInit::$version );
				wp_enqueue_style( 'prdctfltr-admin', Prdctfltr()->plugin_url() .'/lib/css/admin' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', false, PrdctfltrInit::$version );

				wp_register_script( 'prdctfltr-settings', Prdctfltr()->plugin_url() . '/lib/js/admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-sortable' ), PrdctfltrInit::$version, true );
				wp_enqueue_script( 'prdctfltr-settings' );

				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );

				if ( function_exists( 'wp_enqueue_media' ) ) {
					wp_enqueue_media();
				}

				$dec_separator = get_option( 'woocommerce_price_decimal_sep' );

				$curr_args = array(
					'ajax' => admin_url( 'admin-ajax.php' ),
					'url' => Prdctfltr()->plugin_url(),
					'decimal_separator' => $dec_separator,
					'characteristics' => get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ),
					'localization' => array(
						'activate' => esc_html__( 'Activate?', 'prdctfltr' ),
						'deactivate' => esc_html__( 'Deactivate?', 'prdctfltr' ),
						'delete' => esc_html__( 'Delete?', 'prdctfltr' ),
						'remove' => esc_html__( 'Remove?', 'prdctfltr' ),
						'remove_key' => esc_html__( 'Remove key from database?', 'prdctfltr' ),
						'add_override' => esc_html__( 'Add override?', 'prdctfltr' ),
						'remove_override' => esc_html__( 'Remove override?', 'prdctfltr' ),
						'override_notice' => esc_html__( 'Please select both term and the filter preset.', 'prdctfltr' ),
						'added' => esc_html__( 'Added!', 'prdctfltr' ),
						'load' => esc_html__( 'Load?', 'prdctfltr' ),
						'saved' => esc_html__( 'Saved!', 'prdctfltr' ),
						'ajax_error' => esc_html__( 'AJAX Error!', 'prdctfltr' ),
						'missing_settings' => esc_html__( 'Missing name or settings.', 'prdctfltr' ),
						'not_selected' => esc_html__( 'Not selected!', 'prdctfltr' ),
						'deleted' => esc_html__( 'Deleted!', 'prdctfltr' ),
						'customization_save' => esc_html__( 'Customization created!', 'prdctfltr' ) . ' ' . esc_html__( 'Please save your default or your current filter preset to make customization changes!', 'prdctfltr' ),
						'customization_removed' => esc_html__( 'Removed!', 'prdctfltr' ) . ' ' . esc_html__( 'Please save your default or your current filter preset to make customization changes!', 'prdctfltr' ),
						'delete_analytics' => esc_html__( 'Analytics data deleted!', 'prdctfltr' ),
						'adv_filter' => esc_html__( 'Advanced Filter', 'prdctfltr' ),
						'rng_filter' => esc_html__( 'Range Filter', 'prdctfltr' ),
						'mta_filter' => esc_html__( 'Meta Filter', 'prdctfltr' ),
						'decimal_error' =>  esc_html__( 'Use only numbers and the decimal separator!', 'prdctfltr' ) . ' ( ' . $dec_separator . ' )',
						'remove_override_single' =>  esc_html__( 'Remove Override', 'prdctfltr' ),
						'term_slug' => esc_html__( 'Term slug', 'prdctfltr' ),
						'filter_preset' => esc_html__( 'Filter Preset', 'prdctfltr' ),
						'loaded' => esc_html__( 'Loaded!', 'prdctfltr' ),
						'removed' => esc_html__( 'Removed!', 'prdctfltr' ),
						'invalid_key' => esc_html__( 'Invalid key! Cannot be removed from database! Please save your settings.', 'prdctfltr' ),
						'reset_options' => esc_html__( 'This action will reset ALL Product Filter options, presets and overrides! Are you sure?', 'prdctfltr' ),
						'saving_options' => esc_html__( 'Saving options, please wait!', 'prdctfltr' ),
						'loading_options' => esc_html__( 'Loading options, please wait!', 'prdctfltr' ),
						'deleting_options' => esc_html__( 'Deleting preset, please wait!', 'prdctfltr' ),
						'save' => esc_html__( 'Save default?', 'prdctfltr' ),
					)
				);
				wp_localize_script( 'prdctfltr-settings', 'prdctfltr', $curr_args );
			}

		}

		public static function prdctfltr_pf_filter_analytics( $field ) {

		if ( get_option( 'wc_settings_prdctfltr_use_analytics', 'no' ) == 'no' ) {
			return '';
		}

		global $woocommerce;
?>
		<tr valign="top" class="">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo '<img class="help_tip" data-tip="' . esc_attr( $field['desc'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />'; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<div class="prdctfltr_filtering_analytics_wrapper">
			<?php
				$stats = get_option( 'wc_settings_prdctfltr_filtering_analytics_stats', array() );

				if ( empty( $stats ) ) {
					esc_html_e( 'Filtering Analytics are empty! Please enable the filtering analytics and wait for the results! Thank you!', 'prdctfltr' );
				}
				else {
					?>
					<div class="prdctfltr_filtering_analytics_settings">
						<a href="#" class="button-primary prdctfltr_filtering_analytics_reset"><?php esc_html_e( 'Reset Analytics', 'prdctfltr' ); ?></a>
					</div>
					<?php

					foreach( $stats as $k => $v ) {
						$total_count = 0
					?>
						<div class="prdctfltr_filtering_analytics">
							<h3 class="prdctfltr_filtering_analytics_title">
							<?php
								$mode = 'default';
								if ( taxonomy_exists( $k ) ) {
									$mode = 'taxonomy';
									if ( substr( $k, 0, 3 ) == 'pa_' ) {
										$label = wc_attribute_label( $k );
									}
									else {
										if ( $k == 'product_cat' ) {
											$label = esc_html__( 'Categories', 'prdctfltr' );
										}
										else if ( $k == 'product_tag' ) {
											$label = esc_html__( 'Tags', 'prdctfltr' );
										}
										else if ( $k == 'characteristics' ) {
											$label = esc_html__( 'Characteristics', 'prdctfltr' );
										}
										else {
											$curr_term = get_taxonomy( $k );
											$label = $curr_term->name;
										}
									}
								}


								if ( $mode == 'taxonomy' ) {
									if ( !empty( $v ) && is_array( $v ) ) {
										foreach( $v as $vk => $vv ) {
											$term = get_term_by( 'slug', $vk, $k );
											if ( isset( $term->name ) ) {
												$term_name = ucfirst( $term->name ) . ' ( ' . $v[$vk] .' )';
											}
											else {
												$term_name = 'Unknown Term';
											}
											

											$v[$term_name] = $v[$vk];
											$total_count = $total_count + $v[$vk];
											unset( $v[$vk] );
										}
										echo esc_html__( 'Filter', 'prdctfltr' ) . ' <em>' . ucfirst( $label ) . '</em> - ' . esc_html__( 'Total hits count:' ) . ' ' . $total_count;
									}
								}
								else {
									echo esc_html__( 'Filter', 'prdctfltr' ) . ' <em>' . ucfirst( $k ) . '</em>';
								}
		
							?>
							</h3>
							<div id="prdctfltr_filtering_analytics_<?php echo sanitize_title( $k ); ?>" class="prdctfltr_filtering_analytics_chart" data-chart-title="<?php echo esc_attr( esc_html__( 'Filtering data for taxonomy', 'prdctfltr' ) . ': ' . $k ); ?>" data-chart="<?php echo esc_attr( json_encode( $v ) ); ?>"></div>
						</div>
					<?php
					}
			?>
					<script type="text/javascript" src="https://www.google.com/jsapi"></script>
					<script type="text/javascript">
						(function( $){
						"use strict";

							google.load( 'visualization', '1.0', {'packages':['corechart']});

							google.setOnLoadCallback(drawCharts);

							function drawCharts() {

								$( '.prdctfltr_filtering_analytics_chart' ).each( function() {

									var el = $(this).attr( 'id' );
									var chartData = $.parseJSON( $(this).attr( 'data-chart' ));
									var chartDataTitle = $(this).attr( 'data-chart-title' );

									var chartArray = [];
									for (var key in chartData) {
										if (chartData.hasOwnProperty(key)) {
											chartArray.push([key, chartData[key]] );
										}
									};

									var data = new google.visualization.DataTable();
									data.addColumn( 'string', 'Term' );
									data.addColumn( 'number', 'Count' );
									data.addRows(chartArray);

									var options = {'title':chartDataTitle,'is3D':true,'chartArea':{'width':'100%','height':'80%'},'legend':{'position':'bottom'}};

									var chart = new google.visualization.PieChart(document.getElementById(el));
									chart.draw(data, options);

								});

							}
						})(jQuery);
					</script>
			<?php
				}
			?>
				</div>
			</td>
		</tr>
<?php
		}

		public static function get_dropdown( $tax, $option_value, $name, $id ) {

				$readyVals = array();
				if ( taxonomy_exists( $tax ) ) {

					$terms = get_terms( $tax, array( 'hide_empty' => 0, 'hierarchical' => ( is_taxonomy_hierarchical( $tax ) ? 1 : 0 ) ) );
					if ( is_taxonomy_hierarchical( $tax ) ) {
						$terms_sorted = array();
						self::sort_terms_hierarchicaly( $terms, $terms_sorted );
						$terms = $terms_sorted;
					}

					if ( !empty( $terms ) && !is_wp_error( $terms ) ){
						$var =0;
						self::get_option_terms( $terms, $readyVals, $var );
					}

				}
			?>
				<select
					name="<?php echo $name; ?>"
					id="<?php echo $id; ?>"
					style="width:300px;margin-right:12px;"
					multiple="multiple"
					>
					<?php
						foreach ( $readyVals as $key => $val ) {
							?>
							<option value="<?php echo esc_attr( $key ); ?>" <?php
								if ( is_array( $option_value ) ) {
									selected( in_array( $key, $option_value ), true );
								} else {
									selected( $option_value, $key );
								}
							?>><?php echo $val ?></option>
							<?php
						}
					?>
				</select>
			<?php

		}

		public static function prdctfltr_pf_filter( $field ) {

		global $woocommerce;
	?>

		<tr valign="top">
			<th scope="row" class="titledesc" style="display:none;">
				<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
				<?php echo '<img class="help_tip" data-tip="' . esc_attr( $field['desc'] ) . '" src="' . $woocommerce->plugin_url() . '/assets/images/help.png" height="16" width="16" />'; ?>
			</th>
			<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<?php

					$pf_filters_selected = get_option( 'wc_settings_prdctfltr_active_filters', array( 'sort','price','cat' ) );

					$curr_filters = array(
						'sort' => esc_html__( 'Sort By', 'prdctfltr' ),
						'price' => esc_html__( 'By Price', 'prdctfltr' ),
						'cat' => esc_html__( 'By Categories', 'prdctfltr' ),
						'tag' => esc_html__( 'By Tags', 'prdctfltr' ),
						'char' => esc_html__( 'By Characteristics', 'prdctfltr' ),
						'vendor' => esc_html__( 'Vendor', 'prdctfltr' ),
						'instock' => esc_html__( 'In Stock Filter', 'prdctfltr' ),
						'per_page' => esc_html__( 'Products Per Page', 'prdctfltr' ),
						'search' => esc_html__( 'Search Filter', 'prdctfltr' )
					);

					if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'no' ) {
						unset( $curr_filters['char'] );
					}

					$curr_attr = array();
					if ( $attribute_taxonomies = wc_get_attribute_taxonomies() ) {
						foreach ( $attribute_taxonomies as $tax ) {
							$curr_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
							$curr_attr['pa_' . $tax->attribute_name] = ucfirst( $curr_label );
						}
					}

					$pf_filters = $curr_filters + $curr_attr;

				?>
				<div class="form-field prdctfltr_customizer_static">
					<div class="pf_element" data-filter="basic">
						<span><?php esc_html_e( 'General Settings', 'prdctfltr' ); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
					<div class="pf_element" data-filter="style">
						<span><?php esc_html_e( 'Filter Style', 'prdctfltr' ); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
					<div class="pf_element" data-filter="adoptive">
						<span><?php esc_html_e( 'Adoptive Filtering', 'prdctfltr' ); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
					<div class="pf_element" data-filter="mobile">
						<span><?php esc_html_e( 'Mobile Preset', 'prdctfltr' ); ?></span>
						<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
						<div class="pf_options_holder"></div>
					</div>
				</div>
				<h3><?php esc_html_e( 'Filter Settings', 'prdctfltr' ); ?></h3>
				<p class="pf-desc">
					<?php echo esc_html__( 'Create filters! Greens are active, reds are not. Blue buttons add as many filters as you need! Need help? Check this link', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/" target="_blank">' . esc_html__( 'Product Filter - Documentation and Guide', 'prdctfltr' ) . '</a>'; ?>
				</p>
				<p class="form-field prdctfltr_customizer_fields"<?php echo ( self::prdctfltr_wpml_language() !== false ? ' data-lang="' . ICL_LANGUAGE_CODE . '"' : '' ); ?>>
				<?php
					foreach ( $pf_filters as $k => $v ) {
						if ( in_array( $k, $pf_filters_selected ) ) {
							$add['class'] = ' pf_active';
							$add['icon'] = '<i class="prdctfltr-eye"></i>';
						}
						else {
							$add['class'] = '';
							$add['icon'] = '<i class="prdctfltr-eye-disabled"></i>';
						}
				?>
					<a href="#" class="prdctfltr_c_add_filter<?php echo $add['class']; ?>" data-filter="<?php echo $k; ?>">
						<?php echo $add['icon']; ?> 
						<span><?php echo $v; ?></span>
					</a>
				<?php
					}
				?>
					<a href="#" class="prdctfltr_c_add pf_advanced"><i class="prdctfltr-plus"></i> <span><?php esc_html_e( 'Add advanced filter', 'prdctfltr' ); ?></span></a>
					<a href="#" class="prdctfltr_c_add pf_range"><i class="prdctfltr-plus"></i> <span><?php esc_html_e( 'Add range filter', 'prdctfltr' ); ?></span></a>
					<a href="#" class="prdctfltr_c_add pf_meta"><i class="prdctfltr-plus"></i> <span><?php esc_html_e( 'Add meta filter', 'prdctfltr' ); ?></span></a>
				</p>
				<div class="form-field prdctfltr_customizer">
				<?php

					if ( isset( $_POST['pfa_taxonomy'] ) ) {

						$pf_filters_advanced = array();

						for( $i = 0; $i < count( $_POST['pfa_taxonomy'] ); $i++ ) {
							$pf_filters_advanced['pfa_title'][$i] = $_POST['pfa_title'][$i];
							$pf_filters_advanced['pfa_description'][$i] = $_POST['pfa_description'][$i];
							$pf_filters_advanced['pfa_taxonomy'][$i] = $_POST['pfa_taxonomy'][$i];
							$pf_filters_advanced['pfa_include'][$i] = ( isset( $_POST['pfa_include'][$i] ) ? $_POST['pfa_include'][$i] : array() );
							$pf_filters_advanced['pfa_orderby'][$i] = ( isset( $_POST['pfa_orderby'][$i] ) ? $_POST['pfa_orderby'][$i] : '' );
							$pf_filters_advanced['pfa_order'][$i] = ( isset( $_POST['pfa_order'][$i] ) ? $_POST['pfa_order'][$i] : '' );
							$pf_filters_advanced['pfa_multiselect'][$i] = ( isset( $_POST['pfa_multiselect'][$i] ) ? $_POST['pfa_multiselect'][$i] : 'no' );
							$pf_filters_advanced['pfa_relation'][$i] = ( isset( $_POST['pfa_relation'][$i] ) ? $_POST['pfa_relation'][$i] : 'OR' );
							$pf_filters_advanced['pfa_adoptive'][$i] = ( isset( $_POST['pfa_adoptive'][$i] ) ? $_POST['pfa_adoptive'][$i] : 'no' );
							$pf_filters_advanced['pfa_selection'][$i] = ( isset( $_POST['pfa_selection'][$i] ) ? $_POST['pfa_selection'][$i] : 'no' );
							$pf_filters_advanced['pfa_none'][$i] = ( isset( $_POST['pfa_none'][$i] ) ? $_POST['pfa_none'][$i] : 'no' );
							$pf_filters_advanced['pfa_limit'][$i] = ( isset( $_POST['pfa_limit'][$i] ) ? $_POST['pfa_limit'][$i] : '' );
							$pf_filters_advanced['pfa_hierarchy'][$i] = ( isset( $_POST['pfa_hierarchy'][$i] ) ? $_POST['pfa_hierarchy'][$i] : 'no' );
							$pf_filters_advanced['pfa_hierarchy_mode'][$i] = ( isset( $_POST['pfa_hierarchy_mode'][$i] ) ? $_POST['pfa_hierarchy_mode'][$i] : 'no' );
							$pf_filters_advanced['pfa_mode'][$i] = ( isset( $_POST['pfa_mode'][$i] ) ? $_POST['pfa_mode'][$i] : 'showall' );
							$pf_filters_advanced['pfa_style'][$i] = ( isset( $_POST['pfa_style'][$i] ) ? $_POST['pfa_style'][$i] : 'pf_attr_text' );
							$pf_filters_advanced['pfa_term_customization'][$i] = ( isset( $_POST['pfa_term_customization'][$i] ) ? $_POST['pfa_term_customization'][$i] : '' );
						}

					}
					else {
						$pf_filters_advanced = get_option( 'wc_settings_prdctfltr_advanced_filters' );
					}

					if ( isset( $_POST['pfr_taxonomy'] ) ) {

						$pf_filters_range = array();

						for( $i = 0; $i < count( $_POST['pfr_taxonomy'] ); $i++ ) {
							$pf_filters_range['pfr_title'][$i] = $_POST['pfr_title'][$i];
							$pf_filters_range['pfr_description'][$i] = $_POST['pfr_description'][$i];
							$pf_filters_range['pfr_taxonomy'][$i] = $_POST['pfr_taxonomy'][$i];
							$pf_filters_range['pfr_include'][$i] = ( isset( $_POST['pfr_include'][$i] ) ? $_POST['pfr_include'][$i] : array() );
							$pf_filters_range['pfr_orderby'][$i] = ( isset( $_POST['pfr_orderby'][$i] ) ? $_POST['pfr_orderby'][$i] : '' );
							$pf_filters_range['pfr_order'][$i] = ( isset( $_POST['pfr_order'][$i] ) ? $_POST['pfr_order'][$i] : '' );
							$pf_filters_range['pfr_style'][$i] = ( isset( $_POST['pfr_style'][$i] ) ? $_POST['pfr_style'][$i] : 'flat' );
							$pf_filters_range['pfr_grid'][$i] = ( isset( $_POST['pfr_grid'][$i] ) ? $_POST['pfr_grid'][$i] : 'no' );
							$pf_filters_range['pfr_adoptive'][$i] = ( isset( $_POST['pfr_adoptive'][$i] ) ? $_POST['pfr_adoptive'][$i] : 'no' );
							$pf_filters_range['pfr_custom'][$i] = ( isset( $_POST['pfr_custom'][$i] ) ? stripslashes( $_POST['pfr_custom'][$i] ) : '' );
						}

					}
					else {
						$pf_filters_range = get_option( 'wc_settings_prdctfltr_range_filters' );
					}

					if ( isset( $_POST['pfm_key'] ) ) {

						$pf_filters_meta = array();

						for( $i = 0; $i < count( $_POST['pfm_key'] ); $i++ ) {
							$pf_filters_meta['pfm_title'][$i] = $_POST['pfm_title'][$i];
							$pf_filters_meta['pfm_description'][$i] = $_POST['pfm_description'][$i];
							$pf_filters_meta['pfm_key'][$i] = $_POST['pfm_key'][$i];
							$pf_filters_meta['pfm_compare'][$i] = ( isset( $_POST['pfm_compare'][$i] ) ? $_POST['pfm_compare'][$i] : '=' );
							$pf_filters_meta['pfm_type'][$i] = ( isset( $_POST['pfm_type'][$i] ) ? $_POST['pfm_type'][$i] : 'NUMERIC' );
							$pf_filters_meta['pfm_limit'][$i] = ( isset( $_POST['pfm_limit'][$i] ) ? $_POST['pfm_limit'][$i] : '' );
							$pf_filters_meta['pfm_multiselect'][$i] = ( isset( $_POST['pfm_multiselect'][$i] ) ? $_POST['pfm_multiselect'][$i] : 'no' );
							$pf_filters_meta['pfm_relation'][$i] = ( isset( $_POST['pfm_relation'][$i] ) ? $_POST['pfm_relation'][$i] : 'OR' );
							$pf_filters_meta['pfm_none'][$i] = ( isset( $_POST['pfm_none'][$i] ) ? $_POST['pfm_none'][$i] : 'no' );
							$pf_filters_meta['pfm_term_customization'][$i] = ( isset( $_POST['pfm_term_customization'][$i] ) ? $_POST['pfm_term_customization'][$i] : '' );
							$pf_filters_meta['pfm_filter_customization'][$i] = ( isset( $_POST['pfm_filter_customization'][$i] ) ? $_POST['pfm_filter_customization'][$i] : '' );
						}

					}
					else {
						$pf_filters_meta = get_option( 'wc_settings_prdctfltr_meta_filters' );
					}

					if ( $pf_filters_advanced === false ) {
						$pf_filters_advanced = array();
					}

					if ( $pf_filters_range === false ) {
						$pf_filters_range = array();
					}
					if ( $pf_filters_meta === false ) {
						$pf_filters_meta = array();
					}

					$i=0;$q=0;$y=0;

					foreach ( $pf_filters_selected as $v ) {
						if ( $v == 'advanced' && !empty( $pf_filters_advanced ) && isset( $pf_filters_advanced['pfa_taxonomy'][$i] ) ) {
					?>
							<div class="pf_element adv" data-filter="advanced" data-id="<?php echo $i; ?>">
								<span><?php esc_html_e( 'Advanced Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h2><?php esc_html_e( 'Advanced Filter', 'prdctfltr' ); ?></h2>
									<p><?php echo esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_title_%1$s">%2$s</label>', $i, esc_html__( 'Filter Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfa_title[%1$s]" id="pfa_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $i, isset( $pf_filters_advanced['pfa_title'][$i] ) ? $pf_filters_advanced['pfa_title'][$i] : '' );
													?>
													<span class="description"><?php echo esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_description_%1$s">%2$s</label>', $i, esc_html__( 'Filter Description', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php esc_html_e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfa_description[%1$s]" id="pfa_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $i, ( isset( $pf_filters_advanced['pfa_description'][$i] ) ? stripslashes( $pf_filters_advanced['pfa_description'][$i] ) : '' ) );
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													$taxonomies = get_object_taxonomies( 'product', 'object' );
													printf( '<label for="pfa_taxonomy_%1$s">%2$s</label>', $i, esc_html__( 'Select Taxonomy', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														printf( '<select id="pfa_taxonomy_%1$s" name="pfa_taxonomy[%1$s]" class="prdctfltr_adv_select" style="width:300px;margin-right:12px;">', $i) ;
														foreach ( $taxonomies as $k => $v ) {
															if ( in_array( $k, array( 'product_type' ) ) ) {
																continue;
															}
															echo '<option value="' . $k . '"' . ( $pf_filters_advanced['pfa_taxonomy'][$i] == $k ? ' selected="selected"' : '' ) .'>' . ( substr( $v->name, 0, 3 ) == 'pa_' ? wc_attribute_label( $v->name ) : $v->label ) . '</option>';
														}
														echo '</select>';
													?>
													<span class="description"><?php esc_html_e( 'Select filter product taxonomy.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_include_%1$s">%2$s</label>', $i, esc_html__( 'Select Terms', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-multiselect">
												<?php
													$tax = isset( $pf_filters_advanced['pfa_taxonomy'][$i] ) && taxonomy_exists( $pf_filters_advanced['pfa_taxonomy'][$i] ) ? $pf_filters_advanced['pfa_taxonomy'][$i] : $first_tax;
													if ( !empty( $tax ) ) {

														$name = 'pfa_include[' . $i . '][]';
														$id ='pfa_include_' . $i;
														$option_value = $pf_filters_advanced['pfa_include'][$i];
														self::get_dropdown( $tax, $option_value, $name, $id );

													}
													else {
														printf( '<select name="pfa_include[%1$s][]" id="pfa_include_%1$s" multiple="multiple" style="width:300px;margin-right:12px;"></select>', $i );
													}
												?>
													<span class="description"><?php echo esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_style_%1$s">%2$s</label>', $i, esc_html__( 'Appearance', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'pf_attr_text' => esc_html__( 'Text', 'prdctfltr' ),
															'pf_attr_imgtext' => esc_html__( 'Thumbnails with text', 'prdctfltr' ),
															'pf_attr_img' => esc_html__( 'Thumbnails only', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_style'][$i] ) && $pf_filters_advanced['pfa_style'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_style[%2$s]" id="pfa_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php esc_html_e( 'Select style preset to use with the current taxonomy (works only with product attributes).', 'prdctfltr' ); ?><em class="pf_deprecated"></em></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_orderby_%1$s">%2$s</label>', $i, esc_html__( 'Terms Order By', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$orderby_params = array(
															'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
															'id' => esc_html__( 'ID', 'prdctfltr' ),
															'name' => esc_html__( 'Name', 'prdctfltr' ),
															'number' => esc_html__( 'Number', 'prdctfltr' ),
															'slug' => esc_html__( 'Slug', 'prdctfltr' ),
															'count' => esc_html__( 'Count', 'prdctfltr' )
														);

														foreach ( $orderby_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_orderby'][$i] ) && $pf_filters_advanced['pfa_orderby'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_orderby[%2$s]" id="pfa_orderby_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php esc_html_e( 'Select term ordering.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_order_%1$s">%2$s</label>', $i, esc_html__( 'Terms Order', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$order_params = array(
															'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
															'DESC' => esc_html__( 'DESC', 'prdctfltr' )
														);

														foreach ( $order_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_order'][$i] ) && $pf_filters_advanced['pfa_order'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_order[%2$s]" id="pfa_order_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php esc_html_e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_limit_%1$s">%2$s</label>', $i, esc_html__( 'Show More Button', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-number">
													<?php
														printf( '<input name="pfa_limit[%1$s]" id="pfa_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $i, isset( $pf_filters_advanced['pfa_limit'][$i] ) ? $pf_filters_advanced['pfa_limit'][$i] : '' ); ?>
													<span class="description"><?php esc_html_e( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_hierarchy_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_hierarchy[%1$s]" id="pfa_hierarchy_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_hierarchy'][$i] ) && $pf_filters_advanced['pfa_hierarchy'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to enable hierarchy on current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_mode_%1$s">%2$s</label>', $i, esc_html__( 'Taxonomy Hierarchy Filtering Mode', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'showall' => esc_html__( 'Show all', 'prdctfltr' ),
															'drill' => esc_html__( 'Show same level only (Drill filter)', 'prdctfltr' ),
															'drillback' => esc_html__( 'Drill filter with removable parent', 'prdctfltr' ),
															'subonly' => esc_html__( 'Show only child terms, selected parents will not be removable', 'prdctfltr' ),
															'subonlyback' => esc_html__( 'Show only child terms with removable parent', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_mode'][$i] ) && $pf_filters_advanced['pfa_mode'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_mode[%2$s]" id="pfa_mode_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php esc_html_e( 'Select filter hierarchy mode.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Taxonomy Expand Parents', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Taxonomy Expand Parents', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_hierarchy_mode_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_hierarchy_mode[%1$s]" id="pfa_hierarchy_mode_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_hierarchy_mode'][$i] ) && $pf_filters_advanced['pfa_hierarchy_mode'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( ' Check this option to expand parent terms on load.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Use Multi Select', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Use Multi Select', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_multiselect_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_multiselect[%1$s]" id="pfa_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_multiselect'][$i] ) && $pf_filters_advanced['pfa_multiselect'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_relation_%1$s">%2$s</label>', $i, esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
															'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_advanced['pfa_relation'][$i] ) && $pf_filters_advanced['pfa_relation'][$i] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfa_relation[%2$s]" id="pfa_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $i );
													?>
													<span class="description"><?php esc_html_e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Selection Change Reset', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Selection Change Reset', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_selection_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_selection[%1$s]" id="pfa_selection_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_selection'][$i] ) && $pf_filters_advanced['pfa_selection'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to reset other filters when this one is used.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_adoptive_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_adoptive[%1$s]" id="pfa_adoptive_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_adoptive'][$i] ) && $pf_filters_advanced['pfa_adoptive'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Hide None', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Hide None', 'prdctfltr' );
														?>
														</legend>
														<label for="pfa_none_<?php echo $i; ?>">
														<?php
															printf( '<input name="pfa_none[%1$s]" id="pfa_none_%1$s" type="checkbox" value="yes" %2$s />', $i, ( isset( $pf_filters_advanced['pfa_none'][$i] ) && $pf_filters_advanced['pfa_none'][$i] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfa_term_customization_%1$s">%2$s</label>', $i, esc_html__( 'Style Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfa_term_customization[%1$s]" id="pfa_term_customization_%1$s" class="pf_term_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $i, ( isset( $pf_filters_advanced['pfa_term_customization'][$i] ) ? $pf_filters_advanced['pfa_term_customization'][$i] : '' ) );
													?>
													<span class="description"><?php esc_html_e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$i++;
						}
						else if ( $v == 'range' && !empty( $pf_filters_range ) && isset( $pf_filters_range['pfr_taxonomy'][$q] ) ) {
					?>
							<div class="pf_element rng" data-filter="range" data-id="<?php echo $q; ?>">
								<span><?php esc_html_e( 'Range Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h2><?php esc_html_e( 'Range Filter', 'prdctfltr' ); ?></h2>
									<p><?php echo esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_title_%1$s">%2$s</label>', $q, esc_html__( 'Filter Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfr_title[%1$s]" id="pfr_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $q, $pf_filters_range['pfr_title'][$q] );
													?>
													<span class="description"><?php echo esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_description_%1$s">%2$s</label>', $q, esc_html__( 'Filter Description', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php esc_html_e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfr_description[%1$s]" id="pfr_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $q, ( isset( $pf_filters_range['pfr_description'][$q] ) ? stripslashes( $pf_filters_range['pfr_description'][$q] ) : '' ) );
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_taxonomy_%1$s">%2$s</label>', $q, esc_html__( 'Select Range', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$taxonomies = get_object_taxonomies( 'product', 'object' );
														printf( '<select name="pfr_taxonomy[%1$s]" id="pfr_taxonomy_%1$s" class="prdctfltr_rng_select"  style="width:300px;margin-right:12px;">', $q );
														echo '<option value="price"' . ( $pf_filters_range['pfr_taxonomy'][$q] == 'price' ? ' selected="selected"' : '' ) . '>' . esc_html__( 'Price range', 'prdctfltr' ) . '</option>';
														foreach ( $taxonomies as $k => $v ) {
															if ( in_array( $k, array( 'product_type' ) ) ) {
																continue;
															}
															if ( substr( $k, 0, 3 ) == 'pa_' ) {
																$curr_label = wc_attribute_label( $v->name );
																$curr_value = $v->name;
															}
															else {
																$curr_label = $v->label;
																$curr_value = $k;
															}
															echo '<option value="' . $curr_value . '"' . ( $pf_filters_range['pfr_taxonomy'][$q] == '' . $curr_value ? ' selected="selected"' : '' ) .'>' . $curr_label . '</option>';
														}
														echo '</select>';
													?>
													<span class="description"><?php esc_html_e( 'Enter title for the current range filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_include_%1$s">%2$s</label>', $q, esc_html__( 'Select Terms', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-multiselect">
													<?php

														if ( isset( $pf_filters_range['pfr_taxonomy'][$q] ) && $pf_filters_range['pfr_taxonomy'][$q] !== 'price' ) {

															$tax = isset( $pf_filters_range['pfr_taxonomy'][$q] ) ? $pf_filters_range['pfr_taxonomy'][$q] : '';

															$name = 'pfr_include_' . $q . '[]';
															$id = 'pfr_include_' . $q;
															$option_value = $pf_filters_range['pfr_include'][$q];
															self::get_dropdown( $tax, $option_value, $name, $id );

															$add_disabled = '';

														}
														else {
															printf( '<select name="pfr_include[%1$s][]" id="pfr_include_%1$s" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>', $q );
															$add_disabled = ' disabled';
														}
													?>
													<span class="description"><?php echo esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_orderby_%1$s">%2$s</label>', $q, esc_html__( 'Terms Order By', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$orderby_params = array(
														'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
														'id' => esc_html__( 'ID', 'prdctfltr' ),
														'name' => esc_html__( 'Name', 'prdctfltr' ),
														'number' => esc_html__( 'Number', 'prdctfltr' ),
														'slug' => esc_html__( 'Slug', 'prdctfltr' ),
														'count' => esc_html__( 'Count', 'prdctfltr' )
													);
													foreach ( $orderby_params as $k => $v ) {
														$selected = ( isset( $pf_filters_range['pfr_orderby'][$q] ) && $pf_filters_range['pfr_orderby'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}
													printf( '<select name="pfr_orderby[%2$s]" id="pfr_orderby_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options, $q, $add_disabled );
												?>
													<span class="description"><?php esc_html_e( 'Select term ordering.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_order_%1$s">%2$s</label>', $q, esc_html__( 'Terms Order', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$order_params = array(
														'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
														'DESC' => esc_html__( 'DESC', 'prdctfltr' )
													);
													foreach ( $order_params as $k => $v ) {
														$selected = ( isset( $pf_filters_range['pfr_order'][$q] ) && $pf_filters_range['pfr_order'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}

													printf( '<select name="pfr_order[%2$s]" id="pfr_order_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q, $add_disabled );
												?>
													<span class="description"><?php esc_html_e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_style_%1$s">%2$s</label>', $q, esc_html__( 'Select Style', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
													$catalog_style = array(
														'flat' => esc_html__( 'Flat', 'prdctfltr' ),
														'modern' => esc_html__( 'Modern', 'prdctfltr' ),
														'html5' => esc_html__( 'HTML5', 'prdctfltr' ),
														'white' => esc_html__( 'White', 'prdctfltr' ),
														'thin' => esc_html__( 'Thin', 'prdctfltr' ),
														'knob' => esc_html__( 'Knob', 'prdctfltr' ),
														'metal' => esc_html__( 'Metal', 'prdctfltr' )
													);
													foreach ( $catalog_style as $k => $v ) {
														$selected = ( $pf_filters_range['pfr_style'][$q] == $k ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
													}

													printf( '<select name="pfr_style[%2$s]" id="pfr_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $q );
												?>
													<span class="description"><?php esc_html_e( 'Select current range style.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Use Grid', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Use Grid', 'prdctfltr' );
														?>
														</legend>
														<label for="pfr_grid_<?php echo $q; ?>">
														<?php
															printf( '<input name="pfr_grid[%2$s]" id="pfr_grid_%2$s" type="checkbox" value="yes"%1$s />', ( $pf_filters_range['pfr_grid'][$q] == 'yes' ? ' checked="checked"' : '' ), $q );
															esc_html_e( 'Check this option to use grid in current range.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
														?>
														</legend>
														<label for="pfr_adoptive_<?php echo $q; ?>">
														<?php
															printf( '<input name="pfr_adoptive[%2$s]" id="pfr_adoptive_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $pf_filters_range['pfr_adoptive'][$q] ) && $pf_filters_range['pfr_adoptive'][$q] == 'yes' ? ' checked="checked"' : '' ), $q );
															esc_html_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfr_custom_%1$s">%2$s</label>', $q, esc_html__( 'Custom Settings', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php esc_html_e( 'Enter custom settings for the range filter.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfr_custom[%1$s]" id="pfr_custom_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $q, ( isset( $pf_filters_range['pfr_custom'][$q] ) ? stripslashes( $pf_filters_range['pfr_custom'][$q] ) : '' ) );
													?>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$q++;
						}
						else if ( $v == 'meta' && !empty( $pf_filters_meta ) && isset( $pf_filters_meta['pfm_key'][$y] ) ) {
					?>
							<div class="pf_element mta" data-filter="meta" data-id="<?php echo $y; ?>">
								<span><?php esc_html_e( 'Meta Filter', 'prdctfltr' ); ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder">
									<h2><?php esc_html_e( 'Meta Filter', 'prdctfltr' ); ?></h2>
									<p><?php echo esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
									<table class="form-table">
										<tbody>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_title_%1$s">%2$s</label>', $y, esc_html__( 'Filter Title', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_title[%1$s]" id="pfm_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, isset( $pf_filters_meta['pfm_title'][$y] ) ? $pf_filters_meta['pfm_title'][$y] : '' );
													?>
													<span class="description"><?php echo esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_description_%1$s">%2$s</label>', $y, esc_html__( 'Filter Description', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-textarea">
													<p style="margin-top:0;"><?php esc_html_e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
													<?php
														printf( '<textarea name="pfm_description[%1$s]" id="pfm_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $y, ( isset( $pf_filters_meta['pfm_description'][$y] ) ? stripslashes( $pf_filters_meta['pfm_description'][$y] ) : '' ) );
													?>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_key_%1$s">%2$s</label>', $y, esc_html__( 'Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_key[%1$s]" id="pfm_key_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, isset( $pf_filters_meta['pfm_key'][$y] ) ? $pf_filters_meta['pfm_key'][$y] : '' );
													?>
													<span class="description"><?php echo esc_html__( 'Meta key.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_compare_%1$s">%2$s</label>', $y, esc_html__( 'Compare', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
							
													$meta_compares = array(
														array(
															'value' => '=',
															'label' => '='
														),
														array(
															'value' => '!=',
															'label' => '!='
														),
														array(
															'value' => '>',
															'label' => '>'
														),
														array(
															'value' => '<',
															'label' => '<'
														),
														array(
															'value' => '>=',
															'label' => '>='
														),
														array(
															'value' => '<=',
															'label' => '<='
														),
														array(
															'value' => 'LIKE',
															'label' => 'LIKE'
														),
														array(
															'value' => 'NOT LIKE',
															'label' => 'NOT LIKE'
														),
														array(
															'value' => 'IN',
															'label' => 'IN'
														),
														array(
															'value' => 'NOT IN',
															'label' => 'NOT IN'
														),
														array(
															'value' => 'EXISTS',
															'label' => 'EXISTS'
														),
														array(
															'value' => 'NOT EXISTS',
															'label' => 'NOT EXISTS'
														),
														array(
															'value' => 'BETWEEN',
															'label' => 'BETWEEN'
														),
														array(
															'value' => 'NOT BETWEEN',
															'label' => 'NOT BETWEEN'
														),
													);
													foreach ( $meta_compares as $k => $v ) {
														$selected = ( isset( $pf_filters_meta['pfm_compare'][$y] ) && $pf_filters_meta['pfm_compare'][$y] == $v['value'] ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
													}

													printf( '<select name="pfm_compare[%2$s]" id="pfm_compare_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $y );
												?>
													<span class="description"><?php esc_html_e( 'Meta compare.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_type_%1$s">%2$s</label>', $y, esc_html__( 'Type', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-select">
												<?php
													$curr_options = '';
							
													$meta_types = array(
														array(
															'value' => 'NUMERIC',
															'label' => 'NUMERIC'
														),
														array(
															'value' => 'BINARY',
															'label' => 'BINARY'
														),
														array(
															'value' => 'CHAR',
															'label' => 'CHAR'
														),
														array(
															'value' => 'DATE',
															'label' => 'DATE'
														),
														array(
															'value' => 'DATETIME',
															'label' => 'DATETIME'
														),
														array(
															'value' => 'DECIMAL',
															'label' => 'DECIMAL'
														),
														array(
															'value' => 'SIGNED',
															'label' => 'SIGNED'
														),
														array(
															'value' => 'TIME',
															'label' => 'TIME'
														),
														array(
															'value' => 'UNSIGNED',
															'label' => 'UNSIGNED'
														)
													);
													foreach ( $meta_types as $k => $v ) {
														$selected = ( isset( $pf_filters_meta['pfm_type'][$y] ) && $pf_filters_meta['pfm_type'][$y] == $v['value'] ? ' selected="selected"' : '' );
														$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
													}

													printf( '<select name="pfm_type[%2$s]" id="pfm_type_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $y );
												?>
													<span class="description"><?php esc_html_e( 'Meta type.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_limit_%1$s">%2$s</label>', $y, esc_html__( 'Show More Button', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-number">
													<?php
														printf( '<input name="pfm_limit[%1$s]" id="pfm_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $y, isset( $pf_filters_meta['pfm_limit'][$y] ) ? $pf_filters_meta['pfm_limit'][$y] : '' ); ?>
													<span class="description"><?php esc_html_e( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Use Multi Select', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Use Multi Select', 'prdctfltr' );
														?>
														</legend>
														<label for="pfm_multiselect_<?php echo $y; ?>">
														<?php
															printf( '<input name="pfm_multiselect[%1$s]" id="pfm_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $y, ( isset( $pf_filters_meta['pfm_multiselect'][$y] ) && $pf_filters_meta['pfm_multiselect'][$y] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_relation_%1$s">%2$s</label>', $y, esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ) );
												?>
													
												</th>
												<td class="forminp forminp-select">
													<?php
														$curr_options = '';
														$relation_params = array(
															'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
															'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
														);

														foreach ( $relation_params as $k => $v ) {
															$selected = ( isset( $pf_filters_meta['pfm_relation'][$y] ) && $pf_filters_meta['pfm_relation'][$y] == $k ? ' selected="selected"' : '' );
															$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
														}

														printf( '<select name="pfm_relation[%2$s]" id="pfm_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $y );
													?>
													<span class="description"><?php esc_html_e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													esc_html_e( 'Hide None', 'prdctfltr' );
												?>
												</th>
												<td class="forminp forminp-checkbox">
													<fieldset>
														<legend class="screen-reader-text">
														<?php
															esc_html_e( 'Hide None', 'prdctfltr' );
														?>
														</legend>
														<label for="pfm_none_<?php echo $y; ?>">
														<?php
															printf( '<input name="pfm_none[%1$s]" id="pfm_none_%1$s" type="checkbox" value="yes" %2$s />', $y, ( isset( $pf_filters_meta['pfm_none'][$y] ) && $pf_filters_meta['pfm_none'][$y] == 'yes' ? ' checked="checked"' : '' ) );
															esc_html_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
														?>
														</label>
													</fieldset>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_term_customization_%1$s">%2$s</label>', $y, esc_html__( 'Style Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_term_customization[%1$s]" id="pfm_term_customization_%1$s" class="pf_term_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, ( isset( $pf_filters_meta['pfm_term_customization'][$y] ) ? $pf_filters_meta['pfm_term_customization'][$y] : '' ) );
													?>
													<span class="description"><?php esc_html_e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
											<tr valign="top">
												<th scope="row" class="titledesc">
												<?php
													printf( '<label for="pfm_filter_customization_%1$s">%2$s</label>', $y, esc_html__( 'Terms Customization Key', 'prdctfltr' ) );
												?>
												</th>
												<td class="forminp forminp-text">
													<?php
														printf( '<input name="pfm_filter_customization[%1$s]" id="pfm_filter_customization_%1$s" class="pf_filter_customization" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $y, ( isset( $pf_filters_meta['pfm_filter_customization'][$y] ) ? $pf_filters_meta['pfm_filter_customization'][$y] : '' ) );
													?>
													<span class="description"><?php esc_html_e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
												</td>
											</tr>
										</tbody>
									</table>
								</div>
							</div>
						<?php
							$y++;
						}
						else if ( !in_array( $v, array( 'advanced', 'range', 'meta' ) ) ) {
							if ( substr( $v, 0, 3 ) == 'pa_' && !taxonomy_exists( $v ) ) {
								continue;
							}
						?>
							<div class="pf_element" data-filter="<?php echo $v; ?>">
								<span><?php echo $pf_filters[$v]; ?></span>
								<a href="#" class="prdctfltr_c_delete"><i class="prdctfltr-delete"></i></a>
								<a href="#" class="prdctfltr_c_move"><i class="prdctfltr-move"></i></a>
								<a href="#" class="prdctfltr_c_toggle"><i class="prdctfltr-down"></i></a>
								<div class="pf_options_holder"></div>
							</div>
						<?php
						}
					}
				?>
				</div>

				<p class="form-field prdctfltr_hidden">
					<select name="wc_settings_prdctfltr_active_filters[]" id="wc_settings_prdctfltr_active_filters" class="hidden" multiple="multiple">
					<?php
						foreach ( $pf_filters_selected as $v ) {
							if ( $v == 'advanced' ) {
							?>
								<option value="<?php echo $v; ?>" selected="selected"><?php esc_html_e( 'Advanced Filter', 'prdctfltr' ); ?></option>
							<?php
							}
							else if ( $v == 'range' ) {
							?>
									<option value="<?php echo $v; ?>" selected="selected"><?php esc_html_e( 'Range Filter', 'prdctfltr' ); ?></option>
							<?php
							}
							else if ( $v == 'meta' ) {
							?>
									<option value="<?php echo $v; ?>" selected="selected"><?php esc_html_e( 'Meta Filter', 'prdctfltr' ); ?></option>
							<?php
							}
							else {
								if ( substr( $v, 0, 3 ) == 'pa_' && !taxonomy_exists( $v ) ) {
									continue;
								}
							?>
								<option value="<?php echo $v; ?>" selected="selected"><?php echo $pf_filters[$v]; ?></option>
							<?php
							}
						}
					?>
					</select>
				</p>

			</td>
		</tr><?php
		}

		public static function prdctfltr_add_settings_tab( $settings_tabs ) {
			$settings_tabs['settings_products_filter'] = esc_html__( 'Product Filter', 'prdctfltr' );
			return $settings_tabs;
		}

		public static function prdctfltr_settings_tab() {
			$name = 'prdctfltr_wc_default';
			$name = ( $language = self::prdctfltr_wpml_language() ) === false ? $name : $name . '_' . $language;

			WC_Prdctfltr_Options::set_preset( $name );
			//WC_Prdctfltr_Options::set_preset( 'prdctfltr_wc_default' );
			woocommerce_admin_fields( self::prdctfltr_get_settings( 'get' ) );
		}

		public static function prdctfltr_update_settings() {

			woocommerce_update_options( self::prdctfltr_get_settings( 'update' ) );

		}

		public static function prdctfltr_get_settings( $action = 'get' ) {

			$attribute_taxonomies = wc_get_attribute_taxonomies();

			$product_taxonomies = get_object_taxonomies( 'product' );

			$ready_tax = array();
			foreach( $product_taxonomies as $product_tax ) {
				if ( $product_tax == 'product_type' ) {
					continue;
				}
				$tax = get_taxonomy( $product_tax );

				$ready_tax[$product_tax] = $tax->labels->name;
			}

			$ready_tax_clean = $ready_tax;
			if ( isset( $ready_tax_clean['product_visibility'] ) ) {
				unset( $ready_tax_clean['product_visibility'] );
			}

			$curr_filters = array(
				'sort' => esc_html__( 'Sort By', 'prdctfltr' ),
				'price' => esc_html__( 'By Price', 'prdctfltr' ),
				'vendor' => esc_html__( 'Vendor', 'prdctfltr' ),
				'instock' => esc_html__( 'In Stock Filter', 'prdctfltr' ),
				'per_page' => esc_html__( 'Products Per Page', 'prdctfltr' ),
				'search' => esc_html__( 'Search Filter', 'prdctfltr' )
			);

			if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'no' ) {
				unset( $curr_filters['char'] );
			}

			$curr_attr = array();
			if ( $attribute_taxonomies ) {
				foreach ( $attribute_taxonomies as $tax ) {
					$curr_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;
					$curr_attr['pa_' . $tax->attribute_name] = ucfirst( $curr_label );
				}
			}

			$pf_filters = $curr_filters + $curr_attr;

			foreach( $ready_tax as $k => $v ) {
				if ( !array_key_exists( $k, $pf_filters ) ) {
					$pf_filters[$k] = $v;
				}
			}

			$vendors = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );
			$ready_vendors = array();

			foreach ( $vendors as $vendor ) {
				$ready_vendors[$vendor->ID] = $vendor->display_name;
			}

			if ( $action == 'get' ) {
		?>
		<ul class="prdctfltr-menu <?php echo ( isset( $_GET['section'] ) ? ' wcpf_mode_' . $_GET['section'] : ' wcpf_mode_presets' ); ?>">
		<?php
			$sections = array(
				'presets' => array(
					'title' => esc_html__( 'Default Filter and Filter Presets', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-filter"></i>'
				),
				'overrides' => array(
					'title' => esc_html__( 'Filter Overrides and Restrictions', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-overrides"></i>'
				),
				'advanced' => array(
					'title' => esc_html__( 'Installation and Advanced Options', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-terms"></i>'
				),
				'analytics' =>array(
					'title' => esc_html__( 'Filter Analytics', 'prdctfltr' ),
					'icon' => '<i class="prdctfltr-analytics"></i>'
				)
			);

			$i=0;
			foreach ( $sections as $k => $v ) {

				$curr_class = ( isset( $_GET['section'] ) && $_GET['section'] == $k ) || ( !isset( $_GET['section'] ) && $k == 'presets' ) ? true : false;

				printf( '<li class="%5$s"><a href="%1$s"%3$s><span class="pf-icon">%4$s</span> <span class="pf-title">%2$s</span></a></li>', admin_url( 'admin.php?page=wc-settings&tab=settings_products_filter&section=' . $k ), $v['title'], $curr_class !== false ? ' class="current"' : '', $v['icon'], $curr_class !== false ? ' active' : '' );

				$i++;
			}
			printf( '<li class="pink"><a href="%1$s" target="_blank"><span class="pf-icon"><i class="prdctfltr-check"></i></span> <span class="pf-title">%2$s</span></a></li>', 'http://codecanyon.net/user/dzeriho/portfolio?ref=dzeriho', esc_html__( 'More plugins for WooCommerce?', 'prdctfltr' ) );
			printf( '<li class="pink"><a href="%1$s" target="_blank"><span class="pf-icon"><i class="prdctfltr-check"></i></span><span class="pf-title">%2$s</span></a></li>', 'http://themeforest.net/user/dzeriho/portfolio?ref=dzeriho', esc_html__( 'Get ShopKit Theme for WooCommerce!', 'prdctfltr' ) );
			if ( isset( $_GET['section'] ) && $_GET['section'] == 'advanced' ) {
				printf( '<li class="red"><a href="%1$s" id="pf_reset_options" target="_blank"><span class="pf-icon"><i class="prdctfltr-delete"></i></span> <span class="pf-title">%2$s</span></a></li>', '#', esc_html__( 'Delete/Reset Product Filter Options!', 'prdctfltr' ) );
			}
		?>
		</ul>
		<?php
			}
			if ( isset( $_GET['section'] ) && $_GET['section'] == 'analytics' ) {

				$settings = array();

				$settings = array(
					'section_analytics_title' => array(
						'name' => esc_html__( 'Product Filter Analytics', 'prdctfltr' ),
						'type' => 'title',
						'desc' => esc_html__( 'Follow your customers filtering data. BETA VERSION Please note, this section and its features will be extended in the future updates. Need help? Check this link', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/" target="_blank">' . esc_html__( 'Product Filter - Documentation and Guide', 'prdctfltr' ) . '</a>',
					),
					'prdctfltr_use_analytics' => array(
						'name' => esc_html__( 'Use Filtering Analytics', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use filtering analytics.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_analytics',
						'default' => 'no'
					),
					'prdctfltr_filtering_analytics' => array(
						'name' => esc_html__( 'Filtering Analytics', 'prdctfltr' ),
						'type' => 'pf_filter_analytics',
						'desc' => esc_html__( 'See what your customers are searching for.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_filtering_analytics',
						'default' => 'no'
					),
					'section_analytics_end' => array(
						'type' => 'sectionend'
					)
				);

			}
			else if ( isset( $_GET['section'] ) && $_GET['section'] == 'advanced' ) {
				$curr_theme = wp_get_theme();
				$more_overrides_std = ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'yes' ? array( 'product_cat', 'product_tag', 'characteristics' ) : array( 'product_cat', 'product_tag' ) );

				$settings = array(
					'section_general_title' => array(
						'name' => esc_html__( 'Shop/Product Archives Installation Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => esc_html__( 'General installation settings for Shop/Product Archive pages. Need help? Check this link', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/" target="_blank">' . esc_html__( 'Product Filter - Documentation and Guide', 'prdctfltr' ) . '</a>' . '<div id="prdctfltr_installation"></div>'
					),
					'prdctfltr_enable' => array(
						'name' => esc_html__( 'Shop/Product Archives Installation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select method for installing the Product Filter template in your Shop and Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable',
						'options' => array(
							'yes' => esc_html__( 'Override Default WooCommerce Templates', 'prdctfltr' ),
							'no' => esc_html__( 'Use Widget', 'prdctfltr' ),
							'action' => esc_html__( 'Custom Action', 'prdctfltr' )
						),
						'default' => 'yes',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_enable_action' => array(
						'name' => esc_html__( 'Custom Action', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom products action to initialize Product Filter template. Use actions from your theme archive-product.php template. Please enter action name in following format action_name:priority. E.G. woocommerce_before_shop_loop:40 woocommerce_archive_description:50', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable_action',
						'default' => 'woocommerce_archive_description:50',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_default_templates' => array(
						'name' => esc_html__( 'Disable WooCommerce Templates', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide orderby.php and result-count.php templates. Works with the option below (Select Templates).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_default_templates',
						'default' => 'no'
					),
					'prdctfltr_enable_overrides' => array(
						'name' => esc_html__( 'Select Templates', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select WooCommerce templates to use. Use CTRL+Click to select multiple templates or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_enable_overrides',
						'options' => array(
							'orderby' => esc_html__( 'Order By', 'prdctfltr' ),
							'result-count' => esc_html__( 'Result Count', 'prdctfltr' )
						),
						'default' => array( 'orderby', 'result-count' ),
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_general_end' => array(
						'type' => 'sectionend'
					),


					'section_advanced_title' => array(
						'name' => esc_html__( 'General - Advanced Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => esc_html__( 'These settings will have an effect on both AJAX and NON-AJAX installations.', 'prdctfltr' )
					),
					'prdctfltr_custom_tax' => array(
						'name' => esc_html__( 'Use Characteristics', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use Characteristics taxonomy.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax',
						'default' => 'no',
					),
/*					'prdctfltr_instock' => array(
						'name' => esc_html__( 'Show In Stock Products by Default', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to show the In Stock products by default.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock',
						'default' => 'no'
					),*/
					'prdctfltr_use_variable_images' => array(
						'name' => esc_html__( 'Switch Variable Images', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to switch variable images when attribute filters are used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_variable_images',
						'default' => 'no'
					),
					'prdctfltr_hideempty' => array(
						'name' => esc_html__( 'Hide Empty Taxonomy Terms', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this setting to hide empty taxonomy terms in options.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_hideempty',
						'default' => 'no',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_clearall' => array(
						'name' => esc_html__( 'Clear All Action', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Selected filters will not be cleared.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_clearall',
						'options' => $pf_filters,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
/*					'prdctfltr_taxonomy_relation' => array(
						'name' => esc_html__( 'Filter Taxonomy Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Set filter relation for product taxonomies.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_taxonomy_relation',
						'options' => array(
							'AND' => esc_html__( 'AND', 'prdctfltr' ),
							'OR' => esc_html__( 'OR', 'prdctfltr' )
						),
						'default' => 'AND',
						'css' => 'width:300px;margin-right:12px;'
					),*/
					'prdctfltr_more_overrides' => array(
						'name' => esc_html__( 'Supported Filter Overrides', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select taxonomies that will support the Product Filter Overrides.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_more_overrides',
						'options' => $ready_tax_clean,
						'default' => $more_overrides_std,
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_disable_scripts' => array(
						'name' => esc_html__( 'Disable JavaScript Libraries', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select JavaScript libraries to disable. Use CTRL+Click to select multiple libraries or deselect all. Selected libraries will not be loaded.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_scripts',
						'options' => array(
							'ionrange' => esc_html__( 'Ion Range Slider', 'prdctfltr' ),
							'isotope' => esc_html__( 'Isotope', 'prdctfltr' ),
							'mcustomscroll' => esc_html__( 'Malihu jQuery Scrollbar', 'prdctfltr' )
						),
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_advanced_end' => array(
						'type' => 'sectionend'
					),

					'section_ajax_title' => array(
						'name' => esc_html__( 'AJAX Shop/Product Archives - Advanced Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => esc_html__( 'Set AJAX settings for Shop/Product Archive pages. AJAX shortcodes also need correct jQuery selectors to work properly. If your theme is good skip any jQuery Selector setting. For more information check the ', 'prdctfltr' ) . '<a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/#installation-sdgs" target="_blank">' . esc_html__( 'Specific Theme Installations', 'prdctfltr' ) . '</a>'
					),
					'prdctfltr_use_ajax' => array(
						'name' => esc_html__( 'Enable AJAX', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to use AJAX in Shop/Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_use_ajax',
						'default' => 'no'
					),
					'prdctfltr_ajax_class' => array(
						'name' => esc_html__( 'Products Wrapper jQuery Selector (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom wrapper jQuery selector if the default setting is not working. Default selector: .products', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_category_class' => array(
						'name' => esc_html__( 'Category jQuery Selector (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom category jQuery selector if the default setting is not working. Default selector: .product-category', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_category_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_product_class' => array(
						'name' => esc_html__( 'Product jQuery Selector (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom products jQuery selector if the default setting is not working. Default selector: .type-product', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_product_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_pagination_class' => array(
						'name' => esc_html__( 'Pagination jQuery Selector (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom pagination jQuery selector if the default setting is not working. Default selector: .woocommerce-pagination', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_pagination_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_pagination' => array(
						'name' => esc_html__( 'Custom Pagination Function (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Function for displaying pagination. Default function: woocommerce_pagination', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_pagination',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_count_class' => array(
						'name' => esc_html__( 'Result Count jQuery Selector (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom result count jQuery selector if the default setting is not working. Default selector: .woocommerce-result-count', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_count_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_orderby_class' => array(
						'name' => esc_html__( 'Order By jQuery Selector (AJAX)', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter custom order by jQuery selector if the default setting is not working. Default selector: .woocommerce-ordering', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_orderby_class',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_columns' => array(
						'name' => esc_html__( 'Product Columns (AJAX)', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'This option should be used if theme returns wrong product columns after AJAX.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_columns',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_rows' => array(
						'name' => esc_html__( 'Product Rows (AJAX)', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'This option should be used if theme returns wrong products per page after AJAX.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_rows',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_pagination_type' => array(
						'name' => esc_html__( 'Pagination Type (AJAX)', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select pagination template to use in Shop/Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_pagination_type',
						'options' => array(
							'default' => esc_html__( 'Default (In Theme)', 'prdctfltr' ),
							'prdctfltr-pagination-default' => esc_html__( 'Pagination (ONLY AJAX)', 'prdctfltr' ),
							'prdctfltr-pagination-load-more' => esc_html__( 'Load More (ONLY AJAX)', 'prdctfltr' ),
							'prdctfltr-pagination-infinite-load' => esc_html__( 'Infinite Load (ONLY AJAX)', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_product_animation' => array(
						'name' => esc_html__( 'Product Animation (AJAX)', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select animation when showing new products.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_product_animation',
						'options' => array(
							'none' => esc_html__( 'No Animation', 'prdctfltr' ),
							'default' => esc_html__( 'Fade Each Product', 'prdctfltr' ),
							'slide' => esc_html__( 'Slide Each Product', 'prdctfltr' ),
							'random' => esc_html__( 'Fade Random Products', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_after_ajax_scroll' => array(
						'name' => esc_html__( 'Scroll Animation (AJAX)', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select type of scroll animation after filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_after_ajax_scroll',
						'options' => array(
							'none' => esc_html__( 'No Animation', 'prdctfltr' ),
							'filter' => esc_html__( 'Scroll to Filter', 'prdctfltr' ),
							'products' => esc_html__( 'Scroll to Products', 'prdctfltr' ),
							'top' => esc_html__( 'Scroll to Top', 'prdctfltr' )
						),
						'default' => 'products',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_permalink' => array(
						'name' => esc_html__( 'Permalink Structure (AJAX)', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Set permalink structure when AJAX is used on Shop/Product Archive pages.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_permalink',
						'options' => array(
							'no' => esc_html__( 'Use Product Filter Redirects (Default)', 'prdctfltr' ),
							'query' => esc_html__( 'Only Add Query Parameters', 'prdctfltr' ),
							'yes' => esc_html__( 'Disable URL Changes', 'prdctfltr' )
						),
						'default' => 'no',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_templates' => array(
						'name' => esc_html__( 'Disable Template Changes (AJAX)', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select templates that will not change after AJAX (if supported).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_templates',
						'options' => array(
							'title' => esc_html__( 'Title', 'prdctfltr' ),
							'desc' => esc_html__( 'Description', 'prdctfltr' ),
							'result' => esc_html__( 'Result Count', 'prdctfltr' ),
							'orderby' => esc_html__( 'Order By', 'prdctfltr' )
						),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_failsafe' => array(
						'name' => esc_html__( 'Failsafe Check (AJAX)', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select elemets to check before calling AJAX function in Shop/Product Archives.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_failsafe',
						'options' => array(
							'wrapper' => esc_html__( 'Products Wrapper', 'prdctfltr' ),
							'product' => esc_html__( 'Products Found', 'prdctfltr' ),
							'pagination' => esc_html__( 'Pagination', 'prdctfltr' )
						),
						'default' => array( 'wrapper' ),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_ajax_js' => array(
						'name' => esc_html__( 'jQuery and JS Refresh (AJAX)', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter jQuery or JavaScript code to execute after AJAX.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_ajax_js',
						'default' => '',
						'css' 		=> 'max-width:600px;margin-top:12px;min-height:150px;',
					),
					'section_ajax_end' => array(
						'type' => 'sectionend'
					),

					'section_noneajax_title' => array(
						'name' => esc_html__( 'NON-AJAX Shop/Product Archives - Advanced Settings', 'prdctfltr' ),
						'type' => 'title',
						'desc' => esc_html__( 'Setup advanced options for Shop/Product Archives when AJAX is disabled.', 'prdctfltr' )
					),
					'prdctfltr_force_redirects' => array(
						'name' => esc_html__( 'Permalink Structure', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Set your permalinks structure.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_redirects',
						'options' => array(
							'no' => esc_html__( 'Use Product Filter Redirects (Default)', 'prdctfltr' ),
							'yes' => esc_html__( 'Use .htaccess and native WP redirects (Advanced)', 'prdctfltr' )
						),
						'default' => 'no',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_remove_single_redirect' => array(
						'name' => esc_html__( 'Single Product Redirect', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Uncheck to enable single product page redirect when only one product is found. This option is related to WooCommerce filter woocommerce_redirect_single_search_result and only works when searching for products in non-AJAX mode.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_remove_single_redirect',
						'default' => 'yes'
					),
					'prdctfltr_force_product' => array(
						'name' => esc_html__( 'Force Post Type Variable (Advanced)', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Option will add the ?post_type=product parameter when filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_product',
						'default' => 'no'
					),
					'prdctfltr_force_action' => array(
						'name' => esc_html__( 'Force Stay on Permalink (Advanced)', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to force filtering on same permalink (URL).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_force_action',
						'default' => 'no'
					),
					'section_noneajax_end' => array(
						'type' => 'sectionend'
					),
				);
			}
			else if ( ( isset( $_GET['section'] ) && $_GET['section'] == 'presets' ) || !isset( $_GET['section'] ) ) {
				$curr_presets_ready = array();
				if ( $action == 'get' ) {

					$GLOBALS['hide_save_button'] = true;

					printf( '<h3>%1$s</h3><p>%2$s</p><p>', esc_html__( 'Filter Preset Manager', 'prdctfltr' ), esc_html__( 'Manage your Product Filter presets. Save, load and manage unlimited filter presets! Need help? Check this link', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/" target="_blank">' . esc_html__( 'Product Filter - Documentation and Guide', 'prdctfltr' ) . '</a>' );
			?>
							<select id="prdctfltr_filter_presets">
								<option value="default"><?php esc_html_e( 'Default', 'wcwar' ); ?></option>
								<?php
									$curr_presets = get_option( 'prdctfltr_templates', array() );
									$curr_presets_ready = array( 'default' => esc_html__( 'None', 'prdctfltr' ) );

									if ( !empty( $curr_presets) ) {
										foreach ( $curr_presets as $k => $v ) {
											$curr_presets_ready[$k] = $k;
									?>
											<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
										}
									}
								?>
							</select>
			<?php
					printf( '<a href="#" id="prdctfltr_save" class="button-primary">%1$s</a> <a href="#" id="prdctfltr_load" class="button-primary">%2$s</a> <a href="#" id="prdctfltr_delete" class="button-primary">%3$s</a> <a href="#" id="prdctfltr_reset_default" class="button-primary">%4$s</a> <a href="#" id="prdctfltr_save_default" class="button-primary">%5$s</a></p>', esc_html__( 'Save as preset', 'prdctfltr' ), esc_html__( 'Load', 'prdctfltr' ), esc_html__( 'Delete', 'prdctfltr' ), esc_html__( 'Load default preset', 'prdctfltr' ), esc_html__( 'Save as default preset', 'prdctfltr' ) );
					/*printf( '<p>%1$s: <span id="prdctfltr_slug_container">[prdctfltr_sc_products]</span></p>', esc_html__( 'To use selected preset in a products shortcode on Custom Pages use the following syntax.', 'prdctfltr' ) );*/

				}

				$settings = array(
					'section_mobile_title' => array(
						'name'     => esc_html__( 'Mobile Preset', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup mobile/handheld devices preset.', 'prdctfltr' ) . '<span class="wcpff_mobile"></span>'
					),
					'prdctfltr_mobile_preset' => array(
						'name' => esc_html__( 'Select Mobile Preset', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select mobile preset that will be shown on lower screen resolutions.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_mobile_preset',
						'options' => $curr_presets_ready,
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_mobile_resolution' => array(
						'name' => esc_html__( 'Set Mobile Resolution', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Set screen resolution that wil trigger the mobile preset.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_mobile_resolution',
						'default' => 640,
						'custom_attributes' => array(
							'min' 	=> 640,
							'max' 	=> 1024,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'section_mobile_end' => array(
						'type' => 'sectionend'
					),
					'section_adoptive_title' => array(
						'name'     => esc_html__( 'Adoptive Filtering', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup adpotive filtering.', 'prdctfltr' ) . '<span class="wcpff_adoptive"></span>'
					),
					'prdctfltr_adoptive' => array(
						'name' => esc_html__( 'Enable/Disable Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable the adoptive filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive',
						'default' => 'no',
					),
					'prdctfltr_adoptive_mode' => array(
						'name' => esc_html__( 'Select Adoptive Filtering Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select mode to use with the filtered terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_mode',
						'options' => array(
							'always' => esc_html__( 'Always Active', 'prdctfltr' ),
							'permalink' => esc_html__( 'Active on Permalinks and Filters', 'prdctfltr' ),
							'filter' => esc_html__( 'Active on Filters', 'prdctfltr' )
						),
						'default' => 'permalink',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_style' => array(
						'name' => esc_html__( 'Select Adoptive Filtering Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select style to use with the filtered terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_style',
						'options' => array(
							'pf_adptv_default' => esc_html__( 'Hide Terms', 'prdctfltr' ),
							'pf_adptv_unclick' => esc_html__( 'Disabled and Unclickable', 'prdctfltr' ),
							'pf_adptv_click' => esc_html__( 'Disabled but Clickable', 'prdctfltr' )
						),
						'default' => 'pf_adptv_default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_depend' => array(
						'name' => esc_html__( 'Select Adoptive Filtering Dependency', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Adoptive filters can depend only on ceratin taxonomy.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_depend',
						'options' => array_merge( array( '' => esc_html__( 'Default', 'prdctfltr' ) ), $ready_tax_clean ),
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_show_counts_mode' => array(
						'name' => esc_html__( 'Adoptive Term Products Count Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select how to display the product count when adoptive filtering is used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_counts_mode',
						'options' => array(
							'default' => esc_html__( 'Filtered Count / Total', 'prdctfltr' ),
							'count' => esc_html__( 'Filtered Count', 'prdctfltr' ),
							'total' => esc_html__( 'Total', 'prdctfltr' )
						),
						'default' => 'default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_adoptive_reorder' => array(
						'name' => esc_html__( 'Reorder Adoptive Terms', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to reorder adoptive terms to front.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_adoptive_reorder',
						'default' => 'yes',
					),
					'section_adoptive_end' => array(
						'type' => 'sectionend'
					),
					'section_basic_title' => array(
						'name'     => esc_html__( 'General Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter basic settings and appearance.', 'prdctfltr' ) . '<span class="wcpff_basic"></span>'
					),
					'prdctfltr_always_visible' => array(
						'name' => esc_html__( 'Always Visible', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'This option will make Product Filter visible without the slide up/down animation at all times.', 'prdctfltr' ) . ' <em>' . esc_html__( '(Does not work with the Arrow presets as these presets are absolutely positioned and the widget version)', 'prdctfltr' ) . '</em>',
						'id'   => 'wc_settings_prdctfltr_always_visible',
						'default' => 'no',
					),
					'prdctfltr_click_filter' => array(
						'name' => esc_html__( 'Instant Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to disable the filter button and use instant product filtering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_click_filter',
						'default' => 'no',
					),
					'prdctfltr_show_counts' => array(
						'name' => esc_html__( 'Show Term Products Count', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to show products count with the terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_counts',
						'default' => 'no',
					),
					'prdctfltr_show_search' => array(
						'name' => esc_html__( 'Show Term Search Fields', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to show search fields on supported filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_show_search',
						'default' => 'no',
					),
					'prdctfltr_selection_area' => array(
						'name' => esc_html__( 'Selected Terms Area', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Set where to show selected terms. Use CTRL+Click to select multiple areas.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_selection_area',
						'options' => array(
							'topbar' => esc_html__( 'Top Bar', 'prdctfltr' ),
							'collector' => esc_html__( 'Collector', 'prdctfltr' ),
							'intitle' => esc_html__( 'In Filter Titles', 'prdctfltr' ),
							'aftertitle' => esc_html__( 'After Filter Titles', 'prdctfltr' )
						),
						'default' => array( 'topbar' ),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_collector' => array(
						'name' => esc_html__( 'Selected Terms Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Collector and After Filter Titles support styles.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_collector',
						'options' => array(
							'flat' => esc_html__( 'Flat', 'prdctfltr' ),
							'border' => esc_html__( 'Border', 'prdctfltr' )
						),
						'default' => 'off',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_selected_reorder' => array(
						'name' => esc_html__( 'Reorder Selected Terms', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to reorder selected terms to front.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_selected_reorder',
						'default' => 'no',
					),
					'prdctfltr_tabbed_selection' => array(
						'name' => esc_html__( 'Stepped Filter Selection', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable stepped selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tabbed_selection',
						'default' => 'no',
					),
					'prdctfltr_disable_bar' => array(
						'name' => esc_html__( 'Disable Top Bar', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide the Product Filter top bar. This option will also make the filter always visible.', 'prdctfltr' ) . ' <em>' . esc_html__( '(Does not work with the Arrow presets as these presets are absolutely positioned and the widget version)', 'prdctfltr' ) . '</em>',
						'id'   => 'wc_settings_prdctfltr_disable_bar',
						'default' => 'no',
					),
					'prdctfltr_disable_sale' => array(
						'name' => esc_html__( 'Disable Sale Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide the Product Filter sale button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_sale',
						'default' => 'no',
					),
					'prdctfltr_disable_instock' => array(
						'name' => esc_html__( 'Disable In Stock Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide the Product Filter in stock button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_instock',
						'default' => 'no',
					),
					'prdctfltr_disable_reset' => array(
						'name' => esc_html__( 'Disable Clear All Button', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide the Clear All button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_reset',
						'default' => 'no',
					),
					'prdctfltr_custom_action' => array(
						'name' => esc_html__( 'Override Filter Form Action', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Advanced users can override filter form action. Please check documentation for more details.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_action',
						'default' => '',
						'css' 		=> 'width:300px;margin-right:12px;',
					),
					'prdctfltr_noproducts' => array(
						'name' => esc_html__( 'Override No Products Action', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Input HTML/Shortcode to override the default action when no products are found. Default action means that random products will be shown when there are no products within the filter query.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_noproducts',
						'default' => '',
						'css' 		=> 'max-width:600px;margin-top:12px;min-height:150px;',
					),
					'section_basic_end' => array(
						'type' => 'sectionend'
					),
					'section_style_title' => array(
						'name'     => esc_html__( 'Filter Style', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter style settings.', 'prdctfltr' ) . '<span class="wcpff_style"></span>'
					),
					'prdctfltr_style_preset' => array(
						'name' => esc_html__( 'Select Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select style.', 'prdctfltr' ) . ' ' . esc_html__( 'This option does not work with the widget version.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_preset',
						'options' => array(
							'pf_default' => esc_html__( 'Default', 'prdctfltr' ),
							'pf_default_inline' => esc_html__( 'Default Inline', 'prdctfltr' ),
							'pf_arrow' => esc_html__( 'Pop Up', 'prdctfltr' ),
							'pf_arrow_inline' => esc_html__( 'Pop Up Inline', 'prdctfltr' ),
							'pf_select' => esc_html__( 'Select Boxes', 'prdctfltr' ),
							'pf_sidebar' => esc_html__( 'Fixed Sidebar Left', 'prdctfltr' ),
							'pf_sidebar_right' => esc_html__( 'Fixed Sidebar Right', 'prdctfltr' ),
							'pf_sidebar_css' => esc_html__( 'Fixed Sidebar Left With Overlay', 'prdctfltr' ),
							'pf_sidebar_css_right' => esc_html__( 'Fixed Sidebar Right With Overlay', 'prdctfltr' ),
							'pf_fullscreen' => esc_html__( 'Full Screen Overlay', 'prdctfltr' ),
						),
						'default' => 'pf_default',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_style_mode' => array(
						'name' => esc_html__( 'Select Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select mode to use with the filter..', 'prdctfltr' ) . ' ' . esc_html__( 'This option does not work with the widget version.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_mode',
						'options' => array(
							'pf_mod_row' => esc_html__( 'One Row', 'prdctfltr' ),
							'pf_mod_multirow' => esc_html__( 'Multiple Rows', 'prdctfltr' ),
							'pf_mod_masonry' => esc_html__( 'Masonry Filters', 'prdctfltr' )
						),
						'default' => 'pf_mod_multirow',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_max_columns' => array(
						'name' => esc_html__( 'Max Columns', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'This option sets the number of columns for the filter. This option does not work with the widget version or the fixed sidebar layouts.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_max_columns',
						'default' => 3,
						'custom_attributes' => array(
							'min' 	=> 1,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_limit_max_height' => array(
						'name' => esc_html__( 'Limit Max Height', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to limit the Max Height of for the filters.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_limit_max_height',
						'default' => 'no',
					),
					'prdctfltr_max_height' => array(
						'name' => esc_html__( 'Max Height', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Set the Max Height value.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_max_height',
						'default' => 150,
						'custom_attributes' => array(
							'min' 	=> 100,
							'max' 	=> 300,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_custom_scrollbar' => array(
						'name' => esc_html__( 'Use Custom Scroll Bars', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to override default browser scroll bars with javascrips scrollbars in Max Height mode.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_scrollbar',
						'default' => 'no',
					),
					'prdctfltr_style_checkboxes' => array(
						'name' => esc_html__( 'Select Checkbox Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select style for the term checkboxes.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_checkboxes',
						'options' => array(
							'prdctfltr_bold' => esc_html__( 'Hide', 'prdctfltr' ),
							'prdctfltr_round' => esc_html__( 'Round', 'prdctfltr' ),
							'prdctfltr_square' => esc_html__( 'Square', 'prdctfltr' ),
							'prdctfltr_checkbox' => esc_html__( 'Checkbox', 'prdctfltr' ),
							'prdctfltr_system' => esc_html__( 'System Checkboxes', 'prdctfltr' )
						),
						'default' => 'pf_round',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_style_hierarchy' => array(
						'name' => esc_html__( 'Select Hierarchy Style', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select style for hierarchy terms.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_style_hierarchy',
						'options' => array(
							'prdctfltr_hierarchy_hide' => esc_html__( 'Hide', 'prdctfltr' ),
							'prdctfltr_hierarchy_circle' => esc_html__( 'Circle', 'prdctfltr' ),
							'prdctfltr_hierarchy_filled' => esc_html__( 'Circle Solid', 'prdctfltr' ),
							'prdctfltr_hierarchy_lined' => esc_html__( 'Lined', 'prdctfltr' ),
							'prdctfltr_hierarchy_arrow' => esc_html__( 'Arrows', 'prdctfltr' )
						),
						'default' => 'prdctfltr_hierarchy_circle',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_button_position' => array(
						'name' => esc_html__( 'Select Filter Buttons Position', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select position of the filter buttons, top or bottom.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_button_position',
						'options' => array(
							'bottom' => esc_html__( 'Bottom', 'prdctfltr' ),
							'top' => esc_html__( 'Top', 'prdctfltr' ),
							'both' => esc_html__( 'Both', 'prdctfltr' )
						),
						'default' => 'bottom',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_icon' => array(
						'name' => esc_html__( 'Override Filter Icon', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter icon class to override the default Product Filter icon. Use icon class e.g. prdctfltr-filter or FontAwesome fa fa-shopping-cart or any other.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_icon',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_title' => array(
						'name' => esc_html__( 'Override Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override default filter heading (Filter Products).', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_submit' => array(
						'name' => esc_html__( 'Override Filter Button Text', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override Filter selected, the default filter submit button text.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_submit',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_loader' => array(
						'name' => esc_html__( 'Select Spinner Animation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select Spinner Animation.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_loader',
						'options' => array(
							'css-spinner-full' => sprintf( esc_html__( 'Spinner CSS %s - Fullscreen Overlay', 'prdctfltr' ), '1' ),
							'css-spinner-full-01' => sprintf( esc_html__( 'Spinner CSS %s - Fullscreen Overlay', 'prdctfltr' ), '2' ),
							'css-spinner-full-02' => sprintf( esc_html__( 'Spinner CSS %s - Fullscreen Overlay', 'prdctfltr' ), '3' ),
							'css-spinner-full-03' => sprintf( esc_html__( 'Spinner CSS %s - Fullscreen Overlay', 'prdctfltr' ), '4' ),
							'css-spinner-full-04' => sprintf( esc_html__( 'Spinner CSS %s - Fullscreen Overlay', 'prdctfltr' ), '5' ),
							'css-spinner-full-05' => sprintf( esc_html__( 'Spinner CSS %s - Fullscreen Overlay', 'prdctfltr' ), '6' ),
							'css-spinner' => sprintf( esc_html__( 'Spinner CSS %s - In Title', 'prdctfltr' ), '1' ),
							'css-spinner-01' => sprintf( esc_html__( 'Spinner CSS %s - In Title', 'prdctfltr' ), '2' ),
							'css-spinner-02' => sprintf( esc_html__( 'Spinner CSS %s - In Title', 'prdctfltr' ), '3' ),
							'css-spinner-03' => sprintf( esc_html__( 'Spinner CSS %s - In Title', 'prdctfltr' ), '4' ),
							'css-spinner-04' => sprintf( esc_html__( 'Spinner CSS %s - In Title', 'prdctfltr' ), '5' ),
							'css-spinner-05' => sprintf( esc_html__( 'Spinner CSS %s - In Title', 'prdctfltr' ), '6' ),
							'none' => esc_html__( 'None', 'prdctfltr' ),
						),
						'default' => 'css-spinner-full-01',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_style_end' => array(
						'type' => 'sectionend'
					),
					'section_title' => array(
						'name'     => esc_html__( 'General Settings', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup style and general settings. Need help? Check this link', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/" target="_blank">' . esc_html__( 'Product Filter - Documentation and Guide', 'prdctfltr' ) . '</a>'
					),
					'prdctfltr_filters' => array(
						'name' => esc_html__( 'Select Filters', 'prdctfltr' ),
						'type' => 'pf_filter',
						'desc' => esc_html__( 'Select Filters.', 'prdctfltr' )
					),
					'section_end' => array(
						'type' => 'sectionend'
					),

					'section_perpage_filter_title' => array(
						'name'     => esc_html__( 'Products Per Page', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_per_page"></span>',
					),
					'prdctfltr_perpage_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_perpage_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_perpage_label' => array(
						'name' => esc_html__( 'Override Products Per Page Filter Label', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter label for the products per page filter.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_label',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_perpage_range' => array(
						'name' => esc_html__( 'Per Page Filter Initial', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Initial products per page value.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_range',
						'default' => 20,
						'custom_attributes' => array(
							'min' 	=> 3,
							'max' 	=> 999,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_perpage_range_limit' => array(
						'name' => esc_html__( 'Per Page Filter Values', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Number of product per page values.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_range_limit',
						'default' => 5,
						'custom_attributes' => array(
							'min' 	=> 2,
							'max' 	=> 20,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_perpage_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'prdctfltr_perpage_filter_customization' => array(
						'name' => esc_html__( 'Terms Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_perpage_filter_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_filter_customization'
					),
					'section_perpage_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_vendor_filter_title' => array(
						'name'     => esc_html__( 'Vendor', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_vendor"></span>'
					),
					'prdctfltr_vendor_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_vendor_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_vendor_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_vendor_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_vendor' => array(
						'name' => esc_html__( 'Select Vendors', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_vendor',
						'options' => $ready_vendors,
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_vendor_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_vendor_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_vendor_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_instock_filter_title' => array(
						'name'     => esc_html__( 'In Stock', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_instock"></span>'
					),
					'prdctfltr_instock_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_instock_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_instock_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_instock_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_instock_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_orderby_filter_title' => array(
						'name'     => esc_html__( 'Sort By', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_sort"></span>'
					),
					'prdctfltr_orderby_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_orderby_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_orderby' => array(
						'name' => esc_html__( 'Select Terms', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_orderby',
						'options' => array(
								'menu_order'    => esc_html__( 'Default', 'prdctfltr' ),
								'comment_count' => esc_html__( 'Review Count', 'prdctfltr' ),
								'popularity'    => esc_html__( 'Popularity', 'prdctfltr' ),
								'rating'        => esc_html__( 'Average rating', 'prdctfltr' ),
								'date'          => esc_html__( 'Newness', 'prdctfltr' ),
								'price'         => esc_html__( 'Price: low to high', 'prdctfltr' ),
								'price-desc'    => esc_html__( 'Price: high to low', 'prdctfltr' ),
								'rand'          => esc_html__( 'Random Products', 'prdctfltr' ),
								'title'         => esc_html__( 'Product Name', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_orderby_none' => array(
						'name' => esc_html__( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_none',
						'default' => 'no',
					),
					'prdctfltr_orderby_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_orderby_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_orderby_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_search_filter_title' => array(
						'name'     => esc_html__( 'Search', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_search"></span>'
					),
					'prdctfltr_search_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_search_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_search_placeholder' => array(
						'name' => esc_html__( 'Override Search Filter Placeholder', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Enter title for the search filter placeholder.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_search_placeholder',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_search_filter_end' => array(
						'type' => 'sectionend'
					),

					'section_price_filter_title' => array(
						'name'     => esc_html__( 'By Price', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_price"></span>'
					),
					'prdctfltr_price_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_price_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_price_range' => array(
						'name' => esc_html__( 'Price Range Filter Initial Price', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Initial price for the filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range',
						'default' => 100,
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_range_add' => array(
						'name' => esc_html__( 'Price Range Filter Price Add', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Price to add.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range_add',
						'default' => 100,
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_range_limit' => array(
						'name' => esc_html__( 'Price Range Filter Intervals', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Number of price intervals to use. E.G. You have set the initial price to 99.9, and the add price is set to 100, you will achieve filtering like 0-99.9, 99.9-199.9, 199.9- 299.9 for the number of times as set in the price intervals setting.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_range_limit',
						'default' => 6,
						'custom_attributes' => array(
							'min' 	=> 2,
							'max' 	=> 20,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_price_none' => array(
						'name' => esc_html__( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_none',
						'default' => 'no',
					),
					'prdctfltr_price_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'prdctfltr_price_filter_customization' => array(
						'name' => esc_html__( 'Terms Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_price_filter_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_filter_customization'
					),
					'section_price_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_cat_filter_title' => array(
						'name'     => esc_html__( 'Category Filter', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_cat"></span>'
					),

/*					'prdctfltr_cat_termsearch' => array(
						'name' => esc_html__( 'Terms Search Field Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select prefered search field mode type.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_termsearch',
						'options' => array(
								'show' => esc_html__( 'Show', 'prdctfltr' ),
								'disable' => esc_html__( 'Hide', 'prdctfltr' ),
								'search' => esc_html__( 'Special Search Filter Mode', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
*/
					'prdctfltr_cat_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_description',
						'default' => '',
						'css' => 'width:100%;max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_cats' => array(
						'name' => esc_html__( 'Select Terms', 'prdctfltr' ),
						'type' => 'pf_taxonomy',
						'desc' => esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_cats',
						'options' => 'product_cat',
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_orderby' => array(
						'name' => esc_html__( 'Terms Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select terms ordering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_orderby',
						'options' => array(
								'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
								'id' => esc_html__( 'ID', 'prdctfltr' ),
								'name' => esc_html__( 'Name', 'prdctfltr' ),
								'number' => esc_html__( 'Number', 'prdctfltr' ),
								'slug' => esc_html__( 'Slug', 'prdctfltr' ),
								'count' => esc_html__( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_order' => array(
						'name' => esc_html__( 'Term Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_order',
						'options' => array(
								'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
								'DESC' => esc_html__( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_limit' => array(
						'name' => esc_html__( 'Show More Button', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_cat_hierarchy' => array(
						'name' => esc_html__( 'Use Hierarchy', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable hierarchy.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_hierarchy',
						'default' => 'no',
					),
					'prdctfltr_cat_mode' => array(
						'name' => esc_html__( 'Hierarchy Filtering Mode', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select filter hierarchy mode.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_mode',
						'options' => array(
								'showall' => esc_html__( 'Show all', 'prdctfltr' ),
								'drill' => esc_html__( 'Show same level only (Drill filter)', 'prdctfltr' ),
								'drillback' => esc_html__( 'Drill filter with removable parent', 'prdctfltr' ),
								'subonly' => esc_html__( 'Show only child terms, selected parents will not be removable', 'prdctfltr' ),
								'subonlyback' => esc_html__( 'Show only child terms with removable parent', 'prdctfltr' )
							),
						'default' => 'showall',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_hierarchy_mode' => array(
						'name' => esc_html__( 'Expand Parents', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to expand parent terms on load.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_hierarchy_mode',
						'default' => 'no',
					),
					'prdctfltr_cat_multi' => array(
						'name' => esc_html__( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable multi term selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_multi',
						'default' => 'no',
					),
					'prdctfltr_cat_relation' => array(
						'name' => esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_relation',
						'options' => array(
								'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_cat_selection' => array(
						'name' => esc_html__( 'Selection Change Reset', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to reset other filters when this one is used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_selection',
						'default' => 'no',
					),
					'prdctfltr_cat_adoptive' => array(
						'name' => esc_html__( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_adoptive',
						'default' => 'no',
					),
					'prdctfltr_cat_none' => array(
						'name' => esc_html__( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_none',
						'default' => 'no',
					),
					'prdctfltr_cat_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_cat_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_cat_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_tag_filter_title' => array(
						'name'     => esc_html__( 'Tag Filter', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_tag"></span>'
					),
					'prdctfltr_tag_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_tags' => array(
						'name' => esc_html__( 'Select Terms', 'prdctfltr' ),
						'type' => 'pf_taxonomy',
						'desc' => esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_tags',
						'options' => 'product_tag',
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_orderby' => array(
						'name' => esc_html__( 'Terms Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select terms ordering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_orderby',
						'options' => array(
								'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
								'id' => esc_html__( 'ID', 'prdctfltr' ),
								'name' => esc_html__( 'Name', 'prdctfltr' ),
								'number' => esc_html__( 'Number', 'prdctfltr' ),
								'slug' => esc_html__( 'Slug', 'prdctfltr' ),
								'count' => esc_html__( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_order' => array(
						'name' => esc_html__( 'Tags Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_order',
						'options' => array(
								'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
								'DESC' => esc_html__( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_limit' => array(
						'name' => esc_html__( 'Show More Button', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_tag_multi' => array(
						'name' => esc_html__( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable multi term selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_multi',
						'default' => 'no',
					),
					'prdctfltr_tag_relation' => array(
						'name' => esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_relation',
						'options' => array(
								'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_tag_selection' => array(
						'name' => esc_html__( 'Selection Change Reset', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to reset other filters when this one is used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_selection',
						'default' => 'no',
					),
					'prdctfltr_tag_adoptive' => array(
						'name' => esc_html__( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_adoptive',
						'default' => 'no',
					),
					'prdctfltr_tag_none' => array(
						'name' => esc_html__( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_none',
						'default' => 'no',
					),
					'prdctfltr_tag_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_tag_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_tag_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_char_filter_title' => array(
						'name'     => esc_html__( 'Characteristics Filter', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_char"></span>'
					),
					'prdctfltr_custom_tax_title' => array(
						'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_title',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_description' => array(
						'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
						'type' => 'textarea',
						'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_description',
						'default' => '',
						'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
					),
					'prdctfltr_include_chars' => array(
						'name' => esc_html__( 'Select Terms', 'prdctfltr' ),
						'type' => 'pf_taxonomy',
						'desc' => esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_include_chars',
						'options' => 'characteristics',
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_orderby' => array(
						'name' => esc_html__( 'Terms Order By', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select terms ordering.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_orderby',
						'options' => array(
								'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
								'id' => esc_html__( 'ID', 'prdctfltr' ),
								'name' => esc_html__( 'Name', 'prdctfltr' ),
								'number' => esc_html__( 'Number', 'prdctfltr' ),
								'slug' => esc_html__( 'Slug', 'prdctfltr' ),
								'count' => esc_html__( 'Count', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_order' => array(
						'name' => esc_html__( 'Characteristics Order', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select ascending or descending order.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_order',
						'options' => array(
								'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
								'DESC' => esc_html__( 'DESC', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_custom_tax_limit' => array(
						'name' => esc_html__( 'Show More Button', 'prdctfltr' ),
						'type' => 'number',
						'desc' => esc_html__( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_limit',
						'default' => 0,
						'custom_attributes' => array(
							'min' 	=> 0,
							'max' 	=> 100,
							'step' 	=> 1
						),
						'css' => 'width:100px;margin-right:12px;'
					),
					'prdctfltr_chars_multi' => array(
						'name' => esc_html__( 'Use Multi Select', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable multi term selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_multi',
						'default' => 'no',
					),
					'prdctfltr_custom_tax_relation' => array(
						'name' => esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_custom_tax_relation',
						'options' => array(
								'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
								'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
							),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_chars_selection' => array(
						'name' => esc_html__( 'Selection Change Reset', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to reset other filters when this one is used.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_selection',
						'default' => 'no',
					),
					'prdctfltr_chars_adoptive' => array(
						'name' => esc_html__( 'Use Adoptive Filtering', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_adoptive',
						'default' => 'no',
					),
					'prdctfltr_chars_none' => array(
						'name' => esc_html__( 'Hide None', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_none',
						'default' => 'no',
					),
					'prdctfltr_chars_term_customization' => array(
						'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
						'type' => 'text',
						'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_chars_term_customization',
						'default' => '',
						'css' => 'width:300px;margin-right:12px;',
						'class' => 'pf_term_customization'
					),
					'section_char_filter_end' => array(
						'type' => 'sectionend'
					),

				);

				if ( $attribute_taxonomies) {
					$settings = $settings + array (
						
					);
					foreach ( $attribute_taxonomies as $tax) {

						$catalog_attrs = get_terms( 'pa_' . $tax->attribute_name, array( 'hide_empty' => 0 ) );
						$curr_attrs = array();
						if ( !empty( $catalog_attrs ) && !is_wp_error( $catalog_attrs ) ){
							foreach ( $catalog_attrs as $term ) {
								$curr_attrs[self::prdctfltr_utf8_decode( $term->slug )] = $term->name;
							}
						}

						$tax->attribute_label = !empty( $tax->attribute_label ) ? $tax->attribute_label : $tax->attribute_name;

						$settings = $settings + array(
							'section_pa_' . $tax->attribute_name.'_title' => array(
								'name'     => ucfirst( $tax->attribute_label ) . ' ' . esc_html__( 'Filter', 'prdctfltr' ),
								'type'     => 'title',
								'desc'     => esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a><span class="wcpfs_pa_' . $tax->attribute_name . '"></span>'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_title' => array(
								'name' => esc_html__( 'Filter Title', 'prdctfltr' ),
								'type' => 'text',
								'desc' => esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_title',
								'default' => '',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_description' => array(
								'name' => esc_html__( 'Filter Description', 'prdctfltr' ),
								'type' => 'textarea',
								'desc' => esc_html__( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_description',
								'default' => '',
								'css' => 'max-width:600px;margin-top:12px;min-height:90px;',
							),
							'prdctfltr_include_pa_' . $tax->attribute_name => array(
								'name' => esc_html__( 'Select Terms', 'prdctfltr' ),
								'type' => 'pf_taxonomy',
								'desc' => esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_include_pa_' . $tax->attribute_name,
								'options' => 'pa_' . $tax->attribute_name,
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name => array(
								'name' => esc_html__( 'Appearance', 'prdctfltr' ),
								'type' => 'select',
								'desc' => esc_html__( 'Select style preset to use with the current attribute.', 'prdctfltr' ) . '<em class="pf_deprecated"></em>',
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name,
								'options' => array(
									'pf_attr_text' => esc_html__( 'Text', 'prdctfltr' ),
									'pf_attr_imgtext' => esc_html__( 'Thumbnails with text', 'prdctfltr' ),
									'pf_attr_img' => esc_html__( 'Thumbnails only', 'prdctfltr' )
								),
								'default' => 'pf_attr_text',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_orderby' => array(
								'name' => esc_html__( 'Terms Order By', 'prdctfltr' ),
								'type' => 'select',
								'desc' => esc_html__( 'Select terms ordering.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_orderby',
								'options' => array(
									'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
									'id' => esc_html__( 'ID', 'prdctfltr' ),
									'name' => esc_html__( 'Name', 'prdctfltr' ),
									'number' => esc_html__( 'Number', 'prdctfltr' ),
									'slug' => esc_html__( 'Slug', 'prdctfltr' ),
									'count' => esc_html__( 'Count', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_order' => array(
								'name' => esc_html__( 'Terms Order', 'prdctfltr' ),
								'type' => 'select',
								'desc' => esc_html__( 'Select ascending or descending order.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_order',
								'options' => array(
										'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
										'DESC' => esc_html__( 'DESC', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_limit' => array(
								'name' => esc_html__( 'Show More Button', 'prdctfltr' ),
								'type' => 'number',
								'desc' => esc_html__( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_limit',
								'default' => 0,
								'custom_attributes' => array(
									'min' 	=> 0,
									'max' 	=> 100,
									'step' 	=> 1
								),
								'css' => 'width:100px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_hierarchy' => array(
								'name' => esc_html__( 'Use Hierarchy', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => esc_html__( 'Check this option to enable terms hierarchy.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_hierarchy',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_mode' => array(
								'name' => esc_html__( 'Hierarchy Filtering Mode', 'prdctfltr' ),
								'type' => 'select',
								'desc' => esc_html__( 'Select filter hierarchy mode.', 'prdctfltr' ),
								'id'   => 'wc_settings_pa_' . $tax->attribute_name.'_mode',
								'options' => array(
										'showall' => esc_html__( 'Show all', 'prdctfltr' ),
										'drill' => esc_html__( 'Show same level only (Drill filter)', 'prdctfltr' ),
										'drillback' => esc_html__( 'Drill filter with removable parent', 'prdctfltr' ),
										'subonly' => esc_html__( 'Show only child terms, selected parents will not be removable', 'prdctfltr' ),
										'subonlyback' => esc_html__( 'Show only child terms with removable parent', 'prdctfltr' )
									),
								'default' => 'showall',
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_hierarchy_mode' => array(
								'name' => esc_html__( 'Expand Parents', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => esc_html__( ' Check this option to expand parent terms on load.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_hierarchy_mode',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_multi' => array(
								'name' => esc_html__( 'Use Multi Select', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => esc_html__( 'Check this option to enable multi term selection.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_multi',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_relation' => array(
								'name' => esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ),
								'type' => 'select',
								'desc' => esc_html__( 'Select term relation when multiple terms are selected.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_relation',
								'options' => array(
										'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
										'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
									),
								'default' => array(),
								'css' => 'width:300px;margin-right:12px;'
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_selection' => array(
								'name' => esc_html__( 'Selection Change Reset', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => esc_html__( 'Check this option to reset other filters when this one is used.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_selection',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_adoptive' => array(
								'name' => esc_html__( 'Use Adoptive Filtering', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => esc_html__( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_adoptive',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_none' => array(
								'name' => esc_html__( 'Hide None', 'prdctfltr' ),
								'type' => 'checkbox',
								'desc' => esc_html__( 'Check this option to hide none in the current filter.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_none',
								'default' => 'no',
							),
							'prdctfltr_pa_' . $tax->attribute_name.'_term_customization' => array(
								'name' => esc_html__( 'Style Customization Key', 'prdctfltr' ),
								'type' => 'text',
								'desc' => esc_html__( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ),
								'id'   => 'wc_settings_prdctfltr_pa_' . $tax->attribute_name.'_term_customization',
								'default' => '',
								'css' => 'width:300px;margin-right:12px;',
								'class' => 'pf_term_customization'
							),
							'section_pa_' . $tax->attribute_name.'_end' => array(
								'type' => 'sectionend'
							),

						);
					}
				}

			}
			else if ( isset( $_GET['section'] ) && $_GET['section'] == 'overrides' ) {

				$catalog_categories = get_terms( 'product_cat', array( 'hide_empty' => 0 ) );
				$curr_cats = array();
				if ( !empty( $catalog_categories ) && !is_wp_error( $catalog_categories ) ){
					foreach ( $catalog_categories as $term ) {
						$curr_cats[self::prdctfltr_utf8_decode( $term->slug )] = $term->name;
					}
				}

				$curr_presets = get_option( 'prdctfltr_templates', array() );
				$curr_theme = wp_get_theme();

				$curr_presets_set = array();
				foreach( $curr_presets as $q => $w ) {
					$curr_presets_set[$q] = $q;
				}

				$settings = array(
					'section_overrides_filter_title' => array(
						'name'     => esc_html__( 'Shop and Archives Appearance', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Setup Shop and Product Archives appearance.', 'prdctfltr' )
					),
					'prdctfltr_shop_disable' => array(
						'name' => esc_html__( 'Enable/Disable Shop Page Product Filter', 'prdctfltr' ),
						'type' => 'checkbox',
						'desc' => esc_html__( 'Check this option in order to disable the Product Filter on Shop page. This option can be useful for themes with custom Shop pages, if checked the default WooCommerce or', 'prdctfltr' ) . ' ' . $curr_theme->get( 'Name' ) . ' ' . esc_html__( 'filter template will be overriden only on product archives that support it.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_shop_disable',
						'default' => 'no'
					),
					'prdctfltr_shop_page_override' => array(
						'name' => esc_html__( 'Shop Page Override', 'prdctfltr' ),
						'type' => 'select',
						'desc' => esc_html__( 'Override default template on the shop page.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_shop_page_override',
						'options' => array( '' => esc_html__( 'Default' ) ) + $curr_presets_set,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'prdctfltr_disable_display' => array(
						'name' => esc_html__( 'Shop/Category Display Types And Product Filter', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'Select what display types will not show the Product Filter.  Use CTRL+Click to select multiple display types or deselect all.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_disable_display',
						'options' => array(
							'subcategories' => esc_html__( 'Show Categories', 'prdctfltr' ),
							'both' => esc_html__( 'Show Both', 'prdctfltr' )
						),
						'default' => array(),
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_overrides_filter_end' => array(
						'type' => 'sectionend'
					),
					'section_restrictions_title' => array(
						'name'     => esc_html__( 'Product Filter Restrictions', 'prdctfltr' ),
						'type'     => 'title',
						'desc'     => esc_html__( 'Limit filter appearance with Product Filter restrictions.', 'prdctfltr' ) . '<span class="wcpfs_instock"></span>'
					),
					'prdctfltr_showon_product_cat' => array(
						'name' => esc_html__( 'Show Filter Only On Categories', 'prdctfltr' ),
						'type' => 'multiselect',
						'desc' => esc_html__( 'To show filter only on certain categories in Shop and Product Archives, select them from the list.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ),
						'id'   => 'wc_settings_prdctfltr_showon_product_cat',
						'options' => $curr_cats,
						'default' => '',
						'css' => 'width:300px;margin-right:12px;'
					),
					'section_restrictions_end' => array(
						'type' => 'sectionend'
					)
				);
				if ( $action == 'get' ) {
					$curr_or_settings = get_option( 'prdctfltr_overrides', array() );
				?>
					<h3><?php esc_html_e( 'Product Filter Overrides and Restrictions', 'prdctfltr' ); ?></h3>
					<p><?php echo esc_html__( 'Override default filter preset on assigned taxonomy terms. Need help? Check this link', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/" target="_blank">' . esc_html__( 'Product Filter - Documentation and Guide', 'prdctfltr' ) . '</a>'; ?></p>
				<?php

					$curr_overrides = get_option( 'wc_settings_prdctfltr_more_overrides', false );

					if ( $curr_overrides === false ) {
						$curr_overrides = array( 'product_cat', 'product_tag' );
						if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'yes' ) {
							$curr_overrides[] = 'characteristics';
						}
					}

					foreach ( $curr_overrides as $n ) {
						$get_dropdown = wp_dropdown_categories( array( 'hide_empty' => 0, 'echo' => 0, 'hierarchical' => ( is_taxonomy_hierarchical( $n ) ? 1 : 0 ), 'class' => 'prdctfltr_or_select', 'depth' => 0, 'taxonomy' => $n, 'hide_if_empty' => true, 'value_field' => 'slug', ) );
						if ( empty( $get_dropdown ) ) {
							continue;
						}
				?>
						<h3>
						<?php
							$curr_tax = get_taxonomy( $n );
							echo esc_html__( 'Product', 'prdctfltr' ) . ' ' . $curr_tax->labels->name . ' ' . esc_html__( 'Overrides', 'prdctfltr' );
						?>
						</h3>
						<p class="prdctfltr_override_wrap" data-taxonomy="<?php echo $n; ?>">
						<?php
							if ( isset( $curr_or_settings[$n] ) ) {
								foreach ( $curr_or_settings[$n] as $k => $v ) {
							?>
							<span class="prdctfltr_override"><input type="checkbox" class="pf_override_checkbox" /><?php echo '<span class="slug">' . $k . '</span>'; ?><span class="preset"><?php echo $v; ?></span><a href="#" class="prdctfltr_or_remove"></a></span>
							<?php
								}
							}
						?>
						</p>
						<p class="prdctfltr_override_controls_wrap" data-taxonomy="<?php echo $n; ?>">
							<span class="prdctfltr_override_controls">
								<a href="#" class="button prdctfltr_or_remove_selected"><?php esc_html_e( 'Remove Selected Overrides', 'prdctfltr' ); ?></a> <a href="#" class="button prdctfltr_or_remove_all"><?php esc_html_e( 'Remove All Overrides', 'prdctfltr' ); ?></a>
							</span>
						<?php
							echo $get_dropdown;
						?>
							<select class="prdctfltr_filter_presets">
								<option value="default"><?php esc_html_e( 'Default', 'wcwar' ); ?></option>
								<?php
									if ( !empty( $curr_presets) ) {
										foreach ( $curr_presets as $k => $v ) {
									?>
											<option value="<?php echo $k; ?>"><?php echo $k; ?></option>
									<?php
										}
									}
								?>
							</select>
							<a href="#" class="button-primary prdctfltr_or_add"><?php esc_html_e( 'Add Override', 'prdctfltr' ); ?></a>
						</p>
				<?php
					}
				}
			}

			return apply_filters( 'wc_settings_products_filter_settings', $settings );
		}

		public static function prdctfltr_admin_save() {

			$curr_name = ( !isset( $_POST['curr_name'] ) ? 'prdctfltr_wc_default' : $_POST['curr_name'] );
			$curr_slug = ( $curr_name == 'prdctfltr_wc_default' ? 'prdctfltr_wc_default' : 'prdctfltr_wc_template_' . sanitize_title( $curr_name ) );
			$curr_settings = $_POST['curr_settings'];

			$language = self::prdctfltr_wpml_language();
			if ( $language !== false ) {
				$curr_slug .= '_' . $language;
			}

			if ( is_string( $curr_settings ) && substr( $curr_settings, 0, 1 ) == '{' ) {

				if ( substr( $curr_name, 0, 20 ) !== 'prdctfltr_wc_default' ) {

					$curr_data = array();
					$curr_data[$curr_name] = array();

					$curr_presets = get_option( 'prdctfltr_templates', array() );
					if ( !is_array( $curr_presets ) ) {
						$curr_presets = array();
					}

					if ( is_array( $curr_presets ) ) {

						if ( array_key_exists( $curr_name, $curr_presets) ) {
							unset( $curr_presets[$curr_name] );
						}

						$curr_presets = $curr_presets + $curr_data;
						ksort( $curr_presets );

						update_option( 'prdctfltr_templates', $curr_presets, 'no' );

					}

				}

				update_option( $curr_slug, $curr_settings, 'no' );

				die( $curr_slug );
				exit;

			}

			die();
			exit;

		}

		public static function prdctfltr_admin_load() {

			$curr_name = $_POST['curr_name'];

			$language = self::prdctfltr_wpml_language();

			$curr_slug = sanitize_title( $curr_name );

			$curr_presets = get_option( 'prdctfltr_templates', array() );

			if ( is_array( $curr_presets ) ) {
				if ( array_key_exists( $curr_name, $curr_presets ) ) {

					$new_slug = $curr_slug;
					if ( $language !== false ) {
						$new_slug .= '_' . $language;
					}

					$option = get_option( 'prdctfltr_wc_template_' . $new_slug, false );
					if ( $option !== false && is_string( $option ) && substr( $option, 0, 1 ) == '{' ) {
						die( stripslashes( $option ) );
						exit;
					}

					if ( $curr_slug !== $new_slug ) {
						$option = get_option( 'prdctfltr_wc_template_' . $curr_slug, false );
						if ( $option !== false && is_string( $option ) && substr( $option, 0, 1 ) == '{' ) {
							die( stripslashes( $option ) );
							exit;
						}
					}

					if ( isset( $curr_presets[$curr_name] ) && is_string( $curr_presets[$curr_name] ) && substr( $curr_presets, 0, 1 ) == '{' ) {
						die( stripslashes( $curr_presets[$curr_name] ) );
						exit;
					}
				}
			}

			die();
			exit;

		}

		public static function prdctfltr_admin_delete() {

			$curr_name = $_POST['curr_name'];

			$language = self::prdctfltr_wpml_language();

			$curr_slug = sanitize_title( $curr_name );
			if ( $language !== false ) {
				$curr_slug .= '_' . $language;
			}

			$curr_presets = get_option( 'prdctfltr_templates', array() );
			if ( is_array( $curr_presets ) ) {
				if ( array_key_exists( $curr_name, $curr_presets ) ) {
					
					delete_option( 'prdctfltr_wc_template_' . $curr_slug );
					if ( $language === false ) {
						unset( $curr_presets[$curr_name] );
						update_option( 'prdctfltr_templates', $curr_presets, 'no' );
					}
				}

				die('1');
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_or_add() {
			$curr_tax = $_POST['curr_tax'];
			$curr_term = $_POST['curr_term'];
			$curr_override = $_POST['curr_override'];

			$curr_overrides = get_option( 'prdctfltr_overrides', array() );

			$curr_data = array(
				$curr_tax => array( $curr_term => $curr_override )
			);

			if ( isset( $curr_overrides) && is_array( $curr_overrides) ) {
				if ( isset( $curr_overrides[$curr_tax] ) && isset( $curr_overrides[$curr_tax][$curr_term] )) {
					unset( $curr_overrides[$curr_tax][$curr_term] );
				}
				$curr_overrides = array_merge_recursive( $curr_overrides, $curr_data);
				update_option( 'prdctfltr_overrides', $curr_overrides, 'no' );
				die( '1' );
				exit;
			}

			die();
			exit;

		}

		public static function prdctfltr_or_remove() {
			$curr_tax = $_POST['curr_tax'];
			$curr_term = $_POST['curr_term'];
			$curr_overrides = get_option( 'prdctfltr_overrides', array() );

			if ( isset( $curr_overrides ) && is_array( $curr_overrides ) ) {
				if ( isset( $curr_overrides[$curr_tax] ) && isset( $curr_overrides[$curr_tax][$curr_term] ) ) {
					unset( $curr_overrides[$curr_tax][$curr_term] );
					update_option( 'prdctfltr_overrides', $curr_overrides, 'no' );
					die( '1' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function prdctfltr_m_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h2><?php esc_html_e( 'Meta Filter', 'prdctfltr' ); ?></h2>
			<p><?php echo esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_title_%1$s">%2$s</label>', $pf_id, esc_html__( 'Filter Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_title[%1$s]" id="pfm_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_title'] ) ? $_POST['pfm_title'] : '' );
							?>
							<span class="description"><?php echo esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_description_%1$s">%2$s</label>', $pf_id, esc_html__( 'Filter Description', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php esc_html_e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfm_description[%1$s]" id="pfm_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfm_description'] ) ? stripslashes( $_POST['pfm_description'] ) : '' ) );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_key_%1$s">%2$s</label>', $pf_id, esc_html__( 'Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_key[%1$s]" id="pfm_key_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_key'] ) ? $_POST['pfm_key'] : '' );
							?>
							<span class="description"><?php echo esc_html__( 'Meta key.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_compare_%1$s">%2$s</label>', $pf_id, esc_html__( 'Compare', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
	
							$meta_compares = array(
								array(
									'value' => '=',
									'label' => '='
								),
								array(
									'value' => '!=',
									'label' => '!='
								),
								array(
									'value' => '>',
									'label' => '>'
								),
								array(
									'value' => '<',
									'label' => '<'
								),
								array(
									'value' => '>=',
									'label' => '>='
								),
								array(
									'value' => '<=',
									'label' => '<='
								),
								array(
									'value' => 'LIKE',
									'label' => 'LIKE'
								),
								array(
									'value' => 'NOT LIKE',
									'label' => 'NOT LIKE'
								),
								array(
									'value' => 'IN',
									'label' => 'IN'
								),
								array(
									'value' => 'NOT IN',
									'label' => 'NOT IN'
								),
								array(
									'value' => 'EXISTS',
									'label' => 'EXISTS'
								),
								array(
									'value' => 'NOT EXISTS',
									'label' => 'NOT EXISTS'
								),
								array(
									'value' => 'BETWEEN',
									'label' => 'BETWEEN'
								),
								array(
									'value' => 'NOT BETWEEN',
									'label' => 'NOT_BETWEEN'
								),
							);
							foreach ( $meta_compares as $k => $v ) {
								$selected = ( isset( $_POST['pfm_compare'] ) && $_POST['pfm_compare'] == $v['value'] ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
							}

							printf( '<select name="pfm_compare[%2$s]" id="pfm_compare_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php esc_html_e( 'Meta compare.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_type_%1$s">%2$s</label>', $pf_id, esc_html__( 'Type', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
	
							$meta_types = array(
								array(
									'value' => 'NUMERIC',
									'label' => 'NUMERIC'
								),
								array(
									'value' => 'BINARY',
									'label' => 'BINARY'
								),
								array(
									'value' => 'CHAR',
									'label' => 'CHAR'
								),
								array(
									'value' => 'DATE',
									'label' => 'DATE'
								),
								array(
									'value' => 'DATETIME',
									'label' => 'DATETIME'
								),
								array(
									'value' => 'DECIMAL',
									'label' => 'DECIMAL'
								),
								array(
									'value' => 'SIGNED',
									'label' => 'SIGNED'
								),
								array(
									'value' => 'TIME',
									'label' => 'TIME'
								),
								array(
									'value' => 'UNSIGNED',
									'label' => 'UNSIGNED'
								)
							);
							foreach ( $meta_types as $k => $v ) {
								$selected = ( isset( $_POST['pfm_type'] ) && $_POST['pfm_type'] == $v['value'] ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $v['value'], $v['label'], $selected );
							}

							printf( '<select name="pfm_type[%2$s]" id="pfm_type_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php esc_html_e( 'Meta type.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_limit_%1$s">%2$s</label>', $pf_id, esc_html__( 'Show More Button', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-number">
							<?php
								printf( '<input name="pfm_limit[%1$s]" id="pfm_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $pf_id, isset( $_POST['pfm_limit'] ) ? $_POST['pfm_limit'] : '' ); ?>
							<span class="description"><?php esc_html_e( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Use Multi Select', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Use Multi Select', 'prdctfltr' );
								?>
								</legend>
								<label for="pfm_multiselect_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfm_multiselect[%1$s]" id="pfm_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfm_multiselect'] ) && $_POST['pfm_multiselect'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_relation_%1$s">%2$s</label>', $pf_id, esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
									'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfm_relation'] ) && $_POST['pfm_relation'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfm_relation[%2$s]" id="pfm_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php esc_html_e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Hide None', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Hide None', 'prdctfltr' );
								?>
								</legend>
								<label for="pfm_none_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfm_none[%1$s]" id="pfm_none_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfm_none'] ) && $_POST['pfm_none'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_term_customization_%1$s">%2$s</label>', $pf_id, esc_html__( 'Style Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_term_customization[%1$s]" id="pfm_term_customization_%1$s" type="text" value="%2$s" class="pf_term_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_term_customization'] ) ? $_POST['pfm_term_customization'] : '' );
							?>
							<span class="description"><?php esc_html_e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfm_filter_customization_%1$s">%2$s</label>', $pf_id, esc_html__( 'Terms Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfm_filter_customization[%1$s]" id="pfm_filter_customization_%1$s" type="text" value="%2$s" class="pf_filter_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfm_filter_customization'] ) ? $_POST['pfm_filter_customization'] : '' );
							?>
							<span class="description"><?php esc_html_e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>

				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die( $html);
			exit;

		}

		public static function prdctfltr_c_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h2><?php esc_html_e( 'Advanced Filter', 'prdctfltr' ); ?></h2>
			<p><?php echo esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_title_%1$s">%2$s</label>', $pf_id, esc_html__( 'Filter Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfa_title[%1$s]" id="pfa_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfa_title'] ) ? $_POST['pfa_title'] : '' );
							?>
							<span class="description"><?php echo esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_description_%1$s">%2$s</label>', $pf_id, esc_html__( 'Filter Description', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php esc_html_e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfa_description[%1$s]" id="pfa_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfa_description'] ) ? stripslashes( $_POST['pfa_description'] ) : '' ) );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							$taxonomies = get_object_taxonomies( 'product', 'object' );
							printf( '<label for="pfa_taxonomy_%1$s">%2$s</label>', $pf_id, esc_html__( 'Select Taxonomy', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								printf( '<select id="pfa_taxonomy_%1$s" name="pfa_taxonomy[%1$s]" class="prdctfltr_adv_select" style="width:300px;margin-right:12px;">', $pf_id) ;
								foreach ( $taxonomies as $k => $v ) {
									if ( in_array( $k, array( 'product_type' ) ) ) {
										continue;
									}
									if ( !isset( $first_tax ) ) {
										$first_tax = $k;
									}
									echo '<option value="' . $k . '"' . ( isset( $_POST['pfa_taxonomy'] ) && $_POST['pfa_taxonomy'] == $k ? ' selected="selected"' : '' ) .'>' . ( substr( $v->name, 0, 3 ) == 'pa_' ? wc_attribute_label( $v->name ) : $v->label ) . '</option>';
								}
								echo '</select>';
							?>
							<span class="description"><?php esc_html_e( 'Select filter product taxonomy.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_include_%1$s">%2$s</label>', $pf_id, esc_html__( 'Select Terms', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-multiselect">
						<?php
							$tax = isset( $_POST['pfa_taxonomy'] ) && taxonomy_exists( $_POST['pfa_taxonomy'] ) ? $_POST['pfa_taxonomy'] : $first_tax;
							if ( !empty( $tax ) ) {

								$name = 'pfa_include[' . $pf_id . '][]';
								$id ='pfa_include_' . $pf_id;
								$option_value =  isset( $_POST['pfa_include'] ) ? $_POST['pfa_include'] : array();
								self::get_dropdown( $tax, $option_value, $name, $id );

							}
							else {
								printf( '<select name="pfa_include[%1$s][]" id="pfa_include_%1$s" multiple="multiple" style="width:300px;margin-right:12px;"></select>', $pf_id );
							}
						?>
							<span class="description"><?php echo esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_style_%1$s">%2$s</label>', $pf_id, esc_html__( 'Appearance', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'pf_attr_text' => esc_html__( 'Text', 'prdctfltr' ),
									'pf_attr_imgtext' => esc_html__( 'Thumbnails with text', 'prdctfltr' ),
									'pf_attr_img' => esc_html__( 'Thumbnails only', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_style'] ) && $_POST['pfa_style'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_style[%2$s]" id="pfa_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php esc_html_e( 'Select style preset to use with the current taxonomy (works only with product attributes).', 'prdctfltr' ); ?><em class="pf_deprecated"></em></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_orderby_%1$s">%2$s</label>', $pf_id, esc_html__( 'Terms Order By', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$orderby_params = array(
									'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
									'id' => esc_html__( 'ID', 'prdctfltr' ),
									'name' => esc_html__( 'Name', 'prdctfltr' ),
									'number' => esc_html__( 'Number', 'prdctfltr' ),
									'slug' => esc_html__( 'Slug', 'prdctfltr' ),
									'count' => esc_html__( 'Count', 'prdctfltr' )
								);

								foreach ( $orderby_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_orderby'] ) && $_POST['pfa_orderby'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_orderby[%2$s]" id="pfa_orderby_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php esc_html_e( 'Select terms ordering.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_order_%1$s">%2$s</label>', $pf_id, esc_html__( 'Terms Order', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$order_params = array(
									'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
									'DESC' => esc_html__( 'DESC', 'prdctfltr' )
								);

								foreach ( $order_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_order'] ) && $_POST['pfa_order'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_order[%2$s]" id="pfa_order_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php esc_html_e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_limit_%1$s">%2$s</label>', $pf_id, esc_html__( 'Show More Button', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-number">
							<?php
								printf( '<input name="pfa_limit[%1$s]" id="pfa_limit_%1$s" type="number" style="width:100px;margin-right:12px;" value="%2$s" class="" placeholder="" min="0" max="100" step="1">', $pf_id, isset( $_POST['pfa_limit'] ) ? $_POST['pfa_limit'] : '' ); ?>
							<span class="description"><?php esc_html_e( 'Limit number of terms to display before the Show More button.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Use Taxonomy Hierarchy', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_hierarchy_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_hierarchy[%1$s]" id="pfa_hierarchy_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_hierarchy'] ) && $_POST['pfa_hierarchy'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to enable hierarchy on current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_mode_%1$s">%2$s</label>', $pf_id, esc_html__( 'Taxonomy Hierarchy Filtering Mode', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'showall' => esc_html__( 'Show all', 'prdctfltr' ),
									'drill' => esc_html__( 'Show same level only (Drill filter)', 'prdctfltr' ),
									'drillback' => esc_html__( 'Drill filter with removable parent', 'prdctfltr' ),
									'subonly' => esc_html__( 'Show only child terms, selected parents will not be removable', 'prdctfltr' ),
									'subonlyback' => esc_html__( 'Show only child terms with removable parent', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_mode'] ) && $_POST['pfa_mode'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_mode[%2$s]" id="pfa_mode_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php esc_html_e( 'Select filter hierarchy mode.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Taxonomy Expand Parents', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Taxonomy Expand Parents', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_hierarchy_mode_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_hierarchy_mode[%1$s]" id="pfa_hierarchy_mode_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_hierarchy_mode'] ) && $_POST['pfa_hierarchy_mode'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( ' Check this option to expand parent terms on load.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Use Multi Select', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Use Multi Select', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_multiselect_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_multiselect[%1$s]" id="pfa_multiselect_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_multiselect'] ) && $_POST['pfa_multiselect'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to enable multi term selection.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_relation_%1$s">%2$s</label>', $pf_id, esc_html__( 'Multi Select Terms Relation', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$curr_options = '';
								$relation_params = array(
									'IN' => esc_html__( 'Filtered products have at least one term (IN)', 'prdctfltr' ),
									'AND' => esc_html__( 'Filtered products have selected terms (AND)', 'prdctfltr' )
								);

								foreach ( $relation_params as $k => $v ) {
									$selected = ( isset( $_POST['pfa_relation'] ) && $_POST['pfa_relation'] == $k ? ' selected="selected"' : '' );
									$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
								}

								printf( '<select name="pfa_relation[%2$s]" id="pfa_relation_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
							?>
							<span class="description"><?php esc_html_e( 'Select term relation when multiple terms are selected.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Selection Change Reset', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Selection Change Reset', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_selection_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_selection[%1$s]" id="pfa_selection_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_selection'] ) && $_POST['pfa_selection'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to reset other filters when this one is used.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_adoptive_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_adoptive[%1$s]" id="pfa_adoptive_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_adoptive'] ) && $_POST['pfa_adoptive'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Hide None', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Hide None', 'prdctfltr' );
								?>
								</legend>
								<label for="pfa_none_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfa_none[%1$s]" id="pfa_none_%1$s" type="checkbox" value="yes" %2$s />', $pf_id, ( isset( $_POST['pfa_none'] ) && $_POST['pfa_none'] == 'yes' ? ' checked="checked"' : '' ) );
									esc_html_e( 'Check this option to hide none in the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfa_term_customization_%1$s">%2$s</label>', $pf_id, esc_html__( 'Style Customization Key', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfa_term_customization[%1$s]" id="pfa_term_customization_%1$s" type="text" value="%2$s" class="pf_term_customization" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfa_term_customization'] ) ? $_POST['pfa_term_customization'] : '' );
							?>
							<span class="description"><?php esc_html_e( 'Once customized, customization key will appear. If you use matching filters in presets just copy and paste this key to get the same customization.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die( $html);
			exit;

		}

		public static function prdctfltr_c_terms() {

			$tax = ( isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '' );

			if ( $tax == '' ) {
				die();
				exit;
			}

			$name = 'pfa_include[%%][]';
			$id = 'pfa_include_%%';
			$option_value = array();

			ob_start();

			self::get_dropdown( $tax, $option_value, $name, $id );

			$dropdown = ob_get_clean();

			die( $dropdown );
			exit;

		}

		public static function prdctfltr_r_fields() {

			$pf_id = ( isset( $_POST['pf_id'] ) ? $_POST['pf_id'] : 0 );

			ob_start();
		?>

			<h2><?php esc_html_e( 'Range Filter', 'prdctfltr' ); ?></h2>
			<p><?php echo esc_html__( 'Setup filter. Check following link for more information.', 'prdctfltr' ) . ' <a href="http://mihajlovicnenad.com/product-filter/documentation-and-full-guide-video/">' . esc_html__( 'Documentation & Knowledge Base', 'prdctfltr' ) . '</a>'; ?></p>
			<table class="form-table">
				<tbody>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_title_%1$s">%2$s</label>', $pf_id, esc_html__( 'Filter Title', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-text">
							<?php
								printf( '<input name="pfr_title[%1$s]" id="pfr_title_%1$s" type="text" value="%2$s" style="width:300px;margin-right:12px;" /></label>', $pf_id, isset( $_POST['pfr_title'] ) ? $_POST['pfr_title'] : '' );
							?>
							<span class="description"><?php echo esc_html__( 'Override filter title.', 'prdctfltr' ) . ' ' . esc_html__( 'If you leave this field empty default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_description_%1$s">%2$s</label>', $pf_id, esc_html__( 'Filter Description', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php esc_html_e( 'Enter description for the current filter. If entered small text will apprear just bellow the filter title.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfr_description[%1$s]" id="pfr_description_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfr_description'] ) ? stripslashes( $_POST['pfr_description'] ) : '' ) );
							?>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_taxonomy_%1$s">%2$s</label>', $pf_id, esc_html__( 'Select Range', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
							<?php
								$taxonomies = get_object_taxonomies( 'product', 'object' );
								printf( '<select name="pfr_taxonomy[%1$s]" id="pfr_taxonomy_%1$s" class="prdctfltr_rng_select"  style="width:300px;margin-right:12px;">', $pf_id );
								echo '<option value="price"' . ( !isset( $_POST['pfr_taxonomy'] ) || $_POST['pfr_taxonomy'] == 'price' ? ' selected="selected"' : '' ) . '>' . esc_html__( 'Price range', 'prdctfltr' ) . '</option>';
								foreach ( $taxonomies as $k => $v ) {
									if ( in_array( $k, array( 'product_type' ) ) ) {
										continue;
									}
									if ( substr( $k, 0, 3 ) == 'pa_' ) {
										$curr_label = wc_attribute_label( $v->name );
										$curr_value = $v->name;
									}
									else {
										$curr_label = $v->label;
										$curr_value = $k;
									}
									echo '<option value="' . $curr_value . '"' . ( isset( $_POST['pfr_taxonomy'] ) && $_POST['pfr_taxonomy'] == $curr_value ? ' selected="selected"' : '' ) .'>' . $curr_label . '</option>';
								}
								echo '</select>';
							?>
							<span class="description"><?php esc_html_e( 'Enter title for the current range filter. If you leave this field blank default will be used.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_include_%1$s">%2$s</label>', $pf_id, esc_html__( 'Select Terms', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-multiselect">
						<?php
							if ( isset( $_POST['pfr_taxonomy'] ) && $_POST['pfr_taxonomy'] !== 'price' ) {

								$tax = isset( $_POST['pfr_taxonomy'] ) ? $_POST['pfr_taxonomy'] : '';

								$name = 'pfr_include_' . $pf_id . '[]';
								$id = 'pfr_include_' . $pf_id;
								$option_value =  isset( $_POST['pfr_include'] ) ? $_POST['pfr_include'] : array();
								self::get_dropdown( $tax, $option_value, $name, $id );

								$add_disabled = '';
							}
							else {

								printf( '<select name="pfr_include[%1$s][]" id="pfr_include_%1$s" multiple="multiple" disabled style="width:300px;margin-right:12px;"></select></label>', $pf_id );
								$add_disabled = ' disabled';

							}
						?>
							<span class="description"><?php echo esc_html__( 'Select terms to include.', 'prdctfltr' ) . ' ' . esc_html__( 'Use CTRL+Click to select terms or clear selection.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_orderby_%1$s">%2$s</label>', $pf_id, esc_html__( 'Terms Order By', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$orderby_params = array(
								'' => esc_html__( 'None (Custom Menu Order)', 'prdctfltr' ),
								'id' => esc_html__( 'ID', 'prdctfltr' ),
								'name' => esc_html__( 'Name', 'prdctfltr' ),
								'number' => esc_html__( 'Number', 'prdctfltr' ),
								'slug' => esc_html__( 'Slug', 'prdctfltr' ),
								'count' => esc_html__( 'Count', 'prdctfltr' )
							);
							foreach ( $orderby_params as $k => $v ) {
								$selected = ( isset( $_POST['pfr_orderby'] ) && $_POST['pfr_orderby'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}
							printf( '<select name="pfr_orderby[%2$s]" id="pfr_orderby_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select></label>', $curr_options, $pf_id, $add_disabled );
						?>
							<span class="description"><?php esc_html_e( 'Select terms ordering.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_order_%1$s">%2$s</label>', $pf_id, esc_html__( 'Terms Order', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$order_params = array(
								'ASC' => esc_html__( 'ASC', 'prdctfltr' ),
								'DESC' => esc_html__( 'DESC', 'prdctfltr' )
							);
							foreach ( $order_params as $k => $v ) {
								$selected = ( isset( $_POST['pfr_order'] ) && $_POST['pfr_order'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}

							printf( '<select name="pfr_order[%2$s]" id="pfr_order_%2$s"%3$s style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id, $add_disabled );
						?>
							<span class="description"><?php esc_html_e( 'Select ascending or descending order.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_style_%1$s">%2$s</label>', $pf_id, esc_html__( 'Select Style', 'prdctfltr' ) );
						?>
							
						</th>
						<td class="forminp forminp-select">
						<?php
							$curr_options = '';
							$catalog_style = array(
								'flat' => esc_html__( 'Flat', 'prdctfltr' ),
								'modern' => esc_html__( 'Modern', 'prdctfltr' ),
								'html5' => esc_html__( 'HTML5', 'prdctfltr' ),
								'white' => esc_html__( 'White', 'prdctfltr' ),
								'thin' => esc_html__( 'Thin', 'prdctfltr' ),
								'knob' => esc_html__( 'Knob', 'prdctfltr' ),
								'metal' => esc_html__( 'Metal', 'prdctfltr' )
							);
							foreach ( $catalog_style as $k => $v ) {
								$selected = ( isset( $_POST['pfr_style'] ) && $_POST['pfr_style'] == $k ? ' selected="selected"' : '' );
								$curr_options .= sprintf( '<option value="%1$s"%3$s>%2$s</option>', $k, $v, $selected );
							}

							printf( '<select name="pfr_style[%2$s]" id="pfr_style_%2$s" style="width:300px;margin-right:12px;">%1$s</select>', $curr_options, $pf_id );
						?>
							<span class="description"><?php esc_html_e( 'Select current range style.', 'prdctfltr' ); ?></span>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Use Grid', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Use Grid', 'prdctfltr' );
								?>
								</legend>
								<label for="pfr_grid_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfr_grid[%2$s]" id="pfr_grid_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $_POST['pfr_grid'] ) && $_POST['pfr_grid'] == 'yes' ? ' checked="checked"' : '' ), $pf_id );
									esc_html_e( 'Check this option to use grid in current range.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
						?>
						</th>
						<td class="forminp forminp-checkbox">
							<fieldset>
								<legend class="screen-reader-text">
								<?php
									esc_html_e( 'Use Adoptive Filtering', 'prdctfltr' );
								?>
								</legend>
								<label for="pfr_adoptive_<?php echo $pf_id; ?>">
								<?php
									printf( '<input name="pfr_adoptive[%2$s]" id="pfr_adoptive_%2$s" type="checkbox" value="yes"%1$s />', ( isset( $_POST['pfr_adoptive'] ) && $_POST['pfr_adoptive'] == 'yes' ? ' checked="checked"' : '' ), $pf_id );
									esc_html_e( 'Check this option to enable adoptive filtering on the current filter.', 'prdctfltr' );
								?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row" class="titledesc">
						<?php
							printf( '<label for="pfr_custom_%1$s">%2$s</label>', $pf_id, esc_html__( 'Custom Settings', 'prdctfltr' ) );
						?>
						</th>
						<td class="forminp forminp-textarea">
							<p style="margin-top:0;"><?php esc_html_e( 'Enter custom settings for the range filter.', 'prdctfltr' ); ?></p>
							<?php
								printf( '<textarea name="pfr_custom[%1$s]" id="pfr_custom_%1$s" type="text" style="max-width:600px;margin-top:12px;min-height:90px;">%2$s</textarea>', $pf_id, ( isset( $_POST['pfr_custom'] ) ? stripslashes( $_POST['pfr_custom'] ) : '' ) );
							?>
						</td>
					</tr>

				</tbody>
			</table>
		<?php

			$html = $pf_id . '%SPLIT%' . ob_get_clean();

			die( $html);
			exit;

		}

		public static function prdctfltr_r_terms() {

			$tax = ( isset( $_POST['taxonomy'] ) ? $_POST['taxonomy'] : '' );

			if ( $tax == '' ) {
				die();
				exit;
			}

			$name = 'pfr_include[%%][]';
			$id = 'pfr_include_%%';
			$option_value = array();

			ob_start();

			self::get_dropdown( $tax, $option_value, $name, $id );

			$dropdown = ob_get_clean();

			die( $dropdown );
			exit;

		}

		public static function set_terms() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$addkey = isset( $_POST['addkey'] ) ? $_POST['addkey'] : '';

			if ( $filter == '' ) {
				die();
				exit;
			}

			if ( $key !== '' ) {
				$get_customization = get_option( $key, '' );
				if ( !empty( $get_customization ) ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array(
					'style' => 'text',
					'settings' => array()
				);
				$key = 'wc_settings_prdctfltr_term_customization_' . uniqid();
			}

			if ( $filter == 'advanced' ) {
				$advanced = isset( $_POST['advanced'] ) ? $_POST['advanced'] : '';

				if ( $filter == '' ) {
					die();
					exit;
				}

			}

			$curr_filter = $filter;

			switch ( $filter ) {

				case 'meta' :
				case 'price' :
				case 'per_page' :
					$baked_filters = self::get_terms( $filter, $customization, $addkey );
				break;
				case 'vendor' :
				case 'sort' :
				case 'instock' :
					$baked_filters = self::get_terms( $filter, $customization, $addkey );
				break;

				default :

					if ( $filter == 'cat' ) {
						$curr_filter = 'product_cat';
					}
					else if ( $filter == 'tag' ) {
						$curr_filter = 'product_tag';
					}
					else if ( $filter == 'char' ) {
						$curr_filter = 'characteristics';
					}
					else if ( $filter == 'advanced' ) {
						$curr_filter = $advanced;
					}
					else if ( substr( $filter, 0, 3) == 'pa_' ) {
						$curr_filter = $filter;
					}
					else {
						$curr_filter = '';
					}

					if ( $curr_filter == '' ) {
						die();
						exit;
					}

					$baked_filters = self::get_terms( $curr_filter, $customization, $addkey );

				break;

			}

			if ( isset( $baked_filters ) ) {

				ob_start();
?>
				<div class="prdctfltr_quickview_terms" data-key="<?php echo $key; ?>"<?php echo $addkey !== '' ? ' data-addkey="' . $addkey . '"' : ''; ?>>
					<span class="prdctfltr_quickview_close"><span class="prdctfltr_quickview_close_button"><?php esc_html_e( 'Click to discard any settings!', 'prdctfltr' ); ?></span></span>
					<div class="prdctfltr_quickview_terms_inner">
						<div class="prdctfltr_quickview_terms_settings">
							<span class="prdctfltr_set_terms" data-taxonomy="<?php echo $curr_filter; ?>"><?php esc_html_e( 'Filter', 'prdctfltr' ); ?> &rarr; <code><?php echo $curr_filter; ?></code></span>
<?php

							$select_style = '<label class="pf_wpml"><span>' . esc_html__( 'Select Style', 'prdctfltr' ) . '</span> <select class="prdctfltr_set_terms_attr_select" name="style">';

							$styles = array(
								'text' => esc_html__( 'Text', 'prdctfltr' ),
								'color' => esc_html__( 'Color', 'prdctfltr' ),
								'image' => esc_html__( 'Thumbnail', 'prdctfltr' ),
								'image-text' => esc_html__( 'Thumbnail and Text', 'prdctfltr' ),
								'html' => esc_html__( 'HTML', 'prdctfltr' ),
								'select' => esc_html__( 'Select Box', 'prdctfltr' )
							);

							foreach ( $styles as $k => $v ) {
								$selected = $customization['style'] == $k ? ' selected="selected"' : '';
								$select_style .= '<option value="' . $k . '" ' . $selected . '>' . $v . '</option>';
							}

							$select_style .= '</select></label>';

							echo $select_style;
?>
							<a href="#" class="button-primary prdctfltr_set_terms_save"><?php esc_html_e( 'Save Customization', 'prdctfltr' ); ?></a>
						</div>
						<div class="prdctfltr_quickview_terms_manager">
							<?php echo $baked_filters; ?>
						</div>
					</div>
				</div>
<?php
				$html = ob_get_clean();
			}

			if ( isset( $html ) ) {
				die( $html );
				exit;
			}

			die();
			exit;

		}

		public static function set_terms_new() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$style = isset( $_POST['style'] ) ? $_POST['style'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$addkey = isset( $_POST['addkey'] ) ? $_POST['addkey'] : '';

			/*$language = self::prdctfltr_wpml_language();*/

			if ( $filter == '' || $style == '' ) {
				die();
				exit;
			}

			if ( $key !== '' ) {
				$get_customization = get_option( $key, '' );
				if ( !empty( $get_customization ) ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array(
					'style' => $style,
					'settings' => array()
				);
			}
			else {
				$customization = array_merge( $customization, array( 'style' => $style ) );
			}

			$html = self::get_terms( $filter, $customization, $addkey );

			die( $html );
			exit;

		}

		public static function get_terms( $filter, $customization, $addkey ) {

			if ( $filter == '' ) {
				return '';
			}

			$catalog_attrs = array();
			$curr_style = $customization['style'];
			$settings = $customization['settings'];

			if ( taxonomy_exists( $filter ) && !in_array( $filter, array( 'price', 'per_page' ) ) ) {
				$catalog_attrs = get_terms( $filter, array( 'hide_empty' => 0 ) );
			}
			else {
				switch ( $filter ) {
					case 'instock' :
						$curr_set = apply_filters( 'prdctfltr_catalog_instock', array(
							'both'    => esc_html__( 'All Products', 'prdctfltr' ),
							'in'      => esc_html__( 'In Stock', 'prdctfltr' ),
							'out'     => esc_html__( 'Out Of Stock', 'prdctfltr' )
						) );
						foreach( $curr_set as $k => $v ) {
							$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
						}
					break;
					case 'sort' :
						$curr_set = apply_filters( 'prdctfltr_catalog_orderby', array(
							''              => apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) ),
							'menu_order'    => esc_html__( 'Default', 'prdctfltr' ),
							'comment_count' => esc_html__( 'Review Count', 'prdctfltr' ),
							'popularity'    => esc_html__( 'Popularity', 'prdctfltr' ),
							'rating'        => esc_html__( 'Average rating', 'prdctfltr' ),
							'date'          => esc_html__( 'Newness', 'prdctfltr' ),
							'price'         => esc_html__( 'Price: low to high', 'prdctfltr' ),
							'price-desc'    => esc_html__( 'Price: high to low', 'prdctfltr' ),
							'rand'          => esc_html__( 'Random Products', 'prdctfltr' ),
							'title'         => esc_html__( 'Product Name', 'prdctfltr' )
						) );
						foreach( $curr_set as $k => $v ) {
							$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
						}
					break;
					case 'price' :
						$filter_customization = self::get_filter_customization( 'price', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $k => $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $k, 'name' => $v );
							}
						}
						else {

							$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', 100 );
							$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', 100 );
							$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', 6 );

							if ( get_option( 'wc_settings_prdctfltr_price_none', 'no' ) == 'no' ) {
								$catalog_ready_price = array(
									'-' => apply_filters( 'prdctfltr_none_text', esc_html__( 'None', 'prdctfltr' ) )
								);
							}

							for ( $i = 0; $i < $curr_price_limit; $i++) {

								if ( $i == 0 ) {
									$min_price = 0;
									$max_price = $curr_price_set;
								}
								else {
									$min_price = $curr_price_set+( $i-1)*$curr_price_add;
									$max_price = $curr_price_set+$i*$curr_price_add;
								}

								$slug = $min_price . '-' . ( ( $i+1) == $curr_price_limit ? '' : $max_price );
								$name = wc_price( $min_price ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . wc_price( $max_price ) );

								$catalog_attrs[] = (object) array( 'slug' => $slug, 'name' => $name );

							}
						}
					break;
					case 'per_page' :
						$filter_customization = self::get_filter_customization( 'per_page', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $v['value'], 'name' => $v['text'] );
							}
						}
						else {
							$curr_perpage_set = get_option( 'wc_settings_prdctfltr_perpage_range', 20 );
							$curr_perpage_limit = get_option( 'wc_settings_prdctfltr_perpage_range_limit', 5 );

							$curr_perpage = array();

							for ( $i = 1; $i <= $curr_perpage_limit; $i++) {

								$slug = $curr_perpage_set*$i;
								$name = $curr_perpage_set*$i . ' ' . ( get_option( 'wc_settings_prdctfltr_perpage_label', '' ) == '' ? esc_html__( 'Products', 'prdctfltr' ) : get_option( 'wc_settings_prdctfltr_perpage_label', '' ) );

								$catalog_attrs[] = (object) array( 'slug' => $slug, 'name' => $name );

							}
						}
					break;
					case 'meta' :
						$filter_customization = self::get_filter_customization( 'meta', $addkey );

						if ( !empty( $filter_customization ) && isset( $filter_customization['settings'] ) && is_array( $filter_customization['settings'] ) ) {
							foreach( $filter_customization['settings'] as $v ) {
								$catalog_attrs[] = (object) array( 'slug' => $v['value'], 'name' => $v['text'] );
							}
						}
						else {
							$catalog_attrs[] = (object) array( 'slug' => '', 'name' => '' );
						}

					break;
					case 'vendor' :
						$vendors = get_users( array( 'fields' => array( 'ID', 'display_name' ) ) );

						foreach ( $vendors as $vendor ) {
							$catalog_attrs[] = (object) array( 'slug' => $vendor->ID, 'name' => $vendor->display_name );
						}
					break;
					default :
						$catalog_attrs = array();
					break;
				}
			}

			if ( !empty( $catalog_attrs ) ) {

				ob_start();

				switch ( $curr_style ) {

					case 'text' :

						?>
							<div class="prdctfltr_st_term_style">
								<span class="prdctfltr_st_option">
									<em><?php esc_html_e( 'Type', 'prdctfltr' ); ?></em>
									<select name="type">
								<?php
									$styles = array(
										'border' => esc_html__( 'Border', 'prdctfltr' ),
										'background' => esc_html__( 'Background', 'prdctfltr' ),
										'round' => esc_html__( 'Round', 'prdctfltr' )
									);
									$selected = isset( $settings['type'] ) ? $settings['type'] : 'border';

									$c=0;
									foreach ( $styles as $k => $v ) {
										
								?>
										<option value="<?php echo $k; ?>"<?php echo $selected == $k ? ' selected="selected"' : ''; ?>><?php echo $v; ?></option>
								<?php
										$c++;
									}
								?>
									</select>
								</span>
								<span class="prdctfltr_st_option">
									<em><?php esc_html_e( 'Normal', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="normal" value="<?php echo isset( $settings['normal'] ) ? $settings['normal'] : '#bbbbbb'; ?>" />
								</span>
								<span class="prdctfltr_st_option">
									<em><?php esc_html_e( 'Active', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="active" value="<?php echo isset( $settings['active'] ) ? $settings['active'] : '#333333'; ?>" />
								</span>
								<span class="prdctfltr_st_option">
									<em><?php esc_html_e( 'Disabled', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="disabled" value="<?php echo isset( $settings['disabled'] ) ? $settings['disabled'] : '#eeeeee'; ?>"/>
								</span>

							</div>
						<?php

							foreach ( $catalog_attrs as $term ) {

							?>
								<div class="prdctfltr_st_term prdctfltr_style_text" data-term="<?php echo $term->slug; ?>">
									<span class="prdctfltr_st_option prdctfltr_st_option_plaintext">
										<em><?php echo $term->name . ' ' . esc_html__( 'Tooltip', 'prdctfltr' ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
									</span>
								</div>
							<?php
							}

					break;


					case 'color' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_color" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_color">
									<em><?php echo $term->name . ' ' . esc_html__( 'Color', 'prdctfltr' ); ?></em> <input class="prdctfltr_st_color" type="text" name="term_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : '#cccccc'; ?>" />
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . esc_html__( 'Tooltip', 'prdctfltr' ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;


					case 'image' :
					case 'image-text' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_image" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_imgurl">
									<em><?php echo $term->name . ' ' . esc_html__( 'Image URL', 'prdctfltr' ); ?></em> <input type="text" name="term_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['term_' . $term->slug] ) ? $settings['term_' . $term->slug] : ''; ?>" />
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_button">
									<em><?php esc_html_e( 'Add/Upload image', 'prdctfltr' ); ?></em> <a href="#" class="prdctfltr_st_upload_media button"><?php esc_html_e( 'Image Gallery', 'prdctfltr' ); ?></a>
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . ( $curr_style == 'image' ? esc_html__( 'Tooltip', 'prdctfltr' ) : esc_html__( 'Text', 'prdctfltr' ) ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;


					case 'html' :

						foreach ( $catalog_attrs as $term ) {

						?>
							<div class="prdctfltr_st_term prdctfltr_style_html" data-term="<?php echo $term->slug; ?>">
								<span class="prdctfltr_st_option prdctfltr_st_option_html">
									<em><?php echo $term->name . ' ' . esc_html__( 'HTML', 'prdctfltr' ); ?></em> <textarea name="term_<?php echo $term->slug; ?>"><?php echo isset( $settings['term_' . $term->slug] ) ? stripslashes( $settings['term_' . $term->slug] ) : ''; ?></textarea>
								</span>
								<span class="prdctfltr_st_option prdctfltr_st_option_tooltip">
									<em><?php echo $term->name . ' ' . esc_html__( 'Tooltip', 'prdctfltr' ); ?></em> <input type="text" name="tooltip_<?php echo $term->slug; ?>" value="<?php echo isset( $settings['tooltip_' . $term->slug] ) ? $settings['tooltip_' . $term->slug] : ''; ?>" />
								</span>
							</div>
						<?php
						}

					break;

					case 'select' :
					?>
						<div class="prdctfltr_select">
							<?php esc_html_e( 'Select Box currently has no special options. !Important Do not use select boxes inside the select box mode!', 'prdctfltr' ); ?>
						</div>
					<?php
					break;

					default :
					break;

				}

				$html = ob_get_clean();

				return $html;

			}
			else {
				if ( $filter == 'meta' ) {
					return esc_html__( 'Meta filter not customized. Use the Cogs Wheel icon!', 'prdctfltr' );
				}
				return esc_html__( 'Error! No terms!', 'prdctfltr' );
			}

		}

		public static function save_terms() {

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $key == '' || $settings == '' ) {
				die();
				exit;
			}

			if ( isset( $settings['style'] ) ) {

				$alt = array();

				$alt['style'] = $settings['style'];

				unset( $settings['style'] );

				$alt['settings'] = $settings;

				update_option( $key, $alt, 'no' );

				die( 'Updated!' );
				exit;
			}

			die();
			exit;

		}

		public static function remove_terms() {

			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $settings !== '' ) {
				$get_customization = get_option( $key, '' );

				if ( $get_customization !== '' ) {
					delete_option( $key );

					die( 'Removed' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function add_filters() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

			if ( !isset( $filter ) ) {
				die();
				exit;
			}

			switch ( $filter ) {
				case 'price' :
					ob_start();
?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_min">
							<em><?php esc_html_e( 'Minimum', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_min" value="" />
						</span>
						<span class="pf_max">
							<em><?php esc_html_e( 'Maximum', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_max" value="" />
						</span>
						<span class="pf_text">
							<em><?php esc_html_e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="prdctfltr_filter_remove"></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;

				break;
				case 'per_page' :
					ob_start();
?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_value">
							<em><?php esc_html_e( 'Value', 'prdctfltr' ); ?></em>
							<input type="number" min="1" name="pf_value" value="" />
						</span>
						<span class="pf_text">
							<em><?php esc_html_e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="prdctfltr_filter_remove"></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;
				break;
				case 'meta' :
					ob_start();
				?>
					<div class="prdctfltr_quickview_filter">
						<span class="pf_value">
							<em><?php esc_html_e( 'Value', 'prdctfltr' ); ?></em>
							<input type="text" name="pf_value" value="" />
						</span>
						<span class="pf_text">
							<em><?php esc_html_e( 'Text', 'prdctfltr' ); ?></em>
							<textarea name="pf_text"></textarea>
						</span>
						<a href="#" class="prdctfltr_filter_remove"></a>
					</div>
<?php
					$html = ob_get_clean();
					die( $html );
					exit;
				default :
				break;
			}

		}

		public static function set_filters() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';

			if ( !isset( $filter ) ) {
				die();
				exit;
			}

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';

			if ( $key !== '' ) {
				$get_customization = get_option( $key, '' );
				if ( !empty( $get_customization ) ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
				$key = 'wc_settings_prdctfltr_filter_customization_' . uniqid();
			}

			ob_start();
?>
			<div class="prdctfltr_quickview_terms" data-key="<?php echo $key; ?>">
				<span class="prdctfltr_quickview_close"><span class="prdctfltr_quickview_close_button"><?php esc_html_e( 'Click to discard any settings!', 'prdctfltr' ); ?></span></span>
				<div class="prdctfltr_quickview_terms_inner">
					<div class="prdctfltr_quickview_filters_settings">
						<span class="prdctfltr_set_filters_type" data-filter="<?php echo $filter; ?>"><?php esc_html_e( 'Filter', 'prdctfltr' ); ?> &rarr; <code><?php echo $filter; ?></code></span>
						<a href="#" class="button prdctfltr_set_filters_add"><?php esc_html_e( 'Add Value', 'prdctfltr' ); ?></a>
						<a href="#" class="button-primary prdctfltr_set_filters_save"><?php esc_html_e( 'Save Customization', 'prdctfltr' ); ?></a>
					</div>
					<div class="prdctfltr_quickview_filters_manager prdctfltr_quickview_filter_<?php echo $filter; ?>">
<?php
						self::get_filters( $filter, $customization );
?>
					</div>
				</div>
			</div>
<?php
			$html = ob_get_clean();

			die( $html );
			exit;

		}

		public static function set_filters_new() {

			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';

			if ( $filter == '' ) {
				die();
				exit;
			}

			if ( $key !== '' ) {
				$get_customization = get_option( $key, '' );
				if ( !empty( $get_customization ) ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
			}

			$html = self::get_filters( $filter, $customization );

			die( $html );
			exit;

		}

		public static function get_filters( $filter, $customization ) {

			switch ( $filter ) {

				case 'price' :

					if ( empty( $customization ) ) {

						$curr_prices = array();
						$curr_prices_currency = array();
						$catalog_ready_price = array();

						$curr_price_set = get_option( 'wc_settings_prdctfltr_price_range', '100' );
						$curr_price_add = get_option( 'wc_settings_prdctfltr_price_range_add', '100' );
						$curr_price_limit = get_option( 'wc_settings_prdctfltr_price_range_limit', '6' );

						if ( get_option( 'wc_settings_prdctfltr_price_none', 'no' ) == 'no' ) {
							$catalog_ready_price = array(
								'-' => esc_html__( 'None', 'prdctfltr' )
							);
						}
					}
					else {
						foreach( $customization['settings'] as $k => $v ) {
							$prices[] = array(
								'value' => $k,
								'text' => $v
							);
						}
						$curr_price_limit = count( $customization['settings'] );
					}

					for ( $i = 0; $i < $curr_price_limit; $i++ ) {

						if ( empty( $customization ) ) {

							if ( $i == 0 ) {
								$min_price = 0;
								$max_price = $curr_price_set;
							}
							else {
								$min_price = $curr_price_set+( $i-1)*$curr_price_add;
								$max_price = $curr_price_set+$i*$curr_price_add;
							}

							$curr_text = strip_tags( wc_price( $min_price ) . ( $i+1 == $curr_price_limit ? '+' : ' - ' . wc_price( $max_price ) ) );

						}
						else {
							$vals = explode( '-', $prices[$i]['value'] );
							$min_price = ( isset( $vals[0] ) ? $vals[0] : '' );
							$max_price = ( isset( $vals[1] ) ? $vals[1] : '' );
							$curr_text = ( isset( $prices[$i]['text'] ) ? $prices[$i]['text'] : '' );
						}
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_min">
								<em><?php esc_html_e( 'Minimum', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_min" value="<?php echo $min_price; ?>" />
							</span>
							<span class="pf_max">
								<em><?php esc_html_e( 'Maximum', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_max" value="<?php echo $max_price; ?>" />
							</span>
							<span class="pf_text">
								<em><?php esc_html_e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo stripslashes( $curr_text ); ?></textarea>
							</span>
							<a href="#" class="prdctfltr_filter_remove"></a>
						</div>
<?php
					}

				break;

				case 'per_page' :

					if ( empty( $customization ) ) {

						$curr_perpage_set = get_option( 'wc_settings_prdctfltr_perpage_range', '20' );
						$curr_perpage_limit = get_option( 'wc_settings_prdctfltr_perpage_range_limit', '5' );

						$curr_perpage = array();

						for ( $i = 1; $i <= $curr_perpage_limit; $i++ ) {
							$curr_perpage[$curr_perpage_set*$i] = $curr_perpage_set*$i . ' ' . esc_html__( 'Products', 'prdctfltr' );
						}

					}
					else {
						$curr_perpage_limit = count( $customization['settings'] );

						for ( $i = 0; $i < $curr_perpage_limit; $i++ ) {
							$curr_perpage[$customization['settings'][$i]['value']] = $customization['settings'][$i]['text'];
						}
					}

					foreach( $curr_perpage as $k => $v ) {
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_value">
								<em><?php esc_html_e( 'Value', 'prdctfltr' ); ?></em>
								<input type="number" name="pf_value" min="1" value="<?php echo $k; ?>" />
							</span>
							<span class="pf_text">
								<em><?php esc_html_e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo stripslashes( $v ); ?></textarea>
							</span>
							<a href="#" class="prdctfltr_filter_remove"></a>
						</div>
<?php

					}

				break;

				case 'meta' :

					if ( empty( $customization ) ) {

						$curr_meta = array(
							'' => ''
						);

					}
					else {
						$curr_meta_limit = count( $customization['settings'] );

						for ( $i = 0; $i < $curr_meta_limit; $i++ ) {
							$curr_meta[$customization['settings'][$i]['value']] = $customization['settings'][$i]['text'];
						}
					}

					foreach( $curr_meta as $k => $v ) {
?>
						<div class="prdctfltr_quickview_filter">
							<span class="pf_value">
								<em><?php esc_html_e( 'Value', 'prdctfltr' ); ?></em>
								<input type="text" name="pf_value" value="<?php echo $k; ?>" />
							</span>
							<span class="pf_text">
								<em><?php esc_html_e( 'Text', 'prdctfltr' ); ?></em>
								<textarea name="pf_text"><?php echo stripslashes( $v ); ?></textarea>
							</span>
							<a href="#" class="prdctfltr_filter_remove"></a>
						</div>
<?php

					}

				break;

				default :
				break;

			}

		}

		public static function save_filters() {

			$key = isset( $_POST['key'] ) ? $_POST['key'] : '';
			$filter = isset( $_POST['filter'] ) ? $_POST['filter'] : '';
			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $key == '' || $filter == '' || $settings == '' ) {
				die();
				exit;
			}

			$alt = array();

			$alt['filter'] = $filter;

			if ( $filter == 'price' ) {
				foreach ( $settings as $set ) {
					$alt['settings'][$set['min'] . '-' . $set['max']] = $set['text'];
				}
			}
			else {
				$alt['settings'] = $settings;
			}

			update_option( $key, $alt, 'no' );

			die( 'Updated!' );
			exit;

		}

		public static function remove_filters() {

			$settings = isset( $_POST['settings'] ) ? $_POST['settings'] : '';

			if ( $settings !== '' ) {
				$get_customization = get_option( $key, '' );

				if ( $get_customization !== '' ) {
					delete_option( $key );

					die( 'Removed' );
					exit;
				}
			}

			die();
			exit;

		}

		public static function reset_options() {

			global $wpdb;

			$wpdb->query( "delete from $wpdb->options where option_name like '%prdctfltr%';" );

			update_option( 'wc_settings_prdctfltr_version', PrdctfltrInit::$version, 'yes' );

			die( 'Deleted!');
			exit;
		
		}
		public static function analytics_reset() {

			delete_option( 'wc_settings_prdctfltr_filtering_analytics_stats' );
			die( 'Updated!' );
			exit;

		}

		public static function prdctfltr_utf8_decode( $str ) {
			$str = preg_replace( "/%u([0-9a-f]{3,4})/i", "&#x\\1;", urldecode( $str ) );
			return html_entity_decode( $str, null, 'UTF-8' );
		}

		public static function prdctfltr_wpml_language() {

			if ( class_exists( 'SitePress' ) ) {
				global $sitepress;

				$default_language = $sitepress->get_default_language();
				$current_language = $sitepress->get_current_language();

				if ( $default_language != $current_language ) {
					$language = sanitize_title( $current_language );

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

		public static function get_filter_customization( $filter, $key ) {

			/*$language = self::prdctfltr_wpml_language();*/

			if ( $key !== '' ) {
				$get_customization = get_option( $key, '' );
				if ( !empty( $get_customization ) ) {
					$customization = $get_customization;
				}
			}

			if ( !isset( $customization ) ) {
				$customization = array();
			}

			return $customization;

		}


		public static function prdctfltr_pf_taxonomy_sanitize( $value, $option, $raw_value ) {
			if ( $option['type'] == 'pf_taxonomy' ) {
				$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
				return $value;
			}
			return $value;
		}


		public static function prdctfltr_pf_taxonomy( $field ) {

			$option_value = WC_Admin_Settings::get_option( $field['id'], $field['default'] );
		?>
			<tr valign="top">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field['id'] ); ?>"><?php echo esc_html( $field['title'] ); ?></label>
					<?php //echo $tooltip_html; ?>
				</th>
				<td class="forminp forminp-<?php echo sanitize_title( $field['type'] ) ?>">
				<?php
					$readyVals = array();
					if ( taxonomy_exists( $field['options'] ) ) {

						$terms = get_terms( $field['options'], array( 'hide_empty' => 0, 'hierarchical' => ( is_taxonomy_hierarchical( $field['options'] ) ? 1 : 0 ) ) );
						if ( is_taxonomy_hierarchical( $field['options'] ) ) {
							$terms_sorted = array();
							self::sort_terms_hierarchicaly( $terms, $terms_sorted );
							$terms = $terms_sorted;
						}

						if ( !empty( $terms ) && !is_wp_error( $terms ) ){
							$var =0;
							self::get_option_terms( $terms, $readyVals, $var );
						}

					}
					
				?>
					<select
						name="<?php echo esc_attr( $field['id'] ); ?>[]"
						id="<?php echo esc_attr( $field['id'] ); ?>"
						style="<?php echo esc_attr( $field['css'] ); ?>"
						class="<?php echo esc_attr( $field['class'] ); ?>"
						<?php echo 'multiple="multiple"';?>
						>
						<?php
							foreach ( $readyVals as $key => $val ) {
								?>
								<option value="<?php echo esc_attr( $key ); ?>" <?php
									if ( is_array( $option_value ) ) {
										selected( in_array( $key, $option_value ), true );
									} else {
										selected( $option_value, $key );
									}
								?>><?php echo $val ?></option>
								<?php
							}
						?>
					</select> <?php echo $field['desc']; ?>
				</td>
			</tr>
		<?php

		}

		public static function get_option_terms( $terms, &$readyVals, &$level ) {
			foreach ( $terms as $term ) {
				$readyVals[self::prdctfltr_utf8_decode( $term->slug )] = ( $level > 0 ? str_repeat( '&nbsp;&nbsp;', $level ) : '' ) . $term->name;
				if ( !empty( $term->children ) ) {
					$level++;
					self::get_option_terms( $term->children, $readyVals, $level );
					$level--;
				}
			}
		}

		public static function sort_terms_hierarchicaly( Array &$cats, Array &$into, $parentId = 0 ) {
			foreach ( $cats as $i => $cat ) {
				if ( $cat->parent == $parentId ) {
					$into[$cat->term_id] = $cat;
					unset($cats[$i]);
				}
			}
			foreach ( $into as $topCat ) {
				$topCat->children = array();
				self::sort_terms_hierarchicaly( $cats, $topCat->children, $topCat->term_id );
			}
		}

		public static function save_fields( $options, $options_ajax ) {
			if ( empty( $options_ajax ) ) {
				return false;
			}

			$update_options = array();

			foreach ( $options as $option ) {
				if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) ) {
					continue;
				}

				if ( strstr( $option['id'], '[' ) ) {
					parse_str( $option['id'], $option_name_array );
					$option_name  = current( array_keys( $option_name_array ) );
					$setting_name = key( $option_name_array[ $option_name ] );
					$raw_value    = isset( 	$options_ajax[ $option_name ][ $setting_name ] ) ? wp_unslash( 	$options_ajax[ $option_name ][ $setting_name ] ) : null;
				} else {
					$option_name  = $option['id'];
					$setting_name = '';
					$raw_value    = isset( 	$options_ajax[ $option['id'] ] ) ? wp_unslash( 	$options_ajax[ $option['id'] ] ) : null;
				}

				switch ( $option['type'] ) {
					case 'checkbox' :
						$value = is_null( $raw_value ) ? 'no' : 'yes';
						break;
					case 'textarea' :
						$value = wp_kses_post( trim( $raw_value ) );
						break;
					case 'multiselect' :
					case 'multi_select_countries' :
						$value = array_filter( array_map( 'wc_clean', (array) $raw_value ) );
						break;
					case 'image_width' :
						$value = array();
						if ( isset( $raw_value['width'] ) ) {
							$value['width']  = wc_clean( $raw_value['width'] );
							$value['height'] = wc_clean( $raw_value['height'] );
							$value['crop']   = isset( $raw_value['crop'] ) ? 1 : 0;
						} else {
							$value['width']  = $option['default']['width'];
							$value['height'] = $option['default']['height'];
							$value['crop']   = $option['default']['crop'];
						}
						break;
					default :
						$value = wc_clean( $raw_value );
						break;
				}

				if ( has_action( 'woocommerce_update_option_' . sanitize_title( $option['type'] ) ) ) {
					_deprecated_function( 'The woocommerce_update_option_X action', '2.4.0', 'woocommerce_admin_settings_sanitize_option filter' );
					do_action( 'woocommerce_update_option_' . sanitize_title( $option['type'] ), $option );
					continue;
				}

				$value = apply_filters( 'woocommerce_admin_settings_sanitize_option', $value, $option, $raw_value );

				$value = apply_filters( "woocommerce_admin_settings_sanitize_option_$option_name", $value, $option, $raw_value );

				if ( is_null( $value ) ) {
					continue;
				}

				if ( $option_name && $setting_name ) {
					if ( ! isset( $update_options[ $option_name ] ) ) {
						$update_options[ $option_name ] = get_option( $option_name, array() );
					}
					if ( ! is_array( $update_options[ $option_name ] ) ) {
						$update_options[ $option_name ] = array();
					}
					$update_options[ $option_name ][ $setting_name ] = $value;
				} else {
					$update_options[ $option_name ] = $value;
				}

				do_action( 'woocommerce_update_option', $option );
			}

			return $update_options;
		}

	}

	add_action( 'init', 'WC_Settings_Prdctfltr::init' );

?>

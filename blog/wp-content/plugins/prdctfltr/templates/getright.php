<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	switch ( WC_Prdctfltr::$settings['template'] ) {

		case 'loop/orderby.php' :
			if ( apply_filters( 'prdctfltr_orderby_template_condition', !isset( WC_Prdctfltr::$settings['was_init'] ) ) ) {
				include( 'product-filter.php' );
				WC_Prdctfltr::$settings['was_init'] = true;
			}
		break;

		case 'loop/result-count.php' :
			if ( apply_filters( 'prdctfltr_resultcount_template_condition', !isset( WC_Prdctfltr::$settings['was_init'] ) ) ) {
				include( 'product-filter.php' );
				WC_Prdctfltr::$settings['was_init'] = true;
			}
		break;

		case 'loop/pagination.php' :

			global $prdctfltr_global;

			$pf_pag_type = isset( $prdctfltr_global['pagination_type'] ) ? $prdctfltr_global['pagination_type'] : WC_Prdctfltr::$settings['wc_settings_prdctfltr_pagination_type'];

			if ( $pf_pag_type == 'prdctfltr-pagination-default' ) {

				global $wp_query;

				if ( $wp_query->max_num_pages <= 1 ) {
					return;
				}

			?>
				<nav class="woocommerce-pagination prdctfltr-pagination prdctfltr-pagination-default">
					<?php
						echo paginate_links( apply_filters( 'prdctfltr_pagination_args', array(
							'base'         => esc_url( untrailingslashit( esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ) ) . '/?paged=%#%' ),
							'format'       => '',
							'add_args'     => false,
							'current'      => max( 1, !isset( $wp_query->query_vars['paged'] ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->query_vars['paged'] : $wp_query->query_vars['paged'] ),
							'total'        => !isset( $wp_query->max_num_pages ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->max_num_pages : $wp_query->max_num_pages,
							'prev_text'    => '&larr;',
							'next_text'    => '&rarr;',
							'type'         => 'list',
							'end_size'     => 3,
							'mid_size'     => 3
						) ) );
					?>
				</nav>
			<?php
			}
			else if ( $pf_pag_type == 'prdctfltr-pagination-load-more' ) {
				global $wp_query;

				$pf_found_posts = !isset( $wp_query->found_posts ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->found_posts : $wp_query->found_posts;
				$pf_per_page = !isset( $wp_query->query_vars['posts_per_page'] ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->query_vars['posts_per_page'] : $wp_query->query_vars['posts_per_page'];
				$pf_offset = isset( $wp_query->query_vars['offset'] ) ? $wp_query->query_vars['offset'] : 0;

			?>
				<nav class="woocommerce-pagination prdctfltr-pagination prdctfltr-pagination-load-more">
				<?php
					if ( $pf_found_posts > 0 && $pf_found_posts > $pf_per_page + $pf_offset ) {
					?>
						<a href="#" class="button"><?php esc_html_e( 'Load More', 'prdctfltr' ); ?></a>
					<?php
					}
					else {
					?>
						<span class="button disabled"><?php esc_html_e( 'No More Products!', 'prdctfltr' ); ?></span>
					<?php
					}
				?>
				</nav>
			<?php
			}
			else if ( $pf_pag_type == 'prdctfltr-pagination-infinite-load' ) {
				global $wp_query;

				$pf_found_posts = !isset( $wp_query->found_posts ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->found_posts : $wp_query->found_posts;
				$pf_per_page = !isset( $wp_query->query_vars['posts_per_page'] ) ? WC_Prdctfltr_Shortcodes::$settings['instance']->query_vars['posts_per_page'] : $wp_query->query_vars['posts_per_page'];
				$pf_offset = isset( $wp_query->query_vars['offset'] ) ? $wp_query->query_vars['offset'] : 0;

			?>
				<nav class="woocommerce-pagination prdctfltr-pagination prdctfltr-pagination-load-more prdctfltr-pagination-infinite-load">
				<?php
					if ( $pf_found_posts > 0 && $pf_found_posts > $pf_per_page + $pf_offset ) {
					?>
						<a href="#" class="button"><?php esc_html_e( 'Load More', 'prdctfltr' ); ?></a>
					<?php
					}
					else {
					?>
						<span class="button disabled"><?php esc_html_e( 'No More Products!', 'prdctfltr' ); ?></span>
					<?php
					}
				?>
				</nav>
			<?php
			}

		break;

		case 'loop/no-products-found.php' :

			WC_Prdctfltr::$settings['did_noproducts'] = true;

			if ( isset( WC_Prdctfltr::$settings['instance'] ) ) {
				$override = WC_Prdctfltr::$settings['instance']['wc_settings_prdctfltr_noproducts'];
			}
			else {
				$override = '';
			}

			$class = ( WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_class'] == '' ? 'products' : WC_Prdctfltr::$settings['wc_settings_prdctfltr_ajax_class'] );

			echo '<div class="' . $class . ' woocommerce-page prdctfltr-added-wrap">';

			if ( $override == '' ) {
				do_action( 'woocommerce_no_products_found' );
			}
			else {
				echo do_shortcode( $override );
			}

			echo '</div>';

		break;

		default :
		break;

	}

?>
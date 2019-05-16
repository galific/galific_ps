<?php

	if ( !isset($pf_activated['instock_products']) && isset($query->query_vars['instock_products']) && in_array( $query->query_vars['instock_products'], array( 'in', 'out', 'both' ) ) ) {
		$pf_activated['instock_products'] = $query->query_vars['instock_products'];
	}

	if ( ( ( ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] !== '' && ( $pf_activated['instock_products'] == 'in' || $pf_activated['instock_products'] == 'out' ) ) || self::$settings['wc_settings_prdctfltr_instock'] == 'yes' ) !== false ) && ( !isset( $pf_activated['instock_products'] ) || $pf_activated['instock_products'] !== 'both' ) ) {

		$notify = absint( max( get_option( 'woocommerce_notify_no_stock_amount' ), 0 ) )+1;
		$notify_out = $notify-1;

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

		$managedStock = array();
		$notManagedStock = array();
		$variableManagedStockOut = array();
		$variableNotManagedStockOut = array();
		$variableManagedStock = array();
		$variableManagedStock = array();
		$variableManagedStockBack = array();
		$variableNotManagedStock = array();

		if ( apply_filters( 'prdctfltr_instock_single', true ) === true ) {
			if ( apply_filters( 'prdctfltr_instock_manageable', true ) === true ) {
				$managedStock = $wpdb->get_results( $wpdb->prepare( '
					SELECT DISTINCT(%1$s.ID) ID FROM %1$s
					INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id) AND pf1.meta_key = "_manage_stock" AND pf1.meta_value = "yes"
					INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_stock" AND pf2.meta_value < ' . $notify . '
					INNER JOIN %2$s AS pf3 ON (%1$s.ID = pf3.post_id) AND pf3.meta_key = "_backorders" AND pf3.meta_value = "no"
					' . $join . '
					WHERE %1$s.post_type = "product"
					AND %1$s.post_status = "publish"
					' . $where . '
					LIMIT 29999
					
				', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );
			}
			if ( apply_filters( 'prdctfltr_instock_nonmanageable', true ) === true ) {
				$notManagedStock = $wpdb->get_results( $wpdb->prepare( '
					SELECT DISTINCT(%1$s.ID) FROM %1$s
					INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id) AND pf1.meta_key = "_stock_status" AND pf1.meta_value = "outofstock"
					INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_manage_stock" AND pf2.meta_value = "no"
					' . $join . '
					WHERE %1$s.post_type = "product"
					AND %1$s.post_status = "publish"
					' . $where . '
					LIMIT 29999
				', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );
			}
		}

		if ( apply_filters( 'prdctfltr_instock_variable', true ) === true ) {
			if ( count( $f_attrs ) > 0 ) {

				$curr_atts =  implode( '","', array_map( 'esc_sql', $f_attrs ) );
				$curr_terms = implode( '","', array_map( 'esc_sql', $f_terms ) );

				global $wpdb;

				if ( apply_filters( 'prdctfltr_instock_manageable', true ) === true ) {
					$variableManagedStockOut = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT(%1$s.post_parent) as ID FROM %1$s
						INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id)
						INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_manage_stock" AND pf2.meta_value = "yes"
						INNER JOIN %2$s AS pf3 ON (%1$s.ID = pf3.post_id) AND pf3.meta_key = "_stock" AND pf3.meta_value < ' . $notify . '
						INNER JOIN %2$s AS pf4 ON (%1$s.ID = pf4.post_id) AND pf4.meta_key = "_backorders" AND pf4.meta_value = "no"
						WHERE %1$s.post_type = "product_variation"
						AND pf1.meta_key IN ("'.$curr_atts.'") AND pf1.meta_value IN ("'.$curr_terms.'","")
						GROUP BY pf1.post_id
						HAVING COUNT(DISTINCT pf1.meta_key) = ' . count( $f_attrs ) .'
						LIMIT 29999
					', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );
				}

				if ( apply_filters( 'prdctfltr_instock_nonmanageable', true ) === true ) {
					$variableNotManagedStockOut = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT(%1$s.post_parent) as ID FROM %1$s
						INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id)
						INNER JOIN %2$s AS pf3 ON (%1$s.ID = pf3.post_id) AND pf3.meta_key = "_stock_status" AND pf3.meta_value = "outofstock"
						INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_manage_stock" AND pf2.meta_value = "no"
						WHERE %1$s.post_type = "product_variation"
						AND pf1.meta_key IN ("'.$curr_atts.'") AND pf1.meta_value IN ("'.$curr_terms.'","")
						GROUP BY pf1.post_id
						HAVING COUNT(DISTINCT pf1.meta_value) = ' . count( $f_attrs ) .'
						LIMIT 29999
					', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );
				}

				if ( apply_filters( 'prdctfltr_instock_manageable', true ) === true ) {
					$variableManagedStock = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT(%1$s.post_parent) as ID FROM %1$s
						INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id)
						INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_manage_stock" AND pf2.meta_value = "yes"
						INNER JOIN %2$s AS pf3 ON (%1$s.ID = pf3.post_id) AND pf3.meta_key = "_stock" AND pf3.meta_value >= ' . $notify . '
						WHERE %1$s.post_type = "product_variation"
						AND pf1.meta_key IN ("'.$curr_atts.'") AND pf1.meta_value IN ("'.$curr_terms.'","")
						GROUP BY pf1.post_id
						HAVING COUNT(DISTINCT pf1.meta_key) = ' . count( $f_attrs ) .'
						LIMIT 29999
					', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );

					$variableManagedStockBack = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT(%1$s.post_parent) as ID FROM %1$s
						INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id)
						INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_manage_stock" AND pf2.meta_value = "yes"
						INNER JOIN %2$s AS pf3 ON (%1$s.ID = pf3.post_id) AND pf3.meta_key = "_stock" AND pf3.meta_value < ' . $notify . '
						INNER JOIN %2$s AS pf4 ON (%1$s.ID = pf4.post_id) AND pf4.meta_key = "_backorders" AND pf4.meta_value != "no"
						WHERE %1$s.post_type = "product_variation"
						AND pf1.meta_key IN ("'.$curr_atts.'") AND pf1.meta_value IN ("'.$curr_terms.'","")
						GROUP BY pf1.post_id
						HAVING COUNT(DISTINCT pf1.meta_key) = ' . count( $f_attrs ) .'
						LIMIT 29999
					', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );
				}

				if ( apply_filters( 'prdctfltr_instock_nonmanageable', true ) === true ) {
					$variableNotManagedStock = $wpdb->get_results( $wpdb->prepare( '
						SELECT DISTINCT(%1$s.post_parent) as ID FROM %1$s
						INNER JOIN %2$s AS pf1 ON (%1$s.ID = pf1.post_id)
						INNER JOIN %2$s AS pf3 ON (%1$s.ID = pf3.post_id) AND pf3.meta_key = "_stock_status" AND pf3.meta_value = "instock"
						INNER JOIN %2$s AS pf2 ON (%1$s.ID = pf2.post_id) AND pf2.meta_key = "_manage_stock" AND pf2.meta_value = "no"
						WHERE %1$s.post_type = "product_variation"
						AND pf1.meta_key IN ("'.$curr_atts.'") AND pf1.meta_value IN ("'.$curr_terms.'","")
						GROUP BY pf1.post_id
						HAVING COUNT(DISTINCT pf1.meta_value) = ' . count( $f_attrs ) .'
						LIMIT 29999
					', $wpdb->posts, $wpdb->postmeta ), ARRAY_N );
				}

			}
		}

		$pf_exclude_product = array_merge( $managedStock, $notManagedStock, $variableManagedStockOut, $variableNotManagedStockOut );
		$pf_out_products = array_merge( $variableManagedStock, $variableNotManagedStock, $variableManagedStockBack );

		$curr_in = array();
		$curr_out = array();

		if ( isset( $pf_out_products ) && is_array( $pf_out_products ) ) {
			foreach ( $pf_out_products as $k => $p ) {
				if ( !in_array( $p[0], $curr_out ) ) {
					$curr_out[] = $p[0];
				}
			}
			if ( !empty( $curr_out ) ) {
				if ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] == 'out' ) {
					$curr_args = array_merge( $curr_args, array(
							'post__not_in' => $curr_out
						) );
				}
				/*else {
					$curr_args = array_merge( $curr_args, array(
							'post__in' => $curr_out
						) );
				}*/
			}
		}

		if ( isset( $pf_exclude_product ) && is_array( $pf_exclude_product ) ) {
			foreach ( $pf_exclude_product as $k => $p ) {
				if ( !in_array( $p[0], $curr_out ) && !in_array( $p[0], $curr_in ) ) {
					$curr_in[] = $p[0];
				}
			}
			if ( !empty( $curr_in ) ) {
				if ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] == 'out' ) {
					$curr_args = array_merge( $curr_args, array(
							'post__in' => $curr_in
						) );
				}
				else {
					$curr_args = array_merge( $curr_args, array(
							'post__not_in' => $curr_in
						) );
				}
			}
			else if ( isset( $pf_activated['instock_products'] ) && $pf_activated['instock_products'] == 'out' ) {
				$curr_args = array_merge( $curr_args, array(
						'post__in' => -1
					) );
			}
		}

	}

?>

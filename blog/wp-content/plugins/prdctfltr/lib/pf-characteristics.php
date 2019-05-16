<?php

	if ( ! defined( 'ABSPATH' ) ) exit;

	if ( get_option( 'wc_settings_prdctfltr_custom_tax', 'no' ) == 'yes' ) {

		function prdctfltr_characteristics() {

			$labels = array(
				'name'                       => esc_html_x( 'Characteristics', 'taxonomy general name', 'prdctfltr' ),
				'singular_name'              => esc_html_x( 'Characteristic', 'taxonomy singular name', 'prdctfltr' ),
				'search_items'               => esc_html__( 'Search Characteristics', 'prdctfltr' ),
				'popular_items'              => esc_html__( 'Popular Characteristics', 'prdctfltr' ),
				'all_items'                  => esc_html__( 'All Characteristics', 'prdctfltr' ),
				'parent_item'                => null,
				'parent_item_colon'          => null,
				'edit_item'                  => esc_html__( 'Edit Characteristics', 'prdctfltr' ),
				'update_item'                => esc_html__( 'Update Characteristics', 'prdctfltr' ),
				'add_new_item'               => esc_html__( 'Add New Characteristic', 'prdctfltr' ),
				'new_item_name'              => esc_html__( 'New Characteristic Name', 'prdctfltr' ),
				'separate_items_with_commas' => esc_html__( 'Separate Characteristics with commas', 'prdctfltr' ),
				'add_or_remove_items'        => esc_html__( 'Add or remove Characteristics', 'prdctfltr' ),
				'choose_from_most_used'      => esc_html__( 'Choose from the most used Characteristics', 'prdctfltr' ),
				'not_found'                  => esc_html__( 'No Characteristics found', 'prdctfltr' ),
				'menu_name'                  => esc_html__( 'Characteristics', 'prdctfltr' ),
			);

			$args = array(
				'hierarchical'          => false,
				'labels'                => $labels,
				'show_ui'               => true,
				'show_admin_column'     => true,
				'update_count_callback' => '_update_post_term_count',
				'query_var'             => true,
				'rewrite'               => array( 'slug' => 'characteristics' ),
			);

			register_taxonomy( 'characteristics', array( 'product' ), $args );
		}

		add_action( 'init', 'prdctfltr_characteristics', 9999 );

	}

?>
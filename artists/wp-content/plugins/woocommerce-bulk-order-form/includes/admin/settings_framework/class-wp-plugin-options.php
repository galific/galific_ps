<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link https://wordpress.org/plugins/woocommerce-role-based-price/
 * @package WooCommerce Role Based Price
 * @subpackage WooCommerce Role Based Price/Admin
 * @since 3.0
 */
if ( ! defined( 'WPINC' ) ) {
	die;
}

class WooCommerce_Bulk_Order_Form_Admin_Settings_Options {

	public function __construct() {
		add_filter( 'wc_bof_settings_pages', array( $this, 'settings_pages' ) );
		add_filter( 'wc_bof_settings_section', array( $this, 'settings_section' ) );
		add_filter( 'wc_bof_settings_fields', array( $this, 'settings_fields' ) );
	}

	public function settings_pages( $page ) {
		$page[] = array(
			'id'    => 'general',
			'slug'  => 'general',
			'title' => __( 'Settings', WC_BOF_TXT ),
		);
		$page[] = array(
			'id'    => 'addons',
			'slug'  => 'addons',
			'title' => __( 'Addons', WC_BOF_TXT ),
		);
		return $page;
	}

	public function settings_section( $section ) {
		$section['general'][] = array(
			'id'    => 'general',
			'title' => __( 'General Settings', WC_BOF_TXT ),
		);
		$section['general'][] = array(
			'id'    => 'products',
			'title' => __( 'Products', WC_BOF_TXT ),
		);
		$section['general'][] = array(
			'id'    => 'template_label',
			'title' => __( 'Text Translations', WC_BOF_TXT ),
		);
		//$section['addons'][] = array( 'id'=>'welcome', 'title'=> __('Addons Settings',WC_BOF_TXT));  

		$addonSettings = array(
			'addon_sample' => array(
				'id'    => 'welcome',
				'title' => __( 'No Addons Activated / Installed.', WC_BOF_TXT ),
			),
		);
		$addonSettings = apply_filters( 'wc_bof_addon_sections', $addonSettings );

		if ( count( $addonSettings ) > 1 )
			unset( $addonSettings['addon_sample'] );

		$section['addons'] = $addonSettings;
		return $section;
	}

	public function settings_fields( $fields ) {
		$templates = wc_bof_template_types();
		$tpl_List  = array();
		foreach ( $templates as $ky => $template ) {
			$tpl_List[ $ky ] = $template['name'];
		}
		$is_wc3                  = wc_bof_is_wc_v( '>=', '3.0' );
		$included_products_array = array();
		$included_products_json  = '';
		$included_products       = wc_bof_option( 'included' );
		$excluded_products_array = array();
		$excluded_products       = wc_bof_option( 'excluded' );
		$excluded_products_json  = '';

		if ( ! empty( $included_products ) ) {
			$included_products_array = wc_bof_settings_products_json( $included_products );
			$included_products_json  = json_encode( $included_products_array );
		} else {
			$included_products = '';
		}

		if ( ! empty( $excluded_products ) ) {
			$excluded_products_array = wc_bof_settings_products_json( $excluded_products );
			$excluded_products_json  = json_encode( $excluded_products_array );
		} else {
			$excluded_products = '';
		}

		$fields['general']['general'][] = array(
			'id'      => WC_BOF_DB . 'template_type',
			'type'    => 'select',
			'label'   => __( 'Order Form Template', WC_BOF_TXT ),
			'desc'    => __( 'Select which template you want to use.', WC_BOF_TXT ),
			'options' => $tpl_List,
			'default' => 'standard',
			'attr'    => array( 'class' => 'wc-enhanced-select wcbof-settings-width-fix', ),
		);


		$fields['general']['general'][] = array(
			'id'        => WC_BOF_DB . 'no_of_rows',
			'type'      => 'text',
			'text_type' => 'number',
			'default'   => '10',
			'label'     => __( 'Number of Rows', WC_BOF_TXT ),
			'desc'      => __( 'Number of rows to display on the bulk order form', WC_BOF_TXT ),
			//  'attr'    => array(  'class' => ' wcbof-settings-width-fix', )
		);


		$fields['general']['general'][] = array(
			'id'        => WC_BOF_DB . 'max_items',
			'type'      => 'text',
			'text_type' => 'number',
			'default'   => '0',
			'label'     => __( 'Maximum Items', WC_BOF_TXT ),
			'desc'      => __( 'Maximum Items to Display in a Search', WC_BOF_TXT ),
		);


		$fields['general']['general'][] = array(
			'id'         => WC_BOF_DB . 'single_addtocart',
			'pro_option' => true,
			'type'       => 'checkbox',
			'default'    => true,
			'label'      => __( 'Each Row Cart Button', WC_BOF_TXT ),
			//'desc' =>__('Display Add Row Button ?',WC_BOF_TXT),
			'attr'       => array(
				'data-show-label' => true,
				'data-label'      => __( 'Show Add To Cart Button on each row', WC_BOF_TXT ),
				'data-ulabel'     => __( 'Show Add To Cart Button on each row', WC_BOF_TXT ),
				'data-separator'  => '||',
			),
		);


		$fields['general']['general'][] = array(
			'id'         => WC_BOF_DB . 'add_rows',
			'pro_option' => true,
			'type'       => 'checkbox',
			'default'    => true,
			'label'      => __( 'Add Rows Button', WC_BOF_TXT ),
			//'desc' =>__('Display Add Row Button ?',WC_BOF_TXT),
			'attr'       => array(
				'data-show-label' => true,
				'data-label'      => __( 'Display Add Row Button ?', WC_BOF_TXT ),
				'data-ulabel'     => __( 'Display Add Row Button ?', WC_BOF_TXT ),
				'data-separator'  => '||',
			),
		);
		// register string for translation
		$add_rows_button = __('Add Rows (+)', WC_BOF_TXT );


		$fields['general']['general'][] = array(
			'id'         => WC_BOF_DB . 'show_image',
			'pro_option' => true,
			'type'       => 'checkbox',
			'default'    => true,
			'label'      => __( 'Show Product Image ', WC_BOF_TXT ),
			// 'desc' =>  __('Display Product Image in Autocomplete search ?', WC_BOF_TXT),
			'attr'       => array(
				'data-show-label' => true,
				'data-label'      => __( 'Display Product Image in Autocomplete search & preopulated templates?', WC_BOF_TXT ),
				'data-ulabel'     => __( 'Display Product Image in Autocomplete search & preopulated templates?', WC_BOF_TXT ),
				'data-separator'  => '||',
			),
		);


		$fields['general']['general'][] = array(
			'id'      => WC_BOF_DB . 'show_price',
			'type'    => 'checkbox',
			'default' => true,
			'label'   => __( 'Show Price ', WC_BOF_TXT ),
			//'desc' => __('Display price on bulk order form?', WC_BOF_TXT),
			'attr'    => array(
				'data-show-label' => true,
				'data-label'      => __( 'Display price on bulk order form?', WC_BOF_TXT ),
				'data-ulabel'     => __( 'Display price on bulk order form?', WC_BOF_TXT ),
				'data-separator'  => '||',
			),
		);


		$fields['general']['general'][] = array(
			'id'         => WC_BOF_DB . 'action_button',
			'pro_option' => true,
			'type'       => 'radio',
			'label'      => __( 'Set button to cart or checkout?', WC_BOF_TXT ),
			'default'    => 'cart',
			'options'    => array(
				'cart'     => __( 'Cart', WC_BOF_TXT ),
				'checkout' => __( 'Checkout', WC_BOF_TXT ),
			),
		);

		$fields['general']['general'][] = array(
			'id'         => WC_BOF_DB . 'image_width',
			'pro_option' => true,
			'type'       => 'text',
			'text_type'  => 'number',
			'label'      => __( 'Product Image Width & Height', WC_BOF_TXT ),
			'default'    => '50',
		);

		$fields['general']['general'][] = array(
			'id'         => WC_BOF_DB . 'image_height',
			'pro_option' => true,
			'type'       => 'text',
			'text_type'  => 'number',
			'label'      => '',
			'default'    => '50',
		);


		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'price_label',
			'type'    => 'text',
			'default' => __( 'Price', WC_BOF_TXT ),
			'label'   => __( 'Price column', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);

		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'product_label',
			'type'    => 'text',
			'default' => __( 'Product', WC_BOF_TXT ),
			'label'   => __( 'Product column', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);


		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'quantity_label',
			'type'    => 'text',
			'default' => __( 'Qty', WC_BOF_TXT ),
			'label'   => __( 'Quantity column', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);


		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'variation_label',
			'type'    => 'text',
			'default' => __( 'Variation', WC_BOF_TXT ),
			'label'   => __( 'Variation column', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);

		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'total_label',
			'type'    => 'text',
			'default' => __( 'Add To Cart', WC_BOF_TXT ),
			'label'   => __( 'Order Form total', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);

		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'single_addtocart_label',
			'type'    => 'text',
			'default' => __( 'Add To Cart', WC_BOF_TXT ),
			'label'   => __( 'Single Row Add To Cart button', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);


		$fields['general']['template_label'][] = array(
			'id'      => WC_BOF_DB . 'cart_label',
			'type'    => 'text',
			'default' => __( 'Cart', WC_BOF_TXT ),
			'label'   => __( 'Cart button', WC_BOF_TXT ),
			'attr'    => array( 'class' => ' wcbof-settings-width-fix', ),
		);


		$fields['general']['template_label'][] = array(
			'id'         => WC_BOF_DB . 'checkout_label',
			'pro_option' => true,
			'type'       => 'text',
			'default'    => __( 'Checkout', WC_BOF_TXT ),
			'label'      => __( 'Checkout button', WC_BOF_TXT ),
			'attr'       => array( 'class' => ' wcbof-settings-width-fix', ),
		);


		$fields['general']['products'][] = array(
			'id'         => WC_BOF_DB . 'category',
			'pro_option' => true,
			'type'       => 'select',
			'options'    => wc_bof_settings_get_categories(),
			'label'      => __( 'Product Category', WC_BOF_TXT ),
			'desc'       => __( 'Select The Category to list only products from that category', WC_BOF_TXT ),
			'attr'       => array(
				'multiple' => "multiple",
				'class'    => 'wc-enhanced-select  wcbof-settings-width-fix',
			),
		);

		$fields['general']['products'][] = array(
			'id'         => WC_BOF_DB . 'excluded',
			'pro_option' => true,
			'type'       => ( $is_wc3 === true ) ? 'select' : 'text',
			'label'      => __( 'Excluded Products', WC_BOF_TXT ),
			'desc'       => __( 'Search & Select product by name.', WC_BOF_TXT ),
			'value'      => $excluded_products,
			'options'    => $excluded_products_array,
			'attr'       => array(
				'data-action'   => "woocommerce_json_search_products",
				'data-multiple' => "true",
				'multiple'      => "multiple",
				'class'         => 'wc-product-search  wcbof-settings-width-fix',
				'data-selected' => $excluded_products_json,
			),
		);

		$fields['general']['products'][] = array(
			'id'         => WC_BOF_DB . 'included',
			'pro_option' => true,
			'type'       => ( $is_wc3 === true ) ? 'select' : 'text',
			'label'      => __( 'Included Products', WC_BOF_TXT ),
			'desc'       => __( 'Search & Select product by name.', WC_BOF_TXT ),
			'value'      => $included_products,
			'options'    => $included_products_array,
			'attr'       => array(
				'data-action'   => "woocommerce_json_search_products",
				'data-multiple' => "true",
				'class'         => 'wc-product-search  wcbof-settings-width-fix',
				'multiple'      => "multiple",
				"data-selected" => $included_products_json,
			),

		);

		$fields['general']['products'][] = array(
			'id'         => WC_BOF_DB . 'search_by',
			'pro_option' => true,
			'type'       => 'select',
			'default'    => 'all',
			'label'      => __( 'Search By', WC_BOF_TXT ),
			'desc'       => __( 'When searching for products search by:', WC_BOF_TXT ),
			'options'    => wc_bof_get_search_types(),
			'attr'       => array( 'class' => 'wc-enhanced-select  wcbof-settings-width-fix', ),
		);

		$att_desc   = __( 'This option is used only when search by is set to all %s', WC_BOF_TXT );
		$att_desc_s = '<br/> <span style="font-weight: bold; color: red; font-size: 10px; display: block;">' . __( "Note : if attribute search enabled when search by set to `ALL` search process may become slow based on how many attribute you have. so please do select only the attribute you need to be search in below field. ", WC_BOF_TXT ) . '</span>';
		$att_desc   = sprintf( $att_desc, $att_desc_s );

		$fields['general']['products'][] = array(
			'id'    => WC_BOF_DB . 'enable_search_attributes',
			'type'  => 'checkbox',
			'label' => __( 'Product Attributes', WC_BOF_TXT ),
			'desc'  => $att_desc,
			'attr'  => array(
				'data-label'     => __( 'Attributes Will Be Searched.', WC_BOF_TXT ),
				'data-ulabel'    => __( 'Attributes Will Not Be Searched.', WC_BOF_TXT ),
				'data-separator' => '||',
			),
		);


		$fields['general']['products'][] = array(
			'id'      => WC_BOF_DB . 'product_attributes',
			'type'    => 'select',
			'options' => wc_bof_settings_get_product_attributes(),
			'label'   => __( 'Product Attributes', WC_BOF_TXT ),
			//'desc' => __('Enter Category ID by <code>,</code> to list only products from that category',WC_BOF_TXT),
			'attr'    => array(
				'multiple' => "multiple",
				'class'    => 'wc-enhanced-select  wcbof-settings-width-fix',
			),
		);

		$fields['general']['products'][] = array(
			'id'         => WC_BOF_DB . 'result_format',
			'pro_option' => true,
			'type'       => 'select',
			'default'    => 'TPS',
			'label'      => __( 'Product Result Format', WC_BOF_TXT ),
			'desc'       => __( 'Choose your product search results format', WC_BOF_TXT ),
			'options'    => wc_bof_get_title_templates(),
			'attr'       => array( 'class' => 'wc-enhanced-select  wcbof-settings-width-fix', ),
		);


		$fields['general']['products'][] = array(
			'id'         => WC_BOF_DB . 'result_variation_format',
			'pro_option' => true,
			'type'       => 'select',
			'default'    => 'TPS',
			'label'      => __( 'Variation Product Result Format', WC_BOF_TXT ),
			'desc'       => __( 'Choose your variation product search results format', WC_BOF_TXT ),
			'options'    => wc_bof_get_title_templates(),
			'attr'       => array( 'class' => 'wc-enhanced-select  wcbof-settings-width-fix', ),
		);


		// $fields['general']['products'][] = array(
		// 	'id'      => WC_BOF_DB . 'attribute_display_format',
		// 	'type'    => 'radio',
		// 	'label'   => __( 'Product Attribute Format', WC_BOF_TXT ),
		// 	'default' => 'value',
		// 	'desc'    => __( 'Display Attribute Title or Just attribute value Eg : <code>Color:Red</code> | <code>Red</code>', WC_BOF_TXT ),
		// 	'options' => array(
		// 		'value'            => __( 'Attribute Value Only (recommended)', WC_BOF_TXT ),
		// 		'attributes_value' => __( 'Attribute Title And Value', WC_BOF_TXT ),
		// 	),
		// );
		return $fields;
	}

}

return new WooCommerce_Bulk_Order_Form_Admin_Settings_Options;
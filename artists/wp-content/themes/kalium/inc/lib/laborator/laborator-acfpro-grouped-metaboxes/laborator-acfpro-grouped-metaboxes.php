<?php
/**
 *  ACF5 (Pro) Group
 *  Developed by Arlind Nushi
 *  Date: 18-sep-2018
 *
 *  Laborator.co
 *  www.laborator.co
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access not allowed.
}

/**
 * Initialize ACF5 metabox grouping
 */
function kalium_acfpro_gm_init() {
	global $pagenow;

	// Resources
	wp_register_script( 'kalium-acfpro-gm-store-js', kalium()->locateFileUrl( 'inc/lib/laborator/laborator-acfpro-grouped-metaboxes/store.modern.min.js' ), null, kalium()->getVersion() );
	wp_register_script( 'kalium-acfpro-gm-main-js', kalium()->locateFileUrl( 'inc/lib/laborator/laborator-acfpro-grouped-metaboxes/laborator-acfpro-grouped-metaboxes.min.js' ), array(
		'jquery',
		'kalium-acfpro-gm-store-js',
	), kalium()->getVersion() );
	wp_register_style( 'kalium-acfpro-gm-main-css', kalium()->locateFileUrl( 'inc/lib/laborator/laborator-acfpro-grouped-metaboxes/laborator-acfpro-grouped-metaboxes.min.css' ), array( 'font-awesome' ), kalium()->getVersion() );

	// Enqueue admin scripts and styles
	if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {
		global $kalium_acfpro_gm_groups;

		$kalium_acfpro_gm_groups = array();
		$allowed_field_groups    = kalium_acfpro_gm_get_field_groups();

		$field_groups = function_exists( 'acf_get_field_groups' ) ? acf_get_field_groups() : acf()->local->groups;

		foreach ( $field_groups as $group_id => $group ) {
			if ( in_array( $group['key'], $allowed_field_groups ) ) {
				$kalium_acfpro_gm_groups[ $group['key'] ] = array(
					'title' => $group['title'],
					'icon'  => kalium_acfpro_gm_get_field_group_icon( $group['title'] ),
				);
			}
		}

		add_action( 'admin_enqueue_scripts', 'kalium_acfpro_gm_enqueue_assets' );
		add_action( 'admin_footer', 'kalium_acfpro_gm_grouped_metaboxes_footer' );
		add_action( 'add_meta_boxes', 'kalium_acfpro_gm_grouped_metaboxes_register', 10 );
	}

	// Development use only!
	// Generate code for supported Kalium metaboxes to group to avoid grouping other custom metaboxes
	if ( defined( 'KALIUM_DEV' ) && current_user_can( 'manage_options' ) && kalium()->url->get( 'list-acf-groups', true ) ) {
		$groups = new WP_Query( "post_type=acf-field-group&posts_per_page=-1" );
		$nl     = PHP_EOL;
		$code   = 'return array(' . $nl;

		foreach ( $groups->posts as $acf_group ) {
			$code .= "\t'" . $acf_group->post_name . "', // $acf_group->post_title" . $nl;
		}
		$code .= ');';

		die( $code );
	}
}

add_action( 'acf/init', 'kalium_acfpro_gm_init', 10 );

/**
 * Get field groups to group in single metabox container
 */
function kalium_acfpro_gm_get_field_groups() {
	return array(
		'group_5ba0c4870387e', // Portfolio Settings
		'group_5ba0c48759d39', // Post Slider Images
		'group_5ba0c4875e914', // Video Post Format Settings
		'group_5ba0c486ef604', // Audio Post Format Settings
		'group_5ba0c486f384b', // Portfolio Item Type
		'group_5ba0c48768d0b', // Side Portfolio (Portfolio Type 1)
		'group_5ba0c48780e3d', // Columned (Portfolio Type 2)
		'group_5ba0c48790af7', // Carousel (Portfolio Type 3)
		'group_5ba0c48794e83', // Zig Zag (Portfolio Type 4)
		'group_5ba0c4879adbe', // Fullscreen (Portfolio Type 5)
		'group_5ba0c487a27c2', // Lightbox (Portfolio Type 6)
		'group_5ba0c487b1831', // General Details
		'group_5ba0c487ca369', // Project Link
		'group_5ba0c487d4320', // Checklists
		'group_5ba0c487e4256', // Portfolio Gallery
		'group_5ba0c48846cf6', // Portfolio Gallery
		'group_5ba0c48866e5d', // Portfolio Gallery
		'group_5ba0c488c86e4', // Portfolio Gallery
		'group_5ba0c488cf1ee', // Other Settings
		'group_5ba0c488dda57', // Page Options
		'group_5ba0c4893cf5d', // Post Settings
		'group_5ba0c48949e0a', // Custom CSS
	);
}

/**
 * Get main title of grouped metaboxes
 */
function kalium_acfpro_gm_get_title() {
	return apply_filters( 'kalium_acfpro_gm_get_title', 'Parameters and Options' );
}

/**
 * Allowed post types to use grouped metaboxes
 */
function kalium_acfpro_gm_get_allowed_post_types() {
	$allowed_post_types = array( 'post', 'page', 'portfolio', 'product' );

	return apply_filters( 'kalium_acfpro_gm_get_allowed_post_types', $allowed_post_types );
}

/**
 * Defined field group icons
 */
function kalium_acfpro_gm_get_field_group_icon( $group_name ) {
	$icon = '';

	switch ( $group_name ) {
		case 'Audio Post Format Settings' :
			$icon = 'volume-up';
			break;

		case 'Video Post Format Settings' :
			$icon = 'video-camera';
			break;

		case 'Post Slider Images' :
			$icon = 'image';
			break;

		case 'Portfolio Settings' :
			$icon = 'file-text';
			break;

		case 'Portfolio Item Type' :
			$icon = 'th';
			break;

		case 'Side Portfolio (Portfolio Type 1)' :
		case 'Columned (Portfolio Type 2)' :
		case 'Carousel (Portfolio Type 3)' :
		case 'Zig Zag (Portfolio Type 4)' :
		case 'Fullscreen (Portfolio Type 5)' :
		case 'Lightbox (Portfolio Type 6)' :
			$icon = 'check';
			break;

		case 'General Details' :
			$icon = 'wrench';
			break;

		case 'Project Link' :
			$icon = 'link';
			break;

		case 'Checklists' :
			$icon = 'list';
			break;

		case 'Portfolio Gallery' :
			$icon = 'image';
			break;

		case 'Other Settings' :
			$icon = 'cog';
			break;

		case 'Page Options' :
			$icon = 'toggle-on';
			break;

		case 'Post Settings' :
			$icon = 'file-text-o';
			break;

		case 'Custom CSS' :
			$icon = 'code';
			break;
	}

	return $icon;
}

/**
 * Assets enqueue
 */
function kalium_acfpro_gm_enqueue_assets() {
	wp_enqueue_script( 'kalium-acfpro-gm-main-js' );
	wp_enqueue_style( 'kalium-acfpro-gm-main-css' );
}

/**
 * Register metabox for grouping metaboxes
 */
function kalium_acfpro_gm_grouped_metaboxes_register( $post_type ) {
	if ( in_array( $post_type, kalium_acfpro_gm_get_allowed_post_types() ) ) {
		$loading_indicaator = '<span class="panel-loading-indicator"><i class="fa fa-circle-o-notch fa-spin"></i></span>';
		add_meta_box( 'kalium-acfpro-grouped-metaboxes', $loading_indicaator . kalium_acfpro_gm_get_title(), 'kalium_acfpro_gm_metaboxes_container', $post_type, 'normal', 'high' );
	}
}

/**
 * Grouped metabox containers
 */
function kalium_acfpro_gm_metaboxes_container() {
	?>
    <div class="kalium-acfpro-grouped-metaboxes-container">
        <div class="kalium-acfpro-grouped-metaboxes-inner">
            <div class="kalium-acfpro-grouped-metaboxes-loading-indicator">
				<span>
					<i class="fa fa-circle-o-notch fa-spin"></i>
					Loading Options...
				</span>
            </div>
            <ul class="kalium-acfpro-grouped-metaboxes-tabs"></ul>
            <div class="kalium-acfpro-grouped-metaboxes-body"></div>
        </div>
    </div>
	<?php
}

/**
 * Grouped metaboxes JS instance
 */
function kalium_acfpro_gm_grouped_metaboxes_footer() {
	global $kalium_acfpro_gm_groups;

	// Parse JS variable to use in the JS lib
	kalium_define_js_variable( 'groupedMetaboxes', $kalium_acfpro_gm_groups );

	// Hide metaboxes initially until grouped
	echo '<style>';
	foreach ( $kalium_acfpro_gm_groups as $metabox_id => $metabox ) {
		//echo ".postbox-container .meta-box-sortables > #acf-{$metabox_id} { display: none; }\n";
	}
	echo '</style>';
}

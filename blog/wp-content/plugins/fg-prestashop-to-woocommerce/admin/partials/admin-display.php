<?php

/**
 * Provide an admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.fredericgilles.net/fg-prestashop-to-woocommerce/
 * @since      2.0.0
 *
 * @package    FG_PrestaShop_to_WooCommerce
 * @subpackage FG_PrestaShop_to_WooCommerce/admin/partials
 */
?>
<div id="fgp2wc_admin_page" class="wrap">
	<h2><?php print $data['title'] ?></h2>
	
	<p><?php print $data['description'] ?></p>
	
	<div id="fgp2wc_settings">
		<?php require('database-info.php'); ?>
		<?php require('empty-content.php'); ?>

		<form id="form_import" method="post">

			<?php wp_nonce_field( 'parameters_form', 'fgp2wc_nonce' ); ?>

			<table class="form-table">
				<?php require('settings.php'); ?>
				<?php do_action('fgp2wc_post_display_settings_options'); ?>
				<?php require('settings-submit.php'); ?>
				
				<?php require('behavior.php'); ?>

				<?php do_action('fgp2wc_post_display_behavior_options'); ?>
				<?php require('actions.php'); ?>
				<?php require('progress-bar.php'); ?>
				<?php require('logger.php'); ?>
			</table>
		</form>
	</div>
	
	<?php require('extra-features.php'); ?>
	
</div>

		<form id="form_empty_wordpress_content" method="post">
			<?php wp_nonce_field( 'empty', 'fgp2wc_nonce' ); ?>
			
			<table class="form-table">
				<tr>
					<th scope="row"><?php _e('If you want to restart the import from scratch, you must empty the WordPress content with the button hereafter.', 'fg-prestashop-to-woocommerce'); ?></th>
					<td><input type="radio" name="empty_action" id="empty_action_imported" value="imported" /> <label for="empty_action_imported"><?php _e('Remove only previously imported data', 'fg-prestashop-to-woocommerce'); ?></label><br />
					<input type="radio" name="empty_action" id="empty_action_all" value="all" /> <label for="empty_action_all"><?php _e('Remove all WordPress content', 'fg-prestashop-to-woocommerce'); ?></label><br />
					<?php submit_button( __('Empty WordPress content', 'fg-prestashop-to-woocommerce'), 'primary', 'empty' ); ?></td>
				</tr>
			</table>
		</form>

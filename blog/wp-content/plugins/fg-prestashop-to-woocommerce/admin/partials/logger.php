				<tr>
					<td colspan="2">
						<?php _e('Log', 'fg-prestashop-to-woocommerce'); ?>
						<label id="label_logger_autorefresh"><input type="checkbox" name="logger_autorefresh" id="logger_autorefresh" value="1" <?php checked($data['logger_autorefresh'], 1); ?> /><?php _e('Log auto-refresh', 'fg-prestashop-to-woocommerce'); ?></label>
						<div id="logger"></div>
					</td>
				</tr>

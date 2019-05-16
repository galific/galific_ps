				<tr>
					<th scope="row"><?php _e('Automatic removal:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td><input id="automatic_empty" name="automatic_empty" type="checkbox" value="1" <?php checked($data['automatic_empty'], 1); ?> /> <label for="automatic_empty" ><?php _e('Automatically remove all the WordPress content before each import', 'fg-prestashop-to-woocommerce'); ?></label></td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h3><?php _e('PrestaShop web site parameters', 'fg-prestashop-to-woocommerce'); ?></h3></th>
				</tr>
				<tr>
					<th scope="row"><label for="url"><?php _e('URL of the live PrestaShop web site', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="url" name="url" type="text" size="50" value="<?php echo $data['url']; ?>" /><br />
						<small><?php _e('This field is used to pull the media off that site. It must contain the URL of the original site.', 'fg-prestashop-to-woocommerce'); ?></small>
					</td>
				</tr>
				<tr>
					<th scope="row" colspan="2"><h3><?php _e('PrestaShop database parameters', 'fg-prestashop-to-woocommerce'); ?></h3></th>
				</tr>
				<tr>
					<th scope="row"><label for="hostname"><?php _e('Hostname', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="hostname" name="hostname" type="text" size="50" value="<?php echo $data['hostname']; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="port"><?php _e('Port', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="port" name="port" type="text" size="50" value="<?php echo $data['port']; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="database"><?php _e('Database', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="database" name="database" type="text" size="50" value="<?php echo $data['database']; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="username"><?php _e('Username', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="username" name="username" type="text" size="50" value="<?php echo $data['username']; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="password"><?php _e('Password', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="password" name="password" type="password" size="50" value="<?php echo $data['password']; ?>" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="prefix"><?php _e('PrestaShop Table Prefix', 'fg-prestashop-to-woocommerce'); ?></label></th>
					<td><input id="prefix" name="prefix" type="text" size="50" value="<?php echo $data['prefix']; ?>" /></td>
				</tr>

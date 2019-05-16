				<tr>
					<th scope="row" colspan="2"><h3><?php _e('Behavior', 'fg-prestashop-to-woocommerce'); ?></h3></th>
				</tr>
				<tr><th><?php _e('SKU:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td>
						<input type="radio" name="sku" id="sku_reference" value="reference" <?php checked($data['sku'], 'reference', 1); ?> /><label for="sku_reference"><?php _e('Reference field', 'fg-prestashop-to-woocommerce'); ?></label>&nbsp;
						<input type="radio" name="sku" id="sku_ean13" value="ean13" <?php checked($data['sku'], 'ean13', 1); ?> /><label for="sku_ean13"><?php _e('EAN-13 field', 'fg-prestashop-to-woocommerce'); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Media:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td><input id="skip_media" name="skip_media" type="checkbox" value="1" <?php checked($data['skip_media'], 1); ?> /> <label for="skip_media" ><?php _e('Skip media', 'fg-prestashop-to-woocommerce'); ?></label>
					<br />
					<div id="media_import_box">
						<?php _e('Import first image:', 'fg-prestashop-to-woocommerce'); ?>&nbsp;
						<input id="first_image_as_is" name="first_image" type="radio" value="as_is" <?php checked($data['first_image'], 'as_is'); ?> /> <label for="first_image_as_is" title="<?php _e('The first image will be kept in the post content', 'fg-prestashop-to-woocommerce'); ?>"><?php _e('as is', 'fg-prestashop-to-woocommerce'); ?></label>&nbsp;&nbsp;
						<input id="first_image_as_featured" name="first_image" type="radio" value="as_featured" <?php checked($data['first_image'], 'as_featured'); ?> /> <label for="first_image_as_featured" title="<?php _e('The first image will be removed from the post content and imported as the featured image only', 'fg-prestashop-to-woocommerce'); ?>"><?php _e('as featured only', 'fg-prestashop-to-woocommerce'); ?></label>&nbsp;&nbsp;
						<input id="first_image_as_is_and_featured" name="first_image" type="radio" value="as_is_and_featured" <?php checked($data['first_image'], 'as_is_and_featured'); ?> /> <label for="first_image_as_is_and_featured" title="<?php _e('The first image will be kept in the post content and imported as the featured image', 'fg-prestashop-to-woocommerce'); ?>"><?php _e('as is and as featured', 'fg-prestashop-to-woocommerce'); ?></label>
						<br />
						<input id="image_size_thumbnail" name="image_size" type="radio" value="thumbnail" <?php checked($data['image_size'], 'thumbnail'); ?> /> <label for="image_size_thumbnail"><?php _e('Import the thumbnail product images', 'fg-prestashop-to-woocommerce'); ?></label>&nbsp;&nbsp;
						<input id="image_size_full" name="image_size" type="radio" value="full" <?php checked($data['image_size'], 'full'); ?> /> <label for="image_size_full"><?php _e('Import the full size product images', 'fg-prestashop-to-woocommerce'); ?></label>&nbsp;&nbsp;
						<br />
						<input id="import_external" name="import_external" type="checkbox" value="1" <?php checked($data['import_external'], 1); ?> /> <label for="import_external"><?php _e('Import external media', 'fg-prestashop-to-woocommerce'); ?></label>
						<br />
						<input id="import_duplicates" name="import_duplicates" type="checkbox" value="1" <?php checked($data['import_duplicates'], 1); ?> /> <label for="import_duplicates" title="<?php _e('Checked: download the media with their full path in order to import media with identical names.', 'fg-prestashop-to-woocommerce'); ?>"><?php _e('Import media with duplicate names', 'fg-prestashop-to-woocommerce'); ?></label>
						<br />
						<input id="force_media_import" name="force_media_import" type="checkbox" value="1" <?php checked($data['force_media_import'], 1); ?> /> <label for="force_media_import" title="<?php _e('Checked: download the media even if it has already been imported. Unchecked: Download only media which were not already imported.', 'fg-prestashop-to-woocommerce'); ?>" ><?php _e('Force media import. Keep unchecked except if you had previously some media download issues.', 'fg-prestashop-to-woocommerce'); ?></label>
						<br />
						<input id="first_image_not_in_gallery" name="first_image_not_in_gallery" type="checkbox" value="1" <?php checked($data['first_image_not_in_gallery'], 1, 1); ?> /> <label for="first_image_not_in_gallery"><?php _e("Don't include the first image into the product gallery", 'fg-prestashop-to-woocommerce'); ?></label>
						<br />
						<?php do_action('fgp2wc_post_display_medias_box', $data); ?>
						<?php _e('Timeout for each media:', 'fg-prestashop-to-woocommerce'); ?>&nbsp;
						<input id="timeout" name="timeout" type="text" size="5" value="<?php echo $data['timeout']; ?>" /> <?php _e('seconds', 'fg-prestashop-to-woocommerce'); ?>
					</div></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Import prices:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td>
						<input type="radio" name="price" id="price_without_tax" value="without_tax" <?php checked($data['price'], 'without_tax', 1); ?> /><label for="price_without_tax"><?php _e('excluding tax', 'fg-prestashop-to-woocommerce'); ?></label>&nbsp;
						<input type="radio" name="price" id="price_with_tax" value="with_tax" <?php checked($data['price'], 'with_tax', 1); ?> /><label for="price_with_tax"><?php _e('including tax <small>in this case, you must define a default tax rate before running the import</small>', 'fg-prestashop-to-woocommerce'); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Stock management:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td>
						<input id="stock_management" name="stock_management" type="checkbox" value="1" <?php checked($data['stock_management'], 1); ?> /> <label for="stock_management" ><?php _e('Enable stock management', 'fg-prestashop-to-woocommerce'); ?></label>
					</td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Meta keywords:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td><input id="meta_keywords_in_tags" name="meta_keywords_in_tags" type="checkbox" value="1" <?php checked($data['meta_keywords_in_tags'], 1); ?> /> <label for="meta_keywords_in_tags" ><?php _e('Import meta keywords as tags', 'fg-prestashop-to-woocommerce'); ?></label></td>
				</tr>
				<tr>
					<th scope="row"><?php _e('Create pages:', 'fg-prestashop-to-woocommerce'); ?></th>
					<td><input id="import_as_pages" name="import_as_pages" type="checkbox" value="1" <?php checked($data['import_as_pages'], 1); ?> /> <label for="import_as_pages" ><?php _e('Import the CMS as pages instead of posts (without categories)', 'fg-prestashop-to-woocommerce'); ?></label></td>
				</tr>

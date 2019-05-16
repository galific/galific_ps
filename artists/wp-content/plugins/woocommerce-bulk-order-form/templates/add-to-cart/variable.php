<?php
/**
 * @author  WooThemes
 * @package WC Bulk Order Form/Templates
 * @version 2.5.0
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }
global $product;
$attribute_keys = array_keys( $attributes );
?>

<div class="variations_form cart" method="post" enctype='multipart/form-data'
     data-formid="<?php echo $args['formid']; ?>"
	 data-product_id="<?php echo absint( $product->get_id() ); ?>" 
	 data-product_variations="<?php echo htmlspecialchars( json_encode( $available_variations ) ) ?>">

	<input type="hidden" name="wcbulkorder[wcbof_products][REPLACECOUNT][variation_id]" value="" class="variation_id" />
	
	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock">
			<?php _e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?>
		</p>
	<?php else : ?>
		<div class="variations"> 
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<div class="value" data-formid="<?php echo $args['formid']; ?>">
						<?php
							$selected = isset($_REQUEST['attribute_'.sanitize_title($attribute_name)]) ? wc_clean($_REQUEST[ 'attribute_'.sanitize_title($attribute_name)]) : $product->get_variation_default_attribute($attribute_name);
							wc_dropdown_variation_attribute_options( array( 
								'show_option_none' => wc_attribute_label($attribute_name),
								'options' => $options,
								'attribute' => $attribute_name, 
								'product' => $product, 
								'name'  => 'wcbulkorder[wcbof_products][REPLACECOUNT][attributes]['.$attribute_name.']',
								'id' => sanitize_title( $attribute_name.'_'.absint( $product->get_id() ) ),
								'selected' => $selected ) );
							echo end( $attribute_keys ) === $attribute_name ? apply_filters( 'woocommerce_reset_variations_link', '<a class="reset_variations" href="#">' . __( 'Clear', 'woocommerce' ) . '</a>' ) : '';
						?>
					</div> 
		        <?php endforeach;?> 
		</div>
		
	<?php endif; ?>
</div>




<tr data-formid="<?php echo $formid; ?>" id="wc_bof_product_<?php echo $count; ?>" class="wc_bof_product_list wc_bof_product_<?php echo $count; ?>" data-rowcount="<?php echo $count; ?>">
	
    <?php do_action('wc_bof_template_content_row_start',$count,$engine->attr); ?>
    
    <td data-count="<?php echo $count; ?>" class="wc_bof_product_name" id="wc_bof_product_name_<?php echo $count; ?>">
		<?php $engine->get_product_column($count); ?>
	</td>

	<?php if($is_variation) : ?>
	<td class="wc_bof_variation_name" id="wc_bof_variation_name_<?php echo $count; ?>" data-count="<?php echo $count; ?>">
        <?php $engine->get_variation_column($count); ?>
	</td>
	<?php endif;?>

	<td class="wc_bof_product_qty" id="wc_bof_product_qty_<?php echo $count; ?>" data-count="<?php echo $count; ?>" >
		<?php $engine->get_quantity_column($count,$currency_symbol); ?>
	</td>
	
	<?php if($price) : ?> 
    <td data-count="<?php echo $count; ?>" class="wc_bof_product_price" id="wc_bof_product_price_<?php echo $count; ?>">
        <?php $engine->get_price_column($count); ?>
    </td> 
	<?php endif;?>
    
    <?php do_action('wc_bof_template_content_row_end',$count,$engine->attr); ?>
</tr>
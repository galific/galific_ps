<table class="wcbulkordertable wcbulkorderproducttbl" id="wcbulkorderproducttbl">
    <thead>
        <tr>
            <td><?php echo $product_label; ?></td>

            <?php if($is_variation) : ?>
                <td><?php echo $variation_label; ?></td>
            <?php endif;?>

            <td><?php echo $quantity_label; ?></td>

            <?php if($price) : ?>
                <td><?php echo $price_label; ?></td>
            <?php endif;?>

            <?php do_action('wc_bof_template_content_headers',$engine->attr); ?>
        </tr>
    </thead>
    <tbody class="list">

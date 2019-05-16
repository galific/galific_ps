    </tbody>

    <tfoot id="wcbulkordertotaltbl" class="wcbulkordertotaltbl">
        <?php if( ($is_variation === true  || $is_standard === true ) && ($price == 'on')) : ?>
            <tr id="wcbulkordertotals" class="wcbulkordertotals">
                <td class="wc_bof_pricetotalhead" colspan=""><?php echo $total_label; ?> :</td>
                <td class="wc_bof_pricetotal"><span class="wcbulkorderalltotal"></span></td>

            </tr>
        <?php endif; ?>

        <tr id="wcbulkorderactions" class="wcbulkorderactions">
            <td>
                <?php do_action('wc_bof_template_footer_actions',$engine->attr); ?>
            </td>
            <td>
                <button data-formid="<?php echo $formid; ?>" type="submit" class="wcbofaddtocart button" id="wcbofaddtocart_<?php echo $formid; ?>"><?php echo $cart_label; ?></button>
            </td>
        </tr>
    </tfoot>
</table> 
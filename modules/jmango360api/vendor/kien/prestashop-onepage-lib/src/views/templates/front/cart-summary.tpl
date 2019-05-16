{**
* @license Created by JMango
*}

<div id="checkout-review-table-wrapper">
        <table class="data-table" id="checkout-review-table">
            <tbody>

            {foreach $products as $product}
                <tr class="jm-item">
                    <td class="jm-item-img">
                        <div class="jm-item-img-inner">
                            <span class="jm-item-qty">{$product.cart_quantity}</span>
                            <img src='{$link->getImageLink($product.link_rewrite, $product.id_image, 'large_default')|escape:"html":"UTF-8"}' alt=""/>
                        </div>
                    </td>
                    <td class="jm-item-detail">
                        <span class="jm-item-name">{$product.name|escape:'html':'UTF-8'}</span>
                        <span class="jm-item-desc">
                    <span class="jm-item-desc-item">
                        {if $product.reference}<small>{l s='SKU' d=$module_name} : {$product.reference|escape:'html':'UTF-8'}</small>{/if}
                    </span>
                    <span class="jm-item-desc-item">
                        {if isset($product.attributes) && $product.attributes}<small>{$product.attributes|escape:'html':'UTF-8'}</small>{/if}
                    </span>
                </span>
                    </td>
                    <td class="jm-item-price">
                        <div>
                    <span class="cart-price">
                        <span class="price">
                            {if !$priceDisplay}{displayPrice price=$product.total_wt}{else}{displayPrice price=$product.total}{/if}
                        </span>
                    </span>
                        </div>
                    </td>
                </tr>
            {/foreach}

            </tbody>
            <tfoot>
            <tr>
                {if $use_taxes}
                    {if $priceDisplay}
                        <td style="" class="a-right" colspan="2">{if $display_tax_label}{l s='Total products (tax excl.)' d=$module_name}{else}{l s='Total products' d=$module_name}{/if}</td>
                        <td style="" class="a-right">
                            <span class="price">{displayPrice price=$total_products}</span>
                        </td>
                    {else}
                        <td style="" class="a-right" colspan="2">{if $display_tax_label}{l s='Total products (tax incl.)' d=$module_name}{else}{l s='Total products' d=$module_name}{/if}</td>
                        <td style="" class="a-right"><span class="price">{displayPrice price=$total_products_wt}</span></td>
                    {/if}
                {else}
                    <td style="" class="a-right" colspan="2">{l s='Total products' d=$module_name}</td>
                    <td style="" class="a-right">
                        <span class="price">{displayPrice price=$total_products}</span>
                    </td>
                {/if}
            </tr>
            <tr>
                {if $total_shipping_tax_exc <= 0 && (!isset($isVirtualCart) || !$isVirtualCart) && $free_ship}
                    <td style="" class="a-right" colspan="2">
                        {l s='Total shipping' d=$module_name}
                    </td>
                    <td style="" class="a-right">
                        <span class="price">{l s='Free Shipping' d=$module_name}</span>
                    </td>
                {else}
                    {if $use_taxes && $total_shipping_tax_exc != $total_shipping}
                        {if $priceDisplay}
                            <td style="" class="a-right" colspan="2">
                                {if $display_tax_label}{l s='Total shipping (tax excl.)' d=$module_name}{else}{l s='Total shipping' d=$module_name}{/if}
                            </td>
                            <td style="" class="a-right">
                                <span class="price">{displayPrice price=$total_shipping_tax_exc}</span>
                            </td>
                        {else}
                            <td style="" class="a-right" colspan="2">
                                {if $display_tax_label}{l s='Total shipping (tax incl.)' d=$module_name}{else}{l s='Total shipping' d=$module_name}{/if}
                            </td>
                            <td style="" class="a-right">
                                <span class="price">{displayPrice price=$total_shipping}</span>
                            </td>
                        {/if}
                    {else}
                        <td style="" class="a-right" colspan="2">
                            {l s='Total shipping' d=$module_name}
                        </td>
                        <td style="" class="a-right">
                            <span class="price">{displayPrice price=$total_shipping_tax_exc}</span>
                        </td>
                    {/if}
                {/if}
            </tr>


            <tr>
                <td style="{if $total_discounts == 0}display:none{/if}" class="a-right" colspan="2">
                    {if $display_tax_label}
                        {if $use_taxes && $priceDisplay == 0}
                            {l s='Total vouchers (tax incl.)' d=$module_name}
                        {else}
                            {l s='Total vouchers (tax excl.)' d=$module_name}
                        {/if}
                    {else}
                        {l s='Total vouchers' d=$module_name}
                    {/if}
                </td>
                <td style="{if $total_discounts == 0}display:none{/if}" class="a-right">
            <span class="price">
                {if $use_taxes && $priceDisplay == 0}
                    {assign var='total_discounts_negative' value=$total_discounts * -1}
                {else}
                    {assign var='total_discounts_negative' value=$total_discounts_tax_exc * -1}
                {/if}
                {displayPrice price=$total_discounts_negative}
            </span>
                </td>
            </tr>


            {if $use_taxes && $show_taxes && $total_tax != 0 }
                {if $priceDisplay != 0}
                    <tr>
                        <td style="" class="a-right" colspan="2">
                            <strong>{if $display_tax_label}{l s='Total (tax excl.)' d=$module_name}{else}{l s='Total' d=$module_name}{/if}</strong>
                        </td>
                        <td style="" class="a-right">
                            <strong><span class="price">{displayPrice price=$total_price_without_tax}</span></strong>
                        </td>
                    </tr>
                {/if}
                <tr>
                    <td style="" class="a-right" colspan="2">
                        <strong>{l s='Tax' d=$module_name}</strong>
                    </td>
                    <td style="" class="a-right">
                        <strong><span class="price">{displayPrice price=$total_tax}</span></strong>
                    </td>
                </tr>
            {/if}

            <tr>
                <td style="" class="a-right" colspan="2">
                    <strong>{l s='Total' d=$module_name}</strong>
                    {*<div class="hookDisplayProductPriceBlock-price" id="HOOK_DISPLAY_PRODUCT_PRICE_BLOCK">*}
                    {*{hook h="displayCartTotalPriceLabel"}*}
                    {*</div>*}
                </td>
                {if $use_taxes}
                    <td style="" class="a-right">
                        <strong><span class="price">{displayPrice price=$total_price}</span></strong>
                    </td>
                {else}
                    <td style="" class="a-right">
                        <strong><span class="price">{displayPrice price=$total_price_without_tax}</span></strong>
                    </td>
                {/if}
            </tr>
            </tfoot>
        </table>
    </div>
    <div id="checkout-review-submit">
        {if $conditions && $cms_id}
            <form action="" id="checkout-agreements" class="checkout-agreements" onsubmit="return false;">
                <dl class="sp-methods">
                    <dt>
                        <input type="checkbox" id="agreement-1" name="agreement[1]" value="1" title="Terms &amp; Conditions" class="checkbox" {if $checkedTOS}checked="checked"{/if} >
                        <label for="agreement-1">
                            {l s='I agree to the terms of service and will adhere to them unconditionally.' d=$module_name}
                            <a href="#" data-toggle="modal" data-target="#agreementModal1" class="agreement-label"> {l s='(Read the Terms of Service)' d=$module_name}</a>
                        </label>

                    <div id="agreementModal1" class="modal fade modal-agreement" tabindex="-1" role="dialog" aria-labelledby="agreementModalLabel1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='$("#agreementModal1").modal("hide");'>
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                    <h1 class="modal-title" id="agreementModalLabel1">
                                        {l s='Terms of Service' d=$module_name}
                                    </h1>
                                </div>
                                <div class="modal-body">

                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' d=$module_name}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    </dt>
                </dl>
                <script>
                    var $modal = $('#agreementModal1');
                    // Show loader & then get content when modal is shown
                    $modal.on('show.bs.modal', function(e) {
                        $(this)
                            .find('.modal-body')
                            .html('<p class="saving">{l s='Loading' d=$module_name js=1}<span>.</span><span>.</span><span>.</span></p>')
                            .load('{$link_conditions} #center_column', function() {
                                // Use Bootstrap's built-in function to fix scrolling (to no avail)
                                $modal.modal('handleUpdate');
                            });
                    });
                </script>
            </form>
        {/if}

        <form id="co-coupon-form" action="" method="post" style="{if $enable_coupon_onepage === '0'}display: none;{/if}">
            <dl class="sp-methods">
                <dt>{l s='Vouchers' d=$module_name}
                    <a href="#coupon-content" data-toggle="collapse" role="button" aria-expanded="false" aria-controls="coupon-content" class="section-control collapsed">
                        <i class="icomoon-pencil"></i>
                    </a>
                </dt>
                <dd>
                    <ul class="form-list" id="coupon-content" aria-expanded="false" style="height: auto;">
                        <li>
                            {if sizeof($discounts)}
                                <table>
                                    <tbody>
                                    {foreach $discounts as $discount}
                                        {if (float)$discount.value_real == 0}
                                            {continue}
                                        {/if}
                                        <tr>
                                            <td style="" class="a-right">{$discount.name|escape:'html':'UTF-8'}</td>
                                            <td style="" class="a-right" colspan="2">
                                                {if strlen($discount.code)}
                                                    <i class="icon-trash" onclick="coupon.remove('{$discount.id_discount|escape:'html':'UTF-8'}')"></i>
                                                {/if}
                                            </td>
                                        </tr>
                                    {/foreach}
                                    </tbody>
                                </table>
                            {/if}
                        </li>
                        <li>
                            <label for="coupon_code">{l s='Enter your voucher code if you have one.' d=$module_name}</label>
                            <input type="hidden" name="remove" id="remove-coupone" value="0">
                            <div class="input-box">
                                <input class="input-text" id="coupon_code" name="coupon_code" type="text" value="">
                            </div>
                            <div class="buttons-set">
                                <button type="button" class="ladda-button" data-color="jmango" data-size="xs" data-style="slide-up" onclick="coupon.save(false)" style="width:auto;" id="coupon-button" title="Apply Coupon" value="Apply Coupon">
                                    <span class="ladda-label">{l s='Apply' d=$module_name}</span></button>
                            </div>
                        </li>
                    </ul>
                </dd>
            </dl>
        </form>


        <div id="paymentModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick='$("#paymentModal").modal("hide");'>
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h1 class="modal-title" id="paymentModalLabel">
                            {l s='Payment Method' d=$module_name}
                        </h1>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button id="payment_dialog_close" type="button" class="btn btn-default" data-dismiss="modal">{l s='Close' d=$module_name}</button>
                        <button id="payment_dialog_proceed" type="button" class="btn btn-primary">{l s='Proceed' d=$module_name}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="buttons-set" id="review-buttons-container">
        <button type="button" id="review-button" class="ladda-button" data-color="jmango" data-style="slide-up" onclick="review.save()" data-size="s">
            <span class="ladda-label">{l s='Place Order' d=$module_name}</span>
            <span class="ladda-spinner"></span></button>
        <div class="ladda-progress" style="width: 0px;"></div>
        </button>
    </div>
</div>

<script type="text/javascript">
        var conditionEnabled = {$conditions|intval};
</script>

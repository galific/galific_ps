{**
* @license Created by JMango
*}

{if isset($empty)}
    <p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='jmango360api'}</p>
{else}
{if $custom_css}
    <style type="text/css">{$custom_css}</style>
{/if}
{if $custom_js}
    <script type="text/javascript">{$custom_js}</script>
{/if}
<div class="main-container">
    <div class="col-main">
        <ul class="opc" id="checkoutSteps">
            <li id="opc-billing" class="panel section {if selected_invoice_address_id}allow{/if}">
                <div class="step-title" role="tab">
                    <div class="step-number-wrapper">
                        <span class="step-number">1</span>
                    </div>
                    <h2>{l s='Billing Address' mod='jmango360api'}</h2>
                    <a href="#checkout-step-billing" data-toggle="collapse"
                       role="button" data-parent="#checkoutSteps"
                       aria-expanded="false" aria-controls="checkout-step-billing"
                       class="section-control collapsed">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-billing" role="tabpanel" class="step a-item collapse"
                     style="height: 0px;" aria-expanded="false">
                    <form id="co-billing-form"
                          action="{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'htmlall':'UTF-8'}">
                        <fieldset>
                            <ul class="form-list">
                                {if $is_logged}
                                    <li class="wide">
                                        <label for="billing-address-select">
                                            {l s='Select a billing address from your address book or enter a new address.'
                                            mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <select name="billing_address_id" id="billing-address-select"
                                                    class="address-select validation-passed" title=""
                                                    onchange="billing.formUpdater.newAddress(!this.value)">
                                                {if $selected_invoice_address_id}
                                                    <option name="create_new" value="">
                                                        {l s='Add a new address...' mod='jmango360api'}</option>
                                                    {*<option name="{$current_country|intval}" value="">{l s='Add a new address...' mod='jmango360api'}</option>*}
                                                {else}
                                                    <option name="create_new" value="">
                                                        {l s='Add a new address...' mod='jmango360api'}
                                                    </option>
                                                    {*<option name="{$current_country|intval}" value="">
                                                         {l s='Add a new address...' mod='jmango360api'}
                                                    </option>*}
                                                {/if}
                                                {foreach from=$addresses key=k item=address}
                                                    <option name="{$address.id_country|intval}"
                                                            value="{$address.id_address|intval}"
                                                            {if $address.id_address == $selected_invoice_address_id}
                                                                selected="selected"
                                                            {/if}>
                                                        {$address.alias|escape:'html':'UTF-8'}
                                                    </option>
                                                {/foreach}
                                            </select>
                                            <div class="japi-address-edit" style="">
                                                <a href="#" class="japi-address-edit-btn"
                                                   id="billing-address-edit-btn">
                                                    {l s='Edit' mod='jmango360api'}
                                                </a>
                                                <input type="hidden" name="billing[edit]" value="0" id="billing:edit">
                                            </div>
                                        </div>
                                    </li>
                                {else}
                                    {if $customer}
                                        <select name="billing_address_id" id="billing-address-select"
                                                class="address-select validation-passed" title="" style="display:none" ">
                                            {foreach from=$addresses key=k item=address}
                                                <option value="{$address.id_address|intval}"
                                                        {if $address.id_address == $selected_invoice_address_id}
                                                            selected="selected"
                                                        {/if}>
                                                    {$address.alias|escape:'html':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                {/if}


                                <li id="billing-new-address-form">
                                     {include file="$template_dir./front/billing-address-form.tpl"}
                                </li>


                                <li class="control">
                                    <input type="radio" name="billing[use_for_shipping]"
                                           id="billing:use_for_shipping_yes" value="1"
                                           checked="checked" title="Ship to this address"
                                           onclick="$('#shipping\\:same_as_billing').prop('checked', true);" class="radio">
                                    <label for="billing:use_for_shipping_yes">
                                        {l s='Ship to this address' mod='jmango360api'}
                                    </label>
                                </li>
                                <li class="control">
                                    <input type="radio" name="billing[use_for_shipping]"
                                           id="billing:use_for_shipping_no" value="0"
                                           title="Ship to different address"
                                           onclick="$('#shipping\\:same_as_billing').prop('checked', false);"
                                           class="radio">
                                    <label for="billing:use_for_shipping_no">
                                        {l s='Ship to different address' mod='jmango360api'}
                                    </label>
                                </li>
                            </ul>
                            <div class="buttons-set" id="billing-buttons-container">
                                <button id="billing-button" type="button"
                                        title="Continue" class="ladda-button"
                                        onclick="billing.save()" data-color="jmango"
                                        data-style="slide-up" data-size="s">
                                    <span class="ladda-label">
                                        {l s='Continue' mod='jmango360api'}
                                    </span>
                                    <span class="ladda-spinner"></span></button>
                                    <div class="ladda-progress" style="width: 0px;"></div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </li>


            <li id="opc-shipping" class="panel section {if selected_delivery_address_id}allow{/if}">
                <div class="step-title" role="tab">
                    <div class="step-number-wrapper">
                        <span class="step-number">2</span>
                    </div>
                    <h2>{l s='Delivery Address' mod='jmango360api'}</h2>
                    <a href="#checkout-step-shipping" data-toggle="collapse" role="button"
                       data-parent="#checkoutSteps" aria-controls="checkout-step-shipping"
                       class="section-control collapsed" aria-expanded="false">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-shipping" role="tabpanel" class="step a-item collapse"
                     style="height: 0px;" aria-expanded="false">
                    <form action="{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'htmlall':'UTF-8'}"
                          id="co-shipping-form">
                        <ul class="form-list">

                            {if $is_logged}
                                <li class="wide">
                                    <label for="shipping-address-select">
                                        {l s='Select a delivery address from your address book or enter a new address.'
                                        mod='jmango360api'}
                                    </label>
                                    <div class="input-box">
                                        <select name="shipping_address_id" id="shipping-address-select"
                                                class="address-select validation-passed"
                                                title="" onchange="shipping.newAddress(!this.value)">
                                            {if $selected_delivery_address_id}
                                                <option name="create_new" value="">
                                                    {l s='Add a new address...' mod='jmango360api'}
                                                </option>
                                            {else}
                                                <option name="create_new" value="" selected="selected">
                                                    {l s='Add a new address...' mod='jmango360api'}
                                                </option>
                                            {/if}
                                            {foreach from=$addresses key=k item=address}
                                                <option value="{$address.id_address|intval}"
                                                        {if $address.id_address == $selected_delivery_address_id}
                                                            selected="selected"
                                                        {/if}>
                                                    {$address.alias|escape:'html':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                        <div class="japi-address-edit" style="">
                                            <a href="#" class="japi-address-edit-btn"
                                               id="shipping-address-edit-btn">
                                                {l s='Edit' mod='jmango360api'}
                                            </a>
                                            <input type="hidden" name="shipping[edit]" value="0" id="shipping:edit"/>
                                        </div>
                                    </div>
                                </li>
                            {else}
                                {*{if isset($customer) && $customer.is_guest != 1}*}
                                    {*<select name="shipping_address_id" id="shipping-address-select"*}
                                            {*class="address-select validation-passed" title="" style="display:none" ">*}
                                    {*{foreach from=$addresses key=k item=address}*}
                                        {*<option name="{$address.id_country|intval}"*}
                                                {*value="{$address.id_address|intval}"*}
                                                {*{if $address.id_address == $selected_delivery_address_id}*}
                                                    {*selected="selected"*}
                                                {*{/if}>*}
                                            {*{$address.alias|escape:'html':'UTF-8'}*}
                                        {*</option>*}
                                    {*{/foreach}*}
                                    {*</select>*}
                                {*{/if}*}
                            {/if}
                            <li id="shipping-new-address-form">
                                {include file="$template_dir./front/shipping-address-form.tpl"}
                            </li>
                            <li class="control">
                                <input type="checkbox" name="shipping[same_as_billing]"
                                       id="shipping:same_as_billing" value="1"
                                       checked="" title="Use Billing Address"
                                       onclick="shipping.setSameAsBilling(this.checked)" class="checkbox">
                                <label for="shipping:same_as_billing">
                                    {l s='Use the delivery address as the billing address.' mod='jmango360api'}
                                </label>
                            </li>
                            <div id="" class="form-group">
                                <label>
                                    {l s='If you would like to add a comment about your order, please write it in the field below.'
                                    mod='jmango360api'}
                                </label>
                                <textarea class="form-control" cols="60" rows="6" name="message_box">{if isset($oldMessage)}{$oldMessage|escape:'html':'UTF-8'}{/if}</textarea>
                            </div>
                        </ul>
                        <div class="buttons-set" id="shipping-buttons-container">
                            <p class="required">* Required Fields</p>
                            <p class="back-link">
                                <a href="http://preview-store.jmango360.com/japi/checkout/onepage/#"
                                   onclick="checkout.back(); return false;">
                                    <small>« </small>Back</a>
                            </p>
                            <button id="shipping-button" type="button" class="ladda-button" data-color="jmango"
                                    data-style="slide-up" data-size="s" title="Continue" onclick="shipping.save()">
                                <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
                                <span class="ladda-spinner"></span>
                                <div class="ladda-progress" style="width: 0px;"></div>
                            </button>

                        </div>
                    </form>
                </div>
            </li>
            <li id="opc-shipping_method" class="panel section ">
                <div class="step-title" role="tab">
                    <div class="step-number-wrapper">
                        <span class="step-number">3</span>
                    </div>
                    <h2>{l s='Shipping' mod='jmango360api'}</h2>
                    <a href="#checkout-step-shipping_method" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-controls="checkout-step-shipping_method" class="section-control" aria-expanded="true">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-shipping_method" role="tabpanel" class="step a-item collapse" style="" aria-expanded="true">
                    {include file="$template_dir./front/shipping-methods.tpl"}
                </div>
            </li>
            <li id="opc-payment" class="panel section ">
                <div class="step-title" role="tab">
                    <div class="step-number-wrapper">
                        <span class="step-number">4</span>
                    </div>
                    <h2>{l s='Payment' mod='jmango360api'}</h2>
                    <a href="#checkout-step-payment" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-controls="checkout-step-payment" class="section-control collapsed" aria-expanded="false">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-payment" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                    {include file="$template_dir./front/payment-methods.tpl"}
                </div>
            </li>
            <li id="opc-review" class="panel section ">
                <div class="step-title" role="tab">
                    <div class="step-number-wrapper">
                        <span class="step-number">5</span>
                    </div>
                    <h2>{l s='Summary' mod='jmango360api'}</h2>
                    <a href="#checkout-step-review" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-controls="checkout-step-review" class="section-control collapsed" aria-expanded="false">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-review" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                    <div class="order-review" id="checkout-review-load">
                        {include file="$template_dir./front/cart-summary.tpl"}
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    var newAddressLabel = "{l s='Add a new address...' mod='jmango360api'}";
    var TOSMsg = "{l s='You must agree to the terms of service before continuing.' mod='jmango360api' js=1}";
    var networkErrorMsg = "{l s='No internet connection, please try again' mod='jmango360api' js=1}";
    var unknownErrorMsg = "{l s='Unknown error.' mod='jmango360api' js=1}";
    var loadingMsg = "{l s='Loading' mod='jmango360api' js=1}";
    var hasReload = "{$has_reload|json_encode}";
    var isLogged = {$is_logged|intval};
    var default_country = {$default_country|escape:'htmlall':'UTF-8'};
    var countries = {$countries|json_encode};
    var selectedBillingAddress = {$selected_invoice_address_id|intval};
    var selectedShippingAddress = {$selected_delivery_address_id|intval};
    var current_country = {$current_country|intval};
    var my_address = "  {l s='My address' mod='jmango360api' js=1}";
    var message_alert_required = {$message_alert_required|json_encode};
    var ones_phone_at_least = {$one_phone_at_least|intval};
    var langCode = "{$lang_code|escape:'htmlall':'UTF-8'}";
    var addresses = {$addresses|json_encode};
    // console.log(addresses);
    var customer = {$customer|json_encode};

    document.addEventListener("DOMContentLoaded", function () {
        opc_accordion = new Accordion('checkoutSteps', '.step-title', true);
        checkout = new Checkout(opc_accordion);
        billing = new Billing('co-billing-form',
            "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}",
            customer,
            addresses,
            countries,
            current_country);
        shipping = new Shipping('co-shipping-form',
            "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}"
            , customer
            , addresses
            , countries
            , current_country);
        //Start handle billing
        billing.start();
        shipping.start();
        idSelectedState = {$idSelectedState|intval};
        idSelectedInvoiceState = {$idSelectedInvoiceState|intval};
        shippingMethod = new ShippingMethod('co-shipping-method-form', "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}");
        paymentMethod = new PaymentMethod('co-payment-form', "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}");
        review = new Review('co-payment-form', 'checkout-agreements', "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}");
        coupon = new Coupon('co-coupon-form', "{$linkJm->getModuleLink("jmango360api", "jmcheckout")|escape:'quotes':'UTF-8'}");
        //new JMAgreement('checkout-agreements');
    });

  //]]>
</script>
{/if}

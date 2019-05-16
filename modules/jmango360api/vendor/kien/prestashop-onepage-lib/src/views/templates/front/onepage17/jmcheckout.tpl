{**
* @license Created by JMango
*}
<!DOCTYPE html>
<html>
<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
    {literal}
        <style type="text/css">
            body {
                padding: 15px;
            }
        </style>
    {/literal}
    {if $custom_css}
        <style type="text/css">{$custom_css}</style>
    {/if}
    {if $custom_js}
        <script type="text/javascript">{$custom_js}</script>
    {/if}
</head>
<body>
{if isset($empty)}
    <p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='jmango360api'}</p>
{else}
    <div class="main-container" id="content">
        <div class="col-main">
            <ol class="opc" id="checkoutSteps">
                <li id="opc-personal-information" class="panel section allow" style="{if $is_logged}display:none;{/if}">
                    <div class="step-title" role="tab">
                        <div class="step-number-wrapper">
                            <span class="step-number">0</span>
                        </div>
                        <h2>{l s='Personal Information' mod='jmango360api'}</h2>
                        <a href="#checkout-step-personal-information" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-expanded="false" aria-controls="checkout-step-billing" class="section-control collapsed">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                    <div id="checkout-step-personal-information" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                        <form id="co-personal-information-form" action="">
                            <fieldset>
                                <ul class="form-list">
                                    {render file="$template_dir/front/onepage17/_partials/customer-form.tpl" ui=$register_form guest_allowed=$guest_allowed}
                                </ul>
                                <div class="buttons-set" id="personal-information-buttons-container">
                                    <button id="personal-information-button" type="button" title="Continue" class="ladda-button" onclick="personalInformation.save();" data-color="jmango" data-style="slide-up" data-size="s">
                                        <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
                                        <span class="ladda-spinner"></span></button>
                                    <div class="ladda-progress" style="width: 0px;"></div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </li>
                <li id="opc-billing" class="panel section allow">
                    <div class="step-title" role="tab">
                        <div class="step-number-wrapper">
                            <span class="step-number">1</span>
                        </div>
                        <h2>{l s='Billing Address' mod='jmango360api'}</h2>
                        <a href="#checkout-step-billing" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-expanded="false" aria-controls="checkout-step-billing" class="section-control collapsed">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                    <div id="checkout-step-billing" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                        <form id="co-billing-form" action="">
                            <fieldset>
                                <ul class="form-list">
                                    {if $is_logged}
                                        <li class="wide">
                                            <label for="billing-address-select">
                                                {l s='Select a billing address from your address book or enter a new address.' mod='jmango360api'}
                                            </label>
                                            <div class="input-box">
                                                <select name="billing_address_id" id="billing-address-select" class="address-select validation-passed" title="" onchange="">
                                                    {if $selected_invoice_address_id}
                                                        <option value="">{l s='Add a new address...' mod='jmango360api'}</option>
                                                    {else}
                                                        <option value="" selected="selected">{l s='Add a new address...' mod='jmango360api'}</option>
                                                    {/if}
                                                    {foreach from=$addresses key=k item=address}
                                                        <option value="{$address.id_address|intval}" {if $address.id_address == $selected_invoice_address_id} selected="selected"{/if}>
                                                            {$address.firstname|escape:'html':'UTF-8'} {$address.lastname|escape:'html':'UTF-8'}{if $address.address1}, {$address.address1|escape:'html':'UTF-8'}{/if}{if $address.address2}, {$address.address2|escape:'html':'UTF-8'}{/if}{if $address.city}, {$address.city|escape:'html':'UTF-8'}{/if}{if $address.postcode}, {$address.postcode|escape:'html':'UTF-8'}{/if}{if $address.phone}, {$address.phone|escape:'html':'UTF-8'}{/if}{if $address.phone_mobile}, {$address.phone_mobile|escape:'html':'UTF-8'}{/if}
                                                        </option>
                                                    {/foreach}
                                                </select>
                                                <div class="japi-address-edit" id="japi-billing-address-edit" style="">
                                                    <a href="#" class="japi-address-edit-btn" id="billing-address-edit-btn">{l s='Edit' mod='jmango360api'}</a>
                                                    <input type="hidden" name="billing[edit]" value="0" id="billing:edit">
                                                </div>
                                            </div>
                                        </li>
                                    {else}
                                        {if $customer}
                                            <select name="billing_address_id" id="billing-address-select" class="address-select validation-passed" title="" style="display:none" ">
                                            {foreach from=$addresses key=k item=address}
                                                <option value="{$address.id_address|intval}" {if $address.id_address == $selected_invoice_address_id} selected="selected"{/if}>
                                                    {$address.firstname|escape:'html':'UTF-8'} {$address.lastname|escape:'html':'UTF-8'}{if $address.address1}, {$address.address1|escape:'html':'UTF-8'}{/if}{if $address.address2}, {$address.address2|escape:'html':'UTF-8'}{/if}{if $address.city}, {$address.city|escape:'html':'UTF-8'}{/if}{if $address.postcode}, {$address.postcode|escape:'html':'UTF-8'}{/if}{if $address.phone}, {$address.phone|escape:'html':'UTF-8'}{/if}{if $address.phone_mobile}, {$address.phone_mobile|escape:'html':'UTF-8'}{/if}
                                                </option>
                                            {/foreach}
                                            </select>
                                        {/if}
                                    {/if}

                                    <li id="billing-new-address-form" {if $is_logged}style="display: none"{/if}>
                                        <div id="billing-address">
                                            {render file              = "$template_dir/front/onepage17/_partials/address-form.tpl"
                                            ui                        = $address_form
                                            type                      = "invoice"
                                            }
                                        </div>
                                    </li>
                                    <li class="control">
                                        <input type="radio" name="use_for_shipping" id="use_for_shipping_yes" value="1" checked="checked" title="Ship to this address" onclick="$('#shipping\\:same_as_billing').prop('checked', true);" class="radio">
                                        <label for="use_for_shipping_yes">{l s='Ship to this address' mod='jmango360api'}</label>
                                    </li>
                                    <li class="control">
                                        <input type="radio" name="use_for_shipping" id="use_for_shipping_no" value="0" title="Ship to different address" onclick="$('#shipping\\:same_as_billing').prop('checked', false);" class="radio">
                                        <label for="use_for_shipping_no">{l s='Ship to different address' mod='jmango360api'}</label>
                                    </li>
                                </ul>
                                <div class="buttons-set" id="billing-buttons-container">
                                    <button id="billing-button" type="button" title="Continue" class="ladda-button" onclick="billing.save();" data-color="jmango" data-style="slide-up" data-size="s">
                                        <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
                                        <span class="ladda-spinner"></span></button>
                                    <div class="ladda-progress" style="width: 0px;"></div>
                                </div>
                            </fieldset>
                        </form>
                    </div>
                </li>
                <li id="opc-shipping" class="panel section allow">
                    <div class="step-title" role="tab">
                        <div class="step-number-wrapper">
                            <span class="step-number">2</span>
                        </div>
                        <h2>{l s='Delivery Address' mod='jmango360api'}</h2>
                        <a href="#checkout-step-shipping" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-controls="checkout-step-shipping" class="section-control collapsed" aria-expanded="false">
                            <i class="fa fa-pencil"></i>
                        </a>
                    </div>
                    <div id="checkout-step-shipping" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                        <form id="co-shipping-form" action="">
                            <fieldset>
                                <ul class="form-list">
                                    {if $is_logged}
                                        <li class="wide">
                                            <label for="shipping-address-select">
                                                {l s='Select a delivery address from your address book or enter a new address.' mod='jmango360api'}
                                            </label>
                                            <div class="input-box">
                                                <select name="shipping_address_id" id="shipping-address-select" class="address-select validation-passed" onchange="">
                                                    {if $selected_delivery_address_id}
                                                        <option value="">{l s='Add a new address...' mod='jmango360api'}</option>
                                                    {else}
                                                        <option value="" selected="selected">{l s='Add a new address...' mod='jmango360api'}</option>
                                                    {/if}
                                                    {foreach from=$addresses key=k item=address}
                                                        <option value="{$address.id_address|intval}" {if $address.id_address == $selected_delivery_address_id} selected="selected"{/if}>
                                                            {$address.firstname|escape:'html':'UTF-8'} {$address.lastname|escape:'html':'UTF-8'}{if $address.address1}, {$address.address1|escape:'html':'UTF-8'}{/if}{if $address.address2}, {$address.address2|escape:'html':'UTF-8'}{/if}{if $address.city}, {$address.city|escape:'html':'UTF-8'}{/if}{if $address.postcode}, {$address.postcode|escape:'html':'UTF-8'}{/if}{if $address.phone}, {$address.phone|escape:'html':'UTF-8'}{/if}{if $address.phone_mobile}, {$address.phone_mobile|escape:'html':'UTF-8'}{/if}
                                                        </option>
                                                    {/foreach}
                                                </select>
                                                <div class="japi-address-edit" id="japi-shipping-address-edit" style="">
                                                    <a href="#" class="japi-address-edit-btn" id="shipping-address-edit-btn">{l s='Edit' mod='jmango360api'}</a>
                                                    <input type="hidden" name="shipping[edit]" value="0" id="shipping:edit">
                                                </div>
                                            </div>
                                        </li>
                                    {else}
                                        {if $customer}
                                            <select name="shipping_address_id" id="shipping-address-select" class="address-select validation-passed" title="" style="display:none" ">
                                            {foreach from=$addresses key=k item=address}
                                                <option value="{$address.id_address|intval}" {if $address.id_address == $selected_delivery_address_id} selected="selected"{/if}>
                                                    {$address.firstname|escape:'html':'UTF-8'} {$address.lastname|escape:'html':'UTF-8'}{if $address.address1}, {$address.address1|escape:'html':'UTF-8'}{/if}{if $address.address2}, {$address.address2|escape:'html':'UTF-8'}{/if}{if $address.city}, {$address.city|escape:'html':'UTF-8'}{/if}{if $address.postcode}, {$address.postcode|escape:'html':'UTF-8'}{/if}{if $address.phone}, {$address.phone|escape:'html':'UTF-8'}{/if}{if $address.phone_mobile}, {$address.phone_mobile|escape:'html':'UTF-8'}{/if}
                                                </option>
                                            {/foreach}
                                            </select>
                                        {/if}
                                    {/if}
                                    <li id="shipping-new-address-form">
                                        <div id="shipping-address">
                                        </div>
                                    </li>
                                    <li class="control">
                                        <input type="checkbox" name="set_to_billing" id="shipping:same_as_billing" value="1" checked="checked" title="Use Billing Address" class="checkbox">
                                        <label for="shipping:same_as_billing">{l s='Use the delivery address as the billing address.' mod='jmango360api'}</label>
                                    </li>
                                </ul>
                                <div class="buttons-set" id="shipping-buttons-container">
                                    <button id="shipping-button" type="button" title="Continue" class="ladda-button" onclick="shipping.save();" data-color="jmango" data-style="slide-up" data-size="s">
                                        <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
                                        <span class="ladda-spinner"></span></button>
                                    <div class="ladda-progress" style="width: 0px;"></div>
                                </div>
                            </fieldset>
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
                        </div>
                    </div>
                </li>
            </ol>
        </div>
    </div>

    <script type="text/javascript">
        //<![CDATA[
        function decodeHTMLEntities(text) {
            var entities = [
                ['amp', '&'],
                ['apos', '\''],
                ['#x27', '\''],
                ['#x2F', '/'],
                ['#39', '\''],
                ['#47', '/'],
                ['lt', '<'],
                ['gt', '>'],
                ['nbsp', ' '],
                ['quot', '"']
            ];

            for (var i = 0, max = entities.length; i < max; ++i)
                text = text.replace(new RegExp('&'+entities[i][0]+';', 'g'), entities[i][1]);

            return text;
        }

        var newAddressLabel = "{l s='Add a new address...' mod='jmango360api'}";
        var TOSMsg = "{l s='You must agree to the terms of service before continuing.' mod='jmango360api' js=1}";
        var networkErrorMsg = "{l s='No internet connection, please try again' mod='jmango360api' js=1}";
        var unknownErrorMsg = "{l s='Unknown error.' mod='jmango360api' js=1}";
        var loadingMsg = "{l s='Loading' mod='jmango360api' js=1}";

        var langCode = "{$lang_code|escape:'htmlall':'UTF-8'}";
        var addresses = {$addresses|json_encode};
        var customer = {$customer|json_encode};
        var isLogged = {$is_logged|intval};
        {*var default_country = {$default_country|escape:'htmlall':'UTF-8'};*}
        {*var countries = {$countries|json_encode};*}
        var selectedBillingAddress = {$selected_invoice_address_id|intval};
        var selectedShippingAddress = {$selected_delivery_address_id|intval};

        {*var idSelectedState = {$idSelectedState|intval};*}
        {*var idSelectedInvoiceState = {$idSelectedInvoiceState|intval};*}
        var myopc_checkout_url = '{url entity='module' name=$module_name controller='jmcheckout' relative_protocol=false}';

        document.addEventListener("DOMContentLoaded", function () {
            opc_accordion = new Accordion('checkoutSteps', '.step-title', true);
            checkout = new Checkout(opc_accordion);
            personalInformation = new PersonalInformation('co-personal-information-form', myopc_checkout_url);
            billing = new Billing('co-billing-form', myopc_checkout_url);
            shipping = new Shipping('co-shipping-form', myopc_checkout_url);
            shippingMethod = new ShippingMethod('co-shipping-method-form', myopc_checkout_url);
            paymentMethod = new PaymentMethod('co-payment-form', myopc_checkout_url);
            review = new Review('co-payment-form', 'checkout-agreements', myopc_checkout_url);
            {*coupon = new Coupon('co-coupon-form', "{$myopc_checkout_url}")*}
            //new JMAgreement('checkout-agreements');
        });

        //]]>
    </script>
{/if}
</body>
</html>
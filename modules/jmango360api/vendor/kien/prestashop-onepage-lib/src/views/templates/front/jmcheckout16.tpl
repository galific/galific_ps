{**
* @license Created by JMango
*}

{if isset($empty)}
    <p class="alert alert-warning">{l s='Your shopping cart is empty.' d=$module_name}</p>
{else}
<div class="main-container">
    <div class="col-main">
        <ol class="opc" id="checkoutSteps">
            <li id="opc-billing" class="panel section {if selected_invoice_address_id}allow{/if}">
                <div class="step-title" role="tab">
                    <div class="step-number-wrapper">
                        <span class="step-number">1</span>
                    </div>
                    <h2>{l s='Billing Address' d=$module_name}</h2>
                    <a href="#checkout-step-billing" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-expanded="false" aria-controls="checkout-step-billing" class="section-control collapsed">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-billing" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                    <form id="co-billing-form" action="{$myopc_checkout_url|escape:'url'}">
                        <fieldset>
                            <ul class="form-list">
                                {if $is_logged}
                                    <li class="wide">
                                        <label for="billing-address-select">
                                            {l s='Select a billing address from your address book or enter a new address.' d=$module_name}
                                        </label>
                                        <div class="input-box">
                                            <select name="billing_address_id" id="billing-address-select" class="address-select validation-passed" title="" onchange="billing.newAddress(!this.value)">
                                                {if $selected_invoice_address_id}
                                                    <option value="">{l s='Add a new address...' d=$module_name}</option>
                                                {else}
                                                    <option value="" selected="selected">{l s='Add a new address...' d=$module_name}</option>
                                                {/if}
                                                {foreach from=$addresses key=k item=address}
                                                    <option value="{$address.id_address|intval}" {if $address.id_address == $selected_invoice_address_id} selected="selected"{/if}>
                                                        {$address.alias|escape:'html':'UTF-8'}
                                                    </option>
                                                {/foreach}
                                            </select>
                                            <div class="japi-address-edit" style="display: none">
                                                <a href="#" class="japi-address-edit-btn">{l s='Edit' d=$module_name}</a>
                                            </div>
                                        </div>
                                    </li>
                                {else}
                                    {if $customer}
                                        <select name="billing_address_id" id="billing-address-select" class="address-select validation-passed" title="" style="display:none" ">
                                            {foreach from=$addresses key=k item=address}
                                                <option value="{$address.id_address|intval}" {if $address.id_address == $selected_invoice_address_id} selected="selected"{/if}>
                                                    {$address.alias|escape:'html':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                    {/if}
                                {/if}

                                <li id="billing-new-address-form">
                                    <fieldset class="">
                                        <input type="hidden" name="billing[address_id]" value="" id="billing:address_id">
                                        <ul>
                                            <li class="wide" style="{if $is_logged eq 1}display:none{/if}">
                                                <div class="field">
                                                    <label for="billing:gender_id">{l s='Title' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <select id="billing:gender_id" name="billing[gender_id]" title="Title" class="validate-select">
                                                            <option value="" selected="selected"></option>
                                                            {foreach from=$genders key=k item=gender}
                                                                <option value="{$gender->id|intval}" >{$gender->name|escape:'html':'UTF-8'}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="wide">
                                                <div class="field name-firstname">
                                                    <label for="billing:firstname" class="required">{l s='First name' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" id="billing:firstname" name="billing[firstname]" value="" title="First name" maxlength="255" class="input-text required" data-validation="required">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="wide">
                                                <div class="field name-lastname">
                                                    <label for="billing:lastname" class="required">{l s='Last name' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" id="billing:lastname" name="billing[lastname]" value="" title="Last name" maxlength="255" class="input-text required" data-validation="required">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="fields" style="{if $is_logged eq 1}display:none{/if}">
                                                <label for="billing:days">{l s='Date of Birth' d=$module_name}</label>
                                                <div class="customer-dob">
                                                    <div class="field dob-day">
                                                        <select id="billing:days" name="billing[days]" class="validate-select">
                                                            <option value="">-</option>
                                                            {foreach from=$days item=day}
                                                                <option value="{$day|intval}">{$day|intval}&nbsp;&nbsp;</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                    <div class="field dob-month">
                                                        <select id="billing:months" name="billing[months]" class="validate-select">
                                                            <option value="">-</option>
                                                            {foreach from=$months key=k item=month}
                                                                <option value="{$k|intval}">{l s=$month d=$module_name}&nbsp;</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                    <div class="field dob-year">
                                                        <select id="billing:years" name="billing[years]" class="validate-select">
                                                            <option value="">-</option>
                                                            {foreach from=$years item=year}
                                                                <option value="{$year|intval}">{$year|intval}&nbsp;&nbsp;</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="fields">
                                                <div class="field input-company">
                                                    <label for="billing:company">{l s='Company' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" id="billing:company" name="billing[company]" value="" title="Company" class="input-text ">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="wide" style="{if $is_logged}display:none{/if}">
                                                <div class="field">
                                                    <label for="billing:email" class="required">{l s='Email address' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" name="billing[email]" id="billing:email" value="" title="Email address" {if $is_logged}readonly="true"{/if} class="input-text validate-email required" data-validation="email">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="wide">
                                                <label for="billing:street1" class="required">{l s='Address' d=$module_name}</label>
                                                <div class="input-box">
                                                    <input type="text" title="Street Address" name="billing[street]" id="billing:street1" value="" class="input-text  required-entry" data-validation="required">
                                                </div>
                                            </li>
                                            <li class="fields">
                                                <div class="field">
                                                    <label for="billing:city" class="required">{l s='City' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" title="City" name="billing[city]" value="" class="input-text  required-entry" id="billing:city" data-validation="required">
                                                    </div>
                                                </div>
                                                <div class="field" id="billing:state">
                                                    <label for="billing:state_id" class="required">{l s='State/Province' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <select id="billing:state_id" name="billing[state_id]" title="State/Province" class="validate-select">
                                                            <option value="">{l s='Please select region, state or province' d=$module_name}</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="fields">
                                                {if in_array('postcode', $ordered_adr_fields)}
                                                <div class="field">
                                                    <label for="billing:postcode" class="required">{l s='Zip/Postal Code' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" title="Zip/Postal Code" name="billing[postcode]" id="billing:postcode" value="" class="input-text validate-zip-international required-entry" data-validation="required">
                                                    </div>
                                                </div>
                                                {/if}
                                                <div class="field">
                                                    <label for="billing:country_id" class="required">{l s='Country' d=$module_name}</label>
                                                    <div class="input-box input-country">
                                                        <select name="billing[country_id]" id="billing:country_id" class="validate-select" title="Country">
                                                            {foreach from=$countries item=v}
                                                                <option value="{$v.id_country|intval}"{if $default_country == $v.id_country} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="fields">
                                                <div class="field">
                                                    <label for="billing:telephone" class="required">{l s='Mobile phone' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" name="billing[telephone]" value="" title="Telephone" class="input-text  required-entry" id="billing:telephone" data-validation="required">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="fields" style="{if $is_logged eq 0}display: none{/if}">
                                                <div class="field">
                                                    <label for="billing:alias" class="required">{l s='Please assign an address title for future reference.' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" name="billing[alias]" value="{l s='My address' d=$module_name}" title="Alias" class="input-text  required-entry" id="billing:alias" data-validation="required">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="fields" id="register-customer-password" style="display: none;">
                                                <div class="field">
                                                    <label for="billing:customer_password" class="required"><em>*</em>{l s='Password' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="password" name="billing[customer_password]" id="billing:customer_password" title="Password" class="input-text required-entry validate-password">
                                                    </div>
                                                </div>
                                                <div class="field">
                                                    <label for="billing:confirm_password" class="required"><em>*</em>{l s='Confirm Password' d=$module_name} </label>
                                                    <div class="input-box">
                                                        <input type="password" name="billing[confirm_password]" title="Confirm Password" id="billing:confirm_password" class="input-text required-entry validate-cpassword">
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="no-display">
                                                <input type="hidden" id='billing:save_in_address_book' name="billing[save_in_address_book]" value="1">
                                            </li>
                                        </ul>
                                    </fieldset>
                                </li>
                                <li class="control">
                                    <input type="radio" name="billing[use_for_shipping]" id="billing:use_for_shipping_yes" value="1" checked="checked" title="Ship to this address" onclick="$('#shipping\\:same_as_billing').prop('checked', true);" class="radio">
                                    <label for="billing:use_for_shipping_yes">{l s='Ship to this address' d=$module_name}</label>
                                </li>
                                <li class="control">
                                    <input type="radio" name="billing[use_for_shipping]" id="billing:use_for_shipping_no" value="0" title="Ship to different address" onclick="$('#shipping\\:same_as_billing').prop('checked', false);" class="radio">
                                    <label for="billing:use_for_shipping_no">{l s='Ship to different address' d=$module_name}</label>
                                </li>
                            </ul>
                            <div class="buttons-set" id="billing-buttons-container">
                                <button id="billing-button" type="button" title="Continue" class="ladda-button" onclick="billing.save()" data-color="jmango" data-style="slide-up" data-size="s">
                                    <span class="ladda-label">{l s='Continue' d=$module_name}</span>
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
                    <h2>{l s='Delivery Address' d=$module_name}</h2>
                    <a href="#checkout-step-shipping" data-toggle="collapse" role="button" data-parent="#checkoutSteps" aria-controls="checkout-step-shipping" class="section-control collapsed" aria-expanded="false">
                        <i class="fa fa-pencil"></i>
                    </a>
                </div>
                <div id="checkout-step-shipping" role="tabpanel" class="step a-item collapse" style="height: 0px;" aria-expanded="false">
                    <form action="{$myopc_checkout_url|escape:'url'}" id="co-shipping-form">
                        <ul class="form-list">

                            {if $is_logged}
                                <li class="wide">
                                    <label for="shipping-address-select">
                                        {l s='Select a delivery address from your address book or enter a new address.' d=$module_name}
                                    </label>
                                    <div class="input-box">
                                        <select name="shipping_address_id" id="shipping-address-select" class="address-select validation-passed" title="" onchange="shipping.newAddress(!this.value)">
                                            {if $selected_delivery_address_id}
                                                <option value="">{l s='Add a new address...' d=$module_name}</option>
                                            {else}
                                                <option value="" selected="selected">{l s='Add a new address...' d=$module_name}</option>
                                            {/if}
                                            {foreach from=$addresses key=k item=address}
                                                <option value="{$address.id_address|intval}" {if $address.id_address == $selected_delivery_address_id} selected="selected"{/if}>
                                                    {$address.alias|escape:'html':'UTF-8'}
                                                </option>
                                            {/foreach}
                                        </select>
                                        <div class="japi-address-edit" style="display: none">
                                            <a href="#" class="japi-address-edit-btn">{l s='Edit' d=$module_name}</a>
                                        </div>
                                    </div>
                                </li>
                            {else}
                                {if $customer}
                                    <select name="shipping_address_id" id="shipping-address-select" class="address-select validation-passed" title="" style="display:none" ">
                                    {foreach from=$addresses key=k item=address}
                                        <option value="{$address.id_address|intval}" {if $address.id_address == $selected_delivery_address_id} selected="selected"{/if}>
                                            {$address.alias|escape:'html':'UTF-8'}
                                        </option>
                                    {/foreach}
                                    </select>
                                {/if}
                            {/if}
                            <li id="shipping-new-address-form">
                                <fieldset>
                                    <input type="hidden" name="shipping[address_id]" value="" id="shipping:address_id">
                                    <ul>
                                        <li class="fields">
                                            <div class="customer-name">
                                                <div class="field name-firstname">
                                                    <label for="shipping:firstname" class="required">{l s='First name' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" id="shipping:firstname" name="shipping[firstname]" value="" title="First name" maxlength="255" class="input-text required-entry" data-validation="required" onchange="shipping.setSameAsBilling(false)">
                                                    </div>
                                                </div>
                                                <div class="field name-lastname">
                                                    <label for="shipping:lastname" class="required">{l s='Last name' d=$module_name}</label>
                                                    <div class="input-box">
                                                        <input type="text" id="shipping:lastname" name="shipping[lastname]" value="" title="Last name" maxlength="255" class="input-text required-entry" data-validation="required" onchange="shipping.setSameAsBilling(false)">
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="fields">
                                            <div class="fields">
                                                <label for="shipping:company">{l s='Company' d=$module_name}</label>
                                                <div class="input-box">
                                                    <input type="text" id="shipping:company" name="shipping[company]" value="" title="Company" class="input-text " onchange="shipping.setSameAsBilling(false);">
                                                </div>
                                            </div>
                                        </li>
                                        <li class="wide">
                                            <label for="shipping:street1" class="required">{l s='Address' d=$module_name} </label>
                                            <div class="input-box">
                                                <input type="text" title="Street Address" name="shipping[street]" id="shipping:street1" value="" class="input-text  required-entry" data-validation="required" onchange="shipping.setSameAsBilling(false);">
                                            </div>
                                        </li>

                                        <li class="fields">
                                            <div class="field">
                                                <label for="shipping:city" class="required">{l s='City' d=$module_name} </label>
                                                <div class="input-box">
                                                    <input type="text" title="City" name="shipping[city]" value="" class="input-text  required-entry" id="shipping:city" data-validation="required" onchange="shipping.setSameAsBilling(false);">
                                                </div>
                                            </div>
                                            <div class="field" id="shipping:state">
                                                <label for="shipping:state_id" class="required">State/Province</label>
                                                <div class="input-box">
                                                    <select id="shipping:state_id" name="shipping[state_id]" class="validate-select" title="State/Province" data-validation-skipped="1">
                                                        <option value="">Please select region, state or province</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="fields">
                                            <div class="field">
                                                <label for="shipping:postcode" class="required">{l s='Zip/Postal Code' d=$module_name}</label>
                                                <div class="input-box">
                                                    <input type="text" title="Zip/Postal Code" name="shipping[postcode]" id="shipping:postcode" value="" class="input-text validate-zip-international  required-entry" data-validation="required" onchange="shipping.setSameAsBilling(false);">
                                                </div>
                                            </div>
                                            <div class="field">
                                                <label for="shipping:country_id" class="required">{l s='Country' d=$module_name}</label>
                                                <div class="input-box">
                                                    <select name="shipping[country_id]" id="shipping:country_id" class="validate-select" title="Country" onchange="if(window.shipping)shipping.setSameAsBilling(false);">
                                                        {foreach from=$countries item=v}
                                                            <option value="{$v.id_country|intval}" {if $default_country == $v.id_country} selected="selected"{/if}>{$v.name|escape:'html':'UTF-8'}</option>
                                                        {/foreach}
                                                    </select>
                                                </div>
                                            </div>
                                        </li>
                                        <li class="fields">
                                            <div class="field">
                                                <label for="shipping:telephone" class="required">{l s='Mobile phone' d=$module_name}</label>
                                                <div class="input-box">
                                                    <input type="text" name="shipping[telephone]" value="" title="Telephone" class="input-text  required-entry" id="shipping:telephone" data-validation="required" onchange="shipping.setSameAsBilling(false);">
                                                </div>
                                            </div>
                                        </li>
                                        <li class="fields" style="{if $is_logged eq 0}display: none{/if}">
                                            <div class="field">
                                                <label for="shipping:alias" class="required">{l s='Assign an address alias for future reference.' d=$module_name}</label>
                                                <div class="input-box">
                                                    <input type="text" name="shipping[alias]" value="{l s='My invoice address' d=$module_name}" title="Alias" class="input-text  required-entry" id="shipping:alias" data-validation="required">
                                                </div>
                                            </div>
                                        </li>
                                        <li class="no-display">
                                            <input type="hidden" name="shipping[save_in_address_book]" value="1">
                                        </li>
                                    </ul>
                                </fieldset>
                            </li>
                            <li class="control">
                                <input type="checkbox" name="shipping[same_as_billing]" id="shipping:same_as_billing" value="1" checked="checked" title="Use Billing Address" onclick="shipping.setSameAsBilling(this.checked)" class="checkbox">
                                <label for="shipping:same_as_billing">{l s='Use the delivery address as the billing address.' d=$module_name}</label>
                            </li>
                        </ul>
                        <div class="buttons-set" id="shipping-buttons-container">
                            <p class="required">* Required Fields</p>
                            <p class="back-link">
                                <a href="http://preview-store.jmango360.com/japi/checkout/onepage/#" onclick="checkout.back(); return false;">
                                    <small>« </small>Back</a>
                            </p>
                            <button id="shipping-button" type="button" class="ladda-button" data-color="jmango" data-style="slide-up" data-size="s" title="Continue" onclick="shipping.save()">
                                <span class="ladda-label">{l s='Continue' d=$module_name}</span>
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
                    <h2>{l s='Shipping' d=$module_name}</h2>
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
                    <h2>{l s='Payment' d=$module_name}</h2>
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
                    <h2>{l s='Summary' d=$module_name}</h2>
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
        </ol>
    </div>
</div>
<script type="text/javascript">
    //<![CDATA[
    var TOSMsg = "{l s='You must agree to the terms of service before continuing.' d=$module_name js=1}";
    var networkErrorMsg = "{l s='No internet connection, please try again' d=$module_name js=1}";
    var unknownErrorMsg = "{l s='Unknown error.' d=$module_name js=1}";
    var loadingMsg = "{l s='Loading' d=$module_name js=1}";

    var langCode = "{$lang_code|escape:'htmlall':'UTF-8'}";
    var addresses = {$addresses|json_encode};
    var customer = {$customer|json_encode};
    var isLogged = {$is_logged|intval};
    var default_country = {$default_country|escape:'htmlall':'UTF-8'};
    var countries = {$countries|json_encode};
    var selectedBillingAddress = {$selected_invoice_address_id|intval};
    var selectedShippingAddress = {$selected_delivery_address_id|intval};

    var idSelectedState = {$idSelectedState|intval};
    var idSelectedInvoiceState = {$idSelectedInvoiceState|intval};

    document.addEventListener("DOMContentLoaded", function () {
        opc_accordion = new Accordion('checkoutSteps', '.step-title', true);
        checkout = new Checkout(opc_accordion);
        billing = new Billing('co-billing-form', "{$myopc_checkout_url|escape:'url'}");
        shipping = new Shipping('co-shipping-form', "{$myopc_checkout_url|escape:'url'}");
        shippingMethod = new ShippingMethod('co-shipping-method-form', "{$myopc_checkout_url|escape:'url'}");
        paymentMethod = new PaymentMethod('co-payment-form', "{$myopc_checkout_url|escape:'url'}");
        review = new Review('co-payment-form', 'checkout-agreements', "{$myopc_checkout_url|escape:'url'}");
        coupon = new Coupon('co-coupon-form', "{$myopc_checkout_url|escape:'url'}")
        //new JMAgreement('checkout-agreements');
    });

    //]]>
</script>
{/if}

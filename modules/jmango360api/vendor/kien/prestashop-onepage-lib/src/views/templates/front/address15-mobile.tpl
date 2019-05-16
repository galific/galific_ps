{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2018 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
<!DOCTYPE html>
<html lang="{$lang_iso|escape:'html':'UTF-8'}">
<head>
	<title>{$meta_title|escape:'htmlall':'UTF-8'}</title>
    {*<meta name="viewport" content="width=device-width, initial-scale=1">*}
	<meta content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    {if isset($meta_description) AND $meta_description}
		<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
    {/if}
    {if isset($meta_keywords) AND $meta_keywords}
		<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
    {/if}
	<meta http-equiv="Content-Type" content="application/xhtml+xml; charset=utf-8" />
	<meta name="generator" content="PrestaShop" />
	<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
	<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url|escape:'url'}?{$img_update_time|escape:'html':'UTF-8'}" />
	<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url|escape:'url'}?{$img_update_time|escape:'html':'UTF-8'}" />
	<script type="text/javascript">
        var baseDir = '{$content_dir|escape:'html':'UTF-8'}';
        var static_token = '{$static_token|escape:'html':'UTF-8'}';
        var token = '{$token|escape:'html':'UTF-8'}';
        var priceDisplayPrecision = {$priceDisplayPrecision*$currency->decimals|escape:'html':'UTF-8'};
        var priceDisplayMethod = {$priceDisplay|escape:'html':'UTF-8'};
        var roundMode = {$roundMode|escape:'html':'UTF-8'};
	</script>
    {if isset($css_files)}
        {foreach from=$css_files key=css_uri item=media}
			<link href="{$css_uri|escape:'url'}" rel="stylesheet" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
        {/foreach}
    {/if}
    {if isset($js_files)}
        {foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'url'}"></script>
        {/foreach}
    {/if}
    {$HOOK_MOBILE_HEADER|escape:'html':'UTF-8'}
</head>
<body {if isset($page_name)}id="{$page_name|escape:'htmlall':'UTF-8'}"{/if}>
<div data-role="page" {if isset($wrapper_id)}id="{$wrapper_id|intval}"{/if} class="type-interior prestashop-page">

{capture assign='page_title'}{l s='Your address' d=$module_name}{/capture}
{include file="$tpl_dir./mobile/page-title.tpl"}

{include file="$tpl_dir./mobile/errors.tpl"}

<script type="text/javascript">
    // <![CDATA[
    idSelectedCountry = {if isset($smarty.post.id_state)}{$smarty.post.id_state|intval}{else}{if isset($address->id_state)}{$address->id_state|intval}{else}false{/if}{/if};
    countries = new Array();
    countriesNeedIDNumber = new Array();
    countriesNeedZipCode = new Array();
    {foreach from=$countries item='country'}
    {if isset($country.states) && $country.contains_states}
    countries[{$country.id_country|intval}] = new Array();
    {foreach from=$country.states item='state' name='states'}
    countries[{$country.id_country|intval}].push({ldelim}'id' : '{$state.id_state}', 'name' : '{$state.name|escape:'htmlall':'UTF-8'}'{rdelim});
    {/foreach}
    {/if}
    {if $country.need_identification_number}
    countriesNeedIDNumber.push({$country.id_country|intval});
    {/if}
    {if isset($country.need_zip_code)}
    countriesNeedZipCode[{$country.id_country|intval}] = {$country.need_zip_code|escape:'html':'UTF-8'};
    {/if}
    {/foreach}
    $(function(){ldelim}
        $('.id_state option[value={if isset($smarty.post.id_state)}{$smarty.post.id_state|escape:'html':'UTF-8'}{else}{if isset($address->id_state)}{$address->id_state|escape:'htmlall':'UTF-8'}{/if}{/if}]').attr('selected', 'selected');
        {rdelim});
    {literal}
    $(document).ready(function() {
        $('#company').blur(function(){
            vat_number();
        });
        vat_number();
        function vat_number()
        {
            if ($('#company').val() != '')
                $('#vat_number').show();
            else
                $('#vat_number').hide();
        }
    });
    {/literal}
    //]]>
</script>

<div data-role="content" id="content">
	<div>
		<p>
            {if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
                {l s='Modify address' d=$module_name}
                {if isset($smarty.post.alias)}
					"{$smarty.post.alias|escape:'html':'UTF-8'}"
                {else}
                    {if isset($address->alias)}"{$address->alias|escape:'htmlall':'UTF-8'}"{/if}
                {/if}
            {else}
                {l s='To add a new address, please fill out the form below.' d=$module_name}
            {/if}
		</p>

		<form action="{$link->getModuleLink("$module_name", "address", array())|escape:'quotes':'UTF-8'}" method="post" id="add_address" data-ajax="false">
			<legend><h3>{if isset($id_address) && $id_address != 0}{l s='Your address' d=$module_name}{else}{l s='New address' d=$module_name}{/if}</h3></legend>
			<div class="required text dni">
				<label for="dni">{l s='Identification number' d=$module_name}</label>
				<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni|escape:'html':'UTF-8'}{else}{if isset($address->dni)}{$address->dni|escape:'htmlall':'UTF-8'}{/if}{/if}" />
				<p>{l s='DNI / NIF / NIE' d=$module_name} <sup>*</sup></p>
				<label for="dni">{l s='Identification number' d=$module_name}</label>
				<input type="text" class="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni|escape:'html':'UTF-8'}{else}{if isset($address->dni)}{$address->dni|escape:'htmlall':'UTF-8'}{/if}{/if}" />
				<p>{l s='DNI / NIF / NIE' d=$module_name} <sup>*</sup></p>
			</div>
            {if $vat_display == 2}
			<div id="vat_area">
                {elseif $vat_display == 1}
				<div id="vat_area" style="display: none;">
                    {else}
					<div style="display: none;">
                        {/if}
						<div id="vat_number">
							<p class="text">
								<label for="vat_number">{l s='VAT number' d=$module_name}</label>
								<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number|escape:'html':'UTF-8'}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'htmlall':'UTF-8'}{/if}{/if}" />
								<label for="vat_number">{l s='VAT number' d=$module_name}</label>
								<input type="text" class="text" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number|escape:'html':'UTF-8'}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'htmlall':'UTF-8'}{/if}{/if}" />
							</p>
						</div>
					</div>
                    {assign var="stateExist" value="false"}
                    {assign var="postCodeExist" value="false"}
                    {foreach from=$ordered_adr_fields item=field_name}
                    {if $field_name eq 'company'}
						<div class="text">
							<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
							<label for="company">{l s='Company' d=$module_name}</label>
							<input type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company|escape:'html':'UTF-8'}{else}{if isset($address->company)}{$address->company|escape:'htmlall':'UTF-8'}{/if}{/if}" />
							<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
							<label for="company">{l s='Company' d=$module_name}</label>
							<input type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company|escape:'html':'UTF-8'}{else}{if isset($address->company)}{$address->company|escape:'htmlall':'UTF-8'}{/if}{/if}" />
						</div>
                    {/if}
                    {if $field_name eq 'firstname'}
						<div class="required text">
							<label for="firstname">{l s='First name' d=$module_name} <sup>*</sup></label>
							<input type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape:'html':'UTF-8'}{else}{if isset($address->firstname)}{$address->firstname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
							<label for="firstname">{l s='First name' d=$module_name} <sup>*</sup></label>
							<input type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape:'html':'UTF-8'}{else}{if isset($address->firstname)}{$address->firstname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
						</div>
                    {/if}
                    {if $field_name eq 'lastname'}
						<div class="required text">
							<label for="lastname">{l s='Last name' d=$module_name} <sup>*</sup></label>
							<input type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape:'html':'UTF-8'}{else}{if isset($address->lastname)}{$address->lastname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
							<label for="lastname">{l s='Last name' d=$module_name} <sup>*</sup></label>
							<input type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape:'html':'UTF-8'}{else}{if isset($address->lastname)}{$address->lastname|escape:'htmlall':'UTF-8'}{/if}{/if}" />
						</div>
                    {/if}
                    {if $field_name eq 'address1'}
						<div class="required text">
							<label for="address1">{l s='Address' d=$module_name} <sup>*</sup></label>
							<input type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape:'html':'UTF-8'}{else}{if isset($address->address1)}{$address->address1|escape:'htmlall':'UTF-8'}{/if}{/if}" />
							<label for="address1">{l s='Address' d=$module_name} <sup>*</sup></label>
							<input type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape:'html':'UTF-8'}{else}{if isset($address->address1)}{$address->address1|escape:'htmlall':'UTF-8'}{/if}{/if}" />
						</div>
                    {/if}
                    {if $field_name eq 'address2'}
						<div class="required text">
							<label for="address2">{l s='Address (Line 2)' d=$module_name}</label>
							<input type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape:'html':'UTF-8'}{else}{if isset($address->address2)}{$address->address2|escape:'htmlall':'UTF-8'}{/if}{/if}" />
							<label for="address2">{l s='Address (Line 2)' d=$module_name}</label>
							<input type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape:'html':'UTF-8'}{else}{if isset($address->address2)}{$address->address2|escape:'htmlall':'UTF-8'}{/if}{/if}" />
						</div>
                    {/if}
                    {if $field_name eq 'postcode'}
                        {assign var="postCodeExist" value="true"}
						<div class="required postcode text">
							<label for="postcode">{l s='Zip / Postal Code' d=$module_name} <sup>*</sup></label>
							<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'htmlall':'UTF-8'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
							<label for="postcode">{l s='Zip / Postal Code' d=$module_name} <sup>*</sup></label>
							<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'htmlall':'UTF-8'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
						</div>
                    {/if}
                    {if $field_name eq 'city'}
						<div class="required text">
							<label for="city">{l s='City' d=$module_name} <sup>*</sup></label>
							<input type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape:'html':'UTF-8'}{else}{if isset($address->city)}{$address->city|escape:'htmlall':'UTF-8'}{/if}{/if}" maxlength="64" />
							<label for="city">{l s='City' d=$module_name} <sup>*</sup></label>
							<input type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape:'html':'UTF-8'}{else}{if isset($address->city)}{$address->city|escape:'htmlall':'UTF-8'}{/if}{/if}" maxlength="64" />
						</div>
                    {/if}
                    {if $field_name eq 'Country:name' || $field_name eq 'country'}
						<div class="required select">
							<label for="id_country">{l s='Country' d=$module_name} <sup>*</sup></label>
							<select id="id_country" name="id_country">{$countries_list|escape:'html':'UTF-8'}</select>
							<label for="id_country">{l s='Country' d=$module_name} <sup>*</sup></label>
							<select id="id_country" name="id_country">{$countries_list|escape:'html':'UTF-8'}</select>
						</div>
                    {if $vatnumber_ajax_call}
						<script type="text/javascript">
                            var ajaxurl = '{$ajaxurl|escape:'url'}';
                            {literal}
                            $(document).ready(function(){
                                $('#id_country').change(function() {
                                    $.ajax({
                                        type: "GET",
                                        url: ajaxurl+"vatnumber/ajax.php?id_country="+$('#id_country').val(),
                                        success: function(isApplicable){
                                            if(isApplicable == "1")
                                            {
                                                $('#vat_area').show();
                                                $('#vat_number').show();
                                            }
                                            else
                                            {
                                                $('#vat_area').hide();
                                            }
                                        }
                                    });
                                });
                            });
                            {/literal}
						</script>
                    {/if}
                    {/if}
                    {if $field_name eq 'State:name'}
                        {assign var="stateExist" value="true"}
						<div class="required id_state select">
							<label for="id_state">{l s='State' d=$module_name} <sup>*</sup></label>
							<select name="id_state" id="id_state">
								<option value="">-</option>
							</select>
						</div>
                    {/if}
                    {/foreach}
                    {if $postCodeExist eq "false"}
						<div class="required postcode text hidden">
							<label for="postcode">{l s='Zip / Postal Code' d=$module_name} <sup>*</sup></label>
							<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'htmlall':'UTF-8'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
							<label for="postcode">{l s='Zip / Postal Code' d=$module_name} <sup>*</sup></label>
							<input type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'htmlall':'UTF-8'}{/if}{/if}" onkeyup="$('#postcode').val($('#postcode').val().toUpperCase());" />
						</div>
                    {/if}
                    {if $stateExist eq "false"}
						<div class="required id_state select hidden">
							<label for="id_state">{l s='State' d=$module_name} <sup>*</sup></label>
							<select name="id_state" id="id_state">
								<option value="">-</option>
							</select>
						</div>
                    {/if}
					<div class="textarea">
						<label for="other">{l s='Additional information' d=$module_name}</label>
						<textarea id="other" name="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other|escape:'html':'UTF-8'}{else}{if isset($address->other)}{$address->other|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
						<label for="other">{l s='Additional information' d=$module_name}</label>
						<textarea id="other" name="other" cols="26" rows="3">{if isset($smarty.post.other)}{$smarty.post.other|escape:'html':'UTF-8'}{else}{if isset($address->other)}{$address->other|escape:'htmlall':'UTF-8'}{/if}{/if}</textarea>
					</div>

					<p>{l s='You must register at least one phone number.' d=$module_name} <sup class="required">*</sup></p>
					<div class="text">
						<label for="phone">{l s='Home phone' d=$module_name}</label>
						<input type="text" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'html':'UTF-8'}{else}{if isset($address->phone)}{$address->phone|escape:'htmlall':'UTF-8'}{/if}{/if}" />
					</div>
					<div class="text">
						<label for="phone_mobile">{l s='Mobile phone' d=$module_name}</label>
						<input type="text" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape:'html':'UTF-8'}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'htmlall':'UTF-8'}{/if}{/if}" />
						<label for="phone">{l s='Home phone' d=$module_name}</label>
						<input type="text" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'html':'UTF-8'}{else}{if isset($address->phone)}{$address->phone|escape:'htmlall':'UTF-8'}{/if}{/if}" />
					</div>
					<div class="text">
						<label for="phone_mobile">{l s='Mobile phone' d=$module_name}</label>
						<input type="text" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape:'html':'UTF-8'}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'htmlall':'UTF-8'}{/if}{/if}" />
					</div>
					<p class="required text" id="adress_alias">
						<label for="alias">{l s='Please assign an address title for future reference.' d=$module_name} <sup>*</sup></label>
						<input type="text" id="alias" name="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias|escape:'html':'UTF-8'}{else if isset($address->alias)}{$address->alias|escape:'htmlall':'UTF-8'}{else if isset($select_address)}{l s='My address' d=$module_name}{/if}" />
					</p>
					<div>
                        {if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
                        {*{if isset($back)}<input type="hidden" name="back" value="{$back}" />{/if}*}
                        {if isset($mod)}<input type="hidden" name="mod" value="{$mod|escape:'html':'UTF-8'}" />{/if}
                        {if isset($select_address)}<input type="hidden" name="select_address" value="{$select_address|intval}" />{/if}
						<button type="submit" data-theme="a" name="submitAddress" value="submit-value" id="submitAddress" >{l s='Save' d=$module_name}</button>
					</div>
		</form>
	</div>

</div><!-- /content -->

</div>
</body>
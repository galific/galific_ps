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
<!DOCTYPE HTML>
<!--[if lt IE 7]> <html class="no-js lt-ie9 lt-ie8 lt-ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if IE 8]><html class="no-js lt-ie9 ie8"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<!--[if gt IE 8]> <html class="no-js ie9"{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}><![endif]-->
<html{if isset($language_code) && $language_code} lang="{$language_code|escape:'html':'UTF-8'}"{/if}>
	<head>
		<meta charset="utf-8" />
		<title>{$meta_title|escape:'html':'UTF-8'}</title>
		{if isset($meta_description) AND $meta_description}
			<meta name="description" content="{$meta_description|escape:'html':'UTF-8'}" />
		{/if}
		{if isset($meta_keywords) AND $meta_keywords}
			<meta name="keywords" content="{$meta_keywords|escape:'html':'UTF-8'}" />
		{/if}
		<meta name="generator" content="PrestaShop" />
		<meta name="robots" content="{if isset($nobots)}no{/if}index,{if isset($nofollow) && $nofollow}no{/if}follow" />
		<meta name="viewport" content="width=device-width, minimum-scale=0.25, maximum-scale=1.6, initial-scale=1.0" />
		<meta name="apple-mobile-web-app-capable" content="yes" />
		<link rel="icon" type="image/vnd.microsoft.icon" href="{$favicon_url|escape:'url'}?{$img_update_time|escape:'html':'UTF-8'}" />
		<link rel="shortcut icon" type="image/x-icon" href="{$favicon_url|escape:'url'}?{$img_update_time|escape:'html':'UTF-8'}" />
		{if isset($css_files)}
			{foreach from=$css_files key=css_uri item=media}
				{if $css_uri == 'lteIE9'}
					<!--[if lte IE 9]>
					{foreach from=$css_files[$css_uri] key=css_uriie9 item=mediaie9}
					<link rel="stylesheet" href="{$css_uriie9|escape:'html':'UTF-8'}" type="text/css" media="{$mediaie9|escape:'html':'UTF-8'}" />
					{/foreach}
					<![endif]-->
				{else}
					<link rel="stylesheet" href="{$css_uri|escape:'html':'UTF-8'}" type="text/css" media="{$media|escape:'html':'UTF-8'}" />
				{/if}
			{/foreach}
		{/if}
		{if isset($js_defer) && !$js_defer && isset($js_files) && isset($js_def)}
			{$js_def|escape:'html':'UTF-8'}
			{foreach from=$js_files item=js_uri}
			<script type="text/javascript" src="{$js_uri|escape:'html':'UTF-8'}"></script>
			{/foreach}
		{/if}
		<link rel="stylesheet" href="//fonts.googleapis.com/css?family=Open+Sans:300,600&amp;subset=latin,latin-ext" type="text/css" media="all" />
		<!--[if IE 8]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
		<![endif]-->
	</head>
<body{if isset($page_name)} id="{$page_name|escape:'html':'UTF-8'}"{/if} class="{if isset($page_name)}{$page_name|escape:'html':'UTF-8'}{/if}{if isset($body_classes) && $body_classes|@count} {implode value=$body_classes separator=' '}{/if}{if $hide_left_column} hide-left-column{else} show-left-column{/if}{if $hide_right_column} hide-right-column{else} show-right-column{/if}{if isset($content_only) && $content_only} content_only{/if} lang_{$lang_iso}">
<div id="page">
	<div class="columns-container">
				<div id="columns" class="container">

{capture name=path}{l s='Your addresses' d=$module_name}{/capture}
<div class="box">
	<h1 class="page-subheading">{l s='Your addresses' d=$module_name}</h1>
	<p class="info-title">
        {if isset($id_address) && (isset($smarty.post.alias) || isset($address->alias))}
            {l s='Modify address' d=$module_name}
            {if isset($smarty.post.alias)}
				"{$smarty.post.alias|escape:'html':'UTF-8'}"
            {else}
                {if isset($address->alias)}"{$address->alias|escape:'html':'UTF-8'}"{/if}
            {/if}
        {else}
            {l s='To add a new address, please fill out the form below.' d=$module_name}
        {/if}
	</p>
    {include file="$tpl_dir./errors.tpl"}
	<p class="required"><sup>*</sup>{l s='Required field' d=$module_name}</p>
	<form action="{$link->getModuleLink("$module_name", "address", array())|escape:'quotes':'UTF-8'}" method="post" class="std" id="add_address">
		<!--h3 class="page-subheading">{if isset($id_address)}{l s='Your address' d=$module_name}{else}{l s='New address' d=$module_name}{/if}</h3-->
        {assign var="stateExist" value=false}
        {assign var="postCodeExist" value=false}
        {assign var="dniExist" value=false}
        {assign var="homePhoneExist" value=false}
        {assign var="mobilePhoneExist" value=false}
        {assign var="atLeastOneExists" value=false}
        {foreach from=$ordered_adr_fields item=field_name}
            {if $field_name eq 'company'}
				<div class="form-group">
					<label for="company">{l s='Company' d=$module_name}{if isset($required_fields) && in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
					<input class="form-control validate" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" id="company" name="company" value="{if isset($smarty.post.company)}{$smarty.post.company|escape:'html':'UTF-8'}{else}{if isset($address->company)}{$address->company|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if $field_name eq 'vat_number'}
				<div id="vat_area">
					<div id="vat_number">
						<div class="form-group">
							<label for="vat-number">{l s='VAT number' d=$module_name}{if isset($required_fields) && in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
							<input type="text" class="form-control validate" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" id="vat-number" name="vat_number" value="{if isset($smarty.post.vat_number)}{$smarty.post.vat_number|escape:'html':'UTF-8'}{else}{if isset($address->vat_number)}{$address->vat_number|escape:'html':'UTF-8'}{/if}{/if}" />
						</div>
					</div>
				</div>
            {/if}
            {if $field_name eq 'dni'}
                {assign var="dniExist" value=true}
				<div class="required form-group dni">
					<label for="dni">{l s='Identification number' d=$module_name} <sup>*</sup></label>
					<input class="form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni|escape:'html':'UTF-8'}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}" />
					<span class="form_info">{l s='DNI / NIF / NIE' d=$module_name}</span>
				</div>
            {/if}
            {if $field_name eq 'firstname'}
				<div class="required form-group">
					<label for="firstname">{l s='First name' d=$module_name} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" name="firstname" id="firstname" value="{if isset($smarty.post.firstname)}{$smarty.post.firstname|escape:'html':'UTF-8'}{else}{if isset($address->firstname)}{$address->firstname|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if $field_name eq 'lastname'}
				<div class="required form-group">
					<label for="lastname">{l s='Last name' d=$module_name} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" id="lastname" name="lastname" value="{if isset($smarty.post.lastname)}{$smarty.post.lastname|escape:'html':'UTF-8'}{else}{if isset($address->lastname)}{$address->lastname|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if $field_name eq 'address1'}
				<div class="required form-group">
					<label for="address1">{l s='Address' d=$module_name} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" id="address1" name="address1" value="{if isset($smarty.post.address1)}{$smarty.post.address1|escape:'html':'UTF-8'}{else}{if isset($address->address1)}{$address->address1|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if $field_name eq 'address2'}
				<div class="required form-group">
					<label for="address2">{l s='Address (Line 2)' d=$module_name}{if isset($required_fields) && in_array($field_name, $required_fields)} <sup>*</sup>{/if}</label>
					<input class="validate form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" id="address2" name="address2" value="{if isset($smarty.post.address2)}{$smarty.post.address2|escape:'html':'UTF-8'}{else}{if isset($address->address2)}{$address->address2|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if $field_name eq 'postcode'}
                {assign var="postCodeExist" value=true}
				<div class="required postcode form-group unvisible">
					<label for="postcode">{l s='Zip/Postal Code' d=$module_name} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if $field_name eq 'city'}
				<div class="required form-group">
					<label for="city">{l s='City' d=$module_name} <sup>*</sup></label>
					<input class="is_required validate form-control" data-validate="{$address_validation.$field_name.validate|escape:'html':'UTF-8'}" type="text" name="city" id="city" value="{if isset($smarty.post.city)}{$smarty.post.city|escape:'html':'UTF-8'}{else}{if isset($address->city)}{$address->city|escape:'html':'UTF-8'}{/if}{/if}" maxlength="64" />
				</div>
                {* if customer hasn't update his layout address, country has to be verified but it's deprecated *}
            {/if}
            {if $field_name eq 'Country:name' || $field_name eq 'country' || $field_name eq 'Country:iso_code'}
				<div class="required form-group">
					<label for="id_country">{l s='Country' d=$module_name} <sup>*</sup></label>
					<select id="id_country" class="form-control" name="id_country">
						{$countries_list|escape:'html':'UTF-8'}
						<script type="text/javascript">
                            var text = '{$countries_list|regex_replace:"/[\r\n]/" : ""}';
                            // var decoded = $('<div/>').html(text).text();
                            $('#id_country').html(text);
						</script>

					</select>
				</div>
            {/if}
            {if $field_name eq 'State:name'}
                {assign var="stateExist" value=true}
				<div class="required id_state form-group">
					<label for="id_state">{l s='State' d=$module_name} <sup>*</sup></label>
					<select name="id_state" id="id_state" class="form-control">
						<option value="">-</option>
					</select>
				</div>
            {/if}
            {if $field_name eq 'phone'}
                {assign var="homePhoneExist" value=true}
				<div class="form-group phone-number">
					<label for="phone">{l s='Home phone' d=$module_name}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control" data-validate="{$address_validation.phone.validate|escape:'html':'UTF-8'}" type="tel" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'html':'UTF-8'}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"  />
				</div>
				<div class="clearfix"></div>
            {/if}
            {if $field_name eq 'phone_mobile'}
                {assign var="mobilePhoneExist" value=true}
				<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
					<label for="phone_mobile">{l s='Mobile phone' d=$module_name}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
					<input class="validate form-control" data-validate="{$address_validation.phone_mobile.validate|escape:'html':'UTF-8'}" type="tel" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape:'html':'UTF-8'}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}" />
				</div>
            {/if}
            {if ($field_name eq 'phone_mobile') || ($field_name eq 'phone_mobile') && !isset($atLeastOneExists) && isset($one_phone_at_least) && $one_phone_at_least}
                {assign var="atLeastOneExists" value=true}
				<p class="inline-infos required">** {l s='You must register at least one phone number.' d=$module_name}</p>
            {/if}
        {/foreach}
        {if !$postCodeExist}
			<div class="required postcode form-group unvisible">
				<label for="postcode">{l s='Zip/Postal Code' d=$module_name} <sup>*</sup></label>
				<input class="is_required validate form-control" data-validate="{$address_validation.postcode.validate|escape:'html':'UTF-8'}" type="text" id="postcode" name="postcode" value="{if isset($smarty.post.postcode)}{$smarty.post.postcode|escape:'html':'UTF-8'}{else}{if isset($address->postcode)}{$address->postcode|escape:'html':'UTF-8'}{/if}{/if}" />
			</div>
        {/if}
        {if !$stateExist}
			<div class="required id_state form-group unvisible">
				<label for="id_state">{l s='State' d=$module_name} <sup>*</sup></label>
				<select name="id_state" id="id_state" class="form-control">
					<option value="">-</option>
				</select>
			</div>
        {/if}
        {if !$dniExist}
			<div class="required dni form-group unvisible">
				<label for="dni">{l s='Identification number' d=$module_name} <sup>*</sup></label>
				<input class="is_required form-control" data-validate="{$address_validation.dni.validate|escape:'html':'UTF-8'}" type="text" name="dni" id="dni" value="{if isset($smarty.post.dni)}{$smarty.post.dni|escape:'html':'UTF-8'}{else}{if isset($address->dni)}{$address->dni|escape:'html':'UTF-8'}{/if}{/if}" />
				<span class="form_info">{l s='DNI / NIF / NIE' d=$module_name}</span>
			</div>
        {/if}
		<div class="form-group">
			<label for="other">{l s='Additional information' d=$module_name}</label>
			<textarea class="validate form-control" data-validate="{$address_validation.other.validate|escape:'html':'UTF-8'}" id="other" name="other" cols="26" rows="3" >{if isset($smarty.post.other)}{$smarty.post.other|escape:'html':'UTF-8'}{else}{if isset($address->other)}{$address->other|escape:'html':'UTF-8'}{/if}{/if}</textarea>
		</div>
        {if !$homePhoneExist}
			<div class="form-group phone-number">
				<label for="phone">{l s='Home phone' d=$module_name}</label>
				<input class="{if isset($one_phone_at_least) && $one_phone_at_least}is_required{/if} validate form-control" data-validate="{$address_validation.phone.validate|escape:'html':'UTF-8'}" type="tel" id="phone" name="phone" value="{if isset($smarty.post.phone)}{$smarty.post.phone|escape:'html':'UTF-8'}{else}{if isset($address->phone)}{$address->phone|escape:'html':'UTF-8'}{/if}{/if}"  />
			</div>
        {/if}
		<div class="clearfix"></div>
        {if !$mobilePhoneExist}
			<div class="{if isset($one_phone_at_least) && $one_phone_at_least}required {/if}form-group">
				<label for="phone_mobile">{l s='Mobile phone' d=$module_name}{if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}</label>
				<input class="validate form-control" data-validate="{$address_validation.phone_mobile.validate|escape:'html':'UTF-8'}" type="tel" id="phone_mobile" name="phone_mobile" value="{if isset($smarty.post.phone_mobile)}{$smarty.post.phone_mobile|escape:'html':'UTF-8'}{else}{if isset($address->phone_mobile)}{$address->phone_mobile|escape:'html':'UTF-8'}{/if}{/if}" />
			</div>
        {/if}
        {if isset($one_phone_at_least) && $one_phone_at_least && !$atLeastOneExists}
			<p class="inline-infos required">{l s='You must register at least one phone number.' d=$module_name}</p>
        {/if}
		<div class="required form-group" id="adress_alias">
			<label for="alias">{l s='Please assign an address title for future reference.' d=$module_name} <sup>*</sup></label>
			<input type="text" id="alias" class="is_required validate form-control" data-validate="{$address_validation.alias.validate|escape:'html':'UTF-8'}" name="alias" value="{if isset($smarty.post.alias)}{$smarty.post.alias|escape:'html':'UTF-8'}{elseif isset($address->alias)}{$address->alias|escape:'html':'UTF-8'}{elseif !$select_address}{l s='My address' d=$module_name}{/if}" />
		</div>
		<p class="submit2">
            {if isset($id_address)}<input type="hidden" name="id_address" value="{$id_address|intval}" />{/if}
            {if isset($back)}<input type="hidden" name="back" value="{$back|escape:'html':'UTF-8'}" />{/if}
            {if isset($mod)}<input type="hidden" name="mod" value="{$mod|escape:'html':'UTF-8'}" />{/if}
            {if isset($select_address)}<input type="hidden" name="select_address" value="{$select_address|intval}" />{/if}
			<input type="hidden" name="token" value="{$token|escape:'html':'UTF-8'}" />
			<button type="submit" name="submitAddress" id="submitAddress" class="btn btn-default button button-medium">
				<span>
					{l s='Save' d=$module_name}
					<i class="icon-chevron-right right"></i>
				</span>
			</button>
		</p>
	</form>
</div>
{*<ul class="footer_links clearfix">*}
{*<li>*}
{*<a class="btn btn-defaul button button-small" href="{$link->getPageLink('addresses', true)|escape:'html':'UTF-8'}">*}
{*<span><i class="icon-chevron-left"></i> {l s='Back to your addresses'}</span>*}
{*</a>*}
{*</li>*}
{*</ul>*}
{strip}
    {if isset($smarty.post.id_state) && $smarty.post.id_state}
        {addJsDef idSelectedState=$smarty.post.id_state|intval}
    {elseif isset($address->id_state) && $address->id_state}
        {addJsDef idSelectedState=$address->id_state|intval}
    {else}
        {addJsDef idSelectedState=false}
    {/if}
    {if isset($smarty.post.id_country) && $smarty.post.id_country}
        {addJsDef idSelectedCountry=$smarty.post.id_country|intval}
    {elseif isset($address->id_country) && $address->id_country}
        {addJsDef idSelectedCountry=$address->id_country|intval}
    {else}
        {addJsDef idSelectedCountry=false}
    {/if}
    {if isset($countries)}
        {addJsDef countries=$countries}
    {/if}
    {if isset($vatnumber_ajax_call) && $vatnumber_ajax_call}
        {addJsDef vatnumber_ajax_call=$vatnumber_ajax_call}
    {/if}
{/strip}

</div>
</div>
</div>
</body>

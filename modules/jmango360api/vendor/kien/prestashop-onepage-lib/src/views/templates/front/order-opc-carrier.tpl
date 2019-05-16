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

<script type="text/javascript">
    // <![CDATA[
    var orderProcess = 'order';
    var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"|escape:'html'}';
    var currencyRate = '{$currencyRate|floatval}';
    var currencyFormat = '{$currencyFormat|intval}';
    var currencyBlank = '{$currencyBlank|intval}';
    var txtProduct = "{l s='product' d=$module_name js=1}";
    var txtProducts = "{l s='products' d=$module_name js=1}";
    var orderUrl = '{$link->getPageLink("order", true)|escape:'html'}';
    var msg = "{l s='You must agree to the terms of service before continuing.' d=$module_name js=1}";

    {literal}
    function acceptCGV() {
        if ($('#cgv').length && !$('input#cgv:checked').length) {
            alert(msg);
            return false;
        }
        else
            return true;
    }
    {/literal}
    //]]>
</script>

<div data-role="content" id="delivery_choose">
    <h3 class="bg">{l s='Choose your delivery method' d=$module_name}</h3>
    <fieldset data-role="controlgroup">
        {if isset($delivery_option_list)}
            {foreach $delivery_option_list as $id_address => $option_list}
                {foreach $option_list as $key => $option}
                    <div class="delivery_option {if ($option@index % 2)}alternate_{/if}item">
                        <input class="delivery_option_radio" type="radio" name="delivery_option[{$id_address|intval}]"
                               onchange="{if $opc}updateCarrierSelectionAndGift();{else}updateExtraCarrier('{$key|intval}', {$id_address|intval});{/if}"
                               id="delivery_option_{$id_address|intval}_{$option@index|escape:'html':'UTF-8'}" value="{$key|intval}"
                               {if isset($delivery_option[$id_address]) && $delivery_option[$id_address|intval] == $key|intval}checked="checked"{/if} />
                        <label for="delivery_option_{$id_address|intval}_{$option@index|escape:'html':'UTF-8'}">
                            <div class="ui-grid-a">
							<span class="resume ui-block-a">
								<div class="ui-grid-b">
									<p class="delivery_option_logo ui-block-a">
										{foreach $option.carrier_list as $carrier}
                                            {if $carrier.logo}
                                                <img src="{$carrier.logo|escape:'url'}" alt="{$carrier.instance->name|escape:'html':'UTF-8'}"/>
                                            {else if !$option.unique_carrier}
                                                {$carrier.instance->name|escape:'html':'UTF-8'}
                                                {if !$carrier@last} - {/if}
                                            {/if}
                                        {/foreach}
									</p>
									<div class="ui-block-b" style="padding-left:4px;">
									{if $option.unique_carrier}
                                        {foreach $option.carrier_list as $carrier}
                                            <div class="delivery_option_title">{$carrier.instance->name|escape:'html':'UTF-8'}</div>
                                        {/foreach}
                                    {/if}
									</div>
									<div class="ui-block-c">
										<div class="delivery_option_price">
											{if $option.total_price_with_tax && !$free_shipping}
                                                {if $use_taxes == 1}
                                                    {convertPrice price=$option.total_price_with_tax} {l s='(tax incl.)' d=$module_name}
                                                {else}
                                                    {convertPrice price=$option.total_price_without_tax} {l s='(tax excl.)' d=$module_name}
                                                {/if}
                                            {else}
                                                {l s='Free' d=$module_name}
                                            {/if}
										</div>
									</div>
								</div>
							</span>
                                <span class="delivery_option_carrier_desc ui-block-b {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}selected{/if} {if $option.unique_carrier}not-displayable{/if}">
								{foreach $option.carrier_list as $carrier}
                                    <tr>
									{if !$option.unique_carrier}
                                        <td class="first_item">
									<input type="hidden" value="{$carrier.instance->id|intval}" name="id_carrier"/>
                                            {if $carrier.logo}
                                                <img src="{$carrier.logo|escape:'url'}" alt="{$carrier.instance->name|escape:'html':'UTF-8'}"/>
                                            {/if}
									</td>
                                        <td>
										{$carrier.instance->name|escape:'html':'UTF-8'}
									</td>
                                    {/if}
                                        <td {if $option.unique_carrier}class="first_item" colspan="2"{/if}>
										<input type="hidden" value="{$carrier.instance->id|intval}" name="id_carrier"/>
                                            {if isset($carrier.instance->delay[$cookie->id_lang])}
                                                {$carrier.instance->delay[$cookie->id_lang]|intval}
                                                <br/>
                                            {if count($carrier.product_list) <= 1}
                                                ({l s='Product concerned:' d=$module_name}
                                            {else}
                                                ({l s='Products concerned:' d=$module_name}
                                            {/if}
                                                {* This foreach is on one line, to avoid tabulation in the title attribute of the acronym *}
                                                {foreach $carrier.product_list as $product}
                                                {if $product@index == 4}<acronym title="{/if}{if $product@index >= 4}{$product.name}{if !$product@last}, {else}">...</acronym>){/if}{else}{$product.name}{if !$product@last}, {else}){/if}{/if}{/foreach}
                                            {/if}
									</td>
								</tr>
                                {/foreach}
							</span>
                            </div>
                        </label>
                    </div>
                {/foreach}
            {/foreach}
        {/if}
    </fieldset>
    <fieldset data-role="fieldcontain">
        <input type="checkbox" name="same" id="recyclable" value="1" class="delivery_option_radio"
               {if $recyclable == 1}checked="checked"{/if} autocomplete="off"/>
        <label for="recyclable">{l s='I agree to receive my order in recycled packaging' d=$module_name}.</label>
    </fieldset>

    {if $giftAllowed}
        <h3 class="gift_title">{l s='Gift' d=$module_name}</h3>
        <p class="checkbox">
            <input type="checkbox" name="gift" id="gift" value="1" {if $cart->gift == 1}checked="checked"{/if}
                   autocomplete="off"/>
            <label for="gift">{l s='I would like my order to be gift wrapped.' d=$module_name}</label>
            <br/>
            {if $gift_wrapping_price > 0}
                ({l s='Additional cost of' d=$module_name}
                <span class="price" id="gift-price">
					{if $priceDisplay == 1}{convertPrice price=$total_wrapping_tax_exc_cost}{else}{convertPrice price=$total_wrapping_cost}{/if}
				</span>
                {if $use_taxes}{if $priceDisplay == 1} {l s='(tax excl.)' d=$module_name}{else} {l s='(tax incl.)' d=$module_name}{/if}{/if})
            {/if}
        </p>
        <p id="gift_div" class="textarea">
            <label for="gift_message">{l s='If you\'d like, you can add a note to the gift:' d=$module_name}</label>
            <textarea rows="5" cols="35" id="gift_message"
                      name="gift_message">{$cart->gift_message|escape:'htmlall':'UTF-8'}</textarea>
        </p>
    {/if}

    <h3 class="bg">{l s='Terms of service' d=$module_name}</h3>
    <fieldset data-role="fieldcontain" id="cgv_checkbox">
        <input type="checkbox" value="1" id="cgv" name="cgv" {if $checkedTOS}checked="checked"{/if} autocomplete="off"/>
        <label for="cgv">{l s='I agree to the terms of service and will adhere to them unconditionally.' d=$module_name}</label>
    </fieldset>
    <p class="lnk_CGV"><a href="{$link_conditions|escape:'url'}" data-ajax="false">{l s='(Read Terms of Service)' d=$module_name}</a></p>
</div>

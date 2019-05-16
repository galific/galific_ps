<head>
    <style>
        h1.page-heading.step-num { display: none; }
        #opc_payment_methods {
            padding-left: 10px;
            padding-right: 10px; padding-top: 10px;
        }
    </style>
</head>

{assign var="back_order_page" value="order-opc.php"}
{if $PS_CATALOG_MODE}
	{capture name=path}{l s='Your shopping cart' mod='kbmobileapp'}{/capture}
	<h2 id="cart_title">{l s='Your shopping cart' mod='kbmobileapp'}</h2>
	<p class="alert alert-warning">{l s='Your new order was not accepted.' mod='kbmobileapp'}</p>
{else}
	{if $productNumber}
		<!-- Payment -->
		{include file="$tpl_dir./order-payment.tpl"}
		<!-- END Payment -->
	{else}
		{capture name=path}{l s='Your shopping cart' mod='kbmobileapp'}{/capture}
		<h2 class="page-heading">{l s='Your shopping cart' mod='kbmobileapp'}</h2>
		{include file="$tpl_dir./errors.tpl"}
		<p class="alert alert-warning">{l s='Your shopping cart is empty.' mod='kbmobileapp'}</p>
	{/if}
{strip}
{addJsDef imgDir=$img_dir}
{addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
{addJsDef orderOpcUrl=$link->getPageLink("order-opc", true)|escape:'quotes':'UTF-8'}
{addJsDef historyUrl=$link->getPageLink("history", true)|escape:'quotes':'UTF-8'}
{addJsDef guestTrackingUrl=$link->getPageLink("guest-tracking", true)|escape:'quotes':'UTF-8'}
{addJsDef addressUrl=$link->getPageLink("address", true, NULL, "back={$back_order_page}")|escape:'quotes':'UTF-8'}
{addJsDef orderProcess='order-opc'}
{addJsDef guestCheckoutEnabled=$PS_GUEST_CHECKOUT_ENABLED|intval}
{addJsDef displayPrice=$priceDisplay}
{addJsDef taxEnabled=$use_taxes}
{addJsDef conditionEnabled=$conditions|intval}
{addJsDef vat_management=$vat_management|intval}
{addJsDef errorCarrier=$errorCarrier|@addcslashes:'\''}
{addJsDef errorTOS=$errorTOS|@addcslashes:'\''}
{addJsDef checkedCarrier=$checked|intval}
{addJsDef addresses=array()}
{addJsDef isVirtualCart=$isVirtualCart|intval}
{addJsDef isPaymentStep=$isPaymentStep|intval}
{addJsDefL name=txtWithTax}{l s='(tax incl.)' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtWithoutTax}{l s='(tax excl.)' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtHasBeenSelected}{l s='has been selected' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtNoCarrierIsSelected}{l s='No carrier has been selected' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtNoCarrierIsNeeded}{l s='No carrier is needed for this order' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtConditionsIsNotNeeded}{l s='You do not need to accept the Terms of Service for this order.' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtTOSIsAccepted}{l s='The service terms have been accepted' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtTOSIsNotAccepted}{l s='The service terms have not been accepted' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtThereis}{l s='There is' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtErrors}{l s='Error(s)' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtDeliveryAddress}{l s='Delivery address' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtInvoiceAddress}{l s='Invoice address' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtModifyMyAddress}{l s='Modify my address' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtInstantCheckout}{l s='Instant checkout' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtSelectAnAddressFirst}{l s='Please start by selecting an address.' mod='kbmobileapp' js=1}{/addJsDefL}
{addJsDefL name=txtFree}{l s='Free' mod='kbmobileapp' js=1}{/addJsDefL}

{capture}{if $back}&mod={$back|urlencode|escape:'html':'UTF-8'}{/if}{/capture}
{capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
{addJsDef addressUrl=$smarty.capture.addressUrl}
{capture}{'&multi-shipping=1'|urlencode|escape:'html':'UTF-8'}{/capture}
{addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
{capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='|escape:'html':'UTF-8'}{/capture}
{addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
{addJsDef opc=$opc|boolval}
{capture}<h3 class="page-subheading">{l s='Your billing address' mod='kbmobileapp' js=1}</h3>{/capture}
{addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''|escape:'html':'UTF-8'}{/addJsDefL}
{capture}<h3 class="page-subheading">{l s='Your delivery address' mod='kbmobileapp' js=1}</h3>{/capture}
{addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''|escape:'html':'UTF-8'}{/addJsDefL}
{capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd|escape:'html':'UTF-8'}" title="{l s='Update' mod='kbmobileapp' js=1}"><span>{l s='Update' mod='kbmobileapp' js=1}<i class="icon-chevron-right right"></i></span></a>{/capture}
{addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''|escape:'html':'UTF-8'}{/addJsDefL}
{/strip}
{/if}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Hook tpl file
*}
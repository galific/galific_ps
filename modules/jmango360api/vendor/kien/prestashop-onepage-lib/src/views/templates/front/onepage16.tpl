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
{literal}
    <style type="text/css">
        body {
            padding: 15px;
        }
    </style>
{/literal}

{literal}
    <script type="text/javascript">
        $(function() {
            (function() {
                $('#orderopc').attr("id", "order-opc");

                $('.step-num').each(function (i, step) {
                    $(step).children().eq(0).html(i + 1);
                });
            })();
        });
    </script>
{/literal}

{if $opc}
    {assign var="back_order_page" value="order-opc.php"}
{else}
    {assign var="back_order_page" value="order.php"}
{/if}

{if $PS_CATALOG_MODE}
    {capture name=path}{l s='Your shopping cart' d=$module_name}{/capture}
    <h2 id="cart_title">{l s='Your shopping cart' d=$module_name}</h2>
    <p class="alert alert-warning">{l s='Your new order was not accepted.' d=$module_name}</p>
{else}
    {if $productNumber}
        <!-- Carrier -->
        {include file="$tpl_dir./order-carrier.tpl"}
        <!-- END Carrier -->

        <!-- Payment -->
        {include file="$tpl_dir./order-payment.tpl"}
        <!-- END Payment -->
    {else}
        {capture name=path}{l s='Your shopping cart' d=$module_name}{/capture}
        <h2 class="page-heading">{l s='Your shopping cart' d=$module_name}</h2>
        {include file="$tpl_dir./errors.tpl"}
        <p class="alert alert-warning">{l s='Your shopping cart is empty.' d=$module_name}</p>
    {/if}
    {strip}
        {addJsDef imgDir=$img_dir}
        {addJsDef authenticationUrl=$link->getPageLink("authentication", true)|escape:'quotes':'UTF-8'}
        {addJsDef orderOpcUrl=$link->getModuleLink("$module_name", "orderopc", array())|escape:'quotes':'UTF-8'}
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
        {addJsDefL name=txtWithTax}{l s='(tax incl.)' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtWithoutTax}{l s='(tax excl.)' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtHasBeenSelected}{l s='has been selected' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtNoCarrierIsSelected}{l s='No carrier has been selected' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtNoCarrierIsNeeded}{l s='No carrier is needed for this order' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtConditionsIsNotNeeded}{l s='You do not need to accept the Terms of Service for this order.' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtTOSIsAccepted}{l s='The service terms have been accepted' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtTOSIsNotAccepted}{l s='The service terms have not been accepted' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtThereis}{l s='There is' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtErrors}{l s='Error(s)' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtDeliveryAddress}{l s='Delivery address' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtInvoiceAddress}{l s='Invoice address' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtModifyMyAddress}{l s='Modify my address' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtInstantCheckout}{l s='Instant checkout' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtSelectAnAddressFirst}{l s='Please start by selecting an address.' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtFree}{l s='Free' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtProduct}{l s='product' d=$module_name js=1}{/addJsDefL}
        {addJsDefL name=txtProducts}{l s='products' d=$module_name js=1}{/addJsDefL}

        {capture}{if $back}&mod={$back|urlencode|escape:'html':'UTF-8'}{/if}{/capture}
        {capture name=addressUrl}{$link->getPageLink('address', true, NULL, 'back='|cat:$back_order_page|cat:'?step=1'|cat:$smarty.capture.default)|escape:'quotes':'UTF-8'}{/capture}
        {addJsDef addressUrl=$smarty.capture.addressUrl}
        {capture}{'&multi-shipping=1'|urlencode|escape:'html':'UTF-8'}{/capture}
        {addJsDef addressMultishippingUrl=$smarty.capture.addressUrl|cat:$smarty.capture.default}
        {capture name=addressUrlAdd}{$smarty.capture.addressUrl|cat:'&id_address='|escape:'html':'UTF-8'}{/capture}
        {addJsDef addressUrlAdd=$smarty.capture.addressUrlAdd}
        {addJsDef opc=$opc|boolval}
        {capture}<h3 class="page-subheading">{l s='Your billing address' d=$module_name js=1}</h3>{/capture}
        {addJsDefL name=titleInvoice}{$smarty.capture.default|@addcslashes:'\''|escape:'html':'UTF-8'}{/addJsDefL}
        {capture}<h3 class="page-subheading">{l s='Your delivery address' d=$module_name js=1}</h3>{/capture}
        {addJsDefL name=titleDelivery}{$smarty.capture.default|@addcslashes:'\''|escape:'html':'UTF-8'}{/addJsDefL}
        {capture}<a class="button button-small btn btn-default" href="{$smarty.capture.addressUrlAdd|escape:'url'}"
                    title="{l s='Update' d=$module_name js=1}"><span>{l s='Update' d=$module_name js=1}<i class="icon-chevron-right right"></i></span>
            </a>{/capture}
        {addJsDefL name=liUpdate}{$smarty.capture.default|@addcslashes:'\''|escape:'html':'UTF-8'}{/addJsDefL}
    {/strip}
{/if}

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
        .ui-icon-radio-on {
            background-position: -802px 50%;
        }

        .ui-icon-radio-off {
            background-position: -762px 50%;
        }

        .ui-icon-checkbox-off {
            background-position: -762px 50%;
        }

        .ui-icon-checkbox-on {
            background-position: -723px 50%;
        }

        #header #block_cart {
            display: none;
        }
    </style>
{/literal}

{capture assign='page_title'}{l s='Order' d=$module_name}{/capture}

{if $PS_CATALOG_MODE}
    <p class="warning">{l s='This store does not accept orders.' d=$module_name}</p>
{else}
    <script type="text/javascript">
        // <![CDATA[
        var imgDir = '{$img_dir|escape:'url'}';
        var authenticationUrl = '{$link->getPageLink("authentication", true)|addslashes|escape:'url'}';
        var orderOpcUrl = '{$link->getModuleLink($module_name, 'orderopc', array(), true)|addslashes|escape:'url'}';
        var historyUrl = '{$link->getPageLink("history", true)|addslashes|escape:'url'}';
        var guestTrackingUrl = '{$link->getPageLink("guest-tracking", true)|addslashes|escape:'url'}';
        var addressUrl = '{$link->getPageLink("address", true)|addslashes|escape:'url'}';
        var orderProcess = 'order-opc';
        var guestCheckoutEnabled = {$PS_GUEST_CHECKOUT_ENABLED|intval};
        var currencySign = '{$currencySign|html_entity_decode:2:"UTF-8"|escape:'html'}';
        var currencyRate = '{$currencyRate|floatval}';
        var currencyFormat = '{$currencyFormat|intval}';
        var currencyBlank = '{$currencyBlank|intval}';
        var displayPrice = {$priceDisplay|escape:'html':'UTF-8'};
        var taxEnabled = {$use_taxes|escape:'html':'UTF-8'};
        var conditionEnabled = {$conditions|intval};
        var countries = new Array();
        var countriesNeedIDNumber = new Array();
        var countriesNeedZipCode = new Array();
        var vat_management = {$vat_management|intval};

        var txtWithTax = "{l s='(tax incl.)' d=$module_name js=1}";
        var txtWithoutTax = "{l s='(tax excl.)' d=$module_name js=1}";
        var txtHasBeenSelected = "{l s='has been selected' d=$module_name js=1}";
        var txtNoCarrierIsSelected = "{l s='No carrier has been selected' d=$module_name js=1}";
        var txtNoCarrierIsNeeded = "{l s='No carrier is needed for this order' d=$module_name js=1}";
        var txtConditionsIsNotNeeded = "{l s='You do not need to accept the Terms of Service for this order.' d=$module_name js=1}";
        var txtTOSIsAccepted = "{l s='The service terms have been accepted' d=$module_name js=1}";
        var txtTOSIsNotAccepted = "{l s='The service terms have not been accepted' d=$module_name js=1}";
        var txtThereis = "{l s='There is' d=$module_name js=1}";
        var txtErrors = "{l s='Error(s)' d=$module_name js=1}";
        var txtDeliveryAddress = "{l s='Delivery address' d=$module_name js=1}";
        var txtInvoiceAddress = "{l s='Invoice address' d=$module_name js=1}";
        var txtModifyMyAddress = "{l s='Modify my address' d=$module_name js=1}";
        var txtInstantCheckout = "{l s='Instant checkout' d=$module_name js=1}";
        var errorCarrier = "{$errorCarrier|escape:'html':'UTF-8'}";
        var errorTOS = "{$errorTOS|escape:'html':'UTF-8'}";
        var checkedCarrier = "{if isset($checked)}{$checked|escape:'html':'UTF-8'}{else}0{/if}";
        var freeShippingTranslation = "{l s='Free shipping!' d=$module_name js=1}";

        var addresses = new Array();
        var isLogged = {$isLogged|intval};
        var isGuest = {$isGuest|intval};
        var isVirtualCart = {$isVirtualCart|intval};
        var isPaymentStep = {$isPaymentStep|intval};
        //]]>
    </script>
    {* if there is at least one product : checkout process *}
    {if $productNumber}
        {if $isLogged AND !$isGuest}
            <!-- Carrier -->
            {include file="./order-opc-carrier.tpl"}
            <!-- END Carrier -->
            <!-- Payment -->
            {include file="./order-opc-payment.tpl"}
            <!-- END Payment -->
        {/if}
        {* else : warning *}
    {else}
        <p class="warning">{l s='Your shopping cart is empty.' d=$module_name}</p>
    {/if}
{/if}

{*
* Amazon Advanced Payment APIs Modul
* for Support please visit www.patworx.de
*
*  @author patworx multimedia GmbH <service@patworx.de>
*  In collaboration with alkim media
*  @copyright  2013-2015 patworx multimedia GmbH
*  @license    Released under the GNU General Public License
*}
{extends file='page.tpl'}

{block name='page_content'}
{nocache}

<h4>{l s='Select your delivery address and your payment method from your Amazon account, to go through the checkout quickly and easily.' mod='amzpayments'}</h4>

<div class="row">
	<div class="col-xs-12 col-sm-6">
		<div class="row">
			<div class="col-xs-12" id="addressBookWidgetDivBs">
			</div>
			<div class="col-xs-12" id="addressMissings">		
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-sm-6">
		<div class="row">
			<div class="col-xs-12" id="walletWidgetDivBs">
			</div>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-xs-12 text-right addressWalletSubmit">
		<button type="button" id="submitAddress" name="submitAddress" class="button btn btn-default button-medium" style="display:none;" data-continue="{l s='Continue' mod='amzpayments'}" data-change="{l s='Save changes' mod='amzpayments'}">
			<span>
				{l s='Continue' mod='amzpayments'}
			</span>
		</button>
	</div>
</div>

{literal}
<script>
jQuery(document).ready(function($) {

	options = { scope: 'payments:widget', popup: true, interactive: 'never' };
	amazon.Login.authorize(options, function(response) {
		if (response.error) { 
			loginOptions =  {scope: 'profile postal_code payments:widget payments:shipping_address payments:billing_address', popup: !useRedirect, state: '' };
			amazon.Login.authorize (loginOptions, (useRedirect ? redirectURL : function(response) {
				jQuery.ajax({
		    				type: 'GET',
		            	    url: REDIRECTAMZ,
		                	data: 'ajax=true&method=setsession&access_token=' + response.access_token,
			                success: function(htmlcontent){
			                  	location.reload();
			                }
				});
			})
			);
		}
	});

	new OffAmazonPayments.Widgets.AddressBook({
		sellerId: '{/literal}{$sellerID|escape:'htmlall':'UTF-8'}{literal}',
		{/literal}{if isset($amz_session) && $amz_session != ''}{literal}amazonOrderReferenceId: '{/literal}{$amz_session|escape:'htmlall':'UTF-8'}{literal}', {/literal}{/if}{literal}
		onOrderReferenceCreate: function(orderReference) {			
			 amazonOrderReferenceId = orderReference.getAmazonOrderReferenceId();
		},
		onAddressSelect: function(orderReference) {
			updateAddressSelection(amazonOrderReferenceId);
		},
		{/literal}{if isset($widgetreadonly)}{literal}		
		displayMode: "Read",
		{/literal}{/if}{literal}
		design: {
			designMode: 'responsive'
		},
		onError: function(error) {
			console.log(error.getErrorCode());
			console.log(error.getErrorMessage());
		}
	}).bind("addressBookWidgetDivBs");
	new OffAmazonPayments.Widgets.Wallet({
		sellerId: '{/literal}{$sellerID|escape:'htmlall':'UTF-8'}{literal}',
		{/literal}{if isset($amz_session) && $amz_session != ''}{literal}amazonOrderReferenceId: '{/literal}{$amz_session|escape:'htmlall':'UTF-8'}{literal}', {/literal}{/if}{literal}
		design: {
			designMode: 'responsive'
		},
		onPaymentSelect: function(orderReference) {
		},
		onError: function(error) {
			console.log(error.getErrorMessage());
		}
	}).bind("walletWidgetDivBs");	
});


function updateAddressSelection(amazonOrderReferenceId)
{
	$("#submitAddress").fadeOut();
	var idAddress_delivery = 0;
	var idAddress_invoice = idAddress_delivery;
	var returnback = {/literal}'{if isset($step)}{$step|escape:'javascript':'UTF-8'}{/if}'{literal};
	
	var additional_fields = '';
	$("#addressMissings .additional_field").each(function() {
		additional_fields += '&add[' + $(this).attr("name") + ']=' + $(this).val();		
	});
	
	$.ajax({
		type: 'POST',
		headers: { "cache-control": "no-cache" },
		url: '{/literal}{$ajaxSetAddressUrl nofilter}{literal}' + '?rand=' + new Date().getTime(),
		async: true,
		cache: false,
		dataType : "json",
		data: 'src=addresswallet&returnback=' + returnback + '&amazonOrderReferenceId=' + amazonOrderReferenceId + '&allow_refresh=1&ajax=true&method=updateAddressesSelected&id_address_delivery=' + idAddress_delivery + '&id_address_invoice=' + idAddress_invoice + additional_fields,
		success: function(jsonData)
		{
			if (jsonData.hasError)
			{
				var errors = '';
				for(var error in jsonData.errors)
					if(error !== 'indexOf')
						errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
				alert(errors);
				
				if (jsonData.fields_html) {
					$("#addressMissings").empty();
					$("#addressMissings").html(jsonData.fields_html);
					$("#submitAddress span").text($("#submitAddress").attr("data-change"));
					$("#submitAddress").fadeIn();
					$("#submitAddress").unbind('click').on('click', function() { updateAddressSelection(amazonOrderReferenceId); });
				}
				
			}
			else
			{
				$("#submitAddress span").text($("#submitAddress").attr("data-continue"));
				$("#submitAddress").fadeIn();
				$("#submitAddress").unbind('click').on('click', function() { window.location.href = jsonData.redirect; });
			}
		},
		error: function(XMLHttpRequest, textStatus, errorThrown) {
			if (textStatus !== 'abort')
				alert("TECHNICAL ERROR: unable to save adresses \n\nDetails:\nError thrown: " + XMLHttpRequest + "\n" + 'Text status: ' + textStatus);
		}
	});
}

</script>
{/literal}

{/nocache}

{/block}
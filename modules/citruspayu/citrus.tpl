<p class="payment_module">
	<a style="display:none" href="#" onclick="launchICP(); return false;" title="{l s='Pay with Citrus' mod='citrus'}">
		<img src="{$module_dir}citrus.gif" alt="{l s='Pay with Citrus' mod='citrus'}" />
		{l s='Pay with Citrus' mod='citrus'}
	</a>
</p>

<form method="post" id="citrus_form" name="TransactionForm" class="hidden">
	<input id="gateway" name="gateway" type="hidden" value="{$gateway}" />	
	<input id="merchantTxnId" name="merchantTxnId" type="hidden" value="{$merchantTxnId}" />	
    <input id="addressState" name="addressState" type="hidden" value="{$state}" />
    <input id="addressCity" name="addressCity" type="hidden" value="{$address->city}" />
    <input id="addressStreet1" name="addressStreet1" type="hidden" value="{$address->address1}" />
    <input id="addressCountry" name="addressCountry" type="hidden" value="{$country}" />
    <input id="addressZip" name="addressZip" type="hidden" value="{$address->postcode}" />
    <input id="firstName" name="firstName" type="hidden" value="{$address->firstname}"  />
    <input id="lastName" name="lastName" type="hidden" value="{$address->lastname}" />
    <input id="phoneNumber" name="phoneNumber" type="hidden" value="{$address->phone}" />
    <input id="email" name="email" type="hidden" value="{$customer->email}" />    
	<input id="vanityUrl" name="vanityUrl" type="hidden" value="{$vanityurl}" />
    <input id="returnUrl" name="returnUrl" type="hidden" value="{$return_url}" />
    <input id="notifyUrl" name="notifyUrl" type="hidden" value="{$notify_url}" />
    <input id="orderAmount" name="orderAmount" type="hidden" value="{$orderAmount}" />
    <input type="hidden" id="secSignature" name="secSignature" value="{$secSignature}" />
    <input type="hidden" id="currency" name="currency" value="{$currency}" />
</form>

<script src="https://checkout-static.citruspay.com/lib/js/jquery.min.js"></script>

<script>

	var sicp = document.createElement("script");
	sicp.id = "context";
	sicp.type = "text/javascript";
	if ('{$gateway}' == 'sandbox')
		sicp.src = "https://sboxcontext.citruspay.com/static/kiwi/app-js/icp.js";
	else
		sicp.src = "https://checkout-static.citruspay.com/kiwi/app-js/icp.min.js";
	jQuery("head").append(sicp);

	

    function launchICP() {
	
		//check if tc checked
		if ($('#conditions_to_approve\\[terms-and-conditions\\]').length > 0)
		{
			if(document.getElementById('conditions_to_approve[terms-and-conditions]').checked == false) {
				alert("You must accept the Terms and Conditions.")
				return;
			}
		}	


        var dataObj = {
            orderAmount: $('#orderAmount').val(),
            currency: $('#currency').val(),
            phoneNumber: $('#phoneNumber').val(),
            email: $('#email').val(),
            merchantTxnId: $('#merchantTxnId').val(),
            secSignature: $('#secSignature').val(),
            firstName: $('#firstName').val(),
            lastName: $('#lastName').val(),
            addressStreet1: $('#addressStreet1').val(),
            addressStreet2: '',
            addressCity: $('#addressCity').val(),
            addressState: $('#addressState').val(),
            addressCountry: $('#addressCountry').val(),
            addressZip: $('#addressZip').val(),
            vanityUrl: $('#vanityUrl').val(),
			returnUrl: $('#returnUrl').val(),
			notifyUrl: $('#notifyUrl').val(),
			mode:"dropAround"
        };

		var configObj;

		if ($('#gateway').val() == "sandbox")
		{

			configObj = {

				icpUrl: "https://sboxcontext.citruspay.com/kiwi/kiwi-popover",

				eventHandler: function (cbObj) {
					if (cbObj.event === 'icpLaunched') {
						console.log('Citrus ICP pop-up is launched');
					} else if (cbObj.event === 'icpClosed') {
						//console.log(JSON.stringify(cbObj.message));
						console.log('Citrus ICP pop-up is closed');
						/*if (cbObj.message.TxStatus == 'SUCCESS')
						{ 
							document.getElementById('citrus_ret').value = JSON.stringify(cbObj.message);
							document.getElementById('frmicp').submit();
						} */
					}
				}
			};
		}
		else
		{
			configObj = {

				eventHandler: function (cbObj) {
					if (cbObj.event === 'icpLaunched') {
						console.log('Citrus ICP pop-up is launched');
					} else if (cbObj.event === 'icpClosed') {
						//console.log(JSON.stringify(cbObj.message));
						console.log('Citrus ICP pop-up is closed');
						/*if (cbObj.message.TxStatus == 'SUCCESS')
						{ 
							document.getElementById('citrus_ret').value = JSON.stringify(cbObj.message);
							document.getElementById('frmicp').submit();
						} */
					}
				}
			};
		}

		if (citrusICP == undefined || citrusICP == null)
			return false;
		
        try {
            citrusICP.launchIcp(dataObj, configObj);
        }
        catch (error) {
            console.log(error);
        }

		return false;
    }

</script>


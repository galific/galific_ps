$(document).ready(function(){
	$('#CITRUSPAYU_PAYUPERCENTAGE').bind('change',function() {
		var val = parseInt(this.value,10);	
		if(val > 100)
		{
			$('input[name=CITRUSPAYU_PAYUPERCENTAGE]').val(100);
			$('input[name=CITRUSPAYU_CITRUSPERCENTAGE]').val(0);
		}
		else if(val < 0)
		{
			$('input[name=CITRUSPAYU_PAYUPERCENTAGE]').val(0);
			$('input[name=CITRUSPAYU_CITRUSPERCENTAGE]').val(100);
		}
		else {
			$('input[name=CITRUSPAYU_CITRUSPERCENTAGE]').val(Math.abs(100 - val));	
		}	
	});

	$('#CITRUSPAYU_CITRUSPERCENTAGE').bind('change',function() {
		var val = parseInt(this.value,10);	
		if(val > 100)
		{
			$('input[name=CITRUSPAYU_CITRUSPERCENTAGE]').val(100);
			$('input[name=CITRUSPAYU_PAYUPERCENTAGE]').val(0);		
		}
		else if(val < 0)
		{
			$('input[name=CITRUSPAYU_CITRUSPERCENTAGE]').val(0);
			$('input[name=CITRUSPAYU_PAYUPERCENTAGE]').val(100);

		}
		else {	
			$('input[name=CITRUSPAYU_PAYUPERCENTAGE]').val(Math.abs(100 - val));	
		}
	});
    
});



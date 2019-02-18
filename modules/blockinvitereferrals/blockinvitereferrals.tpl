{if $isModuleEnabled}
<div id='invtrflfloatbtn'></div>
{literal}<script>	
var invite_referrals = window.invite_referrals || {}; (function() { 
	invite_referrals.auth = { 
  	bid_e : "{/literal}{$encodedParam}{literal}",
	bid : "{/literal}{$brandid}{literal}", email : "{/literal}{$email}{literal}",
	t : {/literal}{$tgNpinTime}{literal},
	t2 : "{/literal}{$idOrder}{literal}", 
	userParams : {'fname' : "{/literal}{$fname}{literal}",
	'lname' : "{/literal}{$lname}{literal}"} };	
var script = document.createElement('script');script.async = true;
script.src = (document.location.protocol == 'https:' ? "//d11yp7khhhspcr.cloudfront.net" : "//cdn.invitereferrals.com") + '/js/invite-referrals-1.0.js';
var entry = document.getElementsByTagName('script')[0];entry.parentNode.insertBefore(script, entry); })();
</script>{/literal}
{/if}
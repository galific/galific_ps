<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:19:10
  from '/var/www/html/modules/zendesk/views/templates/hook/header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69aca6eadb41_52960364',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd9cd25ea905dc979af13cec321487d43202246af' => 
    array (
      0 => '/var/www/html/modules/zendesk/views/templates/hook/header.tpl',
      1 => 1549916868,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c69aca6eadb41_52960364 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/javascript">
	zendesk_subdomain = "<?php echo htmlspecialchars(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['zendesk_subdomain']->value,'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
";
	zendesk_iso = "<?php echo htmlspecialchars(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['zendesk_iso']->value,'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
";

	
	/*<![CDATA[*/window.zEmbed||function(e,t){var n,o,d,i,s,a=[],r=document.createElement("iframe");window.zEmbed=function(){a.push(arguments)},window.zE=window.zE||window.zEmbed,r.src="javascript:false",r.title="",r.role="presentation",(r.frameElement||r).style.cssText="display: none",d=document.getElementsByTagName("script"),d=d[d.length-1],d.parentNode.insertBefore(r,d),i=r.contentWindow,s=i.document;try{o=s}catch(c){n=document.domain,r.src='javascript:var d=document.open();d.domain="'+n+'";void(0);',o=s}o.open()._l=function(){var o=this.createElement("script");n&&(this.domain=n),o.id="js-iframe-async",o.src=e,this.t=+new Date,this.zendeskHost=t,this.zEQueue=a,this.body.appendChild(o)},o.write('<body onload="document._l();">'),o.close()}("https://assets.zendesk.com/embeddable_framework/main.js",zendesk_subdomain+".zendesk.com");/*]]>*/

	zE(function() {
		zE.setLocale(zendesk_iso);
	});
	
<?php echo '</script'; ?>
><?php }
}

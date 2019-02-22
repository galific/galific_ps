<?php
/* Smarty version 3.1.33, created on 2019-02-22 14:23:21
  from '/var/www/html/admin623dgtg6r/themes/default/template/content.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6fb88188f5c8_09952308',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '403277ac60c3efff144566bcb9e7d9bd94928e4d' => 
    array (
      0 => '/var/www/html/admin623dgtg6r/themes/default/template/content.tpl',
      1 => 1547743396,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6fb88188f5c8_09952308 (Smarty_Internal_Template $_smarty_tpl) {
?><div id="ajax_confirmation" class="alert alert-success hide"></div>
<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div>
<?php }
}

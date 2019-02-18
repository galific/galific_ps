<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:19:14
  from 'module:psemailsubscriptionviewst' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69acaa262534_36390563',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '307dc6bd4724d29d1572cc301dd7148e962604ef' => 
    array (
      0 => 'module:psemailsubscriptionviewst',
      1 => 1548364870,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c69acaa262534_36390563 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ft_newsletter col-lg-9 col-md-12 col-sm-12">
	<div class="row">
		<div class="title-newsletter col-md-5 col-xs-12">
			<h2><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Sign up to Newsletter','d'=>'Shop.Theme.Global'),$_smarty_tpl ) );?>
</h2>
			<p class="desc"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Register now to get updates on promotions & coupons.','d'=>'Shop.Theme.Global'),$_smarty_tpl ) );?>
</p>
		</div>
		<div class="col-md-7 col-xs-12">
		  <form action="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['urls']->value['pages']['index'], ENT_QUOTES, 'UTF-8');?>
#footer" method="post">
				<input
				  class="btn btn-primary float-xs-right hidden-xs-down"
				  name="submitNewsletter"
				  type="submit"
				  value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Subscribe','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>
"
				>
				<input
				  class="btn btn-primary float-xs-right hidden-sm-up"
				  name="submitNewsletter"
				  type="submit"
				  value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'OK','d'=>'Shop.Theme.Actions'),$_smarty_tpl ) );?>
"
				>
				<div class="input-wrapper">
				  <input
					name="email"
					type="text"
					value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['value']->value, ENT_QUOTES, 'UTF-8');?>
"
					placeholder="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Your email address','d'=>'Shop.Forms.Labels'),$_smarty_tpl ) );?>
"
					aria-labelledby="block-newsletter-label"
				  >
				</div>
				<input type="hidden" name="action" value="0">
				<div class="clearfix"></div>
			  <?php if ($_smarty_tpl->tpl_vars['msg']->value) {?>
				<p class="alert <?php if ($_smarty_tpl->tpl_vars['nw_error']->value) {?>alert-danger<?php } else { ?>alert-success<?php }?>">
				  <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['msg']->value, ENT_QUOTES, 'UTF-8');?>

				</p>
			  <?php }?>
		  </form>
		</div>
	</div>
</div>
<?php }
}

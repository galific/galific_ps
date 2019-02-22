<?php
/* Smarty version 3.1.33, created on 2019-02-22 14:22:34
  from '/var/www/html/themes/theme_ostromi2/templates/_partials/footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6fb8521d04c6_19855568',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ce1bad03476afa0fa476bcd187bd6c95332134a0' => 
    array (
      0 => '/var/www/html/themes/theme_ostromi2/templates/_partials/footer.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6fb8521d04c6_19855568 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>
<div class="footer-container">
	<div class="footer-top">
		<div class="container">
			<div class="row">
				<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayBlockFooter1'),$_smarty_tpl ) );?>

			</div>
		</div>
	</div>
	<div class="footer-center">
		<div class="container">
			<div class="row">
			<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_18740046765c6fb8521ce588_82953684', 'hook_footer');
?>


			</div>
		</div>
	</div>	
	<div class="container">
		<div class="footer-middle">
			<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_19474820265c6fb8521cf160_35932483', 'hook_footer_before');
?>

		</div>
	</div>
	<div class="footer-bottom">	
		<div class="container">
			<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_15365978335c6fb8521cfb98_40008537', 'hook_footer_after');
?>

		</div>
	</div>
</div>
<?php }
/* {block 'hook_footer'} */
class Block_18740046765c6fb8521ce588_82953684 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer' => 
  array (
    0 => 'Block_18740046765c6fb8521ce588_82953684',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooter'),$_smarty_tpl ) );?>

			<?php
}
}
/* {/block 'hook_footer'} */
/* {block 'hook_footer_before'} */
class Block_19474820265c6fb8521cf160_35932483 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer_before' => 
  array (
    0 => 'Block_19474820265c6fb8521cf160_35932483',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooterBefore'),$_smarty_tpl ) );?>

			<?php
}
}
/* {/block 'hook_footer_before'} */
/* {block 'hook_footer_after'} */
class Block_15365978335c6fb8521cfb98_40008537 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer_after' => 
  array (
    0 => 'Block_15365978335c6fb8521cfb98_40008537',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayFooterAfter'),$_smarty_tpl ) );?>

			<?php
}
}
/* {/block 'hook_footer_after'} */
}

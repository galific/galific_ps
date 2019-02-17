<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:26
  from 'C:\wamp64\www\galific\themes\theme_ostromi2\templates\_partials\footer.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921ba2fafb8_96498299',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2c2294b43a30f4466d7e29b934c26db10d624ddd' => 
    array (
      0 => 'C:\\wamp64\\www\\galific\\themes\\theme_ostromi2\\templates\\_partials\\footer.tpl',
      1 => 1550252731,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6921ba2fafb8_96498299 (Smarty_Internal_Template $_smarty_tpl) {
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
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_12867336135c6921ba2bf925_72536581', 'hook_footer');
?>


			</div>
		</div>
	</div>	
	<div class="container">
		<div class="footer-middle">
			<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_20243573305c6921ba2c93a5_44565574', 'hook_footer_before');
?>

		</div>
	</div>
	<div class="footer-bottom">	
		<div class="container">
			<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_17254611285c6921ba2d70c7_26854925', 'hook_footer_after');
?>

		</div>
	</div>
</div>
<?php }
/* {block 'hook_footer'} */
class Block_12867336135c6921ba2bf925_72536581 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer' => 
  array (
    0 => 'Block_12867336135c6921ba2bf925_72536581',
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
class Block_20243573305c6921ba2c93a5_44565574 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer_before' => 
  array (
    0 => 'Block_20243573305c6921ba2c93a5_44565574',
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
class Block_17254611285c6921ba2d70c7_26854925 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'hook_footer_after' => 
  array (
    0 => 'Block_17254611285c6921ba2d70c7_26854925',
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

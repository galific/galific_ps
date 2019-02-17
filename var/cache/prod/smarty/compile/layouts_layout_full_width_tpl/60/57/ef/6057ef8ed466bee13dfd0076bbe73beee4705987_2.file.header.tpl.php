<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:25
  from 'C:\wamp64\www\galific\themes\theme_ostromi2\templates\_partials\header.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921b9a3a238_38321471',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6057ef8ed466bee13dfd0076bbe73beee4705987' => 
    array (
      0 => 'C:\\wamp64\\www\\galific\\themes\\theme_ostromi2\\templates\\_partials\\header.tpl',
      1 => 1550252731,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6921b9a3a238_38321471 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
?>

<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_8317537285c6921b9a1c2e8_48707993', 'header_top');
?>

<?php }
/* {block 'header_top'} */
class Block_8317537285c6921b9a1c2e8_48707993 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'header_top' => 
  array (
    0 => 'Block_8317537285c6921b9a1c2e8_48707993',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

  <div class="container">
       <div class="row" style="padding: 15px;">
		<div class="header_logo col-left col col-lg-3 col-md-12 col-xs-6">
		  <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['urls']->value['base_url'], ENT_QUOTES, 'UTF-8');?>
" class="clearfix">
			<img class="logo img-responsive" style="max-width: 150px;" src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['logo'], ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['shop']->value['name'], ENT_QUOTES, 'UTF-8');?>
">
		  </a>
		</div>
		<div class="col-right col col-xs-12 col-lg-9 col-md-12 display_top">
			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayTop'),$_smarty_tpl ) );?>
 
		</div>
      </div>
    </div>
  </div>
<div class="header-bottom">
	<div class="container">
		<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displaymegamenu'),$_smarty_tpl ) );?>

	</div>
</div>
  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayNavFullWidth'),$_smarty_tpl ) );?>

<?php
}
}
/* {/block 'header_top'} */
}

<?php
/* Smarty version 3.1.33, created on 2019-02-18 06:33:45
  from '/var/www/html/themes/theme_ostromi2/templates/catalog/_partials/product-additional-info.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6a0471828d94_20522269',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '797c271a7e4c6b044c86e2de4467c7f7cf5d1019' => 
    array (
      0 => '/var/www/html/themes/theme_ostromi2/templates/catalog/_partials/product-additional-info.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6a0471828d94_20522269 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="product-additional-info">
  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductAdditionalInfo','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

</div>
<?php }
}

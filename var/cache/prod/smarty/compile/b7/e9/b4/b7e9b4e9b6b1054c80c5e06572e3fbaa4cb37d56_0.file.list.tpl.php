<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:26:54
  from '/var/www/html/modules/poslistcateproduct/views/templates/hook/list.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69ae76d19695_94013515',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b7e9b4e9b6b1054c80c5e06572e3fbaa4cb37d56' => 
    array (
      0 => '/var/www/html/modules/poslistcateproduct/views/templates/hook/list.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c69ae76d19695_94013515 (Smarty_Internal_Template $_smarty_tpl) {
?><div class="panel"><h3><i class="icon-list-ul"></i> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Items list','mod'=>'poslistcateproduct'),$_smarty_tpl ) );?>

	<span class="panel-heading-action">
		<a id="desc-product-new" class="list-toolbar-btn" href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules');?>
&configure=poslistcateproduct&addItem=1">
			<span title="" data-toggle="tooltip" class="label-tooltip" data-original-title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add new','mod'=>'homeslider'),$_smarty_tpl ) );?>
" data-html="true">
				<i class="process-icon-new "></i>
			</span>
		</a>
	</span>
	</h3>
	<div id="slidesContent">
		<div id="items">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['items']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
				
				<div id="items_<?php echo $_smarty_tpl->tpl_vars['item']->value['id_item'];?>
" class="panel">
					<div class="row">
						<div class="col-lg-2">
							<span><i class="icon-arrows "></i></span>
						</div>
						<!-- <div class="col-md-3">
							<img src="<?php echo $_smarty_tpl->tpl_vars['image_baseurl']->value;
echo $_smarty_tpl->tpl_vars['item']->value['image'];?>
" alt="" class="img-thumbnail" />
						</div> -->
						<div class="col-md-10">
							<h4 class="pull-left">
								#<?php echo $_smarty_tpl->tpl_vars['item']->value['id_item'];?>
 - <?php echo $_smarty_tpl->tpl_vars['item']->value['category_name'];?>
 - ID category: <?php echo $_smarty_tpl->tpl_vars['item']->value['id_category'];?>

							</h4>
							<div class="btn-group-action pull-right">
								<?php echo $_smarty_tpl->tpl_vars['item']->value['status'];?>


								<a class="btn btn-default"
									href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules');?>
&configure=poslistcateproduct&id_item=<?php echo $_smarty_tpl->tpl_vars['item']->value['id_item'];?>
">
									<i class="icon-edit"></i>
									<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Edit','mod'=>'poslistcateproduct'),$_smarty_tpl ) );?>

								</a>
								<a class="btn btn-default"
									href="<?php echo $_smarty_tpl->tpl_vars['link']->value->getAdminLink('AdminModules');?>
&configure=poslistcateproduct&delete_id_item=<?php echo $_smarty_tpl->tpl_vars['item']->value['id_item'];?>
">
									<i class="icon-trash"></i>
									<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Delete','mod'=>'poslistcateproduct'),$_smarty_tpl ) );?>

								</a>
							</div>
						</div>
					</div>
				</div>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		</div>
	</div>
</div>
<?php }
}

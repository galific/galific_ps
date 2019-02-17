<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:25
  from 'C:\wamp64\www\galific\modules\posmegamenu\megamenu.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921b9f26919_42471687',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3cf2d54b4453ffd4ba1c08c6d8f043c036aa048b' => 
    array (
      0 => 'C:\\wamp64\\www\\galific\\modules\\posmegamenu\\megamenu.tpl',
      1 => 1550393263,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6921b9f26919_42471687 (Smarty_Internal_Template $_smarty_tpl) {
?><!-- Block categories module -->
<?php if ($_smarty_tpl->tpl_vars['blockCategTree']->value != '') {?>
	<div class="ma-nav-mobile-container hidden-lg-up">
		<div class="pt_custommenu_mobile">
		<div class="navbar">
			<div id="navbar-inner" class="navbar-inner navbar-inactive">
				<a class="btn-navbar"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Category','mod'=>'posmegamenu'),$_smarty_tpl ) );?>
</a>
				<ul id="pt_custommenu_itemmobile" class="tree <?php if ($_smarty_tpl->tpl_vars['isDhtml']->value) {?>dhtml<?php }?>  mobilemenu nav-collapse collapse">
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['blockCategTree']->value['children'], 'child', false, NULL, 'blockCategTree', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['child']->value) {
?>
						<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['child']->value['link'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['child']->value['name'], ENT_QUOTES, 'UTF-8');?>
 </a>
						<?php if ($_smarty_tpl->tpl_vars['child']->value['children']) {?>
						<ul class="dhtml">
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['child']->value['children'], 'child2', false, NULL, 'blockCategTree2', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['child2']->value) {
?>
							<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['child2']->value['link'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['child2']->value['name'], ENT_QUOTES, 'UTF-8');?>
 </a>
								<ul>
								<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['child2']->value['children'], 'child3', false, NULL, 'blockCategTree3', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['child3']->value) {
?>
									<li><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['child3']->value['link'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['child3']->value['name'], ENT_QUOTES, 'UTF-8');?>
 </a>
								<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
								</ul>
						<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						</ul>
						<?php }?>
						</li>
					<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
					    <?php if (isset($_smarty_tpl->tpl_vars['cms_link']->value)) {?> 
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cms_link']->value, 'cms_link1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['cms_link1']->value) {
?>
								<li class="cms">
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_link1']->value['link'], ENT_QUOTES, 'UTF-8');?>
" title="Contains Subs"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a>
								</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['cms_cate']->value)) {?> 
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['cms_cate']->value, 'cms_cate1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['cms_cate1']->value) {
?>
								<li>
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_cate1']->value['link'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_cate1']->value['title'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cms_cate1']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a>
								</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['manufacture_link']->value)) {?> 
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['manufacture_link']->value, 'manufacture_link1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['manufacture_link1']->value) {
?>
								<li>
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['manufacture_link1']->value['link'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['manufacture_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['manufacture_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a>
								</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['supply_link']->value)) {?> 
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['supply_link']->value, 'supply_link1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['supply_link1']->value) {
?>
								<li>
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['supply_link1']->value['link'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['supply_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['supply_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a>
								</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['custom_link']->value)) {?> 
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['custom_link']->value, 'custom_link1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['custom_link1']->value) {
?>
								<li>
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['custom_link1']->value['link'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['custom_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['custom_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a>
								</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['product_link']->value)) {?> 
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product_link']->value, 'product_link1');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['product_link1']->value) {
?>
								<li>
									<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_link1']->value['link'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product_link1']->value['title'], ENT_QUOTES, 'UTF-8');?>
</a>
								</li>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
						<?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['all_man_link']->value)) {?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['all_man_link']->value, ENT_QUOTES, 'UTF-8');?>
 <?php }?>
						<?php if (isset($_smarty_tpl->tpl_vars['all_sup_link']->value)) {?> <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['all_sup_link']->value, ENT_QUOTES, 'UTF-8');?>
 <?php }?>
				</ul>
			</div>
		</div>
		</div>
</div>
<?php }?>
<!-- /Block categories module -->
<div class="nav-container hidden-md-down">
	<div class="nav-inner">
		<div id="pt_custommenu" class="pt_custommenu">
		    <?php echo $_smarty_tpl->tpl_vars['megamenu']->value;?>

		</div>
	</div>
</div>
<?php echo '<script'; ?>
 type="text/javascript">
//<![CDATA[
var CUSTOMMENU_POPUP_EFFECT = <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['effect']->value, ENT_QUOTES, 'UTF-8');?>
;
var CUSTOMMENU_POPUP_TOP_OFFSET = <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['top_offset']->value, ENT_QUOTES, 'UTF-8');?>
;
//]]>
<?php echo '</script'; ?>
><?php }
}

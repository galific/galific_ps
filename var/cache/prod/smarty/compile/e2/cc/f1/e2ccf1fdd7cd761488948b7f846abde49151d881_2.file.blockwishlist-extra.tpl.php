<?php
/* Smarty version 3.1.33, created on 2019-02-13 11:12:25
  from '/var/www/html/modules/blockwishlist/blockwishlist-extra.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c63ae415da3d1_22761415',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e2ccf1fdd7cd761488948b7f846abde49151d881' => 
    array (
      0 => '/var/www/html/modules/blockwishlist/blockwishlist-extra.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c63ae415da3d1_22761415 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['wishlists']->value) && count($_smarty_tpl->tpl_vars['wishlists']->value) > 1) {?>
    <div class="buttons_bottom_block no-print panel-product-line panel-product-actions">
    	<div id="wishlist_button">
    		<button class="wishlist_button_extra" onclick="WishlistCart('wishlist_block_list', 'add', '<?php echo htmlspecialchars(intval($_smarty_tpl->tpl_vars['id_product']->value), ENT_QUOTES, 'UTF-8');?>
', $('#idCombination').val(), document.getElementById('quantity_wanted').value, $('#idWishlist').val()); return false;"  title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add to wishlist','mod'=>'blockwishlist'),$_smarty_tpl ) );?>
">
    			<i class="ion-android-favorite-outline" style="font-weight:400;"></i>
                <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add to wishlist','mod'=>'blockwishlist'),$_smarty_tpl ) );?>

    		</button>
            <select id="idWishlist">
    			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['wishlists']->value, 'wishlist');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['wishlist']->value) {
?>
    				<option value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['wishlist']->value['id_wishlist'], ENT_QUOTES, 'UTF-8');?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['wishlist']->value['name'], ENT_QUOTES, 'UTF-8');?>
</option>
    			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    		</select>
    	</div>
    </div>
<?php } else { ?>
    <p class="panel-product-line panel-product-actions">
    	<a id="wishlist_button" href="#" onclick="WishlistCart('wishlist_block_list', 'add', '<?php echo htmlspecialchars(intval($_smarty_tpl->tpl_vars['id_product']->value), ENT_QUOTES, 'UTF-8');?>
', $('#idCombination').val(), document.getElementById('quantity_wanted').value); return false;" rel="nofollow"  title="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add to my wishlist','mod'=>'blockwishlist'),$_smarty_tpl ) );?>
">
    		<i class="ion-android-favorite-outline" style="font-weight:400;"></i>
            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Add to wishlist','mod'=>'blockwishlist'),$_smarty_tpl ) );?>

    	</a>
    </p>
<?php }
}
}

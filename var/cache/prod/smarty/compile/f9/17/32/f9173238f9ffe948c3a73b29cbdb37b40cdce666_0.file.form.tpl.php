<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:22:00
  from '/var/www/html/modules/posrecommendedproducts/views/templates/admin/_configure/helpers/form/form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69ad509f6e83_94667655',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f9173238f9ffe948c3a73b29cbdb37b40cdce666' => 
    array (
      0 => '/var/www/html/modules/posrecommendedproducts/views/templates/admin/_configure/helpers/form/form.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c69ad509f6e83_94667655 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_19167606565c69ad509dfdd5_65808016', "field");
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, "helpers/form/form.tpl");
}
/* {block "field"} */
class Block_19167606565c69ad509dfdd5_65808016 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'field' => 
  array (
    0 => 'Block_19167606565c69ad509dfdd5_65808016',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<?php if ($_smarty_tpl->tpl_vars['input']->value['type'] == 'file_lang') {?>
		<div class="col-lg-9">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['languages']->value, 'language');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['language']->value) {
?>
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value) > 1) {?>
					<div class="translatable-field lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" <?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang'] != $_smarty_tpl->tpl_vars['defaultFormLanguage']->value) {?>style="display:none"<?php }?>>
				<?php }?>
				<div class="form-group">
					<div class="col-lg-6">
						<input id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" type="file" name="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" class="hide" />
						<div class="dummyfile input-group">
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-name" type="text" class="disabled" name="filename" readonly />
						<span class="input-group-btn">
								<button id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Choose a file','mod'=>'blockbanner'),$_smarty_tpl ) );?>

								</button>
							</span>
						</div>
					</div>
					<?php if (count($_smarty_tpl->tpl_vars['languages']->value) > 1) {?>
						<div class="col-lg-2">
							<button type="button" class="btn btn-default dropdown-toggle" tabindex="-1" data-toggle="dropdown">
								<?php echo $_smarty_tpl->tpl_vars['language']->value['iso_code'];?>

								<span class="caret"></span>
							</button>
							<ul class="dropdown-menu">
								<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['languages']->value, 'lang');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['lang']->value) {
?>
								<li><a href="javascript:hideOtherLanguage(<?php echo $_smarty_tpl->tpl_vars['lang']->value['id_lang'];?>
);" tabindex="-1"><?php echo $_smarty_tpl->tpl_vars['lang']->value['name'];?>
</a></li>
								<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
							</ul>
						</div>
					<?php }?>
				</div>
				<div class="form-group">
					<?php if (isset($_smarty_tpl->tpl_vars['fields_value']->value[$_smarty_tpl->tpl_vars['input']->value['name']][$_smarty_tpl->tpl_vars['language']->value['id_lang']]) && $_smarty_tpl->tpl_vars['fields_value']->value[$_smarty_tpl->tpl_vars['input']->value['name']][$_smarty_tpl->tpl_vars['language']->value['id_lang']] != '') {?>
					<div id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-images-thumbnails" class="col-lg-12">
						<img src="<?php echo $_smarty_tpl->tpl_vars['uri']->value;?>
img/<?php echo $_smarty_tpl->tpl_vars['fields_value']->value[$_smarty_tpl->tpl_vars['input']->value['name']][$_smarty_tpl->tpl_vars['language']->value['id_lang']];?>
" class="img-thumbnail"/>
					</div>
					<?php }?>
				</div>
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value) > 1) {?>
					</div>
				<?php }?>
				<?php echo '<script'; ?>
>
				$(document).ready(function(){
					$('#<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-selectbutton').click(function(e){
						$('#<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').trigger('click');
					});
					$('#<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
').change(function(e){
						var val = $(this).val();
						var file = val.split(/[\\/]/);
						$('#<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-name').val(file[file.length-1]);
					});
				});
			<?php echo '</script'; ?>
>
			<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
			<?php if (isset($_smarty_tpl->tpl_vars['input']->value['desc']) && !empty($_smarty_tpl->tpl_vars['input']->value['desc'])) {?>
				<p class="help-block">
					<?php echo $_smarty_tpl->tpl_vars['input']->value['desc'];?>

				</p>
			<?php }?>
		</div>
	<?php } elseif ($_smarty_tpl->tpl_vars['input']->value['type'] == 'listmanu') {?>
		<div class="row"><div class="col-lg-6">
		<div id="manufacturer-block">
			<div class="panel" style="max-height: 300px; overflow: auto; background: #f5f5f5;">
				<ul class="tree">
					<?php echo $_smarty_tpl->tpl_vars['options']->value;?>

				</ul>
			</div>
		</div>
        </div>
        </div>
		<?php echo '<script'; ?>
 type="text/javascript">
		$(document).ready(function(){
			$('#manufacturer-block ul li span').css("padding", "3px 5px");
			<?php if ($_smarty_tpl->tpl_vars['manuCurrent']->value) {?>
				<?php $_smarty_tpl->_assignInScope('imploded_selected_manufacturers', implode('","',$_smarty_tpl->tpl_vars['manuCurrent']->value));?>					
				var manuCurrent = new Array("<?php echo $_smarty_tpl->tpl_vars['imploded_selected_manufacturers']->value;?>
");
				console.log(manuCurrent);
				$('#manufacturer-block').find(':input').each(function(){
					if ($.inArray($(this).val(), manuCurrent) != -1)
					{
						$(this).prop("checked", true);
						$(this).parent().addClass("tree-selected");
					};
					$(this).on("click", function(){
						var test= $(this).parent();						
						if (test.hasClass("tree-selected")){
							test.removeClass("tree-selected");
						}else{
							test.addClass("tree-selected");
						}
					})
				});
			<?php }?>
			
		});
		<?php echo '</script'; ?>
>
	<?php } elseif ($_smarty_tpl->tpl_vars['input']->value['type'] == 'selectproduct') {?>
		<div class="row">
			<div class="col-lg-4">
				<input type="text" value="" id="product_name" name="product_name"/>
				<div class="form-control-static" id="product-list">
					<?php if (isset($_smarty_tpl->tpl_vars['products']->value) && $_smarty_tpl->tpl_vars['products']->value) {?>
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['products']->value, 'product');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
?>
		                <div id="product<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
">
		                	<i class="icon-remove text-danger"></i>
		                	<?php echo $_smarty_tpl->tpl_vars['product']->value['name'];?>

		                  	<input type="hidden" name="product[]" value="<?php echo $_smarty_tpl->tpl_vars['product']->value['product_id'];?>
" />
		                </div>
		            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
		            <?php }?>
	            </div>
	        </div>
	    </div>
	    <style type="text/css">
	    	#product_name {
	    		width: 100% !important;
	    	}
	    	#product-list {
	    		background: #f1f1f1;
	    	}
	    	#product-list i {
	    		display: inline-block;
			    font-weight: normal;
			    text-align: center;
			    vertical-align: middle;
			    cursor: pointer;
			    background-image: none;
			    border: 1px solid #ccc;
			    white-space: nowrap;
			    padding: 6px 8px;
			    font-size: 12px;
			    line-height: 1.42857;
			    border-radius: 3px;
			    -webkit-user-select: none;
			    -moz-user-select: none;
			    -ms-user-select: none;
			    user-select: none;	
			    background: #fff;
			    margin-left: 10px;
    			margin-bottom: 5px;
	    	}
	    	#product-list i:hover {
	    		color: #fff;
			    background-color: #00aff0;
			    border-color: #008abd;
			    -webkit-box-shadow: none;
			    box-shadow: none;
	    	}
		</style>
	<?php } else { ?>

		<?php 
$_smarty_tpl->inheritance->callParent($_smarty_tpl, $this, '{$smarty.block.parent}');
?>

	<?php }?>

<?php
}
}
/* {/block "field"} */
}

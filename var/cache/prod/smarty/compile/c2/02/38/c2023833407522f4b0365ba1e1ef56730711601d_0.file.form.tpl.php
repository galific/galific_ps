<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:27:11
  from '/var/www/html/modules/poslistcateproduct/views/templates/admin/_configure/helpers/form/form.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69ae87c406f7_64761505',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c2023833407522f4b0365ba1e1ef56730711601d' => 
    array (
      0 => '/var/www/html/modules/poslistcateproduct/views/templates/admin/_configure/helpers/form/form.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c69ae87c406f7_64761505 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_11334407995c69ae87c30392_91504653', "field");
$_smarty_tpl->inheritance->endChild($_smarty_tpl, "helpers/form/form.tpl");
}
/* {block "field"} */
class Block_11334407995c69ae87c30392_91504653 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'field' => 
  array (
    0 => 'Block_11334407995c69ae87c30392_91504653',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

	<?php if ($_smarty_tpl->tpl_vars['input']->value['type'] == 'file_lang') {?>
		<div class="row">
			<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['languages']->value, 'language');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['language']->value) {
?>
				<?php if (count($_smarty_tpl->tpl_vars['languages']->value) > 1) {?>
					<div class="translatable-field lang-<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" <?php if ($_smarty_tpl->tpl_vars['language']->value['id_lang'] != $_smarty_tpl->tpl_vars['defaultFormLanguage']->value) {?>style="display:none"<?php }?>>
				<?php }?>
					<div class="col-lg-6">
						<?php if (isset($_smarty_tpl->tpl_vars['fields']->value[0]['form']['images'])) {?>
						<img src="<?php echo $_smarty_tpl->tpl_vars['image_baseurl']->value;
echo $_smarty_tpl->tpl_vars['fields']->value[0]['form']['images'][$_smarty_tpl->tpl_vars['language']->value['id_lang']];?>
" class="img-thumbnail" />
						<?php }?>
						<div class="dummyfile input-group">
							<input id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" type="file" name="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
" class="hide-file-upload" />
							<span class="input-group-addon"><i class="icon-file"></i></span>
							<input id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-name" type="text" class="disabled" name="filename" readonly />
							<span class="input-group-btn">
								<button id="<?php echo $_smarty_tpl->tpl_vars['input']->value['name'];?>
_<?php echo $_smarty_tpl->tpl_vars['language']->value['id_lang'];?>
-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
									<i class="icon-folder-open"></i> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Choose a file','mod'=>'homeslider'),$_smarty_tpl ) );?>

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
		</div>
	<?php } elseif ($_smarty_tpl->tpl_vars['input']->value['type'] == 'selectlist') {?>
		<div class="col-lg-9">
			<div id="category-block" class="panel">
				<div class="tree-panel-heading-controls clearfix">
					<div class="tree-actions pull-right">
						<a href="#" onclick="$('#category-block .cattree .tree').hide();$('#category-block #collapse-all-categories-tree').hide();$('#category-block #expand-all-categories-tree').show(); return false;" id="collapse-all-categories-tree" class="btn btn-default"><i class="icon-collapse-alt"></i> Collapse All</a>
						<a href="#" onclick="$('#category-block .cattree .tree').show();$('#category-block #collapse-all-categories-tree').show();$('#category-block #expand-all-categories-tree').hide(); return false;" id="expand-all-categories-tree" class="btn btn-default"><i class="icon-expand-alt"></i>	Expand All</a>
					</div>
				</div>
				<ul class="cattree tree">
						<?php echo $_smarty_tpl->tpl_vars['options']->value;?>

				</ul>
			</div>
        </div> 
		<?php echo '<script'; ?>
 type="text/javascript">
			$(document).ready(function(){
				$('#category-block ul.tree').hide();
				$('#category-block ul.tree.cattree').show(); 
				<?php if (isset($_smarty_tpl->tpl_vars['selected_subcategories']->value)) {?>
					<?php $_smarty_tpl->_assignInScope('imploded_selected_subcategories', implode('","',$_smarty_tpl->tpl_vars['selected_subcategories']->value));?>					
					var selected_subcategories = new Array("<?php echo $_smarty_tpl->tpl_vars['imploded_selected_subcategories']->value;?>
");
					$('#category-block').find(':input').each(function(){
						if ($.inArray($(this).val(), selected_subcategories) != -1)
						{
							$(this).prop("checked", true);
							$(this).parent().addClass("tree-selected");
							$(this).parents('ul.tree').each(function(){
								$(this).show();
								$(this).prev().find('.icon-folder-close').removeClass('icon-folder-close').addClass('icon-folder-open');
							});
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
				
				$("#category-block .tree-folder-name i").each(function(){
					$(this).on("click", function(){
						if($(this).hasClass('icon-folder-close')){
							$(this).removeClass('icon-folder-close');
							$(this).addClass('icon-folder-open');	
						}else{
							$(this).removeClass('icon-folder-open');
							$(this).addClass('icon-folder-close');
						}
						$(this).parent().parent().children("ul.tree").slideToggle();
					})
				});
				$("#category-block .tree-folder-name label").each(function(){
					$(this).on("click", function(){
						if($(this).parent().find('i').hasClass('icon-folder-close')){
							$(this).parent().find('i').removeClass('icon-folder-close');
							$(this).parent().find('i').addClass('icon-folder-open');	
						}else{
							$(this).parent().find('i').removeClass('icon-folder-open');
							$(this).parent().find('i').addClass('icon-folder-close');
						}
						$(this).parent().parent().children("ul.tree").slideToggle();
						
					})
				})
				
			});
		<?php echo '</script'; ?>
>
	<?php } else { ?>
		<?php 
$_smarty_tpl->inheritance->callParent($_smarty_tpl, $this, '{$smarty.block.parent}');
?>

	<?php }
}
}
/* {/block "field"} */
}

<?php
/* Smarty version 3.1.33, created on 2019-02-22 14:22:33
  from '/var/www/html/themes/theme_ostromi2/templates/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6fb85199f3d9_83655344',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0fa12206ec2f338386f909286a9b91f79bc7584c' => 
    array (
      0 => '/var/www/html/themes/theme_ostromi2/templates/index.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6fb85199f3d9_83655344 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_7312451435c6fb85199d195_00560257', 'page_content_container');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, 'page.tpl');
}
/* {block 'page_content_top'} */
class Block_15619213415c6fb85199d783_28692225 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'page_content_top'} */
/* {block 'hook_home'} */
class Block_8213317655c6fb85199e354_11181177 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <?php echo $_smarty_tpl->tpl_vars['HOOK_HOME']->value;?>

          <?php
}
}
/* {/block 'hook_home'} */
/* {block 'page_content'} */
class Block_8020058475c6fb85199df91_65797491 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_8213317655c6fb85199e354_11181177', 'hook_home', $this->tplIndex);
?>

        <?php
}
}
/* {/block 'page_content'} */
/* {block 'page_content_container'} */
class Block_7312451435c6fb85199d195_00560257 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'page_content_container' => 
  array (
    0 => 'Block_7312451435c6fb85199d195_00560257',
  ),
  'page_content_top' => 
  array (
    0 => 'Block_15619213415c6fb85199d783_28692225',
  ),
  'page_content' => 
  array (
    0 => 'Block_8020058475c6fb85199df91_65797491',
  ),
  'hook_home' => 
  array (
    0 => 'Block_8213317655c6fb85199e354_11181177',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <section id="content" class="page-home">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_15619213415c6fb85199d783_28692225', 'page_content_top', $this->tplIndex);
?>


        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_8020058475c6fb85199df91_65797491', 'page_content', $this->tplIndex);
?>

      </section>
    <?php
}
}
/* {/block 'page_content_container'} */
}

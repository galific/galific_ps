<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:19:13
  from '/var/www/html/themes/theme_ostromi2/templates/index.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69aca9c10d99_49440989',
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
function content_5c69aca9c10d99_49440989 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_15345928555c69aca9c0e598_22763712', 'page_content_container');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, 'page.tpl');
}
/* {block 'page_content_top'} */
class Block_21148873595c69aca9c0eae2_82405278 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'page_content_top'} */
/* {block 'hook_home'} */
class Block_7095427345c69aca9c0fcd7_59063234 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

            <?php echo $_smarty_tpl->tpl_vars['HOOK_HOME']->value;?>

          <?php
}
}
/* {/block 'hook_home'} */
/* {block 'page_content'} */
class Block_12832187415c69aca9c0f8e1_31376197 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_7095427345c69aca9c0fcd7_59063234', 'hook_home', $this->tplIndex);
?>

        <?php
}
}
/* {/block 'page_content'} */
/* {block 'page_content_container'} */
class Block_15345928555c69aca9c0e598_22763712 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'page_content_container' => 
  array (
    0 => 'Block_15345928555c69aca9c0e598_22763712',
  ),
  'page_content_top' => 
  array (
    0 => 'Block_21148873595c69aca9c0eae2_82405278',
  ),
  'page_content' => 
  array (
    0 => 'Block_12832187415c69aca9c0f8e1_31376197',
  ),
  'hook_home' => 
  array (
    0 => 'Block_7095427345c69aca9c0fcd7_59063234',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <section id="content" class="page-home">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_21148873595c69aca9c0eae2_82405278', 'page_content_top', $this->tplIndex);
?>


        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_12832187415c69aca9c0f8e1_31376197', 'page_content', $this->tplIndex);
?>

      </section>
    <?php
}
}
/* {/block 'page_content_container'} */
}

<?php
/* Smarty version 3.1.33, created on 2019-02-22 14:22:33
  from '/var/www/html/themes/theme_ostromi2/templates/page.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6fb8519a7749_55404791',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a1ef22ffc99c02054870e704acf98df5d9e4bff2' => 
    array (
      0 => '/var/www/html/themes/theme_ostromi2/templates/page.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6fb8519a7749_55404791 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, true);
?>


<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_7897034345c6fb8519a3e56_32046390', 'content');
?>

<?php $_smarty_tpl->inheritance->endChild($_smarty_tpl, $_smarty_tpl->tpl_vars['layout']->value);
}
/* {block 'page_title'} */
class Block_6373832665c6fb8519a47c4_50683549 extends Smarty_Internal_Block
{
public $callsChild = 'true';
public $hide = 'true';
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

        <header class="page-header">
          <h1><?php 
$_smarty_tpl->inheritance->callChild($_smarty_tpl, $this);
?>
</h1>
        </header>
      <?php
}
}
/* {/block 'page_title'} */
/* {block 'page_header_container'} */
class Block_21309994775c6fb8519a42a7_40084844 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_6373832665c6fb8519a47c4_50683549', 'page_title', $this->tplIndex);
?>

    <?php
}
}
/* {/block 'page_header_container'} */
/* {block 'page_content_top'} */
class Block_4157095525c6fb8519a5b21_19999794 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
}
}
/* {/block 'page_content_top'} */
/* {block 'page_content'} */
class Block_8657490825c6fb8519a61e7_75155383 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <!-- Page content -->
        <?php
}
}
/* {/block 'page_content'} */
/* {block 'page_content_container'} */
class Block_12540336925c6fb8519a5746_48919113 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <section id="content" class="page-content card card-block">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_4157095525c6fb8519a5b21_19999794', 'page_content_top', $this->tplIndex);
?>

        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_8657490825c6fb8519a61e7_75155383', 'page_content', $this->tplIndex);
?>

      </section>
    <?php
}
}
/* {/block 'page_content_container'} */
/* {block 'page_footer'} */
class Block_4017625205c6fb8519a6da2_36638219 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

          <!-- Footer content -->
        <?php
}
}
/* {/block 'page_footer'} */
/* {block 'page_footer_container'} */
class Block_110557555c6fb8519a6a04_11460960 extends Smarty_Internal_Block
{
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

      <footer class="page-footer">
        <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_4017625205c6fb8519a6da2_36638219', 'page_footer', $this->tplIndex);
?>

      </footer>
    <?php
}
}
/* {/block 'page_footer_container'} */
/* {block 'content'} */
class Block_7897034345c6fb8519a3e56_32046390 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'content' => 
  array (
    0 => 'Block_7897034345c6fb8519a3e56_32046390',
  ),
  'page_header_container' => 
  array (
    0 => 'Block_21309994775c6fb8519a42a7_40084844',
  ),
  'page_title' => 
  array (
    0 => 'Block_6373832665c6fb8519a47c4_50683549',
  ),
  'page_content_container' => 
  array (
    0 => 'Block_12540336925c6fb8519a5746_48919113',
  ),
  'page_content_top' => 
  array (
    0 => 'Block_4157095525c6fb8519a5b21_19999794',
  ),
  'page_content' => 
  array (
    0 => 'Block_8657490825c6fb8519a61e7_75155383',
  ),
  'page_footer_container' => 
  array (
    0 => 'Block_110557555c6fb8519a6a04_11460960',
  ),
  'page_footer' => 
  array (
    0 => 'Block_4017625205c6fb8519a6da2_36638219',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>


  <section id="main">

    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_21309994775c6fb8519a42a7_40084844', 'page_header_container', $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_12540336925c6fb8519a5746_48919113', 'page_content_container', $this->tplIndex);
?>


    <?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_110557555c6fb8519a6a04_11460960', 'page_footer_container', $this->tplIndex);
?>


  </section>

<?php
}
}
/* {/block 'content'} */
}

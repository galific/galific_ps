<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:38
  from 'C:\wamp64\www\galific\modules\poscountdown\views\templates\hook\countdown.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921c6dd9996_60938541',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6edc1b191ffe0e2fc4ef51b6bd3e8f6f4cb9d625' => 
    array (
      0 => 'C:\\wamp64\\www\\galific\\modules\\poscountdown\\views\\templates\\hook\\countdown.tpl',
      1 => 1550393263,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6921c6dd9996_60938541 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\wamp64\\www\\galific\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php','function'=>'smarty_modifier_date_format',),));
if ($_smarty_tpl->tpl_vars['enddate']->value != null && $_smarty_tpl->tpl_vars['enddate']->value > 0) {?>
<div class='time_count_down' 
	data-years="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Years','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-year="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Year','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-months="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Months','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-month="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Month','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-weeks="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Weeks','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-week="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Week','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-days="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Days','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-day="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Day','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-hours="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Hrs','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-hour="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Hr','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-minutes="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Mins','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-minute="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Min','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-seconds="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Secs','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
	data-second="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Sec','mod'=>'poscountdown'),$_smarty_tpl ) );?>
"
>
    <span class="future_date_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id_cate']->value, ENT_QUOTES, 'UTF-8');?>
_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['id_product_time']->value, ENT_QUOTES, 'UTF-8');?>
 time_countdown"  data-date-y ='<?php echo htmlspecialchars(smarty_modifier_date_format($_smarty_tpl->tpl_vars['enddate']->value,"%Y"), ENT_QUOTES, 'UTF-8');?>
' data-date-m ='<?php echo htmlspecialchars(smarty_modifier_date_format($_smarty_tpl->tpl_vars['enddate']->value,"%m"), ENT_QUOTES, 'UTF-8');?>
' data-date-d='<?php echo htmlspecialchars(smarty_modifier_date_format($_smarty_tpl->tpl_vars['enddate']->value,"%d"), ENT_QUOTES, 'UTF-8');?>
' data-date-h = '<?php echo htmlspecialchars(smarty_modifier_date_format($_smarty_tpl->tpl_vars['enddate']->value,"%H"), ENT_QUOTES, 'UTF-8');?>
' data-date-mi = '<?php echo htmlspecialchars(smarty_modifier_date_format($_smarty_tpl->tpl_vars['enddate']->value,"%M"), ENT_QUOTES, 'UTF-8');?>
' data-date-s= '<?php echo htmlspecialchars(smarty_modifier_date_format($_smarty_tpl->tpl_vars['enddate']->value,"%S"), ENT_QUOTES, 'UTF-8');?>
' >  </span>
 </div>
<?php }
}
}

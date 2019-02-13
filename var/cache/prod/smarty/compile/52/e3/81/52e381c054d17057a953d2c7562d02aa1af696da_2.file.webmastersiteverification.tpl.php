<?php
/* Smarty version 3.1.33, created on 2019-02-13 11:10:15
  from '/var/www/html/modules/webmastersiteverification/webmastersiteverification.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c63adbf4909d4_16362035',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '52e381c054d17057a953d2c7562d02aa1af696da' => 
    array (
      0 => '/var/www/html/modules/webmastersiteverification/webmastersiteverification.tpl',
      1 => 1549307392,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c63adbf4909d4_16362035 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['page_name']->value == 'index') {?>
<!-- Webmaster Site Verifications -->
<?php if (isset($_smarty_tpl->tpl_vars['google_pass_thou']->value)) {?><meta name="google-site-verification" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['google_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['googleapps_pass_thou']->value)) {?><meta name="google-site-verification" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['googleapps_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['bing_pass_thou']->value)) {?><meta name="msvalidate.01" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['bing_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['alexa_pass_thou']->value)) {?><meta name="alexaVerifyID" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['alexa_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['norton_pass_thou']->value)) {?><meta name="norton-safeweb-site-verification" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['norton_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['wot_pass_thou']->value)) {?><meta name="wot-verification" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['wot_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['pinterest_pass_thou']->value)) {?><meta name="p:domain_verify" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['pinterest_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
if (isset($_smarty_tpl->tpl_vars['yandex_pass_thou']->value)) {?><meta name="yandex-verification" content="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['yandex_pass_thou']->value, ENT_QUOTES, 'UTF-8');?>
" />
<?php }
}
}
}

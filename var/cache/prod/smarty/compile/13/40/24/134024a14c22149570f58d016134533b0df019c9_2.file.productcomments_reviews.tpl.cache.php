<?php
/* Smarty version 3.1.33, created on 2019-02-13 11:10:16
  from '/var/www/html/modules/productcomments/productcomments_reviews.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c63adc0ef3148_06800647',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '134024a14c22149570f58d016134533b0df019c9' => 
    array (
      0 => '/var/www/html/modules/productcomments/productcomments_reviews.tpl',
      1 => 1548364870,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c63adc0ef3148_06800647 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->compiled->nocache_hash = '16948087955c63adc0ee2df5_43969368';
if (isset($_smarty_tpl->tpl_vars['nbComments']->value) && $_smarty_tpl->tpl_vars['nbComments']->value > 0) {?>
	<div class="comments_note" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
		<div class="star_content clearfix">
			<?php
$_smarty_tpl->tpl_vars['__smarty_section_i'] = new Smarty_Variable(array());
if (true) {
for ($__section_i_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] = 0; $__section_i_0_iteration <= 5; $__section_i_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']++){
?>
				<?php if ($_smarty_tpl->tpl_vars['averageTotal']->value <= (isset($_smarty_tpl->tpl_vars['__smarty_section_i']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_i']->value['index'] : null)) {?>
					<div class="star"></div>
				<?php } else { ?>
					<div class="star star_on"></div>
				<?php }?>
			<?php
}
}
?>
            <meta itemprop="worstRating" content = "0" />
            <meta itemprop="ratingValue" content = "<?php if (isset($_smarty_tpl->tpl_vars['ratings']->value['avg'])) {
echo htmlspecialchars(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( round($_smarty_tpl->tpl_vars['ratings']->value['avg'],1),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');
} else {
echo htmlspecialchars(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( round($_smarty_tpl->tpl_vars['averageTotal']->value,1),'html','UTF-8' )), ENT_QUOTES, 'UTF-8');
}?>" />
            <meta itemprop="bestRating" content = "5" />
		</div>
		<span class="nb-comments"><span itemprop="reviewCount"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['nbComments']->value, ENT_QUOTES, 'UTF-8');?>
</span> <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'Review(s)','mod'=>'productcomments'),$_smarty_tpl ) );?>
</span>
	</div>
<?php } else { ?>
	<div itemtype="http://schema.org/AggregateRating" itemscope="" itemprop="aggregateRating" class="comments_note">	
	<div class="star_content clearfix">
	<div class="star "></div>
	<div class="star "></div>
	<div class="star "></div>
	<div class="star"></div>
	<div class="star "></div>
	</div>
	</div>
<?php }
}
}

<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:41
  from 'module:posproductsmanufacturerpo' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921c9e6a197_10952764',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '63982f526035e6a5af12b06aeb2c931944ba8d38' => 
    array (
      0 => 'module:posproductsmanufacturerpo',
      1 => 1550393263,
      2 => 'module',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5c6921c9e6a197_10952764 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_loadInheritance();
$_smarty_tpl->inheritance->init($_smarty_tpl, false);
$_smarty_tpl->compiled->nocache_hash = '2359178435c6921c9cea855_09982689';
?>

<div class="tab-manufacturers-container-slider">
	<div class="tab-manufacturer">				
		<div class="pos_tab">
			<div class ='pos_title'>		
				<h2><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['title']->value, ENT_QUOTES, 'UTF-8');?>
</h2>	
						
			</div> 				
		</div>
		<div class="pos_content">
			<div class="col-brand col-brand1">
				<div class="thumb_manu">
					<ul class="tab_manus owl-carousel"> 
						<?php $_smarty_tpl->_assignInScope('count', 0);?>
						<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arrayManufacturers']->value, 'productsManufacturer', false, NULL, 'productsManufacturer', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['productsManufacturer']->value) {
?>
								<li  data-title="tabtitle_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['productsManufacturer']->value['id_manufacturer'], ENT_QUOTES, 'UTF-8');?>
" rel="tab_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['productsManufacturer']->value['id_manufacturer'], ENT_QUOTES, 'UTF-8');?>
" <?php if ($_smarty_tpl->tpl_vars['count']->value == 0) {?> class="active"<?php }?> > 
									<div class="manu_thumb">
										<a href="#manu_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['productsManufacturer']->value['id_manufacturer'], ENT_QUOTES, 'UTF-8');?>
">
											<img src="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getManufacturerImageLink($_smarty_tpl->tpl_vars['productsManufacturer']->value['id_manufacturer'],'home_default'), ENT_QUOTES, 'UTF-8');?>
" alt="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['productsManufacturer']->value['name_manufacturer'], ENT_QUOTES, 'UTF-8');?>
" />
										</a>
									</div>
								</li>
								<?php $_smarty_tpl->_assignInScope('count', $_smarty_tpl->tpl_vars['count']->value+1);?>
						<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>	
					</ul>
				</div>
			</div>
			<!-- <div class="col-brand col-brand2  col-xs-12 col-sm-12 col-md-5 col-lg-5 col-xl-5">
				 <div class="banner-box">
					 <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['image_link']->value, ENT_QUOTES, 'UTF-8');?>
"><img class="img-responsive" src="<?php echo htmlspecialchars(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'escape' ][ 0 ], array( $_smarty_tpl->tpl_vars['banner_img']->value,'htmlall','UTF-8' )), ENT_QUOTES, 'UTF-8');?>
" alt="" title=""/></a>
					</div>
			</div> -->
			<div class="col-brand">
				<div class="manu_facturer">
					<?php $_smarty_tpl->_assignInScope('rows', $_smarty_tpl->tpl_vars['slider_options']->value['rows']);?>
					<?php $_smarty_tpl->_assignInScope('count', 0);?>					
					<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['arrayManufacturers']->value, 'productsManufacturer', false, NULL, 'productsManufacturer', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['productsManufacturer']->value) {
?>	
					<div id="tab_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['productsManufacturer']->value['id_manufacturer'], ENT_QUOTES, 'UTF-8');?>
" class="manu_facturer_tab">
					<div class="manu-item_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['count']->value, ENT_QUOTES, 'UTF-8');?>
 owl-carousel"> 					
							<?php if ($_smarty_tpl->tpl_vars['productsManufacturer']->value['products']) {?>
							<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['productsManufacturer']->value['products'], 'product', false, NULL, 'myLoop', array (
  'index' => true,
  'first' => true,
  'iteration' => true,
  'last' => true,
  'total' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['product']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['total'];
?>
								<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['index'] : null)%$_smarty_tpl->tpl_vars['rows']->value == 0 || (isset($_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['first'] : null)) {?>
								<div class="item_manu">
								<?php }?>
								<article class="js-product-miniature item_in" data-id-product="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['id_product'], ENT_QUOTES, 'UTF-8');?>
" data-id-product-attribute="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['id_product_attribute'], ENT_QUOTES, 'UTF-8');?>
" itemscope itemtype="http://schema.org/Product">
									<div class="img_block">
										<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_16042085055c6921c9d56a91_30109771', 'product_thumbnail');
?>

										<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_18297345565c6921c9d79369_03564347', 'product_flags');
?>

										
									</div>
									<div class="product_desc">
										<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_2760271365c6921c9da17b8_25058330', 'product_name');
?>

										<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_14102746385c6921c9db4970_93658620', 'product_reviews');
?>

										<?php 
$_smarty_tpl->inheritance->instanceBlock($_smarty_tpl, 'Block_19492795115c6921c9dbe936_61513129', 'product_price_and_shipping');
?>

										
									</div>
								</article>
								<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['iteration'] : null)%$_smarty_tpl->tpl_vars['rows']->value == 0 || (isset($_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_myLoop']->value['last'] : null)) {?>
								</div>
								<?php }?>
							<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
							<?php } else { ?>
								<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['l'][0], array( array('s'=>'There is no product from this manufacturer','mod'=>'posproductsmanufacturer'),$_smarty_tpl ) );?>

							<?php }?>		
					
					 </div> <!-- .tab_container -->
					 </div>
					 <?php echo '<script'; ?>
 type="text/javascript">
					
						$(document).ready(function() {
							var manuSlide = $(".tab-manufacturers-container-slider .manu-item_<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['count']->value, ENT_QUOTES, 'UTF-8');?>
");
							manuSlide.owlCarousel({
								autoPlay : 
								<?php if ($_smarty_tpl->tpl_vars['slider_options']->value['auto_play']) {?> 
									<?php if ($_smarty_tpl->tpl_vars['slider_options']->value['auto_time']) {
echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['auto_time'], ENT_QUOTES, 'UTF-8');?>
 <?php } else { ?> true <?php }?> 
								<?php } else { ?> 
									false 
								<?php }?> ,
								smartSpeed: 200,
								fluidSpeed: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['speed_slide'], ENT_QUOTES, 'UTF-8');?>
,
								navSpeed: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['speed_slide'], ENT_QUOTES, 'UTF-8');?>
,
								animateOut: 'fadeOut',
								animateIn: 'fadeIn',
								autoplayHoverPause: <?php if ($_smarty_tpl->tpl_vars['slider_options']->value['pausehover']) {?>true<?php } else { ?>false<?php }?>,
								nav: <?php if ($_smarty_tpl->tpl_vars['slider_options']->value['show_arrow']) {?>true<?php } else { ?>false<?php }?>,
								dots : <?php if ($_smarty_tpl->tpl_vars['slider_options']->value['show_pagination']) {?>true<?php } else { ?>false<?php }?>,	
								responsive:{
									0:{
										items:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['items_xxs'], ENT_QUOTES, 'UTF-8');?>
,
									},
									480:{
										items:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['items_xs'], ENT_QUOTES, 'UTF-8');?>
,
									},
									768:{
										items:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['items_sm'], ENT_QUOTES, 'UTF-8');?>
,
										nav:false,
									},
									992:{
										items:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['items_md'], ENT_QUOTES, 'UTF-8');?>
,
									},
									1200:{
										items:<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['slider_options']->value['number_item'], ENT_QUOTES, 'UTF-8');?>
,
									}
								}
							});

							
						});

						<?php echo '</script'; ?>
>
					 <?php $_smarty_tpl->_assignInScope('count', $_smarty_tpl->tpl_vars['count']->value+1);?>
					 <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
				</div> 
			</div> 
		</div>
	</div>		
</div>
<?php echo '<script'; ?>
 type="text/javascript">
$(document).ready(function() {
 $(".tab_manus").owlCarousel({
  autoplay : false ,
  smartSpeed: 1000,
  addClassActive: true,
  nav : true,
  dots : false,
  responsive : {
      0 : {
          items : 1,
      },
      480 : {
          items : 2, 
      },
      768 : {
          items : 3,
      },
      992 : {
          items : 4,
      },
      1200 : {
          items : 5,
      }
  }
 });
   $(".manu_facturer_tab").hide();
 $(".manu_facturer_tab:first").show(); 

 $("ul.tab_manus li").click(function() {
  $("ul.tab_manus li").removeClass("active");
  $(this).addClass("active");
  $(".manu_facturer_tab").hide();
  var activeTab = $(this).attr("rel"); 
  $("#"+activeTab).fadeIn();  
 });
 
});
<?php echo '</script'; ?>
><?php }
/* {block 'product_thumbnail'} */
class Block_16042085055c6921c9d56a91_30109771 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_thumbnail' => 
  array (
    0 => 'Block_16042085055c6921c9d56a91_30109771',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

										  <a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['url'], ENT_QUOTES, 'UTF-8');?>
" class="thumbnail product-thumbnail">
											<img
											  src = "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['cover']['bySize']['home_default']['url'], ENT_QUOTES, 'UTF-8');?>
"
											  alt = "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['cover']['legend'], ENT_QUOTES, 'UTF-8');?>
"
											  data-full-size-image-url = "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['cover']['large']['url'], ENT_QUOTES, 'UTF-8');?>
"
											>
											<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>"rotatorImg",'product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>
		
										  </a>
										<?php
}
}
/* {/block 'product_thumbnail'} */
/* {block 'product_flags'} */
class Block_18297345565c6921c9d79369_03564347 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_flags' => 
  array (
    0 => 'Block_18297345565c6921c9d79369_03564347',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

										  <ul class="product-flag">
											<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['product']->value['flags'], 'flag');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['flag']->value) {
?>
												<?php if ($_smarty_tpl->tpl_vars['flag']->value['type'] == "discount") {?>
													<?php continue 1;?>
												<?php }?>
												<li class="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['flag']->value['type'], ENT_QUOTES, 'UTF-8');?>
"><span><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['flag']->value['label'], ENT_QUOTES, 'UTF-8');?>
</span></li>
											<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
										  </ul>
										<?php
}
}
/* {/block 'product_flags'} */
/* {block 'product_name'} */
class Block_2760271365c6921c9da17b8_25058330 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_name' => 
  array (
    0 => 'Block_2760271365c6921c9da17b8_25058330',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

											<h4><a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['url'], ENT_QUOTES, 'UTF-8');?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['name'], ENT_QUOTES, 'UTF-8');?>
" itemprop="name" class="product_name"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['name'], ENT_QUOTES, 'UTF-8');?>
</a></h4>
										<?php
}
}
/* {/block 'product_name'} */
/* {block 'product_reviews'} */
class Block_14102746385c6921c9db4970_93658620 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_reviews' => 
  array (
    0 => 'Block_14102746385c6921c9db4970_93658620',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

											<div class="hook-reviews">
											<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductListReviews','product'=>$_smarty_tpl->tpl_vars['product']->value),$_smarty_tpl ) );?>

											</div>
										<?php
}
}
/* {/block 'product_reviews'} */
/* {block 'product_price_and_shipping'} */
class Block_19492795115c6921c9dbe936_61513129 extends Smarty_Internal_Block
{
public $subBlocks = array (
  'product_price_and_shipping' => 
  array (
    0 => 'Block_19492795115c6921c9dbe936_61513129',
  ),
);
public function callBlock(Smarty_Internal_Template $_smarty_tpl) {
?>

											<?php if ($_smarty_tpl->tpl_vars['product']->value['show_price']) {?>
											  <div class="product-price-and-shipping">
												<span itemprop="price" class="price <?php if ($_smarty_tpl->tpl_vars['product']->value['has_discount']) {?> price_sale <?php }?>"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['price'], ENT_QUOTES, 'UTF-8');?>
</span>
												<?php if ($_smarty_tpl->tpl_vars['product']->value['has_discount']) {?>
												  <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>"old_price"),$_smarty_tpl ) );?>


												  <span class="regular-price"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['product']->value['regular_price'], ENT_QUOTES, 'UTF-8');?>
</span>
												<?php }?>

												<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>"before_price"),$_smarty_tpl ) );?>


												<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>'unit_price'),$_smarty_tpl ) );?>


												<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['hook'][0], array( array('h'=>'displayProductPriceBlock','product'=>$_smarty_tpl->tpl_vars['product']->value,'type'=>'weight'),$_smarty_tpl ) );?>

											  </div>
											<?php }?>
										<?php
}
}
/* {/block 'product_price_and_shipping'} */
}

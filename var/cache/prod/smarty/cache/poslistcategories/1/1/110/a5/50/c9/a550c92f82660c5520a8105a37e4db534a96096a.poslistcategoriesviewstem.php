<?php
/* Smarty version 3.1.33, created on 2019-02-17 14:26:38
  from 'module:poslistcategoriesviewstem' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c6921c684e8c0_65835139',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6cde3405a83e54bc9e5af3924a8ce9acec0e049b' => 
    array (
      0 => 'module:poslistcategoriesviewstem',
      1 => 1550393263,
      2 => 'module',
    ),
  ),
  'cache_lifetime' => 31536000,
),true)) {
function content_5c6921c684e8c0_65835139 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="poslistcategories">
	<div class="pos_title">
		<h2>Our Categories</h2>
	</div>
	<div class="pos_content">
		<div class="block_content owl-carousel">
										<div class="item-listcategories">
				
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="http://localhost/galific/3-clothes" target="_blank"><img src="http://localhost/galific/modules/poslistcategories/images/thumb-1.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Clothes
								
							</div>		
							<span class="number_product">2 products</span>
							<!-- 							<div class="see-more"><a href="http://localhost/galific/3-clothes" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						
									
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="http://localhost/galific/4-men" target="_blank"><img src="http://localhost/galific/modules/poslistcategories/images/thumb-2.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Men
								
							</div>		
							<span class="number_product">1 products</span>
							<!-- 							<div class="see-more"><a href="http://localhost/galific/4-men" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						</div>
						
											<div class="item-listcategories">
				
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="http://localhost/galific/5-women" target="_blank"><img src="http://localhost/galific/modules/poslistcategories/images/thumb-3.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Women
								
							</div>		
							<span class="number_product">1 products</span>
							<!-- 							<div class="see-more"><a href="http://localhost/galific/5-women" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						
									
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="http://localhost/galific/6-accessories" target="_blank"><img src="http://localhost/galific/modules/poslistcategories/images/thumb-4.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Accessories
								
							</div>		
							<span class="number_product">11 products</span>
							<!-- 							<div class="see-more"><a href="http://localhost/galific/6-accessories" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						</div>
						
							
		</div>
	</div>	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var poslistcategories = $(".poslistcategories .block_content");
		poslistcategories.owlCarousel({
			autoplay :   false,
			smartSpeed : 1000,
			nav :  false ,
			dots :  true ,
			responsive:{
				0:{
					items:1,
				},
				480:{
					items:2,
				},
				768:{
					items:2,
	
				},
				992:{
					items:3,
				},
				1200:{
					items:3,
				}
			}
		});
	});
</script>
<?php }
}

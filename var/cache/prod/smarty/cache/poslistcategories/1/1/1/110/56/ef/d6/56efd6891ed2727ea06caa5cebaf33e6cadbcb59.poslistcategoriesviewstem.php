<?php
/* Smarty version 3.1.33, created on 2019-02-18 00:44:36
  from 'module:poslistcategoriesviewstem' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5c69b29c9fd3b3_81616024',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6cde3405a83e54bc9e5af3924a8ce9acec0e049b' => 
    array (
      0 => 'module:poslistcategoriesviewstem',
      1 => 1548364870,
      2 => 'module',
    ),
  ),
  'cache_lifetime' => 31536000,
),true)) {
function content_5c69b29c9fd3b3_81616024 (Smarty_Internal_Template $_smarty_tpl) {
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
							<a href="https://galific.com/3-personalized-gifts" target="_blank"><img src="https://galific.com/modules/poslistcategories/images/574c3877f0886e81849fdc0a4345c5d10f6d3d3a_banner-galific4.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Personalized Gifts
								
							</div>		
							<span class="number_product">2 products</span>
							<!-- 							<div class="description-list">
								<div class="desc-content">
									<p>Personalized Gift, Customised Gift, Mug, ...
								</div>
									
							</div>
														<div class="see-more"><a href="https://galific.com/3-personalized-gifts" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						
									
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="https://galific.com/6-cards" target="_blank"><img src="https://galific.com/modules/poslistcategories/images/9d3f47a6ec94967078fb46e27a03a27729675061_banner-galific5.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Cards
								
							</div>		
							<span class="number_product">0 products</span>
							<!-- 							<div class="description-list">
								<div class="desc-content">
									<p>Occasion cards, Festive Cards, Get Well ...
								</div>
									
							</div>
														<div class="see-more"><a href="https://galific.com/6-cards" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						</div>
						
											<div class="item-listcategories">
				
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="https://galific.com/19-mobile-cases" target="_blank"><img src="https://galific.com/modules/poslistcategories/images/1418b212f44d0c04414d811979c8f3b9a5c2a491_banner-galific7.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Mobile Cases
								
							</div>		
							<span class="number_product">1 products</span>
							<!-- 							<div class="description-list">
								<div class="desc-content">
									<p>Customized Mobile Case, Mobile Cover, ...
								</div>
									
							</div>
														<div class="see-more"><a href="https://galific.com/19-mobile-cases" target="_blank">Shop Now</a> </div> -->
						</div>	
					</div>							
				</div>	
						
									
				<div class="list-categories">
					<div class="box-inner">
												<div class="thumb-category">
							<a href="https://galific.com/22-paintings-and-craft" target="_blank"><img src="https://galific.com/modules/poslistcategories/images/3d5e1b500ac0398c278cba32f807733d77ea50aa_banner-galific6.jpg" alt="" /></a>
						</div>
												<div class="desc-listcategoreis">		
							<div class="name_categories">
								Paintings and Craft
								
							</div>		
							<span class="number_product">4 products</span>
							<!-- 							<div class="description-list">
								<div class="desc-content">
									<p>motivational poster, devotional poster, ...
								</div>
									
							</div>
														<div class="see-more"><a href="https://galific.com/22-paintings-and-craft" target="_blank">Shop Now</a> </div> -->
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
					items:2,
				}
			}
		});
	});
</script>
<?php }
}

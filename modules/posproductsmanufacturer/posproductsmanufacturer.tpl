
<div class="tab-manufacturers-container-slider">
	<div class="tab-manufacturer">				
		<div class="pos_tab">
			<div class ='pos_title'>		
				<h2>{$title}</h2>	
						
			</div> 				
		</div>
		<div class="pos_content">
			<div class="col-brand col-brand1">
				<div class="thumb_manu">
					<ul class="tab_manus owl-carousel"> 
						{$count=0}
						{foreach from=$arrayManufacturers item=productsManufacturer name=productsManufacturer}
								<li  data-title="tabtitle_{$productsManufacturer.id_manufacturer}" rel="tab_{$productsManufacturer.id_manufacturer}" {if $count==0} class="active"{/if} > 
									<div class="manu_thumb">
										<a href="#manu_{$productsManufacturer.id_manufacturer}">
											<img src="{$link->getManufacturerImageLink($productsManufacturer.id_manufacturer, 'home_default')}" alt="{$productsManufacturer.name_manufacturer}" />
										</a>
									</div>
								</li>
								{$count= $count+1}
						{/foreach}	
					</ul>
				</div>
			</div>
			<!-- <div class="col-brand col-brand2  col-xs-12 col-sm-12 col-md-5 col-lg-5 col-xl-5">
				 <div class="banner-box">
					 <a href="{$image_link}"><img class="img-responsive" src="{$banner_img|escape:'htmlall':'UTF-8'}" alt="" title=""/></a>
					</div>
			</div> -->
			<div class="col-brand">
				<div class="manu_facturer">
					{$rows= $slider_options.rows}
					{$count=0}					
					{foreach from=$arrayManufacturers item=productsManufacturer name=productsManufacturer}	
					<div id="tab_{$productsManufacturer.id_manufacturer}" class="manu_facturer_tab">
					<div class="manu-item_{$count} owl-carousel"> 					
							{if $productsManufacturer.products}
							{foreach from=$productsManufacturer.products item=product name=myLoop}
								{if $smarty.foreach.myLoop.index % $rows == 0 || $smarty.foreach.myLoop.first }
								<div class="item_manu">
								{/if}
								<article class="js-product-miniature item_in" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
									<div class="img_block">
										{block name='product_thumbnail'}
										  <a href="{$product.url}" class="thumbnail product-thumbnail">
											<img
											  src = "{$product.cover.bySize.home_default.url}"
											  alt = "{$product.cover.legend}"
											  data-full-size-image-url = "{$product.cover.large.url}"
											>
											{hook h="rotatorImg" product=$product}		
										  </a>
										{/block}
										{block name='product_flags'}
										  <ul class="product-flag">
											{foreach from=$product.flags item=flag}
												{if $flag.type == "discount"}
													{continue}
												{/if}
												<li class="{$flag.type}"><span>{$flag.label}</span></li>
											{/foreach}
										  </ul>
										{/block}
										
									</div>
									<div class="product_desc">
										{block name='product_name'}
											<h4><a href="{$product.url}" title="{$product.name}" itemprop="name" class="product_name">{$product.name}</a></h4>
										{/block}
										{block name='product_reviews'}
											<div class="hook-reviews">
											{hook h='displayProductListReviews' product=$product}
											</div>
										{/block}
										{block name='product_price_and_shipping'}
											{if $product.show_price}
											  <div class="product-price-and-shipping">
												<span itemprop="price" class="price {if $product.has_discount} price_sale {/if}">{$product.price}</span>
												{if $product.has_discount}
												  {hook h='displayProductPriceBlock' product=$product type="old_price"}

												  <span class="regular-price">{$product.regular_price}</span>
												{/if}

												{hook h='displayProductPriceBlock' product=$product type="before_price"}

												{hook h='displayProductPriceBlock' product=$product type='unit_price'}

												{hook h='displayProductPriceBlock' product=$product type='weight'}
											  </div>
											{/if}
										{/block}
										
									</div>
								</article>
								{if $smarty.foreach.myLoop.iteration % $rows == 0 || $smarty.foreach.myLoop.last  }
								</div>
								{/if}
							{/foreach}
							{else}
								{l s='There is no product from this manufacturer' mod='posproductsmanufacturer'}
							{/if}		
					
					 </div> <!-- .tab_container -->
					 </div>
					 <script type="text/javascript">
					
						$(document).ready(function() {
							var manuSlide = $(".tab-manufacturers-container-slider .manu-item_{$count}");
							manuSlide.owlCarousel({
								autoPlay : 
								{if $slider_options.auto_play} 
									{if $slider_options.auto_time}{$slider_options.auto_time} {else} true {/if} 
								{else} 
									false 
								{/if} ,
								smartSpeed: 200,
								fluidSpeed: {$slider_options.speed_slide},
								navSpeed: {$slider_options.speed_slide},
								animateOut: 'fadeOut',
								animateIn: 'fadeIn',
								autoplayHoverPause: {if $slider_options.pausehover}true{else}false{/if},
								nav: {if $slider_options.show_arrow}true{else}false{/if},
								dots : {if $slider_options.show_pagination}true{else}false{/if},	
								responsive:{
									0:{
										items:{$slider_options.items_xxs},
									},
									480:{
										items:{$slider_options.items_xs},
									},
									768:{
										items:{$slider_options.items_sm},
										nav:false,
									},
									992:{
										items:{$slider_options.items_md},
									},
									1200:{
										items:{$slider_options.number_item},
									}
								}
							});

							
						});

						</script>
					 {$count= $count+1}
					 {/foreach}
				</div> 
			</div> 
		</div>
	</div>		
</div>
<script type="text/javascript">
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
</script>
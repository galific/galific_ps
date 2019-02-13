
<div class="tab-category-container-slider"  
	data-items="{$slider_options.number_item}" 
	data-speed="{$slider_options.speed_slide}"
	data-autoplay="{$slider_options.auto_play}"
	data-time="{$slider_options.auto_time}"
	data-arrow="{$slider_options.show_arrow}"
	data-pagination="{$slider_options.show_pagination}"
	data-move="{$slider_options.move}"
	data-pausehover="{$slider_options.pausehover}"
	data-md="{$slider_options.items_md}"
	data-sm="{$slider_options.items_sm}"
	data-xs="{$slider_options.items_xs}"
	data-xxs="{$slider_options.items_xxs}">
		<div class="tab-category">	
			<div class="pos_title">
				 <h2>
					<span>{$title}</span>
				</h2>	
				<ul class="tab_cates"> 
					{$count=0}
					{foreach from=$productCates item=productCate name=postabcateslider}
							<li data-title="tabtitle_{$productCate.id}" rel="tab_{$productCate.id}" {if $count==0} class="active"  {/if} > 
							<span>{$productCate.name}</span>
							</li>
							{$count= $count+1}
					{/foreach}	
				</ul>
			</div>
			<div class="row pos_content">	
				{$rows= $slider_options.rows}			
				<div class="tab1_container"> 
				{foreach from=$productCates item=productCate name=postabcateslider}				
					<div id="tab_{$productCate.id}" class="tab_category">
						<div class="productTabCategorySlider  owl-carousel">
						{foreach from=$productCate.product item=product name=myLoop}
							{if $smarty.foreach.myLoop.index % $rows == 0 || $smarty.foreach.myLoop.first }
								<div class="item-product">
							{/if}
								{include file="catalog/_partials/miniatures/product.tpl" product=$product}
								
							{if $smarty.foreach.myLoop.iteration % $rows == 0 || $smarty.foreach.myLoop.last  }
								</div>
							{/if}
						{/foreach}
						</div>
					</div>			
				{/foreach}	
				 </div> <!-- .tab_container -->
			</div>
		</div>	
	
</div>

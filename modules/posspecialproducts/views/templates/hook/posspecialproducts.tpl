
<div class="pos-special-products " 
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
	{if $title}
	<div class="pos_title">
		 <h2>
			<span>{$title}</span>
		</h2>	
	</div>
	{/if}
	{$rows= $slider_options.rows}
	<div class="special-products">
		<div class="row pos_content">
			<div class="special-item owl-carousel">
			{foreach from=$products item=product name=myLoop}
				{if $smarty.foreach.myLoop.index % $rows == 0 || $smarty.foreach.myLoop.first }
				<div class="item-product">
				{/if}			
					<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
						<div class="img_block">
						  {block name='product_thumbnail'}
							<a href="{$product.url}" class="thumbnail product-thumbnail">
							  <img
								src = "{$product.cover.bySize.large_default.url}"
								alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
								data-full-size-image-url = "{$product.cover.large.url}"
							  >
							</a>
						  {/block}
						</div>
						<div class="product_desc">
							<div class="desc-inner">
								{if isset($product.id_manufacturer)}
									<div class="manufacturer"><a href="{$link->getManufacturerLink($product.id_manufacturer)}">{$product.manufacturer_name|strip_tags:'UTF-8'|escape:'html':'UTF-8'}</a></div>
								{/if}
								{block name='product_name'}
								  <h1 itemprop="name"><a href="{$product.url}" class="product_name">{$product.name|truncate:100:'...'}</a></h1>
								{/block}
								{block name='product_reviews'}
									<div class="hook-reviews">
									{hook h='displayProductListReviews' product=$product}
									</div>
								{/block}
								{block name='product_description_short'}
									<div class="product-desc" itemprop="description">{$product.description_short|truncate:100:'...' nofilter}</div>
								{/block}
								<div class="price-box">
									{block name='product_price_and_shipping'}
									  {if $product.show_price}
										<div class="product-price-and-shipping">
										  {if $product.has_discount}
											{hook h='displayProductPriceBlock' product=$product type="old_price"}

											<span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
											<span class="regular-price">{$product.regular_price}</span>
										  {/if}

										  {hook h='displayProductPriceBlock' product=$product type="before_price"}

										  <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
										  <span itemprop="price" class="price {if $product.has_discount} price_sale {/if}">{$product.price}</span>
										 <!--  {if $product.has_discount}
											{if $product.discount_type === 'percentage'}
											  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
											{elseif $product.discount_type === 'amount'}
											  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
											{/if}
										  {/if} -->
										  {hook h='displayProductPriceBlock' product=$product type='unit_price'}

										  {hook h='displayProductPriceBlock' product=$product type='weight'}
										</div>
									  {/if}
									{/block}
								</div>	
								<p class="text-hurryup">{l s='Hurry Up! Offer ends in:' d='Shop.Theme.Catalog'}</p>
								<div class="countdown" >
									{hook h='timecountdown' product=$product }
									<span 	id="future_date_{$product.id_category_default}_{$product.id_product}"
									class="id_countdown"></span>
									<div class="clearfix"></div>
								</div>
							</div>	
						</div>
					</article>
				{if $smarty.foreach.myLoop.iteration % $rows == 0 || $smarty.foreach.myLoop.last  }
				</div>
				{/if}
			{/foreach}
			</div>
		</div>
	</div>
</div>


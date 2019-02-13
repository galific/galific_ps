{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @version  Release: $Revision$
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<div class="poslistcategories">
	<div class="pos_title">
		<h2>{l s='Our Categories' mod='poslistcategories'}</h2>
	</div>
	<div class="pos_content">
		<div class="block_content owl-carousel">
		{$count=0}
		{foreach from=$categories item=category name=myLoop}
			{if $smarty.foreach.myLoop.index % 2 == 0 || $smarty.foreach.myLoop.first }
			<div class="item-listcategories">
			{/if}	
				<div class="list-categories">
					<div class="box-inner">
						{if $category.image}
						<div class="thumb-category">
							<a href="{$link->getCategoryLink($category['id_category'])}" target="_blank"><img src="{$link->getMediaLink("`$smarty.const._MODULE_DIR_`poslistcategories/images/`$category.image|escape:'htmlall':'UTF-8'`")}" alt="" /></a>
						</div>
						{/if}
						<div class="desc-listcategoreis">		
							<div class="name_categories">
								{$category.category_name}
								
							</div>		
							<span class="number_product">{$category.nbProducts} {l s='products' mod='poslistcategories'}</span>
							<!-- {if $category.description}
							<div class="description-list">
								<div class="desc-content">
									{$category.description|truncate:50:' ...'|escape:'html':'UTF-8' nofilter}
								</div>
									
							</div>
							{/if}
							<div class="see-more"><a href="{$link->getCategoryLink($category['id_category'])}" target="_blank">{l s='Shop Now' mod='poslistcategories'}</a> </div> -->
						</div>	
					</div>							
				</div>	
			{if $smarty.foreach.myLoop.iteration % 2 == 0 || $smarty.foreach.myLoop.last  }
			</div>
			{/if}			
			{$count= $count+1}
		{/foreach}		
		</div>
	</div>	
</div>

<script type="text/javascript">
	$(document).ready(function() {
		var poslistcategories = $(".poslistcategories .block_content");
		poslistcategories.owlCarousel({
			autoplay :  {if $slider_options.auto_play}{if $slider_options.delay}{$slider_options.delay}{else}3000{/if}{else} false{/if},
			smartSpeed : {if $slider_options.speed_slide}{$slider_options.speed_slide}{else}1000{/if},
			nav : {if $slider_options.show_arrow} true {else} false {/if},
			dots : {if $slider_options.show_pagination} true {else} false {/if},
			responsive:{
				0:{
					items:{$slider_options.items_xxs},
				},
				480:{
					items:{$slider_options.items_xs},
				},
				768:{
					items:{$slider_options.items_sm},
	
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

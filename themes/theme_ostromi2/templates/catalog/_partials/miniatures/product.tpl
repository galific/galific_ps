{**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
{block name='product_miniature_item'}
	<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}" itemscope itemtype="http://schema.org/Product">
		<div class="img_block">
		  {block name='product_thumbnail'}
			<a href="{$product.url}" class="thumbnail product-thumbnail">
			  <img
				src = "{$product.cover.bySize.home_default.url}"
				alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
				data-full-size-image-url = "{$product.cover.large.url}"
			  >
			</a>
		  {/block}
		  {block name='product_flags'}
			<ul class="product-flag">
			{foreach from=$product.flags item=flag}
				<li class="{$flag.type}"><span>{$flag.label}</span></li>
			{/foreach}
			</ul>
			{/block}
			{block name='product_price_and_shipping'}
				{if $product.show_price}
				  <div class="product-price">
					{if $product.has_discount}
					  {if $product.discount_type === 'percentage'}
						<span class="discount-percentage">{$product.discount_percentage}</span>
					  {/if}
					{/if}

					{hook h='displayProductPriceBlock' product=$product type="before_price"}

					{hook h='displayProductPriceBlock' product=$product type='unit_price'}

					{hook h='displayProductPriceBlock' product=$product type='weight'}
				  </div>
				{/if}
			{/block}
			<ul class="add-to-links">
				<li class="cart">
					{include file='catalog/_partials/customize/button-cart.tpl' product=$product}
				</li>
				<li>
					<a href="#" class="quick-view" data-link-action="quickview" title="{l s='Quick view' d='Shop.Theme.Actions'}"><i class="ion-eye"></i>{l s='Quick view' d='Shop.Theme.Actions'}</a>
				</li>
				<li>
					{hook h='displayProductListFunctionalButtons' product=$product}
				</li>
			</ul>
		</div>
		<div class="product_desc">
			{block name='product_name'}
			  <h1 itemprop="name"><a href="{$product.url}" class="product_name">{$product.name|truncate:100:'...'}</a></h1>
			{/block}
			{block name='product_reviews'}
				<div class="hook-reviews">
				{hook h='displayProductListReviews' product=$product}
				</div>
			{/block}
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
				  <span itemprop="price" class="price">{$product.price}</span>
				  {hook h='displayProductPriceBlock' product=$product type='unit_price'}

				  {hook h='displayProductPriceBlock' product=$product type='weight'}
				</div>
			  {/if}
			{/block}
			{block name='product_description_short'}
				<div class="product-desc" itemprop="description">{$product.description_short|truncate:300:'...' nofilter}</div>
			{/block}
			<ul class="add-to-links">
				<li class="cart">
					{include file='catalog/_partials/customize/button-cart.tpl' product=$product}
				</li>
				<li>
					<a href="#" class="quick-view" data-link-action="quickview" title="{l s='Quick view' d='Shop.Theme.Actions'}"><i class="ion-eye"></i>{l s='Quick view' d='Shop.Theme.Actions'}</a>
				</li>
				<li>
					<a href="{$product.url}" class="links-details" title="{l s='Details' d='Shop.Theme.Actions'}"><i class="ion-ios-copy"></i>{l s='Details' d='Shop.Theme.Actions'}</a>
				</li>
			</ul>
			{block name='product_variants'}
			{if $product.main_variants}
			{include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
			{/if}
			{/block}
		</div>
	  </article>
{/block}

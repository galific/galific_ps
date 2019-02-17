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

{block name='header_top'}
  <div class="container">
       <div class="row" style="padding: 15px;">
		<div class="header_logo col-left col col-lg-3 col-md-12 col-xs-6">
		  <a href="{$urls.base_url}" class="clearfix">
			<img class="logo img-responsive" style="max-width: 150px;" src="{$shop.logo}" alt="{$shop.name}">
		  </a>
		</div>
		<div class="col-right col col-xs-12 col-lg-9 col-md-12 display_top">
			{hook h='displayTop'} 
		</div>
      </div>
    </div>
  </div>
<div class="header-bottom">
	<div class="container">
		{hook h='displaymegamenu'}
	</div>
</div>
  {hook h='displayNavFullWidth'}
{/block}

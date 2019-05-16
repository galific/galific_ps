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
       <div class="row header-top" style="padding: 15px 0;">
		<div class="header_logo col-left col col-lg-3 col-md-12 col-xs-6">
		  <a href="{$urls.base_url}" class="clearfix">
			<img class="logo img-responsive"  src="{$shop.logo}" alt="{$shop.name}">
		  </a>
		</div>
		<div class="col-right col col-sm-9 col-lg-9 col-md-12 display_top">
			<div class="top-links clearfix"> 	{hook h='displayTop'}  </div> 
		</div>
      </div>
    </div>
  </div>
<div class="header-bottom">
	 <div class="seller-nav">
                <ul class="list-inline">
                        <li class="list-item"><i class="fa fa-shopping-cart"></i><a href="https://galific.com/login?create_account=1"> Sell Your Craft  </a></li>
                        <li class="list-item"><i class="fa fa-cart-plus" aria-hidden="true"></i><a href=""> Bulk Order</a>  </li>
                        <li class="list-item"><i class="fa fa-rss-square" aria-hidden="true"></i><a href="https://galific.com/artists/blog/"> Blog </a> </li>
                        <li class="list-item"><i class="fa fa-user-plus" aria-hidden="true"></i><a href="https://galific.com/artists/wp-login.php?action=register"> Create your Portfolio </a> </li>
                        <li class="list-item"><i class="fa fa-user-secret" aria-hidden="true"></i><a href="https://galific.com/artists/"> Our Artists </a></li>
                </ul>
        </div>

	<div class="container">
		{hook h='displaymegamenu'}
	</div>
</div>
  {hook h='displayNavFullWidth'}
{/block}

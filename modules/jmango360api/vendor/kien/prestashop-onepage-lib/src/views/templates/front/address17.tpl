{**
 * 2007-2018 PrestaShop
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
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!doctype html>
<html lang="{$language.iso_code}">
<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
</head>
<body id="address" class="{$page.body_classes|classnames}">

<section id="main">
  <div class="container">
    <header class="page-header">
      <h1>
          {block name='page_title'}
              {if $editing}
                  {l s='Update your address' d=$module_name}
              {else}
                  {l s='New address' d=$module_name}
              {/if}
          {/block}
      </h1>
    </header>
    <section id="content" class="page-content">
      {block name='page_content'}
        <div class="address-form">
          {render template="address-form-17.tpl" ui=$address_form}
        </div>
      {/block}
    </section>
  </div>
</section>

{block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{/block}

<script type="text/javascript">
    jQuery('link[href*="/themes/specialdev603/assets/css/custom.css"]').prop('disabled', true);
</script>

</body>
</html>
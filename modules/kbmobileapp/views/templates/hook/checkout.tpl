<!doctype html>
<html lang="{$language.iso_code}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>
{* Changes started by rishabh jain on 3rd sep 2018 to hide order summary content *}
<style>
        #order-summary-content {
            display:none!important;
        }
        .condition-label {
                pointer-events: none;
        }
</style>
{* Changes over *}
  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}

    <header id="header">
      {block name='header'}
      {/block}
    </header>

    {block name='notifications'}
    {/block}

    <section id="wrapper">
      <div class="container">

      {block name='content'}
        <section id="content">
          <div class="row">
            <div class="col-md-8">
              {block name='cart_summary'}
                {render file='module:kbmobileapp/views/templates/hook/checkout-process.tpl' ui=$checkout_process}
              {/block}
            </div>
            <div class="col-md-4">

              {block name='cart_summary'}
              {/block}

            </div>
          </div>
        </section>
      {/block}
      </div>
    </section>

    <footer id="footer">
      {block name='footer'}
      {/block}
    </footer>

    {block name='javascript_bottom'}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {block name='hook_before_body_closing_tag'}
    {/block}

  </body>

</html>

{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Hook tpl file
*}
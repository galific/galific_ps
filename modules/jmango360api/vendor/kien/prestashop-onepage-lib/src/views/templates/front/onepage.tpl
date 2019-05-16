{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<!doctype html>
<html lang="{$language.iso_code|escape:'html':'UTF-8'}">
<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
    <style type="text/css">
        body#checkout section#wrapper {
            padding-top: 15px
        }

        body#checkout section#content {
            margin-bottom: 15px
        }

        body#checkout section#js-checkout-summary {
            margin-bottom: 0;
        }
    </style>
</head>
<body id="checkout" class="{$page.body_classes|classnames}">

{hook h='displayAfterBodyOpeningTag'}

{block name='notifications'}
    {include file='_partials/notifications.tpl'}
{/block}

<section id="wrapper">
    <div class="container">
        {block name='content'}
            <section id="content">
                <div class="row">
                    <div class="col-md-8">
                        {render file='checkout/checkout-process.tpl' ui=$checkout_process}
                    </div>
                    <div class="col-md-4">
                        {include file='checkout/_partials/cart-summary.tpl' cart = $cart}
                        {hook h='displayReassurance'}
                    </div>
                </div>
            </section>
        {/block}
    </div>
</section>

{block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{/block}

{hook h='displayBeforeBodyClosingTag'}

</body>
</html>

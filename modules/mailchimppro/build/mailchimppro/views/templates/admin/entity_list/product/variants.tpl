{*
 * PrestaChamps
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Commercial License
 * you can't distribute, modify or sell this code
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file
 * If you need help please contact leo@prestachamps.com
 *
 * @author    Mailchimp
 * @copyright PrestaChamps
 * @license   commercial
 *}
{foreach $variants as $name => $variant}
    <p class="well">
        <b>
            <a href="{$variant.url}">
                {$variant.title}
            </a>
        </b>
        <small style="color: red">SKU: {$variant.sku}</small>
        <small style="color: rebeccapurple">PRICE: {$variant.price}</small>
    </p>
{/foreach}
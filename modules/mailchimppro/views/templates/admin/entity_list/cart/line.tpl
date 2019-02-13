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
{foreach  $lines as $product}
    <p>
        <b>{$product.product_variant_title}</b>
        <small style="color: red">PID: {$product.product_id}, CID: {$product.product_variant_id}</small>
        <small style="color: blue">QTY: {$product.quantity}</small>
        <small style="color: green">{$product.price} {$currency_code}</small>
    </p>
{/foreach}
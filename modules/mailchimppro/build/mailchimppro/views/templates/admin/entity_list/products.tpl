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
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>{l s='ID' mod='mailchimppro'}</th>
            <th></th>
            <th>{l s='Name' mod='mailchimppro'}</th>
            <th>{l s='Description' mod='mailchimppro'}</th>
            <th>{l s='Type' mod='mailchimppro'}</th>
            <th>{l s='Vendor' mod='mailchimppro'}</th>
            <th>{l s='Variants' mod='mailchimppro'}</th>
            <th>#</th>
        </tr>
        </thead>
        <tbody>
        {foreach $products as $product}
            <tr>
                <td>{$product.id}</td>
                <td>
                    <img src="{$product.image_url}" class="img-responsive" style="max-width: 75px">
                </td>
                <td>
                    <a href="{$product.url}">
                        {$product.title}
                    </a>
                </td>
                <td>{$product.description}</td>
                <td>{$product.type}</td>
                <td>{$product.vendor}</td>
                <td>
                    {include file='./product/variants.tpl' variants=$product.variants}
                </td>
                <td>
                    <a href="{LinkHelper::getAdminLink('AdminMailchimpProProducts', true, [], ['action' => 'entitydelete', 'entity_id' => $product.id])}">
                        Delete
                    </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
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

<tr>
    <td>{$order.id}</td>
    <td>
        {if isset($order.customer)}
            <b>{$order.customer.email_address}</b>
            <small>ID: {$order.customer.id}</small>
        {else}
            <span class="text-danger">{l s='No customer' mod='mailchimppro'}</span>
        {/if}
    </td>
    <td>
        {$order.store_id}
    </td>
    <td>
        {$order.financial_status}
    </td>
    <td>
        {$order.fulfillment_status}
    </td>
    <td>
        {$order.order_total} {$order.currency_code}
    </td>
    <td>
        {$order.discount_total} {$order.currency_code}
    </td>
    <td>
        {$order.tax_total} {$order.currency_code}
    </td>
    <td>
        {$order.shipping_total} {$order.currency_code}
    </td>
    <td>
        {$order.processed_at_foreign}
    </td>
    <td>
        {include file='./../cart/line.tpl' lines=$order.lines currency_code=$order.currency_code}
    </td>
    <td>
        <a href="{$link->getAdminLink('AdminMailchimpProOrders', true, [], ['action' => 'entitydelete', 'entity_id' => $order.id])}">
            Delete
        </a>
    </td>
</tr>

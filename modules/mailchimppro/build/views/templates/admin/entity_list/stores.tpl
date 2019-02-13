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
            <th>{l s='List ID' mod='mailchimppro'}</th>
            <th>{l s='Name' mod='mailchimppro'}</th>
            <th>{l s='Platform' mod='mailchimppro'}</th>
            <th>{l s='Domain' mod='mailchimppro'}</th>
            <th>{l s='Is syncing' mod='mailchimppro'}</th>
            <th>{l s='Is active' mod='mailchimppro'}</th>
            <th>{l s='Email address' mod='mailchimppro'}</th>
            <th>{l s='Currency code' mod='mailchimppro'}</th>
            <th>{l s='Money format' mod='mailchimppro'}</th>
            <th>{l s='Primary locale' mod='mailchimppro'}</th>
            <th>{l s='Timezone' mod='mailchimppro'}</th>
            <th>{l s='Phone' mod='mailchimppro'}</th>
            <th>{l s='Address' mod='mailchimppro'}</th>
            <th>{l s='Automations' mod='mailchimppro'}</th>
            <th>{l s='List is active' mod='mailchimppro'}</th>
            <th>{l s='Created at' mod='mailchimppro'}</th>
            <th>{l s='Updated at' mod='mailchimppro'}</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        {foreach $stores as $store}
            <tr>
                <td>{$store.id}</td>
                <td>{$store.list_id}</td>
                <td>{$store.name}</td>
                <td>{$store.platform}</td>
                <td>{$store.domain}</td>
                <td>{$store.is_syncing}</td>
                <td>{$store.list_is_active}</td>
                <td>{$store.email_address}</td>
                <td>{$store.currency_code}</td>
                <td>{$store.money_format}</td>
                <td>{$store.primary_locale}</td>
                <td>{$store.timezone}</td>
                <td>{$store.phone}</td>
                <td>{', '|implode:$store.address|escape:'htmlall':'UTF-8'}</td>
                <td>
                    {$JSON_PRETTY_PRINT = 128}
                    {foreach $store.automations as $name => $automation}
                        <div class="well">
                            <p><b>{$name}</b></p>
                            <pre>{json_encode($automation, $JSON_PRETTY_PRINT)}</pre>
                        </div>
                    {/foreach}
                </td>
                <td>{$store.list_is_active}</td>
                <td>{$store.created_at}</td>
                <td>{$store.updated_at}</td>
                <td>
                    <a href="{LinkHelper::getAdminLink('AdminMailchimpProStores', true, [], ['action' => 'entitydelete', 'entity_id' => $store.id])}">
                        Delete
                    </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
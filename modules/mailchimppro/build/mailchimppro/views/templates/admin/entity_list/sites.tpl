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
            <th>{l s='Foreign ID' mod='mailchimppro'}</th>
            <th>{l s='Store ID' mod='mailchimppro'}</th>
            <th>{l s='Platform' mod='mailchimppro'}</th>
            <th>{l s='Domain' mod='mailchimppro'}</th>
            <th>{l s='Site script' mod='mailchimppro'}</th>
            <th>#</th>
        </tr>
        </thead>
        <tbody>
        {foreach $sites as $site}
            <tr>
                <td>{$site.foreign_id}</td>
                <td>{$site.store_id}</td>
                <td>{$site.platform}</td>
                <td>{$site.domain}</td>
                <td>{$site.site_script.url}</td>
                <td>
                    <a href="{LinkHelper::getAdminLink('AdminMailchimpProSites', true, [], ['action' => 'entitydelete', 'entity_id' => $site.foreign_id])}">
                        Delete
                    </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
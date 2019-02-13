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
            <th>{l s='Email' mod='mailchimppro'}</th>
            <th>{l s='Email type' mod='mailchimppro'}</th>
            <th>{l s='Status' mod='mailchimppro'}</th>
            <th>{l s='IP signup' mod='mailchimppro'}</th>
            <th>{l s='Signup time' mod='mailchimppro'}</th>
            <th>{l s='IP Opt-in' mod='mailchimppro'}</th>
            <th>{l s='Language' mod='mailchimppro'}</th>
            <th>{l s='VIP' mod='mailchimppro'}</th>
        </tr>
        </thead>
        <tbody>
        {foreach $members as $member}
            <tr>

                <td>{$member.id}</td>
                <td>{$member.email_address}</td>
                <td>{$member.email_type}</td>
                <td>{$member.status}</td>
                <td>{$member.ip_signup}</td>
                <td>{$member.timestamp_signup}</td>
                <td>{$member.ip_opt}</td>
                <td>{$member.language}</td>
                <td>{$member.vip}</td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
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
            <th>{l s='Title' mod='mailchimppro'}</th>
            <th>{l s='Starts at' mod='mailchimppro'}
            <th>{l s='Ends at' mod='mailchimppro'}
            <th>{l s='Amount' mod='mailchimppro'}
            <th>{l s='type' mod='mailchimppro'}
            <th>{l s='target' mod='mailchimppro'}
            <th>{l s='enabled' mod='mailchimppro'}
            <th>#</th>
        </tr>
        </thead>
        <tbody>
        {foreach $promo_rules as $promo_rule}
            <tr>
                <td>{$promo_rule.id}</td>
                <td>{$promo_rule.title}</td>
                <td>{$promo_rule.starts_at}</td>
                <td>{$promo_rule.ends_at}</td>
                <td>{$promo_rule.amount}</td>
                <td>{$promo_rule.type}</td>
                <td>{$promo_rule.target}</td>
                <td>{$promo_rule.enabled}</td>
                <td>
                    <div class="btn-group  btn-group-xs" role="group" aria-label="...">
                        <a class="btn btn-default"
                           href="{LinkHelper::getAdminLink('AdminMailchimpProPromoCodes', true, [], ['action' => 'entitydelete', 'entity_id' => $promo_rule.id])}">
                            Delete
                        </a>

                        <a class="btn btn-default"
                           href="{LinkHelper::getAdminLink('AdminMailchimpProPromoCodes', true, [], ['rule_id' => $promo_rule.id])}">
                            {l s='Promo codes' mod='mailchimppro'}
                        </a>
                    </div>

                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
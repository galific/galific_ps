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
            <th>{l s='Status' mod='mailchimppro'}</th>
            <th>{l s='Total operations' mod='mailchimppro'}</th>
            <th>{l s='Finished operations' mod='mailchimppro'}</th>
            <th>{l s='Failed operations' mod='mailchimppro'}</th>
            <th>{l s='Submitted at' mod='mailchimppro'}</th>
            <th>{l s='Completed at' mod='mailchimppro'}</th>
            <th>#</th>
        </tr>
        </thead>
        <tbody>
        {foreach $batches as $batch}
            <tr>
                <td>
                    <a href="{LinkHelper::getAdminLink('AdminMailchimpProBatches', true, [], ['action' => 'single', 'entity_id' => $batch.id])}">
                        {$batch.id}
                    </a>
                </td>
                <td>{$batch.status}</td>
                <td>{$batch.total_operations}</td>
                <td>{$batch.finished_operations}</td>
                <td>{$batch.errored_operations}</td>
                <td>{$batch.submitted_at}</td>
                <td>{$batch.completed_at}</td>
                <td>
                    <a href="{LinkHelper::getAdminLink('AdminMailchimpProBatches', true, [], ['action' => 'entitydelete', 'entity_id' => $batch.id])}">
                        Delete
                    </a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
</div>
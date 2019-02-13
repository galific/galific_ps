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
<div class="panel">
    <div class="panel-heading">
        {l s='Batch operation' mod='mailchimppro'} #{$entity.id}
    </div>
    <div class="panel-body">
        <table class="table table-striped table-bordered">
            <thead>
            <tbody>
                <tr>
                    <td>{l s='ID' mod='mailchimppro'}</td>
                    <td>{$entity.id}</td>
                </tr>
                <tr>
                    <td>{l s='Status' mod='mailchimppro'}</td>
                    <td>{$entity.status}</td>
                </tr>
                <tr>
                    <td>{l s='Total operations' mod='mailchimppro'}</td>
                    <td>{$entity.total_operations}</td>
                </tr>
                <tr>
                    <td>{l s='Finished operations' mod='mailchimppro'}</td>
                    <td>{$entity.finished_operations}</td>
                </tr>
                <tr>
                    <td>{l s='Errored operations' mod='mailchimppro'}</td>
                    <td>{$entity.errored_operations}</td>
                </tr>
                <tr>
                    <td>{l s='Submitted at' mod='mailchimppro'}</td>
                    <td>{$entity.submitted_at}</td>
                </tr>
                <tr>
                    <td>{l s='Completed at' mod='mailchimppro'}</td>
                    <td>{$entity.completed_at}</td>
                </tr>
                <tr>
                    <td>{l s='Response body url' mod='mailchimppro'}</td>
                    <td>{$entity.response_body_url}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

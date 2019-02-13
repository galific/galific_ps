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
<div class="text-center">
    <div class="alert alert-info alert-mc" id="customer-sync-in-progress">
        {l s='Syncing customers, please wait' mod='mailchimppro'}
    </div>
    <div class="alert alert-success alert-mc hidden" id="customer-sync-completed">
        {l s='The batch operation of syncing the customers has been sent to the Mailchimp servers. The setup is completed' mod='mailchimppro'}
    </div>
    <div class="alert alert-error hidden" id="customer-sync-error">
        {l s='Error during customer sync' mod='mailchimppro'}
    </div>
    <div class="progress hidden">
        <div class="progress-bar" style="width:0"></div>
    </div>
</div>
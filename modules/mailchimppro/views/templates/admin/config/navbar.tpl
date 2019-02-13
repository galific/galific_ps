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
<div class="row">
    <div class="col-sm-6 hidden-xs">
        <img class="img-responsive" src="../modules/mailchimppro/views/img/logo-horizontal.png" height="326" width="1200" style="max-height: 32px; width: auto">
    </div>
    <div class="col-sm-6">
        <div class="btn-group pull-right" role="group" style="height:100%; vertical-align:center;line-height : 100%;">
            <a class="btn btn-default" href="{LinkHelper::getAdminLink('AdminMailchimpProWizard')}">
                <i class="icon icon-floppy-o" aria-hidden="true"></i>

                {l s='Setup wizard' mod='mailchimppro'}
            </a>
            <a class="btn btn-default" href="{LinkHelper::getAdminLink('AdminMailchimpProSync')}">
                <i class="icon icon-retweet" aria-hidden="true"></i>
                {l s='Sync' mod='mailchimppro'}
            </a>
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-primary btn-mc dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                        aria-expanded="false">
                    <i class="icon icon-folder-open-o" aria-hidden="true"></i>
                    {l s='Mailchimp Objects' mod='mailchimppro'}
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProBatches')}">
                            {l s='Batches' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProCarts')}">
                            {l s='Carts' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProCustomers')}">
                            {l s='Customers' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProLists')}">
                            {l s='Lists' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProOrders')}">
                            {l s='Orders' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProProducts')}">
                            {l s='Products' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProStores')}">
                            {l s='Stores' mod='mailchimppro'}
                        </a>
                    </li>

                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProSites')}">
                            {l s='Sites' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProAutomations')}">
                            {l s='Automations' mod='mailchimppro'}
                        </a>
                    </li>
                    <li>
                        <a href="{LinkHelper::getAdminLink('AdminMailchimpProPromoRules')}">
                            {l s='Promo rules' mod='mailchimppro'}
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<hr>
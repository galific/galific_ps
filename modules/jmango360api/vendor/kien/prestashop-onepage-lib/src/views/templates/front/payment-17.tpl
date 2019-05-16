{*
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*}
<!doctype html>
<html lang="{$language.iso_code}">
<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
    {literal}
        <style type="text/css">
            body {
                padding: 1em
            }
        </style>
    {/literal}
</head>
<body id="checkout" class="{$page.body_classes|classnames}">
<div class="payment-options">
    {foreach from=$payment_options item="module_options"}
        {foreach from=$module_options item="option"}
            <div>
                <div id="{$option.id}-container" class="payment-option clearfix">
                    {* This is the way an option should be selected when Javascript is enabled *}
                    <span class="custom-radio pull-xs-left">
                        <input
                                class="ps-shown-by-js {if $option.binary} binary {/if}"
                                id="{$option.id}"
                                data-module-name="{$option.module_name}"
                                name="payment-option"
                                type="radio"
                                required
                                {if $selected_payment_option == $option.id} checked {/if}>
                        <span></span>
                    </span>
                    {* This is the way an option should be selected when Javascript is disabled *}
                    <form method="GET" class="ps-hidden-by-js">
                        {if $option.id === $selected_payment_option}
                            {l s='Selected' d=$module_name}
                        {else}
                            <button class="ps-hidden-by-js" type="submit" name="select_payment_option"
                                    value="{$option.id}">
                                {l s='Choose' d=$module_name}
                            </button>
                        {/if}
                    </form>

                    <label for="{$option.id}">
                        <span>{$option.call_to_action_text}</span>
                        {if $option.logo}
                            <img src="{$option.logo}">
                        {/if}
                    </label>
                </div>
            </div>
            {if $option.additionalInformation}
                <div id="{$option.id}-additional-information"
                     class="js-additional-information definition-list additional-information{if $option.id != $selected_payment_option} ps-hidden {/if}">
                    {$option.additionalInformation}
                </div>
            {/if}
            <div id="pay-with-{$option.id}-form"
                 class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}">
                {if $option.form}
                    {$option.form}
                {else}
                    <form id="payment-form" method="POST" action="{$option.action}">
                        {foreach from=$option.inputs item=input}
                            <input type="{$input.type}" name="{$input.name}" value="{$input.value}">
                        {/foreach}
                        <button style="display:none" id="pay-with-{$option.id}" type="submit"></button>
                    </form>
                {/if}
            </div>
        {/foreach}
        {foreachelse}
        <p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.' d=$module_name}</p>
    {/foreach}
</div>
{if $conditions_to_approve|count}
    <p class="ps-hidden-by-js">
        {* At the moment, we're not showing the checkboxes when JS is disabled
           because it makes ensuring they were checked very tricky and overcomplicates
           the template. Might change later.
        *}
        {l s='By confirming the order, you certify that you have read and agree with all of the conditions below:' d=$module_name}
    </p>
    <form id="conditions-to-approve" method="GET">
        <ul>
            {foreach from=$conditions_to_approve item="condition" key="condition_name"}
                <li>
                    <div class="pull-xs-left">
                        <span class="custom-checkbox">
                            <input id="conditions_to_approve[{$condition_name}]"
                                   name="conditions_to_approve[{$condition_name}]"
                                   required
                                   type="checkbox"
                                   value="1"
                                   class="ps-shown-by-js">
                            <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
                        </span>
                    </div>
                    <div class="condition-label">
                        <label class="js-terms" for="conditions_to_approve[{$condition_name}]">
                            {$condition}
                        </label>
                    </div>
                </li>
            {/foreach}
        </ul>
    </form>
{/if}

<div id="payment-confirmation">
    <div class="ps-shown-by-js">
        <button type="submit" {if !$selected_payment_option} disabled {/if} class="btn btn-primary center-block">
            {l s='Order with an obligation to pay' d=$module_name}
        </button>
        {if $show_final_summary}
            <article class="alert alert-danger m-t-2 js-alert-payment-conditions" role="alert" data-alert="danger">
                {l
                s='Please make sure you\'ve chosen a [1]payment method[/1] and accepted the [2]terms and conditions[/2].'
                sprintf=[
                '[1]' => '<a href="#checkout-payment-step">',
                '[/1]' => '</a>',
                '[2]' => '<a href="#conditions-to-approve">',
                '[/2]' => '</a>'
                ]
                d=$module_name
                }
            </article>
        {/if}
    </div>
    <div class="ps-hidden-by-js">
        {if $selected_payment_option and $all_conditions_approved}
            <label for="pay-with-{$selected_payment_option}">{l s='Order with an obligation to pay' d=$module_name}</label>
        {/if}
    </div>
</div>

{hook h='displayPaymentByBinaries'}

<div class="modal fade" id="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>

{block name='javascript_bottom'}
    {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
{/block}
</body>
</html>
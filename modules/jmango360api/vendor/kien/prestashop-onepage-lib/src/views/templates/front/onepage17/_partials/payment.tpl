{**
* @license Created by JMango
*}

{hook h='displayPaymentTop'}
<div class="content" style="padding: 2.313rem 2.313rem;">
    <form
            class="clearfix"
            id="co-payment-form"
            data-url-update="{url entity='order' params=['ajax' => 1, 'action' => 'selectPaymentOption']}"
            method="post"

    >
        {if $is_free}
            <p>{l s='No payment needed for this order' d=$module_name}</p>
        {/if}
        <div class="payment-options {if $is_free}hidden-xs-up{/if}">
            {foreach from=$payment_options item="module_options"}
                {foreach from=$module_options item="option"}
                    <div>
                        <div id="{$option.id}-container" class="payment-option clearfix">
                            {* This is the way an option should be selected when Javascript is enabled *}
                            <span class="custom-radio float-xs-left">
              <input
                      class="ps-shown-by-js {if $option.binary} binary {/if}"
                      id="{$option.id}"
                      data-module-name="{$option.module_name}"
                      name="payment-option"
                      type="radio"
                      required
                      {if $selected_payment_option == $option.id || $is_free} checked {/if}
              >
              <span></span>
            </span>
                            {* This is the way an option should be selected when Javascript is disabled *}
                            <form method="GET" class="ps-hidden-by-js">
                                {if $option.id === $selected_payment_option}
                                    {l s='Selected' d=$module_name}
                                {else}
                                    <button class="ps-hidden-by-js" type="submit"
                                            name="select_payment_option" value="{$option.id}">
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
                        <div
                                id="{$option.id}-additional-information"
                                class="js-additional-information definition-list additional-information{if $option.id != $selected_payment_option} ps-hidden {/if}"
                        >
                            {$option.additionalInformation}
                        </div>
                    {/if}
                    <div
                            id="pay-with-{$option.id}-form"
                            class="js-payment-option-form {if $option.id != $selected_payment_option} ps-hidden {/if}"
                    >

                        {if $option.form}
                            {$option.form}
                        {else}
                            <form id="payment-form-submit-{$option.id}" method="POST"
                                  action="{$option.action}">
                                {foreach from=$option.inputs item=input}
                                    <input type="{$input.type}" name="{$input.name}"
                                           value="{$input.value}">
                                {/foreach}
                                <button style="display:none" id="pay-with-{$option.id}"
                                        type="submit"></button>
                            </form>
                        {/if}
                    </div>
                {/foreach}
                {foreachelse}
                <p class="alert alert-danger">{l s='Unfortunately, there are no payment method available.' d=$module_name}</p>
            {/foreach}
        </div>


        {hook h='displayPaymentByBinaries'}



        <div class="buttons-set" id="payment-buttons-container">
            <button id="payment-button" type="button" title="Continue" class="ladda-button"
                    onclick="paymentMethod.save();" data-color="jmango" data-style="slide-up"
                    data-size="s">
                <span class="ladda-label">{l s='Continue' d=$module_name}</span>
                <span class="ladda-spinner"></span></button>
            <div class="ladda-progress" style="width: 0px;"></div>
        </div>
    </form>
</div>

<script type="text/javascript">
    var paymentMethod = new PaymentMethod('co-payment-form', "{url entity='module' name=$module_name controller='jmcheckout' relative_protocol=false}");
</script>


{**
* @license Created by JMango
*}
<form id="co-shipping-method-form" action="">
    <div id="checkout-shipping-method-load" class="shipping-methods">
        <dl class="sp-methods">
            {if isset($delivery_option_list)}
                {foreach $cart->getDeliveryAddressesWithoutCarriers(true) as $address}
                    <!-- TODO Show {l s='No Delivery Method Available' mod='jmango360api'} -->
                    {foreachelse}
                    {foreach $delivery_option_list as $id_address => $option_list}
                        {foreach $option_list as $key => $option}
                            <dt class="carrier-id-{$key|intval}">
                                {if isset($delivery_option[$id_address]) && $delivery_option[$id_address] == $key}
                                    <input class="myopccheckout_shipping_option" type="radio"
                                           name="delivery_option[{$id_address|intval}]"
                                           value="{$key|escape:'htmlall':'UTF-8'}"
                                           id="shipping_method_{$id_address|intval}_{$option@index|intval}"
                                           checked="checked"/>
                                {elseif isset($default_shipping_method) && $key == $default_shipping_method}
                                    <input class="myopccheckout_shipping_option" type="radio"
                                           name="delivery_option[{$id_address|intval}]"
                                           value="{$key|escape:'htmlall':'UTF-8'}"
                                           id="shipping_method_{$id_address|intval}_{$option@index|intval}"
                                           checked="checked"/>
                                {else}
                                    <input class="myopccheckout_shipping_option" type="radio"
                                           name="delivery_option[{$id_address|intval}]"
                                           value="{$key|escape:'htmlall':'UTF-8'}"
                                           id="shipping_method_{$id_address|intval}_{$option@index|intval}"/>
                                {/if}
                                <label for="shipping_method_{$id_address|intval}_{$option@index|intval}">
                                    {assign var='sub_carriers_count' value=count($option.carrier_list)}
                                    {foreach $option.carrier_list as $carrier}
                                        {if $carrier.logo}
                                            <img src="{$carrier.logo|escape:'htmlall':'UTF-8'}"
                                                 alt="{$carrier.instance->name|escape:'htmlall':'UTF-8'}"
                                                 {if isset($carrier.width) && $carrier.width != ""}width="{$carrier.width|escape:'htmlall':'UTF-8'}"{/if} {if isset($carrier.height) && $carrier.height != ""}height="{$carrier.height|escape:'htmlall':'UTF-8'}"{/if}/>
                                        {/if}
                                    {/foreach}
                                    <strong>{$carrier.instance->name|escape:'htmlall':'UTF-8'}</strong><br/>
                                    {if $option.unique_carrier && isset($carrier.instance->delay[$cookie->id_lang])}
                                        <span class="desc-shipping">{l s='Delivery time:' mod='jmango360api'}
                                            &nbsp;{$carrier.instance->delay[$cookie->id_lang]|escape:'htmlall':'UTF-8'}</span>
                                    {/if}
                                    {if count($option_list) > 1}
                                        <span class="desc-shipping">
                                            {if $option.is_best_grade}
                                                {if $option.is_best_price}
                                                    {l s='The best price and speed' mod='jmango360api'}
                                                {else}
                                                    {l s='The Fastest' mod='jmango360api'}
                                                {/if}
                                            {else}
                                                {if $option.is_best_price}
                                                    {l s='The Best Price' mod='jmango360api'}
                                                {/if}
                                            {/if}
                                        </span>
                                    {/if}
                                    <br/>
                                    <span class="price-shipping">
                                        {if $option.total_price_with_tax && (isset($option.is_free) && $option.is_free == 0) && (!isset($free_shipping) || (isset($free_shipping) && !$free_shipping))}
                                            {if $use_taxes == 1}
                                                {if $priceDisplay == 1}
                                                    {convertPrice price=$option.total_price_without_tax} {l s='(Tax excl.)' mod='jmango360api'}
                                                {else}
                                                    {convertPrice price=$option.total_price_with_tax} {l s='(Tax incl.)' mod='jmango360api'}
                                                {/if}
                                            {else}
                                                {convertPrice price=$option.total_price_without_tax}
                                            {/if}
                                        {else}
                                            {l s='Free' mod='jmango360api'}
                                        {/if}
                                    </span>
                                </label>
                            </dt>
                        {/foreach}
                    {/foreach}
                {/foreach}
            {/if}
        </dl>
    </div>
    <div class="buttons-set" id="shipping-method-buttons-container">
        <button id="shipping-method-button" type="button" class="ladda-button" onclick="shippingMethod.save()"
                data-style="slide-up" data-color="jmango" data-size="s">
            <span class="ladda-label">{l s='Continue' mod='jmango360api'}</span>
            <span class="ladda-spinner"></span>
            <div class="ladda-progress" style="width: 0px;"></div>
        </button>
    </div>
</form>

{if isset($link_to_register)}
    {if $kb_seller_agreement != ''}
        <a id="open_kb_seller_agreement_modal" class="col-lg-4 col-md-6 col-sm-6" href="javascript:void(0)" data-modal="kb_seller_agreement_modal" 
           title="{l s='Click to register as seller' mod='kbmarketplace'}" >
            <span class="link-item">
                <i class="kb-material-icons">&#xe563;</i>
                {l s='Register as seller' mod='kbmarketplace'}
            </span>
        </a>
    {else}
        <a class="col-lg-4 col-md-6 col-sm-6" href="javascript:void(0)" data-href="{$link_to_register nofilter}{* Variable contains HTML/CSS/JSON, escape not required *}" 
           title="{l s='Click to register as seller' mod='kbmarketplace'}" 
           onclick="takeconfirmationforregister(this)" >
            <span class="link-item">
                <i class="kb-material-icons">&#xe563;</i>
                {l s='Register as seller' mod='kbmarketplace'}
            </span>
        </a> {* Variable contains HTML/CSS/JSON, escape not required *}

    {/if}
    <script type="text/javascript">
        var kb_confirm_msg = "{l s='Are you sure?' mod='kbmarketplace'}";
        function takeconfirmationforregister(e){
            if(confirm(kb_confirm_msg)){
                location.href=$(e).attr('data-href');
            }
        }
    </script>
    

{elseif isset($menus) && count($menus) > 0}
    <div class="row_info" style="display:block;clear:both;width:100%;">	
        <h1 style="margin-left: 0.9375rem;">{l s='Seller Account' mod='kbmarketplace'}</h1>
        {foreach $menus as $menu}
            <a class="col-lg-4 col-md-6 col-sm-6" title="{$menu['title']|escape:'htmlall':'UTF-8'}" href="{$menu['href']|escape:'htmlall':'UTF-8'}">
                <span class="link-item">
                    <i class="kb-material-icons">{$menu['icon_class'] nofilter}</i> {* Variable contains HTML/CSS/JSON, escape not required *}

                    {$menu['label']|escape:'htmlall':'UTF-8'}
                </span>
            </a>
        {/foreach}
    </div>
{/if}
{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer tohttp://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}
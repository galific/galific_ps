{if true}
{*    <li class="lnk_wishlist">*}
        {if $kb_seller_agreement != ''}
            <a  id="open_kb_seller_agreement_modal_footer" href="javascript:void(0)" data-modal="kb_seller_agreement_modal_footer"
               title="{l s='Click to register as seller' mod='kbmarketplace'}" >
                    <span>{l s='Become a seller' mod='kbmarketplace'}</span>
            </a>
        
        {/if}
{*    </li>*}
    <script type="text/javascript">
        var kb_confirm_msg = "{l s='Are you sure?' mod='kbmarketplace'}";
        function takeconfirmationforregister(e){
            if(confirm(kb_confirm_msg)){
                location.href=$(e).attr('data-href');
            }
        }
    </script>
    


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
<div class="kb-block seller_profile_view">
    <div class="s-vp-banner">
        <img src="{$seller['banner'] nofilter}" /> {* Variable contains HTML/CSS/JSON, escape not required *}

    </div>
    <div class="info-view">
        <div class="seller-profile-photo">
            <a href="{KbGlobal::getSellerLink($seller['id_seller']) nofilter}" > {* Variable contains HTML/CSS/JSON, escape not required *}

                <img src="{$seller['logo'] nofilter}" title="{$seller['title']}" alt="{$seller['title']}"> {* Variable contains HTML/CSS/JSON, escape not required *}

            </a>
        </div>
        <div class="seller-info">
            <div class="seller-basic">
                <div class="seller-name">
                    <span class="name">
                        {$seller['title']}
                    </span>
                    <div class="seller-rating-block">
                    </div>
                </div>
                <div class="seller-social">
                    {if !empty($seller['twit_link'])}
                        <a title="{l s='Twitter' mod='kbmarketplace'}" href="{$seller['twit_link'] nofilter}" class="btn-sm btn-primary social-btn twitter" ></a> {* Variable contains HTML/CSS/JSON, escape not required *}

                    {/if}
                    {if !empty($seller['fb_link'])}
                        <a title="{l s='Facebook' mod='kbmarketplace'}" href="{$seller['fb_link'] nofilter}" class="btn-sm btn-primary social-btn facebook"></a> {* Variable contains HTML/CSS/JSON, escape not required *}

                    {/if}
                    {if !empty($seller['gplus_link'])}
                        <a title="{l s='Google+' mod='kbmarketplace'}" href="{$seller['gplus_link'] nofilter}" class="btn-sm btn-primary social-btn googleplus"></a> {* Variable contains HTML/CSS/JSON, escape not required *}

                    {/if}       
                </div>
            </div>
        </div>
            </div>
    {if !isset($seller['is_review_page'])}
        {if !empty($seller['description'])}
            <section class="slr-f-box">
                <h3 class="page-product-heading">{l s='About Seller' mod='kbmarketplace'}</h3>
                <div  class="rte slr-content">
                    {$seller['description'] nofilter}  {* Variable contains HTML/CSS/JSON, escape not required *}

                </div>
            </section>
        {/if}
        <section class="slr-f-box">
            <h3 class="page-product-heading">{l s='Privacy Policy' mod='kbmarketplace'}</h3>
            <div  class="rte slr-content">
                {if !empty($seller['privacy_policy'])}
                    {$seller['privacy_policy'] nofilter}{*Variable contains HTML content,escape not required*}
                {else}
                    {l s='No Privacy Policy Provided by Seller Yet.' mod='kbmarketplace'}
                {/if}

            </div>
        </section>
        <section class="slr-f-box">
            <h3 class="page-product-heading">{l s='Return Policy' mod='kbmarketplace'}</h3>
            <div  class="rte slr-content">
                {if !empty($seller['return_policy'])}
                    {$seller['return_policy'] nofilter}  {* Variable contains HTML/CSS/JSON, escape not required *}

                {else}
                    {l s='No Return Policy Provided by Seller Yet.' mod='kbmarketplace'}
                {/if}

            </div>
        </section>
        <section class="slr-f-box">
            <h3 class="page-product-heading">{l s='Shipping Policy' mod='kbmarketplace'}</h3>
            <div  class="rte slr-content">
                {if !empty($seller['shipping_policy'])}
                    {$seller['shipping_policy'] nofilter} {* Variable contains HTML/CSS/JSON, escape not required *}

                {else}
                    {l s='No Shipping Policy Provided by Seller Yet.' mod='kbmarketplace'}
                {/if}

            </div>
        </section>
        {hook h="displayKbSellerView" id_seller=$seller['id_seller'] area="profile"}
    {else}
        {hook h="displayKbSellerView" id_seller=$seller['id_seller'] area="review"}
    {/if}
</div>
    <script type="text/javascript">
            var kb_empty_field = "{l s='Field cannot be empty.' mod='kbmarketplace'}";
            var kb_email_valid = "{l s='Email is not valid.' mod='kbmarketplace'}";
            var seller_front_url = "{$filter_form_action}";
            var kb_email_not_exit= "{l s='Email Address is not associated with any account.' mod='kbmarketplace'}";
        </script>
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
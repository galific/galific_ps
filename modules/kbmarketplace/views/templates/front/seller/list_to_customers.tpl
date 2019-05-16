{if isset($sellers) && count($sellers) > 0}
    <h1 class="page-heading">
        <span clas="cat-name">{l s='Sellers' mod='kbmarketplace'}</span>
        <div class="clearfix"></div>
    </h1>

    <div class="row products-selection">
        <div class="col-lg-3 hidden-md-down total-products">
            <p>{$pagination_string nofilter}</p>  {* Variable contains HTML/CSS/JSON, escape not required *}

        </div>
        <div class="col-lg-5 col-md-6">
            <div class="row">
               
            </div>
        </div>
        {if isset($kb_pagination.pagination) && $kb_pagination.pagination neq ''}
            <div id="front-end-customer-pagination" class="top-pagination-content clearfix">
                <div class="sv-p-paging">
                    {$kb_pagination.pagination nofilter}  {* Variable contains HTML/CSS/JSON, escape not required *}

                    <div class='clearfix'></div>
                </div>
            </div>
        {/if}        
    </div>
    <div class="clearfix"></div>
        <img id="kb-list-loader" src="{$kb_image_path|escape:'htmlall':'UTF-8'}loader128.gif" />

    <div class='kbmp-_block'>

    </div>
    <div id="seller_list_to_customers">
        {include file="./seller_list.tpl"}
    </div>
    <script type="text/javascript">
        var kb_page_start = {$kb_pagination.page_position|intval};
    </script>
    
    
{else}
    <h1 class="page-heading" style='border:0;'>
        <span clas="cat-name">{$empty_list|escape:'htmlall':'UTF-8'}</span>
        <div class="clearfix"></div>
    </h1>
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
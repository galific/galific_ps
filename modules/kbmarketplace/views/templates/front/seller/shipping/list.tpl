<div class="kb-content">
    <div class="kb-content-header">
        <h1>{l s='Shippings' mod='kbmarketplace'}</h1>
        <div class="kb-content-header-btn">
        </div> {* Variable contains HTML/CSS/JSON, escape not required *}

        <div class="clearfix"></div>
    </div>
    <div class='kb-vspacer5'></div>
    {if isset($kblist)}
        <div class="kb-vspacer5"></div>
        {$kblist nofilter}  {* Variable contains HTML/CSS/JSON, escape not required *}

    {/if}
    
    </div>
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

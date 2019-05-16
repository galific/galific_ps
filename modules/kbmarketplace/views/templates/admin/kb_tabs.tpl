<div class="kb_custom_tabs kb_custom_panel">
    <span>
        <a class="kb_custom_tab {if $selected_nav == 'config'}kb_active{/if}" title="{l s='Marketplace General Settings' mod='kbmarketplace'}" id="kbsl_config_link" href="{$mp_setting}">{*Variable contains URL content, escape not required*}
            <i class="icon-gear"></i>
            {l s='General Settings' mod='kbmarketplace'}
        </a>
    </span>

    <span>
        <a class="kb_custom_tab {if $selected_nav == 'gdpr_config'}kb_active{/if}" title="{l s='GDPR Settings' mod='kbmarketplace'}" id="kbsl_gdpr_config" href="{$gdpr_setting}">{*Variable contains URL content, escape not required*}
            <i class="icon-lock"></i>
            {l s='GDPR Settings' mod='kbmarketplace'}
        </a>
    </span>
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
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*
* Description
*
* Admin tpl file
*}
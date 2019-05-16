{if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_edit_old'}
    <button class='edit btn btn-default' title='Edit' onclick='edit_row(this);'>
        <i class='icon-pencil'></i>{l s='Edit' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_edit_new'}
    <a class='edit btn btn-default' title='Edit' onclick='edit_row(this);'>
        <i class='icon-pencil'></i>{l s='Edit' mod='kbmobileapp'}
    </a>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_delete_old'}
    <button type='button' class='edit btn btn-default' title='delete' onclick='delete_confirmation(this);' style='cursor:pointer;'>
        <i class='icon-trash' style='padding-right:5px'></i>{l s='Delete' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_delete_new'} 
    <a class='delete' title='Delete' onclick='delete_confirmation(this);' style='cursor:pointer;'>
        <i class='icon-trash' style='padding-right:5px'></i>{l s='Delete' mod='kbmobileapp'}
    </a>
        {* changes by rishabh jain *}
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_edit_name_layout_old'}
    <button type='button' class='edit btn btn-default' title='delete' onclick='editLayoutName(this);' style='cursor:pointer;'>
        <i class='icon-pencil' style='padding-right:5px'></i>{l s='Edit Layout Name' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_edit_name_layout_new'} 
    <a class='delete' title='Delete' onclick='editLayoutName(this);' style='cursor:pointer;'>
        <i class='icon-pencil' style='padding-right:5px'></i>{l s='Edit Layout Name' mod='kbmobileapp'}
    </a>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_delete_banner_old'}
    <button class='edit btn btn-default' title='delete' onclick='delete_banner_slider(this);' >
           <i class='icon-trash' style='padding-right:5px'></i>{l s='Delete' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_delete_banner_new'} 
    <a class='edit btn btn-default' title='delete' onclick='delete_banner_slider(this);' >
           <i class='icon-trash' style='padding-right:5px'></i>{l s='Delete' mod='kbmobileapp'}
    </a>

{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_deletelayout_old'}
    <button type='button' class='edit btn btn-default' title='delete' onclick='delete_confirmation_layout(this);' style='cursor:pointer;'>
        <i class='icon-trash' style='padding-right:5px'></i>{l s='Delete' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_deletelayout_new'} 
    <a class='delete' title='Delete' onclick='delete_confirmation_layout(this);' style='cursor:pointer;'>
        <i class='icon-trash' style='padding-right:5px'></i>{l s='Delete' mod='kbmobileapp'}
    </a>
        {* changes over *}
        {* changes by rishabh jain *}
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'image_path_old'}
    <img src="{$path}" style="
    max-height: 36px;"/>
        {* changes over *}
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_enable_old'}
    <button type='button' class='edit btn btn-default enable_disable' onclick='enable_disable(this);' style='cursor:pointer;'>
        <i class='icon-wrench' style='padding-right:5px'></i>{l s='Enable/Disable' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_enable_new'}
    <a class='delete enable_disable' onclick='enable_disable(this);' style='cursor:pointer;'>
        <i class='icon-wrench' style='padding-right:5px'></i>{l s='Enable/Disable' mod='kbmobileapp'}
    </a>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_view_old'}
    <button type='button  btn btn-default' class='btn btn-default view_push_details' onclick='showNotificationDetails(this);' style='cursor:pointer;'>
        <i class="icon-search-plus"></i>{l s='Details' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_view_new'}
    <a class='view_push_details  btn btn-default' onclick='showNotificationDetails(this);' style='cursor:pointer;'>
        <i class="icon-search-plus"></i>{l s='Details' mod='kbmobileapp'}
    </a>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_editslider_old'}
    <button class='edit btn btn-default' title='Edit' onclick='editSlider(this);'>
        <i class='icon-pencil'></i>{l s='Edit' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_editslider_new'}
    <a class='edit btn btn-default' title='Edit' onclick='editSlider(this);'>
        <i class='icon-pencil'></i>{l s='Edit' mod='kbmobileapp'}
    </a>
        {* changes by rishabh jain *}
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_editlayout_old'}
    <button class='edit btn btn-default' title='Edit' onclick='editLayout(this);'>
        <i class='icon-pencil'></i>{l s='Edit' mod='kbmobileapp'}
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_editlayout_new'}
    <a class='edit btn btn-default' title='Edit' onclick='editLayout(this);'>
        <i class='icon-pencil'></i>{l s='Edit' mod='kbmobileapp'}
    </a>
{* chnages over *}
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_status_old'}
    <button class='list-action-enable {if $enabled} action-enabled{else} action-disabled{/if}' title='Edit' onclick='changeSliderStatus(this);'>
        <i class="icon-check {if !$enabled} hidden{/if}"></i><i class="icon-remove {if $enabled} hidden{/if}"></i>
    </button>
{else if isset($vss_cjc_link)  && $vss_cjc_link eq 'vss_status_new'}
    <a class='list-action-enable {if $enabled} action-enabled{else} action-disabled{/if}' title='Edit' onclick='changeSliderStatus(this);'>
        <i class="icon-check {if !$enabled} hidden{/if}"></i><i class="icon-remove {if $enabled} hidden{/if}"></i>
    </a>
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
    * @copyright 2015 Knowband
    * @license   see file: LICENSE.txt
    *
    * Description
    *
    * Admin tpl file
    *}


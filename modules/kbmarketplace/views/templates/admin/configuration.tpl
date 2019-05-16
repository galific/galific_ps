<div id="kb-seller-account-configuration">
    {if !empty($msg)}
        {$msg nofilter} {* Variable contains HTML, escape not required *}

    {/if}
    {$form_template nofilter} {* Variable contains HTML, escape not required *}

</div>


<script type='text/javascript'>
    var seller_setting_form_action = '{$action}';
    {literal}    
    $('document').ready(function(){
        if($('#customer_form').length){
            var config_html = $('#kb-seller-account-configuration').html();
            $('#kb-seller-account-configuration').html('');
            
            var new_html = '<form id="seller_configuration_form" method="post" class="defaultForm form-horizontal AdminSellerSetting" action="'+seller_setting_form_action+'">';
            new_html += config_html;
            new_html += '</form>';
            $('#customer_form').parent().append(new_html);
        }
    });
    {/literal}
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
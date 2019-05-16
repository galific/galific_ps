{$kb_tabs nofilter}{*Variable contains css and html content, escape not required*}
{$form_fields nofilter}{*Variable contains css and html content, escape not required*}
{hook h="displayMarketplaceSetting"}
<script>
    $('.marketplace-setting').each(function(){
        $('#kb_mp_seller_config_form .form-wrapper').append($(this).find('.form-wrapper').html());
        $(this).html('');
    });
    var add = "{l s='Add tag' mod='kbmarketplace'}";
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
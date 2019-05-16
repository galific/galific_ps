
<div style="min-height: 350px;min-width: 600px;">
    <table class="notification_table">
        <tr>
            <td>
                {l s='Title' mod='kbmobileapp'}
            </td>
            <td>
                {$title|escape:'html':'UTF-8'}
            </td>
        </tr>
        <tr class="alternate_notification_tr">
            <td>
                {l s='Message' mod='kbmobileapp'}
            </td>
            <td>
                {$message|escape:'html':'UTF-8'}
            </td>
        </tr>
        <tr>
            <td>
                {l s='Redirect Activity' mod='kbmobileapp'}
            </td>
            <td>
                {$redirect_activity|escape:'html':'UTF-8'}
            </td>
        </tr>
        <tr class="alternate_notification_tr">
            <td>
                {l s='Image Type' mod='kbmobileapp'}
            </td>
            <td>
                {$image_type|escape:'html':'UTF-8'}
            </td>
        </tr>
        <tr>
            <td>
                {l s='Preview' mod='kbmobileapp'}
            </td>
            <td>
                <img class='notification_image_preview' src='{$image_url|escape:'html':'UTF-8'}' />
            </td>
        </tr>
        <tr class="alternate_notification_tr">
            <td>
                {l s='Category' mod='kbmobileapp'}
            </td>
            <td>
                {$category_name|escape:'html':'UTF-8'}
            </td>
        </tr>
        <tr>
            <td>
                {l s='Product' mod='kbmobileapp'}
            </td>
            <td>
                {$product_name|escape:'html':'UTF-8'}
            </td>
        </tr>
        <tr class="alternate_notification_tr">
            <td>
                {l s='Sent Date' mod='kbmobileapp'}
            </td>
            <td>
                {$date_add|escape:'html':'UTF-8'}
            </td>
        </tr>
        
    </table>
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
* @copyright 2018 knowband
* @license   see file: LICENSE.txt
*}
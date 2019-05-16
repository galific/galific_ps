{if isset($error_validate)}
    <script>
    var empty_field = "{l s='Field can not be Empty.' mod='kbmarketplace'}";
    </script>
{elseif isset($shipping_method_info)}
    <div  class="alert alert-info">
        <p>{l s='Add the method which you want to allow Sellers to Select. If you don\'t want to use this feature and wants that Seller can define own custom method, then Go to Settings ->  Allow Seller to Define Own Custom Shipping Method' mod='kbmarketplace'}</p>
    </div>
{else}

<div id="marketplace-extra-content" class="bootstrap">
    {$extra_content}{*Variable contains css and html content, escape not required*}
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
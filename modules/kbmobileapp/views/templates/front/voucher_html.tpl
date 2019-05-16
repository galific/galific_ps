<p><b>{$voucher_text|escape:'quotes':'UTF-8'}</b></p><div id="display_cart_vouchers">{foreach from=$available_cart_rules item=voucher}{if $voucher['code'] != ''}<span class="voucher_name">{$voucher['code']|escape:'quotes':'UTF-8'}</span> - {$voucher['name']|escape:'quotes':'UTF-8'} <br />{/if}{/foreach}</div>
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
* @copyright 2015 knowband
* @license   see file: LICENSE.txt
*}
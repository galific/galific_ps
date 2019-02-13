{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author PrestaShop SA <contact@prestashop.com>
* @copyright  2007-2018 PrestaShop SA
* @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
* International Registered Trademark & Property of PrestaShop SA
*}

    </div>
        <div class="form-box">
            <form method="post" action="{$form_url|escape:'htmlall':'UTF-8'|replace:'&amp;':'&'}">
            <input type ="hidden" name="customtoken" id="customtoken" value="{$customtoken|escape:'htmlall':'UTF-8'|stripslashes}">
            <input type ="hidden" name="langvalue" id="langvalue" value="{$langvalue|escape:'htmlall':'UTF-8'|stripslashes}">
            <input type ="hidden" name="id_shop_group" id="id_shop_group" value="{$id_shop_group|escape:'htmlall':'UTF-8'|stripslashes}">
            <input type ="hidden" name="id_shop" id="id_shop" value="{$id_shop|escape:'htmlall':'UTF-8'|stripslashes}">
            <input type ="hidden" name="defaultnlmsg" id="defaultnlmsg" value="{l s='You need to set up PrestaShop Newsletter module on your Prestashop back office before using it' mod='sendinblue'}">
            <input type ="hidden" name="iso_code" id="iso_code" value="{$iso_code|escape:'htmlall':'UTF-8'|stripslashes}"><div class="form-box">
            <h2 class="heading">
            <img src="{$img_source|escape:'htmlall':'UTF-8'|stripslashes}logo_sib.png" />{l s='Activation' mod='sendinblue'}</h2>
            <div class="form-box-content">
            <div class="row"><label>{l s='Activate the SendinBlue plugin?' mod='sendinblue'}</label><div style="padding-top:5px">
            <label class="differ-radio-btn"><input type="radio" id="y" class="keyyes"
            name="status" value="1"
            {if !empty($key_status)}checked="checked"{/if} /><span>{l s='Yes' mod='sendinblue'}
            </span></label><label class="differ-radio-btn"><input type="radio"  id="n" class="keyyes differ-radio-btn"
            name="status" value="0"
            {if empty($key_status)}checked="checked"{/if} /><span>{l s='No' mod='sendinblue'}
            </span></label></div></div><div class="clear"></div>
            <div class="row"><div id="apikeybox" {$str|escape:'htmlall':'UTF-8'|stripslashes}><label class="key">{l s='API key' mod='sendinblue'}</label>
            <div class="key">
            <input type="text" name="apikey" id="apikeys" value="{$api_key|escape:'htmlall':'UTF-8'|stripslashes}" />&nbsp;
            <span class="toolTip"
            title="{l s='Please enter your API key from your SendinBlue account and if you don\'t have it yet, please go to www.sendinblue.com and subscribe. You can then get the API key from https://my.sendinblue.com/integration' mod='sendinblue'}">
            &nbsp;</span>
            </div>
            </div>
            </div>
            <div class="row>"<div class="clear pspace">
            <label>&nbsp;</label><input type="submit" name="submitUpdate" value="{l s='Update' mod='sendinblue'}" class="blue-btn" />&nbsp;
            </div><div class="clear"></div>
            </div>
            </div>
            </form>
			</div>
		</div>
	</div>


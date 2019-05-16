{**
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 * International Registered Trademark & Property of PrestaShop SA
 *}
<div class="jmango-prestashop-qs">
    {include file="./quickstart/header_pretashop_plugin.tpl" }
    <div>
        <div id="pres_qs_app_config" style="display: none">
            {include file="./quickstart/pres_qs_app_config.tpl" }
        </div>
    </div>

    <div id="pres_qs_create_account" style="display: none">
        {include file="./quickstart/pres_qs_create_account.tpl" }
    </div>

    <div id="pres_qs_business_questions" style="display: none">
        {include file="./quickstart/pres_qs_business_question.tpl" }
    </div>

    <div id="pres_qs_import_data" style="display: none">
        {include file="./quickstart/pres_qs_import_data.tpl" }
    </div>

    <div id="pres_qs_preview_app" style="display: none">
        {include file="./quickstart/pres_qs_preview_app.tpl" }
    </div>
    <div id="pres_qs_hurray" style="display: none">
        {include file="./quickstart/pres_qs_hurray.tpl" }
    </div>
    <div id="pres_qs_login" style="display: none">
        {include file="./quickstart/pres_qs_login.tpl" }
    </div>
    <div id="pres_qs_login2" style="display: none">
        {include file="./quickstart/pres_qs_login2.tpl" }
    </div>
    <div id="pres_qs_forgot_password" style="display: none">
        <div class="forgot_password_dialog">
            {include file="./quickstart/pres_qs_forgot_password.tpl" }
        </div>
    </div>
    <div id="pres_qs_logout" style="display: none">
        {include file="./quickstart/pres_qs_logout.tpl" }
    </div>
    <script type="text/javascript">
        var config = new Config({$data}, {$default_lang}, {$current_lang});
    </script>

</div>
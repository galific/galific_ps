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
<div class="pretashop-qs-view">
    <div class="wrap-box-shadow">
        <div class="steps-block">
            <div class="pres-container">
                <div class="help">
                    <a href="https://support.jmango360.com/portal/home" id="txt_import_data_need_help" target="_blank" class="btn-help">Need help?</a>
                </div>
                <div class="step-breadcrumbs">
                    <ul class="breadcrumbs">
                        <li class="active"><span class="text" id="txt_import_data_create_account">Create account</span><span class="number">1</span></li>
                        <li class="active"><span class="text" id="txt_import_data_business_info">Business info</span><span class="number">2</span></li>
                        <li class="current"><span class="text" id="txt_import_data_integrate">Integration</span><span class="number">3</span></li>
                        <li><span class="text" id="txt_import_data_preview_app">Preview app</span><span class="number">4</span></li>
                    </ul>
                </div>
            </div>
        </div>


        <div id="pretashop-qs-import" class="pretashop-qs-import pretashop-qs-content-step">
            <div class="pres-container">

                <div class="title-block">
                    <h3 class="title" id="txt_import_data_right_now">3. Right now we are performing magic!</h3>
                    <div class="desc" id="txt_import_data_import_all">We are importing all relevant data into the app. This process might take several minutes, please wait...</div>
                </div>


                <div class="pretashop-qs-import-content">
                    <div class="row">
                        <div class="col-md-3 col-sm-2 hidden-xs"></div>
                        <div class="col-md-6 col-sm-8 col-xs-12">
                            <div class="fields">
                                <div class="item">
                                    <div class="field-label" id="txt_import_data_shop_config">Import Shop Configuration</div>
                                    <div class="field-status status-wait"><span id="import-config-status" class="import-config-status import-config-status-0">0%</span><i id="finish-config-status" class="fa fa-check" style="display: none"></i></div>
                                </div>
                                <div class="item">
                                    <div class="field-label" id="txt_import_data_cms_import">Import CMS Pages</div>
                                    <div class="field-status status-wait"><span id="import-CMS-status" class="import-CMS-status import-CMS-status-0">0%</span><i id="finish-CMS-status" class="fa fa-check" style="display: none"></i></div>
                                </div>
                                <div class="item">
                                    <div class="field-label" id="txt_import_data_product_import">Import Product Data</div>
                                    <div class="field-status status-wait"><span id="import-product-status" class="import-product-status import-product-status-0">0%</span><i id="finish-product-status" class="fa fa-check" style="display: none"></i></div>
                                </div>
                                <div id="error_msg_import_data" class="error_intergrate_shop_app">We can't integrate with your webshop, please validate if the url and webservice key are correct</div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-2 hidden-xs"></div>

                    </div>

                    <div id="import-success-message" style="display: none" class="msg-infor">We have succesfully synchronized your website with your mobile app. Please click next to preview your app</div>



                </div>


                <div class="btn-group-actions">
                    <a id="btn-back-import-data" class="btn-action btn-back disabled"><i class="fa fa-angle-left"></i>Back</a>
                    <a id="btn-next-import-data" class="btn-action btn-next disabled">Next<i class="fa fa-angle-right"></i></a>
                </div>

            </div>

        </div>


    </div>
</div>







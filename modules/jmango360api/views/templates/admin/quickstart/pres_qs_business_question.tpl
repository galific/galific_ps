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
                    <a href="https://support.jmango360.com/portal/home" target="_blank" class="btn-help" id="anchor_business_question_need_help">Need help?</a>
                </div>
                <div class="step-breadcrumbs">
                    <ul class="breadcrumbs">
                        <li class="active"><span class="text" id="txt_business_question_create_account">Create account</span><span class="number">1</span></li>
                        <li class="current"><span class="text" id="txt_business_question_business_info">Business info</span><span class="number">2</span></li>
                        <li><span class="text" id="txt_business_question_integration">Integration</span><span class="number">3</span></li>
                        <li><span class="text" id="txt_business_question_preview_app">Preview app</span><span class="number">4</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="pretashop-qs-business-question pretashop-qs-content-step">
            <div class="business-question-wrap">
                <div class="pres-container">
                    <div class="title-block">
                        <div class="title" id="txt_h3_business_question_title">2. We’d love to hear a bit more about your business</div>
                        <div class="desc" id="txt_business_question_description">This will help us to give you the best recommendations</div>
                    </div>
                    <form class="business-question">
                        <div class="form-fields">
                            <div class="row">
                                <div class="col-md-3 col-sm-2 hidden-xs"></div>
                                <div class="col-md-6 col-sm-8 col-xs-12">
                                    <div class="form-field">
                                        <div id="error-msg-market-segment" class="error-business-info">Please choose your market segment</div>
                                        <select id="market-segment" class="business-question question-1">
                                            <option value="" class="bs-title-option" class="options_business_question_option_0">Select your market segment</option>
                                            <option value="Animals and Pets" class="options_business_question_option_1">Animals and Pets</option>
                                            <option value="Art and Culture" class="options_business_question_option_2">Art and Culture</option>
                                            <option value="Babies" class="options_business_question_option_3">Babies</option>
                                            <option value="Beauty and Personal Care" class="options_business_question_option_4">Beauty and Personal Care</option>
                                            <option value="Cars" class="options_business_question_option_5">Cars</option>
                                            <option value="Computer Hardware and Software" class="options_business_question_option_6">Computer Hardware and Software</option>
                                            <option value="Download" class="options_business_question_option_7">Download</option>
                                            <option value="Fashion and accessories" class="options_business_question_option_8">Fashion and accessories</option>
                                            <option value="Flowers, Gifts and Crafts" class="options_business_question_option_9">Flowers, Gifts and Crafts</option>
                                            <option value="Food and beverage" class="options_business_question_option_10">Food and beverage</option>
                                            <option value="HiFi, Photo and Video" class="options_business_question_option_11">HiFi, Photo and Video</option>
                                            <option value="Home and Garden" class="options_business_question_option_12">Home and Garden</option>
                                            <option value="Home Appliances" class="options_business_question_option_13">Home Appliances</option>
                                            <option value="Jewelry" class="options_business_question_option_14">Jewelry</option>
                                            <option value="Lingerie and Adult" class="options_business_question_option_15">Lingerie and Adult</option>
                                            <option value="Mobile and Telecom" class="options_business_question_option_16">Mobile and Telecom</option>
                                            <option value="Services" class="options_business_question_option_17">Services</option>
                                            <option value="Shoes and accessories" class="options_business_question_option_18">Shoes and accessories</option>
                                            <option value="Sport and Entertainment" class="options_business_question_option_19">Sport and Entertainment</option>
                                            <option value="Travel" class="options_business_question_option_20">Travel</option>
                                        </select>
                                        <div class="sucess-msg"><i class="fa fa-check"></i></div>

                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-2 hidden-xs"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-2 hidden-xs"></div>
                                <div class="col-md-6 col-sm-8 col-xs-12">
                                    <div class="form-field">
                                        <div id="error-msg-mobile-traffic" class="error-business-info">Please choose your number of monthly mobile visitor</div>
                                        <select id="mobile-traffic" class="business-question question-2">
                                            <option value="" class="bs-title-option" id="options_business_question_mobile_traffic">Percentage of mobile traffic</option>
                                            <option value="0 - 25">0 - 25</option>
                                            <option value="25 - 50">25 - 50</option>
                                            <option value="> 50">> 50</option>
                                        </select>
                                        <div class="sucess-msg"><i class="fa fa-check"></i></div>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-2 hidden-xs"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-3 col-sm-2 hidden-xs"></div>
                                <div class="col-md-6 col-sm-8 col-xs-12">
                                    <div class="form-field">
                                        <div id="error-msg-annual-revenue" class="error-business-info">Please choose your annual revenue</div>
                                        <select id="annual-revenue" class="business-question question-3">
                                            <option value="" class="bs-title-option" id="options_business_question_annual_revenue">Annual revenue range</option>
                                            <option value="0 - 250,000">0 - 250,000</option>
                                            <option value="250,000 - 500,000">250,000 - 500,000</option>
                                            <option value="500,000 - 750,000">500,000 - 750,000</option>
                                            <option value="750,000 - 1,500,000">750,000 - 1,500,000</option>
                                            <option value="> 1,500,000">> 1,500,000</option>
                                            <option value="That's my business" class="options_business_question_my_business">That's my business</option>
                                        </select>
                                        <div class="sucess-msg"><i class="fa fa-check"></i></div>

                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-2 hidden-xs"></div>
                            </div>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-md-3 col-sm-2 hidden-xs"></div>
                        <div class="col-md-6 col-sm-8 col-xs-12">
                            <div id="error_msg_submit_business_info" class="error_intergrate_shop_app">We can't integrate with your webshop, please validate if the url and webservice key are correct</div>
                        </div>
                        <div class="col-md-3 col-sm-2 hidden-xs"></div>
                    </div>


                    <div class="btn-group-actions">
                        <a id="ps-question-btn-back" class="btn-action btn-back"><i class="fa fa-angle-left"></i>Back</a>
                        <a id="ps-question-submit" class="btn-action btn-next">Next<i class="fa fa-angle-right"></i></a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

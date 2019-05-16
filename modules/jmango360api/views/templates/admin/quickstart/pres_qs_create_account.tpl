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
                    <a href="https://support.jmango360.com/portal/home" target="_blank" id="txt_create_account_need_help" class="btn-help">Need help?</a>
                </div>
                <div class="step-breadcrumbs">
                    <ul class="breadcrumbs">
                        <li class="current"><span class="text" id="txt_create_account_create_acccount">Create account</span><span class="number">1</span></li>
                        <li><span class="text" id="txt_create_account_business_info">Business info</span><span class="number">2</span></li>
                        <li><span class="text" id="txt_create_account_integration">Integration</span><span class="number">3</span></li>
                        <li><span class="text" id="txt_create_account_preview_app">Preview app</span><span class="number">4</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="pretashop-qs-create-acc pretashop-qs-content-step">

            <div class="create-acc-form-wrap">
                <div class="pres-container">
                    <div class="title-block">
                        <h3 class="title" id="txt_create_account_header_create_account">1. Create your JMango360 account</h3>
                        <div class="desc" id="txt_create_account_msg_verify">Please verify all required fields below to setup the account for your mobile app.</div>
                    </div>
                    <form class="create-acc-form">
                        <div class="form-fields">
                            <div class="row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-field">
                                        <div class="form-label" ><span id="txt_create_account_first_name">First Name</span>*</div>
                                        <div class="form-value">
                                            <span id="error-msg-first-name" class="error-msg">Please enter a valid Email Address</span>
                                            <input id="first-name" type="text" class="txt_text" placeholder="John" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-field">
                                        <div class="form-label"><span id="txt_create_account_last_name">Last Name</span>*</div>
                                        <div class="form-value">
                                            <span id="error-msg-last-name" class="error-msg">Please enter a valid Email Address</span>
                                            <input id="last-name" type="text" class="txt_text" placeholder="Smith" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-field">
                                        <div class="form-label"><span id="txt_create_account_email_address">Email Address</span>*</div>
                                        <div class="form-value">
                                            <span id="error-msg-email" class="error-msg">Please enter a valid Email Address</span>
                                            <span id="error_msg_email_exist" class="error-msg">Please enter a valid Email Address</span>
                                            <input id="email-address" type="email" class="txt_text" placeholder="example@email.com" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-field">
                                        <div class="form-label" ><span id="txt_create_account_phone_number">Phone</span>*</div>
                                        <div class="form-value">
                                            <span id="error-msg-phone" class="error-msg">Please enter a valid Email Address</span>
                                            <input id="phone" type="text" class="txt_text" placeholder="+314677894" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-field">
                                        <div class="form-label"><span id="txt_create_account_password">Password</span>*</div>
                                        <div class="form-value">
                                            <span id="error-msg-password" class="error-msg">Please enter a valid Email Address</span>
                                            <input id="create-account-password" type="password" class="txt_text" placeholder="Examplepassword" />
                                            <span class="showPassword">
												<svg class="eyeClose" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="#727173" d="M301.952005,7 L301,7.96148736 L319.850756,27 L320.802761,26.0385129 L316.74622,21.9415671 C319.503691,20.0559126 320.864789,17.4192798 320.882971,17.3837981 C320.959351,17.2706831 321.00014,17.1369148 321,17.0000001 C320.999898,16.8466336 320.948461,16.6978062 320.854043,16.5776892 C320.783897,16.4636444 316.905546,10.2005312 310.90138,10.2005312 C309.197984,10.2005312 307.669372,10.7106221 306.346482,11.438247 L301.952005,7 Z M310.617051,12 C313.037691,12 315,14.2426386 315,17.0090843 C315,18.1339501 314.668175,19.1650286 314.119497,20 L312.308351,17.9301183 C312.426367,17.6515488 312.495458,17.3402893 312.495458,17.0090843 C312.495458,15.8233625 311.654558,14.8623339 310.617051,14.8623339 C310.327247,14.8623339 310.054895,14.941295 309.811147,15.0761703 L308,13.0062893 C308.7306,12.3792281 309.632794,12 310.617051,12 Z M304.215122,13 C302.285198,14.7269393 301.178563,16.5996024 301.142049,16.6617195 C301.050254,16.7839353 301.000172,16.9356771 301,17.0921133 C301.000115,17.2257803 301.036683,17.3565409 301.105268,17.4685393 C301.106109,17.4698915 301.106955,17.4712407 301.107805,17.4725866 C301.11897,17.4970958 304.259058,24 310.740488,24 C311.941815,24 313.026601,23.7744464 314,23.4104008 L312.319513,21.6213666 C311.826644,21.816169 311.296345,21.927634 310.740488,21.927634 C308.23004,21.927634 306.194927,19.7627023 306.194927,17.0921133 C306.194927,16.5007982 306.299708,15.9366719 306.482829,15.4123633 L304.215122,13 Z" transform="translate(-301 -7)"/></svg>
												<svg class="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="15" viewBox="0 0 20 15"><path fill="#727173" d="M341,10 C333.727273,10 331,17.2727273 331,17.2727273 C331,17.2727273 333.727273,24.5454545 341,24.5454545 C348.272727,24.5454545 351,17.2727273 351,17.2727273 C351,17.2727273 348.272727,10 341,10 Z M341,12.7272727 C343.51,12.7272727 345.545455,14.7627273 345.545455,17.2727273 C345.545455,19.7827273 343.51,21.8181818 341,21.8181818 C338.49,21.8181818 336.454545,19.7827273 336.454545,17.2727273 C336.454545,14.7627273 338.49,12.7272727 341,12.7272727 Z M341,14.5454545 C339.493769,14.5454545 338.272727,15.7664961 338.272727,17.2727273 C338.272727,18.7789584 339.493769,20 341,20 C342.506231,20 343.727273,18.7789584 343.727273,17.2727273 C343.727273,15.7664961 342.506231,14.5454545 341,14.5454545 Z" transform="translate(-331 -10)"/></svg>
											</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-sm-12 col-xs-12">
                                    <div class="form-field">
                                        <div class="form-label"><span id="txt_create_account_confirm_password">Confirm Password</span>*</div>
                                        <div class="form-value">
                                            <span id="error-msg-confirm-password" class="error-msg">Please enter a valid Email Address</span>
                                            <input id="confirm-password" type="password" class="txt_text" placeholder="Examplepassword" />
                                            <span class="showPassword">
												<svg class="eyeClose" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"><path fill="#727173" d="M301.952005,7 L301,7.96148736 L319.850756,27 L320.802761,26.0385129 L316.74622,21.9415671 C319.503691,20.0559126 320.864789,17.4192798 320.882971,17.3837981 C320.959351,17.2706831 321.00014,17.1369148 321,17.0000001 C320.999898,16.8466336 320.948461,16.6978062 320.854043,16.5776892 C320.783897,16.4636444 316.905546,10.2005312 310.90138,10.2005312 C309.197984,10.2005312 307.669372,10.7106221 306.346482,11.438247 L301.952005,7 Z M310.617051,12 C313.037691,12 315,14.2426386 315,17.0090843 C315,18.1339501 314.668175,19.1650286 314.119497,20 L312.308351,17.9301183 C312.426367,17.6515488 312.495458,17.3402893 312.495458,17.0090843 C312.495458,15.8233625 311.654558,14.8623339 310.617051,14.8623339 C310.327247,14.8623339 310.054895,14.941295 309.811147,15.0761703 L308,13.0062893 C308.7306,12.3792281 309.632794,12 310.617051,12 Z M304.215122,13 C302.285198,14.7269393 301.178563,16.5996024 301.142049,16.6617195 C301.050254,16.7839353 301.000172,16.9356771 301,17.0921133 C301.000115,17.2257803 301.036683,17.3565409 301.105268,17.4685393 C301.106109,17.4698915 301.106955,17.4712407 301.107805,17.4725866 C301.11897,17.4970958 304.259058,24 310.740488,24 C311.941815,24 313.026601,23.7744464 314,23.4104008 L312.319513,21.6213666 C311.826644,21.816169 311.296345,21.927634 310.740488,21.927634 C308.23004,21.927634 306.194927,19.7627023 306.194927,17.0921133 C306.194927,16.5007982 306.299708,15.9366719 306.482829,15.4123633 L304.215122,13 Z" transform="translate(-301 -7)"/></svg>
												<svg class="eyeOpen" xmlns="http://www.w3.org/2000/svg" width="20" height="15" viewBox="0 0 20 15"><path fill="#727173" d="M341,10 C333.727273,10 331,17.2727273 331,17.2727273 C331,17.2727273 333.727273,24.5454545 341,24.5454545 C348.272727,24.5454545 351,17.2727273 351,17.2727273 C351,17.2727273 348.272727,10 341,10 Z M341,12.7272727 C343.51,12.7272727 345.545455,14.7627273 345.545455,17.2727273 C345.545455,19.7827273 343.51,21.8181818 341,21.8181818 C338.49,21.8181818 336.454545,19.7827273 336.454545,17.2727273 C336.454545,14.7627273 338.49,12.7272727 341,12.7272727 Z M341,14.5454545 C339.493769,14.5454545 338.272727,15.7664961 338.272727,17.2727273 C338.272727,18.7789584 339.493769,20 341,20 C342.506231,20 343.727273,18.7789584 343.727273,17.2727273 C343.727273,15.7664961 342.506231,14.5454545 341,14.5454545 Z" transform="translate(-331 -10)"/></svg>
											</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="error_msg_create_account" class="error_intergrate_shop_app">We can't integrate with your webshop, please validate if the url and webservice key are correct</div>
                            <div class="form-field term-condition-field">
                                <div class="form-value">
                                    <input id="term-condition" type="checkbox" class="checkbox" />
                                    <label for="term-condition"  id="txt_create_account_i_agree">I agree with the
                                        <a target="_blank" href="https://jmango360.com/terms-conditions/" id="txt_create_account_terms_condition">
                                            Terms & Conditions
                                        </a> and the
                                        <a href="https://jmango360.com/privacy-policy/" target="_blank" id="txt_create_account_privacy_policy">
                                            Privacy Policy
                                        </a>
                                    </label>
                                    <span id="error-msg-term-condition" class="error-msg">Please enter a valid Email Address</span>
                                </div>
                            </div>

                        </div>
                        <div class="login-link">
                            <a id="ps-login2" href="" class="login-url">Already have an account? Login here</a>


                        </div>

                    </form>

                    <div class="btn-group-actions">
                        <a id="ps-create-account-btn-back" class="btn-action btn-back"><i class="fa fa-angle-left" id="txt_create_account_button_back"></i>Back</a>
                        <a id="ps-create-account-btn-next" class="btn-action btn-next">Next<i class="fa fa-angle-right" id="txt_create_account_button_next"></i></a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
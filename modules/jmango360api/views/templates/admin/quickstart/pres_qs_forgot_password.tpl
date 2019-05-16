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

<div class="pres-qs-forgot-password">
    <div class="login-box-wrap">
        <div class="login-box">
            <div class="title-block">
                <h3 class="title" id="txt_forgot_password_question">Forgot Your Password?</h3>
                <div class="desc" id="txt_forgot_password_enter_details">Please enter your details below.</div>
            </div>
            <form id="forgot-password-form" class="login-form">
                <div class="fields">
                    <div id="forgot-password-error-message" class="form-field error-field">The email address does not exist in our system</div>
                    <div class="form-field">
                        <div class="label-field" id="txt_forgot_password_email_address">Email Address</div>
                        <div class="input-field">
                            <input type="email" class="txt-input" id="ps-forgot-pass-email" placeholder="example@mail.com">
                        </div>
                    </div>

                    <div class="form-field submit-field">
                        <a href="#" class="btnCancel" id="txt_forgot_password_back_login">Back to login screen</a>
                        <button type="submit" id="forgot-pass-submit" class="btnSubmit"><span class="text" id="txt_forgot_password_submit_btn">Submit</span></button>
                    </div>
                </div>
            </form>
        </div>

        <div class="resetEmailSuccess">
            <div class="msgIcon">
                <svg xmlns="http://www.w3.org/2000/svg" width="74" height="76" viewBox="0 0 74 76">
                    <path fill="#62CB31" d="M37,0 C16.6099774,0 0,17.0588957 0,38 C0,58.9411043 16.6099774,76 37,76 C57.3900226,76 74,58.9411043 74,38 C74,17.0588957 57.3900226,0 37,0 Z M37,3.95010395 C55.342932,3.95010395 70.1538462,19.1613131 70.1538462,38 C70.1538462,56.8386869 55.342932,72.049896 37,72.049896 C18.657068,72.049896 3.84615385,56.8386869 3.84615385,38 C3.84615385,19.1613131 18.657068,3.95010395 37,3.95010395 Z M49.7140337,26.7027027 L32.8689556,43.721216 L25.2498545,36.0419089 L22,39.3252236 L32.8689556,50.3243243 L53,29.9860174 L49.7140337,26.7027027 Z"/>
                </svg>
            </div>
            <div class="msgDetail">
                <div class="title-block">
                    <div class="title" id="txt_forgot_password_msg_got_mail">You’ve got mail</div>
                    <div class="desc" id="txt_forgot_password_msg_new_pass">We send you an email with new password.</div>
                </div>
                <div class=""><a href="#" class="btnCancel" id="txt_forgot_password_msg_back_login">Back to login screen</a></div>
            </div>
            <div style="clear:both;"></div>
        </div>


    </div>
</div>
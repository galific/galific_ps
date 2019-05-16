/**
 * @license
 */
PsLogin = {
    init:function(id, isPasswordChanged){
        var isEmptyField = false;
        $('#login-error-message').hide();
        var onGetDataSuccess = function(result) {
            console.log("data = "+JSON.stringify(result));
            if(result.hashNext === '#pres_qs_hurray'){
                $('#pres_qs_login').css("display","none");
                $('#pres_qs_hurray').css("display","block");
                HurrayPage.init(id);
            }
            if(result.hashNext === '#pres_qs_app_config'){
                $('#pres_qs_login').css("display","none");
                AppConfig.init("#pres_qs_app_config");
                AppConfig.populateData(id, Session.getValue(Const.SESSION_TICKET));
                $('#pres_qs_app_config').css("display","block");
            }
            if(result.hashNext === '#pres_qs_business_info'){
                $('#pres_qs_login').css("display","none");
                $('#pres_qs_business_questions').css("display","block");
                BusinessQuestions.init(id);
            }
        };
        var onGetDataError = function(result) {

        };

        var onLoginError = function(result) {
        };

        var onLoginSuccess = function(result) {
            if(result.ok === true) {
                if(result.ticket !== null){
                    Session.setValue(Const.SESSION_TICKET, result.ticket);
                }
                PsDataService.getData(id, result.ticket, currentHash, onGetDataSuccess, onGetDataError);
            }
            else {
                // $('#login-error-message').html("Password or email is invalid, please try again or click on \"Forgot password\"");
                $('#login-error-message').show();
                $('#pretashop-qs-login .login-form button.btnSubmit').removeClass('loading');
            }
        };

        $('#showPassword').off('click').on('click', function () {
            var inputPassword = $(this).closest('.input-field').find('input#ps-login-password');
            if ($(inputPassword).attr("type") == "password") {
                $(inputPassword).attr("type", "text");
                $(this).addClass('showed');
            } else {
                $(inputPassword).attr("type", "password");
                $(this).removeClass('showed');
            }
        });

        function validateEmptyField(){

            var userName = $('#ps-login-username'),
                userPass = $('#ps-login-password');

            if ((userName.val() && userPass.val()) == ''){
                if (userName.val() == ''){
                    userName.addClass('invalid');
                } else {
                    userName.removeClass('invalid');
                }

                if (userPass.val() == ''){
                    userPass.addClass('invalid');
                } else {
                    userPass.removeClass('invalid');
                }
                // $('#login-error-message').html('Password or email is invalid, please try again or click on "Forgot password"');
                $('#login-error-message').show();
                isEmptyField = true;
            } else {
                userName.removeClass('invalid');
                userPass.removeClass('invalid');
                isEmptyField = false;
                // $('#login-error-message').html('');
                $('#login-error-message').hide();
            }
        }


        var currentHash = "#pres_qs_login";

        function log(){console.log(isPasswordChanged === true);}
        log();
        if(isPasswordChanged === true){
            $('#ps-change-password').css("display","block");
        }

        $('#login-form #pres-qs-btnLoginSubmit').off('click').on('click', function(e) {
            e.preventDefault();
            validateEmptyField();

            if (isEmptyField){
                $('#login-form input.invalid').first().focus();
                $('#login-error-message').show();
            } else {
                $('#login-form').validationEngine();
                if (!$("#login-form").validationEngine('validate')) {
                    return false;
                }

                $('#pretashop-qs-login .login-form button.btnSubmit').addClass('loading');

                var username = $('#login-form #ps-login-username').val();
                var password = StringUtils.encrypt($('#login-form #ps-login-password').val());
                PsLogin1Service.login1(id, username, password, currentHash, onLoginSuccess, onLoginError);
                return false;
            }
        });

        $('a.forget-pwd').off('click').on('click',function (e) {
            e.preventDefault();
            PsForgotPassword.init(e);
            $('#pres_qs_forgot_password').css("display","block");
        });

    }
};
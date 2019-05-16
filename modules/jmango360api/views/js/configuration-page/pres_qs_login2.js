/**
 * @license
 */
PsLogin2 = {
    init:function(id, isPasswordChanged){
        var isEmptyField = false;
        $('#login2-error-message').hide();
        var onGetDataSuccess = function(result) {
            if(result.hashNext === '#pres_qs_hurray'){
                $('#pres_qs_login2').css("display","none");
                $('#pres_qs_hurray').css("display","block");
                HurrayPage.init(id);
            }
            if(result.hashNext === '#pres_qs_business_info'){
                $('#pres_qs_login2').css("display","none");
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
                // $('#login2-error-message').html("Password or email is invalid, please try again or click on \"Forgot password\"");
                $('#login2-error-message').show();
                $('#pretashop-qs-login .login-form2 button.btnSubmit').removeClass('loading');
            }        };

        $('#showPassword-login2').off('click').on('click', function () {
            var inputPassword = $(this).closest('.input-field').find('input#ps-login2-password');
            if ($(inputPassword).attr("type") == "password") {
                $(inputPassword).attr("type", "text");
                $(this).addClass('showed');
            } else {
                $(inputPassword).attr("type", "password");
                $(this).removeClass('showed');
            }
        });

        function validateEmptyField(){

            var userName = $('#ps-login2-username'),
                userPass = $('#ps-login2-password');

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
                // $('#login2-error-message').html('Password or email is invalid, please try again or click on "Forgot password"');
                $('#login2-error-message').show();
                isEmptyField = true;
            } else {
                userName.removeClass('invalid');
                userPass.removeClass('invalid');
                isEmptyField = false;
                // $('#login2-error-message').html('');
                $('#login2-error-message').hide();
            }
        }


        var currentHash = "#pres_qs_login2";

        function log(){console.log(isPasswordChanged === 'true');}
        log();
        if(isPasswordChanged === 'true'){
            $('#ps-change-password').css("display","block");
        }

        $('#login-form2 #pres-qs-btnLogin2Submit').off('click').on('click', function(e) {
            e.preventDefault();
            validateEmptyField();

            if (isEmptyField){
                $('#login-form2 input.invalid').first().focus();
                $('#login-error-message').show();
            } else {
                $('#login-form2').validationEngine();
                if (!$("#login-form2").validationEngine('validate')) {
                    return false;
                }

                $('#pretashop-qs-login .login-form2 button.btnSubmit').addClass('loading');

                var username = $('#login-form2 #ps-login2-username').val();
                var password = StringUtils.encrypt($('#login-form2 #ps-login2-password').val());
                PsLogin1Service.login1(id, username, password, currentHash, onLoginSuccess, onLoginError);
                return false;
            }
        });

        $('a.forget-pwd').off('click').on('click',function (e) {
            e.preventDefault();
            PsForgotPassword.init(e);
            $('#pres_qs_forgot_password').css("display","block");
        });
        $('#login2-btn-back').off('click').on('click', function (e) {
            e.preventDefault();
            $('#pres_qs_login2').css("display","none");
            $('#pres_qs_create_account').css("display","block");
        })
    }
};
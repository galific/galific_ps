/**
 * @license
 */
PsForgotPassword = {
    init: function(e) {

        $('.pres-qs-forgot-password').parents('.dialog-content').find('.icon-dialog-close').remove();

        var onSuccess = function(result) {

            if (result.KR1 == true){
                $('.pres-qs-forgot-password .login-box').hide();
                $('.pres-qs-forgot-password .resetEmailSuccess').show();
            } else {
                var txtErrorMsg = $('#forgot-password-error-message');
                // txtErrorMsg.text('The email address does not exist in our system');
                txtErrorMsg.show();
                $('#ps-languages').trigger('change');
                $('button#forgot-pass-submit').removeClass('loading');
            }

        };

        var onError = function(){
            var txtErrorMsg = $('#forgot-password-error-message');
            // txtErrorMsg.text('The email address does not exist in our system');
            txtErrorMsg.show();
            $('button#forgot-pass-submit').removeClass('loading');
        };

        //function get server URL
        function getURLParameters() {
            var sURL = window.document.URL.toString();
            if (sURL.indexOf("#") > 0) {
                var arrParams = sURL.split("#");
                var arrURLParams = arrParams[0];
                return arrURLParams;
            }
        }

        function validateEmail(){
            var txtEmail = $('#forgot-password-form #ps-forgot-pass-email').val();
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            if ((txtEmail == '') || (!re.test(txtEmail))){
                return false;
            } else {
                return true;
            }
        }

        $('#forgot-pass-submit').off('click').on('click', function(e) {
            e.preventDefault();

            if (!validateEmail()){
                onError();
                $('#forgot-password-form #ps-forgot-pass-email').addClass('invalid');
            } else {
                $('#forgot-password-form #ps-forgot-pass-email').removeClass('invalid');
                $('#forgot-password-error-message').text('');
                $('#forgot-password-error-message').hide();

                $(this).addClass('loading');
                var user;
                var username = $('#ps-forgot-pass-email').val();
                user = {P0:{P0:username},KR0:{A0:"project.name", K0:{B0:"anonymous"}},KR1:"1.0"};
                PsUserService.forgotPassword(user, onSuccess, onError);
            }
        });

        $('.btnCancel').off('click').on('click',function (e) {
            e.preventDefault();
            $('#pres_qs_forgot_password').css("display","none");
            $('.pres-qs-forgot-password .resetEmailSuccess').hide();
            $('button#forgot-pass-submit').removeClass('loading');
            $('#forgot-password-form').find('#ps-forgot-pass-email').val('');
            $('#forgot-password-error-message').hide();
            $('.pres-qs-forgot-password .login-box').show();
        })

    }
};



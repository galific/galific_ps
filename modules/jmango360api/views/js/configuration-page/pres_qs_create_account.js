/**
 * @license
 */
var CreateAccount = {
    init : function(id){
        var currentHash = "#pres_qs_create_customer";

        $('#ps-login2').on('click', function(e){
            e.preventDefault();
            $('#pres_qs_create_account').css("display","none");
            PsLogin2.init(id);
            $('#pres_qs_login2').css("display","block");

        });

        $('.error-msg').hide();
        $('#error_msg_create_account').hide();

        function validateFormField() {
            var passValid = true;
            $('.txt_text').each(function () {
                var errorMmsgDiv = $(this).parents('.form-field').find('.error-msg');
                var errorMmsgText = $(this).parents('.form-field').find('.form-label span')
                if ($(this).val() =='' && $(this).attr("id") !== "email-address"){
                    $(errorMmsgDiv).text(errorMmsgText.text() + ' is required').show();
                    $(this).addClass('invalid');
                    passValid = false;
                } else {
                    $(errorMmsgDiv).text('').hide();
                    $(this).removeClass('invalid');
                }
            });

            var regex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

            // if ($('#email-address').val() != ''){
                var emailValidate = regex.test($('#email-address').val());

                if (!emailValidate){
                    $('#error-msg-email').text('Please enter a valid Email Address').show();
                    $('#email-address').addClass('invalid');
                    passValid = emailValidate;
                } else {
                    $('#error-msg-email').text('').hide();
                    $('#email-address').removeClass('invalid');
                }
            // }

            if ((($('.pretashop-qs-create-acc #create-account-password').val() &&  ($('.pretashop-qs-create-acc #confirm-password').val()) != ''))){
                if ($('.pretashop-qs-create-acc #create-account-password').val() ==  $('.pretashop-qs-create-acc #confirm-password').val()){
                    $('#error-msg-confirm-password').text('').hide();
                    $('#confirm-password').removeClass('invalid');
                } else {
                    $('#error-msg-confirm-password').text('Password is not the same').show();
                    $('#confirm-password').addClass('invalid');
                    passValid = false;
                }
            }
            if (!$('#term-condition').prop('checked')){
                $('#error-msg-term-condition').text('Please accept the terms & conditions\n').show();
                passValid = false;
            } else {
                $('#error-msg-term-condition').text('').hide();
            }
            $('#ps-languages').trigger('change');

            return passValid;

        }

        $('.showPassword').off('click').on('click', function () {
            var inputPassword = $(this).closest('.form-value').find('input.txt_text');
            if ($(inputPassword).attr("type") == "password") {
                $(inputPassword).attr("type", "text");
                $(this).addClass('showed');
            } else {
                $(inputPassword).attr("type", "password");
                $(this).removeClass('showed');
            }
        });

        var onCreateUserSuccess = function(result) {
            //save ticket
            if(result.ok === true){
                Session.setValue(Const.APP_CREATED, false);
                if(result.ticket !== null){
                    Session.setValue(Const.SESSION_TICKET, result.ticket);
                }
                $('#pres_qs_create_account').css("display","none");
                BusinessQuestions.init(id);
                $('#pres_qs_business_questions').css("display","block");
            } else {
                if(result.code === 6000){
                    $('#error_msg_create_account').show();
                } else {
                    $('#error_msg_email_exist').show();
                    $('#email-address').addClass('invalid');
                }
            }
        };

        var onCreatUserError = function(result) {
        };

        var createNewUser = function(id) {
            var firstName = $('#first-name').val();
            var lastName = $('#last-name').val();
            var email = $('#email-address').val();
            var phone = $('#phone').val();
            var password = StringUtils.encrypt($('#create-account-password').val());
            var data = {id:id, currentHash: currentHash, firstName:firstName, lastName:lastName, email:email, phone: phone, password: password};
            PsCreateUserService.createUser(data, onCreateUserSuccess, onCreatUserError);
        };

        var onGetDataSuccess = function(result) {
            var info = result.info;
            $('#first-name').val(info.firstName);
            $('#last-name').val(info.lastName);
            $('#email-address').val(info.email);

            $('#ps-create-account-btn-back').off('click').click(function(){
                $('#pres_qs_create_account').css("display","none");
                AppConfig.init("#pres_qs_app_config");
                $('#pres_qs_app_config').css("display","block");
            });

            $('#ps-create-account-btn-next').off('click').on('click', function (e) {
                e.preventDefault();

                validateFormField();

                console.log('validateFormField: ' + validateFormField());
                $('#error_msg_create_account').hide();
                if (validateFormField()){
                    createNewUser(id);
                }
            })
        };

        var onGetDataError = function(result) {
        };

        PsDataService.getData(id, null, currentHash, onGetDataSuccess, onGetDataError);

        // $('#btn-next').click(function(){
        //     createNewUser(id);
        // });

        // $('a.login-url').off('click').on('click', function (e) {
        //     e.preventDefault();
        //     //#pres_qs_login2
        //     // Navigation.change(View.PS_QUICK_START_LOGIN2);
        //     window.location.hash = "#pres_qs_login2?id="+id;
        // });


    }
};
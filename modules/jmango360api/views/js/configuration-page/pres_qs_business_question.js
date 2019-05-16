/**
 * @license
 */
BusinessQuestions = {
    init: function(id) {
        var currentHash = "#pres_qs_business_info";
        $('#error_msg_submit_business_info').hide();
        ticket = Session.getValue(Const.SESSION_TICKET);

        onGetDataSuccess = function(result){
            if(result.hashNext === '#pres_qs_create_app' && Session.getValue(Const.APP_CREATED) === 'false'){
                $('#pres_qs_business_questions').hide();
                $('#pres_qs_import_data').show();
                ImportData.init(id);
            }
            else if (result.hashNext === '#pres_qs_hurray' && Session.getValue(Const.APP_CREATED) === 'false'){
                $('#pres_qs_business_questions').css("display","none");
                $('#pres_qs_hurray').css("display","block");
                HurrayPage.init(id);
            } else if (Session.getValue(Const.APP_CREATED) === 'true') {
                $('#pres_qs_business_questions').css("display","none");
                $('#pres_qs_preview_app').css("display","block");
                PreviewApp.init(id);
            }
        };

        onGetDataError = function () {

        };

        var onSubmitSuccess = function (result) {
            if(result.ok === true) {
                PsDataService.getData(id, ticket, currentHash, onGetDataSuccess, onGetDataError);
            } else if(result.code === 6000){
                $('#error_msg_submit_business_info').show();
            }
        };


        var onSubmitError = function (result) {
        };


        var submitAnswer = function (id, ticket, currentHash) {
            //prepare data
            var marketSegment = $('#market-segment').val();
            var mobileTrafficPercent = $('#mobile-traffic').val();
            var revenue = $('#annual-revenue').val();
            var data = {
                id: id,
                currentHash: currentHash,
                ticket: ticket,
                marketSegment: marketSegment,
                mobileTrafficPercent: mobileTrafficPercent,
                revenue: revenue
            };
            PsBusinessQuestionService.submitAnswer(data, onSubmitSuccess, onSubmitError);
        };

        $('#ps-question-submit').off('click').on('click', function (e) {
            e.preventDefault();
            validateSelectBox();
            if (validateSelectBox()) {
                submitAnswer(id, ticket, currentHash);
            } else {
                return false;
            }
        });


        $('.business-question').selectpicker({
            style: '',
            dropupAuto: false,
        });


        $('select.business-question').each(function () {
            $(this).on('change', function () {
                $(this).parents('.form-field').find('.business-question').removeClass('invalid');
                $(this).parents('.form-field').find('.error-business-info').hide();
                $(this).parents('.form-field').find('.sucess-msg').show();
            });
        });

        function validateSelectBox() {
            var passError = true;
            $('select.business-question').each(function () {
                if ($(this).val() != '') {
                    $(this).parents('.form-field').find('.business-question').removeClass('invalid');
                    $(this).parents('.form-field').find('.sucess-msg').show();
                } else {
                    $(this).parents('.form-field').find('.business-question').addClass('invalid');
                    passError = false;
                    $(this).parents('.form-field').find('.error-business-info').show();
                    $(this).parents('.form-field').find('.sucess-msg').hide();
                }
            });
            return passError;
        }

        $('#ps-question-btn-back').off('click').on('click', function (e) {
            e.preventDefault();
            $('#pres_qs_business_questions').css("display","none");
            PsLogout.init(id, "#pres_qs_business_questions");
            $('#pres_qs_logout').css("display","block");
        });

        function onSuccess(result){
            if(result.info.marketSegment!=null) {
                $('#market-segment').val(result.info.marketSegment).change();
                $('#mobile-traffic').val(result.info.mobileTrafficPercent).change();
                $('#annual-revenue').val(result.info.revenue).change();
            }
        }

        function onError(result){}
        $('#ps-languages').trigger('change');

        PsDataService.getData(id, ticket, currentHash, onSuccess, onError);
    }
};

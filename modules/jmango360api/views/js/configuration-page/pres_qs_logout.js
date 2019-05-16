/**
 * @license
 */
PsLogout = {
    init: function(id, hashBack) {
        var currentHash = "#pres_qs_logout";
        var ticket = Session.getValue(Const.SESSION_TICKET);

        function getEmail() {
            PsDataService.getData(id, ticket, currentHash
                , function (result) {
                    result = result || {info: {}};
                    var userEmail = result.info.email;
                    $('#user_email').text(userEmail);
                }, function (result) {
                    console.log(result.KR2);
                }
            );
        }
        getEmail();


        $('#ps-qs-logout').off('click').on('click', function (e) {
            e.preventDefault();
            Session.setValue(Const.SESSION_TICKET, "");
            $('#pres_qs_logout').css("display","none");
            CreateAccount.init(id);
            $('#pres_qs_create_account').css("display","block");
        });

        $('#btn-next').off('click').on('click', function (e) {
            e.preventDefault();
            if(hashBack === "#pres_qs_business_questions"){
                $('#pres_qs_logout').css("display","none");
                $('#pres_qs_business_questions').css("display","block");
            }
            if(hashBack === "#pres_qs_import_data"){
                $('#pres_qs_logout').css("display","none");
                $('#pres_qs_import_data').css("display","block");
            }
        })
    }
};
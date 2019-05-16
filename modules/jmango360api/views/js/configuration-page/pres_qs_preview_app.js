/**
 * @license
 */
PreviewApp = {
    init: function(id){
        ticket = Session.getValue(Const.SESSION_TICKET);
        var onGetDataSuccess = function(result) {

            $('#btn-back-preview-app').click(function(){
                $('#pres_qs_preview_app').css("display","none");
                $('#pres_qs_import_data').css("display","block");
            });

            $('#btn-done-preview-app').off('click').on('click',function(e){
                e.preventDefault();
                var endpoint = Const.ORCHARD_URL + "#pres_qs_congratulations?AppRef=" + result.info.appKey + "&tickets=" + self.ticket + "&id=" + id + "&lang=" + $('#ps-languages').val();
                window.open(endpoint, '_blank');
            });
        };

        var onGetDataError = function(result) {

        };

        PsUserService.getNewTicket(id, ticket, currentHash, function(result){
            self.ticket = result.ticket;
        }, function (result){});
        var currentHash = "#pres_qs_preview_app";

        PsDataService.getData(id, ticket, currentHash, onGetDataSuccess, onGetDataError);
    }
};

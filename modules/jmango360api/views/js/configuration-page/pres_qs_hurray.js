/**
 * @license
 */
HurrayPage =  {
    init: function(id) {
        var currentHash = "#pres_qs_hurray";
        var ticket = Session.getValue(Const.SESSION_TICKET);

        $('#header-pretashop-plugin').addClass("has-tab");

        var appKey;
        var loading = $('.loading');

        var templateButtonTab = '<div id="btn-tab-hurray" class="tab-item">';
        templateButtonTab += '<span id="txt_hurray_go_to_app">Go to my app</span>';
        templateButtonTab += '</div>';

        var templateContentTab = '<div id="hurray" class="tab-content">';
        templateContentTab += '<div class="tab-content-inner">';
        templateContentTab += '<svg xmlns="http://www.w3.org/2000/svg" width="74" height="76" viewBox="0 0 74 76"><path fill="#67AB48" d="M37,0 C16.6099774,0 0,17.0588957 0,38 C0,58.9411043 16.6099774,76 37,76 C57.3900226,76 74,58.9411043 74,38 C74,17.0588957 57.3900226,0 37,0 Z M37,3.95010395 C55.342932,3.95010395 70.1538462,19.1613131 70.1538462,38 C70.1538462,56.8386869 55.342932,72.049896 37,72.049896 C18.657068,72.049896 3.84615385,56.8386869 3.84615385,38 C3.84615385,19.1613131 18.657068,3.95010395 37,3.95010395 Z M49.7140337,26.7027027 L32.8689556,43.721216 L25.2498545,36.0419089 L22,39.3252236 L32.8689556,50.3243243 L53,29.9860174 L49.7140337,26.7027027 Z"/></svg>';
        templateContentTab += '<div class="title" id="txt_hurray_hurray">Hurray!</div>';
        templateContentTab += '<div class="desc" id="txt_hurray_congrad">Congratulations your app was created successfully, click on go to my app to start designing your app</div>';
        templateContentTab += '<button id="goto-myapp">Go to my app</button>';
        templateContentTab += '</div>';
        templateContentTab += '</div>';


        function getData() {
            PsDataService.getData(id, ticket, currentHash, function(result) {
                result = result || {
                    info: {}
                };

                appKey = result.info.appKey;
                loading.hide();
                self.appKey = result.info.appKey;
                PsUserService.getNewTicket(id, ticket, currentHash, function(result){
                    $('#goto-myapp').off('click').on('click', function(e) {
                        e.preventDefault();
                        loading.show();
                        var endpoint = Const.ORCHARD_URL + "#my-application?ticket=" + result.ticket;
                        // OpenEditApplicationDialogNewTab(appKey, ticket, result.endpoint);
                        window.open(endpoint, '_blank');
                    });
                }, function (result){});
            }, function(result) {
                var response = new KResult();
                response.parse(result);
                notification.error(response.get("Message"));
                loading.hide();
            });
        }

        function onload() {
            $('#pres_qs_app_config').css("display","block");
            if($('#btn-tab-hurray').length == 0) {
                $('.pres-plugin-tabs .tab-buttons-group').append(templateButtonTab);
                $('.pres-plugin-tabs .tab-contents-group').append(templateContentTab);
            }
            AppConfig.init("#pres_qs_hurray");
            $('#ps-languages').trigger('change');
            $('#btn-tab-hurray').trigger('click');
            loading.show();
            getData();
        }
        onload();
    }
};
/**
 * @license
 */
ImportData = {
    init: function(id){
        var ticket = Session.getValue(Const.SESSION_TICKET);
        self.finishLoading = false;
        self.finishImporting = false;
        $('#error_msg_import_data').hide();
        var STATES = {
            success: 'success',
            inprogress: 'inprogress',
            failed: 'failed'
        };

        var currentHash = "#pres_qs_create_app";
        var hashBack = "#pres_qs_logout";
        var hashNext = "#pres_qs_preview_app";
        var configState;
        var importCMSState;
        var importProductState;
        var wizardState;
        var appKey;

        $('#btn-back-import-data').off('click').on('click', function (e) {
            e.preventDefault();
            $('#pres_qs_import_data').css("display","none");
            BusinessQuestions.init(id);
            $('#pres_qs_business_questions').css("display","block");
        });

        $('#btn-next-import-data').off('click').on('click', function (e) {
            e.preventDefault();
            PsDataService.getData(id,ticket,currentHash,function() {
                $('#pres_qs_import_data').css("display", "none");
                $('#pres_qs_preview_app').css("display", "block");
                PreviewApp.init(id);
            });
        });

        function goBack() {
            window.location.hash = hashBack;
        }

        var worker;

        function checkImportState() {
            // getData();
            updateState();
        }

        function importData() {
            var request = {id:id, ticket:ticket, currentHash: currentHash};
            PsCreateAppService.createApp(request
                , function (result) {
                    if(result.ok !== true){
                        $('#error_msg_import_data').show();
                    }
                }, function (result) {
                }
            );
        }

        function updateState() {
            // if(STATES.success === wizardState){
            //     clearInterval(worker);
            //     $('#btn-next-import-data').css("pointer-events", "auto");
            //     $('#btn-back-import-data').css("pointer-events", "auto");
            // }
            // if(configState === STATES.inprogress){
            // }
            // else if(configState === STATES.success){
            //     $('#import-config-status').css("display", "none");
            //     $('#finish-config-status').css("display", "block");
            // }
            // if(importCMSState === STATES.inprogress){
            // }
            // else if(importCMSState === STATES.success){
            //     $('#import-CMS-status').css("display", "none");
            //     $('#finish-CMS-status').css("display", "block");
            // }
            // if(importProductState === STATES.inprogress){
            // }
            // else if(importProductState === STATES.success){
            //     $('#import-product-status').css("display", "none");
            //     $('#finish-product-status').css("display", "block");
            // }

        }

        function getData() {
            PsDataService.getData(id, ticket, currentHash
                , function (result) {
                    result = result || {info: {}};

                    hashBack = result.hashBack || hashBack;
                    configState = result.info.importConfig;
                    importCMSState = result.info.importCMSPage;
                    importProductState = result.info.importProduct;
                    wizardState = result.info.finishWizard;
                    appKey = result.info.appKey;

                    updateState();
                }, function (result) {
                    var response = new KResult();
                    response.parse(result);
                    notification.error(response.get("Message"));
                }
            );
        }

        function onload() {
            // worker =  setInterval(checkImportState, 2000);
            // setTimeout(function() {
            // $('#import-config-status').css("display", "none");
            // $('#finish-config-status').css("display", "block");
            // }, 2000
            // );
            // setTimeout(function() {
            // $('#import-CMS-status').css("display", "none");
            // $('#finish-CMS-status').css("display", "block");
            // }, 4000
            // );
            // setTimeout(function() {
            // $('#import-product-status').css("display", "none");
            // $('#finish-product-status').css("display", "block");
            // $('#btn-next-import-data').removeClass("disabled");
            // $('#btn-back-import-data').removeClass("disabled");
            // $('#import-success-message').css("display", "block");
            // }, 6000
            // );

            setTimeout(function() {
                    $('#import-config-status').addClass("import-config-status-25");
                    $('#import-config-status').text('25%');
                }, 1000
            );
            setTimeout(function() {
                    $('#import-config-status').addClass("import-config-status-50");
                    $('#import-config-status').text('50%');
                }, 2000
            );
            setTimeout(function() {
                    $('#import-config-status').addClass("import-config-status-75");
                    $('#import-config-status').text('75%');
                }, 3200
            );
            setTimeout(function() {
                    $('#import-config-status').css("display", "none");
                    $('#finish-config-status').css("display", "block");
                }, 4500
            );
            setTimeout(function() {
                    $('#import-CMS-status').addClass("import-CMS-status-25");
                    $('#import-CMS-status').text('25%');
                }, 5000
            );
            setTimeout(function() {
                    $('#import-CMS-status').addClass("import-CMS-status-50");
                    $('#import-CMS-status').text('50%');
                }, 6000
            );
            setTimeout(function() {
                    $('#import-CMS-status').addClass("import-CMS-status-75");
                    $('#import-CMS-status').text('75%');
                }, 7000
            );
            setTimeout(function() {
                    $('#import-CMS-status').css("display", "none");
                    $('#finish-CMS-status').css("display", "block");
                }, 8500
            );
            setTimeout(function() {
                    $('#import-product-status').addClass("import-product-status-25");
                    $('#import-product-status').text('25%');
                }, 9000
            );
            setTimeout(function() {
                    $('#import-product-status').addClass("import-product-status-50");
                    $('#import-product-status').text('50%');
                }, 10000
            );
            setTimeout(function() {
                    $('#import-product-status').addClass("import-product-status-75");
                    $('#import-product-status').text('75%');
                }, 11000
            );
            setTimeout(function() {
                    self.finishLoading = true;
                    $('#import-product-status').css("display", "none");
                    $('#finish-product-status').css("display", "block");
                }, 12500
            );
            setTimeout(function() {
                    $('#btn-next-import-data').removeClass("disabled");
                    $('#btn-back-import-data').removeClass("disabled");
                    $('#import-success-message').css("display", "block");
                }, 12700
            );
            if(Session.getValue(Const.APP_CREATED) === 'false') {
                Session.setValue(Const.APP_CREATED, true);
                importData();
                // setTimeout(function() {
                //     importData();
                // }, 60000);
            }
        }
        onload();
    }
};

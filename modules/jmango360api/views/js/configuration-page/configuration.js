/**
 * @license
 */
var Const = {
    // PROD
    API_HOST_URL: "https://orchard.jmango360.com/orchardserver/",
    ORCHARD_URL: 'https://orchard.jmango360.com/',

    // UAT
    // API_HOST_URL: "https://orchard-uat.jmango360.com/orchardserver/",
    // ORCHARD_URL: 'https://orchard-uat.jmango360.com/',

    // INT
    // API_HOST_URL: "https://int-orchardserver.jmango360.com/orchardserver/",
    // ORCHARD_URL: 'https://int-orchard.jmango360.com/',

    SESSION_TICKET: "CASTGC",
    APP_CREATED: 'app_created',
    DEFAULT_LANG: 'default_lang',
    CURRENT_LANG: 'current_lang'
};

var Service = {
    callOrchard : function (request, endpoint, onSuccess, onError, async){
        $.ajax({
            type: 'POST',
            headers: {"Content-Type": "application/json"},
            url: endpoint,
            async: async,
            cache: false,
            dataType: "json",
            data: JSON.stringify(request),
            beforeSend: function () {
            },
            complete: function () {
            },
            success: function (result) {
                onSuccess(result);
            },
            error: function(result) {
                onError(result);
            }
        });
    }
};

var PsDataService = {
    getData: function (id, ticket, currentHash, onSuccess, onError){
        var data = {id: id, ticket:ticket, currentHash: currentHash};
        Service.callOrchard(data, Const.API_HOST_URL + "prestashop/getData", onSuccess, onError, false);
    }
};

var Config = function (data, default_lang, current_language) {
    self.data = data;
    Session.setValue(Const.SESSION_TICKET,data.ticket);
    Session.setValue(Const.DEFAULT_LANG, default_lang.id);
    Session.setValue(Const.CURRENT_LANG, current_language);
    self.nextStep = function (response) {
    };
    var ticket = Session.getValue(Const.SESSION_TICKET);
    function onSuccess(result){
        var hash = result.hashNext;
        var id = result.info._id;
        Session.setValue(Const.APP_CREATED, false);
        QuickstartHeader.init(id);
        if(hash === '#pres_qs_app_config'){
            AppConfig.populateData(id, ticket);
            AppConfig.init("#pres_qs_app_config");
            $('#pres_qs_app_config').css("display","block");
        }else if (hash === '#pres_qs_login'){
            PsLogin.init(id, result.forgotPassword);
            $('#pres_qs_login').css("display","block");
        }else if (hash === '#pres_qs_hurray'){
            $('#pres_qs_hurray').css("display","block");
            HurrayPage.init(id);
        }else if (hash === '#pres_qs_business_info'){
            BusinessQuestions.init(id);
            $('#pres_qs_business_questions').css("display","block");
        }
    }

    function onError(result){

    }

    Service.callOrchard(self.data, Const.API_HOST_URL + "prestashop/getendpoint" , onSuccess, onError, false);

};

var PsSaveConfigService = {
    saveConfig: function (data, onSuccess, onError){
        Service.callOrchard(data, Const.API_HOST_URL + "prestashop/saveAppConfig", onSuccess, onError, false);
    }
};

var PsCreateUserService = {
    createUser: function (data, onSuccess, onError){
        Service.callOrchard(data, Const.API_HOST_URL + "prestashop/createCustomer", onSuccess, onError, false);
    }
};

var PsBusinessQuestionService = {
    submitAnswer: function (data, onSuccess, onError){
        Service.callOrchard(data, Const.API_HOST_URL + "prestashop/saveBusinessInfo", onSuccess, onError, true);
    }
};

var PsCreateAppService = {
    createApp: function (data, onSuccess, onError){
        Service.callOrchard(data, Const.API_HOST_URL + "prestashop/createApp", onSuccess, onError, true);
    }
};

var PsLogin1Service = {
    login1: function(id, email, password, currentHash, onSuccess, onError) {
        var request = {id:id, email:email, password:password, currentHash:currentHash};
        Service.callOrchard(request, Const.API_HOST_URL + "prestashop/login", onSuccess, onError, false);
    }
};

var PsUserService = {
    forgotPassword : function(request, onSuccess, onError){
        Service.callOrchard(request, Const.API_HOST_URL + 'a03/User_ForgotPassword', onSuccess, onError, true);
    },
    getNewTicket : function(id, ticket, currentHash, onSuccess, onError){
        var data = {id: id, ticket:ticket, currentHash: currentHash};
        Service.callOrchard(data, Const.API_HOST_URL + 'prestashop/newTicket', onSuccess, onError, false);
    }
};

StringUtils = {};

StringUtils.encrypt = function(password) {
    var hash = CryptoJS.SHA1(password);
    return CryptoJS.enc.Base64.stringify(hash);
};

var Session = {
    removeAll: function() {
        sessionStorage.clear();
    },
    setValue: function(key, value) {
        sessionStorage.setItem(key, value);
    },
    getValue: function (key) {
        var value = sessionStorage.getItem(key);
        return value;
    }
};


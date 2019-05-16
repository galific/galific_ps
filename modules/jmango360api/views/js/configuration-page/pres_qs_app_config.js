/**
 * @license
 */
var AppConfig = {

    init : function(currentHash) {
        $('#error_msg_save_app_config').hide();
        $.fn.iwTabs = function () {
            $(this).each(function () {
                var iwTabObj = this, $iwTab = $(this);
                iwTabObj.content_list = $iwTab.find('.tab-contents-group .tab-content');
                iwTabObj.list = $iwTab.find('.tab-buttons-group .tab-item');
                iwTabObj.item_click_index = 0;
                $('.tab-buttons-group .tab-item', this).click(function () {
                    if ($(this).hasClass('active')) {
                        return;
                    }
                    var itemclick = this, item_active = $iwTab.find('.tab-buttons-group .tab-item.active');
                    iwTabObj.item_click_index = iwTabObj.list.index(itemclick);
                    $(itemclick).addClass('active');
                    iwTabObj.list.each(function () {
                        if (iwTabObj.list.index(this) !== iwTabObj.list.index(itemclick) && $(this).hasClass('active')) {
                            $(this).removeClass('active');
                        }
                    });
                    iwTabObj.loadTabContent();
                });
                this.loadTabContent = function () {
                    var item_click = $(iwTabObj.content_list.get(iwTabObj.item_click_index));
                    iwTabObj.content_list.each(function () {
                        if (iwTabObj.content_list.index(this) < iwTabObj.content_list.index(item_click)) {
                            $(this).addClass('prev').removeClass('active next');
                        } else if (iwTabObj.content_list.index(this) === iwTabObj.content_list.index(item_click)) {
                            $(this).addClass('active').removeClass('prev next');
//                            $(".map-contain",this).iwMap();
                        } else {
                            $(this).addClass('next').removeClass('prev active');
                        }
                    });
                };

            });
        };

        $('.pres-plugin-tabs').iwTabs();
        if(Session.getValue("tiny-tip") === null){
            $('.tTips').tinyTips('data-title');
            Session.setValue("tiny-tip", "called");
        }
        if(currentHash === '#pres_qs_hurray'){
            $('.pres-plugin-tabs .tab-buttons-group #btn-tab-hurray').trigger('click');
            $('#btn-tab-get-started').css("display","none");

            $('.btn-get-started').hide();
        }
        if(currentHash === '#pres_qs_app_config'){
            $('.tab-buttons-group .tab-item:nth-child(1)').trigger('click');
            $('.btn-get-started').show();
        }

        $('.btn-get-started').off('click').on('click', function (e) {
            e.preventDefault();
            $('.tab-buttons-group .tab-item:nth-child(1)').trigger('click');
        });

        $('#header-pretashop-plugin').addClass("has-tab");

        $('.tab-item:not(#btn-tab-introduction)').on('click', function () {
            $('#app_config_intro_video').get(0).pause();
            $('#app_config_intro_video').get(0).load();
        })

    },

    populateData : function (id, ticket){
        self.langList = [];
        var onSuccess = function (result) {
            var info = result.info;
            self.hashNext = result.hashNext;
            $('#store-url').val(info.shopUrl);
            var shopIdList = document.getElementById("shop-ids");
            for (var i = 0; i < info.shops.length; i++) {
                var shopOption = document.createElement("option");
                shopOption.value = info.shops[i].id_shop;
                shopOption.text = info.shops[i].name;
                if (info.shops[i].id_shop === info.currentShop.id_shop) {
                    shopOption.selected = true;
                }
                shopIdList.appendChild(shopOption);
            }

            var languagesList = document.getElementById("languages");
            for (var j = 0; j < info.languages.length; j++) {
                self.langList.push({id_lang: info.languages[j].id_lang, iso_code: info.languages[j].iso_code});
                var langOption = document.createElement("option");
                langOption.value = info.languages[j].id_lang;
                langOption.text = info.languages[j].name;
                var default_lang = Session.getValue(Const.DEFAULT_LANG);
                if (info.languages[j].id_lang === parseInt(default_lang)) {
                    langOption.selected = true;
                }
                languagesList.appendChild(langOption);
            }

            $('select.txt-select').selectpicker({
                style: ''
            });

            $('#ps-create-app').click(function () {
                $('#error_msg_save_app_config').hide();
                submitData(id, ticket);
            });
        };

        var onError = function (result) {
        };

        var onSaveSuccess = function (result) {
            if(result.ok === true) {
                var newId = result.info._id;
                $('#header-pretashop-plugin').removeClass("has-tab");
                if (self.hashNext === '#pres_qs_create_customer') {
                    $('#pres_qs_app_config').css("display", "none");
                    CreateAccount.init(newId);
                    $('#pres_qs_create_account').css("display", "block");
                }
                else if (self.hashNext === '#pres_qs_business_info') {
                    $('#pres_qs_app_config').css("display", "none");
                    BusinessQuestions.init(newId);
                    $('#pres_qs_business_questions').css("display", "block");
                }
                else if (self.hashNext === '#pres_qs_create_app') {
                    $('#pres_qs_app_config').css("display", "none");
                    $('#pres_qs_import_data').css("display", "block");
                    ImportData.init(newId);
                }
            } else {
                $('#error_msg_save_app_config').show();
            }
        };

        var onSaveError = function (result) {
        };


        var submitData = function (id, ticket) {
            var data;
            var id_lang = $('#languages').find("option:selected").val();
            var id_shop = $('#shop-ids').find('option:selected').val();
            var iso_code;
            for(var i = 0; i<self.langList.length; i++){
                if(self.langList[i].id_lang === parseInt(id_lang)){
                    iso_code = self.langList[i].iso_code;
                }
            }
            var url = $('#store-url').val();
            data = {
                id: id,
                currentHash: currentHash,
                shopUrl: url,
                current_shop: {id_shop: id_shop},
                current_language: {id_lang: id_lang, iso_code : iso_code},
                ticket: ticket
            };
            PsSaveConfigService.saveConfig(data, onSaveSuccess, onSaveError);
        };

        var currentHash = "#pres_qs_app_config";

        PsDataService.getData(id, ticket, currentHash, onSuccess, onError);
    }
};
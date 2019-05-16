/**
 * @license
 */QuickstartHeader =  {
    init: function (id){
        var currentHash = "#pres_qs_app_config";
        var ticket = Session.getValue(Const.SESSION_TICKET);
        var supportedLang = ['en', 'fr', 'es', 'it', 'nl'];
        var videoUrl = {en: "https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-en.mp4",
                        es: "https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-spanish.mp4",
                        it: "https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-italian.mp4",
                        nl: "https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-dutch.mp4",
                        fr: "https://s3.eu-central-1.amazonaws.com/prod-eu-frankfurt/others/prestashop/jmango360-fr.mp4"};

        var iso_code = Session.getValue(Const.CURRENT_LANG);
        console.log(supportedLang.includes(iso_code));
        if(supportedLang.includes(iso_code)){
            $("#ps-languages").val(iso_code).change();
            Localization.trans(iso_code);
            setLocalizedVideo(iso_code);
        }

        $('#header-pretashop-plugin select.txt-lang-change').selectpicker();

        function setLocalizedVideo(iso_code){
            $('#app_config_intro_video').get(0).pause();
            $('#app_config_intro_video_source').attr("src", videoUrl[iso_code]);
            $('#app_config_intro_video').get(0).load();

        }

        $('body').addClass($('#ps-languages').val());

        $('#ps-languages').on('change', function(){
            var selectedLang = $('#ps-languages').val();
            Localization.trans(selectedLang);
            setLocalizedVideo(selectedLang);
            $('body').removeClass('en fr es it nl');
            $('body').addClass(selectedLang);

        });
    }
};
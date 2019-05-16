/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 Knowband
 * @license   see file: LICENSE.txt
 */

var file_error = false;
var slider_banner_file_error = false;
/*start:Changes made by Aayushi Agarwal on 1 Dec 2018 for logo at navigation bar*/
var logo_navigation_file_error = false;
/*end*/
var default_file_size = 2097152;
var google_file_error = false;

$(document).ready(function()
{
    $('#general_form').addClass('col-lg-10 col-md-9');
    $('#push_form').addClass('col-lg-10 col-md-9');
    $('#payments_form').addClass('col-lg-10 col-md-9');
    $('#sliders_settings_form').addClass('col-lg-10 col-md-9');
    $('#sliders_settings_form').css("float", "right");
    $('#push_notification_settings_form').addClass('col-lg-10 col-md-9');
    $('#configuration_form').addClass('col-lg-10 col-md-9');
    $('#form-configuration').addClass('col-lg-10 col-md-9');
    $("#form-configuration").css("float", "right");
    $('#slider_form').addClass('col-lg-10 col-md-9');
    $("#slider_form").css("float", "right");
    $('#form-kb_push_notifications_history').addClass('col-lg-10 col-md-9');
    $("#form-kb_push_notifications_history").css("float", "right");
    $('#form-kb_sliders_list').addClass('col-lg-10 col-md-9');
    $("#form-kb_sliders_list").css("float", "right");
    $('#form-kb_banners_list').addClass('col-lg-10 col-md-9');
    $("#form-kb_banners_list").css("float", "right");
    /* changes by rishabh jain
     * for layput tab
     */
    $('#form-kb_layouts_list').addClass('col-lg-10 col-md-9');
    $("#form-kb_layouts_list").css("float", "right");
    /* changes over */
    $('#google_setup_form').addClass('col-lg-10 col-md-9');
    $("#google_setup_form").css("float", "right");
    $('#facebook_setup_form').addClass('col-lg-10 col-md-9');
    $("#facebook_setup_form").css("float", "right");

    $("#image_url").on('blur', function () {
        $('.kb_error_message').remove();
        $('input[name="image_url"]').removeClass('kb_error_field');
        if ($('#image_url').val() != '') {
            var image_url_err = velovalidation.checkUrl($('input[name="image_url"]'));
            $('#sliderimage').attr('src', $('#image_url').val());
        }
    });


    $(".slides").sortable({
        placeholder: 'slide-placeholder',
        axis: "y",
        revert: 150,
        start: function (e, ui) {

            placeholderHeight = ui.item.outerHeight();
            ui.placeholder.height(placeholderHeight + 15);
            $('<div class="slide-placeholder-animator" data-height="' + placeholderHeight + '"></div>').insertAfter(ui.placeholder);

        },
        change: function (event, ui) {

            ui.placeholder.stop().height(0).animate({
                height: ui.item.outerHeight() + 15
            }, 300);

            placeholderAnimatorHeight = parseInt($(".slide-placeholder-animator").attr("data-height"));

            $(".slide-placeholder-animator").stop().height(placeholderAnimatorHeight + 15).animate({
                height: 0
            }, 300, function () {
                $(this).remove();
                placeholderHeight = ui.item.outerHeight();
                $('<div class="slide-placeholder-animator" data-height="' + placeholderHeight + '"></div>').insertAfter(ui.placeholder);
            });

        },
        stop: function (e, ui) {

            // set position of components
            var order_component = $(".slides").sortable('toArray');
            var id_layout = $('#id_layout').val();
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&setComponentOrder=true",
                data: 'id_layout=' + id_layout + '&position_array=' + order_component,
                type: "post",
                success: function (data)
                {
                    var b = JSON.parse(data);
                }
            });
            // changes over
            $(".slide-placeholder-animator").remove();
            showSuccessMessage(position_update);
            preview_content();

        },
    });


    $('#general_form').show();
    $('#push_form').hide();
    $('#payments_form').hide();
    $('#push_notification_settings_form').hide();
    $('#form-configuration').hide();
    $('#form-kb_push_notifications_history').hide();
    $('#vss-button').hide();
    $('#vss-send-notofication-button').hide();
    $('#form-kb_sliders_list').hide();
    $('#form-kb_banners_list').hide();
    $('#vss-validate-button').hide();


    $('#image_url').parent().parent().hide();
    $('#slideruploadedfile').parent().parent().parent().parent().hide();
    $('#category_id').parent().parent().hide();
    $('#redirect_product_name').parent().parent().hide();

    $('#push_notification_image_url').parent().parent().hide();
    $('#uploadedfile').parent().parent().parent().parent().hide();
    $('#push_notification_redirect_category_id').parent().parent().hide();
    $('#push_notification_redirect_product_name').parent().parent().hide();

    if (default_tab == 'PushNotificationHistory') {
        change_tab($("#link-"+default_tab), 3);
    } else if (default_tab == 'SlidersSettings') {
        change_tab($("#link-"+default_tab), 4);
    } else if (default_tab == 'BannersSettings') {
        change_tab($("#link-"+default_tab), 5);
    } else if (default_tab == 'PaymentMethods') {
        change_tab($("#link-"+default_tab), 6);
    }/*start:changes made by Aayushi Agarwal on 1 dec 2018 for shipping methods*/
      else if ( default_tab == 'ShippingMethods') {
         change_tab($("#link-"+default_tab), 7);
    }
    /*end*/
    else {
        change_tab($("#link-GeneralSettings"), 1);
    }
    /*start:changes made by Aayushi Agarwal on 1 dec 2018 for logo at navigation bar*/

    $('input:radio[name="KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH"]').on('click change', function(e) {

        if ($(this).val() == 1) {
            $('#KB_MOBILE_APP_ADD_LOGO_NAVIGATION').parent().parent().parent().parent().show();
        } else {
            $('#KB_MOBILE_APP_ADD_LOGO_NAVIGATION').parent().parent().parent().parent().hide();
        }
    });

    if ($('input[name="KB_MOBILE_APP_ADD_LOGO_NAVIGATION_SWITCH"]:checked').val() == "1" ) {
        //$('#KB_MOBILE_APP_ADD_LOGO_NAVIGATION').attr('src', b.image_url);
        $('#KB_MOBILE_APP_ADD_LOGO_NAVIGATION').parent().parent().parent().parent().show();
    } else {
        $('#KB_MOBILE_APP_ADD_LOGO_NAVIGATION').parent().parent().parent().parent().hide();
    }
    $('#KB_MOBILE_APP_ADD_LOGO_NAVIGATION').on('change', function(e) {
        $('.kb_error_message').remove();
        $('input[name="filename"]').removeClass('kb_error_field')
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];
            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('input[name="KB_MOBILE_APP_ADD_LOGO_NAVIGATION"]').parent().parent().append('<span class="kb_error_message">'+invalid_file_format_txt+'</span>');
                logo_navigation_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="KB_MOBILE_APP_ADD_LOGO_NAVIGATION"]').parent().parent().append('<span class="kb_error_message">'+file_size_error_txt+'</span>');
                logo_navigation_file_error = true;
            } else {
                logo_navigation_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sldierimages");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function(e) {
                        $('#sldierimages').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="KB_MOBILE_APP_ADD_LOGO_NAVIGATION"]').parent().parent().find('.kb_error_message').remove();
            }

        }
    });
//        else // Internet Explorer 9 Compatibility
//        {
//            $('#notification_error').html(invalid_file_txt);
//            file_error = true;
//        }
    /*end:changes made by Aayushi Agarwal on 1 dec 2018 for logo at navigation bar*/
    $('input:radio[name="KB_MOBILE_APP_CHAT_SUPPORT"]').on('click change', function(e) {

        if ($(this).val() == 1) {
            $('#KB_MOBILE_APP_CHAT_SUPPORT_KEY').parent().parent().show();
        } else {
            $('#KB_MOBILE_APP_CHAT_SUPPORT_KEY').parent().parent().hide();
        }
    });

    if ($('input[name="KB_MOBILE_APP_CHAT_SUPPORT"]:checked').val() == "1" ) {
        $('#KB_MOBILE_APP_CHAT_SUPPORT_KEY').parent().parent().show();
    } else {
        $('#KB_MOBILE_APP_CHAT_SUPPORT_KEY').parent().parent().hide();
    }
    /* Changes started
     * @author : Rishabh Jain
     * DOM : 25/09/2018
     * To hide whatsapp number field if disabled
     */
    $('input:radio[name="KB_MOBILE_WHATSAPP_CHAT_SUPPORT"]').on('click change', function (e) {

        if ($(this).val() == 1) {
            $('#KB_MOBILE_WHATSAPP_CHAT_NUMBER').parent().parent().show();
        } else {
            $('#KB_MOBILE_WHATSAPP_CHAT_NUMBER').parent().parent().hide();
        }
    });
    if ($('input[name="KB_MOBILE_WHATSAPP_CHAT_SUPPORT"]:checked').val() == "1") {
        $('#KB_MOBILE_WHATSAPP_CHAT_NUMBER').parent().parent().show();
    } else {
        $('#KB_MOBILE_WHATSAPP_CHAT_NUMBER').parent().parent().hide();
    }
    /* Changes over */

    /* Changes started by rishabh jain
     * to show reqired mobile number field only after admin enable mobile number regustration
     */
    if ($('input:radio[name="KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION"]:checked').val() == '1') {
        $('input:radio[name="KB_MOBILEAPP_PHONE_NUMBER_MANDATORY"]').parent().parent().parent().show();
    } else {
        $('input:radio[name="KB_MOBILEAPP_PHONE_NUMBER_MANDATORY"]').parent().parent().parent().hide();
    }
    $('input:radio[name="KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION"]').on('change', function (e) {
        if ($(this).val() == 1) {
            $('input:radio[name="KB_MOBILEAPP_PHONE_NUMBER_MANDATORY"]').parent().parent().parent().show();
        } else {
            $('input:radio[name="KB_MOBILEAPP_PHONE_NUMBER_MANDATORY"]').parent().parent().parent().hide();
        }
    });
    if ($('input[name="KB_MOBILEAPP_PHONE_NUMBER_REGISTRTAION"]:checked').val() == "1") {
        $('#KB_MOBILEAPP_PHONE_NUMBER_MANDATORY').parent().parent().show();
    } else {
        $('#KB_MOBILEAPP_PHONE_NUMBER_MANDATORY').parent().parent().hide();
    }

    /* Changes over */

    $('#push_notification_redirect_product_name').autocomplete(ajaxaction + '&configure=kbmobileapp&ajaxproductaction=true', {
        delay: 100,
        minChars: 1,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        cacheLength: 0,
        // param multipleSeparator:'||' ajouté à cause de bug dans lib autocomplete
        multipleSeparator: '||',
        formatItem: function(item) {
            return item[1] + ' - ' + item[0];
        },
        extraParams: {
            excludeIds: '',
            excludeVirtuals: '',
            exclude_packs: ''
        }
    }).result(function(event, item) {
        $('#push_notification_redirect_product_id').val(item[1]);
        $('#push_notification_redirect_product_name').val(item[0]);
    });


    $('#redirect_product_name').autocomplete(ajaxaction + '&configure=kbmobileapp&ajaxproductaction=true', {
        delay: 100,
        minChars: 1,
        autoFill: true,
        max: 20,
        matchContains: true,
        mustMatch: true,
        scroll: false,
        cacheLength: 0,
        // param multipleSeparator:'||' ajouté à cause de bug dans lib autocomplete
        multipleSeparator: '||',
        formatItem: function(item) {
            return item[1] + ' - ' + item[0];
        },
        extraParams: {
            excludeIds: '',
            excludeVirtuals: '',
            exclude_packs: ''
        }
    }).result(function(event, item) {
        $('#redirect_product_id').val(item[1]);
        $('#redirect_product_name').val(item[0]);
    });

    $('.paypal-payment-info').hide();

    $("#payment_method").on('change', function() {
        var payment_method_name = $('#payment_method_name_' + default_language_id).val();
        payment_method_name = $.trim(payment_method_name);
//               $('#payment_method_name').val($(this).val());
        $('#payment_method_name_' + default_language_id).val($("#payment_method :selected").text());
        if ($(this).val() == 'paypal') {
            $('#payment_method_name_' + default_language_id).parents('.form-group').show();
            $('#payment_method_client_id').parent().parent().show();
            $('#payment_method_mode').parent().parent().show();
            $('#payment_method_other_info').parent().parent().hide();
            $('.paypal-payment-info').show();
        } else if ($(this).val() == 'cod') {
            $('#payment_method_name_' + default_language_id).parents('.form-group').show();
            $('#payment_method_client_id').parent().parent().hide();
            $('#payment_method_mode').parent().parent().hide();
            $('#payment_method_other_info').parent().parent().hide();
            $('.paypal-payment-info').hide();
        } else {
            $('#payment_method_name_' + default_language_id).parents('.form-group').hide();
            $('#payment_method_client_id').parent().parent().hide();
            $('#payment_method_mode').parent().parent().hide();
            $('#payment_method_other_info').parent().parent().hide();
            $('.paypal-payment-info').hide();
        }
    });


    $("#push_notification_image_type").on('change', function() {
        if ($(this).val() == 'url') {
            $('#push_notification_image_url').parent().parent().show();
            $('#uploadedfile').parent().parent().parent().parent().show();
            $('#uploadedfile').parent().hide();
        } else if ($(this).val() == 'image') {
            $('#push_notification_image_url').parent().parent().hide();
            $('#uploadedfile').parent().parent().parent().parent().show();
            $('#uploadedfile').parent().show();
        } else {
            $('#push_notification_image_url').parent().parent().hide();
            $('#uploadedfile').parent().parent().parent().parent().hide();
        }
    });

    $("#push_notification_image_url").on('blur', function() {
        $('.kb_error_message').remove();
        $('input[name="push_notification_image_url"]').removeClass('kb_error_field');
        if ($(this).val() != '') {
//            var image_url_err = velovalidation.checkUrl($('input[name="push_notification_image_url"]'));
//            if (image_url_err != true)
//            {
//                $('input[name="push_notification_image_url"]').addClass('kb_error_field');
//                $('input[name="push_notification_image_url"]').after('<span class="kb_error_message">' + image_url_err + '</span>');
//            } else {
                $('#notificatonimage').attr('src', $(this).val());
//            }
        }
    });

    $("#image_url").on('blur', function() {
        $('.kb_error_message').remove();
        $('input[name="image_url"]').removeClass('kb_error_field');
        if ($(this).val() != '') {
            var image_url_err = velovalidation.checkUrl($('input[name="image_url"]'));
//            if (image_url_err != true)
//            {
//                $('input[name="image_url"]').addClass('kb_error_field');
//                $('input[name="image_url"]').after('<span class="kb_error_message">' + image_url_err + '</span>');
//            } else {
                $('#sliderimage').attr('src', $(this).val());
//            }
        }
    });

    $("#push_notification_redirect_type").on('change', function() {
        if ($(this).val() == 'category') {
            $('#push_notification_redirect_category_id').parent().parent().show();
            $('#push_notification_redirect_product_name').parent().parent().hide();
        } else if ($(this).val() == 'product') {
            $('#push_notification_redirect_category_id').parent().parent().hide();
            $('#push_notification_redirect_product_name').parent().parent().show();
        } else {
            $('#push_notification_redirect_category_id').parent().parent().hide();
            $('#push_notification_redirect_product_name').parent().parent().hide();
        }
    });
    $("#image_type").on('change', function() {
        if ($(this).val() == 'url') {
            $('#image_url').parent().parent().show();
            $('#slideruploadedfile').parent().parent().parent().parent().show();
            $('#slideruploadedfile').parent().hide();
        } else if ($(this).val() == 'image') {
            $('#image_url').parent().parent().hide();
            $('#slideruploadedfile').parent().parent().parent().parent().show();
            $('#slideruploadedfile').parent().show();
        } else {
            $('#image_url').parent().parent().hide();
            $('#slideruploadedfile').parent().parent().parent().parent().hide();
        }
    });

    $("#redirect_activity").on('change', function() {
        if ($(this).val() == 'category') {
            $('#category_id').parent().parent().show();
            $('#redirect_product_name').parent().parent().hide();
        } else if ($(this).val() == 'product') {
            $('#category_id').parent().parent().hide();
            $('#redirect_product_name').parent().parent().show();
        } else {
            $('#category_id').parent().parent().hide();
            $('#redirect_product_name').parent().parent().hide();
        }
    });


    $('.kb_general_setting_btn').click(function() {
        return veloValidateConfigForms(this);
    });
    $('.kb_push_notification_btn').click(function() {
        return veloValidateConfigForms(this);
    });
    $('.kb_payment_method_btn').click(function() {
        return veloValidatePaymentForm(this);
    });

    $('.kb_slider_banner_setting_btn').click(function() {
        return veloValidateBannerSliderForm(this);
    });
    $('.kb_product_setting_btn').click(function () {
        return veloValidateProductForm(this);
    });

    $('.kb_facebook_setup_btn').click(function() {
        return veloValidateFacebookSetupForm(this);
    });

    $('.kb_google_setup_btn').click(function() {
        return veloValidateGoogleSetupForm(this);
    });

    $('#send_notification_btn').click(function() {
        sendNotification();
    });

    $('#uploadedfile').on('change', function(e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];

            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('#notification_error').html(invalid_file_format_txt);
                file_error = true;

            } else if (files.size > 2097152) {
                $('#notification_error').html(file_size_error_txt);
                file_error = true;
            } else {
                file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#notificatonimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#notificatonimage').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('#notification_error').html('');
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });

    $('#slideruploadedfile').on('change', function(e) {
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'image/gif',
                'image/jpeg',
                'image/png',
                'application/x-shockwave-flash',
                'image/psd',
                'image/bmp',
                'image/tiff',
                'application/octet-stream',
                'image/jp2',
                'image/iff',
                'image/vnd.wap.wbmp',
                'image/xbm',
                'image/vnd.microsoft.icon',
                'image/webp'
            ];

            var file_format = false;
            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }

            if (!file_format)
            {
                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">'+invalid_file_format_txt+'</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">'+file_size_error_txt+'</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function(e) {

                        $('#sliderimage').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });



    $('#googlejsonfile').on('change', function(e) {
        $('.kb_error_message').remove();
        if ($(this)[0].files !== undefined && $(this)[0].files.length > 0)
        {
            var files = $(this)[0].files[0];
            var file_data = e.target.files;
            var file_mimetypes = [
                'application/json',
                'application/x-javascript',
                'text/javascript',
                'text/x-javascript',
                'text/x-json'
            ];

            var file_format = false;

            var filename = files.name;
            var ext = filename.split(".");
            ext = ext[ext.length-1].toLowerCase();

            for (i = 0; i < file_mimetypes.length; i++) {
                if (files.type == file_mimetypes[i]) {
                    file_format = true;
                }
            }


            if (ext != 'json')
            {
                $('#googlejsonfile-name').addClass('kb_error_field');
                $('#googlejsonfile').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                google_file_error = true;

            } else if (files.size > 2097152) {
                $('#googlejsonfile-name').addClass('kb_error_field');
                $('#googlejsonfile').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                google_file_error = true;
            } else {
                google_file_error = false;
                $('#googlejsonfile-name').removeClass('kb_error_field');
                $('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#googlejsonfile-name').addClass('kb_error_field');
            $('#googlejsonfile').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
            google_file_error = true;
        }
    });

    $('.gamification-tip').hide();

});
function change_tab(a, b) {
    $('.gamification-tip').hide();
    if (version == 1.5) {
        $('.tab-page').removeClass('active');
        $('.tab-page').removeClass('selected');
    }
    $('.list-group-item').removeClass('active');
    $(a).addClass(' active');
    if (b == 1) {
        $("[id^='fieldset'] h3 span").html(general_settings);
        $(".panel-heading span").html(general_settings);
//                $('.panel-heading').remove($('#vss-button'));
//                $('#vss-button').appendTo($(".panel-heading"));
        $('#general_form').show();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').hide();
        $('.vss-add-new').hide();
        $('#vss-add-new-layout-button').hide();
        $('#vss-button').hide();
        $('#vss-send-notofication-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').hide();
        $('#google_setup_form').hide();
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        closePaymentMethod();
        hideSliderForm();
    } else if (b == 3) {
        $("[id^='fieldset'] h3 span").html(push_notification_history);
        $(".panel-heading span").html(push_notification_history);
        closeNotificationForm();
        $('#general_form').hide();
        $('#push_form').show();
        $('#push_notification_settings_form').hide();
        $('#push_notification_image_url').parent().parent().hide();
        $('#uploadedfile').parent().parent().parent().parent().hide();
        $('#payments_form').hide();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('.panel-heading').remove($('#vss-send-notofication-button'));
        $('#vss-send-notofication-button').appendTo($("#form-kb_push_notifications_history .panel .panel-heading"));
        $('#vss-send-notofication-button').show();
        $('#vss-add-new-layout-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').show();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').hide();
        $('#google_setup_form').hide();
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        closePaymentMethod();
        hideSliderForm();
    } else if (b == 4) {
        $("[id^='fieldset'] h3 span").html(payments_settings);
        $(".panel-heading span").html(payments_settings);
        $('.panel-heading').remove($('#vss-button'));
        $('#vss-button').appendTo($("#form-configuration .panel h3"));
        $('#general_form').hide();
        $('#push_notification_settings_form').hide();
        $('#push_form').hide();
        $('#payments_form').show();
        $('#vss-button').show();
        $('#vss-send-notofication-button').hide();
        $('#vss-add-new-layout-button').hide();
        $('#form-configuration').show();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').hide();
        $('#google_setup_form').hide();
        $('#vss-validate-button').hide();
        $('#facebook_setup_form').hide();
        hideSliderForm();
    } else if (b == 2) {
        $("[id^='fieldset'] h3 span").html(push_notification_settings);
        $(".panel-heading span").html(push_notification_settings);
        $('#general_form').hide();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').show();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('#vss-add-new-layout-button').hide();
        $('#vss-send-notofication-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').hide();
        $('#google_setup_form').hide();
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        closePaymentMethod();
        hideSliderForm();
    } else if (b == 4) {
        $("[id^='fieldset'] h3 span").html(sliders_settings);
        $(".panel-heading span").html(sliders_settings);
        $('#general_form').hide();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').hide();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('#vss-send-notofication-button').hide();
        $('#vss-add-new-layout-button').show();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').show();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').hide();
        $('#vss-cancel_slider_settings-button').appendTo($("#slider_form .panel .panel-heading"));
        $('#google_setup_form').hide();
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        closePaymentMethod();
        hideSliderForm();
    } else if (b == 5) {
//        $("[id^='fieldset'] h3 span").html(sliders_settings);
//        $(".panel-heading span").html(sliders_settings);
//        $('#general_form').hide();
//        $('#push_form').hide();
//        $('#payments_form').hide();
//        $('#push_notification_settings_form').hide();
//        $('.vss-add-new').hide();
//        $('#vss-button').hide();
//        $('#vss-send-notofication-button').hide();
//        $('#form-configuration').hide();
//        $('#form-kb_push_notifications_history').hide();
//        $('#form-kb_sliders_list').hide();
//        $('#form-kb_layouts_list').hide();
//        $('#form-kb_banners_list').show();
//        $('#vss-cancel_slider_settings-button').appendTo($("#slider_form .panel .panel-heading"));
//        $('#google_setup_form').hide();
//        $('#vss-validate-button').hide();
//        $('#facebook_setup_form').hide();
//        closePaymentMethod();
//        hideSliderForm();

        $('#general_form').hide();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').hide();
        $('.vss-add-new').hide();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('#vss-add-new-layout-button').show();
        $('#vss-send-notofication-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').show();
        /*start: changes made by Aayushi Agarwal on 1 Dec 2018 for shipping methods*/
        //$('#google_setup_form').show();
        $('#form-kb_shipping_list').show();
        /*end*/
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        $('.panel-heading').remove($('#vss-add-new-layout-button'));
        $('#vss-add-new-layout-button').appendTo($("#layout_list .panel .panel-heading"));
        closePaymentMethod();
        hideSliderForm();
    } else if (b == 7) {
        $('#general_form').hide();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').hide();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('#vss-send-notofication-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#form-kb_layouts_list').show();
        /*start: changes made by Aayushi Agarwal on 1 Dec 2018 for shipping methods*/
        //$('#google_setup_form').show();
        $('#form-kb_shipping_list').show();
        /*end*/
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        closePaymentMethod();
        hideSliderForm();
    } else if (b == 8) {
        $('#general_form').hide();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').hide();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('#vss-send-notofication-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#google_setup_form').hide();
        $('#vss-validate-button').show();
        $('#facebook_setup_form').show();
        $('#vss-validate-button').appendTo($("#facebook_setup_form .form-group:last .col-lg-9"));

        closePaymentMethod();
        hideSliderForm();
    }else {
        $('#general_form').show();
        $('#push_form').hide();
        $('#payments_form').hide();
        $('#push_notification_settings_form').hide();
        $('.vss-add-new').hide();
        $('#vss-button').hide();
        $('#vss-send-notofication-button').hide();
        $('#form-configuration').hide();
        $('#form-kb_push_notifications_history').hide();
        $('#form-kb_sliders_list').hide();
        $('#form-kb_banners_list').hide();
        $('#google_setup_form').hide();
        $('#facebook_setup_form').hide();
        $('#vss-validate-button').hide();
        closePaymentMethod();
        hideSliderForm();
    }
    $('.list-group-item').attr('class', 'list-group-item');
    $(a).attr('class', 'list-group-item active');
}

function addPaymentMethod()
{
    getpaymentForm();
    $('#payment_method_name_' + default_language_id).parents('.form-group').hide();
    $('#payment_method_client_id').parent().parent().hide();
    $('#payment_method_other_info').parent().parent().hide();
    $('#payment_method_mode').parent().parent().hide();
    $('.vss-add-new').slideDown("fast", function() {
        $('#add_new').html(close_new_entry);
        $('#add_new').attr('onclick', 'closePaymentMethod()');
        $('#form-configuration .panel h3').remove("#vss-button");
        $('#vss-button').appendTo($("#configuration_form .panel-heading"));
    });
    $('#payment_method_name').val('');
    $('#payment_method_client_id').val('');
    $('#payment_method_other_info').val('');
    $('#payment_method_mode').val('live');
}


function closePaymentMethod()
{
    $('.vss-add-new').slideUp("fast", function() {
        $('#add_new').html(add_new_entry);
        $('#add_new').attr('onclick', 'addPaymentMethod()');
        $('#configuration_form .panel-heading').remove("#vss-button");
        $('#vss-button').appendTo($("#form-configuration .panel h3"));
        $('#payment_method').val('0');
    });
}

function showNotificationForm()
{
//    getpaymentForm();

    $('.vss-send-new-notification').slideDown("fast", function() {
        $('#send_notification').html(close_new_entry);
        $('#send_notification').attr('onclick', 'closeNotificationForm()');
        $('#form-kb_push_notifications_history .panel .panel-heading').remove("#vss-send-notofication-button");
        $('#vss-send-notofication-button').appendTo($("#push_form .panel-heading"));
    });
}

function closeNotificationForm()
{
    $('.vss-send-new-notification').slideUp("fast", function() {
        $('#send_notification').html(send_notification_text);
        $('#send_notification').attr('onclick', 'showNotificationForm()');
        $('#push_form .panel-heading').remove("#vss-send-notofication-button");
        $('#vss-send-notofication-button').appendTo($("#form-kb_push_notifications_history .panel .panel-heading"));
//        $('#payment_method').val('0');
    });
}



function veloValidateConfigForms(button_ele)
{
    var is_error = false;
    var general_setting_error = false;
    var push_notification_error = false;

    $('.kb_error_message').remove();
    $('textarea[name="KB_MOBILEAPP_CSS"]').removeClass('kb_error_field');
    $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').removeClass('kb_error_field');


    if ($.trim($('#KB_MOBILEAPP_CSS').val()) != '') {
        var css_mandatory_err = velovalidation.checkMandatory($('textarea[name="KB_MOBILEAPP_CSS"]'));
        if (css_mandatory_err != true)
        {
            is_error = true;
            general_setting_error = true;
            $('textarea[name="KB_MOBILEAPP_CSS"]').addClass('kb_error_field');
            $('textarea[name="KB_MOBILEAPP_CSS"]').after('<span class="kb_error_message">' + css_mandatory_err + '</span>');
        }
    }

    if ($('input[name="push_notification[create_order][status]"]:checked').val() == "1" || $('input[name="push_notification[order_status_change][status]"]:checked').val() == "1" || $('input[name="push_notification[abandoned_cart][status]"]:checked').val() == "1") {
        var key_mandatory_err = velovalidation.checkMandatory($('input[name="KB_MOBILEAPP_FIREBASE_KEY"]'));
        if (key_mandatory_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').addClass('kb_error_field');
            $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').after('<span class="kb_error_message">' + key_mandatory_err + '</span>');
        }
    }

    if ($('input[name="push_notification[create_order][status]"]:checked').val() == "1") {

        var create_order_title_err = velovalidation.checkMandatory($('input[name="push_notification[create_order][title]"]'));
        if (create_order_title_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('input[name="push_notification[create_order][title]"]').addClass('kb_error_field');
            $('input[name="push_notification[create_order][title]"]').after('<span class="kb_error_message">' + create_order_title_err + '</span>');
        }

        var create_order_message_err = velovalidation.checkMandatory($('textarea[name="push_notification[create_order][message]"]'));
        if (create_order_message_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('textarea[name="push_notification[create_order][message]"]').addClass('kb_error_field');
            $('textarea[name="push_notification[create_order][message]"]').after('<span class="kb_error_message">' + create_order_message_err + '</span>');
        }
    }

    if ($('input[name="push_notification[order_status_change][status]"]:checked').val() == "1") {

        var order_status_change_title_err = velovalidation.checkMandatory($('input[name="push_notification[order_status_change][title]"]'));
        if (order_status_change_title_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('input[name="push_notification[order_status_change][title]"]').addClass('kb_error_field');
            $('input[name="push_notification[order_status_change][title]"]').after('<span class="kb_error_message">' + order_status_change_title_err + '</span>');
        }

        var order_status_change_message_err = velovalidation.checkMandatory($('textarea[name="push_notification[order_status_change][message]"]'));
        if (order_status_change_message_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('textarea[name="push_notification[order_status_change][message]"]').addClass('kb_error_field');
            $('textarea[name="push_notification[order_status_change][message]"]').after('<span class="kb_error_message">' + order_status_change_message_err + '</span>');
        }
    }

    if ($('input[name="push_notification[abandoned_cart][status]"]:checked').val() == "1") {



        var abandoned_cart_title_err = velovalidation.checkMandatory($('input[name="push_notification[abandoned_cart][title]"]'));
        if (abandoned_cart_title_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('input[name="push_notification[abandoned_cart][title]"]').addClass('kb_error_field');
            $('input[name="push_notification[abandoned_cart][title]"]').after('<span class="kb_error_message">' + abandoned_cart_title_err + '</span>');
        }

        var abandoned_cart_message_err = velovalidation.checkMandatory($('textarea[name="push_notification[abandoned_cart][message]"]'));
        if (abandoned_cart_message_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('textarea[name="push_notification[abandoned_cart][message]"]').addClass('kb_error_field');
            $('textarea[name="push_notification[abandoned_cart][message]"]').after('<span class="kb_error_message">' + abandoned_cart_message_err + '</span>');
        }
//        var abandoned_cart_interval_err = velovalidation.isNumeric($('input[name="push_notification[abandoned_cart][interval]"]'), true);
//        if (abandoned_cart_interval_err != true)
//        {
//            is_error = true;
//            push_notification_error = true;
//            $('input[name="push_notification[abandoned_cart][interval]"]').addClass('kb_error_field');
//            $('input[name="push_notification[abandoned_cart][interval]"]').after('<span class="kb_error_message">' + abandoned_cart_interval_err + '</span>');
//        }
    }

        var abandoned_cart_interval_err = velovalidation.isNumeric($('input[name="push_notification[abandoned_cart][interval]"]'), true);
        if (abandoned_cart_interval_err != true)
        {
            is_error = true;
            push_notification_error = true;
            $('input[name="push_notification[abandoned_cart][interval]"]').addClass('kb_error_field');
            $('input[name="push_notification[abandoned_cart][interval]"]').after('<span class="kb_error_message">' + abandoned_cart_interval_err + '</span>');
        }

//    if ($('input[name="KB_MOBILE_APP_CHAT_SUPPORT"]:checked').val() == "1" ) {
//        var chat_api_key = velovalidation.checkMandatory($('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]'));
//        if (chat_api_key != true)
//        {
//            is_error = true;
//            general_setting_error = true;
//            $('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]').addClass('kb_error_field');
//            $('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]').after('<span class="kb_error_message">' + chat_api_key + '</span>');
//        }
//    }
    /*start:changes made by Aayushi on 1 dec 2018 for logo at navigation*/
    if (logo_navigation_file_error) {
        general_setting_error = true;
         is_error = true;
        $('input[name="filename"]').addClass('kb_error_field');
        return false;
    }
    /*end:changes made by Aayushi on 1 dec 2018 for logo at navigation*/
    /* CHANGES STARTED
     * @author : Rishabh Jain
     * DOM : 25/09/2018
     * to check whatsapp number is empty or not if enabled
     */
//    if ($('input[name="KB_MOBILE_WHATSAPP_CHAT_SUPPORT"]:checked').val() == "1") {
//        var chat_number = velovalidation.checkMandatory($('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]'));
//        if (chat_number != true)
//        {
//            is_error = true;
//            general_setting_error = true;
//            $('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]').addClass('kb_error_field');
//            $('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]').after('<span class="kb_error_message">' + chat_number + '</span>');
//        }
//    }
    if ($('input[name="KB_MOBILE_WHATSAPP_CHAT_SUPPORT"]:checked').val() == "1" && $('input[name="KB_MOBILE_APP_CHAT_SUPPORT"]:checked').val() == "1") {
       is_error = true;
       general_setting_error = true;
       $('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]').addClass('kb_error_field');
       $('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]').after('<span class="kb_error_message">' + one_chat_enabled + '</span>');
       $('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]').addClass('kb_error_field');
       $('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]').after('<span class="kb_error_message">' + one_chat_enabled + '</span>');
   } else {
       if ($('input[name="KB_MOBILE_WHATSAPP_CHAT_SUPPORT"]:checked').val() == "1") {
           var chat_number = velovalidation.checkMandatory($('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]'));
           if (chat_number != true)
           {
               is_error = true;
               general_setting_error = true;
               $('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]').addClass('kb_error_field');
               $('input[name="KB_MOBILE_WHATSAPP_CHAT_NUMBER"]').after('<span class="kb_error_message">' + chat_number + '</span>');
           }
       }
       if ($('input[name="KB_MOBILE_APP_CHAT_SUPPORT"]:checked').val() == "1") {
           var chat_api_key = velovalidation.checkMandatory($('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]'));
           if (chat_api_key != true)
           {
               is_error = true;
               general_setting_error = true;
               $('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]').addClass('kb_error_field');
               $('input[name="KB_MOBILE_APP_CHAT_SUPPORT_KEY"]').after('<span class="kb_error_message">' + chat_api_key + '</span>');
           }
       }

   }

    if (general_setting_error == true) {
        $('#link-GeneralSettings i').show();
    } else {
        $('#link-GeneralSettings i').hide();
    }

    if (push_notification_error == true) {
        $('#link-PushNotificationSettings i').show();
    } else {
        $('#link-PushNotificationSettings i').hide();
    }

    if (is_error) {
        return false;
    }

    /*Knowband button validation start*/
    $('.kb_general_setting_btn').attr('disabled', 'disabled');
    $('.kb_push_notification_btn').attr('disabled', 'disabled');
    $('.kb_payment_method_btn').attr('disabled', 'disabled');
    /*Knowband button validation end*/

    if ($(button_ele).hasClass('kb_general_setting_btn')) {
//        $('#general_form :input').not(':submit').clone().hide().appendTo('#general_form');
        $('#push_notification_settings_form :input').not(':submit').clone().hide().appendTo('#general_form');
        $('#general_form').submit();
    }
    if ($(button_ele).hasClass('kb_push_notification_btn')) {
        $('#general_form :input').not(':submit').clone().hide().appendTo('#push_notification_settings_form');
//        $('#push_notification_settings_form :input').not(':submit').clone().hide().appendTo('#push_notification_settings_form');
        $('#push_notification_settings_form').submit();
    }

}

function veloValidatePaymentForm(button_ele)
{
    var is_error = false;
    var payment_error = false;
    var error_msg = ''

    $('.kb_error_message').remove();
    $('select[name="payment_method"]').removeClass('kb_error_field');
    $('input[name="payment_method_name_' + default_language_code + '"]').removeClass('kb_error_field');
    $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').removeClass('kb_error_field');

    /*Knowband button validation start*/
    $('.kb_general_setting_btn').attr('disabled', 'disabled');
    $('.kb_push_notification_btn').attr('disabled', 'disabled');
    $('.kb_payment_method_btn').attr('disabled', 'disabled');
    /*Knowband button validation end*/

    var payment_method_code = $('#payment_method').val();
    var payment_method_name = $.trim($('#payment_method_name_' + default_language_id).val());
    var payment_method_client_id = $.trim($('#payment_method_client_id').val());

    if (payment_method_code == '0') {
        error_msg = select_methods_txt;
        $('select[name="payment_method"]').addClass('kb_error_field');
        $('select[name="payment_method"]').after('<span class="kb_error_message">' + select_methods_txt + '</span>');
        is_error = true;
    }

    if (payment_method_code == 'paypal') {
        if (payment_method_name == '') {

            error_msg = payment_name_txt + " " + default_language_code;
            $('input[name="payment_method_name_' + default_language_code + '"]').addClass('kb_error_field');
            $('input[name="payment_method_name_' + default_language_code + '"]').after('<span class="kb_error_message">' + error_msg + '</span>');
            is_error = true;
        }
        if (payment_method_client_id == '') {
            error_msg = client_id_txt;
            $('input[name="payment_method_client_id"]').addClass('kb_error_field');
            $('input[name="payment_method_client_id"]').after('<span class="kb_error_message">' + client_id_txt + '</span>');
            is_error = true;
        }
    }

    if (payment_method_code == 'cod') {
        if (payment_method_name == '') {
            error_msg = payment_name_txt + " " + default_language_code;
            $('input[name="payment_method_name_' + default_language_code + '"]').addClass('kb_error_field');
            $('input[name="payment_method_name_' + default_language_code + '"]').after('<span class="kb_error_message">' + error_msg + '</span>');
            is_error = true;
        }
    }

    if (is_error) {
        $('.kb_general_setting_btn').removeAttr('disabled');
        $('.kb_push_notification_btn').removeAttr('disabled');
        $('.kb_payment_method_btn').removeAttr('disabled');
        return false;
    }

    if ($(button_ele).hasClass('kb_payment_method_btn')) {
        $('#configuration_form').submit();
    }

}


function veloValidateBannerSliderForm(event)
{
    $('.kb_error_message').remove();
    $('select[name="image_type"]').removeClass('kb_error_field');
    $('input[name="image_url"]').removeClass('kb_error_field');
    $('select[name="redirect_activity"]').removeClass('kb_error_field');
    $('select[name="category_id"]').removeClass('kb_error_field');
    $('input[name="redirect_banner_product_name"]').removeClass('kb_error_field');
    $('#countdown_validity').removeClass('kb_error_field');
    $('input[name="timer_text_color"]').removeClass('kb_error_field');
    $('input[name="timer_background_color"]').removeClass('kb_error_field');
    $('input[name="filename"]').removeClass('kb_error_field');
    var slider_banner_error = false;

    var status = $('input[name="status"]:checked').val();
    var image_type = $.trim($('#image_type').val());
    var image_url = $.trim($('#image_url').val());
    var redirect_activity = $.trim($('#redirect_activity').val());
    var redirect_category_id = $.trim($('#category_id').val());
    var redirect_product_name = $.trim($('#redirect_banner_product_name').val());
    var redirect_product_id = $.trim($('#redirect_banner_product_id').val());
    var banner_image = $.trim($('#slideruploadedfile').val());
    var error_message = '';

    if (image_type == 'image' && slider_banner_file_error) {
        slider_banner_error = true;
        error_message = select_image_txt;
        $('input[name="filename"]').addClass('kb_error_field');
        $('input[name="filename"]').after('<span class="kb_error_message">' + error_message + '</span>');
    }
    if ($('#countdown_validity').is(":visible")) {
        if ($('#countdown_validity').val() == '') {
            slider_banner_error = true;
            error_message = select_image_txt;
            $('#countdown_validity').addClass('kb_error_field');
            $('#countdown_validity').after('<span class="kb_error_message">' + 'Select the Countdown Validity' + '</span>');

        }
    }


    if (image_type == '') {
        error_message = select_image_type_txt;
        slider_banner_error = true;
        $('select[name="image_type"]').addClass('kb_error_field');
        $('select[name="image_type"]').after('<span class="kb_error_message">' + select_image_type_txt + '</span>');
    }


    if (image_type == 'url' && image_url == '') {
        error_message = image_url_error_txt;
        slider_banner_error = true;
        $('input[name="image_url"]').addClass('kb_error_field');
        $('input[name="image_url"]').after('<span class="kb_error_message">' + image_url_error_txt + '</span>');
    }



    if (image_type == 'image' && slider_banner_file_error) {
        error_message = select_image_txt;
        slider_banner_error = true;
        $('input[name="filename"]').addClass('kb_error_field');
    }


    if (redirect_activity == 'category' && redirect_category_id == '0') {
        error_message = select_category_txt;
        slider_banner_error = true;
        $('select[name="category_id"]').addClass('kb_error_field');
        $('select[name="category_id"]').after('<span class="kb_error_message">' + select_category_txt + '</span>');

    }

    if (redirect_activity == 'product' && redirect_product_name == '') {
        error_message = provide_product_name_txt;
        slider_banner_error = true;
        $('input[name="redirect_product_name"]').addClass('kb_error_field');
        $('input[name="redirect_product_name"]').after('<span class="kb_error_message">' + provide_product_name_txt + '</span>');
    }

    if (slider_banner_error) {
        return false;
    }
    if ($('#countdown_validity').is(":visible")) {
        submitCountdownbannersliderform(event);
    } else {
        submitbannersliderform(event);
    }
    return false;
}


function sendNotification()
{

    $('.kb_error_message').remove();
    $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').removeClass('kb_error_field');
    $('input[name="push_notification_title"]').removeClass('kb_error_field');
    $('textarea[name="push_notification_message"]').removeClass('kb_error_field');
    $('select[name="push_notification_image_type"]').removeClass('kb_error_field');
    $('input[name="push_notification_image_url"]').removeClass('kb_error_field');
    $('input[name="filename"]').removeClass('kb_error_field');




    var notification_error = false;

    var firebase_key = $.trim($('#KB_MOBILEAPP_FIREBASE_KEY').val());
    var notification_title = $.trim($('#push_notification_title').val());
    var notification_message = $.trim($('#push_notification_message').val());
    var notification_image_type = $.trim($('#push_notification_image_type').val());
    var notification_image_url = $.trim($('#push_notification_image_url').val());
    var redirect_activity = $.trim($('#push_notification_redirect_type').val());
    var redirect_category_id = $.trim($('#push_notification_redirect_category_id').val());
    var redirect_product_name = $.trim($('#push_notification_redirect_product_name').val());
    var redirect_product_id = $.trim($('#push_notification_redirect_product_id').val());
    var notification_image = $.trim($('#uploadedfile').val());
    var error_message = '';

    if (notification_image_type == 'image' && file_error) {
        return
    }

    $('#notification_error').html('');

    if (firebase_key == '') {
        error_message = firebase_server_key_txt;
        notification_error = true;
        $('#notification_error').removeClass('kb-notification-success');
        $('#notification_error').addClass('kb-notification-error');
        $('#notification_error').html(error_message);
//        $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').addClass('kb_error_field');
//        $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').after('<span class="kb_error_message">' + firebase_server_key_txt + '</span>');

//        $('#notification_error').html(error_message);
//        return;
    }

    if (notification_title == '') {
        error_message = push_notification_title_txt;
        notification_error = true;
        $('input[name="push_notification_title"]').addClass('kb_error_field');
        $('input[name="push_notification_title"]').after('<span class="kb_error_message">' + push_notification_title_txt + '</span>');
//        $('#notification_error').html(error_message);
//        return;
    }

    if (notification_message == '') {
        error_message = push_notification_msg_txt;
        notification_error = true;
        $('textarea[name="push_notification_message"]').addClass('kb_error_field');
        $('textarea[name="push_notification_message"]').after('<span class="kb_error_message">' + push_notification_msg_txt + '</span>');
//        $('#notification_error').html(error_message);
//        return;
    }

    if (notification_image_type == '') {
        error_message = select_image_type_txt;
        notification_error = true;
        $('select[name="push_notification_image_type"]').addClass('kb_error_field');
        $('select[name="push_notification_image_type"]').after('<span class="kb_error_message">' + select_image_type_txt + '</span>');
//        $('#notification_error').html(error_message);
//        return;
    }


    if (notification_image_type == 'url' && notification_image_url == '') {
        error_message = image_url_error_txt;
        notification_error = true;
        $('input[name="push_notification_image_url"]').addClass('kb_error_field');
        $('input[name="push_notification_image_url"]').after('<span class="kb_error_message">' + image_url_error_txt + '</span>');
//        $('#notification_error').html(error_message);
//        return;
    }

    if (notification_image_type == 'image' && notification_image == '') {
        error_message = select_image_txt;
        notification_error = true;
        $('input[name="filename"]').addClass('kb_error_field');
//        $('input[name="filename"]').parent().parent('.form-group').append('<span class="kb_error_message">' + select_image_txt + '</span>');
//        $('#notification_error').html(error_message);
//        return;
    }


    if (redirect_activity == 'category' && redirect_category_id == '0') {
        error_message = select_category_txt;
        notification_error = true;
        $('select[name="push_notification_redirect_category_id"]').addClass('kb_error_field');
        $('select[name="push_notification_redirect_category_id"]').after('<span class="kb_error_message">' + select_category_txt + '</span>');

//        return;
    }

    if (redirect_activity == 'product' && redirect_product_name == '') {
        error_message = provide_product_name_txt;
        notification_error = true;
        $('input[name="push_notification_redirect_product_name"]').addClass('kb_error_field');
        $('input[name="push_notification_redirect_product_name"]').after('<span class="kb_error_message">' + provide_product_name_txt + '</span>');

//        return;
    }




    if (notification_error) {
        return false;
    }

    $('#notification-loader').css('display', 'inline-block');
    $('#notification_error').html('');

    $('#send_notification_btn').html('sending..');
    var form = new FormData(jQuery('#push_form')[0]);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&send_notification=true",
        type: 'POST',
        contentType: false,
        processData: false,
        data: form,
        dataType: 'json',
        success: function(json) {
            $('#notification-loader').css('display', 'none');
            $('#send_notification_btn').html(send_notification_txt);
            $('.kb_error_message').remove();
            $('input[name="KB_MOBILEAPP_FIREBASE_KEY"]').removeClass('kb_error_field');
            $('input[name="push_notification_title"]').removeClass('kb_error_field');
            $('textarea[name="push_notification_message"]').removeClass('kb_error_field');
            $('select[name="push_notification_image_type"]').removeClass('kb_error_field');
            $('input[name="push_notification_image_url"]').removeClass('kb_error_field');
            $('file[name="uploadedfile"]').removeClass('kb_error_field');
            if (!json.error) {
                $('#notification_error').removeClass('kb-notification-error');
                $('#notification_error').addClass('kb-notification-success');
                $('#push_notification_title').val('');
                $('#push_notification_message').val('');
                $('#push_notification_image_type').val('');
                $('#push_notification_image_url').val('');
                $('#uploadedfile').val('');
                // next line modified
                $('#uploadedfile').parent().parent().parent().parent().hide();
                // added this line
                $('#push_notification_image_url').parent().parent().hide();
                $('#uploadedfile').parent().hide();
                // modified next line
                $('#notificatonimage').attr('src', json.demo_image);
            } else {
                $('#notification_error').removeClass('kb-notification-success');
                $('#notification_error').addClass('kb-notification-error');

            }
            $('#notification_error').html(json.msg);

            setTimeout(function() {
                jQuery('#notification_error').html('');
                $('#notification_error').removeClass('kb-notification-success');
                $('#notification_error').removeClass('kb-notification-error');
            }
            , 3000);
        },
        error: function(request, status, error) {
            $('#notification_error').html(request_error_txt + '!!' + '  (' + error + ')');
        }
    });


}

function enable_disable(a) {
    var a = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
    $('.alert-success').parent().remove();
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&enable_disable=true",
        data: 'code=' + a,
        type: "post",
        success: function(data)
        {
            var a = JSON.parse(data);
            $('.row:first').prepend(a.msg);
            var vss_btn_html = $('#vss-button').html();
            $("#form-configuration").replaceWith(a.html);
            $('#form-configuration').addClass('col-lg-10 col-md-9');
            $("#form-configuration").css("float", "right");
            $("#form-configuration .panel h3").append("<div id='vss-button'>" + vss_btn_html + "</div>");
            $('#vss-button').show();
//           $("#configuration").replaceWith(a.html);
            $('.alert-success').not(':first').hide();
        }
    });
}
function showConfigurationForm() {
    $('#layout_add_edit_form').hide();
    $('#kbmobileapp_configuration_form').show();

}
function showNotificationDetails(a) {
    var a = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
//    var a = 1;
    $('.alert-success').parent().remove();
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&show_notification_details=true",
        data: 'notification_id=' + a,
        type: "post",
        success: function(data)
        {
            var html = data;
            $.fancybox.open(data);
//            $('.view_push_details').fancybox({
//            titlePosition: 'inside',
//            transitionIn: 'none',
//            transitionOut: 'none',
//            overlayShow: false,
//            fitToView: false,
//            width: '100%',
//            height: '100%',
//            maxWidth: '600',
//            maxHeight: '350',
//            autoSize: false,
//            hideOnContentClick: false,
//            beforeLoad: function () {
//                var html = data;
//                $('div#config_chart_preview_link_modal #content').html(html);
//                $('div#config_chart_preview_link_modal').show();
//            },
//            beforeClose: function () {
//                $('div#config_chart_preview_link_modal').hide();
//            }
//        });
//            var a = JSON.parse(data);
//            $('.row:first').prepend(a.msg);
//            var vss_btn_html = $('#vss-button').html();
//            $("#form-configuration").replaceWith(a.html);
//            $('#form-configuration').addClass('col-lg-10 col-md-9');
//            $("#form-configuration").css("float", "right");
//            $("#form-configuration .panel h3").append("<div id='vss-button'>" + vss_btn_html + "</div>");
//            $('#vss-button').show();
////           $("#configuration").replaceWith(a.html);
//            $('.alert-success').not(':first').hide();
        }
    });
}

function delete_confirmation(a)
{
    if (confirm(confirmation_txt + '?'))
    {
        $('.alert-success').parent().remove();
        $(a).closest('tr').remove();
        var code = $(a).closest('tr').find('.td-vss-code').html();
        code = $.trim(code);
        $.ajax({
            url: ajaxaction + "&configure=kbmobileapp&delete=true" + "&code=" + code,
            type: "post",
            dataType: "text",
            success: function(data) {
                var a = JSON.parse(data);
                $("#configuration_form").replaceWith(a.html);
                $('#configuration_form').addClass('col-lg-10 col-md-9');
                $("#payment_method").bind('change', function() {
                    var payment_method_name = $('#payment_method_name_' + default_language_id).val();
                    payment_method_name = $.trim(payment_method_name);
//               $('#payment_method_name').val($(this).val());
                    $('#payment_method_name_' + default_language_id).val($("#payment_method :selected").text());
                    if ($(this).val() == 'paypal') {
                        $('#payment_method_name_' + default_language_id).parents('.form-group').show();
                        $('#payment_method_client_id').parent().parent().show();
                        $('#payment_method_mode').parent().parent().show();
                        $('#payment_method_other_info').parent().parent().hide();
                    } else if ($(this).val() == 'cod') {
                        $('#payment_method_name_' + default_language_id).parents('.form-group').show();
                        $('#payment_method_client_id').parent().parent().hide();
                        $('#payment_method_mode').parent().parent().hide();
                        $('#payment_method_other_info').parent().parent().hide();
                    } else {
                        $('#payment_method_name_' + default_language_id).parents('.form-group').hide();
                        $('#payment_method_client_id').parent().parent().hide();
                        $('#payment_method_mode').parent().parent().hide();
                        $('#payment_method_other_info').parent().parent().hide();
                    }
                });
                $('.kb_payment_method_btn').bind('click', function() {
                    return veloValidatePaymentForm(this);
                });
                $('.row:first').prepend(a.msg);
                $('.alert-success').not(':first').hide();
            }
        });
    }
    else
    {
        return false;
    }
}

function edit_row(a)
{
    var a = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
    var option_name = '';
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&edit=true",
        data: 'code=' + a,
        type: "post",
        success: function(data)
        {

            var b = JSON.parse(data);
            if (b.msg == 'success') {
                $('#payment_method_client_id').val('');
                $('#payment_method_other_info').val('');
                $('#payment_method_name_' + default_language_id).parents('.form-group').hide();
                $('#payment_method_client_id').parent().parent().hide();
                $('#payment_method_other_info').parent().parent().hide();
                $('#payment_method_mode').val('live');
                if (a == 'cod') {
                    option_name = cod_txt;
                    $('.paypal-payment-info').hide();
                } else if (a == 'paypal') {
                    option_name = paypal_txt;
                    $('.paypal-payment-info').show();
                }
                if (!optionExists(a, document.getElementById('payment_method'))) {
                    $("#payment_method").append('<option value="' + a + '">' + option_name + '</option>');
                }
                $('#payment_method').val(a);

                if ($.trim(b.client_id) != '') {
                    $('#payment_method_client_id').val(b.client_id);
                    $('#payment_method_client_id').parent().parent().show();
                }
                if ($.trim(b.other_info) != '') {
                    $('#payment_method_other_info').val(b.client_id);
                    $('#payment_method_other_info').parent().parent().show();
                }
                if (a == 'paypal') {
                    $('#payment_method_mode').val(b.payment_mode);
                    $('#payment_method_mode').parent().parent().show();
                }

                if (a == 'cod') {
                    $('#payment_method_mode').parent().parent().hide();
                }

                for (var key in b.payment_name) {
                    $('#payment_method_name_' + key).val(b.payment_name[key]);
                }
                $('#payment_method_name_' + default_language_id).parents('.form-group').show();

                $('.vss-add-new').slideDown("fast", function() {
                    $('#add_new').html(close_new_entry);
                    $('#add_new').attr('onclick', 'closePaymentMethod()');
                    $('#form-configuration .panel h3').remove("#vss-button");
                    $('#vss-button').appendTo($("#configuration_form .panel-heading"));
                });
            }
        }
    });
}

function editSlider(a)
{
    var a = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
//    var a = 1;
    var option_name = '';
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&editSlider=true",
        data: 'id_slider=' + a,
        type: "post",
        success: function(data)
        {

            var b = JSON.parse(data);
//            b.msg == 'success'
            if (1) {

                if ($.trim(b.status) != '') {
                    if (b.status == 1) {
                        $("input[name=status][value='1']").prop("checked", true);
                    } else if (b.status == 0) {
                        $("input[name=status][value='0']").prop("checked", true);
                    }
                }

                $('#kb_banner_slider_type').val(b.type);
                $('#kb_banner_id').val(b.kb_banner_id);


                if ($.trim(b.image_type) != '') {
                    $('#image_type').val(b.image_type);
                    if ($.trim(b.image_type) == 'url'){
                        $('#sliderimage').attr('src', b.image_url);
                        $('#image_url').val(b.image_url);
                        $('#image_url').parent().parent().show();
                        $('#slideruploadedfile').parent().parent().parent().parent().show();
                        $('#slideruploadedfile').parent().hide();
                    }
                    if ($.trim(b.image_type) == 'image'){
                        $('#sliderimage').attr('src', b.image_url);
                        $('#image_url').val(b.image_url);
                        $('#image_url').parent().parent().hide();
                        $('#slideruploadedfile').parent().parent().parent().parent().show();
                        $('#slideruploadedfile').parent().show();
                    }
                }

                if ($.trim(b.redirect_activity) == 'category') {
                    $('#redirect_activity').val(b.redirect_activity);
                    $('#category_id').val(b.category_id);
                    $('#category_id').parent().parent().show();
                    $('#redirect_product_name').parent().parent().hide();
                } else if ($.trim(b.redirect_activity) == 'product') {
                    $('#redirect_activity').val(b.redirect_activity);
                    $('#redirect_product_id').val(b.product_id);
                    $('#redirect_product_name').val(b.product_name);
                    $('#redirect_product_name').parent().parent().show();
                    $('#category_id').parent().parent().hide();
                } else {
                    $('#redirect_activity').val(b.redirect_activity);
                    $('#redirect_product_name').parent().parent().hide();
                    $('#category_id').parent().parent().hide();
                }


                $('.vss-add-slider').slideDown("fast", function() {
//                    $('#add_new').html(close_new_entry);
//                    $('#add_new').attr('onclick', 'closePaymentMethod()');
//                    $('#form-configuration .panel h3').remove("#vss-button");
//                    $('#vss-button').appendTo($("#configuration_form .panel-heading"));
                });
            }
        }
    });
}

function hideSliderForm() {
    $('.vss-add-slider').hide();
}

function optionExists(needle, haystack)
{
    var optionExists = false,
            optionsLength = haystack.length;

    while (optionsLength--)
    {
        if (haystack.options[ optionsLength ].value === needle)
        {
            optionExists = true;
            break;
        }
    }
    return optionExists;
}

function getpaymentForm() {
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&payment_form=true",
        type: "post",
        dataType: "text",
        async: false,
        success: function(data) {
            var a = JSON.parse(data);
            $("#configuration_form").replaceWith(a.html);
            $('#configuration_form').addClass('col-lg-10 col-md-9');
            $('.paypal-payment-info').hide();
            $("#payment_method").bind('change', function() {
                var payment_method_name = $('#payment_method_name_' + default_language_id).val();
                payment_method_name = $.trim(payment_method_name);
//               $('#payment_method_name').val($(this).val());
                $('#payment_method_name_' + default_language_id).val($("#payment_method :selected").text());
                if ($(this).val() == 'paypal') {
                    $('#payment_method_name_' + default_language_id).parents('.form-group').show();
                    $('#payment_method_client_id').parent().parent().show();
                    $('#payment_method_other_info').parent().parent().hide();
                    $('#payment_method_mode').parent().parent().show();
                    $('.paypal-payment-info').show();
                } else if ($(this).val() == 'cod') {
                    $('#payment_method_name_' + default_language_id).parents('.form-group').show();
                    $('#payment_method_client_id').parent().parent().hide();
                    $('#payment_method_other_info').parent().parent().hide();
                    $('#payment_method_mode').parent().parent().hide();
                    $('.paypal-payment-info').hide();
                } else {
                    $('#payment_method_name_' + default_language_id).parents('.form-group').hide();
                    $('#payment_method_client_id').parent().parent().hide();
                    $('#payment_method_other_info').parent().parent().hide();
                    $('#payment_method_mode').parent().parent().hide();
                    $('.paypal-payment-info').hide();
                }
            });
            $('.kb_payment_method_btn').bind('click', function() {
                return veloValidatePaymentForm(this);
            });
        }
    });
}

function changeSliderStatus(a) {

    var id = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());

    var status = '';
    if($(a).hasClass('action-enabled')) {
        status = 0;
    } else if ($(a).hasClass('action-disabled')) {
        status = 1;
    }

//
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&change_slider_banner_status=true",
        data: 'id_slider=' + id+'&status='+status,
        type: "post",
        success: function(data)
        {
            if (status == 0) {
                $(a).removeClass('action-enabled');
                $(a).addClass('action-disabled');
                $(a).children('.icon-check').addClass('hidden');
                $(a).children('.icon-remove').removeClass('hidden');
            }
            if (status == 1) {
                $(a).removeClass('action-disabled');
                $(a).addClass('action-enabled');
                $(a).children('.icon-check').removeClass('hidden');
                $(a).children('.icon-remove').addClass('hidden');
            }
        }
    });
}


function veloValidateGoogleSetupForm(button_ele)
{

    $('.kb_error_message').remove();
    $('#googlejsonfile-name').removeClass('kb_error_field');

    var google_error = false;
    var google_file = $.trim($('#googlejsonfile').val());
    var uploadedfile = $.trim($('#googlefilename').val());

    var status = $('input[name="google_status"]:checked').val();
    var error_message = '';

    if (status == 1) {
        if (google_file == '' && uploadedfile == '') {
            google_error = true;
            error_message = select_file_txt;
            $('#googlejsonfile-name').addClass('kb_error_field');
            $('#googlejsonfile').parent().append('<span class="kb_error_message">' + error_message + '</span>');
            return false;
        }
    }

    if (google_file_error) {
        google_error = true;
        error_message = invalid_file_txt;
        $('#googlejsonfile-name').addClass('kb_error_field');
        $('#googlejsonfile').parent().append('<span class="kb_error_message">' + error_message + '</span>');
        return false;
    }

    if (google_error) {
        return false;
    }

    $('#google_setup_form').submit();
}


function veloValidateFacebookSetupForm(button_ele)
{

    $('.kb_error_message').remove();
    $('#facebook_app_error').removeClass('kb-fb-success');
    $('#facebook_app_error').removeClass('kb-fb-error');
    $("#facebook_app_error").html('');


    var status = $('input[name="facebook_setup_status"]:checked').val();

    if (status == 1) {
        validateFBKey(true);
        return false;
    } else {
        $('#facebook_setup_form').submit();
    }

}

// changes by rishabh jain

function delete_confirmation_layout(a)
{
    if (confirm(confirmation_txt + '?'))
    {
        $('.alert-success').parent().remove();
        $(a).closest('tr').remove();
        var code = $(a).closest('tr').find('.td-vss-code').html();
        code = $.trim(code);
        $.ajax({
            url: ajaxaction + "&configure=kbmobileapp&delete_layout=true" + "&code=" + code,
            type: "post",
            dataType: "text",
            success: function (data) {
                var b = JSON.parse(data);
                if (1) {
                    $('#layout_list').html('');
                    $('#layout_list').append(b.html);
                    $("#layout_list .panel .panel-heading").append(b.button);
                    $('#KBMOBILEAPP_HOME_PAGE_LAYOUT').html('');
                    $('#KBMOBILEAPP_HOME_PAGE_LAYOUT').append(b.layout_select_options);
                    $('#form-kb_layouts_list').addClass('col-lg-10 col-md-9');
                    $("#form-kb_layouts_list").css("float", "right");
                    showSuccessMessage(Layout_delete_message);
                }
            }
        });
    }
    else
    {
        return false;
    }
}

function validateFBKey(submitform) {
        var key = $("#facebook_setup_app_id").val();
        console.log(key);
        $('#facebook_app_error').html();
        $('#facebook_app_error').removeClass('kb-fb-success');
        $('#facebook_app_error').removeClass('kb-fb-error');
        $("#facebook_app_error").html(validating_key_txt);

        $.ajax({
            url: ajaxaction + "&configure=kbmobileapp&validatefbkey=true",
            data: 'key=' + key,
            type: "post",
            async: "false",
            success: function(data)
            {
                if ($.trim(data) == 'false') {
                    $('#facebook_app_error').removeClass('kb-fb-success');
                    $('#facebook_app_error').addClass('kb-fb-error');
                    $("#facebook_app_error").html(invalid_key_txt);
                    if (submitform) {
                        return false;
                    }
                } else {
                    $('#facebook_app_error').addClass('kb-fb-success');
                    $('#facebook_app_error').removeClass('kb-fb-error');
                    $("#facebook_app_error").html(valid_key_txt);
                    if (submitform) {
                        $('#facebook_setup_form').submit();
                    }
                }
            }
        });

    }
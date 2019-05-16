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
var num_of_component = 0;
$(document).ready(function () {
    $('#top_category').click(function () {
        addTopCategory(0);
    });
    $('#banner_square').click(function () {
        addBannerSquare(0);
    });
    $('#banner_HS').click(function () {
        addBannerHorizontalslide(0);
    });
    $('#banner_grid').click(function () {
        addBannergrid(0);
    });
    $('#banner_countdown').click(function () {
        addBannerCountdown(0);
    });
    $('#product_square').click(function () {
        addProductSquare(0);
    });
    $('#product_HS').click(function () {
        addProductHorizontalslide(0);
    });
    $('#product_grid').click(function () {
        addProductGrid(0);
    });
    $('#product_LA').click(function () {
        addLastAccessed(0);
    });

});

function addTopCategory(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var top_category_html = $('.top_category').html();
        var id_layout = $('#id_layout').val();
        top_category_html = top_category_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        top_category_html = top_category_html.replace(/top_category_edit_component/g, 'edit_' + id);
        top_category_html = top_category_html.replace(/top_category_delete_component/g, 'delete_' + id);
        $('.slides').append(top_category_html);
        preview_content();
        scrollToBottom();
    } else {
        var a = "top_category";
        var id_layout = $('#id_layout').val();
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var top_category_html = $('.top_category').html();
                        var id_layout = $('#id_layout').val();
                        top_category_html = top_category_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        top_category_html = top_category_html.replace(/top_category_edit_component/g, 'edit_' + id);
                        top_category_html = top_category_html.replace(/top_category_delete_component/g, 'delete_' + id);
                        $('.slides').append(top_category_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);
                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}

function showHideImageType(a) {
    //$("#image_type").on('change', function () {
    if ($(a).val() == 'url') {
        $('#image_url').parent().parent().show();
        $('#slideruploadedfile').parent().parent().parent().parent().show();
        $('#slideruploadedfile').parent().hide();
    } else if ($(a).val() == 'image') {
        $('#image_url').parent().parent().hide();
        $('#slideruploadedfile').parent().parent().parent().parent().show();
        $('#slideruploadedfile').parent().show();
    } else {
        $('#image_url').parent().parent().hide();
        $('#slideruploadedfile').parent().parent().parent().parent().hide();
    }
    //});

}
function getCategoryproducts(a) {
    var id_category = $('#category_id').val();
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getCategoryProducts=true",
        data: 'id_category=' + id_category,
        type: "post",
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#category_products').html('');
                $('#category_products').append(b.category_product_options);
            }
        }
    });

}
function showHideProductType(a) {
    if ($('#product_type').val() == 'category_products') {
        $('#product_list').closest('.form-group').hide();
        $('#category_id').closest('.form-group').show();
        $('#category_products').closest('.form-group').show();

    } else if ($('#product_type').val() == 'custom_products') {
        $('#product_list').closest('.form-group').show();
        $('#category_products').closest('.form-group').hide();
        $('#category_id').closest('.form-group').hide();
    } else {
        $('#product_list').closest('.form-group').hide();
        $('#category_products').closest('.form-group').hide();
        $('#category_id').closest('.form-group').hide();
    }

}
function showUrlImage() {
//    $('.kb_error_message').remove();
//    $('input[name="image_url"]').removeClass('kb_error_field');
//    if ($('#image_url').val() != '') {
//        var image_url_err = velovalidation.checkUrl($('input[name="image_url"]'));
//        $('#sliderimage').attr('src', $('#image_url').val());
//    }
    $("#image_url").on('blur', function () {
        $('.kb_error_message').remove();
        $('input[name="image_url"]').removeClass('kb_error_field');
        if ($('#image_url').val() != '') {
            var image_url_err = velovalidation.checkUrl($('input[name="image_url"]'));
            $('#sliderimage').attr('src', $('#image_url').val());
            $('#sliderimage').show();
        }
    });

}
function showuploadedimage() {
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
            $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
            slider_banner_file_error = true;

        } else if (files.size > default_file_size) {
            $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
            slider_banner_file_error = true;
        } else {
            slider_banner_file_error = false;
            if (typeof (FileReader) != "undefined") {

                var image_holder = $("#sliderimage");

                image_holder.empty();

                var reader = new FileReader();
                reader.onload = function (e) {
                    $('#sliderimage').attr('src', e.target.result);
                    $('#sliderimage').show();
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
}
function showHideRedirectType(a) {
    if ($(a).val() == 'category') {
        $('#category_id').parent().parent().show();
        $('#redirect_banner_product_name').parent().parent().hide();
    } else if ($(a).val() == 'product') {
        $('#category_id').parent().parent().hide();
        $('#redirect_banner_product_name').parent().parent().show();
    } else {
        $('#category_id').parent().parent().hide();
        $('#redirect_banner_product_name').parent().parent().hide();
    }

}
function addBannerSquare(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var banner_square_html = $('.banner-slide').html();
        var id_layout = $('#id_layout').val();
        banner_square_html = banner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        banner_square_html = banner_square_html.replace(/banner_square_edit_component/g, 'edit_' + id);
        banner_square_html = banner_square_html.replace(/banner_square_delete_component/g, 'delete_' + id);
        $('.slides').append(banner_square_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();
        var a = "banner_square";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var banner_square_html = $('.banner-slide').html();
                        var id_layout = $('#id_layout').val();
                        banner_square_html = banner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        banner_square_html = banner_square_html.replace(/banner_square_edit_component/g, 'edit_' + id);
                        banner_square_html = banner_square_html.replace(/banner_square_delete_component/g, 'delete_' + id);
                        $('.slides').append(banner_square_html);
                        preview_content();
                        scrollToBottom();

                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}
function addBannerHorizontalslide(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var Hbanner_square_html = $('.Hbanner-slide').html();
        var id_layout = $('#id_layout').val();
        Hbanner_square_html = Hbanner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component/g, 'edit_' + id);
        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_delete_component/g, 'delete_' + id);
        $('.slides').append(Hbanner_square_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "banner_horizontal_slider";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var Hbanner_square_html = $('.Hbanner-slide').html();
                        var id_layout = $('#id_layout').val();
                        Hbanner_square_html = Hbanner_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_edit_component/g, 'edit_' + id);
                        Hbanner_square_html = Hbanner_square_html.replace(/banner_horizontal_delete_component/g, 'delete_' + id);
                        $('.slides').append(Hbanner_square_html);
                        preview_content();
                        scrollToBottom();

                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}
function addBannergrid(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var banner_Grid_html = $('.banner-grid').html();
        var id_layout = $('#id_layout').val();
        banner_Grid_html = banner_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component/g, 'edit_' + id);
        banner_Grid_html = banner_Grid_html.replace(/banner_grid_delete_component/g, 'delete_' + id);
        $('.slides').append(banner_Grid_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "banners_grid";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var banner_Grid_html = $('.banner-grid').html();
                        var id_layout = $('#id_layout').val();
                        banner_Grid_html = banner_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        banner_Grid_html = banner_Grid_html.replace(/banner_grid_edit_component/g, 'edit_' + id);
                        banner_Grid_html = banner_Grid_html.replace(/banner_grid_delete_component/g, 'delete_' + id);
                        $('.slides').append(banner_Grid_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);

                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }

}
function addBannerCountdown(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var banner_countdown_html = $('.banner-countdown').html();
        var id_layout = $('#id_layout').val();
        banner_countdown_html = banner_countdown_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component/g, 'edit_' + id);
        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_delete_component/g, 'delete_' + id);
        $('.slides').append(banner_countdown_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "banners_countdown";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var banner_countdown_html = $('.banner-countdown').html();
                        var id_layout = $('#id_layout').val();
                        banner_countdown_html = banner_countdown_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_edit_component/g, 'edit_' + id);
                        banner_countdown_html = banner_countdown_html.replace(/banner_countdown_delete_component/g, 'delete_' + id);
                        $('.slides').append(banner_countdown_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);
                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}
function addProductSquare(id) {

    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var product_square_html = $('.product-square').html();
        var id_layout = $('#id_layout').val();
        product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);
        product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);
        $('.slides').append(product_square_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "products_square";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var product_square_html = $('.product-square').html();
                        var id_layout = $('#id_layout').val();
                        product_square_html = product_square_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        product_square_html = product_square_html.replace(/product_square_edit_component/g, 'edit_' + id);
                        product_square_html = product_square_html.replace(/product_square_delete_component/g, 'delete_' + id);
                        $('.slides').append(product_square_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);
                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}
function addProductHorizontalslide(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var Hproduct_slide_html = $('.Hproduct-slide').html();
        var id_layout = $('#id_layout').val();
        Hproduct_slide_html = Hproduct_slide_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component/g, 'edit_' + id);
        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_delete_component/g, 'delete_' + id);
        $('.slides').append(Hproduct_slide_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "products_horizontal";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var Hproduct_slide_html = $('.Hproduct-slide').html();
                        var id_layout = $('#id_layout').val();
                        Hproduct_slide_html = Hproduct_slide_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_edit_component/g, 'edit_' + id);
                        Hproduct_slide_html = Hproduct_slide_html.replace(/product_horizontal_delete_component/g, 'delete_' + id);
                        $('.slides').append(Hproduct_slide_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);
                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}
function addProductGrid(id) {


    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var product_Grid_html = $('.product-grid').html();
        var id_layout = $('#id_layout').val();
        product_Grid_html = product_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        product_Grid_html = product_Grid_html.replace(/product_grid_edit_component/g, 'edit_' + id);
        product_Grid_html = product_Grid_html.replace(/product_grid_delete_component/g, 'delete_' + id);
        $('.slides').append(product_Grid_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "products_grid";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var product_Grid_html = $('.product-grid').html();
                        var id_layout = $('#id_layout').val();
                        product_Grid_html = product_Grid_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        product_Grid_html = product_Grid_html.replace(/product_grid_edit_component/g, 'edit_' + id);
                        product_Grid_html = product_Grid_html.replace(/product_grid_delete_component/g, 'delete_' + id);
                        $('.slides').append(product_Grid_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);
                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }
}
function addLastAccessed(id) {
    if (id) {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        $('#number_of_component').val(num_of_component);
        var last_accessed_html = $('.product-lastAccess').html();
        var id_layout = $('#id_layout').val();
        last_accessed_html = last_accessed_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
        last_accessed_html = last_accessed_html.replace(/last_access_delete_component/g, 'delete_' + id);
        $('.slides').append(last_accessed_html);
        preview_content();
        scrollToBottom();
    } else {
        num_of_component = parseInt($('#number_of_component').val());
        num_of_component = num_of_component + 1;
        var id_layout = $('#id_layout').val();

        var a = "products_recent";
        if (num_of_component <= 20) {
            $('#number_of_component').val(num_of_component);
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&assign_component_id=true",
                data: 'component_type=' + a + '&id_layout=' + id_layout,
                type: "post",
                success: function (data)
                {
                    var id = data;
                    if (id) {
                        var last_accessed_html = $('.product-lastAccess').html();
                        var id_layout = $('#id_layout').val();
                        last_accessed_html = last_accessed_html.replace(/component_position/g, 'layout_component_' + id_layout + '_' + id);
                        last_accessed_html = last_accessed_html.replace(/last_access_delete_component/g, 'delete_' + id);
                        $('.slides').append(last_accessed_html);
                        preview_content();
                        scrollToBottom();
                        showSuccessMessage(component_add);
                    }
                }
            });

        } else {
            showErrorMessage(limit_reached);
        }
    }

}
function settingFunction(setting) {
    //$(setting).next('.file-uploader').slideToggle();
}
function trashFunction(trash) {
    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    $(trash).parents('.slide').remove();
    preview_content();
}
function preview_content() {
    $('.iframe_html').html('');
    var Display_content = $('.slides').html();
    //alert(Display_content);
    $('.iframe_html').append(Display_content);
}


function scrollToBottom() {

    var content = jQuery(".layout_gallery"), autoScrollTimer = 200, autoScrollTimerAdjust, autoScroll;

    content.mCustomScrollbar({
        scrollButtons: {
            enable: true
        },
        theme: "dark",
        callbacks: {
            whileScrolling: function () {
                autoScrollTimerAdjust = autoScrollTimer * this.mcs.topPct / 100;
                privateTop = this.mcs.topPct;
                if (privateTop >= 90) {
                    jQuery('.goToLastMessage').hide();
                    count = 0;
                }

            },
            onScroll: function () {
                if (jQuery(this).data("mCS").trigger === "internal") {
                    AutoScrollOff();

                }
            }
        }
    });

    content.addClass("auto-scrolling-on auto-scrolling-to-bottom");
    AutoScrollOn("bottom");

    function AutoScrollOn(to, timer) {

        if (!timer) {
            timer = autoScrollTimer;
        }
        content.addClass("auto-scrolling-on").mCustomScrollbar("scrollTo", to, {
            scrollInertia: timer,
            scrollEasing: "easeInOutSmooth"
        });

    }
    function AutoScrollOff() {
        clearTimeout(autoScroll);
        content.removeClass("auto-scrolling-on").mCustomScrollbar("stop");
    }

}
function addLayoutComponents(b) {
    for (i = 0; i < b.length; i++) {
        if (b[i]['type'] == 'banner_square') {
            addBannerSquare(b[i]['id']);
        } else if (b[i]['type'] == 'top_category') {
            addTopCategory(b[i]['id']);
        } else if (b[i]['type'] == 'banners_countdown') {
            addBannerCountdown(b[i]['id']);
        } else if (b[i]['type'] == 'products_square') {
            addProductSquare(b[i]['id']);
        } else if (b[i]['type'] == 'products_grid') {
            addProductGrid(b[i]['id']);
        } else if (b[i]['type'] == 'products_recent') {
            addLastAccessed(b[i]['id']);
        } else if (b[i]['type'] == 'banners_grid') {
            addBannergrid(b[i]['id']);
        } else if (b[i]['type'] == 'banner_horizontal_slider') {
            addBannerHorizontalslide(b[i]['id']);
        } else if (b[i]['type'] == 'products_horizontal') {
            addProductHorizontalslide(b[i]['id']);
        }
    }
}
function editLayout(a)
{
    $('.slides').empty();
    var id_layout = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getlayoutComponent=true",
        data: 'id_layout=' + id_layout,
        type: "post",
        success: function (data)
        {
            var b = JSON.parse(data);
            $('#id_layout').val(id_layout);
            if (b.length > 0) {
                $('.slides').html('');
                $('.iframe_html').html('');
                addLayoutComponents(b);
            } else {
                $('.slides').html('');
                $('.iframe_html').html('');
            }
            $('#kbmobileapp_configuration_form').hide();
            $('.layout_add_edit_form').slideDown("fast", function () {
                $('#id_layout').val(id_layout);
                $('#add_new').html(add_new_entry);
                $('#add_new').attr('onclick', 'closelayoutForm()');
                $('#form-configuration .panel h3').remove("#vss-button");
                $('#vss-button').appendTo($("#configuration_form .panel-heading"));
            });
            $(".slides").sortable();
        }
    });
    showUrlImage();
    //getproductdata();
}

function autoCompleteProduct() {
    $('#redirect_banner_product_name').autocomplete(ajaxaction + '&configure=kbmobileapp&ajaxproductaction=true', {
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
        formatItem: function (item) {
            return item[1] + ' - ' + item[0];
        },
        extraParams: {
            excludeIds: '',
            excludeVirtuals: '',
            exclude_packs: ''
        }
    }).result(function (event, item) {
        $('#redirect_banner_product_id').val(item[1]);
        $('#redirect_banner_product_name').val(item[0]);
    });
}

function countdownbannerDatepicker() {
    $('.datetimepicker').click(function () {
        $('.ui-datepicker').css('z-index', '99999999');
    });
}
function editBannerSquareComponentFunction(a)
{
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getBannerForm=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
            $('#kbGDPRDialogueModel .modal-body').html('');
            $('#kbGDPRDialogueModel').modal({
                show: 'true',
            });
        },
        success: function (data)
        {
            var b = JSON.parse(data);

            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $('#kbGDPRDialogueModel').modal({
                    show: 'true',
                });
                $('#sliderimage').hide();
                $('#category_id').parent().parent().hide();
                $('#redirect_banner_product_name').parent().parent().hide();
                $('#is_enabled_background_color_on').parent().closest('.form-group').hide();
                $('#countdown_validity').closest('.form-group').hide();
                $('.kbsw_wheel_color').closest('.form-group').parents('.form-group').hide();
                $('#image_url').parent().parent().hide();
                $('#slideruploadedfile').parent().parent().parent().parent().hide();
                showUrlImage();
                uploadfile();
                autoCompleteProduct();
                setDate();
                setColor();
            }
        }
    });


}
function showHidebackgroundColor() {
    if ($('input[name="is_enabled_background_color"]:checked').val() == "1") {
        $('input[name="timer_background_color"]').parent().parent().show();
    } else {
        $('input[name="timer_background_color"]').parent().parent().hide();
    }
}
function editBannerCountdownComponentFunction(a)
{
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getBannerForm=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
            $('#kbGDPRDialogueModel .modal-body').html('');
            $('#kbGDPRDialogueModel').modal({
                show: 'true',
            });

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $('#kbGDPRDialogueModel').modal({
                    show: 'true',
                });
                $('#sliderimage').hide();
                $('#category_id').parent().parent().hide();
                $('#redirect_banner_product_name').parent().parent().hide();
                $('#image_url').parent().parent().hide();
                $('#slideruploadedfile').parent().parent().parent().parent().hide();
                showUrlImage();
                uploadfile();
                countdownbannerDatepicker();
                //showHidebackgroundColor();
                autoCompleteProduct();
                setDate();
                setColor();
            }
        }
    });
    //showHidebackgroundColor();


}
function editLayoutName(a) {
    var id_layout = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getlayoutNameForm=true",
        data: 'id_layout=' + id_layout,
        type: "post",
        beforeSend: function () {
            $('#kbGDPRDialogueModel .modal-body').html('');
            $('#layoutNameModel').modal({
                show: 'true',
            });
            $('.modal-layout-body').html('<img id="loader_module_list" style="text-align: center;width:50px;height:50px;align:center" src=" ' + loader_url + '" alt="" border="0" />');

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            $('#id_layout').val(id_layout);
            $('.modal-layout-body').html('');
            $('.modal-layout-body').append(b.html);

        },
        complete: function () {
            //$('#kbsw_show_loader').hide();
        }
    });
}
function addNewLayout(a) {
    var id_layout = 0;
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getlayoutNameForm=true",
        data: 'id_layout=' + id_layout,
        type: "post",
        beforeSend: function () {
            $('#kbGDPRDialogueModel .modal-body').html('');
            $('#layoutNameModel').modal({
                show: 'true',
            });
            $('.modal-layout-body').html('<img id="loader_module_list" style="text-align: center;width:50px;height:50px;align:center" src=" ' + loader_url + '" alt="" border="0" />');

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            $('.modal-layout-body').html('');
            $('.modal-layout-body').append(b.html);
//            $('#layoutNameModel').modal({
//                show: 'true',
//            });
        }
    });
}
function saveLayoutData(a) {
    var id_layout = $('#layout_id').val();
    var layout_name = $('#layout_title').val();
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&savelayoutNameForm=true",
        data: 'id_layout=' + id_layout + '&layout_name=' + layout_name,
        type: "post",
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                if (id_layout == 0) {
                    showSuccessMessage(layout_add_message);
                } else {
                    showSuccessMessage(layout_name_update_message);
                }
                $('#layout_list').html('');
                $('#layout_list').append(b.html);
                $("#layout_list .panel .panel-heading").append(b.button);
                $('#KBMOBILEAPP_HOME_PAGE_LAYOUT').html('');
                $('#KBMOBILEAPP_HOME_PAGE_LAYOUT').append(b.layout_select_options);
                /* changes by rishabh jain
                 * for layput tab
                 */
                $('#form-kb_layouts_list').addClass('col-lg-10 col-md-9');
                $("#form-kb_layouts_list").css("float", "right");
                $('#layoutNameModel').modal('hide');
                /* changes over */
            }
            return false;
        }
    });
    return false;
}
function veloValidateProductForm(a) {
    $('.kb_error_message').remove();
    $('input[name="number_of_products"]').removeClass('kb_error_field');
    $('select[name="category_id"]').removeClass('kb_error_field');
    $('select[name="product_list"]').removeClass('kb_error_field');
    $('select[name="category_products"]').removeClass('kb_error_field');
    var product_form_error = false;


    var number_of_product = $.trim($('#number_of_products').val());
    var category_id = $.trim($('#category_id').val());
    var product_list = $.trim($('#product_list').val());
    var category_products = $.trim($('#category_products').val());
    var product_type = $('#product_type').val();
    var error_message = '';

    if (product_type == 'category_products') {
        if (category_id == 0) {
            product_form_error = true;
            error_message = select_image_txt;
            $('#category_id').addClass('kb_error_field');
            $('#category_id').after('<span class="kb_error_message">' + error_message + '</span>');
        }
    } else if (product_type == 'custom_products') {
        if (product_list == '') {
            product_form_error = true;
            error_message = select_image_txt;
            $('#product_list').addClass('kb_error_field');
            $('#product_list').after('<span class="kb_error_message">' + error_message + '</span>');
        }
    }

    var key_numeric_err = velovalidation.isNumeric($('#number_of_products'), true);
    if (key_numeric_err != true)
    {
        product_form_error = true;
        error_message = select_image_txt;
        $('#number_of_products').addClass('kb_error_field');
        $('#number_of_products').after('<span class="kb_error_message">' + error_message + '</span>');
    }


    if (product_form_error) {
        return false;
    }
    submitProductform(event);

    return false;
}

function editProductHorizontalComponentFunction(a)
{
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getProductForm=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
            $('#kbGDPRDialogueModel .modal-body').html('');
            $('#kbGDPRDialogueModel').modal({
                show: 'true',
            });

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $('#kbGDPRDialogueModel').modal({
                    show: 'true',
                });
                showHideProductType(a);
                //$('#redirect_banner_product_name').parent().parent().hide();
//                $('#countdown_validity').closest('.form-group').hide();
//                $('.kbsw_wheel_color').closest('.form-group').parents('.form-group').hide();
//                $('#image_url').parent().parent().hide();
//                $('#slideruploadedfile').parent().parent().parent().parent().hide();
//                showUrlImage();
//                uploadfile();
//                autoCompleteProduct();
//                setDate();
//                setColor();
            }
        }
    });



}

function setColor() {
    $(document).on('change', '.kbsw_wheel_color', function () {
        var color = $(this).val();
        changeColor(color);
    });
}
function rgb2hsb(r, g, b)
{
    r /= 255;
    g /= 255;
    b /= 255; // Scale to unity.
    var minVal = Math.min(r, g, b),
        maxVal = Math.max(r, g, b),
        delta = maxVal - minVal,
        HSB = {hue: 0, sat: 0, bri: maxVal},
    del_R, del_G, del_B;

    if (delta !== 0)
    {
        HSB.sat = delta / maxVal;
        del_R = (((maxVal - r) / 6) + (delta / 2)) / delta;
        del_G = (((maxVal - g) / 6) + (delta / 2)) / delta;
        del_B = (((maxVal - b) / 6) + (delta / 2)) / delta;

        if (r === maxVal) {
            HSB.hue = del_B - del_G;
        } else if (g === maxVal) {
            HSB.hue = (1 / 3) + del_R - del_B;
        } else if (b === maxVal) {
            HSB.hue = (2 / 3) + del_G - del_R;
        }

        if (HSB.hue < 0) {
            HSB.hue += 1;
        }
        if (HSB.hue > 1) {
            HSB.hue -= 1;
        }
    }

    HSB.hue *= 360;
    HSB.sat *= 100;
    HSB.bri *= 100;
    return HSB;
}
function hexToRgb(hex) {
    // Expand shorthand form (e.g. "03F") to full form (e.g. "0033FF")
    var shorthandRegex = /^#?([a-f\d])([a-f\d])([a-f\d])$/i;
    hex = hex.replace(shorthandRegex, function (m, r, g, b) {
        return r + r + g + g + b + b;
    });

    var result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
    return result ? {
        r: parseInt(result[1], 16),
        g: parseInt(result[2], 16),
        b: parseInt(result[3], 16)
    } : null;
}

function changeColor(wheel_color)
{
    velsofWheelHexCode = wheel_color;
    var colorRGB = hexToRgb(velsofWheelHexCode);
    var hslColorCode = rgb2hsb(colorRGB.r, colorRGB.g, colorRGB.b);
    //   document.getElementById("kbsw_preview_img").style.filter = 'hue-rotate(' + hslColorCode.hue + 'deg) saturate(' + hslColorCode.sat + '%) contrast(1.1)';

}

function setDate() {
    $('.datetimepicker').click(function () {
        $('.ui-datepicker').css('z-index', '99999999');
    });
}
function setCategoryId(a) {
    if ($(a).val != 0) {
        for (i = 1; i < 8; i++) {
            var cat_id = 'category_id_' + i;
            if ($(a).attr('id') != cat_id) {
                if ($('#category_id_' + i).val() == $(a).val()) {
                    $('#category_id_' + i).val(0);
                }
            }
        }
    }
}
function veloValidateTopcategoryForm(a) {
    var unselected_cat = 0;
    for (i = 1; i <= 8; i++) {
        if ($('#category_id_' + i).val() == 0) {
            unselected_cat = unselected_cat + 1;
        }
    }
    if (unselected_cat > 4) {
        var error = true;
        showErrorMessage(min_category_limit);
    }
    if (error) {
        showErrorMessage(error_check_message);
        return false;
    } else {
        submitTopCategoryForm();
    }
    return false;
}
function submitTopCategoryForm() {
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    var id_category_1 = $('#category_id_1').val();
    var id_category_2 = $('#category_id_2').val();
    var id_category_3 = $('#category_id_3').val();
    var id_category_4 = $('#category_id_4').val();
    var id_category_5 = $('#category_id_5').val();
    var id_category_6 = $('#category_id_6').val();
    var id_category_7 = $('#category_id_7').val();
    var id_category_8 = $('#category_id_8').val();
    // changes
    var image_content_mode = $('#image_content_mode').val();
    // changes started
    var fd = new FormData();
    if ($('#slideruploadedfile_1').get(0).files.length > 0 && id_category_1 != 0) {
        fd.append('image_1', $('#slideruploadedfile_1')[0].files[0]);
    }
    if ($('#slideruploadedfile_2').get(0).files.length > 0 && id_category_2 != 0) {
        fd.append('image_2', $('#slideruploadedfile_2')[0].files[0]);
    }
    fd.append('image_content_mode', image_content_mode);
    if ($('#slideruploadedfile_3').get(0).files.length > 0 && id_category_3 != 0) {
        fd.append('image_3', $('#slideruploadedfile_3')[0].files[0]);
    }
    if ($('#slideruploadedfile_4').get(0).files.length > 0 && id_category_4 != 0) {
        fd.append('image_4', $('#slideruploadedfile_4')[0].files[0]);
    }
    if ($('#slideruploadedfile_5').get(0).files.length > 0 && id_category_5 != 0) {
        fd.append('image_5', $('#slideruploadedfile_5')[0].files[0]);
    }
    if ($('#slideruploadedfile_6').get(0).files.length > 0 && id_category_6 != 0) {
        fd.append('image_6', $('#slideruploadedfile_6')[0].files[0]);
    }
    if ($('#slideruploadedfile_7').get(0).files.length > 0 && id_category_7 != 0) {
        fd.append('image_7', $('#slideruploadedfile_7')[0].files[0]);
    }
    if ($('#slideruploadedfile_8').get(0).files.length > 0 && id_category_8 != 0) {
        fd.append('image_8', $('#slideruploadedfile_8')[0].files[0]);
    }

    fd.append('id_layout', id_layout);
    fd.append('id_component', id_component);
    fd.append('id_category_1', id_category_1);
    fd.append('id_category_2', id_category_2);
    fd.append('id_category_3', id_category_3);
    fd.append('id_category_4', id_category_4);
    fd.append('id_category_5', id_category_5);
    fd.append('id_category_6', id_category_6);
    fd.append('id_category_7', id_category_7);
    fd.append('id_category_8', id_category_8);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&saveTopcategoryFormData=true",
        data: fd,
        type: "post",
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#submitOptionsslider2').prop("disabled", true);

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#banner_form_popup').show();
                $('#component_edit_popup').empty();
                $('#component_edit_popup').append(b.html);
                $('#confirmation_block_modal').show();
                showSuccessMessage(success_message);
                //$('#kbGDPRDialogueModel').modal('hide');
            }
        }
    });
    return false;
}
function deleteCategoryImage(e) {
    $('#kbGDPRDialogueModel').find('.icon-trash').parent().each(function () {
        var str = $(this).parent().parent().find('.category_image_class').attr('id');
        var src = $("#" + str).attr('src');
        if (src == '') {
            $(this).parent().css("display", "none");
        }
        $(this).bind('click', function () {
            var str = $(this).parent().parent().find('.category_image_class').attr('id');
            var array = str.split("_");
            var id_category_component = array[1];
            var id_component = $('#id_component_selected').val();
            $(this).parent().css("display", "none");
            $.ajax({
                url: ajaxaction + "&configure=kbmobileapp&deleteTopCategoryImage=true",
                data: 'id_category_component=' + id_category_component + '&id_component=' + id_component,
                type: "post",
                success: function (data)
                {
                    if (1) {
                        $("#" + str).attr('src', '');
                        $(this).parent().css("display", "none");
                        //$(this).css("display", "none");
                        showSuccessMessage(category_image_delete_message);
                    }
                }
            });
            return false;
        })
    })

}

function editTopCategoryComponentFunction(a)
{
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);

    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&getCategoryForm=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        beforeSend: function () {
            $('#kbGDPRDialogueModel .modal-body').html('');
            $('#kbGDPRDialogueModel').modal({
                show: 'true',
            });

        },
        success: function (data)
        {
            var c = JSON.parse(data);
            if (1) {

                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').append(c.html);
                $('#kbGDPRDialogueModel').modal({
                    show: 'true',
                });

                uploadtopCategoryfile();
                $.ajax({
                    url: ajaxaction + "&configure=kbmobileapp&getTopcategoryImageUrl=true",
                    data: 'id_layout=' + id_layout + '&id_component=' + id_component,
                    type: "post",
                    success: function (data)
                    {
                        var b = JSON.parse(data);
                        if (b.length > 0) {
                            for (i = 0; i < b.length; i++) {
                                if (b[i]['name'] == 'sliderimage_1') {
                                    $("#sliderimage_1").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_2') {
                                    $("#sliderimage_2").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_3') {
                                    $("#sliderimage_3").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_4') {
                                    $("#sliderimage_4").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_5') {
                                    $("#sliderimage_5").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_6') {
                                    $("#sliderimage_6").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_7') {
                                    $("#sliderimage_7").attr('src', b[i]['value']);
                                } else if (b[i]['name'] == 'sliderimage_8') {
                                    $("#sliderimage_8").attr('src', b[i]['value']);
                                }
                            }
                        }
                        deleteCategoryImage();
                        uploadtopCategoryfile();
                    }
                });
            }
            uploadtopCategoryfile();
        }
    });
}



function submitbannersliderform(a)
{
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    var image_type = $('#image_type').val();
    var image_url = $('#image_url').val();
    var redirect_activity = $('#redirect_activity').val();
    var category_id = $('#category_id').val();
    var redirect_product_id = $('#redirect_banner_product_id').val();
    var image_content_mode = $('#image_content_mode').val();
    var redirect_product_name = $('#redirect_banner_product_name').val();
    var fd = new FormData();
    fd.append('image', $('#slideruploadedfile')[0].files[0]);
    fd.append('id_layout', id_layout);
    fd.append('id_component', id_component);
    fd.append('category_id', category_id);
    fd.append('redirect_activity', redirect_activity);
    fd.append('image_url', image_url);
    var lang = active_languages;
    for (i = 0; i < lang.length; i++) {
        fd.append('banner_heading_' + lang[i], $('#banner_heading_' + lang[i]).val());
    }
    if ($('#countdown_validity').is(":visible")) {
        fd.append('countdown_validity', $('#countdown_validity').val());
        fd.append('is_enabled_background_color', $('input[name="is_enabled_background_color"]:checked').val());
        fd.append('timer_background_color', $('input[name=timer_background_color]').val());
        fd.append('timer_text_color', $('input[name=timer_text_color]').val());
    }
    fd.append('image_content_mode', image_content_mode);
    fd.append('image_type', image_type);
    fd.append('redirect_product_id', redirect_product_id);
    fd.append('redirect_product_name', redirect_product_name);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&saveBannerSliderFormData=true",
        data: fd,
        type: "post",
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#kbGDPRDialogueModel').modal({
                show: 'true',
            });
            $('#submitOptionsslider2').prop("disabled", true);

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').empty();
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $('#sliderimage').hide();
                //$('#category_id').parent().parent().hide();
                $('#is_enabled_background_color_on').parent().closest('.form-group').hide();
                $('#countdown_validity').closest('.form-group').hide();
                $('.kbsw_wheel_color').closest('.form-group').parents('.form-group').hide();
                $('#redirect_banner_product_name').parent().parent().hide();
                $('#image_url').parent().parent().hide();
                $('#slideruploadedfile').parent().parent().parent().parent().hide();
                $('#confirmation_block_modal').show();
                showSuccessMessage(success_message);
                showUrlImage();
                uploadfile();
                autoCompleteProduct();
            }
        }
    });
    return false;
}
function submitProductform(a)
{
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    var category_id = $('#category_id').val();
    var number_of_product = $.trim($('#number_of_products').val());
    var category_id = $.trim($('#category_id').val());
    var product_list = $.trim($('#product_list').val());
    var category_products = $.trim($('#category_products').val());
    var product_type = $('#product_type').val();
    var image_content_mode = $('#image_content_mode').val();
    var fd = new FormData();
    fd.append('number_of_product', number_of_product);
    fd.append('id_component', id_component);
    fd.append('category_id', category_id);
    fd.append('id_layout', id_layout);
    var lang = active_languages;
    for (i = 0; i < lang.length; i++) {
        fd.append('component_heading_' + lang[i], $('#component_heading_' + lang[i]).val());
    }
    fd.append('product_type', product_type);
    fd.append('product_list', product_list);
    fd.append('category_products', category_products);
    fd.append('image_content_mode', image_content_mode);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&saveProductFormData=true",
        data: fd,
        type: "post",
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#submitOptionsslider2').prop("disabled", true);
        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').empty();
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $
                    ('#confirmation_block_modal').show();
                //$('#category_id').parent().parent().hide();
                showHideProductType(a);
                showSuccessMessage(success_message);
            }
        }
    });
    return false;
}
function submitCountdownbannersliderform(a)
{
    var id_layout = $('#id_layout').val();
    var id_component = $('#id_component_selected').val();
    var image_type = $('#image_type').val();
    var image_url = $('#image_url').val();
    var redirect_activity = $('#redirect_activity').val();
    var category_id = $('#category_id').val();
    var redirect_product_id = $('#redirect_banner_product_id').val();
    var image_content_mode = $('#image_content_mode').val();
    var redirect_product_name = $('#redirect_banner_product_name').val();
    var fd = new FormData();
    fd.append('image', $('#slideruploadedfile')[0].files[0]);
    fd.append('id_layout', id_layout);
    fd.append('id_component', id_component);
    fd.append('category_id', category_id);
    fd.append('redirect_activity', redirect_activity);
    fd.append('image_url', image_url);
    var lang = active_languages;
    for (i = 0; i < lang.length; i++) {
        fd.append('banner_heading_' + lang[i], $('#banner_heading_' + lang[i]).val());
    }
    if ($('#countdown_validity').is(":visible")) {
        fd.append('countdown_validity', $('#countdown_validity').val());
        fd.append('is_enabled_background_color', $('input[name="is_enabled_background_color"]:checked').val());
        fd.append('timer_background_color', $('input[name=timer_background_color]').val());
        fd.append('timer_text_color', $('input[name=timer_text_color]').val());
    }
    fd.append('image_content_mode', image_content_mode);
    fd.append('image_type', image_type);
    fd.append('redirect_product_id', redirect_product_id);
    fd.append('redirect_product_name', redirect_product_name);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&saveBannerSliderFormData=true",
        data: fd,
        type: "post",
        processData: false,
        contentType: false,
        beforeSend: function () {
//            $('#kbGDPRDialogueModel').modal({
//                show: 'true',
//            });
//            $('body').addClass("kb_loading");
            //$('modal-body').addClass("kb_loading");
            // $('#kbGDPRDialogueModel .modal-body').append('<img id="loader_module_list" style="width:50px;height:50px;align:center" src=" ' + loader_url + '" alt="" border="0" />');

        },
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').empty();
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $('#redirect_banner_product_name').parent().parent().hide();
                $('#image_url').parent().parent().hide();
                $('#slideruploadedfile').parent().parent().parent().parent().hide();
                $('#confirmation_block_modal').show();
                //$('#kbGDPRDialogueModel').modal('hide');
                showSuccessMessage(success_message);
                showUrlImage();
                uploadfile();
                setDate();
                setColor();
                autoCompleteProduct();
            }
        }
    });
    return false;
}
function delete_banner_slider(a) {
    var a = jQuery.trim($(a).closest('tr').find('.td-vss-code').html());
    var id_component = $('#id_component_selected').val();
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteSliderBanner=true",
        data: 'id_banner=' + a + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            var b = JSON.parse(data);
            if (1) {
                $('#kbGDPRDialogueModel .modal-body').html('');
                $('#kbGDPRDialogueModel .modal-body').empty();
                $('#kbGDPRDialogueModel .modal-body').append(b.html);
                $('#component_edit_popup').append(b.html);
                $('#category_id').parent().parent().hide();
                $('#redirect_product_name').parent().parent().hide();
                $('#image_url').parent().parent().hide();
                $('#slideruploadedfile').parent().parent().parent().parent().hide();
                showUrlImage();
                uploadfile();
                showSuccessMessage(banner_delete_message);
                countdownbannerDatepicker();
                //showHidebackgroundColor();
                autoCompleteProduct();
                setDate();
                setColor();
                $.ajax({
                    url: ajaxaction + "&configure=kbmobileapp&getComponentType=true",
                    data: '&id_component=' + id_component,
                    type: "post",
                    success: function (data)
                    {
                        if (data != 'banners_countdown') {
                            $('#is_enabled_background_color_on').parent().closest('.form-group').hide();
                            $('#countdown_validity').closest('.form-group').hide();
                            $('.kbsw_wheel_color').closest('.form-group').parents('.form-group').hide();
                        }
                    }
                });

            }
        }
    });
    showUrlImage();
}

function uploadfile() {
    $('#slideruploadedfile').on('change', function (e) {
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
                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
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

}
function uploadtopCategoryfile() {
    $('#slideruploadedfile_1').on('change', function (e) {
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
                $('input[name="slideruploadedfile_"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_1"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_1");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_1').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_1"]').parent().find('.kb_error_message').remove();
            }

        }
        else
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_2').on('change', function (e) {
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
                $('input[name="slideruploadedfile_2"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_2"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_2");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_2').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_2"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_4').on('change', function (e) {
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
                $('input[name="slideruploadedfile_4"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_4"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_2");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_4').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_4"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_3').on('change', function (e) {
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
                $('input[name="slideruploadedfile_3"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_3"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_3");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_3').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_3"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_5').on('change', function (e) {
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
                $('input[name="slideruploadedfile_5"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_5"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_5");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_5').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_5"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_6').on('change', function (e) {
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
                $('input[name="slideruploadedfile_6"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_6"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_6");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_6').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_6"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_7').on('change', function (e) {
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
                $('input[name="slideruploadedfile_7"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_7"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_7");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_7').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_7"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });
    $('#slideruploadedfile_8').on('change', function (e) {
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
                $('input[name="slideruploadedfile_8"]').parent().append('<span class="kb_error_message">' + invalid_file_format_txt + '</span>');
                slider_banner_file_error = true;

            } else if (files.size > default_file_size) {
                $('input[name="slideruploadedfile_8"]').parent().append('<span class="kb_error_message">' + file_size_error_txt + '</span>');
                slider_banner_file_error = true;
            } else {
                slider_banner_file_error = false;
                if (typeof (FileReader) != "undefined") {

                    var image_holder = $("#sliderimage_8");

                    image_holder.empty();

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        $('#sliderimage_8').attr('src', e.target.result);
                    }
                    image_holder.show();
                    reader.readAsDataURL($(this)[0].files[0]);
                }
                $('input[name="slideruploadedfile_8"]').parent().find('.kb_error_message').remove();
            }

        }
        else // Internet Explorer 9 Compatibility
        {
            $('#notification_error').html(invalid_file_txt);
            file_error = true;
        }
    });


}
function trashBannerSquareComponentFunction(a) {

    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteBannerSquarecomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);

            }
        }
    });

}
function trashBannerCountdownComponentFunction(a) {

    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteBannerCountdowncomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();

            }
        }
    });

}
function trashBannerGridComponentFunction(a) {

    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteBannerGridcomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);

            }
        }
    });

}
function trashBannerHorizontalComponentFunction(a) {

    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteBannerHorizontalcomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);
            }
        }
    });

}
function trashLastAccessComponentFunction(a) {
    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteLastAccesscomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);
            }
        }
    });

}
function trashProductSquareComponentFunction(a) {
    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteProductSquarecomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);
            }
        }
    });

}
function trashProductGridComponentFunction(a) {
    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteProductGridcomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);
            }
        }
    });

}
function trashProductHorizontalComponentFunction(a) {
    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)
    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteProductHorizonatlcomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);
            }
        }
    });

}
function trashTopcategoryComponentFunction(a) {
    num_of_component = parseInt($('#number_of_component').val());
    num_of_component = num_of_component - 1;
    $('#number_of_component').val(num_of_component)

    var str = $(a).attr('id');
    var array = str.split("_");
    var id_component = array[1];
    var id_layout = $('#id_layout').val();
    $('#id_component_selected').val(id_component);
    $.ajax({
        url: ajaxaction + "&configure=kbmobileapp&deleteTopcategorycomponent=true",
        data: 'id_layout=' + id_layout + '&id_component=' + id_component,
        type: "post",
        success: function (data)
        {
            if (1) {
                $(a).parents('.slide').remove();
                preview_content();
                showSuccessMessage(component_delete);
            }
        }
    });

}
function closeLayoutForm()
{
    $('.layout_add_edit_form').slideUp("fast", function () {

    });
}

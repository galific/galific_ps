/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store. 
 *
 * @category  PrestaShop Module
 * @author    knowband.com <support@knowband.com>
 * @copyright 2016 knowband
 * @license   see file: LICENSE.txt
 */

var validation_fields = {
        'isGenericName': /^[^<>={}]*$/,
        'isAddress': /^[^!<>?=+@{}_$%]*$/,
        'isPhoneNumber': /^[+0-9. ()-]*$/,
        'isInt': /^[0-9]*$/,
        'isIntExcludeZero':/^[1-9][0-9]*$/,
        'isPrice': /^[0-9]*(?:\.\d{1,6})?$/,
        'isPriceExcludeZero': /^[1-9]*(?:\.\d{1,6})?$/,
        'isDate': /^([0-9]{4})-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/,
        'isUrl': /[-a-zA-Z0-9@:%_\+.~#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~#?&//=]*)?/gi,
        'isEmail': /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
};

var submit_url = '';
$(document).ready(function(){
    
    $('button[name="submitAddkb_mp_seller_shipping_method"]').click(function(){
        var error = false;
        $('.error_message').remove();
          $('input[name="method"]').removeClass('error_field');
        if ($('input[name="method"]').val().trim() == '') {
            error = true;
            $('input[name="method"]').addClass('error_field');
            $('input[name="method"]').after('<span class="error_message">'+empty_field+'</span>');
        }
        
        if (error) {
            return false;
        } else {
             $('button[name="submitAddkb_mp_seller_shipping_method"]').attr('disabled', true);
             $('#kb_mp_seller_shipping_method_form').submit();
        }
        
    });
    
    $('#kb-mp-payout-setting-submit').click(function () {
        var error = false;
        $('.error_message').remove();
        $('input[name="kb_mp_payout_setting[client_id]"]').removeClass('error_field');
        $('input[name="kb_mp_payout_setting[client_secret]"]').removeClass('error_field');
        $('input[name="kb_mp_payout_setting[paypal_email_subject]"]').removeClass('error_field');

        if ($('input[name="kb_mp_payout_setting[client_id]"]').val().trim() == '') {
            error = true;
            $('input[name="kb_mp_payout_setting[client_id]"]').addClass('error_field');
            $('input[name="kb_mp_payout_setting[client_id]"]').after('<span class="error_message">' + field_empty + '</span>');
        }

        if ($('input[name="kb_mp_payout_setting[client_secret]"]').val().trim() == '') {
            error = true;
            $('input[name="kb_mp_payout_setting[client_secret]"]').addClass('error_field');
            $('input[name="kb_mp_payout_setting[client_secret]"]').after('<span class="error_message">' + field_empty + '</span>');
        }
        
        if ($('input[name="kb_mp_payout_setting[paypal_email_subject]"]').val().trim() == '') {
            error = true;
            $('input[name="kb_mp_payout_setting[paypal_email_subject]"]').addClass('error_field');
            $('input[name="kb_mp_payout_setting[paypal_email_subject]"]').after('<span class="error_message">' + field_empty + '</span>');
        } else {
            if ($('input[name="kb_mp_payout_setting[paypal_email_subject]"]').val().trim().match(/([\<])([^\>]{1,})*([\>])/i)) {
                error = true;
                $('input[name="kb_mp_payout_setting[paypal_email_subject]"]').addClass('error_field');
                $('input[name="kb_mp_payout_setting[paypal_email_subject]"]').after('<span class="error_message">' + kb_html_tags + '</span>');
            }
        }
        if (error) {
            return false;
        } else {
            $('#kb-mp-payout-setting-submit').attr('disabled', true);
            $('#configuration_form').submit();
        }
    });
    
    
    try
    {
        $(".kb_mp_html_decode").each(function( index ) {
            var text = $(this).text();
            text = text.replace("<","&lt;");
            text = text.replace(">","&gt;");
            $(this).html(text);
        });
    } catch (e) {}
    
    $('.fancybox-inner').attr('style','height:auto;');
    
    if($('#kb-marketplce-allw-cat').length){
        $('#kb-marketplce-allw-cat').multipleSelect({
            placeholder: 'Choose Category(s)',
            onCheckAll: function() {

            },
            onUncheckAll: function() {

            },
            onClick: function() {

            }
        });
    }
    
    if($('.marketplace-reason-modal').length){
        $('.marketplace-reason-modal').fancybox({
            width: 400,
            autoWidth: false,
            beforeLoad: function () {
                submit_url = $(this.element).attr('data-url');
                $('#kb-reason-error').html('');
                $('#marketplace_reason_comment').val('');
                $('#marketplace-reason-modal').show();
            },
            beforeClose: function () {
                $('#marketplace-reason-modal').hide();
            }
        });    
    }
    
    if($('.marketplace-view-modal').length){
        $('.marketplace-view-modal').fancybox({
            autoWidth: false,
            beforeLoad: function () {
                submit_url = $(this.element).attr('data-url');
                $.ajax({
                    type: 'POST',
                    headers: { "cache-control": "no-cache" },
                    url: submit_url + ((submit_url.indexOf('?') < 0) ? '?' : '&')+'rand=' + new Date().getTime(),
                    async: true,
                    cache: false,
                    dataType : "html",
                    data: 'ajax=true',
                    beforeSend: function() {
                        $('div#marketplace-view-modal').html('<div class="modal-content-loader"></div>');
                        $('div#marketplace-view-modal').show();
                    },
                    success: function(html)
                    {
                        $('div#marketplace-view-modal').html(html);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        $('div#marketplace-view-modal').html('');
                    }
                });
            },
            beforeClose: function () {
                $('div#marketplace-view-modal').hide();
            }
        });    
    }
    
    if($('.open_new_transaction_form').length){
        $('.open_new_transaction_form').fancybox({
            beforeLoad: function () {
                $('#kb-new-transaction-form').show();
            },
            beforeClose: function () {
                $('#kb-new-transaction-form').hide();
            }
        });    
    }
    
    if($('#kb_mp_seller_config_form_reset_btn').length){
        $('#kb_mp_seller_config_form_reset_btn').on('click', function(){
            var html = '<input type="hidden" name="kbmp_reset_setting" value="1" />';
            $('form#kb_mp_seller_config_form').append(html);
            $('form#kb_mp_seller_config_form').submit();
        });
    }
    
    $(".kb_checkbox_seller_settings:checked").each(function()
    {
//        $(this).parent().parent().find("div").find("input").each(function ()
//        {
//            $(this).attr('disabled',true);
//        });
        $(this).parent().parent().find("span").find("input").each(function ()
        {
            $(this).attr('disabled',true);
        });
    });
    
    $('#submitFilterButtonkb_mp_seller_transaction').click(function(){
        var controller_url = $('#form-kb_mp_seller_transaction').attr('action');
        $('#form-kb_mp_seller_transaction').attr('action', controller_url.split('#')[0]+'&transaction_view_type=1&submitFilterkb_mp_seller_transaction=1');
    });
    
//    $('#kb_mp_email_template_form button[name=submitAddkb_mp_email_template]').on('click',function(){
//        alert('sdf');
//    });
//    $('#kb_mp_email_template_form button[name=submitAddkb_mp_email_templateAndStay]').on('click',function(){
//        
//    });
    
});


function submitPayoutTransaction()
{
   
    var error = false;
    $('.error_message').remove();
    $('input[name="kb_payout_transaction_id"]').removeClass('error_field');
    $('input[name="kb_payout_transaction_comment"]').removeClass('error_field');
    
    var transaction_comment = $('input[name="kb_payout_transaction_comment"]').val().trim(); 
    
    if ($('input[name="kb_payout_transaction_id"]').length) {
        var transaction_id = $('input[name="kb_payout_transaction_id"]').val().trim();
        if (transaction_id == '') {
            error = true;
            $('input[name="kb_payout_transaction_id"]').addClass('error_field');
            $('input[name="kb_payout_transaction_id"]').after('<span class="error_message">' + pay_transid_required + '</span>');
        }
    }
    
    if (transaction_comment != '') {
        if (transaction_comment.match(/([\<])([^\>]{1,})*([\>])/i)) {
             error = true;
            $('input[name="kb_payout_transaction_comment"]').addClass('error_field');
            $('input[name="kb_payout_transaction_comment"]').after('<span class="error_message">' + kb_html_tags + '</span>');
        }
    }
    
    if (error) {
        return false;
    } else {
        $('form#form-transaction-payout-payment').submit();
    }
}


function disapproveWithConfirmation(e)
{
    if (confirm(kb_admin_dis_conf)){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}
function deleteWithConfirmation(e)
{
    if (confirm(kb_admin_del_conf)){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}
function approveWithConfirmation(e)
{
    if (confirm(kb_admin_app_conf)){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}

function kbValidateField(value, element)
{
    var value = $(element).val();
    
    for (var key in validation_fields)
    {
        if ($(element).hasClass(key))
        {
            var reg = new RegExp(validation_fields[key]);
            if(reg.test(value))
            {
                return true;
                break;
            }     
        }
    }
    
    return false;
}

//function validateTransactionAmount(id_form) {
//    $('#'+id_form+' .form-wrapper').find('.alert').remove();
//    $('#'+id_form+' .form-wrapper').find('.form-group').removeClass('has-error');
//    var error = false;
//    if(id_form == 'kb_new_transaction_form')
//    {
//        var element = $('#new_transaction_amount');
//        if(element.val() == '0')
//        {
//            element.closest(".form-group").addClass('has-error');
//            error = true;
//        }
//    }
//     if(!error){
//        return true;
//    }else{
//        return false;
//    }
//}


function validateKbHelperForm(id_form)
{
    $('#'+id_form+' .form-wrapper').find('.alert').remove();
    $('#'+id_form+' .form-wrapper').find('.form-group').removeClass('has-error');
    var error = false;
    if(id_form == 'kb_new_transaction_form')
    {
        var element = $('#new_transaction_amount');
        if(element.val() == '0')
        {
            element.closest(".form-group").addClass('has-error');
            error = true;
        }
    }
     if($('#'+id_form+' input[type="text"]').length || $('#'+id_form+' select').length)
    {
        var value = '';
        $('#'+id_form+' input[type="text"], #'+id_form+' select').each(function(){
            value = $(this).val();
            value = value.trim();
            if($(this).attr('required') && $(this).attr('required') == 'required')
            {
                if(value == '')
                {
                    error = true;
                    $(this).closest(".form-group").addClass('has-error');
                }
                else{
                    if(!kbValidateField(value, this))
                    {
                        error = true;
                        $(this).closest(".form-group").addClass('has-error');
                    }
                }
            }else if(!kbValidateField(value, this)){
                error = true;
                $(this).closest(".form-group").addClass('has-error');
            }
        });
    }
    
    if(!error){
        return true;
    }else{
        return false;
    }
}

function changeCommsionView(e)
{
    $('#marketplace-extra-content #configuration_form').submit();
}

function changeTransactionView(e)
{
    location.href = $(e).val();
}

function changeEmailTranslation(e)
{
    location.href = email_translation_url+'&id_email_template_lang='+$(e).val();
}

function openNewTransactionForm(e, id_seller)
{
    $('#kb_new_transaction_form input[type="text"]').val('');
    $('#kb_new_transaction_form select option').removeAttr('selected');
    $('#kb_new_transaction_form textarea').html('');
    if (id_seller > 0)
    {
        $('#kb_new_transaction_form select option').each(function(){
            if($(this).val() == id_seller){
                $(this).attr('selected', 'selected');
            }
        });
    }
    
    if($('#kb-new-transaction-form').is(':visible'))
    {
        $('#kb-new-transaction-form').slideUp('fast');
//        $('.open_new_transaction_form i').removeClass('icon-minus-sign');
//        $('#update_transaction_form_btn').removeClass('icon-plus-sign').addClass('icon-minus-sign');
        $('#update_transaction_form_btn').removeClass('icon-minus-sign').addClass('icon-plus-sign');
        $('#kb-new-trabsaction-btn-label').html(add_trasaction);
    }else{
        $('#kb-new-transaction-form').slideDown('fast');
        $('#update_transaction_form_btn').removeClass('icon-plus-sign').addClass('icon-minus-sign');
//        $('#update_transaction_form_btn').removeClass('icon-minus-sign').addClass('icon-plus-sign');
//        $('.open_new_transaction_form i').addClass('icon-minus-sign');
        $('#kb-new-trabsaction-btn-label').html(close_trasaction);
    }
}

function openkbPayoutSettingForm(e)
{
    if($('#kb_payout_setting_form').is(':visible'))
    {
        $('#kb_payout_setting_form').slideUp('fast');
        $('#kb-new-trabsaction-btn-label').html(kb_admin_trans_new_txt);
        $('#icon_add_colapse_new_transaction').removeClass('icon-collapse-top');
        $('#icon_add_colapse_new_transaction').addClass('icon-collapse');
    }else{
        $('#kb_payout_setting_form').slideDown('fast');
        $('#kb-new-trabsaction-btn-label').html(kb_admin_trans_close_txt);
        $('#icon_add_colapse_new_transaction').removeClass('icon-collapse');
        $('#icon_add_colapse_new_transaction').addClass('icon-collapse-top');
    }
}

function actionDissapprove()
{
    $('#kb-reason-error').html('');
    var txt = $('#marketplace_reason_comment').val();
    txt = txt.replace(/^\s+|\s+$/g,'');
    var click = 1;
    if(txt == ''){
        $('#kb-reason-error').html('<div class="alert alert-danger">'+empty_field_error+'</div>');
    }else if(txt.length < reason_min_length){
        $('#kb-reason-error').html('<div class="alert alert-danger">'+reason_min_length_msg+'</div>');
    }else{
        if (click == '1') {
            $('form#kb-reason-form').attr('action', submit_url);
            $('form#kb-reason-form').submit();
            $('#kb-reason-form .btn-success').attr('disabled','true');
            click++;
        }  else {
            $('#kb-reason-form .btn-success').attr('disabled','true');
        }
    }
}

function validateKbNewTransactionForm()
{
    var status = validateKbHelperForm('kb_new_transaction_form');
    var errors = [];
    if(status){
        $('#kb_new_transaction_form').submit();
    }else{
        if ($('#select_seller_transaction').val() == '0') {
            errors.push(select_seller);
//            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please Select Seller.</div>');
        }
        if (($('#new_transaction_amount').val() <= 0) || $('#new_transaction_amount').length == '' ) {
            errors.push(transaction_amt_error);
//            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please provide information with valid values.Tansaction Amount must be greater than 0.</div>');
        } if ($('#new_transaction_id').val() == '') {
            errors.push(transaction_id_error);
//            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>Please provide information with valid values.Tansaction Amount must be greater than 0.</div>');
        }
        if(errors.length > 0) {
            $('#kb_new_transaction_form .form-wrapper').prepend('<div class="alert alert-danger"><button type="button" class="close" data-dismiss="alert">×</button>' +errors.length +' '+errors+' <br><ol></ol></div>');
            for (var i=0;i<errors.length;i++) {
                $('.alert ol').prepend('<li>'+errors[i] + '</li>');
            }
            
        }
    }
}

function disapproveWithConfirmation(e)
{
    if (confirm(disapprove_conf)){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}
function deleteWithConfirmation(e)
{
    if (confirm(delete_conf)){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}
function approveWithConfirmation(e)
{
    if (confirm(approve_conf)){
        return true;
    }else{
        event.stopPropagation(); event.preventDefault();
    }
    return false;
}

function changeSwitchColor(element)
{
    if($(element).prop("checked") == true)
    {
//        $(element).parent().parent().find("div").find("input").each(function ()
//        {
//            $(this).attr('disabled',true);
//        });
        $(element).parent().parent().find("span").find("input").each(function ()
        {
            $(this).attr('disabled',true);
        });
    }
    else
    {
//        $(element).parent().parent().find("div").find("input").each(function ()
//        {
//            $(this).attr('disabled',false);
//        });
        $(element).parent().parent().find("span").find("input").each(function ()
        {
            $(this).attr('disabled',false);
        });
    }
}

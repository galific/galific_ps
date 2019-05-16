/**
 * @license
 */
var calledFromShipping = 0;
var extrasError = false;

$(document).ready(function() {

    // Create State list
    statelist(selectedCountry, 0, 'select[name="id_state"]');
    statelist(selectedCountry, 0, 'select[name="id_state_invoice"]');


    $('#myopc_invoice_address').hide();

    $('#myopc_create_new_account_button').click(function(e) {
        e.preventDefault();
        display_progress();

        var selected_shipping_address = $('select[name="shipping_address_id"] option:selected').val();


        if(isLogged) {
            if(selected_shipping_address != '-') {
                updateCartAddress();
            }
            else
            {
                createAddress();
            }
        } else {
            createNewAccount();
        }
    });

    //Create shipping state list based on selected shipping country
    $('select[name="id_country"]').change(function(){
        var selected_country = $(this).find('option:selected').attr('value');
        var selected_state = 0;
        statelist(selected_country, selected_state, 'select[name="id_state"]');
        // checkDniandVatNumber('delivery');
        getCarrierList();
    });

    //Create billing state list based on selected billing country
    $('select[name="id_country_invoice"]').change(function(){
        var selected_country = $(this).find('option:selected').attr('value');
        var selected_state = 0;
        statelist(selected_country, selected_state, 'select[name="id_state_invoice"]');
        // checkDniandVatNumber('delivery');
        // getCarrierList();
    });

    $('.myopccheckout_shipping_option').change(function(){
        calledFromShipping = 1;
        updateCarriers();
    });

    $('.myopccheckout_payment_options').change(function(){
        loadPayments();
    });

    //Get Payment Method Form
    if($('input:radio[name="payment_method"]:checked').length){
        actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
    } else{
        $('input:radio[name="payment_method"]').first().attr('checked', 'checked');
        $('input:radio[name="payment_method"]').first().parent().addClass('checked');
    }

    //Create shipping state list based on selected shipping country
    $('select[name="shipping_address_id"]').change(function(){
        var selected_shipping_address = $('select[name="shipping_address_id"] option:selected').val();
        var selected_billing_address = $('select[name="billing_address_id"] option:selected').val();
        var selectedDeliveryAddress;
        var selectedInvoiceAddress;

        if(selected_shipping_address != '-') {
            for (var i = 0; i < addresses.length; i++) {
                if (addresses[i].id_address == selected_shipping_address) {
                    selectedDeliveryAddress = addresses[i];
                }
            }

            $('#myopc_create_new_account_button').attr({name : 'updateAccount'});
        } else {
            $('#myopc_create_new_account_button').attr({name : 'createNewAddress'});

            $('input[name="dni"]').val('');
            $('input[name="company"]').val('');
            $('input[name="address1"]').val('');
            $('input[name="address2"]').val('');
            $('input[name="postcode"]').val('');
            $('input[name="city"]').val('');
            $('input[name="phone"]').val('');
            $('input[name="phone_mobile"]').val('');
        }


        if(selected_billing_address != '-') {
            for (var i = 0; i < addresses.length; i++) {
                if (addresses[i].id_address == selected_billing_address) {
                    selectedInvoiceAddress = addresses[i];
                }
            }

            $('#myopc_create_new_account_button').attr({name : 'updateAccount'});
        } else {
            $('#myopc_create_new_account_button').attr({name : 'createNewAddress'});

            $('input[name="dni_invoice"]').val('');
            $('input[name="company_invoice"]').val('');
            $('input[name="address1_invoice"]').val('');
            $('input[name="address2_invoice"]').val('');
            $('input[name="postcode_invoice"]').val('');
            $('input[name="city_invoice"]').val('');
            $('input[name="phone_invoice"]').val('');
            $('input[name="phone_mobile_invoice"]').val('');
        }

        if(selectedDeliveryAddress === undefined || selectedInvoiceAddress === undefined) return;

        // Fill Delivery address
        $('input[name="dni"]').val(selectedDeliveryAddress.dni);
        $('input[name="company"]').val(selectedDeliveryAddress.company);
        $('input[name="address1"]').val(selectedDeliveryAddress.address1);
        $('input[name="address2"]').val(selectedDeliveryAddress.address2);
        $('input[name="postcode"]').val(selectedDeliveryAddress.postcode);
        $('input[name="city"]').val(selectedDeliveryAddress.city);
        $('input[name="phone"]').val(selectedDeliveryAddress.phone);
        $('input[name="phone_mobile"]').val(selectedDeliveryAddress.phone_mobile);

        $('select[name="id_country"] option:selected').attr("selected",null);
        $('select[name="id_country"] option[value="' + selectedDeliveryAddress.id_country + '"]').attr("selected","selected");

        $('select[name="id_state"] option:selected').attr("selected",null);
        $('select[name="id_state"] option[value="' + selectedDeliveryAddress.id_state + '"]').attr("selected","selected");

        // Fill Invoice address
        $('input[name="dni_invoice"]').val(selectedInvoiceAddress.dni);
        $('input[name="company_invoice"]').val(selectedInvoiceAddress.company);
        $('input[name="address1_invoice"]').val(selectedInvoiceAddress.address1);
        $('input[name="address2_invoice"]').val(selectedInvoiceAddress.address2);
        $('input[name="postcode_invoice"]').val(selectedInvoiceAddress.postcode);
        $('input[name="city_invoice"]').val(selectedInvoiceAddress.city);
        $('input[name="phone_invoice"]').val(selectedInvoiceAddress.phone);
        $('input[name="phone_mobile_invoice"]').val(selectedInvoiceAddress.phone_mobile);

        $('select[name="id_country_invoice"] option:selected').attr("selected",null);
        $('select[name="id_country_invoice"] option[value="' + selectedInvoiceAddress.id_country + '"]').attr("selected","selected");

        $('select[name="id_state_invoice"] option:selected').attr("selected",null);
        $('select[name="id_state_invoice"] option[value="' + selectedInvoiceAddress.id_state + '"]').attr("selected","selected");

        statelist(selectedDeliveryAddress.id_country, selectedDeliveryAddress.id_state, 'select[name="id_state"]');
        statelist(selectedInvoiceAddress.id_country, selectedInvoiceAddress.id_state, 'select[name="id_state_invoice"]');

        getCarrierList();
    });

    if(isLogged) {
        $('#myopc_create_new_account_button').attr({name : 'updateAccount'});
    } else {
        $('#myopc_create_new_account_button').attr({name : 'submitGuestAccount'});
    }

    $("#myopc_place_order").click(function() {
        $('.errorsmall').remove();
        extrasError = false;
        if($('#myopccheckout-agree input[name="term-of-use"]').length && !$('#myopccheckout-agree input[name="term-of-use"]').is(':checked')){
            extrasError = true;
            $('#myopccheckout-agree').after('<span class="errorsmall">'+tosRequire+'</span>');
            return;
        }

        if(!extrasError) {
            display_progress();
            placeOrder();
        }
    });

    //trigger confirm order after confirming payment in dialog
    $("#payment-form #myopccheckout_dialog_proceed").click(function() {
        confirmOrder();
    });


    $('#btn-add-promo').click(function(){
        applyCouponCode();
    });


    if(customer) {
        $('#firstname').val(customer.firstname);
        $('#lastname').val(customer.lastname);
        $('#email').val(customer.email);

        var selected_shipping_address = $('select[name="shipping_address_id"] option:selected').val();
        var selected_billing_address = $('select[name="billing_address_id"] option:selected').val();
        var selectedDeliveryAddress;
        var selectedInvoiceAddress;

        for (var i = 0; i < addresses.length; i++) {
            if (addresses[i].id_address == selected_shipping_address) {
                selectedDeliveryAddress = addresses[i];
            }
        }


        for (var i = 0; i < addresses.length; i++) {
            if (addresses[i].id_address == selected_billing_address) {
                selectedInvoiceAddress = addresses[i];
            }
        }

        if (selectedDeliveryAddress === undefined || selectedInvoiceAddress === undefined) return;

        // Fill delivery Address
        $('input[name="dni"]').val(selectedDeliveryAddress.dni);
        $('input[name="company"]').val(selectedDeliveryAddress.company);
        $('input[name="address1"]').val(selectedDeliveryAddress.address1);
        $('input[name="address2"]').val(selectedDeliveryAddress.address2);
        $('input[name="postcode"]').val(selectedDeliveryAddress.postcode);
        $('input[name="city"]').val(selectedDeliveryAddress.city);
        $('input[name="phone"]').val(selectedDeliveryAddress.phone);
        $('input[name="phone_mobile"]').val(selectedDeliveryAddress.phone_mobile);

        $('select[name="id_country"] option:selected').attr("selected", null);
        $('select[name="id_country"] option[value="' + selectedDeliveryAddress.id_country + '"]').attr("selected", "selected");

        $('select[name="id_state"] option:selected').attr("selected", null);
        $('select[name="id_state"] option[value="' + selectedDeliveryAddress.id_state + '"]').attr("selected", "selected");

        // Fill invoice address
        $('input[name="dni_invoice"]').val(selectedInvoiceAddress.dni);
        $('input[name="company_invoice"]').val(selectedInvoiceAddress.company);
        $('input[name="address1_invoice"]').val(selectedInvoiceAddress.address1);
        $('input[name="address2_invoice"]').val(selectedInvoiceAddress.address2);
        $('input[name="postcode_invoice"]').val(selectedInvoiceAddress.postcode);
        $('input[name="city_invoice"]').val(selectedInvoiceAddress.city);
        $('input[name="phone_invoice"]').val(selectedInvoiceAddress.phone);
        $('input[name="phone_mobile_invoice"]').val(selectedInvoiceAddress.phone_mobile);

        $('select[name="id_country_invoice"] option:selected').attr("selected", null);
        $('select[name="id_country_invoice"] option[value="' + selectedInvoiceAddress.id_country + '"]').attr("selected", "selected");

        $('select[name="id_state_invoice"] option:selected').attr("selected", null);
        $('select[name="id_state_invoice"] option[value="' + selectedInvoiceAddress.id_state + '"]').attr("selected", "selected");
    }
});

function updateCartAddress() {
    var requestParam = getCounrtryAndIdDelivery();
    var id_address_delivery = requestParam[1];

    $.ajax({
        type: 'POST',
        data: 'id_address_delivery=' + id_address_delivery + '&id_address_invoice=' + id_address_delivery + "&ajax=true" + "&updateCartAddress=true",
        url: $('#module_url').val() + '&rand=' + new Date().getTime()
    })
        .done(function(response) {
            getCarrierList();

            hide_progress();

        })
        .fail(function(data) {

            hide_progress();

        });
}


function createAddress() {
    var requestParam = getCounrtryAndIdDelivery();
    var id_address_delivery = requestParam[1];

    var form = $('#myopc_create_new_account');
    var formMessages = $('#form-messages');
    var formData = $(form).serialize();

    $.ajax({
        type: 'POST',
        data: "&ajax=true"
            + "&token=" + static_token
            + "&createAddress=true"
            + "&" + formData,
        url: $('#module_url').val() + '&rand=' + new Date().getTime()
    })
        .done(function(response) {
            var jsonData = JSON.parse(response);

            // Set the message text.
            if (!jsonData.hasError) {
                $(formMessages).text("Save customer information successfully");
                loadAddressList(jsonData.addresses);
                setSelectedAddress(jsonData.id_address_selected);
                getCarrierList();
            } else {
                var errors = '';
                for(var error in jsonData.errors)
                    if(error !== 'indexOf')
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                $(formMessages).html(errors);
            }

            hide_progress();

        })
        .fail(function(data) {

            hide_progress();

        });
}

function createShippingAddress() {
    var requestParam = getCounrtryAndIdDelivery();
    var id_address_delivery = requestParam[1];

    var form = $('#myopc_create_new_account');
    var formMessages = $('#form-messages');
    var formData = $(form).serialize();

    $.ajax({
        type: 'POST',
        data: "&ajax=true"
        + "&token=" + static_token
        + "&createAddress=true"
        + "&" + formData,
        url: $('#module_url').val() + '&rand=' + new Date().getTime()
    })
        .done(function(response) {
            var jsonData = JSON.parse(response);

            // Set the message text.
            if (!jsonData.hasError) {
                $(formMessages).text("Save customer information successfully");
                loadAddressList(jsonData.addresses);
                setSelectedAddress(jsonData.id_address_delivery);
                getCarrierList();
            } else {
                var errors = '';
                for(var error in jsonData.errors)
                    if(error !== 'indexOf')
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                $(formMessages).html(errors);
            }

            hide_progress();

        })
        .fail(function(data) {

            hide_progress();

        });
}

function createBllingAddress() {
    var requestParam = getCounrtryAndIdDelivery();
    var id_address_delivery = requestParam[1];

    var form = $('#myopc_create_new_account');
    var formMessages = $('#form-messages');
    var formData = $(form).serialize();

    $.ajax({
        type: 'POST',
        data: "&ajax=true"
        + "&token=" + static_token
        + "&createAddress=true"
        + "&" + formData,
        url: $('#module_url').val() + '&rand=' + new Date().getTime()
    })
        .done(function(response) {
            var jsonData = JSON.parse(response);

            // Set the message text.
            if (!jsonData.hasError) {
                $(formMessages).text("Save customer information successfully");
                loadAddressList(jsonData.addresses);
                setSelectedAddress(jsonData.id_address_invoice);
                getCarrierList();
            } else {
                var errors = '';
                for(var error in jsonData.errors)
                    if(error !== 'indexOf')
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                $(formMessages).html(errors);
            }

            hide_progress();

        })
        .fail(function(data) {

            hide_progress();

        });
}

function loadAddressList(addresses) {
    $('#shipping_address_id').empty();
    $('#shipping_address_id').append('<option value="-">Add a new address...</option>');
    for(key in addresses) {
        $('#shipping_address_id').append('<option value="' + addresses[key].id_address + '">' + addresses[key].alias + '</option>');
    }
}

function setSelectedAddress(selected_address_id) {
    $("#shipping_address_id").val(selected_address_id);
}

function createNewAccount(){

    var errors = '';
    // Get the form.
    var form = $('#myopc_create_new_account');

    // Get the messages div.
    var formMessages = $('#form-messages');

    // Serialize the form data.
    var formData = $(form).serialize();

    // Submit the form using AJAX.
    $.ajax({
        type: 'POST',
        url: $(form).attr('action') + "&ajax=true" + "&submitGuestAccount=true",
        data: formData
    })
        .done(function(response) {
            // Make sure that the formMessages div has the 'success' class.
            $(formMessages).removeClass('error');
            $(formMessages).addClass('success');

            // Set the message text.
            var jsonData = JSON.parse(response);
            // Set the message text.
            if (!jsonData.hasError) {
                $(formMessages).text("Save customer information successfully");


                $('select[name="shipping_address_id"] option:selected').attr("selected", null);
                $('select[name="shipping_address_id"] option[value="' + jsonData.id_address_delivery + '"]').attr("selected", "selected");

                $('select[name="billing_address_id"] option:selected').attr("selected", null);
                $('select[name="billing_address_id"] option[value="' + jsonData.id_address_invoice + '"]').attr("selected", "selected");

                getCarrierList();
            } else {
                var errors = '';
                for(var error in jsonData.errors)
                    if(error !== 'indexOf')
                        errors += $('<div />').html(jsonData.errors[error]).text() + "\n";
                $(formMessages).html(errors);
            }

            hide_progress();

        })
        .fail(function(data) {
            // Make sure that the formMessages div has the 'error' class.
            $(formMessages).removeClass('success');
            $(formMessages).addClass('error');

            // Set the message text.
            if (data.responseText !== '') {
                $(formMessages).text(data.responseText);
            } else {
                $(formMessages).text('Oops! An error occured and your message could not be sent.');
            }

            hide_progress();

        });
}


var shipping_error_found_on_load = false;

function getCarrierList() {
    var requestParam = getCounrtryAndIdDelivery();
    var id_country = requestParam[0];
    var id_state = 0;
    if(checkStateVisibility(id_country, 'select[name="id_state"]')) {
        id_state = $('select[name="id_state"]').val();
    }
    var postcode = $('input[name="postcode"]').val();
    var city = $('input[name="city"]').val();
    var id_address_delivery = requestParam[1];
    shipping_error_found_on_load = false;
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
        +'&id_country='+id_country
        +'&id_state='+id_state
        +'&postcode='+postcode
        +'&city='+city
        +'&id_address_delivery='+id_address_delivery
        +'&method=getCarrierList&token=' + static_token,
        beforeSend: function() {
            // $('#shippingMethodLoader').show();
            // $('#shipping-method .myopccheckout-checkout-content').find('.permanent-warning').html('');
        },
        complete: function() {
            //$('#shippingMethodLoader').hide();
        },
        success: function(jsonData)
        {
            carriers_count = jsonData['carriers_count'];
            is_cart_virtual = jsonData['is_cart_virtual'];
            $('#hook-extracarrier').html(jsonData['HOOK_EXTRACARRIER']);
            if(jsonData['hasError']){
                $('#shipping-method .myopccheckout-checkout-content').html('<div class="permanent-warning">'+jsonData['errors'][0]+'</div>');
                shipping_error_found_on_load = true;
            }else{
                shipping_error_found_on_load = false;
            }
            if (calledFromShipping == 0)
                $('#shipping-method').html(jsonData['carrier_block']);

            calledFromShipping = 0;

            updateShippingExtra(jsonData);
            // set_column_inside_height();
            // updateCartSummary(jsonData[0].summary);
            updateCarriers();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            // $('#shipping-method .myopccheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
        }
    });
}


function updateCarriers(){
    var delivery_option = ($('.myopccheckout_shipping_option').length)? '&'+$('.myopccheckout_shipping_option:checked').attr('name')+'='+$('.myopccheckout_shipping_option:checked').attr('value') : '';
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
        + delivery_option
        +'&method=updateCarrier&token=' + static_token,
        beforeSend: function() {
            if(!shipping_error_found_on_load){
                $('#shipping-method .myopccheckout-checkout-content').find('.permanent-warning').remove();
            }
            $('#shippingMethodLoader').show();
        },
        complete: function() {
            $('#shippingMethodLoader').hide();
        },
        success: function(jsonData)
        {
            if(jsonData['hasError']){
                if(jsonData['errors'][0] != undefined && jsonData['errors'][0] != ''){
                    $('#shipping-method .myopccheckout-checkout-content').html('<div class="permanent-warning">'+jsonData['errors'][0]+'</div>');
                }
            }
            loadPayments();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#shipping-method .myopccheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
        }
    });
}

function loadPayments(){
    var requestParam = getCounrtryAndIdDelivery();
    var selected_payment_method_id=$('input:radio[name="payment_method"]:checked').val(); // getting value of selected payment methods
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
        +'&id_country='+requestParam[0]
        +'&id_address_delivery='+requestParam[1]
        +'&selected_payment_method_id='+selected_payment_method_id
        +'&method=loadPayment&token=' + static_token,
        beforeSend: function() {
            // $('#payment-method .supercheckout-checkout-content').find('.permanent-warning').html('');
            // $('#paymentMethodLoader').show();
        },
        complete: function() {
            // $('#paymentMethodLoader').hide();
        },
        success: function(jsonData)
        {
            // $('#payment-method').html(jsonData['payment_method']);

            if(jsonData['payment_method_list']['methods'] != undefined && jsonData['payment_method_list']['methods'].length){
                $('#payment_method_list').html('');
                $('#payment_method_html').html('');
                var payment_list_html = '';
                var description_html = '';
                for (var i in jsonData['payment_method_list']['methods']){
                    payment_list_html += '<div class="form-field">';
                    payment_list_html += '<input type="hidden" id="' + jsonData['payment_method_list']['methods'][i]['id_module'] + '_name" value="' + jsonData['payment_method_list']['methods'][i]['payment_module_url'] + '" />'
                    payment_list_html += '<input type="radio" class="myopccheckout_payment_options" name="payment_method" value="' + jsonData['payment_method_list']['methods'][i]['id_module'] + '" id="'+ jsonData['payment_method_list']['methods'][i]['name'] + '"/>'
                    payment_list_html += '<label id="payment_lbl_' + jsonData['payment_method_list']['methods'][i]['id_module'] + '" for="' + jsonData['payment_method_list']['methods'][i]['name'] + '">'
                    payment_list_html += '<img src="https://onepagecheckoutps.presteamshop.com/demo/1.6/modules/onepagecheckoutps/views/img/payments/bankwire.gif" alt="" />'
                    payment_list_html += '<span id="' + jsonData['payment_method_list']['methods'][i]['id_module'] + '">' + jsonData['payment_method_list']['methods'][i]['display_name'] + '</span>'
                    payment_list_html += '</label>';
                    payment_list_html += '</div>';

                    description_html += '<div id="paymentmodule_'+jsonData['payment_method_list']['methods'][i]['id_module']+'_'+jsonData['payment_method_list']['methods'][i]['name']+'">';
                    description_html += jsonData['payment_method_list']['methods'][i]['html'];
                    description_html += '</div>';
                }
                $('#payment_method_list').html(payment_list_html);
                $('#payment_method_html').html(description_html);
            }

            // if($('input:radio[name="payment_method"]').length && !$('input:radio[name="payment_method"]:checked').length){
            //     $('#payment_display_block .supercheckout-payment-info').hide();
            //     $('#display_payment').html('');
            // }
            // if (typeof changePaymentMethodFee == 'function') {
            //     changePaymentMethodFee();
            // }
            // changePaymentMethodLabel();
            // set_column_inside_height();
            loadCart();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            // $('#payment-method .supercheckout-checkout-content').html('<div class="permanent-warning">'+errors+'</div>');
        }
    });
}


function getPaymentMethods () {


}


var custom_epay = 0;
function placeOrder(){
    var errors = '';

    var comment = $('textarea[name="comment"]').val();
    // if (typeof value === "undefined") {
    //     comment = '';
    // }


    // var form = $('#myopccheckout_form');


    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&ajax=true&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
            + '&PlaceOrder=1'
            + '&comment=' + comment
            + '&token=' + static_token,
        beforeSend: function() {
            // $('.errorsmall').remove();
            // hideGeneralError();
            // display_progress(20);
        },
        complete: function() {

        },
        success: function(jsonData)
        {
            actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));

            // if(jsonData['error'] != undefined){
            //     var has_validation_error = false;
            //     var i=0;
            //     if(jsonData['error']['checkout_option'] != undefined){
            //         has_validation_error = true;
            //         for(i in jsonData['error']['checkout_option']){
            //             $('input[name="'+jsonData['error']['checkout_option'][i]['key']+'"]').parent().append('<span class="errorsmall">'+jsonData['error']['checkout_option'][i]['error']+'</span>');
            //             if (inline_validation == 1)
            //                 $('input[name="'+jsonData['error']['checkout_option'][i]['key']+'"]').addClass('error-form').removeClass('ok-form');
            //         }
            //     }
            //
            //     var i=0;
            //     var key = '';
            //     if(jsonData['error']['customer_personal'] != undefined){
            //         has_validation_error = true;
            //         for(i in jsonData['error']['customer_personal']){
            //             key = jsonData['error']['customer_personal'][i]['key'];
            //             if(key == 'dob' || key == 'id_gender'){
            //                 $('.myopccheckout_personal_'+key).append('<span class="errorsmall">'+jsonData['error']['customer_personal'][i]['error']+'</span>');
            //             }else if(key == 'password'){
            //                 $('input[name="customer_personal['+key+']"]').parent().append('<span class="errorsmall">'+jsonData['error']['customer_personal'][i]['error']+'</span>');
            //                 if (inline_validation == 1)
            //                     $('input[name="customer_personal['+key+']"]').addClass('error-form').removeClass('ok-form');
            //             }else{
            //                 $('input[name="customer_personal['+key+']"]').parent().parent().parent().parent().append('<span class="errorsmall">'+jsonData['error']['customer_personal'][i]['error']+'</span>');
            //                 if (inline_validation == 1)
            //                     $('input[name="customer_personal['+key+']"]').addClass('error-form').removeClass('ok-form');
            //             }
            //         }
            //     }
            //
            //     var tmp_index;
            //     if(jsonData['error']['shipping_address'] != undefined){
            //         has_validation_error = true;
            //         for(tmp_index in jsonData['error']['shipping_address']){
            //             $('input[name="shipping_address['+jsonData['error']['shipping_address'][tmp_index]['key']+']"]').parent().append('<span class="errorsmall">'+jsonData['error']['shipping_address'][tmp_index]['error']+'</span>');
            //             if (inline_validation == 1)
            //                 $('input[name="shipping_address['+jsonData['error']['shipping_address'][tmp_index]['key']+']"]').addClass('error-form').removeClass('ok-form');
            //             if(jsonData['error']['shipping_address'][tmp_index]['key']=='postcode')
            //                 $('#shipping_post_code').css("display","block");// helpful when postcode is hidden from our module but is equired for some country
            //         }
            //     }
            //
            //
            //     var tmp_index;
            //     if(jsonData['error']['payment_address'] != undefined){
            //         has_validation_error = true;
            //         for(tmp_index in jsonData['error']['payment_address']){
            //             $('input[name="payment_address['+jsonData['error']['payment_address'][tmp_index]['key']+']"]').parent().append('<span class="errorsmall">'+jsonData['error']['payment_address'][tmp_index]['error']+'</span>');
            //             if (inline_validation == 1)
            //                 $('input[name="payment_address['+jsonData['error']['payment_address'][tmp_index]['key']+']"]').addClass('error-form').removeClass('ok-form');
            //             if(jsonData['error']['payment_address'][tmp_index]['key']=='postcode')
            //                 $('#payment_post_code').css("display","block"); // helpful when postcode is hidden from our module but is equired for some country
            //         }
            //     }
            //     i=0;
            //     if(jsonData['error']['general'] != undefined){
            //         errors = '';
            //         for(var i in jsonData['error']['general']){
            //             errors += jsonData['error']['general'][i]+'<br>';
            //         }
            //     }else if(has_validation_error){
            //         errors = validationfailedMsg;
            //     }else{
            //         errors = scOtherError;
            //     }
            //     displayGeneralError(errors);
            //     hide_progress();
            //     $("html, body").animate({ scrollTop: 0 }, "fast");
            // }else{
            //     if(jsonData['warning'] != undefined){
            //         //handle warning here
            //     }
            //     display_progress(30);
            //     var is_carrier_selected = true;
            //
            //     //validate Methods
            //     $('#shipping-method .myopccheckout-checkout-content .permanent-warning').remove();
            //     if($('#shipping-method .myopccheckout_shipping_option').length){
            //         if(!$('#shipping-method .myopccheckout_shipping_option:checked').length){
            //             is_carrier_selected = false;
            //         }
            //     }
            //
            //     var is_payment_selected = true;
            //     $('#payment-method .myopccheckout-checkout-content .permanent-warning').remove();
            //     if($('#payment-method input[name="payment_method"]').length){
            //         if(!$('#payment-method input[name="payment_method"]:checked').length){
            //             is_payment_selected = false;
            //         }
            //     }
            //
            //     if(carriers_count == 0 && !(is_cart_virtual))
            //         is_carrier_selected = false;
            //
            //     if(!is_carrier_selected){
            //         $('#shipping-method .myopccheckout-checkout-content').html('<div class="permanent-warning">'+ShippingRequired+'</div>');
            //     }
            //     if(!is_payment_selected){
            //         $('#payment-method .myopccheckout-checkout-content').html('<div class="permanent-warning">'+paymentRequired+'</div>');
            //     }
            //
            //     if(!is_carrier_selected || !is_payment_selected){
            //         hide_progress();
            //         displayGeneralError('Please provide required Information');
            //         $("html, body").animate({ scrollTop: 0 }, "fast");
            //     }else{
            //
            //         display_progress(50);
            //         //Validate Order Extras
            //         var messagePattern = /[<>{}]/i;
            //         var message = '';
            //         var extrasError = false;
            //         if($('#myopccheckout-comment_order').length){
            //             message = $('#myopccheckout-comment_order').val();
            //             if(messagePattern.test(message)){
            //                 extrasError = true;
            //                 $('#myopccheckout-comment_order').parent().append('<span class="errorsmall">'+commentInvalid+'</span>');
            //             }
            //         }
            //
            //         if($('#gift').length && $('#gift').is(':checked')){
            //             message = $('#gift_message').val();
            //             if(messagePattern.test(message)){
            //                 extrasError = true;
            //                 $('#gift_message').parent().append('<span class="errorsmall">'+commentInvalid+'</span>');
            //             }
            //         }
            //
            //         if($('#myopccheckout-agree input[name="cgv"]').length && (!$('#myopccheckout-agree input[name="cgv"]').is(':checked') && scp_required_tos == 1)){
            //             extrasError = true;
            //             $('#myopccheckout-agree').after('<span class="errorsmall">'+tosRequire+'</span>');
            //         }
            //
            //         if(extrasError){
            //             hide_progress();
            //         }else{
            //             display_progress(80);
            //             var is_free_order = false;
            //             if (scp_use_taxes && scp_order_total_price <= 0){
            //                 is_free_order = true;
            //             }else if(!scp_use_taxes && scp_order_total_price_wt <= 0){
            //                 is_free_order = true;
            //             }
            //             if(is_free_order){
            //                 createFreeOrder();
            //             }else{
            //                 proceed_to_payment = true;
            //                 if($('input:radio[name="payment_method"]:checked').length){
            //                     var p_m_name = $('input:radio[name="payment_method"]:checked').attr('id');
            //                     if(p_m_name == 'stripejs' || p_m_name == 'stripepro' || p_m_name == 'firstdata' || p_m_name == 'conektatarjeta' || p_m_name == 'braintreejs_backup' || p_m_name == 'twocheckout' || p_m_name == 'brinkscheckout' || p_m_name == 'ewayrapid' || p_m_name == 'npaypalpro' ||  p_m_name == 'authorizeaim' || p_m_name == 'librapay' || p_m_name == 'secureframe' || p_m_name == 'cashondelivery' || p_m_name == 'compropago' || p_m_name == 'checkout' || p_m_name == 'westernunion' || p_m_name == 'billmatepartpayment' || p_m_name == 'billmateinvoice' || p_m_name == 'paysondirect' || p_m_name == 'mercadoc' || p_m_name == 'boletosantanderpro' || p_m_name == 'payu' || p_m_name == 'megashoppay' || p_m_name == 'zipcheck' || p_m_name == 'megareembolso' || p_m_name == 'payinstore' || p_m_name == 'codfee' || p_m_name == 'obsredsys' || p_m_name == 'hipay' || p_m_name == 'psphipay' || p_m_name == 'finanziamento' || p_m_name == 'cecatpv' || p_m_name == 'dineromail' || p_m_name == 'payulatam' || p_m_name == 'cuatrob'){
            //                         moveToPayment();
            //                     }else{
            //                         actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
            //                     }
            //                     if (p_m_name == 'epay' && custom_epay == 0)
            //                     {
            //                         if($('#velsof_payment_container').is(':visible')) {
            //                             $('#velsof_payment_container .velsof_dialog_close').click();
            //                         }
            //                         custom_epay = 1;
            //                         $('#myopccheckout_confirm_order').click();
            //                     }
            //                 }else{
            //                     $('input:radio[name="payment_method"]').first().attr('checked', 'checked');
            //                     $('input:radio[name="payment_method"]').first().parent().addClass('checked');
            //                     actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
            //                 }
            //             }
            //         }
            //
            //     }
            // }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
            hide_progress();
            $("html, body").animate({ scrollTop: 0 }, "fast");
        }
    });
}


function actionOnPaymentSelect(element){

    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var id = $('input:radio[name="payment_method"]:checked').val();

    if(payment_module_name == 'bankwire' || payment_module_name == 'cheque' || payment_module_name == 'cashondelivery') {
        getPaymentForm(element);
    } else if(payment_module_name == 'paypal') {
        getPaymentForm1(element);
    }

    //
    // if (payment_module_name != 'epay')
    // {
    //     $('#myopccheckout_dialog_proceed').show();
    //     $('#velsof_payment_container .velsof_content_section').css('height', '200px');
    // }
    //
    // var redirectHtml = '';
    // //to fix stripjs method you need to edit its stripjs.php file too.. check pmsheet wiki for more information
    // //to fix braintreejs method you need to edit its braintreejs.php file too.. check pmsheet wiki for more information
    // if(payment_module_name != 'stripejs' && payment_module_name != 'firstdata' && payment_module_name != 'conektatarjeta' && payment_module_name != 'stripepro' && payment_module_name != 'braintreejs_backup' && payment_module_name != 'twocheckout' && payment_module_name != 'brinkscheckout' && payment_module_name != 'ewayrapid' && payment_module_name != 'npaypalpro' && payment_module_name != 'authorizeaim'){
    //     $('#selected_payment_method_html').html(''); // to hide form if customer select any other payment method later
    // }
    // if(payment_module_name == 'librapay' || payment_module_name == 'cashondelivery' || payment_module_name == 'secureframe' || payment_module_name == 'compropago' || payment_module_name == 'checkout' || payment_module_name == 'westernunion' || payment_module_name == 'billmateinvoice' || payment_module_name == 'billmatepartpayment' || payment_module_name == 'mercadoc' ||payment_module_name == 'boletosantanderpro' || payment_module_name == 'payu' || payment_module_name == 'payulatam' || payment_module_name == 'zipcheck' || payment_module_name == 'megareembolso' || payment_module_name == 'payinstore' || payment_module_name == 'codfee' || payment_module_name == 'finanziamento' || payment_module_name == 'megashoppay' ){
    //     redirectHtml += '<input type="hidden" id="payment_redirect" value="'+$('#'+id+'_name').val()+'" />';
    //     $('#velsof_payment_dialog .velsof_content_section').html(redirectHtml);
    // }else if(payment_module_name == 'bankwire' || payment_module_name == 'boleto' || payment_module_name == 'invoicepayment' || payment_module_name == 'pagofacil' || payment_module_name == 'postepay' || payment_module_name == 'paysera' || payment_module_name == 'offlinecreditcard' || payment_module_name == 'trustly' || payment_module_name == 'cheque' || payment_module_name == 'deluxecodfees'){
    //     getPaymentForm(element);
    // }else if(payment_module_name == 'stripejs' || payment_module_name == 'stripepro' || payment_module_name == 'firstdata' || payment_module_name == 'conektatarjeta' || payment_module_name == 'braintreejs_backup' || payment_module_name == 'twocheckout' || payment_module_name == 'brinkscheckout' || payment_module_name == 'ewayrapid' || payment_module_name == 'npaypalpro' ||  payment_module_name == 'mobilpay_cc' || payment_module_name == 'authorizeaim' || payment_module_name == 'khipupayment' || payment_module_name == 'paynl_paymentmethods' || payment_module_name == 'mollie' || payment_module_name == 'quickpay' || payment_module_name == 'moneybookers' || payment_module_name == 'faspay' || payment_module_name == 'paynlpaymentmethods' || payment_module_name == 'add_gopay_new' || payment_module_name == 'paypal' || payment_module_name == 'parspalpayment' || payment_module_name == 'pronesis_bancasella' || payment_module_name == 'paypalmx' || payment_module_name == 'cmcic_tbweb' || payment_module_name == 'sisoweb' || payment_module_name == 'citrus' || payment_module_name == 'banc_sabadell' || payment_module_name == 'ccavenue' || payment_module_name == 'ogone' || payment_module_name == 'epay' || payment_module_name == 'creditcardpaypal' || payment_module_name == 'paypalusa' || payment_module_name == 'sisowideal' || payment_module_name == 'paypalwithfee' || payment_module_name == 'sisowmc'){
    //     getPaymentForm1(element);
    // }else{
    //     getPaymentForm1(element);
    // }
    // if (typeof changePaymentMethodFeeCart == 'function') {
    //     changePaymentMethodFeeCart();
    // }
}



function getPaymentForm(element){
    var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var setErrorResponse = '<input type="hidden" id="payment_fetch_error" value="0" />';
    $.ajax({
        type: 'GET',
        headers: { "cache-control": "no-cache" },
        url: url,
        async: true,
        //cache: false,
        dataType : "html",
        beforeSend: function() {
            // $('#paymentMethodLoader').show();
            // $('#velsof_payment_dialog .velsof_content_section').html(setErrorResponse);
        },
        complete: function() {
            // $('#paymentMethodLoader').hide();
        },
        success: function(dataHtml)
        {
            try{
                var payment_info_html = $(dataHtml).find('#'+payment_content_id);
                $(payment_info_html).find('#order_step').remove();
                $('h1', payment_info_html).remove();
                $('#cart_navigation', payment_info_html).remove();
                $('.cart_navigation', payment_info_html).remove();      // Added for Prestashop 1.5 for removing the buttons in the payment method html
                $('#amount', payment_info_html).removeClass('price');
                $(payment_info_html).find('form:first').find('div:first, div.box').find('p:last-child').remove();
                $(payment_info_html).find('form:first').find('div:first, div.box').find('#currency_payement').parent().hide();

                 $('#payment-form .content-dialog').html(payment_info_html.html());
                hide_progress();

                 $('#payment-form').modal('show');
            }catch(err){
                $('#payment-form .content-dialog').html(setErrorResponse);
                hide_progress();

            }
            // if(proceed_to_payment){
            //     moveToPayment();
            // }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
        }
    });
}

function getPaymentForm1(element){
    $('#payment-form').html('');
    $('#payment-form').parent().find('.content-dialog_payment').remove();

    var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var payment_module_id = $('input:radio[name="payment_method"]:checked').val();
    var setErrorResponse = '<input type="hidden" id="payment_fetch_error" value="0" />';
    $.ajax({
        type: 'GET',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        //cache: false,
        dataType : "json",
        data: 'ajax=true'
        +'&method=getPaymentInformation'
        +'&id_module='+payment_module_id
        +'&payment_module_name='+payment_module_name
        +'&token=' + static_token,
        beforeSend: function() {
            $('#paymentMethodLoader').show();
            $('#payment-form .content-dialog_payment').html(setErrorResponse);
        },
        complete: function() {
            $('#paymentMethodLoader').hide();
        },
        success: function(json)
        {
            var html = '';
            if(json['error'] != undefined){
                html = '<input type="hidden" id="payment_fetch_error" value="0" />';
                $('#payment-form .content-dialog_payment').html(html);
            }else{
                $('#payment-form .content-dialog_payment').html(json['html']);
            }
            // if(proceed_to_payment){
                moveToPayment();
            // }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            displayGeneralError(errors);
        }
    });
}

function moveToPayment(){
    var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
    var p_m_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var dialogContainer = '#payment-form .content-dialog_payment ';


    if($(dialogContainer+'#payment_fetch_error').length){
        window.reload();
    }else{
        if(p_m_name == 'paypal' && url == 'javascript:void(0)'){
            $('#paypal_process_payment').trigger('click');
            $('#paypal_payment_form_payment').submit(); // above statement was not working for Prestashop 1.6.1.0
        }else if(p_m_name == 'boleto'){
            $('#myopccheckout_dialog_proceed').trigger('click');
        }
        else if(p_m_name == 'parspalpayment'){
            var form_action = $('#parspalpayment_form').attr('action');
            $('#parspalpayment_form').attr('action', '/'+form_action);
            $('#parspalpayment_form').submit();
        }else if(p_m_name == 'pronesis_bancasella'){
            $('#bancasella_process_payment').trigger('click');
        }else if(p_m_name == 'deluxeservired'){
            $('#deluxeservired_form').submit();
        }else if(p_m_name == 'plationline'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'bmsboletobancario'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'paypalmx'){
            $('#paypal-express-checkout-form').submit();
        }else if(p_m_name == 'cmcic_tbweb'){
            javascript:$('#PaymentRequest1').submit();
        }else if(p_m_name == 'sisoweb'){
            $('#sisow_ebill_form').submit();
        }else if(p_m_name == 'sisowob'){
            $('#sisow_overboeking_form').submit();
        }else if(p_m_name == 'sisowpp'){
            $('#sisow_paypalec_form').submit();
        }else if(p_m_name == 'citrus'){
            javascript:$('#citrus_form').submit();
        }else if(p_m_name == 'banc_sabadell'){
            javascript:$('#SabadellTPVForm').submit();
        }
        else if(p_m_name == 'ogone'){
            document.forms['ogone_form'].submit();
        }else if(p_m_name == 'creditcardpaypal' && url == 'javascript:void(0)'){
            $('#paypal_payment_form_credit_card input[name=\'express_checkout\']').val('payment_cart');
            $(dialogContainer+'#paypal_process_payment_credit_card').trigger('click');
        }else if(p_m_name == 'paypalusa'){
            $(dialogContainer+'#paypal-standard-btn').trigger('click');
            $('#velsof_payment_dialog #paypal-express-checkout-btn-product').click(); //for mexico paypalusa
        }else if(p_m_name == 'ccavenue'){
            javascript:document.redirect.submit();
        }else if(p_m_name == 'paypalwithfee'){
            $(dialogContainer+'#paypal_process_payment_').trigger('click');
        }else if(p_m_name == 'sisowideal'){
            $(dialogContainer+'#sisowideal_process_payment').trigger('click');
        }else if(p_m_name == 'sisowmc'){
            $(dialogContainer+'#sisowmistercash_process_payment').trigger('click');
        }else if(p_m_name == 'dineromail'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'add_gopay_new'){
            createPaymentPop();
            $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection
            $('#gopay-payment-form .payment_module').css('display','none');
        }else if(p_m_name == 'khipupayment' || p_m_name == 'paynl_paymentmethods' || p_m_name == 'mollie' || p_m_name == 'moneybookers' || p_m_name == 'faspay' || p_m_name == 'paynlpaymentmethods' || p_m_name == 'epay' || p_m_name == 'quickpay'){
            createPaymentPop();
            $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection
        }else if(p_m_name == 'offlinecreditcard'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'mobilpay_cc'){
            $('#mobilpay_cc_form').submit();
        }
        else if(p_m_name == 'paysera'  || p_m_name == 'pagofacil'){
            location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
        }else if(p_m_name == 'bankwire' || p_m_name == 'mercadopago' || p_m_name == 'add_bankwire' || p_m_name == 'swipp' || p_m_name == 'boleto' || p_m_name == 'pstransparenteloja5' || p_m_name == 'spsbradesco' || p_m_name == 'cielows' || p_m_name == 'edinar' || p_m_name == 'clictopay' || p_m_name == 'allpay' || p_m_name == 'pay2go' || p_m_name == 'cash' || p_m_name == 'postfinance' || p_m_name == 'pagseguro' || p_m_name == 'braintreejs' || p_m_name == 'bcash' || p_m_name == 'invoicepayment' || p_m_name == 'przelewy24' || p_m_name == 'prestalia_cashondelivery' ||  p_m_name == 'virtpaypayment' || p_m_name == 'cashondeliveryfeeplus' || p_m_name == 'pagonlineimprese' || p_m_name == 'mokejimai' || p_m_name == 'payplug' || p_m_name == 'seurcashondelivery' || p_m_name == 'cashondeliveryplus' || p_m_name == 'universalpay' || p_m_name == 'mandiri' || p_m_name == 'bni' || p_m_name == 'bca' || p_m_name == 'veritranspay' || p_m_name == 'przelewy24' || p_m_name == 'transbancaria' || p_m_name == 'cashondeliveryplusmax' || p_m_name == 'multibanco' || p_m_name == 'ceca' || p_m_name == 'dotpay' || p_m_name == 'postepay' || p_m_name == 'paypaladvanced' || p_m_name == 'trustly' || p_m_name == 'billmateinvoice' || p_m_name == 'billmatepartpayment' || p_m_name == 'cheque' || p_m_name == 'westernunion' ||  p_m_name == 'paysondirect' || p_m_name == 'mercadoc' || p_m_name == 'boletosantanderpro' || p_m_name == 'payu' ||  p_m_name == 'librapay' ||  p_m_name == 'secureframe' || p_m_name == 'cashondelivery' || p_m_name == 'compropago' || p_m_name == 'checkout' || p_m_name == 'megashoppay' || p_m_name == 'payulatam' || p_m_name == 'zipcheck' || p_m_name == 'megareembolso' || p_m_name == 'deluxecodfees' || p_m_name == 'payinstore' || p_m_name == 'codfee' || p_m_name == 'obsredsys' || p_m_name == 'hipay' || p_m_name == 'psphipay' || p_m_name == 'finanziamento'){
            if($(dialogContainer+'form').length){
                createPaymentPop();
                $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection
            }
            else if(p_m_name == 'paysondirect'){
                disableBtn(); }
            else {
                location.href = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();
            }
        }else if(p_m_name == 'redsys'){
            $('#redsys_form').submit();
        }else if(p_m_name == 'cecatpv'){
            $('#cecatpv_form').submit();
        }else if(p_m_name == 'firstdata'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            $('#firstdata_submit').trigger('click');
        }else if(p_m_name == 'conektatarjeta'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            $('#conekta-submit-button').trigger('click');
        }else if(p_m_name == 'stripepro'){
            $('#stripe-proceed-button').trigger('click');
        }else if(p_m_name == 'braintreejs_backup'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            //$('#braintree-dropin-form').submit();
        }else if(p_m_name == 'stripejs'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            $('.stripe-submit-button').trigger('click');
        }else if(p_m_name == 'twocheckout'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            //('#twocheckoutCCForm input.button').trigger('click');
            $('#twocheckoutCCForm').submit();
        }else if(p_m_name == 'brinkscheckout'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            $('#twocheckoutCCForm #submit_payment').trigger('click');
        }else if(p_m_name == 'ewayrapid'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            $('#processPayment').trigger('click');
        }else if(p_m_name == 'npaypalpro'){
            hide_progress(); // to hide progress bar in case some error occur in first data payment form
            $('.paypalpro-submit-button').trigger('click');
        }else if(p_m_name == 'authorizeaim'){
            hide_progress(); // to hide progress bar in case some error occur in authorizeaim payment form
            $('#asubmit').trigger('click');
        }else if(p_m_name == 'iupay'){
            $('#iupay_form').submit();
        }else if(p_m_name == 'cuatrob'){
            $('#cuatrob_form').submit();
        }else if(p_m_name == 'gopay'){
            $('#gopay_form').submit();
        }else if($(dialogContainer+'button').length){
            createPaymentPop();
            $('#velsof_payment_dialog .velsof_action_section').show(); // @nitin 27 July - Important to show proceed button if it is hide in previous payment method selection
        }else{
            if($(dialogContainer+'button').length){
                $(dialogContainer+'button').trigger('click');
            }else if($(dialogContainer+'form').length){
                $(dialogContainer+'form').trigger('click');
            }else if($(dialogContainer+'a').length){
                $(dialogContainer+'a').trigger('click');
            }else{
                if($('#payment_method_html').length){
                    $('#payment_method_html').html(payment_method_html);
                }
                alert('Payment Processing Error');
            }
        }
    }
}

function display_progress(value){
    // $.busyLoadFull("show", {
    //     background: "rgba(0, 0, 0, 0.86)"
    // });
}

function hide_progress(){
    // $.busyLoadFull("hide");
}

function getCounrtryAndIdDelivery(){
    var id_country = $('#id_country').find(":selected").val();
    var id_address_delivery = $('#shipping_address_id').find(":selected").val();
    var arr = [];
    arr.push(id_country);
    arr.push(id_address_delivery);
    return arr;
}

function checkStateVisibility(selected_country, element){
    var has_states = false;
    for (var id_country in countries){
        if(id_country == selected_country){
            if(countries[id_country]['contains_states'] == 1){
                has_states = true;
            }
        }
    }

    return has_states;
}

function display_progress(value){
    $.busyLoadFull("show", {
        background: "rgba(0, 0, 0, 0.86)"
    });
}

function hide_progress(){
    $.busyLoadFull("hide");
}

function statelist(selected_country, selected_state, element){
    var state_html = ''; //<option value="0">Select State</option>
    var has_states = false;
    var show_state = false;
    for (var id_country in countries){
        if(id_country == selected_country){
            if(countries[id_country]['contains_states'] == 1){
                has_states = true;
                for (var i in countries[id_country]['states']){
                    if(countries[id_country]['states'][i]['id_state'] == selected_state){
                        state_html += '<option value="'+countries[id_country]['states'][i]['id_state']+'" selected="selected" >'+countries[id_country]['states'][i]['name']+'</option>';
                    }else{
                        state_html += '<option value="'+countries[id_country]['states'][i]['id_state']+'">'+countries[id_country]['states'][i]['name']+'</option>';
                    }

                }
            }
        }

    }


    if(has_states){
        $(element).html(state_html);
        $(element).show();
    }else{
        $(element).hide();
    }
}



function updateShippingExtra(jsonData){
    var checked_termCondition = (($('#myopccheckout-agree input[type=checkbox]').is(':checked'))? 1 : 0);

    if(checked_termCondition == 1){
        $('#myopccheckout-agree input[type=checkbox]').attr('checked', 'checked');
    }
}


function applyCouponCode(){
    $.ajax( {
        type: "POST",
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime()+'&ajax=true',
        async: true,
        cache: false,
        data: $('#promo-code input'),
        dataType: 'json',
        beforeSend: function() {
            $('#cart_update_warning .permanent-warning').remove();
            $('#confirmLoader').show();
        },
        complete: function() {
            $('#confirmLoader').hide();
        },
        success: function( json ) {
            if(json['success'] != undefined){
                $.gritter.add({
                    title: notification,
                    text: json['success'],
                    //	image: '',
                    class_name:'gritter-success',
                    sticky: false,
                    time: '3000'
                });
                $('#discount_name').attr('value','');
                getCarrierList();
            }else if(json['error'] != undefined){
                $('#cart_update_warning').html('<div class="permanent-warning">'+json['error']+'</div>');
            }
            loadCart();
            // $('#highlighted_cart_rules').html(json['cart_rule']);
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var error = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#cart_update_warning').html('<div class="permanent-warning">'+error+'</div>');
        }
    } );
}


function updateCartSummary(json){
    ajaxCart.refresh();
    var i;

    // Update discounts
    var discount_count = 0;
    for(var e in json.discounts)
    {
        discount_count++;
        break;
    }

    $('#cart-promo-code').html('');

    if (discount_count) {

        //Update Discounts
        var total_discount_html = '';
        var total_discount_value = 0;
        if (priceDisplayMethod !== 0) {
            total_discount_value = '-' + formatCurrency(json.total_discounts_tax_exc, currencyFormat, currencySign, currencyBlank);
        } else {
            total_discount_value = '-' + formatCurrency(json.total_discounts, currencyFormat, currencySign, currencyBlank);

        }

        var individual_discount_html = '';

        for (var i in json.discounts) {
            var discount_value = 0;
            if (priceDisplayMethod == 0) {
                discount_value = formatCurrency(json.discounts[i].value_real * -1, currencyFormat, currencySign, currencyBlank);
            } else {
                discount_value = formatCurrency(json.discounts[i].value_tax_exc * -1, currencyFormat, currencySign, currencyBlank);
            }
            individual_discount_html += '<tr id="cart_discount_' + json.discounts[i].id_discount + '" class="cart_discount" >'
                + '<td class="cart_discount_name"><b>' + json.discounts[i].name + '</b></td>'
                + '<td class="price_discount_del text-center"><a href="javascript:void(0)" onclick="removeDiscount(' + json.discounts[i].id_discount + ')"><i class="icon-trash"></i></a></td>'
                + '<td class="cart_discount_price"><span class="price-discount price">' + discount_value + '</span></td>'
                + '</tr>';
        }

        if (priceDisplayMethod !== 0)
            $('#total_discount').html('-' + formatCurrency(json.total_discounts_tax_exc, currencyFormat, currencySign, currencyBlank));
        else
            $('#total_discount').html('-' + formatCurrency(json.total_discounts, currencyFormat, currencySign, currencyBlank));
    } else {
        $('#total_discount').html(formatCurrency(0, currencyFormat, currencySign, currencyBlank));
    }

    $('#cart-promo-code').html(individual_discount_html);

    if (priceDisplayMethod !== 0)
        $('#total_product').html(formatCurrency(json.total_products, currencyFormat, currencySign, currencyBlank));
    else
        $('#total_product').html(formatCurrency(json.total_products_wt, currencyFormat, currencySign, currencyBlank));
    if (json.total_shipping > 0)
    {
        if (priceDisplayMethod !== 0)
            $('#total_shipping').html(formatCurrency(json.total_shipping_tax_exc, currencyFormat, currencySign, currencyBlank));
        else
            $('#total_shipping').html(formatCurrency(json.total_shipping, currencyFormat, currencySign, currencyBlank));
    } else {
        $('#total_shipping').html("Free Shipping");
    }

    $('#total_price').html(formatCurrency(json.total_price, currencyFormat, currencySign, currencyBlank));
    $('#total_price_without_tax').html(formatCurrency(json.total_price_without_tax, currencyFormat, currencySign, currencyBlank));
    $('#total_tax').html(formatCurrency(json.total_tax, currencyFormat, currencySign, currencyBlank));

}

function removeDiscount(discount_id){
    $.ajax( {
        type: "POST",
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        data: '&ajax=true&deleteDiscount='+discount_id,
        dataType: 'json',
        beforeSend: function() {
            // $('#cart_update_warning .permanent-warning').remove();
            // $('#confirmLoader').show();
        },
        complete: function() {
            // $('#confirmLoader').hide();
        },
        success: function( json ) {
            if(json['success'] != undefined){
                // $.gritter.add({
                //     title: notification,
                //     text: json['success'],
                //     //	image: '',
                //     class_name:'gritter-success',
                //     sticky: false,
                //     time: '3000'
                // });
                // $('#discount_name').attr('value','');
                loadCart();
            }else if(json['error'] != undefined){
                // $('#cart_update_warning').html('<div class="permanent-warning">'+json['error']+'</div>');
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            var error = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            $('#cart_update_warning').html('<div class="permanent-warning">'+error+'</div>');
        }
    } );
}

function loadCart(){
    $.ajax({
        type: 'POST',
        headers: { "cache-control": "no-cache" },
        url: $('#module_url').val() + '&rand=' + new Date().getTime(),
        async: true,
        cache: false,
        dataType : "json",
        data: 'ajax=true'
        +'&method=loadCart&token=' + static_token,
        beforeSend: function() {
            // $('#cart_update_warning .permanent-warning').remove();
            // $('#confirmLoader').show();
        },
        success: function(jsonData)
        {
            // $('#confirmLoader').hide();
            updateCartSummary(jsonData);

            //Update Payment Information
            // if($('input:radio[name="payment_method"]:checked').length){
            //     actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
            // }else{
            //     $('input:radio[name="payment_method"]').first().attr('checked', 'checked');
            //     $('input:radio[name="payment_method"]').first().parent().addClass('checked');
            //     actionOnPaymentSelect($('input:radio[name="payment_method"]:checked'));
            // }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            // var errors = sprintf(ajaxRequestFailedMsg, XMLHttpRequest, textStatus);
            // $('#cart_update_warning').html('<div class="permanent-warning">'+errors+'</div>');
        }
    });
}

function confirmOrder() {
    var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
    var dialogContainer = '#payment-form .content-dialog ';
    // if(payment_module_name == 'bankwire' || payment_module_name == 'invoicepayment'){
    //     $('#velsof_payment_container .velsof_action_section').css('display','none'); //@Nitin Jain, 1-Oct-2015, to hide proceed button on click, because if clickd twice it was showing error.
    // }else if(payment_module_name == 'add_gopay_new'){
    //     document.getElementById('gopay-payment-form').submit(); return false;
    // }
    if (payment_module_name == 'bankwire' || payment_module_name == 'cashondelivery' || payment_module_name == 'add_bankwire' || payment_module_name == 'cielows' || payment_module_name == 'spsbradesco' || payment_module_name == 'pstransparenteloja5' || payment_module_name == 'boleto' || payment_module_name == 'edinar' || payment_module_name == 'clictopay' || payment_module_name == 'allpay' || payment_module_name == 'pay2go' || payment_module_name == 'cash' || payment_module_name == 'postfinance' || payment_module_name == 'pagseguro' || payment_module_name == 'bcash' || payment_module_name == 'braintreejs' || payment_module_name == 'invoicepayment' || payment_module_name == 'przelewy24' || payment_module_name == 'prestalia_cashondelivery' || payment_module_name == 'virtpaypayment' || payment_module_name == 'cashondeliveryfeeplus' || payment_module_name == 'pagonlineimprese' || payment_module_name == 'mokejimai' || payment_module_name == 'payplug' || payment_module_name == 'seurcashondelivery' || payment_module_name == 'cashondeliveryplus' || payment_module_name == 'universalpay' || payment_module_name == 'mandiri' || payment_module_name == 'bni' || payment_module_name == 'bca' || payment_module_name == 'veritranspay' || payment_module_name == 'przelewy24' || payment_module_name == 'transbancaria' || payment_module_name == 'cashondeliveryplusmax' || payment_module_name == 'multibanco' || payment_module_name == 'ceca' || payment_module_name == 'dotpay' || payment_module_name == 'pagofacil' || payment_module_name == 'postepay' || payment_module_name == 'paysera' || payment_module_name == 'offlinecreditcard' || payment_module_name == 'paypaladvanced' || payment_module_name == 'trustly' || payment_module_name == 'cheque' || payment_module_name == 'deluxecodfees') {
        if ($(dialogContainer + 'form').length) {
            $(dialogContainer + 'form').submit();
        } else {
            location.href = $('#' + $('input:radio[name="payment_method"]:checked').attr('value') + '_name').val();
        }
    }
}

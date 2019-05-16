/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$(document).ready(function () {

    $('.free-disabled').closest('.form-group').addClass('free-disabled').parent().closest('.form-group').addClass('free-disabled');
    $("input[name='KB_MARKETPLACE_CSS']").closest('.form-group').addClass('free-disabled');
    $("input[name='KB_MARKETPLACE_JS']").closest('.form-group').addClass('free-disabled');
    $("input[name='kbmp_seller_listing_meta_keywords']").closest('.form-group').addClass('free-disabled');
    $("input[name='kbmp_seller_listing_meta_description']").closest('.form-group').addClass('free-disabled');
    $("input[name='kbmp_seller_agreement']").closest('.form-group').addClass('free-disabled');
    $("input[name='kbmp_seller_order_email_template']").closest('.form-group').addClass('free-disabled');

    $("#kb_buy_link").insertAfter(".col-lg-12:eq( 0 )");

});
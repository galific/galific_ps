/**
 * @license
 */
/* global setUserSetting, ajaxurl, commonL10n, alert, confirm, pagenow */
(function ($) {

	function tabsInvoiceAddress(){
		$("#tabs-invoice-address .tab-contents .tab-content").hide(); // Initially hide all content
        $("#tabs-invoice-address .nav-tab-buttons li:first").addClass("current"); // Activate first tab
        $("#tabs-invoice-address .tab-contents .tab-content:first").show(); // Show first tab content

        $('#tabs-invoice-address .nav-tab-buttons li a').click(function(e) {
            e.preventDefault();
            $("#tabs-invoice-address .tab-contents .tab-content").hide(); //Hide all content
            $("#tabs-invoice-address .nav-tab-buttons li").removeClass("current"); //Reset id's
            $(this).parent().addClass("current"); // Activate this
            $($(this).attr('href')).fadeIn(); // Show content for current tab
        });
		$('#check-billing-show').change(function() {
			if($(this).is(":checked")) {
				$(this).parents("#billing-address").find(".billing-address-container").addClass("active");
				$(this).parents(".uniform-checkbox").addClass("checked");
				
			} else {
				$(this).parents("#billing-address").find(".billing-address-container").removeClass("active");
				$(this).parents(".uniform-checkbox").removeClass("checked");
			}      
		});
		$('#is-create-acc-checkbox').change(function() {
			if($(this).is(":checked")) {
                $('#is_new_customer').val('1');
				$(this).parents(".field-group-create-acc").find(".row").show();
				$(this).parents(".uniform-checkbox").addClass("checked");
			} else {
                $('#is_new_customer').val('0');
                $('#submitAccount').attr({id : 'submitGuestAccount', name : 'submitGuestAccount'});
				$(this).parents(".field-group-create-acc").find(".row").hide();
				$(this).parents(".uniform-checkbox").removeClass("checked");
			}      
		});
	}
	
	function radioIsChecked(Class){
		$('.quick-checkout ' + Class + ' input[type=radio]').each(function() {
			if($(this).is(':checked')){
				$(this).parents(".uniform-radio").addClass("checked");
			}  
		});
		$('.quick-checkout ' + Class + ' input[type=radio]').change(function() {
			if($(this).is(':checked')){
				$('.quick-checkout ' + Class).find(".uniform-radio").removeClass("checked");
				$(this).parents(".uniform-radio").addClass("checked");
			}    
		});
	}
	
	$(document).ready(function () {
		tabsInvoiceAddress();
		radioIsChecked('.shipping-methods');
		radioIsChecked('.payment-methods');
	});
})(jQuery);

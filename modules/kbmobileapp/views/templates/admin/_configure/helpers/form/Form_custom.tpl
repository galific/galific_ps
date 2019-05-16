{extends file="helpers/form/form.tpl"}
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
{block name="defaultForm"}
<script>
    var success_message = "{l s='Data has been Saved successfully.' mod='kbmobileapp' }";
    var min_category_limit = "{l s='Need to add value in atleast 4 categories.' mod='kbmobileapp' }";
    var error_check_message = "{l s='Check the errors in form.' mod='kbmobileapp' }";
    var component_add = "{l s='Component Added to layout.' mod='kbmobileapp' }";
    var component_delete = "{l s='Component Removed from layout successfully.' mod='kbmobileapp' }";
    var limit_reached = "{l s='Limit Reached ! Can not add more than 20 elements in a layout.' mod='kbmobileapp' }";
    var layout_add_message = "{l s='New layout successfully added.' mod='kbmobileapp' }";
    var layout_name_update_message = "{l s='layout Name Updated successfully.' mod='kbmobileapp' }";
    var Layout_delete_message = "{l s='layout Deleted successfully.' mod='kbmobileapp' }";
    var position_update = "{l s='Components order saved successfully.' mod='kbmobileapp' }";
    var banner_delete_message = "{l s='Banner/slider Deleted successfully.' mod='kbmobileapp' }";
    var category_image_delete_message = "{l s='Category image deleted successfully.' mod='kbmobileapp' }";
    
    
    var active_languages = {$active_languages};
    var num_of_lang="{$num_of_lang|escape:'htmlall':'UTF-8'}";
    var mod_dir="{$mod_dir|escape:'htmlall':'UTF-8'}";
    var version ={$version|escape:'htmlall':'UTF-8'};
    var action_page ="{$action_page|escape:'quotes':'UTF-8'}";
    var general_settings = "{$general_settings|escape:'htmlall':'UTF-8'}";
    var push_notification_settings = "{$push_notification_settings|escape:'htmlall':'UTF-8'}";
    var push_notification_history = "{$push_notification_history|escape:'htmlall':'UTF-8'}";
    var payments_settings = "{$payments_settings|escape:'htmlall':'UTF-8'}";
    var banners_settings = "{$banners_settings|escape:'htmlall':'UTF-8'}";
    var sliders_settings = "{$slidders_settings|escape:'htmlall':'UTF-8'}";
    var close_new_entry = "{l s='Close' mod='kbmobileapp' }";
    var add_new_entry = "{l s='Add New Method' mod='kbmobileapp' }";
    // changes by rishabh jain for layout tab
    var add_new_layout = "{l s='Add New layout' mod='kbmobileapp' }";
    var send_notification_text = "{l s='Send Notification' mod='kbmobileapp' }";
    var one_chat_enabled = "{l s='At a time only one chat option can be enabled among whatsapp or Zopim' mod='kbmobileapp' }";
    var ajaxaction = "{$action|escape:'quotes':'UTF-8'}";
    var default_language_id = "{$default_language_id|escape:'quotes':'UTF-8'}";
    var default_language_code = "{$default_language_code|escape:'quotes':'UTF-8'}";
    var invalid_file_format_txt = "{l s='Invalid File Format' mod='kbmobileapp' }";
    var file_size_error_txt = "{l s='File size must be less than 2MB' mod='kbmobileapp' }";
    var invalid_file_txt = "{l s='Invalid File.' mod='kbmobileapp' }";
    var cod_txt = "{l s='COD' mod='kbmobileapp' }";
    var paypal_txt = "{l s='PayPal' mod='kbmobileapp' }";
    var confirmation_txt = "{l s='Are you sure you want to delete this method' mod='kbmobileapp' }";
    var request_error_txt = "{l s='Request Error' mod='kbmobileapp' }";
    var send_notification_txt = "{l s='Send Notification' mod='kbmobileapp' }";
    var select_image_txt = "{l s='Please select image' mod='kbmobileapp' }";
    var select_category_txt = "{l s='Please select category' mod='kbmobileapp' }";
    var provide_product_name_txt = "{l s='Please enter product name' mod='kbmobileapp' }";
    var image_url_error_txt = "{l s='Please provide image url' mod='kbmobileapp' }";
    var select_image_type_txt = "{l s='Please select image type' mod='kbmobileapp' }";
    var one_chat_enabled = "{l s='At a time only one chat option can be enabled among whatsapp or Zopim' mod='kbmobileapp' }";
    var push_notification_msg_txt = "{l s='Please enter message for push notification' mod='kbmobileapp' }";
    var push_notification_title_txt = "{l s='Please enter push notification title' mod='kbmobileapp' }";
    var firebase_server_key_txt = "{l s='Please save firebase server key first' mod='kbmobileapp' }";
    var payment_name_txt = "{l s='Please provide payment name in' mod='kbmobileapp' }";
    var client_id_txt = "{l s='Please provide Client Id' mod='kbmobileapp' }";
    var select_methods_txt = "{l s='Please Select Payment Methods' mod='kbmobileapp' }";
    var invalid_value_txt = "{l s='Tags not allowed' mod='kbmobileapp' }";
    var default_tab="{$default_tab|escape:'htmlall':'UTF-8'}";
    var select_file_txt = "{l s='Please select file' mod='kbmobileapp' }";
    var validating_key_txt = "{l s='Validating key...' mod='kbmobileapp' }";
    var valid_key_txt = "{l s='Valid key' mod='kbmobileapp' }";
    var invalid_key_txt = "{l s='Invalid Key' mod='kbmobileapp' }";
    var limit_alert = "{l s='The max Limit(20) of adding component in the layour is reached.' mod='kbmobileapp' }";
    var loader_url = "{$loader}/show_loader.gif";
    
    velovalidation.setErrorLanguage({
            empty_fname: "{l s='Please enter First name.' mod='kbmobileapp'}",
            maxchar_fname: "{l s='First name cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_fname: "{l s='First name cannot be less than # characters.' mod='kbmobileapp'}",
            empty_mname: "{l s='Please enter middle name.' mod='kbmobileapp'}",
            maxchar_mname: "{l s='Middle name cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_mname: "{l s='Middle name cannot be less than # characters.' mod='kbmobileapp'}",
            only_alphabet: "{l s='Only alphabets are allowed.' mod='kbmobileapp'}",
            empty_lname: "{l s='Please enter Last name.' mod='kbmobileapp'}",
            maxchar_lname: "{l s='Last name cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_lname: "{l s='Last name cannot be less than # characters.' mod='kbmobileapp'}",
            alphanumeric: "{l s='Field should be alphanumeric.' mod='kbmobileapp'}",
            empty_pass: "{l s='Please enter Password.' mod='kbmobileapp'}",
            maxchar_pass: "{l s='Password cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_pass: "{l s='Password cannot be less than # characters.' mod='kbmobileapp'}",
            specialchar_pass: "{l s='Password should contain atleast 1 special character.' mod='kbmobileapp'}",
            alphabets_pass: "{l s='Password should contain alphabets.' mod='kbmobileapp'}",
            capital_alphabets_pass: "{l s='Password should contain atleast 1 capital letter.' mod='kbmobileapp'}",
            small_alphabets_pass: "{l s='Password should contain atleast 1 small letter.' mod='kbmobileapp'}",
            digit_pass: "{l s='Password should contain atleast 1 digit.' mod='kbmobileapp'}",
            empty_field: "{l s='Field cannot be empty.' mod='kbmobileapp'}",
            number_field: "{l s='You can enter only numbers.' mod='kbmobileapp'}",            
            positive_number: "{l s='Number should be greater than 0.' mod='kbmobileapp'}",
            maxchar_field: "{l s='Field cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_field: "{l s='Field cannot be less than # character(s).' mod='kbmobileapp'}",
            empty_email: "{l s='Please enter Email.' mod='kbmobileapp'}",
            validate_email: "{l s='Please enter a valid Email.' mod='kbmobileapp'}",
            empty_country: "{l s='Please enter country name.' mod='kbmobileapp'}",
            maxchar_country: "{l s='Country cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_country: "{l s='Country cannot be less than # characters.' mod='kbmobileapp'}",
            empty_city: "{l s='Please enter city name.' mod='kbmobileapp'}",
            maxchar_city: "{l s='City cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_city: "{l s='City cannot be less than # characters.' mod='kbmobileapp'}",
            empty_state: "{l s='Please enter state name.' mod='kbmobileapp'}",
            maxchar_state: "{l s='State cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_state: "{l s='State cannot be less than # characters.' mod='kbmobileapp'}",
            empty_proname: "{l s='Please enter product name.' mod='kbmobileapp'}",
            maxchar_proname: "{l s='Product cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_proname: "{l s='Product cannot be less than # characters.' mod='kbmobileapp'}",
            empty_catname: "{l s='Please enter category name.' mod='kbmobileapp'}",
            maxchar_catname: "{l s='Category cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_catname: "{l s='Category cannot be less than # characters.' mod='kbmobileapp'}",
            empty_zip: "{l s='Please enter zip code.' mod='kbmobileapp'}",
            maxchar_zip: "{l s='Zip cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_zip: "{l s='Zip cannot be less than # characters.' mod='kbmobileapp'}",
            empty_username: "{l s='Please enter Username.' mod='kbmobileapp'}",
            maxchar_username: "{l s='Username cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_username: "{l s='Username cannot be less than # characters.' mod='kbmobileapp'}",
            invalid_date: "{l s='Invalid date format.' mod='kbmobileapp'}",
            maxchar_sku: "{l s='SKU cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_sku: "{l s='SKU cannot be less than # characters.' mod='kbmobileapp'}",
            invalid_sku: "{l s='Invalid SKU format.' mod='kbmobileapp'}",
            empty_sku: "{l s='Please enter SKU.' mod='kbmobileapp'}",
            validate_range: "{l s='Number is not in the valid range. It should be betwen # and ##' mod='kbmobileapp'}",
            empty_address: "{l s='Please enter address.' mod='kbmobileapp'}",
            minchar_address: "{l s='Address cannot be less than # characters.' mod='kbmobileapp'}",
            maxchar_address: "{l s='Address cannot be greater than # characters.' mod='kbmobileapp'}",
            empty_company: "{l s='Please enter company name.' mod='kbmobileapp'}",
            minchar_company: "{l s='Company name cannot be less than # characters.' mod='kbmobileapp'}",
            maxchar_company: "{l s='Company name cannot be greater than # characters.' mod='kbmobileapp'}",
            invalid_phone: "{l s='Phone number is invalid.' mod='kbmobileapp'}",
            empty_phone: "{l s='Please enter phone number.' mod='kbmobileapp'}",
            minchar_phone: "{l s='Phone number cannot be less than # characters.' mod='kbmobileapp'}",
            maxchar_phone: "{l s='Phone number cannot be greater than # characters.' mod='kbmobileapp'}",
            empty_brand: "{l s='Please enter brand name.' mod='kbmobileapp'}",
            maxchar_brand: "{l s='Brand name cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_brand: "{l s='Brand name cannot be less than # characters.' mod='kbmobileapp'}",
            empty_shipment: "{l s='Please enter Shimpment.' mod='kbmobileapp'}",
            maxchar_shipment: "{l s='Shipment cannot be greater than # characters.' mod='kbmobileapp'}",
            minchar_shipment: "{l s='Shipment cannot be less than # characters.' mod='kbmobileapp'}",
            invalid_ip: "{l s='Invalid IP format.' mod='kbmobileapp'}",
            invalid_url: "{l s='Invalid URL format.' mod='kbmobileapp'}",
            empty_url: "{l s='Please enter URL.' mod='kbmobileapp'}",
            valid_amount: "{l s='Field should be numeric.' mod='kbmobileapp'}",
            valid_decimal: "{l s='Field can have only upto two decimal values.' mod='kbmobileapp'}",
            max_email: "{l s='Email cannot be greater than # characters.' mod='kbmobileapp'}",
            specialchar_zip: "{l s='Zip should not have special characters.' mod='kbmobileapp'}",
            specialchar_sku: "{l s='SKU should not have special characters.' mod='kbmobileapp'}",
            max_url: "{l s='URL cannot be greater than # characters.' mod='kbmobileapp'}",
            valid_percentage: "{l s='Percentage should be in number.' mod='kbmobileapp'}",
            between_percentage: "{l s='Percentage should be between 0 and 100.' mod='kbmobileapp'}",
            maxchar_size: "{l s='Size cannot be greater than # characters.' mod='kbmobileapp'}",
            specialchar_size: "{l s='Size should not have special characters.' mod='kbmobileapp'}",
            specialchar_upc: "{l s='UPC should not have special characters.' mod='kbmobileapp'}",
            maxchar_upc: "{l s='UPC cannot be greater than # characters.' mod='kbmobileapp'}",
            specialchar_ean: "{l s='EAN should not have special characters.' mod='kbmobileapp'}",
            maxchar_ean: "{l s='EAN cannot be greater than # characters.' mod='kbmobileapp'}",
            specialchar_bar: "{l s='Barcode should not have special characters.' mod='kbmobileapp'}",
            maxchar_bar: "{l s='Barcode cannot be greater than # characters.' mod='kbmobileapp'}",
            positive_amount: "{l s='Field should be positive.' mod='kbmobileapp'}",
            maxchar_color: "{l s='Color could not be greater than # characters.' mod='kbmobileapp'}",
            invalid_color: "{l s='Color is not valid.' mod='kbmobileapp'}",
            specialchar: "{l s='Special characters are not allowed.' mod='kbmobileapp'}",
            script: "{l s='Script tags are not allowed.' mod='kbmobileapp'}",
            style: "{l s='Style tags are not allowed.' mod='kbmobileapp'}",
            iframe: "{l s='Iframe tags are not allowed.' mod='kbmobileapp'}",
            not_image: "{l s='Uploaded file is not an image.' mod='kbmobileapp'}",
            image_size: "{l s='Uploaded file size must be less than #.' mod='kbmobileapp'}",
            html_tags: "{l s='Field should not contain HTML tags.' mod='kbmobileapp'}",
            number_pos: "{l s='You can enter only positive numbers.' mod='kbmobileapp'}",
            invalid_separator:"{l s='Invalid comma (#) separated values.' mod='kbmobileapp'}",
});
</script>
{if $version eq 1.6}
        <div class="row" id="kbmobileapp_configuration_form">
            
            
            <div class="productTabs col-lg-2 col-md-3">
                <div class="list-group">
                    {$i=1}
                    {foreach $available_tabs key=numStep item=tab}
                            <a class="list-group-item {if $tab.selected|escape:'htmlall':'UTF-8'}{if $version eq 1.5}selected{else}active{/if}{/if}" id="link-{$tab.id|escape:'htmlall':'UTF-8'}" onclick="change_tab(this,{$i|escape:'htmlall':'UTF-8'});">
                                {$tab.name|escape:'htmlall':'UTF-8'}
                                <i class="icon-exclamation-circle" style="display:none;"></i>
                            </a>
                            {$i=$i+1}
                    {/foreach}
                </div>
            </div>
            <div class="vss-add-new" style="display:none">
                {$form_add_new} {*Variable contains html content, escape not required*}
            </div>
            
                {$form} {*Variable contains html content, escape not required*}
                {$form2} {*Variable contains html content, escape not required*}
                {$notification_button} {*Variable contains html content, escape not required*}
                <div class="vss-send-new-notification" style="display:none">
                    {$form1} {*Variable contains html content, escape not required*}
                </div>            
                {$notification_list} {*Variable contains html content, escape not required*}
                <div class="vss-add-slider" style="display:none">
                    {$cancel_button} {*Variable contains html content, escape not required*}
                
                </div>
                {*{$slider_list} *}{*Variable contains html content, escape not required*}
                {*{$banner_list}*} {*Variable contains html content, escape not required*}
                {* changes by rishabh jain 
                to show slider list*}
                {*<div class="vss-add-new-layout" style="display:none">
                    {$form3} 
                </div>   *}
                {$add_new_layout_button}
                <div id="layout_list">
                {$layout_list} {*Variable contains html content, escape not required*}
                </div>
                {* changes over *}
                {$view} {*Variable contains html content, escape not required*}
                {$table} {*Variable contains html content, escape not required*}
                {$google_form} {*Variable contains html content, escape not required*}
                {$validatebuttonview} {*Variable contains html content, escape not required*}
                {$facebook_form} {*Variable contains html content, escape not required*}
                
        </div>
        {else}
            <div class="row" id="kbmobileapp_configuration_form">
            <div>
		<div class="productTabs col-lg-2 col-md-3" >
			<ul class="tab">
			{*todo href when nojs*}
            {$i=1}
			{foreach $available_tabs key=numStep item=tab}
				<li class="tab-row">
					<a class="tab-page {if $tab.selected|escape:'htmlall':'UTF-8'}selected{/if}" id="link-{$tab.id|escape:'htmlall':'UTF-8'}" onclick="change_tab(this,{$i|escape:'htmlall':'UTF-8'});">
                                            {$tab.name|escape:'htmlall':'UTF-8'}
                                            <i class="icon-exclamation-circle" style="display:none;"></i>
                                        </a>
                    {$i=$i+1}
				</li>
			{/foreach}
			</ul>
		</div>
            <div class="vss-add-new" style="display:none">
                {$form_add_new} {*Variable contains html content, escape not required*}
            </div>
                {$form} {*Variable contains html content, escape not required*}
                {$form2} {*Variable contains html content, escape not required*}
                {$notification_button} {*Variable contains html content, escape not required*}
                <div class="vss-send-new-notification" style="display:none">
                    {$form1} {*Variable contains html content, escape not required*}
                </div>
                {$notification_list} {*Variable contains html content, escape not required*}
                <div class="vss-add-slider" style="display:none">
                    {$cancel_button} {*Variable contains html content, escape not required*}
                    
                </div>
                {*{$slider_list}*} {*Variable contains html content, escape not required*}
                {*{$banner_list}*} {*Variable contains html content, escape not required*}
                {* changes by rishabh jain 
                to show slider list*}
                {$add_new_layout_button}
                <div id="layout_list">
                {$layout_list} {*Variable contains html content, escape not required*}
                </div>
                {* changes over *}
                {$view} {*Variable contains html content, escape not required*}
                {$table} {*Variable contains html content, escape not required*}
                {$google_form} {*Variable contains html content, escape not required*}
                {$validatebuttonview} {*Variable contains html content, escape not required*}
                {$facebook_form} {*Variable contains html content, escape not required*}
                
	</div>
            </div>
                {/if}
{/block}
    {*
    * DISCLAIMER
    *
    * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
    * versions in the future. If you wish to customize PrestaShop for your
    * needs please refer tohttp://www.prestashop.com for more information.
    * We offer the best and most useful modules PrestaShop and modifications for your online store.
    *
    * @category  PrestaShop Module
    * @author    knowband.com <support@knowband.com>
    * @copyright 2015 Knowband
    * @license   see file: LICENSE.txt
    *
    * Description
    *
    * Admin tpl file
    *}


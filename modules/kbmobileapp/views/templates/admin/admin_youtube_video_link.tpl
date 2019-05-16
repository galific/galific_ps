<div id="product-tab-content-kbmobileapp-youtube" class="product-tab-content" style="display: block;">
    <div id="kbmobileapp-product-youtube" class="panel product-tab">
	<h3>{l s='Product YouTube Video URL' mod='kbmobileapp'}</h3>
	<div class="form-group">
            <label class="control-label col-lg-3" for="product_youtube_url">
                <span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="{l s='Please enter the Product Video URL from YouTube here and save the product' mod='kbmobileapp'}">
                    {l s='YouTube URL' mod='kbmobileapp'}
                </span>
            </label>
            <div class="col-lg-9">
                <fieldset style="border:none;">
                    <input type="text" class="form-control" id="product_youtube_url" name="product_youtube_url" value="{if isset($velsof_yt_data['youtube_url']) && $velsof_yt_data['youtube_url'] != ''}{$velsof_yt_data['youtube_url']|escape:'htmlall':'UTF-8'}{/if}"/>
                </fieldset>
            </div>
	</div>
        <div class="panel-footer">
            <a href="{$velsof_product_back_url|escape:'htmlall':'UTF-8'}" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='kbmobileapp'}</a>
            <button type="submit" name="submitAddproduct" class="btn btn-default pull-right kbmobileapp-product-youtube-submit"><i class="process-icon-save"></i> {l s='Save' mod='kbmobileapp'}</button>
            <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right kbmobileapp-product-youtube-submit"><i class="process-icon-save"></i> {l s='Save and stay' mod='kbmobileapp'}</button>
        </div>
    </div>
</div>

<script type="text/javascript">
//error messages for velovalidation.js
velovalidation.setErrorLanguage({
    empty_fname: "{l s='Please enter First name.' mod='kbmobileapp'}",
    maxchar_fname: "{l s='First name cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_fname: "{l s='First name cannot be less than {%d} characters.'  mod='kbmobileapp'}",
    empty_mname: "{l s='Please enter middle name.' mod='kbmobileapp'}",
    maxchar_mname: "{l s='Middle name cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_mname: "{l s='Middle name cannot be less than {%d} characters.' mod='kbmobileapp'}",
    only_alphabet: "{l s='Only alphabets are allowed.' mod='kbmobileapp'}",
    empty_lname: "{l s='Please enter Last name.' mod='kbmobileapp'}",
    maxchar_lname: "{l s='Last name cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_lname: "{l s='Last name cannot be less than {%d} characters.' mod='kbmobileapp'}",
    alphanumeric: "{l s='Field should be alphanumeric.' mod='kbmobileapp'}",
    empty_pass: "{l s='Please enter Password.' mod='kbmobileapp'}",
    maxchar_pass: "{l s='Password cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_pass: "{l s='Password cannot be less than {%d} characters.' mod='kbmobileapp'}",
    specialchar_pass: "{l s='Password should contain atleast 1 special character.' mod='kbmobileapp'}",
    alphabets_pass: "{l s='Password should contain alphabets.' mod='kbmobileapp'}",
    capital_alphabets_pass: "{l s='Password should contain atleast 1 capital letter.' mod='kbmobileapp'}",
    small_alphabets_pass: "{l s='Password should contain atleast 1 small letter.' mod='kbmobileapp'}",
    digit_pass: "{l s='Password should contain atleast 1 digit.' mod='kbmobileapp'}",
    empty_field: "{l s='Field cannot be empty.' mod='kbmobileapp'}",
    number_field: "{l s='You can enter only numbers.' mod='kbmobileapp'}",
    positive_number: "{l s='Number should be greater than 0.' mod='kbmobileapp'}",
    maxchar_field: "{l s='Fields cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_field: "{l s='Fields cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    empty_email: "{l s='Please enter Email.' mod='kbmobileapp'}",
    validate_email: "{l s='Please enter a valid Email.' mod='kbmobileapp'}",
    empty_country: "{l s='Please enter country name.' mod='kbmobileapp'}",
    maxchar_country: "{l s='Country cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_country: "{l s='Country cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_city: "{l s='Please enter city name.' mod='kbmobileapp'}",
    maxchar_city: "{l s='City cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_city: "{l s='City cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_state: "{l s='Please enter state name.' mod='kbmobileapp'}",
    maxchar_state: "{l s='State cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_state: "{l s='State cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_proname: "{l s='Please enter product name.' mod='kbmobileapp'}",
    maxchar_proname: "{l s='Product cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_proname: "{l s='Product cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_catname: "{l s='Please enter category name.' mod='kbmobileapp'}",
    maxchar_catname: "{l s='Category cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_catname: "{l s='Category cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_zip: "{l s='Please enter zip code.' mod='kbmobileapp'}",
    maxchar_zip: "{l s='Zip cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_zip: "{l s='Zip cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_username: "{l s='Please enter zip code.' mod='kbmobileapp'}",
    maxchar_username: "{l s='Zip cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_username: "{l s='Zip cannot be less than {%d} characters.' mod='kbmobileapp'}",
    invalid_date: "{l s='Invalid date format.' mod='kbmobileapp'}",
    maxchar_sku: "{l s='SKU cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_sku: "{l s='SKU cannot be less than {%d} characters.' mod='kbmobileapp'}",
    invalid_sku: "{l s='Invalid SKU format.' mod='kbmobileapp'}",
    empty_sku: "{l s='Please enter SKU.' mod='kbmobileapp'}",
    validate_range: "{l s='Number is not in the valid range.' mod='kbmobileapp'}",
    empty_address: "{l s='Please enter address.' mod='kbmobileapp'}",
    minchar_address: "{l s='Address cannot be less than {%d} characters.' mod='kbmobileapp'}",
    maxchar_address: "{l s='Address cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    empty_company: "{l s='Please enter company name.' mod='kbmobileapp'}",
    minchar_company: "{l s='Company name cannot be less than {%d} characters.' mod='kbmobileapp'}",
    maxchar_company: "{l s='Company name cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    invalid_phone: "{l s='Phone number is invalid.' mod='kbmobileapp'}",
    empty_phone: "{l s='Please enter phone number.' mod='kbmobileapp'}",
    minchar_phone: "{l s='Phone number cannot be less than {%d} characters.' mod='kbmobileapp'}",
    maxchar_phone: "{l s='Phone number cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    empty_brand: "{l s='Please enter brand name.' mod='kbmobileapp'}",
    maxchar_brand: "{l s='Brand name cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_brand: "{l s='Brand name cannot be less than {%d} characters.' mod='kbmobileapp'}",
    empty_shipment: "{l s='Please enter Shimpment.' mod='kbmobileapp'}",
    maxchar_shipment: "{l s='Shipment cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    minchar_shipment: "{l s='Shipment cannot be less than {%d} characters.' mod='kbmobileapp'}",
    invalid_ip: "{l s='Invalid IP format.' mod='kbmobileapp'}",
    invalid_url: "{l s='Invalid URL format.' mod='kbmobileapp'}",
    empty_url: "{l s='Please enter URL.' mod='kbmobileapp'}",
    empty_amount: "{l s='Amount cannot be empty.' mod='kbmobileapp'}",
    valid_amount: "{l s='Amount should be numeric.' mod='kbmobileapp'}",
    max_email: "{l s='Email cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    specialchar_zip: "{l s='Zip should not have special characters.' mod='kbmobileapp'}",
    specialchar_sku: "{l s='SKU should not have special characters.' mod='kbmobileapp'}",
    max_url: "{l s='URL cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    valid_percentage: "{l s='Percentage should be in number.' mod='kbmobileapp'}",
    between_percentage: "{l s='Percentage should be between 0 and 100.' mod='kbmobileapp'}",
    maxchar_size: "{l s='Size cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    specialchar_size: "{l s='Size should not have special characters.' mod='kbmobileapp'}",
    specialchar_upc: "{l s='UPC should not have special characters.' mod='kbmobileapp'}",
    maxchar_upc: "{l s='UPC cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    specialchar_ean: "{l s='EAN should not have special characters.' mod='kbmobileapp'}",
    maxchar_ean: "{l s='EAN cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    specialchar_bar: "{l s='Barcode should not have special characters.' mod='kbmobileapp'}",
    maxchar_bar: "{l s='Barcode cannot be greater than {%d} characters.' mod='kbmobileapp'}",
    positive_amount: "{l s='Amount should be positive.' mod='kbmobileapp'}",
    maxchar_color: "{l s='Color could not be greater than {%d} characters.' mod='kbmobileapp'}",
    invalid_color: "{l s='Color is not valid.' mod='kbmobileapp'}",
    specialchar: "{l s='Special characters are not allowed.' mod='kbmobileapp'}",
    script: "{l s='Script tags are not allowed.' mod='kbmobileapp'}",
    style: "{l s='Style tags are not allowed.' mod='kbmobileapp'}",
    iframe: "{l s='Iframe tags are not allowed.' mod='kbmobileapp'}",
    not_image: "{l s='Uploaded file is not an image' mod='kbmobileapp'}",
    image_size: "{l s='Uploaded file size must be less than {%d}.' mod='kbmobileapp'}",
    html_tags: "{l s='Field should not contain HTML tags.' mod='kbmobileapp'}",
    number_pos: "{l s='You can enter only positive numbers.' mod='kbmobileapp'}",
    invalid_separator: "{l s='Invalid comma ({%d}) separated values.' mod='kbmobileapp'}"
});
</script>
            
{*
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
*
* Description
*
* Knowband Mobile App Product TPL File
*}
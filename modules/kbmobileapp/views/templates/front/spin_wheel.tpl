{*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
* @category  PrestaShop Module
*
*
* Description
*
* Gamification wheel for offering discount coupons.
*}
<link href="https://fonts.googleapis.com/css?family=Acme|Bree+Serif|Cinzel|Gloria+Hallelujah|Indie+Flower|Pacifico" rel="stylesheet">
<script>
    var velsofWheelHexCode = "#4497bb";
    var spinwheel_base_url = "{$spinwheel_base_url|escape:'htmlall':'UTF-8'}";
    var win_msg = "{l s='You Win ' mod='kbmobileapp'}";
    var loose_msg = "{l s='Not lucky today ! Try next time.' mod='kbmobileapp'}";
    var coupon_msg = "{l s='Use This Code To Redeem Your Offer.' mod='kbmobileapp'}";
    var show_pull_out = "{$show_popup|escape:'htmlall':'UTF-8'}";
    var email_recheck = "{$email_recheck|escape:'htmlall':'UTF-8'}";
    var wheel_device = "{$wheel_device|escape:'htmlall':'UTF-8'}";
    var email_check = "{l s='This email has been used already.' mod='kbmobileapp'}";
    var time_display = "";
    var scroll_display = "";
    var exit_display = "0";
    var hide_after = "";
    var copy_msg = "{l s='Code Copied' mod='kbmobileapp'}";
    var display_option = "{$display_option|escape:'htmlall':'UTF-8'}";
    var privacy_error_msg = "{l s='Please accept the mandatory terms of service and privacy policy.' mod='kbmobileapp'}";
    var Wheel_Display_Interval = "{$Wheel_Display_Interval|escape:'htmlall':'UTF-8'}";
    var show_fireworks = "{$show_fireworks|escape:'htmlall':'UTF-8'}";
    var spin_wheel_front_path = "{$spin_wheel_front_path}";
    {*variable contains HTML content, Can not escape this*}
    var email_only_msg = "{l s='Coupon code has been sent to your email id.' mod='kbmobileapp'}";
    var wheel_design = "{$wheel_design}";{*variable contains HTML content, Can not escape this*}
    var mediapath = "{$path}";{*variable contains URL content, Can not escape this*}
    var wheel_color = "{$wheel_color|escape:'htmlall':'UTF-8'}";
    var wheel_sound = "{$wheel_sound|escape:'htmlall':'UTF-8'}";
    var requireName = "{$req_cust_name|escape:'htmlall':'UTF-8'}";
    var custName = "{$cust_name|escape:'htmlall':'UTF-8'}";
    velovalidation.setErrorLanguage({
        empty_field: "{l s='Field cannot be empty.' mod='kbmobileapp'}",
        number_field: "{l s='You can enter only numbers.' mod='kbmobileapp'}",
        positive_number: "{l s='Number should be greater than 0.' mod='kbmobileapp'}",
        maxchar_field: "{l s='Field cannot be greater than {#} characters.' mod='kbmobileapp'}",
        minchar_field: "{l s='Field cannot be less than {#} character(s).' mod='kbmobileapp'}",
        empty_email: "{l s='Please enter Email.' mod='kbmobileapp'}",
        validate_email: "{l s='Please enter a valid Email.' mod='kbmobileapp'}",
        invalid_date: "{l s='Invalid date format.' mod='kbmobileapp'}",
        validate_range: "{l s='Number is not in the valid range. It should be betwen {##} and {###}' mod='kbmobileapp'}",
        valid_amount: "{l s='Field should be numeric.' mod='kbmobileapp'}",
        valid_decimal: "{l s='Field can have only upto two decimal values.' mod='kbmobileapp'}",
        max_email: "{l s='Email cannot be greater than {#} characters.' mod='kbmobileapp'}",
        specialchar_zip: "{l s='Zip should not have special characters.' mod='kbmobileapp'}",
        valid_percentage: "{l s='Percentage should be in number.' mod='kbmobileapp'}",
        between_percentage: "{l s='Percentage should be between 0 and 100.' mod='kbmobileapp'}",
        maxchar_size: "{l s='Size cannot be greater than {#} characters.' mod='kbmobileapp'}",
        maxchar_color: "{l s='Color could not be greater than {#} characters.' mod='kbmobileapp'}",
        invalid_color: "{l s='Color is not valid.' mod='kbmobileapp'}",
        specialchar: "{l s='Special characters are not allowed.' mod='kbmobileapp'}",
        script: "{l s='Script tags are not allowed.' mod='kbmobileapp'}",
        style: "{l s='Style tags are not allowed.' mod='kbmobileapp'}",
        iframe: "{l s='Iframe tags are not allowed.' mod='kbmobileapp'}",
        not_image: "{l s='Uploaded file is not an image.' mod='kbmobileapp'}",
        image_size: "{l s='Uploaded file size must be less than {#}.' mod='kbmobileapp'}",
        html_tags: "{l s='Field should not contain HTML tags.' mod='kbmobileapp'}",
    });
    {$custom_js}{*variable contains JS content, Can not escape this*}
</script>
<style>
    {$custom_css}{*variable contains CSS content, Can not escape this*}
    .inactiveLink {
        pointer-events: none;
        cursor: default;
    }
    .velsof_button {
        background-color: {$spin_button_color};{*variable contains HTML content, Can not escape this*}
    }
    .cancel_button{
        color: {$cancel_button_color};{*variable contains HTML content, Can not escape this*}
        text-align: right;  cursor: pointer;
    }
    #velsof_wheel_main_container{
        background-color: {$background_color_wheel};{*variable contains HTML content, Can not escape this*}
    }
    .wheelslices{
        color: {$text_color_wheel};{*variable contains HTML content, Can not escape this*}
         font-family: {$font_family}{*variable contains HTML content, Can not escape this*}
    }
    #velsof_wheel_container{
        font-family: {$font_family}; {*variable contains HTML content, Can not escape this*}
    }
    
</style>
<div id="velsof_wheel_container"  style="display: block; height: 100%; position: fixed; left: 0px; bottom: 0px;  top: 0px; z-index: 100000">
    {*audio controls id="kbmyaudio">
    <source src="{$path|escape:'htmlall':'UTF-8'}views/audio/wheelsound.mp3" type="audio/mpeg">
    </audio*}
    <div id="velsof_wheel_model"> </div>
    <div id="velsof_wheel_main_container" class={$theme}>{*variable contains theme, Can not escape this*}
        {if $theme == 1}
            <div id="velsoftop" class="velsoftheme xmas1"></div>
            <div id="velsofbottom" class="velsoftheme xmas1"> </div>
        {elseif $theme == 2}
            <div id="velsoftop" class="velsoftheme xmas2"></div>
            <div id="velsofbottom" class="velsoftheme xmas2"> </div>
        {* Start: Changes done by Aayushi on 20th-Sep-2018 for integrating new theme *}
        {else}
            
            <div id="velsoftop" class="velsoftheme {$theme}"></div>{*variable contains theme, Can not escape this*}
            <div id="velsofbottom" class="velsoftheme {$theme}"> </div>{*variable contains theme, Can not escape this*}
        {/if}
        {* End: Changes done by Aayushi on 20th-Sep-2018 for integrating new theme *}
        <div id="velsof_offer_container">
            {if isset($front_image_path) && $front_image_path neq "" }
                <div id="spin_wheel_logo_container"><img src='{$front_image_path}' alt='Logo' id='spin_wheel_logo'/></div>{*variable contains HTML content, Can not escape this*}
                {/if}
            <div id="velsof_offer_main_container">
                <div style="color:{$text_color_popup};"> {*variable contains color, Can not escape this*}
                    <div id='main_title' class="velsof_main_title">{$title_text|escape:'htmlall':'UTF-8'}</div>
                    <div id = 'suc_msg' style = 'display: none;' class="velsof_main_title"></div>
                    <div>
                        <div id='velsof_success_description' class="velsof_subtitle" style="padding-bottom:10px ;display: none;"></div>
                        <div id='velsof_description' class="velsof_subtitle" style="padding-bottom:10px;">{$description|escape:'htmlall':'UTF-8'}</div>

                        <ul class="velsof_ul">
                            {foreach $rules_name key=numStep item=rule}
                                <li> {$rule|escape:'htmlall':'UTF-8'}</li>
                                {/foreach}
                        </ul>
                    </div>
                </div>
                <div>
                    {if $cust_name != '4'} 
                        {if $cust_name == '3'}
                            <span><input type="text" id="kbsw_first_name" style="width:44.5%" class="velsof_input_field" name="sw_first_name" placeholder="{l s='Firstname' mod='kbmobileapp'}" value=""></span>
                            <span><input type="text" id="kbsw_last_name" style="width:44.5%" class="velsof_input_field" name="sw_last_name" placeholder="{l s='Lastname' mod='kbmobileapp'}" value=""></span>
                            {/if}
                            {if $cust_name == '2'}
                            <span><input class="velsof_input_field" id="kbsw_last_name" type="text" name="sw_last_name"  placeholder="{l s='Lastname' mod='kbmobileapp'}" value=""></span>
                            {/if}
                            {if $cust_name == '1'}
                            <span><input class="velsof_input_field" id="kbsw_first_name" type="text" name="sw_first_name" placeholder="{l s='Firstname' mod='kbmobileapp'}" value=""></span>
                            {/if}
                        {/if}
                    <input id='velsof_spin_wheel' style="margin-top: 3px;" type="text" name="spin_wheel_email" class="velsof_input_field" placeholder="{l s='Enter you email' mod='kbmobileapp'}" value=''>
                    <div class="saving velsof_button" style='display:none;'><span> </span><span> </span><span> </span><span> </span><span> </span></div>
                    <input id='rotate_btn' type="button" class="velsof_button" name="Rotate" value="{l s='Try your luck' mod='kbmobileapp'}" onclick="onRotateWheel()" />
                    {if isset($gdpr) && ($gdpr['status'] eq 1)}
                        <div class="velsof_spin_wheel_checkbox">
                            <label class="velsof-checkbox-label" for="velsof_privacy_checkbox" >
                                <input type="checkbox" class="velsof-checkbox vel_gdpr_required" id="velsof_privacy_checkbox" name='velsof_privacy_checkbox'>
                            </label>
                            <a href="{$gdpr['privacy_link']}" target="_blank" class="inactiveLink">{*variable contains URL content, Can not escape this*}
                                {$gdpr['privacy_text']|escape:'htmlall':'UTF-8'}
                            </a>
                        </div>
                        {if $gdpr['advance'] eq 1}
                            {foreach from=$gdpr['services'] item= service}
                                <div class="velsof_spin_wheel_checkbox">
                                    <label class="velsof-checkbox-label" for="velsof_privacy_checkbox{$service['service_id']|escape:'htmlall':'UTF-8'}" >
                                        <input type="checkbox" class="velsof-checkbox {if $service['is_manadatory'] eq 1}vel_gdpr_required{/if}" id="velsof_service_checkbox_{$service['service_id']|escape:'htmlall':'UTF-8'}" name='velsof_service_checkbox[{$service['service_id']|escape:'htmlall':'UTF-8'}]'>
                                    </label>
                                    {$service['description']|escape:'htmlall':'UTF-8'}
                                </div>
                            {/foreach}
                        {/if}
                    {/if}
                </div>
            </div>
            <div class='before_loader' id="velsof_offer_main_container" style='display:none;'><img id='spin_after_loader' src="{$path|escape:'htmlall':'UTF-8'}/views/img/front/loader.gif" alt='loader'/> </div>  
            <div class='coupon_result'></div>

        </div>

        <div id="velsof_spinner_container">
            <div id="velsof_spinners">
                <div class="velsof_shadow"></div>
                <div id="velsof_spinner" class="velsof_spinner{$wheel_design|escape:'htmlall':'UTF-8'}">
                    {$deg = 0}

                    {foreach $label_name key=numStep item=tab}                     
                        <div class="wheelslices" style="transform: rotate(-{$deg|escape:'htmlall':'UTF-8'}deg) translate(0px, -50%);">{*variable contains HTML content, Can not escape this*}
                            {$tab|escape:'quotes':'UTF-8'}
                        </div>
                        {$deg = $deg + 30}
                    {/foreach}

                </div>
            </div>
            <img id='velsof_wheel_pointer' class="velsof_wheel_pointer{$wheel_design|escape:'htmlall':'UTF-8'}" src="{$path|escape:'htmlall':'UTF-8'}/views/img/front/pointer{$wheel_design}.png" alt="Ponter"/>
        </div>
    </div>
</div>
<style>
    label.velsof-checkbox-label .checker span {
        background: transparent;
        width: 16px;
        height: 16px;
        border: 2px solid #fff;
        border-radius: 3px;
        vertical-align: middle;
    }
    .velsof_spin_wheel_checkbox input {
        position: relative;
        top: 1px;
        width: 16px;
        height: 16px;
        border: 2px solid #fff;
        border-radius: 3px;
        vertical-align: middle;
        transition: background 0.1s ease;
        cursor: pointer;
        display: inline-block !important;
        margin-right: 5px;
        -webkit-appearance: none;
        z-index: 999;
        opacity: 0 !important;
        background: #004d70;
    }
    .velsof_spin_wheel_checkbox input:checked {
        border-color: transparent;
        background: #fff;
        animation: jelly 0.6s ease;
        z-index: 999999;
        opacity: 1 !important;
        top: -4px;
        border-radius: 0;
        left: 0px;
        width: 14px;
        height: 13px;
        outline: 0 !important;
    }
    .velsof-checkbox-label input:after {
        content: '';
        position: absolute;
        top: 1px;
        left: 3px;
        width: 5px;
        height: 11px;
        opacity: 0;
        transform: rotate(45deg) scale(0);
        border-right: 2px solid #6871f1;
        border-bottom: 2px solid #6871f1;
        transition: all 0.3s ease;
        transition-delay: 0.15s;
    }
    .velsof_spin_wheel_checkbox input:checked:after {
        opacity: 1;
        transform: rotate(45deg) scale(1);
    }

    .velsof_spin_wheel_checkbox a {
        color: #dedede;
    }
    .velsof_spin_wheel_checkbox {
        margin-top: 10px;
        color: #dedede;
    }
</style>
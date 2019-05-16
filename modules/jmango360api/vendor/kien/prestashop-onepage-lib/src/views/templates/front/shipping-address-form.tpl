{**
* @license Created by JMango
*}
{strip}
<fieldset class="">
    <input type="hidden" name="shipping[address_id]" value="" id="shipping:address_id">
    {if $is_logged} {* Customer *}
        {assign var="stateExist" value=false}
        {assign var="postCodeExist" value=false}
        {assign var="dniExist" value=false}
        {assign var="homePhoneExist" value=false}
        {assign var="mobilePhoneExist" value=false}
        {assign var="atLeastOneExists" value=false}
        <ul id="address_form">
            {foreach from=$ordered_adr_fields item=field_name}
                {if $field_name eq 'company'}
                    <li class="fields" id="company">
                        <div class="field input-company">
                            <label for="shipping:company"
                                    {if isset($required_fields) && in_array('company', $required_fields)}
                                        class="required"
                                    {/if} >
                                {l s='Company' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" id="shipping:company" name="shipping[company]" value=""
                                       title="Company" class="input-text" onchange="shipping.onCompanyChange(event)"
                                        {if isset($required_fields) && in_array('company', $required_fields)}
                                    data-validation="required"
                                        {/if}>
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'vat_number'}
                    <li class="fields" id="shipping_vat_number">
                        <div class="field input-vat_number">
                            <label id="vat_number_label" for="shipping:vat_number"
                                    {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                        class="required"
                                    {/if} >
                                {l s='VAT number' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" id="shipping:vat_number" name="shipping[vat_number]"
                                       value="" title="VAT number"
                                       class="input-text required"
                                        {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                    data-validation="required"
                                        {/if}>
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'dni'}
                    {assign var="dniExist" value=true}
                    <li id="shipping_dni" class="wide">
                        <div class="field">
                            <label for="shipping:dni"
                                    {if isset($required_fields) && in_array('dni', $required_fields)}
                                        class="required"
                                    {/if} >
                                {l s='Identification number' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text"
                                       title="{l s='Identification number' mod='jmango360api'}"
                                       name="shipping[dni]" id="shipping:dni" value=""
                                       class="input-text required-entry"
                                        {if isset($required_fields) && in_array('dni', $required_fields)}
                                            data-validation="required"
                                        {/if} />
                                <span class="form_info">
                                    {l s='DNI / NIF / NIE' mod='jmango360api'}
                                </span>
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'firstname'}
                    <li class="wide" id="firstname">
                        <div class="field name-firstname">
                            <label for="shipping:firstname" class="required">{l s='First name' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" id="shipping:firstname" name="shipping[firstname]"
                                       value="{if $customer}
                                        {$customer->firstname|escape:'htmlall':'UTF-8'}

                                        {/if}"
                                       title="First name" maxlength="255" class="input-text required"
                                       data-validation="required">
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'lastname'}
                    <li class="wide" id="lastname">
                        <div class="field name-lastname">
                            <label for="shipping:lastname" class="required">{l s='Last name' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" id="shipping:lastname" name="shipping[lastname]"
                                       value="{if $customer}
                                            {$customer->lastname|escape:'htmlall':'UTF-8'}

                                       {/if}"
                                       title="Last name" maxlength="255"
                                       class="input-text required"
                                       data-validation="required"/>
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'address1'}
                    <li class="wide" id="address1">
                        <label for="shipping:street1" class="required">{l s='Address' mod='jmango360api'}</label>
                        <div class="input-box">
                            <input type="text" title="Street Address" name="shipping[street]"
                                   id="shipping:street1" value="" class="input-text
                                   required-entry" data-validation="required">
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'address2'}
                    <li class="wide" id="address2">
                        <div class="required form-group">
                            <label for="shipping:street2"
                                    {if isset($required_fields) && in_array('address2', $required_fields)}
                                class="required"
                                    {/if}>
                                {l s='Address (Line 2)' mod='jmango360api'}
                            </label>
                            <input class="input-text  required-entry" title="Street Address2"
                                    {if isset($required_fields) && in_array('address2', $required_fields)}
                                        data-validation="required"
                                    {/if}
                                   type="text " id="shipping:street2" name="shipping[street2]" value="" />
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'postcode'}
                    {assign var="postCodeExist" value=true}
                    <li id="Country_zip_code_format" class="fields">
                        <div class="field" id="shipping-postcode">
                            <label for="shipping:postcode" class="required">
                                {l s='Zip/Postal Code' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" title="Zip/Postal Code"
                                       name="shipping[postcode]" id="shipping:postcode" value=""
                                       class="input-text validate-zip-international required-entry" data-validation="required">
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'city'}
                    <li class="fields" id="city">
                        <div class="field">
                            <label for="shipping:city" class="required">{l s='City' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" title="City" name="shipping[city]" value="" class="input-text
                                required-entry" id="shipping:city" data-validation="required">
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'Country:name' || $field_name eq 'country' || $field_name eq 'Country:iso_code'}
                    <li id="Country_name" class="fields">
                        <div class="field">
                            <label for="shipping:country_id" class="required">{l s='Country' mod='jmango360api'}</label>
                            <div id="shipping_country_id" class="input-box input-country">
                                <select name="shipping[country_id]" id="shipping:country_id" class="validate-select"
                                        title="Country" onchange="shipping.onCountryChange(event)">
                                    {foreach from=$countries item=v}
                                        <option value="{$v.id_country|intval}"
                                                {if $default_country == $v.id_country}
                                                    selected="selected"
                                                {/if}>
                                                {$v.name|escape:'html':'UTF-8'}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'State:name'}
                    {assign var="stateExist" value=true}
                    <li class="fields" id="State_name">
                        <div id="shipping:state" class="field">
                            <label for="shipping:state_id" class="required">{l s='State' mod='jmango360api'}</label>
                            <div class="input-box">
                                <select id="shipping:state_id" name="shipping[state_id]" title="State/Province" class="validate-select">
                                    <option value="">{l s='Please select region, state, province' mod='jmango360api'}</option>
                                </select>
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'phone'}
                    {assign var="homePhoneExist" value=true}
                    <li class="fields" id="shipping_phone">
                        <div class="field"
                            {if isset($required_fields) && in_array('phone', $required_fields)}
                                class="required"
                            {/if} >
                            <label id="phone_label" for="telephone"
                                   {if isset($required_fields) && in_array('phone', $required_fields)}
                                        class="required"
                                    {/if}>
                                        {l s='Home phone' mod='jmango360api'}
                                        {if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}
                            </label>
                            <div class="input-box"
                                {if isset($required_fields) && in_array('phone', $required_fields)}
                                    data-validate=required"
                                {/if} >
                                <input class="input-text required-entry" name="shipping[telephone]"
                                       type="tel" id="shipping_phone_input" value=""
                                       {if isset($required_fields) && in_array('phone', $required_fields)}
                                           data-validation= "required"
                                       {/if} />
                            </div>
                        </div>
                    </li>
                {/if}
                {if $field_name eq 'phone_mobile'}
                    {assign var="mobilePhoneExist" value=true}
                    <li class="fields" id="phone_mobile">
                        <div class="field"
                            {if isset($required_fields) && in_array('phone_mobile', $required_fields)}
                                class="required"
                            {elseif !in_array('phone', $required_fields) && !in_array('phone_mobile', $required_fields)}
                            {/if} >

                            <label id="phone_mobile_label" for="shipping:phone_mobile"
                                   {if isset($required_fields) && in_array('phone_mobile', $required_fields)}
                                       class="required"
                                   {elseif !in_array('phone', $required_fields) && !in_array('phone_mobile', $required_fields)}
                                   {/if} >
                                        {l s='Mobile phone' mod='jmango360api'}
                                        {if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}
                            </label>
                            <div class="input-box"
                                {if isset($required_fields) && in_array('phone_mobile', $required_fields)}data-validation="required"
                                {/if}>
                                <input id="shipping_phone_mobile_input" type="tel" name="shipping[phone_mobile]"
                                       value="" title="Telephone" class="input-text  required-entry"
                                       {if isset($required_fields) && in_array('phone_mobile', $required_fields)}
                                           data-validation="required"
                                       {/if} >
                                <label style="color: red;">
                                    <span id="hide_mess_phone"></span>** {l s='You must register at least one phone number.' mod='jmango360api'}
                                </label>
                            </div>
                        </div>
                    </li>
                {/if}
                {assign var="atLeastOneExists" value=true}
                {if ($field_name eq 'phone_mobile') || ($field_name eq 'phone_mobile') && !isset($atLeastOneExists)
                        && isset($one_phone_at_least) && $one_phone_at_least}
                    {assign var="atLeastOneExists" value=true}
                    <p class="inline-infos required">**
                        {l s='You must register at least one phone number.' mod='jmango360api'}
                    </p>
                {/if}
            {/foreach}
            {if !$postCodeExist}
                <li id="Country_zip_code_format" class="fields">
                    <div class="field" id="shipping-postcode">
                        <label for="shipping:postcode" class="required">
                            {l s='Zip/Postal Code' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="text" title="Zip/Postal Code" name="shipping[postcode]"
                                   id="shipping:postcode" value=""
                                   class="input-text validate-zip-international required-entry"
                                   data-validation="required">
                        </div>
                    </div>
                </li>
            {/if}
            {if !$stateExist}
                <li class="fields" id="State_name">
                    <div id="shipping:state" class="field">
                        <label for="shipping:state_id" class="required">{l s='State' mod='jmango360api'}</label>
                        <div class="input-box">
                            <select id="shipping:state_id" name="shipping[state_id]" title="State/Province"
                                    class="validate-select">
                                <option value="">
                                    {l s='Please select region, state, province' mod='jmango360api'}
                                </option>
                            </select>
                        </div>
                    </div>
                </li>
            {/if}
            {if !$dniExist}
                <li id="shipping_dni" class="wide">
                    <div class="field">
                        <label for="shipping:dni" class="required">
                            {l s='Identification number' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="text" title="{l s='Identification number' mod='jmango360api'}"
                                   name="shipping[dni]" id="shipping:dni" value=""
                                   class="input-text required-entry"
                                   data-validation="required">
                            <span class="form_info">
                                {l s='DNI / NIF / NIE' mod='jmango360api'}
                            </span>
                        </div>
                    </div>
                </li>
            {/if}
            <div class="form-group">
                <label for="other">
                    {l s='Additional information' mod='jmango360api'}
                </label>
                <textarea class="" data-validate="" id="shipping_other" name="shipping[other]" cols="26" rows="3" ></textarea>
            </div>
            {if !$homePhoneExist}
                <li class="fields" id="shipping_phone">
                    <div class="field">
                        <label for="telephone">
                            {l s='Home phone' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input class="input-text  required-entry" name="shipping[telephone]" data-validate=""
                                   type="tel" id="shipping_phone" value=""  />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
            {/if}
            {if !$mobilePhoneExist}
                <li class="fields" id="phone_mobile">
                    <div class="field">
                        <label id="phone_mobile_label" for="shipping:phone_mobile" >
                            {l s='Mobile phone' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="tel" name="shipping[phone_mobile]" value=""
                                   title="Telephone" class="input-text  required-entry" id="shipping:phone_mobile" >
                            <label style="color: red;"> <span id="hide_mess_phone"></span>
                                {l s='You must register at least one phone number.' mod='jmango360api'}
                            </label>
                        </div>
                    </div>
                </li>
            {/if}
            <li id="shipping_alias" class="fields"
                style="{if $is_logged eq 0}
                    display: none
                {/if}">
                <div class="field">
                    <label for="shipping:alias" class="required">
                        {l s='Please assign an address title for future reference.' mod='jmango360api'}
                    </label>
                    <div class="input-box">
                        <input type="text" name="shipping[alias]"
                               value="{l s='My address' mod='jmango360api'}"
                               title="Alias" class="input-text  required-entry" id="shipping:alias"
                               data-validation="required">
                    </div>
                </div>
            </li>
            <li class="fields" id="register-customer-password" style="display: none;">
                <div class="field">
                    <label for="shipping:customer_password" class="required"><em>*</em>
                        {l s='Password' mod='jmango360api'}
                    </label>
                    <div class="input-box">
                        <input type="password" name="shipping[customer_password]"
                               id="shipping:customer_password" title="Password"
                               class="input-text required-entry validate-password">
                    </div>
                </div>
                <div class="field">
                    <label for="shipping:confirm_password" class="required">
                        <em>*</em>
                        {l s='Confirm Password' mod='jmango360api'}
                    </label>
                    <div class="input-box">
                        <input type="password" name="shipping[confirm_password]" title="Confirm Password"
                               id="shipping:confirm_password" class="input-text required-entry validate-cpassword">
                    </div>
                </div>
            </li>
            <li class="no-display">
                <input type="hidden" id='shipping:save_in_address_book' name="shipping[save_in_address_book]" value="1">
            </li>

        </ul>
    {else} {* Guest *}
        <ul id="address_form">
            {assign var='stateExist' value=false}
            {assign var="postCodeExist" value=false}
            {assign var="homePhoneExist" value=false}
            {assign var="mobilePhoneExist" value=false}
            {assign var="dniExist" value=false}
            {assign var="atLeastOneExists" value=false}
            {foreach from=$ordered_adr_fields item=field_name}
                {if $field_name eq 'firstname'}
                    <li class="wide" id="firstname">
                        <div class="field name-firstname">
                            <label for="shipping:firstname" class="required">{l s='First name' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" id="shipping:firstname" name="shipping[firstname]"
                                       value="{if $customer}
                                            {$customer->firstname|escape:'htmlall':'UTF-8'}

                                            {/if}"
                                       title="First name" maxlength="255" class="input-text required"
                                       data-validation="required">
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq 'lastname'}
                    <li class="wide" id="lastname">
                        <div class="field name-lastname">
                            <label for="shipping:lastname" class="required">{l s='Last name' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" id="shipping:lastname" name="shipping[lastname]"
                                       value="{if $customer}
                                                {$customer->lastname|escape:'htmlall':'UTF-8'}

                                           {/if}"
                                       title="Last name" maxlength="255"
                                       class="input-text required"
                                       data-validation="required"/>
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq "company"}
                    <li class="fields control" id="shipping_company">
                        <div class="field input-company">
                            <label for="shipping:company"
                                    {if isset($required_fields) && in_array('company', $required_fields)}
                                        class="required"
                                    {/if} >
                                {l s='Company' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" id="shipping:company"
                                       name="shipping[company]" value="" title="Company"
                                       class="input-text" onchange="shipping.addressFormUpdater.showVatNumber()"
                                        {if isset($required_fields) && in_array('company', $required_fields)}
                                            data-validation="required"
                                        {/if} />
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq "vat_number"}
                    <li class="fields" id="shipping_vat_number">
                        <div class="field input-vat_number">
                            <label id="vat_number_label" for="shipping:vat_number"
                                    {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                        class="required"
                                    {/if} >
                                {l s='VAT number' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" id="shipping:vat_number"
                                       name="shipping[vat_number]" value="" title="VAT number"
                                       class="input-text required"
                                        {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                            data-validation="required"
                                        {/if} />
                            </div>
                        </div>
                    </li>

                {elseif $field_name eq "dni"}
                    {assign var='dniExist' value=true}
                    <li id="shipping_dni" class="wide">
                        <div class="field">
                            <label for="shipping:dni" class="required">
                                {l s='Identification number' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" title="{l s='Identification number' mod='jmango360api'}"
                                       name="shipping[dni]" id="shipping:dni" value=""
                                       class="input-text required-entry"
                                            data-validation="required"
                                <span class="form_info">
                                    {l s='DNI / NIF / NIE' mod='jmango360api'}
                                 </span>
                            </div>

                        </div>
                    </li>
                {elseif $field_name eq "address1"}
                    <li class="wide" id="shipping_address1">
                        <label for="shipping:street1" class="required">
                            {l s='Address' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="text" title="Street Address"
                                   name="shipping[street]" id="shipping:street1" value=""
                                   class="input-text  required-entry"

                                    {if isset($required_fields) && in_array('address1', $required_fields)}
                                        data-validation="required"
                                    {/if}/>
                        </div>
                    </li>
                {elseif $field_name eq "address2"}
                    <li class="wide " id="shipping_address2">
                        <div class="required form-group">
                            <label for="shipping:street2"
                                    {if isset($required_fields) && in_array('address2', $required_fields)}
                                        class="required"
                                    {/if} >
                                {l s='Address (Line 2)' mod='jmango360api'}
                            </label>
                            <input class="input-text  required-entry"
                                   title="Street Address2"
                                   type="text" id="shipping:street2"
                                   name="shipping[street2]" value=""
                                    {if isset($required_fields) && in_array('address2', $required_fields)}
                                        data-validation="required"
                                    {/if} />
                        </div>
                    </li>
                {elseif $field_name eq "postcode"}
                    {assign var='postCodeExist' value=true}
                    <li id="shipping_Country_zip_code_format" class="fields">
                        <div class="field" id="shipping-postcode">
                            <label for="shipping:postcode" class="required">
                                {l s='Zip/Postal Code' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" title="Zip/Postal Code"
                                       name="shipping[postcode]"
                                       id="shipping:postcode" value=""
                                       class="input-text validate-zip-international required-entry"
                                        {if isset($required_fields) && in_array('postcode', $required_fields)}
                                            data-validation="required"
                                        {/if} />
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq "city"}
                    <li class="fields" id="city">
                        <div class="field">
                            <label for="shipping:city" class="required">
                                {l s='City' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" title="City"
                                       name="shipping[city]" value=""
                                       class="input-text  required-entry"
                                       id="shipping:city"
                                        {if isset($required_fields) && in_array('city', $required_fields)}
                                            data-validation="required"
                                        {/if} />
                            </div>
                        </div>
                    </li>
                    <!-- if customer hasn't update his layout address, country has to be verified but it's deprecated -->
                {elseif $field_name eq "Country:name" || $field_name eq "country"}
                    <li id="Country_name" class="fields">
                        <div class="field">
                            <label for="shipping:country_id" class="required">
                                {l s='Country' mod='jmango360api'}</label>
                            <div id="shipping_country_id" class="input-box input-country">
                                <select name="shipping[country_id]" id="shipping:country_id"
                                        class="validate-select" title="Country"
                                        onchange="shipping.onCountryChange(event)">
                                    {foreach from=$countries item=v}
                                        <option value="{$v.id_country|intval}"
                                                {if $current_country == $v.id_country}
                                            selected="selected"
                                                {/if}>
                                            {$v.name|escape:'html':'UTF-8'}
                                        </option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq "State:name"}
                    {assign var='stateExist' value=true}
                    <li class="fields" id="State_name">
                        <div id="shipping:state" class="field">
                            <label for="shipping:state_id" class="required">
                                {l s='State' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <select id="shipping:state_id" name="shipping[state_id]"
                                        title="State/Province" class="validate-select">
                                    <option value="">
                                        {l s='Please select region, state, province' mod='jmango360api'}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq 'phone'}
                    {assign var="homePhoneExist" value=true}
                    <li class="fields" id="phone">
                        <div id="shipping:phone" class="field">
                            <label for="phone">{l s='Home phone' mod='jmango360api'}
                                {if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}
                            </label>
                            <div class="input-box">
                                <input class="{if isset($one_phone_at_least) && $one_phone_at_least}required{/if} form-control"
                                       type="tel" id="shipping_telephone" name="shipping[telephone]" value=""  />
                            </div>
                        </div>
                    </li>
                {elseif $field_name eq 'phone_mobile'}
                    {assign var="mobilePhoneExist" value=true}
                    <li class="fields" id="phone_mobile">
                        <div id="shipping:phone_mobile" class="field">
                            <label for="phone_mobile">{l s='Mobile phone' mod='jmango360api'}
                                {if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}
                            </label>
                            <div class="input-box">
                                <input class="required form-control"
                                       type="tel" id="shipping_phone_mobile" name="shipping[phone_mobile]" value="" />
                            </div>
                        </div>
                    </li>

                {/if}
                {if ($field_name eq 'phone_mobile') || ($field_name eq 'phone') && !isset($atLeastOneExists) && isset($one_phone_at_least) && $one_phone_at_least }
                    {assign var="atLeastOneExists" value=true}
                    <p class="inline-infos" style="color: red;">** {l s='You must register at least one phone number.' mod='jmango360api'}</p>
                {/if}
            {/foreach}

            {if $postCodeExist eq false}
                <li id="shipping_Country_zip_code_format" class="fields">
                    <div class="field" id="shipping-postcode">
                        <label for="shipping:postcode" class="required">
                            {l s='Zip/Postal Code' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="text" title="Zip/Postal Code"
                                   name="shipping[postcode]" id="shipping:postcode" value=""
                                   class="input-text validate-zip-international required-entry"
                                   data-validation="required">
                        </div>
                    </div>
                </li>
            {/if}
            {if $stateExist eq false}
                <li class="fields" id="State_name">
                    <div id="shipping:state" class="field">
                        <label for="shipping:state_id" class="required">
                            {l s='State' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <select id="shipping:state_id" name="shipping[state_id]" title="State/Province"
                                    class="validate-select">
                                <option value="">
                                    {l s='Please select region, state, province' mod='jmango360api'}
                                </option>
                            </select>
                        </div>
                    </div>
                </li>
            {/if}
            {if $dniExist eq false}
                <li id="shipping_dni" class="fields">
                    <div class="field">
                        <label for="shipping:dni" class="required">
                            {l s='Identification number' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="text" title="{l s='Identification number' mod='jmango360api'}"
                                   name="shipping[dni]" id="shipping:dni" value=""
                                   class="input-text required-entry"
                                    {if isset($required_fields) && in_array('dni', $required_fields)}
                                        data-validation="required"
                                    {/if} />
                            <span class="form_info">
                                    {l s='DNI / NIF / NIE' mod='jmango360api'}
                                </span>
                        </div>
                    </div>
                </li>
            {/if}
            <div class="form-group">
                <label for="other">
                    {l s='Additional information' mod='jmango360api'}
                </label>
                <textarea class="" data-validate="" id="shipping_other" name="shipping[other]" cols="26" rows="3" ></textarea>
            </div>
            {if !$homePhoneExist}
                <div class="form-group phone-number">
                    <label for="phone">{l s='Home phone' mod='jmango360api'}
                    </label>
                    <input class="{if isset($one_phone_at_least) && $one_phone_at_least}required{/if} form-control"
                           type="tel" id="phone" name="shipping[telephone]" value=""  />
                </div>
            {/if}
            <div class="clearfix"></div>
            {if !$mobilePhoneExist}

                <div class="{if isset($one_phone_at_least) && $one_phone_at_least}
                                 required
                            {/if}form-group">
                    <label for="phone_mobile">{l s='Mobile phone' mod='jmango360api'}
                        {if isset($one_phone_at_least) && $one_phone_at_least}
                            <sup>**</sup>
                        {/if}
                    </label>
                    <input class="required form-control"  type="tel" id="phone_mobile" name="shipping[phone_mobile]" value="" />
                </div>
            {/if}
            {if isset($one_phone_at_least) && $one_phone_at_least && !$atLeastOneExists}
                <p class="inline-infos" style="color: red;">{l s='You must register at least one phone number.' mod='jmango360api'}</p>
            {/if}
            <input type="hidden" name="alias" id="alias" value="{l s='My address' mod='jmango360api'}" />
            <input type="hidden" name="is_new_customer" id="is_new_customer" value="0" />
        </ul>
    {/if}
</fieldset>
{/strip}
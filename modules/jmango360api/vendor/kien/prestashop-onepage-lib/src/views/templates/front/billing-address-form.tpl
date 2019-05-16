{**
* @license Created by JMango
*}

{strip}
<fieldset class="">
    <input type="hidden" name="billing[address_id]"  id="billing:address_id"/>
    {if $is_logged} {* register user (aka customer) *}
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
                                <label for="billing:company"
                                       {if isset($required_fields) && in_array('company', $required_fields)}
                                           class="required"
                                       {/if} >
                                    {l s='Company' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" id="billing:company" name="billing[company]" value="" title="Company"
                                           class="input-text" onchange="billing.showVatNumber()"
                                           {if isset($required_fields) && in_array('company', $required_fields)}
                                                data-validation="required"
                                            {/if}>
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'vat_number'}
                        <li class="fields" id="vat_number">
                            <div class="field input-vat_number">
                                <label id="vat_number_label" for="billing:vat_number"
                                       {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                           class="required"
                                       {/if} >
                                      {l s='VAT number' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" id="billing:vat_number" name="billing[vat_number]" value="" title="VAT number" class="input-text required"
                                            {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                                data-validation="required"
                                            {/if}>
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'dni'}
                        {assign var="dniExist" value=true}
                        <li id="dni" class="wide">
                            <div class="field">
                                <label for="billing:dni" class="required" >
                                    {l s='Identification number' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" title="{l s='Identification number' mod='jmango360api'}"
                                           name="billing[dni]" id="billing:dni" value=""
                                           class="input-text required-entry"
                                           data-validation="required"/>
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
                                <label for="billing:firstname" class="required">
                                    {l s='First name' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" id="billing:firstname" name="billing[firstname]"
                                           value="{if $customer}
                                                        {$customer->firstname|escape:'htmlall':'UTF-8'}
                                                   {/if}"
                                           title="First name"
                                           maxlength="255"
                                           class="input-text required"
                                           data-validation="required" />
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'lastname'}
                        <li class="wide" id="lastname">
                            <div class="field name-lastname">
                                <label for="billing:lastname" class="required">
                                    {l s='Last name' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" id="billing:lastname"
                                           name="billing[lastname]"
                                           value="{if $customer}
                                                        {$customer->lastname|escape:'htmlall':'UTF-8'}
                                                  {/if}"
                                           title="Last name"
                                           maxlength="255"
                                           class="input-text required"
                                           data-validation="required" />
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'address1'}
                        <li class="wide" id="address1">
                            <label for="billing:street1" class="required">
                                {l s='Address' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" title="Street Address"
                                       name="billing[street]" id="billing:street1"
                                       value="" class="input-text  required-entry" data-validation="required" />
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'address2'}
                        <li class="wide" id="address2">
                            <div class="required form-group">
                                <label for="billing:street2"
                                       {if isset($required_fields) && in_array('address2', $required_fields)}
                                            class="required"
                                        {/if}>
                                        {l s='Address (Line 2)' mod='jmango360api'}
                                </label>
                                <input class="input-text  required-entry" title="Street Address2"
                                       {if isset($required_fields) && in_array('address2', $required_fields)}
                                           data-validation="required"
                                       {/if}  type="text "
                                       id="billing:street2" name="billing[street2]" value="" />
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'postcode'}
                        {assign var="postCodeExist" value=true}
                        <li id="Country_zip_code_format" class="fields">
                            <div class="field" id="billing-postcode">
                                <label for="billing:postcode" class="required">
                                    {l s='Zip/Postal Code' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" title="Zip/Postal Code"
                                           name="billing[postcode]" id="billing:postcode"
                                           value="" class="input-text validate-zip-international required-entry"
                                           data-validation="required"/>
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'city'}
                        <li class="fields" id="city">
                            <div class="field">
                                <label for="billing:city" class="required">
                                    {l s='City' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" title="City" name="billing[city]" value="" class="input-text
                                    required-entry" id="billing:city" data-validation="required">
                                </div>
                            </div>
                        </li>
                         {*if customer hasn't update his layout address, country has to be verified but it's deprecated*}
                    {/if}
                    {if $field_name eq 'Country:name' || $field_name eq 'country'
                        || $field_name eq 'Country:iso_code'}
                        <li id="Country_name" class="fields">
                            <div class="field">
                                <label for="billing:country_id" class="required">
                                    {l s='Country' mod='jmango360api'}
                                </label>
                                <div id="billing_country_id" class="input-box input-country">
                                    <select name="billing[country_id]" id="billing:country_id"
                                            class="validate-select" title="Country"
                                            onchange="billing.onCountryChange(event)">
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
                            <div id="billing:state" class="field">
                                <label for="billing:state_id" class="required">
                                    {l s='State' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <select id="billing:state_id" name="billing[state_id]" title="State/Province" class="validate-select">
                                        <option value="">
                                            {l s='Please select region, state, province' mod='jmango360api'}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $field_name eq 'phone'}
                        {assign var="homePhoneExist" value=true}
                        <li class="fields" id="billing_phone">
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
                                         data-validate = "required"
                                    {/if}>
                                    <input class="input-text required-entry" name="billing[telephone]"
                                           type="tel" id="phone_input" name="phone" value=""
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
                                {/if}
                                <label id="phone_mobile_label" for="billing:phone_mobile"
                                    {if isset($required_fields) && in_array('phone_mobile', $required_fields)}
                                        class="required"
                                     {elseif !in_array('phone', $required_fields) && !in_array('phone_mobile', $required_fields)}
                                     {/if} >
                                        {l s='Mobile phone' mod='jmango360api'}
                                        {if isset($one_phone_at_least) && $one_phone_at_least} <sup>**</sup>{/if}
                                </label>
                                <div class="input-box"
                                    {if isset($required_fields) && in_array('phone_mobile', $required_fields)}
                                        data-validation="required"
                                     {/if}>
                                     <input id="phone_mobile_input" type="tel" name="billing[phone_mobile]"
                                            value="" title="Telephone" class="input-text  required-entry"
                                            {if isset($required_fields) && in_array('phone_mobile', $required_fields)}
                                                data-validation="required"
                                            {/if} />
                                    <label style="color: red;">
                                        <span id="hide_mess_phone"></span>** {$message_phone|escape:'html':'UTF-8'}
                                    </label>
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if ($field_name eq 'phone_mobile')
                        || ($field_name eq 'phone_mobile')
                        && !isset($atLeastOneExists) && isset($one_phone_at_least)
                        && $one_phone_at_least}
                            {assign var="atLeastOneExists" value=true}
                        <p class="inline-infos required">** {l s='You must register at least one phone number.' mod='jmango360api'}
                        </p>
                    {/if}
                {/foreach}
                {if !$postCodeExist}
                    <li id="Country_zip_code_format" class="fields">
                        <div class="field" id="billing-postcode">
                            <label for="billing:postcode" class="required">{l s='Zip/Postal Code' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" title="Zip/Postal Code" name="billing[postcode]" id="billing:postcode" value="" class="input-text validate-zip-international required-entry" data-validation="required">
                            </div>
                        </div>
                    </li>
                {/if}
                {if !$stateExist}
                    <li class="fields" id="State_name">
                        <div id="billing:state" class="field">
                            <label for="billing:state_id" class="required">{l s='State' mod='jmango360api'}</label>
                            <div class="input-box">
                                <select id="billing:state_id" name="billing[state_id]" title="State/Province" class="validate-select">
                                    <option value="">{l s='Please select region, state, province' mod='jmango360api'}</option>
                                </select>
                            </div>
                        </div>
                    </li>
                {/if}
                {if !$dniExist}
                    <li id="dni" class="wide">
                        <div class="field">
                            <label for="billing:dni" class="required">{l s='Identification number' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" title="{l s='Identification number' mod='jmango360api'}" name="billing[dni]" id="billing:dni" value="" class="input-text required-entry" data-validation="required">
                                <span class="form_info">{l s='DNI / NIF / NIE' mod='jmango360api'}</span>
                            </div>
                        </div>
                    </li>
                {/if}
                <div class="form-group">
                    <label for="other">
                        {l s='Additional information' mod='jmango360api'}
                    </label>
                    <textarea class="" data-validate="" id="other" name="billing[other]"
                        cols="26" rows="3" >

                    </textarea>
                </div>
                {if !$homePhoneExist}
                    <li class="fields" id="billing_phone">
                        <div class="field">
                            <label for="telephone">{l s='Home phone' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input class="input-text  required-entry" name="billing[telephone]" data-validate="" type="tel" id="billing_phone" name="phone" value=""  />
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </li>
                {/if}
                {if !$mobilePhoneExist}
                    <li class="fields" id="phone_mobile">
                        <div class="field">
                            <label id="phone_mobile_label" for="billing:phone_mobile" >{l s='Mobile phone' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="tel" name="billing[phone_mobile]" value="" title="Telephone" class="input-text  required-entry" id="billing:phone_mobile" >
                                <label style="color: red;"> <span id="hide_mess_phone"></span> {$message_phone|escape:'html':'UTF-8'}</label>
                            </div>
                        </div>
                    </li>
                {/if}
                <li id="billing_alias" class="fields"
                    style="{if $is_logged eq 0}display: none {/if}">
                    <div class="field">
                        <label for="billing:alias" class="required">
                            {l s='Please assign an address title for future reference.' mod='jmango360api'}
                            </label>
                        <div class="input-box">
                            <input type="text" name="billing[alias]" value="{l s='My address' mod='jmango360api'}"
                                title="Alias" class="input-text  required-entry" id="billing:alias"
                                data-validation="required" />
                        </div>
                    </div>
                </li>
                <li class="fields" id="register-customer-password"
                    style="display: none;">
                    <div class="field">
                        <label for="billing:customer_password" class="required">
                            <em>*</em>{l s='Password' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="password" name="billing[customer_password]"
                                id="billing:customer_password"
                                title="Password"
                                class="input-text required-entry validate-password">
                        </div>
                    </div>
                    <div class="field">
                        <label for="billing:confirm_password"
                            class="required">
                            <em>*</em>
                            {l s='Confirm Password' mod='jmango360api'}
                        </label>
                        <div class="input-box">
                            <input type="password" name="billing[confirm_password]"
                            title="Confirm Password" id="billing:confirm_password"
                            class="input-text required-entry validate-cpassword">
                        </div>
                    </div>
                </li>
                <li class="no-display">
                    <input type="hidden" id='billing:save_in_address_book'
                        name="billing[save_in_address_book]" value="1" />
                </li>
            </ul>
        {else} {* Guest *}
            <ul id="address_form">
                {if !$is_logged}
                    {assign var='stateExist' value=false}
                    {assign var="postCodeExist" value=false}
                    {assign var="dniExist" value=false}
                    <!-- Account -->
                    <li id="Customer_email" class="wide" style="{if $is_logged}display:none{/if}">
                        <div class="field">
                            <label for="billing:email" class="required">{l s='Email address' mod='jmango360api'}</label>
                            <div class="input-box">
                                <input type="text" name="billing[email]" id="billing:email" value="" title="Email address"
                                    {if $is_logged}
                                        readonly="true"
                                    {/if}
                                    class="input-text validate-email required" data-validation="email" >
                            </div>
                        </div>
                    </li>
                    <li class="wide"
                        style="{if $is_logged eq 1}display:none{/if}">
                        <div class="field field-radio">
                            <label for="billing:gender_id">
                                {l s='Title' mod='jmango360api'}
                            </label>
                            {foreach from=$genders key=k item=gender}
                                <input id="billing:gender_id" title="Title" class="validate-select input-text"
                                    type="radio" name="billing[gender_id]" value="{$gender->id|intval}" />
                                        <label for="">
                                        {$gender->name|escape:'html':'UTF-8'}
                                        </label>
                            {/foreach}
                        </div>
                    </li>
                    <li class="wide" id="firstname">
                        <div class="field name-firstname">
                            <label for="billing:firstname" class="required">
                                {l s='First name' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" id="billing:firstname" name="billing[firstname]"
                                    value="" title="First name" maxlength="255" class="input-text
                                    required" data-validation="required" />
                            </div>
                        </div>
                    </li>
                    <li class="wide" id="lastname">
                        <div class="field name-lastname">
                            <label for="billing:lastname" class="required">
                                {l s='Last name' mod='jmango360api'}
                            </label>
                            <div class="input-box">
                                <input type="text" id="billing:lastname" name="billing[lastname]"
                                    value="" title="Last name" maxlength="255"
                                    class="input-text required" data-validation="required" />
                            </div>
                        </div>
                    </li>
                    <li id="Customer_birthday" class="fields"
                        style="{if $is_logged eq 1}display:none{/if}">
                        <label for="billing:days">
                            {l s='Date of Birth' mod='jmango360api'}
                        </label>
                        <div class="customer-dob">
                            <div class="field dob-day">
                                <select id="billing:days" name="billing[days]" class="validate-select">
                                    <option value="">-</option>
                                    {foreach from=$days item=day}
                                        <option value="{$day|intval}">{$day|intval}&nbsp;&nbsp;</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="field dob-month">
                                <select id="billing:months" name="billing[months]" class="validate-select">
                                    <option value="">-</option>
                                    {foreach from=$months key=k item=month}
                                        <option value="{$k|intval}">{l s=$month mod='jmango360api'}&nbsp;</option>
                                    {/foreach}
                                </select>
                            </div>
                            <div class="field dob-year">
                                <select id="billing:years" name="billing[years]" class="validate-select">
                                    <option value="">-</option>
                                    {foreach from=$years item=year}
                                        <option value="{$year|intval}">{$year|intval}&nbsp;&nbsp;</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </li>

                    {if isset($newsletter) && $newsletter}
                        <li id="newsletter" class="control">
                            <input type="checkbox" name="newsletter"
                                id="newsletter" value="1"
                                {if isset($guestInformations)
                                    && isset($guestInformations.newsletter)
                                    && $guestInformations.newsletter}
                                        checked="checked"
                                {/if} autocomplete="off"/>
                            <label id="newsletter_label" class="" for="newsletter">
                                {l s='Sign up for our newsletter!' mod='jmango360api'}
                            </label>
                        </li>
                    {/if}

                    {if isset($optin) && $optin}
                        <li id="optin" class="field control">
                            <input type="checkbox" name="optin" id="optin" value="1"
                                {if isset($guestInformations)
                                    && isset($guestInformations.optin)
                                    && $guestInformations.optin}
                                    checked="checked"
                                {/if} autocomplete="off"/>
                            <label id="optin_label" class="" for="optin">
                                {l s='Receive special offers from our partners!' mod='jmango360api'}
                            </label>
                        </li>
                    {/if}
                    <li class="field control">
                        <input type="hidden" value="{$is_logged}" name="billing[is_logged]">
                    </li>
                    {foreach from=$dlv_all_fields item=field_name}
                            {if $field_name eq "company"}
                                <li class="fields control" id="company">
                                    <div class="field input-company">
                                        <label for="billing:company"
                                            {if isset($required_fields) && in_array('company', $required_fields)}
                                                class="required"
                                            {/if} >
                                                {l s='Company' mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <input type="text" id="billing:company" name="billing[company]"
                                                    value="" title="Company" class="input-text"
                                                    onchange="billing.onCompanyChange(event)"
                                                    {if isset($required_fields)
                                                        && in_array('company', $required_fields)}
                                                        data-validation="required"
                                                    {/if}>
                                        </div>
                                    </div>
                                </li>
                            {elseif $field_name eq "vat_number"}
                                <li class="fields" id="vat_number">
                                    <div class="field input-vat_number">
                                        <label id="vat_number_label" for="billing:vat_number"
                                            {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                                class="required"
                                            {/if} >
                                                {l s='VAT number' mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <input type="text" id="billing:vat_number" name="billing[vat_number]"
                                                value="" title="VAT number" class="input-text required"
                                                {if isset($required_fields) && in_array('vat_number', $required_fields)}
                                                    data-validation="required"
                                                {/if} />
                                        </div>
                                    </div>
                                </li>
                            {elseif $field_name eq "dni"}
                                {assign var='dniExist' value=true}
                                <li id="dni" class="wide">
                                    <div class="field">
                                        <label for="billing:dni" class="required">
                                            {l s='Identification number' mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <input type="text"
                                                title="{l s='Identification number' mod='jmango360api'}"
                                                name="billing[dni]" id="billing:dni" value=""
                                                class="input-text required-entry"
                                                {*{if isset($required_fields) && in_array('dni', $required_fields)}*}
                                                    data-validation="required"
                                                {*{/if}>*}
                                                 <span class="form_info">
                                                    {l s='DNI / NIF / NIE' mod='jmango360api'}
                                                 </span>
                                        </div>
                                    </div>
                                </li>
                            {elseif $field_name eq "address1"}
                                <li class="wide" id="address1">
                                    <label for="billing:street1" class="required">
                                        {l s='Address' mod='jmango360api'}</label>
                                    <div class="input-box">
                                        <input type="text" title="Street Address" name="billing[street]"
                                        id="billing:street1" value="" class="input-text  required-entry"

                                        {if isset($required_fields) && in_array('address1', $required_fields)}
                                                  data-validation="required"

                                        {/if} >
                                    </div>
                                </li>
                            {elseif $field_name eq "address2"}
                                <li class="wide " id="address2_hide" style="display: none;">
                                    <div class="required form-group">
                                        <label for="billing:street2"
                                          {if isset($required_fields) && in_array('address2', $required_fields)}
                                                class="required"
                                          {/if} >
                                            {l s='Address (Line 2)' mod='jmango360api'}
                                        </label>
                                        <input class="input-text  required-entry" title="Street Address2"
                                            type="text" id="billing:street2"
                                            name="billing[street2]" value=""
                                            {if isset($required_fields) && in_array('address2', $required_fields)}
                                                  data-validation="required">
                                            {/if} />
                                    </div>
                                </li>
                            {elseif $field_name eq "postcode"}
                                {assign var='postCodeExist' value=true}
                                <li id="Country_zip_code_format" class="fields">
                                    <div class="field" id="billing-postcode">
                                        <label for="billing:postcode" class="required">
                                            {l s='Zip/Postal Code' mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <input type="text" title="Zip/Postal
                                                Code" name="billing[postcode]" id="billing:postcode"
                                                value=""
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
                                        <label for="billing:city"
                                            class="required">
                                            {l s='City' mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <input type="text" title="City" name="billing[city]"
                                                value="" class="input-text  required-entry"
                                                id="billing:city"
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
                                        <label for="billing:country_id" class="required">
                                            {l s='Country' mod='jmango360api'}
                                        </label>
                                        <div id="billing_country_id" class="input-box input-country">
                                            <select name="billing[country_id]" id="billing:country_id"
                                                class="validate-select" title="Country"
                                                onchange="billing.onCountryChange(event)">
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
                                    <div id="billing:state" class="field">
                                        <label for="billing:state_id" class="required">
                                            {l s='State' mod='jmango360api'}
                                        </label>
                                        <div class="input-box">
                                            <select id="billing:state_id" name="billing[state_id]"
                                            title="State/Province" class="validate-select">
                                                <option value="">
                                                    {l s='Please select region, state, province' mod='jmango360api'}
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                </li>
			                {/if}

                    {/foreach}
                    {if $stateExist eq false}
                        <li class="fields" id="State_name">
                            <div id="billing:state" class="field">
                                <label for="billing:state_id" class="required">
                                    {l s='State' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <select id="billing:state_id" name="billing[state_id]"
                                    title="State" class="validate-select">
                                        <option value="">
                                            {l s='Please select region, state, province' mod='jmango360api'}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $postCodeExist eq false}
                        <li id="Country_zip_code_format" class="fields">
                            <div class="field" id="billing-postcode">
                                <label for="billing:postcode" class="required">
                                    {l s='Zip/Postal Code' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" title="Zip/Postal Code"
                                        name="billing[postcode]" id="billing:postcode"
                                        value="" class="input-text validate-zip-international required-entry"
                                        data-validation="required">
                                </div>
                            </div>
                        </li>
                    {/if}
                    {if $dniExist eq false}
                        <li id="dni" class="wide">
                            <div class="field">
                                <label for="billing:dni" class="required">
                                    {l s='Identification number' mod='jmango360api'}
                                </label>
                                <div class="input-box">
                                    <input type="text" title="{l s='Identification number' mod='jmango360api'}"
                                        name="billing[dni]" id="billing:dni" value="" class="input-text required-entry"
                                        data-validation="required"/>
                                    <span class="form_info">{l s='DNI / NIF / NIE' mod='jmango360api'}</span>
                                </div>

                            </div>
                        </li>
                    {/if}
                    <div class="clearfix"></div>
                        <li class="fields">
                            <div class="field">
                                 <label for="phone_mobile"
                                 class="
                                    {if isset($one_phone_at_least) && $one_phone_at_least}
                                            required
                                    {/if}">
                                    {l s='Mobile phone' mod='jmango360api'}

                                </label>
                                <input class="validate form-control" type="tel" id="phone_mobile" name="billing[phone_mobile]" value=""
                                    {if isset($one_phone_at_least) && $one_phone_at_least}
                                        data-validation="required"
                                    {/if}
                                />
                            </div>
                        </li>
                    <input type="hidden" name="alias" id="alias" value="{l s='My address' mod='jmango360api'}" />
                    <input type="hidden" name="is_new_customer" id="is_new_customer" value="0" />
                {/if}
            </ul>
        {/if}
</fieldset>
{/strip}
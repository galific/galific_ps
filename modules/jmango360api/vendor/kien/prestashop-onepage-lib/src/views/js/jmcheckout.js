/**
 * @license
 */

$.fn.clear = function()
{
    $(this).find('input')
        .filter(':text, :password, :file').val('')
        .end()
        .filter(':checkbox, :radio')
        .removeAttr('checked')
        .end()
        .end()
        .find('textarea').val('')
        .end()
        .find('select').prop("selectedIndex", -1)
        .find('option:selected').removeAttr('selected')
    ;
    return this;
};


var Accordion = function(elm, clickableEntity, checkAllow) {
    var self = this;

    var vars = {
        sections:[]
    };

    self.disallowAccessToNextSections = false;

    self.construct = function(elm, clickableEntity, checkAllow){
        var options = {
            container: '#' + elm,
            checkAllow: checkAllow,
            clickableEntity: clickableEntity,
            currentSectionId: false,
        };

        self.sections = vars.sections;

        $.extend(vars, options);

        $(vars.container).find(vars.clickableEntity).each(function(){
            var sectionId = $(this).parent('li').attr('id');
            vars.sections.push(sectionId);
            $(this).click(function(event) {
                if (sectionId != vars.currentSectionId)  {
                    if (vars.checkAllow) {
                        if (self.checkSectionAllow(sectionId))  {
                            self.closeCurrentSection();
                            self.openSection(sectionId);
                        }
                    }
                    else {
                        self.closeCurrentSection();
                        self.openSection(sectionId);
                    }
                }
            });
        });
    };

    self.checkSectionAllow = function (sectionId) {
        var section = $('#' + sectionId);
        if (section.hasClass('allow')){
            return true;
        }
        return false;
    }

    self.openSection =  function(sectionId) {
        var section = $('#' + sectionId);
        if (section != null && section != 'undefined') {
            section.find('.a-item').collapse('show');
            vars.currentSectionId = sectionId;
        }

        if (self.disallowAccessToNextSections) {
            var pastCurrentSection = false;
            for (var i=0; i<self.sections.length; i++) {
                if (pastCurrentSection) {
                    $('#'+ self.sections[i]).removeClass('allow');
                }
                if (this.sections[i]==sectionId) {
                    pastCurrentSection = true;
                }
            }
        }
    };

    self.closeSection = function(sectionId) {
        var section = $('#' + sectionId);
        if (section != null && section != 'undefined') {
            section.find('.a-item').collapse('hide');
        }
    };

    self.openNextSection =  function(setAllow){
        var nextIndex = 0;
        for (var i = 0; i < vars.sections.length; i++) {
            var nextIndex = i + 1;
            var sectionId = vars.sections[i];
            if (sectionId == vars.currentSectionId && vars.sections[nextIndex]){
                if (setAllow) {
                    $('#' + vars.sections[nextIndex]).addClass('allow');
                }
                self.closeCurrentSection();
                self.openSection(vars.sections[nextIndex]);

                return;
            }
        }
    };

    self.openPrevSection = function(setAllow){
        for (var i = vars.sections.length - 1; i > 0 ; i--) {
            var sectionId = vars.sections[i];
            var prevIndex = i - 1;
            if (sectionId == vars.currentSectionId && vars.sections[prevIndex]){
                if (setAllow) {
                    $('#' + vars.sections[prevIndex]).addClass('allow');
                }
                self.closeCurrentSection();
                self.openSection(this.sections[prevIndex]);
                return;
            }
        }
    };

    self.closeCurrentSection = function() {
        if(vars.currentSectionId) {
            self.closeSection(vars.currentSectionId);
        }
    }

    self.construct(elm, clickableEntity, checkAllow);
}


var AddressFormUpdater = function(type , addresses, countries, customer) {

    var self = this;

    var contructor = function (type, addresses, countries, customer) {
        self.type = type;
        self.addresses = [];
        if (addresses) {
            self.addresses = addresses;
        }
        // self.addresses = addresses;
        self.customer = customer;
        self.countries = countries;
    }

    contructor(type, addresses, countries, customer);

    self.replaceFormFieldSet = function(fieldset) {
        $("#"+self.type+"-new-address-form").html(fieldset);
    }

    self.reloadForm = function (data) {

        // self.countries = data["countries"];
        var field_show = data["ordered_adress_fields"];
        var field_require = data["requireds_fields"];
        var customer_field_require = data["customer_form_required"];

        // store user-entered data
        formData = $('#co-'+ self.type + '-form').serialize();

        // replace form fields
        if (isLogged) {
            self.replaceFormFieldSet(data['output']);
        }
        // Restore user-entered data
        $('#co-'+self.type + '-form').deserialize(formData);

        //restore validation
        $.validate({lang : langCode});

        self.bindData(data["current_country"]);

        // clear form blank for crete new.
        if (($("#"+self.type+"-address-select :selected").val() == "") && ($("#"+self.type+"\\:country_id :selected").val() == current_country) && billing.addresses.length >=1) {
            self.clearAddress();
        }

        $('#address2_hide').hide();
        $("#"+self.type+"_address2_hide").hide();
        if (field_show && field_require) {
            $('#address_guest_form li').hide();
            $("#Country_zip_code_format").hide();
            $("#dni").hide();
            $("#State_name").hide();

            $('#address_guest_form').show();
            $('#newsletter').show();
            for (i in field_show) {
                var temp = field_show[i].replace(/:/i,'_');
                if (temp == 'vat_number') continue;
                $('#'+temp).show();
            }
            for (i in field_require) {
                var temp = field_require[i].replace(/:/i,'_');
                if (temp == 'vat_number') continue;
                $('#'+temp).show();
            }
        }

        self.updateState(self.countries);
        self.updateZipcode(self.countries);
        self.updateNeedDNI(self.countries);
        self.showVatNumber();



        // set required field for phone number
        if (customer_field_require !== undefined
            && customer_field_require.indexOf("phone") < 0
            && customer_field_require.indexOf("mobile_phone") < 0
            && ones_phone_at_least != "0") {
            $("#phone_mobile_label").addClass('required');
        }

        // self.bindData(data["current_country"]);
        // self.resetLoadWaiting(false);
        // self.reloadCountrySelected();
    }

    self.getSelectedAddress = function() {
        var selectedId = $('select[name="' + self.type + '_address_id"] option:selected').val();
        var selectedAddress;
        var addresses = billing.addresses;

        for (var i = 0; i < addresses.length; i++) {
            if (addresses[i].id_address == selectedId) {
                selectedAddress = addresses[i];
            }
        }
        return selectedAddress;
    }

    // Check if country has no state will not show field State.
    self.updateState = function (countries) {
        self.updateStates(self.countries);
        var selectedAddress = self.getSelectedAddress();
        if (self.countries[$('#'+self.type+'_country_id select').val()] !== undefined
            && self.countries[$('#'+self.type+'_country_id select').val()]["states"] !== undefined
            && self.countries[$('#'+self.type+'_country_id select').val()]["contains_states"] == 1) {
            try {
                if (isLogged) {
                    (selectedAddress !== undefined) ? $("#"+self.type+"\\:state_id").val(selectedAddress.id_state): $("#"+self.type+"\\:state_id").val('');
                }
                if ($("#"+self.type+"\\:state_id").val() === null) {
                    $("#"+self.type+"\\:state_id").val('');
                }
            } catch (e) {}
            $("li[id = State_name]").show();
        } else {
            $("li[id = State_name]").hide();
        }
    }

    self.updateZipcode = function (countries) {
        if (countries[(parseInt($('#'+self.type+'_country_id select').val()))] !== undefined
            && countries[(parseInt($('#'+self.type+'_country_id select').val()))]['id_country'] !== 'undefined'
            && countries[(parseInt($('#'+self.type+'_country_id select').val()))]['need_zip_code'] == 1 ) {
            if (self.type == "billing") {
                $("#Country_zip_code_format").show();
            } else {
                $("#shipping_Country_zip_code_format").show();
            }
        } else {
            if (self.type == "billing") {
                $("#Country_zip_code_format").hide();
            } else {
                $("#shipping_Country_zip_code_format").hide();
            }
        }
    }

    self.updateNeedDNI = function (countries) {
        if (self.countries[parseInt($('#'+self.type+'_country_id select').val())] !== undefined
            && self.countries[parseInt($('#'+self.type+'_country_id select').val())]['need_identification_number'] == '1'
        ) {
            if (self.type == "billing") {
              $("#dni").show();
            } else {
                $("#shipping_dni").show();
            }
        } else {
            if (self.type == "billing") {
                $("#dni").hide();
            } else {
                $("#shipping_dni").hide();
            }
        }
    }

    self.showVatNumber = function () {
        var id_country = parseInt($('#'+self.type+'_country_id select').val()) ;
        var check = "input[name='"+self.type+"[company]']";
        if ($(check).val() !== "" && $(check).val() !== undefined) {
            $('#vat_number').show();
            $('#shipping_vat_number').show();
            //
            if (!$("#vat_number_label").hasClass('required')) {
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: baseDir + 'modules/vatnumber/ajax.php?id_country=' + id_country + '&rand=' + new Date().getTime(),
                    success: function(isApplicable) {
                        if(isApplicable === undefined)
                        {
                            $('#vat_number').hide();
                            $('#shipping_vat_number').hide();
                        }
                    }
                });
            }
        } else {
            $('#vat_number').hide();
            $('#shipping_vat_number').hide();

        }
    }

    self.convertError = function (err) {
        var label = err;
        var decoded = $('<div/>').html(label).text();
        return decoded;
    }

    self.resetLoadWaiting = function(transport){
        checkout.setLoadWaiting(false);
    };

    self.fillMessRequired = function (message) {
        if (message !== undefined) {

        }
    }


    self.bindData = function(current_country) {
        var selectedAddress = self.getSelectedAddress();

        if (selectedAddress !== undefined) {
            $('input[name="'+self.type+'[firstname]"]').val((selectedAddress.firstname)? selectedAddress.firstname: '');
            $('input[name="'+self.type+'[lastname]"]').val((selectedAddress.lastname) ? selectedAddress.lastname : '');
            if(selectedAddress.email !== null) {
                $('input[name="'+self.type+'[email]"]').val(selectedAddress.email);
            }
            $('input[name="'+self.type+'[company]"]').val((selectedAddress.company != " ")? selectedAddress.company : '');
            $('input[name="'+self.type+'[vat_number]"]').val((selectedAddress.vat_number != "") ? selectedAddress.vat_number : '');
            $('input[name="'+self.type+'[street]"]').val((selectedAddress.address1) ? selectedAddress.address1 : '');
            if(selectedAddress.address2){
                $('input[name="'+self.type+'[street2]"]').val((selectedAddress.address2 != " ") ? selectedAddress.address2 : '');
            } else {
                $('input[name="'+self.type+'[street2]"]').val('');

            }
            if (selectedAddress.phone) {
                $('input[name="'+self.type+'[telephone]"]').val((selectedAddress.phone != "") ? selectedAddress.phone : '');
            } else {
                $('input[name="'+self.type+'[telephone]"]').val('');
            }
            $('input[name="'+self.type+'[postcode]"]').val((selectedAddress.postcode) ? selectedAddress.postcode : '');
            $('input[name="'+self.type+'[city]"]').val((selectedAddress.city != " ") ? selectedAddress.city : '');
            if(selectedAddress.phone_mobile){
                $('input[name="'+self.type+'[phone_mobile]"]').val((selectedAddress.phone_mobile != "") ? selectedAddress.phone_mobile : '');
            } else  {
                $('input[name="'+self.type+'[phone_mobile]"]').val('');
            }
            $('input[name="'+self.type+'[alias]"]').val(selectedAddress.alias);
            $('input[name="'+self.type+'[dni]"]').val((selectedAddress.dni) ? selectedAddress.dni : '');
            $('textarea[name="'+self.type+'[other]"]').val((selectedAddress.other) ? selectedAddress.other : '');

            if (self.type == 'shipping' && isLogged) {
                $('#'+self.type+'\\:country_id').val(current_country);
            } else {
                $('#'+self.type+'\\:country_id').val(selectedAddress.id_country);
            }
            $('#'+self.type+'\\:state_id').val(selectedAddress.id_state);
            $('#'+self.type+'\\:state_id').trigger('change');

        } else if (selectedAddress === undefined) {
            // $('input[name="billing[firstname]"]').val(firstname_create_new);
            // $('input[name="billing[lastname]"]').val(lastname_create_new);
            // $('input[name="billing[company]"]').val(company_create_new);
            $('input[name="'+self.type+'[alias]"]').val(my_address);
            var country = (default_country != current_country) ? default_country : current_country;
            $('#'+self.type+'\\:country_id').val(current_country);
        }

        // Fill data for Guest
        if (isLogged == 0) {
            var country = (default_country != current_country) ? default_country : current_country;
            $('#'+self.type+'\\:country_id').val(current_country);
        }
    }

    self.updateStates = function(countries) {
        if (isLogged) {
            $('#' + (self.type) + '\\:state_id' + ' option:not(:first-child)').remove();
        }
        if (typeof countries !== 'undefined' && $('#'+self.type+'_country_id :selected').val() !== undefined)
            var state_list = countries[$('#'+self.type+'_country_id :selected').val()]["states"];
        if (typeof state_list !== 'undefined')
        {
            for (var key in state_list) {
                $('#' + (self.type) + '\\:state_id').addClass('validate-select').append('<option value="' + parseInt(state_list[key].id_state) + '">' + state_list[key].name + '</option>');
            };

            $('#' + (self.type) + '\\:state' + ':hidden').show();
        }
        else
            $('#' + (self.type) + '\\:state').hide();
    }

    self.isValidCustomer = function () {
        return (self.customer && self.customer.id && self.customer.is_guest);
    }

    self.clearAddress = function() {
        if (self.isValidCustomer()) {
            customer_firstname = self.customer.firstname;
            customer_lastname = self.customer.lastname;
            if (self.customer.company) {
                customer_company = self.customer.company;
            }
        }

        //Init these data with customer info
        $('input[name="'+ self.type + '[firstname]"]').val(self.customer.firstname);
        $('input[name="'+ self.type + '[lastname]"]').val(self.customer.lastname);
        $('input[name="'+ self.type + '[company]"]').val(self.customer.company);
        $('input[name="'+ self.type + '[email]"]').val('');
        $('input[name="'+ self.type + '[vat_number]"]').val('');
        $('input[name="'+ self.type + '[street]"]').val('');
        $('input[name="'+ self.type + '[street2]"]').val('');
        $('input[name="'+ self.type + '[telephone]"]').val('');
        $('input[name="'+ self.type + '[postcode]"]').val('');
        $('input[name="'+ self.type + '[city]"]').val('');
        $('input[name="'+ self.type + '[state_id]"]').val('');
        $('input[name="'+ self.type + '[phone_mobile]"]').val('');
        $('textarea[name="'+ self.type + '[other]"]').val('');
        $('input[name="'+ self.type + '[alias]"]').val(my_address);
        $('input[name="'+ self.type + '[dni]"]').val('');


    }


    self.initAddressForm = function() {



        selectedBillingAddress = self.getSelectedAddress();


        if (self.addresses && self.addresses.length > 0) {
            selectedBillingAddress = self.addresses[0];
        }

        if (selectedBillingAddress === undefined) {
            if (!self.customer.is_guest)
                self.clearAddress();
            self.updateState();
            self.updateNeedDNI()
            return;
        };

        //region Fill Addess
        if (selectedBillingAddress.id_customer === '0') return;

        self.newAddress(!selectedBillingAddress, self.customer.is_guest);

        // Fill invoice address
        $('input[name="'+self.type+'[firstname]"]').val((selectedBillingAddress.firstname)? selectedBillingAddress.firstname: '');
        $('input[name="'+self.type+'[lastname]"]').val((selectedBillingAddress.lastname) ? selectedBillingAddress.lastname : '');
        if(customer.email !== null) {
            $('input[name="'+self.type+'[email]"]').val(customer.email);
        }
        $('input[name="'+self.type+'[company]"]').val((selectedBillingAddress.company != " ")? selectedBillingAddress.company : '');
        $('input[name="'+self.type+'[vat_number]"]').val((selectedBillingAddress.vat_number != "") ? selectedBillingAddress.vat_number : '');


        $('input[name="'+self.type+'[street]"]').val((selectedBillingAddress.address1) ? selectedBillingAddress.address1 : '');
        if(selectedBillingAddress.address2){
            $('input[name="'+self.type+'[street2]"]').val((selectedBillingAddress.address2 != " ") ? selectedBillingAddress.address2 : '');
        } else {
            $('input[name="'+self.type+'[street2]"]').val('');

        }
        if (selectedBillingAddress.phone) {
            $('input[name="'+self.type+'[telephone]"]').val((selectedBillingAddress.phone != "") ? selectedBillingAddress.phone : '');
        } else {
            $('input[name="'+self.type+'[telephone]"]').val('');
        }
        $('input[name="'+self.type+'[postcode]"]').val((selectedBillingAddress.postcode) ? selectedBillingAddress.postcode : '');
        $('input[name="'+self.type+'[city]"]').val((selectedBillingAddress.city != " ") ? selectedBillingAddress.city : '');
        if(selectedBillingAddress.phone_mobile){
            $('input[name="'+self.type+'[phone_mobile]"]').val((selectedBillingAddress.phone_mobile != "") ? selectedBillingAddress.phone_mobile : '');
        } else  {
            $('input[name="'+self.type+'[phone_mobile]"]').val('');
        }
        // else {
        //     $('input[name="billing[telephone]"]').val(selectedBillingAddress.phone_mobile);
        // }
        $('input[name="'+self.type+'[alias]"]').val(selectedBillingAddress.alias);
        $('input[name="'+self.type+'[dni]"]').val((selectedBillingAddress.dni) ? selectedBillingAddress.dni : '');
        $('textarea[name="'+self.type+'[other]"]').val((selectedBillingAddress.other) ? selectedBillingAddress.other : '');


        if (self.type == 'billing' && self.customer && self.customer.is_guest) {
            $('input[name="newsletter"]').prop(
                'checked', self.customer.newsletter == '1' ? true : false);
            $('input[name="optin"]').prop(
                'checked', self.customer.optin == '1' ? true : false);

            $('input[name= "billing[gender_id]"][value="'
                + self.customer.id_gender +  '"]').click();

        }

        // Fill customer 's birthday
        if (self.customer &&  self.customer.birthday) {
            birthday = self.customer.birthday.split('-');
            $('select[name="'+self.type+'[days]"]').val(parseInt(birthday[2]));
            $('select[name="'+self.type+'[months]"]').val(parseInt(birthday[1]));
            $('select[name="'+self.type+'[years]"]').val(parseInt(birthday[0]));
        }

        $('#'+self.type+'\\:country_id').val(selectedBillingAddress.id_country);

        // Setup State <Select/> if country has states
        self.updateState();
        self.updateNeedDNI();
        $('#'+self.type+'\\:state_id').val(selectedBillingAddress.id_state);
        self.showVatNumber();
        //endregion
    };

    self.newAddress = function(isNew, isGuest){
        if (isNew) {
            self.resetSelectedAddress();
            $('#'+self.type+'-new-address-form').show();
        } else {
            if(isGuest == 1) {
                $('#'+self.type+'-new-address-form').show();
            } else {
                if($('#'+self.type+'\\:edit').val() == '0') {
                    $('#'+self.type+'-new-address-form').hide();
                } else {
                    $('#'+self.type+'-new-address-form').show();
                }
            }
        }
    };

    self.resetSelectedAddress =  function(){
        var selectElement = $('#'+self.type+'-address-select');
        if (selectElement) {
            selectElement.value='';
        }
    };

    self.updateAddresses = function() {
        addresses;
    };

    self.hideForm = function () {
        // Hide Form after all success.
        if (isLogged) {
            $("#shipping-new-address-form").hide();
            $("#billing-new-address-form").hide();
        }
    }




}

var Billing = function (form, url, customer, addreses, countries, current_country) {
    var self = this;

    self.constructor = function(form, url, customer, addreses, countries, current_country) {
        self.url = url;
        self.form = $('#' + form);
        self.customer = customer;
        self.addresses = [];
        self.addressService = new AddressService(url, addresses);
        self.countries = countries;

        if (addresses) {
            self.addresses = addresses;
        }

        self.current_country = current_country;

        self.formUpdater = new AddressFormUpdater('billing',addresses, countries, customer);

        if (self.form) {
            self.form.submit(self.save);
        }

    };

    self.start = function() {
        self.initAddressForm();

        $('#billing-address-select').change(function(event){
            selectedAddress = self.formUpdater.getSelectedAddress();
            id_country =  (selectedAddress)? selectedAddress.id_country:self.current_country;
            self.addressService.getAddressForm(id_country, self.formUpdater.reloadForm, 'billing');
            // if (default_country != id_country ) {
            //     } else {
            //         self.formUpdater.reloadForm(addresses);
            //     }
            }
        );

        $('#billing-address-edit-btn').click(function(){
            if($('#billing\\:edit').val() == '0') {
                $('#billing\\:edit').val('1');
            } else {
                $('#billing\\:edit').val('0');
            }
            if ($("#billing-new-address-form").css('display') == 'none') {
                $('#billing-address-select').trigger('change');
            } else {
                self.formUpdater.hideForm();
            }
        });

        $.validate({lang : langCode});

    }

    self.isEditMode = function() {
        if (self.customer && self.customer.id) {
            if (self.customer.is_guest) {
                // check if customer create new  and change country is not editMode.
                if ($('#billing-address-select').val() !== undefined && $('#billing-address-select').val() == "") {
                    return false;
                } else {
                    return (self.addresses.length >= 1);
                }
            } else {

                //isLogged < 1
                //if (
                //|| $("#billing-address-edit-btn").data('clicked')
                return $('#billing\\:edit').val() == '1'
                    || $("#billing-address-edit-btn").data('clicked')
                    || $('select[name="billing_address_id"] option:selected').val() != ""

            }
        } else {
            return false;
        }
    }

    //Country select change event handler
    self.onCountryChange = function(event) {
        countryId = $(event.srcElement).val();
        if (self.isEditMode()) {
            var data = self.addressService.getCountries(function(countries) {
                self.formUpdater.updateState(countries);
                self.formUpdater.updateNeedDNI(countries);
                self.formUpdater.updateZipcode(countries);
                // if (typeof validate_field !== 'undefined')
                //     validate_field('#postcode');
            });
        } else {
            // Only get form when not editing an address
            self.addressService.getAddressForm(countryId, self.formUpdater.reloadForm, 'billing');
        }

        return false;

    }

    self.onCompanyChange = function(event) {
        self.formUpdater.showVatNumber();
    }

    //Check valid customer
    self.isLoggedCustomer = function() {
        return (self.customer && self.customer.id);
    }

    self.initAddressForm = function() {
        self.formUpdater.initAddressForm();
    };

    self.reloadAddressBox = function() {
        var options = [];
        if(selectedBillingAddress == 0) {
            options.push('<option value="" selected="selected">'+ newAddressLabel +'</option>');
        } else {
            options.push('<option value="">'+ newAddressLabel +'</option>');
        }
        $.each(self.addresses, function(i , address) {
            if(selectedBillingAddress == address.id_address) {

                options.push('<option value="' + address.id_address + '" selected="selected">' + address.alias + '</option>');
            } else {
                options.push('<option value="' + address.id_address + '">' + address.alias + '</option>');
            }
        });
        if (isLogged) {
            $('#billing-address-select').html(options.join(""));
            $('#billing-address-select').trigger('change');
        }
    }

    self.save = function () {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('billing');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + "&ajax=true" + "&submitGuestAccount=true&step=billing" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function () {

                },
                complete: function () {
                    self.resetLoadWaiting(false);
                },
                success: function (json) {
                    self.nextStep(json);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }

    };

    self.resetSelectedAddress =  function(){
        var selectElement = $('#billing-address-select');
        if (selectElement) {
            selectElement.value='';
        }
    };

    self.newAddress = function(isNew, isGuest){
        self.formUpdater(isNew,isGuest);
    };

    self.setUseForShipping = function(flag) {
        $('#shipping\\:same_as_billing').checked = flag;
    };

    self.resetLoadWaiting = function(transport){
        checkout.setLoadWaiting(false);
    };

    self.nextStep = function (json) {
        if (json.hasError){
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                // PS-766: Onepage - Display large space between sentence on validate Zip/postal code message;
                plainText = plainText.replace(/\s\s+/g, '');
                plainText = plainText.replace(/\./, '. ');
                alert(plainText);
            }
            return false;
        }

        if (json.updated_section) {
            $('#checkout-step-shipping_method').html(json.updated_section);
        }

        // Update selected address,
        selectedBillingAddress = json.id_address_invoice;
        shipping.selectedShippingAddress = json.id_address_delivery;

        // Update new addresses after sasve.
        window.addresses = json.addresses;
        self.addresses = json.addresses;

        self.formUpdater.addresses = json.addresses;

        shipping.addresses = json.addresses;
        shipping.addressFormUpdater.addresses = json.addresses;

        if (self.formUpdater.isValidCustomer()) {
            self.reloadAddressBox();
            shipping.reloadAddressBox();
        }

        checkout.gotoSection(json.goto_section, true);
        checkout.setStepResponse(json);

        if (json.shipping_fields) {
            if (shipping) {
                shipping.setFormFieldSet(json.shipping_fields);
            }

        }

        if(json.duplicateBillingInfo) {
            if(shipping != 'undefined') {
                shipping.syncWithBilling();
            }
        }

        //Hide form
        self.formUpdater.hideForm();
    };

    self.fillForm = function (address) {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('billing');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + "&ajax=true" + "&submitGuestAccount=true" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function () {

                },
                complete: function () {

                },
                success: function (json) {
                    self.nextStep(json);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }
    }

    self.showVatNumber = function () {
        if ($("input[name='billing[company]']").val() !== "" && $("input[name='billing[company]']").val() !== undefined) {
            $('#vat_number').show();
            //
            if (!$("#vat_number_label").hasClass('required')) {
                $.ajax({
                    type: 'POST',
                    headers: {"cache-control": "no-cache"},
                    url: baseDir + 'modules/vatnumber/ajax.php?id_country=' + parseInt($('#billing_country_id select').val()) + '&rand=' + new Date().getTime(),
                    success: function(isApplicable) {
                        if(isApplicable === undefined)
                        {
                            $('#vat_number').hide();
                        }
                    }
                });
            }
        } else {
            $('#vat_number').hide();
        }
    }

    self.getFormData = function(){
        serialized = self.form.serialize();
        return serialized;
    }

    self.constructor(form,url, customer, addresses, countries);

    self.syncWithShipping = function() {

        shippingSerialized = shipping.getFormData();

        /** TODO use more sophisticated regex */
        serialized = shippingSerialized.replace(/shipping_/g,'billing_');
        serialized = serialized.replace(/shipping%5B/g,'billing%5B');

        // this must be called before deserialize
        // otherwise state is not selected
        self.form.deserialize(serialized);
        self.formUpdater.updateState(self.countries);
        self.formUpdater.updateZipcode(self.countries);

        self.formUpdater.updateNeedDNI(self.countries);
        self.formUpdater.showVatNumber();

    }

}

var Checkout = function(accordion) {
    var self = this;

    self.constructor = function(accordion) {
        self.accordion = accordion;
        self.steps = ['billing', 'shipping', 'shipping_method', 'payment', 'review'];
        self.currentStep = 'billing';
        self.loadWaiting = false;
        self.accordion.disallowAccessToNextSections = true;


        self.accordion.sections.forEach(function(section) {

            var section_elm = $('#' + section);
            section_elm.find('.step-title').click(function(event) {
                event.stopImmediatePropagation();
                if (section_elm.hasClass('allow')) {
                    event.stopImmediatePropagation();
                    self.gotoSection(section.replace('opc-',''), false);
                    return false;
                }

            });
        });

        // if(selectedBillingAddress != 0 && selectedShippingAddress != 0) {
        //     self.gotoSection('shipping_method', true);
        // } else {
        self.gotoSection('billing', true);
        // }


        setTimeout(function() {
            $.uniform.restore("input");
        }, 3000);

    };


    self.resetPreviousSteps = function(){

    }



    function getLaddaButton(step) {
        return Ladda.create($('#' + step + "-button").get(0));
    }

    self.setLoadWaiting = function(step, keepDisabled) {
        if (step) {
            if (self.loadWaiting) {
                this.setLoadWaiting(false);
            }
            var container = $('#' + step+'-buttons-container');
            container.attr('disabled');
            container.css('{opacity:.5}');
            getLaddaButton(step).start();
            self._disableEnableAll(container, true);
        } else {
            if (this.loadWaiting) {
                var container = $('#' + this.loadWaiting+'-buttons-container');
                var isDisabled = (keepDisabled ? true : false);
                if (!isDisabled) {
                    container.removeAttr('disabled');
                    container.css('{opacity:1}');
                }
                self._disableEnableAll(container, isDisabled);
                getLaddaButton(this.loadWaiting).stop();
            }
        }
        this.loadWaiting = step;
    };


    self.gotoSection = function (section, reloadProgressBlock) {

        if (reloadProgressBlock) {
            // this.reloadProgressBlock(this.currentStep);
        }

        var sectionElement = $('#opc-' + section);
        sectionElement.addClass('allow');

        this.accordion.closeSection('opc-' + this.currentStep);
        this.accordion.openSection('opc-' + section);
        this.currentStep = section;

        if(!reloadProgressBlock) {
            this.resetPreviousSteps();
        }
    };


    self.setStepResponse = function (json) {

        if (json.allow_sections) {
            json.allow_sections.forEach(function(e){
                $('#opc-'+e).addClass('allow');
            });
        }
        return false;
    };

    self. _disableEnableAll =  function(element, isDisabled) {
        var children = element.find('button');
        for (var k in children) {
            children[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    };
    self.constructor(accordion);
}

var Shipping = function (form, url, customer, addresses, countries, current_country) {
    var self = this;


    self.constructor = function(form, url, customer, addresses, countries, current_country) {
        self.url = url;
        self.selectedShippingAddress = selectedShippingAddress;
        self.form = $('#' + form);
        self.customer = customer;
        self.countries = countries;

        self.addresses = [];
        if (addresses) {
            self.addresses = addresses;
        }

        self.current_country = current_country;

        if (self.form) {
            self.form.submit(self.save);
        }

        self.shippingAddressService = new AddressService(url, addresses);
        self.addressFormUpdater = new AddressFormUpdater('shipping',addresses, countries, customer);
    };

    self.getAddressId = function() {
        return $('input[name="shipping[address_id]"]').val()
    }

    self.setAddressId = function(addressId) {
        $('input[name="shipping[address_id]"]').val(addressId);
    }

    self.setFormFieldSet = function(fieldset) {
        self.addressFormUpdater.replaceFormFieldSet(fieldset);

        self.addressFormUpdater.updateState(self.countries);
        self.addressFormUpdater.updateZipcode(self.countries);
        self.addressFormUpdater.updateNeedDNI(self.countries);
        self.addressFormUpdater.showVatNumber();

    }

    self.isEditMode = function() {
        if(!isLogged) {
            return false;
        }
        if (self.customer && self.customer.id) {
            if (self.customer.is_guest) {
                //return (self.addresses.length >= 1);
                return (self.getAddressId());


            } else {

                //isLogged < 1
                //if (
                //|| $("#billing-address-edit-btn").data('clicked')
                return $('#shipping\\:edit').val() == '1'
                    || $("#shipping-address-edit-btn").data('clicked')
                    || $('select[name="shipping_address_id"] option:selected').val() != ""

            }
        } else {
            return false;
        }
    }

    self.start = function() {
        // self.initAddressForm();

        $('#shipping-address-select').change(function(event){
            selectedAddress = self.addressFormUpdater.getSelectedAddress();
            id_country =  (selectedAddress)? selectedAddress.id_country:self.current_country;
            self.shippingAddressService.getAddressForm(id_country, self.addressFormUpdater.reloadForm, 'shipping');
            // untick checkbox data shipping same as billing when change address selected.
            $('#shipping\\:same_as_billing').prop('checked', false);


        });

        $('#shipping-address-edit-btn').click(function(){
            if($('#shipping\\:edit').val() == '0') {
                $('#shipping\\:edit').val('1');
            } else {
                $('#shipping\\:edit').val('0');
            }
            $('#shipping-address-select').trigger('change');
        });

        $('#shipping\\:same_as_billing').change(function() {
            if($('#shipping\\:same_as_billing').is(':checked')) {
                $("input[name='billing[use_for_shipping]'][value='1']").attr("checked", true);
                // Check if country in shipping not same billing will pull form and sys-data.
                if ($('#shipping\\:country_id').val() == $('#billing\\:country_id').val()) {
                    self.shippingAddressService.getAddressForm($('#billing\\:country_id').val(), self.addressFormUpdater.reloadForm, 'shipping');
                }
                //sys data
                self.syncWithBilling();
            }
        });

        $('#shipping-address-select').change(function() {
            if($('#shipping-address-select').val() != $('#shipping-address-select').val()) {
                $('#shipping\\:same_as_billing').prop('checked', false);
            } else {
                // $('#shipping\\:same_as_billing').prop('checked', true);
            }
        });

        $.validate({lang : langCode});

    }

    self.onCountryChange = function (event) {
        countryId = $(event.srcElement).val();
        if (!self.isEditMode()) {
            self.shippingAddressService.getAddressForm(countryId, self.addressFormUpdater.reloadForm, 'shipping');
        } else {
            var data = self.shippingAddressService.getCountries(function(countries) {
                self.addressFormUpdater.updateState(countries);
                self.addressFormUpdater.updateNeedDNI(countries);
                self.addressFormUpdater.updateZipcode(countries);
            });
        }
        return false;
    };

    self.onCompanyChange = function(event) {
        self.addressFormUpdater.showVatNumber();
    }

    self.isLoggedCustomer = function() {
        return (customer && customer.id);
    }

    self.initAddressForm = function() {

        var selected_shipping_address = $('select[name="shipping_address_id"] option:selected').val();
        var selectedShippingngAddress;

        for (var i = 0; i < addresses.length; i++) {
            if (addresses[i].id_address == selected_shipping_address) {
                selectedShippingngAddress = addresses[i];
            }
        }

        var customer_firstname = "";
        var customer_lastname = "";
        var customer_company = "";
        if (self.isLoggedCustomer()) {
            customer_firstname = self.customer.firstname;
            customer_lastname = self.customer.lastname;
            if (self.customer.company) {
                customer_company = self.customer.company;
            }
        }

        // if (selectedShippingngAddress === undefined) return;
        if (selectedShippingngAddress === undefined) {

            return;
        }

        if (selectedShippingngAddress.id_customer === '0') return;

        self.newAddress(!selected_shipping_address, self.customer.is_guest);

        // Fill invoice address
        $('input[name="shipping[firstname]"]').val(selectedShippingngAddress.firstname);
        $('input[name="shipping[lastname]"]').val(selectedShippingngAddress.lastname);
        $('input[name="shipping[company]"]').val(selectedShippingngAddress.company);
        $('input[name="shipping[vat_number]"]').val(selectedShippingngAddress.vat_number);
        $('input[name="shipping[street]"]').val(selectedShippingngAddress.address1);
        $('input[name="shipping[street2]"]').val(selectedShippingngAddress.address2);
        $('input[name="shipping[postcode]"]').val(selectedShippingngAddress.postcode);
        $('input[name="shipping[city]"]').val(selectedShippingngAddress.city);
        $('input[name="shipping[phone_mobile]"]').val(selectedShippingngAddress.phone_mobile);
        $('input[name="shipping[telephone]"]').val(selectedShippingngAddress.phone);
        $('input[name="shipping[alias]"]').val(selectedShippingngAddress.alias);
        $('input[name="shipping[dni]"]').val(selectedShippingngAddress.dni);
        $('textarea[name="shipping[other]"]').val(selectedShippingngAddress.other);

        $('#shipping\\:country_id').val(selectedShippingngAddress.id_country);
        // $('#shipping\\:country_id').trigger('change');
        $('#shipping\\:state_id').val(selectedShippingngAddress.id_state);
        $('#shipping\\:state_id').trigger('change');
    };

    self.reloadAddressBox = function() {
        var options = [];
        if(selectedShippingAddress == 0) {
            options.push('<option value="" selected="selected">'+ newAddressLabel +'</option>');
        } else {
            options.push('<option value="">'+ newAddressLabel +'</option>');
        }
        $.each(self.addresses, function(i, address) {
            if(selectedShippingAddress == address.id_address) {
                options.push('<option value="' + address.id_address + '" selected="selected">' + address.alias + '</option>');
            } else {
                options.push('<option value="' + address.id_address + '">' + address.alias + '</option>');
            }
        });

        if (isLogged) {
            $('#shipping-address-select').html(options.join(""));
            $('#shipping-address-select').val(self.selectedShippingAddress);
            $('#shipping-address-select').trigger('change');
        }
    };

    self.save = function () {
        // var validator = new Validation(this.form);
        if($('#shipping\\:same_as_billing').is(':checked')) {
            $("input[name='billing[use_for_shipping]'][value='1']").attr("checked", true);
        }
        if (self.form.isValid()) {
            checkout.setLoadWaiting('shipping');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + "&ajax=true" + "&submitGuestAccount=true&step=shipping" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function () {

                },
                complete: function () {
                    self.resetLoadWaiting(false);
                },
                success: function (json) {
                    self.nextStep(json);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }

    };

    self.resetSelectedAddress = function(){
        var selectElement = $('#shipping-address-select');
        if (selectElement) {
            selectElement.value='';
        }
    };

    self.newAddress = function(isNew, isGuest){
        if (isNew) {
            self.resetSelectedAddress();
            $('#shipping-new-address-form').show();
        } else {
            if(isGuest == 1) {
                $('#shipping-new-address-form').show();
            } else {
                if($('#shipping\\:edit').val() == '0') {
                    $('#shipping-new-address-form').hide();
                } else {
                    $('#shipping-new-address-form').show();
                }
            }
        }
    };

    self.setUseForShipping = function(flag) {
        $('#shipping\\:same_as_billing').checked = flag;
    };

    self.resetLoadWaiting = function(transport){
        checkout.setLoadWaiting(false);
        // document.body.fire('shipping-request:completed', {transport: transport});
    };

    self.nextStep = function (json) {
        if (json.hasError){
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        //
        selectedBillingAddress = json.id_address_invoice;
        self.selectedShippingAddress = json.id_address_delivery;

        self.setAddressId(self.selectedShippingAddress);

        addresses = json.addresses;
        self.addresses = json.addresses;
        self.addressFormUpdater.addresses = json.addresses;

        billing.addresses = json.addresses;
        billing.formUpdater.addresses = json.addresses;

        if (self.addressFormUpdater.isValidCustomer()) {
            self.reloadAddressBox();
            billing.reloadAddressBox();
        }

        if (json.updated_section) {
            $('#checkout-step-shipping_method').html(json.updated_section);
        }

        if (selectedBillingAddress == self.selectedShippingAddress) {
            billing.syncWithShipping();

        }

        checkout.gotoSection(json.goto_section, true);
        checkout.setStepResponse(json);

        // Hide form after save success.
        self.addressFormUpdater.hideForm();
    };

    self.fillForm = function (address) {

    }

    self.setSameAsBilling = function(isSame) {
        $('#shipping\\:same_as_billing').checked = isSame;

        if (isSame) {
            $("input[name='billing[use_for_shipping]'][value='1']").attr("checked", true);
            self.syncWithBilling();
        }
    }

    self.syncWithBilling = function() {

        billingSerialized = billing.getFormData();

        /** TODO use more sophisticated regex */
        serialized = billingSerialized.replace(/billing_/g,'shipping_');
        serialized = serialized.replace(/billing%5B/g,'shipping%5B');

        // this must be called before deserialize
        // otherwise state is not selected
        self.addressFormUpdater.updateState(self.countries);
        self.addressFormUpdater.updateZipcode(self.countries);

        self.form.deserialize(serialized);

        self.addressFormUpdater.updateNeedDNI(self.countries);
        self.addressFormUpdater.showVatNumber();

    }

    self.getFormData = function(){
        serialized = self.form.serialize();
        return serialized;
    }

    self.constructor(form,url, customer, addresses, countries, current_country);
}

var ShippingMethod = function (form,url) {
    var self = this;
    var vars = {

    };

    self.constructor = function(form, url) {
        self.url = url;
        self.form = $('#' + form);
    };

    self.save = function () {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('shipping-method');

            var formData = ($('.myopccheckout_shipping_option').length)? '&'+$('.myopccheckout_shipping_option:checked').attr('name')+'='+$('.myopccheckout_shipping_option:checked').attr('value') : '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + '&ajax=true' + '&method=updateCarrier' + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function () {

                },
                complete: function () {
                    self.resetLoadWaiting(false);
                },
                success: function (json) {
                    self.nextStep(json);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }

    };

    self.nextStep = function(json) {
        if (json.hasError){
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        if (json.updated_section) {
            $('#checkout-step-payment').html(json.updated_section);
        }
        checkout.gotoSection(json.goto_section, true);
        checkout.setStepResponse(json);
    };


    self.resetLoadWaiting = function(transport){
        checkout.setLoadWaiting(false);
    };

    self.constructor(form,url);
}

var PaymentMethod = function (form,url)  {
    var self = this;
    var vars = {

    };

    self.constructor = function(form, url) {
        self.url = url;
        self.form = $('#' + form);
    };

    self.save = function () {
        // var validator = new Validation(this.form);
        var formData = $('#co-payment-form').serialize();

        checkout.setLoadWaiting('payment');

        $.ajax({
            type: 'POST',
            headers: {"cache-control": "no-cache"},
            url: self.url + "&ajax=true" + "&method=updatePayment" + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: formData,
            beforeSend: function () {

            },
            complete: function () {
                self.resetLoadWaiting(false);
            },
            success: function (json) {
                self.nextStep(json);
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.nextStep = function(json) {
        if (json.hasError){
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        if (json.updated_section) {
            $('#checkout-review-load').html(json.updated_section);
        }
        checkout.gotoSection(json.goto_section, true);
    };

    self.resetLoadWaiting = function(transport){
        checkout.setLoadWaiting(false);
    };


    self.constructor(form,url);
}

var Review = function (paymentForm, agreementsForm, url) {
    var self = this;
    var vars = {

    };

    self.constructor = function(paymentForm, agreementsForm, url) {
        self.url = url;
        self.paymentForm = $('#' + paymentForm);
        self.agreementsForm = $('#' + agreementsForm);

        $.validate({lang : langCode});

    };

    self.nextStep = function(json) {
        if (json.hasError){
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        $modal =  $('#paymentModal');


        //trigger confirm order after confirming payment in dialog
        $("#paymentModal #payment_dialog_proceed").click(function() {
            self.confirmOrder();
        });

        var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
        var payment_module_id = $('input:radio[name="payment_method"]:checked').val();
        var id = $('input:radio[name="payment_method"]:checked').val();
        var url = $('#'+$('input:radio[name="payment_method"]:checked').attr('value')+'_name').val();

        if(payment_module_name == 'bankwire' || payment_module_name == 'myinvoice' || payment_module_name == 'cheque' || payment_module_name == 'cashondelivery' || payment_module_name == 'pscodfee' || payment_module_name == 'codfee') {
            // Show loader & then get content when modal is shown

            $modal.on('show.bs.modal', function (e) {
                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')
                    .load(url + ' body', function (dataHtml) { // PS-971: Prevent script execution
                        // Use Bootstrap's built-in function to fix scrolling (to no avail)

                        var payment_info_html = $(dataHtml).find('#center_column');
                        $(payment_info_html).find('#order_step').remove();
                        $('h1', payment_info_html).remove();
                        $('#cart_navigation', payment_info_html).remove();
                        $('.cart_navigation', payment_info_html).remove();      // Added for Prestashop 1.5 for removing the buttons in the payment method html
                        $('#amount', payment_info_html).removeClass('price');
                        $(payment_info_html).find('form:first').find('div:first, div.box').find('p:last-child').remove();
                        $(payment_info_html).find('form:first').find('div:first, div.box').find('#currency_payement').parent().hide();
                        if(payment_module_name == 'codfee') {
                            $(payment_info_html).find('form').find('strong').hide();
                        }
                        $modal.find('.modal-body').html(payment_info_html.html());
                        $modal.modal('handleUpdate');
                    });
            }).modal();
        } else if(payment_module_name === 'paypal') {
            if (url.indexOf('javascript:') !== -1) {
                $('#paypal_process_payment').trigger('click');
                if ($("#paypal_payment_form_payment").length === 1) {
                    $('#paypal_payment_form_payment').submit();
                } else if ($("#paypal_payment_form").length === 1) {
                    $('#paypal_payment_form').submit();
                }
            }
        } else if(payment_module_name == 'paypalusa') {
            if(url == '') {
                $('#paypal-standard-btn').trigger('click');
            }
        }
        else if(payment_module_name == 'pronesis_bancasella'){
            $('#bancasella_process_payment').trigger('click');
        } else if(payment_module_name == 'redsys') {
            $('#redsys_form').submit();
        } else if (payment_module_name == 'payplug') {
            $('a.payplug')[0].click();
        } else if (payment_module_name == 'systempay') {
            $('#systempay_standard').submit();
            // $('#systempay_standard').trigger('click');

        } else if (payment_module_name == 'sisowideal') { // PS-649 : Support Payment Method - Sisow iDEAL
            var formSisowideal = $('#sisow_ideal_form').serialize();
            var url_ajax_sisowideal = $( '#sisow_ideal_form' ).attr( 'action' );
            var varlue_respose = '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url_ajax_sisowideal,
                // dataType: "json",
                data: formSisowideal,
                complete: function () {

                },
                success: function (response) {
                    payment_info_html = $(response).find("#center_column");
                    payment_info_html.find('#order_step').hide();
                    payment_info_html.find('h2').hide();
                    payment_info_html.find('#cart_navigation').hide();
                    var title = payment_info_html.find('h3').text();
                    payment_info_html.find('h3').remove();

                    $modal.find('#paymentModalLabel').text(title);
                    $modal.find('.modal-body').html(payment_info_html.html());
                    $modal.modal('handleUpdate');
                    console.log(result);
                },
                error: function(request, textStatus, errorThrown) {
                    alert('fail');
                }

            });
            $modal.on('show.bs.modal', function (e) {

                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')

            }).modal();
        } else if (payment_module_name == 'sisowcapayable') { // PS-648 : Support Payment Method - Sisow Capayable in 3 installments (0% interest)
            var formSisowcapayable = $('#sisow_capayable_form').serialize();
            var url_ajax_sisowcapayable = $( '#sisow_capayable_form' ).attr( 'action' );
            var varlue_respose = '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url_ajax_sisowcapayable,
                // dataType: "json",
                data: formSisowcapayable,
                complete: function () {

                },
                success: function (response) {
                    payment_info_html = $(response).find("#center_column");
                    payment_info_html.find('#order_step').hide();
                    payment_info_html.find('h2').hide();
                    payment_info_html.find('#cart_navigation').hide();
                    payment_info_html.find('.breadcrumb').hide();
                    $modal.find('.modal-body').html(payment_info_html.html());
                    $modal.modal('handleUpdate');
                    console.log(result);
                },
                error: function(request, textStatus, errorThrown) {
                    alert('fail');
                }

            });
            $modal.on('show.bs.modal', function (e) {

                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')

            }).modal();
        } else if (payment_module_name == 'sisowafterpay') { //PS-645 : Support Payment Method - Sisow Pay after Receipt
            var formSisowafterpay = $('#sisow_afterpay_form').serialize();
            var url_ajax_sisowafterpay = $( '#sisow_afterpay_form' ).attr( 'action' );
            var varlue_respose = '';
            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: url_ajax_sisowafterpay,
                // dataType: "json",
                data: formSisowafterpay,
                complete: function () {

                },
                success: function (response) {
                    payment_info_html = $(response).find("#center_column");
                    payment_info_html.find('#order_step').hide();
                    payment_info_html.find('h2').hide();
                    payment_info_html.find('#cart_navigation').hide();
                    payment_info_html.find('.breadcrumb').hide();
                    $modal.find('.modal-body').html(payment_info_html.html());
                    $modal.modal('handleUpdate');
                    console.log(result);
                },
                error: function(request, textStatus, errorThrown) {
                    alert('fail');
                }

            });
            $modal.on('show.bs.modal', function (e) {

                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')

            }).modal();
        } else if (payment_module_name == 'sisowpp') { //PS-653 : Support Payment Method - Sisow PayPal
            $('#sisow_paypalec_form').submit();
        } else if (payment_module_name == 'sisowbelfius') { //PS-647 : Support Payment Method -Sisow belfius
            $('#sisow_belfius_form').submit();
        } else if (payment_module_name == 'sisowmaestro') { //PS-661 : Support Payment Method - Sisow Maestro
            $('#sisow_maestro_form').submit();
        } else if (payment_module_name == 'sisowmastercard') { //PS-650 : Support Payment Method - Sisow MasterCard
            $('#sisow_mastercard_form').submit();
        } else if (payment_module_name == 'sisowvisa') { //PS-643 : Support Payment Method - Sisow Visa
            $('#sisow_visa_form').submit();
        } else if (payment_module_name == 'sisowmc') { //PS-646 : Support Payment Method - Sisow Bancontact
            $('#sisow_mistercash_form').submit();
        } else if (payment_module_name == 'sisowde') { //PS-644 : Support Payment Method - Sisow SofortBanking
            $('#sisow_sofort_form').submit();
        } else  if (payment_module_name === 'stripe_official') {
            if (!this.payment_form_html) {
                var payment_form = $('#payment_method_' + payment_module_id);
                if (payment_form.length) {
                    this.payment_form_html = payment_form.html();
                    payment_form.remove();
                }
            }
            if (this.payment_form_html) {
                $modal.find('.modal-body').html(this.payment_form_html);
                $modal.find('.modal-footer').hide();
                $modal.modal('show');
            }
        }
    };

    self.confirmOrder = function() {
        $modal =  $('#paymentModal form').submit();

    }


    self.save = function() {
        if(conditionEnabled) {
            if (!$('#agreement-1').is(':checked')) {
                alert(TOSMsg);
                return;
            }
        }
        if (checkout.loadWaiting!=false)
            return;
        if (self.paymentForm.isValid()) {
            checkout.setLoadWaiting('review');

            var params = self.paymentForm.serialize();
            if (this.agreementsForm) {
                params += '&'+ self.agreementsForm.serialize();
            }

            $.ajax({
                type: 'POST',
                headers: {"cache-control": "no-cache"},
                url: self.url + "&ajax=true" + "&PlaceOrder=1" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: params,
                beforeSend: function () {

                },
                complete: function () {
                    self.resetLoadWaiting();
                },
                success: function (json) {
                    self.nextStep(json);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }
    };


    self.resetLoadWaiting = function(){
        checkout.setLoadWaiting(false);
    };

    self.constructor(paymentForm, agreementsForm, url);

}

var Coupon = function(form,url) {
    var self = this;
    var vars = {

    };

    self.constructor = function(form, url) {

        self.url = url;
        self.form = $('#' + form); //discount-coupon-form

        self.inputField = self.form.find('#coupon_code');
        self.removeField = self.form.find('#remove-coupone');

        if (self.form) {
            self.form.submit(function(event){
                self.save(event.data.isRemove);
            });
        }
    };


    self.resetLoadWaiting = function () {
        checkout.setLoadWaiting(false);
    }

    self.save = function(isRemove) {

        if (isRemove) {
            self.inputField.removeClass('required-entry');
            self.removeField.value = "1";
        } else {
            self.inputField.addClass('required-entry');
            self.removeField.value = "0";
        }

        var formData = $('#co-coupon-form').serialize();
        $.ajax( {
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&submitDiscount=true' +'&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            data: formData,
            dataType: 'json',
            beforeSend: function() {
                //$('#cart_update_warning .permanent-warning').remove();
                //$('#confirmLoader').show();
            },
            complete: function() {
                // $('#confirmLoader').hide();
            },
            success: function( json ) {
                self.nextStep(json);
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.remove = function(discountId) {
        $.ajax( {
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&deleteDiscount=' + discountId +'&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: 'json',
            beforeSend: function() {
                //$('#cart_update_warning .permanent-warning').remove();
                //$('#confirmLoader').show();
            },
            complete: function() {
                // $('#confirmLoader').hide();
            },
            success: function( json ) {
                self.nextStep(json);
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    };

    self.setLoadWaiting = function(show) {

        if (show) {
            if (self.loadWaiting) {
                this.setLoadWaiting(false);
            }

            var container = $('#' + step+'-buttons-container');
            container.attr('disabled');
            container.css('{opacity:.5}');
            self.getLaddaButton().start();
            self._disableEnableAll(container, true);

        } else {
            if (self.loadWaiting) {
                var container = $('#' + this.loadWaiting+'-buttons-container');
                var isDisabled = (keepDisabled ? true : false);
                if (!isDisabled) {
                    container.removeAttr('disabled');
                    container.css('{opacity:1}');
                }
                self._disableEnableAll(container, isDisabled);
                self.getLaddaButton(step).stop();
            }
        }
        self.loadWaiting = show;
    }

    self.getLaddaButton = function() {

    }

    self._disableEnableAll = function () {

    }



    self.nextStep = function(json) {
        if (json.hasError){
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        }

        if (json.updated_section) {
            $('#checkout-review-load').html(json.updated_section);
        }
    };

    self.constructor(form,url);
}

var JMAgreement = function (form) {
    var self = this;
    self.constructor = function(form) {
        self.form = $('#'+form);
        self.initModals();
    };


    self.initModals = function() {
        self.form.select('div.modal').each(function (modal) {
            $(modal).find('a').click(function (e) {
                e.preventDefault();
            });
            $(modal).modal({
                show: false
            });
        });
    }
    self.constructor(form);
}

var AddressService = function (url) {
    var self = this;


    self.constructor = function(url) {
        self.url = url;
    }

    self.constructor(url);

    self.getAddressForm = function (countryId, callback, step) {

        $.ajax( {
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&ReloadFormField=true'+'&step='+step+'&rand=' + new Date().getTime(),
            async: true,
            data :{ id_country : countryId},
            cache: false,
            beforeSend: function() {
                //TODO Implement this
            },
            complete: function() {
            },
            success: function (json) {
                callback(JSON.parse(json));
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    }

    self.getCountries = function (callback) {

        $.ajax( {
            type: "POST",
            headers: { "cache-control": "no-cache" },
            url: self.url + '&ajax=true'+ '&get_countries=true'+'&rand=' + new Date().getTime(),
            async: true,
            data :{},
            cache: false,
            beforeSend: function() {
                //TODO Implement this
            },
            complete: function() {
            },
            success: function (json) {
                callback(JSON.parse(json));
            },
            error: function(request, textStatus, errorThrown) {
                if (request.readyState == 4) {
                    alert(request.responseText);
                } else if (request.readyState == 0) {
                    alert(networkErrorMsg);
                } else {
                    alert(unknownErrorMsg);
                }
            }
        });
    }


}

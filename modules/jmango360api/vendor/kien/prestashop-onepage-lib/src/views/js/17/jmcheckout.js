/**
 * @license
 */
var Accordion = function(elm, clickableEntity, checkAllow) {
    var self = this;

    var vars = {
        sections: []
    };

    self.disallowAccessToNextSections = false;

    self.construct = function(elm, clickableEntity, checkAllow) {
        var options = {
            container: '#' + elm,
            checkAllow: checkAllow,
            clickableEntity: clickableEntity,
            currentSectionId: false,
        };

        self.sections = vars.sections;

        $.extend(vars, options);

        $(vars.container).find(vars.clickableEntity).each(function() {
            var sectionId = $(this).parent('li').attr('id');
            vars.sections.push(sectionId);
            $(this).click(function(event) {
                if (sectionId != vars.currentSectionId) {
                    if (vars.checkAllow) {
                        if (self.checkSectionAllow(sectionId)) {
                            self.closeCurrentSection();
                            self.openSection(sectionId);
                        }
                    } else {
                        self.closeCurrentSection();
                        self.openSection(sectionId);
                    }
                }
            });
        });
    };

    self.checkSectionAllow = function(sectionId) {
        var section = $('#' + sectionId);
        if (section.hasClass('allow')) {
            return true;
        }
        return false;
    }

    self.openSection = function(sectionId) {
        var section = $('#' + sectionId);
        if (section != null && section != 'undefined') {
            section.find('.a-item').collapse('show');
            vars.currentSectionId = sectionId;
        }

        if (self.disallowAccessToNextSections) {
            var pastCurrentSection = false;
            for (var i = 0; i < self.sections.length; i++) {
                if (pastCurrentSection) {
                    $('#' + self.sections[i]).removeClass('allow');
                }
                if (this.sections[i] == sectionId) {
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

    self.openNextSection = function(setAllow) {
        var nextIndex = 0;
        for (var i = 0; i < vars.sections.length; i++) {
            var nextIndex = i + 1;
            var sectionId = vars.sections[i];
            if (sectionId == vars.currentSectionId && vars.sections[nextIndex]) {
                if (setAllow) {
                    $('#' + vars.sections[nextIndex]).addClass('allow');
                }
                self.closeCurrentSection();
                self.openSection(vars.sections[nextIndex]);

                return;
            }
        }
    };

    self.openPrevSection = function(setAllow) {
        for (var i = vars.sections.length - 1; i > 0; i--) {
            var sectionId = vars.sections[i];
            var prevIndex = i - 1;
            if (sectionId == vars.currentSectionId && vars.sections[prevIndex]) {
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
        if (vars.currentSectionId) {
            self.closeSection(vars.currentSectionId);
        }
    }

    self.construct(elm, clickableEntity, checkAllow);
}

var PersonalInformation = function(form, url) {
    var self = this;
    var vars = {};

    self.constructor = function(form, url) {
        self.url = url.replace(/\/$/, '');
        self.form = $('#' + form);

        if (self.form) {
            self.form.submit(self.save);
        }


        $.validate({
            lang: langCode
        });
    };

    self.resetLoadWaiting = function(transport) {
        checkout.setLoadWaiting(false);
    };

    self.save = function() {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('personal-information');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?ajax=true&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                    self.resetLoadWaiting(false);
                },
                success: function(json) {
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
        if (json.hasError) {
            if ((typeof json.errors) == 'string') {
                var plainText = $("<div/>").html(json.errors).text();
                alert(plainText);
            } else {
                var errors = json.errors.join("\n");
                var plainText = $("<div/>").html(errors).text();
                alert(plainText);
            }
            return false;
        } else {
            checkout.gotoSection(json.goto_section, true);
            if (json.updated_section && 'billing' in json.updated_section) {
                $('#billing-address').html(json.updated_section.billing);
            }
        }
    };

    self.constructor(form, url);
}

var Billing = function(form, url) {
    var self = this;
    var vars = {};

    self.constructor = function(form, url) {
        self.url = url.replace(/\/$/, '');
        self.form = $('#' + form);

        if (self.form) {
            self.form.submit(self.save);
        }

        $(document).on('change', 'select[name=id_country]', function(e) {
            var selectedCountry = $('select[name=id_country]').val();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?method=changeCountry&ajax=true' + '&id_country=' + selectedCountry + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                beforeSend: function() {

                },
                complete: function() {
                    // self.resetLoadWaiting(false);
                },
                success: function(json) {
                    var input = [];
                    $('#billing-address').find(':input').each(function() {
                        input[$(this).attr('name')] = $(this).val();
                    });
                    /**
                     * Only set id_state again if it not found and country field available in form
                     */
                    if ($('select[name="id_country"]', '#billing-address').length && !input['id_state']) {
                        input['id_state'] = $('select[name="id_country"]', '#billing-address').data('id_state');
                    }

                    if (json.updated_section && ('billing' in json.updated_section)) {
                        $('#billing-address').html(json.updated_section.billing);
                    }

                    $('#billing-address').find(':input').each(function() {
                        $(this).val(input[$(this).attr('name')]);
                    });
                    if ($('select[name="id_state"]', '#billing-address').length && input['id_state']) {
                        $('select[name="id_state"]', '#billing-address').val(input['id_state']);
                    }
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
        });

        self.initAddressForm();


        $('#billing-address-select').change(function() {
            self.initAddressForm();

          if(isLogged) {
              if ($('#billing-address-select').val()) {
              $('#billing-new-address-form').hide();
              $('#japi-billing-address-edit').show();
              $('#billing\\:edit').val('0');
            } else {
              $('#billing-new-address-form').show();
              $('#japi-billing-address-edit').hide();
              $('#billing\\:edit').val('1');
            }
          } else {
            $('#billing-new-address-form').show();
            $('#japi-billing-address-edit').hide();
          }
        });

        $('#billing-address-edit-btn').click(function() {
            if ($('#billing\\:edit').val() == '0') {
                $('#billing\\:edit').val('1');
                $('#billing-new-address-form').show();
            } else {
                $('#billing\\:edit').val('0');
                $('#billing-new-address-form').hide();
            }
        });

        // $('#billing-new-address-form').hide();
        $.validate({
            lang: langCode
        });
    };

    self.save = function() {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('billing');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?ajax=true' + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                    self.resetLoadWaiting(false);
                },
                success: function(json) {
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

    self.initAddressForm = function() {
        var selected_billing_address = $('select[name="billing_address_id"] option:selected').val();
        var selectedBillingAddress;
        for (var i = 0; i < addresses.length; i++) {
            if (addresses[i].id_address == selected_billing_address) {
                selectedBillingAddress = addresses[i];
            }
        }

        if(isLogged) {
            if ($('#billing-address-select').val()) {
                $('#billing-new-address-form').hide();
                $('#japi-billing-address-edit').show();
                $('#billing\\:edit').val('0');
            } else {
                $('#billing-new-address-form').show();
                $('#japi-billing-address-edit').hide();
                $('#billing\\:edit').val('1');
            }
        } else {
            $('#billing-new-address-form').show();
            $('#japi-billing-address-edit').hide();
        }

        if(isLogged) {
            if ($('#shipping-address-select').val()) {
                $('#shipping-new-address-form').hide();
                $('#japi-shipping-address-edit').show();
                $('#shipping\\:edit').val('0');
            } else {
                $('#shipping-new-address-form').show();
                $('#japi-shipping-address-edit').hide();
                $('#shipping\\:edit').val('1');
            }
        } else {
            $('#shipping-new-address-form').show();
            $('#japi-shipping-address-edit').hide();
        }

        if (selectedBillingAddress === undefined) return;

        if (selectedBillingAddress.id_customer === '0') return;

        self.newAddress(!selected_billing_address, customer.is_guest);

        // Fill invoice address
        $('input[name="firstname"]').val(selectedBillingAddress.firstname);
        $('input[name="lastname"]').val(selectedBillingAddress.lastname);
        $('input[name="company"]').val(selectedBillingAddress.company);
        $('input[name="vat_number"]').val(selectedBillingAddress.vat_number);
        $('input[name="address1"]').val(selectedBillingAddress.address1);
        $('input[name="address2"]').val(selectedBillingAddress.address2);
        $('input[name="postcode"]').val(selectedBillingAddress.postcode);
        $('input[name="city"]').val(selectedBillingAddress.city);
        $('input[name="phone"]').val(selectedBillingAddress.phone);
        $('input[name="phone_mobile"]').val(selectedBillingAddress.phone_mobile);
        $('input[name="alias"]').val(selectedBillingAddress.alias);
        $('input[name="dni"]').val(selectedBillingAddress.dni);

        $('select[name="id_country"]').val(selectedBillingAddress.id_country);
        $('select[name="id_country"]').trigger('change');
        $('select[name="id_state"]').val(selectedBillingAddress.id_state);
        /**
         * Store id_state to country field, which always available in all address form
         * We will use this value when states load complete
         */
        $('select[name="id_country"]').data('id_state', selectedBillingAddress.id_state);
        $('select[name="id_state"]').trigger('change');
    }

    self.resetSelectedAddress = function() {
        var selectElement = $('#billing-address-select');
        if (selectElement) {
            selectElement.value = '';
        }
    };

    self.newAddress = function(isNew, isGuest) {
        if (isNew) {
            self.resetSelectedAddress();
            $('#billing-new-address-form').show();
        } else {
            if (isGuest == 1) {
                $('#billing-new-address-form').show();
            } else {
                if ($('#billing\\:edit').val() == '0') {
                    $('#billing-new-address-form').hide();
                } else {
                    $('#billing-new-address-form').show();
                }
            }
        }
    };

    self.reloadAddressBox = function() {
        var options = [];
        if (selectedBillingAddress == 0) {
            options.push('<option value="" selected="selected">' + newAddressLabel + '</option>');
        } else {
            options.push('<option value="">' + newAddressLabel + '</option>');
        }
        $.each(addresses, function(i, address) {
            var alias = address.firstname + ' ' + address.lastname +
                (address.address1 ? ', ' + address.address1 : '') +
                (address.address2 ? ', ' + address.address2 : '') +
                (address.city ? ', ' + address.city : '') +
                (address.postcode ? ', ' + address.postcode : '') +
                (address.phone ? ', ' + address.phone : '') +
                (address.phone_mobile ? ', ' + address.phone_mobile : '');
            if (selectedBillingAddress == address.id_address) {
                options.push('<option value="' + address.id_address + '" selected="selected">' + alias + '</option>');
            } else {
                options.push('<option value="' + address.id_address + '">' + alias + '</option>');
            }
        });
        $('#billing-address-select').html(options.join(""));
        $('#billing-address-select').trigger('change');


        //reload Shipping adrress list
        options = [];
        if (selectedShippingAddress == 0) {
            options.push('<option value="" selected="selected">' + newAddressLabel + '</option>');
        } else {
            options.push('<option value="">' + newAddressLabel + '</option>');
        }
        $.each(addresses, function(i, address) {
            var alias = address.firstname + ' ' + address.lastname +
                (address.address1 ? ', ' + address.address1 : '') +
                (address.address2 ? ', ' + address.address2 : '') +
                (address.city ? ', ' + address.city : '') +
                (address.postcode ? ', ' + address.postcode : '') +
                (address.phone ? ', ' + address.phone : '') +
                (address.phone_mobile ? ', ' + address.phone_mobile : '');
            if (selectedShippingAddress == address.id_address) {
                options.push('<option value="' + address.id_address + '" selected="selected">' + alias + '</option>');
            } else {
                options.push('<option value="' + address.id_address + '">' + alias + '</option>');
            }
        });
        $('#shipping-address-select').html(options.join(""));
    }


    self.setUseForShipping = function(flag) {
        $('#shipping\\:same_as_billing').checked = flag;
    };

    self.resetLoadWaiting = function(transport) {
        checkout.setLoadWaiting(false);
    };

    self.nextStep = function(json) {
        if (json.hasError) {
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
            if (json.goto_section == 'shipping_method') {
                if ('shipping_method' in json.updated_section) {
                    $('#checkout-step-shipping_method').html(json.updated_section.shipping_method);
                }
            }
            if ('shipping' in json.updated_section) {
                $('#shipping-address').html(json.updated_section.shipping);
            }
        }
        selectedBillingAddress = json.id_address_invoice;
        selectedShippingAddress = json.id_address_delivery;
        addresses = json.addresses;
        if (addresses) {
            self.reloadAddressBox();
        }
        // shipping.reloadAddressBox();

        checkout.gotoSection(json.goto_section, true);
        checkout.setStepResponse(json);
        //
        // if(json.duplicateBillingInfo) {
        //     if(shipping != 'undefined') {
        //         shipping.syncWithBilling();
        //     }
        // }
    };

    self.fillForm = function(address) {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('billing');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + "&ajax=true" + "&submitGuestAccount=true" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {

                },
                success: function(json) {
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


    self.constructor(form, url);
}

var Checkout = function(accordion) {
    var self = this;

    self.constructor = function(accordion) {
        self.accordion = accordion;
        self.steps = ['personal-information', 'billing', 'shipping', 'shipping_method', 'payment', 'review'];
        self.loadWaiting = false;
        self.accordion.disallowAccessToNextSections = true;


        self.accordion.sections.forEach(function(section) {

            var section_elm = $('#' + section);
            section_elm.find('.step-title').click(function(event) {
                event.stopImmediatePropagation();
                if (section_elm.hasClass('allow')) {
                    event.stopImmediatePropagation();
                    self.gotoSection(section.replace('opc-', ''), false);
                    return false;
                }

            });
        });

        if (isLogged) {
            self.currentStep = 'billing';
            self.gotoSection('billing', true);
        } else {
            self.currentStep = 'personal-information';
            self.gotoSection('personal-information', true);
        }


        // setTimeout(function () {
        //     $.uniform.restore("input");
        // }, 3000);

    };


    self.resetPreviousSteps = function() {

    }


    function getLaddaButton(step) {
        return Ladda.create($('#' + step + "-button").get(0));
    }

    self.setLoadWaiting = function(step, keepDisabled) {
        if (step) {
            if (self.loadWaiting) {
                this.setLoadWaiting(false);
            }
            var container = $('#' + step + '-buttons-container');
            container.attr('disabled');
            container.css('{opacity:.5}');
            getLaddaButton(step).start();
            self._disableEnableAll(container, true);
        } else {
            if (this.loadWaiting) {
                var container = $('#' + this.loadWaiting + '-buttons-container');
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


    self.gotoSection = function(section, reloadProgressBlock) {

        if (reloadProgressBlock) {
            // this.reloadProgressBlock(this.currentStep);
        }

        var sectionElement = $('#opc-' + section);
        if(section !== 'review') {
            sectionElement.addClass('allow');
        }

        this.accordion.closeSection('opc-' + this.currentStep);
        this.accordion.openSection('opc-' + section);
        this.currentStep = section;

        if (!reloadProgressBlock) {
            this.resetPreviousSteps();
        }
    };


    self.setStepResponse = function(json) {

        if (json.allow_sections) {
            json.allow_sections.forEach(function(e) {
                $('#opc-' + e).addClass('allow');
            });
        }
        return false;
    };

    self._disableEnableAll = function(element, isDisabled) {
        var children = element.find('button');
        for (var k in children) {
            children[k].disabled = isDisabled;
        }
        element.disabled = isDisabled;
    };
    self.constructor(accordion);
}

var Shipping = function(form, url) {
    var self = this;
    var vars = {};

    self.constructor = function(form, url) {
        self.url = url;
        self.form = $('#' + form);

        if (self.form) {
            self.form.submit(self.save);
        }

        $(document).on('change', '#shipping\\:id_country', function() {
            var selectedCountry = $('#shipping\\:id_country').val();
            if ($('#shipping-new-address-form').is(":visible")) {
                checkout.setLoadWaiting('shipping');
            }
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?method=changeDeliveryCountry&ajax=true' + '&id_country=' + selectedCountry + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                beforeSend: function() {

                },
                complete: function() {
                    self.resetLoadWaiting(false);
                },
                success: function(json) {
                    var input = [];
                    $('#shipping-address').find(':input').each(function() {
                        input[$(this).attr('name')] = $(this).val();
                    });
                    /**
                     * Only set id_state again if it not found and country field available in form
                     */
                    if ($('#shipping\\:id_country').length && !input['id_state']) {
                        input['id_state'] = $('#shipping\\:id_country').data('id_state');
                    }

                    if (json.updated_section && ('shipping' in json.updated_section)) {
                        $('#shipping-address').html(json.updated_section.shipping);
                    }

                    $('#shipping-address').find(':input').each(function() {
                        $(this).val(input[$(this).attr('name')]);
                    });
                    if ($('#shipping\\:id_state').length && input['id_state']) {
                        $('#shipping\\:id_state').val(input['id_state']);
                    }
                    self.resetLoadWaiting(false);
                },
                error: function(request, textStatus, errorThrown) {
                    self.resetLoadWaiting(false);
                    if (request.readyState == 4) {
                        alert(request.responseText);
                    } else if (request.readyState == 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        });

        self.initAddressForm();

      $('#shipping-address-select').change(function() {
        self.initAddressForm();

        if(isLogged) {
          if ($('#shipping-address-select').val()) {
            $('#shipping-new-address-form').hide();
            $('#japi-shipping-address-edit').show();
            $('#shipping\\:edit').val('0');
          } else {
            $('#shipping-new-address-form').show();
            $('#japi-shipping-address-edit').hide();
            $('#shipping\\:edit').val('1');
          }
        } else {
          $('#shipping-new-address-form').show();
          $('#japi-shipping-address-edit').hide();
        }
      });

      $('#shipping-address-edit-btn').click(function() {
        if ($('#shipping\\:edit').val() == '0') {
          $('#shipping\\:edit').val('1');
          $('#shipping-new-address-form').show();
        } else {
          $('#shipping\\:edit').val('0');
          $('#shipping-new-address-form').hide();
        }
      });

        $('#shipping\\:same_as_billing').change(function() {
            if ($('#shipping\\:same_as_billing').is(':checked')) {
                $("input[name='billing[use_for_shipping]'][value='1']").attr("checked", true);
            } else {
                $("input[name='billing[use_for_shipping]'][value='0']").attr("checked", true);
            }
        });

        $.validate({
            lang: langCode
        });
    };

    self.initAddressForm = function() {
        var selected_shipping_address = $('select[name="shipping_address_id"] option:selected').val();
        var selectedShippingngAddress;

        $.each(addresses, function(i, address) {
            if (address.id_address == selected_shipping_address) {
                selectedShippingngAddress = address;
            }
        });

        if (selectedShippingngAddress === undefined) return;

        if (selectedShippingngAddress.id_customer === '0') return;

        self.newAddress(!selected_shipping_address, customer.is_guest);

        // Fill invoice address
        $('#shipping\\:firstname').val(selectedShippingngAddress.firstname);
        $('#shipping\\:lastname').val(selectedShippingngAddress.lastname);
        $('#shipping\\:company').val(selectedShippingngAddress.company);
        $('#shipping\\:address1').val(selectedShippingngAddress.address1);
        $('#shipping\\:address2').val(selectedShippingngAddress.address2);
        $('#shipping\\:postcode').val(selectedShippingngAddress.postcode);
        $('#shipping\\:city').val(selectedShippingngAddress.city);
        $('#shipping\\:phone').val(selectedShippingngAddress.phone);
        $('#shipping\\:phone_mobile').val(selectedShippingngAddress.phone_mobile);
        $('#shipping\\:alias').val(selectedShippingngAddress.alias);
        $('#shipping\\:dni').val(selectedShippingngAddress.dni);
        $('#shipping\\:vat_number').val(selectedShippingngAddress.vat_number);

        $('#shipping\\:id_country').val(selectedShippingngAddress.id_country);
        $('#shipping\\:id_country').trigger('change');
        /**
         * Store id_state to country field, which always available in all address form
         * We will use this value when states load complete
         */
        $('#shipping\\:id_country').data('id_state', selectedShippingngAddress.id_state);
        $('#shipping\\:id_state').val(selectedShippingngAddress.id_state);
        $('#shipping\\:id_state').trigger('change');
    };

    self.reloadAddressBox = function() {
        var options = [];
        if (selectedShippingAddress == 0) {
            options.push('<option value="" selected="selected">' + newAddressLabel + '</option>');
        } else {
            options.push('<option value="">' + newAddressLabel + '</option>');
        }
        $.each(addresses, function(i, address) {
            var alias = address.firstname + ' ' + address.lastname +
                (address.address1 ? ', ' + address.address1 : '') +
                (address.address2 ? ', ' + address.address2 : '') +
                (address.city ? ', ' + address.city : '') +
                (address.postcode ? ', ' + address.postcode : '') +
                (address.phone ? ', ' + address.phone : '') +
                (address.phone_mobile ? ', ' + address.phone_mobile : '');
            if (selectedShippingAddress == address.id_address) {
                options.push('<option value="' + address.id_address + '" selected="selected">' + alias + '</option>');
            } else {
                options.push('<option value="' + address.id_address + '">' + alias + '</option>');
            }
        });
        $('#shipping-address-select').html(options.join(""));
        $('#shipping-address-select').trigger('change');

        options = [];
        if (selectedBillingAddress == 0) {
            options.push('<option value="" selected="selected">' + newAddressLabel + '</option>');
        } else {
            options.push('<option value="">' + newAddressLabel + '</option>');
        }
        $.each(addresses, function(i, address) {
            var alias = address.firstname + ' ' + address.lastname +
                (address.address1 ? ', ' + address.address1 : '') +
                (address.address2 ? ', ' + address.address2 : '') +
                (address.city ? ', ' + address.city : '') +
                (address.postcode ? ', ' + address.postcode : '') +
                (address.phone ? ', ' + address.phone : '') +
                (address.phone_mobile ? ', ' + address.phone_mobile : '');
            if (selectedBillingAddress == address.id_address) {
                options.push('<option value="' + address.id_address + '" selected="selected">' + alias + '</option>');
            } else {
                options.push('<option value="' + address.id_address + '">' + alias + '</option>');
            }
        });
        $('#billing-address-select').html(options.join(""));
    }

    self.save = function() {
        if (self.form.isValid()) {
            checkout.setLoadWaiting('shipping');

            var formData = $(self.form).serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + "?ajax=true" + "&saveAddress=delivery" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                    self.resetLoadWaiting(false);
                },
                success: function(json) {
                    if ($('#shipping\\:same_as_billing').is(':checked')) {
                        $("#use_for_shipping_no").prop("checked", false);
                        $("#use_for_shipping_yes").prop("checked", true);
                        self.syncToBilling();
                    } else {
                        $("#use_for_shipping_no").prop("checked", true);
                        $("#use_for_shipping_yes").prop("checked", false);
                    }
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

    self.resetSelectedAddress = function() {
        var selectElement = $('#shipping-address-select');
        if (selectElement) {
            selectElement.value = '';
        }
    };

    self.newAddress = function(isNew, isGuest) {
        if (isNew) {
            self.resetSelectedAddress();
            $('#shipping-new-address-form').show();
        } else {
            if (isGuest == 1) {
                $('#shipping-new-address-form').show();
            } else {
                if ($('#shipping\\:edit').val() == '0') {
                    $('#shipping-new-address-form').hide();
                } else {
                    $('#shipping-new-address-form').show();
                }
            }
        }
    };

    self.resetLoadWaiting = function(transport) {
        checkout.setLoadWaiting(false);
        // document.body.fire('shipping-request:completed', {transport: transport});
    };

    self.nextStep = function(json) {
        if (json.hasError) {
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

        selectedBillingAddress = json.id_address_invoice;
        selectedShippingAddress = json.id_address_delivery;
        addresses = json.addresses;
        if (addresses) {
            self.reloadAddressBox();
        }
        // billing.reloadAddressBox();

        if (json.updated_section && 'shipping_method' in json.updated_section) {
            $('#checkout-step-shipping_method').html(json.updated_section.shipping_method);
        }
        checkout.gotoSection(json.goto_section, true);
        checkout.setStepResponse(json);
    };

    self.fillForm = function(address) {

    }

    self.syncToBilling = function() {
        $("[name='firstname']").val($("#shipping\\:firstname").val());
        $("[name='lastname']").val($("#shipping\\:lastname").val());
        $("[name='company']").val($("#shipping\\:company").val());
        $("[name='address1']").val($("#shipping\\:address1").val());
        $("[name='address2']").val($("#shipping\\:address2").val());
        $("[name='city']").val($("#shipping\\:city").val());
        $("[name='postcode']").val($("#shipping\\:postcode").val());
        $("[name='phone']").val($("#shipping\\:phone").val());
        $("[name='phone_mobile']").val($("#shipping\\:phone_mobile").val());
        $("[name='alias']").val($("#shipping\\:alias").val());
        $("[name='dni']").val($("#shipping\\:dni").val());
        $("[name='vat_number']").val($("#shipping\\:vat_number").val());

        $("[name='id_country']").val($("#shipping\\:id_country").val());
        $("[name='id_state']").val($("#shipping\\:id_state").val());
    }

    self.constructor(form, url);
}

var ShippingMethod = function(form, url) {
    var self = this;
    var vars = {};

    self.constructor = function(form, url) {
        self.url = url;
        self.form = $('#' + form);
    };

    self.save = function() {
        // var validator = new Validation(this.form);
        if (self.form.isValid()) {
            checkout.setLoadWaiting('shipping-method');

            //var formData = ($('.myopccheckout_shipping_option').length) ? '&' + $('.myopccheckout_shipping_option:checked').attr('name') + '=' + $('.myopccheckout_shipping_option:checked').attr('value') + '&delivery_message=' + $('#delivery_message').val() : '';
            var formData = $('#co-shipping-method-form').serialize();
            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + '?ajax=true' + '&method=updateCarrier' + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: formData,
                beforeSend: function() {

                },
                complete: function() {
                    self.resetLoadWaiting(false);
                },
                success: function(json) {
                    self.nextStep(json);
                },
                error: function(request, textStatus, errorThrown) {
                    if (request.readyState === 4) {
                        alert(request.responseText);
                    } else if (request.readyState === 0) {
                        alert(networkErrorMsg);
                    } else {
                        alert(unknownErrorMsg);
                    }
                }
            });
        }

    };

    self.nextStep = function(json) {
        if (json.hasError) {
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

        if (json.updated_section && 'payment' in json.updated_section) {
            $('#checkout-step-payment').html(json.updated_section.payment);
        }
        checkout.gotoSection(json.goto_section, true);
        checkout.gotoSection('payment', true);
        checkout.setStepResponse(json);
    };


    self.resetLoadWaiting = function(transport) {
        checkout.setLoadWaiting(false);
    };

    self.constructor(form, url);
}

var PaymentMethod = function(form, url) {
    var self = this;
    var vars = {};

    self.constructor = function(form, url) {
        self.url = url;
        self.form = $('#' + form);
    };

    self.save = function() {
        // var validator = new Validation(this.form);
        var formData = $('#co-payment-form').serialize();

        checkout.setLoadWaiting('payment');

        $.ajax({
            type: 'POST',
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + "?ajax=true" + "&method=updatePayment" + '&rand=' + new Date().getTime(),
            async: true,
            cache: false,
            dataType: "json",
            data: formData,
            beforeSend: function() {

            },
            complete: function() {
                self.resetLoadWaiting(false);
            },
            success: function(json) {
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
        // if (json.hasError){
        //     if ((typeof json.errors) == 'string') {
        //         var plainText = $("<div/>").html(json.errors).text();
        //         alert(plainText);
        //     } else {
        //         var errors = json.errors.join("\n");
        //         var plainText = $("<div/>").html(errors).text();
        //         alert(plainText);
        //     }
        //     return false;
        // }

        if (json.updated_section && 'review' in json.updated_section) {
            $('#checkout-review-load').html(json.updated_section.review);
        }
        // checkout.gotoSection(json.goto_section, true);
        checkout.gotoSection('review', true);
    };

    self.resetLoadWaiting = function(transport) {
        checkout.setLoadWaiting(false);
    };


    self.constructor(form, url);
}

var Review = function(paymentForm, agreementsForm, url) {
    var self = this;
    var vars = {};

    self.constructor = function(paymentForm, agreementsForm, url) {
        self.url = url;
        self.paymentForm = $('#' + paymentForm);
        self.agreementsForm = $('#' + agreementsForm);

        $.validate({
            lang: langCode
        });

    };


    self.submit = function() {
        var payment_module_name = $('input:radio[name="payment-option"]:checked').attr('id');
        console.log('module name => ' + payment_module_name);

        var form_id = 'payment-form-submit-' + payment_module_name;

        $('#' + form_id).submit();

        checkout.setLoadWaiting('review');
    }

    self.nextStep = function(json) {
        if (json.hasError) {
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

        $modal = $('#paymentModal');


        //trigger confirm order after confirming payment in dialog
        $("#paymentModal #payment_dialog_proceed").click(function() {
            self.confirmOrder();
        });

        var payment_module_name = $('input:radio[name="payment_method"]:checked').attr('id');
        var payment_module_id = $('input:radio[name="payment_method"]:checked').val();
        var id = $('input:radio[name="payment_method"]:checked').val();
        var url = $('#' + $('input:radio[name="payment_method"]:checked').attr('value') + '_name').val();

        if (payment_module_name == 'bankwire' || payment_module_name == 'cheque' || payment_module_name == 'cashondelivery' || payment_module_name == 'pscodfee' || payment_module_name == 'codfee') {
            // Show loader & then get content when modal is shown

            $modal.on('show.bs.modal', function(e) {
                $(this)
                    .find('.modal-body')
                    .html('<p class="saving">' + loadingMsg + '<span>.</span><span>.</span><span>.</span></p>')
                    .load(url + ' body', function(dataHtml) { // PS-971: Prevent script execution
                        // Use Bootstrap's built-in function to fix scrolling (to no avail)

                        var payment_info_html = $(dataHtml).find('#center_column');
                        $(payment_info_html).find('#order_step').remove();
                        $('h1', payment_info_html).remove();
                        $('#cart_navigation', payment_info_html).remove();
                        $('.cart_navigation', payment_info_html).remove(); // Added for Prestashop 1.5 for removing the buttons in the payment method html
                        $('#amount', payment_info_html).removeClass('price');
                        $(payment_info_html).find('form:first').find('div:first, div.box').find('p:last-child').remove();
                        $(payment_info_html).find('form:first').find('div:first, div.box').find('#currency_payement').parent().hide();
                        if (payment_module_name == 'codfee') {
                            $(payment_info_html).find('form').find('strong').hide();
                        }
                        $modal.find('.modal-body').html(payment_info_html.html());
                        $modal.modal('handleUpdate');
                    });
            }).modal();
        } else if (payment_module_name == 'paypal') {
            if (url.indexOf('javascript:') !== -1) {
                $('#paypal_process_payment').trigger('click');
                $('#paypal_payment_form_payment').submit()
            }
        } else if (payment_module_name == 'pronesis_bancasella') {
            $('#bancasella_process_payment').trigger('click');
        } else if (payment_module_name == 'redsys') {
            $('#redsys_form').submit();
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
        $modal = $('#paymentModal form').submit();

    }


    self.save = function() {
        if (conditionEnabled) {
            if (!$('#agreement-1').is(':checked')) {
                alert(TOSMsg);
                return;
            }
        }
        if (checkout.loadWaiting != false)
            return;
        if (self.paymentForm.isValid()) {
            checkout.setLoadWaiting('review');

            var params = self.paymentForm.serialize();
            if (this.agreementsForm) {
                params += '&' + self.agreementsForm.serialize();
            }

            $.ajax({
                type: 'POST',
                headers: {
                    "cache-control": "no-cache"
                },
                url: self.url + "&ajax=true" + "&PlaceOrder=1" + '&rand=' + new Date().getTime(),
                async: true,
                cache: false,
                dataType: "json",
                data: params,
                beforeSend: function() {

                },
                complete: function() {
                    self.resetLoadWaiting();
                },
                success: function(json) {
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


    self.resetLoadWaiting = function() {
        checkout.setLoadWaiting(false);
    };

    self.constructor(paymentForm, agreementsForm, url);

}

var Coupon = function(form, url) {
    var self = this;
    var vars = {};

    self.constructor = function(form, url) {

        self.url = url;
        self.form = $('#' + form); //discount-coupon-form

        self.inputField = self.form.find('#coupon_code');
        self.removeField = self.form.find('#remove-coupone');

        if (self.form) {
            self.form.submit(function(event) {
                self.save(event.data.isRemove);
            });
        }
    };


    self.resetLoadWaiting = function() {
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
        $.ajax({
            type: "POST",
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + '?ajax=true' + '&submitDiscount=true' + '&rand=' + new Date().getTime(),
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
            success: function(json) {
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
        $.ajax({
            type: "POST",
            headers: {
                "cache-control": "no-cache"
            },
            url: self.url + '?ajax=true' + '&deleteDiscount=' + discountId + '&rand=' + new Date().getTime(),
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
            success: function(json) {
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

            var container = $('#' + step + '-buttons-container');
            container.attr('disabled');
            container.css('{opacity:.5}');
            self.getLaddaButton().start();
            self._disableEnableAll(container, true);

        } else {
            if (self.loadWaiting) {
                var container = $('#' + this.loadWaiting + '-buttons-container');
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

    self._disableEnableAll = function() {

    }


    self.nextStep = function(json) {
        if (json.hasError) {
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

        if (json.updated_section && 'review' in json.updated_section) {
            $('#checkout-review-load').html(json.updated_section.review);
        }
    };

    self.constructor(form, url);
}

var JMAgreement = function(form) {
    var self = this;
    self.constructor = function(form) {
        self.form = $('#' + form);
        self.initModals();
    };


    self.initModals = function() {
        self.form.select('div.modal').each(function(modal) {
            $(modal).find('a').click(function(e) {
                e.preventDefault();
            });
            $(modal).modal({
                show: false
            });
        });
    }
    self.constructor(form);
};
/**
 * @license Created by JMango on 1/24/18.
 */
var countriesNeedIDNumber = [];
var countriesNeedZipCode = [];
var states = [];

$(document).ready(function(){
    // setCountries();
    // bindStateInputAndUpdate();
    // if (typeof bindUniform !=='undefined')
    //     bindUniform();
    // bindZipcode();
    // bindCheckbox();
    // $(document).on('click', '#invoice_address', function(e){
    //     bindCheckbox();
    // });
});

function setCountries()
{
    if (typeof countries !== 'undefined' && countries)
    {
        var countriesPS = [];
        for (var i in countries)
        {
            var id_country = countries[i]['id_country'];
            if (typeof countries[i]['states'] !== 'undefined' && parseInt(countries[i]['contains_states']))
            {
                countriesPS[id_country] = [];
                for (var j in countries[i]['states'])
                    countriesPS[parseInt(id_country)].push({'id' : parseInt(countries[i]['states'][j]['id_state']), 'name' : countries[i]['states'][j]['name']});
            }

            if (typeof countries[i]['need_identification_number'] !== 'undefined' && parseInt(countries[i]['need_identification_number']) > 0)
                countriesNeedIDNumber.push(parseInt(countries[i]['id_country']));
            if (typeof countries[i]['need_zip_code'] !== 'undefined' && parseInt(countries[i]['need_zip_code']) > 0)
                countriesNeedZipCode[parseInt(countries[i]['id_country'])] = countries[i]['zip_code_format'];
        }
    }
    states = countriesPS;
}

function bindCheckbox()
{
    // if ($('#invoice_address:checked').length > 0)
    // {
    //     $('#opc_invoice_address').slideDown('slow');
    //     if ($('#company_invoice').val() == '')
    //         $('#vat_number_block_invoice').hide();
    //     if (typeof bindUniform !=='undefined')
    //         bindUniform();
    // }
    // else
    //     $('#opc_invoice_address').slideUp('slow');
}

function bindZipcode()
{
    $(document).on('keyup', 'input[name^=postcode]', function(e)
    {
        var char = String.fromCharCode(e.keyCode);
        if (/[a-zA-Z]/.test(char))
            $.trim($(this).val($(this).val().toUpperCase()));
    });
}

function bindStateInputAndUpdate()
{
    $('.state_id, .dni, .postcode').css({'display':'none'});

    if (typeof idSelectedCountry !== 'undefined' && idSelectedCountry)
        $('#billing\\:country_id option[value=' + idSelectedCountry + ']').prop('selected', true);

    updateState();
    updateNeedIDNumber();
    updateZipCode();

    $(document).on('change', '#billing\\:country_id', function(e)
    {
        updateState();
        updateNeedIDNumber();
        updateZipCode();
        if (typeof validate_field !== 'undefined')
            validate_field('#billing\\:postcode');
    });

    if ($('#shipping\\:country_id').length !== 0)
    {
        $(document).on('change', '#shipping\\:country_id', function(e)
        {
            updateState('shipping');
            updateNeedIDNumber('shipping');
            updateZipCode('shipping');
            if (typeof validate_field !== 'undefined')
                validate_field('#shipping\\:postcode');
        });
        updateState('shipping');
        updateNeedIDNumber('shipping');
        updateZipCode('shipping');
    }

    if (typeof idSelectedState !== 'undefined' && idSelectedState)
        $('#billing\\:state_id option[value=' + idSelectedState + ']').prop('selected', true);
    if (typeof idSelectedStateInvoice !== 'undefined' && idSelectedStateInvoice)
        $('#shipping\\:state_id option[value=' + idSelectedStateInvoice + ']').prop('selected', true);
}

function updateState(prefix)
{
    $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:state_id' + ' option:not(:first-child)').remove();
    if (typeof countries !== 'undefined')
        var state_list = states[parseInt($('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:country_id').val())];
    if (typeof state_list !== 'undefined')
    {
        $(state_list).each(function(key, item){
            $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:state_id').addClass('validate-select').append('<option value="' + parseInt(item.id) + '">' + item.name + '</option>');
        });

        $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:state' + ':hidden').show();
    }
    else
        $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:state').hide();
}

function updateNeedIDNumber(prefix)
{
    var id_country = parseInt($('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:country_id').val());
    if (in_array(id_country, countriesNeedIDNumber))
    {
        $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '-dni').show();
        // $('#billing\\:dni').uniform();
    }
    else
        $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '-dni').hide();
}

function updateZipCode(prefix)
{
    var id_country = parseInt($('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '\\:country_id').val());
    if (typeof countriesNeedZipCode[id_country] !== 'undefined')
    {
        $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '-postcode').show();
        // $('#billing\\:postcode').uniform();
    }
    else
        $('#' + (typeof prefix !== 'undefined' ? 'shipping' : 'billing') + '-postcode').hide();
}

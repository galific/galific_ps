/*
* 2007-2017 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    ST-themes <hellolee@gmail.com>
*  @copyright 2007-2017 ST-themes
*  @license   Use, by you or one client for one Prestashop instance.
*/
jQuery(function($){
     $('#product_name').autocomplete('ajax_products_list.php?excludeIds=', {
        minChars: 1,
        autoFill: true,
        max:20,
        matchContains: true,
        mustMatch:true,
        scroll:false,
        cacheLength:0,
        extraParams:{ excludeIds:getMenuProductsIds()},
        formatItem: function(item) {
            if (item.length == 2) {
              return item[1]+' - '+item[0];  
            } else {
                return '--';
            }
        }
    }).result(function(event, data, formatted) {
		if (data == null || data.length != 2)
			return false;
		var productId = data[1];
		var productName = data[0];

        $('input[name=\'product_name\']').val('');
        $('#product' + productId).remove();

        var divProductName = $('#product-list');
        divProductName.append('<div id="product'+productId+'"><i class="icon-remove text-danger"></i>'+productName+'<input type="hidden" name="product[]" value="'+productId+'"/>');

        $('#product_name').setOptions({
            extraParams: {excludeIds : getMenuProductsIds()}
        });
    }); 

    $('#product-list').delegate('.icon-remove', 'click', function(){
        $(this).parent().remove();
    });
   
});

var getMenuProductsIds = function()
{
    if (!$('#inputMenuProducts').val())
        return '-1';
    return $('#inputMenuProducts').val().replace(/\-/g,',');
}


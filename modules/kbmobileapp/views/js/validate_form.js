/**
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
* We offer the best and most useful modules PrestaShop and modifications for your online store.
*
* @category  PrestaShop Module
* @author    knowband.com <support@knowband.com>
* @copyright 2017 Knowband
* @license   see file: LICENSE.txt
*/

$(document).ready(function(){
    $('.kbmobileapp-product-youtube-submit').click(function(){
        var is_error = false;
        $('.kb_error_message').remove();
        $('input[name="product_youtube_url"]').removeClass('kb_error_field');

        /*Knowband validation start*/
        var url_msg_err = velovalidation.checkUrl($('input[name="product_youtube_url"]'));
        if (url_msg_err != true)
        {
            is_error = true;            
            $('input[name="product_youtube_url"]').addClass('kb_error_field');
            $('input[name="product_youtube_url"]').after('<span class="kb_error_message">' + url_msg_err + '</span>');
        }
        /*Knowband validation end*/
        
        if(is_error){
            return false;
        }
    });
});
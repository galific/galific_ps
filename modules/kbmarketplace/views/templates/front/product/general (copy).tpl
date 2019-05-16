<div class="kb-panel outer-border">
    <div class='kb-panel-header'>
        <h1>Information</h1>
        <div data-toggle="kb-product-form-information" class='kb-accordian-symbol kbexpand'></div>
        <div class='clearfix'></div>
    </div>
    <div id="kb-product-form-information" class='kb-panel-body'>
        <div class="kb-block kb-form">
            <ul class="kb-form-list">
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Name</span><em>*</em>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="name" value="" />
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Reference Code</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="reference" value="" />
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">EAN-13 or JAN barcode</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="ean13" value="" />
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">UPC barcode</span>
                    </div>
                    <div class="kb-form-field-block">
                        <input type="text" class="kb-inpfield" name="upc" value="" />
                    </div>
                </li>
                <li class="kb-form-l">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Enabled</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="active" class="kb-inpselect">
                            <option value="0">No</option>
                            <option value="1">Yes</option>
                        </select>
                    </div>
                </li>
                <li class="kb-form-r">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Visibility</span>
                    </div>
                    <div class="kb-form-field-block">
                        <select name="visibility" class="kb-inpselect">
                            <option value="both">Everywhere</option>
                            <option value="catalog">Catalog only</option>
                            <option value="search">Search only</option>
                            <option value="none">Nowhere</option>
                        </select>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Condition</span>
                    </div>
                    <div class="kb-form-field-block inpfwidth" style="width:47.4%">
                        <select name="condition" class="kb-inpselect">
                            <option value="new">New</option>
                            <option value="used">Used</option>
                            <option value="refurbished">Refurbished</option>
                        </select>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Options</span>
                    </div>
                    <div class="kb-form-field-block">
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="checkbox" id="label_for_available_order" name="available_for_order" value="1" checked="checked" /> <label for="label_for_available_order">Available for order</label>    
                        </div>
                        <div class="kboption-inline kb-inpoption">
                            <input class="" type="checkbox" id="label_for_show_price" name="show_price" value="1" checked="checked" /> <label  for="label_for_show_price">Show price</label>    
                        </div>
                        <div class="kboption-inline kb-inpoption" style="margin-bottom:0;">
                            <input class="" type="checkbox" id="label_for_online_only" name="online_only" value="1" /> <label  for="label_for_online_only">Online only (not sold in your retail store)</label>    
                        </div>
                    </div>
                </li>
                <li class="kb-form-fwidth">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Short Description</span>
                    </div>
                    <div class="kb-form-field-block">
                        <textarea name="description_short" rows="5" class="kb-inptexarea"></textarea>
                    </div>
                </li>
                <li class="kb-form-fwidth last-row">
                    <div class="kb-form-label-block">
                        <span class="kblabel ">Description</span>
                    </div>
                    <div class="kb-form-field-block">
                        <textarea name="description" rows="5" class="kb-inptexarea"></textarea>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
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
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}
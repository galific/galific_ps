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
	{*<link rel="stylesheet" href="http://139.59.46.134/mab/nov/scroll/jquery.mCustomScrollbar.css">
	<script src="http://139.59.46.134/mab/nov/scroll/jquery.mCustomScrollbar.concat.min.js"></script>
*}
<div id="layout_add_edit_form" class="layout_add_edit_form" style="display:none">
    {*<button  onclick="showConfigurationForm()">Cancel</button>*}
    
    <button type="button" class="btn btn-default btn-block" onclick="showConfigurationForm()" style="float:right; padding:3px 5px; width:12%; ">{l s='Cancel' mod='kbmobileapp'}</button>
<div class='row'>
            <div class="productTabs col-lg-3 col-md-3 col-sm-6">
                <div class="list-group">
                    <a id="top_category" class="list-group-item"> {l s='Top Categories' mod='kbmobileapp'}<i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="banner_square" class="list-group-item">{l s='Banner-Square' mod='kbmobileapp'}<i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="banner_HS" class="list-group-item">{l s='Banner-Horizontal Sliding' mod='kbmobileapp'} <i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="banner_grid" class="list-group-item">{l s='Banner-Grid' mod='kbmobileapp'} <i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="banner_countdown" class="list-group-item">{l s='Banner-With Countdown timer' mod='kbmobileapp'} <i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="product_square" class="list-group-item">{l s='Products-Square' mod='kbmobileapp'} <i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="product_HS" class="list-group-item">{l s='Products-Horizontal Sliding ' mod='kbmobileapp'}<i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="product_grid" class="list-group-item"> {l s='Products-Grid' mod='kbmobileapp'}<i class="icon-plus trash" style="padding-right:5px"></i></a>
                    <a id="product_LA" class="list-group-item">{l s='Products Recently access' mod='kbmobileapp'} <i class="icon-plus trash" style="padding-right:5px"></i></a>
					
                </div>
            </div>
			
			
			
            
<!--BOC
	AUTHOR: MONIKA
	Date: 01122018-->

        

		<div class="col-lg-5 col-md-4 col-sm-6">
			<div class="panel panel-default" style="min-height:400px;">
				<ul class="slides">
					
				</ul>
			</div>			
		</div>
		<div class="col-lg-4 col-md-5 col-sm-12">
			<div class="front_preview" >
				<div class="layout_gallery">
					<div class="iframe_html" >
						
					</div>
				</div>
			</div>			
		</div>
		<!--Dynamic HTML structure-->
		<div class="top_category" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Top Categories' mod='kbmobileapp'}</span>
				<!--span class="settings" onclick="settingFunction(this)"><i class="fa fa-gear"></i></span-->
				<span class="edit_component" id="top_category_edit_component" onclick="editTopCategoryComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="top_category_delete_component" onclick="trashTopcategoryComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$top_category}"/>
				</div>
			</li>
		</div>
		<div class="banner-slide" id="banner_slider_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Banner-Square' mod='kbmobileapp'}</span>
                                <span class="edit_component" id="banner_square_edit_component" onclick="editBannerSquareComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="banner_square_delete_component" onclick="trashBannerSquareComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$banner_square}"/>
				</div>
			</li>
		</div>
		<div class="Hbanner-slide" id="banner_horizontal_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Banner-Horizontal Sliding' mod='kbmobileapp'}</span>
                                <span class="edit_component" id="banner_horizontal_edit_component" onclick="editBannerSquareComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="banner_horizontal_delete_component" onclick="trashBannerHorizontalComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$banner_horizontal_sliding}"/>
				</div>
			</li>
		</div>
		<div class="banner-grid" id="banner_grid_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Banner-Grid' mod='kbmobileapp'} </span>
                                <span class="edit_component" id="banner_grid_edit_component" onclick="editBannerSquareComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="banner_grid_delete_component" onclick="trashBannerGridComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$banner_grid}"/>
				</div>
			</li>
		</div>
		<div class="banner-countdown" id="banner_countdown_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Banner-With Countdown timer' mod='kbmobileapp'}</span>
                                <span class="edit_component" id="banner_countdown_delete_component" onclick="editBannerCountdownComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="banner_countdown_delete_component" onclick="trashBannerCountdownComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$banner_countdown}"/>
				</div>
			</li>
		</div>
		<div class="product-square" id="product_square_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Products-Square' mod='kbmobileapp'} </span>
                                <span class="edit_component" id="product_square_edit_component" onclick="editProductHorizontalComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="product_square_delete_component" onclick="trashProductSquareComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$product_square}"/>
				</div>
			</li>
		</div>
		<div class="Hproduct-slide" id="product_horizontal_slide_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Products-Horizontal Sliding ' mod='kbmobileapp'}</span>
                                <span class="edit_component" id="product_horizontal_edit_component" onclick="editProductHorizontalComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="product_horizontal_delete_component" onclick="trashProductHorizontalComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$product_horizontal_sliding}"/>
				</div>
			</li>
		</div>
		<div class="product-grid" id="product_grid_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Products-Grid' mod='kbmobileapp'} </span>
                                <span class="edit_component" id="product_grid_edit_component" onclick="editProductHorizontalComponentFunction(this)"><i class='icon-pencil' style='padding-right:5px'></i></span>
				<span class="trash" id="product_grid_delete_component"  onclick="trashProductGridComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
				<div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$product_grid}"/>
				</div>
			</li>
		</div>
		<div class="product-lastAccess" id="product_last_accessed_id" style="display:none;">
			<li class="slide" id="component_position">
				<span class="slideTitle">{l s='Products Recently accessed' mod='kbmobileapp'} </span>
                                <span class="trash" id="last_access_delete_component" onclick="trashLastAccessComponentFunction(this)"><i class='icon-trash' style='padding-right:5px'></i></span>
                                <div class="banner_preview layout_div" >
					<img class="banner_preview_image" src="{$product_recent_access}"/>
				</div>
			</li>
		</div>
                <input type="hidden" id="number_of_component" value="0"/>
                <input type="hidden" id="id_layout" value="0"/>
                <input type="hidden" id="id_component_selected" value="0"/>
                <input type="hidden" id="id_layout_name_selected" value="0"/>
                <img id='kbsw_show_loader'  style="width:50px;height:50px;display:none;" src="{$loader}/show_loader.gif">
</div>
</div>
                    
                    <div id="banner_form_popup" style="display:none;">
                        
                    </div>
                    {*<div id="rm_return_form_popup" class="white_content" style="display:none;width: 50%;">
                            <a href="javascript:void(0)" id="rm_popup_close_icon" class="rm_popup_close_icon">&nbsp;</a>
                        
                        <div id="component_edit_popup" style="max-height: 500px;overflow-y: scroll;">
                        </div>*}
                        {*<div id="rm_fade" class="black_overlay" style="display: block;"></div>*}
        
        
{*</div>  *}
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="kbGDPRDialogueModel" class="modal loader fade" style="display: none;">
    <div class="modal-dialog" style='width:57%'>
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                <h4 class="modal-title">{l s='Edit Component' mod='kbmobileapp'}</h4>
                <div class="bootstrap">
		{*<div id="confirmation_block_modal" style="display:none;" class="module_confirmation conf confirm alert alert-success">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<div id="success_message">
                        {l s='Data Saved successfully' mod='kbmobileapp'}
                        </div>
		</div>*}
		</div>
            </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                </div>
        </div>
    </div>
</div>
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="layoutNameModel" class="modal fade" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                <h4 class="modal-title">{l s='Manage Layouts' mod='kbmobileapp'}</h4>
            </div>
                <div class="modal-layout-body">
                </div>
                <div class="modal-footer">
                </div>
        </div>
    </div>
</div>
      <style>
            .loader {
           display:    none;
           position:   fixed;
           z-index:    99999;
           top:        0;
           left:       0;
           height:     100% ;
           width:      100% ;
           background: 
               url('{$loader}/show_loader.gif')
               50% 50%
               no-repeat;
           background-size: 70px;
       }
       </style>
<div class="modal"></div>
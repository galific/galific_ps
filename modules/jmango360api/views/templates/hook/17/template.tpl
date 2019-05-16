{**
* @license Created by JMango
*}
<div class="">
    <h3 class="tab"> <i class="icon-star"></i>{l s='Product Setting'  mod='jmango360api'}</h3>
    <hr/>
    <div class="form-group">
        <label class="control-label col-lg-6" for="link_rewrite_1">
			<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="" >
				{l s='Hide product in JMango360 app'  mod='jmango360api'}
			</span>

        </label>
        <div class="col-lg-6">
            <div class="radio">
                <label for="show_on">
                    <input type="radio" name="status_mobile" id="show_on" value="1" {if $stt_on_mobile == 1}checked="checked"{/if}>
                    {l s='No'  mod='jmango360api'}
                </label>
            </div>
            <div class="radio">
                <label for="show_off">
                    <input type="radio" name="status_mobile" id="show_off" value="2" {if $stt_on_mobile == 2}checked="checked"{/if}>
                    {l s='Yes'  mod='jmango360api'}
                </label>
            </div>
        </div>
    </div>
    <hr/>
</div>

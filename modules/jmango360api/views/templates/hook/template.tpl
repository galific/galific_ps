{**
* @license Created by JMango
*}

<div class="panel product-tab">
    <h3 class="tab"> <i class="icon-gears"></i>{l s='JMango360 Setting' mod='jmango360api'}</h3>
    <hr/>
    <div class="form-group">
        <label class="control-label col-lg-6" for="link_rewrite_1">
			<span class="label-tooltip" data-toggle="tooltip" title="" data-original-title="" >
				{l s='Hide product in JMango360 app' mod='jmango360api'}
			</span>

        </label>
        <div class="col-lg-6">
            <div class="radio">
                <label for="show_on">
                    <input type="radio" name="status_mobile" id="show_on" value="1" checked="checked">
                    {l s='No' mod='jmango360api'}
                </label>
            </div>
            <div class="radio">
                <label for="show_off">
                    <input type="radio" name="status_mobile" id="show_off" value="2" {if $stt_on_mobile == 2}checked="checked"{/if}>
                    {l s='Yes' mod='jmango360api'}
                </label>
            </div>
        </div>
    </div>
    <hr/>
    <div class="panel-footer">
        <a href="index.php?controller=AdminProducts&amp;token=b367fb387df2b4381644d224d694f14c" class="btn btn-default"><i class="process-icon-cancel"></i> {l s='Cancel' mod='jmango360api'}</a>
        <button type="submit" name="submitAddproduct" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Save' mod='jmango360api'}</button>
        <button type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right"><i class="process-icon-save"></i>{l s='Save and stay' mod='jmango360api'}</button>
    </div>
</div>

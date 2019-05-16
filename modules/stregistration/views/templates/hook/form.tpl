<div class="st_reg_box">
{foreach from=$st_reg_form item="field"}
  {form_field field=$field file='_partials/form-fields.tpl'}
{/foreach}
{if $st_reg_custom_content}<div class="st_reg_custom_content">{$st_reg_custom_content nofilter}</div>{/if}
</div>
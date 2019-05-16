{if !isset($smarty.get.content_only)}
<!--Start of Zopim Live Chat Script-->
{literal}
<script type="text/javascript">
window.$zopim||(function(d,s){var z=$zopim=function(c){z._.push(c)},$=z.s=
d.createElement(s),e=d.getElementsByTagName(s)[0];z.set=function(o){z.set.
_.push(o)};z._=[];z.set._=[];$.async=!0;$.setAttribute('charset','utf-8');
$.src='//cdn.zopim.com/?{/literal}{$widgetid}{literal}';z.t=+new Date;$.
type='text/javascript';e.parentNode.insertBefore($,e)})(document,'script');
</script>
{/literal}
{if $customerName && $customerEmail}
{literal}
<script>
  $zopim(function() {
    $zopim.livechat.setName('{/literal}{if $customerName}{$customerName}{/if}{literal}');
    $zopim.livechat.setEmail('{/literal}{if $customerEmail}{$customerEmail}{/if}{literal}');
  });
</script>
{/literal}
{/if}
<!--End of Zopim Live Chat Script-->
{/if}
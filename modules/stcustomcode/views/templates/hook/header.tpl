{if isset($stcustomcode)}
    {if $stcustomcode.css}
    <style type="text/css">{$stcustomcode.css nofilter}</style>
    {/if}
    {if $stcustomcode.js}
    <script type="text/javascript">{$stcustomcode.js nofilter}</script>
    {/if}
    {if $stcustomcode.head_code}
    {$stcustomcode.head_code nofilter}
    {/if}
{/if}
{if $page_name == 'index'}
<!-- Webmaster Site Verifications -->
{if isset($google_pass_thou)}<meta name="google-site-verification" content="{$google_pass_thou}" />
{/if}
{if isset($googleapps_pass_thou)}<meta name="google-site-verification" content="{$googleapps_pass_thou}" />
{/if}
{if isset($bing_pass_thou)}<meta name="msvalidate.01" content="{$bing_pass_thou}" />
{/if}
{if isset($alexa_pass_thou)}<meta name="alexaVerifyID" content="{$alexa_pass_thou}" />
{/if}
{if isset($norton_pass_thou)}<meta name="norton-safeweb-site-verification" content="{$norton_pass_thou}" />
{/if}
{if isset($wot_pass_thou)}<meta name="wot-verification" content="{$wot_pass_thou}" />
{/if}
{if isset($pinterest_pass_thou)}<meta name="p:domain_verify" content="{$pinterest_pass_thou}" />
{/if}
{if isset($yandex_pass_thou)}<meta name="yandex-verification" content="{$yandex_pass_thou}" />
{/if}{/if}
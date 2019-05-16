{**
* @license Created by JMango
*}

<script src="/modules/jmango360api/views/js/smartAppBanner/smart-app-banner.js"></script>
<script type="text/javascript">
    {if $smartAppBannerSetting!=null}
        new SmartBanner({
            daysHidden: {$smartAppBannerSetting->days_hidden},
            daysReminder: {$smartAppBannerSetting->days_reminder},
            appStoreLanguage: '{$smartAppBannerSetting->app_store_language}',
            title: '{$smartAppBannerSetting->title}',
            author: '{$smartAppBannerSetting->author}',
            button: '{$smartAppBannerSetting->button}',
            store: {
                ios: '{$smartAppBannerSetting->store_ios}',
                android: '{$smartAppBannerSetting->store_android}'
            },
            price: {
                ios: '{$smartAppBannerSetting->price_ios}',
                android: '{$smartAppBannerSetting->price_android}'
            }
        });
    {/if}
</script>

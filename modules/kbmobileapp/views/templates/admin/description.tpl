<div class="alert alert-info">
    <h4>{l s='Cron Instructions' mod='kbmobileapp'}</h4>
                    {l s='Add the cron to your store via control panel/putty to create complete database backup and download it to server and google drive automatically according to your serial reminder settings. Find below the Instruction to add the cron.' mod='kbmobileapp'}
                    <br><br><b>{l s='URLs to Add to Cron via Control Panel' mod='kbmobileapp'}</b><br>{$cron_url|escape:'quotes':'UTF-8'}
                    <br><br><b>{l s='Cron setup via SSH' mod='kbmobileapp'}</b><br>0/30 * * * * wget -O /dev/null&nbsp{$cron_url|escape:'quotes':'UTF-8'}
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
* @copyright 2016 Knowband
* @license   see file: LICENSE.txt
*
*}
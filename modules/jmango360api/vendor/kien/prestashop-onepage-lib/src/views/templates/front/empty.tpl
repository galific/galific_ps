{*
 * @author Duc Ngo <duc@jmango360.com>
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*}
<!doctype html>
<html lang="{$language.iso_code|escape:'html':'UTF-8'}">
<head>
    {block name='head'}
        {include file='_partials/head.tpl'}
    {/block}
    {literal}
        <style type="text/css">
            body {
                padding: 15px;
            }
        </style>
    {/literal}
</head>
<body>
<center>{l s='There are no more items in your cart' d=$module_name}</center>
</body>
</html>

<section id="main">
    <section id="products">
        <div class="products row">
            {foreach from=$sellers item=seller name=sellers}
                <article class="product-miniature js-product-miniature">
                    <div class="thumbnail-container">
                        <a href="{$seller.href nofilter}" title="{$seller.title}" class="thumbnail product-thumbnail"> {* Variable contains HTML/CSS/JSON, escape not required *}

                            <img
                              src = "{$seller.logo nofilter}"  {* Variable contains HTML/CSS/JSON, escape not required *}

                              alt = "{$seller.title}"
                            >
                        </a>
                        <div class="product-description">
                            <h1 class="h3 product-title"><a href="{$seller.href nofilter}" target='_blank' title="{$seller.title}">{$seller.title|truncate:30:'...'}</a></h1>  {* Variable contains HTML/CSS/JSON, escape not required *}

                           
                        </div>
                    </div>
                </article>
            {/foreach}           
        </div>
    </section>
</section>
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
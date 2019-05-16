<div class="kb-block kb-panel">
    <div class="kb-content">
        <div class="kb-content-header">
            <h1>{l s='Dashboard' mod='kbmarketplace'}</h1>
            <div class="clearfix"></div>
        </div>
        <div class='outer-border'>
            <ul class='summary-list-group'>
                <li class='summary-box blue-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe263;</i>
                                <span class="big-title">{l s='Total Sale' mod='kbmarketplace'}</span>
                                <span class="big-value">{Tools::displayPrice($total_revenue)}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li class='summary-box purple-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe048;</i>
                                <span class="big-title">{l s='Total Earning' mod='kbmarketplace'}</span>
                                <span class="big-value">{Tools::displayPrice($total_earning)}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li class='summary-box green-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe8d0;</i>
                                <span class="big-title">{l s='Total Orders' mod='kbmarketplace'}</span>
                                <span class="big-value">{$total_orders|intval}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li class='summary-box yellow-summary'>
                    <div class="summary-single-box">
                        <div class="mo_kpi_content big">
                                <i class="big kb-material-icons">&#xe54c;</i>
                                <span class="big-title">{l s='Total Products Sold' mod='kbmarketplace'}</span>
                                <span class="big-value">{$total_sold_products|intval}</span>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </li>
            </ul>
        </div>
        <div class="kb-vspacer5"></div>
        <div class="kb-panel outer-border">
            <script type="text/javascript">
                var kb_graph_revenue_label = "{l s='Your Revenue' mod='kbmarketplace'}";
                var kb_graph_orders_label = "{l s='Total Orders' mod='kbmarketplace'}";
                var kb_graph_products_label = "{l s='Total Products Sold' mod='kbmarketplace'}";
            </script>
            <div class='kb-panel-header kb-sale-stat-tab'>
                <h1>{l s='Sales Statistics' mod='kbmarketplace'}</h1>
                <span class="link" style="display:none;">
                    <select name="sales_statistics_type" onchange="displaySalesGraph(this)">
                        <option value="last_7_days">{l s='Last 7 Days' mod='kbmarketplace'}</option>
                        <option value="week">{l s='This Week' mod='kbmarketplace'}</option>
                        <option value="month">{l s='This Month' mod='kbmarketplace'}</option>
                        <option value="year">{l s='This Year' mod='kbmarketplace'}</option>
                    </select>
                </span>
                <div class='clearfix'></div>
            </div>
            <div id="kb_seller_sales_graph_container" class='kb-panel-body kb-content'>
                <div class="loader128"></div>
                <div class="kb_graph_area" style="width:100%;">
                    <div id="kb_graph_legend_holder" class="kb_graph_legend_container"></div>
                    <div id="kb_seller_sales_graph" style="width:100%; height:300px" class="kb_graph_container"></div>
                </div>
            </div>
        </div>
        <div class="kb-vspacer5"></div>

        <div class="kb-vspacer5"></div>
        <div class="kb-panel outer-border">

        {hook h="displayKbMarketPlaceSellerDashboard"}
    </div>
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
* @copyright 2016 knowband
* @license   see file: LICENSE.txt
*}
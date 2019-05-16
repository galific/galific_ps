<div class="panel">
    <fieldset>
        <legend>{l s='Access Rules' mod='kbmobileapp'}:</legend>
        <div id="info-panel">
            <h2>{l s='1. Website Pages Descriptions' mod='kbmobileapp'}</h2>
            <div class="alert alert-info">
                <h4>{l s='These are the pages which will be accessible to customers. To access website page, please use the following url' mod='kbmobileapp'}:</h4>
                <ul class="list-unstyled">
                    <li>http://www.example.com/module/kbmobileapp/ApiCall?url=your_pagename</li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="pages_description">
                <div class="page_list table-responsive-row clearfix">
                    <table class="table tableDnD">
                        <thead>
                            <tr>
                                <th><span class="title_box">{l s='Page Name' mod='kbmobileapp'}</span></th>
                                <th><span class="title_box">url</span></th>
                                <th><span class="title_box">{l s='Descriptions' mod='kbmobileapp'}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{l s='Home Page' mod='kbmobileapp'}</td>
                                <td>homepage</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{l s='Category Page' mod='kbmobileapp'}</td>
                                <td>categorypage&id_category=requested_category_id</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{l s='Product Page' mod='kbmobileapp'}</td>
                                <td>productpage&id_product=requested_product_id</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>{l s='Cart Page' mod='kbmobileapp'}</td>
                                <td>cartpage&id_product=requested_cart_id</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <h2>{l s='2. Seller Account Pages Descriptions' mod='kbmobileapp'}</h2>
            <div class="alert alert-info">
                <h4>{l s='These are the pages which will only be accessible to sellers. To access seller account pages, please use the following url' mod='kbmobileapp'}:</h4>
                <ul class="list-unstyled">
                    <li>http://www.example.com/module/kbmobileapp/ApiCall?url=marketplace_sellerpagename&id_customer=your_customer_id</li>
                    <li><b>{l s='Note: ' mod='kbmobileapp'}</b>{l s='To access seller pages, you must have to pass customer id in request.' mod='kbmobileapp'}</li>
                </ul>
            </div>
            <div class="clearfix"></div>
            <div class="pages_description">
                <div class="page_list table-responsive-row clearfix">
                    <table class="table tableDnD">
                        <thead>
                            <tr>
                                <th><span class="title_box">{l s='Seller Page Name' mod='kbmobileapp'}</span></th>
                                <th><span class="title_box">url</span></th>
                                <th><span class="title_box">{l s='Descriptions' mod='kbmobileapp'}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{l s='Dashboard' mod='kbmobileapp'}</td>
                                <td>sellerdashboard</td>
                                <td>{l s='This API will return overall sale summary, sales comparision data with previous terms, Last 10 orders' mod='kbmobileapp'}</td>
                            </tr>
                            <tr>
                                <td>{l s='Products' mod='kbmobileapp'}</td>
                                <td>sellerproduct</td>
                                <td>{l s='Return seller\'s products list' mod='kbmobileapp'}</td>
                            </tr>
                            <tr>
                                <td>{l s='Orders' mod='kbmobileapp'}</td>
                                <td>sellerorders</td>
                                <td>{l s='Return seller\'s orders' mod='kbmobileapp'}</td>
                            </tr>
                            <tr>
                                <td>{l s='Product Reviews' mod='kbmobileapp'}</td>
                                <td>sellerpreviews</td>
                                <td>{l s='Return reviews given by customers on requested seller.' mod='kbmobileapp'}</td>
                            </tr>
                            <tr>
                                <td>{l s='Seller Reviews' mod='kbmobileapp'}</td>
                                <td>sellerreviews</td>
                                <td>{l s='Return seller\'s review' mod='kbmobileapp'}</td>
                            </tr>
                            <tr>
                                <td>{l s='Seller Earnings' mod='kbmobileapp'}</td>
                                <td>sellerearning</td>
                                <td>{l s='Return earning summary, earning report (last 7 days) and orderwise earning.' mod='kbmobileapp'}</td>
                            </tr>
                            <tr>
                                <td>{l s='Seller Transactions' mod='kbmobileapp'}</td>
                                <td>sellertransactions</td>
                                <td>{l s='Return transaction details made by admin to this requested seller.' mod='kbmobileapp'}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="clearfix"></div>
            <br><br>
            <div class="alert alert-warning">
                <h4>{l s='You can also access the default webservice of your store by using following url:' mod='kbmobileapp'}:</h4>
                <ul class="list-unstyled">
                    <li>http://www.example.com/module/kbmobileapp/ApiCall?url=webservicename</li>
                </ul>
            </div>
        </div>
    </fieldset>
    <div class="clear"><br/></div>
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
* Description
*
* Tpl file
*}
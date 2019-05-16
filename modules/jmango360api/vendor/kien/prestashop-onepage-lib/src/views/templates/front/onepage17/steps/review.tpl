{**
* @license Created by JMango
*}

<div class="content" style="padding: 0.8rem 0;">

    <!-- Shopping cart review area -->
    <div class="row">
        {include file="$cart_template_path"
        products=$cart.products
        products_count=$cart.products_count
        subtotals=$cart.subtotals
        totals=$cart.totals
        labels=$cart.labels
        add_product_link=false
        }
    </div>

    <!-- End area -->

    <!--Term and condition line -->
    <div id="term-and-condition">
        {if $conditions_to_approve|count}
            <p class="ps-hidden-by-js term-and-condition-title">
                {* At the moment, we're not showing the checkboxes when JS is disabled
                   because it makes ensuring they were checked very tricky and overcomplicates
                   the template. Might change later.
                *}
                {l s='By confirming the order, you certify that you have read and agree with all of the conditions below:' d='Shop.Theme.Checkout'}
            </p>
            <form id="conditions-to-approve" method="GET">
                <ul>
                    {foreach from=$conditions_to_approve item="condition" key="condition_name"}
                        <li>
                            <div class="float-xs-left">
              <span class="custom-checkbox">
                <input id="conditions_to_approve[{$condition_name}]"
                       name="conditions_to_approve[{$condition_name}]"
                       required
                       type="checkbox"
                       value="1"
                       class="ps-shown-by-js cb-term-and-condition"
                >
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
              </span>
                            </div>
                            <div class="condition-label">
                                <label class="js-terms" id="id_label_{$condition_name}"
                                       for="conditions_to_approve[{$condition_name}]">
                                    {$condition}
                                </label>
                            </div>
                        </li>
                    {/foreach}
                </ul>
            </form>
        {/if}

    </div>
    <!-- End area -->

    <!-- Promo code -->
    <form id="co-coupon-form" action="" method="post"
          style="{if $enable_coupon_onepage === '0'}display: none;{/if}">
        <dl class="sp-methods">
            <dt>{l s='Vouchers' mod='jmango360api'}
                <a href="#coupon-content" data-toggle="collapse" role="button" aria-expanded="false"
                   aria-controls="coupon-content" class="section-control collapsed">
                    <i class="icomoon-pencil"></i>
                </a>
            </dt>
            <dd>
                <ul class="form-list" id="coupon-content" aria-expanded="false"
                    style="height: auto;">
                    <li>
                        {if sizeof($discounts)}
                            <table>
                                <tbody>
                                {foreach $discounts as $discount}
                                    {if (float)$discount.value_real == 0}
                                        {continue}
                                    {/if}
                                    <tr>
                                        <td>{$discount.name}</td>
                                        <td>
                                            {if strlen($discount.code)}
                                                <i class="fa fa-trash"
                                                   onclick="coupon.remove('{$discount.id_discount}')"></i>
                                            {/if}
                                        </td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        {/if}
                    </li>
                    <li>
                        <label for="coupon_code">{l s='Enter your voucher code if you have one.' mod='jmango360api'}</label>
                        <input type="hidden" name="remove" id="remove-coupone" value="0">
                        <div class="input-box">
                            <input class="input-text" id="coupon_code" name="coupon_code"
                                   type="text" value="">
                        </div>
                        <div class="buttons-set">
                            <button type="button" class="ladda-button" data-color="jmango"
                                    data-size="xs" data-style="slide-up"
                                    onclick="coupon.save(false)" style="width:auto;"
                                    id="coupon-button" title="Apply Coupon" value="Apply Coupon">
                                <span class="ladda-label">{l s='Apply' mod='jmango360api'}</span>
                            </button>
                        </div>
                    </li>
                </ul>
            </dd>
        </dl>
    </form>
    <!-- End area -->

    <!--submit button-->
    <div class="buttons-set" id="review-buttons-container">
        <button id="review-button" type="button" title="Place Order" class="ladda-button"
                data-color="jmango" data-style="slide-up" data-size="s">
            <span class="ladda-label">{l s='Place Order' mod='jmango360api'}</span>
            <span class="ladda-spinner"></span></button>
        <div class="ladda-progress" style="width: 0px;"></div>
    </div>

</div>


<!-- Term and condition popup area-->
<div class="modal fade term-and-condition-popup" id="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"
                    aria-label="{l s='Close' mod='jmango360api'}">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="js-modal-content"></div>
        </div>
    </div>
</div>
<!-- End area -->

<script type="text/javascript">
    var review = new Review('co-payment-form', 'checkout-agreements', "{url entity='module' name=$module_name controller='jmcheckout' relative_protocol=false}");
    var coupon = new Coupon('co-coupon-form', '{url entity='module' name=$module_name controller='jmcheckout' relative_protocol=false}');

    $('.js-terms a').on('click', (event) => {
        event.preventDefault();
        var url = $(event.target).attr('href');
        if (url) {
            // TODO: Handle request if no pretty URL
            url += `?content_only=1`;
            $.get(url, (content) => {
                $('#modal').find('.js-modal-content').html($(content).find('.page-cms').contents());
            }).fail((resp) => {
                console.log('error loading term and condition');
            });
        }

        $('#modal').modal('show');
    });

    $('#review-button').click(function (e) {
        var checked = $('.cb-term-and-condition:checkbox:checked').length > 0;
        if (!checked) {
            e.preventDefault();
            var errMessage = TOSMsg || "{l s='You must agree to the terms of service before continuing.' mod='jmango360api' js=1}";
            alert(errMessage);
        } else {
            review.submit();
        }
    });

    $(".over-view-img").each(function (index, element) {
        var value = $(".over-view-img").eq(index).attr("src");
        if (value) {
            //set nothing
        } else {
            //set max height to 100px
            $(".over-view-img").eq(index).css('max-height', '100px');
        }
    });

</script>


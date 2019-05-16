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
            add_product_link=true
        }
    </div>

    <!-- End area -->

    <!--Term and condition line -->
    <div id="term-and-condition" style="margin-top: 40px">
        {if $conditions_to_approve|count}
            <p class="ps-hidden-by-js">
                {* At the moment, we're not showing the checkboxes when JS is disabled
                   because it makes ensuring they were checked very tricky and overcomplicates
                   the template. Might change later.
                *}
                {l s='By confirming the order, you certify that you have read and agree with all of the conditions below:' d=$module_name}
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
                       class="ps-shown-by-js"
                >
                <span><i class="material-icons checkbox-checked">&#xE5CA;</i></span>
              </span>
                            </div>
                            <div class="condition-label">
                                <label class="js-terms"
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

    <!--submit button-->
    <div class="buttons-set" id="payment-buttons-container">
        <button id="payment-button" type="button" title="Place Order" class="ladda-button"
                onclick="review.submit();" data-color="jmango" data-style="slide-up" data-size="s">
            <span class="ladda-label">{l s='Place Order' d=$module_name}</span>
            <span class="ladda-spinner"></span></button>
        <div class="ladda-progress" style="width: 0px;"></div>
    </div>

</div>


<!-- Term and condition popup area-->
<div class="modal fade" id="modal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal"
                    aria-label="{l s='Close' d=$module_name}">
                <span aria-hidden="true">&times;</span>
            </button>
            <div class="js-modal-content"></div>
        </div>
    </div>
</div>
<!-- End area -->

<script type="text/javascript">
    var review = new Review('co-payment-form', 'checkout-agreements', "{url entity='module' name=$module_name controller='jmcheckout' relative_protocol=false}");

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

</script>


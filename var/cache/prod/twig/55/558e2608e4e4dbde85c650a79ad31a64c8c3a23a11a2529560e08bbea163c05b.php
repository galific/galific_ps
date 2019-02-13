<?php

/* @Product/ProductPage/Panels/pricing.html.twig */
class __TwigTemplate_86990f6bd0fd50bd440692de118472441fa5b5f672a86479ef4f0479c1c5f39a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "<div role=\"tabpanel\" class=\"form-contenttab tab-pane\" id=\"step2\">
  <div class=\"row\">
    <div class=\"col-md-12\">
      <div class=\"container-fluid\">
        <div class=\"row\">

          <div class=\"col-md-12\">
            <h2>";
        // line 32
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Retail price", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
              <span class=\"help-box\" data-toggle=\"popover\"
                data-content=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This is the price at which you intend to sell this product to your customers. The tax included price will change according to the tax rule you select.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
            </h2>
          </div>

          <div class=\"col-md-12 form-group\">
            <div class=\"row\">

              <div class=\"col-xl-2 col-lg-3\">
                <label class=\"form-control-label\">";
        // line 42
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["pricingForm"] ?? null), "price", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                ";
        // line 43
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "price", array()), 'errors');
        echo "
                ";
        // line 44
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "price", array()), 'widget');
        echo "
              </div>
              <div class=\"col-xl-2 col-lg-3\">
                <label class=\"form-control-label\">";
        // line 47
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["pricingForm"] ?? null), "price_ttc", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                ";
        // line 48
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "price_ttc", array()), 'errors');
        echo "
                ";
        // line 49
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "price_ttc", array()), 'widget');
        echo "
              </div>

              <div class=\"col-xl-4 col-lg-6 mx-auto\">
                <label class=\"form-control-label\">
                  ";
        // line 54
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["pricingForm"] ?? null), "unit_price", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 56
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Some products can be purchased by unit (per bottle, per pound, etc.),  and this is the price for one unit. For instance, if you’re selling fabrics, it would be the price per meter.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </label>
                <div class=\"row\">
                  <div class=\"col-md-6\">
                    ";
        // line 60
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "unit_price", array()), 'errors');
        echo "
                    ";
        // line 61
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "unit_price", array()), 'widget');
        echo "
                  </div>
                  <div class=\"col-md-6\">
                    ";
        // line 64
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "unity", array()), 'errors');
        echo "
                    ";
        // line 65
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "unity", array()), 'widget');
        echo "
                  </div>
                </div>
              </div>
              <div class=\"col-md-2 col-md-offset-1 ";
        // line 69
        if (($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_USE_ECOTAX") != 1)) {
            echo "hide";
        }
        echo "\">
                <label class=\"form-control-label\">";
        // line 70
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["pricingForm"] ?? null), "ecotax", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                ";
        // line 71
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "ecotax", array()), 'errors');
        echo "
                ";
        // line 72
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "ecotax", array()), 'widget');
        echo "
              </div>
            </div>
          </div>

          <div class=\"col-md-12\">
            <div class=\"row form-group\">
              <div class=\"col-md-4\">
                <label class=\"form-control-label\">";
        // line 80
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["pricingForm"] ?? null), "id_tax_rules_group", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                ";
        // line 81
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "id_tax_rules_group", array()), 'errors');
        echo "
                ";
        // line 82
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "id_tax_rules_group", array()), 'widget');
        echo "
              </div>
              <div class=\"col-md-8\">
                <label class=\"form-control-label\">&nbsp;</label>
                <a class=\"form-control-static external-link\" href=\"";
        // line 86
        echo twig_escape_filter($this->env, $this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getAdminLink("AdminTaxes"), "html", null, true);
        echo "\">
                  ";
        // line 87
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Manage tax rules", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                </a>
              </div>
              <div class=\"col-md-12 pt-1\">
                ";
        // line 91
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "on_sale", array()), 'widget');
        echo "
              </div>
              <div class=\"col-md-12\">
                <div class=\"row\">
                  <div class=\"col-xl-5 col-lg-12\">
                    <div class=\"alert alert-info mt-2\" role=\"alert\">
                      <p class=\"alert-text\">
                        ";
        // line 98
        echo twig_replace_filter($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Final retail price: [1][2][/2] tax incl.[/1] / [3][/3] tax excl.", array(), "Admin.Catalog.Feature"), array("[1]" => "<strong>", "[/1]" => "</strong>", "[2]" => "<span id=\"final_retail_price_ti\">", "[/2]" => "</span>", "[3]" => "<span id=\"final_retail_price_te\">", "[/3]" => "</span>"));
        echo "
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class=\"col-md-12\">
            <div class=\"row mb-3\">
              <div class=\"col-md-12\">
                <h2>
                  ";
        // line 111
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Cost price", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 113
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The cost price is the price you paid for the product. Do not include the tax. It should be lower than the retail price: the difference between the two will be your margin.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </h2>
              </div>
              <div class=\"col-xl-2 col-lg-3 form-group\">
                <label class=\"form-control-label\">";
        // line 117
        echo $this->getAttribute($this->getAttribute($this->getAttribute(($context["pricingForm"] ?? null), "wholesale_price", array()), "vars", array()), "label", array());
        echo "</label>
                ";
        // line 118
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "wholesale_price", array()), 'errors');
        echo "
                ";
        // line 119
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "wholesale_price", array()), 'widget');
        echo "
              </div>
            </div>
          </div>

          <div class=\"col-md-12\">
            <div class=\"row mb-3\">
              <div class=\"col-md-12\">
                <h2>
                  ";
        // line 128
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Specific prices", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 130
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can set specific prices for customers belonging to different groups, different countries, etc.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </h2>
              </div>
              <div class=\"col-md-12\">
                <div id=\"specific-price\" class=\"mb-2\">
                  <a id=\"js-open-create-specific-price-form\" class=\"btn btn-outline-primary mb-3\" data-toggle=\"collapse\" href=\"#specific_price_form\" aria-expanded=\"false\">
                    <i class=\"material-icons\">add_circle</i>
                    ";
        // line 137
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Add a specific price", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                  </a>
                  <div class=\"collapse\" id=\"specific_price_form\" data-action=\"";
        // line 139
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_specific_price_add");
        echo "\">
                      ";
        // line 140
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_specific_price.html.twig", array("form" => $this->getAttribute(($context["pricingForm"] ?? null), "specific_price", array()), "is_multishop_context" => ($context["is_multishop_context"] ?? null)));
        echo "
                  </div>
                  <table
                      id=\"js-specific-price-list\"
                      class=\"table hide seo-table\"
                      data-listing-url=\"";
        // line 145
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_specific_price_list", array("idProduct" => 1));
        echo "\"
                      data-action-delete=\"";
        // line 146
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_delete_specific_price", array("idSpecificPrice" => 1));
        echo "\"
                      data-action-edit=\"";
        // line 147
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_get_specific_price_update_form", array("idSpecificPrice" => 1));
        echo "\">
                    <thead class=\"thead-default\">
                    <tr>
                      <th>";
        // line 150
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Rule", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</th>
                      <th>";
        // line 151
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Combination", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</th>
                      <th>";
        // line 152
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Currency", array(), "Admin.Global"), "html", null, true);
        echo "</th>
                      <th>";
        // line 153
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Country", array(), "Admin.Global"), "html", null, true);
        echo "</th>
                      <th>";
        // line 154
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Group", array(), "Admin.Global"), "html", null, true);
        echo "</th>
                      <th>";
        // line 155
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Customer", array(), "Admin.Global"), "html", null, true);
        echo "</th>
                      <th>";
        // line 156
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Fixed price", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</th>
                      <th>";
        // line 157
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Impact", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</th>
                      <th>";
        // line 158
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Period", array(), "Admin.Global"), "html", null, true);
        echo "</th>
                      <th>";
        // line 159
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("From", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</th>
                      <th></th>
                      <th></th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>

          <div>
            ";
        // line 172
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_edit_specific_price_modal.html.twig");
        echo "
          </div>


          <div class=\"col-md-12\">
            <div class=\"row\">
              <div class=\"col-md-12\">
                <h2>
                  ";
        // line 180
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Priority management", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 182
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Sometimes one customer can fit into multiple price rules. Priorities allow you to define which rules apply first.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </h2>
              </div>
              <div class=\"col-md-3\">
                <fieldset class=\"form-group\">
                  <label>";
        // line 187
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Priorities", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</label>
                  ";
        // line 188
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_0", array()), 'errors');
        echo "
                  ";
        // line 189
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_0", array()), 'widget');
        echo "
                </fieldset>
              </div>
              <div class=\"col-md-3\">
                <fieldset class=\"form-group\">
                  <label>&nbsp;</label>
                  ";
        // line 195
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_1", array()), 'errors');
        echo "
                  ";
        // line 196
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_1", array()), 'widget');
        echo "
                </fieldset>
              </div>
              <div class=\"col-md-3\">
                <fieldset class=\"form-group\">
                  <label>&nbsp;</label>
                  ";
        // line 202
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_2", array()), 'errors');
        echo "
                  ";
        // line 203
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_2", array()), 'widget');
        echo "
                </fieldset>
              </div>
              <div class=\"col-md-3\">
                <fieldset class=\"form-group\">
                  <label>&nbsp;</label>
                  ";
        // line 209
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_3", array()), 'errors');
        echo "
                  ";
        // line 210
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriority_3", array()), 'widget');
        echo "
                </fieldset>
              </div>
              <div class=\"col-md-12\">
                ";
        // line 214
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["pricingForm"] ?? null), "specificPricePriorityToAll", array()), 'widget');
        echo "
              </div>
            </div>
          </div>

          ";
        // line 219
        echo $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHook("displayAdminProductsPriceStepBottom", array("id_product" => ($context["productId"] ?? null)));
        echo "

        </div>
      </div>
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Panels/pricing.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  400 => 219,  392 => 214,  385 => 210,  381 => 209,  372 => 203,  368 => 202,  359 => 196,  355 => 195,  346 => 189,  342 => 188,  338 => 187,  330 => 182,  325 => 180,  314 => 172,  298 => 159,  294 => 158,  290 => 157,  286 => 156,  282 => 155,  278 => 154,  274 => 153,  270 => 152,  266 => 151,  262 => 150,  256 => 147,  252 => 146,  248 => 145,  240 => 140,  236 => 139,  231 => 137,  221 => 130,  216 => 128,  204 => 119,  200 => 118,  196 => 117,  189 => 113,  184 => 111,  168 => 98,  158 => 91,  151 => 87,  147 => 86,  140 => 82,  136 => 81,  132 => 80,  121 => 72,  117 => 71,  113 => 70,  107 => 69,  100 => 65,  96 => 64,  90 => 61,  86 => 60,  79 => 56,  74 => 54,  66 => 49,  62 => 48,  58 => 47,  52 => 44,  48 => 43,  44 => 42,  33 => 34,  28 => 32,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Panels/pricing.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Panels/pricing.html.twig");
    }
}

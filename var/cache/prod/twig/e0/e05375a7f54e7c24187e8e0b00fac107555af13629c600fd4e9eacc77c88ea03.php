<?php

/* @Product/ProductPage/Panels/combinations.html.twig */
class __TwigTemplate_205c98f7bea0835beca52a02006def9e6bdf72d5796afce2258714681f3d48e2 extends Twig_Template
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
        echo "<div role=\"tabpanel\" class=\"form-contenttab tab-pane\" id=\"step3\">
  <div class=\"row\">
    <div class=\"col-md-12\">
      <div class=\"container-fluid\">
        <div class=\"row\">

          <div class=\"col-md-12\">

            <div id=\"quantities\" style=\"";
        // line 33
        if ((($context["has_combinations"] ?? null) || ($this->getAttribute($this->getAttribute(($context["formDependsOnStocks"] ?? null), "vars", array()), "value", array()) != "0"))) {
            echo "display: none;";
        }
        echo "\">
              <h2>";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Quantities", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</h2>
              <fieldset class=\"form-group\">
                <div class=\"row\">
                  ";
        // line 37
        if ($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_STOCK_MANAGEMENT")) {
            // line 38
            echo "                    <div class=\"col-md-4\">
                      <label class=\"form-control-label\">";
            // line 39
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["formStockQuantity"] ?? null), "vars", array()), "label", array()), "html", null, true);
            echo "</label>
                      ";
            // line 40
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formStockQuantity"] ?? null), 'errors');
            echo "
                      ";
            // line 41
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formStockQuantity"] ?? null), 'widget');
            echo "
                    </div>
                  ";
        }
        // line 44
        echo "                  <div class=\"col-md-4\">
                    <label class=\"form-control-label\">";
        // line 45
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["formStockMinimalQuantity"] ?? null), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    <span class=\"help-box\" data-toggle=\"popover\"
                          data-content=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The minimum quantity required to buy this product (set to 1 to disable this feature). E.g.: if set to 3, customers will be able to purchase the product only if they take at least 3 in quantity.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                    ";
        // line 48
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formStockMinimalQuantity"] ?? null), 'errors');
        echo "
                    ";
        // line 49
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formStockMinimalQuantity"] ?? null), 'widget');
        echo "
                  </div>
                </div>
              </fieldset>

              <h2>";
        // line 54
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Stock", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</h2>
              <fieldset class=\"form-group\">
                <div class=\"row\">
                  <div class=\"col-md-4\">
                    <label class=\"form-control-label\">";
        // line 58
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["formLocation"] ?? null), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    ";
        // line 59
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formLocation"] ?? null), 'errors');
        echo "
                    ";
        // line 60
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formLocation"] ?? null), 'widget');
        echo "
                  </div>
                </div>
                <div class=\"row\">
                  <div class=\"col-md-4\">
                    <label class=\"form-control-label\">";
        // line 65
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["formLowStockThreshold"] ?? null), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    ";
        // line 66
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formLowStockThreshold"] ?? null), 'errors');
        echo "
                    ";
        // line 67
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formLowStockThreshold"] ?? null), 'widget');
        echo "
                  </div>
                  <div class=\"col-md-8\">
                    <label class=\"form-control-label\">&nbsp;</label>
                    <div class=\"widget-checkbox-inline\">
                      ";
        // line 72
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formLowStockAlert"] ?? null), 'errors');
        echo "
                      ";
        // line 73
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formLowStockAlert"] ?? null), 'widget');
        echo "
                      <span class=\"help-box\" data-toggle=\"popover\" data-html=\"true\" data-content=\"";
        // line 74
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The email will be sent to all the users who have the right to run the stock page. To modify the permissions, go to [1]Advanced Parameters > Team[/1]", array("[1]" => (("<a href=&quot;" . $this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getAdminLink("AdminEmployees")) . "&quot;>"), "[/1]" => "</a>"), "Admin.Catalog.Help");
        echo "\" ></span>
                    </div>
                  </div>
                </div>
              </fieldset>
            </div>

            <div id=\"virtual_product\" data-action=\"";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_virtual_save_action", array("idProduct" => ($context["productId"] ?? null))), "html", null, true);
        echo "\" data-action-remove=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_virtual_remove_action", array("idProduct" => ($context["productId"] ?? null))), "html", null, true);
        echo "\">

              <div class=\"row\">
                <div class=\"col-md-4\">
                  <h2>";
        // line 85
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "vars", array()), "label", array()), "html", null, true);
        echo "</h2>
                </div>
                <div class=\"col-md-8\">
                  <fieldset class=\"form-group\">
                    ";
        // line 89
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "is_virtual_file", array()), 'widget');
        echo "
                  </fieldset>
                </div>
              </div>

              <div id=\"virtual_product_content\" class=\"row col-md-8\">
                ";
        // line 95
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formVirtualProduct"] ?? null), 'errors');
        echo "
                <div class=\"col-md-12\">
                  <fieldset class=\"form-group\">
                    <label class=\"form-control-label\">";
        // line 98
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "file", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    <span class=\"help-box\" data-toggle=\"popover\"
                          data-content=\"";
        // line 100
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Upload a file from your computer (%maxUploadSize% max.)", array("%maxUploadSize%" => ($context["max_upload_size"] ?? null)), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                    <div id=\"form_step3_virtual_product_file_input\" class=\"";
        // line 101
        echo (($this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "vars", array(), "any", false, true), "value", array(), "any", false, true), "filename", array(), "any", true, true)) ? ("hide") : ("show"));
        echo "\">
                      ";
        // line 102
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "file", array()), 'widget');
        echo "
                    </div>
                    <div id=\"form_step3_virtual_product_file_details\" class=\"";
        // line 104
        echo (($this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "vars", array(), "any", false, true), "value", array(), "any", false, true), "filename", array(), "any", true, true)) ? ("show") : ("hide"));
        echo "\">
                      <a href=\"";
        // line 105
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "vars", array(), "any", false, true), "value", array(), "any", false, true), "file_download_link", array(), "any", true, true)) ? ($this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "vars", array()), "value", array()), "file_download_link", array())) : ("")), "html", null, true);
        echo "\" class=\"btn btn-default btn-sm download\">";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Download file", array(), "Admin.Actions"), "html", null, true);
        echo "</a>
                      <a href=\"";
        // line 106
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_virtual_remove_file_action", array("idProduct" => ($context["productId"] ?? null))), "html", null, true);
        echo "\" class=\"btn btn-danger btn-sm delete\">";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Delete this file", array(), "Admin.Actions"), "html", null, true);
        echo "</a>
                    </div>
                  </fieldset>
                </div>
                <div class=\"col-md-6\">
                  <fieldset class=\"form-group\">
                    <label class=\"form-control-label\">";
        // line 112
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "name", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    <span class=\"help-box\" data-toggle=\"popover\"
                          data-content=\"";
        // line 114
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The full filename with its extension (e.g. Book.pdf)", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                    ";
        // line 115
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "name", array()), 'errors');
        echo "
                    ";
        // line 116
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "name", array()), 'widget');
        echo "
                  </fieldset>
                </div>
                <div class=\"col-md-6\">
                  <fieldset class=\"form-group\">
                    <label class=\"form-control-label\">";
        // line 121
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "nb_downloadable", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    <span class=\"help-box\" data-toggle=\"popover\"
                          data-content=\"";
        // line 123
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Number of downloads allowed per customer. Set to 0 for unlimited downloads.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                    ";
        // line 124
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "nb_downloadable", array()), 'errors');
        echo "
                    ";
        // line 125
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "nb_downloadable", array()), 'widget');
        echo "
                  </fieldset>
                </div>
                <div class=\"col-md-6\">
                  <fieldset class=\"form-group\">
                    <label class=\"form-control-label\">";
        // line 130
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "expiration_date", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    <span class=\"help-box\" data-toggle=\"popover\"
                          data-content=\"";
        // line 132
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("If set, the file will not be downloadable after this date. Leave blank if you do not wish to attach an expiration date.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                    ";
        // line 133
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "expiration_date", array()), 'errors');
        echo "
                    ";
        // line 134
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "expiration_date", array()), 'widget');
        echo "
                  </fieldset>
                </div>
                <div class=\"col-md-6\">
                  <fieldset class=\"form-group\">
                    <label class=\"form-control-label\">";
        // line 139
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["formVirtualProduct"] ?? null), "nb_days", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
                    <span class=\"help-box\" data-toggle=\"popover\"
                          data-content=\"";
        // line 141
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Number of days this file can be accessed by customers. Set to zero for unlimited access.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                    ";
        // line 142
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "nb_days", array()), 'errors');
        echo "
                    ";
        // line 143
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "nb_days", array()), 'widget');
        echo "
                  </fieldset>
                </div>
                <div class=\"col-md-12\">
                  ";
        // line 147
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["formVirtualProduct"] ?? null), "save", array()), 'widget');
        echo "
                </div>
              </div>
            </div>

            ";
        // line 152
        if ((($context["asm_globally_activated"] ?? null) && ($this->getAttribute($this->getAttribute(($context["formType"] ?? null), "vars", array()), "value", array()) != "2"))) {
            // line 153
            echo "              <div class=\"form-group\" id=\"asm_quantity_management\">
                <label class=\"col-sm-2 control-label\" for=\"form_step3_advanced_stock_management\"></label>
                <div class=\"col-sm-10\">
                  ";
            // line 156
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formAdvancedStockManagement"] ?? null), 'errors');
            echo "
                  ";
            // line 157
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formAdvancedStockManagement"] ?? null), 'widget');
            echo "
                  ";
            // line 158
            if (($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "step1", array()), "type_product", array()), "vars", array()), "value", array()) == "1")) {
                // line 159
                echo "                    ";
                echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("When enabling advanced stock management for a pack, please make sure it is also enabled for its product(s) – if you choose to decrement product quantities.", array(), "Admin.Catalog.Notification"), "html", null, true);
                echo "
                  ";
            }
            // line 161
            echo "                </div>
              </div>
              <div class=\"form-group\" id=\"depends_on_stock_div\" style=\"";
            // line 163
            if ( !$this->getAttribute($this->getAttribute(($context["formAdvancedStockManagement"] ?? null), "vars", array()), "checked", array())) {
                echo "display: none;";
            }
            echo "\">
                <label class=\"col-sm-2 control-label\" for=\"form_step3_depends_on_stock\"></label>
                <div class=\"col-sm-10\">
                  ";
            // line 166
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formDependsOnStocks"] ?? null), 'errors');
            echo "
                  ";
            // line 167
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formDependsOnStocks"] ?? null), 'widget');
            echo "
                </div>
              </div>
            ";
        }
        // line 171
        echo "            ";
        if ($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_STOCK_MANAGEMENT")) {
            // line 172
            echo "              <div id=\"pack_stock_type\">
                <h2>";
            // line 173
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["formPackStockType"] ?? null), "vars", array()), "label", array()), "html", null, true);
            echo "</h2>
                <div class=\"row col-md-4\">
                  <fieldset class=\"form-group\">
                    ";
            // line 176
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formPackStockType"] ?? null), 'errors');
            echo "
                    ";
            // line 177
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formPackStockType"] ?? null), 'widget');
            echo "
                  </fieldset>
                </div>
              </div>
            ";
        }
        // line 182
        echo "            ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_combinations.html.twig", array("form" => ($context["formStep3"] ?? null), "form_combination_bulk" => ($context["formCombinations"] ?? null)));
        echo "

            ";
        // line 184
        echo $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHook("displayAdminProductsQuantitiesStepBottom", array("id_product" => ($context["productId"] ?? null)));
        echo "

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Panels/combinations.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  378 => 184,  372 => 182,  364 => 177,  360 => 176,  354 => 173,  351 => 172,  348 => 171,  341 => 167,  337 => 166,  329 => 163,  325 => 161,  319 => 159,  317 => 158,  313 => 157,  309 => 156,  304 => 153,  302 => 152,  294 => 147,  287 => 143,  283 => 142,  279 => 141,  274 => 139,  266 => 134,  262 => 133,  258 => 132,  253 => 130,  245 => 125,  241 => 124,  237 => 123,  232 => 121,  224 => 116,  220 => 115,  216 => 114,  211 => 112,  200 => 106,  194 => 105,  190 => 104,  185 => 102,  181 => 101,  177 => 100,  172 => 98,  166 => 95,  157 => 89,  150 => 85,  141 => 81,  131 => 74,  127 => 73,  123 => 72,  115 => 67,  111 => 66,  107 => 65,  99 => 60,  95 => 59,  91 => 58,  84 => 54,  76 => 49,  72 => 48,  68 => 47,  63 => 45,  60 => 44,  54 => 41,  50 => 40,  46 => 39,  43 => 38,  41 => 37,  35 => 34,  29 => 33,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Panels/combinations.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Panels/combinations.html.twig");
    }
}

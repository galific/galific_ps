<?php

/* @Product/ProductPage/Panels/options.html.twig */
class __TwigTemplate_a88ca5439cd7a756011ac51c6f3d632c6a78ea6c1ee3759567c255856fa0b1c5 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            '__internal_86104f99fa15311eafccea77aaacfb8f941c0b6abea2b23775fd33fcb1108839' => array($this, 'block___internal_86104f99fa15311eafccea77aaacfb8f941c0b6abea2b23775fd33fcb1108839'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "<div role=\"tabpanel\" class=\"form-contenttab tab-pane\" id=\"step6\">
  <div class=\"row\">
    <div class=\"col-md-12\">
      <div class=\"container-fluid\">
        <div class=\"row\">

          <div class=\"col-md-12\">

            ";
        // line 33
        echo $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHook("displayAdminProductsOptionsStepTop", array("id_product" => ($context["productId"] ?? null)));
        echo "

            <div class=\"row\">
              <div class=\"col-md-12\">
                <h2>";
        // line 37
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Visibility", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</h2>
                <p class=\"subtitle\">";
        // line 38
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Where do you want your product to appear?", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</p>
              </div>
            </div>

            <div class=\"row\">
              <div class=\"col-md-4 form-group\">
                ";
        // line 44
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "visibility", array()), 'errors');
        echo "
                ";
        // line 45
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "visibility", array()), 'widget');
        echo "
              </div>
            </div>

            <div class=\"row\">
              <div class=\"col-md-7 form-group\">
                  ";
        // line 51
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "display_options", array()), 'errors');
        echo "
                  <div class=\"row\">
                    <div class=\"col-md-4 js-available-for-order\">
                      ";
        // line 54
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "display_options", array()), "available_for_order", array()), 'widget');
        echo "
                    </div>
                    <div class=\"col-md-3 js-show-price\">
                      ";
        // line 57
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "display_options", array()), "show_price", array()), 'widget');
        echo "
                    </div>
                    <div class=\"col-md-5\">
                      ";
        // line 60
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "display_options", array()), "online_only", array()), 'widget');
        echo "
                    </div>
                  </div>
              </div>
            </div>
            <div class=\"row form-group\">
              <div class=\"col-md-8\">
                <label class=\"form-control-label\">";
        // line 67
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Tags", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</label>
                ";
        // line 68
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "tags", array()), 'errors');
        echo "
                ";
        // line 69
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "tags", array()), 'widget');
        echo "
                <div class=\"alert expandable-alert alert-info mt-3\" role=\"alert\">
                  <button type=\"button\" class=\"read-more btn-link\" data-toggle=\"collapse\" data-target=\"#tagsInfo\" aria-expanded=\"false\" aria-controls=\"collapseDanger\">
                    ";
        // line 72
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Read more", array(), "Admin.Actions");
        echo "
                  </button>
                  <p class=\"alert-text\">";
        // line 74
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Tags are meant to help your customers find your products via the search bar.", array(), "Admin.Catalog.Help");
        echo "</p>
                  <div class=\"alert-more collapse\" id=\"tagsInfo\">
                    <p>
                      ";
        // line 77
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Choose terms and keywords that your customers will use to search for this product and make sure you are consistent with the tags you may have already used.", array(), "Admin.Catalog.Help");
        echo "<br>
                      ";
        // line 78
        echo twig_replace_filter($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can manage tag aliases in the [1]Search section[/1]. If you add new tags, you have to rebuild the index.", array(), "Admin.Catalog.Help"), array("[1]" => (("<a href=\"" . $this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getAdminLink("AdminSearchConf")) . "\" target=\"_blank\">"), "[/1]" => "</a>"));
        // line 83
        echo "
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <div class=\"row\">
              <div class=\"col-md-12\">
                <h2>";
        // line 92
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Condition & References", array(), "Admin.Catalog.Feature");
        echo "</h2>
              </div>
            </div>

            <div class=\"row\">
              <fieldset class=\"col-md-4 form-group\">
                <label class=\"form-control-label\">
                  ";
        // line 99
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "condition", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 101
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Not all shops sell new products. This option enables you to indicate the condition of the product. It can be required on some marketplaces.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </label>
                ";
        // line 103
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "condition", array()), 'errors');
        echo "
                ";
        // line 104
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "condition", array()), 'widget');
        echo "
              </fieldset>
              <fieldset class=\"col-md-4 form-group\">
                <label class=\"form-control-label\">&nbsp;</label>
                ";
        // line 108
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "show_condition", array()), 'widget');
        echo "
              </fieldset>
            </div>
            <div class=\"row\">
              <fieldset class=\"col-md-4 form-group\">
                <label class=\"form-control-label\">
                  ";
        // line 114
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "isbn", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 116
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("ISBN is used internationally to identify books and their various editions.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </label>
                ";
        // line 118
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "isbn", array()), 'errors');
        echo "
                ";
        // line 119
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "isbn", array()), 'widget');
        echo "
              </fieldset>
              <fieldset class=\"col-md-4 form-group\">
                <label class=\"form-control-label\">
                  ";
        // line 123
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "ean13", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 125
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This type of product code is specific to Europe and Japan, but is widely used internationally. It is a superset of the UPC code: all products marked with an EAN will be accepted in North America.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </label>
                ";
        // line 127
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "ean13", array()), 'errors');
        echo "
                ";
        // line 128
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "ean13", array()), 'widget');
        echo "
              </fieldset>
            </div>
            <div class=\"row\">
              <fieldset class=\"col-md-4 form-group\">
                <label class=\"form-control-label\">
                  ";
        // line 134
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "upc", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
                  <span class=\"help-box\" data-toggle=\"popover\"
                    data-content=\"";
        // line 136
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This type of product code is widely used in the United States, Canada, the United Kingdom, Australia, New Zealand and in other countries.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
                </label>
                ";
        // line 138
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "upc", array()), 'errors');
        echo "
                ";
        // line 139
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "upc", array()), 'widget');
        echo "
              </fieldset>
            </div>

            <div class=\"row\">
              <div class=\"col-md-12\">
                <div id=\"custom_fields\" class=\"mt-3\">
                  <h2>";
        // line 146
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "custom_fields", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</h2>
                  <p class=\"subtitle\">";
        // line 147
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Customers can personalize the product by entering some text or by providing custom image files.", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</p>
                  ";
        // line 148
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "custom_fields", array()), 'errors');
        echo "
                  <ul class=\"customFieldCollection nostyle\" data-prototype=\"
                              ";
        // line 150
        echo twig_escape_filter($this->env,         $this->renderBlock("__internal_86104f99fa15311eafccea77aaacfb8f941c0b6abea2b23775fd33fcb1108839", $context, $blocks));
        // line 152
        echo "\">
                    ";
        // line 153
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["optionsForm"] ?? null), "custom_fields", array()));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["field"]) {
            // line 154
            echo "                      <li>
                        ";
            // line 155
            echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_custom_fields.html.twig", array("form" => $context["field"]));
            echo "
                      </li>
                    ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['field'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 158
        echo "                  </ul>
                  <a href=\"#\" class=\"btn btn-outline-secondary add\">
                    <i class=\"material-icons\">add_circle</i>
                    ";
        // line 161
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Add a customization field", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                  </a>
                </div>
              </div>
            </div>

            <div class=\"row mt-4\">
              <div class=\"col-md-8\">
                <h2>";
        // line 169
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Attached files", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</h2>
                <p class=\"subtitle\">";
        // line 170
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Add files that customers can download directly on the product page (instructions, manual, recipe, etc.).", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</p>
                ";
        // line 171
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "attachments", array()), 'widget');
        echo "
              </div>
            </div>
            <div class=\"row mt-3\">
              <div class=\"col-md-8\">
                <a
                  class=\"btn btn-outline-secondary mb-3\"
                  href=\"#collapsedForm\"
                  data-toggle=\"collapse\"
                  aria-expanded=\"false\"
                  aria-controls=\"collapsedForm\"
                >
                  <i class=\"material-icons\">add_circle</i>
                  ";
        // line 184
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Attach a new file", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
                </a>
                <fieldset class=\"form-group collapse\" id=\"collapsedForm\">
                  ";
        // line 187
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), 'errors');
        echo "
                  <div id=\"form_step6_attachment_product\" data-action=\"";
        // line 188
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), "vars", array()), "attr", array()), "data-action", array(), "array"), "html", null, true);
        echo "\">
                    <div class=\"form-group\">";
        // line 189
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), "file", array()), 'widget');
        echo "</div>
                    <div class=\"form-group\">";
        // line 190
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), "name", array()), 'widget');
        echo "</div>
                    <div class=\"form-group\">";
        // line 191
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), "description", array()), 'widget');
        echo "</div>
                    <div class=\"form-group\">
                      ";
        // line 193
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), "add", array()), 'widget');
        echo "
                      ";
        // line 194
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "attachment_product", array()), "cancel", array()), 'widget');
        echo "
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>

            <div class=\"row mt-3\">
              <div class=\"col-md-8\" id=\"supplier_collection\">
                ";
        // line 203
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_supplier_choice.html.twig", array("form" => ($context["optionsForm"] ?? null)));
        echo "
              </div>
            </div>
            <div class=\"row\">
              <div id=\"supplier_combination_collection\" class=\"col-md-12\" data-url=\"";
        // line 207
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_supplier_refresh_product_supplier_combination_form", array("idProduct" => ($context["productId"] ?? null), "supplierIds" => 1)), "html", null, true);
        echo "\">
                ";
        // line 208
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_supplier_combination.html.twig", array("suppliers" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "suppliers", array()), "vars", array()), "value", array()), "form" => ($context["optionsForm"] ?? null)));
        echo "
              </div>
            </div>

            ";
        // line 212
        echo $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHook("displayAdminProductsOptionsStepBottom", array("id_product" => ($context["productId"] ?? null)));
        echo "

          </div>
        </div>
      </div>
    </div>
  </div>
</div>
";
    }

    // line 150
    public function block___internal_86104f99fa15311eafccea77aaacfb8f941c0b6abea2b23775fd33fcb1108839($context, array $blocks = array())
    {
        // line 151
        echo "                              ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_custom_fields.html.twig", array("form" => $this->getAttribute($this->getAttribute($this->getAttribute(($context["optionsForm"] ?? null), "custom_fields", array()), "vars", array()), "prototype", array())));
        echo "
                              ";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Panels/options.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  413 => 151,  410 => 150,  397 => 212,  390 => 208,  386 => 207,  379 => 203,  367 => 194,  363 => 193,  358 => 191,  354 => 190,  350 => 189,  346 => 188,  342 => 187,  336 => 184,  320 => 171,  316 => 170,  312 => 169,  301 => 161,  296 => 158,  279 => 155,  276 => 154,  259 => 153,  256 => 152,  254 => 150,  249 => 148,  245 => 147,  241 => 146,  231 => 139,  227 => 138,  222 => 136,  217 => 134,  208 => 128,  204 => 127,  199 => 125,  194 => 123,  187 => 119,  183 => 118,  178 => 116,  173 => 114,  164 => 108,  157 => 104,  153 => 103,  148 => 101,  143 => 99,  133 => 92,  122 => 83,  120 => 78,  116 => 77,  110 => 74,  105 => 72,  99 => 69,  95 => 68,  91 => 67,  81 => 60,  75 => 57,  69 => 54,  63 => 51,  54 => 45,  50 => 44,  41 => 38,  37 => 37,  30 => 33,  20 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Panels/options.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Panels/options.html.twig");
    }
}

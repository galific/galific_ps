<?php

/* @Product/ProductPage/Forms/form_shipping.html.twig */
class __TwigTemplate_78531e9bf1256fcb680cc24a770fc8aeb34530050d006d8c77b98999eb124adc extends Twig_Template
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
        list($context["dimension_unit"], $context["weight_unit"]) =         array($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_DIMENSION_UNIT"), $this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_WEIGHT_UNIT"));
        // line 26
        echo "
<div class=\"col-md-12 pb-1\">
  <h2>";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Package dimension", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</h2>
  <p class=\"subtitle\">";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Adjust your shipping costs by filling in the product dimensions.", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</p>
</div>

<div class=\"col-xl-2 col-lg-3\">
  <div class=\"form-group\">
    <label class=\"form-control-label\">";
        // line 34
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "width", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
    ";
        // line 35
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "width", array()), 'errors');
        echo "
    <div class=\"input-group\">
      ";
        // line 37
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "width", array()), 'widget');
        echo "
      <div class=\"input-group-append\">
        <span class=\"input-group-text\">";
        // line 39
        echo twig_escape_filter($this->env, ($context["dimension_unit"] ?? null), "html", null, true);
        echo "</span>
      </div>
    </div>
  </div>
</div>
<div class=\"col-xl-2 col-lg-3\">
  <div class=\"form-group\">
    <label class=\"form-control-label\">";
        // line 46
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "height", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
    ";
        // line 47
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "height", array()), 'errors');
        echo "
    <div class=\"input-group\">
      ";
        // line 49
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "height", array()), 'widget');
        echo "
      <div class=\"input-group-append\">
        <span class=\"input-group-text\">";
        // line 51
        echo twig_escape_filter($this->env, ($context["dimension_unit"] ?? null), "html", null, true);
        echo "</span>
      </div>
    </div>
  </div>
</div>
<div class=\"col-xl-2 col-lg-3\">
  <div class=\"form-group\">
    <label class=\"form-control-label\">";
        // line 58
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "depth", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
    ";
        // line 59
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "depth", array()), 'errors');
        echo "
    <div class=\"input-group\">
      ";
        // line 61
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "depth", array()), 'widget');
        echo "
      <div class=\"input-group-append\">
        <span class=\"input-group-text\">";
        // line 63
        echo twig_escape_filter($this->env, ($context["dimension_unit"] ?? null), "html", null, true);
        echo "</span>
      </div>
    </div>
  </div>
</div>
<div class=\"col-xl-2 col-lg-3\">
  <div class=\"form-group\">
    <label class=\"form-control-label\">";
        // line 70
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "weight", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
    ";
        // line 71
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "weight", array()), 'errors');
        echo "
    <div class=\"input-group\">
      ";
        // line 73
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "weight", array()), 'widget');
        echo "
      <div class=\"input-group-append\">
        <span class=\"input-group-text\">";
        // line 75
        echo twig_escape_filter($this->env, ($context["weight_unit"] ?? null), "html", null, true);
        echo "</span>
      </div>
    </div>
  </div>
</div>

<div class=\"col-md-12\">
  <div class=\"form-group\">
    <h2>
      ";
        // line 84
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "additional_delivery_times", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
      <span class=\"help-box\" data-toggle=\"popover\"
        data-content=\"";
        // line 86
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Display delivery time for a product is advised for merchants selling in Europe to comply with the local laws.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
    </h2>
    <div class=\"row\">
       <div class=\"col-md-12\" ";
        // line 89
        if (        $this->hasBlock("widget_container_attributes", $context, $blocks)) {
            echo " ";
            $this->displayBlock("widget_container_attributes", $context, $blocks);
            echo " ";
        }
        echo ">
          ";
        // line 90
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["form"] ?? null), "additional_delivery_times", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["child"]) {
            // line 91
            echo "            ";
            if (($this->getAttribute($this->getAttribute($context["child"], "vars", array()), "value", array()) == 1)) {
                // line 92
                echo "              <div class=\"widget-radio-inline\">
                ";
                // line 93
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($context["child"], 'widget');
                echo "
                <a href=\"";
                // line 94
                echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_preferences");
                echo "\" class=\"btn sensitive px-0\" target=_blank><i class=\"material-icons\">open_in_new</i> ";
                echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("edit", array(), "Admin.Catalog.Help"), "html", null, true);
                echo "</a>
              </div>
            ";
            } else {
                // line 97
                echo "              ";
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($context["child"], 'widget');
                echo "
            ";
            }
            // line 99
            echo "          ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['child'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 100
        echo "        </div>
     </div>
  </div>
</div>

<div class=\"col-xl-6 col-lg-6\">
  <div class=\"form-group\">
    <label class=\"form-control-label\">";
        // line 107
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "delivery_in_stock", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
    ";
        // line 108
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "delivery_in_stock", array()), 'errors');
        echo "
    ";
        // line 109
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "delivery_in_stock", array()), 'widget');
        echo "
    <p class=\"subtitle italic\">";
        // line 110
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Leave empty to disable.", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</p>
  </div>
</div>
<div class=\"col-xl-6 col-lg-6\">
  <div class=\"form-group\">
    <label class=\"form-control-label\">";
        // line 115
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "delivery_out_stock", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
    ";
        // line 116
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "delivery_out_stock", array()), 'errors');
        echo "
    ";
        // line 117
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "delivery_out_stock", array()), 'widget');
        echo "
    <p class=\"subtitle italic\">";
        // line 118
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Leave empty to disable.", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</p>
  </div>
</div>

<div class=\"col-md-12\">
  <div class=\"form-group\">
    <h2>
      ";
        // line 125
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "additional_shipping_cost", array()), "vars", array()), "label", array()), "html", null, true);
        echo "
      <span class=\"help-box\" data-toggle=\"popover\"
        data-content=\"";
        // line 127
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("If a carrier has a tax, it will be added to the shipping fees. Does not apply to free shipping.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\" ></span>
    </h2>
    <label class=\"form-control-label\">";
        // line 129
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Does this product incur additional shipping costs?", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</label>
    <div class=\"row\">
      <div class=\"col-md-2\">
        ";
        // line 132
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "additional_shipping_cost", array()), 'widget');
        echo "
      </div>
    </div>
  </div>
</div>

<div class=\"col-md-12\">
  <div class=\"form-group\">
    <h2>";
        // line 140
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "selectedCarriers", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</h2>
    ";
        // line 141
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "selectedCarriers", array()), 'widget');
        echo "
  </div>
</div>

<div class=\"col-md-12\">
  <div class=\"alert alert-warning\" role=\"alert\">
    <p class=\"alert-text\">
        ";
        // line 148
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("If no carrier is selected then all the carriers will be available for customers orders.", array(), "Admin.Catalog.Notification");
        echo "
    </p>
  </div>
</div>

<div class=\"col-md-12\">
  <div id=\"warehouse_combination_collection\" class=\"col-md-12 form-group\" data-url=\"";
        // line 154
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_warehouse_refresh_product_warehouse_combination_form");
        echo "\">
    ";
        // line 155
        if (((($context["asm_globally_activated"] ?? null) && ($context["isNotVirtual"] ?? null)) && ($context["isChecked"] ?? null))) {
            // line 156
            echo "      ";
            echo twig_include($this->env, $context, "@PrestaShop/Admin:Product/ProductPage/Forms/form_warehouse_combination.html.twig", array("warehouses" => ($context["warehouses"] ?? null), "form" => ($context["form"] ?? null)));
            echo "
    ";
        }
        // line 158
        echo "  </div>
</div>

";
        // line 161
        echo $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHook("displayAdminProductsShippingStepBottom", array("id_product" => ($context["id_product"] ?? null)));
        echo "
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Forms/form_shipping.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  306 => 161,  301 => 158,  295 => 156,  293 => 155,  289 => 154,  280 => 148,  270 => 141,  266 => 140,  255 => 132,  249 => 129,  244 => 127,  239 => 125,  229 => 118,  225 => 117,  221 => 116,  217 => 115,  209 => 110,  205 => 109,  201 => 108,  197 => 107,  188 => 100,  182 => 99,  176 => 97,  168 => 94,  164 => 93,  161 => 92,  158 => 91,  154 => 90,  146 => 89,  140 => 86,  135 => 84,  123 => 75,  118 => 73,  113 => 71,  109 => 70,  99 => 63,  94 => 61,  89 => 59,  85 => 58,  75 => 51,  70 => 49,  65 => 47,  61 => 46,  51 => 39,  46 => 37,  41 => 35,  37 => 34,  29 => 29,  25 => 28,  21 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Forms/form_shipping.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Forms/form_shipping.html.twig");
    }
}

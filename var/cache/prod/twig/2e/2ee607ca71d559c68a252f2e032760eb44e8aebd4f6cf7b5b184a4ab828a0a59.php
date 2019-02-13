<?php

/* @Product/ProductPage/Forms/form_specific_price.html.twig */
class __TwigTemplate_8731ad2af62006c6b5ef2f1f7cc25bb561708942909784ed83930ea4cacd5d67 extends Twig_Template
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
        if ( !(isset($context["is_modal"]) || array_key_exists("is_modal", $context))) {
            // line 26
            echo "  ";
            $context["is_modal"] = false;
        }
        // line 28
        echo "
";
        // line 29
        if ((($context["is_modal"] ?? null) == false)) {
            // line 30
            echo "  ";
            $context["column_default_md_3"] = "col-md-3";
            // line 31
            echo "  ";
            $context["column_default_md_2"] = "col-md-2";
            // line 32
            echo "  ";
            $context["column_default_xl_2"] = "col-xl-2";
        } else {
            // line 34
            echo "  ";
            $context["column_default_md_3"] = "col-md-9";
            // line 35
            echo "  ";
            $context["column_default_md_2"] = "col-md-4";
            // line 36
            echo "  ";
            $context["column_default_xl_2"] = "col-xl-4";
        }
        // line 38
        echo "
<div class=\"card card-block\">
  <h4><b>";
        // line 40
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Specific price conditions", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</b></h4>
  ";
        // line 41
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        echo "

  ";
        // line 43
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "sp_id_shop", array(), "any", false, true), "vars", array(), "any", false, true), "choices", array(), "any", true, true)) {
            // line 44
            echo "  <div class=\"row\">
    <div class=\"col-md-4\">
      <fieldset class=\"form-group\">
        <label>";
            // line 47
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Shop", array(), "Admin.Global"), "html", null, true);
            echo "</label>
        ";
            // line 48
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_shop", array()), 'errors');
            echo "
        ";
            // line 49
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_shop", array()), 'widget');
            echo "
      </fieldset>
    </div>
  </div>
  ";
        } else {
            // line 54
            echo "      ";
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_shop", array()), 'widget');
            echo "
  ";
        }
        // line 56
        echo "
  <div class=\"row\">
    <div class=\"";
        // line 58
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>";
        // line 60
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("For", array(), "Admin.Global"), "html", null, true);
        echo "</label>
        ";
        // line 61
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_currency", array()), 'errors');
        echo "
        ";
        // line 62
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_currency", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"";
        // line 65
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>&nbsp;</label>
        ";
        // line 68
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_country", array()), 'errors');
        echo "
        ";
        // line 69
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_country", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"";
        // line 72
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>&nbsp;</label>
        ";
        // line 75
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_group", array()), 'errors');
        echo "
        ";
        // line 76
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_group", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"col-md-6\">
      <fieldset class=\"form-group\">
        <label>";
        // line 81
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Customer", array(), "Admin.Global"), "html", null, true);
        echo "</label>
        ";
        // line 82
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_customer", array()), 'errors');
        echo "
        ";
        // line 83
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_customer", array()), 'widget');
        echo "
      </fieldset>
    </div>
  </div>
  <div class=\"row\">
    <div id=\"specific-price-combination-selector\" class=\"col-md-6 ";
        // line 88
        echo ((($context["has_combinations"] ?? null)) ? ("") : ("hide"));
        echo "\">
      <fieldset class=\"form-group\">
        <label>";
        // line 90
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "sp_id_product_attribute", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
        ";
        // line 91
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_product_attribute", array()), 'errors');
        echo "
        ";
        // line 92
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_id_product_attribute", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"clearfix\"></div>
    <div class=\"";
        // line 96
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>";
        // line 98
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "sp_from", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
        ";
        // line 99
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_from", array()), 'errors');
        echo "
        ";
        // line 100
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_from", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"";
        // line 103
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>";
        // line 105
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("to", array(), "Admin.Global");
        echo "</label>
        ";
        // line 106
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_to", array()), 'errors');
        echo "
        ";
        // line 107
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_to", array()), 'widget');
        echo "
      </fieldset>
    </div>
  ";
        // line 110
        if ((($context["is_modal"] ?? null) == true)) {
            // line 111
            echo "  </div>
  <div class=\"row\">
  ";
        }
        // line 114
        echo "    <div class=\"";
        echo twig_escape_filter($this->env, ($context["column_default_md_2"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>";
        // line 116
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "sp_from_quantity", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
        ";
        // line 117
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_from_quantity", array()), 'errors');
        echo "
        <div class=\"input-group\">
          ";
        // line 119
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_from_quantity", array()), 'widget');
        echo "
          <div class=\"input-group-append\">
            <span class=\"input-group-text\">";
        // line 121
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Unit(s)", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</span>
          </div>
        </div>
      </fieldset>
    </div>
  </div>
  <br>

  <h4><b>";
        // line 129
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Impact on price", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</b></h4>
  <div class=\"row\">
    <div class=\"";
        // line 131
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>";
        // line 133
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "sp_price", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
        ";
        // line 134
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_price", array()), 'errors');
        echo "
        ";
        // line 135
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_price", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"";
        // line 138
        echo twig_escape_filter($this->env, ($context["column_default_md_3"] ?? null), "html", null, true);
        echo "\">
      <fieldset class=\"form-group\">
        <label>&nbsp;</label>
        ";
        // line 141
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "leave_bprice", array()), 'errors');
        echo "
        ";
        // line 142
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "leave_bprice", array()), 'widget');
        echo "
      </fieldset>
    </div>
  </div>
  <div class=\"row\">
    <div class=\"";
        // line 147
        echo twig_escape_filter($this->env, ($context["column_default_xl_2"] ?? null), "html", null, true);
        echo " col-lg-3\">
      <fieldset class=\"form-group\">
        <label>";
        // line 149
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Apply a discount of", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</label>
        ";
        // line 150
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_reduction", array()), 'errors');
        echo "
        ";
        // line 151
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_reduction", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"";
        // line 154
        echo twig_escape_filter($this->env, ($context["column_default_xl_2"] ?? null), "html", null, true);
        echo " col-lg-3\">
      <fieldset class=\"form-group\">
        <label>&nbsp;</label>
        ";
        // line 157
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_reduction_type", array()), 'errors');
        echo "
        ";
        // line 158
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_reduction_type", array()), 'widget');
        echo "
      </fieldset>
    </div>
    <div class=\"";
        // line 161
        echo twig_escape_filter($this->env, ($context["column_default_xl_2"] ?? null), "html", null, true);
        echo " col-lg-3\">
      <fieldset class=\"form-group\">
        <label>&nbsp;</label>
        ";
        // line 164
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_reduction_tax", array()), 'errors');
        echo "
        ";
        // line 165
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "sp_reduction_tax", array()), 'widget');
        echo "
      </fieldset>
    </div>
  </div>
  <div class=\"col-md-12 text-sm-right\">
    ";
        // line 170
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "cancel", array()), 'widget');
        echo "
    ";
        // line 171
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "save", array()), 'widget');
        echo "
  </div>
  <div class=\"clearfix\"></div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Forms/form_specific_price.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  360 => 171,  356 => 170,  348 => 165,  344 => 164,  338 => 161,  332 => 158,  328 => 157,  322 => 154,  316 => 151,  312 => 150,  308 => 149,  303 => 147,  295 => 142,  291 => 141,  285 => 138,  279 => 135,  275 => 134,  271 => 133,  266 => 131,  261 => 129,  250 => 121,  245 => 119,  240 => 117,  236 => 116,  230 => 114,  225 => 111,  223 => 110,  217 => 107,  213 => 106,  209 => 105,  204 => 103,  198 => 100,  194 => 99,  190 => 98,  185 => 96,  178 => 92,  174 => 91,  170 => 90,  165 => 88,  157 => 83,  153 => 82,  149 => 81,  141 => 76,  137 => 75,  131 => 72,  125 => 69,  121 => 68,  115 => 65,  109 => 62,  105 => 61,  101 => 60,  96 => 58,  92 => 56,  86 => 54,  78 => 49,  74 => 48,  70 => 47,  65 => 44,  63 => 43,  58 => 41,  54 => 40,  50 => 38,  46 => 36,  43 => 35,  40 => 34,  36 => 32,  33 => 31,  30 => 30,  28 => 29,  25 => 28,  21 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Forms/form_specific_price.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Forms/form_specific_price.html.twig");
    }
}

<?php

/* @Product/ProductPage/Forms/form_supplier_combination.html.twig */
class __TwigTemplate_b8b659272570c0d660ca348c2107a6cc50f0ed1c854f4528d27f3d15057a2840 extends Twig_Template
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
        if ((twig_length_filter($this->env, ($context["suppliers"] ?? null)) > 0)) {
            // line 26
            echo "  <h4>";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Supplier reference(s)", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</h4>
  <div class=\"row\">
    <div class=\"col-md-12\">
      <div class=\"alert alert-info\" role=\"alert\">
        <p class=\"alert-text\">
          ";
            // line 31
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can specify product reference(s) for each associated supplier. Click \"Save\" after changing selected suppliers to display the associated product references.", array(), "Admin.Catalog.Help");
            echo "
        </p>
      </div>
    </div>
  </div>
";
        }
        // line 37
        echo "
";
        // line 38
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["suppliers"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["supplierId"]) {
            // line 39
            echo "  ";
            $context["collectionName"] = ("supplier_combination_" . $context["supplierId"]);
            // line 40
            echo "  <div class=\"panel panel-default\">
    <div class=\"panel-heading\"><strong>";
            // line 41
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), ($context["collectionName"] ?? null), array(), "array"), "vars", array()), "label", array()), "html", null, true);
            echo "</strong></div>
    <div class=\"panel-body\" id=\"supplier_combination_";
            // line 42
            echo twig_escape_filter($this->env, $context["supplierId"], "html", null, true);
            echo "\">
      <div>
        ";
            // line 44
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), ($context["collectionName"] ?? null), array(), "array"), 'errors');
            echo "
        <table class=\"table\">
          <thead class=\"thead-default\">
            <tr >
              <th width=\"30%\">";
            // line 48
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Product name", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</th>
              <th width=\"30%\">";
            // line 49
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Supplier reference", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</th>
              <th width=\"20%\">";
            // line 50
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Price (tax excl.)", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</th>
              <th width=\"20%\">";
            // line 51
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Currency", array(), "Admin.Global"), "html", null, true);
            echo "</th>
            </tr>
          </thead>
          <tbody>
          ";
            // line 55
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["form"] ?? null), ($context["collectionName"] ?? null), array(), "array"));
            foreach ($context['_seq'] as $context["_key"] => $context["supplier_combination"]) {
                // line 56
                echo "            <tr>
              <td>";
                // line 57
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute($context["supplier_combination"], "vars", array()), "value", array()), "label", array()), "html", null, true);
                echo "</td>
              <td>";
                // line 58
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($context["supplier_combination"], "supplier_reference", array()), 'widget');
                echo "</td>
              <td>";
                // line 59
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($context["supplier_combination"], "product_price", array()), 'widget');
                echo "</td>
              <td>
                ";
                // line 61
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($context["supplier_combination"], "product_price_currency", array()), 'widget');
                echo "
                ";
                // line 62
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($context["supplier_combination"], "id_product_attribute", array()), 'widget');
                echo "
                ";
                // line 63
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($context["supplier_combination"], "supplier_id", array()), 'widget');
                echo "
              </td>
            </tr>
          ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['supplier_combination'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 67
            echo "          </tbody>
        </table>
      </div>
    </div>
  </div>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['supplierId'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Forms/form_supplier_combination.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 67,  115 => 63,  111 => 62,  107 => 61,  102 => 59,  98 => 58,  94 => 57,  91 => 56,  87 => 55,  80 => 51,  76 => 50,  72 => 49,  68 => 48,  61 => 44,  56 => 42,  52 => 41,  49 => 40,  46 => 39,  42 => 38,  39 => 37,  30 => 31,  21 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Forms/form_supplier_combination.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Forms/form_supplier_combination.html.twig");
    }
}

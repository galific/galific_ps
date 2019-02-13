<?php

/* @Product/ProductPage/Forms/form_supplier_choice.html.twig */
class __TwigTemplate_cc81620cf7ae65f098dcfa4dfd04c24be0feeb3b2cd0822d448c87c820ec7ac9 extends Twig_Template
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
        if ((twig_length_filter($this->env, $this->getAttribute(($context["form"] ?? null), "suppliers", array())) > 0)) {
            // line 26
            echo "  <div id=\"custom_fields\">
    <h2>";
            // line 27
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "suppliers", array()), "vars", array()), "label", array()), "html", null, true);
            echo "</h2>
    <div class=\"row mb-1\">
      <div class=\"col-md-12\">
        <div class=\"alert expandable-alert alert-info\" role=\"alert\">
          <button type=\"button\" class=\"read-more btn-link\" data-toggle=\"collapse\" data-target=\"#suppliersInfo\" aria-expanded=\"false\" aria-controls=\"collapseDanger\">
            ";
            // line 32
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Read more", array(), "Admin.Actions");
            echo "
          </button>
          <p class=\"alert-text\">
            ";
            // line 35
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This interface allows you to specify the suppliers of the current product and its combinations, if any.", array(), "Admin.Catalog.Help");
            echo "<br>
            ";
            // line 36
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can specify supplier references according to previously associated suppliers.", array(), "Admin.Catalog.Help");
            echo "
          </p>
          <div class=\"alert-more collapse\" id=\"suppliersInfo\">
            <p>
              ";
            // line 40
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("When using the advanced stock management tool (see Shop Parameters > Products settings), the values you define (price, references) will be used in supply orders.", array(), "Admin.Catalog.Help");
            echo "
            </p>
          </div>
        </div>
      </div>
    </div>

    <div class=\"panel panel-default\">
      <div class=\"panel-body\">
        <div>
          ";
            // line 50
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "suppliers", array()), 'errors');
            echo "
          <table class=\"table\" id=\"form_step6_suppliers\">
            <thead class=\"thead-default\">
            <tr>
              <th width=\"70%\">";
            // line 54
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Choose the suppliers associated with this product", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</th>
              <th width=30%\">";
            // line 55
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Default supplier", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</th>
            </tr>
            </thead>
            <tbody>
            ";
            // line 59
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["form"] ?? null), "suppliers", array()));
            foreach ($context['_seq'] as $context["key"] => $context["supplier"]) {
                // line 60
                echo "              <tr>
                <td>";
                // line 61
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($context["supplier"], 'widget');
                echo "</td>
                <td>";
                // line 62
                echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute($this->getAttribute(($context["form"] ?? null), "default_supplier", array()), $context["key"], array(), "array"), 'widget');
                echo "</td>
              </tr>
            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['supplier'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 65
            echo "            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
";
        }
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Forms/form_supplier_choice.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  100 => 65,  91 => 62,  87 => 61,  84 => 60,  80 => 59,  73 => 55,  69 => 54,  62 => 50,  49 => 40,  42 => 36,  38 => 35,  32 => 32,  24 => 27,  21 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Forms/form_supplier_choice.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Forms/form_supplier_choice.html.twig");
    }
}

<?php

/* @PrestaShop/Admin/Module/Includes/dropdown_bulk.html.twig */
class __TwigTemplate_2effdf95504f047c7145462ebe01e990dad53c05c67ee9ec1143758a7ddf9938 extends Twig_Template
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
        echo "<div class=\"ps-dropdown dropdown btn-group bordered mb-1 module-bulk-actions disabled\">
  <div id=\"bulk-actions-dropdown\" class=\"dropdown-label\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
    <span class=\"js-selected-item selected-item module-bulk-actions-selector-label\">
      ";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Uninstall", array(), "Admin.Actions"), "html", null, true);
        echo "
    </span>
    <i class=\"material-icons arrow-down float-right\">keyboard_arrow_down</i>
  </div>

  <div class=\"ps-dropdown-menu dropdown-menu module-category-selector\">
    <ul class=\"items-list js-items-list\">
      ";
        // line 35
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["bulkActions"] ?? null));
        foreach ($context['_seq'] as $context["key"] => $context["name"]) {
            // line 36
            echo "        <li
          class=\"module-bulk-menu\"
          data-ref=\"";
            // line 38
            echo twig_escape_filter($this->env, $context["key"], "html", null, true);
            echo "\"
          data-display-name=\"";
            // line 39
            echo twig_escape_filter($this->env, $context["name"], "html", null, true);
            echo "\"
        >
          <a class=\"dropdown-item\" href=\"#\">
            ";
            // line 42
            echo twig_escape_filter($this->env, $context["name"], "html", null, true);
            echo "
          </a>
        </li>
      ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['key'], $context['name'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 46
        echo "    </ul>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/dropdown_bulk.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  62 => 46,  52 => 42,  46 => 39,  42 => 38,  38 => 36,  34 => 35,  24 => 28,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/dropdown_bulk.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/dropdown_bulk.html.twig");
    }
}

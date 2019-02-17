<?php

/* @PrestaShop/Admin/Module/Includes/grid_manage_recently_used.html.twig */
class __TwigTemplate_7088945f33643625fac8e24f5af558ada7c4acbc4e476a8079ea5cc45e624505 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'catalog_recently_used' => array($this, 'block_catalog_recently_used'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "
";
        // line 26
        $this->displayBlock('catalog_recently_used', $context, $blocks);
    }

    public function block_catalog_recently_used($context, array $blocks = array())
    {
        // line 27
        echo "  <div id=\"module-recently-used-list\" class=\"module-short-list\">
    <span id=\"recently-used_modules\" class=\"module-search-result-title\">";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Recently Used", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</span>
    <div class=\"modules-list\" data-name=\"recently-used\"></div>
  </div>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/grid_manage_recently_used.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  32 => 28,  29 => 27,  23 => 26,  20 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/grid_manage_recently_used.html.twig", "C:\\wamp64\\www\\galific\\src\\PrestaShopBundle\\Resources\\views\\Admin\\Module\\Includes\\grid_manage_recently_used.html.twig");
    }
}

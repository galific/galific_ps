<?php

/* @PrestaShop/Admin/Module/Includes/grid_manage_empty.html.twig */
class __TwigTemplate_410fd39778586b1d838bae6cbfb457a87705cfd5577fb54a73443349627e4839 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'catalog_category_empty' => array($this, 'block_catalog_category_empty'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "
";
        // line 26
        $this->displayBlock('catalog_category_empty', $context, $blocks);
    }

    public function block_catalog_category_empty($context, array $blocks = array())
    {
        // line 27
        echo "  <div class=\"modules-list module-list-empty\" data-name=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["category"] ?? null), "refMenu", array()), "html", null, true);
        echo "\">
    <p>
      ";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You do not have module in « %categoryName% ».", array("%categoryName%" => $this->getAttribute(($context["category"] ?? null), "name", array())), "Admin.Modules.Feature"), "html", null, true);
        echo "<br/>
      ";
        // line 30
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Discover the best-selling modules of this category in the %link% page.", array("%link%" => (((((("<a href=\"" . $this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getAdminLink("AdminPsMboModule", true)) . "&filterCategoryTab=") . $this->getAttribute(($context["category"] ?? null), "refMenu", array())) . "\">") . $this->getAttribute(($context["category"] ?? null), "name", array())) . "</a>")), "Admin.Modules.Feature");
        echo "
    </p>
  </div>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/grid_manage_empty.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  39 => 30,  35 => 29,  29 => 27,  23 => 26,  20 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/grid_manage_empty.html.twig", "/var/www/html/modules/ps_mbo/views/PrestaShop/Admin/Module/Includes/grid_manage_empty.html.twig");
    }
}

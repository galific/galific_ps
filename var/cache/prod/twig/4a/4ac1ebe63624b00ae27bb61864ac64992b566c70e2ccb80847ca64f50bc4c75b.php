<?php

/* @PrestaShop/Admin/Module/Includes/see_more.html.twig */
class __TwigTemplate_23cf6f4e10a124dbb3e85bbbf4aff1ecd7fb4108943f0cbb4b1727756a154931 extends Twig_Template
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
        echo "<div class=\"col-12\">
  <p class=\"text-center\">
    <button type=\"button\" class=\"btn btn-link see-more\" data-category=\"";
        // line 27
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\">
      ";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("See more", array(), "Admin.Actions"), "html", null, true);
        echo "
    </button>
    <button type=\"button\" class=\"btn btn-link see-less d-none\" data-category=\"";
        // line 30
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\">
      ";
        // line 31
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("See less", array(), "Admin.Actions"), "html", null, true);
        echo "
    </button>
  </p>
</div>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/see_more.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  36 => 31,  32 => 30,  27 => 28,  23 => 27,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/see_more.html.twig", "C:\\wamp64\\www\\galific\\src\\PrestaShopBundle\\Resources\\views\\Admin\\Module\\Includes\\see_more.html.twig");
    }
}

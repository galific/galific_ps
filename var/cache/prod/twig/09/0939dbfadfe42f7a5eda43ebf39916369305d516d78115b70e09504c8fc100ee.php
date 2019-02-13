<?php

/* @Product/ProductPage/Blocks/tabs.html.twig */
class __TwigTemplate_b9f271e94f5e72b335258c1508fc1f69eb63515bebda97f5d695ce639799c2ad extends Twig_Template
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
        echo "<div class=\"tabs js-tabs\">
  <div class=\"arrow d-xl-none js-arrow left-arrow float-left\">
    <i class=\"material-icons hide\">chevron_left</i>
  </div>

  <ul class=\"nav nav-tabs js-nav-tabs\" id=\"form-nav\" role=\"tablist\">
    <li id=\"tab_step1\" class=\"nav-item\"><a href=\"#step1\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link active\">";
        // line 31
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Basic settings", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</a></li>
    <li id=\"tab_step3\" class=\"nav-item\"><a href=\"#step3\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link\">";
        // line 32
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Quantities", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</a></li>
    <li id=\"tab_step4\" class=\"nav-item\"><a href=\"#step4\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link\">";
        // line 33
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Shipping", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</a></li>
    <li id=\"tab_step2\" class=\"nav-item\"><a href=\"#step2\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link\">";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Pricing", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</a></li>
    <li id=\"tab_step5\" class=\"nav-item\"><a href=\"#step5\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link\">";
        // line 35
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("SEO", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</a></li>
    <li id=\"tab_step6\" class=\"nav-item\"><a href=\"#step6\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link\">";
        // line 36
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Options", array(), "Admin.Global"), "html", null, true);
        echo "</a></li>
    ";
        // line 37
        if (($this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->hookCount("displayAdminProductsExtra") > 0)) {
            // line 38
            echo "      <li id=\"tab_hooks\" class=\"nav-item\"><a href=\"#hooks\" role=\"tab\" data-toggle=\"tab\" class=\"nav-link\">";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Modules", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</a></li>
    ";
        }
        // line 40
        echo "  </ul>
  <div class=\"arrow d-xl-none js-arrow right-arrow visible float-right\">
    <i class=\"material-icons hide\">chevron_right</i>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Blocks/tabs.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  59 => 40,  53 => 38,  51 => 37,  47 => 36,  43 => 35,  39 => 34,  35 => 33,  31 => 32,  27 => 31,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Blocks/tabs.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Blocks/tabs.html.twig");
    }
}

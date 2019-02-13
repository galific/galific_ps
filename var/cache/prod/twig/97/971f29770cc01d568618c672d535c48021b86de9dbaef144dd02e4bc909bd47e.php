<?php

/* @Product/ProductPage/Forms/form_feature.html.twig */
class __TwigTemplate_9b2587ab3d99fef2257602afb69d65ad4dfe855db373b83e9d095d91e8d9bac1 extends Twig_Template
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
        echo "<div class=\"row product-feature\">
    <div class=\"col-lg-12 col-xl-4\">
        <fieldset class=\"form-group mb-0\">
            <label class=\"form-control-label\">";
        // line 28
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "feature", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
            ";
        // line 29
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "feature", array()), 'widget');
        echo "
        </fieldset>
    </div>
    <div class=\"col-lg-12 col-xl-4\">
        <fieldset class=\"form-group mb-0\">
            <label class=\"form-control-label\">";
        // line 34
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "value", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
            ";
        // line 35
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "value", array()), 'widget');
        echo "
        </fieldset>
    </div>
    <div class=\"col-lg-11 col-xl-3\">
        <fieldset class=\"form-group mb-0\">
            <label class=\"form-control-label\">";
        // line 40
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["form"] ?? null), "custom_value", array()), "vars", array()), "label", array()), "html", null, true);
        echo "</label>
            ";
        // line 41
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "custom_value", array()), 'widget');
        echo "
        </fieldset>
    </div>
    <div class=\"col-lg-1 col-xl-1\">
        <a class=\"btn tooltip-link delete pl-0 pr-0\"><i class=\"material-icons\">delete</i></a>
    </div>
</div>
<hr class=\"mb-2 d-xl-none\" />
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Forms/form_feature.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  52 => 41,  48 => 40,  40 => 35,  36 => 34,  28 => 29,  24 => 28,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Forms/form_feature.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Forms/form_feature.html.twig");
    }
}

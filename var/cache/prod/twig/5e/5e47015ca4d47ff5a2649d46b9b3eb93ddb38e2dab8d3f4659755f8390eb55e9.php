<?php

/* @PrestaShop/Admin/Module/Includes/action_button.html.twig */
class __TwigTemplate_b5527cb3097329970f90ebd0f500c11666b592316f66e8f6a46729690a03a884 extends Twig_Template
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
        echo "
";
        // line 26
        $context["displayAction"] = $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans(twig_replace_filter(twig_capitalize_string_filter($this->env, ($context["action"] ?? null)), array("_" => " ")), array(), "Admin.Actions");
        // line 27
        echo "
";
        // line 28
        if ((($context["action"] ?? null) == "configure")) {
            // line 29
            echo "
    <a class=\"";
            // line 30
            echo twig_escape_filter($this->env, ($context["classes"] ?? null), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\">
      ";
            // line 31
            echo twig_escape_filter($this->env, ($context["displayAction"] ?? null), "html", null, true);
            echo "
    </a>

";
        } else {
            // line 35
            echo "
    <form class='";
            // line 36
            echo twig_escape_filter($this->env, (((isset($context["classes_form"]) || array_key_exists("classes_form", $context))) ? (_twig_default_filter(($context["classes_form"] ?? null))) : ("")), "html", null, true);
            echo "' method=\"post\" action=\"";
            echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\">
      <button type=\"submit\" class=\"";
            // line 37
            echo twig_escape_filter($this->env, ($context["classes"] ?? null), "html", null, true);
            echo " module_action_menu_";
            echo twig_escape_filter($this->env, ($context["action"] ?? null), "html", null, true);
            echo "\" data-confirm_modal=\"module-modal-confirm-";
            echo twig_escape_filter($this->env, ($context["name"] ?? null), "html", null, true);
            echo "-";
            echo twig_escape_filter($this->env, ($context["action"] ?? null), "html", null, true);
            echo "\">
        ";
            // line 38
            echo twig_escape_filter($this->env, ($context["displayAction"] ?? null), "html", null, true);
            echo "
      </button>
    </form>

";
        }
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/action_button.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  64 => 38,  54 => 37,  48 => 36,  45 => 35,  38 => 31,  32 => 30,  29 => 29,  27 => 28,  24 => 27,  22 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/action_button.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/action_button.html.twig");
    }
}

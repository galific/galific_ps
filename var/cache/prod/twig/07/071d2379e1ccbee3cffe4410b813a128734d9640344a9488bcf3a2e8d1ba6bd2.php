<?php

/* @PrestaShop/Admin/Module/Includes/grid.html.twig */
class __TwigTemplate_41a464a33581defcdbf3cac6ad7f6843362fa16e235bc407d51fd6ac8886da12 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'addon_card' => array($this, 'block_addon_card'),
            'addon_search_card' => array($this, 'block_addon_search_card'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "<div id=\"modules-list-container-";
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\" class=\"row modules-list\" data-name=\"";
        echo twig_escape_filter($this->env, ($context["id"] ?? null), "html", null, true);
        echo "\">
  ";
        // line 26
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["modules"] ?? null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["index"] => $context["module"]) {
            // line 27
            echo "    ";
            $this->displayBlock('addon_card', $context, $blocks);
            // line 30
            echo "  ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['index'], $context['module'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo "
  ";
        // line 32
        $this->displayBlock('addon_search_card', $context, $blocks);
        // line 38
        echo "</div>
";
    }

    // line 27
    public function block_addon_card($context, array $blocks = array())
    {
        // line 28
        echo "      ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/card_list.html.twig", "@PrestaShop/Admin/Module/Includes/grid.html.twig", 28)->display(array_merge($context, array("index" => ($context["index"] ?? null), "module" => ($context["module"] ?? null), "origin" => (((isset($context["origin"]) || array_key_exists("origin", $context))) ? (_twig_default_filter(($context["origin"] ?? null), "none")) : ("none")), "category" => ($context["id"] ?? null))));
        // line 29
        echo "    ";
    }

    // line 32
    public function block_addon_search_card($context, array $blocks = array())
    {
        // line 33
        echo "    ";
        if (((isset($context["requireAddonsSearch"]) || array_key_exists("requireAddonsSearch", $context)) && (($context["requireAddonsSearch"] ?? null) == true))) {
            // line 34
            echo "      ";
            $this->loadTemplate("@PrestaShop/Admin/Module/Includes/card_grid_addons.html.twig", "@PrestaShop/Admin/Module/Includes/grid.html.twig", 34)->display($context);
            // line 35
            echo "      ";
            $this->loadTemplate("@PrestaShop/Admin/Module/Includes/card_list_addons.html.twig", "@PrestaShop/Admin/Module/Includes/grid.html.twig", 35)->display($context);
            // line 36
            echo "    ";
        }
        // line 37
        echo "  ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/grid.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  97 => 37,  94 => 36,  91 => 35,  88 => 34,  85 => 33,  82 => 32,  78 => 29,  75 => 28,  72 => 27,  67 => 38,  65 => 32,  62 => 31,  48 => 30,  45 => 27,  28 => 26,  21 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/grid.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/grid.html.twig");
    }
}

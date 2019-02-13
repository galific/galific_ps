<?php

/* @Product/CatalogPage/Forms/form_edit_dropdown.html.twig */
class __TwigTemplate_557d44bfc1d682525872789505e1f3b515becbc587ecdde9d10cfe032e57e6f5 extends Twig_Template
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
        $context["buttonType"] = (((isset($context["buttonType"]) || array_key_exists("buttonType", $context))) ? (_twig_default_filter(($context["buttonType"] ?? null), "primary")) : ("primary"));
        // line 26
        $context["right"] = (((isset($context["right"]) || array_key_exists("right", $context))) ? (_twig_default_filter(($context["right"] ?? null), false)) : (false));
        // line 27
        echo "
<div class=\"";
        // line 28
        echo twig_escape_filter($this->env, (((isset($context["div_style"]) || array_key_exists("div_style", $context))) ? (_twig_default_filter(($context["div_style"] ?? null), "btn-group")) : ("btn-group")), "html", null, true);
        echo "\">

  ";
        // line 30
        if ((isset($context["default_item"]) || array_key_exists("default_item", $context))) {
            // line 31
            echo "    <a
      href=\"";
            // line 32
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["default_item"] ?? null), "href", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["default_item"] ?? null), "href", array()), "#")) : ("#")), "html", null, true);
            echo "\"
      title=\"";
            // line 33
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["default_item"] ?? null), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["default_item"] ?? null), "title", array()), (($this->getAttribute(($context["default_item"] ?? null), "label", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["default_item"] ?? null), "label", array()), "")) : ("")))) : ((($this->getAttribute(($context["default_item"] ?? null), "label", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["default_item"] ?? null), "label", array()), "")) : ("")))), "html", null, true);
            echo "\"
      class=\"btn tooltip-link product-edit\"
      ";
            // line 35
            if (((isset($context["disabled"]) || array_key_exists("disabled", $context)) && (($context["disabled"] ?? null) == true))) {
                echo "disabled=\"disabled\"";
            }
            // line 36
            echo "    >
      ";
            // line 37
            if ($this->getAttribute(($context["default_item"] ?? null), "icon", array())) {
                // line 38
                echo "        <i class=\"material-icons\">";
                echo twig_escape_filter($this->env, $this->getAttribute(($context["default_item"] ?? null), "icon", array()), "html", null, true);
                echo "</i>
      ";
            }
            // line 40
            echo "      ";
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["default_item"] ?? null), "label", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["default_item"] ?? null), "label", array()), "")) : ("")), "html", null, true);
            echo "
    </a>
  ";
        }
        // line 43
        echo "
  <button class=\"btn btn-link dropdown-toggle dropdown-toggle-split product-edit\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\">
    ";
        // line 45
        echo twig_escape_filter($this->env, (((isset($context["menu_label"]) || array_key_exists("menu_label", $context))) ? (_twig_default_filter(($context["menu_label"] ?? null), "")) : ("")), "html", null, true);
        echo "
  </button>

  <div class=\"dropdown-menu";
        // line 48
        if (($context["right"] ?? null)) {
            echo " dropdown-menu-right";
        }
        echo "\" x-placement=\"bottom-start\" >
    ";
        // line 49
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["items"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["entry"]) {
            // line 50
            echo "      ";
            if (($this->getAttribute($context["entry"], "divider", array(), "any", true, true) && ($this->getAttribute($context["entry"], "divider", array()) == true))) {
                // line 51
                echo "        <div class=\"dropdown-divider\"></div>
      ";
            } else {
                // line 53
                echo "        <a
          class=\"dropdown-item product-edit\" href=\"";
                // line 54
                echo twig_escape_filter($this->env, (($this->getAttribute($context["entry"], "href", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($context["entry"], "href", array()), "#")) : ("#")), "html", null, true);
                echo "\"
          ";
                // line 55
                if ($this->getAttribute($context["entry"], "onclick", array(), "any", true, true)) {
                    echo "onclick=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["entry"], "onclick", array()), "html", null, true);
                    echo "\"";
                }
                // line 56
                echo "          ";
                if ($this->getAttribute($context["entry"], "target", array(), "any", true, true)) {
                    echo "target=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["entry"], "target", array()), "html", null, true);
                    echo "\"";
                }
                // line 57
                echo "        >
          ";
                // line 58
                if ($this->getAttribute($context["entry"], "icon", array())) {
                    // line 59
                    echo "            <i class=\"material-icons\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["entry"], "icon", array()), "html", null, true);
                    echo "</i>
          ";
                }
                // line 61
                echo "          ";
                echo twig_escape_filter($this->env, (($this->getAttribute($context["entry"], "label", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($context["entry"], "label", array()), "")) : ("")), "html", null, true);
                echo "
        </a>
      ";
            }
            // line 64
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['entry'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 65
        echo "  </div>

</div>
";
    }

    public function getTemplateName()
    {
        return "@Product/CatalogPage/Forms/form_edit_dropdown.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  138 => 65,  132 => 64,  125 => 61,  119 => 59,  117 => 58,  114 => 57,  107 => 56,  101 => 55,  97 => 54,  94 => 53,  90 => 51,  87 => 50,  83 => 49,  77 => 48,  71 => 45,  67 => 43,  60 => 40,  54 => 38,  52 => 37,  49 => 36,  45 => 35,  40 => 33,  36 => 32,  33 => 31,  31 => 30,  26 => 28,  23 => 27,  21 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/CatalogPage/Forms/form_edit_dropdown.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/Forms/form_edit_dropdown.html.twig");
    }
}

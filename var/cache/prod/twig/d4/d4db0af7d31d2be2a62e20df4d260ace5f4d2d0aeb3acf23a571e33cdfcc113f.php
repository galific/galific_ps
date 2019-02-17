<?php

/* @PrestaShop/Admin/Module/manage.html.twig */
class __TwigTemplate_4d1b7cc5337f6bd693a1f55a4ebd4d5bda7e0ebffd895a5301f83fbc181d39e4 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Module/common.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 25);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'catalog_categories_listing' => array($this, 'block_catalog_categories_listing'),
            'addon_card_see_more' => array($this, 'block_addon_card_see_more'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Module/common.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 66
        $context["js_translatable"] = twig_array_merge(array("Bulk Action - One module minimum" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You need to select at least one module to use the bulk action.", array(), "Admin.Modules.Notification"), "Bulk Action - Request not found" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The action \"[1]\" is not available, impossible to perform your request.", array(), "Admin.Modules.Notification"), "Bulk Action - Request not available for module" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The action [1] is not available for module [2]. Skipped.", array(), "Admin.Modules.Notification")),         // line 70
($context["js_translatable"] ?? null));
        // line 25
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 27
    public function block_content($context, array $blocks = array())
    {
        // line 28
        echo "  <div class=\"row justify-content-center\">
    <div class=\"col-lg-10\">
      ";
        // line 31
        echo "      ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/modal_confirm_bulk_action.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 31)->display($context);
        // line 32
        echo "      ";
        // line 33
        echo "      ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/modal_confirm_prestatrust.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 33)->display($context);
        // line 34
        echo "      ";
        // line 35
        echo "      ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/modal_addons_connect.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 35)->display(array_merge($context, array("level" => ($context["level"] ?? null), "errorMessage" => ($context["errorMessage"] ?? null))));
        // line 36
        echo "      ";
        // line 37
        echo "      ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/modal_import.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 37)->display(array_merge($context, array("level" => ($context["level"] ?? null), "errorMessage" => ($context["errorMessage"] ?? null))));
        // line 38
        echo "      ";
        // line 39
        echo "      ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/menu_top.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 39)->display(array_merge($context, array("topMenuData" => ($context["topMenuData"] ?? null), "bulkActions" => ($context["bulkActions"] ?? null))));
        // line 40
        echo "
      ";
        // line 41
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/grid_manage_recently_used.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 41)->display(array_merge($context, array("display_type" => "list", "origin" => "manage")));
        // line 42
        echo "
      ";
        // line 43
        $this->displayBlock('catalog_categories_listing', $context, $blocks);
        // line 62
        echo "    </div>
  </div>
";
    }

    // line 43
    public function block_catalog_categories_listing($context, array $blocks = array())
    {
        // line 44
        echo "        ";
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute(($context["categories"] ?? null), "subMenu", array()));
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
        foreach ($context['_seq'] as $context["_key"] => $context["category"]) {
            // line 45
            echo "          <div class=\"module-short-list\">
            <span id=\"";
            // line 46
            echo twig_escape_filter($this->env, $this->getAttribute($context["category"], "refMenu", array()), "html", null, true);
            echo "_modules\" class=\"module-search-result-title\">";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans($this->getAttribute($context["category"], "name", array()), array(), "Admin.Modules.Feature"), "html", null, true);
            echo "</span>

            ";
            // line 48
            if (twig_test_empty($this->getAttribute($context["category"], "modules", array()))) {
                // line 49
                echo "              ";
                $this->loadTemplate("@PrestaShop/Admin/Module/Includes/grid_manage_empty.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 49)->display(array_merge($context, array("category" => $context["category"], "display_type" => "list", "origin" => "manage")));
                // line 50
                echo "            ";
            } else {
                // line 51
                echo "              ";
                $this->loadTemplate("@PrestaShop/Admin/Module/Includes/grid_manage_installed.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 51)->display(array_merge($context, array("modules" => $this->getAttribute($context["category"], "modules", array()), "display_type" => "list", "origin" => "manage", "id" => $this->getAttribute($context["category"], "refMenu", array()))));
                // line 52
                echo "
              ";
                // line 53
                $this->displayBlock('addon_card_see_more', $context, $blocks);
                // line 58
                echo "            ";
            }
            // line 59
            echo "          </div>
        ";
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
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['category'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 61
        echo "      ";
    }

    // line 53
    public function block_addon_card_see_more($context, array $blocks = array())
    {
        // line 54
        echo "                ";
        if ((twig_length_filter($this->env, $this->getAttribute(($context["category"] ?? null), "modules", array())) > ($context["maxModulesDisplayed"] ?? null))) {
            // line 55
            echo "                  ";
            $this->loadTemplate("@PrestaShop/Admin/Module/Includes/see_more.html.twig", "@PrestaShop/Admin/Module/manage.html.twig", 55)->display(array_merge($context, array("id" => $this->getAttribute(($context["category"] ?? null), "refMenu", array()))));
            // line 56
            echo "                ";
        }
        // line 57
        echo "              ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/manage.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  161 => 57,  158 => 56,  155 => 55,  152 => 54,  149 => 53,  145 => 61,  130 => 59,  127 => 58,  125 => 53,  122 => 52,  119 => 51,  116 => 50,  113 => 49,  111 => 48,  104 => 46,  101 => 45,  83 => 44,  80 => 43,  74 => 62,  72 => 43,  69 => 42,  67 => 41,  64 => 40,  61 => 39,  59 => 38,  56 => 37,  54 => 36,  51 => 35,  49 => 34,  46 => 33,  44 => 32,  41 => 31,  37 => 28,  34 => 27,  30 => 25,  28 => 70,  27 => 66,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/manage.html.twig", "C:\\wamp64\\www\\galific\\src\\PrestaShopBundle\\Resources\\views\\Admin\\Module\\manage.html.twig");
    }
}

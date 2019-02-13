<?php

/* @PrestaShop/Admin/Module/Includes/card_list.html.twig */
class __TwigTemplate_382b1f2f1e9a9aa2b4ecff19a4b28894073e633fbfd4b3bd2c702fc9327f3af6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'addon_version' => array($this, 'block_addon_version'),
            'addon_description' => array($this, 'block_addon_description'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        $context["isModuleActive"] = (($this->getAttribute($this->getAttribute(($context["module"] ?? null), "database", array(), "any", false, true), "active", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute(($context["module"] ?? null), "database", array(), "any", false, true), "active", array()), "0")) : ("0"));
        // line 26
        echo "
<div
  class=\"module-item module-item-list col-md-12 ";
        // line 28
        if (((($context["origin"] ?? null) == "manage") && (($context["isModuleActive"] ?? null) == "0"))) {
            echo "module-item-list-isNotActive";
        }
        echo "\"
  data-id=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "id", array()), "html", null, true);
        echo "\"
  data-name=\"";
        // line 30
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array()), "html", null, true);
        echo "\"
  data-scoring=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "avgRate", array()), "html", null, true);
        echo "\"
  data-logo=\"";
        // line 32
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "img", array()), "html", null, true);
        echo "\"
  data-author=\"";
        // line 33
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "author", array()), "html", null, true);
        echo "\"
  data-version=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "version", array()), "html", null, true);
        echo "\"
  data-description=\"";
        // line 35
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "description", array()), "html", null, true);
        echo "\"
  data-tech-name=\"";
        // line 36
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
        echo "\"
  data-child-categories=\"";
        // line 37
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "categoryName", array()), "html", null, true);
        echo "\"
  data-categories=\"";
        // line 38
        echo twig_escape_filter($this->env, ($context["category"] ?? null), "html", null, true);
        echo "\"
  data-type=\"";
        // line 39
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "productType", array()), "html", null, true);
        echo "\"
  data-price=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "price", array()), "raw", array()), "html", null, true);
        echo "\"
  data-active=\"";
        // line 41
        echo twig_escape_filter($this->env, ($context["isModuleActive"] ?? null), "html", null, true);
        echo "\"
  data-last-access=\"";
        // line 42
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "database", array()), "last_access_date", array()), "html", null, true);
        echo "\"
>
  <div class=\"container-fluid\">
    <div class=\"module-item-wrapper-list row\">
      <div class=\"col-sm-12 col-md-12 col-lg-1 text-sm-center\">
        <div class=\"module-logo-thumb-list\">
          <img src=\"";
        // line 48
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "img", array()), "html", null, true);
        echo "\" class=\"text-md-center\" alt=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array()), "html", null, true);
        echo "\"/>
        </div>
      </div>
      <div class=\"col-md-12 col-lg-11 row\">
        <div class=\"col-sm-12 col-md-10 col-lg-11\">
          <h3
            class=\"text-ellipsis module-name-list\"
            data-toggle=\"pstooltip\"
            data-placement=\"top\"
            title=\"";
        // line 57
        echo $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array());
        echo "\"
          >
            ";
        // line 59
        if ($this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array())) {
            // line 60
            echo "              ";
            echo $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array());
            echo "

            ";
        } else {
            // line 63
            echo "              ";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
            echo "
            ";
        }
        // line 65
        echo "            <span class=\"h5\">
                ";
        // line 66
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "picos", array()));
        foreach ($context['_seq'] as $context["_key"] => $context["pico"]) {
            // line 67
            echo "                    <span class=\"";
            echo twig_escape_filter($this->env, (($this->getAttribute($context["pico"], "class", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($context["pico"], "class", array()), "")) : ("")), "html", null, true);
            echo "\"><img src=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["pico"], "img", array()), "html", null, true);
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["pico"], "label", array()), "html", null, true);
            echo "\" /> ";
            echo twig_escape_filter($this->env, $this->getAttribute($context["pico"], "text", array()), "html", null, true);
            echo "</span>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['pico'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 69
        echo "            </span>
          </h3>
        </div>
        <div class=\"col-sm-12 col-md-2\">
          <span class=\"text-ellipsis small-text\">
            ";
        // line 74
        $this->displayBlock('addon_version', $context, $blocks);
        // line 81
        echo "          </span>
        </div>
        <div class=\"col-sm-12 col-md-8 col-lg-7\">
          ";
        // line 84
        $this->displayBlock('addon_description', $context, $blocks);
        // line 95
        echo "        </div>
        <div class=\"col-sm-12 col-md-12 col-lg-3 text-md-right\">
          ";
        // line 97
        if (((isset($context["requireBulkActions"]) || array_key_exists("requireBulkActions", $context)) && (($context["requireBulkActions"] ?? null) == true))) {
            // line 98
            echo "            <div class=\"module-checkbox-bulk-list md-checkbox\">
              <label>
                <input type=\"checkbox\" data-name=\"";
            // line 100
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array()), "html", null, true);
            echo "\" data-tech-name=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
            echo "\" />
                <i class=\"md-checkbox-control\"></i>
              </label>
            </div>
          ";
        }
        // line 105
        echo "          ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/action_menu.html.twig", "@PrestaShop/Admin/Module/Includes/card_list.html.twig", 105)->display(array_merge($context, array("module" => ($context["module"] ?? null))));
        // line 106
        echo "        </div>
        ";
        // line 107
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/modal_read_more.html.twig", "@PrestaShop/Admin/Module/Includes/card_list.html.twig", 107)->display(array_merge($context, array("module" => ($context["module"] ?? null), "additionalModalSuffix" => (((isset($context["additionalModalSuffix"]) || array_key_exists("additionalModalSuffix", $context))) ? (_twig_default_filter(($context["additionalModalSuffix"] ?? null), "")) : ("")))));
        // line 108
        echo "        ";
        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/modal_confirm.html.twig", "@PrestaShop/Admin/Module/Includes/card_list.html.twig", 108)->display(array_merge($context, array("module" => ($context["module"] ?? null))));
        // line 109
        echo "      </div>
    </div>
  </div>
</div>
";
    }

    // line 74
    public function block_addon_version($context, array $blocks = array())
    {
        // line 75
        echo "              ";
        if (($this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "productType", array()) == "service")) {
            // line 76
            echo "                ";
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Service by %author%", array("%author%" => (("<b>" . $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "author", array())) . "</b>")), "Admin.Modules.Feature");
            echo "
              ";
        } else {
            // line 78
            echo "                ";
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("v%version% - by %author%", array("%version%" => $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "version", array()), "%author%" => (("<b>" . $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "author", array())) . "</b>")), "Admin.Modules.Feature");
            echo "
              ";
        }
        // line 80
        echo "            ";
    }

    // line 84
    public function block_addon_description($context, array $blocks = array())
    {
        // line 85
        echo "            ";
        echo $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "description", array());
        echo "
            ";
        // line 86
        if (((twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "description", array())) > 0) && (twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "description", array())) < twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "fullDescription", array()))))) {
            // line 87
            echo "              ...
            ";
        }
        // line 89
        echo "            <span>
              ";
        // line 90
        if (($this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "id", array()) != "0")) {
            // line 91
            echo "                <a class=\"module-read-more-list-btn url\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_module_cart", array("moduleId" => $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "id", array()))), "html", null, true);
            echo "\" data-target=\"#module-modal-read-more-";
            echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
            echo twig_escape_filter($this->env, (((isset($context["additionalModalSuffix"]) || array_key_exists("additionalModalSuffix", $context))) ? (_twig_default_filter(($context["additionalModalSuffix"] ?? null), "")) : ("")), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Read More", array(), "Admin.Modules.Feature"), "html", null, true);
            echo "</a>
              ";
        }
        // line 93
        echo "            </span>
          ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/card_list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  257 => 93,  246 => 91,  244 => 90,  241 => 89,  237 => 87,  235 => 86,  230 => 85,  227 => 84,  223 => 80,  217 => 78,  211 => 76,  208 => 75,  205 => 74,  197 => 109,  194 => 108,  192 => 107,  189 => 106,  186 => 105,  176 => 100,  172 => 98,  170 => 97,  166 => 95,  164 => 84,  159 => 81,  157 => 74,  150 => 69,  135 => 67,  131 => 66,  128 => 65,  122 => 63,  115 => 60,  113 => 59,  108 => 57,  94 => 48,  85 => 42,  81 => 41,  77 => 40,  73 => 39,  69 => 38,  65 => 37,  61 => 36,  57 => 35,  53 => 34,  49 => 33,  45 => 32,  41 => 31,  37 => 30,  33 => 29,  27 => 28,  23 => 26,  21 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/card_list.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/card_list.html.twig");
    }
}

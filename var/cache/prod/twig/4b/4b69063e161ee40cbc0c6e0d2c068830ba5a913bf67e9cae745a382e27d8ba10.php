<?php

/* @PrestaShop/Admin/Module/Includes/action_menu.html.twig */
class __TwigTemplate_fe9c0ddd29271dc9cc65fec2f0228cd4c23e8d52426368112bd9dadff8852798 extends Twig_Template
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
        list($context["url"], $context["priceRaw"], $context["priceDisplay"], $context["url_active"], $context["urls"], $context["name"]) =         array($this->getAttribute($this->getAttribute(        // line 26
($context["module"] ?? null), "attributes", array()), "url", array()), $this->getAttribute($this->getAttribute($this->getAttribute(        // line 27
($context["module"] ?? null), "attributes", array()), "price", array()), "raw", array()), $this->getAttribute($this->getAttribute($this->getAttribute(        // line 28
($context["module"] ?? null), "attributes", array()), "price", array()), "displayPrice", array()), $this->getAttribute($this->getAttribute(        // line 29
($context["module"] ?? null), "attributes", array()), "url_active", array()), $this->getAttribute($this->getAttribute(        // line 30
($context["module"] ?? null), "attributes", array()), "urls", array()), $this->getAttribute($this->getAttribute(        // line 31
($context["module"] ?? null), "attributes", array()), "name", array()));
        // line 33
        echo " <div class=\"btn-group module-actions\"> 
  ";
        // line 34
        if ((($context["url_active"] ?? null) == "buy")) {
            // line 35
            echo "    <a class=\"btn btn-primary btn-primary-reverse btn-block btn-outline-primary module_action_menu_go_to_addons\" href=\"";
            echo twig_escape_filter($this->env, ($context["url"] ?? null), "html", null, true);
            echo "\" target=\"_blank\">
      ";
            // line 36
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Discover", array(), "Admin.Modules.Feature"), "html", null, true);
            echo "
    </a>
  ";
        } elseif (twig_length_filter($this->env,         // line 38
($context["urls"] ?? null))) {
            // line 39
            echo "    ";
            $this->loadTemplate("@PrestaShop/Admin/Module/Includes/action_button.html.twig", "@PrestaShop/Admin/Module/Includes/action_menu.html.twig", 39)->display(array_merge($context, array("name" =>             // line 40
($context["name"] ?? null), "classes_form" => "btn-group form-action-button", "classes" => "btn btn-primary-reverse btn-outline-primary", "url" => $this->getAttribute(            // line 43
($context["urls"] ?? null), ($context["url_active"] ?? null), array(), "array"), "action" =>             // line 44
($context["url_active"] ?? null))));
            // line 46
            echo "    ";
            if ((twig_length_filter($this->env, ($context["urls"] ?? null)) > 1)) {
                // line 47
                echo "        <input type=\"hidden\" class=\"btn\" /> 
        <button type=\"button\" class=\"btn btn-outline-primary dropdown-toggle dropdown-toggle-split\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"false\"> 
          <span class=\"caret\"></span> 
        </button>
        <span class=\"sr-only\">";
                // line 51
                echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Toggle Dropdown", array(), "Admin.Modules.Feature"), "html", null, true);
                echo "</span> 
        <div class=\"dropdown-menu\">
          ";
                // line 53
                $context['_parent'] = $context;
                $context['_seq'] = twig_ensure_traversable(($context["urls"] ?? null));
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
                foreach ($context['_seq'] as $context["module_action"] => $context["module_url"]) {
                    // line 54
                    echo "            ";
                    if (($context["module_action"] != ($context["url_active"] ?? null))) {
                        // line 55
                        echo "                <li>
                  ";
                        // line 56
                        $this->loadTemplate("@PrestaShop/Admin/Module/Includes/action_button.html.twig", "@PrestaShop/Admin/Module/Includes/action_menu.html.twig", 56)->display(array_merge($context, array("name" =>                         // line 57
($context["name"] ?? null), "classes" => "dropdown-item", "url" =>                         // line 59
$context["module_url"], "action" =>                         // line 60
$context["module_action"])));
                        // line 62
                        echo "                </li>
            ";
                    }
                    // line 64
                    echo "          ";
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
                unset($context['_seq'], $context['_iterated'], $context['module_action'], $context['module_url'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 65
                echo "        </div>
    ";
            }
            // line 67
            echo "  ";
        }
        // line 68
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/action_menu.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  117 => 68,  114 => 67,  110 => 65,  96 => 64,  92 => 62,  90 => 60,  89 => 59,  88 => 57,  87 => 56,  84 => 55,  81 => 54,  64 => 53,  59 => 51,  53 => 47,  50 => 46,  48 => 44,  47 => 43,  46 => 40,  44 => 39,  42 => 38,  37 => 36,  32 => 35,  30 => 34,  27 => 33,  25 => 31,  24 => 30,  23 => 29,  22 => 28,  21 => 27,  20 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/action_menu.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/action_menu.html.twig");
    }
}

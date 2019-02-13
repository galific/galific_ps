<?php

/* @Product/ProductPage/Blocks/header.html.twig */
class __TwigTemplate_7d206549a788d467d42c06ba5e5ae030597d6a6a791a7fe546999ad6ed64740e extends Twig_Template
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
        echo "<div class=\"product-header col-md-12\">
  <div class=\"row justify-content-md-center\">
  ";
        // line 27
        if (($context["is_multishop_context"] ?? null)) {
            // line 28
            echo "    <div class=\"col-xxl-10\">
      <div class=\"alert alert-warning\" role=\"alert\">
        <p class=\"alert-text\">";
            // line 30
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You are in a multistore context: any modification will impact all your shops, or each shop of the active group.", array(), "Admin.Catalog.Notification"), "html", null, true);
            echo "</p>
      </div>
    </div>
  ";
        }
        // line 34
        echo "
    <div class=\"col-xxl-10\">
      <div class=\"row\">
        <div class=\"col-md-7 big-input ";
        // line 37
        echo ((($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_FORCE_FRIENDLY_PRODUCT") == 1)) ? ("friendly-url-force-update") : (""));
        echo "\" id=\"form_step1_names\">
          ";
        // line 38
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formName"] ?? null), 'widget');
        echo "
        </div>
        <div class=\"col-sm-7 col-md-2 form_step1_type_product\">
          ";
        // line 41
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formType"] ?? null), 'widget');
        echo "
          <span class=\"help-box pull-xs-right\" data-toggle=\"popover\"
            data-content=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Is the product a pack (a combination of at least two existing products), a virtual product (downloadable file, service, etc.), or simply a standard, physical product?", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"></span>
        </div>
        <div class=\"col-sm-2 col-md-1 form_switch_language\">
          <div class=\"";
        // line 46
        echo (((twig_length_filter($this->env, ($context["languages"] ?? null)) == 1)) ? ("hide") : (""));
        echo "\">
            <select id=\"form_switch_language\" class=\"custom-select\">
              ";
        // line 48
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["languages"] ?? null));
        foreach ($context['_seq'] as $context["_key"] => $context["language"]) {
            // line 49
            echo "                <option value=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "iso_code", array()), "html", null, true);
            echo "\" ";
            if ((($context["default_language_iso"] ?? null) == $this->getAttribute($context["language"], "iso_code", array()))) {
                echo "selected=\"selected\"";
            }
            echo ">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["language"], "iso_code", array()), "html", null, true);
            echo "</option>
              ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['language'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 51
        echo "            </select>
          </div>
        </div>
        <div class=\"toolbar col-sm-3 col-md-2 text-md-right\">
          <a class=\"toolbar-button btn-sales\" href=\"";
        // line 55
        echo twig_escape_filter($this->env, ($context["stats_link"] ?? null), "html", null, true);
        echo "\" target=\"_blank\" title=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Sales", array(), "Admin.Global"), "html", null, true);
        echo "\"
            id=\"product_form_go_to_sales\">
            <i class=\"material-icons\">assessment</i>
            <span class=\"title\">";
        // line 58
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Sales", array(), "Admin.Global"), "html", null, true);
        echo "</span>
          </a>

          <a
            class=\"toolbar-button btn-quicknav btn-sidebar\"
            href=\"#\"
            title=\"";
        // line 64
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Quick navigation", array(), "Admin.Global"), "html", null, true);
        echo "\"
            data-toggle=\"sidebar\"
            data-target=\"#right-sidebar\"
            data-url=\"";
        // line 67
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_list", array("limit" => "last", "offset" => "last", "view" => "quicknav")), "html", null, true);
        echo "\"
            id=\"product_form_open_quicknav\"
          >
            <i class=\"material-icons\">list</i>
            <span class=\"title\">";
        // line 71
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Product list", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "</span>
          </a>

          <a class=\"toolbar-button btn-help btn-sidebar\" href=\"#\"
            title=\"";
        // line 75
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Help", array(), "Admin.Global"), "html", null, true);
        echo "\"
            data-toggle=\"sidebar\"
            data-target=\"#right-sidebar\"
            data-url=\"";
        // line 78
        echo twig_escape_filter($this->env, ($context["help_link"] ?? null), "html", null, true);
        echo "\"
            id=\"product_form_open_help\"
          >
            <i class=\"material-icons\">help</i>
            <span class=\"title\">";
        // line 82
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Help", array(), "Admin.Global"), "html", null, true);
        echo "</span>
          </a>
        </div>
      </div>
      <div class=\"row\">
        <div class=\"col-lg-12\">
          ";
        // line 88
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formName"] ?? null), 'errors');
        echo "
          ";
        // line 89
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["formType"] ?? null), 'errors');
        echo "
        </div>
      </div>
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Blocks/header.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  155 => 89,  151 => 88,  142 => 82,  135 => 78,  129 => 75,  122 => 71,  115 => 67,  109 => 64,  100 => 58,  92 => 55,  86 => 51,  71 => 49,  67 => 48,  62 => 46,  56 => 43,  51 => 41,  45 => 38,  41 => 37,  36 => 34,  29 => 30,  25 => 28,  23 => 27,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Blocks/header.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Blocks/header.html.twig");
    }
}

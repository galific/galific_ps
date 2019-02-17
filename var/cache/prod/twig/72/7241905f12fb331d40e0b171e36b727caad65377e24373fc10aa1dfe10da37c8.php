<?php

/* @PrestaShop/Admin/Improve/International/Localization/index.html.twig */
class __TwigTemplate_c320c290b87d1c4a51b9169f9d7371a3603036d2201c6a2d9d16aa1d775cdd8f extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 26
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/layout.html.twig", "@PrestaShop/Admin/Improve/International/Localization/index.html.twig", 26);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'localization_settings_form_rest' => array($this, 'block_localization_settings_form_rest'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 29
        list($context["configurationForm"], $context["localUnitsForm"], $context["advancedForm"]) =         array($this->getAttribute(($context["localizationForm"] ?? null), "configuration", array()), $this->getAttribute(($context["localizationForm"] ?? null), "local_units", array()), $this->getAttribute(($context["localizationForm"] ?? null), "advanced", array()));
        // line 26
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 31
    public function block_content($context, array $blocks = array())
    {
        // line 32
        echo "  <div class=\"row justify-content-center\">
    <div class=\"col-xl-10\">
      ";
        // line 34
        $this->loadTemplate("@PrestaShop/Admin/Improve/International/Localization/Blocks/import_localization_pack_block.html.twig", "@PrestaShop/Admin/Improve/International/Localization/index.html.twig", 34)->display($context);
        // line 35
        echo "    </div>
  </div>

  ";
        // line 38
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock(($context["localizationForm"] ?? null), 'form_start', array("action" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_localization_save_options")));
        echo "
    <div class=\"row justify-content-center\">
      <div class=\"col-xl-10\">
        ";
        // line 41
        $this->loadTemplate("@PrestaShop/Admin/Improve/International/Localization/Blocks/configuration.html.twig", "@PrestaShop/Admin/Improve/International/Localization/index.html.twig", 41)->display($context);
        // line 42
        echo "      </div>
      <div class=\"col-xl-10\">
        ";
        // line 44
        $this->loadTemplate("@PrestaShop/Admin/Improve/International/Localization/Blocks/local_units.html.twig", "@PrestaShop/Admin/Improve/International/Localization/index.html.twig", 44)->display($context);
        // line 45
        echo "      </div>
      <div class=\"col-xl-10\">
        ";
        // line 47
        $this->loadTemplate("@PrestaShop/Admin/Improve/International/Localization/Blocks/advanced_configuration.html.twig", "@PrestaShop/Admin/Improve/International/Localization/index.html.twig", 47)->display($context);
        // line 48
        echo "      </div>

      ";
        // line 50
        $this->displayBlock('localization_settings_form_rest', $context, $blocks);
        // line 53
        echo "    </div>
  ";
        // line 54
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock(($context["localizationForm"] ?? null), 'form_end');
        echo "
";
    }

    // line 50
    public function block_localization_settings_form_rest($context, array $blocks = array())
    {
        // line 51
        echo "        ";
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["localizationForm"] ?? null), 'rest');
        echo "
      ";
    }

    // line 57
    public function block_javascripts($context, array $blocks = array())
    {
        // line 58
        echo "  ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "

  <script src=\"";
        // line 60
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/new-theme/public/localization.bundle.js"), "html", null, true);
        echo "\"></script>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Improve/International/Localization/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  101 => 60,  95 => 58,  92 => 57,  85 => 51,  82 => 50,  76 => 54,  73 => 53,  71 => 50,  67 => 48,  65 => 47,  61 => 45,  59 => 44,  55 => 42,  53 => 41,  47 => 38,  42 => 35,  40 => 34,  36 => 32,  33 => 31,  29 => 26,  27 => 29,  11 => 26,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Improve/International/Localization/index.html.twig", "C:\\wamp64\\www\\galific\\src\\PrestaShopBundle\\Resources\\views\\Admin\\Improve\\International\\Localization\\index.html.twig");
    }
}

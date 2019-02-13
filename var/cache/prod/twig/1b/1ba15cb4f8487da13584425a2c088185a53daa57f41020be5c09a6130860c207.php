<?php

/* @PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig */
class __TwigTemplate_fe877979fa0cfc9d3522b23b7dab8df85f7eb1c3ab1ffe7338d1d11dafc4b6f6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/layout.html.twig", "@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig", 25);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'preferences_form_general' => array($this, 'block_preferences_form_general'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 27
        $context["ps"] = $this->loadTemplate("@PrestaShop/Admin/macros.html.twig", "@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig", 27);
        // line 30
        $context["generalForm"] = $this->getAttribute(($context["form"] ?? null), "general", array());
        // line 25
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 33
    public function block_content($context, array $blocks = array())
    {
        // line 34
        echo "    ";
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock(($context["form"] ?? null), 'form_start', array("attr" => array("class" => "form", "id" => "configuration_form")));
        echo "
    <div class=\"row justify-content-center\">
        ";
        // line 36
        $this->displayBlock('preferences_form_general', $context, $blocks);
        // line 178
        echo "
    </div>
    ";
        // line 180
        echo         $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->renderBlock(($context["form"] ?? null), 'form_end');
        echo "
";
    }

    // line 36
    public function block_preferences_form_general($context, array $blocks = array())
    {
        // line 37
        echo "            <div class=\"col-xl-10\">
                <div class=\"card\">
                    <h3 class=\"card-header\">
                        <i class=\"material-icons\">settings</i> ";
        // line 40
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("General", array(), "Admin.Global"), "html", null, true);
        echo "
                    </h3>
                    <div class=\"card-block row\">
                        <div class=\"card-text\">
                            <div class=\"form-group row\">
                                ";
        // line 45
        echo $context["ps"]->getlabel_with_help($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable SSL", array(), "Admin.Shopparameters.Feature"), $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("If you want to enable SSL on all the pages of your shop, activate the \"Enable on all the pages\" option below.", array(), "Admin.Shopparameters.Help"));
        // line 48
        echo "
                                ";
        // line 49
        if ($this->getAttribute($this->getAttribute(($context["app"] ?? null), "request", array()), "isSecure", array(), "method")) {
            // line 50
            echo "                                    <div class=\"col-sm\">
                                        ";
            // line 51
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "enable_ssl", array()), 'errors');
            echo "
                                        ";
            // line 52
            echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "enable_ssl", array()), 'widget');
            echo "
                                        <small class=\"form-text\">";
            // line 53
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("If you own an SSL certificate for your shop's domain name, you can activate SSL encryption (https://) for customer account identification and order processing.", array(), "Admin.Shopparameters.Help"), "html", null, true);
            echo "</small>
                                    </div>
                                ";
        } else {
            // line 56
            echo "                                    <div class=\"col-sm\">
                                        <div class=\"form-control-plaintext\">
                                            <a class=\"d-block\" href=\"";
            // line 58
            echo twig_escape_filter($this->env, ($context["sslUri"] ?? null), "html", null, true);
            echo "\">
                                                ";
            // line 59
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Please click here to check if your shop supports HTTPS.", array(), "Admin.Shopparameters.Feature"), "html", null, true);
            echo "
                                            </a>
                                        </div>
                                    </div>
                                ";
        }
        // line 64
        echo "                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 66
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable SSL on all pages", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 68
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "enable_ssl_everywhere", array()), 'errors');
        echo "
                                    ";
        // line 69
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "enable_ssl_everywhere", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("When enabled, all the pages of your shop will be SSL-secured.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 74
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Increase front office security", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 76
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "enable_token", array()), 'errors');
        echo "
                                    ";
        // line 77
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "enable_token", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 78
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable or disable token in the Front Office to improve PrestaShop's security.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>

                                    ";
        // line 80
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["generalForm"] ?? null), "enable_token", array()), "vars", array()), "disabled", array())) {
            // line 81
            echo "                                      <div class=\"alert alert-warning mt-2 mb-0\" role=\"alert\">
                                        <p class=\"alert-text\">
                                          ";
            // line 83
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can't change the value of this configuration field in the context of this shop.", array(), "Admin.Notifications.Warning"), "html", null, true);
            echo "
                                        </p>
                                      </div>
                                    ";
        }
        // line 87
        echo "                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 90
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Allow iframes on HTML fields", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 92
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "allow_html_iframes", array()), 'errors');
        echo "
                                    ";
        // line 93
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "allow_html_iframes", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 94
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Allow iframes on text fields like product description. We recommend that you leave this option disabled.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 98
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Use HTMLPurifier Library", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 100
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "use_htmlpurifier", array()), 'errors');
        echo "
                                    ";
        // line 101
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "use_htmlpurifier", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 102
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Clean the HTML content on text fields. We recommend that you leave this option enabled.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 106
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Round mode", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 108
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "price_round_mode", array()), 'errors');
        echo "
                                    ";
        // line 109
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "price_round_mode", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 110
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can choose among 6 different ways of rounding prices. \"Round up away from zero ...\" is the recommended behavior.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 114
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Round type", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 116
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "price_round_type", array()), 'errors');
        echo "
                                    ";
        // line 117
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "price_round_type", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 118
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can choose when to round prices: either on each item, each line or the total (of an invoice, for example).", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 122
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Number of decimals", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 124
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "price_display_precision", array()), 'errors');
        echo "
                                    ";
        // line 125
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "price_display_precision", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 126
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Choose how many decimals you want to display", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 130
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Display brands and suppliers", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 132
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "display_suppliers", array()), 'errors');
        echo "
                                    ";
        // line 133
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "display_suppliers", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 134
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable brands and suppliers pages on your front office even when their respective modules are disabled.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 138
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Display best sellers", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 140
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "display_best_sellers", array()), 'errors');
        echo "
                                    ";
        // line 141
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "display_best_sellers", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 142
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable best sellers page on your front office even when its respective module is disabled.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>
                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 146
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable Multistore", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 148
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "multishop_feature_active", array()), 'errors');
        echo "
                                    ";
        // line 149
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "multishop_feature_active", array()), 'widget');
        echo "
                                    <small class=\"form-text\">";
        // line 150
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The multistore feature allows you to manage several e-shops with one Back Office. If this feature is enabled, a \"Multistore\" page will be available in the \"Advanced Parameters\" menu.", array(), "Admin.Shopparameters.Help"), "html", null, true);
        echo "</small>

                                    ";
        // line 152
        if ($this->getAttribute($this->getAttribute($this->getAttribute(($context["generalForm"] ?? null), "multishop_feature_active", array()), "vars", array()), "disabled", array())) {
            // line 153
            echo "                                      <div class=\"alert alert-warning mt-2 mb-0\" role=\"alert\">
                                        <p class=\"alert-text\">
                                          ";
            // line 155
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You can't change the value of this configuration field in the context of this shop.", array(), "Admin.Notifications.Warning"), "html", null, true);
            echo "
                                        </p>
                                      </div>
                                    ";
        }
        // line 159
        echo "                                </div>
                            </div>
                            <div class=\"form-group row\">
                                <label class=\"form-control-label\">";
        // line 162
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Main Shop Activity", array(), "Admin.Shopparameters.Feature"), "html", null, true);
        echo "</label>
                                <div class=\"col-sm\">
                                    ";
        // line 164
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "shop_activity", array()), 'errors');
        echo "
                                    ";
        // line 165
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["generalForm"] ?? null), "shop_activity", array()), 'widget');
        echo "
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class=\"card-footer\">
                        <div class=\"d-flex justify-content-end\">
                            <button class=\"btn btn-primary\">";
        // line 172
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save", array(), "Admin.Actions"), "html", null, true);
        echo "</button>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  366 => 172,  356 => 165,  352 => 164,  347 => 162,  342 => 159,  335 => 155,  331 => 153,  329 => 152,  324 => 150,  320 => 149,  316 => 148,  311 => 146,  304 => 142,  300 => 141,  296 => 140,  291 => 138,  284 => 134,  280 => 133,  276 => 132,  271 => 130,  264 => 126,  260 => 125,  256 => 124,  251 => 122,  244 => 118,  240 => 117,  236 => 116,  231 => 114,  224 => 110,  220 => 109,  216 => 108,  211 => 106,  204 => 102,  200 => 101,  196 => 100,  191 => 98,  184 => 94,  180 => 93,  176 => 92,  171 => 90,  166 => 87,  159 => 83,  155 => 81,  153 => 80,  148 => 78,  144 => 77,  140 => 76,  135 => 74,  128 => 70,  124 => 69,  120 => 68,  115 => 66,  111 => 64,  103 => 59,  99 => 58,  95 => 56,  89 => 53,  85 => 52,  81 => 51,  78 => 50,  76 => 49,  73 => 48,  71 => 45,  63 => 40,  58 => 37,  55 => 36,  49 => 180,  45 => 178,  43 => 36,  37 => 34,  34 => 33,  30 => 25,  28 => 30,  26 => 27,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Configure/ShopParameters/preferences.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Configure/ShopParameters/preferences.html.twig");
    }
}

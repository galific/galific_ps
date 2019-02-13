<?php

/* @PrestaShop/Admin/Module/Includes/modal_confirm.html.twig */
class __TwigTemplate_95fe8df811f1bf5974c3a819fd5bd7aabfc0615fbbaa1a6f89cb2f3867db8fce extends Twig_Template
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
        if ((twig_length_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "urls", array())) >= 1)) {
            // line 26
            echo "  ";
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "urls", array()));
            foreach ($context['_seq'] as $context["module_action"] => $context["module_url"]) {
                // line 27
                echo "    ";
                if (twig_in_filter($context["module_action"], array(0 => "disable", 1 => "uninstall", 2 => "reset"))) {
                    // line 28
                    echo "      <div id=\"module-modal-confirm-";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
                    echo "-";
                    echo twig_escape_filter($this->env, $context["module_action"], "html", null, true);
                    echo "\" class=\"modal modal-vcenter fade\" role=\"dialog\">
        <div class=\"modal-dialog\">
          <!-- Modal content-->
          <div class=\"modal-content\">
            <div class=\"modal-header\">
              <h4 class=\"modal-title module-modal-title\">
                ";
                    // line 34
                    if (($context["module_action"] == "disable")) {
                        // line 35
                        echo "                  ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Disable module?", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                ";
                    }
                    // line 37
                    echo "                ";
                    if (($context["module_action"] == "uninstall")) {
                        // line 38
                        echo "                  ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Uninstall module?", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                ";
                    }
                    // line 40
                    echo "                ";
                    if (($context["module_action"] == "reset")) {
                        // line 41
                        echo "                  ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Reset module?", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                ";
                    }
                    // line 43
                    echo "              </h4>
              <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                <span aria-hidden=\"true\">&times;</span>
              </button>
            </div>
            <div class=\"modal-body row\">
              <div class=\"col-md-12\">
                <p>
                  ";
                    // line 51
                    if (($context["module_action"] == "disable")) {
                        // line 52
                        echo "                    ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You are about to disable %moduleName% module.", array("%moduleName%" => $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array())), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    <br>
                    <br>
                    ";
                        // line 55
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Your current settings will be saved, but the module will no longer be active.", array(), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                  ";
                    }
                    // line 57
                    echo "                  ";
                    if (($context["module_action"] == "uninstall")) {
                        // line 58
                        echo "                    ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You are about to uninstall %moduleName% module.", array("%moduleName%" => $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array())), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    <br>
                    ";
                        // line 60
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "confirmUninstall", array()), "html", null, true);
                        echo "
                    <br>
                    <br>
                    ";
                        // line 63
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This will disable the module and delete all its files. For good.", array(), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    <br>
                    <label>
                      <input type=\"checkbox\" id=\"force_deletion\" name=\"force_deletion\" data-tech-name=\"";
                        // line 66
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
                        echo "\"> ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Optional: delete module folder after uninstall.", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                    </label>
                    <br>
                    <span class=\"italic red\">
                      ";
                        // line 70
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This action cannot be undone.", array(), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    </span>
                  ";
                    }
                    // line 73
                    echo "                  ";
                    if (($context["module_action"] == "reset")) {
                        // line 74
                        echo "                    ";
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("You're about to reset %moduleName% module.", array("%moduleName%" => $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "displayName", array())), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    <br>
                    <br>
                    ";
                        // line 77
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This will restore the defaults settings.", array(), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    <br>
                    <span class=\"italic red\">
                      ";
                        // line 80
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This action cannot be undone.", array(), "Admin.Modules.Notification"), "html", null, true);
                        echo "
                    </span>
                  ";
                    }
                    // line 83
                    echo "                </p>
              </div>
            </div>
            <div class=\"modal-footer\">
              <input type=\"button\" class=\"btn btn-outline-secondary\" data-dismiss=\"modal\" value=\"";
                    // line 87
                    echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Cancel", array(), "Admin.Actions"), "html", null, true);
                    echo "\" />
              ";
                    // line 88
                    if (($context["module_action"] == "disable")) {
                        // line 89
                        echo "                <a
                  class=\"btn btn-primary uppercase module_action_modal_";
                        // line 90
                        echo twig_escape_filter($this->env, $context["module_action"], "html", null, true);
                        echo "\"
                  href=\"";
                        // line 91
                        echo twig_escape_filter($this->env, $context["module_url"], "html", null, true);
                        echo "\"
                  data-dismiss=\"modal\"
                  data-tech-name=\"";
                        // line 93
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
                        echo "\"
                >
                  ";
                        // line 95
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Yes, disable it", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                </a>
              ";
                    }
                    // line 98
                    echo "              ";
                    if (($context["module_action"] == "uninstall")) {
                        // line 99
                        echo "                <a
                  class=\"btn btn-primary uppercase module_action_modal_";
                        // line 100
                        echo twig_escape_filter($this->env, $context["module_action"], "html", null, true);
                        echo "\"
                  href=\"";
                        // line 101
                        echo twig_escape_filter($this->env, $context["module_url"], "html", null, true);
                        echo "\"
                  data-dismiss=\"modal\"
                  data-tech-name=\"";
                        // line 103
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
                        echo "\"
                >
                  ";
                        // line 105
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Yes, uninstall it", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                </a>
              ";
                    }
                    // line 108
                    echo "              ";
                    if (($context["module_action"] == "reset")) {
                        // line 109
                        echo "                <a
                  class=\"btn btn-primary uppercase module_action_modal_";
                        // line 110
                        echo twig_escape_filter($this->env, $context["module_action"], "html", null, true);
                        echo "\"
                  href=\"";
                        // line 111
                        echo twig_escape_filter($this->env, $context["module_url"], "html", null, true);
                        echo "\"
                  data-dismiss=\"modal\"
                  data-tech-name=\"";
                        // line 113
                        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute(($context["module"] ?? null), "attributes", array()), "name", array()), "html", null, true);
                        echo "\"
                >
                  ";
                        // line 115
                        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Yes, reset it", array(), "Admin.Modules.Feature"), "html", null, true);
                        echo "
                </a>
              ";
                    }
                    // line 118
                    echo "            </div>
          </div>
        </div>
      </div>
    ";
                }
                // line 123
                echo "  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['module_action'], $context['module_url'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        }
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/modal_confirm.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  243 => 123,  236 => 118,  230 => 115,  225 => 113,  220 => 111,  216 => 110,  213 => 109,  210 => 108,  204 => 105,  199 => 103,  194 => 101,  190 => 100,  187 => 99,  184 => 98,  178 => 95,  173 => 93,  168 => 91,  164 => 90,  161 => 89,  159 => 88,  155 => 87,  149 => 83,  143 => 80,  137 => 77,  130 => 74,  127 => 73,  121 => 70,  112 => 66,  106 => 63,  100 => 60,  94 => 58,  91 => 57,  86 => 55,  79 => 52,  77 => 51,  67 => 43,  61 => 41,  58 => 40,  52 => 38,  49 => 37,  43 => 35,  41 => 34,  29 => 28,  26 => 27,  21 => 26,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/modal_confirm.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/modal_confirm.html.twig");
    }
}

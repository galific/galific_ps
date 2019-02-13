<?php

/* @PrestaShop/Admin/Module/Includes/modal_confirm_prestatrust.html.twig */
class __TwigTemplate_bf20f86191fda6ac1633cc83d123297c5014c862ae4cb93700171f2f2917c85f extends Twig_Template
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
<div id=\"modal-prestatrust\" class=\"modal\">
  <div class=\"modal-dialog\" role=\"document\">
    <div class=\"modal-content\">
      <div class=\"modal-header\">
        <h5 class=\"modal-title\">";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Module verification", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</h5>
        <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
          <span aria-hidden=\"true\">&times;</span>
        </button>
      </div>
      <div class=\"modal-body\">
        <div class=\"row\">
            <div class=\"col-md-2 text-sm-center\">
              <img id=\"pstrust-img\" src=\"\" alt=\"\"/>
            </div>
            <div class=\"col-md-10\">
              <dl class=\"row\">
                <dt class=\"col-sm-3\">";
        // line 42
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Module", array(), "Admin.Global"), "html", null, true);
        echo "</dt>
                <dd class=\"col-sm-9\">
                    <strong id=\"pstrust-name\"></strong>
                </dd>
                <dt class=\"col-sm-3\">";
        // line 46
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Author", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</dt>
                <dd class=\"col-sm-9\" id=\"pstrust-author\"></dd>
                <dt class=\"col-sm-3\">";
        // line 48
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Status", array(), "Admin.Global"), "html", null, true);
        echo "</dt>
                <dd class=\"col-sm-9\"><strong><span class=\"text-info\" id=\"pstrust-label\"></span></strong></dd>
              </dl>
            </div>
        </div>
        <div class=\"row\">
            <div class=\"col-md-12\">
                <div class=\"alert alert-info\" id=\"pstrust-message\" role=\"alert\">
                    <p class=\"alert-text\"></p>
                </div>
            </div>
        </div>
      </div>
      <div class=\"modal-footer\">
        <div id=\"pstrust-btn-property-ok\">
            <button type=\"button\" class=\"btn btn-outline-secondary\" data-dismiss=\"modal\">";
        // line 63
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Back to modules list", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</button>
            <button type=\"submit\" class=\"btn btn-primary pstrust-install\">";
        // line 64
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Proceed with the installation", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</button>
        </div>
        <div id=\"pstrust-btn-property-nok\">
            <button type=\"submit\" class=\"btn btn-outline-secondary pstrust-install\">";
        // line 67
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Proceed with the installation", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</button>
            <a href=\"\" class=\"btn btn-primary\" id=\"pstrust-buy\" target=\"_blank\">";
        // line 68
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Buy module", array(), "Admin.Modules.Feature"), "html", null, true);
        echo "</a>
        </div>
      </div>
    </div>
  </div>
</div>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Module/Includes/modal_confirm_prestatrust.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  85 => 68,  81 => 67,  75 => 64,  71 => 63,  53 => 48,  48 => 46,  41 => 42,  26 => 30,  19 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Module/Includes/modal_confirm_prestatrust.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Module/Includes/modal_confirm_prestatrust.html.twig");
    }
}

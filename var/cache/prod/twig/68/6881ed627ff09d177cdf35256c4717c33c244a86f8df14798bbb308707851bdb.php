<?php

/* @Product/ProductPage/Blocks/footer.html.twig */
class __TwigTemplate_9885156e9434afa55d82f1e8a24af6dc29bd6b12fc87fa99198ce6230b3dd12a extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'product_footer' => array($this, 'block_product_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo " ";
        $this->displayBlock('product_footer', $context, $blocks);
    }

    public function block_product_footer($context, array $blocks = array())
    {
        // line 26
        echo "  <div class=\"product-footer justify-content-md-center\">
    <div class=\"col-lg-4\">
      <a
        href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_unit_action", array("action" => "delete", "id" => ($context["productId"] ?? null))), "html", null, true);
        echo "\"
        class=\"tooltip-link btn btn-lg delete\"
        data-toggle=\"pstooltip\"
        id=\"product_form_delete_btn\"
        title=\"";
        // line 33
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Permanently delete this product.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
        data-placement=\"left\"
        data-original-title=\"Delete\"
      >
        <i class=\"material-icons\">delete</i>
      </a>
      <a
        href=\"\"
        data-redirect=\"";
        // line 41
        echo twig_escape_filter($this->env, ($context["preview_link"] ?? null), "html", null, true);
        echo "\"
        data-url-deactive=\"";
        // line 42
        echo twig_escape_filter($this->env, ($context["preview_link_deactivate"] ?? null), "html", null, true);
        echo "\"
        target=\"_blank\"
        class=\"btn btn-secondary preview\"
        data-toggle=\"pstooltip\"
        id=\"product_form_preview_btn\"
        title=\"";
        // line 47
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("See how your product sheet will look online: ALT+SHIFT+V", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
      >
        ";
        // line 49
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Preview", array(), "Admin.Actions"), "html", null, true);
        echo "
      </a>
      ";
        // line 51
        if (($context["editable"] ?? null)) {
            // line 52
            echo "      <h2 class=\"for-switch online-title\" ";
            if ( !($context["is_active"] ?? null)) {
                echo "style=\"display:none;\"";
            }
            echo " data-toggle=\"pstooltip\"
          title=\"";
            // line 53
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable or disable the product on your shop: ALT+SHIFT+O", array(), "Admin.Catalog.Help"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Online", array(), "Admin.Global"), "html", null, true);
            echo "</h2>
      <h2 class=\"for-switch offline-title\" ";
            // line 54
            if (($context["is_active"] ?? null)) {
                echo "style=\"display:none;\"";
            }
            echo " data-toggle=\"pstooltip\"
          title=\"";
            // line 55
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Enable or disable the product on your shop: ALT+SHIFT+O", array(), "Admin.Catalog.Help"), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Offline", array(), "Admin.Global"), "html", null, true);
            echo "</h2>
      <input
        class=\"switch-input-lg\"
        id=\"form_step1_active\"
        data-toggle=\"switch\"
        type=\"checkbox\"
        name=\"form[step1][active]\"
        value=\"1\"
        ";
            // line 63
            echo ((($context["is_active"] ?? null)) ? ("checked=\"checked\"") : (""));
            echo "
      />
      ";
        }
        // line 66
        echo "    </div>
    <div class=\"col-sm-5 col-lg-7 text-right\">
      <input
        id=\"submit\"
        type=\"submit\"
        class=\"btn btn-primary save uppercase ml-3\"
        value=\"";
        // line 72
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save", array(), "Admin.Actions"), "html", null, true);
        echo "\"
        data-toggle=\"pstooltip\"
        title=\"";
        // line 74
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save the product and stay on the current page: ALT+SHIFT+S", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
        />
      ";
        // line 76
        if (($context["is_shop_context"] ?? null)) {
            // line 77
            echo "        <button
          type=\"button\"
          class=\"btn btn-outline-secondary btn-submit hidden-xs uppercase duplicate ml-3\"
          id=\"product_form_save_duplicate_btn\"
          data-redirect=\"";
            // line 81
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_unit_action", array("action" => "duplicate", "id" => ($context["productId"] ?? null))), "html", null, true);
            echo "\"
          data-toggle=\"pstooltip\"
          title=\"";
            // line 83
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save and duplicate this product, then go to the new product: ALT+SHIFT+D", array(), "Admin.Catalog.Help"), "html", null, true);
            echo "\"
          >
          ";
            // line 85
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplicate", array(), "Admin.Actions");
            echo "
        </button>
      ";
        }
        // line 88
        echo "      <button
        type=\"button\"
        class=\"btn btn-outline-secondary btn-submit hidden-xs uppercase go-catalog ml-3\"
        id=\"product_form_save_go_to_catalog_btn\"
        data-redirect=\"";
        // line 92
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_catalog", array("offset" => "last", "limit" => "last")), "html", null, true);
        echo "\"
        data-toggle=\"pstooltip\"
        title=\"";
        // line 94
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save and go back to the catalog: ALT+SHIFT+Q", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
        >
        ";
        // line 96
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Go to catalog", array(), "Admin.Catalog.Feature");
        echo "
      </button>
      <button
        type=\"button\"
        class=\"btn btn-outline-secondary btn-submit hidden-xs uppercase new-product ml-3\"
        id=\"product_form_save_new_btn\"
        data-redirect=\"";
        // line 102
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_new");
        echo "\"
        data-toggle=\"pstooltip\"
        title=\"";
        // line 104
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save and create a new product: ALT+SHIFT+P", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
        >
        ";
        // line 106
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Add new product", array(), "Admin.Catalog.Feature");
        echo "
      </button>

        <div class=\"js-spinner spinner hide btn-primary-reverse onclick mr-1 btn\"></div>
        <div class=\"btn-group hide dropdown\">
          <button
          class=\"btn btn-primary js-btn-save ml-3\"
          type=\"submit\"
          >
            <span>";
        // line 115
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save", array(), "Admin.Actions"), "html", null, true);
        echo "</span>
          </button><button
            class=\"btn btn-primary dropdown-toggle dropdown-toggle-split\"
            type=\"button\"
            id=\"dropdownMenu\"
            data-toggle=\"dropdown\"
            aria-expanded=\"false\"
          >
            <span class=\"sr-only\">Toggle Dropdown</span>
          </button>
          <div class=\"dropdown-menu\" aria-labelledby=\"dropdownMenu\">
            ";
        // line 126
        if (($context["is_shop_context"] ?? null)) {
            // line 127
            echo "              <a
              class=\"dropdown-item duplicate js-btn-save\"
              href=\"";
            // line 129
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_unit_action", array("action" => "duplicate", "id" => ($context["productId"] ?? null))), "html", null, true);
            echo "\"
              >
              ";
            // line 131
            echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplicate", array(), "Admin.Actions");
            echo "
              </a>
            ";
        }
        // line 134
        echo "          <a
            class=\"dropdown-item go-catalog js-btn-save\"
            href=\"";
        // line 136
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_catalog", array("offset" => "last", "limit" => "last")), "html", null, true);
        echo "\"
            >
            ";
        // line 138
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Go to catalog", array(), "Admin.Catalog.Feature");
        echo "
          </a>
          <a
            class=\"dropdown-item new-product js-btn-save\"
            href=\"";
        // line 142
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_new");
        echo "\"
            >
            ";
        // line 144
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Add new product", array(), "Admin.Catalog.Feature");
        echo "
          </a>
        </div>
      </div>
    </div>
  </div>
";
    }

    public function getTemplateName()
    {
        return "@Product/ProductPage/Blocks/footer.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  253 => 144,  248 => 142,  241 => 138,  236 => 136,  232 => 134,  226 => 131,  221 => 129,  217 => 127,  215 => 126,  201 => 115,  189 => 106,  184 => 104,  179 => 102,  170 => 96,  165 => 94,  160 => 92,  154 => 88,  148 => 85,  143 => 83,  138 => 81,  132 => 77,  130 => 76,  125 => 74,  120 => 72,  112 => 66,  106 => 63,  93 => 55,  87 => 54,  81 => 53,  74 => 52,  72 => 51,  67 => 49,  62 => 47,  54 => 42,  50 => 41,  39 => 33,  32 => 29,  27 => 26,  20 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/ProductPage/Blocks/footer.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/Blocks/footer.html.twig");
    }
}

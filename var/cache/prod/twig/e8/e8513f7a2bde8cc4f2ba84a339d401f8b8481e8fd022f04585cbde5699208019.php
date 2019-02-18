<?php

/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/layout.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 25);
        $this->blocks = array(
            'javascripts' => array($this, 'block_javascripts'),
            'content' => array($this, 'block_content'),
            'product_catalog_tools' => array($this, 'block_product_catalog_tools'),
            'product_catalog_filters' => array($this, 'block_product_catalog_filters'),
            'product_catalog_form' => array($this, 'block_product_catalog_form'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 26
        $this->env->getRuntime("Symfony\\Component\\Form\\FormRenderer")->setTheme(($context["categories"] ?? null), array(0 => "@PrestaShop/Admin/Product/Themes/categories_theme.html.twig"), true);
        // line 25
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 28
    public function block_javascripts($context, array $blocks = array())
    {
        // line 29
        echo "  ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
  <script src=\"";
        // line 30
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/catalog.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/pagination.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 32
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/category-tree.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 33
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("../js/jquery/ui/jquery.ui.sortable.min.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/new-theme/public/catalog.bundle.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 37
    public function block_content($context, array $blocks = array())
    {
        // line 38
        echo "  <div class=\"products-catalog\">

    ";
        // line 40
        echo $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHook("legacy_block_kpi", array("kpi_controller" => "AdminProductsController"));
        echo "

    <div class=\"content container-fluid\">

      ";
        // line 44
        if (twig_length_filter($this->env, ($context["permission_error"] ?? null))) {
            // line 45
            echo "      <div class=\"row\">
        <div class=\"col-md-12\">
          <div class=\"alert alert-danger\" role=\"alert\">
            <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
              <span aria-hidden=\"true\"><i class=\"material-icons\">close</i></span>
            </button>
            <p class=\"alert-text\">
              ";
            // line 52
            echo twig_escape_filter($this->env, ($context["permission_error"] ?? null), "html", null, true);
            echo "
            </p>
          </div>
        </div>
      </div>
      ";
        }
        // line 58
        echo "
      <div class=\"row align-items-start\">
        ";
        // line 60
        $this->displayBlock('product_catalog_tools', $context, $blocks);
        // line 63
        echo "
        ";
        // line 64
        $this->displayBlock('product_catalog_filters', $context, $blocks);
        // line 73
        echo "      </div>

      ";
        // line 75
        $this->displayBlock('product_catalog_form', $context, $blocks);
        // line 99
        echo "
    </div>
  </div>

  ";
        // line 104
        echo "  ";
        $this->loadTemplate("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 104, "1325378082")->display(array_merge($context, array("id" => "catalog_duplicate_all_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplicating products", array(), "Admin.Catalog.Notification"), "closable" => true, "progressbar" => array("id" => "catalog_duplicate_all_progression", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplicating...", array(), "Admin.Catalog.Notification")), "actions" => array())));
        // line 123
        echo "

  ";
        // line 126
        echo "  ";
        $this->loadTemplate("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 126, "977681542")->display(array_merge($context, array("id" => "catalog_activate_all_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Activating products", array(), "Admin.Catalog.Notification"), "closable" => true, "progressbar" => array("id" => "catalog_activate_all_progression", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Activating...", array(), "Admin.Catalog.Notification")), "actions" => array())));
        // line 145
        echo "

  ";
        // line 148
        echo "  ";
        $this->loadTemplate("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 148, "805521738")->display(array_merge($context, array("id" => "catalog_deactivate_all_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deactivating products", array(), "Admin.Catalog.Notification"), "closable" => true, "progressbar" => array("id" => "catalog_deactivate_all_progression", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deactivating...", array(), "Admin.Catalog.Notification")), "actions" => array())));
        // line 167
        echo "

  ";
        // line 170
        echo "  ";
        $this->loadTemplate("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 170, "1314789835")->display(array_merge($context, array("id" => "catalog_delete_all_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deleting products", array(), "Admin.Catalog.Notification"), "closable" => true, "progressbar" => array("id" => "catalog_delete_all_progression", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deleting...", array(), "Admin.Catalog.Notification")), "actions" => array())));
        // line 189
        echo "

  ";
        // line 192
        echo "  ";
        $this->loadTemplate("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 192, "1383307493")->display(array_merge($context, array("id" => "catalog_deletion_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Delete products?", array(), "Admin.Catalog.Feature"), "closable" => true, "actions" => array(0 => array("type" => "button", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Delete now", array(), "Admin.Actions"), "value" => "confirm", "class" => "btn btn-primary btn-lg")))));
        // line 209
        echo "
  ";
        // line 210
        $this->loadTemplate("@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 210, "1617907564")->display(array_merge($context, array("id" => "catalog_sql_query_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("SQL query", array(), "Admin.Global"), "closable" => true, "actions" => array(0 => array("type" => "button", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Export to SQL Manager", array(), "Admin.Actions"), "value" => "sql_manager", "class" => "btn btn-primary btn-lg")))));
        // line 230
        echo "
";
    }

    // line 60
    public function block_product_catalog_tools($context, array $blocks = array())
    {
        // line 61
        echo "          ";
        echo twig_include($this->env, $context, "@Product/CatalogPage/Blocks/tools.html.twig", array("import_link" => ($context["import_link"] ?? null)));
        echo "
        ";
    }

    // line 64
    public function block_product_catalog_filters($context, array $blocks = array())
    {
        // line 65
        echo "          ";
        echo twig_include($this->env, $context, "@Product/CatalogPage/Blocks/filters.html.twig", array("limit" =>         // line 66
($context["limit"] ?? null), "offset" =>         // line 67
($context["offset"] ?? null), "orderBy" =>         // line 68
($context["orderBy"] ?? null), "sortOrder" =>         // line 69
($context["sortOrder"] ?? null)));
        // line 71
        echo "
        ";
    }

    // line 75
    public function block_product_catalog_form($context, array $blocks = array())
    {
        // line 76
        echo "        ";
        echo twig_include($this->env, $context, "@Product/CatalogPage/Forms/form_products.html.twig", array("limit" =>         // line 77
($context["limit"] ?? null), "orderBy" =>         // line 78
($context["orderBy"] ?? null), "offset" =>         // line 79
($context["offset"] ?? null), "sortOrder" =>         // line 80
($context["sortOrder"] ?? null), "filter_category" =>         // line 81
($context["filter_category"] ?? null), "filter_column_id_product" =>         // line 82
($context["filter_column_id_product"] ?? null), "filter_column_name" =>         // line 83
($context["filter_column_name"] ?? null), "filter_column_reference" =>         // line 84
($context["filter_column_reference"] ?? null), "filter_column_name_category" =>         // line 85
($context["filter_column_name_category"] ?? null), "filter_column_price" =>         // line 86
($context["filter_column_price"] ?? null), "filter_column_sav_quantity" =>         // line 87
($context["filter_column_sav_quantity"] ?? null), "filter_column_active" =>         // line 88
($context["filter_column_active"] ?? null), "has_category_filter" =>         // line 89
($context["has_category_filter"] ?? null), "activate_drag_and_drop" =>         // line 90
($context["activate_drag_and_drop"] ?? null), "products" =>         // line 91
($context["products"] ?? null), "last_sql" =>         // line 92
($context["last_sql"] ?? null), "product_count_filtered" =>         // line 93
($context["product_count_filtered"] ?? null), "pagination_parameters" =>         // line 94
($context["pagination_parameters"] ?? null), "pagination_limit_choices" =>         // line 95
($context["pagination_limit_choices"] ?? null)));
        // line 97
        echo "
      ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}


/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8_1325378082 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 104
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 104);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 114
    public function block_content($context, array $blocks = array())
    {
        // line 115
        echo "      <div class=\"modal-body\">
        ";
        // line 116
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplication in progress...", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        <span id=\"catalog_duplicate_all_failure\" style=\"display: none;color: darkred;\">
          ";
        // line 118
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplication failed.", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        </span>
      </div>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  283 => 118,  278 => 116,  275 => 115,  272 => 114,  255 => 104,  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}


/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8_977681542 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 126
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 126);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 136
    public function block_content($context, array $blocks = array())
    {
        // line 137
        echo "      <div class=\"modal-body\">
        ";
        // line 138
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Activation in progress...", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        <span id=\"catalog_activate_all_failure\" style=\"display: none;color: darkred;\">
          ";
        // line 140
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Activation failed.", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        </span>
      </div>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  356 => 140,  351 => 138,  348 => 137,  345 => 136,  328 => 126,  283 => 118,  278 => 116,  275 => 115,  272 => 114,  255 => 104,  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}


/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8_805521738 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 148
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 148);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 158
    public function block_content($context, array $blocks = array())
    {
        // line 159
        echo "      <div class=\"modal-body\">
        ";
        // line 160
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deactivation in progress...", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        <span id=\"catalog_deactivate_all_failure\" style=\"display: none;color: darkred;\">
          ";
        // line 162
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deactivation failed.", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        </span>
      </div>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  429 => 162,  424 => 160,  421 => 159,  418 => 158,  401 => 148,  356 => 140,  351 => 138,  348 => 137,  345 => 136,  328 => 126,  283 => 118,  278 => 116,  275 => 115,  272 => 114,  255 => 104,  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}


/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8_1314789835 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 170
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 170);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 180
    public function block_content($context, array $blocks = array())
    {
        // line 181
        echo "      <div class=\"modal-body\">
        ";
        // line 182
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deletion in progress...", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        <span id=\"catalog_delete_all_failure\" style=\"display: none;color: darkred;\">
          ";
        // line 184
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deletion failed.", array(), "Admin.Catalog.Notification"), "html", null, true);
        echo "
        </span>
      </div>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  502 => 184,  497 => 182,  494 => 181,  491 => 180,  474 => 170,  429 => 162,  424 => 160,  421 => 159,  418 => 158,  401 => 148,  356 => 140,  351 => 138,  348 => 137,  345 => 136,  328 => 126,  283 => 118,  278 => 116,  275 => 115,  272 => 114,  255 => 104,  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}


/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8_1383307493 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 192
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 192);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 203
    public function block_content($context, array $blocks = array())
    {
        // line 204
        echo "      <div class=\"modal-body\">
        ";
        // line 205
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("These products will be deleted for good. Please confirm.", array(), "Admin.Catalog.Feature"), "html", null, true);
        echo "
      </div>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  570 => 205,  567 => 204,  564 => 203,  547 => 192,  502 => 184,  497 => 182,  494 => 181,  491 => 180,  474 => 170,  429 => 162,  424 => 160,  421 => 159,  418 => 158,  401 => 148,  356 => 140,  351 => 138,  348 => 137,  345 => 136,  328 => 126,  283 => 118,  278 => 116,  275 => 115,  272 => 114,  255 => 104,  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}


/* @PrestaShop/Admin/Product/CatalogPage/catalog.html.twig */
class __TwigTemplate_4ec0192774941273c8c5483667d1e147347b1631fe6b5f2f46a5c6b9bc6484a8_1617907564 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 210
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", 210);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 221
    public function block_content($context, array $blocks = array())
    {
        // line 222
        echo "      <form method=\"post\" action=\"";
        echo twig_escape_filter($this->env, ($context["sql_manager_add_link"] ?? null), "html", null, true);
        echo "\" id=\"catalog_sql_query_modal_content\">
        <div class=\"modal-body\">
          <textarea name=\"sql\" rows=\"20\" cols=\"65\"></textarea>
          <input type=\"hidden\" name=\"name\" value=\"\" />
        </div>
      </form>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  634 => 222,  631 => 221,  614 => 210,  570 => 205,  567 => 204,  564 => 203,  547 => 192,  502 => 184,  497 => 182,  494 => 181,  491 => 180,  474 => 170,  429 => 162,  424 => 160,  421 => 159,  418 => 158,  401 => 148,  356 => 140,  351 => 138,  348 => 137,  345 => 136,  328 => 126,  283 => 118,  278 => 116,  275 => 115,  272 => 114,  255 => 104,  213 => 97,  211 => 95,  210 => 94,  209 => 93,  208 => 92,  207 => 91,  206 => 90,  205 => 89,  204 => 88,  203 => 87,  202 => 86,  201 => 85,  200 => 84,  199 => 83,  198 => 82,  197 => 81,  196 => 80,  195 => 79,  194 => 78,  193 => 77,  191 => 76,  188 => 75,  183 => 71,  181 => 69,  180 => 68,  179 => 67,  178 => 66,  176 => 65,  173 => 64,  166 => 61,  163 => 60,  158 => 230,  156 => 210,  153 => 209,  150 => 192,  146 => 189,  143 => 170,  139 => 167,  136 => 148,  132 => 145,  129 => 126,  125 => 123,  122 => 104,  116 => 99,  114 => 75,  110 => 73,  108 => 64,  105 => 63,  103 => 60,  99 => 58,  90 => 52,  81 => 45,  79 => 44,  72 => 40,  68 => 38,  65 => 37,  59 => 34,  55 => 33,  51 => 32,  47 => 31,  43 => 30,  38 => 29,  35 => 28,  31 => 25,  29 => 26,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/catalog.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/catalog.html.twig");
    }
}

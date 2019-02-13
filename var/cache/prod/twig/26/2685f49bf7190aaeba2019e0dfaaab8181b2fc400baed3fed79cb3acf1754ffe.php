<?php

/* @Product/CatalogPage/Lists/products_table.html.twig */
class __TwigTemplate_095099f237cbe0eb793672f6a10279a2009e63c1243eeafca960855459fd9520 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'product_catalog_form_table_header' => array($this, 'block_product_catalog_form_table_header'),
            'product_catalog_form_table_filters' => array($this, 'block_product_catalog_form_table_filters'),
            'product_catalog_form_table_items' => array($this, 'block_product_catalog_form_table_items'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        $context["ps"] = $this->loadTemplate("@PrestaShop/Admin/macros.html.twig", "@Product/CatalogPage/Lists/products_table.html.twig", 25);
        // line 26
        echo "<div class=\"table-responsive\">
  <table
    class=\"table product mt-3\"
    redirecturl=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_catalog", array("limit" =>         // line 30
($context["limit"] ?? null), "offset" =>         // line 31
($context["offset"] ?? null), "orderBy" =>         // line 32
($context["orderBy"] ?? null), "sortOrder" =>         // line 33
($context["sortOrder"] ?? null))), "html", null, true);
        // line 35
        echo "\"
  >
    <thead class=\"with-filters\">
      ";
        // line 38
        $this->displayBlock('product_catalog_form_table_header', $context, $blocks);
        // line 81
        echo "
      ";
        // line 82
        $this->displayBlock('product_catalog_form_table_filters', $context, $blocks);
        // line 196
        echo "    </thead>
    ";
        // line 197
        $this->displayBlock('product_catalog_form_table_items', $context, $blocks);
        // line 207
        echo "  </table>
</div>
";
    }

    // line 38
    public function block_product_catalog_form_table_header($context, array $blocks = array())
    {
        // line 39
        echo "        <tr class=\"column-headers\">
          <th scope=\"col\" style=\"width: 2rem\"></th>
          <th scope=\"col\" style=\"width: 5rem\">
            ";
        // line 42
        echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("ID", array(), "Admin.Global"), "id_product", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
        echo "
          </th>
          <th scope=\"col\">
            ";
        // line 45
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Image", array(), "Admin.Global"), "html", null, true);
        echo "
          </th>
          <th scope=\"col\">
            ";
        // line 48
        echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Name", array(), "Admin.Global"), "name", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
        echo "
          </th>
          <th scope=\"col\" style=\"width: 9%\">
            ";
        // line 51
        echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Reference", array(), "Admin.Global"), "reference", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
        echo "
          </th>
          <th scope=\"col\">
            ";
        // line 54
        echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Category", array(), "Admin.Catalog.Feature"), "name_category", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
        echo "
          </th>
          <th scope=\"col\" class=\"text-center\" style=\"width: 9%\">
            ";
        // line 57
        echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Price (tax excl.)", array(), "Admin.Catalog.Feature"), "price", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
        echo "
          </th>

          ";
        // line 60
        if ($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_STOCK_MANAGEMENT")) {
            // line 61
            echo "          <th scope=\"col\" class=\"text-center\" style=\"width: 9%\">
            ";
            // line 62
            echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Quantity", array(), "Admin.Catalog.Feature"), "sav_quantity", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
            echo "
          </th>
          ";
        } else {
            // line 65
            echo "            <th></th>
          ";
        }
        // line 67
        echo "
          <th scope=\"col\" class=\"text-center\">
            ";
        // line 69
        echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Status", array(), "Admin.Global"), "active", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
        echo "
          </th>
          ";
        // line 71
        if ((($context["has_category_filter"] ?? null) == true)) {
            // line 72
            echo "            <th scope=\"col\">
              ";
            // line 73
            echo $context["ps"]->getsortable_column_header($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Position", array(), "Admin.Global"), "position", ($context["orderBy"] ?? null), ($context["sortOrder"] ?? null));
            echo "
            </th>
          ";
        }
        // line 76
        echo "          <th scope=\"col\" class=\"text-right\" style=\"width: 3rem; padding-right: 2rem\">
              ";
        // line 77
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Actions", array(), "Admin.Global"), "html", null, true);
        echo "
          </th>
        </tr>
      ";
    }

    // line 82
    public function block_product_catalog_form_table_filters($context, array $blocks = array())
    {
        // line 83
        echo "        ";
        $context["filters_disabled"] = ($context["activate_drag_and_drop"] ?? null);
        // line 84
        echo "        <tr class=\"column-filters\">
          <th colspan=\"2\">
            ";
        // line 86
        $this->loadTemplate("@PrestaShop/Admin/Helpers/range_inputs.html.twig", "@Product/CatalogPage/Lists/products_table.html.twig", 86)->display(array_merge($context, array("input_name" => "filter_column_id_product", "min" => "0", "max" => "1000000", "minLabel" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Min", array(), "Admin.Global"), "maxLabel" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Max", array(), "Admin.Global"), "value" =>         // line 92
($context["filter_column_id_product"] ?? null), "disabled" =>         // line 93
($context["filters_disabled"] ?? null))));
        // line 95
        echo "          </th>
          <th>&nbsp;</th>
          <th>
            <input
              type=\"text\"
              class=\"form-control\"
              placeholder=\"";
        // line 101
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Search name", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
              name=\"filter_column_name\"
              value=\"";
        // line 103
        echo twig_escape_filter($this->env, ($context["filter_column_name"] ?? null), "html", null, true);
        echo "\"
              ";
        // line 104
        if (($context["filters_disabled"] ?? null)) {
            echo "disabled";
        }
        // line 105
        echo "            />
          </th>
          <th>
            <input
              type=\"text\"
              class=\"form-control\"
              placeholder=\"";
        // line 111
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Search ref.", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
              name=\"filter_column_reference\"
              value=\"";
        // line 113
        echo twig_escape_filter($this->env, ($context["filter_column_reference"] ?? null), "html", null, true);
        echo "\"
              ";
        // line 114
        if (($context["filters_disabled"] ?? null)) {
            echo "disabled";
        }
        // line 115
        echo "            />
          </th>
          <th>
            <input
              type=\"text\"
              class=\"form-control\"
              placeholder=\"";
        // line 121
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Search category", array(), "Admin.Catalog.Help"), "html", null, true);
        echo "\"
              name=\"filter_column_name_category\"
              value=\"";
        // line 123
        echo twig_escape_filter($this->env, ($context["filter_column_name_category"] ?? null), "html", null, true);
        echo "\"
              ";
        // line 124
        if (($context["filters_disabled"] ?? null)) {
            echo "disabled";
        }
        // line 125
        echo "            />
          </th>
          <th class=\"text-center\">
            ";
        // line 128
        $this->loadTemplate("@PrestaShop/Admin/Helpers/range_inputs.html.twig", "@Product/CatalogPage/Lists/products_table.html.twig", 128)->display(array_merge($context, array("input_name" => "filter_column_price", "min" => "0", "max" => "1000000", "minLabel" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Min", array(), "Admin.Global"), "maxLabel" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Max", array(), "Admin.Global"), "value" =>         // line 134
($context["filter_column_price"] ?? null), "disabled" =>         // line 135
($context["filters_disabled"] ?? null))));
        // line 137
        echo "          </th>

          ";
        // line 139
        if ($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_STOCK_MANAGEMENT")) {
            // line 140
            echo "          <th class=\"text-center\">
            ";
            // line 141
            $this->loadTemplate("@PrestaShop/Admin/Helpers/range_inputs.html.twig", "@Product/CatalogPage/Lists/products_table.html.twig", 141)->display(array_merge($context, array("input_name" => "filter_column_sav_quantity", "min" => "-1000000", "max" => "1000000", "minLabel" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Min", array(), "Admin.Global"), "maxLabel" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Max", array(), "Admin.Global"), "value" =>             // line 147
($context["filter_column_sav_quantity"] ?? null), "disabled" =>             // line 148
($context["filters_disabled"] ?? null))));
            // line 150
            echo "          </th>
          ";
        } else {
            // line 152
            echo "            <th></th>
          ";
        }
        // line 154
        echo "
          <th id=\"product_filter_column_active\" class=\"text-center\">
            <div class=\"form-select\">
              <select class=\"custom-select\"  name=\"filter_column_active\" ";
        // line 157
        if (($context["filters_disabled"] ?? null)) {
            echo "disabled";
        }
        echo ">
                <option value=\"\"></option>
                <option value=\"1\" ";
        // line 159
        if (((isset($context["filter_column_active"]) || array_key_exists("filter_column_active", $context)) && (($context["filter_column_active"] ?? null) == "1"))) {
            echo "selected=\"selected\"";
        }
        echo ">Active</option>
                <option value=\"0\" ";
        // line 160
        if (((isset($context["filter_column_active"]) || array_key_exists("filter_column_active", $context)) && (($context["filter_column_active"] ?? null) == "0"))) {
            echo "selected=\"selected\"";
        }
        echo ">Inactive</option>
              </select>
            </div>
          </th>
          ";
        // line 164
        if ((($context["has_category_filter"] ?? null) == true)) {
            // line 165
            echo "            <th>
              ";
            // line 166
            if ( !($context["activate_drag_and_drop"] ?? null)) {
                // line 167
                echo "                <input type=\"button\" class=\"btn btn-outline-secondary\" name=\"products_filter_position_asc\" value=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Reorder", array(), "Admin.Actions"), "html", null, true);
                echo "\" onclick=\"productOrderPrioritiesTable();\" />
                ";
            } else {
                // line 169
                echo "                <input type=\"button\" id=\"bulk_edition_save_keep\" class=\"btn\" onclick=\"bulkProductAction(this, 'edition');\" value=\"";
                echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Save & refresh", array(), "Admin.Actions");
                echo "\" />
              ";
            }
            // line 171
            echo "            </th>
          ";
        }
        // line 173
        echo "          <th class=\"text-right\" style=\"width: 5rem\">
            <button
              type=\"submit\"
              class=\"btn btn-primary\"
              name=\"products_filter_submit\"
              title=\"";
        // line 178
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Search", array(), "Admin.Actions"), "html", null, true);
        echo "\"
            >
              <i class=\"material-icons\">search</i>
              ";
        // line 181
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Search", array(), "Admin.Actions"), "html", null, true);
        echo "
            </button>
            <button
              type=\"reset\"
              class=\"btn btn-link\"
              name=\"products_filter_reset\"
              onclick=\"productColumnFilterReset(\$(this).closest('tr.column-filters'))\"
              title=\"";
        // line 188
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Reset", array(), "Admin.Actions"), "html", null, true);
        echo "\"
            >
              <i class=\"material-icons\">clear</i>
              ";
        // line 191
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Reset", array(), "Admin.Actions"), "html", null, true);
        echo "
            </button>
          </th>
        </tr>
      ";
    }

    // line 197
    public function block_product_catalog_form_table_items($context, array $blocks = array())
    {
        // line 198
        echo "      ";
        echo $this->env->getRuntime('Symfony\Bridge\Twig\Extension\HttpKernelRuntime')->renderFragment(Symfony\Bridge\Twig\Extension\HttpKernelExtension::controller("PrestaShopBundle\\Controller\\Admin\\ProductController::listAction", array("limit" =>         // line 199
($context["limit"] ?? null), "offset" =>         // line 200
($context["offset"] ?? null), "orderBy" =>         // line 201
($context["orderBy"] ?? null), "sortOrder" =>         // line 202
($context["sortOrder"] ?? null), "products" =>         // line 203
($context["products"] ?? null), "last_sql" =>         // line 204
($context["last_sql"] ?? null))));
        // line 205
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "@Product/CatalogPage/Lists/products_table.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  355 => 205,  353 => 204,  352 => 203,  351 => 202,  350 => 201,  349 => 200,  348 => 199,  346 => 198,  343 => 197,  334 => 191,  328 => 188,  318 => 181,  312 => 178,  305 => 173,  301 => 171,  295 => 169,  289 => 167,  287 => 166,  284 => 165,  282 => 164,  273 => 160,  267 => 159,  260 => 157,  255 => 154,  251 => 152,  247 => 150,  245 => 148,  244 => 147,  243 => 141,  240 => 140,  238 => 139,  234 => 137,  232 => 135,  231 => 134,  230 => 128,  225 => 125,  221 => 124,  217 => 123,  212 => 121,  204 => 115,  200 => 114,  196 => 113,  191 => 111,  183 => 105,  179 => 104,  175 => 103,  170 => 101,  162 => 95,  160 => 93,  159 => 92,  158 => 86,  154 => 84,  151 => 83,  148 => 82,  140 => 77,  137 => 76,  131 => 73,  128 => 72,  126 => 71,  121 => 69,  117 => 67,  113 => 65,  107 => 62,  104 => 61,  102 => 60,  96 => 57,  90 => 54,  84 => 51,  78 => 48,  72 => 45,  66 => 42,  61 => 39,  58 => 38,  52 => 207,  50 => 197,  47 => 196,  45 => 82,  42 => 81,  40 => 38,  35 => 35,  33 => 33,  32 => 32,  31 => 31,  30 => 30,  29 => 29,  24 => 26,  22 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/CatalogPage/Lists/products_table.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/Lists/products_table.html.twig");
    }
}

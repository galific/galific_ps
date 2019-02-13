<?php

/* @Product/CatalogPage/Blocks/filters.html.twig */
class __TwigTemplate_addb91e2a5a10eadad6b3697998ac841d15e6a2e0b58a730ee873cf48f94dbf2 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'product_catalog_filter_by_categories' => array($this, 'block_product_catalog_filter_by_categories'),
            'product_catalog_filter_bulk_actions' => array($this, 'block_product_catalog_filter_bulk_actions'),
            'product_catalog_filter_select_all' => array($this, 'block_product_catalog_filter_select_all'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "<div id=\"catalog-actions\" class=\"col order-first\">
  <div class=\"row\">
    <div class=\"col\">
      ";
        // line 28
        $this->displayBlock('product_catalog_filter_by_categories', $context, $blocks);
        // line 76
        echo "
      ";
        // line 77
        $this->displayBlock('product_catalog_filter_bulk_actions', $context, $blocks);
        // line 129
        echo "    </div>
  </div>

  ";
        // line 132
        $this->displayBlock('product_catalog_filter_select_all', $context, $blocks);
        // line 150
        echo "</div>
";
    }

    // line 28
    public function block_product_catalog_filter_by_categories($context, array $blocks = array())
    {
        // line 29
        echo "        <div id=\"product_catalog_category_tree_filter\" class=\"d-inline-block dropdown dropdown-clickable mr-2\">
          <button
                  class=\"btn btn-outline-secondary dropdown-toggle\"
                  type=\"button\"
                  data-toggle=\"dropdown\"
                  aria-haspopup=\"true\"
                  aria-expanded=\"false\"
          >
              ";
        // line 37
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Filter by categories", array(), "Admin.Actions"), "html", null, true);
        echo "
              ";
        // line 38
        if (((isset($context["selected_category"]) || array_key_exists("selected_category", $context)) &&  !(null === ($context["selected_category"] ?? null)))) {
            // line 39
            echo "                  (";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["selected_category"] ?? null), "getName", array(), "method"), "html", null, true);
            echo ")
              ";
        }
        // line 41
        echo "          </button>
          <div id=\"tree-categories\" class=\"dropdown-menu\">
            <div class=\"categories-tree-actions\">
              <a
                href=\"#\"
                name=\"product_catalog_category_tree_filter_expand\"
                onclick=\"productCategoryFilterExpand(\$('div#product_catalog_category_tree_filter'), this);\"
                id=\"product_catalog_category_tree_filter_expand\"
              >
                <i class=\"material-icons\">expand_more</i>
                  ";
        // line 51
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Expand", array(), "Admin.Actions"), "html", null, true);
        echo "
              </a>
              <a
                href=\"#\"
                name=\"product_catalog_category_tree_filter_collapse\"
                onclick=\"productCategoryFilterCollapse(\$('div#product_catalog_category_tree_filter'), this);\"
                id=\"product_catalog_category_tree_filter_collapse\"
              >
                <i class=\"material-icons\">expand_less</i>
                  ";
        // line 60
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Collapse", array(), "Admin.Actions"), "html", null, true);
        echo "
              </a>
              <a
                href=\"#\"
                name=\"product_catalog_category_tree_filter_reset\"
                onclick=\"productCategoryFilterReset(\$('div#product_catalog_category_tree_filter'));\"
                id=\"product_catalog_category_tree_filter_reset\"
              >
                <i class=\"material-icons\">radio_button_unchecked</i>
                  ";
        // line 69
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Unselect", array(), "Admin.Actions"), "html", null, true);
        echo "
              </a>
            </div>
              ";
        // line 72
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["categories"] ?? null), 'widget');
        echo "
          </div>
        </div>
      ";
    }

    // line 77
    public function block_product_catalog_filter_bulk_actions($context, array $blocks = array())
    {
        // line 78
        echo "        <div
            class=\"d-inline-block\"
            bulkurl=\"";
        // line 80
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_bulk_action", array("action" => "activate_all"));
        echo "\"
            massediturl=\"";
        // line 81
        echo $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_mass_edit_action", array("action" => "sort"));
        echo "\"
            redirecturl=\"";
        // line 82
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_catalog", array("limit" => ($context["limit"] ?? null), "offset" => ($context["offset"] ?? null), "orderBy" => ($context["orderBy"] ?? null), "sortOrder" => ($context["sortOrder"] ?? null))), "html", null, true);
        echo "\"
            redirecturlnextpage=\"";
        // line 83
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_product_catalog", array("limit" => ($context["limit"] ?? null), "offset" => (($context["offset"] ?? null) + ($context["limit"] ?? null)), "orderBy" => ($context["orderBy"] ?? null), "sortOrder" => ($context["sortOrder"] ?? null))), "html", null, true);
        echo "\"
        >
          ";
        // line 85
        $context["buttons_action"] = array(0 => array("onclick" => "bulkProductAction(this, 'activate_all');", "icon" => "radio_button_checked", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Activate selection", array(), "Admin.Actions")), 1 => array("onclick" => "bulkProductAction(this, 'deactivate_all');", "icon" => "radio_button_unchecked", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Deactivate selection", array(), "Admin.Actions")));
        // line 96
        echo "
          ";
        // line 97
        $context["buttons_action"] = twig_array_merge(($context["buttons_action"] ?? null), array(0 => array("divider" => true), 1 => array("onclick" => "bulkProductAction(this, 'duplicate_all');", "icon" => "content_copy", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplicate selection", array(), "Admin.Actions"))));
        // line 106
        echo "

          ";
        // line 108
        $context["buttons_action"] = twig_array_merge(($context["buttons_action"] ?? null), array(0 => array("divider" => true), 1 => array("onclick" => "bulkProductAction(this, 'delete_all');", "icon" => "delete", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Delete selection", array(), "Admin.Actions"))));
        // line 117
        echo "
          ";
        // line 118
        $this->loadTemplate("@PrestaShop/Admin/Helpers/dropdown_menu.html.twig", "@Product/CatalogPage/Blocks/filters.html.twig", 118)->display(array_merge($context, array("div_style" => "btn-group dropdown bulk-catalog", "button_id" => "product_bulk_menu", "disabled" => true, "menu_label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Bulk actions", array(), "Admin.Global"), "buttonType" => "outline-secondary", "menu_icon" => "icon-caret-up", "items" =>         // line 125
($context["buttons_action"] ?? null))));
        // line 127
        echo "        </div>
      ";
    }

    // line 132
    public function block_product_catalog_filter_select_all($context, array $blocks = array())
    {
        // line 133
        echo "  <div class=\"row\">
    <div class=\"col\">
      <div class=\"md-checkbox\">
        <label>
          <input
            type=\"checkbox\"
            id=\"bulk_action_select_all\"
            onclick=\"\$('#product_catalog_list').find('table td.checkbox-column input:checkbox').prop('checked', \$(this).prop('checked')); updateBulkMenu();\"
            value=\"\"
          >
          <i class=\"md-checkbox-control\"></i>
            ";
        // line 144
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Select all", array(), "Admin.Actions"), "html", null, true);
        echo "
        </label>
      </div>
    </div>
  </div>
  ";
    }

    public function getTemplateName()
    {
        return "@Product/CatalogPage/Blocks/filters.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  185 => 144,  172 => 133,  169 => 132,  164 => 127,  162 => 125,  161 => 118,  158 => 117,  156 => 108,  152 => 106,  150 => 97,  147 => 96,  145 => 85,  140 => 83,  136 => 82,  132 => 81,  128 => 80,  124 => 78,  121 => 77,  113 => 72,  107 => 69,  95 => 60,  83 => 51,  71 => 41,  65 => 39,  63 => 38,  59 => 37,  49 => 29,  46 => 28,  41 => 150,  39 => 132,  34 => 129,  32 => 77,  29 => 76,  27 => 28,  22 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@Product/CatalogPage/Blocks/filters.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/Blocks/filters.html.twig");
    }
}

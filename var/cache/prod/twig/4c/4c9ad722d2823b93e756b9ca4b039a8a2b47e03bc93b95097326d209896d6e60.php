<?php

/* @PrestaShop/Admin/Product/ProductPage/product.html.twig */
class __TwigTemplate_8ae68f1fbb020365a5e7ccde64bc18d57dfe073d9e3e5d77b4c8fc38873a1769 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 25
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/layout.html.twig", "@PrestaShop/Admin/Product/ProductPage/product.html.twig", 25);
        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'product_header' => array($this, 'block_product_header'),
            'product_tabs_container' => array($this, 'block_product_tabs_container'),
            'product_panel_essentials' => array($this, 'block_product_panel_essentials'),
            'product_panel_combinations' => array($this, 'block_product_panel_combinations'),
            'product_panel_shipping' => array($this, 'block_product_panel_shipping'),
            'product_panel_pricing' => array($this, 'block_product_panel_pricing'),
            'product_panel_seo' => array($this, 'block_product_panel_seo'),
            'product_panel_options' => array($this, 'block_product_panel_options'),
            'product_panel_modules' => array($this, 'block_product_panel_modules'),
            'javascripts' => array($this, 'block_javascripts'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@PrestaShop/Admin/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 329
        $context["js_translatable"] = twig_array_merge(array("Are you sure to disable variations ? they will all be deleted" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This will delete all the combinations. Do you wish to proceed?", array(), "Admin.Catalog.Notification")),         // line 331
($context["js_translatable"] ?? null));
        // line 333
        $context["js_translatable"] = twig_array_merge(array("Form update success" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Settings updated.", array(), "Admin.Notifications.Success"), "Form update errors" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Unable to update settings.", array(), "Admin.Notifications.Error"), "Delete" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Delete", array(), "Admin.Actions"), "ToLargeFile" => twig_replace_filter($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("The file is too large. Maximum size allowed is: [1]. The file you are trying to upload is [2].", array(), "Admin.Notifications.Error"), array("[1]" => "{{maxFilesize}}", "[2]" => "{{filesize}}")), "Drop images here" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Drop images here", array(), "Admin.Catalog.Feature"), "or select files" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("or select files", array(), "Admin.Catalog.Feature"), "files recommandations" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Recommended size 800 x 800px for default theme.", array(), "Admin.Catalog.Feature"), "files recommandations2" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("JPG, GIF or PNG format.", array(), "Admin.Catalog.Feature"), "Cover" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Cover", array(), "Admin.Catalog.Feature"), "Are you sure to delete this?" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Are you sure to delete this?", array(), "Admin.Notifications.Warning"), "This will delete the specific price. Do you wish to proceed?" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("This will delete the specific price. Do you wish to proceed?", array(), "Admin.Catalog.Notification"), "Quantities" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Quantities", array(), "Admin.Catalog.Feature"), "Combinations" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Combinations", array(), "Admin.Catalog.Feature"), "Virtual product" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Virtual product", array(), "Admin.Catalog.Feature"), "tax incl." => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("tax incl.", array(), "Admin.Catalog.Feature"), "tax excl." => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("tax excl.", array(), "Admin.Catalog.Feature"), "You can't create pack product with variations. Are you sure to disable variations ? they will all be deleted." => (($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("A pack of products can't have combinations.", array(), "Admin.Catalog.Notification") . " ") . $this->getAttribute(        // line 350
($context["js_translatable"] ?? null), "Are you sure to disable variations ? they will all be deleted", array(), "array")), "You can't create virtual product with variations. Are you sure to disable variations ? they will all be deleted." => (($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("A virtual product can't have combinations.", array(), "Admin.Catalog.Notification") . " ") . $this->getAttribute(        // line 351
($context["js_translatable"] ?? null), "Are you sure to disable variations ? they will all be deleted", array(), "array"))),         // line 352
($context["js_translatable"] ?? null));
        // line 25
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 27
    public function block_content($context, array $blocks = array())
    {
        // line 28
        echo "
  <form name=\"form\" id=\"form\" method=\"post\" class=\"form-horizontal product-page row justify-content-md-center\" novalidate=\"novalidate\">

    ";
        // line 31
        if ( !($context["editable"] ?? null)) {
            echo " <fieldset disabled id=\"field-disabled\"> ";
        }
        // line 32
        echo "    ";
        // line 33
        echo "    ";
        $this->displayBlock('product_header', $context, $blocks);
        // line 44
        echo "
    <div class=\"col-md-10\">
      <div id=\"form_bubbling_errors\">
        ";
        // line 47
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock(($context["form"] ?? null), 'errors');
        echo "
      </div>
    </div>

    <div id=\"form-loading\" class=\"col-xxl-10\">
      ";
        // line 53
        echo "      ";
        $this->displayBlock('product_tabs_container', $context, $blocks);
        // line 56
        echo "      <div id=\"form_content\" class=\"tab-content\">

        ";
        // line 59
        echo "        ";
        $this->displayBlock('product_panel_essentials', $context, $blocks);
        // line 80
        echo "
        ";
        // line 82
        echo "        ";
        $this->displayBlock('product_panel_combinations', $context, $blocks);
        // line 104
        echo "
        ";
        // line 106
        echo "        ";
        $this->displayBlock('product_panel_shipping', $context, $blocks);
        // line 125
        echo "
        ";
        // line 127
        echo "        ";
        $this->displayBlock('product_panel_pricing', $context, $blocks);
        // line 134
        echo "
        ";
        // line 136
        echo "        ";
        $this->displayBlock('product_panel_seo', $context, $blocks);
        // line 142
        echo "
        ";
        // line 144
        echo "        ";
        $this->displayBlock('product_panel_options', $context, $blocks);
        // line 150
        echo "
        ";
        // line 152
        echo "        ";
        $this->displayBlock('product_panel_modules', $context, $blocks);
        // line 248
        echo "      </div>

      ";
        // line 250
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "id_product", array()), 'widget');
        echo "
      ";
        // line 251
        echo $this->env->getRuntime('Symfony\Component\Form\FormRenderer')->searchAndRenderBlock($this->getAttribute(($context["form"] ?? null), "_token", array()), 'widget');
        echo "

    </div>
    ";
        // line 255
        echo "    ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Blocks/footer.html.twig", array("preview_link" =>         // line 256
($context["preview_link"] ?? null), "preview_link_deactivate" =>         // line 257
($context["preview_link_deactivate"] ?? null), "is_shop_context" =>         // line 258
($context["is_shop_context"] ?? null), "editable" =>         // line 259
($context["editable"] ?? null), "is_active" => $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(        // line 260
($context["form"] ?? null), "step1", array()), "vars", array()), "value", array()), "active", array()), "productId" =>         // line 261
($context["id_product"] ?? null)));
        // line 262
        echo "
    ";
        // line 263
        if ( !($context["editable"] ?? null)) {
            echo " </fieldset> ";
        }
        // line 264
        echo "  </form>


  ";
        // line 267
        $this->loadTemplate("@PrestaShop/Admin/Product/ProductPage/product.html.twig", "@PrestaShop/Admin/Product/ProductPage/product.html.twig", 267, "1937039913")->display(array_merge($context, array("id" => "confirmation_modal", "title" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Warning", array(), "Admin.Notifications.Warning"), "closable" => false, "actions" => array(0 => array("type" => "button", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("No", array(), "Admin.Global"), "class" => "btn btn-outline-secondary btn-lg cancel"), 1 => array("type" => "button", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Yes", array(), "Admin.Global"), "class" => "btn btn-primary btn-lg continue")))));
        // line 288
        echo "
";
    }

    // line 33
    public function block_product_header($context, array $blocks = array())
    {
        // line 34
        echo "      ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Blocks/header.html.twig", array("formName" => $this->getAttribute($this->getAttribute(        // line 35
($context["form"] ?? null), "step1", array()), "name", array()), "formType" => $this->getAttribute($this->getAttribute(        // line 36
($context["form"] ?? null), "step1", array()), "type_product", array()), "is_multishop_context" =>         // line 37
($context["is_multishop_context"] ?? null), "languages" =>         // line 38
($context["languages"] ?? null), "help_link" =>         // line 39
($context["help_link"] ?? null), "stats_link" =>         // line 40
($context["stats_link"] ?? null)));
        // line 42
        echo "
    ";
    }

    // line 53
    public function block_product_tabs_container($context, array $blocks = array())
    {
        // line 54
        echo "        ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Blocks/tabs.html.twig");
        echo "
      ";
    }

    // line 59
    public function block_product_panel_essentials($context, array $blocks = array())
    {
        // line 60
        echo "          ";
        $context["formQuantityShortcut"] = (($this->getAttribute($this->getAttribute(($context["form"] ?? null), "step1", array(), "any", false, true), "qty_0_shortcut", array(), "any", true, true)) ? ($this->getAttribute($this->getAttribute(($context["form"] ?? null), "step1", array()), "qty_0_shortcut", array())) : (null));
        // line 61
        echo "          ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Panels/essentials.html.twig", array("formPackItems" => $this->getAttribute($this->getAttribute(        // line 62
($context["form"] ?? null), "step1", array()), "inputPackItems", array()), "productId" =>         // line 63
($context["id_product"] ?? null), "images" => $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(        // line 64
($context["form"] ?? null), "step1", array()), "vars", array()), "value", array()), "images", array()), "formShortDescription" => $this->getAttribute($this->getAttribute(        // line 65
($context["form"] ?? null), "step1", array()), "description_short", array()), "formDescription" => $this->getAttribute($this->getAttribute(        // line 66
($context["form"] ?? null), "step1", array()), "description", array()), "formFeatures" => $this->getAttribute($this->getAttribute(        // line 67
($context["form"] ?? null), "step1", array()), "features", array()), "formManufacturer" => $this->getAttribute($this->getAttribute(        // line 68
($context["form"] ?? null), "step1", array()), "id_manufacturer", array()), "formRelatedProducts" => $this->getAttribute($this->getAttribute(        // line 69
($context["form"] ?? null), "step1", array()), "related_products", array()), "is_combination_active" =>         // line 70
($context["is_combination_active"] ?? null), "has_combinations" =>         // line 71
($context["has_combinations"] ?? null), "formReference" => $this->getAttribute($this->getAttribute(        // line 72
($context["form"] ?? null), "step6", array()), "reference", array()), "formQuantityShortcut" =>         // line 73
($context["formQuantityShortcut"] ?? null), "formPriceShortcut" => $this->getAttribute($this->getAttribute(        // line 74
($context["form"] ?? null), "step1", array()), "price_shortcut", array()), "formPriceShortcutTTC" => $this->getAttribute($this->getAttribute(        // line 75
($context["form"] ?? null), "step1", array()), "price_ttc_shortcut", array()), "formCategories" => $this->getAttribute(        // line 76
($context["form"] ?? null), "step1", array())));
        // line 78
        echo "
        ";
    }

    // line 82
    public function block_product_panel_combinations($context, array $blocks = array())
    {
        // line 83
        echo "          ";
        $context["formStockQuantity"] = (($this->getAttribute($this->getAttribute(($context["form"] ?? null), "step3", array(), "any", false, true), "qty_0", array(), "any", true, true)) ? ($this->getAttribute($this->getAttribute(($context["form"] ?? null), "step3", array()), "qty_0", array())) : (null));
        // line 84
        echo "          ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Panels/combinations.html.twig", array("formDependsOnStocks" => $this->getAttribute($this->getAttribute(        // line 85
($context["form"] ?? null), "step3", array()), "depends_on_stock", array()), "productId" =>         // line 86
($context["id_product"] ?? null), "formStockQuantity" =>         // line 87
($context["formStockQuantity"] ?? null), "formStockMinimalQuantity" => $this->getAttribute($this->getAttribute(        // line 88
($context["form"] ?? null), "step3", array()), "minimal_quantity", array()), "formLowStockThreshold" => $this->getAttribute($this->getAttribute(        // line 89
($context["form"] ?? null), "step3", array()), "low_stock_threshold", array()), "formLocation" => $this->getAttribute($this->getAttribute(        // line 90
($context["form"] ?? null), "step3", array()), "location", array()), "formLowStockAlert" => $this->getAttribute($this->getAttribute(        // line 91
($context["form"] ?? null), "step3", array()), "low_stock_alert", array()), "formVirtualProduct" => $this->getAttribute($this->getAttribute(        // line 92
($context["form"] ?? null), "step3", array()), "virtual_product", array()), "asm_globally_activated" =>         // line 93
($context["asm_globally_activated"] ?? null), "formType" => $this->getAttribute($this->getAttribute(        // line 94
($context["form"] ?? null), "step1", array()), "type_product", array()), "formAdvancedStockManagement" => $this->getAttribute($this->getAttribute(        // line 95
($context["form"] ?? null), "step3", array()), "advanced_stock_management", array()), "formPackStockType" => $this->getAttribute($this->getAttribute(        // line 96
($context["form"] ?? null), "step3", array()), "pack_stock_type", array()), "formStep3" => $this->getAttribute(        // line 97
($context["form"] ?? null), "step3", array()), "formCombinations" =>         // line 98
($context["formCombinations"] ?? null), "has_combinations" =>         // line 99
($context["has_combinations"] ?? null), "max_upload_size" =>         // line 100
($context["max_upload_size"] ?? null)));
        // line 102
        echo "
        ";
    }

    // line 106
    public function block_product_panel_shipping($context, array $blocks = array())
    {
        // line 107
        echo "          <div role=\"tabpanel\" class=\"form-contenttab tab-pane\" id=\"step4\">
            <div class=\"row\">
              <div class=\"col-md-12\">
                <div class=\"container-fluid\">
                  <div class=\"row\">
                    ";
        // line 112
        echo twig_include($this->env, $context, "@Product/ProductPage/Forms/form_shipping.html.twig", array("form" => $this->getAttribute(        // line 113
($context["form"] ?? null), "step4", array()), "asm_globally_activated" =>         // line 114
($context["asm_globally_activated"] ?? null), "isNotVirtual" => ($this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(        // line 115
($context["form"] ?? null), "step1", array()), "type_product", array()), "vars", array()), "value", array()) != "2"), "isChecked" => $this->getAttribute($this->getAttribute($this->getAttribute($this->getAttribute(        // line 116
($context["form"] ?? null), "step3", array()), "advanced_stock_management", array()), "vars", array()), "checked", array()), "warehouses" =>         // line 117
($context["warehouses"] ?? null)));
        // line 118
        echo "
                  </div>
                </div>
              </div>
            </div>
          </div>
        ";
    }

    // line 127
    public function block_product_panel_pricing($context, array $blocks = array())
    {
        // line 128
        echo "          ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Panels/pricing.html.twig", array("pricingForm" => $this->getAttribute(        // line 129
($context["form"] ?? null), "step2", array()), "is_multishop_context" =>         // line 130
($context["is_multishop_context"] ?? null), "productId" =>         // line 131
($context["id_product"] ?? null)));
        // line 132
        echo "
        ";
    }

    // line 136
    public function block_product_panel_seo($context, array $blocks = array())
    {
        // line 137
        echo "          ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Panels/seo.html.twig", array("seoForm" => $this->getAttribute(        // line 138
($context["form"] ?? null), "step5", array()), "productId" =>         // line 139
($context["id_product"] ?? null)));
        // line 140
        echo "
        ";
    }

    // line 144
    public function block_product_panel_options($context, array $blocks = array())
    {
        // line 145
        echo "          ";
        echo twig_include($this->env, $context, "@Product/ProductPage/Panels/options.html.twig", array("optionsForm" => $this->getAttribute(        // line 146
($context["form"] ?? null), "step6", array()), "productId" =>         // line 147
($context["id_product"] ?? null)));
        // line 148
        echo "
        ";
    }

    // line 152
    public function block_product_panel_modules($context, array $blocks = array())
    {
        // line 153
        echo "          ";
        if (($this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->hookCount("displayAdminProductsExtra") > 0)) {
            // line 154
            echo "            <div role=\"tabpanel\" class=\"form-contenttab tab-pane\" id=\"hooks\">
              <div class=\"row\">
                <div class=\"col-md-12\">
                  <div class=\"container-fluid\">
                    <div class=\"row\">

                      ";
            // line 161
            echo "                      <div class=\"col-md-12\">
                        ";
            // line 162
            $context["hooks"] = $this->env->getExtension('PrestaShopBundle\Twig\HookExtension')->renderHooksArray("displayAdminProductsExtra", array("id_product" => ($context["id_product"] ?? null)));
            // line 163
            echo "
                        <div class=\"row module-selection\" style=\"display: none;\">
                          <div class=\"col-md-12 col-lg-7\">
                            ";
            // line 166
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["hooks"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["module"]) {
                // line 167
                echo "                              <div class=\"module-render-container module-";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "name", array()), "html", null, true);
                echo "\">
                                <div>
                                  <img class=\"top-logo\" src=\"";
                // line 169
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "img", array()), "html", null, true);
                echo "\" alt=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "displayName", array()), "html", null, true);
                echo "\">
                                  <h2 class=\"text-ellipsis module-name-grid\">
                                    ";
                // line 171
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "displayName", array()), "html", null, true);
                echo "
                                  </h2>
                                  <div class=\"text-ellipsis small-text module-version\">
                                    ";
                // line 174
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "version", array()), "html", null, true);
                echo " by ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "author", array()), "html", null, true);
                echo "
                                  </div>
                                </div>
                                <div class=\"small no-padding\">
                                  ";
                // line 178
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "description", array()), "html", null, true);
                echo "
                                </div>
                              </div>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['module'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 182
            echo "                          </div>
                          <div class=\"col-md-12 col-lg-5\">
                            <h2>";
            // line 184
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Module to configure", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</h2>
                            <select class=\"modules-list-select\" data-toggle=\"select2\">
                              ";
            // line 186
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["hooks"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["module"]) {
                // line 187
                echo "                                <option value=\"module-";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "name", array()), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "displayName", array()), "html", null, true);
                echo "</option>
                              ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['module'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 189
            echo "                            </select>
                          </div>
                        </div>

                        <div class=\"module-render-container all-modules\">
                          <p>
                            <h2>";
            // line 195
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Choose a module to configure", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "</h2>
                            ";
            // line 196
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("These modules are relative to the product page of your shop.", array(), "Admin.Catalog.Feature"), "html", null, true);
            echo "<br />
                            ";
            // line 197
            echo twig_replace_filter($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("To manage all your modules go to the [1]Installed module page[/1]", array(), "Admin.Catalog.Feature"), array("[1]" => (("<a href=\"" . $this->env->getExtension('Symfony\Bridge\Twig\Extension\RoutingExtension')->getPath("admin_module_manage")) . "\">"), "[/1]" => "</a>"));
            echo "
                          </p>
                          <div class=\"row\">
                            ";
            // line 200
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["hooks"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["module"]) {
                // line 201
                echo "                              <div class=\"col-md-12 col-lg-6 col-xl-4\">
                                <div class=\"module-item-wrapper-grid\">
                                  <div class=\"module-item-heading-grid\">
                                    <img class=\"module-logo-thumb-grid\" src=\"";
                // line 204
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "img", array()), "html", null, true);
                echo "\" alt=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "displayName", array()), "html", null, true);
                echo "\">
                                    <h3 class=\"text-ellipsis module-name-grid\">
                                      ";
                // line 206
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "displayName", array()), "html", null, true);
                echo "
                                    </h3>
                                    <div class=\"text-ellipsis small-text module-version-author-grid\">
                                      ";
                // line 209
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "version", array()), "html", null, true);
                echo " by ";
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "author", array()), "html", null, true);
                echo "
                                    </div>
                                  </div>
                                  <div class=\"module-quick-description-grid small no-padding\">
                                    ";
                // line 213
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "description", array()), "html", null, true);
                echo "
                                  </div>
                                  <div class=\"module-container\">
                                    <div class=\"module-quick-action-grid clearfix\">
                                      <button class=\"modules-list-button btn btn-outline-primary pull-xs-right\" data-target=\"module-";
                // line 217
                echo twig_escape_filter($this->env, $this->getAttribute($context["module"], "id", array()), "html", null, true);
                echo "\">
                                        ";
                // line 218
                echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Configure", array(), "Admin.Actions"), "html", null, true);
                echo "
                                      </button>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['module'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 225
            echo "                          </div>
                        </div>

                        ";
            // line 228
            $context['_parent'] = $context;
            $context['_seq'] = twig_ensure_traversable(($context["hooks"] ?? null));
            foreach ($context['_seq'] as $context["_key"] => $context["module"]) {
                // line 229
                echo "                          <div
                            id=\"module_";
                // line 230
                echo twig_escape_filter($this->env, $this->getAttribute($context["module"], "id", array()), "html", null, true);
                echo "\"
                            class=\"module-render-container module-";
                // line 231
                echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($context["module"], "attributes", array()), "name", array()), "html", null, true);
                echo "\"
                            style=\"display: none;\"
                          >
                            <div>
                              ";
                // line 235
                echo $this->getAttribute($context["module"], "content", array());
                echo "
                            </div>
                          </div>
                        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['module'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 239
            echo "                      </div>

                    </div>
                  </div>
                </div>
              </div>
            </div>
          ";
        }
        // line 247
        echo "        ";
    }

    // line 291
    public function block_javascripts($context, array $blocks = array())
    {
        // line 292
        echo "  ";
        $this->displayParentBlock("javascripts", $context, $blocks);
        echo "
  <script src=\"";
        // line 293
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/form.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 294
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/new-theme/public/catalog_product.bundle.js"), "html", null, true);
        echo "\"></script>

  <script src=\"";
        // line 296
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/product-manufacturer.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 297
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/product-related.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 298
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/product-category-tags.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 299
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/default-category.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 300
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/product/product-combinations.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 301
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/category-tree.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 302
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/module/module_card.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 303
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("themes/default/js/bundle/modal-confirmation.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 304
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("../js/tiny_mce/tiny_mce.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 305
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("../js/admin/tinymce.inc.js"), "html", null, true);
        echo "\"></script>
  <script src=\"";
        // line 306
        echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\AssetExtension')->getAssetUrl("../js/admin/tinymce_loader.js"), "html", null, true);
        echo "\"></script>

  <script>
      \$(function() {
        var editable = '";
        // line 310
        echo twig_escape_filter($this->env, ($context["editable"] ?? null), "html", null, true);
        echo "';

        if (editable !== '1'){
          \$('#field-disabled').find(\"select\").each(function() {
            \$(this).removeClass('select2-hidden-accessible');
          });
          \$('#field-disabled').find(\"span.select2\").each(function() {
            \$(this).hide();
          });
          \$('#field-disabled').find(\"a.pstaggerClosingCross\").each(function() {
            \$(this).attr(\"disabled\", \"disabled\").on(\"click\", function() {
              return false;
            });
          });
        }
      });
  </script>
";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/ProductPage/product.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  596 => 310,  589 => 306,  585 => 305,  581 => 304,  577 => 303,  573 => 302,  569 => 301,  565 => 300,  561 => 299,  557 => 298,  553 => 297,  549 => 296,  544 => 294,  540 => 293,  535 => 292,  532 => 291,  528 => 247,  518 => 239,  508 => 235,  501 => 231,  497 => 230,  494 => 229,  490 => 228,  485 => 225,  472 => 218,  468 => 217,  461 => 213,  452 => 209,  446 => 206,  439 => 204,  434 => 201,  430 => 200,  424 => 197,  420 => 196,  416 => 195,  408 => 189,  397 => 187,  393 => 186,  388 => 184,  384 => 182,  374 => 178,  365 => 174,  359 => 171,  352 => 169,  346 => 167,  342 => 166,  337 => 163,  335 => 162,  332 => 161,  324 => 154,  321 => 153,  318 => 152,  313 => 148,  311 => 147,  310 => 146,  308 => 145,  305 => 144,  300 => 140,  298 => 139,  297 => 138,  295 => 137,  292 => 136,  287 => 132,  285 => 131,  284 => 130,  283 => 129,  281 => 128,  278 => 127,  268 => 118,  266 => 117,  265 => 116,  264 => 115,  263 => 114,  262 => 113,  261 => 112,  254 => 107,  251 => 106,  246 => 102,  244 => 100,  243 => 99,  242 => 98,  241 => 97,  240 => 96,  239 => 95,  238 => 94,  237 => 93,  236 => 92,  235 => 91,  234 => 90,  233 => 89,  232 => 88,  231 => 87,  230 => 86,  229 => 85,  227 => 84,  224 => 83,  221 => 82,  216 => 78,  214 => 76,  213 => 75,  212 => 74,  211 => 73,  210 => 72,  209 => 71,  208 => 70,  207 => 69,  206 => 68,  205 => 67,  204 => 66,  203 => 65,  202 => 64,  201 => 63,  200 => 62,  198 => 61,  195 => 60,  192 => 59,  185 => 54,  182 => 53,  177 => 42,  175 => 40,  174 => 39,  173 => 38,  172 => 37,  171 => 36,  170 => 35,  168 => 34,  165 => 33,  160 => 288,  158 => 267,  153 => 264,  149 => 263,  146 => 262,  144 => 261,  143 => 260,  142 => 259,  141 => 258,  140 => 257,  139 => 256,  137 => 255,  131 => 251,  127 => 250,  123 => 248,  120 => 152,  117 => 150,  114 => 144,  111 => 142,  108 => 136,  105 => 134,  102 => 127,  99 => 125,  96 => 106,  93 => 104,  90 => 82,  87 => 80,  84 => 59,  80 => 56,  77 => 53,  69 => 47,  64 => 44,  61 => 33,  59 => 32,  55 => 31,  50 => 28,  47 => 27,  43 => 25,  41 => 352,  40 => 351,  39 => 350,  38 => 333,  36 => 331,  35 => 329,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/ProductPage/product.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/product.html.twig");
    }
}


/* @PrestaShop/Admin/Product/ProductPage/product.html.twig */
class __TwigTemplate_8ae68f1fbb020365a5e7ccde64bc18d57dfe073d9e3e5d77b4c8fc38873a1769_1937039913 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        // line 267
        $this->parent = $this->loadTemplate("@PrestaShop/Admin/Helpers/bootstrap_popup.html.twig", "@PrestaShop/Admin/Product/ProductPage/product.html.twig", 267);
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

    // line 284
    public function block_content($context, array $blocks = array())
    {
        // line 285
        echo "      <div class=\"modal-body\"></div>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/ProductPage/product.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  675 => 285,  672 => 284,  655 => 267,  596 => 310,  589 => 306,  585 => 305,  581 => 304,  577 => 303,  573 => 302,  569 => 301,  565 => 300,  561 => 299,  557 => 298,  553 => 297,  549 => 296,  544 => 294,  540 => 293,  535 => 292,  532 => 291,  528 => 247,  518 => 239,  508 => 235,  501 => 231,  497 => 230,  494 => 229,  490 => 228,  485 => 225,  472 => 218,  468 => 217,  461 => 213,  452 => 209,  446 => 206,  439 => 204,  434 => 201,  430 => 200,  424 => 197,  420 => 196,  416 => 195,  408 => 189,  397 => 187,  393 => 186,  388 => 184,  384 => 182,  374 => 178,  365 => 174,  359 => 171,  352 => 169,  346 => 167,  342 => 166,  337 => 163,  335 => 162,  332 => 161,  324 => 154,  321 => 153,  318 => 152,  313 => 148,  311 => 147,  310 => 146,  308 => 145,  305 => 144,  300 => 140,  298 => 139,  297 => 138,  295 => 137,  292 => 136,  287 => 132,  285 => 131,  284 => 130,  283 => 129,  281 => 128,  278 => 127,  268 => 118,  266 => 117,  265 => 116,  264 => 115,  263 => 114,  262 => 113,  261 => 112,  254 => 107,  251 => 106,  246 => 102,  244 => 100,  243 => 99,  242 => 98,  241 => 97,  240 => 96,  239 => 95,  238 => 94,  237 => 93,  236 => 92,  235 => 91,  234 => 90,  233 => 89,  232 => 88,  231 => 87,  230 => 86,  229 => 85,  227 => 84,  224 => 83,  221 => 82,  216 => 78,  214 => 76,  213 => 75,  212 => 74,  211 => 73,  210 => 72,  209 => 71,  208 => 70,  207 => 69,  206 => 68,  205 => 67,  204 => 66,  203 => 65,  202 => 64,  201 => 63,  200 => 62,  198 => 61,  195 => 60,  192 => 59,  185 => 54,  182 => 53,  177 => 42,  175 => 40,  174 => 39,  173 => 38,  172 => 37,  171 => 36,  170 => 35,  168 => 34,  165 => 33,  160 => 288,  158 => 267,  153 => 264,  149 => 263,  146 => 262,  144 => 261,  143 => 260,  142 => 259,  141 => 258,  140 => 257,  139 => 256,  137 => 255,  131 => 251,  127 => 250,  123 => 248,  120 => 152,  117 => 150,  114 => 144,  111 => 142,  108 => 136,  105 => 134,  102 => 127,  99 => 125,  96 => 106,  93 => 104,  90 => 82,  87 => 80,  84 => 59,  80 => 56,  77 => 53,  69 => 47,  64 => 44,  61 => 33,  59 => 32,  55 => 31,  50 => 28,  47 => 27,  43 => 25,  41 => 352,  40 => 351,  39 => 350,  38 => 333,  36 => 331,  35 => 329,  11 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/ProductPage/product.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/ProductPage/product.html.twig");
    }
}

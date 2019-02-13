<?php

/* @PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig */
class __TwigTemplate_6a2b92c6bf4e365b86b4f02a53191e904b3349f74438a425999aa2d37f74a204 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'product_catalog_form_table_row' => array($this, 'block_product_catalog_form_table_row'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 25
        echo "<tbody
    ";
        // line 26
        if (($context["activate_drag_and_drop"] ?? null)) {
            echo "class=\"sortable\"";
        }
        // line 27
        echo "    last_sql=\"";
        echo twig_escape_filter($this->env, ($context["last_sql_query"] ?? null), "html_attr");
        echo "\"
>
    ";
        // line 29
        $context['_parent'] = $context;
        $context['_seq'] = twig_ensure_traversable(($context["products"] ?? null));
        $context['_iterated'] = false;
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["_key"] => $context["product"]) {
            // line 30
            echo "    ";
            $this->displayBlock('product_catalog_form_table_row', $context, $blocks);
            $context['_iterated'] = true;
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        if (!$context['_iterated']) {
            // line 137
            echo "<tr><td colspan=\"11\">
        ";
            // line 138
            echo twig_escape_filter($this->env, $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("There is no result for this search. Update your filters to view other products.", array(), "Admin.Catalog.Notification"), "html", null, true);
            echo "
    </td></tr>
";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['product'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
    }

    // line 30
    public function block_product_catalog_form_table_row($context, array $blocks = array())
    {
        // line 31
        echo "    <tr data-uniturl=\"";
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "unit_action_url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "unit_action_url", array()), "#")) : ("#")), "html", null, true);
        echo "\" data-product-id=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "id_product", array()), "html", null, true);
        echo "\">
        <td class=\"checkbox-column form-group\">
            <div class=\"md-checkbox md-checkbox-inline\">
                <label>
                    <input type=\"checkbox\" id=\"bulk_action_selected_products-";
        // line 35
        echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "id_product", array()), "html", null, true);
        echo "\" name=\"bulk_action_selected_products[]\" value=\"";
        echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "id_product", array()), "html", null, true);
        echo "\">
                    <i class=\"md-checkbox-control\"></i>
                </label>
            </div>
        </td>
        <td>
            <label class=\"form-check-label\" for=\"bulk_action_selected_products-";
        // line 41
        echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "id_product", array()), "html", null, true);
        echo "\">
                ";
        // line 42
        echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "id_product", array()), "html", null, true);
        echo "
            </label>
        </td>
        <td>
            <a href=\"";
        // line 46
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "url", array()), "")) : ("")), "html", null, true);
        echo "#tab-step1\">";
        echo $this->getAttribute(($context["product"] ?? null), "image", array());
        echo "</a>
        </td>
        <td>
            <a href=\"";
        // line 49
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "url", array()), "")) : ("")), "html", null, true);
        echo "#tab-step1\">";
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "name", array()), $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("N/A", array(), "Admin.Global"))) : ($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("N/A", array(), "Admin.Global"))), "html", null, true);
        echo "</a>
        </td>
        <td>
            ";
        // line 52
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "reference", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "reference", array()), "")) : ("")), "html", null, true);
        echo "
        </td>
        <td>
            ";
        // line 55
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "name_category", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "name_category", array()), "")) : ("")), "html", null, true);
        echo "
        </td>
        <td class=\"text-center\">
            <a href=\"";
        // line 58
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "url", array()), "")) : ("")), "html", null, true);
        echo "#tab-step2\">";
        echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "price", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "price", array()), $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("N/A", array(), "Admin.Global"))) : ($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("N/A", array(), "Admin.Global"))), "html", null, true);
        echo "</a>
        </td>

        ";
        // line 61
        if ($this->env->getExtension('PrestaShopBundle\Twig\LayoutExtension')->getConfiguration("PS_STOCK_MANAGEMENT")) {
            // line 62
            echo "            <td class=\"product-sav-quantity text-center\" data-product-quantity-value=\"";
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "sav_quantity", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "sav_quantity", array()), "")) : ("")), "html", null, true);
            echo "\">
                <a href=\"";
            // line 63
            echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "url", array()), "")) : ("")), "html", null, true);
            echo "#tab-step3\">
                    ";
            // line 64
            if (($this->getAttribute(($context["product"] ?? null), "sav_quantity", array(), "any", true, true) && ($this->getAttribute(($context["product"] ?? null), "sav_quantity", array()) > 0))) {
                // line 65
                echo "                        ";
                echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "sav_quantity", array()), "html", null, true);
                echo "
                    ";
            } else {
                // line 67
                echo "                        ";
                echo twig_escape_filter($this->env, (($this->getAttribute(($context["product"] ?? null), "sav_quantity", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "sav_quantity", array()), $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("N/A", array(), "Admin.Global"))) : ($this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("N/A", array(), "Admin.Global"))), "html", null, true);
                echo "
                    ";
            }
            // line 69
            echo "                </a>
            </td>
        ";
        } else {
            // line 72
            echo "            <td></td>
        ";
        }
        // line 74
        echo "        <td class=\"text-center\">
            ";
        // line 75
        if (((($this->getAttribute(($context["product"] ?? null), "active", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "active", array()), 0)) : (0)) == 0)) {
            // line 76
            echo "                <a href=\"#\" onclick=\"unitProductAction(this, 'activate'); return false;\">
                    <i class=\"material-icons action-disabled\">clear</i>
                </a>
            ";
        } else {
            // line 80
            echo "                <a href=\"#\" onclick=\"unitProductAction(this, 'deactivate'); return false;\">
                    <i class=\"material-icons action-enabled \">check</i>
                </a>
            ";
        }
        // line 84
        echo "        </td>
        ";
        // line 85
        if ($this->getAttribute(($context["product"] ?? null), "position", array(), "any", true, true)) {
            // line 86
            echo "            <td ";
            if (($context["activate_drag_and_drop"] ?? null)) {
                echo "class=\"placeholder\"";
            }
            echo " style=\"cursor: pointer; cursor: hand;\">
                ";
            // line 87
            if (($context["activate_drag_and_drop"] ?? null)) {
                // line 88
                echo "                    <big><big>⇅</big></big>
                ";
            }
            // line 90
            echo "                <span class=\"position\">";
            echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "position", array()), "html", null, true);
            echo "</span>
                <input type=\"hidden\" name=\"mass_edit_action_sorted_products[]\" value=\"";
            // line 91
            echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "id_product", array()), "html", null, true);
            echo "\" />
                <input type=\"hidden\" name=\"mass_edit_action_sorted_positions[]\" value=\"";
            // line 92
            echo twig_escape_filter($this->env, $this->getAttribute(($context["product"] ?? null), "position", array()), "html", null, true);
            echo "\" />
            </td>
        ";
        }
        // line 95
        echo "        <td class=\"text-right\">
            <div class=\"btn-group-action\">

                ";
        // line 98
        $context["buttons_action"] = array(0 => array("href" => (($this->getAttribute(        // line 100
($context["product"] ?? null), "preview_url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "preview_url", array()), "#")) : ("#")), "target" => "_blank", "icon" => "remove_red_eye", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Preview", array(), "Admin.Actions")));
        // line 106
        echo "
                ";
        // line 107
        $context["buttons_action"] = twig_array_merge(($context["buttons_action"] ?? null), array(0 => array("onclick" => "unitProductAction(this, 'duplicate');", "icon" => "content_copy", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Duplicate", array(), "Admin.Actions"))));
        // line 114
        echo "
                ";
        // line 115
        $context["buttons_action"] = twig_array_merge(($context["buttons_action"] ?? null), array(0 => array("onclick" => "unitProductAction(this, 'delete');", "icon" => "delete", "label" => $this->env->getExtension('Symfony\Bridge\Twig\Extension\TranslationExtension')->trans("Delete", array(), "Admin.Actions"))));
        // line 122
        echo "
                ";
        // line 123
        $this->loadTemplate("@Product/CatalogPage/Forms/form_edit_dropdown.html.twig", "@PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig", 123)->display(array_merge($context, array("button_id" => (("product_list_id_" . $this->getAttribute(        // line 124
($context["product"] ?? null), "id_product", array())) . "_menu"), "default_item" => array("href" => (($this->getAttribute(        // line 126
($context["product"] ?? null), "url", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute(($context["product"] ?? null), "url", array()), "#")) : ("#")), "icon" => "mode_edit"), "right" => true, "items" =>         // line 130
($context["buttons_action"] ?? null))));
        // line 132
        echo "            </div>
        </td>
    </tr>
    ";
    }

    public function getTemplateName()
    {
        return "@PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  260 => 132,  258 => 130,  257 => 126,  256 => 124,  255 => 123,  252 => 122,  250 => 115,  247 => 114,  245 => 107,  242 => 106,  240 => 100,  239 => 98,  234 => 95,  228 => 92,  224 => 91,  219 => 90,  215 => 88,  213 => 87,  206 => 86,  204 => 85,  201 => 84,  195 => 80,  189 => 76,  187 => 75,  184 => 74,  180 => 72,  175 => 69,  169 => 67,  163 => 65,  161 => 64,  157 => 63,  152 => 62,  150 => 61,  142 => 58,  136 => 55,  130 => 52,  122 => 49,  114 => 46,  107 => 42,  103 => 41,  92 => 35,  82 => 31,  79 => 30,  68 => 138,  65 => 137,  51 => 30,  33 => 29,  27 => 27,  23 => 26,  20 => 25,);
    }

    /** @deprecated since 1.27 (to be removed in 2.0). Use getSourceContext() instead */
    public function getSource()
    {
        @trigger_error('The '.__METHOD__.' method is deprecated since version 1.27 and will be removed in 2.0. Use getSourceContext() instead.', E_USER_DEPRECATED);

        return $this->getSourceContext()->getCode();
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "@PrestaShop/Admin/Product/CatalogPage/Lists/list.html.twig", "/var/www/html/src/PrestaShopBundle/Resources/views/Admin/Product/CatalogPage/Lists/list.html.twig");
    }
}

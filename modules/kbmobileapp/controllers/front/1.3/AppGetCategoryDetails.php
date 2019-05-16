<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * API to get product details of selected category in APP
 */

require_once 'AppCore.php';

require_once 'AppSearchedProducts.php';

class AppGetCategoryDetails extends AppCore
{
    private $search_term = '';

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $proceed = true;
        if (!(int) Tools::getValue('category_id', 0) && !Tools::getIsset('search_term')) {
            $proceed = false;
            $this->content['filter_result'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Category id is missing'),
                    'AppGetCategoryDetails'
                )
            );
        } elseif (!(int) Tools::getValue('category_id', 0)) {
            if (!Tools::getIsset('search_term') || !Tools::getValue('search_term', '')) {
                $proceed = false;
                $this->content['filter_result'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Search term is missing'),
                        'AppGetCategoryDetails'
                    )
                );
            } else {
                $this->search_term = Tools::getValue('search_term', '');
            }
        }

        if ($proceed) {
            $this->content['fproducts'] = $this->getProductList();
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Get product list of selected category
     *
     * @return array products data
     */
    public function getProductList()
    {
        $this->setListPagingData();
        $selected_filter = $this->getSelectedFilters();
        if (!empty($this->search_term)) {
            $search_p = new AppSearchedProducts($this);
            $cat_products = $search_p->getProducts($this->search_term, $selected_filter);
        } else {
            $cat_products = $this->getProductByFilters($selected_filter);
        }

        $cat_products = Product::getProductsProperties((int) $this->context->language->id, $cat_products);

        $response = array();
        if (!empty($cat_products)) {
            $index = 0;
            foreach ($cat_products as &$product) {
                if (isset($product['id_product_attribute'])
                    && $product['id_product_attribute']
                    && isset($product['product_attribute_minimal_quantity'])) {
                    $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
                }

                $response[$index] = array(
                    'id' => $product['id_product'],
                    'is_in_wishlist' => $this->isProductHasInWishlist($product['id_product']),
                    'name' => $product['name'],
                    'available_for_order' => $product['available_for_order'],
                    'show_price' => $product['show_price'],
                    'new_products' => (isset($product['new']) && $product['new'] == 1) ? "1" : "0",
                    'on_sale_products' => $product['on_sale'],
                    'price' => $this->formatPrice($product['price_without_reduction']),
                    'out_of_stock' => isset($product['out_of_stock']) ?  $product['out_of_stock'] : "0",
                    'src' => $this->context->link->getImageLink(
                        $product['link_rewrite'],
                        $product['id_image'],
                        $this->getImageType('large')
                    )
                );
                if (count($product['specific_prices']) > 0) {
                    $response[$index]['discount_price'] = $this->formatPrice($product['price']);
                    if ($product['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                        $temp_price = (float) $product['specific_prices']['reduction'] * 100;
                        $response[$index]['discount_percentage'] = $temp_price;
                        unset($temp_price);
                    } else {
                        if ($product['price_without_reduction']) {
                            $temp_price = (float) ($product['specific_prices']['reduction'] * 100);
                            $percent = (float) ($temp_price / $product['price_without_reduction']);
                        } else {
                            $percent = 0;
                        }
                        $response[$index]['discount_percentage'] = Tools::ps_round($percent);
                    }
                } else {
                    $response[$index]['discount_price'] = '';
                    $response[$index]['discount_percentage'] = '';
                }
                $index++;
            }
        }

        $title = '';
        if ($cat_id = Tools::getValue('category_id', 0)) {
            $category = new Category((int)$cat_id, Context::getContext()->language->id);
            $title = $category->name;
        } else if (Tools::getIsset('search_term') && Tools::getValue('search_term') != '') {
            $title = Tools::getValue('search_term');
        }
        
        return array('title' => $title, 'products' => $response);
    }

    
    /**
     * Set pagination data i.e order by and order way values
     *
     */
    public function setListPagingData()
    {
        parent::setListPagingData();

        $sort_values = array('price');

        if (!in_array($this->order_by, $sort_values)) {
            $this->order_by = 'price';
        }

        if ($this->order_way == 'low') {
            $this->order_way = 'asc';
        } else {
            $this->order_way = 'desc';
        }

        $_POST['orderby'] = $this->order_by;
    }

    /**
     * Get Selected filters in APP
     *
     */
    private function getSelectedFilters()
    {
        $post_filters = Tools::getValue('filter', Tools::jsonEncode(array()));
        $post_filters = Tools::jsonDecode($post_filters);
        $filters = array();
        if (!empty($post_filters)) {
            foreach ($post_filters->filter_result as $filt) {
                if (isset($filt->items) && count($filt->items) > 0) {
                    $filter = explode('|', $filt->title);
                    if ($filter[0] == 'price') {
                        $name = 'layered_' . $filter[0] . '_slider';
                        $filters[$name] = $filt->items[0]->id;
                    } else {
                        foreach ($filt->items as $item) {
                            $name = 'layered_' . $filter[0] . '_' . $item->id;
                            if ($filter[1] == 0) {
                                $value = $item->id;
                            } else {
                                $value = $item->id . '_' . $filter[1];
                            }
                            $filters[$name] = $value;
                        }
                    }
                }
            }
        }
        unset($_POST['filter']);

        $_GET = array_merge($_GET, $filters);

        if (empty($this->search_term)) {
            $home_category = Configuration::get('PS_HOME_CATEGORY');
            $id_parent = (int) Tools::getValue('category_id', $home_category);
            if ($id_parent == $home_category) {
                return array();
            }
        }

        $selected_filters = array();
        foreach ($_GET as $key => $value) {
            if (Tools::substr($key, 0, 8) == 'layered_') {
                preg_match(
                    '/^(.*)_([0-9]+|new|used|refurbished|slider)$/',
                    Tools::substr($key, 8, Tools::strlen($key) - 8),
                    $res
                );
                if (isset($res[1])) {
                    $tmp_tab = explode('_', $this->filterVar($value));
                    $value = $this->filterVar($tmp_tab[0]);
                    $id_key = false;
                    if (isset($tmp_tab[1])) {
                        $id_key = $tmp_tab[1];
                    }
                    if ($res[1] == 'condition' && in_array($value, array('new', 'used', 'refurbished'))) {
                        $selected_filters['condition'][] = $value;
                    } elseif ($res[1] == 'quantity' && (!$value || $value == 1)) {
                        $selected_filters['quantity'][] = $value;
                    } elseif (in_array($res[1], array('category', 'manufacturer'))) {
                        if (!isset($selected_filters[$res[1] . ($id_key ? '_' . $id_key : '')])) {
                            $selected_filters[$res[1] . ($id_key ? '_' . $id_key : '')] = array();
                        }
                        $selected_filters[$res[1] . ($id_key ? '_' . $id_key : '')][] = (int) $value;
                    } elseif (in_array($res[1], array('id_attribute_group', 'id_feature'))) {
                        if (!isset($selected_filters[$res[1]])) {
                            $selected_filters[$res[1]] = array();
                        }
                        $selected_filters[$res[1]][(int) $value] = $id_key . '_' . (int) $value;
                    } elseif ($res[1] == 'weight') {
                        $selected_filters[$res[1]] = $tmp_tab;
                    } elseif ($res[1] == 'price') {
                        $selected_filters[$res[1]] = $tmp_tab;
                    }
                }
            }
        }

        return $selected_filters;
    }

    /**
     * Get product list by selected filters
     *
     * @param array $selected_filters selected filters array
     */
    public function getProductByFilters($selected_filters = array())
    {
        $home_category = Configuration::get('PS_HOME_CATEGORY');

        $id_parent = (int) Tools::getValue('category_id', $home_category);
        if ($id_parent == $home_category) {
            return array();
        }

        $alias_where = 'p';
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $alias_where = 'product_shop';
        }

        $query_filters_where = ' AND ' . pSQL($alias_where) . '.`active` = 1 '
                . 'AND ' . pSQL($alias_where) . '.`visibility` IN ("both", "catalog")';
        $query_filters_from = '';

        $parent = new Category((int) $id_parent);

        foreach ($selected_filters as $key => $filter_values) {
            if (!count($filter_values)) {
                continue;
            }

            preg_match('/^(.*[^_0-9])/', $key, $res);
            $key = $res[1];

            switch ($key) {
                case 'id_feature':
                    $sub_queries = array();
                    foreach ($filter_values as $filter_value) {
                        $filter_value_array = explode('_', $filter_value);
                        if (!isset($sub_queries[$filter_value_array[0]])) {
                            $sub_queries[$filter_value_array[0]] = array();
                        }
                        $sub_queries[$filter_value_array[0]][] = 'fp.`id_feature_value`='.(int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query) {
                        $query_filters_where .= ' AND p.id_product IN (SELECT `id_product` '
                                . 'FROM `' . _DB_PREFIX_ . 'feature_product` fp WHERE ';
                        $query_filters_where .= pSQL(implode(' OR ', $sub_query)) . ') ';
                    }
                    break;

                case 'id_attribute_group':
                    $sub_queries = array();


                    foreach ($filter_values as $filter_value) {
                        $filter_value_array = explode('_', $filter_value);
                        if (!isset($sub_queries[$filter_value_array[0]])) {
                            $sub_queries[$filter_value_array[0]] = array();
                        }
                        $sub_queries[$filter_value_array[0]][] = 'pac.`id_attribute` = '.(int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query) {
                        $query_filters_where .= ' AND p.id_product IN (SELECT pa.`id_product`
                                            FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
                                            LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
                                            ON (pa.`id_product_attribute` = pac.`id_product_attribute`)' .
                                Shop::addSqlAssociation('product_attribute', 'pa') . '
                                            WHERE ' . pSQL(implode(' OR ', $sub_query)) . ') ';
                    }
                    break;

                case 'category':
                    $query_filters_where .= ' AND p.id_product IN (SELECT id_product '
                        . 'FROM ' . _DB_PREFIX_ . 'category_product cp WHERE ';
                    foreach ($selected_filters['category'] as $id_category) {
                        $query_filters_where .= 'cp.`id_category` = ' . (int) $id_category . ' OR ';
                    }
                    $query_filters_where = pSQL(rtrim($query_filters_where, 'OR ')) . ')';
                    break;

                case 'quantity':
                    if (count($selected_filters['quantity']) == 2) {
                        break;
                    }

                    $query_filters_where .= ' AND sa.quantity '.(!$selected_filters['quantity'][0] ? '<=' : '>').' 0 ';
                    $query_filters_from .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa '
                            . 'ON (sa.id_product = p.id_product '
                            . StockAvailable::addSqlShopRestriction(null, null, 'sa') . ') ';
                    break;

                case 'manufacturer':
                    $query_filters_where .= ' AND p.id_manufacturer IN '
                        . '(' . pSQL(implode($selected_filters['manufacturer'], ',')) . ')';
                    break;

                case 'condition':
                    if (count($selected_filters['condition']) == 3) {
                        break;
                    }
                    $query_filters_where .= ' AND ' . pSQL($alias_where) . '.condition IN (';
                    foreach ($selected_filters['condition'] as $cond) {
                        $query_filters_where .= '"' . $cond . '",';
                    }
                    $query_filters_where = rtrim($query_filters_where, ',') . ')';
                    $query_filters_where = trim(preg_replace('/\s\s+/', ' ', $query_filters_where));
                    break;

                case 'weight':
                    if ($selected_filters['weight'][0] != 0 || $selected_filters['weight'][1] != 0) {
                        $query_filters_where .= ' AND p.`weight` BETWEEN '
                                . (float) ($selected_filters['weight'][0] - 0.001) . ' AND '
                                . (float) ($selected_filters['weight'][1] + 0.001);
                    }
                    break;

                case 'price':
                    if (isset($selected_filters['price'])) {
                        if ($selected_filters['price'][0] !== '' || $selected_filters['price'][1] !== '') {
                            $price_filter = array();
                            $price_filter['min'] = (float) ($selected_filters['price'][0]);
                            $price_filter['max'] = (float) ($selected_filters['price'][1]);
                        }
                    } else {
                        $price_filter = false;
                    }
                    break;
            }
        }

        $id_currency = (int) $this->context->currency->id;

        $price_filter_query_in = '';
        $price_filter_query_out = '';
        if (isset($price_filter) && $price_filter) {
            $price_filter_query_in = 'INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi'.
                   ' ON( psi.price_min <= ' . (int) $price_filter['max'] . '
                        AND psi.price_max >= ' . (int) $price_filter['min'] . '
                        AND psi.`id_product` = p.`id_product`
                        AND psi.`id_shop` = ' . (int) $this->context->shop->id . '
                        AND psi.`id_currency` = ' . (int) $id_currency . '
                    )';

            $price_filter_query_out = 'INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi'.
                    ' ON ((psi.price_min < ' . (int) $price_filter['min'] . ' AND '
                    . 'psi.price_max > ' . (int) $price_filter['min'] . ') OR '.
                    '(psi.price_max > '.(int)$price_filter['max'].' AND psi.price_min < '.(int)$price_filter['max'].'))
                    AND psi.`id_product` = p.`id_product`
                    AND psi.`id_shop` = ' . (int) $this->context->shop->id . '
                    AND psi.`id_currency` = ' . (int) $id_currency;
        }

        $search_term_query = '';
        if (!empty($this->search_term)) {
            $search_term_query .= 'INNER JOIN ' . _DB_PREFIX_ . 'product_lang psearch 
                        ON (
                                p.id_product = psearch.id_product 
                                AND 
                                psearch.id_lang = ' . (int) $this->context->language->id .
                    '   (psearch.name like "%' . pSQL($this->search_term) . '%" OR psearch.description_short '
                    . 'LIKE "%' . pSQL($this->search_term) . '%" OR psearch.description '
                    . 'LIKE "%' . pSQL($this->search_term) . '%")'
                    . ') ';
        }

        $query_filters_from .= trim(preg_replace('/\s\s+/', ' ', Shop::addSqlAssociation('product', 'p')));

        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
            'DROP TEMPORARY TABLE IF EXISTS '
            . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction',
            false
        );

        /* Create the table which contains all the id_product in a cat or a tree */
        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
            'CREATE TEMPORARY TABLE '
            . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category_product cp
            INNER JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category AND
            ' . (Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= ' . (int) $parent->nleft . '
            AND c.nright <= ' . (int) $parent->nright : 'c.id_category = ' . (int) $id_parent) . '
            AND c.active = 1)
            JOIN `' . _DB_PREFIX_ . 'product` p USING (id_product)
            ' . $search_term_query . $price_filter_query_in . $query_filters_from . '
            WHERE 1 ' . $query_filters_where . '
            GROUP BY cp.id_product ORDER BY position, id_product',
            false
        );

        Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
            'ALTER TABLE ' . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction'
            . ' ADD PRIMARY KEY (id_product), ADD KEY (position, id_product) USING BTREE',
            false
        );

        if (isset($price_filter) && $price_filter) {
            static $ps_layered_filter_price_usetax = null;
            static $ps_layered_filter_price_rounding = null;

            if ($ps_layered_filter_price_usetax === null) {
                $ps_layered_filter_price_usetax = Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX');
            }

            if ($ps_layered_filter_price_rounding === null) {
                $ps_layered_filter_price_rounding = Configuration::get('PS_LAYERED_FILTER_PRICE_ROUNDING');
            }

            $price_filter_query_out = trim(preg_replace('/\s\s+/', ' ', $price_filter_query_out));
            $query_filters_from = trim(preg_replace('/\s\s+/', ' ', $query_filters_from));
            $query_filters_where = trim(preg_replace('/\s\s+/', ' ', $query_filters_where));
            $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
                'SELECT p.`id_product` id_product
                FROM `' . _DB_PREFIX_ . 'product` p JOIN ' . _DB_PREFIX_ . 'category_product
                cp USING (id_product)
                INNER JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category AND
                    ' . (Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= ' . (int) $parent->nleft . '
                    AND c.nright <= ' . (int) $parent->nright : 'c.id_category = ' . (int) $id_parent) . '
                    AND c.active = 1)
                ' . $price_filter_query_out . '
                ' . $query_filters_from . '
                WHERE 1 ' . $query_filters_where . ' GROUP BY cp.id_product',
                true,
                false
            );

            /* for this case, price could be out of range, so we need to compute the real price */
            foreach ($all_products_out as $product) {
                $price = Product::getPriceStatic($product['id_product'], $ps_layered_filter_price_usetax);
                if ($ps_layered_filter_price_rounding) {
                    $price = (int) $price;
                }
                $product_id_delete_list = array();
                if ($price < $price_filter['min'] || $price > $price_filter['max']) {
                    /* out of range price, exclude the product */
                    $product_id_delete_list[] = (int) $product['id_product'];
                }
            }
            if (!empty($product_id_delete_list)) {
                Db::getInstance(_PS_USE_SQL_SLAVE_)->execute(
                    'DELETE FROM ' . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction WHERE id_product '
                    . 'IN (' . pSQL(implode(',', $product_id_delete_list)) . ')',
                    false
                );
            }
        }
        $nbr_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction',
            false
        );

        if ($nbr_products == 0) {
            $products = array();
        } else {
            $long_sql = '';
            $n = $this->limit;

            $nb_day_new_product = (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20);

            $t_qry = ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity';
            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                $long_sql .= 'SELECT
                                    p.*,
                                    ' . ($alias_where == 'p' ? '' : 'product_shop.*,' ) . '
                                    ' . pSQL($alias_where) . '.id_category_default,
                                    pl.*,
                                    image_shop.`id_image` id_image,
                                    il.legend,
                                    m.name manufacturer_name,
                                    ' . (Combination::isFeatureActive() ? 'product_attribute_shop.id_product_attribute
                                    id_product_attribute,' : '') . '
                                    DATEDIFF(' . pSQL($alias_where) . '.`date_add`,
                                    DATE_SUB("' . pSQL(date('Y-m-d')) . ' 00:00:00",
                                    INTERVAL ' . (int) $nb_day_new_product . ' DAY)) > 0 AS new,
                                    stock.out_of_stock, IFNULL(stock.quantity, 0) as
                                    quantity' . (Combination::isFeatureActive() ? pSQL($t_qry) : '') . '
                            FROM ' . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction cp
                            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                            ' . Shop::addSqlAssociation('product', 'p') .
                        (Combination::isFeatureActive() ?
                                ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                                    ON (p.`id_product` = product_attribute_shop.`id_product` AND
                                    product_attribute_shop.`default_on` = 1 AND
                                    product_attribute_shop.id_shop=' . (int) $this->context->shop->id . ')' : '') . '
                            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product = p.id_product'
                            . Shop::addSqlRestrictionOnLang('pl') . ' AND
                            pl.id_lang = ' . (int) $this->context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                                    ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND
                                    image_shop.id_shop=' . (int) $this->context->shop->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
                            AND il.`id_lang` = ' . (int) $this->context->language->id . ')
                            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
                            ' . Product::sqlStock('p', 0) . '
                            WHERE ' . pSQL($alias_where) . '.`active` = 1 AND ' . pSQL($alias_where) . '.`visibility`
                            IN ("both", "catalog")
                            ORDER BY ' . Tools::getProductsOrder('by', $this->order_by, true) . ' '
                            . Tools::getProductsOrder('way', $this->order_way) . ' , cp.id_product' .
                        ' LIMIT ' . (((int) $this->page_number - 1) * $n . ',' . $n);
                $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($long_sql, true, false);
            } else {
                $t_qry1 = 'MAX(product_attribute_shop.id_product_attribute) id_product_attribute,';
                $qry_asso = Shop::addSqlAssociation(
                    'product_attribute',
                    'pa',
                    false,
                    'product_attribute_shop.`default_on` = 1 AND '
                    . 'product_attribute_shop.id_shop=' . (int) $this->context->shop->id
                );
                $long_sql .= 'SELECT
                                    p.*,
                                    ' . ($alias_where == 'p' ? '' : 'product_shop.*,' ) . '
                                    ' . pSQL($alias_where) . '.id_category_default,
                                    pl.*,
                                    MAX(image_shop.`id_image`) id_image,
                                    il.legend,
                                    m.name manufacturer_name,
                                    ' . (Combination::isFeatureActive() ? $t_qry1 : '') . '
                                    DATEDIFF(' . pSQL($alias_where) . '.`date_add`,
                                    DATE_SUB("' . pSQL(date('Y-m-d')) . ' 00:00:00",
                                    INTERVAL ' . (int) $nb_day_new_product . ' DAY)) > 0 AS new,
                                    stock.out_of_stock, IFNULL(stock.quantity, 0) as 
                                    quantity' . (Combination::isFeatureActive() ? ',
                                    MAX(product_attribute_shop.minimal_quantity) AS
                                    product_attribute_minimal_quantity' : '') . '
                            FROM ' . _DB_PREFIX_ . 'mobileapp_cat_filter_restriction cp
                            LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                            ' . Shop::addSqlAssociation('product', 'p') .
                        (Combination::isFeatureActive() ?
                                'LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
                            ' . $qry_asso : '') . '
                            LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON
                            (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl') . 'AND
                            pl.id_lang = ' . (int) $this->context->language->id . ')
                            LEFT JOIN `' . _DB_PREFIX_ . 'image` i  ON (i.`id_product` = p.`id_product`)' .
                        Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
                            LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image`
                            AND il.`id_lang` = ' . (int) $this->context->language->id . ')
                            LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
                            ' . Product::sqlStock('p', 0) . '
                            WHERE ' . pSQL($alias_where) . '.`active` = 1 AND ' . pSQL($alias_where) . '.`visibility`
                                IN ("both", "catalog")
                            GROUP BY product_shop.id_product
                            ORDER BY ' . Tools::getProductsOrder('by', $this->order_by, true) . ' '
                            . Tools::getProductsOrder('way', $this->order_way) . ' , cp.id_product' .
                        ' LIMIT ' . (((int) $this->page_number - 1) * $n . ',' . $n);
                $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($long_sql, true, false);
            }
        }

        Tools::orderbyPrice($products, Tools::getProductsOrder('way', $this->order_way));

        return $products;
    }
}

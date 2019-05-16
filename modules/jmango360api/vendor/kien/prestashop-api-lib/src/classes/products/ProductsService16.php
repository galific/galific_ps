<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if (file_exists(_PS_MODULE_DIR_ . '/blocklayered/blocklayered.php')) {
    require_once _PS_MODULE_DIR_ . '/blocklayered/blocklayered.php';
}

class ProductsService16 extends ProductsService
{
    protected $products;
    protected $nbr_products;
    protected $page = 1;
    protected $hook_executed = false;
    protected $SHOW_ON_MOBILE = 1;
    protected $id_products;

    public function doExecute()
    {
        $id_category = Tools::getValue('id_category');

        if (!$id_category || !Validate::isUnsignedId($id_category)) {
            throw new WebserviceException('Missing category ID', 400);
        }

        // Instantiate category
        $this->category = new Category($id_category, $this->context->language->id);
        //check category null or not
        if (!$this->category || !$this->category->id) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Category not found');

            return;
        }

        //check category active or not
        if (!Validate::isLoadedObject($this->category)
            || !$this->category->active
            || !$this->category->inShop()
            || !$this->category->isAssociatedToShop()
            || in_array($this->category->id, array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY')))
        ) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Category not found');

            return;
        }

        if (!$this->category->checkAccess($this->context->customer->id)) {
            //if customer can't view this category or not
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');

            $this->response = new JmResponse();
            $error = new JmError(403, $this->getTranslation('error_access_category', 'product-service'));
            $this->response->errors = array($error);

            return;
        }

        $this->id_lang = $this->context->language->id;
        $this->id_customer = $this->context->customer->id;
        $this->p = $this->getRequestValue('page_num', 1);
        $this->n = $this->getRequestValue('page_size', 20);

        $_GET['p'] = $this->p;
        $_GET['n'] = $this->n;

        $this->initializeCart(Context::getContext());
        Product::initPricesComputation();
        $this->page = $this->p;
        // Product sort must be called before assignProductList()

        $this->productSort();
        $this->assignProductList();

        $products = $this->transformProductList();
        $this->setBanner($products);

        if ($this->hook_executed) {
            $selected_filters = $this->getSelectedFilters();
            $this->getFilterBlock($selected_filters);
        }

        $nav_layered = $this->transformNavigationLayered();

        $this->response = new JmProductsResponse();
        $this->response->products = $products;
        $this->response->navigation_layered = $nav_layered;
    }

    /**
     * Assigns product list template variables
     */
    protected function assignProductList()
    {
        $hook_executed = false;

        if (strpos($_SERVER['HTTP_HOST'], 'tricotcafe.com') !== false) {
            Hook::exec('actionProductListOverride', array(
                'nbProducts' => &$this->nbProducts,
                'catProducts' => &$this->cat_products,
                'hookExecuted' => &$hook_executed,
            ));

            // The hook was not executed, standard working
            if (!$hook_executed) {
                $this->context->smarty->assign('categoryNameComplement', '');
                $this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
                $this->pagination((int)$this->nbProducts); // Pagination must be call after "getProducts"
                $this->cat_products = $this->category->getProducts($this->context->language->id, (int)$this->p, (int)$this->n, $this->orderBy, $this->orderWay);
            } // Hook executed, use the override
            else {
                // Pagination must be call after "getProducts"
                $this->pagination($this->nbProducts);
            }
        } else {
            if (Module::isEnabled('blocklayered')) {
                $hook_executed = true;
                $selected_filters = $this->getSelectedFilters();
                $this->getProducts(
                    $selected_filters,
                    $this->cat_products,
                    $this->nbProducts,
                    $this->p,
                    $this->n,
                    $this->pages_nb,
                    $this->start,
                    $this->stop,
                    $this->range
                );
            }

            // The hook was not executed, standard working
            if (!$hook_executed) {
                $selected_filters = array();
                $this->getProducts(
                    $selected_filters,
                    $this->cat_products,
                    $this->nbProducts,
                    $this->p,
                    $this->n,
                    $this->pages_nb,
                    $this->start,
                    $this->stop,
                    $this->range
                );
            } else {
                // PS-677 : [Prestashop 16] Doesn't display filter function on bottom bar while showing on website
                $this->hook_executed = true;
            }

            // Pagination must be call after "getProducts"
            $this->pagination($this->nbProducts);
        }

        Hook::exec('actionProductListModifier', array(
            'nb_products' => &$this->nbProducts,
            'cat_products' => &$this->cat_products,
        ));

        foreach ($this->cat_products as &$product) {
            if (isset($product['id_product_attribute'])
                && $product['id_product_attribute']
                && isset($product['product_attribute_minimal_quantity'])) {
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
            }
        }
    }

    // Add tags of corresponding language to the response.
    protected function setBanner(&$products)
    {
        foreach ($products as $prod) {
            $temp = array();
            $prod->banner_info = array();
            $temp['code'] = 'on_sale';
            $temp['value'] = $prod->on_sale;
            $temp['label'] = $this->getTranslation('Sale!', 'product-list');
            $prod->banner_info[] = $temp;
            $temp['code'] = 'new';
            $temp['value'] = $prod->new;
            $temp['label'] = $this->getTranslation('New', 'product-list');
            $prod->banner_info[] = $temp;
        }
    }

    public function localizeOrderByValues()
    {
        return array(
            'name' => $this->getTranslation('name', 'order-by-values'),
            'price' => $this->getTranslation('price', 'order-by-values'),
            'quantity' => $this->getTranslation('quantity', 'order-by-values'),
            'reference' => $this->getTranslation('reference', 'order-by-values'),
            'date_add' => $this->getTranslation('date_add', 'order-by-values'),
            'date_upd' => $this->getTranslation('date_upd', 'order-by-values'),
            'position' => $this->getTranslation('position', 'order-by-values'),
            'manufacturer_name' => $this->getTranslation('manufacturer_name', 'order-by-values'),
        );
    }

    protected function transformNavigationLayered()
    {
        $data = array();

        $navigation_layered = new JmNavigationLayered();

        if ($this->nav_filter_block) {
            $navigation_layered->layered_show_qties = $this->nav_filter_block['layered_show_qties'];
            $navigation_layered->id_category_layered = $this->nav_filter_block['id_category_layered'];
            $navigation_layered->selected_filters = $this->nav_filter_block['selected_filters'];
            $navigation_layered->nbr_filterBlocks = $this->nav_filter_block['nbr_filterBlocks'];
            $navigation_layered->title_values = $this->nav_filter_block['title_values'];
            $navigation_layered->meta_values = $this->nav_filter_block['meta_values'];
            $navigation_layered->current_friendly_url = $this->nav_filter_block['current_friendly_url'];
            $navigation_layered->param_product_url = $this->nav_filter_block['param_product_url'];
            $navigation_layered->no_follow = $this->nav_filter_block['no_follow'];
            $navigation_layered->filters = $this->transformFilters($this->nav_filter_block['filters']);
        }
        $navigation_layered->order_by = $this->orderBy;
        $navigation_layered->order_way = $this->orderWay;
        $navigation_layered->order_by_values = $this->localizeOrderByValues();
        $navigation_layered->order_way_values = $this->order_way_values;

        $data[] = $navigation_layered;
        return $data;
    }

    private function transformFilters($filters = array())
    {
        $data = array();

        $get_filter = Tools::getValue('selected_filters');
        $get_filter = str_split($get_filter);
        $arr_filter = array();
        $i = 0;
        foreach ($get_filter as $t) {
            if ($t != '/') {
                $arr_filter[$i] .= $t;
            } else {
                $i++;
            }
        }

        foreach ($filters as $filter) {
            $item = new JmFilter();

            $item->type_lite = $filter['type_lite'];
            $item->type = $filter['type'];
            $item->id_key = $filter['id_key'];
            $item->name = $filter['name'];
            $item->nameKey = !empty($filter['nameKey']) ? $filter['nameKey'] : Tools::toUnderscoreCase(str_replace($this->getAnchor(), '_', Tools::link_rewrite($filter['name'])));
            $item->is_color_group = $filter['is_color_group'];

            if (strcmp("price", $item->type) == 0) {
                $item->values = $this->transformPriceFilterValues($filter, $arr_filter);
            } else {
                $item->values = $this->transformFilterValues($filter, $arr_filter);
            }

            $data[] = $item;
        }

        return $data;
    }

    private function transformFilterValues($filter, $selected_filters = array())
    {
        $data = array();

        if (!is_array($filter)) {
            return $data;
        }

        $filterValues = isset($filter['values']) ? $filter['values'] : array();
        $filterName = isset($filter['name']) ? $filter['name'] : '';

        foreach ($filterValues as $value) {
            // Remove filter with no product
            if ((int)$value['nbr'] > 0 && !$value['checked']) {
                $item = new JmFilterValue();
                $value['name'] = isset($value['name']) ? $value['name'] : '';
                $valueKey = !empty($value['valueKey']) ? $value['valueKey'] : str_replace($this->getAnchor(), '_', Tools::link_rewrite($value['name']));
                $value['name'] = str_replace(' ', '_', $value['name']);
                $filter = $filterName . '-' . $value['name'];

                $flag = false;
                foreach ($selected_filters as $temp) {
                    if (Tools::strtolower($temp) == Tools::strtolower($filter)) {
                        $flag = true;
                        break;
                    }
                }

                if ($flag == true) {
                    continue;
                }

                if (is_array($value)) {
                    $item->name = array_key_exists('name', $value) ? $value['name'] : '';
                    $item->nbr = array_key_exists('nbr', $value) ? $value['nbr'] : '';
                    $item->link = array_key_exists('link', $value) ? $value['link'] : '';
                    $item->valueKey = Tools::strtolower($valueKey);
                }

                $data[] = $item;
            }
        }

        return $data;
    }

    /**
     * Assigns product list page pagination variables
     *
     * @param int|null $total_products
     * @throws PrestaShopException
     */
    public function pagination($total_products = null)
    {
        // Retrieve the default number of products per page and the other available selections
        $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
        $n_array = array($default_products_per_page, $default_products_per_page * 2, $default_products_per_page * 5);

        if ((int)Tools::getValue('page_size') && (int)$total_products > 0) {
            $n_array[] = $total_products;
        }
        // Retrieve the current number of products per page (either the default,
        //the GET parameter or the one in the cookie)
        $this->n = $default_products_per_page;
        if (isset($this->context->cookie->nb_item_per_page)
            && in_array($this->context->cookie->nb_item_per_page, $n_array)) {
            $this->n = (int)$this->context->cookie->nb_item_per_page;
        }

        if ((int)Tools::getValue('page_size')) {
            $this->n = (int)Tools::getValue('page_size');
        }
        // PS-378: if page size is too big, set it to 24.
        if ($this->n > 24) {
            $this->n = 24;
        }

        // Retrieve the page number (either the GET parameter or the first page)
        $this->p = (int)Tools::getValue('page_num', 1);
        // If the parameter is not correct then redirect (do not merge with the previous line,
        //the redirect is required in order to avoid duplicate content)
        if (!is_numeric($this->p) || $this->p < 1) {
            Tools::redirect($this->context->link->getPaginationLink(false, false, $this->n, false, 1, false));
        }

        // Remove the page parameter in order to get a clean URL for the pagination template
        $current_url = preg_replace('/(?:(\?)|&amp;)p=\d+/', '$1', Tools::htmlentitiesUTF8($_SERVER['REQUEST_URI']));

        if ($this->n != $default_products_per_page || isset($this->context->cookie->nb_item_per_page)) {
            $this->context->cookie->nb_item_per_page = $this->n;
        }

        $pages_nb = ceil((int)$total_products / (int)$this->n);

        $range = 2; /* how many pages around page selected */
        $start = (int)($this->p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int)($this->p + $range);
        if ($stop > $pages_nb) {
            $stop = (int)$pages_nb;
        }
    }

    public function getProducts(
        $selected_filters,
        &$products,
        &$nb_products,
        &$p,
        &$n,
        &$pages_nb,
        &$start,
        &$stop,
        &$range
    ) {
        //global $cookie;

        $products = $this->getProductByFilters($selected_filters);
        $products = Product::getProductsProperties((int)$this->id_lang, $products);
        $nb_products = $this->nbr_products;
        $range = 2; /* how many pages around page selected */

        $product_per_page =
            isset($this->context->cookie->nb_item_per_page) ?
                (int)$this->context->cookie->nb_item_per_page : Configuration::get('PS_PRODUCTS_PER_PAGE');
        $n = (int)Tools::getValue('page_size', Configuration::get('PS_PRODUCTS_PER_PAGE'));

        if ($n <= 0) {
            $n = 1;
        }

        $p = $this->page;

        if ($p < 0) {
            $p = 0;
        }

        if ($p > ($nb_products / $n)) {
            $p = ceil($nb_products / $n);
        }

        $pages_nb = ceil($nb_products / (int)($n));

        $start = (int)($p - $range);
        if ($start < 1) {
            $start = 1;
        }

        $stop = (int)($p + $range);
        if ($stop > $pages_nb) {
            $stop = (int)($pages_nb);
        }

        foreach ($products as &$product) {
            if ($product['id_product_attribute'] && isset($product['product_attribute_minimal_quantity'])) {
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
            }
        }
    }

    public function getProductByFilters($selected_filters = array())
    {
        //global $cookie;

        if (!empty($this->products)) {
            return $this->products;
        }

        $home_category = Configuration::get('PS_HOME_CATEGORY');
        /* If the current category isn't defined or if it's homepage, we have nothing to display */
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));
        if ($this->category && $id_parent == $home_category) {
            return false;
        }

        $alias_where = 'p';
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $alias_where = 'product_shop';
        }

        $query_filters_where =
            ' AND ' . $alias_where . '.`active` = 1 AND ' . $alias_where . '.`visibility` IN ("both", "catalog")';
        $query_filters_from = '';

        $parent = new Category((int)$id_parent);

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
                        $sub_queries[$filter_value_array[0]][] =
                            'fp.`id_feature_value` = ' . (int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query) {
                        $query_filters_where .=
                            ' AND p.id_product IN (SELECT `id_product`
                            FROM `' . _DB_PREFIX_ . 'feature_product` fp WHERE ';
                        $query_filters_where .= implode(' OR ', $sub_query) . ') ';
                    }
                    break;

                case 'id_attribute_group':
                    $sub_queries = array();


                    foreach ($filter_values as $filter_value) {
                        $filter_value_array = explode('_', $filter_value);
                        if (!isset($sub_queries[$filter_value_array[0]])) {
                            $sub_queries[$filter_value_array[0]] = array();
                        }
                        $sub_queries[$filter_value_array[0]][] = 'pac.`id_attribute` = ' . (int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query) {
                        $query_filters_where .= ' AND p.id_product IN (SELECT pa.`id_product`
						FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
						LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa
						ON (pa.`id_product_attribute` = pac.`id_product_attribute`)' .
                            Shop::addSqlAssociation('product_attribute', 'pa') . '
						WHERE ' . implode(' OR ', $sub_query) . ') ';
                    }
                    break;

                case 'category':
                    $query_filters_where .=
                        ' AND p.id_product IN (SELECT id_product
                        FROM ' . _DB_PREFIX_ . 'category_product cp WHERE ';
                    foreach ($selected_filters['category'] as $id_category) {
                        $query_filters_where .= 'cp.`id_category` = ' . (int)$id_category . ' OR ';
                    }
                    $query_filters_where = rtrim($query_filters_where, 'OR ') . ')';
                    break;

                case 'quantity':
                    if (count($selected_filters['quantity']) == 2) {
                        break;
                    }
                    $query_filters_where .=
                        ' AND sa.quantity ' . (!$selected_filters['quantity'][0] ? '<=' : '>') . ' 0 ';
                    $query_filters_from .=
                        'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                        ON (sa.id_product = p.id_product ' .
                        StockAvailable::addSqlShopRestriction(null, null, 'sa') . ') ';
                    break;

                case 'manufacturer':
                    $query_filters_where .=
                        ' AND p.id_manufacturer IN (' . implode($selected_filters['manufacturer'], ',') . ')';
                    break;

                case 'condition':
                    if (count($selected_filters['condition']) == 3) {
                        break;
                    }
                    $query_filters_where .= ' AND ' . $alias_where . '.condition IN (';
                    foreach ($selected_filters['condition'] as $cond) {
                        $query_filters_where .= '\'' . pSQL($cond) . '\',';
                    }
                    $query_filters_where = rtrim($query_filters_where, ',') . ')';
                    break;

                case 'weight':
                    if ($selected_filters['weight'][0] != 0 || $selected_filters['weight'][1] != 0) {
                        $query_filters_where .=
                            ' AND p.`weight` BETWEEN ' . (float)($selected_filters['weight'][0] - 0.001) .
                            'AND ' . (float)($selected_filters['weight'][1] + 0.001);
                    }
                    break;

                case 'price':
                    if (isset($selected_filters['price'])) {
                        if ($selected_filters['price'][0] !== '' || $selected_filters['price'][1] !== '') {
                            $price_filter = array();
                            $price_filter['min'] = (float)($selected_filters['price'][0]);
                            $price_filter['max'] = (float)($selected_filters['price'][1]);
                        }
                    } else {
                        $price_filter = false;
                    }
                    break;
            }
        }

        $context = Context::getContext();
        $id_currency = (int)$context->currency->id;

        $price_filter_query_in = ''; // All products with price range between price filters limits
        $price_filter_query_out = ''; // All products with a price filters limit on it price range
        if (isset($price_filter) && $price_filter) {
            $price_filter_query_in = 'INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi
			ON
			(
				psi.price_min <= ' . (int)$price_filter['max'] . '
				AND psi.price_max >= ' . (int)$price_filter['min'] . '
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_shop` = ' . (int)$context->shop->id . '
				AND psi.`id_currency` = ' . $id_currency . '
			)';

            $price_filter_query_out = 'INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi
			ON
				((psi.price_min < ' . (int)$price_filter['min'] .
                ' AND psi.price_max > ' . (int)$price_filter['min'] . ')
				OR
				(psi.price_max > ' . (int)$price_filter['max'] .
                ' AND psi.price_min < ' . (int)$price_filter['max'] . '))
				AND psi.`id_product` = p.`id_product`
				AND psi.`id_shop` = ' . (int)$context->shop->id . '
				AND psi.`id_currency` = ' . $id_currency;
        }

        $query_filters_from .= Shop::addSqlAssociation('product', 'p');

        Db::getInstance()->execute('DROP TEMPORARY TABLE IF EXISTS ' . _DB_PREFIX_ . 'cat_filter_restriction', false);
        if (!empty($this->id_products)) {
            $sql = 'CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
            STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND c.active = 1)
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
            ' . $price_filter_query_in . '
            ' . $query_filters_from . '
            WHERE 1 ' . $query_filters_where . (empty($this->id_products) ?
                    '' : ' AND p.`id_product`  IN (' . implode(',', $this->id_products) . ')') . ' GROUP BY cp.id_product ORDER BY position, id_product';
            Db::getInstance()->execute($sql, true);
        } elseif (empty($selected_filters['category'])) {
            /* Create the table which contains all the id_product in a cat or a tree */
            $sql = 'CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
            STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND
            ' . (Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= ' . (int)$parent->nleft . '
            AND c.nright <= ' . (int)$parent->nright : 'c.id_category = ' . (int)$id_parent) . '
            AND c.active = 1)
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
            ' . $price_filter_query_in . '
            ' . $query_filters_from . '
            WHERE 1 ' . $query_filters_where . '
            GROUP BY cp.id_product ORDER BY position, id_product';
            Db::getInstance()->execute($sql, false);
        } else {
            $categories = array_map('intval', $selected_filters['category']);

            Db::getInstance()->execute('CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category_product cp
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
            ' . $price_filter_query_in . '
            ' . $query_filters_from . '
            WHERE cp.`id_category` IN (' . implode(',', $categories) . ') ' . $query_filters_where . '
            GROUP BY cp.id_product ORDER BY position, id_product', false);
        }
        Db::getInstance()->execute(
            'ALTER TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ADD PRIMARY KEY (id_product),
            ADD KEY (position, id_product) USING BTREE',
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

            if (!empty($this->id_products)) {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.`id_product` id_product
				FROM `' . _DB_PREFIX_ . 'product` p JOIN ' . _DB_PREFIX_ . 'category_product cp USING (id_product)
				INNER JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category AND c.active = 1)
				' . $price_filter_query_out . '
				' . $query_filters_from . '
				WHERE 1 ' . $query_filters_where . ' GROUP BY cp.id_product');
            } else if (empty($selected_filters['category'])) {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.`id_product` id_product
				FROM `' . _DB_PREFIX_ . 'product` p JOIN ' . _DB_PREFIX_ . 'category_product cp USING (id_product)
				INNER JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category AND
					' . (Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= ' . (int)$parent->nleft . '
					AND c.nright <= ' . (int)$parent->nright : 'c.id_category = ' . (int)$id_parent) . '
					AND c.active = 1)
				' . $price_filter_query_out . '
				' . $query_filters_from . '
				WHERE 1 ' . $query_filters_where . ' GROUP BY cp.id_product');
            } else {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT p.`id_product` id_product
				FROM `' . _DB_PREFIX_ . 'product` p JOIN ' . _DB_PREFIX_ . 'category_product cp USING (id_product)
				' . $price_filter_query_out . '
				' . $query_filters_from . '
				WHERE cp.`id_category`
				IN (' . implode(',', $categories) . ') ' . $query_filters_where . ' GROUP BY cp.id_product');
            }

            /* for this case, price could be out of range, so we need to compute the real price */
            $product_id_delete_list = null;
            foreach ($all_products_out as $product) {
                $price = Product::getPriceStatic($product['id_product'], $ps_layered_filter_price_usetax);
                if ($ps_layered_filter_price_rounding) {
                    $price = (int)$price;
                }
                if ($price < $price_filter['min'] || $price > $price_filter['max']) {
                    // out of range price, exclude the product
                    $product_id_delete_list[] = (int)$product['id_product'];
                }
            }
            if (!empty($product_id_delete_list)) {
                Db::getInstance()->execute(
                    'DELETE FROM ' . _DB_PREFIX_ . 'cat_filter_restriction WHERE id_product IN (' . implode(',
                    ', $product_id_delete_list) . ')',
                    false
                );
            }
        }

        // Query Hidden pack products
        $hidden_pack_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT(`id_product_pack`)
                FROM `' . _DB_PREFIX_ . 'pack`
                LEFT JOIN ' . _DB_PREFIX_ . 'jm_product_visibility
                ON ' . _DB_PREFIX_ . 'jm_product_visibility.id_product = ' . _DB_PREFIX_ . 'pack.id_product_item
                WHERE ' . _DB_PREFIX_ . 'jm_product_visibility.not_visible = 2');

        $hidden_pack_product_ids = array();
        foreach ($hidden_pack_product as $pack) {
            $hidden_pack_product_ids[] = $pack['id_product_pack'];
        }

        $this->nbr_products = Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'cat_filter_restriction',
            false
        );

        if ($this->nbr_products == 0) {
            $this->products = array();
        } else {
            $product_per_page = isset($this->context->cookie->nb_item_per_page) ?
                $this->context->cookie->nb_item_per_page : Configuration::get('PS_PRODUCTS_PER_PAGE');
            $default_products_per_page = max(1, (int)Configuration::get('PS_PRODUCTS_PER_PAGE'));
            $n = $default_products_per_page;
            if (isset($this->context->cookie->nb_item_per_page)) {
                $n = (int)$this->context->cookie->nb_item_per_page;
            }
            if ((int)Tools::getValue('page_size')) {
                $n = (int)Tools::getValue('page_size');
            }
            $nb_day_new_product = (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20);

            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                $sql = '
				SELECT
					p.*,
					' . ($alias_where == 'p' ? '' : 'product_shop.*,') . '
					' . $alias_where . '.id_category_default,
					pl.*,
					jm.not_visible,
					image_shop.`id_image` id_image,
					il.legend,
					m.name manufacturer_name,
					' . (Combination::isFeatureActive() ?
                        'product_attribute_shop.id_product_attribute id_product_attribute,' : '') . '
					DATEDIFF(' . $alias_where . '.`date_add`,
					DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int)$nb_day_new_product . ' DAY)) > 0 AS new,
					stock.out_of_stock,
					IFNULL(stock.quantity, 0) as quantity' . (Combination::isFeatureActive() ? ',
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . '
				FROM ' . _DB_PREFIX_ . 'cat_filter_restriction cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p') .
                    (Combination::isFeatureActive() ?
                        ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
					ON (p.`id_product` = product_attribute_shop.`id_product`
					AND product_attribute_shop.`default_on` = 1
					AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')' : '') . '
				LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
				ON (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl') .
                    ' AND pl.id_lang = ' . (int)$this->id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product`
					AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
				ON (image_shop.`id_image` = il.`id_image`
				AND il.`id_lang` = ' . (int)$this->id_lang . ')
				LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`)
				' . Product::sqlStock('p', 0) . '
				WHERE (jm.`not_visible` =' . $this->SHOW_ON_MOBILE . '
				OR jm.`not_visible` is null)
				AND ' . $alias_where . '.`active` = 1
				AND ' . $alias_where . '.`visibility`
				IN ("both", "catalog")
                ' . (empty($hidden_pack_product_ids) ?
                        '' : 'AND p.`id_product` NOT IN (' . implode(',', $hidden_pack_product_ids) . ')') . ' ' . (empty($this->id_products) ?
                        '' : 'AND p.`id_product`  IN (' . implode(',', $this->id_products) . ')') . '
				ORDER BY ' . Tools::getProductsOrder('by', $this->orderBy, true) . ' ' .
                    Tools::getProductsOrder('way', $this->orderWay) . ' , cp.id_product' .
                    ' LIMIT ' . (((int)$this->page - 1) * $n . ',' . $n);
                $this->products = Db::getInstance()->executeS($sql, true, false);
            } else {
                $this->products = Db::getInstance()->executeS('
				SELECT
					p.*,
					' . ($alias_where == 'p' ? '' : 'product_shop.*,') . '
					' . $alias_where . '.id_category_default,
					pl.*,
					jm.not_visible,
					MAX(image_shop.`id_image`) id_image,
					il.legend,
					m.name manufacturer_name,
					' . (Combination::isFeatureActive() ?
                        'MAX(product_attribute_shop.id_product_attribute) id_product_attribute,' : '') . '
					DATEDIFF(' . $alias_where . '.`date_add`,
					DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
					INTERVAL ' . (int)$nb_day_new_product . ' DAY)) > 0 AS new,
					stock.out_of_stock,
					IFNULL(stock.quantity, 0) as quantity' . (Combination::isFeatureActive() ?
                        ', MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity' : '') . '
				FROM ' . _DB_PREFIX_ . 'cat_filter_restriction cp
				LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
				' . Shop::addSqlAssociation('product', 'p') .
                    (Combination::isFeatureActive() ?
                        'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
				' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1
				AND product_attribute_shop.id_shop=' . (int)$context->shop->id) : '') . '
				LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
				ON (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl') . '
				AND pl.id_lang = ' . (int)$this->id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image` i  ON (i.`id_product` = p.`id_product`)' .
                    Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
				ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$this->id_lang . ')
				LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
				LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`)

				' . Product::sqlStock('p', 0) . '
				WHERE (jm.`not_visible` =' . $this->SHOW_ON_MOBILE . '
				OR jm.`not_visible` is null)
				AND ' . $alias_where . '.`active` = 1
				AND ' . $alias_where . '.`visibility` IN ("both", "catalog")
                ' . (empty($hidden_pack_product_ids) ? '' : 'AND p.`id_product`
                NOT IN (' . implode(',', $hidden_pack_product_ids) . ')') . '
				GROUP BY product_shop.id_product
				ORDER BY ' .
                    Tools::getProductsOrder('by', $this->orderBy, true) . ' ' .
                    Tools::getProductsOrder('way', $this->orderWay) . ' , cp.id_product' .
                    ' LIMIT ' . (((int)$this->page - 1) * $n . ',' . $n), true, false);
            }
        }

        if (Tools::getProductsOrder('by', $this->orderBy, true) == 'p.price') {
            Tools::orderbyPrice($this->products, Tools::getProductsOrder('way', $this->orderWay));
        }
        return $this->products;
    }

    protected function getSelectedFilters()
    {
        $home_category = Configuration::get('PS_HOME_CATEGORY');
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));
        if ($this->category && $id_parent == $home_category) {
            return;
        }

        // Force attributes selection (by url '.../2-mycategory/color-blue' or by get parameter 'selected_filters')
        if (Tools::getValue('selected_filters') !== false) {
            if (Tools::getValue('selected_filters')) {
                $url = Tools::getValue('selected_filters');
            } else {
                $url = preg_replace('/\/(?:\w*)\/(?:[0-9]+[-\w]*)([^\?]*)\??.*/', '$1', Tools::safeOutput($_SERVER['REQUEST_URI'], true));
            }

            $selected_filters = array('category' => array($id_parent));

            $url_attributes = explode('/', ltrim($url, '/'));
            if (!empty($url_attributes)) {
                foreach ($url_attributes as $url_attribute) {
                    /* Pagination uses - as separator, can be different from $this->getAnchor()*/
                    if (strpos($url_attribute, 'page-') === 0) {
                        $url_attribute = str_replace('-', $this->getAnchor(), $url_attribute);
                    }
                    $url_parameters = explode($this->getAnchor(), $url_attribute);
                    $attribute_name = array_shift($url_parameters);
                    if ($attribute_name == 'page') {
                        $this->page = (int)$url_parameters[0];
                    } elseif (in_array($attribute_name, array('price', 'weight'))) {
                        $selected_filters[$attribute_name] = array($this->filterVar($url_parameters[0]), $this->filterVar($url_parameters[1]));
                    } elseif (in_array($attribute_name, array('category'))) {
                        $selected_filters[$attribute_name] = array($this->filterVar($url_parameters[0]));
                    } else {
                        foreach ($url_parameters as $url_parameter) {
                            $data = Db::getInstance()->getValue('SELECT data FROM `' . _DB_PREFIX_ . 'layered_friendly_url` WHERE `url_key` = \'' . md5('/' . $attribute_name . $this->getAnchor() . $url_parameter) . '\'');
                            if ($data) {
                                foreach (Tools::unSerialize($data) as $key_params => $params) {
                                    if (!isset($selected_filters[$key_params])) {
                                        $selected_filters[$key_params] = array();
                                    }
                                    foreach ($params as $key_param => $param) {
                                        if (!isset($selected_filters[$key_params][$key_param])) {
                                            $selected_filters[$key_params][$key_param] = array();
                                        }
                                        $selected_filters[$key_params][$key_param] = $this->filterVar($param);
                                    }
                                }
                            }
                        }
                    }
                }

                return $selected_filters;
            }
        }
    }

    public function getFilterBlock($selected_filters = array())
    {
        static $cache = null;

        $context = Context::getContext();

        $id_lang = $context->language->id;
        $currency = $context->currency;
        $id_shop = (int)$context->shop->id;
        $alias = 'product_shop';

        if (is_array($cache)) {
            $this->nav_filter_block = $cache;
            return;
        }

        $home_category = Configuration::get('PS_HOME_CATEGORY');
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));
        if ($id_parent == $home_category) {
            return;
        }

        $parent = new Category((int)$id_parent, $id_lang);

        /* Get the filters for the current category */
        $filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT type, id_value, filter_show_limit, filter_type FROM ' . _DB_PREFIX_ . 'layered_category
			WHERE id_category = ' . (int)$id_parent . '
				AND id_shop = ' . $id_shop . '
			GROUP BY `type`, id_value ORDER BY position ASC');

        /* Create the table which contains all the id_product in a cat or a tree */

        Db::getInstance()->execute('DROP TEMPORARY TABLE IF EXISTS ' . _DB_PREFIX_ . 'cat_restriction', false);
        Db::getInstance()->execute('CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_restriction ENGINE=MEMORY
        SELECT DISTINCT cp.id_product,
        p.id_manufacturer,
        product_shop.condition,
        p.weight FROM ' . _DB_PREFIX_ . 'category c
        STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND
        ' . (Configuration::get('PS_LAYERED_FULL_TREE') ? 'c.nleft >= ' . (int)$parent->nleft . '
        AND c.nright <= ' . (int)$parent->nright : 'c.id_category = ' . (int)$id_parent) . '
        AND c.active = 1)
        STRAIGHT_JOIN ' . _DB_PREFIX_ . 'product_shop product_shop ON (product_shop.id_product = cp.id_product
        AND product_shop.id_shop = ' . (int)$context->shop->id . ')
        STRAIGHT_JOIN ' . _DB_PREFIX_ . 'product p ON (p.id_product=cp.id_product)
        WHERE product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog")', false);

        Db::getInstance()->execute('ALTER TABLE ' . _DB_PREFIX_ . 'cat_restriction ADD PRIMARY KEY (id_product),
        ADD KEY `id_manufacturer` (`id_manufacturer`,`id_product`)USING BTREE,
        ADD KEY `condition` (`condition`,`id_product`) USING BTREE,
        ADD KEY `weight` (`weight`,`id_product`) USING BTREE', false);

        // Remove all empty selected filters
        foreach ($selected_filters as $key => $value) {
            switch ($key) {
                case 'price':
                case 'weight':
                    if ($value[0] === '' && $value[1] === '') {
                        unset($selected_filters[$key]);
                    }
                    break;
                default:
                    if ($value == '') {
                        unset($selected_filters[$key]);
                    }
                    break;
            }
        }

        $filter_blocks = array();
        foreach ($filters as $filter) {
            $sql_query = array(
                'select' => '',
                'from' => '',
                'join' => '',
                'where' => '',
                'group' => '',
                'second_query' => ''
            );
            switch ($filter['type']) {
                case 'price':
                    $sql_query['select'] = 'SELECT p.`id_product`, psi.price_min, psi.price_max ';
                    // price slider is not filter dependent
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'cat_restriction p';
                    $sql_query['join'] = 'INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi
								ON (psi.id_product = p.id_product
								AND psi.id_currency = ' . (int)$context->currency->id .
                        ' AND psi.id_shop=' . (int)$context->shop->id . ')
                        LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                    break;
                case 'weight':
                    $sql_query['select'] = 'SELECT p.`id_product`, p.`weight` ';
                    // price slider is not filter dependent
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'cat_restriction p';
                    $sql_query['join'] = 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                    break;
                case 'condition':
                    $sql_query['select'] = 'SELECT p.`id_product`, product_shop.`condition` ';
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'cat_restriction p';
                    $sql_query['join'] = 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                    $sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
                    break;
                case 'quantity':
                    $sql_query['select'] = 'SELECT p.`id_product`, sa.`quantity`, sa.`out_of_stock` ';

                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'cat_restriction p';

                    $sql_query['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
						ON (sa.id_product = p.id_product
						AND sa.id_product_attribute=0 ' . StockAvailable::addSqlShopRestriction(null, null, 'sa') . ')
						LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                    break;

                case 'manufacturer':
                    $sql_query['select'] = 'SELECT COUNT(DISTINCT p.id_product) nbr, m.id_manufacturer, m.name ';
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'cat_restriction p
					INNER JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
					 LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                    $sql_query['group'] = ' GROUP BY p.id_manufacturer ORDER BY m.name';

                    if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES')) {
                        $sql_query['second_query'] = '
							SELECT m.name, 0 nbr, m.id_manufacturer

							FROM ' . _DB_PREFIX_ . 'cat_restriction p
							INNER JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
							WHERE 1
							GROUP BY p.id_manufacturer ORDER BY m.name';
                    }

                    break;
                case 'id_attribute_group':// attribute group
                    $sql_query['select'] = '
					SELECT COUNT(DISTINCT lpa.id_product) nbr, lpa.id_attribute_group,
					a.color, al.name attribute_name,
					agl.public_name attribute_group_name ,
					lpa.id_attribute, ag.is_color_group,
					liagl.url_name name_url_name,
					liagl.meta_title name_meta_title,
					lial.url_name value_url_name,
					lial.meta_title value_meta_title';
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'layered_product_attribute lpa
					INNER JOIN ' . _DB_PREFIX_ . 'attribute a
					ON a.id_attribute = lpa.id_attribute
					INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al
					ON al.id_attribute = a.id_attribute
					AND al.id_lang = ' . (int)$id_lang . '
					INNER JOIN ' . _DB_PREFIX_ . 'cat_restriction p
					ON p.id_product = lpa.id_product
					INNER JOIN ' . _DB_PREFIX_ . 'attribute_group ag
					ON ag.id_attribute_group = lpa.id_attribute_group
					INNER JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl
					ON agl.id_attribute_group = lpa.id_attribute_group
					AND agl.id_lang = ' . (int)$id_lang . '
					LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_attribute_group_lang_value liagl
					ON (liagl.id_attribute_group = lpa.id_attribute_group AND liagl.id_lang = ' . (int)$id_lang . ')
					LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_attribute_lang_value lial
					ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = ' . (int)$id_lang . ') 
					LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';

                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null) AND lpa.id_attribute_group = ' . (int)$filter['id_value'];
                    $sql_query['where'] .= ' AND lpa.`id_shop` = ' . (int)$context->shop->id;
                    $sql_query['group'] = '
					GROUP BY lpa.id_attribute
					ORDER BY ag.`position` ASC, a.`position` ASC';

                    if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES')) {
                        $sql_query['second_query'] = '
							SELECT 0 nbr, lpa.id_attribute_group,
								a.color, al.name attribute_name,
								agl.public_name attribute_group_name ,
								lpa.id_attribute, ag.is_color_group,
								liagl.url_name name_url_name,
								liagl.meta_title name_meta_title,
								lial.url_name value_url_name,
								lial.meta_title value_meta_title
							FROM ' . _DB_PREFIX_ . 'layered_product_attribute lpa' .
                            Shop::addSqlAssociation('product', 'lpa') . '
							INNER JOIN ' . _DB_PREFIX_ . 'attribute a
								ON a.id_attribute = lpa.id_attribute
							INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al
								ON al.id_attribute = a.id_attribute AND al.id_lang = ' . (int)$id_lang . '
							INNER JOIN ' . _DB_PREFIX_ . 'product as p
								ON p.id_product = lpa.id_product
							INNER JOIN ' . _DB_PREFIX_ . 'attribute_group ag
								ON ag.id_attribute_group = lpa.id_attribute_group
							INNER JOIN ' . _DB_PREFIX_ . 'attribute_group_lang agl
								ON agl.id_attribute_group = lpa.id_attribute_group
							AND agl.id_lang = ' . (int)$id_lang . '
							LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_attribute_group_lang_value liagl
								ON (liagl.id_attribute_group = lpa.id_attribute_group
								AND liagl.id_lang = ' . (int)$id_lang . ')
							LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_attribute_lang_value lial
								ON (lial.id_attribute = lpa.id_attribute AND lial.id_lang = ' . (int)$id_lang . ')
							WHERE lpa.id_attribute_group = ' . (int)$filter['id_value'] . '
							AND lpa.`id_shop` = ' . (int)$context->shop->id . '
							GROUP BY lpa.id_attribute
							ORDER BY id_attribute_group, id_attribute';
                    }
                    break;

                case 'id_feature':
                    $sql_query['select'] = 'SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
					COUNT(DISTINCT p.id_product) nbr,
					lifl.url_name name_url_name,
					lifl.meta_title name_meta_title,
					lifvl.url_name value_url_name,
					lifvl.meta_title value_meta_title ';
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'feature_product fp
					INNER JOIN ' . _DB_PREFIX_ . 'cat_restriction p
					ON p.id_product = fp.id_product
					LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl
					ON (fl.id_feature = fp.id_feature AND fl.id_lang = ' . $id_lang . ')
					INNER JOIN ' . _DB_PREFIX_ . 'feature_value fv
					ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
					LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl
					ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = ' . $id_lang . ')
					LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_feature_lang_value lifl
					ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = ' . $id_lang . ')
					LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_feature_value_lang_value lifvl
					ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = ' . $id_lang . ') 
					LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null) AND fp.id_feature = ' . (int)$filter['id_value'];
                    $sql_query['group'] = 'GROUP BY fv.id_feature_value ';

                    if (!Configuration::get('PS_LAYERED_HIDE_0_VALUES')) {
                        $sql_query['second_query'] = '
							SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
							0 nbr,
							lifl.url_name name_url_name,
							lifl.meta_title name_meta_title,
							lifvl.url_name value_url_name,
							lifvl.meta_title value_meta_title

							FROM ' . _DB_PREFIX_ . 'feature_product fp' .
                            Shop::addSqlAssociation('product', 'fp') . '
							INNER JOIN ' . _DB_PREFIX_ . 'product p ON (p.id_product = fp.id_product)
							LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl
							ON (fl.id_feature = fp.id_feature AND fl.id_lang = ' . (int)$id_lang . ')
							INNER JOIN ' . _DB_PREFIX_ . 'feature_value fv
							ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
							LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl
							ON (fvl.id_feature_value = fp.id_feature_value
							AND fvl.id_lang = ' . (int)$id_lang . ')
							LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_feature_lang_value lifl
								ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = ' . (int)$id_lang . ')
							LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_feature_value_lang_value lifvl
								ON (lifvl.id_feature_value = fp.id_feature_value
								AND lifvl.id_lang = ' . (int)$id_lang . ')
							WHERE fp.id_feature = ' . (int)$filter['id_value'] . '
							GROUP BY fv.id_feature_value';
                    }

                    break;

                case 'category':
                    if (Group::isFeatureActive()) {
                        $this->user_groups = (Context::getContext()->customer->isLogged() ?
                            Context::getContext()->customer->getGroups() :
                            array(Configuration::get('PS_UNIDENTIFIED_GROUP')));
                    }

                    $depth = Configuration::get('PS_LAYERED_FILTER_CATEGORY_DEPTH');
                    if ($depth === false) {
                        $depth = 1;
                    }

                    $sql_query['select'] = '
					SELECT c.id_category, c.id_parent, cl.name, (SELECT count(DISTINCT p.id_product) # ';
                    $sql_query['from'] = '
					FROM ' . _DB_PREFIX_ . 'category_product cp
					LEFT JOIN ' . _DB_PREFIX_ . 'product p ON (p.id_product = cp.id_product) 
					LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                    $sql_query['where'] = '
					WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null) AND cp.id_category = c.id_category
					AND ' . $alias . '.active = 1 AND ' . $alias . '.`visibility` IN ("both", "catalog")';
                    $sql_query['group'] = ') count_products
					FROM ' . _DB_PREFIX_ . 'category c
					LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl
					ON (cl.id_category = c.id_category
					AND cl.`id_shop` =
					' . (int)Context::getContext()->shop->id . ' and cl.id_lang = ' . (int)$id_lang . ') ';

                    if (Group::isFeatureActive()) {
                        $sql_query['group'] .=
                            'RIGHT JOIN ' . _DB_PREFIX_ . 'category_group cg
                            ON (cg.id_category = c.id_category
                            AND cg.`id_group` IN (' . implode(', ', $this->user_groups) . ')) ';
                    }

                    $sql_query['group'] .= 'WHERE c.nleft > ' . (int)$parent->nleft . '
					AND c.nright < ' . (int)$parent->nright . '
					' . ($depth ? 'AND c.level_depth <= ' . ($parent->level_depth + (int)$depth) : '') . '
					AND c.active = 1
					GROUP BY c.id_category ORDER BY c.nleft, c.position';

                    $sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
            }

            foreach ($filters as $filter_tmp) {
                $method_name = 'get' . $this->correctFilterType(Tools::ucfirst($filter_tmp['type'])) . 'FilterSubQuery';
                if (method_exists('BlockLayered', $method_name) &&
                    ($filter['type'] != 'price'
                        && $filter['type'] != 'weight'
                        && $filter['type'] != $filter_tmp['type'] || $filter['type'] == $filter_tmp['type'])) {
                    if ($filter['type'] == $filter_tmp['type'] && $filter['id_value'] == $filter_tmp['id_value']) {
                        $sub_query_filter = self::$method_name(array(), true);
                    } else {
                        if (!is_null($filter_tmp['id_value'])) {
                            $selected_filters_cleaned = $this->cleanFilterByIdValue(
                                @$selected_filters[$filter_tmp['type']],
                                $filter_tmp['id_value']
                            );
                        } else {
                            $selected_filters_cleaned = @$selected_filters[$filter_tmp['type']];
                        }
                        $sub_query_filter = self::$method_name(
                            $selected_filters_cleaned,
                            $filter['type'] == $filter_tmp['type']
                        );
                    }
                    foreach ($sub_query_filter as $key => $value) {
                        $sql_query[$key] .= $value;
                    }
                }
            }

            $products = false;
            if (!empty($sql_query['from'])) {
                $products =
                    Db::getInstance()->executeS($sql_query['select'] .
                        "\n" . $sql_query['from'] .
                        "\n" . $sql_query['join'] .
                        "\n" . $sql_query['where'] .
                        "\n" . $sql_query['group'], true, false);
            }

            // price & weight have slidebar, so it's ok to not complete recompute the product list
            if (!empty($selected_filters['price']) && $filter['type'] != 'price' && $filter['type'] != 'weight') {
                $products = self::filterProductsByPrice(@$selected_filters['price'], $products);
            }

            if (!empty($sql_query['second_query'])) {
                $res = Db::getInstance()->executeS($sql_query['second_query']);
                if ($res) {
                    $products = array_merge($products, $res);
                }
            }

            switch ($filter['type']) {
                case 'price':
                    if ($this->showPriceFilter($selected_filters) && !array_key_exists('price', $selected_filters)) {
                        $price_array = array(
                            'type_lite' => 'price',
                            'type' => 'price',
                            'id_key' => 0,
                            'name' => $this->getTranslation('price', 'order-by-values'),
                            'nameKey' => 'price',
                            'slider' => true,
                            'max' => '0',
                            'min' => null,
                            'values' => array('1' => 0),
                            'unit' => $currency->sign,
                            'format' => $currency->format,
                            'filter_show_limit' => $filter['filter_show_limit'],
                            'filter_type' => $filter['filter_type']
                        );
                        if (isset($products) && $products) {
                            foreach ($products as $product) {
                                if (is_null($price_array['min'])) {
                                    $price_array['min'] = $product['price_min'];
                                    $price_array['values'][0] = $product['price_min'];
                                } elseif ($price_array['min'] > $product['price_min']) {
                                    $price_array['min'] = $product['price_min'];
                                    $price_array['values'][0] = $product['price_min'];
                                }

                                if ($price_array['max'] < $product['price_max']) {
                                    $price_array['max'] = $product['price_max'];
                                    $price_array['values'][1] = $product['price_max'];
                                }
                            }
                        }

                        if ($price_array['max'] != $price_array['min'] && $price_array['min'] != null) {
                            if ($filter['filter_type'] == 2) {
                                $price_array['list_of_values'] = array();
                                $nbr_of_value = $filter['filter_show_limit'];
                                if ($nbr_of_value < 2) {
                                    $nbr_of_value = 4;
                                }
                                $delta = ($price_array['max'] - $price_array['min']) / $nbr_of_value;
                                $current_step = $price_array['min'];
                                for ($i = 0; $i < $nbr_of_value; $i++) {
                                    $price_array['list_of_values'][] = array(
                                        (int)($price_array['min'] + $i * $delta),
                                        (int)($price_array['min'] + ($i + 1) * $delta)
                                    );
                                }
                            }
                            if (isset($selected_filters['price']) && isset($selected_filters['price'][0])
                                && isset($selected_filters['price'][1])) {
                                $price_array['values'][0] = $selected_filters['price'][0];
                                $price_array['values'][1] = $selected_filters['price'][1];
                            }
                            $filter_blocks[] = $price_array;
                        }
                    }
                    break;

                case 'weight':
                    $weight_array = array(
                        'type_lite' => 'weight',
                        'type' => 'weight',
                        'id_key' => 0,
                        'name' => $this->getTranslation('weight', 'product-service'),
                        'nameKey' => 'weight',
                        'slider' => true,
                        'max' => '0',
                        'min' => null,
                        'values' => array('1' => 0),
                        'unit' => Configuration::get('PS_WEIGHT_UNIT'),
                        'format' => 5, // Ex: xxxxx kg
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );
                    if (isset($products) && $products) {
                        foreach ($products as $product) {
                            if (is_null($weight_array['min'])) {
                                $weight_array['min'] = $product['weight'];
                                $weight_array['values'][0] = $product['weight'];
                            } elseif ($weight_array['min'] > $product['weight']) {
                                $weight_array['min'] = $product['weight'];
                                $weight_array['values'][0] = $product['weight'];
                            }

                            if ($weight_array['max'] < $product['weight']) {
                                $weight_array['max'] = $product['weight'];
                                $weight_array['values'][1] = $product['weight'];
                            }
                        }
                    }
                    if ($weight_array['max'] != $weight_array['min'] && $weight_array['min'] != null) {
                        if (isset($selected_filters['weight']) && isset($selected_filters['weight'][0])
                            && isset($selected_filters['weight'][1])) {
                            $weight_array['values'][0] = $selected_filters['weight'][0];
                            $weight_array['values'][1] = $selected_filters['weight'][1];
                        }
                        $filter_blocks[] = $weight_array;
                    }
                    break;

                case 'condition':
                    $condition_array = array(
                        'new' => array('name' => 'New', 'nbr' => 0),
                        'used' => array('name' => 'Used', 'nbr' => 0),
                        'refurbished' => array('name' => 'Refurbished',
                            'nbr' => 0)
                    );
                    if (isset($products) && $products) {
                        foreach ($products as $product) {
                            if (isset($selected_filters['condition'])
                                && in_array($product['condition'], $selected_filters['condition'])) {
                                $condition_array[$product['condition']]['checked'] = true;
                            }
                        }
                    }
                    foreach ($condition_array as $key => $condition) {
                        if (isset($selected_filters['condition']) && in_array($key, $selected_filters['condition'])) {
                            $condition_array[$key]['checked'] = true;
                        }
                    }
                    if (isset($products) && $products) {
                        foreach ($products as $product) {
                            if (isset($condition_array[$product['condition']])) {
                                $condition_array[$product['condition']]['nbr']++;
                            }
                        }
                    }
                    $filter_blocks[] = array(
                        'type_lite' => 'condition',
                        'type' => 'condition',
                        'id_key' => 0,
                        'name' => $this->getTranslation('condition', 'product-service'),
                        'nameKey' => 'condition',
                        'values' => $condition_array,
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );
                    break;

                case 'quantity':
                    $quantity_array = array(
                        0 => array('name' => 'Not available', 'nbr' => 0),
                        1 => array('name' => 'In stock', 'nbr' => 0)
                    );
                    foreach ($quantity_array as $key => $quantity) {
                        if (isset($selected_filters['quantity']) && in_array($key, $selected_filters['quantity'])) {
                            $quantity_array[$key]['checked'] = true;
                        }
                    }
                    if (isset($products) && $products) {
                        foreach ($products as $product) {
                            //If oosp move all not available quantity to available quantity
                            if ((int)$product['quantity'] > 0
                                || Product::isAvailableWhenOutOfStock($product['out_of_stock'])) {
                                $quantity_array[1]['nbr']++;
                            } else {
                                $quantity_array[0]['nbr']++;
                            }
                        }
                    }

                    $filter_blocks[] = array(
                        'type_lite' => 'quantity',
                        'type' => 'quantity',
                        'id_key' => 0,
                        'name' => $this->getTranslation('availability', 'product-service'),
                        'nameKey' => 'quantity',
                        'values' => $quantity_array,
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type']
                    );

                    break;

                case 'manufacturer':
                    if (isset($products) && $products) {
                        $manufaturers_array = array();
                        foreach ($products as $manufacturer) {
                            if (!isset($manufaturers_array[$manufacturer['id_manufacturer']])) {
                                $manufaturers_array[$manufacturer['id_manufacturer']] = array(
                                    'name' => $manufacturer['name'],
                                    'nbr' => $manufacturer['nbr']
                                );
                            }

                            if (isset($selected_filters['manufacturer'])
                                && in_array((int)$manufacturer['id_manufacturer'], $selected_filters['manufacturer'])) {
                                $manufaturers_array[$manufacturer['id_manufacturer']]['checked'] = true;
                            }
                        }
                        $filter_blocks[] = array(
                            'type_lite' => 'manufacturer',
                            'type' => 'manufacturer',
                            'id_key' => 0,
                            'name' => $this->getTranslation('Manufacturer', 'product-service'),
                            'nameKey' => 'manufacturer',
                            'values' => $manufaturers_array,
                            'filter_show_limit' => $filter['filter_show_limit'],
                            'filter_type' => $filter['filter_type']
                        );
                    }
                    break;

                case 'id_attribute_group':
                    $attributes_array = array();
                    if (isset($products) && $products) {
                        foreach ($products as $attributes) {
                            if (!isset($attributes_array[$attributes['id_attribute_group']])) {
                                $attributes_array[$attributes['id_attribute_group']] = array(
                                    'type_lite' => 'id_attribute_group',
                                    'type' => 'id_attribute_group',
                                    'id_key' => (int)$attributes['id_attribute_group'],
                                    'name' => $attributes['attribute_group_name'],
                                    'is_color_group' => (bool)$attributes['is_color_group'],
                                    'values' => array(),
                                    'url_name' => $attributes['name_url_name'],
                                    'meta_title' => $attributes['name_meta_title'],
                                    'filter_show_limit' => $filter['filter_show_limit'],
                                    'filter_type' => $filter['filter_type']
                                );
                            }

                            if (!isset($attributes_array[$attributes['id_attribute_group']]
                                ['values']
                                [$attributes['id_attribute']])) {
                                $attributes_array[$attributes['id_attribute_group']]
                                ['values']
                                [$attributes['id_attribute']] = array(
                                    'color' => $attributes['color'],
                                    'name' => $attributes['attribute_name'],
                                    'nbr' => (int)$attributes['nbr'],
                                    'url_name' => $attributes['value_url_name'],
                                    'meta_title' => $attributes['value_meta_title']
                                );
                            }

                            if (isset($selected_filters['id_attribute_group'][$attributes['id_attribute']])) {
                                $attributes_array[$attributes['id_attribute_group']]
                                ['values']
                                [$attributes['id_attribute']]['checked'] = true;
                            }
                        }

                        $filter_blocks = array_merge($filter_blocks, $attributes_array);
                    }
                    break;
                case 'id_feature':
                    $feature_array = array();
                    if (isset($products) && $products) {
                        foreach ($products as $feature) {
                            if (!isset($feature_array[$feature['id_feature']])) {
                                $feature_array[$feature['id_feature']] = array(
                                    'type_lite' => 'id_feature',
                                    'type' => 'id_feature',
                                    'id_key' => (int)$feature['id_feature'],
                                    'values' => array(),
                                    'name' => $feature['feature_name'],
                                    'url_name' => $feature['name_url_name'],
                                    'meta_title' => $feature['name_meta_title'],
                                    'filter_show_limit' => $filter['filter_show_limit'],
                                    'filter_type' => $filter['filter_type']
                                );
                            }

                            if (!isset($feature_array[$feature['id_feature']]
                                ['values']
                                [$feature['id_feature_value']])) {
                                $feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']] = array(
                                    'nbr' => (int)$feature['nbr'],
                                    'name' => $feature['value'],
                                    'url_name' => $feature['value_url_name'],
                                    'meta_title' => $feature['value_meta_title']
                                );
                            }

                            if (isset($selected_filters['id_feature'][$feature['id_feature_value']])) {
                                $feature_array[$feature['id_feature']]
                                ['values']
                                [$feature['id_feature_value']]
                                ['checked'] = true;
                            }
                        }

                        //Natural sort
                        foreach ($feature_array as $key => $value) {
                            $temp = array();
                            foreach ($feature_array[$key]['values'] as $keyint => $valueint) {
                                $temp[$keyint] = $valueint['name'];
                            }

                            natcasesort($temp);
                            $temp2 = array();

                            foreach ($temp as $keytemp => $valuetemp) {
                                $temp2[$keytemp] = $feature_array[$key]['values'][$keytemp];
                            }

                            $feature_array[$key]['values'] = $temp2;
                        }

                        $filter_blocks = array_merge($filter_blocks, $feature_array);
                    }
                    break;

                case 'category':
                    $tmp_array = array();
                    if (isset($products) && $products) {
                        $categories_with_products_count = 0;
                        foreach ($products as $category) {
                            $tmp_array[$category['id_category']] = array(
                                'name' => $category['name'],
                                'nbr' => (int)$category['count_products'],
                                'valueKey' => $category['id_category']
                            );

                            if ((int)$category['count_products']) {
                                $categories_with_products_count++;
                            }

                            if (isset($selected_filters['category'])
                                && in_array($category['id_category'], $selected_filters['category'])) {
                                $tmp_array[$category['id_category']]['checked'] = true;
                            }
                        }
                        if ($categories_with_products_count || !Configuration::get('PS_LAYERED_HIDE_0_VALUES')) {
                            $filter_blocks[] = array(
                                'type_lite' => 'category',
                                'type' => 'category',
                                'id_key' => 0,
                                'name' => $this->getTranslation('category', 'product-service'),
                                'nameKey' => 'category',
                                'values' => $tmp_array,
                                'filter_show_limit' => $filter['filter_show_limit'],
                                'filter_type' => $filter['filter_type']
                            );
                        }
                    }
                    break;
            }
        }

        // All non indexable attribute and feature
        $non_indexable = array();

        // Get all non indexable attribute groups
        foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT public_name
		FROM `' . _DB_PREFIX_ . 'attribute_group_lang` agl
		LEFT JOIN `' . _DB_PREFIX_ . 'layered_indexable_attribute_group` liag
		ON liag.id_attribute_group = agl.id_attribute_group
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = ' . (int)$id_lang) as $attribute) {
            $non_indexable[] = Tools::link_rewrite($attribute['public_name']);
        }

        // Get all non indexable features
        foreach (Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT name
		FROM `' . _DB_PREFIX_ . 'feature_lang` fl
		LEFT JOIN  `' . _DB_PREFIX_ . 'layered_indexable_feature` lif
		ON lif.id_feature = fl.id_feature
		WHERE indexable IS NULL OR indexable = 0
		AND id_lang = ' . (int)$id_lang) as $attribute) {
            $non_indexable[] = Tools::link_rewrite($attribute['name']);
        }

        //generate SEO link
        $param_selected = '';
        $param_product_url = '';
        $option_checked_array = array();
        $param_group_selected_array = array();
        $title_values = array();
        $meta_values = array();

        //get filters checked by group

        foreach ($filter_blocks as $type_filter) {
            $filter_name = (!empty($type_filter['url_name']) ? $type_filter['url_name'] : $type_filter['name']);
            $filter_meta = (!empty($type_filter['meta_title']) ? $type_filter['meta_title'] : $type_filter['name']);
            $attr_key = $type_filter['type'] . '_' . $type_filter['id_key'];

            $param_group_selected = '';
            $lower_filter = Tools::strtolower($type_filter['type']);
            $filter_name_rewritten = Tools::link_rewrite($filter_name);

            if (($lower_filter == 'price' || $lower_filter == 'weight')
                && (float)$type_filter['values'][0] > (float)$type_filter['min']
                && (float)$type_filter['values'][1] > (float)$type_filter['max']) {
                $param_group_selected .=
                    $this->getAnchor() . str_replace($this->getAnchor(), '_', $type_filter['values'][0])
                    . $this->getAnchor() . str_replace($this->getAnchor(), '_', $type_filter['values'][1]);
                $param_group_selected_array[$filter_name_rewritten][] = $filter_name_rewritten;

                if (!isset($title_values[$filter_meta])) {
                    $title_values[$filter_meta] = array();
                }
                $title_values[$filter_meta][] = $filter_meta;
                if (!isset($meta_values[$attr_key])) {
                    $meta_values[$attr_key] = array('title' => $filter_meta, 'values' => array());
                }
                $meta_values[$attr_key]['values'][] = $filter_meta;
            } else {
                foreach ($type_filter['values'] as $key => $value) {
                    if (is_array($value) && array_key_exists('checked', $value)) {
                        $value_name = !empty($value['url_name']) ? $value['url_name'] : $value['name'];
                        $value_meta = !empty($value['meta_title']) ? $value['meta_title'] : $value['name'];
                        $param_group_selected .=
                            $this->getAnchor() . str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name));
                        $param_group_selected_array[$filter_name_rewritten][] = Tools::link_rewrite($value_name);

                        if (!isset($title_values[$filter_meta])) {
                            $title_values[$filter_meta] = array();
                        }
                        $title_values[$filter_meta][] = $value_name;
                        if (!isset($meta_values[$attr_key])) {
                            $meta_values[$attr_key] = array('title' => $filter_meta, 'values' => array());
                        }
                        $meta_values[$attr_key]['values'][] = $value_meta;
                    } else {
                        $param_group_selected_array[$filter_name_rewritten][] = array();
                    }
                }
            }

            if (!empty($param_group_selected)) {
                $param_selected .=
                    '/' . str_replace($this->getAnchor(), '_', $filter_name_rewritten) . $param_group_selected;
                $option_checked_array[$filter_name_rewritten] = $param_group_selected;
            }
            // select only attribute and group attribute to display an unique product combination link
            if (!empty($param_group_selected) && $type_filter['type'] == 'id_attribute_group') {
                $param_product_url .=
                    '/' . str_replace($this->getAnchor(), '_', $filter_name_rewritten) . $param_group_selected;
            }
        }

        if ($this->page > 1) {
            $param_selected .= '/page-' . $this->page;
        }

        $blacklist = array('weight', 'price');

        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CDT')) {
            $blacklist[] = 'condition';
        }

        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_QTY')) {
            $blacklist[] = 'quantity';
        }

        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_MNF')) {
            $blacklist[] = 'manufacturer';
        }

        if (!Configuration::get('PS_LAYERED_FILTER_INDEX_CAT')) {
            $blacklist[] = 'category';
        }

        $global_nofollow = false;
        $categorie_link = Context::getContext()->link->getCategoryLink($parent, null, null);

        foreach ($filter_blocks as &$type_filter) {
            $filter_name = (!empty($type_filter['url_name']) ? $type_filter['url_name'] : $type_filter['name']);
            $filter_link_rewrite = Tools::link_rewrite($filter_name);

            if (count($type_filter) > 0 && !isset($type_filter['slider'])) {
                foreach ($type_filter['values'] as $key => $values) {
                    $nofollow = false;
                    if (!empty($values['checked']) && in_array($type_filter['type'], $blacklist)) {
                        $global_nofollow = true;
                    }

                    $option_checked_clone_array = $option_checked_array;

                    // If not filters checked, add parameter
                    $value_name = !empty($values['url_name']) ? $values['url_name'] : $values['name'];

                    if (!in_array(
                        Tools::link_rewrite($value_name),
                        $param_group_selected_array[$filter_link_rewrite]
                    )) {
                        // Update parameter filter checked before
                        if (array_key_exists($filter_link_rewrite, $option_checked_array)) {
                            $option_checked_clone_array[$filter_link_rewrite] =
                                $option_checked_clone_array[$filter_link_rewrite] .
                                $this->getAnchor() .
                                str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name));
                            if (in_array($type_filter['type'], $blacklist)) {
                                $nofollow = true;
                            }
                        } else {
                            $option_checked_clone_array[$filter_link_rewrite] =
                                $this->getAnchor() .
                                str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name));
                        }
                    } else {
                        // Remove selected parameters
                        $option_checked_clone_array[$filter_link_rewrite] = str_replace(
                            $this->getAnchor() . str_replace($this->getAnchor(), '_', Tools::link_rewrite($value_name)),
                            '',
                            $option_checked_clone_array[$filter_link_rewrite]
                        );
                        if (empty($option_checked_clone_array[$filter_link_rewrite])) {
                            unset($option_checked_clone_array[$filter_link_rewrite]);
                        }
                    }
                    $parameters = '';
                    ksort($option_checked_clone_array); // Order parameters
                    foreach ($option_checked_clone_array as $key_group => $value_group) {
                        $parameters .= '/' . str_replace($this->getAnchor(), '_', $key_group) . $value_group;
                    }

                    // Add nofollow if any blacklisted filters ins in parameters
                    foreach ($filter_blocks as $filter) {
                        $name =
                            Tools::link_rewrite((!empty($filter['url_name']) ? $filter['url_name'] : $filter['name']));
                        if (in_array($filter['type'], $blacklist) && strpos($parameters, $name . '-') !== false) {
                            $nofollow = true;
                        }
                    }

                    // Check if there is an non indexable attribute or feature in the url
                    foreach ($non_indexable as $value) {
                        if (strpos($parameters, '/' . $value) !== false) {
                            $nofollow = true;
                        }
                    }

                    $type_filter['values'][$key]['link'] = $categorie_link . '#' . ltrim($parameters, '/');
                    $type_filter['values'][$key]['rel'] = ($nofollow) ? 'nofollow' : '';
                }
            }
        }

        $n_filters = 0;

        if (isset($selected_filters['price'])) {
            if ($price_array['min'] == $selected_filters['price'][0] && $price_array['max'] == $selected_filters['price'][1]) {
                unset($selected_filters['price']);
            }
        }
        if (isset($selected_filters['weight'])) {
            if ($weight_array['min'] == $selected_filters['weight'][0] && $weight_array['max'] == $selected_filters['weight'][1]) {
                unset($selected_filters['weight']);
            }
        }

        foreach ($selected_filters as $filters) {
            $n_filters += count($filters);
        }

        $cache = array(
            'layered_show_qties' => (int)Configuration::get('PS_LAYERED_SHOW_QTIES'),
            'id_category_layered' => (int)$id_parent,
            'selected_filters' => $selected_filters,
            'n_filters' => (int)$n_filters,
            'nbr_filterBlocks' => count($filter_blocks),
            'filters' => $filter_blocks,
            'title_values' => $title_values,
            'meta_values' => $meta_values,
            'current_friendly_url' => $param_selected,
            'param_product_url' => $param_product_url,
            'no_follow' => (!empty($param_selected) || $global_nofollow)
        );

        $this->nav_filter_block = $cache;
        return;
    }

    private static function getPriceFilterSubQuery($filter_value, $ignore_join = false)
    {
        $id_currency = (int)Context::getContext()->currency->id;

        if (isset($filter_value) && $filter_value) {
            $price_filter_query = '
			INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi
			ON (psi.id_product = p.id_product AND psi.id_currency = ' . (int)$id_currency . '
			AND psi.price_min <= ' . (int)$filter_value[1] . '
			AND psi.price_max >= ' . (int)$filter_value[0] . '
			AND psi.id_shop=' . (int)Context::getContext()->shop->id . ') ';
            return array('join' => $price_filter_query);
        }
        return array();
    }

    private static function filterProductsByPrice($filter_value, $product_collection)
    {
        static $ps_layered_filter_price_usetax = null;
        static $ps_layered_filter_price_rounding = null;

        if (empty($filter_value)) {
            return $product_collection;
        }

        if ($ps_layered_filter_price_usetax === null) {
            $ps_layered_filter_price_usetax = Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX');
        }

        if ($ps_layered_filter_price_rounding === null) {
            $ps_layered_filter_price_rounding = Configuration::get('PS_LAYERED_FILTER_PRICE_ROUNDING');
        }

        foreach ($product_collection as $key => $product) {
            if (isset($filter_value) && $filter_value && isset($product['price_min']) && isset($product['id_product'])
                && (($product['price_min'] < (int)$filter_value[0] && $product['price_max'] > (int)$filter_value[0])
                    || ($product['price_max'] > (int)$filter_value[1]
                        && $product['price_min'] < (int)$filter_value[1]))) {
                $price = Product::getPriceStatic($product['id_product'], $ps_layered_filter_price_usetax);
                if ($ps_layered_filter_price_rounding) {
                    $price = (int)$price;
                }
                if ($price < $filter_value[0] || $price > $filter_value[1]) {
                    unset($product_collection[$key]);
                }
            }
        }
        return $product_collection;
    }

    private static function getWeightFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (isset($filter_value) && $filter_value) {
            if ($filter_value[0] != 0 || $filter_value[1] != 0) {
                return array('where' => ' AND p.`weight`
                BETWEEN ' . (float)($filter_value[0] - 0.001) .
                    ' AND ' . (float)($filter_value[1] + 0.001) . ' ');
            }
        }

        return array();
    }

    private static function getIdFeatureFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value)) {
            return array();
        }
        $query_filters =
            ' AND EXISTS (SELECT * FROM ' . _DB_PREFIX_ . 'feature_product fp WHERE fp.id_product = p.id_product AND ';

        foreach ($filter_value as $filter_val) {
            $query_filters .= 'fp.`id_feature_value` = ' . (int)$filter_val . ' OR ';
        }
        $query_filters = rtrim($query_filters, 'OR ') . ') ';

        return array('where' => $query_filters);
    }

    private static function getIdAttributeGroupFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value)) {
            return array();
        }

        $query_filters = '
		AND EXISTS (SELECT *
		FROM `' . _DB_PREFIX_ . 'product_attribute_combination` pac
		LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (pa.`id_product_attribute` = pac.`id_product_attribute`)
		WHERE pa.id_product = p.id_product AND ';

        foreach ($filter_value as $filter_val) {
            $query_filters .= 'pac.`id_attribute` = ' . (int)$filter_val . ' OR ';
        }
        $query_filters = rtrim($query_filters, 'OR ') . ') ';

        return array('where' => $query_filters);
    }

    private static function getCategoryFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value)) {
            return array();
        }
        $query_filters_where =
            ' AND EXISTS (SELECT * FROM ' . _DB_PREFIX_ . 'category_product cp WHERE id_product = p.id_product AND ';
        foreach ($filter_value as $id_category) {
            $query_filters_where .= 'cp.`id_category` = ' . (int)$id_category . ' OR ';
        }
        $query_filters_where = rtrim($query_filters_where, 'OR ') . ') ';

        return array('where' => $query_filters_where);
    }

    private static function getQuantityFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (count($filter_value) == 2 || empty($filter_value)) {
            return array();
        }

        $query_filters_join = '';

        $query_filters = ' AND sav.quantity ' . (!$filter_value[0] ? '<=' : '>') . ' 0 ';
        $query_filters_join =
            'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav
            ON (sav.id_product = p.id_product AND sav.id_shop = ' . (int)Context::getContext()->shop->id . ') ';

        return array('where' => $query_filters, 'join' => $query_filters_join);
    }

    private static function getManufacturerFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value)) {
            $query_filters = '';
        } else {
            array_walk($filter_value, create_function(
                '&$id_manufacturer',
                '$id_manufacturer = (int)$id_manufacturer;'
            ));
            $query_filters = ' AND p.id_manufacturer IN (' . implode($filter_value, ',') . ')';
        }

        if ($ignore_join) {
            return array('where' => $query_filters);
        } else {
            return array(
                'where' => $query_filters,
                'join' => 'LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.id_manufacturer = p.id_manufacturer) '
            );
        }
    }

    private static function getConditionFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (count($filter_value) == 3 || empty($filter_value)) {
            return array();
        }

        $query_filters = ' AND p.condition IN (';

        foreach ($filter_value as $cond) {
            $query_filters .= '\'' . $cond . '\',';
        }
        $query_filters = rtrim($query_filters, ',') . ') ';
        return array('where' => $query_filters);
    }

    public function cleanFilterByIdValue($attributes, $id_value)
    {
        $selected_filters = array();
        if (is_array($attributes)) {
            foreach ($attributes as $attribute) {
                $attribute_data = explode('_', $attribute);
                if ($attribute_data[0] == $id_value) {
                    $selected_filters[] = $attribute_data[1];
                }
            }
        }
        return $selected_filters;
    }

    protected function showPriceFilter()
    {
        return Group::getCurrent()->show_prices;
    }

    protected function getAnchor()
    {
        static $anchor = null;
        if ($anchor === null) {
            if (!$anchor = Configuration::get('PS_ATTRIBUTE_ANCHOR_SEPARATOR')) {
                $anchor = '-';
            }
        }
        return $anchor;
    }

    protected function filterVar($value)
    {
        if (version_compare(_PS_VERSION_, '1.6.0.7', '>=') === true) {
            return Tools::purifyHTML($value);
        } else {
            return filter_var($value, FILTER_SANITIZE_STRING);
        }
    }

    private function transformPriceFilterValues($filter, $selected_filters = array())
    {
        $data = array();

        if (!is_array($filter)) return $data;

        $filterValues = isset($filter['list_of_values']) ? $filter['list_of_values'] : array();
        $symbol = isset($filter['unit']) ? $filter['unit'] : '';

        foreach ($filterValues as $value) {
            if (is_array($value) && count($value) >= 2) {
                $formatedName = $value[0] . '-' . $value[1];
                $formatedItem = sprintf('%s-%s', str_replace($this->getAnchor(), '_', Tools::link_rewrite($filter['name'])), $formatedName);
                if (in_array($formatedItem, $selected_filters)) {
                    continue;
                }

                $item = new JmFilterValue();
                $item->name = $symbol . $value[0] . ' - ' . $symbol . $value[1];
                $item->valueKey = $formatedName;
                $data[] = $item;
            }
        }

        return $data;
    }

    /**
     * This function change id_attribute to IdAttribute
     */
    private function correctFilterType($filterType)
    {
        $result = '';

        if (!$filterType) {
            return $result;
        }

        $values = explode('_', $filterType);
        foreach ($values as $value) {
            $result = $result . Tools::ucfirst($value);
        }

        return $result;
    }
}

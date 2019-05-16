<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductsSearchService16 extends BaseService
{
    /** @var array Products to be displayed in the current page . */
    protected $cat_products;

    /** @var array Controller errors */
    public $errors = array();

    /** @var string ORDER BY field */
    public $orderBy;

    /** @var string Order way string ('ASC', 'DESC') */
    public $orderWay;

    /** @var int Current page number */
    public $p;

    /** @var int Items (products) per page */
    public $n;

    /** @var int Number of products in the current page. */
    protected $nbProducts;

    /** @var string Search query (keywords). */
    public $query;

    /** var boolean - Display quantity order if stock management disabled */
    public $stock_management;

    private $id_lang;

    protected $id_customer;

    protected $order_by_values  = array(0 => 'name',
        1 => 'price',
        2 => 'date_add',
        3 => 'date_upd',
        4 => 'position',
        5 => 'manufacturer_name',
        6 => 'quantity',
        7 => 'reference');
    protected $order_way_values = array(0 => 'asc', 1 => 'desc');

    protected $array_product_hide;
    public function doExecute()
    {
        $this->query = Tools::getValue('query');
        $this->id_lang = Tools::getValue('id_lang');
        $this->p = Tools::getValue('page_num', 1);

        $this->n = Tools::getValue('page_size', 20);

        $cookie = Context::getContext()->cookie;
        if (Tools::getValue('id_customer')) {
            $this->id_customer = Tools::getValue('id_customer');
            $customer = new Customer(Tools::getValue('id_customer'));
            if ($customer->id) {
                $cookie->__set('id_customer', $customer->id);
                $cookie->__set('logged', 1);
                Context::getContext()->customer = $customer;
            }
        }
        $this->initializeCart(Context::getContext());
        Product::initPricesComputation();
        $this->productSort();
        $this->productSearch();
        $products = $this->transformProductList();
        $this->setBannerInfo($products);
        $nav_layered = $this->transformNavigationLayered();
        $this->response = new \JmProductsResponse();
        $this->response->products = $products;
        $this->response->navigation_layered = $nav_layered;
    }

    protected function setBannerInfo(&$products)
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

    protected function transformNavigationLayered()
    {
        $data = array();
        $navigation_layered = new \JmNavigationLayered();
        $navigation_layered->order_by = $this->orderBy;
        $navigation_layered->order_way = $this->orderWay;
        $navigation_layered->order_by_values = $this->localizeOrderByValues();
        $navigation_layered->order_way_values = $this->order_way_values;

        $data[] = $navigation_layered;
        return $data;
    }

    public function productSort()
    {
        $this->stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false;
        $this->orderBy  = Tools::strtolower(
            Tools::getValue('orderby', $this->order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')])
        );
        $this->orderWay = Tools::strtolower(
            Tools::getValue('orderway', $this->order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')])
        );

        if (!in_array($this->orderBy, $this->order_by_values)) {
            $this->orderBy =  Tools::getProductsOrder("by");
        }

        if (!in_array($this->orderWay, $this->order_way_values)) {
            $this->orderWay =  Tools::getProductsOrder("way");
        }
    }

    public function productSearch()
    {
        $result = $this->findProducts(
            $this->id_lang,
            $this->query,
            $this->p,
            $this->n,
            $this->orderBy,
            $this->orderWay
        );
        $this->cat_products = $result['result'];
    }

    public function transformProductList()
    {
        $data = array();
        $flag = true;

        foreach ($this->cat_products as $product) {
            $prod = \ProductDataTransform::productList($product, $this->stock_management, $this->id_customer);
            $data[] = $prod;
        }
        return $data;
    }

    protected function localizeOrderByValues()
    {
//        $order_by_values = array();
//        $order_by_configuration=json_decode(ConfigurationCore::get(JM_ORDER_BY_VALUES));
//        //If configuration exist.
//        if (!empty($order_by_configuration)) {
//            foreach ($order_by_configuration->orderBy as $key => $value) {
//                if (strcmp($value, 'enable')==0) {
//                    $order_by_values[$key] = $this->getTranslation($key, 'order-by-values');
//                }
//            }
//            return $order_by_values;
//        } else {
            // If there's no configuration, return all sort option as default.
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
        //}
    }

    public static function findProducts(
        $id_lang,
        $expr,
        $page_number = 1,
        $page_size = 1,
        $order_by = 'position',
        $order_way = 'desc',
        $ajax = false,
        $use_cookie = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }
        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);

        // TODO : smart page management
        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($page_size < 1) {
            $page_size = 1;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            return false;
        }

        $intersect_array = array();
        $score_array = array();
        $words = explode(' ', Search::sanitize($expr, $id_lang, false, $context->language->iso_code));

        foreach ($words as $key => $word) {
            if (!empty($word) && Tools::strlen($word) >= (int)Configuration::get('PS_SEARCH_MINWORDLEN')) {
                $word = str_replace(array('%', '_'), array('\\%', '\\_'), $word);
                $start_search = Configuration::get('PS_SEARCH_START') ? '%': '';
                $end_search = Configuration::get('PS_SEARCH_END') ? '': '%';

                $intersect_array[] = 'SELECT DISTINCT si.id_product
					FROM '._DB_PREFIX_.'search_word sw
					LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = '.(int)$id_lang.'
						AND sw.id_shop = '.$context->shop->id.'
						AND sw.word LIKE
					'.($word[0] == '-'
                        ? ' \''.$start_search.pSQL(Tools::substr($word, 1, PS_SEARCH_MAX_WORD_LENGTH)).$end_search.'\''
                        : ' \''.$start_search.pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).$end_search.'\''
                    );

                if ($word[0] != '-') {
                    $score_array[] = 'sw.word LIKE \''.$start_search.
                        pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)).$end_search.'\'';
                }
            } else {
                unset($words[$key]);
            }
        }

        if (!count($words)) {
            return ($ajax ? array() : array('total' => 0, 'result' => array()));
        }

        $score = '';
        if (is_array($score_array) && !empty($score_array)) {
            $score = ',(
				SELECT SUM(weight)
				FROM '._DB_PREFIX_.'search_word sw
				LEFT JOIN '._DB_PREFIX_.'search_index si ON sw.id_word = si.id_word
				WHERE sw.id_lang = '.(int)$id_lang.'
					AND sw.id_shop = '.$context->shop->id.'
					AND si.id_product = p.id_product
					AND ('.implode(' OR ', $score_array).')
			) position';
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1');
        }
        // Versions 1.6.1.11 and above have new search query, 1.6.1.10 and below have old search query
        if (version_compare(_PS_VERSION_, '1.6.1.11', '>=')) {
            $results = $db->executeS('
		SELECT DISTINCT cp.`id_product`
		FROM `'._DB_PREFIX_.'category_product` cp
		'.(Group::isFeatureActive() ?
                'INNER JOIN `'._DB_PREFIX_.'category_group` cg ON cp.`id_category` = cg.`id_category`' : '').'
		INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
		INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
		'.Shop::addSqlAssociation('product', 'p', false).'
		WHERE c.`active` = 1
		AND product_shop.`active` = 1
		AND product_shop.`visibility` IN ("both", "search")
		AND product_shop.indexed = 1
		'.$sql_groups, true, false);

            $eligible_products = array();
            foreach ($results as $row) {
                $eligible_products[] = $row['id_product'];
            }

            $eligible_products2 = array();
            foreach ($intersect_array as $query) {
                foreach ($db->executeS($query, true, false) as $row) {
                    $eligible_products2[] = $row['id_product'];
                }
            }
            $eligible_products = array_unique(array_intersect($eligible_products, array_unique($eligible_products2)));
            if (!count($eligible_products)) {
                return ($ajax ? array() : array('total' => 0, 'result' => array()));
            }
        } else {
            // old search query for 1.6.10 and older
            $results = $db->executeS('
		SELECT cp.`id_product`
		FROM `'._DB_PREFIX_.'category_product` cp
		'.(Group::isFeatureActive() ? 'INNER JOIN `'._DB_PREFIX_.'category_group` cg ON cp.`id_category` = cg.`id_category`' : '').'
		INNER JOIN `'._DB_PREFIX_.'category` c ON cp.`id_category` = c.`id_category`
		INNER JOIN `'._DB_PREFIX_.'product` p ON cp.`id_product` = p.`id_product`
		'.Shop::addSqlAssociation('product', 'p', false).'
		WHERE c.`active` = 1
		AND product_shop.`active` = 1
		AND product_shop.`visibility` IN ("both", "search")
		AND product_shop.indexed = 1
		'.$sql_groups, true, false);

            $eligible_products = array();
            foreach ($results as $row) {
                $eligible_products[] = $row['id_product'];
            }
            foreach ($intersect_array as $query) {
                $eligible_products2 = array();
                foreach ($db->executeS($query, true, false) as $row) {
                    $eligible_products2[] = $row['id_product'];
                }

                $eligible_products = array_intersect($eligible_products, $eligible_products2);
                if (!count($eligible_products)) {
                    return ($ajax ? array() : array('total' => 0, 'result' => array()));
                }
            }

            $eligible_products = array_unique($eligible_products);
        }

        $product_pool = '';
        foreach ($eligible_products as $id_product) {
            if ($id_product) {
                $product_pool .= (int)$id_product.',';
            }
        }
        if (empty($product_pool)) {
            return ($ajax ? array() : array('total' => 0, 'result' => array()));
        }
        $product_pool = ((strpos($product_pool, ',') === false) ?
            (' = '.(int)$product_pool.' ') : (' IN ('.rtrim($product_pool, ',').') '));

        if ($ajax) {
            $sql = 'SELECT DISTINCT p.id_product, pl.name pname, cl.name cname,
						cl.link_rewrite crewrite, pl.link_rewrite prewrite '.$score.'
					FROM '._DB_PREFIX_.'product p
					INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
					)
					'.Shop::addSqlAssociation('product', 'p').'
					INNER JOIN `'._DB_PREFIX_.'category_lang` cl ON (
						product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('cl').'
					)
					WHERE p.`id_product` '.$product_pool.'
					ORDER BY position DESC LIMIT 10';
            return $db->executeS($sql, true, false);
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }
        $alias = '';
        if ($order_by == 'price') {
            $alias = 'product_shop.';
        } elseif (in_array($order_by, array('date_upd', 'date_add'))) {
            $alias = 'p.';
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

        $sql = 'SELECT p.*,jm.`not_visible`, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
			 image_shop.`id_image` id_image, il.`legend`, m.`name` manufacturer_name '.$score.',
				DATEDIFF(
					p.`date_add`,
					DATE_SUB(
						"'.date('Y-m-d').' 00:00:00",
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 new'.(Combination::isFeatureActive() ?
                ', product_attribute_shop.minimal_quantity
                AS product_attribute_minimal_quantity,
                IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').'
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				'.(Combination::isFeatureActive() ?
                'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product`
				AND product_attribute_shop.`default_on` = 1
				AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product`
					AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
				ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
				LEFT JOIN `'._DB_PREFIX_.'jm_product_visibility` jm ON (p.`id_product` = jm.`id_product`)
				
				WHERE (jm.`not_visible` =1 OR jm.`not_visible` is null) AND p.`id_product` '.$product_pool.'
				GROUP BY product_shop.id_product
				'.($order_by ? 'ORDER BY  '.$alias.$order_by : '').($order_way ? ' '.$order_way : '').'
				LIMIT '.(int)(($page_number - 1) * $page_size).','.(int)$page_size;
        $result = $db->executeS($sql, true, false);

        $sql = 'SELECT COUNT(*)
				FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
				)
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` '.$product_pool;
        $total = $db->getValue($sql, false);

        if (!$result) {
            $result_properties = false;
        } else {
            $result_properties = Product::getProductsProperties((int)$id_lang, $result);
        }

        return array('total' => $total,'result' => $result_properties);
    }
}

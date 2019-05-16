<?php
/**
 *
 * @author Jmango
 * @copyright opyright 2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */
class ProductsManufacturerService16 extends ProductsService
{

    private $manufacturer;
    protected static $currentCustomerGroups;


    public function doExecute()
    {
        //get id_manufacturer if exists.
        $id_manufacturer = $this->getRequestResourceId(3);

        if (!$id_manufacturer || !Validate::isUnsignedId($id_manufacturer)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->response->errors[] = Tools::displayError('Missing manufacturer ID');

            return;
        }

        $this->manufacturer = new Manufacturer((int)$id_manufacturer, $this->id_lang);

        if (!$this->manufacturer || !$this->manufacturer->id) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->response->errors[] = Tools::displayError('Manufacturer not found');

            return;
        }

        if (!Validate::isLoadedObject($this->manufacturer) || !$this->manufacturer->active || !$this->manufacturer->isAssociatedToShop()) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->response->errors[] = Tools::displayError('Manufacturer not found');

            return;
        }

        $this->id_lang = $this->context->language->id;
        $this->id_customer = $this->context->customer->id;
        $this->p = $this->getRequestValue('page_num', 1);
        $this->n = $this->getRequestValue('page_size', 20);
        $this->orderBy = $this->getRequestValue('orderby');
        $this->orderWay = $this->getRequestValue('orderway');

        $this->productSort();
        $this->cat_products = $this->getProducts($this->manufacturer->id, $this->context->language->id, (int)$this->p, $this->n, $this->orderBy, $this->orderWay);

        $products = $this->transformProductList();
        $this->setBanner($products);
        $nav_layered = $this->transformNavigationLayered();

        $this->response = new JmProductsResponse();
        $this->response->products = $products;
        $this->response->navigation_layered = $nav_layered;
    }

    public static function getProducts($id_manufacturer, $id_lang, $p, $n, $order_by = null, $order_way = null, $get_total = false, $active = true, $active_category = true, Context $context = null)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }


        if ($p < 1) {
            $p = 1;
        }

        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'name';
        }

        if (empty($order_way)) {
            $order_way = 'ASC';
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $groups = self::getCurrentCustomerGroups();
        $sql_groups = count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1';

        /* Return only the number of products */
        if ($get_total) {
            $sql = '
				SELECT p.`id_product`,jm.`not_visible`
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				LEFT JOIN `'._DB_PREFIX_.'jm_product_visibility` jm ON (p.`id_product` = jm.`id_product`)
				WHERE (jm.`not_visible` =1 OR jm.`not_visible` is null) AND p.id_manufacturer = '.(int)$id_manufacturer
                .($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				AND EXISTS (
					SELECT 1
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
                ($active_category ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
              
					WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` '.$sql_groups.'
				)';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            return (int)count($result);
        }
        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }
        $alias = '';
        if ($order_by == 'price') {
            $alias = 'product_shop.';
        } elseif ($order_by == 'name') {
            $alias = 'pl.';
        } elseif ($order_by == 'manufacturer_name') {
            $order_by = 'name';
            $alias = 'm.';
        } elseif ($order_by == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        $sql = 'SELECT p.*, product_shop.*,jm.not_visible, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
            .(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').'
			, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
			pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
				DATEDIFF(
					product_shop.`date_add`,
					DATE_SUB(
						"'.date('Y-m-d').' 00:00:00",
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 AS new'
            .' FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p').
            (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
						ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
				ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il
				ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
			LEFT JOIN `'._DB_PREFIX_.'jm_product_visibility` jm
			    ON (p.`id_product` = jm.`id_product`)	
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
				ON (m.`id_manufacturer` = p.`id_manufacturer`)
			'.Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $active_category) {
            $sql .= 'JOIN `'._DB_PREFIX_.'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` '.$sql_groups.')';
            }
            if ($active_category) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE (jm.`not_visible` = 1 OR jm.`not_visible` IS NULL ) AND p.`id_manufacturer` = '.(int)$id_manufacturer.'
				'.($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				GROUP BY p.id_product
				ORDER BY '.$alias.'`'.bqSQL($order_by).'` '.pSQL($order_way).'
				LIMIT '.(((int)$p - 1) * (int)$n).','.(int)$n;

        $result = Db::getInstance()->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }

    public static function getCurrentCustomerGroups()
    {
        if (!Group::isFeatureActive()) {
            return array();
        }

        $context = Context::getContext();
        if (!isset($context->customer) || !$context->customer->id) {
            return array();
        }

        if (!is_array(self::$currentCustomerGroups)) {
            self::$currentCustomerGroups = array();
            $result = Db::getInstance()->executeS('SELECT id_group FROM '._DB_PREFIX_.'customer_group WHERE id_customer = '.(int)$context->customer->id);
            foreach ($result as $row) {
                self::$currentCustomerGroups[] = $row['id_group'];
            }
        }

        return self::$currentCustomerGroups;
    }

    protected function transformNavigationLayered()
    {
        $data = array();

        $navigation_layered = new JmNavigationLayered();

        $navigation_layered->order_by = $this->orderBy;
        $navigation_layered->order_way = $this->orderWay;
        $navigation_layered->order_by_values = $this->localizeOrderByValues();
        $navigation_layered->order_way_values = $this->order_way_values;

        $data[] = $navigation_layered;
        return $data;
    }

    protected function localizeOrderByValues()
    {
        $order_by_values = array();
        $order_by_configuration=json_decode(ConfigurationCore::get(JM_ORDER_BY_VALUES));
        //If configuration exist.
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
//        }
    }

    // Add tags of corresponding language to the response.
    protected function setBanner(&$products)
    {
        $temp = array();
        foreach ($products as $prod) {
            $prod->banner_info = array();
            $temp['code']='on_sale';
            $temp['value']=$prod->on_sale;
            $temp['label']=$this->getTranslation('Sale!', 'product-list');
            $prod->banner_info[]=$temp;
            $temp['code']='new';
            $temp['value']=$prod->new;
            $temp['label']=$this->getTranslation('New', 'product-list');
            $prod->banner_info[]=$temp;
        }
    }

    public function productSort()
    {
        // $this->orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
        // $this->orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
        // 'orderbydefault' => Tools::getProductsOrder('by'),
        // 'orderwayposition' => Tools::getProductsOrder('way'), // Deprecated: orderwayposition
        // 'orderwaydefault' => Tools::getProductsOrder('way'),

        $this->stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false; // no display quantity order if stock management disabled

        $this->orderBy = Tools::strtolower(Tools::getValue('orderby', $this->order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')]));
        $this->orderWay = Tools::strtolower(Tools::getValue('orderway', $this->order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')]));

        if (!in_array($this->orderBy, $this->order_by_values)) {
            $this->orderBy =  Tools::getProductsOrder("by");
        }

        if (!in_array($this->orderWay, $this->order_way_values)) {
            $this->orderWay =  Tools::getProductsOrder("way");
        }
    }
}

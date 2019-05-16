<?php
/**
 * Created by PhpStorm.
 * User: JMango
 * Date: 3/13/18
 * Time: 10:09
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

//require_once _DIR_.DIRECTORY_SEPARATOR.'JmFacetedsearchFiltersConverter.php';
//require_once _DIR_.DIRECTORY_SEPARATOR.'JmFacetedsearchFacetsURLSerializer.php';
//require_once _DIR_.DIRECTORY_SEPARATOR.'JmFacetedsearchRangeAggregator.php';
require_once dirname(__FILE__) . '/JmFacetedsearchFiltersConverter.php';
require_once dirname(__FILE__) . '/JmFacetedsearchFacetsURLSerializer.php';
require_once dirname(__FILE__) . '/JmFacetedsearchRangeAggregator.php';

use PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\Facet;
use PrestaShop\PrestaShop\Core\Product\Search\FacetCollection;
use PrestaShop\PrestaShop\Core\Product\Search\Filter;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;

class JmProductSearchProvider implements ProductSearchProviderInterface
{
    private $context;
    private $filtersConverter;
    private $facetsSerializer;
    private $nbr_products;
    private $ps_layered_full_tree;
    private $sortOrderFactory;
    private $SHOW_ON_MOBILE = 1;

    public function __construct()
    {
        $this->context = Context::getContext();
        $this->filtersConverter = new JmFacetedsearchFiltersConverter();
        $this->facetsSerializer = new JmFacetedsearchFacetsURLSerializer();
        $this->sortOrderFactory = new SortOrderFactory(Context::getContext()->getTranslator());
        $this->ps_layered_full_tree = Configuration::get('PS_LAYERED_FULL_TREE');
    }

    public function getFacetCollectionFromEncodedFacets(
        ProductSearchQuery $query
    ) {
        // do not compute range filters, all info we need is encoded in $encodedFacets
        $compute_range_filters = false;
        $filterBlock = $this->getFilterBlock(
            array(),
            $compute_range_filters
        );

        $queryTemplate = $this->filtersConverter->getFacetsFromFacetedSearchFilters(
            $filterBlock['filters']
        );

        $facets = $this->facetsSerializer->setFiltersFromEncodedFacets(
            $queryTemplate,
            $query->getEncodedFacets()
        );

        return (new FacetCollection())->setFacets($facets);
    }

    private function copyFiltersActiveState(
        array $sourceFacets,
        array $targetFacets
    ) {
        $copyByLabel = function (Facet $source, Facet $target) {
            foreach ($target->getFilters() as $targetFilter) {
                foreach ($source->getFilters() as $sourceFilter) {
                    if ($sourceFilter->getLabel() === $targetFilter->getLabel()) {
                        $targetFilter->setActive($sourceFilter->isActive());
                        break;
                    }
                }
            }
        };

        $copyByRangeValue = function (Facet $source, Facet $target) {
            foreach ($source->getFilters() as $sourceFilter) {
                if ($sourceFilter->isActive()) {
                    $foundRange = false;
                    foreach ($target->getFilters() as $targetFilter) {
                        $tFrom = $targetFilter->getValue()['from'];
                        $tTo = $targetFilter->getValue()['to'];
                        $sFrom = $sourceFilter->getValue()['from'];
                        $sTo = $sourceFilter->getValue()['to'];
                        if ($tFrom <= $sFrom && $sTo <= $tTo) {
                            $foundRange = true;
                            $targetFilter->setActive(true);
                            break;
                        }
                    }
                    if (!$foundRange) {
                        $filter = clone $sourceFilter;
                        $filter->setDisplayed(false);
                        $target->addFilter($filter);
                    }
                    break;
                }
            }
        };

        $copy = function (
            Facet $source,
            Facet $target
        ) use (
            $copyByLabel,
            $copyByRangeValue
        ) {
            if ($target->getProperty('range')) {
                $strategy = $copyByRangeValue;
            } else {
                $strategy = $copyByLabel;
            }

            $strategy($source, $target);
        };

        foreach ($targetFacets as $targetFacet) {
            foreach ($sourceFacets as $sourceFacet) {
                if ($sourceFacet->getLabel() === $targetFacet->getLabel()) {
                    $copy($sourceFacet, $targetFacet);
                    break;
                }
            }
        }
    }

    public function getAvailableSortOrders()
    {
        return array(
            (new SortOrder('product', 'name', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Name, A to Z', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'name', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Name, Z to A', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'price', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Price, low to high', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'price', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Price, high to low', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'quantity', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Quantity, newest to oldest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'quantity', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Quantity, oldest to newest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'reference', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Reference, newest to oldest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'reference', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Reference, oldest to newest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'date_add', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Date added, newest to oldest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'date_add', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Date added, oldest to newest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'date_upd', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Date modified, newest to oldest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'date_upd', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Date modified, oldest to newest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'position', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Relevance asc', array(), 'Modules.FacetedSearch.Shop')
            ),
            (new SortOrder('product', 'position', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Relevance desc', array(), 'Modules.FacetedSearch.Shop')
            ),
            (new SortOrder('product', 'manufacturer_name', 'desc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Manufacturer, newest to oldest', array(), 'Shop.Theme.Catalog')
            ),
            (new SortOrder('product', 'manufacturer_name', 'asc'))->setLabel(
                Context::getContext()->getTranslator()->trans('Manufacturer, oldest to newest', array(), 'Shop.Theme.Catalog')
            ),
        );
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = array();
        $count = 0;

        $productSearchResult = new ProductSearchResult();

        if (($string = $query->getSearchString())) {
            $queryString = Tools::replaceAccentedChars(urldecode($string));

            $result = $this->find(
                $context->getIdLang(),
                $queryString,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(),
                $query->getSortOrder()->toLegacyOrderWay(),
                false, // ajax, what's the link?
                false, // $use_cookie, ignored anyway
                null
            );
            $products = $result['result'];
            $count = $result['total'];

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ));

            if (!empty($products)) {
                $productSearchResult
                    ->setProducts($products)
                    ->setTotalProductsCount($count);

                $productSearchResult->setAvailableSortOrders(
                    $this->getAvailableSortOrders()
                );
            }
            return $productSearchResult;
        } elseif (($tag = $query->getSearchTag())) {
            $queryString = urldecode($tag);

            $products = $this->searchTag(
                $context->getIdLang(),
                $queryString,
                false,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );

            $count = $this->searchTag(
                $context->getIdLang(),
                $queryString,
                true,
                $query->getPage(),
                $query->getResultsPerPage(),
                $query->getSortOrder()->toLegacyOrderBy(true),
                $query->getSortOrder()->toLegacyOrderWay(),
                false,
                null
            );

            Hook::exec('actionSearch', array(
                'searched_query' => $queryString,
                'total' => $count,

                // deprecated since 1.7.x
                'expr' => $queryString,
            ));

            if (!empty($products)) {
                $productSearchResult
                    ->setProducts($products)
                    ->setTotalProductsCount($count);

                $productSearchResult->setAvailableSortOrders(
                    $this->sortOrderFactory->getDefaultSortOrders()
                );
            }
            return $productSearchResult;
        }

        $menu = $this->getFacetCollectionFromEncodedFacets($query);

        $order_by = $query->getSortOrder()->toLegacyOrderBy(true);
        $order_way = $query->getSortOrder()->toLegacyOrderWay();

        $facetedSearchFilters = $this->filtersConverter->getFacetedSearchFiltersFromFacets(
            $menu->getFacets()
        );

        $productsAndCount = $this->getProductByFilters(
            $query->getResultsPerPage(),
            $query->getPage(),
            $order_by,
            $order_way,
            $context->getIdLang(),
            $facetedSearchFilters,
            $query->id_products
        );

        $productSearchResult
            ->setProducts($productsAndCount['products'])
            ->setTotalProductsCount($productsAndCount['count'])
            ->setAvailableSortOrders($this->getAvailableSortOrders());

        $filterBlock = $this->getFilterBlock($facetedSearchFilters);
        $facets = $this->filtersConverter->getFacetsFromFacetedSearchFilters(
            $filterBlock['filters']
        );

        $this->copyFiltersActiveState(
            $menu->getFacets(),
            $facets
        );

        $this->labelRangeFilters($facets);

        $this->addEncodedFacetsToFilters($facets);

        $this->hideZeroValues($facets);
        $this->hideUselessFacets($facets);

        $nextMenu = (new FacetCollection())->setFacets($facets);
        $productSearchResult->setFacetCollection($nextMenu);
        $productSearchResult->setEncodedFacets($this->facetsSerializer->serialize($facets));

        return $productSearchResult;
    }

    private function labelRangeFilters(array $facets)
    {
        foreach ($facets as $facet) {
            if ($facet->getType() === 'weight') {
                $unit = Configuration::get('PS_WEIGHT_UNIT');
                foreach ($facet->getFilters() as $filter) {
                    $filter->setLabel(
                        sprintf(
                            '%1$s%2$s - %3$s%4$s',
                            Tools::displayNumber($filter->getValue()['from']),
                            $unit,
                            Tools::displayNumber($filter->getValue()['to']),
                            $unit
                        )
                    );
                }
            } elseif ($facet->getType() === 'price') {
                foreach ($facet->getFilters() as $filter) {
                    $filter->setLabel(
                        sprintf(
                            '%1$s - %2$s',
                            Tools::displayPrice($filter->getValue()['from']),
                            Tools::displayPrice($filter->getValue()['to'])
                        )
                    );
                }
            }
        }
    }

    /**
     * This method generates a URL stub for each filter inside the given facets
     * and assigns this stub to the filters.
     * The URL stub is called 'nextEncodedFacets' because it is used
     * to generate the URL of the search once a filter is activated.
     */
    private function addEncodedFacetsToFilters(array $facets)
    {
        // first get the currently active facetFilter in an array
        $activeFacetFilters = $this->facetsSerializer->getActiveFacetFiltersFromFacets($facets);
        $urlSerializer = new URLFragmentSerializer();

        foreach ($facets as $facet) {
            // If only one filter can be selected, we keep track of
            // the current active filter to disable it before generating the url stub
            // and not select two filters in a facet that can have only one active filter.
            if (!$facet->isMultipleSelectionAllowed()) {
                foreach ($facet->getFilters() as $filter) {
                    if ($filter->isActive()) {
                        // we have a currently active filter is the facet, remove it from the facetFilter array
                        $activeFacetFilters = $this->facetsSerializer->removeFilterFromFacetFilters(
                            $activeFacetFilters,
                            $filter,
                            $facet
                        );
                        break;
                    }
                }
            }

            foreach ($facet->getFilters() as $filter) {
                $facetFilters = $activeFacetFilters;

                // toggle the current filter
                if ($filter->isActive()) {
                    $facetFilters = $this->facetsSerializer->removeFilterFromFacetFilters(
                        $facetFilters,
                        $filter,
                        $facet
                    );
                } else {
                    $facetFilters = $this->facetsSerializer->addFilterToFacetFilters($facetFilters, $filter, $facet);
                }

                // We've toggled the filter, so the call to serialize
                // returns the "URL" for the search when user has toggled
                // the filter.
                $filter->setNextEncodedFacets(
                    $urlSerializer->serialize($facetFilters)
                );
            }
        }
    }

    private function hideZeroValues(array $facets)
    {
        foreach ($facets as $facet) {
            foreach ($facet->getFilters() as $filter) {
                if ($filter->getMagnitude() === 0) {
                    $filter->setDisplayed(false);
                }
            }
        }
    }

    private function hideUselessFacets(array $facets)
    {
        foreach ($facets as $facet) {
            $usefulFiltersCount = 0;
            foreach ($facet->getFilters() as $filter) {
                if ($filter->getMagnitude() > 0) {
                    ++$usefulFiltersCount;
                }
            }
            $facet->setDisplayed(
                $usefulFiltersCount > 1
            );
        }
    }

//    Funtion is realy rename because not run same expected. Fucntion has been replace by function getProductByFilters().
    public function getProductByFiltersOld(
        $products_per_page,
        $page,
        $order_by,
        $order_way,
        $id_lang,
        $selected_filters = array(),
        $product_ids = array()
    ) {
        $products_per_page = (int)$products_per_page;

        if (!Validate::isOrderBy($order_by)) {
            $order_by = 'cp.position';
        }

        if (!Validate::isOrderWay($order_way)) {
            $order_way = 'ASC';
        }

        $order_clause = $order_by . ' ' . $order_way;

        $home_category = Configuration::get('PS_HOME_CATEGORY');
        /* If the current category isn't defined or if it's homepage, we have nothing to display */
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));

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
                            ' AND p.id_product IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'feature_product` fp WHERE ';
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
                        'AND p.id_product IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product cp WHERE';
                    foreach ($selected_filters['category'] as $id_category) {
                        $query_filters_where .= 'cp.`id_category` = ' . (int)$id_category . ' OR ';
                    }
                    $query_filters_where = rtrim($query_filters_where, 'OR ') . ')';
                    break;

                case 'quantity':
                    if (count($selected_filters['quantity']) == 2) {
                        break;
                    }

                    $query_filters_where .= ' AND sa.quantity ' . (!$selected_filters['quantity'][0] ? '<=' : '>') . ' 0 ';
                    $query_filters_from .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                    ON (sa.id_product = p.id_product ' . StockAvailable::addSqlShopRestriction(null, null, 'sa') . ')';
                    break;

                case 'manufacturer':
                    $selected_filters['manufacturer'] = array_map('intval', $selected_filters['manufacturer']);
                    $query_filters_where .= '
                    AND p.id_manufacturer
                    IN (' . implode($selected_filters['manufacturer'], ',') . ')';
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
                        $query_filters_where .= '
                        AND p.`weight`
                        BETWEEN ' . (float)($selected_filters['weight'][0] - 0.001) . '
                        AND ' . (float)($selected_filters['weight'][1] + 0.001);
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
                ((psi.price_min < ' . (int)$price_filter['min'] . ' AND psi.price_max > ' . (int)$price_filter['min'] . ')
                OR
                (psi.price_max > ' . (int)$price_filter['max'] . ' AND psi.price_min < ' . (int)$price_filter['max'] . '))
                AND psi.`id_product` = p.`id_product`
                AND psi.`id_shop` = ' . (int)$context->shop->id . '
                AND psi.`id_currency` = ' . $id_currency;
        }

        $query_filters_from .= Shop::addSqlAssociation('product', 'p');

        Db::getInstance()->execute('DROP TEMPORARY TABLE IF EXISTS ' . _DB_PREFIX_ . 'cat_filter_restriction', false);
        if (!empty($product_ids)) {
            /* Create the table which contains all the id_product in a cat or a tree */
            Db::getInstance()->execute('CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
            STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category
            AND c.active = 1)
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
            ' . $price_filter_query_in . '
            ' . $query_filters_from . '
            WHERE 1 ' . $query_filters_where . (empty($product_ids) ?
                    '' : ' AND p.`id_product`  IN (' . implode(',', $product_ids) . ')') . '
            GROUP BY cp.id_product ORDER BY position, id_product', false);
        } elseif (empty($selected_filters['category'])) {
            /* Create the table which contains all the id_product in a cat or a tree */
            Db::getInstance()->execute('CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
            STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND
            ' . ($this->ps_layered_full_tree ? 'c.nleft >= ' . (int)$parent->nleft . '
            AND c.nright <= ' . (int)$parent->nright : 'c.id_category = ' . (int)$id_parent) . '
            AND c.active = 1)
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
            ' . $price_filter_query_in . '
            ' . $query_filters_from . '
            WHERE 1 ' . $query_filters_where . '
            GROUP BY cp.id_product ORDER BY position, id_product', false);
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
            'ALTER TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction
            ADD PRIMARY KEY (id_product),
            ADD KEY (position,
            id_product) USING BTREE',
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

            if (empty($selected_filters['category'])) {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT p.`id_product` id_product
                    FROM `' . _DB_PREFIX_ . 'product` p JOIN ' . _DB_PREFIX_ . 'category_product cp USING (id_product)
                    INNER JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category AND
                        ' . ($this->ps_layered_full_tree ? 'c.nleft >= ' . (int)$parent->nleft . '
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
                    'DELETE FROM ' . _DB_PREFIX_ . 'cat_filter_restriction
                    WHERE id_product IN (' . implode(',', $product_id_delete_list) . ')',
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

//        $this->nbr_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM ' . $catFilterRestrictionDerivedTable . ' ps');


        if ($this->nbr_products == 0) {
            $products = array();
        } else {
            $nb_day_new_product = (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20);

            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                $products = Db::getInstance()->executeS('
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
                        IFNULL(stock.quantity, 0) as quantity' . (Combination::isFeatureActive() ?
                        ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . '
                    FROM ' . _DB_PREFIX_ . 'cat_filter_restriction cp
                    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                    ' . Shop::addSqlAssociation('product', 'p') .
                    (Combination::isFeatureActive() ?
                        ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                        ON (p.`id_product` = product_attribute_shop.`id_product`
                        AND product_attribute_shop.`default_on` = 1
                        AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')' : '') . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl
                    ON (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl') . '
                    AND pl.id_lang = ' . (int)$id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                        ON (image_shop.`id_product` = p.`id_product`
                        AND image_shop.cover=1
                        AND image_shop.id_shop=' . (int)$context->shop->id . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                    ON (image_shop.`id_image` = il.`id_image`
                    AND il.`id_lang` = ' . (int)$id_lang . ')
                    LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
                    LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`)
                    ' . Product::sqlStock('p', 0) . '
                    WHERE (jm.`not_visible` =' . $this->SHOW_ON_MOBILE . '
                    OR jm.`not_visible` is null)
                    AND ' . $alias_where . '.`active` = 1 AND ' . $alias_where . '.`visibility` IN ("both", "catalog")
                    ' . (empty($hidden_pack_product_ids) ? '' : '
                    AND p.`id_product` NOT IN (' . implode(',', $hidden_pack_product_ids) . ')') . ' ' . (empty($product_ids) ?
                        '' : 'AND p.`id_product`  IN (' . implode(',', $product_ids) . ')') . '
				    ORDER BY ' . $order_clause . ' , cp.id_product' .
                    ' LIMIT ' . (((int)$page - 1) * $products_per_page . ',' . $products_per_page), true, false);
            } else {
                $products = Db::getInstance()->executeS('
                    SELECT
                        p.*,
                        ' . ($alias_where == 'p' ? '' : 'product_shop.*,') . '
                        ' . $alias_where . '.id_category_default,
                        pl.*,
                        MAX(image_shop.`id_image`) id_image,
                        il.legend,
                        m.name manufacturer_name,
                        ' . (Combination::isFeatureActive() ?
                        'MAX(product_attribute_shop.id_product_attribute) id_product_attribute,' : '') . '
                        DATEDIFF(' . $alias_where . '.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00",
                        INTERVAL ' . (int)$nb_day_new_product . ' DAY)) > 0 AS new,
                        stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity' . (Combination::isFeatureActive() ?
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
                    AND pl.id_lang = ' . (int)$id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image` i  ON (i.`id_product` = p.`id_product`)' .
                    Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
                    ON (image_shop.`id_image` = il.`id_image`
                    AND il.`id_lang` = ' . (int)$id_lang . ')
                    LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
                    ' . Product::sqlStock('p', 0) . '
                    WHERE ' . $alias_where . '.`active` = 1 AND ' . $alias_where . '.`visibility` IN ("both", "catalog")
                    ' . (empty($hidden_pack_product_ids) ? '' : '
                    AND p.`id_product` NOT IN (' . implode(',', $hidden_pack_product_ids) . ')') . '
                    GROUP BY product_shop.id_product
                    ORDER BY ' . $order_clause . ' , cp.id_product' .
                    ' LIMIT ' . (((int)$page - 1) * $products_per_page . ',' . $products_per_page), true, false);
            }
        }

        if ($order_by == 'p.price') {
            Tools::orderbyPrice($products, $order_way);
        }

        return array(
            'products' => $products,
            'count' => $this->nbr_products,
        );
    }

//     Funtion replace for getProductByFiltersOld();
    public function getProductByFilters(
        $products_per_page,
        $page,
        $order_by,
        $order_way,
        $id_lang,
        $selected_filters = array(),
        $product_ids = array()
    ) {
        $products_per_page = (int)$products_per_page;

        if (!Validate::isOrderBy($order_by)) {
            $order_by = 'cp.position';
        }

        if (!Validate::isOrderWay($order_way)) {
            $order_way = 'ASC';
        }

        $order_clause = $order_by . ' ' . $order_way;

        $home_category = Configuration::get('PS_HOME_CATEGORY');
        /* If the current category isn't defined or if it's homepage, we have nothing to display */
        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', $home_category));

        $alias_where = 'p';
        if (version_compare(_PS_VERSION_, '1.5', '>')) {
            $alias_where = 'product_shop';
        }

        $query_filters_where = ' AND ' . $alias_where . '.`active` = 1 AND ' . $alias_where . '.`visibility` IN ("both", "catalog")';
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
                        $sub_queries[$filter_value_array[0]][] = 'fp.`id_feature_value` = ' . (int)$filter_value_array[1];
                    }
                    foreach ($sub_queries as $sub_query) {
                        $query_filters_where .= ' AND p.id_product IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'feature_product` fp WHERE ';
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
                    $query_filters_where .= ' AND p.id_product IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'category_product cp WHERE ';
                    foreach ($selected_filters['category'] as $id_category) {
                        $query_filters_where .= 'cp.`id_category` = ' . (int)$id_category . ' OR ';
                    }
                    $query_filters_where = rtrim($query_filters_where, 'OR ') . ')';
                    break;

                case 'quantity':
                    if (count($selected_filters['quantity']) == 2) {
                        break;
                    }

                    $query_filters_where .= ' AND sa.quantity ' . (!$selected_filters['quantity'][0] ? '<=' : '>') . ' 0 ';
                    $query_filters_from .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa ON (sa.id_product = p.id_product ' . StockAvailable::addSqlShopRestriction(null, null, 'sa') . ') ';
                    break;

                case 'manufacturer':
                    $selected_filters['manufacturer'] = array_map('intval', $selected_filters['manufacturer']);
                    $query_filters_where .= ' AND p.id_manufacturer IN (' . implode($selected_filters['manufacturer'], ',') . ')';
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
                        $query_filters_where .= ' AND p.`weight` BETWEEN ' . (float)($selected_filters['weight'][0] - 0.001) . ' AND ' . (float)($selected_filters['weight'][1] + 0.001);
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
                ((psi.price_min < ' . (int)$price_filter['min'] . ' AND psi.price_max > ' . (int)$price_filter['min'] . ')
                OR
                (psi.price_max > ' . (int)$price_filter['max'] . ' AND psi.price_min < ' . (int)$price_filter['max'] . '))
                AND psi.`id_product` = p.`id_product`
                AND psi.`id_shop` = ' . (int)$context->shop->id . '
                AND psi.`id_currency` = ' . $id_currency;
        }

        $query_filters_from .= Shop::addSqlAssociation('product', 'p');
        $extraWhereQuery = '';

        if (!empty($selected_filters['category'])) {
            $categories = array_map('intval', $selected_filters['category']);
        }

        if (isset($price_filter) && $price_filter) {
            static $ps_layered_filter_price_usetax = null;
            static $ps_layered_filter_price_rounding = null;

            if ($ps_layered_filter_price_usetax === null) {
                $ps_layered_filter_price_usetax = Configuration::get('PS_LAYERED_FILTER_PRICE_USETAX');
            }

            if ($ps_layered_filter_price_rounding === null) {
                $ps_layered_filter_price_rounding = Configuration::get('PS_LAYERED_FILTER_PRICE_ROUNDING');
            }

            if (empty($selected_filters['category'])) {
                $all_products_out = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT p.`id_product` id_product
                    FROM `' . _DB_PREFIX_ . 'product` p JOIN ' . _DB_PREFIX_ . 'category_product cp USING (id_product)
                    INNER JOIN ' . _DB_PREFIX_ . 'category c ON (c.id_category = cp.id_category AND
                        ' . ($this->ps_layered_full_tree ? 'c.nleft >= ' . (int)$parent->nleft . '
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
                    WHERE cp.`id_category` IN (' . implode(',', $categories) . ') ' . $query_filters_where . ' GROUP BY cp.id_product');
            }

            /* for this case, price could be out of range, so we need to compute the real price */
            foreach ($all_products_out as $product) {
                $price = Product::getPriceStatic($product['id_product'], $ps_layered_filter_price_usetax);
                if ($ps_layered_filter_price_rounding) {
                    $price = (int)$price;
                }
                if ($price < $price_filter['min'] || $price > $price_filter['max']) {
                    // out of range price, exclude the product
                    $product_id_delete_list = array();
                    $product_id_delete_list[] = (int)$product['id_product'];
                }
            }
            if (!empty($product_id_delete_list)) {
                $extraWhereQuery = ' AND p.id_product NOT IN (' . implode(',', $product_id_delete_list) . ') ';
            }
        }
        if (!empty($product_ids)) {
            /* Create the table which contains all the id_product in a cat or a tree */
            Db::getInstance()->execute('CREATE TEMPORARY TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction ENGINE=MEMORY
            SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
            STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category
            AND c.active = 1)
            STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
            ' . $price_filter_query_in . '
            ' . $query_filters_from . '
            WHERE 1 ' . $query_filters_where . (empty($product_ids) ?
                    '' : ' AND p.`id_product`  IN (' . implode(',', $product_ids) . ')') . '
            GROUP BY cp.id_product ORDER BY position, id_product', false);

            Db::getInstance()->execute(
                'ALTER TABLE ' . _DB_PREFIX_ . 'cat_filter_restriction
            ADD PRIMARY KEY (id_product),
            ADD KEY (position,
            id_product) USING BTREE',
                false
            );

            $this->nbr_products = Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'cat_filter_restriction',
                false
            );

            $catFilterRestrictionDerivedTable = _DB_PREFIX_ . 'cat_filter_restriction';
        } else {
            if (empty($selected_filters['category'])) {
                $catFilterRestrictionDerivedTable = ' ((SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
                                                         STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND
                                                         c.id_category = ' . (int)$id_parent . '
                                                         AND c.active = 1)
                                                         STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
                                                         ' . $price_filter_query_in . '
                                                         ' . $query_filters_from . '
                                                         WHERE 1 ' . $query_filters_where . $extraWhereQuery . '
                                                         GROUP BY cp.id_product)';
                if ($this->ps_layered_full_tree) {
                    // add other products in subcategories, but not present in the main cat!
                    $catFilterRestrictionDerivedTable .= ' UNION ALL (SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category c
                                                         STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND
                                                         c.id_category != ' . (int)$id_parent . '
                                                         AND c.nleft >= ' . (int)$parent->nleft . '
                                                         AND c.nright <= ' . (int)$parent->nright . '
                                                         AND c.active = 1)
                                                         STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
                                                         ' . $price_filter_query_in . '
                                                         ' . $query_filters_from . '
                                                         WHERE NOT EXISTS(SELECT * FROM ' . _DB_PREFIX_ . 'category_product cpe 
                                                                            WHERE cp.id_product=cpe.id_product AND cpe.id_category = ' . (int)$id_parent . ')
                                                         ' . $query_filters_where . $extraWhereQuery . '
                                                         GROUP BY cp.id_product)';
                }
                $catFilterRestrictionDerivedTable .= ')';
            } else {
                $catFilterRestrictionDerivedTable = ' (SELECT cp.id_product, MIN(cp.position) position FROM ' . _DB_PREFIX_ . 'category_product cp
                                                         STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.id_product=cp.id_product)
                                                         ' . $price_filter_query_in . '
                                                         ' . $query_filters_from . '
                                                         WHERE cp.`id_category` IN (' . implode(',', $categories) . ') ' . $query_filters_where . $extraWhereQuery . '
                                                         GROUP BY cp.id_product)';
            }

            $this->nbr_products = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT COUNT(*) FROM ' . $catFilterRestrictionDerivedTable . ' ps');
        }

        if ($this->nbr_products == 0) {
            $products = array();
        } else {
            $nb_day_new_product = (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20);
            if (version_compare(_PS_VERSION_, '1.6.1', '>=') === true) {
                $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT
                        p.*,
                        ' . ($alias_where == 'p' ? '' : 'product_shop.*,') . '
                        ' . $alias_where . '.id_category_default,
                        pl.*,
                        image_shop.`id_image` id_image,
                        il.legend,
                        m.name manufacturer_name,
                        ' . (Combination::isFeatureActive() ? 'product_attribute_shop.id_product_attribute id_product_attribute,' : '') . '
                        DATEDIFF(' . $alias_where . '.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00", INTERVAL ' . (int)$nb_day_new_product . ' DAY)) > 0 AS new,
                        stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity' . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '') . '
                    FROM ' . $catFilterRestrictionDerivedTable . ' cp
                    
                    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                    ' . Shop::addSqlAssociation('product', 'p') .
                    (Combination::isFeatureActive() ?
                        ' LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
                        ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')' : '') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`)
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl') . ' AND pl.id_lang = ' . (int)$id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                        ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop=' . (int)$context->shop->id . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')
                    LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
                    ' . Product::sqlStock('p', 0) . '
                    WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null) AND ' . $alias_where . '.`active` = 1 AND ' . $alias_where . '.`visibility` IN ("both", "catalog")
                    ' . (empty($product_ids) ?
                        '' : 'AND p.`id_product`  IN (' . implode(',', $product_ids) . ')') . '
				    ORDER BY ' . $order_clause . ' , cp.id_product' .
                    ' LIMIT ' . (((int)$page - 1) * $products_per_page . ',' . $products_per_page));
            } else {
                $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT
                        p.*,
                        ' . ($alias_where == 'p' ? '' : 'product_shop.*,') . '
                        ' . $alias_where . '.id_category_default,
                        pl.*,
                        MAX(image_shop.`id_image`) id_image,
                        il.legend,
                        m.name manufacturer_name,
                        ' . (Combination::isFeatureActive() ? 'MAX(product_attribute_shop.id_product_attribute) id_product_attribute,' : '') . '
                        DATEDIFF(' . $alias_where . '.`date_add`, DATE_SUB("' . date('Y-m-d') . ' 00:00:00", INTERVAL ' . (int)$nb_day_new_product . ' DAY)) > 0 AS new,
                        stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity' . (Combination::isFeatureActive() ? ', MAX(product_attribute_shop.minimal_quantity) AS product_attribute_minimal_quantity' : '') . '
                    FROM ' . $catFilterRestrictionDerivedTable . ' cp
                    LEFT JOIN `' . _DB_PREFIX_ . 'product` p ON p.`id_product` = cp.`id_product`
                    ' . Shop::addSqlAssociation('product', 'p') .
                    (Combination::isFeatureActive() ?
                        'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute` pa ON (p.`id_product` = pa.`id_product`)
                    ' . Shop::addSqlAssociation('product_attribute', 'pa', false, 'product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop=' . (int)$context->shop->id) : '') . '
                    LEFT JOIN ' . _DB_PREFIX_ . 'product_lang pl ON (pl.id_product = p.id_product' . Shop::addSqlRestrictionOnLang('pl') . ' AND pl.id_lang = ' . (int)$id_lang . ')
                    LEFT JOIN `' . _DB_PREFIX_ . 'image` i  ON (i.`id_product` = p.`id_product`)' .
                    Shop::addSqlAssociation('image', 'i', false, 'image_shop.cover=1') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int)$id_lang . ')
                    LEFT JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer)
                    ' . Product::sqlStock('p', 0) . '
                    WHERE ' . $alias_where . '.`active` = 1 AND ' . $alias_where . '.`visibility` IN ("both", "catalog")
                    GROUP BY product_shop.id_product
                    ORDER BY ' . $order_clause . ' , cp.id_product' .
                    ' LIMIT ' . (((int)$page - 1) * $products_per_page . ',' . $products_per_page));
            }
        }

        if ($order_by == 'p.price') {
            Tools::orderbyPrice($products, $order_way);
        }

        if ($products == null) {
            $products = array();
        }

        return array(
            'products' => $products,
            'count' => $this->nbr_products,
        );
    }

    public function getFilterBlock(
        $selected_filters = array(),
        $compute_range_filters = true
    ) {
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
                    if ($value == '' || $value == array()) {
                        unset($selected_filters[$key]);
                    }
                    break;
            }
        }

        static $latest_selected_filters = null;
        static $productCache = array();
        $context = Context::getContext();

        $id_lang = $context->language->id;
        $currency = $context->currency;
        $id_shop = (int)$context->shop->id;
        $alias = 'product_shop';

        $id_parent = (int)Tools::getValue('id_category', Tools::getValue('id_category_layered', Configuration::get('PS_HOME_CATEGORY')));

        $parent = new Category((int)$id_parent, $id_lang);

        /* Get the filters for the current category */
        $filters = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS(
            'SELECT type, id_value, filter_show_limit, filter_type FROM ' . _DB_PREFIX_ . 'layered_category
			WHERE id_category = ' . (int)$id_parent . '
				AND id_shop = ' . $id_shop . '
			GROUP BY `type`, id_value ORDER BY position ASC'
        );

        $catRestrictionDerivedTable = '(SELECT DISTINCT cp.id_product, p.id_manufacturer, product_shop.condition, p.weight FROM ' . _DB_PREFIX_ . 'category c
                                             STRAIGHT_JOIN ' . _DB_PREFIX_ . 'category_product cp ON (c.id_category = cp.id_category AND
                                             ' . ($this->ps_layered_full_tree ? 'c.nleft >= ' . (int)$parent->nleft . '
                                             AND c.nright <= ' . (int)$parent->nright : 'c.id_category = ' . (int)$id_parent) . '
                                             AND c.active = 1)
                                             STRAIGHT_JOIN ' . _DB_PREFIX_ . 'product_shop product_shop ON (product_shop.id_product = cp.id_product
                                             AND product_shop.id_shop = ' . (int)$context->shop->id . ')
                                             STRAIGHT_JOIN ' . _DB_PREFIX_ . 'product p ON (p.id_product=cp.id_product)
                                             WHERE product_shop.`active` = 1 AND product_shop.`visibility` IN ("both", "catalog"))';

        $filter_blocks = array();
        foreach ($filters as $filter) {
            $cacheKey = $filter['type'] . '-' . $filter['id_value'];
            if ($latest_selected_filters == $selected_filters && isset($productCache[$cacheKey])) {
                $products = $productCache[$cacheKey];
            } else {
                $sql_query = array('select' => '', 'from' => '', 'join' => '', 'where' => '', 'group' => '');
                switch ($filter['type']) {
                    case 'price':
                        $sql_query['select'] = 'SELECT p.`id_product`, psi.price_min, psi.price_max ';
                        // price slider is not filter dependent
                        $sql_query['from'] = '
                        FROM ' . $catRestrictionDerivedTable . ' p';
                        $sql_query['join'] = 'INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi
                                    ON (psi.id_product = p.id_product AND psi.id_currency = ' . (int)$context->currency->id . ' AND psi.id_shop=' . (int)$context->shop->id . ')' . 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`)';
                        $sql_query['where'] = 'WHERE' . ' (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        break;
                    case 'weight':
                        $sql_query['select'] = 'SELECT p.`id_product`, p.`weight` ';
                        // price slider is not filter dependent
                        $sql_query['from'] = '
                        FROM ' . $catRestrictionDerivedTable . ' p';
                        $sql_query['join'] = 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`) ';
                        $sql_query['where'] = 'WHERE' . '(jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        break;
                    case 'condition':
                        $sql_query['select'] = 'SELECT DISTINCT p.`id_product`, product_shop.`condition` ';
                        $sql_query['from'] = '
                        FROM ' . $catRestrictionDerivedTable . ' p';
                        $sql_query['join'] = 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`) ';
                        $sql_query['where'] = 'WHERE' . '(jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        $sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
                        break;
                    case 'quantity':
                        $sql_query['select'] = 'SELECT DISTINCT p.`id_product`, sa.`quantity`, sa.`out_of_stock` ';

                        $sql_query['from'] = '
                        FROM ' . $catRestrictionDerivedTable . ' p';

                        $sql_query['join'] .= 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sa
                            ON (sa.id_product = p.id_product AND sa.id_product_attribute=0 ' . StockAvailable::addSqlShopRestriction(null, null, 'sa') . ') ' . 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`) ';
                        $sql_query['where'] = 'WHERE' . '(jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        break;

                    case 'manufacturer':
                        $sql_query['select'] = 'SELECT COUNT(DISTINCT p.id_product) nbr, m.id_manufacturer, m.name ';
                        $sql_query['from'] = '
                        FROM ' . $catRestrictionDerivedTable . ' p
                        INNER JOIN ' . _DB_PREFIX_ . 'manufacturer m ON (m.id_manufacturer = p.id_manufacturer) ' . 'LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                                    ON (jm.`id_product`= p.`id_product`) ';
                        $sql_query['where'] = 'WHERE' . '(jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        $sql_query['group'] = ' GROUP BY p.id_manufacturer ORDER BY m.name';
                        break;
                    case 'id_attribute_group':// attribute group
                        $sql_query['select'] = '
                        SELECT COUNT(DISTINCT lpa.id_product) nbr, lpa.id_attribute_group,
                        a.color, al.name attribute_name, agl.public_name attribute_group_name , lpa.id_attribute, ag.is_color_group,
                        liagl.url_name name_url_name, liagl.meta_title name_meta_title, lial.url_name value_url_name, lial.meta_title value_meta_title';
                        $sql_query['from'] = '
                        FROM ' . _DB_PREFIX_ . 'layered_product_attribute lpa
                        INNER JOIN ' . _DB_PREFIX_ . 'attribute a
                        ON a.id_attribute = lpa.id_attribute
                        INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang al
                        ON al.id_attribute = a.id_attribute
                        AND al.id_lang = ' . (int)$id_lang . '
                        INNER JOIN ' . $catRestrictionDerivedTable . ' p
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
                                    ON (jm.`id_product`= p.`id_product`) ';

                        $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null) AND lpa.id_attribute_group = ' . (int)$filter['id_value'];
                        $sql_query['where'] .= ' AND lpa.`id_shop` = ' . (int)$context->shop->id;

                        $sql_query['group'] = '
                        GROUP BY lpa.id_attribute
                        ORDER BY ag.`position` ASC, a.`position` ASC';
//                        $sql_query['join'] = 'LEFT JOIN `'._DB_PREFIX_.'jm_product_visibility` jm
//                                    ON (jm.`id_product`= p.`id_product`)';
//                        $sql_query['where'] = 'WHERE (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        break;

                    case 'id_feature':
                        $id_lang = (int)$id_lang;
                        $sql_query['select'] = 'SELECT fl.name feature_name, fp.id_feature, fv.id_feature_value, fvl.value,
                        COUNT(DISTINCT p.id_product) nbr,
                        lifl.url_name name_url_name, lifl.meta_title name_meta_title, lifvl.url_name value_url_name, lifvl.meta_title value_meta_title ';
                        $sql_query['from'] = '
                        FROM ' . _DB_PREFIX_ . 'feature_product fp
                        INNER JOIN ' . $catRestrictionDerivedTable . ' p
                        ON p.id_product = fp.id_product
                        LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = fp.id_feature AND fl.id_lang = ' . $id_lang . ')
                        INNER JOIN ' . _DB_PREFIX_ . 'feature_value fv ON (fv.id_feature_value = fp.id_feature_value AND (fv.custom IS NULL OR fv.custom = 0))
                        LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = fp.id_feature_value AND fvl.id_lang = ' . $id_lang . ')
                        LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_feature_lang_value lifl
                        ON (lifl.id_feature = fp.id_feature AND lifl.id_lang = ' . $id_lang . ')
                        LEFT JOIN ' . _DB_PREFIX_ . 'layered_indexable_feature_value_lang_value lifvl
                        ON (lifvl.id_feature_value = fp.id_feature_value AND lifvl.id_lang = ' . $id_lang . ') ' . '
                        LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm
                        ON (jm.`id_product`= p.`id_product`) ';
                        $sql_query['where'] = 'WHERE fp.id_feature = ' . (int)$filter['id_value'] .
                            ' AND (jm.`not_visible` = 1 OR jm.`not_visible` is null)';
                        $sql_query['group'] = 'GROUP BY fv.id_feature_value ';
                        break;

                    case 'category':
                        if (Group::isFeatureActive()) {
                            $this->user_groups = ($this->context->customer->isLogged() ? $this->context->customer->getGroups() : array(
                                Configuration::get(
                                    'PS_UNIDENTIFIED_GROUP'
                                )
                            ));
                        }

                        $depth = Configuration::get('PS_LAYERED_FILTER_CATEGORY_DEPTH');
                        if ($depth === false) {
                            $depth = 1;
                        }

                        $sql_query['select'] = '
                        SELECT c.id_category, c.id_parent, cl.name, (SELECT count(DISTINCT p.id_product) # ';
                        $sql_query['from'] = '
                        FROM ' . _DB_PREFIX_ . 'category_product cp
                        LEFT JOIN ' . _DB_PREFIX_ . 'product p ON (p.id_product = cp.id_product) ';
                        $sql_query['where'] = '
                        WHERE cp.id_category = c.id_category
                        AND ' . $alias . '.active = 1 AND ' . $alias . '.`visibility` IN ("both", "catalog")';
                        $sql_query['group'] = ') count_products
                        FROM ' . _DB_PREFIX_ . 'category c
                        LEFT JOIN ' . _DB_PREFIX_ . 'category_lang cl ON (cl.id_category = c.id_category AND cl.`id_shop` = ' . (int)Context::getContext()->shop->id . ' and cl.id_lang = ' . (int)$id_lang . ') ';

                        if (Group::isFeatureActive()) {
                            $sql_query['group'] .= 'RIGHT JOIN ' . _DB_PREFIX_ . 'category_group cg ON (cg.id_category = c.id_category AND cg.`id_group` IN (' . implode(', ', $this->user_groups) . ')) ';
                        }
                        $sql_query['group'] .= 'WHERE c.nleft > ' . (int)$parent->nleft . '
                        AND c.nright < ' . (int)$parent->nright . '
                        ' . ($depth ? 'AND c.level_depth <= ' . ($parent->level_depth + (int)$depth) : '') . '
                        AND c.active = 1
                        GROUP BY c.id_category ORDER BY c.nleft, c.position';

                        $sql_query['from'] .= Shop::addSqlAssociation('product', 'p');
                }

                /*
                 * Loop over the filters again to add their restricting clauses to the sql
                 * query being built.
                 */

                foreach ($filters as $filter_tmp) {
                    $method_name = 'get' . (Tools::ucfirst($filter_tmp['type'])) . 'FilterSubQuery';
                    $method_name = preg_replace('/_/', '', $method_name);
                    if (method_exists('JmProductSearchProvider', $method_name)) {
                        $no_subquery_necessary = ($filter['type'] == $filter_tmp['type'] && $filter['id_value'] == $filter_tmp['id_value'] && ($filter['id_value'] || $filter['type'] === 'category' || $filter['type'] === 'condition' || $filter['type'] === 'quantity'));

                        if ($no_subquery_necessary) {
                            // Do not apply the same filter twice, i.e. when the primary filter
                            // and the sub filter have the same type and same id_value.
                            $sub_query_filter = array();
                        } else {
                            // The next part is hard to follow, but here's what I think this
                            // bit of code does:

                            // It checks whether some filters in the current facet
                            // (our current iterator, $filter_tmp), which
                            // is part of the "template" for this category, were selected by the
                            // user.

                            // If so, it formats the current facet
                            // in yet another strange way that is appropriate
                            // for calling get***FilterSubQuery.

                            // For instance, if inside $selected_filters I have:

                            // [id_attribute_group] => Array
                            //   (
                            //      [8] => 3_8
                            //      [11] => 3_11
                            //   )

                            // And $filter_tmp is:
                            // Array
                            // (
                            //   [type] => id_attribute_group
                            //   [id_value] => 3
                            //   [filter_show_limit] => 0
                            //   [filter_type] => 0
                            //  )

                            // Then $selected_filters_cleaned will be:
                            // Array
                            // (
                            //   [0] => 8
                            //   [1] => 11
                            // )

                            // The strategy employed is different whether we're dealing with
                            // a facet with an "id_value" (this is the most complex case involving
                            // the usual underscore-encoded values deserialization witchcraft)
                            // such as "id_attribute_group" or with a facet without id_value.
                            // In the latter case we're in luck because we can just use the
                            // facet in $selected_filters directly.

                            if (!is_null($filter_tmp['id_value'])) {
                                $selected_filters_cleaned = $this->cleanFilterByIdValue(
                                    @$selected_filters[$filter_tmp['type']],
                                    $filter_tmp['id_value']
                                );
                            } else {
                                $selected_filters_cleaned = @$selected_filters[$filter_tmp['type']];
                            }
                            $ignore_join = ($filter['type'] == $filter_tmp['type']);
                            // Prepare the new bits of SQL query.
                            // $ignore_join is set to true when the sub-facet
                            // is of the same "type" as the main facet. This way
                            // the method ($method_name) knows that the tables it needs are already
                            // there and don't need to be joined again.
                            $sub_query_filter = self::$method_name(
                                $selected_filters_cleaned,
                                $ignore_join
                            );
                        }
                        // Now we "merge" the query from the subfilter with the main query
                        foreach ($sub_query_filter as $key => $value) {
                            $sql_query[$key] .= $value;
                        }
                    }
                }

                $products = false;
                if (!empty($sql_query['from'])) {
                    $assembled_sql_query = implode(
                        "\n",
                        array(
                            $sql_query['select'],
                            $sql_query['from'],
                            $sql_query['join'],
                            $sql_query['where'],
                            $sql_query['group'],
                        )
                    );
                    $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($assembled_sql_query);
                }

                // price & weight have slidebar, so it's ok to not complete recompute the product list
                if (!empty($selected_filters['price']) && $filter['type'] != 'price' && $filter['type'] != 'weight') {
                    $products = self::filterProductsByPrice(@$selected_filters['price'], $products);
                }
                $productCache[$cacheKey] = $products;
            }

            switch ($filter['type']) {
                case 'price':
                    if ($this->showPriceFilter()) {
                        $price_array = array(
                            'type_lite' => 'price',
                            'type' => 'price',
                            'id_key' => 0,
                            'name' => $this->trans('Price', array(), 'Modules.FacetedSearch.Shop'),
                            'slider' => true,
                            'max' => '0',
                            'min' => null,
                            'unit' => $currency->sign,
                            'format' => $currency->format,
                            'filter_show_limit' => $filter['filter_show_limit'],
                            'filter_type' => $filter['filter_type'],
                            'list_of_values' => array(),
                        );
                        if ($compute_range_filters && isset($products) && $products) {
                            $rangeAggregator = new JmFacetedsearchRangeAggregator();
                            $aggregatedRanges = $rangeAggregator->aggregateRanges(
                                $products,
                                'price_min',
                                'price_max'
                            );
                            $price_array['min'] = $aggregatedRanges['min'];
                            $price_array['max'] = $aggregatedRanges['max'];

                            $mergedRanges = $rangeAggregator->mergeRanges(
                                $aggregatedRanges['ranges'],
                                10
                            );

                            $price_array['list_of_values'] = array_map(function (array $range) {
                                return array(
                                    0 => $range['min'],
                                    1 => $range['max'],
                                    'nbr' => $range['count'],
                                );
                            }, $mergedRanges);

                            $price_array['values'] = array($price_array['min'], $price_array['max']);
                        }
                        $filter_blocks[] = $price_array;
                    }
                    break;

                case 'weight':
                    $weight_array = array(
                        'type_lite' => 'weight',
                        'type' => 'weight',
                        'id_key' => 0,
                        'name' => $this->trans('Weight', array(), 'Modules.FacetedSearch.Shop'),
                        'slider' => true,
                        'max' => '0',
                        'min' => null,
                        'unit' => Configuration::get('PS_WEIGHT_UNIT'),
                        'format' => 5, // Ex: xxxxx kg
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type'],
                        'list_of_values' => array(),
                    );
                    if ($compute_range_filters && isset($products) && $products) {
                        $rangeAggregator = new JmFacetedsearchRangeAggregator();
                        $aggregatedRanges = $rangeAggregator->getRangesFromList(
                            $products,
                            'weight'
                        );
                        $weight_array['min'] = $aggregatedRanges['min'];
                        $weight_array['max'] = $aggregatedRanges['max'];

                        $mergedRanges = $rangeAggregator->mergeRanges(
                            $aggregatedRanges['ranges'],
                            10
                        );

                        $weight_array['list_of_values'] = array_map(function (array $range) {
                            return array(
                                0 => $range['min'],
                                1 => $range['max'],
                                'nbr' => $range['count'],
                            );
                        }, $mergedRanges);

                        if (empty($weight_array['list_of_values']) && isset($selected_filters['weight'])) {
                            // in case we don't have a list of values,
                            // add the original one.
                            // This may happen when e.g. all products
                            // weigh 0.
                            $weight_array['list_of_values'] = array(
                                array(
                                    0 => $selected_filters['weight'][0],
                                    1 => $selected_filters['weight'][1],
                                    'nbr' => count($products),
                                ),
                            );
                        }

                        $weight_array['values'] = array($weight_array['min'], $weight_array['max']);
                    }
                    $filter_blocks[] = $weight_array;
                    break;

                case 'condition':
                    $condition_array = array(
                        'new' => array('name' => $this->trans('New', array(), 'Modules.FacetedSearch.Shop'), 'nbr' => 0),
                        'used' => array('name' => $this->trans('Used', array(), 'Modules.FacetedSearch.Shop'), 'nbr' => 0),
                        'refurbished' => array('name' => $this->trans('Refurbished', array(), 'Modules.FacetedSearch.Shop'),
                            'nbr' => 0,),
                    );
                    if (isset($products) && $products) {
                        foreach ($products as $product) {
                            if (isset($selected_filters['condition']) && in_array($product['condition'], $selected_filters['condition'])) {
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
                                ++$condition_array[$product['condition']]['nbr'];
                            }
                        }
                    }
                    $filter_blocks[] = array(
                        'type_lite' => 'condition',
                        'type' => 'condition',
                        'id_key' => 0,
                        'name' => $this->trans('Condition', array(), 'Modules.FacetedSearch.Shop'),
                        'values' => $condition_array,
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type'],
                    );
                    break;

                case 'quantity':
                    $quantity_array = array(
                        0 => array('name' => $this->trans('Not available', array(), 'Modules.FacetedSearch.Shop'), 'nbr' => 0),
                        1 => array('name' => $this->trans('In stock', array(), 'Modules.FacetedSearch.Shop'), 'nbr' => 0),
                    );
                    foreach ($quantity_array as $key => $quantity) {
                        if (isset($selected_filters['quantity']) && in_array($key, $selected_filters['quantity'])) {
                            $quantity_array[$key]['checked'] = true;
                        }
                    }
                    if (isset($products) && $products) {
                        foreach ($products as $product) {
                            //If oosp move all not available quantity to available quantity
                            if ((int)$product['quantity'] > 0 || Product::isAvailableWhenOutOfStock($product['out_of_stock'])) {
                                ++$quantity_array[1]['nbr'];
                            } else {
                                ++$quantity_array[0]['nbr'];
                            }
                        }
                    }

                    $filter_blocks[] = array(
                        'type_lite' => 'quantity',
                        'type' => 'quantity',
                        'id_key' => 0,
                        'name' => $this->trans('Availability', array(), 'Modules.FacetedSearch.Shop'),
                        'values' => $quantity_array,
                        'filter_show_limit' => $filter['filter_show_limit'],
                        'filter_type' => $filter['filter_type'],
                    );

                    break;

                case 'manufacturer':
                    if (isset($products) && $products) {
                        $manufaturers_array = array();
                        foreach ($products as $manufacturer) {
                            if (!isset($manufaturers_array[$manufacturer['id_manufacturer']])) {
                                $manufaturers_array[$manufacturer['id_manufacturer']] = array('name' => $manufacturer['name'], 'nbr' => $manufacturer['nbr']);
                            }
                            if (isset($selected_filters['manufacturer']) && in_array((int)$manufacturer['id_manufacturer'], $selected_filters['manufacturer'])) {
                                $manufaturers_array[$manufacturer['id_manufacturer']]['checked'] = true;
                            }
                        }
                        $filter_blocks[] = array(
                            'type_lite' => 'manufacturer',
                            'type' => 'manufacturer',
                            'id_key' => 0,
                            'name' => $this->trans('Brand', array(), 'Modules.FacetedSearch.Shop'),
                            'values' => $manufaturers_array,
                            'filter_show_limit' => $filter['filter_show_limit'],
                            'filter_type' => $filter['filter_type'],
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
                                    'filter_type' => $filter['filter_type'],
                                );
                            }

                            if (!isset($attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']])) {
                                $attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']] = array(
                                    'color' => $attributes['color'],
                                    'name' => $attributes['attribute_name'],
                                    'nbr' => (int)$attributes['nbr'],
                                    'url_name' => $attributes['value_url_name'],
                                    'meta_title' => $attributes['value_meta_title'],
                                );
                            }

                            if (isset($selected_filters['id_attribute_group'][$attributes['id_attribute']])) {
                                $attributes_array[$attributes['id_attribute_group']]['values'][$attributes['id_attribute']]['checked'] = true;
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
                                    'filter_type' => $filter['filter_type'],
                                );
                            }

                            if (!isset($feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']])) {
                                $feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']] = array(
                                    'nbr' => (int)$feature['nbr'],
                                    'name' => $feature['value'],
                                    'url_name' => $feature['value_url_name'],
                                    'meta_title' => $feature['value_meta_title'],
                                );
                            }

                            if (isset($selected_filters['id_feature'][$feature['id_feature_value']])) {
                                $feature_array[$feature['id_feature']]['values'][$feature['id_feature_value']]['checked'] = true;
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
                            );

                            if ((int)$category['count_products']) {
                                ++$categories_with_products_count;
                            }

                            if (isset($selected_filters['category']) && in_array($category['id_category'], $selected_filters['category'])) {
                                $tmp_array[$category['id_category']]['checked'] = true;
                            }
                        }
                        if ($categories_with_products_count) {
                            $filter_blocks[] = array(
                                'type_lite' => 'category',
                                'type' => 'category',
                                'id_key' => 0,
                                'name' => $this->trans('Categories', array(), 'Modules.FacetedSearch.Shop'),
                                'values' => $tmp_array,
                                'filter_show_limit' => $filter['filter_show_limit'],
                                'filter_type' => $filter['filter_type'],
                            );
                        }
                    }
                    break;
            }
        }

        $latest_selected_filters = $selected_filters;

        return array(
            'filters' => $filter_blocks,
        );
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
            if (isset($filter_value)
                && $filter_value
                && isset($product['price_min'])
                && isset($product['id_product'])
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

    public static function searchTag(
        $id_lang,
        $tag,
        $count = false,
        $pageNumber = 0,
        $pageSize = 10,
        $orderBy = false,
        $orderWay = false,
        $useCookie = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        // Only use cookie if id_customer is not present
        if ($useCookie) {
            $id_customer = (int)$context->customer->id;
        } else {
            $id_customer = 0;
        }

        if (!is_numeric($pageNumber)
            || !is_numeric($pageSize)
            || !Validate::isBool($count)
            || !Validate::isValidSearch($tag)
            || $orderBy && !$orderWay
            || ($orderBy && !Validate::isOrderBy($orderBy))
            || ($orderWay && !Validate::isOrderBy($orderWay))) {
            return false;
        }

        if ($pageNumber < 1) {
            $pageNumber = 1;
        }
        if ($pageSize < 1) {
            $pageSize = 10;
        }

        $id = Context::getContext()->shop->id;
        $id_shop = $id ? $id : Configuration::get('PS_SHOP_DEFAULT');

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1');
        }

        if ($count) {
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue(
                'SELECT COUNT(DISTINCT pt.`id_product`) nb
			FROM
			`' . _DB_PREFIX_ . 'tag` t
			STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product_tag` pt
			ON (pt.`id_tag` = t.`id_tag` AND t.`id_lang` = ' . (int)$id_lang . ')
			STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pt.`id_product`)
			' . Shop::addSqlAssociation('product', 'p') . '
			LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`)
			LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs
			ON (cp.`id_category` = cs.`id_category` AND cs.`id_shop` = ' . (int)$id_shop . ')
			' . (Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg
			ON (cg.`id_category` = cp.`id_category`)' : '') . '
			WHERE product_shop.`active` = 1
			AND p.visibility IN (\'both\', \'search\')
			AND cs.`id_shop` = ' . (int)Context::getContext()->shop->id . '
			' . $sql_groups . '
			AND t.`name` LIKE \'%' . pSQL($tag) . '%\''
            );
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

        $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, jm.not_visible,
        IFNULL(stock.quantity, 0)
        as quantity, pl.`description_short`, pl.`link_rewrite`, pl.`name`, pl.`available_now`, pl.`available_later`,
					MAX(image_shop.`id_image`) id_image, il.`legend`, m.`name` manufacturer_name, 1 position,
					DATEDIFF(
						p.`date_add`,
						DATE_SUB(
							"' . date('Y-m-d') . ' 00:00:00",
							INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
						)
					) > 0 new
				FROM
				`' . _DB_PREFIX_ . 'tag` t
				STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product_tag` pt
				ON (pt.`id_tag` = t.`id_tag`
				AND t.`id_lang` = ' . (int)$id_lang . ')
				STRAIGHT_JOIN `' . _DB_PREFIX_ . 'product` p ON (p.`id_product` = pt.`id_product`)
				INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				' . Shop::addSqlAssociation('product', 'p', false) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product`
				AND product_attribute_shop.`default_on` = 1
				AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product`
					AND image_shop.cover=1
					AND image_shop.id_shop=' . (int)$context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
				ON (image_shop.`id_image` = il.`id_image`
				AND il.`id_lang` = ' . (int)$id_lang . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`)
				LEFT JOIN `' . _DB_PREFIX_ . 'category_product` cp ON (cp.`id_product` = p.`id_product`)
				' . (Group::isFeatureActive() ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_group` cg
				ON (cg.`id_category` = cp.`id_category`)' : '') . '
				LEFT JOIN `' . _DB_PREFIX_ . 'category_shop` cs
				ON (cp.`id_category` = cs.`id_category`
				AND cs.`id_shop` = ' . (int)$id_shop . ')
				' . Product::sqlStock('p', 0) . '
				WHERE product_shop.`active` = 1
				    AND (jm.`not_visible` = 1 OR jm.`not_visible` is null)
					AND cs.`id_shop` = ' . (int)Context::getContext()->shop->id . '
					' . $sql_groups . '
					AND t.`name` LIKE \'%' . pSQL($tag) . '%\'
					GROUP BY product_shop.id_product
				ORDER BY position DESC' . ($orderBy ? ', ' . $orderBy : '') . ($orderWay ? ' ' . $orderWay : '') . '
				LIMIT ' . (int)(($pageNumber - 1) * $pageSize) . ',' . (int)$pageSize;
        if (!$result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false)) {
            return false;
        }

        return Product::getProductsProperties((int)$id_lang, $result);
    }

    public static function find(
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
                $start_search = Configuration::get('PS_SEARCH_START') ? '%' : '';
                $end_search = Configuration::get('PS_SEARCH_END') ? '' : '%';

                $intersect_array[] = 'SELECT DISTINCT si.id_product
					FROM ' . _DB_PREFIX_ . 'search_word sw
					LEFT JOIN ' . _DB_PREFIX_ . 'search_index si ON sw.id_word = si.id_word
					WHERE sw.id_lang = ' . (int)$id_lang . '
						AND sw.id_shop = ' . $context->shop->id . '
						AND sw.word LIKE
					' . ($word[0] == '-'
                        ? ' \'' . $start_search . pSQL(Tools::substr($word, 1, PS_SEARCH_MAX_WORD_LENGTH)) . $end_search . '\''
                        : ' \'' . $start_search . pSQL(Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)) . $end_search . '\''
                    );

                if ($word[0] != '-') {
                    $score_array[] =
                        'sw.word LIKE \'' . $start_search . pSQL(
                            Tools::substr($word, 0, PS_SEARCH_MAX_WORD_LENGTH)
                        ) . $end_search . '\'';
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
				FROM ' . _DB_PREFIX_ . 'search_word sw
				LEFT JOIN ' . _DB_PREFIX_ . 'search_index si ON sw.id_word = si.id_word
				WHERE sw.id_lang = ' . (int)$id_lang . '
					AND sw.id_shop = ' . $context->shop->id . '
					AND si.id_product = p.id_product
					AND (' . implode(' OR ', $score_array) . ')
			) position';
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = 'AND cg.`id_group` ' . (count($groups) ? 'IN (' . implode(',', $groups) . ')' : '= 1');
        }

        $results = $db->executeS('
		SELECT DISTINCT cp.`id_product`, jm.not_visible
		FROM `' . _DB_PREFIX_ . 'category_product` cp
		' . (Group::isFeatureActive() ? 'INNER JOIN `' . _DB_PREFIX_ . 'category_group` cg
		ON cp.`id_category` = cg.`id_category`' : '') . '
		INNER JOIN `' . _DB_PREFIX_ . 'category` c ON cp.`id_category` = c.`id_category`
		INNER JOIN `' . _DB_PREFIX_ . 'product` p ON cp.`id_product` = p.`id_product`
		LEFT JOIN `' . _DB_PREFIX_ . 'jm_product_visibility` jm ON (jm.`id_product`= p.`id_product`)
		' . Shop::addSqlAssociation('product', 'p', false) . '
		WHERE c.`active` = 1
		AND product_shop.`active` = 1
		AND product_shop.`visibility` IN ("both", "search")
		AND product_shop.indexed = 1
		AND (jm.`not_visible` = 1 OR jm.`not_visible` is null) 
		' . $sql_groups, true, false);

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

        if (!empty($hidden_pack_product_ids)) {
            $eligible_products = array_unique(array_diff($eligible_products, $hidden_pack_product_ids));
        }

        $product_pool = '';
        foreach ($eligible_products as $id_product) {
            if ($id_product) {
                $product_pool .= (int)$id_product . ',';
            }
        }
        if (empty($product_pool)) {
            return ($ajax ? array() : array('total' => 0, 'result' => array()));
        }
        $product_pool = ((strpos($product_pool, ',') === false) ?
            (' = ' . (int)$product_pool . ' ') : (' IN (' . rtrim($product_pool, ',') . ') '));

        if ($ajax) {
            $sql = 'SELECT DISTINCT p.id_product, pl.name pname, cl.name cname,
						cl.link_rewrite crewrite, pl.link_rewrite prewrite ' . $score . '
					FROM ' . _DB_PREFIX_ . 'product p
					INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
						p.`id_product` = pl.`id_product`
						AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . '
					)
					' . Shop::addSqlAssociation('product', 'p') . '
					INNER JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (
						product_shop.`id_category_default` = cl.`id_category`
						AND cl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('cl') . '
					)
					WHERE p.`id_product` ' . $product_pool . '
					ORDER BY position DESC LIMIT 10';
            return $db->executeS($sql, true, false);
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]) . '.`' . pSQL($order_by[1]) . '`';
        }
        $alias = '';
        if ($order_by == 'price') {
            $alias = 'product_shop.';
        } elseif (in_array($order_by, array('date_upd', 'date_add'))) {
            $alias = 'p.';
        }


        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
				pl.`description_short`, pl.`available_now`, pl.`available_later`, pl.`link_rewrite`, pl.`name`,
			 image_shop.`id_image` id_image, il.`legend`, m.`name` manufacturer_name ' . $score . ',
				DATEDIFF(
					p.`date_add`,
					DATE_SUB(
						"' . date('Y-m-d') . ' 00:00:00",
						INTERVAL ' . (Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ?
                Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20) . ' DAY
					)
				) > 0 new' . (Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity
				AS product_attribute_minimal_quantity,
				IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '') . '
				FROM ' . _DB_PREFIX_ . 'product p
				' . Shop::addSqlAssociation('product', 'p') . '
				INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				' . (Combination::isFeatureActive() ?
                'LEFT JOIN `' . _DB_PREFIX_ . 'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product`
				AND product_attribute_shop.`default_on` = 1
				AND product_attribute_shop.id_shop=' . (int)$context->shop->id . ')' : '') . '
				' . Product::sqlStock('p', 0) . '
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product`
					AND image_shop.cover=1
					AND image_shop.id_shop=' . (int)$context->shop->id . ')
				LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il
				ON (image_shop.`id_image` = il.`id_image`
				AND il.`id_lang` = ' . (int)$id_lang . ')
				WHERE p.`id_product` ' . $product_pool . '
				GROUP BY product_shop.id_product
				' . ($order_by ? 'ORDER BY  ' . $alias . $order_by : '') . ($order_way ? ' ' . $order_way : '') . '
				LIMIT ' . (int)(($page_number - 1) * $page_size) . ',' . (int)$page_size;
        $result = $db->executeS($sql, true, false);

        $sql = 'SELECT COUNT(*)
				FROM ' . _DB_PREFIX_ . 'product p
				' . Shop::addSqlAssociation('product', 'p') . '
				INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (
					p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = ' . (int)$id_lang . Shop::addSqlRestrictionOnLang('pl') . '
				)
				LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE p.`id_product` ' . $product_pool;
        $total = $db->getValue($sql, false);

        if (!$result) {
            $result_properties = false;
        } else {
            $result_properties = Product::getProductsProperties((int)$id_lang, $result);
        }

        return array('total' => $total, 'result' => $result_properties);
    }

    protected function showPriceFilter()
    {
        return Group::getCurrent()->show_prices;
    }


    protected function trans($id, array $parameters = array(), $domain = null, $locale = null)
    {
        $parameters['legacy'] = 'htmlspecialchars';
        return Context::getContext()->getTranslator()->trans($id, $parameters, $domain, $locale);
    }

    private static function getIdattributegroupFilterSubQuery($filter_value, $ignore_join = false)
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
        $query_filters_where = ' AND EXISTS (SELECT * FROM ' . _DB_PREFIX_ . 'category_product cp WHERE id_product = p.id_product AND ';
        foreach ($filter_value as $id_category) {
            $query_filters_where .= 'cp.`id_category` = ' . (int)$id_category . ' OR ';
        }
        $query_filters_where = rtrim($query_filters_where, 'OR ') . ') ';

        return array('where' => $query_filters_where);
    }

    private static function getQuantityFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value) || count($filter_value) == 2) {
            return array();
        }

        $query_filters_join = '';

        $query_filters = ' AND sav.quantity ' . (!$filter_value[0] ? '<=' : '>') . ' 0 ';
        $query_filters_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` sav ON (sav.id_product = p.id_product AND sav.id_shop = ' . (int)Context::getContext()->shop->id . ') ';

        return array('where' => $query_filters, 'join' => $query_filters_join);
    }

    private static function getManufacturerFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value)) {
            $query_filters = '';
        } else {
            array_walk($filter_value, create_function('&$id_manufacturer', '$id_manufacturer = (int)$id_manufacturer;'));
            $query_filters = ' AND p.id_manufacturer IN (' . implode($filter_value, ',') . ')';
        }
        if ($ignore_join) {
            return array('where' => $query_filters);
        } else {
            return array('where' => $query_filters, 'join' => 'LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.id_manufacturer = p.id_manufacturer) ');
        }
    }

    private static function getConditionFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value) || count($filter_value) == 3) {
            return array();
        }

        $query_filters = ' AND p.condition IN (';

        foreach ($filter_value as $cond) {
            $query_filters .= '\'' . Db::getInstance()->escape($cond) . '\',';
        }
        $query_filters = rtrim($query_filters, ',') . ') ';

        return array('where' => $query_filters);
    }

    private static function getWeightFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (isset($filter_value) && $filter_value) {
            if ($filter_value[0] != 0 || $filter_value[1] != 0) {
                return array('where' => ' AND p.`weight` BETWEEN ' . (float)($filter_value[0] - 0.001) . ' AND ' . (float)($filter_value[1] + 0.001) . ' ');
            }
        }

        return array();
    }

    private static function getPriceFilterSubQuery($filter_value, $ignore_join = false)
    {
        $id_currency = (int)Context::getContext()->currency->id;

        if ($ignore_join && $filter_value) {
            return array('where' => ' AND psi.price_min >= ' . (int)$filter_value[0] . ' AND psi.price_max <= ' . (int)$filter_value[1]);
        } elseif ($filter_value) {
            $price_filter_query = '
			INNER JOIN `' . _DB_PREFIX_ . 'layered_price_index` psi ON (psi.id_product = p.id_product AND psi.id_currency = ' . (int)$id_currency . '
			AND psi.price_min <= ' . (int)$filter_value[1] . ' AND psi.price_max >= ' . (int)$filter_value[0] . ' AND psi.id_shop=' . (int)Context::getContext()->shop->id . ') ';

            return array('join' => $price_filter_query);
        }

        return array();
    }

    private static function getIdfeatureFilterSubQuery($filter_value, $ignore_join = false)
    {
        if (empty($filter_value)) {
            return array();
        }
        $query_filters = ' AND EXISTS (SELECT * FROM ' . _DB_PREFIX_ . 'feature_product fp WHERE fp.id_product = p.id_product AND ';
        foreach ($filter_value as $filter_val) {
            $query_filters .= 'fp.`id_feature_value` = ' . (int)$filter_val . ' OR ';
        }
        $query_filters = rtrim($query_filters, 'OR ') . ') ';

        return array('where' => $query_filters);
    }
}

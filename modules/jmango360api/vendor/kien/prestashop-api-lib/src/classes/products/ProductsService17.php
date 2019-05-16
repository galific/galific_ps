<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

if (file_exists(_PS_MODULE_DIR_ . 'netreviews/NetReviewsModel.php')) {
    require_once _PS_MODULE_DIR_ . "netreviews/NetReviewsModel.php";
}
if (file_exists(_PS_MODULE_DIR_ . 'netreviews/models/NetReviewsModel.php')) {
    require_once _PS_MODULE_DIR_ . "netreviews/models/NetReviewsModel.php";
}

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ProductsService17 extends ProductsService
{
    protected $availableSortOders;
    protected $facetCollection;
    private $jmProductSearchProvider;
    protected $id_products;

    public function doExecute()
    {
        $id_category = $this->getRequestValue('id_category');

        if (!$id_category || !Validate::isUnsignedId($id_category)) {
            throw new WebserviceException('Missing category ID', 400);
        }
        $this->jmProductSearchProvider = new \JmProductSearchProvider();
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
            $this->response = new JmResponse();
            $this->response->errors[] = new JmError(500, $this->getTranslation(
                'You do not have access to this category.',
                'product-service'
            ));

            return;
        }

        $this->p = $this->getRequestValue('page_num', 1);
        $this->n = $this->getRequestValue('page_size', 20);

        $this->initializeCart(Context::getContext());
        Product::initPricesComputation();

        $this->productSort();
        //$result = $this->getProductList17();
        $result = $this->getProductSearchVariables()['result'];
        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );
        $this->cat_products = $products;
        $this->nb_products = $result->getTotalProductsCount();

        $products = $this->transformProductList();

        //assign verified review to product list
        $avisverifies_display_stars = Configuration::get('AV_DISPLAYSTARPLIST', null, null, $this->id_shop);
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();
        if ($avisverifies_display_stars
            && $moduleManager->isInstalled('netreviews')
            && Module::getInstanceByName('netreviews')->active) {
            $this->assignVerifiedReview($products);
        }

        $this->availableSortOders = $result->getAvailableSortOrders();
        $this->facetCollection = $result->getFacetCollection();

        $nav_layered = $this->transformNavigationLayered();
        $this->setBanner($products);
        $this->response = new \JmProductsResponse();
        $this->response->products = $products;
        $this->response->navigation_layered = $nav_layered;
    }

    protected function getProductList17()
    {
        // the search provider will need a context (language, shop...) to do its job
        $context = $this->getProductSearchContext();

        // the controller generates the query...
        $query = $this->getProductSearchQuery();

        // ...modules decide if they can handle it (first one that can is used)
        if ($query->getIdCategory() || $query->id_products) {
            $provider = new \JmProductSearchProvider();
        }

        // if no module wants to do the query, then the core feature is used
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }

        $resultsPerPage = $this->n;
        if ($resultsPerPage <= 0 || $resultsPerPage > 36) {
            $resultsPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        // we need to set a few parameters from back-end preferences
        $query
            ->setResultsPerPage($this->n)
            ->setPage($this->p);

        $encodedFacets = Tools::getValue('selected_filters');
        if ($encodedFacets) {
            $query->setEncodedFacets($encodedFacets);
        }

        // We're ready to run the actual query!

        $result = $provider->runQuery(
            $context,
            $query
        );

        return $result;
    }

    /**
     * The ProductSearchContext is passed to search providers
     * so that they can avoid using the global id_lang and such
     * variables. This method acts as a factory for the ProductSearchContext.
     *
     * @return ProductSearchContext a search context for the queries made by this controller
     */
    protected function getProductSearchContext()
    {
        return (new ProductSearchContext())
            ->setIdShop($this->context->shop->id)
            ->setIdLang($this->context->language->id)
            ->setIdCurrency($this->context->currency->id)
            ->setIdCustomer(
                $this->context->customer ?
                    $this->context->customer->id :
                    null
            );
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setIdCategory($this->category->id)
            ->setSortOrder(new SortOrder('product', $this->orderBy, $this->orderWay))//            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by'), Tools::getProductsOrder('way')))
        ;
        $query->id_products = $this->id_products;
        return $query;
    }

    // Add tags of corresponding language to the response.
    protected function setBanner(&$products)
    {
        foreach ($products as $prod) {
            $temp = array();
            $prod->banner_info = array();
            $show_price = $prod->show_price;
            $temp['code'] = 'pack';
            $temp['value'] = $prod->pack ? '1' : '0';
            $temp['label'] = $this->context->getTranslator()->trans('Pack', array(), 'Shop.Theme.Catalog');
            $prod->banner_info[] = $temp;

            $temp['code'] = 'on_sale';
            $temp['value'] = $show_price ? $prod->on_sale : '0';
            $temp['label'] = $this->context->getTranslator()->trans('On sale!', array(), 'Shop.Theme.Catalog');
            $prod->banner_info[] = $temp;

            $temp['code'] = 'new';
            $temp['value'] = $prod->new;
            $temp['label'] = $this->context->getTranslator()->trans('New', array(), 'Shop.Theme.Catalog');
            $prod->banner_info[] = $temp;
        }
    }

    private function getProductSearchProviderFromModules($query)
    {
        $providers = null;
        $val = $query['query'];
        // do something with query,
        // e.g. use $query->getIdCategory()
        // to choose a template for filters.
        // Query is an instance of:
        // PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery
        if ($val->getIdCategory()) {
            $providers = new \JmProductSearchProvider;
        }

        if (!is_array($providers)) {
            $providers = array();
        }

        foreach ($providers as $provider) {
            if ($provider instanceof ProductSearchProviderInterface) {
                return $provider;
            }
        }

        return;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new CategoryProductSearchProvider(
            $this->getTranslator(),
            $this->category
        );
    }

    protected function getTranslator()
    {
        return $this->translator;
    }

    private function getFactory()
    {
        return new ProductPresenterFactory($this->context, new TaxConfiguration());
    }

    protected function getProductPresentationSettings()
    {
        return $this->getFactory()->getPresentationSettings();
    }

    protected function getProductPresenter()
    {
        return $this->getFactory()->getPresenter();
    }

    /**
     * Takes an associative array with at least the "id_product" key
     * and returns an array containing all information necessary for
     * rendering the product in the template.
     *
     * @param array $rawProduct an associative array with at least the "id_product" key
     *
     * @return array a product ready for templating
     */
    private function prepareProductForTemplate(array $rawProduct)
    {
        $product = (new ProductAssembler($this->context))
            ->assembleProduct($rawProduct);

        $presenter = $this->getProductPresenter();
        $settings = $this->getProductPresentationSettings();

        return $presenter->present(
            $settings,
            $product,
            $this->context->language
        );
    }

    /**
     * Runs "prepareProductForTemplate" on the collection
     * of product ids passed in.
     *
     * @param array $products array of arrays containing at list the "id_product" key
     *
     * @return array of products ready for templating
     */
    protected function prepareMultipleProductsForTemplate(array $products)
    {
        return array_map(array($this, 'prepareProductForTemplate'), $products);
    }

    protected function transformNavigationLayered()
    {
        $data = array();
        $navigation_layered = new JmNavigationLayered();
        $navigation_layered->order_by = $this->orderBy;
        $navigation_layered->order_way = $this->orderWay;
        $navigation_layered->availableSortOders = $this->transformSortOrders($this->availableSortOders);
        $navigation_layered->filters = $this->transformFilters($this->facetCollection->getFacets());
        $data[] = $navigation_layered;
        return $data;
    }

    private function transformSortOrders($sortOders = array())
    {
        $data = array();

        foreach ($sortOders as $sortOder) {
            $item = new \JmSortOrder();

            $item->entity = $sortOder->getEntity();
            $item->field = $sortOder->getField();
            $item->direction = $sortOder->getDirection();
            if (strcmp("position", $sortOder->getField()) == 0) {
                $item->label = $this->getTranslation(relevance, 'order-by-values');
            } else {
                $item->label = $this->getTranslation($sortOder->getField(), 'order-by-values');
            }

            $data[] = $item;
        }

        return $data;
    }

    private function transformFilters($filters = array())
    {
        //Code check duplicate filters and remove.
        $arr = array();
        $flag = true;
        foreach ($filters as $temps) {
            foreach ($arr as $a) {
                if ($a == $temps) {
                    $flag = false;
                    break;
                }
            }
            if ($flag) {
                $arr[] = $temps;
            }
            $flag = true;
        }

        // Transfrom format for filter
        $data = array();
        foreach ($arr as $filter) {
            if ($filter->isDisplayed() == false) {
                continue;
            }

            $item = new JmFilter();

            $item->type_lite = $filter->getType();
            $item->type = $filter->getType();
            $item->name = $filter->getLabel();
            $item->nameKey = $filter->getLabel();
            $item->values = $this->transformFilterValues($filter->getFilters());
            $data[] = $item;
        }

        return $data;
    }

    private function transformFilterValues($filterValues = array())
    {
        $data = array();

        foreach ($filterValues as $value) {
            if ($value->isDisplayed() == false) {
                continue;
            }
            if ($value->isActive() == true) {
                continue;
            }

            $item = new JmFilterValue();
            $item->name = $value->getLabel();
            $item->nbr = $value->getMagnitude();
            if (strcmp("price", $value->getType()) == 0) {
                $symbol = $value->getProperty('symbol');
                $fromPrice = $value->getValue()['from'];
                $toPrice = $value->getValue()['to'];
                $formatedName = $symbol . '-' . $fromPrice . '-' . $toPrice;
                $item->valueKey = $formatedName;
            } else {
                $formatedName = $value->getLabel();
                $item->valueKey = $this->serializeListOfStrings('-', '-', array($formatedName));
            }

            $data[] = $item;
        }

        return $data;
    }

    /**
     * Copy from \PrestaShop\PrestaShop\Core\Product\Search\URLFragmentSerializer:serializeListOfStrings()
     *
     * @param $separator
     * @param $escape
     * @param array $list
     * @return string
     */
    protected function serializeListOfStrings($separator, $escape, array $list)
    {
        return implode($separator, array_map(function ($item) use ($separator, $escape) {
            return str_replace($separator, $escape . $separator, $item);
        }, $list));
    }

    /**
     * Transform a CamelCase string to string
     *
     * @param string $string
     * @return string
     */
    public function buildFilterKey($string)
    {
        // 'CMSCategories' => 'CMS_Categories'
        // 'RangePrice' => 'Range_Price'
        return trim(preg_replace('/([A-Z][a-z])/', '_$1', $string), '+');
    }

    protected function getProductSearchVariables()
    {
        /*
         * To render the page we need to find something (a ProductSearchProviderInterface)
         * that knows how to query products.
         */

        // the search provider will need a context (language, shop...) to do its job
        $context = $this->getProductSearchContext();

        // the controller generates the query...
        $query = $this->getProductSearchQuery();

        // ...modules decide if they can handle it (first one that can is used)
        $provider = new \JmProductSearchProvider();
        //$provider = $this->getProductSearchProviderFromModules($query);
        // if no module wants to do the query, then the core feature is used
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }

        $resultsPerPage = (int)Tools::getValue('resultsPerPage');
        if ($resultsPerPage <= 0 || $resultsPerPage > 36) {
            $resultsPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        // we need to set a few parameters from back-end preferences
        $query
            ->setResultsPerPage($this->n)
            ->setPage($this->p);

        // set the sort order if provided in the URL
        if (($encodedSortOrder = Tools::getValue('order'))) {
            $query->setSortOrder(SortOrder::newFromString(
                $encodedSortOrder
            ));
        }

        // get the parameters containing the encoded facets from the URL
        $encodedFacets = Tools::getValue('selected_filters');

        /*
         * The controller is agnostic of facets.
         * It's up to the search module to use /define them.
         *
         * Facets are encoded in the "q" URL parameter, which is passed
         * to the search provider through the query's "$encodedFacets" property.
         */

        $query->setEncodedFacets($encodedFacets);

        // We're ready to run the actual query!

        $result = $provider->runQuery(
            $context,
            $query
        );
        $result->setAvailableSortOrders($this->jmProductSearchProvider->getAvailableSortOrders());

        // sort order is useful for template,
        // add it if undefined - it should be the same one
        // as for the query anyway
        if (!$result->getCurrentSortOrder()) {
            $result->setCurrentSortOrder($query->getSortOrder());
        }

        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );

        // prepare the sort orders
        // note that, again, the product controller is sort-orders
        // agnostic
        // a module can easily add specific sort orders that it needs
        // to support (e.g. sort by "energy efficiency")

        $sort_orders = null;
        $sort_selected = false;
        if (!empty($sort_orders)) {
            foreach ($sort_orders as $order) {
                if (isset($order['current']) && true === $order['current']) {
                    $sort_selected = $order['label'];
                    break;
                }
            }
        }

        $searchVariables = array(
            'result' => $result,
            'products' => $products,
            'sort_orders' => $sort_orders,
            'sort_selected' => $sort_selected,
            'current_url' => $this->updateQueryString(array(
                'selected_filters' => $result->getEncodedFacets(),
            )),
        );

        Hook::exec('filterProductSearch', array('searchVariables' => &$searchVariables));
        Hook::exec('actionProductSearchAfter', $searchVariables);

        return $searchVariables;
    }

    protected function updateQueryString(array $extraParams = null)
    {
        $uriWithoutParams = explode('?', $_SERVER['REQUEST_URI'])[0];
        $url = Tools::getCurrentUrlProtocolPrefix() . $_SERVER['HTTP_HOST'] . $uriWithoutParams;
        $params = array();
        $paramsFromUri = '';
        if (strpos($_SERVER['REQUEST_URI'], '?') !== false) {
            $paramsFromUri = explode('?', $_SERVER['REQUEST_URI'])[1];
        }
        parse_str($paramsFromUri, $params);

        if (null !== $extraParams) {
            foreach ($extraParams as $key => $value) {
                if (null === $value) {
                    unset($params[$key]);
                } else {
                    $params[$key] = $value;
                }
            }
        }

        ksort($params);

        if (null !== $extraParams) {
            foreach ($params as $key => $param) {
                if (null === $param || '' === $param) {
                    unset($params[$key]);
                }
            }
        } else {
            $params = array();
        }

        $queryString = str_replace('%2F', '/', http_build_query($params, '', '&'));

        return $url . ($queryString ? "?$queryString" : '');
    }

    public function assignVerifiedReview(&$products)
    {
        $o_av = new NetReviewsModel();
        $multisite = Configuration::get('AV_MULTISITE');
        $av_idshop = (!empty($multisite)) ? $this->context->shop->getContextShopID() : null;
        $productReviewService = new \ProductReviewService();
        if (Configuration::get('AV_MULTILINGUE', null, null, $av_idshop) == 'checked') {
            $this->id_lang = $this->context->language->id;
            $iso_lang = pSQL(Language::getIsoById($this->id_lang));
            $group_name = $productReviewService->getIdConfigurationGroup($iso_lang);
        }
        foreach ($products as &$product) {
            $stats_product = $o_av->getStatsProduct($product->id_product, $group_name, $av_idshop);
            $product->verified_review = $stats_product ? $stats_product : null;
        }
    }
}

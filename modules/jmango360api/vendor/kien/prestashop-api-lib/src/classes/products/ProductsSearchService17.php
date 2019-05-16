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
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Adapter\Search\SearchProductSearchProvider;
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ProductsSearchService17 extends BaseService
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
    public $search_string;

    /** var boolean - Display quantity order if stock management disabled */
    public $stock_management;

    private $id_lang;

    protected $id_customer;

    protected $availableSortOders;

    protected $order_by_values  = array(0 => 'name',
        1 => 'price',
        2 => 'date_add',
        3 => 'date_upd',
        4 => 'position',
        5 => 'manufacturer_name',
        6 => 'quantity',
        7 => 'reference');
    protected $order_way_values = array(0 => 'asc', 1 => 'desc');

    public function doExecute()
    {

        $this->search_string = Tools::getValue('query');
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

        $this->orderBy = Tools::strtolower(Tools::getValue(
            'orderby',
            $this->order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')]
        ));
        $this->orderWay = Tools::strtolower(Tools::getValue(
            'orderway',
            $this->order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')]
        ));

        if (strcmp($this->orderBy, 'lowerCaseName')==0) {
            $this->orderBy = 'name';
        }

        if (strcmp($this->orderBy, 'final_price')==0) {
            $this->orderBy = 'price';
        }

        if (!in_array($this->orderBy, $this->order_by_values)) {
            $this->orderBy =  Tools::getProductsOrder("by");
        }

        if (!in_array($this->orderWay, $this->order_way_values)) {
            $this->orderWay =  Tools::getProductsOrder("way");
        }

        $result=$this->productSearch();
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );
        $this->cat_products = $products;
        $this->nb_products = $result->getTotalProductsCount();
        $products = $this->transformProductList();
        $this->setBannerInfo($products);

        //assign verified review to product list
        $avisverifies_display_stars = Configuration::get('AV_DISPLAYSTARPLIST', null, null, $this->context->shop->id);
        $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
        $moduleManager = $moduleManagerBuilder->build();
        if ($avisverifies_display_stars
            && $moduleManager->isInstalled('netreviews')
            && Module::getInstanceByName('netreviews')->active) {
            $this->assignVerifiedReview($products);
        }

        $this->availableSortOders = $result->getAvailableSortOrders();
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

    public function productSearch()
    {
        // the search provider will need a context (language, shop...) to do its job
        $context = $this->getProductSearchContext();

        // the controller generates the query...
        $query = $this->getProductSearchQuery();

        // ...modules decide if they can handle it (first one that can is used)
        $provider = new \JmProductSearchProvider($query);

        // if no module wants to do the query, then the core feature is used
        if (null === $provider) {
            $provider = $this->getDefaultProductSearchProvider();
        }

        $resultsPerPage = $this->n;
        if ($resultsPerPage <= 0 || $resultsPerPage > 36) {
            $resultsPerPage = Configuration::get('PS_PRODUCTS_PER_PAGE');
        }

        $query
            ->setResultsPerPage($resultsPerPage)
            ->setPage(max($this->p, 1));

        $result = $provider->runQuery(
            $context,
            $query
        );

        $result->setAvailableSortOrders($provider->getAvailableSortOrders());

        return $result;
    }

    public function transformProductList()
    {

        $data = array();

        foreach ($this->cat_products as $product) {
            $prod = \ProductDataTransform::productList($product, $this->stock_management, $this->id_customer);
            $data[] = $prod;
        }
        return $data;
    }

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
        $query->setSearchString($this->search_string);
        if ($this->orderBy!=null && $this->orderWay!=null) {
            $query->setSortOrder(new SortOrder('product', $this->orderBy, $this->orderWay));
        }
        return $query;
    }

    private function getProductSearchProviderFromModules($query)
    {
        $providers = Hook::exec(
            'productSearchProvider',
            array('query' => $query),
            null,
            true
        );

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
        return new SearchProductSearchProvider(
            $this->context->getTranslator()
        );
    }

    protected function prepareMultipleProductsForTemplate(array $products)
    {
        return array_map(array($this, 'prepareProductForTemplate'), $products);
    }

    protected function transformNavigationLayered()
    {
        $data = array();

        $navigation_layered = new \JmNavigationLayered();

        $navigation_layered->order_by = $this->orderBy;
        $navigation_layered->order_way = $this->orderWay;
        $navigation_layered->availableSortOders = $this->transformSortOrders($this->availableSortOders);
        $data[] = $navigation_layered;
        return $data;
    }

    private function transformSortOrders($sortOders = array())
    {
        $data = array();

        foreach ($sortOders as $sortOder) {
            $item = new JmSortOrder();

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

    public function assignVerifiedReview(&$products)
    {
        $o_av = new NetReviewsModel();
        $multisite = Configuration::get('AV_MULTISITE');
        $av_idshop = (!empty($multisite))? $this->context->shop->getContextShopID():null;
        $productReviewService = new ProductReviewService();
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

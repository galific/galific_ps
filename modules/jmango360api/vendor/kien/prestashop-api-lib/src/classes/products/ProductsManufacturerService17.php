<?php
/**
 *
 * @author Jmango
 * @copyright opyright 2007-2015 PrestaShop SA
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

class ProductsManufacturerService17 extends ProductsService
{
    protected $availableSortOders;
    private $manufacturer;

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
        $result = $this->doProductSearch();

        $this->cat_products = $result->getProducts();
        $this->nbProducts = $result->getTotalProductsCount();
        $this->availableSortOders = $result->getAvailableSortOrders();

        $products = $this->transformProductList(true);
        $this->setBanner($products);

        $nav_layered = $this->transformNavigationLayered();
        $this->response = new JmProductsResponse();
        $this->response->products = $products;
        $this->response->navigation_layered = $nav_layered;
    }

    protected function getProductSearchQuery()
    {
        $query = new ProductSearchQuery();
        $query
            ->setIdManufacturer($this->manufacturer->id)
            ->setSortOrder(new SortOrder('product', Tools::getProductsOrder('by', $this->orderBy), Tools::getProductsOrder('way', $this->orderWay)));
        ;

        return $query;
    }

    protected function getDefaultProductSearchProvider()
    {
        return new JmManufacturerProductSearchProvider(
            Context::getContext()->getTranslator(),
            $this->manufacturer
        );
    }

    protected function doProductSearch()
    {
        $context = $this->getProductSearchContext();

        // the controller generates the query...
        $query = $this->getProductSearchQuery();

        $provider = $this->getDefaultProductSearchProvider();

        // we need to set a few parameters from back-end preferences
        $query
            ->setResultsPerPage($this->n)
            ->setPage($this->p)
        ;

        // We're ready to run the actual query!

        $result = $provider->runQuery(
            $context,
            $query
        );

        return $result;
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
            )
            ;
    }

    protected function transformNavigationLayered()
    {
        $data = array();

        $navigation_layered = new JmNavigationLayered();

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

    protected function setBanner(&$products)
    {
        $temp = array();
        foreach ($products as $prod) {
            $prod->banner_info = array();
            $show_price = $prod->show_price;
            $temp['code']='pack';
            $temp['value']=$prod->pack?'1':'0';
            $temp['label']=$this->context->getTranslator()->trans('Pack', array(), 'Shop.Theme.Catalog');
            $prod->banner_info[]=$temp;

            $temp['code']='on_sale';
            $temp['value']=$show_price?$prod->on_sale:'0';
            $temp['label']=$this->context->getTranslator()->trans('On sale!', array(), 'Shop.Theme.Catalog');
            $prod->banner_info[]=$temp;

            $temp['code']='new';
            $temp['value']=$prod->new;
            $temp['label']=$this->context->getTranslator()->trans('New', array(), 'Shop.Theme.Catalog');
            $prod->banner_info[]=$temp;
        }
    }
}

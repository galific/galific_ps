<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */


class ProductLookbookService17 extends ProductsService17
{
    public function doExecute()
    {
        $arrayId = explode(",", Tools::getValue('id_products'));
        if (!$arrayId || empty($arrayId)) {
            throw new WebserviceException('Missing product IDs', 400);
        }
        $id_products = array_map('intval', $arrayId);
        $this->id_products = $id_products;
        $this->p = $this->getRequestValue('page_num', 1);
        $this->n = $this->getRequestValue('page_size', sizeof($arrayId));

        $this->initializeCart(Context::getContext());
        Product::initPricesComputation();

        $this->productSort();
        $result = $this->getProductList17();
        // prepare the products
        $products = $this->prepareMultipleProductsForTemplate(
            $result->getProducts()
        );
        $this->cat_products = $products;
        $this->nb_products = $result->getTotalProductsCount();

        $products = $this->transformProductList();

        $this->availableSortOders = $result->getAvailableSortOrders();
        $this->facetCollection = $result->getFacetCollection();

        $this->setBanner($products);
        $this->response = new JmProductsResponse();
        $this->response->products = $products;
    }
}

<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductLookbookService16 extends ProductsService16
{

    public function doExecute()
    {
        $arrayId = explode(",", Tools::getValue('id_products'));
        if (!$arrayId || empty($arrayId)) {
            throw new WebserviceException('Missing id_products ', 400);
        }
        $id_products = array_map('intval', $arrayId);
        $this->id_products = $id_products;
        $this->id_lang = $this->context->language->id;
        $this->id_customer = $this->context->customer->id;
        $this->p = $this->getRequestValue('page_num', 1);
        $this->n = $this->getRequestValue('page_size', sizeof($arrayId));

        $_GET['p'] = $this->p;
        $_GET['n'] = $this->n;

        $this->initializeCart(Context::getContext());
        Product::initPricesComputation();
        $this->page = $this->p;
        // Product sort must be called before assignProductList()

        $this->productSort();
        $this->assignProductList();

        $products = $this->transformProductList();

        if ($this->hook_executed) {
            $selected_filters = $this->getSelectedFilters();
            $this->getFilterBlock($selected_filters);
        }
        $this->setBanner($products);


        $this->response = new JmProductsResponse();
        $this->response->products = $products;
    }
}

<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductsAccessoriesService extends BaseService
{
    private $stock_management;
    private $id_customer;
    private $id_lang;

    /**
     * Implement business logic
     */
    public function doExecute()
    {
        $productId = $this->getRequestValue('id_product');
        $this->response = new JmProductsResponse();
        $this->id_lang = $this->context->language->id;
        $this->id_customer = $this->context->customer->id;
        $this->stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false;
        Product::initPricesComputation($this->context->customer->id);
        $product = new Product($productId);

        $productAccessories = $product->getAccessories($this->id_lang);
        if (!$productAccessories || !count($productAccessories)) {
            /**
             * PS-1167: Support related product from easycarousels module
             */
            if (Module::isInstalled('easycarousels')) {
                require_once _PS_MODULE_DIR_ . '/easycarousels/easycarousels.php';
                $easycarousels = new EasyCarousels();
                try {
                    $carouselsByHookName = $easycarousels->getAllCarousels(
                        'hook_name',
                        'displayFooterProduct',
                        true,
                        $productId,
                        null,
                        $productId,
                        'product'
                    );
                } catch (Exception $e) {
                    $carouselsByHookName = array();
                }
                if (is_array($carouselsByHookName)) {
                    foreach ($carouselsByHookName as $carouselsWrapper) {
                        if (is_array($carouselsWrapper)) {
                            foreach ($carouselsWrapper as $carouselsSorted) {
                                if (is_array($carouselsSorted)) {
                                    foreach ($carouselsSorted as $carousel) {
                                        if (is_array($carousel['items'])) {
                                            foreach ($carousel['items'] as $item) {
                                                if (isset($item[1])) {
                                                    $productAccessories[] = Product::getProductProperties($this->id_lang, $item[1]);
                                                }
                                            }
                                            break 3;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $products = $this->transformProductList($productAccessories, false);

        $db = Db::getInstance(_PS_USE_SQL_SLAVE_);
        $sql = 'SELECT id_product
                FROM ' . _DB_PREFIX_ . 'jm_product_visibility
                WHERE not_visible = 2';
        $result = $db->executeS($sql);
        $hidden_products = array();
        foreach ($result as $row) {
            $hidden_products[] = $row['id_product'];
        }

        foreach ($products as $index => &$item) {
            if (!in_array($item->id_product, $hidden_products)) {
                $productDetail = new Product($item->id_product);

                $wsCombinations = $productDetail->getWsCombinations();
                $item->has_required_options = !empty($wsCombinations);
            } else {
                unset($products[(int)$index]);
            }
        }
        $products = array_values($products);
        $this->response->products = $products;
    }


    /**
     * Transform Prestashop ProductCore -> JmProduct
     * @param bool $isManufacturerCatalog
     * @return array
     */
    protected function transformProductList($products, $isManufacturerCatalog = false)
    {
        $data = array();

        foreach ($products as $product) {
            $prod = ProductDataTransform::productList($product, $this->stock_management, $this->id_customer, $isManufacturerCatalog);
            $data[] = $prod;
        }
        return $data;
    }
}

<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductsSearchTermService extends BaseService
{

    private $products;

    private $query;

    private $limit;

    private $orderBy;

    private $orderWay;

    public function doExecute()
    {
        $original_query = Tools::getValue('query');
        $this->query = Tools::replaceAccentedChars(urldecode($original_query));
        $limit = Tools::getValue('limit');
        if (JmValidator::IsNullOrEmptyString($limit)) {
            $limit = 10;
        }
        $this->limit = $limit;

        // Init service
        $this->initializeCart(Context::getContext());
        Product::initPricesComputation();

        $products = array();
        if ($this->isV17()) {
            $products = $this->findSearchTermV17();
        } else {
            $products = $this->findSearchTermV16();
        }

        $this->response = new JmSearchTermResponse();
        $this->response->products = $products;
    }

    private function findSearchTermV16()
    {
        $orderBy = Tools::getValue('orderBy');
        if (JmValidator::IsNullOrEmptyString($orderBy)) {
            $orderBy =  Tools::getProductsOrder("by");
        }
        $this->orderBy = $orderBy;

        $orderWay = Tools::getValue('orderWay');
        if (JmValidator::IsNullOrEmptyString($orderWay)) {
            $orderWay = Tools::getProductsOrder("way");
        }
        $this->orderWay = $orderWay;
        $searchResults = Search::find($this->context->language->id, $this->query, 1, $this->limit, $this->orderBy, $this->orderWay, true);
        $products = $this->transformSearchTermV16($searchResults);

        return $products;
    }

    private function findSearchTermV17()
    {
        $orderBy = Tools::getValue('orderBy');
        if (JmValidator::IsNullOrEmptyString($orderBy)) {
            $orderBy = Tools::getProductsOrder('by');
        }
        $this->orderBy = $orderBy;
        $orderWay = Tools::getValue('orderWay');
        if (JmValidator::IsNullOrEmptyString($orderWay)) {
            $orderWay = Tools::getProductsOrder('way');
        }
        $this->orderWay = $orderWay;
        $searchResults = Search::find($this->context->language->id, $this->query, 1, $this->limit, $this->orderBy, $this->orderWay, false);
        $products = $this->transformSearchTermV17($searchResults);
        return $products;
    }

    private function transformSearchTermV16($searchResults)
    {
        $data = array();
        if (!JmValidator::IsArrayEmpty($searchResults) && is_array($searchResults)) {
            foreach ($searchResults as $product) {
                $id_product = $product['id_product'];
                if ($this->isHiddenProduct($id_product)) {
                    continue;
                }
                $prod = new JmSearchTerm();
                $prod->id_product = $id_product;
                $prod->title = $product['pname'];
                $data[] = $prod;
            }
        }
        return $data;
    }

    private function transformSearchTermV17($searchResults)
    {
        $data = array();
        if (!JmValidator::IsArrayEmpty($searchResults['result']) && is_array($searchResults['result'])) {
            foreach ($searchResults['result'] as $product) {
                $id_product = $product['id_product'];
                if ($this->isHiddenProduct($id_product)) {
                    continue;
                }

                $prod = new JmSearchTerm();
                $prod->id_product = $id_product;
                $prod->title = $product['name'];
                $data[] = $prod;
            }
        }
        return $data;
    }

    private function isHiddenProduct($id_product)
    {
        // Check if this product is hidden
        $status = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT(`not_visible`)
                FROM `' . _DB_PREFIX_ . 'jm_product_visibility`
                WHERE ' . _DB_PREFIX_ . 'jm_product_visibility.id_product = '.(int)$id_product.'
                AND ' . _DB_PREFIX_ . 'jm_product_visibility.not_visible = 2
                ');

        if (!JmValidator::IsArrayEmpty($status)) {
            return true;
        }

        // Check if this product is a hidden pack
        $hidden_pack_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                SELECT DISTINCT(`id_product_pack`)
                FROM `' . _DB_PREFIX_ . 'pack`
                LEFT JOIN ' . _DB_PREFIX_ . 'jm_product_visibility ON ' . _DB_PREFIX_ . 'jm_product_visibility.id_product = ' . _DB_PREFIX_ . 'pack.id_product_item
                WHERE ' . _DB_PREFIX_ . 'jm_product_visibility.not_visible = 2
                AND ' . _DB_PREFIX_ . 'pack.id_product_pack = '.(int)$id_product.'
                ');

        if (!JmValidator::IsArrayEmpty($hidden_pack_product)) {
            return true;
        }
        return false;
    }
}

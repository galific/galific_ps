<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductSortOptionsService extends BaseService
{
    public function doExecute()
    {
        $product_service = new ProductsService16();
        $sort_by = $product_service->localizeOrderByValues();
        $response = array();

        $default_order_by = $product_service->order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')];
        foreach ($sort_by as $key => $value) {
            $jmOption = array();
            $jmOption['sortKey'] = $key;
            $jmOption['sortDisplay'] = $value;
            if ($key === $default_order_by) {
                $jmOption['defaultSort'] = true;
            } else {
                $jmOption['defaultSort'] = false;
            }
            $response[] = $jmOption;
        }
        $this->response = $response;
    }
}

<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

abstract class ProductsService extends BaseService
{

    /** @var Category Current category object */
    protected $category;

    /** @var array Controller errors */
    public $errors = array();

    /** @var string ORDER BY field */
    public $orderBy;

    /** @var string Order way string ('ASC', 'DESC') */
    public $orderWay;

    public $order_by_values = array(
        0 => 'name',
        1 => 'price',
        2 => 'date_add',
        3 => 'date_upd',
        4 => 'position',
        5 => 'manufacturer_name',
        6 => 'quantity',
        7 =>'reference'
    );
    protected $order_way_values = array(0 => 'asc', 1 => 'desc');

    /** var boolean - Display quantity order if stock management disabled */
    public $stock_management;

    /** @var int Current page number */
    public $p;

    /** @var int Items (products) per page */
    public $n;

    /** @var int Number of products in the current page. */
    protected $nbProducts;

    /** @var array Products to be displayed in the current page . */
    protected $cat_products;

    /** @var object navigation filter to be displayed */
    protected $nav_filter_block;

    protected $id_lang;

    protected $id_shop;

    protected $image_names = array();

    protected $id_customer;

    public function doExecute()
    {
        $this->image_names = array(
            ImageType::getFormatedName('cart'),
            ImageType::getFormatedName('small'),
            ImageType::getFormatedName('medium'),
            ImageType::getFormatedName('large'),
            ImageType::getFormatedName('thickbox')
        );
        // Will be implemented for each Prestashop version
        echo ("Inside product service");
    }

    abstract protected function transformNavigationLayered();

    /**
     * Transform Prestashop ProductCore -> JmProduct
     * @param bool $isManufacturerCatalog
     * @return array
     */
    protected function transformProductList($isManufacturerCatalog = false)
    {
        $data = array();

        foreach ($this->cat_products as $product) {
            $prod = ProductDataTransform::productList($product, $this->stock_management, $this->id_customer, $isManufacturerCatalog);
            $stockAvailable = new StockAvailable(StockAvailableCore::getStockAvailableIdByProductId($prod->id_product));
            $prod->out_of_stock = ProductCore::isAvailableWhenOutOfStock($stockAvailable->out_of_stock);
            $prod->allow_oosp = ProductCore::isAvailableWhenOutOfStock($stockAvailable->out_of_stock);
            $prod->has_required_options = (int)ProductCore::getDefaultAttribute($prod->id_product) ? true : false;
            $data[] = $prod;
        }

        return $data;
    }

    /**
     * Assigns product list page sorting variables
     */
    public function productSort()
    {
        // $this->orderBy = Tools::getProductsOrder('by', Tools::getValue('orderby'));
        // $this->orderWay = Tools::getProductsOrder('way', Tools::getValue('orderway'));
        // 'orderbydefault' => Tools::getProductsOrder('by'),
        // 'orderwayposition' => Tools::getProductsOrder('way'), // Deprecated: orderwayposition
        // 'orderwaydefault' => Tools::getProductsOrder('way'),

        $this->stock_management = Configuration::get('PS_STOCK_MANAGEMENT') ? true : false;
        // no display quantity order if stock management disabled
        $this->orderBy = Tools::strtolower(Tools::getValue(
            'orderby',
            $this->order_by_values[(int)Configuration::get('PS_PRODUCTS_ORDER_BY')]
        ));
        $this->orderWay = Tools::strtolower(Tools::getValue(
            'orderway',
            $this->order_way_values[(int)Configuration::get('PS_PRODUCTS_ORDER_WAY')]
        ));

        if (!in_array($this->orderBy, $this->order_by_values)) {
            $this->orderBy =  Tools::getProductsOrder("by");
        }

        if (!in_array($this->orderWay, $this->order_way_values)) {
            $this->orderWay =  Tools::getProductsOrder("way");
        }
    }
}

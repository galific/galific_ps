<?php
/**
 * Class Products_16
 * @author Jmango
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Products16 extends ModuleFrontController
{
    /** string Internal controller name */
    public $php_self = '';

    /** @var Category Current category object */
    protected $category;

    /** @var bool If set to false, customer cannot view the current category. */
    public $customer_access = true;

    /** @var int Number of products in the current page. */
    protected $nbProducts;

    /** @var array Products to be displayed in the current page . */
    protected $cat_products;

    public $module_name;

    public function initHeader()
    {
        parent::initHeader();
        $this->context->smarty->assign('content_only', 1);
    }


    public function initContent()
    {
        //parent::initContent();
        $this->display_header = false;
        $this->display_footer = false;
        $this->display_header_javascript = false;
        $this->context->smarty->assign(array(
            'module_name' => $this->module_name,
        ));
        header('Content-Type: application/json');
        // Get category ID
        $id_category = (int)Tools::getValue('id_category');
        if (!$id_category || !Validate::isUnsignedId($id_category)) {
            $this->errors[] = Tools::displayError('Missing category ID');
        }

        // Instantiate category
        $this->category = new Category($id_category, $this->context->language->id);

        // Check if the category is active and return 404 error if is disable.
        if (!$this->category->active
            || !Validate::isLoadedObject($this->category)
            || !$this->category->inShop()
            || !$this->category->isAssociatedToShop()
            || in_array($this->category
                ->id, array(Configuration::get('PS_HOME_CATEGORY'), Configuration::get('PS_ROOT_CATEGORY')))) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Category not found');
        } else {
            // Check if category can be accessible by current customer and return 403 if not
            if (!$this->category->checkAccess($this->context->customer->id)) {
                header('HTTP/1.1 403 Forbidden');
                header('Status: 403 Forbidden');
                $this->errors[] = Tools::displayError('You do not have access to this category.');
                $this->customer_access = false;
            }
        }

        // Product sort must be called before assignProductList()
        $this->productSort();
        $this->assignProductList();

        $this->context->smarty->assign(array(
            'category'             => $this->category,
            'description_short'    => Tools::truncateString($this->category->description, 350),
            'products'             => (isset($this->cat_products) && $this->cat_products) ? $this->cat_products : null,
            'id_category'          => (int)$this->category->id,
            'id_category_parent'   => (int)$this->category->id_parent,
            'return_category_name' => Tools::safeOutput($this->category->name),
            'path'                 => Tools::getPath($this->category->id),
            'add_prod_display'     => Configuration::get('PS_ATTRIBUTE_CATEGORY_DISPLAY'),
            'categorySize'         => Image::getSize(ImageType::getFormatedName('category')),
            'mediumSize'           => Image::getSize(ImageType::getFormatedName('medium')),
            'thumbSceneSize'       => Image::getSize(ImageType::getFormatedName('m_scene')),
            'homeSize'             => Image::getSize(ImageType::getFormatedName('home')),
            'allow_oosp'           => (int)Configuration::get('PS_ORDER_OUT_OF_STOCK'),
            'comparator_max_item'  => (int)Configuration::get('PS_COMPARATOR_MAX_ITEM'),
            'suppliers'            => Supplier::getSuppliers()
        ));
        $json = json_encode($this->cat_products);
        echo $json;
    }


    /**
     * Assigns product list template variables
     */
    public function assignProductList()
    {
        $hook_executed = false;
        Hook::exec('actionProductListOverride', array(
            'nbProducts'   => &$this->nbProducts,
            'catProducts'  => &$this->cat_products,
            'hookExecuted' => &$hook_executed,
        ));

        // The hook was not executed, standard working
        if (!$hook_executed) {
            $this->context->smarty->assign('categoryNameComplement', '');
            $this->nbProducts = $this->category->getProducts(null, null, null, $this->orderBy, $this->orderWay, true);
            $this->pagination((int)$this->nbProducts); // Pagination must be call after "getProducts"
            $this->cat_products = $this->category->getProducts(
                $this->context->language->id,
                (int)$this->p,
                (int)$this->n,
                $this->orderBy,
                $this->orderWay
            );
        } else {// Hook executed, use the override
            // Pagination must be call after "getProducts"
            $this->pagination($this->nbProducts);
        }

        $this->addColorsToProductList($this->cat_products);

        Hook::exec('actionProductListModifier', array(
            'nb_products'  => &$this->nbProducts,
            'cat_products' => &$this->cat_products,
        ));

        foreach ($this->cat_products as &$product) {
            if (isset($product['id_product_attribute'])
                && $product['id_product_attribute']
                && isset($product['product_attribute_minimal_quantity'])) {
                $product['minimal_quantity'] = $product['product_attribute_minimal_quantity'];
            }

            $id_images = Product::getCover($product->id);
            // get Image by id
            if (sizeof($id_images) > 0) {
                $image = new Image($id_images['id_image']);
                print_r($id_images['id_image']);
                
                // get image full URL
                $image_url = _PS_BASE_URL_._THEME_PROD_DIR_.$image->getExistingImgPath().".jpg";
                $product[url_image]= $image_url;
            }
        }

        $this->context->smarty->assign('nb_products', $this->nbProducts);
    }
}

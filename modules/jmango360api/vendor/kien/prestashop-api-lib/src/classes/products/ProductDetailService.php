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
//require_once _PS_MODULE_DIR_ . "jmango360api/classes/productReviews/ProductReviewService.php";
use PrestaShop\PrestaShop\Core\Addon\Module\ModuleManagerBuilder;

class ProductDetailService extends BaseService
{

    /** @var Product */
    protected $product;

    /** @var Category */
    protected $category;

    protected $errors = array();

    private $id_lang;

    private $id_shop;

    private $id_customer;

    private $selected_options;

    private $id_currency;

    private $id_product_attribute;

    private $id_product;

    public function doExecute()
    {
        //TODO: update implementation follow base service

        $response = new \JmProductDetailResponse();

        $this->id_lang = Tools::getValue('id_lang');

        if ($this->id_lang) {
            Context::getContext()->language->id = $this->id_lang;
        } else {
            $this->errors[] = Tools::displayError('Invalid input id_lang parameter');
        }

        $this->id_shop = Tools::getValue('id_shop');

        if ($this->id_shop) {
            Context::getContext()->shop->id = $this->id_shop;
        } else {
            $this->errors[] = Tools::displayError('Invalid input id_shop parameter');
        }

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

        if ($id_product = (int)Tools::getValue('id_product')) {
            //check product does exists.
            if ($this->checkExistsIdProduct($id_product)) {
                //check product has enable show on mobile.
                if ($this->checkEnableShowProduct($id_product)) {
                    $this->product = new Product($id_product, true, $this->id_lang, $this->id_shop, $this->context);

                    // PS-686 : [Prestashop] The price is incorrect in product detail ( in case: Login by user and happens both on Prestashop 16, Prestashop 17 )
                    if (version_compare(_PS_VERSION_, '1.7', '>=')) {
                        $present = new \PrestaShop\PrestaShop\Adapter\ObjectPresenter();
                        $product = $present->present($this->product);
                        $product['id_product'] = (int)$product['id'];
                        $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);
                        if (isset($product_full) && isset($product_full['price_without_reduction'])) {
                            $this->product->base_price = $product_full['price_without_reduction'];
                        }
                    } else {
                        $product = array();
                        $product['id_product'] = $this->product->id;
                        $product_full = Product::getProductProperties($this->context->language->id, $product, $this->context);
                        if (isset($product_full) && isset($product_full['price_without_reduction'])) {
                            $this->product->base_price = $product_full['price_without_reduction'];
                        }
                    }
                    $this->id_product = $id_product;
                } else {
                    $this->errors[] = Tools::displayError('Product that this message is linked to no longer exists');
                }
            } else {
                $this->errors[] = Tools::displayError('Product does not exists');
            }
        } else {
            $this->errors[] = Tools::displayError('Invalid input id_product parameter');
        }

        $this->id_currency = Tools::getValue('id_currency');
        $this->selected_options = Tools::getValue('selected_options');
        if ($this->selected_options) {
            $option_array = $this->transformSelectedOptions();
            $this->id_product_attribute = SimpleCartService::getIdProductAttributesByIdAttributes($this->id_product, $option_array);
        } else {
            $this->id_product_attribute = null;
        }

        if ($this->errors) {
            header('HTTP/1.1 400 Bad Request');
            header('Status: 400 Bad Request');

            $response->errors = $this->errors;
            echo(json_encode($response, JSON_PRETTY_PRINT));
            exit();
        }

        if (!Validate::isLoadedObject($this->product)) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('Product not found');
        }

        /*
            * If the product is not associated to the shop or not active
            *  => 404 "Product is no longer available"
            */
        if (!$this->product->isAssociatedToShop() || !$this->product->active) {
            header('HTTP/1.1 404 Not Found');
            header('Status: 404 Not Found');
            $this->errors[] = Tools::displayError('This product is no longer available.');
        } elseif (!$this->product->checkAccess(isset($this->context->customer->id) && $this->context->customer->id ? (int)$this->context->customer->id : 0)) {
            header('HTTP/1.1 403 Forbidden');
            header('Status: 403 Forbidden');
            $this->errors[] = Tools::displayError('You do not have access to this product.');
        }

        if ($this->errors) {
            // Need to echo error
        }

        $jm_product_detail = new JmProductDetail();

        if (Pack::isPack((int)$this->product->id) && !Pack::isInStock((int)$this->product->id)) {
            $this->product->quantity = 0;
        }
        $this->product->description = $this->transformDescriptionWithImg($this->product->description);
        $jm_product_detail = \ProductDataTransform::productDetails($this->product);

        $customization_fields =
            $this->product->customizable ? $this->product->getCustomizationFields($this->id_lang) : false;
        $jm_product_detail->customization_fields = ProductDataTransform::customizationFields($customization_fields);

        // Assign template vars related to the price and tax
        $this->assignPriceAndTax($jm_product_detail);

        // Assign product images as gallery
        $this->assignImages($jm_product_detail);

        // Assign attribute groups
        $this->assignAttributesGroups($jm_product_detail);

        // Pack management
        $pack_items =
            Pack::isPack($this->product->id) ? Pack::getItemTable($this->product->id, $this->id_lang, true) : array();
        foreach ($pack_items as &$item) {
            // get Image by id
            if (sizeof($item['id_image']) > 0) {
                $protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
                $link = new LinkCore();
                $imageUrl =
                    $link->getImageLink($item['link_rewrite'], $item['id_image'], $this->isV17() ? ImageType::getFormattedName('home') : ImageType::getFormatedName('home'));
                $item['imageUrl'] = $protocol_link.$imageUrl;
                $stockAvailable =
                    new StockAvailable(StockAvailableCore::getStockAvailableIdByProductId($item['id_product']));
                $item['out_of_stock']= ProductCore::isAvailableWhenOutOfStock($stockAvailable->out_of_stock);
            }
        }
        $jm_product_detail->out_of_stock = ProductCore::isAvailableWhenOutOfStock($jm_product_detail->out_of_stock);
        $jm_product_detail->pack_items = $pack_items;

        // Indicate existing products as accessories for this product
        $accessories = $this->product->getAccessories($this->id_lang);
        $jm_product_detail->accessories = $accessories;
        $feature = $this->product->getFrontFeatures($this->id_lang);
        $jm_product_detail->features = $feature;
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $this->setBanner17($jm_product_detail);
        } else {
            $this->setBanner16($jm_product_detail);
        }
        $jm_product_detail->product_manufacturer = new Manufacturer(
            (int)$this->product->id_manufacturer,
            $this->id_lang
        );
        $jm_product_detail->product_url = $this->context->link->getProductLink($this->product);
        $jm_product_detail->image = $this->getCoverImage($id_product);

        // update Display_Price.
//        if ($this->updatePrice($jm_product_detail)) {
//            $jm_product_detail->price = $this->updatePrice($jm_product_detail);
//        }

        //Assign verified review to product details
        $jm_product_detail->verified_review = null;
        if ($this->isV17()) {
            $moduleManagerBuilder = ModuleManagerBuilder::getInstance();
            $moduleManager = $moduleManagerBuilder->build();
            if ($moduleManager->isInstalled('netreviews')
                && Module::getInstanceByName('netreviews')->active) {
                $jm_product_detail->verified_review = $this->assignVerifiedReview();
            }
        }
        $this->response = $jm_product_detail;
    }

    public function getProductQuantityDiscount($id_lang, $id_shop, $id_product, $id_customer, $id_currency, $id_product_attribute, $context)
    {
        $this->id_lang = $id_lang;
        $this->id_shop = $id_shop;
        $this->id_customer = $id_customer;
        $this->id_product = $id_product;
        $this->id_currency = $id_currency;
        $this->id_product_attribute = $id_product_attribute;
        $this->context = $context;
        $this->context->customer = new Customer($id_customer);
        $this->product = new Product($id_product, true, $this->id_lang, $this->id_shop, $this->context);
        $quantity_discounts = $this->getQuantityDiscount();
        return ProductDataTransform::quantityDiscounts($quantity_discounts);
    }

    protected function transformDescriptionWithImg($desc)
    {
        $reg = '/\[img\-([0-9]+)\-(left|right)\-([a-zA-Z0-9-_]+)\]/';
        while (preg_match($reg, $desc, $matches)) {
            $link_lmg = $this->context->link->getImageLink(
                $this->product->link_rewrite,
                $this->product->id . '-' . $matches[1],
                $matches[3]
            );
            $class = $matches[2] == 'left' ? 'class="imageFloatLeft"' : 'class="imageFloatRight"';
            $html_img = '<img src="' . $link_lmg . '" alt="" ' . $class . '/>';
            $desc = str_replace($matches[0], $html_img, $desc);
        }
        return $desc;
    }

    /**
     * Assign template vars related to images
     */
    protected function assignImages(JmProductDetail &$jm_product_detail)
    {
        $images = $this->product->getImages((int)$this->id_lang);
        if (empty($images)) {
            $images[] = array(
                'id_image' => null
            );
        }
        $product_gallery = array();

        foreach ($images as $k => $image) {
            $id_image = (int)$image['id_image'];
            $images = ProductDataTransform::productImages($id_image);
            $product_gallery[] = $images;
        }

        $jm_product_detail->gallery = $product_gallery;
    }

    /**
     * Assign price and tax to the template
     */
    protected function assignPriceAndTax(JmProductDetail &$jm_product_detail)
    {
        $id_customer = (isset($this->context->customer) ? (int)$this->context->customer->id : 0);
        $id_group = (int)Group::getCurrent()->id;
        $id_country = $id_customer ? (int)Customer::getCurrentCountry($id_customer) : (int)Tools::getCountry();
        $group_reduction = GroupReduction::getValueForProduct($this->product->id, $id_group);

        if ($group_reduction === false) {
            $group_reduction = Group::getReduction((int)$this->context->cookie->id_customer) / 100;
        }

        // Tax
        $tax = (float)$this->product->getTaxesRate(
            new Address((int)$this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')})
        );
        $jm_product_detail->tax_rate = $tax;

        $product_price_with_tax = Product::getPriceStatic($this->product->id, true, null, 2);
        if (Product::$_taxCalculationMethod == PS_TAX_INC) {
            $product_price_with_tax = Tools::ps_round($product_price_with_tax, 2);
        }
        $product_price_without_eco_tax = (float)$product_price_with_tax - $this->product->ecotax;

        $ecotax_rate =
            (float)Tax::getProductEcotaxRate($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        if (Product::$_taxCalculationMethod == PS_TAX_INC && (int)Configuration::get('PS_TAX')) {
            $ecotax_tax_amount = Tools::ps_round($this->product->ecotax * (1 + $ecotax_rate / 100), 2);
        } else {
            $ecotax_tax_amount = Tools::ps_round($this->product->ecotax, 2);
        }

        $id_currency = (int)$this->context->cookie->id_currency;
        $id_product = (int)$this->product->id;
        $id_shop = $this->context->shop->id;

        $quantity_discounts = SpecificPrice::getQuantityDiscounts(
            $id_product,
            $id_shop,
            $id_currency,
            $id_country,
            $id_group,
            null,
            true,
            (int)$this->context->customer->id
        );
        foreach ($quantity_discounts as &$quantity_discount) {
            if (!isset($quantity_discount['base_price'])) {
                $quantity_discount['base_price'] = 0;
            }
            if ($quantity_discount['id_product_attribute']) {
                $quantity_discount['base_price'] = $this->product->getPrice(
                    Product::$_taxCalculationMethod == PS_TAX_INC,
                    $quantity_discount['id_product_attribute']
                );

                $combination = new Combination((int)$quantity_discount['id_product_attribute']);
                $attributes = $combination->getAttributesName((int)$this->context->language->id);
                foreach ($attributes as $attribute) {
                    $quantity_discount['attributes'] = $attribute['name'] . ' - ';
                }
                $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
            }
            if ((int)$quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                $quantity_discount['reduction'] =
                    Tools::convertPriceFull(
                        $quantity_discount['reduction'],
                        null,
                        Context::getContext()->currency
                    );
            }
        }

        $address = new Address($this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

        // Specific prices
        $quantity_discounts = $this->getQuantityDiscount();
        $jm_product_detail->quantity_discounts = ProductDataTransform::quantityDiscounts($quantity_discounts);
        $jm_product_detail->ecotax_tax_inc = $ecotax_tax_amount;
        $jm_product_detail->ecotax_tax_exc = Tools::ps_round($this->product->ecotax, 2);
        $jm_product_detail->ecotax_tax_rate = $ecotax_rate;
        $jm_product_detail->product_price_without_eco_tax = (float)$product_price_without_eco_tax;
        $jm_product_detail->group_reduction = $group_reduction;
        $jm_product_detail->no_tax = Tax::excludeTaxeOption() || !$this->product->getTaxesRate($address);
        $jm_product_detail->ecotax =(!count($this->errors)
        && $this->product->ecotax > 0 ? Tools::convertPrice((float)$this->product->ecotax) : 0);
        $jm_product_detail->tax_enabled = Configuration::get('PS_TAX') && !Configuration::get('AEUC_LABEL_TAX_INC_EXC');
        if (Tools::getValue('id_customer')!= null) {
            $jm_product_detail->customer_group_without_tax =
                (strcmp(
                    Group::getPriceDisplayMethod(Customer::getDefaultGroupId(Tools::getValue('id_customer'))),
                    "1"
                ) == 0 ? true : false);
        } else {
            $jm_product_detail->customer_group_without_tax =
                (strcmp(Group::getPriceDisplayMethod("1"), "1") == 0 ? true : false);
        }
        if ($jm_product_detail->customer_group_without_tax) {
            $jm_product_detail->price = Product::getPriceStatic(
                (int)$this->product->id,
                false,
                $this->id_product_attribute,
                2,
                null,
                false,
                true,
                1,
                false,
                $this->id_customer,
                null,
                null
            );
            $jm_product_detail->base_price = $this->product->getPriceWithoutReduct(true, $this->id_product_attribute, 2);
        } else {
            $jm_product_detail->price = Product::getPriceStatic(
                (int)$this->product->id,
                true,
                $this->id_product_attribute,
                2,
                null,
                false,
                true,
                1,
                false,
                $this->id_customer,
                null,
                null
            );
            $jm_product_detail->base_price = $this->product->getPriceWithoutReduct(false, $this->id_product_attribute, 2);
        }
    }

    protected function setBanner16(&$prod)
    {
        $temp = array();
        $prod->banner_info = array();
        $temp['code']='on_sale';
        $temp['value']=$prod->on_sale;
        $temp['label']=$this->getTranslation(
            'Sale!',
            'product-list'
        );
        $prod->banner_info[]=$temp;
        $temp['code']='new';
        $temp['value']=$prod->new?1:0;
        $temp['label']=$this->getTranslation(
            'New',
            'product-list'
        );
        $prod->banner_info[]=$temp;
    }

    protected function setBanner17(&$prod)
    {
        $temp = array();
        $prod->banner_info = array();
        $show_price = $prod->show_price;
        $temp['code']='pack';
        $temp['value']=Pack::isPack((int)$this->product->id)?'1':'0';
        $temp['label']=$this->context->getTranslator()->trans('Pack', array(), 'Shop.Theme.Catalog');
        $prod->banner_info[]=$temp;

        $temp['code']='on_sale';
        $temp['value']=$show_price?$prod->on_sale:'0';
        $temp['label']=$this->context->getTranslator()->trans('On sale!', array(), 'Shop.Theme.Catalog');
        $prod->banner_info[]=$temp;

        $temp['code']='new';
        $temp['value']=$prod->new?1:0;
        $temp['label']=$this->context->getTranslator()->trans('New', array(), 'Shop.Theme.Catalog');
        $prod->banner_info[]=$temp;
    }

    public function getQuantityDiscount()
    {
        $id_country = $this->id_customer ? (int) Customer::getCurrentCountry($this->id_customer) : (int) Tools::getCountry();
        $id_group = (int) Group::getCurrent()->id;
        $tax = (float) $this->product->getTaxesRate(new Address((int) $this->context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')}));

        $quantity_discounts = SpecificPrice::getQuantityDiscounts($this->id_product, $this->id_shop, $this->id_currency, $id_country, $id_group, $this->id_product_attribute, false, (int) $this->context->customer->id);
        $product_price = $this->product->getPrice(Product::getTaxCalculationMethod((int)$this->id_customer) == PS_TAX_INC, false);
        //prestashop 1.7's quantity discount calculation
        if ($this->isV17()) {
            foreach ($quantity_discounts as &$quantity_discount) {
                if ($quantity_discount['id_product_attribute']) {
                    $combination = new Combination((int) $quantity_discount['id_product_attribute']);
                    $attributes = $combination->getAttributesName((int) $this->context->language->id);
                    foreach ($attributes as $attribute) {
                        $quantity_discount['attributes'] = $attribute['name'].' - ';
                    }
                    $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
                }
                if ((int) $quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                    $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
                }
            }
            return $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float)$tax, $this->product->ecotax);
        } //prestashop 1.6's quantity discount calculation
        else {
            $discount_attribute_ids = array();
            $has_attribute_discount = false;
            foreach ($quantity_discounts as $quantity_discount) {
                $discount_attribute_ids[] = (int)$quantity_discount['id_product_attribute'];
            }
            if (in_array((int)$this->id_product_attribute, $discount_attribute_ids) && (int)$this->id_product_attribute !== 0) {
                $has_attribute_discount = true;
            }
            foreach ($quantity_discounts as $key => &$quantity_discount) {
                //PS-727: prestashop 16 only return quantity price of selected attribute.
                if ($has_attribute_discount) {
                    if ((int)$quantity_discount['id_product_attribute'] !== (int)$this->id_product_attribute) {
                        unset($quantity_discounts[$key]);
                        continue;
                    }
                }
                if (!isset($quantity_discount['base_price'])) {
                    $quantity_discount['base_price'] = 0;
                }
                if ($quantity_discount['id_product_attribute']) {
                    $quantity_discount['base_price'] = $this->product->getPrice(Product::getTaxCalculationMethod((int)$this->id_customer) == PS_TAX_INC, $quantity_discount['id_product_attribute']);

                    $combination = new Combination((int)$quantity_discount['id_product_attribute']);
                    $attributes = $combination->getAttributesName((int)$this->context->language->id);
                    foreach ($attributes as $attribute) {
                        $quantity_discount['attributes'] = $attribute['name'].' - ';
                    }
                    $quantity_discount['attributes'] = rtrim($quantity_discount['attributes'], ' - ');
                }
                if ((int)$quantity_discount['id_currency'] == 0 && $quantity_discount['reduction_type'] == 'amount') {
                    $quantity_discount['reduction'] = Tools::convertPriceFull($quantity_discount['reduction'], null, Context::getContext()->currency);
                }
            }
            $quantity_discounts = array_values($quantity_discounts);
            return $this->formatQuantityDiscounts($quantity_discounts, $product_price, (float)$tax, $this->product->ecotax);
        }
    }

    protected function formatQuantityDiscounts($specific_prices, $price, $tax_rate, $ecotax_amount)
    {
        foreach ($specific_prices as $key => &$row) {
            $row['quantity'] = &$row['from_quantity'];
            if ($row['price'] >= 0) {
                // The price may be directly set

                /** @var float $currentPriceDefaultCurrency current price with taxes in default currency */
                $currentPriceDefaultCurrency = (!$row['reduction_tax'] ? $row['price'] : $row['price'] * (1 + $tax_rate / 100)) + (float) $ecotax_amount;
                // Since this price is set in default currency,
                // we need to convert it into current currency
                $row['id_currency'];
                $currentPriceCurrentCurrency = Tools::convertPrice($currentPriceDefaultCurrency, $this->context->currency, true, $this->context);

                if ($row['reduction_type'] == 'amount') {
                    $currentPriceCurrentCurrency -= ($row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100));
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                } else {
                    $currentPriceCurrentCurrency *= 1 - $row['reduction'];
                }
                $row['real_value'] = $price > 0 ? $price - $currentPriceCurrentCurrency : $currentPriceCurrentCurrency;
                $discountPrice = $price - $row['real_value'];

                if (Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')) {
                    if ($row['reduction_tax'] == 0 && !$row['price']) {
                        $row['discount'] = $price - ($price * $row['reduction_with_tax']);
                    } else {
                        $row['discount'] = $price - $row['real_value'];
                    }
                } else {
                    $row['discount'] = $row['real_value'];
                }
            } else {
                if ($row['reduction_type'] == 'amount') {
                    if (Product::getTaxCalculationMethod((int) $this->id_customer) == PS_TAX_INC) {
                        $row['real_value'] = $row['reduction_tax'] == 1 ? $row['reduction'] : $row['reduction'] * (1 + $tax_rate / 100);
                    } else {
                        $row['real_value'] = $row['reduction_tax'] == 0 ? $row['reduction'] : $row['reduction'] / (1 + $tax_rate / 100);
                    }
                    $row['reduction_with_tax'] = $row['reduction_tax'] ? $row['reduction'] : $row['reduction'] +  ($row['reduction'] * $tax_rate) / 100;
                    $discountPrice = $price - $row['real_value'];
                    if (Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')) {
                        if ($row['reduction_tax'] == 0 && !$row['price']) {
                            $row['discount'] = $price - ($price * $row['reduction_with_tax']);
                        } else {
                            $row['discount'] = $price - $row['real_value'];
                        }
                    } else {
                        $row['discount'] = $row['real_value'];
                    }
                } else {
                    $row['real_value'] = $row['reduction'] * 100;
                    $discountPrice = $price - $price * $row['reduction'];
                    if (Configuration::get('PS_DISPLAY_DISCOUNT_PRICE')) {
                        if ($row['reduction_tax'] == 0) {
                            $row['discount'] = $price - ($price * $row['reduction_with_tax']);
                        } else {
                            $row['discount'] = $price - ($price * $row['reduction']);
                        }
                    } else {
                        $row['discount'] = $row['real_value'].'%';
                    }
                }
            }

            $row['save'] = (($price * $row['quantity']) - ($discountPrice * $row['quantity']));
            $row['nextQuantity'] = (isset($specific_prices[$key + 1]) ? (int) $specific_prices[$key + 1]['from_quantity'] : -1);

            $quantity_price = $this->getProductProperties($row['quantity'], (int) $row['id_product_attribute']);
            if (Product::getTaxCalculationMethod((int) $this->id_customer) == PS_TAX_INC) {
                $row['discounted_price'] = $quantity_price['price'];
            } else {
                $row['discounted_price'] = $quantity_price['price_tax_exc'];
            }
        }

        return $specific_prices;
    }

    public function getProductProperties($quantity, $id_product_attribute)
    {
        $row = array();
        $row['price_tax_exc'] = Product::getPriceStatic(
            (int)$this->id_product,
            false,
            $id_product_attribute,
            (Product::getTaxCalculationMethod((int) $this->id_customer) == PS_TAX_EXC ? 2 : 6),
            null,
            false,
            true,
            $quantity
        );

        if (Product::getTaxCalculationMethod((int) $this->id_customer) == PS_TAX_EXC) {
            $row['price_tax_exc'] = Tools::ps_round($row['price_tax_exc'], 2);
            $row['price'] = Product::getPriceStatic(
                (int)$this->id_product,
                true,
                $id_product_attribute,
                6,
                null,
                false,
                true,
                $quantity
            );
            $row['price_without_reduction'] = Product::getPriceStatic(
                (int)$this->id_product,
                false,
                $id_product_attribute,
                2,
                null,
                false,
                false,
                $quantity
            );
        } else {
            $row['price'] = Tools::ps_round(
                Product::getPriceStatic(
                    (int)$this->id_product,
                    true,
                    $id_product_attribute,
                    6,
                    null,
                    false,
                    true,
                    $quantity
                ),
                (int) Configuration::get('PS_PRICE_DISPLAY_PRECISION')
            );
            $row['price_without_reduction'] = Product::getPriceStatic(
                (int)$this->id_product,
                true,
                $id_product_attribute,
                6,
                null,
                false,
                false,
                $quantity
            );
        }
        return $row;
    }

    public function transformSelectedOptions()
    {
        $optionArray = array();
        $attributes = explode('/', ltrim($this->selected_options, '/'));
        if (!empty($attributes)) {
            // For each options
            foreach ($attributes as $attr) {
                $parameters = explode('-', $attr);
                $attribute_name = $parameters[0];
                $attribute_value = $parameters[1];
                $optionArray[$attribute_name] = $attribute_value;
            }
        }
        return $optionArray;
    }

    /**
     * Assign template vars related to attribute groups and colors
     */
    protected function assignAttributesGroups(JmProductDetail &$jm_product_detail)
    {
        $colors = array();
        $groups = array();
        $combinations = array();

        // @todo (RM) should only get groups and not all declination ?
        $attributes_groups = $this->product->getAttributesGroups($this->id_lang);
        if (is_array($attributes_groups) && $attributes_groups) {
            $combination_images = $this->product->getCombinationImages($this->id_lang);
            $combination_prices_set = array();
            foreach ($attributes_groups as $k => $row) {
                // Color management
                if (isset($row['is_color_group'])
                    && $row['is_color_group']
                    && (isset($row['attribute_color'])
                        && $row['attribute_color'])
                    || (file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int)$row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'id_group' => $row['id_attribute_group'],
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }

                $groups[$row['id_attribute_group']]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int)$row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $groups[$row['id_attribute_group']]
                ['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]
                ['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]
                ['attributes'][] = (int)$row['id_attribute'];
                $combinations[$row['id_product_attribute']]
                ['price'] = (float)Tools::convertPriceFull($row['price'], null, Context::getContext()->currency, false);

                // Call getPriceStatic in order to set $combination_specific_price
                $combination_specific_price = null;
                if (!isset($combination_prices_set[(int)$row['id_product_attribute']])) {
                    Product::getPriceStatic(
                        (int)$this->product->id,
                        false,
                        $row['id_product_attribute'],
                        2,
                        null,
                        false,
                        false,
                        1,
                        false,
                        null,
                        null,
                        null,
                        $combination_specific_price
                    );
                    $combination_prices_set[(int)$row['id_product_attribute']] = true;
                    $combinations[$row['id_product_attribute']]['specific_price'] = $combination_specific_price;
                }
                $combinations[$row['id_product_attribute']]['ecotax'] = (float)$row['ecotax'];
                $combinations[$row['id_product_attribute']]['weight'] = (float)$row['weight'];
                $combinations[$row['id_product_attribute']]['quantity'] = (int)$row['quantity'];
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'];
                $combinations[$row['id_product_attribute']]
                ['unit_impact'] = Tools::convertPriceFull(
                    $row['unit_price_impact'],
                    null,
                    Context::getContext()->currency,
                    false
                );
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $combinations[$row['id_product_attribute']]
                    ['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $combinations[$row['id_product_attribute']]
                    ['available_date'] = $combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $combinations[$row['id_product_attribute']]
                    ['id_image'] = $id_image = (int)$combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($id_image > 0) {
                        $images = ProductDataTransform::productImages($id_image);
                        $combinations[$row['id_product_attribute']]['images'] = $images;
                    }
                }
            }

            // wash attributes list (if some attributes are unavailables and if allowed to wash it)
            if (!Product::isAvailableWhenOutOfStock($this->product->out_of_stock)
                && Configuration::get('PS_DISP_UNAVAILABLE_ATTR') == 0) {
                foreach ($groups as &$group) {
                    foreach ($group['attributes_quantity'] as $key => &$quantity) {
                        if ($quantity <= 0) {
                            unset($group['attributes'][$key]);
                        }
                    }
                }

                foreach ($colors as $key => $color) {
                    if ($color['attributes_quantity'] <= 0) {
                        unset($colors[$key]);
                    }
                }
            }

            // combine groups & colors into single model
            if (count($colors)) {
                foreach ($groups as &$group) {
                    if ($group['group_type'] === 'color') {
                        foreach ($group['attributes'] as $key => &$color_key) {
                            foreach ($colors as $key => $value) {
                                $color_name = $value['name'];
                                $color_value = $value['value'];

                                if ($color_name === $color_key) {
                                    if (!isset($groups[$group['id_group']]['attributes_color'][$key])) {
                                        $groups[$group['id_group']]['attributes_color'][$key] = $color_value == null? null : $color_value;
                                        if (file_exists(_PS_COL_IMG_DIR_ . $key . '.jpg')) {
                                            $groups[$group['id_group']]['attributes_texture'][$key] = _PS_BASE_URL_ . '/img/co/' . $key . '.jpg';
                                        } else {
                                            $groups[$group['id_group']]['attributes_texture'][$key] = null;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\'' . (int)$id_attribute . '\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
                    $combinations[$id_product_attribute]['id_combination'] = $id_product_attribute;
            }

            $jm_product_detail->groups = array_values($groups);
            $jm_product_detail->combinations = array_values($combinations);
        }
    }

    // check enable product show on mobile.
    protected function checkEnableShowProduct($id_product)
    {
        if ($id_product) {
            $sql = 'SELECT `not_visible` FROM `'._DB_PREFIX_.'jm_product_visibility` WHERE `id_product`='. (int)$id_product;
            $temps = Db::getInstance()->getValue($sql);
            if ($temps == 1 || $temps == null) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

    // function check product has exists in db.
    protected function checkExistsIdProduct($id_product)
    {
        if ($id_product) {
            $sql = 'SELECT EXISTS(SELECT `id_product` FROM `'._DB_PREFIX_.'product` WHERE `id_product`='. (int)$id_product.')';
            $result = Db::getInstance()->getValue($sql);
            if ($result == true) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function getCoverImage($id_product)
    {
        $coverImage = Image::getCover($id_product);
        return ProductDataTransform::productImage($coverImage['id_image']);
    }

    /**
     * @function : Return price of product to display if product have combinations. Price could excl-Tax or incl-Tax (maybe develop feature..).
     * @param  :$jm_product_detail : all info about product, need for calculate price.
     * @return : (float) Price to display.
     * @ticket-involve : PS-739 :[Prestashop 16] Final price is displayed is include tax on product detail while showing exclude tax on website.
     */
    public function updatePrice(&$jm_product_detail, $comId = null)
    {
        $currencyRate = Tools::setCurrency($this->context->cookie)->conversion_rate;
        // Get combination prices
        $comId = $this->transformSelectedOptions();
        if (!isset($comId)) {
            $comId = $this->product->cache_default_attribute;
        }
        $combination = array();
        foreach ($jm_product_detail->combinations as $comb) {
            $flag = false;
            foreach ($comb['attributes'] as $temps) {
                if (!in_array($temps, $comId)) {
                    $flag = false;
                    break;
                }
                $flag = true;
            }
            if ($flag) {
                $combination = $comb;
                break;
            }
//            if ($comb['attributes'] == $comId) {
//                $combination = $comb;
//                break;
//            }
        }

        if (!isset($combination) || $combination == null) {
            return;
        }

        // Set product (not the combination) base price
        $basePriceWithoutTax = $this->product->getPriceWithoutReduct(true, false) - $jm_product_detail->ecotax;
        $basePriceWithTax = $this->product->getPriceWithoutReduct(false, false) - $jm_product_detail->ecotax * (1 + $jm_product_detail->ecotax_tax_rate / 100);
        $priceWithGroupReductionWithoutTax = 0;

        $priceWithGroupReductionWithoutTax = $basePriceWithoutTax*(1 - $jm_product_detail->group_reduction);

        // Apply combination price impact (only if there is no specific price)
        // 0 by default, +x if price is inscreased, -x if price is decreased

        $basePriceWithoutTax = $basePriceWithoutTax + +($combination['price']);
        $basePriceWithTax = $basePriceWithTax + +($combination['price'])*($jm_product_detail->tax_rate/100 +1);

        $priceWithDiscountsWithoutTax = $basePriceWithoutTax;
        $priceWithDiscountsWithTax = $basePriceWithTax;

        if ($jm_product_detail->ecotax) {
            // combination.ecotax doesn't modify the price but only the display
            $priceWithDiscountsWithoutTax = $priceWithDiscountsWithoutTax + $jm_product_detail->ecotax * (1 + $jm_product_detail->ecotax_tax_rate / 100);
            $priceWithDiscountsWithTax = $priceWithDiscountsWithTax + $jm_product_detail->ecotax * (1 + $jm_product_detail->ecotax_tax_rate / 100);
            $basePriceWithTax = $basePriceWithTax + $jm_product_detail->ecotax * (1 + $jm_product_detail->ecotax_tax_rate / 100);
            $basePriceWithoutTax = $basePriceWithoutTax + $jm_product_detail->ecotax * (1 + $jm_product_detail->ecotax_tax_rate / 100);
        }

        // Apply specific price (discount)
        // We only apply percentage discount and discount amount given before tax
        // Specific price give after tax will be handled after taxes are added
        if ($combination['specific_price'] && $combination['specific_price']['reduction'] > 0) {
            if ($combination['specific_price']['reduction_type'] == 'amount') {
                if (isset($combination['specific_price']['reduction_tax']) && $combination['specific_price']['reduction_tax'] === "0") {
                    $reduction = $combination['specific_price']['reduction'];
                    if ($combination['specific_price']['id_currency'] == 0) {
                        $reduction = $reduction * $currencyRate * (1 - $jm_product_detail->group_reduction);
                    }
                    $priceWithDiscountsWithoutTax -= $reduction;
                    $priceWithDiscountsWithTax -= $reduction * ($jm_product_detail->tax_rate/100 + 1);
                }
            } else if ($combination['specific_price']['reduction_type'] == 'percentage') {
                $priceWithDiscountsWithoutTax = $priceWithDiscountsWithoutTax * (1 - +$combination['specific_price']['reduction']);
                $priceWithDiscountsWithTax = $priceWithDiscountsWithTax * (1 - +$combination['specific_price']['reduction']);
            }
        }

        // Apply Tax if necessary
        if ($jm_product_detail->no_tax || $jm_product_detail->customer_group_without_tax) {
            $basePriceDisplay = $basePriceWithoutTax;
            $priceWithDiscountsDisplay = $priceWithDiscountsWithoutTax;
        } else {
            $basePriceDisplay = $basePriceWithTax;
            $priceWithDiscountsDisplay = $priceWithDiscountsWithTax;
        }
        // If the specific price was given after tax, we apply it now
        if ($combination['specific_price'] && $combination['specific_price']['reduction'] > 0) {
            if ($combination['specific_price']['reduction_type'] == 'amount') {
                if (isset($combination['specific_price']['reduction_tax']) || isset($combination['specific_price']['reduction_tax']) && $combination['specific_price']['reduction_tax'] === '1') {
                    $reduction = $combination['specific_price']['reduction'];

                    if (isset($jm_product_detail->specific_prices->id_currency) && $jm_product_detail->specific_prices && (int)$combination['specific_price']['id_currency'] && $combination['specific_price']['id_currency'] != (int)$this->context->cookie->id_currency) {
                        $reduction = $reduction / $currencyRate;
                    } else if (!$jm_product_detail->specific_prices->id_currency) {
                        $reduction = $reduction * $currencyRate;
                    }
                    if (isset($jm_product_detail->group_reduction) && $jm_product_detail->group_reduction > 0) {
                        $reduction *= 1 - (float)$jm_product_detail->group_reduction;
                    }
                    $priceWithDiscountsDisplay -= $reduction;
                    // We recalculate the price without tax in order to keep the data consistency
                    $priceWithDiscountsWithoutTax = $priceWithDiscountsDisplay - $reduction * ( 1/(1 + $jm_product_detail->tax_rate / 100) );
                }
            }
        }

        if ($priceWithDiscountsDisplay < 0) {
            $priceWithDiscountsDisplay = 0;
        }
        return Tools::ps_round($priceWithDiscountsDisplay, 2);
    }

    public function assignVerifiedReview()
    {
        $o_av = new NetReviewsModel();
        $multisite = Configuration::get('AV_MULTISITE');
        $av_idshop = (!empty($multisite))? $this->context->shop->getContextShopID():null;
        $productReviewService = new \ProductReviewService();
        if (Configuration::get('AV_MULTILINGUE', null, null, $av_idshop) == 'checked') {
            $this->id_lang = $this->context->language->id;
            $iso_lang = pSQL(Language::getIsoById($this->id_lang));
            $group_name = $productReviewService->getIdConfigurationGroup($iso_lang);
        }
        $stats_product = $o_av->getStatsProduct($this->id_product, $group_name, $av_idshop);
        return $stats_product ? $stats_product : null;
    }
}

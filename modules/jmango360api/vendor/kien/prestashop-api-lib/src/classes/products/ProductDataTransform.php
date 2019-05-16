<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductDataTransform
{
    protected static $image_names = array();

    /**
     * Get largest product image type size
     *
     * @return string
     */
    public static function getLargestProductImageType()
    {
        try {
            $imageTypes = ImageType::getImagesTypes('products', true);
            if (!empty($imageTypes[0]['name'])) {
                return $imageTypes[0]['name'];
            } else {
                throw new Exception('No image type');
            }
        } catch (Exception $e) {
            if (method_exists('ImageType', 'getFormatedName')) {
                return ImageType::getFormatedName('large');
            } elseif (method_exists('ImageType', 'getFormattedName')) {
                return ImageType::getFormattedName('large');
            }
        }
    }

    /**
     * Return array of different sizes of the image with its full url
     *
     * @param string $id_image Id of the image
     * @return array
     */
    public static function productImages($id_image)
    {
        self::$image_names = array(
            self::getLargestProductImageType()
        );
        $image = new Image($id_image);
        $images = array();
        foreach (self::$image_names as $image_type) {
            $image_url = self::getProductImageUrlWithCheck($image, $image_type);
            if ($image_url) {
                $image_data = new JmKeyValue();
                $image_data->key = $image_type;
                $image_data->value = $image_url;

                $images[] = $image_data;
            }
        }

        return $images;
    }

    /**
     * Return product image url with best size
     *
     * @param string|Image $id_image
     * @return string
     */
    public static function productImage($id_image)
    {
        $image_type = self::getLargestProductImageType();
        $image = $id_image instanceof Image ? $id_image : new Image($id_image);
        return self::getProductImageUrlWithCheck($image, $image_type);
    }

    /**
     * Return product image url with pre-check existence
     *
     * @param Image $image
     * @param $image_type
     * @return null|string
     */
    public static function getProductImageUrlWithCheck($image, $image_type)
    {
        $baseUrl = Configuration::get('PS_SSL_ENABLED') ? _PS_BASE_URL_SSL_ : _PS_BASE_URL_;
        $dummyImageUrl = $baseUrl . '/make-sure-this-is-invalid-image.jpg';

        if (!($image instanceof Image)) {
            return $dummyImageUrl;
        }

        $imagePath = $image->getExistingImgPath() . '-' . $image_type . ".jpg";
        $originImagePath = $image->getExistingImgPath() . ".jpg";
        if (file_exists(_PS_PROD_IMG_DIR_ . $imagePath)) {
            return $baseUrl . _THEME_PROD_DIR_ . $imagePath;
        } else if (file_exists(_PS_PROD_IMG_DIR_ . $originImagePath)) {
            return $baseUrl . _THEME_PROD_DIR_ . $originImagePath;
        }

        return $dummyImageUrl;
    }

    public static function productList($product, $stock_management, $id_customer, $isManufacturerCatalog = false)
    {
        $prod = new JmProduct();
        $prod->id_product = $product['id_product'];
        $prod->name = $product['name'];
        $prod->description = $product['description'];
        if (ServiceProvider::isV17() && !$isManufacturerCatalog) {
            $prod->price = round($product['price_amount'] ? $product['price_amount'] : $product['price'], 2);
        } else {
            $prod->price = round($product['price'], 2);
        }
        $prod->price_tax_exc = $product['price_tax_exc'];
        $prod->price_without_reduction = round(
            $product['price_without_reduction'],
            2
        );
        $prod->description_short = $product['description_short'];
        $prod->available_now = $product['available_now'];
        $prod->available_later = $product['available_later'];
        $prod->on_sale = $product['on_sale'];
        $prod->show_price = $product['show_price'];
        $prod->new = $product['new'];
        $prod->quantity = $product['quantity'];
        $prod->minimal_quantity = $product['minimal_quantity'];
        $prod->reduction = $product['reduction'];
        $prod->pack = (int)$product['pack'];
        $prod->active = $product['active'];
        $prod->available_for_order = $product['available_for_order'];
        $prod->condition = $product['condition'];
        $prod->visibility = $product['visibility'];
        $prod->out_of_stock = $product['out_of_stock'];
        $prod->is_virtual = $product['is_virtual'];
        $prod->allow_oosp = $product['allow_oosp'];
        $prod->quantity_all_versions = $product['quantity_all_versions'];
        if (ServiceProvider::isV17()) {
            if (array_key_exists('cover', $product) && array_key_exists('id_image', $product['cover'])) {
                $prod->image = self::productImage($product['cover']['id_image']);
            } else {
                $prod->image = self::getProductImageUrl($product);
            }
        } else {
            $prod->image = self::getProductImageUrl($product);
        }
        $prod->stock_manangement = $stock_management;
        $prod->specific_prices = self::specificPrices($product['specific_prices']);

        // Display tax or not
        $prod->display_excl_tax_price = Product::getTaxCalculationMethod($id_customer);

        return $prod;
    }

    public static function getProductImageUrl($product)
    {
        $coverImage = Image::getCover($product['id_product']);
        return ProductDataTransform::productImage($coverImage['id_image']);
    }

    public static function customizationFields($customization_fields = array())
    {
        $custom_fields = array();
        foreach ($customization_fields as $field) {
            $custom = new JmProductCustomField();
            $custom->id_customization_field = $field['id_customization_field'];
            $custom->type = $field['type'];
            $custom->required = $field['required'];
            $custom->name = $field['name'];
            $custom->id_lang = $field['id_lang'];
            $custom->type = $field['type'];

            switch ($custom->type) {
                case (0):
                    $custom->field_type = JmConstants::FIELD_FILE;
                    break;
                case (1):
                    $custom->field_type = JmConstants::FIELD_TEXTAREA;
                    break;
                default:
                    $custom->field_type = JmConstants::FIELD_NOT_SUPPORTED;
                    break;
            }

            $custom_fields[] = $custom;
        }

        return $custom_fields;
    }

    public static function specificPrices($priceObject)
    {
        $specific_price = new JmProductSpecificPrice();

        if (JmValidator::IsNullOrEmptyObject($priceObject)) {
            return $specific_price;
        }

        $specific_price->id_product = $priceObject['id_product'];
        $specific_price->id_product_attribute = $priceObject['id_product_attribute'];
        $specific_price->price = $priceObject['price'];
        $specific_price->from_quantity = $priceObject['from_quantity'];
        $specific_price->reduction = $priceObject['reduction'];
        $specific_price->reduction_tax_incl = $priceObject['reduction_tax'];
        $specific_price->reduction_type = $priceObject['reduction_type'];
        $specific_price->from = $priceObject['from'];
        $specific_price->to = $priceObject['to'];
        $specific_price->id_currency = $priceObject['id_currency'];

        return $specific_price;
    }

    public static function quantityDiscounts($quantity_discounts = array())
    {
//        $specific_prices = array();
//
//        foreach ($quantity_discounts as $discount) {
//            $specific_price = new JmProductQuantityDiscount();
//
//            $specific_price->id_currency = $discount['id_currency'];
//            $specific_price->id_product = $discount['id_product'];
//            $specific_price->id_product_attribute = $discount['id_product_attribute'];
//            $specific_price->price = $discount['price'];
//            $specific_price->from_quantity = $discount['from_quantity'];
//            $specific_price->reduction = $discount['reduction'];
//            $specific_price->reduction_tax_incl = $discount['reduction_tax'];
//            $specific_price->reduction_type = $discount['reduction_type'];
//            $specific_price->from = $discount['from'];
//            $specific_price->to = $discount['to'];
//            $specific_price->score = $discount['score'];
//            $specific_price->base_price = $discount['base_price'];
//            $specific_price->attributes = $discount['attributes'];
//            $specific_price->quantity = $discount['quantity'];
//            $specific_price->real_value = $discount['real_value'];
//            $specific_price->reduction_with_tax = $discount['reduction_with_tax'];
//            $specific_prices[] = $specific_price;
//        }
//
//        return $specific_prices;
        $result = array();
        foreach ($quantity_discounts as $quantity_discount) {
            $formatted_quantity_discount = array();
            $formatted_quantity_discount['reduction_type'] = $quantity_discount['reduction_type'];
            $formatted_quantity_discount['from_quantity'] = $quantity_discount['from_quantity'];
            $formatted_quantity_discount['discount'] = Tools::ps_round($quantity_discount['real_value'], 2);
            $formatted_quantity_discount['price'] = Tools::ps_round($quantity_discount['discounted_price'], 2);
            $formatted_quantity_discount['save'] = Tools::ps_round($quantity_discount['save'], 2);
            $result[] = $formatted_quantity_discount;
        }
        return $result;
    }

    public static function productDetails(ProductCore $product)
    {
        $prod = new JmProductDetail();

        // echo json_encode($product);
        // die();
        $prod->id_manufacturer = $product->id_manufacturer;
        $prod->manufacturer_name = $product->manufacturer_name;
        $prod->id_supplier = $product->id_supplier;
        $prod->supplier_name = $product->supplier_name;
        if (is_array($product->name)) {
            $prod->name = reset($product->name);
        } else {
            $prod->name = $product->name;
        }

        if (is_array($product->description)) {
            $prod->description = reset($product->description);
        } else {
            $prod->description = $product->description;
        }

        /**
         * PS-923: Support displayProductElementor hook
         */
        $productVars = self::getProductVars($product);
        $smarty = Context::getContext()->smarty;
        $smarty->assign(array(
            'product' => $productVars
        ));
        $prod->description .= Hook::exec('displayProductElementor', array(
            'smarty' => $smarty
        ));

        if (is_array($product->description_short)) {
            $prod->description_short = reset($product->description_short);
        } else {
            $prod->description_short = $product->description_short;
        }

        /**
         * PS-911: Support displayReassurance, displayProductExtraContent hook
         */
        $prod->description_short .= Hook::exec('displayReassurance');
        /**
         * PS-1239: Fix netreviews v7.8.0
         */
        if (version_compare(_PS_VERSION_, '1.7', '>=')) {
            $extraContents = Hook::exec('displayProductExtraContent', array('product' => $product), null, true);
        }
        $extraContentModules = array('iqitadditionaltabs');
        if (is_array($extraContents)) {
            $extraContentArray = array();
            foreach ($extraContents as $moduleId => $extraContent) {
                if (in_array($moduleId, $extraContentModules)) {
                    if (is_array($extraContent)) {
                        foreach ($extraContent as $item) {
                            if (is_object($item)) {
                                /** @var PrestaShop\PrestaShop\Core\Product\ProductExtraContent $item */
                                $extraContentArray[] = sprintf(
                                    '<strong>%s</strong><br/><div>%s</div>',
                                    $item->getTitle(),
                                    $item->getContent()
                                );
                            }
                        }
                    }
                }
            }
            if (count($extraContentArray)) {
                $prod->description_short .= '<div id="block-productextracontent">';
                $prod->description_short .= join('<br>', $extraContentArray);
                $prod->description_short .= '</div>';
            }
        }
        $prod->description_short .= self::getProductCustomCss();

        $prod->quantity = $product->quantity;
        $prod->minimal_quantity = $product->minimal_quantity;
        $prod->available_now = reset($product->available_now);
        $prod->available_later = reset($product->available_later);
        $prod->price = $product->price;
        $prod->specific_prices = $product->specificPrice;
        $prod->additional_shipping_cost = $product->additional_shipping_cost;
        $prod->wholesale_price = $product->wholesale_price;
        $prod->on_sale = $product->on_sale;
        $prod->unity = $product->unity;
        $prod->unit_price = $product->unit_price;
        $prod->unit_price_ratio = $product->unit_price_ratio;
        $prod->reference = $product->reference;
        $prod->supplier_reference = $product->supplier_reference;
        $prod->location = $product->location;
        $prod->width = $product->width;
        $prod->height = $product->height;
        $prod->depth = $product->depth;
        $prod->weight = $product->weight;
        $prod->ean13 = $product->ean13;
        $prod->upc = $product->upc;
        $prod->link_rewrite = reset($product->link_rewrite);
        $prod->meta_description = reset($product->meta_description);
        $prod->meta_keywords = reset($product->meta_keywords);
        $prod->meta_title = reset($product->meta_title);
        $prod->quantity_discount = $product->quantity_discount;
        $prod->customizable = $product->customizable;
        $prod->new = $product->new;
        $prod->uploadable_files = $product->uploadable_files;
        $prod->text_fields = $product->text_fields;
        $prod->active = $product->active;
        $prod->available_for_order = $product->available_for_order;
        $prod->available_date = $product->available_date;
        $prod->condition = $product->condition;
        $prod->show_price = $product->show_price;
        $prod->indexed = $product->indexed;
        $prod->visibility = $product->visibility;

        if ($product->tags) {
            $prod->tags = reset($product->tags);
        }

        $prod->base_price = $product->base_price;
        $prod->advanced_stock_management = $product->advanced_stock_management;
        $prod->out_of_stock = $product->out_of_stock;
        $prod->depends_on_stock = $product->depends_on_stock;
        $prod->is_virtual = $product->is_virtual;
        $prod->pack_stock_type = $product->pack_stock_type;
        $prod->id_product = $product->id;
        $prod->specific_prices = self::specificPrices($product->specificPrice);
        $prod->stock_manangement = Configuration::get('PS_STOCK_MANAGEMENT');

        return $prod;
    }

    /**
     * Get product variables as array for Smarty
     *
     * @param ProductCore $product
     * @return array
     * @throws
     */
    protected static function getProductVars($product)
    {
        if (ServiceProvider::isV17()) {
            try {
                $objPresenter = new \PrestaShop\PrestaShop\Adapter\ObjectPresenter();
                return $objPresenter->present($product);
            } catch (Exception $e) {
                return array();
            }
        } else {
            if (!is_a($product, 'ObjectModel')) {
                return array();
            }

            $presentedObject = array();

            $fields = Product::$definition['fields'];
            foreach ($fields as $fieldName => $null) {
                $presentedObject[$fieldName] = $product->{$fieldName};
            }
            $mustHave = array('id');
            foreach ($mustHave as $fieldName) {
                $presentedObject[$fieldName] = $product->{$fieldName};
            }

            $mustRemove = array('deleted', 'active');
            foreach ($mustRemove as $fieldName) {
                if (isset($presentedObject[$fieldName])) {
                    unset($presentedObject[$fieldName]);
                }
            }

            return $presentedObject;
        }
    }

    /**
     * Get custom CSS for product short description
     *
     * @return string
     */
    protected static function getProductCustomCss()
    {
        return '<style type="text/css">#block-reassurance .block-reassurance-item img,#block-reassurance .block-reassurance-item span{width:auto;vertical-align:middle}</style>';
    }

    public static function attributeGroups($groups = array())
    {
        $attributes = array();

        foreach ($groups as $group) {
            $specific_price = new JmProductQuantityDiscount();

            $specific_price->id_currency = $group['id_currency'];
            $specific_price->id_product = $group['id_product'];
            $specific_price->id_product_attribute = $group['id_product_attribute'];
            $specific_price->price = $group['price'];
            $specific_price->from_quantity = $group['from_quantity'];
            $specific_price->reduction = $group['reduction'];
            $specific_price->reduction_tax_incl = $group['reduction_tax'];
            $specific_price->reduction_type = $group['reduction_type'];
            $specific_price->from = $group['from'];
            $specific_price->to = $group['to'];
            $specific_price->score = $group['score'];
            $specific_price->base_price = $group['base_price'];
            $specific_price->attributes = $group['attributes'];
            $specific_price->quantity = $group['quantity'];
            $specific_price->real_value = $group['real_value'];
            $specific_price->reduction_with_tax = $group['reduction_with_tax'];

            //$specific_prices[] = $specific_price;
            $attributes[] = $specific_price;
        }

        return $attributes;
    }
}

<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmProduct
{
    public $id_product; // similar as id_product
        // "id_manufacturer": "1",
        // "id_category_default": "5",
        // "id_shop_default": "1",
        // "id_tax_rules_group": "1",
        // "on_sale": "0",
    public $on_sale;
        // "online_only": "0",
        // "ean13": "0",
        // "upc": "",
        // "ecotax": "0.000000",
        // "quantity": 299,
    public $quantity;
        // "minimal_quantity": "1",
    public $minimal_quantity;
        // "price": 16.51,
    public $price;
        // "wholesale_price": "4.950000",
        // "unity": "",
        // "unit_price_ratio": "0.000000",
        // "additional_shipping_cost": "0.00",r
        // "reference": "demo_1",
        // "supplier_reference": "",
        // "location": "",
        // "width": "0.000000",
        // "height": "0.000000",
        // "depth": "0.000000",
        // "weight": "0.000000",
        // "out_of_stock": "2",
    public $out_of_stock;
        // "quantity_discount": "0",
        // "customizable": "0",
        // "uploadable_files": "0",
        // "text_fields": "0",
        // "active": "1",
    public $active;
        // "redirect_type": "404",
        // "id_product_redirected": "0",
        // "available_for_order": "1",
    public $available_for_order;
        // "available_date": "0000-00-00",
        // "condition": "new",
    public $condition;
        // "show_price": "1",
    public $show_price;
        // "indexed": "1",
        // "visibility": "both",
    public $visibility;
        // "cache_is_pack": "0",
        // "cache_has_attachments": "0",
        // "is_virtual": "0",
    public $is_virtual;
        // "cache_default_attribute": "1",
        // "date_add": "2017-12-14 23:39:59",
        // "date_upd": "2017-12-14 23:39:59",
        // "advanced_stock_management": "0",
        // "pack_stock_type": "3",
        // "id_shop": "1",
        // "id_lang": "1",
        // "description": "<p>Fashion has been creating well-designed collections since 2010.
    //The brand offers feminine designs delivering stylish separates and statement dresses which have since evolved
    // into a full ready-to-wear collection in which every item is a vital part of a woman's wardrobe.
    //The result? Cool, easy, chic looks with youthful
    // elegance and unmistakable signature style.
    //All the beautiful pieces are made in Italy and manufactured with
    // the greatest attention. Now Fashion extends to a range of accessories
    // including shoes, hats, belts and more!</p>",
    public $description;
        // "description_short": "<p>Faded short sleeves t-shirt with high neckline.
    // Soft and stretchy material for a comfortable fit.
    // Accessorize with a straw hat and you're ready for summer!</p>",
    public $description_short;
        // "link_rewrite": "faded-short-sleeves-tshirt",
        // "meta_description": "",
        // "meta_keywords": "",
        // "meta_title": "",
        // "name": "Faded Short Sleeves T-shirt",
    public $name;
        // "available_now": "In stock",
    public $available_now;
        // "available_later": "",
    public $available_later;
        // "id_image": "1-1",
    public $image;
        // "legend": "",
        // "manufacturer_name": "Fashion Manufacturer",
        // "id_product_attribute": 1,
        // "new": "1",
    public $new;
        // "product_attribute_minimal_quantity": "1",
        // "allow_oosp": 0,   //isAvailableWhenOutOfStock
    public $allow_oosp;
        // "category": "tshirts",
        // "link": "http://localhost/index.php?id_product=1&controller=product",
        // "attribute_price": 0,
        // "price_tax_exc": 16.51,
    public $price_tax_exc;
        // "price_without_reduction": 16.51,
    public $price_without_reduction;
        // "reduction": 0,
    public $reduction;
    // "specific_prices" : false,
    public $specific_prices;
        // "quantity_all_versions": 1799,
    public $quantity_all_versions;
        // "features": [
        //     {
        //         "name": "Compositions",
        //         "value": "Cotton",
        //         "id_feature": "5"
        //     },
        //     {
        //         "name": "Styles",
        //         "value": "Casual",
        //         "id_feature": "6"
        //     },
        //     {
        //         "name": "Properties",
        //         "value": "Short Sleeve",
        //         "id_feature": "7"
        //     }
        // ],
        // "attachments": [],
        // "virtual": 0,
        // "pack": 0,
    public $pack;
        // "packItems": [],
        // "nopackprice": 0,
        // "customization_required": false,
        // "rate": 0,
        // "tax_name": ""
    public $stock_manangement;
    public $display_excl_tax_price;
    public $product_url;
    public $verified_review = null;
}

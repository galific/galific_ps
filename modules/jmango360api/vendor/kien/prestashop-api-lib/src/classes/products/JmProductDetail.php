<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmProductDetail extends JmProduct
{
    public $gallery;
    public $customization_fields = array();
    public $quantity_discounts = array();
    public $ecotax_tax_inc;
    public $ecotax_tax_exc;
    public $ecotax_tax_rate;
    public $product_price_without_eco_tax;
    public $group_reduction;
    public $no_tax;
    public $tax_enabled;
    public $customer_group_without_tax;
    public $groups = array();
    public $combinations = array();
    public $pack_items = array();
    public $accessories = array();
    public $product_manufacturer;

    // {
    //     "tax_name": "deprecated",
    //     "tax_rate": 0,
    public $tax_rate;
    //     "id_manufacturer": "1",
    public $id_manufacturer;
    //     "id_supplier": "1",
    public $id_supplier;
    //     "id_category_default": "5",
    //     "id_shop_default": "1",
    //     "manufacturer_name": "Fashion Manufacturer",
    public $manufacturer_name;
    //     "supplier_name": "Fashion Supplier",
    public $supplier_name;
    //     "name": {
    //         "1": "Faded Short Sleeves T-shirt"
    //     },
    //     "description": {
    //         "1": "<p>Fashion has been creating well-designed collections since 2010.
    //  The brand offers feminine designs delivering stylish
    //  separates and statement dresses which have
    //  since evolved into a full ready-to-wear collection
    //  in which every item is a vital part of a woman's wardrobe.
    //  The result? Cool, easy, chic looks with youthful elegance and unmistakable
    //  signature style.
    //  All the beautiful pieces are made in Italy and manufactured with the greatest attention.
    //  Now Fashion extends to a range of accessories including shoes, hats, belts and more!</p>"
    //     },
    //     "description_short": {
    //         "1": "<p>Faded short sleeves t-shirt with high neckline.
    //  Soft and stretchy material for a comfortable fit.
    //  Accessorize with a straw hat and you're ready for summer!</p>"
    //     },
    //     "quantity": 1798,
    //     "minimal_quantity": "1",
    //     "available_now": {
    //         "1": "In stock"
    //     },
    //     "available_later": {
    //         "1": ""
    //     },
    //     "price": 14.51,
    //     "specificPrice": {
    //         "id_specific_price": "149",
    //         "id_specific_price_rule": "1",
    //         "id_cart": "0",
    //         "id_product": "1",
    //         "id_shop": "1",
    //         "id_shop_group": "0",
    //         "id_currency": "0",
    //         "id_country": "0",
    //         "id_group": "0",
    //         "id_customer": "0",
    //         "id_product_attribute": "0",
    //         "price": "-1.000000",
    //         "from_quantity": "1",
    //         "reduction": "2.000000",
    //         "reduction_tax": "0",
    //         "reduction_type": "amount",
    //         "from": "0000-00-00 00:00:00",
    //         "to": "0000-00-00 00:00:00",
    //         "score": "48"
    //     },
    //     "additional_shipping_cost": "0.00",
    public $additional_shipping_cost;
    //     "wholesale_price": "4.950000",
    public $wholesale_price;
    //     "on_sale": "0",
    //     "online_only": "0",
    //     "unity": "",
    public $unity;
    //     "unit_price": 0,
    public $unit_price;
    //     "unit_price_ratio": "0.000000",
    public $unit_price_ratio;
    //     "ecotax": "0.000000",
    public $ecotax;
    //     "reference": "demo_1",
    public $reference;
    //     "supplier_reference": "",
    public $supplier_reference;
    //     "location": "",
    public $location;
    //     "width": "0.000000",
    public $width;
    //     "height": "0.000000",
    public $height;
    //     "depth": "0.000000",
    public $depth;
    //     "weight": "0.000000",
    public $weight;
    //     "ean13": "0",
    public $ean13;
    //     "upc": "",
    public $upc;
    //     "link_rewrite": {
    //         "1": "faded-short-sleeves-tshirt"
    //     },
    public $link_rewrite;
    //     "meta_description": {
    //         "1": ""
    //     },
    public $meta_description;
    //     "meta_keywords": {
    //         "1": ""
    //     },
    public $meta_keywords;
    //     "meta_title": {
    //         "1": ""
    //     },
    public $meta_title;
    //     "quantity_discount": "0",
    public $quantity_discount;
    //     "customizable": "2",
    public $customizable;
    //     "new": true,
    //     "uploadable_files": "1",
    public $uploadable_files;
    //     "text_fields": "2",
    public $text_fields;
    //     "active": "1",
    //     "redirect_type": "404",
    //     "id_product_redirected": "0",
    //     "available_for_order": "1",
    //     "available_date": "0000-00-00",
    public $available_date;
    //     "condition": "new",
    //     "show_price": "1",
    //     "indexed": "1",
    public $indexed;
    //     "visibility": "both",
    //     "date_add": "2017-12-14 23:39:59",
    //     "date_upd": "2017-12-29 13:23:26",
    //     "tags": false,
    public $tags = array();
    //     "base_price": "16.510000",
    public $base_price;
    //     "id_tax_rules_group": "1",
    //     "id_color_default": 0,
    //     "advanced_stock_management": "0",
    public $advanced_stock_management;
    //     "out_of_stock": 2,
    //     "depends_on_stock": false,
    public $depends_on_stock;
    //     "isFullyLoaded": true,
    //     "cache_is_pack": "0",
    //     "cache_has_attachments": "0",
    //     "is_virtual": "0",
    //     "id_pack_product_attribute": null,
    //     "cache_default_attribute": "1",
    //     "category": false,
    //     "pack_stock_type": "3",
    public $pack_stock_type;
    //     "id": 1,
    //     "id_shop_list": null,
    //     "force_id": false
    // }

    public $calculated_base_price;
    public $calculated_final_price;
    public $verified_review = null;
}

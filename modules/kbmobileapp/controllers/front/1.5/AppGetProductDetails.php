<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 *
 * Description
 *
 * API to get details of product
 */

require_once 'AppCore.php';

class AppGetProductDetails extends AppCore
{
    private $product = null;
    private $has_file_field = 0;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        if (!(int) Tools::getValue('product_id', 0)) {
            $this->content['product_result'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Product id is missing'),
                    'AppGetProductDetails'
                )
            );
        } else {
            $this->product = new Product(
                Tools::getValue('product_id', 0),
                true,
                $this->context->language->id,
                $this->context->shop->id,
                $this->context
            );
            if (!Validate::isLoadedObject($this->product)) {
                $this->content['product_result'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Product not found'),
                        'AppGetProductDetails'
                    )
                );
            } else {
                $this->content['product'] = $this->getProduct();
            }
        }

        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Get Product details
     *
     * @return array product data
     */
    public function getProduct()
    {
        $product = array();
        $product['id_product'] = $this->product->id;
        $product['name'] = $this->product->name;
        $product['available_for_order'] = $this->product->available_for_order;
        $product['show_price'] = $this->product->show_price;
        $product['new_products'] = (isset($this->product->new) && $this->product->new == 1) ? "1" : "0";
        $product['on_sale_products'] = $this->product->on_sale;
        $product['quantity'] = $this->product->quantity;
        $product['minimal_quantity'] = $this->product->minimal_quantity;
        if ($this->product->out_of_stock == 1) {
            $product['allow_out_of_stock'] = "1";
        } elseif ($this->product->out_of_stock == 0) {
            $product['allow_out_of_stock'] = "0";
        } elseif ($this->product->out_of_stock == 2) {
            $out_of_stock = Configuration::get('PS_ORDER_OUT_OF_STOCK');
            if ($out_of_stock == 1) {
                $product['allow_out_of_stock'] = "1";
            } else {
                $product['allow_out_of_stock'] = "0";
            }
        }



        $priceDisplay = Product::getTaxCalculationMethod(0); //(int)$this->context->cookie->id_customer
        if (!$priceDisplay || $priceDisplay == 2) {
            $price = $this->product->getPrice(true, false);
            $price_without_reduction = $this->product->getPriceWithoutReduct(false);
        } else {
            $price = $this->product->getPrice(false, false);
            $price_without_reduction = $this->product->getPriceWithoutReduct(true);
        }
        if ($priceDisplay >= 0 && $priceDisplay <= 2) {
            if ($price_without_reduction <= 0 || !$this->product->specificPrice) {
                $product['price'] = $this->formatPrice($price);
                $product['discount_price'] = '';
                $product['discount_percentage'] = '';
            } else {
                if ($this->product->specificPrice
                    && $this->product->specificPrice['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                    $product['discount_percentage'] = $this->product->specificPrice['reduction'] * 100;
                } elseif ($this->product->specificPrice
                    && $this->product->specificPrice['reduction_type'] == 'amount'
                    && $this->product->specificPrice['reduction'] > 0) {
                    $temp_price = (float) ($this->product->specificPrice['reduction'] * 100);
                    $percent = (float) ($temp_price/ $price_without_reduction);
                    $product['discount_percentage'] = Tools::ps_round($percent);
                    unset($temp_price);
                }
                $product['price'] = $this->formatPrice($price_without_reduction);
                $product['discount_price'] = $this->formatPrice($price);
            }
        } else {
            $product['price'] = '';
            $product['discount_price'] = '';
            $product['discount_percentage'] = '';
        }

        $product['images'] = array();
        $temp_images = $this->product->getImages((int) $this->context->language->id);
        $cover = false;
        $images = array();
        foreach ($temp_images as $image) {
            if ($image['cover']) {
                $cover = $image;
            } else {
                $images[] = $image;
            }
        }

        if ($cover) {
            $images = array_merge(array($cover), $images);
        }
        foreach ($images as $image) {
            /* Changes started by rishabh jain on 3rd sep 2018
            * Added urlencode perimeter in image link if enabled by admin
            */
            
            if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                $product['images'][]['src'] = $this->context->link->getImageLink(
                    urlencode($this->product->link_rewrite),
                    ($this->product->id . '-' . $image['id_image']),
                    $this->getImageType('large')
                );
            } else {
                $product['images'][]['src'] = $this->context->link->getImageLink(
                    $this->product->link_rewrite,
                    ($this->product->id . '-' . $image['id_image']),
                    $this->getImageType('large')
                );
            }
            /* Changes over */
        }

        $options = array();
        $combinations = array();
        $attributes = $this->getProductAttributesGroups();
        if (!empty($attributes['groups'])) {
            $index = 0;
            foreach ($attributes['groups'] as $grp_id => $grp) {
                $options[$index]['id'] = $grp_id;
                $options[$index]['title'] = $grp['name'];
                if ($grp['group_type'] == 'color') {
                    $options[$index]['is_color_option'] = 1;
                } else {
                    $options[$index]['is_color_option'] = 0;
                }
                $item = array();
                foreach ($grp['attributes'] as $key => $group_item) {
                    if ($grp['group_type'] == 'color') {
                        $hex_value = '';
                        if (isset($attributes['colors'][$key]['value'])) {
                            $hex_value = $attributes['colors'][$key]['value'];
                        }
                        $item[] = array(
                            'id' => $key,
                            'value' => $group_item,
                            'hex_value' => $hex_value
                        );
                    } else {
                        $item[] = array(
                            'id' => $key,
                            'value' => $group_item
                        );
                    }
                }
                $options[$index]['items'] = $item;
                $index++;
            }
        }
        if (!empty($attributes['combinations'])) {
            $index = 0;
            foreach ($attributes['combinations'] as $attr_id => $attr) {
                $combinations[$index]['id_product_attribute'] = $attr_id;
                $combinations[$index]['quantity'] = $attr['quantity'];
                $combinations[$index]['price'] = $attr['price'];
                $combinations[$index]['minimal_quantity'] = $attr['minimal_quantity'];
                $attribute_list = '';
                foreach ($attr['attributes'] as $attribute_id) {
                    $attribute_list .= (int) $attribute_id . '_';
                }
                $attribute_list = rtrim($attribute_list, '_');
                $combinations[$index]['combination_code'] = $attribute_list;
                $index++;
            }
        }
        $product['combinations'] = $combinations;
        $product['options'] = $options;

        $product['description'] = preg_replace('/<iframe.*?\/iframe>/i', '', $this->product->description);
        /*start:changes made by aayushi on 1 DEC 2018 to add Short Description on product page*/
        if (Configuration::get('KB_MOBILE_APP_SHORT_DESCRIPTION_SWITCH') == 1) {
            $product['short_description'] = preg_replace('/<iframe.*?\/iframe>/i', '', $this->product->description_short);
        } else {
            $product['short_description'] = '';
        }
        /*end:changes made by aayushi on 1 DEC 2018 to add Short Description on product page*/
        $product_info = array();
        if ($this->product->id_manufacturer) {
            $product_info[] = array(
                'name' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Brand'),
                    'AppGetProductDetails'
                ),
                'value' => Manufacturer::getNameById($this->product->id_manufacturer)
            );
        }

        $product_info[] = array(
            'name' => parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('SKU'),
                'AppGetProductDetails'
            ),
            'value' => $this->product->reference
        );
        $product_info[] = array(
            'name' => parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Condition'),
                'AppGetProductDetails'
            ),
            'value' => Tools::ucfirst($this->product->condition)
        );

        $features = $this->product->getFrontFeatures($this->context->language->id);
        if (!empty($features)) {
            foreach ($features as $f) {
                $product_info[] = array('name' => $f['name'], 'value' => $f['value']);
            }
        }
        $product['product_info'] = $product_info;
        $product['accessories'] = $this->getProductAccessories();
        $product['customization_fields'] = $this->getCustomizationFields();
        $product['pack_products'] = $this->getPackProducts();
        if ($this->has_file_field == 1) {
            $product['has_file_customization'] = '1';
            $product['customization_message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('This product is not available on App as it has file customization field.'),
                'AppGetProductDetails'
            );
        } else {
            $product['has_file_customization'] = '0';
            $product['customization_message'] = '';
        }
        $product['seller_info'] = array();
        
        //Add seller Information if Marketplace is installed and feature is enable
        $product['seller_info'] = array();
        if ($this->isMarketplaceEnable()) {
            $seller = KbSellerProduct::getSellerByProductId($this->product->id);
            if (is_array($seller) && count($seller) > 0) {
                $sellerRating = Tools::math_round(KbSellerReview::getSellerRating($seller['id_seller']), '1');
                $writeEnabled = KbSellerSetting::getSellerSettingByKey($seller['id_seller'], 'kbmp_enable_seller_review');
                $product['seller_info'][] = array(
                    'seller_id' => $seller['id_seller'],
                    'name' => $seller['title'],
                    'rating' => $sellerRating,
                    'is_write_review_enabled' => $writeEnabled
                );
            }
        }
        
        $product['product_youtube_url'] = $this->getProductVideoURL($this->product->id);
        $product['product_attachments_array'] = $this->getProductAttachmentURLs($this->product->id);
        
        $product['is_in_wishlist'] = $this->isProductHasInWishlist($this->product->id);
        $link = new Link();
        $url = $link->getProductLink($product);
        $product['product_url'] = $url;
        
        return $product;
    }

    /**
     * Get product youtube video URL
     *
     * @param int $id_product product id
     * @return string youtube video url
     */
    public function getProductVideoURL($id_product)
    {
        $get_ytdata_qry = 'select * from '._DB_PREFIX_.'kb_product_youtube_mapping
            where id_product='.(int)$id_product;
        $yt_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($get_ytdata_qry);
        
        if (isset($yt_data['youtube_url']) && $yt_data['youtube_url'] != '') {
            return $yt_data['youtube_url'];
        } else {
            return '';
        }
    }
    
    /**
     * Get Virtual product attchements URLS
     *
     * @param int $id_product product id
     * @return array product attachment data
     */
    public function getProductAttachmentURLs($id_product)
    {
        $final_attachment_data = array();
        $attachments = Product::getAttachmentsStatic((int)$this->context->language->id, $id_product);
        $count = 0;
        foreach ($attachments as $attachment) {
            $final_attachment_data[$count]['download_link'] = $this->context->link->getPageLink('attachment', true, null, "id_attachment=".$attachment['id_attachment']);
            $final_attachment_data[$count]['file_size'] = Tools::formatBytes($attachment['file_size'], 2);
            $final_attachment_data[$count]['description'] = $attachment['description'];
            $final_attachment_data[$count]['file_name'] = $attachment['file_name'];
            $final_attachment_data[$count]['mime'] = $attachment['mime'];
            $final_attachment_data[$count]['display_name'] = $attachment['name'];
            $count++;
        }
        return $final_attachment_data;
    }
    
    /**
     * Get details of product attributes groups
     *
     * @return array product attribute group data
     */
    public function getProductAttributesGroups()
    {
        $colors = array();
        $groups = array();
        $combinations = array();

        $attributes_groups = $this->product->getAttributesGroups($this->context->language->id);

        if (is_array($attributes_groups) && $attributes_groups) {
            foreach ($attributes_groups as $row) {
                // Color management
                if (isset($row['is_color_group'])
                    && $row['is_color_group']
                    && (isset($row['attribute_color']) && $row['attribute_color'])
                    || (file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg'))) {
                    $colors[$row['id_attribute']]['value'] = $row['attribute_color'];
                    $colors[$row['id_attribute']]['name'] = $row['attribute_name'];
                    if (!isset($colors[$row['id_attribute']]['attributes_quantity'])) {
                        $colors[$row['id_attribute']]['attributes_quantity'] = 0;
                    }
                    $colors[$row['id_attribute']]['attributes_quantity'] += (int) $row['quantity'];
                }
                if (!isset($groups[$row['id_attribute_group']])) {
                    $groups[$row['id_attribute_group']] = array(
                        'group_name' => $row['group_name'],
                        'name' => $row['public_group_name'],
                        'group_type' => $row['group_type'],
                        'default' => -1,
                    );
                }

                $attr_g = $row['id_attribute_group'];
                $groups[$attr_g]['attributes'][$row['id_attribute']] = $row['attribute_name'];
                if ($row['default_on'] && $groups[$row['id_attribute_group']]['default'] == -1) {
                    $groups[$row['id_attribute_group']]['default'] = (int) $row['id_attribute'];
                }
                if (!isset($groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']])) {
                    $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] = 0;
                }
                $r_attr = $row['id_attribute_group'];
                $groups[$r_attr]['attributes_quantity'][$row['id_attribute']] += (int) $row['quantity'];

                $combinations[$row['id_product_attribute']]['attributes'][] = (int) $row['id_attribute'];

                //calculate full price for combination
                $priceDisplay = Product::getTaxCalculationMethod(0); //(int)$this->context->cookie->id_customer
                if (!$priceDisplay || $priceDisplay == 2) {
                    $combination_price = $this->product->getPrice(true, $row['id_product_attribute']);
                } else {
                    $combination_price = $this->product->getPrice(false, $row['id_product_attribute']);
                }
                $combinations[$row['id_product_attribute']]['price'] = $this->formatPrice($combination_price);
                $combinations[$row['id_product_attribute']]['quantity'] = (int) $row['quantity'];
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = (int) $row['minimal_quantity'];
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
            foreach ($combinations as $id_product_attribute => $comb) {
                $attribute_list = '';
                foreach ($comb['attributes'] as $id_attribute) {
                    $attribute_list .= '\'' . (int) $id_attribute . '\',';
                }
                $attribute_list = rtrim($attribute_list, ',');
                $combinations[$id_product_attribute]['list'] = $attribute_list;
            }
        }

        return array(
            'groups' => $groups,
            'colors' => (count($colors)) ? $colors : false,
            'combinations' => $combinations
        );
    }

    /**
     * Get details of accessories products
     *
     * @return array product accessories information
     */
    public function getProductAccessories()
    {
        $accessory_products = array();
        $accessories = $this->product->getAccessories($this->context->language->id);
        $has_accessories = "1";

        if ($accessories) {
            $index = 0;
            foreach ($accessories as $accessory) {
                if ($accessory['available_for_order']) {
                    $accessory_products[$index] = array(
                        'id' => $accessory['id_product'],
                        'is_in_wishlist' => $this->isProductHasInWishlist($accessory['id_product']),
                        'name' => $accessory['name'],
                        'price' => $this->formatPrice($accessory['price_without_reduction']),
                        'available_for_order' => $accessory['available_for_order'],
                        'show_price' => $accessory['show_price'],
                        'new_products' => (isset($accessory['new']) && $accessory['new'] == 1) ? "1" : "0",
                        'on_sale_products' => $accessory['on_sale'],
                        
                    );
                    /* Changes started by rishabh jain on 3rd sep 2018
                    * Added urlencode perimeter in image link if enabled by admin
                    */
                    if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                        $accessory_products[$index]['src']  =  $this->context->link->getImageLink(
                            urlencode($accessory['link_rewrite']),
                            $accessory['id_image'],
                            $this->getImageType('large')
                        );
                    } else {
                        $accessory_products[$index]['src']  =  $$this->context->link->getImageLink(
                            $accessory['link_rewrite'],
                            $accessory['id_image'],
                            $this->getImageType('large')
                        );
                    }
                    /* Changes over */

                    if (count($accessory['specific_prices']) > 0) {
                        $accessory_products[$index]['discount_price'] = $this->formatPrice($accessory['price']);
                        if ($accessory['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                            $temp_p = (float) $accessory['specific_prices']['reduction'] * 100;
                            $accessory_products[$index]['discount_percentage'] = $temp_p;
                            unset($temp_p);
                        } else {
                            if ($accessory['price_without_reduction']) {
                                $temp_price = ((float) $accessory['specific_prices']['reduction'] * 100);
                                $percent = (float) ($temp_price / $accessory['price_without_reduction']);
                                unset($temp_price);
                            } else {
                                $percent = 0;
                            }
                            $accessory_products[$index]['discount_percentage'] = Tools::ps_round($percent);
                        }
                    } else {
                        $accessory_products[$index]['discount_price'] = '';
                        $accessory_products[$index]['discount_percentage'] = '';
                    }
                    $index++;
                }
            }
        } else {
            $has_accessories = "0";
        }
        return array('has_accessories' => $has_accessories, 'accessories_items' => $accessory_products);
    }

    /**
     * Get details of customzable fields of customized product
     *
     * @return array product customized data
     */
    public function getCustomizationFields()
    {
        $customization_fields = array();
        $customization_data = $this->product->getCustomizationFields($this->context->language->id);
        $is_customizable = "0";

        if ($customization_data && is_array($customization_data)) {
            $index = 0;
            foreach ($customization_data as $data) {
                if ($data['type'] == 1) {
                    $is_customizable = "1";
                    $customization_fields[$index] = array(
                        'id_customization_field' => $data['id_customization_field'],
                        'required' => $data['required'],
                        'title' => $data['name'],
                        'type' => 'text'
                    );
                    $index++;
                } elseif ($data['type'] == 0 && $data['required'] == 1) {
                    $this->has_file_field = 1;
                }
            }
        }

        return array('is_customizable' => $is_customizable, 'customizable_items' => $customization_fields);
    }

    /**
     * Get details of pack products
     *
     * @return array pick items information
     */
    public function getPackProducts()
    {
        $is_pack = "0";
        $pack_products = array();
        if (Pack::isPack($this->product->id)) {
            $is_pack = "1";
            $pack_items = Pack::getItemTable($this->product->id, $this->context->language->id, true);
            if ($pack_items) {
                $index = 0;
                foreach ($pack_items as $item) {
                    $pack_products[$index] = array(
                        'id' => $item['id_product'],
                        'is_in_wishlist' => $this->isProductHasInWishlist($item['id_product']),
                        'name' => $item['name'],
                        'price' => $this->formatPrice($item['price_without_reduction']),
                        'available_for_order' => $item['available_for_order'],
                        'show_price' => $item['show_price'],
                        'new_products' => (isset($item['new']) && $item['new'] == 1) ? "1" : "0",
                        'on_sale_products' => $item['on_sale'],
                        'pack_quantity' => $item['pack_quantity']
                    );
                    /* Changes started by rishabh jain on 3rd sep 2018
                    * Added urlencode perimeter in image link if enabled by admin
                    */
                    if (Configuration::get('KB_MOBILEAPP_URL_ENCODING') == 1) {
                        $pack_products[$index]['src']  =  $this->context->link->getImageLink(
                            urlencode($item['link_rewrite']),
                            $item['id_image'],
                            $this->getImageType('large')
                        );
                    } else {
                        $pack_products[$index]['src']  =  $this->context->link->getImageLink(
                            $item['link_rewrite'],
                            $item['id_image'],
                            $this->getImageType('large')
                        );
                    }
                    /* Changes over */

                    if (count($item['specific_prices']) > 0) {
                        $pack_products[$index]['discount_price'] = $this->formatPrice($item['price']);
                        if ($item['specific_prices']['reduction_type'] == parent::PRICE_REDUCTION_TYPE_PERCENT) {
                            $item[$index]['discount_percentage'] = (float) $item['specific_prices']['reduction'] * 100;
                        } else {
                            if ($item['price_without_reduction']) {
                                $temp_price = (float) ($item['specific_prices']['reduction'] * 100);
                                $percent = (float) ($temp_price / $item['price_without_reduction']);
                                unset($temp_price);
                            } else {
                                $percent = 0;
                            }
                            $pack_products[$index]['discount_percentage'] = Tools::ps_round($percent);
                        }
                    } else {
                        $pack_products[$index]['discount_price'] = '';
                        $pack_products[$index]['discount_percentage'] = '';
                    }
                    $index++;
                }
            }
        }
        return array('is_pack' => $is_pack, 'pack_items' => $pack_products);
    }
}

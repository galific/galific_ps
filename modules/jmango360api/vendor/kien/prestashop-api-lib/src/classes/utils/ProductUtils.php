<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class ProductUtils
{
    /**
     * Convert product group to return
     * @param $product
     * @param $id_lang
     * @param $buy_requests
     * @return array
     * @throws Exception
     */
    public static function convertAttributes($product, $id_lang, $buy_requests = array())
    {
        static $cache = array();

        $cache_id = ($product instanceof Product ? $product->id : $product) . '-' . $id_lang;
        if (isset($cache[$cache_id])) {
            return $cache[$cache_id];
        }

        $product = $product instanceof Product ? $product : new Product($product);
        $colors = array();
        $groups = array();
        $combinations = array();

        // @todo (RM) should only get groups and not all declination ?
        $attributes_groups = $product->getAttributesGroups($id_lang);

        if (is_array($attributes_groups) && $attributes_groups) {
            /**
             * Create array of products combinations
             */
            $products_attributes = array();
            foreach ($attributes_groups as $attributes_group) {
                $products_attributes[$attributes_group['id_product_attribute']][$attributes_group['id_attribute_group']] = $attributes_group['id_attribute'];
            }

            $combination_images = $product->getCombinationImages($id_lang);
            $combination_prices_set = array();

            foreach ($attributes_groups as $k => $row) {
                /**
                 * Skip product combination not in cart
                 */
                if (isset($products_attributes[$row['id_product_attribute']])
                    && count($buy_requests)
                    && !in_array($products_attributes[$row['id_product_attribute']], $buy_requests)
                ) {
                    continue;
                }

                /**
                 * Color management
                 */
                if ((isset($row['is_color_group']) && $row['is_color_group'])
                    && (isset($row['attribute_color']) && $row['attribute_color'])
                    || file_exists(_PS_COL_IMG_DIR_ . $row['id_attribute'] . '.jpg')
                ) {
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
                $groups[$row['id_attribute_group']]['attributes_quantity'][$row['id_attribute']] += (int)$row['quantity'];

                $combinations[$row['id_product_attribute']]['attributes_values'][$row['id_attribute_group']] = $row['attribute_name'];
                $combinations[$row['id_product_attribute']]['attributes'][] = (int)$row['id_attribute'];
                $combinations[$row['id_product_attribute']]['id_attributes'][$row['id_attribute_group']] = (int)$row['id_attribute'];
                $combinations[$row['id_product_attribute']]['price'] = (float)Tools::convertPriceFull($row['price'], null, Context::getContext()->currency, false);

                /**
                 * Call getPriceStatic in order to set $combination_specific_price
                 */
                $combination_specific_price = null;
                if (!isset($combination_prices_set[(int)$row['id_product_attribute']])) {
                    Product::getPriceStatic(
                        (int)$product->id,
                        false,
                        $row['id_product_attribute'],
                        6,
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
                $combinations[$row['id_product_attribute']]['reference'] = $row['reference'] == null ? '' : $row['reference'];
                $combinations[$row['id_product_attribute']]['unit_impact'] = Tools::convertPriceFull($row['unit_price_impact'], null, Context::getContext()->currency, false);
                $combinations[$row['id_product_attribute']]['minimal_quantity'] = $row['minimal_quantity'];
                if ($row['available_date'] != '0000-00-00' && Validate::isDate($row['available_date'])) {
                    $combinations[$row['id_product_attribute']]['available_date'] = $row['available_date'];
                    $combinations[$row['id_product_attribute']]['date_formatted'] = Tools::displayDate($row['available_date']);
                } else {
                    $combinations[$row['id_product_attribute']]['available_date'] = $combinations[$row['id_product_attribute']]['date_formatted'] = '';
                }

                if (!isset($combination_images[$row['id_product_attribute']][0]['id_image'])) {
                    $combinations[$row['id_product_attribute']]['id_image'] = -1;
                } else {
                    $combinations[$row['id_product_attribute']]['id_image'] = $id_image = (int)$combination_images[$row['id_product_attribute']][0]['id_image'];
                    if ($id_image > 0) {
                        $images = ProductDataTransform::productImages($id_image);
                        $combinations[$row['id_product_attribute']]['images'] = $images;
                    }
                }
            }

            /**
             * Wash attributes list (if some attributes are unavailables and if allowed to wash it)
             * PS-1250: Dont wash out-of-stock attributes
             */

            /**
             * Combine groups & colors into single model
             */
            if (count($colors)) {
                foreach ($groups as &$group) {
                    if ($group['group_type'] === 'color') {
                        foreach ($group['attributes'] as $key => &$color_key) {
                            foreach ($colors as $key => $value) {
                                $color_name = $value['name'];
                                $color_value = $value['value'];

                                if ($color_name === $color_key) {
                                    if (!isset($groups[$group['id_group']]['attributes_color'][$key])) {
                                        $groups[$group['id_group']]['attributes_color'][$key] = $color_value == null ? null : $color_value;
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

            $groupValues = array_values($groups);
            $combinationValues = array_values($combinations);

            $cache[$cache_id] = array($groupValues, $combinationValues);

            return array($groupValues, $combinationValues);
        }
    }
}

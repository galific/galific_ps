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
 * API to get list of filters
 */

require_once 'AppCore.php';

class AppGetFilters extends AppCore
{
    private $search_term = '';

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $proceed = true;
        if (!(int) Tools::getValue('category_id', 0) && !Tools::getIsset('search_term')) {
            $proceed = false;
            $this->content['filter_result'] = array(
                'status' => 'failure',
                'message' => parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Category id is missing'),
                    'AppGetFilters'
                )
            );
        } elseif (!(int) Tools::getValue('category_id', 0)) {
            if (!Tools::getIsset('search_term') || !Tools::getValue('search_term', '')) {
                $proceed = false;
                $this->content['filter_result'] = array(
                    'status' => 'failure',
                    'message' => parent::getTranslatedTextByFileAndISO(
                        Tools::getValue('iso_code', false),
                        $this->l('Search term is missing'),
                        'AppGetFilters'
                    )
                );
            } else {
                $this->search_term = Tools::getValue('search_term', '');
            }
        }

        $this->content['install_module'] = '';
        if ($proceed) {
            $this->content['filter_result'] = $this->getFilters();
        }

        return $this->fetchJSONContent();
    }

    /**
     * Get filters data
     *
     * @return array filters data
     */
    public function getFilters()
    {
        $filters = array();
        $filters = $this->getFilterBlock();
        return $filters;
    }

    /**
     * Get filters list
     *
     * @return array
     */
    public function getFilterBlock()
    {
        $id_lang = $this->context->language->id;
        $currency = $this->context->currency;
        $id_shop = (int) $this->context->shop->id;

        $product_ids = array();

        if (!empty($this->search_term)) {
            $products = Search::find(
                $this->context->language->id,
                $this->search_term,
                1,
                10000,
                'position',
                'desc',
                false,
                true,
                $this->context
            );
            if ((int) $products['total'] > 0) {
                foreach ($products['result'] as $prod) {
                    $product_ids[] = $prod['id_product'];
                }
            }
        } else {
            $home_category = Configuration::get('PS_HOME_CATEGORY');
            $id_parent = (int) Tools::getValue('category_id', $home_category);
            if ($id_parent == $home_category) {
                return array();
            }

            $qry = 'SELECT DISTINCT(cat_product.`id_product`) as id_product 
                FROM `' . _DB_PREFIX_ . 'category` c
                ' . Shop::addSqlAssociation('category', 'c') . ' 
                INNER JOIN ' . _DB_PREFIX_ . 'category_product as cat_product 
                on (c.id_category = cat_product.id_category)
                RIGHT JOIN `' . _DB_PREFIX_ . 'category` c2 ON
                c2.`id_category` = ' . (int) $id_parent . ' AND c.`nleft` >= c2.`nleft`
                AND c.`nright` <= c2.`nright` 
                WHERE 1 AND c.`active` = 1 GROUP BY cat_product.`id_product`,
                c.`level_depth` ASC, category_shop.`position` ASC';

            $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);

            if (!empty($products)) {
                foreach ($products as $prod) {
                    $product_ids[] = $prod['id_product'];
                }
            }
        }

        if (empty($product_ids)) {
            return array();
        }

        $filters = array();

        /* Attribute Filters */
        $attribute_qry = 'Select attr_grp.*, attr.id_attribute, attr.color, attr_grp_lang.name as '
                . 'grp_name, attr_lang.name as attr_name from ' . _DB_PREFIX_ . 'product_attribute as pro_attr '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_shop as pro_attr_shop '
                . 'on (pro_attr.id_product_attribute = pro_attr_shop.id_product_attribute '
                . 'AND pro_attr_shop.id_shop = ' . (int) $id_shop . ') '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_attribute_combination as pro_attr_com '
                . 'on (pro_attr.id_product_attribute = pro_attr_com.id_product_attribute) '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute as attr '
                . 'on (pro_attr_com.id_attribute = attr.id_attribute) '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute_group as attr_grp '
                . 'on (attr.id_attribute_group = attr_grp.id_attribute_group) '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute_group_lang as attr_grp_lang '
                . 'on (attr_grp.id_attribute_group = attr_grp_lang.id_attribute_group '
                . 'AND attr_grp_lang.id_lang = ' . (int) $id_lang . ') '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang as attr_lang '
                . 'on (attr.id_attribute = attr_lang.id_attribute '
                . 'AND attr_grp_lang.id_lang = ' . (int) $id_lang . ') '
                . 'WHERE 1 AND pro_attr.id_product IN (' . pSQL(implode(',', $product_ids)) . ') GROUP BY '
                . 'attr.id_attribute, attr_grp.id_attribute_group ORDER BY attr.position ASC, attr_grp.position ASC';

        $attribute_results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($attribute_qry);
        if (!empty($attribute_results)) {
            $attribute_filters = array();

            foreach ($attribute_results as $res) {
                $attribute_filters[$res['id_attribute_group']]['id'] = $res['id_attribute_group'];
                $attribute_filters[$res['id_attribute_group']]['name'] = 'id_attribute_group';
                $attribute_filters[$res['id_attribute_group']]['choice_type'] = 'multiple';
                $attribute_filters[$res['id_attribute_group']]['title'] = $res['grp_name'];

                $item = array(
                    'id' => $res['id_attribute'],
                    'name' => $res['attr_name']
                );

                if ($res['is_color_group']) {
                    $attribute_filters[$res['id_attribute_group']]['is_color_group'] = 1;
                    $item['color_value'] = $res['color'];
                } else {
                    $attribute_filters[$res['id_attribute_group']]['is_color_group'] = 0;
                    $item['color_value'] = '';
                }
                $attribute_filters[$res['id_attribute_group']]['items'][] = $item;
            }

            $filters = array_merge($filters, $attribute_filters);
        }

        /* Features Filters */
        $features_qry = 'Select feature.*, feature_lang.name as grp_name, fval_lang.value as f_name, '
                . 'pro_feature.id_feature_value from ' . _DB_PREFIX_ . 'feature_product as pro_feature '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'feature as feature on '
                . '(pro_feature.id_feature = feature.id_feature) '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'feature_shop as feature_shop on '
                . '(feature.id_feature = feature_shop.id_feature '
                . 'AND feature_shop.id_shop = ' . (int) $id_shop . ') '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'feature_lang as feature_lang on '
                . '(feature.id_feature = feature_lang.id_feature AND feature_lang.id_lang = ' . (int) $id_lang . ') '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'feature_value_lang as fval_lang on '
                . '(pro_feature.id_feature_value = fval_lang.id_feature_value '
                . 'AND fval_lang.id_lang = ' . (int) $id_lang . ') '
                . 'WHERE 1 AND pro_feature.id_product IN (' . pSQL(implode(',', $product_ids)) . ') '
                . 'GROUP BY pro_feature.id_feature_value, pro_feature.id_feature ORDER BY feature.position ASC';
        $features_results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($features_qry);

        if (!empty($features_results)) {
            $features_filters = array();

            foreach ($features_results as $res) {
                $features_filters[$res['id_feature']]['id'] = $res['id_feature'];
                $features_filters[$res['id_feature']]['name'] = 'id_feature';
                $features_filters[$res['id_feature']]['choice_type'] = 'multiple';
                $features_filters[$res['id_feature']]['title'] = $res['grp_name'];
                $features_filters[$res['id_feature']]['is_color_group'] = 0;

                $item = array(
                    'id' => $res['id_feature_value'],
                    'name' => $res['f_name']
                );
                $features_filters[$res['id_feature']]['items'][] = $item;
            }

            $filters = array_merge($filters, $features_filters);
        }

        /*Conditions */
        $qry = 'SELECT DISTINCT(p.condition) as p_condition from ' . _DB_PREFIX_ . 'product as p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_shop as ps '
                . 'on (p.id_product = ps.id_product AND ps.id_shop = ' . (int) $id_shop . ') '
                . 'WHERE p.id_product IN (' . pSQL(implode(',', $product_ids)) . ')';
        $condition_results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
        if (!empty($condition_results)) {
            $items = array();
            foreach ($condition_results as $res) {
                if (empty($res['p_condition'])) {
                    continue;
                }
                $item = array(
                    'id' => $res['p_condition'],
                    'name' => Tools::ucfirst($res['p_condition'])
                );
                $items[] = $item;
            }
            if (!empty($items)) {
                $filter = array();
                $filter['id'] = 0;
                $filter['name'] = 'condition';
                $filter['choice_type'] = 'multiple';
                $filter['title'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Condition'),
                    'AppGetFilters'
                );
                $filter['is_color_group'] = 0;
                $filter['items'] = $items;
                $filters = array_merge($filters, array($filter));
            }
        }

        /* Manufacturers */
        $qry = 'SELECT m.id_manufacturer, m.name from ' . _DB_PREFIX_ . 'product as p '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'product_shop as ps '
                . 'on (p.id_product = ps.id_product AND ps.id_shop = ' . (int) $id_shop . ') '
                . 'INNER JOIN ' . _DB_PREFIX_ . 'manufacturer as m on '
                . '(p.id_manufacturer = m.id_manufacturer AND m.active = 1) '
                . 'WHERE p.id_product IN (' . pSQL(implode(',', $product_ids)) . ') GROUP BY m.id_manufacturer';
        $manufacturer_results = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($qry);
        if (!empty($manufacturer_results)) {
            $items = array();
            foreach ($manufacturer_results as $res) {
                $item = array(
                    'id' => $res['id_manufacturer'],
                    'name' => Tools::ucfirst($res['name'])
                );
                $items[] = $item;
            }
            if (!empty($items)) {
                $filter = array();
                $filter['id'] = 0;
                $filter['name'] = 'manufacturer';
                $filter['choice_type'] = 'multiple';
                $filter['title'] = parent::getTranslatedTextByFileAndISO(
                    Tools::getValue('iso_code', false),
                    $this->l('Manufacturer'),
                    'AppGetFilters'
                );
                $filter['is_color_group'] = 0;
                $filter['items'] = $items;
                $filters = array_merge($filters, array($filter));
            }
        }

        if (!Module::isInstalled('blocklayered') || !Module::isEnabled('blocklayered')) {
            return $filters;
        }

        $qry = 'SELECT MIN(price_min) as min_price, MAX(price_max) as max_price '
                . 'from ' . _DB_PREFIX_ . 'layered_price_index '
                . 'WHERE id_currency = ' . (int) $currency->id . ' AND '
                . 'id_shop = ' . (int) $id_shop . ' AND id_product IN (' . pSQL(implode(',', $product_ids)) . ')';
        $prices = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($qry);
        if (!empty($prices) && ($prices['min_price'] > 0 && $prices['max_price'] > 0)) {
            $filter = array();
            $filter['id'] = 0;
            $filter['name'] = 'price';
            $filter['choice_type'] = 'radio';
            $filter['title'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Price'),
                'AppGetFilters'
            );
            $filter['is_color_group'] = 0;
            $filter['items'] = $this->getFilterPriceItems($prices['min_price'], $prices['max_price']);
            ;
            $filters = array_merge($filters, array($filter));
        }
        return $filters;
    }

    /**
     * Get price filter items
     *
     * @param float $min_price minimum price value
     * @param float $max_price maximum price value
     * @return array
     */
    public function getFilterPriceItems($min_price, $max_price)
    {
        if ($min_price <= 0 && $max_price <= 0) {
            return array();
        } elseif ($min_price >= $max_price) {
            return array();
        }

        $differnce = $max_price - $min_price;
        if ($differnce <= 30) {
            return array(
                array(
                    'id' => $min_price . '_' . $max_price,
                    'name' => $this->formatPrice($min_price) . ' - ' . $this->formatPrice($max_price)
                )
            );
        } else {
            $range = (float) ($differnce / 4);
            $range = Tools::ps_round($range);
            $arr = array();
            $range_min = $min_price;
            for ($i = 0; $i < 4; $i++) {
                if ($i == 0) {
                    $arr[] = array(
                        'id' => $range_min . '_' . floor($range_min + $range),
                        'name' => $this->formatPrice($range_min).' - '.$this->formatPrice(floor($range_min + $range))
                    );

                    $range_min = ceil($range_min + $range);
                } elseif ($i == 3) {
                    $arr[] = array(
                        'id' => $range_min . '_' . $max_price,
                        'name' => $this->formatPrice($range_min) . ' - ' . $this->formatPrice($max_price)
                    );

                    $range_min = ceil($range_min + $range);
                } else {
                    $arr[] = array(
                        'id' => $range_min . '_' . floor($range_min + $range),
                        'name' => $this->formatPrice($range_min).' - '.$this->formatPrice(floor($range_min + $range))
                    );

                    $range_min = ceil($range_min + $range);
                }
            }
            return $arr;
        }
    }
}

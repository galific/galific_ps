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
 */

class AppSearchedProducts
{

    private $core = null;
    private $context = null;
    private $skipped_filters = array('quantity', 'price', 'weight');

    public function __construct($core_obj)
    {
        $this->core = $core_obj;

        $this->context = $this->core->context;
    }

    /**
     * Get product list of selected category
     * @param string $search_term search term string
     * @param array $fiters selected filters array
     *
     * @return array products data
     */
    public function getProducts($search_term = '', $fiters = array())
    {
        $filter_string = '';
        if (!empty($fiters)) {
            foreach ($fiters as $grp => $filter) {
                if (in_array($grp, $this->skipped_filters)) {
                    continue;
                }

                switch ($grp) {
                    case 'id_attribute_group':
                        foreach ($filter as $value) {
                            $tmp_value = explode('_', $value);
                            $qry = 'SELECT al.name from ' . _DB_PREFIX_ . 'attribute as a '
                                    . 'INNER JOIN ' . _DB_PREFIX_ . 'attribute_lang as al on '
                                    . '(a.id_attribute = al.id_attribute AND '
                                    . 'al.id_lang = ' . (int) $this->context->language->id . ')'
                                    . ' WHERE a.id_attribute = ' . (int) $tmp_value[1] . ' AND '
                                    . 'a.id_attribute_group = ' . (int) $tmp_value[0];
                            $search_keyword = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($qry);
                            $filter_string .= ' ' . $search_keyword;
                        }
                        $filter_string = trim($filter_string);
                        break;
                    case 'id_feature':
                        foreach ($filter as $value) {
                            $tmp_value = explode('_', $value);
                            $qry = 'SELECT al.value from ' . _DB_PREFIX_ . 'feature_value as a '
                                    . 'INNER JOIN ' . _DB_PREFIX_ . 'feature_value_lang as al on '
                                    . '(a.id_feature_value = al.id_feature_value AND '
                                    . 'al.id_lang = ' . (int) $this->context->language->id . ')'
                                    . ' WHERE a.id_feature_value = ' . (int) $tmp_value[1] . ' AND '
                                    . 'a.id_feature = ' . (int) $tmp_value[0];

                            $search_keyword = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($qry);
                            $filter_string .= ' ' . $search_keyword;
                        }
                        $filter_string = trim($filter_string);
                        break;
                    case 'condition':
                        foreach ($filter as $value) {
                            $filter_string .= ' ' . $value;
                        }
                        $filter_string = trim($filter_string);
                        break;
                }
            }
        }
        $search_term .= ' ' . $filter_string;
        $search_term = trim($search_term);

        if (!empty($search_term)) {
            $products = Search::find(
                $this->context->language->id,
                $search_term,
                $this->core->page_number,
                $this->core->limit,
                $this->core->order_by,
                $this->core->order_way,
                false,
                true,
                $this->context
            );
            if ($products['total'] > 0) {
                $products = $products['result'];
            } else {
                $products = array();
            }
        } else {
            $products = array();
        }

        return $products;
    }
}

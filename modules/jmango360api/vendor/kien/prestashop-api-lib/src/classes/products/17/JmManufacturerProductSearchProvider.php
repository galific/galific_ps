<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchProviderInterface;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResult;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrderFactory;
use Symfony\Component\Translation\TranslatorInterface;

class JmManufacturerProductSearchProvider implements ProductSearchProviderInterface
{
    private $translator;
    private $manufacturer;
    private $sortOrderFactory;
    private $jmProductSearchProvider;

    public function __construct(
        TranslatorInterface $translator,
        Manufacturer $manufacturer
    ) {
        $this->translator = $translator;
        $this->manufacturer = $manufacturer;
        $this->sortOrderFactory = new SortOrderFactory($this->translator);
        $this->jmProductSearchProvider = new JmProductSearchProvider();
    }

    private function getProductsOrCount(
        ProductSearchContext $context,
        ProductSearchQuery $query,
        $type = 'products'
    ) {
        return $this->getProducts(
            $this->manufacturer->id,
            $context->getIdLang(),
            $query->getPage(),
            $query->getResultsPerPage(),
            $query->getSortOrder()->toLegacyOrderBy(),
            $query->getSortOrder()->toLegacyOrderWay(),
            $type !== 'products'
        );
    }

    public function runQuery(
        ProductSearchContext $context,
        ProductSearchQuery $query
    ) {
        $products = $this->getProductsOrCount($context, $query, 'products');
        $count = $this->getProductsOrCount($context, $query, 'count');

        $result = new ProductSearchResult();

        if (!empty($products)) {
            $result
                ->setProducts($products)
                ->setTotalProductsCount($count);

            $result->setAvailableSortOrders(
                $this->jmProductSearchProvider->getAvailableSortOrders()
            );
        }

        return $result;
    }

    public static function getProducts(
        $idManufacturer,
        $idLang,
        $p,
        $n,
        $orderBy = null,
        $orderWay = null,
        $getTotal = false,
        $active = true,
        $activeCategory = true,
        Context $context = null
    ) {
        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;

        if ($p < 1) {
            $p = 1;
        }

        if (empty($orderBy) || $orderBy == 'position') {
            $orderBy = 'name';
        }

        if (empty($orderWay)) {
            $orderWay = 'ASC';
        }

        if (!Validate::isOrderBy($orderBy) || !Validate::isOrderWay($orderWay)) {
            die(Tools::displayError());
        }

        $groups = FrontController::getCurrentCustomerGroups();
        $sqlGroups = count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1';

        /* Return only the number of products */
        if ($getTotal) {
            $sql = '
				SELECT p.`id_product`
				FROM `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE p.id_manufacturer = '.(int) $idManufacturer
                .($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				AND EXISTS (
					SELECT 1
					FROM `'._DB_PREFIX_.'category_group` cg
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON (cp.`id_category` = cg.`id_category`)'.
                ($activeCategory ? ' INNER JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1' : '').'
					WHERE p.`id_product` = cp.`id_product` AND cg.`id_group` '.$sqlGroups.'
				)';

            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
            return (int) count($result);
        }
        if (strpos($orderBy, '.') > 0) {
            $orderBy = explode('.', $orderBy);
            $orderBy = pSQL($orderBy[0]).'.`'.pSQL($orderBy[1]).'`';
        }

        if ($orderBy == 'price') {
            $alias = 'product_shop.';
        } elseif ($orderBy == 'name') {
            $alias = 'pl.';
        } elseif ($orderBy == 'manufacturer_name') {
            $orderBy = 'name';
            $alias = 'm.';
        } elseif ($orderBy == 'quantity') {
            $alias = 'stock.';
        } else {
            $alias = 'p.';
        }

        // Query Hidden pack products
        $hidden_pack_product = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT(`id_product_pack`)
				FROM `' . _DB_PREFIX_ . 'pack`
				LEFT JOIN ' . _DB_PREFIX_ . 'jm_product_visibility ON ' . _DB_PREFIX_ . 'jm_product_visibility.id_product = ' . _DB_PREFIX_ . 'pack.id_product_item
				WHERE ' . _DB_PREFIX_ . 'jm_product_visibility.not_visible = 2');

        $hidden_pack_product_ids = array();
        foreach ($hidden_pack_product as $pack) {
            $hidden_pack_product_ids[] = $pack['id_product_pack'];
        }

        $sql = 'SELECT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity'
            .(Combination::isFeatureActive() ? ', product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.`id_product_attribute`,0) id_product_attribute' : '').'
			, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`,
			pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
				DATEDIFF(
					product_shop.`date_add`,
					DATE_SUB(
						"'.date('Y-m-d').' 00:00:00",
						INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
					)
				) > 0 AS new'
            .' FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p').
            (Combination::isFeatureActive() ? 'LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
						ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')':'').'
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
				ON (p.`id_product` = pl.`id_product` AND pl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il
				ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $idLang.')
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
				ON (m.`id_manufacturer` = p.`id_manufacturer`)
			'.Product::sqlStock('p', 0);

        if (Group::isFeatureActive() || $activeCategory) {
            $sql .= 'JOIN `'._DB_PREFIX_.'category_product` cp ON (p.id_product = cp.id_product)';
            if (Group::isFeatureActive()) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.`id_category` = cg.`id_category` AND cg.`id_group` '.$sqlGroups.')';
            }
            if ($activeCategory) {
                $sql .= 'JOIN `'._DB_PREFIX_.'category` ca ON cp.`id_category` = ca.`id_category` AND ca.`active` = 1';
            }
        }

        $sql .= '
				WHERE p.`id_manufacturer` = '.(int) $idManufacturer.'
				'.($active ? ' AND product_shop.`active` = 1' : '').'
				'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
				' . (empty($hidden_pack_product_ids) ? '' : 'AND p.`id_product` NOT IN ('.implode(',', $hidden_pack_product_ids).')') . '
				GROUP BY p.id_product
				ORDER BY '.$alias.'`'.bqSQL($orderBy).'` '.pSQL($orderWay).'
				LIMIT '.(((int) $p - 1) * (int) $n).','.(int) $n;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        // Query Hidden pack products
        $hidden_products_result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT(`id_product`)
				FROM `' . _DB_PREFIX_ . 'jm_product_visibility`
				WHERE (`not_visible` = 2)');

        $hidden_products = array_map(function ($product) {
            return (int) $product['id_product'];
        }, $hidden_products_result);

        $final_result = array();
        foreach ($result as $res) {
            if (in_array((int)$res['id_product'], $hidden_products)) {
                continue;
            }
            $final_result[] = $res;
        }

        if ($orderBy == 'price') {
            Tools::orderbyPrice($final_result, $orderWay);
        }

        return Product::getProductsProperties($idLang, $final_result);
    }
}

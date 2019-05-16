<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class Cart extends CartCore
{

    /**
     * Return shipping total for the cart
     *
     * @param array|null $delivery_option Array of the delivery option for each address
     * @param bool $use_tax
     * @param Country|null $default_country
     * @return float Shipping total
     */
    public function getTotalShippingCostSelected($delivery_option = null, $use_tax = true, Country $default_country = null, $package_list)
    {
        if (isset(Context::getContext()->cookie->id_country)) {
            $default_country = new Country(Context::getContext()->cookie->id_country);
        }
        if (is_null($delivery_option)) {
            $delivery_option = $this->getDeliveryOptionSelectedSeller($package_list, $default_country, false, false);
        }

        $total_shipping = 0;
        $delivery_option_list = $this->getDeliveryOptionListSelectedPackage($package_list, $default_country, false);

        //print_r($delivery_option_list);

        foreach ($delivery_option as $id_address => $key) {
            if (!isset($delivery_option_list[$id_address]) || !isset($delivery_option_list[$id_address][$key])) {
                continue;
            }
            if ($use_tax) {
                $total_shipping += $delivery_option_list[$id_address][$key]['total_price_with_tax'];
            } else {
                $total_shipping += $delivery_option_list[$id_address][$key]['total_price_without_tax'];
            }
        }

        return $total_shipping;
    }

    /**
     * Get all deliveries options available for the current cart
     * @param Country $default_country
     * @param bool $flush Force flushing cache
     *
     * @return array array(
     *                   0 => array( // First address
     *                       '12,' => array(  // First delivery option available for this address
     *                           carrier_list => array(
     *                               12 => array( // First carrier for this option
     *                                   'instance' => Carrier Object,
     *                                   'logo' => <url to the carriers logo>,
     *                                   'price_with_tax' => 12.4,
     *                                   'price_without_tax' => 12.4,
     *                                   'package_list' => array(
     *                                       1,
     *                                       3,
     *                                   ),
     *                               ),
     *                           ),
     *                           is_best_grade => true, // Does this option have the biggest grade (quick shipping) for this shipping address
     *                           is_best_price => true, // Does this option have the lower price for this shipping address
     *                           unique_carrier => true, // Does this option use a unique carrier
     *                           total_price_with_tax => 12.5,
     *                           total_price_without_tax => 12.5,
     *                           position => 5, // Average of the carrier position
     *                       ),
     *                   ),
     *               );
     *               If there are no carriers available for an address, return an empty  array
     */
    public function getDeliveryOptionListSelectedPackage($package_list, Country $default_country = null, $flush = false)
    {
        static $cache = array();
 
        $delivery_option_list = array();
        $carriers_price = array();
        $carrier_collection = array();

        // Foreach addresses
        foreach ($package_list as $id_address => $packages) {
            // Initialize vars
            $delivery_option_list[$id_address] = array();
            $carriers_price[$id_address] = array();
            $common_carriers = null;
            $best_price_carriers = array();
            $best_grade_carriers = array();
            $carriers_instance = array();

            // Get country
            if ($id_address) {
                $address = new Address($id_address);
                $country = new Country($address->id_country);
            } else {
                $country = $default_country;
            }

            // Foreach packages, get the carriers with best price, best position and best grade
            foreach ($packages as $id_package => $package) {
                // No carriers available
                if (count($packages) == 1 && count($package['carrier_list']) == 1 && current($package['carrier_list']) == 0) {
                    $cache[$this->id] = array();
                    return $cache[$this->id];
                }

                $carriers_price[$id_address][$id_package] = array();

                // Get all common carriers for each packages to the same address
                if (is_null($common_carriers)) {
                    $common_carriers = $package['carrier_list'];
                } else {
                    $common_carriers = array_intersect($common_carriers, $package['carrier_list']);
                }

                $best_price = null;
                $best_price_carrier = null;
                $best_grade = null;
                $best_grade_carrier = null;

                // Foreach carriers of the package, calculate his price, check if it the best price, position and grade
                foreach ($package['carrier_list'] as $id_carrier) {
                    if (!isset($carriers_instance[$id_carrier])) {
                        $carriers_instance[$id_carrier] = new Carrier($id_carrier);
                    }

                    $price_with_tax = $this->getPackageShippingCost((int) $id_carrier, true, $country, $package['product_list']);
                    $price_without_tax = $this->getPackageShippingCost((int) $id_carrier, false, $country, $package['product_list']);
                    if (is_null($best_price) || $price_with_tax < $best_price) {
                        $best_price = $price_with_tax;
                        $best_price_carrier = $id_carrier;
                    }
                    $carriers_price[$id_address][$id_package][$id_carrier] = array(
                        'without_tax' => $price_without_tax,
                        'with_tax' => $price_with_tax);

                    $grade = $carriers_instance[$id_carrier]->grade;
                    if (is_null($best_grade) || $grade > $best_grade) {
                        $best_grade = $grade;
                        $best_grade_carrier = $id_carrier;
                    }
                }

                $best_price_carriers[$id_package] = $best_price_carrier;
                $best_grade_carriers[$id_package] = $best_grade_carrier;
            }

            // Reset $best_price_carrier, it's now an array
            $best_price_carrier = array();
            $key = '';

            // Get the delivery option with the lower price
            foreach ($best_price_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_price_carrier[$id_carrier])) {
                    $best_price_carrier[$id_carrier] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                }
                $best_price_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_price_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_price_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_price_carrier[$id_carrier]['product_list'] = array_merge($best_price_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_price_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
                $real_best_price = !isset($real_best_price) || $real_best_price > $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] ?
                        $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'] : $real_best_price;
                $real_best_price_wt = !isset($real_best_price_wt) || $real_best_price_wt > $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] ?
                        $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'] : $real_best_price_wt;
            }

            // Add the delivery option with best price as best price
            $delivery_option_list[$id_address][$key] = array(
                'carrier_list' => $best_price_carrier,
                'is_best_price' => true,
                'is_best_grade' => false,
                'unique_carrier' => (count($best_price_carrier) <= 1)
            );

            // Reset $best_grade_carrier, it's now an array
            $best_grade_carrier = array();
            $key = '';

            // Get the delivery option with the best grade
            foreach ($best_grade_carriers as $id_package => $id_carrier) {
                $key .= $id_carrier . ',';
                if (!isset($best_grade_carrier[$id_carrier])) {
                    $best_grade_carrier[$id_carrier] = array(
                        'price_with_tax' => 0,
                        'price_without_tax' => 0,
                        'package_list' => array(),
                        'product_list' => array(),
                    );
                }
                $best_grade_carrier[$id_carrier]['price_with_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                $best_grade_carrier[$id_carrier]['price_without_tax'] += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                $best_grade_carrier[$id_carrier]['package_list'][] = $id_package;
                $best_grade_carrier[$id_carrier]['product_list'] = array_merge($best_grade_carrier[$id_carrier]['product_list'], $packages[$id_package]['product_list']);
                $best_grade_carrier[$id_carrier]['instance'] = $carriers_instance[$id_carrier];
            }

            // Add the delivery option with best grade as best grade
            if (!isset($delivery_option_list[$id_address][$key])) {
                $delivery_option_list[$id_address][$key] = array(
                    'carrier_list' => $best_grade_carrier,
                    'is_best_price' => false,
                    'unique_carrier' => (count($best_grade_carrier) <= 1)
                );
            }
            $delivery_option_list[$id_address][$key]['is_best_grade'] = true;

            // Get all delivery options with a unique carrier
            foreach ($common_carriers as $id_carrier) {
                $key = '';
                $package_list = array();
                $product_list = array();
                $price_with_tax = 0;
                $price_without_tax = 0;

                foreach ($packages as $id_package => $package) {
                    $key .= $id_carrier . ',';
                    $price_with_tax += $carriers_price[$id_address][$id_package][$id_carrier]['with_tax'];
                    $price_without_tax += $carriers_price[$id_address][$id_package][$id_carrier]['without_tax'];
                    $package_list[] = $id_package;
                    $product_list = array_merge($product_list, $package['product_list']);
                }

                if (!isset($delivery_option_list[$id_address][$key])) {
                    $delivery_option_list[$id_address][$key] = array(
                        'is_best_price' => false,
                        'is_best_grade' => false,
                        'unique_carrier' => true,
                        'carrier_list' => array(
                            $id_carrier => array(
                                'price_with_tax' => $price_with_tax,
                                'price_without_tax' => $price_without_tax,
                                'instance' => $carriers_instance[$id_carrier],
                                'package_list' => $package_list,
                                'product_list' => $product_list,
                            )
                        )
                    );
                } else {
                    $delivery_option_list[$id_address][$key]['unique_carrier'] = (count($delivery_option_list[$id_address][$key]['carrier_list']) <= 1);
                }
            }
        }

        $cart_rules = CartRule::getCustomerCartRules(Context::getContext()->cookie->id_lang, Context::getContext()->cookie->id_customer, true, true, false, $this, true);

        $result = false;
        if ($this->id) {
            $result = Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart = ' . (int) $this->id);
        }

        $cart_rules_in_cart = array();

        if (is_array($result)) {
            foreach ($result as $row) {
                $cart_rules_in_cart[] = $row['id_cart_rule'];
            }
        }

        $total_products_wt = $this->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $total_products = $this->getOrderTotal(false, Cart::ONLY_PRODUCTS);

        $free_carriers_rules = array();

        $context = Context::getContext();
        foreach ($cart_rules as $cart_rule) {
            $total_price = $cart_rule['minimum_amount_tax'] ? $total_products_wt : $total_products;
            $total_price += $cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price : 0;
            $total_price +=!$cart_rule['minimum_amount_tax'] && $cart_rule['minimum_amount_shipping'] ? $real_best_price_wt : 0;
            if ($cart_rule['free_shipping'] && $cart_rule['carrier_restriction'] && in_array($cart_rule['id_cart_rule'], $cart_rules_in_cart) && $cart_rule['minimum_amount'] <= $total_price) {
                $cr = new CartRule((int) $cart_rule['id_cart_rule']);
                if (Validate::isLoadedObject($cr) &&
                        $cr->checkValidity($context, in_array((int) $cart_rule['id_cart_rule'], $cart_rules_in_cart), false, false)) {
                    $carriers = $cr->getAssociatedRestrictions('carrier', true, false);
                    if (is_array($carriers) && count($carriers) && isset($carriers['selected'])) {
                        foreach ($carriers['selected'] as $carrier) {
                            if (isset($carrier['id_carrier']) && $carrier['id_carrier']) {
                                $free_carriers_rules[] = (int) $carrier['id_carrier'];
                            }
                        }
                    }
                }
            }
        }

        // For each delivery options :
        //    - Set the carrier list
        //    - Calculate the price
        //    - Calculate the average position
        foreach ($delivery_option_list as $id_address => $delivery_option) {
            foreach ($delivery_option as $key => $value) {
                $total_price_with_tax = 0;
                $total_price_without_tax = 0;
                $position = 0;
                foreach ($value['carrier_list'] as $id_carrier => $data) {
                    $total_price_with_tax += $data['price_with_tax'];
                    $total_price_without_tax += $data['price_without_tax'];
                    $total_price_without_tax_with_rules = (in_array($id_carrier, $free_carriers_rules)) ? 0 : $total_price_without_tax;

                    if (!isset($carrier_collection[$id_carrier])) {
                        $carrier_collection[$id_carrier] = new Carrier($id_carrier);
                    }
                    $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['instance'] = $carrier_collection[$id_carrier];

                    if (file_exists(_PS_SHIP_IMG_DIR_ . $id_carrier . '.jpg')) {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = _THEME_SHIP_DIR_ . $id_carrier . '.jpg';
                    } else {
                        $delivery_option_list[$id_address][$key]['carrier_list'][$id_carrier]['logo'] = false;
                    }

                    $position += $carrier_collection[$id_carrier]->position;
                }
                $delivery_option_list[$id_address][$key]['total_price_with_tax'] = $total_price_with_tax;
                $delivery_option_list[$id_address][$key]['total_price_without_tax'] = $total_price_without_tax;
                $delivery_option_list[$id_address][$key]['is_free'] = !$total_price_without_tax_with_rules ? true : false;
                $delivery_option_list[$id_address][$key]['position'] = $position / count($value['carrier_list']);
            }
        }

        // Sort delivery option list
        foreach ($delivery_option_list as &$array) {
            uasort($array, array('Cart', 'sortDeliveryOptionList'));
        }

        $cache[$this->id] = $delivery_option_list;
        return $cache[$this->id];
    }

    /**
     * Get the delivery option selected, or if no delivery option was selected,
     * the cheapest option for each address
     *
     * @param Country|null $default_country
     * @param bool         $dontAutoSelectOptions
     * @param bool         $use_cache
     *
     * @return array|bool|mixed Delivery option
     */
    public function getDeliveryOptionSelectedSeller($package_list, $default_country = null, $dontAutoSelectOptions = false, $use_cache = true)
    {
        static $cache = array();
 
        $delivery_option_list = $this->getDeliveryOptionListSelectedPackage($package_list, $default_country, false);

        // The delivery option was selected
        if (isset($this->delivery_option) && $this->delivery_option != '') {
            $delivery_option = Tools::unSerialize($this->delivery_option);
            $validated = true;
            foreach ($delivery_option as $id_address => $key) {
                if (!isset($delivery_option_list[$id_address][$key])) {
                    $validated = false;
                    break;
                }
            }

            if ($validated) {
                return $delivery_option;
            }
        }

        if ($dontAutoSelectOptions) {
            return false;
        }

        // No delivery option selected or delivery option selected is not valid, get the better for all options
        $delivery_option = array();
        foreach ($delivery_option_list as $id_address => $options) {
            foreach ($options as $key => $option) {
                if (Configuration::get('PS_CARRIER_DEFAULT') == -1 && $option['is_best_price']) {
                    $delivery_option[$id_address] = $key;
                    break;
                } elseif (Configuration::get('PS_CARRIER_DEFAULT') == -2 && $option['is_best_grade']) {
                    $delivery_option[$id_address] = $key;
                    break;
                } elseif ($option['unique_carrier'] && in_array(Configuration::get('PS_CARRIER_DEFAULT'), array_keys($option['carrier_list']))) {
                    $delivery_option[$id_address] = $key;
                    break;
                }
            }

            reset($options);
            if (!isset($delivery_option[$id_address])) {
                $delivery_option[$id_address] = key($options);
            }
        }

        return $delivery_option;
    }
}

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
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class Hook extends HookCore
{

    /**
     * Get list of modules we can execute per hook
     *
     * @since 1.5.0
     * @param string $hook_name Get list of modules for this hook if given
     * @return array
     */
    public static function getHookModuleExecList($hook_name = null)
    {
        $context = Context::getContext();
        $cache_id = self::MODULE_LIST_BY_HOOK_KEY . (isset($context->shop->id) ? '_' . $context->shop->id : '') . ((isset($context->customer)) ? '_' . $context->customer->id : '');
        if (!Cache::isStored($cache_id) || $hook_name == 'displayPayment' || $hook_name == 'displayPaymentEU' || $hook_name == 'paymentOptions' || $hook_name == 'displayBackOfficeHeader') {
            $frontend = true;
            $groups = array();
            $use_groups = Group::isFeatureActive();
            if (isset($context->employee)) {
                $frontend = false;
            } else {
                // Get groups list
                if ($use_groups) {
                    if (isset($context->customer) && $context->customer->isLogged()) {
                        $groups = $context->customer->getGroups();
                    } elseif (isset($context->customer) && $context->customer->isLogged(true)) {
                        $groups = array(
                            (int) Configuration::get('PS_GUEST_GROUP'));
                    } else {
                        $groups = array(
                            (int) Configuration::get('PS_UNIDENTIFIED_GROUP'));
                    }
                }
            }

            // SQL Request
            $sql = new DbQuery();
            $sql->select('h.`name` as hook, m.`id_module`, h.`id_hook`, m.`name` as module');
            $sql->from('module', 'm');
            if ($hook_name != 'displayBackOfficeHeader') {
                $sql->join(Shop::addSqlAssociation('module', 'm', true, 'module_shop.enable_device & ' . (int) Context::getContext()->getDevice()));
                $sql->innerJoin('module_shop', 'ms', 'ms.`id_module` = m.`id_module`');
            }
            $sql->innerJoin('hook_module', 'hm', 'hm.`id_module` = m.`id_module`');
            $sql->innerJoin('hook', 'h', 'hm.`id_hook` = h.`id_hook`');
            if ($hook_name != 'paymentOptions') {
                $sql->where('h.`name` != "paymentOptions"');
            } elseif ($frontend) {
                // For payment modules, we check that they are available in the contextual country
                if (Validate::isLoadedObject($context->country)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions")AND (SELECT `id_country` FROM `' . _DB_PREFIX_ . 'module_country` mc WHERE mc.`id_module` = m.`id_module` AND `id_country` = ' . (int) $context->country->id . ' AND `id_shop` = ' . (int) $context->shop->id . ' LIMIT 1) = ' . (int) $context->country->id . ')');
                }
                if (Validate::isLoadedObject($context->currency)) {
                    $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions") AND (SELECT `id_currency` FROM `' . _DB_PREFIX_ . 'module_currency` mcr WHERE mcr.`id_module` = m.`id_module` AND `id_currency` IN (' . (int) $context->currency->id . ', -1, -2) LIMIT 1) IN (' . (int) $context->currency->id . ', -1, -2))');
                }
                if (Validate::isLoadedObject($context->cart)) {
                    $carrier = new Carrier($context->cart->id_carrier);
                    if (Validate::isLoadedObject($carrier)) {
                        $sql->where('((h.`name` = "displayPayment" OR h.`name` = "displayPaymentEU" OR h.`name` = "paymentOptions") AND (SELECT `id_reference` FROM `' . _DB_PREFIX_ . 'module_carrier` mcar WHERE mcar.`id_module` = m.`id_module` AND `id_reference` = ' . (int) $carrier->id_reference . ' AND `id_shop` = ' . (int) $context->shop->id . ' LIMIT 1) = ' . (int) $carrier->id_reference . ')');
                    }
                }
            }
            if (Validate::isLoadedObject($context->shop)) {
                $sql->where('hm.`id_shop` = ' . (int) $context->shop->id);
            }

            if ($frontend) {
                if ($use_groups) {
                    $sql->leftJoin('module_group', 'mg', 'mg.`id_module` = m.`id_module`');
                    if (Validate::isLoadedObject($context->shop)) {
                        $sql->where('mg.id_shop = ' . ((int) $context->shop->id) . (count($groups) ? ' AND  mg.`id_group` IN (' . implode(', ', $groups) . ')' : ''));
                    } elseif (count($groups)) {
                        $sql->where('mg.`id_group` IN (' . implode(', ', $groups) . ')');
                    }
                }
            }

            $sql->groupBy('hm.id_hook, hm.id_module');
            $sql->orderBy('hm.`position`');

            $list = array();
            if ($result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql)) {
                foreach ($result as $row) {
                    /* Changes started by rishabh jain on 3rd sep
                     * To remove the display override template hok from the execution list 
                     * for modules other than kbmobileapp only if the mobile app cookie is set
                     */
                    if (isset($context->cookie->kbmobileapp) && $context->cookie->kbmobileapp) {
                        if (Module::isInstalled('kbmobileapp')) {
                            $mobileapp_module_id = Module::getModuleIdByName('kbmobileapp');
                            if ($mobileapp_module_id != $row['id_module']) {
                                $override_hook = Hook::getIdByName('displayOverrideTemplate');
                                if ($override_hook == $row['id_hook']) {
                                    continue;
                                }
                            }
                        }
                    }
                    /* changes over */
                    $row['hook'] = Tools::strtolower($row['hook']);
                    if (!isset($list[$row['hook']])) {
                        $list[$row['hook']] = array();
                    }

                    $list[$row['hook']][] = array(
                        'id_hook' => $row['id_hook'],
                        'module' => $row['module'],
                        'id_module' => $row['id_module'],
                    );
                }
            }
            if ($hook_name != 'displayPayment' && $hook_name != 'displayPaymentEU' && $hook_name != 'paymentOptions' && $hook_name != 'displayBackOfficeHeader') {
                Cache::store($cache_id, $list);
                // @todo remove this in 1.6, we keep it in 1.5 for backward compatibility
                self::$_hook_modules_cache_exec = $list;
            }
        } else {
            $list = Cache::retrieve($cache_id);
        }

        // If hook_name is given, just get list of modules for this hook
        if ($hook_name) {
            $retro_hook_name = Tools::strtolower(Hook::getRetroHookName($hook_name));
            $hook_name = Tools::strtolower($hook_name);

            $return = array();
            $inserted_modules = array();
            if (isset($list[$hook_name])) {
                $return = $list[$hook_name];
            }
            foreach ($return as $module) {
                $inserted_modules[] = $module['id_module'];
            }
            if (isset($list[$retro_hook_name])) {
                foreach ($list[$retro_hook_name] as $retro_module_call) {
                    if (!in_array($retro_module_call['id_module'], $inserted_modules)) {
                        $return[] = $retro_module_call;
                    }
                }
            }
            return (count($return) > 0 ? $return : false);
        } else {
            return $list;
        }
    }
}

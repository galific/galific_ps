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
 * API to get active countries list on store
 */

require_once 'AppCore.php';

class AppGetCountries extends AppCore
{
    private $address = null;
    private $country = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->content['countries'] = $this->assignCountries();
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Get list of active countries on store
     *
     * @return array countries data
     */
    public function assignCountries()
    {
        if (Configuration::get('PS_RESTRICT_DELIVERED_COUNTRIES')) {
            $countries = Carrier::getDeliveredCountries($this->context->language->id, true, true);
        } else {
            $countries = Country::getCountries($this->context->language->id, true);
        }

        $country_list = array();
        $country_index = 0;
        foreach ($countries as $country) {
            $country_list[$country_index] = array(
                'id' => $country['id_country'],
                'name' => htmlentities($country['name'], ENT_COMPAT, 'UTF-8')
            );
            $country_index++;
        }
        $this->content['default_country_id'] = $this->context->country->id;
        return $country_list;
    }
}

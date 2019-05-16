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
 * API to get states for selected country
 */

require_once 'AppCore.php';

class AppGetRegions extends AppCore
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
        if (Tools::getIsset('country_id')) {
            $country_id = Tools::getValue('country_id', 0);
            $this->content['states'] = $this->getStatesByCountry((int) $country_id);
        }
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }


    /**
     * Get list of states for selected country
     *
     * @param int $id_country counmtry id
     * @return array state data
     */
    public function getStatesByCountry($id_country)
    {
        $country = new Country((int) $id_country);
        if (!Validate::isLoadedObject($country)) {
            $this->content['status'] = 'failure';
            $this->content['message'] = parent::getTranslatedTextByFileAndISO(
                Tools::getValue('iso_code', false),
                $this->l('Unable to get State for Country'),
                'AppGetRegions'
            );
        } else {
            $this->content['zipcode_required'] = $country->need_zip_code;
            if ($country->isNeedDni()) {
                $this->content['dni_required'] = "1";
            } else {
                $this->content['dni_required'] = "0";
            }
            if ($country->contains_states) {
                $states = array();
                $country_states = State::getStatesByIdCountry((int) $country->id);
                $state_index = 0;
                foreach ($country_states as $state) {
                    if ($state['active'] == 1) {
                        $states[$state_index] = array(
                            'country_id' => $state['id_country'],
                            'state_id' => $state['id_state'],
                            'name' => $state['name']
                        );
                        $state_index++;
                    }
                }
                return $states;
            } else {
                return array();
            }
        }
    }
}

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
 * API to get registration form fields
 */

require_once 'AppCore.php';

class AppGetRegistrationForm extends AppCore
{
    private $product = null;

    /**
     * This function is trigger whenever this class is called in API
     * This is abstract function in appcore
     *
     * @return json
     */
    public function getPageData()
    {
        $this->content['signup_details'] = $this->getFormData();
        $this->content['install_module'] = '';
        return $this->fetchJSONContent();
    }

    /**
     * Get form fields
     *
     * @return array form data
     */
    public function getFormData()
    {
        $form_data = array();
        $genders = Gender::getGenders();
        $gender_index = 0;
        foreach ($genders as $gender) {
            $form_data['titles'][$gender_index] = array(
                'id' => $gender->id,
                'name' => 'gender',
                'label' => $gender->name
            );
            $gender_index++;
        }
        $form_data['firstname'] = '';
        $form_data['lastname'] = '';
        $form_data['email'] = '';
        $form_data['password'] = '';
        $form_data['dob'] = '';
        return $form_data;
    }
}

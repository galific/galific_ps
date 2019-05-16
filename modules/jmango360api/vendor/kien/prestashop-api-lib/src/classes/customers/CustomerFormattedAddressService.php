<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CustomerFormattedAddressService extends BaseService
{
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            $total = 0;
            $multiple_addresses_formated = array();
            $addresses = $this->context->customer->getAddresses($this->context->language->id);

            foreach ($addresses as $detail) {
                $address = new Address($detail['id_address']);
                $multiple_addresses_formated[$total] = AddressFormat::getFormattedLayoutData($address);
                unset($address);
                ++$total;
            }

            $this->response = new CustomerAddressResponse();
            $this->response->addresses = $multiple_addresses_formated;
        } else {
            $this->response = new JmResponse();
            $this->response->errors = array(new JmError(500, 'Customer doest not exits!'));
        }
    }
}

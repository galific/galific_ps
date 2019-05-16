<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class LogoutService extends BaseService
{

    public function doExecute()
    {
        if ($this->isDeleteMethod()) {
            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);

            if ($customer && $customer->id) {
                $customer->logout();
                $this->response = new JmResponse();
                $this->response->messages = array('Logout success');
            } else {
                $this->response = new JmResponse();
                $this->response->errors = array('Invalid customer id');
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}

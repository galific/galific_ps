<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class CustomerOrderService extends BaseService
{
    public function doExecute()
    {
        if ($this->isGetMethod()) {
            //get customer orders
            $customer_id = $this->getRequestResourceId();
            $customer = new CustomerCore($customer_id);

            if ($customer && $customer->id) {
                $orders = OrderCore::getCustomerOrders($customer_id);
                $this->response = new CustomerOrderResponse();
                $this->response->orders = $orders;
            } else {
                $this->response = new JmResponse();
                $this->response->errors = array('Customer doest not exits!');
            }
        } else {
            $this->throwUnsupportedMethodException();
        }
    }
}

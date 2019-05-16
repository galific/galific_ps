<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class UpdateBraintreeOrderService extends BaseService
{
    public function doExecute()
    {
        // update existing order
        if ($this->isPutMethod()) {
            $requestData = json_decode($this->getRequestBody());
            $orderStatus = 0;
            switch($requestData->order_status) {
                case 'settling':
                    $orderStatus = Configuration::get('JM_BRAINTREE_SETTLING');
                    break;
                case 'voided':
                    $orderStatus = Configuration::get('JM_BRAINTREE_VOIDED');
                    break;
                case 'complete':
                    $orderStatus = Configuration::get('PS_OS_PAYMENT');
                    break;
            }

            $order = new Order((int)Order::getOrderByCartId($requestData->id_cart));
            $new_history = new OrderHistory();
            $new_history->id_order = (int)$order->id;
            $new_history->changeIdOrderState((int)$orderStatus, $order, true);
            $new_history->addWithemail(true);

            $this->response = new OrderResponse();
            $this->response->order = new Order((int)Order::getOrderByCartId($requestData->id_cart));
        }
    }
}
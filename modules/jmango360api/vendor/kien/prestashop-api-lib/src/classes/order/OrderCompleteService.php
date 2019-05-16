<?php
/**
 * Created by PhpStorm.
 * User: kien
 * Date: 2/26/18
 * Time: 2:52 PM
 * @author kien
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class OrderCompleteService extends BaseService
{
    private $id_lang;
    private $id_shop;
    private $id_orders = array();
    private $orders;
    private $jmOrderDetails;
    private $orderDetails;

    public function doExecute()
    {
        if ($this->isPostMethod()) {
            $jsonRequestBody = CustomRequest::getRequestBody();
            $this->id_orders = json_decode($jsonRequestBody);
            $this->id_lang = Tools::getValue('id_lang');

            $this->id_shop = Tools::getValue('id_shop');

            if ($this->id_shop) {
                Context::getContext()->shop->id = $this->id_shop;
            } else {
                $this->errors[] = Tools::displayError('Invalid input id_shop parameter');
            }

            foreach ($this->id_orders->orderIds as $id) {
                $order = $this->getOrderDetails($id);
                $this->orders[] = $order;
            }
            $this->orderDetails->orders = $this->orders;
            $this->response = $this->orderDetails;
        } else {
            $this->throwUnsupportedMethodException();
        }
    }

    public function getOrderDetails($id_order)
    {
        $order = $this->getByOrderReference($id_order);
        $customer = new Customer((int)$order->id_customer);
        return $this->transformOrderDetails(
            $order,
            $customer
        );
    }

    public function transformOrderDetails(
        $order,
        $customer
    ) {
        $this->jmOrderDetails = null;
        $this->jmOrderDetails->orderId = $order->id;
        $this->jmOrderDetails->amount = $order->total_paid;
        $this->jmOrderDetails->customer->displayName = $customer->firstname.' '.$customer->lastname;
        $this->jmOrderDetails->customer->email = $customer->email;
        $this->jmOrderDetails->customer->firstName = $customer->firstname;
        $this->jmOrderDetails->customer->lastName = $customer->lastname;
        $this->jmOrderDetails->customer->id = $customer->id;
        $this->jmOrderDetails->currency = CurrencyCore::getCurrency($order->id_currency)['iso_code'];
        $this->jmOrderDetails->deliveryNumber = $order->delivery_number;
        $this->jmOrderDetails->orderDate = $order->date_add;
        $this->jmOrderDetails->paymentName = $order->payment;
        $this->jmOrderDetails->idShop = $order->id_shop;
        $this->jmOrderDetails->currentState = $order->current_state;
        $order_states = OrderStateCore::getOrderStates($this->id_lang);

        foreach ($order_states as $state) {
            if (strcmp($order->current_state, $state['id_order_state'])==0) {
                if (strcmp($state['paid'], '0') == 0) {
                    $this->jmOrderDetails->paid=false;
                } else {
                    $this->jmOrderDetails->paid=true;
                }
            }
        }
        return $this->jmOrderDetails;
    }

    public static function getByOrderReference($reference)
    {
        $sql = '
          SELECT id_order
            FROM `'._DB_PREFIX_.'orders` o
            LEFT JOIN `'._DB_PREFIX_.'customer` c ON (o.`id_customer` = c.`id_customer`)
                WHERE o.`reference` = \''.pSQL($reference).'\'';

        $id = (int) Db::getInstance()->getValue($sql);
        return new Order($id);
    }
}

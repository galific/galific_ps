<?php
/**
 * @author Jmango360
 * @copyright 2017 JMango360
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class JmCheckoutSession
{
    private $context;
    private $deliveryOptionsFinder;

    public function __construct(Context $context, DeliveryOptionsFinder $deliveryOptionsFinder)
    {
        $this->context = $context;
        $this->deliveryOptionsFinder = $deliveryOptionsFinder;
    }

    public function customerHasLoggedIn()
    {
        return $this->context->customer->isLogged();
    }

    public function getCustomer()
    {
        return $this->context->customer;
    }

    public function getCart()
    {
        return $this->context->cart;
    }

    public function getCustomerAddressesCount()
    {
        return count($this->getCustomer()->getSimpleAddresses(
            $this->context->language->id,
            true // no cache
        ));
    }

    public function setIdAddressDelivery($id_address)
    {
        $this->context->cart->updateAddressId($this->context->cart->id_address_delivery, $id_address);
        $this->context->cart->id_address_delivery = $id_address;
        $this->context->cart->save();

        return $this;
    }

    public function setIdAddressInvoice($id_address)
    {
        $this->context->cart->id_address_invoice = $id_address;
        $this->context->cart->save();

        return $this;
    }

    public function getIdAddressDelivery()
    {
        return $this->context->cart->id_address_delivery;
    }

    public function getIdAddressInvoice()
    {
        return $this->context->cart->id_address_invoice;
    }

    public function setMessage($message)
    {
        $this->_updateMessage(Tools::safeOutput($message));

        return $this;
    }

    public function getMessage()
    {
        if ($message = Message::getMessageByCartId($this->context->cart->id)) {
            return $message['message'];
        }

        return false;
    }

    private function _updateMessage($messageContent)
    {
        if ($messageContent) {
            if ($oldMessage = Message::getMessageByCartId((int)$this->context->cart->id)) {
                $message = new Message((int)$oldMessage['id_message']);
                $message->message = $messageContent;
                $message->update();
            } else {
                $message = new Message();
                $message->message = $messageContent;
                $message->id_cart = (int)$this->context->cart->id;
                $message->id_customer = (int)$this->context->cart->id_customer;
                $message->add();
            }
        } else {
            if ($oldMessage = Message::getMessageByCartId($this->context->cart->id)) {
                $message = new Message($oldMessage['id_message']);
                $message->delete();
            }
        }

        return true;
    }

    public function setDeliveryOption($option)
    {
        $this->context->cart->setDeliveryOption($option);

        return $this->context->cart->update();
    }

    public function getSelectedDeliveryOption()
    {
        return $this->deliveryOptionsFinder->getSelectedDeliveryOption();
    }

    public function getDeliveryOptions()
    {
        $deliveryOptions = $this->deliveryOptionsFinder->getDeliveryOptions();

        /**
         * PS-832: Support exclude carriers
         */
        $exluded = PaymentCarrierService::getExcludedPaymentsCarriers();
        $exludedCarriers = isset($exluded['carriers']) ? $exluded['carriers'] : array();
        foreach ($deliveryOptions as $carrier_id => $carrier) {
            $carrier_id_clean = trim($carrier_id, ',');
            if (in_array($carrier_id_clean, $exludedCarriers)) {
                unset($deliveryOptions[$carrier_id]);
            }
        }

        return $deliveryOptions;
    }

    public function setRecyclable($option)
    {
        $this->context->cart->recyclable = (int)$option;

        return $this->context->cart->update();
    }

    public function isRecyclable()
    {
        return $this->context->cart->recyclable;
    }

    public function setGift($gift, $gift_message)
    {
        $this->context->cart->gift = (int)$gift;
        $this->context->cart->gift_message = $gift_message;

        return $this->context->cart->update();
    }

    public function getGift()
    {
        return array(
            'isGift' => $this->context->cart->gift,
            'message' => $this->context->cart->gift_message,
        );
    }

    public function isGuestAllowed()
    {
        return Configuration::get('PS_GUEST_CHECKOUT_ENABLED');
    }

    public function getCheckoutURL()
    {
        return $this->context->link->getPageLink('order');
    }
}

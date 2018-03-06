<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/16/18
 * Time: 8:24 PM
 */ 
class TDK_DropShip_Helper_Data
    extends Mage_Core_Helper_Abstract {


    public function allowDropShipForQuote(Mage_Sales_Model_Quote $quote)
    {
        $store = $quote->getStore();

        if (!$store) {
            $store = Mage::app()->getStore();
        }

        if (!$this->_allowStoreDropShip($store)) {
            return false;
        }

        return true;
    }

    public function allowDropShipForOrder(Mage_Sales_Model_Order $order)
    {
        $store = $order->getStore();

        if (!$this->_allowStoreDropShip($store)) {
            return false;
        }

        $carrier = $order->getShippingCarrier()->getCarrierCode();
        if (!in_array($carrier, array('ups', 'usps'))) {
            return false;
        }

        return true;
    }


    protected function _allowStoreDropShip($store)
    {
        if (is_int($store)) {
            $store = Mage::app()->getStore($store);
        }
        return $store->getConfig('shipping/dropship/allow');;
    }

}
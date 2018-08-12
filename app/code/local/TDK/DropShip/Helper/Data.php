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

	    $postCode = $quote->getShippingAddress()->getPostcode();

	    if (!$postCode || $this->isPostCodeFromHawaii($postCode)) {
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
	    $carrier = $order->getShippingCarrier();

        if (!$carrier || !($carrier instanceof Mage_Shipping_Model_Carrier_Abstract)) {
        	return false;
        }

        $carrierCode = $carrier->getCarrierCode();
        if (!in_array($carrierCode, array('ups', 'usps'))) {
            return false;
        }

        $postCode = $order->getShippingAddress()->getPostcode();

	    if (!$postCode || $this->isPostCodeFromHawaii($postCode)) {
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

	protected function isPostCodeFromHawaii($postCode ) {
    	$postCode = (int) $postCode;
    	return $postCode >= 96701 && $postCode <= 96898;
	}

}
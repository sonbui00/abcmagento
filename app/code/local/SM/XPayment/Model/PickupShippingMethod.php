<?php

/**
 * Created by PhpStorm.
 * User: Smartor
 * Date: 10/14/14
 * Time: 2:13 PM
 */
class SM_XPayment_Model_PickupShippingMethod extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {
    protected $_code = 'xpayment_pickup_shipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $session = Mage::getSingleton("core/session");
        $order_from = $session->getData("order_from");

        $result = Mage::getModel('shipping/rate_result');
        /* @var $result Mage_Shipping_Model_Rate_Result */

        if ($this->getConfigFlag('frontend_enabled') || Mage::app()->getStore()->isAdmin() || $order_from == 'api') {
            $result->append($this->_getStandardShippingRate());
        }

        return $result;
    }

    protected function _getStandardShippingRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */

        $rate->setCarrier($this->_code);
        /**
         * getConfigData(config_key) returns the configuration value for the
         * carriers/[carrier_code]/[config_key]
         */
        $rate->setCarrierTitle($this->getConfigData('title'));

        $rate->setMethod('xpayment_pickup_shipping');
        $rate->setMethodTitle($this->getConfigData('name'));
//            $price = floatval($this->getConfigData('pickup_fee'));
//            $cost = Mage::registry('xpos_shipping_cost');
        if (Mage::registry('xpos_shipping_cost')) {
            $shippingAmount = Mage::registry('xpos_shipping_cost');
            $baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
            $currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();
            $allowedCurrencies = Mage::getModel('directory/currency')->getConfigAllowCurrencies();
            $rates = Mage::getModel('directory/currency')->getCurrencyRates($baseCurrencyCode, array_values($allowedCurrencies));
            if(count($allowedCurrencies)>1){
                if(isset($rates)){
                    if(array_key_exists($baseCurrencyCode, $rates) && array_key_exists($currentCurrencyCode, $rates)){
                        $baseAmount = $rates[$baseCurrencyCode];
                        $currentAmount = $rates[$currentCurrencyCode];
                    }
                    if($baseAmount > $currentAmount){
                        $calPercent = $baseAmount/$currentAmount;
                    }elseif($baseAmount < $currentAmount){
                        $calPercent = $currentAmount/$baseAmount;
                    }else{
                        $calPercent =1;
                    }
                }else{
                    $calPercent =1;
                }
            }else{
                $calPercent = implode($rates);
            }
            $rate->setPrice($shippingAmount*$calPercent);
            $rate->setCost($shippingAmount*$calPercent);
        } else {
            $rate->setPrice('0.00');
            $rate->setCost('0.00');
        }
//        $rate->setPrice($price > 0 ? $price : 0);
//        $rate->setCost(0);

        return $rate;
    }

    public function getAllowedMethods() {
        return array(
            'xpayment_pickup_shipping' => 'X-POS Shipping',
            //            'express' => 'Express',
        );
    }

    protected function _getExpressShippingRate() {
        $rate = Mage::getModel('shipping/rate_result_method');
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('express');
        $rate->setMethodTitle('Express (Next day)');
        $rate->setPrice(12.99);
        $rate->setCost(0);
        return $rate;
    }


}

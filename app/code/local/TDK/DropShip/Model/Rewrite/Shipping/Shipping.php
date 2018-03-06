<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/6/18
 * Time: 8:31 AM
 */
class TDK_DropShip_Model_Rewrite_Shipping_Shipping
    extends Mage_Shipping_Model_Shipping
{
    public function collectCarrierRates($carrierCode, $request)
    {
        if (in_array($carrierCode, array('ups', 'usps'))) {
            $quote = Mage::getModel('checkout/cart')->getQuote();
            if (!Mage::helper('tdk_dropship')->allowDropShipForQuote($quote)) {
                return parent::collectCarrierRates($carrierCode, $request);
            }

            Mage::helper('tdk_dropship/quote_supplier')->addSupplierIdToQuote($quote);

            $this->collectDropShipRates($carrierCode, $request);
        } else {
            return parent::collectCarrierRates($carrierCode, $request);
        }
    }

    /**
     * @param $carrierCode
     * @param Mage_Shipping_Model_Rate_Request $request
     */
    protected function collectDropShipRates($carrierCode, $request)
    {
        $items = $request->getAllItems();
        $itemsBySupplier = array();

        foreach ($items as $item) {
            if (isset($itemsBySupplier[$item->getSupplierId()])) {
                $itemsBySupplier[$item->getSupplierId()][$item->getId()] = $item;
            } else {
                $itemsBySupplier[$item->getSupplierId()] = array($item->getId() => $item);
            }
        }
        if (count($itemsBySupplier) > 1) {
            $sumResults = array();
            foreach ($itemsBySupplier as $supplierId => $itemsOfSupplier) {
                $supplier = Mage::getModel('tdk_dropship/supplier')->load($supplierId);
                $request = clone $request;
                $request->setAllItems($itemsOfSupplier);
                $request
                    ->setCountryId($supplier->getCountryId())
                    ->setRegionId($supplier->getRegionId())
                    ->setCity($supplier->getCity())
                    ->setPostcode($supplier->getPostcode());

                /* from parent::collectCarrierRates($carrierCode, $request); and modify*/

                /* @var $carrier Mage_Shipping_Model_Carrier_Abstract */
                $carrier = $this->getCarrierByCode($carrierCode, $request->getStoreId());
                if (!$carrier) {
                    return $this;
                }
                $carrier->setActiveFlag($this->_availabilityConfigField);
                $result = $carrier->checkAvailableShipCountries($request);
                if (false !== $result && !($result instanceof Mage_Shipping_Model_Rate_Result_Error)) {
                    $result = $carrier->proccessAdditionalValidation($request);
                }
                /*
                * Result will be false if the admin set not to show the shipping module
                * if the delivery country is not within specific countries
                */
                if (false !== $result){
                    if (!$result instanceof Mage_Shipping_Model_Rate_Result_Error) {
                        $result = $this->_aaaaaaaa($request, $carrier);
                        if (!$result) {
                            return $this;
                        }
                    }
                    if ($carrier->getConfigData('showmethod') == 0 && $result->getError()) {
                        return $this;
                    }

                }

                $sumResults[] = $result;

                /* end from parent::collectCarrierRates($carrierCode, $request); and modify*/

            }

            if (!empty($sumResults) && count($sumResults) > 1) {
                $result = array();
                foreach ($sumResults as $res) {
                    if (empty($result)) {
                        $result = $res;
                        continue;
                    }
                    foreach ($res->getAllRates() as $method) {
                        foreach ($result->getAllRates() as $resultMethod) {
                            if ($method->getMethod() == $resultMethod->getMethod()) {
                                $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                continue;
                            }
                        }
                    }
                }
            }

            // sort rates by price
            if (method_exists($result, 'sortRatesByPrice')) {
                $result->sortRatesByPrice();
            }
            $this->getResult()->append($result);

            return $this;

        } else {
            return parent::collectCarrierRates($carrierCode, $request);
        }


    }

    /**
     * @param $request
     * @param $carrier
     * @return array|mixed
     */
    protected function _aaaaaaaa($request, $carrier)
    {
        if ($carrier->getConfigData('shipment_requesttype')) {
            $packages = $this->composePackagesForCarrier($carrier, $request);
            if (!empty($packages)) {
                $sumResults = array();
                foreach ($packages as $weight => $packageCount) {
                    //clone carrier for multi-requests
                    $carrierObj = clone $carrier;
                    $request->setPackageWeight($weight);
                    $result = $carrierObj->collectRates($request);
                    if (!$result) {
                        return false;
                    } else {
                        $result->updateRatePrice($packageCount);
                    }
                    $sumResults[] = $result;
                }
                if (!empty($sumResults) && count($sumResults) > 1) {
                    $result = array();
                    foreach ($sumResults as $res) {
                        if (empty($result)) {
                            $result = $res;
                            continue;
                        }
                        foreach ($res->getAllRates() as $method) {
                            foreach ($result->getAllRates() as $resultMethod) {
                                if ($method->getMethod() == $resultMethod->getMethod()) {
                                    $resultMethod->setPrice($method->getPrice() + $resultMethod->getPrice());
                                    continue;
                                }
                            }
                        }
                    }
                }
            } else {
                $result = $carrier->collectRates($request);
            }
        } else {
            $result = $carrier->collectRates($request);
        }
        return $result;
    }

}
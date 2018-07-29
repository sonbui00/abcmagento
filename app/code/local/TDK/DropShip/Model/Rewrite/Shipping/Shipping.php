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
                        $result = $this->_collectRate($request, $carrier);
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
    protected function _collectRate($request, $carrier)
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

	/**
	 * Only change admin get
	 * Prepare and do request to shipment
	 *
	 * @param Mage_Sales_Model_Order_Shipment $orderShipment
	 * @return Varien_Object
	 */
	public function requestToShipment(Mage_Sales_Model_Order_Shipment $orderShipment)
	{
		$admin = Mage::getSingleton('admin/session')->getUser();
		if (!$admin) {
			$admin = Mage::getModel('admin/user')->getCollection()->getFirstItem();
		}
		$order = $orderShipment->getOrder();
		$address = $order->getShippingAddress();
		$shippingMethod = $order->getShippingMethod(true);
		$shipmentStoreId = $orderShipment->getStoreId();
		$shipmentCarrier = $order->getShippingCarrier();
		$baseCurrencyCode = Mage::app()->getStore($shipmentStoreId)->getBaseCurrencyCode();
		if (!$shipmentCarrier) {
			Mage::throwException('Invalid carrier: ' . $shippingMethod->getCarrierCode());
		}
		$shipperRegionCode = Mage::getStoreConfig(self::XML_PATH_STORE_REGION_ID, $shipmentStoreId);
		if (is_numeric($shipperRegionCode)) {
			$shipperRegionCode = Mage::getModel('directory/region')->load($shipperRegionCode)->getCode();
		}

		$recipientRegionCode = Mage::getModel('directory/region')->load($address->getRegionId())->getCode();

		$originStreet1 = Mage::getStoreConfig(self::XML_PATH_STORE_ADDRESS1, $shipmentStoreId);
		$originStreet2 = Mage::getStoreConfig(self::XML_PATH_STORE_ADDRESS2, $shipmentStoreId);
		$storeInfo = new Varien_Object(Mage::getStoreConfig('general/store_information', $shipmentStoreId));

		if (!$admin->getFirstname() || !$admin->getLastname() || !$storeInfo->getName() || !$storeInfo->getPhone()
		    || !$originStreet1 || !Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId)
		    || !$shipperRegionCode || !Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId)
		    || !Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId)
		) {
			Mage::throwException(
				Mage::helper('sales')->__('Insufficient information to create shipping label(s). Please verify your Store Information and Shipping Settings.')
			);
		}

		/** @var $request Mage_Shipping_Model_Shipment_Request */
		$request = Mage::getModel('shipping/shipment_request');
		$request->setOrderShipment($orderShipment);
		$request->setShipperContactPersonName($admin->getName());
		$request->setShipperContactPersonFirstName($admin->getFirstname());
		$request->setShipperContactPersonLastName($admin->getLastname());
		$request->setShipperContactCompanyName($storeInfo->getName());
		$request->setShipperContactPhoneNumber($storeInfo->getPhone());
		$request->setShipperEmail($admin->getEmail());
		$request->setShipperAddressStreet(trim($originStreet1 . ' ' . $originStreet2));
		$request->setShipperAddressStreet1($originStreet1);
		$request->setShipperAddressStreet2($originStreet2);
		$request->setShipperAddressCity(Mage::getStoreConfig(self::XML_PATH_STORE_CITY, $shipmentStoreId));
		$request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
		$request->setShipperAddressPostalCode(Mage::getStoreConfig(self::XML_PATH_STORE_ZIP, $shipmentStoreId));
		$request->setShipperAddressCountryCode(Mage::getStoreConfig(self::XML_PATH_STORE_COUNTRY_ID, $shipmentStoreId));
		$request->setRecipientContactPersonName(trim($address->getFirstname() . ' ' . $address->getLastname()));
		$request->setRecipientContactPersonFirstName($address->getFirstname());
		$request->setRecipientContactPersonLastName($address->getLastname());
		$request->setRecipientContactCompanyName($address->getCompany());
		$request->setRecipientContactPhoneNumber($address->getTelephone());
		$request->setRecipientEmail($address->getEmail());
		$request->setRecipientAddressStreet(trim($address->getStreet1() . ' ' . $address->getStreet2()));
		$request->setRecipientAddressStreet1($address->getStreet1());
		$request->setRecipientAddressStreet2($address->getStreet2());
		$request->setRecipientAddressCity($address->getCity());
		$request->setRecipientAddressStateOrProvinceCode($address->getRegionCode());
		$request->setRecipientAddressRegionCode($recipientRegionCode);
		$request->setRecipientAddressPostalCode($address->getPostcode());
		$request->setRecipientAddressCountryCode($address->getCountryId());
		$request->setShippingMethod($shippingMethod->getMethod());
		$request->setPackageWeight($order->getWeight());
		$request->setPackages($orderShipment->getPackages());
		$request->setBaseCurrencyCode($baseCurrencyCode);
		$request->setStoreId($shipmentStoreId);

		return $shipmentCarrier->requestToShipment($request);
	}

}
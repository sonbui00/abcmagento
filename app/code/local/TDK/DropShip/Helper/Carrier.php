<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/6/18
 * Time: 11:46 PM
 */
class TDK_DropShip_Helper_Carrier extends Mage_Core_Helper_Abstract
{

    public function isDropShipRequest()
    {
        return !!$this->_getSupplierId();
    }

    public function replaceSupplierAddress($request)
    {
        $supplier = Mage::getModel('tdk_dropship/supplier')->load($this->_getSupplierId());
        $address = $supplier->getStreetAddress();

        $shipperRegionCode = $supplier->getRegionId();
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = Mage::getModel('directory/region')->load($shipperRegionCode)->getCode();
        }

        $request->setShipperAddressStreet(trim($address[0] . ' ' . $address[1]));
        $request->setShipperAddressStreet1($address[0]);
        $request->setShipperAddressStreet2($address[1]);

        $request->setShipperAddressCity($supplier->getCity());
        $request->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $request->setShipperAddressPostalCode($supplier->getPostcode());
        $request->setShipperAddressCountryCode($supplier->getCountryId());

        return $this;
    }

    protected function _getSupplierId()
    {
        return Mage::app()->getRequest()->getParam('supplier_id', false);
    }
}
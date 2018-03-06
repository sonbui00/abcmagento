<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/6/18
 * Time: 11:45 PM
 */
class TDK_DropShip_Model_Rewrite_Usa_Shipping_Carrier_Usps
    extends Mage_Usa_Model_Shipping_Carrier_Usps
{
    /**
     * @param Mage_Shipping_Model_Shipment_Request $request
     * @return array
     */
    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
    {
        /** @var $helper TDK_DropShip_Helper_Carrier */
        $helper = Mage::helper('tdk_dropship/carrier');
        if ($helper->isDropShipRequest()) {
            $helper->replaceSupplierAddress($request);
        }
        return parent::requestToShipment($request); // TODO: Change the autogenerated stub
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/6/18
 * Time: 11:43 PM
 */
class TDK_DropShip_Model_Rewrite_Usa_Shipping_Carrier_Ups
    extends Mage_Usa_Model_Shipping_Carrier_Ups
{
    public function requestToShipment(Mage_Shipping_Model_Shipment_Request $request)
    {
        $helper = Mage::helper('tdk_dropship/carrier');
        if ($helper->isDropShipRequest()) {
            $helper->replaceSupplierAddress($request);
        }
        return parent::requestToShipment($request); // TODO: Change the autogenerated stub
    }

}
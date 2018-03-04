<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 11:39 AM
 */

class TDK_DropShip_Model_Observer
{

    public function salesOrderPlaceAfter($event)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $event->getOrder();
        if (!Mage::helper('tdk_dropship')->allowDropShipForOrder($order)) {
            return;
        }
        $supplierId = array();
        foreach ($order->getAllItems() as $item) {
            $_id = (int) $item->getSupplierId();
            if ($_id && !in_array($supplierId)) {
                $supplierId[] = $_id;
                Mage::getModel('tdk_dropship/supplierOrder')
                    ->setOrderId($order->getId())
                    ->setSupplierId($_id)
                    ->save();
            }
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 11:39 AM
 */

class TDK_DropShip_Model_Observer
{

    public function salesOrderPlaceBefore($event)
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = $event->getOrder();
        if (!Mage::helper('tdk_dropship')->allowDropShipForOrder($order)) {
            return;
        }
        $order->setData('can_ship_partially_item', 0);
    }

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
            if ($_id && !in_array($_id, $supplierId)) {
                $supplierId[] = $_id;
                Mage::getModel('tdk_dropship/supplierOrder')
                    ->setOrderId($order->getId())
                    ->setSupplierId($_id)
                    ->save();

                Mage::helper('tdk_dropship/email')->newOrderSupplier($order, $_id);
            }
        }
    }

    public function salesOrderShipmentSaveAfter($event)
    {
        /** @var Mage_Sales_Model_Order_Shipment $shipment */
        $shipment = $event->getShipment();
        $supplierId = (int) Mage::app()->getRequest()->getParam('supplier_id', false);
        if ($supplierId) {
            /** @var TDK_DropShip_Model_SupplierOrder $supplierOrder */
            $supplierOrder = Mage::getResourceModel('tdk_dropship/supplierOrder_collection')
                ->addFieldToFilter('order_id', $shipment->getOrderId())
                ->addFieldToFilter('supplier_id', $supplierId)
                ->getFirstItem();
            if ($supplierOrder->getId()) {
                $supplierOrder->setShipmentId($shipment->getId());
                $supplierOrder->setShipmentIncrementId($shipment->getIncrementId());
                $supplierOrder->save();
            }
        }
    }
}
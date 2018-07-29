<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/7/18
 * Time: 12:11 AM
 */
class TDK_DropShip_Block_Rewrite_Adminhtml_Sales_Order_Shipment_Packaging
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Packaging
{
    public function getConfigDataJson()
    {
        $data = Mage::helper('core')->jsonDecode(parent::getConfigDataJson());
        $shipmentId = $this->getShipment()->getId();
        $orderId = $this->getRequest()->getParam('order_id');
        $urlParams = array();

        if ($shipmentId) {
            $urlParams['shipment_id'] = $shipmentId;
            $supplierOrder = Mage::getResourceModel('tdk_dropship/supplierOrder_collection')
                ->addFieldToFilter('shipment_id', $shipmentId)
                ->getFirstItem();

            if ($supplierOrder->getId()) {
                $urlParams['supplier_id'] = (int) $supplierOrder->getSupplierId();
            }
            $createLabelUrl = $this->getUrl('*/sales_order_shipment/createLabel', $urlParams);
        } else if ($orderId) {
            $urlParams['order_id'] = $orderId;
            $urlParams['supplier_id'] = Mage::app()->getRequest()->getParam('supplier_id', false);
            $createLabelUrl = $this->getUrl('*/sales_order_shipment/save', $urlParams);
        }
        $data['createLabelUrl'] = $createLabelUrl;
        return Mage::helper('core')->jsonEncode($data);
    }

}
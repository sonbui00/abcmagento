<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/7/18
 * Time: 2:12 AM
 */
class TDK_DropShip_Block_Rewrite_Adminhtml_Sales_Order_Shipment_Create
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create
{
    public function getBackUrl()
    {
        if (false !== ($backUrl = Mage::app()->getRequest()->getParam('back_url', false))) {
            return Mage::helper('core')->urlDecode($backUrl);

        }
        return $this->getUrl('*/sales_order/view', array('order_id'=>$this->getShipment()->getOrderId()));
    }
}
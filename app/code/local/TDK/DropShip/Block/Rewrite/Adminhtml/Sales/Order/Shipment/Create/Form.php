<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 5:39 PM
 */
class TDK_DropShip_Block_Rewrite_Adminhtml_Sales_Order_Shipment_Create_Form
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_Create_Form
{
    public function getSaveUrl()
    {
        $supplierId = (int) Mage::app()->getRequest()->getParam('supplier_id', false);
        $params = array('order_id' => $this->getShipment()->getOrderId());
        if ($supplierId > 0) {
            $params['supplier_id'] = $supplierId;
        }
        return $this->getUrl('*/*/save', $params);
    }

}
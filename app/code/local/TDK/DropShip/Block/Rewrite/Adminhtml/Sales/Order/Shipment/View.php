<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/7/18
 * Time: 2:16 AM
 */
class TDK_DropShip_Block_Rewrite_Adminhtml_Sales_Order_Shipment_View
    extends Mage_Adminhtml_Block_Sales_Order_Shipment_View
{
    public function getBackUrl()
    {
        if (false !== ($backUrl = Mage::app()->getRequest()->getParam('back_url', false))) {
            return Mage::helper('core')->urlDecode($backUrl);
        }
        return parent::getBackUrl();
    }
}
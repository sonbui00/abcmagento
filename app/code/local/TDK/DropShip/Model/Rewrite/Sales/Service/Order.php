<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 5:20 PM
 */

class TDK_DropShip_Model_Rewrite_Sales_Service_Order
    extends Mage_Sales_Model_Service_Order
{
    protected function _canShipItem($item, $qtys = array())
    {
        $supplierId = (int) Mage::app()->getRequest()->getParam('supplier_id', false);
        $itemSupplierId = (int) $item->getSupplierId();
        if ($supplierId > 0 && $itemSupplierId > 0) {
            return ($supplierId === $itemSupplierId) && parent::_canShipItem($item, $qtys);
        }
        return parent::_canShipItem($item, $qtys);
    }

}
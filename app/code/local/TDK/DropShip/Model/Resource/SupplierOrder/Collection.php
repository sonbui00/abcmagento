<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 11:45 AM
 */ 
class TDK_DropShip_Model_Resource_SupplierOrder_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tdk_dropship/supplierOrder');
    }

}
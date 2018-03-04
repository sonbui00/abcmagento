<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/4/18
 * Time: 11:45 AM
 */ 
class TDK_DropShip_Model_Resource_SupplierOrder extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('tdk_dropship/supplier_order', 'id');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/19/18
 * Time: 5:27 AM
 */ 
class TDK_DropShip_Model_Resource_SupplierProduct extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('tdk_dropship/supplier_product', 'id');
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/16/18
 * Time: 8:28 PM
 */ 
class TDK_DropShip_Model_Resource_Supplier extends Mage_Core_Model_Resource_Db_Abstract
{

    protected function _construct()
    {
        $this->_init('tdk_dropship/supplier', 'supplier_id');
    }

}
<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/6/18
 * Time: 3:43 PM
 */
class TDK_DropShip_Helper_SupplierProxy extends Mage_Core_Helper_Abstract
{
    protected $_suppliers;

    public function getSupplier($supplierId)
    {
        if (!isset($this->_suppliers[$supplierId])) {

            $this->_suppliers[$supplierId] = Mage::getModel('tdk_dropship/supplier')->load($supplierId);
        }
        return $this->_suppliers[$supplierId];
    }
}
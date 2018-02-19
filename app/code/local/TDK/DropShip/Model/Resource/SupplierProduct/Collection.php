<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/19/18
 * Time: 5:27 AM
 */ 
class TDK_DropShip_Model_Resource_SupplierProduct_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tdk_dropship/supplierProduct');
    }

    public function updateSuppliersIdWithProductId($productId, $newSupplierIds)
    {
        $this->addFieldToFilter('product_id', $productId);
        $oldSupplierIds = array();
        foreach ($this as $supplier) {
            $oldSupplierIds[] = $supplier->getSupplierId();
        }
        $insert = array_diff($newSupplierIds, $oldSupplierIds);
        $delete = array_diff($oldSupplierIds, $newSupplierIds);

        if ($delete) {
            $where = array(
                'product_id = ?'     => (int) $productId,
                'supplier_id IN (?)' => $delete
            );

            $this->getConnection()->delete($this->getMainTable(), $where);
        }

        if ($insert) {
            $data = array();

            foreach ($insert as $supplierId) {
                $data[] = array(
                    'supplier_id'  => (int) $supplierId,
                    'product_id' => (int) $productId
                );
            }

            $this->getConnection()->insertMultiple($this->getMainTable(), $data);
        }
    }

}
<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/19/18
 * Time: 9:05 AM
 */
class TDK_DropShip_Admin_Product_SupplierController
    extends Mage_Adminhtml_Controller_Action
{
    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     *
     */
    public function saveAction()
    {
        $productId = $this->getRequest()->getParam('product_id', 0);
        $newSupplierIds = $this->getRequest()->getPost('product_supplier');
        if ($productId > 0) {
            Mage::getResourceModel('tdk_dropship/supplierProduct_collection')
                ->updateSuppliersIdWithProductId($productId, $newSupplierIds);
        }
    }
}
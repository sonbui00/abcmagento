<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/16/18
 * Time: 9:54 PM
 */

class TDK_DropShip_Admin_SupplierController extends Mage_Adminhtml_Controller_Action
{

    protected function _initSupplier($idFieldName = 'id')
    {
        $this->_title($this->__('Suppliers'))->_title($this->__('Manage Suppliers'));

        $supplierId = (int) $this->getRequest()->getParam($idFieldName);
        $supplier = Mage::getModel('tdk_dropship/supplier');

        if ($supplierId) {
            $supplier->load($supplierId);
        }

        Mage::register('current_supplier', $supplier);
        return $this;
    }

    public function indexAction()
    {
        $this->_title($this->__('Drop Ships'))->_title($this->__('Manage Suppliers'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/suppliers');
        $this->renderLayout();
    }

    public function editAction()
    {
        $this->_initSupplier();
        $this->loadLayout();

        /** @var $supplier TDK_DropShip_Model_Supplier */
        $supplier = Mage::registry('current_supplier');

        $this->_title($supplier->getId() ? $supplier->getName() : $this->__('New Supplier'));

        /**
         * Set active menu item
         */
        $this->_setActiveMenu('sales/suppliers');

        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function saveAction()
    {
        $postData = $this->getRequest()->getPost();
        if ($postData) {
            $this->_initSupplier();

            /** @var $supplier TDK_DropShip_Model_Supplier */
            $supplier = Mage::registry('current_supplier');


            try {

                $supplier
                    ->addData($postData)
                    ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess('successfully saved');
                Mage::getSingleton('adminhtml/session')->setfilmsData(false);
                $this->_redirect('*/*/');
                return;

            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setSupplierData($postData);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }

            $this->_redirect('*/*/');

        }
        $this->getResponse()->setRedirect($this->getUrl('*/supplier'));
    }

    public function deleteAction()
    {
        if ($id = $this->getRequest()->getParam('id')) {
            try {
                $model = Mage::getModel('tdk_dropship/supplier');
                $model->load($id);
                $model->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The supplier has been deleted.'));
                $this->_redirect('*/*/');
                return;
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError($this->__('Unable to find a supplier to delete.'));
        $this->_redirect('*/*/');
    }

    /**
     * Generate suppliers grid for ajax request
     */
    public function orderGridAction()
    {
        $id = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);

        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);

        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tdk_dropship/sales_order_view_tab_dropShipping')->toHtml()
        );
    }

    public function shipmentsAction()
    {
        $this->_title($this->__('Drop Ships'))->_title($this->__('Shipments'));
        $this->loadLayout();
        $this->_setActiveMenu('sales/dropship/shipments');
        $this->renderLayout();
    }

    public function supplierViewShipmentAction()
    {
        if (false !== ($shipmentId = $this->getRequest()->getParam('shipment_id', false))) {
            $this->_forward('view', 'sales_order_shipment', null, array('come_from'=>'shipment'));
        }
        if (false !== ($shipmentId = $this->getRequest()->getParam('supplier_id', false))) {
            $this->_forward('new', 'sales_order_shipment', null, array('come_from'=>'shipment'));
        }
    }

    public function testAction()
    {
        $shipment = Mage::getModel('sales/order_shipment')->load(1171);
        $supplier = 1;
        Mage::helper('tdk_dropship/email')->deliveredShipmentCustomer($shipment, $supplier);
        die('OK');
    }

}
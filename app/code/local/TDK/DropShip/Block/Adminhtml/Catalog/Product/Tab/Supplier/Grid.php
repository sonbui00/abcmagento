<?php
///**
// * Created by PhpStorm.
// * User: sonbv
// * Date: 2/19/18
// * Time: 4:26 AM
// */
//
//class TDK_DropShip_Block_Adminhtml_Catalog_Product_Tab_Supplier_Grid
//    extends Mage_Adminhtml_Block_Widget_Grid
//{
//    public function __construct()
//    {
//        parent::__construct();
//
//        $this->setId('supplierProduct');
//        $this->setDefaultSort('supplier_id');
//        $this->setDefaultSort('DESC');
//        $this->setUseAjax(true);
//        $this->setFilterVisibility(false);
//        $this->setEmptyText(Mage::helper('tdk_dropship')->__('There are no suppliers for this product'));
//    }
//
//    protected function _prepareCollection()
//    {
////        $productId = $this->getRequest()->getParam('id');
//        $productId = $this->_getProduct()->getId();
//
//        $collection = Mage::getResourceModel('tdk_dropship/supplier_collection')
//            ->addProductIdToFilter($productId);
//        $this->setCollection($collection);
//        return parent::_prepareCollection();
//    }
//
//    protected function _prepareColumns()
//    {
//        $this->addColumn('first_name', array(
//            'header' => Mage::helper('tdk_dropship')->__('First Name'),
//            'index' => 'first_name',
//        ));
//
//        $this->addColumn('last_name', array(
//            'header' => Mage::helper('tdk_dropship')->__('Last Name'),
//            'index' => 'last_name',
//        ));
//
//        $this->addColumn('email', array(
//            'header' => Mage::helper('tdk_dropship')->__('Email'),
//            'index' => 'email',
//        ));
//
//        return parent::_prepareColumns();
//    }
//
//    protected function _prepareMassaction()
//    {
//        $this->setMassactionIdField('entity_id');
//        $this->getMassactionBlock()->setFormFieldName('product');
//
//        $this->getMassactionBlock()->addItem('delete', array(
//            'label'=> Mage::helper('catalog')->__('Delete'),
//            'url'  => $this->getUrl('*/*/massDelete'),
//            'confirm' => Mage::helper('catalog')->__('Are you sure?')
//        ));
//
//        $statuses = Mage::getSingleton('catalog/product_status')->getOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//            'label'=> Mage::helper('catalog')->__('Change status'),
//            'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//            'additional' => array(
//                'visibility' => array(
//                    'name' => 'status',
//                    'type' => 'select',
//                    'class' => 'required-entry',
//                    'label' => Mage::helper('catalog')->__('Status'),
//                    'values' => $statuses
//                )
//            )
//        ));
//
//        if (Mage::getSingleton('admin/session')->isAllowed('catalog/update_attributes')){
//            $this->getMassactionBlock()->addItem('attributes', array(
//                'label' => Mage::helper('catalog')->__('Update Attributes'),
//                'url'   => $this->getUrl('*/catalog_product_action_attribute/edit', array('_current'=>true))
//            ));
//        }
//
//        Mage::dispatchEvent('adminhtml_catalog_product_grid_prepare_massaction', array('block' => $this));
//        return $this;
//    }
//
//    public function getGridUrl()
//    {
//        $productId = $this->getRequest()->getParam('id');
//        $storeId = $this->getRequest()->getParam('store', 0);
//        if ($storeId) {
//            $storeId = Mage::app()->getStore($storeId)->getId();
//        }
//        return $this->getUrl('*/catalog_product/alertsPriceGrid', array(
//            'id' => $productId,
//            'store' => $storeId
//        ));
//    }
//}
<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/17/18
 * Time: 12:36 PM
 */
class TDK_DropShip_Block_Adminhtml_Supplier_Shipment_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('suppliersShipmentsGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('tdk_dropship/supplierOrder_collection')
            ->join(
                array('supplier' => 'tdk_dropship/supplier'),
                'main_table.supplier_id = supplier.supplier_id'
            )
//            ->addFieldToFilter('order_id', $this->getOrder()->getId())
        ;
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn('id',
            array(
                'header' => 'ID',
                'width' => '50px',
                'index' => 'id',
            ));

        $this->addColumn('order_id',
            array(
                'header' => 'Order',
                'width' => '50px',
                'index' => 'order_id',
            ));

        $this->addColumn('first_name',
            array(
                'header' => 'Supplier First Name',
                'index' => 'first_name',
            ));
        $this->addColumn('last_name',
            array(
                'header' => 'Supplier Last Name',
                'index' => 'last_name',
            ));

        $this->addColumn('email',
            array(
                'header' => 'Supplier Email',
                'index' => 'email',
            ));

        $this->addColumn('shipment',
            array(
                'header' => 'Shipment',
                'align' => 'left',
                'index' => 'shipment_increment_id',
            ));


        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        if ($row->getShipmentId()) {
            return $this->getUrl(
                '*/sales_order_shipment/view',
                array(
                    'shipment_id'=> $row->getShipmentId(),
                    'order_id'  => $row->getOrderId(),
                ));
        } else {
            return $this->getUrl(
                '*/sales_order_shipment/new',
                array(
                    'supplier_id'=> $row->getId(),
                    'order_id'  => $row->getOrderId(),
                ));
        }
    }
}
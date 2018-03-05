<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/5/18
 * Time: 3:46 AM
 */

class TDK_DropShip_Model_CronJob
{

    const STATUS_ERROR = 10;
    const STATUS_DELIVERED = 20;

    public function updateShipmentTrackingStatus()
    {
        $tracks = $this->_getShipmentTracks();
        foreach ($tracks as $track) {
            $info = $track->getNumberDetail();
            $description = '';
            if ($info instanceof Mage_Shipping_Model_Tracking_Result_Status){
                if ($info->getCarrier() === 'ups') {
                    $description = $info->getStatus();
                    if ($info->getStatus === 'DELIVERED') {
                        $track->setStatus(static::STATUS_DELIVERED);
                    }
                } elseif ($info->getCarrier() === 'usps') {
                    $description = $info->getTrackSummary();
                    if (strpos($description,'was delivered') !== false) {
                        $track->setStatus(static::STATUS_DELIVERED);
                    }
                }
            } elseif ($info instanceof Mage_Shipping_Model_Tracking_Result_Error) {
                $description = $info->getErrorMessage();
            }
            if ($description) {
                $track->setDescription($description);
                $track->save();
            }
            unset($info);
        }
        return $this;
    }

    /**
     * @return Mage_Sales_Model_Resource_Order_Shipment_Track_Collection
     */
    protected function _getShipmentTracks()
    {
        /* @var Mage_Sales_Model_Resource_Order_Shipment_Track_Collection $tracks */
        $tracks = Mage::getResourceModel('sales/order_shipment_track_collection');
        $tracks->getSelect()
            ->joinRight(
                array('dropshipOrder' => $tracks->getTable('tdk_dropship/supplier_order')),
                'main_table.parent_id = dropshipOrder.shipment_id'
            );
        $tracks->addFieldToFilter('shipment_id',  array("notnull" => 1));
        $tracks->addFieldToFilter('carrier_code', array("in" => array('usps', 'ups')));
        $tracks->addFieldToFilter('status', array("null" => 1));

        $createdAt = date('Y-m-d H:i:s', strtotime('-1 month')); //only push alerts generated in last hour
        $tracks->addFieldToFilter('created_at',array('gteq' => $createdAt));
        return $tracks;
    }
}
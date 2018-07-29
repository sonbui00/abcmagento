<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/6/18
 * Time: 5:02 PM
 */
class TDK_DropShip_Model_Adminhtml_System_Config_Backend_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH = 'crontab/jobs/update_shipment_tracking_status/schedule/cron_expr';

    protected function _afterSave()
    {
        $frequency = $this->getValue();

        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($frequency)
                ->setPath(self::CRON_STRING_PATH)
                ->save();
        }
        catch (Exception $e) {
            throw new Exception(Mage::helper('cron')->__('Unable to save the cron expression.'));
        }
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/5/18
 * Time: 11:04 PM
 */ 
class TDK_Zipcode_Model_Resource_Coordinate_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    protected function _construct()
    {
        $this->_init('tdk_zipcode/coordinate');
    }

}
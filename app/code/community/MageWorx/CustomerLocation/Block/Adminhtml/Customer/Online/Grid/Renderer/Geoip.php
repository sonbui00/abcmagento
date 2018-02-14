<?php
/**
 * MageWorx
 * Customer Location Extension
 *
 * @category   MageWorx
 * @package    MageWorx_CustomerLocation
 * @copyright  Copyright (c) 2015 MageWorx (http://www.mageworx.com/)
 */

class MageWorx_CustomerLocation_Block_Adminhtml_Customer_Online_Grid_Renderer_Geoip extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders GeoIP Location column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $version = Mage::getConfig()->getModuleConfig("Mage_Log")->version;
        if (isset($version[0]) && version_compare($version[0], '1.6.1.1', '>=')) {
            $ip = @inet_ntop($row->getData($this->getColumn()->getIndex()));
        } else {
            $ip = long2ip($row->getData($this->getColumn()->getIndex()));
        }
        $row->setData('geo_ip', Mage::getSingleton('mageworx_geoip/geoip')->getLocation($ip));

        return Mage::helper('mageworx_customerlocation')->getGeoIpHtml($row);
    }
}
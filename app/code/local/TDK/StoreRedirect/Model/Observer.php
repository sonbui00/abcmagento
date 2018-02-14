<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 2/13/18
 * Time: 8:46 PM
 */
class TDK_StoreRedirect_Model_Observer
{
    public function controllerActionPostdispatch($event)
    {
//        echo(Mage::app()->getStore()->getCode());
        $cookie = Mage::getSingleton('core/cookie');
        if ($cookie->get('geoip_processed') != 1) {
//            $ip = '72.229.28.185';// Mage::helper('core/http')->getRemoteAddr();
//            $ip = '132.160.48.210';// Mage::helper('core/http')->getRemoteAddr();
            $ip = Mage::helper('core/http')->getRemoteAddr();
            /** @var MageWorx_GeoIP_Model_Geoip $geoIP */
            $geoIP = Mage::getSingleton('mageworx_geoip/geoip')->getLocation($ip);
            $geoIPRegion= $geoIP->getData('region');
            if ('Hawaii' !== $geoIPRegion) {
                $store = Mage::getModel('core/store')->load('mainland', 'code');
                if ($store->getName() != Mage::app()->getStore()->getName()) {
                    $event->getControllerAction()->getResponse()->setRedirect($store->getCurrentUrl(false));
                }
            }
            $cookie->set('geoip_processed', 1, time() + 86400, '/');
        }
    }
}
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
        if (!$cookie->get('geoip_store')) {
//            $ip = '72.229.28.185';// Mage::helper('core/http')->getRemoteAddr();
//            $ip = '132.160.48.210';// Mage::helper('core/http')->getRemoteAddr();
            $ip = Mage::helper('core/http')->getRemoteAddr();
            /** @var MageWorx_GeoIP_Model_Geoip $geoIP */
            $geoIP = Mage::getSingleton('mageworx_geoip/geoip')->getLocation($ip);
            $geoIPRegion= $geoIP->getData('region');

            if ('Hawaii' === $geoIPRegion) {
                $geoIPStore = 'default';
            } else {
                $geoIPStore = 'mainland';
            }
            $cookie->set('geoip_store', $geoIPStore, time() + 86400, '/');
        } else {
            $geoIPStore = $cookie->get('geoip_store');
        }

        if (Mage::app()->getStore()->getCode() !== $geoIPStore) {
            $event->getControllerAction()->getResponse()->setRedirect(Mage::app()->getStore($geoIPStore)->getCurrentUrl(false));
        }
    }
}
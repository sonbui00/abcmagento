<?php

/**
 * Created by PhpStorm.
 * User: sonbv
 * Date: 3/5/18
 * Time: 11:00 PM
 */
class TDK_Zipcode_Helper_Data extends Mage_Core_Helper_Abstract
{

    const API_KEY = 'AIzaSyD05hvlJe4bpDfEazRAMF1iwTJ52GiEDWE';

    public function getDistance($zipcode1, $country1, $zipcode2, $country2)
    {
        $c1 = $this->_getLatLng($zipcode1, $country1);
        $c2 = $this->_getLatLng($zipcode2, $country2);

        return $this->_getDistanceOpt($c1[0], $c1[1], $c2[0], $c2[1]);
    }

    protected function _getLatLng($zipcode, $country)
    {
        if (isset($country) && strtolower($country) !== 'us') {
            $address = $country;
        } else {
            $address = $zipcode;
        }
        $coordinate = Mage::getResourceModel('tdk_zipcode/coordinate_collection')
            ->addFieldToFilter('zipcode', $address)
            ->getFirstItem();
        
        if (!$coordinate->getId()) {
            $latlng = $this->_getLatLngFromGoogleMap($address);
            $coordinate->setZipcode($address);
            $coordinate->setLat($latlng[0]);
            $coordinate->setLng($latlng[1]);
            $coordinate->save();
        }
        return array($coordinate->getLat(), $coordinate->getLng());
    }


    /**
     * Get from
     * https://stackoverflow.com/questions/10053358/measuring-the-distance-between-two-coordinates-in-php
     * Optimized algorithm from http://www.codexworld.com
     *
     * @param float $latitudeFrom
     * @param float $longitudeFrom
     * @param float $latitudeTo
     * @param float $longitudeTo
     *
     * @return float [km]
     */
    protected function _getDistanceOpt($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo)
    {
        $rad = M_PI / 180;
        //Calculate distance from latitude and longitude
        $theta = $longitudeFrom - $longitudeTo;
        $dist = sin($latitudeFrom * $rad) * sin($latitudeTo * $rad) + cos($latitudeFrom * $rad) * cos($latitudeTo * $rad) * cos($theta * $rad);

        return acos($dist) / $rad * 60 * 1.853;
    }

    /**
     * @param $address
     * @return array
     */
    protected function _getLatLngFromGoogleMap($address)
    {
        $address = urlencode($address);
        $url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=" . $this::API_KEY;
        // Get JSON results from this request
        $geo = file_get_contents($url);
        $geo = json_decode($geo, true); // Convert the JSON to an array

        if (isset($geo['status']) && ($geo['status'] == 'OK')) {
            $latitude = $geo['results'][0]['geometry']['location']['lat']; // Latitude
            $longitude = $geo['results'][0]['geometry']['location']['lng']; // Longitude
            return array($latitude, $longitude);
        } else {
            $error = "";
            if (isset($geo['error_message'])) {
                $error = $geo['error_message'];
            }
            Mage::log("TDK Zipcode: can't get lat lng of $address. Error: $error", null, 'tdkzipcode.log');
            return array(0, 0);
        }
    }

}
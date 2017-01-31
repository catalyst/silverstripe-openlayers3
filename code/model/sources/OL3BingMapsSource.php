<?php

/**
 * File told conatain OL3BingMapsSource
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A wrapper for ol.source.BingMaps
 * @link https://openlayers.org/en/latest/apidoc/ol.source.BingMaps.html
 */

class OL3BingMapsSource extends OL3Source
{
    /**
     * Bing Maps API key. Get yours at http://www.bingmapsportal.com/. Required
     * var String
     */
    private static $bing_api_key;

    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types
     * @var array
     */
    private static $db = [
        'ImagerySet' => "Enum('Road,Aerial,AerialWithLabels','Road')"
    ];

    /**
     * Getter for the persistent properties.
     * This implementation adds the bing api key
     * Used in OL3Map::JsonLayers() to export the layer structure to the template
     * @see OL3Map::JsonLayers()
     * @return Array
     */
    public function toMap()
    {
        $map = parent::toMap();
        $map['Key'] = $this->config()->get('bing_api_key');
        return $map;
    }
}

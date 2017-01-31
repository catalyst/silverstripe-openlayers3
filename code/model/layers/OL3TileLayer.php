<?php

/**
 * File told conatain OL3TileLayer
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * Representation of an Openlayers3 ol.layer.Tile
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.layer.Tile.html
 */

class OL3TileLayer extends OL3Layer
{
    /**
     * Map of available sources that work this class of layer.
     * Keys are class names, values are nice names
     * @var array
     */
    private static $available_source_types = [
        'OL3OSMSource' => 'OSM Source',
        'OL3BingMapsSource' => 'Bing Source',
        'OL3TileWMSSource' => 'WMS Tile Source',
    ];
}

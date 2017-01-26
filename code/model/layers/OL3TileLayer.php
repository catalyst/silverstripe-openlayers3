<?php

/**
 * Representation of an Openlayers3 ol.layer.Tile
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.layer.Tile.html
 */

class OL3TileLayer extends OL3Layer
{
    private static $singular_name = 'OpenLayer3 Tile Layer';
    private static $plural_name = 'OpenLayer3 Tile Layers';

    private static $available_source_types = [
        'OL3OSMSource' => 'OSM Source',
        'OL3BingSource' => 'Bing Source',
        'OL3TileWMSSource' => 'WMS Tile Source',
    ];
}

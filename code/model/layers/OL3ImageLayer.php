<?php

/**
 * File told conatain OL3ImageLayer
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * Representation of an Openlayers3 ol.layer.Image
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.layer.Image.html
 */

class OL3ImageLayer extends OL3Layer
{
    /**
     * Map of available sources that work this class of layer.
     * Keys are class names, values are nice names
     * @var array
     */
    private static $available_source_types = [
        'OL3ImageStaticSource' => 'Static Image Source',
    ];
}

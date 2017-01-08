<?php

/**
 * Representation of an Openlayers3 ol.style.Style
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Style.html
 */

class OL3StyleStyle extends OL3Style
{
    private static $singular_name = 'Style Container';
    private static $plural_name = 'Style Containers';

    private static $db = [
        'Title' => 'Varchar',
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
        'Text' => 'OL3TextStyle',
        'Image' => 'OL3ImageStyle',
    ];
}

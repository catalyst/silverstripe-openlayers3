<?php

/**
 * Representation of an Openlayers3 ol.style.Text
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Text.html
 */

class OL3TextStyle extends OL3Style
{
    private static $singular_name = 'Text Style';
    private static $plural_name = 'Text Styles';

    private static $db = [
        'TextAlign' => "Enum('left,right,center,end,start','start')",
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    private static $defaults = [
        'TextAlign' => 'start',
    ];
}

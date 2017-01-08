<?php

/**
 * Representation of an Openlayers3 ol.style.Circle
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Circle.html
 */

class OL3CircleStyle extends OL3ImageStyle
{
    private static $singular_name = 'Circle Style';
    private static $plural_name = 'Circle Styles';

    private static $db = [
        'Radius' => 'Int',
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    private static $defaults = [
        'Radius' => '20',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Radius')->setRange(1,100);

        return $fields;
    }
}

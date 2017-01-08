<?php

/**
 * Representation of an Openlayers3 ol.style.Stroke
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Stroke.html
 */

class OL3StrokeStyle extends OL3Style
{
    private static $singular_name = 'Stroke Style';
    private static $plural_name = 'Stroke Styles';

    private static $db = [
        'Color' => 'Varchar',
        'Width' => 'Int(1)',
    ];

    private static $defaults = [
        'Color' => 'rgba(192,0,0,.5)',
        'Width' => '1',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField('Color', ColorField::create('Color'));
        $fields->dataFieldByName('Width')->setRange(1,5,1,'px');

        return $fields;
    }
}

<?php

/**
 * Representation of an Openlayers3 ol.style.Icon
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Icon.html
 */

class OL3IconStyle extends OL3ImageStyle
{
    private static $singular_name = 'Icon Style';
    private static $plural_name = 'Icon Styles';

    private static $db = [
        'Scale' => 'Decimal',
        'Opacity' => 'Decimal',
    ];

    private static $has_one = [
        'Icon' => 'Image',
    ];

    private static $defaults = [
        'Scale' => 1,
        'Opacity' => 1,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Scale')->setRange(0.01, 2, 0.01);
        $fields->dataFieldByName('Opacity')->setRange(0, 1, 0.1);

        return $fields;
    }
}

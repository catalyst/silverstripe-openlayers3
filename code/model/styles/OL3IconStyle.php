<?php

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

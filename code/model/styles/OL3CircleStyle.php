<?php

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

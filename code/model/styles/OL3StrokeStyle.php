<?php

class OL3StrokeStyle extends OL3Style
{
    private static $singular_name = 'Stroke Style';
    private static $plural_name = 'Stroke Styles';

    private static $db = [
        'Color' => 'Varchar',
        'Width' => 'Int(1)',
    ];

    private static $defaults = [
        'Color' => '#a00',
        'Width' => '2',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField('Color', ColorField::create('Color'));
        $fields->dataFieldByName('Width')->setRange(1,5,1,'px');

        return $fields;
    }
}

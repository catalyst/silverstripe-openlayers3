<?php

class OL3RegularShapeStyle extends OL3ImageStyle
{
    private static $singular_name = 'Regular Shape Style';
    private static $plural_name = 'Regular Shape Styles';

    private static $db = [
        'InnerRadius' => 'Int',
        'OuterRadius' => 'Int',
        'Points' => 'Int',
        'Angle' => 'Int',
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    private static $defaults = [
        'Radius' => '20',
        'Radius' => '20',
        'Points' => '3',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('InnerRadius')->setRange(0,100)->setRightTitle('Inner Radius of a star, set to 0 for polygon');
        $fields->dataFieldByName('OuterRadius')->setRange(1,100)->setRightTitle('Outer Radius of a star');
        $fields->dataFieldByName('Points')->setRange(3,8);
        $fields->dataFieldByName('Angle')->setRange(0,360);

        return $fields;
    }
}

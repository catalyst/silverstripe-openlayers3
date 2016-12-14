<?php

class OL3CircleStyle extends OL3ImageStyle
{
    private static $singular_name = 'Circle Style';
    private static $plural_name = 'Circle Styles';

    private static $db = [
        'Title' => 'Varchar',
        'Radius' => 'Varchar',
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    private static $defaults = [
        'Radius' => '20',
    ];

    public function getTitle()
    {
        return $this->exists() ? $this->getField('Title') : 'new Stroke Style';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Radius')
            ->setAttribute('type', 'range')
            ->setAttribute('min', '1')
            ->setAttribute('max', '100')
            ->setAttribute('step', '1');

        return $fields;
    }
}

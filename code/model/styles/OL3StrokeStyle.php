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

    public function getTitle()
    {
        return $this->exists() ? "Stroke Color: {$this->Color}, Stroke Width: {$this->Width}" : 'new Stroke Style';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Color')->setAttribute('type', 'color');
        $fields->dataFieldByName('Width')
            ->setAttribute('type', 'range')
            ->setAttribute('min', '1')
            ->setAttribute('max', '5')
            ->setAttribute('step', '1');

        return $fields;
    }
}

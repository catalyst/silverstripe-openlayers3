<?php

class OL3StyleStyle extends OL3Style
{
    private static $singular_name = 'Style Container';
    private static $plural_name = 'Style Containers';

    private static $db = [
        'Title' => 'Varchar',
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
        'Text' => 'OL3TextStyle',
        'Image' => 'OL3ImageStyle',
    ];

    public function getTitle()
    {
        return $this->exists() ? $this->getField('Title') : 'new Style';
    }
}

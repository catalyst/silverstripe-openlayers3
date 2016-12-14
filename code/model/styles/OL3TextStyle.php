<?php

class OL3TextStyle extends OL3Style
{
    private static $singular_name = 'Text Style';
    private static $plural_name = 'Text Styles';

    private static $db = [
        'Text' => 'Varchar(255)',
        'Font' => 'Varchar',
        'TextAlign' => "Enum('left,right,center,end,start','start')",
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    private static $defaults = [
        'Font' => '10px sans-serif',
        'TextAlign' => 'start',
    ];

    public function getTitle()
    {
        return $this->exists() ? "Text: {$this->Text}" : 'new Text Style';
    }
}

<?php

class OL3TextStyle extends OL3Style
{
    private static $singular_name = 'Text Style';
    private static $plural_name = 'Text Styles';

    private static $db = [
        'TextAlign' => "Enum('left,right,center,end,start','start')",
    ];

    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    private static $defaults = [
        'TextAlign' => 'start',
    ];
}

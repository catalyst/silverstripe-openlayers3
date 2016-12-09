<?php

class OL3Layer extends DataObject
{
    private static $singular_name = 'OpenLayer3 Layer';
    private static $plural_name = 'OpenLayer3 Layer';

    private static $db = [
        'Title' => 'Varchar',
        'Type' => "Enum('ImageWMS')",
        'URL' => 'Varchar(255)',
    ];

    private static $has_one = [ 'Map' => 'OL3Map' ];
}

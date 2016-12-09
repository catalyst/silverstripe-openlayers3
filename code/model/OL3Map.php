<?php

class OL3Map extends DataObject
{
    private static $singular_name = 'OpenLayer3 Map';
    private static $plural_name = 'OpenLayer3 Maps';

    private static $db = [
        'Title' => 'Varchar',
        'Lat' => 'Decimal',
        'Lon' => 'Decimal',
        'Zoom' => 'Int',
    ];

    private static $has_many = [
        'Pages' => 'OL3MapPage',
        'Layers' => 'OL3Layer',
    ];
}

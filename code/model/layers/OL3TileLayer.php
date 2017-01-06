<?php

class OL3TileLayer extends OL3Layer
{
    private static $singular_name = 'OpenLayer3 Tile Layer';
    private static $plural_name = 'OpenLayer3 Tile Layers';

    private static $db = [
        'SourceType' => "Enum('OSM,WMS')",
        'SourceUrl' => 'Varchar(255)',
        'SourceLayers' => 'Varchar',
        'SourceProjection' => 'Varchar',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldsToTab(
            'Root.Main',
            [
                $fields->dataFieldByName('SourceType'),
                $fields->dataFieldByName('SourceUrl')->setRightTitle('Prefix with "/OL3Proxy/dorequest?u=" to work around same-origin issues'),
                $fields->dataFieldByName('SourceLayers')->setRightTitle('Comma separated list of names to identify layers on the server side'),
                $fields->dataFieldByName('SourceProjection')->setRightTitle('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection'),
            ],
            'Opacity'
        );

        return $fields;
    }
}

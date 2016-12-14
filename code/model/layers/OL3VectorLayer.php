<?php

class OL3VectorLayer extends OL3Layer
{
    private static $singular_name = 'OpenLayer3 Vector Layer';
    private static $plural_name = 'OpenLayer3 Vector Layer';

    private static $db = [
        'SourceType' => "Enum('polygon,line,point')",
        'SourceUrl' => 'Varchar(255)',
        'SourceFeatureTypes' => 'Varchar',
        'SourceProjection' => 'Varchar',
    ];

    private static $has_one = [
        'DefaultStyle' => 'Ol3StyleStyle',
        'HoverStyle' => 'Ol3StyleStyle',
        'SelectStyle' => 'Ol3StyleStyle',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceType'), 'Opacity');
        // $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceUrl')->setRightTitle('Prefix with "/OL3Proxy/dorequest?u=" to work around same-origin issues'), 'Opacity');
        // $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceLayers')->setRightTitle('Comma separated list of names to identify layers on the server side'), 'Opacity');
        // $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceProjection')->setRightTitle('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection'), 'Opacity');

        return $fields;
    }
}

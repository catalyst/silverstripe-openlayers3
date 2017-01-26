<?php

class OL3VectorSource extends OL3Source
{
    private static $db = [
        'Url' => 'Varchar(255)',
        'FeatureTypes' => 'Varchar',
        'Projection' => 'Varchar',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Url')
            ->setDescription('Prefix with "/OL3Proxy/dorequest?u=" to work around same-origin issues');
        $fields->dataFieldByName('FeatureTypes')
            ->setDescription('Comma separated list of names to identify layers on the server side');
        $fields->dataFieldByName('Projection')
            ->setDescription('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection');

        return $fields;
    }
}
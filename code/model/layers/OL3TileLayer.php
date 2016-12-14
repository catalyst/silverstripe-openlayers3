<?php

class OL3TileLayer extends OL3Layer
{
    private static $singular_name = 'OpenLayer3 Tile Layer';
    private static $plural_name = 'OpenLayer3 Tile Layer';

    private static $db = [
        'SourceType' => "Enum('OSM,WMS')",
        'SourceUrl' => 'Varchar(255)',
        'SourceLayers' => 'Varchar',
        'SourceProjection' => 'Varchar',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceType'), 'Opacity');
        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceUrl')->setRightTitle('Prefix with "/OL3Proxy/dorequest?u=" to work around same-origin issues'), 'Opacity');
        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceLayers')->setRightTitle('Comma separated list of names to identify layers on the server side'), 'Opacity');
        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('SourceProjection')->setRightTitle('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection'), 'Opacity');

        $fields->removeByName('MapID');

        // select layer type on creation
        if (!$this->exists() && $this->ClassName = __CLASS__) {

            $subclasses = ClassInfo::subclassesFor(__CLASS__);

            if (isset($subclasses[__CLASS__])) {
                unset($subclasses[__CLASS__]);
            }

            if (count($subclasses)) {
                $fields->addFieldToTab('Root.Main', DropdownField::create('ClassName', 'Layer Type', $subclasses), 'Title');
            }
        }

        return $fields;
    }
}

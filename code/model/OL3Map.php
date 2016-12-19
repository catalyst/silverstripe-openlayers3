<?php

class OL3Map extends DataObject
{
    private static $singular_name = 'OpenLayer3 Map';
    private static $plural_name = 'OpenLayer3 Maps';

    private static $db = [
        'Title' => 'Varchar',
        'Projection' => 'Varchar',
        'Lat' => 'Decimal',
        'Lon' => 'Decimal',
        'Zoom' => 'Int',
    ];

    private static $has_many = [
        'Pages' => 'OL3MapPage',
    ];

    private static $many_many = [
        'Layers' => 'OL3Layer',
    ];

    private static $many_many_extraFields = [
        'Layers' => [ 'SortOrder' => 'Int' ],
    ];

    public function Layers() {
        return $this->getManyManyComponents('Layers')->sort('SortOrder');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($this->exists()) {
            $fields->dataFieldByName('Layers')->getConfig()->addComponent(new GridFieldSortableRows('SortOrder'));
        }

        return $fields;
    }
}

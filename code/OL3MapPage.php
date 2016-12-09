<?php

class OL3MapPage extends Page
{
    private static $singular_name = 'OpenLayer3 Map Page';
    private static $plural_name = 'OpenLayer3 Map Pages';

    private static $has_one = [
        'Map' => 'OL3Map',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();
        $fields->removeByName('Content');
        $fields->addFieldToTab('Root.Main', DropdownField::create('MapID', 'Map', OL3Map::get()->map()), 'Metadata');
        return $fields;
    }
}

class OL3MapPage_Controller extends Page_Controller
{
    public function View()
    {
        if (($map = $this->Map())) {
            return json_encode(array_intersect_key($map->toMap(), array_flip(['Lat', 'Lon', 'Zoom'])));
        }
    }

    public function Layers()
    {
        if (($map = $this->Map())) {
            return json_encode($map->Layers()->toNestedArray());
        }
    }

    public function init()
    {
        parent::init();
        Requirements::css('https://openlayers.org/en/v3.19.1/css/ol.css');
        Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
        Requirements::javascript('https://openlayers.org/en/v3.19.1/build/ol.js');
        Requirements::javascript('openlayers3/javascript/OL3MapPage-init.js');
    }
}

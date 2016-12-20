<?php

class OL3Map extends DataObject
{
    private static $singular_name = 'Map';
    private static $plural_name = 'Maps';

    private static $db = [
        'Title' => 'Varchar',
        'Projection' => 'Varchar',
        'Lat' => 'Decimal',
        'Lon' => 'Decimal',
        'Zoom' => 'Int',
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

    public function JsonView()
    {
        return json_encode(array_intersect_key($this->toMap(), array_flip(['Lat', 'Lon', 'Zoom', 'Projection'])));
    }

    public function JsonLayers()
    {
        return json_encode($this->Layers()->toNestedArray());
    }

    public function JsonStyles()
    {
        $styles = [];
        foreach ($this->Layers() as $layer) {
            if ($layer->hasMethod('getStyles')) {
                $layer->getStyles($styles);
            }
        }
        return json_encode($styles);
    }

    public function forTemplate()
    {
        Requirements::css('https://openlayers.org/en/v3.19.1/css/ol.css');
        Requirements::javascript(THIRDPARTY_DIR.'/jquery/jquery.js');
        Requirements::javascript('https://openlayers.org/en/v3.19.1/build/ol.js');
        Requirements::javascript('openlayers3/javascript/OL3.base.js');
        Requirements::javascript('openlayers3/javascript/OL3.html.js');
        Requirements::javascript('openlayers3/javascript/OL3.layer.js');
        Requirements::javascript('openlayers3/javascript/OL3.layersPanel.js');
        Requirements::javascript('openlayers3/javascript/OL3.interaction.js');
        Requirements::javascript('openlayers3/javascript/OL3.featurePopup.js');
        return $this->renderWith(__CLASS__);
    }
}

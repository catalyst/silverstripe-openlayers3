<?php

/**
 * Representation of an Openlayers3 ol.View
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.View.html
 */

class OL3Map extends DataObject
{
    private static $singular_name = 'Map';
    private static $plural_name = 'Maps';

    private static $db = [
        'Title' => 'Varchar',
        'Projection' => 'Varchar',
        'Lat' => 'Decimal(12,6)',
        'Lon' => 'Decimal(12,6)',
        'MinLat' => 'Decimal(12,6)',
        'MinLon' => 'Decimal(12,6)',
        'MaxLat' => 'Decimal(12,6)',
        'MaxLon' => 'Decimal(12,6)',
        'Zoom' => 'Int',
        'MinZoom' => 'Int',
        'MaxZoom' => 'Int',
    ];

    private static $field_labels = [
        'Lat' => 'Latitude',
        'Lon' => 'Longitude',
    ];

    private static $has_one = [
        'Background' => 'OL3Layer',
    ];

    private static $has_many = [
        'Layers' => 'OL3Layer',
    ];

    private static $defaults = [
        'Zoom' => 8,
        'MinZoom' => 0,
        'MaxZoom' => 30,
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($field = $fields->dataFieldByName('Layers')) {
            $field->getConfig()
                ->removeComponentsByType('GridFieldAddExistingAutocompleter')
                ->removeComponentsByType('GridFieldDeleteAction')
                ->addComponent(new GridFieldDeleteAction());
            if (class_exists('GridFieldSortableRows')) {
                $field->getConfig()->addComponent(new GridFieldSortableRows('SortOrder'));
            }
        }

        if ($this->Layers()->Count()) {
            $fields->dataFieldByname('BackgroundID')->setSource($this->Layers()->map());
        } else {
            $fields->removeByName('BackgroundID');
        }

        $fields->dataFieldByName('Zoom')->setRange(0, 30);
        $fields->addFieldsToTab('Root.Constraints', [
            $fields->dataFieldByName('MinZoom')->setRange(0, 30),
            $fields->dataFieldByName('MaxZoom')->setRange(0, 30),
            $fields->dataFieldByName('MinLat'),
            $fields->dataFieldByName('MinLon'),
            $fields->dataFieldByName('MaxLat'),
            $fields->dataFieldByName('MaxLon'),
        ]);

        $fields->dataFieldByName('Projection')->setDescription('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection');

        return $fields;
    }

    /**
     * Getter for the template to retrive the ol.View config object
     * @return String Json representation $this
     */
    public function JsonView()
    {
        return json_encode($this->toMap());
    }

    /**
     * Getter for the template to retrive the ol.layer config for all layers to be displayed
     * @return String Json representation $this->Layers()
     */
    public function JsonLayers()
    {
        return json_encode($this->Layers()->toNestedArray());
    }

    /**
     * Getter for the template to retrive the ol.style config for all styles attached to all layers of the map
     * @return String Json array of all styles necessary to display all vecor layers
     * @see OL3Style::getStyles()
     */
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

    public static function requirements()
    {
        Requirements::css('https://openlayers.org/en/v3.19.1/css/ol.css');
        Requirements::javascript('openlayers3/thirdparty/promise.js');
        Requirements::javascript('openlayers3/thirdparty/fetch.js');
        Requirements::javascript('openlayers3/thirdparty/CustomEvent.js');
        Requirements::javascript('https://openlayers.org/en/v3.19.1/build/ol.js');
        Requirements::javascript('openlayers3/javascript/OL3.base.js');
        Requirements::javascript('openlayers3/javascript/OL3.html.js');
        Requirements::javascript('openlayers3/javascript/OL3.layer.js');
        Requirements::javascript('openlayers3/javascript/OL3.init.js');
    }

    /**
     * @return String **V** of MVC for OL3Map
     */
    public function forTemplate()
    {
        $this->requirements();
        return $this->renderWith(__CLASS__);
    }

    public function validate()
    {
        $result = parent::validate();
        if ($this->MaxZoom < $this->Zoom) $result->error('MaxZoom must be greater than Zoom');
        if ($this->MinZoom > $this->Zoom) $result->error('MinZoom must be less than Zoom');
        return $result;
    }
}

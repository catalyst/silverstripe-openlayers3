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
        'Zoom' => 'Int',
    ];

    private static $field_labels = [
        'Lat' => 'Latitude',
        'Lon' => 'Longitude',
    ];

    private static $has_many = [
        'Layers' => 'OL3Layer',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if ($field = $fields->dataFieldByName('Layers')) {
            $field->getConfig()
                ->addComponent(new GridFieldSortableRows('SortOrder'))
                ->removeComponentsByType('GridFieldAddExistingSearchButton');
        }

        $fields->dataFieldByName('Zoom')->setRange(0, 30);
        $fields->dataFieldByName('Projection')->setRightTitle('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection');

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

    /**
     * @return String **V** of MVC for OL3Map
     */
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
        Requirements::javascript('mysite/javascript/OL3.niwa.js');
        return $this->renderWith(__CLASS__);
    }
}

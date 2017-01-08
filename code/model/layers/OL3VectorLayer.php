<?php

/**
 * Representation of an Openlayers3 ol.layer.Vector
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.layer.Vector.html
 */

class OL3VectorLayer extends OL3Layer
{
    private static $singular_name = 'OpenLayer3 Vector Layer';
    private static $plural_name = 'OpenLayer3 Vector Layers';

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

        // reordering, setting right titels
        $fields->addFieldsToTab(
            'Root.Main',
            [
                $fields->dataFieldByName('SourceType'),
                $fields->dataFieldByName('SourceUrl')->setRightTitle('Prefix with "/OL3Proxy/dorequest?u=" to work around same-origin issues'),
                $fields->dataFieldByName('SourceFeatureTypes')->setRightTitle('Comma separated list of names to identify layers on the server side'),
                $fields->dataFieldByName('SourceProjection')->setRightTitle('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection'),
            ],
            'Opacity'
        );

        return $fields;
    }

    /**
     * Method to collect styles
     * @param &$styles Array to which the styles get added
     * @return void
     * @see OL3Map::JsonStyles()
     * @see OL3Style::getStyles()
     */
    public function getStyles(&$styles)
    {
        foreach(['DefaultStyle', 'HoverStyle', 'SelectStyle'] as $style) {
            if ($curr = $this->$style()) {
                $curr->getStyles($styles);
            }
        }
    }
}

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

    private static $has_one = [
        'DefaultStyle' => 'Ol3StyleStyle',
        'HoverStyle' => 'Ol3StyleStyle',
        'SelectStyle' => 'Ol3StyleStyle',
    ];

    private static $available_source_types = [
        'OL3ClusterSource' => 'Cluster Source',
        'OL3VectorSource' => 'GML Vector Source',
        'OL3JsonVectorSource' => 'JSON Vector Source',
    ];

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

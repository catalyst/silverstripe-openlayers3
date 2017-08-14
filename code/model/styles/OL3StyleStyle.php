<?php

/**
 * File contains the OL3StyleStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.Style
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Style.html
 */

class OL3StyleStyle extends OL3Style
{
    /**
     * Nice singular name for this class to be used in the CMS.
     * 
     * @var string
     */
    private static $singular_name = 'Style Container';

    /**
     * Nice plural name for this class to be used in the CMS.
     * 
     * @var string
     */
    private static $plural_name = 'Style Containers';

    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types.
     * 
     * @var array DB types
     */
    private static $db = [
        'Title' => 'Varchar',
    ];

    /**
     * Used by the ORM to establish class relations
     * Map of has_one components
     * 
     * Keys are component names, values are DataObject class names.
     * 
     * @var array has_one component classes
     */
    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
        'Text' => 'OL3TextStyle',
        'Image' => 'OL3ImageStyle',
    ];
}

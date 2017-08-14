<?php

/**
 * File contains the OL3TextStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.Text
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Text.html
 */

class OL3TextStyle extends OL3Style
{
    /**
     * Map of class properties to persist in the database.
     * Keys are property names, values are data types.
     * 
     * @var array DB types
     */
    private static $db = [
        'TextAlign' => "Enum('left,right,center,end,start','start')",
    ];

    /**
     * Used by the ORM to establish class relations.
     * Map of has_one components.
     * Keys are component names, values are DataObject class names.
     * 
     * @var array  has_one component classes
     */
    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    /**
     * Map of default values to hydrate instances with on creation.
     * Keys are property names, values are scalar values.
     * 
     * @var array
     */
    private static $defaults = [
        'TextAlign' => 'center',
    ];
}

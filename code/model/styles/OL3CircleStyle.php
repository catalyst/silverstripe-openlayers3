<?php

/**
 * File told conatain OL3CircleStyle
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.Circle
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Circle.html
 */

class OL3CircleStyle extends OL3ImageStyle
{
    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types
     * @var string[] DB types
     */
    private static $db = [
        'Radius' => 'Int',
    ];

    /**
     * Used by the ORM to establish class relations
     * Map of has_one components
     * Keys are component names, values are DataObject class names
     * @var string[] has_one component classes
     */
    private static $has_one = [
        'Fill' => 'OL3FillStyle',
        'Stroke' => 'OL3StrokeStyle',
    ];

    /**
     * Map of default values to hydrate instances with on creation
     * Keys are property names, values are scalar values
     * @var mixed[]
     */
    private static $defaults = [
        'Radius' => '20',
    ];

    /**
     * Getter for FieldList that is used for CRUD forms for this class
     * Conatins field customisations, mainly transforming NumericFields to range sliders
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Radius')->setRange(1, 100);

        return $fields;
    }
}

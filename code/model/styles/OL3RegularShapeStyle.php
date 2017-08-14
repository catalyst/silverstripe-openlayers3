<?php

/**
 * File contains the OL3RegularShapeStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.RegularShape
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.RegularShape.html
 */

class OL3RegularShapeStyle extends OL3ImageStyle
{
    /**
     * Map of class properties to persist in the database.
     * Keys are property names, values are data types.
     * 
     * @var array
     */
    private static $db = [
        'InnerRadius' => 'Int',
        'OuterRadius' => 'Int',
        'Points' => 'Int',
        'Angle' => 'Int',
    ];

    /**
     * Used by the ORM to establish class relations.
     * Map of has_one components.
     * Keys are component names, values are DataObject class names.
     * 
     * @var array has_one component classes
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
        'InnerRadius' => 0,
        'OuterRadius' => 25,
        'Points' => 3,
        'Angle' => 0,
    ];

    /**
     * Getter for FieldList that is used for CRUD forms for this class.
     * Conatins field customisations, mainly transforming NumericFields to range
     * sliders and adding field descriptions.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('InnerRadius')->setRange(0, 100)->setDescription('Inner Radius of a star, set to 0 for polygon');
        $fields->dataFieldByName('OuterRadius')->setRange(1, 100)->setDescription('Outer Radius of a star');
        $fields->dataFieldByName('Points')->setRange(3, 8);
        $fields->dataFieldByName('Angle')->setRange(0, 360);

        return $fields;
    }
}

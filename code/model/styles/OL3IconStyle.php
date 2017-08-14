<?php

/**
 * File contains the OL3IconStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.Icon
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Icon.html
 */

class OL3IconStyle extends OL3ImageStyle
{
    /**
     * Map of class properties to persist in the database.
     * Keys are property names, values are data types.
     * 
     * @var array DB types
     */
    private static $db = [
        'Scale' => 'Decimal',
        'Opacity' => 'Decimal',
    ];

    /**
     * Used by the ORM to establish class relations.
     * Map of has_one components.
     * Keys are component names, values are DataObject class names.
     * 
     * @var array has_one component classes
     */
    private static $has_one = [
        'Icon' => 'Image',
    ];

    /**
     * Map of default values to hydrate instances with on creation.
     * Keys are property names, values are scalar values.
     * 
     * @var array
     */
    private static $defaults = [
        'Scale' => 1,
        'Opacity' => 1,
    ];

    /**
     * Getter for FieldList that is used for CRUD forms for this class.
     * Conatins field customisations, mainly transforming NumericFields to range sliders.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Scale')->setRange(0.01, 2, 0.01);
        $fields->dataFieldByName('Opacity')->setRange(0, 1, 0.1);

        return $fields;
    }
}

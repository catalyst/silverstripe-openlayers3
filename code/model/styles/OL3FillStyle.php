<?php

/**
 * File contains the OL3FillStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.Fill
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Fill.html
 */

class OL3FillStyle extends OL3Style
{
    /**
     * Map of class properties to persist in the database.
     * Keys are property names, values are data types.
     * 
     * @var string[] DB types
     */
    private static $db = [
        'Color' => 'Varchar',
    ];

    /**
     * Map of default values to hydrate instances with on creation.
     * Keys are property names, values are scalar values.
     * 
     * @var mixed[]
     */
    private static $defaults = [
        'Color' => 'rgba(255,255,0,.25)',
    ];

    /**
     * Getter for FieldList that is used for CRUD forms for this class.
     * Conatins field customisations, mainly transforming the color field into a color picker.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField('Color', ColorField::create('Color'));

        return $fields;
    }
}

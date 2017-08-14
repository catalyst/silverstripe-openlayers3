<?php

/**
 * File contains the OL3StrokeStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * Representation of an Openlayers3 ol.style.Stroke
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Stroke.html
 */

class OL3StrokeStyle extends OL3Style
{
    /**
     * Map of class properties to persist in the database.
     * Keys are property names, values are data types.
     * 
     * @var array
     */
    private static $db = [
        'Color' => 'Varchar',
        'Width' => 'Int(1)',
    ];

    /**
     * Map of default values to hydrate instances with on creation.
     * Keys are property names, values are scalar values.
     * 
     * @var array
     */
    private static $defaults = [
        'Color' => 'rgba(192,0,0,.5)',
        'Width' => '1',
    ];

    /**
     * Getter for FieldList that is used for CRUD forms for this class.
     * Conatins field customisations, transforming NumericFields to range sliders
     * and the color field to a colorpicker.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField('Color', ColorField::create('Color'));
        $fields->dataFieldByName('Width')->setRange(1, 5, 1, 'px');

        return $fields;
    }
}

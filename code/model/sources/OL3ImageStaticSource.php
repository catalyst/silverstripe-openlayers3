<?php

/**
 * File told conatain OL3ImageStaticSource
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A wrapper for ol.source.ImageStatic
 * @link https://openlayers.org/en/latest/apidoc/ol.source.ImageStatic.html
 */

class OL3ImageStaticSource extends OL3Source
{
    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types
     * @var array
     */
     private static $db = [
        'Url' => 'Varchar(255)',
        'Lat' => 'Decimal(12,6)',
        'Lon' => 'Decimal(12,6)',
        'Scale' => 'Decimal(12,6)',
        'Projection' => 'Varchar',
    ];

    /**
     * Getter for FieldList that is used for CRUD forms for this class
     * Conatins field customisations, adds FormField::$description
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Projection')
            ->setDescription('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection');

        return $fields;
    }
}

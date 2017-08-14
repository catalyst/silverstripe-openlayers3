<?php

/**
 * File contains the OL3TileWMSSource class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A wrapper for ol.source.TileWMS
 * @link https://openlayers.org/en/latest/apidoc/ol.source.TileWMS.html
 */

class OL3TileWMSSource extends OL3Source
{
    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types
     * @var array
     */
     private static $db = [
        'Url' => 'Varchar(255)',
        'Layers' => 'Varchar',
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

        $fields->dataFieldByName('Layers')
            ->setDescription('Comma separated list of names to identify layers on the server side');
        $fields->dataFieldByName('Projection')
            ->setDescription('Common values are "EPSG:3857" or "EPSG:4326", leave empty for server side default projection');

        return $fields;
    }
}

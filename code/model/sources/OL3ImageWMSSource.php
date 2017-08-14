<?php

/**
 * File contains the OL3ImageWMSSource class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

class OL3ImageWMSSource extends OL3Source
{
    /**
     * @var array
     */
    private static $db = [
       'Url' => 'Varchar(255)',
       'Layers' => 'Varchar',
       'Projection' => 'Varchar',
   ];

    /**
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

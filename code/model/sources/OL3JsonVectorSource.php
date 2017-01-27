<?php

/**
 * File told conatain OL3JsonVectorSource
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A wrapper for ol.source.Vector
 * @link https://openlayers.org/en/latest/apidoc/ol.source.Vector.html
 */

class OL3JsonVectorSource extends OL3Source
{
    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types
     * @var Array
     */
    private static $db = [
        'Url' => 'Varchar(255)',
    ];
}

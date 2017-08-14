<?php

/**
 * File contains the OL3ClusterSource class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A wrapper for ol.source.Cluster
 * @link https://openlayers.org/en/latest/apidoc/ol.source.Cluster.html
 */

class OL3ClusterSource extends OL3VectorSource
{
    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types.
     * 
     * @var array
     */
    private static $db = [
        'Distance' => 'Int',
    ];
}

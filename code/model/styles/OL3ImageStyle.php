<?php

/**
 * File contains the OL3ImageStyle class.
 *
 * @package openlayers3
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 */

/**
 * A base class for ol.style.images
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.style.Image.html
 */

class OL3ImageStyle extends OL3Style
{
    /**
     * Getter for FieldList that is used for CRUD forms for this class.
     * Conatins field customisations, mainly choosing the concrete class for this record.
     * 
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // add a class selector field
        $subclasses = ClassInfo::subclassesFor(__CLASS__);

        if (isset($subclasses[__CLASS__])) {
            unset($subclasses[__CLASS__]);
        }

        if (count($subclasses)) {
            $fields->addFieldToTab('Root.Main', DropdownField::create('ClassName', 'Style Type', $subclasses));
        }

        if ($this->ClassName == __CLASS__) {
            $fields->addFieldToTab('Root.Main', HeaderField::create('Notice', 'Please save record to see more fields for the specific record type you selected.'));
        }

        return $fields;
    }
}

<?php

/**
 * A base class for ol.layers
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.layer.html
 */

class OL3Layer extends DataObject
{
    private static $singular_name = 'OpenLayer3 Layer';
    private static $plural_name = 'OpenLayer3 Layers';

    private static $db = [
        'SortOrder' => 'Int',
        'Title' => 'Varchar',
        'Visible' => 'Boolean(1)',
        'Opacity' => 'Decimal(3,2,1)',
    ];

    private static $defaults = [
        'Visible' => true,
        'Opacity' => 1,
    ];

    private static $summary_fields = [
        'Title',
        'ClassName',
        'Visible',
    ];

    private static $default_sort = [
        'SortOrder',
    ];

    private static $has_one = [
        'Map' => 'OL3Map',
    ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->removeByName('SortOrder');

        // push the field to the end of the fieldlist and add range
        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('Opacity')->setRange(0,1,.1), 'Visible');

        // add a class selector field on creation
        $subclasses = ClassInfo::subclassesFor(__CLASS__);

        if (isset($subclasses[__CLASS__])) {
            unset($subclasses[__CLASS__]);
        }

        if (count($subclasses)) {
            $field = DropdownField::create('ClassName', 'Layer Type', $subclasses);
            $fields->addFieldToTab('Root.Main', $field, 'Title');
            if ($this->exists() || $this->ClassName != __CLASS__) {
                $field = $field->performReadonlyTransformation();
            } else {
                $fields->addFieldToTab('Root.Main', HeaderField::create('Notice', 'Please save record to see more fields for the specific record type you selected.'));
            }
        }

        return $fields;
    }
}

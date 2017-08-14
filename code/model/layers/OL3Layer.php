<?php

/**
 * File contains the OL3Layer class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A base class for ol.layers
 * @link http://openlayers.org/en/v3.19.1/apidoc/ol.layer.html
 */

class OL3Layer extends DataObject
{
    /**
     * Nice singular name for this class to be used in the CMS
     * @var string
     */
    private static $singular_name = 'OpenLayer3 Layer';

    /**
     * Nice plural name for this class to be used in the CMS
     * @var string
     */
    private static $plural_name = 'OpenLayer3 Layers';

    /**
     * Map of class properties to persist in the database
     * Keys are property names, values are data types
     * @var string[] DB types
     */
    private static $db = [
        'SortOrder' => 'Int',
        'Title' => 'Varchar',
        'Visible' => 'Boolean(1)',
        'Opacity' => 'Decimal(3,2,1)',
    ];

    /**
     * Used by the ORM to establish class relations
     * Map of has_one components
     * Keys are component names, values are DataObject class names
     * @var string[] has_one component classes
     */
    private static $has_one = [
        'Map' => 'OL3Map',
        'Source' => 'OL3Source',
    ];

    /**
     * Map of default values to hydrate instances with on creation
     * Keys are property names, values are scalar values
     * @var mixed[]
     */
    private static $defaults = [
        'Visible' => true,
        'Opacity' => 1,
    ];

    /**
     * Column definition used mainly while creating columns for GridFields
     * Keys are property names, dot notated compontent properties or class methods
     * values are nice column names
     * @var string[] nice column names
     */
    private static $summary_fields = [
        'Title' => 'Title',
        'ClassName' => 'Type',
        'Source.ClassName' => 'Type',
        'Visible' => 'Visible',
    ];

    /**
     * Property by which the records of this class are sorted by by default
     * @var string
     */
    private static $default_sort = [
        'SortOrder',
    ];

    /**
     * Map of available sources that work this class of layer.
     * Keys are class names, values are nice names
     * This base class is not supposed to have sources, only its extensions
     * @var string[] nice class names
     */
    private static $available_source_types = [];

    /**
     * Getter for the Source component
     * This method always returns a source, while the magic component getter it "overwrites" doesn't
     * @return DataList
     */
    public function Source()
    {
        return $this->getComponent('Source') ?: OL3Source::create();
    }

    /**
     * Getter for FieldList that is used for CRUD forms for this class
     * Conatins field customisations, mainly to choose the concrete class for this record and
     * to display additional fields to edit the source of this record inline
     * @return FieldList
     */
    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if (class_exists('GridFieldSortableRows')) {
            $fields->removeByName('SortOrder');
        }

        // push the field to the end of the fieldlist and add range
        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('Opacity')->setRange(0, 1, .1), 'Visible');

        // add a class selector field on creation
        {
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
        }

        // edit $this->Source() in-line
        if ($sourceTypes = $this->config()->get('available_source_types')) {
            $source = $this->Source();

            $fields->addFieldToTab('Root.Source', DropdownField::create('Source_ClassName', 'Source Type', $sourceTypes, $source->ClassName));

            if ($dataFields = $source->getCMSFields()->dataFields()) {
                foreach ($dataFields as $fieldName => $field) {

                // use componentName_fieldName syntax to avoid field name conflicts
                $name = 'Source_' . $field->getName();

                // set the value only in the first iteration since it will not be available in the next loop hence be null
                if (strpos($fieldName, '_') === false) {

                    // UploadField edge case: UploadField::setValue() is inconsistent with the way data is being
                    // loaded into the flattened form fields
                    if (is_a($source->has_one($fieldName), 'File', true) && $field instanceof UploadField) {
                        $field->setValue(null, $source);
                    } else {
                        $field->setValue($source->$fieldName);
                    }
                }

                    $fields->addFieldToTab('Root.Source', $field->setName($name));
                }
            }
        }
        $fields->removeByName('SourceID');

        return $fields;
    }

    /**
     * Getter for the persistent properties.
     * This implementation adds the properties of the source component
     * Used in OL3Map::JsonLayers() to export the layer structure to the template
     * @see OL3Map::JsonLayers()
     * @return Array
     */
    public function toMap()
    {
        $map = parent::toMap();
        $map['Source'] = $this->Source()->toMap();
        return $map;
    }

    /**
     * Hook for logic to be executed before the record is written to the database
     * Takes care of the data coming from the extra fields for the inline editing of the source component
     * @see OL3Layer::getCMSFields()
     * @return void
     */
    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // components cache
        $source = $this->Source();

        // loop over properties
        foreach ($this->record as $key => $val) {

            // split property names by underscore and take the first name as the child style node comontents name
            $segments = explode('_', $key);

            // if the first segment is not 'Source' or no unconsumed segements are left, this is not a source field
            $isSourceFieldValue = array_shift($segments) == 'Source' && count($segments);

            if ($isSourceFieldValue) {

                // concatenate remaning segments to be the data for recursive calls
                $componentPropertyName = implode('_', $segments);

                // set the value of the comontent
                // if the class has been changed, don't set the value but create a new instance of the new class
                if ($componentPropertyName == 'ClassName' && $source->ClassName != $val) {
                    $source = $source->newClassInstance($val);
                } else {
                    $source->$componentPropertyName = $val;
                }
            }
        }

        // write all components to db
        $this->SourceID = $source->write();
    }
}

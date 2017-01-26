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

    private static $has_one = [
        'Map' => 'OL3Map',
        'Source' => 'OL3Source',
    ];

    private static $defaults = [
        'Visible' => true,
        'Opacity' => 1,
    ];

    private static $summary_fields = [
        'Title' => 'Title',
        'ClassName' => 'Type',
        'Source.ClassName' => 'Type',
        'Visible' => 'Visible',
    ];

    private static $default_sort = [
        'SortOrder',
    ];

    private static $available_source_types = [];

    public function Source()
    {
        return $this->getComponent('Source') ?: OL3Source::create();
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        if (class_exists('GridFieldSortableRows')) {
            $fields->removeByName('SortOrder');
        }

        // push the field to the end of the fieldlist and add range
        $fields->addFieldToTab('Root.Main', $fields->dataFieldByName('Opacity')->setRange(0,1,.1), 'Visible');

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

            if ($dataFields = $source->getCMSFields()->dataFields()) foreach ($dataFields as $fieldName => $field) {

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

            $fields->removeByName('SourceID');
            // $fields->replaceField('SourceID', HiddenField::create('SourceID'));
        }

        return $fields;
    }

    public function toMap()
    {
        $map = parent::toMap();
        $map['Source'] = $this->Source()->toMap();
        return $map;
    }

    public function onBeforeWrite()
    {
        parent::onBeforeWrite();

        // components cache
        $source = $this->Source();

        // loop over properties
        foreach ($this->record as $key => $val) {

            // split property names by underscore and take the first name as the child style node comontents name
            $segments = explode('_', $key);
            $isSourceFieldValue = array_shift($segments) == 'Source' && count($segments);

            // if no unconsumed segements are left, this is not a component
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

<?php

class OL3Style extends DataObject
{
    private static $singular_name = 'Style';
    private static $plural_name = 'Styles';

    private static $summary_fields = [
        'Title' => 'Title',
        'singular_name' => 'Type',
    ];

    public function getTitle()
    {
        return 'new Style';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // select layer type on creation
        if (!$this->exists() && $this->ClassName = __CLASS__) {

            $subclasses = ClassInfo::subclassesFor(__CLASS__);

            if (isset($subclasses[__CLASS__])) {
                unset($subclasses[__CLASS__]);
            }

            if (isset($subclasses['OL3ImageStyle'])) {
                unset($subclasses['OL3ImageStyle']);
            }

            if (count($subclasses)) {
                $fields->addFieldToTab('Root.Main', DropdownField::create('ClassName', 'Style Type', $subclasses));
            }
        }

        return $fields;
    }
}

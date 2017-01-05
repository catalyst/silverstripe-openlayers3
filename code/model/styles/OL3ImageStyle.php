<?php

class OL3ImageStyle extends OL3Style
{
    private static $singular_name = 'Image Style';
    private static $plural_name = 'Image Styles';

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // select layer type on creation
        if (!$this->exists() && $this->ClassName = __CLASS__) {

            $subclasses = ClassInfo::subclassesFor(__CLASS__);

            if (isset($subclasses[__CLASS__])) {
                unset($subclasses[__CLASS__]);
            }

            if (count($subclasses)) {
                $fields->addFieldToTab('Root.Main', DropdownField::create('ClassName', 'Image Style Type', $subclasses));
            }
        }

        return $fields;
    }
}

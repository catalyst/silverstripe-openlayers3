<?php

class OL3FillStyle extends OL3Style
{
    private static $singular_name = 'Fill Style';
    private static $plural_name = 'Fill Styles';

    private static $db = [ 'Color' => 'Varchar' ];

    public function getTitle()
    {
        return $this->exists() ? "Fill Color: {$this->Color}" : 'new Fill Style';
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->dataFieldByName('Color')->setAttribute('type', 'color');

        return $fields;
    }
}

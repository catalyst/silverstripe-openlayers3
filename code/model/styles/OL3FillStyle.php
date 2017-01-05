<?php

class OL3FillStyle extends OL3Style
{
    private static $singular_name = 'Fill Style';
    private static $plural_name = 'Fill Styles';

    private static $db = [ 'Color' => 'Varchar' ];

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        $fields->replaceField('Color', ColorField::create('Color'));

        return $fields;
    }
}

<?php

/**
 * File told conatain ColorField
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * A FormField to edit colors with opacity in the rgba() css format
 */

class ColorField extends TextField
{
    /**
     * Data format to be used in PHP and JS as a PCRE compatible regex.
     *
     * @var string
     */
    private static $regex = '^rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d?\.?\d+)\s*\)$';

    /**
     * Make sure the color value matches the format
     * @param Validator $validator
     * @return bool
     */
    public function validate($validator)
    {
        if (!$this->value && !$validator->fieldIsRequired($this->name)) {
            return true;
        }

        if (preg_match('/' . $this->config()->get('regex') . '/', $this->value)) {
            return true;
        }

        $validator->validationError(
            $this->name,
            _t(
                'ColorField.VALIDATION', "'{value}' is not a valid color, only RGBa colors can be accepted for this field",
                array('value' => $this->value)
            ),
            "validation"
        );
        
        return false;
    }

    /**
     * Overrides FormField::Field() to add RefEx and requirements
     * @param array $properties
     * @return string
     */
    public function Field($properties = array())
    {
        Requirements::javascript('openlayers/javascript/StyleFields.js');
        Requirements::css('openlayers/css/StyleFields.css');
        $this->setAttribute('pattern', $this->config()->get('regex'));
        
        return parent::Field($properties);
    }
}

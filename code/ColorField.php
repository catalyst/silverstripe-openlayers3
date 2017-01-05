<?php

class ColorField extends TextField
{
    private static $regex = '^rgba\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d?\.?\d+)\s*\)$';

    public function validate($validator) {
		if(!$this->value && !$validator->fieldIsRequired($this->name)) {
			return true;
		}

        // ___()
		if(preg_match('/' . $this->config()->get('regex') . '/', $this->value)) return true;

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

    public function Field($properties = array())
    {
        Requirements::javascript('openlayers/javascript/StyleFields.js');
        Requirements::css('openlayers/css/StyleFields.css');
        $this->setAttribute('pattern', $this->config()->get('regex'));
        return parent::Field($properties);
    }
}

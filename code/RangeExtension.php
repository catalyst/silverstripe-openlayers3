<?php

/**
 * File contains the RangeExtension class.
 *
 * @author Catalyst SilverStripe Team <silverstripedev@catalyst.net.nz>
 * @package openlayers3
 */

/**
 * An extension to NumericField to display a HTML5 range slider
 */

class RangeExtension extends Extension
{
    /**
     * Default attributes for the range control.
     * 
     * @var array
     */
    protected $range = [
        'min' => null,
        'max' => null,
        'step' => 1,
        'data-unit' => null,
    ];

    /**
     * Convenience setter for all attributes of the range control.
     * This is what switches out the controls.
     * 
     * @param int $min the mininum that can be set on the field
     * @param int $max the maxinum that can be set on the field
     * @param int $step (optional) the incement, default is 1
     * @param int $unit (optional) a unit to be displayed after the value, default is null
     * @return NumericField $this for chaining
     */
    public function setRange($min, $max, $step = 1, $unit = null)
    {
        return $this->setMin($min)->setMax($max)->setStep($step)->setUnit($unit);
    }

    /**
     * Setter for $this->min.
     * 
     * @param int $min the mininum that can be set on the field
     * @return NumericField $this for chaining
     */
    public function setMin($min)
    {
        if (is_numeric($min)) {
            $this->range['min'] = $min;
        }
        return $this->owner;
    }

    /**
     * Setter for $this->max.
     * 
     * @param int $max the maxinum that can be set on the field
     * @return NumericField $this for chaining
     */
    public function setMax($max)
    {
        if (is_numeric($max)) {
            $this->range['max'] = $max;
        }
        return $this->owner;
    }

    /**
     * Setter for $this->step.
     * 
     * @param int $step the incement
     * @return NumericField $this for chaining
     */
    public function setStep($step)
    {
        if (is_numeric($step)) {
            $this->range['step'] = $step;
        }
        return $this->owner;
    }

    /**
     * Setter for $this->unit.
     * 
     * @param int $unit the unit to be displayed after the value
     * @return NumericField $this for chaining
     */
    public function setUnit($unit)
    {
        if ($unit !== null) {
            $this->range['data-unit'] = $unit;
        }
        return $this->owner;
    }

    /**
     * Hook to replace the control.
     * 
     * @param int $unit the unit to be displayed after the value
     * @return void
     */
    public function onBeforeRender()
    {
        if (is_numeric($this->range['min']) && is_numeric($this->range['max'])) {
            $this->owner->setAttribute('type', 'range')->addExtraClass('range');
            foreach ($this->range as $key => $val) {
                $this->owner->setAttribute($key, $val);
            }
            Requirements::javascript('openlayers3/javascript/StyleFields.js');
            Requirements::css('openlayers3/css/StyleFields.css');
        }
    }
}

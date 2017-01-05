<?php

class RangeExtension extends Extension
{
    protected $range = [
        'min' => null,
        'max' => null,
        'step' => 1,
        'data-unit' => null,
    ];

    public function setRange($min, $max, $step = 1, $unit = null)
    {
        return $this->setMin($min)->setMax($max)->setStep($step)->setUnit($unit);
    }

    public function setMin($min)
    {
        if (is_numeric($min)) $this->range['min'] = $min;
        return $this->owner;
    }

    public function setMax($max)
    {
        if (is_numeric($max)) $this->range['max'] = $max;
        return $this->owner;
    }

    public function setStep($step)
    {
        if (is_numeric($step)) $this->range['step'] = $step;
        return $this->owner;
    }

    public function setUnit($unit)
    {
        if ($unit !== null) $this->range['data-unit'] = $unit;
        return $this->owner;
    }

    public function onBeforeRender()
    {
        if (is_numeric($this->range['min']) && is_numeric($this->range['max'])) {
            $this->owner->setAttribute('type', 'range')->addExtraClass('range');
            foreach ($this->range as $key => $val) $this->owner->setAttribute($key, $val);
            Requirements::javascript('openlayers3/javascript/StyleFields.js');
            Requirements::css('openlayers3/css/StyleFields.css');
        }
    }
}

<?php

class OL3VectorLayer extends OL3Layer
{
    private static $singular_name = 'OpenLayer3 Vector Layer';
    private static $plural_name = 'OpenLayer3 Vector Layer';

    private static $db = [
        'SourceType' => "Enum('polygon,line,point')",
        'SourceUrl' => 'Varchar(255)',
        'SourceFeatureTypes' => 'Varchar',
        'SourceProjection' => 'Varchar',
    ];

    private static $has_one = [
        'DefaultStyle' => 'Ol3StyleStyle',
        'HoverStyle' => 'Ol3StyleStyle',
        'SelectStyle' => 'Ol3StyleStyle',
    ];

    public function getStyles(&$styles)
    {
        foreach(['DefaultStyle', 'HoverStyle', 'SelectStyle'] as $style) {
            if ($curr = $this->$style()) {
                $curr->getStyles($styles);
            }
        }
    }
}

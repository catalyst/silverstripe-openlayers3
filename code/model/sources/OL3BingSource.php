<?php

class OL3BingSource extends OL3Source
{
    private static $bing_api_key = 'Bing Maps API key. Get yours at http://www.bingmapsportal.com/. Required.';

    public function toMap()
    {
        $map = parent::toMap();
        $map['Key'] = $this->config()->get('bing_api_key');
        return $map;
    }
}

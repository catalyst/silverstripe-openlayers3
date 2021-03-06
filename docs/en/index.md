## What does it do?

Provides OpenLayers3.19.1 integration for the SilverStripe 3.x CMS and Framework.

## Installation

    #> composer require catalyst/silverstripe-openlayers3
    #> ./framework/sake dev/build flush=all

## Configuration

If you have CORS issues with your WFS, you can use the the built-in proxy. For this to work you have to whitelist the WFS end point in mysite/_config/config.yml like this:

    OL3Proxy_Controller:
      allowed_host:
        - ahocevar.com

If you want to use Bing services you need to supply an API key in mysite/_config/config.yml like this:

    OL3BingMapsSource:
      bing_api_key: 'your-key-goes-here'

## Usage

### Code

This module provides you with a configurable Map in the form of a DataObject that can be used in templates, nothing more. Here is an easy example for how to use it:

    class MapPage extends Page
    {
    	private static $has_one = [
    		'Map' => 'OL3Map',
    	];

    	public function getCMSFields()
    	{
    		$fields = parent::getCMSFields();
    		$fields->addFieldToTab('Root.Main', DropdownField::create('MapID', 'Map', OL3Map::get()->map()), 'Content');
    		return $fields;
    	}
    }

    class MapPage_Controller extends Page_Controller {}

The above code adds a new page type to which you can add a predefined map. In your themes/<your-theme-name>/templates/Layout/MapPage.ss you can now add `$Map` at the position where you want the map to appear.

Run dev/build and you are done coding.

### Basic Setup

To get you started you need to setup a map in the CMS for the above example to work. Login to the CMS, go to the OpenLayers3 admin pane and

1. create a new map.
2. Enter a name for the map (the name is for admin purposes only, in your MapPage setup you can select maps by their names), then switch to the layers tab.
3. Add a new layer,
4. give it a title,
5. set it to OL3TileLayer and
6. hit save. Switch to the source tab, make sure that the source type is set to OSM (OpenStreetMap) and
7. hit save again. Go to the pages pane in the admin,
8. create a new page of type MapPage, pick the map you just created and save and publish

In the frontend you should now see a map, showing the Gulf of Guinea because that is the default center of maps (0° N/0° W). That can be adjusted in the Openlayers pane for the map object.

### GeoJson Vector Layer

Wikipedia [https://en.wikipedia.org/wiki/GeoJSON] provides a basic example for GeoJSON, that you can use in a vector layer. These layers are rendered by Openlayers using styles. You can create a default style easily: Log in to the CMS, go to the Openlayers admin pane, pick the Styles Container tab,

1. create a new style,
2. give it a name and
3. save _twice_

Next you go back to the map tab, chose the map you created earlier, go to the layers tab and

1. creating a new layer.
2. Give it a title
3. set the type to OL3VectorLayer and
4. hit save. Go to the source tab
5. set the type to vector source and format to GeoJSON.
6. Enter URL to point to http://your-domain/path-to-project/openlayers3/docs/geo.json. Then select the styles tab and
7. choose the style you created as the default style

You have just created a GeoJSON vector layer. When you now go the map in the frontend navigate the map to Singapore to see the the vectors.

### GML WFS Vector Layer

To create a WFS layer in GML format follow steps 1 - 4 and then

5. set the type to vector source and format to GML.
6. Enter URL to point to https://ahocevar.com/geoserver/wfs.
7. Feature types to usa:states and projection to 'EPSG:3857'. Then select the styles tab and
8. choose the style you created as the default style

When you navigate the front end map to the US, you should see the WFS data rendered as a map layer.

## Trouble Shooting

### General Problems

While the Openlayers JS library very powerful and feature rich it is also not very helpful, when you make a mistake. When something goes wrong, you want to have a look in your browsers console to check for JS errors, next you check the network pane and make sure you see requests for the layers and check the responses for error messages from the layer services.

### Cross Origin WFS

Due to the nature of client side WFS AJAX request they are subject to CORS [https://en.wikipedia.org/wiki/Cross-origin_resource_sharing]. If you intend to use a restricted service, you can elevate the level of trust by using the built-in proxy. Prepend your WFS url with: `/OL3Proxy/dorequest?u=`. If the original URL had a question mark  replace it with an ampersand. E.g.:

    http://domain/mapserv?mapfile=default

you change it to:

    /OL3Proxy/dorequest?u=http://domain/mapserv&mapfile=default

Don't forget to whitelist domain in the proxy setup (s.a.)

## Releasing

Releases are provided on a best-effort basis using Semantic Versioning or "semver" for short.

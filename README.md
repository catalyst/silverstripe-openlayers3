# OpenLayers 3

## Introduction

This module is a wrapper for the OpenLayers 3 javascript library to be used in the Silverstripe framework. You can create maps in the CMS, add layers and style features. You get a OL3Map DataObject e.g. to be used in your custom page types.

## Requirements

 * SilverStripe 3.1

## Installation

Installation can be done either by composer or by manually downloading the
release from Gitlab.

After installation, make sure you rebuild your database through `dev/build`.

## Links ##

 * [Documentation](docs/en/index.md)
 * [OpenLayers 3 website](https://openlayers.org/)
 * [OpenLayers 3 on GitHub](https://github.com/openlayers/ol3)

## Features

This module is a work in progress and only supports a subset of the many feature of the js lib, but it is built in a way that it can be extended to implement missing features.

### Supported Features

 * Open Street Map layers
 * Tile layers
 * Vector layers
 * WFS sources in XML format for vector layers

### Missing Features

 * WFS sources in Json format

### Extending the module

The basic idea is that the Openlayers3 structure is tightly wrapped in PHP. E.g. you will find that a lot of the class names and properties of the Openlayers3 JS lib can be found in the PHP code. All the data entered in the CMS to configure the view, layers and styles are collected in the OL3Map class and pushed to the browser throught its methods OL3Map::JsonView(),  OL3Map::JsonLayers() and  OL3Map::JsonStyles(). The extendable javascript that reads this information uses generic factories to create the Openlayers3 objects that make up your map.

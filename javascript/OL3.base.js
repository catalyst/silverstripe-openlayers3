// @requires OL3.base.js

function OL3(config) {

    var ol3 = this,
        constructorType,
        constructorElement,
        constructorVal = config;

    switch (true) {
        case typeof config === 'undefined':
            constructorType = constructorType || 'auto';
            config = '#map';
        case typeof config === 'string':
            constructorType = constructorType || 'querySelector';
            config = document.querySelector(config);
        case (typeof config === 'object') && (config.nodeType === 1) && (typeof config.style === 'object') && (typeof config.ownerDocument === 'object'):
            constructorElement = config;
            constructorType = constructorType || 'element';
            config = {
                view:   JSON.parse(config.getAttribute('data-view')),
                layers: JSON.parse(config.getAttribute('data-layers')),
                styles: JSON.parse(config.getAttribute('data-styles'))
            };
        case typeof config === 'object':
            constructorType = constructorType || 'object';
            break;
    }

    ol3.config = config;
    ol3.cache = {};

    ol3.render = function(element) {
        element = element || constructorElement;
        return ol3.cache.map = new ol.Map({
            target: element,
            view: ol3.view.create()
        });
    };

    ol3.view = {
        create: function(config) {

            var extent;

            config = config || ol3.config.view;

            if (
                parseFloat(config.MinLon) &&
                parseFloat(config.MinLon) &&
                parseFloat(config.MaxLat) &&
                parseFloat(config.MaxLon)
            ) {
                extent = [
                    parseFloat(config.MinLon),
                    parseFloat(config.MinLat),
                    parseFloat(config.MaxLon),
                    parseFloat(config.MaxLat)
                ];
                extent = ol.proj.transformExtent(extent, 'EPSG:4326', config.Projection);
            }

            return new ol.View({
                projection: config.Projection,
                center: ol.proj.fromLonLat([parseFloat(config.Lon), parseFloat(config.Lat)]),
                extent: extent,
                minZoom: parseInt(config.MinZoom) || undefined,
                maxZoom: parseInt(config.MaxZoom) || undefined,
                zoom: config.Zoom
            });
        }
    };

    ol3.style = {
        size: 0,
        cache: {},
        get: function(id, size) {

            if (!parseInt(id)) return;

            var styleConfig = ol3.config.styles[id],
                factoryName = styleConfig.ClassName,
                cacheId = id + ':' + size,
                style = ol3.style.cache[cacheId] || ol3.style.create[factoryName](styleConfig, size);

            if (!ol3.style.cache[cacheId]) {
                ol3.style.cache[cacheId] = style;
            }

            return style;
        },
        // @todo: work out a more versatile way to utilise size, not only for OL3TextStyles
        create: {
            OL3StyleStyle: function(config, size) {
                return new ol.style.Style({
                    fill: ol3.style.get(config.FillID, size),
                    image: ol3.style.get(config.ImageID, size),
                    stroke: ol3.style.get(config.StrokeID, size),
                    text: ol3.style.get(config.TextID, size),
                });
            },
            OL3FillStyle: function(config, size) {
                return new ol.style.Fill({
                    color: config.Color
                });
            },
            OL3StrokeStyle: function(config, size) {
                return new ol.style.Stroke({
                    color: config.Color,
                    width: parseInt(config.Width)
                });
            },
            OL3TextStyle: function(config, size) {
                return new ol.style.Text({
                    text: size,
                    textAlign: config.TextAlign,
                    fill: ol3.style.get(config.FillID, size),
                    stroke: ol3.style.get(config.StrokeID, size)
                });
            },
            OL3CircleStyle: function(config, size) {
                return new ol.style.Circle({
                    radius: parseInt(config.Radius),
                    fill: ol3.style.get(config.FillID, size),
                    stroke: ol3.style.get(config.StrokeID, size)
                });
            },
            OL3RegularShapeStyle: function(config, size) {
                var shape = {
                    points: parseInt(config.Points),
                    angle: (parseInt(config.Angle) / 180) * Math.PI,
                    fill: ol3.style.get(config.FillID, size),
                    stroke: ol3.style.get(config.StrokeID, size)
                };
                if (parseInt(config.InnerRadius) === 0) {
                    shape.radius = parseInt(config.OuterRadius);
                } else {
                    shape.radius1 = parseInt(config.OuterRadius);
                    shape.radius2 = parseInt(config.InnerRadius);
                }
                return new ol.style.RegularShape(shape);
            },
            OL3IconStyle: function(config, size) {
                return new ol.style.Icon({
                    src: config.IconSRC,
                    scale: parseFloat(config.Scale),
                    opacity: parseFloat(config.Opacity)
                });
            }
        }
    };

    for (var i = 0; i < OL3.extensions.length; i++) {
        var extension = OL3.extensions[i];
        extension.call(ol3);
    }
};

OL3.extensions = [];
OL3.extend = function(extension) { OL3.extensions.push(extension); };

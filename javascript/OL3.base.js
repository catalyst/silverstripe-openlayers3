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
            config = config || ol3.config.view;
            return new ol.View({
                projection: config.Projection,
                center: ol.proj.fromLonLat([parseFloat(config.Lon), parseFloat(config.Lat)]),
                zoom: config.Zoom
            });
        }
    };

    ol3.style = {
        get: function(id, feature) {
            if (!parseInt(id)) return;
            var styleConfig = ol3.config.styles[id];
            var factoryName = styleConfig.ClassName;
            var style = ol3.style.create[factoryName](styleConfig, feature);
            return style;
        },
        create: {
            OL3StyleStyle: function(config, feature) {
                return new ol.style.Style({
                    fill: ol3.style.get(config.FillID, feature),
                    image: ol3.style.get(config.ImageID, feature),
                    stroke: ol3.style.get(config.StrokeID, feature),
                    text: ol3.style.get(config.TextID, feature),
                });
            },
            OL3FillStyle: function(config, feature) {
                return new ol.style.Fill({
                    color: config.Color
                });
            },
            OL3StrokeStyle: function(config, feature) {
                return new ol.style.Stroke({
                    color: config.Color,
                    width: config.Width
                });
            },
            OL3TextStyle: function(config, feature) {

                // @todo: work out a more versatile way to utilise feature, not only for OL3TextStyles

                var text = {}, features;

                if (feature (features = feature.get('features'))) {
                    text.size = feature.get('features').length.toString();
                }

                return new ol.style.Text({
                    text: text.size,
                    fill: ol3.style.get(config.FillID, feature)
                });
            },
            OL3CircleStyle: function(config, feature) {
                return new ol.style.Circle({
                    radius: config.Radius,
                    fill: ol3.style.get(config.FillID, feature),
                    stroke: ol3.style.get(config.StrokeID, feature)
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

// this init code and needs to go elsewhere
(function($) {
    $(function() {  // IMPORTANT!! wait for all OL3 extensions to be loaded
        var ol3 = new OL3();
        map = ol3.render();
        ol3.layer.init();
        ol3.interaction.init();
    });
}(jQuery));

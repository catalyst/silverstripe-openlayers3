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
        get: function(id, text) {
            if (!parseInt(id)) return;
            var styleConfig = ol3.config.styles[id];
            var factoryName = styleConfig.ClassName;
            return ol3.style.create[factoryName](styleConfig, text);
        },
        create: {
            OL3StyleStyle: function(config, text) {
                return new ol.style.Style({
                    fill: ol3.style.get(config.FillID),
                    image: ol3.style.get(config.ImageID, text),
                    stroke: ol3.style.get(config.StrokeID),
                    text: ol3.style.get(config.TextID, text),
                });
            },
            OL3FillStyle: function(config) {
                return new ol.style.Fill({
                    color: config.Color
                });
            },
            OL3StrokeStyle: function(config) {
                return new ol.style.Stroke({
                    color: config.Color,
                    width: config.Width
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

(function($) {
    $(function() {  // IMPORTANT!! wait for all OL3 extensions to be loaded
        var ol3 = new OL3();
        map = ol3.render();
        // console.log(andy.style.get('11'));

        ol3.layer.init();
    });
}(jQuery));

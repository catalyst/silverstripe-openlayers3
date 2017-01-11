// @requires OL3.base.js

OL3.extend(function(){

    var ol3 = this;

    ol3.layer = {
        init: function() {

            var map = ol3.cache.map;

            if (!map) {
                console.err('please render map before initialising layers.');
                return;
            }

            for (var i = 0; i < ol3.config.layers.length; i++) {

                var layer,
                    layerConfig = ol3.config.layers[i],
                    factoryName = layerConfig.ClassName,
                    factory = this.create[factoryName];

                layer = factory(layerConfig);
                layer.config = layerConfig;
                layer.set('Title', layerConfig.Title);
                map.addLayer(layer);
            }
        },
        create: {
            OL3TileLayer: function(config) {

                var factoryName = config.SourceType,
                    factory = ol3.source.create[factoryName],
                    source = factory(config);

                return new ol.layer.Tile({
                    source: source,
                    opacity: parseFloat(config.Opacity),
                    visible: config.Visible == '1'
                });
            },
            OL3VectorLayer: function(config) {

                var source, style, hoverStyle, selectStyle;

                if (config.SourceType == 'point') {
                    source = ol3.source.create.Group(config);
                } else {
                    source = ol3.source.create.Vector(config);
                }

                return new ol.layer.Vector({
                    title: config.Title,
                    source: source,
                    opacity: parseFloat(config.Opacity),
                    visible: config.Visible == '1',
                    style: ol3.layer.StyleCallback(config.DefaultStyleID, config.SourceType, 'style'),
                    hoverStyle: ol3.layer.StyleCallback(config.HoverStyleID, config.SourceType, 'hover'),
                    selectStyle: ol3.layer.StyleCallback(config.SelectStyleID, config.SourceType, 'select')
                });
            }
        },
        StyleCallback: function(styleId, _type, _styleType) {
            return function(feature) {
                var size;

                // if this callback is executed as ol.FeatureStyleFunction() instaed of ol.FStyleFunction()
                // the feature param contains the resolution not a feature, use this instead
                // @see https://github.com/openlayers/ol3/issues/5902
                // @see http://openlayers.org/en/v3.19.1/apidoc/ol.html#.StyleFunction
                // @see http://openlayers.org/en/v3.19.1/apidoc/ol.html#.FeatureStyleFunction

                feature = feature instanceof ol.Feature ? feature : this;

                if (feature instanceof ol.Feature && (features = feature.get('features'))) {
                    size = feature.get('features').length.toString();
                }

                return ol3.style.get(styleId, size);
            };
        },
        hoverStyleFeature: function(feature, reverse) {

            var marker = feature.marker || feature
                style = reverse === false ? marker.get('currentStyle') : feature.layer.get('hoverStyle');

            marker.setStyle(style);
        },
        selectStyleFeature: function(feature, reverse) {

            var marker = feature.marker || feature
                style = reverse === false ? undefined : feature.layer.get('selectStyle');

            marker.set('currentStyle', style)
            marker.setStyle(style);
        },
        selectedFeatures: [],
        selectFeatures: function(features) {

            // unselect previously selected features
            for (var i = 0; i < ol3.layer.selectedFeatures.length; i++) {
                feature = ol3.layer.selectedFeatures[i];
                ol3.layer.selectStyleFeature(feature, false);
            }

            // select new features
            for (var i = 0; i < features.length; i++) {
                feature = features[i];
                ol3.layer.selectStyleFeature(feature);
            }

            ol3.layer.selectedFeatures = features;
        }
    };

    ol3.source = {
        create: {
            WMS: function(config) {

                var params = {
                    url: config.SourceUrl,
                    params: { 'LAYERS': config.SourceLayers }
                };

                if (config.SourceProjection) {
                    params.projection = config.SourceProjection;
                }
                return new ol.source.TileWMS(params);

            },
            OSM: function(config) {
                return new ol.source.OSM();
            },
            Vector: function(config) {
                var vectorSource = new ol.source.Vector({
                    loader: function(extent, resolution, projection) {

                        var featureRequest = new ol.format.WFS().writeGetFeature({
                            srsName: config.SourceProjection,
                            featureNS: 'http://www.opengis.net/gml',
                            featureTypes: config.SourceFeatureTypes.split(','),
                            outputFormat: 'text/xml; subtype=gml/3.1.1'
                        });

                        // then post the request and add the received features to a layer
                        fetch(config.SourceUrl, {
                            method: 'POST',
                            body: new XMLSerializer().serializeToString(featureRequest)
                        }).then(function(response) {
                            return response.text();
                        }).then(function(wfs) {
                            var features = new ol.format.WFS().readFeatures(
                                wfs, {
                                    dataProjection: config.SourceProjection,
                                    featureProjection: ol3.config.view.Projection
                                }
                            );
                            vectorSource.addFeatures(features);
                        });
                    },
                    projection: ol3.config.view.Projection
                });
                return vectorSource;
            },
            Group: function(config) {
                return new ol.source.Cluster({
                    distance: 40,
                    source: ol3.source.create.Vector(config)
                });
            }
        }
    }
});

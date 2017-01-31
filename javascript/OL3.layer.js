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

                var factoryName = config.Source.ClassName,
                    factory = ol3.source.create[factoryName],
                    source = factory(config.Source);

                return new ol.layer.Tile({
                    source: source,
                    opacity: parseFloat(config.Opacity),
                    visible: config.Visible == '1'
                });
            },
            OL3VectorLayer: function(config) {

                var factoryName = config.Source.ClassName,
                    factory = ol3.source.create[factoryName],
                    source = factory(config.Source),
                    style, hoverStyle, selectStyle;

                return new ol.layer.Vector({
                    title: config.Title,
                    source: source,
                    opacity: parseFloat(config.Opacity),
                    visible: config.Visible == '1',
                    style: ol3.layer.StyleCallback(config.DefaultStyleID, config.SourceType, 'style'),
                    hoverStyle: ol3.layer.StyleCallback(config.HoverStyleID, config.SourceType, 'hover'),
                    selectStyle: ol3.layer.StyleCallback(config.SelectStyleID, config.SourceType, 'select')
                });
            },
            OL3ImageLayer: function(config) {

                var factoryName = config.Source.ClassName,
                    factory = ol3.source.create[factoryName],
                    source = factory(config.Source);

                return new ol.layer.Image({
                    source: source,
                    opacity: parseFloat(config.Opacity),
                    visible: config.Visible == '1'
                });
            }
        },
        getFeature: function(options) {

            var sourceProjection = options.config.Projection,
                outputProjection = options.config.outputProjection || ol3.config.view.Projection,
                featureRequest,
                readOptions = {},
                getOptions = {
                    featureNS: 'http://www.opengis.net/gml',
                    featureTypes: options.featureTypes || options.config.featureTypes,
                    outputFormat: 'text/xml; subtype=gml/3.1.1',
                    filter: options.filter || null
                };

            if (sourceProjection) {
                getOptions.srsName = sourceProjection;
                readOptions.dataProjection = sourceProjection;
            }

            if (outputProjection) {
                readOptions.featureProjection = outputProjection;
            }

            featureRequest = new ol.format.WFS().writeGetFeature(getOptions);

            // console.log(sourceConfig.Url, new XMLSerializer().serializeToString(featureRequest));

            // then post the request and add the received features to a layer
            fetch(options.config.Url, {
                method: 'POST',
                body: new XMLSerializer().serializeToString(featureRequest)
            }).then(function(response) {
                // console.log(response.text());
                return response.text();
            }).then(function(wfs) {
                // console.log(wfs);
                var features = new ol.format.WFS().readFeatures(
                    wfs, readOptions
                );
                options.callback(features);
            });
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
            OL3TileWMSSource: function(config) {

                var params = {
                    url: config.Url,
                    params: { 'LAYERS': config.Layers }
                };

                if (config.Projection) {
                    params.projection = config.Projection;
                }

                return new ol.source.TileWMS(params);

            },
            OL3OSMSource: function(config) {
                return new ol.source.OSM();
            },
            OL3BingMapsSource: function(config) {
                return new ol.source.BingMaps({
                    key: config.Key,
                    imagerySet: config.ImagerySet
                });
            },
            OL3ImageStaticSource: function(config) {

                var extent = [ parseFloat(config.Lon), parseFloat(config.Lat), parseFloat(config.Lon), parseFloat(config.Lat) ];

                if (config.Projection) extent = ol.proj.transformExtent(extent, config.Projection, ol3.config.view.Projection);

                extent[0] -= parseFloat(config.Scale);
                extent[1] -= parseFloat(config.Scale);
                extent[2] += parseFloat(config.Scale);
                extent[3] += parseFloat(config.Scale);

                return new ol.source.ImageStatic({
                    url: config.Url,
                    imageExtent: extent,
                    imageSize: [1384, 849],
                    projection: ol3.config.view.Projection
                });
            },
            OL3ClusterSource: function(config) {
                return new ol.source.Cluster({
                    distance: config.Distance,
                    source: ol3.source.create.OL3VectorSource(config)
                });
            },
            OL3VectorSource: function(config) {

                var optionFactoryName = config.Format,
                    optionFactory = ol3.source.OL3VectorSourceOptions[optionFactoryName],
                    options = optionFactory(config),
                    source = new ol.source.Vector(options);

                // set a backreference to the source for ajax loader callbacks
                config.source = source;

                return source;
            }
        },
        OL3VectorSourceOptions: {
            GML: function(config){
                return {

                    loader: function(extent, resolution, projection) {
                        ol3.layer.getFeature({
                            config: config,
                            callback: function(features){
                                config.source.addFeatures(features);
                            },
                            filter: config.Filter || null,
                            featureTypes: config.FeatureTypes.split(','),
                            outputProjection: projection.getCode()
                        });

                    },
                    projection: ol3.config.view.Projection
                }
            },
            GeoJSON: function(config){
                return {
                    url: function(extent,resolution,projection) {
                        var replacements = {
                            extent: extent.join(','),
                            resolution: resolution,
                            projection: projection.getCode()
                        };

                        return config.Url.replace(/\$([a-z0-9]+)/gi, function(match, key){
                            return replacements[key];
                        });
                    },
                    format: new ol.format.GeoJSON({ defaultDataProjection: config.Projection || 'EPSG:4326' }),
                    strategy: ol.loadingstrategy.bbox
                };
            }
        }
    }
});

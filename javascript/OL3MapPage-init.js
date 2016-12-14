(function($) {

    var target = $('#map');
    var setup = {
        view: target.data('view'),
        layers: target.data('layers'),
        styles: target.data('styles')
    }

    var styleCache = {};
    var layers = [];

    var createLayer = function(config) {
        console.log(config);
        var layer;
        var styleCache = {};

        switch (config.ClassName) {

            case 'OL3TileLayer':

                var source = new ol.source.OSM();
                layer = new ol.layer.Tile({
                    opacity: parseFloat(config.Opacity),
                    visible: config.Visible == '1'
                });

                switch (config.SourceType) {

                    case 'WMS':

                        var params = {
                            url: config.SourceUrl,
                            params: { 'LAYERS': config.SourceLayers }
                        };

                        if (config.SourceProjection) {
                            params.projection = config.SourceProjection;
                        }

                        source = new ol.source.TileWMS(params);
                        break;

                    default:
                        source = new ol.source.OSM();
                }

                layer.setSource(source);
                break;

            case 'OL3VectorLayer':

                if (config.SourceType == 'point') {

                    var source = createVectorSource(config);

                    layer = new ol.layer.Vector({
                        opacity: parseFloat(config.Opacity),
                        visible: config.Visible == '1',
                        source: new ol.source.Cluster({
                            distance: 40,
                            source: source
                        }),
                        style: function(feature) {
                            var size = feature.get('features').length;
                            var style = styleCache[size];

                            if (!style) {
                                style = new ol.style.Style({
                                    image: new ol.style.Circle({
                                        radius: 10,
                                        stroke: new ol.style.Stroke({
                                            color: '#fff'
                                        }),
                                        fill: new ol.style.Fill({
                                            color: '#3399CC'
                                        })
                                    }),
                                    text: new ol.style.Text({
                                        text: size.toString(),
                                        fill: new ol.style.Fill({
                                            color: '#fff'
                                        })
                                    })
                                });
                                styleCache[size] = style;
                            }
                            return style;
                        }
                    });

                } else {

                    var source = createVectorSource(config);

                    layer = new ol.layer.Vector({
                        source: source,
                        style: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: 'rgba(180, 50, 50, 1.0)',
                                width: 2
                            })
                        }),
                        highlightStyle: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: 'rgba(255, 0, 0, 1.0)',
                                width: 3
                            })
                        }),
                        selectedStyle: new ol.style.Style({
                            stroke: new ol.style.Stroke({
                                color: 'rgba(50, 180, 50, 1.0)',
                                width: 3
                            })
                        })
                    });

                }

                break;
        }

        return layer;
    }

    var createVectorSource = function(config) {
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
                            featureProjection: setup.view.Projection
                        }
                    );
                    vectorSource.addFeatures(features);
                });
            },
            projection: setup.view.Projection
        });
        return vectorSource;
    };

    for (var i = 0; i < setup.layers.length; i++) {
        var config = setup.layers[i];
        var layer = createLayer(config);
        layers.push(layer);
    }

    // // background layer 1: topgraphy
    // layers.push(new ol.layer.Tile({
    //     source: new ol.source.OSM()
    // }));
    //
    // // background layer 1: niwa map
    // layers.push(new ol.layer.Tile({
    //     source: new ol.source.TileWMS({
    //         url: 'http://wms.niwa.co.nz/cgi-bin/bnz_demo',
    //         projection: 'EPSG:4326',
    //         params: { 'LAYERS': 'coast_poly' }
    //     })
    // }));

    // // background layer 2: bathymetry
    // layers.push(new ol.layer.Tile({
    //     source: new ol.source.TileWMS({
    //         url: 'http://wms.niwa.co.nz/cgi-bin/cc_context',
    //         projection: 'EPSG:4326',
    //         params: { 'LAYERS': 'contours_z1' }
    //     })
    // }));
    //
    // // background layer 3: NZ EEZ
    // layers.push(new ol.layer.Tile({
    //     source: new ol.source.TileWMS({
    //         url: 'http://wms.niwa.co.nz/cgi-bin/nz_poly',
    //         projection: 'EPSG:4326',
    //         params: { 'LAYERS': 'EEZ' }
    //     })
    // }));

    // // foreground layer 1: WFS Polygons Multibeam
    // var wfsPolygons = new ol.layer.Vector({
    //     source: new ol.source.Vector({
    //         loader: function(extent, resolution, projection) {
    //
    //             var featureRequest = new ol.format.WFS().writeGetFeature({
    //                 srsName: 'EPSG:4326',
    //                 featureNS: 'http://www.opengis.net/gml',
    //                 featureTypes: ['swath_poly'],
    //                 outputFormat: 'text/xml; subtype=gml/3.1.1'
    //             });
    //             // then post the request and add the received features to a layer
    //             fetch('/OL3Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/os2020_swath', {
    //                 method: 'POST',
    //                 body: new XMLSerializer().serializeToString(featureRequest)
    //             }).then(function(response) {
    //                 return response.text();
    //             }).then(function(wfs) {
    //                 var features = new ol.format.WFS().readFeatures(
    //                     wfs, {
    //                         dataProjection: 'EPSG:4326',
    //                         featureProjection: 'EPSG:3857'
    //                     }
    //                 );
    //                 wfsPolygons.getSource().addFeatures(features);
    //             });
    //         },
    //         projection: 'EPSG:3857'
    //     }),
    //
    //     style: new ol.style.Style({
    //         stroke: new ol.style.Stroke({
    //             color: 'rgba(100, 100, 150, 1.0)',
    //             width: 1
    //         })
    //     })
    // });
    //
    // layers.push(wfsPolygons);

    // // foreground layer 2: WFS Lines Seismic
    // var wfsLines = new ol.layer.Vector({
    //     source: new ol.source.Vector({
    //         loader: function(extent, resolution, projection) {
    //
    //             var featureRequest = new ol.format.WFS().writeGetFeature({
    //                 srsName: 'EPSG:4326',
    //                 featureNS: 'http://www.opengis.net/gml',
    //                 featureTypes: ['niwa_seismic'],
    //                 outputFormat: 'text/xml; subtype=gml/3.1.1'
    //             });
    //             // then post the request and add the received features to a layer
    //             fetch('/OL3Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/seismic_bw', {
    //                 method: 'POST',
    //                 body: new XMLSerializer().serializeToString(featureRequest)
    //             }).then(function(response) {
    //                 return response.text();
    //             }).then(function(wfs) {
    //                 var features = new ol.format.WFS().readFeatures(
    //                     wfs, {
    //                         dataProjection: 'EPSG:4326',
    //                         featureProjection: 'EPSG:3857'
    //                     }
    //                 );
    //                 wfsLines.getSource().addFeatures(features);
    //             });
    //         },
    //         projection: 'EPSG:3857'
    //     }),
    //
    //     style: new ol.style.Style({
    //         stroke: new ol.style.Stroke({
    //             color: 'rgba(180, 50, 50, 1.0)',
    //             width: 2
    //         })
    //     })
    // });
    //
    // layers.push(wfsLines);




    // // foreground layer 3: WFS Point DTIS
    // var styleCache = {};
    // var wfsPoints = new ol.layer.Vector({
    //     source: new ol.source.Cluster({
    //         distance: 40,
    //         source: new ol.source.Vector({
    //             loader: function(extent, resolution, projection) {
    //
    //                 var featureRequest = new ol.format.WFS().writeGetFeature({
    //                     srsName: 'EPSG:4326',
    //                     featureNS: 'http://www.opengis.net/gml',
    //                     featureTypes: ['DTIS'],
    //                     outputFormat: 'text/xml; subtype=gml/3.1.1'
    //                 });
    //                 // then post the request and add the received features to a layer
    //                 fetch('/OL3Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/stations', {
    //                     method: 'POST',
    //                     body: new XMLSerializer().serializeToString(featureRequest)
    //                 }).then(function(response) {
    //                     return response.text();
    //                 }).then(function(wfs) {
    //                     var features = new ol.format.WFS().readFeatures(
    //                         wfs, {
    //                             dataProjection: 'EPSG:4326',
    //                             featureProjection: 'EPSG:3857'
    //                         }
    //                     );
    //                     wfsPoints.getSource().getSource().addFeatures(features);
    //                 });
    //             },
    //             projection: 'EPSG:3857'
    //         })
    //     }),
    //
    //     style: function(feature) {
    //         var size = feature.get('features').length;
    //         var style = styleCache[size];
    //         if (!style) {
    //             style = new ol.style.Style({
    //                 image: new ol.style.Circle({
    //                     radius: 10,
    //                     stroke: new ol.style.Stroke({
    //                         color: '#fff'
    //                     }),
    //                     fill: new ol.style.Fill({
    //                         color: '#3399CC'
    //                     })
    //                 }),
    //                 text: new ol.style.Text({
    //                     text: size.toString(),
    //                     fill: new ol.style.Fill({
    //                         color: '#fff'
    //                     })
    //                 })
    //             });
    //             styleCache[size] = style;
    //          }
    //          return style;
    //     }
    // });
    //
    // layers.push(wfsPoints);




    var interactions = ol.interaction.defaults();
    interactions.push(new ol.interaction.Select({ layers: [  ] }));

    var map = new ol.Map({
        target: target.get(0),
        layers: layers,
        controls: [
            new ol.control.Zoom(),
            new ol.control.ZoomSlider()
        ],
        interactions: interactions,
        view: new ol.View({
            projection: setup.view.Projection,
            center: ol.proj.fromLonLat([parseFloat(setup.view.Lon), parseFloat(setup.view.Lat)]),
            zoom: setup.view.Zoom
        })
    });

    var highlight;
    var displayFeatureInfo = function(pixel) {

        var feature = map.forEachFeatureAtPixel(pixel, function(feature, layer) {
            return feature;
        });

        if (feature !== highlight) {
            if (highlight) {
                highlight.setStyle(null);
            }
            if (feature) {
                // console.log(feature.getProperties());
                console.log(layer.getProperties().highlightStyle);
                feature.setStyle(layer.getProperties().highlightStyle);
            }
            highlight = feature;
        }
    };

    $(map.getViewport()).on('mousemove', function(evt) {
        var pixel = map.getEventPixel(evt.originalEvent);
        displayFeatureInfo(pixel);
    });

    // map.on('click', function(evt) {
    //     displayFeatureInfo(evt.pixel);
    // });

}(jQuery));

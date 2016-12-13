(function($) {

    var projection1 = 'EPSG:3857';
    var projection2 = 'EPSG:4326';
    var projection = projection1;
    var projectionO = projection1 == projection ? projection2 : projection1;


    var target = $('#map');
    var setup = {
        view: target.data('view'),
        layers: target.data('layers')
    }

    var layers = [];

    // background layer 1: topgraphy
    layers.push(new ol.layer.Tile({
        source: new ol.source.OSM()
    }));

    // background layer 1: niwa map
    layers.push(new ol.layer.Tile({
        source: new ol.source.TileWMS({
            url: 'http://wms.niwa.co.nz/cgi-bin/bnz_demo',
            projection: projection2,
            params: { 'LAYERS': 'coast_poly' }
        })
    }));

    // background layer 2: bathymetry
    layers.push(new ol.layer.Tile({
        source: new ol.source.TileWMS({
            url: 'http://wms.niwa.co.nz/cgi-bin/cc_context',
            projection: 'EPSG:4326',
            params: { 'LAYERS': 'contours_z1' }
        })
    }));

    // background layer 3: NZ EEZ
    layers.push(new ol.layer.Tile({
        source: new ol.source.TileWMS({
            url: 'http://wms.niwa.co.nz/cgi-bin/nz_poly',
            projection: 'EPSG:4326',
            params: { 'LAYERS': 'EEZ' }
        })
    }));

    // foreground layer 1: WFS Polygons Multibeam
    var wfsPolygons = new ol.layer.Vector({
        source: new ol.source.Vector({
            loader: function(extent, resolution, projection) {

                var featureRequest = new ol.format.WFS().writeGetFeature({
                    srsName: projection2,
                    featureNS: 'http://www.opengis.net/gml',
                    featureTypes: ['swath_poly'],
                    outputFormat: 'text/xml; subtype=gml/3.1.1'
                });
                // then post the request and add the received features to a layer
                fetch('/OL3Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/os2020_swath', {
                    method: 'POST',
                    body: new XMLSerializer().serializeToString(featureRequest)
                }).then(function(response) {
                    return response.text();
                }).then(function(wfs) {
                    var features = new ol.format.WFS().readFeatures(
                        wfs, {
                            dataProjection: projection2,
                            featureProjection: projection1
                        }
                    );
                    wfsPolygons.getSource().addFeatures(features);
                });
            },
            projection: projection
        }),

        style: new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'rgba(100, 100, 150, 1.0)',
                width: 1
            })
        })
    });

layers.push(wfsPolygons);

// foreground layer 2: WFS Lines Seismic
var wfsLines = new ol.layer.Vector({
    source: new ol.source.Vector({
        loader: function(extent, resolution, projection) {

            var featureRequest = new ol.format.WFS().writeGetFeature({
                srsName: projection2,
                featureNS: 'http://www.opengis.net/gml',
                featureTypes: ['niwa_seismic'],
                outputFormat: 'text/xml; subtype=gml/3.1.1'
            });
            // then post the request and add the received features to a layer
            fetch('/OL3Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/seismic_bw', {
                method: 'POST',
                body: new XMLSerializer().serializeToString(featureRequest)
            }).then(function(response) {
                return response.text();
            }).then(function(wfs) {
                var features = new ol.format.WFS().readFeatures(
                    wfs, {
                        dataProjection: projection2,
                        featureProjection: projection1
                    }
                );
                wfsLines.getSource().addFeatures(features);
            });
        },
        projection: projection
    }),

    style: new ol.style.Style({
        stroke: new ol.style.Stroke({
            color: 'rgba(150, 100, 100, 1.0)',
            width: 1
        })
    })
});

layers.push(wfsLines);




// foreground layer 3: WFS Point Seismic
var styleCache = {};
var wfsPoints = new ol.layer.Vector({
    source: new ol.source.Cluster({
        distance: 40,
        source: new ol.source.Vector({
            loader: function(extent, resolution, projection) {

                var featureRequest = new ol.format.WFS().writeGetFeature({
                    srsName: projection2,
                    featureNS: 'http://www.opengis.net/gml',
                    featureTypes: ['DTIS'],
                    outputFormat: 'text/xml; subtype=gml/3.1.1'
                });
                // then post the request and add the received features to a layer
                fetch('/OL3Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/stations', {
                    method: 'POST',
                    body: new XMLSerializer().serializeToString(featureRequest)
                }).then(function(response) {
                    return response.text();
                }).then(function(wfs) {
                    var features = new ol.format.WFS().readFeatures(
                        wfs, {
                            dataProjection: projection2,
                            featureProjection: projection1
                        }
                    );
                    wfsPoints.getSource().getSource().addFeatures(features);
                });
            },
            projection: projection
        })
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

layers.push(wfsPoints);





    var map = new ol.Map({
        target: target.get(0),
        layers: layers,
        view: new ol.View({
            projection: projection,
            center: ol.proj.fromLonLat([parseFloat(setup.view.Lon), parseFloat(setup.view.Lat)]),
            zoom: setup.view.Zoom
        })
    });

}(jQuery));

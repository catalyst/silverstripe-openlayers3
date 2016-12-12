(function($) {

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
            projection: 'EPSG:4326',
            params: { 'LAYERS': 'coast_poly' }
        })
    }));

    // // background layer 2: bathymetry
    // layers.push(new ol.layer.Tile({
    //     source: new ol.source.TileWMS({
    //         url: 'http://wms.niwa.co.nz/cgi-bin/cc_context',
    //         projection: 'EPSG:4326',
    //         params: { 'LAYERS': 'contours_z1' }
    //     })
    // }));

    // // background layer 3: NZ EEZ
    // layers.push(new ol.layer.Tile({
    //     source: new ol.source.TileWMS({
    //         url: 'http://wms.niwa.co.nz/cgi-bin/nz_poly',
    //         projection: 'EPSG:4326',
    //         params: { 'LAYERS': 'EEZ' }
    //     })
    // }));

    var layerWFS = new ol.layer.Vector({
        source: new ol.source.Vector({
            loader: function(extent) {
                $.ajax('http://demo.opengeo.org/geoserver/wfs', {
                    type: 'GET',
                    data: {
                        service: 'WFS',
                        version: '1.1.0',
                        request: 'GetFeature',
                        typename: 'water_areas',
                        srsname: 'EPSG:3857',
                        bbox: extent.join(',') + ',EPSG:3857'
                    }
                }).done(function(response) {
                    layerWFS
                    .getSource()
                    .addFeatures(new ol.format.WFS()
                    .readFeatures(response));
                });
            },
            strategy:  ol.loadingstrategy.bbox,
            projection: 'EPSG:3857'
            // url: 'http://wms.niwa.co.nz/cgi-bin/os2020_swath'
            // url: '/Proxy/dorequest?u=http://wms.niwa.co.nz/cgi-bin/os2020_swath'
        }),

        style: new ol.style.Style({
            stroke: new ol.style.Stroke({
                color: 'rgba(0, 0, 255, 1.0)',
                width: 2
            })
        })
    });

layers.push(layerWFS);

    var map = new ol.Map({
        target: target.get(0),
        layers: layers,
        view: new ol.View({
            // center: ol.proj.fromLonLat([parseFloat(setup.view.Lon), parseFloat(setup.view.Lat)]),
            // zoom: setup.view.Zoom
            center: [-8908887.277395891, 5381918.072437216],
            zoom: 14
        })
    });

}(jQuery));

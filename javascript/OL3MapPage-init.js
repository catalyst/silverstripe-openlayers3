(function($) {

    var target = $('#map');
    var setup = {
        view: target.data('view'),
        layers: target.data('layers')
    }

    // background layer 1: topgraphy
    var layers = [new ol.layer.Tile({source: new ol.source.OSM()})];

    // background layer 2: bathymetry
    layers.push(new ol.layer.Tile({
        source: new ol.source.TileWMS({
            url: 'http://wms.niwa.co.nz/cgi-bin/cc_context',
            params: {
               'LAYERS': 'contours_z1',
               'TRANSPARENT': 'true'
            }
        })
    }));

    // background layer 3: NZ EEZ
    layers.push(new ol.layer.Tile({
        source: new ol.source.TileWMS({
            url: 'http://wms.niwa.co.nz/cgi-bin/nz_poly',
            projection: 'EPSG:4326',
            params: {
               'LAYERS': 'EEZ',
               'VERSION': '1.1.1',
               'TRANSPARENT': 'true'
            }
        })
    }));

    var map = new ol.Map({
        target: target.get(0),
        layers: layers,
        view: new ol.View({
            center: ol.proj.fromLonLat([parseFloat(setup.view.Lon), parseFloat(setup.view.Lat)]),
            zoom: setup.view.Zoom
        })
    });

}(jQuery));

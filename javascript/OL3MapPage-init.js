(function($) {

    var target = $('#map');
    var setup = {
        view: target.data('view'),
        layers: target.data('layers')
    }
    var layers = [new ol.layer.Tile({source: new ol.source.OSM()})];

    layers.push(new ol.layer.Tile({
        source: new ol.source.TileWMS({
            // url: 'http://gis.niwa.co.nz/arcgis/services/OS2020/BoI_Bathymetry_MS/MapServer/WMSServer',
            // params: { 'LAYERS': 'contours_z1', 'VERSION': '1.1.1' }

            // url: 'http://www.idee.es/wms/MTN-Raster/MTN-Raster',
            // params: {
            //    'LAYERS': 'mtn_rasterizado',
            //    'TRANSPARENT': 'true'
            // }

            url: 'http://wms.niwa.co.nz/cgi-bin/cc_context',
            params: {
               'LAYERS': 'contours_z1',
               'TRANSPARENT': 'true',
               'VERSION': '1.1.1'
            }
        })
    }));

console.log(layers);

// http://wms.niwa.co.nz/cgi-bin/cc_context?
// URL_PARAMS=%5Bobject%20Object%5D&
// SSID=68
// LAYERS=contours_z1
// TRANSPARENT=true
// FORMAT=png
// SERVICE=WMS
// VERSION=1.1.1
// REQUEST=GetMap
// STYLES=
// SRS=EPSG%3A4326
// BBOX=170.93552124024,-42.954577026367,178.80447875976,-40.165422973633
// WIDTH=2865
// HEIGHT=1015

    var map = new ol.Map({
        target: target.get(0),
        layers: layers,
        view: new ol.View({
            center: ol.proj.fromLonLat([parseFloat(setup.view.Lon), parseFloat(setup.view.Lat)]),
            zoom: setup.view.Zoom
        })
    });

}(jQuery));

OL3.extend(function(){
    var ol3 = this;

    ol3.layer = {
        init: function() {
            for (var i = 0; i < ol3.config.layers.length; i++) {

                var layer = ol3.config.layers[i],
                    factoryName = layer.ClassName,
                    factory = this.create[factoryName],
                    map = ol3.cache.map;

                if (factoryName == 'OL3TileLayer') map.addLayer(factory(layer));
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
                // return ol3.layer.create.OL3TileLayer(config);
            }
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
            }
        }
    }
});

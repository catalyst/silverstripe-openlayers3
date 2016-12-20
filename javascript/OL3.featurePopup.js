// @requires OL3.interaction.js

OL3.extend(function(){

    var ol3 = this;

    ol3.featurePopup = {
        init: function(config) {

            var map = ol3.cache.map,
                config = config || {},
                element = config.element || new ol3.html('<div>').get(),
                close = new ol3.html('<a>').attr('class', 'close').append('"❌"');

            new ol3.html(map.getTarget())
                .append(
                    new ol3.html(element)
                        .attr('id', 'popup')
                        .append(close)
                );

            close.on('click', function(){
                new ol3.html(element).css('display', 'none');
            });

            map.addOverlay(new ol.Overlay({ element: element, id: 'popup', autoPan: true }));

            map.getLayers().forEach(function(layer){

                layer.addEventListener('singleclick', function(e){

                    var feature = e.detail.feature,
                        features = feature.get('features') || [],
                        properties,
                        list;

                    switch (features.length) {
                        case 1:

                            feature = features[0];

                        case 0:

                            properties = feature.getProperties();
                            list = new ol3.html('<table>');

                            for (var i in properties) {
                                var property = properties[i];

                                if (typeof property === 'string') {
                                    list.append(
                                        new ol3.html('<tr>')
                                            .append(
                                                new ol3.html('<td>').append('"' + i + '"')
                                            )
                                            .append(
                                                new ol3.html('<td>').append('"' + property + '"')
                                            )
                                    );
                                }
                            }

                            break;
                        default:

                            list = new ol3.html('<ul>');
                            for (var i = 0; i < features.length; i++) {
                                list.append(new ol3.html('<li>').append('"' + features[i].get('id') + '"'));
                            }

                    }

                    $('#popup table,#popup ul').remove();
                    new ol3.html(element)
                        .append(list)
                        .css('display', 'block');

                    map.getOverlayById('popup').setPosition(map.getCoordinateFromPixel([e.detail.pixel[0], e.detail.pixel[1]]));

                });

            });
        }
    };

});

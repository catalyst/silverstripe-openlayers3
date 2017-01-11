// @requires OL3.interaction.js

OL3.extend(function(){

    var ol3 = this;

    ol3.featurePopup = {
        element: undefined,
        init: function(config) {

            var map = ol3.cache.map,
                config = config || {},
                close = new ol3.html('<a>').attr('class', 'close').append('"❌"');

            ol3.featurePopup.element = config.element || new ol3.html('<div>').get();

            new ol3.html(ol3.featurePopup.element).on('mousemove', function(evt){
                evt.stopPropagation();
            });

            new ol3.html(map.getTarget())
                .append(
                    new ol3.html(ol3.featurePopup.element)
                        .attr('id', 'popup')
                        .append(close)
                );

            close.on('click', function() { ol3.featurePopup.close(); });

            map.addOverlay(new ol.Overlay({ element: ol3.featurePopup.element, id: 'popup', autoPan: true }));

            map.on('click', function(evt){

                var features = ol3.featurePopup.getFeaturesAtPixel(evt.pixel);

                if (features.length == 1) {
                    ol3.featurePopup.popupFeature(features[0], evt.pixel);
                } else if (features.length > 1) {
                    ol3.featurePopup.listFeatures(features, evt.pixel);
                } else {
                    ol3.featurePopup.close();
                }

            });

        },
        close: function() {
            new ol3.html(ol3.featurePopup.element).css('display', 'none');
            ol3.layer.selectFeatures([]);
        },
        getFeaturesAtPixel: function(pixel) {

            var map = ol3.cache.map,
                features = [];

            map.forEachFeatureAtPixel(pixel, function(feature, layer){
                if (groupFeatures = feature.get('features')) {
                    groupFeatures.forEach(function(featureDetail) {
                        featureDetail.marker = feature;
                        featureDetail.layer = layer;
                        features.push(featureDetail);
                    });
                } else {
                    feature.layer = layer;
                    features.push(feature);
                }
            });

            return features;
        },
        popup: function (content, pixel) {
            $('#popup table,#popup ul').remove();
            new ol3.html(ol3.featurePopup.element)
                .append(content)
                .css('display', 'block');

            map.getOverlayById('popup').setPosition(map.getCoordinateFromPixel([pixel[0], pixel[1]]));
        },
        renderFeature: function(feature) {

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
            return list;
        },
        popupFeature: function(feature, pixel) {

            list = ol3.featurePopup.renderFeature(feature);

            ol3.layer.selectFeatures([feature]);
            ol3.featurePopup.popup(list, pixel);
        },
        listFeatures: function(features, pixel) {

            var list = new ol3.html('<ul>');

            for (var i = 0; i < features.length; i++) {
                var item = new ol3.html('<li>')
                    .data('feature', features[i])
                    .on('click', function(){ ol3.featurePopup.popupFeature(new ol3.html(this).data('feature'), pixel); })
                    .on('mousemove', function(){ ol3.layer.hoverStyleFeature(new ol3.html(this).data('feature')); })
                    .on('mouseout', function(){ ol3.layer.hoverStyleFeature(new ol3.html(this).data('feature'), false); })
                    .append('"' + features[i].layer.get('Title') + ': ' + features[i].get('id') + '"');
                list.append(item);
            }

            ol3.layer.selectFeatures(features);
            ol3.featurePopup.popup(list, pixel);
        }
    };

});

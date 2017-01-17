// @requires OL3.interaction.js

OL3.extend(function(){

    var ol3 = this;

    ol3.featurePopup = {
        element: undefined,
        init: function(config) {

            var map = ol3.cache.map,
                config = config || {},
                close = H('<a>').attr('class', 'close').append('"❌"');

            ol3.featurePopup.element = config.element || H('<div>').get();

            H(ol3.featurePopup.element).on('mousemove', function(evt){
                evt.stopPropagation();
            });

            H(map.getTarget())
                .append(
                    H(ol3.featurePopup.element)
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
            H(ol3.featurePopup.element).css('display', 'none');
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
            H(ol3.featurePopup.element)
                .append(content)
                .css('display', 'block');

            map.getOverlayById('popup').setPosition(map.getCoordinateFromPixel([pixel[0], pixel[1]]));
        },
        renderFeature: function(feature) {

            properties = feature.getProperties();
            list = H('<table>');

            for (var i in properties) {
                var property = properties[i];

                if (typeof property === 'string') {
                    list.append(
                        H('<tr>')
                            .append(
                                H('<td>').attr('class', 'key').append('"' + i + ':"')
                            )
                            .append(
                                H('<td>').attr('class', 'data').append('"' + property + '"')
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
        popupFeatureTitle: function (feature){
            var properties = feature.getProperties(),
                popupFeatureTitle = feature.layer.config.PopupFeatureTitle;

            return popupFeatureTitle ? popupFeatureTitle.replace(/\$([a-z0-9]+)/gi, function(match, key){ return properties[key]; }) : feature.layer.config.Title + ' (' + properties.id + ')';
        },
        listFeatures: function(features, pixel) {

            var list = H('<ul>');

            list.append(H('<li>').append('"' + features.length + ' items selected:"').attr('class', 'header'));

            for (var i = 0; i < features.length; i++) {
                var title = ol3.featurePopup.popupFeatureTitle(features[i]), // features[i].layer.get('Title') + ': ' + features[i].get('id')
                    item = H('<li>')
                        .data('feature', features[i])
                        .on('click', function(){ ol3.featurePopup.popupFeature(H(this).data('feature'), pixel); })
                        .on('mousemove', function(){ ol3.layer.hoverStyleFeature(H(this).data('feature')); })
                        .on('mouseout', function(){ ol3.layer.hoverStyleFeature(H(this).data('feature'), false); })
                        .append('"' + title + '"');
                list.append(item);
            }

            ol3.layer.selectFeatures(features);
            ol3.featurePopup.popup(list, pixel);
        }
    };

});

// @requires OL3.interaction.js

OL3.extend(function(){

    var ol3 = this;

    ol3.featurePopup = {
        element: undefined,
        init: function(config) {

            var map = ol3.cache.map,
                config = config || {},
                close = H('<a>').addClass('close').text('❌');

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

                var features = ol3.featurePopup.getFeaturesAtPixel(evt.pixel)
                    coordinate = map.getCoordinateFromPixel([evt.pixel[0], evt.pixel[1]]);

                if (features.length == 1) {
                    ol3.featurePopup.popupFeature(features[0], coordinate);
                } else if (features.length > 1) {
                    ol3.featurePopup.listFeatures(features, coordinate);
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

                // if (typeof feature.layer !== 'undefined') return;

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
        popup: function (content, coordinate) {
            $('#popup table,#popup ul').remove();
            H(ol3.featurePopup.element)
                .append(content)
                .css('display', 'block');

            map.getOverlayById('popup').setPosition(coordinate);
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
                                H('<td>').addClass('key').text(i + ':')
                            )
                            .append(
                                H('<td>').addClass('data').text(property)
                            )
                    );
                }
            }
            return list;
        },
        popupFeature: function(feature, coordinate) {

            list = ol3.featurePopup.renderFeature(feature);

            ol3.layer.selectFeatures([feature]);
            ol3.featurePopup.popup(list, coordinate);
        },
        popupFeatureTitle: function (feature){
            var properties = feature.getProperties(),
                popupFeatureTitle = feature.layer.config.PopupFeatureTitle;

            return popupFeatureTitle ? popupFeatureTitle.replace(/\$([a-z0-9]+)/gi, function(match, key){ return properties[key]; }) : feature.layer.config.Title + ' (' + properties.id + ')';
        },
        listFeatures: function(features, coordinate) {

            var list = H('<ul>');

            list.append(H('<li>').text(features.length + ' items selected:').addClass('header'));

            for (var i = 0; i < features.length; i++) {
                var title = ol3.featurePopup.popupFeatureTitle(features[i]), // features[i].layer.get('Title') + ': ' + features[i].get('id')
                    item = H('<li>')
                        .data('feature', features[i])
                        .on('click', function(){ ol3.featurePopup.popupFeature(H(this).data('feature'), coordinate); })
                        .on('mousemove', function(){ ol3.layer.hoverStyleFeature(H(this).data('feature')); })
                        .on('mouseout', function(){ ol3.layer.hoverStyleFeature(H(this).data('feature'), false); })
                        .text(title);
                list.append(item);
            }

            ol3.layer.selectFeatures(features);
            ol3.featurePopup.popup(list, coordinate);
        }
    };

});

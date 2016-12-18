// @requires OL3.base.js

OL3.extend(function(){


    var ol3 = this;

    ol3.interaction = {
        init: function() {

            var map = ol3.cache.map;

            if (!map) {
                console.error('please render map before initialising layers.');
                return;
            }

            map.on(
                [
                    'click',
                    'dblClick',
                    'singleclick',
                    'pointermove'
                ],
                function(evt) {
                    ol3.interaction.refire(evt, evt.pixel);
                }
            );

            // attach click and move listeners to all vector layers
            ol3.cache.map.getLayers().forEach(function(layer){

                if (layer instanceof ol.layer.Vector) {

                    layer.addEventListener('moveInFeature', function(e) {

                        var feature = e.detail.feature
                            layer = e.detail.layer,
                            style = layer.getProperties().hoverStyle;

                        feature.setStyle(style);

                    });

                    layer.addEventListener('moveOutFeature', function(e){

                        var feature = e.detail.feature;
                        feature.setStyle(undefined);

                    });
                }
            });

        },
        previouslyHovered: [],
        refire: function(evt, pixel) {

            // delegate click and move events to layers

            var currentlyHovered = [];

            map.forEachFeatureAtPixel(pixel, function(feature, layer) {

                // mock up moveIn event
                if (evt.type == 'pointermove') {

                    currentlyHovered.push({layer:layer,feature:feature});

                    if (ol3.interaction.previouslyHovered.indexOf({layer:layer,feature:feature}) < 0) {
                        ol3.interaction.trigger('moveInFeature', layer, {
                            feature: feature,
                            layer: layer,
                            pixel: pixel,
                            originalEvent: evt
                        });
                    }
                }

                ol3.interaction.trigger(evt.type, layer, {
                    feature: feature,
                    layer: layer,
                    pixel: pixel,
                    originalEvent: evt
                });

            });

            // mock up moveOut events
            if (evt.type == 'pointermove') {

                if (ol3.interaction.previouslyHovered.length) {
                    // find all features in previouslyHovered that are not currentlyHovered
                    var movedOut = ol3.interaction.previouslyHovered.filter(function(i) { return currentlyHovered.indexOf(i) < 0; });

                    for (var i = 0; i < movedOut.length; i++) {
                        ol3.interaction.trigger('moveOutFeature', movedOut[i].layer, {
                            feature: movedOut[i].feature,
                            layer: movedOut[i].layer,
                            pixel: pixel,
                            originalEvent: evt
                        });
                    }
                }

                ol3.interaction.previouslyHovered = currentlyHovered;
            }

        },
        trigger: function(type, target, payload) {

            var evt = new CustomEvent(type, { detail: payload });

            target.dispatchEvent(evt);

        }
    };
});

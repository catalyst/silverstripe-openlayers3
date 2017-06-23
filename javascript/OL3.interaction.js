// @requires OL3.base.js
// @requires OL3.layer.js

OL3.extend(function(){


    var ol3 = this;

    ol3.interaction = {
        handler: function(evt) {
            ol3.interaction.refire(evt, evt.pixel);
        },
        init: function() {

            this.bind();

            // attach click and move listeners to all vector layers
            ol3.cache.map.getLayers().forEach(function(layer){

                if (layer instanceof ol.layer.Vector) {

                    if (!layer) return;

                    layer.addEventListener('moveInFeature', function(e) {

                        ol3.layer.hoverStyleFeature(e.detail.feature);

                    });

                    layer.addEventListener('moveOutFeature', function(e){

                        ol3.layer.hoverStyleFeature(e.detail.feature, false);

                    });
                }
            });

        },
        bind: function(){
            var map = ol3.cache.map;

            map.on(
                [
                    'click',
                    'dblClick',
                    'singleclick',
                    'pointermove'
                ],
                this.handler
            );
        },
        unbind: function(){
            var map = ol3.cache.map;

            map.un(
                [
                    'click',
                    'dblClick',
                    'singleclick',
                    'pointermove'
                ],
                this.handler
            );
        },
        previouslyHovered: [],
        refire: function(evt, pixel) {

            // delegate click and move events to layers

            var currentlyHovered = [];

            map.forEachFeatureAtPixel(pixel, function(feature, layer) {

                feature.layer = layer;

                // mock up moveIn event
                if (evt.type == 'pointermove') {

                    currentlyHovered.push(feature);

                    if (ol3.interaction.previouslyHovered.indexOf(feature) < 0) {
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
                            feature: movedOut[i],
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

            if (target) target.dispatchEvent(evt);

        }
    };
});

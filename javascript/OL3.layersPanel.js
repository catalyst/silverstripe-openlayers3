// @requires OL3.layer.js

OL3.extend(function(){

    var ol3 = this;

    ol3.layersPanel = {
        init: function(config) {

            ol3.layersPanel.render(config ? config.element : undefined);

        },
        render: function(element) {

            var map = ol3.cache.map,
                element = element || new ol3.html('<div>').get(),
                list = new ol3.html('<ul>'),
                panel;

            new ol3.html(map.getTarget())
                .append(
                    new ol3.html(element)
                        .attr('id', 'panel')
                        .append(list)
                );

            map.addControl(new ol.control.Control({ element: element }));
            map.getLayers().forEach(function(layer){

                var checkbox = new ol3.html('<input>').attr('type', 'checkbox').data('layer', layer),
                    title = '" ' + layer.get('Title') + '"',
                    item = new ol3.html('<li>').append(new ol3.html('<label>').append(checkbox).append(title));

                if (layer.getVisible()) checkbox.attr('checked', true);

                checkbox.on('click', function(){

                    var checkbox = new ol3.html(this),
                        layer = checkbox.data('layer');

                    layer.setVisible(checkbox.attr('checked'));

                });

                list.append(item);

            });
        }
    };

});

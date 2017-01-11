// @requires OL3.layer.js

OL3.extend(function(){

    var ol3 = this;

    ol3.layersPanel = {
        init: function(config) {

            ol3.layersPanel.render(config ? config.element : undefined);

        },
        iconSize: { width: 25, height: 25 },
        iconPolygon: new ol.geom.Polygon([[[8, 3], [22, 1], [15, 12], [24, 24], [4, 24], [1, 13], [8, 3]]]),
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
                    item = new ol3.html('<li>'),
                    itemContent = new ol3.html('<label>').append(checkbox).append(title),
                    icon = ol3.layersPanel.getIconForLayer(layer);

                if (icon) {
                    item
                        .append(new ol3.html(icon)
                        .css({
                            width: ol3.layersPanel.iconSize.width + 'px',
                            height: ol3.layersPanel.iconSize.height + 'px'
                        }));
                }
                item.append(itemContent);

                if (layer.getVisible()) checkbox.attr('checked', 'checked');

                checkbox.on('click', function(){

                    var checkbox = new ol3.html(this),
                        layer = checkbox.data('layer');

                    layer.setVisible(checkbox.prop('checked'));

                });

                list.append(item);

            });
        },
        getIconForLayer: function(layer) {

            if (!(layer instanceof ol.layer.Vector)) return;

            var image,
                style = layer.getStyle(),
                type = layer.config.SourceType,
                geos = {
                    polygon: ol3.layersPanel.iconPolygon,
                    line: new ol.geom.LineString([[0, ol3.layersPanel.iconSize.height], [ol3.layersPanel.iconSize.width, 0]]),
                    point: new ol.geom.Point([ol3.layersPanel.iconSize.width / 2, ol3.layersPanel.iconSize.height / 2])
                };

            if (typeof style == 'function') style = style();

            if (type == 'point') {
                markerimage = style.getImage().getImage();
                if (markerimage instanceof Image) {
                    image = new Image();
                    markerimage.addEventListener('load', function() { console.log(image, this.src); image.src = this.src; });
                }
            }
            if (!(image instanceof Image)) {

                var vectorContext,
                    image = new ol3.html('<canvas>')
                        .attr(ol3.layersPanel.iconSize)
                        .css({ position: 'absolute', 'top': 0, 'left': 0, background: 'white' })
                        .get();

                vectorContext = ol.render.toContext(image.getContext('2d'), {size: [ol3.layersPanel.iconSize.width, ol3.layersPanel.iconSize.height]});

                vectorContext.setStyle(style);
                vectorContext.drawGeometry(geos[type]);
            }

            return image instanceof Image ? image : new ol3.html('<img>').prop('src', image.toDataURL('image/png'));
        }
    };
});

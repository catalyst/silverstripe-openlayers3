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
                element = element || H('<div>').get(),
                list = H('<ul>'),
                panel;

            H(map.getTarget())
                .append(
                    H(element)
                        .attr('id', 'panel')
                        .append(list)
                );

            map.addControl(new ol.control.Control({ element: element }));
            map.getLayers().forEach(function(layer){
                var item = ol3.layersPanel.renderLayerItem(layer);
                list.prepend(item);
            });
        },
        renderLayerItem: function(layer){

            // exempt background from toggling
            if (ol3.config.view.BackgroundID == layer.config.ID) return;

            var checkbox = H('<input>').attr('type', 'checkbox').data('layer', layer),
                title = '" ' + layer.get('Title') + '"',
                item = H('<li>').attr('data-layer-id', layer.config.ID),
                itemContent = H('<label>').append(checkbox).append(H('<span>').append(title)),
                icon = ol3.layersPanel.getIconForLayer(layer);

            if (icon) {
                item
                    .append(H(icon)
                    .css({
                        width: ol3.layersPanel.iconSize.width + 'px',
                        height: ol3.layersPanel.iconSize.height + 'px'
                    }));
            }
            item.append(itemContent);

            if (layer.getVisible()) checkbox.attr('checked', 'checked');

            checkbox.on('click', function(){

                var checkbox = H(this),
                    layer = checkbox.data('layer');

                layer.setVisible(checkbox.prop('checked'));

            });

            return item;

        },
        getIconForLayer: function(layer) {

            if (layer instanceof ol.layer.Tile) return H('<img>').attr('src', 'openlayers3/images/world_map25.png');

            var image,
                style = layer.getStyle(),
                type = layer.config.Source.ClassName,
                geos = {
                    OL3VectorSource: new ol.geom.LineString([[0, ol3.layersPanel.iconSize.height], [ol3.layersPanel.iconSize.width, 0]]), // line
                    OL3VectorSource: ol3.layersPanel.iconPolygon, // polygon
                    OL3ClusterSource: new ol.geom.Point([ol3.layersPanel.iconSize.width / 2, ol3.layersPanel.iconSize.height / 2]) // point
                };

            if (typeof style == 'function') style = style();

            if (type == 'OL3ClusterSource') {
                markerimage = style.getImage().getImage();
                if (markerimage instanceof Image) {
                    image = new Image();
                    markerimage.addEventListener('load', function() { image.src = this.src; });
                }
            }
            if (!(image instanceof Image)) {

                var vectorContext,
                    image = H('<canvas>')
                        .attr(ol3.layersPanel.iconSize)
                        .css({ position: 'absolute', 'top': 0, 'left': 0, background: 'white' })
                        .get();

                vectorContext = ol.render.toContext(image.getContext('2d'), {size: [ol3.layersPanel.iconSize.width, ol3.layersPanel.iconSize.height]});

                vectorContext.setStyle(style);
                vectorContext.drawGeometry(geos[type]);
            }

            return image instanceof Image ? image : H('<img>').prop('src', image.toDataURL('image/png'));
        }
    };
});

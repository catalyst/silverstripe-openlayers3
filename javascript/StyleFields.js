(function($){

	$.entwine('ss', function($) {
		$('.field input.range[type="range"]').entwine({
			onmatch: function(event) {
                var value = this.val();
                var label = $('<div class="range-label" data-unit="' + (this.data('unit') || '') + '">' + value + '</div>');
				this.parent().append(label);
			},
            onchange: function() {
                this.updateLabel();
            },
            oninput: function() {
                this.updateLabel();
            },
            updateLabel: function() {
                var value = this.val();
                var label = $('.range-label', this.parent());
                label.text(value)
            }
        });

        $('.field.color input.color').entwine({
            onmatch: function(event) {
                var value = this.val(),
                    segements = value.match(new RegExp(this.attr('pattern'))) || [0,0,0,0,0],
                    red = segements[1],
                    green = segements[2],
                    blue = segements[3],
                    opacity = segements[4],
                    hex = '#' + ('0' + parseInt(red).toString(16)).substr(-2) + ('0' + parseInt(green).toString(16)).substr(-2) + ('0' + parseInt(blue).toString(16)).substr(-2),
                    colorPicker = $('<input class="color-picker" type="color" value="' + hex + '">'),
                    opacityRange = $('<input class="opacity-range" type="range" value="' + opacity + '" min="0" max="1" step=".1">');
                segements && colorPicker.css('background', value);
                this.parent().append(colorPicker).append(opacityRange);
                this.css({ position:'absolute', left: '-500px' });
            },
            update: function() {
                var colorPicker = $('.color-picker', this.parent()),
                    color = colorPicker.val(),
                    opacity = $('.opacity-range', this.parent()).val(),
                    red = parseInt(color.substr(1, 2), 16),
                    green = parseInt(color.substr(3, 2), 16),
                    blue = parseInt(color.substr(5, 2), 16),
                    rgba = 'rgba(' + red + ', ' + green + ', ' + blue + ', ' + opacity + ')';
                this.val(rgba);
                colorPicker.css('background', rgba);
            }
        });

        $('.field.color input.color-picker').entwine({
            onchange: function(event) {
                $('.color', this.parent()).update();
            },
            oninput: function(event) {
                $('.color', this.parent()).update();
            }
        });

        $('.field.color input.opacity-range').entwine({
            onchange: function(event) {
                $('.color', this.parent()).update();
            },
            oninput: function(event) {
                $('.color', this.parent()).update();
            }
        });
	});
}(jQuery));

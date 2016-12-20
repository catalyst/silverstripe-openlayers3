// @requires OL3.layer.js

OL3.extend(function(){

    var ol3 = this;

    function _html(selector, conatiner, config) {

        var matches;

        this.selector = selector;
        this.elements = [];

        if (selector instanceof _html) {

            for (var i in selector) this[i] = selector[i];

        } else if (
            typeof selector === 'object' &&
            (selector.nodeType === 1 || selector.nodeType === 3)
        ) {

            this.elements = [ selector ];

        } else if (matches = selector.match(/^<([a-z][a-z0-9]*)>$/)) {

            this.elements = [ document.createElement(matches[1]) ];

        } else if(matches = selector.match(/^"([^"]*)"$/)) {

            this.elements = [ document.createTextNode(matches[1]) ];

        } else {

            this.elements = document.querySelectorAll(selector);

        }

        this.length = this.elements.length;

        return this;
    };

    _html.prototype.get = function(i) {
        i = i > 0 ? i : 0;
        return this.elements instanceof NodeList ? this.elements.item(i) : this.elements[i];
    };

    _html.prototype.each = function(callback) {

        for (var i = 0; i < this.elements.length; i++) {
            callback.call(new _html(this.get(i)), i, this);
        }

        return this;
    };

    _html.prototype.append = function(html) {
        html = new ol3.html(html);
        this.each(function(){
            var attachTo = this.get();
            html.each(function(){
                attachTo.appendChild(this.get());
            });
        });

        return this;
    };

    _html.prototype.attr = function(key, val) {

        if (typeof key === 'object') {
            for (var i in key) this.attr(i, key[i]);
            return this;
        } else if(val === undefined) {
            return this.get()[key];
        }

        this.each(function(){
            this.get()[key] = val;
        });

        return this;
    };

    _html.prototype.data = function(key, val) {

        if (typeof key === 'object') {
            for (var i in key) this.data(i, key[i]);
            return this;
        } else if(val === undefined) {
            element = this.get();
            return element.data ? element.data[key] : undefined;
        }

        this.each(function(){
            element = this.get();
            element.data = element.data || [];
            element.data[key] = val;
        });

        return this;
    };

    _html.prototype.on = function(eventType, eventHandler) {

        this.each(function(){
            element = this.get();
            if (typeof element !== 'object' || element.nodeType !== 1) return;
            element.addEventListener(eventType, eventHandler);
        });

        return this;

    };

    ol3.html = _html;

});

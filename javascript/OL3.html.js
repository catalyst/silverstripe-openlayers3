(function(window, undefined) {

    function _html(selector, container, config) {

        var matches;

        this.selector = selector;
        this.elements = [];

        if (!selector) {

            this.elements = [];

        } else if (selector instanceof _html) {

            for (var i in selector) this[i] = selector[i];

        } else if (
            typeof selector === 'object' &&
            (selector.nodeType === 1 || selector.nodeType === 3)
        ) {

            this.elements = [ selector ];

        } else if (matches = selector.match(/^<([a-z][a-z0-9]*)>$/)) {

            var element = document.createElement(matches[1]);
            container && new _html(container).get().appendChild(element)
            this.elements = [ element ];

        } else if(matches = selector.match(/^"([^"]*)"$/)) {

            var element = document.createTextNode(matches[1]);
            container && new _html(container).get().appendChild(element)
            this.elements = [ element ];

        } else {

            container = (new _html(container)).get() || document;
            this.elements = container.querySelectorAll(selector);

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

    _html.prototype.insert = function(html, before) {
        html = H(html);

        if ((before = before || null) !== true) before = H(before).get();

        this.each(function(){
            var insertedInto = this.get();
            var beforeNode = before === true ? insertedInto.firstChild : before;
            html.each(function(){
                insertedInto.insertBefore(this.get(), beforeNode);
            });
        });

        return this;
    }

    _html.prototype.append = function(html) {
        return this.insert(html);
    };

    _html.prototype.prepend = function(html) {
        return this.insert(html, true);
    };

    _html.prototype.insertBefore = function(html) {
        var parent = H(H(html).get().parentNode);
        parent.insert(this, html);

        return this;
    };

    _html.prototype.attr = function(key, val) {

        if (typeof key === 'object') {
            for (var i in key) this.attr(i, key[i]);
            return this;
        } else if(val === undefined) {
            var element = this.get();
            return element.getAttribute(key);
        }

        this.each(function(){
            var element = this.get();
            element.setAttribute(key, val);
        });

        return this;
    };

    _html.prototype.prop = function(key, val) {

        if (typeof key === 'object') {
            for (var i in key) this.prop(i, key[i]);
            return this;
        } else if(val === undefined) {
            var element = this.get();
            return element[key];
        }

        this.each(function(){
            var element = this.get();
            element[key] = val;
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

    _html.prototype.css = function(key, val) {

        if (typeof key === 'object') {
            for (var i in key) this.css(i, key[i]);
            return this;
        } else if(val === undefined) {
            var element = this.get();
            if (typeof element !== 'object' || element.nodeType !== 1) return undefined;
            return window.getComputedStyle(element).getPropertyValue(key);
        }

        this.each(function(){
            var element = this.get();
            if (typeof element !== 'object' || element.nodeType !== 1) return;
            element.style[key] = val;
        });

        return this;
    };

    _html.prototype.text = function(t) {
        if (typeof t === 'string') {
            for (var i = 0; i < this.length; i++) {
                this.get(i).innerText = t;
            }
            return this;
        } else {
            return this.length ? this.get().innerText : null;
        }
    };

    _html.prototype.flush = function() {
        return this.text('');
    };

    _html.prototype.hasClass = function(c) {
        return this.length ? this.attr('class').split(/\s+/).indexOf(c) != -1 : false;
    };

    _html.prototype.addClass = function(c) {
        cs = typeof c === 'string' ? c.split(' ') : (c.constructor == Array ? c : []);
        for (var i = 0; i < cs.length; i++) {
            for (var j = 0; j < this.length; j++) {
                var val = this.get(j).getAttribute('class') || '';
                if (val.split(/\s+/).indexOf(cs[i]) == -1) {
                    this.get(j).setAttribute('class', (val + ' ' + cs[i]).trim());
                }
            }
        }
        return this;
    };

    _html.prototype.removeClass = function(c) {
        cs = typeof c === 'string' ? c.split(' ') : (c.constructor == Array ? c : []);
        for (var i = 0; i < cs.length; i++) {
            for (var j = 0; j < this.length; j++) {
                var vals = this.get(j).getAttribute('class').split(/\s+/) || [];
                if (vals.indexOf(cs[i]) != -1 && vals.splice(vals.indexOf(cs[i]), 1)) {
                    this.get(j).setAttribute('class', vals.join(' '));
                }
            }
        }
        return this;
    };

    window.H = function(selector, conatiner, config) { return new _html(selector, conatiner, config); };

    window.H.appendQueryToUrl = function(url, params) {
        var parts = url.split('?');
        var query = new URLSearchParams(parts[1] || '');
        for (var i in params) query.set(i, params[i]);
        return parts[0] + '?' + query.toString();
    }

})(this);

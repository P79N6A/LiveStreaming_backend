/*!
 * =====================================================
 * SUI Mobile - http://m.sui.taobao.org/
 *
 * =====================================================
 */
;$.smVersion = "0.6.2";+function ($) {
    "use strict";

    //全局配置
    var defaults = {
        autoInit: false, //自动初始化页面
        showPageLoadingIndicator: true, //push.js加载页面的时候显示一个加载提示
        router: true, //默认使用router
        swipePanel: "left", //滑动打开侧栏
        swipePanelOnlyClose: true  //只允许滑动关闭，不允许滑动打开侧栏
    };

    $.smConfig = $.extend(defaults, $.config);

}(Zepto);

+ function($) {
    "use strict";

    //比较一个字符串版本号
    //a > b === 1
    //a = b === 0
    //a < b === -1
    $.compareVersion = function(a, b) {
        var as = a.split('.');
        var bs = b.split('.');
        if (a === b) return 0;

        for (var i = 0; i < as.length; i++) {
            var x = parseInt(as[i]);
            if (!bs[i]) return 1;
            var y = parseInt(bs[i]);
            if (x < y) return -1;
            if (x > y) return 1;
        }
        return -1;
    };

    $.getCurrentPage = function() {
        return $(".page-current")[0] || $(".page")[0] || document.body;
    };

}(Zepto);

/* global WebKitCSSMatrix:true */

(function($) {
    "use strict";
    ['width', 'height'].forEach(function(dimension) {
        var  Dimension = dimension.replace(/./, function(m) {
            return m[0].toUpperCase();
        });
        $.fn['outer' + Dimension] = function(margin) {
            var elem = this;
            if (elem) {
                var size = elem[dimension]();
                var sides = {
                    'width': ['left', 'right'],
                    'height': ['top', 'bottom']
                };
                sides[dimension].forEach(function(side) {
                    if (margin) size += parseInt(elem.css('margin-' + side), 10);
                });
                return size;
            } else {
                return null;
            }
        };
    });

    //support
    $.support = (function() {
        var support = {
            touch: !!(('ontouchstart' in window) || window.DocumentTouch && document instanceof window.DocumentTouch)
        };
        return support;
    })();

    $.touchEvents = {
        start: $.support.touch ? 'touchstart' : 'mousedown',
        move: $.support.touch ? 'touchmove' : 'mousemove',
        end: $.support.touch ? 'touchend' : 'mouseup'
    };

    $.getTranslate = function (el, axis) {
        var matrix, curTransform, curStyle, transformMatrix;

        // automatic axis detection
        if (typeof axis === 'undefined') {
            axis = 'x';
        }

        curStyle = window.getComputedStyle(el, null);
        if (window.WebKitCSSMatrix) {
            // Some old versions of Webkit choke when 'none' is passed; pass
            // empty string instead in this case
            transformMatrix = new WebKitCSSMatrix(curStyle.webkitTransform === 'none' ? '' : curStyle.webkitTransform);
        }
        else {
            transformMatrix = curStyle.MozTransform || curStyle.transform || curStyle.getPropertyValue('transform').replace('translate(', 'matrix(1, 0, 0, 1,');
            matrix = transformMatrix.toString().split(',');
        }

        if (axis === 'x') {
            //Latest Chrome and webkits Fix
            if (window.WebKitCSSMatrix)
                curTransform = transformMatrix.m41;
            //Crazy IE10 Matrix
            else if (matrix.length === 16)
                curTransform = parseFloat(matrix[12]);
            //Normal Browsers
            else
                curTransform = parseFloat(matrix[4]);
        }
        if (axis === 'y') {
            //Latest Chrome and webkits Fix
            if (window.WebKitCSSMatrix)
                curTransform = transformMatrix.m42;
            //Crazy IE10 Matrix
            else if (matrix.length === 16)
                curTransform = parseFloat(matrix[13]);
            //Normal Browsers
            else
                curTransform = parseFloat(matrix[5]);
        }

        return curTransform || 0;
    };
    /* jshint ignore:start */
    $.requestAnimationFrame = function (callback) {
        if (requestAnimationFrame) return requestAnimationFrame(callback);
        else if (webkitRequestAnimationFrame) return webkitRequestAnimationFrame(callback);
        else if (mozRequestAnimationFrame) return mozRequestAnimationFrame(callback);
        else {
            return setTimeout(callback, 1000 / 60);
        }
    };
    $.cancelAnimationFrame = function (id) {
        if (cancelAnimationFrame) return cancelAnimationFrame(id);
        else if (webkitCancelAnimationFrame) return webkitCancelAnimationFrame(id);
        else if (mozCancelAnimationFrame) return mozCancelAnimationFrame(id);
        else {
            return clearTimeout(id);
        }
    };
    /* jshint ignore:end */

    $.fn.dataset = function() {
        var dataset = {},
            ds = this[0].dataset;
        for (var key in ds) { // jshint ignore:line
            var item = (dataset[key] = ds[key]);
            if (item === 'false') dataset[key] = false;
            else if (item === 'true') dataset[key] = true;
            else if (parseFloat(item) === item * 1) dataset[key] = item * 1;
        }
        // mixin dataset and __eleData
        return $.extend({}, dataset, this[0].__eleData);
    };
    $.fn.data = function(key, value) {
        var tmpData = $(this).dataset();
        if (!key) {
            return tmpData;
        }
        // value may be 0, false, null
        if (typeof value === 'undefined') {
            // Get value
            var dataVal = tmpData[key],
                __eD = this[0].__eleData;

            //if (dataVal !== undefined) {
            if (__eD && (key in __eD)) {
                return __eD[key];
            } else {
                return dataVal;
            }

        } else {
            // Set value,uniformly set in extra ```__eleData```
            for (var i = 0; i < this.length; i++) {
                var el = this[i];
                // delete multiple data in dataset
                if (key in tmpData) delete el.dataset[key];

                if (!el.__eleData) el.__eleData = {};
                el.__eleData[key] = value;
            }
            return this;
        }
    };
    function __dealCssEvent(eventNameArr, callback) {
        var events = eventNameArr,
            i, dom = this;// jshint ignore:line

        function fireCallBack(e) {
            /*jshint validthis:true */
            if (e.target !== this) return;
            callback.call(this, e);
            for (i = 0; i < events.length; i++) {
                dom.off(events[i], fireCallBack);
            }
        }
        if (callback) {
            for (i = 0; i < events.length; i++) {
                dom.on(events[i], fireCallBack);
            }
        }
    }
    $.fn.animationEnd = function(callback) {
        __dealCssEvent.call(this, ['webkitAnimationEnd', 'animationend'], callback);
        return this;
    };
    $.fn.transitionEnd = function(callback) {
        __dealCssEvent.call(this, ['webkitTransitionEnd', 'transitionend'], callback);
        return this;
    };
    $.fn.transition = function(duration) {
        if (typeof duration !== 'string') {
            duration = duration + 'ms';
        }
        for (var i = 0; i < this.length; i++) {
            var elStyle = this[i].style;
            elStyle.webkitTransitionDuration = elStyle.MozTransitionDuration = elStyle.transitionDuration = duration;
        }
        return this;
    };
    $.fn.transform = function(transform) {
        for (var i = 0; i < this.length; i++) {
            var elStyle = this[i].style;
            elStyle.webkitTransform = elStyle.MozTransform = elStyle.transform = transform;
        }
        return this;
    };
    $.fn.prevAll = function (selector) {
        var prevEls = [];
        var el = this[0];
        if (!el) return $([]);
        while (el.previousElementSibling) {
            var prev = el.previousElementSibling;
            if (selector) {
                if($(prev).is(selector)) prevEls.push(prev);
            }
            else prevEls.push(prev);
            el = prev;
        }
        return $(prevEls);
    };
    $.fn.nextAll = function (selector) {
        var nextEls = [];
        var el = this[0];
        if (!el) return $([]);
        while (el.nextElementSibling) {
            var next = el.nextElementSibling;
            if (selector) {
                if($(next).is(selector)) nextEls.push(next);
            }
            else nextEls.push(next);
            el = next;
        }
        return $(nextEls);
    };

    //重置zepto的show方法，防止有些人引用的版本中 show 方法操作 opacity 属性影响动画执行
    $.fn.show = function(){
        var elementDisplay = {};
        function defaultDisplay(nodeName) {
            var element, display;
            if (!elementDisplay[nodeName]) {
                element = document.createElement(nodeName);
                document.body.appendChild(element);
                display = getComputedStyle(element, '').getPropertyValue("display");
                element.parentNode.removeChild(element);
                display === "none" && (display = "block");
                elementDisplay[nodeName] = display;
            }
            return elementDisplay[nodeName];
        }

        return this.each(function(){
            this.style.display === "none" && (this.style.display = '');
            if (getComputedStyle(this, '').getPropertyValue("display") === "none");
            this.style.display = defaultDisplay(this.nodeName);
        });
    };
})(Zepto);

/*===========================
Device/OS Detection
===========================*/
;(function ($) {
    "use strict";
    var device = {};
    var ua = navigator.userAgent;

    var android = ua.match(/(Android);?[\s\/]+([\d.]+)?/);
    var ipad = ua.match(/(iPad).*OS\s([\d_]+)/);
    var ipod = ua.match(/(iPod)(.*OS\s([\d_]+))?/);
    var iphone = !ipad && ua.match(/(iPhone\sOS)\s([\d_]+)/);

    device.ios = device.android = device.iphone = device.ipad = device.androidChrome = false;

    // Android
    if (android) {
        device.os = 'android';
        device.osVersion = android[2];
        device.android = true;
        device.androidChrome = ua.toLowerCase().indexOf('chrome') >= 0;
    }
    if (ipad || iphone || ipod) {
        device.os = 'ios';
        device.ios = true;
    }
    // iOS
    if (iphone && !ipod) {
        device.osVersion = iphone[2].replace(/_/g, '.');
        device.iphone = true;
    }
    if (ipad) {
        device.osVersion = ipad[2].replace(/_/g, '.');
        device.ipad = true;
    }
    if (ipod) {
        device.osVersion = ipod[3] ? ipod[3].replace(/_/g, '.') : null;
        device.iphone = true;
    }
    // iOS 8+ changed UA
    if (device.ios && device.osVersion && ua.indexOf('Version/') >= 0) {
        if (device.osVersion.split('.')[0] === '10') {
            device.osVersion = ua.toLowerCase().split('version/')[1].split(' ')[0];
        }
    }

    // Webview
    device.webView = (iphone || ipad || ipod) && ua.match(/.*AppleWebKit(?!.*Safari)/i);

    // Minimal UI
    if (device.os && device.os === 'ios') {
        var osVersionArr = device.osVersion.split('.');
        device.minimalUi = !device.webView &&
            (ipod || iphone) &&
            (osVersionArr[0] * 1 === 7 ? osVersionArr[1] * 1 >= 1 : osVersionArr[0] * 1 > 7) &&
            $('meta[name="viewport"]').length > 0 && $('meta[name="viewport"]').attr('content').indexOf('minimal-ui') >= 0;
    }

    // Check for status bar and fullscreen app mode
    var windowWidth = $(window).width();
    var windowHeight = $(window).height();
    device.statusBar = false;
    if (device.webView && (windowWidth * windowHeight === screen.width * screen.height)) {
        device.statusBar = true;
    }
    else {
        device.statusBar = false;
    }

    // Classes
    var classNames = [];

    // Pixel Ratio
    device.pixelRatio = window.devicePixelRatio || 1;
    classNames.push('pixel-ratio-' + Math.floor(device.pixelRatio));
    if (device.pixelRatio >= 2) {
        classNames.push('retina');
    }

    // OS classes
    if (device.os) {
        classNames.push(device.os, device.os + '-' + device.osVersion.split('.')[0], device.os + '-' + device.osVersion.replace(/\./g, '-'));
        if (device.os === 'ios') {
            var major = parseInt(device.osVersion.split('.')[0], 10);
            for (var i = major - 1; i >= 6; i--) {
                classNames.push('ios-gt-' + i);
            }
        }

    }
    // Status bar classes
    if (device.statusBar) {
        classNames.push('with-statusbar-overlay');
    }
    else {
        $('html').removeClass('with-statusbar-overlay');
    }

    // Add html classes
    if (classNames.length > 0) $('html').addClass(classNames.join(' '));

    // keng..
    device.isWeixin = /MicroMessenger/i.test(ua);

    $.device = device;
})(Zepto);

;(function () {
    'use strict';

    /**
     * @preserve FastClick: polyfill to remove click delays on browsers with touch UIs.
     *
     * @codingstandard ftlabs-jsv2
     * @copyright The Financial Times Limited [All Rights Reserved]
     * @license MIT License (see LICENSE.txt)
     */

    /*jslint browser:true, node:true, elision:true*/
    /*global Event, Node*/


    /**
     * Instantiate fast-clicking listeners on the specified layer.
     *
     * @constructor
     * @param {Element} layer The layer to listen on
     * @param {Object} [options={}] The options to override the defaults
     */
    function FastClick(layer, options) {
        var oldOnClick;

        options = options || {};

        /**
         * Whether a click is currently being tracked.
         *
         * @type boolean
         */
        this.trackingClick = false;


        /**
         * Timestamp for when click tracking started.
         *
         * @type number
         */
        this.trackingClickStart = 0;


        /**
         * The element being tracked for a click.
         *
         * @type EventTarget
         */
        this.targetElement = null;


        /**
         * X-coordinate of touch start event.
         *
         * @type number
         */
        this.touchStartX = 0;


        /**
         * Y-coordinate of touch start event.
         *
         * @type number
         */
        this.touchStartY = 0;


        /**
         * ID of the last touch, retrieved from Touch.identifier.
         *
         * @type number
         */
        this.lastTouchIdentifier = 0;


        /**
         * Touchmove boundary, beyond which a click will be cancelled.
         *
         * @type number
         */
        this.touchBoundary = options.touchBoundary || 10;


        /**
         * The FastClick layer.
         *
         * @type Element
         */
        this.layer = layer;

        /**
         * The minimum time between tap(touchstart and touchend) events
         *
         * @type number
         */
        this.tapDelay = options.tapDelay || 200;

        /**
         * The maximum time for a tap
         *
         * @type number
         */
        this.tapTimeout = options.tapTimeout || 700;

        if (FastClick.notNeeded(layer)) {
            return;
        }

        // Some old versions of Android don't have Function.prototype.bind
        function bind(method, context) {
            return function() { return method.apply(context, arguments); };
        }


        var methods = ['onMouse', 'onClick', 'onTouchStart', 'onTouchMove', 'onTouchEnd', 'onTouchCancel'];
        var context = this;
        for (var i = 0, l = methods.length; i < l; i++) {
            context[methods[i]] = bind(context[methods[i]], context);
        }

        // Set up event handlers as required
        if (deviceIsAndroid) {
            layer.addEventListener('mouseover', this.onMouse, true);
            layer.addEventListener('mousedown', this.onMouse, true);
            layer.addEventListener('mouseup', this.onMouse, true);
        }

        layer.addEventListener('click', this.onClick, true);
        layer.addEventListener('touchstart', this.onTouchStart, false);
        layer.addEventListener('touchmove', this.onTouchMove, false);
        layer.addEventListener('touchend', this.onTouchEnd, false);
        layer.addEventListener('touchcancel', this.onTouchCancel, false);

        // Hack is required for browsers that don't support Event#stopImmediatePropagation (e.g. Android 2)
        // which is how FastClick normally stops click events bubbling to callbacks registered on the FastClick
        // layer when they are cancelled.
        if (!Event.prototype.stopImmediatePropagation) {
            layer.removeEventListener = function(type, callback, capture) {
                var rmv = Node.prototype.removeEventListener;
                if (type === 'click') {
                    rmv.call(layer, type, callback.hijacked || callback, capture);
                } else {
                    rmv.call(layer, type, callback, capture);
                }
            };

            layer.addEventListener = function(type, callback, capture) {
                var adv = Node.prototype.addEventListener;
                if (type === 'click') {
                    adv.call(layer, type, callback.hijacked || (callback.hijacked = function(event) {
                        if (!event.propagationStopped) {
                            callback(event);
                        }
                    }), capture);
                } else {
                    adv.call(layer, type, callback, capture);
                }
            };
        }

        // If a handler is already declared in the element's onclick attribute, it will be fired before
        // FastClick's onClick handler. Fix this by pulling out the user-defined handler function and
        // adding it as listener.
        if (typeof layer.onclick === 'function') {

            // Android browser on at least 3.2 requires a new reference to the function in layer.onclick
            // - the old one won't work if passed to addEventListener directly.
            oldOnClick = layer.onclick;
            layer.addEventListener('click', function(event) {
                oldOnClick(event);
            }, false);
            layer.onclick = null;
        }
    }

    /**
     * Windows Phone 8.1 fakes user agent string to look like Android and iPhone.
     *
     * @type boolean
     */
    var deviceIsWindowsPhone = navigator.userAgent.indexOf("Windows Phone") >= 0;

    /**
     * Android requires exceptions.
     *
     * @type boolean
     */
    var deviceIsAndroid = navigator.userAgent.indexOf('Android') > 0 && !deviceIsWindowsPhone;


    /**
     * iOS requires exceptions.
     *
     * @type boolean
     */
    var deviceIsIOS = /iP(ad|hone|od)/.test(navigator.userAgent) && !deviceIsWindowsPhone;


    /**
     * iOS 4 requires an exception for select elements.
     *
     * @type boolean
     */
    var deviceIsIOS4 = deviceIsIOS && (/OS 4_\d(_\d)?/).test(navigator.userAgent);


    /**
     * iOS 6.0-7.* requires the target element to be manually derived
     *
     * @type boolean
     */
    var deviceIsIOSWithBadTarget = deviceIsIOS && (/OS [6-7]_\d/).test(navigator.userAgent);

    /**
     * BlackBerry requires exceptions.
     *
     * @type boolean
     */
    var deviceIsBlackBerry10 = navigator.userAgent.indexOf('BB10') > 0;

    /**
     * 判断是否组合型label
     * @type {Boolean}
     */
    var isCompositeLabel = false;

    /**
     * Determine whether a given element requires a native click.
     *
     * @param {EventTarget|Element} target Target DOM element
     * @returns {boolean} Returns true if the element needs a native click
     */
    FastClick.prototype.needsClick = function(target) {

        // 修复bug: 如果父元素中有 label
        // 如果label上有needsclick这个类，则用原生的点击，否则，用模拟点击
        var parent = target;
        while(parent && (parent.tagName.toUpperCase() !== "BODY")) {
            if (parent.tagName.toUpperCase() === "LABEL") {
                isCompositeLabel = true;
                if ((/\bneedsclick\b/).test(parent.className)) return true;
            }
            parent = parent.parentNode;
        }

        switch (target.nodeName.toLowerCase()) {

            // Don't send a synthetic click to disabled inputs (issue #62)
            case 'button':
            case 'select':
            case 'textarea':
                if (target.disabled) {
                    return true;
                }

                break;
            case 'input':

                // File inputs need real clicks on iOS 6 due to a browser bug (issue #68)
                if ((deviceIsIOS && target.type === 'file') || target.disabled) {
                    return true;
                }

                break;
            case 'label':
            case 'iframe': // iOS8 homescreen apps can prevent events bubbling into frames
            case 'video':
                return true;
        }

        return (/\bneedsclick\b/).test(target.className);
    };


    /**
     * Determine whether a given element requires a call to focus to simulate click into element.
     *
     * @param {EventTarget|Element} target Target DOM element
     * @returns {boolean} Returns true if the element requires a call to focus to simulate native click.
     */
    FastClick.prototype.needsFocus = function(target) {
        switch (target.nodeName.toLowerCase()) {
            case 'textarea':
                return true;
            case 'select':
                return !deviceIsAndroid;
            case 'input':
                switch (target.type) {
                    case 'button':
                    case 'checkbox':
                    case 'file':
                    case 'image':
                    case 'radio':
                    case 'submit':
                        return false;
                }

                // No point in attempting to focus disabled inputs
                return !target.disabled && !target.readOnly;
            default:
                return (/\bneedsfocus\b/).test(target.className);
        }
    };


    /**
     * Send a click event to the specified element.
     *
     * @param {EventTarget|Element} targetElement
     * @param {Event} event
     */
    FastClick.prototype.sendClick = function(targetElement, event) {
        var clickEvent, touch;

        // On some Android devices activeElement needs to be blurred otherwise the synthetic click will have no effect (#24)
        if (document.activeElement && document.activeElement !== targetElement) {
            document.activeElement.blur();
        }

        touch = event.changedTouches[0];

        // Synthesise a click event, with an extra attribute so it can be tracked
        clickEvent = document.createEvent('MouseEvents');
        clickEvent.initMouseEvent(this.determineEventType(targetElement), true, true, window, 1, touch.screenX, touch.screenY, touch.clientX, touch.clientY, false, false, false, false, 0, null);
        clickEvent.forwardedTouchEvent = true;
        targetElement.dispatchEvent(clickEvent);
    };

    FastClick.prototype.determineEventType = function(targetElement) {

        //Issue #159: Android Chrome Select Box does not open with a synthetic click event
        if (deviceIsAndroid && targetElement.tagName.toLowerCase() === 'select') {
            return 'mousedown';
        }

        return 'click';
    };


    /**
     * @param {EventTarget|Element} targetElement
     */
    FastClick.prototype.focus = function(targetElement) {
        var length;

        // Issue #160: on iOS 7, some input elements (e.g. date datetime month) throw a vague TypeError on setSelectionRange. These elements don't have an integer value for the selectionStart and selectionEnd properties, but unfortunately that can't be used for detection because accessing the properties also throws a TypeError. Just check the type instead. Filed as Apple bug #15122724.
        var unsupportedType = ['date', 'time', 'month', 'number', 'email'];
        if (deviceIsIOS && targetElement.setSelectionRange && unsupportedType.indexOf(targetElement.type) === -1) {
            length = targetElement.value.length;
            targetElement.setSelectionRange(length, length);
        } else {
            targetElement.focus();
        }
    };


    /**
     * Check whether the given target element is a child of a scrollable layer and if so, set a flag on it.
     *
     * @param {EventTarget|Element} targetElement
     */
    FastClick.prototype.updateScrollParent = function(targetElement) {
        var scrollParent, parentElement;

        scrollParent = targetElement.fastClickScrollParent;

        // Attempt to discover whether the target element is contained within a scrollable layer. Re-check if the
        // target element was moved to another parent.
        if (!scrollParent || !scrollParent.contains(targetElement)) {
            parentElement = targetElement;
            do {
                if (parentElement.scrollHeight > parentElement.offsetHeight) {
                    scrollParent = parentElement;
                    targetElement.fastClickScrollParent = parentElement;
                    break;
                }

                parentElement = parentElement.parentElement;
            } while (parentElement);
        }

        // Always update the scroll top tracker if possible.
        if (scrollParent) {
            scrollParent.fastClickLastScrollTop = scrollParent.scrollTop;
        }
    };


    /**
     * @param {EventTarget} targetElement
     * @returns {Element|EventTarget}
     */
    FastClick.prototype.getTargetElementFromEventTarget = function(eventTarget) {

        // On some older browsers (notably Safari on iOS 4.1 - see issue #56) the event target may be a text node.
        if (eventTarget.nodeType === Node.TEXT_NODE) {
            return eventTarget.parentNode;
        }

        return eventTarget;
    };


    /**
     * On touch start, record the position and scroll offset.
     *
     * @param {Event} event
     * @returns {boolean}
     */
    FastClick.prototype.onTouchStart = function(event) {
        var targetElement, touch, selection;

        // Ignore multiple touches, otherwise pinch-to-zoom is prevented if both fingers are on the FastClick element (issue #111).
        if (event.targetTouches.length > 1) {
            return true;
        }

        targetElement = this.getTargetElementFromEventTarget(event.target);
        touch = event.targetTouches[0];

        if (deviceIsIOS) {

            // Only trusted events will deselect text on iOS (issue #49)
            selection = window.getSelection();
            if (selection.rangeCount && !selection.isCollapsed) {
                return true;
            }

            if (!deviceIsIOS4) {

                // Weird things happen on iOS when an alert or confirm dialog is opened from a click event callback (issue #23):
                // when the user next taps anywhere else on the page, new touchstart and touchend events are dispatched
                // with the same identifier as the touch event that previously triggered the click that triggered the alert.
                // Sadly, there is an issue on iOS 4 that causes some normal touch events to have the same identifier as an
                // immediately preceeding touch event (issue #52), so this fix is unavailable on that platform.
                // Issue 120: touch.identifier is 0 when Chrome dev tools 'Emulate touch events' is set with an iOS device UA string,
                // which causes all touch events to be ignored. As this block only applies to iOS, and iOS identifiers are always long,
                // random integers, it's safe to to continue if the identifier is 0 here.
                if (touch.identifier && touch.identifier === this.lastTouchIdentifier) {
                    event.preventDefault();
                    return false;
                }

                this.lastTouchIdentifier = touch.identifier;

                // If the target element is a child of a scrollable layer (using -webkit-overflow-scrolling: touch) and:
                // 1) the user does a fling scroll on the scrollable layer
                // 2) the user stops the fling scroll with another tap
                // then the event.target of the last 'touchend' event will be the element that was under the user's finger
                // when the fling scroll was started, causing FastClick to send a click event to that layer - unless a check
                // is made to ensure that a parent layer was not scrolled before sending a synthetic click (issue #42).
                this.updateScrollParent(targetElement);
            }
        }

        this.trackingClick = true;
        this.trackingClickStart = event.timeStamp;
        this.targetElement = targetElement;

        this.touchStartX = touch.pageX;
        this.touchStartY = touch.pageY;

        // Prevent phantom clicks on fast double-tap (issue #36)
        if ((event.timeStamp - this.lastClickTime) < this.tapDelay) {
            event.preventDefault();
        }

        return true;
    };


    /**
     * Based on a touchmove event object, check whether the touch has moved past a boundary since it started.
     *
     * @param {Event} event
     * @returns {boolean}
     */
    FastClick.prototype.touchHasMoved = function(event) {
        var touch = event.changedTouches[0], boundary = this.touchBoundary;

        if (Math.abs(touch.pageX - this.touchStartX) > boundary || Math.abs(touch.pageY - this.touchStartY) > boundary) {
            return true;
        }

        return false;
    };


    /**
     * Update the last position.
     *
     * @param {Event} event
     * @returns {boolean}
     */
    FastClick.prototype.onTouchMove = function(event) {
        if (!this.trackingClick) {
            return true;
        }

        // If the touch has moved, cancel the click tracking
        if (this.targetElement !== this.getTargetElementFromEventTarget(event.target) || this.touchHasMoved(event)) {
            this.trackingClick = false;
            this.targetElement = null;
        }

        return true;
    };


    /**
     * Attempt to find the labelled control for the given label element.
     *
     * @param {EventTarget|HTMLLabelElement} labelElement
     * @returns {Element|null}
     */
    FastClick.prototype.findControl = function(labelElement) {

        // Fast path for newer browsers supporting the HTML5 control attribute
        if (labelElement.control !== undefined) {
            return labelElement.control;
        }

        // All browsers under test that support touch events also support the HTML5 htmlFor attribute
        if (labelElement.htmlFor) {
            return document.getElementById(labelElement.htmlFor);
        }

        // If no for attribute exists, attempt to retrieve the first labellable descendant element
        // the list of which is defined here: http://www.w3.org/TR/html5/forms.html#category-label
        return labelElement.querySelector('button, input:not([type=hidden]), keygen, meter, output, progress, select, textarea');
    };


    /**
     * On touch end, determine whether to send a click event at once.
     *
     * @param {Event} event
     * @returns {boolean}
     */
    FastClick.prototype.onTouchEnd = function(event) {
        var forElement, trackingClickStart, targetTagName, scrollParent, touch, targetElement = this.targetElement;

        if (!this.trackingClick) {
            return true;
        }

        // Prevent phantom clicks on fast double-tap (issue #36)
        if ((event.timeStamp - this.lastClickTime) < this.tapDelay) {
            this.cancelNextClick = true;
            return true;
        }

        if ((event.timeStamp - this.trackingClickStart) > this.tapTimeout) {
            return true;
        }
        //修复安卓微信下，input type="date" 的bug，经测试date,time,month已没问题
        var unsupportedType = ['date', 'time', 'month'];
        if(unsupportedType.indexOf(event.target.type) !== -1){
            　　　　return false;
            　　}
        // Reset to prevent wrong click cancel on input (issue #156).
        this.cancelNextClick = false;

        this.lastClickTime = event.timeStamp;

        trackingClickStart = this.trackingClickStart;
        this.trackingClick = false;
        this.trackingClickStart = 0;

        // On some iOS devices, the targetElement supplied with the event is invalid if the layer
        // is performing a transition or scroll, and has to be re-detected manually. Note that
        // for this to function correctly, it must be called *after* the event target is checked!
        // See issue #57; also filed as rdar://13048589 .
        if (deviceIsIOSWithBadTarget) {
            touch = event.changedTouches[0];

            // In certain cases arguments of elementFromPoint can be negative, so prevent setting targetElement to null
            targetElement = document.elementFromPoint(touch.pageX - window.pageXOffset, touch.pageY - window.pageYOffset) || targetElement;
            targetElement.fastClickScrollParent = this.targetElement.fastClickScrollParent;
        }

        targetTagName = targetElement.tagName.toLowerCase();
        if (targetTagName === 'label') {
            forElement = this.findControl(targetElement);
            if (forElement) {
                this.focus(targetElement);
                if (deviceIsAndroid) {
                    return false;
                }

                targetElement = forElement;
            }
        } else if (this.needsFocus(targetElement)) {

            // Case 1: If the touch started a while ago (best guess is 100ms based on tests for issue #36) then focus will be triggered anyway. Return early and unset the target element reference so that the subsequent click will be allowed through.
            // Case 2: Without this exception for input elements tapped when the document is contained in an iframe, then any inputted text won't be visible even though the value attribute is updated as the user types (issue #37).
            if ((event.timeStamp - trackingClickStart) > 100 || (deviceIsIOS && window.top !== window && targetTagName === 'input')) {
                this.targetElement = null;
                return false;
            }

            this.focus(targetElement);
            this.sendClick(targetElement, event);

            // Select elements need the event to go through on iOS 4, otherwise the selector menu won't open.
            // Also this breaks opening selects when VoiceOver is active on iOS6, iOS7 (and possibly others)
            if (!deviceIsIOS || targetTagName !== 'select') {
                this.targetElement = null;
                event.preventDefault();
            }

            return false;
        }

        if (deviceIsIOS && !deviceIsIOS4) {

            // Don't send a synthetic click event if the target element is contained within a parent layer that was scrolled
            // and this tap is being used to stop the scrolling (usually initiated by a fling - issue #42).
            scrollParent = targetElement.fastClickScrollParent;
            if (scrollParent && scrollParent.fastClickLastScrollTop !== scrollParent.scrollTop) {
                return true;
            }
        }

        // Prevent the actual click from going though - unless the target node is marked as requiring
        // real clicks or if it is in the whitelist in which case only non-programmatic clicks are permitted.
        if (!this.needsClick(targetElement)) {
            event.preventDefault();
            this.sendClick(targetElement, event);
        }

        return false;
    };


    /**
     * On touch cancel, stop tracking the click.
     *
     * @returns {void}
     */
    FastClick.prototype.onTouchCancel = function() {
        this.trackingClick = false;
        this.targetElement = null;
    };


    /**
     * Determine mouse events which should be permitted.
     *
     * @param {Event} event
     * @returns {boolean}
     */
    FastClick.prototype.onMouse = function(event) {

        // If a target element was never set (because a touch event was never fired) allow the event
        if (!this.targetElement) {
            return true;
        }

        if (event.forwardedTouchEvent) {
            return true;
        }

        // Programmatically generated events targeting a specific element should be permitted
        if (!event.cancelable) {
            return true;
        }

        // Derive and check the target element to see whether the mouse event needs to be permitted;
        // unless explicitly enabled, prevent non-touch click events from triggering actions,
        // to prevent ghost/doubleclicks.
        if (!this.needsClick(this.targetElement) || this.cancelNextClick) {

            // Prevent any user-added listeners declared on FastClick element from being fired.
            if (event.stopImmediatePropagation) {
                event.stopImmediatePropagation();
            } else {

                // Part of the hack for browsers that don't support Event#stopImmediatePropagation (e.g. Android 2)
                event.propagationStopped = true;
            }

            // Cancel the event
            event.stopPropagation();
            // 允许组合型label冒泡
            if (!isCompositeLabel) {
                event.preventDefault();
            }
            // 允许组合型label冒泡
            return false;
        }

        // If the mouse event is permitted, return true for the action to go through.
        return true;
    };


    /**
     * On actual clicks, determine whether this is a touch-generated click, a click action occurring
     * naturally after a delay after a touch (which needs to be cancelled to avoid duplication), or
     * an actual click which should be permitted.
     *
     * @param {Event} event
     * @returns {boolean}
     */
    FastClick.prototype.onClick = function(event) {
        var permitted;

        // It's possible for another FastClick-like library delivered with third-party code to fire a click event before FastClick does (issue #44). In that case, set the click-tracking flag back to false and return early. This will cause onTouchEnd to return early.
        if (this.trackingClick) {
            this.targetElement = null;
            this.trackingClick = false;
            return true;
        }

        // Very odd behaviour on iOS (issue #18): if a submit element is present inside a form and the user hits enter in the iOS simulator or clicks the Go button on the pop-up OS keyboard the a kind of 'fake' click event will be triggered with the submit-type input element as the target.
        if (event.target.type === 'submit' && event.detail === 0) {
            return true;
        }

        permitted = this.onMouse(event);

        // Only unset targetElement if the click is not permitted. This will ensure that the check for !targetElement in onMouse fails and the browser's click doesn't go through.
        if (!permitted) {
            this.targetElement = null;
        }

        // If clicks are permitted, return true for the action to go through.
        return permitted;
    };


    /**
     * Remove all FastClick's event listeners.
     *
     * @returns {void}
     */
    FastClick.prototype.destroy = function() {
        var layer = this.layer;

        if (deviceIsAndroid) {
            layer.removeEventListener('mouseover', this.onMouse, true);
            layer.removeEventListener('mousedown', this.onMouse, true);
            layer.removeEventListener('mouseup', this.onMouse, true);
        }

        layer.removeEventListener('click', this.onClick, true);
        layer.removeEventListener('touchstart', this.onTouchStart, false);
        layer.removeEventListener('touchmove', this.onTouchMove, false);
        layer.removeEventListener('touchend', this.onTouchEnd, false);
        layer.removeEventListener('touchcancel', this.onTouchCancel, false);
    };


    /**
     * Check whether FastClick is needed.
     *
     * @param {Element} layer The layer to listen on
     */
    FastClick.notNeeded = function(layer) {
        var metaViewport;
        var chromeVersion;
        var blackberryVersion;
        var firefoxVersion;

        // Devices that don't support touch don't need FastClick
        if (typeof window.ontouchstart === 'undefined') {
            return true;
        }

        // Chrome version - zero for other browsers
        chromeVersion = +(/Chrome\/([0-9]+)/.exec(navigator.userAgent) || [,0])[1];

        if (chromeVersion) {

            if (deviceIsAndroid) {
                metaViewport = document.querySelector('meta[name=viewport]');

                if (metaViewport) {
                    // Chrome on Android with user-scalable="no" doesn't need FastClick (issue #89)
                    if (metaViewport.content.indexOf('user-scalable=no') !== -1) {
                        return true;
                    }
                    // Chrome 32 and above with width=device-width or less don't need FastClick
                    if (chromeVersion > 31 && document.documentElement.scrollWidth <= window.outerWidth) {
                        return true;
                    }
                }

                // Chrome desktop doesn't need FastClick (issue #15)
            } else {
                return true;
            }
        }

        if (deviceIsBlackBerry10) {
            blackberryVersion = navigator.userAgent.match(/Version\/([0-9]*)\.([0-9]*)/);

            // BlackBerry 10.3+ does not require Fastclick library.
            // https://github.com/ftlabs/fastclick/issues/251
            if (blackberryVersion[1] >= 10 && blackberryVersion[2] >= 3) {
                metaViewport = document.querySelector('meta[name=viewport]');

                if (metaViewport) {
                    // user-scalable=no eliminates click delay.
                    if (metaViewport.content.indexOf('user-scalable=no') !== -1) {
                        return true;
                    }
                    // width=device-width (or less than device-width) eliminates click delay.
                    if (document.documentElement.scrollWidth <= window.outerWidth) {
                        return true;
                    }
                }
            }
        }

        // IE10 with -ms-touch-action: none or manipulation, which disables double-tap-to-zoom (issue #97)
        if (layer.style.msTouchAction === 'none' || layer.style.touchAction === 'manipulation') {
            return true;
        }

        // Firefox version - zero for other browsers
        firefoxVersion = +(/Firefox\/([0-9]+)/.exec(navigator.userAgent) || [,0])[1];

        if (firefoxVersion >= 27) {
            // Firefox 27+ does not have tap delay if the content is not zoomable - https://bugzilla.mozilla.org/show_bug.cgi?id=922896

            metaViewport = document.querySelector('meta[name=viewport]');
            if (metaViewport && (metaViewport.content.indexOf('user-scalable=no') !== -1 || document.documentElement.scrollWidth <= window.outerWidth)) {
                return true;
            }
        }

        // IE11: prefixed -ms-touch-action is no longer supported and it's recomended to use non-prefixed version
        // http://msdn.microsoft.com/en-us/library/windows/apps/Hh767313.aspx
        if (layer.style.touchAction === 'none' || layer.style.touchAction === 'manipulation') {
            return true;
        }

        return false;
    };


    /**
     * Factory method for creating a FastClick object
     *
     * @param {Element} layer The layer to listen on
     * @param {Object} [options={}] The options to override the defaults
     */
    FastClick.attach = function(layer, options) {
        return new FastClick(layer, options);
    };

    window.FastClick = FastClick;
}());

/*======================================================
************   Modals   ************
======================================================*/
/*jshint unused: false*/
+function ($) {
    "use strict";
    var _modalTemplateTempDiv = document.createElement('div');

    $.modalStack = [];

    $.modalStackClearQueue = function () {
        if ($.modalStack.length) {
            ($.modalStack.shift())();
        }
    };
    $.modal = function (params) {
        params = params || {};
        var modalHTML = '';
        var buttonsHTML = '';
        if (params.buttons && params.buttons.length > 0) {
            for (var i = 0; i < params.buttons.length; i++) {
                buttonsHTML += '<span class="modal-button' + (params.buttons[i].bold ? ' modal-button-bold' : '') + '">' + params.buttons[i].text + '</span>';
            }
        }
        var extraClass = params.extraClass || '';
        var titleHTML = params.title ? '<div class="modal-title">' + params.title + '</div>' : '';
        var textHTML = params.text ? '<div class="modal-text">' + params.text + '</div>' : '';
        var afterTextHTML = params.afterText ? params.afterText : '';
        var noButtons = !params.buttons || params.buttons.length === 0 ? 'modal-no-buttons' : '';
        var verticalButtons = params.verticalButtons ? 'modal-buttons-vertical' : '';
        modalHTML = '<div class="modal ' + extraClass + ' ' + noButtons + '"><div class="modal-inner">' + (titleHTML + textHTML + afterTextHTML) + '</div><div class="modal-buttons ' + verticalButtons + '">' + buttonsHTML + '</div></div>';

        _modalTemplateTempDiv.innerHTML = modalHTML;

        var modal = $(_modalTemplateTempDiv).children();

        $(defaults.modalContainer).append(modal[0]);

        // Add events on buttons
        modal.find('.modal-button').each(function (index, el) {
            $(el).on('click', function (e) {
                if (params.buttons[index].close !== false) $.closeModal(modal);
                if (params.buttons[index].onClick) params.buttons[index].onClick(modal, e);
                if (params.onClick) params.onClick(modal, index);
            });
        });
        $.openModal(modal);
        return modal[0];
    };
    $.alert = function (text, title, callbackOk) {
        if (typeof title === 'function') {
            callbackOk = arguments[1];
            title = undefined;
        }
        return $.modal({
            text: text || '',
            title: typeof title === 'undefined' ? defaults.modalTitle : title,
            buttons: [ {text: defaults.modalButtonOk, bold: true, onClick: callbackOk} ]
        });
    };
    $.confirm = function (text, title, callbackOk, callbackCancel) {
        if (typeof title === 'function') {
            callbackCancel = arguments[2];
            callbackOk = arguments[1];
            title = undefined;
        }
        return $.modal({
            text: text || '',
            title: typeof title === 'undefined' ? defaults.modalTitle : title,
            buttons: [
                {text: defaults.modalButtonCancel, onClick: callbackCancel},
                {text: defaults.modalButtonOk, bold: true, onClick: callbackOk}
            ]
        });
    };
    $.prompt = function (text, title, callbackOk, callbackCancel) {
        if (typeof title === 'function') {
            callbackCancel = arguments[2];
            callbackOk = arguments[1];
            title = undefined;
        }
        return $.modal({
            text: text || '',
            title: typeof title === 'undefined' ? defaults.modalTitle : title,
            afterText: '<input type="text" class="modal-text-input">',
            buttons: [
                {
                    text: defaults.modalButtonCancel
                },
                {
                    text: defaults.modalButtonOk,
                    bold: true
                }
            ],
            onClick: function (modal, index) {
                if (index === 0 && callbackCancel) callbackCancel($(modal).find('.modal-text-input').val());
                if (index === 1 && callbackOk) callbackOk($(modal).find('.modal-text-input').val());
            }
        });
    };
    $.modalLogin = function (text, title, callbackOk, callbackCancel) {
        if (typeof title === 'function') {
            callbackCancel = arguments[2];
            callbackOk = arguments[1];
            title = undefined;
        }
        return $.modal({
            text: text || '',
            title: typeof title === 'undefined' ? defaults.modalTitle : title,
            afterText: '<input type="text" name="modal-username" placeholder="' + defaults.modalUsernamePlaceholder + '" class="modal-text-input modal-text-input-double"><input type="password" name="modal-password" placeholder="' + defaults.modalPasswordPlaceholder + '" class="modal-text-input modal-text-input-double">',
            buttons: [
                {
                    text: defaults.modalButtonCancel
                },
                {
                    text: defaults.modalButtonOk,
                    bold: true
                }
            ],
            onClick: function (modal, index) {
                var username = $(modal).find('.modal-text-input[name="modal-username"]').val();
                var password = $(modal).find('.modal-text-input[name="modal-password"]').val();
                if (index === 0 && callbackCancel) callbackCancel(username, password);
                if (index === 1 && callbackOk) callbackOk(username, password);
            }
        });
    };
    $.modalPassword = function (text, title, callbackOk, callbackCancel) {
        if (typeof title === 'function') {
            callbackCancel = arguments[2];
            callbackOk = arguments[1];
            title = undefined;
        }
        return $.modal({
            text: text || '',
            title: typeof title === 'undefined' ? defaults.modalTitle : title,
            afterText: '<input type="password" name="modal-password" placeholder="' + defaults.modalPasswordPlaceholder + '" class="modal-text-input">',
            buttons: [
                {
                    text: defaults.modalButtonCancel
                },
                {
                    text: defaults.modalButtonOk,
                    bold: true
                }
            ],
            onClick: function (modal, index) {
                var password = $(modal).find('.modal-text-input[name="modal-password"]').val();
                if (index === 0 && callbackCancel) callbackCancel(password);
                if (index === 1 && callbackOk) callbackOk(password);
            }
        });
    };
    $.showPreloader = function (title) {
        $.hidePreloader();
        $.showPreloader.preloaderModal = $.modal({
            title: title || defaults.modalPreloaderTitle,
            text: '<div class="preloader"></div>'
        });

        return $.showPreloader.preloaderModal;
    };
    $.hidePreloader = function () {
        $.showPreloader.preloaderModal && $.closeModal($.showPreloader.preloaderModal);
    };
    $.showIndicator = function () {
        if ($('.preloader-indicator-modal')[0]) return;
        $(defaults.modalContainer).append('<div class="preloader-indicator-overlay"></div><div class="preloader-indicator-modal"><span class="preloader preloader-white"></span></div>');
    };
    $.hideIndicator = function () {
        $('.preloader-indicator-overlay, .preloader-indicator-modal').remove();
    };
    // Action Sheet
    $.actions = function (params) {
        var modal, groupSelector, buttonSelector;
        params = params || [];

        if (params.length > 0 && !$.isArray(params[0])) {
            params = [params];
        }
        var modalHTML;
        var buttonsHTML = '';
        for (var i = 0; i < params.length; i++) {
            for (var j = 0; j < params[i].length; j++) {
                if (j === 0) buttonsHTML += '<div class="actions-modal-group">';
                var button = params[i][j];
                var buttonClass = button.label ? 'actions-modal-label' : 'actions-modal-button';
                if (button.bold) buttonClass += ' actions-modal-button-bold';
                if (button.color) buttonClass += ' color-' + button.color;
                if (button.bg) buttonClass += ' bg-' + button.bg;
                if (button.disabled) buttonClass += ' disabled';
                buttonsHTML += '<span class="' + buttonClass + '">' + button.text + '</span>';
                if (j === params[i].length - 1) buttonsHTML += '</div>';
            }
        }
        modalHTML = '<div class="actions-modal">' + buttonsHTML + '</div>';
        _modalTemplateTempDiv.innerHTML = modalHTML;
        modal = $(_modalTemplateTempDiv).children();
        $(defaults.modalContainer).append(modal[0]);
        groupSelector = '.actions-modal-group';
        buttonSelector = '.actions-modal-button';

        var groups = modal.find(groupSelector);
        groups.each(function (index, el) {
            var groupIndex = index;
            $(el).children().each(function (index, el) {
                var buttonIndex = index;
                var buttonParams = params[groupIndex][buttonIndex];
                var clickTarget;
                if ($(el).is(buttonSelector)) clickTarget = $(el);
                // if (toPopover && $(el).find(buttonSelector).length > 0) clickTarget = $(el).find(buttonSelector);

                if (clickTarget) {
                    clickTarget.on('click', function (e) {
                        if (buttonParams.close !== false) $.closeModal(modal);
                        if (buttonParams.onClick) buttonParams.onClick(modal, e);
                    });
                }
            });
        });
        $.openModal(modal);
        return modal[0];
    };
    $.popup = function (modal, removeOnClose) {
        if (typeof removeOnClose === 'undefined') removeOnClose = true;
        if (typeof modal === 'string' && modal.indexOf('<') >= 0) {
            var _modal = document.createElement('div');
            _modal.innerHTML = modal.trim();
            if (_modal.childNodes.length > 0) {
                modal = _modal.childNodes[0];
                if (removeOnClose) modal.classList.add('remove-on-close');
                $(defaults.modalContainer).append(modal);
            }
            else return false; //nothing found
        }
        modal = $(modal);
        if (modal.length === 0) return false;
        modal.show();
        modal.find(".content").scroller("refresh");
        if (modal.find('.' + defaults.viewClass).length > 0) {
            $.sizeNavbars(modal.find('.' + defaults.viewClass)[0]);
        }
        $.openModal(modal);

        return modal[0];
    };
    $.pickerModal = function (pickerModal, removeOnClose) {
        if (typeof removeOnClose === 'undefined') removeOnClose = true;
        if (typeof pickerModal === 'string' && pickerModal.indexOf('<') >= 0) {
            pickerModal = $(pickerModal);
            if (pickerModal.length > 0) {
                if (removeOnClose) pickerModal.addClass('remove-on-close');
                $(defaults.modalContainer).append(pickerModal[0]);
            }
            else return false; //nothing found
        }
        pickerModal = $(pickerModal);
        if (pickerModal.length === 0) return false;
        pickerModal.show();
        $.openModal(pickerModal);
        return pickerModal[0];
    };
    $.loginScreen = function (modal) {
        if (!modal) modal = '.login-screen';
        modal = $(modal);
        if (modal.length === 0) return false;
        modal.show();
        if (modal.find('.' + defaults.viewClass).length > 0) {
            $.sizeNavbars(modal.find('.' + defaults.viewClass)[0]);
        }
        $.openModal(modal);
        return modal[0];
    };
    //显示一个消息，会在2秒钟后自动消失
    $.toast = function(msg, duration, extraclass) {
        var $toast = $('<div class="modal toast ' + (extraclass || '') + '">' + msg + '</div>').appendTo(document.body);
        $.openModal($toast, function(){
            setTimeout(function() {
                $.closeModal($toast);
            }, duration || 2000);
        });
    };
    $.openModal = function (modal, cb) {
        modal = $(modal);
        var isModal = modal.hasClass('modal'),
            isNotToast = !modal.hasClass('toast');
        if ($('.modal.modal-in:not(.modal-out)').length && defaults.modalStack && isModal && isNotToast) {
            $.modalStack.push(function () {
                $.openModal(modal, cb);
            });
            return;
        }
        var isPopup = modal.hasClass('popup');
        var isLoginScreen = modal.hasClass('login-screen');
        var isPickerModal = modal.hasClass('picker-modal');
        var isToast = modal.hasClass('toast');
        if (isModal) {
            modal.show();
            modal.css({
                marginTop: - Math.round(modal.outerHeight() / 2) + 'px'
            });
        }
        if (isToast) {
            modal.css({
                marginLeft: - Math.round(modal.outerWidth() / 2 / 1.185) + 'px' //1.185 是初始化时候的放大效果
            });
        }

        var overlay;
        if (!isLoginScreen && !isPickerModal && !isToast) {
            if ($('.modal-overlay').length === 0 && !isPopup) {
                $(defaults.modalContainer).append('<div class="modal-overlay"></div>');
            }
            if ($('.popup-overlay').length === 0 && isPopup) {
                $(defaults.modalContainer).append('<div class="popup-overlay"></div>');
            }
            overlay = isPopup ? $('.popup-overlay') : $('.modal-overlay');
        }

        //Make sure that styles are applied, trigger relayout;
        var clientLeft = modal[0].clientLeft;

        // Trugger open event
        modal.trigger('open');

        // Picker modal body class
        if (isPickerModal) {
            $(defaults.modalContainer).addClass('with-picker-modal');
        }

        // Classes for transition in
        if (!isLoginScreen && !isPickerModal && !isToast) overlay.addClass('modal-overlay-visible');
        modal.removeClass('modal-out').addClass('modal-in').transitionEnd(function (e) {
            if (modal.hasClass('modal-out')) modal.trigger('closed');
            else modal.trigger('opened');
        });
        // excute callback
        if (typeof cb === 'function') {
          cb.call(this);
        }
        return true;
    };
    $.closeModal = function (modal) {
        modal = $(modal || '.modal-in');
        if (typeof modal !== 'undefined' && modal.length === 0) {
            return;
        }
        var isModal = modal.hasClass('modal'),
            isPopup = modal.hasClass('popup'),
            isToast = modal.hasClass('toast'),
            isLoginScreen = modal.hasClass('login-screen'),
            isPickerModal = modal.hasClass('picker-modal'),
            removeOnClose = modal.hasClass('remove-on-close'),
            overlay = isPopup ? $('.popup-overlay') : $('.modal-overlay');
        if (isPopup){
            if (modal.length === $('.popup.modal-in').length) {
                overlay.removeClass('modal-overlay-visible');
            }
        }
        else if (!(isPickerModal || isToast)) {
            overlay.removeClass('modal-overlay-visible');
        }

        modal.trigger('close');

        // Picker modal body class
        if (isPickerModal) {
            $(defaults.modalContainer).removeClass('with-picker-modal');
            $(defaults.modalContainer).addClass('picker-modal-closing');
        }

        modal.removeClass('modal-in').addClass('modal-out').transitionEnd(function (e) {
            if (modal.hasClass('modal-out')) modal.trigger('closed');
            else modal.trigger('opened');

            if (isPickerModal) {
                $(defaults.modalContainer).removeClass('picker-modal-closing');
            }
            if (isPopup || isLoginScreen || isPickerModal) {
                modal.removeClass('modal-out').hide();
                if (removeOnClose && modal.length > 0) {
                    modal.remove();
                }
            }
            else {
                modal.remove();
            }
        });
        if (isModal &&  defaults.modalStack ) {
            $.modalStackClearQueue();
        }

        return true;
    };
    function handleClicks(e) {
        /*jshint validthis:true */
        var clicked = $(this);
        var url = clicked.attr('href');


        //Collect Clicked data- attributes
        var clickedData = clicked.dataset();

        // Popup
        var popup;
        if (clicked.hasClass('open-popup')) {
            if (clickedData.popup) {
                popup = clickedData.popup;
            }
            else popup = '.popup';
            $.popup(popup);
        }
        if (clicked.hasClass('close-popup')) {
            if (clickedData.popup) {
                popup = clickedData.popup;
            }
            else popup = '.popup.modal-in';
            $.closeModal(popup);
        }

        // Close Modal
        if (clicked.hasClass('modal-overlay')) {
            if ($('.modal.modal-in').length > 0 && defaults.modalCloseByOutside)
                $.closeModal('.modal.modal-in');
            if ($('.actions-modal.modal-in').length > 0 && defaults.actionsCloseByOutside)
                $.closeModal('.actions-modal.modal-in');

        }
        if (clicked.hasClass('popup-overlay')) {
            if ($('.popup.modal-in').length > 0 && defaults.popupCloseByOutside)
                $.closeModal('.popup.modal-in');
        }




    }
    $(document).on('click', ' .modal-overlay, .popup-overlay, .close-popup, .open-popup, .close-picker', handleClicks);
    var defaults =  $.modal.prototype.defaults  = {
        modalStack: true,
        modalButtonOk: '确定',
        modalButtonCancel: '取消',
        modalPreloaderTitle: '加载中',
        modalContainer : document.body
    };
}(Zepto);

/*======================================================
************   Calendar   ************
======================================================*/
/*jshint unused: false*/
+function ($) {
    "use strict";
    var rtl = false;
    var Calendar = function (params) {
        var p = this;
        var defaults = {
            monthNames: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月' , '九月' , '十月', '十一月', '十二月'],
            monthNamesShort: ['一月', '二月', '三月', '四月', '五月', '六月', '七月', '八月' , '九月' , '十月', '十一月', '十二月'],
            dayNames: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
            dayNamesShort: ['周日', '周一', '周二', '周三', '周四', '周五', '周六'],
            firstDay: 1, // First day of the week, Monday
            weekendDays: [0, 6], // Sunday and Saturday
            multiple: false,
            dateFormat: 'yyyy-mm-dd',
            direction: 'horizontal', // or 'vertical'
            minDate: null,
            maxDate: null,
            touchMove: true,
            animate: true,
            closeOnSelect: true,
            monthPicker: true,
            monthPickerTemplate:
                '<div class="picker-calendar-month-picker">' +
                '<a href="#" class="link icon-only picker-calendar-prev-month"><i class="icon icon-prev"></i></a>' +
                '<div class="current-month-value"></div>' +
                '<a href="#" class="link icon-only picker-calendar-next-month"><i class="icon icon-next"></i></a>' +
                '</div>',
            yearPicker: true,
            yearPickerTemplate:
                '<div class="picker-calendar-year-picker">' +
                '<a href="#" class="link icon-only picker-calendar-prev-year"><i class="icon icon-prev"></i></a>' +
                '<span class="current-year-value"></span>' +
                '<a href="#" class="link icon-only picker-calendar-next-year"><i class="icon icon-next"></i></a>' +
                '</div>',
            weekHeader: true,
            // Common settings
            scrollToInput: true,
            inputReadOnly: true,
            toolbar: true,
            toolbarCloseText: 'Done',
            toolbarTemplate:
                '<div class="toolbar">' +
                '<div class="toolbar-inner">' +
                '{{monthPicker}}' +
                '{{yearPicker}}' +
                // '<a href="#" class="link close-picker">{{closeText}}</a>' +
                '</div>' +
                '</div>',
            /* Callbacks
               onMonthAdd
               onChange
               onOpen
               onClose
               onDayClick
               onMonthYearChangeStart
               onMonthYearChangeEnd
               */
        };
        params = params || {};
        for (var def in defaults) {
            if (typeof params[def] === 'undefined') {
                params[def] = defaults[def];
            }
        }
        p.params = params;
        p.initialized = false;

        // Inline flag
        p.inline = p.params.container ? true : false;

        // Is horizontal
        p.isH = p.params.direction === 'horizontal';

        // RTL inverter
        var inverter = p.isH ? (rtl ? -1 : 1) : 1;

        // Animating flag
        p.animating = false;

        // Format date
        function formatDate(date) {
            date = new Date(date);
            var year = date.getFullYear();
            var month = date.getMonth();
            var month1 = month + 1;
            var day = date.getDate();
            var weekDay = date.getDay();
            return p.params.dateFormat
                .replace(/yyyy/g, year)
                .replace(/yy/g, (year + '').substring(2))
                .replace(/mm/g, month1 < 10 ? '0' + month1 : month1)
                .replace(/m/g, month1)
                .replace(/MM/g, p.params.monthNames[month])
                .replace(/M/g, p.params.monthNamesShort[month])
                .replace(/dd/g, day < 10 ? '0' + day : day)
                .replace(/d/g, day)
                .replace(/DD/g, p.params.dayNames[weekDay])
                .replace(/D/g, p.params.dayNamesShort[weekDay]);
        }


        // Value
        p.addValue = function (value) {
            if (p.params.multiple) {
                if (!p.value) p.value = [];
                var inValuesIndex;
                for (var i = 0; i < p.value.length; i++) {
                    if (new Date(value).getTime() === new Date(p.value[i]).getTime()) {
                        inValuesIndex = i;
                    }
                }
                if (typeof inValuesIndex === 'undefined') {
                    p.value.push(value);
                }
                else {
                    p.value.splice(inValuesIndex, 1);
                }
                p.updateValue();
            }
            else {
                p.value = [value];
                p.updateValue();
            }
        };
        p.setValue = function (arrValues) {
            p.value = arrValues;
            p.updateValue();
        };
        p.updateValue = function () {
            p.wrapper.find('.picker-calendar-day-selected').removeClass('picker-calendar-day-selected');
            var i, inputValue;
            for (i = 0; i < p.value.length; i++) {
                var valueDate = new Date(p.value[i]);
                p.wrapper.find('.picker-calendar-day[data-date="' + valueDate.getFullYear() + '-' + valueDate.getMonth() + '-' + valueDate.getDate() + '"]').addClass('picker-calendar-day-selected');
            }
            if (p.params.onChange) {
                p.params.onChange(p, p.value, p.value.map(formatDate));
            }
            if (p.input && p.input.length > 0) {
                if (p.params.formatValue) inputValue = p.params.formatValue(p, p.value);
                else {
                    inputValue = [];
                    for (i = 0; i < p.value.length; i++) {
                        inputValue.push(formatDate(p.value[i]));
                    }
                    inputValue = inputValue.join(', ');
                }
                $(p.input).val(inputValue);
                $(p.input).trigger('change');
            }
        };

        // Columns Handlers
        p.initCalendarEvents = function () {
            var col;
            var allowItemClick = true;
            var isTouched, isMoved, touchStartX, touchStartY, touchCurrentX, touchCurrentY, touchStartTime, touchEndTime, startTranslate, currentTranslate, wrapperWidth, wrapperHeight, percentage, touchesDiff, isScrolling;
            function handleTouchStart (e) {
                if (isMoved || isTouched) return;
                // e.preventDefault();
                isTouched = true;
                touchStartX = touchCurrentY = e.type === 'touchstart' ? e.targetTouches[0].pageX : e.pageX;
                touchStartY = touchCurrentY = e.type === 'touchstart' ? e.targetTouches[0].pageY : e.pageY;
                touchStartTime = (new Date()).getTime();
                percentage = 0;
                allowItemClick = true;
                isScrolling = undefined;
                startTranslate = currentTranslate = p.monthsTranslate;
            }
            function handleTouchMove (e) {
                if (!isTouched) return;

                touchCurrentX = e.type === 'touchmove' ? e.targetTouches[0].pageX : e.pageX;
                touchCurrentY = e.type === 'touchmove' ? e.targetTouches[0].pageY : e.pageY;
                if (typeof isScrolling === 'undefined') {
                    isScrolling = !!(isScrolling || Math.abs(touchCurrentY - touchStartY) > Math.abs(touchCurrentX - touchStartX));
                }
                if (p.isH && isScrolling) {
                    isTouched = false;
                    return;
                }
                e.preventDefault();
                if (p.animating) {
                    isTouched = false;
                    return;
                }
                allowItemClick = false;
                if (!isMoved) {
                    // First move
                    isMoved = true;
                    wrapperWidth = p.wrapper[0].offsetWidth;
                    wrapperHeight = p.wrapper[0].offsetHeight;
                    p.wrapper.transition(0);
                }
                e.preventDefault();

                touchesDiff = p.isH ? touchCurrentX - touchStartX : touchCurrentY - touchStartY;
                percentage = touchesDiff/(p.isH ? wrapperWidth : wrapperHeight);
                currentTranslate = (p.monthsTranslate * inverter + percentage) * 100;

                // Transform wrapper
                p.wrapper.transform('translate3d(' + (p.isH ? currentTranslate : 0) + '%, ' + (p.isH ? 0 : currentTranslate) + '%, 0)');

            }
            function handleTouchEnd (e) {
                if (!isTouched || !isMoved) {
                    isTouched = isMoved = false;
                    return;
                }
                isTouched = isMoved = false;

                touchEndTime = new Date().getTime();
                if (touchEndTime - touchStartTime < 300) {
                    if (Math.abs(touchesDiff) < 10) {
                        p.resetMonth();
                    }
                    else if (touchesDiff >= 10) {
                        if (rtl) p.nextMonth();
                        else p.prevMonth();
                    }
                    else {
                        if (rtl) p.prevMonth();
                        else p.nextMonth();
                    }
                }
                else {
                    if (percentage <= -0.5) {
                        if (rtl) p.prevMonth();
                        else p.nextMonth();
                    }
                    else if (percentage >= 0.5) {
                        if (rtl) p.nextMonth();
                        else p.prevMonth();
                    }
                    else {
                        p.resetMonth();
                    }
                }

                // Allow click
                setTimeout(function () {
                    allowItemClick = true;
                }, 100);
            }

            function handleDayClick(e) {
                if (!allowItemClick) return;
                var day = $(e.target).parents('.picker-calendar-day');
                if (day.length === 0 && $(e.target).hasClass('picker-calendar-day')) {
                    day = $(e.target);
                }
                if (day.length === 0) return;
                if (day.hasClass('picker-calendar-day-selected') && !p.params.multiple) return;
                if (day.hasClass('picker-calendar-day-disabled')) return;
                if (day.hasClass('picker-calendar-day-next')) p.nextMonth();
                if (day.hasClass('picker-calendar-day-prev')) p.prevMonth();
                var dateYear = day.attr('data-year');
                var dateMonth = day.attr('data-month');
                var dateDay = day.attr('data-day');
                if (p.params.onDayClick) {
                    p.params.onDayClick(p, day[0], dateYear, dateMonth, dateDay);
                }
                p.addValue(new Date(dateYear, dateMonth, dateDay).getTime());
                if (p.params.closeOnSelect) p.close();
            }

            p.container.find('.picker-calendar-prev-month').on('click', p.prevMonth);
            p.container.find('.picker-calendar-next-month').on('click', p.nextMonth);
            p.container.find('.picker-calendar-prev-year').on('click', p.prevYear);
            p.container.find('.picker-calendar-next-year').on('click', p.nextYear);
            p.wrapper.on('click', handleDayClick);
            if (p.params.touchMove) {
                p.wrapper.on($.touchEvents.start, handleTouchStart);
                p.wrapper.on($.touchEvents.move, handleTouchMove);
                p.wrapper.on($.touchEvents.end, handleTouchEnd);
            }

            p.container[0].f7DestroyCalendarEvents = function () {
                p.container.find('.picker-calendar-prev-month').off('click', p.prevMonth);
                p.container.find('.picker-calendar-next-month').off('click', p.nextMonth);
                p.container.find('.picker-calendar-prev-year').off('click', p.prevYear);
                p.container.find('.picker-calendar-next-year').off('click', p.nextYear);
                p.wrapper.off('click', handleDayClick);
                if (p.params.touchMove) {
                    p.wrapper.off($.touchEvents.start, handleTouchStart);
                    p.wrapper.off($.touchEvents.move, handleTouchMove);
                    p.wrapper.off($.touchEvents.end, handleTouchEnd);
                }
            };


        };
        p.destroyCalendarEvents = function (colContainer) {
            if ('f7DestroyCalendarEvents' in p.container[0]) p.container[0].f7DestroyCalendarEvents();
        };

        // Calendar Methods
        p.daysInMonth = function (date) {
            var d = new Date(date);
            return new Date(d.getFullYear(), d.getMonth() + 1, 0).getDate();
        };
        p.monthHTML = function (date, offset) {
            date = new Date(date);
            var year = date.getFullYear(),
                month = date.getMonth(),
                day = date.getDate();
            if (offset === 'next') {
                if (month === 11) date = new Date(year + 1, 0);
                else date = new Date(year, month + 1, 1);
            }
            if (offset === 'prev') {
                if (month === 0) date = new Date(year - 1, 11);
                else date = new Date(year, month - 1, 1);
            }
            if (offset === 'next' || offset === 'prev') {
                month = date.getMonth();
                year = date.getFullYear();
            }
            var daysInPrevMonth = p.daysInMonth(new Date(date.getFullYear(), date.getMonth()).getTime() - 10 * 24 * 60 * 60 * 1000),
                daysInMonth = p.daysInMonth(date),
                firstDayOfMonthIndex = new Date(date.getFullYear(), date.getMonth()).getDay();
            if (firstDayOfMonthIndex === 0) firstDayOfMonthIndex = 7;

            var dayDate, currentValues = [], i, j,
                rows = 6, cols = 7,
                monthHTML = '',
                dayIndex = 0 + (p.params.firstDay - 1),
                today = new Date().setHours(0,0,0,0),
                minDate = p.params.minDate ? new Date(p.params.minDate).getTime() : null,
                maxDate = p.params.maxDate ? new Date(p.params.maxDate).getTime() : null;

            if (p.value && p.value.length) {
                for (i = 0; i < p.value.length; i++) {
                    currentValues.push(new Date(p.value[i]).setHours(0,0,0,0));
                }
            }

            for (i = 1; i <= rows; i++) {
                var rowHTML = '';
                var row = i;
                for (j = 1; j <= cols; j++) {
                    var col = j;
                    dayIndex ++;
                    var dayNumber = dayIndex - firstDayOfMonthIndex;
                    var addClass = '';
                    if (dayNumber < 0) {
                        dayNumber = daysInPrevMonth + dayNumber + 1;
                        addClass += ' picker-calendar-day-prev';
                        dayDate = new Date(month - 1 < 0 ? year - 1 : year, month - 1 < 0 ? 11 : month - 1, dayNumber).getTime();
                    }
                    else {
                        dayNumber = dayNumber + 1;
                        if (dayNumber > daysInMonth) {
                            dayNumber = dayNumber - daysInMonth;
                            addClass += ' picker-calendar-day-next';
                            dayDate = new Date(month + 1 > 11 ? year + 1 : year, month + 1 > 11 ? 0 : month + 1, dayNumber).getTime();
                        }
                        else {
                            dayDate = new Date(year, month, dayNumber).getTime();
                        }
                    }
                    // Today
                    if (dayDate === today) addClass += ' picker-calendar-day-today';
                    // Selected
                    if (currentValues.indexOf(dayDate) >= 0) addClass += ' picker-calendar-day-selected';
                    // Weekend
                    if (p.params.weekendDays.indexOf(col - 1) >= 0) {
                        addClass += ' picker-calendar-day-weekend';
                    }
                    // Disabled
                    if ((minDate && dayDate < minDate) || (maxDate && dayDate > maxDate)) {
                        addClass += ' picker-calendar-day-disabled';
                    }

                    dayDate = new Date(dayDate);
                    var dayYear = dayDate.getFullYear();
                    var dayMonth = dayDate.getMonth();
                    rowHTML += '<div data-year="' + dayYear + '" data-month="' + dayMonth + '" data-day="' + dayNumber + '" class="picker-calendar-day' + (addClass) + '" data-date="' + (dayYear + '-' + dayMonth + '-' + dayNumber) + '"><span>'+dayNumber+'</span></div>';
                }
                monthHTML += '<div class="picker-calendar-row">' + rowHTML + '</div>';
            }
            monthHTML = '<div class="picker-calendar-month" data-year="' + year + '" data-month="' + month + '">' + monthHTML + '</div>';
            return monthHTML;
        };
        p.animating = false;
        p.updateCurrentMonthYear = function (dir) {
            if (typeof dir === 'undefined') {
                p.currentMonth = parseInt(p.months.eq(1).attr('data-month'), 10);
                p.currentYear = parseInt(p.months.eq(1).attr('data-year'), 10);
            }
            else {
                p.currentMonth = parseInt(p.months.eq(dir === 'next' ? (p.months.length - 1) : 0).attr('data-month'), 10);
                p.currentYear = parseInt(p.months.eq(dir === 'next' ? (p.months.length - 1) : 0).attr('data-year'), 10);
            }
            p.container.find('.current-month-value').text(p.params.monthNames[p.currentMonth]);
            p.container.find('.current-year-value').text(p.currentYear);

        };
        p.onMonthChangeStart = function (dir) {
            p.updateCurrentMonthYear(dir);
            p.months.removeClass('picker-calendar-month-current picker-calendar-month-prev picker-calendar-month-next');
            var currentIndex = dir === 'next' ? p.months.length - 1 : 0;

            p.months.eq(currentIndex).addClass('picker-calendar-month-current');
            p.months.eq(dir === 'next' ? currentIndex - 1 : currentIndex + 1).addClass(dir === 'next' ? 'picker-calendar-month-prev' : 'picker-calendar-month-next');

            if (p.params.onMonthYearChangeStart) {
                p.params.onMonthYearChangeStart(p, p.currentYear, p.currentMonth);
            }
        };
        p.onMonthChangeEnd = function (dir, rebuildBoth) {
            p.animating = false;
            var nextMonthHTML, prevMonthHTML, newMonthHTML;
            p.wrapper.find('.picker-calendar-month:not(.picker-calendar-month-prev):not(.picker-calendar-month-current):not(.picker-calendar-month-next)').remove();

            if (typeof dir === 'undefined') {
                dir = 'next';
                rebuildBoth = true;
            }
            if (!rebuildBoth) {
                newMonthHTML = p.monthHTML(new Date(p.currentYear, p.currentMonth), dir);
            }
            else {
                p.wrapper.find('.picker-calendar-month-next, .picker-calendar-month-prev').remove();
                prevMonthHTML = p.monthHTML(new Date(p.currentYear, p.currentMonth), 'prev');
                nextMonthHTML = p.monthHTML(new Date(p.currentYear, p.currentMonth), 'next');
            }
            if (dir === 'next' || rebuildBoth) {
                p.wrapper.append(newMonthHTML || nextMonthHTML);
            }
            if (dir === 'prev' || rebuildBoth) {
                p.wrapper.prepend(newMonthHTML || prevMonthHTML);
            }
            p.months = p.wrapper.find('.picker-calendar-month');
            p.setMonthsTranslate(p.monthsTranslate);
            if (p.params.onMonthAdd) {
                p.params.onMonthAdd(p, dir === 'next' ? p.months.eq(p.months.length - 1)[0] : p.months.eq(0)[0]);
            }
            if (p.params.onMonthYearChangeEnd) {
                p.params.onMonthYearChangeEnd(p, p.currentYear, p.currentMonth);
            }
        };
        p.setMonthsTranslate = function (translate) {
            translate = translate || p.monthsTranslate || 0;
            if (typeof p.monthsTranslate === 'undefined') p.monthsTranslate = translate;
            p.months.removeClass('picker-calendar-month-current picker-calendar-month-prev picker-calendar-month-next');
            var prevMonthTranslate = -(translate + 1) * 100 * inverter;
            var currentMonthTranslate = -translate * 100 * inverter;
            var nextMonthTranslate = -(translate - 1) * 100 * inverter;
            p.months.eq(0).transform('translate3d(' + (p.isH ? prevMonthTranslate : 0) + '%, ' + (p.isH ? 0 : prevMonthTranslate) + '%, 0)').addClass('picker-calendar-month-prev');
            p.months.eq(1).transform('translate3d(' + (p.isH ? currentMonthTranslate : 0) + '%, ' + (p.isH ? 0 : currentMonthTranslate) + '%, 0)').addClass('picker-calendar-month-current');
            p.months.eq(2).transform('translate3d(' + (p.isH ? nextMonthTranslate : 0) + '%, ' + (p.isH ? 0 : nextMonthTranslate) + '%, 0)').addClass('picker-calendar-month-next');
        };
        p.nextMonth = function (transition) {
            if (typeof transition === 'undefined' || typeof transition === 'object') {
                transition = '';
                if (!p.params.animate) transition = 0;
            }
            var nextMonth = parseInt(p.months.eq(p.months.length - 1).attr('data-month'), 10);
            var nextYear = parseInt(p.months.eq(p.months.length - 1).attr('data-year'), 10);
            var nextDate = new Date(nextYear, nextMonth);
            var nextDateTime = nextDate.getTime();
            var transitionEndCallback = p.animating ? false : true;
            if (p.params.maxDate) {
                if (nextDateTime > new Date(p.params.maxDate).getTime()) {
                    return p.resetMonth();
                }
            }
            p.monthsTranslate --;
            if (nextMonth === p.currentMonth) {
                var nextMonthTranslate = -(p.monthsTranslate) * 100 * inverter;
                var nextMonthHTML = $(p.monthHTML(nextDateTime, 'next')).transform('translate3d(' + (p.isH ? nextMonthTranslate : 0) + '%, ' + (p.isH ? 0 : nextMonthTranslate) + '%, 0)').addClass('picker-calendar-month-next');
                p.wrapper.append(nextMonthHTML[0]);
                p.months = p.wrapper.find('.picker-calendar-month');
                if (p.params.onMonthAdd) {
                    p.params.onMonthAdd(p, p.months.eq(p.months.length - 1)[0]);
                }
            }
            p.animating = true;
            p.onMonthChangeStart('next');
            var translate = (p.monthsTranslate * 100) * inverter;

            p.wrapper.transition(transition).transform('translate3d(' + (p.isH ? translate : 0) + '%, ' + (p.isH ? 0 : translate) + '%, 0)');
            if (transitionEndCallback) {
                p.wrapper.transitionEnd(function () {
                    p.onMonthChangeEnd('next');
                });
            }
            if (!p.params.animate) {
                p.onMonthChangeEnd('next');
            }
        };
        p.prevMonth = function (transition) {
            if (typeof transition === 'undefined' || typeof transition === 'object') {
                transition = '';
                if (!p.params.animate) transition = 0;
            }
            var prevMonth = parseInt(p.months.eq(0).attr('data-month'), 10);
            var prevYear = parseInt(p.months.eq(0).attr('data-year'), 10);
            var prevDate = new Date(prevYear, prevMonth + 1, -1);
            var prevDateTime = prevDate.getTime();
            var transitionEndCallback = p.animating ? false : true;
            if (p.params.minDate) {
                if (prevDateTime < new Date(p.params.minDate).getTime()) {
                    return p.resetMonth();
                }
            }
            p.monthsTranslate ++;
            if (prevMonth === p.currentMonth) {
                var prevMonthTranslate = -(p.monthsTranslate) * 100 * inverter;
                var prevMonthHTML = $(p.monthHTML(prevDateTime, 'prev')).transform('translate3d(' + (p.isH ? prevMonthTranslate : 0) + '%, ' + (p.isH ? 0 : prevMonthTranslate) + '%, 0)').addClass('picker-calendar-month-prev');
                p.wrapper.prepend(prevMonthHTML[0]);
                p.months = p.wrapper.find('.picker-calendar-month');
                if (p.params.onMonthAdd) {
                    p.params.onMonthAdd(p, p.months.eq(0)[0]);
                }
            }
            p.animating = true;
            p.onMonthChangeStart('prev');
            var translate = (p.monthsTranslate * 100) * inverter;
            p.wrapper.transition(transition).transform('translate3d(' + (p.isH ? translate : 0) + '%, ' + (p.isH ? 0 : translate) + '%, 0)');
            if (transitionEndCallback) {
                p.wrapper.transitionEnd(function () {
                    p.onMonthChangeEnd('prev');
                });
            }
            if (!p.params.animate) {
                p.onMonthChangeEnd('prev');
            }
        };
        p.resetMonth = function (transition) {
            if (typeof transition === 'undefined') transition = '';
            var translate = (p.monthsTranslate * 100) * inverter;
            p.wrapper.transition(transition).transform('translate3d(' + (p.isH ? translate : 0) + '%, ' + (p.isH ? 0 : translate) + '%, 0)');
        };
        p.setYearMonth = function (year, month, transition) {
            if (typeof year === 'undefined') year = p.currentYear;
            if (typeof month === 'undefined') month = p.currentMonth;
            if (typeof transition === 'undefined' || typeof transition === 'object') {
                transition = '';
                if (!p.params.animate) transition = 0;
            }
            var targetDate;
            if (year < p.currentYear) {
                targetDate = new Date(year, month + 1, -1).getTime();
            }
            else {
                targetDate = new Date(year, month).getTime();
            }
            if (p.params.maxDate && targetDate > new Date(p.params.maxDate).getTime()) {
                return false;
            }
            if (p.params.minDate && targetDate < new Date(p.params.minDate).getTime()) {
                return false;
            }
            var currentDate = new Date(p.currentYear, p.currentMonth).getTime();
            var dir = targetDate > currentDate ? 'next' : 'prev';
            var newMonthHTML = p.monthHTML(new Date(year, month));
            p.monthsTranslate = p.monthsTranslate || 0;
            var prevTranslate = p.monthsTranslate;
            var monthTranslate, wrapperTranslate;
            var transitionEndCallback = p.animating ? false : true;
            if (targetDate > currentDate) {
                // To next
                p.monthsTranslate --;
                if (!p.animating) p.months.eq(p.months.length - 1).remove();
                p.wrapper.append(newMonthHTML);
                p.months = p.wrapper.find('.picker-calendar-month');
                monthTranslate = -(prevTranslate - 1) * 100 * inverter;
                p.months.eq(p.months.length - 1).transform('translate3d(' + (p.isH ? monthTranslate : 0) + '%, ' + (p.isH ? 0 : monthTranslate) + '%, 0)').addClass('picker-calendar-month-next');
            }
            else {
                // To prev
                p.monthsTranslate ++;
                if (!p.animating) p.months.eq(0).remove();
                p.wrapper.prepend(newMonthHTML);
                p.months = p.wrapper.find('.picker-calendar-month');
                monthTranslate = -(prevTranslate + 1) * 100 * inverter;
                p.months.eq(0).transform('translate3d(' + (p.isH ? monthTranslate : 0) + '%, ' + (p.isH ? 0 : monthTranslate) + '%, 0)').addClass('picker-calendar-month-prev');
            }
            if (p.params.onMonthAdd) {
                p.params.onMonthAdd(p, dir === 'next' ? p.months.eq(p.months.length - 1)[0] : p.months.eq(0)[0]);
            }
            p.animating = true;
            p.onMonthChangeStart(dir);
            wrapperTranslate = (p.monthsTranslate * 100) * inverter;
            p.wrapper.transition(transition).transform('translate3d(' + (p.isH ? wrapperTranslate : 0) + '%, ' + (p.isH ? 0 : wrapperTranslate) + '%, 0)');
            if (transitionEndCallback) {
                p.wrapper.transitionEnd(function () {
                    p.onMonthChangeEnd(dir, true);
                });
            }
            if (!p.params.animate) {
                p.onMonthChangeEnd(dir);
            }
        };
        p.nextYear = function () {
            p.setYearMonth(p.currentYear + 1);
        };
        p.prevYear = function () {
            p.setYearMonth(p.currentYear - 1);
        };


        // HTML Layout
        p.layout = function () {
            var pickerHTML = '';
            var pickerClass = '';
            var i;

            var layoutDate = p.value && p.value.length ? p.value[0] : new Date().setHours(0,0,0,0);
            var prevMonthHTML = p.monthHTML(layoutDate, 'prev');
            var currentMonthHTML = p.monthHTML(layoutDate);
            var nextMonthHTML = p.monthHTML(layoutDate, 'next');
            var monthsHTML = '<div class="picker-calendar-months"><div class="picker-calendar-months-wrapper">' + (prevMonthHTML + currentMonthHTML + nextMonthHTML) + '</div></div>';
            // Week days header
            var weekHeaderHTML = '';
            if (p.params.weekHeader) {
                for (i = 0; i < 7; i++) {
                    var weekDayIndex = (i + p.params.firstDay > 6) ? (i - 7 + p.params.firstDay) : (i + p.params.firstDay);
                    var dayName = p.params.dayNamesShort[weekDayIndex];
                    weekHeaderHTML += '<div class="picker-calendar-week-day ' + ((p.params.weekendDays.indexOf(weekDayIndex) >= 0) ? 'picker-calendar-week-day-weekend' : '') + '"> ' + dayName + '</div>';

                }
                weekHeaderHTML = '<div class="picker-calendar-week-days">' + weekHeaderHTML + '</div>';
            }
            pickerClass = 'picker-modal picker-calendar ' + (p.params.cssClass || '');
            var toolbarHTML = p.params.toolbar ? p.params.toolbarTemplate.replace(/{{closeText}}/g, p.params.toolbarCloseText) : '';
            if (p.params.toolbar) {
                toolbarHTML = p.params.toolbarTemplate
                    .replace(/{{closeText}}/g, p.params.toolbarCloseText)
                    .replace(/{{monthPicker}}/g, (p.params.monthPicker ? p.params.monthPickerTemplate : ''))
                    .replace(/{{yearPicker}}/g, (p.params.yearPicker ? p.params.yearPickerTemplate : ''));
            }

            pickerHTML =
                '<div class="' + (pickerClass) + '">' +
                toolbarHTML +
                '<div class="picker-modal-inner">' +
                weekHeaderHTML +
                monthsHTML +
                '</div>' +
                '</div>';


            p.pickerHTML = pickerHTML;
        };

        // Input Events
        function openOnInput(e) {
            e.preventDefault();
            // 安卓微信webviewreadonly的input依然弹出软键盘问题修复
            if ($.device.isWeixin && $.device.android && p.params.inputReadOnly) {
                /*jshint validthis:true */
                this.focus();
                this.blur();
            }
            if (p.opened) return;
            p.open();
            if (p.params.scrollToInput) {
                var pageContent = p.input.parents('.content');
                if (pageContent.length === 0) return;

                var paddingTop = parseInt(pageContent.css('padding-top'), 10),
                    paddingBottom = parseInt(pageContent.css('padding-bottom'), 10),
                    pageHeight = pageContent[0].offsetHeight - paddingTop - p.container.height(),
                    pageScrollHeight = pageContent[0].scrollHeight - paddingTop - p.container.height(),
                    newPaddingBottom;

                var inputTop = p.input.offset().top - paddingTop + p.input[0].offsetHeight;
                if (inputTop > pageHeight) {
                    var scrollTop = pageContent.scrollTop() + inputTop - pageHeight;
                    if (scrollTop + pageHeight > pageScrollHeight) {
                        newPaddingBottom = scrollTop + pageHeight - pageScrollHeight + paddingBottom;
                        if (pageHeight === pageScrollHeight) {
                            newPaddingBottom = p.container.height();
                        }
                        pageContent.css({'padding-bottom': (newPaddingBottom) + 'px'});
                    }
                    pageContent.scrollTop(scrollTop, 300);
                }
            }
        }
        function closeOnHTMLClick(e) {
            if (p.input && p.input.length > 0) {
                if (e.target !== p.input[0] && $(e.target).parents('.picker-modal').length === 0) p.close();
            }
            else {
                if ($(e.target).parents('.picker-modal').length === 0) p.close();
            }
        }

        if (p.params.input) {
            p.input = $(p.params.input);
            if (p.input.length > 0) {
                if (p.params.inputReadOnly) p.input.prop('readOnly', true);
                if (!p.inline) {
                    p.input.on('click', openOnInput);
                }
            }

        }

        if (!p.inline) $('html').on('click', closeOnHTMLClick);

        // Open
        function onPickerClose() {
            p.opened = false;
            if (p.input && p.input.length > 0) p.input.parents('.content').css({'padding-bottom': ''});
            if (p.params.onClose) p.params.onClose(p);

            // Destroy events
            p.destroyCalendarEvents();
        }

        p.opened = false;
        p.open = function () {
            var updateValue = false;
            if (!p.opened) {
                // Set date value
                if (!p.value) {
                    if (p.params.value) {
                        p.value = p.params.value;
                        updateValue = true;
                    }
                }

                // Layout
                p.layout();

                // Append
                if (p.inline) {
                    p.container = $(p.pickerHTML);
                    p.container.addClass('picker-modal-inline');
                    $(p.params.container).append(p.container);
                }
                else {
                    p.container = $($.pickerModal(p.pickerHTML));
                    $(p.container)
                        .on('close', function () {
                            onPickerClose();
                        });
                }

                // Store calendar instance
                p.container[0].f7Calendar = p;
                p.wrapper = p.container.find('.picker-calendar-months-wrapper');

                // Months
                p.months = p.wrapper.find('.picker-calendar-month');

                // Update current month and year
                p.updateCurrentMonthYear();

                // Set initial translate
                p.monthsTranslate = 0;
                p.setMonthsTranslate();

                // Init events
                p.initCalendarEvents();

                // Update input value
                if (updateValue) p.updateValue();

            }

            // Set flag
            p.opened = true;
            p.initialized = true;
            if (p.params.onMonthAdd) {
                p.months.each(function () {
                    p.params.onMonthAdd(p, this);
                });
            }
            if (p.params.onOpen) p.params.onOpen(p);
        };

        // Close
        p.close = function () {
            if (!p.opened || p.inline) return;
            $.closeModal(p.container);
            return;
        };

        // Destroy
        p.destroy = function () {
            p.close();
            if (p.params.input && p.input.length > 0) {
                p.input.off('click', openOnInput);
            }
            $('html').off('click', closeOnHTMLClick);
        };

        if (p.inline) {
            p.open();
        }

        return p;
    };
    $.fn.calendar = function (params) {
        return this.each(function() {
            var $this = $(this);
            if(!$this[0]) return;
            var p = {};
            if($this[0].tagName.toUpperCase() === "INPUT") {
                p.input = $this;
            } else {
                p.container = $this;
            }
            new Calendar($.extend(p, params));
        });
    };

    $.initCalendar = function(content) {
        var $content = content ? $(content) : $(document.body);
        $content.find("[data-toggle='date']").each(function() {
            $(this).calendar();
        });
    };
}(Zepto);

/*======================================================
************   Picker   ************
======================================================*/
/* jshint unused:false */
/* jshint multistr:true */
+ function($) {
    "use strict";
    var Picker = function (params) {
        var p = this;
        var defaults = {
            updateValuesOnMomentum: false,
            updateValuesOnTouchmove: true,
            rotateEffect: false,
            momentumRatio: 7,
            freeMode: false,
            // Common settings
            scrollToInput: true,
            inputReadOnly: true,
            toolbar: true,
            toolbarCloseText: '确定',
            toolbarTemplate: '<header class="bar bar-nav">\
                <button class="button button-link pull-right close-picker">确定</button>\
                <h1 class="title">请选择</h1>\
                </header>',
        };
        params = params || {};
        for (var def in defaults) {
            if (typeof params[def] === 'undefined') {
                params[def] = defaults[def];
            }
        }
        p.params = params;
        p.cols = [];
        p.initialized = false;

        // Inline flag
        p.inline = p.params.container ? true : false;

        // 3D Transforms origin bug, only on safari
        var originBug = $.device.ios || (navigator.userAgent.toLowerCase().indexOf('safari') >= 0 && navigator.userAgent.toLowerCase().indexOf('chrome') < 0) && !$.device.android;

        // Value
        p.setValue = function (arrValues, transition) {
            var valueIndex = 0;
            for (var i = 0; i < p.cols.length; i++) {
                if (p.cols[i] && !p.cols[i].divider) {
                    p.cols[i].setValue(arrValues[valueIndex], transition);
                    valueIndex++;
                }
            }
        };
        p.updateValue = function () {
            var newValue = [];
            var newDisplayValue = [];
            for (var i = 0; i < p.cols.length; i++) {
                if (!p.cols[i].divider) {
                    newValue.push(p.cols[i].value);
                    newDisplayValue.push(p.cols[i].displayValue);
                }
            }
            if (newValue.indexOf(undefined) >= 0) {
                return;
            }
            p.value = newValue;
            p.displayValue = newDisplayValue;
            if (p.params.onChange) {
                p.params.onChange(p, p.value, p.displayValue);
            }
            if (p.input && p.input.length > 0) {
                $(p.input).val(p.params.formatValue ? p.params.formatValue(p, p.value, p.displayValue) : p.value.join(' '));
                $(p.input).trigger('change');
            }
        };

        // Columns Handlers
        p.initPickerCol = function (colElement, updateItems) {
            var colContainer = $(colElement);
            var colIndex = colContainer.index();
            var col = p.cols[colIndex];
            if (col.divider) return;
            col.container = colContainer;
            col.wrapper = col.container.find('.picker-items-col-wrapper');
            col.items = col.wrapper.find('.picker-item');

            var i, j;
            var wrapperHeight, itemHeight, itemsHeight, minTranslate, maxTranslate;
            col.replaceValues = function (values, displayValues) {
                col.destroyEvents();
                col.values = values;
                col.displayValues = displayValues;
                var newItemsHTML = p.columnHTML(col, true);
                col.wrapper.html(newItemsHTML);
                col.items = col.wrapper.find('.picker-item');
                col.calcSize();
                col.setValue(col.values[0], 0, true);
                col.initEvents();
            };
            col.calcSize = function () {
                if (p.params.rotateEffect) {
                    col.container.removeClass('picker-items-col-absolute');
                    if (!col.width) col.container.css({width:''});
                }
                var colWidth, colHeight;
                colWidth = 0;
                colHeight = col.container[0].offsetHeight;
                wrapperHeight = col.wrapper[0].offsetHeight;
                itemHeight = col.items[0].offsetHeight;
                itemsHeight = itemHeight * col.items.length;
                minTranslate = colHeight / 2 - itemsHeight + itemHeight / 2;
                maxTranslate = colHeight / 2 - itemHeight / 2;
                if (col.width) {
                    colWidth = col.width;
                    if (parseInt(colWidth, 10) === colWidth) colWidth = colWidth + 'px';
                    col.container.css({width: colWidth});
                }
                if (p.params.rotateEffect) {
                    if (!col.width) {
                        col.items.each(function () {
                            var item = $(this);
                            item.css({width:'auto'});
                            colWidth = Math.max(colWidth, item[0].offsetWidth);
                            item.css({width:''});
                        });
                        col.container.css({width: (colWidth + 2) + 'px'});
                    }
                    col.container.addClass('picker-items-col-absolute');
                }
            };
            col.calcSize();

            col.wrapper.transform('translate3d(0,' + maxTranslate + 'px,0)').transition(0);


            var activeIndex = 0;
            var animationFrameId;

            // Set Value Function
            col.setValue = function (newValue, transition, valueCallbacks) {
                if (typeof transition === 'undefined') transition = '';
                var newActiveIndex = col.wrapper.find('.picker-item[data-picker-value="' + newValue + '"]').index();
                if(typeof newActiveIndex === 'undefined' || newActiveIndex === -1) {
                    return;
                }
                var newTranslate = -newActiveIndex * itemHeight + maxTranslate;
                // Update wrapper
                col.wrapper.transition(transition);
                col.wrapper.transform('translate3d(0,' + (newTranslate) + 'px,0)');

                // Watch items
                if (p.params.updateValuesOnMomentum && col.activeIndex && col.activeIndex !== newActiveIndex ) {
                    $.cancelAnimationFrame(animationFrameId);
                    col.wrapper.transitionEnd(function(){
                        $.cancelAnimationFrame(animationFrameId);
                    });
                    updateDuringScroll();
                }

                // Update items
                col.updateItems(newActiveIndex, newTranslate, transition, valueCallbacks);
            };

            col.updateItems = function (activeIndex, translate, transition, valueCallbacks) {
                if (typeof translate === 'undefined') {
                    translate = $.getTranslate(col.wrapper[0], 'y');
                }
                if(typeof activeIndex === 'undefined') activeIndex = -Math.round((translate - maxTranslate)/itemHeight);
                if (activeIndex < 0) activeIndex = 0;
                if (activeIndex >= col.items.length) activeIndex = col.items.length - 1;
                var previousActiveIndex = col.activeIndex;
                col.activeIndex = activeIndex;
                /*
                   col.wrapper.find('.picker-selected, .picker-after-selected, .picker-before-selected').removeClass('picker-selected picker-after-selected picker-before-selected');

                   col.items.transition(transition);
                   var selectedItem = col.items.eq(activeIndex).addClass('picker-selected').transform('');
                   var prevItems = selectedItem.prevAll().addClass('picker-before-selected');
                   var nextItems = selectedItem.nextAll().addClass('picker-after-selected');
                   */
                //去掉 .picker-after-selected, .picker-before-selected 以提高性能
                col.wrapper.find('.picker-selected').removeClass('picker-selected');
                if (p.params.rotateEffect) {
                    col.items.transition(transition);
                }
                var selectedItem = col.items.eq(activeIndex).addClass('picker-selected').transform('');

                if (valueCallbacks || typeof valueCallbacks === 'undefined') {
                    // Update values
                    col.value = selectedItem.attr('data-picker-value');
                    col.displayValue = col.displayValues ? col.displayValues[activeIndex] : col.value;
                    // On change callback
                    if (previousActiveIndex !== activeIndex) {
                        if (col.onChange) {
                            col.onChange(p, col.value, col.displayValue);
                        }
                        p.updateValue();
                    }
                }

                // Set 3D rotate effect
                if (!p.params.rotateEffect) {
                    return;
                }
                var percentage = (translate - (Math.floor((translate - maxTranslate)/itemHeight) * itemHeight + maxTranslate)) / itemHeight;

                col.items.each(function () {
                    var item = $(this);
                    var itemOffsetTop = item.index() * itemHeight;
                    var translateOffset = maxTranslate - translate;
                    var itemOffset = itemOffsetTop - translateOffset;
                    var percentage = itemOffset / itemHeight;

                    var itemsFit = Math.ceil(col.height / itemHeight / 2) + 1;

                    var angle = (-18*percentage);
                    if (angle > 180) angle = 180;
                    if (angle < -180) angle = -180;
                    // Far class
                    if (Math.abs(percentage) > itemsFit) item.addClass('picker-item-far');
                    else item.removeClass('picker-item-far');
                    // Set transform
                    item.transform('translate3d(0, ' + (-translate + maxTranslate) + 'px, ' + (originBug ? -110 : 0) + 'px) rotateX(' + angle + 'deg)');
                });
            };

            function updateDuringScroll() {
                animationFrameId = $.requestAnimationFrame(function () {
                    col.updateItems(undefined, undefined, 0);
                    updateDuringScroll();
                });
            }

            // Update items on init
            if (updateItems) col.updateItems(0, maxTranslate, 0);

            var allowItemClick = true;
            var isTouched, isMoved, touchStartY, touchCurrentY, touchStartTime, touchEndTime, startTranslate, returnTo, currentTranslate, prevTranslate, velocityTranslate, velocityTime;
            function handleTouchStart (e) {
                if (isMoved || isTouched) return;
                e.preventDefault();
                isTouched = true;
                touchStartY = touchCurrentY = e.type === 'touchstart' ? e.targetTouches[0].pageY : e.pageY;
                touchStartTime = (new Date()).getTime();

                allowItemClick = true;
                startTranslate = currentTranslate = $.getTranslate(col.wrapper[0], 'y');
            }
            function handleTouchMove (e) {
                if (!isTouched) return;
                e.preventDefault();
                allowItemClick = false;
                touchCurrentY = e.type === 'touchmove' ? e.targetTouches[0].pageY : e.pageY;
                if (!isMoved) {
                    // First move
                    $.cancelAnimationFrame(animationFrameId);
                    isMoved = true;
                    startTranslate = currentTranslate = $.getTranslate(col.wrapper[0], 'y');
                    col.wrapper.transition(0);
                }
                e.preventDefault();

                var diff = touchCurrentY - touchStartY;
                currentTranslate = startTranslate + diff;
                returnTo = undefined;

                // Normalize translate
                if (currentTranslate < minTranslate) {
                    currentTranslate = minTranslate - Math.pow(minTranslate - currentTranslate, 0.8);
                    returnTo = 'min';
                }
                if (currentTranslate > maxTranslate) {
                    currentTranslate = maxTranslate + Math.pow(currentTranslate - maxTranslate, 0.8);
                    returnTo = 'max';
                }
                // Transform wrapper
                col.wrapper.transform('translate3d(0,' + currentTranslate + 'px,0)');

                // Update items
                col.updateItems(undefined, currentTranslate, 0, p.params.updateValuesOnTouchmove);

                // Calc velocity
                velocityTranslate = currentTranslate - prevTranslate || currentTranslate;
                velocityTime = (new Date()).getTime();
                prevTranslate = currentTranslate;
            }
            function handleTouchEnd (e) {
                if (!isTouched || !isMoved) {
                    isTouched = isMoved = false;
                    return;
                }
                isTouched = isMoved = false;
                col.wrapper.transition('');
                if (returnTo) {
                    if (returnTo === 'min') {
                        col.wrapper.transform('translate3d(0,' + minTranslate + 'px,0)');
                    }
                    else col.wrapper.transform('translate3d(0,' + maxTranslate + 'px,0)');
                }
                touchEndTime = new Date().getTime();
                var velocity, newTranslate;
                if (touchEndTime - touchStartTime > 300) {
                    newTranslate = currentTranslate;
                }
                else {
                    velocity = Math.abs(velocityTranslate / (touchEndTime - velocityTime));
                    newTranslate = currentTranslate + velocityTranslate * p.params.momentumRatio;
                }

                newTranslate = Math.max(Math.min(newTranslate, maxTranslate), minTranslate);

                // Active Index
                var activeIndex = -Math.floor((newTranslate - maxTranslate)/itemHeight);

                // Normalize translate
                if (!p.params.freeMode) newTranslate = -activeIndex * itemHeight + maxTranslate;

                // Transform wrapper
                col.wrapper.transform('translate3d(0,' + (parseInt(newTranslate,10)) + 'px,0)');

                // Update items
                col.updateItems(activeIndex, newTranslate, '', true);

                // Watch items
                if (p.params.updateValuesOnMomentum) {
                    updateDuringScroll();
                    col.wrapper.transitionEnd(function(){
                        $.cancelAnimationFrame(animationFrameId);
                    });
                }

                // Allow click
                setTimeout(function () {
                    allowItemClick = true;
                }, 100);
            }

            function handleClick(e) {
                if (!allowItemClick) return;
                $.cancelAnimationFrame(animationFrameId);
                /*jshint validthis:true */
                var value = $(this).attr('data-picker-value');
                col.setValue(value);
            }

            col.initEvents = function (detach) {
                var method = detach ? 'off' : 'on';
                col.container[method]($.touchEvents.start, handleTouchStart);
                col.container[method]($.touchEvents.move, handleTouchMove);
                col.container[method]($.touchEvents.end, handleTouchEnd);
                col.items[method]('click', handleClick);
            };
            col.destroyEvents = function () {
                col.initEvents(true);
            };

            col.container[0].f7DestroyPickerCol = function () {
                col.destroyEvents();
            };

            col.initEvents();

        };
        p.destroyPickerCol = function (colContainer) {
            colContainer = $(colContainer);
            if ('f7DestroyPickerCol' in colContainer[0]) colContainer[0].f7DestroyPickerCol();
        };
        // Resize cols
        function resizeCols() {
            if (!p.opened) return;
            for (var i = 0; i < p.cols.length; i++) {
                if (!p.cols[i].divider) {
                    p.cols[i].calcSize();
                    p.cols[i].setValue(p.cols[i].value, 0, false);
                }
            }
        }
        $(window).on('resize', resizeCols);

        // HTML Layout
        p.columnHTML = function (col, onlyItems) {
            var columnItemsHTML = '';
            var columnHTML = '';
            if (col.divider) {
                columnHTML += '<div class="picker-items-col picker-items-col-divider ' + (col.textAlign ? 'picker-items-col-' + col.textAlign : '') + ' ' + (col.cssClass || '') + '">' + col.content + '</div>';
            }
            else {
                for (var j = 0; j < col.values.length; j++) {
                    columnItemsHTML += '<div class="picker-item" data-picker-value="' + col.values[j] + '">' + (col.displayValues ? col.displayValues[j] : col.values[j]) + '</div>';
                }

                columnHTML += '<div class="picker-items-col ' + (col.textAlign ? 'picker-items-col-' + col.textAlign : '') + ' ' + (col.cssClass || '') + '"><div class="picker-items-col-wrapper">' + columnItemsHTML + '</div></div>';
            }
            return onlyItems ? columnItemsHTML : columnHTML;
        };
        p.layout = function () {
            var pickerHTML = '';
            var pickerClass = '';
            var i;
            p.cols = [];
            var colsHTML = '';
            for (i = 0; i < p.params.cols.length; i++) {
                var col = p.params.cols[i];
                colsHTML += p.columnHTML(p.params.cols[i]);
                p.cols.push(col);
            }
            pickerClass = 'picker-modal picker-columns ' + (p.params.cssClass || '') + (p.params.rotateEffect ? ' picker-3d' : '');
            pickerHTML =
                '<div class="' + (pickerClass) + '">' +
                (p.params.toolbar ? p.params.toolbarTemplate.replace(/{{closeText}}/g, p.params.toolbarCloseText) : '') +
                '<div class="picker-modal-inner picker-items">' +
                colsHTML +
                '<div class="picker-center-highlight"></div>' +
                '</div>' +
                '</div>';

            p.pickerHTML = pickerHTML;
        };

        // Input Events
        function openOnInput(e) {
            e.preventDefault();
            // 安卓微信webviewreadonly的input依然弹出软键盘问题修复
            if ($.device.isWeixin && $.device.android && p.params.inputReadOnly) {
                /*jshint validthis:true */
                this.focus();
                this.blur();
            }
            if (p.opened) return;
            p.open();
            if (p.params.scrollToInput) {
                var pageContent = p.input.parents('.content');
                if (pageContent.length === 0) return;

                var paddingTop = parseInt(pageContent.css('padding-top'), 10),
                    paddingBottom = parseInt(pageContent.css('padding-bottom'), 10),
                    pageHeight = pageContent[0].offsetHeight - paddingTop - p.container.height(),
                    pageScrollHeight = pageContent[0].scrollHeight - paddingTop - p.container.height(),
                    newPaddingBottom;
                var inputTop = p.input.offset().top - paddingTop + p.input[0].offsetHeight;
                if (inputTop > pageHeight) {
                    var scrollTop = pageContent.scrollTop() + inputTop - pageHeight;
                    if (scrollTop + pageHeight > pageScrollHeight) {
                        newPaddingBottom = scrollTop + pageHeight - pageScrollHeight + paddingBottom;
                        if (pageHeight === pageScrollHeight) {
                            newPaddingBottom = p.container.height();
                        }
                        pageContent.css({'padding-bottom': (newPaddingBottom) + 'px'});
                    }
                    pageContent.scrollTop(scrollTop, 300);
                }
            }
        }
        function closeOnHTMLClick(e) {
            if (!p.opened) return;
            if (p.input && p.input.length > 0) {
                if (e.target !== p.input[0] && $(e.target).parents('.picker-modal').length === 0) p.close();
            }
            else {
                if ($(e.target).parents('.picker-modal').length === 0) p.close();
            }
        }

        if (p.params.input) {
            p.input = $(p.params.input);
            if (p.input.length > 0) {
                if (p.params.inputReadOnly) p.input.prop('readOnly', true);
                if (!p.inline) {
                    p.input.on('click', openOnInput);
                }
            }
        }

        if (!p.inline) $('html').on('click', closeOnHTMLClick);

        // Open
        function onPickerClose() {
            p.opened = false;
            if (p.input && p.input.length > 0) p.input.parents('.content').css({'padding-bottom': ''});
            if (p.params.onClose) p.params.onClose(p);

            // Destroy events
            p.container.find('.picker-items-col').each(function () {
                p.destroyPickerCol(this);
            });
        }

        p.opened = false;
        p.open = function () {
            if (!p.opened) {

                // Layout
                p.layout();

                // Append
                if (p.inline) {
                    p.container = $(p.pickerHTML);
                    p.container.addClass('picker-modal-inline');
                    $(p.params.container).append(p.container);
                    p.opened = true;
                }
                else {
                    p.container = $($.pickerModal(p.pickerHTML));
                    $(p.container)
                        .one('opened', function() {
                            p.opened = true;
                        })
                        .on('close', function () {
                            onPickerClose();
                        });
                }

                // Store picker instance
                p.container[0].f7Picker = p;

                // Init Events
                p.container.find('.picker-items-col').each(function () {
                    var updateItems = true;
                    if ((!p.initialized && p.params.value) || (p.initialized && p.value)) updateItems = false;
                    p.initPickerCol(this, updateItems);
                });

                // Set value
                if (!p.initialized) {
                    if (p.params.value) {
                        p.setValue(p.params.value, 0);
                    }
                }
                else {
                    if (p.value) p.setValue(p.value, 0);
                }
            }

            // Set flag
            p.initialized = true;

            if (p.params.onOpen) p.params.onOpen(p);
        };

        // Close
        p.close = function () {
            if (!p.opened || p.inline) return;
            $.closeModal(p.container);
            return;
        };

        // Destroy
        p.destroy = function () {
            p.close();
            if (p.params.input && p.input.length > 0) {
                p.input.off('click', openOnInput);
            }
            $('html').off('click', closeOnHTMLClick);
            $(window).off('resize', resizeCols);
        };

        if (p.inline) {
            p.open();
        }

        return p;
    };

    $(document).on("click", ".close-picker", function() {
        var pickerToClose = $('.picker-modal.modal-in');
        $.closeModal(pickerToClose);
    });

    $.fn.picker = function(params) {
        var args = arguments;
        return this.each(function() {
            if(!this) return;
            var $this = $(this);

            var picker = $this.data("picker");
            if(!picker) {
                var p = $.extend({
                    input: this,
                    value: $this.val() ? $this.val().split(' ') : ''
                }, params);
                picker = new Picker(p);
                $this.data("picker", picker);
            }
            if(typeof params === typeof "a") {
                picker[params].apply(picker, Array.prototype.slice.call(args, 1));
            }
        });
    };
}(Zepto);

/* jshint unused:false*/

+ function($) {
    "use strict";

    var today = new Date();

    var getDays = function(max) {
        var days = [];
        for(var i=1; i<= (max||31);i++) {
            days.push(i < 10 ? "0"+i : i);
        }
        return days;
    };

    var getDaysByMonthAndYear = function(month, year) {
        var int_d = new Date(year, parseInt(month)+1-1, 1);
        var d = new Date(int_d - 1);
        return getDays(d.getDate());
    };

    var formatNumber = function (n) {
        return n < 10 ? "0" + n : n;
    };

    var initMonthes = ('01 02 03 04 05 06 07 08 09 10 11 12').split(' ');

    var initYears = (function () {
        var arr = [];
        for (var i = 1950; i <= 2030; i++) { arr.push(i); }
        return arr;
    })();


    var defaults = {

        rotateEffect: false,  //为了性能

        value: [today.getFullYear(), formatNumber(today.getMonth()+1), formatNumber(today.getDate()), today.getHours(), formatNumber(today.getMinutes())],

        onChange: function (picker, values, displayValues) {
            var days = getDaysByMonthAndYear(picker.cols[1].value, picker.cols[0].value);
            var currentValue = picker.cols[2].value;
            if(currentValue > days.length) currentValue = days.length;
            picker.cols[2].setValue(currentValue);
        },

        formatValue: function (p, values, displayValues) {
            return displayValues[0] + '-' + values[1] + '-' + values[2] + ' ' + values[3] + ':' + values[4];
        },

        cols: [
            // Years
        {
            values: initYears
        },
        // Months
        {
            values: initMonthes
        },
        // Days
        {
            values: getDays()
        },

        // Space divider
        {
            divider: true,
            content: '  '
        },
        // Hours
        {
            values: (function () {
                var arr = [];
                for (var i = 0; i <= 23; i++) { arr.push(i); }
                return arr;
            })(),
        },
        // Divider
        {
            divider: true,
            content: ':'
        },
        // Minutes
        {
            values: (function () {
                var arr = [];
                for (var i = 0; i <= 59; i++) { arr.push(i < 10 ? '0' + i : i); }
                return arr;
            })(),
        }
        ]
    };

    $.fn.datetimePicker = function(params) {
        return this.each(function() {
            if(!this) return;
            var p = $.extend(defaults, params);
            $(this).picker(p);
            if (params.value) $(this).val(p.formatValue(p, p.value, p.value));
        });
    };

}(Zepto);

+ function(window) {

    "use strict";

    var rAF = window.requestAnimationFrame ||
        window.webkitRequestAnimationFrame ||
        window.mozRequestAnimationFrame ||
        window.oRequestAnimationFrame ||
        window.msRequestAnimationFrame ||
        function(callback) {
            window.setTimeout(callback, 1000 / 60);
        };
    /*var cRAF = window.cancelRequestAnimationFrame ||
        window.webkitCancelRequestAnimationFrame ||
        window.mozCancelRequestAnimationFrame ||
        window.oCancelRequestAnimationFrame ||
        window.msCancelRequestAnimationFrame;*/

    var utils = (function() {
        var me = {};

        var _elementStyle = document.createElement('div').style;
        var _vendor = (function() {
            var vendors = ['t', 'webkitT', 'MozT', 'msT', 'OT'],
                transform,
                i = 0,
                l = vendors.length;

            for (; i < l; i++) {
                transform = vendors[i] + 'ransform';
                if (transform in _elementStyle) return vendors[i].substr(0, vendors[i].length - 1);
            }

            return false;
        })();

        function _prefixStyle(style) {
            if (_vendor === false) return false;
            if (_vendor === '') return style;
            return _vendor + style.charAt(0).toUpperCase() + style.substr(1);
        }

        me.getTime = Date.now || function getTime() {
            return new Date().getTime();
        };

        me.extend = function(target, obj) {
            for (var i in obj) {  // jshint ignore:line
                    target[i] = obj[i]; 
            }
        };

        me.addEvent = function(el, type, fn, capture) {
            el.addEventListener(type, fn, !!capture);
        };

        me.removeEvent = function(el, type, fn, capture) {
            el.removeEventListener(type, fn, !!capture);
        };

        me.prefixPointerEvent = function(pointerEvent) {
            return window.MSPointerEvent ?
                'MSPointer' + pointerEvent.charAt(9).toUpperCase() + pointerEvent.substr(10) :
                pointerEvent;
        };

        me.momentum = function(current, start, time, lowerMargin, wrapperSize, deceleration, self) {
            var distance = current - start,
                speed = Math.abs(distance) / time,
                destination,
                duration;

            // var absDistance = Math.abs(distance);
            speed = speed / 2; //slowdown
            speed = speed > 1.5 ? 1.5 : speed; //set max speed to 1
            deceleration = deceleration === undefined ? 0.0006 : deceleration;

            destination = current + (speed * speed) / (2 * deceleration) * (distance < 0 ? -1 : 1);
            duration = speed / deceleration;

            if (destination < lowerMargin) {
                destination = wrapperSize ? lowerMargin - (wrapperSize / 2.5 * (speed / 8)) : lowerMargin;
                distance = Math.abs(destination - current);
                duration = distance / speed;
            } else if (destination > 0) {
                destination = wrapperSize ? wrapperSize / 2.5 * (speed / 8) : 0;
                distance = Math.abs(current) + destination;
                duration = distance / speed;
            }

            //simple trigger, every 50ms
            var t = +new Date();
            var l = t;

            function eventTrigger() {
                if (+new Date() - l > 50) {
                    self._execEvent('scroll');
                    l = +new Date();
                }
                if (+new Date() - t < duration) {
                    rAF(eventTrigger);
                }
            }
            rAF(eventTrigger);

            return {
                destination: Math.round(destination),
                duration: duration
            };
        };

        var _transform = _prefixStyle('transform');

        me.extend(me, {
            hasTransform: _transform !== false,
            hasPerspective: _prefixStyle('perspective') in _elementStyle,
            hasTouch: 'ontouchstart' in window,
            hasPointer: window.PointerEvent || window.MSPointerEvent, // IE10 is prefixed
            hasTransition: _prefixStyle('transition') in _elementStyle
        });

        // This should find all Android browsers lower than build 535.19 (both stock browser and webview)
        me.isBadAndroid = /Android /.test(window.navigator.appVersion) && !(/Chrome\/\d/.test(window.navigator.appVersion)) && false; //this will cause many android device scroll flash; so set it to false!

        me.extend(me.style = {}, {
            transform: _transform,
            transitionTimingFunction: _prefixStyle('transitionTimingFunction'),
            transitionDuration: _prefixStyle('transitionDuration'),
            transitionDelay: _prefixStyle('transitionDelay'),
            transformOrigin: _prefixStyle('transformOrigin')
        });

        me.hasClass = function(e, c) {
            var re = new RegExp('(^|\\s)' + c + '(\\s|$)');
            return re.test(e.className);
        };

        me.addClass = function(e, c) {
            if (me.hasClass(e, c)) {
                return;
            }

            var newclass = e.className.split(' ');
            newclass.push(c);
            e.className = newclass.join(' ');
        };

        me.removeClass = function(e, c) {
            if (!me.hasClass(e, c)) {
                return;
            }

            var re = new RegExp('(^|\\s)' + c + '(\\s|$)', 'g');
            e.className = e.className.replace(re, ' ');
        };

        me.offset = function(el) {
            var left = -el.offsetLeft,
                top = -el.offsetTop;

            // jshint -W084
            while (el = el.offsetParent) {
                left -= el.offsetLeft;
                top -= el.offsetTop;
            }
            // jshint +W084

            return {
                left: left,
                top: top
            };
        };

        me.preventDefaultException = function(el, exceptions) {
            for (var i in exceptions) {
                if (exceptions[i].test(el[i])) {
                    return true;
                }
            }

            return false;
        };

        me.extend(me.eventType = {}, {
            touchstart: 1,
            touchmove: 1,
            touchend: 1,

            mousedown: 2,
            mousemove: 2,
            mouseup: 2,

            pointerdown: 3,
            pointermove: 3,
            pointerup: 3,

            MSPointerDown: 3,
            MSPointerMove: 3,
            MSPointerUp: 3
        });

        me.extend(me.ease = {}, {
            quadratic: {
                style: 'cubic-bezier(0.25, 0.46, 0.45, 0.94)',
                fn: function(k) {
                    return k * (2 - k);
                }
            },
            circular: {
                style: 'cubic-bezier(0.1, 0.57, 0.1, 1)', // Not properly 'circular' but this looks better, it should be (0.075, 0.82, 0.165, 1)
                fn: function(k) {
                    return Math.sqrt(1 - (--k * k));
                }
            },
            back: {
                style: 'cubic-bezier(0.175, 0.885, 0.32, 1.275)',
                fn: function(k) {
                    var b = 4;
                    return (k = k - 1) * k * ((b + 1) * k + b) + 1;
                }
            },
            bounce: {
                style: '',
                fn: function(k) {
                    if ((k /= 1) < (1 / 2.75)) {
                        return 7.5625 * k * k;
                    } else if (k < (2 / 2.75)) {
                        return 7.5625 * (k -= (1.5 / 2.75)) * k + 0.75;
                    } else if (k < (2.5 / 2.75)) {
                        return 7.5625 * (k -= (2.25 / 2.75)) * k + 0.9375;
                    } else {
                        return 7.5625 * (k -= (2.625 / 2.75)) * k + 0.984375;
                    }
                }
            },
            elastic: {
                style: '',
                fn: function(k) {
                    var f = 0.22,
                        e = 0.4;

                    if (k === 0) {
                        return 0;
                    }
                    if (k === 1) {
                        return 1;
                    }

                    return (e * Math.pow(2, -10 * k) * Math.sin((k - f / 4) * (2 * Math.PI) / f) + 1);
                }
            }
        });

        me.tap = function(e, eventName) {
            var ev = document.createEvent('Event');
            ev.initEvent(eventName, true, true);
            ev.pageX = e.pageX;
            ev.pageY = e.pageY;
            e.target.dispatchEvent(ev);
        };

        me.click = function(e) {
            var target = e.target,
                ev;

            if (!(/(SELECT|INPUT|TEXTAREA)/i).test(target.tagName)) {
                ev = document.createEvent('MouseEvents');
                ev.initMouseEvent('click', true, true, e.view, 1,
                    target.screenX, target.screenY, target.clientX, target.clientY,
                    e.ctrlKey, e.altKey, e.shiftKey, e.metaKey,
                    0, null);

                ev._constructed = true;
                target.dispatchEvent(ev);
            }
        };

        return me;
    })();

    function IScroll(el, options) {
        this.wrapper = typeof el === 'string' ? document.querySelector(el) : el;
        this.scroller = $(this.wrapper).find('.content-inner')[0]; // jshint ignore:line


        this.scrollerStyle = this.scroller&&this.scroller.style; // cache style for better performance

        this.options = {

            resizeScrollbars: true,

            mouseWheelSpeed: 20,

            snapThreshold: 0.334,

            // INSERT POINT: OPTIONS 

            startX: 0,
            startY: 0,
            scrollY: true,
            directionLockThreshold: 5,
            momentum: true,

            bounce: true,
            bounceTime: 600,
            bounceEasing: '',

            preventDefault: true,
            preventDefaultException: {
                tagName: /^(INPUT|TEXTAREA|BUTTON|SELECT)$/
            },

            HWCompositing: true,
            useTransition: true,
            useTransform: true,

            //other options
            eventPassthrough: undefined, //if you  want to use native scroll, you can set to: true or horizontal
        };

        for (var i in options) {
                this.options[i] = options[i];
        }

        // Normalize options
        this.translateZ = this.options.HWCompositing && utils.hasPerspective ? ' translateZ(0)' : '';

        this.options.useTransition = utils.hasTransition && this.options.useTransition;
        this.options.useTransform = utils.hasTransform && this.options.useTransform;

        this.options.eventPassthrough = this.options.eventPassthrough === true ? 'vertical' : this.options.eventPassthrough;
        this.options.preventDefault = !this.options.eventPassthrough && this.options.preventDefault;

        // If you want eventPassthrough I have to lock one of the axes
        this.options.scrollY = this.options.eventPassthrough === 'vertical' ? false : this.options.scrollY;
        this.options.scrollX = this.options.eventPassthrough === 'horizontal' ? false : this.options.scrollX;

        // With eventPassthrough we also need lockDirection mechanism
        this.options.freeScroll = this.options.freeScroll && !this.options.eventPassthrough;
        this.options.directionLockThreshold = this.options.eventPassthrough ? 0 : this.options.directionLockThreshold;

        this.options.bounceEasing = typeof this.options.bounceEasing === 'string' ? utils.ease[this.options.bounceEasing] || utils.ease.circular : this.options.bounceEasing;

        this.options.resizePolling = this.options.resizePolling === undefined ? 60 : this.options.resizePolling;

        if (this.options.tap === true) {
            this.options.tap = 'tap';
        }

        if (this.options.shrinkScrollbars === 'scale') {
            this.options.useTransition = false;
        }

        this.options.invertWheelDirection = this.options.invertWheelDirection ? -1 : 1;

        if (this.options.probeType === 3) {
            this.options.useTransition = false;
        }

        // INSERT POINT: NORMALIZATION

        // Some defaults    
        this.x = 0;
        this.y = 0;
        this.directionX = 0;
        this.directionY = 0;
        this._events = {};

        // INSERT POINT: DEFAULTS

        this._init();
        this.refresh();

        this.scrollTo(this.options.startX, this.options.startY);
        this.enable();
    }

    IScroll.prototype = {
        version: '5.1.3',

        _init: function() {
            this._initEvents();

            if (this.options.scrollbars || this.options.indicators) {
                this._initIndicators();
            }

            if (this.options.mouseWheel) {
                this._initWheel();
            }

            if (this.options.snap) {
                this._initSnap();
            }

            if (this.options.keyBindings) {
                this._initKeys();
            }

            // INSERT POINT: _init

        },

        destroy: function() {
            this._initEvents(true);

            this._execEvent('destroy');
        },

        _transitionEnd: function(e) {
            if (e.target !== this.scroller || !this.isInTransition) {
                return;
            }

            this._transitionTime();
            if (!this.resetPosition(this.options.bounceTime)) {
                this.isInTransition = false;
                this._execEvent('scrollEnd');
            }
        },

        _start: function(e) {
            // React to left mouse button only
            if (utils.eventType[e.type] !== 1) {
                if (e.button !== 0) {
                    return;
                }
            }

            if (!this.enabled || (this.initiated && utils.eventType[e.type] !== this.initiated)) {
                return;
            }

            if (this.options.preventDefault && !utils.isBadAndroid && !utils.preventDefaultException(e.target, this.options.preventDefaultException)) {
                e.preventDefault();
            }

            var point = e.touches ? e.touches[0] : e,
                pos;

            this.initiated = utils.eventType[e.type];
            this.moved = false;
            this.distX = 0;
            this.distY = 0;
            this.directionX = 0;
            this.directionY = 0;
            this.directionLocked = 0;

            this._transitionTime();

            this.startTime = utils.getTime();

            if (this.options.useTransition && this.isInTransition) {
                this.isInTransition = false;
                pos = this.getComputedPosition();
                this._translate(Math.round(pos.x), Math.round(pos.y));
                this._execEvent('scrollEnd');
            } else if (!this.options.useTransition && this.isAnimating) {
                this.isAnimating = false;
                this._execEvent('scrollEnd');
            }

            this.startX = this.x;
            this.startY = this.y;
            this.absStartX = this.x;
            this.absStartY = this.y;
            this.pointX = point.pageX;
            this.pointY = point.pageY;

            this._execEvent('beforeScrollStart');
        },

        _move: function(e) {
            if (!this.enabled || utils.eventType[e.type] !== this.initiated) {
                return;
            }

            if (this.options.preventDefault) { // increases performance on Android? TODO: check!
                e.preventDefault();
            }

            var point = e.touches ? e.touches[0] : e,
                deltaX = point.pageX - this.pointX,
                deltaY = point.pageY - this.pointY,
                timestamp = utils.getTime(),
                newX, newY,
                absDistX, absDistY;

            this.pointX = point.pageX;
            this.pointY = point.pageY;

            this.distX += deltaX;
            this.distY += deltaY;
            absDistX = Math.abs(this.distX);
            absDistY = Math.abs(this.distY);

            // We need to move at least 10 pixels for the scrolling to initiate
            if (timestamp - this.endTime > 300 && (absDistX < 10 && absDistY < 10)) {
                return;
            }

            // If you are scrolling in one direction lock the other
            if (!this.directionLocked && !this.options.freeScroll) {
                if (absDistX > absDistY + this.options.directionLockThreshold) {
                    this.directionLocked = 'h'; // lock horizontally
                } else if (absDistY >= absDistX + this.options.directionLockThreshold) {
                    this.directionLocked = 'v'; // lock vertically
                } else {
                    this.directionLocked = 'n'; // no lock
                }
            }

            if (this.directionLocked === 'h') {
                if (this.options.eventPassthrough === 'vertical') {
                    e.preventDefault();
                } else if (this.options.eventPassthrough === 'horizontal') {
                    this.initiated = false;
                    return;
                }

                deltaY = 0;
            } else if (this.directionLocked === 'v') {
                if (this.options.eventPassthrough === 'horizontal') {
                    e.preventDefault();
                } else if (this.options.eventPassthrough === 'vertical') {
                    this.initiated = false;
                    return;
                }

                deltaX = 0;
            }

            deltaX = this.hasHorizontalScroll ? deltaX : 0;
            deltaY = this.hasVerticalScroll ? deltaY : 0;

            newX = this.x + deltaX;
            newY = this.y + deltaY;

            // Slow down if outside of the boundaries
            if (newX > 0 || newX < this.maxScrollX) {
                newX = this.options.bounce ? this.x + deltaX / 3 : newX > 0 ? 0 : this.maxScrollX;
            }
            if (newY > 0 || newY < this.maxScrollY) {
                newY = this.options.bounce ? this.y + deltaY / 3 : newY > 0 ? 0 : this.maxScrollY;
            }

            this.directionX = deltaX > 0 ? -1 : deltaX < 0 ? 1 : 0;
            this.directionY = deltaY > 0 ? -1 : deltaY < 0 ? 1 : 0;

            if (!this.moved) {
                this._execEvent('scrollStart');
            }

            this.moved = true;

            this._translate(newX, newY);

            /* REPLACE START: _move */
            if (timestamp - this.startTime > 300) {
                this.startTime = timestamp;
                this.startX = this.x;
                this.startY = this.y;

                if (this.options.probeType === 1) {
                    this._execEvent('scroll');
                }
            }

            if (this.options.probeType > 1) {
                this._execEvent('scroll');
            }
            /* REPLACE END: _move */

        },

        _end: function(e) {
            if (!this.enabled || utils.eventType[e.type] !== this.initiated) {
                return;
            }

            if (this.options.preventDefault && !utils.preventDefaultException(e.target, this.options.preventDefaultException)) {
                e.preventDefault();
            }

            var /*point = e.changedTouches ? e.changedTouches[0] : e,*/
                momentumX,
                momentumY,
                duration = utils.getTime() - this.startTime,
                newX = Math.round(this.x),
                newY = Math.round(this.y),
                distanceX = Math.abs(newX - this.startX),
                distanceY = Math.abs(newY - this.startY),
                time = 0,
                easing = '';

            this.isInTransition = 0;
            this.initiated = 0;
            this.endTime = utils.getTime();

            // reset if we are outside of the boundaries
            if (this.resetPosition(this.options.bounceTime)) {
                return;
            }

            this.scrollTo(newX, newY); // ensures that the last position is rounded

            // we scrolled less than 10 pixels
            if (!this.moved) {
                if (this.options.tap) {
                    utils.tap(e, this.options.tap);
                }

                if (this.options.click) {
                    utils.click(e);
                }

                this._execEvent('scrollCancel');
                return;
            }

            if (this._events.flick && duration < 200 && distanceX < 100 && distanceY < 100) {
                this._execEvent('flick');
                return;
            }

            // start momentum animation if needed
            if (this.options.momentum && duration < 300) {
                momentumX = this.hasHorizontalScroll ? utils.momentum(this.x, this.startX, duration, this.maxScrollX, this.options.bounce ? this.wrapperWidth : 0, this.options.deceleration, this) : {
                    destination: newX,
                    duration: 0
                };
                momentumY = this.hasVerticalScroll ? utils.momentum(this.y, this.startY, duration, this.maxScrollY, this.options.bounce ? this.wrapperHeight : 0, this.options.deceleration, this) : {
                    destination: newY,
                    duration: 0
                };
                newX = momentumX.destination;
                newY = momentumY.destination;
                time = Math.max(momentumX.duration, momentumY.duration);
                this.isInTransition = 1;
            }


            if (this.options.snap) {
                var snap = this._nearestSnap(newX, newY);
                this.currentPage = snap;
                time = this.options.snapSpeed || Math.max(
                    Math.max(
                        Math.min(Math.abs(newX - snap.x), 1000),
                        Math.min(Math.abs(newY - snap.y), 1000)
                    ), 300);
                newX = snap.x;
                newY = snap.y;

                this.directionX = 0;
                this.directionY = 0;
                easing = this.options.bounceEasing;
            }

            // INSERT POINT: _end

            if (newX !== this.x || newY !== this.y) {
                // change easing function when scroller goes out of the boundaries
                if (newX > 0 || newX < this.maxScrollX || newY > 0 || newY < this.maxScrollY) {
                    easing = utils.ease.quadratic;
                }

                this.scrollTo(newX, newY, time, easing);
                return;
            }

            this._execEvent('scrollEnd');
        },

        _resize: function() {
            var that = this;

            clearTimeout(this.resizeTimeout);

            this.resizeTimeout = setTimeout(function() {
                that.refresh();
            }, this.options.resizePolling);
        },

        resetPosition: function(time) {
            var x = this.x,
                y = this.y;

            time = time || 0;

            if (!this.hasHorizontalScroll || this.x > 0) {
                x = 0;
            } else if (this.x < this.maxScrollX) {
                x = this.maxScrollX;
            }

            if (!this.hasVerticalScroll || this.y > 0) {
                y = 0;
            } else if (this.y < this.maxScrollY) {
                y = this.maxScrollY;
            }

            if (x === this.x && y === this.y) {
                return false;
            }

            if (this.options.ptr && this.y > 44 && this.startY * -1 < $(window).height() && !this.ptrLock) {// jshint ignore:line
                // not trigger ptr when user want to scroll to top
                y = this.options.ptrOffset || 44;
                this._execEvent('ptr');
                // 防止返回的过程中再次触发了 ptr ，导致被定位到 44px（因为可能done事件触发很快，在返回到44px以前就触发done
                this.ptrLock = true;
                var self = this;
                setTimeout(function() {
                    self.ptrLock = false;
                }, 500);
            }

            this.scrollTo(x, y, time, this.options.bounceEasing);

            return true;
        },

        disable: function() {
            this.enabled = false;
        },

        enable: function() {
            this.enabled = true;
        },

        refresh: function() {
            // var rf = this.wrapper.offsetHeight; // Force reflow

            this.wrapperWidth = this.wrapper.clientWidth;
            this.wrapperHeight = this.wrapper.clientHeight;

            /* REPLACE START: refresh */

            this.scrollerWidth = this.scroller.offsetWidth;
            this.scrollerHeight = this.scroller.offsetHeight;

            this.maxScrollX = this.wrapperWidth - this.scrollerWidth;
            this.maxScrollY = this.wrapperHeight - this.scrollerHeight;

            /* REPLACE END: refresh */

            this.hasHorizontalScroll = this.options.scrollX && this.maxScrollX < 0;
            this.hasVerticalScroll = this.options.scrollY && this.maxScrollY < 0;

            if (!this.hasHorizontalScroll) {
                this.maxScrollX = 0;
                this.scrollerWidth = this.wrapperWidth;
            }

            if (!this.hasVerticalScroll) {
                this.maxScrollY = 0;
                this.scrollerHeight = this.wrapperHeight;
            }

            this.endTime = 0;
            this.directionX = 0;
            this.directionY = 0;

            this.wrapperOffset = utils.offset(this.wrapper);

            this._execEvent('refresh');

            this.resetPosition();

            // INSERT POINT: _refresh

        },

        on: function(type, fn) {
            if (!this._events[type]) {
                this._events[type] = [];
            }

            this._events[type].push(fn);
        },

        off: function(type, fn) {
            if (!this._events[type]) {
                return;
            }

            var index = this._events[type].indexOf(fn);

            if (index > -1) {
                this._events[type].splice(index, 1);
            }
        },

        _execEvent: function(type) {
            if (!this._events[type]) {
                return;
            }

            var i = 0,
                l = this._events[type].length;

            if (!l) {
                return;
            }

            for (; i < l; i++) {
                this._events[type][i].apply(this, [].slice.call(arguments, 1));
            }
        },

        scrollBy: function(x, y, time, easing) {
            x = this.x + x;
            y = this.y + y;
            time = time || 0;

            this.scrollTo(x, y, time, easing);
        },

        scrollTo: function(x, y, time, easing) {
            easing = easing || utils.ease.circular;

            this.isInTransition = this.options.useTransition && time > 0;

            if (!time || (this.options.useTransition && easing.style)) {
                this._transitionTimingFunction(easing.style);
                this._transitionTime(time);
                this._translate(x, y);
            } else {
                this._animate(x, y, time, easing.fn);
            }
        },

        scrollToElement: function(el, time, offsetX, offsetY, easing) {
            el = el.nodeType ? el : this.scroller.querySelector(el);

            if (!el) {
                return;
            }

            var pos = utils.offset(el);

            pos.left -= this.wrapperOffset.left;
            pos.top -= this.wrapperOffset.top;

            // if offsetX/Y are true we center the element to the screen
            if (offsetX === true) {
                offsetX = Math.round(el.offsetWidth / 2 - this.wrapper.offsetWidth / 2);
            }
            if (offsetY === true) {
                offsetY = Math.round(el.offsetHeight / 2 - this.wrapper.offsetHeight / 2);
            }

            pos.left -= offsetX || 0;
            pos.top -= offsetY || 0;

            pos.left = pos.left > 0 ? 0 : pos.left < this.maxScrollX ? this.maxScrollX : pos.left;
            pos.top = pos.top > 0 ? 0 : pos.top < this.maxScrollY ? this.maxScrollY : pos.top;

            time = time === undefined || time === null || time === 'auto' ? Math.max(Math.abs(this.x - pos.left), Math.abs(this.y - pos.top)) : time;

            this.scrollTo(pos.left, pos.top, time, easing);
        },

        _transitionTime: function(time) {
            time = time || 0;

            this.scrollerStyle[utils.style.transitionDuration] = time + 'ms';

            if (!time && utils.isBadAndroid) {
                this.scrollerStyle[utils.style.transitionDuration] = '0.001s';
            }


            if (this.indicators) {
                for (var i = this.indicators.length; i--;) {
                    this.indicators[i].transitionTime(time);
                }
            }


            // INSERT POINT: _transitionTime

        },

        _transitionTimingFunction: function(easing) {
            this.scrollerStyle[utils.style.transitionTimingFunction] = easing;


            if (this.indicators) {
                for (var i = this.indicators.length; i--;) {
                    this.indicators[i].transitionTimingFunction(easing);
                }
            }


            // INSERT POINT: _transitionTimingFunction

        },

        _translate: function(x, y) {
            if (this.options.useTransform) {

                /* REPLACE START: _translate */

                this.scrollerStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.translateZ;

                /* REPLACE END: _translate */

            } else {
                x = Math.round(x);
                y = Math.round(y);
                this.scrollerStyle.left = x + 'px';
                this.scrollerStyle.top = y + 'px';
            }

            this.x = x;
            this.y = y;


            if (this.indicators) {
                for (var i = this.indicators.length; i--;) {
                    this.indicators[i].updatePosition();
                }
            }


            // INSERT POINT: _translate

        },

        _initEvents: function(remove) {
            var eventType = remove ? utils.removeEvent : utils.addEvent,
                target = this.options.bindToWrapper ? this.wrapper : window;

            eventType(window, 'orientationchange', this);
            eventType(window, 'resize', this);

            if (this.options.click) {
                eventType(this.wrapper, 'click', this, true);
            }

            if (!this.options.disableMouse) {
                eventType(this.wrapper, 'mousedown', this);
                eventType(target, 'mousemove', this);
                eventType(target, 'mousecancel', this);
                eventType(target, 'mouseup', this);
            }

            if (utils.hasPointer && !this.options.disablePointer) {
                eventType(this.wrapper, utils.prefixPointerEvent('pointerdown'), this);
                eventType(target, utils.prefixPointerEvent('pointermove'), this);
                eventType(target, utils.prefixPointerEvent('pointercancel'), this);
                eventType(target, utils.prefixPointerEvent('pointerup'), this);
            }

            if (utils.hasTouch && !this.options.disableTouch) {
                eventType(this.wrapper, 'touchstart', this);
                eventType(target, 'touchmove', this);
                eventType(target, 'touchcancel', this);
                eventType(target, 'touchend', this);
            }

            eventType(this.scroller, 'transitionend', this);
            eventType(this.scroller, 'webkitTransitionEnd', this);
            eventType(this.scroller, 'oTransitionEnd', this);
            eventType(this.scroller, 'MSTransitionEnd', this);
        },

        getComputedPosition: function() {
            var matrix = window.getComputedStyle(this.scroller, null),
                x, y;

            if (this.options.useTransform) {
                matrix = matrix[utils.style.transform].split(')')[0].split(', ');
                x = +(matrix[12] || matrix[4]);
                y = +(matrix[13] || matrix[5]);
            } else {
                x = +matrix.left.replace(/[^-\d.]/g, '');
                y = +matrix.top.replace(/[^-\d.]/g, '');
            }

            return {
                x: x,
                y: y
            };
        },

        _initIndicators: function() {
            var interactive = this.options.interactiveScrollbars,
                customStyle = typeof this.options.scrollbars !== 'string',
                indicators = [],
                indicator;

            var that = this;

            this.indicators = [];

            if (this.options.scrollbars) {
                // Vertical scrollbar
                if (this.options.scrollY) {
                    indicator = {
                        el: createDefaultScrollbar('v', interactive, this.options.scrollbars),
                        interactive: interactive,
                        defaultScrollbars: true,
                        customStyle: customStyle,
                        resize: this.options.resizeScrollbars,
                        shrink: this.options.shrinkScrollbars,
                        fade: this.options.fadeScrollbars,
                        listenX: false
                    };

                    this.wrapper.appendChild(indicator.el);
                    indicators.push(indicator);
                }

                // Horizontal scrollbar
                if (this.options.scrollX) {
                    indicator = {
                        el: createDefaultScrollbar('h', interactive, this.options.scrollbars),
                        interactive: interactive,
                        defaultScrollbars: true,
                        customStyle: customStyle,
                        resize: this.options.resizeScrollbars,
                        shrink: this.options.shrinkScrollbars,
                        fade: this.options.fadeScrollbars,
                        listenY: false
                    };

                    this.wrapper.appendChild(indicator.el);
                    indicators.push(indicator);
                }
            }

            if (this.options.indicators) {
                // TODO: check concat compatibility
                indicators = indicators.concat(this.options.indicators);
            }

            for (var i = indicators.length; i--;) {
                this.indicators.push(new Indicator(this, indicators[i]));
            }

            // TODO: check if we can use array.map (wide compatibility and performance issues)
            function _indicatorsMap(fn) {
                for (var i = that.indicators.length; i--;) {
                    fn.call(that.indicators[i]);
                }
            }

            if (this.options.fadeScrollbars) {
                this.on('scrollEnd', function() {
                    _indicatorsMap(function() {
                        this.fade();
                    });
                });

                this.on('scrollCancel', function() {
                    _indicatorsMap(function() {
                        this.fade();
                    });
                });

                this.on('scrollStart', function() {
                    _indicatorsMap(function() {
                        this.fade(1);
                    });
                });

                this.on('beforeScrollStart', function() {
                    _indicatorsMap(function() {
                        this.fade(1, true);
                    });
                });
            }


            this.on('refresh', function() {
                _indicatorsMap(function() {
                    this.refresh();
                });
            });

            this.on('destroy', function() {
                _indicatorsMap(function() {
                    this.destroy();
                });

                delete this.indicators;
            });
        },

        _initWheel: function() {
            utils.addEvent(this.wrapper, 'wheel', this);
            utils.addEvent(this.wrapper, 'mousewheel', this);
            utils.addEvent(this.wrapper, 'DOMMouseScroll', this);

            this.on('destroy', function() {
                utils.removeEvent(this.wrapper, 'wheel', this);
                utils.removeEvent(this.wrapper, 'mousewheel', this);
                utils.removeEvent(this.wrapper, 'DOMMouseScroll', this);
            });
        },

        _wheel: function(e) {
            if (!this.enabled) {
                return;
            }

            e.preventDefault();
            e.stopPropagation();

            var wheelDeltaX, wheelDeltaY,
                newX, newY,
                that = this;

            if (this.wheelTimeout === undefined) {
                that._execEvent('scrollStart');
            }

            // Execute the scrollEnd event after 400ms the wheel stopped scrolling
            clearTimeout(this.wheelTimeout);
            this.wheelTimeout = setTimeout(function() {
                that._execEvent('scrollEnd');
                that.wheelTimeout = undefined;
            }, 400);

            if ('deltaX' in e) {
                if (e.deltaMode === 1) {
                    wheelDeltaX = -e.deltaX * this.options.mouseWheelSpeed;
                    wheelDeltaY = -e.deltaY * this.options.mouseWheelSpeed;
                } else {
                    wheelDeltaX = -e.deltaX;
                    wheelDeltaY = -e.deltaY;
                }
            } else if ('wheelDeltaX' in e) {
                wheelDeltaX = e.wheelDeltaX / 120 * this.options.mouseWheelSpeed;
                wheelDeltaY = e.wheelDeltaY / 120 * this.options.mouseWheelSpeed;
            } else if ('wheelDelta' in e) {
                wheelDeltaX = wheelDeltaY = e.wheelDelta / 120 * this.options.mouseWheelSpeed;
            } else if ('detail' in e) {
                wheelDeltaX = wheelDeltaY = -e.detail / 3 * this.options.mouseWheelSpeed;
            } else {
                return;
            }

            wheelDeltaX *= this.options.invertWheelDirection;
            wheelDeltaY *= this.options.invertWheelDirection;

            if (!this.hasVerticalScroll) {
                wheelDeltaX = wheelDeltaY;
                wheelDeltaY = 0;
            }

            if (this.options.snap) {
                newX = this.currentPage.pageX;
                newY = this.currentPage.pageY;

                if (wheelDeltaX > 0) {
                    newX--;
                } else if (wheelDeltaX < 0) {
                    newX++;
                }

                if (wheelDeltaY > 0) {
                    newY--;
                } else if (wheelDeltaY < 0) {
                    newY++;
                }

                this.goToPage(newX, newY);

                return;
            }

            newX = this.x + Math.round(this.hasHorizontalScroll ? wheelDeltaX : 0);
            newY = this.y + Math.round(this.hasVerticalScroll ? wheelDeltaY : 0);

            if (newX > 0) {
                newX = 0;
            } else if (newX < this.maxScrollX) {
                newX = this.maxScrollX;
            }

            if (newY > 0) {
                newY = 0;
            } else if (newY < this.maxScrollY) {
                newY = this.maxScrollY;
            }

            this.scrollTo(newX, newY, 0);

            this._execEvent('scroll');

            // INSERT POINT: _wheel
        },

        _initSnap: function() {
            this.currentPage = {};

            if (typeof this.options.snap === 'string') {
                this.options.snap = this.scroller.querySelectorAll(this.options.snap);
            }

            this.on('refresh', function() {
                var i = 0,
                    l,
                    m = 0,
                    n,
                    cx, cy,
                    x = 0,
                    y,
                    stepX = this.options.snapStepX || this.wrapperWidth,
                    stepY = this.options.snapStepY || this.wrapperHeight,
                    el;

                this.pages = [];

                if (!this.wrapperWidth || !this.wrapperHeight || !this.scrollerWidth || !this.scrollerHeight) {
                    return;
                }

                if (this.options.snap === true) {
                    cx = Math.round(stepX / 2);
                    cy = Math.round(stepY / 2);

                    while (x > -this.scrollerWidth) {
                        this.pages[i] = [];
                        l = 0;
                        y = 0;

                        while (y > -this.scrollerHeight) {
                            this.pages[i][l] = {
                                x: Math.max(x, this.maxScrollX),
                                y: Math.max(y, this.maxScrollY),
                                width: stepX,
                                height: stepY,
                                cx: x - cx,
                                cy: y - cy
                            };

                            y -= stepY;
                            l++;
                        }

                        x -= stepX;
                        i++;
                    }
                } else {
                    el = this.options.snap;
                    l = el.length;
                    n = -1;

                    for (; i < l; i++) {
                        if (i === 0 || el[i].offsetLeft <= el[i - 1].offsetLeft) {
                            m = 0;
                            n++;
                        }

                        if (!this.pages[m]) {
                            this.pages[m] = [];
                        }

                        x = Math.max(-el[i].offsetLeft, this.maxScrollX);
                        y = Math.max(-el[i].offsetTop, this.maxScrollY);
                        cx = x - Math.round(el[i].offsetWidth / 2);
                        cy = y - Math.round(el[i].offsetHeight / 2);

                        this.pages[m][n] = {
                            x: x,
                            y: y,
                            width: el[i].offsetWidth,
                            height: el[i].offsetHeight,
                            cx: cx,
                            cy: cy
                        };

                        if (x > this.maxScrollX) {
                            m++;
                        }
                    }
                }

                this.goToPage(this.currentPage.pageX || 0, this.currentPage.pageY || 0, 0);

                // Update snap threshold if needed
                if (this.options.snapThreshold % 1 === 0) {
                    this.snapThresholdX = this.options.snapThreshold;
                    this.snapThresholdY = this.options.snapThreshold;
                } else {
                    this.snapThresholdX = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].width * this.options.snapThreshold);
                    this.snapThresholdY = Math.round(this.pages[this.currentPage.pageX][this.currentPage.pageY].height * this.options.snapThreshold);
                }
            });

            this.on('flick', function() {
                var time = this.options.snapSpeed || Math.max(
                    Math.max(
                        Math.min(Math.abs(this.x - this.startX), 1000),
                        Math.min(Math.abs(this.y - this.startY), 1000)
                    ), 300);

                this.goToPage(
                    this.currentPage.pageX + this.directionX,
                    this.currentPage.pageY + this.directionY,
                    time
                );
            });
        },

        _nearestSnap: function(x, y) {
            if (!this.pages.length) {
                return {
                    x: 0,
                    y: 0,
                    pageX: 0,
                    pageY: 0
                };
            }

            var i = 0,
                l = this.pages.length,
                m = 0;

            // Check if we exceeded the snap threshold
            if (Math.abs(x - this.absStartX) < this.snapThresholdX &&
                Math.abs(y - this.absStartY) < this.snapThresholdY) {
                return this.currentPage;
            }

            if (x > 0) {
                x = 0;
            } else if (x < this.maxScrollX) {
                x = this.maxScrollX;
            }

            if (y > 0) {
                y = 0;
            } else if (y < this.maxScrollY) {
                y = this.maxScrollY;
            }

            for (; i < l; i++) {
                if (x >= this.pages[i][0].cx) {
                    x = this.pages[i][0].x;
                    break;
                }
            }

            l = this.pages[i].length;

            for (; m < l; m++) {
                if (y >= this.pages[0][m].cy) {
                    y = this.pages[0][m].y;
                    break;
                }
            }

            if (i === this.currentPage.pageX) {
                i += this.directionX;

                if (i < 0) {
                    i = 0;
                } else if (i >= this.pages.length) {
                    i = this.pages.length - 1;
                }

                x = this.pages[i][0].x;
            }

            if (m === this.currentPage.pageY) {
                m += this.directionY;

                if (m < 0) {
                    m = 0;
                } else if (m >= this.pages[0].length) {
                    m = this.pages[0].length - 1;
                }

                y = this.pages[0][m].y;
            }

            return {
                x: x,
                y: y,
                pageX: i,
                pageY: m
            };
        },

        goToPage: function(x, y, time, easing) {
            easing = easing || this.options.bounceEasing;

            if (x >= this.pages.length) {
                x = this.pages.length - 1;
            } else if (x < 0) {
                x = 0;
            }

            if (y >= this.pages[x].length) {
                y = this.pages[x].length - 1;
            } else if (y < 0) {
                y = 0;
            }

            var posX = this.pages[x][y].x,
                posY = this.pages[x][y].y;

            time = time === undefined ? this.options.snapSpeed || Math.max(
                Math.max(
                    Math.min(Math.abs(posX - this.x), 1000),
                    Math.min(Math.abs(posY - this.y), 1000)
                ), 300) : time;

            this.currentPage = {
                x: posX,
                y: posY,
                pageX: x,
                pageY: y
            };

            this.scrollTo(posX, posY, time, easing);
        },

        next: function(time, easing) {
            var x = this.currentPage.pageX,
                y = this.currentPage.pageY;

            x++;

            if (x >= this.pages.length && this.hasVerticalScroll) {
                x = 0;
                y++;
            }

            this.goToPage(x, y, time, easing);
        },

        prev: function(time, easing) {
            var x = this.currentPage.pageX,
                y = this.currentPage.pageY;

            x--;

            if (x < 0 && this.hasVerticalScroll) {
                x = 0;
                y--;
            }

            this.goToPage(x, y, time, easing);
        },

        _initKeys: function() {
            // default key bindings
            var keys = {
                pageUp: 33,
                pageDown: 34,
                end: 35,
                home: 36,
                left: 37,
                up: 38,
                right: 39,
                down: 40
            };
            var i;

            // if you give me characters I give you keycode
            if (typeof this.options.keyBindings === 'object') {
                for (i in this.options.keyBindings) {
                    if (typeof this.options.keyBindings[i] === 'string') {
                        this.options.keyBindings[i] = this.options.keyBindings[i].toUpperCase().charCodeAt(0);
                    }
                }
            } else {
                this.options.keyBindings = {};
            }

            for (i in keys) { // jshint ignore:line
                    this.options.keyBindings[i] = this.options.keyBindings[i] || keys[i];
            }

            utils.addEvent(window, 'keydown', this);

            this.on('destroy', function() {
                utils.removeEvent(window, 'keydown', this);
            });
        },

        _key: function(e) {
            if (!this.enabled) {
                return;
            }

            var snap = this.options.snap, // we are using this alot, better to cache it
                newX = snap ? this.currentPage.pageX : this.x,
                newY = snap ? this.currentPage.pageY : this.y,
                now = utils.getTime(),
                prevTime = this.keyTime || 0,
                acceleration = 0.250,
                pos;

            if (this.options.useTransition && this.isInTransition) {
                pos = this.getComputedPosition();

                this._translate(Math.round(pos.x), Math.round(pos.y));
                this.isInTransition = false;
            }

            this.keyAcceleration = now - prevTime < 200 ? Math.min(this.keyAcceleration + acceleration, 50) : 0;

            switch (e.keyCode) {
                case this.options.keyBindings.pageUp:
                    if (this.hasHorizontalScroll && !this.hasVerticalScroll) {
                        newX += snap ? 1 : this.wrapperWidth;
                    } else {
                        newY += snap ? 1 : this.wrapperHeight;
                    }
                    break;
                case this.options.keyBindings.pageDown:
                    if (this.hasHorizontalScroll && !this.hasVerticalScroll) {
                        newX -= snap ? 1 : this.wrapperWidth;
                    } else {
                        newY -= snap ? 1 : this.wrapperHeight;
                    }
                    break;
                case this.options.keyBindings.end:
                    newX = snap ? this.pages.length - 1 : this.maxScrollX;
                    newY = snap ? this.pages[0].length - 1 : this.maxScrollY;
                    break;
                case this.options.keyBindings.home:
                    newX = 0;
                    newY = 0;
                    break;
                case this.options.keyBindings.left:
                    newX += snap ? -1 : 5 + this.keyAcceleration >> 0; // jshint ignore:line
                    break;
                case this.options.keyBindings.up:
                    newY += snap ? 1 : 5 + this.keyAcceleration >> 0; // jshint ignore:line
                    break;
                case this.options.keyBindings.right:
                    newX -= snap ? -1 : 5 + this.keyAcceleration >> 0; // jshint ignore:line
                    break;
                case this.options.keyBindings.down:
                    newY -= snap ? 1 : 5 + this.keyAcceleration >> 0; // jshint ignore:line
                    break;
                default:
                    return;
            }

            if (snap) {
                this.goToPage(newX, newY);
                return;
            }

            if (newX > 0) {
                newX = 0;
                this.keyAcceleration = 0;
            } else if (newX < this.maxScrollX) {
                newX = this.maxScrollX;
                this.keyAcceleration = 0;
            }

            if (newY > 0) {
                newY = 0;
                this.keyAcceleration = 0;
            } else if (newY < this.maxScrollY) {
                newY = this.maxScrollY;
                this.keyAcceleration = 0;
            }

            this.scrollTo(newX, newY, 0);

            this.keyTime = now;
        },

        _animate: function(destX, destY, duration, easingFn) {
            var that = this,
                startX = this.x,
                startY = this.y,
                startTime = utils.getTime(),
                destTime = startTime + duration;

            function step() {
                var now = utils.getTime(),
                    newX, newY,
                    easing;

                if (now >= destTime) {
                    that.isAnimating = false;
                    that._translate(destX, destY);

                    if (!that.resetPosition(that.options.bounceTime)) {
                        that._execEvent('scrollEnd');
                    }

                    return;
                }

                now = (now - startTime) / duration;
                easing = easingFn(now);
                newX = (destX - startX) * easing + startX;
                newY = (destY - startY) * easing + startY;
                that._translate(newX, newY);

                if (that.isAnimating) {
                    rAF(step);
                }

                if (that.options.probeType === 3) {
                    that._execEvent('scroll');
                }
            }

            this.isAnimating = true;
            step();
        },

        handleEvent: function(e) {
            switch (e.type) {
                case 'touchstart':
                case 'pointerdown':
                case 'MSPointerDown':
                case 'mousedown':
                    this._start(e);
                    break;
                case 'touchmove':
                case 'pointermove':
                case 'MSPointerMove':
                case 'mousemove':
                    this._move(e);
                    break;
                case 'touchend':
                case 'pointerup':
                case 'MSPointerUp':
                case 'mouseup':
                case 'touchcancel':
                case 'pointercancel':
                case 'MSPointerCancel':
                case 'mousecancel':
                    this._end(e);
                    break;
                case 'orientationchange':
                case 'resize':
                    this._resize();
                    break;
                case 'transitionend':
                case 'webkitTransitionEnd':
                case 'oTransitionEnd':
                case 'MSTransitionEnd':
                    this._transitionEnd(e);
                    break;
                case 'wheel':
                case 'DOMMouseScroll':
                case 'mousewheel':
                    this._wheel(e);
                    break;
                case 'keydown':
                    this._key(e);
                    break;
                case 'click':
                    if (!e._constructed) {
                        e.preventDefault();
                        e.stopPropagation();
                    }
                    break;
            }
        }
    };

    function createDefaultScrollbar(direction, interactive, type) {
        var scrollbar = document.createElement('div'),
            indicator = document.createElement('div');

        if (type === true) {
            scrollbar.style.cssText = 'position:absolute;z-index:9999';
            indicator.style.cssText = '-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;position:absolute;background:rgba(0,0,0,0.5);border:1px solid rgba(255,255,255,0.9);border-radius:3px';
        }

        indicator.className = 'iScrollIndicator';

        if (direction === 'h') {
            if (type === true) {
                scrollbar.style.cssText += ';height:5px;left:2px;right:2px;bottom:0';
                indicator.style.height = '100%';
            }
            scrollbar.className = 'iScrollHorizontalScrollbar';
        } else {
            if (type === true) {
                scrollbar.style.cssText += ';width:5px;bottom:2px;top:2px;right:1px';
                indicator.style.width = '100%';
            }
            scrollbar.className = 'iScrollVerticalScrollbar';
        }

        scrollbar.style.cssText += ';overflow:hidden';

        if (!interactive) {
            scrollbar.style.pointerEvents = 'none';
        }

        scrollbar.appendChild(indicator);

        return scrollbar;
    }

    function Indicator(scroller, options) {
        this.wrapper = typeof options.el === 'string' ? document.querySelector(options.el) : options.el;
        this.wrapperStyle = this.wrapper.style;
        this.indicator = this.wrapper.children[0];
        this.indicatorStyle = this.indicator.style;
        this.scroller = scroller;

        this.options = {
            listenX: true,
            listenY: true,
            interactive: false,
            resize: true,
            defaultScrollbars: false,
            shrink: false,
            fade: false,
            speedRatioX: 0,
            speedRatioY: 0
        };

        for (var i in options) { // jshint ignore:line
                this.options[i] = options[i];

        }

        this.sizeRatioX = 1;
        this.sizeRatioY = 1;
        this.maxPosX = 0;
        this.maxPosY = 0;

        if (this.options.interactive) {
            if (!this.options.disableTouch) {
                utils.addEvent(this.indicator, 'touchstart', this);
                utils.addEvent(window, 'touchend', this);
            }
            if (!this.options.disablePointer) {
                utils.addEvent(this.indicator, utils.prefixPointerEvent('pointerdown'), this);
                utils.addEvent(window, utils.prefixPointerEvent('pointerup'), this);
            }
            if (!this.options.disableMouse) {
                utils.addEvent(this.indicator, 'mousedown', this);
                utils.addEvent(window, 'mouseup', this);
            }
        }

        if (this.options.fade) {
            this.wrapperStyle[utils.style.transform] = this.scroller.translateZ;
            this.wrapperStyle[utils.style.transitionDuration] = utils.isBadAndroid ? '0.001s' : '0ms';
            this.wrapperStyle.opacity = '0';
        }
    }

    Indicator.prototype = {
        handleEvent: function(e) {
            switch (e.type) {
                case 'touchstart':
                case 'pointerdown':
                case 'MSPointerDown':
                case 'mousedown':
                    this._start(e);
                    break;
                case 'touchmove':
                case 'pointermove':
                case 'MSPointerMove':
                case 'mousemove':
                    this._move(e);
                    break;
                case 'touchend':
                case 'pointerup':
                case 'MSPointerUp':
                case 'mouseup':
                case 'touchcancel':
                case 'pointercancel':
                case 'MSPointerCancel':
                case 'mousecancel':
                    this._end(e);
                    break;
            }
        },

        destroy: function() {
            if (this.options.interactive) {
                utils.removeEvent(this.indicator, 'touchstart', this);
                utils.removeEvent(this.indicator, utils.prefixPointerEvent('pointerdown'), this);
                utils.removeEvent(this.indicator, 'mousedown', this);

                utils.removeEvent(window, 'touchmove', this);
                utils.removeEvent(window, utils.prefixPointerEvent('pointermove'), this);
                utils.removeEvent(window, 'mousemove', this);

                utils.removeEvent(window, 'touchend', this);
                utils.removeEvent(window, utils.prefixPointerEvent('pointerup'), this);
                utils.removeEvent(window, 'mouseup', this);
            }

            if (this.options.defaultScrollbars) {
                this.wrapper.parentNode.removeChild(this.wrapper);
            }
        },

        _start: function(e) {
            var point = e.touches ? e.touches[0] : e;

            e.preventDefault();
            e.stopPropagation();

            this.transitionTime();

            this.initiated = true;
            this.moved = false;
            this.lastPointX = point.pageX;
            this.lastPointY = point.pageY;

            this.startTime = utils.getTime();

            if (!this.options.disableTouch) {
                utils.addEvent(window, 'touchmove', this);
            }
            if (!this.options.disablePointer) {
                utils.addEvent(window, utils.prefixPointerEvent('pointermove'), this);
            }
            if (!this.options.disableMouse) {
                utils.addEvent(window, 'mousemove', this);
            }

            this.scroller._execEvent('beforeScrollStart');
        },

        _move: function(e) {
            var point = e.touches ? e.touches[0] : e,
                deltaX, deltaY,
                newX, newY,
                timestamp = utils.getTime();

            if (!this.moved) {
                this.scroller._execEvent('scrollStart');
            }

            this.moved = true;

            deltaX = point.pageX - this.lastPointX;
            this.lastPointX = point.pageX;

            deltaY = point.pageY - this.lastPointY;
            this.lastPointY = point.pageY;

            newX = this.x + deltaX;
            newY = this.y + deltaY;

            this._pos(newX, newY);


            if (this.scroller.options.probeType === 1 && timestamp - this.startTime > 300) {
                this.startTime = timestamp;
                this.scroller._execEvent('scroll');
            } else if (this.scroller.options.probeType > 1) {
                this.scroller._execEvent('scroll');
            }


            // INSERT POINT: indicator._move

            e.preventDefault();
            e.stopPropagation();
        },

        _end: function(e) {
            if (!this.initiated) {
                return;
            }

            this.initiated = false;

            e.preventDefault();
            e.stopPropagation();

            utils.removeEvent(window, 'touchmove', this);
            utils.removeEvent(window, utils.prefixPointerEvent('pointermove'), this);
            utils.removeEvent(window, 'mousemove', this);

            if (this.scroller.options.snap) {
                var snap = this.scroller._nearestSnap(this.scroller.x, this.scroller.y);

                var time = this.options.snapSpeed || Math.max(
                    Math.max(
                        Math.min(Math.abs(this.scroller.x - snap.x), 1000),
                        Math.min(Math.abs(this.scroller.y - snap.y), 1000)
                    ), 300);

                if (this.scroller.x !== snap.x || this.scroller.y !== snap.y) {
                    this.scroller.directionX = 0;
                    this.scroller.directionY = 0;
                    this.scroller.currentPage = snap;
                    this.scroller.scrollTo(snap.x, snap.y, time, this.scroller.options.bounceEasing);
                }
            }

            if (this.moved) {
                this.scroller._execEvent('scrollEnd');
            }
        },

        transitionTime: function(time) {
            time = time || 0;
            this.indicatorStyle[utils.style.transitionDuration] = time + 'ms';

            if (!time && utils.isBadAndroid) {
                this.indicatorStyle[utils.style.transitionDuration] = '0.001s';
            }
        },

        transitionTimingFunction: function(easing) {
            this.indicatorStyle[utils.style.transitionTimingFunction] = easing;
        },

        refresh: function() {
            this.transitionTime();

            if (this.options.listenX && !this.options.listenY) {
                this.indicatorStyle.display = this.scroller.hasHorizontalScroll ? 'block' : 'none';
            } else if (this.options.listenY && !this.options.listenX) {
                this.indicatorStyle.display = this.scroller.hasVerticalScroll ? 'block' : 'none';
            } else {
                this.indicatorStyle.display = this.scroller.hasHorizontalScroll || this.scroller.hasVerticalScroll ? 'block' : 'none';
            }

            if (this.scroller.hasHorizontalScroll && this.scroller.hasVerticalScroll) {
                utils.addClass(this.wrapper, 'iScrollBothScrollbars');
                utils.removeClass(this.wrapper, 'iScrollLoneScrollbar');

                if (this.options.defaultScrollbars && this.options.customStyle) {
                    if (this.options.listenX) {
                        this.wrapper.style.right = '8px';
                    } else {
                        this.wrapper.style.bottom = '8px';
                    }
                }
            } else {
                utils.removeClass(this.wrapper, 'iScrollBothScrollbars');
                utils.addClass(this.wrapper, 'iScrollLoneScrollbar');

                if (this.options.defaultScrollbars && this.options.customStyle) {
                    if (this.options.listenX) {
                        this.wrapper.style.right = '2px';
                    } else {
                        this.wrapper.style.bottom = '2px';
                    }
                }
            }

            // var r = this.wrapper.offsetHeight; // force refresh

            if (this.options.listenX) {
                this.wrapperWidth = this.wrapper.clientWidth;
                if (this.options.resize) {
                    this.indicatorWidth = Math.max(Math.round(this.wrapperWidth * this.wrapperWidth / (this.scroller.scrollerWidth || this.wrapperWidth || 1)), 8);
                    this.indicatorStyle.width = this.indicatorWidth + 'px';
                } else {
                    this.indicatorWidth = this.indicator.clientWidth;
                }

                this.maxPosX = this.wrapperWidth - this.indicatorWidth;

                if (this.options.shrink === 'clip') {
                    this.minBoundaryX = -this.indicatorWidth + 8;
                    this.maxBoundaryX = this.wrapperWidth - 8;
                } else {
                    this.minBoundaryX = 0;
                    this.maxBoundaryX = this.maxPosX;
                }

                this.sizeRatioX = this.options.speedRatioX || (this.scroller.maxScrollX && (this.maxPosX / this.scroller.maxScrollX));
            }

            if (this.options.listenY) {
                this.wrapperHeight = this.wrapper.clientHeight;
                if (this.options.resize) {
                    this.indicatorHeight = Math.max(Math.round(this.wrapperHeight * this.wrapperHeight / (this.scroller.scrollerHeight || this.wrapperHeight || 1)), 8);
                    this.indicatorStyle.height = this.indicatorHeight + 'px';
                } else {
                    this.indicatorHeight = this.indicator.clientHeight;
                }

                this.maxPosY = this.wrapperHeight - this.indicatorHeight;

                if (this.options.shrink === 'clip') {
                    this.minBoundaryY = -this.indicatorHeight + 8;
                    this.maxBoundaryY = this.wrapperHeight - 8;
                } else {
                    this.minBoundaryY = 0;
                    this.maxBoundaryY = this.maxPosY;
                }

                this.maxPosY = this.wrapperHeight - this.indicatorHeight;
                this.sizeRatioY = this.options.speedRatioY || (this.scroller.maxScrollY && (this.maxPosY / this.scroller.maxScrollY));
            }

            this.updatePosition();
        },

        updatePosition: function() {
            var x = this.options.listenX && Math.round(this.sizeRatioX * this.scroller.x) || 0,
                y = this.options.listenY && Math.round(this.sizeRatioY * this.scroller.y) || 0;

            if (!this.options.ignoreBoundaries) {
                if (x < this.minBoundaryX) {
                    if (this.options.shrink === 'scale') {
                        this.width = Math.max(this.indicatorWidth + x, 8);
                        this.indicatorStyle.width = this.width + 'px';
                    }
                    x = this.minBoundaryX;
                } else if (x > this.maxBoundaryX) {
                    if (this.options.shrink === 'scale') {
                        this.width = Math.max(this.indicatorWidth - (x - this.maxPosX), 8);
                        this.indicatorStyle.width = this.width + 'px';
                        x = this.maxPosX + this.indicatorWidth - this.width;
                    } else {
                        x = this.maxBoundaryX;
                    }
                } else if (this.options.shrink === 'scale' && this.width !== this.indicatorWidth) {
                    this.width = this.indicatorWidth;
                    this.indicatorStyle.width = this.width + 'px';
                }

                if (y < this.minBoundaryY) {
                    if (this.options.shrink === 'scale') {
                        this.height = Math.max(this.indicatorHeight + y * 3, 8);
                        this.indicatorStyle.height = this.height + 'px';
                    }
                    y = this.minBoundaryY;
                } else if (y > this.maxBoundaryY) {
                    if (this.options.shrink === 'scale') {
                        this.height = Math.max(this.indicatorHeight - (y - this.maxPosY) * 3, 8);
                        this.indicatorStyle.height = this.height + 'px';
                        y = this.maxPosY + this.indicatorHeight - this.height;
                    } else {
                        y = this.maxBoundaryY;
                    }
                } else if (this.options.shrink === 'scale' && this.height !== this.indicatorHeight) {
                    this.height = this.indicatorHeight;
                    this.indicatorStyle.height = this.height + 'px';
                }
            }

            this.x = x;
            this.y = y;

            if (this.scroller.options.useTransform) {
                this.indicatorStyle[utils.style.transform] = 'translate(' + x + 'px,' + y + 'px)' + this.scroller.translateZ;
            } else {
                this.indicatorStyle.left = x + 'px';
                this.indicatorStyle.top = y + 'px';
            }
        },

        _pos: function(x, y) {
            if (x < 0) {
                x = 0;
            } else if (x > this.maxPosX) {
                x = this.maxPosX;
            }

            if (y < 0) {
                y = 0;
            } else if (y > this.maxPosY) {
                y = this.maxPosY;
            }

            x = this.options.listenX ? Math.round(x / this.sizeRatioX) : this.scroller.x;
            y = this.options.listenY ? Math.round(y / this.sizeRatioY) : this.scroller.y;

            this.scroller.scrollTo(x, y);
        },

        fade: function(val, hold) {
            if (hold && !this.visible) {
                return;
            }

            clearTimeout(this.fadeTimeout);
            this.fadeTimeout = null;

            var time = val ? 250 : 500,
                delay = val ? 0 : 300;

            val = val ? '1' : '0';

            this.wrapperStyle[utils.style.transitionDuration] = time + 'ms';

            this.fadeTimeout = setTimeout((function(val) {
                this.wrapperStyle.opacity = val;
                this.visible = +val;
            }).bind(this, val), delay);
        }
    };

    IScroll.utils = utils;

    window.IScroll = IScroll;
}(window);

/* ===============================================================================
************   scroller   ************
=============================================================================== */
+ function($) {
    "use strict";
    //重置zepto自带的滚动条
    var _zeptoMethodCache = {
        "scrollTop": $.fn.scrollTop,
        "scrollLeft": $.fn.scrollLeft
    };
    //重置scrollLeft和scrollRight
    (function() {
        $.extend($.fn, {
            scrollTop: function(top, dur) {
                if (!this.length) return;
                var scroller = this.data('scroller');
                if (scroller && scroller.scroller) { //js滚动
                    return scroller.scrollTop(top, dur);
                } else {
                    return _zeptoMethodCache.scrollTop.apply(this, arguments);
                }
            }
        });
        $.extend($.fn, {
            scrollLeft: function(left, dur) {
                if (!this.length) return;
                var scroller = this.data('scroller');
                if (scroller && scroller.scroller) { //js滚动
                    return scroller.scrollLeft(left, dur);
                } else {
                    return _zeptoMethodCache.scrollLeft.apply(this, arguments);
                }
            }
        });
    })();



    //自定义的滚动条
    var Scroller = function(pageContent, _options) {
        var $pageContent = this.$pageContent = $(pageContent);

        this.options = $.extend({}, this._defaults, _options);

        var type = this.options.type;
        //auto的type,系统版本的小于4.4.0的安卓设备和系统版本小于6.0.0的ios设备，启用js版的iscoll
        var useJSScroller = (type === 'js') || (type === 'auto' && ($.device.android && $.compareVersion('4.4.0', $.device.osVersion) > -1) || (type === 'auto' && ($.device.ios && $.compareVersion('6.0.0', $.device.osVersion) > -1)));

        if (useJSScroller) {

            var $pageContentInner = $pageContent.find('.content-inner');
            //如果滚动内容没有被包裹，自动添加wrap
            if (!$pageContentInner[0]) {
                // $pageContent.html('<div class="content-inner">' + $pageContent.html() + '</div>');
                var children = $pageContent.children();
                if (children.length < 1) {
                    $pageContent.children().wrapAll('<div class="content-inner"></div>');
                } else {
                    $pageContent.html('<div class="content-inner">' + $pageContent.html() + '</div>');
                }
            }

            if ($pageContent.hasClass('pull-to-refresh-content')) {
                //因为iscroll 当页面高度不足 100% 时无法滑动，所以无法触发下拉动作，这里改动一下高度
                //区分是否有.bar容器，如有，则content的top:0，无则content的top:-2.2rem,这里取2.2rem的最大值，近60
                var minHeight = $(window).height() + ($pageContent.prev().hasClass(".bar") ? 1 : 61);
                $pageContent.find('.content-inner').css('min-height', minHeight + 'px');
            }

            var ptr = $(pageContent).hasClass('pull-to-refresh-content');
            //js滚动模式，用transform移动内容区位置，会导致fixed失效，表现类似absolute。因此禁用transform模式
            var useTransform = $pageContent.find('.fixed-tab').length === 0;
            var options = {
                probeType: 1,
                mouseWheel: true,
                //解决安卓js模式下，刷新滚动条后绑定的事件不响应，对chrome内核浏览器设置click:true
                click: $.device.androidChrome,
                useTransform: useTransform,
                //js模式下允许滚动条横向滚动，但是需要注意，滚动容易宽度必须大于屏幕宽度滚动才生效
                scrollX: true
            };
            if (ptr) {
                options.ptr = true;
                options.ptrOffset = 44;
            }
            //如果用js滚动条，用transform计算内容区位置，position：fixed将实效。若有.fixed-tab，强制使用native滚动条；备选方案，略粗暴
            // if($(pageContent).find('.fixed-tab').length>0){
            //     $pageContent.addClass('native-scroll');
            //     return;
            // }
            this.scroller = new IScroll(pageContent, options); // jshint ignore:line
            //和native滚动统一起来
            this._bindEventToDomWhenJs();
            $.initPullToRefresh = $._pullToRefreshJSScroll.initPullToRefresh;
            $.pullToRefreshDone = $._pullToRefreshJSScroll.pullToRefreshDone;
            $.pullToRefreshTrigger = $._pullToRefreshJSScroll.pullToRefreshTrigger;
            $.destroyToRefresh = $._pullToRefreshJSScroll.destroyToRefresh;
            $pageContent.addClass('javascript-scroll');
            if (!useTransform) {
                $pageContent.find('.content-inner').css({
                    width: '100%',
                    position: 'absolute'
                });
            }

            //如果页面本身已经进行了原生滚动，那么把这个滚动换成JS的滚动
            var nativeScrollTop = this.$pageContent[0].scrollTop;
            if(nativeScrollTop) {
                this.$pageContent[0].scrollTop = 0;
                this.scrollTop(nativeScrollTop);
            }
        } else {
            $pageContent.addClass('native-scroll');
        }
    };
    Scroller.prototype = {
        _defaults: {
            type: 'native',
        },
        _bindEventToDomWhenJs: function() {
            //"scrollStart", //the scroll started.
            //"scroll", //the content is scrolling. Available only in scroll-probe.js edition. See onScroll event.
            //"scrollEnd", //content stopped scrolling.
            if (this.scroller) {
                var self = this;
                this.scroller.on('scrollStart', function() {
                    self.$pageContent.trigger('scrollstart');
                });
                this.scroller.on('scroll', function() {
                    self.$pageContent.trigger('scroll');
                });
                this.scroller.on('scrollEnd', function() {
                    self.$pageContent.trigger('scrollend');
                });
            } else {
                //TODO: 实现native的scrollStart和scrollEnd
            }
        },
        scrollTop: function(top, dur) {
            if (this.scroller) {
                if (top !== undefined) {
                    this.scroller.scrollTo(0, -1 * top, dur);
                } else {
                    return this.scroller.getComputedPosition().y * -1;
                }
            } else {
                return this.$pageContent.scrollTop(top, dur);
            }
            return this;
        },
        scrollLeft: function(left, dur) {
            if (this.scroller) {
                if (left !== undefined) {
                    this.scroller.scrollTo(-1 * left, 0);
                } else {
                    return this.scroller.getComputedPosition().x * -1;
                }
            } else {
                return this.$pageContent.scrollTop(left, dur);
            }
            return this;
        },
        on: function(event, callback) {
            if (this.scroller) {
                this.scroller.on(event, function() {
                    callback.call(this.wrapper);
                });
            } else {
                this.$pageContent.on(event, callback);
            }
            return this;
        },
        off: function(event, callback) {
            if (this.scroller) {
                this.scroller.off(event, callback);
            } else {
                this.$pageContent.off(event, callback);
            }
            return this;
        },
        refresh: function() {
            if (this.scroller) this.scroller.refresh();
            return this;
        },
        scrollHeight: function() {
            if (this.scroller) {
                return this.scroller.scrollerHeight;
            } else {
                return this.$pageContent[0].scrollHeight;
            }
        }

    };

    //Scroller PLUGIN DEFINITION
    // =======================

    function Plugin(option) {
        var args = Array.apply(null, arguments);
        args.shift();
        var internal_return;

        this.each(function() {

            var $this = $(this);

            var options = $.extend({}, $this.dataset(), typeof option === 'object' && option);

            var data = $this.data('scroller');
            //如果 scroller 没有被初始化，对scroller 进行初始化r
            if (!data) {
                //获取data-api的
                $this.data('scroller', (data = new Scroller(this, options)));

            }
            if (typeof option === 'string' && typeof data[option] === 'function') {
                internal_return = data[option].apply(data, args);
                if (internal_return !== undefined)
            return false;
            }

        });

        if (internal_return !== undefined)
            return internal_return;
        else
            return this;

    }

    var old = $.fn.scroller;

    $.fn.scroller = Plugin;
    $.fn.scroller.Constructor = Scroller;


    // Scroll NO CONFLICT
    // =================

    $.fn.scroller.noConflict = function() {
        $.fn.scroller = old;
        return this;
    };
    //添加data-api
    $(function() {
        $('[data-toggle="scroller"]').scroller();
    });

    //统一的接口,带有 .javascript-scroll 的content 进行刷新
    $.refreshScroller = function(content) {
        if (content) {
            $(content).scroller('refresh');
        } else {
            $('.javascript-scroll').each(function() {
                $(this).scroller('refresh');
            });
        }

    };
    //全局初始化方法，会对页面上的 [data-toggle="scroller"]，.content. 进行滚动条初始化
    $.initScroller = function(option) {
        this.options = $.extend({}, typeof option === 'object' && option);
        $('[data-toggle="scroller"],.content').scroller(option);
    };
    //获取scroller对象
    $.getScroller = function(content) {
        //以前默认只能有一个无限滚动，因此infinitescroll都是加在content上，现在允许里面有多个，因此要判断父元素是否有content
        content = content.hasClass('content') ? content : content.parents('.content');
        if (content) {
            return $(content).data('scroller');
        } else {
            return $('.content.javascript-scroll').data('scroller');
        }
    };
    //检测滚动类型,
    //‘js’: javascript 滚动条
    //‘native’: 原生滚动条
    $.detectScrollerType = function(content) {
        if (content) {
            if ($(content).data('scroller') && $(content).data('scroller').scroller) {
                return 'js';
            } else {
                return 'native';
            }
        }
    };

}(Zepto);

/* ===============================================================================
************   Tabs   ************
=============================================================================== */
+function ($) {
    "use strict";

    var showTab = function (tab, tabLink, force) {
        var newTab = $(tab);
        if (arguments.length === 2) {
            if (typeof tabLink === 'boolean') {
                force = tabLink;
            }
        }
        if (newTab.length === 0) return false;
        if (newTab.hasClass('active')) {
            if (force) newTab.trigger('show');
            return false;
        }
        var tabs = newTab.parent('.tabs');
        if (tabs.length === 0) return false;

        // Animated tabs
        /*var isAnimatedTabs = tabs.parent().hasClass('tabs-animated-wrap');
          if (isAnimatedTabs) {
          tabs.transform('translate3d(' + -newTab.index() * 100 + '%,0,0)');
          }*/

        // Remove active class from old tabs
        var oldTab = tabs.children('.tab.active').removeClass('active');
        // Add active class to new tab
        newTab.addClass('active');
        // Trigger 'show' event on new tab
        newTab.trigger('show');

        // Update navbars in new tab
        /*if (!isAnimatedTabs && newTab.find('.navbar').length > 0) {
        // Find tab's view
        var viewContainer;
        if (newTab.hasClass(app.params.viewClass)) viewContainer = newTab[0];
        else viewContainer = newTab.parents('.' + app.params.viewClass)[0];
        app.sizeNavbars(viewContainer);
        }*/

        // Find related link for new tab
        if (tabLink) tabLink = $(tabLink);
        else {
            // Search by id
            if (typeof tab === 'string') tabLink = $('.tab-link[href="' + tab + '"]');
            else tabLink = $('.tab-link[href="#' + newTab.attr('id') + '"]');
            // Search by data-tab
            if (!tabLink || tabLink && tabLink.length === 0) {
                $('[data-tab]').each(function () {
                    if (newTab.is($(this).attr('data-tab'))) tabLink = $(this);
                });
            }
        }
        if (tabLink.length === 0) return;

        // Find related link for old tab
        var oldTabLink;
        if (oldTab && oldTab.length > 0) {
            // Search by id
            var oldTabId = oldTab.attr('id');
            if (oldTabId) oldTabLink = $('.tab-link[href="#' + oldTabId + '"]');
            // Search by data-tab
            if (!oldTabLink || oldTabLink && oldTabLink.length === 0) {
                $('[data-tab]').each(function () {
                    if (oldTab.is($(this).attr('data-tab'))) oldTabLink = $(this);
                });
            }
        }

        // Update links' classes
        if (tabLink && tabLink.length > 0) tabLink.addClass('active');
        if (oldTabLink && oldTabLink.length > 0) oldTabLink.removeClass('active');
        tabLink.trigger('active');

        //app.refreshScroller();

        return true;
    };

    var old = $.showTab;
    $.showTab = showTab;

    $.showTab.noConflict = function () {
        $.showTab = old;
        return this;
    };
    //a标签上的click事件，在iscroll下响应有问题
    $(document).on("click", ".tab-link", function(e) {
        e.preventDefault();
        var clicked = $(this);
        showTab(clicked.data("tab") || clicked.attr('href'), clicked);
    });


}(Zepto);

/* ===============================================================================
************   Tabs   ************
=============================================================================== */
+function ($) {
    "use strict";
    $.initFixedTab = function(){
        var $fixedTab = $('.fixed-tab');
        if ($fixedTab.length === 0) return;
        $('.fixed-tab').fixedTab();//默认{offset: 0}
    };
    var FixedTab = function(pageContent, _options) {
        var $pageContent = this.$pageContent = $(pageContent);
        var shadow = $pageContent.clone();
        var fixedTop = $pageContent[0].getBoundingClientRect().top;

        shadow.css('visibility', 'hidden');
        this.options = $.extend({}, this._defaults, {
            fixedTop: fixedTop,
            shadow: shadow,
            offset: 0
        }, _options);

        this._bindEvents();
    };

    FixedTab.prototype = {
        _defaults: {
            offset: 0,
        },
        _bindEvents: function() {
            this.$pageContent.parents('.content').on('scroll', this._scrollHandler.bind(this));
            this.$pageContent.on('active', '.tab-link', this._tabLinkHandler.bind(this));
        },
        _tabLinkHandler: function(ev) {
            var isFixed = $(ev.target).parents('.buttons-fixed').length > 0;
            var fixedTop = this.options.fixedTop;
            var offset = this.options.offset;
            $.refreshScroller();
            if (!isFixed) return;
            this.$pageContent.parents('.content').scrollTop(fixedTop - offset);
        },
        // 滚动核心代码
        _scrollHandler: function(ev) {
            var $scroller = $(ev.target);
            var $pageContent = this.$pageContent;
            var shadow = this.options.shadow;
            var offset = this.options.offset;
            var fixedTop = this.options.fixedTop;
            var scrollTop = $scroller.scrollTop();
            var isFixed = scrollTop >= fixedTop - offset;
            if (isFixed) {
                shadow.insertAfter($pageContent);
                $pageContent.addClass('buttons-fixed').css('top', offset);
            } else {
                shadow.remove();
                $pageContent.removeClass('buttons-fixed').css('top', 0);
            }
        }
    };

    //FixedTab PLUGIN DEFINITION
    // =======================

    function Plugin(option) {
        var args = Array.apply(null, arguments);
        args.shift();
        this.each(function() {
            var $this = $(this);
            var options = $.extend({}, $this.dataset(), typeof option === 'object' && option);
            var data = $this.data('fixedtab');
            if (!data) {
                //获取data-api的
                $this.data('fixedtab', (data = new FixedTab(this, options)));
            }
        });

    }
    $.fn.fixedTab = Plugin;
    $.fn.fixedTab.Constructor = FixedTab;
    $(document).on('pageInit',function(){
        $.initFixedTab();
    });



}(Zepto);

+ function($) {
    "use strict";
    //这里实在js滚动时使用的下拉刷新代码。

    var refreshTime = 0;
    var initPullToRefreshJS = function(pageContainer) {
        var eventsTarget = $(pageContainer);
        if (!eventsTarget.hasClass('pull-to-refresh-content')) {
            eventsTarget = eventsTarget.find('.pull-to-refresh-content');
        }
        if (!eventsTarget || eventsTarget.length === 0) return;

        var page = eventsTarget.hasClass('content') ? eventsTarget : eventsTarget.parents('.content');
        var scroller = $.getScroller(page[0]);
        if(!scroller) return;


        var container = eventsTarget;

        function handleScroll() {
            if (container.hasClass('refreshing')) return;
            if (scroller.scrollTop() * -1 >= 44) {
                container.removeClass('pull-down').addClass('pull-up');
            } else {
                container.removeClass('pull-up').addClass('pull-down');
            }
        }

        function handleRefresh() {
            if (container.hasClass('refreshing')) return;
            container.removeClass('pull-down pull-up');
            container.addClass('refreshing transitioning');
            container.trigger('refresh');
            container.find(".pull-to-refresh-arrow").html('');
            refreshTime = +new Date();
        }
        scroller.on('scroll', handleScroll);
        scroller.scroller.on('ptr', handleRefresh);

        // Detach Events on page remove
        function destroyPullToRefresh() {
            scroller.off('scroll', handleScroll);
            scroller.scroller.off('ptr', handleRefresh);
        }
        eventsTarget[0].destroyPullToRefresh = destroyPullToRefresh;

    };

    var pullToRefreshDoneJS = function(container) {
        container = $(container);
        if (container.length === 0) container = $('.pull-to-refresh-content.refreshing');
        if (container.length === 0) return;
        var interval = (+new Date()) - refreshTime;
        var timeOut = interval > 1000 ? 0 : 1000 - interval; //long than bounce time
        var scroller = $.getScroller(container);
        setTimeout(function() {
            scroller.refresh();
            container.removeClass('refreshing');
            container.transitionEnd(function() {
                container.removeClass("transitioning");
            });
        }, timeOut);
    };
    var pullToRefreshTriggerJS = function(container) {
        container = $(container);
        if (container.length === 0) container = $('.pull-to-refresh-content');
        if (container.hasClass('refreshing')) return;
        container.addClass('refreshing');
        var scroller = $.getScroller(container);
        scroller.scrollTop(44 + 1, 200);
        container.trigger('refresh');
    };

    var destroyPullToRefreshJS = function(pageContainer) {
        pageContainer = $(pageContainer);
        var pullToRefreshContent = pageContainer.hasClass('pull-to-refresh-content') ? pageContainer : pageContainer.find('.pull-to-refresh-content');
        if (pullToRefreshContent.length === 0) return;
        if (pullToRefreshContent[0].destroyPullToRefresh) pullToRefreshContent[0].destroyPullToRefresh();
    };

    $._pullToRefreshJSScroll = {
        "initPullToRefresh": initPullToRefreshJS,
        "pullToRefreshDone": pullToRefreshDoneJS,
        "pullToRefreshTrigger": pullToRefreshTriggerJS,
        "destroyPullToRefresh": destroyPullToRefreshJS,
    };
}(Zepto); // jshint ignore:line

+ function($) {
    'use strict';
    $.initPullToRefresh = function(pageContainer) {
        var eventsTarget = $(pageContainer);
        if (!eventsTarget.hasClass('pull-to-refresh-content')) {
            eventsTarget = eventsTarget.find('.pull-to-refresh-content');
        }
        if (!eventsTarget || eventsTarget.length === 0) return;

        var isTouched, isMoved, touchesStart = {},
            isScrolling, touchesDiff, touchStartTime, container, refresh = false,
            useTranslate = false,
            startTranslate = 0,
            translate, scrollTop, wasScrolled, triggerDistance, dynamicTriggerDistance;

        container = eventsTarget;

        // Define trigger distance
        if (container.attr('data-ptr-distance')) {
            dynamicTriggerDistance = true;
        } else {
            triggerDistance = 44;
        }

        function handleTouchStart(e) {
            if (isTouched) {
                if ($.device.android) {
                    if ('targetTouches' in e && e.targetTouches.length > 1) return;
                } else return;
            }
            isMoved = false;
            isTouched = true;
            isScrolling = undefined;
            wasScrolled = undefined;
            touchesStart.x = e.type === 'touchstart' ? e.targetTouches[0].pageX : e.pageX;
            touchesStart.y = e.type === 'touchstart' ? e.targetTouches[0].pageY : e.pageY;
            touchStartTime = (new Date()).getTime();
            /*jshint validthis:true */
            container = $(this);
        }

        function handleTouchMove(e) {
            if (!isTouched) return;
            var pageX = e.type === 'touchmove' ? e.targetTouches[0].pageX : e.pageX;
            var pageY = e.type === 'touchmove' ? e.targetTouches[0].pageY : e.pageY;
            if (typeof isScrolling === 'undefined') {
                isScrolling = !!(isScrolling || Math.abs(pageY - touchesStart.y) > Math.abs(pageX - touchesStart.x));
            }
            if (!isScrolling) {
                isTouched = false;
                return;
            }

            scrollTop = container[0].scrollTop;
            if (typeof wasScrolled === 'undefined' && scrollTop !== 0) wasScrolled = true;

            if (!isMoved) {
                /*jshint validthis:true */
                container.removeClass('transitioning');
                if (scrollTop > container[0].offsetHeight) {
                    isTouched = false;
                    return;
                }
                if (dynamicTriggerDistance) {
                    triggerDistance = container.attr('data-ptr-distance');
                    if (triggerDistance.indexOf('%') >= 0) triggerDistance = container[0].offsetHeight * parseInt(triggerDistance, 10) / 100;
                }
                startTranslate = container.hasClass('refreshing') ? triggerDistance : 0;
                if (container[0].scrollHeight === container[0].offsetHeight || !$.device.ios) {
                    useTranslate = true;
                } else {
                    useTranslate = false;
                }
                useTranslate = true;
            }
            isMoved = true;
            touchesDiff = pageY - touchesStart.y;

            if (touchesDiff > 0 && scrollTop <= 0 || scrollTop < 0) {
                // iOS 8 fix
                if ($.device.ios && parseInt($.device.osVersion.split('.')[0], 10) > 7 && scrollTop === 0 && !wasScrolled) useTranslate = true;

                if (useTranslate) {
                    e.preventDefault();
                    translate = (Math.pow(touchesDiff, 0.85) + startTranslate);
                    container.transform('translate3d(0,' + translate + 'px,0)');
                } else {}
                if ((useTranslate && Math.pow(touchesDiff, 0.85) > triggerDistance) || (!useTranslate && touchesDiff >= triggerDistance * 2)) {
                    refresh = true;
                    container.addClass('pull-up').removeClass('pull-down');
                    container.find(".pull-to-refresh-arrow").html("松开立即刷新");
                } else {
                    refresh = false;
                    container.removeClass('pull-up').addClass('pull-down');
                    container.find(".pull-to-refresh-arrow").html("下拉可以刷新");
                }
            } else {
                container.removeClass('pull-up pull-down');
                container.find(".pull-to-refresh-arrow").html("");
                refresh = false;
                return;
            }
        }

        function handleTouchEnd() {
            if (!isTouched || !isMoved) {
                isTouched = false;
                isMoved = false;
                return;
            }
            if (translate) {
                container.addClass('transitioning');
                translate = 0;
            }
            container.transform('');
            if (refresh) {
                //防止二次触发
                if(container.hasClass('refreshing')) return;
                container.addClass('refreshing');
                container.trigger('refresh');
                container.find(".pull-to-refresh-arrow").html("");
            } else {
                container.removeClass('pull-down');
            }
            isTouched = false;
            isMoved = false;
        }

        // Attach Events
        eventsTarget.on($.touchEvents.start, handleTouchStart);
        eventsTarget.on($.touchEvents.move, handleTouchMove);
        eventsTarget.on($.touchEvents.end, handleTouchEnd);


        function destroyPullToRefresh() {
            eventsTarget.off($.touchEvents.start, handleTouchStart);
            eventsTarget.off($.touchEvents.move, handleTouchMove);
            eventsTarget.off($.touchEvents.end, handleTouchEnd);
        }
        eventsTarget[0].destroyPullToRefresh = destroyPullToRefresh;

    };
    $.pullToRefreshDone = function(container) {
        $(window).scrollTop(0);//解决微信下拉刷新顶部消失的问题
        container = $(container);
        if (container.length === 0) container = $('.pull-to-refresh-content.refreshing');
        container.removeClass('refreshing').addClass('transitioning');
        container.find('pull-to-refresh-arrow').html('');
        container.transitionEnd(function() {
            container.removeClass('transitioning pull-up pull-down');
        });
    };
    $.pullToRefreshTrigger = function(container) {
        container = $(container);
        if (container.length === 0) container = $('.pull-to-refresh-content');
        if (container.hasClass('refreshing')) return;
        container.addClass('transitioning refreshing');
        container.trigger('refresh');
    };

    $.destroyPullToRefresh = function(pageContainer) {
        pageContainer = $(pageContainer);
        var pullToRefreshContent = pageContainer.hasClass('pull-to-refresh-content') ? pageContainer : pageContainer.find('.pull-to-refresh-content');
        if (pullToRefreshContent.length === 0) return;
        if (pullToRefreshContent[0].destroyPullToRefresh) pullToRefreshContent[0].destroyPullToRefresh();
    };

    //这里是否需要写到 scroller 中去？
/*    $.initPullToRefresh = function(pageContainer) {
        var $pageContainer = $(pageContainer);
        $pageContainer.each(function(index, item) {
            if ($.detectScrollerType(item) === 'js') {
                $._pullToRefreshJSScroll.initPullToRefresh(item);
            } else {
                initPullToRefresh(item);
            }
        });
    };


    $.pullToRefreshDone = function(pageContainer) {
        var $pageContainer = $(pageContainer);
        $pageContainer.each(function(index, item) {
            if ($.detectScrollerType(item) === 'js') {
                $._pullToRefreshJSScroll.pullToRefreshDone(item);
            } else {
                pullToRefreshDone(item);
            }
        });
    };


    $.pullToRefreshTrigger = function(pageContainer) {
       var $pageContainer = $(pageContainer);
        $pageContainer.each(function(index, item) {
            if ($.detectScrollerType(item) === 'js') {
                $._pullToRefreshJSScroll.pullToRefreshTrigger(item);
            } else {
                pullToRefreshTrigger(item);
            }
        });
    };

    $.destroyPullToRefresh = function(pageContainer) {
        var $pageContainer = $(pageContainer);
        $pageContainer.each(function(index, item) {
            if ($.detectScrollerType(item) === 'js') {
                $._pullToRefreshJSScroll.destroyPullToRefresh(item);
            } else {
                destroyPullToRefresh(item);
            }
        });
    };
*/

}(Zepto); //jshint ignore:line

+ function($) {
    'use strict';

    function handleInfiniteScroll() {
        /*jshint validthis:true */
        var inf = $(this);
        var scroller = $.getScroller(inf);
        var scrollTop = scroller.scrollTop();
        var scrollHeight = scroller.scrollHeight();
        var height = inf[0].offsetHeight;
        var distance = inf[0].getAttribute('data-distance');
        var virtualListContainer = inf.find('.virtual-list');
        var virtualList;
        var onTop = inf.hasClass('infinite-scroll-top');
        if (!distance) distance = 50;
        if (typeof distance === 'string' && distance.indexOf('%') >= 0) {
            distance = parseInt(distance, 10) / 100 * height;
        }
        if (distance > height) distance = height;
        if (onTop) {
            if (scrollTop < distance) {
                inf.trigger('infinite');
            }
        } else {
            if (scrollTop + height >= scrollHeight - distance) {
                if (virtualListContainer.length > 0) {
                    virtualList = virtualListContainer[0].f7VirtualList;
                    if (virtualList && !virtualList.reachEnd) return;
                }
                inf.trigger('infinite');
            }
        }

    }
    $.attachInfiniteScroll = function(infiniteContent) {
        $.getScroller(infiniteContent).on('scroll', handleInfiniteScroll);
    };
    $.detachInfiniteScroll = function(infiniteContent) {
        $.getScroller(infiniteContent).off('scroll', handleInfiniteScroll);
    };

    $.initInfiniteScroll = function(pageContainer) {
        pageContainer = $(pageContainer);
        var infiniteContent = pageContainer.hasClass('infinite-scroll')?pageContainer:pageContainer.find('.infinite-scroll');
        if (infiniteContent.length === 0) return;
        $.attachInfiniteScroll(infiniteContent);
        //如果是顶部无限刷新，要将滚动条初始化于最下端
        pageContainer.forEach(function(v){
            if($(v).hasClass('infinite-scroll-top')){
                var height = v.scrollHeight - v.clientHeight;
                $(v).scrollTop(height);
            }
        });
        function detachEvents() {
            $.detachInfiniteScroll(infiniteContent);
            pageContainer.off('pageBeforeRemove', detachEvents);
        }
        pageContainer.on('pageBeforeRemove', detachEvents);
    };
}(Zepto);

+function ($) {
    "use strict";
    $(function() {
        $(document).on("focus", ".searchbar input", function(e) {
            var $input = $(e.target);
            $input.parents(".searchbar").addClass("searchbar-active");
        });
        $(document).on("click", ".searchbar-cancel", function(e) {
            var $btn = $(e.target);
            $btn.parents(".searchbar").removeClass("searchbar-active");
        });
        $(document).on("blur", ".searchbar input", function(e) {
            var $input = $(e.target);
            $input.parents(".searchbar").removeClass("searchbar-active");
        });
    });
}(Zepto);

/*======================================================
************   Panels   ************
======================================================*/
/*jshint unused: false*/
+function ($) {
    "use strict";
    $.allowPanelOpen = true;
    $.openPanel = function (panel) {
        if (!$.allowPanelOpen) return false;
        if(panel === 'left' || panel === 'right') panel = ".panel-" + panel;  //可以传入一个方向
        panel = panel ? $(panel) : $(".panel").eq(0);
        var direction = panel.hasClass("panel-right") ? "right" : "left";
        if (panel.length === 0 || panel.hasClass('active')) return false;
        $.closePanel(); // Close if some panel is opened
        $.allowPanelOpen = false;
        var effect = panel.hasClass('panel-reveal') ? 'reveal' : 'cover';
        panel.css({display: 'block'}).addClass('active');
        panel.trigger('open');

        // Trigger reLayout
        var clientLeft = panel[0].clientLeft;

        // Transition End;
        var transitionEndTarget = effect === 'reveal' ? $($.getCurrentPage()) : panel;
        var openedTriggered = false;

        function panelTransitionEnd() {
            transitionEndTarget.transitionEnd(function (e) {
                if (e.target === transitionEndTarget[0]) {
                    if (panel.hasClass('active')) {
                        panel.trigger('opened');
                    }
                    else {
                        panel.trigger('closed');
                    }
            $.allowPanelOpen = true;
                }
                else panelTransitionEnd();
            });
        }
        panelTransitionEnd();

        $(document.body).addClass('with-panel-' + direction + '-' + effect);
        return true;
    };
    $.closePanel = function () {
        var activePanel = $('.panel.active');
        if (activePanel.length === 0) return false;
        var effect = activePanel.hasClass('panel-reveal') ? 'reveal' : 'cover';
        var panelPosition = activePanel.hasClass('panel-left') ? 'left' : 'right';
        activePanel.removeClass('active');
        var transitionEndTarget = effect === 'reveal' ? $('.page') : activePanel;
        activePanel.trigger('close');
        $.allowPanelOpen = false;

        transitionEndTarget.transitionEnd(function () {
            if (activePanel.hasClass('active')) return;
            activePanel.css({display: ''});
            activePanel.trigger('closed');
            $('body').removeClass('panel-closing');
            $.allowPanelOpen = true;
        });

        $('body').addClass('panel-closing').removeClass('with-panel-' + panelPosition + '-' + effect);
    };

    $(document).on("click", ".open-panel", function(e) {
        var panel = $(e.target).data('panel');
        $.openPanel(panel);
    });
    $(document).on("click", ".close-panel, .panel-overlay", function(e) {
        $.closePanel();
    });
    /*======================================================
     ************   Swipe panels   ************
     ======================================================*/
    $.initSwipePanels = function () {
        var panel, side;
        var swipePanel = $.smConfig.swipePanel;
        var swipePanelOnlyClose = $.smConfig.swipePanelOnlyClose;
        var swipePanelCloseOpposite = true;
        var swipePanelActiveArea = false;
        var swipePanelThreshold = 2;
        var swipePanelNoFollow = false;

        if(!(swipePanel || swipePanelOnlyClose)) return;

        var panelOverlay = $('.panel-overlay');
        var isTouched, isMoved, isScrolling, touchesStart = {}, touchStartTime, touchesDiff, translate, opened, panelWidth, effect, direction;
        var views = $('.page');

        function handleTouchStart(e) {
            if (!$.allowPanelOpen || (!swipePanel && !swipePanelOnlyClose) || isTouched) return;
            if ($('.modal-in, .photo-browser-in').length > 0) return;
            if (!(swipePanelCloseOpposite || swipePanelOnlyClose)) {
                if ($('.panel.active').length > 0 && !panel.hasClass('active')) return;
            }
            touchesStart.x = e.type === 'touchstart' ? e.targetTouches[0].pageX : e.pageX;
            touchesStart.y = e.type === 'touchstart' ? e.targetTouches[0].pageY : e.pageY;
            if (swipePanelCloseOpposite || swipePanelOnlyClose) {
                if ($('.panel.active').length > 0) {
                    side = $('.panel.active').hasClass('panel-left') ? 'left' : 'right';
                }
                else {
                    if (swipePanelOnlyClose) return;
                    side = swipePanel;
                }
                if (!side) return;
            }
            panel = $('.panel.panel-' + side);
            if(!panel[0]) return;
            opened = panel.hasClass('active');
            if (swipePanelActiveArea && !opened) {
                if (side === 'left') {
                    if (touchesStart.x > swipePanelActiveArea) return;
                }
                if (side === 'right') {
                    if (touchesStart.x < window.innerWidth - swipePanelActiveArea) return;
                }
            }
            isMoved = false;
            isTouched = true;
            isScrolling = undefined;

            touchStartTime = (new Date()).getTime();
            direction = undefined;
        }
        function handleTouchMove(e) {
            if (!isTouched) return;
            if(!panel[0]) return;
            if (e.f7PreventPanelSwipe) return;
            var pageX = e.type === 'touchmove' ? e.targetTouches[0].pageX : e.pageX;
            var pageY = e.type === 'touchmove' ? e.targetTouches[0].pageY : e.pageY;
            if (typeof isScrolling === 'undefined') {
                isScrolling = !!(isScrolling || Math.abs(pageY - touchesStart.y) > Math.abs(pageX - touchesStart.x));
            }
            if (isScrolling) {
                isTouched = false;
                return;
            }
            if (!direction) {
                if (pageX > touchesStart.x) {
                    direction = 'to-right';
                }
                else {
                    direction = 'to-left';
                }

                if (
                        side === 'left' &&
                        (
                         direction === 'to-left' && !panel.hasClass('active')
                        ) ||
                        side === 'right' &&
                        (
                         direction === 'to-right' && !panel.hasClass('active')
                        )
                   )
                {
                    isTouched = false;
                    return;
                }
            }

            if (swipePanelNoFollow) {
                var timeDiff = (new Date()).getTime() - touchStartTime;
                if (timeDiff < 300) {
                    if (direction === 'to-left') {
                        if (side === 'right') $.openPanel(side);
                        if (side === 'left' && panel.hasClass('active')) $.closePanel();
                    }
                    if (direction === 'to-right') {
                        if (side === 'left') $.openPanel(side);
                        if (side === 'right' && panel.hasClass('active')) $.closePanel();
                    }
                }
                isTouched = false;
                console.log(3);
                isMoved = false;
                return;
            }

            if (!isMoved) {
                effect = panel.hasClass('panel-cover') ? 'cover' : 'reveal';
                if (!opened) {
                    panel.show();
                    panelOverlay.show();
                }
                panelWidth = panel[0].offsetWidth;
                panel.transition(0);
                /*
                   if (panel.find('.' + app.params.viewClass).length > 0) {
                   if (app.sizeNavbars) app.sizeNavbars(panel.find('.' + app.params.viewClass)[0]);
                   }
                   */
            }

            isMoved = true;

            e.preventDefault();
            var threshold = opened ? 0 : -swipePanelThreshold;
            if (side === 'right') threshold = -threshold;

            touchesDiff = pageX - touchesStart.x + threshold;

            if (side === 'right') {
                translate = touchesDiff  - (opened ? panelWidth : 0);
                if (translate > 0) translate = 0;
                if (translate < -panelWidth) {
                    translate = -panelWidth;
                }
            }
            else {
                translate = touchesDiff  + (opened ? panelWidth : 0);
                if (translate < 0) translate = 0;
                if (translate > panelWidth) {
                    translate = panelWidth;
                }
            }
            if (effect === 'reveal') {
                views.transform('translate3d(' + translate + 'px,0,0)').transition(0);
                panelOverlay.transform('translate3d(' + translate + 'px,0,0)');
                //app.pluginHook('swipePanelSetTransform', views[0], panel[0], Math.abs(translate / panelWidth));
            }
            else {
                panel.transform('translate3d(' + translate + 'px,0,0)').transition(0);
                //app.pluginHook('swipePanelSetTransform', views[0], panel[0], Math.abs(translate / panelWidth));
            }
        }
        function handleTouchEnd(e) {
            if (!isTouched || !isMoved) {
                isTouched = false;
                isMoved = false;
                return;
            }
            isTouched = false;
            isMoved = false;
            var timeDiff = (new Date()).getTime() - touchStartTime;
            var action;
            var edge = (translate === 0 || Math.abs(translate) === panelWidth);

            if (!opened) {
                if (translate === 0) {
                    action = 'reset';
                }
                else if (
                        timeDiff < 300 && Math.abs(translate) > 0 ||
                        timeDiff >= 300 && (Math.abs(translate) >= panelWidth / 2)
                        ) {
                            action = 'swap';
                        }
                else {
                    action = 'reset';
                }
            }
            else {
                if (translate === -panelWidth) {
                    action = 'reset';
                }
                else if (
                        timeDiff < 300 && Math.abs(translate) >= 0 ||
                        timeDiff >= 300 && (Math.abs(translate) <= panelWidth / 2)
                        ) {
                            if (side === 'left' && translate === panelWidth) action = 'reset';
                            else action = 'swap';
                        }
                else {
                    action = 'reset';
                }
            }
            if (action === 'swap') {
                $.allowPanelOpen = true;
                if (opened) {
                    $.closePanel();
                    if (edge) {
                        panel.css({display: ''});
                        $('body').removeClass('panel-closing');
                    }
                }
                else {
                    $.openPanel(side);
                }
                if (edge) $.allowPanelOpen = true;
            }
            if (action === 'reset') {
                if (opened) {
                    $.allowPanelOpen = true;
                    $.openPanel(side);
                }
                else {
                    $.closePanel();
                    if (edge) {
                        $.allowPanelOpen = true;
                        panel.css({display: ''});
                    }
                    else {
                        var target = effect === 'reveal' ? views : panel;
                        $('body').addClass('panel-closing');
                        target.transitionEnd(function () {
                            $.allowPanelOpen = true;
                            panel.css({display: ''});
                            $('body').removeClass('panel-closing');
                        });
                    }
                }
            }
            if (effect === 'reveal') {
                views.transition('');
                views.transform('');
            }
            panel.transition('').transform('');
            panelOverlay.css({display: ''}).transform('');
        }
        $(document).on($.touchEvents.start, handleTouchStart);
        $(document).on($.touchEvents.move, handleTouchMove);
        $(document).on($.touchEvents.end, handleTouchEnd);
    };

    $.initSwipePanels();
}(Zepto);

/**
 * 路由
 *
 * 路由功能将接管页面的链接点击行为，最后达到动画切换的效果，具体如下：
 *  1. 链接对应的是另一个页面，那么则尝试 ajax 加载，然后把新页面里的符合约定的结构提取出来，然后做动画切换；如果没法 ajax 或结构不符合，那么则回退为普通的页面跳转
 *  2. 链接是当前页面的锚点，并且该锚点对应的元素存在且符合路由约定，那么则把该元素做动画切入
 *  3. 浏览器前进后退（history.forward/history.back）时，也使用动画效果
 *  4. 如果链接有 back 这个 class，那么则忽略一切，直接调用 history.back() 来后退
 *
 * 路由功能默认开启，如果需要关闭路由功能，那么在 zepto 之后，msui 脚本之前设置 $.config.router = false 即可（intro.js 中会 extend 到 $.smConfig 中）。
 *
 * 可以设置 $.config.routerFilter 函数来设置当前点击链接是否使用路由功能，实参是 a 链接的 zepto 对象；返回 false 表示不使用 router 功能。
 *
 * ajax 载入新的文档时，并不会执行里面的 js。到目前为止，在开启路由功能时，建议的做法是：
 *  把所有页面的 js 都放到同一个脚本里，js 里面的事件绑定使用委托而不是直接的绑定在元素上（因为动态加载的页面元素还不存在），然后所有页面都引用相同的 js 脚本。非事件类可以通过监控 pageInit 事件，根据里面的 pageId 来做对应区别处理。
 *
 * 如果有需要
 *
 * 对外暴露的方法
 *  - load （原 loadPage 效果一致,但后者已标记为待移除）
 *  - forward
 *  - back
 *
 * 事件
 * pageLoad* 系列在发生 ajax 加载时才会触发；当是块切换或已缓存的情况下，不会发送这些事件
 *  - pageLoadCancel: 如果前一个还没加载完,那么取消并发送该事件
 *  - pageLoadStart: 开始加载
 *  - pageLodComplete: ajax complete 完成
 *  - pageLoadError: ajax 发生 error
 *  - pageAnimationStart: 执行动画切换前，实参是 event，sectionId 和 $section
 *  - pageAnimationEnd: 执行动画完毕，实参是 event，sectionId 和 $section
 *  - beforePageRemove: 新 document 载入且动画切换完毕，旧的 document remove 之前在 window 上触发，实参是 event 和 $pageContainer
 *  - pageRemoved: 新的 document 载入且动画切换完毕，旧的 document remove 之后在 window 上触发
 *  - beforePageSwitch: page 切换前，在 pageAnimationStart 前，beforePageSwitch 之后会做一些额外的处理才触发 pageAnimationStart
 *  - pageInitInternal: （经 init.js 处理后，对外是 pageInit）紧跟着动画完成的事件，实参是 event，sectionId 和 $section
 *
 * 术语
 *  - 文档（document），不带 hash 的 url 关联着的应答 html 结构
 *  - 块（section），一个文档内有指定块标识的元素
 *
 * 路由实现约定
 *  - 每个文档的需要展示的内容必需位于指定的标识（routerConfig.sectionGroupClass）的元素里面，默认是: div.page-group （注意,如果改变这个需要同时改变 less 中的命名）
 *  - 每个块必需带有指定的块标识（routerConfig.pageClass），默认是 .page
 *
 *  即，使用路由功能的每一个文档应当是下面这样的结构（省略 <body> 等）:
 *      <div class="page-group">
 *          <div class="page">xxx</div>
 *          <div class="page">yyy</div>
 *      </div>
 *
 * 另，每一个块都应当有一个唯一的 ID，这样才能通过 #the-id 的形式来切换定位。
 * 当一个块没有 id 时，如果是第一个的默认的需要展示的块，那么会给其添加一个随机的 id；否则，没有 id 的块将不会被切换展示。
 *
 * 通过 history.state/history.pushState 以及用 sessionStorage 来记录当前 state 以及最大的 state id 来辅助前进后退的切换效果，所以在不支持 sessionStorage 的情况下，将不开启路由功能。
 *
 * 为了解决 ajax 载入页面导致重复 ID 以及重复 popup 等功能，上面约定了使用路由功能的所有可展示内容都必需位于指定元素内。从而可以在进行文档间切换时可以进行两个文档的整体移动，切换完毕后再把前一个文档的内容从页面之间移除。
 *
 * 默认地过滤了部分协议的链接，包括 tel:, javascript:, mailto:，这些链接将不会使用路由功能。如果有更多的自定义控制需求，可以在 $.config.routerFilter 实现
 *
 * 注: 以 _ 开头的函数标明用于此处内部使用，可根据需要随时重构变更，不对外确保兼容性。
 *
 */
+function($) {
    'use strict';

    if (!window.CustomEvent) {
        window.CustomEvent = function(type, config) {
            config = config || { bubbles: false, cancelable: false, detail: undefined};
            var e = document.createEvent('CustomEvent');
            e.initCustomEvent(type, config.bubbles, config.cancelable, config.detail);
            return e;
        };

        window.CustomEvent.prototype = window.Event.prototype;
    }

    var EVENTS = {
        pageLoadStart: 'pageLoadStart', // ajax 开始加载新页面前
        pageLoadCancel: 'pageLoadCancel', // 取消前一个 ajax 加载动作后
        pageLoadError: 'pageLoadError', // ajax 加载页面失败后
        pageLoadComplete: 'pageLoadComplete', // ajax 加载页面完成后（不论成功与否）
        pageAnimationStart: 'pageAnimationStart', // 动画切换 page 前
        pageAnimationEnd: 'pageAnimationEnd', // 动画切换 page 结束后
        beforePageRemove: 'beforePageRemove', // 移除旧 document 前（适用于非内联 page 切换）
        pageRemoved: 'pageRemoved', // 移除旧 document 后（适用于非内联 page 切换）
        beforePageSwitch: 'beforePageSwitch', // page 切换前，在 pageAnimationStart 前，beforePageSwitch 之后会做一些额外的处理才触发 pageAnimationStart
        pageInit: 'pageInitInternal' // 目前是定义为一个 page 加载完毕后（实际和 pageAnimationEnd 等同）
    };

    var Util = {
        /**
         * 获取 url 的 fragment（即 hash 中去掉 # 的剩余部分）
         *
         * 如果没有则返回空字符串
         * 如: http://example.com/path/?query=d#123 => 123
         *
         * @param {String} url url
         * @returns {String}
         */
        getUrlFragment: function(url) {
            var hashIndex = url.indexOf('#');
            return hashIndex === -1 ? '' : url.slice(hashIndex + 1);
        },
        /**
         * 获取一个链接相对于当前页面的绝对地址形式
         *
         * 假设当前页面是 http://a.com/b/c
         * 那么有以下情况:
         * d => http://a.com/b/d
         * /e => http://a.com/e
         * #1 => http://a.com/b/c#1
         * http://b.com/f => http://b.com/f
         *
         * @param {String} url url
         * @returns {String}
         */
        getAbsoluteUrl: function(url) {
            var link = document.createElement('a');
            link.setAttribute('href', url);
            var absoluteUrl = link.href;
            link = null;
            return absoluteUrl;
        },
        /**
         * 获取一个 url 的基本部分，即不包括 hash
         *
         * @param {String} url url
         * @returns {String}
         */
        getBaseUrl: function(url) {
            var hashIndex = url.indexOf('#');
            return hashIndex === -1 ? url.slice(0) : url.slice(0, hashIndex);
        },
        /**
         * 把一个字符串的 url 转为一个可获取其 base 和 fragment 等的对象
         *
         * @param {String} url url
         * @returns {UrlObject}
         */
        toUrlObject: function(url) {
            var fullUrl = this.getAbsoluteUrl(url),
                baseUrl = this.getBaseUrl(fullUrl),
                fragment = this.getUrlFragment(url);

            return {
                base: baseUrl,
                full: fullUrl,
                original: url,
                fragment: fragment
            };
        },
        /**
         * 判断浏览器是否支持 sessionStorage，支持返回 true，否则返回 false
         * @returns {Boolean}
         */
        supportStorage: function() {
            var mod = 'sm.router.storage.ability';
            try {
                sessionStorage.setItem(mod, mod);
                sessionStorage.removeItem(mod);
                return true;
            } catch(e) {
                return false;
            }
        }
    };

    var routerConfig = {
        sectionGroupClass: 'page-group',
        // 表示是当前 page 的 class
        curPageClass: 'page-current',
        // 用来辅助切换时表示 page 是 visible 的,
        // 之所以不用 curPageClass，是因为 page-current 已被赋予了「当前 page」这一含义而不仅仅是 display: block
        // 并且，别的地方已经使用了，所以不方便做变更，故新增一个
        visiblePageClass: 'page-visible',
        // 表示是 page 的 class，注意，仅是标志 class，而不是所有的 class
        pageClass: 'page'
    };

    var DIRECTION = {
        leftToRight: 'from-left-to-right',
        rightToLeft: 'from-right-to-left'
    };

    var theHistory = window.history;

    var Router = function() {
        this.sessionNames = {
            currentState: 'sm.router.currentState',
            maxStateId: 'sm.router.maxStateId'
        };

        this._init();
        this.xhr = null;
        window.addEventListener('popstate', this._onPopState.bind(this));
    };

    /**
     * 初始化
     *
     * - 把当前文档内容缓存起来
     * - 查找默认展示的块内容，查找顺序如下
     *      1. id 是 url 中的 fragment 的元素
     *      2. 有当前块 class 标识的第一个元素
     *      3. 第一个块
     * - 初始页面 state 处理
     *
     * @private
     */
    Router.prototype._init = function() {

        this.$view = $('body');

        // 用来保存 document 的 map
        this.cache = {};
        var $doc = $(document);
        var currentUrl = location.href;
        this._saveDocumentIntoCache($doc, currentUrl);

        var curPageId;

        var currentUrlObj = Util.toUrlObject(currentUrl);
        var $allSection = $doc.find('.' + routerConfig.pageClass);
        var $visibleSection = $doc.find('.' + routerConfig.curPageClass);
        var $curVisibleSection = $visibleSection.eq(0);
        var $hashSection;

        if (currentUrlObj.fragment) {
            $hashSection = $doc.find('#' + currentUrlObj.fragment);
        }
        if ($hashSection && $hashSection.length) {
            $visibleSection = $hashSection.eq(0);
        } else if (!$visibleSection.length) {
            $visibleSection = $allSection.eq(0);
        }
        if (!$visibleSection.attr('id')) {
            $visibleSection.attr('id', this._generateRandomId());
        }

        if ($curVisibleSection.length &&
            ($curVisibleSection.attr('id') !== $visibleSection.attr('id'))) {
            // 在 router 到 inner page 的情况下，刷新（或者直接访问该链接）
            // 直接切换 class 会有「闪」的现象,或许可以采用 animateSection 来减缓一下
            $curVisibleSection.removeClass(routerConfig.curPageClass);
            $visibleSection.addClass(routerConfig.curPageClass);
        } else {
            $visibleSection.addClass(routerConfig.curPageClass);
        }
        curPageId = $visibleSection.attr('id');


        // 新进入一个使用 history.state 相关技术的页面时，如果第一个 state 不 push/replace,
        // 那么在后退回该页面时，将不触发 popState 事件
        if (theHistory.state === null) {
            var curState = {
                id: this._getNextStateId(),
                url: Util.toUrlObject(currentUrl),
                pageId: curPageId
            };

            theHistory.replaceState(curState, '', currentUrl);
            this._saveAsCurrentState(curState);
            this._incMaxStateId();
        }
    };

    /**
     * 切换到 url 指定的块或文档
     *
     * 如果 url 指向的是当前页面，那么认为是切换块；
     * 否则是切换文档
     *
     * @param {String} url url
     * @param {Boolean=} ignoreCache 是否强制请求不使用缓存，对 document 生效，默认是 false
     */
    Router.prototype.load = function(url, ignoreCache) {
        if (ignoreCache === undefined) {
            ignoreCache = false;
        }

        if (this._isTheSameDocument(location.href, url)) {
            this._switchToSection(Util.getUrlFragment(url));
        } else {
            this._saveDocumentIntoCache($(document), location.href);
            this._switchToDocument(url, ignoreCache);
        }
    };

    /**
     * 调用 history.forward()
     */
    Router.prototype.forward = function() {
        theHistory.forward();
    };

    /**
     * 调用 history.back()
     */
    Router.prototype.back = function() {
        theHistory.back();
    };

    //noinspection JSUnusedGlobalSymbols
    /**
     * @deprecated
     */
    Router.prototype.loadPage = Router.prototype.load;

    /**
     * 切换显示当前文档另一个块
     *
     * 把新块从右边切入展示，同时会把新的块的记录用 history.pushState 来保存起来
     *
     * 如果已经是当前显示的块，那么不做任何处理；
     * 如果没对应的块，那么忽略。
     *
     * @param {String} sectionId 待切换显示的块的 id
     * @private
     */
    Router.prototype._switchToSection = function(sectionId) {
        if (!sectionId) {
            return;
        }

        var $curPage = this._getCurrentSection(),
            $newPage = $('#' + sectionId);

        // 如果已经是当前页，不做任何处理
        if ($curPage === $newPage) {
            return;
        }

        this._animateSection($curPage, $newPage, DIRECTION.rightToLeft);
        this._pushNewState('#' + sectionId, sectionId);
    };

    /**
     * 载入显示一个新的文档
     *
     * - 如果有缓存，那么直接利用缓存来切换
     * - 否则，先把页面加载过来缓存，然后再切换
     *      - 如果解析失败，那么用 location.href 的方式来跳转
     *
     * 注意：不能在这里以及其之后用 location.href 来 **读取** 切换前的页面的 url，
     *     因为如果是 popState 时的调用，那么此时 location 已经是 pop 出来的 state 的了
     *
     * @param {String} url 新的文档的 url
     * @param {Boolean=} ignoreCache 是否不使用缓存强制加载页面
     * @param {Boolean=} isPushState 是否需要 pushState
     * @param {String=} direction 新文档切入的方向
     * @private
     */
    Router.prototype._switchToDocument = function(url, ignoreCache, isPushState, direction) {
        var baseUrl = Util.toUrlObject(url).base;

        if (ignoreCache) {
            delete this.cache[baseUrl];
        }

        var cacheDocument = this.cache[baseUrl];
        var context = this;

        if (cacheDocument) {
            this._doSwitchDocument(url, isPushState, direction);
        } else {
            this._loadDocument(url, {
                success: function($doc) {
                    try {
                        context._parseDocument(url, $doc);
                        context._doSwitchDocument(url, isPushState, direction);
                    } catch (e) {
                        location.href = url;
                    }
                },
                error: function() {
                    location.href = url;
                }
            });
        }
    };

    /**
     * 利用缓存来做具体的切换文档操作
     *
     * - 确定待切入的文档的默认展示 section
     * - 把新文档 append 到 view 中
     * - 动画切换文档
     * - 如果需要 pushState，那么把最新的状态 push 进去并把当前状态更新为该状态
     *
     * @param {String} url 待切换的文档的 url
     * @param {Boolean} isPushState 加载页面后是否需要 pushState，默认是 true
     * @param {String} direction 动画切换方向，默认是 DIRECTION.rightToLeft
     * @private
     */
    Router.prototype._doSwitchDocument = function(url, isPushState, direction) {
        if (typeof isPushState === 'undefined') {
            isPushState = true;
        }

        var urlObj = Util.toUrlObject(url);
        var $currentDoc = this.$view.find('.' + routerConfig.sectionGroupClass);
        var $newDoc = $($('<div></div>').append(this.cache[urlObj.base].$content).html());

        // 确定一个 document 展示 section 的顺序
        // 1. 与 hash 关联的 element
        // 2. 默认的标识为 current 的 element
        // 3. 第一个 section
        var $allSection = $newDoc.find('.' + routerConfig.pageClass);
        var $visibleSection = $newDoc.find('.' + routerConfig.curPageClass);
        var $hashSection;

        if (urlObj.fragment) {
            $hashSection = $newDoc.find('#' + urlObj.fragment);
        }
        if ($hashSection && $hashSection.length) {
            $visibleSection = $hashSection.eq(0);
        } else if (!$visibleSection.length) {
            $visibleSection = $allSection.eq(0);
        }
        if (!$visibleSection.attr('id')) {
            $visibleSection.attr('id', this._generateRandomId());
        }

        var $currentSection = this._getCurrentSection();
        $currentSection.trigger(EVENTS.beforePageSwitch, [$currentSection.attr('id'), $currentSection]);

        $allSection.removeClass(routerConfig.curPageClass);
        $visibleSection.addClass(routerConfig.curPageClass);

        // prepend 而不 append 的目的是避免 append 进去新的 document 在后面，
        // 其里面的默认展示的(.page-current) 的页面直接就覆盖了原显示的页面（因为都是 absolute）
        this.$view.prepend($newDoc);

        this._animateDocument($currentDoc, $newDoc, $visibleSection, direction);

        if (isPushState) {
            this._pushNewState(url, $visibleSection.attr('id'));
        }
    };

    /**
     * 判断两个 url 指向的页面是否是同一个
     *
     * 判断方式: 如果两个 url 的 base 形式（不带 hash 的绝对形式）相同，那么认为是同一个页面
     *
     * @param {String} url
     * @param {String} anotherUrl
     * @returns {Boolean}
     * @private
     */
    Router.prototype._isTheSameDocument = function(url, anotherUrl) {
        return Util.toUrlObject(url).base === Util.toUrlObject(anotherUrl).base;
    };

    /**
     * ajax 加载 url 指定的页面内容
     *
     * 加载过程中会发出以下事件
     *  pageLoadCancel: 如果前一个还没加载完,那么取消并发送该事件
     *  pageLoadStart: 开始加载
     *  pageLodComplete: ajax complete 完成
     *  pageLoadError: ajax 发生 error
     *
     *
     * @param {String} url url
     * @param {Object=} callback 回调函数配置，可选，可以配置 success\error 和 complete
     *      所有回调函数的 this 都是 null，各自实参如下：
     *      success: $doc, status, xhr
     *      error: xhr, status, err
     *      complete: xhr, status
     *
     * @private
     */
    Router.prototype._loadDocument = function(url, callback) {
        if (this.xhr && this.xhr.readyState < 4) {
            this.xhr.onreadystatechange = function() {
            };
            this.xhr.abort();
            this.dispatch(EVENTS.pageLoadCancel);
        }

        this.dispatch(EVENTS.pageLoadStart);

        callback = callback || {};
        var self = this;

        this.xhr = $.ajax({
            url: url,
            success: $.proxy(function(data, status, xhr) {
                // 给包一层 <html/>，从而可以拿到完整的结构
                var $doc = $('<html></html>');
                $doc.append(data);
                callback.success && callback.success.call(null, $doc, status, xhr);
            }, this),
            error: function(xhr, status, err) {
                callback.error && callback.error.call(null, xhr, status, err);
                self.dispatch(EVENTS.pageLoadError);
            },
            complete: function(xhr, status) {
                callback.complete && callback.complete.call(null, xhr, status);
                self.dispatch(EVENTS.pageLoadComplete);
            }
        });
    };

    /**
     * 对于 ajax 加载进来的页面，把其缓存起来
     *
     * @param {String} url url
     * @param $doc ajax 载入的页面的 jq 对象，可以看做是该页面的 $(document)
     * @private
     */
    Router.prototype._parseDocument = function(url, $doc) {
        var $innerView = $doc.find('.' + routerConfig.sectionGroupClass);

        if (!$innerView.length) {
            throw new Error('missing router view mark: ' + routerConfig.sectionGroupClass);
        }

        this._saveDocumentIntoCache($doc, url);
    };

    /**
     * 把一个页面的相关信息保存到 this.cache 中
     *
     * 以页面的 baseUrl 为 key,而 value 则是一个 DocumentCache
     *
     * @param {*} doc doc
     * @param {String} url url
     * @private
     */
    Router.prototype._saveDocumentIntoCache = function(doc, url) {
        var urlAsKey = Util.toUrlObject(url).base;
        var $doc = $(doc);

        this.cache[urlAsKey] = {
            $doc: $doc,
            $content: $doc.find('.' + routerConfig.sectionGroupClass)
        };
    };

    /**
     * 从 sessionStorage 中获取保存下来的「当前状态」
     *
     * 如果解析失败，那么认为当前状态是 null
     *
     * @returns {State|null}
     * @private
     */
    Router.prototype._getLastState = function() {
        var currentState = sessionStorage.getItem(this.sessionNames.currentState);
        try {
            currentState = JSON.parse(currentState);
        } catch(e) {
            currentState = null;
        }

        return currentState;
    };

    /**
     * 把一个状态设为当前状态，保存仅 sessionStorage 中
     *
     * @param {State} state
     * @private
     */
    Router.prototype._saveAsCurrentState = function(state) {
        sessionStorage.setItem(this.sessionNames.currentState, JSON.stringify(state));
    };

    /**
     * 获取下一个 state 的 id
     *
     * 读取 sessionStorage 里的最后的状态的 id，然后 + 1；如果原没设置，那么返回 1
     *
     * @returns {number}
     * @private
     */
    Router.prototype._getNextStateId = function() {
        var maxStateId = sessionStorage.getItem(this.sessionNames.maxStateId);
        return maxStateId ? parseInt(maxStateId, 10) + 1 : 1;
    };

    /**
     * 把 sessionStorage 里的最后状态的 id 自加 1
     *
     * @private
     */
    Router.prototype._incMaxStateId = function() {
        sessionStorage.setItem(this.sessionNames.maxStateId, this._getNextStateId());
    };

    /**
     * 从一个文档切换为显示另一个文档
     *
     * @param $from 目前显示的文档
     * @param $to 待切换显示的新文档
     * @param $visibleSection 新文档中展示的 section 元素
     * @param direction 新文档切入方向
     * @private
     */
    Router.prototype._animateDocument = function($from, $to, $visibleSection, direction) {
        var sectionId = $visibleSection.attr('id');


        var $visibleSectionInFrom = $from.find('.' + routerConfig.curPageClass);
        $visibleSectionInFrom.addClass(routerConfig.visiblePageClass).removeClass(routerConfig.curPageClass);

        $visibleSection.trigger(EVENTS.pageAnimationStart, [sectionId, $visibleSection]);

        this._animateElement($from, $to, direction);

        $from.animationEnd(function() {
            $visibleSectionInFrom.removeClass(routerConfig.visiblePageClass);
            // 移除 document 前后，发送 beforePageRemove 和 pageRemoved 事件
            $(window).trigger(EVENTS.beforePageRemove, [$from]);
            $from.remove();
            $(window).trigger(EVENTS.pageRemoved);
        });

        $to.animationEnd(function() {
            $visibleSection.trigger(EVENTS.pageAnimationEnd, [sectionId, $visibleSection]);
            // 外层（init.js）中会绑定 pageInitInternal 事件，然后对页面进行初始化
            $visibleSection.trigger(EVENTS.pageInit, [sectionId, $visibleSection]);
        });
    };

    /**
     * 把当前文档的展示 section 从一个 section 切换到另一个 section
     *
     * @param $from
     * @param $to
     * @param direction
     * @private
     */
    Router.prototype._animateSection = function($from, $to, direction) {
        var toId = $to.attr('id');
        $from.trigger(EVENTS.beforePageSwitch, [$from.attr('id'), $from]);

        $from.removeClass(routerConfig.curPageClass);
        $to.addClass(routerConfig.curPageClass);
        $to.trigger(EVENTS.pageAnimationStart, [toId, $to]);
        this._animateElement($from, $to, direction);
        $to.animationEnd(function() {
            $to.trigger(EVENTS.pageAnimationEnd, [toId, $to]);
            // 外层（init.js）中会绑定 pageInitInternal 事件，然后对页面进行初始化
            $to.trigger(EVENTS.pageInit, [toId, $to]);
        });
    };

    /**
     * 切换显示两个元素
     *
     * 切换是通过更新 class 来实现的，而具体的切换动画则是 class 关联的 css 来实现
     *
     * @param $from 当前显示的元素
     * @param $to 待显示的元素
     * @param direction 切换的方向
     * @private
     */
    Router.prototype._animateElement = function($from, $to, direction) {
        // todo: 可考虑如果入参不指定，那么尝试读取 $to 的属性，再没有再使用默认的
        // 考虑读取点击的链接上指定的方向
        if (typeof direction === 'undefined') {
            direction = DIRECTION.rightToLeft;
        }

        var animPageClasses = [
            'page-from-center-to-left',
            'page-from-center-to-right',
            'page-from-right-to-center',
            'page-from-left-to-center'].join(' ');

        var classForFrom, classForTo;
        switch(direction) {
            case DIRECTION.rightToLeft:
                classForFrom = 'page-from-center-to-left';
                classForTo = 'page-from-right-to-center';
                break;
            case DIRECTION.leftToRight:
                classForFrom = 'page-from-center-to-right';
                classForTo = 'page-from-left-to-center';
                break;
            default:
                classForFrom = 'page-from-center-to-left';
                classForTo = 'page-from-right-to-center';
                break;
        }

        $from.removeClass(animPageClasses).addClass(classForFrom);
        $to.removeClass(animPageClasses).addClass(classForTo);

        $from.animationEnd(function() {
            $from.removeClass(animPageClasses);
        });
        $to.animationEnd(function() {
            $to.removeClass(animPageClasses);
        });
    };

    /**
     * 获取当前显示的第一个 section
     *
     * @returns {*}
     * @private
     */
    Router.prototype._getCurrentSection = function() {
        return this.$view.find('.' + routerConfig.curPageClass).eq(0);
    };

    /**
     * popState 事件关联着的后退处理
     *
     * 判断两个 state 判断是否是属于同一个文档，然后做对应的 section 或文档切换；
     * 同时在切换后把新 state 设为当前 state
     *
     * @param {State} state 新 state
     * @param {State} fromState 旧 state
     * @private
     */
    Router.prototype._back = function(state, fromState) {
        if (this._isTheSameDocument(state.url.full, fromState.url.full)) {
            var $newPage = $('#' + state.pageId);
            if ($newPage.length) {
                var $currentPage = this._getCurrentSection();
                this._animateSection($currentPage, $newPage, DIRECTION.leftToRight);
                this._saveAsCurrentState(state);
            } else {
                location.href = state.url.full;
            }
        } else {
            this._saveDocumentIntoCache($(document), fromState.url.full);
            this._switchToDocument(state.url.full, false, false, DIRECTION.leftToRight);
            this._saveAsCurrentState(state);
        }
    };

    /**
     * popState 事件关联着的前进处理,类似于 _back，不同的是切换方向
     *
     * @param {State} state 新 state
     * @param {State} fromState 旧 state
     * @private
     */
    Router.prototype._forward = function(state, fromState) {
        if (this._isTheSameDocument(state.url.full, fromState.url.full)) {
            var $newPage = $('#' + state.pageId);
            if ($newPage.length) {
                var $currentPage = this._getCurrentSection();
                this._animateSection($currentPage, $newPage, DIRECTION.rightToLeft);
                this._saveAsCurrentState(state);
            } else {
                location.href = state.url.full;
            }
        } else {
            this._saveDocumentIntoCache($(document), fromState.url.full);
            this._switchToDocument(state.url.full, false, false, DIRECTION.rightToLeft);
            this._saveAsCurrentState(state);
        }
    };

    /**
     * popState 事件处理
     *
     * 根据 pop 出来的 state 和当前 state 来判断是前进还是后退
     *
     * @param event
     * @private
     */
    Router.prototype._onPopState = function(event) {
        var state = event.state;
        // if not a valid state, do nothing
        if (!state || !state.pageId) {
            return;
        }

        var lastState = this._getLastState();

        if (!lastState) {
            console.error && console.error('Missing last state when backward or forward');
            return;
        }

        if (state.id === lastState.id) {
            return;
        }

        if (state.id < lastState.id) {
            this._back(state, lastState);
        } else {
            this._forward(state, lastState);
        }
    };

    /**
     * 页面进入到一个新状态
     *
     * 把新状态 push 进去，设置为当前的状态，然后把 maxState 的 id +1。
     *
     * @param {String} url 新状态的 url
     * @param {String} sectionId 新状态中显示的 section 元素的 id
     * @private
     */
    Router.prototype._pushNewState = function(url, sectionId) {
        var state = {
            id: this._getNextStateId(),
            pageId: sectionId,
            url: Util.toUrlObject(url)
        };

        theHistory.pushState(state, '', url);
        this._saveAsCurrentState(state);
        this._incMaxStateId();
    };

    /**
     * 生成一个随机的 id
     *
     * @returns {string}
     * @private
     */
    Router.prototype._generateRandomId = function() {
        return "page-" + (+new Date());
    };

    Router.prototype.dispatch = function(event) {
        var e = new CustomEvent(event, {
            bubbles: true,
            cancelable: true
        });

        //noinspection JSUnresolvedFunction
        window.dispatchEvent(e);
    };

    /**
     * 判断一个链接是否使用 router 来处理
     *
     * @param $link
     * @returns {boolean}
     */
    function isInRouterBlackList($link) {
        var classBlackList = [
            'external',
            'tab-link',
            'open-popup',
            'close-popup',
            'open-panel',
            'close-panel'
        ];

        for (var i = classBlackList.length -1 ; i >= 0; i--) {
            if ($link.hasClass(classBlackList[i])) {
                return true;
            }
        }

        var linkEle = $link.get(0);
        var linkHref = linkEle.getAttribute('href');

        var protoWhiteList = [
            'http',
            'https'
        ];

        //如果非noscheme形式的链接，且协议不是http(s)，那么路由不会处理这类链接
        if (/^(\w+):/.test(linkHref) && protoWhiteList.indexOf(RegExp.$1) < 0) {
            return true;
        }

        //noinspection RedundantIfStatementJS
        if (linkEle.hasAttribute('external')) {
            return true;
        }

        return false;
    }

    /**
     * 自定义是否执行路由功能的过滤器
     *
     * 可以在外部定义 $.config.routerFilter 函数，实参是点击链接的 Zepto 对象。
     *
     * @param $link 当前点击的链接的 Zepto 对象
     * @returns {boolean} 返回 true 表示执行路由功能，否则不做路由处理
     */
    function customClickFilter($link) {
        var customRouterFilter = $.smConfig.routerFilter;
        if ($.isFunction(customRouterFilter)) {
            var filterResult = customRouterFilter($link);
            if (typeof filterResult === 'boolean') {
                return filterResult;
            }
        }

        return true;
    }

    $(function() {
        // 用户可选关闭router功能
        if (!$.smConfig.router) {
            return;
        }

        if (!Util.supportStorage()) {
            return;
        }

        var $pages = $('.' + routerConfig.pageClass);
        if (!$pages.length) {
            var warnMsg = 'Disable router function because of no .page elements';
            if (window.console && window.console.warn) {
                console.warn(warnMsg);
            }
            return;
        }

        var router = $.router = new Router();

        $(document).on('click', 'a', function(e) {
            var $target = $(e.currentTarget);

            var filterResult = customClickFilter($target);
            if (!filterResult) {
                return;
            }

            if (isInRouterBlackList($target)) {
                return;
            }

            e.preventDefault();

            if ($target.hasClass('back')) {
                router.back();
            } else {
                var url = $target.attr('href');
                if (!url || url === '#') {
                    return;
                }

                var ignoreCache = $target.attr('data-no-cache') === 'true';

                router.load(url, ignoreCache);
            }
        });
    });
}(Zepto);

/**
 * @typedef {Object} State
 * @property {Number} id
 * @property {String} url
 * @property {String} pageId
 */

/**
 * @typedef {Object} UrlObject 字符串 url 转为的对象
 * @property {String} base url 的基本路径
 * @property {String} full url 的完整绝对路径
 * @property {String} origin 转换前的 url
 * @property {String} fragment url 的 fragment
 */

/**
 * @typedef {Object} DocumentCache
 * @property {*|HTMLElement} $doc 看做是 $(document)
 * @property {*|HTMLElement} $content $doc 里的 routerConfig.innerViewClass 元素
 */

/*======================================================
************   Modals   ************
======================================================*/
/*jshint unused: false*/
+function ($) {
  "use strict";
  $.lastPosition =function(options) {
    if ( !sessionStorage) {
        return;
    }
    // 需要记忆模块的className
    var needMemoryClass = options.needMemoryClass || [];

    $(window).off('beforePageSwitch').on('beforePageSwitch', function(event,id,arg) {
      updateMemory(id,arg);
    });
    $(window).off('pageAnimationStart').on('pageAnimationStart', function(event,id,arg) {
      getMemory(id,arg);
    });
    //让后退页面回到之前的高度
    function getMemory(id,arg){
      needMemoryClass.forEach(function(item, index) {
          if ($(item).length === 0) {
              return;
          }
          var positionName = id ;
          // 遍历对应节点设置存储的高度
          var memoryHeight = sessionStorage.getItem(positionName);
          arg.find(item).scrollTop(parseInt(memoryHeight));

      });
    }
    //记住即将离开的页面的高度
    function updateMemory(id,arg) {
        var positionName = id ;
        // 存储需要记忆模块的高度
        needMemoryClass.forEach(function(item, index) {
            if ($(item).length === 0) {
                return;
            }
            sessionStorage.setItem(
                positionName,
                arg.find(item).scrollTop()
            );

        });
    }
  };
}(Zepto);

/*jshint unused: false*/
+function($) {
    'use strict';

    var getPage = function() {
        var $page = $(".page-current");
        if (!$page[0]) $page = $(".page").addClass('page-current');
        return $page;
    };

    //初始化页面中的JS组件
    $.initPage = function(page) {
        var $page = getPage();
        if (!$page[0]) $page = $(document.body);
        var $content = $page.hasClass('content') ?
                       $page :
                       $page.find('.content');
        $content.scroller();  //注意滚动条一定要最先初始化

        $.initPullToRefresh($content);
        $.initInfiniteScroll($content);
        $.initCalendar($content);

        //extend
        if ($.initSwiper) $.initSwiper($content);
    };

    if ($.smConfig.showPageLoadingIndicator) {
        //这里的 以 push 开头的是私有事件，不要用
        $(window).on('pageLoadStart', function() {
            $.showIndicator();

        });
        $(window).on('pageAnimationStart', function() {
            $.hideIndicator();
        });
        $(window).on('pageLoadCancel', function() {
            $.hideIndicator();
        });
        $(window).on('pageLoadComplete', function() {
            $.hideIndicator();
        });
        $(window).on('pageLoadError', function() {
            $.hideIndicator();
            $.toast('加载失败');
        });
    }

    $(window).on('pageAnimationStart', function(event,id,page) {
        // 在路由切换页面动画开始前,为了把位于 .page 之外的 popup 等隐藏,此处做些处理
        $.closeModal();
        $.closePanel();
        // 如果 panel 的 effect 是 reveal 时,似乎是 page 的动画或别的样式原因导致了 transitionEnd 时间不会触发
        // 这里暂且处理一下
        $('body').removeClass('panel-closing');
        $.allowPanelOpen = true;  
    });
   
    $(window).on('pageInit', function() {
        $.hideIndicator();
        $.lastPosition({
            needMemoryClass: [
                '.content'
            ]
        });
    });
    // safari 在后退的时候会使用缓存技术，但实现上似乎存在些问题，
    // 导致路由中绑定的点击事件不会正常如期的运行（log 和 debugger 都没法调试），
    // 从而后续的跳转等完全乱了套。
    // 所以，这里检测到是 safari 的 cache 的情况下，做一次 reload
    // 测试路径(后缀 D 表示是 document，E 表示 external，不使用路由跳转）：
    // 1. aD -> bDE
    // 2. back
    // 3. aD -> bD
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            location.reload();
        }
    });

    $.init = function() {
        var $page = getPage();
        var id = $page[0].id;
        $.initPage();
        $page.trigger('pageInit', [id, $page]);
    };

    //DOM READY
    $(function() {
        //直接绑定
        FastClick.attach(document.body);

        if ($.smConfig.autoInit) {
            $.init();
        }

        $(document).on('pageInitInternal', function(e, id, page) {
            $.init();
        });
    });

}(Zepto);

/**
 * ScrollFix v0.1
 * http://www.joelambert.co.uk
 *
 * Copyright 2011, Joe Lambert.
 * Free to use under the MIT license.
 * http://www.opensource.org/licenses/mit-license.php
 */
/* ===============================================================================
************   ScrollFix   ************
=============================================================================== */

+ function($) {
    "use strict";
    //安卓微信中使用scrollfix会有问题，因此只在ios中使用，安卓机器按照原来的逻辑

    if($.device.ios){
        var ScrollFix = function(elem) {

            // Variables to track inputs
            var startY;
            var startTopScroll;

            elem = elem || document.querySelector(elem);

            // If there is no element, then do nothing
            if(!elem)
                return;

            // Handle the start of interactions
            elem.addEventListener('touchstart', function(event){
                startY = event.touches[0].pageY;
                startTopScroll = elem.scrollTop;

                if(startTopScroll <= 0)
                elem.scrollTop = 1;

            if(startTopScroll + elem.offsetHeight >= elem.scrollHeight)
                elem.scrollTop = elem.scrollHeight - elem.offsetHeight - 1;
            }, false);
        };

        var initScrollFix = function(){
            var prefix = $('.page-current').length > 0 ? '.page-current ' : '';
            var scrollable = $(prefix + ".content");
            new ScrollFix(scrollable[0]);
        };

        $(document).on($.touchEvents.move, ".page-current .bar",function(){
            event.preventDefault();
        });
        //监听ajax页面跳转
        $(document).on("pageLoadComplete", function(){
             initScrollFix();
        });
        //监听内联页面跳转
        $(document).on("pageAnimationEnd", function(){
             initScrollFix();
        });
        initScrollFix();
    }

}(Zepto);

/*!
 * =====================================================
 * SUI Mobile - http://m.sui.taobao.org/
 *
 * =====================================================
 */
+function(a){"use strict";var b=function(c,d){function e(){return"horizontal"===o.params.direction}function f(){o.autoplayTimeoutId=setTimeout(function(){o.params.loop?(o.fixLoop(),o._slideNext()):o.isEnd?d.autoplayStopOnLast?o.stopAutoplay():o._slideTo(0):o._slideNext()},o.params.autoplay)}function g(b,c){var d=a(b.target);if(!d.is(c))if("string"==typeof c)d=d.parents(c);else if(c.nodeType){var e;return d.parents().each(function(a,b){b===c&&(e=c)}),e?c:void 0}if(0!==d.length)return d[0]}function h(a,b){b=b||{};var c=window.MutationObserver||window.WebkitMutationObserver,d=new c(function(a){a.forEach(function(a){o.onResize(),o.emit("onObserverUpdate",o,a)})});d.observe(a,{attributes:"undefined"==typeof b.attributes?!0:b.attributes,childList:"undefined"==typeof b.childList?!0:b.childList,characterData:"undefined"==typeof b.characterData?!0:b.characterData}),o.observers.push(d)}function i(b,c){b=a(b);var d,f,g;d=b.attr("data-swiper-parallax")||"0",f=b.attr("data-swiper-parallax-x"),g=b.attr("data-swiper-parallax-y"),f||g?(f=f||"0",g=g||"0"):e()?(f=d,g="0"):(g=d,f="0"),f=f.indexOf("%")>=0?parseInt(f,10)*c+"%":f*c+"px",g=g.indexOf("%")>=0?parseInt(g,10)*c+"%":g*c+"px",b.transform("translate3d("+f+", "+g+",0px)")}function j(a){return 0!==a.indexOf("on")&&(a=a[0]!==a[0].toUpperCase()?"on"+a[0].toUpperCase()+a.substring(1):"on"+a),a}var k=this.defaults,l=d&&d.virtualTranslate;d=d||{};for(var m in k)if("undefined"==typeof d[m])d[m]=k[m];else if("object"==typeof d[m])for(var n in k[m])"undefined"==typeof d[m][n]&&(d[m][n]=k[m][n]);var o=this;if(o.params=d,o.classNames=[],o.$=a,o.container=a(c),0!==o.container.length){if(o.container.length>1)return void o.container.each(function(){new a.Swiper(this,d)});o.container[0].swiper=o,o.container.data("swiper",o),o.classNames.push("swiper-container-"+o.params.direction),o.params.freeMode&&o.classNames.push("swiper-container-free-mode"),o.support.flexbox||(o.classNames.push("swiper-container-no-flexbox"),o.params.slidesPerColumn=1),(o.params.parallax||o.params.watchSlidesVisibility)&&(o.params.watchSlidesProgress=!0),["cube","coverflow"].indexOf(o.params.effect)>=0&&(o.support.transforms3d?(o.params.watchSlidesProgress=!0,o.classNames.push("swiper-container-3d")):o.params.effect="slide"),"slide"!==o.params.effect&&o.classNames.push("swiper-container-"+o.params.effect),"cube"===o.params.effect&&(o.params.resistanceRatio=0,o.params.slidesPerView=1,o.params.slidesPerColumn=1,o.params.slidesPerGroup=1,o.params.centeredSlides=!1,o.params.spaceBetween=0,o.params.virtualTranslate=!0,o.params.setWrapperSize=!1),"fade"===o.params.effect&&(o.params.slidesPerView=1,o.params.slidesPerColumn=1,o.params.slidesPerGroup=1,o.params.watchSlidesProgress=!0,o.params.spaceBetween=0,"undefined"==typeof l&&(o.params.virtualTranslate=!0)),o.params.grabCursor&&o.support.touch&&(o.params.grabCursor=!1),o.wrapper=o.container.children("."+o.params.wrapperClass),o.params.pagination&&(o.paginationContainer=a(o.params.pagination),o.params.paginationClickable&&o.paginationContainer.addClass("swiper-pagination-clickable")),o.rtl=e()&&("rtl"===o.container[0].dir.toLowerCase()||"rtl"===o.container.css("direction")),o.rtl&&o.classNames.push("swiper-container-rtl"),o.rtl&&(o.wrongRTL="-webkit-box"===o.wrapper.css("display")),o.params.slidesPerColumn>1&&o.classNames.push("swiper-container-multirow"),o.device.android&&o.classNames.push("swiper-container-android"),o.container.addClass(o.classNames.join(" ")),o.translate=0,o.progress=0,o.velocity=0,o.lockSwipeToNext=function(){o.params.allowSwipeToNext=!1},o.lockSwipeToPrev=function(){o.params.allowSwipeToPrev=!1},o.lockSwipes=function(){o.params.allowSwipeToNext=o.params.allowSwipeToPrev=!1},o.unlockSwipeToNext=function(){o.params.allowSwipeToNext=!0},o.unlockSwipeToPrev=function(){o.params.allowSwipeToPrev=!0},o.unlockSwipes=function(){o.params.allowSwipeToNext=o.params.allowSwipeToPrev=!0},o.params.grabCursor&&(o.container[0].style.cursor="move",o.container[0].style.cursor="-webkit-grab",o.container[0].style.cursor="-moz-grab",o.container[0].style.cursor="grab"),o.imagesToLoad=[],o.imagesLoaded=0,o.loadImage=function(a,b,c,d){function e(){d&&d()}var f;a.complete&&c?e():b?(f=new Image,f.onload=e,f.onerror=e,f.src=b):e()},o.preloadImages=function(){function a(){"undefined"!=typeof o&&null!==o&&(void 0!==o.imagesLoaded&&o.imagesLoaded++,o.imagesLoaded===o.imagesToLoad.length&&(o.params.updateOnImagesReady&&o.update(),o.emit("onImagesReady",o)))}o.imagesToLoad=o.container.find("img");for(var b=0;b<o.imagesToLoad.length;b++)o.loadImage(o.imagesToLoad[b],o.imagesToLoad[b].currentSrc||o.imagesToLoad[b].getAttribute("src"),!0,a)},o.autoplayTimeoutId=void 0,o.autoplaying=!1,o.autoplayPaused=!1,o.startAutoplay=function(){return"undefined"!=typeof o.autoplayTimeoutId?!1:o.params.autoplay?o.autoplaying?!1:(o.autoplaying=!0,o.emit("onAutoplayStart",o),void f()):!1},o.stopAutoplay=function(){o.autoplayTimeoutId&&(o.autoplayTimeoutId&&clearTimeout(o.autoplayTimeoutId),o.autoplaying=!1,o.autoplayTimeoutId=void 0,o.emit("onAutoplayStop",o))},o.pauseAutoplay=function(a){o.autoplayPaused||(o.autoplayTimeoutId&&clearTimeout(o.autoplayTimeoutId),o.autoplayPaused=!0,0===a?(o.autoplayPaused=!1,f()):o.wrapper.transitionEnd(function(){o.autoplayPaused=!1,o.autoplaying?f():o.stopAutoplay()}))},o.minTranslate=function(){return-o.snapGrid[0]},o.maxTranslate=function(){return-o.snapGrid[o.snapGrid.length-1]},o.updateContainerSize=function(){o.width=o.container[0].clientWidth,o.height=o.container[0].clientHeight,o.size=e()?o.width:o.height},o.updateSlidesSize=function(){o.slides=o.wrapper.children("."+o.params.slideClass),o.snapGrid=[],o.slidesGrid=[],o.slidesSizesGrid=[];var a,b=o.params.spaceBetween,c=0,d=0,f=0;"string"==typeof b&&b.indexOf("%")>=0&&(b=parseFloat(b.replace("%",""))/100*o.size),o.virtualSize=-b,o.rtl?o.slides.css({marginLeft:"",marginTop:""}):o.slides.css({marginRight:"",marginBottom:""});var g;o.params.slidesPerColumn>1&&(g=Math.floor(o.slides.length/o.params.slidesPerColumn)===o.slides.length/o.params.slidesPerColumn?o.slides.length:Math.ceil(o.slides.length/o.params.slidesPerColumn)*o.params.slidesPerColumn);var h;for(a=0;a<o.slides.length;a++){h=0;var i=o.slides.eq(a);if(o.params.slidesPerColumn>1){var j,k,l,m,n=o.params.slidesPerColumn;"column"===o.params.slidesPerColumnFill?(k=Math.floor(a/n),l=a-k*n,j=k+l*g/n,i.css({"-webkit-box-ordinal-group":j,"-moz-box-ordinal-group":j,"-ms-flex-order":j,"-webkit-order":j,order:j})):(m=g/n,l=Math.floor(a/m),k=a-l*m),i.css({"margin-top":0!==l&&o.params.spaceBetween&&o.params.spaceBetween+"px"}).attr("data-swiper-column",k).attr("data-swiper-row",l)}"none"!==i.css("display")&&("auto"===o.params.slidesPerView?h=e()?i.outerWidth(!0):i.outerHeight(!0):(h=(o.size-(o.params.slidesPerView-1)*b)/o.params.slidesPerView,e()?o.slides[a].style.width=h+"px":o.slides[a].style.height=h+"px"),o.slides[a].swiperSlideSize=h,o.slidesSizesGrid.push(h),o.params.centeredSlides?(c=c+h/2+d/2+b,0===a&&(c=c-o.size/2-b),Math.abs(c)<.001&&(c=0),f%o.params.slidesPerGroup===0&&o.snapGrid.push(c),o.slidesGrid.push(c)):(f%o.params.slidesPerGroup===0&&o.snapGrid.push(c),o.slidesGrid.push(c),c=c+h+b),o.virtualSize+=h+b,d=h,f++)}o.virtualSize=Math.max(o.virtualSize,o.size);var p;if(o.rtl&&o.wrongRTL&&("slide"===o.params.effect||"coverflow"===o.params.effect)&&o.wrapper.css({width:o.virtualSize+o.params.spaceBetween+"px"}),(!o.support.flexbox||o.params.setWrapperSize)&&(e()?o.wrapper.css({width:o.virtualSize+o.params.spaceBetween+"px"}):o.wrapper.css({height:o.virtualSize+o.params.spaceBetween+"px"})),o.params.slidesPerColumn>1&&(o.virtualSize=(h+o.params.spaceBetween)*g,o.virtualSize=Math.ceil(o.virtualSize/o.params.slidesPerColumn)-o.params.spaceBetween,o.wrapper.css({width:o.virtualSize+o.params.spaceBetween+"px"}),o.params.centeredSlides)){for(p=[],a=0;a<o.snapGrid.length;a++)o.snapGrid[a]<o.virtualSize+o.snapGrid[0]&&p.push(o.snapGrid[a]);o.snapGrid=p}if(!o.params.centeredSlides){for(p=[],a=0;a<o.snapGrid.length;a++)o.snapGrid[a]<=o.virtualSize-o.size&&p.push(o.snapGrid[a]);o.snapGrid=p,Math.floor(o.virtualSize-o.size)>Math.floor(o.snapGrid[o.snapGrid.length-1])&&o.snapGrid.push(o.virtualSize-o.size)}0===o.snapGrid.length&&(o.snapGrid=[0]),0!==o.params.spaceBetween&&(e()?o.rtl?o.slides.css({marginLeft:b+"px"}):o.slides.css({marginRight:b+"px"}):o.slides.css({marginBottom:b+"px"})),o.params.watchSlidesProgress&&o.updateSlidesOffset()},o.updateSlidesOffset=function(){for(var a=0;a<o.slides.length;a++)o.slides[a].swiperSlideOffset=e()?o.slides[a].offsetLeft:o.slides[a].offsetTop},o.updateSlidesProgress=function(a){if("undefined"==typeof a&&(a=o.translate||0),0!==o.slides.length){"undefined"==typeof o.slides[0].swiperSlideOffset&&o.updateSlidesOffset();var b=o.params.centeredSlides?-a+o.size/2:-a;o.rtl&&(b=o.params.centeredSlides?a-o.size/2:a),o.slides.removeClass(o.params.slideVisibleClass);for(var c=0;c<o.slides.length;c++){var d=o.slides[c],e=o.params.centeredSlides===!0?d.swiperSlideSize/2:0,f=(b-d.swiperSlideOffset-e)/(d.swiperSlideSize+o.params.spaceBetween);if(o.params.watchSlidesVisibility){var g=-(b-d.swiperSlideOffset-e),h=g+o.slidesSizesGrid[c],i=g>=0&&g<o.size||h>0&&h<=o.size||0>=g&&h>=o.size;i&&o.slides.eq(c).addClass(o.params.slideVisibleClass)}d.progress=o.rtl?-f:f}}},o.updateProgress=function(a){"undefined"==typeof a&&(a=o.translate||0);var b=o.maxTranslate()-o.minTranslate();0===b?(o.progress=0,o.isBeginning=o.isEnd=!0):(o.progress=(a-o.minTranslate())/b,o.isBeginning=o.progress<=0,o.isEnd=o.progress>=1),o.isBeginning&&o.emit("onReachBeginning",o),o.isEnd&&o.emit("onReachEnd",o),o.params.watchSlidesProgress&&o.updateSlidesProgress(a),o.emit("onProgress",o,o.progress)},o.updateActiveIndex=function(){var a,b,c,d=o.rtl?o.translate:-o.translate;for(b=0;b<o.slidesGrid.length;b++)"undefined"!=typeof o.slidesGrid[b+1]?d>=o.slidesGrid[b]&&d<o.slidesGrid[b+1]-(o.slidesGrid[b+1]-o.slidesGrid[b])/2?a=b:d>=o.slidesGrid[b]&&d<o.slidesGrid[b+1]&&(a=b+1):d>=o.slidesGrid[b]&&(a=b);(0>a||"undefined"==typeof a)&&(a=0),c=Math.floor(a/o.params.slidesPerGroup),c>=o.snapGrid.length&&(c=o.snapGrid.length-1),a!==o.activeIndex&&(o.snapIndex=c,o.previousIndex=o.activeIndex,o.activeIndex=a,o.updateClasses())},o.updateClasses=function(){o.slides.removeClass(o.params.slideActiveClass+" "+o.params.slideNextClass+" "+o.params.slidePrevClass);var b=o.slides.eq(o.activeIndex);if(b.addClass(o.params.slideActiveClass),b.next("."+o.params.slideClass).addClass(o.params.slideNextClass),b.prev("."+o.params.slideClass).addClass(o.params.slidePrevClass),o.bullets&&o.bullets.length>0){o.bullets.removeClass(o.params.bulletActiveClass);var c;o.params.loop?(c=Math.ceil(o.activeIndex-o.loopedSlides)/o.params.slidesPerGroup,c>o.slides.length-1-2*o.loopedSlides&&(c-=o.slides.length-2*o.loopedSlides),c>o.bullets.length-1&&(c-=o.bullets.length)):c="undefined"!=typeof o.snapIndex?o.snapIndex:o.activeIndex||0,o.paginationContainer.length>1?o.bullets.each(function(){a(this).index()===c&&a(this).addClass(o.params.bulletActiveClass)}):o.bullets.eq(c).addClass(o.params.bulletActiveClass)}o.params.loop||(o.params.prevButton&&(o.isBeginning?(a(o.params.prevButton).addClass(o.params.buttonDisabledClass),o.params.a11y&&o.a11y&&o.a11y.disable(a(o.params.prevButton))):(a(o.params.prevButton).removeClass(o.params.buttonDisabledClass),o.params.a11y&&o.a11y&&o.a11y.enable(a(o.params.prevButton)))),o.params.nextButton&&(o.isEnd?(a(o.params.nextButton).addClass(o.params.buttonDisabledClass),o.params.a11y&&o.a11y&&o.a11y.disable(a(o.params.nextButton))):(a(o.params.nextButton).removeClass(o.params.buttonDisabledClass),o.params.a11y&&o.a11y&&o.a11y.enable(a(o.params.nextButton)))))},o.updatePagination=function(){if(o.params.pagination&&o.paginationContainer&&o.paginationContainer.length>0){for(var a="",b=o.params.loop?Math.ceil((o.slides.length-2*o.loopedSlides)/o.params.slidesPerGroup):o.snapGrid.length,c=0;b>c;c++)a+=o.params.paginationBulletRender?o.params.paginationBulletRender(c,o.params.bulletClass):'<span class="'+o.params.bulletClass+'"></span>';o.paginationContainer.html(a),o.bullets=o.paginationContainer.find("."+o.params.bulletClass)}},o.update=function(a){function b(){d=Math.min(Math.max(o.translate,o.maxTranslate()),o.minTranslate()),o.setWrapperTranslate(d),o.updateActiveIndex(),o.updateClasses()}if(o.updateContainerSize(),o.updateSlidesSize(),o.updateProgress(),o.updatePagination(),o.updateClasses(),o.params.scrollbar&&o.scrollbar&&o.scrollbar.set(),a){var c,d;o.params.freeMode?b():(c="auto"===o.params.slidesPerView&&o.isEnd&&!o.params.centeredSlides?o.slideTo(o.slides.length-1,0,!1,!0):o.slideTo(o.activeIndex,0,!1,!0),c||b())}},o.onResize=function(){if(o.updateContainerSize(),o.updateSlidesSize(),o.updateProgress(),("auto"===o.params.slidesPerView||o.params.freeMode)&&o.updatePagination(),o.params.scrollbar&&o.scrollbar&&o.scrollbar.set(),o.params.freeMode){var a=Math.min(Math.max(o.translate,o.maxTranslate()),o.minTranslate());o.setWrapperTranslate(a),o.updateActiveIndex(),o.updateClasses()}else o.updateClasses(),"auto"===o.params.slidesPerView&&o.isEnd&&!o.params.centeredSlides?o.slideTo(o.slides.length-1,0,!1,!0):o.slideTo(o.activeIndex,0,!1,!0)};var p=["mousedown","mousemove","mouseup"];window.navigator.pointerEnabled?p=["pointerdown","pointermove","pointerup"]:window.navigator.msPointerEnabled&&(p=["MSPointerDown","MSPointerMove","MSPointerUp"]),o.touchEvents={start:o.support.touch||!o.params.simulateTouch?"touchstart":p[0],move:o.support.touch||!o.params.simulateTouch?"touchmove":p[1],end:o.support.touch||!o.params.simulateTouch?"touchend":p[2]},(window.navigator.pointerEnabled||window.navigator.msPointerEnabled)&&("container"===o.params.touchEventsTarget?o.container:o.wrapper).addClass("swiper-wp8-"+o.params.direction),o.initEvents=function(b){var c=b?"off":"on",e=b?"removeEventListener":"addEventListener",f="container"===o.params.touchEventsTarget?o.container[0]:o.wrapper[0],g=o.support.touch?f:document,h=o.params.nested?!0:!1;o.browser.ie?(f[e](o.touchEvents.start,o.onTouchStart,!1),g[e](o.touchEvents.move,o.onTouchMove,h),g[e](o.touchEvents.end,o.onTouchEnd,!1)):(o.support.touch&&(f[e](o.touchEvents.start,o.onTouchStart,!1),f[e](o.touchEvents.move,o.onTouchMove,h),f[e](o.touchEvents.end,o.onTouchEnd,!1)),!d.simulateTouch||o.device.ios||o.device.android||(f[e]("mousedown",o.onTouchStart,!1),g[e]("mousemove",o.onTouchMove,h),g[e]("mouseup",o.onTouchEnd,!1))),window[e]("resize",o.onResize),o.params.nextButton&&(a(o.params.nextButton)[c]("click",o.onClickNext),o.params.a11y&&o.a11y&&a(o.params.nextButton)[c]("keydown",o.a11y.onEnterKey)),o.params.prevButton&&(a(o.params.prevButton)[c]("click",o.onClickPrev),o.params.a11y&&o.a11y&&a(o.params.prevButton)[c]("keydown",o.a11y.onEnterKey)),o.params.pagination&&o.params.paginationClickable&&a(o.paginationContainer)[c]("click","."+o.params.bulletClass,o.onClickIndex),(o.params.preventClicks||o.params.preventClicksPropagation)&&f[e]("click",o.preventClicks,!0)},o.attachEvents=function(){o.initEvents()},o.detachEvents=function(){o.initEvents(!0)},o.allowClick=!0,o.preventClicks=function(a){o.allowClick||(o.params.preventClicks&&a.preventDefault(),o.params.preventClicksPropagation&&(a.stopPropagation(),a.stopImmediatePropagation()))},o.onClickNext=function(a){a.preventDefault(),o.slideNext()},o.onClickPrev=function(a){a.preventDefault(),o.slidePrev()},o.onClickIndex=function(b){b.preventDefault();var c=a(this).index()*o.params.slidesPerGroup;o.params.loop&&(c+=o.loopedSlides),o.slideTo(c)},o.updateClickedSlide=function(b){var c=g(b,"."+o.params.slideClass);if(!c)return o.clickedSlide=void 0,void(o.clickedIndex=void 0);if(o.clickedSlide=c,o.clickedIndex=a(c).index(),o.params.slideToClickedSlide&&void 0!==o.clickedIndex&&o.clickedIndex!==o.activeIndex){var d,e=o.clickedIndex;if(o.params.loop)if(d=a(o.clickedSlide).attr("data-swiper-slide-index"),e>o.slides.length-o.params.slidesPerView)o.fixLoop(),e=o.wrapper.children("."+o.params.slideClass+'[data-swiper-slide-index="'+d+'"]').eq(0).index(),setTimeout(function(){o.slideTo(e)},0);else if(e<o.params.slidesPerView-1){o.fixLoop();var f=o.wrapper.children("."+o.params.slideClass+'[data-swiper-slide-index="'+d+'"]');e=f.eq(f.length-1).index(),setTimeout(function(){o.slideTo(e)},0)}else o.slideTo(e);else o.slideTo(e)}};var q,r,s,t,u,v,w,x,y,z="input, select, textarea, button",A=Date.now(),B=[];o.animating=!1,o.touches={startX:0,startY:0,currentX:0,currentY:0,diff:0};var C,D;o.onTouchStart=function(b){if(b.originalEvent&&(b=b.originalEvent),C="touchstart"===b.type,C||!("which"in b)||3!==b.which){if(o.params.noSwiping&&g(b,"."+o.params.noSwipingClass))return void(o.allowClick=!0);if(!o.params.swipeHandler||g(b,o.params.swipeHandler)){if(q=!0,r=!1,t=void 0,D=void 0,o.touches.startX=o.touches.currentX="touchstart"===b.type?b.targetTouches[0].pageX:b.pageX,o.touches.startY=o.touches.currentY="touchstart"===b.type?b.targetTouches[0].pageY:b.pageY,s=Date.now(),o.allowClick=!0,o.updateContainerSize(),o.swipeDirection=void 0,o.params.threshold>0&&(w=!1),"touchstart"!==b.type){var c=!0;a(b.target).is(z)&&(c=!1),document.activeElement&&a(document.activeElement).is(z)&&document.activeElement.blur(),c&&b.preventDefault()}o.emit("onTouchStart",o,b)}}},o.onTouchMove=function(b){if(b.originalEvent&&(b=b.originalEvent),!(C&&"mousemove"===b.type||b.preventedByNestedSwiper)){if(o.params.onlyExternal)return r=!0,void(o.allowClick=!1);if(C&&document.activeElement&&b.target===document.activeElement&&a(b.target).is(z))return r=!0,void(o.allowClick=!1);if(o.emit("onTouchMove",o,b),!(b.targetTouches&&b.targetTouches.length>1)){if(o.touches.currentX="touchmove"===b.type?b.targetTouches[0].pageX:b.pageX,o.touches.currentY="touchmove"===b.type?b.targetTouches[0].pageY:b.pageY,"undefined"==typeof t){var c=180*Math.atan2(Math.abs(o.touches.currentY-o.touches.startY),Math.abs(o.touches.currentX-o.touches.startX))/Math.PI;t=e()?c>o.params.touchAngle:90-c>o.params.touchAngle}if(t&&o.emit("onTouchMoveOpposite",o,b),"undefined"==typeof D&&o.browser.ieTouch&&(o.touches.currentX!==o.touches.startX||o.touches.currentY!==o.touches.startY)&&(D=!0),q){if(t)return void(q=!1);if(D||!o.browser.ieTouch){o.allowClick=!1,o.emit("onSliderMove",o,b),b.preventDefault(),o.params.touchMoveStopPropagation&&!o.params.nested&&b.stopPropagation(),r||(d.loop&&o.fixLoop(),v=o.getWrapperTranslate(),o.setWrapperTransition(0),o.animating&&o.wrapper.trigger("webkitTransitionEnd transitionend oTransitionEnd MSTransitionEnd msTransitionEnd"),o.params.autoplay&&o.autoplaying&&(o.params.autoplayDisableOnInteraction?o.stopAutoplay():o.pauseAutoplay()),y=!1,o.params.grabCursor&&(o.container[0].style.cursor="move",o.container[0].style.cursor="-webkit-grabbing",o.container[0].style.cursor="-moz-grabbin",o.container[0].style.cursor="grabbing")),r=!0;var f=o.touches.diff=e()?o.touches.currentX-o.touches.startX:o.touches.currentY-o.touches.startY;f*=o.params.touchRatio,o.rtl&&(f=-f),o.swipeDirection=f>0?"prev":"next",u=f+v;var g=!0;if(f>0&&u>o.minTranslate()?(g=!1,o.params.resistance&&(u=o.minTranslate()-1+Math.pow(-o.minTranslate()+v+f,o.params.resistanceRatio))):0>f&&u<o.maxTranslate()&&(g=!1,o.params.resistance&&(u=o.maxTranslate()+1-Math.pow(o.maxTranslate()-v-f,o.params.resistanceRatio))),g&&(b.preventedByNestedSwiper=!0),!o.params.allowSwipeToNext&&"next"===o.swipeDirection&&v>u&&(u=v),!o.params.allowSwipeToPrev&&"prev"===o.swipeDirection&&u>v&&(u=v),o.params.followFinger){if(o.params.threshold>0){if(!(Math.abs(f)>o.params.threshold||w))return void(u=v);if(!w)return w=!0,o.touches.startX=o.touches.currentX,o.touches.startY=o.touches.currentY,u=v,void(o.touches.diff=e()?o.touches.currentX-o.touches.startX:o.touches.currentY-o.touches.startY)}(o.params.freeMode||o.params.watchSlidesProgress)&&o.updateActiveIndex(),o.params.freeMode&&(0===B.length&&B.push({position:o.touches[e()?"startX":"startY"],time:s}),B.push({position:o.touches[e()?"currentX":"currentY"],time:(new Date).getTime()})),o.updateProgress(u),o.setWrapperTranslate(u)}}}}}},o.onTouchEnd=function(b){if(b.originalEvent&&(b=b.originalEvent),o.emit("onTouchEnd",o,b),q){o.params.grabCursor&&r&&q&&(o.container[0].style.cursor="move",o.container[0].style.cursor="-webkit-grab",o.container[0].style.cursor="-moz-grab",o.container[0].style.cursor="grab");var c=Date.now(),d=c-s;if(o.allowClick&&(o.updateClickedSlide(b),o.emit("onTap",o,b),300>d&&c-A>300&&(x&&clearTimeout(x),x=setTimeout(function(){o&&(o.params.paginationHide&&o.paginationContainer.length>0&&!a(b.target).hasClass(o.params.bulletClass)&&o.paginationContainer.toggleClass(o.params.paginationHiddenClass),o.emit("onClick",o,b))},300)),300>d&&300>c-A&&(x&&clearTimeout(x),o.emit("onDoubleTap",o,b))),A=Date.now(),setTimeout(function(){o&&o.allowClick&&(o.allowClick=!0)},0),!q||!r||!o.swipeDirection||0===o.touches.diff||u===v)return void(q=r=!1);q=r=!1;var e;if(e=o.params.followFinger?o.rtl?o.translate:-o.translate:-u,o.params.freeMode){if(e<-o.minTranslate())return void o.slideTo(o.activeIndex);if(e>-o.maxTranslate())return void o.slideTo(o.slides.length-1);if(o.params.freeModeMomentum){if(B.length>1){var f=B.pop(),g=B.pop(),h=f.position-g.position,i=f.time-g.time;o.velocity=h/i,o.velocity=o.velocity/2,Math.abs(o.velocity)<.02&&(o.velocity=0),(i>150||(new Date).getTime()-f.time>300)&&(o.velocity=0)}else o.velocity=0;B.length=0;var j=1e3*o.params.freeModeMomentumRatio,k=o.velocity*j,l=o.translate+k;o.rtl&&(l=-l);var m,n=!1,p=20*Math.abs(o.velocity)*o.params.freeModeMomentumBounceRatio;l<o.maxTranslate()&&(o.params.freeModeMomentumBounce?(l+o.maxTranslate()<-p&&(l=o.maxTranslate()-p),m=o.maxTranslate(),n=!0,y=!0):l=o.maxTranslate()),l>o.minTranslate()&&(o.params.freeModeMomentumBounce?(l-o.minTranslate()>p&&(l=o.minTranslate()+p),m=o.minTranslate(),n=!0,y=!0):l=o.minTranslate()),0!==o.velocity&&(j=o.rtl?Math.abs((-l-o.translate)/o.velocity):Math.abs((l-o.translate)/o.velocity)),o.params.freeModeMomentumBounce&&n?(o.updateProgress(m),o.setWrapperTransition(j),o.setWrapperTranslate(l),o.onTransitionStart(),o.animating=!0,o.wrapper.transitionEnd(function(){y&&(o.emit("onMomentumBounce",o),o.setWrapperTransition(o.params.speed),o.setWrapperTranslate(m),o.wrapper.transitionEnd(function(){o.onTransitionEnd()}))})):o.velocity?(o.updateProgress(l),o.setWrapperTransition(j),o.setWrapperTranslate(l),o.onTransitionStart(),o.animating||(o.animating=!0,o.wrapper.transitionEnd(function(){o.onTransitionEnd()}))):o.updateProgress(l),o.updateActiveIndex()}return void((!o.params.freeModeMomentum||d>=o.params.longSwipesMs)&&(o.updateProgress(),o.updateActiveIndex()))}var t,w=0,z=o.slidesSizesGrid[0];for(t=0;t<o.slidesGrid.length;t+=o.params.slidesPerGroup)"undefined"!=typeof o.slidesGrid[t+o.params.slidesPerGroup]?e>=o.slidesGrid[t]&&e<o.slidesGrid[t+o.params.slidesPerGroup]&&(w=t,z=o.slidesGrid[t+o.params.slidesPerGroup]-o.slidesGrid[t]):e>=o.slidesGrid[t]&&(w=t,z=o.slidesGrid[o.slidesGrid.length-1]-o.slidesGrid[o.slidesGrid.length-2]);var C=(e-o.slidesGrid[w])/z;if(d>o.params.longSwipesMs){if(!o.params.longSwipes)return void o.slideTo(o.activeIndex);"next"===o.swipeDirection&&(C>=o.params.longSwipesRatio?o.slideTo(w+o.params.slidesPerGroup):o.slideTo(w)),"prev"===o.swipeDirection&&(C>1-o.params.longSwipesRatio?o.slideTo(w+o.params.slidesPerGroup):o.slideTo(w))}else{if(!o.params.shortSwipes)return void o.slideTo(o.activeIndex);"next"===o.swipeDirection&&o.slideTo(w+o.params.slidesPerGroup),"prev"===o.swipeDirection&&o.slideTo(w)}}},o._slideTo=function(a,b){return o.slideTo(a,b,!0,!0)},o.slideTo=function(a,b,c,d){"undefined"==typeof c&&(c=!0),"undefined"==typeof a&&(a=0),0>a&&(a=0),o.snapIndex=Math.floor(a/o.params.slidesPerGroup),o.snapIndex>=o.snapGrid.length&&(o.snapIndex=o.snapGrid.length-1);var e=-o.snapGrid[o.snapIndex];o.params.autoplay&&o.autoplaying&&(d||!o.params.autoplayDisableOnInteraction?o.pauseAutoplay(b):o.stopAutoplay()),o.updateProgress(e);for(var f=0;f<o.slidesGrid.length;f++)-e>=o.slidesGrid[f]&&(a=f);return"undefined"==typeof b&&(b=o.params.speed),o.previousIndex=o.activeIndex||0,o.activeIndex=a,e===o.translate?(o.updateClasses(),!1):(o.onTransitionStart(c),0===b?(o.setWrapperTransition(0),o.setWrapperTranslate(e),o.onTransitionEnd(c)):(o.setWrapperTransition(b),o.setWrapperTranslate(e),o.animating||(o.animating=!0,o.wrapper.transitionEnd(function(){o.onTransitionEnd(c)}))),o.updateClasses(),!0)},o.onTransitionStart=function(a){"undefined"==typeof a&&(a=!0),o.lazy&&o.lazy.onTransitionStart(),a&&(o.emit("onTransitionStart",o),o.activeIndex!==o.previousIndex&&o.emit("onSlideChangeStart",o))},o.onTransitionEnd=function(a){o.animating=!1,o.setWrapperTransition(0),"undefined"==typeof a&&(a=!0),o.lazy&&o.lazy.onTransitionEnd(),a&&(o.emit("onTransitionEnd",o),o.activeIndex!==o.previousIndex&&o.emit("onSlideChangeEnd",o)),o.params.hashnav&&o.hashnav&&o.hashnav.setHash()},o.slideNext=function(a,b,c){return o.params.loop?o.animating?!1:(o.fixLoop(),o.slideTo(o.activeIndex+o.params.slidesPerGroup,b,a,c)):o.slideTo(o.activeIndex+o.params.slidesPerGroup,b,a,c)},o._slideNext=function(a){return o.slideNext(!0,a,!0)},o.slidePrev=function(a,b,c){return o.params.loop?o.animating?!1:(o.fixLoop(),o.slideTo(o.activeIndex-1,b,a,c)):o.slideTo(o.activeIndex-1,b,a,c)},o._slidePrev=function(a){return o.slidePrev(!0,a,!0)},o.slideReset=function(a,b){return o.slideTo(o.activeIndex,b,a)},o.setWrapperTransition=function(a,b){o.wrapper.transition(a),"slide"!==o.params.effect&&o.effects[o.params.effect]&&o.effects[o.params.effect].setTransition(a),o.params.parallax&&o.parallax&&o.parallax.setTransition(a),o.params.scrollbar&&o.scrollbar&&o.scrollbar.setTransition(a),o.params.control&&o.controller&&o.controller.setTransition(a,b),o.emit("onSetTransition",o,a)},o.setWrapperTranslate=function(a,b,c){var d=0,f=0,g=0;e()?d=o.rtl?-a:a:f=a,o.params.virtualTranslate||(o.support.transforms3d?o.wrapper.transform("translate3d("+d+"px, "+f+"px, "+g+"px)"):o.wrapper.transform("translate("+d+"px, "+f+"px)")),o.translate=e()?d:f,b&&o.updateActiveIndex(),"slide"!==o.params.effect&&o.effects[o.params.effect]&&o.effects[o.params.effect].setTranslate(o.translate),o.params.parallax&&o.parallax&&o.parallax.setTranslate(o.translate),o.params.scrollbar&&o.scrollbar&&o.scrollbar.setTranslate(o.translate),o.params.control&&o.controller&&o.controller.setTranslate(o.translate,c),o.emit("onSetTranslate",o,o.translate)},o.getTranslate=function(a,b){var c,d,e,f;return"undefined"==typeof b&&(b="x"),o.params.virtualTranslate?o.rtl?-o.translate:o.translate:(e=window.getComputedStyle(a,null),window.WebKitCSSMatrix?f=new WebKitCSSMatrix("none"===e.webkitTransform?"":e.webkitTransform):(f=e.MozTransform||e.OTransform||e.MsTransform||e.msTransform||e.transform||e.getPropertyValue("transform").replace("translate(","matrix(1, 0, 0, 1,"),c=f.toString().split(",")),"x"===b&&(d=window.WebKitCSSMatrix?f.m41:16===c.length?parseFloat(c[12]):parseFloat(c[4])),"y"===b&&(d=window.WebKitCSSMatrix?f.m42:16===c.length?parseFloat(c[13]):parseFloat(c[5])),o.rtl&&d&&(d=-d),d||0)},o.getWrapperTranslate=function(a){return"undefined"==typeof a&&(a=e()?"x":"y"),o.getTranslate(o.wrapper[0],a)},o.observers=[],o.initObservers=function(){if(o.params.observeParents)for(var a=o.container.parents(),b=0;b<a.length;b++)h(a[b]);h(o.container[0],{childList:!1}),h(o.wrapper[0],{attributes:!1})},o.disconnectObservers=function(){for(var a=0;a<o.observers.length;a++)o.observers[a].disconnect();o.observers=[]},o.createLoop=function(){o.wrapper.children("."+o.params.slideClass+"."+o.params.slideDuplicateClass).remove();var b=o.wrapper.children("."+o.params.slideClass);o.loopedSlides=parseInt(o.params.loopedSlides||o.params.slidesPerView,10),o.loopedSlides=o.loopedSlides+o.params.loopAdditionalSlides,o.loopedSlides>b.length&&(o.loopedSlides=b.length);var c,d=[],e=[];for(b.each(function(c,f){var g=a(this);c<o.loopedSlides&&e.push(f),c<b.length&&c>=b.length-o.loopedSlides&&d.push(f),g.attr("data-swiper-slide-index",c)}),c=0;c<e.length;c++)o.wrapper.append(a(e[c].cloneNode(!0)).addClass(o.params.slideDuplicateClass));for(c=d.length-1;c>=0;c--)o.wrapper.prepend(a(d[c].cloneNode(!0)).addClass(o.params.slideDuplicateClass))},o.destroyLoop=function(){o.wrapper.children("."+o.params.slideClass+"."+o.params.slideDuplicateClass).remove(),o.slides.removeAttr("data-swiper-slide-index")},o.fixLoop=function(){var a;o.activeIndex<o.loopedSlides?(a=o.slides.length-3*o.loopedSlides+o.activeIndex,a+=o.loopedSlides,o.slideTo(a,0,!1,!0)):("auto"===o.params.slidesPerView&&o.activeIndex>=2*o.loopedSlides||o.activeIndex>o.slides.length-2*o.params.slidesPerView)&&(a=-o.slides.length+o.activeIndex+o.loopedSlides,a+=o.loopedSlides,o.slideTo(a,0,!1,!0))},o.appendSlide=function(a){if(o.params.loop&&o.destroyLoop(),"object"==typeof a&&a.length)for(var b=0;b<a.length;b++)a[b]&&o.wrapper.append(a[b]);else o.wrapper.append(a);o.params.loop&&o.createLoop(),o.params.observer&&o.support.observer||o.update(!0)},o.prependSlide=function(a){o.params.loop&&o.destroyLoop();var b=o.activeIndex+1;if("object"==typeof a&&a.length){for(var c=0;c<a.length;c++)a[c]&&o.wrapper.prepend(a[c]);b=o.activeIndex+a.length}else o.wrapper.prepend(a);o.params.loop&&o.createLoop(),o.params.observer&&o.support.observer||o.update(!0),o.slideTo(b,0,!1)},o.removeSlide=function(a){o.params.loop&&o.destroyLoop();var b,c=o.activeIndex;if("object"==typeof a&&a.length){for(var d=0;d<a.length;d++)b=a[d],o.slides[b]&&o.slides.eq(b).remove(),c>b&&c--;c=Math.max(c,0)}else b=a,o.slides[b]&&o.slides.eq(b).remove(),c>b&&c--,c=Math.max(c,0);o.params.observer&&o.support.observer||o.update(!0),o.slideTo(c,0,!1)},o.removeAllSlides=function(){for(var a=[],b=0;b<o.slides.length;b++)a.push(b);o.removeSlide(a)},o.effects={fade:{fadeIndex:null,setTranslate:function(){for(var a=0;a<o.slides.length;a++){var b=o.slides.eq(a),c=b[0].swiperSlideOffset,d=-c;o.params.virtualTranslate||(d-=o.translate);var f=0;e()||(f=d,d=0);var g=o.params.fade.crossFade?Math.max(1-Math.abs(b[0].progress),0):1+Math.min(Math.max(b[0].progress,-1),0);g>0&&1>g&&(o.effects.fade.fadeIndex=a),b.css({opacity:g}).transform("translate3d("+d+"px, "+f+"px, 0px)")}},setTransition:function(a){if(o.slides.transition(a),o.params.virtualTranslate&&0!==a){var b=null!==o.effects.fade.fadeIndex?o.effects.fade.fadeIndex:o.activeIndex;o.slides.eq(b).transitionEnd(function(){for(var a=["webkitTransitionEnd","transitionend","oTransitionEnd","MSTransitionEnd","msTransitionEnd"],b=0;b<a.length;b++)o.wrapper.trigger(a[b])})}}},cube:{setTranslate:function(){var b,c=0;o.params.cube.shadow&&(e()?(b=o.wrapper.find(".swiper-cube-shadow"),0===b.length&&(b=a('<div class="swiper-cube-shadow"></div>'),o.wrapper.append(b)),b.css({height:o.width+"px"})):(b=o.container.find(".swiper-cube-shadow"),0===b.length&&(b=a('<div class="swiper-cube-shadow"></div>'),o.container.append(b))));for(var d=0;d<o.slides.length;d++){var f=o.slides.eq(d),g=90*d,h=Math.floor(g/360);o.rtl&&(g=-g,h=Math.floor(-g/360));var i=Math.max(Math.min(f[0].progress,1),-1),j=0,k=0,l=0;d%4===0?(j=4*-h*o.size,l=0):(d-1)%4===0?(j=0,l=4*-h*o.size):(d-2)%4===0?(j=o.size+4*h*o.size,l=o.size):(d-3)%4===0&&(j=-o.size,l=3*o.size+4*o.size*h),o.rtl&&(j=-j),e()||(k=j,j=0);var m="rotateX("+(e()?0:-g)+"deg) rotateY("+(e()?g:0)+"deg) translate3d("+j+"px, "+k+"px, "+l+"px)";if(1>=i&&i>-1&&(c=90*d+90*i,o.rtl&&(c=90*-d-90*i)),f.transform(m),o.params.cube.slideShadows){var n=e()?f.find(".swiper-slide-shadow-left"):f.find(".swiper-slide-shadow-top"),p=e()?f.find(".swiper-slide-shadow-right"):f.find(".swiper-slide-shadow-bottom");0===n.length&&(n=a('<div class="swiper-slide-shadow-'+(e()?"left":"top")+'"></div>'),f.append(n)),0===p.length&&(p=a('<div class="swiper-slide-shadow-'+(e()?"right":"bottom")+'"></div>'),f.append(p)),n.length&&(n[0].style.opacity=-f[0].progress),p.length&&(p[0].style.opacity=f[0].progress)}}if(o.wrapper.css({"-webkit-transform-origin":"50% 50% -"+o.size/2+"px","-moz-transform-origin":"50% 50% -"+o.size/2+"px","-ms-transform-origin":"50% 50% -"+o.size/2+"px",
"transform-origin":"50% 50% -"+o.size/2+"px"}),o.params.cube.shadow)if(e())b.transform("translate3d(0px, "+(o.width/2+o.params.cube.shadowOffset)+"px, "+-o.width/2+"px) rotateX(90deg) rotateZ(0deg) scale("+o.params.cube.shadowScale+")");else{var q=Math.abs(c)-90*Math.floor(Math.abs(c)/90),r=1.5-(Math.sin(2*q*Math.PI/360)/2+Math.cos(2*q*Math.PI/360)/2),s=o.params.cube.shadowScale,t=o.params.cube.shadowScale/r,u=o.params.cube.shadowOffset;b.transform("scale3d("+s+", 1, "+t+") translate3d(0px, "+(o.height/2+u)+"px, "+-o.height/2/t+"px) rotateX(-90deg)")}var v=o.isSafari||o.isUiWebView?-o.size/2:0;o.wrapper.transform("translate3d(0px,0,"+v+"px) rotateX("+(e()?0:c)+"deg) rotateY("+(e()?-c:0)+"deg)")},setTransition:function(a){o.slides.transition(a).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(a),o.params.cube.shadow&&!e()&&o.container.find(".swiper-cube-shadow").transition(a)}},coverflow:{setTranslate:function(){for(var b=o.translate,c=e()?-b+o.width/2:-b+o.height/2,d=e()?o.params.coverflow.rotate:-o.params.coverflow.rotate,f=o.params.coverflow.depth,g=0,h=o.slides.length;h>g;g++){var i=o.slides.eq(g),j=o.slidesSizesGrid[g],k=i[0].swiperSlideOffset,l=(c-k-j/2)/j*o.params.coverflow.modifier,m=e()?d*l:0,n=e()?0:d*l,p=-f*Math.abs(l),q=e()?0:o.params.coverflow.stretch*l,r=e()?o.params.coverflow.stretch*l:0;Math.abs(r)<.001&&(r=0),Math.abs(q)<.001&&(q=0),Math.abs(p)<.001&&(p=0),Math.abs(m)<.001&&(m=0),Math.abs(n)<.001&&(n=0);var s="translate3d("+r+"px,"+q+"px,"+p+"px)  rotateX("+n+"deg) rotateY("+m+"deg)";if(i.transform(s),i[0].style.zIndex=-Math.abs(Math.round(l))+1,o.params.coverflow.slideShadows){var t=e()?i.find(".swiper-slide-shadow-left"):i.find(".swiper-slide-shadow-top"),u=e()?i.find(".swiper-slide-shadow-right"):i.find(".swiper-slide-shadow-bottom");0===t.length&&(t=a('<div class="swiper-slide-shadow-'+(e()?"left":"top")+'"></div>'),i.append(t)),0===u.length&&(u=a('<div class="swiper-slide-shadow-'+(e()?"right":"bottom")+'"></div>'),i.append(u)),t.length&&(t[0].style.opacity=l>0?l:0),u.length&&(u[0].style.opacity=-l>0?-l:0)}}if(o.browser.ie){var v=o.wrapper[0].style;v.perspectiveOrigin=c+"px 50%"}},setTransition:function(a){o.slides.transition(a).find(".swiper-slide-shadow-top, .swiper-slide-shadow-right, .swiper-slide-shadow-bottom, .swiper-slide-shadow-left").transition(a)}}},o.lazy={initialImageLoaded:!1,loadImageInSlide:function(b){if("undefined"!=typeof b&&0!==o.slides.length){var c=o.slides.eq(b),d=c.find("img.swiper-lazy:not(.swiper-lazy-loaded):not(.swiper-lazy-loading)");0!==d.length&&d.each(function(){var b=a(this);b.addClass("swiper-lazy-loading");var d=b.attr("data-src");o.loadImage(b[0],d,!1,function(){b.attr("src",d),b.removeAttr("data-src"),b.addClass("swiper-lazy-loaded").removeClass("swiper-lazy-loading"),c.find(".swiper-lazy-preloader, .preloader").remove(),o.emit("onLazyImageReady",o,c[0],b[0])}),o.emit("onLazyImageLoad",o,c[0],b[0])})}},load:function(){if(o.params.watchSlidesVisibility)o.wrapper.children("."+o.params.slideVisibleClass).each(function(){o.lazy.loadImageInSlide(a(this).index())});else if(o.params.slidesPerView>1)for(var b=o.activeIndex;b<o.activeIndex+o.params.slidesPerView;b++)o.slides[b]&&o.lazy.loadImageInSlide(b);else o.lazy.loadImageInSlide(o.activeIndex);if(o.params.lazyLoadingInPrevNext){var c=o.wrapper.children("."+o.params.slideNextClass);c.length>0&&o.lazy.loadImageInSlide(c.index());var d=o.wrapper.children("."+o.params.slidePrevClass);d.length>0&&o.lazy.loadImageInSlide(d.index())}},onTransitionStart:function(){o.params.lazyLoading&&(o.params.lazyLoadingOnTransitionStart||!o.params.lazyLoadingOnTransitionStart&&!o.lazy.initialImageLoaded)&&(o.lazy.initialImageLoaded=!0,o.lazy.load())},onTransitionEnd:function(){o.params.lazyLoading&&!o.params.lazyLoadingOnTransitionStart&&o.lazy.load()}},o.scrollbar={set:function(){if(o.params.scrollbar){var b=o.scrollbar;b.track=a(o.params.scrollbar),b.drag=b.track.find(".swiper-scrollbar-drag"),0===b.drag.length&&(b.drag=a('<div class="swiper-scrollbar-drag"></div>'),b.track.append(b.drag)),b.drag[0].style.width="",b.drag[0].style.height="",b.trackSize=e()?b.track[0].offsetWidth:b.track[0].offsetHeight,b.divider=o.size/o.virtualSize,b.moveDivider=b.divider*(b.trackSize/o.size),b.dragSize=b.trackSize*b.divider,e()?b.drag[0].style.width=b.dragSize+"px":b.drag[0].style.height=b.dragSize+"px",b.divider>=1?b.track[0].style.display="none":b.track[0].style.display="",o.params.scrollbarHide&&(b.track[0].style.opacity=0)}},setTranslate:function(){if(o.params.scrollbar){var a,b=o.scrollbar,c=b.dragSize;a=(b.trackSize-b.dragSize)*o.progress,o.rtl&&e()?(a=-a,a>0?(c=b.dragSize-a,a=0):-a+b.dragSize>b.trackSize&&(c=b.trackSize+a)):0>a?(c=b.dragSize+a,a=0):a+b.dragSize>b.trackSize&&(c=b.trackSize-a),e()?(o.support.transforms3d?b.drag.transform("translate3d("+a+"px, 0, 0)"):b.drag.transform("translateX("+a+"px)"),b.drag[0].style.width=c+"px"):(o.support.transforms3d?b.drag.transform("translate3d(0px, "+a+"px, 0)"):b.drag.transform("translateY("+a+"px)"),b.drag[0].style.height=c+"px"),o.params.scrollbarHide&&(clearTimeout(b.timeout),b.track[0].style.opacity=1,b.timeout=setTimeout(function(){b.track[0].style.opacity=0,b.track.transition(400)},1e3))}},setTransition:function(a){o.params.scrollbar&&o.scrollbar.drag.transition(a)}},o.controller={setTranslate:function(a,c){var d,e,f=o.params.control;if(o.isArray(f))for(var g=0;g<f.length;g++)f[g]!==c&&f[g]instanceof b&&(a=f[g].rtl&&"horizontal"===f[g].params.direction?-o.translate:o.translate,d=(f[g].maxTranslate()-f[g].minTranslate())/(o.maxTranslate()-o.minTranslate()),e=(a-o.minTranslate())*d+f[g].minTranslate(),o.params.controlInverse&&(e=f[g].maxTranslate()-e),f[g].updateProgress(e),f[g].setWrapperTranslate(e,!1,o),f[g].updateActiveIndex());else f instanceof b&&c!==f&&(a=f.rtl&&"horizontal"===f.params.direction?-o.translate:o.translate,d=(f.maxTranslate()-f.minTranslate())/(o.maxTranslate()-o.minTranslate()),e=(a-o.minTranslate())*d+f.minTranslate(),o.params.controlInverse&&(e=f.maxTranslate()-e),f.updateProgress(e),f.setWrapperTranslate(e,!1,o),f.updateActiveIndex())},setTransition:function(a,c){var d=o.params.control;if(o.isArray(d))for(var e=0;e<d.length;e++)d[e]!==c&&d[e]instanceof b&&d[e].setWrapperTransition(a,o);else d instanceof b&&c!==d&&d.setWrapperTransition(a,o)}},o.parallax={setTranslate:function(){o.container.children("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function(){i(this,o.progress)}),o.slides.each(function(){var b=a(this);b.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function(){var a=Math.min(Math.max(b[0].progress,-1),1);i(this,a)})})},setTransition:function(b){"undefined"==typeof b&&(b=o.params.speed),o.container.find("[data-swiper-parallax], [data-swiper-parallax-x], [data-swiper-parallax-y]").each(function(){var c=a(this),d=parseInt(c.attr("data-swiper-parallax-duration"),10)||b;0===b&&(d=0),c.transition(d)})}},o._plugins=[];for(var E in o.plugins)if(o.plugins.hasOwnProperty(E)){var F=o.plugins[E](o,o.params[E]);F&&o._plugins.push(F)}return o.callPlugins=function(a){for(var b=0;b<o._plugins.length;b++)a in o._plugins[b]&&o._plugins[b][a](arguments[1],arguments[2],arguments[3],arguments[4],arguments[5])},o.emitterEventListeners={},o.emit=function(a){o.params[a]&&o.params[a](arguments[1],arguments[2],arguments[3],arguments[4],arguments[5]);var b;if(o){if(o.emitterEventListeners[a])for(b=0;b<o.emitterEventListeners[a].length;b++)o.emitterEventListeners[a][b](arguments[1],arguments[2],arguments[3],arguments[4],arguments[5]);o.callPlugins&&o.callPlugins(a,arguments[1],arguments[2],arguments[3],arguments[4],arguments[5])}},o.on=function(a,b){return a=j(a),o.emitterEventListeners[a]||(o.emitterEventListeners[a]=[]),o.emitterEventListeners[a].push(b),o},o.off=function(a,b){var c;if(a=j(a),"undefined"==typeof b)return o.emitterEventListeners[a]=[],o;if(o.emitterEventListeners[a]&&0!==o.emitterEventListeners[a].length){for(c=0;c<o.emitterEventListeners[a].length;c++)o.emitterEventListeners[a][c]===b&&o.emitterEventListeners[a].splice(c,1);return o}},o.once=function(a,b){a=j(a);var c=function(){b(arguments[0],arguments[1],arguments[2],arguments[3],arguments[4]),o.off(a,c)};return o.on(a,c),o},o.a11y={makeFocusable:function(a){return a[0].tabIndex="0",a},addRole:function(a,b){return a.attr("role",b),a},addLabel:function(a,b){return a.attr("aria-label",b),a},disable:function(a){return a.attr("aria-disabled",!0),a},enable:function(a){return a.attr("aria-disabled",!1),a},onEnterKey:function(b){13===b.keyCode&&(a(b.target).is(o.params.nextButton)?(o.onClickNext(b),o.isEnd?o.a11y.notify(o.params.lastSlideMsg):o.a11y.notify(o.params.nextSlideMsg)):a(b.target).is(o.params.prevButton)&&(o.onClickPrev(b),o.isBeginning?o.a11y.notify(o.params.firstSlideMsg):o.a11y.notify(o.params.prevSlideMsg)))},liveRegion:a('<span class="swiper-notification" aria-live="assertive" aria-atomic="true"></span>'),notify:function(a){var b=o.a11y.liveRegion;0!==b.length&&(b.html(""),b.html(a))},init:function(){if(o.params.nextButton){var b=a(o.params.nextButton);o.a11y.makeFocusable(b),o.a11y.addRole(b,"button"),o.a11y.addLabel(b,o.params.nextSlideMsg)}if(o.params.prevButton){var c=a(o.params.prevButton);o.a11y.makeFocusable(c),o.a11y.addRole(c,"button"),o.a11y.addLabel(c,o.params.prevSlideMsg)}a(o.container).append(o.a11y.liveRegion)},destroy:function(){o.a11y.liveRegion&&o.a11y.liveRegion.length>0&&o.a11y.liveRegion.remove()}},o.init=function(){o.params.loop&&o.createLoop(),o.updateContainerSize(),o.updateSlidesSize(),o.updatePagination(),o.params.scrollbar&&o.scrollbar&&o.scrollbar.set(),"slide"!==o.params.effect&&o.effects[o.params.effect]&&(o.params.loop||o.updateProgress(),o.effects[o.params.effect].setTranslate()),o.params.loop?o.slideTo(o.params.initialSlide+o.loopedSlides,0,o.params.runCallbacksOnInit):(o.slideTo(o.params.initialSlide,0,o.params.runCallbacksOnInit),0===o.params.initialSlide&&(o.parallax&&o.params.parallax&&o.parallax.setTranslate(),o.lazy&&o.params.lazyLoading&&o.lazy.load())),o.attachEvents(),o.params.observer&&o.support.observer&&o.initObservers(),o.params.preloadImages&&!o.params.lazyLoading&&o.preloadImages(),o.params.autoplay&&o.startAutoplay(),o.params.keyboardControl&&o.enableKeyboardControl&&o.enableKeyboardControl(),o.params.mousewheelControl&&o.enableMousewheelControl&&o.enableMousewheelControl(),o.params.hashnav&&o.hashnav&&o.hashnav.init(),o.params.a11y&&o.a11y&&o.a11y.init(),o.emit("onInit",o)},o.cleanupStyles=function(){o.container.removeClass(o.classNames.join(" ")).removeAttr("style"),o.wrapper.removeAttr("style"),o.slides&&o.slides.length&&o.slides.removeClass([o.params.slideVisibleClass,o.params.slideActiveClass,o.params.slideNextClass,o.params.slidePrevClass].join(" ")).removeAttr("style").removeAttr("data-swiper-column").removeAttr("data-swiper-row"),o.paginationContainer&&o.paginationContainer.length&&o.paginationContainer.removeClass(o.params.paginationHiddenClass),o.bullets&&o.bullets.length&&o.bullets.removeClass(o.params.bulletActiveClass),o.params.prevButton&&a(o.params.prevButton).removeClass(o.params.buttonDisabledClass),o.params.nextButton&&a(o.params.nextButton).removeClass(o.params.buttonDisabledClass),o.params.scrollbar&&o.scrollbar&&(o.scrollbar.track&&o.scrollbar.track.length&&o.scrollbar.track.removeAttr("style"),o.scrollbar.drag&&o.scrollbar.drag.length&&o.scrollbar.drag.removeAttr("style"))},o.destroy=function(a,b){o.detachEvents(),o.stopAutoplay(),o.params.loop&&o.destroyLoop(),b&&o.cleanupStyles(),o.disconnectObservers(),o.params.keyboardControl&&o.disableKeyboardControl&&o.disableKeyboardControl(),o.params.mousewheelControl&&o.disableMousewheelControl&&o.disableMousewheelControl(),o.params.a11y&&o.a11y&&o.a11y.destroy(),o.emit("onDestroy"),a!==!1&&(o=null)},o.init(),o}};b.prototype={defaults:{direction:"horizontal",touchEventsTarget:"container",initialSlide:0,speed:300,autoplay:!1,autoplayDisableOnInteraction:!0,freeMode:!1,freeModeMomentum:!0,freeModeMomentumRatio:1,freeModeMomentumBounce:!0,freeModeMomentumBounceRatio:1,setWrapperSize:!1,virtualTranslate:!1,effect:"slide",coverflow:{rotate:50,stretch:0,depth:100,modifier:1,slideShadows:!0},cube:{slideShadows:!0,shadow:!0,shadowOffset:20,shadowScale:.94},fade:{crossFade:!1},parallax:!1,scrollbar:null,scrollbarHide:!0,keyboardControl:!1,mousewheelControl:!1,mousewheelForceToAxis:!1,hashnav:!1,spaceBetween:0,slidesPerView:1,slidesPerColumn:1,slidesPerColumnFill:"column",slidesPerGroup:1,centeredSlides:!1,touchRatio:1,touchAngle:45,simulateTouch:!0,shortSwipes:!0,longSwipes:!0,longSwipesRatio:.5,longSwipesMs:300,followFinger:!0,onlyExternal:!1,threshold:0,touchMoveStopPropagation:!0,pagination:null,paginationClickable:!1,paginationHide:!1,paginationBulletRender:null,resistance:!0,resistanceRatio:.85,nextButton:null,prevButton:null,watchSlidesProgress:!1,watchSlidesVisibility:!1,grabCursor:!1,preventClicks:!0,preventClicksPropagation:!0,slideToClickedSlide:!1,lazyLoading:!1,lazyLoadingInPrevNext:!1,lazyLoadingOnTransitionStart:!1,preloadImages:!0,updateOnImagesReady:!0,loop:!1,loopAdditionalSlides:0,loopedSlides:null,control:void 0,controlInverse:!1,allowSwipeToPrev:!0,allowSwipeToNext:!0,swipeHandler:null,noSwiping:!0,noSwipingClass:"swiper-no-swiping",slideClass:"swiper-slide",slideActiveClass:"swiper-slide-active",slideVisibleClass:"swiper-slide-visible",slideDuplicateClass:"swiper-slide-duplicate",slideNextClass:"swiper-slide-next",slidePrevClass:"swiper-slide-prev",wrapperClass:"swiper-wrapper",bulletClass:"swiper-pagination-bullet",bulletActiveClass:"swiper-pagination-bullet-active",buttonDisabledClass:"swiper-button-disabled",paginationHiddenClass:"swiper-pagination-hidden",observer:!1,observeParents:!1,a11y:!1,prevSlideMessage:"Previous slide",nextSlideMessage:"Next slide",firstSlideMessage:"This is the first slide",lastSlideMessage:"This is the last slide",runCallbacksOnInit:!0},isSafari:function(){var a=navigator.userAgent.toLowerCase();return a.indexOf("safari")>=0&&a.indexOf("chrome")<0&&a.indexOf("android")<0}(),isUiWebView:/(iPhone|iPod|iPad).*AppleWebKit(?!.*Safari)/i.test(navigator.userAgent),isArray:function(a){return"[object Array]"===Object.prototype.toString.apply(a)},browser:{ie:window.navigator.pointerEnabled||window.navigator.msPointerEnabled,ieTouch:window.navigator.msPointerEnabled&&window.navigator.msMaxTouchPoints>1||window.navigator.pointerEnabled&&window.navigator.maxTouchPoints>1},device:function(){var a=navigator.userAgent,b=a.match(/(Android);?[\s\/]+([\d.]+)?/),c=a.match(/(iPad).*OS\s([\d_]+)/),d=!c&&a.match(/(iPhone\sOS)\s([\d_]+)/);return{ios:c||d||c,android:b}}(),support:{touch:window.Modernizr&&Modernizr.touch===!0||function(){return!!("ontouchstart"in window||window.DocumentTouch&&document instanceof DocumentTouch)}(),transforms3d:window.Modernizr&&Modernizr.csstransforms3d===!0||function(){var a=document.createElement("div").style;return"webkitPerspective"in a||"MozPerspective"in a||"OPerspective"in a||"MsPerspective"in a||"perspective"in a}(),flexbox:function(){for(var a=document.createElement("div").style,b="alignItems webkitAlignItems webkitBoxAlign msFlexAlign mozBoxAlign webkitFlexDirection msFlexDirection mozBoxDirection mozBoxOrient webkitBoxDirection webkitBoxOrient".split(" "),c=0;c<b.length;c++)if(b[c]in a)return!0}(),observer:function(){return"MutationObserver"in window||"WebkitMutationObserver"in window}()},plugins:{}},a.Swiper=b}(Zepto),+function(a){"use strict";a.Swiper.prototype.defaults.pagination=".page-current .swiper-pagination",a.swiper=function(b,c){return new a.Swiper(b,c)},a.fn.swiper=function(b){return new a.Swiper(this,b)},a.initSwiper=function(b){function c(a){function b(){a.destroy(),d.off("pageBeforeRemove",b)}d.on("pageBeforeRemove",b)}var d=a(b||document.body),e=d.find(".swiper-container");if(0!==e.length)for(var f=0;f<e.length;f++){var g,h=e.eq(f);if(h.data("swiper"))h.data("swiper").update(!0);else{g=h.dataset();var i=a.swiper(h[0],g);c(i)}}},a.reinitSwiper=function(b){var c=a(b||".page-current"),d=c.find(".swiper-container");if(0!==d.length)for(var e=0;e<d.length;e++){var f=d[0].swiper;f&&f.update(!0)}}}(Zepto),+function(a){"use strict";var b=function(b){var c,d=this,e=this.defaults;b=b||{};for(var f in e)"undefined"==typeof b[f]&&(b[f]=e[f]);d.params=b;var g=d.params.navbarTemplate||'<header class="bar bar-nav"><a class="icon icon-left pull-left photo-browser-close-link'+("popup"===d.params.type?" close-popup":"")+'"></a><h1 class="title"><div class="center sliding"><span class="photo-browser-current"></span> <span class="photo-browser-of">'+d.params.ofText+'</span> <span class="photo-browser-total"></span></div></h1></header>',h=d.params.toolbarTemplate||'<nav class="bar bar-tab"><a class="tab-item photo-browser-prev" href="#"><i class="icon icon-prev"></i></a><a class="tab-item photo-browser-next" href="#"><i class="icon icon-next"></i></a></nav>',i=d.params.template||'<div class="photo-browser photo-browser-'+d.params.theme+'">{{navbar}}{{toolbar}}<div data-page="photo-browser-slides" class="content">{{captions}}<div class="photo-browser-swiper-container swiper-container"><div class="photo-browser-swiper-wrapper swiper-wrapper">{{photos}}</div></div></div></div>',j=d.params.lazyLoading?d.params.photoLazyTemplate||'<div class="photo-browser-slide photo-browser-slide-lazy swiper-slide"><div class="preloader'+("dark"===d.params.theme?" preloader-white":"")+'"></div><span class="photo-browser-zoom-container"><img data-src="{{url}}" class="swiper-lazy"></span></div>':d.params.photoTemplate||'<div class="photo-browser-slide swiper-slide"><span class="photo-browser-zoom-container"><img src="{{url}}"></span></div>',k=d.params.captionsTheme||d.params.theme,l=d.params.captionsTemplate||'<div class="photo-browser-captions photo-browser-captions-'+k+'">{{captions}}</div>',m=d.params.captionTemplate||'<div class="photo-browser-caption" data-caption-index="{{captionIndex}}">{{caption}}</div>',n=d.params.objectTemplate||'<div class="photo-browser-slide photo-browser-object-slide swiper-slide">{{html}}</div>',o="",p="";for(c=0;c<d.params.photos.length;c++){var q=d.params.photos[c],r="";"string"==typeof q||q instanceof String?r=q.indexOf("<")>=0||q.indexOf(">")>=0?n.replace(/{{html}}/g,q):j.replace(/{{url}}/g,q):"object"==typeof q&&(q.hasOwnProperty("html")&&q.html.length>0?r=n.replace(/{{html}}/g,q.html):q.hasOwnProperty("url")&&q.url.length>0&&(r=j.replace(/{{url}}/g,q.url)),q.hasOwnProperty("caption")&&q.caption.length>0?p+=m.replace(/{{caption}}/g,q.caption).replace(/{{captionIndex}}/g,c):r=r.replace(/{{caption}}/g,"")),o+=r}var s=i.replace("{{navbar}}",d.params.navbar?g:"").replace("{{noNavbar}}",d.params.navbar?"":"no-navbar").replace("{{photos}}",o).replace("{{captions}}",l.replace(/{{captions}}/g,p)).replace("{{toolbar}}",d.params.toolbar?h:"");d.activeIndex=d.params.initialSlide,d.openIndex=d.activeIndex,d.opened=!1,d.open=function(b){return"undefined"==typeof b&&(b=d.activeIndex),b=parseInt(b,10),d.opened&&d.swiper?void d.swiper.slideTo(b):(d.opened=!0,d.openIndex=b,"standalone"===d.params.type&&a(d.params.container).append(s),"popup"===d.params.type&&(d.popup=a.popup('<div class="popup photo-browser-popup">'+s+"</div>"),a(d.popup).on("closed",d.onPopupClose)),"page"===d.params.type?(a(document).on("pageBeforeInit",d.onPageBeforeInit),a(document).on("pageBeforeRemove",d.onPageBeforeRemove),d.params.view||(d.params.view=a.mainView),void d.params.view.loadContent(s)):(d.layout(d.openIndex),void(d.params.onOpen&&d.params.onOpen(d))))},d.close=function(){d.opened=!1,d.swiperContainer&&0!==d.swiperContainer.length&&(d.params.onClose&&d.params.onClose(d),d.attachEvents(!0),"standalone"===d.params.type&&d.container.removeClass("photo-browser-in").addClass("photo-browser-out").transitionEnd(function(){d.container.remove()}),d.swiper.destroy(),d.swiper=d.swiperContainer=d.swiperWrapper=d.slides=t=u=v=void 0)},d.onPopupClose=function(){d.close(),a(d.popup).off("pageBeforeInit",d.onPopupClose)},d.onPageBeforeInit=function(b){"photo-browser-slides"===b.detail.page.name&&d.layout(d.openIndex),a(document).off("pageBeforeInit",d.onPageBeforeInit)},d.onPageBeforeRemove=function(b){"photo-browser-slides"===b.detail.page.name&&d.close(),a(document).off("pageBeforeRemove",d.onPageBeforeRemove)},d.onSliderTransitionStart=function(b){d.activeIndex=b.activeIndex;var c=b.activeIndex+1,e=b.slides.length;if(d.params.loop&&(e-=2,c-=b.loopedSlides,1>c&&(c=e+c),c>e&&(c-=e)),d.container.find(".photo-browser-current").text(c),d.container.find(".photo-browser-total").text(e),a(".photo-browser-prev, .photo-browser-next").removeClass("photo-browser-link-inactive"),b.isBeginning&&!d.params.loop&&a(".photo-browser-prev").addClass("photo-browser-link-inactive"),b.isEnd&&!d.params.loop&&a(".photo-browser-next").addClass("photo-browser-link-inactive"),d.captions.length>0){d.captionsContainer.find(".photo-browser-caption-active").removeClass("photo-browser-caption-active");var f=d.params.loop?b.slides.eq(b.activeIndex).attr("data-swiper-slide-index"):d.activeIndex;d.captionsContainer.find('[data-caption-index="'+f+'"]').addClass("photo-browser-caption-active")}var g=b.slides.eq(b.previousIndex).find("video");g.length>0&&"pause"in g[0]&&g[0].pause(),d.params.onSlideChangeStart&&d.params.onSlideChangeStart(b)},d.onSliderTransitionEnd=function(a){d.params.zoom&&t&&a.previousIndex!==a.activeIndex&&(u.transform("translate3d(0,0,0) scale(1)"),v.transform("translate3d(0,0,0)"),t=u=v=void 0,w=x=1),d.params.onSlideChangeEnd&&d.params.onSlideChangeEnd(a)},d.layout=function(b){"page"===d.params.type?d.container=a(".photo-browser-swiper-container").parents(".view"):d.container=a(".photo-browser"),"standalone"===d.params.type&&d.container.addClass("photo-browser-in"),d.swiperContainer=d.container.find(".photo-browser-swiper-container"),d.swiperWrapper=d.container.find(".photo-browser-swiper-wrapper"),d.slides=d.container.find(".photo-browser-slide"),d.captionsContainer=d.container.find(".photo-browser-captions"),d.captions=d.container.find(".photo-browser-caption");var c={nextButton:d.params.nextButton||".photo-browser-next",prevButton:d.params.prevButton||".photo-browser-prev",indexButton:d.params.indexButton,initialSlide:b,spaceBetween:d.params.spaceBetween,speed:d.params.speed,loop:d.params.loop,lazyLoading:d.params.lazyLoading,lazyLoadingInPrevNext:d.params.lazyLoadingInPrevNext,lazyLoadingOnTransitionStart:d.params.lazyLoadingOnTransitionStart,preloadImages:d.params.lazyLoading?!1:!0,onTap:function(a,b){d.params.onTap&&d.params.onTap(a,b)},onClick:function(a,b){d.params.exposition&&d.toggleExposition(),d.params.onClick&&d.params.onClick(a,b)},onDoubleTap:function(b,c){d.toggleZoom(a(c.target).parents(".photo-browser-slide")),d.params.onDoubleTap&&d.params.onDoubleTap(b,c)},onTransitionStart:function(a){d.onSliderTransitionStart(a)},onTransitionEnd:function(a){d.onSliderTransitionEnd(a)},onLazyImageLoad:function(a,b,c){d.params.onLazyImageLoad&&d.params.onLazyImageLoad(d,b,c)},onLazyImageReady:function(b,c,e){a(c).removeClass("photo-browser-slide-lazy"),d.params.onLazyImageReady&&d.params.onLazyImageReady(d,c,e)}};d.params.swipeToClose&&"page"!==d.params.type&&(c.onTouchStart=d.swipeCloseTouchStart,c.onTouchMoveOpposite=d.swipeCloseTouchMove,c.onTouchEnd=d.swipeCloseTouchEnd),d.swiper=a.swiper(d.swiperContainer,c),0===b&&d.onSliderTransitionStart(d.swiper),d.attachEvents()},d.attachEvents=function(a){var b=a?"off":"on";if(d.params.zoom){var c=d.params.loop?d.swiper.slides:d.slides;c[b]("gesturestart",d.onSlideGestureStart),c[b]("gesturechange",d.onSlideGestureChange),c[b]("gestureend",d.onSlideGestureEnd),c[b]("touchstart",d.onSlideTouchStart),c[b]("touchmove",d.onSlideTouchMove),c[b]("touchend",d.onSlideTouchEnd)}d.container.find(".photo-browser-close-link")[b]("click",d.close)},d.exposed=!1,d.toggleExposition=function(){d.container&&d.container.toggleClass("photo-browser-exposed"),d.params.expositionHideCaptions&&d.captionsContainer.toggleClass("photo-browser-captions-exposed"),d.exposed=!d.exposed},d.enableExposition=function(){d.container&&d.container.addClass("photo-browser-exposed"),d.params.expositionHideCaptions&&d.captionsContainer.addClass("photo-browser-captions-exposed"),d.exposed=!0},d.disableExposition=function(){d.container&&d.container.removeClass("photo-browser-exposed"),d.params.expositionHideCaptions&&d.captionsContainer.removeClass("photo-browser-captions-exposed"),d.exposed=!1};var t,u,v,w=1,x=1,y=!1;d.onSlideGestureStart=function(){return t||(t=a(this),u=t.find("img, svg, canvas"),v=u.parent(".photo-browser-zoom-container"),0!==v.length)?(u.transition(0),void(y=!0)):void(u=void 0)},d.onSlideGestureChange=function(a){u&&0!==u.length&&(w=a.scale*x,w>d.params.maxZoom&&(w=d.params.maxZoom-1+Math.pow(w-d.params.maxZoom+1,.5)),w<d.params.minZoom&&(w=d.params.minZoom+1-Math.pow(d.params.minZoom-w+1,.5)),u.transform("translate3d(0,0,0) scale("+w+")"))},d.onSlideGestureEnd=function(){u&&0!==u.length&&(w=Math.max(Math.min(w,d.params.maxZoom),d.params.minZoom),u.transition(d.params.speed).transform("translate3d(0,0,0) scale("+w+")"),x=w,y=!1,1===w&&(t=void 0))},d.toggleZoom=function(){t||(t=d.swiper.slides.eq(d.swiper.activeIndex),u=t.find("img, svg, canvas"),v=u.parent(".photo-browser-zoom-container")),u&&0!==u.length&&(v.transition(300).transform("translate3d(0,0,0)"),w&&1!==w?(w=x=1,u.transition(300).transform("translate3d(0,0,0) scale(1)"),t=void 0):(w=x=d.params.maxZoom,u.transition(300).transform("translate3d(0,0,0) scale("+w+")")))};var z,A,B,C,D,E,F,G,H,I,J,K,L,M,N,O,P,Q={},R={};d.onSlideTouchStart=function(b){u&&0!==u.length&&(z||("android"===a.device.os&&b.preventDefault(),z=!0,Q.x="touchstart"===b.type?b.targetTouches[0].pageX:b.pageX,Q.y="touchstart"===b.type?b.targetTouches[0].pageY:b.pageY))},d.onSlideTouchMove=function(b){if(u&&0!==u.length&&(d.swiper.allowClick=!1,z&&t)){A||(H=u[0].offsetWidth,I=u[0].offsetHeight,J=a.getTranslate(v[0],"x")||0,K=a.getTranslate(v[0],"y")||0,v.transition(0));var c=H*w,e=I*w;if(!(c<d.swiper.width&&e<d.swiper.height)){if(D=Math.min(d.swiper.width/2-c/2,0),F=-D,E=Math.min(d.swiper.height/2-e/2,0),G=-E,R.x="touchmove"===b.type?b.targetTouches[0].pageX:b.pageX,R.y="touchmove"===b.type?b.targetTouches[0].pageY:b.pageY,!A&&!y&&(Math.floor(D)===Math.floor(J)&&R.x<Q.x||Math.floor(F)===Math.floor(J)&&R.x>Q.x))return void(z=!1);b.preventDefault(),b.stopPropagation(),A=!0,B=R.x-Q.x+J,C=R.y-Q.y+K,D>B&&(B=D+1-Math.pow(D-B+1,.8)),B>F&&(B=F-1+Math.pow(B-F+1,.8)),E>C&&(C=E+1-Math.pow(E-C+1,.8)),C>G&&(C=G-1+Math.pow(C-G+1,.8)),L||(L=R.x),O||(O=R.y),M||(M=Date.now()),N=(R.x-L)/(Date.now()-M)/2,P=(R.y-O)/(Date.now()-M)/2,Math.abs(R.x-L)<2&&(N=0),Math.abs(R.y-O)<2&&(P=0),L=R.x,O=R.y,M=Date.now(),v.transform("translate3d("+B+"px, "+C+"px,0)")}}},d.onSlideTouchEnd=function(){if(u&&0!==u.length){if(!z||!A)return z=!1,void(A=!1);z=!1,A=!1;var a=300,b=300,c=N*a,e=B+c,f=P*b,g=C+f;0!==N&&(a=Math.abs((e-B)/N)),0!==P&&(b=Math.abs((g-C)/P));var h=Math.max(a,b);B=e,C=g;var i=H*w,j=I*w;D=Math.min(d.swiper.width/2-i/2,0),F=-D,E=Math.min(d.swiper.height/2-j/2,0),G=-E,B=Math.max(Math.min(B,F),D),C=Math.max(Math.min(C,G),E),v.transition(h).transform("translate3d("+B+"px, "+C+"px,0)")}};var S,T,U,V,W,X=!1,Y=!0,Z=!1;return d.swipeCloseTouchStart=function(){Y&&(X=!0)},d.swipeCloseTouchMove=function(a,b){if(X){Z||(Z=!0,T="touchmove"===b.type?b.targetTouches[0].pageY:b.pageY,V=d.swiper.slides.eq(d.swiper.activeIndex),W=(new Date).getTime()),b.preventDefault(),U="touchmove"===b.type?b.targetTouches[0].pageY:b.pageY,S=T-U;var c=1-Math.abs(S)/300;V.transform("translate3d(0,"+-S+"px,0)"),d.swiper.container.css("opacity",c).transition(0)}},d.swipeCloseTouchEnd=function(){if(X=!1,!Z)return void(Z=!1);Z=!1,Y=!1;var b=Math.abs(S),c=(new Date).getTime()-W;return 300>c&&b>20||c>=300&&b>100?void setTimeout(function(){"standalone"===d.params.type&&d.close(),"popup"===d.params.type&&a.closeModal(d.popup),d.params.onSwipeToClose&&d.params.onSwipeToClose(d),Y=!0},0):(0!==b?V.addClass("transitioning").transitionEnd(function(){Y=!0,V.removeClass("transitioning")}):Y=!0,d.swiper.container.css("opacity","").transition(""),void V.transform(""))},d};b.prototype={defaults:{photos:[],container:"body",initialSlide:0,spaceBetween:20,speed:300,zoom:!0,maxZoom:3,minZoom:1,exposition:!0,expositionHideCaptions:!1,type:"standalone",navbar:!0,toolbar:!0,theme:"light",swipeToClose:!0,backLinkText:"Close",ofText:"of",loop:!1,lazyLoading:!1,lazyLoadingInPrevNext:!1,lazyLoadingOnTransitionStart:!1}},a.photoBrowser=function(c){return a.extend(c,a.photoBrowser.prototype.defaults),new b(c)},a.photoBrowser.prototype={defaults:{}}}(Zepto);
/*!
 * =====================================================
 * SUI Mobile - http://m.sui.taobao.org/
 *
 * =====================================================
 */
// jshint ignore: start
+function($){

    $.smConfig.rawCitiesData = [
        {
            "name":"北京市",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"东城区"
                },
                {
                    "name":"西城区"
                },
                {
                    "name":"崇文区"
                },
                {
                    "name":"宣武区"
                },
                {
                    "name":"朝阳区"
                },
                {
                    "name":"海淀区"
                },
                {
                    "name":"丰台区"
                },
                {
                    "name":"石景山区"
                },
                {
                    "name":"房山区"
                },
                {
                    "name":"通州区"
                },
                {
                    "name":"顺义区"
                },
                {
                    "name":"昌平区"
                },
                {
                    "name":"大兴区"
                },
                {
                    "name":"怀柔区"
                },
                {
                    "name":"平谷区"
                },
                {
                    "name":"门头沟区"
                },
                {
                    "name":"密云县"
                },
                {
                    "name":"延庆县"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"广东省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"广州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"越秀区"
                        },
                        {
                            "name":"荔湾区"
                        },
                        {
                            "name":"海珠区"
                        },
                        {
                            "name":"天河区"
                        },
                        {
                            "name":"白云区"
                        },
                        {
                            "name":"黄埔区"
                        },
                        {
                            "name":"番禺区"
                        },
                        {
                            "name":"花都区"
                        },
                        {
                            "name":"南沙区"
                        },
                        {
                            "name":"萝岗区"
                        },
                        {
                            "name":"增城市"
                        },
                        {
                            "name":"从化市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"深圳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"福田区"
                        },
                        {
                            "name":"罗湖区"
                        },
                        {
                            "name":"南山区"
                        },
                        {
                            "name":"宝安区"
                        },
                        {
                            "name":"龙岗区"
                        },
                        {
                            "name":"盐田区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"珠海市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"香洲区"
                        },
                        {
                            "name":"斗门区"
                        },
                        {
                            "name":"金湾区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"汕头市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"金平区"
                        },
                        {
                            "name":"濠江区"
                        },
                        {
                            "name":"龙湖区"
                        },
                        {
                            "name":"潮阳区"
                        },
                        {
                            "name":"潮南区"
                        },
                        {
                            "name":"澄海区"
                        },
                        {
                            "name":"南澳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"韶关市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"浈江区"
                        },
                        {
                            "name":"武江区"
                        },
                        {
                            "name":"曲江区"
                        },
                        {
                            "name":"乐昌市"
                        },
                        {
                            "name":"南雄市"
                        },
                        {
                            "name":"始兴县"
                        },
                        {
                            "name":"仁化县"
                        },
                        {
                            "name":"翁源县"
                        },
                        {
                            "name":"新丰县"
                        },
                        {
                            "name":"乳源瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"佛山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"禅城区"
                        },
                        {
                            "name":"南海区"
                        },
                        {
                            "name":"顺德区"
                        },
                        {
                            "name":"三水区"
                        },
                        {
                            "name":"高明区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"江门市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"蓬江区"
                        },
                        {
                            "name":"江海区"
                        },
                        {
                            "name":"新会区"
                        },
                        {
                            "name":"恩平市"
                        },
                        {
                            "name":"台山市"
                        },
                        {
                            "name":"开平市"
                        },
                        {
                            "name":"鹤山市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"湛江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"赤坎区"
                        },
                        {
                            "name":"霞山区"
                        },
                        {
                            "name":"坡头区"
                        },
                        {
                            "name":"麻章区"
                        },
                        {
                            "name":"吴川市"
                        },
                        {
                            "name":"廉江市"
                        },
                        {
                            "name":"雷州市"
                        },
                        {
                            "name":"遂溪县"
                        },
                        {
                            "name":"徐闻县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"茂名市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"茂南区"
                        },
                        {
                            "name":"茂港区"
                        },
                        {
                            "name":"化州市"
                        },
                        {
                            "name":"信宜市"
                        },
                        {
                            "name":"高州市"
                        },
                        {
                            "name":"电白县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"肇庆市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"端州区"
                        },
                        {
                            "name":"鼎湖区"
                        },
                        {
                            "name":"高要市"
                        },
                        {
                            "name":"四会市"
                        },
                        {
                            "name":"广宁县"
                        },
                        {
                            "name":"怀集县"
                        },
                        {
                            "name":"封开县"
                        },
                        {
                            "name":"德庆县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"惠州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"惠城区"
                        },
                        {
                            "name":"惠阳区"
                        },
                        {
                            "name":"博罗县"
                        },
                        {
                            "name":"惠东县"
                        },
                        {
                            "name":"龙门县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"梅州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"梅江区"
                        },
                        {
                            "name":"兴宁市"
                        },
                        {
                            "name":"梅县"
                        },
                        {
                            "name":"大埔县"
                        },
                        {
                            "name":"丰顺县"
                        },
                        {
                            "name":"五华县"
                        },
                        {
                            "name":"平远县"
                        },
                        {
                            "name":"蕉岭县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"汕尾市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城区"
                        },
                        {
                            "name":"陆丰市"
                        },
                        {
                            "name":"海丰县"
                        },
                        {
                            "name":"陆河县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"河源市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"源城区"
                        },
                        {
                            "name":"紫金县"
                        },
                        {
                            "name":"龙川县"
                        },
                        {
                            "name":"连平县"
                        },
                        {
                            "name":"和平县"
                        },
                        {
                            "name":"东源县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阳江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"江城区"
                        },
                        {
                            "name":"阳春市"
                        },
                        {
                            "name":"阳西县"
                        },
                        {
                            "name":"阳东县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"清远市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"清城区"
                        },
                        {
                            "name":"英德市"
                        },
                        {
                            "name":"连州市"
                        },
                        {
                            "name":"佛冈县"
                        },
                        {
                            "name":"阳山县"
                        },
                        {
                            "name":"清新县"
                        },
                        {
                            "name":"连山壮族瑶族自治县"
                        },
                        {
                            "name":"连南瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"东莞市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"中山市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"潮州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"湘桥区"
                        },
                        {
                            "name":"潮安县"
                        },
                        {
                            "name":"饶平县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"揭阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"榕城区"
                        },
                        {
                            "name":"普宁市"
                        },
                        {
                            "name":"揭东县"
                        },
                        {
                            "name":"揭西县"
                        },
                        {
                            "name":"惠来县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"云浮市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"云城区"
                        },
                        {
                            "name":"罗定市"
                        },
                        {
                            "name":"云安县"
                        },
                        {
                            "name":"新兴县"
                        },
                        {
                            "name":"郁南县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"上海市",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"黄浦区"
                },
                {
                    "name":"卢湾区"
                },
                {
                    "name":"徐汇区"
                },
                {
                    "name":"长宁区"
                },
                {
                    "name":"静安区"
                },
                {
                    "name":"普陀区"
                },
                {
                    "name":"闸北区"
                },
                {
                    "name":"虹口区"
                },
                {
                    "name":"杨浦区"
                },
                {
                    "name":"宝山区"
                },
                {
                    "name":"闵行区"
                },
                {
                    "name":"嘉定区"
                },
                {
                    "name":"松江区"
                },
                {
                    "name":"金山区"
                },
                {
                    "name":"青浦区"
                },
                {
                    "name":"南汇区"
                },
                {
                    "name":"奉贤区"
                },
                {
                    "name":"浦东新区"
                },
                {
                    "name":"崇明县"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"天津市",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"和平区"
                },
                {
                    "name":"河东区"
                },
                {
                    "name":"河西区"
                },
                {
                    "name":"南开区"
                },
                {
                    "name":"河北区"
                },
                {
                    "name":"红桥区"
                },
                {
                    "name":"塘沽区"
                },
                {
                    "name":"汉沽区"
                },
                {
                    "name":"大港区"
                },
                {
                    "name":"东丽区"
                },
                {
                    "name":"西青区"
                },
                {
                    "name":"北辰区"
                },
                {
                    "name":"津南区"
                },
                {
                    "name":"武清区"
                },
                {
                    "name":"宝坻区"
                },
                {
                    "name":"静海县"
                },
                {
                    "name":"宁河县"
                },
                {
                    "name":"蓟县"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"重庆市",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"渝中区"
                },
                {
                    "name":"大渡口区"
                },
                {
                    "name":"江北区"
                },
                {
                    "name":"南岸区"
                },
                {
                    "name":"北碚区"
                },
                {
                    "name":"渝北区"
                },
                {
                    "name":"巴南区"
                },
                {
                    "name":"长寿区"
                },
                {
                    "name":"双桥区"
                },
                {
                    "name":"沙坪坝区"
                },
                {
                    "name":"万盛区"
                },
                {
                    "name":"万州区"
                },
                {
                    "name":"涪陵区"
                },
                {
                    "name":"黔江区"
                },
                {
                    "name":"永川区"
                },
                {
                    "name":"合川区"
                },
                {
                    "name":"江津区"
                },
                {
                    "name":"九龙坡区"
                },
                {
                    "name":"南川区"
                },
                {
                    "name":"綦江县"
                },
                {
                    "name":"潼南县"
                },
                {
                    "name":"荣昌县"
                },
                {
                    "name":"璧山县"
                },
                {
                    "name":"大足县"
                },
                {
                    "name":"铜梁县"
                },
                {
                    "name":"梁平县"
                },
                {
                    "name":"开县"
                },
                {
                    "name":"忠县"
                },
                {
                    "name":"城口县"
                },
                {
                    "name":"垫江县"
                },
                {
                    "name":"武隆县"
                },
                {
                    "name":"丰都县"
                },
                {
                    "name":"奉节县"
                },
                {
                    "name":"云阳县"
                },
                {
                    "name":"巫溪县"
                },
                {
                    "name":"巫山县"
                },
                {
                    "name":"石柱土家族自治县"
                },
                {
                    "name":"秀山土家族苗族自治县"
                },
                {
                    "name":"酉阳土家族苗族自治县"
                },
                {
                    "name":"彭水苗族土家族自治县"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"辽宁省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"沈阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"沈河区"
                        },
                        {
                            "name":"皇姑区"
                        },
                        {
                            "name":"和平区"
                        },
                        {
                            "name":"大东区"
                        },
                        {
                            "name":"铁西区"
                        },
                        {
                            "name":"苏家屯区"
                        },
                        {
                            "name":"东陵区"
                        },
                        {
                            "name":"于洪区"
                        },
                        {
                            "name":"新民市"
                        },
                        {
                            "name":"法库县"
                        },
                        {
                            "name":"辽中县"
                        },
                        {
                            "name":"康平县"
                        },
                        {
                            "name":"新城子区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"大连市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"西岗区"
                        },
                        {
                            "name":"中山区"
                        },
                        {
                            "name":"沙河口区"
                        },
                        {
                            "name":"甘井子区"
                        },
                        {
                            "name":"旅顺口区"
                        },
                        {
                            "name":"金州区"
                        },
                        {
                            "name":"瓦房店市"
                        },
                        {
                            "name":"普兰店市"
                        },
                        {
                            "name":"庄河市"
                        },
                        {
                            "name":"长海县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鞍山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"铁东区"
                        },
                        {
                            "name":"铁西区"
                        },
                        {
                            "name":"立山区"
                        },
                        {
                            "name":"千山区"
                        },
                        {
                            "name":"海城市"
                        },
                        {
                            "name":"台安县"
                        },
                        {
                            "name":"岫岩满族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"抚顺市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"顺城区"
                        },
                        {
                            "name":"新抚区"
                        },
                        {
                            "name":"东洲区"
                        },
                        {
                            "name":"望花区"
                        },
                        {
                            "name":"抚顺县"
                        },
                        {
                            "name":"清原满族自治县"
                        },
                        {
                            "name":"新宾满族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"本溪市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"平山区"
                        },
                        {
                            "name":"明山区"
                        },
                        {
                            "name":"溪湖区"
                        },
                        {
                            "name":"南芬区"
                        },
                        {
                            "name":"本溪满族自治县"
                        },
                        {
                            "name":"桓仁满族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"丹东市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"振兴区"
                        },
                        {
                            "name":"元宝区"
                        },
                        {
                            "name":"振安区"
                        },
                        {
                            "name":"东港市"
                        },
                        {
                            "name":"凤城市"
                        },
                        {
                            "name":"宽甸满族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"锦州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"太和区"
                        },
                        {
                            "name":"古塔区"
                        },
                        {
                            "name":"凌河区"
                        },
                        {
                            "name":"凌海市"
                        },
                        {
                            "name":"黑山县"
                        },
                        {
                            "name":"义县"
                        },
                        {
                            "name":"北宁市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"营口市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"站前区"
                        },
                        {
                            "name":"西市区"
                        },
                        {
                            "name":"鲅鱼圈区"
                        },
                        {
                            "name":"老边区"
                        },
                        {
                            "name":"大石桥市"
                        },
                        {
                            "name":"盖州市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阜新市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海州区"
                        },
                        {
                            "name":"新邱区"
                        },
                        {
                            "name":"太平区"
                        },
                        {
                            "name":"清河门区"
                        },
                        {
                            "name":"细河区"
                        },
                        {
                            "name":"彰武县"
                        },
                        {
                            "name":"阜新蒙古族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"辽阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"白塔区"
                        },
                        {
                            "name":"文圣区"
                        },
                        {
                            "name":"宏伟区"
                        },
                        {
                            "name":"太子河区"
                        },
                        {
                            "name":"弓长岭区"
                        },
                        {
                            "name":"灯塔市"
                        },
                        {
                            "name":"辽阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"盘锦市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"双台子区"
                        },
                        {
                            "name":"兴隆台区"
                        },
                        {
                            "name":"盘山县"
                        },
                        {
                            "name":"大洼县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"铁岭市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"银州区"
                        },
                        {
                            "name":"清河区"
                        },
                        {
                            "name":"调兵山市"
                        },
                        {
                            "name":"开原市"
                        },
                        {
                            "name":"铁岭县"
                        },
                        {
                            "name":"昌图县"
                        },
                        {
                            "name":"西丰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"朝阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"双塔区"
                        },
                        {
                            "name":"龙城区"
                        },
                        {
                            "name":"凌源市"
                        },
                        {
                            "name":"北票市"
                        },
                        {
                            "name":"朝阳县"
                        },
                        {
                            "name":"建平县"
                        },
                        {
                            "name":"喀喇沁左翼蒙古族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"葫芦岛市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"龙港区"
                        },
                        {
                            "name":"南票区"
                        },
                        {
                            "name":"连山区"
                        },
                        {
                            "name":"兴城市"
                        },
                        {
                            "name":"绥中县"
                        },
                        {
                            "name":"建昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"江苏省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"南京市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"玄武区"
                        },
                        {
                            "name":"白下区"
                        },
                        {
                            "name":"秦淮区"
                        },
                        {
                            "name":"建邺区"
                        },
                        {
                            "name":"鼓楼区"
                        },
                        {
                            "name":"下关区"
                        },
                        {
                            "name":"栖霞区"
                        },
                        {
                            "name":"雨花台区"
                        },
                        {
                            "name":"浦口区"
                        },
                        {
                            "name":"江宁区"
                        },
                        {
                            "name":"六合区"
                        },
                        {
                            "name":"溧水县"
                        },
                        {
                            "name":"高淳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"苏州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"金阊区"
                        },
                        {
                            "name":"平江区"
                        },
                        {
                            "name":"沧浪区"
                        },
                        {
                            "name":"虎丘区"
                        },
                        {
                            "name":"吴中区"
                        },
                        {
                            "name":"相城区"
                        },
                        {
                            "name":"常熟市"
                        },
                        {
                            "name":"张家港市"
                        },
                        {
                            "name":"昆山市"
                        },
                        {
                            "name":"吴江市"
                        },
                        {
                            "name":"太仓市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"无锡市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"崇安区"
                        },
                        {
                            "name":"南长区"
                        },
                        {
                            "name":"北塘区"
                        },
                        {
                            "name":"滨湖区"
                        },
                        {
                            "name":"锡山区"
                        },
                        {
                            "name":"惠山区"
                        },
                        {
                            "name":"江阴市"
                        },
                        {
                            "name":"宜兴市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"常州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"钟楼区"
                        },
                        {
                            "name":"天宁区"
                        },
                        {
                            "name":"戚墅堰区"
                        },
                        {
                            "name":"新北区"
                        },
                        {
                            "name":"武进区"
                        },
                        {
                            "name":"金坛市"
                        },
                        {
                            "name":"溧阳市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"镇江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"京口区"
                        },
                        {
                            "name":"润州区"
                        },
                        {
                            "name":"丹徒区"
                        },
                        {
                            "name":"丹阳市"
                        },
                        {
                            "name":"扬中市"
                        },
                        {
                            "name":"句容市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"南通市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"崇川区"
                        },
                        {
                            "name":"港闸区"
                        },
                        {
                            "name":"通州市"
                        },
                        {
                            "name":"如皋市"
                        },
                        {
                            "name":"海门市"
                        },
                        {
                            "name":"启东市"
                        },
                        {
                            "name":"海安县"
                        },
                        {
                            "name":"如东县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"泰州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海陵区"
                        },
                        {
                            "name":"高港区"
                        },
                        {
                            "name":"姜堰市"
                        },
                        {
                            "name":"泰兴市"
                        },
                        {
                            "name":"靖江市"
                        },
                        {
                            "name":"兴化市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"扬州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"广陵区"
                        },
                        {
                            "name":"维扬区"
                        },
                        {
                            "name":"邗江区"
                        },
                        {
                            "name":"江都市"
                        },
                        {
                            "name":"仪征市"
                        },
                        {
                            "name":"高邮市"
                        },
                        {
                            "name":"宝应县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"盐城市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"亭湖区"
                        },
                        {
                            "name":"盐都区"
                        },
                        {
                            "name":"大丰市"
                        },
                        {
                            "name":"东台市"
                        },
                        {
                            "name":"建湖县"
                        },
                        {
                            "name":"射阳县"
                        },
                        {
                            "name":"阜宁县"
                        },
                        {
                            "name":"滨海县"
                        },
                        {
                            "name":"响水县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"连云港市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"新浦区"
                        },
                        {
                            "name":"海州区"
                        },
                        {
                            "name":"连云区"
                        },
                        {
                            "name":"东海县"
                        },
                        {
                            "name":"灌云县"
                        },
                        {
                            "name":"赣榆县"
                        },
                        {
                            "name":"灌南县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"徐州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"云龙区"
                        },
                        {
                            "name":"鼓楼区"
                        },
                        {
                            "name":"九里区"
                        },
                        {
                            "name":"泉山区"
                        },
                        {
                            "name":"贾汪区"
                        },
                        {
                            "name":"邳州市"
                        },
                        {
                            "name":"新沂市"
                        },
                        {
                            "name":"铜山县"
                        },
                        {
                            "name":"睢宁县"
                        },
                        {
                            "name":"沛县"
                        },
                        {
                            "name":"丰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"淮安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"清河区"
                        },
                        {
                            "name":"清浦区"
                        },
                        {
                            "name":"楚州区"
                        },
                        {
                            "name":"淮阴区"
                        },
                        {
                            "name":"涟水县"
                        },
                        {
                            "name":"洪泽县"
                        },
                        {
                            "name":"金湖县"
                        },
                        {
                            "name":"盱眙县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宿迁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"宿城区"
                        },
                        {
                            "name":"宿豫区"
                        },
                        {
                            "name":"沭阳县"
                        },
                        {
                            "name":"泗阳县"
                        },
                        {
                            "name":"泗洪县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"湖北省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"武汉市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"江岸区"
                        },
                        {
                            "name":"武昌区"
                        },
                        {
                            "name":"江汉区"
                        },
                        {
                            "name":"硚口区"
                        },
                        {
                            "name":"汉阳区"
                        },
                        {
                            "name":"青山区"
                        },
                        {
                            "name":"洪山区"
                        },
                        {
                            "name":"东西湖区"
                        },
                        {
                            "name":"汉南区"
                        },
                        {
                            "name":"蔡甸区"
                        },
                        {
                            "name":"江夏区"
                        },
                        {
                            "name":"黄陂区"
                        },
                        {
                            "name":"新洲区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黄石市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"黄石港区"
                        },
                        {
                            "name":"西塞山区"
                        },
                        {
                            "name":"下陆区"
                        },
                        {
                            "name":"铁山区"
                        },
                        {
                            "name":"大冶市"
                        },
                        {
                            "name":"阳新县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"十堰市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"张湾区"
                        },
                        {
                            "name":"茅箭区"
                        },
                        {
                            "name":"丹江口市"
                        },
                        {
                            "name":"郧县"
                        },
                        {
                            "name":"竹山县"
                        },
                        {
                            "name":"房县"
                        },
                        {
                            "name":"郧西县"
                        },
                        {
                            "name":"竹溪县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"荆州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"沙市区"
                        },
                        {
                            "name":"荆州区"
                        },
                        {
                            "name":"洪湖市"
                        },
                        {
                            "name":"石首市"
                        },
                        {
                            "name":"松滋市"
                        },
                        {
                            "name":"监利县"
                        },
                        {
                            "name":"公安县"
                        },
                        {
                            "name":"江陵县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宜昌市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"西陵区"
                        },
                        {
                            "name":"伍家岗区"
                        },
                        {
                            "name":"点军区"
                        },
                        {
                            "name":"猇亭区"
                        },
                        {
                            "name":"夷陵区"
                        },
                        {
                            "name":"宜都市"
                        },
                        {
                            "name":"当阳市"
                        },
                        {
                            "name":"枝江市"
                        },
                        {
                            "name":"秭归县"
                        },
                        {
                            "name":"远安县"
                        },
                        {
                            "name":"兴山县"
                        },
                        {
                            "name":"五峰土家族自治县"
                        },
                        {
                            "name":"长阳土家族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"襄樊市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"襄城区"
                        },
                        {
                            "name":"樊城区"
                        },
                        {
                            "name":"襄阳区"
                        },
                        {
                            "name":"老河口市"
                        },
                        {
                            "name":"枣阳市"
                        },
                        {
                            "name":"宜城市"
                        },
                        {
                            "name":"南漳县"
                        },
                        {
                            "name":"谷城县"
                        },
                        {
                            "name":"保康县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鄂州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鄂城区"
                        },
                        {
                            "name":"华容区"
                        },
                        {
                            "name":"梁子湖区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"荆门市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东宝区"
                        },
                        {
                            "name":"掇刀区"
                        },
                        {
                            "name":"钟祥市"
                        },
                        {
                            "name":"京山县"
                        },
                        {
                            "name":"沙洋县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"孝感市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"孝南区"
                        },
                        {
                            "name":"应城市"
                        },
                        {
                            "name":"安陆市"
                        },
                        {
                            "name":"汉川市"
                        },
                        {
                            "name":"云梦县"
                        },
                        {
                            "name":"大悟县"
                        },
                        {
                            "name":"孝昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黄冈市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"黄州区"
                        },
                        {
                            "name":"麻城市"
                        },
                        {
                            "name":"武穴市"
                        },
                        {
                            "name":"红安县"
                        },
                        {
                            "name":"罗田县"
                        },
                        {
                            "name":"浠水县"
                        },
                        {
                            "name":"蕲春县"
                        },
                        {
                            "name":"黄梅县"
                        },
                        {
                            "name":"英山县"
                        },
                        {
                            "name":"团风县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"咸宁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"咸安区"
                        },
                        {
                            "name":"赤壁市"
                        },
                        {
                            "name":"嘉鱼县"
                        },
                        {
                            "name":"通山县"
                        },
                        {
                            "name":"崇阳县"
                        },
                        {
                            "name":"通城县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"随州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"曾都区"
                        },
                        {
                            "name":"广水市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"恩施土家族苗族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"恩施市"
                        },
                        {
                            "name":"利川市"
                        },
                        {
                            "name":"建始县"
                        },
                        {
                            "name":"来凤县"
                        },
                        {
                            "name":"巴东县"
                        },
                        {
                            "name":"鹤峰县"
                        },
                        {
                            "name":"宣恩县"
                        },
                        {
                            "name":"咸丰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"仙桃市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"天门市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"潜江市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"神农架林区",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"四川省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"成都市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"青羊区"
                        },
                        {
                            "name":"锦江区"
                        },
                        {
                            "name":"金牛区"
                        },
                        {
                            "name":"武侯区"
                        },
                        {
                            "name":"成华区"
                        },
                        {
                            "name":"龙泉驿区"
                        },
                        {
                            "name":"青白江区"
                        },
                        {
                            "name":"新都区"
                        },
                        {
                            "name":"温江区"
                        },
                        {
                            "name":"都江堰市"
                        },
                        {
                            "name":"彭州市"
                        },
                        {
                            "name":"邛崃市"
                        },
                        {
                            "name":"崇州市"
                        },
                        {
                            "name":"金堂县"
                        },
                        {
                            "name":"郫县"
                        },
                        {
                            "name":"新津县"
                        },
                        {
                            "name":"双流县"
                        },
                        {
                            "name":"蒲江县"
                        },
                        {
                            "name":"大邑县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"自贡市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"大安区"
                        },
                        {
                            "name":"自流井区"
                        },
                        {
                            "name":"贡井区"
                        },
                        {
                            "name":"沿滩区"
                        },
                        {
                            "name":"荣县"
                        },
                        {
                            "name":"富顺县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"攀枝花市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"仁和区"
                        },
                        {
                            "name":"米易县"
                        },
                        {
                            "name":"盐边县"
                        },
                        {
                            "name":"东区"
                        },
                        {
                            "name":"西区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"泸州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"江阳区"
                        },
                        {
                            "name":"纳溪区"
                        },
                        {
                            "name":"龙马潭区"
                        },
                        {
                            "name":"泸县"
                        },
                        {
                            "name":"合江县"
                        },
                        {
                            "name":"叙永县"
                        },
                        {
                            "name":"古蔺县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"德阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"旌阳区"
                        },
                        {
                            "name":"广汉市"
                        },
                        {
                            "name":"什邡市"
                        },
                        {
                            "name":"绵竹市"
                        },
                        {
                            "name":"罗江县"
                        },
                        {
                            "name":"中江县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"绵阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"涪城区"
                        },
                        {
                            "name":"游仙区"
                        },
                        {
                            "name":"江油市"
                        },
                        {
                            "name":"盐亭县"
                        },
                        {
                            "name":"三台县"
                        },
                        {
                            "name":"平武县"
                        },
                        {
                            "name":"安县"
                        },
                        {
                            "name":"梓潼县"
                        },
                        {
                            "name":"北川羌族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"广元市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"元坝区"
                        },
                        {
                            "name":"朝天区"
                        },
                        {
                            "name":"青川县"
                        },
                        {
                            "name":"旺苍县"
                        },
                        {
                            "name":"剑阁县"
                        },
                        {
                            "name":"苍溪县"
                        },
                        {
                            "name":"市中区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"遂宁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"船山区"
                        },
                        {
                            "name":"安居区"
                        },
                        {
                            "name":"射洪县"
                        },
                        {
                            "name":"蓬溪县"
                        },
                        {
                            "name":"大英县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"内江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"市中区"
                        },
                        {
                            "name":"东兴区"
                        },
                        {
                            "name":"资中县"
                        },
                        {
                            "name":"隆昌县"
                        },
                        {
                            "name":"威远县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"乐山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"市中区"
                        },
                        {
                            "name":"五通桥区"
                        },
                        {
                            "name":"沙湾区"
                        },
                        {
                            "name":"金口河区"
                        },
                        {
                            "name":"峨眉山市"
                        },
                        {
                            "name":"夹江县"
                        },
                        {
                            "name":"井研县"
                        },
                        {
                            "name":"犍为县"
                        },
                        {
                            "name":"沐川县"
                        },
                        {
                            "name":"马边彝族自治县"
                        },
                        {
                            "name":"峨边彝族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"南充市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"顺庆区"
                        },
                        {
                            "name":"高坪区"
                        },
                        {
                            "name":"嘉陵区"
                        },
                        {
                            "name":"阆中市"
                        },
                        {
                            "name":"营山县"
                        },
                        {
                            "name":"蓬安县"
                        },
                        {
                            "name":"仪陇县"
                        },
                        {
                            "name":"南部县"
                        },
                        {
                            "name":"西充县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"眉山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东坡区"
                        },
                        {
                            "name":"仁寿县"
                        },
                        {
                            "name":"彭山县"
                        },
                        {
                            "name":"洪雅县"
                        },
                        {
                            "name":"丹棱县"
                        },
                        {
                            "name":"青神县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宜宾市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"翠屏区"
                        },
                        {
                            "name":"宜宾县"
                        },
                        {
                            "name":"兴文县"
                        },
                        {
                            "name":"南溪县"
                        },
                        {
                            "name":"珙县"
                        },
                        {
                            "name":"长宁县"
                        },
                        {
                            "name":"高县"
                        },
                        {
                            "name":"江安县"
                        },
                        {
                            "name":"筠连县"
                        },
                        {
                            "name":"屏山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"广安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"广安区"
                        },
                        {
                            "name":"华蓥市"
                        },
                        {
                            "name":"岳池县"
                        },
                        {
                            "name":"邻水县"
                        },
                        {
                            "name":"武胜县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"达州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"通川区"
                        },
                        {
                            "name":"万源市"
                        },
                        {
                            "name":"达县"
                        },
                        {
                            "name":"渠县"
                        },
                        {
                            "name":"宣汉县"
                        },
                        {
                            "name":"开江县"
                        },
                        {
                            "name":"大竹县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"雅安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"雨城区"
                        },
                        {
                            "name":"芦山县"
                        },
                        {
                            "name":"石棉县"
                        },
                        {
                            "name":"名山县"
                        },
                        {
                            "name":"天全县"
                        },
                        {
                            "name":"荥经县"
                        },
                        {
                            "name":"宝兴县"
                        },
                        {
                            "name":"汉源县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"巴中市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"巴州区"
                        },
                        {
                            "name":"南江县"
                        },
                        {
                            "name":"平昌县"
                        },
                        {
                            "name":"通江县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"资阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"雁江区"
                        },
                        {
                            "name":"简阳市"
                        },
                        {
                            "name":"安岳县"
                        },
                        {
                            "name":"乐至县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阿坝藏族羌族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"马尔康县"
                        },
                        {
                            "name":"九寨沟县"
                        },
                        {
                            "name":"红原县"
                        },
                        {
                            "name":"汶川县"
                        },
                        {
                            "name":"阿坝县"
                        },
                        {
                            "name":"理县"
                        },
                        {
                            "name":"若尔盖县"
                        },
                        {
                            "name":"小金县"
                        },
                        {
                            "name":"黑水县"
                        },
                        {
                            "name":"金川县"
                        },
                        {
                            "name":"松潘县"
                        },
                        {
                            "name":"壤塘县"
                        },
                        {
                            "name":"茂县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"甘孜藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"康定县"
                        },
                        {
                            "name":"丹巴县"
                        },
                        {
                            "name":"炉霍县"
                        },
                        {
                            "name":"九龙县"
                        },
                        {
                            "name":"甘孜县"
                        },
                        {
                            "name":"雅江县"
                        },
                        {
                            "name":"新龙县"
                        },
                        {
                            "name":"道孚县"
                        },
                        {
                            "name":"白玉县"
                        },
                        {
                            "name":"理塘县"
                        },
                        {
                            "name":"德格县"
                        },
                        {
                            "name":"乡城县"
                        },
                        {
                            "name":"石渠县"
                        },
                        {
                            "name":"稻城县"
                        },
                        {
                            "name":"色达县"
                        },
                        {
                            "name":"巴塘县"
                        },
                        {
                            "name":"泸定县"
                        },
                        {
                            "name":"得荣县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"凉山彝族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"西昌市"
                        },
                        {
                            "name":"美姑县"
                        },
                        {
                            "name":"昭觉县"
                        },
                        {
                            "name":"金阳县"
                        },
                        {
                            "name":"甘洛县"
                        },
                        {
                            "name":"布拖县"
                        },
                        {
                            "name":"雷波县"
                        },
                        {
                            "name":"普格县"
                        },
                        {
                            "name":"宁南县"
                        },
                        {
                            "name":"喜德县"
                        },
                        {
                            "name":"会东县"
                        },
                        {
                            "name":"越西县"
                        },
                        {
                            "name":"会理县"
                        },
                        {
                            "name":"盐源县"
                        },
                        {
                            "name":"德昌县"
                        },
                        {
                            "name":"冕宁县"
                        },
                        {
                            "name":"木里藏族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"陕西省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"西安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"莲湖区"
                        },
                        {
                            "name":"新城区"
                        },
                        {
                            "name":"碑林区"
                        },
                        {
                            "name":"雁塔区"
                        },
                        {
                            "name":"灞桥区"
                        },
                        {
                            "name":"未央区"
                        },
                        {
                            "name":"阎良区"
                        },
                        {
                            "name":"临潼区"
                        },
                        {
                            "name":"长安区"
                        },
                        {
                            "name":"高陵县"
                        },
                        {
                            "name":"蓝田县"
                        },
                        {
                            "name":"户县"
                        },
                        {
                            "name":"周至县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"铜川市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"耀州区"
                        },
                        {
                            "name":"王益区"
                        },
                        {
                            "name":"印台区"
                        },
                        {
                            "name":"宜君县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宝鸡市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"渭滨区"
                        },
                        {
                            "name":"金台区"
                        },
                        {
                            "name":"陈仓区"
                        },
                        {
                            "name":"岐山县"
                        },
                        {
                            "name":"凤翔县"
                        },
                        {
                            "name":"陇县"
                        },
                        {
                            "name":"太白县"
                        },
                        {
                            "name":"麟游县"
                        },
                        {
                            "name":"扶风县"
                        },
                        {
                            "name":"千阳县"
                        },
                        {
                            "name":"眉县"
                        },
                        {
                            "name":"凤县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"咸阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"秦都区"
                        },
                        {
                            "name":"渭城区"
                        },
                        {
                            "name":"杨陵区"
                        },
                        {
                            "name":"兴平市"
                        },
                        {
                            "name":"礼泉县"
                        },
                        {
                            "name":"泾阳县"
                        },
                        {
                            "name":"永寿县"
                        },
                        {
                            "name":"三原县"
                        },
                        {
                            "name":"彬县"
                        },
                        {
                            "name":"旬邑县"
                        },
                        {
                            "name":"长武县"
                        },
                        {
                            "name":"乾县"
                        },
                        {
                            "name":"武功县"
                        },
                        {
                            "name":"淳化县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"渭南市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"临渭区"
                        },
                        {
                            "name":"韩城市"
                        },
                        {
                            "name":"华阴市"
                        },
                        {
                            "name":"蒲城县"
                        },
                        {
                            "name":"潼关县"
                        },
                        {
                            "name":"白水县"
                        },
                        {
                            "name":"澄城县"
                        },
                        {
                            "name":"华县"
                        },
                        {
                            "name":"合阳县"
                        },
                        {
                            "name":"富平县"
                        },
                        {
                            "name":"大荔县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"延安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"宝塔区"
                        },
                        {
                            "name":"安塞县"
                        },
                        {
                            "name":"洛川县"
                        },
                        {
                            "name":"子长县"
                        },
                        {
                            "name":"黄陵县"
                        },
                        {
                            "name":"延川县"
                        },
                        {
                            "name":"富县"
                        },
                        {
                            "name":"延长县"
                        },
                        {
                            "name":"甘泉县"
                        },
                        {
                            "name":"宜川县"
                        },
                        {
                            "name":"志丹县"
                        },
                        {
                            "name":"黄龙县"
                        },
                        {
                            "name":"吴起县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"汉中市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"汉台区"
                        },
                        {
                            "name":"留坝县"
                        },
                        {
                            "name":"镇巴县"
                        },
                        {
                            "name":"城固县"
                        },
                        {
                            "name":"南郑县"
                        },
                        {
                            "name":"洋县"
                        },
                        {
                            "name":"宁强县"
                        },
                        {
                            "name":"佛坪县"
                        },
                        {
                            "name":"勉县"
                        },
                        {
                            "name":"西乡县"
                        },
                        {
                            "name":"略阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"榆林市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"榆阳区"
                        },
                        {
                            "name":"清涧县"
                        },
                        {
                            "name":"绥德县"
                        },
                        {
                            "name":"神木县"
                        },
                        {
                            "name":"佳县"
                        },
                        {
                            "name":"府谷县"
                        },
                        {
                            "name":"子洲县"
                        },
                        {
                            "name":"靖边县"
                        },
                        {
                            "name":"横山县"
                        },
                        {
                            "name":"米脂县"
                        },
                        {
                            "name":"吴堡县"
                        },
                        {
                            "name":"定边县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"安康市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"汉滨区"
                        },
                        {
                            "name":"紫阳县"
                        },
                        {
                            "name":"岚皋县"
                        },
                        {
                            "name":"旬阳县"
                        },
                        {
                            "name":"镇坪县"
                        },
                        {
                            "name":"平利县"
                        },
                        {
                            "name":"石泉县"
                        },
                        {
                            "name":"宁陕县"
                        },
                        {
                            "name":"白河县"
                        },
                        {
                            "name":"汉阴县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"商洛市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"商州区"
                        },
                        {
                            "name":"镇安县"
                        },
                        {
                            "name":"山阳县"
                        },
                        {
                            "name":"洛南县"
                        },
                        {
                            "name":"商南县"
                        },
                        {
                            "name":"丹凤县"
                        },
                        {
                            "name":"柞水县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"河北省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"石家庄市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"长安区"
                        },
                        {
                            "name":"桥东区"
                        },
                        {
                            "name":"桥西区"
                        },
                        {
                            "name":"新华区"
                        },
                        {
                            "name":"裕华区"
                        },
                        {
                            "name":"井陉矿区"
                        },
                        {
                            "name":"鹿泉市"
                        },
                        {
                            "name":"辛集市"
                        },
                        {
                            "name":"藁城市"
                        },
                        {
                            "name":"晋州市"
                        },
                        {
                            "name":"新乐市"
                        },
                        {
                            "name":"深泽县"
                        },
                        {
                            "name":"无极县"
                        },
                        {
                            "name":"赵县"
                        },
                        {
                            "name":"灵寿县"
                        },
                        {
                            "name":"高邑县"
                        },
                        {
                            "name":"元氏县"
                        },
                        {
                            "name":"赞皇县"
                        },
                        {
                            "name":"平山县"
                        },
                        {
                            "name":"井陉县"
                        },
                        {
                            "name":"栾城县"
                        },
                        {
                            "name":"正定县"
                        },
                        {
                            "name":"行唐县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"唐山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"路北区"
                        },
                        {
                            "name":"路南区"
                        },
                        {
                            "name":"古冶区"
                        },
                        {
                            "name":"开平区"
                        },
                        {
                            "name":"丰南区"
                        },
                        {
                            "name":"丰润区"
                        },
                        {
                            "name":"遵化市"
                        },
                        {
                            "name":"迁安市"
                        },
                        {
                            "name":"迁西县"
                        },
                        {
                            "name":"滦南县"
                        },
                        {
                            "name":"玉田县"
                        },
                        {
                            "name":"唐海县"
                        },
                        {
                            "name":"乐亭县"
                        },
                        {
                            "name":"滦县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"秦皇岛市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海港区"
                        },
                        {
                            "name":"山海关区"
                        },
                        {
                            "name":"北戴河区"
                        },
                        {
                            "name":"昌黎县"
                        },
                        {
                            "name":"抚宁县"
                        },
                        {
                            "name":"卢龙县"
                        },
                        {
                            "name":"青龙满族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"邯郸市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"邯山区"
                        },
                        {
                            "name":"丛台区"
                        },
                        {
                            "name":"复兴区"
                        },
                        {
                            "name":"峰峰矿区"
                        },
                        {
                            "name":"武安市"
                        },
                        {
                            "name":"邱县"
                        },
                        {
                            "name":"大名县"
                        },
                        {
                            "name":"魏县"
                        },
                        {
                            "name":"曲周县"
                        },
                        {
                            "name":"鸡泽县"
                        },
                        {
                            "name":"肥乡县"
                        },
                        {
                            "name":"广平县"
                        },
                        {
                            "name":"成安县"
                        },
                        {
                            "name":"临漳县"
                        },
                        {
                            "name":"磁县"
                        },
                        {
                            "name":"涉县"
                        },
                        {
                            "name":"永年县"
                        },
                        {
                            "name":"馆陶县"
                        },
                        {
                            "name":"邯郸县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"邢台市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"桥东区"
                        },
                        {
                            "name":"桥西区"
                        },
                        {
                            "name":"南宫市"
                        },
                        {
                            "name":"沙河市"
                        },
                        {
                            "name":"临城县"
                        },
                        {
                            "name":"内丘县"
                        },
                        {
                            "name":"柏乡县"
                        },
                        {
                            "name":"隆尧县"
                        },
                        {
                            "name":"任县"
                        },
                        {
                            "name":"南和县"
                        },
                        {
                            "name":"宁晋县"
                        },
                        {
                            "name":"巨鹿县"
                        },
                        {
                            "name":"新河县"
                        },
                        {
                            "name":"广宗县"
                        },
                        {
                            "name":"平乡县"
                        },
                        {
                            "name":"威县"
                        },
                        {
                            "name":"清河县"
                        },
                        {
                            "name":"临西县"
                        },
                        {
                            "name":"邢台县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"保定市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"新市区"
                        },
                        {
                            "name":"北市区"
                        },
                        {
                            "name":"南市区"
                        },
                        {
                            "name":"定州市"
                        },
                        {
                            "name":"涿州市"
                        },
                        {
                            "name":"安国市"
                        },
                        {
                            "name":"高碑店市"
                        },
                        {
                            "name":"易县"
                        },
                        {
                            "name":"徐水县"
                        },
                        {
                            "name":"涞源县"
                        },
                        {
                            "name":"顺平县"
                        },
                        {
                            "name":"唐县"
                        },
                        {
                            "name":"望都县"
                        },
                        {
                            "name":"涞水县"
                        },
                        {
                            "name":"高阳县"
                        },
                        {
                            "name":"安新县"
                        },
                        {
                            "name":"雄县"
                        },
                        {
                            "name":"容城县"
                        },
                        {
                            "name":"蠡县"
                        },
                        {
                            "name":"曲阳县"
                        },
                        {
                            "name":"阜平县"
                        },
                        {
                            "name":"博野县"
                        },
                        {
                            "name":"满城县"
                        },
                        {
                            "name":"清苑县"
                        },
                        {
                            "name":"定兴县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"张家口市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"桥东区"
                        },
                        {
                            "name":"桥西区"
                        },
                        {
                            "name":"宣化区"
                        },
                        {
                            "name":"下花园区"
                        },
                        {
                            "name":"张北县"
                        },
                        {
                            "name":"康保县"
                        },
                        {
                            "name":"沽源县"
                        },
                        {
                            "name":"尚义县"
                        },
                        {
                            "name":"蔚县"
                        },
                        {
                            "name":"阳原县"
                        },
                        {
                            "name":"怀安县"
                        },
                        {
                            "name":"万全县"
                        },
                        {
                            "name":"怀来县"
                        },
                        {
                            "name":"赤城县"
                        },
                        {
                            "name":"崇礼县"
                        },
                        {
                            "name":"宣化县"
                        },
                        {
                            "name":"涿鹿县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"承德市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"双桥区"
                        },
                        {
                            "name":"双滦区"
                        },
                        {
                            "name":"鹰手营子矿区"
                        },
                        {
                            "name":"兴隆县"
                        },
                        {
                            "name":"平泉县"
                        },
                        {
                            "name":"滦平县"
                        },
                        {
                            "name":"隆化县"
                        },
                        {
                            "name":"承德县"
                        },
                        {
                            "name":"丰宁满族自治县"
                        },
                        {
                            "name":"宽城满族自治县"
                        },
                        {
                            "name":"围场满族蒙古族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"沧州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"新华区"
                        },
                        {
                            "name":"运河区"
                        },
                        {
                            "name":"泊头市"
                        },
                        {
                            "name":"任丘市"
                        },
                        {
                            "name":"黄骅市"
                        },
                        {
                            "name":"河间市"
                        },
                        {
                            "name":"献县"
                        },
                        {
                            "name":"吴桥县"
                        },
                        {
                            "name":"沧县"
                        },
                        {
                            "name":"东光县"
                        },
                        {
                            "name":"肃宁县"
                        },
                        {
                            "name":"南皮县"
                        },
                        {
                            "name":"盐山县"
                        },
                        {
                            "name":"青县"
                        },
                        {
                            "name":"海兴县"
                        },
                        {
                            "name":"孟村回族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"廊坊市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"安次区"
                        },
                        {
                            "name":"广阳区"
                        },
                        {
                            "name":"霸州市"
                        },
                        {
                            "name":"三河市"
                        },
                        {
                            "name":"香河县"
                        },
                        {
                            "name":"永清县"
                        },
                        {
                            "name":"固安县"
                        },
                        {
                            "name":"文安县"
                        },
                        {
                            "name":"大城县"
                        },
                        {
                            "name":"大厂回族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"衡水市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"桃城区"
                        },
                        {
                            "name":"冀州市"
                        },
                        {
                            "name":"深州市"
                        },
                        {
                            "name":"枣强县"
                        },
                        {
                            "name":"武邑县"
                        },
                        {
                            "name":"武强县"
                        },
                        {
                            "name":"饶阳县"
                        },
                        {
                            "name":"安平县"
                        },
                        {
                            "name":"故城县"
                        },
                        {
                            "name":"景县"
                        },
                        {
                            "name":"阜城县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"山西省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"太原市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"杏花岭区"
                        },
                        {
                            "name":"小店区"
                        },
                        {
                            "name":"迎泽区"
                        },
                        {
                            "name":"尖草坪区"
                        },
                        {
                            "name":"万柏林区"
                        },
                        {
                            "name":"晋源区"
                        },
                        {
                            "name":"古交市"
                        },
                        {
                            "name":"阳曲县"
                        },
                        {
                            "name":"清徐县"
                        },
                        {
                            "name":"娄烦县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"大同市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城区"
                        },
                        {
                            "name":"矿区"
                        },
                        {
                            "name":"南郊区"
                        },
                        {
                            "name":"新荣区"
                        },
                        {
                            "name":"大同县"
                        },
                        {
                            "name":"天镇县"
                        },
                        {
                            "name":"灵丘县"
                        },
                        {
                            "name":"阳高县"
                        },
                        {
                            "name":"左云县"
                        },
                        {
                            "name":"广灵县"
                        },
                        {
                            "name":"浑源县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阳泉市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城区"
                        },
                        {
                            "name":"矿区"
                        },
                        {
                            "name":"郊区"
                        },
                        {
                            "name":"平定县"
                        },
                        {
                            "name":"盂县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"长治市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城区"
                        },
                        {
                            "name":"郊区"
                        },
                        {
                            "name":"潞城市"
                        },
                        {
                            "name":"长治县"
                        },
                        {
                            "name":"长子县"
                        },
                        {
                            "name":"平顺县"
                        },
                        {
                            "name":"襄垣县"
                        },
                        {
                            "name":"沁源县"
                        },
                        {
                            "name":"屯留县"
                        },
                        {
                            "name":"黎城县"
                        },
                        {
                            "name":"武乡县"
                        },
                        {
                            "name":"沁县"
                        },
                        {
                            "name":"壶关县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"晋城市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城区"
                        },
                        {
                            "name":"高平市"
                        },
                        {
                            "name":"泽州县"
                        },
                        {
                            "name":"陵川县"
                        },
                        {
                            "name":"阳城县"
                        },
                        {
                            "name":"沁水县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"朔州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"朔城区"
                        },
                        {
                            "name":"平鲁区"
                        },
                        {
                            "name":"山阴县"
                        },
                        {
                            "name":"右玉县"
                        },
                        {
                            "name":"应县"
                        },
                        {
                            "name":"怀仁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"晋中市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"榆次区"
                        },
                        {
                            "name":"介休市"
                        },
                        {
                            "name":"昔阳县"
                        },
                        {
                            "name":"灵石县"
                        },
                        {
                            "name":"祁县"
                        },
                        {
                            "name":"左权县"
                        },
                        {
                            "name":"寿阳县"
                        },
                        {
                            "name":"太谷县"
                        },
                        {
                            "name":"和顺县"
                        },
                        {
                            "name":"平遥县"
                        },
                        {
                            "name":"榆社县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"运城市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"盐湖区"
                        },
                        {
                            "name":"河津市"
                        },
                        {
                            "name":"永济市"
                        },
                        {
                            "name":"闻喜县"
                        },
                        {
                            "name":"新绛县"
                        },
                        {
                            "name":"平陆县"
                        },
                        {
                            "name":"垣曲县"
                        },
                        {
                            "name":"绛县"
                        },
                        {
                            "name":"稷山县"
                        },
                        {
                            "name":"芮城县"
                        },
                        {
                            "name":"夏县"
                        },
                        {
                            "name":"万荣县"
                        },
                        {
                            "name":"临猗县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"忻州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"忻府区"
                        },
                        {
                            "name":"原平市"
                        },
                        {
                            "name":"代县"
                        },
                        {
                            "name":"神池县"
                        },
                        {
                            "name":"五寨县"
                        },
                        {
                            "name":"五台县"
                        },
                        {
                            "name":"偏关县"
                        },
                        {
                            "name":"宁武县"
                        },
                        {
                            "name":"静乐县"
                        },
                        {
                            "name":"繁峙县"
                        },
                        {
                            "name":"河曲县"
                        },
                        {
                            "name":"保德县"
                        },
                        {
                            "name":"定襄县"
                        },
                        {
                            "name":"岢岚县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"临汾市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"尧都区"
                        },
                        {
                            "name":"侯马市"
                        },
                        {
                            "name":"霍州市"
                        },
                        {
                            "name":"汾西县"
                        },
                        {
                            "name":"吉县"
                        },
                        {
                            "name":"安泽县"
                        },
                        {
                            "name":"大宁县"
                        },
                        {
                            "name":"浮山县"
                        },
                        {
                            "name":"古县"
                        },
                        {
                            "name":"隰县"
                        },
                        {
                            "name":"襄汾县"
                        },
                        {
                            "name":"翼城县"
                        },
                        {
                            "name":"永和县"
                        },
                        {
                            "name":"乡宁县"
                        },
                        {
                            "name":"曲沃县"
                        },
                        {
                            "name":"洪洞县"
                        },
                        {
                            "name":"蒲县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"吕梁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"离石区"
                        },
                        {
                            "name":"孝义市"
                        },
                        {
                            "name":"汾阳市"
                        },
                        {
                            "name":"文水县"
                        },
                        {
                            "name":"中阳县"
                        },
                        {
                            "name":"兴县"
                        },
                        {
                            "name":"临县"
                        },
                        {
                            "name":"方山县"
                        },
                        {
                            "name":"柳林县"
                        },
                        {
                            "name":"岚县"
                        },
                        {
                            "name":"交口县"
                        },
                        {
                            "name":"交城县"
                        },
                        {
                            "name":"石楼县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"河南省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"郑州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"中原区"
                        },
                        {
                            "name":"金水区"
                        },
                        {
                            "name":"二七区"
                        },
                        {
                            "name":"管城回族区"
                        },
                        {
                            "name":"上街区"
                        },
                        {
                            "name":"惠济区"
                        },
                        {
                            "name":"巩义市"
                        },
                        {
                            "name":"新郑市"
                        },
                        {
                            "name":"新密市"
                        },
                        {
                            "name":"登封市"
                        },
                        {
                            "name":"荥阳市"
                        },
                        {
                            "name":"中牟县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"开封市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鼓楼区"
                        },
                        {
                            "name":"龙亭区"
                        },
                        {
                            "name":"顺河回族区"
                        },
                        {
                            "name":"禹王台区"
                        },
                        {
                            "name":"金明区"
                        },
                        {
                            "name":"开封县"
                        },
                        {
                            "name":"尉氏县"
                        },
                        {
                            "name":"兰考县"
                        },
                        {
                            "name":"杞县"
                        },
                        {
                            "name":"通许县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"洛阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"西工区"
                        },
                        {
                            "name":"老城区"
                        },
                        {
                            "name":"涧西区"
                        },
                        {
                            "name":"瀍河回族区"
                        },
                        {
                            "name":"洛龙区"
                        },
                        {
                            "name":"吉利区"
                        },
                        {
                            "name":"偃师市"
                        },
                        {
                            "name":"孟津县"
                        },
                        {
                            "name":"汝阳县"
                        },
                        {
                            "name":"伊川县"
                        },
                        {
                            "name":"洛宁县"
                        },
                        {
                            "name":"嵩县"
                        },
                        {
                            "name":"宜阳县"
                        },
                        {
                            "name":"新安县"
                        },
                        {
                            "name":"栾川县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"平顶山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"新华区"
                        },
                        {
                            "name":"卫东区"
                        },
                        {
                            "name":"湛河区"
                        },
                        {
                            "name":"石龙区"
                        },
                        {
                            "name":"汝州市"
                        },
                        {
                            "name":"舞钢市"
                        },
                        {
                            "name":"宝丰县"
                        },
                        {
                            "name":"叶县"
                        },
                        {
                            "name":"郏县"
                        },
                        {
                            "name":"鲁山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"安阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"北关区"
                        },
                        {
                            "name":"文峰区"
                        },
                        {
                            "name":"殷都区"
                        },
                        {
                            "name":"龙安区"
                        },
                        {
                            "name":"林州市"
                        },
                        {
                            "name":"安阳县"
                        },
                        {
                            "name":"滑县"
                        },
                        {
                            "name":"内黄县"
                        },
                        {
                            "name":"汤阴县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鹤壁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"淇滨区"
                        },
                        {
                            "name":"山城区"
                        },
                        {
                            "name":"鹤山区"
                        },
                        {
                            "name":"浚县"
                        },
                        {
                            "name":"淇县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"新乡市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"卫滨区"
                        },
                        {
                            "name":"红旗区"
                        },
                        {
                            "name":"凤泉区"
                        },
                        {
                            "name":"牧野区"
                        },
                        {
                            "name":"卫辉市"
                        },
                        {
                            "name":"辉县市"
                        },
                        {
                            "name":"新乡县"
                        },
                        {
                            "name":"获嘉县"
                        },
                        {
                            "name":"原阳县"
                        },
                        {
                            "name":"长垣县"
                        },
                        {
                            "name":"封丘县"
                        },
                        {
                            "name":"延津县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"焦作市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"解放区"
                        },
                        {
                            "name":"中站区"
                        },
                        {
                            "name":"马村区"
                        },
                        {
                            "name":"山阳区"
                        },
                        {
                            "name":"沁阳市"
                        },
                        {
                            "name":"孟州市"
                        },
                        {
                            "name":"修武县"
                        },
                        {
                            "name":"温县"
                        },
                        {
                            "name":"武陟县"
                        },
                        {
                            "name":"博爱县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"濮阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"华龙区"
                        },
                        {
                            "name":"濮阳县"
                        },
                        {
                            "name":"南乐县"
                        },
                        {
                            "name":"台前县"
                        },
                        {
                            "name":"清丰县"
                        },
                        {
                            "name":"范县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"许昌市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"魏都区"
                        },
                        {
                            "name":"禹州市"
                        },
                        {
                            "name":"长葛市"
                        },
                        {
                            "name":"许昌县"
                        },
                        {
                            "name":"鄢陵县"
                        },
                        {
                            "name":"襄城县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"漯河市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"源汇区"
                        },
                        {
                            "name":"郾城区"
                        },
                        {
                            "name":"召陵区"
                        },
                        {
                            "name":"临颍县"
                        },
                        {
                            "name":"舞阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"三门峡市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"湖滨区"
                        },
                        {
                            "name":"义马市"
                        },
                        {
                            "name":"灵宝市"
                        },
                        {
                            "name":"渑池县"
                        },
                        {
                            "name":"卢氏县"
                        },
                        {
                            "name":"陕县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"南阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"卧龙区"
                        },
                        {
                            "name":"宛城区"
                        },
                        {
                            "name":"邓州市"
                        },
                        {
                            "name":"桐柏县"
                        },
                        {
                            "name":"方城县"
                        },
                        {
                            "name":"淅川县"
                        },
                        {
                            "name":"镇平县"
                        },
                        {
                            "name":"唐河县"
                        },
                        {
                            "name":"南召县"
                        },
                        {
                            "name":"内乡县"
                        },
                        {
                            "name":"新野县"
                        },
                        {
                            "name":"社旗县"
                        },
                        {
                            "name":"西峡县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"商丘市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"梁园区"
                        },
                        {
                            "name":"睢阳区"
                        },
                        {
                            "name":"永城市"
                        },
                        {
                            "name":"宁陵县"
                        },
                        {
                            "name":"虞城县"
                        },
                        {
                            "name":"民权县"
                        },
                        {
                            "name":"夏邑县"
                        },
                        {
                            "name":"柘城县"
                        },
                        {
                            "name":"睢县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"信阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"浉河区"
                        },
                        {
                            "name":"平桥区"
                        },
                        {
                            "name":"潢川县"
                        },
                        {
                            "name":"淮滨县"
                        },
                        {
                            "name":"息县"
                        },
                        {
                            "name":"新县"
                        },
                        {
                            "name":"商城县"
                        },
                        {
                            "name":"固始县"
                        },
                        {
                            "name":"罗山县"
                        },
                        {
                            "name":"光山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"周口市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"川汇区"
                        },
                        {
                            "name":"项城市"
                        },
                        {
                            "name":"商水县"
                        },
                        {
                            "name":"淮阳县"
                        },
                        {
                            "name":"太康县"
                        },
                        {
                            "name":"鹿邑县"
                        },
                        {
                            "name":"西华县"
                        },
                        {
                            "name":"扶沟县"
                        },
                        {
                            "name":"沈丘县"
                        },
                        {
                            "name":"郸城县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"驻马店市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"驿城区"
                        },
                        {
                            "name":"确山县"
                        },
                        {
                            "name":"新蔡县"
                        },
                        {
                            "name":"上蔡县"
                        },
                        {
                            "name":"西平县"
                        },
                        {
                            "name":"泌阳县"
                        },
                        {
                            "name":"平舆县"
                        },
                        {
                            "name":"汝南县"
                        },
                        {
                            "name":"遂平县"
                        },
                        {
                            "name":"正阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"焦作市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"济源市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"吉林省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"长春市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"朝阳区"
                        },
                        {
                            "name":"宽城区"
                        },
                        {
                            "name":"二道区"
                        },
                        {
                            "name":"南关区"
                        },
                        {
                            "name":"绿园区"
                        },
                        {
                            "name":"双阳区"
                        },
                        {
                            "name":"九台市"
                        },
                        {
                            "name":"榆树市"
                        },
                        {
                            "name":"德惠市"
                        },
                        {
                            "name":"农安县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"吉林市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"船营区"
                        },
                        {
                            "name":"昌邑区"
                        },
                        {
                            "name":"龙潭区"
                        },
                        {
                            "name":"丰满区"
                        },
                        {
                            "name":"舒兰市"
                        },
                        {
                            "name":"桦甸市"
                        },
                        {
                            "name":"蛟河市"
                        },
                        {
                            "name":"磐石市"
                        },
                        {
                            "name":"永吉县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"四平市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"铁西区"
                        },
                        {
                            "name":"铁东区"
                        },
                        {
                            "name":"公主岭市"
                        },
                        {
                            "name":"双辽市"
                        },
                        {
                            "name":"梨树县"
                        },
                        {
                            "name":"伊通满族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"辽源市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"龙山区"
                        },
                        {
                            "name":"西安区"
                        },
                        {
                            "name":"东辽县"
                        },
                        {
                            "name":"东丰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"通化市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东昌区"
                        },
                        {
                            "name":"二道江区"
                        },
                        {
                            "name":"梅河口市"
                        },
                        {
                            "name":"集安市"
                        },
                        {
                            "name":"通化县"
                        },
                        {
                            "name":"辉南县"
                        },
                        {
                            "name":"柳河县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"白山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"八道江区"
                        },
                        {
                            "name":"江源区"
                        },
                        {
                            "name":"临江市"
                        },
                        {
                            "name":"靖宇县"
                        },
                        {
                            "name":"抚松县"
                        },
                        {
                            "name":"长白朝鲜族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"松原市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"宁江区"
                        },
                        {
                            "name":"乾安县"
                        },
                        {
                            "name":"长岭县"
                        },
                        {
                            "name":"扶余县"
                        },
                        {
                            "name":"前郭尔罗斯蒙古族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"白城市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"洮北区"
                        },
                        {
                            "name":"大安市"
                        },
                        {
                            "name":"洮南市"
                        },
                        {
                            "name":"镇赉县"
                        },
                        {
                            "name":"通榆县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"延边朝鲜族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"延吉市"
                        },
                        {
                            "name":"图们市"
                        },
                        {
                            "name":"敦化市"
                        },
                        {
                            "name":"龙井市"
                        },
                        {
                            "name":"珲春市"
                        },
                        {
                            "name":"和龙市"
                        },
                        {
                            "name":"安图县"
                        },
                        {
                            "name":"汪清县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"黑龙江省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"哈尔滨市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"松北区"
                        },
                        {
                            "name":"道里区"
                        },
                        {
                            "name":"南岗区"
                        },
                        {
                            "name":"平房区"
                        },
                        {
                            "name":"香坊区"
                        },
                        {
                            "name":"道外区"
                        },
                        {
                            "name":"呼兰区"
                        },
                        {
                            "name":"阿城区"
                        },
                        {
                            "name":"双城市"
                        },
                        {
                            "name":"尚志市"
                        },
                        {
                            "name":"五常市"
                        },
                        {
                            "name":"宾县"
                        },
                        {
                            "name":"方正县"
                        },
                        {
                            "name":"通河县"
                        },
                        {
                            "name":"巴彦县"
                        },
                        {
                            "name":"延寿县"
                        },
                        {
                            "name":"木兰县"
                        },
                        {
                            "name":"依兰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"齐齐哈尔市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"龙沙区"
                        },
                        {
                            "name":"昂昂溪区"
                        },
                        {
                            "name":"铁锋区"
                        },
                        {
                            "name":"建华区"
                        },
                        {
                            "name":"富拉尔基区"
                        },
                        {
                            "name":"碾子山区"
                        },
                        {
                            "name":"梅里斯达斡尔族区"
                        },
                        {
                            "name":"讷河市"
                        },
                        {
                            "name":"富裕县"
                        },
                        {
                            "name":"拜泉县"
                        },
                        {
                            "name":"甘南县"
                        },
                        {
                            "name":"依安县"
                        },
                        {
                            "name":"克山县"
                        },
                        {
                            "name":"泰来县"
                        },
                        {
                            "name":"克东县"
                        },
                        {
                            "name":"龙江县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鹤岗市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"兴山区"
                        },
                        {
                            "name":"工农区"
                        },
                        {
                            "name":"南山区"
                        },
                        {
                            "name":"兴安区"
                        },
                        {
                            "name":"向阳区"
                        },
                        {
                            "name":"东山区"
                        },
                        {
                            "name":"萝北县"
                        },
                        {
                            "name":"绥滨县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"双鸭山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"尖山区"
                        },
                        {
                            "name":"岭东区"
                        },
                        {
                            "name":"四方台区"
                        },
                        {
                            "name":"宝山区"
                        },
                        {
                            "name":"集贤县"
                        },
                        {
                            "name":"宝清县"
                        },
                        {
                            "name":"友谊县"
                        },
                        {
                            "name":"饶河县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鸡西市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鸡冠区"
                        },
                        {
                            "name":"恒山区"
                        },
                        {
                            "name":"城子河区"
                        },
                        {
                            "name":"滴道区"
                        },
                        {
                            "name":"梨树区"
                        },
                        {
                            "name":"麻山区"
                        },
                        {
                            "name":"密山市"
                        },
                        {
                            "name":"虎林市"
                        },
                        {
                            "name":"鸡东县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"大庆市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"萨尔图区"
                        },
                        {
                            "name":"红岗区"
                        },
                        {
                            "name":"龙凤区"
                        },
                        {
                            "name":"让胡路区"
                        },
                        {
                            "name":"大同区"
                        },
                        {
                            "name":"林甸县"
                        },
                        {
                            "name":"肇州县"
                        },
                        {
                            "name":"肇源县"
                        },
                        {
                            "name":"杜尔伯特蒙古族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"伊春市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"伊春区"
                        },
                        {
                            "name":"带岭区"
                        },
                        {
                            "name":"南岔区"
                        },
                        {
                            "name":"金山屯区"
                        },
                        {
                            "name":"西林区"
                        },
                        {
                            "name":"美溪区"
                        },
                        {
                            "name":"乌马河区"
                        },
                        {
                            "name":"翠峦区"
                        },
                        {
                            "name":"友好区"
                        },
                        {
                            "name":"上甘岭区"
                        },
                        {
                            "name":"五营区"
                        },
                        {
                            "name":"红星区"
                        },
                        {
                            "name":"新青区"
                        },
                        {
                            "name":"汤旺河区"
                        },
                        {
                            "name":"乌伊岭区"
                        },
                        {
                            "name":"铁力市"
                        },
                        {
                            "name":"嘉荫县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"牡丹江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"爱民区"
                        },
                        {
                            "name":"东安区"
                        },
                        {
                            "name":"阳明区"
                        },
                        {
                            "name":"西安区"
                        },
                        {
                            "name":"绥芬河市"
                        },
                        {
                            "name":"宁安市"
                        },
                        {
                            "name":"海林市"
                        },
                        {
                            "name":"穆棱市"
                        },
                        {
                            "name":"林口县"
                        },
                        {
                            "name":"东宁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"佳木斯市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"向阳区"
                        },
                        {
                            "name":"前进区"
                        },
                        {
                            "name":"东风区"
                        },
                        {
                            "name":"郊区"
                        },
                        {
                            "name":"同江市"
                        },
                        {
                            "name":"富锦市"
                        },
                        {
                            "name":"桦川县"
                        },
                        {
                            "name":"抚远县"
                        },
                        {
                            "name":"桦南县"
                        },
                        {
                            "name":"汤原县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"七台河市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"桃山区"
                        },
                        {
                            "name":"新兴区"
                        },
                        {
                            "name":"茄子河区"
                        },
                        {
                            "name":"勃利县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黑河市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"爱辉区"
                        },
                        {
                            "name":"北安市"
                        },
                        {
                            "name":"五大连池市"
                        },
                        {
                            "name":"逊克县"
                        },
                        {
                            "name":"嫩江县"
                        },
                        {
                            "name":"孙吴县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"绥化市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"北林区"
                        },
                        {
                            "name":"安达市"
                        },
                        {
                            "name":"肇东市"
                        },
                        {
                            "name":"海伦市"
                        },
                        {
                            "name":"绥棱县"
                        },
                        {
                            "name":"兰西县"
                        },
                        {
                            "name":"明水县"
                        },
                        {
                            "name":"青冈县"
                        },
                        {
                            "name":"庆安县"
                        },
                        {
                            "name":"望奎县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"大兴安岭地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"呼玛县"
                        },
                        {
                            "name":"塔河县"
                        },
                        {
                            "name":"漠河县"
                        },
                        {
                            "name":"大兴安岭辖区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"内蒙古自治区",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"呼和浩特市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"回民区"
                        },
                        {
                            "name":"玉泉区"
                        },
                        {
                            "name":"新城区"
                        },
                        {
                            "name":"赛罕区"
                        },
                        {
                            "name":"托克托县"
                        },
                        {
                            "name":"清水河县"
                        },
                        {
                            "name":"武川县"
                        },
                        {
                            "name":"和林格尔县"
                        },
                        {
                            "name":"土默特左旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"包头市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"昆都仑区"
                        },
                        {
                            "name":"青山区"
                        },
                        {
                            "name":"东河区"
                        },
                        {
                            "name":"九原区"
                        },
                        {
                            "name":"石拐区"
                        },
                        {
                            "name":"白云矿区"
                        },
                        {
                            "name":"固阳县"
                        },
                        {
                            "name":"土默特右旗"
                        },
                        {
                            "name":"达尔罕茂明安联合旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"乌海市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海勃湾区"
                        },
                        {
                            "name":"乌达区"
                        },
                        {
                            "name":"海南区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"赤峰市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"红山区"
                        },
                        {
                            "name":"元宝山区"
                        },
                        {
                            "name":"松山区"
                        },
                        {
                            "name":"宁城县"
                        },
                        {
                            "name":"林西县"
                        },
                        {
                            "name":"喀喇沁旗"
                        },
                        {
                            "name":"巴林左旗"
                        },
                        {
                            "name":"敖汉旗"
                        },
                        {
                            "name":"阿鲁科尔沁旗"
                        },
                        {
                            "name":"翁牛特旗"
                        },
                        {
                            "name":"克什克腾旗"
                        },
                        {
                            "name":"巴林右旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"通辽市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"科尔沁区"
                        },
                        {
                            "name":"霍林郭勒市"
                        },
                        {
                            "name":"开鲁县"
                        },
                        {
                            "name":"科尔沁左翼中旗"
                        },
                        {
                            "name":"科尔沁左翼后旗"
                        },
                        {
                            "name":"库伦旗"
                        },
                        {
                            "name":"奈曼旗"
                        },
                        {
                            "name":"扎鲁特旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鄂尔多斯市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东胜区"
                        },
                        {
                            "name":"准格尔旗"
                        },
                        {
                            "name":"乌审旗"
                        },
                        {
                            "name":"伊金霍洛旗"
                        },
                        {
                            "name":"鄂托克旗"
                        },
                        {
                            "name":"鄂托克前旗"
                        },
                        {
                            "name":"杭锦旗"
                        },
                        {
                            "name":"达拉特旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"呼伦贝尔市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海拉尔区"
                        },
                        {
                            "name":"满洲里市"
                        },
                        {
                            "name":"牙克石市"
                        },
                        {
                            "name":"扎兰屯市"
                        },
                        {
                            "name":"根河市"
                        },
                        {
                            "name":"额尔古纳市"
                        },
                        {
                            "name":"陈巴尔虎旗"
                        },
                        {
                            "name":"阿荣旗"
                        },
                        {
                            "name":"新巴尔虎左旗"
                        },
                        {
                            "name":"新巴尔虎右旗"
                        },
                        {
                            "name":"鄂伦春自治旗"
                        },
                        {
                            "name":"莫力达瓦达斡尔族自治旗"
                        },
                        {
                            "name":"鄂温克族自治旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"巴彦淖尔市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"临河区"
                        },
                        {
                            "name":"五原县"
                        },
                        {
                            "name":"磴口县"
                        },
                        {
                            "name":"杭锦后旗"
                        },
                        {
                            "name":"乌拉特中旗"
                        },
                        {
                            "name":"乌拉特前旗"
                        },
                        {
                            "name":"乌拉特后旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"乌兰察布市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"集宁区"
                        },
                        {
                            "name":"丰镇市"
                        },
                        {
                            "name":"兴和县"
                        },
                        {
                            "name":"卓资县"
                        },
                        {
                            "name":"商都县"
                        },
                        {
                            "name":"凉城县"
                        },
                        {
                            "name":"化德县"
                        },
                        {
                            "name":"四子王旗"
                        },
                        {
                            "name":"察哈尔右翼前旗"
                        },
                        {
                            "name":"察哈尔右翼中旗"
                        },
                        {
                            "name":"察哈尔右翼后旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"锡林郭勒盟",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"锡林浩特市"
                        },
                        {
                            "name":"二连浩特市"
                        },
                        {
                            "name":"多伦县"
                        },
                        {
                            "name":"阿巴嘎旗"
                        },
                        {
                            "name":"西乌珠穆沁旗"
                        },
                        {
                            "name":"东乌珠穆沁旗"
                        },
                        {
                            "name":"苏尼特左旗"
                        },
                        {
                            "name":"苏尼特右旗"
                        },
                        {
                            "name":"太仆寺旗"
                        },
                        {
                            "name":"正镶白旗"
                        },
                        {
                            "name":"正蓝旗"
                        },
                        {
                            "name":"镶黄旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"兴安盟",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"乌兰浩特市"
                        },
                        {
                            "name":"阿尔山市"
                        },
                        {
                            "name":"突泉县"
                        },
                        {
                            "name":"扎赉特旗"
                        },
                        {
                            "name":"科尔沁右翼前旗"
                        },
                        {
                            "name":"科尔沁右翼中旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阿拉善盟",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"阿拉善左旗"
                        },
                        {
                            "name":"阿拉善右旗"
                        },
                        {
                            "name":"额济纳旗"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"山东省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"济南市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"市中区"
                        },
                        {
                            "name":"历下区"
                        },
                        {
                            "name":"天桥区"
                        },
                        {
                            "name":"槐荫区"
                        },
                        {
                            "name":"历城区"
                        },
                        {
                            "name":"长清区"
                        },
                        {
                            "name":"章丘市"
                        },
                        {
                            "name":"平阴县"
                        },
                        {
                            "name":"济阳县"
                        },
                        {
                            "name":"商河县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"青岛市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"市南区"
                        },
                        {
                            "name":"市北区"
                        },
                        {
                            "name":"城阳区"
                        },
                        {
                            "name":"四方区"
                        },
                        {
                            "name":"李沧区"
                        },
                        {
                            "name":"黄岛区"
                        },
                        {
                            "name":"崂山区"
                        },
                        {
                            "name":"胶南市"
                        },
                        {
                            "name":"胶州市"
                        },
                        {
                            "name":"平度市"
                        },
                        {
                            "name":"莱西市"
                        },
                        {
                            "name":"即墨市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"淄博市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"张店区"
                        },
                        {
                            "name":"临淄区"
                        },
                        {
                            "name":"淄川区"
                        },
                        {
                            "name":"博山区"
                        },
                        {
                            "name":"周村区"
                        },
                        {
                            "name":"桓台县"
                        },
                        {
                            "name":"高青县"
                        },
                        {
                            "name":"沂源县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"枣庄市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"市中区"
                        },
                        {
                            "name":"山亭区"
                        },
                        {
                            "name":"峄城区"
                        },
                        {
                            "name":"台儿庄区"
                        },
                        {
                            "name":"薛城区"
                        },
                        {
                            "name":"滕州市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"东营市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东营区"
                        },
                        {
                            "name":"河口区"
                        },
                        {
                            "name":"垦利县"
                        },
                        {
                            "name":"广饶县"
                        },
                        {
                            "name":"利津县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"烟台市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"芝罘区"
                        },
                        {
                            "name":"福山区"
                        },
                        {
                            "name":"牟平区"
                        },
                        {
                            "name":"莱山区"
                        },
                        {
                            "name":"龙口市"
                        },
                        {
                            "name":"莱阳市"
                        },
                        {
                            "name":"莱州市"
                        },
                        {
                            "name":"招远市"
                        },
                        {
                            "name":"蓬莱市"
                        },
                        {
                            "name":"栖霞市"
                        },
                        {
                            "name":"海阳市"
                        },
                        {
                            "name":"长岛县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"潍坊市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"潍城区"
                        },
                        {
                            "name":"寒亭区"
                        },
                        {
                            "name":"坊子区"
                        },
                        {
                            "name":"奎文区"
                        },
                        {
                            "name":"青州市"
                        },
                        {
                            "name":"诸城市"
                        },
                        {
                            "name":"寿光市"
                        },
                        {
                            "name":"安丘市"
                        },
                        {
                            "name":"高密市"
                        },
                        {
                            "name":"昌邑市"
                        },
                        {
                            "name":"昌乐县"
                        },
                        {
                            "name":"临朐县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"济宁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"市中区"
                        },
                        {
                            "name":"任城区"
                        },
                        {
                            "name":"曲阜市"
                        },
                        {
                            "name":"兖州市"
                        },
                        {
                            "name":"邹城市"
                        },
                        {
                            "name":"鱼台县"
                        },
                        {
                            "name":"金乡县"
                        },
                        {
                            "name":"嘉祥县"
                        },
                        {
                            "name":"微山县"
                        },
                        {
                            "name":"汶上县"
                        },
                        {
                            "name":"泗水县"
                        },
                        {
                            "name":"梁山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"泰安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"泰山区"
                        },
                        {
                            "name":"岱岳区"
                        },
                        {
                            "name":"新泰市"
                        },
                        {
                            "name":"肥城市"
                        },
                        {
                            "name":"宁阳县"
                        },
                        {
                            "name":"东平县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"威海市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"环翠区"
                        },
                        {
                            "name":"乳山市"
                        },
                        {
                            "name":"文登市"
                        },
                        {
                            "name":"荣成市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"日照市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东港区"
                        },
                        {
                            "name":"岚山区"
                        },
                        {
                            "name":"五莲县"
                        },
                        {
                            "name":"莒县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"莱芜市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"莱城区"
                        },
                        {
                            "name":"钢城区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"临沂市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"兰山区"
                        },
                        {
                            "name":"罗庄区"
                        },
                        {
                            "name":"河东区"
                        },
                        {
                            "name":"沂南县"
                        },
                        {
                            "name":"郯城县"
                        },
                        {
                            "name":"沂水县"
                        },
                        {
                            "name":"苍山县"
                        },
                        {
                            "name":"费县"
                        },
                        {
                            "name":"平邑县"
                        },
                        {
                            "name":"莒南县"
                        },
                        {
                            "name":"蒙阴县"
                        },
                        {
                            "name":"临沭县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"德州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"德城区"
                        },
                        {
                            "name":"乐陵市"
                        },
                        {
                            "name":"禹城市"
                        },
                        {
                            "name":"陵县"
                        },
                        {
                            "name":"宁津县"
                        },
                        {
                            "name":"齐河县"
                        },
                        {
                            "name":"武城县"
                        },
                        {
                            "name":"庆云县"
                        },
                        {
                            "name":"平原县"
                        },
                        {
                            "name":"夏津县"
                        },
                        {
                            "name":"临邑县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"聊城市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东昌府区"
                        },
                        {
                            "name":"临清市"
                        },
                        {
                            "name":"高唐县"
                        },
                        {
                            "name":"阳谷县"
                        },
                        {
                            "name":"茌平县"
                        },
                        {
                            "name":"莘县"
                        },
                        {
                            "name":"东阿县"
                        },
                        {
                            "name":"冠县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"滨州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"滨城区"
                        },
                        {
                            "name":"邹平县"
                        },
                        {
                            "name":"沾化县"
                        },
                        {
                            "name":"惠民县"
                        },
                        {
                            "name":"博兴县"
                        },
                        {
                            "name":"阳信县"
                        },
                        {
                            "name":"无棣县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"菏泽市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"牡丹区"
                        },
                        {
                            "name":"鄄城县"
                        },
                        {
                            "name":"单县"
                        },
                        {
                            "name":"郓城县"
                        },
                        {
                            "name":"曹县"
                        },
                        {
                            "name":"定陶县"
                        },
                        {
                            "name":"巨野县"
                        },
                        {
                            "name":"东明县"
                        },
                        {
                            "name":"成武县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"安徽省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"合肥市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"庐阳区"
                        },
                        {
                            "name":"瑶海区"
                        },
                        {
                            "name":"蜀山区"
                        },
                        {
                            "name":"包河区"
                        },
                        {
                            "name":"长丰县"
                        },
                        {
                            "name":"肥东县"
                        },
                        {
                            "name":"肥西县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"芜湖市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"镜湖区"
                        },
                        {
                            "name":"弋江区"
                        },
                        {
                            "name":"鸠江区"
                        },
                        {
                            "name":"三山区"
                        },
                        {
                            "name":"芜湖县"
                        },
                        {
                            "name":"南陵县"
                        },
                        {
                            "name":"繁昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"蚌埠市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"蚌山区"
                        },
                        {
                            "name":"龙子湖区"
                        },
                        {
                            "name":"禹会区"
                        },
                        {
                            "name":"淮上区"
                        },
                        {
                            "name":"怀远县"
                        },
                        {
                            "name":"固镇县"
                        },
                        {
                            "name":"五河县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"淮南市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"田家庵区"
                        },
                        {
                            "name":"大通区"
                        },
                        {
                            "name":"谢家集区"
                        },
                        {
                            "name":"八公山区"
                        },
                        {
                            "name":"潘集区"
                        },
                        {
                            "name":"凤台县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"马鞍山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"雨山区"
                        },
                        {
                            "name":"花山区"
                        },
                        {
                            "name":"金家庄区"
                        },
                        {
                            "name":"当涂县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"淮北市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"相山区"
                        },
                        {
                            "name":"杜集区"
                        },
                        {
                            "name":"烈山区"
                        },
                        {
                            "name":"濉溪县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"铜陵市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"铜官山区"
                        },
                        {
                            "name":"狮子山区"
                        },
                        {
                            "name":"郊区"
                        },
                        {
                            "name":"铜陵县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"安庆市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"迎江区"
                        },
                        {
                            "name":"大观区"
                        },
                        {
                            "name":"宜秀区"
                        },
                        {
                            "name":"桐城市"
                        },
                        {
                            "name":"宿松县"
                        },
                        {
                            "name":"枞阳县"
                        },
                        {
                            "name":"太湖县"
                        },
                        {
                            "name":"怀宁县"
                        },
                        {
                            "name":"岳西县"
                        },
                        {
                            "name":"望江县"
                        },
                        {
                            "name":"潜山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黄山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"屯溪区"
                        },
                        {
                            "name":"黄山区"
                        },
                        {
                            "name":"徽州区"
                        },
                        {
                            "name":"休宁县"
                        },
                        {
                            "name":"歙县"
                        },
                        {
                            "name":"祁门县"
                        },
                        {
                            "name":"黟县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"滁州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"琅琊区"
                        },
                        {
                            "name":"南谯区"
                        },
                        {
                            "name":"天长市"
                        },
                        {
                            "name":"明光市"
                        },
                        {
                            "name":"全椒县"
                        },
                        {
                            "name":"来安县"
                        },
                        {
                            "name":"定远县"
                        },
                        {
                            "name":"凤阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阜阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"颍州区"
                        },
                        {
                            "name":"颍东区"
                        },
                        {
                            "name":"颍泉区"
                        },
                        {
                            "name":"界首市"
                        },
                        {
                            "name":"临泉县"
                        },
                        {
                            "name":"颍上县"
                        },
                        {
                            "name":"阜南县"
                        },
                        {
                            "name":"太和县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宿州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"埇桥区"
                        },
                        {
                            "name":"萧县"
                        },
                        {
                            "name":"泗县"
                        },
                        {
                            "name":"砀山县"
                        },
                        {
                            "name":"灵璧县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"巢湖市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"居巢区"
                        },
                        {
                            "name":"含山县"
                        },
                        {
                            "name":"无为县"
                        },
                        {
                            "name":"庐江县"
                        },
                        {
                            "name":"和县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"六安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"金安区"
                        },
                        {
                            "name":"裕安区"
                        },
                        {
                            "name":"寿县"
                        },
                        {
                            "name":"霍山县"
                        },
                        {
                            "name":"霍邱县"
                        },
                        {
                            "name":"舒城县"
                        },
                        {
                            "name":"金寨县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"亳州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"谯城区"
                        },
                        {
                            "name":"利辛县"
                        },
                        {
                            "name":"涡阳县"
                        },
                        {
                            "name":"蒙城县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"池州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"贵池区"
                        },
                        {
                            "name":"东至县"
                        },
                        {
                            "name":"石台县"
                        },
                        {
                            "name":"青阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宣城市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"宣州区"
                        },
                        {
                            "name":"宁国市"
                        },
                        {
                            "name":"广德县"
                        },
                        {
                            "name":"郎溪县"
                        },
                        {
                            "name":"泾县"
                        },
                        {
                            "name":"旌德县"
                        },
                        {
                            "name":"绩溪县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"浙江省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"杭州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"拱墅区"
                        },
                        {
                            "name":"西湖区"
                        },
                        {
                            "name":"上城区"
                        },
                        {
                            "name":"下城区"
                        },
                        {
                            "name":"江干区"
                        },
                        {
                            "name":"滨江区"
                        },
                        {
                            "name":"余杭区"
                        },
                        {
                            "name":"萧山区"
                        },
                        {
                            "name":"建德市"
                        },
                        {
                            "name":"富阳市"
                        },
                        {
                            "name":"临安市"
                        },
                        {
                            "name":"桐庐县"
                        },
                        {
                            "name":"淳安县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宁波市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海曙区"
                        },
                        {
                            "name":"江东区"
                        },
                        {
                            "name":"江北区"
                        },
                        {
                            "name":"镇海区"
                        },
                        {
                            "name":"北仑区"
                        },
                        {
                            "name":"鄞州区"
                        },
                        {
                            "name":"余姚市"
                        },
                        {
                            "name":"慈溪市"
                        },
                        {
                            "name":"奉化市"
                        },
                        {
                            "name":"宁海县"
                        },
                        {
                            "name":"象山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"温州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鹿城区"
                        },
                        {
                            "name":"龙湾区"
                        },
                        {
                            "name":"瓯海区"
                        },
                        {
                            "name":"瑞安市"
                        },
                        {
                            "name":"乐清市"
                        },
                        {
                            "name":"永嘉县"
                        },
                        {
                            "name":"洞头县"
                        },
                        {
                            "name":"平阳县"
                        },
                        {
                            "name":"苍南县"
                        },
                        {
                            "name":"文成县"
                        },
                        {
                            "name":"泰顺县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"嘉兴市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"秀城区"
                        },
                        {
                            "name":"秀洲区"
                        },
                        {
                            "name":"海宁市"
                        },
                        {
                            "name":"平湖市"
                        },
                        {
                            "name":"桐乡市"
                        },
                        {
                            "name":"嘉善县"
                        },
                        {
                            "name":"海盐县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"湖州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"吴兴区"
                        },
                        {
                            "name":"南浔区"
                        },
                        {
                            "name":"长兴县"
                        },
                        {
                            "name":"德清县"
                        },
                        {
                            "name":"安吉县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"绍兴市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"越城区"
                        },
                        {
                            "name":"诸暨市"
                        },
                        {
                            "name":"上虞市"
                        },
                        {
                            "name":"嵊州市"
                        },
                        {
                            "name":"绍兴县"
                        },
                        {
                            "name":"新昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"金华市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"婺城区"
                        },
                        {
                            "name":"金东区"
                        },
                        {
                            "name":"兰溪市"
                        },
                        {
                            "name":"义乌市"
                        },
                        {
                            "name":"东阳市"
                        },
                        {
                            "name":"永康市"
                        },
                        {
                            "name":"武义县"
                        },
                        {
                            "name":"浦江县"
                        },
                        {
                            "name":"磐安县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"衢州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"柯城区"
                        },
                        {
                            "name":"衢江区"
                        },
                        {
                            "name":"江山市"
                        },
                        {
                            "name":"龙游县"
                        },
                        {
                            "name":"常山县"
                        },
                        {
                            "name":"开化县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"舟山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"定海区"
                        },
                        {
                            "name":"普陀区"
                        },
                        {
                            "name":"岱山县"
                        },
                        {
                            "name":"嵊泗县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"台州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"椒江区"
                        },
                        {
                            "name":"黄岩区"
                        },
                        {
                            "name":"路桥区"
                        },
                        {
                            "name":"临海市"
                        },
                        {
                            "name":"温岭市"
                        },
                        {
                            "name":"玉环县"
                        },
                        {
                            "name":"天台县"
                        },
                        {
                            "name":"仙居县"
                        },
                        {
                            "name":"三门县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"丽水市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"莲都区"
                        },
                        {
                            "name":"龙泉市"
                        },
                        {
                            "name":"缙云县"
                        },
                        {
                            "name":"青田县"
                        },
                        {
                            "name":"云和县"
                        },
                        {
                            "name":"遂昌县"
                        },
                        {
                            "name":"松阳县"
                        },
                        {
                            "name":"庆元县"
                        },
                        {
                            "name":"景宁畲族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"福建省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"福州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鼓楼区"
                        },
                        {
                            "name":"台江区"
                        },
                        {
                            "name":"仓山区"
                        },
                        {
                            "name":"马尾区"
                        },
                        {
                            "name":"晋安区"
                        },
                        {
                            "name":"福清市"
                        },
                        {
                            "name":"长乐市"
                        },
                        {
                            "name":"闽侯县"
                        },
                        {
                            "name":"闽清县"
                        },
                        {
                            "name":"永泰县"
                        },
                        {
                            "name":"连江县"
                        },
                        {
                            "name":"罗源县"
                        },
                        {
                            "name":"平潭县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"厦门市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"思明区"
                        },
                        {
                            "name":"海沧区"
                        },
                        {
                            "name":"湖里区"
                        },
                        {
                            "name":"集美区"
                        },
                        {
                            "name":"同安区"
                        },
                        {
                            "name":"翔安区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"莆田市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城厢区"
                        },
                        {
                            "name":"涵江区"
                        },
                        {
                            "name":"荔城区"
                        },
                        {
                            "name":"秀屿区"
                        },
                        {
                            "name":"仙游县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"三明市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"梅列区"
                        },
                        {
                            "name":"三元区"
                        },
                        {
                            "name":"永安市"
                        },
                        {
                            "name":"明溪县"
                        },
                        {
                            "name":"将乐县"
                        },
                        {
                            "name":"大田县"
                        },
                        {
                            "name":"宁化县"
                        },
                        {
                            "name":"建宁县"
                        },
                        {
                            "name":"沙县"
                        },
                        {
                            "name":"尤溪县"
                        },
                        {
                            "name":"清流县"
                        },
                        {
                            "name":"泰宁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"泉州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鲤城区"
                        },
                        {
                            "name":"丰泽区"
                        },
                        {
                            "name":"洛江区"
                        },
                        {
                            "name":"泉港区"
                        },
                        {
                            "name":"石狮市"
                        },
                        {
                            "name":"晋江市"
                        },
                        {
                            "name":"南安市"
                        },
                        {
                            "name":"惠安县"
                        },
                        {
                            "name":"永春县"
                        },
                        {
                            "name":"安溪县"
                        },
                        {
                            "name":"德化县"
                        },
                        {
                            "name":"金门县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"漳州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"芗城区"
                        },
                        {
                            "name":"龙文区"
                        },
                        {
                            "name":"龙海市"
                        },
                        {
                            "name":"平和县"
                        },
                        {
                            "name":"南靖县"
                        },
                        {
                            "name":"诏安县"
                        },
                        {
                            "name":"漳浦县"
                        },
                        {
                            "name":"华安县"
                        },
                        {
                            "name":"东山县"
                        },
                        {
                            "name":"长泰县"
                        },
                        {
                            "name":"云霄县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"南平市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"延平区"
                        },
                        {
                            "name":"建瓯市"
                        },
                        {
                            "name":"邵武市"
                        },
                        {
                            "name":"武夷山市"
                        },
                        {
                            "name":"建阳市"
                        },
                        {
                            "name":"松溪县"
                        },
                        {
                            "name":"光泽县"
                        },
                        {
                            "name":"顺昌县"
                        },
                        {
                            "name":"浦城县"
                        },
                        {
                            "name":"政和县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"龙岩市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"新罗区"
                        },
                        {
                            "name":"漳平市"
                        },
                        {
                            "name":"长汀县"
                        },
                        {
                            "name":"武平县"
                        },
                        {
                            "name":"上杭县"
                        },
                        {
                            "name":"永定区"
                        },
                        {
                            "name":"连城县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宁德市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"蕉城区"
                        },
                        {
                            "name":"福安市"
                        },
                        {
                            "name":"福鼎市"
                        },
                        {
                            "name":"寿宁县"
                        },
                        {
                            "name":"霞浦县"
                        },
                        {
                            "name":"柘荣县"
                        },
                        {
                            "name":"屏南县"
                        },
                        {
                            "name":"古田县"
                        },
                        {
                            "name":"周宁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"湖南省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"长沙市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"岳麓区"
                        },
                        {
                            "name":"芙蓉区"
                        },
                        {
                            "name":"天心区"
                        },
                        {
                            "name":"开福区"
                        },
                        {
                            "name":"雨花区"
                        },
                        {
                            "name":"浏阳市"
                        },
                        {
                            "name":"长沙县"
                        },
                        {
                            "name":"望城县"
                        },
                        {
                            "name":"宁乡县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"株洲市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"天元区"
                        },
                        {
                            "name":"荷塘区"
                        },
                        {
                            "name":"芦淞区"
                        },
                        {
                            "name":"石峰区"
                        },
                        {
                            "name":"醴陵市"
                        },
                        {
                            "name":"株洲县"
                        },
                        {
                            "name":"炎陵县"
                        },
                        {
                            "name":"茶陵县"
                        },
                        {
                            "name":"攸县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"湘潭市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"岳塘区"
                        },
                        {
                            "name":"雨湖区"
                        },
                        {
                            "name":"湘乡市"
                        },
                        {
                            "name":"韶山市"
                        },
                        {
                            "name":"湘潭县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"衡阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"雁峰区"
                        },
                        {
                            "name":"珠晖区"
                        },
                        {
                            "name":"石鼓区"
                        },
                        {
                            "name":"蒸湘区"
                        },
                        {
                            "name":"南岳区"
                        },
                        {
                            "name":"耒阳市"
                        },
                        {
                            "name":"常宁市"
                        },
                        {
                            "name":"衡阳县"
                        },
                        {
                            "name":"衡东县"
                        },
                        {
                            "name":"衡山县"
                        },
                        {
                            "name":"衡南县"
                        },
                        {
                            "name":"祁东县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"邵阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"双清区"
                        },
                        {
                            "name":"大祥区"
                        },
                        {
                            "name":"北塔区"
                        },
                        {
                            "name":"武冈市"
                        },
                        {
                            "name":"邵东县"
                        },
                        {
                            "name":"洞口县"
                        },
                        {
                            "name":"新邵县"
                        },
                        {
                            "name":"绥宁县"
                        },
                        {
                            "name":"新宁县"
                        },
                        {
                            "name":"邵阳县"
                        },
                        {
                            "name":"隆回县"
                        },
                        {
                            "name":"城步苗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"岳阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"岳阳楼区"
                        },
                        {
                            "name":"云溪区"
                        },
                        {
                            "name":"君山区"
                        },
                        {
                            "name":"临湘市"
                        },
                        {
                            "name":"汨罗市"
                        },
                        {
                            "name":"岳阳县"
                        },
                        {
                            "name":"湘阴县"
                        },
                        {
                            "name":"平江县"
                        },
                        {
                            "name":"华容县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"常德市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"武陵区"
                        },
                        {
                            "name":"鼎城区"
                        },
                        {
                            "name":"津市市"
                        },
                        {
                            "name":"澧县"
                        },
                        {
                            "name":"临澧县"
                        },
                        {
                            "name":"桃源县"
                        },
                        {
                            "name":"汉寿县"
                        },
                        {
                            "name":"安乡县"
                        },
                        {
                            "name":"石门县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"张家界市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"永定区"
                        },
                        {
                            "name":"武陵源区"
                        },
                        {
                            "name":"慈利县"
                        },
                        {
                            "name":"桑植县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"益阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"赫山区"
                        },
                        {
                            "name":"资阳区"
                        },
                        {
                            "name":"沅江市"
                        },
                        {
                            "name":"桃江县"
                        },
                        {
                            "name":"南县"
                        },
                        {
                            "name":"安化县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"郴州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"北湖区"
                        },
                        {
                            "name":"苏仙区"
                        },
                        {
                            "name":"资兴市"
                        },
                        {
                            "name":"宜章县"
                        },
                        {
                            "name":"汝城县"
                        },
                        {
                            "name":"安仁县"
                        },
                        {
                            "name":"嘉禾县"
                        },
                        {
                            "name":"临武县"
                        },
                        {
                            "name":"桂东县"
                        },
                        {
                            "name":"永兴县"
                        },
                        {
                            "name":"桂阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"永州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"冷水滩区"
                        },
                        {
                            "name":"零陵区"
                        },
                        {
                            "name":"祁阳县"
                        },
                        {
                            "name":"蓝山县"
                        },
                        {
                            "name":"宁远县"
                        },
                        {
                            "name":"新田县"
                        },
                        {
                            "name":"东安县"
                        },
                        {
                            "name":"江永县"
                        },
                        {
                            "name":"道县"
                        },
                        {
                            "name":"双牌县"
                        },
                        {
                            "name":"江华瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"怀化市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"鹤城区"
                        },
                        {
                            "name":"洪江市"
                        },
                        {
                            "name":"会同县"
                        },
                        {
                            "name":"沅陵县"
                        },
                        {
                            "name":"辰溪县"
                        },
                        {
                            "name":"溆浦县"
                        },
                        {
                            "name":"中方县"
                        },
                        {
                            "name":"新晃侗族自治县"
                        },
                        {
                            "name":"芷江侗族自治县"
                        },
                        {
                            "name":"通道侗族自治县"
                        },
                        {
                            "name":"靖州苗族侗族自治县"
                        },
                        {
                            "name":"麻阳苗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"娄底市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"娄星区"
                        },
                        {
                            "name":"冷水江市"
                        },
                        {
                            "name":"涟源市"
                        },
                        {
                            "name":"新化县"
                        },
                        {
                            "name":"双峰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"湘西土家族苗族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"吉首市"
                        },
                        {
                            "name":"古丈县"
                        },
                        {
                            "name":"龙山县"
                        },
                        {
                            "name":"永顺县"
                        },
                        {
                            "name":"凤凰县"
                        },
                        {
                            "name":"泸溪县"
                        },
                        {
                            "name":"保靖县"
                        },
                        {
                            "name":"花垣县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"广西省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"南宁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"青秀区"
                        },
                        {
                            "name":"兴宁区"
                        },
                        {
                            "name":"西乡塘区"
                        },
                        {
                            "name":"良庆区"
                        },
                        {
                            "name":"江南区"
                        },
                        {
                            "name":"邕宁区"
                        },
                        {
                            "name":"武鸣县"
                        },
                        {
                            "name":"隆安县"
                        },
                        {
                            "name":"马山县"
                        },
                        {
                            "name":"上林县"
                        },
                        {
                            "name":"宾阳县"
                        },
                        {
                            "name":"横县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"柳州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城中区"
                        },
                        {
                            "name":"鱼峰区"
                        },
                        {
                            "name":"柳北区"
                        },
                        {
                            "name":"柳南区"
                        },
                        {
                            "name":"柳江县"
                        },
                        {
                            "name":"柳城县"
                        },
                        {
                            "name":"鹿寨县"
                        },
                        {
                            "name":"融安县"
                        },
                        {
                            "name":"融水苗族自治县"
                        },
                        {
                            "name":"三江侗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"桂林市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"象山区"
                        },
                        {
                            "name":"秀峰区"
                        },
                        {
                            "name":"叠彩区"
                        },
                        {
                            "name":"七星区"
                        },
                        {
                            "name":"雁山区"
                        },
                        {
                            "name":"阳朔县"
                        },
                        {
                            "name":"临桂县"
                        },
                        {
                            "name":"灵川县"
                        },
                        {
                            "name":"全州县"
                        },
                        {
                            "name":"平乐县"
                        },
                        {
                            "name":"兴安县"
                        },
                        {
                            "name":"灌阳县"
                        },
                        {
                            "name":"荔浦县"
                        },
                        {
                            "name":"资源县"
                        },
                        {
                            "name":"永福县"
                        },
                        {
                            "name":"龙胜各族自治县"
                        },
                        {
                            "name":"恭城瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"梧州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"万秀区"
                        },
                        {
                            "name":"蝶山区"
                        },
                        {
                            "name":"长洲区"
                        },
                        {
                            "name":"岑溪市"
                        },
                        {
                            "name":"苍梧县"
                        },
                        {
                            "name":"藤县"
                        },
                        {
                            "name":"蒙山县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"北海市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海城区"
                        },
                        {
                            "name":"银海区"
                        },
                        {
                            "name":"铁山港区"
                        },
                        {
                            "name":"合浦县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"防城港市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"港口区"
                        },
                        {
                            "name":"防城区"
                        },
                        {
                            "name":"东兴市"
                        },
                        {
                            "name":"上思县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"钦州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"钦南区"
                        },
                        {
                            "name":"钦北区"
                        },
                        {
                            "name":"灵山县"
                        },
                        {
                            "name":"浦北县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"贵港市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"港北区"
                        },
                        {
                            "name":"港南区"
                        },
                        {
                            "name":"覃塘区"
                        },
                        {
                            "name":"桂平市"
                        },
                        {
                            "name":"平南县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"玉林市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"玉州区"
                        },
                        {
                            "name":"北流市"
                        },
                        {
                            "name":"容县"
                        },
                        {
                            "name":"陆川县"
                        },
                        {
                            "name":"博白县"
                        },
                        {
                            "name":"兴业县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"百色市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"右江区"
                        },
                        {
                            "name":"凌云县"
                        },
                        {
                            "name":"平果县"
                        },
                        {
                            "name":"西林县"
                        },
                        {
                            "name":"乐业县"
                        },
                        {
                            "name":"德保县"
                        },
                        {
                            "name":"田林县"
                        },
                        {
                            "name":"田阳县"
                        },
                        {
                            "name":"靖西县"
                        },
                        {
                            "name":"田东县"
                        },
                        {
                            "name":"那坡县"
                        },
                        {
                            "name":"隆林各族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"贺州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"八步区"
                        },
                        {
                            "name":"钟山县"
                        },
                        {
                            "name":"昭平县"
                        },
                        {
                            "name":"富川瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"河池市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"金城江区"
                        },
                        {
                            "name":"宜州市"
                        },
                        {
                            "name":"天峨县"
                        },
                        {
                            "name":"凤山县"
                        },
                        {
                            "name":"南丹县"
                        },
                        {
                            "name":"东兰县"
                        },
                        {
                            "name":"都安瑶族自治县"
                        },
                        {
                            "name":"罗城仫佬族自治县"
                        },
                        {
                            "name":"巴马瑶族自治县"
                        },
                        {
                            "name":"环江毛南族自治县"
                        },
                        {
                            "name":"大化瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"来宾市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"兴宾区"
                        },
                        {
                            "name":"合山市"
                        },
                        {
                            "name":"象州县"
                        },
                        {
                            "name":"武宣县"
                        },
                        {
                            "name":"忻城县"
                        },
                        {
                            "name":"金秀瑶族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"崇左市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"江州区"
                        },
                        {
                            "name":"凭祥市"
                        },
                        {
                            "name":"宁明县"
                        },
                        {
                            "name":"扶绥县"
                        },
                        {
                            "name":"龙州县"
                        },
                        {
                            "name":"大新县"
                        },
                        {
                            "name":"天等县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"江西省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"南昌市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"东湖区"
                        },
                        {
                            "name":"西湖区"
                        },
                        {
                            "name":"青云谱区"
                        },
                        {
                            "name":"湾里区"
                        },
                        {
                            "name":"青山湖区"
                        },
                        {
                            "name":"新建县"
                        },
                        {
                            "name":"南昌县"
                        },
                        {
                            "name":"进贤县"
                        },
                        {
                            "name":"安义县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"景德镇市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"珠山区"
                        },
                        {
                            "name":"昌江区"
                        },
                        {
                            "name":"乐平市"
                        },
                        {
                            "name":"浮梁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"萍乡市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"安源区"
                        },
                        {
                            "name":"湘东区"
                        },
                        {
                            "name":"莲花县"
                        },
                        {
                            "name":"上栗县"
                        },
                        {
                            "name":"芦溪县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"九江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"浔阳区"
                        },
                        {
                            "name":"庐山区"
                        },
                        {
                            "name":"瑞昌市"
                        },
                        {
                            "name":"九江县"
                        },
                        {
                            "name":"星子县"
                        },
                        {
                            "name":"武宁县"
                        },
                        {
                            "name":"彭泽县"
                        },
                        {
                            "name":"永修县"
                        },
                        {
                            "name":"修水县"
                        },
                        {
                            "name":"湖口县"
                        },
                        {
                            "name":"德安县"
                        },
                        {
                            "name":"都昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"新余市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"渝水区"
                        },
                        {
                            "name":"分宜县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"鹰潭市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"月湖区"
                        },
                        {
                            "name":"贵溪市"
                        },
                        {
                            "name":"余江县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"赣州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"章贡区"
                        },
                        {
                            "name":"瑞金市"
                        },
                        {
                            "name":"南康市"
                        },
                        {
                            "name":"石城县"
                        },
                        {
                            "name":"安远县"
                        },
                        {
                            "name":"赣县"
                        },
                        {
                            "name":"宁都县"
                        },
                        {
                            "name":"寻乌县"
                        },
                        {
                            "name":"兴国县"
                        },
                        {
                            "name":"定南县"
                        },
                        {
                            "name":"上犹县"
                        },
                        {
                            "name":"于都县"
                        },
                        {
                            "name":"龙南县"
                        },
                        {
                            "name":"崇义县"
                        },
                        {
                            "name":"信丰县"
                        },
                        {
                            "name":"全南县"
                        },
                        {
                            "name":"大余县"
                        },
                        {
                            "name":"会昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"吉安市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"吉州区"
                        },
                        {
                            "name":"青原区"
                        },
                        {
                            "name":"井冈山市"
                        },
                        {
                            "name":"吉安县"
                        },
                        {
                            "name":"永丰县"
                        },
                        {
                            "name":"永新县"
                        },
                        {
                            "name":"新干县"
                        },
                        {
                            "name":"泰和县"
                        },
                        {
                            "name":"峡江县"
                        },
                        {
                            "name":"遂川县"
                        },
                        {
                            "name":"安福县"
                        },
                        {
                            "name":"吉水县"
                        },
                        {
                            "name":"万安县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"宜春市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"袁州区"
                        },
                        {
                            "name":"丰城市"
                        },
                        {
                            "name":"樟树市"
                        },
                        {
                            "name":"高安市"
                        },
                        {
                            "name":"铜鼓县"
                        },
                        {
                            "name":"靖安县"
                        },
                        {
                            "name":"宜丰县"
                        },
                        {
                            "name":"奉新县"
                        },
                        {
                            "name":"万载县"
                        },
                        {
                            "name":"上高县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"抚州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"临川区"
                        },
                        {
                            "name":"南丰县"
                        },
                        {
                            "name":"乐安县"
                        },
                        {
                            "name":"金溪县"
                        },
                        {
                            "name":"南城县"
                        },
                        {
                            "name":"东乡县"
                        },
                        {
                            "name":"资溪县"
                        },
                        {
                            "name":"宜黄县"
                        },
                        {
                            "name":"广昌县"
                        },
                        {
                            "name":"黎川县"
                        },
                        {
                            "name":"崇仁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"上饶市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"信州区"
                        },
                        {
                            "name":"德兴市"
                        },
                        {
                            "name":"上饶县"
                        },
                        {
                            "name":"广丰县"
                        },
                        {
                            "name":"鄱阳县"
                        },
                        {
                            "name":"婺源县"
                        },
                        {
                            "name":"铅山县"
                        },
                        {
                            "name":"余干县"
                        },
                        {
                            "name":"横峰县"
                        },
                        {
                            "name":"弋阳县"
                        },
                        {
                            "name":"玉山县"
                        },
                        {
                            "name":"万年县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"贵州省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"贵阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"南明区"
                        },
                        {
                            "name":"云岩区"
                        },
                        {
                            "name":"花溪区"
                        },
                        {
                            "name":"乌当区"
                        },
                        {
                            "name":"白云区"
                        },
                        {
                            "name":"小河区"
                        },
                        {
                            "name":"清镇市"
                        },
                        {
                            "name":"开阳县"
                        },
                        {
                            "name":"修文县"
                        },
                        {
                            "name":"息烽县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"六盘水市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"钟山区"
                        },
                        {
                            "name":"水城县"
                        },
                        {
                            "name":"盘县"
                        },
                        {
                            "name":"六枝特区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"遵义市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"红花岗区"
                        },
                        {
                            "name":"汇川区"
                        },
                        {
                            "name":"赤水市"
                        },
                        {
                            "name":"仁怀市"
                        },
                        {
                            "name":"遵义县"
                        },
                        {
                            "name":"绥阳县"
                        },
                        {
                            "name":"桐梓县"
                        },
                        {
                            "name":"习水县"
                        },
                        {
                            "name":"凤冈县"
                        },
                        {
                            "name":"正安县"
                        },
                        {
                            "name":"余庆县"
                        },
                        {
                            "name":"湄潭县"
                        },
                        {
                            "name":"道真仡佬族苗族自治县"
                        },
                        {
                            "name":"务川仡佬族苗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"安顺市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"西秀区"
                        },
                        {
                            "name":"普定县"
                        },
                        {
                            "name":"平坝县"
                        },
                        {
                            "name":"镇宁布依族苗族自治县"
                        },
                        {
                            "name":"紫云苗族布依族自治县"
                        },
                        {
                            "name":"关岭布依族苗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"铜仁地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"铜仁市"
                        },
                        {
                            "name":"德江县"
                        },
                        {
                            "name":"江口县"
                        },
                        {
                            "name":"思南县"
                        },
                        {
                            "name":"石阡县"
                        },
                        {
                            "name":"玉屏侗族自治县"
                        },
                        {
                            "name":"松桃苗族自治县"
                        },
                        {
                            "name":"印江土家族苗族自治县"
                        },
                        {
                            "name":"沿河土家族自治县"
                        },
                        {
                            "name":"万山特区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"毕节地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"毕节市"
                        },
                        {
                            "name":"黔西县"
                        },
                        {
                            "name":"大方县"
                        },
                        {
                            "name":"织金县"
                        },
                        {
                            "name":"金沙县"
                        },
                        {
                            "name":"赫章县"
                        },
                        {
                            "name":"纳雍县"
                        },
                        {
                            "name":"威宁彝族回族苗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黔西南布依族苗族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"兴义市"
                        },
                        {
                            "name":"望谟县"
                        },
                        {
                            "name":"兴仁县"
                        },
                        {
                            "name":"普安县"
                        },
                        {
                            "name":"册亨县"
                        },
                        {
                            "name":"晴隆县"
                        },
                        {
                            "name":"贞丰县"
                        },
                        {
                            "name":"安龙县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黔东南苗族侗族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"凯里市"
                        },
                        {
                            "name":"施秉县"
                        },
                        {
                            "name":"从江县"
                        },
                        {
                            "name":"锦屏县"
                        },
                        {
                            "name":"镇远县"
                        },
                        {
                            "name":"麻江县"
                        },
                        {
                            "name":"台江县"
                        },
                        {
                            "name":"天柱县"
                        },
                        {
                            "name":"黄平县"
                        },
                        {
                            "name":"榕江县"
                        },
                        {
                            "name":"剑河县"
                        },
                        {
                            "name":"三穗县"
                        },
                        {
                            "name":"雷山县"
                        },
                        {
                            "name":"黎平县"
                        },
                        {
                            "name":"岑巩县"
                        },
                        {
                            "name":"丹寨县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黔南布依族苗族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"都匀市"
                        },
                        {
                            "name":"福泉市"
                        },
                        {
                            "name":"贵定县"
                        },
                        {
                            "name":"惠水县"
                        },
                        {
                            "name":"罗甸县"
                        },
                        {
                            "name":"瓮安县"
                        },
                        {
                            "name":"荔波县"
                        },
                        {
                            "name":"龙里县"
                        },
                        {
                            "name":"平塘县"
                        },
                        {
                            "name":"长顺县"
                        },
                        {
                            "name":"独山县"
                        },
                        {
                            "name":"三都水族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"云南省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"昆明市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"盘龙区"
                        },
                        {
                            "name":"五华区"
                        },
                        {
                            "name":"官渡区"
                        },
                        {
                            "name":"西山区"
                        },
                        {
                            "name":"东川区"
                        },
                        {
                            "name":"安宁市"
                        },
                        {
                            "name":"呈贡县"
                        },
                        {
                            "name":"晋宁县"
                        },
                        {
                            "name":"富民县"
                        },
                        {
                            "name":"宜良县"
                        },
                        {
                            "name":"嵩明县"
                        },
                        {
                            "name":"石林彝族自治县"
                        },
                        {
                            "name":"禄劝彝族苗族自治县"
                        },
                        {
                            "name":"寻甸回族彝族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"曲靖市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"麒麟区"
                        },
                        {
                            "name":"宣威市"
                        },
                        {
                            "name":"马龙县"
                        },
                        {
                            "name":"沾益县"
                        },
                        {
                            "name":"富源县"
                        },
                        {
                            "name":"罗平县"
                        },
                        {
                            "name":"师宗县"
                        },
                        {
                            "name":"陆良县"
                        },
                        {
                            "name":"会泽县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"玉溪市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"红塔区"
                        },
                        {
                            "name":"江川县"
                        },
                        {
                            "name":"澄江县"
                        },
                        {
                            "name":"通海县"
                        },
                        {
                            "name":"华宁县"
                        },
                        {
                            "name":"易门县"
                        },
                        {
                            "name":"峨山彝族自治县"
                        },
                        {
                            "name":"新平彝族傣族自治县"
                        },
                        {
                            "name":"元江哈尼族彝族傣族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"保山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"隆阳区"
                        },
                        {
                            "name":"施甸县"
                        },
                        {
                            "name":"腾冲县"
                        },
                        {
                            "name":"龙陵县"
                        },
                        {
                            "name":"昌宁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"昭通市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"昭阳区"
                        },
                        {
                            "name":"鲁甸县"
                        },
                        {
                            "name":"巧家县"
                        },
                        {
                            "name":"盐津县"
                        },
                        {
                            "name":"大关县"
                        },
                        {
                            "name":"永善县"
                        },
                        {
                            "name":"绥江县"
                        },
                        {
                            "name":"镇雄县"
                        },
                        {
                            "name":"彝良县"
                        },
                        {
                            "name":"威信县"
                        },
                        {
                            "name":"水富县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"丽江市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"古城区"
                        },
                        {
                            "name":"永胜县"
                        },
                        {
                            "name":"华坪县"
                        },
                        {
                            "name":"玉龙纳西族自治县"
                        },
                        {
                            "name":"宁蒗彝族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"普洱市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"思茅区"
                        },
                        {
                            "name":"普洱哈尼族彝族自治县"
                        },
                        {
                            "name":"墨江哈尼族自治县"
                        },
                        {
                            "name":"景东彝族自治县"
                        },
                        {
                            "name":"景谷傣族彝族自治县"
                        },
                        {
                            "name":"镇沅彝族哈尼族拉祜族自治县"
                        },
                        {
                            "name":"江城哈尼族彝族自治县"
                        },
                        {
                            "name":"孟连傣族拉祜族佤族自治县"
                        },
                        {
                            "name":"澜沧拉祜族自治县"
                        },
                        {
                            "name":"西盟佤族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"临沧市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"临翔区"
                        },
                        {
                            "name":"凤庆县"
                        },
                        {
                            "name":"云县"
                        },
                        {
                            "name":"永德县"
                        },
                        {
                            "name":"镇康县"
                        },
                        {
                            "name":"双江拉祜族佤族布朗族傣族自治县"
                        },
                        {
                            "name":"耿马傣族佤族自治县"
                        },
                        {
                            "name":"沧源佤族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"德宏傣族景颇族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"潞西市"
                        },
                        {
                            "name":"瑞丽市"
                        },
                        {
                            "name":"梁河县"
                        },
                        {
                            "name":"盈江县"
                        },
                        {
                            "name":"陇川县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"怒江傈僳族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"泸水县"
                        },
                        {
                            "name":"福贡县"
                        },
                        {
                            "name":"贡山独龙族怒族自治县"
                        },
                        {
                            "name":"兰坪白族普米族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"迪庆藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"香格里拉县"
                        },
                        {
                            "name":"德钦县"
                        },
                        {
                            "name":"维西傈僳族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"大理白族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"大理市"
                        },
                        {
                            "name":"祥云县"
                        },
                        {
                            "name":"宾川县"
                        },
                        {
                            "name":"弥渡县"
                        },
                        {
                            "name":"永平县"
                        },
                        {
                            "name":"云龙县"
                        },
                        {
                            "name":"洱源县"
                        },
                        {
                            "name":"剑川县"
                        },
                        {
                            "name":"鹤庆县"
                        },
                        {
                            "name":"漾濞彝族自治县"
                        },
                        {
                            "name":"南涧彝族自治县"
                        },
                        {
                            "name":"巍山彝族回族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"楚雄彝族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"楚雄市"
                        },
                        {
                            "name":"双柏县"
                        },
                        {
                            "name":"牟定县"
                        },
                        {
                            "name":"南华县"
                        },
                        {
                            "name":"姚安县"
                        },
                        {
                            "name":"大姚县"
                        },
                        {
                            "name":"永仁县"
                        },
                        {
                            "name":"元谋县"
                        },
                        {
                            "name":"武定县"
                        },
                        {
                            "name":"禄丰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"红河哈尼族彝族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"蒙自县"
                        },
                        {
                            "name":"个旧市"
                        },
                        {
                            "name":"开远市"
                        },
                        {
                            "name":"绿春县"
                        },
                        {
                            "name":"建水县"
                        },
                        {
                            "name":"石屏县"
                        },
                        {
                            "name":"弥勒县"
                        },
                        {
                            "name":"泸西县"
                        },
                        {
                            "name":"元阳县"
                        },
                        {
                            "name":"红河县"
                        },
                        {
                            "name":"金平苗族瑶族傣族自治县"
                        },
                        {
                            "name":"河口瑶族自治县"
                        },
                        {
                            "name":"屏边苗族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"文山壮族苗族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"文山县"
                        },
                        {
                            "name":"砚山县"
                        },
                        {
                            "name":"西畴县"
                        },
                        {
                            "name":"麻栗坡县"
                        },
                        {
                            "name":"马关县"
                        },
                        {
                            "name":"丘北县"
                        },
                        {
                            "name":"广南县"
                        },
                        {
                            "name":"富宁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"西双版纳傣族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"景洪市"
                        },
                        {
                            "name":"勐海县"
                        },
                        {
                            "name":"勐腊县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"西藏自治区",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"拉萨市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城关区"
                        },
                        {
                            "name":"林周县"
                        },
                        {
                            "name":"当雄县"
                        },
                        {
                            "name":"尼木县"
                        },
                        {
                            "name":"曲水县"
                        },
                        {
                            "name":"堆龙德庆县"
                        },
                        {
                            "name":"达孜县"
                        },
                        {
                            "name":"墨竹工卡县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"那曲地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"那曲县"
                        },
                        {
                            "name":"嘉黎县"
                        },
                        {
                            "name":"比如县"
                        },
                        {
                            "name":"聂荣县"
                        },
                        {
                            "name":"安多县"
                        },
                        {
                            "name":"申扎县"
                        },
                        {
                            "name":"索县"
                        },
                        {
                            "name":"班戈县"
                        },
                        {
                            "name":"巴青县"
                        },
                        {
                            "name":"尼玛县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"昌都地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"昌都县"
                        },
                        {
                            "name":"江达县"
                        },
                        {
                            "name":"贡觉县"
                        },
                        {
                            "name":"类乌齐县"
                        },
                        {
                            "name":"丁青县"
                        },
                        {
                            "name":"察雅县"
                        },
                        {
                            "name":"八宿县"
                        },
                        {
                            "name":"左贡县"
                        },
                        {
                            "name":"芒康县"
                        },
                        {
                            "name":"洛隆县"
                        },
                        {
                            "name":"边坝县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"林芝地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"林芝县"
                        },
                        {
                            "name":"工布江达县"
                        },
                        {
                            "name":"米林县"
                        },
                        {
                            "name":"墨脱县"
                        },
                        {
                            "name":"波密县"
                        },
                        {
                            "name":"察隅县"
                        },
                        {
                            "name":"朗县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"山南地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"乃东县"
                        },
                        {
                            "name":"扎囊县"
                        },
                        {
                            "name":"贡嘎县"
                        },
                        {
                            "name":"桑日县"
                        },
                        {
                            "name":"琼结县"
                        },
                        {
                            "name":"曲松县"
                        },
                        {
                            "name":"措美县"
                        },
                        {
                            "name":"洛扎县"
                        },
                        {
                            "name":"加查县"
                        },
                        {
                            "name":"隆子县"
                        },
                        {
                            "name":"错那县"
                        },
                        {
                            "name":"浪卡子县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"日喀则地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"日喀则市"
                        },
                        {
                            "name":"南木林县"
                        },
                        {
                            "name":"江孜县"
                        },
                        {
                            "name":"定日县"
                        },
                        {
                            "name":"萨迦县"
                        },
                        {
                            "name":"拉孜县"
                        },
                        {
                            "name":"昂仁县"
                        },
                        {
                            "name":"谢通门县"
                        },
                        {
                            "name":"白朗县"
                        },
                        {
                            "name":"仁布县"
                        },
                        {
                            "name":"康马县"
                        },
                        {
                            "name":"定结县"
                        },
                        {
                            "name":"仲巴县"
                        },
                        {
                            "name":"亚东县"
                        },
                        {
                            "name":"吉隆县"
                        },
                        {
                            "name":"聂拉木县"
                        },
                        {
                            "name":"萨嘎县"
                        },
                        {
                            "name":"岗巴县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阿里地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"噶尔县"
                        },
                        {
                            "name":"普兰县"
                        },
                        {
                            "name":"札达县"
                        },
                        {
                            "name":"日土县"
                        },
                        {
                            "name":"革吉县"
                        },
                        {
                            "name":"改则县"
                        },
                        {
                            "name":"措勤县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"海南省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"海口市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"龙华区"
                        },
                        {
                            "name":"秀英区"
                        },
                        {
                            "name":"琼山区"
                        },
                        {
                            "name":"美兰区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"三亚市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"三亚市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"五指山市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"琼海市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"儋州市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"文昌市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"万宁市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"东方市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"澄迈县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"定安县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"屯昌县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"临高县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"白沙黎族自治县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"昌江黎族自治县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"乐东黎族自治县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"陵水黎族自治县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"保亭黎族苗族自治县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"琼中黎族苗族自治县",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"甘肃省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"兰州市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城关区"
                        },
                        {
                            "name":"七里河区"
                        },
                        {
                            "name":"西固区"
                        },
                        {
                            "name":"安宁区"
                        },
                        {
                            "name":"红古区"
                        },
                        {
                            "name":"永登县"
                        },
                        {
                            "name":"皋兰县"
                        },
                        {
                            "name":"榆中县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"嘉峪关市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"嘉峪关市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"金昌市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"金川区"
                        },
                        {
                            "name":"永昌县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"白银市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"白银区"
                        },
                        {
                            "name":"平川区"
                        },
                        {
                            "name":"靖远县"
                        },
                        {
                            "name":"会宁县"
                        },
                        {
                            "name":"景泰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"天水市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"清水县"
                        },
                        {
                            "name":"秦安县"
                        },
                        {
                            "name":"甘谷县"
                        },
                        {
                            "name":"武山县"
                        },
                        {
                            "name":"张家川回族自治县"
                        },
                        {
                            "name":"北道区"
                        },
                        {
                            "name":"秦城区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"武威市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"凉州区"
                        },
                        {
                            "name":"民勤县"
                        },
                        {
                            "name":"古浪县"
                        },
                        {
                            "name":"天祝藏族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"酒泉市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"肃州区"
                        },
                        {
                            "name":"玉门市"
                        },
                        {
                            "name":"敦煌市"
                        },
                        {
                            "name":"金塔县"
                        },
                        {
                            "name":"肃北蒙古族自治县"
                        },
                        {
                            "name":"阿克塞哈萨克族自治县"
                        },
                        {
                            "name":"安西县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"张掖市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"甘州区"
                        },
                        {
                            "name":"民乐县"
                        },
                        {
                            "name":"临泽县"
                        },
                        {
                            "name":"高台县"
                        },
                        {
                            "name":"山丹县"
                        },
                        {
                            "name":"肃南裕固族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"庆阳市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"西峰区"
                        },
                        {
                            "name":"庆城县"
                        },
                        {
                            "name":"环县"
                        },
                        {
                            "name":"华池县"
                        },
                        {
                            "name":"合水县"
                        },
                        {
                            "name":"正宁县"
                        },
                        {
                            "name":"宁县"
                        },
                        {
                            "name":"镇原县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"平凉市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"崆峒区"
                        },
                        {
                            "name":"泾川县"
                        },
                        {
                            "name":"灵台县"
                        },
                        {
                            "name":"崇信县"
                        },
                        {
                            "name":"华亭县"
                        },
                        {
                            "name":"庄浪县"
                        },
                        {
                            "name":"静宁县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"定西市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"安定区"
                        },
                        {
                            "name":"通渭县"
                        },
                        {
                            "name":"临洮县"
                        },
                        {
                            "name":"漳县"
                        },
                        {
                            "name":"岷县"
                        },
                        {
                            "name":"渭源县"
                        },
                        {
                            "name":"陇西县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"陇南市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"武都区"
                        },
                        {
                            "name":"成县"
                        },
                        {
                            "name":"宕昌县"
                        },
                        {
                            "name":"康县"
                        },
                        {
                            "name":"文县"
                        },
                        {
                            "name":"西和县"
                        },
                        {
                            "name":"礼县"
                        },
                        {
                            "name":"两当县"
                        },
                        {
                            "name":"徽县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"临夏回族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"临夏市"
                        },
                        {
                            "name":"临夏县"
                        },
                        {
                            "name":"康乐县"
                        },
                        {
                            "name":"永靖县"
                        },
                        {
                            "name":"广河县"
                        },
                        {
                            "name":"和政县"
                        },
                        {
                            "name":"东乡族自治县"
                        },
                        {
                            "name":"积石山保安族东乡族撒拉族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"甘南藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"合作市"
                        },
                        {
                            "name":"临潭县"
                        },
                        {
                            "name":"卓尼县"
                        },
                        {
                            "name":"舟曲县"
                        },
                        {
                            "name":"迭部县"
                        },
                        {
                            "name":"玛曲县"
                        },
                        {
                            "name":"碌曲县"
                        },
                        {
                            "name":"夏河县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"宁夏回族自治区",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"银川市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"兴庆区"
                        },
                        {
                            "name":"西夏区"
                        },
                        {
                            "name":"金凤区"
                        },
                        {
                            "name":"灵武市"
                        },
                        {
                            "name":"永宁县"
                        },
                        {
                            "name":"贺兰县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"石嘴山市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"大武口区"
                        },
                        {
                            "name":"惠农区"
                        },
                        {
                            "name":"平罗县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"吴忠市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"利通区"
                        },
                        {
                            "name":"青铜峡市"
                        },
                        {
                            "name":"盐池县"
                        },
                        {
                            "name":"同心县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"固原市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"原州区"
                        },
                        {
                            "name":"西吉县"
                        },
                        {
                            "name":"隆德县"
                        },
                        {
                            "name":"泾源县"
                        },
                        {
                            "name":"彭阳县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"中卫市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"沙坡头区"
                        },
                        {
                            "name":"中宁县"
                        },
                        {
                            "name":"海原县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"青海省",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"西宁市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"城中区"
                        },
                        {
                            "name":"城东区"
                        },
                        {
                            "name":"城西区"
                        },
                        {
                            "name":"城北区"
                        },
                        {
                            "name":"湟源县"
                        },
                        {
                            "name":"湟中县"
                        },
                        {
                            "name":"大通回族土族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"海东地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"平安县"
                        },
                        {
                            "name":"乐都县"
                        },
                        {
                            "name":"民和回族土族自治县"
                        },
                        {
                            "name":"互助土族自治县"
                        },
                        {
                            "name":"化隆回族自治县"
                        },
                        {
                            "name":"循化撒拉族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"海北藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"海晏县"
                        },
                        {
                            "name":"祁连县"
                        },
                        {
                            "name":"刚察县"
                        },
                        {
                            "name":"门源回族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"海南藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"共和县"
                        },
                        {
                            "name":"同德县"
                        },
                        {
                            "name":"贵德县"
                        },
                        {
                            "name":"兴海县"
                        },
                        {
                            "name":"贵南县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"黄南藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"同仁县"
                        },
                        {
                            "name":"尖扎县"
                        },
                        {
                            "name":"泽库县"
                        },
                        {
                            "name":"河南蒙古族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"果洛藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"玛沁县"
                        },
                        {
                            "name":"班玛县"
                        },
                        {
                            "name":"甘德县"
                        },
                        {
                            "name":"达日县"
                        },
                        {
                            "name":"久治县"
                        },
                        {
                            "name":"玛多县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"玉树藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"玉树县"
                        },
                        {
                            "name":"杂多县"
                        },
                        {
                            "name":"称多县"
                        },
                        {
                            "name":"治多县"
                        },
                        {
                            "name":"囊谦县"
                        },
                        {
                            "name":"曲麻莱县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"海西蒙古族藏族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"德令哈市"
                        },
                        {
                            "name":"格尔木市"
                        },
                        {
                            "name":"乌兰县"
                        },
                        {
                            "name":"都兰县"
                        },
                        {
                            "name":"天峻县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"新疆维吾尔自治区",
            "sub":[
                {
                    "name":"请选择",
                    "sub":[

                    ]
                },
                {
                    "name":"乌鲁木齐市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"天山区"
                        },
                        {
                            "name":"沙依巴克区"
                        },
                        {
                            "name":"新市区"
                        },
                        {
                            "name":"水磨沟区"
                        },
                        {
                            "name":"头屯河区"
                        },
                        {
                            "name":"达坂城区"
                        },
                        {
                            "name":"东山区"
                        },
                        {
                            "name":"乌鲁木齐县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"克拉玛依市",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"克拉玛依区"
                        },
                        {
                            "name":"独山子区"
                        },
                        {
                            "name":"白碱滩区"
                        },
                        {
                            "name":"乌尔禾区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"吐鲁番地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"吐鲁番市"
                        },
                        {
                            "name":"托克逊县"
                        },
                        {
                            "name":"鄯善县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"哈密地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"哈密市"
                        },
                        {
                            "name":"伊吾县"
                        },
                        {
                            "name":"巴里坤哈萨克自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"和田地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"和田市"
                        },
                        {
                            "name":"和田县"
                        },
                        {
                            "name":"洛浦县"
                        },
                        {
                            "name":"民丰县"
                        },
                        {
                            "name":"皮山县"
                        },
                        {
                            "name":"策勒县"
                        },
                        {
                            "name":"于田县"
                        },
                        {
                            "name":"墨玉县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"阿克苏地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"阿克苏市"
                        },
                        {
                            "name":"温宿县"
                        },
                        {
                            "name":"沙雅县"
                        },
                        {
                            "name":"拜城县"
                        },
                        {
                            "name":"阿瓦提县"
                        },
                        {
                            "name":"库车县"
                        },
                        {
                            "name":"柯坪县"
                        },
                        {
                            "name":"新和县"
                        },
                        {
                            "name":"乌什县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"喀什地区",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"喀什市"
                        },
                        {
                            "name":"巴楚县"
                        },
                        {
                            "name":"泽普县"
                        },
                        {
                            "name":"伽师县"
                        },
                        {
                            "name":"叶城县"
                        },
                        {
                            "name":"岳普湖县"
                        },
                        {
                            "name":"疏勒县"
                        },
                        {
                            "name":"麦盖提县"
                        },
                        {
                            "name":"英吉沙县"
                        },
                        {
                            "name":"莎车县"
                        },
                        {
                            "name":"疏附县"
                        },
                        {
                            "name":"塔什库尔干塔吉克自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"克孜勒苏柯尔克孜自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"阿图什市"
                        },
                        {
                            "name":"阿合奇县"
                        },
                        {
                            "name":"乌恰县"
                        },
                        {
                            "name":"阿克陶县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"巴音郭楞蒙古自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"库尔勒市"
                        },
                        {
                            "name":"和静县"
                        },
                        {
                            "name":"尉犁县"
                        },
                        {
                            "name":"和硕县"
                        },
                        {
                            "name":"且末县"
                        },
                        {
                            "name":"博湖县"
                        },
                        {
                            "name":"轮台县"
                        },
                        {
                            "name":"若羌县"
                        },
                        {
                            "name":"焉耆回族自治县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"昌吉回族自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"昌吉市"
                        },
                        {
                            "name":"阜康市"
                        },
                        {
                            "name":"奇台县"
                        },
                        {
                            "name":"玛纳斯县"
                        },
                        {
                            "name":"吉木萨尔县"
                        },
                        {
                            "name":"呼图壁县"
                        },
                        {
                            "name":"木垒哈萨克自治县"
                        },
                        {
                            "name":"米泉市"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"博尔塔拉蒙古自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"博乐市"
                        },
                        {
                            "name":"精河县"
                        },
                        {
                            "name":"温泉县"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"石河子市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"阿拉尔市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"图木舒克市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"五家渠市",
                    "sub":[

                    ],
                    "type":0
                },
                {
                    "name":"伊犁哈萨克自治州",
                    "sub":[
                        {
                            "name":"请选择"
                        },
                        {
                            "name":"伊宁市"
                        },
                        {
                            "name":"奎屯市"
                        },
                        {
                            "name":"伊宁县"
                        },
                        {
                            "name":"特克斯县"
                        },
                        {
                            "name":"尼勒克县"
                        },
                        {
                            "name":"昭苏县"
                        },
                        {
                            "name":"新源县"
                        },
                        {
                            "name":"霍城县"
                        },
                        {
                            "name":"巩留县"
                        },
                        {
                            "name":"察布查尔锡伯自治县"
                        },
                        {
                            "name":"塔城地区"
                        },
                        {
                            "name":"阿勒泰地区"
                        },
                        {
                            "name":"其他"
                        }
                    ],
                    "type":0
                },
                {
                    "name":"其他"
                }
            ],
            "type":1
        },
        {
            "name":"香港特别行政区",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"中西区"
                },
                {
                    "name":"湾仔区"
                },
                {
                    "name":"东区"
                },
                {
                    "name":"南区"
                },
                {
                    "name":"深水埗区"
                },
                {
                    "name":"油尖旺区"
                },
                {
                    "name":"九龙城区"
                },
                {
                    "name":"黄大仙区"
                },
                {
                    "name":"观塘区"
                },
                {
                    "name":"北区"
                },
                {
                    "name":"大埔区"
                },
                {
                    "name":"沙田区"
                },
                {
                    "name":"西贡区"
                },
                {
                    "name":"元朗区"
                },
                {
                    "name":"屯门区"
                },
                {
                    "name":"荃湾区"
                },
                {
                    "name":"葵青区"
                },
                {
                    "name":"离岛区"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"澳门特别行政区",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"花地玛堂区"
                },
                {
                    "name":"圣安多尼堂区"
                },
                {
                    "name":"大堂区"
                },
                {
                    "name":"望德堂区"
                },
                {
                    "name":"风顺堂区"
                },
                {
                    "name":"嘉模堂区"
                },
                {
                    "name":"圣方济各堂区"
                },
                {
                    "name":"路凼"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"台湾省",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"台北市"
                },
                {
                    "name":"高雄市"
                },
                {
                    "name":"台北县"
                },
                {
                    "name":"桃园县"
                },
                {
                    "name":"新竹县"
                },
                {
                    "name":"苗栗县"
                },
                {
                    "name":"台中县"
                },
                {
                    "name":"彰化县"
                },
                {
                    "name":"南投县"
                },
                {
                    "name":"云林县"
                },
                {
                    "name":"嘉义县"
                },
                {
                    "name":"台南县"
                },
                {
                    "name":"高雄县"
                },
                {
                    "name":"屏东县"
                },
                {
                    "name":"宜兰县"
                },
                {
                    "name":"花莲县"
                },
                {
                    "name":"台东县"
                },
                {
                    "name":"澎湖县"
                },
                {
                    "name":"基隆市"
                },
                {
                    "name":"新竹市"
                },
                {
                    "name":"台中市"
                },
                {
                    "name":"嘉义市"
                },
                {
                    "name":"台南市"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        },
        {
            "name":"海外",
            "sub":[
                {
                    "name":"请选择"
                },
                {
                    "name":"其他"
                }
            ],
            "type":0
        }
    ];

}(Zepto);
// jshint ignore: end

/* jshint unused:false*/

+ function($) {
    "use strict";
    var format = function(data) {
        var result = [];
        for(var i=0;i<data.length;i++) {
            var d = data[i];
            if(d.name === "请选择") continue;
            result.push(d.name);
        }
        if(result.length) return result;
        return [""];
    };

    var sub = function(data) {
        if(!data.sub) return [""];
        return format(data.sub);
    };

    var getCities = function(d) {
        for(var i=0;i< raw.length;i++) {
            if(raw[i].name === d) return sub(raw[i]);
        }
        return [""];
    };

    var getDistricts = function(p, c) {
        for(var i=0;i< raw.length;i++) {
            if(raw[i].name === p) {
                for(var j=0;j< raw[i].sub.length;j++) {
                    if(raw[i].sub[j].name === c) {
                        return sub(raw[i].sub[j]);
                    }
                }
            }
        }
        return [""];
    };

    var raw = $.smConfig.rawCitiesData;
    var provinces = raw.map(function(d) {
        return d.name;
    });
    var initCities = sub(raw[0]);
    var initDistricts = [""];

    var currentProvince = provinces[0];
    var currentCity = initCities[0];
    var currentDistrict = initDistricts[0];

    var t;
    var defaults = {

        cssClass: "city-picker",
        rotateEffect: false,  //为了性能

        onChange: function (picker, values, displayValues) {
            var newProvince = picker.cols[0].value;
            var newCity;
            if(newProvince !== currentProvince) {
                // 如果Province变化，节流以提高reRender性能
                clearTimeout(t);

                t = setTimeout(function(){
                    var newCities = getCities(newProvince);
                    newCity = newCities[0];
                    var newDistricts = getDistricts(newProvince, newCity);
                    picker.cols[1].replaceValues(newCities);
                    picker.cols[2].replaceValues(newDistricts);
                    currentProvince = newProvince;
                    currentCity = newCity;
                    picker.updateValue();
                }, 200);
                return;
            }
            newCity = picker.cols[1].value;
            if(newCity !== currentCity) {
                picker.cols[2].replaceValues(getDistricts(newProvince, newCity));
                currentCity = newCity;
                picker.updateValue();
            }
        },

        cols: [
            {
                textAlign: 'center',
                values: provinces,
                cssClass: "col-province"
            },
            {
                textAlign: 'center',
                values: initCities,
                cssClass: "col-city"
            },
            {
                textAlign: 'center',
                values: initDistricts,
                cssClass: "col-district"
            }
        ]
    };

    $.fn.cityPicker = function(params) {
        return this.each(function() {
            if(!this) return;
            var p = $.extend(defaults, params);
            //计算value
            if (p.value) {
                $(this).val(p.value.join(' '));
            } else {
                var val = $(this).val();
                val && (p.value = val.split(' '));
            }

            if (p.value) {
                //p.value = val.split(" ");
                if(p.value[0]) {
                    currentProvince = p.value[0];
                    p.cols[1].values = getCities(p.value[0]);
                }
                if(p.value[1]) {
                    currentCity = p.value[1];
                    p.cols[2].values = getDistricts(p.value[0], p.value[1]);
                } else {
                    p.cols[2].values = getDistricts(p.value[0], p.cols[1].values[0]);
                }
                !p.value[2] && (p.value[2] = '');
                currentDistrict = p.value[2];
            }
            $(this).picker(p);
        });
    };

}(Zepto);

$(document).on("pageInit","#page-pai_podcast-goods,#page-pai_user-goods", function(e, pageId, $page) {
	// 下拉刷新
	if(pageId == 'page-pai_user-goods'){
		var pull_to_refresh_url = TMPL+"index.php?ctl=pai_user&act=goods&is_true="+is_true;
	    var $content = $($page).find(".content").on('refresh', function(e) {
	    	refresh(pull_to_refresh_url,pageId,"#refresh-layer",$content,function(){
	    		// 倒计时
			    $(".left_time").each(function(){
			    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
			    	left_time(leftTime,$(this));
			    });

			    // 初始化分页数据
				p = 2;
				has_next = $("input[name='has_next']").val();
	    	});
	    });
	}
	if(pageId == 'page-pai_podcast-goods'){
		var pull_to_refresh_url = TMPL+"index.php?ctl=pai_podcast&act=goods&is_true="+is_true;
	    var $content = $($page).find(".content").on('refresh', function(e) {
	    	refresh(pull_to_refresh_url,pageId,"#refresh-layer",$content,function(){
	    		// 倒计时
			    $(".left_time").each(function(){
			    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
			    	left_time(leftTime,$(this));
			    });

			    // 初始化分页数据
				p = 2;
				has_next = $("input[name='has_next']").val();
	    	});
	    });
	}
});




$(document).on("pageInit","#page-pai_podcast-goods,#page-pai_user-goods", function(e, pageId, $page) {
    // 无限加载
    var page_ajax_url;
	switch (pageId)
	{
		case 'page-pai_podcast-goods':
			page_ajax_url = TMPL+"index.php?ctl=pai_podcast&act=goods&post_type=json&ajax=1&is_true="+is_true;
	  		break;
	  	case 'page-pai_user-goods':
			page_ajax_url = TMPL+"index.php?ctl=pai_user&act=goods&post_type=json&ajax=1&is_true="+is_true;
	  		break;
	}
    pai_infinite_scroll($page,page_ajax_url);
});

function pai_infinite_scroll($page,page_ajax_url,func) {

	var loading=false;
	$($page).on('infinite', function() {
 	 	if (loading || !has_next){
 	 		if(!has_next){
				$(".infinite-scroll-preloader").addClass("data-null").html('<span style="color:#999;font-size:0.75rem;">无更多数据</span>').show();
 	 		}
 	 		$(".content-inner").css({paddingBottom:"0"});
 			return;
 	 	}
      	loading = true;
      	$.ajax({
	      	url:page_ajax_url,
	      	dataType: "html",
	        data:{p:p},
	        async:false,
	        success:function(data){
	        	var data = JSON.parse(data);
	        	has_next = data.is_has;
	        	p++;
	        	// page_ajax_url = data.page_ajax_url;
	        	setTimeout(function() {
	        		loading = false;
	        		$($page).find("#infinite_scroll_box").append(data.html);
	        		$.refreshScroller();

         		}, 300);
         		if(func!=null){
	            	func();
		        }
	        }
      	});
      	return false;
    });
}
// 收入
$(document).on("pageInit","#user_center-income", function(e, pageId, $page) {
	var nowyer=year;
	var income_type = GetQueryString("type");
	//var displayyear=new Array('2010年', '2011年', '2012年', '2013年', '2014年', '2015年', '2016年', '2017年')
	$("#Date").picker({
	  	toolbarTemplate: '<header class="bar bar-nav">\
	  	<button class="button button-link pull-right close-picker">确定</button>\
	  	<h1 class="title">请选择年份月份</h1>\
	  	</header>',
	  	onClose:function(){
	  		var val=$("#Date").val();
	  		var date=val.split(" ");	
	  		$("#year").text(date[0]);
	  		$("#month").text(date[1]);

	  		yes_counted_url = APP_ROOT+"/wap/index.php?ctl=user_center&act=income&type=0&year="+date[0]+"&month="+date[1];
	  		no_counted_url = APP_ROOT+"/wap/index.php?ctl=user_center&act=income&type=1&year="+date[0]+"&month="+date[1];
    		//location.href= tmpl+"index.php?ctl=user_center&act=income&year="+date[0]+"&month="+date[1];
    		$.ajax({
				url:APP_ROOT+"/wap/index.php?ctl=user_center&act=income&year="+date[0]+"&month="+date[1]+"&type="+income_type,
				type:"post",
				dataType:"html",
				success:function(result){
					$(".content").find(".incomelist").html($(result).find(".content").find(".incomelist").html());
				}
			});
  		},
	  	cols: [
		    {
		      	textAlign: 'center',
		      	//如果你希望显示文案和实际值不同，可以在这里加一个displayValues: [.....]
		      	displayValues: [nowyer-5+'年',nowyer-4+'年',nowyer-3+'年',nowyer-2+'年',nowyer-1+'年',nowyer+'年'],
		      	values: [nowyer-5,nowyer-4,nowyer-3,nowyer-2,nowyer-1,nowyer]
		    },
		    {
		      	textAlign: 'center',
		      	displayValues: ['1月', '2月', '3月', '4月', '5月', '6月', '7月','8月','9月','10月','11月','12月'],
		      	values: ['1', '2', '3', '4', '5', '6', '7','8','9','10','11','12']
		    }
	  	]
	});
 	$(".J-view-income").on('click',function(){
        var iscounted = Number($(this).attr("data-iscounted"));
        iscounted ? location.href = yes_counted_url : location.href = no_counted_url;
    });
});


// 收入
$(document).on("pageInit","#user_center-goods_income_details", function(e, pageId, $page) {
	var nowyer=year;
	var income_type = GetQueryString("type");
	//var displayyear=new Array('2010年', '2011年', '2012年', '2013年', '2014年', '2015年', '2016年', '2017年')
	$("#Date").picker({
	  	toolbarTemplate: '<header class="bar bar-nav">\
	  	<button class="button button-link pull-right close-picker">确定</button>\
	  	<h1 class="title">请选择年份月份</h1>\
	  	</header>',
	  	onClose:function(){
	  		var val=$("#Date").val();
	  		var date=val.split(" ");	
	  		$("#year").text(date[0]);
	  		$("#month").text(date[1]);

	  		yes_goods_url = APP_ROOT+"/wap/index.php?ctl=user_center&act=goods_income_details&type=1&year="+date[0]+"&month="+date[1];
	  		no_goods_url = APP_ROOT+"/wap/index.php?ctl=user_center&act=goods_income_details&type=2&year="+date[0]+"&month="+date[1];
	  		invalid_goods_url = APP_ROOT+"/wap/index.php?ctl=user_center&act=goods_income_details&type=3&year="+date[0]+"&month="+date[1];
    		//location.href= tmpl+"index.php?ctl=user_center&act=income&year="+date[0]+"&month="+date[1];
    		$.ajax({
				url:APP_ROOT+"/wap/index.php?ctl=user_center&act=goods_income_details&year="+date[0]+"&month="+date[1]+"&type="+income_type,
				type:"post",
				dataType:"html",
				success:function(result){
					$(".content").find(".incomelist").html($(result).find(".content").find(".incomelist").html());
				}
			});
  		},
	  	cols: [
		    {
		      	textAlign: 'center',
		      	//如果你希望显示文案和实际值不同，可以在这里加一个displayValues: [.....]
		      	displayValues: [nowyer-5+'年',nowyer-4+'年',nowyer-3+'年',nowyer-2+'年',nowyer-1+'年',nowyer+'年'],
		      	values: [nowyer-5,nowyer-4,nowyer-3,nowyer-2,nowyer-1,nowyer]
		    },
		    {
		      	textAlign: 'center',
		      	displayValues: ['1月', '2月', '3月', '4月', '5月', '6月', '7月','8月','9月','10月','11月','12月'],
		      	values: ['1', '2', '3', '4', '5', '6', '7','8','9','10','11','12']
		    }
	  	]
	});
 	$(".J-view-goods").on('click',function(){
        var iscounted = Number($(this).attr("data-iscounted"));
        if(iscounted == 1){
        	location.href = yes_goods_url;
        }
        else if(iscounted == 2){
    		location.href = no_goods_url;
        }
        else{
        	location.href = invalid_goods_url;
        }
    });
});

// 分享页面
$(document).on("pageInit","#page-share-index", function(e, pageId, $page) {
	// 点击弹出下载提示窗
	$(".show_pop_wp").on('click',function(){
		$(".pop_wp").css({display:"flex"});
	});
	$(".pop_close").on('click',function(){
		$(".pop_wp").css({display:"none"});
	});
   	var width = $(window).width();
    var height = $(window).height();
    console.log("live_in:"+live_in);
	if(live_in==1){
        if(live_url || live_url2){
            (function () {
		        var player = new qcVideo.Player("id_video_container", {
		            "live_url": live_url,
                    "live_url2": live_url2,
                    "width": width,
                    "height": 320,
                    "h5_start_patch":{
                    	"url": head_image_url,
                    	"stretch": true
                    }
		        },	{
				    playStatus: function (status,type){
				        //TODO
				        console.log(status);
				        if(status == "playing"){
				        	player.resize(width, height);
				        	if(!device || device=='iphone'){
				        		$(".live_info").show();
				        		$(".pop_download").hide();
				        	}
				        }
				        else{
				        	player.resize(width, 320);
				        }
				    }
				});
			 	$("#startplay").on('click',function(){
	        		$("#liveing").show();
	        		$("#preVedio").hide();
			    	player.play();
			    });
		    })();
        }else{
            (function () {
		        var player = new qcVideo.Player("id_video_container", {
		           	"channel_id": channel_id,
                    "app_id": app_id,
                    "width": width,
                    "height": 320,
                    "h5_start_patch":{
                    	"url": head_image_url,
                    	"stretch": true
                    }
		        }, {
				    playStatus: function (status){
			         	//TODO
				        console.log(status);
				        if(status == "playing"){
				        	player.resize(width, height);
				        	if(!device || device=='iphone'){
				        		$(".live_info").show();
				        		$(".pop_download").hide();
				        	}
				        }
				        else{
				        	player.resize(width, 320);
				        }
				    }
				});
				$("#startplay").on('click',function(){
					$("#liveing").show();
	        		$("#preVedio").hide();
			    	player.play();
			    });
		    })();
        }
	}else if(live_in==3){
        (function () {
	        var player = new qcVideo.Player("id_video_container", {
	           	"file_id": file_id,
	            "app_id": app_id,
	            "width":width,
	            "height":320
	        }, {
				    playStatus: function (status,type){
				        //TODO
				        console.log(status);
				        if(status == "playing"){
				        	player.resize(width, height);
				        	if(!device || device=='iphone'){
				        		$(".live_info").show();
				        		$(".pop_download").hide();
				        	}
				        }
				        else{
				        	player.resize(width, 320);
				        }
				    }
				});
	        	$("#startplay").on('click',function(){
	        		$("#liveing").show();
	        		$("#preVedio").hide();
			    	player.play();
			    });
	    })();

        /*var player = new qcVideo.Player("id_video_container", {
            "width":width,
            "height":height,
            "stretch_full":1,
            "stop_time":60,
            "third_video": {
                "urls":{
                    20 : urls//演示地址，请替换实际地址
                }
            }
        });*/
    }else{

    }

	wx.ready(function () {
		// 在这里调用 API
		wx.onMenuShareTimeline({
			title: wx_title, // 分享标题
			link: wx_link, // 分享链接
			imgUrl: wx_img, // 分享图标
			success: function () {
				// 用户确认分享后执行的回调函数
			},
			cancel: function () {
				// 用户取消分享后执行的回调函数
			}
		});
		wx.onMenuShareAppMessage({
			title: wx_title, // 分享标题
			desc: wx_desc, // 分享描述
			link: wx_link,  // 分享链接
			imgUrl: wx_img, // 分享图标
			type: 'link', // 分享类型,music、video或link，不填默认为link
			// dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () {
				// 用户确认分享后执行的回调函数
			},
			cancel: function () {
				// 用户取消分享后执行的回调函数
			}
		});

        wx.onMenuShareQQ({
            title: wx_title, // 分享标题
            desc: wx_desc, // 分享描述
            link: wx_link, // 分享链接
            imgUrl: wx_img, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });


        wx.onMenuShareQZone({
            title: wx_title, // 分享标题
            desc: wx_desc, // 分享描述
            link: wx_link, // 分享链接
            imgUrl: wx_img, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
		wx.error(function(res){
			// config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
		});
	});

	function addMsg(msg) {
		var time = webim.Tool.formatTimeStamp(msg.getTime());
		var data = convertMsg(msg);
		if(! data){
			return;
		}

		if(typeof data !== 'object'){
			data = {
				"user_level": 122,
				"nick_name": "[群提示消息]",
				"text": data,
			};
		}

		if (data.type == 2 && showBarrage && player) {
			var barrage = [
				{ "type": "content", "content": data.text, "time": "0" },
			];
			player.addBarrage(barrage);
		}

		$('#chat-box').append('<li><p><a href="javascript:;" class="name"><i class="ico_level"></i>' + data.nick_name + '</a>' + data.text + '</p></li>');
	}

	var listeners = {
        loginSuccess: function () {
            im_message.applyJoinBigGroup(avChatRoomId);
        },
        recieveGroupMsg: function (newMsgList) {
            for (var j in newMsgList) {//遍历新消息
                var newMsg = newMsgList[j];
                addMsg(newMsg);
            }
			
			var el = $('#video_sms_list');
            el.scrollTop(el.prop("scrollHeight"));
        },
        sendMsgOk: function (msg) {
            $('#input-chat-speak').val('');
        },
    };
	if(typeof loginInfo !== 'undefined'){
		im_message.init(loginInfo, listeners);
	}
});

$(document).on("pageInit","#page-pai_podcast-goods,#page-pai_podcast-order,#page-pai_user-goods,#page-pai_user-order", function(e, pageId, $page) {

	// 查看物流
	$(document).on('click', '.J-view_express',function(e) {
		$.alert($(this).attr("data-express"));
	});

	if(pageId == 'page-pai_podcast-goods' || pageId == 'page-pai_user-goods'){
		var ajax_url;
		pageId == 'page-pai_podcast-goods' ? ajax_url=TMPL+'index.php?ctl=pai_podcast&act=goods&post_type=json&is_true='+is_true : ajax_url=TMPL+'index.php?ctl=pai_user&act=goods&post_type=json&is_true='+is_true;
		$.ajax({
            url: ajax_url,
            data: {},
            type: 'POST',
            dataType: 'json',
            success:function(data){
            	if(data.status == 1){
            		var data_list = data.data.list;
            		var data_leftTime;
            		for (var i = 0; i < data_list.length; i++) {
	            		data_list[i].status == 0 ? data_leftTime = data_list[i].pai_left_time : data_leftTime = data_list[i].expire_time;
	            		$(".card-footer").eq(i).find(".left_time").attr("data-leftTime",data_leftTime);
	            		console.log(data_list[i].expire_time);
	            	};
            		$(".left_time").each(function(){
				    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
				    	left_time(leftTime,$(this));
				    });
            	}
            	else{
            		$.toast(data.error,1000);
            	}
            },
            error:function(){
            	$.hideIndicator();
		       	$.toast('请求失败，请检查网络',1000);
		    }

        });
	}
	else{
	 	// 倒计时
	    $(".left_time").each(function(){
	    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
	    	left_time(leftTime,$(this));
	    });
	}
   
    // 监听设备处于锁屏或者浏览器/页面处于后台运行状态
 	document.addEventListener("visibilitychange", function (e) {
 		var reload_ajax_url;
		switch (pageId)
		{
			case 'page-pai_podcast-goods':
			  reload_ajax_url = TMPL+"index.php?ctl=pai_podcast&act=goods&post_type=json&page_size=99999999999&is_true="+is_true;
			  break;
			case 'page-pai_user-goods':
			  reload_ajax_url = TMPL+"index.php?ctl=pai_user&act=goods&post_type=json&page_size=99999999999&is_true="+is_true;
			  break;
			case 'page-pai_podcast-order':
			  var order_sn,pai_id;
			  GetQueryString("order_sn") ? order_sn = GetQueryString("order_sn") : '';
			  GetQueryString("pai_id") ? pai_id = GetQueryString("pai_id") : '';
			  reload_ajax_url = TMPL+"index.php?ctl=pai_podcast&act=order&post_type=json&is_true="+is_true+"&order_sn="+order_sn+"&pai_id="+pai_id;
			  break;
			case 'page-pai_user-order':
		      var order_sn,pai_id;
			  GetQueryString("order_sn") ? order_sn = GetQueryString("order_sn") : '';
			  GetQueryString("pai_id") ? pai_id = GetQueryString("pai_id") : '';
			  reload_ajax_url = TMPL+"index.php?ctl=pai_user&act=order&post_type=json&is_true="+is_true+"&order_sn="+order_sn+"&pai_id="+pai_id;
			  break;
		}
        if(!e.path[0].hidden){ // e.path为页面中document的集合
            $.ajax({
	            url: reload_ajax_url,
	            data: {},
	            type: 'POST',
	            dataType: 'json',
	            beforeSend:function(){
	                $.showIndicator();
	            },
	            success:function(data){
            		$.hideIndicator();
	            	if(data.status == 1){
		            	if(pageId == 'page-pai_podcast-goods' || pageId == 'page-pai_user-goods'){
		            		var data_list = data.data.list;
		            		for (var i = 0; i < data_list.length; i++) {
			            		$(".card-footer").eq(i).find(".left_time").attr("data-leftTime",data_list[i].expire_time);
			            		console.log(data_list[i].expire_time);
			            	};
		            		$(".left_time").each(function(){
						    	var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
						    	left_time(leftTime,$(this));
						    });
		            	}
		            	else{
		            		var data_list = data.data;
		            	 	$(".left_time").each(function(){
						    	left_time(data_list.expire_time,$(this));
						    });
	            	 	}
	            	}
	            	else{
	            		$.toast(data.error,1000);
	            	}
	            },
	            error:function(){
	            	$.hideIndicator();
			       	$.toast('请求失败，请检查网络',1000);
			    }

	        });
        } 
    }, false);
    
    // 充值
    $(".J_recharge").on('click',function(){
    	var json = new Object();
        json.android_page = 'com.fanwe.live.activity.LiveRechargeActivity';
        json.ios_page = 'chargerViewController';
        json = JSON.stringify(json);
        App.start_app_page(json);
    });

    // 继续参拍
	$(document).on('click', '.J-join_live',function(e) {
		var pai_id = $(this).attr('data-id');
		$.ajax({
			url:TMPL+"index.php?ctl=pai_user&act=go_video&post_type=json&itype=shop&videoType=1&pai_id="+pai_id,
			type:"post",
			dataType:"html",
			beforeSend:function(){
				$.showIndicator();
			},
			success:function(result){
				$.hideIndicator();
				result = JSON.parse(result);
				var roomId = result.roomId;
				var groupId = result.groupId;
				var createrId = result.createrId;
				var loadingVideoImageUrl = result.loadingVideoImageUrl;
				var videoType=1;
				if(result.status == 1){
					if(roomId>0){
						var json = new Object(); json.roomId = roomId.toString(),json.videoType=videoType.toString(), json.groupId = groupId.toString(), json.createrId = createrId.toString(), json.loadingVideoImageUrl = loadingVideoImageUrl.toString(), json = JSON.stringify(json);
						App.join_live(json);
					}
					else{
						$.toast("请求失败，房间已关闭");
						return false;
					}
				}
				else{
					$.toast(result.error ? result.error : '操作失败');
					return false;
				}
			},
			error:function(){
				$.hideIndicator();
				$.toast("请求失败，请检查网络");
			}
		});
	});
   	
 	// 观众端
    if(pageId == 'page-pai_user-goods' || pageId == 'page-pai_user-order'){

	    // 进入竞拍详情（未生成订单）
	    $(document).on('click', '.J-pai_live',function(e) {
	    	var id = $(this).attr("data-id");
	    	var json = new Object();
	        json.android_page = 'com.fanwe.auction.activity.AuctionGoodsDetailActivity';
	        json.ios_page = 'detailViewController';
	        json.data = new Object();
	        json.data.id = id;
	        json.data.is_anchor = 0;
	        json = JSON.stringify(json);
	        App.start_app_page(json);
	    });
	    // 提醒约会
	    $(document).on('click', '.J-remind_podcast_to_date',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn')), to_podcast_id = Number($(this).attr('data-to_podcast_id'));
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=remind_podcast_to_date",{order_sn:order_sn, to_podcast_id:to_podcast_id}).done(function(resp){
	            $.toast("已成功提醒",1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
	    });

	    // 提醒主播确认约会
	    $(document).on('click', '.J-remind_podcast_to_confirm_date',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn')), to_podcast_id = Number($(this).attr('data-to_podcast_id'));
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=remind_podcast_to_confirm_date",{order_sn:order_sn, to_podcast_id:to_podcast_id}).done(function(resp){
				$.toast("已成功提醒",1000);
				//$.toast(resp,1000);
	            //setTimeout(function(){
	            //    location.reload();
	            //    $.showPreloader();
	            //},1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
	    });
	    
	    // 确认约会
	    $(document).on('click', '.J-buyer_confirm_date',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn')), to_podcast_id = Number($(this).attr('data-to_podcast_id')), confirm_tip = $(this).attr('data-confirm-tip');
	    	$.confirm(confirm_tip,
		        function () {
		        	handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=buyer_confirm_date",{order_sn:order_sn, to_podcast_id:to_podcast_id}).done(function(resp){
			           is_true==1 ? $.toast("确认收货成功",1000) : $.toast("确认约会成功",1000);
			            setTimeout(function(){
			                location.reload();
			                $.showPreloader();
			            },1000);
			        }).fail(function(err){
			            $.toast(err,1000);
			        });
		        }
	      	);
	    });
	    // 申请退款（我要投诉）
	    $(document).on('click', '.J-buyer_to_complaint',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn')), to_podcast_id = Number($(this).attr('data-to_podcast_id'));    		
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=buyer_to_complaint",{order_sn:order_sn, to_podcast_id:to_podcast_id}).done(function(resp){
	            $.toast("已成功提交<br/>请等待客服联系",1000);
	            setTimeout(function(){
	                location.reload();
	                $.showPreloader();
	            },1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
	    });

	    // 主动撤销退款
	    $(document).on('click', '#J-oreder_revocation',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn')), to_podcast_id = Number($(this).attr('data-to_podcast_id'));    		
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=oreder_revocation",{order_sn:order_sn, to_podcast_id:to_podcast_id}).done(function(resp){
	            $.toast(resp,1000);
	            setTimeout(function(){
	                location.reload();
	                $.showPreloader();
	            },1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
	    });

	    // 付款
	    $(document).on('click', '.J-pay_diamonds',function(e) {
	    	if($('#pay_balance').is(':checked')) {
			    var order_sn = Number($(this).attr('data-order_sn'));
		    	var pai_id = Number($(this).attr('data-pai_id'));
	    		handleAjax.handle(TMPL+"index.php?ctl=pai_user&act=pay_diamonds",{order_sn:order_sn}).done(function(resp){
		            $.toast(resp,1000);
		            setTimeout(function(){
		                location.href = APP_ROOT+"/index.php?ctl=pai_user&act=order&order_sn="+order_sn+"&pai_id="+pai_id;
		                $.showPreloader();
		            },1000);
		        }).fail(function(err){
		            $.toast(err,1000);
		        });
			}
			else{
				$.toast("请选择支付方式");
				return false;
			}
	    });

	    // 买家要求退货
	    $(document).on('click', '#J-buyer_confirm_to_refund',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn')), to_podcast_id = Number($(this).attr('data-to_podcast_id')); 
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=buyer_confirm_to_refund",{order_sn:order_sn, to_podcast_id:to_podcast_id}).done(function(resp){
	            $.toast(resp,1000);
	            setTimeout(function(){
	                location.reload();
	                $.showPreloader();
	            },1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
	    });

	    // 联系卖家
	    $(document).on('click', '#J-link',function(e){
	    	$.alert($(this).attr("data-link"));
	    });
    }

    // 主播端
    if(pageId == 'page-pai_podcast-goods' || pageId == 'page-pai_podcast-order'){
    	// 主播端提醒买家付款
    	$(document).on('click', '.J-remind_buyer_pay',function(e) {
    		var order_sn = Number($(this).attr('data-order_sn')), to_buyer_id = Number($(this).attr('data-to_buyer_id'));
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=remind_buyer_pay",{order_sn:order_sn, to_buyer_id:to_buyer_id}).done(function(resp){
	            $.toast(resp,1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
    	});
    	// 提醒买家约会
    	$(document).on('click', '.J-remind_buyer_to_date',function(e) {
    		var order_sn = Number($(this).attr('data-order_sn')), to_buyer_id = Number($(this).attr('data-to_buyer_id'));
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=remind_buyer_to_date",{order_sn:order_sn, to_buyer_id:to_buyer_id}).done(function(resp){
	            $.toast(resp,1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
    	});
    	// 确认完成约会
    	$(document).on('click', '.J-confirm_virtual_auction',function(e) {
    		var order_sn = Number($(this).attr('data-order_sn')), to_buyer_id = Number($(this).attr('data-to_buyer_id')), confirm_tip = $(this).attr('data-confirm-tip');
    		$.confirm(confirm_tip,function(){
    			handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=confirm_virtual_auction",{order_sn:order_sn, to_buyer_id:to_buyer_id}).done(function(resp){
		            $.toast(resp,1000);
		            setTimeout(function(){
		               	location.reload();
		                $.showPreloader();
		            },1000);
		        }).fail(function(err){
		            $.toast(err,1000);
		        });
    		});
    	});
    	// 提醒买家确认完成约会
    	$(document).on('click', '.J-remind_buyer_receive',function(e) {
    		var order_sn = Number($(this).attr('data-order_sn')), to_buyer_id = Number($(this).attr('data-to_buyer_id'));   	
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=remind_buyer_receive",{order_sn:order_sn, to_buyer_id:to_buyer_id}).done(function(resp){
	            $.toast(resp,1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
    	});
    	// 进入竞拍详情（未生成订单）
    	$(document).on('click', '.J-pai_live',function(e) {
	    	var id = $(this).attr("data-id");
	    	var json = new Object();
	        json.android_page = 'com.fanwe.auction.activity.AuctionGoodsDetailActivity';
	        json.ios_page = 'detailViewController';
	        json.data = new Object();
	        json.data.id = id;
	        json.data.is_anchor = 1;
	        json = JSON.stringify(json);
	        App.start_app_page(json);
	    });

	    // 同意退款（确认收取退货）
    	$(document).on('click', '#J-return_virtual_pai',function(e) {
    		var order_sn = Number($(this).attr('data-order_sn')), to_buyer_id = Number($(this).attr('data-to_buyer_id'));   	
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=return_virtual_pai",{order_sn:order_sn, to_buyer_id:to_buyer_id}).done(function(resp){
	            $.toast(resp,1000);
	            setTimeout(function(){
	                location.reload();
	                $.showPreloader();
	            },1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
    	});

    	// 申请售后
    	$(document).on('click', '#J-complaint_virtual_goods',function(e) {
    		var order_sn = Number($(this).attr('data-order_sn')), to_buyer_id = Number($(this).attr('data-to_buyer_id'));   	
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=complaint_virtual_goods",{order_sn:order_sn, to_buyer_id:to_buyer_id}).done(function(resp){
	            $.toast(resp,1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
    	});

	 	// 提醒卖家发货
	    $(document).on('click', '.J-remind_seller_delivery',function(e) {
	    	var order_sn = Number($(this).attr('data-order_sn'));
    		handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=remind_seller_delivery",{order_sn:order_sn}).done(function(resp){
	            $.toast("已成功提醒",1000);
	        }).fail(function(err){
	            $.toast(err,1000);
	        });
	    });
    }
});

$(document).on("pageInit","#page-pai_user-virtual_order_details", function(e, pageId, $page) {
	// 充值
    $(".J_recharge").on('click',function(){
        var json = new Object();
        json.android_page = 'com.fanwe.live.activity.LiveRechargeDiamondsActivity';
        json.ios_page = 'chargerViewController';
        json = JSON.stringify(json);
        App.start_app_page(json);
    });

    // 付款
    $('.J-pay_diamonds').on('click',function(e) {
        var order_type = $(this).attr("data-ordertype");
        switch(order_type){
        case "h5shop":
			if($("input[name='pay-money']:checked").val()) {
				var order_sn = Number($(this).attr('data-order_sn'));
				var order_id = Number($(this).attr('data-order_id'));
				var pay_id = $("input[name='pay-money']:checked").val();
				var purchase_type = Number($(this).attr('data-purchase_type'));
				$.ajax({
					url: TMPL+"/wap/index.php?ctl=pay&act=shop_h5_pay&post_type=json",
					data: {order_id:order_id, purchase_type:purchase_type,order_sn:order_sn,pay_id:pay_id,shop_info:shop_info},
					type: 'POST',
					dataType: 'json',
					success:function(data){
						if(data.status == 1){
							try{
								App.pay_sdk(JSON.stringify(data.pay.sdk_code));
								return false;
							}
							catch(e){
								$.toast("SDK调用失败");
								return false;
							}
						}else{
							$.hideIndicator();
							$.toast(data.error,1000);
						}
					},
					error:function(){
						$.hideIndicator();
						$.toast('请求失败，请检查网络',1000);
					}
				});
			}
            else{
                $.toast("请选择支付方式");
                return false;
            }
            break;
		case "to_podcast":
			if($("input[name='pay-money']:checked").val()) {
				var order_sn = Number($(this).attr('data-order_sn'));
				var order_id = Number($(this).attr('data-order_id'));
				var pay_id = $("input[name='pay-money']:checked").val();
				var purchase_type = Number($(this).attr('data-purchase_type'));
				$.ajax({
					url: TMPL+"/wap/index.php?ctl=pay&act=shop_h5_pay&post_type=json",
					data: {order_id:order_id, purchase_type:purchase_type,order_sn:order_sn,pay_id:pay_id,shop_info:shop_info},
					type: 'POST',
					dataType: 'json',
					success:function(data){
						if(data.status == 1){
							try{
								App.pay_sdk(JSON.stringify(data.pay.sdk_code));
								return false;
							}
							catch(e){
								$.toast("SDK调用失败");
								return false;
							}
						}else{
							$.hideIndicator();
							$.toast(data.error,1000);
						}
					},
					error:function(){
						$.hideIndicator();
						$.toast('请求失败，请检查网络',1000);
					}
				});
			}
			else{
				$.toast("请选择支付方式");
				return false;
			}
			break;
        default:
			if($('#pay_balance').is(':checked')){
                var order_sn = Number($(this).attr('data-order_sn'));
                var pai_id = Number($(this).attr('data-pai_id'));
				$.ajax({
					url: TMPL+"index.php?ctl=pai_user&act=pay_diamonds&post_type=json&itype=shop&order_sn="+order_sn,
					data: '',
					type: 'POST',
					dataType: 'json',
					beforeSend:function(){
						$.showIndicator();
					},
					success:function(resp){
						$.hideIndicator();
						$.toast(resp.error,1000);
						setTimeout(function(){
							if(resp.status != 1){
								$.toast(resp.error,1000);
							}else{
								window.location.href = TMPL+"index.php?ctl=pai_user&act=order&order_sn="+resp.order_sn+"&pai_id="+pai_id;
								$.showPreloader();
							}
						},1000);
					},
					error:function(){
						$.hideIndicator();
						$.toast("请求出错",1000);
					}
				});
            }
            else{
                $.toast("请选择支付方式");
                return false;
            }
            break;
        }
    });
});

// 我的等级
$(document).on("pageInit","#page-user_center-grade", function(e, pageId, $page) {
    up_score == '满级' ? document.getElementById('grade_progress').style.width = '100%' : document.getElementById('grade_progress').style.width = ((u_score/up_score)*100).toFixed(2)+'%';
	up_ticket == '满级' ? document.getElementById('anchor_grade_progress').style.width = '100%' : document.getElementById('anchor_grade_progress').style.width = ((u_ticket/up_ticket)*100).toFixed(2)+'%';
});

// 支付结果
$(document).on("pageInit","#page-pay_success", function(e, pageId, $page) {
 	// 继续参拍
    $(document).on('click', '.J-join_live',function(e) {
		// App.join_live(data_json);
		App.js_shopping_comeback_live_app();
   	});
});

//sdk支付回调
function js_pay_sdk(status){
	if(status == 1){
		window.location.href = TMPL+'/wap/index.php?ctl=shop&act=shop_order&page=1';
		$.showPreloader();
		//App.js_shopping_comeback_live_app();
	}
}
// 分享页面
$(document).on("pageInit","#page-share-indexs", function(e, pageId, $page) {
	// 点击弹出下载提示窗
	$(".show_pop_wp").on('click',function(){
		$(".pop_wp").css({display:"flex"});
	});
	$(".pop_close").on('click',function(){
		$(".pop_wp").css({display:"none"});
	});
   	var width = $(window).width();
    var height = $(window).height();
    console.log("live_in:"+live_in);
	if(live_in==1){
        if(live_url || live_url2){
            (function () {
            	if(!device || device=='iphone'){
            		var option ={
				     	"live_url": live_url,
	                    "live_url2": live_url2,
	                    "width": width,
	                    "height": 320,
	                    "x5_type": "h5",
	                    "x5_fullscreen": true,
	                    "h5_start_patch":{
	                    	"url": head_image_url,
	                    	"stretch": true
	                    }
					};
            	}
            	else{
            		var option ={
				     	"live_url": live_url,
	                    "live_url2": live_url2,
	                    "width": width,
	                    "height": 320,
	                    "h5_start_patch":{
	                    	"url": head_image_url,
	                    	"stretch": true
	                    }
					};
            	}
		        var player = new qcVideo.Player("id_video_container", option, {
				    playStatus: function (status,type){
				        //TODO
				        console.log(status);
				        if(status == "playing"){
				        	if(!device || device=='iphone'){
				        		player.resize(width, height);
				        		$(".vedio_wp").css({"height": height});
				        		$(".live_info").show();
				        		$(".pop_download").hide();
				        	}
				        }
				        else{
				        	player.resize(width, 320);
				        }
				    }
				});
			 	$("#startplay").on('click',function(){
	        		$("#liveing").show();
	        		$("#preVedio").hide();
			    	player.play();
			    });
		    })();
        }else{
            (function () {
            	if(!device || device=='iphone'){
            		var option ={
			     		"channel_id": channel_id,
	                    "app_id": app_id,
	                    "width": width,
	                    "height": 320,
	                    "x5_type": "h5",
	                    "x5_fullscreen": true,
	                    "h5_start_patch":{
	                    	"url": head_image_url,
	                    	"stretch": true
	                    }
					};
            	}
            	else{
            		var option ={
				     	"channel_id": channel_id,
	                    "app_id": app_id,
	                    "width": width,
	                    "height": 320,
	                    "h5_start_patch":{
	                    	"url": head_image_url,
	                    	"stretch": true
	                    }
					};
            	}
		        var player = new qcVideo.Player("id_video_container", option, {
				    playStatus: function (status){
			         	//TODO
				        console.log(status);
				        if(status == "playing"){
				        	if(!device || device=='iphone'){
				        		player.resize(width, height);
				        		$(".vedio_wp").css({"height": height});
				        		$(".live_info").show();
				        		$(".pop_download").hide();
				        	}
				        }
				        else{
				        	player.resize(width, 320);
				        }
				    }
				});
				$("#startplay").on('click',function(){
					$("#liveing").show();
	        		$("#preVedio").hide();
			    	player.play();
			    });
		    })();
        }
	}else if(live_in==3){
        (function () {
        	if(!device || device=='iphone'){
        		var option ={
	     			"file_id": file_id,
		            "app_id": app_id,
		            "width":width,
		            "height":320,
		            "x5_type": "h5",
	                "x5_fullscreen": true
				};
        	}
        	else{
        		var option ={
			     	"file_id": file_id,
		            "app_id": app_id,
		            "width":width,
		            "height":320
				};
        	}
	        var player = new qcVideo.Player("id_video_container", option, {
			    playStatus: function (status,type){
			        //TODO
			        console.log(status);
			        if(status == "playing"){
			        	if(!device || device=='iphone'){
			        		player.resize(width, height);
			        		$(".vedio_wp").css({"height": height});
			        		$(".live_info").show();
			        		$(".pop_download").hide();
			        	}
			        }
			        else{
			        	player.resize(width, 320);
			        }
			    }
			});
        	$("#startplay").on('click',function(){
        		$("#liveing").show();
        		$("#preVedio").hide();
		    	player.play();
		    });
	    })();

        /*var player = new qcVideo.Player("id_video_container", {
            "width":width,
            "height":height,
            "stretch_full":1,
            "stop_time":60,
            "third_video": {
                "urls":{
                    20 : urls//演示地址，请替换实际地址
                }
            }
        });*/
    }else{

    }

	wx.ready(function () {
		// 在这里调用 API
		wx.onMenuShareTimeline({
			title: wx_title, // 分享标题
			link: wx_link, // 分享链接
			imgUrl: wx_img, // 分享图标
			success: function () {
				// 用户确认分享后执行的回调函数
			},
			cancel: function () {
				// 用户取消分享后执行的回调函数
			}
		});
		wx.onMenuShareAppMessage({
			title: wx_title, // 分享标题
			desc: wx_desc, // 分享描述
			link: wx_link,  // 分享链接
			imgUrl: wx_img, // 分享图标
			type: 'link', // 分享类型,music、video或link，不填默认为link
			// dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () {
				// 用户确认分享后执行的回调函数
			},
			cancel: function () {
				// 用户取消分享后执行的回调函数
			}
		});

        wx.onMenuShareQQ({
            title: wx_title, // 分享标题
            desc: wx_desc, // 分享描述
            link: wx_link, // 分享链接
            imgUrl: wx_img, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });


        wx.onMenuShareQZone({
            title: wx_title, // 分享标题
            desc: wx_desc, // 分享描述
            link: wx_link, // 分享链接
            imgUrl: wx_img, // 分享图标
            success: function () {
                // 用户确认分享后执行的回调函数
            },
            cancel: function () {
                // 用户取消分享后执行的回调函数
            }
        });
		wx.error(function(res){
			// config信息验证失败会执行error函数，如签名过期导致验证失败，具体错误信息可以打开config的debug模式查看，也可以在返回的res参数中查看，对于SPA可以在这里更新签名。
		});
	});

	function addMsg(msg) {
		var time = webim.Tool.formatTimeStamp(msg.getTime());
		var data = convertMsg(msg);
		if(! data){
			return;
		}

		if(typeof data !== 'object'){
			data = {
				"user_level": 122,
				"nick_name": "[群提示消息]",
				"text": data,
			};
		}

		if (data.type == 2 && showBarrage && player) {
			var barrage = [
				{ "type": "content", "content": data.text, "time": "0" },
			];
			player.addBarrage(barrage);
		}

		$('#chat-box').append('<li><p><a href="javascript:;" class="name"><i class="ico_level"></i>' + data.nick_name + '</a>' + data.text + '</p></li>');
	}

	var listeners = {
        loginSuccess: function () {
            im_message.applyJoinBigGroup(avChatRoomId);
        },
        recieveGroupMsg: function (newMsgList) {
            for (var j in newMsgList) {//遍历新消息
                var newMsg = newMsgList[j];
                addMsg(newMsg);
            }
			
			var el = $('#video_sms_list');
            el.scrollTop(el.prop("scrollHeight"));
        },
        sendMsgOk: function (msg) {
            $('#input-chat-speak').val('');
        },
    };
	if(typeof loginInfo !== 'undefined'){
		im_message.init(loginInfo, listeners);
	}
});

var vm_login = new Vue({
    el: '#vscope-login',
    data: {
        mobile: '',
        verify_coder: '',
        is_disabled: false,
        code_lefttime: 0,
        code_timeer: null
    },
    methods: {
        login: function(ajax_url){
            var self = this;
            if(! self.check()){
                return false;
            }
            // 登录
            var query = new Object();
            query.mobile = self.mobile;
            query.verify_coder = self.verify_coder;
            $.ajax({
                url:ajax_url,
                data:query,
                type:"POST",
                dataType:"json",
                success:function(result){
                    if(result.status == 1){
                        //返回分享页
                        if(result.error){
                            $.toast(result.error,1000);
                            if(result.is_url){
                                setTimeout(function(){
                                    location.href= result.url;
                                },1000);
                            }
                        }else{
                            if(result.is_url){
                               location.href= result.url;
                            }
                        }
                    }
                    else{
                        if(result.error){
                            $.toast(result.error,1000);
                            if(result.is_url){
                                setTimeout(function(){
                                    location.href= result.url;
                                },1000);
                            }
                        }
                        else{
                            $.toast("操作失败");
                        }
                    }
                }
            });
        },
        check: function(val, event){
        // 验证表单
            var self = this;
            if($.trim(self.mobile).length == 0)
            {         
                $.toast("手机号码不能为空");
                return false;
            }
            if(!$.checkMobilePhone(self.mobile))
            {   
                $.toast("手机号码格式错误");
                return false;
            }
            if(!$.maxLength(self.mobile,11,true))
            {     
                $.toast("长度不能超过11位");
                return false;
            }
            else{
                return true;
            }
        },
        send_code: function(event){
        // 发送验证码
            var self = this;
            if(self.is_disabled){
                $.toast("发送速度太快了");
                return false; 
            }
            else{
                var thiscountdown=$("#j-send-code"); 
                var query = new Object();
                query.mobile = self.mobile;
                $.ajax({
                    url:APP_ROOT+"/mapi/index.php?ctl=login&act=send_mobile_verify",
                    data:query,
                    type:"POST",
                    dataType:"json",
                    success:function(result){
                        if(result.status == 1){    
                            countdown = 60;
                            // 验证码倒计时
                            vm_login.code_lefttime = 60;
                            self.code_lefttime_fuc("#j-send-code", self.code_lefttime);
                            // $.showSuccess(result.info);
                            return false;
                        }
                        else{
                            $.toast(result.error);
                            return false;
                        }
                  }
                });
            }
        },
        code_lefttime_fuc: function(verify_name,code_lefttime){
        // 验证码倒计时
            var self = this;
            clearTimeout(self.code_timeer);
            $(verify_name).html("重新发送 "+code_lefttime);
            code_lefttime--;
            if(code_lefttime >0){
                $(verify_name).attr("disabled","disabled");
                self.is_disabled=true;
                vm_login.code_timeer = setTimeout(function(){self.code_lefttime_fuc(verify_name,code_lefttime);},1000);
            }
            else{
                code_lefttime = 60;
                self.is_disabled=false;
                $(verify_name).removeAttr("disabled");
                $(verify_name).html("发送验证码");
            }
        }
    }
});
function infinite_scroll($page,ajax_url,cls,vm_paging,func) {
    if (loading || vm_paging.page>total_page){
        $(".content-inner").css({paddingBottom:"0"});
        return;
    }
    loading = true;

    handleAjax.handle(ajax_url,{page:vm_paging.page},"html").done(function(result){
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        html = $(htmlObject).html();
        $(html).find(".total_page").remove();
        vm_paging.page++;
        loading = false;
        $($page).find(cls).append(html);
        $.refreshScroller();
        if(func!=null){
            func();
        }
        // $('.lazyload').picLazyLoad();
    }).fail(function(err){
        $.toast(err);
    });
}

function pull_refresh($page,ajax_url,cls,vm_paging,callback){
    var loading = false;
    if (loading) return;
    loading =true;
    

    handleAjax.handle(ajax_url,'',"html").done(function(result){
        refreshing = false;
        var tplElement = $('<div id="tmpHTML"></div>').html(result),
        htmlObject = tplElement.find(cls),
        list_ele = $($page).find(cls),
        html = $(htmlObject).html();
        value = html.replace(/\s+/g,"");

        var result = $(result).find(".content").html(), total_page = htmlObject.find("input[name='total_page']").val();
        loading =false;
        vm_paging.page = 2;
        vm_paging.total_page = total_page;
        setTimeout(function() {

            list_ele.addClass('animated fadeInUp').html(value.length > 0 ? html : '<div style="text-align:center;color:#999;font-size:0.75rem;">暂无数据</div>');

            setTimeout(function(){
                list_ele.removeClass('fadeInUp');
            }, 1000);

            // 加载完毕需要重置
            $.pullToRefreshDone('.pull-to-refresh-content');
            $(".pull-to-refcontainerresh-layer").css({"visibility":"visible"});

            // 初始化分页数据
            page = 2;

            // 初始化懒加载图片
            // $('.lazyload').picLazyLoad();

            if(typeof(callback) == 'function'){
                callback.call(this);
            }
        }, 300);

    }).fail(function(err){
        $.toast(err);
    });
}
// 分销商品列表
$(document).on("pageInit","#page-shop-distribution_goods_list", function(e, pageId, $page) {
    init_paramet();
    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });

    $(document).on('click', '.J-distribution', function(){
        var self = $(this);
        if(self.hasClass('is_distribution')) return;
        var goods_id = self.attr("data-id");
        handleAjax.handle(TMPL+"index.php?ctl=shop&act=add_distribution_goods",{goods_id:goods_id}).done(function(resp){
            self.addClass('is_distribution');
            $.toast(resp,1000);
            setTimeout(function(){
               self.html('已添加分销');
            },1000);
        }).fail(function(err){
            $.toast(err,1000);
        });
    });

    // 无限滚动
    $($page).on('infinite', function(e) {
        infinite_scroll($page,ajax_url,".shop-list",vm_paging);
    });

    //下拉刷新
   $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
        pull_refresh($page,ajax_url,".shop-list",vm_paging);
        $("#search").val('');
        var all_options = document.getElementById("goods_cate").options;
        for (i=0; i<all_options.length; i++){
          if (all_options[i].id == 0)  // 根据option标签的ID来进行判断  测试的代码这里是两个等号
          {
             all_options[i].selected = true;
          }
       }
    });
    // 初始化参数
    function init_paramet(){
        // var urlinfo = window.location.href; //获取url  
        // paramet.content = decodeURI(urlinfo.split("&")[2].split("=")[1]);
        new_paramet = paramet.options ? '&options='+paramet.options : '',
        // new_paramet = paramet.content ? new_paramet+'&content='+paramet.content : new_paramet,
        // new_paramet = paramet.page ? new_paramet+'&page='+paramet.page : new_paramet,
        ajax_url = APP_ROOT+"/wap/index.php?ctl=shop&act=distribution_goods_list"+new_paramet;
    };
    $(function(){
        var option_url1 = TMPL + "index.php?ctl=shop&act=distribution_goods_list&options=1&cate_id=" + paramet.cate_id;
        var option_url2 = TMPL + "index.php?ctl=shop&act=distribution_goods_list&options=2&cate_id=" + paramet.cate_id;
        var option_url3 = TMPL + "index.php?ctl=shop&act=distribution_goods_list&options=3&cate_id=" + paramet.cate_id;
        $(".option1").attr("href",option_url1);
        $(".option2").attr("href",option_url2);
        $(".option3").attr("href",option_url3);
    });
    //分类筛选
    $(".select").change(function(){
        var self= $('option').not(function(){ return !this.selected });
        var id = self.attr("data-id");
        location.href = TMPL + "index.php?ctl=shop&act=distribution_goods_list&cate_id=" + id + "&options=" + paramet.options + "&page=" + data.page;
    });
    if(paramet.cate_id){
        if($("#search option").attr("data-id") == paramet.cate_id){
            $(this).attr("selected", true);
        }
    }
    //搜索关键字
    $(document).on('click', '.J-search', function(){
        var content = $("#search").val();
        console.log(data);
        location.href = TMPL + "index.php?ctl=shop&act=distribution_goods_list&content=" + content + "&options=" + paramet.options + "&page=" + data.page;
    });
});

$(document).on("pageInit","#page-shop-goods_details", function(e, pageId, $page) {
	$(document).on('click', '.J-anchor', function(){
			handleAjax.handle(TMPL+"index.php?ctl=shop&act=add_distribution_goods",{goods_id:goods_id}).done(function(resp){
            $.toast(resp,1000);
            setTimeout(function(){
               self.html('已添加分销');
            },1000);
        }).fail(function(err){
            $.toast(err,1000);
        });
	});
});
$(document).on("pageInit", "#page-shop-new_address", function(e, pageId, $page) {
    $("#city-picker").cityPicker({
        
            //value: ['四川', '内江', '东兴区']
    });
    $("#city-picker").click(function(){
        $("input:not(this)").blur();
    });
    function objBlur(obj, time){
    if(typeof obj != 'string') return false;
    var obj = document.getElementById(obj),
    time = time || 300,
    docTouchend = function(event){
        if(event.target!= obj){
            setTimeout(function(){
                 obj.blur();
                document.removeEventListener('touchend', docTouchend,false);
            },time);
        }
    };
    if(obj){
        obj.addEventListener('focus', function(){
            //注释这部分是在一个页面多个这样的调用时禁止冒泡让他不要让ios默认输入框上下弹，最好写在对应页面里给对应元素写这里效率低，这种写法很差所以先注释掉下次优化再贴
            // var input = document.getElementsByTagName('input'),
            // ilength = input.length;
            // for(var i=0; i<ilength; i++){
            //     input[i].addEventListener('touchend',function(e){e.stopPropagation()},false);
            // }
            // var textarea = document.getElementsByTagName('textarea'),
            // tlength = textarea.length;
            // for(var i=0; i<tlength; i++){
            //     textarea[i].addEventListener('touchend',function(e){e.stopPropagation()},false);
            // }
            document.addEventListener('touchend', docTouchend,false);
        },false);
    }else{
        //找不到obj
    }
};

var isIPHONE = navigator.userAgent.toUpperCase().indexOf('IPHONE')!= -1;
            if(isIPHONE){
                var input = objBlur('input');
                input = null;
            }




    $('.item-input').find("input[name=consignee]").blur(function() {
        var consignee = $(this).val();
        data.consignee = consignee;
    });
    $('.item-input').find("input[name=consignee_mobile]").blur(function() {
        var consignee_mobile = $(this).val();
        data.consignee_mobile = consignee_mobile;
    });
    $('.item-input').find("input[name=consignee_address]").blur(function() {
        var consignee_address = $(this).val();
        data.consignee_address = consignee_address;
    });
    $(".J-save").click(function() {
        data.consignee_district = $("#city-picker").val();
        console.log(data.consignee_district);
        if ($.checkEmpty(data.consignee)) {
            $.toast("收货人不能为空");
            return false;
        }
        if ($.trim(data.consignee_mobile).length == 0) {
            $.toast("手机号码不能为空");
            return false;
        }
        if (!$.checkMobilePhone(data.consignee_mobile)) {
            $.toast("手机号码格式错误");
            return false;
        }
        if (!$.maxLength(data.consignee_mobile, 11, true)) {
            $.toast("手机号码长度不能超过11位");
            return false;
        }


        if ($.checkEmpty(data.consignee_address)) {
            $.toast("请输入收货地址");
            return false;
        }
        if ($.checkEmpty(data.consignee_district)) {
            $.toast("请输入收货地址");
            return false;
        }

        $.ajax({
            url: APP_ROOT + "/wap/index.php?ctl=shop&act=editaddress&post_type=json&itype=shop",
            type: "post",
            data: data,
            dataType: "json",
            beforeSend: function() {
                $.showIndicator();
                onload = function() {
                    var a = document.querySelector("a");
                    a.onclick = function() {
                        if (this.disabled) {
                            return false;
                        }
                        this.style.color = 'grey';
                        this.disabed = true;
                    };
                }
            },
            success: function(result) {
                if (result.status == 1) {
                    $.toast("操作成功");
                    history.back();
                } else {
                    $.toast(result.error);
                }
            },
            error: function() {
                $.toast(err);
            },
            complete: function() {
                $.hideIndicator();
            }

        });

    });

    

    



});

$(document).on("pageInit", "#page-shop-order_settlement", function(e, pageId, $page) {
   for(var i=0;i<shop_info.length;i++){
        var obj_shop_info = shop_info[i], goods_obj = {};
        for(var j in obj_shop_info){
            if(j == "goods_id"){
                goods_obj.goods_id = shop_info[i][j];
            }
            if(j == "number"){
                goods_obj.number = shop_info[i][j];
            }
            if(j == "podcast_id"){
                goods_obj.podcast_id = shop_info[i][j];
            }
            if(j == "order_sn"){
                goods_obj.order_sn = shop_info[i][j];
            }
        }
        goods_arr.push(goods_obj);
    };
    var data_shop_arr =  JSON.stringify(goods_arr);
    $(document).on('click', '.J-submit-order', function() {
        handleAjax.handle(APP_ROOT + "/wap/index.php?ctl=shop&act=goods_inventory", { shop_info: data_shop_arr }, '', 1).done(function(result) {
            if (result.status == 1) {
                location.href = TMPL + "index.php?ctl=pay&act=h5_pay&purchase_type=" + data.purchase_type + "&shop_info=" + data_shop_arr;
            } else {
                $.toast(result.error);
                return false;
            }
        }).fail(function(err) {
            $.toast(err);
        });

    });
});

$(document).on("pageInit", "#page-shop-order_settlement_user", function(e, pageId, $page) {
    $(document).on('click', '.J-address', function() {

        location.href = TMPL + "index.php?ctl=shop&act=new_address&address_id=" + data.address_id;
    });
    for(var i=0;i<shop_info.length;i++){
        var obj_shop_info = shop_info[i], goods_obj = {};
        for(var j in obj_shop_info){
            if(j == "goods_id"){
                goods_obj.goods_id = shop_info[i][j];
            }
            if(j == "number"){
                goods_obj.number = shop_info[i][j];
            }
            if(j == "podcast_id"){
                goods_obj.podcast_id = shop_info[i][j];
            }
            if(j == "order_sn"){
                goods_obj.order_sn = shop_info[i][j];
            }
        }
        goods_arr.push(goods_obj);
    };
    
    $(document).on('click','.confirm-ok', function () {
        var id = $(this).attr("data-id"), val = $(this).val();
        console.log(val);
        $.alert('<textarea class="footer-input" type="text" name="remarks" data-id="'+id+'" value="'+val+'" placeholder="选填:对本次交易的说明(建议填写已和卖家协商一致的内容)">'+val+'</textarea>', function() {
            var text = $(".modal").find("textarea"),text_id = text.attr("data-id");
            $(".liuyan-"+id).val(text.val());
        });
    });
    $(document).on('click', '.J-submit-order', function() {
        $(".goods-item").each(function(index, element){
            var self = $(this), i = index, input_remarks = self.find("input[name='remarks']");
            goods_arr[i].memo =  input_remarks.val();
            
        });
        console.log(goods_arr);
        var data_shop_arr =  JSON.stringify(goods_arr);
        if (data.address_id == '') {
            $.toast('地址不能为空', 1000);

        } else {
            handleAjax.handle(APP_ROOT + "/wap/index.php?ctl=shop&act=goods_inventory", { shop_info: data_shop_arr }, '', 1).done(function(result) {
            if (result.status == 1) {
                location.href = TMPL + "index.php?ctl=pay&act=h5_pay&address_id=" + data.address_id + "&purchase_type=" + data.purchase_type + "&shop_info="+data_shop_arr;
            } else {
                    $.toast(result.error);
                    return false;
                }
            }).fail(function(err) {
                $.toast(err);
            });
        }

    });
    
});

// 商品管理列表
$(document).on("pageInit", "#page-shop-podcasr_goods_management", function(e, pageId, $page) {

    init_paramet();
    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });

    // 下架商品
    $(document).on('click', '.J-podcasr_shelves_goods', function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
        handleAjax.handle(TMPL + "index.php?ctl=shop&act=podcasr_shelves_goods", { goods_id: goods_id }).done(function(resp) {
            $.toast(resp, 1000);
            setTimeout(function() {
                $("#goods-item-" + goods_id).remove();
            }, 1000);
        }).fail(function(err) {
            $.toast(err, 1000);
        });
    });

    // 删除下架商品
    $(document).on('click', '.J-podcasr_delete_goods', function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
        handleAjax.handle(TMPL + "index.php?ctl=shop&act=podcasr_delete_goods", { goods_id: goods_id }).done(function(resp) {
            $.toast(resp, 1000);
            setTimeout(function() {
                $("#goods-item-" + goods_id).remove();
            }, 1000);
        }).fail(function(err) {
            $.toast(err, 1000);
        });
    });

    // 添加分销商品
    /*    $(document).on('click', '#J-add_distribution_goods', function(){
           var self = $(this);
           var goods_id = self.attr("data-id");
           handleAjax.handle(TMPL+"index.php?ctl=shop&act=add_distribution_goods",{goods_id:goods_id}).done(function(resp){
               $.toast(resp,1000);
               setTimeout(function(){
                   location.reload();
               },1000);
           }).fail(function(err){
               $.toast(err,1000);
           });
        });*/

    // 清空下架商品
    $(document).on('click', '#J-podcasr_empty_goods', function() {
        handleAjax.handle(TMPL + "index.php?ctl=shop&act=podcasr_empty_goods").done(function(resp) {
            $.toast(resp, 1000);
            setTimeout(function() {
                var html = '<div class="tc" style="color:#999;margin-top:50%;">' +
                    '   <i class="icon iconfont" style="font-size:3rem;line-height:1;">&#xe63f;</i>' +
                    '   <div>暂无分销商品，点击马上添加哦~</div>' +
                    '</div>';
                $($page).find(".goods-list").html(html);
            }, 1000);
        }).fail(function(err) {
            $.toast(err, 1000);
        });
    });

    // 无限滚动
    $($page).on('infinite', function(e) {
        infinite_scroll($page,ajax_url,".goods-list",vm_paging);
    });

    //下拉刷新
   $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
        pull_refresh($page,ajax_url,".goods-list",vm_paging);
        $("#search").val("");
    });
    // 初始化参数
    function init_paramet(){
        // var urlinfo = window.location.href; //获取url  
        // paramet.content = decodeURI(urlinfo.split("&")[2].split("=")[1]);

        new_paramet = paramet.state ? '&state='+paramet.state : '',
        // new_paramet = paramet.content ? new_paramet+'&content='+paramet.content : new_paramet,
        // new_paramet = paramet.page ? new_paramet+'&page='+paramet.page : new_paramet,
        ajax_url = APP_ROOT+"/wap/index.php?ctl=shop&act=podcasr_goods_management"+new_paramet;
    };

    $(document).on('click', '.J-search', function(){
        var content = $("#search").val();

        location.href = encodeURI(TMPL + "index.php?ctl=shop&act=podcasr_goods_management&content=" + content + "&state=" + data.state + "&page=" + data.page);
    });

});

$(document).on("pageInit","#page-shop-shop_goods_details", function(e, pageId, $page) {
	var shop_arr = [];
	shop_arr.push({podcast_id:data.podcast_id,goods_id:data.goods_id,number:data.number});
	var data_shop_arr =  JSON.stringify(shop_arr);
	$(document).on('click', '.J-anchor', function(){

			
			location.href = TMPL+"index.php?ctl=shop&act=order_settlement&shop_info="+data_shop_arr;
		
	});
	$(document).on('click', '.J-oneself', function(){
		
			location.href = TMPL+"index.php?ctl=shop&act=order_settlement_user&shop_info="+data_shop_arr;
		
		
	});
});
$(document).on("pageInit", "#page-shop-shop_goods_list", function(e, pageId, $page) {
    init_paramet();
    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });

    // 无限滚动
    $($page).on('infinite', function(e) {
        infinite_scroll($page, ajax_url, ".goods-list", vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
        pull_refresh($page, ajax_url, ".goods-list", vm_paging);
        $("#search").val('');
    });



    //增加购买数量
    $(".input-goods-num").val(0);
    $(document).on('click', '.add', function() {
        var self = $(this);
        var goods_id = self.attr("data-id");

        var num = parseInt($(this).siblings(".input-goods-num").val()) || 0;

        if(num < 99){
           num = num + 1;
        }

        // $(".input-goods-num").val(0);
        $(this).siblings(".input-goods-num").val(num);
        data.goods_id = goods_id;
        data.number = Number(num);
    });
    //减少购买数量
    $(document).on('click', '.lost', function() {
        var self = $(this);
        var goods_id = self.attr("data-id");

        var num = parseInt($(this).siblings(".input-goods-num").val()) || 0;

        if(num > 0){
            num = num - 1;
        }

        // $(".input-goods-num").val(0);
        $(this).siblings(".input-goods-num").val(num);

        data.goods_id = goods_id;
        data.number = Number(num);
    });
    $('.input-goods-num').blur(function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
        data.goods_id = goods_id;
        data.number = Number($(this).val());
    });
    $('.input-goods-num').bind('input propertychange', function() {
        var self = $(this),
            self_num = self.val();
        // if (self_num) {
        //     $(".input-goods-num").not(self).val(0);
        // }
        if (self_num>99) {
            $(this).val(99);
        }
    });



    //买给主播
    $(document).on('click', '.J-anchor', function() {
        var shop_arr = [];
        $(".goods-item").each(function(){
            var self = $(this), input_amount = self.find("input[name='amount']");
            if(input_amount.val()>0){
                shop_arr.push({podcast_id:data.podcast_id,goods_id: input_amount.attr("data-id"), number: input_amount.val()});  
            }
        });
        
        var data_shop_arr =  JSON.stringify(shop_arr);
        if (shop_arr.length) {
            location.href = TMPL + "index.php?ctl=shop&act=order_settlement&shop_info="+data_shop_arr;
        } else {
            $.toast("请先选择商品");
            return false;
        }
    });

    //买给自己
    $(document).on('click', '.J-oneself', function() {
        var shop_arr = [];
        $(".goods-item").each(function(){
            var self = $(this), input_amount = self.find("input[name='amount']");
            if(input_amount.val()>0){
                shop_arr.push({podcast_id:data.podcast_id,goods_id:input_amount.attr("data-id"),number:input_amount.val()});  
            }
        });
        
        var data_shop_arr =  JSON.stringify(shop_arr);
        if (shop_arr.length) {
            location.href = TMPL + "index.php?ctl=shop&act=order_settlement_user&shop_info="+data_shop_arr;
        } else {
            $.toast("请先选择商品");
            return false;
        }
    });

    $(document).on('click', '.J-details', function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
        location.href = TMPL + "index.php?ctl=shop&act=shop_goods_details&podcast_id=" + data.podcast_id + "&goods_id=" + goods_id;

    });


    // 初始化参数
    function init_paramet() {
        // var urlinfo = window.location.href; //获取url  
        // paramet.content = decodeURI(urlinfo.split("&")[2].split("=")[1]);

        new_paramet = paramet.podcast_id ? '&podcast_id=' + paramet.podcast_id : '',
        // new_paramet = paramet.content ? new_paramet+'&content='+paramet.content : new_paramet,
        // new_paramet = paramet.page ? new_paramet+'&page='+paramet.page : new_paramet,
            ajax_url = APP_ROOT + "/wap/index.php?ctl=shop&act=shop_goods_list" + new_paramet;

    }


    //搜索关键字
    $(document).on('click', '.J-search', function(){
        var content = $("#search").val();
        location.href = TMPL + "index.php?ctl=shop&act=shop_goods_list&content=" + content + "&podcast_id=" + data.podcast_id + "&page=" + data.page;
    });

    //加入购物车
    $(document).on('click', '.J-add_shopping_cart', function(){
        var self = $(this);
        var goods_id = self.attr('data-id'), number = self.parents(".card").find("input[name=amount]").val();
        if(number>0){
            handleAjax.handle(TMPL + "index.php?ctl=shop&act=join_shopping",{goods_id:goods_id, podcast_id:data.podcast_id, number:number}).done(function(resp){
            setTimeout(function(){
               $.toast('已添加购物车');
            },1000);
        }).fail(function(err){
            $.toast(err,1000);
        });
        }else{
            $.toast("请先选择商品");
            return false;
        }
        


    });

});

$(document).on("pageInit","#page-shop-shop_order", function(e, pageId, $page) {
    init_paramet();
    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });

    // 无限滚动
    $($page).on('infinite', function(e) {
        infinite_scroll($page,ajax_url,".goods-list",vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
        pull_refresh($page,ajax_url,".goods-list",vm_paging,function(){
            $(".left_time").each(function(){
                var leftTime = Math.abs(parseInt($(this).attr("data-leftTime")));
                left_time(leftTime,$(this));
            });
        });
    });


    $(document).on('click', '.J-pay', function(){
        var self = $(this);
        var order_id = self.attr("data-order_id");
        var order_sn = self.attr("data-order_sn");
        window.location.href = TMPL + "index.php?ctl=pay&act=h5_pay&order_sn="+order_sn+"&order_id="+order_id;
        // $.ajax({
        //     url: APP_ROOT+"/mapi/index.php?ctl=pay&act=h5_pay",
        //     data: {itype:"shop", order_id:order_id,order_sn:order_sn},
        //     type: 'POST',
        //     dataType: 'json',
        //     success:function(data){
        //         if(data.status == 1){
        //             $.toast(data.error,1000);
        //             setTimeout(function(){
        //                 window.location.reload(); 
        //             },1000);
        //         }
        //         else{
        //             $.toast(data.error,1000);
        //         }
        //     },
        //     error:function(){
        //         $.hideIndicator();
        //         $.toast('请求失败，请检查网络',1000);
        //     }
        // });
    });
    $(document).on('click', '.J-confirm', function(){
        var self = $(this);
        var to_podcast_id = self.attr("data-to_podcast_id");
        var order_sn = self.attr("data-order_sn");
        $.confirm("是否确认收货？",function(s){
            handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=buyer_confirm_date",{to_podcast_id:to_podcast_id,order_sn:order_sn}).done(function(resp){
                $.toast('确认收货成功',1000);
                setTimeout(function(){
                    window.location.reload();
                },1000);
            }).fail(function(err){
                $.toast(err,1000);
            });
        });
    });
    $(document).on('click', '.J-remind', function(){
        $.toast('已经提醒商家',1000);
    });
    $(document).on('click', '.J-detail', function(){
        var self = $(this);
        var order_id = self.attr("data-order_id");
        var order_sn = self.attr("data-order_sn");
        location.href = TMPL+"index.php?ctl=shop&act=virtual_shop_order_details&order_id="+order_id+"&order_sn="+order_sn;
    });

    // 初始化参数
    function init_paramet(){

        new_paramet = paramet.state ? '&state='+paramet.state : '',

        ajax_url = APP_ROOT+"/wap/index.php?ctl=shop&act=shop_order"+new_paramet;
        console.log(ajax_url);
    }
});
$(document).on("pageInit","#page-shop-shop_shopping_cart", function(e, pageId, $page) {

    all_js();
    init_paramet();
    var vm_paging = new Vue({
        el: "#vscope-paging",
        data: {
            total_page: total_page,
            page: page,
        }
    });

    // 无限滚动
    $($page).on('infinite', function(e) {
        infinite_scroll($page, ajax_url, ".goods-list", vm_paging);
    });

    // 下拉刷新
    $($page).find(".pull-to-refresh-content").on('refresh', function(e) {
        pull_refresh($page, ajax_url, ".goods-list", vm_paging, function(){
        	all_js();
        });
        $(".J-money").html(0);
        $("input[name=shopping-cart-all]").prop('checked',false);
    });

    // 初始化参数
    function init_paramet() {
        // var urlinfo = window.location.href; //获取url  
        // paramet.content = decodeURI(urlinfo.split("&")[2].split("=")[1]);

        new_paramet = paramet.page ? '&page=' + paramet.page : '',
        ajax_url = APP_ROOT + "/wap/index.php?ctl=shop&act=shop_shopping_cart&page=1";

    };
    function all_js() {
    	var shopping_cart_top = $("input[name=shopping-cart-top]"),
		shopping_cart = $("input[name=shopping-cart]"),
		shopping_cart_all = $("input[name=shopping-cart-all]");
		shopping_cart_top.click(function(){
		var self = $(this);
		if(self.is(":checked")){
			// $(".input-check-"+id).prop('checked',true);
			self.parents(".card").find("input[name=shopping-cart]").prop('checked',true);
		}else{
			// $(".input-check-"+id).prop('checked',false);
			self.parents(".card").find("input[name=shopping-cart]").prop('checked',false);
		}

		var sum = 0;
		$("input[name=shopping-cart]:checked").each(function(){
			sum += parseFloat($(this).parent().find(".input-money").val()*$(this).parent().find(".goods-numb").attr("data-id"));
		});
		sum = sum.toFixed(2);
		$(".J-money").html(sum);
		var card_length = $(".card").length;
		var checked_top_length = $("input[name=shopping-cart-top]:checked").length;
		if (checked_top_length == card_length) {
			shopping_cart_all.prop('checked',true);
		}else{
			shopping_cart_all.prop('checked',false);
		}
	});
	shopping_cart.click(function(){
		if(shopping_cart.is(":checked")){
			
		}else{
			$(this).parents(".card").find("input[name=shopping-cart-top]").prop('checked',false);
			$(shopping_cart_all).prop('checked',false);
		}
		var card_content_length = $(this).parents(".card").find(".card-content").length;
		var checked_length = $(this).parents(".card").find("input[name=shopping-cart]:checked").length;
		
		if (checked_length == card_content_length) {
			$(this).parents(".card").find("input[name=shopping-cart-top]").prop('checked',true);
		}else{
			$(this).parents(".card").find("input[name=shopping-cart-top]").prop('checked',false);
		}
		var card_length = $(".card").length;
		var checked_top_length = $("input[name=shopping-cart-top]:checked").length;
		if (checked_top_length == card_length) {
			shopping_cart_all.prop('checked',true);
		}else{
			shopping_cart_all.prop('checked',false);
		}
		var sum = 0;
		$("input[name=shopping-cart]:checked").each(function(){
			sum += parseFloat($(this).parent().find(".input-money").val()*$(this).parent().find(".goods-numb").attr("data-id"));
			
		});
		sum = sum.toFixed(2);
		$(".J-money").html(sum);
	});

	//编辑
	$(".J-edit").click(function(){
		var self = $(this);
		var txt = self.html();
		var goods_id = self.attr("data-id"), podcast_id = self.attr("data-podcast_id"), number = self.parents(".card").find("input[name=amount]").val();
		if (txt =="编辑") {
			self.html('完成');
		}else if (txt =="完成") {
			self.html('编辑');
		    handleAjax.handle(TMPL + "index.php?ctl=shop&act=update_shopping_goods", { goods_id: goods_id, podcast_id:podcast_id, number:number}).done(function(resp) {
	            $.toast(resp,1000);
	            setTimeout(function() {
	                window.location.reload();
	            });
	        }).fail(function(err) {
	            $.toast(err, 1000);
	        });
		}
		self.parents(".card").find(".goods-text").toggleClass("active");
		self.parents(".card").find(".goods-edit").toggleClass("active");
	});

	//删除
	$(".J-delete").click(function(){
		var self = $(this);
		var parents_card = self.parents(".card");
		var goods_id = self.attr("data-id"), podcast_id = self.attr("data-podcast_id"), number = self.parents(".card").find("input[name=amount]").val();
		$.confirm('是否确定删除商品?',function () {
        	handleAjax.handle(TMPL + "index.php?ctl=shop&act=delete_shopping_goods", { goods_id: goods_id, podcast_id:podcast_id, number:number}).done(function(resp) {
	            setTimeout(function() {
			          	parents_card.remove();
			          	var sum = 0;
						$("input[name=shopping-cart]:checked").each(function(){
							sum += parseFloat($(this).parent().find(".input-money").val()*$(this).parent().find(".goods-numb").attr("data-id"));
							
						});
						sum = sum.toFixed(2);
						$(".J-money").html(sum);
	            });
	        }).fail(function(err) {
	            $.toast(err, 1000);
	        });
    	});
	});

	shopping_cart_all.click(function(){
		if(shopping_cart_all.is(":checked")){
			$(".input-check").prop('checked',true);
		}else{
			$(".input-check").prop('checked',false);
		}
		var sum = 0;
		$("input[name=shopping-cart]:checked").each(function(){
			sum += parseFloat($(this).parent().find(".input-money").val()*$(this).parent().find(".goods-numb").attr("data-id"));
			
		});
		sum = sum.toFixed(2);
		$(".J-money").html(sum);
	});

	//增加购买数量
    $(".add").click(function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
        var num = parseInt($(this).siblings(".input-goods-num").val()) || 0;
        if(num < 99){
           num = num + 1;
        }
        $(this).siblings(".input-goods-num").val(num);
    });
    //减少购买数量
    $(".lost").click(function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
        var num = parseInt($(this).siblings(".input-goods-num").val()) || 0;
        if(num > 1){
            num = num - 1;
        }
        $(this).siblings(".input-goods-num").val(num);
    });
    $('.input-goods-num').blur(function() {
        var self = $(this);
        var goods_id = self.attr("data-id");
    });

    $('.input-goods-num').bind('input propertychange', function() {
        var self = $(this),
            self_num = self.val();
        // if (self_num) {
        //     $(".input-goods-num").not(self).val(0);
        // }
        if (self_num>99) {
            $(this).val(99);
        }
    });

    //结算
    $(document).on('click', '.J-settlement', function(){

    	var shop_arr = [];
	    $("input[name=shopping-cart]:checked").each(function(){
	    	var self = $(this);
	        var input_amount = self.parents(".card").find("input[name='input-number']");
	        var input_money = self.parents(".card").find("input[name='input-money']");
	        if(input_amount.val()>0){
	            shop_arr.push({podcast_id:input_money.attr("data-podcast_id"),goods_id:input_money.attr("data-id"),number:input_amount.val()});  
	        }
	    });

        
	    var data_shop_arr =  JSON.stringify(shop_arr);
		if (shop_arr.length) {
			location.href = TMPL + "index.php?ctl=shop&act=order_settlement_user&shop_info="+data_shop_arr;
		} else {
			$.toast("请先选择商品");
			return false;
		}

    });
    };
});
$(document).on("pageInit","#page-shop-virtual_shop_order_details", function(e, pageId, $page) {
	//付款
	$(document).on('click', '.J-pay', function(){
        window.location.href = TMPL + "index.php?ctl=pay&act=h5_pay&order_sn="+data.order_sn+"&order_id="+data.order_id;
		// $.ajax({
  //           url: APP_ROOT+"/mapi/index.php?ctl=pay&act=h5_pay&itype=shop",
  //           data: data,
  //           type: 'POST',
  //           dataType: 'json',
  //           success:function(data){
  //               if(data.status == 1){
  //                   $.toast(data.error,1000);
  //                   setTimeout(function(){
  //                   	window.location.reload(); 
  //                   },1000);
  //               }
  //               else{
  //                   $.toast(data.error,1000);
  //               }
  //           },
  //           error:function(){
  //               $.hideIndicator();
  //               $.toast('请求失败，请检查网络',1000);
  //           }
  //       });
	});
	$(document).on('click', '.J-remind', function(){
		$.toast('已经提醒商家',1000);
	});
	$(document).on('click', '#J-return_virtual_pai', function(){
      handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=buyer_confirm_date",data).done(function(resp){
            $.toast('确认收货成功',1000);
            setTimeout(function(){
               window.location.reload(); 
            },1000);
        }).fail(function(err){
            $.toast(err,1000);
        });

	});
	$(document).on('click', '.J-buyer_to_complaint', function(){
      handleAjax.handle(TMPL+"index.php?ctl=pai_podcast&act=buyer_to_complaint",data).done(function(resp){
            $.toast('已提交申请',1000);
            setTimeout(function(){
               window.location.reload(); 
            },1000);
        }).fail(function(err){
            $.toast(err,1000);
        });

	});
});

/**
 * Simplethumbs Gallery
 * Version 0.9.2b
 * Author Abel Mohler
 * Released with the MIT License: http://www.wayfarerweb.com/mit.php
 */

(function($) {
    $.fn.simplethumbs = function(o, p) {
        var defaults = {
            slideshow: "",//required, location of image viewport
            preload: true,//recommended for anything but very large galleries, preloads all required images
            activeClass: "active",//class placed on active thumbnail
            speed: 600,//speed of transition between images
            next: "#next-image",//button for "next" image
            prev: "#prev-image",//button for "previous" image
            wrap: false,//if true, reaching the end wraps back to the beginning, when using "next" and "previous" buttons
            cycle: false,//autostart slideshow
            cycleWrap: true,//does slideshow stop at end? similar to "wrap" option for next and previous
            interval: 3000,//delay between automatic slides
            reverse: false,//if true, slideshow goes in reverse
            startCycle: "#start-cycle",//to start the slideshow
            stopCycle: "#stop-cycle",//to stop it
            toggleCycle: "#toggle-cycle",//to toggle it
            hoverPause: false,//if true, hovering over the slideshow will pause the automatic cycle
            callAfter: function(thumb) {},//callback before
            callBefore: function(thumb) {},//callback after
            beforeCycle: function() {},//callback before automatic cycle
            afterCycle: function() {}//callback after automatic cycle
        }
        if (typeof o == "string" && typeof p != "undefined") {
            defaults[o] = p;
            o = {};
        }
        else if(typeof p == "function") {
            defaults.callAfter = p;
        }
        o = $.extend(defaults, o || {});

        if(o.slideshow == "") return this;
        if(o.cycle) o.cycleMem = o.wrap;
        //var thumbList = this,
        var defaultSrc = $(o.slideshow).find("img")[0].src;
        $(o.slideshow).css({
            position: "relative",
            lineHeight: 0,
            overflow: "hidden"
        });
        $("<img>").attr("src", $(o.slideshow).find("img").attr("src")).appendTo(o.slideshow);
        $(o.slideshow).find("img").eq(0).css({
            position: "absolute",
            top: 0,
            left: 0
        });
        $(o.slideshow).find("img").eq(1).css({
            //"float": "left",
            position: "relative"
        });

        this.find("a").each(function(){
            if(o.preload && this.href) {
                var img = new Image();
                img.src = this.href;
            }
            if(this.href == defaultSrc) $(this).addClass(o.activeClass);
        });

        return this.each(function() {
            var a = $(this).find("a"),
            running = false,
            id1, id2;
            a.click(function() {
                if (!running && !$(this).hasClass(o.activeClass)) {
                    running = true;
                    o.callBefore.call(this, this);
                    var href = this.href,
                    title = this.title,
                    captionOk = false,
                    thumb = this;//,
                    //lastThumb = $(thumbList).find(o.activeClass)[0];
                    var img = $(o.slideshow).find("img").eq(1);
                    if ($(o.slideshow).siblings("#caption").length) {
                        captionOk = true;
                    }
                    $(o.slideshow).find("img").eq(0).attr("src", href);

                    if (captionOk) {
                        $(o.slideshow).siblings("#caption").slideUp("normal");
                    }
                    $(a).removeClass(o.activeClass);
                    $(this).addClass(o.activeClass);
                    $(img).fadeOut(o.speed, function(){
                        $(img).attr("src", href).css("display", "block");
                        if (captionOk) {
                            $(o.slideshow).siblings("#caption").find("div").text(title);
                            $(o.slideshow).siblings("#caption").slideDown("normal");
                        }
                        o.callAfter.call(thumb, thumb);
                        //o.callAfter.call(thumb, thumb, lastThumb);
                        running = false;
                    });
                }
                return false;
            });
            function _triggerNext() {
                for(var i = 0; i < a.length; i++) {
                    if(a.eq(i).hasClass(o.activeClass)) {
                        if((a.length - 1) == i) {
                            if(o.wrap)
                                a.eq(0).trigger("click");
                        }
                        else {
                            a.eq(i + 1).trigger("click");
                        }
                        break;
                    }
                }
                return false;
            }
            function _triggerPrevious() {
                for(var i = 0; i < a.length; i++) {
                    if(a.eq(i).hasClass(o.activeClass)) {
                        if(i == 0) {
                            if(o.wrap)
                                a.eq(a.length - 1).trigger("click");
                        }
                        else {
                            a.eq(i - 1).trigger("click");
                        }
                        break;
                    }
                }
                return false;
            }
            function _startCycle() {
                o.beforeCycle();
                o.cycle = true;
                o.wrapMem = o.wrap;
                o.wrap = o.cycleWrap;
                if(o.reverse) {
                    _triggerPrevious();
                }
                else {
                    _triggerNext();
                }
                id1 = setTimeout(function() {//instead of setInterval for better memory management.
                    if(o.reverse) {
                        _triggerPrevious();
                    }
                    else {
                        _triggerNext();
                    }
                    id2 = setTimeout(arguments.callee, o.speed + o.interval);
                },o.speed + o.interval);
                return false;
            }
            function _stopCycle() {
                clearTimeout(id1);
                clearTimeout(id2);
                o.cycle = false;
                o.wrap = o.wrapMem;
                o.afterCycle();
                return false;
            }
            $(o.next).click(function() {
                _stopCycle();
                return _triggerNext();
            });

            $(o.prev).click(function() {
                _stopCycle();
                return _triggerPrevious();
            });
            $(o.startCycle).click(function() {
                return _startCycle();
            });
            $(o.stopCycle).click(function() {
                return _stopCycle();
            });
            $(o.toggleCycle).click(function() {//can't use $().toggle() because o.cycle could be defaulted to true
                if(o.cycle) {
                    return _stopCycle();
                }
                else {
                    return _startCycle();
                }
            });
            if(o.hoverPause) {
                $(o.slideshow).hover(function() {
                    _stopCycle();
                }, function() {
                    _startCycle();
                });
            }
            if(o.cycle) {
                _startCycle();
            }
        });
    }
})(jQuery);
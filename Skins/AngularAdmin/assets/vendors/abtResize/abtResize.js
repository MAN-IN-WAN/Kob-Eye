//based upon https://codepen.io/Reklino/pen/raRaXq

/*

Attributs existants :

resizable : attr de base ( pas de valeur )
r-directions : Directions disponibles au resize"['right']" ou "['top']" ou "['right','left','bottom','top']"...
r-centered-x : Si div centrée double la vitesse de resize (axe x) Booleen
r-centered-y : Si div centrée double la vitesse de resize (axe y) Booleen
r-graber : code html du grabber si celui par defaut ne conviens pas <Html>
r-flex : Gestion des containers flex Booleen  //TODO :pas activée atm
r-width : Définition pour forcer la largeur //TODO :pas activée atm
r-height :Définition pour forcer la hauteur //TODO :pas activée atm
r-pixel : CHoix de l'unité de taille %/px, par default %  Booleen
r-type : Type spécifique "fullCol" ou "fullRow" (Savoir si les modifier une div en affecte une autre (vertical/horizontal))


*/

angular.module("abt.Resizable", []);

angular.module('abt.Resizable', []).directive('resizable', function() {
    var toCall;
    function throttle(fun) {
        if (toCall === undefined) {
            toCall = fun;
            setTimeout(function() {
                toCall();
                toCall = undefined;
            }, 100);
        } else {
            toCall = fun;
        }
    }
    return {
        restrict: 'AE',
        scope: true,
        link: function(scope, element, attr) {
            var flexBasis = 'flexBasis' in document.documentElement.style ? 'flexBasis' :
                'webkitFlexBasis' in document.documentElement.style ? 'webkitFlexBasis' :
                    'msFlexPreferredSize' in document.documentElement.style ? 'msFlexPreferredSize' : 'flexBasis';



            element.addClass('resizable');

            var style = window.getComputedStyle(element[0], null),
                w,
                h,
                dir =  scope.$eval(attr.rDirections),
                vx = scope.$eval(attr.rCenteredX )? 2 : 1, // if centered double velocity
                vy = scope.$eval(attr.rCenteredY) ? 2 : 1, // if centered double velocity
                inner = scope.$eval(attr.rGrabber) ? scope.rGrabber : '<span></span>',
                unit = scope.$eval(attr.rPixel) ? 'px':'%',
                type = attr.rType,
                start,
                dragDir,
                axis,
                info = {};

            // register watchers on width and height attributes if they are set
            scope.$watch('rWidth', function(value){
                element[0].style.width = scope.rWidth + unit;
            });
            scope.$watch('rHeight', function(value){
                element[0].style.height = scope.rHeight + unit;
            });


            var updateInfo = function(e, next) {
                info.width = false; info.height = false;
                if(axis === 'x')
                    info.width = parseInt(element[0].style[scope.rFlex ? flexBasis : 'width']);
                else
                    info.height = parseInt(element[0].style[scope.rFlex ? flexBasis : 'height']);
                info.id = element[0].id;
                info.evt = e;
                info.element = element[0];
                info.next = ( next && next[0] )? next[0] : ( info.next ? info.next : null );
            };

            var dragging = function(e) {
                var prop, offset = axis === 'x' ? start - e.clientX : start - e.clientY;
                var parent = element.parent();
                var parh = parent.innerHeight();
                var parw = parent.innerWidth();
                var next = null;
                if(unit == '%') {
                    switch(dragDir) {
                        case 'top':
                            prop = scope.rFlex ? flexBasis : 'height';
                            var newSize = h + (offset * vy);
                            var percent = Math.floor(newSize/parh*100);
                            if(type == 'fullCol'){
                                var sibs = element.siblings().not('[r-ignore]');
                                var sibPer = 0;
                                for(var i=0; i < sibs.length; i++){
                                    var sibh = sibs.eq(i).outerHeight();
                                    var sibp = Math.ceil(sibh/parh*100);
                                    sibPer += sibp;
                                }
                                var maxper = 100 - sibPer;
                                if(percent > maxper){
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = percent - maxper;
                                        var nexth = next.outerHeight();
                                        var nextper = Math.ceil(nexth / parh*100);
                                        var maxcut = nextper - 1;
                                        if(maxcut - tocut > 1){
                                            nextper -= tocut;
                                            next[0].style[prop] = nextper + '%';
                                            if(next[0].getAttribute('data-height')) {
                                                next[0].setAttribute('data-height', nextper);
                                            }
                                        }
                                        else {
                                            percent = maxper + maxcut;
                                            next[0].style[prop] = '1%';
                                            if(next[0].getAttribute('data-height')) {
                                                next[0].setAttribute('data-height', 1);
                                            }
                                        }

                                    }else{
                                        percent = maxper;
                                    }

                                } else{
                                    if(percent < 1)
                                        percent =1;
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nexth = next.outerHeight();
                                        var nextper = Math.ceil(nexth / parh*100);
                                        var tocomplete = 100 - (sibPer + percent);
                                        nextper += tocomplete;
                                        next[0].style[prop] = nextper + '%';
                                        if(next[0].getAttribute('data-height')) {
                                            next[0].setAttribute('data-height', nextper);
                                        }
                                    }
                                }
                            }
                            element[0].style[prop] = percent + '%';
                            if(element[0].getAttribute('data-height'))
                                element[0].setAttribute('data-height',percent);
                            break;
                        case 'bottom':
                            prop = scope.rFlex ? flexBasis : 'height';
                            var newSize = h - (offset * vy);
                            var percent = Math.floor(newSize/parh*100);
                            if(type == 'fullCol'){
                                var sibs = element.siblings().not('[r-ignore]');
                                var sibPer = 0;
                                for(var i=0; i < sibs.length; i++){
                                    var sibh = sibs.eq(i).outerHeight();
                                    var sibp = Math.ceil(sibh/parh*100);
                                    sibPer += sibp;
                                }
                                var maxper = 100 - sibPer;
                                if(percent > maxper){
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = percent - maxper;
                                        var nexth = next.outerHeight();
                                        var nextper = Math.ceil(nexth / parh*100);
                                        var maxcut = nextper - 1;
                                        if(maxcut - tocut > 1){
                                            nextper -= tocut;
                                            next[0].style[prop] = nextper + '%';
                                            if(next[0].getAttribute('data-height')) {
                                                next[0].setAttribute('data-height', nextper);
                                            }
                                        }
                                        else {
                                            percent = maxper + maxcut;
                                            next[0].style[prop] = '1%';
                                            if(next[0].getAttribute('data-height')) {
                                                next[0].setAttribute('data-height', 1);
                                            }
                                        }

                                    }else{
                                        percent = maxper;
                                    }

                                } else{
                                    if(percent < 1)
                                        percent =1;
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nexth = next.outerHeight();
                                        var nextper = Math.ceil(nexth / parh*100);
                                        var tocomplete = 100 - (sibPer + percent);
                                        nextper += tocomplete;
                                        next[0].style[prop] = nextper + '%';
                                        if(next[0].getAttribute('data-height')) {
                                            next[0].setAttribute('data-height', nextper);
                                        }
                                    }
                                }
                            }
                            element[0].style[prop] = percent + '%';
                            if(element[0].getAttribute('data-height'))
                                element[0].setAttribute('data-height',percent);
                            break;
                        case 'right':
                            prop = scope.rFlex ? flexBasis : 'width';
                            var newSize = w - (offset * vx);
                            var percent = Math.ceil(newSize/parw*100);
                            if(type == 'fullRow'){
                                var sibs = element.siblings().not('[r-ignore]');
                                var sibPer = 0;
                                for(var i=0; i < sibs.length; i++){
                                    var sibw = sibs.eq(i).outerWidth();
                                    var sibp = Math.ceil(sibw/parw*100);
                                    sibPer += sibp;
                                }
                                var maxper = 100 - sibPer;
                                if(percent > maxper){
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next.length){
                                        var tocut = percent - maxper;
                                        var nextw = next.outerWidth();
                                        var nextper = Math.floor(nextw / parw*100);
                                        var maxcut = nextper - 5;
                                        if(maxcut - tocut > 5){
                                            nextper -= tocut;
                                            next[0].style[prop] = nextper + '%';
                                            if(next[0].getAttribute('data-width')) {
                                                next[0].setAttribute('data-width', nextper);
                                            }
                                        }
                                        else {
                                            percent = maxper + maxcut;
                                            next[0].style[prop] = '5%';
                                            if(next[0].getAttribute('data-width')) {
                                                next[0].setAttribute('data-width', 1);
                                            }
                                        }

                                    }else{
                                        percent = maxper;
                                    }

                                } else{
                                    if(percent < 5)
                                        percent =5;
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next.length) {
                                        var nextw = next.outerWidth();
                                        var nextper = Math.floor(nextw / parw*100);
                                        var tocomplete = 100 - (sibPer + percent);
                                        nextper += tocomplete;
                                        next[0].style[prop] = nextper + '%';
                                        if(next[0].getAttribute('data-width')) {
                                            next[0].setAttribute('data-width', nextper);
                                        }
                                    }
                                }
                            }
                            element[0].style[prop] = percent + '%';
                            if(element[0].getAttribute('data-width')) {
                                element[0].setAttribute('data-width', percent);
                            }
                            break;
                        case 'left':
                            prop = scope.rFlex ? flexBasis : 'width';
                            var newSize = w + (offset * vx);
                            var percent = Math.floor(newSize/parw*100);
                            if(type == 'fullRow'){
                                var sibs = element.siblings().not('[r-ignore]');
                                var sibPer = 0;
                                for(var i=0; i < sibs.length; i++){
                                    var sibw = sibs.eq(i).outerWidth();
                                    var sibp = Math.ceil(sibw/parw*100);
                                    sibPer += sibp;
                                }
                                var maxper = 100 - sibPer;
                                if(percent > maxper){
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = percent - maxper;
                                        var nextw = next.outerWidth();
                                        var nextper = Math.ceil(nextw / parw*100);
                                        var maxcut = nextper - 5;
                                        if(maxcut - tocut > 5){
                                            nextper -= tocut;
                                            next[0].style[prop] = nextper + '%';
                                            if(next[0].getAttribute('data-width')) {
                                                next[0].setAttribute('data-width', nextper);
                                            }
                                        }
                                        else {
                                            percent = maxper + maxcut;
                                            next[0].style[prop] = '5%';
                                            if(next[0].getAttribute('data-width')) {
                                                next[0].setAttribute('data-width', 5);
                                            }
                                        }

                                    }else{
                                        percent = maxper;
                                    }

                                } else{
                                    if(percent < 5)
                                        percent =5;
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nextw = next.outerWidth();
                                        var nextper = Math.ceil(nextw / parw*100);
                                        var tocomplete = 100 - (sibPer + percent);
                                        nextper += tocomplete;
                                        next[0].style[prop] = nextper + '%';
                                        if(next[0].getAttribute('data-width')) {
                                            next[0].setAttribute('data-width', nextper);
                                        }
                                    }
                                }
                            }
                            element[0].style[prop] = percent + '%';
                            if(element[0].getAttribute('data-width'))
                                element[0].setAttribute('data-width',percent);
                            break;
                    }
                } else{
                    switch(dragDir) {
                        case 'top':
                            prop = scope.rFlex ? flexBasis : 'height';
                            var pix = h + (offset * vy);
                            if(type == 'fullCol'){
                                var maxpix = parh;
                                var sibs = element.siblings().not('[r-ignore]');
                                for(var i=0; i < sibs.length; i++){
                                    var sibh = sibs.eq(i).outerHeight();
                                    maxpix -= sibh;
                                }
                                if(pix > maxpix){
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = pix - maxpix;
                                        var nextpix = next.outerHeight();
                                        var maxcut = nextpix - 15
                                        if(maxcut - tocut > 115){
                                            nextpix -= tocut;
                                            next[0].style[prop] = nextpix + '%';
                                        }
                                        else {
                                            pix = maxpix + maxcut;
                                            next[0].style[prop] = '15px';
                                        }

                                    }else{
                                        pix = maxpix;
                                    }

                                } else{
                                    if(maxpix < 15)
                                        pix =15;
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nextpix = next.outerHeight();
                                        var tocomplete = parh - (sibpix + pix);
                                        nextpix += tocomplete;
                                        next[0].style[prop] = nextpix + 'px';
                                    }
                                }
                            }
                            element[0].style[prop] = pix + 'px';
                            break;
                        case 'bottom':
                            prop = scope.rFlex ? flexBasis : 'height';
                            var pix = h - (offset * vy);
                            if(type == 'fullCol'){
                                var maxpix = parh;
                                var sibs = element.siblings().not('[r-ignore]');
                                for(var i=0; i < sibs.length; i++){
                                    var sibh = sibs.eq(i).outerHeight();
                                    maxpix -= sibh;
                                }
                                if(pix > maxpix){
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = pix - maxpix;
                                        var nextpix = next.outerHeight();
                                        var maxcut = nextpix - 15
                                        if(maxcut - tocut > 115){
                                            nextpix -= tocut;
                                            next[0].style[prop] = nextpix + '%';
                                        }
                                        else {
                                            pix = maxpix + maxcut;
                                            next[0].style[prop] = '15px';
                                        }

                                    }else{
                                        pix = maxpix;
                                    }

                                } else{
                                    if(maxpix < 15)
                                        pix =15;
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nextpix = next.outerHeight();
                                        var tocomplete = parh - (sibpix + pix);
                                        nextpix += tocomplete;
                                        next[0].style[prop] = nextpix + 'px';
                                    }
                                }
                            }
                            element[0].style[prop] =  pix + 'px';
                            break;
                        case 'right':
                            prop = scope.rFlex ? flexBasis : 'width';
                            var pix = w - (offset * vx);
                            if(type == 'fullRow'){
                                var maxpix = parw;
                                var sibs = element.siblings().not('[r-ignore]');
                                for(var i=0; i < sibs.length; i++){
                                    var sibw = sibs.eq(i).outerWidth();
                                    maxpix -= sibw;
                                }
                                if(pix > maxpix){
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = pix - maxpix;
                                        var nextpix = next.outerWidth();
                                        var maxcut = nextpix - 15
                                        if(maxcut - tocut > 115){
                                            nextpix -= tocut;
                                            next[0].style[prop] = nextpix + '%';
                                        }
                                        else {
                                            pix = maxpix + maxcut;
                                            next[0].style[prop] = '15px';
                                        }

                                    }else{
                                        pix = maxpix;
                                    }

                                } else{
                                    if(maxpix < 15)
                                        pix =15;
                                    next = element.nextAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nextpix = next.outerWidth();
                                        var tocomplete = parw - (sibpix + pix);
                                        nextpix += tocomplete;
                                        next[0].style[prop] = nextpix + 'px';
                                    }
                                }
                            }
                            element[0].style[prop] =  pix + 'px';
                            break;
                        case 'left':
                            prop = scope.rFlex ? flexBasis : 'width';
                            var pix = w + (offset * vx);
                            if(type == 'fullRow'){
                                var maxpix = parw;
                                var sibs = element.siblings().not('[r-ignore]');
                                for(var i=0; i < sibs.length; i++){
                                    var sibw = sibs.eq(i).outerWidth();
                                    maxpix -= sibw;
                                }
                                if(pix > maxpix){
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next){
                                        var tocut = pix - maxpix;
                                        var nextpix = next.outerWidth();
                                        var maxcut = nextpix - 15
                                        if(maxcut - tocut > 115){
                                            nextpix -= tocut;
                                            next[0].style[prop] = nextpix + '%';
                                        }
                                        else {
                                            pix = maxpix + maxcut;
                                            next[0].style[prop] = '15px';
                                        }

                                    }else{
                                        pix = maxpix;
                                    }

                                } else{
                                    if(maxpix < 15)
                                        pix =15;
                                    next = element.prevAll().not('[r-ignore]').first();
                                    if(next) {
                                        var nextpix = next.outerWidth();
                                        var tocomplete = parw - (sibpix + pix);
                                        nextpix += tocomplete;
                                        next[0].style[prop] = nextpix + 'px';
                                    }
                                }
                            }
                            element[0].style[prop] =  pix + 'px';
                            break;
                    }
                }

                updateInfo(e,next);
                throttle(function() { scope.$emit('abt-resizable.resizing', info);});
            };
            var dragEnd = function(e) {
                updateInfo();
                scope.$emit('abt-resizable.resizeEnd', info);
                scope.$apply();
                document.removeEventListener('mouseup', dragEnd, false);
                document.removeEventListener('mousemove', dragging, false);
                element.removeClass('no-transition');
            };
            var dragStart = function(e, direction) {
                dragDir = direction;
                axis = dragDir === 'left' || dragDir === 'right' ? 'x' : 'y';
                start = axis === 'x' ? e.clientX : e.clientY;
                w = parseInt(style.getPropertyValue('width'));
                h = parseInt(style.getPropertyValue('height'));

                //prevent transition while dragging
                element.addClass('no-transition');

                document.addEventListener('mouseup', dragEnd, false);
                document.addEventListener('mousemove', dragging, false);

                // Disable highlighting while dragging
                if(e.stopPropagation) e.stopPropagation();
                if(e.preventDefault) e.preventDefault();
                e.cancelBubble = true;
                e.returnValue = false;

                updateInfo(e);
                scope.$emit('abt-resizable.resizeStart', info);
                scope.$apply();
            };

            dir.forEach(function (direction) {
                var grabber = document.createElement('div');

                // add class for styling purposes
                grabber.setAttribute('class', 'rg-' + direction);
                grabber.innerHTML = inner;
                element[0].appendChild(grabber);
                grabber.ondragstart = function() { return false; };
                grabber.addEventListener('mousedown', function(e) {
                    var disabled = (scope.rDisabled === 'true');
                    if (!disabled && e.which === 1) {
                        // left mouse click
                        dragStart(e, direction);
                    }
                }, false);
            });
        }
    };
});
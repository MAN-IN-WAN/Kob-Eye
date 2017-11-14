/**
 * @Author: Geoffrey Bauduin <bauduin.geo@gmail.com>
 */

angular.module("ion.rangeslider", []);

angular.module("ion.rangeslider").directive("ionRangeSlider",
    ['$timeout', function($timeout) {
        return {
            restrict: "A",
            scope: {
                min: "=",
                max: "=",
                from: "=",
                to: "=?",
                disable: "=",
                values: "=",

                stype: "@",
                step: "@",
                minInterval: "@",
                maxInterval: "@",
                dragInterval: "@",                
                fromFixed: "@",
                fromMin: "@",
                fromMax: "@",
                fromShadow: "@",
                toFixed: "@",
                toMax: "@",
                toShadow: "@",
                prettifyEnabled: "@",
                prettifySeparator: "@",
                forceEdges: "@",
                keyboard: "@",
                keyboardStep: "@",
                grid: "@",
                gridMargin: "@",
                gridNum: "@",
                gridSnap: "@",
                hideMinMax: "@",
                hideFromTo: "@",
                prefix: "@",
                postfix: "@",
                maxPostfix: "@",
                decorateBoth: "@",
                valuesSeparator: "@",
                inputValuesSeparator: "@",
                dragging: "@",

                prettify: "&",
                onChange: "&",
                onFinish: "&",
            },
            replace: true,
            link: function($scope, $element, attrs) {
                $element.ionRangeSlider({
                    min: $scope.min,
                    max: $scope.max,
                    from: $scope.from,
                    to: $scope.to,
                    disable: $scope.disable,
                    type: $scope.stype,
                    step: $scope.step,
                    min_interval: $scope.minInterval,
                    max_interval: $scope.maxInterval,
                    drag_interval: $scope.dragInterval,
                    values: $scope.values,
                    from_fixed: $scope.fromFixed,
                    from_min: $scope.fromMin,
                    from_max: $scope.fromMax,
                    from_shadow: $scope.fromShadow,
                    to_fixed: $scope.toFixed,
                    to_max: $scope.toMax,
                    to_shadow: $scope.toShadow,
                    prettify_enabled: $scope.prettifyEnabled,
                    prettify_separator: $scope.prettifySeparator,
                    force_edges: $scope.forceEdges,
                    keyboard: $scope.keyboard,
                    keyboard_step: $scope.keyboardStep,
                    grid: $scope.grid,
                    grid_margin: $scope.gridMargin,
                    grid_num: $scope.gridNum,
                    grid_snap: $scope.gridSnap,
                    hide_min_max: $scope.hideMinMax,
                    hide_from_to: $scope.hideFromTo,
                    prefix: $scope.prefix,
                    postfix: $scope.postfix,
                    max_postfix: $scope.maxPostfix,
                    decorate_both: $scope.decorateBoth,
                    values_separator: $scope.valuesSeparator,
                    input_values_separator: $scope.inputValuesSeparator,

                    prettify: function(value) {
                        if (!attrs.prettify) {
                            return value;
                        }
                        $scope.prettify = eval(attrs.prettify);

                        return $scope.prettify( value );
                    },
                    onDrag: function(a) {
                        $scope.dragging = true;
                        console.log('pouet');
                    },
                    onFinish: function(a) {
                        $scope.dragging = false;
                        $scope.from = a.from;
                        $scope.to = a.to;

                        $timeout(function() {
                            if (!attrs.onFinish) {
                                return true;
                            }

                            $scope.onFinish = eval(attrs.onFinish);

                            return $scope.onFinish();
                        });
                    }
                });

                var watchers = [];
                $scope.modWatch = [];
                watchers.push($scope.$watch("min", function(value) {
                    $element.data("ionRangeSlider").update({
                        min: value
                    });
                }));
                watchers.push($scope.$watch('max', function(value) {
                    $element.data("ionRangeSlider").update({
                        max: value
                    });
                }));
                watchers.push($scope.$watch('from', function(value) {
                    if($scope.dragging === true)
                        return false;

                    var slider = $element.data("ionRangeSlider");
                    if (slider.old_from !== value) {
                        slider.update({
                            from: value
                        });
                    }
                }));
                watchers.push($scope.$watch('to', function(value) {
                    if($scope.dragging === true)
                        return false;

                    var slider = $element.data("ionRangeSlider");
                    if (slider.old_to !== value) {
                        slider.update({
                            to: value
                        });
                    }
                }));
                watchers.push($scope.$watch('disable', function(value) {
                    $element.data("ionRangeSlider").update({
                        disable: value
                    });
                }));
            }
        };

    }]
);

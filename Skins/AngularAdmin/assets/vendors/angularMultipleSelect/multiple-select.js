angular.module("templates", []).run(["$templateCache", function($templateCache) {$templateCache.put("multiple-autocomplete-tpl.html",
		"<div class=\"ng-ms form-item-container\">\r\n    \n\
			<ul class=\"list-inline\">\r\n        \n\
				<li>\r\n            \n\
					<input name=\"{{name}}\" ng-model=\"inputValue\" placeholder=\"\" ng-keydown=\"keyParser($event)\"\r\n                   err-msg-required=\"{{errMsgRequired}}\"\r\n                   ng-focus=\"onFocus()\" ng-blur=\"onBlur()\" ng-required=\"!modelArr.length && isRequired\"\r\n                    ng-change=\"onChange()\">\r\n        \n\
				</li>\r\n    \n\
				<li ng-repeat=\"id in modelArr\">\r\n			\n\
					<span ng-if=\"objectProperty == undefined || objectProperty == \'\'\">\r\n				\n\
						{{id}} \n\
						<span class=\"remove\" ng-click=\"removeAddedValues(id)\">\r\n                \n\
							<i class=\"glyphicon glyphicon-remove\"></i>\n\
						</span>&nbsp;\r\n			\n\
					</span>\r\n            \n\
					<span ng-if=\"objectProperty != undefined && objectProperty != \'\'\">\r\n				\n\
						{{findLabelById(id)}} \n\
						<span class=\"remove\" ng-click=\"removeAddedValues(id)\">\r\n                \n\
							<i class=\"glyphicon glyphicon-remove\">X</i>\n\
						</span>&nbsp;\r\n			\n\
					</span>\r\n        \n\
				</li>\r\n        \n\
			</ul>\r\n    \r\n    \n\
			<div class=\"autocomplete-list\" ng-show=\"isFocused || isHover\" ng-mouseenter=\"onMouseEnter()\" ng-mouseleave=\"onMouseLeave()\">\r\n        \n\
				<ul ng-if=\"objectProperty == undefined || objectProperty == \'\'\">\r\n            \n\
					<li ng-class=\"{\'autocomplete-active\' : selectedItemIndex == $index}\"\r\n                ng-repeat=\"suggestion in suggestionsArr | filter : inputValue | filter : alreadyAddedValues\"\r\n                ng-click=\"onSuggestedItemsClick(suggestion)\" ng-mouseenter=\"mouseEnterOnItem($index)\">\r\n                {{suggestion}}\r\n            \n\
					</li>\r\n        \n\
				</ul>\r\n        \n\
				<ul ng-if=\"objectProperty != undefined && objectProperty != \'\'\">\r\n            \n\
					<li ng-class=\"{\'autocomplete-active\' : selectedItemIndex == $index}\"\r\n                ng-repeat=\"suggestion in suggestionsArr | filter : inputValue | filter : alreadyAddedValues\"\r\n                ng-click=\"onSuggestedItemsClick(suggestion)\" ng-mouseenter=\"mouseEnterOnItem($index)\">\r\n                {{suggestion[objectProperty]}}\r\n            \n\
					</li>\r\n        \n\
				</ul>\r\n    \n\
			</div>\r\n\r\n\n\
		</div>\r\n");}]);
(function () {
    //declare all modules and their dependencies.
    angular.module('multipleSelect', [
        'templates'
    ]).config(function () {

    });
}
)();
(function () {

    angular.module('multipleSelect').directive('multipleAutocomplete', [
        '$filter',
        '$http',
        function ($filter, $http) {
            return {
                restrict: 'EA',
                scope : {
                    suggestionsArr : '=?',
                    modelArr : '=ngModel',
                    apiUrl : '@',
                    apiUrlOption : '=?',
                    beforeSelectItem : '=?',
                    afterSelectItem : '=?',
                    beforeRemoveItem : '=?',
                    afterRemoveItem : '=?',
					modelLabels : '=?'
                },
                templateUrl: 'multiple-autocomplete-tpl.html',
                link : function(scope, element, attr){
                    scope.objectProperty = attr.objectProperty;
                    scope.selectedItemIndex = 0;
                    scope.name = attr.name;
                    scope.isRequired = attr.required;
                    scope.errMsgRequired = attr.errMsgRequired;
                    scope.isHover = false;
                    scope.isFocused = false;

                    var getSuggestionsList = function () {
                        var url = scope.apiUrl;
                        var method = (scope.apiUrlOption && scope.apiUrlOption.method) || "GET";
                        var responseInterceptor = (scope.apiUrlOption && scope.apiUrlOption.responseInterceptor);
                        $http({
                            method: method,
                            url: url
                        }).then(function (response) {
                            if(responseInterceptor && typeof responseInterceptor == "function"){
                                responseInterceptor(response);
                            }
                            scope.suggestionsArr = response.data;
                        }, function (response) {
                            console.log("*****Angular-multiple-select **** ----- Unable to fetch list");
                        });
                    };

                    if(scope.suggestionsArr == null || scope.suggestionsArr == ""){
                        if(scope.apiUrl != null && scope.apiUrl != "")
                            getSuggestionsList();
                        else{
                            console.log("*****Angular-multiple-select **** ----- Please provide suggestion array list or url");
                        }
                    }

                    if(scope.modelArr == null || scope.modelArr == ""){
                        scope.modelArr = [];
                    }
                    scope.onFocus = function () {
                        scope.isFocused=true
                    };

                    scope.onMouseEnter = function () {
                        scope.isHover = true
                    };

                    scope.onMouseLeave = function () {
                        scope.isHover = false;
                    };

                    scope.onBlur = function () {
                        scope.isFocused=false;
                    };

                    scope.onChange = function () {
                        scope.selectedItemIndex = 0;
                    };

                    scope.keyParser = function ($event) {
                        var keys = {
                            38: 'up',
                            40: 'down',
                            8 : 'backspace',
                            13: 'enter',
                            9 : 'tab',
                            27: 'esc'
                        };
                        var key = keys[$event.keyCode];
                        if(key == 'backspace' && scope.inputValue == ""){
                            if(scope.modelArr.length != 0){
                                scope.removeAddedValues(scope.modelArr[scope.modelArr.length-1]);
                                //scope.modelArr.pop();
                            }
                        }
                        else if(key == 'down'){
                            var filteredSuggestionArr = $filter('filter')(scope.suggestionsArr, scope.inputValue);
                            filteredSuggestionArr = $filter('filter')(filteredSuggestionArr, scope.alreadyAddedValues);
                            if(scope.selectedItemIndex < filteredSuggestionArr.length -1)
                                scope.selectedItemIndex++;
                        }
                        else if(key == 'up' && scope.selectedItemIndex > 0){
                            scope.selectedItemIndex--;
                        }
                        else if(key == 'esc'){
                            scope.isHover = false;
                            scope.isFocused=false;
                        }
                        else if(key == 'enter'){
                            var filteredSuggestionArr = $filter('filter')(scope.suggestionsArr, scope.inputValue);
                            filteredSuggestionArr = $filter('filter')(filteredSuggestionArr, scope.alreadyAddedValues);
                            if(scope.selectedItemIndex < filteredSuggestionArr.length)
                                scope.onSuggestedItemsClick(filteredSuggestionArr[scope.selectedItemIndex]);
                        }
                    };

                    scope.onSuggestedItemsClick = function (selectedValue) {
                        if(scope.beforeSelectItem && typeof(scope.beforeSelectItem) == 'function')
                            scope.beforeSelectItem(selectedValue);

                        scope.modelArr.push(selectedValue.id);
						if(scope.objectProperty != undefined && scope.objectProperty != '' && scope.modelLabels != undefined && Array.isArray(scope.modelLabels)) scope.modelLabels.push(selectedValue[scope.objectProperty]);

                        if(scope.afterSelectItem && typeof(scope.afterSelectItem) == 'function')
                            scope.afterSelectItem(selectedValue);
                        scope.inputValue = "";

                        if(scope.suggestionsArr.length == scope.modelArr.length){
                            scope.isHover = false;
                        }
                    };

                    var isDuplicate = function (arr, item) {
                        var duplicate = false;
                        if(arr == null || arr == "")
                            return duplicate;

                        for(var i=0;i<arr.length;i++){
                            duplicate = angular.equals(arr[i], item.id);
                            if(duplicate)
                                break;
                        }
                        return duplicate;
                    };

                    scope.alreadyAddedValues = function (item) {
                        var isAdded = true;
                        isAdded = !isDuplicate(scope.modelArr, item);
                        //if(scope.modelArr != null && scope.modelArr != ""){
                        //    isAdded = scope.modelArr.indexOf(item) == -1;
                        //    console.log("****************************");
                        //    console.log(item);
                        //    console.log(scope.modelArr);
                        //    console.log(isAdded);
                        //}
                        return isAdded;
                    };

                    scope.removeAddedValues = function (item) {
                        if(scope.modelArr != null && scope.modelArr != "") {
                            var itemIndex = scope.modelArr.indexOf(item);
                            if (itemIndex != -1) {
                                if(scope.beforeRemoveItem && typeof(scope.beforeRemoveItem) == 'function')
                                    scope.beforeRemoveItem(item);

                                scope.modelArr.splice(itemIndex, 1);
								if(scope.modelLabels != undefined && Array.isArray(scope.modelLabels)) scope.modelLabels.splice(itemIndex, 1);

                                if(scope.afterRemoveItem && typeof(scope.afterRemoveItem) == 'function')
                                    scope.afterRemoveItem(item);
                            }
                        }
                    };

                    scope.mouseEnterOnItem = function (index) {
                        scope.selectedItemIndex = index;
                    };
					
					scope.findLabelById = function(id){
						var prop = scope.objectProperty;
						if(scope.modelLabels != undefined && Array.isArray(scope.modelLabels)) {
							var idx = scope.modelArr.indexOf(id);
							if(idx >= 0) return scope.modelLabels[idx];
						} else {
							var sa = scope.suggestionsArr;
							var len = sa.length;
							for(var idx =0; idx < len; idx++){
								if(sa[idx].id == id) return sa[idx][prop];
							}
						}
						return 'N.D.';
					}
                }
            };
        }
    ]);
})();
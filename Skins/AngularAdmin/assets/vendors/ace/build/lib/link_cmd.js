"use strict";function _interopRequireWildcard(e){if(e&&e.__esModule)return e;var r={};if(null!=e)for(var t in e)Object.prototype.hasOwnProperty.call(e,t)&&(r[t]=e[t]);return r["default"]=e,r}function _interopRequireDefault(e){return e&&e.__esModule?e:{"default":e}}Object.defineProperty(exports,"__esModule",{value:!0});var _regenerator=require("babel-runtime/regenerator"),_regenerator2=_interopRequireDefault(_regenerator),_promise=require("babel-runtime/core-js/promise"),_promise2=_interopRequireDefault(_promise),_asyncToGenerator2=require("babel-runtime/helpers/asyncToGenerator"),_asyncToGenerator3=_interopRequireDefault(_asyncToGenerator2),_mkdirpThen=require("mkdirp-then"),_mkdirpThen2=_interopRequireDefault(_mkdirpThen),_path=require("path"),_path2=_interopRequireDefault(_path),_link=require("./link"),link=_interopRequireWildcard(_link),_config=require("./config"),_config2=_interopRequireDefault(_config);exports["default"]=function(){function e(e,t){return r.apply(this,arguments)}var r=(0,_asyncToGenerator3["default"])(_regenerator2["default"].mark(function t(e,r){var n,a=this;return _regenerator2["default"].wrap(function(t){for(;;)switch(t.prev=t.next){case 0:if(n=r._.slice(1),n.length){t.next=10;break}return t.next=4,(0,_mkdirpThen2["default"])(_config2["default"].globalNodeModules);case 4:return t.next=6,(0,_mkdirpThen2["default"])(_config2["default"].globalBin);case 6:return t.next=8,link.linkToGlobal(e);case 8:t.next=13;break;case 10:return t.next=12,(0,_mkdirpThen2["default"])(_path2["default"].join(e,"node_modules"));case 12:return t.abrupt("return",_promise2["default"].all(n.map(function(){var r=(0,_asyncToGenerator3["default"])(_regenerator2["default"].mark(function t(r){return _regenerator2["default"].wrap(function(t){for(;;)switch(t.prev=t.next){case 0:return t.abrupt("return",link.linkFromGlobal(e,r));case 1:case"end":return t.stop()}},t,a)}));return function(e){return r.apply(this,arguments)}}())));case 13:case"end":return t.stop()}},t,this)}));return e}();
"use strict";function _interopRequireDefault(e){return e&&e.__esModule?e:{"default":e}}function initCmd(e,i){var t=_path2["default"].resolve(home,".ace-init");return new _promise2["default"](function(i,n){(0,_initPackageJson2["default"])(e,t,function(e,t){e&&("canceled"===e.message&&console.log("init canceled!"),n(e)),i()})})}Object.defineProperty(exports,"__esModule",{value:!0});var _promise=require("babel-runtime/core-js/promise"),_promise2=_interopRequireDefault(_promise);exports["default"]=initCmd;var _initPackageJson=require("init-package-json"),_initPackageJson2=_interopRequireDefault(_initPackageJson),_path=require("path"),_path2=_interopRequireDefault(_path),isWindows="win32"===process.platform,home=process.env[isWindows?"USERPROFILE":"HOME"];
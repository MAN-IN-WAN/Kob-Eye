"use strict";function _interopRequireDefault(e){return e&&e.__esModule?e:{"default":e}}function resolve(e,r){debug("resolving %s@%s",e,r);var t=e+"@"+r,a=(0,_npmPackageArg2["default"])(t);switch(a.type){case"range":case"version":case"tag":return(0,_npmRegistry2["default"])(e,r,a);case"hosted":var u=a.hosted.directUrl,l=_url2["default"].parse(u),n=l.pathname.split("/");return n.pop(),n.splice(n.length-2,1,n[n.length-2],"archive"),n[n.length-1]+=".tar.gz",l.protocol="https:",l.host="github.com",l.pathname=n.join("/"),r=_url2["default"].format(l),a.spec=r,(0,_tarball2["default"])(e,r,a.spec);case"remote":return(0,_tarball2["default"])(e,r,a.spec);default:throw new Error("Unknown package spec: "+a.type+" on "+t)}}Object.defineProperty(exports,"__esModule",{value:!0}),exports["default"]=resolve;var _url=require("url"),_url2=_interopRequireDefault(_url),_npmPackageArg=require("npm-package-arg"),_npmPackageArg2=_interopRequireDefault(_npmPackageArg),_debug=require("debug"),_debug2=_interopRequireDefault(_debug),_npmRegistry=require("./resolvers/npm-registry"),_npmRegistry2=_interopRequireDefault(_npmRegistry),_tarball=require("./resolvers/tarball"),_tarball2=_interopRequireDefault(_tarball),debug=(0,_debug2["default"])("resolve");
/*
 *****************************************************************************
    Copyright (c) Microsoft Corporation. All rights reserved.
    Licensed under the Apache License, Version 2.0 (the "License"); you may not use
    this file except in compliance with the License. You may obtain a copy of the
    License at http://www.apache.org/licenses/LICENSE-2.0

    THIS CODE IS PROVIDED ON AN *AS IS* BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
    KIND, EITHER EXPRESS OR IMPLIED, INCLUDING WITHOUT LIMITATION ANY IMPLIED
    WARRANTIES OR CONDITIONS OF TITLE, FITNESS FOR A PARTICULAR PURPOSE,
    MERCHANTABLITY OR NON-INFRINGEMENT.

    See the Apache Version 2.0 License for specific language governing permissions
    and limitations under the License.
*****************************************************************************/
'use strict';(function(d,a){"object"===typeof exports&&"undefined"!==typeof module?a(exports,require("react")):"function"===typeof define&&define.amd?define(["exports","react"],a):(d=d||self,a(d.ReactStickyNav={},d.React))})(this,function(d,a){function x(a,e){var b={},c;for(c in a)Object.prototype.hasOwnProperty.call(a,c)&&0>e.indexOf(c)&&(b[c]=a[c]);if(null!=a&&"function"===typeof Object.getOwnPropertySymbols){var g=0;for(c=Object.getOwnPropertySymbols(a);g<c.length;g++)0>e.indexOf(c[g])&&(b[c[g]]=
a[c[g]])}return b}var y=Object.assign||function(a){for(var e,b=1,c=arguments.length;b<c;b++){e=arguments[b];for(var g in e)Object.prototype.hasOwnProperty.call(e,g)&&(a[g]=e[g])}return a},u={position:"sticky"},z=function(){for(var f=[],e=0;e<arguments.length;e++)f[e]=arguments[e];var b=a.useRef(null);a.useEffect(function(){f.forEach(function(a){a&&("function"===typeof a?a(b.current):a.current=b.current)})},[f]);return b},h=a.forwardRef(function(f,e){var b=f.children,c=f.disabled,g=f.render;f=x(f,
["children","disabled","render"]);var d=a.useState("unfixed"),h=d[0],q=d[1],r=a.useRef(0),v=a.useRef(0),k=a.useState(0);d=k[0];var A=k[1];k=a.useRef(null);var l=z(e,k),m=a.useRef(null),n=a.useCallback(function(){if(l.current&&!c){var a=window.pageYOffset;if(!(0>a)){var b=l.current.classList,e=0<a-r.current?"down":"up",d=v.current+r.current-a,f=l.current.getBoundingClientRect(),h=f.height;f=f.top;var k="down"===e?Math.max(d,-h):Math.min(d,0);v.current=k;l.current.style.top=k.toString();g&&A(k);"down"===
e&&!b.contains("hidden")&&d<-h&&(q("hidden"),b.remove("pinned","unfixed"),b.add("hidden"));"up"===e&&!b.contains("pinned")&&d>-h&&(q("pinned"),b.remove("hidden","unfixed"),b.add("pinned"));!b.contains("unfixed")&&(0<f||0===a)&&(q("unfixed"),b.remove("hidden","pinned"),b.add("unfixed"));r.current=a;m.current=null}}},[c,l,g]),p=a.useCallback(function(){m.current&&window.cancelAnimationFrame(m.current);m.current=window.requestAnimationFrame(n)},[n]),w=a.useCallback(function(){"undefined"!==typeof window&&
window.addEventListener("scroll",p)},[p]),t=a.useCallback(function(){"undefined"!==typeof window&&window.removeEventListener("scroll",p)},[p]);a.useEffect(function(){c?t():w();return function(){return t()}},[c,w,t]);a.useEffect(function(){c||n()},[c,n]);return g?g({position:h,ref:l,top:d}):a.createElement("nav",y({},f,{ref:l,style:u}),"function"===typeof b?b(h):b)});h=a.memo(h);d.default=h;d.styles=u;Object.defineProperty(d,"__esModule",{value:!0})})

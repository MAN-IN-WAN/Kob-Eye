import{memo,forwardRef,useState,useRef,useCallback,useEffect,createElement}from'react';/*
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
var r=Object.assign||function(a){for(var d,b=1,c=arguments.length;b<c;b++){d=arguments[b];for(var e in d)Object.prototype.hasOwnProperty.call(d,e)&&(a[e]=d[e])}return a};function w(a,d){var b={},c;for(c in a)Object.prototype.hasOwnProperty.call(a,c)&&0>d.indexOf(c)&&(b[c]=a[c]);if(null!=a&&"function"===typeof Object.getOwnPropertySymbols){var e=0;for(c=Object.getOwnPropertySymbols(a);e<c.length;e++)0>d.indexOf(c[e])&&(b[c[e]]=a[c[e]])}return b}var x={position:"sticky"};
function y(){for(var a=[],d=0;d<arguments.length;d++)a[d]=arguments[d];var b=useRef(null);useEffect(function(){a.forEach(function(c){c&&("function"===typeof c?c(b.current):c.current=b.current)})},[a]);return b}
var A=forwardRef(function(a,d){var b=a.children,c=a.disabled,e=a.render;a=w(a,["children","disabled","render"]);var f=useState("unfixed"),t=f[0],n=f[1],p=useRef(0),u=useRef(0),g=useState(0);f=g[0];var z=g[1];g=useRef(null);var l=y(d,g),m=useRef(null),h=useCallback(function(){if(l.current&&!c){var a=window.pageYOffset;if(!(0>a)){var b=l.current.classList,d=0<a-p.current?"down":"up",f=u.current+p.current-a,g=l.current.getBoundingClientRect(),h=g.height;g=g.top;var k="down"===d?Math.max(f,-h):Math.min(f,
0);u.current=k;l.current.style.top=k.toString();e&&z(k);"down"===d&&!b.contains("hidden")&&f<-h&&(n("hidden"),b.remove("pinned","unfixed"),b.add("hidden"));"up"===d&&!b.contains("pinned")&&f>-h&&(n("pinned"),b.remove("hidden","unfixed"),b.add("pinned"));!b.contains("unfixed")&&(0<g||0===a)&&(n("unfixed"),b.remove("hidden","pinned"),b.add("unfixed"));p.current=a;m.current=null}}},[c,l,e]),k=useCallback(function(){m.current&&window.cancelAnimationFrame(m.current);m.current=window.requestAnimationFrame(h)},
[h]),v=useCallback(function(){"undefined"!==typeof window&&window.addEventListener("scroll",k)},[k]),q=useCallback(function(){"undefined"!==typeof window&&window.removeEventListener("scroll",k)},[k]);useEffect(function(){c?q():v();return function(){return q()}},[c,v,q]);useEffect(function(){c||h()},[c,h]);return e?e({position:t,ref:l,top:f}):createElement("nav",r({},a,{ref:l,style:x}),"function"===typeof b?b(t):b)}),B=memo(A);export default B;export{x as styles}

"use strict";function complete(e){completed+=e,draw(),started===completed&&clear()}function start(e){started+=e,draw()}function draw(){if(!process.env.NODE_DEBUG&&isTTY){var e=started?completed/started:0,r=Math.ceil(1e4*e)/100;process.stderr.cursorTo(0);for(var t=Math.floor(columns*e),s="[",o=0;columns>o;o++)s+=t>=o?"=":" ";s+="] ",process.stderr.write(s+r.toFixed(2)+"%"),process.stderr.clearLine(1)}}function clear(){isTTY&&(process.stderr.clearLine(),process.stderr.cursorTo(0))}Object.defineProperty(exports,"__esModule",{value:!0}),exports.complete=complete,exports.start=start,exports.draw=draw,exports.clear=clear;var isTTY=process.stderr.isTTY,completed=0,started=0,columns=30;
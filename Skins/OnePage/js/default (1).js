// Default JavaScript Functions and Initiations

// Add JS Support
var html = document.querySelector('html');
html.className = html.className.replace(/\bno-js\b/, "js");

// Setup WOW.js
var wow = new WOW({
  boxClass:     'animate-block',
  animateClass: 'active',
  offset:       1,
  mobile:       true,
  live:         true
});
// Initiate WOW.js
wow.init();
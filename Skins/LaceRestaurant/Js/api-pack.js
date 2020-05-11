(function () {
/*!
 * API-PACK generated on 09/10/2013 - 10:38:58
 */
////
//// mappy.js
//// 

var g_domain = 'mappy';


////
//// jquery.js
//// 

/*!
 * jQuery JavaScript Library v1.5.1
 * http://jquery.com/
 *
 * Copyright 2011, John Resig
 * Dual licensed under the MIT or GPL Version 2 licenses.
 * http://jquery.org/license
 *
 * Includes Sizzle.js
 * http://sizzlejs.com/
 * Copyright 2011, The Dojo Foundation
 * Released under the MIT, BSD, and GPL Licenses.
 *
 * Date: Wed Feb 23 13:55:29 2011 -0500
 */
(function( window, undefined ) {

// Use the correct document accordingly with window argument (sandbox)
var document = window.document;
var jQuery = (function() {

// Define a local copy of jQuery
var jQuery = function( selector, context ) {
		// The jQuery object is actually just the init constructor 'enhanced'
		return new jQuery.fn.init( selector, context, rootjQuery );
	},

	// Map over jQuery in case of overwrite
	_jQuery = window.jQuery,

	// Map over the $ in case of overwrite
	_$ = window.$,

	// A central reference to the root jQuery(document)
	rootjQuery,

	// A simple way to check for HTML strings or ID strings
	// (both of which we optimize for)
	quickExpr = /^(?:[^<]*(<[\w\W]+>)[^>]*$|#([\w\-]+)$)/,

	// Check if a string has a non-whitespace character in it
	rnotwhite = /\S/,

	// Used for trimming whitespace
	trimLeft = /^\s+/,
	trimRight = /\s+$/,

	// Check for digits
	rdigit = /\d/,

	// Match a standalone tag
	rsingleTag = /^<(\w+)\s*\/?>(?:<\/\1>)?$/,

	// JSON RegExp
	rvalidchars = /^[\],:{}\s]*$/,
	rvalidescape = /\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g,
	rvalidtokens = /"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g,
	rvalidbraces = /(?:^|:|,)(?:\s*\[)+/g,

	// Useragent RegExp
	rwebkit = /(webkit)[ \/]([\w.]+)/,
	ropera = /(opera)(?:.*version)?[ \/]([\w.]+)/,
	rmsie = /(msie) ([\w.]+)/,
	rmozilla = /(mozilla)(?:.*? rv:([\w.]+))?/,

	// Keep a UserAgent string for use with jQuery.browser
	userAgent = navigator.userAgent,

	// For matching the engine and version of the browser
	browserMatch,

	// Has the ready events already been bound?
	readyBound = false,

	// The deferred used on DOM ready
	readyList,

	// Promise methods
	promiseMethods = "then done fail isResolved isRejected promise".split( " " ),

	// The ready event handler
	DOMContentLoaded,

	// Save a reference to some core methods
	toString = Object.prototype.toString,
	hasOwn = Object.prototype.hasOwnProperty,
	push = Array.prototype.push,
	slice = Array.prototype.slice,
	trim = String.prototype.trim,
	indexOf = Array.prototype.indexOf,

	// [[Class]] -> type pairs
	class2type = {};

jQuery.fn = jQuery.prototype = {
	constructor: jQuery,
	init: function( selector, context, rootjQuery ) {
		var match, elem, ret, doc;

		// Handle $(""), $(null), or $(undefined)
		if ( !selector ) {
			return this;
		}

		// Handle $(DOMElement)
		if ( selector.nodeType ) {
			this.context = this[0] = selector;
			this.length = 1;
			return this;
		}

		// The body element only exists once, optimize finding it
		if ( selector === "body" && !context && document.body ) {
			this.context = document;
			this[0] = document.body;
			this.selector = "body";
			this.length = 1;
			return this;
		}

		// Handle HTML strings
		if ( typeof selector === "string" ) {
			// Are we dealing with HTML string or an ID?
			match = quickExpr.exec( selector );

			// Verify a match, and that no context was specified for #id
			if ( match && (match[1] || !context) ) {

				// HANDLE: $(html) -> $(array)
				if ( match[1] ) {
					context = context instanceof jQuery ? context[0] : context;
					doc = (context ? context.ownerDocument || context : document);

					// If a single string is passed in and it's a single tag
					// just do a createElement and skip the rest
					ret = rsingleTag.exec( selector );

					if ( ret ) {
						if ( jQuery.isPlainObject( context ) ) {
							selector = [ document.createElement( ret[1] ) ];
							jQuery.fn.attr.call( selector, context, true );

						} else {
							selector = [ doc.createElement( ret[1] ) ];
						}

					} else {
						ret = jQuery.buildFragment( [ match[1] ], [ doc ] );
						selector = (ret.cacheable ? jQuery.clone(ret.fragment) : ret.fragment).childNodes;
					}

					return jQuery.merge( this, selector );

				// HANDLE: $("#id")
				} else {
					elem = document.getElementById( match[2] );

					// Check parentNode to catch when Blackberry 4.6 returns
					// nodes that are no longer in the document #6963
					if ( elem && elem.parentNode ) {
						// Handle the case where IE and Opera return items
						// by name instead of ID
						if ( elem.id !== match[2] ) {
							return rootjQuery.find( selector );
						}

						// Otherwise, we inject the element directly into the jQuery object
						this.length = 1;
						this[0] = elem;
					}

					this.context = document;
					this.selector = selector;
					return this;
				}

			// HANDLE: $(expr, $(...))
			} else if ( !context || context.jquery ) {
				return (context || rootjQuery).find( selector );

			// HANDLE: $(expr, context)
			// (which is just equivalent to: $(context).find(expr)
			} else {
				return this.constructor( context ).find( selector );
			}

		// HANDLE: $(function)
		// Shortcut for document ready
		} else if ( jQuery.isFunction( selector ) ) {
			return rootjQuery.ready( selector );
		}

		if (selector.selector !== undefined) {
			this.selector = selector.selector;
			this.context = selector.context;
		}

		return jQuery.makeArray( selector, this );
	},

	// Start with an empty selector
	selector: "",

	// The current version of jQuery being used
	jquery: "1.5.1",

	// The default length of a jQuery object is 0
	length: 0,

	// The number of elements contained in the matched element set
	size: function() {
		return this.length;
	},

	toArray: function() {
		return slice.call( this, 0 );
	},

	// Get the Nth element in the matched element set OR
	// Get the whole matched element set as a clean array
	get: function( num ) {
		return num == null ?

			// Return a 'clean' array
			this.toArray() :

			// Return just the object
			( num < 0 ? this[ this.length + num ] : this[ num ] );
	},

	// Take an array of elements and push it onto the stack
	// (returning the new matched element set)
	pushStack: function( elems, name, selector ) {
		// Build a new jQuery matched element set
		var ret = this.constructor();

		if ( jQuery.isArray( elems ) ) {
			push.apply( ret, elems );

		} else {
			jQuery.merge( ret, elems );
		}

		// Add the old object onto the stack (as a reference)
		ret.prevObject = this;

		ret.context = this.context;

		if ( name === "find" ) {
			ret.selector = this.selector + (this.selector ? " " : "") + selector;
		} else if ( name ) {
			ret.selector = this.selector + "." + name + "(" + selector + ")";
		}

		// Return the newly-formed element set
		return ret;
	},

	// Execute a callback for every element in the matched set.
	// (You can seed the arguments with an array of args, but this is
	// only used internally.)
	each: function( callback, args ) {
		return jQuery.each( this, callback, args );
	},

	ready: function( fn ) {
		// Attach the listeners
		jQuery.bindReady();

		// Add the callback
		readyList.done( fn );

		return this;
	},

	eq: function( i ) {
		return i === -1 ?
			this.slice( i ) :
			this.slice( i, +i + 1 );
	},

	first: function() {
		return this.eq( 0 );
	},

	last: function() {
		return this.eq( -1 );
	},

	slice: function() {
		return this.pushStack( slice.apply( this, arguments ),
			"slice", slice.call(arguments).join(",") );
	},

	map: function( callback ) {
		return this.pushStack( jQuery.map(this, function( elem, i ) {
			return callback.call( elem, i, elem );
		}));
	},

	end: function() {
		return this.prevObject || this.constructor(null);
	},

	// For internal use only.
	// Behaves like an Array's method, not like a jQuery method.
	push: push,
	sort: [].sort,
	splice: [].splice
};

// Give the init function the jQuery prototype for later instantiation
jQuery.fn.init.prototype = jQuery.fn;

jQuery.extend = jQuery.fn.extend = function() {
	var options, name, src, copy, copyIsArray, clone,
		target = arguments[0] || {},
		i = 1,
		length = arguments.length,
		deep = false;

	// Handle a deep copy situation
	if ( typeof target === "boolean" ) {
		deep = target;
		target = arguments[1] || {};
		// skip the boolean and the target
		i = 2;
	}

	// Handle case when target is a string or something (possible in deep copy)
	if ( typeof target !== "object" && !jQuery.isFunction(target) ) {
		target = {};
	}

	// extend jQuery itself if only one argument is passed
	if ( length === i ) {
		target = this;
		--i;
	}

	for ( ; i < length; i++ ) {
		// Only deal with non-null/undefined values
		if ( (options = arguments[ i ]) != null ) {
			// Extend the base object
			for ( name in options ) {
				src = target[ name ];
				copy = options[ name ];

				// Prevent never-ending loop
				if ( target === copy ) {
					continue;
				}

				// Recurse if we're merging plain objects or arrays
				if ( deep && copy && ( jQuery.isPlainObject(copy) || (copyIsArray = jQuery.isArray(copy)) ) ) {
					if ( copyIsArray ) {
						copyIsArray = false;
						clone = src && jQuery.isArray(src) ? src : [];

					} else {
						clone = src && jQuery.isPlainObject(src) ? src : {};
					}

					// Never move original objects, clone them
					target[ name ] = jQuery.extend( deep, clone, copy );

				// Don't bring in undefined values
				} else if ( copy !== undefined ) {
					target[ name ] = copy;
				}
			}
		}
	}

	// Return the modified object
	return target;
};

jQuery.extend({
	noConflict: function( deep ) {
		window.$ = _$;

		if ( deep ) {
			window.jQuery = _jQuery;
		}

		return jQuery;
	},

	// Is the DOM ready to be used? Set to true once it occurs.
	isReady: false,

	// A counter to track how many items to wait for before
	// the ready event fires. See #6781
	readyWait: 1,

	// Handle when the DOM is ready
	ready: function( wait ) {
		// A third-party is pushing the ready event forwards
		if ( wait === true ) {
			jQuery.readyWait--;
		}

		// Make sure that the DOM is not already loaded
		if ( !jQuery.readyWait || (wait !== true && !jQuery.isReady) ) {
			// Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
			if ( !document.body ) {
				return setTimeout( jQuery.ready, 1 );
			}

			// Remember that the DOM is ready
			jQuery.isReady = true;

			// If a normal DOM Ready event fired, decrement, and wait if need be
			if ( wait !== true && --jQuery.readyWait > 0 ) {
				return;
			}

			// If there are functions bound, to execute
			readyList.resolveWith( document, [ jQuery ] );

			// Trigger any bound ready events
			if ( jQuery.fn.trigger ) {
				jQuery( document ).trigger( "ready" ).unbind( "ready" );
			}
		}
	},

	bindReady: function() {
		if ( readyBound ) {
			return;
		}

		readyBound = true;

		// Catch cases where $(document).ready() is called after the
		// browser event has already occurred.
		if ( document.readyState === "complete" ) {
			// Handle it asynchronously to allow scripts the opportunity to delay ready
			return setTimeout( jQuery.ready, 1 );
		}

		// Mozilla, Opera and webkit nightlies currently support this event
		if ( document.addEventListener ) {
			// Use the handy event callback
			document.addEventListener( "DOMContentLoaded", DOMContentLoaded, false );

			// A fallback to window.onload, that will always work
			window.addEventListener( "load", jQuery.ready, false );

		// If IE event model is used
		} else if ( document.attachEvent ) {
			// ensure firing before onload,
			// maybe late but safe also for iframes
			document.attachEvent("onreadystatechange", DOMContentLoaded);

			// A fallback to window.onload, that will always work
			window.attachEvent( "onload", jQuery.ready );

			// If IE and not a frame
			// continually check to see if the document is ready
			var toplevel = false;

			try {
				toplevel = window.frameElement == null;
			} catch(e) {}

			if ( document.documentElement.doScroll && toplevel ) {
				doScrollCheck();
			}
		}
	},

	// See test/unit/core.js for details concerning isFunction.
	// Since version 1.3, DOM methods and functions like alert
	// aren't supported. They return false on IE (#2968).
	isFunction: function( obj ) {
		return jQuery.type(obj) === "function";
	},

	isArray: Array.isArray || function( obj ) {
		return jQuery.type(obj) === "array";
	},

	// A crude way of determining if an object is a window
	isWindow: function( obj ) {
		return obj && typeof obj === "object" && "setInterval" in obj;
	},

	isNaN: function( obj ) {
		return obj == null || !rdigit.test( obj ) || isNaN( obj );
	},

	type: function( obj ) {
		return obj == null ?
			String( obj ) :
			class2type[ toString.call(obj) ] || "object";
	},

	isPlainObject: function( obj ) {
		// Must be an Object.
		// Because of IE, we also have to check the presence of the constructor property.
		// Make sure that DOM nodes and window objects don't pass through, as well
		if ( !obj || jQuery.type(obj) !== "object" || obj.nodeType || jQuery.isWindow( obj ) ) {
			return false;
		}

		// Not own constructor property must be Object
		if ( obj.constructor &&
			!hasOwn.call(obj, "constructor") &&
			!hasOwn.call(obj.constructor.prototype, "isPrototypeOf") ) {
			return false;
		}

		// Own properties are enumerated firstly, so to speed up,
		// if last one is own, then all properties are own.

		var key;
		for ( key in obj ) {}

		return key === undefined || hasOwn.call( obj, key );
	},

	isEmptyObject: function( obj ) {
		for ( var name in obj ) {
			return false;
		}
		return true;
	},

	error: function( msg ) {
		throw msg;
	},

	parseJSON: function( data ) {
		if ( typeof data !== "string" || !data ) {
			return null;
		}

		// Make sure leading/trailing whitespace is removed (IE can't handle it)
		data = jQuery.trim( data );

		// Make sure the incoming data is actual JSON
		// Logic borrowed from http://json.org/json2.js
		if ( rvalidchars.test(data.replace(rvalidescape, "@")
			.replace(rvalidtokens, "]")
			.replace(rvalidbraces, "")) ) {

			// Try to use the native JSON parser first
			return window.JSON && window.JSON.parse ?
				window.JSON.parse( data ) :
				(new Function("return " + data))();

		} else {
			jQuery.error( "Invalid JSON: " + data );
		}
	},

	// Cross-browser xml parsing
	// (xml & tmp used internally)
	parseXML: function( data , xml , tmp ) {

		if ( window.DOMParser ) { // Standard
			tmp = new DOMParser();
			xml = tmp.parseFromString( data , "text/xml" );
		} else { // IE
			xml = new ActiveXObject( "Microsoft.XMLDOM" );
			xml.async = "false";
			xml.loadXML( data );
		}

		tmp = xml.documentElement;

		if ( ! tmp || ! tmp.nodeName || tmp.nodeName === "parsererror" ) {
			jQuery.error( "Invalid XML: " + data );
		}

		return xml;
	},

	noop: function() {},

	// Evalulates a script in a global context
	globalEval: function( data ) {
		if ( data && rnotwhite.test(data) ) {
			// Inspired by code by Andrea Giammarchi
			// http://webreflection.blogspot.com/2007/08/global-scope-evaluation-and-dom.html
			var head = document.head || document.getElementsByTagName( "head" )[0] || document.documentElement,
				script = document.createElement( "script" );

			if ( jQuery.support.scriptEval() ) {
				script.appendChild( document.createTextNode( data ) );
			} else {
				script.text = data;
			}

			// Use insertBefore instead of appendChild to circumvent an IE6 bug.
			// This arises when a base node is used (#2709).
			head.insertBefore( script, head.firstChild );
			head.removeChild( script );
		}
	},

	nodeName: function( elem, name ) {
		return elem.nodeName && elem.nodeName.toUpperCase() === name.toUpperCase();
	},

	// args is for internal usage only
	each: function( object, callback, args ) {
		var name, i = 0,
			length = object.length,
			isObj = length === undefined || jQuery.isFunction(object);

		if ( args ) {
			if ( isObj ) {
				for ( name in object ) {
					if ( callback.apply( object[ name ], args ) === false ) {
						break;
					}
				}
			} else {
				for ( ; i < length; ) {
					if ( callback.apply( object[ i++ ], args ) === false ) {
						break;
					}
				}
			}

		// A special, fast, case for the most common use of each
		} else {
			if ( isObj ) {
				for ( name in object ) {
					if ( callback.call( object[ name ], name, object[ name ] ) === false ) {
						break;
					}
				}
			} else {
				for ( var value = object[0];
					i < length && callback.call( value, i, value ) !== false; value = object[++i] ) {}
			}
		}

		return object;
	},

	// Use native String.trim function wherever possible
	trim: trim ?
		function( text ) {
			return text == null ?
				"" :
				trim.call( text );
		} :

		// Otherwise use our own trimming functionality
		function( text ) {
			return text == null ?
				"" :
				text.toString().replace( trimLeft, "" ).replace( trimRight, "" );
		},

	// results is for internal usage only
	makeArray: function( array, results ) {
		var ret = results || [];

		if ( array != null ) {
			// The window, strings (and functions) also have 'length'
			// The extra typeof function check is to prevent crashes
			// in Safari 2 (See: #3039)
			// Tweaked logic slightly to handle Blackberry 4.7 RegExp issues #6930
			var type = jQuery.type(array);

			if ( array.length == null || type === "string" || type === "function" || type === "regexp" || jQuery.isWindow( array ) ) {
				push.call( ret, array );
			} else {
				jQuery.merge( ret, array );
			}
		}

		return ret;
	},

	inArray: function( elem, array ) {
		if ( array.indexOf ) {
			return array.indexOf( elem );
		}

		for ( var i = 0, length = array.length; i < length; i++ ) {
			if ( array[ i ] === elem ) {
				return i;
			}
		}

		return -1;
	},

	merge: function( first, second ) {
		var i = first.length,
			j = 0;

		if ( typeof second.length === "number" ) {
			for ( var l = second.length; j < l; j++ ) {
				first[ i++ ] = second[ j ];
			}

		} else {
			while ( second[j] !== undefined ) {
				first[ i++ ] = second[ j++ ];
			}
		}

		first.length = i;

		return first;
	},

	grep: function( elems, callback, inv ) {
		var ret = [], retVal;
		inv = !!inv;

		// Go through the array, only saving the items
		// that pass the validator function
		for ( var i = 0, length = elems.length; i < length; i++ ) {
			retVal = !!callback( elems[ i ], i );
			if ( inv !== retVal ) {
				ret.push( elems[ i ] );
			}
		}

		return ret;
	},

	// arg is for internal usage only
	map: function( elems, callback, arg ) {
		var ret = [], value;

		// Go through the array, translating each of the items to their
		// new value (or values).
		for ( var i = 0, length = elems.length; i < length; i++ ) {
			value = callback( elems[ i ], i, arg );

			if ( value != null ) {
				ret[ ret.length ] = value;
			}
		}

		// Flatten any nested arrays
		return ret.concat.apply( [], ret );
	},

	// A global GUID counter for objects
	guid: 1,

	proxy: function( fn, proxy, thisObject ) {
		if ( arguments.length === 2 ) {
			if ( typeof proxy === "string" ) {
				thisObject = fn;
				fn = thisObject[ proxy ];
				proxy = undefined;

			} else if ( proxy && !jQuery.isFunction( proxy ) ) {
				thisObject = proxy;
				proxy = undefined;
			}
		}

		if ( !proxy && fn ) {
			proxy = function() {
				return fn.apply( thisObject || this, arguments );
			};
		}

		// Set the guid of unique handler to the same of original handler, so it can be removed
		if ( fn ) {
			proxy.guid = fn.guid = fn.guid || proxy.guid || jQuery.guid++;
		}

		// So proxy can be declared as an argument
		return proxy;
	},

	// Mutifunctional method to get and set values to a collection
	// The value/s can be optionally by executed if its a function
	access: function( elems, key, value, exec, fn, pass ) {
		var length = elems.length;

		// Setting many attributes
		if ( typeof key === "object" ) {
			for ( var k in key ) {
				jQuery.access( elems, k, key[k], exec, fn, value );
			}
			return elems;
		}

		// Setting one attribute
		if ( value !== undefined ) {
			// Optionally, function values get executed if exec is true
			exec = !pass && exec && jQuery.isFunction(value);

			for ( var i = 0; i < length; i++ ) {
				fn( elems[i], key, exec ? value.call( elems[i], i, fn( elems[i], key ) ) : value, pass );
			}

			return elems;
		}

		// Getting an attribute
		return length ? fn( elems[0], key ) : undefined;
	},

	now: function() {
		return (new Date()).getTime();
	},

	// Create a simple deferred (one callbacks list)
	_Deferred: function() {
		var // callbacks list
			callbacks = [],
			// stored [ context , args ]
			fired,
			// to avoid firing when already doing so
			firing,
			// flag to know if the deferred has been cancelled
			cancelled,
			// the deferred itself
			deferred  = {

				// done( f1, f2, ...)
				done: function() {
					if ( !cancelled ) {
						var args = arguments,
							i,
							length,
							elem,
							type,
							_fired;
						if ( fired ) {
							_fired = fired;
							fired = 0;
						}
						for ( i = 0, length = args.length; i < length; i++ ) {
							elem = args[ i ];
							type = jQuery.type( elem );
							if ( type === "array" ) {
								deferred.done.apply( deferred, elem );
							} else if ( type === "function" ) {
								callbacks.push( elem );
							}
						}
						if ( _fired ) {
							deferred.resolveWith( _fired[ 0 ], _fired[ 1 ] );
						}
					}
					return this;
				},

				// resolve with given context and args
				resolveWith: function( context, args ) {
					if ( !cancelled && !fired && !firing ) {
						firing = 1;
						try {
							while( callbacks[ 0 ] ) {
								callbacks.shift().apply( context, args );
							}
						}
						// We have to add a catch block for
						// IE prior to 8 or else the finally
						// block will never get executed
						catch (e) {
							throw e;
						}
						finally {
							fired = [ context, args ];
							firing = 0;
						}
					}
					return this;
				},

				// resolve with this as context and given arguments
				resolve: function() {
					deferred.resolveWith( jQuery.isFunction( this.promise ) ? this.promise() : this, arguments );
					return this;
				},

				// Has this deferred been resolved?
				isResolved: function() {
					return !!( firing || fired );
				},

				// Cancel
				cancel: function() {
					cancelled = 1;
					callbacks = [];
					return this;
				}
			};

		return deferred;
	},

	// Full fledged deferred (two callbacks list)
	Deferred: function( func ) {
		var deferred = jQuery._Deferred(),
			failDeferred = jQuery._Deferred(),
			promise;
		// Add errorDeferred methods, then and promise
		jQuery.extend( deferred, {
			then: function( doneCallbacks, failCallbacks ) {
				deferred.done( doneCallbacks ).fail( failCallbacks );
				return this;
			},
			fail: failDeferred.done,
			rejectWith: failDeferred.resolveWith,
			reject: failDeferred.resolve,
			isRejected: failDeferred.isResolved,
			// Get a promise for this deferred
			// If obj is provided, the promise aspect is added to the object
			promise: function( obj ) {
				if ( obj == null ) {
					if ( promise ) {
						return promise;
					}
					promise = obj = {};
				}
				var i = promiseMethods.length;
				while( i-- ) {
					obj[ promiseMethods[i] ] = deferred[ promiseMethods[i] ];
				}
				return obj;
			}
		} );
		// Make sure only one callback list will be used
		deferred.done( failDeferred.cancel ).fail( deferred.cancel );
		// Unexpose cancel
		delete deferred.cancel;
		// Call given func if any
		if ( func ) {
			func.call( deferred, deferred );
		}
		return deferred;
	},

	// Deferred helper
	when: function( object ) {
		var lastIndex = arguments.length,
			deferred = lastIndex <= 1 && object && jQuery.isFunction( object.promise ) ?
				object :
				jQuery.Deferred(),
			promise = deferred.promise();

		if ( lastIndex > 1 ) {
			var array = slice.call( arguments, 0 ),
				count = lastIndex,
				iCallback = function( index ) {
					return function( value ) {
						array[ index ] = arguments.length > 1 ? slice.call( arguments, 0 ) : value;
						if ( !( --count ) ) {
							deferred.resolveWith( promise, array );
						}
					};
				};
			while( ( lastIndex-- ) ) {
				object = array[ lastIndex ];
				if ( object && jQuery.isFunction( object.promise ) ) {
					object.promise().then( iCallback(lastIndex), deferred.reject );
				} else {
					--count;
				}
			}
			if ( !count ) {
				deferred.resolveWith( promise, array );
			}
		} else if ( deferred !== object ) {
			deferred.resolve( object );
		}
		return promise;
	},

	// Use of jQuery.browser is frowned upon.
	// More details: http://docs.jquery.com/Utilities/jQuery.browser
	uaMatch: function( ua ) {
		ua = ua.toLowerCase();

		var match = rwebkit.exec( ua ) ||
			ropera.exec( ua ) ||
			rmsie.exec( ua ) ||
			ua.indexOf("compatible") < 0 && rmozilla.exec( ua ) ||
			[];

		return { browser: match[1] || "", version: match[2] || "0" };
	},

	sub: function() {
		function jQuerySubclass( selector, context ) {
			return new jQuerySubclass.fn.init( selector, context );
		}
		jQuery.extend( true, jQuerySubclass, this );
		jQuerySubclass.superclass = this;
		jQuerySubclass.fn = jQuerySubclass.prototype = this();
		jQuerySubclass.fn.constructor = jQuerySubclass;
		jQuerySubclass.subclass = this.subclass;
		jQuerySubclass.fn.init = function init( selector, context ) {
			if ( context && context instanceof jQuery && !(context instanceof jQuerySubclass) ) {
				context = jQuerySubclass(context);
			}

			return jQuery.fn.init.call( this, selector, context, rootjQuerySubclass );
		};
		jQuerySubclass.fn.init.prototype = jQuerySubclass.fn;
		var rootjQuerySubclass = jQuerySubclass(document);
		return jQuerySubclass;
	},

	browser: {}
});

// Create readyList deferred
readyList = jQuery._Deferred();

// Populate the class2type map
jQuery.each("Boolean Number String Function Array Date RegExp Object".split(" "), function(i, name) {
	class2type[ "[object " + name + "]" ] = name.toLowerCase();
});

browserMatch = jQuery.uaMatch( userAgent );
if ( browserMatch.browser ) {
	jQuery.browser[ browserMatch.browser ] = true;
	jQuery.browser.version = browserMatch.version;
}

// Deprecated, use jQuery.browser.webkit instead
if ( jQuery.browser.webkit ) {
	jQuery.browser.safari = true;
}

if ( indexOf ) {
	jQuery.inArray = function( elem, array ) {
		return indexOf.call( array, elem );
	};
}

// IE doesn't match non-breaking spaces with \s
if ( rnotwhite.test( "\xA0" ) ) {
	trimLeft = /^[\s\xA0]+/;
	trimRight = /[\s\xA0]+$/;
}

// All jQuery objects should point back to these
rootjQuery = jQuery(document);

// Cleanup functions for the document ready method
if ( document.addEventListener ) {
	DOMContentLoaded = function() {
		document.removeEventListener( "DOMContentLoaded", DOMContentLoaded, false );
		jQuery.ready();
	};

} else if ( document.attachEvent ) {
	DOMContentLoaded = function() {
		// Make sure body exists, at least, in case IE gets a little overzealous (ticket #5443).
		if ( document.readyState === "complete" ) {
			document.detachEvent( "onreadystatechange", DOMContentLoaded );
			jQuery.ready();
		}
	};
}

// The DOM ready check for Internet Explorer
function doScrollCheck() {
	if ( jQuery.isReady ) {
		return;
	}

	try {
		// If IE is used, use the trick by Diego Perini
		// http://javascript.nwbox.com/IEContentLoaded/
		document.documentElement.doScroll("left");
	} catch(e) {
		setTimeout( doScrollCheck, 1 );
		return;
	}

	// and execute any waiting functions
	jQuery.ready();
}

// Expose jQuery to the global object
return jQuery;

})();


(function() {

	jQuery.support = {};

	var div = document.createElement("div");

	div.style.display = "none";
	div.innerHTML = "   <link/><table></table><a href='/a' style='color:red;float:left;opacity:.55;'>a</a><input type='checkbox'/>";

	var all = div.getElementsByTagName("*"),
		a = div.getElementsByTagName("a")[0],
		select = document.createElement("select"),
		opt = select.appendChild( document.createElement("option") ),
		input = div.getElementsByTagName("input")[0];

	// Can't get basic test support
	if ( !all || !all.length || !a ) {
		return;
	}

	jQuery.support = {
		// IE strips leading whitespace when .innerHTML is used
		leadingWhitespace: div.firstChild.nodeType === 3,

		// Make sure that tbody elements aren't automatically inserted
		// IE will insert them into empty tables
		tbody: !div.getElementsByTagName("tbody").length,

		// Make sure that link elements get serialized correctly by innerHTML
		// This requires a wrapper element in IE
		htmlSerialize: !!div.getElementsByTagName("link").length,

		// Get the style information from getAttribute
		// (IE uses .cssText insted)
		style: /red/.test( a.getAttribute("style") ),

		// Make sure that URLs aren't manipulated
		// (IE normalizes it by default)
		hrefNormalized: a.getAttribute("href") === "/a",

		// Make sure that element opacity exists
		// (IE uses filter instead)
		// Use a regex to work around a WebKit issue. See #5145
		opacity: /^0.55$/.test( a.style.opacity ),

		// Verify style float existence
		// (IE uses styleFloat instead of cssFloat)
		cssFloat: !!a.style.cssFloat,

		// Make sure that if no value is specified for a checkbox
		// that it defaults to "on".
		// (WebKit defaults to "" instead)
		checkOn: input.value === "on",

		// Make sure that a selected-by-default option has a working selected property.
		// (WebKit defaults to false instead of true, IE too, if it's in an optgroup)
		optSelected: opt.selected,

		// Will be defined later
		deleteExpando: true,
		optDisabled: false,
		checkClone: false,
		noCloneEvent: true,
		noCloneChecked: true,
		boxModel: null,
		inlineBlockNeedsLayout: false,
		shrinkWrapBlocks: false,
		reliableHiddenOffsets: true
	};

	input.checked = true;
	jQuery.support.noCloneChecked = input.cloneNode( true ).checked;

	// Make sure that the options inside disabled selects aren't marked as disabled
	// (WebKit marks them as diabled)
	select.disabled = true;
	jQuery.support.optDisabled = !opt.disabled;

	var _scriptEval = null;
	jQuery.support.scriptEval = function() {
		if ( _scriptEval === null ) {
			var root = document.documentElement,
				script = document.createElement("script"),
				id = "script" + jQuery.now();

			try {
				script.appendChild( document.createTextNode( "window." + id + "=1;" ) );
			} catch(e) {}

			root.insertBefore( script, root.firstChild );

			// Make sure that the execution of code works by injecting a script
			// tag with appendChild/createTextNode
			// (IE doesn't support this, fails, and uses .text instead)
			if ( window[ id ] ) {
				_scriptEval = true;
				delete window[ id ];
			} else {
				_scriptEval = false;
			}

			root.removeChild( script );
			// release memory in IE
			root = script = id  = null;
		}

		return _scriptEval;
	};

	// Test to see if it's possible to delete an expando from an element
	// Fails in Internet Explorer
	try {
		delete div.test;

	} catch(e) {
		jQuery.support.deleteExpando = false;
	}

	if ( !div.addEventListener && div.attachEvent && div.fireEvent ) {
		div.attachEvent("onclick", function click() {
			// Cloning a node shouldn't copy over any
			// bound event handlers (IE does this)
			jQuery.support.noCloneEvent = false;
			div.detachEvent("onclick", click);
		});
		div.cloneNode(true).fireEvent("onclick");
	}

	div = document.createElement("div");
	div.innerHTML = "<input type='radio' name='radiotest' checked='checked'/>";

	var fragment = document.createDocumentFragment();
	fragment.appendChild( div.firstChild );

	// WebKit doesn't clone checked state correctly in fragments
	jQuery.support.checkClone = fragment.cloneNode(true).cloneNode(true).lastChild.checked;

	// Figure out if the W3C box model works as expected
	// document.body must exist before we can do this
	jQuery(function() {
		var div = document.createElement("div"),
			body = document.getElementsByTagName("body")[0];

		// Frameset documents with no body should not run this code
		if ( !body ) {
			return;
		}

		div.style.width = div.style.paddingLeft = "1px";
		body.appendChild( div );
		jQuery.boxModel = jQuery.support.boxModel = div.offsetWidth === 2;

		if ( "zoom" in div.style ) {
			// Check if natively block-level elements act like inline-block
			// elements when setting their display to 'inline' and giving
			// them layout
			// (IE < 8 does this)
			div.style.display = "inline";
			div.style.zoom = 1;
			jQuery.support.inlineBlockNeedsLayout = div.offsetWidth === 2;

			// Check if elements with layout shrink-wrap their children
			// (IE 6 does this)
			div.style.display = "";
			div.innerHTML = "<div style='width:4px;'></div>";
			jQuery.support.shrinkWrapBlocks = div.offsetWidth !== 2;
		}

		div.innerHTML = "<table><tr><td style='padding:0;border:0;display:none'></td><td>t</td></tr></table>";
		var tds = div.getElementsByTagName("td");

		// Check if table cells still have offsetWidth/Height when they are set
		// to display:none and there are still other visible table cells in a
		// table row; if so, offsetWidth/Height are not reliable for use when
		// determining if an element has been hidden directly using
		// display:none (it is still safe to use offsets if a parent element is
		// hidden; don safety goggles and see bug #4512 for more information).
		// (only IE 8 fails this test)
		jQuery.support.reliableHiddenOffsets = tds[0].offsetHeight === 0;

		tds[0].style.display = "";
		tds[1].style.display = "none";

		// Check if empty table cells still have offsetWidth/Height
		// (IE < 8 fail this test)
		jQuery.support.reliableHiddenOffsets = jQuery.support.reliableHiddenOffsets && tds[0].offsetHeight === 0;
		div.innerHTML = "";

		body.removeChild( div ).style.display = "none";
		div = tds = null;
	});

	// Technique from Juriy Zaytsev
	// http://thinkweb2.com/projects/prototype/detecting-event-support-without-browser-sniffing/
	var eventSupported = function( eventName ) {
		var el = document.createElement("div");
		eventName = "on" + eventName;

		// We only care about the case where non-standard event systems
		// are used, namely in IE. Short-circuiting here helps us to
		// avoid an eval call (in setAttribute) which can cause CSP
		// to go haywire. See: https://developer.mozilla.org/en/Security/CSP
		if ( !el.attachEvent ) {
			return true;
		}

		var isSupported = (eventName in el);
		if ( !isSupported ) {
			el.setAttribute(eventName, "return;");
			isSupported = typeof el[eventName] === "function";
		}
		el = null;

		return isSupported;
	};

	jQuery.support.submitBubbles = eventSupported("submit");
	jQuery.support.changeBubbles = eventSupported("change");

	// release memory in IE
	div = all = a = null;
})();



var rbrace = /^(?:\{.*\}|\[.*\])$/;

jQuery.extend({
	cache: {},

	// Please use with caution
	uuid: 0,

	// Unique for each copy of jQuery on the page
	// Non-digits removed to match rinlinejQuery
	expando: "jQuery" + ( jQuery.fn.jquery + Math.random() ).replace( /\D/g, "" ),

	// The following elements throw uncatchable exceptions if you
	// attempt to add expando properties to them.
	noData: {
		"embed": true,
		// Ban all objects except for Flash (which handle expandos)
		"object": "clsid:D27CDB6E-AE6D-11cf-96B8-444553540000",
		"applet": true
	},

	hasData: function( elem ) {
		elem = elem.nodeType ? jQuery.cache[ elem[jQuery.expando] ] : elem[ jQuery.expando ];

		return !!elem && !isEmptyDataObject( elem );
	},

	data: function( elem, name, data, pvt /* Internal Use Only */ ) {
		if ( !jQuery.acceptData( elem ) ) {
			return;
		}

		var internalKey = jQuery.expando, getByName = typeof name === "string", thisCache,

			// We have to handle DOM nodes and JS objects differently because IE6-7
			// can't GC object references properly across the DOM-JS boundary
			isNode = elem.nodeType,

			// Only DOM nodes need the global jQuery cache; JS object data is
			// attached directly to the object so GC can occur automatically
			cache = isNode ? jQuery.cache : elem,

			// Only defining an ID for JS objects if its cache already exists allows
			// the code to shortcut on the same path as a DOM node with no cache
			id = isNode ? elem[ jQuery.expando ] : elem[ jQuery.expando ] && jQuery.expando;

		// Avoid doing any more work than we need to when trying to get data on an
		// object that has no data at all
		if ( (!id || (pvt && id && !cache[ id ][ internalKey ])) && getByName && data === undefined ) {
			return;
		}

		if ( !id ) {
			// Only DOM nodes need a new unique ID for each element since their data
			// ends up in the global cache
			if ( isNode ) {
				elem[ jQuery.expando ] = id = ++jQuery.uuid;
			} else {
				id = jQuery.expando;
			}
		}

		if ( !cache[ id ] ) {
			cache[ id ] = {};

			// TODO: This is a hack for 1.5 ONLY. Avoids exposing jQuery
			// metadata on plain JS objects when the object is serialized using
			// JSON.stringify
			if ( !isNode ) {
				cache[ id ].toJSON = jQuery.noop;
			}
		}

		// An object can be passed to jQuery.data instead of a key/value pair; this gets
		// shallow copied over onto the existing cache
		if ( typeof name === "object" || typeof name === "function" ) {
			if ( pvt ) {
				cache[ id ][ internalKey ] = jQuery.extend(cache[ id ][ internalKey ], name);
			} else {
				cache[ id ] = jQuery.extend(cache[ id ], name);
			}
		}

		thisCache = cache[ id ];

		// Internal jQuery data is stored in a separate object inside the object's data
		// cache in order to avoid key collisions between internal data and user-defined
		// data
		if ( pvt ) {
			if ( !thisCache[ internalKey ] ) {
				thisCache[ internalKey ] = {};
			}

			thisCache = thisCache[ internalKey ];
		}

		if ( data !== undefined ) {
			thisCache[ name ] = data;
		}

		// TODO: This is a hack for 1.5 ONLY. It will be removed in 1.6. Users should
		// not attempt to inspect the internal events object using jQuery.data, as this
		// internal data object is undocumented and subject to change.
		if ( name === "events" && !thisCache[name] ) {
			return thisCache[ internalKey ] && thisCache[ internalKey ].events;
		}

		return getByName ? thisCache[ name ] : thisCache;
	},

	removeData: function( elem, name, pvt /* Internal Use Only */ ) {
		if ( !jQuery.acceptData( elem ) ) {
			return;
		}

		var internalKey = jQuery.expando, isNode = elem.nodeType,

			// See jQuery.data for more information
			cache = isNode ? jQuery.cache : elem,

			// See jQuery.data for more information
			id = isNode ? elem[ jQuery.expando ] : jQuery.expando;

		// If there is already no cache entry for this object, there is no
		// purpose in continuing
		if ( !cache[ id ] ) {
			return;
		}

		if ( name ) {
			var thisCache = pvt ? cache[ id ][ internalKey ] : cache[ id ];

			if ( thisCache ) {
				delete thisCache[ name ];

				// If there is no data left in the cache, we want to continue
				// and let the cache object itself get destroyed
				if ( !isEmptyDataObject(thisCache) ) {
					return;
				}
			}
		}

		// See jQuery.data for more information
		if ( pvt ) {
			delete cache[ id ][ internalKey ];

			// Don't destroy the parent cache unless the internal data object
			// had been the only thing left in it
			if ( !isEmptyDataObject(cache[ id ]) ) {
				return;
			}
		}

		var internalCache = cache[ id ][ internalKey ];

		// Browsers that fail expando deletion also refuse to delete expandos on
		// the window, but it will allow it on all other JS objects; other browsers
		// don't care
		if ( jQuery.support.deleteExpando || cache != window ) {
			delete cache[ id ];
		} else {
			cache[ id ] = null;
		}

		// We destroyed the entire user cache at once because it's faster than
		// iterating through each key, but we need to continue to persist internal
		// data if it existed
		if ( internalCache ) {
			cache[ id ] = {};
			// TODO: This is a hack for 1.5 ONLY. Avoids exposing jQuery
			// metadata on plain JS objects when the object is serialized using
			// JSON.stringify
			if ( !isNode ) {
				cache[ id ].toJSON = jQuery.noop;
			}

			cache[ id ][ internalKey ] = internalCache;

		// Otherwise, we need to eliminate the expando on the node to avoid
		// false lookups in the cache for entries that no longer exist
		} else if ( isNode ) {
			// IE does not allow us to delete expando properties from nodes,
			// nor does it have a removeAttribute function on Document nodes;
			// we must handle all of these cases
			if ( jQuery.support.deleteExpando ) {
				delete elem[ jQuery.expando ];
			} else if ( elem.removeAttribute ) {
				elem.removeAttribute( jQuery.expando );
			} else {
				elem[ jQuery.expando ] = null;
			}
		}
	},

	// For internal use only.
	_data: function( elem, name, data ) {
		return jQuery.data( elem, name, data, true );
	},

	// A method for determining if a DOM node can handle the data expando
	acceptData: function( elem ) {
		if ( elem.nodeName ) {
			var match = jQuery.noData[ elem.nodeName.toLowerCase() ];

			if ( match ) {
				return !(match === true || elem.getAttribute("classid") !== match);
			}
		}

		return true;
	}
});

jQuery.fn.extend({
	data: function( key, value ) {
		var data = null;

		if ( typeof key === "undefined" ) {
			if ( this.length ) {
				data = jQuery.data( this[0] );

				if ( this[0].nodeType === 1 ) {
					var attr = this[0].attributes, name;
					for ( var i = 0, l = attr.length; i < l; i++ ) {
						name = attr[i].name;

						if ( name.indexOf( "data-" ) === 0 ) {
							name = name.substr( 5 );
							dataAttr( this[0], name, data[ name ] );
						}
					}
				}
			}

			return data;

		} else if ( typeof key === "object" ) {
			return this.each(function() {
				jQuery.data( this, key );
			});
		}

		var parts = key.split(".");
		parts[1] = parts[1] ? "." + parts[1] : "";

		if ( value === undefined ) {
			data = this.triggerHandler("getData" + parts[1] + "!", [parts[0]]);

			// Try to fetch any internally stored data first
			if ( data === undefined && this.length ) {
				data = jQuery.data( this[0], key );
				data = dataAttr( this[0], key, data );
			}

			return data === undefined && parts[1] ?
				this.data( parts[0] ) :
				data;

		} else {
			return this.each(function() {
				var $this = jQuery( this ),
					args = [ parts[0], value ];

				$this.triggerHandler( "setData" + parts[1] + "!", args );
				jQuery.data( this, key, value );
				$this.triggerHandler( "changeData" + parts[1] + "!", args );
			});
		}
	},

	removeData: function( key ) {
		return this.each(function() {
			jQuery.removeData( this, key );
		});
	}
});

function dataAttr( elem, key, data ) {
	// If nothing was found internally, try to fetch any
	// data from the HTML5 data-* attribute
	if ( data === undefined && elem.nodeType === 1 ) {
		data = elem.getAttribute( "data-" + key );

		if ( typeof data === "string" ) {
			try {
				data = data === "true" ? true :
				data === "false" ? false :
				data === "null" ? null :
				!jQuery.isNaN( data ) ? parseFloat( data ) :
					rbrace.test( data ) ? jQuery.parseJSON( data ) :
					data;
			} catch( e ) {}

			// Make sure we set the data so it isn't changed later
			jQuery.data( elem, key, data );

		} else {
			data = undefined;
		}
	}

	return data;
}

// TODO: This is a hack for 1.5 ONLY to allow objects with a single toJSON
// property to be considered empty objects; this property always exists in
// order to make sure JSON.stringify does not expose internal metadata
function isEmptyDataObject( obj ) {
	for ( var name in obj ) {
		if ( name !== "toJSON" ) {
			return false;
		}
	}

	return true;
}




jQuery.extend({
	queue: function( elem, type, data ) {
		if ( !elem ) {
			return;
		}

		type = (type || "fx") + "queue";
		var q = jQuery._data( elem, type );

		// Speed up dequeue by getting out quickly if this is just a lookup
		if ( !data ) {
			return q || [];
		}

		if ( !q || jQuery.isArray(data) ) {
			q = jQuery._data( elem, type, jQuery.makeArray(data) );

		} else {
			q.push( data );
		}

		return q;
	},

	dequeue: function( elem, type ) {
		type = type || "fx";

		var queue = jQuery.queue( elem, type ),
			fn = queue.shift();

		// If the fx queue is dequeued, always remove the progress sentinel
		if ( fn === "inprogress" ) {
			fn = queue.shift();
		}

		if ( fn ) {
			// Add a progress sentinel to prevent the fx queue from being
			// automatically dequeued
			if ( type === "fx" ) {
				queue.unshift("inprogress");
			}

			fn.call(elem, function() {
				jQuery.dequeue(elem, type);
			});
		}

		if ( !queue.length ) {
			jQuery.removeData( elem, type + "queue", true );
		}
	}
});

jQuery.fn.extend({
	queue: function( type, data ) {
		if ( typeof type !== "string" ) {
			data = type;
			type = "fx";
		}

		if ( data === undefined ) {
			return jQuery.queue( this[0], type );
		}
		return this.each(function( i ) {
			var queue = jQuery.queue( this, type, data );

			if ( type === "fx" && queue[0] !== "inprogress" ) {
				jQuery.dequeue( this, type );
			}
		});
	},
	dequeue: function( type ) {
		return this.each(function() {
			jQuery.dequeue( this, type );
		});
	},

	// Based off of the plugin by Clint Helfers, with permission.
	// http://blindsignals.com/index.php/2009/07/jquery-delay/
	delay: function( time, type ) {
		time = jQuery.fx ? jQuery.fx.speeds[time] || time : time;
		type = type || "fx";

		return this.queue( type, function() {
			var elem = this;
			setTimeout(function() {
				jQuery.dequeue( elem, type );
			}, time );
		});
	},

	clearQueue: function( type ) {
		return this.queue( type || "fx", [] );
	}
});




var rclass = /[\n\t\r]/g,
	rspaces = /\s+/,
	rreturn = /\r/g,
	rspecialurl = /^(?:href|src|style)$/,
	rtype = /^(?:button|input)$/i,
	rfocusable = /^(?:button|input|object|select|textarea)$/i,
	rclickable = /^a(?:rea)?$/i,
	rradiocheck = /^(?:radio|checkbox)$/i;

jQuery.props = {
	"for": "htmlFor",
	"class": "className",
	readonly: "readOnly",
	maxlength: "maxLength",
	cellspacing: "cellSpacing",
	rowspan: "rowSpan",
	colspan: "colSpan",
	tabindex: "tabIndex",
	usemap: "useMap",
	frameborder: "frameBorder"
};

jQuery.fn.extend({
	attr: function( name, value ) {
		return jQuery.access( this, name, value, true, jQuery.attr );
	},

	removeAttr: function( name, fn ) {
		return this.each(function(){
			jQuery.attr( this, name, "" );
			if ( this.nodeType === 1 ) {
				this.removeAttribute( name );
			}
		});
	},

	addClass: function( value ) {
		if ( jQuery.isFunction(value) ) {
			return this.each(function(i) {
				var self = jQuery(this);
				self.addClass( value.call(this, i, self.attr("class")) );
			});
		}

		if ( value && typeof value === "string" ) {
			var classNames = (value || "").split( rspaces );

			for ( var i = 0, l = this.length; i < l; i++ ) {
				var elem = this[i];

				if ( elem.nodeType === 1 ) {
					if ( !elem.className ) {
						elem.className = value;

					} else {
						var className = " " + elem.className + " ",
							setClass = elem.className;

						for ( var c = 0, cl = classNames.length; c < cl; c++ ) {
							if ( className.indexOf( " " + classNames[c] + " " ) < 0 ) {
								setClass += " " + classNames[c];
							}
						}
						elem.className = jQuery.trim( setClass );
					}
				}
			}
		}

		return this;
	},

	removeClass: function( value ) {
		if ( jQuery.isFunction(value) ) {
			return this.each(function(i) {
				var self = jQuery(this);
				self.removeClass( value.call(this, i, self.attr("class")) );
			});
		}

		if ( (value && typeof value === "string") || value === undefined ) {
			var classNames = (value || "").split( rspaces );

			for ( var i = 0, l = this.length; i < l; i++ ) {
				var elem = this[i];

				if ( elem.nodeType === 1 && elem.className ) {
					if ( value ) {
						var className = (" " + elem.className + " ").replace(rclass, " ");
						for ( var c = 0, cl = classNames.length; c < cl; c++ ) {
							className = className.replace(" " + classNames[c] + " ", " ");
						}
						elem.className = jQuery.trim( className );

					} else {
						elem.className = "";
					}
				}
			}
		}

		return this;
	},

	toggleClass: function( value, stateVal ) {
		var type = typeof value,
			isBool = typeof stateVal === "boolean";

		if ( jQuery.isFunction( value ) ) {
			return this.each(function(i) {
				var self = jQuery(this);
				self.toggleClass( value.call(this, i, self.attr("class"), stateVal), stateVal );
			});
		}

		return this.each(function() {
			if ( type === "string" ) {
				// toggle individual class names
				var className,
					i = 0,
					self = jQuery( this ),
					state = stateVal,
					classNames = value.split( rspaces );

				while ( (className = classNames[ i++ ]) ) {
					// check each className given, space seperated list
					state = isBool ? state : !self.hasClass( className );
					self[ state ? "addClass" : "removeClass" ]( className );
				}

			} else if ( type === "undefined" || type === "boolean" ) {
				if ( this.className ) {
					// store className if set
					jQuery._data( this, "__className__", this.className );
				}

				// toggle whole className
				this.className = this.className || value === false ? "" : jQuery._data( this, "__className__" ) || "";
			}
		});
	},

	hasClass: function( selector ) {
		var className = " " + selector + " ";
		for ( var i = 0, l = this.length; i < l; i++ ) {
			if ( (" " + this[i].className + " ").replace(rclass, " ").indexOf( className ) > -1 ) {
				return true;
			}
		}

		return false;
	},

	val: function( value ) {
		if ( !arguments.length ) {
			var elem = this[0];

			if ( elem ) {
				if ( jQuery.nodeName( elem, "option" ) ) {
					// attributes.value is undefined in Blackberry 4.7 but
					// uses .value. See #6932
					var val = elem.attributes.value;
					return !val || val.specified ? elem.value : elem.text;
				}

				// We need to handle select boxes special
				if ( jQuery.nodeName( elem, "select" ) ) {
					var index = elem.selectedIndex,
						values = [],
						options = elem.options,
						one = elem.type === "select-one";

					// Nothing was selected
					if ( index < 0 ) {
						return null;
					}

					// Loop through all the selected options
					for ( var i = one ? index : 0, max = one ? index + 1 : options.length; i < max; i++ ) {
						var option = options[ i ];

						// Don't return options that are disabled or in a disabled optgroup
						if ( option.selected && (jQuery.support.optDisabled ? !option.disabled : option.getAttribute("disabled") === null) &&
								(!option.parentNode.disabled || !jQuery.nodeName( option.parentNode, "optgroup" )) ) {

							// Get the specific value for the option
							value = jQuery(option).val();

							// We don't need an array for one selects
							if ( one ) {
								return value;
							}

							// Multi-Selects return an array
							values.push( value );
						}
					}

					// Fixes Bug #2551 -- select.val() broken in IE after form.reset()
					if ( one && !values.length && options.length ) {
						return jQuery( options[ index ] ).val();
					}

					return values;
				}

				// Handle the case where in Webkit "" is returned instead of "on" if a value isn't specified
				if ( rradiocheck.test( elem.type ) && !jQuery.support.checkOn ) {
					return elem.getAttribute("value") === null ? "on" : elem.value;
				}

				// Everything else, we just grab the value
				return (elem.value || "").replace(rreturn, "");

			}

			return undefined;
		}

		var isFunction = jQuery.isFunction(value);

		return this.each(function(i) {
			var self = jQuery(this), val = value;

			if ( this.nodeType !== 1 ) {
				return;
			}

			if ( isFunction ) {
				val = value.call(this, i, self.val());
			}

			// Treat null/undefined as ""; convert numbers to string
			if ( val == null ) {
				val = "";
			} else if ( typeof val === "number" ) {
				val += "";
			} else if ( jQuery.isArray(val) ) {
				val = jQuery.map(val, function (value) {
					return value == null ? "" : value + "";
				});
			}

			if ( jQuery.isArray(val) && rradiocheck.test( this.type ) ) {
				this.checked = jQuery.inArray( self.val(), val ) >= 0;

			} else if ( jQuery.nodeName( this, "select" ) ) {
				var values = jQuery.makeArray(val);

				jQuery( "option", this ).each(function() {
					this.selected = jQuery.inArray( jQuery(this).val(), values ) >= 0;
				});

				if ( !values.length ) {
					this.selectedIndex = -1;
				}

			} else {
				this.value = val;
			}
		});
	}
});

jQuery.extend({
	attrFn: {
		val: true,
		css: true,
		html: true,
		text: true,
		data: true,
		width: true,
		height: true,
		offset: true
	},

	attr: function( elem, name, value, pass ) {
		// don't get/set attributes on text, comment and attribute nodes
		if ( !elem || elem.nodeType === 3 || elem.nodeType === 8 || elem.nodeType === 2 ) {
			return undefined;
		}

		if ( pass && name in jQuery.attrFn ) {
			return jQuery(elem)[name](value);
		}

		var notxml = elem.nodeType !== 1 || !jQuery.isXMLDoc( elem ),
			// Whether we are setting (or getting)
			set = value !== undefined;

		// Try to normalize/fix the name
		name = notxml && jQuery.props[ name ] || name;

		// Only do all the following if this is a node (faster for style)
		if ( elem.nodeType === 1 ) {
			// These attributes require special treatment
			var special = rspecialurl.test( name );

			// Safari mis-reports the default selected property of an option
			// Accessing the parent's selectedIndex property fixes it
			if ( name === "selected" && !jQuery.support.optSelected ) {
				var parent = elem.parentNode;
				if ( parent ) {
					parent.selectedIndex;

					// Make sure that it also works with optgroups, see #5701
					if ( parent.parentNode ) {
						parent.parentNode.selectedIndex;
					}
				}
			}

			// If applicable, access the attribute via the DOM 0 way
			// 'in' checks fail in Blackberry 4.7 #6931
			if ( (name in elem || elem[ name ] !== undefined) && notxml && !special ) {
				if ( set ) {
					// We can't allow the type property to be changed (since it causes problems in IE)
					if ( name === "type" && rtype.test( elem.nodeName ) && elem.parentNode ) {
						jQuery.error( "type property can't be changed" );
					}

					if ( value === null ) {
						if ( elem.nodeType === 1 ) {
							elem.removeAttribute( name );
						}

					} else {
						elem[ name ] = value;
					}
				}

				// browsers index elements by id/name on forms, give priority to attributes.
				if ( jQuery.nodeName( elem, "form" ) && elem.getAttributeNode(name) ) {
					return elem.getAttributeNode( name ).nodeValue;
				}

				// elem.tabIndex doesn't always return the correct value when it hasn't been explicitly set
				// http://fluidproject.org/blog/2008/01/09/getting-setting-and-removing-tabindex-values-with-javascript/
				if ( name === "tabIndex" ) {
					var attributeNode = elem.getAttributeNode( "tabIndex" );

					return attributeNode && attributeNode.specified ?
						attributeNode.value :
						rfocusable.test( elem.nodeName ) || rclickable.test( elem.nodeName ) && elem.href ?
							0 :
							undefined;
				}

				return elem[ name ];
			}

			if ( !jQuery.support.style && notxml && name === "style" ) {
				if ( set ) {
					elem.style.cssText = "" + value;
				}

				return elem.style.cssText;
			}

			if ( set ) {
				// convert the value to a string (all browsers do this but IE) see #1070
				elem.setAttribute( name, "" + value );
			}

			// Ensure that missing attributes return undefined
			// Blackberry 4.7 returns "" from getAttribute #6938
			if ( !elem.attributes[ name ] && (elem.hasAttribute && !elem.hasAttribute( name )) ) {
				return undefined;
			}

			var attr = !jQuery.support.hrefNormalized && notxml && special ?
					// Some attributes require a special call on IE
					elem.getAttribute( name, 2 ) :
					elem.getAttribute( name );

			// Non-existent attributes return null, we normalize to undefined
			return attr === null ? undefined : attr;
		}
		// Handle everything which isn't a DOM element node
		if ( set ) {
			elem[ name ] = value;
		}
		return elem[ name ];
	}
});




var rnamespaces = /\.(.*)$/,
	rformElems = /^(?:textarea|input|select)$/i,
	rperiod = /\./g,
	rspace = / /g,
	rescape = /[^\w\s.|`]/g,
	fcleanup = function( nm ) {
		return nm.replace(rescape, "\\$&");
	};

/*
 * A number of helper functions used for managing events.
 * Many of the ideas behind this code originated from
 * Dean Edwards' addEvent library.
 */
jQuery.event = {

	// Bind an event to an element
	// Original by Dean Edwards
	add: function( elem, types, handler, data ) {
		if ( elem.nodeType === 3 || elem.nodeType === 8 ) {
			return;
		}

		// TODO :: Use a try/catch until it's safe to pull this out (likely 1.6)
		// Minor release fix for bug #8018
		try {
			// For whatever reason, IE has trouble passing the window object
			// around, causing it to be cloned in the process
			if ( jQuery.isWindow( elem ) && ( elem !== window && !elem.frameElement ) ) {
				elem = window;
			}
		}
		catch ( e ) {}

		if ( handler === false ) {
			handler = returnFalse;
		} else if ( !handler ) {
			// Fixes bug #7229. Fix recommended by jdalton
			return;
		}

		var handleObjIn, handleObj;

		if ( handler.handler ) {
			handleObjIn = handler;
			handler = handleObjIn.handler;
		}

		// Make sure that the function being executed has a unique ID
		if ( !handler.guid ) {
			handler.guid = jQuery.guid++;
		}

		// Init the element's event structure
		var elemData = jQuery._data( elem );

		// If no elemData is found then we must be trying to bind to one of the
		// banned noData elements
		if ( !elemData ) {
			return;
		}

		var events = elemData.events,
			eventHandle = elemData.handle;

		if ( !events ) {
			elemData.events = events = {};
		}

		if ( !eventHandle ) {
			elemData.handle = eventHandle = function() {
				// Handle the second event of a trigger and when
				// an event is called after a page has unloaded
				return typeof jQuery !== "undefined" && !jQuery.event.triggered ?
					jQuery.event.handle.apply( eventHandle.elem, arguments ) :
					undefined;
			};
		}

		// Add elem as a property of the handle function
		// This is to prevent a memory leak with non-native events in IE.
		eventHandle.elem = elem;

		// Handle multiple events separated by a space
		// jQuery(...).bind("mouseover mouseout", fn);
		types = types.split(" ");

		var type, i = 0, namespaces;

		while ( (type = types[ i++ ]) ) {
			handleObj = handleObjIn ?
				jQuery.extend({}, handleObjIn) :
				{ handler: handler, data: data };

			// Namespaced event handlers
			if ( type.indexOf(".") > -1 ) {
				namespaces = type.split(".");
				type = namespaces.shift();
				handleObj.namespace = namespaces.slice(0).sort().join(".");

			} else {
				namespaces = [];
				handleObj.namespace = "";
			}

			handleObj.type = type;
			if ( !handleObj.guid ) {
				handleObj.guid = handler.guid;
			}

			// Get the current list of functions bound to this event
			var handlers = events[ type ],
				special = jQuery.event.special[ type ] || {};

			// Init the event handler queue
			if ( !handlers ) {
				handlers = events[ type ] = [];

				// Check for a special event handler
				// Only use addEventListener/attachEvent if the special
				// events handler returns false
				if ( !special.setup || special.setup.call( elem, data, namespaces, eventHandle ) === false ) {
					// Bind the global event handler to the element
					if ( elem.addEventListener ) {
						elem.addEventListener( type, eventHandle, false );

					} else if ( elem.attachEvent ) {
						elem.attachEvent( "on" + type, eventHandle );
					}
				}
			}

			if ( special.add ) {
				special.add.call( elem, handleObj );

				if ( !handleObj.handler.guid ) {
					handleObj.handler.guid = handler.guid;
				}
			}

			// Add the function to the element's handler list
			handlers.push( handleObj );

			// Keep track of which events have been used, for global triggering
			jQuery.event.global[ type ] = true;
		}

		// Nullify elem to prevent memory leaks in IE
		elem = null;
	},

	global: {},

	// Detach an event or set of events from an element
	remove: function( elem, types, handler, pos ) {
		// don't do events on text and comment nodes
		if ( elem.nodeType === 3 || elem.nodeType === 8 ) {
			return;
		}

		if ( handler === false ) {
			handler = returnFalse;
		}

		var ret, type, fn, j, i = 0, all, namespaces, namespace, special, eventType, handleObj, origType,
			elemData = jQuery.hasData( elem ) && jQuery._data( elem ),
			events = elemData && elemData.events;

		if ( !elemData || !events ) {
			return;
		}

		// types is actually an event object here
		if ( types && types.type ) {
			handler = types.handler;
			types = types.type;
		}

		// Unbind all events for the element
		if ( !types || typeof types === "string" && types.charAt(0) === "." ) {
			types = types || "";

			for ( type in events ) {
				jQuery.event.remove( elem, type + types );
			}

			return;
		}

		// Handle multiple events separated by a space
		// jQuery(...).unbind("mouseover mouseout", fn);
		types = types.split(" ");

		while ( (type = types[ i++ ]) ) {
			origType = type;
			handleObj = null;
			all = type.indexOf(".") < 0;
			namespaces = [];

			if ( !all ) {
				// Namespaced event handlers
				namespaces = type.split(".");
				type = namespaces.shift();

				namespace = new RegExp("(^|\\.)" +
					jQuery.map( namespaces.slice(0).sort(), fcleanup ).join("\\.(?:.*\\.)?") + "(\\.|$)");
			}

			eventType = events[ type ];

			if ( !eventType ) {
				continue;
			}

			if ( !handler ) {
				for ( j = 0; j < eventType.length; j++ ) {
					handleObj = eventType[ j ];

					if ( all || namespace.test( handleObj.namespace ) ) {
						jQuery.event.remove( elem, origType, handleObj.handler, j );
						eventType.splice( j--, 1 );
					}
				}

				continue;
			}

			special = jQuery.event.special[ type ] || {};

			for ( j = pos || 0; j < eventType.length; j++ ) {
				handleObj = eventType[ j ];

				if ( handler.guid === handleObj.guid ) {
					// remove the given handler for the given type
					if ( all || namespace.test( handleObj.namespace ) ) {
						if ( pos == null ) {
							eventType.splice( j--, 1 );
						}

						if ( special.remove ) {
							special.remove.call( elem, handleObj );
						}
					}

					if ( pos != null ) {
						break;
					}
				}
			}

			// remove generic event handler if no more handlers exist
			if ( eventType.length === 0 || pos != null && eventType.length === 1 ) {
				if ( !special.teardown || special.teardown.call( elem, namespaces ) === false ) {
					jQuery.removeEvent( elem, type, elemData.handle );
				}

				ret = null;
				delete events[ type ];
			}
		}

		// Remove the expando if it's no longer used
		if ( jQuery.isEmptyObject( events ) ) {
			var handle = elemData.handle;
			if ( handle ) {
				handle.elem = null;
			}

			delete elemData.events;
			delete elemData.handle;

			if ( jQuery.isEmptyObject( elemData ) ) {
				jQuery.removeData( elem, undefined, true );
			}
		}
	},

	// bubbling is internal
	trigger: function( event, data, elem /*, bubbling */ ) {
		// Event object or event type
		var type = event.type || event,
			bubbling = arguments[3];

		if ( !bubbling ) {
			event = typeof event === "object" ?
				// jQuery.Event object
				event[ jQuery.expando ] ? event :
				// Object literal
				jQuery.extend( jQuery.Event(type), event ) :
				// Just the event type (string)
				jQuery.Event(type);

			if ( type.indexOf("!") >= 0 ) {
				event.type = type = type.slice(0, -1);
				event.exclusive = true;
			}

			// Handle a global trigger
			if ( !elem ) {
				// Don't bubble custom events when global (to avoid too much overhead)
				event.stopPropagation();

				// Only trigger if we've ever bound an event for it
				if ( jQuery.event.global[ type ] ) {
					// XXX This code smells terrible. event.js should not be directly
					// inspecting the data cache
					jQuery.each( jQuery.cache, function() {
						// internalKey variable is just used to make it easier to find
						// and potentially change this stuff later; currently it just
						// points to jQuery.expando
						var internalKey = jQuery.expando,
							internalCache = this[ internalKey ];
						if ( internalCache && internalCache.events && internalCache.events[ type ] ) {
							jQuery.event.trigger( event, data, internalCache.handle.elem );
						}
					});
				}
			}

			// Handle triggering a single element

			// don't do events on text and comment nodes
			if ( !elem || elem.nodeType === 3 || elem.nodeType === 8 ) {
				return undefined;
			}

			// Clean up in case it is reused
			event.result = undefined;
			event.target = elem;

			// Clone the incoming data, if any
			data = jQuery.makeArray( data );
			data.unshift( event );
		}

		event.currentTarget = elem;

		// Trigger the event, it is assumed that "handle" is a function
		var handle = jQuery._data( elem, "handle" );

		if ( handle ) {
			handle.apply( elem, data );
		}

		var parent = elem.parentNode || elem.ownerDocument;

		// Trigger an inline bound script
		try {
			if ( !(elem && elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()]) ) {
				if ( elem[ "on" + type ] && elem[ "on" + type ].apply( elem, data ) === false ) {
					event.result = false;
					event.preventDefault();
				}
			}

		// prevent IE from throwing an error for some elements with some event types, see #3533
		} catch (inlineError) {}

		if ( !event.isPropagationStopped() && parent ) {
			jQuery.event.trigger( event, data, parent, true );

		} else if ( !event.isDefaultPrevented() ) {
			var old,
				target = event.target,
				targetType = type.replace( rnamespaces, "" ),
				isClick = jQuery.nodeName( target, "a" ) && targetType === "click",
				special = jQuery.event.special[ targetType ] || {};

			if ( (!special._default || special._default.call( elem, event ) === false) &&
				!isClick && !(target && target.nodeName && jQuery.noData[target.nodeName.toLowerCase()]) ) {

				try {
					if ( target[ targetType ] ) {
						// Make sure that we don't accidentally re-trigger the onFOO events
						old = target[ "on" + targetType ];

						if ( old ) {
							target[ "on" + targetType ] = null;
						}

						jQuery.event.triggered = true;
						target[ targetType ]();
					}

				// prevent IE from throwing an error for some elements with some event types, see #3533
				} catch (triggerError) {}

				if ( old ) {
					target[ "on" + targetType ] = old;
				}

				jQuery.event.triggered = false;
			}
		}
	},

	handle: function( event ) {
		var all, handlers, namespaces, namespace_re, events,
			namespace_sort = [],
			args = jQuery.makeArray( arguments );

		event = args[0] = jQuery.event.fix( event || window.event );
		event.currentTarget = this;

		// Namespaced event handlers
		all = event.type.indexOf(".") < 0 && !event.exclusive;

		if ( !all ) {
			namespaces = event.type.split(".");
			event.type = namespaces.shift();
			namespace_sort = namespaces.slice(0).sort();
			namespace_re = new RegExp("(^|\\.)" + namespace_sort.join("\\.(?:.*\\.)?") + "(\\.|$)");
		}

		event.namespace = event.namespace || namespace_sort.join(".");

		events = jQuery._data(this, "events");

		handlers = (events || {})[ event.type ];

		if ( events && handlers ) {
			// Clone the handlers to prevent manipulation
			handlers = handlers.slice(0);

			for ( var j = 0, l = handlers.length; j < l; j++ ) {
				var handleObj = handlers[ j ];

				// Filter the functions by class
				if ( all || namespace_re.test( handleObj.namespace ) ) {
					// Pass in a reference to the handler function itself
					// So that we can later remove it
					event.handler = handleObj.handler;
					event.data = handleObj.data;
					event.handleObj = handleObj;

					var ret = handleObj.handler.apply( this, args );

					if ( ret !== undefined ) {
						event.result = ret;
						if ( ret === false ) {
							event.preventDefault();
							event.stopPropagation();
						}
					}

					if ( event.isImmediatePropagationStopped() ) {
						break;
					}
				}
			}
		}

		return event.result;
	},

	props: "altKey attrChange attrName bubbles button cancelable charCode clientX clientY ctrlKey currentTarget data detail eventPhase fromElement handler keyCode layerX layerY metaKey newValue offsetX offsetY pageX pageY prevValue relatedNode relatedTarget screenX screenY shiftKey srcElement target toElement view wheelDelta which".split(" "),

	fix: function( event ) {
		if ( event[ jQuery.expando ] ) {
			return event;
		}

		// store a copy of the original event object
		// and "clone" to set read-only properties
		var originalEvent = event;
		event = jQuery.Event( originalEvent );

		for ( var i = this.props.length, prop; i; ) {
			prop = this.props[ --i ];
			event[ prop ] = originalEvent[ prop ];
		}

		// Fix target property, if necessary
		if ( !event.target ) {
			// Fixes #1925 where srcElement might not be defined either
			event.target = event.srcElement || document;
		}

		// check if target is a textnode (safari)
		if ( event.target.nodeType === 3 ) {
			event.target = event.target.parentNode;
		}

		// Add relatedTarget, if necessary
		if ( !event.relatedTarget && event.fromElement ) {
			event.relatedTarget = event.fromElement === event.target ? event.toElement : event.fromElement;
		}

		// Calculate pageX/Y if missing and clientX/Y available
		if ( event.pageX == null && event.clientX != null ) {
			var doc = document.documentElement,
				body = document.body;

			event.pageX = event.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0);
			event.pageY = event.clientY + (doc && doc.scrollTop  || body && body.scrollTop  || 0) - (doc && doc.clientTop  || body && body.clientTop  || 0);
		}

		// Add which for key events
		if ( event.which == null && (event.charCode != null || event.keyCode != null) ) {
			event.which = event.charCode != null ? event.charCode : event.keyCode;
		}

		// Add metaKey to non-Mac browsers (use ctrl for PC's and Meta for Macs)
		if ( !event.metaKey && event.ctrlKey ) {
			event.metaKey = event.ctrlKey;
		}

		// Add which for click: 1 === left; 2 === middle; 3 === right
		// Note: button is not normalized, so don't use it
		if ( !event.which && event.button !== undefined ) {
			event.which = (event.button & 1 ? 1 : ( event.button & 2 ? 3 : ( event.button & 4 ? 2 : 0 ) ));
		}

		return event;
	},

	// Deprecated, use jQuery.guid instead
	guid: 1E8,

	// Deprecated, use jQuery.proxy instead
	proxy: jQuery.proxy,

	special: {
		ready: {
			// Make sure the ready event is setup
			setup: jQuery.bindReady,
			teardown: jQuery.noop
		},

		live: {
			add: function( handleObj ) {
				jQuery.event.add( this,
					liveConvert( handleObj.origType, handleObj.selector ),
					jQuery.extend({}, handleObj, {handler: liveHandler, guid: handleObj.handler.guid}) );
			},

			remove: function( handleObj ) {
				jQuery.event.remove( this, liveConvert( handleObj.origType, handleObj.selector ), handleObj );
			}
		},

		beforeunload: {
			setup: function( data, namespaces, eventHandle ) {
				// We only want to do this special case on windows
				if ( jQuery.isWindow( this ) ) {
					this.onbeforeunload = eventHandle;
				}
			},

			teardown: function( namespaces, eventHandle ) {
				if ( this.onbeforeunload === eventHandle ) {
					this.onbeforeunload = null;
				}
			}
		}
	}
};

jQuery.removeEvent = document.removeEventListener ?
	function( elem, type, handle ) {
		if ( elem.removeEventListener ) {
			elem.removeEventListener( type, handle, false );
		}
	} :
	function( elem, type, handle ) {
		if ( elem.detachEvent ) {
			elem.detachEvent( "on" + type, handle );
		}
	};

jQuery.Event = function( src ) {
	// Allow instantiation without the 'new' keyword
	if ( !this.preventDefault ) {
		return new jQuery.Event( src );
	}

	// Event object
	if ( src && src.type ) {
		this.originalEvent = src;
		this.type = src.type;

		// Events bubbling up the document may have been marked as prevented
		// by a handler lower down the tree; reflect the correct value.
		this.isDefaultPrevented = (src.defaultPrevented || src.returnValue === false ||
			src.getPreventDefault && src.getPreventDefault()) ? returnTrue : returnFalse;

	// Event type
	} else {
		this.type = src;
	}

	// timeStamp is buggy for some events on Firefox(#3843)
	// So we won't rely on the native value
	this.timeStamp = jQuery.now();

	// Mark it as fixed
	this[ jQuery.expando ] = true;
};

function returnFalse() {
	return false;
}
function returnTrue() {
	return true;
}

// jQuery.Event is based on DOM3 Events as specified by the ECMAScript Language Binding
// http://www.w3.org/TR/2003/WD-DOM-Level-3-Events-20030331/ecma-script-binding.html
jQuery.Event.prototype = {
	preventDefault: function() {
		this.isDefaultPrevented = returnTrue;

		var e = this.originalEvent;
		if ( !e ) {
			return;
		}

		// if preventDefault exists run it on the original event
		if ( e.preventDefault ) {
			e.preventDefault();

		// otherwise set the returnValue property of the original event to false (IE)
		} else {
			e.returnValue = false;
		}
	},
	stopPropagation: function() {
		this.isPropagationStopped = returnTrue;

		var e = this.originalEvent;
		if ( !e ) {
			return;
		}
		// if stopPropagation exists run it on the original event
		if ( e.stopPropagation ) {
			e.stopPropagation();
		}
		// otherwise set the cancelBubble property of the original event to true (IE)
		e.cancelBubble = true;
	},
	stopImmediatePropagation: function() {
		this.isImmediatePropagationStopped = returnTrue;
		this.stopPropagation();
	},
	isDefaultPrevented: returnFalse,
	isPropagationStopped: returnFalse,
	isImmediatePropagationStopped: returnFalse
};

// Checks if an event happened on an element within another element
// Used in jQuery.event.special.mouseenter and mouseleave handlers
var withinElement = function( event ) {
	// Check if mouse(over|out) are still within the same parent element
	var parent = event.relatedTarget;

	// Firefox sometimes assigns relatedTarget a XUL element
	// which we cannot access the parentNode property of
	try {

		// Chrome does something similar, the parentNode property
		// can be accessed but is null.
		if ( parent !== document && !parent.parentNode ) {
			return;
		}
		// Traverse up the tree
		while ( parent && parent !== this ) {
			parent = parent.parentNode;
		}

		if ( parent !== this ) {
			// set the correct event type
			event.type = event.data;

			// handle event if we actually just moused on to a non sub-element
			jQuery.event.handle.apply( this, arguments );
		}

	// assuming we've left the element since we most likely mousedover a xul element
	} catch(e) { }
},

// In case of event delegation, we only need to rename the event.type,
// liveHandler will take care of the rest.
delegate = function( event ) {
	event.type = event.data;
	jQuery.event.handle.apply( this, arguments );
};

// Create mouseenter and mouseleave events
jQuery.each({
	mouseenter: "mouseover",
	mouseleave: "mouseout"
}, function( orig, fix ) {
	jQuery.event.special[ orig ] = {
		setup: function( data ) {
			jQuery.event.add( this, fix, data && data.selector ? delegate : withinElement, orig );
		},
		teardown: function( data ) {
			jQuery.event.remove( this, fix, data && data.selector ? delegate : withinElement );
		}
	};
});

// submit delegation
if ( !jQuery.support.submitBubbles ) {

	jQuery.event.special.submit = {
		setup: function( data, namespaces ) {
			if ( this.nodeName && this.nodeName.toLowerCase() !== "form" ) {
				jQuery.event.add(this, "click.specialSubmit", function( e ) {
					var elem = e.target,
						type = elem.type;

					if ( (type === "submit" || type === "image") && jQuery( elem ).closest("form").length ) {
						trigger( "submit", this, arguments );
					}
				});

				jQuery.event.add(this, "keypress.specialSubmit", function( e ) {
					var elem = e.target,
						type = elem.type;

					if ( (type === "text" || type === "password") && jQuery( elem ).closest("form").length && e.keyCode === 13 ) {
						trigger( "submit", this, arguments );
					}
				});

			} else {
				return false;
			}
		},

		teardown: function( namespaces ) {
			jQuery.event.remove( this, ".specialSubmit" );
		}
	};

}

// change delegation, happens here so we have bind.
if ( !jQuery.support.changeBubbles ) {

	var changeFilters,

	getVal = function( elem ) {
		var type = elem.type, val = elem.value;

		if ( type === "radio" || type === "checkbox" ) {
			val = elem.checked;

		} else if ( type === "select-multiple" ) {
			val = elem.selectedIndex > -1 ?
				jQuery.map( elem.options, function( elem ) {
					return elem.selected;
				}).join("-") :
				"";

		} else if ( elem.nodeName.toLowerCase() === "select" ) {
			val = elem.selectedIndex;
		}

		return val;
	},

	testChange = function testChange( e ) {
		var elem = e.target, data, val;

		if ( !rformElems.test( elem.nodeName ) || elem.readOnly ) {
			return;
		}

		data = jQuery._data( elem, "_change_data" );
		val = getVal(elem);

		// the current data will be also retrieved by beforeactivate
		if ( e.type !== "focusout" || elem.type !== "radio" ) {
			jQuery._data( elem, "_change_data", val );
		}

		if ( data === undefined || val === data ) {
			return;
		}

		if ( data != null || val ) {
			e.type = "change";
			e.liveFired = undefined;
			jQuery.event.trigger( e, arguments[1], elem );
		}
	};

	jQuery.event.special.change = {
		filters: {
			focusout: testChange,

			beforedeactivate: testChange,

			click: function( e ) {
				var elem = e.target, type = elem.type;

				if ( type === "radio" || type === "checkbox" || elem.nodeName.toLowerCase() === "select" ) {
					testChange.call( this, e );
				}
			},

			// Change has to be called before submit
			// Keydown will be called before keypress, which is used in submit-event delegation
			keydown: function( e ) {
				var elem = e.target, type = elem.type;

				if ( (e.keyCode === 13 && elem.nodeName.toLowerCase() !== "textarea") ||
					(e.keyCode === 32 && (type === "checkbox" || type === "radio")) ||
					type === "select-multiple" ) {
					testChange.call( this, e );
				}
			},

			// Beforeactivate happens also before the previous element is blurred
			// with this event you can't trigger a change event, but you can store
			// information
			beforeactivate: function( e ) {
				var elem = e.target;
				jQuery._data( elem, "_change_data", getVal(elem) );
			}
		},

		setup: function( data, namespaces ) {
			if ( this.type === "file" ) {
				return false;
			}

			for ( var type in changeFilters ) {
				jQuery.event.add( this, type + ".specialChange", changeFilters[type] );
			}

			return rformElems.test( this.nodeName );
		},

		teardown: function( namespaces ) {
			jQuery.event.remove( this, ".specialChange" );

			return rformElems.test( this.nodeName );
		}
	};

	changeFilters = jQuery.event.special.change.filters;

	// Handle when the input is .focus()'d
	changeFilters.focus = changeFilters.beforeactivate;
}

function trigger( type, elem, args ) {
	// Piggyback on a donor event to simulate a different one.
	// Fake originalEvent to avoid donor's stopPropagation, but if the
	// simulated event prevents default then we do the same on the donor.
	// Don't pass args or remember liveFired; they apply to the donor event.
	var event = jQuery.extend( {}, args[ 0 ] );
	event.type = type;
	event.originalEvent = {};
	event.liveFired = undefined;
	jQuery.event.handle.call( elem, event );
	if ( event.isDefaultPrevented() ) {
		args[ 0 ].preventDefault();
	}
}

// Create "bubbling" focus and blur events
if ( document.addEventListener ) {
	jQuery.each({ focus: "focusin", blur: "focusout" }, function( orig, fix ) {
		jQuery.event.special[ fix ] = {
			setup: function() {
				this.addEventListener( orig, handler, true );
			},
			teardown: function() {
				this.removeEventListener( orig, handler, true );
			}
		};

		function handler( e ) {
			e = jQuery.event.fix( e );
			e.type = fix;
			return jQuery.event.handle.call( this, e );
		}
	});
}

jQuery.each(["bind", "one"], function( i, name ) {
	jQuery.fn[ name ] = function( type, data, fn ) {
		// Handle object literals
		if ( typeof type === "object" ) {
			for ( var key in type ) {
				this[ name ](key, data, type[key], fn);
			}
			return this;
		}

		if ( jQuery.isFunction( data ) || data === false ) {
			fn = data;
			data = undefined;
		}

		var handler = name === "one" ? jQuery.proxy( fn, function( event ) {
			jQuery( this ).unbind( event, handler );
			return fn.apply( this, arguments );
		}) : fn;

		if ( type === "unload" && name !== "one" ) {
			this.one( type, data, fn );

		} else {
			for ( var i = 0, l = this.length; i < l; i++ ) {
				jQuery.event.add( this[i], type, handler, data );
			}
		}

		return this;
	};
});

jQuery.fn.extend({
	unbind: function( type, fn ) {
		// Handle object literals
		if ( typeof type === "object" && !type.preventDefault ) {
			for ( var key in type ) {
				this.unbind(key, type[key]);
			}

		} else {
			for ( var i = 0, l = this.length; i < l; i++ ) {
				jQuery.event.remove( this[i], type, fn );
			}
		}

		return this;
	},

	delegate: function( selector, types, data, fn ) {
		return this.live( types, data, fn, selector );
	},

	undelegate: function( selector, types, fn ) {
		if ( arguments.length === 0 ) {
				return this.unbind( "live" );

		} else {
			return this.die( types, null, fn, selector );
		}
	},

	trigger: function( type, data ) {
		return this.each(function() {
			jQuery.event.trigger( type, data, this );
		});
	},

	triggerHandler: function( type, data ) {
		if ( this[0] ) {
			var event = jQuery.Event( type );
			event.preventDefault();
			event.stopPropagation();
			jQuery.event.trigger( event, data, this[0] );
			return event.result;
		}
	},

	toggle: function( fn ) {
		// Save reference to arguments for access in closure
		var args = arguments,
			i = 1;

		// link all the functions, so any of them can unbind this click handler
		while ( i < args.length ) {
			jQuery.proxy( fn, args[ i++ ] );
		}

		return this.click( jQuery.proxy( fn, function( event ) {
			// Figure out which function to execute
			var lastToggle = ( jQuery._data( this, "lastToggle" + fn.guid ) || 0 ) % i;
			jQuery._data( this, "lastToggle" + fn.guid, lastToggle + 1 );

			// Make sure that clicks stop
			event.preventDefault();

			// and execute the function
			return args[ lastToggle ].apply( this, arguments ) || false;
		}));
	},

	hover: function( fnOver, fnOut ) {
		return this.mouseenter( fnOver ).mouseleave( fnOut || fnOver );
	}
});

var liveMap = {
	focus: "focusin",
	blur: "focusout",
	mouseenter: "mouseover",
	mouseleave: "mouseout"
};

jQuery.each(["live", "die"], function( i, name ) {
	jQuery.fn[ name ] = function( types, data, fn, origSelector /* Internal Use Only */ ) {
		var type, i = 0, match, namespaces, preType,
			selector = origSelector || this.selector,
			context = origSelector ? this : jQuery( this.context );

		if ( typeof types === "object" && !types.preventDefault ) {
			for ( var key in types ) {
				context[ name ]( key, data, types[key], selector );
			}

			return this;
		}

		if ( jQuery.isFunction( data ) ) {
			fn = data;
			data = undefined;
		}

		types = (types || "").split(" ");

		while ( (type = types[ i++ ]) != null ) {
			match = rnamespaces.exec( type );
			namespaces = "";

			if ( match )  {
				namespaces = match[0];
				type = type.replace( rnamespaces, "" );
			}

			if ( type === "hover" ) {
				types.push( "mouseenter" + namespaces, "mouseleave" + namespaces );
				continue;
			}

			preType = type;

			if ( type === "focus" || type === "blur" ) {
				types.push( liveMap[ type ] + namespaces );
				type = type + namespaces;

			} else {
				type = (liveMap[ type ] || type) + namespaces;
			}

			if ( name === "live" ) {
				// bind live handler
				for ( var j = 0, l = context.length; j < l; j++ ) {
					jQuery.event.add( context[j], "live." + liveConvert( type, selector ),
						{ data: data, selector: selector, handler: fn, origType: type, origHandler: fn, preType: preType } );
				}

			} else {
				// unbind live handler
				context.unbind( "live." + liveConvert( type, selector ), fn );
			}
		}

		return this;
	};
});

function liveHandler( event ) {
	var stop, maxLevel, related, match, handleObj, elem, j, i, l, data, close, namespace, ret,
		elems = [],
		selectors = [],
		events = jQuery._data( this, "events" );

	// Make sure we avoid non-left-click bubbling in Firefox (#3861) and disabled elements in IE (#6911)
	if ( event.liveFired === this || !events || !events.live || event.target.disabled || event.button && event.type === "click" ) {
		return;
	}

	if ( event.namespace ) {
		namespace = new RegExp("(^|\\.)" + event.namespace.split(".").join("\\.(?:.*\\.)?") + "(\\.|$)");
	}

	event.liveFired = this;

	var live = events.live.slice(0);

	for ( j = 0; j < live.length; j++ ) {
		handleObj = live[j];

		if ( handleObj.origType.replace( rnamespaces, "" ) === event.type ) {
			selectors.push( handleObj.selector );

		} else {
			live.splice( j--, 1 );
		}
	}

	match = jQuery( event.target ).closest( selectors, event.currentTarget );

	for ( i = 0, l = match.length; i < l; i++ ) {
		close = match[i];

		for ( j = 0; j < live.length; j++ ) {
			handleObj = live[j];

			if ( close.selector === handleObj.selector && (!namespace || namespace.test( handleObj.namespace )) && !close.elem.disabled ) {
				elem = close.elem;
				related = null;

				// Those two events require additional checking
				if ( handleObj.preType === "mouseenter" || handleObj.preType === "mouseleave" ) {
					event.type = handleObj.preType;
					related = jQuery( event.relatedTarget ).closest( handleObj.selector )[0];
				}

				if ( !related || related !== elem ) {
					elems.push({ elem: elem, handleObj: handleObj, level: close.level });
				}
			}
		}
	}

	for ( i = 0, l = elems.length; i < l; i++ ) {
		match = elems[i];

		if ( maxLevel && match.level > maxLevel ) {
			break;
		}

		event.currentTarget = match.elem;
		event.data = match.handleObj.data;
		event.handleObj = match.handleObj;

		ret = match.handleObj.origHandler.apply( match.elem, arguments );

		if ( ret === false || event.isPropagationStopped() ) {
			maxLevel = match.level;

			if ( ret === false ) {
				stop = false;
			}
			if ( event.isImmediatePropagationStopped() ) {
				break;
			}
		}
	}

	return stop;
}

function liveConvert( type, selector ) {
	return (type && type !== "*" ? type + "." : "") + selector.replace(rperiod, "`").replace(rspace, "&");
}

jQuery.each( ("blur focus focusin focusout load resize scroll unload click dblclick " +
	"mousedown mouseup mousemove mouseover mouseout mouseenter mouseleave " +
	"change select submit keydown keypress keyup error").split(" "), function( i, name ) {

	// Handle event binding
	jQuery.fn[ name ] = function( data, fn ) {
		if ( fn == null ) {
			fn = data;
			data = null;
		}

		return arguments.length > 0 ?
			this.bind( name, data, fn ) :
			this.trigger( name );
	};

	if ( jQuery.attrFn ) {
		jQuery.attrFn[ name ] = true;
	}
});


/*!
 * Sizzle CSS Selector Engine
 *  Copyright 2011, The Dojo Foundation
 *  Released under the MIT, BSD, and GPL Licenses.
 *  More information: http://sizzlejs.com/
 */
(function(){

var chunker = /((?:\((?:\([^()]+\)|[^()]+)+\)|\[(?:\[[^\[\]]*\]|['"][^'"]*['"]|[^\[\]'"]+)+\]|\\.|[^ >+~,(\[\\]+)+|[>+~])(\s*,\s*)?((?:.|\r|\n)*)/g,
	done = 0,
	toString = Object.prototype.toString,
	hasDuplicate = false,
	baseHasDuplicate = true,
	rBackslash = /\\/g,
	rNonWord = /\W/;

// Here we check if the JavaScript engine is using some sort of
// optimization where it does not always call our comparision
// function. If that is the case, discard the hasDuplicate value.
//   Thus far that includes Google Chrome.
[0, 0].sort(function() {
	baseHasDuplicate = false;
	return 0;
});

var Sizzle = function( selector, context, results, seed ) {
	results = results || [];
	context = context || document;

	var origContext = context;

	if ( context.nodeType !== 1 && context.nodeType !== 9 ) {
		return [];
	}
	
	if ( !selector || typeof selector !== "string" ) {
		return results;
	}

	var m, set, checkSet, extra, ret, cur, pop, i,
		prune = true,
		contextXML = Sizzle.isXML( context ),
		parts = [],
		soFar = selector;
	
	// Reset the position of the chunker regexp (start from head)
	do {
		chunker.exec( "" );
		m = chunker.exec( soFar );

		if ( m ) {
			soFar = m[3];
		
			parts.push( m[1] );
		
			if ( m[2] ) {
				extra = m[3];
				break;
			}
		}
	} while ( m );

	if ( parts.length > 1 && origPOS.exec( selector ) ) {

		if ( parts.length === 2 && Expr.relative[ parts[0] ] ) {
			set = posProcess( parts[0] + parts[1], context );

		} else {
			set = Expr.relative[ parts[0] ] ?
				[ context ] :
				Sizzle( parts.shift(), context );

			while ( parts.length ) {
				selector = parts.shift();

				if ( Expr.relative[ selector ] ) {
					selector += parts.shift();
				}
				
				set = posProcess( selector, set );
			}
		}

	} else {
		// Take a shortcut and set the context if the root selector is an ID
		// (but not if it'll be faster if the inner selector is an ID)
		if ( !seed && parts.length > 1 && context.nodeType === 9 && !contextXML &&
				Expr.match.ID.test(parts[0]) && !Expr.match.ID.test(parts[parts.length - 1]) ) {

			ret = Sizzle.find( parts.shift(), context, contextXML );
			context = ret.expr ?
				Sizzle.filter( ret.expr, ret.set )[0] :
				ret.set[0];
		}

		if ( context ) {
			ret = seed ?
				{ expr: parts.pop(), set: makeArray(seed) } :
				Sizzle.find( parts.pop(), parts.length === 1 && (parts[0] === "~" || parts[0] === "+") && context.parentNode ? context.parentNode : context, contextXML );

			set = ret.expr ?
				Sizzle.filter( ret.expr, ret.set ) :
				ret.set;

			if ( parts.length > 0 ) {
				checkSet = makeArray( set );

			} else {
				prune = false;
			}

			while ( parts.length ) {
				cur = parts.pop();
				pop = cur;

				if ( !Expr.relative[ cur ] ) {
					cur = "";
				} else {
					pop = parts.pop();
				}

				if ( pop == null ) {
					pop = context;
				}

				Expr.relative[ cur ]( checkSet, pop, contextXML );
			}

		} else {
			checkSet = parts = [];
		}
	}

	if ( !checkSet ) {
		checkSet = set;
	}

	if ( !checkSet ) {
		Sizzle.error( cur || selector );
	}

	if ( toString.call(checkSet) === "[object Array]" ) {
		if ( !prune ) {
			results.push.apply( results, checkSet );

		} else if ( context && context.nodeType === 1 ) {
			for ( i = 0; checkSet[i] != null; i++ ) {
				if ( checkSet[i] && (checkSet[i] === true || checkSet[i].nodeType === 1 && Sizzle.contains(context, checkSet[i])) ) {
					results.push( set[i] );
				}
			}

		} else {
			for ( i = 0; checkSet[i] != null; i++ ) {
				if ( checkSet[i] && checkSet[i].nodeType === 1 ) {
					results.push( set[i] );
				}
			}
		}

	} else {
		makeArray( checkSet, results );
	}

	if ( extra ) {
		Sizzle( extra, origContext, results, seed );
		Sizzle.uniqueSort( results );
	}

	return results;
};

Sizzle.uniqueSort = function( results ) {
	if ( sortOrder ) {
		hasDuplicate = baseHasDuplicate;
		results.sort( sortOrder );

		if ( hasDuplicate ) {
			for ( var i = 1; i < results.length; i++ ) {
				if ( results[i] === results[ i - 1 ] ) {
					results.splice( i--, 1 );
				}
			}
		}
	}

	return results;
};

Sizzle.matches = function( expr, set ) {
	return Sizzle( expr, null, null, set );
};

Sizzle.matchesSelector = function( node, expr ) {
	return Sizzle( expr, null, null, [node] ).length > 0;
};

Sizzle.find = function( expr, context, isXML ) {
	var set;

	if ( !expr ) {
		return [];
	}

	for ( var i = 0, l = Expr.order.length; i < l; i++ ) {
		var match,
			type = Expr.order[i];
		
		if ( (match = Expr.leftMatch[ type ].exec( expr )) ) {
			var left = match[1];
			match.splice( 1, 1 );

			if ( left.substr( left.length - 1 ) !== "\\" ) {
				match[1] = (match[1] || "").replace( rBackslash, "" );
				set = Expr.find[ type ]( match, context, isXML );

				if ( set != null ) {
					expr = expr.replace( Expr.match[ type ], "" );
					break;
				}
			}
		}
	}

	if ( !set ) {
		set = typeof context.getElementsByTagName !== "undefined" ?
			context.getElementsByTagName( "*" ) :
			[];
	}

	return { set: set, expr: expr };
};

Sizzle.filter = function( expr, set, inplace, not ) {
	var match, anyFound,
		old = expr,
		result = [],
		curLoop = set,
		isXMLFilter = set && set[0] && Sizzle.isXML( set[0] );

	while ( expr && set.length ) {
		for ( var type in Expr.filter ) {
			if ( (match = Expr.leftMatch[ type ].exec( expr )) != null && match[2] ) {
				var found, item,
					filter = Expr.filter[ type ],
					left = match[1];

				anyFound = false;

				match.splice(1,1);

				if ( left.substr( left.length - 1 ) === "\\" ) {
					continue;
				}

				if ( curLoop === result ) {
					result = [];
				}

				if ( Expr.preFilter[ type ] ) {
					match = Expr.preFilter[ type ]( match, curLoop, inplace, result, not, isXMLFilter );

					if ( !match ) {
						anyFound = found = true;

					} else if ( match === true ) {
						continue;
					}
				}

				if ( match ) {
					for ( var i = 0; (item = curLoop[i]) != null; i++ ) {
						if ( item ) {
							found = filter( item, match, i, curLoop );
							var pass = not ^ !!found;

							if ( inplace && found != null ) {
								if ( pass ) {
									anyFound = true;

								} else {
									curLoop[i] = false;
								}

							} else if ( pass ) {
								result.push( item );
								anyFound = true;
							}
						}
					}
				}

				if ( found !== undefined ) {
					if ( !inplace ) {
						curLoop = result;
					}

					expr = expr.replace( Expr.match[ type ], "" );

					if ( !anyFound ) {
						return [];
					}

					break;
				}
			}
		}

		// Improper expression
		if ( expr === old ) {
			if ( anyFound == null ) {
				Sizzle.error( expr );

			} else {
				break;
			}
		}

		old = expr;
	}

	return curLoop;
};

Sizzle.error = function( msg ) {
	throw "Syntax error, unrecognized expression: " + msg;
};

var Expr = Sizzle.selectors = {
	order: [ "ID", "NAME", "TAG" ],

	match: {
		ID: /#((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,
		CLASS: /\.((?:[\w\u00c0-\uFFFF\-]|\\.)+)/,
		NAME: /\[name=['"]*((?:[\w\u00c0-\uFFFF\-]|\\.)+)['"]*\]/,
		ATTR: /\[\s*((?:[\w\u00c0-\uFFFF\-]|\\.)+)\s*(?:(\S?=)\s*(?:(['"])(.*?)\3|(#?(?:[\w\u00c0-\uFFFF\-]|\\.)*)|)|)\s*\]/,
		TAG: /^((?:[\w\u00c0-\uFFFF\*\-]|\\.)+)/,
		CHILD: /:(only|nth|last|first)-child(?:\(\s*(even|odd|(?:[+\-]?\d+|(?:[+\-]?\d*)?n\s*(?:[+\-]\s*\d+)?))\s*\))?/,
		POS: /:(nth|eq|gt|lt|first|last|even|odd)(?:\((\d*)\))?(?=[^\-]|$)/,
		PSEUDO: /:((?:[\w\u00c0-\uFFFF\-]|\\.)+)(?:\((['"]?)((?:\([^\)]+\)|[^\(\)]*)+)\2\))?/
	},

	leftMatch: {},

	attrMap: {
		"class": "className",
		"for": "htmlFor"
	},

	attrHandle: {
		href: function( elem ) {
			return elem.getAttribute( "href" );
		},
		type: function( elem ) {
			return elem.getAttribute( "type" );
		}
	},

	relative: {
		"+": function(checkSet, part){
			var isPartStr = typeof part === "string",
				isTag = isPartStr && !rNonWord.test( part ),
				isPartStrNotTag = isPartStr && !isTag;

			if ( isTag ) {
				part = part.toLowerCase();
			}

			for ( var i = 0, l = checkSet.length, elem; i < l; i++ ) {
				if ( (elem = checkSet[i]) ) {
					while ( (elem = elem.previousSibling) && elem.nodeType !== 1 ) {}

					checkSet[i] = isPartStrNotTag || elem && elem.nodeName.toLowerCase() === part ?
						elem || false :
						elem === part;
				}
			}

			if ( isPartStrNotTag ) {
				Sizzle.filter( part, checkSet, true );
			}
		},

		">": function( checkSet, part ) {
			var elem,
				isPartStr = typeof part === "string",
				i = 0,
				l = checkSet.length;

			if ( isPartStr && !rNonWord.test( part ) ) {
				part = part.toLowerCase();

				for ( ; i < l; i++ ) {
					elem = checkSet[i];

					if ( elem ) {
						var parent = elem.parentNode;
						checkSet[i] = parent.nodeName.toLowerCase() === part ? parent : false;
					}
				}

			} else {
				for ( ; i < l; i++ ) {
					elem = checkSet[i];

					if ( elem ) {
						checkSet[i] = isPartStr ?
							elem.parentNode :
							elem.parentNode === part;
					}
				}

				if ( isPartStr ) {
					Sizzle.filter( part, checkSet, true );
				}
			}
		},

		"": function(checkSet, part, isXML){
			var nodeCheck,
				doneName = done++,
				checkFn = dirCheck;

			if ( typeof part === "string" && !rNonWord.test( part ) ) {
				part = part.toLowerCase();
				nodeCheck = part;
				checkFn = dirNodeCheck;
			}

			checkFn( "parentNode", part, doneName, checkSet, nodeCheck, isXML );
		},

		"~": function( checkSet, part, isXML ) {
			var nodeCheck,
				doneName = done++,
				checkFn = dirCheck;

			if ( typeof part === "string" && !rNonWord.test( part ) ) {
				part = part.toLowerCase();
				nodeCheck = part;
				checkFn = dirNodeCheck;
			}

			checkFn( "previousSibling", part, doneName, checkSet, nodeCheck, isXML );
		}
	},

	find: {
		ID: function( match, context, isXML ) {
			if ( typeof context.getElementById !== "undefined" && !isXML ) {
				var m = context.getElementById(match[1]);
				// Check parentNode to catch when Blackberry 4.6 returns
				// nodes that are no longer in the document #6963
				return m && m.parentNode ? [m] : [];
			}
		},

		NAME: function( match, context ) {
			if ( typeof context.getElementsByName !== "undefined" ) {
				var ret = [],
					results = context.getElementsByName( match[1] );

				for ( var i = 0, l = results.length; i < l; i++ ) {
					if ( results[i].getAttribute("name") === match[1] ) {
						ret.push( results[i] );
					}
				}

				return ret.length === 0 ? null : ret;
			}
		},

		TAG: function( match, context ) {
			if ( typeof context.getElementsByTagName !== "undefined" ) {
				return context.getElementsByTagName( match[1] );
			}
		}
	},
	preFilter: {
		CLASS: function( match, curLoop, inplace, result, not, isXML ) {
			match = " " + match[1].replace( rBackslash, "" ) + " ";

			if ( isXML ) {
				return match;
			}

			for ( var i = 0, elem; (elem = curLoop[i]) != null; i++ ) {
				if ( elem ) {
					if ( not ^ (elem.className && (" " + elem.className + " ").replace(/[\t\n\r]/g, " ").indexOf(match) >= 0) ) {
						if ( !inplace ) {
							result.push( elem );
						}

					} else if ( inplace ) {
						curLoop[i] = false;
					}
				}
			}

			return false;
		},

		ID: function( match ) {
			return match[1].replace( rBackslash, "" );
		},

		TAG: function( match, curLoop ) {
			return match[1].replace( rBackslash, "" ).toLowerCase();
		},

		CHILD: function( match ) {
			if ( match[1] === "nth" ) {
				if ( !match[2] ) {
					Sizzle.error( match[0] );
				}

				match[2] = match[2].replace(/^\+|\s*/g, '');

				// parse equations like 'even', 'odd', '5', '2n', '3n+2', '4n-1', '-n+6'
				var test = /(-?)(\d*)(?:n([+\-]?\d*))?/.exec(
					match[2] === "even" && "2n" || match[2] === "odd" && "2n+1" ||
					!/\D/.test( match[2] ) && "0n+" + match[2] || match[2]);

				// calculate the numbers (first)n+(last) including if they are negative
				match[2] = (test[1] + (test[2] || 1)) - 0;
				match[3] = test[3] - 0;
			}
			else if ( match[2] ) {
				Sizzle.error( match[0] );
			}

			// TODO: Move to normal caching system
			match[0] = done++;

			return match;
		},

		ATTR: function( match, curLoop, inplace, result, not, isXML ) {
			var name = match[1] = match[1].replace( rBackslash, "" );
			
			if ( !isXML && Expr.attrMap[name] ) {
				match[1] = Expr.attrMap[name];
			}

			// Handle if an un-quoted value was used
			match[4] = ( match[4] || match[5] || "" ).replace( rBackslash, "" );

			if ( match[2] === "~=" ) {
				match[4] = " " + match[4] + " ";
			}

			return match;
		},

		PSEUDO: function( match, curLoop, inplace, result, not ) {
			if ( match[1] === "not" ) {
				// If we're dealing with a complex expression, or a simple one
				if ( ( chunker.exec(match[3]) || "" ).length > 1 || /^\w/.test(match[3]) ) {
					match[3] = Sizzle(match[3], null, null, curLoop);

				} else {
					var ret = Sizzle.filter(match[3], curLoop, inplace, true ^ not);

					if ( !inplace ) {
						result.push.apply( result, ret );
					}

					return false;
				}

			} else if ( Expr.match.POS.test( match[0] ) || Expr.match.CHILD.test( match[0] ) ) {
				return true;
			}
			
			return match;
		},

		POS: function( match ) {
			match.unshift( true );

			return match;
		}
	},
	
	filters: {
		enabled: function( elem ) {
			return elem.disabled === false && elem.type !== "hidden";
		},

		disabled: function( elem ) {
			return elem.disabled === true;
		},

		checked: function( elem ) {
			return elem.checked === true;
		},
		
		selected: function( elem ) {
			// Accessing this property makes selected-by-default
			// options in Safari work properly
			if ( elem.parentNode ) {
				elem.parentNode.selectedIndex;
			}
			
			return elem.selected === true;
		},

		parent: function( elem ) {
			return !!elem.firstChild;
		},

		empty: function( elem ) {
			return !elem.firstChild;
		},

		has: function( elem, i, match ) {
			return !!Sizzle( match[3], elem ).length;
		},

		header: function( elem ) {
			return (/h\d/i).test( elem.nodeName );
		},

		text: function( elem ) {
			// IE6 and 7 will map elem.type to 'text' for new HTML5 types (search, etc) 
			// use getAttribute instead to test this case
			return "text" === elem.getAttribute( 'type' );
		},
		radio: function( elem ) {
			return "radio" === elem.type;
		},

		checkbox: function( elem ) {
			return "checkbox" === elem.type;
		},

		file: function( elem ) {
			return "file" === elem.type;
		},
		password: function( elem ) {
			return "password" === elem.type;
		},

		submit: function( elem ) {
			return "submit" === elem.type;
		},

		image: function( elem ) {
			return "image" === elem.type;
		},

		reset: function( elem ) {
			return "reset" === elem.type;
		},

		button: function( elem ) {
			return "button" === elem.type || elem.nodeName.toLowerCase() === "button";
		},

		input: function( elem ) {
			return (/input|select|textarea|button/i).test( elem.nodeName );
		}
	},
	setFilters: {
		first: function( elem, i ) {
			return i === 0;
		},

		last: function( elem, i, match, array ) {
			return i === array.length - 1;
		},

		even: function( elem, i ) {
			return i % 2 === 0;
		},

		odd: function( elem, i ) {
			return i % 2 === 1;
		},

		lt: function( elem, i, match ) {
			return i < match[3] - 0;
		},

		gt: function( elem, i, match ) {
			return i > match[3] - 0;
		},

		nth: function( elem, i, match ) {
			return match[3] - 0 === i;
		},

		eq: function( elem, i, match ) {
			return match[3] - 0 === i;
		}
	},
	filter: {
		PSEUDO: function( elem, match, i, array ) {
			var name = match[1],
				filter = Expr.filters[ name ];

			if ( filter ) {
				return filter( elem, i, match, array );

			} else if ( name === "contains" ) {
				return (elem.textContent || elem.innerText || Sizzle.getText([ elem ]) || "").indexOf(match[3]) >= 0;

			} else if ( name === "not" ) {
				var not = match[3];

				for ( var j = 0, l = not.length; j < l; j++ ) {
					if ( not[j] === elem ) {
						return false;
					}
				}

				return true;

			} else {
				Sizzle.error( name );
			}
		},

		CHILD: function( elem, match ) {
			var type = match[1],
				node = elem;

			switch ( type ) {
				case "only":
				case "first":
					while ( (node = node.previousSibling) )	 {
						if ( node.nodeType === 1 ) { 
							return false; 
						}
					}

					if ( type === "first" ) { 
						return true; 
					}

					node = elem;

				case "last":
					while ( (node = node.nextSibling) )	 {
						if ( node.nodeType === 1 ) { 
							return false; 
						}
					}

					return true;

				case "nth":
					var first = match[2],
						last = match[3];

					if ( first === 1 && last === 0 ) {
						return true;
					}
					
					var doneName = match[0],
						parent = elem.parentNode;
	
					if ( parent && (parent.sizcache !== doneName || !elem.nodeIndex) ) {
						var count = 0;
						
						for ( node = parent.firstChild; node; node = node.nextSibling ) {
							if ( node.nodeType === 1 ) {
								node.nodeIndex = ++count;
							}
						} 

						parent.sizcache = doneName;
					}
					
					var diff = elem.nodeIndex - last;

					if ( first === 0 ) {
						return diff === 0;

					} else {
						return ( diff % first === 0 && diff / first >= 0 );
					}
			}
		},

		ID: function( elem, match ) {
			return elem.nodeType === 1 && elem.getAttribute("id") === match;
		},

		TAG: function( elem, match ) {
			return (match === "*" && elem.nodeType === 1) || elem.nodeName.toLowerCase() === match;
		},
		
		CLASS: function( elem, match ) {
			return (" " + (elem.className || elem.getAttribute("class")) + " ")
				.indexOf( match ) > -1;
		},

		ATTR: function( elem, match ) {
			var name = match[1],
				result = Expr.attrHandle[ name ] ?
					Expr.attrHandle[ name ]( elem ) :
					elem[ name ] != null ?
						elem[ name ] :
						elem.getAttribute( name ),
				value = result + "",
				type = match[2],
				check = match[4];

			return result == null ?
				type === "!=" :
				type === "=" ?
				value === check :
				type === "*=" ?
				value.indexOf(check) >= 0 :
				type === "~=" ?
				(" " + value + " ").indexOf(check) >= 0 :
				!check ?
				value && result !== false :
				type === "!=" ?
				value !== check :
				type === "^=" ?
				value.indexOf(check) === 0 :
				type === "$=" ?
				value.substr(value.length - check.length) === check :
				type === "|=" ?
				value === check || value.substr(0, check.length + 1) === check + "-" :
				false;
		},

		POS: function( elem, match, i, array ) {
			var name = match[2],
				filter = Expr.setFilters[ name ];

			if ( filter ) {
				return filter( elem, i, match, array );
			}
		}
	}
};

var origPOS = Expr.match.POS,
	fescape = function(all, num){
		return "\\" + (num - 0 + 1);
	};

for ( var type in Expr.match ) {
	Expr.match[ type ] = new RegExp( Expr.match[ type ].source + (/(?![^\[]*\])(?![^\(]*\))/.source) );
	Expr.leftMatch[ type ] = new RegExp( /(^(?:.|\r|\n)*?)/.source + Expr.match[ type ].source.replace(/\\(\d+)/g, fescape) );
}

var makeArray = function( array, results ) {
	array = Array.prototype.slice.call( array, 0 );

	if ( results ) {
		results.push.apply( results, array );
		return results;
	}
	
	return array;
};

// Perform a simple check to determine if the browser is capable of
// converting a NodeList to an array using builtin methods.
// Also verifies that the returned array holds DOM nodes
// (which is not the case in the Blackberry browser)
try {
	Array.prototype.slice.call( document.documentElement.childNodes, 0 )[0].nodeType;

// Provide a fallback method if it does not work
} catch( e ) {
	makeArray = function( array, results ) {
		var i = 0,
			ret = results || [];

		if ( toString.call(array) === "[object Array]" ) {
			Array.prototype.push.apply( ret, array );

		} else {
			if ( typeof array.length === "number" ) {
				for ( var l = array.length; i < l; i++ ) {
					ret.push( array[i] );
				}

			} else {
				for ( ; array[i]; i++ ) {
					ret.push( array[i] );
				}
			}
		}

		return ret;
	};
}

var sortOrder, siblingCheck;

if ( document.documentElement.compareDocumentPosition ) {
	sortOrder = function( a, b ) {
		if ( a === b ) {
			hasDuplicate = true;
			return 0;
		}

		if ( !a.compareDocumentPosition || !b.compareDocumentPosition ) {
			return a.compareDocumentPosition ? -1 : 1;
		}

		return a.compareDocumentPosition(b) & 4 ? -1 : 1;
	};

} else {
	sortOrder = function( a, b ) {
		var al, bl,
			ap = [],
			bp = [],
			aup = a.parentNode,
			bup = b.parentNode,
			cur = aup;

		// The nodes are identical, we can exit early
		if ( a === b ) {
			hasDuplicate = true;
			return 0;

		// If the nodes are siblings (or identical) we can do a quick check
		} else if ( aup === bup ) {
			return siblingCheck( a, b );

		// If no parents were found then the nodes are disconnected
		} else if ( !aup ) {
			return -1;

		} else if ( !bup ) {
			return 1;
		}

		// Otherwise they're somewhere else in the tree so we need
		// to build up a full list of the parentNodes for comparison
		while ( cur ) {
			ap.unshift( cur );
			cur = cur.parentNode;
		}

		cur = bup;

		while ( cur ) {
			bp.unshift( cur );
			cur = cur.parentNode;
		}

		al = ap.length;
		bl = bp.length;

		// Start walking down the tree looking for a discrepancy
		for ( var i = 0; i < al && i < bl; i++ ) {
			if ( ap[i] !== bp[i] ) {
				return siblingCheck( ap[i], bp[i] );
			}
		}

		// We ended someplace up the tree so do a sibling check
		return i === al ?
			siblingCheck( a, bp[i], -1 ) :
			siblingCheck( ap[i], b, 1 );
	};

	siblingCheck = function( a, b, ret ) {
		if ( a === b ) {
			return ret;
		}

		var cur = a.nextSibling;

		while ( cur ) {
			if ( cur === b ) {
				return -1;
			}

			cur = cur.nextSibling;
		}

		return 1;
	};
}

// Utility function for retreiving the text value of an array of DOM nodes
Sizzle.getText = function( elems ) {
	var ret = "", elem;

	for ( var i = 0; elems[i]; i++ ) {
		elem = elems[i];

		// Get the text from text nodes and CDATA nodes
		if ( elem.nodeType === 3 || elem.nodeType === 4 ) {
			ret += elem.nodeValue;

		// Traverse everything else, except comment nodes
		} else if ( elem.nodeType !== 8 ) {
			ret += Sizzle.getText( elem.childNodes );
		}
	}

	return ret;
};

// Check to see if the browser returns elements by name when
// querying by getElementById (and provide a workaround)
(function(){
	// We're going to inject a fake input element with a specified name
	var form = document.createElement("div"),
		id = "script" + (new Date()).getTime(),
		root = document.documentElement;

	form.innerHTML = "<a name='" + id + "'/>";

	// Inject it into the root element, check its status, and remove it quickly
	root.insertBefore( form, root.firstChild );

	// The workaround has to do additional checks after a getElementById
	// Which slows things down for other browsers (hence the branching)
	if ( document.getElementById( id ) ) {
		Expr.find.ID = function( match, context, isXML ) {
			if ( typeof context.getElementById !== "undefined" && !isXML ) {
				var m = context.getElementById(match[1]);

				return m ?
					m.id === match[1] || typeof m.getAttributeNode !== "undefined" && m.getAttributeNode("id").nodeValue === match[1] ?
						[m] :
						undefined :
					[];
			}
		};

		Expr.filter.ID = function( elem, match ) {
			var node = typeof elem.getAttributeNode !== "undefined" && elem.getAttributeNode("id");

			return elem.nodeType === 1 && node && node.nodeValue === match;
		};
	}

	root.removeChild( form );

	// release memory in IE
	root = form = null;
})();

(function(){
	// Check to see if the browser returns only elements
	// when doing getElementsByTagName("*")

	// Create a fake element
	var div = document.createElement("div");
	div.appendChild( document.createComment("") );

	// Make sure no comments are found
	if ( div.getElementsByTagName("*").length > 0 ) {
		Expr.find.TAG = function( match, context ) {
			var results = context.getElementsByTagName( match[1] );

			// Filter out possible comments
			if ( match[1] === "*" ) {
				var tmp = [];

				for ( var i = 0; results[i]; i++ ) {
					if ( results[i].nodeType === 1 ) {
						tmp.push( results[i] );
					}
				}

				results = tmp;
			}

			return results;
		};
	}

	// Check to see if an attribute returns normalized href attributes
	div.innerHTML = "<a href='#'></a>";

	if ( div.firstChild && typeof div.firstChild.getAttribute !== "undefined" &&
			div.firstChild.getAttribute("href") !== "#" ) {

		Expr.attrHandle.href = function( elem ) {
			return elem.getAttribute( "href", 2 );
		};
	}

	// release memory in IE
	div = null;
})();

if ( document.querySelectorAll ) {
	(function(){
		var oldSizzle = Sizzle,
			div = document.createElement("div"),
			id = "__sizzle__";

		div.innerHTML = "<p class='TEST'></p>";

		// Safari can't handle uppercase or unicode characters when
		// in quirks mode.
		if ( div.querySelectorAll && div.querySelectorAll(".TEST").length === 0 ) {
			return;
		}
	
		Sizzle = function( query, context, extra, seed ) {
			context = context || document;

			// Only use querySelectorAll on non-XML documents
			// (ID selectors don't work in non-HTML documents)
			if ( !seed && !Sizzle.isXML(context) ) {
				// See if we find a selector to speed up
				var match = /^(\w+$)|^\.([\w\-]+$)|^#([\w\-]+$)/.exec( query );
				
				if ( match && (context.nodeType === 1 || context.nodeType === 9) ) {
					// Speed-up: Sizzle("TAG")
					if ( match[1] ) {
						return makeArray( context.getElementsByTagName( query ), extra );
					
					// Speed-up: Sizzle(".CLASS")
					} else if ( match[2] && Expr.find.CLASS && context.getElementsByClassName ) {
						return makeArray( context.getElementsByClassName( match[2] ), extra );
					}
				}
				
				if ( context.nodeType === 9 ) {
					// Speed-up: Sizzle("body")
					// The body element only exists once, optimize finding it
					if ( query === "body" && context.body ) {
						return makeArray( [ context.body ], extra );
						
					// Speed-up: Sizzle("#ID")
					} else if ( match && match[3] ) {
						var elem = context.getElementById( match[3] );

						// Check parentNode to catch when Blackberry 4.6 returns
						// nodes that are no longer in the document #6963
						if ( elem && elem.parentNode ) {
							// Handle the case where IE and Opera return items
							// by name instead of ID
							if ( elem.id === match[3] ) {
								return makeArray( [ elem ], extra );
							}
							
						} else {
							return makeArray( [], extra );
						}
					}
					
					try {
						return makeArray( context.querySelectorAll(query), extra );
					} catch(qsaError) {}

				// qSA works strangely on Element-rooted queries
				// We can work around this by specifying an extra ID on the root
				// and working up from there (Thanks to Andrew Dupont for the technique)
				// IE 8 doesn't work on object elements
				} else if ( context.nodeType === 1 && context.nodeName.toLowerCase() !== "object" ) {
					var oldContext = context,
						old = context.getAttribute( "id" ),
						nid = old || id,
						hasParent = context.parentNode,
						relativeHierarchySelector = /^\s*[+~]/.test( query );

					if ( !old ) {
						context.setAttribute( "id", nid );
					} else {
						nid = nid.replace( /'/g, "\\$&" );
					}
					if ( relativeHierarchySelector && hasParent ) {
						context = context.parentNode;
					}

					try {
						if ( !relativeHierarchySelector || hasParent ) {
							return makeArray( context.querySelectorAll( "[id='" + nid + "'] " + query ), extra );
						}

					} catch(pseudoError) {
					} finally {
						if ( !old ) {
							oldContext.removeAttribute( "id" );
						}
					}
				}
			}
		
			return oldSizzle(query, context, extra, seed);
		};

		for ( var prop in oldSizzle ) {
			Sizzle[ prop ] = oldSizzle[ prop ];
		}

		// release memory in IE
		div = null;
	})();
}

(function(){
	var html = document.documentElement,
		matches = html.matchesSelector || html.mozMatchesSelector || html.webkitMatchesSelector || html.msMatchesSelector,
		pseudoWorks = false;

	try {
		// This should fail with an exception
		// Gecko does not error, returns false instead
		matches.call( document.documentElement, "[test!='']:sizzle" );
	
	} catch( pseudoError ) {
		pseudoWorks = true;
	}

	if ( matches ) {
		Sizzle.matchesSelector = function( node, expr ) {
			// Make sure that attribute selectors are quoted
			expr = expr.replace(/\=\s*([^'"\]]*)\s*\]/g, "='$1']");

			if ( !Sizzle.isXML( node ) ) {
				try { 
					if ( pseudoWorks || !Expr.match.PSEUDO.test( expr ) && !/!=/.test( expr ) ) {
						return matches.call( node, expr );
					}
				} catch(e) {}
			}

			return Sizzle(expr, null, null, [node]).length > 0;
		};
	}
})();

(function(){
	var div = document.createElement("div");

	div.innerHTML = "<div class='test e'></div><div class='test'></div>";

	// Opera can't find a second classname (in 9.6)
	// Also, make sure that getElementsByClassName actually exists
	if ( !div.getElementsByClassName || div.getElementsByClassName("e").length === 0 ) {
		return;
	}

	// Safari caches class attributes, doesn't catch changes (in 3.2)
	div.lastChild.className = "e";

	if ( div.getElementsByClassName("e").length === 1 ) {
		return;
	}
	
	Expr.order.splice(1, 0, "CLASS");
	Expr.find.CLASS = function( match, context, isXML ) {
		if ( typeof context.getElementsByClassName !== "undefined" && !isXML ) {
			return context.getElementsByClassName(match[1]);
		}
	};

	// release memory in IE
	div = null;
})();

function dirNodeCheck( dir, cur, doneName, checkSet, nodeCheck, isXML ) {
	for ( var i = 0, l = checkSet.length; i < l; i++ ) {
		var elem = checkSet[i];

		if ( elem ) {
			var match = false;

			elem = elem[dir];

			while ( elem ) {
				if ( elem.sizcache === doneName ) {
					match = checkSet[elem.sizset];
					break;
				}

				if ( elem.nodeType === 1 && !isXML ){
					elem.sizcache = doneName;
					elem.sizset = i;
				}

				if ( elem.nodeName.toLowerCase() === cur ) {
					match = elem;
					break;
				}

				elem = elem[dir];
			}

			checkSet[i] = match;
		}
	}
}

function dirCheck( dir, cur, doneName, checkSet, nodeCheck, isXML ) {
	for ( var i = 0, l = checkSet.length; i < l; i++ ) {
		var elem = checkSet[i];

		if ( elem ) {
			var match = false;
			
			elem = elem[dir];

			while ( elem ) {
				if ( elem.sizcache === doneName ) {
					match = checkSet[elem.sizset];
					break;
				}

				if ( elem.nodeType === 1 ) {
					if ( !isXML ) {
						elem.sizcache = doneName;
						elem.sizset = i;
					}

					if ( typeof cur !== "string" ) {
						if ( elem === cur ) {
							match = true;
							break;
						}

					} else if ( Sizzle.filter( cur, [elem] ).length > 0 ) {
						match = elem;
						break;
					}
				}

				elem = elem[dir];
			}

			checkSet[i] = match;
		}
	}
}

if ( document.documentElement.contains ) {
	Sizzle.contains = function( a, b ) {
		return a !== b && (a.contains ? a.contains(b) : true);
	};

} else if ( document.documentElement.compareDocumentPosition ) {
	Sizzle.contains = function( a, b ) {
		return !!(a.compareDocumentPosition(b) & 16);
	};

} else {
	Sizzle.contains = function() {
		return false;
	};
}

Sizzle.isXML = function( elem ) {
	// documentElement is verified for cases where it doesn't yet exist
	// (such as loading iframes in IE - #4833) 
	var documentElement = (elem ? elem.ownerDocument || elem : 0).documentElement;

	return documentElement ? documentElement.nodeName !== "HTML" : false;
};

var posProcess = function( selector, context ) {
	var match,
		tmpSet = [],
		later = "",
		root = context.nodeType ? [context] : context;

	// Position selectors must be done after the filter
	// And so must :not(positional) so we move all PSEUDOs to the end
	while ( (match = Expr.match.PSEUDO.exec( selector )) ) {
		later += match[0];
		selector = selector.replace( Expr.match.PSEUDO, "" );
	}

	selector = Expr.relative[selector] ? selector + "*" : selector;

	for ( var i = 0, l = root.length; i < l; i++ ) {
		Sizzle( selector, root[i], tmpSet );
	}

	return Sizzle.filter( later, tmpSet );
};

// EXPOSE
jQuery.find = Sizzle;
jQuery.expr = Sizzle.selectors;
jQuery.expr[":"] = jQuery.expr.filters;
jQuery.unique = Sizzle.uniqueSort;
jQuery.text = Sizzle.getText;
jQuery.isXMLDoc = Sizzle.isXML;
jQuery.contains = Sizzle.contains;


})();


var runtil = /Until$/,
	rparentsprev = /^(?:parents|prevUntil|prevAll)/,
	// Note: This RegExp should be improved, or likely pulled from Sizzle
	rmultiselector = /,/,
	isSimple = /^.[^:#\[\.,]*$/,
	slice = Array.prototype.slice,
	POS = jQuery.expr.match.POS,
	// methods guaranteed to produce a unique set when starting from a unique set
	guaranteedUnique = {
		children: true,
		contents: true,
		next: true,
		prev: true
	};

jQuery.fn.extend({
	find: function( selector ) {
		var ret = this.pushStack( "", "find", selector ),
			length = 0;

		for ( var i = 0, l = this.length; i < l; i++ ) {
			length = ret.length;
			jQuery.find( selector, this[i], ret );

			if ( i > 0 ) {
				// Make sure that the results are unique
				for ( var n = length; n < ret.length; n++ ) {
					for ( var r = 0; r < length; r++ ) {
						if ( ret[r] === ret[n] ) {
							ret.splice(n--, 1);
							break;
						}
					}
				}
			}
		}

		return ret;
	},

	has: function( target ) {
		var targets = jQuery( target );
		return this.filter(function() {
			for ( var i = 0, l = targets.length; i < l; i++ ) {
				if ( jQuery.contains( this, targets[i] ) ) {
					return true;
				}
			}
		});
	},

	not: function( selector ) {
		return this.pushStack( winnow(this, selector, false), "not", selector);
	},

	filter: function( selector ) {
		return this.pushStack( winnow(this, selector, true), "filter", selector );
	},

	is: function( selector ) {
		return !!selector && jQuery.filter( selector, this ).length > 0;
	},

	closest: function( selectors, context ) {
		var ret = [], i, l, cur = this[0];

		if ( jQuery.isArray( selectors ) ) {
			var match, selector,
				matches = {},
				level = 1;

			if ( cur && selectors.length ) {
				for ( i = 0, l = selectors.length; i < l; i++ ) {
					selector = selectors[i];

					if ( !matches[selector] ) {
						matches[selector] = jQuery.expr.match.POS.test( selector ) ?
							jQuery( selector, context || this.context ) :
							selector;
					}
				}

				while ( cur && cur.ownerDocument && cur !== context ) {
					for ( selector in matches ) {
						match = matches[selector];

						if ( match.jquery ? match.index(cur) > -1 : jQuery(cur).is(match) ) {
							ret.push({ selector: selector, elem: cur, level: level });
						}
					}

					cur = cur.parentNode;
					level++;
				}
			}

			return ret;
		}

		var pos = POS.test( selectors ) ?
			jQuery( selectors, context || this.context ) : null;

		for ( i = 0, l = this.length; i < l; i++ ) {
			cur = this[i];

			while ( cur ) {
				if ( pos ? pos.index(cur) > -1 : jQuery.find.matchesSelector(cur, selectors) ) {
					ret.push( cur );
					break;

				} else {
					cur = cur.parentNode;
					if ( !cur || !cur.ownerDocument || cur === context ) {
						break;
					}
				}
			}
		}

		ret = ret.length > 1 ? jQuery.unique(ret) : ret;

		return this.pushStack( ret, "closest", selectors );
	},

	// Determine the position of an element within
	// the matched set of elements
	index: function( elem ) {
		if ( !elem || typeof elem === "string" ) {
			return jQuery.inArray( this[0],
				// If it receives a string, the selector is used
				// If it receives nothing, the siblings are used
				elem ? jQuery( elem ) : this.parent().children() );
		}
		// Locate the position of the desired element
		return jQuery.inArray(
			// If it receives a jQuery object, the first element is used
			elem.jquery ? elem[0] : elem, this );
	},

	add: function( selector, context ) {
		var set = typeof selector === "string" ?
				jQuery( selector, context ) :
				jQuery.makeArray( selector ),
			all = jQuery.merge( this.get(), set );

		return this.pushStack( isDisconnected( set[0] ) || isDisconnected( all[0] ) ?
			all :
			jQuery.unique( all ) );
	},

	andSelf: function() {
		return this.add( this.prevObject );
	}
});

// A painfully simple check to see if an element is disconnected
// from a document (should be improved, where feasible).
function isDisconnected( node ) {
	return !node || !node.parentNode || node.parentNode.nodeType === 11;
}

jQuery.each({
	parent: function( elem ) {
		var parent = elem.parentNode;
		return parent && parent.nodeType !== 11 ? parent : null;
	},
	parents: function( elem ) {
		return jQuery.dir( elem, "parentNode" );
	},
	parentsUntil: function( elem, i, until ) {
		return jQuery.dir( elem, "parentNode", until );
	},
	next: function( elem ) {
		return jQuery.nth( elem, 2, "nextSibling" );
	},
	prev: function( elem ) {
		return jQuery.nth( elem, 2, "previousSibling" );
	},
	nextAll: function( elem ) {
		return jQuery.dir( elem, "nextSibling" );
	},
	prevAll: function( elem ) {
		return jQuery.dir( elem, "previousSibling" );
	},
	nextUntil: function( elem, i, until ) {
		return jQuery.dir( elem, "nextSibling", until );
	},
	prevUntil: function( elem, i, until ) {
		return jQuery.dir( elem, "previousSibling", until );
	},
	siblings: function( elem ) {
		return jQuery.sibling( elem.parentNode.firstChild, elem );
	},
	children: function( elem ) {
		return jQuery.sibling( elem.firstChild );
	},
	contents: function( elem ) {
		return jQuery.nodeName( elem, "iframe" ) ?
			elem.contentDocument || elem.contentWindow.document :
			jQuery.makeArray( elem.childNodes );
	}
}, function( name, fn ) {
	jQuery.fn[ name ] = function( until, selector ) {
		var ret = jQuery.map( this, fn, until ),
			// The variable 'args' was introduced in
			// https://github.com/jquery/jquery/commit/52a0238
			// to work around a bug in Chrome 10 (Dev) and should be removed when the bug is fixed.
			// http://code.google.com/p/v8/issues/detail?id=1050
			args = slice.call(arguments);

		if ( !runtil.test( name ) ) {
			selector = until;
		}

		if ( selector && typeof selector === "string" ) {
			ret = jQuery.filter( selector, ret );
		}

		ret = this.length > 1 && !guaranteedUnique[ name ] ? jQuery.unique( ret ) : ret;

		if ( (this.length > 1 || rmultiselector.test( selector )) && rparentsprev.test( name ) ) {
			ret = ret.reverse();
		}

		return this.pushStack( ret, name, args.join(",") );
	};
});

jQuery.extend({
	filter: function( expr, elems, not ) {
		if ( not ) {
			expr = ":not(" + expr + ")";
		}

		return elems.length === 1 ?
			jQuery.find.matchesSelector(elems[0], expr) ? [ elems[0] ] : [] :
			jQuery.find.matches(expr, elems);
	},

	dir: function( elem, dir, until ) {
		var matched = [],
			cur = elem[ dir ];

		while ( cur && cur.nodeType !== 9 && (until === undefined || cur.nodeType !== 1 || !jQuery( cur ).is( until )) ) {
			if ( cur.nodeType === 1 ) {
				matched.push( cur );
			}
			cur = cur[dir];
		}
		return matched;
	},

	nth: function( cur, result, dir, elem ) {
		result = result || 1;
		var num = 0;

		for ( ; cur; cur = cur[dir] ) {
			if ( cur.nodeType === 1 && ++num === result ) {
				break;
			}
		}

		return cur;
	},

	sibling: function( n, elem ) {
		var r = [];

		for ( ; n; n = n.nextSibling ) {
			if ( n.nodeType === 1 && n !== elem ) {
				r.push( n );
			}
		}

		return r;
	}
});

// Implement the identical functionality for filter and not
function winnow( elements, qualifier, keep ) {
	if ( jQuery.isFunction( qualifier ) ) {
		return jQuery.grep(elements, function( elem, i ) {
			var retVal = !!qualifier.call( elem, i, elem );
			return retVal === keep;
		});

	} else if ( qualifier.nodeType ) {
		return jQuery.grep(elements, function( elem, i ) {
			return (elem === qualifier) === keep;
		});

	} else if ( typeof qualifier === "string" ) {
		var filtered = jQuery.grep(elements, function( elem ) {
			return elem.nodeType === 1;
		});

		if ( isSimple.test( qualifier ) ) {
			return jQuery.filter(qualifier, filtered, !keep);
		} else {
			qualifier = jQuery.filter( qualifier, filtered );
		}
	}

	return jQuery.grep(elements, function( elem, i ) {
		return (jQuery.inArray( elem, qualifier ) >= 0) === keep;
	});
}




var rinlinejQuery = / jQuery\d+="(?:\d+|null)"/g,
	rleadingWhitespace = /^\s+/,
	rxhtmlTag = /<(?!area|br|col|embed|hr|img|input|link|meta|param)(([\w:]+)[^>]*)\/>/ig,
	rtagName = /<([\w:]+)/,
	rtbody = /<tbody/i,
	rhtml = /<|&#?\w+;/,
	rnocache = /<(?:script|object|embed|option|style)/i,
	// checked="checked" or checked
	rchecked = /checked\s*(?:[^=]|=\s*.checked.)/i,
	wrapMap = {
		option: [ 1, "<select multiple='multiple'>", "</select>" ],
		legend: [ 1, "<fieldset>", "</fieldset>" ],
		thead: [ 1, "<table>", "</table>" ],
		tr: [ 2, "<table><tbody>", "</tbody></table>" ],
		td: [ 3, "<table><tbody><tr>", "</tr></tbody></table>" ],
		col: [ 2, "<table><tbody></tbody><colgroup>", "</colgroup></table>" ],
		area: [ 1, "<map>", "</map>" ],
		_default: [ 0, "", "" ]
	};

wrapMap.optgroup = wrapMap.option;
wrapMap.tbody = wrapMap.tfoot = wrapMap.colgroup = wrapMap.caption = wrapMap.thead;
wrapMap.th = wrapMap.td;

// IE can't serialize <link> and <script> tags normally
if ( !jQuery.support.htmlSerialize ) {
	wrapMap._default = [ 1, "div<div>", "</div>" ];
}

jQuery.fn.extend({
	text: function( text ) {
		if ( jQuery.isFunction(text) ) {
			return this.each(function(i) {
				var self = jQuery( this );

				self.text( text.call(this, i, self.text()) );
			});
		}

		if ( typeof text !== "object" && text !== undefined ) {
			return this.empty().append( (this[0] && this[0].ownerDocument || document).createTextNode( text ) );
		}

		return jQuery.text( this );
	},

	wrapAll: function( html ) {
		if ( jQuery.isFunction( html ) ) {
			return this.each(function(i) {
				jQuery(this).wrapAll( html.call(this, i) );
			});
		}

		if ( this[0] ) {
			// The elements to wrap the target around
			var wrap = jQuery( html, this[0].ownerDocument ).eq(0).clone(true);

			if ( this[0].parentNode ) {
				wrap.insertBefore( this[0] );
			}

			wrap.map(function() {
				var elem = this;

				while ( elem.firstChild && elem.firstChild.nodeType === 1 ) {
					elem = elem.firstChild;
				}

				return elem;
			}).append(this);
		}

		return this;
	},

	wrapInner: function( html ) {
		if ( jQuery.isFunction( html ) ) {
			return this.each(function(i) {
				jQuery(this).wrapInner( html.call(this, i) );
			});
		}

		return this.each(function() {
			var self = jQuery( this ),
				contents = self.contents();

			if ( contents.length ) {
				contents.wrapAll( html );

			} else {
				self.append( html );
			}
		});
	},

	wrap: function( html ) {
		return this.each(function() {
			jQuery( this ).wrapAll( html );
		});
	},

	unwrap: function() {
		return this.parent().each(function() {
			if ( !jQuery.nodeName( this, "body" ) ) {
				jQuery( this ).replaceWith( this.childNodes );
			}
		}).end();
	},

	append: function() {
		return this.domManip(arguments, true, function( elem ) {
			if ( this.nodeType === 1 ) {
				this.appendChild( elem );
			}
		});
	},

	prepend: function() {
		return this.domManip(arguments, true, function( elem ) {
			if ( this.nodeType === 1 ) {
				this.insertBefore( elem, this.firstChild );
			}
		});
	},

	before: function() {
		if ( this[0] && this[0].parentNode ) {
			return this.domManip(arguments, false, function( elem ) {
				this.parentNode.insertBefore( elem, this );
			});
		} else if ( arguments.length ) {
			var set = jQuery(arguments[0]);
			set.push.apply( set, this.toArray() );
			return this.pushStack( set, "before", arguments );
		}
	},

	after: function() {
		if ( this[0] && this[0].parentNode ) {
			return this.domManip(arguments, false, function( elem ) {
				this.parentNode.insertBefore( elem, this.nextSibling );
			});
		} else if ( arguments.length ) {
			var set = this.pushStack( this, "after", arguments );
			set.push.apply( set, jQuery(arguments[0]).toArray() );
			return set;
		}
	},

	// keepData is for internal use only--do not document
	remove: function( selector, keepData ) {
		for ( var i = 0, elem; (elem = this[i]) != null; i++ ) {
			if ( !selector || jQuery.filter( selector, [ elem ] ).length ) {
				if ( !keepData && elem.nodeType === 1 ) {
					jQuery.cleanData( elem.getElementsByTagName("*") );
					jQuery.cleanData( [ elem ] );
				}

				if ( elem.parentNode ) {
					elem.parentNode.removeChild( elem );
				}
			}
		}

		return this;
	},

	empty: function() {
		for ( var i = 0, elem; (elem = this[i]) != null; i++ ) {
			// Remove element nodes and prevent memory leaks
			if ( elem.nodeType === 1 ) {
				jQuery.cleanData( elem.getElementsByTagName("*") );
			}

			// Remove any remaining nodes
			while ( elem.firstChild ) {
				elem.removeChild( elem.firstChild );
			}
		}

		return this;
	},

	clone: function( dataAndEvents, deepDataAndEvents ) {
		dataAndEvents = dataAndEvents == null ? false : dataAndEvents;
		deepDataAndEvents = deepDataAndEvents == null ? dataAndEvents : deepDataAndEvents;

		return this.map( function () {
			return jQuery.clone( this, dataAndEvents, deepDataAndEvents );
		});
	},

	html: function( value ) {
		if ( value === undefined ) {
			return this[0] && this[0].nodeType === 1 ?
				this[0].innerHTML.replace(rinlinejQuery, "") :
				null;

		// See if we can take a shortcut and just use innerHTML
		} else if ( typeof value === "string" && !rnocache.test( value ) &&
			(jQuery.support.leadingWhitespace || !rleadingWhitespace.test( value )) &&
			!wrapMap[ (rtagName.exec( value ) || ["", ""])[1].toLowerCase() ] ) {

			value = value.replace(rxhtmlTag, "<$1></$2>");

			try {
				for ( var i = 0, l = this.length; i < l; i++ ) {
					// Remove element nodes and prevent memory leaks
					if ( this[i].nodeType === 1 ) {
						jQuery.cleanData( this[i].getElementsByTagName("*") );
						this[i].innerHTML = value;
					}
				}

			// If using innerHTML throws an exception, use the fallback method
			} catch(e) {
				this.empty().append( value );
			}

		} else if ( jQuery.isFunction( value ) ) {
			this.each(function(i){
				var self = jQuery( this );

				self.html( value.call(this, i, self.html()) );
			});

		} else {
			this.empty().append( value );
		}

		return this;
	},

	replaceWith: function( value ) {
		if ( this[0] && this[0].parentNode ) {
			// Make sure that the elements are removed from the DOM before they are inserted
			// this can help fix replacing a parent with child elements
			if ( jQuery.isFunction( value ) ) {
				return this.each(function(i) {
					var self = jQuery(this), old = self.html();
					self.replaceWith( value.call( this, i, old ) );
				});
			}

			if ( typeof value !== "string" ) {
				value = jQuery( value ).detach();
			}

			return this.each(function() {
				var next = this.nextSibling,
					parent = this.parentNode;

				jQuery( this ).remove();

				if ( next ) {
					jQuery(next).before( value );
				} else {
					jQuery(parent).append( value );
				}
			});
		} else {
			return this.pushStack( jQuery(jQuery.isFunction(value) ? value() : value), "replaceWith", value );
		}
	},

	detach: function( selector ) {
		return this.remove( selector, true );
	},

	domManip: function( args, table, callback ) {
		var results, first, fragment, parent,
			value = args[0],
			scripts = [];

		// We can't cloneNode fragments that contain checked, in WebKit
		if ( !jQuery.support.checkClone && arguments.length === 3 && typeof value === "string" && rchecked.test( value ) ) {
			return this.each(function() {
				jQuery(this).domManip( args, table, callback, true );
			});
		}

		if ( jQuery.isFunction(value) ) {
			return this.each(function(i) {
				var self = jQuery(this);
				args[0] = value.call(this, i, table ? self.html() : undefined);
				self.domManip( args, table, callback );
			});
		}

		if ( this[0] ) {
			parent = value && value.parentNode;

			// If we're in a fragment, just use that instead of building a new one
			if ( jQuery.support.parentNode && parent && parent.nodeType === 11 && parent.childNodes.length === this.length ) {
				results = { fragment: parent };

			} else {
				results = jQuery.buildFragment( args, this, scripts );
			}

			fragment = results.fragment;

			if ( fragment.childNodes.length === 1 ) {
				first = fragment = fragment.firstChild;
			} else {
				first = fragment.firstChild;
			}

			if ( first ) {
				table = table && jQuery.nodeName( first, "tr" );

				for ( var i = 0, l = this.length, lastIndex = l - 1; i < l; i++ ) {
					callback.call(
						table ?
							root(this[i], first) :
							this[i],
						// Make sure that we do not leak memory by inadvertently discarding
						// the original fragment (which might have attached data) instead of
						// using it; in addition, use the original fragment object for the last
						// item instead of first because it can end up being emptied incorrectly
						// in certain situations (Bug #8070).
						// Fragments from the fragment cache must always be cloned and never used
						// in place.
						results.cacheable || (l > 1 && i < lastIndex) ?
							jQuery.clone( fragment, true, true ) :
							fragment
					);
				}
			}

			if ( scripts.length ) {
				jQuery.each( scripts, evalScript );
			}
		}

		return this;
	}
});

function root( elem, cur ) {
	return jQuery.nodeName(elem, "table") ?
		(elem.getElementsByTagName("tbody")[0] ||
		elem.appendChild(elem.ownerDocument.createElement("tbody"))) :
		elem;
}

function cloneCopyEvent( src, dest ) {

	if ( dest.nodeType !== 1 || !jQuery.hasData( src ) ) {
		return;
	}

	var internalKey = jQuery.expando,
		oldData = jQuery.data( src ),
		curData = jQuery.data( dest, oldData );

	// Switch to use the internal data object, if it exists, for the next
	// stage of data copying
	if ( (oldData = oldData[ internalKey ]) ) {
		var events = oldData.events;
				curData = curData[ internalKey ] = jQuery.extend({}, oldData);

		if ( events ) {
			delete curData.handle;
			curData.events = {};

			for ( var type in events ) {
				for ( var i = 0, l = events[ type ].length; i < l; i++ ) {
					jQuery.event.add( dest, type + ( events[ type ][ i ].namespace ? "." : "" ) + events[ type ][ i ].namespace, events[ type ][ i ], events[ type ][ i ].data );
				}
			}
		}
	}
}

function cloneFixAttributes(src, dest) {
	// We do not need to do anything for non-Elements
	if ( dest.nodeType !== 1 ) {
		return;
	}

	var nodeName = dest.nodeName.toLowerCase();

	// clearAttributes removes the attributes, which we don't want,
	// but also removes the attachEvent events, which we *do* want
	dest.clearAttributes();

	// mergeAttributes, in contrast, only merges back on the
	// original attributes, not the events
	dest.mergeAttributes(src);

	// IE6-8 fail to clone children inside object elements that use
	// the proprietary classid attribute value (rather than the type
	// attribute) to identify the type of content to display
	if ( nodeName === "object" ) {
		dest.outerHTML = src.outerHTML;

	} else if ( nodeName === "input" && (src.type === "checkbox" || src.type === "radio") ) {
		// IE6-8 fails to persist the checked state of a cloned checkbox
		// or radio button. Worse, IE6-7 fail to give the cloned element
		// a checked appearance if the defaultChecked value isn't also set
		if ( src.checked ) {
			dest.defaultChecked = dest.checked = src.checked;
		}

		// IE6-7 get confused and end up setting the value of a cloned
		// checkbox/radio button to an empty string instead of "on"
		if ( dest.value !== src.value ) {
			dest.value = src.value;
		}

	// IE6-8 fails to return the selected option to the default selected
	// state when cloning options
	} else if ( nodeName === "option" ) {
		dest.selected = src.defaultSelected;

	// IE6-8 fails to set the defaultValue to the correct value when
	// cloning other types of input fields
	} else if ( nodeName === "input" || nodeName === "textarea" ) {
		dest.defaultValue = src.defaultValue;
	}

	// Event data gets referenced instead of copied if the expando
	// gets copied too
	dest.removeAttribute( jQuery.expando );
}

jQuery.buildFragment = function( args, nodes, scripts ) {
	var fragment, cacheable, cacheresults,
		doc = (nodes && nodes[0] ? nodes[0].ownerDocument || nodes[0] : document);

	// Only cache "small" (1/2 KB) HTML strings that are associated with the main document
	// Cloning options loses the selected state, so don't cache them
	// IE 6 doesn't like it when you put <object> or <embed> elements in a fragment
	// Also, WebKit does not clone 'checked' attributes on cloneNode, so don't cache
	if ( args.length === 1 && typeof args[0] === "string" && args[0].length < 512 && doc === document &&
		args[0].charAt(0) === "<" && !rnocache.test( args[0] ) && (jQuery.support.checkClone || !rchecked.test( args[0] )) ) {

		cacheable = true;
		cacheresults = jQuery.fragments[ args[0] ];
		if ( cacheresults ) {
			if ( cacheresults !== 1 ) {
				fragment = cacheresults;
			}
		}
	}

	if ( !fragment ) {
		fragment = doc.createDocumentFragment();
		jQuery.clean( args, doc, fragment, scripts );
	}

	if ( cacheable ) {
		jQuery.fragments[ args[0] ] = cacheresults ? fragment : 1;
	}

	return { fragment: fragment, cacheable: cacheable };
};

jQuery.fragments = {};

jQuery.each({
	appendTo: "append",
	prependTo: "prepend",
	insertBefore: "before",
	insertAfter: "after",
	replaceAll: "replaceWith"
}, function( name, original ) {
	jQuery.fn[ name ] = function( selector ) {
		var ret = [],
			insert = jQuery( selector ),
			parent = this.length === 1 && this[0].parentNode;

		if ( parent && parent.nodeType === 11 && parent.childNodes.length === 1 && insert.length === 1 ) {
			insert[ original ]( this[0] );
			return this;

		} else {
			for ( var i = 0, l = insert.length; i < l; i++ ) {
				var elems = (i > 0 ? this.clone(true) : this).get();
				jQuery( insert[i] )[ original ]( elems );
				ret = ret.concat( elems );
			}

			return this.pushStack( ret, name, insert.selector );
		}
	};
});

function getAll( elem ) {
	if ( "getElementsByTagName" in elem ) {
		return elem.getElementsByTagName( "*" );
	
	} else if ( "querySelectorAll" in elem ) {
		return elem.querySelectorAll( "*" );

	} else {
		return [];
	}
}

jQuery.extend({
	clone: function( elem, dataAndEvents, deepDataAndEvents ) {
		var clone = elem.cloneNode(true),
				srcElements,
				destElements,
				i;

		if ( (!jQuery.support.noCloneEvent || !jQuery.support.noCloneChecked) &&
				(elem.nodeType === 1 || elem.nodeType === 11) && !jQuery.isXMLDoc(elem) ) {
			// IE copies events bound via attachEvent when using cloneNode.
			// Calling detachEvent on the clone will also remove the events
			// from the original. In order to get around this, we use some
			// proprietary methods to clear the events. Thanks to MooTools
			// guys for this hotness.

			cloneFixAttributes( elem, clone );

			// Using Sizzle here is crazy slow, so we use getElementsByTagName
			// instead
			srcElements = getAll( elem );
			destElements = getAll( clone );

			// Weird iteration because IE will replace the length property
			// with an element if you are cloning the body and one of the
			// elements on the page has a name or id of "length"
			for ( i = 0; srcElements[i]; ++i ) {
				cloneFixAttributes( srcElements[i], destElements[i] );
			}
		}

		// Copy the events from the original to the clone
		if ( dataAndEvents ) {
			cloneCopyEvent( elem, clone );

			if ( deepDataAndEvents ) {
				srcElements = getAll( elem );
				destElements = getAll( clone );

				for ( i = 0; srcElements[i]; ++i ) {
					cloneCopyEvent( srcElements[i], destElements[i] );
				}
			}
		}

		// Return the cloned set
		return clone;
},
	clean: function( elems, context, fragment, scripts ) {
		context = context || document;

		// !context.createElement fails in IE with an error but returns typeof 'object'
		if ( typeof context.createElement === "undefined" ) {
			context = context.ownerDocument || context[0] && context[0].ownerDocument || document;
		}

		var ret = [];

		for ( var i = 0, elem; (elem = elems[i]) != null; i++ ) {
			if ( typeof elem === "number" ) {
				elem += "";
			}

			if ( !elem ) {
				continue;
			}

			// Convert html string into DOM nodes
			if ( typeof elem === "string" && !rhtml.test( elem ) ) {
				elem = context.createTextNode( elem );

			} else if ( typeof elem === "string" ) {
				// Fix "XHTML"-style tags in all browsers
				elem = elem.replace(rxhtmlTag, "<$1></$2>");

				// Trim whitespace, otherwise indexOf won't work as expected
				var tag = (rtagName.exec( elem ) || ["", ""])[1].toLowerCase(),
					wrap = wrapMap[ tag ] || wrapMap._default,
					depth = wrap[0],
					div = context.createElement("div");

				// Go to html and back, then peel off extra wrappers
				div.innerHTML = wrap[1] + elem + wrap[2];

				// Move to the right depth
				while ( depth-- ) {
					div = div.lastChild;
				}

				// Remove IE's autoinserted <tbody> from table fragments
				if ( !jQuery.support.tbody ) {

					// String was a <table>, *may* have spurious <tbody>
					var hasBody = rtbody.test(elem),
						tbody = tag === "table" && !hasBody ?
							div.firstChild && div.firstChild.childNodes :

							// String was a bare <thead> or <tfoot>
							wrap[1] === "<table>" && !hasBody ?
								div.childNodes :
								[];

					for ( var j = tbody.length - 1; j >= 0 ; --j ) {
						if ( jQuery.nodeName( tbody[ j ], "tbody" ) && !tbody[ j ].childNodes.length ) {
							tbody[ j ].parentNode.removeChild( tbody[ j ] );
						}
					}

				}

				// IE completely kills leading whitespace when innerHTML is used
				if ( !jQuery.support.leadingWhitespace && rleadingWhitespace.test( elem ) ) {
					div.insertBefore( context.createTextNode( rleadingWhitespace.exec(elem)[0] ), div.firstChild );
				}

				elem = div.childNodes;
			}

			if ( elem.nodeType ) {
				ret.push( elem );
			} else {
				ret = jQuery.merge( ret, elem );
			}
		}

		if ( fragment ) {
			for ( i = 0; ret[i]; i++ ) {
				if ( scripts && jQuery.nodeName( ret[i], "script" ) && (!ret[i].type || ret[i].type.toLowerCase() === "text/javascript") ) {
					scripts.push( ret[i].parentNode ? ret[i].parentNode.removeChild( ret[i] ) : ret[i] );

				} else {
					if ( ret[i].nodeType === 1 ) {
						ret.splice.apply( ret, [i + 1, 0].concat(jQuery.makeArray(ret[i].getElementsByTagName("script"))) );
					}
					fragment.appendChild( ret[i] );
				}
			}
		}

		return ret;
	},

	cleanData: function( elems ) {
		var data, id, cache = jQuery.cache, internalKey = jQuery.expando, special = jQuery.event.special,
			deleteExpando = jQuery.support.deleteExpando;

		for ( var i = 0, elem; (elem = elems[i]) != null; i++ ) {
			if ( elem.nodeName && jQuery.noData[elem.nodeName.toLowerCase()] ) {
				continue;
			}

			id = elem[ jQuery.expando ];

			if ( id ) {
				data = cache[ id ] && cache[ id ][ internalKey ];

				if ( data && data.events ) {
					for ( var type in data.events ) {
						if ( special[ type ] ) {
							jQuery.event.remove( elem, type );

						// This is a shortcut to avoid jQuery.event.remove's overhead
						} else {
							jQuery.removeEvent( elem, type, data.handle );
						}
					}

					// Null the DOM reference to avoid IE6/7/8 leak (#7054)
					if ( data.handle ) {
						data.handle.elem = null;
					}
				}

				if ( deleteExpando ) {
					delete elem[ jQuery.expando ];

				} else if ( elem.removeAttribute ) {
					elem.removeAttribute( jQuery.expando );
				}

				delete cache[ id ];
			}
		}
	}
});

function evalScript( i, elem ) {
	if ( elem.src ) {
		jQuery.ajax({
			url: elem.src,
			async: false,
			dataType: "script"
		});
	} else {
		jQuery.globalEval( elem.text || elem.textContent || elem.innerHTML || "" );
	}

	if ( elem.parentNode ) {
		elem.parentNode.removeChild( elem );
	}
}




var ralpha = /alpha\([^)]*\)/i,
	ropacity = /opacity=([^)]*)/,
	rdashAlpha = /-([a-z])/ig,
	rupper = /([A-Z])/g,
	rnumpx = /^-?\d+(?:px)?$/i,
	rnum = /^-?\d/,

	cssShow = { position: "absolute", visibility: "hidden", display: "block" },
	cssWidth = [ "Left", "Right" ],
	cssHeight = [ "Top", "Bottom" ],
	curCSS,

	getComputedStyle,
	currentStyle,

	fcamelCase = function( all, letter ) {
		return letter.toUpperCase();
	};

jQuery.fn.css = function( name, value ) {
	// Setting 'undefined' is a no-op
	if ( arguments.length === 2 && value === undefined ) {
		return this;
	}

	return jQuery.access( this, name, value, true, function( elem, name, value ) {
		return value !== undefined ?
			jQuery.style( elem, name, value ) :
			jQuery.css( elem, name );
	});
};

jQuery.extend({
	// Add in style property hooks for overriding the default
	// behavior of getting and setting a style property
	cssHooks: {
		opacity: {
			get: function( elem, computed ) {
				if ( computed ) {
					// We should always get a number back from opacity
					var ret = curCSS( elem, "opacity", "opacity" );
					return ret === "" ? "1" : ret;

				} else {
					return elem.style.opacity;
				}
			}
		}
	},

	// Exclude the following css properties to add px
	cssNumber: {
		"zIndex": true,
		"fontWeight": true,
		"opacity": true,
		"zoom": true,
		"lineHeight": true
	},

	// Add in properties whose names you wish to fix before
	// setting or getting the value
	cssProps: {
		// normalize float css property
		"float": jQuery.support.cssFloat ? "cssFloat" : "styleFloat"
	},

	// Get and set the style property on a DOM Node
	style: function( elem, name, value, extra ) {
		// Don't set styles on text and comment nodes
		if ( !elem || elem.nodeType === 3 || elem.nodeType === 8 || !elem.style ) {
			return;
		}

		// Make sure that we're working with the right name
		var ret, origName = jQuery.camelCase( name ),
			style = elem.style, hooks = jQuery.cssHooks[ origName ];

		name = jQuery.cssProps[ origName ] || origName;

		// Check if we're setting a value
		if ( value !== undefined ) {
			// Make sure that NaN and null values aren't set. See: #7116
			if ( typeof value === "number" && isNaN( value ) || value == null ) {
				return;
			}

			// If a number was passed in, add 'px' to the (except for certain CSS properties)
			if ( typeof value === "number" && !jQuery.cssNumber[ origName ] ) {
				value += "px";
			}

			// If a hook was provided, use that value, otherwise just set the specified value
			if ( !hooks || !("set" in hooks) || (value = hooks.set( elem, value )) !== undefined ) {
				// Wrapped to prevent IE from throwing errors when 'invalid' values are provided
				// Fixes bug #5509
				try {
					style[ name ] = value;
				} catch(e) {}
			}

		} else {
			// If a hook was provided get the non-computed value from there
			if ( hooks && "get" in hooks && (ret = hooks.get( elem, false, extra )) !== undefined ) {
				return ret;
			}

			// Otherwise just get the value from the style object
			return style[ name ];
		}
	},

	css: function( elem, name, extra ) {
		// Make sure that we're working with the right name
		var ret, origName = jQuery.camelCase( name ),
			hooks = jQuery.cssHooks[ origName ];

		name = jQuery.cssProps[ origName ] || origName;

		// If a hook was provided get the computed value from there
		if ( hooks && "get" in hooks && (ret = hooks.get( elem, true, extra )) !== undefined ) {
			return ret;

		// Otherwise, if a way to get the computed value exists, use that
		} else if ( curCSS ) {
			return curCSS( elem, name, origName );
		}
	},

	// A method for quickly swapping in/out CSS properties to get correct calculations
	swap: function( elem, options, callback ) {
		var old = {};

		// Remember the old values, and insert the new ones
		for ( var name in options ) {
			old[ name ] = elem.style[ name ];
			elem.style[ name ] = options[ name ];
		}

		callback.call( elem );

		// Revert the old values
		for ( name in options ) {
			elem.style[ name ] = old[ name ];
		}
	},

	camelCase: function( string ) {
		return string.replace( rdashAlpha, fcamelCase );
	}
});

// DEPRECATED, Use jQuery.css() instead
jQuery.curCSS = jQuery.css;

jQuery.each(["height", "width"], function( i, name ) {
	jQuery.cssHooks[ name ] = {
		get: function( elem, computed, extra ) {
			var val;

			if ( computed ) {
				if ( elem.offsetWidth !== 0 ) {
					val = getWH( elem, name, extra );

				} else {
					jQuery.swap( elem, cssShow, function() {
						val = getWH( elem, name, extra );
					});
				}

				if ( val <= 0 ) {
					val = curCSS( elem, name, name );

					if ( val === "0px" && currentStyle ) {
						val = currentStyle( elem, name, name );
					}

					if ( val != null ) {
						// Should return "auto" instead of 0, use 0 for
						// temporary backwards-compat
						return val === "" || val === "auto" ? "0px" : val;
					}
				}

				if ( val < 0 || val == null ) {
					val = elem.style[ name ];

					// Should return "auto" instead of 0, use 0 for
					// temporary backwards-compat
					return val === "" || val === "auto" ? "0px" : val;
				}

				return typeof val === "string" ? val : val + "px";
			}
		},

		set: function( elem, value ) {
			if ( rnumpx.test( value ) ) {
				// ignore negative width and height values #1599
				value = parseFloat(value);

				if ( value >= 0 ) {
					return value + "px";
				}

			} else {
				return value;
			}
		}
	};
});

if ( !jQuery.support.opacity ) {
	jQuery.cssHooks.opacity = {
		get: function( elem, computed ) {
			// IE uses filters for opacity
			return ropacity.test((computed && elem.currentStyle ? elem.currentStyle.filter : elem.style.filter) || "") ?
				(parseFloat(RegExp.$1) / 100) + "" :
				computed ? "1" : "";
		},

		set: function( elem, value ) {
			var style = elem.style;

			// IE has trouble with opacity if it does not have layout
			// Force it by setting the zoom level
			style.zoom = 1;

			// Set the alpha filter to set the opacity
			var opacity = jQuery.isNaN(value) ?
				"" :
				"alpha(opacity=" + value * 100 + ")",
				filter = style.filter || "";

			style.filter = ralpha.test(filter) ?
				filter.replace(ralpha, opacity) :
				style.filter + ' ' + opacity;
		}
	};
}

if ( document.defaultView && document.defaultView.getComputedStyle ) {
	getComputedStyle = function( elem, newName, name ) {
		var ret, defaultView, computedStyle;

		name = name.replace( rupper, "-$1" ).toLowerCase();

		if ( !(defaultView = elem.ownerDocument.defaultView) ) {
			return undefined;
		}

		if ( (computedStyle = defaultView.getComputedStyle( elem, null )) ) {
			ret = computedStyle.getPropertyValue( name );
			if ( ret === "" && !jQuery.contains( elem.ownerDocument.documentElement, elem ) ) {
				ret = jQuery.style( elem, name );
			}
		}

		return ret;
	};
}

if ( document.documentElement.currentStyle ) {
	currentStyle = function( elem, name ) {
		var left,
			ret = elem.currentStyle && elem.currentStyle[ name ],
			rsLeft = elem.runtimeStyle && elem.runtimeStyle[ name ],
			style = elem.style;

		// From the awesome hack by Dean Edwards
		// http://erik.eae.net/archives/2007/07/27/18.54.15/#comment-102291

		// If we're not dealing with a regular pixel number
		// but a number that has a weird ending, we need to convert it to pixels
		if ( !rnumpx.test( ret ) && rnum.test( ret ) ) {
			// Remember the original values
			left = style.left;

			// Put in the new values to get a computed value out
			if ( rsLeft ) {
				elem.runtimeStyle.left = elem.currentStyle.left;
			}
			style.left = name === "fontSize" ? "1em" : (ret || 0);
			ret = style.pixelLeft + "px";

			// Revert the changed values
			style.left = left;
			if ( rsLeft ) {
				elem.runtimeStyle.left = rsLeft;
			}
		}

		return ret === "" ? "auto" : ret;
	};
}

curCSS = getComputedStyle || currentStyle;

function getWH( elem, name, extra ) {
	var which = name === "width" ? cssWidth : cssHeight,
		val = name === "width" ? elem.offsetWidth : elem.offsetHeight;

	if ( extra === "border" ) {
		return val;
	}

	jQuery.each( which, function() {
		if ( !extra ) {
			val -= parseFloat(jQuery.css( elem, "padding" + this )) || 0;
		}

		if ( extra === "margin" ) {
			val += parseFloat(jQuery.css( elem, "margin" + this )) || 0;

		} else {
			val -= parseFloat(jQuery.css( elem, "border" + this + "Width" )) || 0;
		}
	});

	return val;
}

if ( jQuery.expr && jQuery.expr.filters ) {
	jQuery.expr.filters.hidden = function( elem ) {
		var width = elem.offsetWidth,
			height = elem.offsetHeight;

		return (width === 0 && height === 0) || (!jQuery.support.reliableHiddenOffsets && (elem.style.display || jQuery.css( elem, "display" )) === "none");
	};

	jQuery.expr.filters.visible = function( elem ) {
		return !jQuery.expr.filters.hidden( elem );
	};
}




var r20 = /%20/g,
	rbracket = /\[\]$/,
	rCRLF = /\r?\n/g,
	rhash = /#.*$/,
	rheaders = /^(.*?):[ \t]*([^\r\n]*)\r?$/mg, // IE leaves an \r character at EOL
	rinput = /^(?:color|date|datetime|email|hidden|month|number|password|range|search|tel|text|time|url|week)$/i,
	// #7653, #8125, #8152: local protocol detection
	rlocalProtocol = /(?:^file|^widget|\-extension):$/,
	rnoContent = /^(?:GET|HEAD)$/,
	rprotocol = /^\/\//,
	rquery = /\?/,
	rscript = /<script\b[^<]*(?:(?!<\/script>)<[^<]*)*<\/script>/gi,
	rselectTextarea = /^(?:select|textarea)/i,
	rspacesAjax = /\s+/,
	rts = /([?&])_=[^&]*/,
	rucHeaders = /(^|\-)([a-z])/g,
	rucHeadersFunc = function( _, $1, $2 ) {
		return $1 + $2.toUpperCase();
	},
	rurl = /^([\w\+\.\-]+:)\/\/([^\/?#:]*)(?::(\d+))?/,

	// Keep a copy of the old load method
	_load = jQuery.fn.load,

	/* Prefilters
	 * 1) They are useful to introduce custom dataTypes (see ajax/jsonp.js for an example)
	 * 2) These are called:
	 *    - BEFORE asking for a transport
	 *    - AFTER param serialization (s.data is a string if s.processData is true)
	 * 3) key is the dataType
	 * 4) the catchall symbol "*" can be used
	 * 5) execution will start with transport dataType and THEN continue down to "*" if needed
	 */
	prefilters = {},

	/* Transports bindings
	 * 1) key is the dataType
	 * 2) the catchall symbol "*" can be used
	 * 3) selection will start with transport dataType and THEN go to "*" if needed
	 */
	transports = {},

	// Document location
	ajaxLocation,

	// Document location segments
	ajaxLocParts;

// #8138, IE may throw an exception when accessing
// a field from document.location if document.domain has been set
try {
	ajaxLocation = document.location.href;
} catch( e ) {
	// Use the href attribute of an A element
	// since IE will modify it given document.location
	ajaxLocation = document.createElement( "a" );
	ajaxLocation.href = "";
	ajaxLocation = ajaxLocation.href;
}

// Segment location into parts
ajaxLocParts = rurl.exec( ajaxLocation.toLowerCase() );

// Base "constructor" for jQuery.ajaxPrefilter and jQuery.ajaxTransport
function addToPrefiltersOrTransports( structure ) {

	// dataTypeExpression is optional and defaults to "*"
	return function( dataTypeExpression, func ) {

		if ( typeof dataTypeExpression !== "string" ) {
			func = dataTypeExpression;
			dataTypeExpression = "*";
		}

		if ( jQuery.isFunction( func ) ) {
			var dataTypes = dataTypeExpression.toLowerCase().split( rspacesAjax ),
				i = 0,
				length = dataTypes.length,
				dataType,
				list,
				placeBefore;

			// For each dataType in the dataTypeExpression
			for(; i < length; i++ ) {
				dataType = dataTypes[ i ];
				// We control if we're asked to add before
				// any existing element
				placeBefore = /^\+/.test( dataType );
				if ( placeBefore ) {
					dataType = dataType.substr( 1 ) || "*";
				}
				list = structure[ dataType ] = structure[ dataType ] || [];
				// then we add to the structure accordingly
				list[ placeBefore ? "unshift" : "push" ]( func );
			}
		}
	};
}

//Base inspection function for prefilters and transports
function inspectPrefiltersOrTransports( structure, options, originalOptions, jqXHR,
		dataType /* internal */, inspected /* internal */ ) {

	dataType = dataType || options.dataTypes[ 0 ];
	inspected = inspected || {};

	inspected[ dataType ] = true;

	var list = structure[ dataType ],
		i = 0,
		length = list ? list.length : 0,
		executeOnly = ( structure === prefilters ),
		selection;

	for(; i < length && ( executeOnly || !selection ); i++ ) {
		selection = list[ i ]( options, originalOptions, jqXHR );
		// If we got redirected to another dataType
		// we try there if executing only and not done already
		if ( typeof selection === "string" ) {
			if ( !executeOnly || inspected[ selection ] ) {
				selection = undefined;
			} else {
				options.dataTypes.unshift( selection );
				selection = inspectPrefiltersOrTransports(
						structure, options, originalOptions, jqXHR, selection, inspected );
			}
		}
	}
	// If we're only executing or nothing was selected
	// we try the catchall dataType if not done already
	if ( ( executeOnly || !selection ) && !inspected[ "*" ] ) {
		selection = inspectPrefiltersOrTransports(
				structure, options, originalOptions, jqXHR, "*", inspected );
	}
	// unnecessary when only executing (prefilters)
	// but it'll be ignored by the caller in that case
	return selection;
}

jQuery.fn.extend({
	load: function( url, params, callback ) {
		if ( typeof url !== "string" && _load ) {
			return _load.apply( this, arguments );

		// Don't do a request if no elements are being requested
		} else if ( !this.length ) {
			return this;
		}

		var off = url.indexOf( " " );
		if ( off >= 0 ) {
			var selector = url.slice( off, url.length );
			url = url.slice( 0, off );
		}

		// Default to a GET request
		var type = "GET";

		// If the second parameter was provided
		if ( params ) {
			// If it's a function
			if ( jQuery.isFunction( params ) ) {
				// We assume that it's the callback
				callback = params;
				params = undefined;

			// Otherwise, build a param string
			} else if ( typeof params === "object" ) {
				params = jQuery.param( params, jQuery.ajaxSettings.traditional );
				type = "POST";
			}
		}

		var self = this;

		// Request the remote document
		jQuery.ajax({
			url: url,
			type: type,
			dataType: "html",
			data: params,
			// Complete callback (responseText is used internally)
			complete: function( jqXHR, status, responseText ) {
				// Store the response as specified by the jqXHR object
				responseText = jqXHR.responseText;
				// If successful, inject the HTML into all the matched elements
				if ( jqXHR.isResolved() ) {
					// #4825: Get the actual response in case
					// a dataFilter is present in ajaxSettings
					jqXHR.done(function( r ) {
						responseText = r;
					});
					// See if a selector was specified
					self.html( selector ?
						// Create a dummy div to hold the results
						jQuery("<div>")
							// inject the contents of the document in, removing the scripts
							// to avoid any 'Permission Denied' errors in IE
							.append(responseText.replace(rscript, ""))

							// Locate the specified elements
							.find(selector) :

						// If not, just inject the full result
						responseText );
				}

				if ( callback ) {
					self.each( callback, [ responseText, status, jqXHR ] );
				}
			}
		});

		return this;
	},

	serialize: function() {
		return jQuery.param( this.serializeArray() );
	},

	serializeArray: function() {
		return this.map(function(){
			return this.elements ? jQuery.makeArray( this.elements ) : this;
		})
		.filter(function(){
			return this.name && !this.disabled &&
				( this.checked || rselectTextarea.test( this.nodeName ) ||
					rinput.test( this.type ) );
		})
		.map(function( i, elem ){
			var val = jQuery( this ).val();

			return val == null ?
				null :
				jQuery.isArray( val ) ?
					jQuery.map( val, function( val, i ){
						return { name: elem.name, value: val.replace( rCRLF, "\r\n" ) };
					}) :
					{ name: elem.name, value: val.replace( rCRLF, "\r\n" ) };
		}).get();
	}
});

// Attach a bunch of functions for handling common AJAX events
jQuery.each( "ajaxStart ajaxStop ajaxComplete ajaxError ajaxSuccess ajaxSend".split( " " ), function( i, o ){
	jQuery.fn[ o ] = function( f ){
		return this.bind( o, f );
	};
} );

jQuery.each( [ "get", "post" ], function( i, method ) {
	jQuery[ method ] = function( url, data, callback, type ) {
		// shift arguments if data argument was omitted
		if ( jQuery.isFunction( data ) ) {
			type = type || callback;
			callback = data;
			data = undefined;
		}

		return jQuery.ajax({
			type: method,
			url: url,
			data: data,
			success: callback,
			dataType: type
		});
	};
} );

jQuery.extend({

	getScript: function( url, callback ) {
		return jQuery.get( url, undefined, callback, "script" );
	},

	getJSON: function( url, data, callback ) {
		return jQuery.get( url, data, callback, "json" );
	},

	// Creates a full fledged settings object into target
	// with both ajaxSettings and settings fields.
	// If target is omitted, writes into ajaxSettings.
	ajaxSetup: function ( target, settings ) {
		if ( !settings ) {
			// Only one parameter, we extend ajaxSettings
			settings = target;
			target = jQuery.extend( true, jQuery.ajaxSettings, settings );
		} else {
			// target was provided, we extend into it
			jQuery.extend( true, target, jQuery.ajaxSettings, settings );
		}
		// Flatten fields we don't want deep extended
		for( var field in { context: 1, url: 1 } ) {
			if ( field in settings ) {
				target[ field ] = settings[ field ];
			} else if( field in jQuery.ajaxSettings ) {
				target[ field ] = jQuery.ajaxSettings[ field ];
			}
		}
		return target;
	},

	ajaxSettings: {
		url: ajaxLocation,
		isLocal: rlocalProtocol.test( ajaxLocParts[ 1 ] ),
		global: true,
		type: "GET",
		contentType: "application/x-www-form-urlencoded",
		processData: true,
		async: true,
		/*
		timeout: 0,
		data: null,
		dataType: null,
		username: null,
		password: null,
		cache: null,
		traditional: false,
		headers: {},
		crossDomain: null,
		*/

		accepts: {
			xml: "application/xml, text/xml",
			html: "text/html",
			text: "text/plain",
			json: "application/json, text/javascript",
			"*": "*/*"
		},

		contents: {
			xml: /xml/,
			html: /html/,
			json: /json/
		},

		responseFields: {
			xml: "responseXML",
			text: "responseText"
		},

		// List of data converters
		// 1) key format is "source_type destination_type" (a single space in-between)
		// 2) the catchall symbol "*" can be used for source_type
		converters: {

			// Convert anything to text
			"* text": window.String,

			// Text to html (true = no transformation)
			"text html": true,

			// Evaluate text as a json expression
			"text json": jQuery.parseJSON,

			// Parse text as xml
			"text xml": jQuery.parseXML
		}
	},

	ajaxPrefilter: addToPrefiltersOrTransports( prefilters ),
	ajaxTransport: addToPrefiltersOrTransports( transports ),

	// Main method
	ajax: function( url, options ) {

		// If url is an object, simulate pre-1.5 signature
		if ( typeof url === "object" ) {
			options = url;
			url = undefined;
		}

		// Force options to be an object
		options = options || {};

		var // Create the final options object
			s = jQuery.ajaxSetup( {}, options ),
			// Callbacks context
			callbackContext = s.context || s,
			// Context for global events
			// It's the callbackContext if one was provided in the options
			// and if it's a DOM node or a jQuery collection
			globalEventContext = callbackContext !== s &&
				( callbackContext.nodeType || callbackContext instanceof jQuery ) ?
						jQuery( callbackContext ) : jQuery.event,
			// Deferreds
			deferred = jQuery.Deferred(),
			completeDeferred = jQuery._Deferred(),
			// Status-dependent callbacks
			statusCode = s.statusCode || {},
			// ifModified key
			ifModifiedKey,
			// Headers (they are sent all at once)
			requestHeaders = {},
			// Response headers
			responseHeadersString,
			responseHeaders,
			// transport
			transport,
			// timeout handle
			timeoutTimer,
			// Cross-domain detection vars
			parts,
			// The jqXHR state
			state = 0,
			// To know if global events are to be dispatched
			fireGlobals,
			// Loop variable
			i,
			// Fake xhr
			jqXHR = {

				readyState: 0,

				// Caches the header
				setRequestHeader: function( name, value ) {
					if ( !state ) {
						requestHeaders[ name.toLowerCase().replace( rucHeaders, rucHeadersFunc ) ] = value;
					}
					return this;
				},

				// Raw string
				getAllResponseHeaders: function() {
					return state === 2 ? responseHeadersString : null;
				},

				// Builds headers hashtable if needed
				getResponseHeader: function( key ) {
					var match;
					if ( state === 2 ) {
						if ( !responseHeaders ) {
							responseHeaders = {};
							while( ( match = rheaders.exec( responseHeadersString ) ) ) {
								responseHeaders[ match[1].toLowerCase() ] = match[ 2 ];
							}
						}
						match = responseHeaders[ key.toLowerCase() ];
					}
					return match === undefined ? null : match;
				},

				// Overrides response content-type header
				overrideMimeType: function( type ) {
					if ( !state ) {
						s.mimeType = type;
					}
					return this;
				},

				// Cancel the request
				abort: function( statusText ) {
					statusText = statusText || "abort";
					if ( transport ) {
						transport.abort( statusText );
					}
					done( 0, statusText );
					return this;
				}
			};

		// Callback for when everything is done
		// It is defined here because jslint complains if it is declared
		// at the end of the function (which would be more logical and readable)
		function done( status, statusText, responses, headers ) {

			// Called once
			if ( state === 2 ) {
				return;
			}

			// State is "done" now
			state = 2;

			// Clear timeout if it exists
			if ( timeoutTimer ) {
				clearTimeout( timeoutTimer );
			}

			// Dereference transport for early garbage collection
			// (no matter how long the jqXHR object will be used)
			transport = undefined;

			// Cache response headers
			responseHeadersString = headers || "";

			// Set readyState
			jqXHR.readyState = status ? 4 : 0;

			var isSuccess,
				success,
				error,
				response = responses ? ajaxHandleResponses( s, jqXHR, responses ) : undefined,
				lastModified,
				etag;

			// If successful, handle type chaining
			if ( status >= 200 && status < 300 || status === 304 ) {

				// Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
				if ( s.ifModified ) {

					if ( ( lastModified = jqXHR.getResponseHeader( "Last-Modified" ) ) ) {
						jQuery.lastModified[ ifModifiedKey ] = lastModified;
					}
					if ( ( etag = jqXHR.getResponseHeader( "Etag" ) ) ) {
						jQuery.etag[ ifModifiedKey ] = etag;
					}
				}

				// If not modified
				if ( status === 304 ) {

					statusText = "notmodified";
					isSuccess = true;

				// If we have data
				} else {

					try {
						success = ajaxConvert( s, response );
						statusText = "success";
						isSuccess = true;
					} catch(e) {
						// We have a parsererror
						statusText = "parsererror";
						error = e;
					}
				}
			} else {
				// We extract error from statusText
				// then normalize statusText and status for non-aborts
				error = statusText;
				if( !statusText || status ) {
					statusText = "error";
					if ( status < 0 ) {
						status = 0;
					}
				}
			}

			// Set data for the fake xhr object
			jqXHR.status = status;
			jqXHR.statusText = statusText;

			// Success/Error
			if ( isSuccess ) {
				deferred.resolveWith( callbackContext, [ success, statusText, jqXHR ] );
			} else {
				deferred.rejectWith( callbackContext, [ jqXHR, statusText, error ] );
			}

			// Status-dependent callbacks
			jqXHR.statusCode( statusCode );
			statusCode = undefined;

			if ( fireGlobals ) {
				globalEventContext.trigger( "ajax" + ( isSuccess ? "Success" : "Error" ),
						[ jqXHR, s, isSuccess ? success : error ] );
			}

			// Complete
			completeDeferred.resolveWith( callbackContext, [ jqXHR, statusText ] );

			if ( fireGlobals ) {
				globalEventContext.trigger( "ajaxComplete", [ jqXHR, s] );
				// Handle the global AJAX counter
				if ( !( --jQuery.active ) ) {
					jQuery.event.trigger( "ajaxStop" );
				}
			}
		}

		// Attach deferreds
		deferred.promise( jqXHR );
		jqXHR.success = jqXHR.done;
		jqXHR.error = jqXHR.fail;
		jqXHR.complete = completeDeferred.done;

		// Status-dependent callbacks
		jqXHR.statusCode = function( map ) {
			if ( map ) {
				var tmp;
				if ( state < 2 ) {
					for( tmp in map ) {
						statusCode[ tmp ] = [ statusCode[tmp], map[tmp] ];
					}
				} else {
					tmp = map[ jqXHR.status ];
					jqXHR.then( tmp, tmp );
				}
			}
			return this;
		};

		// Remove hash character (#7531: and string promotion)
		// Add protocol if not provided (#5866: IE7 issue with protocol-less urls)
		// We also use the url parameter if available
		s.url = ( ( url || s.url ) + "" ).replace( rhash, "" ).replace( rprotocol, ajaxLocParts[ 1 ] + "//" );

		// Extract dataTypes list
		s.dataTypes = jQuery.trim( s.dataType || "*" ).toLowerCase().split( rspacesAjax );

		// Determine if a cross-domain request is in order
		if ( !s.crossDomain ) {
			parts = rurl.exec( s.url.toLowerCase() );
			s.crossDomain = !!( parts &&
				( parts[ 1 ] != ajaxLocParts[ 1 ] || parts[ 2 ] != ajaxLocParts[ 2 ] ||
					( parts[ 3 ] || ( parts[ 1 ] === "http:" ? 80 : 443 ) ) !=
						( ajaxLocParts[ 3 ] || ( ajaxLocParts[ 1 ] === "http:" ? 80 : 443 ) ) )
			);
		}

		// Convert data if not already a string
		if ( s.data && s.processData && typeof s.data !== "string" ) {
			s.data = jQuery.param( s.data, s.traditional );
		}

		// Apply prefilters
		inspectPrefiltersOrTransports( prefilters, s, options, jqXHR );

		// If request was aborted inside a prefiler, stop there
		if ( state === 2 ) {
			return false;
		}

		// We can fire global events as of now if asked to
		fireGlobals = s.global;

		// Uppercase the type
		s.type = s.type.toUpperCase();

		// Determine if request has content
		s.hasContent = !rnoContent.test( s.type );

		// Watch for a new set of requests
		if ( fireGlobals && jQuery.active++ === 0 ) {
			jQuery.event.trigger( "ajaxStart" );
		}

		// More options handling for requests with no content
		if ( !s.hasContent ) {

			// If data is available, append data to url
			if ( s.data ) {
				s.url += ( rquery.test( s.url ) ? "&" : "?" ) + s.data;
			}

			// Get ifModifiedKey before adding the anti-cache parameter
			ifModifiedKey = s.url;

			// Add anti-cache in url if needed
			if ( s.cache === false ) {

				var ts = jQuery.now(),
					// try replacing _= if it is there
					ret = s.url.replace( rts, "$1_=" + ts );

				// if nothing was replaced, add timestamp to the end
				s.url = ret + ( (ret === s.url ) ? ( rquery.test( s.url ) ? "&" : "?" ) + "_=" + ts : "" );
			}
		}

		// Set the correct header, if data is being sent
		if ( s.data && s.hasContent && s.contentType !== false || options.contentType ) {
			requestHeaders[ "Content-Type" ] = s.contentType;
		}

		// Set the If-Modified-Since and/or If-None-Match header, if in ifModified mode.
		if ( s.ifModified ) {
			ifModifiedKey = ifModifiedKey || s.url;
			if ( jQuery.lastModified[ ifModifiedKey ] ) {
				requestHeaders[ "If-Modified-Since" ] = jQuery.lastModified[ ifModifiedKey ];
			}
			if ( jQuery.etag[ ifModifiedKey ] ) {
				requestHeaders[ "If-None-Match" ] = jQuery.etag[ ifModifiedKey ];
			}
		}

		// Set the Accepts header for the server, depending on the dataType
		requestHeaders.Accept = s.dataTypes[ 0 ] && s.accepts[ s.dataTypes[0] ] ?
			s.accepts[ s.dataTypes[0] ] + ( s.dataTypes[ 0 ] !== "*" ? ", */*; q=0.01" : "" ) :
			s.accepts[ "*" ];

		// Check for headers option
		for ( i in s.headers ) {
			jqXHR.setRequestHeader( i, s.headers[ i ] );
		}

		// Allow custom headers/mimetypes and early abort
		if ( s.beforeSend && ( s.beforeSend.call( callbackContext, jqXHR, s ) === false || state === 2 ) ) {
				// Abort if not done already
				jqXHR.abort();
				return false;

		}

		// Install callbacks on deferreds
		for ( i in { success: 1, error: 1, complete: 1 } ) {
			jqXHR[ i ]( s[ i ] );
		}

		// Get transport
		transport = inspectPrefiltersOrTransports( transports, s, options, jqXHR );

		// If no transport, we auto-abort
		if ( !transport ) {
			done( -1, "No Transport" );
		} else {
			jqXHR.readyState = 1;
			// Send global event
			if ( fireGlobals ) {
				globalEventContext.trigger( "ajaxSend", [ jqXHR, s ] );
			}
			// Timeout
			if ( s.async && s.timeout > 0 ) {
				timeoutTimer = setTimeout( function(){
					jqXHR.abort( "timeout" );
				}, s.timeout );
			}

			try {
				state = 1;
				transport.send( requestHeaders, done );
			} catch (e) {
				// Propagate exception as error if not done
				if ( status < 2 ) {
					done( -1, e );
				// Simply rethrow otherwise
				} else {
					jQuery.error( e );
				}
			}
		}

		return jqXHR;
	},

	// Serialize an array of form elements or a set of
	// key/values into a query string
	param: function( a, traditional ) {
		var s = [],
			add = function( key, value ) {
				// If value is a function, invoke it and return its value
				value = jQuery.isFunction( value ) ? value() : value;
				s[ s.length ] = encodeURIComponent( key ) + "=" + encodeURIComponent( value );
			};

		// Set traditional to true for jQuery <= 1.3.2 behavior.
		if ( traditional === undefined ) {
			traditional = jQuery.ajaxSettings.traditional;
		}

		// If an array was passed in, assume that it is an array of form elements.
		if ( jQuery.isArray( a ) || ( a.jquery && !jQuery.isPlainObject( a ) ) ) {
			// Serialize the form elements
			jQuery.each( a, function() {
				add( this.name, this.value );
			} );

		} else {
			// If traditional, encode the "old" way (the way 1.3.2 or older
			// did it), otherwise encode params recursively.
			for ( var prefix in a ) {
				buildParams( prefix, a[ prefix ], traditional, add );
			}
		}

		// Return the resulting serialization
		return s.join( "&" ).replace( r20, "+" );
	}
});

function buildParams( prefix, obj, traditional, add ) {
	if ( jQuery.isArray( obj ) && obj.length ) {
		// Serialize array item.
		jQuery.each( obj, function( i, v ) {
			if ( traditional || rbracket.test( prefix ) ) {
				// Treat each array item as a scalar.
				add( prefix, v );

			} else {
				// If array item is non-scalar (array or object), encode its
				// numeric index to resolve deserialization ambiguity issues.
				// Note that rack (as of 1.0.0) can't currently deserialize
				// nested arrays properly, and attempting to do so may cause
				// a server error. Possible fixes are to modify rack's
				// deserialization algorithm or to provide an option or flag
				// to force array serialization to be shallow.
				buildParams( prefix + "[" + ( typeof v === "object" || jQuery.isArray(v) ? i : "" ) + "]", v, traditional, add );
			}
		});

	} else if ( !traditional && obj != null && typeof obj === "object" ) {
		// If we see an array here, it is empty and should be treated as an empty
		// object
		if ( jQuery.isArray( obj ) || jQuery.isEmptyObject( obj ) ) {
			add( prefix, "" );

		// Serialize object item.
		} else {
			for ( var name in obj ) {
				buildParams( prefix + "[" + name + "]", obj[ name ], traditional, add );
			}
		}

	} else {
		// Serialize scalar item.
		add( prefix, obj );
	}
}

// This is still on the jQuery object... for now
// Want to move this to jQuery.ajax some day
jQuery.extend({

	// Counter for holding the number of active queries
	active: 0,

	// Last-Modified header cache for next request
	lastModified: {},
	etag: {}

});

/* Handles responses to an ajax request:
 * - sets all responseXXX fields accordingly
 * - finds the right dataType (mediates between content-type and expected dataType)
 * - returns the corresponding response
 */
function ajaxHandleResponses( s, jqXHR, responses ) {

	var contents = s.contents,
		dataTypes = s.dataTypes,
		responseFields = s.responseFields,
		ct,
		type,
		finalDataType,
		firstDataType;

	// Fill responseXXX fields
	for( type in responseFields ) {
		if ( type in responses ) {
			jqXHR[ responseFields[type] ] = responses[ type ];
		}
	}

	// Remove auto dataType and get content-type in the process
	while( dataTypes[ 0 ] === "*" ) {
		dataTypes.shift();
		if ( ct === undefined ) {
			ct = s.mimeType || jqXHR.getResponseHeader( "content-type" );
		}
	}

	// Check if we're dealing with a known content-type
	if ( ct ) {
		for ( type in contents ) {
			if ( contents[ type ] && contents[ type ].test( ct ) ) {
				dataTypes.unshift( type );
				break;
			}
		}
	}

	// Check to see if we have a response for the expected dataType
	if ( dataTypes[ 0 ] in responses ) {
		finalDataType = dataTypes[ 0 ];
	} else {
		// Try convertible dataTypes
		for ( type in responses ) {
			if ( !dataTypes[ 0 ] || s.converters[ type + " " + dataTypes[0] ] ) {
				finalDataType = type;
				break;
			}
			if ( !firstDataType ) {
				firstDataType = type;
			}
		}
		// Or just use first one
		finalDataType = finalDataType || firstDataType;
	}

	// If we found a dataType
	// We add the dataType to the list if needed
	// and return the corresponding response
	if ( finalDataType ) {
		if ( finalDataType !== dataTypes[ 0 ] ) {
			dataTypes.unshift( finalDataType );
		}
		return responses[ finalDataType ];
	}
}

// Chain conversions given the request and the original response
function ajaxConvert( s, response ) {

	// Apply the dataFilter if provided
	if ( s.dataFilter ) {
		response = s.dataFilter( response, s.dataType );
	}

	var dataTypes = s.dataTypes,
		converters = {},
		i,
		key,
		length = dataTypes.length,
		tmp,
		// Current and previous dataTypes
		current = dataTypes[ 0 ],
		prev,
		// Conversion expression
		conversion,
		// Conversion function
		conv,
		// Conversion functions (transitive conversion)
		conv1,
		conv2;

	// For each dataType in the chain
	for( i = 1; i < length; i++ ) {

		// Create converters map
		// with lowercased keys
		if ( i === 1 ) {
			for( key in s.converters ) {
				if( typeof key === "string" ) {
					converters[ key.toLowerCase() ] = s.converters[ key ];
				}
			}
		}

		// Get the dataTypes
		prev = current;
		current = dataTypes[ i ];

		// If current is auto dataType, update it to prev
		if( current === "*" ) {
			current = prev;
		// If no auto and dataTypes are actually different
		} else if ( prev !== "*" && prev !== current ) {

			// Get the converter
			conversion = prev + " " + current;
			conv = converters[ conversion ] || converters[ "* " + current ];

			// If there is no direct converter, search transitively
			if ( !conv ) {
				conv2 = undefined;
				for( conv1 in converters ) {
					tmp = conv1.split( " " );
					if ( tmp[ 0 ] === prev || tmp[ 0 ] === "*" ) {
						conv2 = converters[ tmp[1] + " " + current ];
						if ( conv2 ) {
							conv1 = converters[ conv1 ];
							if ( conv1 === true ) {
								conv = conv2;
							} else if ( conv2 === true ) {
								conv = conv1;
							}
							break;
						}
					}
				}
			}
			// If we found no converter, dispatch an error
			if ( !( conv || conv2 ) ) {
				jQuery.error( "No conversion from " + conversion.replace(" "," to ") );
			}
			// If found converter is not an equivalence
			if ( conv !== true ) {
				// Convert with 1 or 2 converters accordingly
				response = conv ? conv( response ) : conv2( conv1(response) );
			}
		}
	}
	return response;
}




var jsc = jQuery.now(),
	jsre = /(\=)\?(&|$)|()\?\?()/i;

// Default jsonp settings
jQuery.ajaxSetup({
	jsonp: "callback",
	jsonpCallback: function() {
		return jQuery.expando + "_" + ( jsc++ );
	}
});

// Detect, normalize options and install callbacks for jsonp requests
jQuery.ajaxPrefilter( "json jsonp", function( s, originalSettings, jqXHR ) {

	var dataIsString = ( typeof s.data === "string" );

	if ( s.dataTypes[ 0 ] === "jsonp" ||
		originalSettings.jsonpCallback ||
		originalSettings.jsonp != null ||
		s.jsonp !== false && ( jsre.test( s.url ) ||
				dataIsString && jsre.test( s.data ) ) ) {

		var responseContainer,
			jsonpCallback = s.jsonpCallback =
				jQuery.isFunction( s.jsonpCallback ) ? s.jsonpCallback() : s.jsonpCallback,
			previous = window[ jsonpCallback ],
			url = s.url,
			data = s.data,
			replace = "$1" + jsonpCallback + "$2",
			cleanUp = function() {
				// Set callback back to previous value
				window[ jsonpCallback ] = previous;
				// Call if it was a function and we have a response
				if ( responseContainer && jQuery.isFunction( previous ) ) {
					window[ jsonpCallback ]( responseContainer[ 0 ] );
				}
			};

		if ( s.jsonp !== false ) {
			url = url.replace( jsre, replace );
			if ( s.url === url ) {
				if ( dataIsString ) {
					data = data.replace( jsre, replace );
				}
				if ( s.data === data ) {
					// Add callback manually
					url += (/\?/.test( url ) ? "&" : "?") + s.jsonp + "=" + jsonpCallback;
				}
			}
		}

		s.url = url;
		s.data = data;

		// Install callback
		window[ jsonpCallback ] = function( response ) {
			responseContainer = [ response ];
		};

		// Install cleanUp function
		jqXHR.then( cleanUp, cleanUp );

		// Use data converter to retrieve json after script execution
		s.converters["script json"] = function() {
			if ( !responseContainer ) {
				jQuery.error( jsonpCallback + " was not called" );
			}
			return responseContainer[ 0 ];
		};

		// force json dataType
		s.dataTypes[ 0 ] = "json";

		// Delegate to script
		return "script";
	}
} );




// Install script dataType
jQuery.ajaxSetup({
	accepts: {
		script: "text/javascript, application/javascript, application/ecmascript, application/x-ecmascript"
	},
	contents: {
		script: /javascript|ecmascript/
	},
	converters: {
		"text script": function( text ) {
			jQuery.globalEval( text );
			return text;
		}
	}
});

// Handle cache's special case and global
jQuery.ajaxPrefilter( "script", function( s ) {
	if ( s.cache === undefined ) {
		s.cache = false;
	}
	if ( s.crossDomain ) {
		s.type = "GET";
		s.global = false;
	}
} );

// Bind script tag hack transport
jQuery.ajaxTransport( "script", function(s) {

	// This transport only deals with cross domain requests
	if ( s.crossDomain ) {

		var script,
			head = document.head || document.getElementsByTagName( "head" )[0] || document.documentElement;

		return {

			send: function( _, callback ) {

				script = document.createElement( "script" );

				script.async = "async";

				if ( s.scriptCharset ) {
					script.charset = s.scriptCharset;
				}

				script.src = s.url;

				// Attach handlers for all browsers
				script.onload = script.onreadystatechange = function( _, isAbort ) {

					if ( !script.readyState || /loaded|complete/.test( script.readyState ) ) {

						// Handle memory leak in IE
						script.onload = script.onreadystatechange = null;

						// Remove the script
						if ( head && script.parentNode ) {
							head.removeChild( script );
						}

						// Dereference the script
						script = undefined;

						// Callback if not abort
						if ( !isAbort ) {
							callback( 200, "success" );
						}
					}
				};
				// Use insertBefore instead of appendChild  to circumvent an IE6 bug.
				// This arises when a base node is used (#2709 and #4378).
				head.insertBefore( script, head.firstChild );
			},

			abort: function() {
				if ( script ) {
					script.onload( 0, 1 );
				}
			}
		};
	}
} );




var // #5280: next active xhr id and list of active xhrs' callbacks
	xhrId = jQuery.now(),
	xhrCallbacks,

	// XHR used to determine supports properties
	testXHR;

// #5280: Internet Explorer will keep connections alive if we don't abort on unload
function xhrOnUnloadAbort() {
	jQuery( window ).unload(function() {
		// Abort all pending requests
		for ( var key in xhrCallbacks ) {
			xhrCallbacks[ key ]( 0, 1 );
		}
	});
}

// Functions to create xhrs
function createStandardXHR() {
	try {
		return new window.XMLHttpRequest();
	} catch( e ) {}
}

function createActiveXHR() {
	try {
		return new window.ActiveXObject( "Microsoft.XMLHTTP" );
	} catch( e ) {}
}

// Create the request object
// (This is still attached to ajaxSettings for backward compatibility)
jQuery.ajaxSettings.xhr = window.ActiveXObject ?
	/* Microsoft failed to properly
	 * implement the XMLHttpRequest in IE7 (can't request local files),
	 * so we use the ActiveXObject when it is available
	 * Additionally XMLHttpRequest can be disabled in IE7/IE8 so
	 * we need a fallback.
	 */
	function() {
		return !this.isLocal && createStandardXHR() || createActiveXHR();
	} :
	// For all other browsers, use the standard XMLHttpRequest object
	createStandardXHR;

// Test if we can create an xhr object
testXHR = jQuery.ajaxSettings.xhr();
jQuery.support.ajax = !!testXHR;

// Does this browser support crossDomain XHR requests
jQuery.support.cors = testXHR && ( "withCredentials" in testXHR );

// No need for the temporary xhr anymore
testXHR = undefined;

// Create transport if the browser can provide an xhr
if ( jQuery.support.ajax ) {

	jQuery.ajaxTransport(function( s ) {
		// Cross domain only allowed if supported through XMLHttpRequest
		if ( !s.crossDomain || jQuery.support.cors ) {

			var callback;

			return {
				send: function( headers, complete ) {

					// Get a new xhr
					var xhr = s.xhr(),
						handle,
						i;

					// Open the socket
					// Passing null username, generates a login popup on Opera (#2865)
					if ( s.username ) {
						xhr.open( s.type, s.url, s.async, s.username, s.password );
					} else {
						xhr.open( s.type, s.url, s.async );
					}

					// Apply custom fields if provided
					if ( s.xhrFields ) {
						for ( i in s.xhrFields ) {
							xhr[ i ] = s.xhrFields[ i ];
						}
					}

					// Override mime type if needed
					if ( s.mimeType && xhr.overrideMimeType ) {
						xhr.overrideMimeType( s.mimeType );
					}

					// Requested-With header
					// Not set for crossDomain requests with no content
					// (see why at http://trac.dojotoolkit.org/ticket/9486)
					// Won't change header if already provided
					if ( !( s.crossDomain && !s.hasContent ) && !headers["X-Requested-With"] ) {
						headers[ "X-Requested-With" ] = "XMLHttpRequest";
					}

					// Need an extra try/catch for cross domain requests in Firefox 3
					try {
						for ( i in headers ) {
							xhr.setRequestHeader( i, headers[ i ] );
						}
					} catch( _ ) {}

					// Do send the request
					// This may raise an exception which is actually
					// handled in jQuery.ajax (so no try/catch here)
					xhr.send( ( s.hasContent && s.data ) || null );

					// Listener
					callback = function( _, isAbort ) {

						var status,
							statusText,
							responseHeaders,
							responses,
							xml;

						// Firefox throws exceptions when accessing properties
						// of an xhr when a network error occured
						// http://helpful.knobs-dials.com/index.php/Component_returned_failure_code:_0x80040111_(NS_ERROR_NOT_AVAILABLE)
						try {

							// Was never called and is aborted or complete
							if ( callback && ( isAbort || xhr.readyState === 4 ) ) {

								// Only called once
								callback = undefined;

								// Do not keep as active anymore
								if ( handle ) {
									xhr.onreadystatechange = jQuery.noop;
									delete xhrCallbacks[ handle ];
								}

								// If it's an abort
								if ( isAbort ) {
									// Abort it manually if needed
									if ( xhr.readyState !== 4 ) {
										xhr.abort();
									}
								} else {
									status = xhr.status;
									responseHeaders = xhr.getAllResponseHeaders();
									responses = {};
									xml = xhr.responseXML;

									// Construct response list
									if ( xml && xml.documentElement /* #4958 */ ) {
										responses.xml = xml;
									}
									responses.text = xhr.responseText;

									// Firefox throws an exception when accessing
									// statusText for faulty cross-domain requests
									try {
										statusText = xhr.statusText;
									} catch( e ) {
										// We normalize with Webkit giving an empty statusText
										statusText = "";
									}

									// Filter status for non standard behaviors

									// If the request is local and we have data: assume a success
									// (success with no data won't get notified, that's the best we
									// can do given current implementations)
									if ( !status && s.isLocal && !s.crossDomain ) {
										status = responses.text ? 200 : 404;
									// IE - #1450: sometimes returns 1223 when it should be 204
									} else if ( status === 1223 ) {
										status = 204;
									}
								}
							}
						} catch( firefoxAccessException ) {
							if ( !isAbort ) {
								complete( -1, firefoxAccessException );
							}
						}

						// Call complete if needed
						if ( responses ) {
							complete( status, statusText, responses, responseHeaders );
						}
					};

					// if we're in sync mode or it's in cache
					// and has been retrieved directly (IE6 & IE7)
					// we need to manually fire the callback
					if ( !s.async || xhr.readyState === 4 ) {
						callback();
					} else {
						// Create the active xhrs callbacks list if needed
						// and attach the unload handler
						if ( !xhrCallbacks ) {
							xhrCallbacks = {};
							xhrOnUnloadAbort();
						}
						// Add to list of active xhrs callbacks
						handle = xhrId++;
						xhr.onreadystatechange = xhrCallbacks[ handle ] = callback;
					}
				},

				abort: function() {
					if ( callback ) {
						callback(0,1);
					}
				}
			};
		}
	});
}




var elemdisplay = {},
	rfxtypes = /^(?:toggle|show|hide)$/,
	rfxnum = /^([+\-]=)?([\d+.\-]+)([a-z%]*)$/i,
	timerId,
	fxAttrs = [
		// height animations
		[ "height", "marginTop", "marginBottom", "paddingTop", "paddingBottom" ],
		// width animations
		[ "width", "marginLeft", "marginRight", "paddingLeft", "paddingRight" ],
		// opacity animations
		[ "opacity" ]
	];

jQuery.fn.extend({
	show: function( speed, easing, callback ) {
		var elem, display;

		if ( speed || speed === 0 ) {
			return this.animate( genFx("show", 3), speed, easing, callback);

		} else {
			for ( var i = 0, j = this.length; i < j; i++ ) {
				elem = this[i];
				display = elem.style.display;

				// Reset the inline display of this element to learn if it is
				// being hidden by cascaded rules or not
				if ( !jQuery._data(elem, "olddisplay") && display === "none" ) {
					display = elem.style.display = "";
				}

				// Set elements which have been overridden with display: none
				// in a stylesheet to whatever the default browser style is
				// for such an element
				if ( display === "" && jQuery.css( elem, "display" ) === "none" ) {
					jQuery._data(elem, "olddisplay", defaultDisplay(elem.nodeName));
				}
			}

			// Set the display of most of the elements in a second loop
			// to avoid the constant reflow
			for ( i = 0; i < j; i++ ) {
				elem = this[i];
				display = elem.style.display;

				if ( display === "" || display === "none" ) {
					elem.style.display = jQuery._data(elem, "olddisplay") || "";
				}
			}

			return this;
		}
	},

	hide: function( speed, easing, callback ) {
		if ( speed || speed === 0 ) {
			return this.animate( genFx("hide", 3), speed, easing, callback);

		} else {
			for ( var i = 0, j = this.length; i < j; i++ ) {
				var display = jQuery.css( this[i], "display" );

				if ( display !== "none" && !jQuery._data( this[i], "olddisplay" ) ) {
					jQuery._data( this[i], "olddisplay", display );
				}
			}

			// Set the display of the elements in a second loop
			// to avoid the constant reflow
			for ( i = 0; i < j; i++ ) {
				this[i].style.display = "none";
			}

			return this;
		}
	},

	// Save the old toggle function
	_toggle: jQuery.fn.toggle,

	toggle: function( fn, fn2, callback ) {
		var bool = typeof fn === "boolean";

		if ( jQuery.isFunction(fn) && jQuery.isFunction(fn2) ) {
			this._toggle.apply( this, arguments );

		} else if ( fn == null || bool ) {
			this.each(function() {
				var state = bool ? fn : jQuery(this).is(":hidden");
				jQuery(this)[ state ? "show" : "hide" ]();
			});

		} else {
			this.animate(genFx("toggle", 3), fn, fn2, callback);
		}

		return this;
	},

	fadeTo: function( speed, to, easing, callback ) {
		return this.filter(":hidden").css("opacity", 0).show().end()
					.animate({opacity: to}, speed, easing, callback);
	},

	animate: function( prop, speed, easing, callback ) {
		var optall = jQuery.speed(speed, easing, callback);

		if ( jQuery.isEmptyObject( prop ) ) {
			return this.each( optall.complete );
		}

		return this[ optall.queue === false ? "each" : "queue" ](function() {
			// XXX 'this' does not always have a nodeName when running the
			// test suite

			var opt = jQuery.extend({}, optall), p,
				isElement = this.nodeType === 1,
				hidden = isElement && jQuery(this).is(":hidden"),
				self = this;

			for ( p in prop ) {
				var name = jQuery.camelCase( p );

				if ( p !== name ) {
					prop[ name ] = prop[ p ];
					delete prop[ p ];
					p = name;
				}

				if ( prop[p] === "hide" && hidden || prop[p] === "show" && !hidden ) {
					return opt.complete.call(this);
				}

				if ( isElement && ( p === "height" || p === "width" ) ) {
					// Make sure that nothing sneaks out
					// Record all 3 overflow attributes because IE does not
					// change the overflow attribute when overflowX and
					// overflowY are set to the same value
					opt.overflow = [ this.style.overflow, this.style.overflowX, this.style.overflowY ];

					// Set display property to inline-block for height/width
					// animations on inline elements that are having width/height
					// animated
					if ( jQuery.css( this, "display" ) === "inline" &&
							jQuery.css( this, "float" ) === "none" ) {
						if ( !jQuery.support.inlineBlockNeedsLayout ) {
							this.style.display = "inline-block";

						} else {
							var display = defaultDisplay(this.nodeName);

							// inline-level elements accept inline-block;
							// block-level elements need to be inline with layout
							if ( display === "inline" ) {
								this.style.display = "inline-block";

							} else {
								this.style.display = "inline";
								this.style.zoom = 1;
							}
						}
					}
				}

				if ( jQuery.isArray( prop[p] ) ) {
					// Create (if needed) and add to specialEasing
					(opt.specialEasing = opt.specialEasing || {})[p] = prop[p][1];
					prop[p] = prop[p][0];
				}
			}

			if ( opt.overflow != null ) {
				this.style.overflow = "hidden";
			}

			opt.curAnim = jQuery.extend({}, prop);

			jQuery.each( prop, function( name, val ) {
				var e = new jQuery.fx( self, opt, name );

				if ( rfxtypes.test(val) ) {
					e[ val === "toggle" ? hidden ? "show" : "hide" : val ]( prop );

				} else {
					var parts = rfxnum.exec(val),
						start = e.cur();

					if ( parts ) {
						var end = parseFloat( parts[2] ),
							unit = parts[3] || ( jQuery.cssNumber[ name ] ? "" : "px" );

						// We need to compute starting value
						if ( unit !== "px" ) {
							jQuery.style( self, name, (end || 1) + unit);
							start = ((end || 1) / e.cur()) * start;
							jQuery.style( self, name, start + unit);
						}

						// If a +=/-= token was provided, we're doing a relative animation
						if ( parts[1] ) {
							end = ((parts[1] === "-=" ? -1 : 1) * end) + start;
						}

						e.custom( start, end, unit );

					} else {
						e.custom( start, val, "" );
					}
				}
			});

			// For JS strict compliance
			return true;
		});
	},

	stop: function( clearQueue, gotoEnd ) {
		var timers = jQuery.timers;

		if ( clearQueue ) {
			this.queue([]);
		}

		this.each(function() {
			// go in reverse order so anything added to the queue during the loop is ignored
			for ( var i = timers.length - 1; i >= 0; i-- ) {
				if ( timers[i].elem === this ) {
					if (gotoEnd) {
						// force the next step to be the last
						timers[i](true);
					}

					timers.splice(i, 1);
				}
			}
		});

		// start the next in the queue if the last step wasn't forced
		if ( !gotoEnd ) {
			this.dequeue();
		}

		return this;
	}

});

function genFx( type, num ) {
	var obj = {};

	jQuery.each( fxAttrs.concat.apply([], fxAttrs.slice(0,num)), function() {
		obj[ this ] = type;
	});

	return obj;
}

// Generate shortcuts for custom animations
jQuery.each({
	slideDown: genFx("show", 1),
	slideUp: genFx("hide", 1),
	slideToggle: genFx("toggle", 1),
	fadeIn: { opacity: "show" },
	fadeOut: { opacity: "hide" },
	fadeToggle: { opacity: "toggle" }
}, function( name, props ) {
	jQuery.fn[ name ] = function( speed, easing, callback ) {
		return this.animate( props, speed, easing, callback );
	};
});

jQuery.extend({
	speed: function( speed, easing, fn ) {
		var opt = speed && typeof speed === "object" ? jQuery.extend({}, speed) : {
			complete: fn || !fn && easing ||
				jQuery.isFunction( speed ) && speed,
			duration: speed,
			easing: fn && easing || easing && !jQuery.isFunction(easing) && easing
		};

		opt.duration = jQuery.fx.off ? 0 : typeof opt.duration === "number" ? opt.duration :
			opt.duration in jQuery.fx.speeds ? jQuery.fx.speeds[opt.duration] : jQuery.fx.speeds._default;

		// Queueing
		opt.old = opt.complete;
		opt.complete = function() {
			if ( opt.queue !== false ) {
				jQuery(this).dequeue();
			}
			if ( jQuery.isFunction( opt.old ) ) {
				opt.old.call( this );
			}
		};

		return opt;
	},

	easing: {
		linear: function( p, n, firstNum, diff ) {
			return firstNum + diff * p;
		},
		swing: function( p, n, firstNum, diff ) {
			return ((-Math.cos(p*Math.PI)/2) + 0.5) * diff + firstNum;
		}
	},

	timers: [],

	fx: function( elem, options, prop ) {
		this.options = options;
		this.elem = elem;
		this.prop = prop;

		if ( !options.orig ) {
			options.orig = {};
		}
	}

});

jQuery.fx.prototype = {
	// Simple function for setting a style value
	update: function() {
		if ( this.options.step ) {
			this.options.step.call( this.elem, this.now, this );
		}

		(jQuery.fx.step[this.prop] || jQuery.fx.step._default)( this );
	},

	// Get the current size
	cur: function() {
		if ( this.elem[this.prop] != null && (!this.elem.style || this.elem.style[this.prop] == null) ) {
			return this.elem[ this.prop ];
		}

		var parsed,
			r = jQuery.css( this.elem, this.prop );
		// Empty strings, null, undefined and "auto" are converted to 0,
		// complex values such as "rotate(1rad)" are returned as is,
		// simple values such as "10px" are parsed to Float.
		return isNaN( parsed = parseFloat( r ) ) ? !r || r === "auto" ? 0 : r : parsed;
	},

	// Start an animation from one number to another
	custom: function( from, to, unit ) {
		var self = this,
			fx = jQuery.fx;

		this.startTime = jQuery.now();
		this.start = from;
		this.end = to;
		this.unit = unit || this.unit || ( jQuery.cssNumber[ this.prop ] ? "" : "px" );
		this.now = this.start;
		this.pos = this.state = 0;

		function t( gotoEnd ) {
			return self.step(gotoEnd);
		}

		t.elem = this.elem;

		if ( t() && jQuery.timers.push(t) && !timerId ) {
			timerId = setInterval(fx.tick, fx.interval);
		}
	},

	// Simple 'show' function
	show: function() {
		// Remember where we started, so that we can go back to it later
		this.options.orig[this.prop] = jQuery.style( this.elem, this.prop );
		this.options.show = true;

		// Begin the animation
		// Make sure that we start at a small width/height to avoid any
		// flash of content
		this.custom(this.prop === "width" || this.prop === "height" ? 1 : 0, this.cur());

		// Start by showing the element
		jQuery( this.elem ).show();
	},

	// Simple 'hide' function
	hide: function() {
		// Remember where we started, so that we can go back to it later
		this.options.orig[this.prop] = jQuery.style( this.elem, this.prop );
		this.options.hide = true;

		// Begin the animation
		this.custom(this.cur(), 0);
	},

	// Each step of an animation
	step: function( gotoEnd ) {
		var t = jQuery.now(), done = true;

		if ( gotoEnd || t >= this.options.duration + this.startTime ) {
			this.now = this.end;
			this.pos = this.state = 1;
			this.update();

			this.options.curAnim[ this.prop ] = true;

			for ( var i in this.options.curAnim ) {
				if ( this.options.curAnim[i] !== true ) {
					done = false;
				}
			}

			if ( done ) {
				// Reset the overflow
				if ( this.options.overflow != null && !jQuery.support.shrinkWrapBlocks ) {
					var elem = this.elem,
						options = this.options;

					jQuery.each( [ "", "X", "Y" ], function (index, value) {
						elem.style[ "overflow" + value ] = options.overflow[index];
					} );
				}

				// Hide the element if the "hide" operation was done
				if ( this.options.hide ) {
					jQuery(this.elem).hide();
				}

				// Reset the properties, if the item has been hidden or shown
				if ( this.options.hide || this.options.show ) {
					for ( var p in this.options.curAnim ) {
						jQuery.style( this.elem, p, this.options.orig[p] );
					}
				}

				// Execute the complete function
				this.options.complete.call( this.elem );
			}

			return false;

		} else {
			var n = t - this.startTime;
			this.state = n / this.options.duration;

			// Perform the easing function, defaults to swing
			var specialEasing = this.options.specialEasing && this.options.specialEasing[this.prop];
			var defaultEasing = this.options.easing || (jQuery.easing.swing ? "swing" : "linear");
			this.pos = jQuery.easing[specialEasing || defaultEasing](this.state, n, 0, 1, this.options.duration);
			this.now = this.start + ((this.end - this.start) * this.pos);

			// Perform the next step of the animation
			this.update();
		}

		return true;
	}
};

jQuery.extend( jQuery.fx, {
	tick: function() {
		var timers = jQuery.timers;

		for ( var i = 0; i < timers.length; i++ ) {
			if ( !timers[i]() ) {
				timers.splice(i--, 1);
			}
		}

		if ( !timers.length ) {
			jQuery.fx.stop();
		}
	},

	interval: 13,

	stop: function() {
		clearInterval( timerId );
		timerId = null;
	},

	speeds: {
		slow: 600,
		fast: 200,
		// Default speed
		_default: 400
	},

	step: {
		opacity: function( fx ) {
			jQuery.style( fx.elem, "opacity", fx.now );
		},

		_default: function( fx ) {
			if ( fx.elem.style && fx.elem.style[ fx.prop ] != null ) {
				fx.elem.style[ fx.prop ] = (fx.prop === "width" || fx.prop === "height" ? Math.max(0, fx.now) : fx.now) + fx.unit;
			} else {
				fx.elem[ fx.prop ] = fx.now;
			}
		}
	}
});

if ( jQuery.expr && jQuery.expr.filters ) {
	jQuery.expr.filters.animated = function( elem ) {
		return jQuery.grep(jQuery.timers, function( fn ) {
			return elem === fn.elem;
		}).length;
	};
}

function defaultDisplay( nodeName ) {
	if ( !elemdisplay[ nodeName ] ) {
		var elem = jQuery("<" + nodeName + ">").appendTo("body"),
			display = elem.css("display");

		elem.remove();

		if ( display === "none" || display === "" ) {
			display = "block";
		}

		elemdisplay[ nodeName ] = display;
	}

	return elemdisplay[ nodeName ];
}




var rtable = /^t(?:able|d|h)$/i,
	rroot = /^(?:body|html)$/i;

if ( "getBoundingClientRect" in document.documentElement ) {
	jQuery.fn.offset = function( options ) {
		var elem = this[0], box;

		if ( options ) {
			return this.each(function( i ) {
				jQuery.offset.setOffset( this, options, i );
			});
		}

		if ( !elem || !elem.ownerDocument ) {
			return null;
		}

		if ( elem === elem.ownerDocument.body ) {
			return jQuery.offset.bodyOffset( elem );
		}

		try {
			box = elem.getBoundingClientRect();
		} catch(e) {}

		var doc = elem.ownerDocument,
			docElem = doc.documentElement;

		// Make sure we're not dealing with a disconnected DOM node
		if ( !box || !jQuery.contains( docElem, elem ) ) {
			return box ? { top: box.top, left: box.left } : { top: 0, left: 0 };
		}

		var body = doc.body,
			win = getWindow(doc),
			clientTop  = docElem.clientTop  || body.clientTop  || 0,
			clientLeft = docElem.clientLeft || body.clientLeft || 0,
			scrollTop  = (win.pageYOffset || jQuery.support.boxModel && docElem.scrollTop  || body.scrollTop ),
			scrollLeft = (win.pageXOffset || jQuery.support.boxModel && docElem.scrollLeft || body.scrollLeft),
			top  = box.top  + scrollTop  - clientTop,
			left = box.left + scrollLeft - clientLeft;

		return { top: top, left: left };
	};

} else {
	jQuery.fn.offset = function( options ) {
		var elem = this[0];

		if ( options ) {
			return this.each(function( i ) {
				jQuery.offset.setOffset( this, options, i );
			});
		}

		if ( !elem || !elem.ownerDocument ) {
			return null;
		}

		if ( elem === elem.ownerDocument.body ) {
			return jQuery.offset.bodyOffset( elem );
		}

		jQuery.offset.initialize();

		var computedStyle,
			offsetParent = elem.offsetParent,
			prevOffsetParent = elem,
			doc = elem.ownerDocument,
			docElem = doc.documentElement,
			body = doc.body,
			defaultView = doc.defaultView,
			prevComputedStyle = defaultView ? defaultView.getComputedStyle( elem, null ) : elem.currentStyle,
			top = elem.offsetTop,
			left = elem.offsetLeft;

		while ( (elem = elem.parentNode) && elem !== body && elem !== docElem ) {
			if ( jQuery.offset.supportsFixedPosition && prevComputedStyle.position === "fixed" ) {
				break;
			}

			computedStyle = defaultView ? defaultView.getComputedStyle(elem, null) : elem.currentStyle;
			top  -= elem.scrollTop;
			left -= elem.scrollLeft;

			if ( elem === offsetParent ) {
				top  += elem.offsetTop;
				left += elem.offsetLeft;

				if ( jQuery.offset.doesNotAddBorder && !(jQuery.offset.doesAddBorderForTableAndCells && rtable.test(elem.nodeName)) ) {
					top  += parseFloat( computedStyle.borderTopWidth  ) || 0;
					left += parseFloat( computedStyle.borderLeftWidth ) || 0;
				}

				prevOffsetParent = offsetParent;
				offsetParent = elem.offsetParent;
			}

			if ( jQuery.offset.subtractsBorderForOverflowNotVisible && computedStyle.overflow !== "visible" ) {
				top  += parseFloat( computedStyle.borderTopWidth  ) || 0;
				left += parseFloat( computedStyle.borderLeftWidth ) || 0;
			}

			prevComputedStyle = computedStyle;
		}

		if ( prevComputedStyle.position === "relative" || prevComputedStyle.position === "static" ) {
			top  += body.offsetTop;
			left += body.offsetLeft;
		}

		if ( jQuery.offset.supportsFixedPosition && prevComputedStyle.position === "fixed" ) {
			top  += Math.max( docElem.scrollTop, body.scrollTop );
			left += Math.max( docElem.scrollLeft, body.scrollLeft );
		}

		return { top: top, left: left };
	};
}

jQuery.offset = {
	initialize: function() {
		var body = document.body, container = document.createElement("div"), innerDiv, checkDiv, table, td, bodyMarginTop = parseFloat( jQuery.css(body, "marginTop") ) || 0,
			html = "<div style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;'><div></div></div><table style='position:absolute;top:0;left:0;margin:0;border:5px solid #000;padding:0;width:1px;height:1px;' cellpadding='0' cellspacing='0'><tr><td></td></tr></table>";

		jQuery.extend( container.style, { position: "absolute", top: 0, left: 0, margin: 0, border: 0, width: "1px", height: "1px", visibility: "hidden" } );

		container.innerHTML = html;
		body.insertBefore( container, body.firstChild );
		innerDiv = container.firstChild;
		checkDiv = innerDiv.firstChild;
		td = innerDiv.nextSibling.firstChild.firstChild;

		this.doesNotAddBorder = (checkDiv.offsetTop !== 5);
		this.doesAddBorderForTableAndCells = (td.offsetTop === 5);

		checkDiv.style.position = "fixed";
		checkDiv.style.top = "20px";

		// safari subtracts parent border width here which is 5px
		this.supportsFixedPosition = (checkDiv.offsetTop === 20 || checkDiv.offsetTop === 15);
		checkDiv.style.position = checkDiv.style.top = "";

		innerDiv.style.overflow = "hidden";
		innerDiv.style.position = "relative";

		this.subtractsBorderForOverflowNotVisible = (checkDiv.offsetTop === -5);

		this.doesNotIncludeMarginInBodyOffset = (body.offsetTop !== bodyMarginTop);

		body.removeChild( container );
		body = container = innerDiv = checkDiv = table = td = null;
		jQuery.offset.initialize = jQuery.noop;
	},

	bodyOffset: function( body ) {
		var top = body.offsetTop,
			left = body.offsetLeft;

		jQuery.offset.initialize();

		if ( jQuery.offset.doesNotIncludeMarginInBodyOffset ) {
			top  += parseFloat( jQuery.css(body, "marginTop") ) || 0;
			left += parseFloat( jQuery.css(body, "marginLeft") ) || 0;
		}

		return { top: top, left: left };
	},

	setOffset: function( elem, options, i ) {
		var position = jQuery.css( elem, "position" );

		// set position first, in-case top/left are set even on static elem
		if ( position === "static" ) {
			elem.style.position = "relative";
		}

		var curElem = jQuery( elem ),
			curOffset = curElem.offset(),
			curCSSTop = jQuery.css( elem, "top" ),
			curCSSLeft = jQuery.css( elem, "left" ),
			calculatePosition = (position === "absolute" && jQuery.inArray('auto', [curCSSTop, curCSSLeft]) > -1),
			props = {}, curPosition = {}, curTop, curLeft;

		// need to be able to calculate position if either top or left is auto and position is absolute
		if ( calculatePosition ) {
			curPosition = curElem.position();
		}

		curTop  = calculatePosition ? curPosition.top  : parseInt( curCSSTop,  10 ) || 0;
		curLeft = calculatePosition ? curPosition.left : parseInt( curCSSLeft, 10 ) || 0;

		if ( jQuery.isFunction( options ) ) {
			options = options.call( elem, i, curOffset );
		}

		if (options.top != null) {
			props.top = (options.top - curOffset.top) + curTop;
		}
		if (options.left != null) {
			props.left = (options.left - curOffset.left) + curLeft;
		}

		if ( "using" in options ) {
			options.using.call( elem, props );
		} else {
			curElem.css( props );
		}
	}
};


jQuery.fn.extend({
	position: function() {
		if ( !this[0] ) {
			return null;
		}

		var elem = this[0],

		// Get *real* offsetParent
		offsetParent = this.offsetParent(),

		// Get correct offsets
		offset       = this.offset(),
		parentOffset = rroot.test(offsetParent[0].nodeName) ? { top: 0, left: 0 } : offsetParent.offset();

		// Subtract element margins
		// note: when an element has margin: auto the offsetLeft and marginLeft
		// are the same in Safari causing offset.left to incorrectly be 0
		offset.top  -= parseFloat( jQuery.css(elem, "marginTop") ) || 0;
		offset.left -= parseFloat( jQuery.css(elem, "marginLeft") ) || 0;

		// Add offsetParent borders
		parentOffset.top  += parseFloat( jQuery.css(offsetParent[0], "borderTopWidth") ) || 0;
		parentOffset.left += parseFloat( jQuery.css(offsetParent[0], "borderLeftWidth") ) || 0;

		// Subtract the two offsets
		return {
			top:  offset.top  - parentOffset.top,
			left: offset.left - parentOffset.left
		};
	},

	offsetParent: function() {
		return this.map(function() {
			var offsetParent = this.offsetParent || document.body;
			while ( offsetParent && (!rroot.test(offsetParent.nodeName) && jQuery.css(offsetParent, "position") === "static") ) {
				offsetParent = offsetParent.offsetParent;
			}
			return offsetParent;
		});
	}
});


// Create scrollLeft and scrollTop methods
jQuery.each( ["Left", "Top"], function( i, name ) {
	var method = "scroll" + name;

	jQuery.fn[ method ] = function(val) {
		var elem = this[0], win;

		if ( !elem ) {
			return null;
		}

		if ( val !== undefined ) {
			// Set the scroll offset
			return this.each(function() {
				win = getWindow( this );

				if ( win ) {
					win.scrollTo(
						!i ? val : jQuery(win).scrollLeft(),
						i ? val : jQuery(win).scrollTop()
					);

				} else {
					this[ method ] = val;
				}
			});
		} else {
			win = getWindow( elem );

			// Return the scroll offset
			return win ? ("pageXOffset" in win) ? win[ i ? "pageYOffset" : "pageXOffset" ] :
				jQuery.support.boxModel && win.document.documentElement[ method ] ||
					win.document.body[ method ] :
				elem[ method ];
		}
	};
});

function getWindow( elem ) {
	return jQuery.isWindow( elem ) ?
		elem :
		elem.nodeType === 9 ?
			elem.defaultView || elem.parentWindow :
			false;
}




// Create innerHeight, innerWidth, outerHeight and outerWidth methods
jQuery.each([ "Height", "Width" ], function( i, name ) {

	var type = name.toLowerCase();

	// innerHeight and innerWidth
	jQuery.fn["inner" + name] = function() {
		return this[0] ?
			parseFloat( jQuery.css( this[0], type, "padding" ) ) :
			null;
	};

	// outerHeight and outerWidth
	jQuery.fn["outer" + name] = function( margin ) {
		return this[0] ?
			parseFloat( jQuery.css( this[0], type, margin ? "margin" : "border" ) ) :
			null;
	};

	jQuery.fn[ type ] = function( size ) {
		// Get window width or height
		var elem = this[0];
		if ( !elem ) {
			return size == null ? null : this;
		}

		if ( jQuery.isFunction( size ) ) {
			return this.each(function( i ) {
				var self = jQuery( this );
				self[ type ]( size.call( this, i, self[ type ]() ) );
			});
		}

		if ( jQuery.isWindow( elem ) ) {
			// Everyone else use document.documentElement or document.body depending on Quirks vs Standards mode
			// 3rd condition allows Nokia support, as it supports the docElem prop but not CSS1Compat
			var docElemProp = elem.document.documentElement[ "client" + name ];
			return elem.document.compatMode === "CSS1Compat" && docElemProp ||
				elem.document.body[ "client" + name ] || docElemProp;

		// Get document width or height
		} else if ( elem.nodeType === 9 ) {
			// Either scroll[Width/Height] or offset[Width/Height], whichever is greater
			return Math.max(
				elem.documentElement["client" + name],
				elem.body["scroll" + name], elem.documentElement["scroll" + name],
				elem.body["offset" + name], elem.documentElement["offset" + name]
			);

		// Get or set width or height on the element
		} else if ( size === undefined ) {
			var orig = jQuery.css( elem, type ),
				ret = parseFloat( orig );

			return jQuery.isNaN( ret ) ? orig : ret;

		// Set the width or height on the element (default to pixels if value is unitless)
		} else {
			return this.css( type, typeof size === "string" ? size : size + "px" );
		}
	};

});


window.jQuery = window.$ = jQuery;
})(window);


////
//// jquery.mousewheel.js
//// 

/*! Copyright (c) 2010 Brandon Aaron (http://brandonaaron.net)
 * Licensed under the MIT License (LICENSE.txt).
 *
 * Thanks to: http://adomas.org/javascript-mouse-wheel/ for some pointers.
 * Thanks to: Mathias Bank(http://www.mathias-bank.de) for a scope bug fix.
 * Thanks to: Seamus Leahy for adding deltaX and deltaY
 *
 * Version: 3.0.3
 * 
 * Requires: 1.2.2+
 */

(function($) {

var types = ['DOMMouseScroll', 'mousewheel'];

$.event.special.mousewheel = {
    setup: function() {
        if ( this.addEventListener ) {
            for ( var i=types.length; i; ) {
                this.addEventListener( types[--i], handler, false );
            }
        } else {
            this.onmousewheel = handler;
        }
    },
    
    teardown: function() {
        if ( this.removeEventListener ) {
            for ( var i=types.length; i; ) {
                this.removeEventListener( types[--i], handler, false );
            }
        } else {
            this.onmousewheel = null;
        }
    }
};

$.fn.extend({
    mousewheel: function(fn) {
        return fn ? this.bind("mousewheel", fn) : this.trigger("mousewheel");
    },
    
    unmousewheel: function(fn) {
        return this.unbind("mousewheel", fn);
    }
});


function handler(event) {
    var orgEvent = event, args = [].slice.call( arguments, 1 ), delta = 0, returnValue = true, deltaX = 0, deltaY = 0;
    
    event = $.event.fix(event || window.event);
    event.type = "mousewheel";
    
    // Old school scrollwheel delta
    if ( event.wheelDelta ) { delta = event.wheelDelta/120; }
    if ( event.detail     ) { delta = -event.detail/3; }
    
    // New school multidimensional scroll (touchpads) deltas
    deltaY = delta;
    
    // Gecko
    if ( orgEvent.axis !== undefined && orgEvent.axis === orgEvent.HORIZONTAL_AXIS ) {
        deltaY = 0;
        deltaX = -1*delta;
    }
    
    // Webkit
    if ( orgEvent.wheelDeltaY !== undefined ) { deltaY = orgEvent.wheelDeltaY/120; }
    if ( orgEvent.wheelDeltaX !== undefined ) { deltaX = -1*orgEvent.wheelDeltaX/120; }
    
    // Add event and delta to the front of the arguments
    args.unshift(event, delta, deltaX, deltaY);
    
    return $.event.handle.apply(this, args);
}

})(jQuery);


////
//// jquery.ba-resize.min.js
//// 

/*
 * jQuery resize event - v1.1 - 3/14/2010
 * http://benalman.com/projects/jquery-resize-plugin/
 * 
 * Copyright (c) 2010 "Cowboy" Ben Alman
 * Dual licensed under the MIT and GPL licenses.
 * http://benalman.com/about/license/
 */
(function($,h,c){var a=$([]),e=$.resize=$.extend($.resize,{}),i,k="setTimeout",j="resize",d=j+"-special-event",b="delay",f="throttleWindow";e[b]=250;e[f]=true;$.event.special[j]={setup:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.add(l);$.data(this,d,{w:l.width(),h:l.height()});if(a.length===1){g()}},teardown:function(){if(!e[f]&&this[k]){return false}var l=$(this);a=a.not(l);l.removeData(d);if(!a.length){clearTimeout(i)}},add:function(l){if(!e[f]&&this[k]){return false}var n;function m(s,o,p){var q=$(this),r=$.data(this,d);r.w=o!==c?o:q.width();r.h=p!==c?p:q.height();n.apply(this,arguments)}if($.isFunction(l)){n=l;return m}else{n=l.handler;l.handler=m}}};function g(){i=h[k](function(){a.each(function(){var n=$(this),m=n.width(),l=n.height(),o=$.data(this,d);if(m!==o.w||l!==o.h){n.trigger(j,[o.w=m,o.h=l])}});g()},e[b])}})(jQuery,this);


////
//// CommonMappy.js
//// 

var g_server, g_tileServers;
//var g_staticPath = '../';
var g_protocol = 'http://';
var g_viewModes = {
        "traffic" : {
            "refresh-delay" : "30",
            "stop-refresh-delay" : "14 * 60"
        }
    },
    g_auth = null,
    g_accountName = null;

var g_services = {
    route : "route/get.aspx?"
    , rmm : "route/rmm/get.aspx?"
    , descr : "map/1.0/multi-descr"
    , slab : "map/1.0/slab"
    , loc : "loc/get.aspx?"
};

/**
	@namespace
*/
var MappyApi = {

    /**
     @namespace
     */
    geo : {},
    /**
     @namespace
     */
    geolocation : {},
    /**
     @namespace
     */
    map : {
        /**
         @namespace
         */
        layer : {},
        /**
         @namespace
         */
        shape : {
            /**
             @namespace
             */
            kml : {}
        },
        /**
         @namespace
         */
        tools : {}
    },
    /**
     @namespace
     */
    route : {},
    /**
     @namespace
     */
    types : {},
    /**
     @namespace
     */
    ui : {},
    /**
     @namespace
     */
    utils : {}
};


window.Mappy = {};
Mappy.api = {
    init: function(clientId, token, options) {
        if (!clientId || !token)
            throw 'Missing init parameter';

        options = options || {};

        g_auth = token;
        g_accountName = clientId;
        if (options.ssl) {
            g_protocol = 'https://';
        }

        g_staticPath    = options.staticPath || "../";
        g_domain        = options.domain || g_domain;
        g_server        = "axe." + g_domain + ".net/1v1/";
        g_tileServers   = ["map1." + g_domain + ".net/", "map2." + g_domain + ".net/", "map3." + g_domain + ".net/", "map4." + g_domain + ".net/"];

        // Really ugly.
        MappyApi.ui.Icon.DEFAULT.image  = MappyApi.ui.Icon.DEFAULT.image.replace('undefined', g_staticPath);
        MappyApi.ui.Icon.VOID.image     = MappyApi.ui.Icon.VOID.image.replace('undefined', g_staticPath);

        // make namespaes
        Mappy.api = MappyApi;
    }
};

window.Mappy = Mappy;


var userAgent = navigator.userAgent.toLowerCase();

var g_hasTouchSupport = /iphone/.test(userAgent) || /android/.test(userAgent) || /ipad/.test(userAgent) || /dolfin/.test(userAgent);
var g_isPhone = /iphone/.test(userAgent) || /android/.test(userAgent) || /ipad/.test(userAgent) || /dolfin/.test(userAgent);
var g_hasGestureSupport = /iphone/.test(userAgent) || /ipad/.test(userAgent); // || /dolfin/.test(userAgent);
var g_isMacOS = navigator.platform && navigator.platform.indexOf('Mac') !== -1;


var g_typeVehicle = [];
g_typeVehicle[0] = "none";
g_typeVehicle[1] = "mot";
g_typeVehicle[2] = "comcar";
g_typeVehicle[3] = "midcar";
g_typeVehicle[4] = "sedcar";
g_typeVehicle[5] = "luxcar";
g_typeVehicle[6] = "van";
g_typeVehicle[7] = "coa";
g_typeVehicle[8] = "lt3.5";
g_typeVehicle[9] = "lt12";
g_typeVehicle[10] = "gt12";
g_typeVehicle[11] = "gt12a";
g_typeVehicle[12] = "ped";
g_typeVehicle[13] = "bike";

var g_reloadEnabled = false;


////
//// Mappy.js
//// 

var g_jQuery = jQuery.noConflict(true);g_XYReverseGeocodingRequest
//g_jQuery("head").append('<link rel="stylesheet" type="text/css" href="' + g_staticPath + 'css/api-pack.css"></link>');

// jQuery configuration
g_jQuery.resize.delay = 500;
g_jQuery.resize.throttle = true;

// Environment variable
var g_isIE6 = g_jQuery.browser.msie && g_jQuery.browser.version < 7;


////
//// utils.js
//// 

/**
 * Copy all properties of a source object to a destination object.  Modifies
 *     the passed in destination object.  Any properties on the source object
 *     that are set to undefined will not be (re)set on the destination object.
 *
 * @param {Object} destination The object that will be modified
 * @param {Object} source The object with properties to be set on the destination
 * @returns {Object} The destination object.
 */
var g_extend = function (destination, source)
{
    destination = destination || {};
    if (source) {
        for (var property in source)
        {
            var value = source[property];
            if (value !== undefined)
            {
                destination[property] = value;
            }
        }

        /**
         * IE doesn't include the toString property when iterating over an object's
         * properties with the for(property in object) syntax.  Explicitly check if
         * the source has its own toString property.
         */

        /*
         * FF/Windows < 2.0.0.13 reports "Illegal operation on WrappedNative
         * prototype object" when calling hawOwnProperty if the source object
         * is an instance of window.Event.
         */

        var sourceIsEvt = typeof window.Event === "function" && source instanceof window.Event;

        if (!sourceIsEvt && source.hasOwnProperty && source.hasOwnProperty('toString'))
        {
            destination.toString = source.toString;
        }
    }
    return destination;
}
/**
 * Base class used to construct all other classes. Includes support for multiple inheritance.
 */
var g_Class = MappyApi.utils.Class = function ()
{
    var Class = function ()
    {
        this.initialize.apply(this, arguments);
    };
    var extended = {};
    var parent, initialize;
    for (var i = 0, len = arguments.length; i < len; i += 1)
    {
        if (typeof arguments[i] === "function")
        {
            // make the class passed as the first argument the superclass
            if (i === 0 && len > 1)
            {
                // replace the initialize method with an empty function,
                // because we do not want to create a real instance here
                initialize = arguments[i].prototype.initialize;
                arguments[i].prototype.initialize = function () {};
                // the line below makes sure that the new class has a
                // superclass
                extended = new arguments[i];
                // restore the original initialize method
                arguments[i].prototype.initialize = initialize;
            }
            // get the prototype of the superclass
            parent = arguments[i].prototype;
        }
        else
        {
            // in this case we're extending with the prototype
            parent = arguments[i];
        }
        g_extend(extended, parent);
    }
    Class.prototype = extended;
    return Class;
};

/**
    Check if not undefined or null
    @param {Object} obj
*/
var g_isDefined = function (obj)
{
    return (typeof obj !== "undefined" && obj !==  null);
}
    /**
        Check if undefined or null
        @param {Object} obj
    */
var g_isNotDefined = function (obj)
{
    return (typeof obj === "undefined" || obj ===  null);
}



/**
 * Retrieves the sub value at the provided path,
 * from the value object provided.
 *
 * Based on YUI Object.getValue()
 * 
 * @method getValue
 * @param o The object from which to extract the property value
 * @param path {Array} A path array, specifying the object traversal path
 * from which to obtain the sub value.
 * @return {Any} The value stored in the path, undefined if not found,
 * undefined if the source is not an object.  Returns the source object 
 * if an empty path is provided.
 */
var g_getPathValue = function (o, path)
{
	if (typeof o !== 'object') {
		return undefined;
	}

	var i,
		p = g_jQuery.makeArray(path), 
		l = p.length;

	for (i=0; g_isDefined(o) && i < l; i++) {
		o = o[p[i]];
	}

	return o;
};

/**
 * Test if a subproperty of an object is defined
 *
 * Don't do this anymore : 
 * if (poi &&
 *	poi.extras &&
 *	poi.extras.wreport &&
 *	poi.extras.wreport.fc &&
 *	poi.extras.wreport.fc[0] &&
 *	poi.extras.wreport.fc[0].times)
 *
 * Do this :
 * if (g_isPathDefined(poi, 'extras.wreport.fc[0].times'))
 *
 * @see g_getPathValue
 */
var g_isPathDefined = function(o, path)
{
	if (typeof path === 'string') {
		path =
			path.replace(/[\[]/g, '.') //Changing array synthax to object
				.replace(/[\'\"\]]/g, '') //Removing non necessary characters
				.split('.');
	}
	return g_isDefined(g_getPathValue(o, path));
};

/**
    Apply a context to a function.
    @param {Function} func
    @param {Object} context The context (this).
    @returns {function} The function with the context.
*/
var g_makeCaller = function (func, context)
{
    return function ()
    {
        func.apply(context, arguments);
    };
}
/**
    Execute the given function with a small delay (13 ms)
    @param {Function} func
    @param {Object} context The context (this).
*/
var g_executeDelayed = function(func, context)
{
    return setTimeout(g_makeCaller(func, context), 13);
}

/**
    Transform a parameter object in a string.
    @param {Object} params
    @param {String} parentString The parent string for recursivity.
    @returns {String} A string.
*/
var g_parametersToString = function (params, parentString)
{
    var str = [];
    for (var i in params)
    {
        if (params.hasOwnProperty(i))
        {
            if (typeof params[i] === "number" || typeof params[i] === "string")
            {
                str.push(parentString + '.' + i + '=' + params[i]);
            }
            else
            {
                str.push(g_parametersToString(params[i], parentString + '.' + i));
            }
        }
    }
    return str.join('&');
}
/**
    Transform a XML object in json.
    @param {Object} node The response from XHR for example.
    @returns {object} The json object.
*/
var g_xml2json = MappyApi.utils.xml2json = function (node)
{
    var data = '', object = {}, isData = true, i;

    if (g_isDefined(node.attributes) && node.attributes.length > 0)
    {
        object["@attributes"] = {};
        for (i = 0; i < node.attributes.length; i += 1)
        {
            object["@attributes"][node.attributes[i].nodeName] = node.attributes[i].value;
        }
    }

    if (g_isDefined(node.childNodes))
    {
        for (i = 0; i < node.childNodes.length; i += 1)
        {
            // Check if data
            if (g_isDefined(node.childNodes[i].data))
            {
                data += node.childNodes[i].data;
            }
            // check if nodeName already exists
            else if (g_isDefined(object[node.childNodes[i].nodeName]))
            {
                isData = false;

                if (typeof object[node.childNodes[i].nodeName] === "string")
                {
                    object[node.childNodes[i].nodeName] = [object[node.childNodes[i].nodeName]];
                }
                else
                {
                    object[node.childNodes[i].nodeName] = g_jQuery.makeArray(object[node.childNodes[i].nodeName]);
                }

                object[node.childNodes[i].nodeName].push(g_xml2json(node.childNodes[i]));
            }
            else
            {
                isData = false;
                object[node.childNodes[i].nodeName] = g_xml2json(node.childNodes[i]);
            }
        }

        if (isData)
        {
            if (g_isDefined(object["@attributes"]) && data === '')
            {
                return object;
            }
            return data;
        }
        else
        {
            return object;
        }
    }
    
    return object;

};

var g_fillTemplate = function (template, data)
{
    return template.replace(
        /{([^{}]*)}/g,
        function (a, b)
        {
            var r = data[b];
            return typeof r === 'string' || typeof r === 'number' ? r : a;
        }
    );
}

var g_isEmpty = function (obj)
{
    for (var test in obj)
    {
        if (obj.hasOwnProperty(test))
        {
            return false;
        }
    }
    return true;
}

/**
    Return an array of object. This function is useful to handle an object before use it in a loop.
    If the object is an array, it's return as it is. If it's an object, this function returns an array with 1 item which is the object first passed. If the param is null, it returns an empty array.
    @param {object} object The object
    @returns {Array} An array.
*/
var g_getAsArray = MappyApi.utils.getAsArray = function(object)
{
    return g_isDefined(object) ? g_jQuery.isArray(object) ? object : [object] : [];
}

/**
    Return the instance of jQuery used by this API. Useful to avoid including twice this library in your website.
    @returns {Object} JQuery Instance
*/
var g_getJQuery = MappyApi.utils.getJQueryInstance = function()
{
    return g_jQuery;
}

var g_floor = Math.floor;

var g_ucfirst = function (str)
{
    return str.charAt(0).toUpperCase() + str.slice(1);
}


////
//// EventListener.js
//// 

var g_EventListener = g_Class(/** @lends EventListener.prototype */{
    /**
        @constructs
    */
    initialize : function (name, handler, priorityLevel)
    {
        this.name = name;
        this.handler = handler;
        this.priorityLevel = priorityLevel || 0;
        this._stopPropatation = false;
    },
    stopPropagation : function ()
    {
        this._stopPropatation = true;
    },
    propagationStopped : function ()
    {
        if (this._stopPropatation === true)
        {
            this._stopPropatation = false;
            return true;
        }
        else
        {
            return false;
        }
    }
});


////
//// EventSource.js
//// 

var g_EventSource = g_Class(/** @lends EventSource.prototype */{
    /**
    List of event name this class can trigger.
    @type String[]
    */
    EVENTS : null,
    /**
    Does this class have listeners?
    @type boolean
    */
    hasListeners : false,
    /**
        @constructs
    */
    initialize : function ()
    {
        this._listeners = {};
        for (var i = 0; i < this.EVENTS.length; i += 1)
        {
            this._listeners[this.EVENTS[i]] = [];
        }
    },
    /**
    @param {String} name Name of the event you want to listen. Must be in EVENTS.
    @param {Function} handler A function that will be executed when the event is triggered.
    @param {Number} priorityLevel (optionnal) change the normal priority of the event.
    @return {EventListener} The event listener.
    */
    addListener : function (name, handler, priorityLevel)
    {
        if (g_jQuery.isArray(this._listeners[name]))
        {
            var eventListener = new g_EventListener(name, handler, priorityLevel);
            this._listeners[name].push(eventListener);
            this._arraySort(this._listeners[name]);
            this.hasListeners = true;
            return eventListener;
        }
        else
        {
            return false;
        }
    },
    /**
    @param {EventListener} eventListener Remove the given listener.
    */
    removeListener : function (eventListener)
    {
        var listeners = this._listeners;
        listeners[eventListener.name] || (listeners[eventListener.name] = []);
        var index = g_jQuery.inArray(eventListener, listeners[eventListener.name]);
        if (index !== -1)
        {
            delete listeners[eventListener.name][index];
        }

        for (var i in listeners)
        {
            if (listeners.hasOwnProperty(i) && listeners[i].length > 0)
            {
                this.hasListeners = true;
                return;
            }
        }
        this.hasListeners = false;
    },
    /**
        Remove all listeners attached to the object
    */
    clearListeners : function()
    {
        this._listeners = {};
        for (var i = 0; i < this.EVENTS.length; i += 1)
        {
            this._listeners[this.EVENTS[i]] = [];
        }

        this.hasListeners = false;
    },
    /**
    @param {String} name Trigger the given event name.
    */
    trigger : function (name)
    {
        var listeners = this._listeners[name];
        if (g_jQuery.isArray(listeners))
        {
            var args = g_jQuery.makeArray(arguments).slice(1);
            /*
             * Save length to avoid to execute another (wrong) listener.
             */
            for (var i = 0, l = listeners.length; i < l; i += 1)
            {
                if (typeof listeners[i] !== "undefined")
                {
                    listeners[i].handler.apply(listeners[i], args);
                    if (g_isDefined(listeners[i]) && listeners[i].propagationStopped() === true)
                    {
                        break;
                    }
                }
            }
            this._listeners[name] = this._arrayFilter(listeners);
        }
    },
    /**
    @private
    */
    _arrayFilter : function (array)
    {
        var ret = [];
        for (var i = 0; i < array.length; i += 1)
        {
            if (typeof array[i] !== "undefined")
            {
                ret.push(array[i]);
            }
        }
        return ret;
    },
    /**
    @private
    */
    _arraySort : function (array)
    {
        array.sort(function (el1, el2)
        {
            if (typeof el1 !== "undefined" && typeof el2 !== "undefined")
            {
                return el2.priorityLevel - el1.priorityLevel;
            }
            return 0;
        });
    }
});


////
//// TaskQueue.js
//// 

var g_TaskQueue = g_Class(g_EventSource, /** @lends TaskQueue.prototype */{
    EVENTS : ["empty"],
    size : 0,    
    _param : null,
    /**
        @constructs
        @private
        @agments EventSource
    */
    initialize : function ()
    {
        g_EventSource.prototype.initialize.apply(this);
    },
    setParam : function (param)
    {
        this._param = param;
    },
    addTask : function ()
    {
        this.size += 1;
    },
    removeTask : function ()
    {
        this.size -= 1;
        if (this.size <= 0)
        {
            this.size = 0;
            this.trigger("empty", this._param);
            this._param = null;
        }
    }
});


////
//// Point.js
//// 

/**
@constructor
@param {Number} x left
@param {Number} y top
*/
MappyApi.types.Point = function (x, y)
{
	x -= 0;
	y -= 0;
	this.x = x;
	this.y = y;
}

var g_Point = MappyApi.types.Point;

g_Point.prototype = /** @lends Mappy.api.types.Point.prototype */{
	/**
    @return {boolean} Return true if point is (0,0).
    */
    isNull : function ()
    {
        return (this.x === 0 && this.y === 0);
    },
    /**
    @return {Number} Returns the hypothenus
    */
    hypot : function ()
    {
        return Math.sqrt(this.x * this.x + this.y * this.y);
    },
    /**
    @param {Mappy.api.types.Point} pt
    @return {Number} Returns distance in pixel with the given point.
    */
    dist : function (pt)
    {
        var dx = this.x - pt.x;
        var dy = this.y - pt.y;
        return Math.sqrt(dx * dx + dy * dy);
    },
    /**
    Parse to int.
    */
    round : function ()
    {
        this.x = Math.round(this.x);
        this.y = Math.round(this.y);
    },
    /**
    Clone the object and return a new instance.
    @return {Mappy.api.types.Point}
    */
    clone : function ()
    {
        return new g_Point(this.x, this.y);
    }
};


////
//// Size.js
//// 

var g_Size = MappyApi.types.Size = g_Class(/** @lends Mappy.api.types.Size.prototype */{
    /**
        @constructs
        @param {Number} width width
        @param {Number} height height
    */
    initialize : function (width, height)
    {
        width -= 0;
        height -= 0;
        this.width = width;
        this.height = height;
    },
    /**
    @return {Mappy.api.types.Point} Position of the center of the area.
    */
    getCenter : function ()
    {
        return new g_Point(Math.floor(this.width / 2), Math.floor(this.height / 2));
    },
    /**
    @return {Number} Returns the hypothenus
    */
    hypot : function ()
    {
        return Math.sqrt(this.width * this.width + this.height * this.height);
    },
   /**
    @return {boolean} Return true if the area if the size is 0.
    */
    isNull : function ()
    {
        return (this.width === 0 || this.height === 0);
    },
    /**
    Clone the object and return a new instance.
    @return {Mappy.api.types.Size}
    */
    clone : function ()
    {
        return new g_Size(this.width, this.height);
    }
});


////
//// Bounds.js
//// 

var g_Bounds = MappyApi.types.Bounds = g_Class(/** @lends Mappy.api.types.Bounds.prototype */{
    /**
    @type Number
    */
    minX : null,
    /**
    @type Number
    */
    minY : null,
    /**
    @type Number
    */
    maxX : null,
    /**
    @type Number
    */
    maxY : null,
    /**
        @constructs
        @param {Number} minX 
        @param {Number} minY 
        @param {Number} maxX 
        @param {Number} maxY 
    */
    initialize : function (minX, minY, maxX, maxY)
    {
        this.minX = minX;
        this.minY = minY;
        this.maxX = maxX;
        this.maxY = maxY;
    },
    /**
        Add a point to the bounds to extend it.
        @param {Mappy.api.types.Point} pt
    */
    addPoint : function (pt)
    {
        if (pt.x < this.minX)
        {
            this.minX = pt.x;
        }
        if (pt.x > this.maxX)
        {
            this.maxX = pt.x;
        }
        if (pt.y < this.minY)
        {
            this.minY = pt.y;
        }
        if (pt.y > this.maxY)
        {
            this.maxY = pt.y;
        }
    },
    /**
    @return {Mappy.api.types.Size} Returns the size of the bounds.
    */
    getSize : function ()
    {
        return new g_Size(this.maxX - this.minX, this.maxY - this.minY);
    },
    /**
    @param {Mappy.api.types.Point} pt
    @return {boolean} Is the point in the bounds?
    */
    inside : function (pt)
    {
        return (pt.x >= this.minX && pt.x < this.maxX &&
                pt.y >= this.minY && pt.y < this.maxY);
    },
    /**
    Returns the intersection of two bounds.
    @param {Mappy.api.types.Bounds} bounds
    @return {Mappy.api.types.Bounds}
    */
    intersect : function (bounds)
    {
        var minX = Math.max(this.minX, bounds.minX);        
        var minY = Math.max(this.minY, bounds.minY);
        var maxX = Math.min(this.maxX, bounds.maxX);        
        var maxY = Math.min(this.maxY, bounds.maxY);
        
        if (maxX - minX < 0 || maxY - minY < 0)
        {
            return null;
        }
        else
        {
            return new g_Bounds(minX, minY, maxX, maxY);
        }
    }
    
});


////
//// ui.js
//// 

function g_draggable(o, options)
{
    options = options || {};

    if (g_isNotDefined(o))
    {
        return;
    }

    if (g_isNotDefined(options.handle))
    {
        options.handle = o;
    }

    var mouseDownEvent;
    var init, mouseDown, mouseMove, mouseUp;
    var moved = false, started=false, needToReleased=false;
    
    if (g_hasTouchSupport === false)
    {
		// Triggered after mousedown & mouseup.
        options.handle.click(function (e)
        {
            if (moved)
            {
                e.stopImmediatePropagation();
                moved = false;
            }
        });
        
        mouseUp = function (e)
        {   g_jQuery(document).unbind('mousemove', mouseMove);
            g_jQuery(document).unbind('mouseup', mouseUp);
            started = false;
            if (!mouseDownEvent)
            {
                o.css('cursor', 'url('+_DATA.mappy_images+'/img/cursor/openhand.cur), default');

                if (typeof options.stop === "function")
                {
                    options.stop(e);
                }
            }
            e.stopPropagation();
            e.cancelBubble=true;
            return false;
        };

        mouseMove = function (e)
        {
            if (mouseDownEvent)
            {
                if (typeof options.start === "function")
                {
                    options.start(mouseDownEvent);
                }

                var p = o.position();
                init = {
                    left : p.left - mouseDownEvent.clientX,
                    top : p.top - mouseDownEvent.clientY
                };

                o.css('cursor', 'url(' + g_staticPath + 'img/cursor/closedhand.cur), move');

                mouseDownEvent = null;
                moved = true;
                started = false;
            }

            o.css({
                top: init.top + e.clientY,
                left: init.left + e.clientX
            });

            if (typeof options.drag === "function")
            {
                options.drag(e);
            }

            e.stopPropagation();
            e.cancelBubble=true;
            return false;
        };

        o.hackModifyInit = function (x, y)
        {
            init.left -= x;
            init.top -= y;
        };
        
        mouseDown = function (e)
        {
            if (started) return;
            started = true;
            mouseDownEvent = e;
            g_jQuery(document).mousemove(mouseMove);
            g_jQuery(document).mouseup(mouseUp);
            e.stopPropagation();
            e.cancelBubble=true;
            return false;
        };
        o.css('position', 'absolute');
        o.css('cursor', 'url(' + g_staticPath + 'img/cursor/openhand.cur), default');

        options.handle.bind("mousedown", mouseDown);
        options.handle.bind("dragstart", function(e){
            e.stopPropagation();
            e.cancelBubble=true;
            return false;
        });

        o.disableDraggable = function ()
        {
            o.css('cursor', '');
            options.handle.unbind("mousedown", mouseDown);
            

        };

        o.enableDraggable = function ()
        {
            o.css('cursor', 'url(' + g_staticPath + 'img/cursor/openhand.cur), default');
            options.handle.unbind("mousedown", mouseDown);
            options.handle.bind("mousedown", mouseDown);
        };
    }
    else
    {
        options.handle.click(function (e)
        {
            if (!mouseDownEvent)
            {
                e.stopImmediatePropagation();
            }
        });
        
        mouseUp = function (e)
        {
            document.removeEventListener('touchmove', mouseMove);
            document.removeEventListener('touchend', mouseUp);

            if (!mouseDownEvent)
            {
                if (typeof options.stop === "function")
                {
                    e = e.touches[0];
                    options.stop(e);
                }
            }
        };

        mouseMove = function (e)
        {
            if (e.touches.length === 1)
            {
                e = e.touches[0];
                if (mouseDownEvent)
                {
                    if (typeof options.start === "function")
                    {
                        options.start(mouseDownEvent);
                    }

                    var p = o.position();
                    init = {
                        left : p.left - mouseDownEvent.clientX,
                        top : p.top - mouseDownEvent.clientY
                    };

                    mouseDownEvent = null;
                }

                o.css({
                    top: init.top + e.clientY,
                    left: init.left + e.clientX
                });
				
                if (typeof options.drag === "function")
                {
                    options.drag(e);
                }
                
                return false;
            }
            else
            {
                e = e.touches[0];
                mouseUp(e);
            }
        };

        mouseDown = function (e)
        {
            if (e.touches.length === 1)
            {
                e.preventDefault();
                e = e.touches[0];
                mouseDownEvent = e;
                document.addEventListener('touchmove', mouseMove);
                document.addEventListener('touchend', mouseUp);
                return false;        
            }
        };
        
        o.css('position', 'absolute');

        options.handle[0].addEventListener('touchstart', mouseDown);

        o.disableDraggable = function ()
        {
            options.handle[0].removeEventListener('touchstart', mouseDown);
        };

        o.enableDraggable = function ()
        {
            options.handle[0].addEventListener('touchstart', mouseDown);
        };
    }
};



function g_loadImage(url, element, handler, error)
{
    var img = new Image();

    /*
        Take care of IE! Memory leak spotted.
    */
    g_jQuery(img).load(function ()
    {
        element.src = url;
        g_jQuery(img).unbind();
        return handler(element);
    });

    if (g_isDefined(error))
    {
        g_jQuery(img).error(error);
    }
    img.src = url;
}

function g_getSize(html, outer)
{
    var tmpHtml = g_jQuery(html).clone();
    tmpHtml.css({
        visibility : "hidden",
        position: "absolute",
        left : 0,
        top : 0
    });
    g_jQuery(document.body).append(tmpHtml);
    var size = (outer) ? new g_Size(tmpHtml.outerWidth(true), tmpHtml.outerHeight(true)) : new g_Size(tmpHtml.width(), tmpHtml.height());
    tmpHtml.remove();
    return size;
}

function g_preventDefault(event)
{
    event.preventDefault();
}

var g_getHtmlTownSign = MappyApi.ui.getHtmlTownSign = function (html)
{
    return '<div class="town-sign"><span>' + html + '</span></div>';
};

function g_timeToString(seconds)
{
    var str = "";
    seconds = seconds - 0;

    var days = Math.floor(seconds / (24 * 3600));
    seconds %= 24 * 3600;
    var hours = Math.floor(seconds / 3600);
    seconds %= 3600;
    var minutes = Math.floor(seconds / 60);
    seconds %= 60;

    if (days === 1) {
        str += "1 jour ";
    } else if (days > 1) {
        str += days + " jours ";
    }
    str += hours + "h" + (minutes < 10 ? "0" : "") + minutes;
    return str;
}

function g_distanceToString (distance, unit)
{
	// From Mappy.website.utils.distanceToString
    var str = '';

    // in miles
    if ( unit && unit === 'miles' )
    {
        str = (parseFloat(distance, 10) * 0.621371192 / 1000).toFixed(1) + ' mi';
    }
    // in km
    else
    {
        var km  = Math.floor(distance / 1000);

        if (km > 9)
        {
            // get by 100 meters for distances up to 10km, otherwise simply print km
			str = Math.round(distance / 1000) + " km";
			
		}
        // 1km or more
		else if (km > 0)
        {
			var km = Math.floor(distance / 1000);
			
			// In hundreds of meters.
            var m = Math.round((distance % 1000) / 100);
			if(m > 9)
			{
				var nbTenths = Math.floor(m / 10);
				m -= (nbTenths * 10);
				km += nbTenths;
			}
			
			// Formatting string
			str = km;
			if(m > 0)
			{
				str += ',' + m;
			}
			str += ' km';
			
        }
        // less than 1km
        else
        {
			m = Math.round(distance % 1000);
            str = m + ' m';
        }
    }

    return str;
};


////
//// Icon.js
//// 

var g_Icon = MappyApi.ui.Icon = g_Class(/** @lends Mappy.api.ui.Icon.prototype */{
    /**
        Name of the css class.
        @type String
    */
    cssClass : null,
    /**
        Url of the image.
        @type String
    */
    image : null,
    /**
        Size of the icon.
        @type Mappy.api.types.Size
    */
    size : null,
    /**
        Shift in pixel to define the anchor in the icon
        @type Mappy.api.types.Point
    */
    iconAnchor : null,
    /**
        Shift in pixel to define the anchor of the popup in the icon
        @type Mappy.api.types.Point
    */
    popUpAnchor : null,
    /**
        Label of the icon.
        @type String
    */
    label : null,
    /**
        @constructs
        @param {Object} options containing properties of the icon :<br/>
        - cssClass : {string} Name of the css class.<br/>
        - image : {string} Url of the image.<br/>
        - size : {Mappy.api.types.Size} Size of the icon.<br/>
        - iconAnchor : {Mappy.api.types.Point} Shift in pixel to define the anchor in the icon<br/>
        - popUpAnchor : {Mappy.api.types.Point} Shift in pixel to define the anchor of the popup in the icon<br/>
        - label : {string} Label of the icon.
    */
    initialize : function (options)
    {
        this.cssClass = options.cssClass;
        this.image = options.image;
        this.size = options.size;
        this.iconAnchor = options.iconAnchor;
        this.popUpAnchor = options.popUpAnchor;
        this.label = options.label;

        if (g_isNotDefined(this.size))
        {
            this._setSize();
        }

        if (g_isNotDefined(this.iconAnchor))
        {
            this.iconAnchor = this.size.getCenter();
        }

        if (g_isNotDefined(this.popUpAnchor))
        {
            this.popUpAnchor = new g_Point(this.iconAnchor.x, 0);
        }
    },
    /**
        @private
    */
    _setSize : function ()
    {
        var size;

        if (g_isDefined(this.cssClass))
        {
            size = g_getSize('<div class="' + this.cssClass + '"></div>');
        }

        if (g_isNotDefined(size) || (size.isNull() && g_isDefined(this.image)))
        {
            size = g_getSize('<img src="' + this.image + '"></img>');
        }
        
        this.size = size;
    },
    /**
        @private
    */
    create : function (baseNode)
    {
        var div = (baseNode? g_jQuery(baseNode) :g_jQuery('<div></div>'));

        if (g_isDefined(this.cssClass))
        {
            div.addClass(this.cssClass);
        }
        div.html(this.label);

        if (g_isDefined(this.image))
        {
            div.append('<img style="position:absolute;left:0;top:0;z-index:-1;" src="' + this.image + '"></img>');
        }
        return div;
    }
});

MappyApi.ui.Icon.DEFAULT = {
    cssClass : "default-icon",
    image :  "../images/img/poi/POI_defaut.png",
    size : new g_Size(21, 31),
    iconAnchor : new g_Point(10, 31),
    popUpAnchor : new g_Point(10, 0)
};

MappyApi.ui.Icon.VOID = {
    cssClass : "void-icon",
    image :"../images/img/icon-void.png",
    size : new g_Size(21, 31),
    iconAnchor : new g_Point(10, 31),
    popUpAnchor : new g_Point(10, 0)
};


////
//// Slider.js
//// 

var g_Slider = MappyApi.ui.Slider = g_Class(/** @lends Mappy.api.ui.Slider.prototype */{
    /**
        @constructs
        @private
    */
    initialize : function (options)
    {
        options = options || {};
        
        if (g_isNotDefined(options.container))
        {
            return;
        }
        
        var container = g_jQuery(options.container);
        
        var subContainer = g_jQuery('<div class="slider"></div>');
        container.append(subContainer);
        
        var stopCallback = options.stop;
        
        this.min = options.min;
        this.max = options.max;                
        this.steps = this.max - this.min;
        this.size = container.height();
        this.domEventHandlers = [];
        
        var handler = g_jQuery('<a class="slider-handler" href="#"/>');
        this._handler = handler;
        
        var that = this;
        handler.click(function (event)
        {
            g_preventDefault(event);
        });
        
        handler.mousedown(function (event)
        {
            var startDragPosY = event.pageY;
            var initPos = handler.position().top + handler.height();
            var doc = g_jQuery(document);
            
            that._mousemoveHandler = function (event)
            {
                var step = that._stepCalc(initPos + event.pageY - startDragPosY);
                that._setStep(step);
                g_preventDefault(event);
            };
            
            that._mouseupHandler = function ()
            {
                doc.unbind('mousemove', that._mousemoveHandler);
                doc.unbind('mouseup', that._mouseupHandler);
                if (g_jQuery.isFunction(stopCallback))
                {
                    stopCallback(that._currentStep + that.min);
                }
            };
            
            
            doc.mousemove(that._mousemoveHandler);
            doc.mouseup(that._mouseupHandler);
            
            g_preventDefault(event);
            event.stopPropagation();
        });
        
        subContainer.append(handler);
        
        this._mousedownHandler = function (event)
        {
            var offset = container.offset();
            var step = that._stepCalc(event.pageY - offset.top);
            that._setStep(step);
            
            if (g_jQuery.isFunction(stopCallback))
            {
                stopCallback(that._currentStep + that.min);
            }
        };
        container.mousedown(this._mousedownHandler);
        
        this.div = subContainer;
        this.container = container;
    },
    _stepCalc : function (position)
    {
        var step = Math.round((this.size - position) * this.steps / this.size);
        
        if (step < 0)
        {
            step = 0;
        }
        if (step > this.steps)
        {
            step = this.steps;
        }
        return step;
    },        
    _setStep : function (step)
    {
        this._handler.css({
            bottom: ((step * 100) / this.steps) + '%'
        });
        this._currentStep = step;
    },
    setValue : function (value)
    {
        value = Math.round(value);
        if (value < this.min)
        {
            value = this.min;
        }
        if (value > this.max)
        {
            value = this.max;
        }        
        this._setStep(value - this.min);
    },
    destroy : function ()
    {
        var doc = g_jQuery(document);
        if (g_jQuery.isFunction(this._mousemoveHandler))
        {
            doc.unbind('mousemove', this._mousemoveHandler);
        }
        if (g_jQuery.isFunction(this._mouseupHandler))
        {
            doc.unbind('mouseup', this._mouseupHandler);
        }
        
        this.container.unbind('mousedown', this._mousedownHandler);
        
        this.div.remove();
    }
});


////
//// ToolTip.js
//// 

var g_ToolTip = MappyApi.ui.ToolTip = g_Class(/** @lends Mappy.api.ui.ToolTip.prototype */{
    div : null,
    isAdded : false,
    TEMPLATE : '<div class="default-tooltip" style="z-index:1000;"></div>',
    /**
        @constructs
        @private
    */
    initialize : function (container, data)
    {
        this.container = container;
        this.data = data;
    },
    add : function (event)
    {
        var container = this.container;
        
        this.remove();
        div = g_jQuery(this.TEMPLATE);
        div.html(this.data);
        container.append(div);
        
        this.div = div;

        var divWidth = div.outerWidth(true);
        var divHeight = div.outerHeight(true);
            
        var container = this.container;
        function mouseMoveHandler(event)
        {
            var mapContainer = container.attr('name') === 'geolayer' ? container.parent() : container;
            var containerPos = container.offset();
            
            var pos = {
                left : event.pageX - containerPos.left,
                top : event.pageY - containerPos.top + 20,
                position : 'absolute',
                width: divWidth
            };
            
            var containerWidth = mapContainer.width();
            var containerHeight = mapContainer.height();
            var containerOffset = mapContainer.offset();
            
            if (event.pageX + divWidth - containerOffset.left > containerWidth)
            {
                pos.left = pos.left - divWidth;
            }
            if (event.pageY + 50 - containerOffset.top > containerHeight)
            {
                pos.top -= 50;
            }
            
            div.css(pos);
        }
        
        
        if (event.type != 'click' && event.type != 'touchend')
        {
            g_jQuery(document).mousemove(mouseMoveHandler);
        }
                
        mouseMoveHandler(event);
        this._mouseMoveHandler = mouseMoveHandler;
        this.isAdded = true;
    },
    remove : function ()
    {
        if (this.isAdded)
        {
            g_jQuery(document).unbind("mousemove", this._mouseMoveHandler);
            this.div.remove();
            delete this.div;
            this.isAdded = false;
        }
    }
});


////
//// TcToolTip.js
//// 

/**
 * A tooltip class dedicated to "Transports en Commun" hover display
 * @class Mappy.api.ui.TcToolTip
 */

var g_TcToolTip = MappyApi.ui.TcToolTip = g_Class(g_ToolTip, /** @lends Mappy.api.ui.ToolTip.prototype */{
    
    // --------------------------------------- private props
    
    /**
     * @private
     * @type Integer
     */ 
    _nbLinesTypes: 0 ,
    
    // --------------------------------------- public methods    
    
    initialize : function (container, data)
    {
        
        if( g_TcToolTip._debug )
            window.console && window.console.log && console.log('new Mappy.api.ui.TcToolTip() ; data=', data );
        
        g_ToolTip.prototype.initialize.call(this, container, data );
        
        this._initTcLabel();
        
    },
    
    // Uncomment this empty function overriding for easier debug... :-)
    /*
    remove : function ()
    {
        
    },
    */
    
    
    // --------------------------------------- private methods    
    
    /**
     * @private
     */ 
    _initTcLabel: function()
    {
        
        // No description ? No problem !
        if( ! this.data.properties.description.line )
        {
            this.data = this.data.properties.description.label;
            return;
        }
        
        
        // Misc setup
        var i, j, currentLineData;
        
        var linesArray = this.data.properties.description.line;
        if( ! g_jQuery.isArray(linesArray) )
            linesArray = [ linesArray ];//1 line = no Array :-(
        
        
        // We are going to group the lines data by type
        var linesByType = {
            M:          [],     //subways
            S:          [],     //suburban trains (RER)
            T:          [],     //trains
            TY:         []      //tramways
        };


        // How many lines types do we display ?
        var linesTypesToDisplay = [];
        for( i=0, j=linesArray.length; i<j; i++ )
        {
            
            currentLineData = linesArray[i];
            
            if( g_jQuery.inArray( currentLineData.type, linesTypesToDisplay ) === -1 )
                linesTypesToDisplay.push( currentLineData.type );
            
        }
        this._nbLinesTypes = linesTypesToDisplay.length;
            
            
        // Lines display build...
        for( i=0, j=linesArray.length; i<j; i++ )
        {
            
            currentLineData = linesArray[i];
            
            if( g_jQuery.inArray( currentLineData.type, linesTypesToDisplay ) === -1 )
                continue;
            
            linesByType[ currentLineData.type ].push( this._renderLine( currentLineData ) );
            
        }


        // Lines blocks display build...
        var linesTypesHtmlArray = [];
        for( i=0, j=linesTypesToDisplay.length; i<j; i++ )
        {
            
            var currentLineType = linesTypesToDisplay[i];
            linesTypesHtmlArray.push( 
                this._renderLinesBlock( currentLineType, linesByType[currentLineType] )
            );
            
        }
        
        
        // Let's populate all this for a nice tooltip ! ^_^
        var lineNameCssClasses = [ 'line-name' ];
        lineNameCssClasses.push( (this._nbLinesTypes > 1) ? 'multi-line-type' : 'single-line-type');
        
        var tooltipHtml = '<span class="tc-tooltip">';
        tooltipHtml += '<span class="' + lineNameCssClasses.join(' ') + '">' + this.data.properties.description.label + '</span>';
        tooltipHtml += linesTypesHtmlArray.join('');
        tooltipHtml += '<span class="clearfix"></span>';
        tooltipHtml += '</span>';
        
        
        // The Tooltip API retrieves our "data" property for diplay
        this.data = tooltipHtml;
        
    },
    

    /**
     * @private
     * @param {Object} lineData
     */ 
    _renderLine: function( /**Object*/ lineData )
    {

      var cssClasses = [ 'roadbook-mm' ];

      switch( lineData.type )
      {
          case 'M':
              cssClasses.push('roadbook-mm-line', 'roadbook-mm-line-metro_' + lineData.num );
              break;
          case 'S':
              cssClasses.push('roadbook-mm-line', 'roadbook-mm-line-rer_' + lineData.num.toLowerCase() );
              break;
          case 'T':
              cssClasses.push('roadbook-mm-line', 'roadbook-mm-line-train_' + lineData.num.toLowerCase() );
              break;
          case 'TY':
              cssClasses.push('roadbook-mm-line', 'roadbook-mm-line-t' + lineData.num );
              break;
      }

      // Html main content
      var html = '';
      html += '<li class="' + cssClasses.join(' ') + '">';
      html += '<span class="num">' + lineData.num + '</span> ';
      html += '</li>';

      return html;

    },
    

    /**
     * @private
     * @param {String} linesType
     * @param {Array} linesDisplay
     * @return String
     */ 
    _renderLinesBlock: function( /**String*/ linesType, /**Array*/ linesDisplay )
    {

      if( linesDisplay.length === 0 )
          return '';//no lines = no rendering


      var cssClasses = [ 'lines', 'roadbook-mm' ];

      switch( linesType )
      {
          case 'M':
              cssClasses.push('roadbook-mm-metro' );
              break;
          case 'S':
              cssClasses.push('roadbook-mm-rer' );
              break;
          case 'T':
              cssClasses.push('roadbook-mm-train' );
              break;
          case 'TY':
              cssClasses.push('roadbook-mm-tram' );
              break;
      }

      var html = '';
      html += '<ul class="lines ' + ( (this._nbLinesTypes > 1) ? 'multi' : 'single' ) + '">';
      html += ' <li class="lines-type first ' + cssClasses.join(' ') + '"></li>';
      html +=   linesDisplay.join('');
      html += '</ul>';

      return html;

    } 
    
} );


/**
 * @private
 * @static
 */
g_TcToolTip._debug = false;


////
//// Coordinates.js
//// 

var g_Coordinates = MappyApi.geo.Coordinates = g_Class(/** @lends Mappy.api.geo.Coordinates.prototype */{
    /**
    @constructs
	@param {Number} x Longitude
	@param {Number} y Latitude
	*/
	initialize : function (x, y)
	{   
		x = x - 0;
		y = y - 0;
		
		if (x > 180) this.x = 180;
		else if (x < -180) this.x = -180;
		else this.x = x;
		
		if (y > 90) this.y = 90;
		else if (y < -90) this.y = -90;
		else this.y = y;
		
		this.x = x - 0;
		this.y = y - 0;
		
		g_Gall.forward(this);
	},
    /**
	@return Return a new instance of Coordinates with the same values
	*/
    clone : function ()
    {
        var c = new g_Coordinates(this.x, this.y);
        c._x = this._x;
        c._y = this._y;
        return c;
    },
    /**
        @param {Mappy.api.geo.Coordinates} coords
        @return {Object} Returns an object with "dx" and "dy" parameters containing the distance between the two coordinates in meter for xaxis and yaxis
    */
    getDistance : function (coords)
    {
        var EARTH_RADIUS = 6378137,
            EARTH_RADIUS2 = 6356752.314;
        var dLonRad = (coords.x - this.x) * Math.PI / 180;
        var dLatRad = (coords.y - this.y) * Math.PI / 180;
        
        var yCenterRad  = (this.y + coords.y) / 2 * Math.PI / 180;
        
        var tan2y = Math.tan(yCenterRad);
        tan2y = tan2y * tan2y;
        
        return {
            dx : Math.abs(EARTH_RADIUS * EARTH_RADIUS * dLonRad / Math.sqrt(EARTH_RADIUS * EARTH_RADIUS  + EARTH_RADIUS2 * EARTH_RADIUS2 * tan2y)),
            dy : Math.abs(dLatRad * EARTH_RADIUS)
        };
    },
    /**
	@return Return a string representing this coordinate
	*/
	toString : function ()
	{
		return this.x + "," + this.y;
	}
});

/**
 * Static function
 */
g_Coordinates.fromNormalized = function (x, y)
{
	return g_Gall.inverse(x, y);
}


////
//// Gall.js
//// 

var g_Gall = {
	_YF : 1.70710678118654752440,
	_XF : 0.70710678118654752440,
	_RYF : 0.58578643762690495119,
	_RXF : 1.41421356237309504880,
	a : 6378137,
	D2R : 0.01745329251994329577,
	R2D : 57.29577951308232088,
   /**
        @constructs
        @private
    */
    initialize : function ()
    {
		var southWest = new g_Coordinates(-180, -90, false);
		var northEast = new g_Coordinates(180, 90, false);
		this.forwardGall(southWest);
		this.forwardGall(northEast);
		
		this.mMaxSize = northEast._x - southWest._x;
		
		this.mSourceAeraProOrigineX = southWest._x;
		this.mSourceAeraProOrigineY = southWest._y;
		
		this.mMaxHeightPct = (northEast._y - southWest._y) / this.mMaxSize;		
    },
    forwardGall : function (coords)
	{
		coords._x = this.a * this._XF * coords.x * this.D2R;
		coords._y = this.a * this._YF * Math.tan(0.5 * coords.y * this.D2R);
	},	
	normalize : function (coords)
	{
		coords._x = (coords._x - this.mSourceAeraProOrigineX)/ this.mMaxSize;
		coords._y = (coords._y - this.mSourceAeraProOrigineY) / this.mMaxSize;
	},	
	inverseGall : function (x, y)
	{
		var lat = this.R2D * this._RXF * x / this.a;
		var lon = this.R2D * 2. * Math.atan(y / this.a * this._RYF);
		return new g_Coordinates(lat, lon);
	},	
	forward : function(coords)
	{
		this.forwardGall(coords);
		this.normalize(coords);
	},	
	inverse : function (x, y)
	{	
		if (x < 0) x = 0;
		else if (x > 1) x = 1;
		
		if (y < 0) y = 0;
		else if (y > this.mMaxHeightPct) y = this.mMaxHeightPct;
		
		
		x *= this.mMaxSize; 
		y *= this.mMaxSize;
		
		x += this.mSourceAeraProOrigineX; 
		y += this.mSourceAeraProOrigineY;
		
		return this.inverseGall(x, y);
	}
};

g_Gall.initialize();


////
//// GeoBounds.js
//// 

var g_GeoBounds = MappyApi.geo.GeoBounds = g_Class(/** @lends Mappy.api.geo.GeoBounds.prototype */{
    /**
        The north east point of the bounds.
        @type Mappy.api.geo.Coordinates
    */
    ne : null,
    /**
        The south west point of the bounds.
        @type Mappy.api.geo.Coordinates
    */
    sw : null,
    /**
        The center point of the bounds.
        @type Mappy.api.geo.Coordinates
    */
    center : null,
    /**
        @constructs
        @param {Mappy.api.geo.Coordinates} coord1
        @param {Mappy.api.geo.Coordinates} coord2
    */
    initialize : function (coord1, coord2)
    {
        if (!coord1 || !coord2)
            throw "Mappy.api.geo.GeoBounds needs 2 valid coordinates"
        
        var minX = Math.min(coord1.x, coord2.x);
        var minY = Math.min(coord1.y, coord2.y);
        var maxX = Math.max(coord1.x, coord2.x);
        var maxY = Math.max(coord1.y, coord2.y);
        
        this.ne = new g_Coordinates(maxX, maxY);
        this.sw = new g_Coordinates(minX, minY);
    },
    /**
    Set the "center" property corresponding to the current "ne" and "sw" properties.
    */
    getCenter : function ()
    {
        var x = this.sw._x + (this.ne._x - this.sw._x) / 2;
        var y = this.sw._y + (this.ne._y - this.sw._y) / 2;
        return g_Coordinates.fromNormalized(x, y);
    },
    /**
    Set the "center" property corresponding to the current "ne" and "sw" properties.
    @deprecated
    */
    refreshCenter : function ()
    {
        var x = this.sw._x + (this.ne._x - this.sw._x) / 2;
        var y = this.sw._y + (this.ne._y - this.sw._y) / 2;
        this.center = g_Coordinates.fromNormalized(x, y);
    },
	/**
	@param {Mappy.api.geo.Coordinates} c
	*/
	contains : function (c)
	{
		return c._x >= this.sw._x &&
			   c._x <= this.ne._x &&
			   c._y >= this.sw._y &&
			   c._y <= this.ne._y;
	},
    /**
    @param {Mappy.api.geo.GeoBounds} bounds
    @return {Mappy.api.geo.GeoBounds} Return the geobounds corresponding to the intersection of bounds parameter and this.
    */
    intersect : function (bounds)
    {
        var minX = Math.max(this.sw._x, bounds.sw._x);
        var minY = Math.max(this.sw._y, bounds.sw._y);
        var maxX = Math.min(this.ne._x, bounds.ne._x);
        var maxY = Math.min(this.ne._y, bounds.ne._y);

        if (maxX - minX < 0 || maxY - minY < 0)
        {
            return null;
        }
        else
        {
            var ne = g_Coordinates.fromNormalized(maxX, maxY);
            var sw = g_Coordinates.fromNormalized(minX, minY);
            return new g_GeoBounds(ne, sw);
        }
    },
    /**
    @param {Mappy.api.geo.Coordinates} c Extends the bounds with the given coordinates.
    */
    extend : function (c)
    {
        var ne = this.ne;
        var sw = this.sw;
        
        if (c._x < sw._x)
        {
            sw._x = c._x;
            sw.x = c.x;
        }
        if (c._x > ne._x)
        {
            ne._x = c._x;
            ne.x = c.x;
        }
        if (c._y < sw._y)
        {
            sw._y = c._y;
            sw.y = c.y;
        }
        if (c._y > ne._y)
        {
            ne._y = c._y;
            ne.y = c.y;
        }
    },
	getDeltaX : function ()
	{
		return this.ne._x - this.sw._x;
	},
	getDeltaY : function ()
	{
		return this.ne._y - this.sw._y;
	},
	toString : function ()
	{
		return this.ne.toString() + " / " + this.sw.toString();
	}
});



/**
 * Creates a GeoBounds instance from a list of locations (= a list of JSON objects having x and y values).
 */
var g_createGeoBoundsFromLocations = MappyApi.geo.createGeoBoundsFromLocations = function(locations)
{
	var neCoord = {};
	var swCoord = {};
	for(var i in locations)
	{
		var loc = locations[i];
		if(!neCoord.x || loc.x > neCoord.x)
		{
			neCoord.x = loc.x;
		}
		if(!neCoord.y || loc.y > neCoord.y)
		{
			neCoord.y = loc.y;
		}
		if(!swCoord.x || loc.x < swCoord.x)
		{
			swCoord.x = loc.x;
		}
		if(!swCoord.y || loc.y < swCoord.y)
		{
			swCoord.y = loc.y;
		}
	}
	var ne = new g_Coordinates(neCoord.x, neCoord.y);
	var sw = new g_Coordinates(swCoord.x, swCoord.y);
	return new g_GeoBounds(ne, sw);
};


////
//// accessor.js
//// 

/**
    Send a request.
    @private
    @param {Mappy.api.accessor.Request} request
    @param {Function} success Success handler function
    @param {Function} error Error handler function
*/
function g_submit(request, success, error)
{
    var url = request.encode();
    url = g_protocol + g_server + url;

    if (g_auth) {
        url += "&auth=" + g_auth;
    }

    url = encodeURI(url);

    g_jQuery.ajax({
        dataType : 'jsonp',
        jsonp : 'callback',
        url : url,
        scriptCharset: "utf-8",
        success : success,
        error : error
    });
}


////
//// Request.js
//// 

var g_Request = g_Class(/** @lends Request.prototype */{
    /**
        @constructs
        @private
    */
    initialize : function ()
    {
    },
    /**
        @returns {String} Specific part of the request url.
    */
    encode : function ()
    {    
    },
    /**
        Configure the request with the given args.
    */
    configure : function ()
    {    
    }
});


////
//// LocRequest.js
//// 

var g_LocRequest = g_Class(g_Request, /** @lends LocRequest.prototype */{
    /**
        @constructs
        @augments Request
        @private
    */
    initialize : function ()
    {
        g_Request.prototype.initialize.call(this);
    },
    encode : function ()
    {
        var url = g_services.loc;
        url += g_parametersToString(this._options, 'opt');
        return url;
    },
    isReady : function ()
    {
        return true;
    },
    configure : function (options)
    {
        this._options = options;
    }
});


////
//// GeocodingRequest.js
//// 

var g_GeocodingRequest = g_Class(g_LocRequest, /** @lends GeocodingRequest.prototype */{
    /**
        @constructs
        @augments LocRequest
        @private
    */
    initialize : function ()
    {
        g_LocRequest.prototype.initialize.call(this);
    },
    encode : function ()
    {
        var url = g_LocRequest.prototype.encode.apply(this);
        
        if (g_isDefined(this._addr._countryName))
        {
            url += "&countryName=" + this._addr._countryName;
        }
        if (g_isDefined(this._addr._townName))
        {
            url += "&townName=" + this._addr._townName;
        }
        if (g_isDefined(this._addr._number))
        {
            url += "&opt.number=" + this._addr._number;
        }
        if (g_isDefined(this._addr._wayName)) 
        {
            url += "&opt.wayName=" + this._addr._wayName;
        }
        if (g_isDefined(this._addr._countryCode))
        {
            url += "&countryCode=" + this._addr._countryCode;
        }
        if (g_isDefined(this._addr._townOfficialCode)) 
        {
            url += "&townOfficialCode=" + this._addr._townOfficialCode;
        }
        if (g_isDefined(this._addr._postalCode))
        {
            url += "&postalCode=" + this._addr._postalCode;
        }
        if (g_isDefined(this._addr._countryIsoCode)) 
        {
            url += "&countryIsoCode=" + this._addr._countryIsoCode;
        }
        if (g_isDefined(this._addr._subcountryName))
        {
            url += "&opt.subcountryName=" + this._addr._subcountryName;
        }
        if (g_isDefined(this._addr._subcountryOfficialCode)) 
        {
            url += "&opt.subcountryOfficialCode=" + this._addr._subcountryOfficialCode;
        }
     
        return url;
    },
    configure : function (addr, options)
    {
        g_LocRequest.prototype.configure.call(this, options);
        this._addr = addr;
    },
    isReady : function ()
    {
        return (g_isDefined(this._addr._countryName) || g_isDefined(this._addr._countryCode));
    }
});


////
//// RMMRequest.js
//// 

var g_RMMRequest = g_Class(g_Request, /** @lends RMMRequest.prototype */{
    /**
        @constructs
        @augments Request
        @private
    */
    initialize : function ()
    {
        g_Request.prototype.initialize.call(this);
    },
    encode : function ()
    {
        var url = g_services.rmm + "opt.format=json&opt.trace=1";
        var nb = this._addressList.length;

        url += "&start.rid=" + this._addressList[0].rid;
        if (g_isDefined(this._addressList[0].pct))
        {
            url += "&start.ridpct=" + this._addressList[0].pct;
        }

        if (this._addressList.length > 2)
        {
            url += "&via.rids=" + this._addressList[1].rid;
            for (i = 2; i < this._addressList.length - 1; i += 1)
            {
                url += "," + this._addressList[i].rid;
            }

            url += "&via.ridspct=" + ((g_isDefined(this._addressList[1].pct)) ? this._addressList[1].pct : '50');
            for (i = 2; i < this._addressList.length - 1; i += 1)
            {
                url += "," + ((g_isDefined(this._addressList[i].pct)) ? this._addressList[i].pct : '50');
            }
        }
        
        url += "&end.rid=" + this._addressList[nb-1].rid;
        
        if (g_isDefined(this._addressList[nb-1].pct))
        {
            url += "&end.ridpct=" + this._addressList[nb-1].pct;
        }
        
        if(g_isDefined(this._options.date))
        {
            url += "&date=" + this._options.date;
        }
        
        if(g_isDefined(this._options.time))
        {
            url += "&time=" + this._options.time;
        }
        
        if(g_isDefined(this._options.sens))
        {
            url += "&sens=" + this._options.sens;
        }
        
        if(g_isDefined(this._options.veh))
        {
            url += "&opt.vehicle=" + this._options.veh;
        }
        
        if(g_isDefined(this._options.criteria))
        {
            url += "&criteria=" + this._options.criteria;
        }
        
        if(g_isDefined(this._options.rbver))
        {
            url += "&opt.rbver=" + this._options.rbver;
        }
        
        if(g_isDefined(this._options.transport_mode))
        {
            url += "&opt.transport_mode=" + this._options.transport_mode;
        }
        
        if(g_isDefined(this._options.realtime))
        {
            url += "&opt.realtime=" + this._options.realtime;
        }
        
        if(g_isDefined(this._options.mode))
        {
            url += "&opt.mode=" + this._options.mode;
        }
        
        if(g_isDefined(this._options.nbroutes))
        {
            url += "&opt.nbroutes=" + this._options.nbroutes;
        }
        
        return url;
    },
    configure : function (addressList, options)
    {
        this._addressList = addressList;
        this._options = options || {};
    }
});


////
//// RouteRequest.js
//// 

var g_RouteRequest = g_Class(g_Request, /** @lends RouteRequest.prototype */{
    /**
        @constructs
        @augments Request
        @private
    */
    initialize : function ()
    {
        g_Request.prototype.initialize.call(this);
    },
    encode : function ()
    {
        var i;
        var url = g_services.route + "opt.format=json&opt.trace=1";

        var addressList = this._addressList;
        url += '&opt.json.route=['

        var elems = [];
        for (var i = 0; i < addressList.length; i += 1)
        {
            if (g_isDefined(addressList[i].rid))
            {
                elems.push(
                    '{"rid":"' + addressList[i].rid + ',' +
                    ((g_isDefined(addressList[i].pct)) ? addressList[i].pct : '50') +
                    '"}'
                );
            }
            else
            {
                elems.push('{"xy":"' + addressList[i].x + ',' + addressList[i].y + '"}');
            }  
        }
        url += elems.join(',') + ']';
        
        if (g_isDefined(this._options))
        {
            url += '&' + g_parametersToString(this._options, "opt");
        }
        
        return url;
    },
    configure : function (addressList, options)
    {
        this._addressList = addressList;
        this._options = options || {};
    }
});


////
//// DescrRequest.js
//// 

var g_DescrRequest = g_Class(g_Request, /** @lends DescrRequest.prototype */{
        /**
         *  if call traffic' events
         *  Boolean
         */
        _callTraffic : false,
        /**
            @constructs
            @augments Request
            @private
        */
        initialize : function ()
        {
            g_Request.prototype.initialize.call(this);
        },
        encode : function ()
        {
            var url = [
                g_services.descr,
                this._viewMode._realName,
                this._viewMode.slabSize,
                this._zoom,
                this._tilesList
            ].join('/');

            return url;
        },
        /***************************
         * raskMode = "geoBounds" || "tilesCoords"
         * Default is "tilesCoords"
         */
        configure : function (coords, zoom, viewMode)
        {
            this._tilesList = coords;
            this._zoom = zoom;
            this._viewMode = viewMode;
        },
        submit: function(success, error)
        {
            var url = g_protocol + g_tileServers[0] + this.encode();
            url = encodeURI(url);

            g_jQuery.ajax({
                dataType : 'jsonp',
                jsonp : 'callback',
                url : url,
                scriptCharset: "utf-8",
                success : success,
                error : error
            });
        }
});


////
//// UniqueFieldGeocodingRequest.js
//// 

var g_UniqueFieldGeocodingRequest = g_Class(g_LocRequest, /** @lends UniqueFieldGeocodingRequest.prototype */{
    /**
        @constructs
        @augments LocRequest
        @private
    */
    initialize : function ()
    {
        g_LocRequest.prototype.initialize.call(this);
    },
    encode : function ()
    {
        var url = g_LocRequest.prototype.encode.apply(this);
        url += "&fullAddress=" + encodeURIComponent(this._addr);
        return url;
    },
    configure : function (addr, options)
    {
        g_LocRequest.prototype.configure.call(this, options);
        this._addr = addr;
    },
    isReady : function ()
    {
        return (typeof this._addr === "string");
    }
});


////
//// XYReverseGeocodingRequest.js
//// 

var g_XYReverseGeocodingRequest = g_Class(g_LocRequest, /** @lends XYReverseGeocodingRequest.prototype */{
    /**
        @constructs
        @augments LocRequest
        @private
    */
    initialize : function ()
    {
        g_LocRequest.prototype.initialize.call(this);
    },
    encode : function ()
    {
        var url = g_LocRequest.prototype.encode.apply(this);
        url += "&x=" + this._coords.x;
        url += "&y=" + this._coords.y;
        return url;
    },
    configure : function (coords, options)
    {
        g_LocRequest.prototype.configure.call(this, options);
        this._coords = coords;
    },
    isReady : function ()
    {
        return (this._coords instanceof g_Coordinates);
    }
});


////
//// Location.js
//// 

var g_Location = g_Class(/** @lends Location.prototype */{
    /**
        @constructs
    */
    initialize : function ()
    {

    }
});


////
//// AddressLocation.js
//// 

var g_AddressLocation = MappyApi.geolocation.AddressLocation = g_Class(g_Location, /** @lends Mappy.api.geolocation.AddressLocation.prototype */{
    /**
        Describes an address. The purpose of this class is to be given to the geocoder as an argument to find a place
        @constructs
        @augments Location
        @param {String} countryName Name of the country where the location is. eg "France".
        @param {String} townName Name of the town where the location is. eg "Paris".
        @param {String} wayName Optionnal parameter specifing the name of the way
        @param {String} number Optional string (beware of "2 bis" numbers !) number in the street
    */
    initialize : function (countryName, townName, wayName, number)
    {
        g_Location.prototype.initialize.call(this);
            
        // Mandatory parameters
    	this._countryName = countryName;
    	this._townName = townName;
    	
        // Optional parameters
        this._wayName = wayName;
    	this._number = number; // a string, to handle "2 bis"
    },
    /**
    @param {String} countryName Name of the country where the location is. eg "France".
    */
    setCountryName : function (countryName)
    {
        delete this._countryCode;
        delete this._countryIsoCode;
        this._countryName = countryName;
    },
    /**
    @param {String} townName Name of the town where the location is. eg "Paris".
    */
    setTownName : function (townName)
    {
        delete this._townOfficialCode;
        this._townName = townName;
    },
    /**
    @param {String} wayName Optionnal parameter specifing the name of the way
    */
    setWayName : function (wayName)
    {
        this._wayName = wayName;
    },
    /**
    @param {String} number Optional string (beware of 2 bis" numbers !) number in the street
    */
    setNumber : function (number)
    {
        this._number = number;
    },
    setCountryCode : function (countryCode)
    {
        delete this._countryName;
        delete this._countryIsoCode;
        this._countryCode = countryCode;
    },
    setTownOfficialCode : function (townOfficialCode)
    {
        delete this._townName;
        this._townOfficialCode = townOfficialCode;
    },
    setPostalCode : function (postalCode)   
    {
        this._postalCode = postalCode;
    },
    /**
        @param {Number} countryIsoCode Set the iso numeric 3-digits code for this address.    
    */
    setCountryIsoCode : function (countryIsoCode)
    {
        delete this._countryName;
        delete this._countryCode;
        this._countryIsoCode = countryIsoCode;
    },
    setSubcountryName : function (subcountryName)
    {
        delete this._subcountryOfficialCode;
        this._subcountryName = subcountryName;
    },
    setSubcountryOfficialCode : function (subcountryOfficialCode)
    {
        delete this._subcountryName;
        this._subcountryOfficialCode = subcountryOfficialCode;
    }
});


////
//// CertifiedLocation.js
//// 

var g_COUNTRY_LEVEL = 3,
    g_SUBCOUNTRY_LEVEL = 4,
    g_TOWN_LEVEL = 7,
    g_WAY_LEVEL = 10,
    g_WAYNUMBER_LEVEL = 10;

var g_CertifiedLocation = g_Class(g_Location, /** @lends CertifiedLocation.prototype */{
    /**
        The KML Placemark of the location.
        @type Object
    */
    Placemark : null,
    /**
        @constructs
        @augments Location
        @param {Object} Placemark KML output.
    */
    initialize : function (Placemark, clone)
    {
        g_Location.prototype.initialize.call(this);

        var i;

        switch (Placemark.ExtendedData['mappy:LocalGeocodeLevel']['mappy:code'])
        {
        case '1' :
            this._bestZoom = g_COUNTRY_LEVEL;
            break;
        case '2' :
            this._bestZoom = g_SUBCOUNTRY_LEVEL;
            break;
        case '3' :
            this._bestZoom = g_TOWN_LEVEL;
            break;
        case '4' :
            this._bestZoom = g_WAY_LEVEL;
            break;
        case '5' :
            this._bestZoom = g_WAYNUMBER_LEVEL;
            break;
        default :
            this._bestZoom = 0;
        }
        
        if (clone !== true)
        {
            var newThoroughfareNumber, tmpNumbers;
            
            if (g_isPathDefined(Placemark, "AddressDetails.Country.AdministrativeArea.Locality.Thoroughfare.ThoroughfareNumber"))
            {
                newThoroughfareNumber = {};
                tmpNumbers = g_jQuery.makeArray(Placemark.AddressDetails.Country.AdministrativeArea.Locality.Thoroughfare.ThoroughfareNumber);
                for (i = 0; i < tmpNumbers.length; i += 1)
                {
                    newThoroughfareNumber[tmpNumbers[i].Type] = tmpNumbers[i].value;
                }
                Placemark.AddressDetails.Country.AdministrativeArea.Locality.Thoroughfare.ThoroughfareNumber = newThoroughfareNumber;
            }
            
            if (g_isPathDefined(Placemark, "AddressDetails.Country.AdministrativeArea.Locality.DependentLocality.Thoroughfare.ThoroughfareNumber"))
            {
                newThoroughfareNumber = {};
                tmpNumbers = g_jQuery.makeArray(Placemark.AddressDetails.Country.AdministrativeArea.Locality.DependentLocality.Thoroughfare.ThoroughfareNumber);
                for (i = 0; i < tmpNumbers.length; i += 1)
                {
                    newThoroughfareNumber[tmpNumbers[i].Type] = tmpNumbers[i].value;
                }
                Placemark.AddressDetails.Country.AdministrativeArea.Locality.DependentLocality.Thoroughfare.ThoroughfareNumber = newThoroughfareNumber;
            }
            
            if (Placemark.Point)
            {
                var coords = Placemark.Point.coordinates.split(",");
                Placemark.Point.coordinates = [];
                for (i = 0; i < coords.length; i += 1)
                {
                    Placemark.Point.coordinates.push(coords[i]);
                }
            }
        }
            
        this.Placemark = Placemark;
    },
    /**
    @returns {CertifiedLocation} Clone the object
    */
    clone : function ()
    {
        var placemark = g_jQuery.extend(true, {}, this.Placemark);
        return new g_CertifiedLocation(placemark, true);
    },
    /**
    @returns {Number} Returns a zoom level based on geolocation precision.
    */
    getBestZoom : function ()
    {
        return this._bestZoom;
    },
    /**
    @private
    */
    getRidInfo : function ()
    {
        return {
            rid: this.Placemark.ExtendedData['mappy:road_element_id'],
            pct: this.Placemark.ExtendedData['mappy:road_element_percentage']
        };
    },
    /**
    @returns {Mappy.api.geo.Coordinates} Returns coordinates of this address
    */
    getCoordinates : function ()
    {
        return new g_Coordinates(this.Placemark.Point.coordinates[0], this.Placemark.Point.coordinates[1]);
    }
});


////
//// Geocoder.js
//// 

var g_Geocoder = MappyApi.geolocation.Geocoder = g_Class(/** @lends Mappy.api.geolocation.Geocoder.prototype */{
    _options :
    {
        format : "json",
        namedPlaceSearch : 1,
        interactive : 1,
        language : "FRE",
        xmlOutput : "3v0"
    },
    /**
        @constructs
    */
    initialize : function ()
    {
    },
    /**
        Submits an asynchronous geocoding request. The request is submited to the server. The geocoding request could be interactive. This means that if no exact response is found, the geocoder returns a list of addresses that are likely to fulfill the request. By default, the request is interactive. When geocoding is done, the succes function is called.
        @param {Object} location Can be a {@link Mappy.api.geolocation.AddressLocation}, a {string} or a {@link Mappy.api.geo.Coordinates}.
        @param {Function} success Success handler function. The first argument is the server response and given as an {array} of {@link CertifiedLocation}.
        @param {Function} error Error handler function
    **/
    geocode : function (location, success, error)
    {
        if (location instanceof g_CertifiedLocation)
        {
            success([location]);
        }
        else
        {
            var req;
            
            if (typeof location === "string")
            {
                req = new g_UniqueFieldGeocodingRequest();
            }
            else if (location instanceof g_Coordinates)
            {
                req = new g_XYReverseGeocodingRequest();
            }
            else if (location instanceof g_AddressLocation)
            {
                req = new g_GeocodingRequest();
            }
            else
            {
                if (g_jQuery.isFunction(error))
                {
                    error(new Error('Bad parameter.'));
                }
                return;
            }

            req.configure(location, this._options);

            if (req.isReady())
            {
                g_submit(req, function (geocoderResponse)
                {
                    var results = [];
                    try
                    {
                        var places = g_jQuery.makeArray(geocoderResponse.kml.Document.Placemark);

                        for (var i = 0; i < places.length; i += 1)
                        {
                            results.push(new g_CertifiedLocation(places[i]));
                        }
                    }
                    catch (e)
                    {
                        if (g_jQuery.isFunction(error))
                        {
                            error(e);
                        }
						else
						{
							throw e;
						}
                        return;
                    }
                    success(results);
                }, error);
            }
            else
            {
                if (g_jQuery.isFunction(error))
                {
                    error(new Error('Not enough information.'));
                }
            }
        }
    },
    /**
    @param {Number} namedPlaceSearch 1 or 0 to enable named place in the research or not.
    */
    setNamedPlaceSearch : function (namedPlaceSearch)
    {
        this._options.namedPlaceSearch = namedPlaceSearch;
    },
    /**
    @param {Boolean} interactive 1 or 0 to enable ambiguity or not.
    */
    setInteractive : function (interactive)
    {
        this._options.interactive = interactive;
    },
    /**
    @param {Number} maxRadius Max distance in meter for a reverse geocoding.
    */
    setMaxRadius : function (maxRadius)
    {
		this._options.maxRadius = maxRadius;
	},
    /**
    @param {String} language Language of the response. Example : "FRE"
    */
    setLanguage : function (language)
    {
		this._options.language = language;
	},
    /**
    @param {Number} favoriteCountry Set the country code of a favorite country to focus the research on it. 
    */
    setFavoriteCountry : function (favoriteCountry)
    {
        this._options.favoriteCountry = favoriteCountry;
    },
    /**
    @param {Number} favoriteCity Set the city code of a favorite city to focus the research on it. 
    */
    setFavoriteCity : function (favoriteCity)
    {
        this._options.favoriteCity = favoriteCity;
    }
});


////
//// BgPoi.js
//// 

var g_BgPoi_hightlighted = 0;
var g_BgPoi = g_Class(/** @lends BgPoi.prototype */ {
    /**
        @private
        @constructs
    */
    initialize : function (item, descrLayerName, controller, key)
    {
        this.key = key;
        this._points = {};
        this._box = item.box;
        this.id = item.id;
        this.label = item.properties.description.label;
        this.controller = controller;
        this.item = item;

        this.item.descrLayerName = descrLayerName;
       
        var c;

        this._points.min = controller.converter.toGeolayerPixels(new g_Coordinates(this._box.minx, this._box.miny));
        this._points.max = controller.converter.toGeolayerPixels(new g_Coordinates(this._box.maxx, this._box.maxy));
    },
    isClickeable : function ()
    {
        return ((g_isPhone === true) || (parseInt(this.item.click) === 1));
    },
    isOver : function (x, y)
    {
        var oddNodes = false;

        var xMax = this._points.max.x;
        var xMin = this._points.min.x;
        var yMax = this._points.max.y;
        var yMin = this._points.min.y;

        if(g_isPhone)
        {
            var xDelta = xMax - xMin;

            if(xDelta < 25 )
            {
                xMax += Math.floor((25 - xDelta) / 2);
                xMin -= Math.ceil((25 - xDelta) / 2);
            }
            var yDelta = yMax - yMin;
            if(yDelta < 25 )
            {
                yMax += Math.floor((25 - yDelta) / 2);
                yMin -= Math.ceil((25 - yDelta) / 2);
            }
        }
        // Test point coordinates
        // Warning ! inverted Y values for GeolayerPixels
        oddNodes = (xMin <= x && x <= xMax) && (yMax <= y && y <= yMin);
        return oddNodes;
    },
    openToolTip : function (event)
    {
        if (!this._toolTip)
        {
            if( this.item.properties.type && 'RAILWAY' === this.item.properties.type )
            {
                this._toolTip = new g_TcToolTip(this.controller.view.geolayer, this.item );
            }
            else
            {
                this._toolTip = new g_ToolTip(this.controller.view.geolayer, '<div style="margin:3px 6px">' + this.label + '</div>');
            }
        }
        
        if (this._toolTip.isAdded === false)
        {
            this._toolTip.add(event);
        }
    },
    closeToolTip : function ()
    {
        if (!this.keepTooltip && this._toolTip)
        {
            this._toolTip.remove();
        }
    },
    destroy : function ()
    {
        if (this._toolTip)
        {
            this._toolTip.remove();
        }
    }
});


////
//// Tile.js
//// 

var g_Tile = g_Class(/** @lends Tile.prototype */{
    /**
        @constructs
        @param {Object} options ix, iy, init, layer
        @private
    */
    initialize : function (sx, sy, zoom, viewMode)
    {
        this.sx = sx;
        this.sy = sy;
        this.zoom = zoom;
        this.viewMode = viewMode;
		
		this.key = this.getKey(sx, sy, zoom, viewMode.name);
    },
    create : function (left, top)
    {
        var viewMode = this.viewMode;
        var slabSize = viewMode.slabSize;
        var positionX = this.sx * slabSize - left;
        var positionY = top - (this.sy + 1) * slabSize;

        var img = document.createElement("img");

        img.style.position = 'absolute';
        img.style.left = positionX + "px";
        img.style.top = positionY + "px";
        img.style.width = slabSize + "px";
        img.style.height = slabSize + "px";

        img.galleryImg = "no";
        img.style.MozUserSelect = "none";
        img.style.KhtmlUserSelect = "none";
        img.unselectable = "on";
        img.onselectstart = function ()
        {
            return false;
        };

        img.src = _DATA.mappy_images+'img/map/transparent.png';

        img.ondragstart = function () {
            return false;
        };

        img.onmousedown = function () {
            return false;
        };

		var sx = viewMode.getSx(this);
		
		var numServer = (sx * this.sy) % g_tileServers.length;
        var url =   g_protocol + g_tileServers[numServer] + [
                        g_services.slab,
                        viewMode._realName,
                        viewMode.slabSize,
                        this.zoom,
                        sx,
                        this.sy
                    ].join('/');

		var that = this;
	
		var errorCallback;
		if (g_reloadEnabled)
		{
			errorCallback = function (error)
			{
				g_loadImage(
					url + "&retry=1",
					img,
					function ()
					{
						that.loaded = true;
					}
				);
			};
		}
		
		g_loadImage(
			url,
			img,
			function ()
			{
				that.loaded = true;
			},
			errorCallback
		);
        
        this.div = img;
        
        this.left = left;
		this.top = top;
    },
    clone : function ()
    {
        var tile = new g_Tile(this.sx, this.sy, this.zoom, this.viewMode);
        if (g_isDefined(this.div))
        {
            tile.div = this.div.cloneNode(false);
			tile.left = this.left;
			tile.top = this.top;
        }
        return tile;
    },
    append : function (parent)
    {
        parent.append(this.div);
    },
    /**
        Zoom and reposition the image.
        @param {Number} zoomFactor The zoom factor.
        @param {Point} position The position of the zoom in the map.
    */
    zoomAt : function (zoomFactor)
    {		
        var slabSize = this.viewMode.slabSize;
		var left = this.left;
		var top = this.top;
		
		var positionX = this.sx * slabSize - left;
        var positionY = top - (this.sy + 1) * slabSize;
        
        var img = this.div;
        img.style.left = (positionX * zoomFactor) + "px";
        img.style.top = (positionY * zoomFactor) + "px";
        img.style.width = (slabSize * zoomFactor) + "px";
        img.style.height = (slabSize * zoomFactor) + "px";
		
		
    },
    /**
        Destroy the image.
    */
    remove : function ()
    {
        this.div.src = _DATA.mappy_images+'img/map/transparent.png';
        this.div.parentNode.removeChild(this.div);
    },
    /**
        Interrupt the download of the image.
    */
    stopDownload : function ()
    {
        if (!this.loaded)
        {
            this.remove();
        }
    },
	getKey : function (sx, sy, zoom, viewMode)
	{
		return sx + "," + sy + "," + zoom + "," + viewMode;
	}
});


////
//// ViewMode.js
//// 

var g_ViewMode = MappyApi.map.ViewMode = g_Class(/** @lends Mappy.api.map.ViewMode.prototype */{
	/**
		Size of the tiles in pixel
		@type Number
    */
    slabSize : null,
    /**
		min zoom level of the mode
		@type Number
    */
    minZoomLevel : null,
    /**
		max zoom level of the mode
		@type Number
    */
    maxZoomLevel : null,
    /**
		@private
    */
    _timer : undefined,
    /**
		@private
    */
    _timestampTimeout : -1,
    /**
		Refresh delay for this viewmode in millisecond
	   @private
	   @type {Integer}
    */
    _refreshDelay : undefined,
    /**
		@private
    */
    _stopRefreshDelay : g_defaultStopRefreshDelay * 1000, // By default 14 minutes in millis
    /**
        @constructs
        @param {String} name The name of the view mode. Possible values depends on your account. Can be a list of value separated by ";".
    */
    initialize : function (name, postKey)
    {
		// Check the existence of configuration for each viewMode defined in name param
		var viewModesNames = new Array();
		var viewModesNamesTmp = name.split(";");
		var viewModesRealNames = {"printmap":["printmap"]};
		
		for(var viewModeNameId in viewModesNamesTmp )
		{
			var viewModeName = viewModesNamesTmp[viewModeNameId];
            viewModesNames.push(viewModeName);

            // Update delays
            if(g_isPathDefined(g_viewModes, "[viewModeName]['refresh-delay']"))
            {
                this._refreshDelay = g_viewModes[viewModeName]["refresh-delay"] * 1000;
                this._stopRefreshDelay = (g_viewModes[viewModeName]["stop-refresh-delay"] || g_defaultStopRefreshDelay) * 1000;
            }
		}
		name = viewModesNames.join(";");
		this._realName = viewModesNames.join(";");
		
		this._realNames = new Array();
		for(var i in viewModesRealNames)
		{
			this._realNames[i] = viewModesRealNames[i].join(";");
		}
		
		// By default, we assume that the configuration of the first viewMode is the one we should use
		this.slabSize = g_ViewMode.slabSize;
		if(g_isPathDefined(g_viewModes, "[viewModesNames[0]]['slab-region'].template.size"))
		{
			this.slabSize = parseInt(g_viewModes[viewModesNames[0]]['slab-region'].template.size, 10);
		}

		this.maxZoomLevel = 12;

		switch (name)
		{
			case "hybrid":
			case "photo":
				this.minZoomLevel = 3;
				break;
			default :
				this.minZoomLevel = 0;
		}

        if (name === "iti" || name === "itimap")
        {
            name += '&postkey=' + postKey;
        }
        this.name = name;

    },
//	/**
//		@private
//	*/
//	_searchRealNameIdx : function(viewModeName)
//	{
//		var combinationIdx = -1
//		for(var i in g_viewModesCombinations)
//		{
//			if(combinationIdx !== -1)
//			{
//				break;
//			}
//			combinationIdx = g_jQuery.inArray(viewModeName, g_viewModesCombinations[i]);
//		}
//		return combinationIdx;
//	},
    /**
		@param {Number} zoom Zoom level to test.
    */
	zoomable : function (zoom)
	{
		if (zoom > this.maxZoomLevel)
		{
			return this.maxZoomLevel;
		}
		else if (zoom < this.minZoomLevel)
		{
			return this.minZoomLevel;
		}

		return -1;
	},
    /**
		@private
    */
    getSx : function (tile)
    {
		var check = g_ViewMode.tileMaxIds[tile.zoom];
        var sx;
        if (g_isDefined(check))
        {
            sx = tile.sx % (check[0] + 1);
            if (sx < 0)
            {
                sx += (check[0] + 1);
            }
        }
        return sx;
    },
    /**
		@private
    */
    checkSx : function (tile)
    {
		var check = g_ViewMode.tileMaxIds[tile.zoom];

        if (g_isDefined(check))
        {
            return (tile.sx >= 0 && tile.sx <= check[0]);
        }
        else
        {
            return false;
        }
    },
    /**
		@private
    */
    checkSy : function (tile)
    {
		var check = g_ViewMode.tileMaxIds[tile.zoom];

         if (g_isDefined(check))
        {
            return (tile.sy >= 0 && tile.sy <= check[1]);
        }
        else
        {
            return false;
        }
    },
    /**
		@param {Number} millis Time before stop refreshing.
    */
	setStopRefreshDelay : function(millis)
	{
		if(millis === undefined || millis === NaN)
		{
			// Default value
			this._stopRefreshDelay = g_defaultStopRefreshDelay * 1000;
		}
		else
		{
			this._stopRefreshDelay = millis;
		}
	},
	/**
		Compute the viewmode to use given a specified background type.
		@param {g_MapModel} The given bgType
		@private
	 */
	_updateRealName : function(mapModel)
	{
		if(g_isDefined(this._realNames[mapModel._bgType]) && typeof this._realNames[mapModel._bgType] === "string")
		{
			this._realName = this._realNames[mapModel._bgType];
		}
	},
    /**
		@param {Number} millis Time between refresh.
    */
	setRefreshDelay : function(millis)
	{
		this._refreshDelay = millis;
		
		if(g_isDefined(this._timer))
		{
			clearTimeout(this._timer); // Clear timeout : if the refresh value has been modified, reload it
			this._refreshTask();
		}
		
	},
    /**
		Start the refresh of this layer if it's possible (delay have been specified)
    */
    _refreshTask : function (ctrl)
    {
		if(g_isDefined(this._refreshDelay))
		{	
			if(this._timestampTimeout === -1)
			{
				/*
				 o	Si c’est le premier refresh de ce viewmode (aucun timestamp stocké) :
						Stocker le timestamp courant.
						Envoi d’un event JS ‘startrefreshviewmode’.
				*/	
				this._mapctrl = ctrl;
				this._mapctrl.map.trigger("startrefreshviewmode", this);
				this._timestampTimeout = (new Date()).getTime();
				var that = this;
				this._timer = setTimeout(function()
										{
										   that._refreshTask(that._mapctrl);
										}
										, this._refreshDelay);
			}
		}
		
		if(this._timestampTimeout !== -1 && (new Date()).getTime() - this._timestampTimeout < this._stopRefreshDelay)
		{
			/*
			o	Si la limite de temps _stopRefreshDelay n’est pas atteinte (currentDate – timestamp > _stopRefreshDelay), 
					Refresh des dalles, via la méthode refreshTiles(viewmode) de MapController
					Relancer un timer sur cette fonction. 
					Envoi d’un event JS ‘refreshviewmode’.
			*/
			this._mapctrl.refreshTiles(this.name, true);
			
			var that = this;
			this._timer = setTimeout(function()
									{
									   that._refreshTask(that._mapctrl);
									}
									, this._refreshDelay);		
			
			this._mapctrl.map.trigger("refreshviewmode", this);
		}
		else if(this._timestampTimeout !== -1)
		{
			this._stopRefreshTask(false);
		}
    }
	/**
		@param {Boolean} removed TRUE if we stop the refresh because of removal of this viewmode.
	*/
	, _stopRefreshTask : function(removed)
	{
		
		this._timestampTimeout = -1;
		
		if(g_isDefined(this._timer))
		{
			/*
			o	Sinon :
					Supprimer le timestamp. 
					Envoi d’un event JS ‘stoprefreshviewmode’. 
			*/
			clearTimeout(this._timer);
			this._timer = undefined;
			
			if(g_isDefined(this._mapctrl))
			{
				this._mapctrl.map.trigger("stoprefreshviewmode", this, removed);
			}
		}
	}
});

MappyApi.map.viewModes = {
    MAP: "map",
    HYBRID: "hybrid",
    PHOTO: "photo"
};

// Default values
g_ViewMode.slabSize = 384;
g_ViewMode.mMaxZoomLevel = 12;
g_ViewMode.tileMaxIds = [
	[0, 0],
	[2, 2],
	[8, 6],
	[26, 20],
	[80, 62],
	[242, 186],
	[728, 560],
	[2186, 1680],
	[6560, 5041],
	[19682, 15125],
	[59048, 45377],
	[177146, 136131],
	[531440, 408395],
	[1594322, 1225186]
];
// In minutes
var g_defaultRefreshDelay = 2 * 6;
var g_defaultStopRefreshDelay = 14 * 6;


////
//// ToolManager.js
//// 

var g_ToolManager = g_Class(/** @lends ToolManager.prototype */{
    _tools : null,
    /**
        @constructs
        @private
    */
    initialize : function (map)
    {
        this._map = map;
        this._tools = [];
    },
    addTool : function (tool)
    {
        if (tool instanceof  g_MiniMap)
        {
            if (g_isDefined(this._map.miniMap))
            {
                this._map.miniMap.removed();
            }
            this._map.miniMap = tool;
            tool.added(this._map);
            this._refreshPositions();
        }
        else
        {
            this._tools.push(tool);
            tool.added(this._map);
            tool.refreshPosition();
        }
    },
    removeTool : function (tool)
    {
		if (g_isNotDefined(tool))
		{
			return;
		}
		
        if (tool instanceof g_MiniMap)
        {
            this._map.miniMap = null;
        }

        tool.removed();
        var index = g_jQuery.inArray(tool, this._tools);
        if (index !== -1)
        {
            this._tools.splice(index, 1);
        }
        
        this._refreshPositions();
    },
    _refreshPositions : function ()
    {        
        for (var i = 0; i < this._tools.length; i += 1)
        {
            this._tools[i].refreshPosition();
        }
    }
});


////
//// Marker.js
//// 

var g_Marker = MappyApi.map.Marker = g_Class(g_EventSource, /** @lends Mappy.api.map.Marker.prototype */{
    /**
    List of events the marker can trigger :<br/>
    - click<br/>
    - dblclick<br/>
    - mouseover<br/>
    - mouseout<br/>
    - dragstart<br/>
    - dragstop<br/>
    - popupopened<br/>
    - popupclosed
    @type String[]
    @static
    */
    EVENTS : ["click", "dblclick", "mouseover", "mouseout", "dragstart", "dragstop", "drag", "popupopened", "popupclosed"],
    /**
        The created object.
        @type g_jQueryObject
    */
    div : null,
    /**
        Coordinates of the marker.
        @type Mappy.api.geo.Coordinates
    */
    coordinates : null,
    /**
        @type {CertifiedLocation}
    */
    location : null,
    /**
        Is it on the map?
        @type boolean
    */
    isOnMap : false,
    /**
        Is it on the hidden?
        @type boolean
    */
    isHidden : false,
    /**
        Is popup opened?
        @type boolean
    */
    isPopUpOpened : false,
    /**
        Is marker in a cluster?
        @type boolean
    */
    isInCluster : false,
    /**
        Is marker dragged?
        @type boolean
    */
    dragging : false,
    isOver : false,
    /**
        @private
    */
    _tailStyle : null,
    /**
        @private
    */
    _toolTip : null,
    /**
        @constructs
        @augments EventSource
        @param {Mappy.api.geo.Coordinates} coordinates coordinates of the marker.
        @param {Mappy.api.ui.Icon} icon Icon of the marker. If you pass a string, the default icon will use this string as a label.
        @param {Mappy.api.map.PopUpOptions} popUpOptions Options for the popup.
        
        @eventdescription click Fires when a click occurs on the marker
        @eventdescription#click {MouseEvent} e Native onclick event on the marker.
        
        @eventdescription dblclick Fires when a double click occurs on the marker
        @eventdescription#dblclick {MouseEvent} e Native ondblclick event on the marker.
        
        @eventdescription mouseover Fires when a mouseover occurs on the marker
        @eventdescription#mouseover {MouseEvent} e Native mouseover event on the marker.
        
        @eventdescription mouseout Fires when a mouseout occurs on the marker
        @eventdescription#mouseout {MouseEvent} e Native mouseout event on the marker.
        
        @eventdescription popupopened Fires when the pop is opened
        
        @eventdescription popupclosed Fires when the pop is closed
        
        @eventdescription dragstart
        @eventdescription#dragstart {MouseEvent} e
        
        @eventdescription dragstop
        @eventdescription#dragstop {MouseEvent} e
        
        @eventdescription drag
        @eventdescription#drag {MouseEvent} e
        
    */
    initialize : function (coordinates, icon, popUpOptions)
    {
        g_EventSource.prototype.initialize.apply(this);
        this.coordinates = coordinates;
        
        if (!icon || typeof icon === "string")
        {
            this._icon = new g_Icon(MappyApi.ui.Icon.DEFAULT);
            if (typeof icon === "string")
            {
                this._icon.label = icon;
            }
        } else {
            this._icon = icon;
        }
        this._popUpOptions = popUpOptions || new g_PopUpOptions({mappyDecoration : true});

        this._toolTipListeners = [];
    },
    /**
        @param {Mappy.api.ui.Icon} icon Set a new icon for this marker.
    */
    setIcon : function (icon)
    {
        this._icon = icon;
        if (this.isOnMap)
        {
            this._createIcon(this.div.parent());
            this.setMarkerPosition();
        }
    },
    /**
        @returns {Mappy.api.ui.Icon} Returns marker's icon.
    */
    getIcon : function ()
    {
        return this._icon;
    },
    /**
        @param {Mappy.api.map.PopUpOptions} popUpOptions Set popup options.
    */
    setPopUpOptions : function (popUpOptions)
    {
        this._popUpOptions = popUpOptions;
    },
    /**
        @returns {Mappy.api.map.PopUpOptions} Returns marker's popup options.
    */
    getPopUpOptions : function ()
    {
        return this._popUpOptions;
    },
    /**
        @private
    */
    _createIcon : function (container)
    {
        if (g_isDefined(this.div))
        {
            this.div.remove();
        }
		
		this.div = this._icon.create(g_jQuery(container)[0].ownerDocument.createElement('div'));
        this.div.css('position', 'absolute');

        var that = this;
        this.div.click(function (event)
        {
            that.trigger('click', event);
        });

        this.div.dblclick(function (event)
        {
            that.trigger('dblclick', event);
        });
        
        this.div.mouseenter(function (event)
        {
            if (!that.isOver)
            {
                that.isOver = true;
                that.trigger('mouseover', event);
            }
        });
        
        this.div.mousemove(function (event)
        {
            if (!that.dragging)
                event.stopPropagation();
        });

        this.div.mouseleave(function (event)
        {
            that.isOver = false;
            that.trigger('mouseout', event);
        });

        container.append(this.div);
    },
    /**
        @private
    */
    _createToolTip : function ()
    {
        if (g_isDefined(this.toolTip))
        {
            this.removeToolTip();

            var toolTip = new g_ToolTip(this._controller.view.geolayer, '<div style="margin:3px 6px">' + this.toolTip + '</div>');

            this._toolTipListeners.push(this.addListener('mouseover', function (event)
            {
                toolTip.add(event);
            }));

            this._toolTipListeners.push(this.addListener('mouseout', function ()
            {
                toolTip.remove();
            }));

            this._toolTipInst = toolTip;
        }
    },
    /**
    @param {Function} success Same success function as geocoder service.
    @param {Function} error Same error function as geocoder service.
    */
    geocode : function (success, error)
    {
        var geo = new g_Geocoder();
        var that = this;
        geo.geocode(this.coordinates, function (results)
        {
            if (results.length > 0)
            {
                var c = results[0].Placemark.Point.coordinates;
                that.location = results[0];
                that.coordinates = new g_Coordinates(c[0], c[1]);
                that.setPosition();
            }
            if (typeof success === "function")
            {
                success(results);
            }
        }, error);
    },
    /**
        @private
    */
    added : function (container, controller)
    {
		this._controller = controller;
        this._createIcon(container);
        
		if (controller.map.isReady)
        {
            this.setPosition();
        }
		
        this.isOnMap = true;
		
		this._createToolTip();
		
        if (this.isHidden)
        {
            this.hide();
        }
    },
    /**
        @private
    */
    removed : function ()
    {
        if (this.isOnMap)
        {
            this.isOnMap = false;
            this.removeToolTip();
            this.closePopUp();
            this.div.remove();
        }
    },
    /**
        @private
    */
    setPosition : function ()
    {
        this.pos = this._controller.converter.toGeolayerPixels(this.coordinates);
        this.setMarkerPosition();
    },
    /**
        @private
    */
    setMarkerPosition : function ()
    {
        this.div.css({
            left: this.pos.x - this._icon.iconAnchor.x,
            top : this.pos.y - this._icon.iconAnchor.y,
            "z-index" : this.pos.y + 10000
        });

        if (g_isDefined(this._popUp))
        {
            this._popUp.setPopUpPosition(this.pos);
        }
    },
    /**
        Open a pop up on the marker with last content if any.
        @param {boolean} slide (Optionnal) If true, the map will slide to the pop position. default : true
    */
    showPopUp : function (slide)
    {
		return this.openPopUp(undefined, slide);
	},
    /**
        Open a pop up on the marker.
        @param html Content of the popup. String, g_jQueryObject or DOM element.
        @param {boolean} slide (Optionnal) If true, the map will slide to the pop position. default : true
    */
    openPopUp : function (html, slide)
    {
        if (g_isDefined(this._popUp))
        {
             this._controller.popuplayer.removePopUp(this._popUp);
        }
		
        if (!g_isDefined(this.pos))
        {
             this.setPosition();
        }
		
		if(g_isDefined(html))
		{
			this._popUp = new g_PopUp(html, this);
		}
		
        if (g_isDefined(this._popUp))
        {
			this._controller.popuplayer.addPopUp(this._popUp);
			this._popUp.setPopUpPosition(this.pos);
	
			this.isPopUpOpened = true;
	
			this.trigger('popupopened');
	
			if (slide !== false)
			{
				return this._popUp.slideTo(this.pos);
			}
		}
		return false;
    },
    /**
        Close the popup
    */
    closePopUp : function ()
    {
        if (g_isDefined(this._popUp))
        {
            this._controller.popuplayer.removePopUp(this._popUp);
            this.isPopUpOpened = false;
            this.trigger("popupclosed");
        }
    },
    /**
        Add a tooltip on the marker
        @param html Content of the tooltip. String, g_jQueryObject or DOM element.
    */
    addToolTip : function (html)
    {
        this.toolTip = html;

        if (this.isOnMap)
        {
            this._createToolTip();
        }
    },
    /**
        Remove the tooltip on the marker.
    */
    removeToolTip : function ()
    {
        if (g_isDefined(this._toolTipInst))
        {
            this._toolTipInst.remove();
        }
        for (var i = 0; i < this._toolTipListeners.length; i += 1)
        {
            this.removeListener(this._toolTipListeners[i]);
        }
        this._toolTipListeners = [];
    },
    /**
        If the marker is draggable, enable it.
    */
    enableDraggable : function ()
    {
		if(g_isDefined(this.div) && typeof this.div.enableDraggable === "function")
		{
			this.div.enableDraggable();
		}
		else if(g_isDefined(this.div))
		{
			this.addDraggable();
		}
	},
    /**
        If the marker is draggable, disable it.
    */
    disableDraggable : function ()
    {
		if(g_isDefined(this.div) && typeof this.div.disableDraggable === "function")
		{
			this.div.disableDraggable();
		}
	},
    /**
        Make the marker draggable. Marker must be on a map.
    */
    addDraggable : function ()
    {
        var that = this;
        var moved = false, timer, dragStarted;
        
        var dragStarted = function()
        {
            var movingX = 0;
			var movingY = 0;
            
            var pos = that._controller.view.position();
            
            var mapSize = that._controller.model.getSize();
            
            var imgPosition = that.div.position();
            var iconAnchor = that._icon.iconAnchor;
            
            var posX = imgPosition.left + iconAnchor.x + pos.left;
            if (posX < 20)
            {
                movingX = 5;
            }
            else if (posX > mapSize.width - 20)
            {
                movingX = -5;
            }

            var posY = imgPosition.top + iconAnchor.y + pos.top;
            if (posY < 20)
            {
                movingY = 5;
            }
            else if (posY > mapSize.height - 20)
            {
                movingY = -5;
            }

            if (movingX || movingY)
            {
				moved = true;
                pos.left += movingX;
                pos.top += movingY;
                that._controller.view.setPosition(pos.left, pos.top);
                imgPosition.left -= movingX;
                imgPosition.top -= movingY;
                that.div.hackModifyInit(movingX, movingY);
                that.div.css(imgPosition);
				that._controller.refreshTiles();
            }
        }
        
        this.div.unbind("click");
        
        g_draggable(this.div, {
            start : function (event)
            {
                timer = setInterval(dragStarted, 10);
                
                that.dragging = true;
                that.trigger('dragstart', event);
            },
            stop : function (event)
            {
                window.clearInterval(timer);
                
                that.dragging = false;
                var imgPosition = that.div.position();
                var positionX = imgPosition.left + that._icon.iconAnchor.x;
				var positionY = imgPosition.top + that._icon.iconAnchor.y;
                that.coordinates = that._controller.converter.fromGeolayerPixels(positionX, positionY);
				
				if (moved)
				{
					that._controller.newStaticPosition();
				}
                that.trigger('dragstop', event);
            },
            drag : function (event)
            {
                var imgPosition = that.div.position();
                var positionX = imgPosition.left + that._icon.iconAnchor.x;
				var positionY = imgPosition.top + that._icon.iconAnchor.y;
                that.coordinates = that._controller.converter.fromGeolayerPixels(positionX, positionY);
				that.setPosition();
                that.trigger('drag', event);
            }
        });
        
        this.div.click(function (event)
        {
            that.trigger('click', event);
        });
    },
    /**
        Hide the marker.
    */
    hide : function ()
    {
        this.isHidden = true;
        if (g_isDefined(this.div))
        {
            this.div.hide();
            this.closePopUp();
        }
    },
    /**
        Show the marker.
    */
    show : function ()
    {
        this.isHidden = false;
        if (g_isDefined(this.div))
        {
            this.div.show();
        }
    },
    /**
        Get tail style
    */
    getTailStyle : function ()
    {
        return this._tailStyle;
    },
    /**
    @param {Mappy.api.map.shape.ShapeStyle} style Modify the style of the tail.
    */
    setTailStyle : function (style)
    {
        this._tailStyle = style;
    },
    /**
        @private
    */
    destroyTail : function ()
    {
        if (g_isDefined(this.tail))
        {
            this.tail.clean();
        }
    },
    /**
        @private
    */
    drawTail : function ()
    {
        this.destroyTail();

        if (g_isDefined(this.center))
        {
            if (this.center.x !== this.pos.x ||
                this.center.y !== this.pos.y)
            {
                this.center.round();
                this.pos.round();

                if (!this.tail)
                {
                    this.tail = g_getDrawer(this.div.parent(), 10, "line");
                    var style = this._tailStyle || new g_ShapeStyle({
                        lineWidth : 1,
                        strokeStyle : "FF000000"
                    });
                    this.tail.setStyle(style);
                }
                var drawBox = new g_Bounds(
                    this.center.x,
                    this.center.y,
                    this.center.x,
                    this.center.y
                );
                drawBox.addPoint(this.pos);
                this.tail.setBoundingBox(drawBox);
                this.center.px = this.center.x;
                this.center.py = this.center.y;
                this.pos.px = this.pos.x;
                this.pos.py = this.pos.y;
                this.tail.line([this.center, this.pos]);
            }
            delete this.center;
        }
    }
});


////
//// Cluster.js
//// 

var g_Cluster = g_Class(g_Marker, /** @lends Cluster.prototype */{
    markers : null,
    bounds : null,
    /**
        @constructs
        @augments Mappy.api.map.Marker
    */
    initialize : function (bounds, icon)
    {
        g_Marker.prototype.initialize.call(this, null, icon);
        this.markers = [];
        this.bounds = bounds;
    },
    /**
    @private
    */
    added : function (container, map)
    {
        this.getIcon().label = "x" + this.markers.length;
        for (var i = 0; i < this.markers.length; i += 1)
        {
            this.markers[i].isInCluster = true;
        }
        g_Marker.prototype.added.call(this, container, map);
    },
    /**
    @private
    */
    setPosition : function ()
    {
        var i, coords = [];
        for (i = 0; i < this.markers.length; i += 1)
        {
            coords.push(this.markers[i].coordinates);
        }
        
        var sum = new g_Coordinates(0, 0);
        sum._x = 0;
        sum._y = 0;
        
        for (i = 0; i < coords.length; i += 1)
        {
            sum._x += coords[i]._x;
            sum._y += coords[i]._y;
        }
        sum._x /= coords.length;
        sum._y /= coords.length;

        this.coordinates = g_Coordinates.fromNormalized(sum._x, sum._y);
        
        g_Marker.prototype.setPosition.apply(this);
    },
    /**
    @private
    */
    contains : function (marker)
    {
        return this.bounds.inside(marker.pos);
    },
    /**
    @private
    */
    removed : function ()
    {
        for (var i = 0; i < this.markers.length; i += 1)
        {
            this.markers[i].show();
            this.markers[i].isInCluster = false;
        }
        g_Marker.prototype.removed.call(this);
    }
});


////
//// conglomerate.js
//// 

function g_conglomerate(markers, icon, container, map, minInCluster)
{
    var gridSize = 100,
        minMarkersPerCluster = minInCluster || 5;
    var clusters = [];
    var i, j;

    function setClusters(bbox)
    {
        var i = 0, j = 0, exit = [false, false];

        do
        {
            do
            {
                clusters.push(
                    new g_Cluster(
                        new g_Bounds(
                            bbox.minX + i * gridSize,
                            bbox.minY + j * gridSize,
                            bbox.minX + (i + 1) * gridSize,
                            bbox.minY + (j + 1) * gridSize),
                        icon
                    )
                );

                if (bbox.minX + (i + 1) * gridSize <= bbox.maxX)
                {
                    i += 1;
                }
                else
                {
                    exit[0] = true;
                }
            }
            while (exit[0] === false);

            exit[0] = false;
            if (bbox.minY + (j + 1) * gridSize <= bbox.maxY)
            {
                j += 1;
                i = 0;
            }
            else
            {
                exit[1] = true;
            }
        }
        while (exit[1] === false);
    }


    if (markers.length > 0)
    {
        var mapSize = map.getSize();

        var bbox = new g_Bounds(0, 0, mapSize.width, mapSize.height);
        
        setClusters(bbox);
        
        for (i = 0; i < markers.length; i += 1)
        {
            for (j = 0; j < clusters.length; j += 1)
            {
                if (clusters[j].contains(markers[i]))
                {
                    clusters[j].markers.push(markers[i]);
                    break;
                }
            }
        }
        for (i = clusters.length - 1; i >= 0 ; i -= 1)
        {
            if (clusters[i].markers.length < minMarkersPerCluster)
            {
                clusters.splice(i, 1);
            }
            else
            {
                for (j = 0; j < clusters[i].markers.length; j += 1)
                {
                    clusters[i].markers[j].hide();
                }
                clusters[i].added(container, map.controller);
            }
        }
    }

    return clusters;
}


////
//// explode.js
//// 

function g_explode(markers)
{
    var avail_moves = [
            [0.0, -1.0],
            [-1.0,  0.0],
            [1.0,  0.0],
            [0.0,  1.0],
            [0.0,  0.0]
        ],
        move_alpha = 0.95,	// Movement multiplier on move success
        move_beta = 0.6,    // Movement multiplier on no-move
        move_gamma = 0.1,	// Movement low threshold stopping the iterations
        epsilon	= 1e-6,    // Epsilon value for numerical tests
        defaultWeights = {
            Canvas : 100.0,		        // Canvas edge push picto away
            PictoCenterDist: 3.0,		// Picto centers attract pictos
            PictoPictoOverlap: 3.0,	    // Pictos push pictos away on overlap
            PictoPictoRepulsion: 0.5,	// Pictos push pictos away on long-distance
            PictoCenterOverlap: 2.0,	    // Centers push other pictos away
            LineLineOverlap: 10.0,		// Lines must not overlap
            CenterProximity: 2.0		    // Pictos should be farther from other picto centers than their pictos.
        },
        m_radius = 20,
        m_tolerance = 12,
        lineWidth = 1;

    var dmove = 30;
    var markersCount = markers.length;
    var timerIterate;

    function isOverlapping(i)
    {
        for (var j = 0; j < markersCount; j += 1)
        {
            if (i !== j &&
                markers[i].center.dist(markers[j].center) < (markers[i].radius + markers[j].radius))
            {
                return true;
            }
        }
        return false;
    }

    function updatePolar(marker)
    {
        marker.r = marker.center.dist(marker.picto);
    }

    function intersect(p11, p12, p21, p22)
    {
        var p11X = p11.x,
            p11Y = p11.y,
            p12X = p12.x,
            p12Y = p12.y,
            p21X = p21.x,
            p21Y = p21.y,
            p22X = p22.x,
            p22Y = p22.y;

        var r =	((p11Y - p21Y) * (p22X - p21X) - (p11X - p21X) * (p22Y - p21Y)) /
            ((p12X - p11X) * (p22Y - p21Y) - (p12Y - p11Y) * (p22X - p21X));

        var s =	((p11Y - p21Y) * (p12X - p11X) - (p11X - p21X) * (p12Y - p11Y)) /
            ((p12X - p11X) * (p22Y - p21Y) - (p12Y - p11Y) * (p22X - p21X));

        return {
            test: !(r < 0 || r > 1 || s < 0 || s > 1),
            r: r,
            s: s
        };
    }

    function pointToLineDist(p, p1, p2)
    {
        var p1p2X = p2.x - p1.x,
            p1p2Y = p2.y - p1.y;

        //1- p1p2 is too small to compute anything from it, consider that p1 and p2
        //are the same.
        //2- p orthogonal projection is not in p1p2, the closest point is p1
        var p1pX = p.x - p1.x,
            p1pY = p.y - p1.y;
        if ((Math.abs(p1p2X) + Math.abs(p1p2Y)) < 2 * epsilon ||
            (p1pX * p1p2X + p1pY * p1p2Y) < 0)
        {
            return Math.sqrt(p1pX * p1pX + p1pY * p1pY);
        }

        //p orthogonal projection is not in p1p2, the closest point is p2

        var p2pX = p.x - p2.x,
            p2pY = p.y - p2.y;
        if ((p2pX * p1p2X + p2pY * p1p2Y) > 0)
        {
            return Math.sqrt(p2pX * p2pX + p2pY * p2pY);
        }

        return Math.abs(p1pX * p1p2Y - p1pY * p1p2X) / Math.sqrt(p1p2X * p1p2X + p1p2Y * p1p2Y);
    }

    function computePointScore(w, pos)
    {
        var score = 0.0, s;

        //The point must be close to its selected orbit
        s = (markers[pos].r - m_radius) / m_tolerance;
        s *= s * w.PictoCenterDist;
        score += s;
        var threshold, d_pp, d_pc, d_pl;

        for (var i = 0; i < markersCount; i += 1)
        {
            if (i !== pos)
            {
                //Picto should be away from each other
                d_pp = markers[pos].picto.dist(markers[i].picto);
                if (d_pp < 2 * m_radius)
                {
                    s = (1.0 - d_pp / (2 * m_radius));
                    s *= s * w.PictoPictoRepulsion;
                    score += s;
                }

                //Picto must not overlap
                if (d_pp < (markers[pos].radius + markers[i].radius))
                {
                    s = (1.0 - d_pp / (markers[pos].radius + markers[i].radius));
                    s *= s * w.PictoPictoOverlap;
                    score += s;
                }

                //Picto must not overlap other pictos center points
                threshold = 1.2 * markers[pos].radius;
                d_pc = markers[pos].picto.dist(markers[i].center);
                if (d_pc < threshold)
                {
                    s = (1.0 - d_pc / threshold);
                    s *= s * w.PictoCenterOverlap;
                    score += s;
                }

                if (lineWidth > 0)
                {
                    var test = true;
                    if (markers[pos].center.dist(markers[i].center) > lineWidth)
                    {
                        var intersectRes = intersect(markers[pos].center, markers[pos].picto, markers[i].center, markers[i].picto);
                        if (intersectRes.test)
                        {
                            //Lines intersect
                            //The scoring will depend on the length on line beyond the intersected
                            s = 1.0 + ((1.0 - intersectRes.r) * markers[pos].r) / (markers[pos].radius + lineWidth);
                            score += w.LineLineOverlap * s;
                        }
                        else
                        {
                            test = false;
                        }
                    }
                    else
                    {
                        test = false;
                    }

                    if (test === false)
                    {
                        //The lines do not intersect, test for picto/line overlap
                        d_pl = pointToLineDist(markers[pos].picto, markers[i].center, markers[i].picto);
                        threshold = 1.5 * markers[pos].radius + lineWidth;
                        if (d_pl < threshold)
                        {
                            s = (1.0 - d_pl / threshold);
                            s *= s * w.LineLineOverlap;
                            score += s;
                        }
                    }
                }
                else
                {
                    //No lines drawn, do not care about them
                }


                //Pictos must not be closer from other center than from theirs
                if (d_pc < markers[pos].r)
                {
                    s = (1.0 - d_pc / markers[pos].r);
                    s *= s * w.CenterProximity / markersCount;
                    score += s;
                }
            }
        }
        return score;
    }
    
    function iterate()
    {
        var i = 0, k;
        var iter_count = 50;
        var dmove_min = dmove * 0.1;

        var w = defaultWeights;
        /*
         * Iterate
         */
        var  move_sum = 0.0;

        for (k = 0; k < markersCount; k += 1)
        {
            if (markers[k].d > epsilon)
            {
                var orig = markers[k].picto.clone();
                var best_move = 0,
                    null_move = avail_moves.length - 1;
                var best_score = Number.MAX_VALUE;

                for (var j = 0; j < avail_moves.length; j += 1)
                {
                    markers[k].picto.x =  orig.x + markers[k].d * avail_moves[j][0];
                    markers[k].picto.y =  orig.y + markers[k].d * avail_moves[j][1];
                    updatePolar(markers[k]);
                    var score = computePointScore(w, k);

                    if (score < best_score)
                    {
                        best_move = j;
                        best_score = score;
                    }
                }

                if (best_move !== null_move)
                {
                    markers[k].picto.x =  orig.x + markers[k].d * avail_moves[best_move][0];
                    markers[k].picto.y =  orig.y + markers[k].d * avail_moves[best_move][1];
                    updatePolar(markers[k]);
                    markers[k].d *= move_alpha;

                    move_sum += markers[k].d;
                }
                else
                {
                    //Reduce the step
                    markers[k].d *= move_beta;
                }
            }
        }

        move_sum /= markersCount;

        if (move_sum > dmove_min && ++i !== iter_count)
        {
            timerIterate = g_executeDelayed(iterate, this);
        }
        else
        {
            for (i = 0; i < markersCount; i += 1)
            {
                var icon = markers[i].getIcon();
                markers[i].pos.x = markers[i].picto.x - icon.size.width * 0.5 + icon.iconAnchor.x;
                markers[i].pos.y = markers[i].picto.y - icon.size.height * 0.5 + icon.iconAnchor.y;
                markers[i].setMarkerPosition();
                markers[i].drawTail();
            }
        }
    }
    
    function placeGreedy_delayed()
    {
        var i;

        clearTimeout(timerIterate);
        /*
         * Prepare markers
         */
        for (i = 0; i < markersCount; i += 1)
        {
            var icon = markers[i].getIcon();
            markers[i].radius = 0.5 * icon.size.hypot();
            markers[i].center = markers[i].pos.clone();
            markers[i].picto = new g_Point(markers[i].pos.x - icon.iconAnchor.x + icon.size.width * 0.5,
                                           markers[i].pos.y - icon.iconAnchor.y + icon.size.height * 0.5);

            updatePolar(markers[i]);
            markers[i].d = dmove;
        }
        for (i = 0; i < markersCount; i += 1)
        {
            if (isOverlapping(i) === false)
            {
                markers[i].d = 0;
            }
        }

        timerIterate = g_executeDelayed(iterate, this);
    }

    function placeGreedy()
    {
        var move_sum;
        var i = 0, k;
        var iter_count = 50;
        var dmove_min = dmove * 0.1;

        var w = defaultWeights;
        /*
        Algo begin
        */

        /*
         * Prepare markers
         */
        var icon;
        for (i = 0; i < markersCount; i += 1)
        {
            icon = markers[i].getIcon();
            markers[i].radius = 0.5 * icon.size.hypot();
            markers[i].center = markers[i].pos.clone();
            markers[i].picto = new g_Point(markers[i].pos.x - icon.iconAnchor.x + icon.size.width * 0.5,
                                           markers[i].pos.y - icon.iconAnchor.y + icon.size.height * 0.5);

            updatePolar(markers[i]);
            markers[i].d = dmove;
        }
        for (i = 0; i < markersCount; i += 1)
        {
            if (isOverlapping(i) === false)
            {
                markers[i].d = 0;
            }
        }

        /*
         * Iterate
		 */
        do
        {
            move_sum = 0.0;

            for (k = 0; k < markersCount; k += 1)
            {
                if (markers[k].d > epsilon)
                {
                    var orig = markers[k].picto.clone();
                    var best_move = 0,
                        null_move = avail_moves.length - 1;
                    var best_score = Number.MAX_VALUE;

                    for (var j = 0; j < avail_moves.length; j += 1)
                    {
                        markers[k].picto.x =  orig.x + markers[k].d * avail_moves[j][0];
                        markers[k].picto.y =  orig.y + markers[k].d * avail_moves[j][1];
                        updatePolar(markers[k]);
                        var score = computePointScore(w, k);

                        if (score < best_score)
                        {
                            best_move = j;
                            best_score = score;
                        }
                    }

                    if (best_move !== null_move)
                    {
                        markers[k].picto.x =  orig.x + markers[k].d * avail_moves[best_move][0];
                        markers[k].picto.y =  orig.y + markers[k].d * avail_moves[best_move][1];
                        updatePolar(markers[k]);
                        markers[k].d *= move_alpha;

                        move_sum += markers[k].d;
                    }
                    else
                    {
                        //Reduce the step
                        markers[k].d *= move_beta;
                    }
                }
            }

            move_sum /= markersCount;
        }
        while (move_sum > dmove_min && ++i !== iter_count);

        for (i = 0; i < markersCount; i += 1)
        {
            icon = markers[i].getIcon();
            markers[i].pos.x = markers[i].picto.x - icon.size.width * 0.5 + icon.iconAnchor.x;
            markers[i].pos.y = markers[i].picto.y - icon.size.height * 0.5 + icon.iconAnchor.y;
            markers[i].setMarkerPosition();
            markers[i].drawTail();
        }
    }

    placeGreedy_delayed();
    
}


////
//// grid.js
//// 

function g_grid(markers, map)
{
    var delta = map.getCenter();
    
    function placerPastille (marker)
    {
        var posX = marker.pos.x + delta.x;
        var posY = marker.pos.y + delta.y;
        var col = Math.round(posX / _sizeParPast);
        var lig = Math.round(posY / _sizeParPast);
        
        if (col >= 0 && col < _pastilleGrilleTaille && lig >= 0 && lig < _pastilleGrilleTaille)
        {
            if (_pastillesPositions[col][lig] == null)
            {
                _pastillesPositions[col][lig] = marker;
            }
            else
            {
                var trouve = false;
                for (var z = 1; z <= 2 && !trouve; z++) {
                    for (var y = -z; y <= z && !trouve; y++) {
                        for (var x = -z; x <= z && !trouve; x++) {
                            if (col + x >= 0 && col + x < _pastilleGrilleTaille && lig + y >= 0 && lig + y < _pastilleGrilleTaille && _pastillesPositions[col + x][lig + y] == null) {
                                col = col + x;
                                lig = lig + y;
                                _pastillesPositions[col][lig] = marker;
                                trouve = true;
                            }
                        }
                    }
                }
            }
            marker.pos.x = col * _sizeParPast - delta.x;
            marker.pos.y = lig * _sizeParPast - delta.y;
            marker.setMarkerPosition();
        }
    }

    if (markers.length > 0)
    {
        var marker = markers[0];
        var iconSize = marker.getIcon().size;
        var _pastilleLargeurPx = iconSize.width;
        var _pastilleHauteurPx = iconSize.height;
        var _sizeParPast = _pastilleLargeurPx + 2;

        var _tailleCartePx = map.getSize().width;
        var _pastilleGrilleTaille = Math.ceil(_tailleCartePx / _sizeParPast) + 1;;

        if (_pastilleGrilleTaille > 0)
        {
            var _pastillesPositions = new Array(_pastilleGrilleTaille);
            for (var i = 0; i < _pastilleGrilleTaille; i += 1)
            {
                _pastillesPositions[i] = new Array(_pastilleGrilleTaille);
            }
            var past = null;
            for (i = 0; i < markers.length; i += 1)
            {
                past = markers[i];
                placerPastille(past);
            }
        }
    }
}


////
//// PopUp.js
//// 

var g_PopUp = g_Class(/** @lends PopUp.prototype */{
    div : null,
    isOnMap : false,
    /**
        @constructs
        @private
    */
    initialize : function (html, marker)
    {
        this.html = html;
        this.marker = marker;
    },
    setPopUpPosition : function (pos)
    {
        var popUpOptions = this.marker.getPopUpOptions();
        var icon = this.marker.getIcon();

        var anchorPoint = popUpOptions.getAnchorPoint(this._controller, this.size, icon, pos);

        this.div.css({
            position : "absolute",
            left : pos.x + anchorPoint.x,
            top : pos.y + anchorPoint.y
        });
    },
    slideTo : function (pos)
    {
        if (this.isOnMap)
        {
            var popUpOptions = this.marker.getPopUpOptions();
            var icon = this.marker.getIcon();
            var anchorPoint = popUpOptions.getAnchorPoint(this._controller, this.size, icon, pos);
            var popUpPos = new g_Point(pos.x + anchorPoint.x, pos.y + anchorPoint.y);
            
            /*
            Bounds of the popup
            */
            var bounds = new g_Bounds(popUpPos.x, popUpPos.y, popUpPos.x + this.size.width, popUpPos.y + this.size.height);
            /*
            Add the icon in bounds
            */
            bounds.addPoint(new g_Point(pos.x - icon.iconAnchor.x, pos.y - icon.iconAnchor.y));
            bounds.addPoint(new g_Point(pos.x - icon.iconAnchor.x + icon.size.width, pos.y - icon.iconAnchor.y + icon.size.height));
            
            /*
            Slide
            */
            this._controller.slideToBounds(bounds, "popup");
        }
    },
    added : function (controller)
    {
        this._controller = controller;

        var popUpOptions = this.marker.getPopUpOptions();
        var popUpResult = popUpOptions.createPopUp(this.html, controller.popuplayer.div, this.marker);
        this.div = popUpResult.div;
        this.size = popUpResult.size;
        this.isOnMap = true;
    },
    removed : function ()
    {
        this.div.remove();
        this.isOnMap = false;
    }
});


////
//// PopUpOptions.js
//// 

var g_PopUpOptions = MappyApi.map.PopUpOptions = g_Class(/** @lends Mappy.api.map.PopUpOptions.prototype */{
    /**
    @private
    */
    TEMPLATE : '<div class="popup"><div class="popup-nw"><img src="{imagePath}/popup/popup-sprite.png"/></div><div class="popup-sw"><img src="{imagePath}/popup/popup-sprite.png"/></div><div class="popup-ne"><img src="{imagePath}/popup/popup-sprite.png"/></div><div class="popup-se"><img src="{imagePath}/popup/popup-sprite.png"/></div><div class="popup-beak"><img src="{imagePath}/popup/popup-sprite.png"/></div><div class="popup-content"></div><div class="popup-close"></div></div>',
    /**
    @private
    */
    mappyDecoration : false,
    /**
    @private
    */
    left : null,
    /**
    @private
    */
    right : null,
    /**
    @private
    */
    top : null,
    /**
    @private
    */
    bottom : null,
    /**
    @private
    */
    autoLayout : false,
    /**
        @constructs
        @param {Object} options Contains informations on the pop up style. Each attributes are optionals. Example :<br/>
        {<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; mappyDecoration: {boolean}, // Do you want to use mappy style? (default true)<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; autoLayout : {boolean}, // Do you want to reposition the popup automatically in your map?<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; left: {number} or "auto", // Distance betwen the popup anchor and the left of your popUp ("auto" will calculate the middle width of your popup)<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; top: {number} or "auto", // Distance betwen the popup anchor and the top of your popUp ("auto" will calculate the middle height of your popup)<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; right: {number} or "auto", // Distance betwen the popup anchor and the right of your popUp ("auto" will calculate the middle width of your popup)<br/><br/>
        &nbsp;&nbsp;&nbsp;&nbsp; bottom: {number} or "auto" // Distance betwen the popup anchor and the bottom of your popUp ("auto" will calculate the middle height of your popup)<br/>
        }
    */
    initialize : function (options)
    {
        options = options || {};

        this.mappyDecoration = options.mappyDecoration;

        if (this.mappyDecoration)
        {
            this.bottom = 0;
            this.left = "auto";
        }
        else
        {
            this.autoLayout = options.autoLayout;
            this.left = options.left;
            this.right = options.right;
            this.top = options.top;
            this.bottom = options.bottom;
        }
    },
    /**
    @private
    */
    getAnchorPoint : function (controller, size, icon, position)
    {
        var newAnchor;
        if (this.autoLayout)
        {
            newAnchor = new g_Point(icon.size.width - icon.iconAnchor.x, - icon.iconAnchor.y);
            var padding = controller.model.getPadding();
            /*
            Calc relative position (default position)
            */
            var relativePos = controller.view.position();
            
            relativePos.left += position.x + newAnchor.x;
            relativePos.top += position.y + newAnchor.y;
            
            /*
            Calc container size with padding
            */
            var containerSize = controller.model.getSize();

            if (relativePos.left + size.width > containerSize.width - padding.right &&
                relativePos.left - size.width - icon.size.width > padding.left)
            {
                newAnchor.x -= size.width + icon.size.width;
            }

            if (relativePos.top + size.height > containerSize.height - padding.bottom)
            {
                if (size.height > containerSize.height - padding.top - padding.bottom)
                {
                    newAnchor.y -= relativePos.top - padding.top;
                }
                else
                {
                    newAnchor.y -= relativePos.top + size.height - containerSize.height + padding.bottom;
                }
            }
        }
        else
        {
            newAnchor = new g_Point(icon.popUpAnchor.x - icon.iconAnchor.x, icon.popUpAnchor.y - icon.iconAnchor.y);

            if (g_isDefined(this.left))
            {
                if (this.left === "auto")
                {
                    newAnchor.x -= size.width / 2;
                }
                else
                {
                    newAnchor.x -= this.left;
                }
            }
            if (g_isDefined(this.right))
            {
                if (this.right === "auto")
                {
                    newAnchor.x -= size.width / 2;
                }
                else
                {
                    newAnchor.x += this.right - size.width;
                }
            }
            if (g_isDefined(this.top))
            {
                if (this.top === "auto")
                {
                    newAnchor.y -= size.height / 2;
                }
                else
                {
                    newAnchor.y -= this.top;
                }
            }
            if (g_isDefined(this.bottom))
            {
                if (this.bottom === "auto")
                {
                    newAnchor.y -= size.height / 2;
                }
                else
                {
                    newAnchor.y += this.bottom - size.height;
                }
            }
        }
        return newAnchor;
    },
    /**
    @private
    */
    createPopUp : function (html, popUpLayerDiv, marker)
    {
        /*
        Not to loose events. Test if it's a string because g_jQuery won't work on a string without tags.
        */
        if (typeof html !== "string")
        {
            html = g_jQuery(html).clone(true);
        }

        var div, size;
        if (this.mappyDecoration)
        {
            var tpl = g_fillTemplate(
                this.TEMPLATE,
                {
                    imagePath : '..images/' + ((g_isIE6) ? 'img_png8' : 'img')
                }
            );
            div = g_jQuery(tpl);

            //popUpLayerDiv.append(div);
            div.appendTo(popUpLayerDiv);

            var content = div.find('.popup-content');
            content.html(html);

            var totalSize = g_getSize(content, true);

            div.width(totalSize.width);
            div.height(totalSize.height);
            
            var internalWidth = totalSize.width - 23;
            var internalHeight = totalSize.height - 22;

            div.find('.popup-nw').width(internalWidth);
            div.find('.popup-sw').width(internalWidth);
            div.find('.popup-sw').height(internalHeight);
            div.find('.popup-se').height(internalHeight);

            var beak = div.find('.popup-beak');
            var beakSize = new g_Size(23, 49);
            var totalCenter = totalSize.getCenter();
            var beakCenter = beakSize.getCenter();
            
            beak.css('left', Math.round(totalCenter.x - beakCenter.x));
            beak.css('top', totalSize.height - 5);

            size = new g_Size(totalSize.width, totalSize.height + beakSize.height - 5);

            div.find('.popup-close').click(function (event)
            {
                marker.closePopUp();
            });
        }
        else
        {
            div = g_jQuery('<div class="popup"></div>');
            div.html(html);
            //popUpLayerDiv.append(div);
            div.appendTo(popUpLayerDiv);
            size = g_getSize(div, true);
        }
        
        div.click(function(event)
        {
            event.stopPropagation();
        });
        
        div.mousemove(function(event)
        {
            event.stopPropagation();
        });
            
        return {
            div : div,
            size : size
        };
    }
});


////
//// Map.js
//// 

var g_Map = MappyApi.map.Map = g_Class(g_EventSource, /** @lends Mappy.api.map.Map.prototype */{
    /**
    List of events the map can trigger :<br/>
    - changestart<br/>
    - changeend<br/>
    - zoomstart<br/>
    - zoomend<br/>
    - drag<br/>
    - dragstart<br/>
    - dragstop<br/>
    - mousewheel<br/>
    - click<br/>
    - dblclick<br/>
    - rightclick<br/>
    - mouseup<br/>
    - mousedown<br/>
    - mousemove<br/>
    - mouseover<br/>
    - mouseout<br/>
    - resize<br/>
    - controlchanged<br/>
    - touchstart<br/>
    - touchmove<br/>
    - touchend<br/>
    - gestureend<br/>
    - gesturestart<br/>
    - refreshdescr<br/>
    - bgpoiclick<br/>
    - startrefreshviewmode<br/>
    - refreshviewmode<br/>
    - stoprefreshviewmode<br/>
    - viewmodechanged<br/>
    @type String[]
    @static
    */
    EVENTS: [
        "changestart",
        "changeend",
        "zoomstart",
        "zoomend",
        "drag",
        "dragstart",
        "dragstop",
        "mousewheel",
        "click",
        "dblclick",
        "rightclick",
        "mouseup",
        "mousedown",
        "mousemove",
        "mouseover",
        "mouseout",
        "beforeresize",
        "resize",
        "controlchanged",
        "touchstart",
        "touchmove",
        "touchend",
        "gestureend",
        "gesturechange",
        "gesturestart",
        "refreshdescr",
        "bgpoiclick",
		"startrefreshviewmode",
		"refreshviewmode",
		"stoprefreshviewmode",
		"viewmodechanged"
    ],
    /**
        Is the map ready?
        @type boolean
    */
    isReady : false,
    /**
        Does the map have inertia?
        @type boolean
    */
    hasInertia : false,
    /**
        Is the map dragged?
        @type boolean
    */
    dragging : false,
	/**
        Map container g_jQuery object.
        @type g_jQueryObject
		@deprecated
    */
    div : null,
    /**
        @private
    */
    _features: {
        "draggable": true,
        "dblClickZoom": true,
        "scrollWheelZoom": true,
        "zoomSelection": false,
        "tooltipsOnClick": false
    },
	/**
        @constructs
        @augments EventSource
        @param {Object}options option object : <br/>
        - {DOMNode} container The container div.<br/>
        - container The container div.<br/>
        - {Object} features (Optional) Same options as setFeatures<br/>
        - {Mappy.api.map.ViewMode} viewmode (Optional) Same options as setViewMode.<br/>
        - {Array} center (Optional) First value is a Mappy.api.geo.Coordinates  for the position and the second is a Number for the zoom level.<br/>
        - {Object} background (Optional) Same options as 2nd param.
        @param {Object} background (Optional) Background description. Should have one of these attributes : <br/>
        - css : CSS background style ("url('/img.jpg') no-repeat top left").<br />
        - image : Image background to use. By default, it'll be repeated on the whole map.<br />
        - color : Color background to use.
        
        @eventdescription click Fires when a click occurs on the map
        @eventdescription#click {MouseEvent} e Native onclick event on the map.
        @eventdescription#click {Mappy.api.geo.Coordinates} c Coordinates of the click.
        
        @eventdescription dblclick Fires when a double click occurs on the map
        @eventdescription#dblclick {MouseEvent} e Native ondblclick event on the map.
        @eventdescription#dblclick {Mappy.api.geo.Coordinates} c Coordinates of the double click.
        
        @eventdescription mouseup
        @eventdescription#mouseup {MouseEvent} e
        @eventdescription#mouseup {Mappy.api.geo.Coordinates} c Coordinates.
        
        @eventdescription mousedown
        @eventdescription#mousedown {MouseEvent} e
        @eventdescription#mousedown {Mappy.api.geo.Coordinates} c Coordinates.
        
        @eventdescription rightclick
        @eventdescription#rightclick {MouseEvent} e
        @eventdescription#rightclick {Mappy.api.geo.Coordinates} c Coordinates.
        
        @eventdescription mouseover
        @eventdescription#mouseover {MouseEvent} e
        
        @eventdescription mouseout
        @eventdescription#mouseout {MouseEvent} e
        
        @eventdescription mousemove
        @eventdescription#mousemove {MouseEvent} e
        
        @eventdescription resize
        @eventdescription#resize {Event} e
        
        @eventdescription beforeresize
        @eventdescription#beforeresize {Event} e
        
        
        @eventdescription mousewheel
        @eventdescription#mousewheel {Event} e
        @eventdescription#mousewheel {Number} delta
        
        @eventdescription changestart Fires when something is about to change on the map. Like new setCenter, zoom, slide or view mode.
        @eventdescription#changestart {String} name String defining the change source. Possible values : newDisplayArea, slide or viewmode.
        @eventdescription#changestart {String} from If the change comes from a slide, defined if the slide comes from a popup reposition or a user slide. Possible values : popup, undefined.
        
        @eventdescription changeend Fires when something has changed on the map. Like new setCenter, zoom, slide or view mode.
        @eventdescription#changeend {String} name String defining the change source. Possible values : newDisplayArea, slide or viewmode.
        @eventdescription#changeend {String} from If the change comes from a slide, defined if the slide comes from a popup reposition or a user slide. Possible values : popup, undefined.

        @eventdescription zoomstart Fires when a zoom animation begins.
        
        @eventdescription zoomend Fires when a zoom animation ends.

        @eventdescription drag
        @eventdescription#drag {MouseEvent} e
        
        @eventdescription dragstart
        @eventdescription#dragstart {MouseEvent} e
        
        @eventdescription dragstop
        @eventdescription#dragstop {MouseEvent} e
        
        @eventdescription touchmove
        @eventdescription#touchmove {TouchEvent} e
        
        @eventdescription touchstart
        @eventdescription#touchstart {TouchEvent} e
        
        @eventdescription touchend
        @eventdescription#touchend {TouchEvent} e
        
        @eventdescription gesturestart
        @eventdescription#gesturestart {GestureEvent} e
        
        @eventdescription gestureend
        @eventdescription#gestureend {GestureEvent} e
        
        @eventdescription gesturechange
        @eventdescription#gesturechange {GestureEvent} e
               
        @eventdescription controlchanged
        @eventdescription#controlchanged {String} name Name of the control that changed
        
    */
	initialize : function (options, background)
    {
        background || (background = options.background);
        var features = options.features || {};
        
		g_EventSource.prototype.initialize.apply(this);

		var container = g_jQuery(options.container);
		this.controller = new g_MapController(this, container, background);
		
		// /* Creating ToolManager */
        this.toolManager = new g_ToolManager(this);

        // /* Creating default tools */
        this.copyrights = new g_Copyrights();
        this.addTool(this.copyrights);
		
		this.div = this.controller.view.div;
        
        if (!g_isEmpty(features))
            this.setFeatures(features);
        
        if (!g_isEmpty(options.viewmode))
            this.setViewMode(options.viewmode);
        
        if (!g_isEmpty(options.center) && options.center.length == 2)
            this.setCenter(options.center[0], options.center[1]);
    },
	/**
        Set the center of the map at the given coordinates and zoom level.
        @param {Mappy.api.geo.Coordinates} coordinates
        @param {Number} zoom
    */
	setCenter : function(coordinates, zoomLevel)
	{
		this.controller.setState(coordinates, zoomLevel);
	},
	/**
        Set the features of the map
        @param {Object} features Available features are: "draggable", "dblClickZoom", "scrollWheelZoom", "zoomSelection", "tooltipsOnClick".<br />
        The features are set with a boolean, example: { "draggable": false }.
    */
	setFeatures : function(features)
	{
        for (var name in this._features)
        {
            if (features.hasOwnProperty(name))
            {
                var action = (features[name] === true) ? "enable" : "disable";
                this[action + g_ucfirst(name)]();
                this._features[name] = features[name];
            }
        }
	},
	/**
        Set a feature of the map
        @param {String} name Available features are: "draggable", "dblClickZoom", "scrollWheelZoom", "zoomSelection", "tooltipsOnClick".
        @param {Boolean} value Enable or disable a feature.
    */
	setFeature : function(name, value)
	{
        if (this._features.hasOwnProperty(name) && typeof value === "boolean")
        {
            var action = (value === true) ? "enable" : "disable";
            this[action + g_ucfirst(name)]();
            this._features[name] = value;
        }
	},
	/**
        get the features of the map
    */
	getFeatures : function(features)
	{
        return this._features;
	},
	/**
        Get a feature of the map
        @param {String} name Available features are: "draggable", "dblClickZoom", "scrollWheelZoom", "zoomSelection", "tooltipsOnClick".
    */
	getFeature : function(name)
	{
        if (g_isDefined(this._features[name]))
        {
            return this._features[name];
        }
        return "undefined";
	},
	/**
        refresh the size of the map
    */
	resize : function()
	{
		this.controller.resize(this.div.width(), this.div.height());
	},
	/**
        @returns {Mappy.api.geo.Coordinates} Coordinates of the center of the map.
    */
    getCenter : function ()
    {
        return this.controller.getCenter();
    },
	/**
	    Set whether the map should be animated during the zoom.
        @param {Boolean} zoomAnimation If TRUE, the map will have a zoom-in/out animation during the zoom.
    */
	setZoomAnimation : function (zoomAnimation)
	{
		this.controller.setZoomAnimation(zoomAnimation);
	},
	/**
        Define whether the map should be animated during the zoom.
        @return {Boolean} TRUE if the map will have a zoom-in/out animation during the zoom.
    */
	getZoomAnimation : function ()
	{
		return this.controller.getZoomAnimation();
	},
	/**
        Set the map at the current coordinates and at the given zoom level.
        @param {Number} zoomLevel
    */
	setZoomLevel : function (zoom)
	{
		this.controller.setZoom(zoom);
	},
	/**
        Returns the current zoom level.
        @returns {Number} zoomLevel
    */
    getZoomLevel : function ()
    {
        return this.controller.model.getZoom();
    },
	/**
        Add a layer in the map.
        @param {Layer} layer A layer
    */
	addLayer : function (layer)
	{
		this.controller.addLayer(layer);
	},
	/**
        Remove a layer from the map.
        @param {Layer} layer A layer
    */
    removeLayer : function (layer)
    {
        this.controller.removeLayer(layer);
    },
	/**
        Add a tool in the map.
        @param {Mappy.api.map.Tool} tool A tool
    */
    addTool : function (tool)
    {
        this.toolManager.addTool(tool);
    },
    /**
        Remove a tool from the map.
        @param {Mappy.api.map.Tool} tool A tool
    */
    removeTool : function (tool)
    {
        this.toolManager.removeTool(tool);
    },
	/**
        @returns {Mappy.api.geo.GeoBounds} Bounds of the visible zone.
    */
    getVisibleBounds : function ()
    {
        var size = this.controller.model.getSize();
		var padding = this.controller.model.getPadding();
        return new g_GeoBounds(
            this.controller.converter.fromPixels(size.width - padding.right, padding.top),
            this.controller.converter.fromPixels(padding.left, size.height - padding.bottom)
        );
    },
	/**
        Retrun the best zoom level for the given bounds.
        @param {Mappy.api.geo.GeoBounds} bounds A bounds
        @returns {Number} zoom level
    */
    getBoundsZoomLevel : function (bounds)
    {
        return this.controller.model.getBoundsZoomLevel(bounds);
    },
    /**
        Slide the map by the given number of pixel.
        @param {Mappy.api.types.Point} pt
    */
    slideBy : function (pt)
    {
        this.controller.slideBy(pt.x, pt.y);
    },
	/**
        Slide to set the given coordinates at the center of the map.
        @param {Mappy.api.geo.Coordinates} coords
    */
    slideTo : function (coords)
    {
        if (this.isReady)
        {
            this.controller.slideTo(coords);
        }
    },
    /**
        Zoom in at center
    */
	zoomIn : function ()
	{
		this.controller.setZoom(
            this.controller.model.getZoom() + 1
        );
	},
    /**
        Zoom out at center
    */
	zoomOut : function ()
	{
		this.controller.setZoom(
            this.controller.model.getZoom() - 1
        );
	},
    /**
        Returns the current view mode.
        @returns {Mappy.api.map.ViewMode} View mode.
    */
    getViewMode : function ()
    {
        return this.controller.model.getViewMode();
    },
    /**
        Set the view mode
        @param {Mappy.api.map.ViewMode} View mode.
    */
    setViewMode : function (viewmode)
    {
        this.controller.setViewMode(viewmode);
    },
    /**
        Returns the size of the map
        @returns {Mappy.api.types.Size}
    */
    getSize : function ()
    {
        return this.controller.model.getSize();
    },
	/**
        Set the padding of the map.
        @param {Object} padding An object containing n, s, e or/and w properties.
    */
    setPadding : function (padding)
    {
		var newPadding = {}
		for (var i in padding)
		{
			if (padding.hasOwnProperty(i))
			{
				if (i === "n")
				{
					newPadding.top = padding[i];
				}
				if (i === "s")
				{
					newPadding.bottom = padding[i];
				}
				if (i === "e")
				{
					newPadding.right = padding[i];
				}
				if (i === "w")
				{
					newPadding.left = padding[i];
				}
			}
		}
        this.controller.setPadding(newPadding);
    },
    /**
        Returns the padding of the map.
        @returns {Object} An object containing n, s, e or/and w properties.
    */
    getPadding : function ()
    {
		var padding = this.controller.model.getPadding();
        return {
			n : padding.top,
			s : padding.bottom,
			e : padding.right, 
			w :	padding.left
		};
    },
	/**
        Make the map undraggable.
    */
    disableDraggable : function ()
    {
        this.controller.disableDraggable();
    },
    /**
        Make the map draggable.
    */
    enableDraggable : function ()
    {
        this.controller.enableDraggable();
    },
	/**
        Enable the zoom triggered by the double click.
    */
    enableDblClickZoom : function ()
    {
        if (this.controller.dblclickZoom === false)
        {
            this.controller.dblclickZoom = true;
            this.trigger("controlchanged", "dblClickZoom");
            this._features.dblclickZoom = true;
        }
    },
    /**
        Disable the zoom triggered by the double click.
    */
    disableDblClickZoom : function ()
    {
        if (this.controller.dblclickZoom === true)
        {
            this.controller.dblclickZoom = false;
            this.trigger("controlchanged", "dblClickZoom");
            this._features.dblclickZoom = false;
        }
    },
    /**
        Is double click zoom enabled?
        @returns {boolean}
    */
    isDblClickZoomEnabled : function ()
    {
        return this.controller.dblclickZoom;
    },
	/**
        Enable the zoom triggered by the mousewheel.
    */
    enableScrollWheelZoom : function ()
    {
		if (this.controller.mousewheelZoom === false)
        {
            this.controller.mousewheelZoom = true;
            this.trigger("controlchanged", "scrollWheelZoom");
            this._features.scrollWheelZoom = true;
        }
    },
    /**
        Disable the zoom triggered by the mousewheel.
    */
    disableScrollWheelZoom : function ()
    {
        if (this.controller.mousewheelZoom === true)
        {
            this.controller.mousewheelZoom = false;
            this.trigger("controlchanged", "scrollWheelZoom");
            this._features.scrollWheelZoom = false;
        }
    },
    /**
        Is mousewheel zoom enabled?
        @returns {boolean}
    */
    isScrollWheelZoomEnabled : function ()
    {
        return this.controller.mousewheelZoom;
    },
    
	/**
        Enable the zoom triggered by the mouse selection.
    */
    enableZoomSelection : function ()
    {
		if (this.controller.zoomSelection === false)
        {
            this.controller.enableZoomSelection();
            this.trigger("controlchanged", "selectionZoom");
            this._features.zoomSelection = true;
        }
    },
    /**
        Disable the zoom triggered by the mouse selection.
    */
    disableZoomSelection : function ()
    {
        if (this.controller.zoomSelection === true)
        {
            this.controller.disableZoomSelection();
            this.trigger("controlchanged", "selectionZoom");
            this._features.zoomSelection = false;
        }
    },
    
    /**
        Enable the opening of the background pois' tooltips on click
    */
    enableTooltipsOnClick : function ()
    {
        if (this.controller.tooltipsOnClick === false)
        {
            this.controller.enableTooltipsOnClick();
            this.trigger("controlchanged", "tooltipsOnClick");
            this._features.tooltipsOnClick = true;
        }
    },
    /**
        Disable the opening of the background pois' tooltips on click
    */
    disableTooltipsOnClick : function ()
    {
        if (this.controller.tooltipsOnClick === true)
        {
            this.controller.disableTooltipsOnClick();
            this.trigger("controlchanged", "tooltipsOnClick");
            this._features.tooltipsOnClick = false;
        }
    },
    
    /**
        Is zoom selection enabled?
        @returns {boolean}
    */
    isZoomSelectionEnabled : function ()
    {
        return this.controller.zoomSelection;
    },
	 /**
        Enable the zoom triggered by the gesture.
    */
	enableGestureZoom: function ()
	{
		if (this.controller.gestureZoom === false)
        {
            this.controller.gestureZoom = true;
            this.trigger("controlchanged", "gestureZoom");
        }
    },	
	/**
        Disable the zoom triggered by the gesture.
    */
	disableGestureZoom: function ()
	{
		if (this.controller.gestureZoom === true)
        {
            this.controller.gestureZoom = false;
            this.trigger("controlchanged", "gestureZoom");
        }
	},    
	/**
        Set the position of the logo.
        @param {Object} position An object containing left, top, right or left property.
        @since version 2.02
    */
    setLogoPosition : function (position)
    {
        this.controller.view.div.find("[name=logo]").css(position);
    },
	/**
    Destroy the map. Remove all tags from the container.
    */
    destroy : function ()
    {
        this.controller.view.destroy();
    },
	/**
        @param {Mappy.api.geo.Coordinates[]} coords Array of coordinates.
        @returns {Mappy.api.geo.GeoBounds} GeoBounds of the given list after projection.
		@deprecated
    */
    getBounds : function (coords)
    {
        if (!g_jQuery.isArray(coords) || coords.length === 0)
        {
            return null;
        }
        
        var bounds = new g_GeoBounds(coords[0], coords[0]);
        for (var i = 0; i < coords.length; i += 1)
        {
            bounds.extend(coords[i]);
        }
        bounds.refreshCenter();
        return bounds;
    },
	/**
        add an other viewMode to the map
        @param {Mappy.api.map.ViewMode} View mode.
    */
    addTileLayer : function (viewMode, zindex)
    {
        this.controller.addTileLayer(viewMode, (zindex || 10));
    },
	/**
        remove a specific viewMode from the map
        @param {String} viewModeName View mode name to remove.
    */
    removeTileLayer : function (viewModeName)
    {
        this.controller.removeTileLayer(viewModeName);
    },
	/**
        remove all viewMode layers from the map
        @param {Mappy.api.map.ViewMode} View mode.
    */
    getTileLayers : function ()
    {
        return this.controller.getTileLayers();
    }
	/**
        Hide the traffic layer if it is currently displayed
        @param {Mappy.api.map.ViewMode} View mode.
    */
    , hideTraffic : function ()
    {
		this.controller.hideTraffic();
    }
	/**
        Show the traffic layer.
        @param {Boolean} events Show or hide events.
    */
    , showTraffic : function (events)
    {
		this.controller.showTraffic(events);
    }
	/**
        Returns background POIs from the mapdescr
        @private
    */
    , getBgPois : function ()
    {
		return this.controller.getBgPois();
    }
});


////
//// ZoomAnimation.js
//// 

var g_ZoomAnimation = g_Class(/** @lends ZoomAnimation.prototype */{
    /**
        @constructs
        @private
    */
    initialize : function (view)
    {
        this.view = view;
		this.duration = 500;
		this.durationReciprocal = 1/500;
		this.finished = true;
		this.currScale = 1;
		this.delta = 0;
    },
	reset : function ()
	{
		this.startScale = 1;
		this.currScale = 1;	
		this.deltaZoom = 0;
	},
	zoom : function (delta, screenX, screenY)
	{
		if (this.finished)
		{
			this.startViewPosition = this.view.position();
			this.view.createBG();
			this.screenX = screenX;
			this.screenY = screenY;
			this.reset();
		}
		
		this.deltaZoom += delta;

		this.startAnimation(this.currScale, Math.pow(3, this.deltaZoom));
		
	},
	startAnimation : function (startScale, endScale)
	{
		this.finished = false;
    	this.startScale = startScale;
    	this.finalScale = endScale;
    	this.startTime = new Date().getTime();
	},
	stepAnimation : function ()
	{
		if (this.finished)
		{
			return false;
		}
		
		var timePassed = new Date().getTime() - this.startTime;
		
		if (timePassed < this.duration)
    	{
    		var x = timePassed * this.durationReciprocal;
    		this.currScale = this.startScale + (this.finalScale - this.startScale) * x;
    		if ((this.currScale == this.finalScale)) {
    			this.finished = true;
            }
    	}
    	else
    	{
    		this.currScale = this.finalScale;
    		this.finished = true;
    	}
	
		this.applyToView();
		
		return true;
	},
	applyToView : function ()
	{
		var position = this.startViewPosition;
		var scale = this.currScale;
		
		var left = g_floor(position.left + (position.left - this.screenX) * (scale - 1));
		var top = g_floor(position.top + (position.top - this.screenY) * (scale - 1));
		this.view.setBGScale(scale, left, top);			
	}	
});


////
//// MapView.js
//// 

var g_MapView = g_Class({

	initialize : function (div, controller, background)
	{
		this.controller = controller;
		this.tilelayers = {};
        this.tiles = {};
		
		// Background appearance
		var css = {
            "position" : "relative",
            "overflow" : "hidden",
            "background-color" : "#FFF3DC"
        };
		if(g_isPathDefined(background, 'css'))
		{
            css["background"] = background.css;
		}
		else if(g_isPathDefined(background, 'image'))
		{
            css["background"] = "url('" + background.image + "') repeat top left";
		}
		else
		{
			if(g_isPathDefined(background, 'color'))
			{
				css["background-color"] = background.color;
			}
            css["background-image"] = "none";
		}
		div.css(css);
		
		this.div = div;
		
		/* Mappy logo */
        if (g_isIE6)
        {
        	 div.append('<div name="logo" id="logoMappy-ie6" class="default-logo-IE6" ></div>');
        	// div.append('<img name="logo" class="default-logo" src="' +  _DATA.mapImageLink + 'logo.png" style="position:absolute;right:5px;top:5px;z-index:999;"></img>');
        }
        else
        {
           // div.append('<img name="logo" class="default-logo" src="' + _DATA.mapImageLink+ 'logo.png" style="position:absolute;right:5px;top:5px;z-index:999;"></img>');
        	 div.append('<div name="logo" id="logoMappy" class="default-logo" ></div>');
        }
		
		var geolayer = g_jQuery('<div name="geolayer" style="position:absolute;"></div>');
		this.geolayer = geolayer;
		
		this.bgTileLayer = g_jQuery('<div name="bgtilelayer" style="position:absolute;z-index:5;"></div>');
		
		this.geolayer.append(this.bgTileLayer);
        
		div.append(this.geolayer);
		
        this.addTileLayer('default', 6);
        
		controller.resize(div.width(), div.height());
		
		/* Add Events */
		
		g_draggable(this.geolayer, {
			drag: function (event)
			{
				controller.drag(event);
			},
			start: function (event)
			{
				controller.dragStart(event);
			},
			stop: function (event)
			{
				controller.dragStop(event);
			}
		});
		  
		geolayer.mouseout(function (event)
        {
			controller.mouseout(event);
        });

        geolayer.mouseover(function (event)
        {
            controller.mouseover(event);
        });

        geolayer.click(function (event)
        {			
			controller.click(event);
        });

        geolayer.dblclick(function (event)
        {
            controller.dblclick(event);
        });

        geolayer.mousedown(function (event)
        {
            controller.mousedown(event);
        });

        geolayer.mouseup(function (event)
        {
            controller.mouseup(event);
        });

        geolayer.mousemove(function (event)
        {
			controller.mousemove(event);
        });

        geolayer.mousewheel(function (event, delta)
        {
			controller.mousewheel(event, delta);
        });

        g_jQuery(window).resize(function ()
        {
            controller.resize();
        });

        /**
            Disable the contextmenu on right click
        */
        geolayer.each(function ()
        {
            this.oncontextmenu = function ()
            {
                return false;
            };
        });

        if (g_hasTouchSupport)
        {
            geolayer[0].addEventListener('touchmove', function (event)
            {
                g_preventDefault(event);
                controller.touchmove(event);
            });

            geolayer[0].addEventListener('touchend', function (event)
            {
				controller.touchend(event);
            });

            var firstTapTime = 0;
            var firstTapPoint = {};
            geolayer[0].addEventListener('touchstart', function (event)
            {
                controller.touchstart(event);
				
                g_preventDefault(event);

                if (event.touches.length === 1)
                {
                    var touch = event.touches[0];

                    if ((new Date().getTime() - firstTapTime) < 1000)
                    {
                        var distance = Math.pow(touch.pageX - firstTapPoint.pageX, 2) + Math.pow(touch.pageY - firstTapPoint.pageY, 2);
                        if (distance < 900)
                        {
                            firstTapTime = 0;
                            firstTapPoint.pageX = touch.pageX;
                            firstTapPoint.pageY = touch.pageY;
							
                            controller.dblclick(touch);
                            return;
                        }
                    }
                    firstTapTime = new Date().getTime();
                    firstTapPoint.pageX = touch.pageX;
                    firstTapPoint.pageY = touch.pageY;
                }
            });
        }

        if (g_hasGestureSupport)
        {
            geolayer[0].addEventListener("gesturestart", function (event)
            {
				controller.gesturestart(event);
            });

            geolayer[0].addEventListener("gestureend", function (event)
            {
				controller.gestureend(event);
            });
            
            geolayer[0].addEventListener("gesturechange", function (event)
            {
				controller.gesturechange(event);
            });
        }
	},
	slide : function (x, y, from, callback)
    {
        var curPos = this.position();
        this.geolayer.animate({
            left : curPos.left + x,
            top : curPos.top + y
        }, {
            duration : 500,
            complete : function ()
            {
				if (g_jQuery.isFunction(callback))
				{
					callback();
				}
            },
            queue : false
        });
    },
	setTiles : function (tiles, left, top, forceRefresh)
	{
		var dom;
		var tile;
		var force = forceRefresh !== undefined ? forceRefresh : false;
        
        for (var viewmode in tiles)
        {
			if (forceRefresh)
			{
				this.clean(viewmode);
			}
			
            for (var i = 0; i < tiles[viewmode].length; i += 1)
            {
                tile = tiles[viewmode][i];
				if(g_isNotDefined(this.tiles[viewmode][tile.key]))
				{
                    tile.create(left, top);
                    tile.append(this.tilelayers[viewmode]);
                    this.tiles[viewmode][tile.key] = tile;
                }
            }
        }
	},
	destroyExtraTiles : function (tiles)
	{
		var t;
        for (var viewmode in tiles)
        {
            for (var i = 0; i < tiles[viewmode].length; i += 1)
            {
                t = tiles[viewmode][i];
                if (this.tiles[viewmode].hasOwnProperty(t.key))
                {
                    this.tiles[viewmode][t.key].keep = true;
                }
            }
            
            for (var key in this.tiles[viewmode])
            {
                t = this.tiles[viewmode][key];
                if (t.keep !== true)
                {
                    t.remove();
                    delete this.tiles[viewmode][key];
                }
                else
                {
                    t.keep = false;
                }
            }
        }
	},
	reset : function ()
	{
		this.clean();
		this.setPosition(0, 0);
	},
	/**
		@param {String} viewmodeName If specified, remove only tiles of this viewmode
	 */
	clean : function (viewmodeName)
	{
        for (var vm in this.tiles)
        {
			if(g_isNotDefined(viewmodeName) || viewmodeName === vm)
			{
				var tiles = this.tiles[vm];
				for (var key in tiles)
				{
					if (tiles.hasOwnProperty(key))
					{
						tiles[key].remove();
					}
				}
				this.tiles[vm] = [];
			}
        }
	},
	setPosition : function (left, top)
	{
		this.geolayer.css({
			left : left,
			top : top
		});
	},
	setScale : function (scale, left, top)
	{
		this.geolayer.css({
			left : left,
			top : top
		});
        
        for (var vm in this.tiles)
        {
            for (var i in this.tiles[vm])
            {
                this.tiles[vm][i].zoomAt(scale);
            }
        }
	},
	setBGScale : function (scale, left, top)
	{
		var bgTileLayer = this.bgTileLayer;
		bgTileLayer.hide();
		bgTileLayer.css({
			left : left,
			top : top
		});
		
		for (var i = 0, bgTiles = this.bgTiles, l = bgTiles.length; i <  l; i += 1)
		{
			bgTiles[i].zoomAt(scale);
		}
		
		bgTileLayer.show();
	},
	createBG : function ()
	{
		this.cleanBG();		
		
		var bgTileLayer = this.bgTileLayer;
		
		var bgTiles = this.bgTiles;
		var t;
        
        for (var i in this.tiles['default'])
        {
            t = this.tiles['default'][i];
            bgTiles.push(t);
            t.append(bgTileLayer);
        }
        this.bgTiles = bgTiles;
        
        for (var viewmode in this.tiles)
        {
            this.tiles[viewmode] = [];
        }
		
		var position = this.position();
		this.setBGScale(1, position.left, position.top);
		this.setPosition(0, 0);
	},
	cleanBG : function ()
	{
		this.bgTileLayer.empty();
		this.bgTiles = [];
	},
	position : function ()
	{
		return this.geolayer.position();
	},
	leftTopFromEvent : function (event)
	{
		var mapOffSet = this.div.offset();
		return {
			left : event.pageX - mapOffSet.left,
			top : event.pageY - mapOffSet.top
		};
	},
	disableDraggable : function ()
	{
		this.geolayer.disableDraggable();
	},
	enableDraggable : function ()
	{
		this.geolayer.enableDraggable();
	},
    enableZoomSelection : function ()
    {
        var that = this;
        
        this._mouseDownSelectionHandler = function (event)
        {
            var mapOffSet = that.div.offset();
            var position = new g_Point(event.pageX - mapOffSet.left, event.pageY - mapOffSet.top);
            var coordinates = that.controller.converter.fromPixels(position.x, position.y);
            
            var _mouseMoveHandler = function()
            {
                that.geolayer.unbind('mousemove', _mouseMoveHandler);
                that.geolayer.unbind('mouseup', _mouseUpHandler);
                
                var eventCatcher = g_jQuery('<div style="position: absolute; z-index: 998; width:10000px; height:10000px;"></div>');
                if (g_jQuery.browser.msie)
                {
                    /*
                    Catch IE events
                    */
                    var div = g_jQuery('<div style="background-color:white; position:absolute; width:2000px; height:2000px;"></div>');
                    div.css('opacity', 0.01);
                    eventCatcher.append(div);
                }

                var selectZone = g_jQuery('<div class="tools-selected-zone" style="position:absolute;z-index: 991;"></div>');
                selectZone.css('opacity', 0.3);
                eventCatcher.append(selectZone);

                that.div.append(eventCatcher);

                eventCatcher[0].onselectstart = function()
                {
                    if (event)
                    {
                        event.returnValue=false;
                    }
                    return false;
                };
                
                var mouseDownCoordinates = coordinates;
                var mouseDownEvent = event;
                
                eventCatcher.mouseup(function (event)
                {
                    eventCatcher.remove();
                    if (mouseDownEvent.pageX !== event.pageX && mouseDownEvent.pageY !== event.pageY)
                    {
                        var mapOffSet = that.div.offset();
                        var position = new g_Point(event.pageX - mapOffSet.left, event.pageY - mapOffSet.top);
                        var coords = that.controller.converter.fromPixels(position.x, position.y);
                        var bbox = new g_GeoBounds(mouseDownCoordinates, mouseDownCoordinates);
                        
                        bbox.extend(coords);
                        bbox.refreshCenter();
                        
                        that.controller.onZoomSelection(bbox);
                    }
                });

                eventCatcher.mousemove(function (event)
                {
                    var mapPosition = that.div.offset();

                    var x = mouseDownEvent.pageX - mapPosition.left,
                    y = mouseDownEvent.pageY - mapPosition.top,
                    width = event.pageX - mouseDownEvent.pageX,
                    height = event.pageY - mouseDownEvent.pageY;

                    if (width < 0)
                    {
                        x = x + width;
                        width = width * -1;
                    }
                    if (height < 0)
                    {
                        y = y + height;
                        height = height * -1;
                    }

                    selectZone.width(width);
                    selectZone.height(height);
                    selectZone.css({
                        left: x,
                        top: y
                    });
                });
                
            };
            
            that.geolayer.mousemove(_mouseMoveHandler);
            
            // unbind listeners if 
            var _mouseUpHandler = function()
            {
                that.geolayer.unbind('mouseup', _mouseUpHandler);
                that.geolayer.unbind('mousemove', _mouseMoveHandler);
            };
            that.geolayer.mouseup(_mouseUpHandler);
            
        };
        
        this.geolayer.mousedown(this._mouseDownSelectionHandler);

    },
    disableZoomSelection : function ()
    {
        this.geolayer.unbind('mousedown', this._mouseDownSelectionHandler);
        this._mouseDownSelectionHandler = null;
    },
    zoomStart : function ()
    {
        // vider les layers 
        for (var viewmode in this.tiles)
        {
            if (viewmode !== 'default')
            {
                var tiles = this.tiles[viewmode];
                for (var key in tiles)
                {
                    if (tiles.hasOwnProperty(key))
                    {
                        tiles[key].remove();
                    }
                }
                this.tiles[viewmode] = [];
            }
        }
		
    },
	destroy : function ()
	{
		this.div.empty();
	},
	addTileLayer : function (name, zindex)
	{
        this.tilelayers[name] = g_jQuery('<div name="tilelayer" style="position:absolute;z-index:' + zindex + ';"></div>');
        this.geolayer.append(this.tilelayers[name]);
        this.tiles[name] = [];
	},
	removeTileLayer : function (name)
	{
        for (var viewmode in this.tiles)
        {
            if (viewmode === name)
            {
                var tiles = this.tiles[viewmode];
                for (var key in tiles)
                {
                    if (tiles.hasOwnProperty(key))
                    {
                        tiles[key].remove();
                    }
                }
                this.tiles[viewmode] = [];
            }
        }
        
        //this.geolayer.remove(this.tilelayers[name]);
        this.tilelayers[name].remove();
		
        delete this.tilelayers[name];
        delete this.tiles[name];
	}
});


////
//// MapController.js
//// 

var g_MapController = g_Class({
	initialize : function (map, container, background)
	{
		this.map = map;
		this.model = new g_MapModel();
		this.view = new g_MapView(container, this, background);
		
        var descrOptions = {
            tooltipsOnClick: map.getFeature('tooltipsOnClick')
        };
		this.mapdescr = new g_MapDescr(this, descrOptions);

		this.converter = new g_PixelConverter(this.model, this.view);
		
		this.layers = [];
		
		this._dragFlag = false;		
		
		this.dblclickZoom = true;
		this.mousewheelZoom = true;
		this.gestureZoom = true;
		this.zoomSelection = false;
		this.zoomAnimation = !g_isPhone;
		this.tooltipsOnClick = false;

        this.mouseWheelTimer = 200;
        this.mouseWheelLock = false;
		
		this.popuplayer = new g_PopUpLayer(900);
		this.addLayer(this.popuplayer);
		
		this.zoomAnimationHelper = new g_ZoomAnimation(this.view);		
		
	},
	setPadding : function(padding)
	{
		this.model.setPadding(padding);
		if(this.map.isReady)
		{
			this.newStaticPosition();
		}
	},
	setState : function (coordinates, zoomLevel)
	{
        this.view.cleanBG();
		this.model.setState(coordinates, zoomLevel);
		this.map.isReady = true;
		this.model.computeTopLeftCorner();
		this.newDisplayArea();
	},
	getCenter : function ()
	{
		var x = this.model.topLeftCornerX;
		var y = this.model.topLeftCornerY;
		return this.converter.fromPixels(x, y);
	},
	setZoomAnimation : function (zoomAnimation)
	{
		this.zoomAnimation = zoomAnimation;
	},
	getZoomAnimation : function ()
	{
		return this.zoomAnimation;
	},
	setZoom : function (zoomLevel, screenX, screenY, disableAnimation)
	{
		if (g_isNotDefined(screenX)) screenX = this.model.topLeftCornerX;
		if (g_isNotDefined(screenY)) screenY = this.model.topLeftCornerY;
		
		var position = this.view.position();

        // disableAnimation parameter is mainly used for gesture zoom
		if (this.zoomAnimation && !disableAnimation)
		{
			var oldZoom = this.model.getZoom();
			if (this.model.setZoom(zoomLevel, screenX, screenY, position))
			{
				this.map.trigger("zoomstart", zoomLevel);

				var that = this;
                
                that.view.zoomStart();
				
				var finished = this.zoomAnimationHelper.finished;
				
				this.zoomAnimationHelper.zoom(this.model.getZoom() - oldZoom, screenX, screenY);			
				
				function step ()
				{
					setTimeout(function ()
					{
						if (that.zoomAnimationHelper.stepAnimation())
						{
							step();
						}
						else
						{
							that.newDisplayArea();
							that.map.trigger("zoomend", zoomLevel);
						}
					}, 13);
				}
				
				if (finished)
				{
					step();	
				}
			}
		}
		else
		{
			if (this.model.setZoom(zoomLevel, screenX, screenY, position))
			{
				this.map.trigger("zoomstart", zoomLevel);
				this.newDisplayArea();
				this.map.trigger("zoomend", zoomLevel);			
			}
		}
	},
	setViewMode : function (viewMode)
    {
        if (this.map.isReady)
        {
            this.map.trigger("changestart", "viewmode");
            this.model.setViewMode(viewMode);
			var checkZoom = viewMode.zoomable(this.model.getZoom());
			
			if (checkZoom !== -1)
			{
				this.setZoom(checkZoom);
			}
			else
			{
				this.view.clean();
				this.refreshTiles();
				
				var position = this.view.position();
				var tiles = this.model.getTiles(position.left, position.top);
				
				this.mapdescr.newDisplayArea();
				this.mapdescr.refresh(tiles);
			}
            this.map.trigger("changeend", "viewmode");
			this.map.trigger("viewmodechanged", viewMode.name);
        }
        else
        {
            this.model.setViewMode(viewMode);
        }
    },
	newDisplayArea : function ()
	{
		this.map.trigger("changestart", "newDisplayArea");
		var tiles = this.model.getTiles(0, 0);
		this.view.reset();
		this.view.setTiles(tiles, this.model.centerX - this.model.topLeftCornerX, this.model.centerY + this.model.topLeftCornerY);
		
		this.refreshLayers();
		this.mapdescr.newDisplayArea();
		this.mapdescr.refresh(tiles);
		this.map.trigger("changeend", "newDisplayArea");		
	},
	refreshLayers : function ()
    {
        for (var i = 0; i < this.layers.length; i += 1)
        {
            this.layers[i].newDisplayArea();
        }
    },
	refreshTiles : function (viewmode, forceRefresh)
	{
		var position = this.view.position();
		var tiles = this.model.getTiles(position.left, position.top, viewmode);
		this.view.setTiles(tiles, this.model.centerX - this.model.topLeftCornerX, this.model.centerY + this.model.topLeftCornerY, forceRefresh);		
		this._dragFlag = false;
		return tiles;
	},
	newStaticPosition : function ()
	{
		var tiles = this.refreshTiles();
		this.view.destroyExtraTiles(tiles);
		this.mapdescr.refresh(tiles);	
	},
	addLayer : function (layer)
	{
		layer.added(this);
        this.layers.push(layer);
	},
	removeLayer : function (layer)
        {
            layer.removed();
            var index = g_jQuery.inArray(layer, this.layers);
            if (index !== -1)
            {
                this.layers.splice(index, 1);
            }
        },
	removeLayerByName : function ( /**String*/ layerName )
        {
          for( var i=0, j=this.layers.length; i<j; i++ )  
          {
              if( layerName === this.layers[i].name )
                  this.removeLayer( this.layers[i] );//we don't "break", since we can have multiple layers with the same name
          }
        },
	getCoordinatesFromEvent : function (event)
	{
		var coords;
		if (this.map.isReady)
		{
			var leftTop = this.view.leftTopFromEvent(event);
			coords = this.converter.fromPixels(leftTop.left, leftTop.top);
		}
		return coords;
	},
    getBgPois : function()
    {
        return this.mapdescr.getPois();
    },
	slideBy : function (x, y, from)
	{
		this.map.trigger("changestart", "slide", from);
		var that = this;
		this.view.slide(x, y, null, function ()
		{
			var tiles = that.refreshTiles();
			that.view.destroyExtraTiles(tiles);
			that.mapdescr.refresh(tiles);	
			that.map.trigger("changeend", "slide", from);
		});
	},
	slideTo : function (coordinates)
	{
		var from = this.converter.toGeolayerPixels(this.getCenter());
		var to = this.converter.toGeolayerPixels(coordinates);
		this.slideBy(from.x - to.x, from.y - to.y);
	},
	slideToBounds : function (bounds, from)
	{
		var slideX = 0;
		var slideY = 0;

        var layerPos = this.view.position();
        var padding = this.model.getPadding();
        var mapSize = this.model.getSize();

        if (bounds.minX + layerPos.left - padding.left < 0)
        {
            slideX = - (bounds.minX + layerPos.left - padding.left);
        }
        else if (bounds.maxX + layerPos.left + padding.right - mapSize.width > 0)
        {
            slideX = - (bounds.maxX + layerPos.left + padding.right - mapSize.width);
        }
		
        if (bounds.minY + layerPos.top - padding.top < 0)
        {
            slideY = - (bounds.minY + layerPos.top - padding.top);
        }
        else if (bounds.maxY + layerPos.top + padding.bottom - mapSize.height > 0)
        {
            slideY = - (bounds.maxY + layerPos.top + padding.bottom - mapSize.height);
        }

        if (slideX !== 0 || slideY !== 0)
        {
            this.slideBy(slideX, slideY, from);
        }
	},
	enableDraggable : function ()
	{
		this.view.enableDraggable();
		this.map.trigger("controlchanged", "draggable");
	},
	disableDraggable : function ()
	{
		this.view.disableDraggable();
		this.map.trigger("controlchanged", "draggable");
	},
	enableZoomSelection : function ()
	{
        if (!this.zoomSelection)
        {
            this.disableDraggable();
            this.view.enableZoomSelection();
            this.zoomSelection = true;
        }
	},
	disableZoomSelection : function ()
	{
        if (this.zoomSelection)
        {
            this.view.disableZoomSelection();
            this.zoomSelection = false;
        }
	},
	enableTooltipsOnClick : function ()
	{
        this.tooltipsOnClick = true;
	},
	disableTooltipsOnClick : function ()
	{
        this.tooltipsOnClick = false;
	},
	/** Events */
	drag : function (event)
	{
		if (!this._dragFlag && !g_isPhone)
		{
			this._dragFlag = true;
			setTimeout(g_makeCaller(this.refreshTiles, this), 200);
		}
		this.map.trigger("drag", event);
	},
	dragStart : function (event)
	{
		this.view.cleanBG();
		this.map.dragging = true;
		this.map.trigger("changestart", "drag");
		this.map.trigger("dragstart", event);
	},
	dragStop : function (event)
	{
		this.newStaticPosition();
		this.map.dragging = false;
		
		this.map.trigger("changeend", "drag");
		this.map.trigger("dragstop", event);
	},
	click : function (event)
	{
		this.map.trigger("click", event, this.getCoordinatesFromEvent(event));
	},
	dblclick : function (event)
	{
		if (this.dblclickZoom === true)
		{
			var leftTop = this.view.leftTopFromEvent(event);
			this.setZoom(this.model.getZoom() + 1, leftTop.left, leftTop.top);
		}
		this.map.trigger("dblclick", event, this.getCoordinatesFromEvent(event));
	},
	mousedown : function (event)
	{
		this.map.trigger("mousedown", event, this.getCoordinatesFromEvent(event));
	},
	mouseup : function (event)
	{
		if (event.button === 2)
		{
			this.map.trigger("rightclick", event, this.getCoordinatesFromEvent(event));
		}
		else
		{
			this.map.trigger("mouseup", event, this.getCoordinatesFromEvent(event));
		}
	},
	mousemove : function (event)
	{
		this.map.trigger("mousemove", event);
	},
	mousewheel : function (event, delta)
	{
        // On Mac OSX, trackpad are very sensitive : a simple scroll may increase zoom level by 6. The only way
        // is to lock mousewheel during a certain time. (FAST-886)
        if (g_isMacOS && this.mouseWheelLock === true) {
            return;
        }
        if (g_isMacOS) {
            this.mouseWheelLock = true;

            setTimeout(g_makeCaller(function() {
                this.mouseWheelLock = false;
            }, this), this.mouseWheelTimer);
        }

		if (this.mousewheelZoom === true)
		{
			var newDelta = (delta > 0) ? 1 : -1;
			var leftTop = this.view.leftTopFromEvent(event);
			this.setZoom(this.model.getZoom() + newDelta, leftTop.left, leftTop.top);
			g_preventDefault(event);
		}
		this.map.trigger("mousewheel", event, delta);
	},
	mouseout : function (event)
	{
		this.map.trigger("mouseout", event);
	},
	mouseover : function (event)
	{
		this.map.trigger("mouseover", event);
	},
	resize : function (w, h)
	{
		var _w = w, _h = h;
		this.map.trigger("beforeresize", w, h);
		
		if(w === undefined && h === undefined)
		{
			_w = this.map.div.width();
			_h = this.map.div.height();
		}
		
		this.model.setSize(_w, _h);
		if (this.map.isReady)
		{
			this.setState(this.getCenter(), this.model.zoom);
			
			var tiles = this.model.getTiles(0, 0);
			
			this.view.reset();
			this.view.setTiles(tiles, this.model.centerX - this.model.topLeftCornerX, this.model.centerY + this.model.topLeftCornerY);
			this.refreshLayers();
			
			this.mapdescr.newDisplayArea();
			this.mapdescr.refresh(tiles);
		}
		
		this.map.trigger("resize", w, h);
	},
	touchmove : function (event)
	{
		this.map.trigger("touchmove", event);
	},
	touchstart : function (event)
	{
		if (g_hasGestureSupport && this.gestureZoom && event.touches.length > 1)
		{
			this._dblTouch = true;
			this._gestureStartEvent = {
				pageX : (event.touches[0].pageX + event.touches[1].pageX) / 2,
				pageY : (event.touches[0].pageY + event.touches[1].pageY) / 2
			};
			
			this._gestureStartPosition = this.view.position();
		}
		
		this.map.trigger("touchstart", event);
	},
	touchend : function (event)
	{
		if (g_hasGestureSupport && this._dblTouch)
		{
			this.setZoom(this.model.getZoom() - 1);
			this._dblTouch = false;
		}
		this.map.trigger("touchend", event);
	},
	gesturestart : function (event)
	{
		if (this.gestureZoom)
		{
            for (var i in this.layers) {
                if (!this.layers[i].isHidden) {
                    this.layers[i].hide();
                }
            }
        }
		this.map.trigger("gesturestart", event);		
	},
	gestureend : function (event, startPosition)
	{
		if (this.gestureZoom)
		{
			this.view.setScale(1, this._gestureStartPosition.left, this._gestureStartPosition.top);
			
			var leftTop;
			if (event.scale > 2)
			{
				leftTop = this.view.leftTopFromEvent(this._gestureStartEvent);
				this.setZoom(this.model.getZoom() + 1, leftTop.left, leftTop.top, true);
                // always disable zoom animation for gesture zoom
			}
			else if (event.scale < 0.5)
			{
				leftTop = this.view.leftTopFromEvent(this._gestureStartEvent);
				this.setZoom(this.model.getZoom() - 1, leftTop.left, leftTop.top, true);
			}
            
            for (var i in this.layers) {
                this.layers[i].show();
            }
		}
		
		this.map.trigger("gestureend", event);
	},
	gesturechange : function (event)
	{
		if (this.gestureZoom)
		{
			this._dblTouch = false;
			
			var scale = event.scale;
			var initPos = this._gestureStartPosition;
			var eventPos = this.view.leftTopFromEvent(this._gestureStartEvent);
					
			var left = initPos.left + (initPos.left - eventPos.left) * (scale - 1);
			var top = initPos.top + (initPos.top - eventPos.top) * (scale - 1);
			
			this.view.setScale(scale, left, top);
		}
		
		this.map.trigger("gesturechange", event);		
	},
	onZoomSelection : function (event)
	{
        this.setState(event.center, this.model.getBoundsZoomLevel(event));
	},
	/**
        add an other viewMode to the map
        @param {Mappy.api.map.ViewMode} View mode.
    */
    addTileLayer : function (viewMode, zindex)
    {
        // add the layer only if it doesn't already exist
        if (!this.model.viewModes[viewMode.name])
        {
			viewMode._refreshTask(this);
            this.model.addTileLayer(viewMode);
            this.view.addTileLayer(viewMode._realName, zindex);
            if (this.map.isReady)
            {
                var tiles = this.refreshTiles(viewMode.name);
                this.mapdescr.refresh(tiles);
            }
        }
    },
	/**
        remove a specific viewMode from the map
    */
    removeTileLayer : function (viewModeName)
    {
		// If the given viewmode is added to the map, then remove it. Else, do nothing.
		var viewModes = this.model.getTileLayers();
		if(g_isDefined(viewModes[viewModeName]))
		{
			viewModes[viewModeName]._stopRefreshTask(true);
			this.view.removeTileLayer(viewModes[viewModeName]._realName);
			this.model.removeTileLayer(viewModeName);
			
			// TODO : refresh only tiles for the removed viewmode
			var tiles = this.model.getTiles(0, 0);
			this.mapdescr.newDisplayArea();
			this.mapdescr.refresh(tiles);
		}
    },
    getTileLayers : function ()
    {
        return this.model.getTileLayers();
    }
	/**
        Hide the traffic layer if it is currently displayed
        @param {Mappy.api.map.ViewMode} View mode.
    */
    , hideTraffic : function ()
    {
		if(g_isDefined(this.trafficViewmode))
		{
			this.removeTileLayer(this.trafficViewmode.name);
			delete this.trafficViewmode;
		}
    }
	/**
        Show the traffic layer.
        @param {Boolean} events Wether to show traffic events or not
    */
    , showTraffic : function (events)
    {
		// Prepare viewmode
		var name = "traffic_road_conditions";
		
		if(events === true)
		{
			name += ";traffic_events";
		}
		var vm = new g_ViewMode(name);
		
		// Hide old viewmode, if needed
		if(g_isDefined(this.trafficViewmode))
		{
			this.hideTraffic();	
		}
		
		// Show new viewmode
		this.addTileLayer(vm);
		this.trafficViewmode = vm;
    }
});


////
//// MapModel.js
//// 

var g_MapModel = g_Class({
	initialize : function ()
	{
		this.viewModes = {};
        this.viewModes['default'] = new g_ViewMode('standard');

        this.refreshResolutions();

		this.padding = {
            left : 0,
            top : 0,
            right : 0,
            bottom : 0
        };
	},
	refreshResolutions : function ()
	{
		var tileSize = this.viewModes['default'].slabSize;

		this.resolutions = [];

		for (var z = 0, max = g_ViewMode.mMaxZoomLevel; z <= max; z += 1)
		{
			this.resolutions[z] = tileSize * Math.pow(3, z);
		}
	},
	setSize : function (w, h)
	{
		this.width = w;
		this.height = h;
		this.halfWidth = w / 2;
		this.halfHeight = h / 2;

		this.computeTopLeftCorner();
	},
	getSize : function ()
	{
		return new g_Size(this.width, this.height);
	},
	setPadding : function (padding)
	{
		if (g_isDefined(padding.left))
		{
			this.padding.left = padding.left;
		}
		if (g_isDefined(padding.top))
		{
			this.padding.top = padding.top;
		}
		if (g_isDefined(padding.right))
		{
			this.padding.right = padding.right;
		}
		if (g_isDefined(padding.bottom))
		{
			this.padding.bottom = padding.bottom;
		}
	},
	getPadding : function ()
	{
		return this.padding;
	},
	setCenter : function (center)
	{
		this.center = center;
		this.computeCenter();
	},
	computeCenter : function()
	{
		if(g_isDefined(this.center))
		{
			var resolution = this.getResolution();
			var center = this.center;
			this.centerX = g_floor(center._x * resolution);
			this.centerY = g_floor(center._y * resolution);
		}
	},
	computeTopLeftCorner : function ()
	{
		this.topLeftCornerX = g_floor(this.halfWidth + (this.padding.left - this.padding.right) / 2);
		this.topLeftCornerY = g_floor(this.halfHeight + (this.padding.top - this.padding.bottom) / 2);
	},
	getResolution : function ()
	{
		return this.resolutions[this.zoom];
	},
	getMeterPerPixel : function ()
	{
		var RADIUS_EARTH_METERS = 6378140;
		return (Math.PI * RADIUS_EARTH_METERS) / this.getResolution();
	},
	setZoom : function (_zoom, screenX, screenY, position)
	{
		var zoom = parseInt(_zoom);
		if (zoom == this.zoom || zoom < this.viewModes['default'].minZoomLevel || zoom > this.viewModes['default'].maxZoomLevel)
		{
			return false;
		}

		if (position.left != 0 || position.top!= 0)
		{
			this.center = this.offsetCenter(-position.left, -position.top);
			this.computeCenter();
		}

		if (screenX != this.topLeftCornerX || screenY != this.topLeftCornerY)
		{
			var factor = 1 - Math.pow(3, this.zoom - zoom);
			
			this.center = this.offsetCenter((screenX - this.topLeftCornerX) * factor, (screenY - this.topLeftCornerY) * factor);
			
			this.computeCenter();
		}
		
		this.zoom = zoom;
		
		this.computeCenter();
		return true;
	},
	getZoom : function ()
	{
		return parseInt(this.zoom);
	},
	offsetCenter : function (x, y)
	{
		var resolution = this.getResolution();
		var newX = this.centerX + x;
		var newY = this.centerY - y;
		return g_Coordinates.fromNormalized(newX/resolution, newY/resolution);
	},
	setState : function (center, zoom)
	{
		this.center = center;
		this.zoom = zoom;
		this.computeCenter();
	},
	setViewMode : function (viewMode)
	{
		this.viewModes['default'] = viewMode;
        this.refreshResolutions();
		this.setBackgroundType(viewMode.name);
	},
	getViewMode : function ()
	{
		return this.viewModes['default'];
	},
	getPointXY : function (coords, pts)
	{
		if (!pts)
        {
        	pts = new g_Point(0, 0);
        }

		var resolution = this.getResolution();
		pts.x = g_floor(coords._x * resolution - this.centerX + this.topLeftCornerX);
		pts.y = g_floor(this.centerY + this.topLeftCornerY - coords._y * resolution);

		return pts;
	},
	getGeoPoint : function (x, y)
	{
		var ptsX = this.centerX - this.topLeftCornerX + x;
		var ptsY = this.centerY +  this.topLeftCornerY - y;
		var resolution = this.getResolution();
		return g_Coordinates.fromNormalized(ptsX/resolution, ptsY/resolution);
	},
	getBoundsZoomLevel : function (bounds)
	{
		var width = this.width - this.padding.left - this.padding.right;
		var height = this.height - this.padding.top - this.padding.bottom;

		var normalizedDeltaX = bounds.getDeltaX();
		var normalizedDeltaY = bounds.getDeltaY();

		var zoom = g_ViewMode.mMaxZoomLevel;

		var resolutions = this.resolutions;
		var currWidth = normalizedDeltaX * resolutions[zoom];
		var currHeight = normalizedDeltaY * resolutions[zoom];

		while ((currWidth > width || currHeight > height) && zoom > 0)
		{
			zoom --;
			currWidth = normalizedDeltaX * resolutions[zoom];
			currHeight = normalizedDeltaY * resolutions[zoom];
		}

		return zoom;
	},
	getTiles : function (deltaX, deltaY, viewmode)
	{
        var tiles = {};

		var zoom = this.zoom;
		
		if(g_isDefined(zoom))
		{
			var tileSize = this.viewModes['default'].slabSize;
			
			var topLeftCornerX = this.centerX - this.topLeftCornerX - deltaX;
			var topLeftCornerY = this.centerY + this.topLeftCornerY + deltaY;
	
			var minSx = g_floor(topLeftCornerX / tileSize);
			var maxSx = g_floor((topLeftCornerX + this.width) / tileSize);
	
			var maxSy = g_floor(topLeftCornerY / tileSize);
			var minSy = g_floor((topLeftCornerY - this.height) / tileSize);
	
	
			var modeSxMax = g_ViewMode.tileMaxIds[zoom][0];
			var modeSyMax = g_ViewMode.tileMaxIds[zoom][1];
	
			if (minSx < 0) minSx = 0;
			if (minSy < 0) minSy = 0;
			if (maxSx > modeSxMax) maxSx = modeSxMax;
			if (maxSy > modeSyMax) maxSy = modeSyMax;
	
			for (var vm in this.viewModes)
			{
				if (!viewmode || viewmode === vm)
				{
					var vmtiles = [];
					for (var sx = minSx; sx <= maxSx; sx += 1)
					{
						for (var sy = minSy; sy <= maxSy; sy += 1)
						{
							vmtiles.push(new g_Tile(sx, sy, zoom, this.viewModes[vm]));
						}
					}
	
					tiles[vm] = vmtiles;
				}
			}
		}
		return tiles;
	},
	addTileLayer : function (viewMode)
	{
        this.viewModes[viewMode.name] = viewMode;
	},
	getTileLayers : function ()
	{
        return this.viewModes;
	},
	removeTileLayer : function (viewMode)
	{
        delete this.viewModes[viewMode];
	},
	clearTileLayers : function ()
	{
        this.viewModes = {};
	},
	setBackgroundType : function(bgType)
	{
		this._bgType = bgType;
		for (var vm in this.viewModes)
		{
			this.viewModes[vm]._updateRealName(this);
		}
	}
});


////
//// MapDescr.js
//// 

var g_MapDescr = g_Class(/** @lends MapDescr.prototype */{
    _tiles : null,
    _pois : null,
    _providers : null,
    _zoomLevel : 0,
    /**
     @constructs
     @private
     */
    initialize : function (controller, options)
    {
        this._controller = controller;

        this._tiles = {};
        this._pois = {};
        this._providers = {};

        var that = this;


        controller.map.addListener('click', function (event)
        {
            handleBgPoiEvent(event, {checkClickeable: true});
        });

        controller.map.addListener('mousemove', function (event)
        {
            handleBgPoiEvent(event);
        });


        var handleBgPoiEvent = function(event, actions)
        {
            actions = actions || {};
            var found = false;
            var map = controller.map;
            if (!map.dragging)
            {
                var offSet = controller.view.position();
                var mapOffSet = controller.view.div.offset();
                var posX = event.pageX - mapOffSet.left - offSet.left;
                var posY = event.pageY - mapOffSet.top - offSet.top;

                for (var viewmode in that._pois)
                {
                    var pois = that._pois[viewmode];

                    for (var i = 0; i < pois.length; i += 1)
                    {
                        if (!found && pois[i].isOver(posX, posY))
                        {
                            if (actions.checkClickeable && pois[i].isClickeable())
                            {
                                map.trigger('bgpoiclick', pois[i].item);
                            }

                            if ((event.type === "mousemove" && !controller.tooltipsOnClick)
                                || (event.type === "click" && controller.tooltipsOnClick))
                            {
                                pois[i].openToolTip(event);
                            }

                            found = true;
                        }
                        else
                        {
                            if (event.type === "click" || (event.type === "mousemove" && !controller.tooltipsOnClick))
                            {
                                pois[i].closeToolTip();
                            }
                        }
                    }
                }
            }
        };


        controller.map.addListener('touchstart', function(e)
        {
            setTimeout(function() {
                if (!controller.map.dragging)
                {
                    var event = {
                        type: 'click'
                    };

                    if (e.touches && e.touches.length) {
                        event.pageX = e.touches[0].pageX;
                        event.pageY = e.touches[0].pageY;
                    } else {
                        event.pageX = e.pageX;
                        event.pageY =  e.pageY;
                    }

                    handleBgPoiEvent(event, {checkClickeable: true});
                }
            }, 100);
        });

        var closeTooltips = function() {
            for (var viewmode in that._pois)
            {
                var pois = that._pois[viewmode];
                for (var i = 0; i < pois.length; i += 1)
                {
                    pois[i].closeToolTip();
                }
            }
        };
        controller.map.addListener('mouseout', function () {
            !controller.tooltipsOnClick && closeTooltips();
        });
        controller.map.addListener('zoomstart', closeTooltips);
    },
    newDisplayArea : function ()
    {
        for (var viewmode in this._tiles)
        {
            if (this._pois[viewmode])
            {
                for (var i = 0; i < this._pois[viewmode].length; i += 1)
                {
                    this._pois[viewmode][i].destroy();
                }
            }
            this._pois[viewmode] = [];
            this._providers[viewmode] = [];
        }
        this._controller.map.copyrights.setText(this._providers);
    },
    refresh : function (tiles)
    {
        this._pois = {};
        this._providers = {};

        var modes = this._controller.model.getTileLayers();
        var new_zoomLevel = this._controller.map.getZoomLevel();

        for (var viewmode in tiles)
        {
            this._pois[viewmode] = [];
            this._providers[viewmode] = [];

            if (!this._tiles[viewmode])
            {
                this._tiles[viewmode] = {};
            }

            var tilesList = [];
            var mode = modes[viewmode];

            this._zoomLevel = new_zoomLevel;
            var t;
            for (var i = 0; i < tiles[viewmode].length; i += 1)
            {
                t = tiles[viewmode][i];
                if ((g_isNotDefined(this._tiles[viewmode][t.key]) && mode.checkSy(t) && mode.checkSx(t)))
                {
                    tilesList.push(t.sx + "," + t.sy);
                }
            }

            this._controller.map.copyrights.remove();

            if (tilesList.length > 0)
            {
                var req = new g_DescrRequest();
                req.configure(tilesList.join(';'), this._controller.model.getZoom(), mode);

                var that = this;

                (function(viewmode, zoom)
                {
                    req.submit(function (o)
                    {
                        if (zoom === that._controller.model.getZoom())
                        {
                            that._providers[viewmode] = [];
                            that._updateTilesList(that._tiles[viewmode], o, modes[viewmode].name);

                            that._refreshTilesInfos(tiles[viewmode], viewmode);
                            that._controller.map.copyrights.setText(that._providers);
                        }

                        that._controller.map.trigger("refreshdescr");

                    });
                }(viewmode, this._controller.model.getZoom()));
            }
            else {
                this._refreshTilesInfos(tiles[viewmode], viewmode);
                this._controller.map.copyrights.setText(this._providers);
            }
        }
    },
    getPois : function()
    {
        return this._pois;
    },
    _refreshTilesInfos: function(tiles, viewmode) {
        var sidToKey = {};
        this._providers[viewmode] = {};
        this._pois[viewmode] = [];
        for (var i = 0; i < tiles.length; i++)
        {
            if (g_isDefined(this._tiles[viewmode][tiles[i].key]))
            {
                var tile = this._tiles[viewmode][tiles[i].key];
                sidToKey[tile.sid] = tile.key;
                for (var j = 0; j < tile.items.length; j++)
                {
                    var item = tile.items[j];
                    this._pois[viewmode].push(new g_BgPoi(item, item.properties.type, this._controller, sidToKey[tile.sid]));
                }

                var copyrights = tile.copyrights;

                for (var k = 0; k < copyrights.length; k++)
                {
                    if (g_isNotDefined(this._providers[viewmode][copyrights[k].name]))
                    {
                        this._providers[viewmode][copyrights[k].name] = {};
                    }
                    this._providers[viewmode][copyrights[k].name][tile.key] = true;
                }
            }
        }

    },
    _updateTilesList : function (tilesList, tiles, viewmode)
    {
        for (var i = 0; i < tiles.length; i += 1)
        {
            var splitSid = tiles[i].sid.split('/');
            tiles[i].key = g_Tile.prototype.getKey(splitSid[1], splitSid[2], splitSid[0], viewmode);
            tilesList[tiles[i].key] = tiles[i];
        }
    }
});


////
//// DescrService.js
//// 

var g_DescrService = MappyApi.map.DescrService = g_Class(/** @lends Mappy.api.map.DescrService.prototype */{
    request : 0,
    /**
        @constructs
    */
    initialize : function ()
    {
        this.request = new g_DescrRequest();
    },
    /**
        Submits an asynchronous decsr request. 
        @param {Object} options Object containing bounds, zoom and viewMode.
        @param {Function} success Success callback.
        @param {Function} error Error callback.
    */
    send : function (options, success, error)
    {
        if (!options.bounds || !g_isDefined(options.zoom) || !options.viewMode) throw "Mappy.api.route.DescrService : errors in options";
        
        this.request.configure(
                        options.bounds, 
                        options.zoom, 
                        options.viewMode, 
                        "geoBounds");
        g_submit(this.request, success, error);
    }
});


////
//// PixelConverter.js
//// 

var g_PixelConverter = g_Class({
    initialize : function (model, view)
    {
		this.model = model;
		this.view = view;
    },
	toGeolayerPixels : function (coords, out)
	{
		return this.model.getPointXY(coords, out);
	},
	fromPixels : function (x, y)
	{
		var pos = this.view.position();
		return this.model.getGeoPoint(x - pos.left, y - pos.top);
	},
	fromGeolayerPixels : function (x, y)
	{
		return this.model.getGeoPoint(x, y);
	}
});


////
//// Drawer.js
//// 

function g_getDrawer(container, delta, type)
{
    if (!!document.createElement("canvas").getContext)
    {
        return new g_DrawerCanvas(container, delta, type);
    }
    else if (g_jQuery.browser.msie === true)
    {
        var drawer = new g_DrawerVML(container, type);
        if (document.namespaces)
        {
            var namespaceExists = false;
            for (var c = 0; c < document.namespaces.length; c += 1)
            {
                var d = document.namespaces(c);
                if (d.name === "v")
                {
                    if (d.urn === "urn:schemas-microsoft-com:vml")
                    {
                        namespaceExists = true;
                    }
                    else
                    {
                        break;
                    }
                }
            }
            if (!namespaceExists)
            {
                document.namespaces.add("v", "urn:schemas-microsoft-com:vml");
            }
        }
        return drawer;
    }
    else
    {
        return new g_DrawerSVG(container, delta, type);
    }
}

var g_Drawer = g_Class(/** @lends Drawer.prototype */{
    /**
        @constructs
        @private
    */
    initialize : function (div, delta, type)
    {
        this.type = type;
        this.div = g_jQuery('<div style="position:absolute;"></div>');
        
        // correct a bug's IE due to mismatched node document origin 
        if (div && div[0].ownerDocument !== this.div[0].ownerDocument){
            this.div = g_jQuery(div[0].ownerDocument.createElement('div'));
            this.div.css({position:"absolute"});
        }
        
        div.append(this.div);
        
        this.div.hide();

        this._delta = delta || 0;

        this.style = new g_ShapeStyle();

        this.lineCap = 'round';
        this.lineJoin = 'round';
        this.shadowColor = 'black';
        this.shadowBlur = 0;
    },
    setStyle : function (style)
    {
        if (g_isDefined(style))
        {
            this.style = style;
        }
    },
    getStyle : function ()
    {
        return this.style;
    },
    refreshStyle : function ()
    {

    },
    setBoundingBox : function (bbox)
    {
        this._bbox = bbox;
        this.div.css('left', this._bbox.minX - this._delta);
        this.div.css('top', this._bbox.minY - this._delta);
    },
    clean : function ()
    {
        this.div.hide();
        delete this._bbox;
        this.div.empty();
    },
    removed : function ()
    {
        this.div.remove();
    },
    buildPath : function (points)
    {
        var i;
        var l = points.length;
        var moves = [[]];
        if (l > 1)
        {
            var bbox = this._bbox;
            var bboxMinX = bbox.minX;
            var bboxMinY = bbox.minY;
            var bboxMaxX = bbox.maxX;
            var bboxMaxY = bbox.maxY;

            var clipLine = g_clipLine;
            for (i = 1; i < l; i += 1)
            {
                clipLine(
                    bboxMinX,
                    bboxMinY,
                    bboxMaxX,
                    bboxMaxY,
                    points[i - 1].x,
                    points[i - 1].y,
                    points[i].x,
                    points[i].y,
                    moves
                );
            }
        }

        for (i = moves.length - 1; i >= 0 ; i -= 1)
        {
            if (moves[i].length < 4)
            {
                moves.splice(i, 1);
            }
        }
        return moves;
    },
    buildPolygon : function (points)
    {
        return g_clipPolygon(points, this._bbox);
    }
});

//return -1 : Totally outside
//return  0 : Totally inside
//return  1 : Enter
//return  2 : Leave
//return  3 : Go trough
function g_clipLine(xbox1, ybox1, xbox2, ybox2, x1, y1, x2, y2, moves)
{
    var CLIP_OUT = -1;

    var entre = false;
    var sort = false;
    var exch = false;                   // indique si x1,y1 et x2,y2 sont echanges
    var d;

    var dummy;
    var alpha = ((y2 - y1) / (x2 - x1));  // pente du segment (constant);

    if (x2 < x1)
    {
        dummy = x2; x2 = x1; x1 = dummy;
        dummy = y2; y2 = y1; y1 = dummy;
        exch = true;
    }

    if (x1 < xbox1)
    {
        if (x2 <= xbox1)
        {
            return CLIP_OUT;
        }
        else
        {
            y1 += (alpha * (xbox1 - x1));
            x1 = xbox1;
            entre = true;
        }
    }

    if (x2 > xbox2)
    {
        if (x1 >= xbox2)
        {
            return CLIP_OUT;
        }
        else
        {
            y2 -= (alpha * (x2 - xbox2));
            x2 = xbox2;
            sort = true;
        }
    }

    if (y2 < y1)
    {
        dummy = x2; x2 = x1; x1 = dummy;
        dummy = y2; y2 = y1; y1 = dummy;
        d = sort; sort = entre; entre = d;
        exch = !exch;
    }

    if (y1 < ybox1)
    {
        if (y2 <= ybox1)
        {
            return CLIP_OUT;
        }
        else
        {
            x1 += ((ybox1 - y1) / alpha);
            y1 = ybox1;
            entre = 1;
        }
    }

    if (y2 > ybox2)
    {
        if (y1 >= ybox2)
        {
            return CLIP_OUT;
        }
        else
        {
            x2 -= ((y2 - ybox2) / alpha);
            y2 = ybox2;
            sort = 1;
        }
    }

    if (exch)
    {
        dummy = x2; x2 = x1; x1 = dummy;
        dummy = y2; y2 = y1; y1 = dummy;
        d = sort; sort = entre; entre = d;
    }

    var state = entre + sort * 2;
    var round = Math.round;
    var pts = moves[moves.length - 1];
    if (state === 0)
    {
        if (pts.length === 0)
        {
            pts.push(round(x1));
            pts.push(round(y1));
        }
        pts.push(round(x2));
        pts.push(round(y2));
    }
    else if (state > 0)
    {
        pts.push(round(x1));
        pts.push(round(y1));
        pts.push(round(x2));
        pts.push(round(y2));
        if (state > 1)
        {
            moves.push([]);
        }
    }
}

function g_clipPolygon (points, bbox)
{
    var i;
    var l = points.length;
    var clipedPoints = [];

    var bboxMinX = bbox.minX;
    var bboxMinY = bbox.minY;
    var bboxMaxX = bbox.maxX;
    var bboxMaxY = bbox.maxY;

    var cur = points[points.length - 1];
    var prev;
    var in_points = 0;
    var tmpX, tmpY;
    var dummy;
    var round = Math.round;
    for (var i = 0, nb_points = points.length; i < nb_points; i += 1)
    {
        prev = cur;
        cur = points[i];

        if (clipedPoints.length === 0 &&
            prev.x >= bboxMinX &&
            prev.x <= bboxMaxX &&
            prev.y >= bboxMinY &&
            prev.y <= bboxMaxY)
        {
            in_points += 1;
            clipedPoints.push(round(prev.x));
            clipedPoints.push(round(prev.y));
        }

        var t0 = g_droiteVecteurIntersect(bboxMinX, 0, bboxMinX, 1, prev.x, prev.y, cur.x, cur.y);
        var t1 = g_droiteVecteurIntersect(bboxMaxX, 0, bboxMaxX, 1, prev.x, prev.y, cur.x, cur.y);
        var t2 = g_droiteVecteurIntersect(0, bboxMinY, 1, bboxMinY, prev.x, prev.y, cur.x, cur.y);
        var t3 = g_droiteVecteurIntersect(0, bboxMaxY, 1, bboxMaxY, prev.x, prev.y, cur.x, cur.y);

        if (t0 > t1)
        {
            dummy = t0; t0 = t1; t1 = dummy;
        }
        if (t2 > t3)
        {
            dummy = t2; t2 = t3; t3 = dummy;
        }
        if (t0 > t2)
        {
            dummy = t0; t0 = t2; t2 = dummy;
        }
        if (t1 > t3)
        {
            dummy = t1; t1 = t3; t3 = dummy;
        }
        if (t1 > t2)
        {
            dummy = t1; t1 = t2; t2 = dummy;
        }

        if (t0 >= 0 && t0 <= 1)
        {
           tmpX = prev.x + (cur.x - prev.x) * t0;
           tmpY = prev.y + (cur.y - prev.y) * t0;

           tmpX = Math.max(tmpX, bboxMinX);
           tmpX = Math.min(tmpX, bboxMaxX);
           tmpY = Math.max(tmpY, bboxMinY);
           tmpY = Math.min(tmpY, bboxMaxY);

           clipedPoints.push(round(tmpX));
           clipedPoints.push(round(tmpY));
        }
        if (t1 >= 0 && t1 <= 1)
        {
           tmpX = prev.x + (cur.x - prev.x) * t1;
           tmpY = prev.y + (cur.y - prev.y) * t1;

           tmpX = Math.max(tmpX, bboxMinX);
           tmpX = Math.min(tmpX, bboxMaxX);
           tmpY = Math.max(tmpY, bboxMinY);
           tmpY = Math.min(tmpY, bboxMaxY);

           clipedPoints.push(round(tmpX));
           clipedPoints.push(round(tmpY));
        }
        if(t2 >= 0 && t2 <= 1)
        {
            tmpX = prev.x + (cur.x - prev.x) * t2;
            tmpY = prev.y + (cur.y - prev.y) * t2;

            tmpX = Math.max(tmpX, bboxMinX);
            tmpX = Math.min(tmpX, bboxMaxX);
            tmpY = Math.max(tmpY, bboxMinY);
            tmpY = Math.min(tmpY, bboxMaxY);

            clipedPoints.push(round(tmpX));
            clipedPoints.push(round(tmpY));
        }
        if(t3 >= 0 && t3 <= 1)
        {
            tmpX = prev.x + (cur.x - prev.x) * t3;
            tmpY = prev.y + (cur.y - prev.y) * t3;

            tmpX = Math.max(tmpX, bboxMinX);
            tmpX = Math.min(tmpX, bboxMaxX);
            tmpY = Math.max(tmpY, bboxMinY);
            tmpY = Math.min(tmpY, bboxMaxY);

            clipedPoints.push(round(tmpX));
            clipedPoints.push(round(tmpY));
        }

        if (clipedPoints.length !== 0 &&
            cur.x >= bboxMinX &&
            cur.x <= bboxMaxX &&
            cur.y >= bboxMinY &&
            cur.y <= bboxMaxY)
        {
            in_points += 1;
            clipedPoints.push(round(cur.x));
            clipedPoints.push(round(cur.y));
        }
    }

    if (clipedPoints.length > 4 &&
        (in_points !== 0 || g_computePolygonArea(clipedPoints) > 1))
    {
        return clipedPoints;
    }
    else
    {
         return [];
    }
}

function g_computePolygonArea(points)
{
	var area = 0;

	for(var i = 0, l = points.length - 2; i < l; i += 2)
	{
        area += (points[i] + points[i + 2]) * (points[i + 1] - points[i + 3]);
	}
    area += (points[i] + points[0]) * (points[i + 1] - points[1]);

	return 0.5 * Math.abs(area);
}

function g_droitePointIntersect(x1, y1, x2, y2, x, y)
{
	if (x2 === x1)
    {
		return x === x1;
    }
	else
    {
		return (y - x * (y2 - y1) / (x2 - x1) + (x1 * y2 - y1 * x2) / (x2 - x1)) === 0;
    }
}

function g_droiteVecteurIntersect(x11, y11, x12, y12, x21, y21, x22, y22)
{
	var q2 = (y12 - y11) * (x21 - x11) - (x12 - x11) * (y21 - y11);
	var d = (y22 - y21) * (x12 - x11) - (y12 - y11) * (x22 - x21);
    
    // 0 si parallele
	if (d !== 0)
	{
		return (q2 / d);
	}
	else if (g_droitePointIntersect(x11, y11, x12, y12, x22, y22))
    {
		return 0;
    }
	else
    {
		return -1;
    }
}


////
//// DrawerCanvas.js
//// 

var g_DrawerCanvas = g_Class(g_Drawer, /** @lends DrawerCanvas.prototype */{
    /**
        @constructs
        @augments Drawer
        @private
    */
    initialize : function (div, delta, type)
    {
        g_Drawer.prototype.initialize.call(this, div, delta, type);

        var canvas = g_jQuery('<canvas name="mappy-route-layer"></canvas>');
        this.div.append(canvas);
        var canvasNode = canvas[0];
        this._ctx = canvasNode.getContext("2d");
    },
    getStrokeStyle : function ()
    {
        var color = this.style.getStrokeStyle();
        return 'rgba(' +
                parseInt(color.substr(6, 2), 16) + ', ' +
                parseInt(color.substr(4, 2), 16) + ', ' +
                parseInt(color.substr(2, 2), 16) + ', ' +
                parseInt(color.substr(0, 2), 16) / 256 + ')';
    },
    getFillStyle : function ()
    {
        var color = this.style.getFillStyle();
        return 'rgba(' +
                parseInt(color.substr(6, 2), 16) + ', ' +
                parseInt(color.substr(4, 2), 16) + ', ' +
                parseInt(color.substr(2, 2), 16) + ', ' +
                parseInt(color.substr(0, 2), 16) / 256 + ')';
    },
    refreshStyle : function ()
    {
        this._ctx.clearRect(0, 0, this._ctx.canvas.width, this._ctx.canvas.height);
        switch (this.type)
        {
        case 'line' :
            this.line(this.points);
            break;
        case 'polygon' :
            this.polygon(this.points);
            break;
        case 'circle' :
            this.circle(this.center, this.radius);
        }
    },
    setBoundingBox : function (bbox)
    {
        g_Drawer.prototype.setBoundingBox.call(this, bbox);
        this._ctx.canvas.width = this._bbox.maxX - this._bbox.minX + this._delta * 2;
        this._ctx.canvas.height = this._bbox.maxY - this._bbox.minY + this._delta * 2;
    },
    _path : function (points)
    {
        var moves = this.buildPath(points);
        var pts;
        var ctx = this._ctx;
        var dX = this._delta - this._bbox.minX;
        var dY = this._delta - this._bbox.minY;
        for (var i = 0; i < moves.length; i += 1)
        {
            pts = moves[i];
            ctx.beginPath();
            ctx.moveTo(pts[0] + dX, pts[1] + dY);
            for (var j = 2; j < pts.length; j += 2)
            {
                ctx.lineTo(pts[j] + dX, pts[j + 1] + dY);
            }
            this._ctx.stroke();
        }
    },
    line : function (points)
    {
        this.points = points;
        if (g_isDefined(this._bbox))
        {
            this._ctx.lineWidth = this.style.getLineWidth();
            this._ctx.strokeStyle = this.getStrokeStyle();
            this._ctx.lineCap = this.lineCap;
            this._ctx.lineJoin = this.lineJoin;
            this._ctx.shadowColor = this.style.getShadowColor();
            this._ctx.shadowBlur = this.style.getShadowBlur();

            this._path(points);

            this.div.show();
        }
    },
    _polygon : function (points)
    {
        var pts = this.buildPolygon(points);
        var ctx = this._ctx;
        var dX = this._delta - this._bbox.minX;
        var dY = this._delta - this._bbox.minY;

        ctx.beginPath();
        ctx.moveTo(pts[0] + dX, pts[1] + dY);
        for (var j = 2; j < pts.length; j += 2)
        {
            ctx.lineTo(pts[j] + dX, pts[j + 1] + dY);
        }
        ctx.fill();
        ctx.closePath();
        if (this.style.getLineWidth() > 0)
        {
            ctx.stroke();
        }
    },
    polygon : function (points)
    {
        this.points = points;
        if (g_isDefined(this._bbox))
        {
            this._ctx.strokeStyle = this.getStrokeStyle();
            this._ctx.fillStyle = this.getFillStyle();
            this._ctx.lineWidth = this.style.getLineWidth();

            this._polygon(points);

            this.div.show();
        }
    },
    circle : function (center, radius)
    {
        this.center = center;
        this.radius = radius;
        if (g_isDefined(this._bbox))
        {
            this._ctx.lineWidth = this.style.getLineWidth();
            this._ctx.strokeStyle = this.getStrokeStyle();
            this._ctx.fillStyle = this.getFillStyle();

            this._ctx.beginPath();
            this._ctx.arc(center.x - this._bbox.minX + this._delta, center.y - this._bbox.minY + this._delta, radius, 0, Math.PI * 2, true);
            this._ctx.fill();
            this._ctx.stroke();

            this.div.show();
        }
    },
    clean : function ()
    {
        this.points = [];
        this.div.hide();
        delete this._bbox;
        this._ctx.clearRect(0, 0, this._ctx.canvas.width, this._ctx.canvas.height);
    }
});


////
//// DrawerSVG.js
//// 

var g_DrawerSVG = g_Class(g_Drawer, /** @lends DrawerSVG.prototype */{
    /**
        @constructs
        @augments Drawer
        @private
    */
    initialize : function (div, delta, type)
    {
        g_Drawer.prototype.initialize.call(this, div, delta, type);
    },
    getStrokeColor : function ()
    {
        var color = this.style.getStrokeStyle();
        return '#' + color.substr(6, 2) + color.substr(4, 2) + color.substr(2, 2);
    },
    getStrokeOpacity : function ()
    {
        var color = this.style.getStrokeStyle();
        return parseInt(color.substr(0, 2), 16) / 256;
    },
    getFillColor : function ()
    {
        var color = this.style.getFillStyle();
        return '#' + color.substr(6, 2) + color.substr(4, 2) + color.substr(2, 2);
    },
    getFillOpacity : function ()
    {
        var color = this.style.getFillStyle();
        return parseInt(color.substr(0, 2), 16) / 256;
    },
    refreshStyle : function ()
    {
        if (g_isDefined(this.div[0].firstChild))
        {
            switch (this.type)
            {
            case 'line' :
                this.div[0].firstChild.firstChild.setAttribute('stroke', this.getStrokeColor());
                this.div[0].firstChild.firstChild.setAttribute('stroke-opacity', this.getStrokeOpacity());
                this.div[0].firstChild.firstChild.setAttribute('stroke-width', this.style.getLineWidth());
                break;
            case 'polygon' :
            case 'circle' :
                this.div[0].firstChild.firstChild.setAttribute('stroke', this.getStrokeColor());
                this.div[0].firstChild.firstChild.setAttribute('stroke-opacity', this.getStrokeOpacity());
                this.div[0].firstChild.firstChild.setAttribute('stroke-width', this.style.getLineWidth());
                this.div[0].firstChild.firstChild.setAttribute('fill', this.getFillColor());
                this.div[0].firstChild.firstChild.setAttribute('fill-opacity', this.getFillOpacity());
                break;
            }
        }
    },
    _init : function ()
    {
        var width = this._bbox.maxX - this._bbox.minX + this._delta * 2;
        var height = this._bbox.maxY - this._bbox.minY + this._delta * 2;
        return '<svg xmlns="http://www.w3.org/2000/svg" version="1.1" width="' + width +
               '" height="' + height +
               '" viewBox="' + (this._bbox.minX - this._delta) + ' ' + (this._bbox.minY - this._delta) + ' ' + width + ' ' + height +
               '">';
    },
    _end : function (svg)
    {
        svg += '</svg>';
        var parser = new DOMParser();
        var xml = parser.parseFromString(svg, 'text/xml');
        this.svg = xml.childNodes[0];

        this.div.append(this.svg);
    },
    _path : function (points)
    {
        var svg = '<path stroke-linejoin="' + this.lineJoin + '" stroke-linecap="' + this.lineCap + '" fill="none" d="';
        
        var moves = this.buildPath(points);
        var pts;
        for (var i = 0; i < moves.length; i += 1)
        {
            pts = moves[i];
            pts.splice(0, 0, ' M');
            pts.splice(3, 0, 'L');
            svg += pts.join(' ');
        }        
        svg += '"/>';
        return svg;
    },
    _polygon : function (points)
    {
        var clipedPoints = this.buildPolygon(points);

        var svg = '<polygon  points="';
            svg += clipedPoints.join(' ');
            svg += '"/>';  
        
        return svg;
    },
    line : function (points)
    {
        if (g_isDefined(this._bbox))
        {
            var svg = this._init();

            svg += this._path(points);

            this._end(svg);
            this.refreshStyle();
            this.div.show();
        }
    },
    polygon : function (points)
    {
        if (g_isDefined(this._bbox))
        {
            var svg = this._init();

            svg += this._polygon(points);

            this._end(svg);
            this.refreshStyle();
            this.div.show();
        }
    },
    _circle : function (center, radius)
    {
        var svg =   '<circle cx="' + center.x +
                    '" cy="' + center.y +
                    '" r="' + radius + '" />';
        return svg;
    },
    circle : function (center, radius)
    {
        if (g_isDefined(this._bbox))
        {
            var svg = this._init();

            svg += this._circle(center, radius);

            this._end(svg);
            this.refreshStyle();
            this.div.show();
        }
    }
});


////
//// DrawerVML.js
//// 

var g_DrawerVML = g_Class(g_Drawer, /** @lends DrawerVML.prototype */{
    /**
        @constructs
        @augments Drawer
        @private
    */
    initialize : function (div, type)
    {
        g_Drawer.prototype.initialize.call(this, div, 0, type);
    },
    getStrokeColor : function ()
    {
        var color = this.style.getStrokeStyle();
        return '#' + color.substr(6, 2) + color.substr(4, 2) + color.substr(2, 2);
    },
    getStrokeOpacity : function ()
    {
        var color = this.style.getStrokeStyle();
        return parseInt(color.substr(0, 2), 16) / 256;
    },
    getFillColor : function ()
    {
        var color = this.style.getFillStyle();
        return '#' + color.substr(6, 2) + color.substr(4, 2) + color.substr(2, 2);
    },
    getFillOpacity : function ()
    {
        var color = this.style.getFillStyle();
        return parseInt(color.substr(0, 2), 16) / 256;
    },
    refreshStyle : function ()
    {
        var div = this.div[0];

        div.onselectstart = function()
        {
            if (event)
            {
                event.returnValue=false;
            }
            return false;
        };
		
        var shapeType = this.type === "circle" ? "oval" : "shape";
        var shape = div.getElementsByTagName(shapeType);
        
        if (shape.length > 0)
        {
            shape[0].strokecolor = this.getStrokeColor();
            shape[0].fillcolor = this.getFillColor();
            shape[0].strokeweight = this.style.getLineWidth() + 'px';

            if (this.style.getLineWidth() === 0)
            {
                shape[0].stroked = "false";
            }
        }

        var stroke = div.getElementsByTagName("stroke");
        if (stroke.length > 0)
        {
            stroke[0].opacity = this.getStrokeOpacity();
        }

        var fill = div.getElementsByTagName("fill");
        if (fill.length > 0)
        {
            fill[0].opacity = this.getFillOpacity();
        }
    },
    _path : function (points)
    {
        var vml =   '<v:shape coordorigin="0 0" coordsize="1 1" filled="false" style="position: absolute;width:1px; height:1px;behavior: url(#default#VML);" path="';

        var moves = this.buildPath(points);

        var pts;
        var dX = this._delta - this._bbox.minX;
        var dY = this._delta - this._bbox.minY;
        for (var i = 0; i < moves.length; i += 1)
        {
            pts = moves[i];
            for (var j = 0; j < pts.length; j += 2)
            {
                pts[j] += dX;
                pts[j + 1] += dY;
            }
            pts[0] = 'M' + pts[0];
            pts[2] = ' L' + pts[2];
            vml += pts.join(' ') + ' E ';
        }

        vml +=  '"><v:stroke joinstyle="' + this.lineJoin +
                '" endcap = "' + this.lineCap +
                '"  style="behavior: url(#default#VML);"/></v:shape>';
        return vml;
    },
    _poly : function (points)
    {
        var vml =   '<v:shape coordorigin="0 0" coordsize="1 1" filled="true" style="position: absolute;width:1px; height:1px;behavior: url(#default#VML);" path="';

        var pts = this.buildPolygon(points);
        var dX = this._delta - this._bbox.minX;
        var dY = this._delta - this._bbox.minY;
        for (var j = 0; j < pts.length; j += 2)
        {
            pts[j] += dX;
            pts[j + 1] += dY;
        }
        pts[0] = 'M' + pts[0];
        pts[2] = ' L' + pts[2];
        vml += pts.join(' ') + ' X E ';
        vml +=  '">';
        if (this.style.getLineWidth() > 0)
        {
            vml += '<v:stroke joinstyle = "' + this.lineJoin +
                '" endcap = "' + this.lineCap +
                '"  style="behavior: url(#default#VML);"/>';
        }

        vml += '<v:fill style="behavior: url(#default#VML);"></v:fill>' +
            '</v:shape>';

        return vml;
    },
    line : function (points)
    {
        if (g_isDefined(this._bbox))
        {
            var vml = this._path(points);
            this.div[0].insertAdjacentHTML("beforeEnd", vml);
            this.refreshStyle();
            this.div.show();
        }
    },
    polygon : function (points)
    {
        if (g_isDefined(this._bbox))
        {
            var vml = this._poly(points);
            this.div[0].insertAdjacentHTML("beforeEnd", vml);
            this.refreshStyle();
            this.div.show();
        }
    },
    circle : function (center, radius)
    {
        if (g_isDefined(this._bbox))
        {
            var vml = "<v:oval style='position:relative;width:" + radius*2 + ";height:" + radius*2 + ";behavior: url(#default#VML);'>";
            vml += '<v:fill style="behavior: url(#default#VML);"></v:fill>';
            vml += "</v:oval>";
            
            this.div[0].insertAdjacentHTML("beforeEnd", vml);
            this.refreshStyle();
            this.div.show();
        }
    }
});


////
//// Layer.js
//// 

var g_Layer = g_Class(/** @lends Layer.prototype */{
    isOnMap : false,
    isHidden : false,
    /**
        Create a layer with a name and a z-index
        @constructs
        @param {String} name The name of the layer
        @param {Number} zIndex The z-index value of the layer
    */
    initialize : function (name, zIndex, options)
    {
        this.name = name;
        this.zIndex = zIndex;
        this.div = g_jQuery('<div name="' + name + '" class="layer"></div>');
        this.div.css('z-index', zIndex);
        this.div.css('position', 'absolute');
        
        options || (options = {});
        if (options.hidden)
        {
            this.hide();
        }
            
    },
    /**
    @private
    */
    added : function (controller)
    {
        controller.view.geolayer.append(this.div);
		
		this._controller = controller;
        this.isOnMap = true;
    },
	/**
    @private
    */
    newDisplayArea : function () {},
    /**
    @private
    */
    removed : function ()
    {
        this.div.remove();
        this.isOnMap = false;
    },
    hide : function ()
    {
        this.div.hide();
        this.isHidden = true;
    },
    show : function ()
    {
        this.div.show();
        this.isHidden = false;
    },
    clean : function ()
    {
        this.div.empty();
    }
});


////
//// MarkerLayer.js
//// 

var g_MarkerLayer = MappyApi.map.layer.MarkerLayer = g_Class(g_Layer, /** @lends Mappy.api.map.layer.MarkerLayer.prototype */{
    /**
    Array of {@link Mappy.api.map.Marker}
    @type Mappy.api.map.Marker[]
    */
    markers : null,
    /**
    Array of {@link Mappy.api.map.Cluster}
    @type Mappy.api.map.Cluster[]
    */
    clusters : null,
    /**
    Number of markers on the layer.
    @type Number
    */
    markersCount : 0,
    /**
        @constructs
        @augments Layer
        @param {Number} zindex
    */
    initialize : function (zindex, options)
    {
        zindex = zindex || 50;
        g_Layer.prototype.initialize.call(this, 'markerLayer', zindex, options);
        this.markers = [];
        this.clusters = [];
    },
    /**
        @private
    */
    added : function (controller)
    {
        
        g_Layer.prototype.added.call(this, controller);
        
		this.div.mousedown(function (event)
        {
            event.stopPropagation();
        });

        if (g_hasTouchSupport)
        {
            this.div[0].addEventListener('touchstart', function (event)
            {
                event.stopPropagation();
            });
        }
		this._map = controller.map;
		controller.map.addListener("zoomstart", g_makeCaller(this._zoomStartHandler, this));
        controller.map.addListener("zoomend", g_makeCaller(this._zoomEndHandler, this));
		
        for (var i = 0; i < this.markersCount; i += 1)
        {
            if (this.markers[i].isOnMap === false)
            {
                this.markers[i].added(this.div, controller);
            }
        }
    },
    /**
        @private
    */
    newDisplayArea : function ()
    {
        var i;

        for (i = 0; i < this.clusters.length; i += 1)
        {
            this.clusters[i].removed();
        }

        this.clusters = [];
        
        for (i = 0; i < this.markersCount; i += 1)
        {
            this.markers[i].destroyTail();
            this.markers[i].setPosition();
        }
    },
    /**
    Add a marker on the layer.
    @param {Mappy.api.map.Marker} marker
    */
    addMarker : function (marker)
    {
        if (g_jQuery.inArray(marker, this.markers) === -1)
        {
            if (this.isOnMap)
            {
                marker.added(this.div, this._controller);
            }
            this.markers.push(marker);
            this.markersCount += 1;
        }
    },
    /**
    Add a markers on the layer.
    @param {Mappy.api.map.Marker[]} markers {array} of {@link Mappy.api.map.Marker}
    */
    addMarkers : function (markers)
    {
        for (var i = 0; i < markers.length; i += 1)
        {
            this.addMarker(markers[i]);
        }
    },
    /**
        @private
    */
    _zoomStartHandler : function ()
    {
        this.div.hide();
    },
    /**
        @private
    */
    _zoomEndHandler : function ()
    {
        if (this.isHidden === false)
        {
            this.div.show();
        }
    },
    /**
    Remove a marker from the layer.
    @param {Mappy.api.map.Marker} marker
    */
    removeMarker : function (marker)
    {
        var i = g_jQuery.inArray(marker, this.markers);
        if (i !== -1)
        {
            this.markers.splice(i, 1);
            this.markersCount -= 1;
            marker.removed();
            return true;
        }
        else
        {
            return false;
        }
    },
    /**
    @returns {Mappy.api.map.Marker[]} Returns an array of {@link Mappy.api.map.Marker}
    */
    getMarkers : function ()
    {
        return this.markers;
    },
    closeAllPopup : function ()
    {
        var markers = this.markers;
        for (var i = 0, l = this.markersCount; i < l; i += 1)
        {
            markers[i].closePopUp();
        }
    },
    /**
    Remove all markers and clean the layer.
    */
    clean : function ()
    {
        while (this.markersCount > 0)
        {
            this.removeMarker(this.markers[0]);
        }
        g_Layer.prototype.clean.call(this);
    },
    /**
    Returns the bounds of the markers.
    @returns {Mappy.api.geo.GeoBounds} 
    */
    getBounds : function ()
    {
        if (this.markersCount > 0)
        {
            var markers = this.markers;
            var boundingBox = new g_GeoBounds(markers[0].coordinates, markers[0].coordinates);
            for (var i = 0; i < this.markersCount; i += 1)
            {
                boundingBox.extend(markers[i].coordinates);
            }
            boundingBox.refreshCenter();
            return boundingBox;
        }
        return null;
    },
    /**
    Regroup visible markers if there are collisions.
    @param {Mappy.api.ui.Icon} icon The icon for the cluster.
    @param {Number} minInCluster Minimum for marker in cluster.
    */
    conglomerate : function (icon, minInCluster)
    {
        icon = icon || new g_Icon({
            cssClass : "default-cluster",
            image : "../images/img/poi/anis_cluster.png",
            size : new g_Size(34, 34),
            iconAnchor : new g_Point(17, 17),
            popUpAnchor : new g_Point(17, 0)
        });
        
       
        this.clusters = g_conglomerate(this.markers, icon, this.div, this._map, minInCluster);
    },
    /**
    Explode all markers to prevent collision.
    */
    explode : function ()
    {
        g_explode(this.markers);
    },
    /**
    Explode all visible markers in a grid.
    @since version 2.02
    */
    grid : function ()
    {
        g_grid(this.markers, this._map);
    },
    /**
    Reset marker's position. Call this function if you want to reset marker's position after calling "conglomerate" or "explode".
    */
    reset : function ()
    {
        this.newDisplayArea();
    }
});


////
//// PopUpLayer.js
//// 

var g_PopUpLayer = g_Class(g_Layer, /** @lends PopUpLayer.prototype */{
    _popUps : null,
    /**
        @constructs
        @augments Layer
        @private
    */
    initialize : function (zindex)
    {
        g_Layer.prototype.initialize.call(this, 'popUpLayer', zindex);
        this._popUps = [];
    },
    added : function (controller)
    {
        g_Layer.prototype.added.call(this, controller);

        this.div.mousedown(function (event)
        {
            event.stopPropagation();
        });

        this.div.dblclick(function (event)
        {
            event.stopPropagation();
        });

        this.div.mousewheel(function (event)
        {
            event.stopPropagation();
        });

        if (g_hasTouchSupport)
        {
            this.div[0].addEventListener('touchstart', function (event)
            {
                event.stopPropagation();
            });
        }

        controller.map.addListener("zoomstart", g_makeCaller(this._zoomStartHandler, this));
        controller.map.addListener("zoomend", g_makeCaller(this._zoomEndHandler, this));
    },
    _zoomStartHandler : function ()
    {
        this.hide();
    },
    _zoomEndHandler : function ()
    {
        this.show();
    },
    addPopUp : function (popUp)
    {
        popUp.added(this._controller);
        this._popUps.push(popUp);
    },
    removePopUp :  function (popUp)
    {
        var i = g_jQuery.inArray(popUp, this._popUps);
        if (i !== -1)
        {
            popUp.removed();
            this._popUps.slice(i, 1);
        }
    }
});


////
//// ShapeLayer.js
//// 

var g_ShapeLayer = MappyApi.map.layer.ShapeLayer = g_Class(g_Layer, /** @lends Mappy.api.map.layer.ShapeLayer.prototype */{
    _shapes : null,
    _movedFromLastRefresh : null,
    /**
        @constructs
        @augments Layer
        @param {Number} zindex
    */
    initialize : function (zindex, options)
    {
        zindex = zindex || 50;
        g_Layer.prototype.initialize.call(this, 'shapeLayer', zindex, options);
        this._shapes = [];
        this._movedFromLastRefresh = new g_Point(0, 0);
    },
    /**
        @private
    */
    added : function (controller)
    {
        g_Layer.prototype.added.call(this, controller);

		// Adding listeners
		var map = controller.map;
		map.addListener("zoomstart", g_makeCaller(this._zoomStartHandler, this));
        map.addListener("zoomend", g_makeCaller(this._zoomEndHandler, this));
        map.addListener("changeend", g_makeCaller(this._redrawShapes, this));
        map.addListener("dragstart", g_makeCaller(this._dragStartHandler, this));
        map.addListener("drag", g_makeCaller(this._dragHandler, this));
        map.addListener("mousemove", g_makeCaller(this._mouseMoveHandler, this));
        map.addListener("mouseout", g_makeCaller(this._mouseOutHandler, this));
        map.addListener("mousedown", g_makeCaller(this._mouseDownHandler, this));
        map.addListener("mouseup", g_makeCaller(this._mouseUpHandler, this));
		
		// Add existing shapes
		this._addShapes();

    },
    /**
        @private
    */
    newDisplayArea : function ()
    {
        for (var i = 0; i < this._shapes.length; i += 1)
        {
            this._shapes[i].calcPoints();
        }
        this._redrawShapes();
    },
    /**
        @private
    */
    _zoomStartHandler : function ()
    {
        this.div.hide();
    },
    /**
        @private
    */
    _zoomEndHandler : function ()
    {
        if (this.isHidden === false)
        {
            this.div.show();
        }
    },
    /**
        @private
    */
    _mouseMoveHandler : function (event)
	{
		var shapes = this._shapes;
		if (this._controller.map.dragging === false)
		{
			for (var i = 0; i < shapes.length; i += 1)
			{
				var shape = shapes[i];
				if (shape.hasListeners)
				{
					if (shape.isOver)
					{
						if (shape.isInShape(event) === false)
						{
							shape.isOver = false;
							shape.trigger("mouseout", event);
						}
					}
					else
					{
						if (shape.isInShape(event))
						{
							shape.isOver = true;
							shape.trigger("mouseover", event);
						}
					}
				}
			}
		}
	},
    /**
        @private
    */
    _mouseOutHandler : function (event)
	{
		for (var i = 0; i < this._shapes.length; i += 1)
		{
			if (this._shapes[i].isOver)
			{
				this._shapes[i].isOver = false;
				this._shapes[i].trigger("mouseout", event);
			}
		}
	},
    /**
        @private
    */
    _mouseDownHandler : function (event)
	{
		for (var i = 0; i < this._shapes.length; i += 1)
		{
			if (this._shapes[i].isInShape(event))
			{
				this._shapes[i].trigger("mousedown", event);
			}
		}
		g_preventDefault(event);
	},
    /**
        @private
    */
    _mouseUpHandler : function (event)
	{
		for (var i = 0; i <  this._shapes.length; i += 1)
		{
			if ( this._shapes[i].isInShape(event))
			{
				 this._shapes[i].trigger("mouseup", event);
			}
		}
	},
    /**
        @private
    */
    _dragStartHandler : function (event)
	{
		this._startEvent = event;
	},
    /**
        @private
    */
    _dragHandler : function (event)
	{
		var startEvent = this._startEvent;
		if (startEvent !== null)
		{
			this._movedFromLastRefresh.x += startEvent.pageX - event.pageX;
			this._movedFromLastRefresh.y += startEvent.pageY - event.pageY;
			if (Math.abs(this._movedFromLastRefresh.x) > 500 ||
				Math.abs(this._movedFromLastRefresh.y) > 500)
			{
				this._redrawShapes();
			}
			this._startEvent = event;
		}
    },
    /**
        @private
    */
    _calcViewBox : function ()
    {
        var mapSize = this._controller.model.getSize();
        var converter = this._controller.converter;

		var ne = converter.fromPixels(mapSize.width + 500, - 500);
        var sw = converter.fromPixels(- 500, mapSize.height + 500);
		
        if (ne._x > 1 ||
            ne._y < 0 ||
            sw._x > 1 ||
            sw._y < 0 ||
            ne._x < sw._x)
        {
            ne = new g_Coordinates(180, 90);
            sw = new g_Coordinates(-180, -90);
        }
        this.viewBox = new g_GeoBounds(ne, sw);
    },
    /**
        @private
    */
    _redrawShapes : function (from)
    {
        if (from !== "drag")
        {
            this._calcViewBox();

            for (var i = 0; i < this._shapes.length; i += 1)
            {
                this._shapes[i].draw(this.viewBox);
            }
            this._movedFromLastRefresh = new g_Point(0, 0);
        }
    },
    /**
        @private
    */
    _addShapes : function ()
    {
        for (var i = 0; i < this._shapes.length; i += 1)
        {
            if (this._shapes[i].isOnMap === false)
            {
                this._shapes[i].added(this.div, this._controller);
            }
        }
        if (this._controller.map.isReady)
        {
            this.newDisplayArea();
        }
    },
    /**
		@param {Shape} shape Add a shape on the layer.
    */
    addShape : function (shape)
    {
        if (g_jQuery.inArray(shape, this._shapes) === -1)
        {
            if (this.isOnMap)
            {
                shape.added(this.div, this._controller);
                if (this._controller.map.isReady)
                {
                    shape.calcPoints();
                    shape.draw(this.viewBox);
                }
            }
            this._shapes.push(shape);
        }
    },
    /**
		@param {Shape} shape Remove a shape from the layer.
    */
    removeShape : function (shape)
    {
        var i = g_jQuery.inArray(shape, this._shapes);
        if (i !== -1)
        {
            this._shapes[i].removed();
            this._shapes.splice(i, 1);
            return true;
        }
        else
        {
            return false;
        }
    },
    /**
		Remove all shapes from the layer.
    */
    clean : function ()
    {
        while (this._shapes.length > 0)
        {
            this.removeShape(this._shapes[0]);
        }
        g_Layer.prototype.clean.call(this);
    },
    /**
		@return {Mappy.api.geo.GeoBounds} Returns the general bounds of the layer.
    */
    getBounds : function ()
    {
        if (this._shapes.length > 0)
        {
            var geoBounds;
            for (var i = 0; i < this._shapes.length; i += 1)
            {
                var shapeBounds = this._shapes[i].getBounds();
                if (g_isDefined(shapeBounds))
                {
                    if (g_isNotDefined(geoBounds))
                    {
                        geoBounds = new g_GeoBounds(shapeBounds.sw, shapeBounds.sw);
                    }
                    geoBounds.extend(shapeBounds.sw);
                    geoBounds.extend(shapeBounds.ne);
                }
            }
            geoBounds.refreshCenter();
            return geoBounds;
        }
        return null;
    },
    /**
		@returns {Shape[]} Returns an array of {@link Shape}
    */
    getShapes : function ()
    {
        return this._shapes;
    }
});


////
//// RoadbookLayer.js
//// 

var g_RoadbookLayer = MappyApi.map.layer.RoadbookLayer = g_Class(g_ShapeLayer, /** @lends Mappy.api.map.layer.RoadbookLayer.prototype */{
    /*
		Polyline style to show
		@type String
		@private
	*/
	_style : null,
    /*
		Polyline style to show
		@type GPolylineLevels
		@private
	*/
    _levels : null,
	/*
		Current displayed roadbook
		@type Roadbook
		@private
	*/
    _roadbook : null,
	/*
		Current route id from roadbook displayed
		@type Number
		@private
	*/
    _idRoute : null,
	/*
		If TRUE, then show the alternatives routes at the same time as the selected route. 
		@type Boolean
		@private
	*/
    _showAltRoute : null,
	/*
		ID of alternate route to display. If not null, then this route is shown, and the selected road is hidden.
		@type Number
		@private
	*/
    _previewAltRoute : null,
    /**
        @constructs
        @augments Mappy.api.map.layer.ShapeLayer
        @param {Number} zindex z-index to use for this div
        @param {String} colorStyle Styles to use in itinerary
        @param {Boolean} showAltRoute If TRUE, then show the alternatives routes at the same time as the selected route. 
    */
    initialize : function (zindex, colorStyle, showAltRoute)
    {
        g_ShapeLayer.prototype.initialize.call(this, zindex);
        this._style = colorStyle || "none";
		this._idRoute = -1;
		this._showAltRoute = showAltRoute || false;
		this._previewAltRoute = false;
    },
    /**
        @private
    */
    added : function (controller)
    {
        g_ShapeLayer.prototype.added.call(this, controller);
		
        this._rblZoomEndListener = this._controller.map.addListener("zoomend", g_makeCaller(this._updateShapes, this));
    },
    /**
		Returns whether or not this layer has a selected route (ie. it has a roadbnook and the id of a route).
		@returns TRUE, if there is a roabook selected.
		@private
    */
    _hasRoute : function ()
    {
		return (g_isDefined(this._roadbook) && this._roadbook !== null && this._idRoute >= 0);
	},
    /**
		Get the route displayed (not alternatives ones).
		@return {Route} The selected route.
    */
    getRoute : function ()
    {
		var route = false;
		if(this._hasRoute())
		{
			route = this._roadbook.routes[this._idRoute];
		}
		return route;
	},
    /**
		Updates or constructs shapes to display
        @private
    */
    _updateShapes : function ()
    {
		var zoom = this._controller.model.getZoom();
		
		if(this._hasRoute()
		   || g_isDefined(this._levels))
		{
            g_ShapeLayer.prototype.clean.call(this);
		}
		
		// Get shapes, with the right zoom, stylename and state
		if(g_isDefined(this._levels))
		{
			var shapes = this._levels.getShapes(zoom, this._style, false, this.overShapeStyle);
			for(var i in shapes)
			{
				this.addShape(shapes[i].clone());
			}
		}
		
		// Display alt routes
		if(this._hasRoute())
		{
			// First, show alt routes.
			if((this._showAltRoute === true || this._previewAltRoute !== false) && i !== this._idRoute)
			{
				for(var i = 0 ; i < this._roadbook.routes.length ; i++)
				{
					// Skip current route if alternative routes should be hidden
					if(i === this._idRoute
					   // If we want to preview a given alt route, then skip other routes
					   || (this._previewAltRoute !== false && this._previewAltRoute !== i))
					{
						continue;
					}
					
					// Get shapes, with the right zoom, stylename and state
					var levels = this._roadbook.routes[i].polyline;
					var shapes = levels.getShapes(zoom, this._style, i !== this._idRoute, this.overShapeStyle);
					for(var j in shapes)
					{
						this.addShape(shapes[j].clone());
					}
				}
			}
			
			// Then, show current route shapes, with the right zoom, stylename
			if(this._hasRoute())
			{
				var levels = this.getRoute().polyline;
				var shapes = levels.getShapes(zoom, this._style, false, this.overShapeStyle);
				for(var j in shapes)
				{
					this.addShape(shapes[j].clone());
				}
			}
		}
    },
    /**
		@private
    */
    clean : function ()
    {
        this._roadbook = null;
        // clean shapeLayer
        g_ShapeLayer.prototype.clean.call(this);
        
        this._controller.map.removeListener(this._rblZoomEndListener);
    },
    /**
		Display a given roadbook. The route displayed is the default route (rank = 0).
        @param {Roadbook} roadbook Roadbook to display
    */
	setRoadbook : function(roadbook)
	{
		if(!this._hasRoute() || roadbook.postKey !== this._roadbook.postKey)
		{
			this._roadbook = roadbook;
			this._idRoute = -1;
			this.showRoute(this._roadbook.getDefaultRoute().idRoute);
		}
	}, 
    /**
		Displays a polyline without a route.
        @param {Mappy.api.map.shape.GPolylineLevels} gPolylineLevels Shapes to display
        @private
    */
	setPolylineLevels : function(gPolylineLevels)
	{
		this._levels = gPolylineLevels;
		this._updateShapes();
	}, 
    /**
		Shape style ("vehicle-mode", "transport", ...) to use for route shapes
        @param {String} style Style to use in itinerary
    */
	setColorStyle : function(style)
	{
		if(this._style !== style)
		{
			this._style = style;
			this._updateShapes();
		}
	},
    /**
		Show a route given its ID (corresponding to its rank attribute).
        @param {Number} idRoute ID of the route to show
    */
	showRoute : function(idRoute)
	{
		if(this._idRoute !== idRoute)
		{
			this._idRoute = idRoute;
			if(this._hasRoute())
			{
				this._updateShapes();
			}
		}
	},
    /**
		Shows alternative routes along with the selected route
        @param {Boolean} showAltRoute If TRUE, then show other route alternative.
    */
	setShowAltRoute : function(showAltRoute)
	{
		if(this._showAltRoute !== showAltRoute)
		{
			this._showAltRoute = showAltRoute;
			this._updateShapes();
		}
	},
    /**
		Given its ID, this method shows the alternative route on the map. Other shapes are hidden.
        @param {int} idRoute The id of the route.
    */
	showAltRoute : function(idAltRoute)
	{
		if(g_isDefined(idAltRoute)
		   && this._previewAltRoute !== idAltRoute
		   && this._previewAltRoute !== this._idRoute)
		{
			this._previewAltRoute = idAltRoute;
			
			this._updateShapes();
		}
	},
    /**
        @param {int} idRoute The id of the route.
    */
	hideAltRoute : function()
	{
		if(this._previewAltRoute !== false)
		{
			this._previewAltRoute = false;
			
			this._updateShapes();
		}
	},
    /**
		Compute actual bounds of this shape layer.
		@return {Mappy.api.geo.GeoBounds} Returns the general bounds of the layer.
		@see ShapeLayer#getBounds
    */
    getBounds : function ()
    {
		this._updateShapes();
        return g_ShapeLayer.prototype.getBounds.call(this);
	},
	/**
		Allows to force some attributes of shape style.
		@param {Object} shapeStyle Values to override. Possible keys are :
									- lineWidth<br />
									- strokeStyle<br />
									- strokeOpacity<br />
									- fillStyle<br />
									- fillOpacity<br />
	 */
	setOverrideShapeStyle : function(shapeStyle)
	{
		if(!g_jQuery.isEmptyObject(shapeStyle))
		{
			this.overShapeStyle = g_jQuery.extend({}, shapeStyle, {modified : true});
			this._updateShapes();
		}
	}
});


////
//// DirectionLayer.js
//// 

var g_DirectionLayer = MappyApi.map.layer.DirectionLayer = g_Class(g_RoadbookLayer, g_EventSource, /** @lends Mappy.api.map.layer.DirectionLayer.prototype */{
    /**
		List of events the map can trigger :<br/>
		- newroute<br/>
		- routedragged<br/>
		- routedragerror<br/>
		- waypointclick
		@type String[]
		@static
    */
	EVENTS : ["newroute", "routedragged", "routedragerror", "waypointclick"],
    maxSteps : null,
    handlerMarker : null,
	/**
		If TRUE then enable actions on markers. Read-only, use setEnableActions() to modify.
		@type Boolean 
	*/
    isEnableActions : null,
	/**
		If TRUE then display icons for waypoints. Read-only, use setShowIcons() to modify.
		@type Boolean 
	*/
	isShowIcons : null,
	/**
		@private
	*/
	_hideNewStepMarker : false,
	/**
		@private
	*/
	_markerLayer : null,
	/**
		@private
	*/
    _movedFromLastRefresh : null,
	/**
		@private
	*/
	_routeService : null,
	/**
		@private
	*/
	_waypoints : null,
	/* @inherited
	_roadbook : null,
	_idRoute : null,
	*/
	/**
		@private
	*/
	_icons :
	{
		"startIcon" : new g_Icon({
            image: "../images/img/poi/poi-route-start_png8.png",
			size : new g_Point(37, 37),
				iconAnchor : new g_Point(18, 18)
        })
		, "endIcon" : new g_Icon({
            image : "../images/img/poi/poi-route-end_png8.png",
			size : new g_Point(37, 37),
				iconAnchor : new g_Point(18, 18)
        })
		, "viaIcon" : new g_Icon({
            image: "../images/img/poi/poi-route-waypoint_png8.png",
			size : new g_Point(25, 25),
			iconAnchor : new g_Point(12, 12)
        })
	},
	/**
		Icons for traffic events. Key is the code alert given in poi.
		@private
	*/
	_trafficEventIcons :
	{
		/*
			From \\int-map-inf\map_2v8\files\TrafficEvents.xml 
			<categories>
				<category Code="1" Label="Ralentissements" Picto="10309"  />
				<category Code="2" Label="Fermeture" Picto="10308" />
				<category Code="3" Label="Incident"  Picto="10305" />
				<category Code="4" Label="Accident"  Picto="10305" />
				<category Code="5" Label="Restrictions de circulation" Picto="10310" />
				<category Code="6" Label="Chaussée réduite" Picto="10306" />
				<category Code="7" Label="Travaux" Picto="10306" />
				<category Code="8" Label="Danger" Picto="10305" />
				<category Code="9" Label="Intempéries" Picto="10304" />
				<category Code="10" Label="Aire de service" Picto="10310" />
				<category Code="11" Label="Rassemblement" Picto="10307" />
				<category Code="12" Label="Police" Picto="10310" />
				<category Code="20" Label="Sans intérêt" Picto="10310" />
			</categories>
		*/
		"evt1" : new g_Icon({
            image:"../images/img/traffic/10309_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		, "evt2" : new g_Icon({
            image: "../images/img/traffic/10308_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		, "evt3" : new g_Icon({
            image: "../images/img/traffic/10305_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		, "evt4" : new g_Icon({
            image: "../images/img/traffic/10305_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		// 10310 : Not used
		//		, "evt5" : new g_Icon({
		//            image: g_staticPath + "img/traffic/10310_5.png",
		//			size : new g_Point(16, 20),
		//			iconAnchor : new g_Point(8, 20)
		//        })
		, "evt6" : new g_Icon({
            image:  "../images/img/traffic/10306_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		, "evt7" : new g_Icon({
            image:  "../images/img/traffic/10306_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		, "evt8" : new g_Icon({
            image: "../images/img/traffic/10305_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		, "evt9" : new g_Icon({
            image:  "../images/img/traffic/10304_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		// 10310 : Not used
		//		, "evt10" : new g_Icon({
		//            image: g_staticPath + "img/traffic/10310_5.png",
		//			size : new g_Point(16, 20),
		//			iconAnchor : new g_Point(8, 20)
		//        })
		, "evt11" : new g_Icon({
            image:"../images/img/traffic/10307_5.png",
			size : new g_Point(16, 20),
			iconAnchor : new g_Point(8, 20)
        })
		// 10310 : Not used
		//		, "evt12" : new g_Icon({
		//            image: g_staticPath + "img/traffic/10310_5.png",
		//			size : new g_Point(16, 20),
		//			iconAnchor : new g_Point(8, 20)
		//        })
		// 10310 : Not used
		//		, "evt20" : new g_Icon({
		//            image: g_staticPath + "img/traffic/10310_5.png",
		//			size : new g_Point(16, 20),
		//			iconAnchor : new g_Point(8, 20)
		//        })
	}, 
    /**
        @constructs
        @augments RoadbookLayer
        @augments EventSource
        @param {Number} zindex z-index to use for this div
        @param {String} colorStyle Styles to use in itinerary
        @param {Boolean} showAltRoute if TRUE then display the alternative route in gray color
    */
    initialize : function (zindex, colorStyle, showAltRoute)
    {
        g_RoadbookLayer.prototype.initialize.call(this, zindex, colorStyle, showAltRoute);
        g_EventSource.prototype.initialize.apply(this);
        this._routeService = new g_RouteService();
        this.maxSteps = 100;
		this.isShowIcons = true;
		this.isEnableActions = true;
		//this._markers = new Array();
		this._markerLayer = new g_MarkerLayer(zindex + 1);
    },
    /**
        @private
    */
    added : function (controller)
    {
        g_RoadbookLayer.prototype.added.call(this, controller);
		
        this._controller.map.addLayer(this._markerLayer);
			
		if(g_isNotDefined(this._newStepMarker))
		{
			this._newStepMarker = this._createNewStepMarker();
			// Set it public. Ugly as it is not documented and only used once in mappy.com to add a tooltip
			this.handlerMarker = this._newStepMarker;
		}
		
        this._dlZoomEndListener = this._controller.map.addListener("zoomend", g_makeCaller(this._updateShapes, this));
    },
    /**
        @private
    */
    removed : function ()
    {
        this._controller.removeLayer(this._markerLayer);
        g_RoadbookLayer.prototype.removed.call(this);
    },
    /**
		Display a given roadbook. The route displayed is the default route (rank = 0).
        @param {Roadbook} roadbook Roadbook to display
        @see RoadbookLayer#setRoadbook
    */
    setRoadbook : function (roadbook)
    {
		if(roadbook != this._roadbook)
		{
			this.geocodingEnable = false;
			this._idRoute = -1;
			g_RoadbookLayer.prototype.setRoadbook.call(this, roadbook);
		}
    },
    /**
		Show a route given its ID (corresponding to its rank attribute).
        @param {Number} idRoute ID of the route to show
        @see RoadbookLayer#showRoute
    */
    showRoute : function (idRoute)
    {
		if(this._idRoute !== idRoute)
		{
			g_RoadbookLayer.prototype.showRoute.call(this, idRoute);
			
			// Enable new step marker if we can add another one
			if (this.getRoute().wayPoints.length < this.maxSteps + 2)
			{
				this._hideNewStepMarker = false;
			}
		}
    },
	/**
		Display waypoints icons on this layer. The waypoint markers are created if they don't exist.
		@param {CertifiedLocation[]} waypoints Waypoints.
		@param {Number} idRoute The id of the route.
	*/
	setWaypoints : function(waypoints, idRoute)
	{
		
		this._waypoints = waypoints;
		
		this._idRoute = idRoute;
		
		this._updateMarkers();
		
	},
    /**
		Update all shapes from the layer.
		@inherited
		@private
	*/
    _updateShapes : function ()
    {
		if(this.isOnMap === true)
		{
			// Update route shapes
			g_RoadbookLayer.prototype._updateShapes.call(this);
			
			// Update icons
			this._updateMarkers();
			
		}
    },
	/**
		Create & add listeners to the marker which will be used to create new waypoints along the road.
		@private
	*/
	_createNewStepMarker : function()
	{
		var that = this;
		var map = this._controller.map;
		
		// Create marker
        var marker = new g_Marker(new g_Coordinates(0, 0), this._icons.viaIcon);
		
		this._hideNewStepMarker = true;
		
        var newStepPosition;
        var lastStepPosition;
        var timerRefresh;
		
		// Drag action
		var _handleDragStartNewStepMarker =  function (event)
        {
            this.geocodingEnable = true;
            lastStepPosition = newStepPosition;
        };
		
		// Drop action
		var _handleDragStopNewStepMarker = function (event)
        {
			var route = this.getRoute();
			
			this.geocodingEnable = false;
			
			var marker = this._newStepMarker;
			if(marker.isOnMap)
			{
				this._markerLayer.removeMarker(marker)
				marker.disableDraggable();
			}
			
			this._hideNewStepMarker = true;
			
			var that = this;
			marker.geocode(function (location)
			{
				var tab = [];
				for (var i = 0; i < route.wayPoints.length; i += 1)
				{
					tab.push(route.wayPoints[i]);
	
					if (i === lastStepPosition)
					{
						tab.push(location[0]);
					}
				}
				
				that._loadNewRoadbook(tab);
			});
			this.trigger("routedragged");
        };
		
		// Showing the marker along the road
		var _handleMouseMoveMap = function (event)
        {
			// If actions are disable, don't show the marker
			if(this.isEnableActions !== true)
			{
				return false;
			}
			
			var marker = this._newStepMarker;
			var route = this.getRoute();
			
			if (g_isDefined(marker)
				&& marker.dragging === false 
				&& map.dragging === false 
				&& this._hideNewStepMarker === false  
				&& this._hasRoute() === true
				&& !route.isMultiModal())
			{
				var i;
				var wp = route.wayPoints;
				for (i = 0; i < wp.length; i += 1)
				{
					if (wp[i].marker.dragging)
					{
						return false;
					}
				}

				var closestPosition;
				var distance = 1000000;
				var shapes = this._shapes;
				for (i = 0; i < shapes.length; i += 1)
				{
					var s = shapes[i];
					var tmpRes = s.getDistance(event);
					if (g_isDefined(tmpRes))
					{
						if (tmpRes.distance < distance)
						{
							newStepPosition = s.getWayPointNum();
							distance = tmpRes.distance;
							closestPosition = tmpRes.position;
						}
					}
				}

				if (g_isDefined(closestPosition))
				{
					if(!marker.isOnMap)
					{
						this._markerLayer.addMarker(marker)
						marker.enableDraggable();
					}
					marker.pos = closestPosition;
					marker.setMarkerPosition();
				}
				else
				{
					if(marker.isOnMap)
					{
						this._markerLayer.removeMarker(marker)
						marker.disableDraggable();
					}
				}
			}
			return false;
        }
		
		// D&D actions attached to Marker
        marker.addListener("dragstart", g_makeCaller(_handleDragStartNewStepMarker, this));
        marker.addListener("dragstop", g_makeCaller(_handleDragStopNewStepMarker, this));

		// Map move : display new step marker along the route shape.
        map.addListener("mousemove", g_makeCaller(_handleMouseMoveMap, this));
		
		return marker;
	},
    /**
		Remove all shapes & markers from the layer.
		@inherited
    */
    clean : function ()
    {
        g_RoadbookLayer.prototype.clean.call(this);
		
		// Removing markers
		this._cleanMarkers();
    },
    /**
		Update all markers from the layer.
		@param {} waypoint Waypoint reprensented by this marker
		@private
    */
    _createMarkerIcon : function (waypoint, idWp)
    {
		// Adding markers for waypoints
		var c = waypoint.Placemark.Point.coordinates;
		var action = { type : '', label : '' };
		if(g_isDefined(waypoint.actions) && g_isDefined(waypoint.actions[this._idRoute || 0]))
		{
			action = waypoint.actions[this._idRoute || 0];
		}
		
		var icon;
		if (action.type === "start")
		{
			icon = this.getStartIcon();
		}
		else if (action.type === "end")
		{
			icon = this.getEndIcon();
		}
		else
		{
			icon = this.getViaIcon();
		}
		marker = new g_Marker(new g_Coordinates(c[0], c[1]), icon);
		marker.addToolTip(action.label);
		this._markerLayer.addMarker(marker);
		marker.addDraggable();
		
		// Handle marker drop : change geolocation of the selected step & re-run route service.
		var hdlFct = (
			function(that, idWp, marker)
			{
				return function(){
					that._dragstopWaypointMarkerHandler(idWp, marker)
				};
			}
		)(this, idWp, marker);
		marker.addListener("dragstop", hdlFct);
		
		// Add a new marker to waypoint geolocations.
		return marker;
	},
	/**
		Drop waypoint marker
		@private
	*/
	_dragstopWaypointMarkerHandler : function(index, marker)
	{
		// Do nothing if actions are not enabled
		if(this._enableActions === false)
		{
			return;
		}
		
		this.geocodingEnable = false;
		var that = this;
		
		marker.geocode(function (location)
		{
			// Copy geocoded locations
			var route = that._roadbook.routes[that._idRoute];
			var tab = [];
			for (var i = 0; i < route.wayPoints.length; i += 1)
			{
				tab.push(route.wayPoints[i]);
			}
			// Replace the right step by the new location ([0] : ignore ambiguities)
			tab[index] = location[0];
			
			// Send new request
			that._loadNewRoadbook(tab);
			
		});
		
		this.trigger("routedragged");
	}, 
    /**
		Update all markers from the layer.
		@private
    */
    _updateMarkers : function ()
    {
		if(!this._hasRoute() && this._waypoints === null )
		{
			return;
		}
		
		// First clean the layer from old markers
		this._cleanMarkers();
		
		var route = this.getRoute();
		var wps = (route !== false) ? route.wayPoints : this._waypoints;
		for(var i in wps)
		{
			var wp = wps[i], that = this;
			
			if(g_isDefined(wp.marker))
			{
				try
                {
                    wp.marker.disableDraggable();
                }
                catch(e)
                {
                    // do nothing : this fail in ie when redraw for print, probably because of the ownerdocument's icon node. So ozef we're rebuilding it right here
                }
				this._markerLayer.removeMarker(wp.marker);
                delete(wp.marker);
			}
			
			wp.marker = this._createMarkerIcon(wp, i);
			
			(function(i, wp)
            {
                wp.marker.addListener("click", function(){
                    that.trigger("waypointclick", i, wp);
                });
            })(i, wp);
			
			this._markerLayer.addMarker(wp.marker);
			
			if(this.isEnableActions === true)
			{
				wp.marker.addDraggable();
			}
		}
		
		// Display Markers for each traffic event
		// Récupération des coordonnées des poi.
		var actions = this.getRoute().actions;
        
        if (!actions || !actions.length) return; //http://jira/browse/RIIIMAPPY-13 (length == undefined kill ie print on iti)
        
		for(var i = 0; i < actions.length ; i++)
		{
			var a = actions[i];
			for(var j = 0; j < a.pois.length ; j++)
			{
				var p = a.pois[j];
				if(p.type === "traffic-event" && p.codeAlert != -1 && g_isDefined(this._trafficEventIcons["evt" + p.codeAlert]))
				{
					// Adding marker to MarkerLayer du marker
					var marker = new g_Marker(p.coordinates, this._trafficEventIcons["evt" + p.codeAlert]);
					marker.addToolTip(p.label);
					this._markerLayer.addMarker(marker);
				}
			}
		}
    }, 
    /**
		Remove all markers from the layer.
		@private
    */
    _cleanMarkers : function ()
    {
		this._markerLayer.clean();
    },
	/**
		Allows the user to enable or disable Drag and drop actions on this layer.
		@param {Boolean} enableActions True if actions (Drag & Drop) are enable on this Layer 
    */
	setEnableActions : function (enableActions)
	{
		this.isEnableActions = (enableActions === true);
		
		if(this._hasRoute())
		{
			for(var wp in this.getRoute().wayPoints)
			{
				var marker = wp.marker;
				if(g_isDefined(marker))
				{
					if(this.isEnableActions)
					{
						marker.enableDraggable();
					}
					else
					{
						marker.disableDraggable();
					}
				}
			}
		}
	},
	/**
		Show or hide waypoints icons & traffic events icon.
		@param {Boolean} showIcons True if icons should be displayed on this layer
    */
	setShowIcons : function (_showIcons)
	{
		this.isShowIcons = _showIcons;
		if(_showIcons === true)
		{
			this._markerLayer.show();
			this._updateMarkers();
		}
		else
		{
			this._markerLayer.hide();
		}
	},
	/**
		Load a new route after a drag & drop. Triggers "newroute" event when completed.
		@param {Array} newLocations Waypoints of the new route.
        @private
    */
	_loadNewRoadbook : function (newLocations)
	{
		var that = this;
		
		var successFct = function (roadbook)
		{
			that.setRoadbook(roadbook);
			
			that.trigger("newroute", that.getRoute());
			
		};
		
		var errorFct = function (error)
		{
			that.trigger("routedragerror", error);
		};
		
		var tmpOptions = g_jQuery.extend(true, {}, this._roadbook.options);
		
		// No optimization if we add a step.
		tmpOptions.route = tmpOptions.route || {};
		tmpOptions.route.optim = 0;
		
		this._routeService.loadRoute(newLocations, tmpOptions, successFct, errorFct);
		
	},
	/**
		Returns the waypoint start icon
        @returns {Mappy.api.ui.Icon} Roadbook start icon
    */
	getStartIcon : function ()
	{
		return this._icons.startIcon;
	},
	/**
		Set the waypoint start icon
		@param {Mappy.api.ui.Icon} icon New roadbook start icon
	*/
	setStartIcon : function(icon)
	{
		this._icons.startIcon = icon;
	},
	/**
		Returns the waypoint end icon
        @returns {Mappy.api.ui.Icon} Roadbook end icon
    */
	getEndIcon : function ()
	{
		return this._icons.endIcon;
	},
	/**
		Set the waypoint end icon
		@param {Mappy.api.ui.Icon} icon New roadbook end icon
	*/
	setEndIcon : function(icon)
	{
		this._icons.endIcon = icon;
	},
	/**
		Returns the waypoint via icon
        @returns {Mappy.api.ui.Icon} Roadbook via icon
    */
	getViaIcon : function ()
	{
		return this._icons.viaIcon;
	},
	/**
		Set the waypoint via icon
		@param {Mappy.api.ui.Icon} icon New roadbook via icon
	*/
	setViaIcon : function(icon)
	{
		this._icons.viaIcon = icon;
	},
	/**
		Set icons for each type of waypoint.
		@param {Object} icons An array-like object of Mappy.api.ui.icon with
						startIcon, endIcon & viaIcon keys
	*/
	setIcons : function(icons)
	{
		this._icons = icons;
	},
	/**
		Set icons for each type of traffic event.
		@param {Object} icons An array-like object of Mappy.api.ui.icon with a keys for each one of these types (from REST API):<br />
			- evt1 : Traffic jam / Tailback<br />
			- evt2 : Road closed<br />
			- evt3 : Hitch<br />
			- evt4 : Accident<br />
			- evt5 : Traffic restraint<br />
			- evt6 : Road restraint<br />
			- evt7 : Roadwork<br />
			- evt8 : Danger<br />
			- evt9 : Weather<br />
			- evt10 : Rest area<br />
			- evt11 : Assembly<br />
			- evt12 : Police<br />
			- evt20 : Wihtout interest<br />
			If the key is absent, then the corresponding event will be hidden.
	*/
	setTrafficEventsIcon : function(icons)
	{
		this._trafficEventIcons = icons;
	}
});


////
//// ShapeStyle.js
//// 

var g_ShapeStyle = MappyApi.map.shape.ShapeStyle = g_Class(/** @lends Mappy.api.map.shape.ShapeStyle.prototype */{
    /**
        @constructs
        @param {Object} style Contains informations on the shape style. Example :<br/>
        {<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; lineWidth: 10, // lines will be 10 pixel large.<br/>
        &nbsp;&nbsp;&nbsp;&nbsp; colorType: ("abgr"|"argb"), // Color format. Due to some legacy with KML, default is "abgr".<br/>
        &nbsp;&nbsp;&nbsp;&nbsp;strokeStyle: "AABBGGRR", // String of 8 characters describing in hexadecimal the values Opacity, Blue, Green, and Red<br/>
        &nbsp;&nbsp;&nbsp;&nbsp;fillStyle: "AABBGGRR"<br/>
        &nbsp;&nbsp;&nbsp;&nbsp;shadowColor: "BBGGRR"<br/>
        &nbsp;&nbsp;&nbsp;&nbsp;shadowBlur: 0 // Integer specifying blur level of shadow<br/>
        }
    */
    initialize : function (style)
    {
        style = style || {};
        
        this.setLineWidth(g_isDefined(style.lineWidth) ? style.lineWidth : 5);
        this.setShadowBlur(g_isDefined(style.shadowBlur) ? style.shadowBlur : 0);
        
        // Processing function to convert from argb to abgr
        var _processColor = function(c)
        {
            var offset = c.substr(0, 1) === "#" ? 1 : 0;
            var i = 0;
            var a = c.length >= 8 ? c.substr(offset + 2 * i++, 2) : 'B2';
            var r = c.substr(offset + 2 * i++, 2)
            var g = c.substr(offset + 2 * i++, 2)
            var b = c.substr(offset + 2 * i++, 2)
            return a + b + g + r;
        }
        
        // Default
        var strokeStyle = 'B2FF0000';
        if(g_isDefined(style.strokeStyle))
        {
            strokeStyle = style.strokeStyle;
            if(g_isDefined(style.colorType) && style.colorType === "argb")
            {
                strokeStyle = _processColor(strokeStyle);   
            }
        }
        this.setStrokeStyle(strokeStyle);
        
        
        // Default
        var fillStyle = 'B2FF0000';
        if(g_isDefined(style.fillStyle))
        {
            fillStyle = style.fillStyle;
            if(g_isDefined(style.colorType) && style.colorType === "argb")
            {
                fillStyle = _processColor(fillStyle);   
            }
        }
        this.setFillStyle(fillStyle);

        // Default
        var shadowColor = 'black';
        if(g_isDefined(style.shadowColor))
        {
            shadowColor = style.shadowColor;
            if(g_isDefined(style.colorType) && style.colorType === "argb")
            {
                shadowColor = _processColor(shadowColor);
            }
        }
        this.setShadowColor(shadowColor);
    },
    /**
    @param {Number} lineWidth
    */
    setLineWidth : function (lineWidth)
    {
        this._lineWidth = lineWidth;
    },
    /**
    @param {String} style
    */
    setStrokeStyle : function (style)
    {
        this._strokeStyle = style;
    },
    /**
    @param {String} style
    */
    setFillStyle : function (style)
    {
        this._fillStyle = style;
    },
    /**
     @param {String} style
     */
    setShadowColor : function (style)
    {
        this._shadowColor = style;
    },
    /**
     @param {String} style
     */
    setShadowBlur : function (style)
    {
        this._shadowBlur = style;
    },
    /**
    @returns {Number}
    */
    getLineWidth : function ()
    {
        return this._lineWidth;
    },
    /**
    @returns {String}
    */
    getStrokeStyle : function ()
    {
        return this._strokeStyle;
    },
    /**
    @returns {String}
    */
    getFillStyle : function ()
    {
        return this._fillStyle;
    },
    /**
    @returns {String}
    */
    getShadowBlur : function ()
    {
        return this._shadowBlur;
    },
    /**
    @returns {String}
    */
    getShadowColor : function ()
    {
        return this._shadowColor;
    },
    /**
    Clone the style.
    @returns {Mappy.api.map.shape.ShapeStyle}
    */
    clone : function ()
    {
        return new g_ShapeStyle({
            lineWidth : this._lineWidth,
            strokeStyle : this._strokeStyle,
            fillStyle : this._fillStyle,
            shadowColor : this._shadowColor,
            shadowBlur : this._shadowBlur
        });
    }
});


////
//// Shape.js
//// 

var g_Shape = g_Class(g_EventSource, /** @lends Shape.prototype */{
    /**
    List of events a shape can trigger :<br/>
    - click<br/>
    - mouseover<br/>
    - mouseout<br/>
    - mousedown<br/>
    - mouseup
    @static
    @type String[]
    */
    EVENTS : ["click", "mouseover", "mouseout", "mousedown", "mouseup"],
    /**
        Is the shape on a map?
        @type boolean
    */
    isOnMap : false,
    /**
        Is the cursor over the shape?
        @type boolean
    */
    isOver : false,
    /**
        @constructs
        @augments EventSource
        @param {Mappy.api.geo.Coordinates[]} coords Array of {@link Mappy.api.geo.Coordinates} that draw the shape.
        @param {Mappy.api.map.shape.ShapeStyle} style
               
        @eventdescription click Fires when a click occurs on the shape
        @eventdescription#click {MouseEvent} e Native onclick event on the shape.
        
        @eventdescription mouseover Fires when a mouseover occurs on the shape
        @eventdescription#mouseover {MouseEvent} e Native mouseover event on the shape.
        
        @eventdescription mouseout Fires when a mouseout occurs on the shape
        @eventdescription#mouseout {MouseEvent} e Native mouseout event on the shape.
        
        @eventdescription mouseup Fires when a mouseup occurs on the shape
        @eventdescription#mouseup {MouseEvent} e Native mouseup event on the shape.
        
        @eventdescription mousedown Fires when a mousedown occurs on the shape
        @eventdescription#mousedown {MouseEvent} e Native mousedown event on the shape.
    */
    initialize : function (coords, style, type)
    {
        g_EventSource.prototype.initialize.apply(this);

        this.type = type;
        this._style = style;
        this._coords = coords;

        var boundingBox;
        var l = coords.length;

        if (l > 0)
        {
            boundingBox = new g_GeoBounds(coords[0], coords[0]);
            var c;
            
            for (var i = 0; i < l; i += 1)
            {
                c = coords[i];
                boundingBox.extend(c);
            }
            boundingBox.refreshCenter();
        }

        this.boundingBox = boundingBox;
    },
    /**
    @private
    */
    added : function (container, controller)
    {
        this._controller = controller;
        this._drawer = g_getDrawer(container, 40, this.type);
        this._drawer.setStyle(this._style);
        this.isOnMap = true;
    },
    /**
    @private
    */
    calcPoints : function ()
	{		
        var coords = this._coords;
        var points = [];
        var converter = this._controller.converter;

        for (var i = 0, l = coords.length; i < l; i += 1)
        {
            points.push(converter.toGeolayerPixels(coords[i]));
        }

        
        if (points.length > 2)
        {
            var pts2 = [];
            pts2.push(points[0]);
            var aire;

            for (var i = 2; i < points.length; i += 1)
            {
                aire = Math.abs(
                    (points[i - 1].x - points[i].x) * (points[i - 1].y - pts2[pts2.length - 1].y) -
                    (points[i - 1].y - points[i].y) * (points[i - 1].x - pts2[pts2.length - 1].x)
                );
                if (aire >= 1)
                {
                    pts2.push(points[i -1]);
                }
            }
            pts2.push(points[i - 1]);
            this.points = pts2;
        }
        else
        {
            this.points = points;
        }
    },
    /**
    @private
    */
    draw : function (viewBox)
    {
        this.clean();
        var converter = this._controller.converter;
        var drawBox = viewBox.intersect(this.boundingBox);
        if (g_isDefined(drawBox))
        {
            var ne = converter.toGeolayerPixels(drawBox.ne);
            var sw = converter.toGeolayerPixels(drawBox.sw);

            var bbox = new g_Bounds(sw.x, ne.y, ne.x, sw.y);
            this._drawer.setBoundingBox(bbox);
        }
    },
    /**
        Retrun the array of coordinates that draws the shape.
        @returns {Mappy.api.geo.Coordinates[]} Array of {@link Mappy.api.geo.Coordinates}
    */
    getCoordinates : function ()
    {
        return this._coords;
    },
    /**
        Set the style of the shape
        @param {Mappy.api.map.shape.ShapeStyle} style A style
    */
    setStyle : function (style)
    {
        this._style = style;
        if (this.isOnMap)
        {
            this._drawer.setStyle(style);
            this._drawer.refreshStyle();
        }
    },
    /**
        Retrun the style of the shape.
        @returns {Mappy.api.map.shape.ShapeStyle} Current style of the shape
    */
    getStyle : function ()
    {
        return this._style;
    },
    /**
    @private
    */
    clean : function ()
    {
        this._drawer.clean();
    },
    /**
    @private
    */
    removed : function ()
    {
        this._drawer.removed();
        this.isOnMap = false;
    },
    /**
    @private
    */
    isInShape : function ()
    {
        return false;
    },
    /**
        Return the geo bounds of the shape.
        @returns {Mappy.api.geo.GeoBounds} Bounds of the shape
    */
    getBounds : function ()
    {
        return this.boundingBox;
    }, 
    /**
        Clone this instance of Shape
        @return a new instance of Shape which is the exact copy to this shape.
    */
    clone : function ()
    {
        return new g_Shape(this._coords, this._style, this.type);
    }
});


////
//// Line.js
//// 

var g_Line = MappyApi.map.shape.Line = g_Class(g_Shape, /** @lends Mappy.api.map.shape.Line.prototype */{
    /**
        @constructs
        @augments Shape
        @param {Mappy.api.geo.Coordinates[]} coords Array of {Mappy.api.geo.Coordinates} that draw the line. Length should be > 1.
        @param {Mappy.api.map.shape.ShapeStyle} style
    */
    initialize : function (coords, style)
    {
        g_Shape.prototype.initialize.call(this, coords, style, "line");
    },
    /**
    @private
    */
    draw : function (viewBox)
    {
        g_Shape.prototype.draw.call(this, viewBox);
        this._drawer.line(this.points);
    },
    /**
    @private
    */
    isInShape : function (event)
    {
        var offset = this._drawer.div.offset();
        var divPos = this._drawer.div.position();

        var pos = [event.pageX - offset.left + divPos.left, event.pageY - offset.top + divPos.top];

        var lineWidth = this._drawer.getStyle().getLineWidth();
        var testWidth = (lineWidth / 2) * (lineWidth / 2);

        var ABx, ABy, ACx, ACy, AB2, pscal, factor, AD2, ADx, ADy, CD, d;

        /**
        *             C x           <br/>
        *               |           <br/>
        *               |           <br/>
        *   A x---------x-----x B   <br/>
        *               D
        */
        var points = this.points;
        for (var i = 1; i < points.length; i += 1)
        {
            ABx = points[i].x - points[i - 1].x;
            ABy = points[i].y - points[i - 1].y;
            ACx = pos[0] - points[i - 1].x;
            ACy = pos[1] - points[i - 1].y;

            AB2 = (ABx * ABx) + (ABy * ABy);

            pscal = ABx * ACx + ABy * ACy;

            factor = pscal / AB2;

            if (factor < 0)
            {
                factor = 0;
                AD2 = 0;
            }
            else if (factor > 1)
            {
                factor = 1;
                AD2 = AB2;
            }
            else
            {
                AD2 = (pscal * pscal) / AB2;
            }

            ADx = factor * ABx;
            ADy = factor * ABy;

            CD = [ADx - ACx, ADy - ACy];

            d  = (CD[0] * CD[0]) + (CD[1] * CD[1]);

            if (d <= testWidth)
            {
                return true;
            }
        }
        return false;
    },
    /**
    @private
    */
    getDistance : function (event)
    {
        var offset = this._drawer.div.offset();
        var divPos = this._drawer.div.position();
        var posX = event.pageX - offset.left + divPos.left;
        var posY = event.pageY - offset.top + divPos.top;
        
        var ABx, ABy, ACx, ACy, AB2, pscal, factor, AD2, ADx, ADy, CD, d;
        var ret;
        var distanceMin = 1000;
        /**
        *             C x           <br/>
        *               |           <br/>
        *               |           <br/>
        *   A x---------x-----x B   <br/>
        *               D
        */
        var points = this.points;
        for (var i = 1; i < points.length; i += 1)
        {
            ABx = points[i].x - points[i - 1].x;
            ABy = points[i].y - points[i - 1].y;
            ACx = posX - points[i - 1].x;
            ACy = posY - points[i - 1].y;

            AB2 = (ABx * ABx) + (ABy * ABy);

            pscal = ABx * ACx + ABy * ACy;

            factor = pscal / AB2;

            if (factor < 0)
            {
                factor = 0;
                AD2 = 0;
            }
            else if (factor > 1)
            {
                factor = 1;
                AD2 = AB2;
            }
            else
            {
                AD2 = (pscal * pscal) / AB2;
            }

            ADx = factor * ABx;
            ADy = factor * ABy;

            CD = [ADx - ACx, ADy - ACy];

            d  = (CD[0] * CD[0]) + (CD[1] * CD[1]);

            if (d <= distanceMin)
            {
                distanceMin = d;
                ret = {
                    position : new g_Point(ADx + points[i - 1].x, ADy + points[i - 1].y),
                    distance : d
                };
            }
        }
        return ret;
    }
});


////
//// RoadSectionLine.js
//// 

var g_RoadSectionLine = g_Class(g_Line, /** @lends RoadSectionLine.prototype */{
	/**
		Waypoint index in the route's waypoint array.
		@type int
		@private
	*/
	_wayPointNum : 0
	/**
        @constructs
        @augments Line
        @param {Mappy.api.geo.Coordinates[]} coords Array of {Mappy.api.geo.Coordinates} that draw the line. Length should be > 1.
        @param {Mappy.api.map.shape.ShapeStyle} style Style of this shape
		@param {int} wpNum The waypoint number
    */
	, initialize : function (coords, style, wpNum)
    {
        g_Line.prototype.initialize.call(this, coords, style);
        this.setWayPointNum(wpNum);
    }
    /**
		Set the waypoint number of this road section shape : it'll be useful to know where to insert new waypoint.
		@param {int} wpNum The new waypoint number
    */
    , setWayPointNum : function (wpNum)
    {
        this._wayPointNum = wpNum;
    }
    /**
		Get the waypoint number of this road section shape : it'll be useful to know where to insert new waypoint.
		@return {int} The waypoint number
    */
    , getWayPointNum : function ()
    {
        return this._wayPointNum;
    },
    /**
        Clone this instance of Shape
        @return a new instance of Shape which is the exact copy to this shape.
    */
    clone : function ()
    {
        return new g_RoadSectionLine(this._coords, this._style, this._wayPointNum);
    }
});


////
//// Polygon.js
//// 

var g_Polygon = MappyApi.map.shape.Polygon = g_Class(g_Shape, /** @lends Mappy.api.map.shape.Polygon.prototype */{
    /**
        @constructs
        @augments Shape
        @param {Mappy.api.geo.Coordinates[]} coords Array of {@link Mappy.api.geo.Coordinates} that draw the polygon. Length should be > 2.
        @param {Mappy.api.map.shape.ShapeStyle} style
    */
    initialize : function (coords, style)
    {
        g_Shape.prototype.initialize.call(this, coords, style, "polygon");
    },
    /**
    @private
    */
    draw : function (viewBox)
    {
        g_Shape.prototype.draw.call(this, viewBox);
        this._drawer.polygon(this.points);
    },
    /**
    @private
    */
    isInShape : function (event)
    {
        var offset = this._drawer.div.offset();
        var divPos = this._drawer.div.position();
        var pos = [event.pageX - offset.left + divPos.left, event.pageY - offset.top + divPos.top];
        
        var points = this.points;
        var polySides = points.length;
        var i, j = polySides - 1;
        var oddNodes = false;

        for (i = 0; i < polySides; i += 1)
        {
            if (points[i].y < pos[1] && points[j].y >= pos[1] ||
                points[j].y < pos[1] && points[i].y >= pos[1])
            {
                if (points[i].x + (pos[1] - points[i].y) / (points[j].y - points[i].y) * (points[j].x - points[i].x) < pos[0])
                {
                    oddNodes = !oddNodes;
                }
            }
            j = i;
        }
        return oddNodes;  
    }
});


////
//// Circle.js
//// 

// var g_Circle = MappyApi.map.shape.Circle = g_Class(g_Polygon, /** @lends Mappy.api.map.shape.Circle.prototype */{
var g_Circle = MappyApi.map.shape.Circle = g_Class(g_Shape, /** @lends Mappy.api.map.shape.Circle.prototype */{
    /**
        @constructs
        @augments Mappy.api.map.shape.Polygon
        @param {Mappy.api.geo.Coordinates} coords Center of the circle.
        @param {Number} radius Radius of the circle in meter.
        @param {Mappy.api.map.shape.ShapeStyle} style
    */
    initialize : function (center, radius, style)
    {
        var EARTH_RADIUS = 6378137;
        var EARTH_RADIUS2 = 6356752.314;
        var SQR_EARTH_RADIUS = EARTH_RADIUS * EARTH_RADIUS;
        var SQR_EARTH_RADIUS2 = EARTH_RADIUS2 * EARTH_RADIUS2;

        /**
        @private
        */
        function WGSPlusMeters(coordWGS, dxMeter, dyMeter)
        {
            var dlat_rad = dyMeter / EARTH_RADIUS; // in radian
            var tany2 = Math.tan(coordWGS.y * Math.PI / 180);
            tany2 = tany2 * tany2;
            var dlon_rad = (dxMeter * Math.sqrt(SQR_EARTH_RADIUS + (SQR_EARTH_RADIUS2 * tany2))) / SQR_EARTH_RADIUS;

            var res = new g_Coordinates(
                coordWGS.x + dlon_rad * 180 / Math.PI,
                coordWGS.y + dlat_rad * 180 / Math.PI
            );
            return res;
        }

        g_Shape.prototype.initialize.call(this, [], style, "circle");
        
        this._coords = [];
        this._coords.push(center);
        this._coords.push(WGSPlusMeters(center, radius, 0)); // reference point to recalculate the radius
        this._coords.push(WGSPlusMeters(center, 0, radius));
        this._coords.push(WGSPlusMeters(center, -radius, 0));
        this._coords.push(WGSPlusMeters(center, 0, -radius));
        
        this.boundingBox = new g_GeoBounds(this._coords[0], this._coords[0]);
        
        var l = this._coords.length;
        
        for (var i = 0; i < l; i += 1)
        {
            this.boundingBox.extend(this._coords[i]);
        }

        this.boundingBox.refreshCenter();
    },
    /**
    @private
    */
    added : function (container, controller)
    {
        this._controller = controller;
        this._drawer = g_getDrawer(container, 40, "circle");
        this._drawer.setStyle(this._style);
        this.isOnMap = true;
    },
    /**
    @private
    */
    draw : function (viewBox)
    {
        g_Shape.prototype.draw.call(this, viewBox);
        var converter = this._controller.converter;
        var center = converter.toGeolayerPixels(this._coords[0]);
        var radius = converter.toGeolayerPixels(this._coords[1]).x - center.x;
        this._drawer.circle(center, radius);
    }
});


////
//// GPolyline.js
//// 

var g_GPolyline = MappyApi.map.shape.GPolyline = g_Class(g_Line, /** @lends GPolyline.prototype */{
    /**
        @private
        @constructs
        @augments Mappy.api.map.shape.Line
    */
    initialize : function (polyline, shapeStyle)
    { 
		var coordinates = g_decodeLine(polyline);
        g_Line.prototype.initialize.call(this, coordinates, shapeStyle);
    }
});

/**
	Decodes a polyline
	@private
*/
var g_GPolylineDecoder = MappyApi.map.shape.GPolylineDecoder = g_Class(/** @lends g_GPolylineDecoder.prototype */{
    /**
        @private
        @constructs
        @augments Mappy.api.map.shape.Line
    */
    initialize : function (encoded)
    { 
		this._encoded = encoded;
		this._index = 0;
    },
	/**
		@return Array An array of values from the polyline
	*/
	decode : function()
	{
		var len = this._encoded.length;
		this._index = 0;
		var array = [];
		while (this._index < len) {
		  array.push(this.g_decodeNextPoint());
		}
		return array;
	},
	g_decodeNextPoint : function()
	{
		var b;
		var shift = 0;
		var result = 0;
		do
		{
			//get binary encodings
			b = this._encoded.charCodeAt(this._index++) - 63;
			//binary shift
			result |= (b & 0x1f) << shift;
			//move to next chunk
			shift += 5;
		} while (b >= 0x20); //see if another binary value
		//if negative, flip bits & return
		return (((result & 1) > 0) ? ~(result >> 1) : (result >> 1));
	}
});


/**
 * Decoding Polylines
 */

/**
	Decode a polyline
	@private 
 */
var g_decodeLine = function (encoded)
{
	var array = [];
	var lng = 0;
	var lat = 0;
	var decoded = new g_GPolylineDecoder(encoded).decode();
	for(var i = 0; i < decoded.length ; i +=2)
	{
		lng += decoded[i] * 1e-5;
		lat += decoded[i+1] * 1e-5;
		array.push(new g_Coordinates(lat, lng));
	}
	return array;
}

/**
 * Decode a polyline-levels
 * @private 
 */
var g_decodeZoomLevels = function (encoded)
{
	var array = [];
	var zoom = -1;
	var decoded = new g_GPolylineDecoder(encoded).decode();
	for(var i = 0; i < decoded.length ; i ++)
	{
		zoom += decoded[i];
		array.push(zoom);
	}
	return array;
}


////
//// GPolylineLevels.js
//// 

var g_GPolylineLevels = MappyApi.map.shape.GPolylineLevels = g_Class(/** @lends GPolylineLevels.prototype */{
    /**
    @private
    @type Array
    */
    _shapes : null,
    /**
    @private
    @type Array
    */
    _coords : null,
    /**
    @private
    @type Array
    */
    _zoomLevels : null,
    /**
    @private
    @type Array
    */
    _wayPointActionsPid : null,
    /**
    @private
    @type Array
    */
    _stylesSections : null,
    /**
    @private
    @type StylesDictionnary
    */
    _stylesDictionnary : null,
    /**
    @private
    @type ShapeStyle
    */
    _disableStyle : null,
    /**
        @constructs
        @param {Object} dataPolylineDefinition Polyline definition (JSON).
        @param {Object} stylesDictionnary Styles that can be used by this line
        @param {Array} wayPointActionsPid Polyline indexes for waypoints.
    */
    initialize : function (dataPolylineDefinition, stylesDictionnary, wayPointActionsPid)
    {
        this._shapes = new Array();
        this._stylesDictionnary = stylesDictionnary;
        
        this._coords = g_decodeLine(dataPolylineDefinition.polyline);
        this._zoomLevels = g_decodeZoomLevels(dataPolylineDefinition["polyline-levels"]);
        this._stylesSections = this._getStylesSections(g_getAsArray(dataPolylineDefinition["road-section-collection"]["road-sections"]));
        
        this._wayPointActionsPid = g_isDefined(wayPointActionsPid) ? wayPointActionsPid : [];
        this._disableStyle = new g_ShapeStyle({strokeStyle : "B2666666"});
    },
    /**
        @private
    */
    _getStylesSections : function (roadSections)
    {
        // Styles sections
        var sectionStyles = new Array();
        for(var i in roadSections)
        {
            var t = roadSections[i]["type"];
            
            // Create array if not exists
            if(g_isNotDefined(sectionStyles[t]))
            {
                sectionStyles[t] = new Array();
            }
            
            var s = roadSections[i]["road-section"].split(" ");
            var plId = 0;
            for(var j = 0; j < s.length; j += 2)
            {
                var styleId = parseInt(s[j]);
                if(this._stylesDictionnary.existsShapeStyle(t, styleId))
                {
                    sectionStyles[t][parseInt(plId)] = styleId;
                }
                plId = parseInt(s[j+1]);
            }
        }
        return sectionStyles;
    },
    /*
        Override shape style definition, given some parameters.
        @private
     */
    _doOverrideShapeStyle : function(shapeStyle, overShapeStyle)
    {
        // Override the shapestyle with values from outside this API.
        if(!g_jQuery.isEmptyObject(overShapeStyle))
        {
            // Linewidth : easy to do.
            if(g_isDefined(overShapeStyle.lineWidth))
            {
                shapeStyle.setLineWidth(overShapeStyle.lineWidth);
            }
            
            /*
             * Setting colors & Opacity managment
             */
            var replaceOpacity = function(colorStr, newOpacity)
            {
                var alpha = parseInt(255 * newOpacity).toString(16);
                return alpha + colorStr.substr(2, 6);
            }
            
            // Stroke
            var newStrokeOpacity = g_isDefined(overShapeStyle.strokeOpacity) ? overShapeStyle.strokeOpacity : null ;
            var newStrokeStyle = g_isDefined(overShapeStyle.strokeStyle) ? overShapeStyle.strokeStyle : shapeStyle.getStrokeStyle() ;
            if(newStrokeOpacity != null)
            {
                newStrokeStyle = replaceOpacity(newStrokeStyle, newStrokeOpacity);
            }
            else if(g_isNotDefined(overShapeStyle.strokeStyle))
            {
                newStrokeStyle = null;
            }
            if(newStrokeStyle !== null)
            {
                shapeStyle.setStrokeStyle(newStrokeStyle);
            }
            
            // Fill
            var newFillOpacity = g_isDefined(overShapeStyle.fillOpacity) ? overShapeStyle.fillOpacity : null ;
            var newFillStyle = g_isDefined(overShapeStyle.fillSytle) ? overShapeStyle.fillSytle : shapeStyle.getFillStyle() ;
            if(newFillOpacity != null)
            {
                newFillStyle = replaceOpacity(newFillStyle, newFillOpacity);
            }
            else if(g_isNotDefined(overShapeStyle.fillSytle))
            {
                newFillStyle = null;
            }
            if(newFillStyle !== null)
            {
                shapeStyle.setFillStyle(newFillStyle);
            }
        }
        
    }, 
    /**
        Construct shapes to display for a given zoomlevel and a given coloration style
        @param {Integer} zoomlevel Zoomlevel to get. Optional (all segments are displayed).
        @param {String} styleType Style type in coloration dictionnaries. Optional (use default style).
        @param {Boolean} disableState If TRUE, display shapes in gray. Optional (by default, FALSE).
		@param {Object} overShapeStyle Values to override. Possible keys are :
									- lineWidth<br />
									- strokeStyle<br />
									- strokeOpacity<br />
									- fillStyle<br />
									- fillOpacity<br />
    */
    getShapes : function (zoomlevel, styleType, disableState, overShapeStyle)
    {
        overShapeStyle = overShapeStyle || {};
        // Default parameters
        var pZoom = -1
        if(g_isDefined(zoomlevel)
           && g_ViewMode.mMaxZoomLevel > zoomlevel)
        {
            pZoom = zoomlevel;
        }
        
        var pStyleType = styleType || "none";
        if(disableState)
        {
            pStyleType = pStyleType + "_disable";
        }
        
        // Creating array of shapes that will be reused if needed.
        if(g_isNotDefined(this._shapes[pZoom]))
        {
            this._shapes[pZoom] = [];
        }
        var shapes = this._shapes[pZoom][pStyleType];
        
        if(g_isNotDefined(shapes) || overShapeStyle.modified === true)
        {
            shapes = [];
            // Coordinates of current shape
            var coordsShapes = [];
            
            // Current road section Id : Useful to know where to insert new waypoint in DirectionLayer.
            var roadSectionId = 0;
            
            // If there is no style for this type, just get a new Array. Default style will be used.
            var st = this._stylesSections[pStyleType] || [];
            var shapeStyle = this._disableStyle;
            if(disableState !== true)
            {
                shapeStyle = this._stylesDictionnary.getShapeStyle(pStyleType, 0);
            }
            
            this._doOverrideShapeStyle(shapeStyle, overShapeStyle);
            
            // For each coordinates from the decoded polyline...
            for(var polyLineIndex = 0;
                polyLineIndex < this._coords.length;
                polyLineIndex ++)
            {
                
                // Handle new style if needed : add another shape.
                var doStyle = disableState !== true && g_isDefined(st[polyLineIndex]);
                
                // Handle Waypoint : add another shape.
                var doWaypoint = (g_jQuery.inArray(polyLineIndex, this._wayPointActionsPid) >= 0);
                
                // If current coordinates shouldn't be display on the given zoom level, we discard it.
                // Except if this is a waypoint or a new style
                if(0 <= pZoom && pZoom < this._zoomLevels[polyLineIndex]
                   && !doStyle
                   && !doWaypoint
                   // This is not the last coordinate
                   && polyLineIndex < this._coords.length - 1)
                {
                    continue;
                }
                
                // If it's the first coordinates we process, do not add a new shape
                if(polyLineIndex > 0
                   && (doStyle || doWaypoint))
                {
                    coordsShapes.push(this._coords[polyLineIndex]);
                    // Push shapes
                    shapes.push(new g_RoadSectionLine(coordsShapes, shapeStyle, roadSectionId));
                    coordsShapes = new Array();
                }
                
                // Update style
                if(doStyle)
                {
                    var shapeStyleId = st[polyLineIndex];
                    shapeStyle = this._stylesDictionnary.getShapeStyle(pStyleType, shapeStyleId);
                    this._doOverrideShapeStyle(shapeStyle, overShapeStyle);
                }
                
                // Update waypoint id
                if(doWaypoint)
                {
                    roadSectionId ++;
                }
                
                // Add new coordinates to polyline
                coordsShapes.push(this._coords[polyLineIndex]);
            }
            
            // Push last shapes
            shapes.push(new g_RoadSectionLine(coordsShapes, shapeStyle, roadSectionId));
            
            // Keeping a copies of shapes in memory for further use
            this._shapes[pZoom][pStyleType] = shapes;
        }
        
        overShapeStyle.modified = false;
        return shapes;
    },
    /**
        Retrieve the roadsection type value of the given polyline index
        @param {Integer} polylineIndex The polyline index
        @param {String} styleType Style type in coloration dictionnaries.
    */
    getStyleId : function (polylineIndex, styleType)
    {        
        var pStyleType = styleType || "none";
        
        var pid = parseInt(polylineIndex);
        
        var st = this._stylesSections[pStyleType] || [];
        
        // If not found, return the last pid
        var styleId = st[st.length - 1];
        
        for(var i in st)
        {
            // If we just pass the wanted pid (style pid is greater than looked for pid) 
            if(g_isDefined(st[i]) && st[i] > pid)
            {
                // Return the last style id
                styleId = st[i - 1];
                break;
            }
        }
        
        return styleId;
    }
});


////
//// KmlImage.js
//// 

var g_KmlImage = g_Class(/** @lends KmlImage.prototype */{
    /**
        @private
        @constructs
    */
    initialize : function (groundOverlay)
    {
        this._url = groundOverlay.Icon.href;
        
        this._coords = {
            nw: {
                x: parseFloat(groundOverlay.LatLonBox.west),
                y: parseFloat(groundOverlay.LatLonBox.north)
            },
            se: {
                x: parseFloat(groundOverlay.LatLonBox.east),
                y: parseFloat(groundOverlay.LatLonBox.south)
            }                    
        };
    },
    configure : function (container, map)
    {
        this._map = map;
        this._map.addListener(this);
        
        this.container = container;
        this.img = g_jQuery("<img style='position:absolute;' src='" + this._url + "'></img>");
        this.container.append(this.img);
        
        if (this._map.isReady())
        {
            this.calcPosition(this._map.getZoomLevel());
            this.setPosition();
        }
    },
    draw : function ()
    {
        this.setPosition();
    },
    setPosition : function ()
    {
        this.img.css('left', this.points.nw[0]);
        this.img.css('top', this.points.nw[1]);
        this.img.css('width', this.points.se[0] - this.points.nw[0]);
        this.img.css('height', this.points.se[1] - this.points.nw[1]);
    },
    calcPosition : function (zoom)
    {
        this.points = {};
        this.zoomLevel = zoom;
        
        this.points.nw = this._map.getDraggableLayersContainer().getMapLayer().getPosition(this._map.getDraggableLayersContainer().getMapLayer().getTileInfoFromCoordinates(this._coords.nw, this.zoomLevel));
        this.points.se = this._map.getDraggableLayersContainer().getMapLayer().getPosition(this._map.getDraggableLayersContainer().getMapLayer().getTileInfoFromCoordinates(this._coords.se, this.zoomLevel));
    },
    onMapBuildBegin : function (data)
    {
        var newZoom = this._map.getZoomLevel();
        if (this.zoomLevel !== newZoom)
        {
            this.calcPosition(newZoom);
            this.setPosition();
        }
    }
});


////
//// KmlLineString.js
//// 

var g_KmlLineString = g_Class(g_Line, /** @lends KmlLineString.prototype */{
    /**
        @private
        @constructs
        @agments Mappy.api.map.shape.Line
    */
    initialize : function (lineString, style, placemark)
    {
        this.Placemark = placemark;
        
        var coordinatesString = lineString.coordinates;
        coordinatesString = coordinatesString.split(/\s+/);
        var coordinates = [];
        var tmp;
        for (var i = 0, l = coordinatesString.length; i < l; i += 1)
        {
            tmp = coordinatesString[i].split(',');
            if (tmp.length === 2 || tmp.length === 3)
            {
                coordinates.push(new g_Coordinates(tmp[0],tmp[1]));
            }
        }
        
        style = style || {};
        style.LineStyle = style.LineStyle || {};
        
        var shapeStyle = new g_ShapeStyle({
            lineWidth : style.LineStyle.width,
            strokeStyle : style.LineStyle.color
        });

        g_Line.prototype.initialize.call(this, coordinates, shapeStyle);
    }
});


////
//// KmlPolygon.js
//// 

var g_KmlPolygon = g_Class(g_Polygon, /** @lends KmlPolygon.prototype */{
    /**
        @private
        @constructs
        @agments Mappy.api.map.shape.Polygon
    */
    initialize : function (polygon, style, placemark)
    {
        this.Placemark = placemark;
        
        var coordinatesString = polygon.outerBoundaryIs.LinearRing.coordinates;
        coordinatesString = coordinatesString.split(/\s+/);
        
        var coordinates = [];
        var tmp;
        for (var i = 0, l = coordinatesString.length; i < l; i += 1)
        {
            tmp = coordinatesString[i].split(',');
            if (tmp.length === 2 || tmp.length === 3)
            {
                coordinates.push(new g_Coordinates(tmp[0],tmp[1]));
            }
        }
        
        style = style || {};
        style.LineStyle = style.LineStyle || {};
        style.PolyStyle = style.PolyStyle || {};
        
        var shapeStyle = new g_ShapeStyle({
            lineWidth : style.LineStyle.width,
            strokeStyle : style.LineStyle.color,
            fillStyle : style.PolyStyle.color
        });
        
        g_Polygon.prototype.initialize.call(this, coordinates, shapeStyle);
    }
});


////
//// KmlReader.js
//// 

var g_KmlReader = MappyApi.map.shape.kml.KmlReader = g_Class(/** @lends Mappy.api.map.shape.kml.KmlReader.prototype */{
    /**
        @constructs
    */
    initialize : function ()
    {
    },
    /**
        @param {} kml Kml object.
        @param {Boolean} fromRecur fromRecur.
    */
    getShapes : function (kml, fromRecur)
    {
        if (fromRecur !== true)
        {
            this.shapes = [];
            this.styles = {};
        }
        var tag;
        for (tag in kml)
        {
            if (kml.hasOwnProperty(tag))
            {
                if (tag === "Style")
                {
                    this._computeStyle(kml[tag]);
                }
            }
        }
        
        for (tag in kml)
        {
            if (kml.hasOwnProperty(tag))
            {
                if (tag === "Document" ||
                    tag === "Folder")
                {
                    var containers = g_jQuery.makeArray(kml[tag]);
                    for (var i = 0; i < containers.length; i += 1)
                    {
                        this.getShapes(containers[i], true);
                    }
                }
                
                if (tag === "GroundOverlay")
                {
                    this._computeGroundOverlay(kml[tag]);
                }

                if (tag === "Placemark")
                {
                    this._computePlacemark(kml[tag]);
                }
            }
        }

        if (fromRecur !== true)
        {
            return this.shapes;
        }
    },
    /**
        @private
    */
    _computeStyle : function (node)
    {
        var styles = g_jQuery.makeArray(node);
        var singleStyle;
        for (var i = 0; i < styles.length; i += 1)
        {
            if (g_isDefined(styles[i].id))
            {
                this.styles[styles[i].id] = styles[i];
            }
            else if (g_isDefined(styles[i]['@attributes']) && g_isDefined(styles[i]['@attributes'].id))
            {
                this.styles[styles[i]['@attributes'].id] = styles[i];
            }
            singleStyle = styles[i];
        }
        return singleStyle;
    },
    /**
        @private
    */
    _computeGroundOverlay : function (node)
    {
        var groundOverlays = g_jQuery.makeArray(node);
        for (var i = 0; i < groundOverlays.length; i += 1)
        {
            this.shapes.push(new g_KmlImage(groundOverlays[i]));
        }
    },
    /**
        @private
    */
    _computePlacemark : function (node)
    {
        var i, j;
        var placemarks = g_jQuery.makeArray(node);
        var style;

        for (i = 0; i < placemarks.length; i += 1)
        {
            if (g_isDefined(placemarks[i].Style))
            {
                style = this._computeStyle(placemarks[i].Style);
            }
            
            if (g_isDefined(placemarks[i].styleUrl))
            {
                style = placemarks[i].styleUrl.substring(1);
                style = this.styles[style];
            }

            if (g_isDefined(placemarks[i].Polygon))
            {
                this.shapes.push(new g_KmlPolygon(placemarks[i].Polygon, style, placemarks[i]));
            }

            if (g_isDefined(placemarks[i].LineString))
            {
                this.shapes.push(new g_KmlLineString(placemarks[i].LineString, style, placemarks[i]));
            }
            
            if (g_isDefined(placemarks[i].MultiGeometry))
            {
                if (g_isDefined(placemarks[i].MultiGeometry.LineString))
                {
                    var lineStrings = g_jQuery.makeArray(placemarks[i].MultiGeometry.LineString);
                    for (j = 0; j < lineStrings.length; j += 1)
                    {
                        this.shapes.push(new g_KmlLineString(lineStrings[j], style, placemarks[i]));
                    }
                }
                
                if (g_isDefined(placemarks[i].MultiGeometry.Polygon))
                {
                    var polygons = g_jQuery.makeArray(placemarks[i].MultiGeometry.Polygon);
                    for (j = 0; j < polygons.length; j += 1)
                    {
                        this.shapes.push(new g_KmlPolygon(polygons[j], style, placemarks[i]));
                    }
                }
            }
        }
    }
});


////
//// ToolPosition.js
//// 

var g_ToolPosition = MappyApi.map.tools.ToolPosition = g_Class(/** @lends Mappy.api.map.tools.ToolPosition.prototype */{
    /**
    Possible values allowed for the anchor property :<br/>
    - "lt"<br/>
    - "rt"<br/>
    - "lb"<br/>
    - "rb"    
    @type String[]
    @static
    */
    POSSIBLE_ANCHORS : ["lt", "rt", "lb", "rb"],
    /**
    Default anchor : "rb"
    @type String
    @static
    */
    DEFAULT_ANCHOR : "rb",
    /**
    Default offset : (0,0)
    @type Mappy.api.types.Point
    @static
    */
    DEFAULT_OFFSET : new g_Point(0, 0),
    /**
        @constructs
        @param {String} anchor Set the corner position.
        @param {Mappy.api.types.Point} offset Set the distance between the tool and the choosen corner in pixel.
    */
    initialize : function (anchor, offset)
    {
        if (g_isDefined(anchor) && g_jQuery.inArray(anchor, this.POSSIBLE_ANCHORS) !== -1)
        {
            this.anchor = anchor;
        }
        else
        {
            this.anchor = this.DEFAULT_ANCHOR;
        }

        if (g_isDefined(offset))
        {
            this.offset = offset;
        }
        else
        {
            this.offset = this.DEFAULT_OFFSET.clone();
        }
    },
    /**
    Clone the object and return a new instance.
    @return {Mappy.api.map.ToolPosition}
    */
    clone : function ()
    {
        return new g_ToolPosition(this.anchor, this.offset.clone());
    }
});


////
//// Tool.js
//// 

var g_Tool = MappyApi.map.tools.Tool = g_Class(/** @lends Mappy.api.map.tools.Tool.prototype */{
    /**
    Is the tool on map?
    @type boolean
    */
    isOnMap : false,
    /**
    Possible values allowed for the direction property :<br/>
    - "vertical"<br/>
    - "horizontal"<br/>
    - "none"<br/>
    @type String[]
    @static
    */
    POSSIBLE_DIRECTIONS : ["vertical", "horizontal", "none"],
    /**
    Default direction the tool is moving when minimap is opened : "horizontal"
    @type String
    @static
    */
    DEFAULT_DIRECTION : "horizontal",
    /**
    Default position of the tool.
    @type Mappy.api.map.tools.ToolPosition
    @static
    */
    DEFAULT_POSITION : new g_ToolPosition(),
    /**
    Div tag containing the html of the tool.
    @type g_jQueryObject
    */
    div : null,
    direction : null,
    position : null,
    /**
        @constructs
        @param {Mappy.api.map.tools.ToolPosition} position Describe the position of the tool.
        @param {String} direction Set the direction that tool is moving when minimap is opened. Must be in POSSIBLE_DIRECTIONS.
    */
    initialize : function (position, direction)
    {
        this.setPosition(position);
        this.setDirection(direction);
    },
    /**
    @private
    */
    added : function (map)
    {
        map.controller.view.div.append(this.div);
		
        this.div.css({
            "position" : "absolute",
            "z-index" : 999
        });
		
		this._map = map;
        this.isOnMap = true;
    },
    /**
    @private
    */
    removed : function ()
    {
        
        if (g_jQuery(this.div)[0].parentNode) 
            g_jQuery(this.div)[0].parentNode.removeChild(g_jQuery(this.div)[0]);
        this.isOnMap = false;
    },
    /**
    @private
    */
    setPosition : function (position)
    {
        if (g_isDefined(position))
        {
            this.position = position;
        }
        else
        {
            this.position = this.DEFAULT_POSITION.clone();
        }
    },
    /**
    @private
    */
    setDirection : function (direction)
    {
        if (g_isDefined(direction) && g_jQuery.inArray(direction, this.POSSIBLE_DIRECTIONS) !== -1)
        {
            this.direction = direction;
        }
        else
        {
            this.direction = this.DEFAULT_DIRECTION;
        }
    },
    /**
    @private
    */
    refreshPosition : function ()
    {
        var shift;
		var miniMap = this._map.miniMap;
        if (g_isDefined(miniMap) && miniMap.position.anchor === this.position.anchor && this.direction !== "none")
        {
            if (this.direction === "horizontal")
            {
                shift = new g_Point(miniMap.position.offset.x, 0);
            }
            else
            {
                shift = new g_Point(0, miniMap.position.offset.y);
            }
        }
        else
        {
            shift = new g_Point(0, 0);
        }

        switch (this.position.anchor)
        {
        case "lt":
            this.div.css({
                left : this.position.offset.x + shift.x,
                top : this.position.offset.y + shift.y
            });
            break;
        case "rt":
            this.div.css({
                right : this.position.offset.x + shift.x,
                top : this.position.offset.y + shift.y
            });
            break;
        case "lb":
            this.div.css({
                left : this.position.offset.x + shift.x,
                bottom : this.position.offset.y + shift.y
            });
            break;
        case "rb":
            this.div.css({
                right : this.position.offset.x + shift.x,
                bottom : this.position.offset.y + shift.y
            });
            break;
        }
    }
});


////
//// Copyrights.js
//// 

var g_Copyrights = g_Class(g_Tool, /** @lends Copyrights.prototype */{
    DEFAULT_DIRECTION : "horizontal",
    TEMPLATE : '<div class="copyright"><a href="javascript:void(0);" class="labels"></a><a href="javascript:void(0);" class="logos"></a></div>',
    PROVIDERS_LOGOS : {
        'InterAtlas' : '../images/img/map/logo-iA-16x26.png',
        'IGN' : '../images/img/map/logo-IGN-16x26.png'
    },
    tabProviders : [],
    tabProvidersLogos : [],
    /**
        @constructs
        @private
    */
    initialize : function ()
    {
        g_Tool.prototype.initialize.call(this);
    },
    added : function (map)
    {
        this.div = g_jQuery(this.TEMPLATE);
        
        this.div.find('a').click(function ()
        {
            window.open('http://corporate.mappy.com/conditions-dutilisation/copyright/', 'Copyright');
        });
        g_Tool.prototype.added.call(this, map);
    },
    remove : function ()
    {
        this.tabProviders = [];
        this.tabProvidersLogos = [];
    },
    setText : function (providers)
    {
        this.tabProvidersLogos = [];
        for (var viewmode in providers)
        {
            for (var i in providers[viewmode])
            {
                if (providers[viewmode].hasOwnProperty(i))
                {
                    var found = false;
                    for (var j = 0; j < this.tabProviders.length; j++) if(this.tabProviders[j] === i)
                    {
                        found = true;
                    }
                    if (i !== "Mappy" && !found)
                    {
                        this.tabProviders.push(i);
                    }
                }
            }
        }

        var providersList = this.tabProviders.join(' - ');
        for (var expr in this.PROVIDERS_LOGOS)
        {
            if (providersList.indexOf(expr) != -1)
            {
                this.tabProvidersLogos.push(
                    '<img src="' + "../images/" + this.PROVIDERS_LOGOS[expr] + '" />'
                );
            }
        }

        if (this.tabProviders.length > 0)
        {
            this.div.find('.labels')
                .css('margin-right', (this.tabProvidersLogos.length * 19) + 'px')
                .html('&copy; Mappy - ' + providersList);
            this.div.find('.logos').html(this.tabProvidersLogos.join(''));
        }
        else
        {
            this.div.find('a').empty();
        }
    }
});


////
//// MiniMap.js
//// 

var g_MiniMap = MappyApi.map.tools.MiniMap = g_Class(g_Tool, /** @lends Mappy.api.map.tools.MiniMap.prototype */{
    ZOOM_DELTA: 3,
    TEMPLATE : '<div class="minimap"><div class="minimap-content"><div style="position:absolute;left:0;top:0;" class="minimap-layer"></div><div class="minimap-rectangle"></div></div><div class="minimap-close"></div></div>',
    TEMPLATE_IE6 : '<div class="minimap minimap-ie6"><div class="minimap-bg-ie6" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src=\' ../images/img/tools/border_minimap.png\');"></div><div class="minimap-content"><div style="position:absolute;left:0;top:0;" class="minimap-layer"></div><div class="minimap-rectangle"></div></div><div class="minimap-close"><div class="minimap-close-ie6" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src=\'../images/img/tools/border_minimap.png\');"></div></div></div>',
    _tiles : [],
    _mapListeners : [],
    shift : null,
    /**
        @constructs
        @augments Mappy.api.map.tools.Tool
        @param {String} anchor minimap position. Possible values : lt | rt | lb | rb (default rb)
    */
    initialize : function (position)
    {
        g_Tool.prototype.initialize.call(this, position);
		this.model = new g_MapModel();
    },
    /**
    @private
    */
    added : function (map)
    {
        if (g_isIE6)
        {
            this.div = g_jQuery(this.TEMPLATE_IE6);
        }
        else
        {
            this.div = g_jQuery(this.TEMPLATE);
        }
        g_Tool.prototype.added.call(this, map);

        /* Init DOM */
		this.model.setSize(120, 120);
        
        var cross = this.div.find('.minimap-close');
        var that = this;
        cross.click(function ()
        {
            map.removeTool(that);
        });

        /* set tool size */
        var globalSize = new g_Size(132, 132);
        this.div.width(globalSize.width);
        this.div.height(globalSize.height);

        /* set rectangle size */
        this._addRectangle();
        
        /* Add map listeners*/
        this._mapListeners.push(map.addListener("resize", g_makeCaller(this._resizeHandler, this)));
        this._mapListeners.push(map.addListener("dragstart", g_makeCaller(this._dragStartHandler, this)));        
        this._mapListeners.push(map.addListener("drag", g_makeCaller(this._dragHandler, this)));
        this._mapListeners.push(map.addListener("changeend", g_makeCaller(this._newDisplayArea, this)));
        
        this.tilelayer = this.div.find('.minimap-layer');

        if (map.isReady)
        {
            this._newDisplayArea();
        }

        this.position.offset = new g_Point(globalSize.width, globalSize.height);
        this.refreshPosition();
    },
    /**
    @private
    */
    refreshPosition : function ()
    {
        var cross = this.div.find('.minimap-close');
        switch (this.position.anchor)
        {
        case "lt":
            this.div.css({
                left: -6,
                top: -6
            });
            cross.css({
                right : 0,
                bottom : 0,
                "background-position": "-43px -43px"
            });
            
            if (g_isIE6)
            {
                cross.find('.minimap-close-ie6').css({
                    left: -43,
                    top: -43
                });
            }
            break;
        case "rt":
            this.div.css({
                right: -6,
                top: -6
            });
            cross.css({
                left : 0,
                bottom : 0,
                "background-position": "-66px -43px"
            });
            if (g_isIE6)
            {
                cross.find('.minimap-close-ie6').css({
                    left: -66,
                    top: -43
                });
            }
            break;
        case "lb":
            this.div.css({
                left: -6,
                bottom: -6
            });
            cross.css({
                right : 0,
                top : 0,
                "background-position": "-43px -66px"
            });
            if (g_isIE6)
            {
                cross.find('.minimap-close-ie6').css({
                    left: -43,
                    top: -66
                });
            }
            break;
        case "rb":
            this.div.css({
                right: -6,
                bottom: -6
            });
            cross.css({
                left : 0,
                top : 0,
                "background-position": "-66px -66px"
            });
            if (g_isIE6)
            {
                cross.find('.minimap-close-ie6').css({
                    left: -66,
                    top: -66
                });
            }
            break;
        }
    },
    /**
    @private
    */
    removed : function ()
    {
        for (var i = 0; i < this._mapListeners.length; i += 1)
        {
            this._map.removeListener(this._mapListeners[i]);
        }
        g_Tool.prototype.removed.call(this);
    },
    /**
    @private
    */
    _newDisplayArea : function ()
    {
        this._rectangle.show();
        
        var coords = this._map.controller.converter.fromPixels(this._map.controller.model.halfWidth, this._map.controller.model.halfHeight);
        
		
		this.model.setState(coords, Math.max(this._map.getZoomLevel() - this.ZOOM_DELTA, this.model.viewModes['default'].minZoomLevel));
		
		this.reset();
        var tiles = this.model.getTiles(0, 0);
		this.setTiles(tiles, this.model.centerX - this.model.halfWidth, this.model.centerY + this.model.halfHeight);
		this.tiles = tiles['default'];
		
        this._rectangle.css(this._rectPosInit);
        
        if (this.model.viewModes['default'].minZoomLevel > this._map.getZoomLevel() - this.ZOOM_DELTA)
        {
            this._rectangle.hide();
        }
    },
    /**
    @private
    */
	setTiles : function (tiles, left, top)
	{
		var dom;
		var tile;
		for (var i = 0; i < tiles['default'].length; i += 1)
		{
			tile = tiles['default'][i];
			if (g_isNotDefined(this.tiles[tile.key]))
			{
				tile.create(left, top);
				tile.append(this.tilelayer);
				this.tiles[tile.key] = tile;
			}
		}
	},
	refreshTiles : function ()
	{	
		var position = this.tilelayer.position();
		var tiles = this.model.getTiles(position.left, position.top);
		this.setTiles(tiles, this.model.centerX - this.model.halfWidth, this.model.centerY + this.model.halfHeight);		
	},
	reset : function ()
	{
		this.clean();
		this.tilelayer.css({
			left : 0,
			top : 0
		});
	},
	clean : function ()
	{
		var tiles = this.tiles;
		for (var key in tiles)
        {
            if (tiles.hasOwnProperty(key))
            {
                tiles[key].remove();
            }
        }
        this.tiles = {};
	},
    /**
    @private
    */
    _resizeHandler : function ()
    {
        this._refreshRectangleSize();
        this._newDisplayArea();
    },
    /**
    @private
    */
    _dragStartHandler : function (event)
    {
        this._dragStartEvent = event;
    },
    /**
    @private
    */
    _dragHandler : function (event)
    {
        this._rectangle.css({
            left : this._rectPosInit.left + (this._dragStartEvent.pageX - event.pageX) / Math.pow(3, this.ZOOM_DELTA),
            top : this._rectPosInit.top + (this._dragStartEvent.pageY - event.pageY) / Math.pow(3, this.ZOOM_DELTA)
        });
    },
    /**
    @private
    */
    _refreshRectangleSize : function ()
    {
        var mapSize = this._map.getSize();
        this._rectSize = new g_Size(
            mapSize.width / Math.pow(3, this.ZOOM_DELTA),
            mapSize.height / Math.pow(3, this.ZOOM_DELTA)
        );

        this._rectangle.width(this._rectSize.width);
        this._rectangle.height(this._rectSize.height);

        this._rectPosInit = {
            left : (this.model.width - this._rectSize.width) / 2,
            top : (this.model.height - this._rectSize.height) / 2
        };

        this._rectangle.css(this._rectPosInit);
    },
    /**
    @private
    */
    _addRectangle : function ()
    {
        this._rectangle = this.div.find('.minimap-rectangle');
        this._rectangle.css("position", "absolute");
        this._rectangle.css('opacity', 0.3);

        this._refreshRectangleSize();

        var _moved;
        var that = this;
        var _startMoving = function ()
        {
            var moving = [0, 0];

            var rectanglePosition = that._rectangle.position();

            if (rectanglePosition.left < - that._rectSize.width)
            {
                moving[0] = 5;
            }
            else if (rectanglePosition.left > that.model.width)
            {
                moving[0] = -5;
            }

            if (rectanglePosition.top < - that._rectSize.height)
            {
                moving[1] = 5;
            }
            else if (rectanglePosition.top > that.model.height)
            {
                moving[1] = -5;
            }

            if (moving[0] || moving[1])
            {
                _moved[0] += moving[0];
                _moved[1] += moving[1];

                var pos = that.tilelayer.position();
                that.tilelayer.css({
                    left : moving[0] + pos.left,
                    top : moving[1] + pos.top
                });
                that.refreshTiles();
            }
        };

        var timer;
        g_draggable(this._rectangle, {
            start: function ()
            {
                _moved = [0, 0];
                timer = setInterval(_startMoving, 10);
            },
            stop: function ()
            {
                window.clearInterval(timer);
                var position = that._rectangle.position();
                var slide = new g_Point(that._rectPosInit.left - position.left + _moved[0], that._rectPosInit.top - position.top + _moved[1]);
                slide.x *= Math.pow(3, that.ZOOM_DELTA);
                slide.y *= Math.pow(3, that.ZOOM_DELTA);
                that._map.slideBy(slide);
            }
        });
    }
});


////
//// Scale.js
//// 

var g_Scale = MappyApi.map.tools.Scale = g_Class(g_Tool, /** @lends Mappy.api.map.tools.Scale.prototype */{
    MAX_WIDTH : 100,
    DEFAULT_DIRECTION : "horizontal",
    TEMPLATE : '<div class="tools-scale" style="height:30px;font-family:Arial,sans-serif;font-size: 11px;"><div style="position:relative;float:left;overflow:hidden;width:7px;height:18px;"><img src="{imagesPath}tools/sprite-scale.png"></img></div><div class="tools-scale-middle" style="position:relative;float:left;overflow:hidden;top:6px;height:7px;"><img style="position: absolute;top:-18px;left:0;height:25px;" src="{imagesPath}tools/sprite-scale.png"></img></div><div style="position:relative;float:left;overflow:hidden;width:7px;height:13px;"><img style="position: absolute;right:0;" src="{imagesPath}tools/sprite-scale.png"></img></div><div class="tools-scale-milesbar" style="position:absolute;overflow:hidden;width:7px;height:8px;top:10px;"><img style="position: absolute;left:-7px;top:-10px;" src="{imagesPath}tools/sprite-scale.png"></img></div><div class="tools-scale-meter" style="position: absolute; left:12px; top:-7px;"></div><div class="tools-scale-miles" style="position: absolute; left:12px; bottom:4px;"></div></div>',
    /**
    @private
    */
    _mapListeners : [],
    /**
        @constructs
        @augments Mappy.api.map.tools.Tool
        @param {Mappy.api.map.tools.ToolPosition} position Describe the position of the tool.
        @param {String} direction Set the direction that tool is moving when minimap is opened. Must be in POSSIBLE_DIRECTIONS.
    */
    initialize : function (position, direction)
    {
        g_Tool.prototype.initialize.call(this, position, direction);
    },
    /**
    @private
    */
    added : function (map)
    {
        var imagesPath = '../images/' + ((g_isIE6) ? 'img_png8/' : 'img/');
        this.div = g_jQuery(g_fillTemplate(this.TEMPLATE, {
            imagesPath : imagesPath
        }));
        
        this.div.width(this.MAX_WIDTH + 14);
        g_Tool.prototype.added.call(this, map);
        
        this._mapListeners.push(map.addListener('changeend', g_makeCaller(this._compute, this)));
        
        if (map.isReady)
        {
            this._compute();
        }
    },
    /**
    @private
    */
    _compute : function ()
    {        
        var displayScaleInMiles, displayScaleInMeters;
        var metersUnitUsed, milesUnitUsed;

		var meterPerPixel = this._map.controller.model.getMeterPerPixel();
		
        var initialDistance = this.MAX_WIDTH * meterPerPixel;

            // Meters
        displayScaleInMeters = this._calcRoundDistance(initialDistance);

        if (displayScaleInMeters > 1000)
        {
            metersUnitUsed = "km";
            this.div.find('.tools-scale-meter').html(displayScaleInMeters / 1000 + " " + metersUnitUsed);
        }
        else
        {
            metersUnitUsed = "m";
            this.div.find('.tools-scale-meter').html(displayScaleInMeters + " " + metersUnitUsed);
        }
        
        var middleWidth = (displayScaleInMeters / meterPerPixel) - 6; // left and right img offset 3 + 3
        var middle = this.div.find('.tools-scale-middle');
        middle.width(middleWidth);
        middle.find(':first-child').width(middleWidth);

        //Miles

        //on convertit les Kms en miles
        var convertInMiles = displayScaleInMeters * 0.6213711 / 1000;
        if (convertInMiles < 1)
        {
            //on convertit en yards
            milesUnitUsed = "yd";
            convertInMiles *= 1760;
        }
        else
        {
            milesUnitUsed = "mi";
        }

        displayScaleInMiles = this._calcRoundDistance(convertInMiles);
        this.div.find('.tools-scale-miles').html(displayScaleInMiles + " " + milesUnitUsed);
        this.div.find('.tools-scale-milesbar').css('left', displayScaleInMiles * (displayScaleInMeters / meterPerPixel)  / convertInMiles - 6); // left and middfle img offset 3 + 3
    },
    /**
    @private
    */
    _calcRoundDistance : function (initDist)
    {
        var scaleArray = [1, 2, 4, 5, 6, 8, 10];
        var factor = 1;
        while ((initDist / factor) > 10)
        {
            factor *= 10;
        }

        for (var i = scaleArray.length - 1; i >= 0 ; i -= 1)
        {
            if ((initDist / factor) >= scaleArray[i])
            {
                return scaleArray[i] * factor;
            }
        }
    },
    /**
    @private
    */
    removed : function ()
    {
        for (var i = 0; i < this._mapListeners.length; i += 1)
        {
            this._map.removeListener(this._mapListeners[i]);
        }
        g_Tool.prototype.removed.call(this);
    }
});


////
//// ToolBar.js
//// 

var g_ToolBar = MappyApi.map.tools.ToolBar = g_Class(g_Tool, g_EventSource, /** @lends Mappy.api.map.tools.ToolBar.prototype */{
    DEFAULT_DIRECTION : "vertical",
    EVENTS : ["movemiddleclick"],
    _mapListeners : [],
    /**
        @constructs
        @augments Mappy.api.map.tools.Tool
        @param {Mappy.api.map.tools.ToolPosition} position Describe the position of the tool.
        @param {String} direction Set the direction that tool is moving when minimap is opened. Must be in POSSIBLE_DIRECTIONS.
        @param {Object} buttonList Object containing elements you want to add to your tool bar with their label and options. Example :<br/>
                {<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; miniMap : {<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; label : "Open miniMap",<br/>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; position : {@link Mappy.api.map.tools.ToolPosition} <br/>
                &nbsp;&nbsp;&nbsp;&nbsp; },<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; move : { label : "Click to move" },<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; zoom : { label : "Zoom in/out" },<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; mouseWheelZoom : { label : "Enable/Disable zoom on mousewheel" },<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; selection : { label: "Rectangle selection" },<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; slider : { label: "Slider" }<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; viewMode : { label: "Mode", lang: "FR"}<br/>
                }<br/>
                Options are optionnal, for example you can add a toolbar with only a zoom like that:<br/>
                {<br/>
                &nbsp;&nbsp;&nbsp;&nbsp; zoom : true<br/>
                }<br/>
    */
    initialize : function (buttonList, position, direction)
    {
        position || (position = new g_ToolPosition("rb", new g_Point(5, 5)));
        g_Tool.prototype.initialize.call(this, position, direction);
        g_EventSource.prototype.initialize.call(this);
        this.buttonList = !g_isEmpty(buttonList) ? buttonList : g_ToolBar.DEFAULT;
    },
    /**
    @private
    */
    added : function (map)
    {
        this.div = g_jQuery('<div class="tools-toolbar" style="position:absolute;z-index:999"></div>');
        var buttonList = this.buttonList;
        g_Tool.prototype.added.call(this, map);

        var isFirst = true;


        if (g_isDefined(buttonList.move) && buttonList.move !== false)
        {
            this._addMove(buttonList.move.label, buttonList.move.labelMiddle);
            isFirst = false;
        }

        if (g_isDefined(buttonList.selection) && buttonList.selection !== false)
        {
            this._addZoomOnSelect(buttonList.selection.label);
            isFirst = false;
        }

        if (g_isDefined(buttonList.mouseWheelZoom) && buttonList.mouseWheelZoom !== false)
        {
            this._addZoomOnWheelSelection(buttonList.mouseWheelZoom.label);
            isFirst = false;
        }

        if (g_isDefined(buttonList.viewMode) && buttonList.viewMode !== false)
        {
            this._addViewModeSelector(isFirst, buttonList.viewMode.label, buttonList.viewMode.lang);
            isFirst = false;
        }

        if (g_isDefined(buttonList.zoom) && buttonList.zoom !== false)
        {
            this._addPlus(isFirst, buttonList.zoom.label);
        }

        if (g_isDefined(buttonList.slider) && buttonList.slider !== false)
        {
            this._addSlider(buttonList.slider.label);
        }

        if (g_isDefined(buttonList.zoom) && buttonList.zoom !== false)
        {
            this._addMinus(!buttonList.miniMap, buttonList.zoom.label);
        }

        if (g_isDefined(buttonList.miniMap) && buttonList.miniMap !== false)
        {
            this._addMiniMap(buttonList.miniMap.position, buttonList.miniMap.label);
            isFirst = false;
        }
    },
    _addToolTip : function (div, label)
    {
        if (g_isDefined(label))
        {
            var tooltip = new g_ToolTip(this._map.controller.view.div, '<div style="margin:3px 6px">' + label + '</div>');
            div.mouseover(function (event)
            {
                tooltip.add(event);
                event.stopPropagation();
            });
            div.mouseout(function ()
            {   tooltip.remove();
            });
        }
    },
    _addMove : function (label, labelMiddle)
    {
        var tpl;
        if (g_isIE6)
        {
            tpl = "<div class=\"tools-move-ie6\"><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
        }
        else
        {
            tpl = '<div class="tools-move"></div>';
        }
        var moveButton = g_jQuery(tpl);

        var that = this;
        moveButton.click(function (event)
        {
            var pos = moveButton.offset();
            var mapSize = that._map.getSize();
            pos = [event.pageX - pos.left - moveButton.width() / 2, event.pageY - pos.top - (moveButton.height() + 2) / 2];

            var slide;
            if (pos[0] < 5 && pos[0] > -5 && pos[1] < 5 && pos[1] > -5)
            {
                that.trigger("movemiddleclick");
                return;
            }
            else if (pos[0] < 5 && pos[0] > -5 && (pos[1] > 5 || pos[1] < -5))
            {
                slide = new g_Point(0, -pos[1] * mapSize.height / (moveButton.height() / 2));
            }
            else if (pos[1] < 5 && pos[1] > -5 && (pos[0] > 5 || pos[0] < -5))
            {
                slide = new g_Point(-pos[0] * mapSize.width / (moveButton.width() / 2), 0);
            }
            else
            {
                slide = new g_Point(-pos[0] * mapSize.width / (moveButton.width() / 2), -pos[1] * mapSize.height / (moveButton.height() / 2));
            }
            that._map.slideBy(slide);
        });

        if (g_isDefined(labelMiddle))
        {
            var middle = g_jQuery('<div style="width:10px;height:10px;position:absolute;z-index:10;left:13px;top:14px;"></div>');
            moveButton.append(middle);
            this._addToolTip(middle, labelMiddle);
        }

        this.div.append(moveButton);
        this._addToolTip(moveButton, label);
    },
    _addMiniMap : function (position, label)
    {
        var tpl;
        if (g_isIE6)
        {
            tpl = "<div class=\"tools-minimap-rounded-ie6\"><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
        }
        else
        {
            tpl = '<div class="tools-minimap-rounded"></div>';
        }
        var miniMapButton = g_jQuery(tpl);

        var that = this;

        var miniMap = new g_MiniMap(position);

        miniMapButton.click(function ()
        {
            if (g_isDefined(that._map.miniMap))
            {
                that._map.removeTool(that._map.miniMap);
            }
            else
            {
                that._map.addTool(miniMap);
            }
        });
        this.div.append(miniMapButton);
        this._addToolTip(miniMapButton, label);
    },
    _addPlus : function (isFirst, label)
    {
        var that = this;
        var tpl;
        if (g_isIE6)
        {
            tpl = "<div><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
        }
        else
        {
            tpl = '<div></div>';
        }
        var plus = g_jQuery(tpl);

        if (isFirst)
        {
            if (g_isIE6)
            {
                plus.addClass('tools-plus-rounded-ie6');
            }
            else
            {
                plus.addClass('tools-plus-rounded');
            }
        }
        else
        {
            if (g_isIE6)
            {
                plus.addClass('tools-plus-ie6');
            }
            else
            {
                plus.addClass('tools-plus');
            }
        }

        plus.click(function ()
        {
            that._map.zoomIn();
        });
        this.div.append(plus);
        this._addToolTip(plus, label);
    },
    _addMinus : function (isLast, label)
    {
        var that = this;
        var tpl;
        if (g_isIE6)
        {
            tpl = "<div><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
        }
        else
        {
            tpl = '<div></div>';
        }
        var minus = g_jQuery(tpl);

        if (isLast)
        {
            if (g_isIE6)
            {
                minus.addClass("tools-minus-rounded-ie6");
            }
            else
            {
                minus.addClass("tools-minus-rounded");
            }
        }
        else
        {
            if (g_isIE6)
            {
                minus.addClass("tools-minus-ie6");
            }
            else
            {
                minus.addClass("tools-minus");
            }
        }

        minus.click(function ()
        {
            that._map.zoomOut();
        });
        this.div.append(minus);
        this._addToolTip(minus, label);
    },
    _addSlider : function (label)
    {
        var that = this;
        var viewMode = this._map.getViewMode();
        var slider;

        var tpl;
        if (g_isIE6)
        {
            tpl = "<div class=\"tools-slider-ie6\"><div class=\"tools-slider-bg-ie6\" style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='../images/img/tools/gdeBarreZoom_centre.png');\"></div></div>";
        }
        else
        {
            tpl = '<div class="tools-slider"></div>';
        }
        var sliderContainer = g_jQuery(tpl);
        this.div.append(sliderContainer);

        var createSlider = function (newViewMode)
        {
            viewMode = newViewMode;
            if (g_isDefined(slider))
            {
                slider.destroy();
            }
            slider = new g_Slider({
                container: sliderContainer,
                min: viewMode.minZoomLevel,
                max: viewMode.maxZoomLevel,
                stop: function (value)
                {
                    that._map.setZoomLevel(value);
                }
            });
        };

        createSlider(viewMode);


        this._mapListeners.push(this._map.addListener("changeend", function ()
        {
            var testViewMode = that._map.getViewMode();
            if (viewMode !== testViewMode)
            {
                createSlider(testViewMode);
            }
            slider.setValue(that._map.getZoomLevel());
        }));

        if (this._map.isReady)
        {
            slider.setValue(this._map.getZoomLevel());
        }

        this._addToolTip(sliderContainer, label);

        this._slider = slider;
    },
    _addZoomOnSelect : function (label)
    {
        var tpl;
        var enableClass;
        var disableClass;
        if (g_isIE6)
        {
            tpl = "<div><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
            enableClass = "tools-selection-selected-ie6";
            disableClass = "tools-selection-ie6";
        }
        else
        {
            tpl = '<div></div>';
            enableClass = "tools-selection-selected";
            disableClass = "tools-selection";
        }
        var button = g_jQuery(tpl);

        var map = this._map;

        if (map.isZoomSelectionEnabled() === false)
        {
            button.addClass(disableClass);
        }
        else
        {
            button.addClass(enableClass);
        }

        this._mapListeners.push(map.addListener("controlchanged", function (name)
        {
            if (name === "selectionZoom")
            {
                if (map.isZoomSelectionEnabled() === false)
                {
                    button.removeClass(enableClass);
                    button.addClass(disableClass);
                }
                else
                {
                    button.removeClass(disableClass);
                    button.addClass(enableClass);
                }
            }
        }));

        button.click(function ()
        {
            if (map.isZoomSelectionEnabled())
            {
                map.disableZoomSelection();
                map.enableDraggable();
            }
            else
            {
                map.enableZoomSelection();
            }
        });
        this.div.append(button);

        this._addToolTip(button, label);
    },
    _addZoomOnWheelSelection : function (label)
    {
        var tpl;
        if (g_isIE6)
        {
            tpl = "<div><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
        }
        else
        {
            tpl = '<div></div>';
        }
        var button = g_jQuery(tpl);
        var that = this;

        var enableClass = (g_isIE6) ? "tools-mousewheelzoom-enable-ie6" : "tools-mousewheelzoom-enable";
        var disableClass = (g_isIE6) ? "tools-mousewheelzoom-disable-ie6" : "tools-mousewheelzoom-disable";

        this._mapListeners.push(this._map.addListener("controlchanged", function (name)
        {
            if (name === "scrollWheelZoom")
            {
                if (that._map.isScrollWheelZoomEnabled() === false)
                {
                    button.removeClass(enableClass);
                    button.addClass(disableClass);
                }
                else
                {
                    button.removeClass(disableClass);
                    button.addClass(enableClass);
                }
            }
        }));

        if (this._map.isScrollWheelZoomEnabled() === true)
        {
            button.addClass(enableClass);
        }
        else
        {
            button.addClass(disableClass);
        }

        button.click(function ()
        {
            if (that._map.isScrollWheelZoomEnabled() === false)
            {
                that._map.enableScrollWheelZoom();
            }
            else
            {
                that._map.disableScrollWheelZoom();
            }
        });

        this.div.append(button);
        this._addToolTip(button, label);
    },
    _addViewModeSelector : function (isFirst, label, lang)
    {
        var map = this._map;
        var tpl;
        if (g_isIE6)
        {
            tpl = "<div><div style=\"filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(enabled=true,sizingMethod=crop,src='/Skins/LaceRestaurant/Img/img/tools/spriteToolbar.png');\"></div></div>";
        }
        else
        {
            tpl = '<div></div>';
        }
        var div = g_jQuery(tpl);

        function setClass()
        {
            var mode = map.getViewMode().name;
            div.removeClass();
            if (isFirst)
            {
                if (g_isIE6)
                {

                    div.addClass('tools-viewmode-' + mode + '-rounded-ie6');
                }
                else
                {
                    div.addClass('tools-viewmode-' + mode + '-rounded');
                }
            }
            else
            {
                if (g_isIE6)
                {
                    div.addClass('tools-viewmode-' + mode + '-ie6');
                }
                else
                {
                    div.addClass('tools-viewmode-' + mode);
                }
            }
        }

        map.addListener('viewmodechanged', function (event)
            {
                setClass();
            }
        );
        setClass();

        var container = g_jQuery('<ul class="tools-viewmode-container"></ul>');
        container.hide();
        
        function switchToViewMode(vmName)
        {
             if (map.getViewMode().name !== vmName)
            {
                map.setViewMode(new g_ViewMode(vmName));
                setClass();
            }
            container.hide();
            event.stopPropagation();
        }

        var name = (lang === 'EN') ? 'Map' : 'Plan';
        var plan = g_jQuery('<li class="tools-viewmode-liste-standard">' + name +'</li>');
        plan.click(function (event){switchToViewMode("standard");});
        container.append(plan);

        var photo = g_jQuery('<li class="tools-viewmode-liste-photo">Photo</li>');
        photo.click(function (event){switchToViewMode("photo");});
        container.append(photo);

        name = (lang === 'EN') ? 'Hybrid' : 'Mixte';
        var hybrid = g_jQuery('<li class="tools-viewmode-liste-hybrid">'+ name +'</li>');
        hybrid.click(function (event){switchToViewMode("hybrid");});
        container.append(hybrid);


        div.append(container);

        div.mouseleave(function ()
        {
            container.hide();
        });

        div.mouseenter(function ()
        {
            container.show();
        });

        this.div.append(div);
        this._addToolTip(div, label);
    },
    /**
    @private
    */
    removed : function ()
    {
        if (g_isDefined(this._slider))
        {
            this._slider.destroy();
        }

        for (var i = 0; i < this._mapListeners.length; i += 1)
        {
            this._map.removeListener(this._mapListeners[i]);
        }

        g_Tool.prototype.removed.call(this);
    }
});

g_ToolBar.DEFAULT = {
    miniMap: true,
    move: true,
    zoom: true,
    mouseWheelZoom: true,
    selection: true,
    slider: true,
    viewMode: true
};


////
//// Action.js
//// 

var g_Action = g_Class(/** @lends Action.prototype */{
    /**
		Name of a place : street name, town, country. Nothing for vehicle change.
		@type String
    */
    name : null,
    /**
		Number of in the street. Nothing for vehicle change.
		@type String
    */
    num : null,
    /**
		Name or number of the next road. Usefull for roundabout or ramp.
		@type String
    */
    nextRoad : null,
    /**
		Label of the action. Translated. (ex : Prendre à droite la rue de Ménilmontant).
		@type String
    */
    label : null,
    /**
		Name of the town of the action.
		@type String
    */
    town : null,
    /**
		Country code of the action.
		@type Number
    */
    countryCode : null,
    /**
		Number of the action (More than 1 action could have the same step attribute)
		@type Number
    */
    step : null,
    /**
		Action type. Possible values : start, end, waypoint (step), continue, turn-left, turn-right, place, ramp-left, ramp-right, ped-to-veh, veh-to-ped, ferry-enter, ferry-exit, ferry-train-enter, ferry-train-exit, train-enter (ex : tunnel sous la manche), train-exit, town-enter, town-exit, town-via, country-enter, country-exit.    
		@type String
    */
    type : null,
    /**
		Number of meter between start point and the action in meter.
		@type Number
    */
    meter : null,
    /**
		Time spent between start point and the action in second.
		@type Number
    */
    sec : null,
    /**
		Internal index of the beginning of the action
		@type Number
    */
    from : null,
    /**
		Internal index of the end of the action
		@type Number
    */
    to : null,
    /**
		Coordinates of the action.
		@type Mappy.api.geo.Coordinates
    */
    coordinates : null,
    /**
		Pois of the action.
		@type RoadPoi[]
    */
    pois : null,
    /**
		Pois of the action.
		@type Shields[]
    */
    shields : null,
    /**
		The route this action belongs to
		@type Route
    */
    route : null,
    /**
        @constructs
        @param {Object} action action from the route service.
    */
    initialize : function (actionData, route, isHighway)
    {
		/*
		//var actionTypes = ["start"
		//				   , "end"
		//				   , "waypoint"
		//				   , "continue"
		//				   , "turn-left"
		//				   , "turn-right"
		//				   , "place"
		//				   , "ramp-left"
		//				   , "ramp-right"
		//				   , "ped-to-veh"
		//				   , "veh-to-ped"
		//				   , "ferry-enter"
		//				   , "ferry-exit"
		//				   , "ferry-train-exit"
		//				   , "train-enter"
		//				   , "train-exit"
		//				   , "town-enter"
		//				   , "town-exit"
		//				   , "town-via"
		//				   , "country-enter"
		//				   , "country-exit"
		//				   , "transport"
		//				   , "ped-to-urban-bike"
		//				   , "urban-bike-to-ped"
		//				   , "transport-wait"
		//				   , "transport-end"
		//				   , "connection"];
		//var modeTypes = ["metro", "bus", "rer", "train", "tram", "shuttle", "boat", "autocar", "transport"];
		*/
		
		/*
		//  <xs:element name="action">
		//    <xs:complexType>
		//      <xs:sequence>
		//        <xs:element ref="poi" minOccurs="0" maxOccurs="unbounded" />
		//      </xs:sequence>
		//      <xs:attribute name="y" use="required" type="xs:decimal">
		//      </xs:attribute>
		//      <xs:attribute name="x" use="required" type="xs:decimal">
		//      </xs:attribute>
		//      <xs:attribute name="traffic-state" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="town" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="sec" use="required" type="xs:int">
		//      </xs:attribute>
		//      <xs:attribute name="polyline-index" use="required" type="xs:int">
		//      </xs:attribute>
		//      <xs:attribute name="num" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="next-road" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="name" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="meter" type="xs:int">
		//      </xs:attribute>
		//      <xs:attribute name="label" use="required" type="xs:string" />
		//      <xs:attribute name="highway-enter" type="xs:boolean" />
		//      <xs:attribute name="highway-exit" type="xs:boolean" />
		//      <xs:attribute name="country" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="free-bikes" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="free-blocks" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="arrDate" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="dataprovider" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="depDate" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="line" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="fare-zone" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="mode" >
		//        <xs:simpleType>
		//          <xs:restriction base="xs:string">
		//            <xs:enumeration value="metro" />
		//            <xs:enumeration value="bus" />
		//            <xs:enumeration value="metro" />
		//            <xs:enumeration value="rer" />
		//            <xs:enumeration value="train" />
		//            <xs:enumeration value="tram" />
		//            <xs:enumeration value="shuttle" />
		//            <xs:enumeration value="boat" />
		//            <xs:enumeration value="autocar" />
		//            <xs:enumeration value="transport" />
		//          </xs:restriction>
		//        </xs:simpleType>
		//      </xs:attribute>
		//      <xs:attribute name="simpleLabel" type="xs:string">
		//      </xs:attribute>
		//      <xs:attribute name="type">
		//        <xs:simpleType>
		//          <xs:restriction base="xs:string">
		//            <xs:enumeration value="start" />
		//            <xs:enumeration value="end" />
		//            <xs:enumeration value="waypoint" />
		//            <xs:enumeration value="continue" />
		//            <xs:enumeration value="turn-left" />
		//            <xs:enumeration value="turn-right" />
		//            <xs:enumeration value="place" />
		//            <xs:enumeration value="ramp-left" />
		//            <xs:enumeration value="ramp-right" />
		//            <xs:enumeration value="ped-to-veh" />
		//            <xs:enumeration value="veh-to-ped" />
		//            <xs:enumeration value="ferry-enter" />
		//            <xs:enumeration value="ferry-exit" />
		//            <xs:enumeration value="ferry-train-exit" />
		//            <xs:enumeration value="train-enter" />
		//            <xs:enumeration value="train-exit" />
		//            <xs:enumeration value="town-enter" />
		//            <xs:enumeration value="town-exit" />
		//            <xs:enumeration value="town-via" />
		//            <xs:enumeration value="country-enter" />
		//            <xs:enumeration value="country-exit" />
		//            <xs:enumeration value="transport" />
		//            <xs:enumeration value="ped-to-urban-bike" />
		//            <xs:enumeration value="urban-bike-to-ped" />
		//            <xs:enumeration value="transport-wait" />
		//            <xs:enumeration value="transport-end" />
		//            <xs:enumeration value="connection" />
		//          </xs:restriction>
		//        </xs:simpleType>
		//      </xs:attribute>
		//      <xs:attribute name="angle" type="xs:int" />
		//      <xs:attribute name="exit-number" type="xs:string" />
		//    </xs:complexType>
		//  </xs:element>
		*/
		
		// Required attributes
        this.coordinates = new g_Coordinates(actionData.x, actionData.y);
		this.sec = parseInt(actionData.sec);
		this.polylineIndex = parseInt(actionData["polyline-index"]);
		this.label = actionData.label;
		this.route = route;
		this.isHighway = isHighway;
		
		// Optional attributes
        this.name = actionData.name;
        this.num = actionData.num;
        this.wpNum = parseInt(actionData["wp-num"] || 0);
        this.nextRoad = actionData["next-road"];
        this.exitNumber = actionData["exit-number"];
        this.town = actionData.town;
        this.country = actionData.country;
        this.countryCode = actionData["country-code"];
		this.type = actionData.type;
        this.meter = parseInt(actionData.meter || 0);
		this.angle = actionData.angle;
        this.highwayEnter = parseInt(actionData["highway-enter"]) === 1;
        this.highwayExit = parseInt(actionData["highway-exit"]) === 1;
		
		// Urban Bike
		if(g_isDefined(actionData["free-bikes"]))
		{
			this.freeBikes = parseInt(actionData["free-bikes"]);
		}
		if(g_isDefined(actionData["free-blocks"]))
		{
			this.freeBlocks = parseInt(actionData["free-blocks"]);
		}
		if(g_isDefined(actionData["station-name"]))
		{
			this.stationName = actionData["station-name"];
		}
		
		// Info-traffic in ITI
		this.trafficState = parseInt(actionData["state"]);
		this.trafficVolatility = parseInt(actionData["volatility"]);
		
		if(this.route.isMultiModal())
		{
			this.initializeRMM(actionData);
		}
		
		/*
		Pois
		*/
		this.pois = new Array();
		this.shields = new Array();
		var poiData = g_getAsArray(actionData.poi);
		var allowedTypes = ['radar', 'brunnel', 'town', 'toll', 'pass', 'shield', 'traffic-event'];
		for(var i in poiData)
		{
			if(poiData[i].type === "shield")
			{
				this.shields.push(new g_Shield(poiData[i], this));
			}
			else if(g_jQuery.inArray(poiData[i].type, allowedTypes) !== -1)
			{
				this.pois.push(new g_RoadPoi(poiData[i], this));
			}
		}
    }
	, initializeRMM : function(actionData)
	{
        /*
        Multimodal
        */
        this.depDate = actionData.depDate;
        this.arrDate = actionData.arrDate;
        this.dataprovider = actionData.dataprovider;
        this.simpleLabel = actionData.simpleLabel;
        this.mode = actionData.mode;
        this.lineName = actionData.line;
		this.trainLine = actionData.trainLine;
        this.depStation = actionData.depStation;
        this.arrStation = actionData.arrStation;
        this.lineDir = actionData.lineDir;
		this.fareZone = actionData["fare-zone"];
		this.dataProvider = actionData.dataprovider;
	}
});


////
//// StylesDictionnary.js
//// 

var g_StylesDictionnary = MappyApi.route.StylesDictionnary = g_Class(/** @lends StylesDictionnary.prototype */{
    _styleTypes : null
    , _styles : null
    , _defaultStyle : null
    , _defaultStyleDef : null
    /**
        @constructs
        @param {Object} summary Data from the route service.
    */
    , initialize : function ()
    {
        this._types = [];
        this._styles = [];
        
        this._defaultStyle = new g_ShapeStyle();
    }
    /**
        Parse and process a color-dictionnary json object from roadbook data
        @param {Object} summary Data from the route service.
    */
    , addToDictionnary : function(colorDictionnaryJson)
    {
        if(g_isNotDefined(colorDictionnaryJson))
        {
            return;
        }
        
        var colors = g_getAsArray(colorDictionnaryJson.color);
        var cStyle;
        var type;
        var value;
        // For each color definition
        for(var j in colors)
        {
            type = colors[j].type;
            
            // Create new type
            if(g_isNotDefined(this._styles[type]))
            {
                this._styles[type] = new Array();
                this._types.push(type);
            }
            
            // Create new value if it's not already defined
            value = colors[j].value;
            if(g_isNotDefined(this._styles[type][value]))
            {
                cStyle = {
                    colorType : "argb"
                    //, lineWidth : 10 use default
                    , strokeStyle : colors[j].color
                    , fillStyle : colors[j].color
                };
                this._styles[type][value] = new g_ShapeStyle(cStyle);
            }
        }
    }
    /**
        @return Array An array of available types of styling a road section (vehicule-mode, traffic, ...)
    */
    , getAvailableStyleTypes : function()
    {
        return this._types;
    }
    /**
        @return Array An array of available types of styling a road section (vehicule-mode, traffic, ...)
    */
    , isStyleTypeAvailable : function(type)
    {
        return g_isDefined(this._styles[type]);
    }
    /**
        @return Array An array of available types of styling a road section (vehicule-mode, traffic, ...)
    */
    , existsShapeStyle : function(type, styleId)
    {
        return g_isDefined(this._styles[type]) && g_isDefined(this._styles[type][styleId]);
    }
    /**
        @returns {Mappy.api.map.shape.ShapeStyle} A Shape style corresponding to a style ID, or if not defined a default shape style.
    */
    , getShapeStyle : function(type, styleId)
    {
        var shapeStyle = this._defaultStyle;
        if(g_isDefined(this._styles[type]) && g_isDefined(this._styles[type][styleId]))
        {
            shapeStyle = this._styles[type][styleId];
        }
        return shapeStyle;
    }
});


////
//// Route.js
//// 

var g_Route = g_Class(/** @lends Route.prototype */{
    /**
        Rank of the route
        @type Number
    */
    idRoute : null
    /**
        Rank of the route
        @type Number
    */
    , rank : null
    /**
        Array of {@link Action} describing the roadbook.
        @type Action[]
    */
    , actions : null
    /**
        Summary of the roadbook.
        @type Summary
    */
    , summary : null
    /**
        Polyline to draw on the map
        @type Mappy.api.map.shape.GPolylineLevels
    */
    , polyline : null
    /**
        @type Array
    */
    , wayPoints : null
    /**
        The roadbook this route belongs to.
        @type Roadbook
    */
    , roadbook : null
    /**
        @constructs
        @param {Object} routeData Data from the route service.
        @param {Object} roadbook The roadbook this route belongs to.
    */
    , initialize : function (routeData, roadbook, _idRoute)
    {
        var waypointActionTypes = ["waypoint", "start", "end"];

        // Values
        this.roadbook = roadbook;
        this.rank = parseInt(routeData.rank);
        this.idRoute = parseInt(_idRoute);

        // Actions
        var _actions = g_getAsArray(routeData.actions.action);
        this.actions = new Array();
        var wayPointActionsPid = new Array();

        // Locations re-order
        newGeoLocs = new Array();
        geocodedLocations = roadbook.geocodedLocations;
        var isHighway = false;
        for(var i in _actions)
        {

            var _a = new g_Action(_actions[i], this, isHighway);
            this.actions.push(_a);

            // Is this action on a highway ?
            if(_actions[i]["highway-enter"])
            {
                isHighway = true;
            }
            if(_actions[i]["highway-exit"])
            {
                isHighway = false;
            }

            if(g_jQuery.inArray(_a.type, waypointActionTypes) !== -1)
            {
                if(g_isDefined(_a.wpNum))
                {
                    var wpNum = _a.wpNum;

                    if(g_isNotDefined(geocodedLocations[wpNum].actions))
                    {
                       geocodedLocations[wpNum].actions = [];
                    }

                    geocodedLocations[wpNum].actions[this.idRoute] = _a;
                    newGeoLocs.push(geocodedLocations[wpNum]);
                }
                if(_a.type === "waypoint")
                {
                    wayPointActionsPid.push(_a.polylineIndex);
                }
            }
        }
        // Start & End don't change.
        newGeoLocs[0] = geocodedLocations[0];
        newGeoLocs[geocodedLocations.length - 1] = geocodedLocations[geocodedLocations.length - 1];

        // Update roadbook geocoded locations.
        roadbook.geocodedLocations = newGeoLocs;
        this.wayPoints = newGeoLocs;

        // Summary
        this.summary = new g_Summary(routeData.summary, this);

        // Polyline
        this.polyline = new g_GPolylineLevels(routeData["polyline-definition"]
                                            , roadbook._styles
                                            , wayPointActionsPid);

        // Retrieving vehicule type information
        for(var a in this.actions)
        {
            var typeId = this.polyline.getStyleId(this.actions[a].polylineIndex, "vehicle-mode");
            if(!this.isMultiModal() && g_isNotDefined(this.actions[a].mode) && typeId !== 0)
            {
                this.actions[a].mode = g_typeVehicle[typeId];
            }
        }
    }
    /**
        Returns the route rank
        @return {Integer} The route rank
     */
    , getShapes : function (zoomLevel, styleType)
    {
        return this.polyline.getShapes(zoomLevel, styleType);
    }
    /**
        Returns whether or not the route is a multi-modal one
        @return {Boolean} TRUE if the route is multi-modal.
     */
    , isMultiModal : function ()
    {
        return (this.roadbook.options.multiModal === true);
    }
    /**
        Returns the total highway length in this route
        @return {int} Total lenght of highway in this route
     */
    , getHighwayLength : function ()
    {
        if (this.summary.lengthOnFreeway)
        {
            return this.summary.lengthOnFreeway;
        }

        // Following code deprecated. Used for rbver = 3 ?
        // see http://jira/browse/ITI-424
        var l = 0;
        kmEnter = -1;
        for(var id in this.actions)
        {
            var action = this.actions[id];
            if(action.highwayEnter === true && kmEnter === -1)
            {
                kmEnter = action.meter;
            }
            if(action.highwayExit === true)
            {
                l += action.meter - kmEnter ;
                kmEnter = -1;
            }
        }
        return l;
    }
    /**
        Format this route to a very basic HTML doc.
        @return {String} An HTML document as a String.
     */
    , toHtmlToPrint : function (cpath)
    {
        return this.toHtml(true, cpath);
    }

    , toHtml : function (printable, cpath)
    {
        /*
            Templates
        */
        var line = '<tr class="{trClass}">\
            <td class="number">{number}</td>\
            <td class="details">'+
                (!printable?
                    '<div class="{panelClass}">':
                    '<img class="panel_print" style="{iconStyle}" src="'+
                                            (cpath||('../images/img/roadbook/print')).replace(/\/\s*$/, '')+'/{panelClass}.png"/>')+'</div></td>\
            <td class="action">{action}</td>\
            <td class="time">{time}</td>\
            <td class="km">{km} km</td>\
        </tr>\
        {details}';
        var lineDetails = '<tr class="details {trClass}">\
            <td></td>\
            <td></td>\
            <td colspan="3" class="shields">\
                <table>\
                    <tr>\
                        <td>{shields}</td>\
                        <td>{poi}</td>\
                    </tr>\
                </table>\
            </td>\
        </tr>';
        var tplPoiDetails = '<tr><td class="km">{meter} km</td><td><div class="panel {poiClass}"></div></td><td>{label}</td></tr>';
        var pois = '<table class="pois">{pois}</table>';

        var actionList = [];
        var i, j;
        for (i = 0; i < this.actions.length; i += 1)
        {
            var action = this.actions[i];
            var detailsIndex = (this.actions[i + 1]) ? this.actions[i + 1].from - 1 : action.to;

            /* Prepare Shields */
            var arrShields = action.shields;
            var shieldsHTML = [];

            for (j = 0; j < arrShields.length; j += 1)
            {
                shieldsHTML.push(arrShields[j].toHtml());
            }

            /* Prepare Pois */
            var arrPOIs = action.pois;
            var POIsHTML = [];
            for (j = 0; j < arrPOIs.length; j += 1) {
                POIsHTML.push(g_fillTemplate(tplPoiDetails, {
                    "meter": arrPOIs[j].meter / 1000,
                    "poiClass": arrPOIs[j].type,
                    "label": arrPOIs[j].label
                }));
            }


            var detailsHTML = "";
            if ((shieldsHTML.length > 0 || POIsHTML.length > 0) && (/^\s*$/.test(shieldsHTML.join('')) && /^\s*$/.test(POIsHTML.join(''))))
            {
                detailsHTML = g_fillTemplate(lineDetails, {
                    "trClass": (i % 2 === 0) ? "out" : "in",
                    "shields": shieldsHTML.join(''),
                    "poi": g_fillTemplate(pois, {
                        "pois": POIsHTML.join('')
                    })
                });
            }

            var instructionHTML, showIcon=true;
            if (action.type === "town-enter" || action.type === "town-exit" || action.type === "town-via")
            {
                instructionHTML = unescape(escape(action.label).replace(
                        new RegExp("(" + escape(action.town) + ")", "gi"),
                        g_getHtmlTownSign(action.town.toUpperCase())
                    ));
                showIcon = false;
            }
            else
            {
                var instruction = (action.name) ? action.name : action.nextRoad;
                instructionHTML = unescape(escape(action.label).replace(
                    new RegExp("(" + escape(instruction) + ")", "gi"),
                    '<span class="bold">' + instruction + '</span>'
                ));
            }

            var panelClass;
            if (action.type !== 'connection') {
                panelClass = 'panel ' + action.type;
            } else {
                panelClass = '';
            }
            actionList.push(g_fillTemplate(line, {
                    "trClass": (i % 2 === 0) ? "out" : "in",
                    "number": i + 1,
                    "panelClass": panelClass,
                    "action": instructionHTML,
                    "iconStyle" : showIcon?"":"visibility:hidden",
                    "details": detailsHTML,
                    "time": g_timeToString(action.sec),
                    "km": action.meter / 1000
                })
            );

            // Reset panel class
            panelClass = '';
        }

        return '<table class="roadbook">' + actionList.join('') + '</table>';
    }
});


////
//// Roadbook.js
//// 

var g_Roadbook = g_Class(
    /**
        This class represent a Roadbook. This one use the service ITI v5 data format.
        A roadbook can have several alternatives routes, and it can be multimodal or not.
        @lends Roadbook.prototype
     */
{
    /**
        Query options
        @private
        @type Array
    */
    options : null
    /**
        @private
        @type Location[]
    */
    , geocodedLocations : null
    /**
        All routes in this roadbook.
        @type Mappy.api.route.Route[]
    */
    , routes : null
    /**
        @private
        @type String
    */
    , postKey : null
    /**
        @private
        Roadbook version
        @type Integer
    */
    , version : null
    /**
        @private
        @type Mappy.api.route.Route
    */
    , defaultRoute : null
    /**
        @private
        @type Mappy.api.route.StyleDictionnary[]
    */
    , _styles : null
    /**
        @constructs
        @param {Object} routesData Data from the route service.
        @param {Location[]} geocodedLocations Locations of route steps.
        @param {Object} options Request options.
    */
    , initialize : function (routesData, geocodedLocations, options)
    {
        if (g_isDefined(options))
        {
            this.options = options;
        }
        
        if (g_isDefined(geocodedLocations))
        {
            this.geocodedLocations = geocodedLocations;
        }
        
        // Postkey (accessor key cache)
        this.postKey = routesData.postkey;
        
        // Route version
        this.version = routesData.version;
        
        
        // Styles
        this._styles = new g_StylesDictionnary();
        this._styles.addToDictionnary(routesData["color-dictionary"]);
        
        // Routes
        this.routes = new Array();
        var _routes = g_getAsArray(routesData.route);
        for(var i in _routes)
        {
            this.routes.push(new g_Route(_routes[i], this, i));
        }
        
        // Default route
        var _defaultRoute = this.routes[0];
        if(this.routes.length > 1)
        {
            for(var i = 1 ; i < this.routes.length ; i++)
            {
                if(_defaultRoute.rank > this.routes[i].rank)
                {
                    _defaultRoute = this._routes[i];
                }
            }
        }
        this.defaultRoute = _defaultRoute;
    }
    /**
    Return the default route for this roadbook
    @return {Route} The route of lower rank
    */
    , getDefaultRoute: function()
    {
        return this.defaultRoute;
    }
    /**
        Return the summaries of all route in this Roadbook.
        @return {Array} An array of Mappy.api.route.Summary
    */
    , getSummaries : function()
    {
        var summmaries = new Array();
        for(var i in this.routes)
        {
            summaries.push(this.routes[i].summary);
        }
        return summaries;
    }
    /**
        List the coloring styles available.
        @return {Array} An array of available types of styling a road section (vehicule-mode, traffic, ...)
    */
    , getAvailableStyleTypes : function()
    {
        return this.styles.getAvailableStyleTypes();
    }
});


////
//// Way.js
//// 

var g_Way = g_Class(/** @lends Way.prototype */{
    /**
    @type String
    */
    category : null,
    /**
    Country name of the way.
    @type String
    */
    country : null,
    /**
    Town name of the way.
    @type String
    */
    town : null,
    /**
    @type String
    */
    type : null,
    /**
    Distance between the start point and the way.
    @type Number
    */
    meter : null,
    /**
    Time in second to go to this way.
    @type Number
    */
    sec : null,
    /**
    Internal index of the beginning of the way.
    @type Number
    */
    from : null,
    /**
    Internal index of the end of the way.
    @type Number
    */
    to : null,
    /**
    @type String
    */
    name : null,
    /**
    @type String
    */
    num : null,
    /**
    Points of interest that you can find on the way. An array of {@link RoadPoi}.
    @type RoadPoi[]
    */
    pois : null,
    /**
    Shields that you can find on the way. An array of {@link Shield}.
    @type Shield[]
    */
    shields : null,
    /**
        @constructs
        @param {Object} data Data from the route service.
    */
    initialize : function (data, category, country, town)
    {
        this.category = category;
        this.country = country;
        this.town = town;
        this.type = data.type; 
        this.meter = data.meter - 0;
        this.sec = data.sec - 0;
        this.from = data.from - 0;
        this.to = data.to - 0;
        this.name = data.name;
        this.num = data.num;
        
        this.pois = [];
        this.shields = [];
    }
});


////
//// RoadPoi.js
//// 

var g_RoadPoi = g_Class(/** @lends RoadPoi.prototype */{
    /**
    @type Number
    */
    type : null,
    /**
    Name of the point of interrest.
    @type String
    */
    name : null,
    /**
    Roadbook indication. Translated.
    @type String
    */
    label : null,
    /**
    Distance between the start point and the way.
    @type Number
    */
    meter : null,
    /**
    Time in second to go to this way.
    @type Number
    */
    sec : null,
    /**
    Length of the brunnel if type is brunnel.
    @type Number
    */
    len : null,
    /**
    Speed limit if type is radar
    @type Number
    */
    speedLimit : null,
    /**
    Town population if type is town.
    @type Number
    */
    pop : null,
    /**
    Cost if type is toll
    @type String
    */
    cost : null,
    /**
    @type Action
    */
    action : null,
    /**
    @type Coordinates
    */
    coordinates : null,
    /**
        For traffic events only
        @type Number
    */
    codeAlert: null,
    /**
        @constructs
        @param {Object} data Data from the route service.
    */
    initialize : function (data, action)
    {
        this.action = data.action;
        this.type = data.type;
        this.name = data.name;
        this.label = data.label || data.comment // Traffic event style;
        this.meter = parseFloat(data.meter || 0);
        this.sec = parseInt(data.sec || 0);
        this.len = parseInt(data.len || 0);
        this.speedLimit = parseInt(data['speed-limit'] || 0);
        this.pop = parseInt(data.pop || 0);
        this.cost = parseInt(data.cost || 0);
        
        if(g_isDefined(data.x) && g_isDefined(data.y))
        {
            this.coordinates = new g_Coordinates(data.x, data.y);
        }
        
        this.codeAlert = parseInt(data["code-alert"] || -1);
        
    }
});


////
//// Shield.js
//// 

var g_Shield = g_Class(/** @lends Shield.prototype */{
    /**
    @type Number
    */
    index : null,
    /**
    Distance between the start point and the way.
    @type Number
    */
    meter : null,
    /**
    Time in second to go to this way.
    @type Number
    */
    sec : null,
    /**
    Shield description line by line.
    @type Object
    */
    lines : null,
    /**
    @type Action
    */
    action : null,
    /**
        @constructs
        @param {Object} data Data from the route service.
    */
    initialize : function (data, action)
    {
        this.type = data.type;
        this.index = parseInt(action.from);
        this.meter = parseInt(data.meter);
        this.sec = parseInt(data.sec);
        this.lines = [];
        // Head. Wait ... It's a line !
        if(g_isDefined(data.head))
        {
            var tmpNum;
            var nums = g_getAsArray(data.head.num);
            var dirs = [];
            for(var i = 0; i < nums.length ; i++)
            {
                tmpNum = nums[i];
                dirs.push({
                    type : "hat"
                    , color : tmpNum.color
                    , "font-color" : tmpNum["font-color"]
                    , value : tmpNum.value
                });
            }
            this.lines.push({dir : dirs});
        }
        // Lines
        var tmpLines = g_getAsArray(data.lines.line);
        for(var i = 0; i < tmpLines.length ; i++)
        {
            this.lines.push(tmpLines[i]);
        }
        
        this.action = action;
    },
    /**
    @return {String} Html string of the shield.
    */
    toHtml : function ()
    {
        var hat = [];
        var highway = this.action.isHighway;

        var exit = false;
        var html = '';
        for (var i = 0; i < this.lines.length; i += 1)
        {
            var line = "";
            var dirs = g_jQuery.makeArray(this.lines[i].dir);
            for (var j = 0; j < dirs.length; j += 1)
            {
                var dir = dirs[j];
                if (typeof dir === "string")
                {
                    line += '<span class="name">' + dir.toUpperCase() + '</span>';
                }
                else if (dir.type === "hat")
                {
                    var cls = (dir.value && dir.value.charAt(0) && dir.value.charAt(0).toLowerCase()) || "";
                    hat.push('<span class="num-' + cls + '">' + dir.value + "</span>");
                }
                else if (dir.type === "num")
                {
                    var cls = (dir.value && dir.value.charAt(0) && dir.value.charAt(0).toLowerCase()) || "";
                    line += '<span class="num-' + cls + '">' + dir.value + "</span>";
                }
                else if (dir.type === "exit")
                {
                    exit = true;
                    line += '<img src="../images/img/roadbook/shield/exit_frame.gif"/><span style="position:relative;left:-25px;font-size:11px;top:-3px;">' + dir.value + '</span>';
                }
                else if (dir.type === "exitname")
                {
                    exit = true;
                    line += '<span class="exitname">' + dir.value + '</span>&nbsp;<img src="../images/img/roadbook/shield/exit_arrow.gif"/>';
                }
                else if (dir.type === "picto")
                {
                    line += '<img src="../images/img/roadbook/shield/picto' + parseInt(dir.value, 10) + '.gif"/>';
                }
                else if (dir.type === "name" ||
                         dir.type === "other" ||
                         dir.type === "streetname")
                {
                    line += '<span class="' + dir.type + '">' + dir.value.toUpperCase() + '</span>';
                }
                // Temporary fix : LBS Bug ?
                else if (dir.type === "text")
                {
                    line += '<span class="name">' + dir.value.toUpperCase() + '</span>';
                }
                // Temporary fix : LBS Bug ?
                else if (dir.type === "street")
                {
                    line += '<span class="streetname">' + dir.value.toUpperCase() + '</span>';
                }

                // Add a space if this is not the last item on the line
                if (j + 1 < dirs.length &&
                    line !== "")
                {
                    line += '&nbsp;';
                }
            }
            if (line !== "")
            {
                html += '<li>' + line + '</li>';
            }
        }
        // Clumsy hack: if the body is empty and the hat is not, swap them
        if (html === "" &&
            hat.length)
        {
            g_jQuery.each(hat, function (index, line)
            {
                html += '<li>' + line + '</li>';
            });
            hat = [];
        }
        return '<div class="shield">' +
               (hat.length ? '<div class="hat">' + hat.join('&nbsp') + '</div>' : '') +
               (html !== "" ? '<ul' + (highway && !exit ? ' class="highway"' : '') + '>' + html + '</ul>' : '') +
               '</div>';
    }
});


////
//// Summary.js
//// 

var g_Summary = g_Class(/** @lends Summary.prototype */{
    /**
    Liters of fuel consumed.
    @type Number
    */
    gasConsumption : null,
    /**
    Name of the fuel.
    @type String
    */
    gasName : null,
    /**
    Total distance.
    @type Number
    */
    length : null,
    /**
    Distance on freeway
    @type Number
    */
    lengthOnFreeway: 0,
    /**
    Total duration.
    @type Number
    */
    time : null,
    /**
    Name of the vehicle used.
    @type String
    */
    vehicle : null,
    /**
    With caravan?
    @type boolean
    */
    caravan : null,
    /**
    Summary of tolls with amount per country.
    @type Object[]
    */
    tolls : null,
    nbCorrespondances : null,
    correspondances : null,
    departDateTime : null,
    fareZone : null,
    arriveeDateTime : null,
    trafficGas : null,
    trafficTime : null,
    _route : null,
    /**
        @constructs
        @param {Object} summaryData Data from the route service.
        @param {Object} route The route which is described by this summary.
    */
    initialize : function (summaryData, route)
    {
        this.initDefaultSummary(summaryData, route);

        if(route.isMultiModal())
        {
            this.initRMMSummary(summaryData, route);
        }

        if (g_isDefined(summaryData["urban-bike"]))
        {
            this.urbanBike = {
                length : parseInt(summaryData["urban-bike"].length, 10),
                time : parseInt(summaryData["urban-bike"].time, 10)
            };
        }

        this._route = route;

    }
    /**
        @private
    */
    , initDefaultSummary : function (summaryData, route)
    {

        this.length = parseInt(summaryData.length, 10);
        this.time = parseInt(summaryData.time, 10);
        this.gasConsumption = parseFloat(summaryData.gas);
        this.name = summaryData.name;
        this.date = summaryData.date;

        var rb = route.roadbook;

        var options = rb.options;

        if (g_isDefined(options))
        {
            if (g_isDefined(options.vehicle))
            {
                if ('bike' === rb.options.vehicle) {
                    this.vehicle = rb.options.vehicle;
                } else {
                    this.vehicle = options.vehicle;
                }
            }

            if (g_isDefined(options.gas))
            {
                this.gasName = options.gas;
                this.gasCost = parseFloat(options.gascost, 10);
            }

            this.caravan = (options.caravan === "1");
        }

        // Traffic
        if(g_isDefined(summaryData.traffic))
        {
            this.trafficGas = parseFloat(summaryData.traffic.gas, 10);
            this.trafficTime = parseInt(summaryData.traffic.time, 10);
        }

        // Freeway length
        if (g_isDefined(summaryData['length-on-freeway']))
        {
            this.lengthOnFreeway = summaryData['length-on-freeway'];
        }

        // Tolls
        var tolls = [];
        if (g_isDefined(summaryData.tolls) && g_isDefined(summaryData.tolls.toll))
        {
            tolls = g_getAsArray(summaryData.tolls.toll);
            for (var i = 0; i < tolls.length; i += 1)
            {
                tolls[i].amount = parseFloat(tolls[i].amount);
            }
        }
        this.tolls = tolls;

    }
    /**
     For multimodal
     @private
     */
    , initRMMSummary : function (summaryData)
    {
        this.nbCorrespondances = parseInt(summaryData.nbCorrespondances, 10);
        this.correspondances = summaryData.correspondances;
        this.departDateTime = summaryData.departDateTime;
        this.arriveeDateTime = summaryData.arrDateTime;
        this.fareZone = summaryData.fareZone;
        this.nextDeparture = summaryData.nextDeparture||null;
        this.previousDeparture = summaryData.previousDeparture||null;

        if ('bike' !== this.vehicle) {
            this.vehicle = "transport";
        } else {
            this.vehicle = "public_bike";
        }

        if (this.date === undefined) {
            this.date = summaryData.arrDateTime;
        }
    }
    /**
		Returns an HTML formatted string to show main caracteristics of this Summary
		@return {String}
    */
    , toHtml : function ()
    {
        // Internal function
        var getVehicleName = function(veh)
        {
            switch (veh)
            {
                case "ped":
                    // Pedestrian
                    return "Pi&eacute;ton";
                case "comcar":
                    // Compact car
                    return "Petite voiture";
                case "midcar":
                    // Midsize car
                    return "Voiture de taille moyenne";
                case "sedcar":
                    // Sedan car
                    return "Routi&egrave;re";
                case "luxcar":
                    // Luxury car
                    return "Grande routi&egrave;re";
                case "lt3.5":
                    // "Truck, PTAC < 3.5T";
                    return "Poids Lourd, PTAC &lt; 3.5T";
                case "lt12":
                    // Truck, PTAC < 12T
                    return "Poids Lourd, PTAC &gt; 12T";
                case "gt12":
                    // Truck, PTAC > 12T
                    return "Poids Lourd, PTAC &gt; 12T";
                case "gt12a":
                    // Truck, PTAC > 12T, articulated
                    return "Poids Lourd, PTAC &gt; 12T, articul&eacute;";
                case "mot":
                    // Motorbike
                    return "Moto";
                case "van":
                    // Van
                    return "Van";
                case "coa":
                    // Coach
                    return "Caravane";
                case "transport":
                    // metro, rer, etc
                    return "Transport en commun";
                case "bike":
                    //bik
                    return "V&eacute;lo";
                case "public_bike":
                    //bik
                    return "V&eacute;lo en libre service";
            }
            return "";
        };

        var isAlternative = this._route.rank > 0;
        var str = '<div class="summary-wrapper"><div class="';
        if(isAlternative)
        {
            str += 'inactive-route';
        }
        else
        {
            str += 'active-route';
        }
        str += ' route-bloc-summary">';

        var roadbookSummaryClass = '';
        if (undefined === this.name) {
            roadbookSummaryClass = ' route-header-public-transport';
        }

        str += '<div class="route-header' + roadbookSummaryClass + '">';

        if(isAlternative)
        {
            // Alternative iti
            str += '<h3>Itin&eacute;raire alternatif</h3>';
        }
        else
        {
            // Main iti
            str += '<h3>R&eacute;sum&eacute; de l\'itin&eacute;raire</h3>';
        }

        if (undefined !== this.name)
        {
            str += '<p class="route-title">En passant par <br /> ' + this.name + '</p>';
        }

        str += '</div>';
        str += '<div class="route-summary">';
        str += '<ul>';
        str += '<li>';

        var dateParts;
        var hm;
        if (-1 === this.date.indexOf(' ')) {
            // Date format "2010-10-15:15:55:00"
            dateParts = this.date.split(':');

            // 15:55:00
            hm = dateParts[1] + "h" + dateParts[2];
        } else {
            // Date format "2010-10-15 15:55:00"
            dateParts = this.date.split(' ');
            // 15:55:00
            var partDateTime = dateParts[1].split(':');
            hm = partDateTime[0] + "h" + partDateTime[1];
        }

        // 2010-10-15
        var partDate = dateParts[0].split('-');
        var date = partDate[2] + "/" + partDate[1] + "/" + partDate[0];

        str += '<strong class="label">Date :</strong><p>' + date + ' &agrave; '+ hm + '</p>';
        str += '</li>';

        var time_delay = this.trafficTime && this.trafficTime > this.time ? this.trafficTime - this.time : undefined;
        var time_total = this.trafficTime ? this.trafficTime : this.time;

        str += '<li>';
        str += '<strong class="label">Dur&eacute;e :</strong>';
        str += '<p><strong>' + g_timeToString(time_total) + '</strong>';
		if(g_isDefined(time_delay))
		{
			str += '<span class="time_delay">(dont ' + g_timeToString(time_delay) + ' de retard selon trafic)</span></p>';
		}
        str += '</li>';

        str += '<li class="marg6tb">';

        if (!isNaN(this.length)) {
            str += '<strong class="label">Distance :</strong>';
            str += '<p><strong>' + g_distanceToString(this.length) + '</strong>';
        }

        if (this.lengthOnFreeway > 0)
        {
            str += ' dont ' + g_distanceToString(this.lengthOnFreeway) + ' sur voies rapides.';
        }
        str += '</p>';
        str += '</li>';

        str += '<li>';
        str += '<strong class="label">V&eacute;hicule :</strong><p><strong>' + getVehicleName(this.vehicle) + '</strong></p>';
        str += '</li>';

        var gas = this.trafficGas ? parseFloat(this.trafficGas, 10) : parseFloat(this.gasConsumption, 10);
        var gascost = gas * this.gasCost;

        if(gascost > 0)
        {
            str += '<li class="gas_label">';
            str += '<strong class="label">Carburant :</strong>';
            str += '<strong>' + gascost.toFixed(2) + " &#8364; (" + Math.round(gas) + "L)"+ '</strong>';
            str += '</li>';
        }

        var tolls, toll;
        if (this.tolls.length > 0)
        {
            var strings = [];
            for (var i = 0; i < this.tolls.length; i += 1)
            {
                toll = this.tolls[i];
                strings.push(toll.country + " : <strong>" + toll.amount + " " + toll.currency + "</strong>");
            }
            tolls = strings.join(", ");
        }
        else
        {
            tolls = "Aucun";
        }
        str += '<li class="tolls_short">';
        str += '<strong class="label">P&eacute;ages :</strong>';
        str += '<p>' + tolls + '</p>';
        str += '</li>';

        if( this.indemnities > 0)
        {
            str += '<li class="indemnities">';
            str += '<strong class="label">Indemnit&eacute;s :</strong>';
            str += '<p><strong>' + this.indemnities + ' &#8364; </strong></p>';
            str += '</li>';
        }

        if(g_isDefined(this.urbanBike))
        {
            var freeBikes;
            var freeBlocks;

            for(var k = 0 ; k < this._route.actions.length  ; k++)
            {
                var action = this._route.actions[k];

                if(g_isDefined(action.freeBikes))
                {
                    freeBikes = action.freeBikes;
                }

                if(g_isDefined(action.freeBlocks))
                {
                    freeBlocks = action.freeBlocks;
                }

                if(freeBikes !== undefined && freeBlocks !== undefined)
                {
                    break;
                }
            }

            str += '<li class="marg6t free_bikes">';
            str += '<strong class="label">V&eacute;los au d&eacute;part : </strong>';
            str += '<p><strong>' + freeBikes + '</strong></p>';
            str += '</li>';

            str += '<li class="free_blocks">';
            str += '<strong class="label">Points d\'attache libres : </strong>';
            str += '<p><strong>' + freeBlocks + '</strong></p>';
            str += '</li>';
        }

        str += '</ul>';
        str += '</div>';
        str += '</div>';
        str += '</div>';

        return str;
    }
});


////
//// RouteService.js
//// 

var g_RouteService = MappyApi.route.RouteService = g_Class(/** @lends Mappy.api.route.RouteService.prototype */{
    /**
     @constructs
     */
    initialize : function ()
    {
    },
    /**
     Submits an asynchronous itinerary request. The request is submited to the server. When itinerary is done, the succes function is called.
     @param {Array} addressList Array of step in your itinerary. A step can be a {@link Mappy.api.geolocation.AddressLocation}, a {string}, a {@link Mappy.api.geo.Coordinates} or a {@link CertifiedLocation}.
     @param {Object} options Object containing options of your itinerary. Options are the same as REST api. Here are options that should be noted :
     - options.transport_mode : "pub_tp" : Public transports, "bike" : Urban bikes (Default : "pub_tp")
     - options.realtime : Boolean. Enable realtime for urbanbike.
     - options.infotraffic : Boolean. Enable info traffic.
     - options.mode : "mul" (multiroute, resquesting several routes), or std.
     - options.forceItiAlt : Boolean. If true, ignore length limits for alternative routes.
     @param {Function} success Success handler function. The first parameter of the success fonction will be a {@link Roadbook}.
     @param {Function} error Error handler function
     */
    loadRoute : function (addressList, options, success, error)
    {
        var tq = new g_TaskQueue();
        var geocodedLocations = [];

        var that = this;
        tq.addListener('empty', function ()
        {
            var rids = [];

            for (var i = 0; i < addressList.length; i += 1)
            {
                if (g_isNotDefined(geocodedLocations[i]) && g_jQuery.isFunction(error))
                {
                    error(new Error('One location not found at step ' + i + '.'));
                    return;
                }
                rids.push(geocodedLocations[i].getRidInfo());
            }
            var opt = that._preProcessOptions(options, geocodedLocations);
            that._sendRequest(rids, opt, success, error, geocodedLocations);
        });
        var geocoder = new g_Geocoder();

        tq.size = addressList.length;
        for (var i = 0; i < addressList.length; i += 1)
        {
            geocoder.geocode(addressList[i], this._geocoderSuccessHandler(geocodedLocations, i, tq), this._geocoderErrorHandler(tq));
        }
    },
    /**
     @private
     */
    _geocoderSuccessHandler : function (geocodedLocations, i, tq)
    {
        return function (data)
        {
            geocodedLocations[i] = data[0];
            tq.removeTask();
        };
    },
    /**
     @private
     */
    _geocoderErrorHandler : function (tq)
    {
        return function ()
        {
            tq.removeTask();
        };
    },
    /**
     @private
     */
    _handleResponse : function (data, options, error, geocodedLocations)
    {
        var roadbook;

        if (data && data.routes)
        {
            if (g_getAsArray(data.routes.route)[0].stats.error !== "none")
            {
                var errorMsg = g_getAsArray(data.routes.route)[0].stats.error;
                throw {
                    message : "Route : " + errorMsg + "; Roadbook : " + errorMsg,
                    errors : {
                        route: errorMsg
                        , roadbook: errorMsg
                    }
                };
            }
            else
            {
                roadbook = new g_Roadbook(data.routes, geocodedLocations, this._getDefaultOptions(options));
            }
        }
        else
        {
            throw {
                message : "Mappy.api.route.RouteService._handleResponse",
                errors : {
                    route: "parse error",
                    roadbook: "parse error"
                }
            };
        }
        return roadbook;
    },
    /**
     @private
     */
    _preProcessOptions : function (options, geocodedLocations)
    {
        // Urban bike
        if(options.transport_mode === "bike" && options.rbver === 5)
        {
            options.multiModal = true;
            // For Urban Bike, we force alternative routes
            options.forceItiAlt = true;
        }

        // Alternatives routes
        if(options.mode === "mul")
        {
            // Default value : 2 itineraries
            options.nbroutes = options.nbroutes || 2;

            if(options.forceItiAlt !== true)
            {
                // Due to performances issues, alternatives routes should be requested only if distance is below 20km length.
                var start = geocodedLocations[0].Placemark.Point.coordinates;
                var end = geocodedLocations[geocodedLocations.length - 1].Placemark.Point.coordinates;
                var startCoord = new g_Coordinates(start[0], start[1]);
                var endCoord = new g_Coordinates(end[0], end[1]);
                var deltaCoord = startCoord.getDistance(endCoord);
                // Distance in km
                var distance = g_floor(Math.sqrt(Math.pow(deltaCoord.dx, 2) + Math.pow(deltaCoord.dy, 2)) / 1000);

                // Rules by mail from Nicolas Korchia
                // Il y a des iti alternatifs sur les :
                // - Iti avec trafic de moins de 100 km
                // - Iti sans trafic de 2 a 10 km

                var limitH = 100;
                var limitL = 0;
                if(options.infotraffic !== 1)
                {
                    limitH = 10;
                    limitL = 2;
                }

                if( distance < limitL || limitH < distance)
                {
                    // Suppress alt. iti. options
                    delete options.mode;
                    delete options.nbroutes;
                }
            }

            // Cleaning options
            if(g_isDefined(options.forceItiAlt))
            {
                delete options.forceItiAlt;
            }
        }

        return options;
    },
    /**
     @private
     */
    _sendRequest : function (rids, options, success, error, geocodedLocations)
    {
        var that = this;

        options.rbver = options.rbver || 5;

        var req;
        if (options.multiModal === true)
        {
            req = new g_RMMRequest();
        }
        else
        {
            req = new g_RouteRequest();
        }

        req.configure(rids, options);

        /**
         * Inner function to dispatch response to right method.
         */
        var _dispatchResponse = function (data)
        {
            var roadbook;
            try
            {
                roadbook = that._handleResponse(data, options, error, geocodedLocations);

                if(g_isNotDefined(roadbook))
                {
                    throw {
                        message : "Mappy.api.route.RouteService._dispatchResponse",
                        errors : {
                            route: "roadbook error",
                            roadbook: "roadbook error"
                        }
                    };
                }
            }
            catch (e)
            {
                if (g_jQuery.isFunction(error))
                {
                    error(e);
                }
                else
                {
                    throw e;
                }
                return;
            }
            success(roadbook);
        };

        g_submit(req, _dispatchResponse, error);

    },
    /**
     @private
     */
    _getDefaultOptions : function (options)
    {
        var o = {
            vehicle : "midcar"
            , gas : "petrol"
            , caravan : 0
            // Not used from here
            //, ver : 5
            //, mode : "std"
            //, bestcost : "time"
            //, src : "xml"
            //, gascost : 1.3
            //, axl : "2ax"
            //, ids : 1
            //, twfrom : 1
            //, twto : 1
            //, twstop : 1
            //, trafficinfo : 0
            //, nosimplif : 0
            //, mask : 0
            //, costgroups : {}
            //, lang : "fre"
            //, summaryline : 0
            //, simplifybrunnel : 0
            //, simplifytownpoi : 0
            //, serialize : 63
        };

        return g_jQuery.extend(true, o, options);
    }
});

}());
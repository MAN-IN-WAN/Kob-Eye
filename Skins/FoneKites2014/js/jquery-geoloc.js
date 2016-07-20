/*! jquery-geoloc - v0.0.0 - 2012-07-08
 * Copyright (c) 2012 Satoshi Ohki; Licensed MIT, GPLv2 */

/*!
 * jquery-geoloc.js - jQuery plugin for geolocation.
 */

;( function($, global, undefined) {'use strict';

		var pluginName = 'geoloc';

		var defaults = {};

		/**
		 * Plugin constructor.
		 * @param {Object} options A plugin options.
		 * @constructor
		 */
		function Plugin(options) {
			this.options = $.extend({}, defaults, options);
			this._defaults = defaults;
			this._name = pluginName;
			this._clearWatchId = null;
			this.init(options);
		}


		Plugin.prototype = {
			/**
			 * Initialize with start watching position.
			 * @param {Object} options HTML5 geolocation watchPosition's properties.
			 */
			init : function(options) {
				this.watchPosition(options);
			},
			/**
			 * Start or restart watching position.
			 * @param {Object} options HTML5 geolocation watchPosition's properties.
			 */
			watchPosition : function(options) {
				this.clearWatch();
				this._clearWatchId = navigator.geolocation.watchPosition(this.onSuccessWatchPosition, this.onErrorWatchPosition, options);
			},
			/**
			 * Clear geolocation watchPosition.
			 */
			clearWatch : function() {
				var id = this._clearWatchId;
				id && navigator.geolocation.clearWatch(id);
				this._clearWatchId = null;
			},
			/**
			 * On success callback with notifying.
			 *    target: navigator.geolocation
			 *    trigger: success
			 * @param {Position} position The container for the geolocation information.
			 */
			onSuccessWatchPosition : function(position) {
				var event = jQuery.Event('success');
				event.position = position;
				// {coords: Coordinates, timestamp: [timestamp]}
				$(navigator.geolocation).trigger(event, position);
			},
			/**
			 * On Error callback with notifying.
			 *    target: navigator.geolocation
			 *    trigger: denied, unavailable, timeout, unknownerror.
			 * @param {PositionError} error The positionError container.
			 */
			onErrorWatchPosition : function(error) {
				this.clearWatchPosition();
				// Clear curernt watchPosition.
				var eventType = null;
				switch (error.code) {
					case error.PERMISION_DENIED:
						eventType = 'denied';
						break;
					case error.POSITION_UNAVAILABLE:
						eventType = 'unavailable';
						break;
					case error.TIMEOUT:
						eventType = 'timeout';
						break;
					default:
						eventType = 'unknownerror';
						break;
				}
				var event = jQuery.Event(eventType);
				event.error = error;
				// {code: [0-3], message: 'An error message'}
				event.errorMessage = error.message;
				$(navigator.geolocation).trigger(event, error);
			},
		};

		/**
		 * Geolocation api with jQuery wrappers.
		 * @see http://dev.w3.org/geo/api/spec-source.html
		 */
		$[pluginName] = {
			/**
			 * HTML5 geolocation getCurrentPosition with jQuery.Deferred.
			 * Example:
			 *    var position = null;
			 *    $.geoloc.getCurrentPositionDeferred()
			 *      .done(function(pos) {
			 *        postion = pos;
			 *      }).fail(function(err) {
			 *        // on error getCurrentPosition.
			 *      });
			 * @param {PositionOptions} options HTML5 Geolocation properties.
			 * @return {jQuery.Deferred} jQuery.Deferred object.
			 */
			getCurrentPositionDeferred : function(options) {
				var deferred = $.Deferred();
				navigator.geolocation.getCurrentPosition(deferred.resolve, deferred.reject, options);
				return deferred.promise();
			},
			/**
			 * HTML geolocation watchPosition with notification events.
			 * Example:
			 *    var geo = null, timeout = 6000;
			 *    $(navigator.geolocation).on('succss', function(ev, pos) {
			 *      // on success watchPosition.
			 *      var coords = pos.coords;
			 *      Map.latitude = coords.latitude;
			 *      Map.longitude = coords.longitude;
			 *      Map.update();
			 *    }).on('denied', function(ev, err) {
			 *      alert('To use this application, ' +
			 *        'You need to enable location-based service.');
			 *    }).on('timeout', function(ev, err) {
			 *      // Recovery watch.
			 *      timeout += 2000;
			 *      geo.watchPosition({timeout: timeout});
			 *    });
			 *    geo = $.geoloc.watchPositionWithNotifying({timeout: timeout);
			 * @param {PositionOptions} options HTML5 Geolocation properties.
			 * @return {Plugin} The Plugin instance.
			 */
			watchPositionWithNotifying : function(options) {
				return new Plugin(options);
			}
		}
	}(jQuery, this));

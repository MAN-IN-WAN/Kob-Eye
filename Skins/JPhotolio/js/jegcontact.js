(function($) {
	$.fn.jegcontact = function( options ) 
	{
		var settings = {
			location			: new Array()
		};
		
		if (options) {
			var options = $.extend(settings, options);	
		} else {
			var options = $.extend(settings);					
		}
		
		
		// public variable
		var root = $(this);
		
		/** touch **/
		var touch 				= false;
		
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
			touch = true;
		}
		
		/** show tool tips when if device is not clicked**/
		if(!touch) {
			$('.contact_location .locdetail').jtooltip({});
		}
		
		
		/** hide / show loader **/
		var hide_loader 	= function() { $('#jeg-loader').stop().hide(); };
		var show_loader 	= function() { $('#jeg-loader').stop().show(); };
		hide_loader();
		
		
		/** init map **/
		var init_map = function(canvas) {
			var infoBubble = new Array();
			var marker = new Array();
			var mapcenter, mapzoom;
			
			if(google == undefined || google.maps.MapTypeId == undefined) {
				return null;				
			}
			
		    var myOptions = {
		      mapTypeId: google.maps.MapTypeId.ROADMAP,
		      disableDefaultUI: true,
		      scrollwheel: false,
		      navigationControl: true,
		      mapTypeControl: false,
		      scaleControl: false,
		      draggable: true,
		      panControl : false,
		      zoomControl: true,  	
		      zoomControlOptions: {
		          style: google.maps.ZoomControlStyle.SMALL ,
		          position: google.maps.ControlPosition.RIGHT_BOTTOM
		      }
		    };
		    var map 	= new google.maps.Map(document.getElementById(canvas), myOptions);
		    var bounds = new google.maps.LatLngBounds();

		    var createBubble = function(json){ 
				var address = "";
				var phone = "";

				if(json.address) {
					address = '<li><i class="icon-map-marker"></i><div class="loc-content">';
					for(var i = 0; i < json.address.length ; i++){
						address += '<div>' + json.address[i] + '<div>';
					}
					address += '</div></li>';
				}

				if(json.phone) {
					phone = '<li><i class="phone-icon"></i><div class="loc-content">';
					for(var i = 0; i < json.phone.length ; i++){
						phone += '<div>' + json.phone[i] + '<div>';
					}
					phone += '</div></li>';
				}
		        
				var infowindow = '<div class="infowindow">' +
					'<div class="infowindow-wrapper">' +			
						'<h2>' + json.title + '</h2>' +
						'<ul>' +
							address + 
							phone +
						'</ul>' +
					'</div>' +
					'<div alt="Close" class="closeme" style="display: block;">' +
						'<div class="icon-remove icon-white"></div>' +
					'</div>' +
				'</div>';
				
				return infowindow;
		    };

			var addMarker = function(pos, maptomark, index) {
				return new google.maps.Marker({
			        position	: pos, 
			        map			: maptomark,
			        zIndex		: 10
				});	
			};

			var AddInfoBubble = function(json, index, latLng) {
				var NewInfoBubble = new InfoBubble({
			          map: map,
			          content: createBubble(json),
			          position: latLng,
			          shadowStyle: 0,
			          padding: 0,
			          backgroundColor: 'rgba(125, 125, 125, 0.3)',
			          borderRadius: 5,
			          arrowSize: 10,
			          borderWidth: 0.5,
			          borderColor: '#fff',
			          disableAutoPan: true,
			          hideCloseButton: true,
			          arrowPosition: 40,
			          backgroundClassName: 'infowindowbg',
			          arrowStyle: 2
				});

				google.maps.event.addListener(marker[index], 'click', function() {
		        	closeAllInfoWindow();		        	
		        	// panto($(".contact_location .locdetail").get(index));
		        	NewInfoBubble.open(map, marker[index]);
		        	NewInfoBubble.panToView();
		        	
					/* attach info bubble **/
					google.maps.event.addListenerOnce(NewInfoBubble, 'domready', function(){	
						$("#contact_canvas .closeme").click(function(){
							closeAllInfoWindow();
				        });				
					});
				});
				
				return NewInfoBubble;
			};

			var closeAllInfoWindow = function(){
				for(var i = 0; i < infoBubble.length ; i++) {
					infoBubble[i].close();
				}
			};	

			var populateMarker = function (json) {
				for(var i = 0; i < json.length; i++) {
					var latLng = new google.maps.LatLng(json[i].x, json[i].y);
					marker[i] = addMarker(latLng, map, 10);
					infoBubble[i] = AddInfoBubble(json[i], i, latLng);
					bounds.extend(latLng);
				}
			};
			
			var centerMap = function () {
				map.setZoom(mapzoom);
				map.panTo(mapcenter);
			};
			
			/** populate marker **/
			populateMarker(options.location);
			
			if(options.location.length > 1) {
				map.fitBounds(bounds);
				mapcenter 	= bounds.getCenter();
				
				var firstloaded = true;
				google.maps.event.addListener(map, 'bounds_changed', function() {
					mapzoom = firstloaded ? map.getZoom() : mapzoom;
					firstloaded = false;
				});
			} else {
				map.setCenter(bounds.getCenter());
				map.setZoom(options.zoomfactor);
				mapcenter 	= bounds.getCenter();
			}
			
			return {
				map 			: map,
				infoBubble		: infoBubble,
				marker			: marker,
				closeBubble		: closeAllInfoWindow,
				centerMap		: centerMap
			};
		};
		
		var gmap 				= init_map("contact_canvas");
		if(gmap !== null) {
			var directionsDisplay 	= new google.maps.DirectionsRenderer();
			var directionsService 	= new google.maps.DirectionsService();
			var currcenter			= null;		
			directionsDisplay.setMap(gmap.map);
		} else {
			notifbox("Google map cannot loaded, if you use addon plus, please turn it off");
		}
		
		var set_center_map = function () {
			if(gmap !== null) {
				if(currcenter !== null) {
					gmap.map.setCenter(currcenter);
				}
				currcenter = gmap.map.getCenter();
			}
		};
		
		$(window).resize(function(){
			if(!scw(mediaquerywidth)) {
				hide_contact_location();
				$('.contactflag').hide();
				$('.contact_form').attr('style','');
			}	
			set_center_map();
					
		});
		
		var show_contact_location = function(){
			/* show contact location */		
			$(".locationflag").hide();
			$('.contact_location').animate({
				"margin-left" : "-10"
			}, 'slow');
		};
		
		var contacthide = function(){
			show_contact_location();
					
			/* hide contact form */
			$('.contact_form').animate({
				"left" : "100%",
				"margin-left" : "0"
			}, 'slow', function(){
				$(".contactflag").show();
			});
		};

		var hide_contact_location = function(){
			/* show contact location */
			$('.contact_location').animate({
				"margin-left" : "-276"
			}, 'slow', function(){
				$(".locationflag").show();
			});
		};
		
		var contactshow = function(){
			hide_contact_location();
			var w = "-" + ( $('.contact_form').width()) / 2 ;
			$(".contactflag").hide();
			$('.contact_form').animate({
				"left" : "50%",
				"margin-left" : w 
			}, 'slow', function(){
			});
		};

		var panto = function(i){
			/* move to this location */			
			var x = $(i).attr('data-x');
			var y = $(i).attr('data-y');
			var idx = $(i).attr('data-index');
			var latlng = new google.maps.LatLng(x,y);
			
			gmap.map.panTo(latlng);

			/* show info bubble */ 
			gmap.closeBubble();
			gmap.infoBubble[idx].open(gmap.map, gmap.marker[idx]);
			
			/* attach info bubble **/
			google.maps.event.addListenerOnce(gmap.infoBubble[idx], 'domready', function(){	
				$("#contact_canvas .closeme").click(function(){
		        	gmap.closeBubble();
		        });				
			});
		};

		var show_direction = function(from_x, from_y, to_x, to_y){			
			var request = {
		        origin: new google.maps.LatLng(from_x,from_y),
		        destination: new google.maps.LatLng(to_x,to_y),
		        travelMode: google.maps.DirectionsTravelMode.DRIVING
		    };

			directionsService.route(request, function(response, status) {
				if (status == google.maps.DirectionsStatus.OK) {
					hide_contact_location();
					directionsDisplay.setDirections(response);
				} else {
					notifbox(options.cantgetdirection, 5000);
				}
				hide_loader();
			});		
		};
		
		/* only execute this when browser support geolocation */
		if (Modernizr.geolocation) {
			
			$(".contact_location .locdetail").bind('click', function(){
				
				if(gmap === null) return;
				
				var from_x; var from_y;
				var to_x = $(this).attr('data-x');
				var to_y = $(this).attr('data-y');

				/** prepare location **/
				show_loader();
				
				if (navigator.geolocation)
				{
					
					navigator.geolocation.getCurrentPosition(function(position){
						from_x = position.coords.latitude;
						from_y = position.coords.longitude;
						
						show_direction(from_x, from_y, to_x, to_y);
					}, function(){
						notifbox(options.cantgetdirection, 5000);
						/*
						from_x = "51.1357431";
						from_y = "4.452029";					
						show_direction(from_x, from_y, to_x, to_y);
						*/
					}, 
					{timeout:10000});
				}
			});
			
		} else {
			notifbox(options.geonotsupport, 5000);
		}
		
		var contact_to_top = function (){
			$("html, body").animate({
				scrollTop	: 0
			});
		};
		
		var contact_to_bottom = function (){
			$("html, body").animate({
				scrollTop	: $(".contact_form").offset().top
			});
		};
		
		var contact_to_center = function() {
			gmap.centerMap();
		};
		
		/** bind event **/
		$(".hideform, .locationflag, .view-map").bind("click", function(){ contacthide(); });
		$(".contactflag").bind("click", function(){ contactshow(); });
		$(".hidelocation").bind("click", function(){ hide_contact_location(); });
		$(".contact_location .locdetail").bind('mouseenter', function(){
			if(gmap === null) {				
				return ;
			}
			panto(this); 
		});
		
		$(".contactotop").bind("click", function(){ contact_to_top(); return false; });
		$(".contacttobottom").bind("click", function(){ contact_to_bottom(); return false; });
		$(".cntactocenter").bind("click", function(){ contact_to_center(); return false; });
		
		/** jpanel **/
		var jpanel = $(".locationlist").jScrollPane().data().jsp;
		
		if(scw(iphonewidth)) {
			$(".contact_form").touchwipe({
				wipeRight: function() {
					contacthide();
					return false;
				},	        		   
				min_move_x: 20,
				min_move_y: 20,
				preventDefaultEvents: true
			});	
		}

		$(".contact_location").touchwipe({	
			wipeLeft: function() {
				contactshow();
				return false;
			},	        		   
			min_move_x: 20,
			min_move_y: 20,
			preventDefaultEvents: true
		});	
		
		/** contact form validation & sender **/
		$("#contactform").validate({
		    errorElement: "span",
			errorPlacement : function(errmsg, element){
				var errspan = $(element).parent().find('.contact_error');
				errspan.html(errmsg);
			}, 
			rules :{
				contact_name: {
		        	required: true,
		        	minlength: 2
			    },
				contact_email: {
					required: true,
					email: true
		        },
				contact_message: {
					required: true,
					minlength: 10
		        }
			},
			messages: {
				contact_name: {
			        required: options.entername,
			        minlength: jQuery.format(options.nameminlength)
			      },
			      contact_email: {
			        required: options.enteremail,
			        email: options.validemail
			      },
			      contact_message: {
			        required: options.entermessage,
			        minlength: jQuery.format(options.messageminlength)
			      }
			},
			submitHandler: function(form) {
				var ajaxdata = {
					name		: $("#contact_name").val(),
					email		: $("#contact_email").val(),
					message		: $("#contact_message").val(),
					action		: 'emailsender'
				};
						
				var disableInput = function(){
					$('input:text, input:password, input:file, select, textarea, input:radio, input:checkbox', form).attr('disabled','disabled');
				};

				var reenableInput = function(){
					$('input:text, input:password, input:file, select, textarea, input:radio, input:checkbox', form).removeAttr('disabled');
				};
				
				var clearInput = function(){
				   	$(form)[0].reset();
				};

				var hideButton = function(){
					$('button', form).hide();
					$('.contact_loader', form).show();
				};

				var showButton = function(){
					$('button', form).show();
					$('.contact_loader', form).hide();
				};

				notifbox(options.sendmessage, 0);
				disableInput();
				hideButton();
				
				/** contact ajax sender **/
				$.ajax({
					url: base_url + contact_url,
					type : "post",
					dataType : "json", 
					data : ajaxdata,
					success: function(data) {
						if( data.status == 1 ) { 
							notifbox(options.messagesent, 5000);
							clearInput();
						} else {
							notifbox(options.failsentmessage, 5000);								
						}
						reenableInput();
						showButton();
						return false;
					},
					timeout : function() {
						notifbox(options.failsentmessage, 5000);	
						reenableInput();
						showButton();
						return false;
					},
					error : function() {
						notifbox(options.failsentmessage, 5000);	
						reenableInput();
						showButton();
						return false;
					}
				});
				
				return false;
			}
		});
	};
})(jQuery);
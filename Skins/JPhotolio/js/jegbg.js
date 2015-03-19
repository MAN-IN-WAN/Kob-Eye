
/** Jeg bg **/

(function($) {
	$.fn.jegbg = function( options , startCallback, endCallback) 
	{
		// global variable
		var elementlist		= Array();
		var curIdx			= -1;
		var timerTimeout	= 0;
		var timer			= 0;			
		var paused			= false;
		var container		= $(this);
		var instance		= Math.random();
		var ytplayer		= null;
		var touch			= false;
		var singleMode		= false;
		
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
			touch = true;
		}
		
		// lock any button table background image still in load
		var locked			= true;
		VIDEO				= 'video';
		IMAGE				= 'image';
		NEXT				= 'next';
		PREV				= 'prev';
		
		// setting & extends options
		var settings = {
			autoplay 					: 1,
			delay						: 6000,
			random						: false,
			fade_speed					: 1500,
			loader_fade					: 1000,
			partial_load				: true,
			autostart					: true,
			content						: null
		};

		startCallback = startCallback || $.noop;
		endCallback = endCallback || $.noop;
		
		if (options) {
			var options = $.extend(settings, options);	
		} else {
			var options = $.extend(settings);					
		}
		
		
		/** create youtube tag  (youtube script move to header)
		{ 
			var tag = document.createElement('script');
		    tag.src = "http://www.youtube.com/player_api";
		    var firstScriptTag = document.getElementsByTagName('script')[0];
		    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);
		}  
		end youtube tag **/
		
		var build_container = function() { $(container).prepend('<div id="bgcontainer"></div>').prepend('<div class="lio-loader"></div>'); };
		var hide_loader 	= function() { 
			$('.lio-loader').stop().fadeOut(options.loader_fade, function(){
				$(this).css({
					height		: '',
					margin		: '',
					opacity		: '',
					overflow 	: '',
					padding 	: '',
					width		: '',
					display		: 'none'
				}); 
			});
		};
		var show_loader 	= function() { 
			$('.lio-loader').stop().show(options.loader_fade); 
		};
		var apply_lock		= function() { locked = true; };
		var release_lock	= function() { 
			locked = false;
			load_prev_next();
		};
		var is_locked		= function() { return locked; };
		
		var get_image_size = function (img)
		{
			var nh, nw, nt, nl;
			var h = $(img).height();
			var w = $(img).width();
			
			if(h == 0) {
				h = img.height;
				w = img.width;
			}
			
			var r = ( h / w ).toFixed(2);
			var wh = $(container).height();
			var ww = $(container).width();
			var wr = wh/ww.toFixed(2);

			var resizeWidth = function() 
			{				
				nw = ww;
				nh = ww * r;
				nl = ( ww - nw )  / 2;
				nt = ( wh - nh )  / 2;
								
				return new Array(nh, nw, nl, nt);
			};

			var resizeHeight = function() 
			{					
				nw = wh / r;
				nh = wh;
				nl = ( ww - nw )  / 2;
				nt = ( wh - nh )  / 2;
				return new Array(nh, nw, nl, nt);
			};
			
			if(wr > r) {
				return (0 && r <= 1 )  ? resizeWidth() : resizeHeight();
			} else {
				return (0 && r > 1 )  ? resizeHeight() : resizeWidth();
			}
		};
		
		var hide_prev_media_loader = function()
		{
			hide_media(); 
			hide_loader();
		};

		var append_media = function(media)
		{			
			startCallback.call(this, elementlist[curIdx], media);
			$("#bgcontainer").append(media);
						
			$(media).fadeIn(options.fade_speed, function(){				
				$(this).addClass("curmedia");
			});
		};

		var display_image = function(idx)
		{
			// locked any attempt to next / prev slide
			apply_lock();
			var img = new Image();
			
			$(img).load(function(){
				var image = new Image();								
				image.src = elementlist[idx].source;

				image = set_image_size(image, true, elementlist[idx].pos);
				hide_prev_media_loader();
				append_media(image);
				
				// relase any lock
				release_lock();
				
				if(!singleMode) {
					clearTimeout(timerTimeout);
					timerTimeout = setTimeout(function(){
						start_show();
					}, options.delay);
				}
			}).error(function(){
				start_show();
			}).attr('src', elementlist[idx].source);
		};

		var hide_media = function()
		{
			if($('#bgcontainer .curmedia').length > 0) 
			{
				if(is_curently_youtube()) {
					youtube_ended();
				} else {
					$('#bgcontainer .curmedia').stop().fadeOut(options.fade_speed, function(){
						$(this).remove();
					});
				}
			}
		};
		
		var check_video_tag_support = function(type)
		{
			if(type == "mp4" || type == "webm" || type == "ogg") {
				if(Modernizr.video){
					if(Modernizr.video["h264"] && type == "mp4") {
						return "video/mp4";
					} else if(Modernizr.video["webm"] && type == "webm") {
						return "video/webm";
					} else if(Modernizr.video["ogg"] && type == "ogg") {
						return "video/ogg";
					} else {
						return false;
					}
				} else {
					return false;
				}
			} else {
				return false;
			}
		};
		
		var get_video_type = function (type) {
			if(type == "mp4") {
				return "video/mp4";
			} else if(type == "webm") {
				return "video/webm";
			} else if(type == "ogg") {
				return "video/ogg";
			} else {
				return false;
			}
		};
		
		var html5_video = function(source)
		{
			var video = "<video id=\"html5video\" style=\"display : none;\" width=\"" + $(container).width() + "\" height=\"" + $(container).height() + "\" autoplay=1></video>";
			var videosource = "";
			for(var i = 0 ; i < source.length ; i++)
			{
				var videotype = get_video_type(source[i].videotype);
				videosource = videosource + "<source src=\"" + source[i].src + "\" type=\"" + videotype + "\" />";								
			}
			return $(video).append(videosource);
		};
		
		var is_html5_video = function()
		{
			if($("#bgcontainer video").length > 0){
				return true;
			} else {
				return false;
			}
		};
		
		var youtube_parser = function (url)
		{
		    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/;
		    var match = url.match(regExp);
		    
		    if ( match && match[7].length == 11 ) {
		        return match[7];
		    } else {
		        alert("Url Incorrect");
		    }
		};
		
		var updateytplayerInfo = function(e)
		{
			if (e.data == YT.PlayerState.ENDED ) {
		    	start_show();
		    	youtube_ended();
	        }
		};
		
		var stop_youtube = function()
		{
			// stop video
			if(typeof player.stopVideo == 'function') {
				player.stopVideo();	
			}
		};
		
		var pause_youtube = function()
		{
			// pause video
			if(typeof player.pauseVideo == 'function') {
				player.pauseVideo();	
			}
		};
		
		var play_youtube = function()
		{
			// play video
			if(typeof player.playVideo == 'function') {
				player.playVideo();	
			}
		};
		
		var youtube_ended = function() 
		{
			// need to handle it separately again :(				
			stop_youtube();
			
			setTimeout(function(){
				$("iframe#ytmedia").remove();
				player = null;
			}, options.loader_fade);
			
		};
		
		var youtube_player_ready = function () 
		{
			// we cannot call any of default method, so we need to call it here, its ugly hack		    
		    startCallback.call(this, elementlist[curIdx], $("#curmedia"));
		    // force to start youtube player
		    if(touch) {
				play_youtube();
			}
	    };
		

		var is_curently_youtube = function()
		{
			if($("#ytmedia").length > 0){
				if($("#ytmedia").hasClass("curmedia")) {
					return true;
				} else {
					return false;
				}
			} else {
				return false;
			}
		};
	    
		var youtube_video = function (source)
		{
			var youtubesrc;
			for(var i = 0 ; i < source.length ; i++)
			{
				var type = source[i].videotype; 
				if(type == 'youtube') {
					youtubesrc = source[i].src; 
					break;
				}
			}
			
			// create youtube api tag
			$("#bgcontainer").append("<div id=\"ytmedia\"></div>");			
			var youtubeid = youtube_parser(youtubesrc);					
		    var playervar = null;
		    
		    if(touch) {
		    	playervar = {
		            	'autoplay'	: 0,
		            	'controls'	: 1,
		            	'showinfo'	: 0,
		            	'rel'		: 0,
		            	'wmode'		: 'opaque'
					};
		    } else {
		    	playervar = {
		            	'autoplay'	: 1,
		            	'controls'	: 0,
		            	'showinfo'	: 0,
		            	'rel'		: 0,
		            	'wmode'		: 'opaque'
					};
		    }
		    	
		    var loadYoutube = function() {    	
		    	player = new YT.Player('ytmedia', {
					height		: $(container).height(),
					width		: $(container).width(),
					videoId		: youtubeid,
					playerVars : playervar,
					events: {
					      'onReady'			: youtube_player_ready,
					      'onStateChange'	: updateytplayerInfo
					}
		        });
				
				$("div#ytmedia").remove();
		    };
		    
		    var checkYoutubeLoaded = function() {
		    	setTimeout(function(){
			    	if(typeof YT === 'undefined') {
			    		checkYoutubeLoaded();
				    } else {
				    	loadYoutube();
				    }
			    }, 1000);
		    };
		    
		    checkYoutubeLoaded();		    
		};
		
		var flashplayer_video = function(source)
		{
			
		};
		
		var create_video_tag = function(source, priority)
		{			
			if(priority[0] == true) {
				return {
					tagbehaviour 	: 0,
					videotag		: html5_video(source)
				};
			} else if(priority[1] == true) {
				return {
					tagbehaviour 	: 1,
					videotag		: youtube_video(source)
				};
			} else if(priority[3] == true){
				return {
					tagbehaviour	: 3, 
					videotag		: flashplayer_video(source)
				};
			}
		};
		
	
		// priority : video tag, youtube, vimeo own player		
		var video_tag = function(source)
		{
			var priority = new Array();
			
			for(var i = 0; i < 4; i++) priority[i] = false;			
			
			// build priority nya dulu
			for(var i = 0 ; i < source.length ; i++)
			{
				var type = source[i].videotype;
				var videotag = check_video_tag_support(source[i].videotype);
				
				if( ( type == "mp4" || type == "webm" || type == "ogg" ) && videotag) {
					if(videotag) priority[0] = true;
				} else if(type == "youtube") {
					priority[1] = true;
				} else if(type == "vimeo") {
					priority[2] = true;
				} else { // fallback
					if(type == "mp4" || type == "flv") {
						priority[3] = true;
					}
				}
			}
			
			return create_video_tag(source, priority);
		};
		
		var display_video = function(idx)
		{
			var videoobj 	= video_tag($(elementlist[idx].source));
			var video 		= videoobj.videotag;
			var priority 	= videoobj.tagbehaviour;
			
			hide_prev_media_loader();
			
			if(priority == 0) {
				append_media(video);
				$(video).bind("ended", function() {
					start_show();
				});
			} else if(priority == 1){
				$("#ytmedia").addClass("curmedia");
			} else if(priority == 3){
				$("#ytmedia").addClass("curmedia");
			}
			
			// relase any lock						
			release_lock();	
		};
		
		var set_image_size = function(image, hide, pos)
		{
			var size = get_image_size(image);
			$(image).css('height', size[0])
				.css('width',size[1])
				.css('left', size[2])
				.css('max-width', '200%');			
			
			if(hide) {
				$(image).css({'display' : 'none'});
			}
			
			if(pos == 'top') {
				$(image).css('top', 0);
			} else if(pos == 'bottom'){
				$(image).css('bottom', 0);
			} else if(pos == 'center'){
				$(image).css('top', size[3]);
			}
			
			$(image).addClass('nextmedia');
			
			return image;
		};

		var resize_image = function()
		{
			set_image_size($('#bgcontainer .curmedia'), false, elementlist[curIdx].pos);					
		};
		
		var resize_video = function()
		{
			$('#bgcontainer .curmedia').width($(container).width()).height($(container).height());
		};
		
		var resize_window_bg = function()
		{
			if(elementlist[curIdx] !== undefined) {
				if(elementlist[curIdx].type == IMAGE) {
					resize_image();
				} else if(elementlist[curIdx].type == VIDEO) {
					resize_video();
				}
			}
		};
		
		var increase_idx = function()
		{
			if(options.random == false) {
				curIdx = ( curIdx + 1 ) % ( elementlist.length );
			} else {
				var oldIdx = curIdx;
				curIdx = Math.floor(Math.random() * ( elementlist.length ));	
				if(oldIdx == curIdx && elementlist.length > 1) {						
					increase_idx();
				}
			}
		};
		
		var reduce_idx = function()
		{
			curIdx = ( curIdx - 1 );
			if(curIdx < 0) {
				curIdx = elementlist.length - 1;
			}
		};		
		
		var stop_show		= function() 
		{
			// todo : check if current video is youtube, than stop those player
			if(is_curently_youtube()) {
				youtube_ended();
			}
			clearTimeout(timerTimeout); 
		};
		
		var pause_show		= function ()
		{
			if(is_curently_youtube()) {				
				pause_youtube();
			} else if(is_html5_video()) {
				player = document.getElementById("html5video");
				player.pause();
			}
			clearTimeout(timerTimeout);
			
			return true;
		};
		
		var replay_show		= function()
		{
			paused = false;			
			if(is_curently_youtube()) {
				play_youtube();
			} else if(is_html5_video()) {
				player = document.getElementById("html5video");
				player.play();				
			} else {
				clearTimeout(timerTimeout);
				timerTimeout = setTimeout(function(){
					start_show();
				}, options.delay);
			}
			return false;
		};
		
		var start_show 		= function(direction) {			
			show_loader();
			var oldIdx = curIdx;
			paused = false;						
			
			if(typeof direction == 'undefined') {
				increase_idx();
			} else if(direction == NEXT) {
				increase_idx();
			} else if(direction == PREV) {
				reduce_idx();
			}
			
			if(curIdx >= 0) endCallback.call(this, elementlist[oldIdx], elementlist[curIdx]);
									
			if(elementlist[curIdx].type == IMAGE) {
				// unmute player when normal begin  
				unmuteplayer();
				// display image
				display_image(curIdx);
			} else if(elementlist[curIdx].type == VIDEO) {
				muteplayer();
				setTimeout(function(){
					display_video(curIdx);
				}, 3000);				
			}			
		};
		
		var load_prev_next = function() {
			
			var nextid = curIdx + 1;
			if(nextid <= elementlist.length - 1) {
				if(elementlist[nextid].type == IMAGE) {
					var img = new Image();
					$(img).attr('src', elementlist[nextid].source);
				}
			}
			
			var previd = curIdx - 1;
			if(previd >= 0) {
				if(elementlist[previd].type == IMAGE) {
					var img = new Image();
					$(img).attr('src', elementlist[previd].source);
				}
			}
			
			
		};
		
		// recursive function for loading image
		var load_all = function(i)
		{
			if(i <= elementlist.length - 1) {
				if(elementlist[i].type == IMAGE) {
					var img = new Image();
					$(img).load(function(){
						load_all(++i);
					}).attr('src', elementlist[i].source);
				}
			} else {
				start_show(); 
			}
		};
		
		var control_bg = function (direction)
		{
			if(!is_locked()) {
				stop_show(); // clear interval first
				start_show(direction);
			}
		};
		
		var hit_pause = function()
		{			
			paused = (paused == false) ? pause_show() : replay_show();
  			return false;
		};
		
		var set_start_callback = function(start_callback)
		{
			startCallback = start_callback;
		};
		
		var set_end_callback = function(end_callback)
		{
			endCallback = end_callback;
		};
		
		// initialize
		var init = function() {
			var eleIdx = 0;
			build_container();
			elementlist = options.content;
			
			if(elementlist.length == 1) {
				singleMode = true;
				$(".navleft", container).hide();
				$(".navright", container).hide();
			}
			
			if(options.partial_load) {
				start_show();
				
				$(window).resize(function(){
					resize_window_bg();
				});	
			} else {
				$(window).load( function() {
					load_all(0);
				});
			}
			
			$(window).resize(function(){
				resize_window_bg();
			});
			
			// kalo ini ya global
			$(window).stop().keydown(function(e) {
				if(e.keyCode == 37) { // left		
					if(!singleMode) {
						control_bg(PREV);
						return false;
					}
				}
				else if(e.keyCode == 39) { // right
					if(!singleMode) {
						control_bg(NEXT);
						return false;
					}
				}  
				else if(e.keyCode == 32) { // right
					hit_pause();
				}
			});
						
			if(!singleMode) {
				$(".navleft" , container).click(function(){
					control_bg(PREV);
				});
				
				$(".navright" , container).click(function(){
					control_bg(NEXT);
				});
			}
			
			if(!singleMode) {
				$("#bgcontainer" , container).touchwipe({
					wipeLeft: function(e) {					
						control_bg(NEXT);
		    			return false;
					},
					wipeRight: function() {
						control_bg(PREV);					
		    			return false;
					},
					min_move_x: 20,
					min_move_y: 20,
					preventDefaultEvents: true
				});	
			}
			
			// listen to destroyed element
			$(window).bind("jmainremove", function(){
				stop_show();
				$(window).unbind('keydown');
				$(window).unbind('resize');
				delete $.fn.jegbg;
			});
			
		};
		
		var next = function(){
			control_bg(PREV);
		};
		
		var prev = function() {
			control_bg(NEXT);
		};
		
		if(options.autostart) {
			init();
		}
		
		return {
			next				: next,
			prev				: prev,
			start				: init,
			pause				: hit_pause,
			restart				: replay_show,
			set_start_callback	: set_start_callback,
			set_end_callback	: set_end_callback
		};
	};
})(jQuery);
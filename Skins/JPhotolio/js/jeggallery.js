/* simple fragment of jeglio, uses only for load gallery */
(function($) {
	$.fn.jeggallery = function( options ) 
	{
		var settings = {};

		if (options) {
			var options = $.extend(settings, options);	
		} else {
			var options = $.extend(settings);					
		}
		
		/** public **/
		var touch					= false;
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
			touch = true;
		}
		
		var type_image = function(ele){
			/** add tooltips for tips **/
			if(!touch) {
				$('a.item-gallery-image', ele).jtooltip({});
			}
			
			/** photo swipe **/			
			(function(PhotoSwipe){
				photoswipe = $('a.item-gallery-image',ele).photoSwipe({
					backButtonHideEnabled 			: false,
					captionAndToolbarAutoHideDelay 	: 0,
					allowUserZoom 					: true,
					getImageSource					: function(obj){ return $(obj).attr('data'); }
				});
				photoswipe.addEventHandler(PhotoSwipe.EventTypes.onHide, function(e){
					$container.isotope("reLayout");
				});
	        }(window.Code.PhotoSwipe));			
			
			$('a', ele).click(function(){
				return false;
			});
		};
		
		var type_gallery = function(ele){
			$(ele).flexslider({
				animation: "slide",              //String: Select your animation type, "fade" or "slide"
				slideDirection: "horizontal",   //String: Select the sliding direction, "horizontal" or "vertical"
				slideshow: true,                //Boolean: Animate slider automatically
				slideshowSpeed: 7000,           //Integer: Set the speed of the slideshow cycling, in milliseconds
				animationDuration: 300,         //Integer: Set the speed of animations, in milliseconds
				directionNav: false,             //Boolean: Create navigation for previous/next navigation? (true/false)
				controlNav: true,               //Boolean: Create navigation for paging control of each clide? Note: Leave true for manualControls usage
				keyboardNav: true,              //Boolean: Allow slider navigating via keyboard left/right keys
				mousewheel: false,              //Boolean: Allow slider navigating via mousewheel
				prevText: "Previous",           //String: Set the text for the "previous" directionNav item
				nextText: "Next",               //String: Set the text for the "next" directionNav item
				pausePlay: false,               //Boolean: Create pause/play dynamic element
				pauseText: 'Pause',             //String: Set the text for the "pause" pausePlay item
				playText: 'Play',               //String: Set the text for the "play" pausePlay item
				randomize: false,               //Boolean: Randomize slide order
				slideToStart: 0,                //Integer: The slide that the slider should start on. Array notation (0 = first slide)
				animationLoop: true,            //Boolean: Should the animation loop? If false, directionNav will received "disable" classes at either end
				pauseOnAction: true,            //Boolean: Pause the slideshow when interacting with control elements, highly recommended.
				pauseOnHover: false,            //Boolean: Pause the slideshow when hovering over slider, then resume when no longer hovering
				controlsContainer: "",          //Selector: Declare which container the navigation elements should be appended too. Default container is the flexSlider element. Example use would be ".flexslider-container", "#container", etc. If the given element is not found, the default action will be taken.
				manualControls: "",             //Selector: Declare custom control navigation. Example would be ".flex-control-nav li" or "#tabs-nav li img", etc. The number of elements in your controlNav should match the number of slides/tabs.
				start: function(){},            //Callback: function(slider) - Fires when the slider loads the first slide
				before: function(){},           //Callback: function(slider) - Fires asynchronously with each slider animation
				after: function(){},            //Callback: function(slider) - Fires after each slider animation completes
				end: function(){}               //Callback: function(slider) - Fires when the slider reaches the last slide (asynchronous)
			});
			
			/** add tooltips for tips **/
			type_image(ele);
		};
		
		var type_audio = function(ele){
			var audioresize = function () {
				var w = parseInt($(ele).css('width'),10);
				var h = 30;
				$('audio', ele).attr('width', '100%').attr('height', h);
			};			
			
			$(window).resize(function(){audioresize();});			
			$(window).resize();
			
			$('audio', ele).mediaelementplayer({
				pluginPath: template_css + "mediaelement/"
			});
		};
		
		var type_video = function(ele){
			/** set size of video & image **/
			$('video', ele).css({'width' : '100%','height': '100%'});
			$('img', ele).css({'width': '100%','height': '100%'});			
			$('video', ele).mediaelementplayer({});
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
		
		var vimeo_parser = function (url) 
		{
			var regExp = /http:\/\/(www\.)?vimeo.com\/(\d+)($|\/)/;
			var match = url.match(regExp);

			if (match){
			    return match[2];
			}else{
			    alert("not a vimeo url");
			}

		};
		
		var type_video_youtube = function(ele){
			var youtube_id = youtube_parser($('.video-container', ele).attr('data-src'));
			var iframe = '<iframe width="700" height="500" src="http://www.youtube.com/embed/' + youtube_id +  '?showinfo=0&theme=light&autohide=1&rel=0&wmode=opaque" frameborder="0" allowfullscreen></iframe>';
			$('.video-container', ele).append(iframe);
		};
		
		var type_video_vimeo = function(ele){
			var vimeo_id = vimeo_parser($('.video-container', ele).attr('data-src'));
			var iframe = '<iframe src="http://player.vimeo.com/video/' + vimeo_id + '?title=0&byline=0&portrait=0" width="700" height="500" frameborder="0"></iframe>';
			$('.video-container', ele).append(iframe);
		};
		
		$(this).each(function(){
			var type = $(this).attr('data-type');	
			switch(type) {
				case "gallery" :
					type_gallery(this);
					break;
				case "image" :
					type_image(this);
					break;
				case "video" : 
					type_video(this);
					break;
				case "youtube" :
					type_video_youtube(this);
					break;
				case "vimeo" :
					type_video_vimeo(this);
					break;	
				case "audio" : 
					type_audio(this);
					break;
				default : 
					break;
			};
		});
		
		$(window).bind("jmainremove", function(){			
		});
	};
})(jQuery);

function setupCarousel(itemTotal, navstyle, tourl){
	/** carousel **/
	var minItem;
	if(scw(iphonewidth)) {
		minItem = itemTotal;
	} else {
		minItem = 4;
	}
	
	jQuery('.carousel').elastislide({
		imageW 			: 180,
		minItems		: minItem,
		navigatorStyle 	: navstyle,
		onClick			: function(i){			
			if($('a', i).attr('data-tourl') == "false") {
				return false;
			} else {
				if(jcurtain == 1) {
					window.tourl($('a',i).attr('href'));
					return false;
				} else {
					window.location = $('a',i).attr('href');
					return true;
				}
			}
		}
	});
}

(function($) {
	$.fn.jegfolio = function( options ) 
	{		
		var settings = {
			minItem			: 6
		};

		if (options) {
			var options = $.extend(settings, options);	
		} else {
			var options = $.extend(settings);					
		}
		
		
		/** public **/
		var touch					= false;
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
			touch = true;
		}
		
		
		/** carousel **/
		setupCarousel(options.minItem, 1, true);
		
		/** jeg gallery **/
		$(".gallery-main").jeggallery({});
		
		
		/** portfolio gallery animation **/
		$(".portfolio-gallery li").hover(function(){
			
			$(this).find('.shadow').stop().animate({
				'height' 	: '100%'
			},'fast');

			var h = ( ($(this).height() - $(this).find('.desc-holder').height()) / 2 ) - 5;
			
			$(this).find('.desc-holder').stop().animate({
				'bottom'	: h
			},'fast');
			
		}, function(){
			
			$(this).find('.shadow').stop().animate({
				'height' 	: '0'
			},'fast');
			
			$(this).find('.desc-holder').stop().animate({
				'bottom'	: '-100'
			},10);	
		});
	};
})(jQuery);


(function($) {
	$.fn.jegblog = function( options ) 
	{		
		var settings = {
			minItem			: 4
		};

		if (options) {
			var options = $.extend(settings, options);	
		} else {
			var options = $.extend(settings);
		}
		
		
		/** public **/
		var touch					= false;
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
			touch = true;
		}
		
		var body = $(this);
		
		/** gallery **/
		$(".blog-gallery-type", body).jeggallery({});
		
		
		/** attach photoswipe **/
		if($('.blog-gallery li a').length > 0 || $('.photoswipe').length > 0) {
			
			$('.blog-gallery li a').click(function() { return false; });
			$('.photoswipe').click(function() { return false; });
			
			(function(PhotoSwipe){
				photoswipe = $('.blog-gallery li a , .photoswipe').photoSwipe({
					backButtonHideEnabled 			: false,
					captionAndToolbarAutoHideDelay 	: 0,
					allowUserZoom 					: true,
					getImageSource					: function(obj){ return $(obj).attr('href'); },
					getImageCaption					: function(obj){ return $(obj).attr('data-title'); }
				});
		    }(window.Code.PhotoSwipe));
		}
		
		/** carousel **/
		setupCarousel(options.minItem, 2, false);			
		
		/** comment **/
		$(".replycomment").click(function(){
			var i = $(this).parents(".coment-box").parent();
			var f = $("#respond");
			var x = $("<div id='comment-box-reply'></div>");
			var t = $("<div id='temp-comment-holder' style='display:none;'></div>");
			var p = $("#comment_parent");			
			var c = "data-comment-id";
			
			$(".closecommentform").hide();
			$(".replycomment").show();
			$("#comment-box-reply").remove();
			
			if(!$("#temp-comment-holder").length) {
				t.insertBefore(f);
			}

			
			x.insertAfter($(i).find('.coment-box-inner')).append(f);
			p.val($(this).attr(c));
			
			$(this).hide();
			
			$(i).find(".closecommentform").show().click(function(){
				f.insertAfter($("#temp-comment-holder"));
				$("#temp-comment-holder").remove();
				$("#comment-box-reply").remove();
				$(this).hide();
				$(i).find('.replycomment').show();
				$("#comment_parent").val(0);
			});			
		});
		
	};
})(jQuery);
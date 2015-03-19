jQuery(document).ready(function($) {
	$.Isotope.prototype._getCenteredMasonryColumns = function() {
		this.width = this.element.width();
	    
		var parentWidth = this.element.parent().width();
	    
	                  // i.e. options.masonry && options.masonry.columnWidth
		var colW = this.options.masonry && this.options.masonry.columnWidth ||
	                  // or use the size of the first item
	                  this.$filteredAtoms.outerWidth(true) ||
	                  // if there's no items, use size of container
	                  parentWidth;
	    
		var cols = Math.floor( parentWidth / colW );
		cols = Math.max( cols, 1 );
	
	    // i.e. this.masonry.cols = ....
		this.masonry.cols = cols;
	    // i.e. this.masonry.columnWidth = ...
		this.masonry.columnWidth = colW;
	};
	  
	$.Isotope.prototype._masonryReset = function() {
	    // layout-specific props
		this.masonry = {};
	    // FIXME shouldn't have to call this again
	    this._getCenteredMasonryColumns();
	    var i = this.masonry.cols;
	    this.masonry.colYs = [];
	    while (i--) {
	    	this.masonry.colYs.push( 0 );
	    }
	};
	
	$.Isotope.prototype._masonryResizeChanged = function() {
		var prevColCount = this.masonry.cols;
	    // get updated colCount
		this._getCenteredMasonryColumns();
		return ( this.masonry.cols !== prevColCount );
	};
	  
	$.Isotope.prototype._masonryGetContainerSize = function() {
		var unusedCols = 0,
			i = this.masonry.cols;
	    // count unused columns
		while ( --i ) {
			if ( this.masonry.colYs[i] !== 0 ) {
				break;
			}
			unusedCols++;
		}
	    
		return {
			height : Math.max.apply( Math, this.masonry.colYs ),
	          // fit container to columns that have been used;
			width : ((this.masonry.cols - unusedCols) * this.masonry.columnWidth) + 5
		};
	};
});
/** jeglio v1 **/

(function($) {
	$.fn.jeglio = function( options ) 
	{
		// item mode : 0 = normal, 1 = masonry
		var settings = {
			itemWidth			: 180,
			itemHeight 			: 210, 
			itemHeightWide		: 550,
			galleryDim			: 3,
			descDim				: 1,
			theatherMode		: false,
			scrolltop			: 50,
			flexDelay			: 7000,
			zoomDelay			: 7000,
			loadAnimation		: 'sequpfade'  // normal | fade | seqfade | upfade | sequpfade | randomfade | randomupfade
		};

		if (options) {
			var options = $.extend(settings, options);	
		} else {
			var options = $.extend(settings);					
		}
		
		var $container 				= $(this);
		var lastclicked 			= undefined;
		var layoutmode 				= undefined;
		var itemWidth 				= undefined;
		var itemHeight				= undefined;
		var itemGalleryWidth 		= undefined;
		var itemDescriptionWidth 	= undefined;	
		var touch					= false;
		var photoswipe				= undefined;
		var iw						= undefined;
		var ih 						= undefined;
		var doctitle				= document.title;
		var iphonewidth				= 150;
		var firstloaded				= true;
		var itemTopPos				= undefined;
		var itemLeftPos				= undefined;		
		
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)) || (navigator.userAgent.match(/iPad/i))) {
			touch = true;
		}
		
		var galleryDim 		= parseInt(options.galleryDim);
		var descDim 		= parseInt(options.descDim);
		var lockClick		= false;
		
		var hovered = function(e)
		{
			var $this = $(e.currentTarget);
			var th = $this.height();
			
			/** show type of item **/
			var i = $this.find('i');			
			$(i.get(0)).addClass('display-inline-table');
			
			$this.find('.shadow').stop().animate({
				height : th
			}, 'fast');
			
			var descholder = $this.find('.desc-holder');
			var dh = ( th - descholder.height() )  / 2; 
			$this.find('.desc-holder').stop().animate({
				bottom : dh
			}, 'fast', function(){	
				if(touch) {
					link = $($this.find('a').get(0)).attr('href');
					window.location = link;
				}
			});
		};
		
		var unhovered = function(e)
		{
			var $this = $(e.currentTarget);
			var th = $this.height();
			
			// show type of item
			var i = $this.find('i');			
			$(i.get(0)).removeClass('display-inline-table');
			
			$this.find('.shadow').stop().animate({
				height : 0
			}, 'fast');
			$this.find('.desc-holder').stop().animate({
				bottom : 0
			}, 'fast');
		};
		
		var type_video = function(ele)
		{
			var w = $(ele.itemGallery).css('width');
			var h = $(ele.itemGallery).css('height');
			
			$('video', ele.itemGallery).attr('width', w).attr('height', h);
			$('img', ele.itemGallery).css('width', w).css('height', h);
			$('video', ele.itemGallery).mediaelementplayer({});
		};
		
		/** vimeo & youtube **/
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
		
		var type_video_youtube = function(ele)
		{
			var w = $(ele.itemGallery).css('width');
			var h = $(ele.itemGallery).css('height');
			
			var youtube_id = youtube_parser($('.video-youtube-container', ele.itemGallery).attr('src'));
			var iframe = '<iframe width="' + w + '" height="' + h + '" src="http://www.youtube.com/embed/' + youtube_id +  '?showinfo=0&theme=light&autohide=1&rel=0&wmode=opaque" frameborder="0" allowfullscreen></iframe>';
			$('.video-youtube-container', ele.itemGallery).append(iframe);
		};
		
		var type_video_vimeo = function(ele)
		{
			var w = $(ele.itemGallery).css('width');
			var h = $(ele.itemGallery).css('height');
			
			var vimeo_id = vimeo_parser($('.video-vimeo-container', ele.itemGallery).attr('src'));
			var iframe = '<iframe src="http://player.vimeo.com/video/' + vimeo_id + '?title=0&byline=0&portrait=0" width="' + w + '" height="' + h + '" frameborder="0"></iframe>';
			$('.video-vimeo-container', ele.itemGallery).append(iframe);
		};
		

		var type_audio = function(ele)
		{
			var w = $(ele.itemGallery).css('width');
			var h = 30;
			$('audio', ele.itemGallery).attr('width', w).attr('height', h);
			$('audio', ele.itemGallery).mediaelementplayer({
				pluginPath: "css/mediaelement/"
			});
		};	
		
		var type_gallery = function(ele)
		{
			$(ele.itemGallery).flexslider({
				animation: "slide",
				slideDirection: "horizontal",
				slideshow: true,
				slideshowSpeed: options.flexDelay,
				animationDuration: 300,
				directionNav: false,
				controlNav: true,
				keyboardNav: true,
				mousewheel: false,
				prevText: "Previous",
				nextText: "Next",
				pausePlay: false,
				pauseText: 'Pause',
				playText: 'Play',
				randomize: false,
				slideToStart: 0,
				animationLoop: true,
				pauseOnAction: true,
				pauseOnHover: false,
				controlsContainer: "",
				manualControls: "",
				start: function(){},
				before: function(){},
				after: function(){},
				end: function(){}
			});
						
			type_image(ele);
		};	
		
		var type_image = function(ele)
		{
			/** add tooltips for tips **/
			if(!touch) {
				$(ele.itemGallery).find('.item-gallery-image').jtooltip({});
			}
			
			/** photo swipe **/
			(function(PhotoSwipe){
				photoswipe = $(ele.itemGallery).find('.item-gallery-image').photoSwipe({
					backButtonHideEnabled 			: false,
					captionAndToolbarAutoHideDelay 	: 0,
					slideshowDelay					: options.zoomDelay,
					slideSpeed						: 500,
					allowUserZoom 					: false,
					getImageSource					: function(obj){ return $(obj).attr('data'); },
					getImageCaption					: function(obj){ return $(obj).attr('data-title'); }
				});
				photoswipe.addEventHandler(PhotoSwipe.EventTypes.onHide, function(e){
					$container.isotope("reLayout");
				});
	        }(window.Code.PhotoSwipe));
			
			$('.item-gallery-image').click(function(){	        			
				return false;
			});
		};		
		
		var bindlovethis = function (i) {
			$(".love-this", i).each(function(){
				$(this).click(function(){
					if(!$(this).hasClass("voted")) {									
						var $this = $(this);
						$this.addClass("locked");
						var lovecounter = $this.find('.love-counter');
						$(lovecounter).text('Loading ...').addClass('italic');
						
						var loveuri = ajax_url + "Galerie/Ajax/vote.json";	
						
						$.ajax({
							url: loveuri,
							type : "post",
							dataType : "json", 
							data : {
								postid		: $(this).attr('data-id'),
								action		: 'vote_post'
							},
							success: function(data) {
								$(lovecounter).text(data.msg);
								
								setTimeout(function(){
									if(data.id == 3) {
										$this.attr('data-counter', data.total);
										$(lovecounter).removeClass('italic');
										$this.addClass('voted');
									}

									$this.removeClass("locked");
									$(lovecounter).text($this.attr('data-counter')).removeClass('italic');									
								}, 1500);
							}
						});
					} else {
						var votedtext = $(this).attr('data-voted');
					}
				}).hover(function(){    						
						if($(this).hasClass("voted")) {
							var votedtext = $(this).attr('data-voted');
							var lovecounter = $(this).find('.love-counter');						
							lovecounter.text(votedtext);
						} else {
							if(!$(this).hasClass("locked")) {	    								
								var votedtext = $(this).attr('data-vote');
								var lovecounter = $(this).find('.love-counter');						
								lovecounter.text(votedtext);
							}
						}
					}, function(){
						if(!$(this).hasClass("locked")) {
							var countertext = $(this).attr('data-counter');
							var lovecounter = $(this).find('.love-counter');						
							lovecounter.text(countertext);	
						}
				});
			});
		};
		
		var theatherClose = function(i, id, changeLocation)
		{
			closePrev(changeLocation);
			
			if(lastclicked) {
				$(lastclicked.i).find('.love-this').replaceWith(function(){
	        		return $('<div>').append($(i).find('.love-this').clone()).html();
	        	});
				bindlovethis(lastclicked.i);
			}
			
			// stop music & video
			$('video, audio', id).each(function() {
				$(this)[0].pause();
			});
			
			$(i).fadeOut("fast", function(){
				$(id).remove();
				$("html").css("overflow-y", "auto");
				$container.css({'max-height' : '100%'});
			});			
		};
		
		// $this => clicked item
		var contentTheater = function($this, title, type, love, voted){
			var i 				= $("#item-theater-overlay");
			var itd 			= $("#item-theater-detail", i);
			var id				= $(".item-detail", i);
			var ele 			= itemDetailElement(id);
			var openprev		= false;					
			var flagTheater		= 0;
			
			/** close previous opened section **/
			if(lastclicked != undefined) {
				closePrev(false);
			}			
			
			$("html, body").animate({
                scrollTop: 0
            }, function(){
            	setTimeout(function(){
            		if(!flagTheater) {
            			showTheater();
            			flagTheater++;
            		}
            	}, 500);
            });  
			
			var showTheater = function() {
	        	// hack for ipad
				if(touch) {
					var wh = $(window).height();
					var ofh = $container.offset();
					var mbtm = parseInt($container.css('margin-bottom'), 10);
					ch = wh - ofh.top - mbtm;
					$container.css({'max-height' : ch});
				} else {
					$("html").css("overflow-y", "hidden");
				}
				
				/*** show item detail & set opacity ***/
				$(id).show().css({"opacity"	: 1});
				$(itd).css({"display" : "block"});
				$(i).fadeIn("fast", function(){
					
					$(itd).css({
			            'width'			: itemWidth,
			            'height'		: itemHeight,
			            'top'			: itemTopPos,
			            'left'			: itemLeftPos,
			            'position'		: 'absolute'
			        });	
					
					/** title **/
	        		document.title = title.unescapeHtml() + " - "  + doctitle.unescapeHtml();
					
					$(ele.itemGallery).css('width', itemGalleryWidth).css('height', itemHeight);
		    		$(ele.itemDescription).css('width', itemDescriptionWidth); 
		    		
		    		// set next & prev 
		    		var nextprev = get_next_prev($this);
		        	$(i).find('.item-prev').parent().attr('href' , nextprev.prev);
		        	$(i).find('.item-next').parent().attr('href' , nextprev.next);
		    		    	    		
	    			// love counter
		        	$(i).find('.love-this').replaceWith(function(){		        		
		        		return $('<div>').append($this.find('.love-this').clone()).html(); 
		        	});
		        	
		        	bindlovethis(i);
	    			
		        	// bug waktu back button
	    			var dww = itemDescriptionWidth - parseInt(ele.descWrapper.css('margin-left'), 10) - parseInt(ele.descWrapper.css('margin-right'), 10);	    			
		    		        		
	    			$(ele.descWrapper).css({
	    				height 			: (itemHeight - 50),
	    				width 			: dww 
	    			}).jScrollPane();
	    			
					switch(type) {
						case "gallery" :
							type_gallery(ele);
							break;
						case "image" :
							type_image(ele);
							break;
						case "video" : 
							type_video(ele);
							break;
						case "youtube" :
							type_video_youtube(ele);
							break;
						case "vimeo" :
							type_video_vimeo(ele);
							break;	
						case "audio" : 
							type_audio(ele);
							break;
						default : 
							break;
					};
	    			
					// release lock
					lockClick = false;
					
					// save last click element
					lastclicked = {
		        		i : $this,
		        		t : type
		        	};
					
					setTimeout(function()
					{
						var hash = location.hash.replace("#!/", "");
		        		var hashres = hash.split(':');
		    			var openlarge = hashres[1];	        		
		    			
		    			if(openlarge != undefined) {
			    			(function(PhotoSwipe){
			    				$(PhotoSwipe.instances[0].originalImages[openlarge]).trigger('click');
			    	        }(window.Code.PhotoSwipe));
		    			}
					}, 500);
					
					/** close when background clicked **/
					$(i).click(function(ev){
						if (ev.target == this){
							theatherClose(i, id, true);						
						}					
					});
					
					/** bind close me button **/
		        	$(".closeme", itd).show().bind("click", function(){
		        		theatherClose(i, id, true);
		        	});
				});	
			};								
		};
		
		var get_next_prev = function($this)
		{
			/** bind prev & next **/
        	var prevlink = undefined;
        	var nextlink = undefined;
        	
        	/** find prev link end **/
        	var eleprev = $this;
        	while(eleprev = $(eleprev).prev()){
        		if(!eleprev.hasClass('isotope-hidden')) {
        			break;
        		}
        	}
        	
        	var elenext = $this;
        	while(elenext = $(elenext).next()){
        		if(!elenext.hasClass('isotope-hidden')) {
        			break;
        		}
        	}
        	
        	prevlink = $($(eleprev).find('a').get(0)).attr('href');
        	nextlink = $($(elenext).find('a').get(0)).attr('href');
        
        	if(prevlink === undefined) {
        		eleprev = $('.item:last-child', $container);
        		while(eleprev){		
	        		if(!eleprev.hasClass('isotope-hidden')) {
	        			break;
	        		}
	        		eleprev = $(eleprev).prev();
        		}
        		prevlink = $($(eleprev).find('a').get(0)).attr('href');
        	}
        	
        	if(nextlink === undefined) {
        		elenext = $('.item:first-child', $container);
        		while(elenext){
	        		if(!elenext.hasClass('isotope-hidden')) {
	        			break;
	        		}
	        		elenext = $(elenext).next();
	        	}
        		nextlink = $($(elenext).find('a').get(0)).attr('href');
        	}
        	
        	$this.find('.item-prev').parent().attr('href' , prevlink);
        	$this.find('.item-next').parent().attr('href' , nextlink);
        	
        	return {
        		next : nextlink,
        		prev : prevlink
        	};
        	/** find prev link end **/
		};
		
		// this clicked item
		var contentLoaded = function($this, firstLoaded, title, type)
		{
			var ele 			= itemDetailElement($this);		
						
			/** close previous opened section **/		
			if(lastclicked != undefined) {
				closePrev(false);
			}
			
			$this.animate({
	            width: itemWidth,
	            height: itemHeight
	        }, 400, function () {
	        	
	        	/** hide item wrapper **/
	        	$(ele.itemWrapper).hide();
	        	
	        	/** bind close me button **/
	        	$(ele.closeme).show().bind("click", function(){
	        		closePrev(true);
	        	});
	        	
	        	/** add class unfolded **/
	        	$this.addClass("unfolded");	        	
	        	
	        	/** find prev link start **/
	        	var nextprev = get_next_prev($this);
	        	$this.find('.item-prev').parent().attr('href' , nextprev.prev);
	        	$this.find('.item-next').parent().attr('href' , nextprev.next);
	        	/** find prev link end **/
	        	
	        	
	        	/** relayout isotope **/
	        	$container.isotope("reLayout", function (e) {
	        		
	        		/** item gallery & description width & height **/
	        		$(ele.itemGallery).css('width', itemGalleryWidth).css('height', itemHeight);
	        		$(ele.itemDescription).css('width', itemDescriptionWidth); 

	        		/** show item detail **/
	        		$(ele.itemDetail).show().animate({opacity : '1' });
	        		        		
	        		if(firstLoaded) 
	        		{
		        		/** set title & type **/
		        		$this.attr('data-title'	, title);
	        			$this.attr('data-type'	, type);   		
		        		
	        			/** assign scroll pane **/
	        			var width = ele.descWrapper.css('width');
	        			
	        			// bug waktu back button
	        			var dww = itemDescriptionWidth - parseInt(ele.descWrapper.css('margin-left'), 10) - parseInt(ele.descWrapper.css('margin-right'), 10);	
	        			
	        			$(ele.descWrapper).css({
	        				height 	: (itemHeight - 50),
	        				width 	: dww
	        			}).jScrollPane();
	        			
	        			switch(type) {
		        			case "gallery" :
		    					type_gallery(ele);
		    					break;
		    				case "image" :
		    					type_image(ele);
		    					break;
		    				case "video" : 
		    					type_video(ele);
		    					break;
		    				case "youtube" :
		    					type_video_youtube(ele);
		    					break;
		    				case "vimeo" :
		    					type_video_vimeo(ele);
		    					break;	
		    				case "audio" : 
		    					type_audio(ele);
		    					break;
		    				default : 
		    					break;
	        			}
	        		}
	        		
	        		/** title **/
	        		document.title = title.unescapeHtml() + " - " + doctitle.unescapeHtml() ;
	        		
	        		// relase lock
	        		lockClick = false;
	        		
	        		/** animate item to scroll **/
	        		setTimeout(function(){
		                $("html, body").animate({
		                    scrollTop: $this.offset().top - options.scrolltop
		                }, 500, function(){
		                	// cache last clicked object
			        		lastclicked = {
			        			i : $this,
			        			t : type
			        		};
			        		
			        		setTimeout(function()
	    					{
	    						var hash = location.hash.replace("#!/", "");
	    		        		var hashres = hash.split(':');
	    		    			var openlarge = hashres[1];	        		
	    		    			
	    		    			if(openlarge != undefined) {
	    			    			(function(PhotoSwipe){
	    			    				$(PhotoSwipe.instances[0].originalImages[openlarge]).trigger('click');
	    			    	        }(window.Code.PhotoSwipe));
	    		    			}
	    					}, 500);
			    			
		                });		               
	        		}, 500);
	            });
	        });
		};
			
		var itemDetailElement = function($this)
		{
			return {
				me					: $this,
				shadow 				: $this.find('.shadow'),
				itemWrapper 		: $this.find('.item-wrapper'),
				closeme 			: $this.find('.closeme'),
				itemDetail 			: $this.find('.item-detail'),
				itemGallery 		: $this.find('.item-gallery'),
				itemDescription 	: $this.find('.item-description'),
				descWrapper 		: $this.find('.item-desc-wrapper')
			};
		};	
	
		var closePrev = function (changeLocation)
		{
			/* revert title */
			document.title = doctitle.unescapeHtml();
			
			if(lastclicked === undefined) {
				return false;
			}
			
			var i = lastclicked.i;
			
			$(i).find('.item-detail').animate({					// hide item detail
				opacity		: 0
			}, 100).hide();	
						
			var wh = {
				width : $(i).attr('data-width') ,
				height : $(i).attr('data-height')
			};
			
			$(i).animate(wh, 'fast', function(){
				$(i).find('.closeme').hide();						// hide close me button				
				$(i).find('.item-wrapper').show('fast');			// show item wrapper (summary)
				$(i).removeClass('unfolded');						// remove class unfolded
				$(i).find('.item-detail').remove();					// destroy item detail, we really need this to simplify code & prevent lag when many item loaded
				
				// normalize item 
				$(i).find('.desc-holder').css({'bottom' : 0}).find('i').removeClass('display-inline-table');				
				$(i).find('.shadow').removeClass("shadowload").height('0');		// remove white shadow on item
				
				lastclicked = undefined;
				$container.isotope("reLayout");	
			});	
			
			if(changeLocation) {
				window.location = "#!/";
			}
		};	
		
		var show_loader = function($this)
		{
			// @todo : show small-loader
			var loader 			= $this.find('.small-loader');
			var loaderWidth 	= $(loader).width();
			var loaderHeight 	= $(loader).height();
			var thisWidth		= $this.width();
			var thisHeight		= $this.height();
			var postX			= ( thisWidth / 2 ) - (loaderWidth / 2);
			var postY			= ( thisHeight / 2 ) - (loaderHeight / 2);		
			
			$this.find('.small-loader').show().css('margin-left', postX).css('margin-top', postY);
		};
		
		var hide_loader = function($this)
		{
			$this.find('.small-loader').hide();
		};
		
		var clicked = function($this)
		{
			if(lockClick) {
				return ;
			}
			else {
				lockClick 			= true; // lock click		
				
				/** show shadow load **/
				$this.find('.shadow').addClass('shadowload');
				
				/** save current width & height **/
				$this.attr('data-width', $this.width());
				$this.attr('data-height', $this.height());
				
				/** if item not exist yet, then we should call ajax and append content **/
				if($this.find('.item-detail').length <= 0) {
					
					/** show loader **/
					show_loader($this);
									
					if(options.theatherMode) {
						if(lastclicked != undefined) {
							var i 				= $("#item-theater-overlay");
							var id				= $(".item-detail", i);
							theatherClose(i, id, false);
						}
					}
					
					var showPasswordForm = function (status) {
						if(!$(".portopwd").length) {
							// create password form
							var pwdtxt = '<div class="portopwd">' +
										'<div class="portopwd-wrapper">' +
											'<h2>' + options.lang.portfoliotitle +  ' </h2> ' +
											'<div>' +
												'<input type="password" class="pwdtxt" placeholder="' + options.lang.passwordplaceholder +  '"/>' +
												'<button href="#" class="btn btn-inverse pwdbtn">' + options.lang.submit + '</button>' +
											'</div>' +
											'<div class="pwderr"></div>' +
											'<div alt="Close" class="pwdcls" style="display: block;">' +
												'<div class="icon-remove icon-white"></div>' +
											'</div>' +
										'</div>' +
									  '</div>';
							$('body').append(pwdtxt);
						}
						
						$(".pwdcls").click(function(){
							$this.find('.small-loader').hide();							
							$(".portopwd").remove();
							window.location = "#!/";							
							lockClick 			= false;
							$this.find('.shadow').removeClass("shadowload");
						});
						
						var submitpwdform = function () {
							ajaxdata.password = $(".pwdtxt").val();
							$(".portopwd").remove();
							loadData();
						};
						
						$(".pwdtxt").keypress(function(e){
							if(e.which == 13){
								submitpwdform();
							}
						});
						
						$(".pwdbtn").click(function(){
							submitpwdform(); 
						});
					};
					
					var attach_social = function () 
					{
						/** facebook like **/
						var fbl = $(document.createElement("fb:like"))
							.attr("href", document.URL)
							.attr("send", "false")
							.attr("layout", "button_count")
							.attr("show_faces", "false");
						
						var fbl = $("<div class='fblio'></div>").append(fbl);						
				        $(".wrapper-social", $this).empty().append(fbl);
				        FB.XFBML.parse($(".fblio", $this).get(0));
				        
				        /** twitter sharer **/
				        var twt = $(document.createElement("a"))
				        		.attr("class", "twitter-share-button")
				        		.attr("href", "http://twitter.com/share")
								.attr("data-url", document.URL)
								.html("Tweet");
				        
				        var twt = $("<div class='twtlio'></div>").append(twt);
				        $(".wrapper-social").append(twt);
				        twttr.widgets.load();						
					};
					
					var showData = function (data) {
						$this.find('.small-loader').hide();
						
						// mute player when selected content is music / video
						if(data.type == "video" || data.type == "youtube" || 
						   data.type == "vimeo" || data.type == "audio") {
							muteplayer();
						} else {
							unmuteplayer();
						}
						
						if(options.theatherMode) {
							$("#item-theater-detail").append(data.content);
							contentTheater($this, data.title, data.type, data.love, data.voted);
						} else {
							$this.append(data.content);
							contentLoaded($this, true, data.title, data.type);
						}
						
						/** now we attach social like **/
						attach_social();
						
					    /** hide loader **/
						hide_loader($this);
					};
					
					var ajaxdata = {
						id		: $this.attr('data-id'),
						width	: itemGalleryWidth,
						height	: Math.floor(itemHeight),
						theater	: options.theatherMode,
						action	: 'get_portfolio_item'
					};
					
					/** build url for requesting portfolio file **/
					var requri = ajax_url + "Galerie/Portfolio/" + $this.attr('data-id') + "/GetJson.json";					
					
					var loadData = function() {
						$.ajax({
							url: requri,
							type : "post",
							dataType : "json", 
							data : ajaxdata,
							success: function(data) {
								if(data.status == 1) {
									// data loaded successfully
									showData(data);									
								} else  if (data.status == 2) {
									// need password
									showPasswordForm(0);
								} else if (data.status == 3) {
									// invalid password
									showPasswordForm(1);
								} else if (data.status == 4) {									
									// wrong post format
								}
								
							}
						});
					};
										
					/** load data from admin **/
					loadData();
					
				} else {
					// untuk sementara waktu ga bakal pernah dipake
					// contentLoaded($this, false, $this.attr('data-title'), $this.attr('data-type'));
				}
			}
		};	
		
		/** use css instead js positioning **/			
		var rearrange_filter = function() {};		
		var rearange_media_query	= function () {};
		
		$container.imagesLoaded( function($images, $proper, $broken) 
		{
			// close loader & show filter
			$('.lio-loader').fadeOut('fast');			
			
			// build isotope
			$container.isotope({
				animationEngine: "jquery",
	            itemSelector: ".item",
	            masonry: {
					columnWidth: 1
				}
			});
			
			bindlovethis($container);
			
			/** run window resize when image loaded to prevent bug **/
			jWindowResize(false);
			
			/** then show image **/
			animload(options.loadAnimation);
						
			setTimeout(function(){
				$(window).hashchange();
			}, 500);		
		});
		
		// normal | fade | seqfade | upfade | sequpfade | randomfade | randomupfade
		var animload = function(animation){
			
			if(animation 			== "normal") {
				$('#liofilter').fadeIn();
				
				$container.find('.item').each(function(){
					$(this).show();
				});
				
				executeBindHash();
			} else if(animation 	== "fade"){
				$('#liofilter').fadeIn("slow");
				
				$container.find('.item').each(function(){
					$(this).fadeIn('slow');
				});
				
				executeBindHash();
			} else if(animation	 	== "seqfade"){
				$('#liofilter').fadeIn(1000);
				
				$container.find('.item').each(function(i){
					var $el = $(this);
					setTimeout(function() {
						$el.fadeIn(300);
					}, 100 + i * 100);
				});
				
				setTimeout(function(){
					executeBindHash();
				}, 100 + $container.find('.item').length * 100);
				
			} else if(animation	 	== "upfade"){
				$('#liofilter').fadeIn(2000);
				
				// setup item
				$container.find('.item').each(function(i){
					var $item 	= $(this),
					t		= parseInt($item.css('top'),10) + ( $item.height() / 2);
					$item.css({ top	: t + 'px', opacity : 0});
				});
				
				$container.find('.item').each(function(i){
					var $el 		= $(this);					
					$el.show();
					$el.stop().animate({
						top			: parseInt($el.css('top'),10)- ( $el.height() / 2),
						opacity		: 1
					}, 1000);
				});
				
				setTimeout(function(){
					executeBindHash();
				}, 1000);
				
			} else if(animation	 	== "sequpfade"){
				$('#liofilter').fadeIn(2000);
				
				// setup item
				setuptop();
				
				$container.find('.item').each(function(i){
					var $el 		= $(this); 
					
					setTimeout(function() {
						$el.show();
						$el.stop().animate({
							top			: parseInt($el.css('top'),10)- ( $el.height() / 2),
							opacity		: 1
						}, 300);
					}, 100 + i * 100);
				});
				
				setTimeout(function(){
					executeBindHash();
				}, 100 + $container.find('.item').length * 100);
				
			} else if(animation	 	== "randomfade"){				
				$('#liofilter').fadeIn(2000);
				
				var shufflearray = shuffleitem();
				
				for(var i = 0; i < shufflearray.length ; i++){					
					setTimeout(function() {
						var $el = shufflearray.pop();
						$el.fadeIn(200);
					}, 75 + i * 75);
				}
				
				setTimeout(function(){
					executeBindHash();
				}, 100 + $container.find('.item').length * 100);
				
			}else if(animation	 	== "randomupfade"){				
				$('#liofilter').fadeIn(2000);
			
				var shufflearray = shuffleitem();
				setuptop();
				
				for(var i = 0; i < shufflearray.length ; i++){			
					setTimeout(function() {
						var $el = shufflearray.pop();
						$el.show();
						$el.stop().animate({
							top			: parseInt($el.css('top'),10)- ( $el.height() / 2),
							opacity		: 1
						}, 300);
					}, 75 + i * 75);
				}
				
				setTimeout(function(){
					executeBindHash();
				}, 100 + $container.find('.item').length * 100);
			}
			
			$container.find('.item').each(function(){
				$(this).hover(hovered,unhovered);
			});
		};
		
		var executeBindHash = function(){
			setTimeout(function(){
				bindhashchange();
				winhash();
			}, 300);
		};

		var setuptop = function () {
			// setup item
			$container.find('.item').each(function(i){
				var $item 	= $(this),
				t		= parseInt($item.css('top'),10) + ( $item.height() / 2);
				$item.css({ top	: t + 'px', opacity : 0});
			});
		};
		
		var shuffleitem = function() {
			var shufflearray = new Array();
			$container.find('.item').each(function(i){ shufflearray[i] = $(this); });
			shufflearray.sort(function(){return Math.round(Math.random());});
			return shufflearray;
		};
		
		var calc_theather = function (){
			var ww = $(window).width() - 50;
			var wh = $(window).height() - $("header").height() - $("footer").height() - 50;			
			
			var theatherdim = {};
			
			if($(window).width() > $(window).height()) {
				var ratio = 11 / 5;
			} else {
				var ratio = 6/5;
			}
						
			if( ( ww / ratio ) < wh ) {
				// use ww as base
				theatherdim.w = ww;
				theatherdim.h = ww / ratio;
			} else {
				// use wh as base
				theatherdim.h = wh;
				theatherdim.w = wh * ratio;				
			}
			
			theatherdim.topPos 		= ( $(window).height() - $("header").height() - $("footer").height() - theatherdim.h ) / 2 + $("header").height();
			theatherdim.leftPos 	= ( $(window).width() - theatherdim.w ) / 2;
			
			theatherdim.descriptionWidth = theatherdim.w * 0.2;
			theatherdim.descriptionWidth = ( theatherdim.descriptionWidth < 200 ) ? 200 : theatherdim.descriptionWidth;
			theatherdim.galleryWidth 	 = theatherdim.w - theatherdim.descriptionWidth;
			
			return theatherdim;
		};
		

		// detect only window width change
		var winwidth = $(window).width(), winheight = $(window).height();
		$(window).resize(function() {
			if(this.resizeTo) clearTimeout(this.resizeTo);
			this.resizeTo = setTimeout(function() {
				if(winwidth != $(window).width()) {
					winwidth = $(window).width();
					$(this).trigger("windowWidthResize");
				}
				
				if(winheight != $(window).height()) {
					$(this).trigger("windowHeightResize");
				}
			}, 250);
		});
		
		
		var windowWidthChange = function ()
		{
			if(options.theatherMode) {
				
				if(lastclicked != undefined) {
					var cachelastClick = lastclicked;
					
					var i 				= $("#item-theater-overlay");
					var id				= $(".item-detail", i);
					theatherClose(i, id, true);
					
					// than reopen last clicked item
					//console.log(cachelastClick);
					// clicked(cachelastClick.i);
				}
			}
		};
		
		var windowHeightChange = function () 
		{
			
		};
		
		/** then when image resize, we do this! **/	
		var jWindowResize = function(relayout)
		{			
			var windowWidth 	= $(window).width();
			
			var item 			= $container.find('.item').get(0);				
			var imb				= parseInt($(item).css('margin-bottom'), 10);
			var iml				= parseInt($(item).css('margin-left'), 10);
			
			iw 					= options.itemWidth;						
			
			if(options.theatherMode) {
				var theaterdim = calc_theather();				
								
				itemWidth 		= theaterdim.w;
				itemHeight 		= theaterdim.h;
							
				itemDescriptionWidth 	= theaterdim.descriptionWidth;
				itemGalleryWidth 		= theaterdim.galleryWidth;
				
				itemTopPos				= theaterdim.topPos;
				itemLeftPos				= theaterdim.leftPos;
			} else {
				itemGalleryWidth		= ( iw * galleryDim ) + iml * (galleryDim - 1);
				itemDescriptionWidth	= ( iw * descDim ) + iml * descDim;
				
				for(var i = (galleryDim + descDim); i > 1  ; i--) {
					var tempWidth 	= ( iw * i ) + ( iml * i ) - iml;				
					if(tempWidth + 10 <= windowWidth){
						itemWidth 			= tempWidth;
						itemHeight 			= options.itemHeightWide ;
						break;
					}
				}
			}			
			
			if(relayout) {
				// console.log("Execute window resize with relayout");
				$container.isotope("reLayout");	
			} else {
				// console.log("Execute window resize without relayout");
			}					
		};
		
		var closeAllMode = function () {
			if(options.theatherMode) {
				var i 				= $("#item-theater-overlay");
				var id				= $(".item-detail", i);
				theatherClose(i, id, false);
			} else {
				closePrev();
			}
		};
		
		var findElement = function(hash)
		{
			for(var i = 0; i < $('.item').length ; i++) {
				var item = $('.item').get(i);
				if(hash == $(item).attr('data-url')) {
					return $(item);
				}
			}		
		};
		
		var hidePhotoswipe = function()
		{
			if(window.Code.PhotoSwipe.activeInstances[0] == undefined) {
				// console.log("photoswipe is undefined");
			} else {
				window.Code.PhotoSwipe.activeInstances[0].instance.hide(0);			
			}
		};
		
		var winhash = function(){
			var hash = location.hash.replace("#!/", "");
			var hashres = hash.split(':');
			hash = hashres[0];
			
			if(hash === "" || hash == "#!") {
				closeAllMode();
				unmuteplayer();
			} else {
				var element = findElement(hash);
				if(scw(mediaquerywidth)) {	
					if(element) {
						if(!options.theatherMode) {
							setTimeout(function(){
								$("html, body").animate({
									scrollTop: $(element).offset().top - options.scrolltop
								});
							}, 500);
						}
						clicked(element);
					}					
				} else {
					// buka page baru
					var url = element.attr('data-src');
					tourl(url, true);
				}
			}
			
			hidePhotoswipe();
		};
		
		/** filter **/
		$("#liofilter .apply-filter").click(function(){
			$("#liofilter .apply-filter").removeClass("filter-select");
			var selector = $(this).attr("data-filter");
			$(this).addClass("filter-select");
			// we need to close previous opened item
			closePrev(true);
			$container.isotope({filter : selector});
			return false;
		});
		
		/** show loader **/
		$('.lio-loader').fadeIn('fast');
		
		/** bind hash change & resize **/
		var bindhashchange = function() {
			$(window).bind('hashchange', function(){ winhash(); });
		};
		
		$(window).bind('resize', function(){
			if(!firstloaded) {
				jWindowResize(true);
			} else {
				jWindowResize(false);
			}
			firstloaded = false;
		});
		
		$(window).bind('windowWidthResize', function(){ windowWidthChange(); });
		$(window).bind('windowHeightResize', function(){ windowHeightChange(); });
				
		/** unbind window resize sama hashchange saat #main ke destroy **/
		$(window).bind("jmainremove", function(){			
			$(window).unbind('hashchange');
			$(window).unbind('resize');
			$("html").css("overflow-y", "auto");
			delete $.fn.jeglio;	
		});
	};
})(jQuery);

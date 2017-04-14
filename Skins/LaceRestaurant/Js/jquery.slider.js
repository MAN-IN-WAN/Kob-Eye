(function($) {
	$.fn.slider = function(o) {
		o = $.extend({ //Config
			auto: null, //durée de l'autoplay (en ms)
			speed: 1000, //durée de l'animation (en ms), minimum 500
			nav: null, //navigation (bool)
			sizefix: null, //fixer la width (integer)
			navipuce: null, //navigation puce (bool)
			displayminimum: null, //affichage special si inferieur a 3 (bool)
			fullscreen: null, //fullscreen (bool)
			height: null, //ratio hauteur/largeur (float)
			fx: null //animation aléatoire (bool)
		}, o || {});		
		return this.each(function() {
			var slider = $(this), //container slider
				figures = $('.figures',slider), //container figures
				figure = $("figure", figures), //figures
				curr = -1, //figure courante
				timer, //Timer auto
				fullscreen = false, //fullscreen actif
				nav, //container nav
				navonly = false, //si uniquement nav
				sizefix, //fixer la width
				navipuce, //navipuce
				displayminimum = false, //displayminimum
				currnav = 0, //nav courante
				bigs = new Array(), //tableau des bigs pour load async
				loadBig = 0, //nb de big chargées
				loader = null; //loader
				
			/*** If n elements > 2 ***/	
			if( (figure.length>2 || !o.displayminimum) && (figure.length>0) ){
				/*************************************/
				/*PREV - NEXT*/
				/*************************************/	
				figures.append('<span class="prev"></span><span class="next"></span>');
				$(".prev",figures).click(function() { go(curr-1); if (o.nav) navgo(curr); });
				$(".next",figures).click(function() { go(curr+1); if (o.nav) navgo(curr); });
				/*************************************/
				/*NAVIGATION PUCES*/
				/*************************************/
				if (o.navipuce){
					li = ""; for(n=0; n<$(figure).length;n++){ li += "<li></li>";}  //nombre de puces
					$(slider).append(" <ul class='navipuce'>"+li+"</ul> ");			 //ajout de la navipuce
					$(".navipuce li",slider).click(function() { go($(this).index()); navgo(curr); });  //events sur les puces
				}
				/*************************************/
				/*ASYNC (balise a)*/
				/*************************************/
				if($("a.big",this).length>0){
					//Loader
					$(slider).hide().append(loader = $('<div class="loader"><span></span></div>'));
					//Traitement des bigs
					$("a.big",figures).each(function(i) {
						bigs.push($(this).attr('href'));
						$(this).parent('figure').prepend('<img class="big" src="" alt="'+$(this).html()+'" />');
						$(this).remove();
					});	
					//Load des bigs
					$("img.big",figures).load(function() {
						loadBig++;
						if (loadBig==figure.length) loader.remove();
						else $('span',loader).html(loadBig+'/'+figure.length);
					});		
				}
				/*************************************/
				/*NAV*/
				/*************************************/	
				if(o.nav){
					//Ajout de la nav
					if(o.sizefix){ var cls=" sizecls ";}else { var cls="";}
					slider.append(nav = $('<div class="figures-nav'+cls+'"><span class="prev"></span>'
						+(o.fullscreen?'<span class="fullscreen"></span><span class="sep"></span>':'')
						+'<span class="next"></span><div><table><tr></tr></table></div></div>'));
					//Navonly
					if($("img.big",figures).length==0){
						navonly = true;
						nav.css('margin-top','0px');
						figures.hide();
						slider.show();
					}
					//Ajout des vignettes
					if(o.sizefix){
						$(".mini",figure).each(function() {
							if( $(this).parent().prop("tagName") == "A" || $(this).parent().prop("tagName") == "a"){ 
								var yyy = $(this).attr("alt");
								$('tr',nav).append($('<td></td>').append($('<div class="sizefix" style="width:'+o.sizefix+'px"></div>').append($(this).parent().detach()).append('<p>'+yyy+'</p>')));
							}
							else{
								var yyy = $(this).attr("alt");
								$('tr',nav).append($('<td></td>').append($('<div class="sizefix" style="width:'+o.sizefix+'px"></div>').append($(this).detach()).append('<p>'+yyy+'</p>')));
							}
						});	
					}else{
						$(".mini",figure).each(function() {
							if( $(this).parent().prop("tagName") == "A" || $(this).parent().prop("tagName") == "a"){ 
								$('tr',nav).append($('<td></td>').append($(this).parent().detach()));
							}
							else{
								$('tr',nav).append($('<td></td>').append($(this).detach()));
							}
							$(this).attr("alt","");
						});	
					}
					//Events
					if (!navonly) $("td",nav).click(function() { go($(this).index()) });
					$(".fullscreen",nav).click(function() { fs() });
					$(".prev",nav).click(function() { navgo(currnav-1) });
					$(".next",nav).click(function() { navgo(currnav+1) });

					setTimeout(function() { 
					var x2 = Math.round($('.figures-nav table').offset().left);
					var x1 = x2-$('.figures-nav table').width()+$('.figures-nav table td:last').width()-4;
					
					$('.figures-nav table').draggable({ 
						axis: "x",
						start: function(event, ui) { },
						stop: function(event, ui) { },
						drag: function(event, ui) { },
						containment:new Array(x1,0,x2,0)
					});
					}, 2000);
				}
				/*************************************/
				/*INIT*/
				/*************************************/
                                $("img.big",figure).load(function() {
                                    // on compare avec le ratio 625*320
                                    if ((320 * $(this).width() / 625) > $(this).height()) {
                                        $(this).addClass('paysage');
                                    } else {
                                        $(this).addClass('portrait');
                                    }
				});
                                        
				$("img.big",figure).eq(0).load(function() { 
					if (loader) for (var i in bigs) if (i>0) $('img.big',figures).eq(i).attr('src',bigs[i]);
					slider.show();
					if (!o.height && !fullscreen){
						var clone = $('img.big',figure).eq(0).clone().css({'position':'absolute','z-index':0,'width':'100%','height':'auto','opacity':0});
						slider.prepend(clone);					
					}
					$(window).resize(function() { //Resize
						if (o.height || fullscreen){
							figures.height(fullscreen?$(window).height()-95:figures.width()*o.height);
							center($(".big",figure).eq(curr));
						} else figures.height(clone.height());
					}).resize();
					
					if( figure.length > 1 ){go(0);} //Start
				}).attr('src',bigs[0]);
				
				if( figure.length < 2 ){
					$(slider).find(".next, .prev, .navipuce li").off('click').hide(0);
				}
				setTimeout(function() { $(window).resize();}, 1000);
				/*************************************/
				/*FONCTIONS*/
				/*************************************/
				function center(i){//Center image
					i.css({'width':'auto','height':'auto','margin-top':'0px'});
					if (i.width()>=i.parent('figure').width())
						i.css({'width':'100%','height':'auto'});
					if (i.height()>=i.parent('figure').height())
						i.css({'width':'auto','height':'100%'});					
					if (i.height()<i.parent('figure').height())
						i.css({'margin-top':Math.round((i.parent('figure').height()-i.height())/2)+'px'});
				}
				function navgo(to) {//Navigation
					if(to<0 || to>=figure.length) return false;
					currnav = to;
					var posi = $("table",nav).position().left;
					var dest = $("td",nav).eq(currnav).position().left;
					var dist = Math.abs((Math.abs(posi)-Math.abs(dest)))*2;
					$("table",nav).stop(true,true).animate({'left':'-'+dest+"px"},dist);	
				}
				function go(to) {//Changement de slide
					/*Navipuce classe active */
					if (o.navipuce){ $(".navipuce li").toggleClass("active",false);
						if(to<$(figure).length){$(".navipuce li").eq(to).toggleClass("active",true);} else {$(".navipuce li").eq(0).toggleClass("active",true);}
					}/*Fin Navipuce*/
					if(to<0) to = figure.length-1;
					if(to>=figure.length) to = 0;				
					if(to == curr) return false;
					var fto = figure.eq(to),
						fcurr = figure.eq(curr),
						W = fto.width()+'px',
						H = fto.height()+'px',
						starts = new Array(
							{'opacity':0},
							{'left':'-'+W},
							{'left':W},
							{'top':'-'+H},
							{'top':H},
							{'top':'-'+H,'left':'-'+W},
							{'top':'-'+H,'left':W},
							{'top':H,'left':W},
							{'top':H,'left':'-'+W}),
						ends = new Array({'opacity':1,'top':'0px','left':'0px'}),
						rand = (o.fx?Math.floor(Math.random()*starts.length):0);
					//nav
					if(o.nav) $("td",nav).removeClass('active').eq(to).addClass('active');
					//z-index
					figure.stop(true,true).css({'z-index':1}).hide().eq(to).css({'z-index':3}).show();
					if (curr!=-1) fcurr.css({'z-index':2}).show();
					if (o.height || fullscreen) center(fto.children('img.big'));	
					//Start anim			
					fcurr.delay(o.speed-500).fadeOut(500);
					fto.css(starts[rand]).animate(ends[0],o.speed,'easeInOutExpo');	
					if($("figcaption").text()!=""){$("figcaption",fto).hide().delay(o.speed).fadeIn(500);}else{ $("figcaption",fto).hide().delay(o.speed).hide(0); }
					//Timer auto
					clearTimeout(timer);
					if(o.auto) timer = setInterval(function() { go(curr+1) }, o.auto);
					curr = to;
				}
				function fs() {//Fullscreen
					var o = (!$.browser.msie || parseInt($.browser.version, 10) >8)?'body':'html';
					if (fullscreen){
						slider.append($(figures).detach()).append($(nav).detach());
						$("#popn").html('').hide();
					} else {
						$('html,body').scrollTop(0);	
						$("#popn").html('').show().append($(figures).detach()).append($(nav).detach());
					}
					$(o).css({'overflow':fullscreen?'auto':'hidden'});
					fullscreen = fullscreen?false:true;	
					$(window).resize();	
				}			
				/*************************************/
			}else{
			/*** If n elements > 1 ***/	
				if(figure.length>1){
					$(figures).css({"background":"none transparent"});
					$(figure).css({"width":"47%" ,"height":"150px" ,"margin":"0 1%","display":"block","border":"1px solid #EFEFEF","background":"#FFFFFF","float":"left","text-align":"center","top":"auto","left":"auto","right":"auto","bottom":"auto","position":"relative"}).find("img").css({"width":"auto","max-width":"100%","height":"100%","display":"block","margin":"auto"});
				}else{
					$(figures).css({"background":"none transparent"});
					$(figure).css({"width":"100%","border":"1px solid #EFEFEF","background":"#FFFFFF","height":"200px" ,"display":"block","top":"auto","left":"auto","right":"auto","bottom":"auto","position":"relative"}).find("img").css({"width":"auto","width":"auto","height":"100%","max-width":"100%","display":"block"});
				}
				
				for(i=0;i<figure.length;i++){
					alt = ($(figure).eq(i).find('img').attr("alt"));
					if( typeof(alt)!='undefined' ){
						if( $(figure).eq(i).find('img').attr("alt")!='' ) {$(figure).eq(i).append('<figcaption class="special" >'+$(figure).eq(i).find('img').attr("alt")+'</figcaption>');}
					}
				}
				
			}
			
			if( figure.length < 2 ){
				setTimeout(function() { $(slider).find(".next, .prev, .navipuce li, .figures-nav").hide(); }, 500);
			}
			if(figure.length<1 ){ $(slider).css({"display":"none"}).hide(0); }else{
				for(i=0;i<figure.length;i++){
					if($(figure).eq(i).find("figcaption").length>0){ 
						if( $(figure).eq(i).find("figcaption").text() == ''){ $(figure).eq(i).find("figcaption").remove(); }
					}
				}
			}
			/*** \if n elements>2***/
			
		});
	};
})(jQuery);
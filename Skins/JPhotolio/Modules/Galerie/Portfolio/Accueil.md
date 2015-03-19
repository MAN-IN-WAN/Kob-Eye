<div id="jegbgcontainer">
	<div class="mask"></div>
	<div class="navleft"><span>&nbsp;</span></div>
	<div class="navright"><span>&nbsp;</span></div>
	
	<div id="homepita" class="hptoogle">
		<div class="triangle-border"></div>
		<div class="triangle"></div>
		<div class="torapper">Loading . . .</div>
		<div class="homeinfo">
			<div class="homeflagwrapper">
				<div class="misc-info-about"></div>
			</div>
		</div>
	</div>
	
	<div class="homeblock">
		<div class="homeblockinside">
			<div class="closeme" style="display: block;">
				<div class="icon-remove"></div>
			</div>
			
			<div class="homelink">
				<a href="#">Plus de détailq</a>
			</div>
		</div>
	</div>
	[STORPROC [!Query!]|C][/STORPROC]
	 <div class="texthome">
	 	<div class="texthome-wrapper">
		 	<h2>[!C::Nom!]</h2>
		 	<h1>[!C::Description!]</h1>
	 	</div>
	 </div>
</div>

<!-- GALERIE -->
<script type="text/javascript" src="/Skins/JPhotolio/js/jegbg.js"></script>
<script type="text/javascript">

	jQuery(document).ready(function($)
	{			
		resize_window("#jegbgcontainer");		

		/** bind jeg default **/
		$(window).jegdefault({
			curtain : 1,
			rightclick 	: 0,
			clickmsg	: "Disable Right Mouse Click"
		});

		var content = [	
			[STORPROC [!Query!]/Image|Im]
			[IF [!Pos!]>1],[/IF]
			{ "index":[!Key!],
				//[IF [!Im::Type!]=Image]
				  "type":"image",
				//[ELSE]
				//	"type":"video",
				//[/IF]
			  "source":"[JSON]/[!Im::Fichier!].limit.1600x1200.jpg[/JSON]",
			  //"source":{ "videotype":"youtube","src":"http:\/\/www.youtube.com\/watch?v=w3jTxLs7Bek"}
			  "pos":"center",					//center,top
			  "title":"[!Im::Titre!]",
			  "link":"",						//internal_link
			  "desc":"[!Im::Description!]"}						//description text html
			[/STORPROC] 			
		];

		var holddesc = undefined;
		
		var jegbg = $("#jegbgcontainer").jegbg({
			fade_speed					: 700,
			delay						: 10000,
			content 					: content,
			autostart					: true,
			partial_load				: true
		},  function(ele, media){
			$('#homepita').fadeIn(1000);				
			$('#homepita .torapper').html(ele.title);		
			$(".homelink a").attr("href" , ele.link);
			holddesc = ele.desc;

			if(!$(".homeinfo").is(":visible")) {
				pitaSlideUp();
			}
		});

		/* binding touchwipe, disable this feature if using iphone */
		if(scw(iphonewidth)) {
			$(".texthome").touchwipe({
				wipeLeft: function(e) {					
					jegbg.next();
	    			return false;
				},
				wipeRight: function() {					
					jegbg.prev();
	    			return false;
				},
				min_move_x: 20,
				min_move_y: 20,
				preventDefaultEvents: true
			});	
		}

		var pitaSlideUp = function(){
			jegbg.restart();
			$(".homeblock").slideUp("fast", function(){
				$("#homepita").animate({
					"right" : -288
				}, function(){
					$(".homeinfo").fadeIn("fast", function(){
						$(this).attr("style","").addClass("displayblock");
					});
					$(".homeblock").removeClass("homedesc");
					$(".homedescdetail").remove();
				});
			});
		};

		$(window).resize(function(){pitaSlideUp();});
		
		var pitaSlideDown = function() {
			jegbg.pause();
			$("#homepita").addClass("hptoogle");
			$(".homeblockinside").prepend("<div class='homedescdetail'>" + holddesc + "</div>");
			$(".homeblock").slideDown("fast", function(){
				$(this).addClass("homedesc");
				$(".homedescdetail").css({
					height 	: $('.homeblockinside').height() - 30,
    				width 	: $('.homeblockinside').width() - 12
				});
				jpanel = $(".homedescdetail").jScrollPane().data().jsp;				
			});
		};
		
		$(".homeinfo").click(function(){
			$(this).fadeOut("fast");
			$("#homepita").animate({
				"right" : -5
			}, function(){
				pitaSlideDown();
			});
		});
		
		$(".homeblock .closeme, .torapper").click(function(){
			pitaSlideUp();
		});
	});
</script>	




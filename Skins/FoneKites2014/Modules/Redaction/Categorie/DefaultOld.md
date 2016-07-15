[INFO [!Query!]|I]
[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[!Req:=[!Query!]!]
[COUNT [!Req!]/Article/Publier=1|NbArt]
[!More:=4!]
<div class="last-news">
	<div class="container">
		<h1>Informations</h1>
		<div id="fone">
			[MODULE Redaction/Article/LoadArticle?Req=[!Req!]&Limit=[!More!]&Offset=0]
			<div class="fone-item item-large element _SOCIAL-MEDIAS">
				<div class="SocialHome">
					<iframe id="facebookIframe" src="http://www.facebook.com/plugins/likebox.php?href=https%3A%2F%2Fwww.facebook.com%2FF.oneInternational&width=551&height=803&colorscheme=light&show_faces=false&header=true&stream=true&show_border=true" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:100%; height:803px;" allowTransparency="true"></iframe>
					<div class="reseau-1">
						<h2>FACEBOOK</h2>
						<h3>F-ONE</h3>
					</div>
				</div>
			</div>
			<div class="fone-item item-large element _SOCIAL-MEDIAS">
				<div class="SocialHome">
					<a class="twitter-timeline" href="https://twitter.com/F_ONEKITES" data-widget-id="380282114614116352">Tweets de @F_ONEKITES</a>
					<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
					<div class="reseau-2">
						<h2>TWITTER</h2>
						<h3>F-ONE<br></h3>
					</div>
				</div>
				
			</div>
		</div> 
	</div>
</div>  
[IF [!NbArt!]>4]
<div class="container">
	<div class="load-more">
		<a href="#nogo"  class="btn-more-Media btn-primary" id="LoadMore" data-url="/Redaction/Article/LoadArticle.htm" data-max="[!NbArt!]" data-more="[!More!]" data-current="[!More!]">LOAD MORE INFOS</a>
	</div> 
</div>
[/IF]

<script type="text/javascript">
	$(document).ready(function(){
		/**
		 * ISOTOPE + MASONRY
		 */
		$('#fone').isotope({
			
	  		// options
			[IF [!I::TypeSearch!]=Direct]
			 	filter: '.[!Cat::Url!]'
			[/IF]
		});
		function refreshIsotope(newElements) {
			$('#fone').isotope({
				
		  		// options
				[IF [!I::TypeSearch!]=Direct]
				 	filter: '.[!Cat::Url!]'
				[/IF]
			});
			$('#fone').imagesLoaded( function(){
				$('#fone').isotope({
					
			  		// options
					[IF [!I::TypeSearch!]=Direct]
					 	filter: '.[!Cat::Url!]'
					[/IF]
				});
			});	
		}
		
		$(window).smartresize(function(){
			refreshIsotope();
		});
		
		// trigger Isotope after images have loaded
		$('#fone').imagesLoaded( function(){
			refreshIsotope();
		});	
		$('.filters a.filter').click(function(){
			$('.filters a.filter.filteractive').removeClass('filteractive');
			$('.filters li.active').removeClass('active');
			var selector = $(this).attr('data-filter');
			$('#fone').isotope({ filter: selector });
			$('a[data-filter="'+selector+'"]').addClass('filteractive');
			return false;
		});
		/**
		 * LOAD MORE
		 */
		$('#LoadMore').on('click',function() {
			//affichage indicateur de chargement
			//lancement du chargement ajax
			var sel = this;
			$.ajax({
				url: $(this).attr('data-url')+'?Offset='+(parseInt($(this).attr('data-current'))-1)+'&Limit='+(parseInt($(this).attr('data-more'))),
				success: function (data) { 
					$('#fone').append($(data)).isotope('reloadItems');
					refreshIsotope(data);
					//mise à jour du total
					$(sel).attr('data-current',parseInt($(sel).attr('data-current'))+parseInt($(sel).attr('data-more')));
					//suppressio ndu bouton load more si tout est chargé
					if (parseInt($(sel).attr('data-current'))>=parseInt($(sel).attr('data-max'))){
						$('#LoadMore').css('display','none');
					}
				},
				dataType: 'html'
			});
			return false;
		});
		
	});
</script>


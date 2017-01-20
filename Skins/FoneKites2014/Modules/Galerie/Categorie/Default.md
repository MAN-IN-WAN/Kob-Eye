[INFO [!Query!]|I]
[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[!Req:=Galerie/Media/Display=1!]
[COUNT [!Req!]|NbMed]
[!More:=4!]
[IF [!I::TypeSearch!]=Direct]
	[!Req:=Galerie/Categorie/[!Cat::Url!]/Media/Display=1!];
[/IF]

<div class="last-news">
	<div class="container nopadding-left nopadding-right">
		<h1>Medias</h1>
		<div id="fone">
			[MODULE Galerie/Media/LoadMedia?Req=[!Req!]&Limit=[!More!]&Offset=0]
		</div>
	</div>
</div>  
[IF [!NbMed!]>4]
	<div class="container nopadding-left nopadding-right">
		<div class="load-more">
			<a href="#nogo"  class="btn-more-Media btn-primary" id="LoadMore" data-url="/Galerie/Media/LoadMedia.htm" data-max="[!NbMed!]" data-more="[!More!]" data-current="[!More!]">LOAD MORE MEDIAS</a>
		</div> 
	</div>
[/IF]

<script type="text/javascript">
	$(document).ready(function(){
		//definition des variables
		[IF [!I::TypeSearch!]=Direct]
			var currentfilter = '[!Cat::Url!]';
		[ELSE]
			var currentfilter = 'all';
		[/IF]
		var datamax = [!NbMed!];
		/**
		 * ISOTOPE + MASONRY
		 */
		$('#fone').isotope({
		 	filter: '.'+currentfilter
		});
		function refreshIsotope(newElements) {
			$('#fone').isotope({
			 	filter: '.'+currentfilter
			});
			$('#fone').imagesLoaded( function(){
				$('#fone').isotope({
				 	filter: '.'+currentfilter
				});
			});	
		}
		
		$(window).on("debouncedresize", function( event ) {
			refreshIsotope();
		});
		
		// trigger Isotope after images have loaded
		$('#fone').imagesLoaded( function(){
			refreshIsotope();
		});	
		$('.filters a.filter').click(function(){
			$('#LoadMore').css('display','inline');
			$('.filters a.filter.filteractive').removeClass('filteractive');
			$('.filters li.active').removeClass('active');
			var selector = $(this).attr('data-filter');
			//filtre courrant
			currentfilter = selector.substring(1);
			$('#fone').isotope({ filter: selector });
			$('a[data-filter="'+selector+'"]').addClass('filteractive');
			//envoi de la requete de la catégorie
			loadMore(true);
			return false;
		});
		/**
		 * LOAD MORE
		 */
		function loadMore(reset) {
			//affichage indicateur de chargement
			var offset=0;
			var limit = 4;
			var url = "/Galerie/Media/LoadMedia.htm";
			var req = "Media/Display=1";
			var module = "Galerie/";
			if (currentfilter!="all") {
				req = module+'Categorie/'+currentfilter+'/'+req;
				offset = $('#fone .fone-item.'+currentfilter).size();
			}else{
				offset = $('#fone .fone-item').size();
				req = module+req;
			}
			if (reset){
				offset=0;
				$('#fone').empty();
			}
			//lancement du chargement ajax
			$.ajax({
				url: url+'?Offset='+offset+'&Limit='+limit+'&Req='+req,
				success: function (data) {
					if (!data.length)
						$('#LoadMore').css('display','none');
					$('#fone').append($(data)).isotope('reloadItems');
					refreshIsotope(data);
					//on compte le nombre d'elements
					if (currentfilter!="all") {
						var nb = $('#fone .fone-item.'+currentfilter).size();
					}else var nb = $('#fone .fone-item').size();
					var nbmax =  $('#fone .fone-item').attr('max-item');
					//on compare avec le nombre total avec le nombre affiché
					if (nb<nbmax){
						$('#LoadMore').css('display','inline');
					}else $('#LoadMore').css('display','none');
				},
				dataType: 'html'
			});
			return false;
		}
		$('#LoadMore').on('click',function() {
			loadMore(false);
		});
	});
</script>
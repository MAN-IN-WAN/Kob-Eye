[INFO [!Query!]|I]
[STORPROC [!Query!]|Cat|0|1][/STORPROC]
[!Req:=Blog/Post/Display=1!]
[!More:=4!]
[IF [!I::TypeSearch!]=Direct]
	[!Req:=Blog/Categorie/[!Cat::Url!]/Post/Display=1!];
[/IF]
[COUNT [!Req!]|NbPost]

<div class="last-news">
	<div class="container nopadding-right nopadding-left">
		<div id="fone">
			[MODULE Blog/Post/LoadPost?Req=[!Req!]&Limit=[!More!]&Offset=0]
		</div> 
	</div>
</div>  

<div class="container nopadding-right nopadding-left">
	<div class="load-more">
		<a href="#nogo" class="btn-more-Media btn-primary" id="LoadMore" data-url="/Blog/Post/LoadPost.htm" data-max="[!NbPost!]" data-more="[!More!]" data-current="[!More!]" [IF [!NbPost!]<=[!More!]]style="display:none;"[/IF]>LOAD MORE NEWS</a>
	</div> 
</div>
[COMPONENT Systeme/Bootstrap.Social/?NOMDIV=resociaux&TITRE=__LATEST_NEWS__]
<script type="text/javascript">
	$(document).ready(function(){
		//définition des variables
		[IF [!I::TypeSearch!]=Direct]
			var currentfilter = '[!Cat::Url!]';
		[ELSE]
			var currentfilter = 'all';
		[/IF]
		var datamax = [!NbPost!];
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
					
			  		// options
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
			//chargement des posts
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
			var url = "/Blog/Post/LoadPost.htm";
			var req = "Post/Display=1";
			var module = "Blog/";
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
					$('#fone').append($(data)).isotope('reloadItems');
					refreshIsotope(data);
					//on compte le nombre d'elements
					if (currentfilter!="all") {
						var nb = $('#fone .fone-item.'+currentfilter).size();
					}else var nb = $('#fone .fone-item').size();
					var nbmax =  $('#fone .fone-item').attr('max-item');
					console.log(nb +' < '+nbmax);
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


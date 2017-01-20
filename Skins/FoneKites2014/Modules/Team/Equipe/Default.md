[INFO [!Query!]|I]
//Recherche du menu racine
[STORPROC [!Query!]|Cat][/STORPROC]

<div class="last-news">
	<div class="container nopadding-right nopadding-left">
		<div id="fone">
	 		[STORPROC Team/Rider/Display=1|Rid]
				[STORPROC Team/Equipe/Rider/[!Rid::Id!]|CatR|0|1] [/STORPROC]
				<div class="fone-item item-normal element [!CatR::Url!] [IF [!Rid::ShowAllDisplay!]]all[/IF]">
					<div class="team">
						<div class="produits-inner">
							[IF [!Rid::Avatar!]!=]
								<a href="/[!Systeme::CurrentMenu::Url!]/[!CatR::Url!]/Rider/[!Rid::Url!]" >
									<img class="img-responsive" src="/[!Rid::Avatar!].mini.290x250.jpg" alt="[!Rid::Nom!]" />
								</a>
							[ELSE]
								[STORPROC Team/Rider/[!Rid::Id!]/Photo|Do|0|1]
									<a href="/[!Systeme::CurrentMenu::Url!]/[!CatR::Url!]/Rider/[!Rid::Url!]" class="thumbnail">
										<img class="img-responsive" src="/[!Do::Image!].mini.290x250.jpg" alt="[!Rid::Nom!]" />
									</a>
									[NORESULT]
										[IF [!Rid::Fond!]!=]
											<a href="/[!Lien!]/Rider/[!Rid::Url!]" class="thumbnail" ><img class="img-responsive" src="/[!Rid::Fond!].mini.260x290.jpg" alt="[!Rid::Nom!]"/></a>
										[/IF]
									[/NORESULT]
								[/STORPROC]
							[/IF]
							<div class="[!CatR::Couleur!]">
								<h4><a href="/[!Systeme::CurrentMenu::Url!]/[!CatR::Url!]/Rider/[!Rid::Url!]" >[!Rid::Prenom!]</a></h4>
								<h3><a href="/[!Systeme::CurrentMenu::Url!]/[!CatR::Url!]/Rider/[!Rid::Url!]" >[!Rid::Nom!]</a></h3>
							</div>
						</div>
					</div>
				</div>
			[/STORPROC]
	    	</div>   
	</div>
</div>



<script type="text/javascript">
	$(document).ready(function(){
		$('#fone').isotope({
	  		// options
			[IF [!I::TypeSearch!]=Direct]
			 	filter: '.[!Cat::Url!]'
			[ELSE]
			 	filter: '.all'
			[/IF]
		});
		// trigger Isotope after images have loaded
		$('#fone').imagesLoaded( function(){
		    	$('#fone').isotope({
	  			// options
				[IF [!I::TypeSearch!]=Direct]
				 	filter: '.[!Cat::Url!]'
				[ELSE]
					filter: '.all'
				[/IF]
			});
		});	
		$(window).on("debouncedresize", function( event ) {
		    	$('#fone').isotope({
	  			// options
				[IF [!I::TypeSearch!]=Direct]
				 	filter: '.[!Cat::Url!]'
				[ELSE]
					filter: '.all'
				[/IF]
			});
		});
		$('.filters a.filter').click(function(){
			$('.filters a.filter.filteractive').removeClass('filteractive');
			$('.filters li.active').removeClass('active');
			var selector = $(this).attr('data-filter');
			$('#fone').isotope({ filter: selector });
			$('a[data-filter="'+selector+'"]').addClass('filteractive');
			return false;
		});
	});
</script>

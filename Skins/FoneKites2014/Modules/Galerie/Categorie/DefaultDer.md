[INFO [!Query!]|I]
[STORPROC [!Query!]|Cat][/STORPROC]
[IF [!More!]][!NbLimitA+=[!More!]!][ELSE][!NbLimitA:=20!][/IF]

[!Req:=Galerie!]

<div class="last-news">
	<div class="container">
		<h1>Medias</h1>
		<div id="fone">
	 		[STORPROC [!Req!]/Media|Med|0|[!NbLimitA!]|tmsCreate|DESC]
				[STORPROC Galerie/Categorie/Media/[!Med::Id!]|CatM][/STORPROC]
				<div class="fone-item item-large element [!CatM::Url!]">		
					[SWITCH [!Legend!]|=]
						[CASE ][!Legend:=legend-blue!][/CASE]
						[CASE legend-blue][!Legend:=legend-green!][/CASE]
						[CASE legend-green][!Legend:=legend-red!][/CASE]
						[CASE legend-red][!Legend:=legend-bgris!][/CASE]
						[CASE legend-bgris][!Legend:=legend-orange!][/CASE]
						[CASE legend-orange][!Legend:=legend-blue!][/CASE]
					[/SWITCH]		
					<div class="category">
						<div class="cat-bloc">
							<a href="#" data-filter=".[!CatM::Url!]">
								Media | [!CatM::Titre!]
							</a>
						</div>
					</div>
					<div class="produits">
						[IF [!Med::Fichier!]!=]
							//[IF [!Med::FichierHD!]!=]<a href="/[!Med::FichierHD!]">[/IF]
							//<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!CatM::Url!]">
								<img class="img-responsive" src="/[!Med::Fichier!]" alt="[!Med::Titre!]"/>
							//</a>
							//[IF [!Med::FichierHD!]!=]</a>[/IF]
						[/IF]
						//<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!CatM::Url!]">
							<div class="[!Legend!]">
								<h2>		
									[!Med::Titre!]
								</h2>
								<h3>
									[!Med::Chapo!]
								</h3>
							</div>
						//</a>
					</div>
				</div>
			[/STORPROC]
		</div>
	</div>
</div>  
//<div class="container">
//	<div class="load-more">
//		[!More+=4!]
//		<a href="/[!Lien!]?More=[!More!]"  class="btn-more-Media btn-primary">LOAD MORE MEDIAS</a>
//	</div> 
//</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#fone').isotope({
	  		// options
			[IF [!I::TypeSearch!]=Direct]
			 	filter: '.[!Cat::Url!]'
			[/IF]
		});
		// trigger Isotope after images have loaded
		$('#fone').imagesLoaded( function(){
		    	$('#fone').isotope({
	  			// options
				[IF [!I::TypeSearch!]=Direct]
				 	filter: '.[!Cat::Url!]'
				[/IF]
			});
		});	
		$('.filters a.filter').click(function(){
			$('.filters a.filter.filteractive').removeClass('filteractive');
			var selector = $(this).attr('data-filter');
			$('#fone').isotope({ filter: selector });
			$('a[data-filter="'+selector+'"]').addClass('filteractive');
			return false;
		});
	});
</script>

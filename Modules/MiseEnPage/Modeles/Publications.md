[STORPROC [!Query!]|Cat]
	[!CateEncours:=[!Cat::Id!]!]
[/STORPROC]
[!Req:=[!Query!]/Categorie/Publier=1!]
[STORPROC [!Query!]/Categorie/Publier=1|Cat]
	[NORESULT]
		[STORPROC [!Query!]|Cat]
			[STORPROC MiseEnPage/Categorie/Categorie/[!Cat::Id!]|SCat]
				[!Req:=MiseEnPage/Categorie/[!SCat::Id!]/Categorie/Publier=1!]	
			[/STORPROC]			
		[/STORPROC]			
	[/NORESULT]
[/STORPROC]
<div class="Publications row noMargin" >
	<div class="Filtres">
		[STORPROC [!Req!]|Cat]
			<div class="unecateg [!Cat::Url!] [IF [!Cat::Id!]=[!CateEncours!]]active[/IF]" >
				<a href="/[!Cat::getUrl()!]" data-filter=".f_[!Cat::Id!]">
					[IF [!Cat::Icone!]!=]
						<img src="[!Domaine!]/[!Cat::Icone!]" alt="[!Cat::Nom!]" title="[!Cat::Nom!]" class="icoFiltre">
					[ELSE]
						[!Cat::Nom!]

					[/IF]
				</a>
				[NORESULT]
					[STORPROC [!Query!]|Cat]
						[STORPROC MiseEnPage/Categorie/Categorie/[!Cat::Id!]|SCat]
							
						[/STORPROC]			
						
					[/STORPROC]			

				[/NORESULT]
			</div>
		[/STORPROC]
	</div>
	
	<div id="listeArticles" class="col-md-12">
			[IF [!Query!]~Article]
				// Prévoir ici l'affichage de l'article seul et en entier
			[ELSE]
				[STORPROC [!Query!]/Categorie/*/Article/Publier=1&ALaUne=1|Art|0|9|tmsCreate|DESC]
					[STORPROC MiseEnPage/Categorie/Article/[!Art::Id!]|ArtCat|0|1][/STORPROC]
					<div class="col-md-4 article f_[!ArtCat::Id!]">
						<div class="titre">
							<img src="[!Domaine!]/[!ArtCat::Icone!]" alt="[!ArtCat::Nom!]" title="[!ArtCat::Nom!]" class="icoCatArticle">
							<h3 class="titleCatArticle"> [!Art::Titre!]</h3>
						</div>
						<div class="quandqui">le [DATE d.m.Y][!Art::Date!][/DATE]</div>
						// Recherche d'une image
						[STORPROC MiseEnPage/Article/[!Art::Id!]/Contenu|ArtCont|0|1]
							[STORPROC MiseEnPage/Contenu/[!ArtCont::Id!]/Colonne|ACCol|0|1]
								[STORPROC MiseEnPage/Colonne/[!ACCol::Id!]/Image|ACImg|0|1]
									<div class="imageblog">
										<img src="[!Domaine!]/[!ACImg::URL!]" alt="[!ACImg::Alt!]" title="[!ACImg::Title!]" class="img-responsive">
									</div>			
								[/STORPROC]
							[/STORPROC]
						[/STORPROC]
						<div class="chapo">
							[!Art::Chapo!]
						</div>
						<div class="LienArticle">
							<a href="/[!Art::getUrl()!]" alt="Lire [!Art::Titre!]" >Lire l'article</a>
						</div>
					</div>
				[/STORPROC]
				<div id="emptyFilter" style="display:none;">
					Aucun article ne correspond à vos critères.
				</div>
			[/IF]
	</div>
</div>

<script type="text/javascript">
	function noResultsCheck() {
		var numItems = $('.article:visible').length;
		console.log(numItems);
		if (!numItems) {
		    $('#emptyFilter').show('slow');
		}
	}
	
	setTimeout(function(){
		var iso =$('#listeArticles').isotope({
			// options
			itemSelector: '.article',
			layoutMode: 'masonry'
		});
		$('.Filtres').on('click','a',function(e){
			e.preventDefault();
			e.stopPropagation();
			$('#emptyFilter').hide('slow');
			var filterValue = $(this).attr('data-filter');
			iso.isotope({ filter: filterValue });
		});
		var showall ='<div class="unecateg" > \
					<a href="#" data-filter="*"> \
						<img src="[!Domaine!]/Skins/[!Sys::Skin!]/Images/tout.png" alt="Tout voir" title="Tout voir" class="icoFiltre"> \
					</a> \
				</div>';
		$('.Filtres').append(showall);
		
		$('#listeArticles').on('arrangeComplete', noResultsCheck );
	},500);
</script>
 
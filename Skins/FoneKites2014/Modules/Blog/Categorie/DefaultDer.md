
[COUNT [!Query!]|NbNiv]
[STORPROC [!Query!]|Cat][/STORPROC]

[!Req:=Blog!]


<div class="last-news">
	<div class="container">
		<h1>Last News</h1>
		<div id="fone">
 			[!Cpt:=0!]
	 		[STORPROC [!Req!]/Post|Po|||tmsCreate|DESC]
				[STORPROC Blog/Categorie/Post/[!Po::Id!]|CatP][/STORPROC]
				<div class="fone-item item-large element [!CatP::Url!]">
					<div class="category">
						<div class="cat-bloc">
							<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]">
								NEWS | [!CatP::Titre!]
							</a>
						</div>
					</div>
					<div class="produits ">
						[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Video|Do|0|1]
							<div class="Post-Aff">
								//<iframe width="auto" height="auto" src="[!Domaine!]/[!Do::Fichier!]" frameborder="0" ></iframe>
								[NORESULT]
									[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
										<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.570x350.jpg" alt="[!Do::Titre!]"/></a>
									[/STORPROC]
								[/NORESULT]
							</div>
						[/STORPROC]
						<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]/Post/[!Po::Url!]"><div class="BlocCouleur BlocCouleur-[!CatP::Couleur!]">
							<h2>[!Po::Titre!]</h2>
							<h3>[!Po::Chapo!]</h3>
						</div></a>
						<div class="teaser-blog">
							<div class="teaser">
								<div class="texteaser" [IF [!HAUTEURBLOCTEXTE!]!=] style="height:290px;"[/IF]> 
									[!Po::Contenu!]
								</div>
								<div class="teaser-info">
									<div class="date">[DATE d/m/Y][!Po::Date!][/DATE]</div>
									<div class="more-BlocCouleur-[!CatP::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]/Post/[!Po::Url!]">MORE DETAILS</a></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			[/STORPROC]
		</div> 
	</div>
</div>  
<div class="container">
	<div class="load-more">
		[!More+=4!]
//		<a href="/[!Lien!]?More=[!More!]"  class="btn-more-Media btn-primary">LOAD MORE MEDIAS</a>
	</div> 
</div>

<script type="text/javascript">
	$('#fone').isotope({
  		// options
		[IF [!NbNiv!]=1]
		 	filter: '.[!CatP::Url!]'
		[/IF]
	});

	$('#filters a').click(function(){
		$('#filters a.filteractive').removeClass('filteractive');
		var selector = $(this).attr('data-filter');
		$('#fone').isotope({ filter: selector });
		$(this).addClass('filteractive');
		return false;
	});


</script>

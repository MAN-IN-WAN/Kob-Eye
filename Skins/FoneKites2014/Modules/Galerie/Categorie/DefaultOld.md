[COUNT [!Query!]|NbCat]

[IF [!More!]][!NbLimitA+=[!More!]!][ELSE][!NbLimitA:=4!][/IF]
[IF [!NbCat!]>1]
	[!NbLimitA:=2!]
	[!Req:=[!Query!]/*/Publier=1!]
[ELSE]
	[!Req:=[!Query!]!]
[/IF]

<div class="container contop">

	[STORPROC [!Req!]|Cat]
 		[COUNT Galerie/Categorie/[!Cat::Id!]/Media|NbMed]
		[IF [!NbMed!]]
			[STORPROC Galerie/Categorie/[!Cat::Id!]/Media|Med|0|[!NbLimitA!]|tmsCreate|DESC]
				[SWITCH [!Legend!]|=]
					[CASE ][!Legend:=legend-blue!][/CASE]
					[CASE legend-blue][!Legend:=legend-green!][/CASE]
					[CASE legend-green][!Legend:=legend-red!][/CASE]
					[CASE legend-red][!Legend:=legend-bgris!][/CASE]
					[CASE legend-bgris][!Legend:=legend-orange!][/CASE]
					[CASE legend-orange][!Legend:=legend-blue!][/CASE]
				[/SWITCH]		
				<div class="col-lg-6 col-sm-6 col-xs-12 ">
					<div class="category">
						<div class="cat-bloc">
							<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!Cat::Url!]">
								Media | [!Cat::Titre!]
							</a>
						</div>
					</div>
					<div class="produits">
						[IF [!Med::Fichier!]!=]
							[IF [!Med::FichierHD!]!=]<a href="/[!Med::FichierHD!]">[/IF]
							<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!Cat::Url!]"><img class="img-responsive" src="/[!Med::Fichier!]" alt="[!Do::Titre!]"/></a>
							[IF [!Med::FichierHD!]!=]</a>[/IF]
						[/IF]
						<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!Cat::Url!]">
							<div class="[!Legend!]">
								<h2>		
									[!Med::Titre!]
								</h2>
								<h3>
									[!Med::Chapo!]
								</h3>
							</div>
						</a>
					</div>
				</div>
			[/STORPROC]
		[/IF]
	[/STORPROC]
</div>  
<div class="container">
	<div class="load-more">
		[!More+=4!]
		<a href="/[!Lien!]?More=[!More!]" class="btn-more-Media btn-primary">LOAD MORE MEDIAS</a>
	</div> 
</div>

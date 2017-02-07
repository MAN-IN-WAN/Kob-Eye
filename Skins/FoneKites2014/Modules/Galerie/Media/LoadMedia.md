[IF [!Req!]=][!Req:=Galerie/Media/Display=1!][/IF]
[COUNT [!Req!]|NB]
[STORPROC [!Req!]|Med|[!Offset!]|[!Limit!]|Date|DESC]
	[STORPROC Galerie/Categorie/Media/[!Med::Id!]|CatM][/STORPROC]
	<div class="fone-item item-large element [!CatM::Url!] all" max-item="[!NB!]">
		<div class="galerie">
			<div class="category">
				<div class="cat-bloc">
					<a href="#" data-filter=".[!CatM::Url!]">
						Media | [!CatM::Titre!]
					</a>
				</div>
			</div>
			<div class="produits-inner ">
				<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!CatM::Url!]/Media/[!Med::Url!]">
					<img class="img-responsive" src="/[!Med::Fichier!].mini.573x350.jpg" alt="[!Do::Titre!]"/>
				</a>
				<div class="BlocCouleur BlocCouleur-[!CatM::Couleur!]">	
					<a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!CatM::Url!]/Media/[!Med::Url!]">			
						<h2>[!Med::Titre!]</h2>
						<h3>[!Med::Chapo!]</h3>
					</a>
				</div>
				<div class="teaser-blog">
					<div class="teaser">
						<div class="texteaser" [IF [!HAUTEURBLOCTEXTE!]!=] style="height:290px;"[/IF]> 
							[SUBSTR 350|[...]][!Med::Description!][/SUBSTR]
						</div>
						<div class="teaser-info">
							<div class="date" style="font-size:22px; font-weight:100;">[DATE d/m/Y][!Med::Date!][/DATE]</div>
							<div class="more-BlocCouleur-[!CatM::Couleur!]"><a href="/[!Systeme::getMenu(Galerie/Categorie)!]/[!CatM::Url!]/Media/[!Med::Url!]">MORE DETAILS</a></div>
						</div>
					</div>
				</div>
	
			</div>
		</div>
	</div>
[/STORPROC]

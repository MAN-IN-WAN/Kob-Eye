[IF [!Req!]=][!Req:=Blog/Post/Display=1!][/IF]
[COUNT [!Req!]|Nb]
[STORPROC [!Req!]|Po|[!Offset!]|[!Limit!]|Date|DESC]
	[STORPROC Blog/Categorie/Post/[!Po::Id!]|CatP][/STORPROC]
	<div class="fone-item item-large element [!CatP::Url!] all" max-item="[!Nb!]">
		<div class="blog">
			<div class="category">
				<div class="cat-bloc">
					<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]">
						NEWS | [!CatP::Titre!] 
					</a>
				</div>
			</div>
			<div class="produits-inner ">
				//[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Video|Do|0|1]
				//	<div class="Post-Aff">
						//<iframe width="auto" height="auto" src="[!Domaine!]/[!Do::Fichier!]" frameborder="0" ></iframe>
						//[NORESULT]
							//[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
								<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Po::Fichier!].mini.570x350.jpg" alt="[!Do::Titre!]"/></a>
							//[/STORPROC]
						//[/NORESULT]
				//	</div>
				//[/STORPROC]
				<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]/Post/[!Po::Url!]"><div class="BlocCouleur BlocCouleur-[!CatP::Couleur!]">
					<h2>[!Po::Titre!]</h2>
					<h3>[!Po::Chapo!]</h3>
				</div></a>
				<div class="teaser-blog">
					<div class="teaser">
						<div class="texteaser"> 
							[SUBSTR 250][!Po::Resume!][/SUBSTR]
							//[!Po::Contenu!]
						</div>
						<div class="teaser-info">
							<div class="date" style="font-size:20px; font-weight:100;">[DATE d/m/Y][!Po::Date!][/DATE]</div>
							<div class="more-BlocCouleur-[!CatP::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!CatP::Url!]/Post/[!Po::Url!]">MORE DETAILS</a></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
[/STORPROC]

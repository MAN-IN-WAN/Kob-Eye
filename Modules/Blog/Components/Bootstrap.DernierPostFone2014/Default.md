[!Req:=Blog/Post/Publier=1!]
[IF [!BLOGCATEGORIE!]!=]
	[!Req:=Blog/Post/Display=1!]
	[!Req2:=Blog/Post!]
[/IF]
<div class="[!NOMDIV!]">
   	<div class="container nopadding-left nopadding-right">
   		<div class="reseau">
 			<h1><a href="/[!Systeme::getMenu([!LIENTITRE!])!]">[!TITRE!]</a></h1>    
			<div class="col-lg-6 col-sm-6 col-xs-12 nopadding-left">
				<div id="myCarouselBlog1" class="carousel slide vertical">  
					<div class="vertical carousel-inner ">
						[STORPROC [!Req!]|Po|0|[!NBBLOGCOL:*2!]|Date|DESC]
							[IF [!Utils::isPair([!Pos!])!]=]
								[STORPROC Blog/Categorie/Post/[!Po::Id!]|Cat|0|1][/STORPROC]
								<div class="[IF [!Pos!]=1]active [/IF]item">
									<div class="blog">
										<div class="category">
											<div class="cat-bloc"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]">NEWS | [!Cat::Titre!]</a></div>
										</div>
										<div class="produits-inner">
											//[STORPROC [!Req2!]/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
											//	<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Do::Titre!]"/></a>
											//[/STORPROC]
											<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Po::Fichier!].mini.570x350.jpg" alt="[!Do::Titre!]"/></a>
											<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
												<h2>[!Po::Titre!]</h2>
												<h3>[!Po::Chapo!]</h3>
											</div>
											<div class="teaser">
												<div class="texteaser"> 
													[SUBSTR 200|...][!Po::Contenu!][/SUBSTR]
												</div>
												<div class="teaser-info">
													<div class="date">[DATE d/m/Y][!Po::Date!][/DATE]</div>
													<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">__MORE_DETAILS__</a></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							[/IF]
						[/STORPROC]
					</div>
				</div>
				<div class="nav-prod">
					<a class="next" href="#myCarouselBlog1" data-slide="next"></a>
				</div>
			</div>
			<div class="col-lg-6 col-sm-6 col-xs-12 hidden-xs nopadding-right">
				<div id="myCarouselBlog2" class="carousel slide vertical">  
					<div class="vertical carousel-inner ">
						[!First:=1!]
						[STORPROC [!Req!]|Po2|0|[!NBBLOGCOL:*2!]|Date|DESC]
							[IF [!Utils::isPair([!Pos!])!]]
								[STORPROC Blog/Categorie/Post/[!Po2::Id!]|Cat|0|1][/STORPROC]
								<div class="[IF [!First!]=1]active [!First:=0!][/IF]item">
									<div class="blog">
										<div class="category">
											<div class="cat-bloc"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]">NEWS | [!Cat::Titre!]</a></div>
										</div>
										<div class="produits-inner">
											//[STORPROC [!Req2!]/[!Po2::Id!]/Donnees/Type=Image|Do|0|1]
											//	<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Do::Titre!]"/></a>
											//[/STORPROC]
											<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po2::Url!]"><img class="img-responsive" src="/[!Po2::Fichier!].mini.570x350.jpg" alt="[!Do::Titre!]"/></a>
											<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
												<h2>[!Po2::Titre!]</h2>
												<h3>[!Po2::Chapo!]</h3>
											</div>
											<div class="teaser">
												<div class="texteaser" > 
													[SUBSTR 200|...][!Po2::Contenu!][/SUBSTR]
												</div>
												<div class="teaser-info">
													<div class="date">[DATE d/m/Y][!Po2::Date!][/DATE]</div>
													<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po2::Url!]">__MORE_DETAILS__</a></div>
												</div>
											</div>
										</div>
									</div>
								</div>
							[/IF]
						[/STORPROC]
					</div>
				</div>
				<div class="nav-prod">
					<a class="next" href="#myCarouselBlog2" data-slide="next"></a>
				</div>
			</div>

		</div>
	</div> 
</div>
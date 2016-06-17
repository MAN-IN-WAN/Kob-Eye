<div class="[!NOMDIV!]">
	<div class="container nopadding-left nopadding-right">
		<h1><a href="/[!Systeme::getMenu([!LIENTITRE!])!]">[!TITRE!]</a></h1>    
		<!-- Carousel items --> 
		<div class="col-lg-6 col-md-6 col-xs-12 nopadding-left extra-padding" style="padding-right:10px;">
			<div id="myCarousel2" class="carousel slide vertical">
				<div class="vertical carousel-inner ">
					[STORPROC Products/Categorie/ALaUne=1|CatP|0|1|Ordre|ASC]
						[STORPROC Products/Categorie/[!CatP::Id!]/Produit|P|0|[!NBINFOS!]]
							<div class="item [IF [!Pos!]=1]active[/IF]">
								<div class="produits">
									<div class="produits-inner">
										<a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]/Produit/[!P::Url!]">
											<img class="img-responsive" src="[!Domaine!]/[!P::ProduitGrandFormat!].mini.590x546.jpg" alt="[!P::Nom!]"/>
										</a>
										<div class="[!CatP::Couleur!]">
											<h2><a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]">[!CatP::Nom!]</a></h2>
											<h3><a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]/Produit/[!P::Url!]">[!P::Nom!]</a></h3>
										</div>
									</div>
								</div>
							</div>
						[/STORPROC]
					[/STORPROC]
					<!-- Carousel nav -->
				</div>
				<div class="nav-prod">
					<a class="next" href="#myCarousel2" data-slide="next"></a>
				</div>
			</div>
		</div>
		<!-- Carousel items --> 
		<div class="col-lg-3-3 col-md-3 col-xs-6 hidden-xs " style="padding-right:5px;padding-left:5px;">
			<div id="myCarousel3" class="carousel slide vertical">
				<div class="vertical carousel-inner ">
					[STORPROC Products/Categorie/ALaUne=1|CatP|1|1|Ordre|ASC]
						[STORPROC Products/Categorie/[!CatP::Id!]/Produit|P|0|[!NBINFOS!]|Ordre|ASC]
							<div class="item  [IF [!Pos!]=1]active[/IF]">
								<div class="produits">
									<div class="produits-inner">
										<a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]/Produit/[!P::Url!]">
											<img class="img-responsive" src="[!Domaine!]/[!P::ProduitGrandFormat!].mini.290x590.jpg" alt="[!P::Nom!]"/>
										</a>
										<div class="[!CatP::Couleur!]">
											<h2><a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]">[!CatP::Nom!]</a></h2>
											<h3><a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]/Produit/[!P::Url!]">[!P::Nom!]</a></h3>
										</div>
									</div>
								</div>
							</div>
						[/STORPROC]
					[/STORPROC]
				</div>
				<div class="nav-prod">
					<a class="next" href="#myCarousel3" data-slide="next"></a>
				</div>
			</div>
		</div>
		<!-- Carousel items -->
		<div class="col-lg-3-3 col-md-3 col-xs-6 hidden-xs  nopadding-right" style="padding-left:10px">
			<div id="myCarousel4" class="carousel slide vertical">
				<div class="vertical carousel-inner ">
					[STORPROC Products/Categorie/ALaUne=1|CatP|2|1|Ordre|ASC]
						[STORPROC Products/Categorie/[!CatP::Id!]/Produit|P|0|[!NBINFOS!]|Ordre|ASC]
							<div class="item  [IF [!Pos!]=1]active[/IF]">
								<div class="produits">
									<div class="produits-inner">
										<a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]/Produit/[!P::Url!]">
											<img class="img-responsive" src="[!Domaine!]/[!P::ProduitGrandFormat!].mini.290x590.jpg" alt="[!P::Nom!]"/>
										</a>
										<div class="[!CatP::Couleur!]">
											<h2><a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]">[!CatP::Nom!]</a></h2>
											<h3><a href="/[!Systeme::getMenu(Products/Categorie)!]/[!CatP::Url!]/Produit/[!P::Url!]">[!P::Nom!]</a></h3>
										</div>
									</div>
								</div>
							</div>
						[/STORPROC]
					[/STORPROC]
				</div>
				<div class="nav-prod">
					<a class="next" href="#myCarousel4" data-slide="next"></a>
				</div>
			</div>
		</div>
		<!-- Carousel items -->
	</div>
</div>
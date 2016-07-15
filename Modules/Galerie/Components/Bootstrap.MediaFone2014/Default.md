[IF [!MEDIACATEGORIE!]!=]
	[!Req:=Galerie/Categorie/[!MEDIACATEGORIE!]/Media!]
[ELSE]
	[!Req:=Galerie/Media!]
[/IF]	
[!ChmpImage:=Fichier!]
[!ReqCat:=Galerie/Categorie/Media!]
<div class="[!NOMDIV!]">
   	<div class="container nopadding-left nopadding-right">
 		<h1><a href="/[!Systeme::getMenu([!URLMEDIA!])!]">[!TITRE!]</a></h1>    
		<div class="col-lg-6 col-sm-6 col-xs-12  nopadding-left">
			<div id="myCarouselGalerie1" class="carousel slide vertical">
				<div class="vertical carousel-inner ">
					[STORPROC [!Req!]|Med|0|[!NBMEDIACOL:*2!]|Date|DESC]
						[IF [!Utils::isPair([!Pos!])!]=]
							[STORPROC [!ReqCat!]/[!Med::Id!]|Cat|0|1][/STORPROC]
							<div class="[IF [!Pos!]=1]active [/IF]item">
								<div class="galerie">
									<div class="category">
										<div class="cat-bloc">MEDIA | [!Cat::Titre!]</div>
									</div>
									<div class="produits-inner">
										[IF [!Med::[!ChmpImage!]!]!=]<img class="img-responsive" src="[!Domaine!]/[!Med::[!ChmpImage!]!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Med::Titre!]" />[/IF]
										<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
											<h2>[!Med::Titre!]</h2>
											<h3>[!Med::Chapo!]</h3>
										</div>
										<div class="teaser">
											<div class="texteaser"> 
												[SUBSTR 200|...][!Med::Description!][/SUBSTR]
											</div>
											<div class="teaser-info">
												<div class="date">[DATE d/m/Y][!Med::Date!][/DATE]</div>
												<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu([!URLMEDIA!])!]/[!Cat::Url!]/Media/[!Med::Url!]">__MORE_DETAILS__</a></div>
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
				<a class="next" href="#myCarouselGalerie1" data-slide="next"></a>
			</div>
		</div>
		<div class="col-lg-6 col-sm-6 col-xs-12 hidden-xs nopadding-right">
			<div id="myCarouselGalerie2" class="carousel slide vertical">  
				<div class="vertical carousel-inner ">
					[!First:=1!]
					[STORPROC [!Req!]|Med|0|[!NBMEDIACOL:*2!]|Date|DESC]
						[IF [!Utils::isPair([!Pos!])!]]
							[STORPROC [!ReqCat!]/[!Med::Id!]|Cat|0|1]
							<div class="[IF [!First!]=1]active [!First:=0!][/IF]item">
								<div class="galerie">
									<div class="category">
										<div class="cat-bloc">MEDIA | [!Cat::Titre!]</div>
									</div>
									<div class="produits-inner">
										<img class="img-responsive" src="[!Domaine!]/[!Med::[!ChmpImage!]!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Med::Titre!]"/>
										<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
											<h2>[!Med::Titre!]</h2>
											<h3>[!Med::Chapo!]</h3>
										</div>
										<div class="teaser">
											<div class="texteaser" > 
												[SUBSTR 200|...][!Med::Description!][/SUBSTR]
											</div>
											<div class="teaser-info">
												<div class="date">[DATE d/m/Y][!Med::Date!][/DATE]</div>
												<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu([!URLMEDIA!])!]/[!Cat::Url!]/Media/[!Med::Url!]">__MORE_DETAILS__</a></div>
											</div>
										</div>
									</div>
								</div>
							</div>
							[/STORPROC]
						[/IF]
					[/STORPROC]
				</div>
			</div>
			<div class="nav-prod">
				<a class="next" href="#myCarouselGalerie2" data-slide="next"></a>
			</div>
		</div>

	</div>
</div>

//DIMENSIONS
[!LARGEURIMAGE:=570!]
[!HAUTEURIMAGE:=350!]

[COUNT Team/Rider/[!RIDER!]/Post|NbPost]

[IF [!NbPost!]]
	//RECHERCHE DU RIDER
	[STORPROC Team/Rider/[!RIDER!]|R|0|1]
	[!Search:=[!R::Nom!] [!R::Prenom!]!]
		<div class="[!NOMDIV!]">
			<div class="container nopadding-right nopadding-left">
				<div class="reseau">
					<h1><a href="/[!LIENTITRE!]">[!TITRE!]</a></h1>    
					<div class="col-lg-6 col-sm-6 col-xs-12 nopadding-left">
						<div id="myCarouselBlog1" class="carousel slide vertical">  
							<div class="vertical carousel-inner ">
								//[STORPROC [!Systeme::getSearch([!Search!])!]/PageModule=Blog&PageObject=Post|TL|0|2]
								[STORPROC Team/Rider/[!R::Id!]/Post|Po|0|10|Date|DESC]
									[IF [!Utils::isPair([!Pos!])!]=]
										//[STORPROC [!TL::PageModule!]/[!TL::PageObject!]/[!TL::PageId!]|Po|0|1|Date|DESC]
											[STORPROC Blog/Categorie/Post/[!Po::Id!]|Cat|0|1][/STORPROC]
											<div class="[IF [!Pos!]=1]active [/IF]item">
												<div class="blog">
													<div class="category">
														<div class="cat-bloc"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]">NEWS | [!Cat::Titre!]</a></div>
													</div>
													<div class="produits-inner">
														<div class="Post-Aff">
															[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Video|Do|0|1]
															<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">
																<video controls poster="/[!Do::Apercu!]" class="img-responsive">
																	<source src="[!Domaine!]/[!Do::FichierMp4!]" type="video/mp4">
																	<source src="[!Domaine!]/[!Do::FichierWEBM!]" type="video/webm">
																	<object type="application/x-shockwave-flash" data="player.swf"	width="100%" height="100%">
																		<param name="allowfullscreen" value="true">
																		<param name="allowscriptaccess" value="always">
																		<param name="flashvars" value="file=[!Domaine!]/[!Do::Fichierh264!]">
																		<!--[if IE]><param name="movie" value="player.swf"><![endif]-->
																		<img src="video.jpg" width="100%" height="100%" alt="[!Do::Titre!]">
																		<p>Your browser can’t play HTML5 video. <a href="[!Domaine!]/[!Do::FichierWEBM!]">Download it</a> instead.</p>
																	</object>
																</video>
															</a>
																[NORESULT]
																	[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
																		<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Do::Titre!]"/></a>
																	[/STORPROC]
																[/NORESULT]
															[/STORPROC]
														</div>
														<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">
															<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
																<h2>[!Po::Titre!]</h2>
																<h3>[!Po::Chapo!]</h3>
															</div>
														</a>
														<div class="teaser">
															<div class="texteaser"> 
																[SUBSTR 200|...][!Po::Contenu!][/SUBSTR]
															</div>
															<div class="teaser-info">
																<div class="date">[DATE d/m/Y][!Po::Date!][/DATE]</div>
																<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">MORE DETAILS</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
	//									[/STORPROC]
									[/IF]
								[/STORPROC]
							</div>
						</div>
						<div class="nav-prod">
							<a class="next" href="#myCarouselBlog1" data-slide="next"></a>
						</div>
					</div>
					<div class="col-lg-6 col-sm-6 col-xs-12 nopadding-right">
						<div id="myCarouselBlog2" class="carousel slide vertical">  
							<div class="vertical carousel-inner ">
	//							[STORPROC [!Systeme::getSearch([!Search!])!]/PageModule=Blog&PageObject=Post|TL|0|2]
								[!Flag:=0!]
								[STORPROC Team/Rider/[!RIDER!]/Post|Po|0|10|Date|DESC]
									[IF [!Utils::isPair([!Pos!])!]]
										[!Flag+=1!]
	//									[STORPROC [!TL::PageModule!]/[!TL::PageObject!]/[!TL::PageId!]|Po|0|1]
											[STORPROC Blog/Categorie/Post/[!Po::Id!]|Cat|0|1][/STORPROC]
											<div class="[IF [!Flag!]=1]active [/IF]item">
												<div class="blog">
													<div class="category">
														<div class="cat-bloc"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]">NEWS | [!Cat::Titre!]</a></div>
													</div>
													<div class="produits-inner">
														[STORPROC [!Req!]/[!Po::Id!]/Donnees/Type=Video|Do|0|1]
															<div class="Post-Aff">
																<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">
																	<video controls poster="/[!Do::Apercu!]" class="img-responsive">
																		<source src="[!Domaine!]/[!Do::FichierMp4!]" type="video/mp4">
																		<source src="[!Domaine!]/[!Do::FichierWEBM!]" type="video/webm">
																		<object type="application/x-shockwave-flash" data="player.swf"	width="100%" height="100%">
																			<param name="allowfullscreen" value="true">
																			<param name="allowscriptaccess" value="always">
																			<param name="flashvars" value="file=[!Domaine!]/[!Do::Fichierh264!]">
																			<!--[if IE]><param name="movie" value="player.swf"><![endif]-->
																			<img src="video.jpg" width="100%" height="100%" alt="[!Do::Titre!]">
																			<p>Your browser can’t play HTML5 video. <a href="[!Domaine!]/[!Do::FichierWEBM!]">Download it</a> instead.</p>
																		</object>
																	</video>
																</a>
																[NORESULT]
																	[STORPROC Blog/Post/[!Po::Id!]/Donnees/Type=Image|Do|0|1]
																		<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]"><img class="img-responsive" src="/[!Do::Fichier!].mini.[!LARGEURIMAGE!]x[!HAUTEURIMAGE!].jpg" alt="[!Do::Titre!]"/></a>
																	[/STORPROC]
																[/NORESULT]
															</div>
														[/STORPROC]
														<a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">
															<div class="BlocCouleur BlocCouleur-[!Cat::Couleur!]">
																<h2>[!Po::Titre!]</h2>
																<h3>[!Po::Chapo!]</h3>
															</div>
														</a>
														<div class="teaser">
															<div class="texteaser"> 
																[SUBSTR 200|...][!Po::Contenu!][/SUBSTR]
															</div>
															<div class="teaser-info">
																<div class="date">[DATE d/m/Y][!Po::Date!][/DATE]</div>
																<div class="more-BlocCouleur-[!Cat::Couleur!]"><a href="/[!Systeme::getMenu(Blog/Categorie)!]/[!Cat::Url!]/Post/[!Po::Url!]">MORE DETAILS</a></div>
															</div>
														</div>
													</div>
												</div>
											</div>
	//									[/STORPROC]
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
			<!--<div class="container">
				<div class="load-more">
					[!More+=4!]
					<a href="/[!Lien!]?More=[!More!]"  class="btn-more-Media btn-primary">LOAD MORE MEDIAS</a>
				</div> 
			</div>-->
		</div>
	[/STORPROC]
[/IF]
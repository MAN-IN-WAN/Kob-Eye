[IF [!Chemin!]]
[ELSE]
	[!Chemin:=[!Query!]!]
[/IF]
<div class="MiseEnPageModele">
	[STORPROC [!Chemin!]|Cat]
		[STORPROC [!Chemin!]/Article/Publier=1|Art]
			[IF [!Art::Contenu!]!=]
				<div class="row" ><div class="col-md-12 col-sm-12 col-xs-12">
					<div class="articleMep"><div class="contenuMEP">
						[IF [!Art::AfficheTitre!]]<h2>[!Art::Titre!]</h2>[/IF]
						[IF [!Art::Chapo!]!=]<h3>[!Art::Chapo!]</h3>[/IF]
					</div></div>
				</div></div>
			[ELSE]
				<div class="articleMep">
					[STORPROC MiseEnPage/Article/[!Art::Id!]/Contenu|Cont]
						// Combien j'ai de colonnes pour ce contenu
						[COUNT MiseEnPage/Contenu/[!Cont::Id!]/Colonne|NbCol]
						<div class="row" ><div class="col-md-12 col-sm-12 col-xs-12">
							<div class="contenuMEP  [IF [!NbCol!]>1]ArticleDecale[/IF]">
								[STORPROC MiseEnPage/Contenu/[!Cont::Id!]/Colonne|Col|||Ordre|ASC]
									<div class="colonneMEP" style="width:[!Col::Ratio!]%;">
										[STORPROC MiseEnPage/Colonne/[!Col::Id!]/Image|ContColImg]
											<div class="imgMEPContainer" style="[IF [!NbCol!]=1]float:left;none;[ELSE]padding-right:15;[/IF]">
												<img src="/[!ContColImg::URL!]" alt="[!ContColImg::Alt!]" title="[!ContColImg::Title!]" class="img-responsive">
											</div>
											[NORESULT]
												[STORPROC MiseEnPage/Colonne/[!Col::Id!]/Texte|ContColText]
													<div class="txtMEPContainer">
														<h2>[!ContColText::Titre!]</h2>
														[IF [!ContColText::Chapo!]!=]<h3>[!ContColText::Chapo!]</h3>[/IF]
														<div>[!ContColText::Contenu!]</div>
													</div>
												[/STORPROC]
											[/NORESULT]
										[/STORPROC]
									</div>
								[/STORPROC]
							</div>
						</div></div>
					[/STORPROC]
				</div>
			[/IF]
		[/STORPROC]
	[/STORPROC]
</div>		
		
